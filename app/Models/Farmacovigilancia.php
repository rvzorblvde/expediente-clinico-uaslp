<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Farmacovigilancia extends Model
{
    protected $table = 'farmacovigilancia';

    protected $fillable = [
        'paciente_id',
        'iniciales',
        'sexo_biologico',
        'peso_kg',
        'fecha_nacimiento_paciente',
        'edad_comienzo_reaccion',
        'pais_ocurrencia',
        'medicamento_sospechoso',
        'es_probable_causante',
        'fabricante_distribuidora',
        'lote',
        'dosis',
        'via_administracion',
        'fecha_inicio_admin',
        'fecha_fin_admin',
        'duracion_admin',
        'indicacion_medicamento',
        'accion_tomada',
        'fecha_inicio_reaccion',
        'fecha_fin_reaccion',
        'duracion_reaccion',
        'descripcion_reaccion',
        'resultado_reaccion',
        'gravedad',
        'es_grave',
        'evolucion',
        'observaciones',
        'enfermedades_previas',
        'comentarios_adicionales',
        'notificador_entidad',
        'notificador_profesion',
        'notificador_nombre',
        'notificador_apellido',
        'notificador_establecimiento',
        'notificador_correo',
        'notificador_telefono',
        'enviar_acuse_correo',
    ];

    // Relación correcta con el modelo Paciente indicando las llaves personalizadas
    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id', 'idPaciente');
    }

    public function medicamentosConcomitantes()
    {
        return $this->hasMany(MedicamentoConcomitante::class);
    }
    
    public function reacciones()
    {
        return $this->hasMany(ReaccionReporte::class);
    }
}