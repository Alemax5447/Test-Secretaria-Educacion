1. Descripción general
   Este proyecto implementa un sistema de extracción de datos desde credenciales oficiales usando IA multimodal, con un backend en Laravel y un frontend en Angular 20.
   La IA analiza imágenes de credenciales, extrae campos relevantes (por ejemplo: nombre, CURP y fecha de nacimiento) y los almacena en una base de datos para su posterior consulta.
   La solución está pensada para demostración funcional, pruebas técnicas y evaluación de uso real de inteligencia artificial.

2. Arquitectura general

[ Angular 20 ]
  |
  v
[ HTTP (REST) ]
  |
  v
[ Laravel API ]
  |
  v
IA multimodal (GEMINI GOOGLE)
  |
  v
[ Servicio de IA ]
  |
  v
[ Base de Datos ]

## Endpoints de la API y ejemplos de uso

Estas son las rutas principales disponibles para consumir desde el frontend (por ejemplo, con Angular, Postman, etc.):

### Listar todos los empleados

**GET** `/api/empleados`

**Respuesta:**

```json
{
    "success": true,
    "data": [
        {
            "id_empleado": 1,
            "nombre": "Juan",
            "apellidos": "Pérez López",
            "curp": "PEPJ800101HDFRRN09",
            ...
        },
        ...
    ]
}
```

### Crear empleado por OCR (subir imagen de credencial)

**POST** `/api/empleados/ocr`

**Body (form-data):**

-   imagen: archivo de imagen (jpeg, png, jpg, heic)

**Respuesta exitosa:**

```json
{
    "success": true,
    "mensaje": "Datos extraídos y empleado creado.",
    "data": { ... },
    "raw_ocr_data": { ... }
}
```

**Si la CURP ya existe:**

```json
{
    "success": false,
    "error": "El empleado con esa CURP ya existe.",
    "detalle_bd": "..."
}
```

### Eliminar empleado

**DELETE** `/api/empleados/{id_empleado}`

**Respuesta exitosa:**

```json
{
    "success": true,
    "mensaje": "Empleado eliminado correctamente."
}
```

**Si el empleado no existe:**

```json
{
    "success": false,
    "mensaje": "Empleado no encontrado."
}
```

---

## Instalación y configuración del backend (Laravel)

### Requisitos previos

-   PHP >= 8.1
-   Composer
-   MySQL o MariaDB
-   Extensiones PHP recomendadas por Laravel

### Pasos de instalación

1. Clona el repositorio y entra a la carpeta del proyecto:
    ```bash
    git clone <URL_DEL_REPOSITORIO>
    cd <NOMBRE_DEL_PROYECTO>
    ```
2. Instala las dependencias de PHP:
    ```bash
    composer install
    ```
3. Copia el archivo de entorno y configura tus variables:
    ```bash
    cp .env.example .env
    # Edita .env con tus credenciales de base de datos y la clave GEMINI_API_KEY
    ```
4. Genera la clave de la aplicación:
    ```bash
    php artisan key:generate
    ```
5. Ejecuta las migraciones para crear las tablas:
    ```bash
    php artisan migrate
    ```
    ```

    ```
6. Inicia el servidor de desarrollo:
    ```bash
    php artisan serve
    ```

### Notas importantes

-   El proyecto utiliza la librería Intervention Image para compresión y manipulación de imágenes. Composer la instalará automáticamente.
-   Asegúrate de tener configurada la variable `GEMINI_API_KEY` en tu archivo `.env` para el uso de la API de Google Gemini.
-   Si tienes problemas con extensiones de PHP, revisa la documentación oficial de Laravel.

---

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

-   [Simple, fast routing engine](https://laravel.com/docs/routing).
-   [Powerful dependency injection container](https://laravel.com/docs/container).
-   Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
-   Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
-   Database agnostic [schema migrations](https://laravel.com/docs/migrations).
-   [Robust background job processing](https://laravel.com/docs/queues).
-   [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

-   **[Vehikl](https://vehikl.com)**
-   **[Tighten Co.](https://tighten.co)**
-   **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
-   **[64 Robots](https://64robots.com)**
-   **[Curotec](https://www.curotec.com/services/technologies/laravel)**
-   **[DevSquad](https://devsquad.com/hire-laravel-developers)**
-   **[Redberry](https://redberry.international/laravel-development)**
-   **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
