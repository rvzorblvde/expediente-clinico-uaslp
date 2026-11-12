<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatosAntropometricos extends Model
{
    protected $table = 'datos_antropometricos';
    protected $primaryKey = 'idDatosAntropometricos';
    public $timestamps = false;

    protected $fillable = [
        'idVisita', 'peso', 'talla', 'imc', 'temperatura', 'presionSistolica',
        'presionDiastolica', 'pulsoPpm', 'frecuenciaRespiratoria', 'saturacionOxigeno'
    ];
}
