@extends('layouts.app')

@section('title', 'Medicamentos - Unifarmacia')

@section('content')
    <div class="min-h-screen bg-slate-50">
        @include('layouts.navigation')

        <main class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">

            <x-breadcrumbs />

            {{-- Encabezado --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">Medicamentos</h2>
                    <p class="text-slate-600 mt-1">Consulta, edita o elimina los medicamentos del catálogo.</p>
                </div>
                <a href="{{ route('medicamentos.create') }}"
                   class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    + Nuevo medicamento
                </a>
            </div>

            {{-- Mensaje de éxito --}}
            @if (session('success'))
                <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Tabla --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Nombre</th>
                            <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Principio activo</th>
                            <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Presentación</th>
                            <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Concentración</th>
                            <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Requiere receta</th>
                            <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Controlado</th>
                            <th class="px-4 py-3 text-right font-semibold">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($medicamentos as $medicamento)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-medium text-slate-900">{{ $medicamento->nombre }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $medicamento->principioActivo }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $medicamento->presentacion ?? '—' }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $medicamento->concentracion ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-block px-2 py-0.5 rounded text-xs font-medium
                                        {{ $medicamento->requiereReceta ? 'bg-amber-50 text-amber-700' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $medicamento->requiereReceta ? 'Sí' : 'No' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-block px-2 py-0.5 rounded text-xs font-medium
                                        {{ $medicamento->esControlado ? 'bg-red-50 text-red-700' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $medicamento->esControlado ? 'Sí' : 'No' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end items-center gap-2">
                                        <a href="{{ route('medicamentos.edit', $medicamento) }}"
                                           class="px-3 py-1 text-xs font-medium text-blue-700 bg-blue-50 rounded hover:bg-blue-100 transition-colors">
                                            Editar
                                        </a>

                                        <form method="POST" action="{{ route('medicamentos.destroy', $medicamento) }}"
                                              onsubmit="return confirm('¿Eliminar el medicamento {{ $medicamento->nombre }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="px-3 py-1 text-xs font-medium text-red-700 bg-red-50 rounded hover:bg-red-100 transition-colors">
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-10 text-center text-slate-500">
                                    No hay medicamentos registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </main>
    </div>
@endsection
