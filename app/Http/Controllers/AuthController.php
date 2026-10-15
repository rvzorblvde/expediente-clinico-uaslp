<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Return towards the login form
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Validar credenciales para el inicio de sesión
    public function login (Request $request)
    {
        $credentials = $request->validate([
            'correo' => ['required', 'email'],
            'contrasena' => ['required'],
        ]);

        $attemptData = [
            'correo' => $credentials['correo'],
            'password' => $credentials['contrasena']
        ];

        // Buscar al usuario por correo antes de autenticar
        $user = \App\Models\User::where('correo', $credentials['correo'])->first();

        // Verificar si el usuario existe y si su estado es inactivo
        if ($user && $user->estado !== 'activo') {
            return back()->withErrors([
                'correo' => 'Esta cuenta se encuentra inactiva. Contacte al administrador.',
            ])->onlyInput('correo');
        }

        // Redirigir al dashboard
        if (Auth::attempt($attemptData, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'correo' => 'Datos de inicio de sesión incorrectos',
        ])->onlyInput('correo');

    }

    // Request a password change to the admin
    public function pwResetRequest (Request $request)
    {
        // Lógica pendiente para notificar al responsable sanitario
    }

    // Close the session
    public function logout (Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login.form');
    }
}
