<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Habito extends Model
{
    protected $table = 'habitos';
    protected $primaryKey = 'idHabito';
    public $timestamps = false;

    protected $fillable = [
        'folioExpediente', 'frecuenciaTabaquismo', 'frecuenciaAlcoholismo',
        'frecuenciaDrogas', 'frecuenciaCafeina',
        'descripcionDrogas', 'horaSueno', 'frecuenciaActFisica'
    ];
}
