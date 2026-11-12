@php
    $r = $reporte ?? null;
    $input = 'w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 text-sm';
    $label = 'block text-sm font-medium text-slate-700 mb-1';
    $labelXs = 'block text-xs font-medium text-slate-700 mb-1';
    $err = 'text-red-500 text-xs mt-1';
    $fmt = fn ($f) => $f ? \Carbon\Carbon::parse($f)->format('d/m/Y') : '';

    $filaVacia = ['sintoma_reaccion' => '', 'fecha_inicio' => '', 'fecha_fin' => '', 'duracion' => '', 'resultado_reaccion' => '', 'es_grave' => 0];
    $filasReacciones = old('reacciones') ?? ($r?->reacciones?->map(fn ($x) => [
        'sintoma_reaccion'   => $x->sintoma_reaccion,
        'fecha_inicio'       => $fmt($x->fecha_inicio),
        'fecha_fin'          => $fmt($x->fecha_fin),
        'duracion'           => $x->duracion,
        'resultado_reaccion' => $x->resultado_reaccion,
        'es_grave'           => $x->es_grave ? 1 : 0,
    ])->values()->all() ?: [$filaVacia]) ?: [$filaVacia];

    $concVacia = ['nombre_medicamento' => '', 'dosis' => '', 'frecuencia' => '', 'fecha_inicio' => ''];
    $filasConcomitantes = old('concomitantes') ?? ($r?->medicamentosConcomitantes?->map(fn ($x) => [
        'nombre_medicamento' => $x->nombre_medicamento,
        'dosis'              => $x->dosis,
        'frecuencia'         => $x->frecuencia,
        'fecha_inicio'       => $fmt($x->fecha_inicio),
    ])->values()->all() ?: [$concVacia]) ?: [$concVacia];
@endphp

