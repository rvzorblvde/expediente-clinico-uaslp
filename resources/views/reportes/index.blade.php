@extends('layouts.app')

@section('title', 'Estadísticas y Reportes - Unifarmacia')

@section('content')
    <div class="min-h-screen bg-slate-50">
        @include('layouts.navigation')

        <main class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8 space-y-6">

            <x-breadcrumbs />

            {{-- Encabezado --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">Estadísticas y reportes</h2>
                    <p class="text-slate-600 mt-1">Consulta métricas, indicadores clínicos y reportes del sistema.</p>
                </div>

                {{-- Botones de acción / exportación --}}
                <div class="flex items-center gap-2">
                    <button type="button" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-slate-300 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-50 transition-colors shadow-sm">
                        Exportar Excel
                    </button>
                    <button type="button" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                        Generar PDF
                    </button>
                </div>
            </div>

            {{-- Tarjetas de Métricas Clave (KPIs) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Usuarios del servicio</p>
                    <p class="text-3xl font-extrabold text-slate-900 mt-2">...</p>
                    <p class="text-xs text-slate-500 mt-1">Actualmente activos</p>
                </div>

                <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Usuarios nuevos</p>
                    <p class="text-3xl font-extrabold text-blue-600 mt-2">...</p>
                    <p class="text-xs text-slate-500 mt-1">En el periodo analizado</p>
                </div>

                <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Usuarios en pausa</p>
                    <p class="text-3xl font-extrabold text-amber-600 mt-2">...</p>
                    <p class="text-xs text-slate-500 mt-1">Temporalmente inactivos</p>
                </div>

                <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Variación</p>
                    <p class="text-3xl font-extrabold text-emerald-600 mt-2">%</p>
                    <p class="text-xs text-slate-500 mt-1">Respecto al periodo anterior</p>
                </div>
            </div>

            {{-- Área provisional para las gráficas --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <h3 class="text-base font-bold text-slate-900 mb-4">Problemas de salud más analizados</h3>
                    <div class="h-64 flex items-center justify-center bg-slate-50 rounded-lg border border-dashed border-slate-300 text-slate-400 text-sm">
                        [ Gráfico de Barras: Diagnósticos ]
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <h3 class="text-base font-bold text-slate-900 mb-4">Medicamentos con mayor seguimiento</h3>
                    <div class="h-64 flex items-center justify-center bg-slate-50 rounded-lg border border-dashed border-slate-300 text-slate-400 text-sm">
                        [ Gráfico de Barras: Medicamentos ]
                    </div>
                </div>
            </div>

        </main>
    </div>
@endsection
