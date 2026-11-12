@php
    $p = $paciente ?? null;

    $input  = 'w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500';
    $select = 'w-36 rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500';
    $label  = 'block text-sm font-medium text-slate-700 mb-1';
    $tipos  = config('expediente.tipos_telefono');

    $telefonos = old('telefonos', $p
        ? $p->telefonos->map(fn ($t) => ['numero' => $t->numero, 'tipo' => $t->tipo])->all()
        : []);
    if (empty($telefonos)) {
        $telefonos = [['numero' => '', 'tipo' => '']];
    }

    $correos = old('correos', $p ? $p->correos->pluck('correo')->all() : []);

    $contactos = old('contactos', $p
        ? $p->contactos->map(fn ($c) => [
            'nombres'    => $c->nombres,
            'apellido1'  => $c->apellido1,
            'apellido2'  => $c->apellido2,
            'parentesco' => $c->parentesco,
            'telefonos'  => $c->telefonos->map(fn ($t) => ['numero' => $t->numero, 'tipo' => $t->tipo])->all(),
            'correos'    => $c->correos->pluck('correo')->all(),
        ])->all()
        : []);

    $antecedentes     = old('antecedentes', []);
    $alergias         = old('alergias', []);
    $noFarmacologicos = old('noFarmacologicos', []);

    $fechaNac = old('fechaNac', $p?->fechaNac?->format('d/m/Y'));
    $sexo     = old('sexo', $p?->sexo);
@endphp

@if ($errors->any())
    <div class="mb-6 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-800 text-sm">
        <p class="font-semibold mb-1">Revisa los siguientes campos:</p>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- ========== Datos del paciente ========== --}}
