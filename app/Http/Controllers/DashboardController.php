<?php

namespace App\Http\Controllers;

use App\Models\Expediente;
use App\Models\User;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $expedientesActivos = Expediente::where('estado', 'activo')->count();
        $expedientesInactivos = Expediente::where('estado', '!=', 'activo')->count();
        $totalUsuarios = User::count();
        
        // Contar cuántos usuarios tienen una solicitud de restablecimiento pendiente
        $solicitudesPendientes = User::where('reset_requested', true)->count();

        return view('dashboard', compact(
            'expedientesActivos', 'expedientesInactivos', 'totalUsuarios', 'solicitudesPendientes'
        ));
    }
}