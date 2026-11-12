@extends('layouts.app')

@section('title', 'Medicamentos - Unifarmacia')

@section('content')
    <div class="min-h-screen bg-slate-50">
        @include('layouts.navigation')

        <main class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <x-breadcrumbs />

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">Medicamentos</h2>
                    <p class="text-slate-600 mt-1">Catálogo de medicamentos de la Unifarmacia.</p>
                </div>
                <a href="{{ route('medicamentos.create') }}"
                   class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    + Nuevo medicamento
                </a>
            </div>

            @if (session('success'))
                <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-800 text-sm">{{ session('error') }}</div>
            @endif

            {{-- Búsqueda y filtros --}}
            <form method="GET" action="{{ route('medicamentos.index') }}" class="mb-4 flex flex-col sm:flex-row gap-3">
                <input type="search" name="q" value="{{ $q }}" maxlength="100" placeholder="Buscar por nombre o principio activo"
                       class="flex-1 px-3 py-2 rounded-lg border border-slate-300 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                <select name="filtro" class="px-3 py-2 rounded-lg border border-slate-300 text-sm bg-white">
                    <option value="">Todos</option>
                    <option value="receta" @selected($filtro === 'receta')>Requieren receta</option>
                    <option value="controlado" @selected($filtro === 'controlado')>Controlados</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded-lg hover:bg-slate-900">Filtrar</button>
                @if ($q !== '' || $filtro)
                    <a href="{{ route('medicamentos.index') }}" class="px-4 py-2 text-sm text-slate-600 hover:underline self-center">Limpiar</a>
                @endif
            </form>

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Nombre</th>
                        <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Principio activo</th>
                        <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Tipo</th>
                        <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Laboratorio</th>
                        <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Presentación</th>
                        <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Concentración</th>
                        <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Receta</th>
                        <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Controlado</th>
                        <th class="px-4 py-3 text-right font-semibold">Acciones</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    @forelse ($medicamentos as $medicamento)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $medicamento->nombre }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $medicamento->principioActivo }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $medicamento->tipo === 'generico' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }}">{{ $medicamento->tipo === 'generico' ? 'Genérico' : 'Comercial' }}</span>
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $medicamento->laboratorio ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $medicamento->presentacion ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $medicamento->concentracion ?? '—' }}</td>
                            <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $medicamento->requiereReceta ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $medicamento->requiereReceta ? 'Sí' : 'No' }}
                                    </span>
                            </td>
                            <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $medicamento->esControlado ? 'bg-red-100 text-red-800' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $medicamento->esControlado ? 'Sí' : 'No' }}
                                    </span>
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <a href="{{ route('medicamentos.edit', $medicamento) }}" class="text-blue-600 hover:underline mr-3">Editar</a>
                                <form action="{{ route('medicamentos.destroy', $medicamento) }}" method="POST" class="inline"
                                      onsubmit="return confirm('¿Eliminar «{{ e($medicamento->nombre) }}»? Esta acción no se puede deshacer.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="px-4 py-8 text-center text-slate-500">No hay medicamentos que coincidan.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $medicamentos->links() }}</div>
        </main>
    </div>
@endsection
