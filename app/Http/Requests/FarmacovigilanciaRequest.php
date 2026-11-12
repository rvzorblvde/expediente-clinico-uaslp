<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class FarmacovigilanciaRequest extends FormRequest
{
    private const TEXTO = '/^[\pL\pN\s.,\-\/()%+\'&]+$/u';

    public const SEXOS = ['Masculino', 'Femenino', 'Desconocido'];

    public const VIAS = [
        'Intramuscular (inyección en el músculo)', 'Intravenosa (no especificada)', 'Oral',
        'Cutánea', 'Desconocida', 'Nasal', 'Oftálmica', 'Otra', 'Rectal',
        'Respiratoria (inhalación)', 'Subcutánea (inyección bajo la piel)', 'Vaginal',
    ];

    public const ACCIONES = [
        'Medicamento retirado', 'Dosis reducida', 'Dosis aumentada',
        'Dosis no modificada', 'Desconocida', 'No aplica',
    ];

    public const RESULTADOS = [
        'Recuperado/Resuelto', 'En recuperación/En resolución', 'No recuperado/No resuelto',
        'Recuperado con secuelas', 'Muerte', 'Desconocido',
    ];

    public function authorize(): bool
    {
        return $this->route('farmacovigilancia') ? Gate::allows('manage-clinical-data') : true;
    }

    /** Convierte DD/MM/AAAA (formato del formulario) a AAAA-MM-DD (formato de la BD). */
    public static function aISO(?string $fecha): ?string
    {
        return blank($fecha) ? null : Carbon::createFromFormat('d/m/Y', $fecha)->format('Y-m-d');
    }

    protected function prepareForValidation(): void
    {
        $limpiar = fn ($v) => is_string($v) ? (trim(preg_replace('/\s+/u', ' ', $v)) ?: null) : $v;

        $datos = [];
        foreach (['folioExpediente', 'iniciales', 'medicamento_sospechoso', 'fabricante_distribuidora',
                     'lote', 'dosis', 'indicacion_medicamento', 'pais_ocurrencia'] as $c) {
            $datos[$c] = $limpiar($this->input($c));
        }
        if ($datos['iniciales']) {
            $datos['iniciales'] = mb_strtoupper($datos['iniciales']);
        }

        // Renglones vacíos de los repetidores se descartan, no truenan
        $datos['reacciones'] = collect($this->input('reacciones', []))
            ->filter(fn ($r) => filled($r['sintoma_reaccion'] ?? null))->values()->all();
        $datos['concomitantes'] = collect($this->input('concomitantes', []))
            ->filter(fn ($c) => filled($c['nombre_medicamento'] ?? null))->values()->all();

        $this->merge($datos);
    }

    public function rules(): array
    {
        return [
            // Identificación: expediente o datos manuales
            'folioExpediente'            => ['nullable', 'string', 'max:50', 'exists:expedientes,folioExpediente'],
            'iniciales'                  => ['nullable', 'required_without:folioExpediente', 'string', 'min:2', 'max:10', 'regex:/^[\pL]+$/u'],
            'sexo_biologico'             => ['nullable', 'required_without:folioExpediente', Rule::in(self::SEXOS)],
            'fecha_nacimiento_paciente'  => ['nullable', 'date_format:d/m/Y'],
            'peso_kg'                    => ['nullable', 'numeric', 'min:0.5', 'max:400'],
            'edad_comienzo_reaccion'     => ['nullable', 'integer', 'min:0', 'max:120'],
            'pais_ocurrencia'            => ['nullable', 'string', 'max:100'],

            // Medicamento sospechoso
            'medicamento_id'             => ['nullable', 'integer', 'exists:medicamentos,idMedicamento'],
            'medicamento_sospechoso'     => ['nullable', 'required_without:medicamento_id', 'string', 'min:2', 'max:255', 'regex:' . self::TEXTO],
            'fabricante_distribuidora'   => ['nullable', 'string', 'max:255', 'regex:' . self::TEXTO],
            'lote'                       => ['nullable', 'string', 'max:100', 'regex:/^[\pL\pN\-\/ ]+$/u'],
            'dosis'                      => ['nullable', 'string', 'max:100'],
            'via_administracion'         => ['nullable', Rule::in(self::VIAS)],
            'accion_tomada'              => ['nullable', Rule::in(self::ACCIONES)],
            'indicacion_medicamento'     => ['nullable', 'string', 'max:1000'],
            'es_probable_causante'       => ['nullable', 'boolean'],

            // Reacción principal
            'descripcion_reaccion'       => ['required', 'string', 'min:5', 'max:5000'],
            'fecha_inicio_reaccion'      => ['nullable', 'date'],
            'fecha_fin_reaccion'         => ['nullable', 'date', 'after_or_equal:fecha_inicio_reaccion'],
            'duracion_reaccion'          => ['nullable', 'string', 'max:100'],
            'resultado_reaccion'         => ['nullable', Rule::in(self::RESULTADOS)],
            'es_grave'                   => ['nullable', 'boolean'],
            'observaciones'              => ['nullable', 'string', 'max:5000'],
            'enfermedades_previas'       => ['nullable', 'string', 'max:5000'],
            'comentarios_adicionales'    => ['nullable', 'string', 'max:5000'],

            // Repetidores
            'reacciones'                        => ['nullable', 'array', 'max:20'],
            'reacciones.*.sintoma_reaccion'     => ['required', 'string', 'min:2', 'max:255', 'regex:' . self::TEXTO],
            'reacciones.*.fecha_inicio'         => ['nullable', 'date_format:d/m/Y'],
            'reacciones.*.fecha_fin'            => ['nullable', 'date_format:d/m/Y'],
            'reacciones.*.duracion'             => ['nullable', 'string', 'max:100'],
            'reacciones.*.resultado_reaccion'   => ['nullable', Rule::in(self::RESULTADOS)],
            'reacciones.*.es_grave'             => ['nullable', 'boolean'],

            'concomitantes'                      => ['nullable', 'array', 'max:30'],
            'concomitantes.*.nombre_medicamento' => ['required', 'string', 'min:2', 'max:255', 'regex:' . self::TEXTO],
            'concomitantes.*.dosis'              => ['nullable', 'string', 'max:100'],
            'concomitantes.*.frecuencia'         => ['nullable', 'string', 'max:100'],
            'concomitantes.*.fecha_inicio'       => ['nullable', 'date_format:d/m/Y'],
        ];
    }

    public function messages(): array
    {
        return [
            'iniciales.required_without'              => 'Indica las iniciales del paciente o selecciona un expediente.',
            'iniciales.regex'                         => 'Las iniciales solo pueden contener letras.',
            'sexo_biologico.required_without'         => 'Indica el sexo del paciente o selecciona un expediente.',
            'medicamento_sospechoso.required_without' => 'Selecciona un medicamento del catálogo o escribe su nombre.',
            'date_format'                             => 'El campo :attribute debe tener el formato DD/MM/AAAA.',
            'regex'                                   => 'El campo :attribute contiene caracteres no permitidos.',
            'in'                                      => 'El valor seleccionado en :attribute no es válido.',
            'reacciones.max'                          => 'Máximo 20 reacciones por reporte.',
            'concomitantes.max'                       => 'Máximo 30 medicamentos concomitantes.',
            'reacciones.*.sintoma_reaccion.required'  => 'Cada reacción necesita el síntoma; quita los renglones vacíos.',
        ];
    }

    public function attributes(): array
    {
        return [
            'fecha_nacimiento_paciente' => 'fecha de nacimiento',
            'edad_comienzo_reaccion'    => 'edad al comienzo de la reacción',
            'peso_kg'                   => 'peso',
            'medicamento_sospechoso'    => 'nombre del medicamento',
            'reacciones.*.fecha_inicio' => 'fecha de inicio de la reacción',
            'reacciones.*.fecha_fin'    => 'fecha de fin de la reacción',
            'concomitantes.*.fecha_inicio' => 'fecha de inicio del concomitante',
        ];
    }

    public function after(): array
    {
        return [function (Validator $v) {
            if ($v->errors()->isNotEmpty()) {
                return; // los formatos ya fallaron; no intentamos parsear
            }

            $fecha = fn (?string $x) => blank($x) ? null : Carbon::createFromFormat('d/m/Y', $x);

            // Nacimiento: no futuro ni anterior a 1900
            if ($nac = $fecha($this->input('fecha_nacimiento_paciente'))) {
                if ($nac->isAfter(today())) {
                    $v->errors()->add('fecha_nacimiento_paciente', 'La fecha de nacimiento no puede ser futura.');
                } elseif ($nac->year < 1900) {
                    $v->errors()->add('fecha_nacimiento_paciente', 'La fecha de nacimiento no es válida.');
                }
            }

            // Cada reacción: fin >= inicio
            foreach ($this->input('reacciones', []) as $i => $r) {
                $ini = $fecha($r['fecha_inicio'] ?? null);
                $fin = $fecha($r['fecha_fin'] ?? null);
                if ($ini && $fin && $fin->lt($ini)) {
                    $v->errors()->add("reacciones.{$i}.fecha_fin", 'En la reacción ' . ($i + 1) . ', la fecha de fin es anterior a la de inicio.');
                }
            }
        }];
    }
}
