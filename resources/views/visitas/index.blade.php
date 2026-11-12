@extends('layouts.app')

@section('title', 'Visitas - Unifarmacia')

@section('content')
    @php
        $badges = [
            'completada' => 'bg-green-100 text-green-800',
            'agendada'   => 'bg-blue-100 text-blue-800',
            'cancelada'  => 'bg-red-100 text-red-800',
            'no_asistio' => 'bg-amber-100 text-amber-800',
        ];
    @endphp

    <div class="min-h-screen bg-slate-50">
        @include('layouts.navigation')

        <main class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">Visitas y citas</h2>
                    <p class="text-slate-600 mt-1">Historial de atenciones y agenda de citas.</p>
                </div>
                <a href="{{ route('visitas.create') }}"
                   class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    + Nueva visita / cita
                </a>
            </div>

            @if (session('success'))
                <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm">{{ session('success') }}</div>
            @endif

            {{-- Pestañas Historial / Agenda --}}
            <div class="flex gap-2 mb-4">
                <a href="{{ route('visitas.index') }}"
                   class="px-4 py-2 text-sm font-medium rounded-lg {{ $vista === 'historial' ? 'bg-slate-800 text-white' : 'bg-white border border-slate-300 text-slate-700 hover:bg-slate-100' }}">
                    Historial
                </a>
                <a href="{{ route('visitas.index', ['vista' => 'agenda']) }}"
                   class="px-4 py-2 text-sm font-medium rounded-lg {{ $vista === 'agenda' ? 'bg-slate-800 text-white' : 'bg-white border border-slate-300 text-slate-700 hover:bg-slate-100' }}">
                    Agenda (citas próximas)
                </a>
            </div>

            {{-- Filtros (RF-05) --}}
            <form method="GET" action="{{ route('visitas.index') }}" class="mb-4 grid grid-cols-2 sm:grid-cols-6 gap-3">
                <input type="hidden" name="vista" value="{{ $vista }}">
                <input type="search" name="q" value="{{ $q }}" maxlength="100"
                       placeholder="Paciente, CURP o folio"
                       class="col-span-2 px-3 py-2 rounded-lg border border-slate-300 text-sm">
                @if ($vista === 'historial')
                    <select name="estado" class="px-3 py-2 rounded-lg border border-slate-300 text-sm bg-white">
                        <option value="">Todos los estados</option>
                        @foreach (\App\Models\Visita::ESTADOS as $valor => $etiqueta)
                            <option value="{{ $valor }}" @selected(request('estado') === $valor)>{{ $etiqueta }}</option>
                        @endforeach
                    </select>
                @endif
                <select name="tipo" class="px-3 py-2 rounded-lg border border-slate-300 text-sm bg-white">
                    <option value="">Todos los tipos</option>
                    @foreach (\App\Models\Visita::TIPOS as $valor => $etiqueta)
                        <option value="{{ $valor }}" @selected(request('tipo') === $valor)>{{ $etiqueta }}</option>
                    @endforeach
                </select>
                <input type="date" name="desde" value="{{ request('desde') }}" class="px-3 py-2 rounded-lg border border-slate-300 text-sm" aria-label="Desde">
                <div class="flex gap-2">
                    <input type="date" name="hasta" value="{{ request('hasta') }}" class="flex-1 px-3 py-2 rounded-lg border border-slate-300 text-sm" aria-label="Hasta">
                    <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded-lg hover:bg-slate-900">Filtrar</button>
                </div>
            </form>

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Folio</th>
                        <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Paciente</th>
                        <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Fecha y hora</th>
                        <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Tipo</th>
                        <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Estado</th>
                        <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Atendió</th>
                        <th class="px-4 py-3 text-right font-semibold">Acciones</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    @forelse ($visitas as $visita)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $visita->folioVisita }}</td>
                            <td class="px-4 py-3 text-slate-700">
                                {{ $visita->nombre_paciente }}
                                @unless ($visita->folioExpediente)
                                    <span class="ml-1 text-xs text-slate-400">(sin expediente)</span>
                                @endunless
                            </td>
                            <td class="px-4 py-3 text-slate-600 whitespace-nowrap">{{ $visita->fecha->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ \App\Models\Visita::TIPOS[$visita->tipo] ?? $visita->tipo }}</td>
                            <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $badges[$visita->estado] ?? 'bg-slate-100 text-slate-600' }}">
                                        {{ \App\Models\Visita::ESTADOS[$visita->estado] ?? $visita->estado }}
                                    </span>
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $visita->usuario?->nombres }} {{ $visita->usuario?->apellido1 }}</td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <a href="{{ route('visitas.show', $visita) }}" class="text-blue-600 hover:underline mr-3">Ver</a>
                                <a href="{{ route('visitas.edit', $visita) }}" class="text-slate-600 hover:underline">Editar</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-8 text-center text-slate-500">
                                {{ $vista === 'agenda' ? 'No hay citas agendadas próximas.' : 'No hay visitas que coincidan.' }}
                            </td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $visitas->links() }}</div>
        </main>
    </div>
@endsection
