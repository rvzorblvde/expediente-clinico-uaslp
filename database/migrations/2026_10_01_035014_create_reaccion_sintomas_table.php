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
        Schema::create('reporte_reacciones', function (Blueprint $table) {
            $table->id('idReporte');

            $table->unsignedBigInteger('idVisita')->nullable();
            $table->foreign('idVisita')->references('idVisita')->on('visitas')->onDelete('cascade');

            $table->string('inicialesConsumidor', 20);
            $table->char('sexo', 1);
            $table->decimal('pesoKg', 5, 2);
            $table->date('fechaNacimiento')->nullable();
            $table->integer('edadAlInicio')->nullable();
            $table->string('paisOcurrencia', 100)->default('México');

            $table->text('descripcionSucedido');
            $table->text('enfermedadesPrevias')->nullable();
            $table->text('comentariosAdicionales')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reaccion_sintomas');
    }
};
