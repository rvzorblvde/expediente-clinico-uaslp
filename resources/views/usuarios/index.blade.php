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
                    <p class="text-slate-600 mt-1">Administra las cuentas del sistema y sus niveles de acceso.</p>
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
                        <tr class="hover:bg-slate-50 {{ $usuario->reset_requested ? 'bg-amber-50/50' : '' }}">
                            <td class="px-4 py-3 font-medium text-slate-900 whitespace-nowrap">
                                {{ $usuario->nombres }} {{ $usuario->apellido1 }} {{ $usuario->apellido2 }}
                                @if ($usuario->reset_requested)
                                    <span class="block text-xs font-semibold text-amber-600 mt-0.5">
                                        Solicitó restablecer contraseña
                                    </span>
                                @endif
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
                                    @if ($usuario->reset_requested)
                                        <button type="button" onclick="openResetModal('{{ $usuario->idUsuario }}', '{{$usuario->nombres }}')"
                                                class="px-3 py-1 text-xs font-medium text-amber-800 bg-amber-100 rounded hover:bg-amber-200 transition-colors">
                                            Asignar contraseña
                                        </button>
                                    @endif

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

    {{-- Modal simple para asignar contraseña temporal --}}
    <div id="resetModal" class="fixed inset-0 bg-slate-900/50 hidden items-center justify-center z-50 px-4">
        <div class="bg-white rounded-xl shadow-lg max-w-md w-full p-6">
            <h3 class="text-lg font-bold text-slate-900 mb-2">Restablecer contraseña para <span id="modalUserName"></span></h3>
            <p class="text-sm text-slate-600 mb-4">Ingresa la nueva contraseña temporal que se le asignará al usuario.</p>
            
            <form id="resetForm" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label for="modalContrasena" class="block text-sm font-medium text-slate-700 mb-1">Nueva contraseña temporal *</label>
                    <div class="flex items-center rounded-lg border border-slate-300 bg-slate-50 overflow-hidden focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500">
                        <input type="password" id="modalContrasena" name="contrasena" required minlength="6"
                               class="w-full px-4 py-2 bg-transparent border-0 focus:outline-none focus:ring-0 text-sm">
                        <button type="button" onclick="toggleModalPassword()"
                                class="px-3 text-slate-400 hover:text-slate-600 focus:outline-none bg-transparent">
                            <svg id="modal-eye-open" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <svg id="modal-eye-closed" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-5 h-5 hidden">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeResetModal()"
                            class="px-4 py-2 text-sm font-medium text-slate-700 bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                        Guardar contraseña
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openResetModal(userId, userName) {
            const modal = document.getElementById('resetModal');
            const form = document.getElementById('resetForm');
            const nameSpan = document.getElementById('modalUserName');
            
            form.action = `/usuarios/${userId}/reset-password`;
            nameSpan.textContent = userName;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeResetModal() {
            const modal = document.getElementById('resetModal');
            modal.classList.remove('flex');
            modal.classList.add('hidden');
            document.getElementById('modalContrasena').value = '';
        }

        function toggleModalPassword() {
            const input = document.getElementById('modalContrasena');
            const eyeOpen = document.getElementById('modal-eye-open');
            const eyeClosed = document.getElementById('modal-eye-closed');

            if (input.type === 'password') {
                input.type = 'text';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            } else {
                input.type = 'password';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            }
        }
    </script>
@endsection