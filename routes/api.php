<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmpleadoOcrController;

Route::get('/empleados/test', [EmpleadoOcrController::class, 'test']);
Route::post('/empleados/ocr', [EmpleadoOcrController::class, 'procesarCredencial']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
