<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Tabla principal de Farmacovigilancia (Reportes RAM)
        Schema::create('farmacovigilancia', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('paciente_id'); // Relación con expedientes/pacientes
            $table->string('medicamento_sospechoso');
            $table->string('lote')->nullable();
            $table->string('dosis');
            $table->date('fecha_inicio_reaccion');
            $table->text('descripcion_reaccion');
            $table->enum('gravedad', ['Leve', 'Moderada', 'Grave']);
            $table->enum('evolucion', ['En proceso', 'Resuelto', 'Con secuelas', 'Desconocido'])->default('En proceso');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        // Tabla para medicamentos concomitantes (RF-06.1)
        Schema::create('medicamentos_concomitantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farmacovigilancia_id')->constrained('farmacovigilancia')->onDelete('cascade');
            $table->string('nombre_medicamento');
            $table->string('dosis');
            $table->string('frecuencia');
            $table->date('fecha_inicio')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicamentos_concomitantes');
        Schema::dropIfExists('farmacovigilancia');
    }
};