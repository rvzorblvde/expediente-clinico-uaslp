<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactoCorreo extends Model
{
    protected $table = 'contacto_correos';
    protected $primaryKey = 'idCorreoContacto';
    public $timestamps = false;

    protected $fillable = ['idContacto', 'correo'];
}
