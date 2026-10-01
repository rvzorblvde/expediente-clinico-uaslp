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
        Schema::create('medicamento_reportados', function (Blueprint $table) {
            $table->id('idMedicamentoReportado');

            $table->unsignedBigInteger('idReporte');
            $table->foreign('idReporte')->references('idReporte')->on('reporte_reacciones')->onDelete('cascade');

            $table->string('nombre', 150);
            $table->boolean('esSospechosoCausante')->default(true);
            $table->string('companiaFabricante', 150)->nullable();
            $table->string('numeroLote', 100)->nullable();
            $table->text('dosis');
            $table->string('viaAdministracion', 100);
            $table->date('fechaInicio');
            $table->date('fechaFinalizacion')->nullable();
            $table->text('indicacionMedicamento');
            $table->string('accionLlevadaCabo', 100);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicamento_reportados');
    }
};
