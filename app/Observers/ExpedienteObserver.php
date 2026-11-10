<?php

namespace App\Observers;

use App\Models\Expediente;
use App\Models\Auditoria;
use Illuminate\Support\Facades\Auth;

class ExpedienteObserver
{
    public function created(Expediente $expediente): void
    {
        $expediente->loadMissing('paciente');

        $detalles = request()->isMethod('post') ? request()->all() : $expediente->toArray();
        unset($detalles['_token'], $detalles['_method']);

        Auditoria::create([
            'idUsuario'     => Auth::id() ?? 1,
            'accion'        => 'Creación de expediente',
            'tablaAfectada' => 'expedientes',
            'detalles'      => [
                'tipo'      => 'creacion_completa',
                'paciente'  => $expediente->paciente ? $expediente->paciente->nombres . ' ' . $expediente->paciente->apellido1 . ' ' . ($expediente->paciente->apellido2 ?? '') : 'No asignado',
                'datos'     => $detalles
            ],
            'fechaAccion'   => now(),
        ]);
    }

    public function updating(Expediente $expediente): void
    {
        // Detectamos si lo que está cambiando es el estado (activación / desactivación)
        if ($expediente->isDirty('estado')) {
            $nuevoEstado = strtolower($expediente->estado);
            $anteriorEstado = strtolower($expediente->getOriginal('estado') ?? 'desconocido');

            $esDesactivacion = in_array($nuevoEstado, ['inactivo', 'baja', 'suspendido']);
            $accion = $esDesactivacion ? 'Desactivación de expediente' : 'Activación de expediente';
            $tipo = $esDesactivacion ? 'desactivacion' : 'activacion';

            $expediente->loadMissing('paciente');

            Auditoria::create([
                'idUsuario'     => Auth::id() ?? 1,
                'accion'        => $accion,
                'tablaAfectada' => 'expedientes',
                'detalles'      => [
                    'tipo'      => $tipo,
                    'paciente'  => $expediente->paciente ? $expediente->paciente->nombres . ' ' . $expediente->paciente->apellido1 . ' ' . ($expediente->paciente->apellido2 ?? '') : 'No asignado',
                    'cambios'   => [
                        'estado' => [
                            'anterior' => $anteriorEstado,
                            'nuevo'    => $nuevoEstado
                        ]
                    ]
                ],
                'fechaAccion'   => now(),
            ]);
        }
    }

    public function deleted(Expediente $expediente): void
    {
        $expediente->loadMissing('paciente');

        Auditoria::create([
            'idUsuario'     => Auth::id() ?? 1,
            'accion'        => 'Eliminación física de expediente',
            'tablaAfectada' => 'expedientes',
            'detalles'      => [
                'tipo'      => 'eliminacion',
                'paciente'  => $expediente->paciente ? $expediente->paciente->nombres . ' ' . $expediente->paciente->apellido1 . ' ' . ($expediente->paciente->apellido2 ?? '') : 'No asignado',
                'registro'  => $expediente->toArray()
            ],
            'fechaAccion'   => now(),
        ]);
    }
}