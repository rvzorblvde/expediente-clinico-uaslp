<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    /**
     * Muestra la pantalla principal de Estadísticas y Reportes.
     */
    public function index(Request $request): View
    {
        //Usuarios activos
        $usuariosServicio = DB::table('expedientes')
            ->where('estado', 'activo')
            ->count();

        return view('reportes.index', compact('usuariosServicio'));
    }
}
