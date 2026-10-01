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
        Schema::create('registro_medicaciones', function (Blueprint $table) {
            $table->id('idRegistroMedicacion');

            $table->unsignedBigInteger('idVisita');
            $table->foreign('idVisita')->references('idVisita')->on('visitas')->onDelete('cascade');

            $table->unsignedBigInteger('idMedicamento');
            $table->foreign('idMedicamento')->references('idMedicamento')->on('medicamentos')->onDelete('restrict');

            $table->string('dosis', 50)->nullable();
            $table->string('viaAdministracion', 50)->nullable();
            $table->string('frecuenciaUso', 100)->nullable();
            $table->text('indicaciones')->nullable();
            $table->timestamp('fechaRegistro')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registro_medicacions');
    }
};
