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
        Schema::create('visitas', function (Blueprint $table) {
            $table->id('idVisita');

            // Nullable porque puede haber reportes o asesorías de gente sin expediente
            $table->string('folioExpediente', 50)->nullable();
            $table->foreign('folioExpediente')->references('folioExpediente')->on('expedientes')->onDelete('cascade');

            $table->unsignedBigInteger('idUsuario');
            $table->foreign('idUsuario')->references('idUsuario')->on('users')->onDelete('restrict');

            $table->timestamp('fecha')->useCurrent();
            $table->text('motivo');
            $table->string('estado', 20)->default('completada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitas');
    }
};
