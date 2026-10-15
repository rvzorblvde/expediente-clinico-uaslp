@extends('layouts.app')

@section('title', 'Inicio de Sesión - Unifarmacia')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-slate-100">
        <div class="max-w-md w-full bg-white p-8 rounded-xl shadow-lg border border-slate-200">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-slate-800">Unifarmacia</h1>
                <p class="text-slate-500 mt-2">Sistema de Gestión y Farmacovigilancia</p>
            </div>

            <!-- Alerta de sesión (por ejemplo, si el middleware lo redirige aquí) -->
            @if(session('error'))
                <div class="mb-6 p-4 text-sm text-red-700 bg-red-100 rounded-lg border border-red-200">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('status'))
                <div class="mb-6 p-4 text-sm text-green-700 bg-green-100 rounded-lg border border-green-200">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Mostrar errores de validación -->
            @if ($errors->any())
                <div class="mb-6 p-4 text-sm text-red-700 bg-red-100 rounded-lg border border-red-200">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700">Correo Electrónico</label>
                    <input type="email" name="correo" id="correo" required
                           class="mt-1 block w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition-colors">
                </div>

                <div>
                    <label for="contrasena" class="block text-sm font-medium text-slate-700 mb-1">Contraseña</label>
                    {{-- Contenedor flexible integrado para el input y el botón del ojo --}}
                    <div class="flex items-center rounded-lg border border-slate-300 bg-slate-50 overflow-hidden focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500">
                        <input type="password" name="contrasena" id="contrasena" required
                               class="w-full px-4 py-2 bg-transparent border-0 focus:outline-none focus:ring-0 text-sm">
                        <button type="button" onclick="togglePassword()" class="px-3 text-slate-400 hover:text-slate-600 focus:outline-none bg-transparent">
                            {{-- Icono de Ojo Abierto (por defecto) --}}
                            <svg id="eye-open" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{-- Icono de Ojo Cerrado / Tachado (oculto por defecto) --}}
                            <svg id="eye-closed" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 hidden">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit"
                        class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    Iniciar Sesión
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-slate-100 text-center">
                <!-- Formulario para notificar al responsable -->
                <form method="POST" action="{{ route('password-reset') }}">
                    @csrf
                    <button type="submit" class="text-sm text-slate-500 hover:text-blue-600 font-medium transition-colors">
                        ¿Olvidaste tu contraseña? Solicitar restablecimiento
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('contrasena');
            const eyeOpen = document.getElementById('eye-open');
            const eyeClosed = document.getElementById('eye-closed');

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
