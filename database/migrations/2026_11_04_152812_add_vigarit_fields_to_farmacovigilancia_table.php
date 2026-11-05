<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('farmacovigilancia', function (Blueprint $table) {
            // Consumidor / Paciente
            $table->string('iniciales', 10)->nullable();
            $table->string('sexo_biologico', 20)->nullable();
            $table->decimal('peso_kg', 5, 2)->nullable();
            $table->date('fecha_nacimiento_paciente')->nullable();
            $table->string('edad_comienzo_reaccion')->nullable();
            $table->string('pais_ocurrencia')->default('México');

            // Reacción / Malestar
            $table->date('fecha_fin_reaccion')->nullable();
            $table->string('duracion_reaccion')->nullable();
            $table->string('resultado_reaccion')->nullable();
            $table->boolean('es_grave')->default(false);

            // Información de Medicamentos (general)
            $table->boolean('es_probable_causante')->default(true);
            $table->string('fabricante_distribuidora')->nullable();
            $table->string('via_administracion')->nullable();
            $table->date('fecha_inicio_admin')->nullable();
            $table->date('fecha_fin_admin')->nullable();
            $table->string('duracion_admin')->nullable();
            $table->string('indicacion_medicamento')->nullable();
            $table->string('accion_tomada')->nullable();

            // Información Adicional
            $table->text('enfermedades_previas')->nullable();
            $table->text('comentarios_adicionales')->nullable();

            // Información de Contacto / Notificador
            $table->string('notificador_entidad')->default('San Luis Potosí');
            $table->string('notificador_profesion')->nullable();
            $table->string('notificador_nombre')->nullable();
            $table->string('notificador_apellido')->nullable();
            $table->string('notificador_establecimiento')->nullable();
            $table->string('notificador_correo')->nullable();
            $table->string('notificador_telefono')->nullable();
            $table->boolean('enviar_acuse_correo')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('farmacovigilancia', function (Blueprint $table) {
            $table->dropColumn([
                'iniciales', 'sexo_biologico', 'peso_kg', 'fecha_nacimiento_paciente',
                'edad_comienzo_reaccion', 'pais_ocurrencia', 'fecha_fin_reaccion',
                'duracion_reaccion', 'resultado_reaccion', 'es_grave', 'es_probable_causante',
                'fabricante_distribuidora', 'via_administracion', 'fecha_inicio_admin',
                'fecha_fin_admin', 'duracion_admin', 'indicacion_medicamento', 'accion_tomada',
                'enfermedades_previas', 'comentarios_adicionales', 'notificador_entidad',
                'notificador_profesion', 'notificador_nombre', 'notificador_apellido',
                'notificador_establecimiento', 'notificador_correo', 'notificador_telefono',
                'enviar_acuse_correo'
            ]);
        });
    }
};
