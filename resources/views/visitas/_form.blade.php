@php
    $v = $visita ?? null;
    $t = $v?->datosAntropometricos;
    $unidadesDosis = config('medicamentos.unidades_concentracion');
    $medicacionExistente = $v?->registrosMedicacion?->map(function ($r) use ($unidadesDosis) {
        $valor = null;
        $unidad = null;
        if ($r->dosis && preg_match('/^([\d.]+)\s*(.+)$/u', trim($r->dosis), $m) && in_array(trim($m[2]), $unidadesDosis, true)) {
            $valor = $m[1];
            $unidad = trim($m[2]);
        }
        return [
            'idMedicamento'     => $r->idMedicamento,
            'dosisValor'        => $valor,
            'dosisUnidad'       => $unidad,
            'viaAdministracion' => $r->viaAdministracion,
            'frecuenciaUso'     => $r->frecuenciaUso,
            'indicaciones'      => $r->indicaciones,
        ];
    })->values() ?? collect();
    $input = 'w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 text-sm';
    $label = 'block text-sm font-medium text-slate-700 mb-1';
    $err = 'text-red-500 text-xs mt-1';
    $fechaValor = old('fecha', $v?->fecha?->format('Y-m-d\TH:i') ?? now()->format('Y-m-d\TH:i'));
    $estadoActual = old('estado', $v->estado ?? 'completada');
@endphp

