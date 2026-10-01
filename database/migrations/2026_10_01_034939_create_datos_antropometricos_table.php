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
        Schema::create('datos_antropometricos', function (Blueprint $table) {
            $table->id('idDatosAntropometricos');

            $table->unsignedBigInteger('idVisita')->unique();
            $table->foreign('idVisita')->references('idVisita')->on('visitas')->onDelete('cascade');

            $table->decimal('peso', 5, 2)->nullable();              // Medido en Kg
            $table->decimal('talla', 5, 2)->nullable();             // Medido en cm
            $table->decimal('imc', 4, 2)->nullable();               // Autocalculado
            $table->integer('presionSistolica')->nullable();                    // Primer valor en 80/120
            $table->integer('presionDiastolica')->nullable();                   // Segundo valor en 80/120
            $table->integer('pulsoPpm')->nullable();                            // Medido en ppm
            $table->integer('frecuenciaRespiratoria')->nullable();              // Medido en rpm
            $table->decimal('saturacionOxigeno', 5, 2)->nullable(); // Medido en %
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('datos_antropometricos');
    }
};
