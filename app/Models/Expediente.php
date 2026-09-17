<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expediente extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nombres', 'apellidos', 'fecha_nacimiento',
        'sexo', 'curp', 'peso', 'altura',
    ];

    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
            'peso' => 'decimal:2',
            'altura' => 'decimal:2',
        ];
    }

    // Accessor: $expediente->imc
    protected function imc(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => $this->altura > 0
                ? round($this->peso / ($this->altura ** 2), 2)
                : null,
        );
    }

    // Accessor: $expediente->nombre_completo
    protected function nombreCompleto(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => "{$this->nombres} {$this->apellidos}",
        );
    }
}
