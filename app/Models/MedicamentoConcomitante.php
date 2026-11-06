<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicamentoConcomitante extends Model
{
    protected $table = 'medicamentos_concomitantes';

    protected $fillable = [
        'farmacovigilancia_id',
        'nombre_medicamento',
        'dosis',
        'frecuencia',
        'fecha_inicio'
    ];
}