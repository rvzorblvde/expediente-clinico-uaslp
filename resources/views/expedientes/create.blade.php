@extends('layouts.app')

@section('title', 'Nuevo expediente - Unifarmacia')

@section('content')
    <div class="min-h-screen bg-slate-50">
        @include('layouts.navigation')

        {{-- Ampliamos el max-w para que quepa el sidebar y el formulario --}}
        <main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

            {{-- LLAMADA AL COMPONENTE BREADCRUMBS --}}
            <x-breadcrumbs />

            <div class="mb-6">
                <h2 class="text-2xl font-bold text-slate-900">Nuevo expediente</h2>
                <p class="text-slate-600 mt-1">Registra al paciente y crea su expediente. El folio se genera automáticamente.</p>
            </div>

            <div class="flex flex-col lg:flex-row gap-8">

                {{-- SIDEBAR FIJO (Solo visible en pantallas grandes) --}}
                <aside class="hidden lg:block w-64 flex-shrink-0">
                    <nav class="sticky top-8 bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                        <ul class="space-y-1 text-sm font-medium text-slate-600">
                            <li><a href="#sec-paciente" class="block px-3 py-2 rounded-lg hover:bg-blue-50 hover:text-blue-700 transition">Datos del paciente</a></li>
                            <li><a href="#sec-contacto" class="block px-3 py-2 rounded-lg hover:bg-blue-50 hover:text-blue-700 transition">Contacto del paciente</a></li>
                            <li><a href="#sec-emergencia" class="block px-3 py-2 rounded-lg hover:bg-blue-50 hover:text-blue-700 transition">Contactos de emergencia</a></li>
                            <li><a href="#sec-antecedentes" class="block px-3 py-2 rounded-lg hover:bg-blue-50 hover:text-blue-700 transition">Antecedentes Clínicos</a></li>
                            <li><a href="#sec-alergias" class="block px-3 py-2 rounded-lg hover:bg-blue-50 hover:text-blue-700 transition">Alergias</a></li>
                            <li><a href="#sec-habitos" class="block px-3 py-2 rounded-lg hover:bg-blue-50 hover:text-blue-700 transition">Hábitos y Estilo</a></li>
                            <li id="nav-gineco" class="{{ old('sexo') === 'F' ? '' : 'hidden' }}">
                                <a href="#seccion-gineco" class="block px-3 py-2 rounded-lg hover:bg-rose-50 hover:text-rose-700 text-rose-600 transition">Ginecoobstétricos</a>
                            </li>
                            <li><a href="#sec-triage" class="block px-3 py-2 rounded-lg hover:bg-blue-50 hover:text-blue-700 text-blue-600 transition mt-4 border-t border-slate-100 pt-3">Triage / Visita</a></li>
                        </ul>
                    </nav>
                </aside>

                {{-- ÁREA DEL FORMULARIO --}}
                <div class="flex-1">
                    <form method="POST" action="{{ route('expedientes.store') }}" autocomplete="off">
                        @csrf
                        @include('expedientes._form', ['paciente' => null, 'textoBoton' => 'Crear expediente'])
                    </form>
                </div>
            </div>
        </main>
    </div>
@endsection
