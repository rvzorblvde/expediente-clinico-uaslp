@extends('layouts.app')

@section('title', 'Detalle del Reporte de RAM')

@section('content')
    <div class="min-h-screen bg-slate-50">
        @include('layouts.navigation')

        <main class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <x-breadcrumbs />

            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">Reporte de Farmacovigilancia #{{ $reporte->id }}</h2>
                    <p class="text-slate-600 text-sm mt-1">Registrado el {{ $reporte->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('farmacovigilancia.index') }}" 
                       class="px-4 py-2 bg-slate-200 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-300 transition-colors">
                        Volver
                    </a>
                    <a href="{{ route('farmacovigilancia.edit', $reporte->id) }}" 
                       class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        Editar Reporte
                    </a>
                </div>
            </div>

            <div class="space-y-6">
                {{-- 1. Consumidor del medicamento --}}
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                    <h3 class="text-lg font-semibold text-slate-900 border-b pb-2">1. Consumidor del medicamento</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                        <div><span class="font-medium text-slate-500">ID Paciente:</span> <p class="text-slate-900">{{ $reporte->paciente_id }}</p></div>
                        <div><span class="font-medium text-slate-500">Iniciales:</span> <p class="text-slate-900">{{ $reporte->iniciales ?? 'N/D' }}</p></div>
                        <div><span class="font-medium text-slate-500">Sexo biológico:</span> <p class="text-slate-900">{{ $reporte->sexo_biologico ?? 'N/D' }}</p></div>
                        <div><span class="font-medium text-slate-500">Peso:</span> <p class="text-slate-900">{{ $reporte->peso_kg ? $reporte->peso_kg . ' kg' : 'N/D' }}</p></div>
                        <div><span class="font-medium text-slate-500">Fecha de nacimiento:</span> <p class="text-slate-900">{{ $reporte->fecha_nacimiento_paciente ?? 'N/D' }}</p></div>
                        <div><span class="font-medium text-slate-500">Edad al comienzo:</span> <p class="text-slate-900">{{ $reporte->edad_comienzo_reaccion ?? 'N/D' }}</p></div>
                        <div><span class="font-medium text-slate-500">País de ocurrencia:</span> <p class="text-slate-900">{{ $reporte->pais_ocurrencia ?? 'México' }}</p></div>
                    </div>
                </div>

                {{-- 2. Describa lo sucedido --}}
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-2">
                    <h3 class="text-lg font-semibold text-slate-900 border-b pb-2">2. Describa lo sucedido</h3>
                    <p class="text-slate-800 text-sm whitespace-pre-line">{{ $reporte->descripcion_reaccion }}</p>
                </div>

                {{-- 3. Reacciones / Síntomas --}}
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                    <h3 class="text-lg font-semibold text-slate-900 border-b pb-2">3. Reacción(es) / Síntoma(s) / Signo(s)</h3>
                    @if($reporte->reacciones && $reporte->reacciones->count() > 0)
                        <div class="space-y-3">
                            @foreach($reporte->reacciones as $index => $reac)
                                <div class="p-4 bg-slate-50 rounded-lg border border-slate-200 text-sm space-y-2">
                                    <p class="font-semibold text-slate-900">Reacción #{{ $index + 1 }}: {{ $reac->sintoma_reaccion }}</p>
                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs text-slate-600">
                                        <div><span class="font-medium">Inicio:</span> {{ $reac->fecha_inicio ?? 'N/D' }}</div>
                                        <div><span class="font-medium">Fin:</span> {{ $reac->fecha_fin ?? 'N/D' }}</div>
                                        <div><span class="font-medium">Duración:</span> {{ $reac->duracion ?? 'N/D' }}</div>
                                        <div><span class="font-medium">Resultado:</span> {{ $reac->resultado_reaccion ?? 'N/D' }}</div>
                                    </div>
                                    <p class="text-xs"><span class="font-medium text-slate-600">¿Grave?:</span> <span class="{{ $reac->es_grave ? 'text-red-600 font-bold' : 'text-slate-700' }}">{{ $reac->es_grave ? 'Sí' : 'No' }}</span></p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        {{-- Fallback por si acaso es un reporte antiguo con campos simples --}}
                        <div class="p-4 bg-slate-50 rounded-lg border border-slate-200 text-sm space-y-2">
                            <p class="font-semibold text-slate-900">{{ $reporte->descripcion_reaccion }}</p>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs text-slate-600">
                                <div><span class="font-medium">Inicio:</span> {{ $reporte->fecha_inicio_reaccion ?? 'N/D' }}</div>
                                <div><span class="font-medium">Fin:</span> {{ $reporte->fecha_fin_reaccion ?? 'N/D' }}</div>
                                <div><span class="font-medium">Resultado:</span> {{ $reporte->resultado_reaccion ?? 'N/D' }}</div>
                                <div><span class="font-medium">Grave:</span> {{ $reporte->es_grave ? 'Sí' : 'No' }}</div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- 4. Medicamento Sospechoso y Concomitantes --}}
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                    <h3 class="text-lg font-semibold text-slate-900 border-b pb-2">4. Medicamentos</h3>
                    <div class="text-sm space-y-2">
                        <p class="font-semibold text-blue-900">Medicamento Sospechoso:</p>
                        <p class="text-slate-800 font-medium">{{ $reporte->medicamento_sospechoso }}</p>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs text-slate-600 pt-1">
                            <div><span class="font-medium">Fabricante:</span> {{ $reporte->fabricante_distribuidora ?? 'N/D' }}</div>
                            <div><span class="font-medium">Lote:</span> {{ $reporte->lote ?? 'N/D' }}</div>
                            <div><span class="font-medium">Dosis:</span> {{ $reporte->dosis ?? 'N/D' }}</div>
                            <div><span class="font-medium">Vía admin:</span> {{ $reporte->via_administracion ?? 'N/D' }}</div>
                            <div><span class="font-medium">Acción tomada:</span> {{ $reporte->accion_tomada ?? 'N/D' }}</div>
                            <div><span class="font-medium">Indicación:</span> {{ $reporte->indicacion_medicamento ?? 'N/D' }}</div>
                        </div>
                    </div>

                    @if($reporte->medicamentosConcomitantes && $reporte->medicamentosConcomitantes->count() > 0)
                        <div class="border-t pt-3 mt-3">
                            <p class="font-semibold text-slate-800 text-sm mb-2">Medicamentos Concomitantes:</p>
                            <ul class="list-disc list-inside text-xs text-slate-700 space-y-1">
                                @foreach($reporte->medicamentosConcomitantes as $concomitante)
                                    <li>{{ $concomitante->nombre_medicamento }} - Dosis: {{ $concomitante->dosis }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                {{-- 5. Información adicional --}}
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-3">
                    <h3 class="text-lg font-semibold text-slate-900 border-b pb-2">5. Información adicional</h3>
                    <div class="text-sm space-y-2">
                        <div><span class="font-medium text-slate-500">Enfermedades previas o actuales:</span> <p class="text-slate-800">{{ $reporte->enfermedades_previas ?? 'Ninguna' }}</p></div>
                        <div><span class="font-medium text-slate-500">Comentarios adicionales:</span> <p class="text-slate-800">{{ $reporte->comentarios_adicionales ?? 'Ninguno' }}</p></div>
                    </div>
                </div>

                {{-- 6. Información de contacto --}}
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                    <h3 class="text-lg font-semibold text-slate-900 border-b pb-2">6. Información de contacto (Notificador)</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                        <div><span class="font-medium text-slate-500">Entidad:</span> <p class="text-slate-900">{{ $reporte->notificador_entidad ?? 'San Luis Potosí' }}</p></div>
                        <div><span class="font-medium text-slate-500">Profesión:</span> <p class="text-slate-900">{{ $reporte->notificador_profesion ?? 'N/D' }}</p></div>
                        <div><span class="font-medium text-slate-500">Nombre completo:</span> <p class="text-slate-900">{{ $reporte->notificador_nombre }} {{ $reporte->notificador_apellido }}</p></div>
                        <div><span class="font-medium text-slate-500">Establecimiento:</span> <p class="text-slate-900">{{ $reporte->notificador_establecimiento ?? 'N/D' }}</p></div>
                        <div><span class="font-medium text-slate-500">Correo:</span> <p class="text-slate-900">{{ $reporte->notificador_correo ?? 'N/D' }}</p></div>
                        <div><span class="font-medium text-slate-500">Teléfono:</span> <p class="text-slate-900">{{ $reporte->notificador_telefono ?? 'N/D' }}</p></div>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection