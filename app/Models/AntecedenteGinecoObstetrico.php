<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AntecedenteGinecoObstetrico extends Model
{
    protected $table = 'antecedentes_gineco_obstetricos';
    protected $primaryKey = 'idGinecoObstetrico';
    public $timestamps = false;

    protected $fillable = [
        'folioExpediente', 'embarazoActual', 'semanasGestacion',
        'menstruacion', 'lactancia', 'observaciones'
    ];
}
