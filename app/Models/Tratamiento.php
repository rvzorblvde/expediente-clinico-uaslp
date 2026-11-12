<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tratamiento extends Model
{
    protected $table = 'tratamientos';
    protected $primaryKey = 'idTratamiento';

    protected $fillable = [
        'folioExpediente', 'idMedicamento', 'dosis', 'viaAdministracion',
        'frecuencia', 'fechaInicio', 'fechaFin', 'estado',
    ];

    protected $casts = ['fechaInicio' => 'date', 'fechaFin' => 'date'];

    public function medicamento()
    {
        return $this->belongsTo(Medicamento::class, 'idMedicamento', 'idMedicamento');
    }

    public function expediente()
    {
        return $this->belongsTo(Expediente::class, 'folioExpediente', 'folioExpediente');
    }
}
