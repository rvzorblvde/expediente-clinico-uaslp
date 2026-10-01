<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactoTelefono extends Model
{
    protected $table = 'contacto_telefonos';
    protected $primaryKey = 'idTelefonoContacto';
    public $timestamps = false;

    protected $fillable = ['idContacto', 'numero'];
}
