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
        Schema::create('habitos', function (Blueprint $table) {
            $table->id('idHabito');

            // UNIQUE asegura la relación 1 a 1
            $table->string('folioExpediente', 50)->unique();
            $table->foreign('folioExpediente')->references('folioExpediente')->on('expedientes')->onDelete('cascade');

            $table->string('frecuenciaTabaquismo', 50)->default('Nunca');
            $table->string('frecuenciaAlcoholismo', 50)->default('Nunca');
            $table->string('frecuenciaDrogas', 50)->default('Nunca');
            $table->string('descripcionDrogas', 255)->nullable();
            $table->integer('horaSueno')->nullable();
            $table->string('frecuenciaActFisica', 50)->default('Nunca');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('habitos');
    }
};
