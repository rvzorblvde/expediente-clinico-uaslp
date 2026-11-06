@extends('layouts.app')

@section('title', 'Inicio de Sesión - Unifarmacia')

@section('content')
    {{-- Fondo con una franja azul marino superior para un contraste más corporativo --}}
    <div class="min-h-screen flex items-center justify-center bg-slate-50 relative">
        <div class="absolute top-0 w-full h-1/3 bg-blue-900"></div>

        <div class="max-w-md w-full bg-white p-10 rounded-2xl shadow-2xl z-10 border border-slate-100">
            <div class="text-center mb-10">
                {{-- Inserción del logo vertical --}}
                <img src="{{ asset('images/unifarmacia_vertical.svg') }}" alt="Logo Unifarmacia" class="h-32 mx-auto mb-4 drop-shadow-sm">
                <p class="text-slate-500 text-sm font-medium tracking-wide">SISTEMA DE GESTIÓN Y FARMACOVIGILANCIA</p>
            </div>

            <!-- Alertas de sesión -->
            @if(session('error'))
                <div class="mb-6 p-4 text-sm text-red-700 bg-red-50 rounded-lg border border-red-200 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    {{ session('error') }}
                </div>
            @endif

            @if(session('status'))
                <div class="mb-6 p-4 text-sm text-teal-700 bg-teal-50 rounded-lg border border-teal-200 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    {{ session('status') }}
                </div>
            @endif

            <!-- Errores de validación -->
            @if ($errors->any())
                <div class="mb-6 p-4 text-sm text-red-700 bg-red-50 rounded-lg border border-red-200">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-1">Correo Electrónico</label>
                    <input type="email" name="correo" id="correo" required
                           class="block w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-blue-900 focus:border-blue-900 transition-all text-slate-800">
                </div>

                <div>
                    <label for="contrasena" class="block text-sm font-semibold text-slate-700 mb-1">Contraseña</label>
                    <div class="flex items-center rounded-lg border border-slate-300 bg-slate-50 overflow-hidden focus-within:bg-white focus-within:ring-2 focus-within:ring-blue-900 focus-within:border-blue-900 transition-all">
                        <input type="password" name="contrasena" id="contrasena" required
                               class="w-full px-4 py-2.5 bg-transparent border-0 focus:outline-none focus:ring-0 text-slate-800">
                        <button type="button" onclick="togglePassword()"
                                class="px-4 text-slate-400 hover:text-blue-900 focus:outline-none bg-transparent transition-colors">
                            {{-- Icono de Ojo Abierto --}}
                            <svg id="eye-open" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{-- Icono de Ojo Cerrado --}}
                            <svg id="eye-closed" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 hidden">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit"
                        class="w-full flex justify-center py-2.5 px-4 mt-2 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-colors">
                    Iniciar Sesión
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-slate-100 text-center">
                <form method="POST" action="{{ route('password-reset') }}" id="reset-request-form">
                    @csrf
                    <input type="hidden" name="correo" id="reset-correo-input">
                    <button type="button" onclick="handlePasswordResetRequest()"
                            class="text-sm text-slate-500 hover:text-blue-900 font-semibold transition-colors">
                        ¿Olvidaste tu contraseña?
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

        function handlePasswordResetRequest() {
            const inputCorreoLogin = document.getElementById('correo');

            if (!inputCorreoLogin || !inputCorreoLogin.value.trim()) {
                alert('Por favor, escribe tu correo electrónico en el campo superior antes de solicitar el restablecimiento.');
                if (inputCorreoLogin) inputCorreoLogin.focus();
                return;
            }

            document.getElementById('reset-correo-input').value = inputCorreoLogin.value.trim();
            document.getElementById('reset-request-form').submit();
        }
    </script>
@endsection
