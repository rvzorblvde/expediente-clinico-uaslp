<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visita extends Model
{
    use HasFactory;

    protected $table = 'visitas';
    protected $primaryKey = 'idVisita';
    
    public $timestamps = false; 

    protected $fillable = [
        'folioExpediente',
        'idUsuario',
        'fecha',
        'motivo',
        'estado',
    ];

    // Relación opcional con Expediente
    public function expediente()
    {
        return $this->belongsTo(Expediente::class, 'folioExpediente', 'folioExpediente');
    }

    // Relación con el usuario que atendió/registró
    public function usuario()
    {
        return $this->belongsTo(User::class, 'idUsuario', 'idUsuario');
    }
}