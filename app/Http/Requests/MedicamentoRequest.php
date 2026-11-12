<?php

namespace App\Http\Requests;

use App\Models\Medicamento;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class MedicamentoRequest extends FormRequest
{
    private const TEXTO = '/^[\pL\pN\s.,\-\/()%+\'&]+$/u';

    public function authorize(): bool
    {
        // Crear: cualquier usuario autenticado. Editar: solo RS y trabajador.
        return $this->route('medicamento') ? Gate::allows('manage-clinical-data') : true;
    }

    protected function prepareForValidation(): void
    {
        $limpiar = fn ($v) => is_string($v) ? (trim(preg_replace('/\s+/u', ' ', $v)) ?: null) : $v;

        $datos = [];
        foreach (['nombre', 'principioActivo', 'laboratorio', 'tipo', 'formaFarmaceutica', 'presentacion', 'concentracion',
                     'concentracionUnidad', 'unidadEnvase', 'concentracionValor', 'cantidadEnvase'] as $campo) {
            if ($this->has($campo)) {
                $datos[$campo] = $limpiar($this->input($campo));
            }
        }

        // Un medicamento controlado siempre requiere receta
        if ($this->boolean('esControlado')) {
            $datos['requiereReceta'] = 1;
        }

        $this->merge($datos);
    }

    public function rules(): array
    {
        $c = config('medicamentos');

        return [
            'nombre'              => ['required', 'string', 'min:2', 'max:150', 'regex:' . self::TEXTO],
            'tipo'        => ['required', Rule::in(array_keys(config('medicamentos.tipos')))],
            'laboratorio' => ['nullable', 'string', 'min:2', 'max:100', 'regex:' . self::TEXTO],
            'principioActivo'     => ['required', 'string', 'min:2', 'max:150', 'regex:' . self::TEXTO],
            'formaFarmaceutica'   => ['nullable', Rule::in([...$c['formas'], 'otra'])],
            'presentacion'        => ['nullable', 'string', 'max:100', 'regex:' . self::TEXTO, 'required_if:formaFarmaceutica,otra'],
            'cantidadEnvase'      => ['nullable', 'integer', 'min:1', 'max:100000'],
            'unidadEnvase'        => ['nullable', Rule::in($c['unidades_envase'])],
            'concentracionValor'  => ['nullable', 'numeric', 'gt:0', 'max:999999', 'decimal:0,4'],
            'concentracionUnidad' => ['nullable', Rule::in([...$c['unidades_concentracion'], 'otra'])],
            'concentracion'       => ['nullable', 'string', 'max:100', 'regex:' . self::TEXTO, 'required_if:concentracionUnidad,otra'],
            'requiereReceta'      => ['required', 'boolean'],
            'esControlado'        => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'required'    => 'El campo :attribute es obligatorio.',
            'required_if' => 'El campo :attribute es obligatorio cuando eliges "Otra".',
            'regex'       => 'El campo :attribute contiene caracteres no permitidos.',
            'min'         => 'El campo :attribute es demasiado corto.',
            'max'         => 'El campo :attribute excede el tamaño máximo permitido.',
            'gt'          => 'El campo :attribute debe ser mayor a 0.',
            'in'          => 'El valor seleccionado en :attribute no es válido.',
            'decimal'     => 'El campo :attribute admite máximo 4 decimales.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre'              => 'nombre',
            'tipo'        => 'tipo',
            'laboratorio' => 'laboratorio',
            'principioActivo'     => 'principio activo',
            'formaFarmaceutica'   => 'forma farmacéutica',
            'presentacion'        => 'presentación',
            'cantidadEnvase'      => 'contenido del envase',
            'unidadEnvase'        => 'unidad del envase',
            'concentracionValor'  => 'valor de concentración',
            'concentracionUnidad' => 'unidad de concentración',
            'concentracion'       => 'concentración',
            'requiereReceta'      => '¿requiere receta?',
            'esControlado'        => '¿es controlado?',
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $in = $this->all();
            $forma = $in['formaFarmaceutica'] ?? null;
            $unidad = $in['concentracionUnidad'] ?? null;
            $formaReal = $forma && $forma !== 'otra';
            $concReal = $unidad && $unidad !== 'otra';

            if ($concReal && blank($in['concentracionValor'] ?? null)) {
                $validator->errors()->add('concentracionValor', 'Indica el valor de la concentración.');
            }
            if (!$unidad && filled($in['concentracionValor'] ?? null)) {
                $validator->errors()->add('concentracionUnidad', 'Selecciona la unidad de la concentración.');
            }
            if (filled($in['cantidadEnvase'] ?? null) xor filled($in['unidadEnvase'] ?? null)) {
                $validator->errors()->add('cantidadEnvase', 'Indica el contenido del envase y su unidad.');
            }
            if (filled($in['cantidadEnvase'] ?? null) && !$formaReal) {
                $validator->errors()->add('formaFarmaceutica', 'Selecciona una forma farmacéutica para indicar el contenido del envase.');
            }
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            // Duplicados (sin distinguir mayúsculas), igual que el índice único de la BD
            $d = $this->construirDatos($in);
            $existe = Medicamento::query()
                ->whereRaw('lower("nombre") = ?', [mb_strtolower($d['nombre'])])
                ->whereRaw('lower("principioActivo") = ?', [mb_strtolower($d['principioActivo'])])
                ->whereRaw('lower(coalesce("presentacion", \'\')) = ?', [mb_strtolower($d['presentacion'] ?? '')])
                ->whereRaw('lower(coalesce("concentracion", \'\')) = ?', [mb_strtolower($d['concentracion'] ?? '')])
                ->whereRaw('lower(coalesce("laboratorio", \'\')) = ?', [mb_strtolower($d['laboratorio'] ?? '')])
                ->when($this->route('medicamento'), fn ($q, $m) => $q->whereKeyNot($m->getKey()))
                ->exists();

            if ($existe) {
                $validator->errors()->add('nombre', 'Ya existe un medicamento con el mismo nombre, principio activo, laboratorio, presentación y concentración.');
            }
        }];
    }

    /** Datos listos para guardar en el modelo. */
    public function datos(): array
    {
        return $this->construirDatos($this->validated());
    }

    private function construirDatos(array $in): array
    {
        $forma = $in['formaFarmaceutica'] ?? null;
        $formaReal = $forma && $forma !== 'otra';
        $unidad = $in['concentracionUnidad'] ?? null;
        $concReal = $unidad && $unidad !== 'otra';
        $controlado = (bool) ($in['esControlado'] ?? false);
        $conEnvase = $formaReal && filled($in['cantidadEnvase'] ?? null);

        return [
            'nombre'              => $in['nombre'],
            'tipo'                => $in['tipo'],
            'laboratorio'         => $in['laboratorio'] ?? null,
            'principioActivo'     => $in['principioActivo'],
            'formaFarmaceutica'   => $formaReal ? $forma : null,
            'cantidadEnvase'      => $conEnvase ? (int) $in['cantidadEnvase'] : null,
            'unidadEnvase'        => $conEnvase ? $in['unidadEnvase'] : null,
            'presentacion'        => $formaReal
                ? Medicamento::textoPresentacion($forma, $conEnvase ? (int) $in['cantidadEnvase'] : null, $in['unidadEnvase'] ?? null)
                : ($forma === 'otra' ? ($in['presentacion'] ?? null) : null),
            'concentracionValor'  => $concReal ? $in['concentracionValor'] : null,
            'concentracionUnidad' => $concReal ? $unidad : null,
            'concentracion'       => $concReal
                ? Medicamento::textoConcentracion($in['concentracionValor'], $unidad)
                : ($unidad === 'otra' ? ($in['concentracion'] ?? null) : null),
            'esControlado'        => $controlado,
            'requiereReceta'      => $controlado || (bool) ($in['requiereReceta'] ?? false),
        ];
    }
}
