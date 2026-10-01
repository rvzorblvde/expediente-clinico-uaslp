<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactoEmergencia extends Model
{
    protected $table = 'contacto_emergencias';
    protected $primaryKey = 'idContacto';
    public $timestamps = false;

    protected $fillable = ['idPaciente', 'nombres', 'apellido1', 'apellido2', 'parentesco'];
}
