@extends('layouts.app')

@section('title', 'Auditoría y Logs - Unifarmacia')

@section('content')
    <div class="min-h-screen bg-slate-50">
        @include('layouts.navigation')

        <main class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">

            <x-breadcrumbs />

            {{-- Encabezado --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">Registro de Auditoría (Logs)</h2>
                    <p class="text-slate-600 mt-1">Trazabilidad completa de las acciones realizadas en el sistema.</p>
                </div>
            </div>

            {{-- Barra de Filtros --}}
            <form method="GET" action="{{ route('auditorias.index') }}"
                class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 mb-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    {{-- Usuario --}}
                    <div>
                        <label for="usuario" class="block text-xs font-semibold text-slate-600 mb-1">Usuario</label>
                        <input type="text" name="usuario" id="usuario" value="{{ request('usuario') }}"
                            placeholder="Nombre o correo..."
                            class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    {{-- Acción --}}
                    <div>
                        <label for="accion" class="block text-xs font-semibold text-slate-600 mb-1">Acción</label>
                        <select name="accion" id="accion"
                            class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Todas las acciones</option>
                            @foreach ($acciones as $accion)
                                <option value="{{ $accion }}" @selected(request('accion') === $accion)>
                                    {{ $accion }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Módulo --}}
                    <div>
                        <label for="modulo" class="block text-xs font-semibold text-slate-600 mb-1">Módulo</label>
                        <select name="modulo" id="modulo"
                            class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Todos los módulos</option>
                            @foreach ($modulos as $modulo)
                                <option value="{{ $modulo }}" @selected(request('modulo') === $modulo)>
                                    {{ ucfirst($modulo) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Fecha Desde --}}
                    <div>
                        <label for="desde" class="block text-xs font-semibold text-slate-600 mb-1">Desde</label>
                        <input type="date" name="desde" id="desde" value="{{ request('desde') }}"
                            class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    {{-- Fecha Hasta --}}
                    <div>
                        <label for="hasta" class="block text-xs font-semibold text-slate-600 mb-1">Hasta</label>
                        <input type="date" name="hasta" id="hasta" value="{{ request('hasta') }}"
                            class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-end gap-2">
                    @if (request()->anyFilled(['usuario', 'accion', 'modulo', 'desde', 'hasta']))
                        <a href="{{ route('auditorias.index') }}"
                            class="px-4 py-2 text-xs font-medium text-slate-600 bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">
                            Limpiar filtros
                        </a>
                    @endif
                    <button type="submit"
                        class="px-4 py-2 text-xs font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                        Filtrar
                    </button>
                </div>
            </form>

            {{-- Tabla de Logs --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Fecha y Hora</th>
                            <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Usuario</th>
                            <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Acción</th>
                            <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Módulo</th>
                            <th class="px-4 py-3 text-right font-semibold">Detalles (JSON)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($auditorias as $log)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 text-slate-600 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($log->fechaAccion)->format('d/m/Y H:i:s') }}
                                </td>
                                <td class="px-4 py-3 font-medium text-slate-900">
                                    {{ $log->usuario ? $log->usuario->nombres . ' ' . $log->usuario->apellido1 : 'Sistema / Desconocido' }}
                                </td>
                                <td class="px-4 py-3 text-slate-700">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                        {{ $log->accion }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-600 font-mono text-xs">
                                    {{ $log->tablaAfectada }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <button type="button" data-detalles='@json($log->detalles)' onclick="mostrarDetalles(this)"
                                        class="px-3 py-1 text-xs font-medium text-blue-700 bg-blue-50 rounded hover:bg-blue-100 transition-colors">
                                        Ver detalles
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-slate-500">
                                    No se encontraron registros de auditoría con los filtros seleccionados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            <div class="mt-6">
                {{ $auditorias->links() }}
            </div>
        </main>
    </div>

    {{-- Modal para inspeccionar los detalles --}}
    <div id="modal-json" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm hidden items-center justify-center p-4 z-50">
        <div class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl border border-slate-100">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-slate-900">Detalles de la Acción</h3>
                <button onclick="cerrarModal()"
                    class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
            </div>

            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 max-h-96 overflow-y-auto">
                <pre id="contenido-json" class="text-slate-700 text-xs font-mono whitespace-pre-wrap"></pre>
            </div>

            <div class="mt-6 flex justify-end">
                <button onclick="cerrarModal()"
                    class="px-4 py-2 bg-slate-100 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-200 transition-colors">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    <script>
        function mostrarDetalles(button) {
            const modal = document.getElementById('modal-json');
            const contenedor = document.getElementById('contenido-json');
            const rawDatos = button.getAttribute('data-detalles');

            let objetoDatos = rawDatos;
            try {
                objetoDatos = JSON.parse(rawDatos);
            } catch (e) {
                objetoDatos = { informacion: rawDatos };
            }

            contenedor.textContent = JSON.stringify(objetoDatos, null, 2);
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function cerrarModal() {
            const modal = document.getElementById('modal-json');
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
    </script>
@endsection