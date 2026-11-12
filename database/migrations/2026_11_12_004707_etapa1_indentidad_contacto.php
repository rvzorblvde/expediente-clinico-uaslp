<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->string('ocupacion', 100)->nullable();
            $table->string('escolaridad', 20)->nullable();
            $table->string('servicioMedico', 30)->nullable();
        });

        Schema::table('contacto_telefonos', function (Blueprint $table) {
            $table->string('tipo', 10)->nullable();
        });

        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        // --- Normalización de teléfonos: solo dígitos ---
        DB::statement("UPDATE paciente_telefonos SET numero = regexp_replace(numero, '\\D', '', 'g')");
        DB::statement("UPDATE contacto_telefonos SET numero = regexp_replace(numero, '\\D', '', 'g')");

        // --- Tipos de teléfono al catálogo Celular/Fijo ---
        DB::statement("UPDATE paciente_telefonos SET tipo = CASE
                           WHEN tipo ILIKE 'celular' OR tipo ILIKE 'm_vil' THEN 'Celular'
                           WHEN tipo ILIKE 'casa' OR tipo ILIKE 'fijo' OR tipo ILIKE 'trabajo' THEN 'Fijo'
                           ELSE 'Celular' END");
        DB::statement("UPDATE contacto_telefonos SET tipo = 'Celular' WHERE tipo IS NULL");

        DB::statement("ALTER TABLE paciente_telefonos ALTER COLUMN tipo SET NOT NULL");
        DB::statement("ALTER TABLE contacto_telefonos ALTER COLUMN tipo SET NOT NULL");

        // --- Parentesco al catálogo ---
        DB::statement("UPDATE contacto_emergencias SET parentesco = CASE
                           WHEN parentesco ILIKE 'espos%' OR parentesco ILIKE 'pareja' OR parentesco ILIKE 'novi%' THEN 'Pareja'
                           WHEN parentesco ILIKE 'cuidador%' THEN 'Cuidador'
                           WHEN parentesco ILIKE 'amig%' OR parentesco ILIKE 'amistad' THEN 'Amistad'
                           WHEN parentesco ILIKE 'familiar' OR parentesco ILIKE 'padre' OR parentesco ILIKE 'madre'
                             OR parentesco ILIKE 'hij%' OR parentesco ILIKE 'herman%' OR parentesco ILIKE 'abuel%'
                             OR parentesco ILIKE 't_o' OR parentesco ILIKE 't_a' OR parentesco ILIKE 'prim%'
                             OR parentesco ILIKE 'sobrin%' OR parentesco ILIKE 'suegr%' OR parentesco ILIKE 'cu_ad%' THEN 'Familiar'
                           ELSE 'Otro' END");

        // --- Restricciones ---
        DB::statement("ALTER TABLE paciente_telefonos
            ADD CONSTRAINT ptel_tipo_chk CHECK (tipo IN ('Celular', 'Fijo')),
            ADD CONSTRAINT ptel_numero_chk CHECK (numero ~ '^[0-9]{7,15}$')");

        DB::statement("ALTER TABLE contacto_telefonos
            ADD CONSTRAINT ctel_tipo_chk CHECK (tipo IN ('Celular', 'Fijo')),
            ADD CONSTRAINT ctel_numero_chk CHECK (numero ~ '^[0-9]{7,15}$')");

        DB::statement("ALTER TABLE contacto_emergencias
            ADD CONSTRAINT contacto_parentesco_chk CHECK (parentesco IN ('Familiar', 'Pareja', 'Cuidador', 'Amistad', 'Otro'))");

        DB::statement("ALTER TABLE pacientes
            ADD CONSTRAINT paciente_sexo_chk CHECK (sexo IN ('M', 'F', 'X')),
            ADD CONSTRAINT paciente_nacimiento_chk CHECK (\"fechaNac\" <= CURRENT_DATE),
            ADD CONSTRAINT paciente_escolaridad_chk CHECK (escolaridad IS NULL OR escolaridad IN
                ('Sin escolaridad', 'Primaria', 'Secundaria', 'Medio superior', 'Universidad')),
            ADD CONSTRAINT paciente_servicio_chk CHECK (\"servicioMedico\" IS NULL OR \"servicioMedico\" IN
                ('Ninguno', 'IMSS', 'ISSSTE', 'IMSS-Bienestar', 'Particular', 'Otro'))");
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE paciente_telefonos DROP CONSTRAINT IF EXISTS ptel_tipo_chk, DROP CONSTRAINT IF EXISTS ptel_numero_chk');
            DB::statement('ALTER TABLE contacto_telefonos DROP CONSTRAINT IF EXISTS ctel_tipo_chk, DROP CONSTRAINT IF EXISTS ctel_numero_chk');
            DB::statement('ALTER TABLE contacto_emergencias DROP CONSTRAINT IF EXISTS contacto_parentesco_chk');
            DB::statement('ALTER TABLE pacientes DROP CONSTRAINT IF EXISTS paciente_sexo_chk,
                           DROP CONSTRAINT IF EXISTS paciente_nacimiento_chk,
                           DROP CONSTRAINT IF EXISTS paciente_escolaridad_chk,
                           DROP CONSTRAINT IF EXISTS paciente_servicio_chk');
        }

        Schema::table('contacto_telefonos', fn (Blueprint $t) => $t->dropColumn('tipo'));
        Schema::table('pacientes', function (Blueprint $table) {
            $table->dropColumn(['ocupacion', 'escolaridad', 'servicioMedico']);
        });
    }
};
