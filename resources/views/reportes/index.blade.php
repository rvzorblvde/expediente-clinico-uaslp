@extends('layouts.app')

@section('title', 'Estadísticas y Reportes - Unifarmacia')

@section('content')
    <div class="min-h-screen bg-slate-50">
        @include('layouts.navigation')

        <main class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8 space-y-6">

            <x-breadcrumbs />

            {{-- Encabezado --}}
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">Estadísticas y reportes</h2>
                    <p class="text-slate-600 mt-1">Consulta métricas, indicadores clínicos y reportes del sistema.</p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    {{-- Filtro por Mes y Año --}}
                    @php
                        $ahora = now('America/Mexico_City');
                        $anioSeleccionado = (int) request('anio', $ahora->year);
                        $mesMax = ($anioSeleccionado >= $ahora->year) ? $ahora->month : 12;
                    @endphp

                    <form method="GET" action="{{ route('reportes.index') }}" class="flex items-center gap-2">
                        <select name="mes" class="rounded-lg border-slate-300 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @foreach(range(1, $mesMax) as $m)
                                <option value="{{ $m }}" {{ (request('mes', $ahora->month) == $m) ? 'selected' : '' }}>
                                    {{ ucfirst(\Carbon\Carbon::create()->month($m)->locale('es')->translatedFormat('F')) }}
                                </option>
                            @endforeach
                        </select>
                        <select name="anio" onchange="this.form.submit()" class="rounded-lg border-slate-300 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @foreach(range($ahora->year - 2, $ahora->year) as $y)
                                <option value="{{ $y }}" {{ (request('anio', $ahora->year) == $y) ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endforeach
                        </select>

                        <button type="submit"
                                class="px-3 py-2 bg-slate-800 text-white text-sm font-medium rounded-lg hover:bg-slate-700 transition-colors shadow-sm">
                            Filtrar
                        </button>
                    </form>

                    {{-- Botones de acción / exportación --}}
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="exportarExcelConGraficas()"
                                class="inline-flex items-center justify-center px-4 py-2 bg-white border border-slate-300 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-50 transition-colors shadow-sm">
                            Exportar Excel
                        </button>
                        <button type="button" onclick="generarPdfConGraficas('oficial')"
                                class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                            PDF Oficial
                        </button>
                        <button type="button" onclick="generarPdfConGraficas('practica')"
                                class="inline-flex items-center justify-center px-4 py-2 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700 transition-colors shadow-sm">
                            PDF Prácticas
                        </button>
                    </div>
                </div>

                {{-- Formulario oculto para enviar las gráficas y el periodo al PDF/Excel --}}
                <form id="formPdf" method="POST" action="{{ route('reportes.pdf') }}" target="_blank" class="hidden">
                    @csrf
                    <input type="hidden" name="formato" id="pdf_formato">
                    <input type="hidden" name="mes" value="{{ request('mes', now('America/Mexico_City')->month) }}">
                    <input type="hidden" name="anio" value="{{ request('anio', now('America/Mexico_City')->year) }}">
                    <input type="hidden" name="img_dona" id="pdf_img_dona">
                    <input type="hidden" name="img_nuevos" id="pdf_img_nuevos">
                    <input type="hidden" name="img_variacion" id="pdf_img_variacion">
                    <input type="hidden" name="img_problemas" id="pdf_img_problemas">
                    <input type="hidden" name="img_medicamentos" id="pdf_img_medicamentos">
                </form>
            </div>

            {{-- Tarjetas de Métricas Clave (KPIs) --}}

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Usuarios de Servicio -->
                <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Usuarios del servicio</p>
                    <p class="text-3xl font-extrabold text-slate-900 mt-2">{{ $usuariosServicio }}</p>
                    <p class="text-xs text-slate-500 mt-1">Actualmente activos</p>
                </div>

                <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Usuarios nuevos</p>
                    <p class="text-3xl font-extrabold text-blue-600 mt-2">{{ $usuariosNuevos }}</p>
                    <p class="text-xs text-slate-500 mt-1 capitalize">{{ $etiquetaPeriodo }}</p>
                </div>

                <!--Usuarios en pausa -->
                <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Usuarios en pausa</p>
                    <p class="text-3xl font-extrabold text-amber-600 mt-2">{{ $usuariosPausa }}</p>
                    <p class="text-xs text-slate-500 mt-1">Temporalmente inactivos</p>
                </div>

                <!-- Varaicion -->
                <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Variación</p>
                    <p class="text-3xl font-extrabold {{ $colorVariacion }} mt-2">{{ $variacion }}</p>
                    <p class="text-xs text-slate-500 mt-1">Respecto al periodo anterior</p>
                </div>
            </div>

            {{-- Sección de Gráficas Conectadas (Dona y Línea) --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- 1. Gráfica de Dona: Estado actual de los usuarios --}}
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
                    <h3 class="text-base font-bold text-slate-900 mb-4">Estado actual de los usuarios</h3>

                    <div class="relative flex items-center justify-center my-2">
                        <div class="w-48 h-48">
                            <canvas id="chartDona"></canvas>
                        </div>
                        {{-- Total en el centro de la dona --}}
                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                            <span class="text-xs text-slate-400 font-medium">Total</span>
                            <span class="text-2xl font-extrabold text-slate-800">{{ $totalUsuarios }}</span>
                            <span class="text-xs text-slate-400 font-medium">usuarios</span>
                        </div>
                    </div>

                    {{-- Leyenda con porcentajes reales --}}
                    <div class="flex justify-around items-center pt-4 border-t border-slate-100 text-xs font-medium text-slate-600">
                        <div class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded-sm bg-emerald-500 inline-block"></span>
                            <span>Activos: <strong>{{ $usuariosServicio }}</strong> ({{ $pctActivos }}%)</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded-sm bg-amber-500 inline-block"></span>
                            <span>En pausa: <strong>{{ $usuariosPausa }}</strong> ({{ $pctPausa }}%)</span>
                        </div>
                    </div>
                </div>

                {{-- 2. Gráfica de Línea: Usuarios nuevos por periodo --}}
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm lg:col-span-2">
                    <h3 class="text-base font-bold text-slate-900 mb-4">Usuarios nuevos por periodo</h3>
                    <div class="relative h-64">
                        <canvas id="chartNuevos"></canvas>
                    </div>
                </div>

            </div>

            {{-- Sección: Variación de usuarios (Activos vs En pausa por periodo) --}}
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm mt-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-center">

                    {{-- Gráfica de 2 líneas --}}
                    <div class="lg:col-span-2">
                        <h3 class="text-base font-bold text-slate-900 mb-4">Variación de usuarios</h3>
                        <div class="relative h-64">
                            <canvas id="chartVariacionUsuarios"></canvas>
                        </div>
                    </div>

                    {{-- Resumen porcentual lateral --}}
                    <div class="flex flex-col items-center justify-center text-center p-6 bg-slate-50 rounded-xl border border-slate-100">
                        <p class="text-sm font-bold text-slate-700">Variación de usuarios</p>
                        <p class="text-5xl font-extrabold {{ $colorVariacion }} my-3">{{ $variacion }}</p>
                        <p class="text-xs text-slate-500">Respecto al periodo anterior</p>
                    </div>

                </div>
            </div>

            {{-- Sección: Problemas de salud analizados y Medicamentos --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">

                {{-- Gráfica 4: Problemas de salud analizados --}}
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <h3 class="text-base font-bold text-slate-900 mb-4">Problemas de salud analizados</h3>
                    <div class="relative h-64">
                        <canvas id="chartProblemasSalud"></canvas>
                    </div>
                </div>

                {{-- Gráfica 5: Medicamentos analizados --}}
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <h3 class="text-base font-bold text-slate-900 mb-4">Medicamentos analizados</h3>
                    <div class="relative h-64">
                        <canvas id="chartMedicamentos"></canvas>
                    </div>
                </div>

            </div>

            {{-- Scripts de Chart.js --}}
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    // 1. Gráfica de Dona
                    const ctxDona = document.getElementById('chartDona').getContext('2d');
                    new Chart(ctxDona, {
                        type: 'doughnut',
                        data: {
                            labels: ['Activos', 'En pausa'],
                            datasets: [{
                                data: [{{ $usuariosServicio }}, {{ $usuariosPausa }}],
                                backgroundColor: ['#10B981', '#F59E0B'],
                                borderWidth: 2,
                                borderColor: '#ffffff',
                                cutout: '70%'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return ` ${context.label}: ${context.raw} usuarios`;
                                        }
                                    }
                                }
                            }
                        }
                    });

                    // 2. Gráfica de Línea (Usuarios nuevos por mes)
                    const ctxNuevos = document.getElementById('chartNuevos').getContext('2d');
                    new Chart(ctxNuevos, {
                        type: 'line',
                        data: {
                            labels: {!! json_encode($labelsMeses) !!},
                            datasets: [{
                                label: 'Usuarios nuevos',
                                data: {!! json_encode($datosNuevosPorMes) !!},
                                borderColor: '#3B82F6',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                pointBackgroundColor: '#3B82F6',
                                pointBorderColor: '#ffffff',
                                pointBorderWidth: 2,
                                pointRadius: 5,
                                borderWidth: 2.5,
                                fill: true,
                                tension: 0.35
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: { precision: 0, color: '#64748B' },
                                    grid: { color: '#F1F5F9' }
                                },
                                x: {
                                    ticks: { color: '#64748B' },
                                    grid: { display: false }
                                }
                            }
                        }
                    });

                    // 3. Gráfica de Variación de usuarios (Activos vs En pausa)
                    const ctxVar = document.getElementById('chartVariacionUsuarios').getContext('2d');
                    new Chart(ctxVar, {
                        type: 'line',
                        data: {
                            labels: {!! json_encode($labelsMeses) !!},
                            datasets: [
                                {
                                    label: 'Activos',
                                    data: {!! json_encode($datosActivosPorMes) !!},
                                    borderColor: '#16A34A',
                                    backgroundColor: '#16A34A',
                                    pointRadius: 4,
                                    borderWidth: 2.5,
                                    tension: 0.3
                                },
                                {
                                    label: 'En pausa',
                                    data: {!! json_encode($datosPausaPorMes) !!},
                                    borderColor: '#3B82F6',
                                    backgroundColor: '#3B82F6',
                                    pointRadius: 4,
                                    borderWidth: 2.5,
                                    tension: 0.3
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'bottom',
                                    labels: { usePointStyle: true, boxWidth: 8 }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: { precision: 0, color: '#64748B' },
                                    grid: { color: '#F1F5F9' }
                                },
                                x: {
                                    ticks: { color: '#64748B' },
                                    grid: { display: false }
                                }
                            }
                        }
                    });
                    // 4. Gráfica de Barras Horizontales: Problemas de salud analizados
                    const ctxProblemas = document.getElementById('chartProblemasSalud').getContext('2d');
                    new Chart(ctxProblemas, {
                        type: 'bar',
                        data: {
                            labels: {!! json_encode($problemasLabels) !!},
                            datasets: [{
                                label: 'Número de casos',
                                data: {!! json_encode($problemasData) !!},
                                backgroundColor: '#60A5FA',
                                borderRadius: 4,
                                barPercentage: 0.65
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    ticks: { precision: 0, color: '#64748B' },
                                    grid: { color: '#F1F5F9' }
                                },
                                y: {
                                    ticks: { color: '#334155', font: { size: 12 } },
                                    grid: { display: false }
                                }
                            }
                        }
                    });
                    // 5. Gráfica de Barras Horizontales: Medicamentos analizados (Principio Activo)
                    const ctxMedicamentos = document.getElementById('chartMedicamentos').getContext('2d');
                    new Chart(ctxMedicamentos, {
                        type: 'bar',
                        data: {
                            labels: {!! json_encode($medicamentosLabels) !!},
                            datasets: [{
                                label: 'Número de casos',
                                data: {!! json_encode($medicamentosData) !!},
                                backgroundColor: '#60A5FA',
                                borderRadius: 4,
                                barPercentage: 0.65
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    ticks: { precision: 0, color: '#64748B' },
                                    grid: { color: '#F1F5F9' }
                                },
                                y: {
                                    ticks: { color: '#334155', font: { size: 12 } },
                                    grid: { display: false }
                                }
                            }
                        }
                    });
                });
            </script>
            <script>
                function generarPdfConGraficas(formato) {
                    document.getElementById('pdf_formato').value = formato;
                    document.getElementById('pdf_img_dona').value = document.getElementById('chartDona').toDataURL('image/png');
                    document.getElementById('pdf_img_nuevos').value = document.getElementById('chartNuevos').toDataURL('image/png');
                    document.getElementById('pdf_img_variacion').value = document.getElementById('chartVariacionUsuarios').toDataURL('image/png');
                    document.getElementById('pdf_img_problemas').value = document.getElementById('chartProblemasSalud').toDataURL('image/png');
                    document.getElementById('pdf_img_medicamentos').value = document.getElementById('chartMedicamentos').toDataURL('image/png');
                    document.getElementById('formPdf').submit();
                }

                function exportarExcelConGraficas() {
                    const form = document.getElementById('formPdf');
                    const actionOriginal = form.action;
                    const targetOriginal = form.target;

                    document.getElementById('pdf_img_dona').value = document.getElementById('chartDona').toDataURL('image/png');
                    document.getElementById('pdf_img_nuevos').value = document.getElementById('chartNuevos').toDataURL('image/png');
                    document.getElementById('pdf_img_variacion').value = document.getElementById('chartVariacionUsuarios').toDataURL('image/png');
                    document.getElementById('pdf_img_problemas').value = document.getElementById('chartProblemasSalud').toDataURL('image/png');
                    document.getElementById('pdf_img_medicamentos').value = document.getElementById('chartMedicamentos').toDataURL('image/png');

                    form.action = "{{ route('reportes.excel') }}";
                    form.target = "_self";
                    form.submit();

                    form.action = actionOriginal;
                    form.target = targetOriginal;
                }
            </script>
        </main>
    </div>
@endsection
