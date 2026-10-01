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
        Schema::create('contacto_emergencias', function (Blueprint $table) {
            $table->id('idContacto');

            $table->unsignedBigInteger('idPaciente');
            $table->foreign('idPaciente')->references('idPaciente')->on('pacientes')->onDelete('cascade');

            $table->string('nombres', 100);
            $table->string('apellido1', 100);
            $table->string('apellido2', 100)->nullable();
            $table->string('parentesco', 50);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacto_emergencias');
    }
};
