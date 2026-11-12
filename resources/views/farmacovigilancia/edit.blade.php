@extends('layouts.app')

@section('title', 'Editar Reporte - Unifarmacia')

@section('content')
    <div class="min-h-screen bg-slate-50">
        @include('layouts.navigation')

        <main class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <x-breadcrumbs />

            <div class="mb-6">
                <h2 class="text-2xl font-bold text-slate-900">Editar reporte {{ $reporte->folio }}</h2>
                <p class="text-slate-600 mt-1">Actualiza la información necesaria del reporte.</p>
            </div>

            @if ($errors->any())
                <div class="mb-6 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-800 text-sm">
                    <p class="font-semibold mb-1">Por favor corrige los siguientes errores:</p>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('farmacovigilancia.update', $reporte) }}" method="POST" class="space-y-6" id="form-ram">
                @csrf
                @method('PUT')
                @include('farmacovigilancia._form', ['reporte' => $reporte])

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('farmacovigilancia.index') }}"
                       class="px-4 py-2 text-sm font-medium text-slate-700 bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">Cancelar</a>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50">
                        Actualizar notificación
                    </button>
                </div>
            </form>
        </main>
    </div>
@endsection
