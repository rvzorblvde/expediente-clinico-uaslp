<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReaccionSintoma extends Model
{
    protected $table = 'reaccion_sintomas';
    protected $primaryKey = 'idSintoma';
    public $timestamps = false;

    protected $fillable = [
        'idReporte', 'reaccion', 'fechaInicio', 'fechaTermino',
        'duracion', 'resultado', 'esGrave'
    ];
}
