@php
    $m = $medicamento ?? null;
    $formas = config('medicamentos.formas');
    $uConc = config('medicamentos.unidades_concentracion');
    $uEnv = config('medicamentos.unidades_envase');

    // Registros viejos (solo texto) se muestran en la opción "Otra"
    $forma = old('formaFarmaceutica', $m?->formaFarmaceutica ?: ($m?->presentacion ? 'otra' : ''));
    $unidadConc = old('concentracionUnidad', $m?->concentracionUnidad ?: ($m?->concentracion ? 'otra' : ''));
    $valorConc = old('concentracionValor', $m?->concentracionValor !== null ? \App\Models\Medicamento::formatearNumero($m->concentracionValor) : '');
    $controlado = (string) old('esControlado', $m?->esControlado ? '1' : '0');
    $receta = (string) old('requiereReceta', $m?->requiereReceta ? '1' : '0');

    $input = 'w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 text-sm';
    $label = 'block text-sm font-medium text-slate-700 mb-1';
    $err = 'text-red-500 text-xs mt-1';
@endphp

{{-- Nombre --}}
@php $tipo = old('tipo', $m?->tipo ?? 'comercial'); @endphp

{{-- Tipo --}}
<div class="mb-5">
    <label class="{{ $label }}">Tipo <span class="text-red-500">*</span></label>
    <div class="flex items-center gap-6">
        @foreach (config('medicamentos.tipos') as $valorTipo => $etiqueta)
            <label class="inline-flex items-center text-sm cursor-pointer">
                <input type="radio" name="tipo" value="{{ $valorTipo }}" id="tipo-{{ $valorTipo }}"
                       @checked($tipo === $valorTipo) required class="h-4 w-4 text-blue-600">
                <span class="ml-2">{{ $etiqueta }}</span>
            </label>
        @endforeach
    </div>
    @error('tipo') <p class="{{ $err }}">{{ $message }}</p> @enderror
</div>

{{-- Nombre --}}
<div class="mb-5">
    <label for="nombre" class="{{ $label }}">Nombre comercial o genérico <span class="text-red-500">*</span></label>
    <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $m?->nombre) }}"
           required minlength="2" maxlength="150" class="{{ $input }}" placeholder="Ej: Tempra (comercial) o Paracetamol (genérico)">
    @error('nombre') <p class="{{ $err }}">{{ $message }}</p> @enderror
</div>

{{-- Principio activo + laboratorio --}}
<div class="mb-5 grid grid-cols-1 sm:grid-cols-2 gap-3">
    <div>
        <label for="principioActivo" class="{{ $label }}">Principio activo <span class="text-red-500">*</span></label>
        <input type="text" id="principioActivo" name="principioActivo" list="lista-principios"
               value="{{ old('principioActivo', $m?->principioActivo) }}"
               required minlength="2" maxlength="150" class="{{ $input }}" placeholder="Ej: Paracetamol">
        <datalist id="lista-principios">
            @foreach ($principiosActivos as $p)
                <option value="{{ $p }}">
            @endforeach
        </datalist>
        @error('principioActivo') <p class="{{ $err }}">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="laboratorio" class="{{ $label }}">Laboratorio / fabricante</label>
        <input type="text" id="laboratorio" name="laboratorio" value="{{ old('laboratorio', $m?->laboratorio) }}"
               minlength="2" maxlength="100" class="{{ $input }}" placeholder="Ej: Genomma Lab">
        @error('laboratorio') <p class="{{ $err }}">{{ $message }}</p> @enderror
    </div>
</div>

