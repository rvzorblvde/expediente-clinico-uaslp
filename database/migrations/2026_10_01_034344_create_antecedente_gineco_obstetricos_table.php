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
        Schema::create('antecedentes_gineco_obstetricos', function (Blueprint $table) {
            $table->id('idGinecoObstetrico');

            // UNIQUE asegura la relación 1 a 1
            $table->string('folioExpediente', 50)->unique();
            $table->foreign('folioExpediente')->references('folioExpediente')->on('expedientes')->onDelete('cascade');

            $table->boolean('embarazoActual')->default(false);
            $table->integer('semanasGestacion')->nullable();
            $table->boolean('menstruacion')->default(false);
            $table->boolean('lactancia')->default(false);
            $table->text('observaciones')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('antecedente_gineco_obstetricos');
    }
};
