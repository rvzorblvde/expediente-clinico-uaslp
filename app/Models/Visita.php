<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visita extends Model
{
    protected $table = 'visitas';
    protected $primaryKey = 'idVisita';
    public $timestamps = false;

    protected $fillable = ['folioExpediente', 'idUsuario', 'fecha', 'motivo', 'estado'];

    // Relación 1 a 1 con Datos Antropométricos (Triage / Signos vitales)
    public function datosAntropometricos()
    {
        return $this->hasOne(DatosAntropometricos::class, 'idVisita', 'idVisita');
    }
}
