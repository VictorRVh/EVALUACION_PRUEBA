<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnidadDidactica extends Model
{
    use HasFactory;
    protected $table = 'unidad_didactica';

    protected $fillable = [
        'id_indicador',
        'especialidad_id',
        'nombre_unidad',
        'fecha_inicio',
        'fecha_final',
        'credito_unidad',
        'hora',
        'dia',
        'descripcion_capacidad'
    ];

    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class, 'especialidad_id', 'id_unidad');
    }
}
