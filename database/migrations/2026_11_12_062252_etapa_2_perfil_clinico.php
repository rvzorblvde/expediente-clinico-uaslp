<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('antecedentes', function (Blueprint $table) {
            $table->boolean('esFamiliar')->default(false);
        });

        Schema::table('alergias', function (Blueprint $table) {
            $table->date('fechaAproximada')->nullable();
        });

        Schema::table('habitos', function (Blueprint $table) {
            $table->string('frecuenciaCafeina', 50)->default('Nunca'); // té / café (Anexo I)
        });

        Schema::create('tratamientos_no_farmacologicos', function (Blueprint $table) {
            $table->id('idTratamientoNF');
            $table->string('folioExpediente', 50);
            $table->foreign('folioExpediente')->references('folioExpediente')->on('expedientes')->onDelete('cascade');
            $table->string('tipo', 40);
            $table->string('titulo', 150);
            $table->text('descripcion')->nullable();
        });

        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        // Normalizar datos existentes al catálogo
        DB::statement("UPDATE antecedentes SET tipo = 'Enfermedad' WHERE tipo ILIKE 'm_dico' OR tipo ILIKE 'enfermedad'");
        DB::statement("UPDATE antecedentes SET tipo = 'Quirúrgico' WHERE tipo ILIKE 'quir%'");
        DB::statement("UPDATE antecedentes SET tipo = 'Otro' WHERE tipo NOT IN ('Quirúrgico', 'Enfermedad', 'Otro')");
        DB::statement("UPDATE alergias SET severidad = initcap(lower(severidad))");

        DB::statement("ALTER TABLE antecedentes
            ADD CONSTRAINT antecedente_tipo_chk CHECK (tipo IN ('Quirúrgico', 'Enfermedad', 'Otro')),
            ADD CONSTRAINT antecedente_nombre_chk CHECK (char_length(btrim(nombre)) >= 2)");

        DB::statement("ALTER TABLE alergias
            ADD CONSTRAINT alergia_severidad_chk CHECK (severidad IN ('Leve', 'Moderada', 'Severa')),
            ADD CONSTRAINT alergia_fecha_chk CHECK (\"fechaAproximada\" IS NULL OR \"fechaAproximada\" <= CURRENT_DATE)");

        DB::statement("ALTER TABLE habitos
            ADD CONSTRAINT habito_cafeina_chk CHECK (\"frecuenciaCafeina\" IN ('Nunca', 'Rara vez', 'Ocasionalmente', 'Con frecuencia', 'Diario'))");

        DB::statement("ALTER TABLE tratamientos_no_farmacologicos
            ADD CONSTRAINT tnf_tipo_chk CHECK (tipo IN ('Herbolario / remedio casero', 'Homeopático', 'Terapia alternativa', 'Otro')),
            ADD CONSTRAINT tnf_titulo_chk CHECK (char_length(btrim(titulo)) >= 2)");
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE antecedentes DROP CONSTRAINT IF EXISTS antecedente_tipo_chk, DROP CONSTRAINT IF EXISTS antecedente_nombre_chk');
            DB::statement('ALTER TABLE alergias DROP CONSTRAINT IF EXISTS alergia_severidad_chk, DROP CONSTRAINT IF EXISTS alergia_fecha_chk');
            DB::statement('ALTER TABLE habitos DROP CONSTRAINT IF EXISTS habito_cafeina_chk');
        }

        Schema::dropIfExists('tratamientos_no_farmacologicos');
        Schema::table('habitos', fn (Blueprint $t) => $t->dropColumn('frecuenciaCafeina'));
        Schema::table('alergias', fn (Blueprint $t) => $t->dropColumn('fechaAproximada'));
        Schema::table('antecedentes', fn (Blueprint $t) => $t->dropColumn('esFamiliar'));
    }
};
