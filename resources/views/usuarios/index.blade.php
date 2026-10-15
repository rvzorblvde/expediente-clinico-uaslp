@extends('layouts.app')

@section('title', 'Gestión de Usuarios - Unifarmacia')

@section('content')
    <div class="min-h-screen bg-slate-50">
        @include('layouts.navigation')

        <main class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <x-breadcrumbs />

            <div class="flex justify-between items-center mb-8">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">Gestión de Usuarios</h2>
                    <p class="text-slate-600 mt-1">Administra los usuarios del sistema y sus niveles de acceso.</p>
                </div>
                <a href="{{ route('usuarios.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg shadow-sm transition-all">
                    + Nuevo Usuario
                </a>
            </div>

            @if (session('success'))
                <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm rounded-xl border border-slate-200 overflow-hidden">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Correo</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Rol</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($users as $usuario)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">
                                    {{ $usuario->nombres }} {{ $usuario->apellido1 }} {{ $usuario->apellido2 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                    {{ $usuario->correo }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-50 text-blue-700">
                                        {{ $usuario->role->nombreRol ?? 'Sin rol' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $usuario->estado === 'activo' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                        {{ ucfirst($usuario->estado) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <a href="{{ route('usuarios.edit', $usuario->idUsuario) }}" class="text-blue-600 hover:text-blue-900">Editar</a>
                                    
                                    <form action="{{ route('usuarios.destroy', $usuario->idUsuario) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-{{ $usuario->estado === 'activo' ? 'amber' : 'emerald' }}-600 hover:opacity-75">
                                            {{ $usuario->estado === 'activo' ? 'Desactivar' : 'Activar' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-sm text-slate-500">
                                    No hay usuarios registrados en el sistema.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </main>
    </div>
@endsection