@extends('layouts.app')

@section('title', 'Expedientes')

@section('content')
    <div class="min-h-screen bg-slate-50">
        @include('layouts.navigation')

        <div class="max-w-7xl mx-auto px-4 py-8">

            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <a href="{{ route('dashboard') }}"
                       class="inline-flex items-center justify-center h-9 w-9 rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-100 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <h1 class="text-2xl font-semibold text-slate-800">Expedientes</h1>
                </div>
                <a href="{{ route('expedientes.create') }}"
                   class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Nuevo expediente
                </a>
            </div>

            @if (session('success'))
                <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Nombre completo</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">CURP</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Sexo</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Edad</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">IMC</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Creado</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Modificado</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Estado</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    @forelse ($expedientes as $expediente)
                        <tr class="hover:bg-slate-50 {{ $expediente->trashed() ? 'opacity-50' : '' }}">
                            <td class="px-4 py-3 text-sm text-slate-400 font-mono">#{{ $expediente->id }}</td>
                            <td class="px-4 py-3 text-sm text-slate-700">
                                <a href="{{ route('expedientes.show', $expediente) }}" class="hover:text-indigo-600 font-medium">
                                    {{ $expediente->nombre_completo }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-500 font-mono">{{ $expediente->curp }}</td>
                            <td class="px-4 py-3 text-sm text-slate-500 capitalize">{{ $expediente->sexo }}</td>
                            <td class="px-4 py-3 text-sm text-slate-500">
                                {{ $expediente->fecha_nacimiento->age }} años
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-500">{{ $expediente->imc ?? '—' }}</td>
                            <td class="px-4 py-3 text-sm text-slate-500">
                                {{ $expediente->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-500">
                                {{ $expediente->updated_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if ($expediente->trashed())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                        Inactivo
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                        Activo
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right text-sm space-x-2 whitespace-nowrap">
                                @if ($expediente->trashed())
                                    <form action="{{ route('expedientes.restore', $expediente->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-indigo-600 hover:text-indigo-800 font-medium">
                                            Reactivar
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('expedientes.edit', $expediente) }}" class="text-slate-500 hover:text-slate-700 font-medium">
                                        Editar
                                    </a>
                                    <form action="{{ route('expedientes.destroy', $expediente) }}" method="POST" class="inline"
                                          onsubmit="return confirm('¿Desactivar este expediente?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 font-medium">
                                            Desactivar
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-8 text-center text-sm text-slate-400">
                                No hay expedientes registrados.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $expedientes->links() }}
            </div>

        </div>
    </div>
@endsection
