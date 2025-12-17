<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmpleadoOcrController;

Route::get('/empleados', [EmpleadoOcrController::class, 'getEmpleados']);
Route::post('/empleados/create', [EmpleadoOcrController::class, 'postProcesarCredencial']);
Route::delete('/empleados/delete/{id}', [EmpleadoOcrController::class, 'deleteEmpleado']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
