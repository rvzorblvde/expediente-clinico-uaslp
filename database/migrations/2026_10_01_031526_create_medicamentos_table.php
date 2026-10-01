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
        Schema::create('medicamentos', function (Blueprint $table) {
            $table->id('idMedicamento');
            $table->string('nombre', 150);
            $table->string('principioActivo', 150);

            $table->string('presentacion', 100)->nullable();
            $table->string('concentracion', 100)->nullable();

            $table->boolean('requiereReceta')->default(false);
            $table->boolean('esControlado')->default(false);

            // Auditoría
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicamentos');
    }
};