{{-- Forma farmacéutica / presentación --}}
<div class="mb-5">
    <label for="formaFarmaceutica" class="{{ $label }}">Forma farmacéutica (presentación)</label>
    <select id="formaFarmaceutica" name="formaFarmaceutica" class="{{ $input }} bg-white">
        <option value="">Sin especificar</option>
        @foreach ($formas as $f)
            <option value="{{ $f }}" @selected($forma === $f)>{{ $f }}</option>
        @endforeach
        <option value="otra" @selected($forma === 'otra')>Otra (texto libre)</option>
    </select>
    @error('formaFarmaceutica') <p class="{{ $err }}">{{ $message }}</p> @enderror

    <div id="grupo-pres-libre" class="mt-3" hidden>
        <input type="text" id="presentacion" name="presentacion" value="{{ old('presentacion', $m?->presentacion) }}"
               maxlength="100" class="{{ $input }}" placeholder="Ej: Caja con 2 blísteres de 10 tabletas">
        @error('presentacion') <p class="{{ $err }}">{{ $message }}</p> @enderror
    </div>

    <div id="grupo-envase" class="mt-3 grid grid-cols-2 gap-3" hidden>
        <div>
            <label for="cantidadEnvase" class="text-xs text-slate-500">Contenido del envase</label>
            <input type="number" id="cantidadEnvase" name="cantidadEnvase" min="1" max="100000" step="1"
                   value="{{ old('cantidadEnvase', $m?->cantidadEnvase) }}" class="{{ $input }}" placeholder="Ej: 20">
            @error('cantidadEnvase') <p class="{{ $err }}">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="unidadEnvase" class="text-xs text-slate-500">Unidad</label>
            <select id="unidadEnvase" name="unidadEnvase" class="{{ $input }} bg-white">
                <option value="">—</option>
                @foreach ($uEnv as $u)
                    <option value="{{ $u }}" @selected(old('unidadEnvase', $m?->unidadEnvase) === $u)>{{ $u }}</option>
                @endforeach
            </select>
            @error('unidadEnvase') <p class="{{ $err }}">{{ $message }}</p> @enderror
        </div>
    </div>
</div>

{{-- Concentración --}}
<div class="mb-5">
    <label class="{{ $label }}">Concentración</label>
    <div class="grid grid-cols-2 gap-3">
        <div id="grupo-conc-valor" hidden>
            <input type="number" id="concentracionValor" name="concentracionValor" min="0.0001" max="999999" step="0.0001"
                   value="{{ $valorConc }}" class="{{ $input }}" placeholder="Valor. Ej: 500">
            @error('concentracionValor') <p class="{{ $err }}">{{ $message }}</p> @enderror
        </div>
        <div id="col-conc-unidad">
            <select id="concentracionUnidad" name="concentracionUnidad" class="{{ $input }} bg-white" aria-label="Unidad de concentración">
                <option value="">Sin especificar</option>
                @foreach ($uConc as $u)
                    <option value="{{ $u }}" @selected($unidadConc === $u)>{{ $u }}</option>
                @endforeach
                <option value="otra" @selected($unidadConc === 'otra')>Otra / combinación (texto libre)</option>
            </select>
            @error('concentracionUnidad') <p class="{{ $err }}">{{ $message }}</p> @enderror
        </div>
    </div>
    <div id="grupo-conc-libre" class="mt-3" hidden>
        <input type="text" id="concentracion" name="concentracion" value="{{ old('concentracion', $m?->concentracion) }}"
               maxlength="100" class="{{ $input }}" placeholder="Ej: 500 mg / 65 mg">
        @error('concentracion') <p class="{{ $err }}">{{ $message }}</p> @enderror
    </div>
</div>

{{-- Vista previa --}}
<div class="mb-5 px-3 py-2 rounded-lg bg-slate-50 border border-slate-200 text-sm text-slate-600">
    <span class="font-medium text-slate-700">Se guardará como:</span>
    <span id="vista-previa">—</span>
</div>

