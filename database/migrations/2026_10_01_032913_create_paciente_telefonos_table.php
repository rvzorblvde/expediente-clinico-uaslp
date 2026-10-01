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
        Schema::create('paciente_telefonos', function (Blueprint $table) {
            $table->id('idTelefono');

            $table->unsignedBigInteger('idPaciente');
            $table->foreign('idPaciente')->references('idPaciente')->on('pacientes')->onDelete('cascade');

            $table->string('numero', 20);
            $table->string('tipo', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paciente_telefonos');
    }
};
