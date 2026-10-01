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

        return view('dashboard', compact(
            'expedientesActivos', 'expedientesInactivos', 'totalUsuarios'
        ));
    }
}
