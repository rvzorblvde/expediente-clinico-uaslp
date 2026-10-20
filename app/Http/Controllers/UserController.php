<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with('role')
            ->orderBy('reset_requested', 'desc')
            ->paginate(10);

        return view('usuarios.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('usuarios.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'idRol' => ['required', 'exists:roles,idRol'],
            'nombres' => ['required', 'string', 'max:100'],
            'apellido1' => ['required', 'string', 'max:100'],
            'apellido2' => ['nullable', 'string', 'max:100'],
            'correo' => ['required', 'email', 'max:255', 'unique:users,correo'],
            'contrasena' => ['required', 'string', 'min:6'],
        ]);

        User::create([
            'idRol' => $validated['idRol'],
            'nombres' => $validated['nombres'],
            'apellido1' => $validated['apellido1'],
            'apellido2' => $validated['apellido2'],
            'correo' => $validated['correo'],
            'contrasena' => $validated['contrasena'], // El modelo se encarga de aplicarle Hash automáticamente
            'estado' => 'activo',
        ]);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    public function edit(User $usuario)
    {
        $roles = Role::all();
        return view('usuarios.edit', compact('usuario', 'roles'));
    }

    public function update(Request $request, User $usuario)
    {
        $validated = $request->validate([
            'idRol' => ['required', 'exists:roles,idRol'],
            'nombres' => ['required', 'string', 'max:100'],
            'apellido1' => ['required', 'string', 'max:100'],
            'apellido2' => ['nullable', 'string', 'max:100'],
            'correo' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'correo')->ignore($usuario->idUsuario, 'idUsuario'),
            ],
        ]);

        $usuario->update($validated);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy(User $usuario)
    {
        // Cambiar estado a inactivo o activar en lugar de eliminar físicamente
        $nuevoEstado = $usuario->estado === 'activo' ? 'inactivo' : 'activo';
        $usuario->update(['estado' => $nuevoEstado]);

        return back()->with('success', "Estado del usuario actualizado a {$nuevoEstado}.");
    }

    public function resetPassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'contrasena' => ['required', 'string', 'min:6'],
        ]);

        $user->update([
            'contrasena' => Hash::make($validated['contrasena']),
            'reset_requested' => false,
        ]);

        return back()->with('success', 'Credenciales restablecidas exitosamente.');
    }

    public function showChangePasswordForm()
    {
        return view('usuarios.change-password');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'contrasena' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'current_password.current_password' => 'La contraseña actual es incorrecta.',
            'contrasena.required' => 'Por favor, ingresa una nueva contraseña.',
            'contrasena.min' => 'La nueva contraseña debe tener al menos 6 caracteres.',
            'contrasena.confirmed' => 'La confirmación de la contraseña no coincide.',
        ]);

        $user = auth()->user();

        // Verificar si la nueva contraseña fue utilizada en los últimos 3 meses
        $tresMesesAtras = now()->subMonths(3);
        
        $historialReciente = \App\Models\PasswordHistory::where('idUsuario', $user->idUsuario)
            ->where('created_at', '>=', $tresMesesAtras)
            ->get();

        foreach ($historialReciente as $historial) {
            if (Hash::check($validated['contrasena'], $historial->contrasena)) {
                return back()->withErrors([
                    'contrasena' => 'No puedes utilizar una contraseña que haya sido empleada en los últimos 3 meses.'
                ])->withInput();
            }
        }

        // Verificar contra la contraseña actual del usuario
        if (Hash::check($validated['contrasena'], $user->contrasena)) {
            return back()->withErrors([
                'contrasena' => 'La nueva contraseña no puede ser igual a tu contraseña actual.'
            ])->withInput();
        }

        // Guardar la contraseña actual en el historial antes de cambiarla
        \App\Models\PasswordHistory::create([
            'idUsuario' => $user->idUsuario,
            'contrasena' => $user->contrasena,
        ]);

        // Actualizar con la nueva contraseña
        $user->update([
            'contrasena' => $validated['contrasena'],
        ]);

        // Depurar historiales más antiguos de 3 meses para limpieza
        \App\Models\PasswordHistory::where('idUsuario', $user->idUsuario)
            ->where('created_at', '<', $tresMesesAtras)
            ->delete();

        return back()->with('success', 'Contraseña actualizada exitosamente.');
    }
}