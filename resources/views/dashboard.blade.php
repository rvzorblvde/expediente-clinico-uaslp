@extends('layouts.app')

@section('title', 'Dashboard - Unifarmacia')

@section('content')
    @php
        $esAdmin = auth()->user()->isResponsableSanitario();

        // 'admin' => true significa que solo lo ve el Responsable Sanitario
        $modulos = [
            [
                'nombre' => 'Expedientes',
                'desc'   => 'Gestionar historiales y notas médicas.',
                'ruta'   => 'expedientes.index',
                'admin'  => false,
                'icono'  => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
            ],
            [
                'nombre' => 'Visitas',
                'desc'   => 'Registrar y consultar visitas.',
                'ruta'   => 'visitas.index',
                'admin'  => false,
                'icono'  => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
            ],
            [
                'nombre' => 'Medicamentos',
                'desc'   => 'Inventario y catálogo de medicamentos.',
                'ruta'   => 'medicamentos.index',
                'admin'  => false,
                'icono'  => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z',
            ],
            [
                'nombre' => 'Farmacovigilancia',
                'desc'   => 'Reportes de reacciones adversas.',
                'ruta'   => 'farmacovigilancia.index',
                'admin'  => false,
                'icono'  => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
            ],
            [
                'nombre' => 'Usuarios',
                'desc'   => 'Administrar cuentas del sistema.',
                'ruta'   => 'usuarios.index',
                'admin'  => true,
                'icono'  => 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4zm6 0a4 4 0 10-4-4',
            ],
            [
                'nombre' => 'Estadísticas y reportes',
                'desc' => 'Consultar métricas y reportes del sistema.',
                'ruta' => 'reportes.index',
                'admin' => false,
'icono'  => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'            ],
            [
                'nombre' => 'Logs',
                'desc'   => 'Registro de auditoría del sistema.',
                'ruta'   => 'logs.index',
                'admin'  => true,
                'icono'  => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
            ],
        ];
    @endphp

    <div class="min-h-screen bg-slate-50">
        @include('layouts.navigation')

        <main class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">

            <x-breadcrumbs />

            <div class="mb-8">
                <h2 class="text-2xl font-bold text-slate-900">Panel Principal</h2>
                <p class="text-slate-600 mt-1">Selecciona el módulo al que deseas acceder.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-5xl mx-auto">
                @foreach ($modulos as $m)
                    @continue($m['admin'] && ! $esAdmin)

                    @php
                        $disponible = Route::has($m['ruta']);
                        // Verificamos si es el módulo de usuarios y hay solicitudes pendientes
                        $esModuloUsuarios = ($m['ruta'] === 'usuarios.index');
                        $tieneSolicitudes = $esModuloUsuarios && isset($solicitudesPendientes) && $solicitudesPendientes > 0;
                    @endphp

                    <a href="{{ $disponible ? route($m['ruta']) : '#' }}"
                       @unless ($disponible) aria-disabled="true" @endunless
                       class="block p-6 bg-white rounded-xl border shadow-sm transition-all group relative
                              {{ $tieneSolicitudes ? 'border-amber-400 ring-2 ring-amber-200 hover:shadow-md' : ($disponible ? 'hover:shadow-md hover:border-blue-400 border-slate-200' : 'opacity-60 cursor-not-allowed border-slate-200') }}">

                        {{-- Insignia de solicitudes pendientes --}}
                        @if ($tieneSolicitudes)
                            <span class="absolute top-4 right-4 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 animate-pulse">
                                {{ $solicitudesPendientes }} pendiente(s)
                            </span>
                        @endif

                        <div class="flex items-center space-x-4">
                            <div class="p-3 bg-blue-50 text-blue-600 rounded-lg transition-colors
                                        {{ $disponible ? 'group-hover:bg-blue-600 group-hover:text-white' : '' }}">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $m['icono'] }}"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-slate-900">{{ $m['nombre'] }}</h3>
                                <p class="text-sm text-slate-500 mt-1">{{ $m['desc'] }}</p>
                                @unless ($disponible)
                                    <span class="inline-block mt-2 text-xs font-medium text-amber-700 bg-amber-50 px-2 py-0.5 rounded">Próximamente</span>
                                @endunless
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </main>
    </div>
@endsection