<section id="sec-paciente" class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 mb-6">
    <h3 class="text-lg font-semibold text-slate-900 mb-4">Datos del paciente</h3>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <label for="nombres" class="{{ $label }}">Nombre(s) *</label>
            <input type="text" id="nombres" name="nombres" value="{{ old('nombres', $p?->nombres) }}" minlength="2" maxlength="100" required class="{{ $input }}">
            @error('nombres') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="apellido1" class="{{ $label }}">Primer apellido *</label>
            <input type="text" id="apellido1" name="apellido1" value="{{ old('apellido1', $p?->apellido1) }}" minlength="2" maxlength="100" required class="{{ $input }}">
            @error('apellido1') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="apellido2" class="{{ $label }}">Segundo apellido</label>
            <input type="text" id="apellido2" name="apellido2" value="{{ old('apellido2', $p?->apellido2) }}" maxlength="100" class="{{ $input }}">
            @error('apellido2') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="fechaNac" class="{{ $label }}">Fecha de nacimiento *</label>
            <input type="text" id="fechaNac" name="fechaNac" value="{{ $fechaNac }}"
                   placeholder="DD/MM/AAAA" inputmode="numeric" maxlength="10" required class="{{ $input }}">
            @error('fechaNac') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="sm:col-span-2">
            <label for="curp" class="{{ $label }}">CURP * <span class="text-xs text-slate-400">(se valida contra fecha de nacimiento y sexo)</span></label>
            <input type="text" id="curp" name="curp" value="{{ old('curp', $p?->curp) }}"
                   maxlength="18" required autocomplete="off" autocapitalize="characters" spellcheck="false"
                   class="{{ $input }} uppercase tracking-wider">
            @error('curp') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="mt-4">
        <span class="{{ $label }}">Sexo *</span>
        <div class="flex flex-wrap gap-6 mt-1">
            @foreach (['M' => 'Masculino', 'F' => 'Femenino', 'X' => 'Otro'] as $valor => $texto)
                <label class="inline-flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                    <input type="radio" name="sexo" value="{{ $valor }}" {{ $sexo === $valor ? 'checked' : '' }} required
                           class="h-4 w-4 border-slate-300 text-blue-600 focus:ring-blue-500">
                    {{ $texto }}
                </label>
            @endforeach
        </div>
        @error('sexo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
        <div>
            <label for="ocupacion" class="{{ $label }}">Ocupación</label>
            <input type="text" id="ocupacion" name="ocupacion" value="{{ old('ocupacion', $p?->ocupacion) }}" maxlength="100"
                   placeholder="Ej. Estudiante, comerciante..." class="{{ $input }}">
            @error('ocupacion') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="escolaridad" class="{{ $label }}">Escolaridad</label>
            <select id="escolaridad" name="escolaridad" class="{{ $input }} bg-white">
                <option value="">Sin especificar</option>
                @foreach (config('expediente.escolaridades') as $esc)
                    <option value="{{ $esc }}" @selected(old('escolaridad', $p?->escolaridad) === $esc)>{{ $esc }}</option>
                @endforeach
            </select>
            @error('escolaridad') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="servicioMedico" class="{{ $label }}">Servicio médico</label>
            <select id="servicioMedico" name="servicioMedico" class="{{ $input }} bg-white">
                <option value="">Sin especificar</option>
                @foreach (config('expediente.servicios_medicos') as $sm)
                    <option value="{{ $sm }}" @selected(old('servicioMedico', $p?->servicioMedico) === $sm)>{{ $sm }}</option>
                @endforeach
            </select>
            @error('servicioMedico') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
        <div>
            <label for="lugarProcedencia" class="{{ $label }}">Lugar de procedencia</label>
            <input type="text" id="lugarProcedencia" name="lugarProcedencia" value="{{ old('lugarProcedencia', $p?->lugarProcedencia) }}" maxlength="255" class="{{ $input }}">
        </div>
        <div>
            <label for="lugarResidencia" class="{{ $label }}">Lugar de residencia</label>
            <input type="text" id="lugarResidencia" name="lugarResidencia" value="{{ old('lugarResidencia', $p?->lugarResidencia) }}" maxlength="255" class="{{ $input }}">
        </div>
    </div>
</section>

{{-- ========== Contacto del paciente ========== --}}
<section id="sec-contacto" class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 mb-6">
    <h3 class="text-lg font-semibold text-slate-900 mb-4">Contacto del paciente</h3>

    <span class="{{ $label }}">Teléfonos * <span class="text-xs text-slate-400">(al menos uno, 10 dígitos)</span></span>
    <div id="lista-telefonos" class="space-y-2">
        @foreach ($telefonos as $i => $t)
            <div class="fila flex items-center gap-2">
                <input type="text" name="telefonos[{{ $i }}][numero]" value="{{ $t['numero'] ?? '' }}"
                       placeholder="Número (10 dígitos)" maxlength="14" inputmode="tel" class="{{ $input }}">
                <select name="telefonos[{{ $i }}][tipo]" class="{{ $select }} bg-white">
                    @foreach ($tipos as $tipo)
                        <option value="{{ $tipo }}" {{ ($t['tipo'] ?? '') === $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
                    @endforeach
                </select>
                <button type="button" data-remove=".fila" class="px-2 text-sm text-red-600 hover:text-red-800">Quitar</button>
            </div>
        @endforeach
    </div>
    <button type="button" data-add="tpl-telefono" data-target="#lista-telefonos"
            class="mt-2 text-sm font-medium text-blue-600 hover:text-blue-800">+ Agregar teléfono</button>
    @error('telefonos') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    @error('telefonos.*.numero') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    @error('telefonos.*.tipo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

    <div class="mt-6">
        <span class="{{ $label }}">Correos electrónicos <span class="text-xs text-slate-400">(opcional)</span></span>
        <div id="lista-correos" class="space-y-2">
            @foreach ($correos as $correo)
                <div class="fila flex items-center gap-2">
                    <input type="email" name="correos[]" value="{{ $correo }}" placeholder="correo@ejemplo.com" maxlength="255" class="{{ $input }}">
                    <button type="button" data-remove=".fila" class="px-2 text-sm text-red-600 hover:text-red-800">Quitar</button>
                </div>
            @endforeach
        </div>
        <button type="button" data-add="tpl-correo" data-target="#lista-correos"
                class="mt-2 text-sm font-medium text-blue-600 hover:text-blue-800">+ Agregar correo</button>
        @error('correos.*') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
</section>

{{-- ========== Contactos de emergencia ========== --}}
<section id="sec-emergencia" class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 mb-6">
    <h3 class="text-lg font-semibold text-slate-900">Contactos de emergencia</h3>
    <p class="text-sm text-slate-500 mb-4">Opcional. Los bloques en blanco se ignoran.</p>

    <div id="lista-contactos" class="space-y-4">
        @foreach ($contactos as $i => $c)
            @include('expedientes._contacto', ['i' => $i, 'c' => $c, 'input' => $input, 'label' => $label])
        @endforeach
    </div>

    <button type="button" data-add="tpl-contacto" data-target="#lista-contactos"
            class="mt-3 text-sm font-medium text-blue-600 hover:text-blue-800">+ Agregar contacto de emergencia</button>
    @error('contactos.*.nombres') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    @error('contactos.*.apellido1') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    @error('contactos.*.parentesco') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    @error('contactos.*.telefonos') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    @error('contactos.*.telefonos.*.numero') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</section>

{{-- ========== Antecedentes Clínicos ========== --}}
<section id="sec-antecedentes" class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 mb-6">
    <h3 class="text-lg font-semibold text-slate-900 mb-4">Antecedentes Clínicos</h3>
    <p class="text-sm text-slate-500 mb-4">Quirúrgicos, enfermedades (de la lista oficial) u otros. Marca si es antecedente familiar.</p>

    <div id="lista-antecedentes" class="space-y-3">
        @foreach ($antecedentes as $i => $a)
            <div class="fila grid grid-cols-1 sm:grid-cols-12 gap-2 items-start border-l-2 border-blue-400 pl-3 py-2">
                <div class="sm:col-span-2">
                    <select name="antecedentes[{{ $i }}][tipo]" class="ant-tipo {{ $input }} bg-white" required>
                        <option value="">Tipo...</option>
                        @foreach (config('expediente.tipos_antecedente') as $tipoA)
                            <option value="{{ $tipoA }}" {{ ($a['tipo'] ?? '') === $tipoA ? 'selected' : '' }}>{{ $tipoA }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-3 ant-enfermedad-wrap" hidden>
                    <select name="antecedentes[{{ $i }}][enfermedad]" class="ant-enfermedad {{ $input }} bg-white">
                        <option value="">Enfermedad...</option>
                        @foreach (config('expediente.enfermedades') as $enf)
                            <option value="{{ $enf }}" {{ ($a['enfermedad'] ?? '') === $enf ? 'selected' : '' }}>{{ $enf }}</option>
                        @endforeach
                        <option value="Otra" {{ ($a['enfermedad'] ?? '') === 'Otra' ? 'selected' : '' }}>Otra (detallar)</option>
                    </select>
                </div>
                <div class="sm:col-span-3 ant-nombre-wrap">
                    <input type="text" name="antecedentes[{{ $i }}][nombre]" value="{{ $a['nombre'] ?? '' }}"
                           placeholder="Nombre / Título" maxlength="100" class="ant-nombre {{ $input }}">
                </div>
                <div class="sm:col-span-2">
                    <input type="text" name="antecedentes[{{ $i }}][descripcion]" value="{{ $a['descripcion'] ?? '' }}"
                           placeholder="Descripción" class="{{ $input }}">
                </div>
                <div class="sm:col-span-1">
                    <label class="inline-flex items-center gap-1 text-xs text-slate-600 mt-2 cursor-pointer">
                        <input type="checkbox" name="antecedentes[{{ $i }}][esFamiliar]" value="1"
                               {{ !empty($a['esFamiliar']) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600">
                        Familiar
                    </label>
                </div>
                <div class="sm:col-span-1 text-right">
                    <button type="button" data-remove=".fila" class="text-sm text-red-600 hover:text-red-800 mt-2">Quitar</button>
                </div>
            </div>
        @endforeach
    </div>
    <button type="button" data-add="tpl-antecedente" data-target="#lista-antecedentes"
            class="mt-3 text-sm font-medium text-blue-600 hover:text-blue-800">+ Agregar antecedente</button>
    @error('antecedentes.*.tipo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    @error('antecedentes.*.enfermedad') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    @error('antecedentes.*.nombre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</section>

{{-- ========== Alergias ========== --}}
<section id="sec-alergias" class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 mb-6">
    <h3 class="text-lg font-semibold text-slate-900 mb-4">Alergias</h3>

    <div id="lista-alergias" class="space-y-3">
        @foreach ($alergias as $i => $al)
            <div class="fila grid grid-cols-1 sm:grid-cols-12 gap-2 items-start border-l-2 border-red-400 pl-3 py-2">
                <div class="sm:col-span-3">
                    <input type="text" name="alergias[{{ $i }}][sustancia]" value="{{ $al['sustancia'] ?? '' }}" placeholder="Sustancia o medicamento" maxlength="150" required class="{{ $input }}">
                </div>
                <div class="sm:col-span-3">
                    <input type="text" name="alergias[{{ $i }}][reaccion]" value="{{ $al['reaccion'] ?? '' }}" placeholder="Reacción presentada" class="{{ $input }}">
                </div>
                <div class="sm:col-span-2">
                    <select name="alergias[{{ $i }}][severidad]" class="{{ $input }} bg-white">
                        @foreach (config('expediente.severidades_alergia') as $sev)
                            <option value="{{ $sev }}" {{ ($al['severidad'] ?? '') === $sev ? 'selected' : '' }}>{{ $sev }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-3">
                    <input type="text" name="alergias[{{ $i }}][fechaAproximada]" value="{{ $al['fechaAproximada'] ?? '' }}"
                           placeholder="¿Desde cuándo? DD/MM/AAAA" inputmode="numeric" maxlength="10" class="campo-fecha {{ $input }}">
                </div>
                <div class="sm:col-span-1 text-right">
                    <button type="button" data-remove=".fila" class="text-sm text-red-600 hover:text-red-800 mt-2">Quitar</button>
                </div>
            </div>
        @endforeach
    </div>
    <button type="button" data-add="tpl-alergia" data-target="#lista-alergias"
            class="mt-3 text-sm font-medium text-blue-600 hover:text-blue-800">+ Agregar alergia</button>
    @error('alergias.*.sustancia') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    @error('alergias.*.severidad') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    @error('alergias.*.fechaAproximada') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</section>

{{-- ========== Hábitos y Estilo de Vida ========== --}}
<section id="sec-habitos" class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 mb-6">
    <h3 class="text-lg font-semibold text-slate-900 mb-4">Hábitos y Estilo de Vida</h3>
    @php
        $frecuencias = config('expediente.frecuencias_habito');
        $habitos = old('habitos', []);
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div>
            <label class="{{ $label }}">Tabaquismo</label>
            <select name="habitos[frecuenciaTabaquismo]" class="{{ $input }}">
                @foreach($frecuencias as $f)
                    <option value="{{ $f }}" {{ ($habitos['frecuenciaTabaquismo'] ?? 'Nunca') === $f ? 'selected' : '' }}>{{ $f }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="{{ $label }}">Alcoholismo</label>
            <select name="habitos[frecuenciaAlcoholismo]" class="{{ $input }}">
                @foreach($frecuencias as $f)
                    <option value="{{ $f }}" {{ ($habitos['frecuenciaAlcoholismo'] ?? 'Nunca') === $f ? 'selected' : '' }}>{{ $f }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="{{ $label }}">Té / café (cafeína)</label>
            <select name="habitos[frecuenciaCafeina]" class="{{ $input }}">
                @foreach($frecuencias as $f)
                    <option value="{{ $f }}" {{ ($habitos['frecuenciaCafeina'] ?? 'Nunca') === $f ? 'selected' : '' }}>{{ $f }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="{{ $label }}">Actividad Física</label>
            <select name="habitos[frecuenciaActFisica]" class="{{ $input }}">
                @foreach($frecuencias as $f)
                    <option value="{{ $f }}" {{ ($habitos['frecuenciaActFisica'] ?? 'Nunca') === $f ? 'selected' : '' }}>{{ $f }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="{{ $label }}">Consumo de Drogas</label>
            <select name="habitos[frecuenciaDrogas]" id="select-drogas" class="{{ $input }}">
                @foreach($frecuencias as $f)
                    <option value="{{ $f }}" {{ ($habitos['frecuenciaDrogas'] ?? 'Nunca') === $f ? 'selected' : '' }}>{{ $f }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="{{ $label }}">Horas de sueño promedio</label>
            <input type="number" name="habitos[horaSueno]" value="{{ $habitos['horaSueno'] ?? '' }}" min="0" max="24" placeholder="Ej. 8" class="{{ $input }}">
        </div>
        <div class="sm:col-span-3">
            <label class="{{ $label }}">Descripción de drogas consumidas</label>
            <input type="text" name="habitos[descripcionDrogas]" id="desc-drogas" value="{{ $habitos['descripcionDrogas'] ?? '' }}"
                   {{ ($habitos['frecuenciaDrogas'] ?? 'Nunca') === 'Nunca' ? 'disabled' : '' }}
                   placeholder="Especificar sustancias..." class="{{ $input }} disabled:bg-slate-100 disabled:cursor-not-allowed">
        </div>
    </div>
    @error('habitos.frecuenciaCafeina') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</section>

{{-- ========== Tratamientos no farmacológicos ========== --}}
<section id="sec-nofarma" class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 mb-6">
    <h3 class="text-lg font-semibold text-slate-900 mb-1">Tratamientos no farmacológicos</h3>
    <p class="text-sm text-slate-500 mb-4">Tés, remedios herbolarios, homeopatía, terapias alternativas (RF-01).</p>

    <div id="lista-nofarma" class="space-y-3">
        @foreach ($noFarmacologicos as $i => $t)
            <div class="fila grid grid-cols-1 sm:grid-cols-12 gap-2 items-start border-l-2 border-emerald-400 pl-3 py-2">
                <div class="sm:col-span-3">
                    <select name="noFarmacologicos[{{ $i }}][tipo]" class="{{ $input }} bg-white" required>
                        <option value="">Tipo...</option>
                        @foreach (config('expediente.tipos_no_farmacologico') as $tipoT)
                            <option value="{{ $tipoT }}" {{ ($t['tipo'] ?? '') === $tipoT ? 'selected' : '' }}>{{ $tipoT }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-4">
                    <input type="text" name="noFarmacologicos[{{ $i }}][titulo]" value="{{ $t['titulo'] ?? '' }}"
                           placeholder="Título (Ej. Té de manzanilla)" maxlength="150" class="{{ $input }}">
                </div>
                <div class="sm:col-span-4">
                    <input type="text" name="noFarmacologicos[{{ $i }}][descripcion]" value="{{ $t['descripcion'] ?? '' }}"
                           placeholder="Descripción (frecuencia, para qué lo usa...)" class="{{ $input }}">
                </div>
                <div class="sm:col-span-1 text-right">
                    <button type="button" data-remove=".fila" class="text-sm text-red-600 hover:text-red-800 mt-2">Quitar</button>
                </div>
            </div>
        @endforeach
    </div>
    <button type="button" data-add="tpl-nofarma" data-target="#lista-nofarma"
            class="mt-3 text-sm font-medium text-blue-600 hover:text-blue-800">+ Agregar tratamiento</button>
    @error('noFarmacologicos.*.tipo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    @error('noFarmacologicos.*.titulo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</section>

{{-- ========== Antecedentes Ginecoobstétricos (Oculto por defecto) ========== --}}
@php $gineco = old('gineco', []); @endphp
<section id="seccion-gineco" class="{{ $sexo === 'F' ? '' : 'hidden' }} bg-rose-50 rounded-xl border border-rose-200 shadow-sm p-6 mb-6">
    <h3 class="text-lg font-semibold text-rose-900 mb-4">Antecedentes Ginecoobstétricos</h3>

    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div>
            <span class="{{ $label }}">¿Embarazo actual?</span>
            <select name="gineco[embarazoActual]" id="select-embarazo" class="{{ $input }}">
                <option value="0" {{ ($gineco['embarazoActual'] ?? '0') === '0' ? 'selected' : '' }}>No</option>
                <option value="1" {{ ($gineco['embarazoActual'] ?? '0') === '1' ? 'selected' : '' }}>Sí</option>
            </select>
        </div>
        <div>
            <label class="{{ $label }}">Semanas de gestación</label>
            <input type="number" name="gineco[semanasGestacion]" id="input-semanas" value="{{ $gineco['semanasGestacion'] ?? '' }}" min="0" max="42" class="{{ $input }} disabled:bg-slate-100 disabled:cursor-not-allowed" disabled>
        </div>
        <div>
            <span class="{{ $label }}">¿Menstruación activa?</span>
            <select name="gineco[menstruacion]" class="{{ $input }}">
                <option value="0" {{ ($gineco['menstruacion'] ?? '0') === '0' ? 'selected' : '' }}>No</option>
                <option value="1" {{ ($gineco['menstruacion'] ?? '0') === '1' ? 'selected' : '' }}>Sí</option>
            </select>
        </div>
        <div>
            <span class="{{ $label }}">¿Lactancia activa?</span>
            <select name="gineco[lactancia]" class="{{ $input }}">
                <option value="0" {{ ($gineco['lactancia'] ?? '0') === '0' ? 'selected' : '' }}>No</option>
                <option value="1" {{ ($gineco['lactancia'] ?? '0') === '1' ? 'selected' : '' }}>Sí</option>
            </select>
        </div>
        <div class="sm:col-span-4">
            <label class="{{ $label }}">Observaciones adicionales</label>
            <textarea name="gineco[observaciones]" rows="2" class="{{ $input }}">{{ $gineco['observaciones'] ?? '' }}</textarea>
        </div>
    </div>
</section>

{{-- ========== Triage / Visita Inicial (Opcional) ========== --}}
@php $triage = old('triage', []); @endphp
<section id="sec-triage" class="bg-blue-50 rounded-xl border border-blue-200 shadow-sm p-6 mb-6">
    <h3 class="text-lg font-semibold text-blue-900 mb-1">Triage / Signos Vitales (Visita Inicial)</h3>
    <p class="text-sm text-blue-700 mb-4">Llenar estos datos creará automáticamente la primera visita médica del paciente.</p>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div>
            <label class="{{ $label }}">Peso (kg)</label>
            <input type="number" step="0.01" min="0.5" max="400" name="triage[peso]" id="peso" value="{{ $triage['peso'] ?? '' }}" class="{{ $input }}">
        </div>
        <div>
            <label class="{{ $label }}">Talla (metros)</label>
            <input type="number" step="0.01" min="0.3" max="2.5" name="triage[talla]" id="talla" value="{{ $triage['talla'] ?? '' }}" placeholder="Ej. 1.75" class="{{ $input }}">
        </div>
        <div>
            <label class="{{ $label }}">IMC</label>
            <input type="text" name="triage[imc]" id="imc" value="{{ $triage['imc'] ?? '' }}" readonly tabindex="-1" class="{{ $input }} bg-slate-100 text-slate-500 font-semibold">
        </div>
        <div>
            <label class="{{ $label }}">Sat. Oxígeno (%)</label>
            <input type="number" step="0.1" min="0" max="100" name="triage[saturacionOxigeno]" value="{{ $triage['saturacionOxigeno'] ?? '' }}" class="{{ $input }}">
        </div>
        <div>
            <label class="{{ $label }}">Presión Sistólica</label>
            <input type="number" min="0" max="300" name="triage[presionSistolica]" value="{{ $triage['presionSistolica'] ?? '' }}" placeholder="Ej. 120" class="{{ $input }}">
        </div>
        <div>
            <label class="{{ $label }}">Presión Diastólica</label>
            <input type="number" min="0" max="200" name="triage[presionDiastolica]" value="{{ $triage['presionDiastolica'] ?? '' }}" placeholder="Ej. 80" class="{{ $input }}">
        </div>
        <div>
            <label class="{{ $label }}">Pulso (lpm)</label>
            <input type="number" min="0" max="300" name="triage[pulsoPpm]" value="{{ $triage['pulsoPpm'] ?? '' }}" class="{{ $input }}">
        </div>
        <div>
            <label class="{{ $label }}">Frec. Respiratoria</label>
            <input type="number" min="0" max="100" name="triage[frecuenciaRespiratoria]" value="{{ $triage['frecuenciaRespiratoria'] ?? '' }}" class="{{ $input }}">
        </div>
    </div>
</section>

{{-- ========== Botones ========== --}}
<div class="flex justify-end gap-3">
    <a href="{{ route('expedientes.index') }}"
       class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
        Cancelar
    </a>
    <button type="submit"
            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
        {{ $textoBoton }}
    </button>
</div>

{{-- ========== Plantillas para filas dinámicas ========== --}}
<template id="tpl-telefono">
    <div class="fila flex items-center gap-2">
        <input type="text" name="telefonos[__I__][numero]" placeholder="Número (10 dígitos)" maxlength="14" inputmode="tel" class="{{ $input }}">
        <select name="telefonos[__I__][tipo]" class="{{ $select }} bg-white">
            @foreach ($tipos as $tipo)
                <option value="{{ $tipo }}">{{ $tipo }}</option>
            @endforeach
        </select>
        <button type="button" data-remove=".fila" class="px-2 text-sm text-red-600 hover:text-red-800">Quitar</button>
    </div>
</template>

<template id="tpl-correo">
    <div class="fila flex items-center gap-2">
        <input type="email" name="correos[]" placeholder="correo@ejemplo.com" maxlength="255" class="{{ $input }}">
        <button type="button" data-remove=".fila" class="px-2 text-sm text-red-600 hover:text-red-800">Quitar</button>
    </div>
</template>

<template id="tpl-contacto">
    @include('expedientes._contacto', ['i' => '__I__', 'c' => [], 'input' => $input, 'label' => $label])
</template>

<template id="tpl-contacto-tel">
    <div class="fila flex items-center gap-2">
        <input type="text" name="contactos[__I__][telefonos][__J__][numero]" placeholder="Número (10 dígitos)" maxlength="14" inputmode="tel" class="{{ $input }}">
        <select name="contactos[__I__][telefonos][__J__][tipo]" class="{{ $select }} bg-white">
            @foreach ($tipos as $tipo)
                <option value="{{ $tipo }}">{{ $tipo }}</option>
            @endforeach
        </select>
        <button type="button" data-remove=".fila" class="px-2 text-sm text-red-600 hover:text-red-800">Quitar</button>
    </div>
</template>

<template id="tpl-contacto-correo">
    <div class="fila flex items-center gap-2">
        <input type="email" name="contactos[__I__][correos][]" placeholder="correo@ejemplo.com" maxlength="255" class="{{ $input }}">
        <button type="button" data-remove=".fila" class="px-2 text-sm text-red-600 hover:text-red-800">Quitar</button>
    </div>
</template>

<template id="tpl-antecedente">
    <div class="fila grid grid-cols-1 sm:grid-cols-12 gap-2 items-start border-l-2 border-blue-400 pl-3 py-2">
        <div class="sm:col-span-2">
            <select name="antecedentes[__I__][tipo]" class="ant-tipo {{ $input }} bg-white" required>
                <option value="">Tipo...</option>
                @foreach (config('expediente.tipos_antecedente') as $tipoA)
                    <option value="{{ $tipoA }}">{{ $tipoA }}</option>
                @endforeach
            </select>
        </div>
        <div class="sm:col-span-3 ant-enfermedad-wrap" hidden>
            <select name="antecedentes[__I__][enfermedad]" class="ant-enfermedad {{ $input }} bg-white">
                <option value="">Enfermedad...</option>
                @foreach (config('expediente.enfermedades') as $enf)
                    <option value="{{ $enf }}">{{ $enf }}</option>
                @endforeach
                <option value="Otra">Otra (detallar)</option>
            </select>
        </div>
        <div class="sm:col-span-3 ant-nombre-wrap">
            <input type="text" name="antecedentes[__I__][nombre]" placeholder="Nombre / Título" maxlength="100" class="ant-nombre {{ $input }}">
        </div>
        <div class="sm:col-span-2">
            <input type="text" name="antecedentes[__I__][descripcion]" placeholder="Descripción" class="{{ $input }}">
        </div>
        <div class="sm:col-span-1">
            <label class="inline-flex items-center gap-1 text-xs text-slate-600 mt-2 cursor-pointer">
                <input type="checkbox" name="antecedentes[__I__][esFamiliar]" value="1" class="rounded border-slate-300 text-blue-600">
                Familiar
            </label>
        </div>
        <div class="sm:col-span-1 text-right">
            <button type="button" data-remove=".fila" class="text-sm text-red-600 hover:text-red-800 mt-2">Quitar</button>
        </div>
    </div>
</template>

<template id="tpl-alergia">
    <div class="fila grid grid-cols-1 sm:grid-cols-12 gap-2 items-start border-l-2 border-red-400 pl-3 py-2">
        <div class="sm:col-span-3">
            <input type="text" name="alergias[__I__][sustancia]" placeholder="Sustancia o medicamento" maxlength="150" required class="{{ $input }}">
        </div>
        <div class="sm:col-span-3">
            <input type="text" name="alergias[__I__][reaccion]" placeholder="Reacción presentada" class="{{ $input }}">
        </div>
        <div class="sm:col-span-2">
            <select name="alergias[__I__][severidad]" class="{{ $input }} bg-white">
                @foreach (config('expediente.severidades_alergia') as $sev)
                    <option value="{{ $sev }}">{{ $sev }}</option>
                @endforeach
            </select>
        </div>
        <div class="sm:col-span-3">
            <input type="text" name="alergias[__I__][fechaAproximada]" placeholder="¿Desde cuándo? DD/MM/AAAA" inputmode="numeric" maxlength="10" class="campo-fecha {{ $input }}">
        </div>
        <div class="sm:col-span-1 text-right">
            <button type="button" data-remove=".fila" class="text-sm text-red-600 hover:text-red-800 mt-2">Quitar</button>
        </div>
    </div>
</template>

<template id="tpl-nofarma">
    <div class="fila grid grid-cols-1 sm:grid-cols-12 gap-2 items-start border-l-2 border-emerald-400 pl-3 py-2">
        <div class="sm:col-span-3">
            <select name="noFarmacologicos[__I__][tipo]" class="{{ $input }} bg-white" required>
                <option value="">Tipo...</option>
                @foreach (config('expediente.tipos_no_farmacologico') as $tipoT)
                    <option value="{{ $tipoT }}">{{ $tipoT }}</option>
                @endforeach
            </select>
        </div>
        <div class="sm:col-span-4">
            <input type="text" name="noFarmacologicos[__I__][titulo]" placeholder="Título (Ej. Té de manzanilla)" maxlength="150" class="{{ $input }}">
        </div>
        <div class="sm:col-span-4">
            <input type="text" name="noFarmacologicos[__I__][descripcion]" placeholder="Descripción (frecuencia, para qué lo usa...)" class="{{ $input }}">
        </div>
        <div class="sm:col-span-1 text-right">
            <button type="button" data-remove=".fila" class="text-sm text-red-600 hover:text-red-800 mt-2">Quitar</button>
        </div>
    </div>
</template>

<script>
    (function () {
        let uid = 1000; // índices únicos para filas nuevas

        // ===== Agregar y quitar filas =====
        document.addEventListener('click', function (e) {
            const add = e.target.closest('[data-add]');
            if (add) {
                let html = document.getElementById(add.dataset.add).innerHTML;
                let destino;

                if (add.dataset.scope === 'contacto') {
                    // Fila interna de un contacto: __I__ = índice del contacto, __J__ = índice nuevo del teléfono
                    const contacto = add.closest('[data-contacto]');
                    html = html.replaceAll('__I__', contacto.dataset.contacto).replaceAll('__J__', uid++);
                    destino = contacto.querySelector(add.dataset.target);
                } else {
                    html = html.replaceAll('__I__', uid++).replaceAll('__J__', 0);
                    destino = document.querySelector(add.dataset.target);
                }
                destino.insertAdjacentHTML('beforeend', html);
                return;
            }

            const quitar = e.target.closest('[data-remove]');
            if (quitar) {
                const fila = quitar.closest(quitar.dataset.remove);
                const lista = fila.parentElement;

                // El paciente debe conservar al menos una fila de teléfono
                if (lista.id === 'lista-telefonos' && lista.children.length === 1) {
                    fila.querySelectorAll('input').forEach(function (i) { i.value = ''; });
                    return;
                }
                fila.remove();
            }
        });

        // ===== Teléfonos: solo dígitos (delegado, cubre filas nuevas) =====
        document.addEventListener('input', function (e) {
            if (e.target.matches('input[inputmode="tel"]')) {
                e.target.value = e.target.value.replace(/\D/g, '').slice(0, 10);
            }
        });

        // ===== Fechas DD/MM/AAAA en filas dinámicas (alergias) =====
        document.addEventListener('input', function (e) {
            if (!e.target.classList.contains('campo-fecha')) return;
            const d = e.target.value.replace(/\D/g, '').slice(0, 8);
            let out = d;
            if (d.length > 4)      out = d.slice(0, 2) + '/' + d.slice(2, 4) + '/' + d.slice(4);
            else if (d.length > 2) out = d.slice(0, 2) + '/' + d.slice(2);
            e.target.value = out;
        });

        // ===== Antecedentes: lista de enfermedades vs. título libre =====
        function sincronizarAntecedente(fila) {
            const tipo = fila.querySelector('.ant-tipo').value;
            const enfWrap = fila.querySelector('.ant-enfermedad-wrap');
            const enf = fila.querySelector('.ant-enfermedad');
            const nombreWrap = fila.querySelector('.ant-nombre-wrap');
            const nombre = fila.querySelector('.ant-nombre');

            const esEnfermedad = tipo === 'Enfermedad';
            enfWrap.hidden = !esEnfermedad;
            if (!esEnfermedad) enf.value = '';

            const otra = esEnfermedad && enf.value === 'Otra';
            nombreWrap.hidden = esEnfermedad && !otra;   // la lista pone el nombre; "Otra" lo pide
            nombre.placeholder = otra ? '¿Cuál enfermedad? (detalle)' : 'Nombre / Título';
            if (esEnfermedad && !otra) nombre.value = '';
        }
        document.addEventListener('change', function (e) {
            if (e.target.classList.contains('ant-tipo') || e.target.classList.contains('ant-enfermedad')) {
                sincronizarAntecedente(e.target.closest('.fila'));
            }
        });
        document.querySelectorAll('#lista-antecedentes .fila').forEach(sincronizarAntecedente);

        // ===== Fecha de nacimiento con máscara DD/MM/AAAA =====
        const fecha = document.getElementById('fechaNac');
        fecha.addEventListener('input', function () {
            const d = fecha.value.replace(/\D/g, '').slice(0, 8);
            let out = d;
            if (d.length > 4)      out = d.slice(0, 2) + '/' + d.slice(2, 4) + '/' + d.slice(4);
            else if (d.length > 2) out = d.slice(0, 2) + '/' + d.slice(2);
            fecha.value = out;
        });

        // ===== CURP: mayúsculas, solo alfanuméricos y sin pegar =====
        const curp = document.getElementById('curp');
        curp.addEventListener('input', function () {
            curp.value = curp.value.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 18);
        });
        ['paste', 'drop'].forEach(function (ev) {
            curp.addEventListener(ev, function (e) { e.preventDefault(); });
        });

        // ===== Gineco según sexo (formulario y sidebar) =====
        const seccionGineco = document.getElementById('seccion-gineco');
        const navGineco = document.getElementById('nav-gineco');

        document.querySelectorAll('input[name="sexo"]').forEach(function (radio) {
            radio.addEventListener('change', function () {
                const esF = this.value === 'F';
                if (seccionGineco) seccionGineco.classList.toggle('hidden', !esF);
                if (navGineco) navGineco.classList.toggle('hidden', !esF);
            });
        });

        // ===== Semanas de gestación según embarazo =====
        const selectEmbarazo = document.getElementById('select-embarazo');
        const inputSemanas = document.getElementById('input-semanas');

        if (selectEmbarazo) {
            selectEmbarazo.addEventListener('change', function () {
                const embarazada = this.value === '1';
                inputSemanas.disabled = !embarazada;
                if (embarazada) inputSemanas.focus();
                else inputSemanas.value = '';
            });
            selectEmbarazo.dispatchEvent(new Event('change'));
        }

        // ===== Descripción de drogas =====
        const selectDrogas = document.getElementById('select-drogas');
        const descDrogas = document.getElementById('desc-drogas');

        if (selectDrogas && descDrogas) {
            selectDrogas.addEventListener('change', function () {
                const consume = this.value !== 'Nunca';
                descDrogas.disabled = !consume;
                if (consume) descDrogas.focus();
                else descDrogas.value = '';
            });
        }

        // ===== Cálculo automático del IMC =====
        const inputPeso = document.getElementById('peso');
        const inputTalla = document.getElementById('talla');
        const inputImc = document.getElementById('imc');

        function calcularIMC() {
            const peso = parseFloat(inputPeso.value);
            const talla = parseFloat(inputTalla.value);
            inputImc.value = (peso > 0 && talla > 0) ? (peso / (talla * talla)).toFixed(2) : '';
        }
        inputPeso.addEventListener('input', calcularIMC);
        inputTalla.addEventListener('input', calcularIMC);
    })();
</script>
