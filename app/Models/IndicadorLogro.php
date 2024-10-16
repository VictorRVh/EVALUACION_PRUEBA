<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndicadorLogro extends Model
{
    use HasFactory;
    protected $table = 'indicador_logro';

    protected $fillable = [
        'descripcion',
        'unidad_didactica_id'
    ];

    public function unidadDidactica()
    {
        return $this->belongsTo(UnidadDidactica::class, 'unidad_didactica_id', 'id_indicador');
    }
}
