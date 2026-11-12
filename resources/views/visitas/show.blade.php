@extends('layouts.app')

@section('title', 'Detalle de visita - Unifarmacia')

@section('content')
    @php
            $badges = [
                'completada' => 'bg-green-100 text-green-800',
                'agendada'   => 'bg-blue-100 text-blue-800',
                'cancelada'  => 'bg-red-100 text-red-800',
                'no_asistio' => 'bg-amber-100 text-amber-800',
            ];
            $t = $visita->datosAntropometricos;
            $filasTriage = $t ? [
                'Peso' => $t->peso ? "{$t->peso} kg" : null,
                'Talla' => $t->talla ? "{$t->talla} m" : null,
                'IMC' => $t->imc,
                'Temperatura' => $t->temperatura ? "{$t->temperatura} °C" : null,
                'Presión' => ($t->presionSistolica && $t->presionDiastolica) ? "{$t->presionSistolica}/{$t->presionDiastolica} mmHg" : null,
                'Pulso' => $t->pulsoPpm ? "{$t->pulsoPpm} lpm" : null,
                'F. respiratoria' => $t->frecuenciaRespiratoria ? "{$t->frecuenciaRespiratoria} rpm" : null,
                'SpO₂' => $t->saturacionOxigeno ? "{$t->saturacionOxigeno} %" : null,
            ] : [];
    @endphp

    <div class="min-h-screen bg-slate-50">
        @include('layouts.navigation')

        <main class="max-w-3xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-slate-900">Visita {{ $visita->folioVisita }}</h2>
                <a href="{{ route('visitas.index') }}"
                   class="px-4 py-2 bg-slate-200 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-300 transition-colors">← Volver</a>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 border-b border-slate-100 pb-4">
                    <div>
                        <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Paciente</span>
                        <span class="text-slate-900 font-medium">{{ $visita->nombre_paciente }}</span>
                        @if ($visita->folioExpediente)
                            <a href="{{ route('expedientes.show', $visita->folioExpediente) }}" class="block text-xs text-blue-600 hover:underline">
                                Expediente {{ $visita->folioExpediente }}
                            </a>
                        @elseif ($visita->contactoVisitante)
                            <span class="block text-xs text-slate-500">Contacto: {{ $visita->contactoVisitante }}</span>
                        @endif
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Estado</span>
                        <span class="inline-block mt-1 px-3 py-1 text-xs font-semibold rounded-full {{ $badges[$visita->estado] ?? 'bg-slate-100 text-slate-600' }}">
                            {{ \App\Models\Visita::ESTADOS[$visita->estado] ?? $visita->estado }}
                        </span>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Fecha y hora</span>
                        <span class="text-slate-800 text-sm">{{ $visita->fecha->format('d/m/Y H:i') }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Atendió</span>
                        <span class="text-slate-800 text-sm">{{ $visita->usuario?->nombres }} {{ $visita->usuario?->apellido1 }} {{ $visita->usuario?->apellido2 }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Tipo</span>
                        <span class="text-slate-800 text-sm">{{ \App\Models\Visita::TIPOS[$visita->tipo] ?? $visita->tipo }}</span>
                    </div>
                </div>

                <div>
                    <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Motivo</span>
                    <div class="p-4 bg-slate-50 rounded-lg border border-slate-200 text-slate-700 text-sm whitespace-pre-line">{{ $visita->motivo }}</div>
                </div>

                @if ($visita->observaciones)
                    <div>
                        <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Observaciones</span>
                        <div class="p-4 bg-slate-50 rounded-lg border border-slate-200 text-slate-700 text-sm whitespace-pre-line">{{ $visita->observaciones }}</div>
                    </div>
                @endif

                @if ($visita->motivoCancelacion)
                    <div>
                        <span class="block text-xs font-semibold text-red-400 uppercase tracking-wider mb-1">Motivo de cancelación</span>
                        <div class="p-4 bg-red-50 rounded-lg border border-red-200 text-red-800 text-sm whitespace-pre-line">{{ $visita->motivoCancelacion }}</div>
                    </div>
                @endif

                @if ($t)
                    <div>
                        <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Triage / signos vitales</span>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm p-4 bg-slate-50 rounded-lg border border-slate-200">
                            @foreach ($filasTriage as $etiqueta => $valor)
                                <div>
                                    <span class="text-slate-400 text-xs block">{{ $etiqueta }}</span>
                                    <strong class="text-slate-700">{{ $valor ?? '—' }}</strong>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($visita->registrosMedicacion->isNotEmpty())
                    <div>
                        <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Historia de medicación</span>
                        <table class="min-w-full text-sm border border-slate-200 rounded-lg overflow-hidden">
                            <thead class="bg-slate-50 text-slate-600">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold">Medicamento</th>
                                <th class="px-3 py-2 text-left font-semibold">Dosis</th>
                                <th class="px-3 py-2 text-left font-semibold">Vía</th>
                                <th class="px-3 py-2 text-left font-semibold">Frecuencia</th>
                                <th class="px-3 py-2 text-left font-semibold">Indicaciones</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                            @foreach ($visita->registrosMedicacion as $r)
                                <tr>
                                    <td class="px-3 py-2 font-medium text-slate-800">{{ $r->medicamento?->nombre ?? '—' }}</td>
                                    <td class="px-3 py-2 text-slate-600">{{ $r->dosis ?? '—' }}</td>
                                    <td class="px-3 py-2 text-slate-600">{{ $r->viaAdministracion ?? '—' }}</td>
                                    <td class="px-3 py-2 text-slate-600">{{ $r->frecuenciaUso ?? '—' }}</td>
                                    <td class="px-3 py-2 text-slate-600">{{ $r->indicaciones ?? '—' }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('visitas.edit', $visita) }}"
                       class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">Editar</a>
                    @can('is-admin')
                        <form action="{{ route('visitas.destroy', $visita) }}" method="POST" class="inline"
                              data-confirm="¿Eliminar la visita {{ $visita->folioVisita }} del historial?"
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-50 text-red-600 text-sm font-medium rounded-lg hover:bg-red-100 transition-colors">
                                Eliminar
                            </button>
                        </form>
                    @endcan
                </div>
            </div>
        </main>
    </div>
@endsection
