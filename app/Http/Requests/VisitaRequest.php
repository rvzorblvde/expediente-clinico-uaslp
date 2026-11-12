<?php

namespace App\Http\Requests;

use App\Models\Expediente;
use App\Models\Visita;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class VisitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->route('visita') ? Gate::allows('manage-clinical-data') : true;
    }

    private function esEdicion(): bool
    {
        return (bool) $this->route('visita');
    }

    protected function prepareForValidation(): void
    {
        $limpiar = fn ($v) => is_string($v) ? (trim(preg_replace('/\s+/u', ' ', $v)) ?: null) : $v;

        $campos = ['motivo', 'observaciones', 'motivoCancelacion', 'tipo', 'estado'];
        if (! $this->esEdicion()) {
            $campos = array_merge($campos, ['folioExpediente', 'nombreVisitante', 'contactoVisitante']);
        }

        $datos = [];
        foreach ($campos as $campo) {
            $datos[$campo] = $limpiar($this->input($campo));
        }

        // Si viene expediente, los datos de visitante externo se ignoran (solo al crear)
        if (! $this->esEdicion() && ($datos['folioExpediente'] ?? null)) {
            $datos['nombreVisitante'] = null;
            $datos['contactoVisitante'] = null;
        }

        if ($datos['estado'] !== 'cancelada') {
            $datos['motivoCancelacion'] = null;
        }

        $this->merge($datos);
    }

    public function rules(): array
    {
        $unidadesDosis = config('medicamentos.unidades_concentracion');
        $vias = config('medicamentos.vias_administracion');

        $reglas = [
            'fecha'             => ['required', 'date'],
            'tipo'              => ['required', Rule::in(array_keys(Visita::TIPOS))],
            'motivo'            => ['required', 'string', 'min:3', 'max:2000'],
            'observaciones'     => ['nullable', 'string', 'max:5000'],
            'estado'            => ['required', Rule::in($this->esEdicion()
                ? array_keys(Visita::ESTADOS)
                : ['completada', 'agendada'])],
            'motivoCancelacion' => ['nullable', 'required_if:estado,cancelada', 'string', 'min:3', 'max:1000'],

            // Triage: rangos clínicos razonables, sin negativos
            'triage.peso'                   => ['nullable', 'numeric', 'min:0.5', 'max:400'],
            'triage.talla'                  => ['nullable', 'numeric', 'min:0.3', 'max:2.5'],
            'triage.temperatura'            => ['nullable', 'numeric', 'min:30', 'max:45'],
            'triage.presionSistolica'       => ['nullable', 'integer', 'min:0', 'max:300'],
            'triage.presionDiastolica'      => ['nullable', 'integer', 'min:0', 'max:200'],
            'triage.pulsoPpm'               => ['nullable', 'integer', 'min:0', 'max:300'],
            'triage.frecuenciaRespiratoria' => ['nullable', 'integer', 'min:0', 'max:100'],
            'triage.saturacionOxigeno'      => ['nullable', 'numeric', 'min:0', 'max:100'],

            // Historia de medicación: dosis y vía estructuradas, como el catálogo
            'medicamentos'                     => ['nullable', 'array', 'max:30'],
            'medicamentos.*.idMedicamento'     => ['required', 'integer', 'exists:medicamentos,idMedicamento'],
            'medicamentos.*.dosisValor'        => ['nullable', 'numeric', 'gt:0', 'max:999999', 'decimal:0,4'],
            'medicamentos.*.dosisUnidad'       => ['nullable', Rule::in($unidadesDosis)],
            'medicamentos.*.viaAdministracion' => ['nullable', Rule::in($vias)],
            'medicamentos.*.frecuenciaUso'     => ['nullable', 'string', 'max:100'],
            'medicamentos.*.indicaciones'      => ['nullable', 'string', 'max:1000'],
        ];

        // La identidad del paciente solo se define al crear; al editar no se toca
        if (! $this->esEdicion()) {
            $reglas['folioExpediente']   = ['nullable', 'string', 'max:50', 'exists:expedientes,folioExpediente'];
            $reglas['nombreVisitante']   = ['nullable', 'required_without:folioExpediente', 'string', 'min:2', 'max:150'];
            $reglas['contactoVisitante'] = ['nullable', 'string', 'max:150'];
        }

        return $reglas;
    }

    public function messages(): array
    {
        return [
            'nombreVisitante.required_without'      => 'Indica el nombre del visitante o selecciona un expediente.',
            'motivoCancelacion.required_if'         => 'Indica el motivo de la cancelación.',
            'folioExpediente.exists'                => 'El expediente seleccionado no existe.',
            'medicamentos.*.idMedicamento.exists'   => 'Uno de los medicamentos no existe en el catálogo.',
            'medicamentos.*.idMedicamento.required' => 'Selecciona el medicamento en cada renglón o quita el renglón vacío.',
            'medicamentos.*.dosisUnidad.in'         => 'La unidad de dosis no es válida.',
            'medicamentos.*.viaAdministracion.in'   => 'La vía de administración no es válida.',
        ];
    }

    public function after(): array
    {
        return [function (Validator $v) {
            if ($v->errors()->isNotEmpty()) {
                return;
            }

            $estado = $this->input('estado');
            $fecha = $this->date('fecha');

            if ($estado === 'agendada' && $fecha->lt(now()->subMinutes(5))) {
                $v->errors()->add('fecha', 'Una cita agendada debe tener fecha futura.');
            }
            if (in_array($estado, ['completada', 'no_asistio']) && $fecha->gt(now()->addMinutes(5))) {
                $v->errors()->add('fecha', 'Una visita ' . ($estado === 'completada' ? 'completada' : 'con inasistencia') . ' no puede tener fecha futura.');
            }

            $visita = $this->route('visita');
            if ($visita && $visita->estado === 'completada' && $estado === 'agendada') {
                $v->errors()->add('estado', 'Una visita completada no puede volver a agendarse.');
            }

            if (! $visita && $this->input('folioExpediente')) {
                $exp = Expediente::find($this->input('folioExpediente'));
                if ($exp && $exp->estado !== 'activo') {
                    $v->errors()->add('folioExpediente', 'El expediente está inactivo; reactívalo antes de registrar visitas.');
                }
            }

            // Dosis: valor y unidad van juntos
            foreach ($this->input('medicamentos', []) ?? [] as $i => $m) {
                if (filled($m['dosisValor'] ?? null) xor filled($m['dosisUnidad'] ?? null)) {
                    $v->errors()->add("medicamentos.{$i}.dosisValor", 'Indica el valor y la unidad de la dosis (renglón ' . ($i + 1) . ').');
                }
            }
        }];
    }

    public function datosVisita(): array
    {
        $d = $this->validated();

        $datos = [
            'fecha'             => $d['fecha'],
            'tipo'              => $d['tipo'],
            'motivo'            => $d['motivo'],
            'observaciones'     => $d['observaciones'] ?? null,
            'estado'            => $d['estado'],
            'motivoCancelacion' => $d['motivoCancelacion'] ?? null,
        ];

        if (! $this->esEdicion()) {
            $datos['folioExpediente']   = $d['folioExpediente'] ?? null;
            $datos['nombreVisitante']   = $d['nombreVisitante'] ?? null;
            $datos['contactoVisitante'] = $d['contactoVisitante'] ?? null;
        }

        return $datos;
    }
}
