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

        //Usuarios en pausa
        $usuariosPausa = DB::table('expedientes')
            ->where('estado', 'inactivo')
            ->count();

        //Variacion porcentual
        $inicioPeriodoAnterior = Carbon::now()->subMonth()->startOfMonth();
        $finPeriodoAnterior    = Carbon::now()->subMonth()->endOfMonth();

        $usuariosPeriodoAnterior = DB::table('expedientes')
            ->whereBetween('created_at', [$inicioPeriodoAnterior, $finPeriodoAnterior])
            ->count();

        if ($usuariosPeriodoAnterior > 0) {
            $diferencia = $usuariosNuevos - $usuariosPeriodoAnterior;
            $variacionCalculada = round(($diferencia / $usuariosPeriodoAnterior) * 100, 1);
            $signo = $variacionCalculada > 0 ? '+' : '';
            $variacion = $signo . $variacionCalculada . '%';
            $colorVariacion = $variacionCalculada >= 0 ? 'text-emerald-600' : 'text-rose-600';
        } else {
            $variacion = $usuariosNuevos > 0 ? '+100%' : '0%';
            $colorVariacion = 'text-emerald-600';
        }

        return view('reportes.index', compact('usuariosServicio', 'usuariosNuevos', 'etiquetaPeriodo', 'inicioPeriodo', 'finPeriodo', 'usuariosPausa', 'variacion', 'colorVariacion'));
    }
}
