@extends('layouts.app')

@section('title', 'Detalle de Visita - Unifarmacia')

@section('content')
    <div class="min-h-screen bg-slate-50">
        @include('layouts.navigation')

        <main class="max-w-3xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">Detalle de la Visita #{{ $visita->idVisita }}</h2>
                    <p class="text-slate-600 text-sm mt-1">Información registrada de la atención o asesoría.</p>
                </div>
                <a href="{{ route('visitas.index') }}" 
                   class="px-4 py-2 bg-slate-200 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-300 transition-colors">
                    ← Volver al Historial
                </a>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 border-b border-slate-200 pb-4">
                    <div>
                        <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Folio de Expediente</span>
                        <span class="text-slate-900 font-medium text-base">{{ $visita->folioExpediente ?? 'No asociado' }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Estado de la Visita</span>
                        <span class="inline-block mt-1 px-3 py-1 text-xs font-semibold rounded-full 
                            {{ $visita->estado == 'completada' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $visita->estado == 'agendada' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $visita->estado == 'reagendada' ? 'bg-amber-100 text-amber-800' : '' }}
                            {{ $visita->estado == 'cancelada' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ ucfirst($visita->estado) }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 border-b border-slate-200 pb-4">
                    <div>
                        <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Fecha y Hora</span>
                        <span class="text-slate-800 text-sm">{{ \Carbon\Carbon::parse($visita->fecha)->format('d/m/Y H:i') }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Registrado por (Usuario ID)</span>
                        <span class="text-slate-800 text-sm">#{{ $visita->idUsuario }}</span>
                    </div>
                </div>

                <div>
                    <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Motivo / Observaciones de la Asesoría</span>
                    <div class="p-4 bg-slate-50 rounded-lg border border-slate-200 text-slate-700 text-sm whitespace-pre-line">
                        {{ $visita->motivo }}
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                    <a href="{{ route('visitas.edit', $visita->idVisita) }}" 
                       class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        Editar Visita
                    </a>
                    
                    <form action="{{ route('visitas.destroy', $visita->idVisita) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar esta visita del historial?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-50 text-red-600 text-sm font-medium rounded-lg hover:bg-red-100 transition-colors">
                            Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </main>
    </div>
@endsection