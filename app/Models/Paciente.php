<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    protected $table = 'pacientes';
    protected $primaryKey = 'idPaciente';

    protected $fillable = [
        'nombres',
        'apellido1',
        'apellido2',
        'fechaNac',
        'curp',
        'sexo',
        'lugarProcedencia',
        'lugarResidencia'
    ];
}
