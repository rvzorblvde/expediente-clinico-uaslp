<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tratamientos', function (Blueprint $table) {
            $table->id('idTratamiento');
            $table->string('folioExpediente', 50);
            $table->foreign('folioExpediente')->references('folioExpediente')->on('expedientes')->onDelete('cascade');
            $table->unsignedBigInteger('idMedicamento');
            $table->foreign('idMedicamento')->references('idMedicamento')->on('medicamentos')->onDelete('restrict');

            $table->string('dosis', 50)->nullable();              // compuesta: "500 mg"
            $table->string('viaAdministracion', 50)->nullable();
            $table->string('frecuencia', 100)->nullable();        // intervalo (RF-01)
            $table->date('fechaInicio');
            $table->date('fechaFin')->nullable();
            $table->string('estado', 15)->default('activo');
            $table->timestamps();
        });

        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement("ALTER TABLE tratamientos
            ADD CONSTRAINT tratamiento_estado_chk CHECK (estado IN ('activo', 'suspendido', 'finalizado')),
            ADD CONSTRAINT tratamiento_fechas_chk CHECK (\"fechaFin\" IS NULL OR \"fechaFin\" >= \"fechaInicio\")");
        DB::statement('CREATE INDEX tratamientos_exp_idx ON tratamientos ("folioExpediente")');
    }

    public function down(): void
    {
        Schema::dropIfExists('tratamientos');
    }
};