{{-- Seccion 1: Consumidor del medicamento --}}
<div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
    <h3 class="text-lg font-semibold text-slate-900 border-b pb-2">1. Consumidor del medicamento</h3>

    @if (is_null($r))
        <div>
            <label class="{{ $label }}">¿El paciente está registrado en el sistema? <span class="text-red-500">*</span></label>
            <div class="flex items-center gap-6 mb-3">
                <label class="inline-flex items-center text-sm cursor-pointer">
                    <input type="radio" name="identidad" value="registrado" id="id-registrado"
                           @checked(old('identidad', old('folioExpediente') ? 'registrado' : 'externo') === 'registrado') class="h-4 w-4 text-blue-600">
                    <span class="ml-2">Sí, buscar su expediente</span>
                </label>
                <label class="inline-flex items-center text-sm cursor-pointer">
                    <input type="radio" name="identidad" value="externo" id="id-externo"
                           @checked(old('identidad', old('folioExpediente') ? 'registrado' : 'externo') === 'externo') class="h-4 w-4 text-blue-600">
                    <span class="ml-2">No, capturar manualmente</span>
                </label>
            </div>

            <div id="grupo-expediente" hidden>
                <select name="folioExpediente" id="fv-expediente" class="{{ $input }} bg-white">
                    <option value="">Selecciona un expediente...</option>
                    @foreach ($expedientes as $exp)
                        @php
                            $p = $exp->paciente;
                            $iniciales = mb_strtoupper(collect(preg_split('/\s+/u', trim($p?->nombres ?? '')))->take(2)
                                ->map(fn ($w) => mb_substr($w, 0, 1))
                                ->push(mb_substr($p?->apellido1 ?? '', 0, 1))
                                ->push(mb_substr($p?->apellido2 ?? '', 0, 1))
                                ->filter()->implode(''));
                            $nacimiento = $p?->fechaNac ? \Carbon\Carbon::parse($p->fechaNac)->format('d/m/Y') : '';
                            $edad = $p?->fechaNac ? \Carbon\Carbon::parse($p->fechaNac)->age : '';
                            $pesoUltimo = $exp->visitas->sortByDesc('fecha')->pluck('datosAntropometricos')
                                ->filter()->pluck('peso')->filter()->first();
                        @endphp
                        <option value="{{ $exp->folioExpediente }}"
                                data-iniciales="{{ $iniciales }}" data-sexo="{{ $p?->sexo }}"
                                data-nacimiento="{{ $nacimiento }}" data-edad="{{ $edad }}" data-peso="{{ $pesoUltimo }}"
                            @selected(old('folioExpediente') === $exp->folioExpediente)>
                            {{ $exp->folioExpediente }} — {{ $p?->nombres }} {{ $p?->apellido1 }} {{ $p?->apellido2 }}
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-slate-400 mt-1">Se llenan iniciales, sexo, fecha de nacimiento, edad y último peso registrado. Edad y peso quedan editables.</p>
                @error('folioExpediente') <p class="{{ $err }}">{{ $message }}</p> @enderror
            </div>
        </div>
    @elseif ($r->folioExpediente)
        <p class="text-sm text-slate-600 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2">
            Reporte vinculado al expediente <span class="font-medium text-slate-800">{{ $r->folioExpediente }}</span>. El vínculo no se modifica al editar.
        </p>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="{{ $label }}">Iniciales <span class="text-red-500">*</span></label>
            <input type="text" name="iniciales" value="{{ old('iniciales', $r?->iniciales) }}" maxlength="10"
                   pattern="[A-Za-zÁÉÍÓÚÑáéíóúñ]{2,10}" title="Solo letras, de 2 a 10" class="{{ $input }}" placeholder="Ej. JCPG">
            <p class="text-xs text-slate-400 mt-1">Solo letras, sin espacios. Ej: dos nombres y dos apellidos.</p>
            @error('iniciales') <p class="{{ $err }}">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="{{ $label }}">Sexo biológico <span class="text-red-500">*</span></label>
            <div class="flex items-center gap-4 mt-2">
                @foreach (\App\Http\Requests\FarmacovigilanciaRequest::SEXOS as $sexo)
                    <label class="inline-flex items-center text-sm cursor-pointer">
                        <input type="radio" name="sexo_biologico" value="{{ $sexo }}"
                               @checked(old('sexo_biologico', $r?->sexo_biologico) === $sexo) class="h-4 w-4 text-blue-600">
                        <span class="ml-2">{{ $sexo }}</span>
                    </label>
                @endforeach
            </div>
            @error('sexo_biologico') <p class="{{ $err }}">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <label class="{{ $label }}">Peso (kg)</label>
            <input type="number" step="0.1" min="0.5" max="400" name="peso_kg" value="{{ old('peso_kg', $r?->peso_kg) }}"
                   class="{{ $input }}" placeholder="Ej. 70.5">
            @error('peso_kg') <p class="{{ $err }}">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="{{ $label }}">Fecha de nacimiento</label>
            <input type="text" inputmode="numeric" maxlength="10" name="fecha_nacimiento_paciente"
                   value="{{ old('fecha_nacimiento_paciente', $fmt($r?->fecha_nacimiento_paciente)) }}"
                   placeholder="DD/MM/AAAA" pattern="\d{2}/\d{2}/\d{4}" title="Formato DD/MM/AAAA" class="campo-fecha {{ $input }}">
            <p class="error-fecha {{ $err }}" hidden>Fecha inválida. Usa el formato DD/MM/AAAA.</p>
            @error('fecha_nacimiento_paciente') <p class="{{ $err }}">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="{{ $label }}">Edad al comienzo de la reacción (años)</label>
            <input type="number" min="0" max="120" step="1" name="edad_comienzo_reaccion"
                   value="{{ old('edad_comienzo_reaccion', $r?->edad_comienzo_reaccion) }}" class="{{ $input }}" placeholder="Ej. 24">
            @error('edad_comienzo_reaccion') <p class="{{ $err }}">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label class="{{ $label }}">País donde ocurrió la reacción</label>
        <select name="pais_ocurrencia" class="{{ $input }} bg-white">
            <option value="México" selected>México</option>
        </select>
    </div>
</div>

