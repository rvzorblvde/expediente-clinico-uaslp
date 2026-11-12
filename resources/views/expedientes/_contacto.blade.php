@php
    $c = $c ?? [];
    $tels = collect($c['telefonos'] ?? [])
        ->map(fn ($t) => is_array($t) ? $t : ['numero' => $t, 'tipo' => ''])->all();
    if (empty($tels)) {
        $tels = [['numero' => '', 'tipo' => '']];
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
            <select name="contactos[{{ $i }}][parentesco]" class="{{ $input }} bg-white">
                <option value="">Selecciona...</option>
                @foreach (config('expediente.parentescos') as $par)
                    <option value="{{ $par }}" {{ ($c['parentesco'] ?? '') === $par ? 'selected' : '' }}>{{ $par }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div>
        <span class="{{ $label }}">Teléfonos * <span class="text-xs text-slate-400">(al menos uno, 10 dígitos)</span></span>
        <div class="lista-ctel space-y-2">
            @foreach ($tels as $j => $t)
                <div class="fila flex items-center gap-2">
                    <input type="text" name="contactos[{{ $i }}][telefonos][{{ $j }}][numero]" value="{{ $t['numero'] ?? '' }}"
                           placeholder="Número (10 dígitos)" maxlength="14" inputmode="tel" class="{{ $input }}">
                    <select name="contactos[{{ $i }}][telefonos][{{ $j }}][tipo]" class="{{ $select }} bg-white">
                        @foreach (config('expediente.tipos_telefono') as $tipo)
                            <option value="{{ $tipo }}" {{ ($t['tipo'] ?? '') === $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
                        @endforeach
                    </select>
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
