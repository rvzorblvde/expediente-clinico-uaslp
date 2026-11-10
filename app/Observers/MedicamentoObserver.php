<?php

namespace App\Observers;

use App\Models\Medicamento;
use App\Models\Auditoria;
use Illuminate\Support\Facades\Auth;

class MedicamentoObserver
{
    public function created(Medicamento $medicamento): void
    {
        $detalles = request()->isMethod('post') ? request()->all() : $medicamento->toArray();
        unset($detalles['_token'], $detalles['_method']);

        Auditoria::create([
            'idUsuario'     => Auth::id() ?? 1,
            'accion'        => 'Creación de medicamento en inventario',
            'tablaAfectada' => 'medicamentos',
            'detalles'      => [
                'tipo'      => 'creacion',
                'datos'     => $detalles
            ],
            'fechaAccion'   => now(),
        ]);
    }

    public function updated(Medicamento $medicamento): void
    {
        Auditoria::create([
            'idUsuario'     => Auth::id() ?? 1,
            'accion'        => 'Actualización de medicamento',
            'tablaAfectada' => 'medicamentos',
            'detalles'      => [
                'tipo'      => 'modificacion',
                'cambios'   => $medicamento->getChanges(),
                'original'  => array_intersect_key($medicamento->getOriginal(), $medicamento->getChanges())
            ],
            'fechaAccion'   => now(),
        ]);
    }

    public function deleted(Medicamento $medicamento): void
    {
        Auditoria::create([
            'idUsuario'     => Auth::id() ?? 1,
            'accion'        => 'Eliminación de medicamento',
            'tablaAfectada' => 'medicamentos',
            'detalles'      => [
                'tipo'      => 'eliminacion',
                'registro'  => $medicamento->toArray()
            ],
            'fechaAccion'   => now(),
        ]);
    }
}