@if ($errors->any())
    <div class="mb-5 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-800 text-sm">
        <p class="font-semibold mb-1">Revisa el formulario:</p>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach ($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Estado y fecha --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
    <div>
        <label for="estado" class="{{ $label }}">{{ $v ? 'Estado' : 'Tipo de registro' }} <span class="text-red-500">*</span></label>
        <select id="estado" name="estado" required class="{{ $input }} bg-white">
            @foreach ($estados as $valor => $etiqueta)
                <option value="{{ $valor }}" @selected($estadoActual === $valor)>{{ $etiqueta }}</option>
            @endforeach
        </select>
        @error('estado') <p class="{{ $err }}">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="fecha" class="{{ $label }}">Fecha y hora <span class="text-red-500">*</span></label>
        <input type="datetime-local" id="fecha" name="fecha" value="{{ $fechaValor }}" required class="{{ $input }}">
        @error('fecha') <p class="{{ $err }}">{{ $message }}</p> @enderror
    </div>
</div>

{{-- Motivo de cancelación (solo si el estado es cancelada) --}}
<div id="grupo-cancelacion" class="mb-5" hidden>
    <label for="motivoCancelacion" class="{{ $label }}">Motivo de cancelación <span class="text-red-500">*</span></label>
    <textarea id="motivoCancelacion" name="motivoCancelacion" rows="2" maxlength="1000"
              class="{{ $input }}">{{ old('motivoCancelacion', $v?->motivoCancelacion) }}</textarea>
    @error('motivoCancelacion') <p class="{{ $err }}">{{ $message }}</p> @enderror
</div>

{{-- Tipo y motivo --}}
<div class="mb-5">
    <label class="{{ $label }}">Tipo de visita <span class="text-red-500">*</span></label>
    <div class="flex items-center gap-6">
        @foreach ($tipos as $valor => $etiqueta)
            <label class="inline-flex items-center text-sm cursor-pointer">
                <input type="radio" name="tipo" value="{{ $valor }}" required
                       @checked(old('tipo', $v->tipo ?? 'orientacion') === $valor) class="h-4 w-4 text-blue-600">
                <span class="ml-2">{{ $etiqueta }}</span>
            </label>
        @endforeach
    </div>
    @error('tipo') <p class="{{ $err }}">{{ $message }}</p> @enderror
</div>

<div class="mb-5">
    <label for="motivo" class="{{ $label }}">Motivo / detalle de la visita <span class="text-red-500">*</span></label>
    <textarea id="motivo" name="motivo" rows="3" required minlength="3" maxlength="2000"
              class="{{ $input }}" placeholder="Describa el motivo de la atención...">{{ old('motivo', $v?->motivo) }}</textarea>
    @error('motivo') <p class="{{ $err }}">{{ $message }}</p> @enderror
</div>

<div class="mb-5">
    <label for="observaciones" class="{{ $label }}">Observaciones de la atención</label>
    <textarea id="observaciones" name="observaciones" rows="3" maxlength="5000"
              class="{{ $input }}" placeholder="Seguimiento, recomendaciones dadas al paciente...">{{ old('observaciones', $v?->observaciones) }}</textarea>
    @error('observaciones') <p class="{{ $err }}">{{ $message }}</p> @enderror
</div>

{{-- Triage --}}
<details class="mb-5 rounded-lg border border-slate-200" @if($t) open @endif>
    <summary class="px-4 py-3 text-sm font-semibold text-slate-700 cursor-pointer select-none bg-slate-50 rounded-lg">
        Triage / signos vitales (opcional)
    </summary>
    <div class="p-4 grid grid-cols-2 sm:grid-cols-4 gap-3">
        @php
            $camposTriage = [
                'peso' => ['Peso (kg)', '0.1', 0.5, 400, $t?->peso],
                'talla' => ['Talla (m)', '0.01', 0.3, 2.5, $t?->talla],
                'temperatura' => ['Temperatura (°C)', '0.1', 30, 45, $t?->temperatura],
                'saturacionOxigeno' => ['SpO₂ (%)', '0.1', 0, 100, $t?->saturacionOxigeno],
                'presionSistolica' => ['P. sistólica (mmHg)', '1', 0, 300, $t?->presionSistolica],
                'presionDiastolica' => ['P. diastólica (mmHg)', '1', 0, 200, $t?->presionDiastolica],
                'pulsoPpm' => ['Pulso (lpm)', '1', 0, 300, $t?->pulsoPpm],
                'frecuenciaRespiratoria' => ['F. respiratoria (rpm)', '1', 0, 100, $t?->frecuenciaRespiratoria],
            ];
        @endphp
        @foreach ($camposTriage as $campo => [$etiqueta, $paso, $min, $max, $valor])
            <div>
                <label for="triage-{{ $campo }}" class="text-xs text-slate-500">{{ $etiqueta }}</label>
                <input type="number" step="{{ $paso }}" min="{{ $min }}" max="{{ $max }}"
                       id="triage-{{ $campo }}" name="triage[{{ $campo }}]"
                       value="{{ old("triage.$campo", $valor) }}" class="{{ $input }}">
                @error("triage.$campo") <p class="{{ $err }}">{{ $message }}</p> @enderror
            </div>
        @endforeach
    </div>
</details>

{{-- Historia de medicación --}}
<details class="mb-6 rounded-lg border border-slate-200" @if($v?->registrosMedicacion?->isNotEmpty()) open @endif>
    <summary class="px-4 py-3 text-sm font-semibold text-slate-700 cursor-pointer select-none bg-slate-50 rounded-lg">
        Historia de medicación (opcional)
    </summary>
    <div class="p-4">
        <div id="lista-medicamentos" class="space-y-3"></div>
        <button type="button" id="agregar-medicamento"
                class="mt-3 px-3 py-1.5 text-sm font-medium text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100">
            + Agregar medicamento
        </button>
        @error('medicamentos') <p class="{{ $err }}">{{ $message }}</p> @enderror
        @error('medicamentos.*.idMedicamento') <p class="{{ $err }}">{{ $message }}</p> @enderror
    </div>
</details>

<template id="plantilla-medicamento">
    <div class="p-4 rounded-lg bg-slate-50 border border-slate-200 fila-medicamento space-y-3">
        <div class="flex items-center gap-2">
            <select data-campo="idMedicamento" required class="{{ $input }} bg-white flex-1" aria-label="Medicamento">
                <option value="">Selecciona un medicamento...</option>
                @foreach ($medicamentos as $m)
                    <option value="{{ $m->idMedicamento }}">
                        {{ $m->nombre }}{{ $m->concentracion ? " {$m->concentracion}" : '' }}{{ $m->presentacion ? " · {$m->presentacion}" : '' }}
                    </option>
                @endforeach
            </select>
            <button type="button" class="quitar-medicamento text-red-600 text-sm px-2 hover:underline shrink-0">Quitar</button>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div>
                <label class="text-xs text-slate-500">Dosis</label>
                <input type="number" data-campo="dosisValor" min="0.0001" max="999999" step="0.0001"
                       placeholder="Ej: 500" class="{{ $input }}">
            </div>
            <div>
                <label class="text-xs text-slate-500">Unidad</label>
                <select data-campo="dosisUnidad" class="{{ $input }} bg-white" aria-label="Unidad de dosis">
                    <option value="">—</option>
                    @foreach (config('medicamentos.unidades_concentracion') as $u)
                        <option value="{{ $u }}">{{ $u }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs text-slate-500">Vía de administración</label>
                <select data-campo="viaAdministracion" class="{{ $input }} bg-white" aria-label="Vía de administración">
                    <option value="">—</option>
                    @foreach (config('medicamentos.vias_administracion') as $via)
                        <option value="{{ $via }}">{{ $via }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs text-slate-500">Frecuencia</label>
                <input type="text" data-campo="frecuenciaUso" maxlength="100" placeholder="Ej: Cada 8 horas" class="{{ $input }}">
            </div>
        </div>
            <label class="text-xs text-slate-500">Indicaciones</label>
            <input type="text" data-campo="indicaciones" maxlength="1000" placeholder="Ej: Tomar con alimentos" class="{{ $input }}">
        </div>
    </div>
</template>

<script>
    (() => {
        const $ = (id) => document.getElementById(id);

        // Mostrar motivo de cancelación solo cuando aplica
        const estado = $('estado'), gCancel = $('grupo-cancelacion'), txtCancel = $('motivoCancelacion');
        function sincronizarEstado() {
            const esCancelada = estado.value === 'cancelada';
            gCancel.hidden = !esCancelada;
            txtCancel.required = esCancelada;
            if (!esCancelada) txtCancel.value = '';
        }
        estado.addEventListener('change', sincronizarEstado);
        sincronizarEstado();

        // Repetidor de medicamentos
        const lista = $('lista-medicamentos'), plantilla = $('plantilla-medicamento');
        let indice = 0;

        function agregarFila(datos = {}) {
            const fila = plantilla.content.cloneNode(true).firstElementChild;
            fila.querySelectorAll('[data-campo]').forEach((el) => {
                el.name = `medicamentos[${indice}][${el.dataset.campo}]`;
                if (datos[el.dataset.campo] != null) el.value = datos[el.dataset.campo];
            });
            fila.querySelector('.quitar-medicamento').addEventListener('click', () => fila.remove());
            lista.appendChild(fila);
            indice++;
        }

        $('agregar-medicamento').addEventListener('click', () => agregarFila());

        // Filas existentes al editar
        const existentes = @json($medicacionExistente);
        existentes.forEach(agregarFila);

        // Evitar envíos dobles
        const formulario = document.getElementById('form-visita');
        formulario.addEventListener('submit', () => {
            formulario.querySelector('button[type=submit]').disabled = true;
        });
    })();
</script>
