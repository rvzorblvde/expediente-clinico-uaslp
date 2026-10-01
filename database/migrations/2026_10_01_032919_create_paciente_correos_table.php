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
        Schema::create('paciente_correos', function (Blueprint $table) {
            $table->id('idCorreo');

            $table->unsignedBigInteger('idPaciente');
            $table->foreign('idPaciente')->references('idPaciente')->on('pacientes')->onDelete('cascade');

            $table->string('correo', 255);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paciente_correos');
    }
};
