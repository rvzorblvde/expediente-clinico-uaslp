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

            {{-- Tabla de Logs --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Fecha y Hora</th>
                            <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Usuario</th>
                            <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Acción</th>
                            <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Tabla Afectada</th>
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
                                    No hay registros de auditoría disponibles.
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

            {{-- Contenedor con scroll para los detalles --}}
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

            // Obtenemos el JSON almacenado en el atributo data
            const rawDatos = button.getAttribute('data-detalles');

            let objetoDatos = rawDatos;
            try {
                // Parseamos el string JSON a un objeto/array de JS
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