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
        // return view('auth.login');
    }

    // Validate credentials and login
    public function login (Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Datos de inicio de sesión incorrectos',
        ])->onlyInput('email');

    }

    // Request a password change to the admin
    public function pwResetRequest (Request $request)
    {
        // Lógica pendiente para notificar al responsable sanitario
    }

    // Close the session
    public function logout ()
    {
        Auth::logout();
        return session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login,form');
    }
}
