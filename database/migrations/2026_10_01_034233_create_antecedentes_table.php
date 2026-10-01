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
        Schema::create('antecedentes', function (Blueprint $table) {
            $table->id('idAntecedente');

            $table->string('folioExpediente', 50);
            $table->foreign('folioExpediente')->references('folioExpediente')->on('expedientes')->onDelete('cascade');

            $table->string('tipo', 50);
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('antecedentes');
    }
};
