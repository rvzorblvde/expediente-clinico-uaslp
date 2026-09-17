<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expedientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombres');
            $table->string('apellidos');
            $table->date('fecha_nacimiento');
            $table->enum('sexo', ['hombre', 'mujer', 'otro']);
            $table->string('curp', 18)->unique();
            $table->decimal('peso', 5, 2);
            $table->decimal('altura', 4, 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expedientes');
    }
};
