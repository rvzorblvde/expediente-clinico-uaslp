<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class ReporteController extends Controller
{
    /**
     * Muestra la pantalla principal de Estadísticas y Reportes.
     */
    public function index(): View
    {
        return view('reportes.index');
    }
}
