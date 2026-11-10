@extends('layouts.app')

@section('title', 'Registrar Visita - Unifarmacia')

@section('content')
    <div class="min-h-screen bg-slate-50">
        @include('layouts.navigation')

        <main class="max-w-2xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-slate-900">Registrar Visita o Asesoría</h2>
                <p class="text-slate-600 text-sm mt-1">Complete los datos de la atención brindada al paciente.</p>
            </div>

            <form action="{{ route('visitas.store') }}" method="POST" class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Folio de Expediente (Opcional)</label>
                    <input type="text" name="folioExpediente" value="{{ old('folioExpediente') }}"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Ej. EXP-2026-001">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Fecha y Hora de la Visita</label>
                    <input type="datetime-local" name="fecha" value="{{ old('fecha', now()->format('Y-m-d\TH:i')) }}" 
                        min="{{ date('Y-m-d\TH:i') }}" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Motivo de la Visita / Observaciones</label>
                    <textarea name="motivo" rows="4" required
                              class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Describa la asesoría o motivo de la atención...">{{ old('motivo') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Estado de la Visita</label>
                    <select name="estado" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="completada" >Completada / Realizada</option>
                        <option value="agendada" selected>Agendada</option>
                        <option value="reagendada">Reagendada</option>
                        <option value="cancelada">Cancelada</option>
                    </select>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4">
                    <a href="{{ route('visitas.index') }}" class="px-4 py-2 border border-slate-300 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-100">
                        Cancelar
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                        Guardar Visita
                    </button>
                </div>
            </form>
        </main>
    </div>
@endsection