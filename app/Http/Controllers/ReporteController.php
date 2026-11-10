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

        //GRAFICAS
        //Grafica circular
        $totalUsuarios = $usuariosServicio + $usuariosPausa;
        $pctActivos = $totalUsuarios > 0 ? round(($usuariosServicio / $totalUsuarios) * 100, 1) : 0;
        $pctPausa   = $totalUsuarios > 0 ? round(($usuariosPausa / $totalUsuarios) * 100, 1) : 0;

        //Grafica lineal por año
        $añoActual = Carbon::now()->year;

        //Consulta a DB agrupando por mes
        $altasPorMes = DB::table('expedientes')
            ->selectRaw('EXTRACT(MONTH FROM created_at) as mes, COUNT(*) as total')
            ->whereYear('created_at', $añoActual)
            ->groupBy(DB::raw('EXTRACT(MONTH FROM created_at)'))
            ->pluck('total', 'mes')
            ->all();

        //Meses
        $mesesNombres = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        $mesActualNumero = Carbon::now()->month;

        $labelsMeses = [];
        $datosNuevosPorMes = [];

        for ($m = 1; $m <= $mesActualNumero; $m++) {
            $labelsMeses[] = $mesesNombres[$m - 1];
            $datosNuevosPorMes[] = $altasPorMes[$m] ?? 0;
        }

        //Grafica de variacion de usuarios
        $estadosPorMes = DB::table('expedientes')
            ->selectRaw('EXTRACT(MONTH FROM created_at) as mes, estado, COUNT(*) as total')
            ->whereYear('created_at', $añoActual)
            ->groupBy(DB::raw('EXTRACT(MONTH FROM created_at)'), 'estado')
            ->get();

        $datosActivosPorMes = [];
        $datosPausaPorMes   = [];

        for ($m = 1; $m <= $mesActualNumero; $m++) {
            $activosMes = $estadosPorMes->where('mes', $m)->where('estado', 'activo')->first();
            $pausaMes   = $estadosPorMes->where('mes', $m)->where('estado', 'inactivo')->first();

            $datosActivosPorMes[] = $activosMes ? $activosMes->total : 0;
            $datosPausaPorMes[]   = $pausaMes ? $pausaMes->total : 0;
        }

        //Grafica de problemas de salud analizados
        $problemasSalud = DB::table('antecedentes')
            ->select('nombre', DB::raw('COUNT(*) as total'))
            ->whereNotNull('nombre')
            ->where('nombre', '!=', '')
            ->groupBy('nombre')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        $problemasLabels = $problemasSalud->pluck('nombre')->toArray();
        $problemasData   = $problemasSalud->pluck('total')->toArray();

        // Grafica de medicamentos analizados
        $medicamentosAnalizados = DB::table('medicamentos')
            ->leftJoin('farmacovigilancia', function ($join) {
                $join->on(
                    DB::raw('LOWER(TRIM(medicamentos."principioActivo"))'),
                    '=',
                    DB::raw('LOWER(TRIM(farmacovigilancia.medicamento_sospechoso))')
                );
            })
            ->select(
                'medicamentos.principioActivo as nombre',
                DB::raw('COUNT(farmacovigilancia.id) as total')
            )
            ->whereNotNull('medicamentos.principioActivo')
            ->where('medicamentos.principioActivo', '!=', '')
            ->groupBy('medicamentos.principioActivo')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        $medicamentosLabels = $medicamentosAnalizados->pluck('nombre')->toArray();
        $medicamentosData   = $medicamentosAnalizados->pluck('total')->toArray();

        return view('reportes.index', compact(
            'usuariosServicio',
            'usuariosNuevos',
            'etiquetaPeriodo',
            'inicioPeriodo',
            'finPeriodo',
            'usuariosPausa',
            'variacion',
            'colorVariacion',
            'totalUsuarios',
            'pctActivos',
            'pctPausa',
            'labelsMeses',
            'datosNuevosPorMes',
            'datosActivosPorMes',
            'datosPausaPorMes',
            'problemasLabels',
            'problemasData',
            'medicamentosLabels',
            'medicamentosData'
        ));
    }
}
