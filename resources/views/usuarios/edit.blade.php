@extends('layouts.app')

@section('title', 'Editar Usuario - Unifarmacia')

@section('content')
    <div class="min-h-screen bg-slate-50">
        @include('layouts.navigation')

        <main class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">

            <x-breadcrumbs />

            <div class="mb-6">
                <h2 class="text-2xl font-bold text-slate-900">Editar usuario</h2>
                <p class="text-slate-600 mt-1">Modifica los datos de la cuenta seleccionada.</p>
            </div>

            {{-- Errores de validación --}}
            @if ($errors->any())
                <div class="mb-6 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-800 text-sm">
                    <p class="font-semibold">Por favor corrige los siguientes errores:</p>
                    <ul class="list-disc list-inside mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 sm:p-8">
                <form method="POST" action="{{ route('usuarios.update', $usuario->idUsuario) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        {{-- Nombres --}}
                        <div>
                            <label for="nombres" class="block text-sm font-medium text-slate-700 mb-1">Nombre(s) *</label>
                            <input type="text" id="nombres" name="nombres" value="{{ old('nombres', $usuario->nombres) }}" required
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        {{-- Primer Apellido --}}
                        <div>
                            <label for="apellido1" class="block text-sm font-medium text-slate-700 mb-1">Primer apellido *</label>
                            <input type="text" id="apellido1" name="apellido1" value="{{ old('apellido1', $usuario->apellido1) }}" required
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        {{-- Segundo Apellido --}}
                        <div>
                            <label for="apellido2" class="block text-sm font-medium text-slate-700 mb-1">Segundo apellido</label>
                            <input type="text" id="apellido2" name="apellido2" value="{{ old('apellido2', $usuario->apellido2) }}"
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        {{-- Correo Electrónico --}}
                        <div>
                            <label for="correo" class="block text-sm font-medium text-slate-700 mb-1">Correo electrónico *</label>
                            <input type="email" id="correo" name="correo" value="{{ old('correo', $usuario->correo) }}" required
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        {{-- Rol del Sistema --}}
                        <div>
                            <label for="idRol" class="block text-sm font-medium text-slate-700 mb-1">Rol en el sistema *</label>
                            <select id="idRol" name="idRol" required
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Selecciona un rol</option>
                                @foreach ($roles as $rol)
                                    <option value="{{ $rol->idRol }}" {{ old('idRol', $usuario->idRol) == $rol->idRol ? 'selected' : '' }}>
                                        {{ $rol->nombreRol }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Botones de acción --}}
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <a href="{{ route('usuarios.index') }}"
                           class="px-4 py-2 text-sm font-medium text-slate-700 bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">
                            Cancelar
                        </a>
                        <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                            Actualizar usuario
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
@endsection