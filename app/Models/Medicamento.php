<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medicamento extends Model
{
    protected $table = 'medicamentos';
    protected $primaryKey = 'idMedicamento';

    protected $fillable = [
        'nombre',
        'tipo',
        'formaFarmaceutica',
        'principioActivo',
        'laboratorio',
        'presentacion',
        'cantidadEnvase',
        'unidadEnvase',
        'concentracionValor',
        'concentracionUnidad',
        'concentracion',
        'requiereReceta',
        'esControlado'
    ];

    protected $casts = [
        'requiereReceta' => 'boolean',
        'esControlado'   => 'boolean',
        'cantidadEnvase' => 'integer',
        'concentracionValor' => 'decimal:4'
    ];

    public const ACTIVO = 'activo';
    public const INACTIVO = 'inactivo';

    public function estaActivo(): bool
    {
        return $this->estado !== self::INACTIVO;
    }

    /**
     * Medicamentos que se pueden elegir en los formularios (expediente, visitas, farmacovigilancia).
     * $incluir permite conservar los ya asignados aunque hoy estén desactivados (al editar).
     */
    public function scopeDisponibles($query, iterable $incluir = [])
    {
        $incluir = collect($incluir)->filter()->unique()->values()->all();

        return $query->where(function ($q) use ($incluir) {
            $q->where('estado', self::ACTIVO);
            if ($incluir) {
                $q->orWhereIn('idMedicamento', $incluir);
            }
        });
    }

    public static function formatearNumero($n): string
    {
        return rtrim(rtrim(number_format((float) $n, 4, '.', ''), '0'), '.');
    }

    public static function textoPresentacion(string $forma, $cantidad = null, ?string $unidad = null): string
    {
        return ($cantidad && $unidad) ? "{$forma} ({$cantidad} {$unidad})" : $forma;
    }

    public static function textoConcentracion($valor, string $unidad): string
    {
        $v = self::formatearNumero($valor);
        return $unidad === '%' ? "{$v}%" : "{$v} {$unidad}";
    }
}
