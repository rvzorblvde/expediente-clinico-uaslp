@extends('layouts.app')

@section('title', 'Dashboard - Unifarmacia')

@section('content')
    <div class="min-h-screen bg-slate-50">
        @include('layouts.navigation')

        <main class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-slate-900">Panel Principal</h2>
                <p class="text-slate-600 mt-1">Selecciona el módulo al que deseas acceder.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                    <p class="text-sm text-slate-500">Expedientes activos</p>
                    <p class="text-3xl font-bold text-slate-900 mt-1">{{ $expedientesActivos }}</p>
                </div>
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                    <p class="text-sm text-slate-500">Expedientes inactivos</p>
                    <p class="text-3xl font-bold text-slate-900 mt-1">{{ $expedientesInactivos }}</p>
                </div>
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                    <p class="text-sm text-slate-500">Usuarios del sistema</p>
                    <p class="text-3xl font-bold text-slate-900 mt-1">{{ $totalUsuarios }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <a href="{{ route('expedientes.index') }}"
                   class="block p-6 bg-white rounded-xl border border-slate-200 shadow-sm hover:shadow-md hover:border-blue-400 transition-all group">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-blue-50 text-blue-600 rounded-lg group-hover:bg-blue-600 group-hover:text-white transition-colors">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900 group-hover:text-blue-700 transition-colors">Expedientes Clínicos</h3>
                            <p class="text-sm text-slate-500 mt-1">Gestionar historiales y notas médicas.</p>
                        </div>
                    </div>
                </a>

                @if (auth()->user()->isResponsableSanitario())
                    <a href="{{ route('usuarios.index') }}"
                       class="block p-6 bg-white rounded-xl border border-slate-200 shadow-sm hover:shadow-md hover:border-emerald-400 transition-all group">
                        <div class="flex items-center space-x-4">
                            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-lg group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4zm6 0a4 4 0 10-4-4"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-slate-900 group-hover:text-emerald-700 transition-colors">Usuarios</h3>
                                <p class="text-sm text-slate-500 mt-1">Administrar cuentas del sistema.</p>
                            </div>
                        </div>
                    </a>
                @endif
            </div>
        </main>
    </div>
@endsection
