<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('farmacovigilancia', function (Blueprint $table) {
            $table->string('folio', 20)->nullable();
            $table->string('folioExpediente', 50)->nullable();
            $table->foreign('folioExpediente')->references('folioExpediente')->on('expedientes')->nullOnDelete();
            $table->unsignedBigInteger('medicamento_id')->nullable();
            $table->foreign('medicamento_id')->references('idMedicamento')->on('medicamentos')->nullOnDelete();
        });

        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        // Reportes sin expediente son válidos (solo iniciales)
        DB::statement('ALTER TABLE farmacovigilancia ALTER COLUMN paciente_id DROP NOT NULL');

        // Vincular reportes existentes a su expediente vía paciente_id
        DB::statement('UPDATE farmacovigilancia f SET "folioExpediente" = (
                           SELECT e."folioExpediente" FROM expedientes e
                           WHERE e."idPaciente" = f.paciente_id LIMIT 1
                       ) WHERE f.paciente_id IS NOT NULL');

        // Folios retroactivos RAM-YYYYMMDD-### por día de creación
        DB::statement('
            UPDATE farmacovigilancia f SET folio = s.folio FROM (
                SELECT id, \'RAM-\' || to_char(created_at, \'YYYYMMDD\') || \'-\' ||
                       lpad((row_number() OVER (PARTITION BY created_at::date ORDER BY created_at, id))::text, 3, \'0\') AS folio
                FROM farmacovigilancia
            ) s WHERE s.id = f.id
        ');

        DB::statement('ALTER TABLE farmacovigilancia ALTER COLUMN folio SET NOT NULL');
        DB::statement('CREATE UNIQUE INDEX farmacovigilancia_folio_idx ON farmacovigilancia (folio)');
        DB::statement('CREATE INDEX farmacovigilancia_exp_idx ON farmacovigilancia ("folioExpediente")');

        // Rangos lógicos
        DB::statement('UPDATE farmacovigilancia SET peso_kg = NULL
                       WHERE peso_kg IS NOT NULL AND (peso_kg < 0.5 OR peso_kg > 400)');

        // edad_comienzo_reaccion era VARCHAR (se capturaba "24 años"):
        // 1) dejar solo los dígitos, 2) convertir la columna a entero, 3) validar rango
        DB::statement("UPDATE farmacovigilancia
                       SET edad_comienzo_reaccion = NULLIF(regexp_replace(edad_comienzo_reaccion, '\\D', '', 'g'), '')");
        DB::statement('ALTER TABLE farmacovigilancia
                       ALTER COLUMN edad_comienzo_reaccion TYPE integer
                       USING edad_comienzo_reaccion::integer');
        DB::statement('UPDATE farmacovigilancia SET edad_comienzo_reaccion = NULL
                       WHERE edad_comienzo_reaccion < 0 OR edad_comienzo_reaccion > 120');

        DB::statement("ALTER TABLE farmacovigilancia
            ADD CONSTRAINT fv_peso_chk CHECK (peso_kg IS NULL OR (peso_kg BETWEEN 0.5 AND 400)),
            ADD CONSTRAINT fv_edad_chk CHECK (edad_comienzo_reaccion IS NULL OR (edad_comienzo_reaccion BETWEEN 0 AND 120))");

        // Coherencia de fechas en reacciones
        DB::statement('ALTER TABLE reacciones_reporte
            ADD CONSTRAINT reaccion_fechas_chk
            CHECK (fecha_fin IS NULL OR fecha_inicio IS NULL OR fecha_fin >= fecha_inicio)');
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE reacciones_reporte DROP CONSTRAINT IF EXISTS reaccion_fechas_chk');
            DB::statement('ALTER TABLE farmacovigilancia DROP CONSTRAINT IF EXISTS fv_peso_chk, DROP CONSTRAINT IF EXISTS fv_edad_chk');
            DB::statement('DROP INDEX IF EXISTS farmacovigilancia_folio_idx');
            DB::statement('DROP INDEX IF EXISTS farmacovigilancia_exp_idx');
            DB::statement('ALTER TABLE farmacovigilancia
                           ALTER COLUMN edad_comienzo_reaccion TYPE varchar(255)
                           USING edad_comienzo_reaccion::varchar');
        }

        Schema::table('farmacovigilancia', function (Blueprint $table) {
            $table->dropForeign(['folioExpediente']);
            $table->dropForeign(['medicamento_id']);
            $table->dropColumn(['folio', 'folioExpediente', 'medicamento_id']);
        });
    }
};
