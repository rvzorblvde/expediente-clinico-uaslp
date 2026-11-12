@extends('layouts.app')

@section('title', 'Nuevo medicamento - Unifarmacia')

@section('content')
    <div class="min-h-screen bg-slate-50">
        @include('layouts.navigation')

        <main class="max-w-2xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <x-breadcrumbs />

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <h2 class="text-xl font-bold text-slate-900 mb-1">Nuevo medicamento</h2>
                <p class="text-sm text-slate-600 mb-6">Registra un medicamento en el catálogo. Los campos con * son obligatorios.</p>

                <form method="POST" action="{{ route('medicamentos.store') }}" id="form-medicamento">
                    @csrf
                    @include('medicamentos._form', ['medicamento' => null])

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <a href="{{ route(auth()->user()->can('manage-clinical-data') ? 'medicamentos.index' : 'dashboard') }}"
                           class="px-4 py-2 text-sm font-medium text-slate-700 bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">Cancelar</a>
                        <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50">
                            Guardar medicamento
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
@endsection
