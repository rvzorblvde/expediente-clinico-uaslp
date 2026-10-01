@php
    $c = $c ?? [];
    $tels = $c['telefonos'] ?? [];
    if (empty($tels)) {
        $tels = [''];
    }
    $cors = $c['correos'] ?? [];
@endphp

<div data-contacto="{{ $i }}" class="rounded-lg border border-slate-200 p-4 space-y-4">
    <div class="flex items-center justify-between">
        <span class="text-sm font-semibold text-slate-700">Contacto</span>
        <button type="button" data-remove="[data-contacto]" class="text-sm text-red-600 hover:text-red-800">Quitar contacto</button>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div>
            <label class="{{ $label }}">Nombre(s) *</label>
            <input type="text" name="contactos[{{ $i }}][nombres]" value="{{ $c['nombres'] ?? '' }}" maxlength="100" class="{{ $input }}">
        </div>
        <div>
            <label class="{{ $label }}">Primer apellido *</label>
            <input type="text" name="contactos[{{ $i }}][apellido1]" value="{{ $c['apellido1'] ?? '' }}" maxlength="100" class="{{ $input }}">
        </div>
        <div>
            <label class="{{ $label }}">Segundo apellido</label>
            <input type="text" name="contactos[{{ $i }}][apellido2]" value="{{ $c['apellido2'] ?? '' }}" maxlength="100" class="{{ $input }}">
        </div>
        <div>
            <label class="{{ $label }}">Parentesco *</label>
            <input type="text" name="contactos[{{ $i }}][parentesco]" value="{{ $c['parentesco'] ?? '' }}" maxlength="50" class="{{ $input }}">
        </div>
    </div>

    <div>
        <span class="{{ $label }}">Teléfonos</span>
        <div class="lista-ctel space-y-2">
            @foreach ($tels as $numero)
                <div class="fila flex items-center gap-2">
                    <input type="text" name="contactos[{{ $i }}][telefonos][]" value="{{ $numero }}" placeholder="Número" maxlength="20" inputmode="tel" class="{{ $input }}">
                    <button type="button" data-remove=".fila" class="px-2 text-sm text-red-600 hover:text-red-800">Quitar</button>
                </div>
            @endforeach
        </div>
        <button type="button" data-add="tpl-contacto-tel" data-scope="contacto" data-target=".lista-ctel"
                class="mt-2 text-sm font-medium text-blue-600 hover:text-blue-800">+ Agregar teléfono</button>
    </div>

    <div>
        <span class="{{ $label }}">Correos</span>
        <div class="lista-ccor space-y-2">
            @foreach ($cors as $correo)
                <div class="fila flex items-center gap-2">
                    <input type="email" name="contactos[{{ $i }}][correos][]" value="{{ $correo }}" placeholder="correo@ejemplo.com" maxlength="255" class="{{ $input }}">
                    <button type="button" data-remove=".fila" class="px-2 text-sm text-red-600 hover:text-red-800">Quitar</button>
                </div>
            @endforeach
        </div>
        <button type="button" data-add="tpl-contacto-correo" data-scope="contacto" data-target=".lista-ccor"
                class="mt-2 text-sm font-medium text-blue-600 hover:text-blue-800">+ Agregar correo</button>
    </div>
</div>
