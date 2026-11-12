<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TratamientoNoFarmacologico extends Model
{
    protected $table = 'tratamientos_no_farmacologicos';
    protected $primaryKey = 'idTratamientoNF';
    public $timestamps = false;

    protected $fillable = ['folioExpediente', 'tipo', 'titulo', 'descripcion'];

    public function expediente()
    {
        return $this->belongsTo(Expediente::class, 'folioExpediente', 'folioExpediente');
    }
}
