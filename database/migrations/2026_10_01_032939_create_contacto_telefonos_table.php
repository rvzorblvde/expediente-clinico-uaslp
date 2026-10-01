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
        Schema::create('contacto_telefonos', function (Blueprint $table) {
            $table->id('idTelefonoContacto');

            $table->unsignedBigInteger('idContacto');
            $table->foreign('idContacto')->references('idContacto')->on('contacto_emergencias')->onDelete('cascade');

            $table->string('numero', 20);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacto_telefonos');
    }
};
