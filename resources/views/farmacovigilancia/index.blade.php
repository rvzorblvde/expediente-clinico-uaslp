@extends('layouts.app')

@section('title', 'Farmacovigilancia - Unifarmacia')

@section('content')
    <div class="min-h-screen bg-slate-50">
        @include('layouts.navigation')

        <main class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">

            <x-breadcrumbs />

            {{-- Encabezado --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">Farmacovigilancia</h2>
                    <p class="text-slate-600 mt-1">Registro y consulta de reacciones adversas a medicamentos.</p>
                </div>
                <a href="{{ route('farmacovigilancia.create') }}"
                   class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    + Nuevo Reporte
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
                            <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">ID / Folio</th>
                            <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Medicamento Sospechoso</th>
                            <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Fecha Reacción</th>
                            <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Gravedad</th>
                            <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Evolución</th>
                            <th class="px-4 py-3 text-right font-semibold">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($reportes as $reporte)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-medium text-slate-900">RAM-{{ $reporte->id }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $reporte->medicamento_sospechoso }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $reporte->fecha_inicio_reaccion }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-block px-2 py-0.5 rounded text-xs font-medium
                                        {{ $reporte->gravedad == 'Grave' ? 'bg-red-50 text-red-700' : ($reporte->gravedad == 'Moderada' ? 'bg-amber-50 text-amber-700' : 'bg-blue-50 text-blue-700') }}">
                                        {{ $reporte->gravedad }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-600">{{ $reporte->evolucion }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end items-center gap-2">
                                        <a href="{{ route('farmacovigilancia.show', $reporte) }}"
                                           class="px-3 py-1 text-xs font-medium text-slate-700 bg-slate-100 rounded hover:bg-slate-200 transition-colors">
                                            Ver detalles
                                        </a>
                                        <a href="{{ route('farmacovigilancia.edit', $reporte) }}"
                                            class="px-3 py-1 text-xs font-medium text-blue-700 bg-blue-50 rounded hover:bg-blue-100 transition-colors">
                                                Editar
                                        </a>
                                        <form action="{{ route('farmacovigilancia.destroy', $reporte) }}" method="POST" class="inline"
                                              data-confirm="¿Eliminar el reporte {{ $reporte->folio }}? Esta acción no se puede deshacer.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">Eliminar</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-slate-500">
                                    No hay reportes de farmacovigilancia registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $reportes->links() }}
            </div>
        </main>
    </div>
@endsection
