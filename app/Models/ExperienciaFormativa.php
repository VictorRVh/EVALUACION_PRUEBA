<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExperienciaFormativa extends Model
{
    use HasFactory;


    protected $table = "experiencia_formativa";
    protected $fillable = [
        'especialidad_id',
        'componente',
        'fecha_inicio',
        'fecha_termino',
        'creditos',
        'dias',
        'horas'
    ];

    public function experienciaFormativa()
    {
        return $this->belongsTo(Especialidad::class, 'especialidad_id', 'id');
    }
}
