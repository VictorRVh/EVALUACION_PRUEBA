<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estudiante extends Model
{
    use HasFactory;

    protected $table = "estudiante"; // Cambia esto para que coincida con el nombre de la tabla en la migración
    protected $fillable = [
        'codigo_estudiante',
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'dni',
        'sexo',
        'celular',
        'correo',
        'fecha_nacimiento'
    ];

    public function matricula()
    {
        return $this->hasOne(Matricula::class, 'codigo_estudiante_id', 'dni');
    }
}
