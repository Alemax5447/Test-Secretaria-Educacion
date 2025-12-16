<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmpleadoOcrController;

Route::get('/empleados', [EmpleadoOcrController::class, 'getEmpleados']); // Endpoint para obtener la lista de empleados
Route::post('/empleados/create', [EmpleadoOcrController::class, 'postProcesarCredencial']); // Endpoint para crear un nuevo empleado con IA
Route::delete('/empleados/delete/{id}', [EmpleadoOcrController::class, 'deleteEmpleado']); // Endpoint para eliminar un empleado por ID

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