{{-- Seccion 2: Describa lo sucedido --}}
<div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
    <h3 class="text-lg font-semibold text-slate-900 border-b pb-2">2. Describa lo sucedido</h3>
    <div>
        <label class="{{ $label }}">Descripción de lo sucedido <span class="text-red-500">*</span></label>
        <textarea name="descripcion_reaccion" rows="4" required minlength="5" maxlength="5000" class="{{ $input }}"
                  placeholder="Describa con sus propias palabras los signos y síntomas sospechados...">{{ old('descripcion_reaccion', $r?->descripcion_reaccion) }}</textarea>
        @error('descripcion_reaccion') <p class="{{ $err }}">{{ $message }}</p> @enderror
    </div>
</div>

{{-- Seccion 3: Reacciones --}}
<div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
    <div class="flex items-center justify-between border-b pb-2">
        <h3 class="text-lg font-semibold text-slate-900">3. Reacción(es) / Síntoma(s) / Malestar(es)</h3>
        <button type="button" id="btn-agregar-reaccion"
                class="px-3 py-1.5 text-sm font-medium text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
            + Agregar reacción
        </button>
    </div>
    <p class="text-xs text-slate-500">Máximo 20 por reporte.</p>

    <div id="lista-reacciones" class="space-y-4">
        @foreach ($filasReacciones as $i => $fila)
            <div class="fila p-4 bg-slate-50 rounded-lg border border-slate-200 space-y-4">
                <div>
                    <label class="{{ $labelXs }}">Reacción (síntoma, signo o malestar) <span class="text-red-500">*</span></label>
                    <input type="text" name="reacciones[{{ $i }}][sintoma_reaccion]" value="{{ $fila['sintoma_reaccion'] }}"
                           required minlength="2" maxlength="255" class="{{ $input }}" placeholder="Ej. Somnolencia">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="{{ $labelXs }}">Fecha de inicio</label>
                        <input type="text" inputmode="numeric" maxlength="10" name="reacciones[{{ $i }}][fecha_inicio]"
                               value="{{ $fila['fecha_inicio'] }}" placeholder="DD/MM/AAAA"
                               pattern="\d{2}/\d{2}/\d{4}" title="Formato DD/MM/AAAA" class="campo-fecha {{ $input }}">
                        <p class="error-fecha {{ $err }}" hidden>Fecha inválida. Usa el formato DD/MM/AAAA.</p>
                    </div>
                    <div>
                        <label class="{{ $labelXs }}">Fecha de finalización</label>
                        <input type="text" inputmode="numeric" maxlength="10" name="reacciones[{{ $i }}][fecha_fin]"
                               value="{{ $fila['fecha_fin'] }}" placeholder="DD/MM/AAAA"
                               pattern="\d{2}/\d{2}/\d{4}" title="Formato DD/MM/AAAA" class="campo-fecha {{ $input }}">
                        <p class="error-fecha {{ $err }}" hidden>Fecha inválida. Usa el formato DD/MM/AAAA.</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="{{ $labelXs }}">Duración de la reacción</label>
                        <input type="text" name="reacciones[{{ $i }}][duracion]" value="{{ $fila['duracion'] }}"
                               maxlength="100" class="{{ $input }}" placeholder="Ej. 2 días">
                    </div>
                    <div>
                        <label class="{{ $labelXs }}">Resultado de la reacción</label>
                        <select name="reacciones[{{ $i }}][resultado_reaccion]" class="{{ $input }} bg-white">
                            <option value="">Seleccione una opción...</option>
                            @foreach (\App\Http\Requests\FarmacovigilanciaRequest::RESULTADOS as $res)
                                <option value="{{ $res }}" @selected($fila['resultado_reaccion'] === $res)>{{ $res }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex items-center justify-between pt-2">
                    <div>
                        <label class="{{ $labelXs }}">¿El evento fue grave?</label>
                        <div class="flex items-center gap-4">
                            <label class="inline-flex items-center text-sm cursor-pointer">
                                <input type="radio" name="reacciones[{{ $i }}][es_grave]" value="1" @checked($fila['es_grave']) class="h-4 w-4 text-blue-600">
                                <span class="ml-2">Sí</span>
                            </label>
                            <label class="inline-flex items-center text-sm cursor-pointer">
                                <input type="radio" name="reacciones[{{ $i }}][es_grave]" value="0" @checked(!$fila['es_grave']) class="h-4 w-4 text-blue-600">
                                <span class="ml-2">No</span>
                            </label>
                        </div>
                    </div>
                    <button type="button" class="btn-quitar px-3 py-1.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 text-xs font-medium">Quitar</button>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- Seccion 4: Medicamento sospechoso --}}
