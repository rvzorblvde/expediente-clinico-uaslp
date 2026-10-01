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
        Schema::create('archivo_adjuntos', function (Blueprint $table) {
            $table->id('idArchivo');

            $table->string('folioExpediente', 50);
            $table->foreign('folioExpediente')->references('folioExpediente')->on('expedientes')->onDelete('cascade');

            $table->string('tipoDocumento', 50);
            $table->string('nombreArchivo', 255);
            $table->string('rutaArchivo', 255);
            $table->timestamp('fechaSubida')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archivo_adjuntos');
    }
};
