<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Validation\ValidationException;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    const ROLE_RESPONSABLE = 'responsable_sanitario';
    const ROLE_TRABAJADOR = 'trabajador';
    const ROLE_ALUMNO = 'alumno';

    const DOMINIO_RESPONSABLE = '@admin.edu.mx';

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (User $user) {
            // Auto-asignar rol si el correo pertenece al dominio del responsable
            if (str_ends_with($user->email, self::DOMINIO_RESPONSABLE)) {
                $user->role = self::ROLE_RESPONSABLE;
            }

            // Solo puede haber un responsable sanitario
            if ($user->role === self::ROLE_RESPONSABLE
                && self::where('role', self::ROLE_RESPONSABLE)->exists()) {
                throw ValidationException::withMessages([
                    'email' => 'Ya existe un responsable sanitario registrado en el sistema.',
                ]);
            }
        });
    }

    public function isResponsableSanitario(): bool
    {
        return $this->role === self::ROLE_RESPONSABLE;
    }
}
