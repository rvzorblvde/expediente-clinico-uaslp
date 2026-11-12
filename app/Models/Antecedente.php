<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Antecedente extends Model
{
    protected $table = 'antecedentes';
    protected $primaryKey = 'idAntecedente';
    public $timestamps = false;

    protected $fillable = ['folioExpediente', 'tipo', 'nombre', 'descripcion', 'esFamiliar'];
}
