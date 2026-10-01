<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visita extends Model
{
    protected $table = 'visitas';
    protected $primaryKey = 'idVisita';
    public $timestamps = false;

    protected $fillable = ['folioExpediente', 'idUsuario', 'fecha', 'motivo', 'estado'];
}
