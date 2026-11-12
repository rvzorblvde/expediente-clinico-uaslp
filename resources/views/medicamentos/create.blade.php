@extends('layouts.app')

@section('title', 'Agregar medicamento - Unifarmacia')

@section('content')
    <div class="min-h-screen bg-slate-50">
        @include('layouts.navigation')

        <main class="max-w-2xl mx-auto py-10 px-4 sm:px-6 lg:px-8">

            {{-- Breadcrumbs --}}
            <div class="mb-6">
                <x-breadcrumbs />
            </div>

            {{-- Encabezado --}}
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-slate-900">Nuevo medicamento</h2>
                <p class="text-slate-600 mt-1">Completa el formulario para agregar un nuevo medicamento al catálogo.</p>
            </div>

            {{-- Formulario --}}
            <form method="POST" action="{{ route('medicamentos.store') }}"
                  class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                @csrf

                {{-- Nombre --}}
                <div class="mb-5">
                    <label for="nombre"
                           class="block text-sm font-medium text-slate-700 mb-1">
                        Nombre del medicamento <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="nombre"
                           name="nombre"
                           required
                           value="{{ old('nombre') }}"
                           class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 text-sm"
                           placeholder="Ej: Paracetamol">
                    @error('nombre')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Principio activo --}}
                <div class="mb-5">
                    <label for="principioActivo"
                           class="block text-sm font-medium text-slate-700 mb-1">
                        Principio activo <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="principioActivo"
                           name="principioActivo"
                           required
                           value="{{ old('principioActivo') }}"
                           class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 text-sm"
                           placeholder="Ej: Acetaminofén">
                    @error('principioActivo')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Presentación --}}
                <div class="mb-5">
                    <label for="presentacion"
                           class="block text-sm font-medium text-slate-700 mb-1">
                        Presentación (opcional)
                    </label>
                    <input type="text"
                           id="presentacion"
                           name="presentacion"
                           value="{{ old('presentacion') }}"
                           class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 text-sm"
                           placeholder="Ej: Tabletas 500 mg">
                    @error('presentacion')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Concentración --}}
                <div class="mb-5">
                    <label for="concentracion"
                           class="block text-sm font-medium text-slate-700 mb-1">
                        Concentración (opcional)
                    </label>
                    <input type="text"
                           id="concentracion"
                           name="concentracion"
                           value="{{ old('concentracion') }}"
                           class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 text-sm"
                           placeholder="Ej: 500 mg / 10 ml">
                    @error('concentracion')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Requiere receta --}}
                <div class="mb-5">
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        ¿Requiere receta? <span class="text-red-500">*</span>
                    </label>
                    <div class="flex items-center gap-6">
                        <label class="inline-flex items-center text-sm cursor-pointer">
                            <input type="radio"
                                   name="requiereReceta"
                                   value="1"
                                   {{ old('requiereReceta', '0') == '1' ? 'checked' : '' }}
                                   class="form-radio text-blue-600 h-4 w-4">
                            <span class="ml-2 text-slate-700">Sí</span>
                        </label>
                        <label class="inline-flex items-center text-sm cursor-pointer">
                            <input type="radio"
                                   name="requiereReceta"
                                   value="0"
                                   {{ old('requiereReceta', '0') == '0' ? 'checked' : '' }}
                                   class="form-radio text-blue-600 h-4 w-4">
                            <span class="ml-2 text-slate-700">No</span>
                        </label>
                    </div>
                    @error('requiereReceta')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Controlado --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        ¿Es un medicamento controlado? <span class="text-red-500">*</span>
                    </label>
                    <div class="flex items-center gap-6">
                        <label class="inline-flex items-center text-sm cursor-pointer">
                            <input type="radio"
                                   name="esControlado"
                                   value="1"
                                   {{ old('esControlado', '0') == '1' ? 'checked' : '' }}
                                   class="form-radio text-blue-600 h-4 w-4">
                            <span class="ml-2 text-slate-700">Sí</span>
                        </label>
                        <label class="inline-flex items-center text-sm cursor-pointer">
                            <input type="radio"
                                   name="esControlado"
                                   value="0"
                                   {{ old('esControlado', '0') == '0' ? 'checked' : '' }}
                                   class="form-radio text-blue-600 h-4 w-4">
                            <span class="ml-2 text-slate-700">No</span>
                        </label>
                    </div>
                    @error('esControlado')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Botones --}}
                <div class="flex flex-col sm:flex-row gap-3 pt-4">
                    <button type="submit"
                            class="inline-flex justify-center items-center px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded
                            hover:bg-blue-700 transition-colors">
                        Guardar medicamento
                    </button>
                    <a href="{{ route('medicamentos.index') }}"
                       class="inline-flex justify-center items-center px-6 py-2.5 bg-slate-100 text-slate-700 text-sm font-medium rounded
                       hover:bg-slate-200 transition-colors">
                        Cancelar
                    </a>
                </div>
            </form>
        </main>
    </div>
@endsection