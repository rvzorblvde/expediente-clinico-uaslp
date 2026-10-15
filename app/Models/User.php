<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'idUsuario';

    protected $fillable = [
        'idRol',
        'nombres',
        'apellido1',
        'apellido2',
        'correo',
        'contrasena',
        'estado',
    ];

    protected $hidden = [
        'contrasena',           // Se oculta la contraseña al hacer consultas
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'contrasena' => 'hashed',           // Encriptar la contraseña automáticamente
        ];
    }

    /**
     * Obtiene el nombre de la columna que almacena la contraseña.
     */
    public function getAuthPasswordName():string
    {
        return 'contrasena';
    }

    /**
     * Obtiene el nombre de la columna usada para recordar sesión.
     */
    public function getAuthIdentifierName():string
    {
        return 'idUsuario';
    }

    public function isResponsableSanitario(): bool
    {
        return $this->idRol == 1;
    }
    
    public function role()
{
    return $this->belongsTo(Role::class, 'idRol', 'idRol');
}
}
