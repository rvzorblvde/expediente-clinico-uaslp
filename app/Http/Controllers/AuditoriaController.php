<?php

namespace App\Http\Controllers;

use App\Models\Auditoria;
use Illuminate\Http\Request;

class AuditoriaController extends Controller
{
    public function index(Request $request)
    {
        // Obtiene los logs ordenados del más reciente al más antiguo con paginación
        $auditorias = Auditoria::with('usuario')
            ->orderBy('fechaAccion', 'desc')
            ->paginate(15);

        return view('auditorias.index', compact('auditorias'));
    }
}