<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reacciones_reporte', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farmacovigilancia_id')->constrained('farmacovigilancia')->onDelete('cascade');
            $table->string('sintoma_reaccion'); // Ej. Somnolencia
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->string('duracion')->nullable(); // Ej. "2 Día"
            $table->string('resultado_reaccion')->nullable(); // Recuperado/Resuelto, etc.
            $table->boolean('es_grave')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reacciones_reporte');
    }
};