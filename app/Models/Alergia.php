<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alergia extends Model
{
    protected $table = 'alergias';
    protected $primaryKey = 'idAlergia';
    public $timestamps = false;

    protected $fillable = ['folioExpediente', 'sustancia', 'reaccion', 'severidad', 'fechaAproximada'];
}
