<?php

namespace App\Http\Controllers;

use App\Models\Expediente;
use App\Models\User;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $expedientesActivos = Expediente::count();
        $expedientesInactivos = Expediente::onlyTrashed()->count();
        $totalUsuarios = User::count();

        return view('dashboard', compact(
            'expedientesActivos', 'expedientesInactivos', 'totalUsuarios'
        ));
    }
}
