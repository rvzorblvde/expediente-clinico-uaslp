@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-slate-50">
        @include('layouts.navigation')

        <main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

            {{-- Breadcrumbs --}}
            <div class="mb-6">
                <x-breadcrumbs />
            </div>

            {{-- Encabezado --}}
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-slate-900">Editar medicamento</h2>
            </div>

            {{-- Formulario de edición --}}
            <div class="flex-1 w-full">
                <form method="POST" action="{{ route('medicamentos.update', $medicamento) }}" autocomplete="off">
                    @csrf
                    @method('PUT')
                    @include('medicamentos._form', ['medicamento' => $medicamento, 'textoBoton' => 'Guardar cambios'])
                </form>
            </div>
        </main>
    </div>
@endsection