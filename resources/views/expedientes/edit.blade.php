@extends('layouts.app')

@section('title', 'Editar expediente - Unifarmacia')

@section('content')
    <div class="min-h-screen bg-slate-50">
        @include('layouts.navigation')

        <main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

            {{-- Breadcrumb dinámico con el folio actual --}}
            <x-breadcrumbs :links="[
                'Expedientes' => route('expedientes.index'),
                'Editar expediente (' . $expediente->folioExpediente . ')' => null
            ]" />

            <div class="mb-6">
                <h2 class="text-2xl font-bold text-slate-900">Editar expediente</h2>
                <p class="text-slate-600 mt-1">
                    Folio: <span class="font-semibold text-slate-800">{{ $expediente->folioExpediente }}</span>
                </p>
            </div>

            <div class="flex flex-col lg:flex-row gap-8 items-start">

                {{-- SIDEBAR DE NAVEGACIÓN RÁPIDA --}}
                <aside class="hidden lg:block w-64 flex-shrink-0 sticky top-8">
                    <nav class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                        <ul class="space-y-1 text-sm font-medium text-slate-600">
                            <li><a href="#sec-paciente" class="block px-3 py-2 rounded-lg hover:bg-blue-50 hover:text-blue-700 transition-all">Datos del paciente</a></li>
                            <li><a href="#sec-contacto" class="block px-3 py-2 rounded-lg hover:bg-blue-50 hover:text-blue-700 transition-all">Contacto del paciente</a></li>
                            <li><a href="#sec-emergencia" class="block px-3 py-2 rounded-lg hover:bg-blue-50 hover:text-blue-700 transition-all">Contactos de emergencia</a></li>
                            <li><a href="#sec-antecedentes" class="block px-3 py-2 rounded-lg hover:bg-blue-50 hover:text-blue-700 transition-all">Antecedentes Clínicos</a></li>
                            <li><a href="#sec-alergias" class="block px-3 py-2 rounded-lg hover:bg-blue-50 hover:text-blue-700 transition-all">Alergias</a></li>
                            <li><a href="#sec-habitos" class="block px-3 py-2 rounded-lg hover:bg-blue-50 hover:text-blue-700 transition-all">Hábitos y Estilo</a></li>
                            <li id="nav-gineco" class="{{ old('sexo', $expediente->paciente->sexo) === 'F' ? '' : 'hidden' }}">
                                <a href="#seccion-gineco" class="block px-3 py-2 rounded-lg hover:bg-rose-50 hover:text-rose-700 text-rose-600 transition-all">Ginecoobstétricos</a>
                            </li>
                            <li><a href="#sec-triage" class="block px-3 py-2 rounded-lg hover:bg-blue-50 hover:text-blue-700 text-blue-600 transition-all mt-4 border-t border-slate-100 pt-3">Triage / Visita</a></li>
                        </ul>
                    </nav>
                </aside>

                {{-- FORMULARIO DE EDICIÓN --}}
                <div class="flex-1 w-full">
                    <form method="POST" action="{{ route('expedientes.update', $expediente) }}" autocomplete="off">
                        @csrf
                        @method('PUT')
                        @include('expedientes._form', ['paciente' => $expediente->paciente, 'textoBoton' => 'Guardar cambios'])
                    </form>
                </div>
            </div>
        </main>
    </div>
@endsection
