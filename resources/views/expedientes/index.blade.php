@extends('layouts.app')

@section('title', 'Expedientes - Unifarmacia')

@section('content')
    @php
        $cabeceras = [
            'idPaciente' => 'ID Paciente',
            'folio'      => 'Folio',
            'nombre'     => 'Paciente',
            'creado'     => 'Creación',
            'modificado' => 'Última mod.',
            'creador'    => 'Creado por',
            'estado'     => 'Estado',
        ];
    @endphp

    <div class="min-h-screen bg-slate-50">
        @include('layouts.navigation')

        <main class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">

            <x-breadcrumbs />

            {{-- Encabezado --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">Expedientes clínicos</h2>
                    <p class="text-slate-600 mt-1">Consulta, edita o desactiva los expedientes.</p>
                </div>
                <a href="{{ route('expedientes.create') }}"
                   class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    + Nuevo expediente
                </a>
            </div>

            {{-- Mensaje de éxito --}}
            @if (session('success'))
                <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Filtros (se aplican al escribir) --}}
            <form id="filtros" method="GET" action="{{ route('expedientes.index') }}"
                  class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <input type="hidden" name="sort" value="{{ $sort }}">
                <input type="hidden" name="dir" value="{{ $dir }}">

                <input type="text" id="f-id" name="id" value="{{ request('id') }}" placeholder="Buscar por ID de paciente"
                       class="filtro w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <input type="text" id="f-folio" name="folio" value="{{ request('folio') }}" placeholder="Buscar por folio"
                       class="filtro w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <input type="text" id="f-nombre" name="nombre" value="{{ request('nombre') }}" placeholder="Buscar por nombre o apellidos"
                       class="filtro w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </form>

            {{-- Tabla --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        @foreach ($cabeceras as $key => $label)
                            @php
                                $activa = $sort === $key;
                                $siguiente = ($activa && $dir === 'asc') ? 'desc' : 'asc';
                            @endphp
                            <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => $key, 'dir' => $siguiente, 'page' => 1]) }}"
                                   class="inline-flex items-center gap-1 hover:text-blue-600">
                                    {{ $label }}
                                    <span class="text-xs {{ $activa ? 'text-blue-600' : 'text-slate-300' }}">
                                            {{ $activa ? ($dir === 'asc' ? '▲' : '▼') : '↕' }}
                                        </span>
                                </a>
                            </th>
                        @endforeach
                        <th class="px-4 py-3 text-right font-semibold">Acciones</th>
                    </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                    @forelse ($expedientes as $e)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 text-slate-700">{{ $e->idPaciente }}</td>
                            <td class="px-4 py-3 font-medium text-slate-900 whitespace-nowrap">{{ $e->folioExpediente }}</td>
                            <td class="px-4 py-3 text-slate-700">
                                {{ $e->paciente->nombres }} {{ $e->paciente->apellido1 }} {{ $e->paciente->apellido2 }}
                            </td>
                            <td class="px-4 py-3 text-slate-600 whitespace-nowrap">{{ $e->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 text-slate-600 whitespace-nowrap">{{ $e->updated_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 text-slate-600">
                                {{ $e->creador ? $e->creador->nombres . ' ' . $e->creador->apellido1 : '—' }}
                            </td>
                            <td class="px-4 py-3">
                                    <span class="inline-block px-2 py-0.5 rounded text-xs font-medium
                                        {{ $e->estado === 'activo' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                        {{ ucfirst($e->estado) }}
                                    </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end items-center gap-2">
                                    {{-- Botón Ver --}}
                                    <a href="{{ route('expedientes.show', $e) }}"
                                       class="px-3 py-1 text-xs font-medium text-slate-700 bg-slate-100 rounded hover:bg-slate-200 transition-colors">
                                        Ver
                                    </a>

                                    {{-- Botón Editar --}}
                                    <a href="{{ route('expedientes.edit', $e) }}"
                                       class="px-3 py-1 text-xs font-medium text-blue-700 bg-blue-50 rounded hover:bg-blue-100 transition-colors">
                                        Editar
                                    </a>

                                    @if ($e->estado === 'activo')
                                        <form method="POST" action="{{ route('expedientes.destroy', $e) }}"
                                              onsubmit="return confirm('¿Desactivar el expediente {{ $e->folioExpediente }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="px-3 py-1 text-xs font-medium text-red-700 bg-red-50 rounded hover:bg-red-100 transition-colors">
                                                Desactivar
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('expedientes.restore', $e) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="px-3 py-1 text-xs font-medium text-emerald-700 bg-emerald-50 rounded hover:bg-emerald-100 transition-colors">
                                                Reactivar
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-slate-500">
                                No se encontraron expedientes.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            <div class="mt-6">
                {{ $expedientes->links() }}
            </div>
        </main>
    </div>

    {{-- Filtrado al escribir: espera 400 ms y envía el formulario --}}
    <script>
        (function () {
            const form = document.getElementById('filtros');
            let timer;

            // Recuperar el foco en el campo donde se estaba escribiendo
            const ultimo = sessionStorage.getItem('expedientes-foco');
            if (ultimo) {
                const campo = document.getElementById(ultimo);
                if (campo) {
                    campo.focus();
                    campo.setSelectionRange(campo.value.length, campo.value.length);
                }
                sessionStorage.removeItem('expedientes-foco');
            }

            document.querySelectorAll('.filtro').forEach(function (input) {
                input.addEventListener('input', function () {
                    clearTimeout(timer);
                    timer = setTimeout(function () {
                        sessionStorage.setItem('expedientes-foco', input.id);
                        form.submit();
                    }, 400);
                });
            });
        })();
    </script>
@endsection
