<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $rangos = [
        'peso'                   => [0.5, 400],
        'talla'                  => [0.3, 2.5],
        'imc'                    => [1, 200],
        'presionSistolica'       => [0, 300],
        'presionDiastolica'      => [0, 200],
        'pulsoPpm'               => [0, 300],
        'frecuenciaRespiratoria' => [0, 100],
        'saturacionOxigeno'      => [0, 100],
    ];

    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        foreach ($this->rangos as $campo => [$min, $max]) {
            DB::statement("UPDATE datos_antropometricos SET \"{$campo}\" = NULL
                           WHERE \"{$campo}\" IS NOT NULL AND (\"{$campo}\" < {$min} OR \"{$campo}\" > {$max})");
            DB::statement("ALTER TABLE datos_antropometricos
                           ADD CONSTRAINT triage_{$campo}_chk
                           CHECK (\"{$campo}\" IS NULL OR (\"{$campo}\" BETWEEN {$min} AND {$max}))");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }
        foreach (array_keys($this->rangos) as $campo) {
            DB::statement("ALTER TABLE datos_antropometricos DROP CONSTRAINT IF EXISTS triage_{$campo}_chk");
        }
    }
};
