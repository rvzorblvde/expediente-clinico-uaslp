<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordHistory extends Model
{
    protected $table = 'password_histories';

    protected $fillable = [
        'idUsuario',
        'contrasena',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'idUsuario', 'idUsuario');
    }
}