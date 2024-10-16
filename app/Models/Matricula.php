<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matricula extends Model
{
    use HasFactory;
    protected $table = 'matricula';

    protected $fillable = [
        'codigo_estudiante_id',
        'turno',
        'condicion',
        'programa_estudio_id',
        'numero_recibo'
    ];

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'codigo_estudiante_id', 'dni');
    }

    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class, 'programa_estudio_id', 'programa_estudio');
    }
}
