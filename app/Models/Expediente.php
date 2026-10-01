<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expediente extends Model
{
    protected $table = 'expedientes';
    protected $primaryKey = 'folioExpediente';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'folioExpediente',
        'idPaciente',
        'idUsuarioCreador',
        'estado'
    ];

    // Relación con el paciente
    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'idPaciente', 'idPaciente');
    }

    // Relación con el usuario creador
    public function creador()
    {
        return $this->belongsTo(User::class, 'idUsuarioCreador', 'idUsuario');
    }

    // Relación 1 a 1 con Hábitos
    public function habitos()
    {
        return $this->hasOne(Habito::class, 'folioExpediente', 'folioExpediente');
    }

    // Relación 1 a 1 con Antecedentes Ginecoobstétricos
    public function antecedentesGinecoObstetricos()
    {
        return $this->hasOne(AntecedenteGinecoObstetrico::class, 'folioExpediente', 'folioExpediente');
    }

    // Relación 1 a muchos con Antecedentes médicos/quirúrgicos
    public function antecedentes()
    {
        return $this->hasMany(Antecedente::class, 'folioExpediente', 'folioExpediente');
    }

    // Relación 1 a muchos con Alergias
    public function alergias()
    {
        return $this->hasMany(Alergia::class, 'folioExpediente', 'folioExpediente');
    }

    // Relación 1 a muchos con Visitas médicas
    public function visitas()
    {
        return $this->hasMany(Visita::class, 'folioExpediente', 'folioExpediente');
    }
}
