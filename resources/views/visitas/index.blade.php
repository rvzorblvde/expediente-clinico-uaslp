@extends('layouts.app')

@section('title', 'Historial de Visitas - Unifarmacia')

@section('content')
    <div class="min-h-screen bg-slate-50">
        @include('layouts.navigation')

        <main class="max-w-6xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">Control de Visitas y Asesorías</h2>
                    <p class="text-slate-600 text-sm mt-1">Historial de atenciones brindadas a los pacientes.</p>
                </div>
                <a href="{{ route('visitas.create') }}" 
                   class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    + Registrar Nueva Visita
                </a>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <table class="w-full text-left border-collapse text-sm">
                    <thead class="bg-slate-100 text-slate-700 border-b border-slate-200">
                        <tr>
                            <th class="p-3 font-semibold">Expediente</th>
                            <th class="p-3 font-semibold">Fecha y Hora</th>
                            <th class="p-3 font-semibold">Motivo</th>
                            <th class="p-3 font-semibold">Estado</th>
                            <th class="p-3 font-semibold text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($visitas as $visita)
                            <tr class="hover:bg-slate-50">
                                <td class="p-3 text-slate-900 font-medium">#{{ $visita->folioExpediente }}</td>
                                <td class="p-3 text-slate-600">{{ \Carbon\Carbon::parse($visita->fecha)->format('d/m/Y H:i') }}</td>
                                <td class="p-3 text-slate-600">{{ $visita->motivo }}</td>
                                <td class="p-3">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full 
                                        {{ $visita->estado == 'Realizada' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $visita->estado == 'Agendada' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $visita->estado == 'Reagendada' ? 'bg-amber-100 text-amber-800' : '' }}
                                        {{ $visita->estado == 'Cancelada' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ $visita->estado }}
                                    </span>
                                </td>
                                <td class="p-3 text-right space-x-2">
                                    <a href="{{ route('visitas.show', $visita->idVisita) }}" class="text-blue-600 hover:underline">Ver</a>
                                    <a href="{{ route('visitas.edit', $visita->idVisita) }}" class="text-slate-600 hover:underline">Editar</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-6 text-center text-slate-500">No hay visitas registradas en el sistema.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4 border-t border-slate-200">
                    {{ $visitas->links() }}
                </div>
            </div>
        </main>
    </div>
@endsection