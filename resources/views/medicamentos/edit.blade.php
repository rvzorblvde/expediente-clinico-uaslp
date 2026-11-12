@extends('layouts.app')

@section('title', 'Editar medicamento - Unifarmacia')

@section('content')
    <div class="min-h-screen bg-slate-50">
        @include('layouts.navigation')

        <main class="max-w-2xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <x-breadcrumbs />

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <h2 class="text-xl font-bold text-slate-900 mb-1">Editar medicamento</h2>
                <p class="text-sm text-slate-600 mb-6">Los campos con * son obligatorios.</p>

                <form method="POST" action="{{ route('medicamentos.update', $medicamento) }}" id="form-medicamento">
                    @csrf
                    @method('PUT')
                    @include('medicamentos._form', ['medicamento' => $medicamento])

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <a href="{{ route('medicamentos.index') }}"
                           class="px-4 py-2 text-sm font-medium text-slate-700 bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">Cancelar</a>
                        <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50">
                            Actualizar medicamento
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
@endsection
