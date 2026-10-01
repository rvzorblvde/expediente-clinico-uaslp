<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auditoria extends Model
{
    protected $table = 'auditorias';
    protected $primaryKey = 'idLog';
    public $timestamps = false;             // Solo usaremos fechaAccion

    protected $fillable = [
        'idUsuario',
        'accion',
        'tablaAfectada',
        'detalles',
        'fechaAccion'
    ];

    // Le decimos a Laravel que maneje este campo como un JSON/Array en PHP
    protected function casts(): array
    {
        return [
            'detalles' => 'array',
        ];
    }
}
