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
                    <label for="password" class="block text-sm font-medium text-slate-700">Contraseña</label>
                    <input type="password" name="contrasena" id="contrasena" required
                           class="mt-1 block w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition-colors">
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
@endsection