<div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
    <h3 class="text-lg font-semibold text-slate-900 border-b pb-2">4. Medicamento Sospechoso</h3>

    <div>
        <div class="mb-4">
            <label class="{{ $label }}">Medicamento del catálogo (opcional)</label>
            <select name="medicamento_id" id="fv-medicamento" class="{{ $input }} bg-white">
                <option value="">No está en el catálogo — capturar manualmente</option>
                @foreach ($medicamentos as $m)
                    <option value="{{ $m->idMedicamento }}"
                            data-nombre="{{ trim("{$m->nombre} " . ($m->concentracion ?? '')) }}"
                            data-laboratorio="{{ $m->laboratorio }}"
                        @selected(old('medicamento_id', $r?->medicamento_id) == $m->idMedicamento)>
                        {{ $m->nombre }}{{ $m->concentracion ? " {$m->concentracion}" : '' }}{{ $m->presentacion ? " · {$m->presentacion}" : '' }}
                    </option>
                @endforeach
            </select>
            @error('medicamento_id') <p class="{{ $err }}">{{ $message }}</p> @enderror
        </div>
        <label class="{{ $label }}">Nombre del medicamento <span class="text-red-500">*</span></label>
        <input type="text" name="medicamento_sospechoso" value="{{ old('medicamento_sospechoso', $r?->medicamento_sospechoso) }}"
               minlength="2" maxlength="255" class="{{ $input }}" placeholder="Nombre completo como en el empaque">
        @error('medicamento_sospechoso') <p class="{{ $err }}">{{ $message }}</p> @enderror
        <label class="inline-flex items-center mt-2 text-sm text-slate-600 cursor-pointer">
            <input type="checkbox" name="es_probable_causante" value="1"
                   @checked(old('es_probable_causante', $r ? $r->es_probable_causante : true)) class="rounded border-slate-300 text-blue-600">
            <span class="ml-2">Probable causante de la reacción</span>
        </label>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <label class="{{ $label }}">Compañía fabricante / distribuidora</label>
            <input type="text" name="fabricante_distribuidora" value="{{ old('fabricante_distribuidora', $r?->fabricante_distribuidora) }}"
                   maxlength="255" class="{{ $input }}">
            @error('fabricante_distribuidora') <p class="{{ $err }}">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="{{ $label }}">Número de lote</label>
            <input type="text" name="lote" value="{{ old('lote', $r?->lote) }}" maxlength="100"
                   pattern="[A-Za-z0-9ÁÉÍÓÚÑáéíóúñ\-/ ]+" title="Solo letras, números, guiones y diagonales"
                   class="{{ $input }}" placeholder="Ej. A123-45">
            @error('lote') <p class="{{ $err }}">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="{{ $label }}">Dosis</label>
            <input type="text" name="dosis" value="{{ old('dosis', $r?->dosis) }}" maxlength="100"
                   class="{{ $input }}" placeholder="Ej. 2 comprimidos de 50 mg c/8h">
            @error('dosis') <p class="{{ $err }}">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="{{ $label }}">Vía de administración</label>
            <select name="via_administracion" class="{{ $input }} bg-white">
                <option value="">Seleccione opción...</option>
                @foreach (\App\Http\Requests\FarmacovigilanciaRequest::VIAS as $via)
                    <option value="{{ $via }}" @selected(old('via_administracion', $r?->via_administracion) === $via)>{{ $via }}</option>
                @endforeach
            </select>
            @error('via_administracion') <p class="{{ $err }}">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="{{ $label }}">Acción tomada con el medicamento</label>
            <select name="accion_tomada" class="{{ $input }} bg-white">
                <option value="">Seleccione opción...</option>
                @foreach (\App\Http\Requests\FarmacovigilanciaRequest::ACCIONES as $accion)
                    <option value="{{ $accion }}" @selected(old('accion_tomada', $r?->accion_tomada) === $accion)>{{ $accion }}</option>
                @endforeach
            </select>
            @error('accion_tomada') <p class="{{ $err }}">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label class="{{ $label }}">Indicación del medicamento</label>
        <input type="text" name="indicacion_medicamento" value="{{ old('indicacion_medicamento', $r?->indicacion_medicamento) }}"
               maxlength="1000" class="{{ $input }}" placeholder="¿Por qué tomó el medicamento? (Ej. dolor de cabeza)">
    </div>
