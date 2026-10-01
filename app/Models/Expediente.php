<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expediente extends Model
{
    protected $table = 'expedientes';
    protected $primaryKey = 'folioExpediente';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'folioExpediente',
        'idPaciente',
        'estado'
    ];
}
