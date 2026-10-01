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
        Schema::create('auditorias', function (Blueprint $table) {
            $table->id('idLog');

            // Relación con el usuario que hizo el movimiento
            $table->unsignedBigInteger('idUsuario');
            $table->foreign('idUsuario')->references('idUsuario')->on('users')->onDelete('cascade');

            $table->string('accion', 100);
            $table->string('tablaAfectada', 100);

            // Text nullable para guardar el JSON de los datos anteriores y nuevos
            $table->text('detalles')->nullable();

            // Usamos timestamp nativo de BD para la hora exacta del suceso
            $table->timestamp('fechaAccion')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auditorias');
    }
};
