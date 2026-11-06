<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReporteController extends Controller
{
    //Muestra la pantalla principal de Estadísticas y Reportes.
    public function index(Request $request): View
    {
        //Usuarios activos
        $usuariosServicio = DB::table('expedientes')
            ->where('estado', 'activo')
            ->count();

        //Usuarios nuevos
        $inicioPeriodo = Carbon::now()->startOfMonth();
        $finPeriodo = Carbon::now()->endOfMonth();

        $usuariosNuevos = DB::table('expedientes')
            ->whereBetween('created_at', [$inicioPeriodo, $finPeriodo])
            ->count();

        $etiquetaPeriodo = Carbon::now()->locale('es')->isoFormat('MMMM YYYY');

        return view('reportes.index', compact('usuariosServicio', 'usuariosNuevos', 'etiquetaPeriodo', 'inicioPeriodo', 'finPeriodo'));
    }
}
