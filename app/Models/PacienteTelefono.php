<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PacienteTelefono extends Model
{
    protected $table = 'paciente_telefonos';
    protected $primaryKey = 'idTelefono';
    public $timestamps = false;

    protected $fillable = ['idPaciente', 'numero', 'tipo'];
}
