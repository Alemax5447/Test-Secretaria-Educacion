<?php

namespace App\Http\Controllers;

use Intervention\Image\ImageManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Empleado;

class EmpleadoOcrController extends Controller
{
    // Obtiene todos los empleados
    public function getEmpleados()
    {
        $empleados = Empleado::all();
        return response()->json([
            'success' => true,
            'data' => $empleados
        ]);
    }
    /**
     * Procesa una imagen de credencial usando Gemini 2.5 Flash (Free Tier)
     * para extraer datos estructurados y guardarlos en la base de datos.
     */
    public function postProcesarCredencial(Request $request)
    {
        // 1. Validación básica de la imagen
        $request->validate([
            'imagen' => 'required|image|mimes:jpeg,png,jpg,heic|max:20480'
        ]);

        try {
            // 2. Compresión/Redimensionamiento de la imagen (Optimización de Ancho de Banda)
            $manager = new ImageManager(\Intervention\Image\Drivers\Gd\Driver::class);
            $image = $manager->read($request->file('imagen')->getRealPath());

            // Redimensionar para un ancho máximo de 1024px si es necesario
            if ($image->width() > 1024) {
                $image->resize(1024, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }

            // Comprimir a 80% de calidad y codificar en Base64
            $imageStream = $image->toJpeg(80);
            $imagenBase64 = base64_encode($imageStream);
            $mimeType = 'image/jpeg';

            // 3. Preparar la Petición a la API de Gemini (Free Tier)
            $apiKey = env('GEMINI_API_KEY');

            // **Endpoint Público para Free Tier:**
            $geminiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}";

            $prompt = "Extrae la información de esta credencial de identificación (INE). Devuelve el JSON con los campos solicitados. Si un campo no está visible o no aplica, usa null. Sé preciso.";

            // Definición del Schema JSON (Garantiza un formato de salida exacto)
            $responseSchema = [
                'type' => 'OBJECT',
                'properties' => [
                    'nombre' => ['type' => 'STRING', 'description' => 'Solo el nombre(s)'],
                    'apellidos' => ['type' => 'STRING', 'description' => 'Apellidos (Paterno y Materno)'],
                    'curp' => ['type' => 'STRING'],
                    'estado' => ['type' => 'STRING', 'description' => 'Nombre del estado donde reside'],
                    'municipio' => ['type' => 'STRING', 'description' => 'Nombre del municipio o delegación'],
                    'localidad' => ['type' => 'STRING', 'description' => 'Colonia, localidad o sección'],
                ],
                'required' => ['nombre', 'apellidos', 'curp']
            ];

            // 4. Realizar la Petición HTTP a Gemini
            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post($geminiUrl, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                            [
                                'inline_data' => [
                                    'mime_type' => $mimeType,
                                    'data' => $imagenBase64
                                ]
                            ]
                        ]
                    ]
                ],
                // 'config' o 'generationConfig' funciona en el API pública, pero 'generationConfig' es el estándar.
                'generationConfig' => [
                    'responseMimeType' => 'application/json',
                    'responseSchema' => $responseSchema
                ]
            ]);

            // 5. Manejo de Respuesta y Errores
            if (!$response->successful()) {
                // Captura errores como API Key inválida o límite de Free Tier excedido
                Log::error('Error de API Gemini: ' . $response->body());
                return response()->json([
                    'success' => false,
                    'error' => 'Error al comunicarse con la API de Gemini (Verifica tu clave y límites)',
                    'detalle' => $response->json(),
                ], $response->status());
            }

            $datosGemini = $response->json();

            // Extraer el texto JSON y decodificar
            $rawJson = $datosGemini['candidates'][0]['content']['parts'][0]['text'] ?? '{}';

            $datosExtraidos = json_decode($rawJson, true);


            // Validar que todos los campos no sean null o vacíos
            $camposClave = ['nombre', 'apellidos', 'curp', 'estado', 'municipio', 'localidad'];
            $faltantes = [];
            foreach ($camposClave as $campo) {
                if (empty($datosExtraidos[$campo])) {
                    $faltantes[] = $campo;
                }
            }
            if (count($faltantes) > 0) {
                return response()->json([
                    'success' => false,
                    'mensaje' => 'No se pudo extraer toda la información. Por favor, sube una foto más clara de la credencial.',
                    'faltantes' => $faltantes
                ], 422);
            }

            // 6. Guardar en la tabla empleados (usando las claves del Schema)

            try {
                $empleado = Empleado::create([
                    'nombre' => $datosExtraidos['nombre'],
                    'apellidos' => $datosExtraidos['apellidos'],
                    'curp' => $datosExtraidos['curp'],
                    'estado' => $datosExtraidos['estado'],
                    'municipio' => $datosExtraidos['municipio'],
                    'localidad' => $datosExtraidos['localidad'],
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
                if ($e->getCode() == 23000) { // Código SQLSTATE para violación de restricción única
                    return response()->json([
                        'success' => false,
                        'error' => 'El empleado con esa CURP ya existe.',
                        'detalle_bd' => $e->getMessage()
                    ], 409);
                }
                throw $e;
            }

            return response()->json([
                'success' => true,
                'mensaje' => 'Datos extraídos y empleado creado.',
                'data' => $empleado,
                'raw_ocr_data' => $datosExtraidos // Útil para verificar la extracción
            ]);

        } catch (\Exception $e) {
            // Manejo de errores internos (ej: disco, base de datos)
            Log::error("Error interno en procesarCredencial: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Ocurrió un error interno del servidor'
            ], 500);
        }
    }
     /**
     * Elimina un empleado por su id.
     */
    public function deleteEmpleado($id)
    {
        $empleado = Empleado::where('id_empleado', $id)->first();
        if (!$empleado) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Empleado no encontrado.'
            ], 404);
        }
        $empleado->delete();
        return response()->json([
            'success' => true,
            'mensaje' => 'Empleado eliminado correctamente.'
        ]);
    }
}
