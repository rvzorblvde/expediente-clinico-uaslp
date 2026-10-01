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
        Schema::create('expedientes', function (Blueprint $table) {
            $table->string('folioExpediente', 50)->primary();           // Llave primaria alfanumérica

            $table->unsignedBigInteger('idPaciente')->unique();              // FK con el paciente
            $table->foreign('idPaciente')->references('idPaciente')->on('pacientes')->onDelete('restrict');

            $table->unsignedBigInteger('idUsuarioCreador')->nullable();
            $table->foreign('idUsuarioCreador')->references('idUsuario')->on('users')->onDelete('restrict');

            $table->string('estado', 20)->default('activo');

            // En lugar de inventar campos de fecha custom, usamos los nativos de Laravel
            // created_at será tu "fechaCreacion" y updated_at será tu "ultimaMod"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expedientes');
    }
};
