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
        Schema::create('contacto_correos', function (Blueprint $table) {
            $table->id('idCorreoContacto');

            $table->unsignedBigInteger('idContacto');
            $table->foreign('idContacto')->references('idContacto')->on('contacto_emergencias')->onDelete('cascade');

            $table->string('correo', 255);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacto_correos');
    }
};
