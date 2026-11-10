<?php

namespace App\Observers;

use App\Models\Farmacovigilancia;
use App\Models\Auditoria;
use Illuminate\Support\Facades\Auth;

class FarmacovigilanciaObserver
{
    public function created(Farmacovigilancia $farmacovigilancia): void
    {
        $detalles = request()->isMethod('post') ? request()->all() : $farmacovigilancia->toArray();
        unset($detalles['_token'], $detalles['_method']);

        Auditoria::create([
            'idUsuario'     => Auth::id() ?? 1,
            'accion'        => 'Registro de reporte de Farmacovigilancia (RAM)',
            'tablaAfectada' => 'farmacovigilancia',
            'detalles'      => [
                'tipo'      => 'creacion',
                'datos'     => $detalles
            ],
            'fechaAccion'   => now(),
        ]);
    }

    public function updated(Farmacovigilancia $farmacovigilancia): void
    {
        Auditoria::create([
            'idUsuario'     => Auth::id() ?? 1,
            'accion'        => 'Actualización de reporte de Farmacovigilancia',
            'tablaAfectada' => 'farmacovigilancia',
            'detalles'      => [
                'tipo'      => 'modificacion',
                'cambios'   => $farmacovigilancia->getChanges(),
            ],
            'fechaAccion'   => now(),
        ]);
    }
}