<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    // Nombre de la tabla
    protected $table = 'empleados';

    // Nombre de la clave primaria
    protected $primaryKey = 'id_empleado';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'nombre',
        'apellidos',
        'curp',
        'estado',
        'municipio',
        'localidad',
    ];
}
