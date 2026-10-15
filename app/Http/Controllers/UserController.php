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
        $users = User::with('role')->paginate(10);
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
            'idRol'     => ['required', 'exists:roles,idRol'],
            'nombres'   => ['required', 'string', 'max:100'],
            'apellido1' => ['required', 'string', 'max:100'],
            'apellido2' => ['nullable', 'string', 'max:100'],
            'correo'    => ['required', 'email', 'max:255', 'unique:users,correo'],
            'contrasena'=> ['required', 'string', 'min:6'],
        ]);

        User::create([
            'idRol'     => $validated['idRol'],
            'nombres'   => $validated['nombres'],
            'apellido1' => $validated['apellido1'],
            'apellido2' => $validated['apellido2'],
            'correo'    => $validated['correo'],
            'contrasena'=> $validated['contrasena'], // El modelo se encarga de aplicarle Hash automáticamente
            'estado'    => 'activo',
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
            'idRol'     => ['required', 'exists:roles,idRol'],
            'nombres'   => ['required', 'string', 'max:100'],
            'apellido1' => ['required', 'string', 'max:100'],
            'apellido2' => ['nullable', 'string', 'max:100'],
            'correo'    => [
                'required', 'email', 'max:255',
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

    public function resetPassword(Request $request, User $usuario)
    {
        $validated = $request->validate([
            'contrasena' => ['required', 'string', 'min:6'],
        ]);

        $usuario->update([
            'contrasena' => $validated['contrasena'],
        ]);

        return back()->with('success', 'Credenciales restablecidas exitosamente.');
    }
}