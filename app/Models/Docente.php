<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Docente extends Model
{
    use HasFactory;
    protected $table = "docente";
    protected $fillable = [
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'dni',
        'sexo',
        'celular',
        'correo',
        'fecha_nacimiento'
    ];
}
