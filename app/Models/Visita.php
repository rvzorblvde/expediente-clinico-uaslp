<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Visita extends Model
{
    use HasFactory, SoftDeletes;

    public const ESTADOS = [
        'completada' => 'Completada',
        'agendada'   => 'Agendada',
        'cancelada'  => 'Cancelada',
        'no_asistio' => 'No asistió',
    ];

    public const TIPOS = [
        'orientacion' => 'Orientación',
        'seguimiento' => 'Seguimiento',
    ];

    protected $table = 'visitas';
    protected $primaryKey = 'idVisita';
    public $timestamps = false;

    protected $fillable = [
        'folioVisita', 'folioExpediente', 'idUsuario', 'fecha', 'tipo', 'motivo',
        'observaciones', 'estado', 'motivoCancelacion', 'nombreVisitante', 'contactoVisitante',
    ];

    protected $casts = ['fecha' => 'datetime'];

    protected static function booted(): void
    {
        static::creating(function (Visita $visita) {
            // Garantiza fecha y folio aunque quien crea la visita no los pase
            // (seeder, visita inicial del expediente, etc.)
            $visita->fecha ??= now();
            $visita->folioVisita ??= static::generarFolio(Carbon::parse($visita->fecha));
        });
    }

    public function expediente()
    {
        return $this->belongsTo(Expediente::class, 'folioExpediente', 'folioExpediente');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'idUsuario', 'idUsuario');
    }

    public function datosAntropometricos()
    {
        return $this->hasOne(DatosAntropometricos::class, 'idVisita', 'idVisita');
    }

    public function registrosMedicacion()
    {
        return $this->hasMany(RegistroMedicacion::class, 'idVisita', 'idVisita');
    }

    /** Nombre a mostrar: paciente del expediente o visitante externo. */
    public function getNombrePacienteAttribute(): string
    {
        $p = $this->expediente?->paciente;
        if ($p) {
            return trim("{$p->nombres} {$p->apellido1} " . ($p->apellido2 ?? ''));
        }
        return $this->nombreVisitante ?? 'Visitante no identificado';
    }

    /** VIS-YYYYMMDD-### según la fecha de la atención o de la cita. */
    public static function generarFolio(Carbon $fecha): string
    {
        $base = 'VIS-' . $fecha->format('Ymd');
        $ultimo = static::withTrashed()
            ->where('folioVisita', 'like', "{$base}-%")
            ->orderByDesc('folioVisita')
            ->value('folioVisita');

        $n = $ultimo ? ((int) substr($ultimo, -3)) + 1 : 1;

        return sprintf('%s-%03d', $base, $n);
    }
}
