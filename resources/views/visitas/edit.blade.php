@extends('layouts.app')

@section('title', 'Editar Visita - Unifarmacia')

@section('content')
    <div class="min-h-screen bg-slate-50">
        @include('layouts.navigation')

        <main class="max-w-2xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-slate-900">Editar Visita #{{ $visita->idVisita }}</h2>
                <p class="text-slate-600 text-sm mt-1">Actualice los datos de la atención o cambie su estado.</p>
            </div>

            @if ($errors->any())
                <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
                    <p class="font-semibold mb-1">Por favor corrige los siguientes errores:</p>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('visitas.update', $visita->idVisita) }}" method="POST" class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Folio de Expediente (Opcional)</label>
                    <input type="text" name="folioExpediente" value="{{ old('folioExpediente', $visita->folioExpediente) }}"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Fecha y Hora de la Visita</label>
                    <input type="datetime-local" name="fecha" value="{{ old('fecha', \Carbon\Carbon::parse($visita->fecha)->format('Y-m-d\TH:i')) }}" 
                        min="{{ date('Y-m-d\TH:i') }}" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Motivo de la Visita / Observaciones</label>
                    <textarea name="motivo" rows="4" required
                              class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('motivo', $visita->motivo) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Estado de la Visita</label>
                    <select name="estado" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="completada" {{ old('estado', $visita->estado) == 'completada' ? 'selected' : '' }}>Completada / Realizada</option>
                        <option value="agendada" {{ old('estado', $visita->estado) == 'agendada' ? 'selected' : '' }}>Agendada</option>
                        <option value="reagendada" {{ old('estado', $visita->estado) == 'reagendada' ? 'selected' : '' }}>Reagendada</option>
                        <option value="cancelada" {{ old('estado', $visita->estado) == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                    </select>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4">
                    <a href="{{ route('visitas.index') }}" class="px-4 py-2 border border-slate-300 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-100">
                        Cancelar
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                        Actualizar Visita
                    </button>
                </div>
            </form>
        </main>
    </div>
@endsection