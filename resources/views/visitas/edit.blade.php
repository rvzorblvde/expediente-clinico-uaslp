@extends('layouts.app')

@section('title', 'Editar visita - Unifarmacia')

@section('content')
    <div class="min-h-screen bg-slate-50">
        @include('layouts.navigation')

        <main class="max-w-3xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <x-breadcrumbs />

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <h2 class="text-xl font-bold text-slate-900 mb-1">Editar visita {{ $visita->folioVisita }}</h2>
                <p class="text-sm text-slate-600 mb-6">
                    Paciente: <span class="font-medium text-slate-800">{{ $visita->nombre_paciente }}</span>
                    @if ($visita->folioExpediente) · Expediente {{ $visita->folioExpediente }} @endif
                </p>

                <form method="POST" action="{{ route('visitas.update', $visita) }}" id="form-visita">
                    @csrf
                    @method('PUT')

                    @include('visitas._form', [
                        'visita' => $visita,
                        'estados' => \App\Models\Visita::ESTADOS,
                        'tipos' => \App\Models\Visita::TIPOS,
                    ])

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <a href="{{ route('visitas.show', $visita) }}"
                           class="px-4 py-2 text-sm font-medium text-slate-700 bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">Cancelar</a>
                        <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50">
                            Actualizar visita
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
@endsection
