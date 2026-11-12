<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medicamentos', function (Blueprint $table) {
            $table->string('tipo', 15)->default('comercial');
            $table->string('laboratorio', 100)->nullable();
        });

        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('UPDATE medicamentos SET tipo = \'generico\' WHERE lower(btrim(nombre)) = lower(btrim("principioActivo"))');

        DB::statement("ALTER TABLE medicamentos
            ADD CONSTRAINT medicamentos_tipo_chk CHECK (tipo IN ('comercial', 'generico')),
            ADD CONSTRAINT medicamentos_laboratorio_chk CHECK (laboratorio IS NULL OR char_length(btrim(laboratorio)) >= 2)");

        DB::statement('DROP INDEX IF EXISTS medicamentos_unico_idx');
        DB::statement('CREATE UNIQUE INDEX medicamentos_unico_idx ON medicamentos
            (lower(nombre), lower("principioActivo"), lower(coalesce(presentacion, \'\')),
             lower(coalesce(concentracion, \'\')), lower(coalesce(laboratorio, \'\')))');
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS medicamentos_unico_idx');
            DB::statement('ALTER TABLE medicamentos DROP CONSTRAINT IF EXISTS medicamentos_tipo_chk, DROP CONSTRAINT IF EXISTS medicamentos_laboratorio_chk');
            DB::statement('CREATE UNIQUE INDEX medicamentos_unico_idx ON medicamentos
                (lower(nombre), lower("principioActivo"), lower(coalesce(presentacion, \'\')), lower(coalesce(concentracion, \'\')))');
        }

        Schema::table('medicamentos', function (Blueprint $table) {
            $table->dropColumn(['tipo', 'laboratorio']);
        });
    }
};