</div>

{{-- Medicamentos concomitantes (RF-06.1) --}}
<div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
    <div class="flex items-center justify-between border-b pb-2">
        <h3 class="text-lg font-semibold text-slate-900">Medicamentos concomitantes</h3>
        <button type="button" id="btn-agregar-concomitante"
                class="px-3 py-1.5 text-sm font-medium text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
            + Agregar medicamento
        </button>
    </div>
    <p class="text-xs text-slate-500">Otros medicamentos que el paciente tomaba al momento de la reacción. Los renglones vacíos se ignoran. Máximo 30.</p>

    <div id="lista-concomitantes" class="space-y-4">
        @foreach ($filasConcomitantes as $i => $fila)
            <div class="fila p-4 bg-slate-50 rounded-lg border border-slate-200">
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                    <div>
                        <label class="{{ $labelXs }}">Nombre del medicamento</label>
                        <input type="text" name="concomitantes[{{ $i }}][nombre_medicamento]" value="{{ $fila['nombre_medicamento'] }}"
                               maxlength="255" class="{{ $input }}" placeholder="Ej. Omeprazol">
                    </div>
                    <div>
                        <label class="{{ $labelXs }}">Dosis</label>
                        <input type="text" name="concomitantes[{{ $i }}][dosis]" value="{{ $fila['dosis'] }}"
                               maxlength="100" class="{{ $input }}" placeholder="Ej. 20 mg">
                    </div>
                    <div>
                        <label class="{{ $labelXs }}">Frecuencia</label>
                        <input type="text" name="concomitantes[{{ $i }}][frecuencia]" value="{{ $fila['frecuencia'] }}"
                               maxlength="100" class="{{ $input }}" placeholder="Ej. Cada 24 horas">
                    </div>
                    <div>
                        <label class="{{ $labelXs }}">Fecha de inicio</label>
                        <div class="flex gap-2 items-start">
                            <div class="flex-1">
                                <input type="text" inputmode="numeric" maxlength="10" name="concomitantes[{{ $i }}][fecha_inicio]"
                                       value="{{ $fila['fecha_inicio'] }}" placeholder="DD/MM/AAAA"
                                       pattern="\d{2}/\d{2}/\d{4}" title="Formato DD/MM/AAAA" class="campo-fecha {{ $input }}">
                                <p class="error-fecha {{ $err }}" hidden>Fecha inválida. Usa el formato DD/MM/AAAA.</p>
                            </div>
                            <button type="button" class="btn-quitar px-2 py-2 text-red-600 text-xs font-medium hover:underline shrink-0">Quitar</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- Seccion 5: Información adicional --}}
<div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
    <h3 class="text-lg font-semibold text-slate-900 border-b pb-2">5. Información adicional</h3>
    <div>
        <label class="{{ $label }}">Enfermedades previas o actuales</label>
        <textarea name="enfermedades_previas" rows="2" maxlength="5000" class="{{ $input }}">{{ old('enfermedades_previas', $r?->enfermedades_previas) }}</textarea>
    </div>
    <div>
        <label class="{{ $label }}">Comentarios adicionales</label>
        <textarea name="comentarios_adicionales" rows="2" maxlength="5000" class="{{ $input }}">{{ old('comentarios_adicionales', $r?->comentarios_adicionales) }}</textarea>
    </div>
