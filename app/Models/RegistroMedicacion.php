<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistroMedicacion extends Model
{
    protected $table = 'registro_medicaciones';
    protected $primaryKey = 'idRegistroMedicacion';
    public $timestamps = false;

    protected $fillable = [
        'idVisita', 'idMedicamento', 'dosis', 'viaAdministracion',
        'frecuenciaUso', 'indicaciones', 'fechaRegistro'
    ];

    public function medicamento()
    {
        return $this->belongsTo(Medicamento::class, 'idMedicamento', 'idMedicamento');
    }
}
