<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PacienteCorreo extends Model
{
    protected $table = 'paciente_correos';
    protected $primaryKey = 'idCorreo';
    public $timestamps = false;

    protected $fillable = ['idPaciente', 'correo'];
}
