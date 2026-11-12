<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ExpedienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Crear: todos (RF-01). Modificar: trabajador y RS (RF-03; la ruta también lo exige).
        return $this->route('expediente') ? Gate::allows('manage-clinical-data') : true;
    }

    protected function prepareForValidation(): void
    {
        $soloDigitos = fn ($v) => preg_replace('/\D/', '', (string) $v);

        $this->merge([
            'curp' => strtoupper(trim((string) $this->input('curp'))),

            'telefonos' => collect($this->input('telefonos', []))
                ->map(fn ($t) => ['numero' => $soloDigitos($t['numero'] ?? ''), 'tipo' => $t['tipo'] ?? null])
                ->filter(fn ($t) => $t['numero'] !== '')
                ->values()->all(),

            'correos' => collect($this->input('correos', []))->filter()->values()->all(),

            'contactos' => collect($this->input('contactos', []))
                ->map(function ($c) use ($soloDigitos) {
                    $c['telefonos'] = collect($c['telefonos'] ?? [])
                        ->map(fn ($t) => is_array($t)
                            ? ['numero' => $soloDigitos($t['numero'] ?? ''), 'tipo' => $t['tipo'] ?? null]
                            : ['numero' => $soloDigitos($t), 'tipo' => null]) // tolera la forma vieja
                        ->filter(fn ($t) => $t['numero'] !== '')
                        ->values()->all();
                    $c['correos'] = collect($c['correos'] ?? [])->filter()->values()->all();
                    return $c;
                })
                ->filter(fn ($c) => filled($c['nombres'] ?? null) || filled($c['apellido1'] ?? null)
                    || filled($c['apellido2'] ?? null) || filled($c['parentesco'] ?? null)
                    || $c['telefonos'] || $c['correos'])
                ->values()->all(),

            // Limpieza de datos clínicos
            'antecedentes' => collect($this->input('antecedentes', []))
                ->map(function ($a) {
                    // Si eligió una enfermedad de la lista, esa es el nombre del antecedente
                    if (($a['tipo'] ?? '') === 'Enfermedad' && filled($a['enfermedad'] ?? null) && $a['enfermedad'] !== 'Otra') {
                        $a['nombre'] = $a['enfermedad'];
                    }
                    return $a;
                })
                ->filter(fn ($a) => filled($a['nombre'] ?? null) || filled($a['enfermedad'] ?? null))
                ->values()->all(),

            'noFarmacologicos' => collect($this->input('noFarmacologicos', []))->filter(fn ($t) => filled($t['titulo'] ?? null))->values()->all(),
            'tratamientos' => collect($this->input('tratamientos', []))->filter(fn ($t) => filled($t['idMedicamento'] ?? null))->values()->all(),
            'alergias'         => collect($this->input('alergias', []))->filter(fn ($a) => filled($a['sustancia'] ?? null))->values()->all(),
            'triage'           => collect($this->input('triage', []))->filter(fn ($val) => filled($val))->all(),
        ]);

        // Gineco: para M/X solo existe si se activó explícitamente (caso especial)
        $gineco = $this->input('gineco');
        if (is_array($gineco)) {
            $activo = $this->input('sexo') === 'F' || !empty($gineco['activo']);
            if (! $activo) {
                $gineco = null;
            } else {
                if (($gineco['embarazoActual'] ?? '0') != 1) {
                    $gineco['semanasGestacion'] = null; // sin embarazo no hay semanas
                }
                unset($gineco['activo']);
            }
            $this->merge(['gineco' => $gineco]);
        }
    }

    public function rules(): array
    {
        $frecuencias = 'in:Nunca,Rara vez,Ocasionalmente,Con frecuencia,Diario';
        $tiposTel = Rule::in(config('expediente.tipos_telefono'));
        $pacienteId = $this->route('expediente')?->paciente?->idPaciente;

        return [
            // --- Datos generales ---
            'nombres'          => ['required', 'string', 'min:2', 'max:100', 'regex:/^[\pL\s.\'-]+$/u'],
            'apellido1'        => ['required', 'string', 'min:2', 'max:100', 'regex:/^[\pL\s.\'-]+$/u'],
            'apellido2'        => ['nullable', 'string', 'max:100', 'regex:/^[\pL\s.\'-]+$/u'],
            'fechaNac'         => ['required', 'date_format:d/m/Y', 'before_or_equal:' . now()->format('d/m/Y')],
            'curp'             => [
                'required', 'size:18', 'regex:/^[A-Z]{4}\d{6}[HMX][A-Z]{2}[B-DF-HJ-NP-TV-Z]{3}[A-Z0-9]\d$/',
                Rule::unique('pacientes', 'curp')->ignore($pacienteId, 'idPaciente'),
            ],
            'sexo'             => ['required', 'in:M,F,X'],
            'ocupacion'        => ['nullable', 'string', 'max:100'],
            'escolaridad'      => ['nullable', Rule::in(config('expediente.escolaridades'))],
            'servicioMedico'   => ['nullable', Rule::in(config('expediente.servicios_medicos'))],
            'lugarProcedencia' => ['nullable', 'string', 'max:255'],
            'lugarResidencia'  => ['nullable', 'string', 'max:255'],

            // --- Contacto del paciente ---
            'telefonos'          => ['required', 'array', 'min:1', 'max:5'],
            'telefonos.*.numero' => ['required', 'digits:10'],
            'telefonos.*.tipo'   => ['required', $tiposTel],
            'correos'            => ['nullable', 'array', 'max:5'],
            'correos.*'          => ['email', 'max:255'],

            // --- Contactos de emergencia ---
            'contactos'                      => ['nullable', 'array', 'max:5'],
            'contactos.*.nombres'            => ['required', 'string', 'min:2', 'max:100', 'regex:/^[\pL\s.\'-]+$/u'],
            'contactos.*.apellido1'          => ['required', 'string', 'min:2', 'max:100', 'regex:/^[\pL\s.\'-]+$/u'],
            'contactos.*.apellido2'          => ['nullable', 'string', 'max:100', 'regex:/^[\pL\s.\'-]+$/u'],
            'contactos.*.parentesco'         => ['required', Rule::in(config('expediente.parentescos'))],
            'contactos.*.telefonos'          => ['required', 'array', 'min:1', 'max:3'],
            'contactos.*.telefonos.*.numero' => ['required', 'digits:10'],
            'contactos.*.telefonos.*.tipo'   => ['required', $tiposTel],
            'contactos.*.correos.*'          => ['email', 'max:255'],

            // --- Perfil clínico ---
            'antecedentes'               => ['nullable', 'array', 'max:20'],
            'antecedentes.*.tipo'        => ['required', Rule::in(config('expediente.tipos_antecedente'))],
            'antecedentes.*.enfermedad'  => ['nullable', Rule::in([...config('expediente.enfermedades'), 'Otra'])],
            'antecedentes.*.nombre'      => ['required', 'string', 'min:2', 'max:100'],
            'antecedentes.*.descripcion' => ['nullable', 'string', 'max:2000'],
            'antecedentes.*.esFamiliar'  => ['nullable', 'boolean'],

            'alergias'                   => ['nullable', 'array', 'max:20'],
            'alergias.*.sustancia'       => ['required', 'string', 'min:2', 'max:150'],
            'alergias.*.reaccion'        => ['nullable', 'string', 'max:1000'],
            'alergias.*.severidad'       => ['required', Rule::in(config('expediente.severidades_alergia'))],
            'alergias.*.fechaAproximada' => ['nullable', 'date_format:d/m/Y'],

            'habitos.frecuenciaTabaquismo'  => ['required', $frecuencias],
            'habitos.frecuenciaAlcoholismo' => ['required', $frecuencias],
            'habitos.frecuenciaCafeina'     => ['required', $frecuencias],
            'habitos.frecuenciaActFisica'   => ['required', $frecuencias],
            'habitos.frecuenciaDrogas'      => ['required', $frecuencias],
            'habitos.descripcionDrogas'     => ['nullable', 'string', 'max:255'],
            'habitos.horaSueno'             => ['nullable', 'integer', 'min:0', 'max:24'],

            'noFarmacologicos'               => ['nullable', 'array', 'max:20'],
            'noFarmacologicos.*.tipo'        => ['required', Rule::in(config('expediente.tipos_no_farmacologico'))],
            'noFarmacologicos.*.titulo'      => ['required', 'string', 'min:2', 'max:150'],
            'noFarmacologicos.*.descripcion' => ['nullable', 'string', 'max:2000'],

            'tratamientos'                     => ['nullable', 'array', 'max:20'],
            'tratamientos.*.idMedicamento'     => ['required', 'integer', 'exists:medicamentos,idMedicamento'],
            'tratamientos.*.dosisValor'        => ['nullable', 'numeric', 'gt:0', 'max:999999'],
            'tratamientos.*.dosisUnidad'       => ['nullable', Rule::in(config('medicamentos.unidades_concentracion'))],
            'tratamientos.*.viaAdministracion' => ['nullable', Rule::in(config('medicamentos.vias_administracion'))],
            'tratamientos.*.frecuencia'        => ['nullable', 'string', 'max:100'],
            'tratamientos.*.fechaInicio'       => ['required', 'date_format:d/m/Y'],
            'tratamientos.*.fechaFin'          => ['nullable', 'date_format:d/m/Y'],

            'gineco.embarazoActual'   => ['nullable', 'boolean'],
            'gineco.semanasGestacion' => ['nullable', 'integer', 'min:1', 'max:42', 'required_if:gineco.embarazoActual,1'],
            'gineco.menstruacion'     => ['nullable', 'boolean'],
            'gineco.lactancia'        => ['nullable', 'boolean'],
            'gineco.observaciones'    => ['nullable', 'string'],

            'triage.peso'                   => ['nullable', 'numeric', 'min:0.5', 'max:400'],
            'triage.talla'                  => ['nullable', 'numeric', 'min:0.3', 'max:2.5'],
            'triage.temperatura'            => ['nullable', 'numeric', 'min:30', 'max:45'],
            'triage.saturacionOxigeno'      => ['nullable', 'numeric', 'min:0', 'max:100'],
            'triage.presionSistolica'       => ['nullable', 'integer', 'min:0', 'max:300'],
            'triage.presionDiastolica'      => ['nullable', 'integer', 'min:0', 'max:200'],
            'triage.pulsoPpm'               => ['nullable', 'integer', 'min:0', 'max:300'],
            'triage.frecuenciaRespiratoria' => ['nullable', 'integer', 'min:0', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'curp.regex'             => 'El CURP no tiene un formato válido (18 caracteres, mayúsculas).',
            'curp.unique'            => 'Ya existe un paciente registrado con este CURP.',
            'fechaNac.date_format'   => 'La fecha de nacimiento debe tener el formato DD/MM/AAAA.',
            'digits'                 => 'El campo :attribute debe tener exactamente 10 dígitos.',
            'regex'                  => 'El campo :attribute contiene caracteres no permitidos.',
            'in'                     => 'El valor seleccionado en :attribute no es válido.',
            'telefonos.required'     => 'Registra al menos un teléfono del paciente.',
            'telefonos.*.tipo.required' => 'Indica si el teléfono es celular o fijo.',
            'contactos.*.telefonos.required' => 'Cada contacto de emergencia necesita al menos un teléfono.',
            'antecedentes.*.nombre.required' => 'Detalla el nombre o título del antecedente (en "Otra", escribe cuál enfermedad).',
            'noFarmacologicos.*.titulo.required' => 'Cada tratamiento no farmacológico necesita un título.',
            'gineco.semanasGestacion.required_if' => 'Indica las semanas de gestación (1 a 42).',
            'tratamientos.*.idMedicamento.required' => 'Selecciona el medicamento en cada tratamiento o quita el renglón vacío.',
            'tratamientos.*.fechaInicio.required'   => 'Cada tratamiento necesita fecha de inicio.'
        ];
    }

    public function attributes(): array
    {
        return [
            'fechaNac'                       => 'fecha de nacimiento',
            'telefonos.*.numero'             => 'teléfono del paciente',
            'contactos.*.telefonos.*.numero' => 'teléfono del contacto',
            'contactos.*.parentesco'         => 'parentesco',
        ];
    }

    /** Convierte DD/MM/AAAA a AAAA-MM-DD. */
    public static function aISO(?string $fecha): ?string
    {
        return blank($fecha) ? null : Carbon::createFromFormat('d/m/Y', $fecha)->format('Y-m-d');
    }

    public function after(): array
    {
        return [function (Validator $v) {
            if ($v->errors()->isNotEmpty()) {
                return;
            }

            $curp = $this->input('curp');
            $fecha = Carbon::createFromFormat('d/m/Y', $this->input('fechaNac'));

            // Congruencia CURP ↔ fecha de nacimiento (posiciones 5-10: AAMMDD)
            if (substr($curp, 4, 6) !== $fecha->format('ymd')) {
                $v->errors()->add('curp', 'La fecha dentro del CURP (' . substr($curp, 4, 6) . ') no coincide con la fecha de nacimiento capturada.');
            }

            // Congruencia CURP ↔ sexo (posición 11: H hombre, M mujer, X no binario)
            $sexoCurp = $curp[10];
            $esperado = ['M' => 'H', 'F' => 'M'][$this->input('sexo')] ?? null;
            if ($esperado && $sexoCurp !== 'X' && $sexoCurp !== $esperado) {
                $v->errors()->add('curp', 'El sexo dentro del CURP (' . $sexoCurp . ') no coincide con el sexo seleccionado.');
            }

            // Antecedentes tipo Enfermedad: deben venir de la lista o detallar "Otra"
            foreach ($this->input('antecedentes', []) as $i => $a) {
                if (($a['tipo'] ?? '') === 'Enfermedad' && blank($a['enfermedad'] ?? null)) {
                    $v->errors()->add("antecedentes.{$i}.enfermedad", 'En el antecedente ' . ($i + 1) . ', elige la enfermedad de la lista (u "Otra" para detallarla).');
                }
            }

            // Fecha aproximada de alergias: no futura
            foreach ($this->input('alergias', []) as $i => $al) {
                if (filled($al['fechaAproximada'] ?? null)
                    && Carbon::createFromFormat('d/m/Y', $al['fechaAproximada'])->isAfter(today())) {
                    $v->errors()->add("alergias.{$i}.fechaAproximada", 'La fecha de la alergia ' . ($i + 1) . ' no puede ser futura.');
                }
            }

            // Tratamientos: dosis en pareja y fin >= inicio
            foreach ($this->input('tratamientos', []) as $i => $t) {
                if (filled($t['dosisValor'] ?? null) xor filled($t['dosisUnidad'] ?? null)) {
                    $v->errors()->add("tratamientos.{$i}.dosisValor", 'En el tratamiento ' . ($i + 1) . ', indica el valor y la unidad de la dosis.');
                }
                $ini = filled($t['fechaInicio'] ?? null) ? Carbon::createFromFormat('d/m/Y', $t['fechaInicio']) : null;
                $fin = filled($t['fechaFin'] ?? null) ? Carbon::createFromFormat('d/m/Y', $t['fechaFin']) : null;
                if ($ini && $fin && $fin->lt($ini)) {
                    $v->errors()->add("tratamientos.{$i}.fechaFin", 'En el tratamiento ' . ($i + 1) . ', la fecha de fin es anterior a la de inicio.');
                }
            }

            // Embarazo y menstruación son excluyentes
            $g = $this->input('gineco');
            if (is_array($g) && ($g['embarazoActual'] ?? 0) == 1 && ($g['menstruacion'] ?? 0) == 1) {
                $v->errors()->add('gineco.menstruacion', 'Si hay embarazo actual no puede haber menstruación activa, y viceversa.');
            }
        }];
    }
}
