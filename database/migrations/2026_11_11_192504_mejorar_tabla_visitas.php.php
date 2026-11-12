<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visitas', function (Blueprint $table) {
            $table->string('folioVisita', 20)->nullable();
            $table->string('tipo', 20)->default('orientacion');
            $table->text('observaciones')->nullable();
            $table->text('motivoCancelacion')->nullable();
            $table->string('nombreVisitante', 150)->nullable();
            $table->string('contactoVisitante', 150)->nullable();
            $table->softDeletes(); // borrado lógico
        });

        Schema::table('datos_antropometricos', function (Blueprint $table) {
            $table->decimal('temperatura', 4, 1)->nullable(); // °C
        });

        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        // 1. Normalizar estados existentes al catálogo nuevo
        DB::statement("UPDATE visitas SET estado = lower(btrim(estado))");
        DB::statement("UPDATE visitas SET estado = 'completada' WHERE estado IN ('realizada', 'completada / realizada')");
        DB::statement("UPDATE visitas SET estado = 'agendada' WHERE estado = 'reagendada'");
        DB::statement("UPDATE visitas SET estado = 'no_asistio' WHERE estado IN ('no asistio', 'no asistió')");
        DB::statement("UPDATE visitas SET estado = 'completada'
                       WHERE estado NOT IN ('completada', 'agendada', 'cancelada', 'no_asistio')");

        // 2. Visitas viejas sin expediente: necesitan al menos un nombre
        DB::statement("UPDATE visitas SET \"nombreVisitante\" = 'Visitante no identificado' WHERE \"folioExpediente\" IS NULL");

        // 3. Folios retroactivos según la fecha de cada visita
        DB::statement('
            UPDATE visitas v SET "folioVisita" = s.folio FROM (
                SELECT "idVisita",
                       \'VIS-\' || to_char(fecha, \'YYYYMMDD\') || \'-\' ||
                       lpad((row_number() OVER (PARTITION BY fecha::date ORDER BY fecha, "idVisita"))::text, 3, \'0\') AS folio
                FROM visitas
            ) s WHERE s."idVisita" = v."idVisita"
        ');

        DB::statement('ALTER TABLE visitas ALTER COLUMN "folioVisita" SET NOT NULL');

        // 4. Índices (RNF-08) y restricciones
        DB::statement('CREATE UNIQUE INDEX visitas_folio_idx ON visitas ("folioVisita")');
        DB::statement('CREATE INDEX visitas_exp_fecha_idx ON visitas ("folioExpediente", fecha)');
        DB::statement('CREATE INDEX visitas_estado_idx ON visitas (estado)');

        DB::statement("ALTER TABLE visitas
            ADD CONSTRAINT visitas_estado_chk CHECK (estado IN ('completada', 'agendada', 'cancelada', 'no_asistio')),
            ADD CONSTRAINT visitas_tipo_chk CHECK (tipo IN ('orientacion', 'seguimiento')),
            ADD CONSTRAINT visitas_identidad_chk CHECK (\"folioExpediente\" IS NOT NULL OR \"nombreVisitante\" IS NOT NULL),
            ADD CONSTRAINT visitas_cancelacion_chk CHECK (estado <> 'cancelada' OR \"motivoCancelacion\" IS NOT NULL)");

        DB::statement('ALTER TABLE datos_antropometricos
            ADD CONSTRAINT triage_temperatura_chk CHECK (temperatura IS NULL OR (temperatura BETWEEN 30 AND 45))');
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE datos_antropometricos DROP CONSTRAINT IF EXISTS triage_temperatura_chk');
            foreach (['estado', 'tipo', 'identidad', 'cancelacion'] as $c) {
                DB::statement("ALTER TABLE visitas DROP CONSTRAINT IF EXISTS visitas_{$c}_chk");
            }
            foreach (['visitas_folio_idx', 'visitas_exp_fecha_idx', 'visitas_estado_idx'] as $i) {
                DB::statement("DROP INDEX IF EXISTS {$i}");
            }
        }

        Schema::table('datos_antropometricos', fn (Blueprint $t) => $t->dropColumn('temperatura'));
        Schema::table('visitas', function (Blueprint $table) {
            $table->dropColumn(['folioVisita', 'tipo', 'observaciones', 'motivoCancelacion',
                'nombreVisitante', 'contactoVisitante', 'deleted_at']);
        });
    }
};
