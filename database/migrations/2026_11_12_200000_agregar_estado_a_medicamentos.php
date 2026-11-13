<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Los medicamentos ya no se eliminan: se desactivan para conservar el historial
        Schema::table('medicamentos', function (Blueprint $table) {
            $table->string('estado', 10)->default('activo');
            $table->index('estado');
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE medicamentos
                ADD CONSTRAINT medicamentos_estado_chk CHECK (estado IN ('activo', 'inactivo'))");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE medicamentos DROP CONSTRAINT IF EXISTS medicamentos_estado_chk');
        }

        Schema::table('medicamentos', function (Blueprint $table) {
            $table->dropIndex(['estado']);
            $table->dropColumn('estado');
        });
    }
};

