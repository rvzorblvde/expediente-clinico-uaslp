<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        // Limpieza de datos existentes para cumplir las reglas nuevas
        DB::statement('UPDATE antecedentes_gineco_obstetricos SET menstruacion = FALSE
                       WHERE "embarazoActual" = TRUE AND menstruacion = TRUE');
        DB::statement('UPDATE antecedentes_gineco_obstetricos SET "semanasGestacion" = NULL
                       WHERE "embarazoActual" = FALSE');
        DB::statement('UPDATE antecedentes_gineco_obstetricos SET "semanasGestacion" = NULL
                       WHERE "semanasGestacion" IS NOT NULL AND ("semanasGestacion" < 1 OR "semanasGestacion" > 42)');

        DB::statement('ALTER TABLE antecedentes_gineco_obstetricos
            ADD CONSTRAINT gineco_xor_chk CHECK (NOT ("embarazoActual" AND menstruacion)),
            ADD CONSTRAINT gineco_semanas_rango_chk CHECK ("semanasGestacion" IS NULL OR ("semanasGestacion" BETWEEN 1 AND 42)),
            ADD CONSTRAINT gineco_semanas_embarazo_chk CHECK ("embarazoActual" OR "semanasGestacion" IS NULL)');
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE antecedentes_gineco_obstetricos
                DROP CONSTRAINT IF EXISTS gineco_xor_chk,
                DROP CONSTRAINT IF EXISTS gineco_semanas_rango_chk,
                DROP CONSTRAINT IF EXISTS gineco_semanas_embarazo_chk');
        }
    }
};
