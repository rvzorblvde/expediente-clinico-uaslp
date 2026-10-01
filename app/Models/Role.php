<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';         // Indicar la tabla a la que pertenece este modelo
    protected $primaryKey = 'idRol';    // Indicar su PK
    public $timestamps = false;         // Los timestamps están desactivados

    protected $fillable = [
        'nombreRol'
    ];
}
