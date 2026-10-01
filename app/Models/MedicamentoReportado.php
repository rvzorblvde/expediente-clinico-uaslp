<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicamentoReportado extends Model
{
    protected $table = 'medicamento_reportados';
    protected $primaryKey = 'idMedicamentoReportado';
    public $timestamps = false;

    protected $fillable = [
        'idReporte', 'nombre', 'esSospechosoCausante', 'companiaFabricante',
        'numeroLote', 'dosis', 'viaAdministracion', 'fechaInicio',
        'fechaFinalizacion', 'indicacionMedicamento', 'accionLlevadaCabo'
    ];
}
