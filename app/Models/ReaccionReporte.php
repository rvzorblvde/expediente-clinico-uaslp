<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReaccionReporte extends Model
{
    protected $table = 'reacciones_reporte';

    protected $fillable = [
        'farmacovigilancia_id',
        'sintoma_reaccion',
        'fecha_inicio',
        'fecha_fin',
        'duracion',
        'resultado_reaccion',
        'es_grave'
    ];

    public function farmacovigilancia()
    {
        return $this->belongsTo(Farmacovigilancia::class);
    }
}