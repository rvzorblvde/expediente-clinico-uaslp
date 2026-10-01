<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pacientes', function (Blueprint $table) {
            // Datos generales
            $table->id('idPaciente');
            $table->string('nombres', 100);
            $table->string('apellido1', 100);
            $table->string('apellido2', 100)->nullable(); // nullable porque no todos tienen dos apellidos
            $table->date('fechaNac');
            $table->string('curp', 18)->unique();
            $table->char('sexo', 1);

            // Residencia
            $table->string('lugarProcedencia', 255)->nullable();
            $table->string('lugarResidencia', 255)->nullable();

            // Auditoria
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};
