@php
    $medicamento ??= new \App\Models\Medicamento;
    $textoBoton ??= 'Guardar';
    $input = 'w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500';
    $label = 'block text-sm font-medium text-slate-700 mb-1';
    $error = 'text-red-500 text-xs mt-1';
@endphp

<section id="sec-medicamento" class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 mb-6">
    <h3 class="text-lg font-semibold text-slate-900 mb-4">Datos del medicamento</h3>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="nombre" class="{{ $label }}">Nombre *</label>
            <input type="text" id="nombre" name="nombre" maxlength="150" required
                   value="{{ old('nombre', $medicamento->nombre) }}" class="{{ $input }}" placeholder="Ej: Paracetamol">
            @error('nombre') <p class="{{ $error }}">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="principioActivo" class="{{ $label }}">Principio activo *</label>
            <input type="text" id="principioActivo" name="principioActivo" maxlength="150" required
                   value="{{ old('principioActivo', $medicamento->principioActivo) }}" class="{{ $input }}" placeholder="Ej: Acetaminofén">
            @error('principioActivo') <p class="{{ $error }}">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="presentacion" class="{{ $label }}">Presentación</label>
            <input type="text" id="presentacion" name="presentacion" maxlength="100"
                   value="{{ old('presentacion', $medicamento->presentacion) }}" class="{{ $input }}" placeholder="Ej: Tabletas">
            @error('presentacion') <p class="{{ $error }}">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="concentracion" class="{{ $label }}">Concentración</label>
            <input type="text" id="concentracion" name="concentracion" maxlength="100"
                   value="{{ old('concentracion', $medicamento->concentracion) }}" class="{{ $input }}" placeholder="Ej: 500 mg">
            @error('concentracion') <p class="{{ $error }}">{{ $message }}</p> @enderror
        </div>

        @foreach (['requiereReceta' => '¿Requiere receta?', 'esControlado' => '¿Es controlado?'] as $campo => $texto)
            @php($valor = (int) old($campo, $medicamento->$campo ?? 0))
            <div>
                <span class="{{ $label }}">{{ $texto }} *</span>
                <div class="flex items-center gap-6 mt-2">
                    @foreach ([1 => 'Sí', 0 => 'No'] as $v => $t)
                        <label class="inline-flex items-center text-sm cursor-pointer">
                            <input type="radio" name="{{ $campo }}" value="{{ $v }}" @checked($valor === $v)
                                   class="h-4 w-4 text-blue-600">
                            <span class="ml-2 text-slate-700">{{ $t }}</span>
                        </label>
                    @endforeach
                </div>
                @error($campo) <p class="{{ $error }}">{{ $message }}</p> @enderror
            </div>
        @endforeach
    </div>
</section>

<div class="flex flex-col sm:flex-row gap-3">
    <button type="submit"
            class="inline-flex justify-center items-center px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
        {{ $textoBoton }}
    </button>
    <a href="{{ route('medicamentos.index') }}"
       class="inline-flex justify-center items-center px-6 py-2.5 bg-slate-100 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-200 transition-colors">
        Cancelar
    </a>
</div>
