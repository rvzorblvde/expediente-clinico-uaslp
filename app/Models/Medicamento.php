<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medicamento extends Model
{
    protected $table = 'medicamentos';
    protected $primaryKey = 'idMedicamento';

    protected $fillable = [
        'nombre',
        'principioActivo',
        'presentacion',
        'concentracion',
        'requiereReceta',
        'esControlado'
    ];
}
