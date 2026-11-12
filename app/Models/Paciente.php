<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    protected $table = 'pacientes';
    protected $primaryKey = 'idPaciente';

    protected $casts = [
        'fechaNac' => 'date:Y-m-d',
    ];

    protected $fillable = [
        'ocupacion',
        'escolaridad',
        'servicioMedico',
        'nombres',
        'apellido1',
        'apellido2',
        'fechaNac',
        'curp',
        'sexo',
        'lugarProcedencia',
        'lugarResidencia'
    ];

    public function telefonos()
    {
        return $this->hasMany(PacienteTelefono::class, 'idPaciente', 'idPaciente');
    }

    public function correos()
    {
        return $this->hasMany(PacienteCorreo::class, 'idPaciente', 'idPaciente');
    }

    public function contactos()
    {
        return $this->hasMany(ContactoEmergencia::class, 'idPaciente', 'idPaciente');
    }
}
