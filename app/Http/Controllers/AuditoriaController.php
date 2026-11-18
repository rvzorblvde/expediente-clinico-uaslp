<?php

namespace App\Http\Controllers;

use App\Models\Auditoria;
use Illuminate\Http\Request;

class AuditoriaController extends Controller
{
    public function index(Request $request)
    {
        $query = Auditoria::with('usuario')
            ->orderBy('fechaAccion', 'desc');

        // 1. Filtro por Acción
        if ($request->filled('accion')) {
            $query->where('accion', $request->accion);
        }

        // 2. Filtro por Módulo (tablaAfectada)
        if ($request->filled('modulo')) {
            $query->where('tablaAfectada', $request->modulo);
        }

        // 3. Filtro por Usuario (nombre, apellidos o correo)
        if ($request->filled('usuario')) {
            $busqueda = trim($request->usuario);
            $palabras = preg_split('/\s+/', $busqueda);

            $query->where(function ($q) use ($palabras, $busqueda) {
                $q->whereHas('usuario', function ($u) use ($palabras) {
                    foreach ($palabras as $palabra) {
                        $u->where(function ($sub) use ($palabra) {
                            $sub->where('nombres', 'like', "%{$palabra}%")
                                ->orWhere('apellido1', 'like', "%{$palabra}%")
                                ->orWhere('apellido2', 'like', "%{$palabra}%")
                                ->orWhere('correo', 'like', "%{$palabra}%");
                        });
                    }
                });

                if (stripos('sistema desconocido', $busqueda) !== false) {
                    $q->orWhereNull('idUsuario');
                }
            });
        }

        // 4. Filtro por rango de fechas
        if ($request->filled('desde')) {
            $query->whereDate('fechaAccion', '>=', $request->desde);
        }

        if ($request->filled('hasta')) {
            $query->whereDate('fechaAccion', '<=', $request->hasta);
        }

        $auditorias = $query->paginate(15)->withQueryString();

        // Catálogos dinámicos con los valores existentes en la tabla
        $acciones = Auditoria::select('accion')
            ->whereNotNull('accion')
            ->distinct()
            ->orderBy('accion')
            ->pluck('accion');

        $modulos = Auditoria::select('tablaAfectada')
            ->whereNotNull('tablaAfectada')
            ->distinct()
            ->orderBy('tablaAfectada')
            ->pluck('tablaAfectada');

        return view('auditorias.index', compact('auditorias', 'acciones', 'modulos'));
    }
}