</div>

{{-- Seccion 6: Información de contacto (la asigna el servidor) --}}
<div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
    <h3 class="text-lg font-semibold text-slate-900 border-b pb-2 mb-3">6. Información de contacto</h3>
    <p class="text-xs text-slate-500 mb-4">Se asigna automáticamente con tu sesión; no es editable.</p>
    <dl class="grid grid-cols-1 sm:grid-cols-3 gap-x-6 gap-y-4 text-sm">
        <div><dt class="text-xs text-slate-400">Entidad federativa</dt><dd class="text-slate-800 font-medium">San Luis Potosí</dd></div>
        <div><dt class="text-xs text-slate-400">Profesión</dt><dd class="text-slate-800 font-medium">Farmacéutico</dd></div>
        <div><dt class="text-xs text-slate-400">Notificador</dt><dd class="text-slate-800 font-medium">{{ auth()->user()->nombres }} {{ auth()->user()->apellido1 }} {{ auth()->user()->apellido2 }}</dd></div>
        <div><dt class="text-xs text-slate-400">Establecimiento</dt><dd class="text-slate-800 font-medium">Unifarmacia UASLP</dd></div>
        <div><dt class="text-xs text-slate-400">Correo</dt><dd class="text-slate-800 font-medium">farmacovigilancia@unifarmacia.example</dd></div>
        <div><dt class="text-xs text-slate-400">Teléfono</dt><dd class="text-slate-800 font-medium">444 000 0000</dd></div>
    </dl>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const campo = (n) => document.querySelector(`[name="${n}"]`);

        // ===== Fechas DD/MM/AAAA (delegado: cubre filas clonadas) =====
        document.addEventListener('input', (e) => {
            if (!e.target.classList.contains('campo-fecha')) return;
            let v = e.target.value.replace(/\D/g, '').slice(0, 8);
            if (v.length > 4) v = v.slice(0, 2) + '/' + v.slice(2, 4) + '/' + v.slice(4);
            else if (v.length > 2) v = v.slice(0, 2) + '/' + v.slice(2);
            e.target.value = v;
        });
        document.addEventListener('focusout', (e) => {
            if (!e.target.classList.contains('campo-fecha')) return;
            const aviso = e.target.parentElement.querySelector('.error-fecha');
            let mal = false;
            const m = e.target.value.match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
            if (e.target.value && !m) mal = true;
            else if (m) {
                const d = +m[1], mes = +m[2], a = +m[3];
                const f = new Date(a, mes - 1, d);
                mal = a < 1900 || f.getFullYear() !== a || f.getMonth() !== mes - 1 || f.getDate() !== d;
            }
            if (aviso) aviso.hidden = !mal;
            e.target.classList.toggle('border-red-400', mal);
        });

        // ===== Iniciales: solo letras, mayúsculas =====
        const ini = campo('iniciales');
        ini.addEventListener('input', () => {
            ini.value = ini.value.replace(/[^\p{L}]/gu, '').toUpperCase().slice(0, 10);
        });

        // ===== Repetidores =====
        function configurarRepetidor(btnId, listaId, minFilas, maxFilas) {
            const btn = document.getElementById(btnId);
            const lista = document.getElementById(listaId);
            if (!btn || !lista) return;

            btn.addEventListener('click', function () {
                const filas = lista.getElementsByClassName('fila');
                if (filas.length >= maxFilas) return;
                const clone = filas[0].cloneNode(true);
                const nuevoIndice = filas.length;

                clone.querySelectorAll('input, select').forEach((input) => {
                    if (input.type === 'radio') input.checked = (input.value === '0');
                    else input.value = '';
                    input.classList.remove('border-red-400');
                    if (input.name) input.name = input.name.replace(/\[\d+\]/, `[${nuevoIndice}]`);
                });
                clone.querySelectorAll('.error-fecha').forEach((p) => p.hidden = true);
                lista.appendChild(clone);
            });

            lista.addEventListener('click', function (e) {
                if (!e.target.classList.contains('btn-quitar')) return;
                const filas = lista.getElementsByClassName('fila');
                if (filas.length > minFilas) e.target.closest('.fila').remove();
            });
        }
        configurarRepetidor('btn-agregar-reaccion', 'lista-reacciones', 1, 20);
        configurarRepetidor('btn-agregar-concomitante', 'lista-concomitantes', 0, 30);

        // ===== Expediente (solo en crear) =====
        const selExp = document.getElementById('fv-expediente');
        if (selExp) {
            const radioRegistrado = document.getElementById('id-registrado');
            const gExp = document.getElementById('grupo-expediente');
            const mapaSexo = { M: 'Masculino', F: 'Femenino', O: 'Desconocido' };

            function aplicarExpediente() {
                const o = selExp.selectedOptions[0];
                const conExp = !!selExp.value;

                [['iniciales', 'iniciales'], ['fecha_nacimiento_paciente', 'nacimiento']].forEach(([name, dato]) => {
                    const el = campo(name);
                    if (conExp) el.value = o.dataset[dato] || '';
                    el.readOnly = conExp;
                    el.classList.toggle('bg-slate-100', conExp);
                });

                document.querySelectorAll('[name="sexo_biologico"]').forEach((r) => {
                    if (conExp) r.checked = (r.value === (mapaSexo[o.dataset.sexo] || 'Desconocido'));
                    r.dataset.bloqueado = conExp ? '1' : '';
                });

                if (conExp) {
                    if (o.dataset.peso) campo('peso_kg').value = o.dataset.peso;
                    if (o.dataset.edad) campo('edad_comienzo_reaccion').value = o.dataset.edad;
                }
            }

            document.querySelectorAll('[name="sexo_biologico"]').forEach((r) => {
                r.addEventListener('click', (e) => {
                    if (r.dataset.bloqueado) { e.preventDefault(); aplicarExpediente(); }
                });
            });

            function sincronizarIdentidad() {
                const registrado = radioRegistrado.checked;
                gExp.hidden = !registrado;
                selExp.required = registrado;
                if (!registrado) {
                    selExp.value = '';
                    ['iniciales', 'fecha_nacimiento_paciente', 'peso_kg', 'edad_comienzo_reaccion'].forEach((n) => {
                        const el = campo(n);
                        el.value = '';
                        el.readOnly = false;
                        el.classList.remove('bg-slate-100');
                    });
                    document.querySelectorAll('[name="sexo_biologico"]').forEach((r) => {
                        r.checked = false;
                        r.dataset.bloqueado = '';
                    });
                }
                aplicarExpediente();
            }
            document.querySelectorAll('[name="identidad"]').forEach((r) => r.addEventListener('change', sincronizarIdentidad));
            selExp.addEventListener('change', aplicarExpediente);
            sincronizarIdentidad();
        }

        // ===== Catálogo → nombre y fabricante del sospechoso =====
        const selMed = document.getElementById('fv-medicamento');
        function aplicarMedicamento() {
            const o = selMed.selectedOptions[0];
            const conMed = !!selMed.value;
            const nombre = campo('medicamento_sospechoso');
            const fab = campo('fabricante_distribuidora');
            if (conMed) nombre.value = o.dataset.nombre;
            nombre.readOnly = conMed;
            nombre.classList.toggle('bg-slate-100', conMed);
            if (conMed && !fab.value) fab.value = o.dataset.laboratorio || '';
        }
        selMed.addEventListener('change', aplicarMedicamento);
        aplicarMedicamento();

        // ===== Evitar envíos dobles =====
        const form = document.getElementById('form-ram');
        form.addEventListener('submit', () => {
            form.querySelector('button[type=submit]').disabled = true;
        });
    });
</script>
