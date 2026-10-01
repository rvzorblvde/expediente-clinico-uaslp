<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArchivoAdjunto extends Model
{
    protected $table = 'archivo_adjuntos';
    protected $primaryKey = 'idArchivo';
    public $timestamps = false;

    protected $fillable = ['folioExpediente', 'tipoDocumento', 'nombreArchivo', 'rutaArchivo', 'fechaSubida'];
}