{{-- Receta / controlado --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-6">
    <div>
        <label class="{{ $label }}">¿Requiere receta? <span class="text-red-500">*</span></label>
        <div class="flex items-center gap-6">
            <label class="inline-flex items-center text-sm cursor-pointer">
                <input type="radio" name="requiereReceta" value="1" id="receta-si" @checked($receta === '1') required class="h-4 w-4 text-blue-600">
                <span class="ml-2">Sí</span>
            </label>
            <label class="inline-flex items-center text-sm cursor-pointer">
                <input type="radio" name="requiereReceta" value="0" id="receta-no" @checked($receta === '0') class="h-4 w-4 text-blue-600">
                <span class="ml-2">No</span>
            </label>
        </div>
        @error('requiereReceta') <p class="{{ $err }}">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="{{ $label }}">¿Es controlado? <span class="text-red-500">*</span></label>
        <div class="flex items-center gap-6">
            <label class="inline-flex items-center text-sm cursor-pointer">
                <input type="radio" name="esControlado" value="1" id="controlado-si" @checked($controlado === '1') required class="h-4 w-4 text-blue-600">
                <span class="ml-2">Sí</span>
            </label>
            <label class="inline-flex items-center text-sm cursor-pointer">
                <input type="radio" name="esControlado" value="0" id="controlado-no" @checked($controlado === '0') class="h-4 w-4 text-blue-600">
                <span class="ml-2">No</span>
            </label>
        </div>
        <p class="text-xs text-slate-500 mt-1">Un medicamento controlado siempre requiere receta.</p>
        @error('esControlado') <p class="{{ $err }}">{{ $message }}</p> @enderror
    </div>
</div>

<script>
    (() => {
        const $ = (id) => document.getElementById(id);
        const forma = $('formaFarmaceutica'), presLibre = $('presentacion'), cant = $('cantidadEnvase'), uEnv = $('unidadEnvase');
        const valor = $('concentracionValor'), uConc = $('concentracionUnidad'), concLibre = $('concentracion');
        const gPres = $('grupo-pres-libre'), gEnvase = $('grupo-envase'), gValor = $('grupo-conc-valor'), gLibre = $('grupo-conc-libre');
        const colUnidad = $('col-conc-unidad');
        const recetaSi = $('receta-si'), recetaNo = $('receta-no'), contSi = $('controlado-si');

        const permitido = /^[\p{L}\p{N}\s.,\-\/()%+'&]+$/u;
        const msgTexto = "Solo letras, números y . , - / ( ) % + ' &";

        function mostrar(grupo, visible) {
            grupo.hidden = !visible;
            grupo.querySelectorAll('input, select').forEach((el) => {
                el.disabled = !visible;
                if (!visible) el.value = '';
            });
        }

        function fmt(n) { return String(Number(n)); }

        function sincronizar() {
            const formaReal = forma.value && forma.value !== 'otra';
            const concReal = uConc.value && uConc.value !== 'otra';

            mostrar(gPres, forma.value === 'otra');
            mostrar(gEnvase, formaReal);
            mostrar(gValor, concReal);
            mostrar(gLibre, uConc.value === 'otra');
            colUnidad.classList.toggle('col-span-2', !concReal);

            presLibre.required = forma.value === 'otra';
            concLibre.required = uConc.value === 'otra';
            valor.required = !!concReal;
            cant.required = !!uEnv.value;
            uEnv.required = !!cant.value;

            // Genérico: el nombre se autocompleta con el principio activo (editable)
            const tipoGen = $('tipo-generico'), nombre = $('nombre'), principio = $('principioActivo');
            let nombreEditado = nombre.value !== '' && nombre.value !== principio.value;
            nombre.addEventListener('input', () => { nombreEditado = true; });
            principio.addEventListener('input', () => {
                if (tipoGen.checked && !nombreEditado) nombre.value = principio.value;
            });
            tipoGen.addEventListener('change', () => {
                if (tipoGen.checked && !nombre.value) nombre.value = principio.value;
            });

            // Controlado => requiere receta
            if (contSi.checked) { recetaSi.checked = true; recetaNo.disabled = true; } else { recetaNo.disabled = false; }

            // Vista previa (misma lógica que el backend)
            const pres = formaReal
                ? (cant.value && uEnv.value ? `${forma.value} (${cant.value} ${uEnv.value})` : forma.value)
                : (forma.value === 'otra' ? presLibre.value : '');
            const conc = concReal
                ? (valor.value ? (uConc.value === '%' ? `${fmt(valor.value)}%` : `${fmt(valor.value)} ${uConc.value}`) : '')
                : (uConc.value === 'otra' ? concLibre.value : '');
            $('vista-previa').textContent = [pres && `Presentación: ${pres}`, conc && `Concentración: ${conc}`].filter(Boolean).join('  ·  ') || '—';
        }

        ['nombre', 'principioActivo', 'laboratorio', 'presentacion', 'concentracion'].forEach((id) => {
            const el = $(id);
            el.addEventListener('input', () => el.setCustomValidity(el.value && !permitido.test(el.value) ? msgTexto : ''));
        });

        [forma, presLibre, cant, uEnv, valor, uConc, concLibre, contSi, $('controlado-no'), recetaSi, recetaNo]
            .forEach((el) => { el.addEventListener('input', sincronizar); el.addEventListener('change', sincronizar); });

        // Evita envíos dobles
        const formulario = $('form-medicamento');
        formulario.addEventListener('submit', () => {
            formulario.querySelector('button[type=submit]').disabled = true;
        });

        sincronizar();
    })();
</script>
