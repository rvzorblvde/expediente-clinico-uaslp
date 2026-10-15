@extends('layouts.app')

@section('title', 'Gestión de Usuarios - Unifarmacia')

@section('content')
    <div class="min-h-screen bg-slate-50">
        @include('layouts.navigation')

        <main class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">

            <x-breadcrumbs />

            {{-- Encabezado --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">Gestión de Usuarios</h2>
                    <p class="text-slate-600 mt-1">Administra los cuentas del sistema y sus niveles de acceso.</p>
                </div>
                <a href="{{ route('usuarios.create') }}"
                   class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    + Nuevo usuario
                </a>
            </div>

            {{-- Mensaje de éxito --}}
            @if (session('success'))
                <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Tabla --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Nombre completo</th>
                        <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Correo electrónico</th>
                        <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Rol</th>
                        <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Estado</th>
                        <th class="px-4 py-3 text-right font-semibold">Acciones</th>
                    </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                    @forelse ($users as $usuario)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-medium text-slate-900 whitespace-nowrap">
                                {{ $usuario->nombres }} {{ $usuario->apellido1 }} {{ $usuario->apellido2 }}
                            </td>
                            <td class="px-4 py-3 text-slate-700">
                                {{ $usuario->correo }}
                            </td>
                            <td class="px-4 py-3 text-slate-700">
                                <span class="inline-block px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700">
                                    {{ $usuario->role->nombreRol ?? 'Sin rol' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-block px-2 py-0.5 rounded text-xs font-medium
                                    {{ $usuario->estado === 'activo' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ ucfirst($usuario->estado) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end items-center gap-2">
                                    {{-- Botón Editar --}}
                                    <a href="{{ route('usuarios.edit', $usuario->idUsuario) }}"
                                       class="px-3 py-1 text-xs font-medium text-blue-700 bg-blue-50 rounded hover:bg-blue-100 transition-colors">
                                        Editar
                                    </a>

                                    {{-- Botón Desactivar / Activar --}}
                                    <form method="POST" action="{{ route('usuarios.destroy', $usuario->idUsuario) }}"
                                          onsubmit="return confirm('¿Cambiar estado del usuario {{ $usuario->nombres }}?')">
                                        @csrf
                                        @method('DELETE')
                                        @if ($usuario->estado === 'activo')
                                            <button type="submit"
                                                    class="px-3 py-1 text-xs font-medium text-red-700 bg-red-50 rounded hover:bg-red-100 transition-colors">
                                                Desactivar
                                            </button>
                                        @else
                                            <button type="submit"
                                                    class="px-3 py-1 text-xs font-medium text-emerald-700 bg-emerald-50 rounded hover:bg-emerald-100 transition-colors">
                                                Activar
                                            </button>
                                        @endif
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-slate-500">
                                No hay usuarios registrados en el sistema.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            <div class="mt-6">
                {{ $users->links() }}
            </div>
        </main>
    </div>
@endsection