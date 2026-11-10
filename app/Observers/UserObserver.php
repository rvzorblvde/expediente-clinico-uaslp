<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Auditoria;
use Illuminate\Support\Facades\Auth;

class UserObserver
{
    public function created(User $user): void
    {
        $detalles = request()->isMethod('post') ? request()->all() : $user->toArray();
        
        unset($detalles['_token'], $detalles['_method'], $detalles['contrasena'], $detalles['password'], $detalles['password_confirmation']);

        Auditoria::create([
            'idUsuario'     => Auth::id() ?? 1,
            'accion'        => 'Creación de usuario del sistema',
            'tablaAfectada' => 'users',
            'detalles'      => [
                'tipo'      => 'creacion',
                'datos'     => $detalles
            ],
            'fechaAccion'   => now(),
        ]);
    }

    public function updated(User $user): void
    {
        $changes = $user->getChanges();
        unset($changes['contrasena'], $changes['password']);

        Auditoria::create([
            'idUsuario'     => Auth::id() ?? 1,
            'accion'        => 'Actualización de datos de usuario',
            'tablaAfectada' => 'users',
            'detalles'      => [
                'tipo'      => 'modificacion',
                'cambios'   => $changes,
                'original'  => array_intersect_key($user->getOriginal(), $changes)
            ],
            'fechaAccion'   => now(),
        ]);
    }
}