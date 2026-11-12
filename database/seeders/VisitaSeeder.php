<?php

namespace Database\Seeders;

use App\Models\DatosAntropometricos;
use App\Models\Expediente;
use App\Models\Medicamento;
use App\Models\User;
use App\Models\Visita;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VisitaSeeder extends Seeder
{
    use WithoutModelEvents; // Sin eventos: folio y fecha se asignan explícitos aquí

    public function run(): void
    {
        $farmaceutico = User::where('idRol', 2)->first() ?? User::first();
        $estudiante   = User::where('idRol', 3)->first() ?? $farmaceutico;

        if (! $farmaceutico) {
            $this->command->warn('No hay usuarios; corre primero el seeder de usuarios.');
            return;
        }

        $expedientes = Expediente::where('estado', 'activo')->orderBy('folioExpediente')->pluck('folioExpediente');

        if ($expedientes->isEmpty()) {
            $this->command->warn('No hay expedientes activos; corre primero ExpedienteSeeder.');
            return;
        }

        $exp = fn (int $i) => $expedientes->get($i % $expedientes->count());

        $visitas = [
            // --- Completadas (historial) ---
            [
                'exp' => $exp(0), 'usuario' => $farmaceutico, 'fecha' => now()->subDays(20)->setTime(10, 30),
                'tipo' => 'orientacion', 'estado' => 'completada',
                'motivo' => 'Orientación sobre el esquema de amoxicilina indicado para infección de vías respiratorias.',
                'observaciones' => 'Se explicó la importancia de completar los 7 días de tratamiento aunque desaparezcan los síntomas.',
                'triage' => ['peso' => 72.5, 'talla' => 1.68, 'temperatura' => 37.8, 'presionSistolica' => 118, 'presionDiastolica' => 76, 'pulsoPpm' => 84, 'frecuenciaRespiratoria' => 18, 'saturacionOxigeno' => 96],
                'medicacion' => [['Amoxicilina', 500, 'mg', 'Oral', 'Cada 8 horas', 'Completar 7 días de tratamiento.']],
            ],
            [
                'exp' => $exp(1), 'usuario' => $farmaceutico, 'fecha' => now()->subDays(18)->setTime(11, 0),
                'tipo' => 'seguimiento', 'estado' => 'completada',
                'motivo' => 'Seguimiento de hipertensión arterial; el paciente refiere apego irregular al tratamiento.',
                'observaciones' => 'Presión elevada. Se reforzó la toma diaria a la misma hora y se sugirió monitoreo en casa.',
                'triage' => ['peso' => 88.2, 'talla' => 1.72, 'temperatura' => 36.6, 'presionSistolica' => 152, 'presionDiastolica' => 96, 'pulsoPpm' => 78, 'frecuenciaRespiratoria' => 16, 'saturacionOxigeno' => 97],
                'medicacion' => [['Losartán', 50, 'mg', 'Oral', 'Cada 24 horas', 'Tomar en ayunas, a la misma hora todos los días.']],
            ],
            [
                'exp' => $exp(2), 'usuario' => $estudiante, 'fecha' => now()->subDays(15)->setTime(9, 15),
                'tipo' => 'orientacion', 'estado' => 'completada',
                'motivo' => 'Cefalea tensional recurrente; solicita orientación sobre analgésicos de venta libre.',
                'observaciones' => 'Se orientó sobre dosis máxima diaria de paracetamol y datos de alarma para acudir al médico.',
                'triage' => ['peso' => 60.4, 'talla' => 1.58, 'temperatura' => 36.4, 'presionSistolica' => 110, 'presionDiastolica' => 70, 'pulsoPpm' => 72, 'frecuenciaRespiratoria' => 15, 'saturacionOxigeno' => 98],
                'medicacion' => [['Tempra', 500, 'mg', 'Oral', 'Cada 8 horas por máximo 3 días', 'No exceder 3 g al día; suspender si no hay mejoría.']],
            ],
            [
                'exp' => $exp(0), 'usuario' => $farmaceutico, 'fecha' => now()->subDays(10)->setTime(10, 0),
                'tipo' => 'seguimiento', 'estado' => 'completada',
                'motivo' => 'Revisión de evolución tras el tratamiento antibiótico.',
                'observaciones' => 'Sin fiebre ni síntomas respiratorios; esquema completado correctamente.',
                'triage' => ['peso' => 72.1, 'talla' => 1.68, 'temperatura' => 36.5, 'presionSistolica' => 116, 'presionDiastolica' => 74, 'pulsoPpm' => 70, 'frecuenciaRespiratoria' => 16, 'saturacionOxigeno' => 98],
            ],
            [
                'exp' => $exp(3), 'usuario' => $farmaceutico, 'fecha' => now()->subDays(8)->setTime(12, 45),
                'tipo' => 'orientacion', 'estado' => 'completada',
                'motivo' => 'Molestias gastrointestinales al iniciar metformina; solicita orientación.',
                'observaciones' => 'Se recomendó tomarla con alimentos y dividir la dosis según indicación médica.',
                'medicacion' => [['Metformina', 850, 'mg', 'Oral', 'Cada 12 horas', 'Tomar junto con los alimentos para reducir molestias gástricas.']],
            ],
            [
                'exp' => null, 'nombre' => 'María Fernanda López Ruiz', 'contacto' => '444 123 4567',
                'usuario' => $estudiante, 'fecha' => now()->subDays(5)->setTime(13, 20),
                'tipo' => 'orientacion', 'estado' => 'completada',
                'motivo' => 'Consulta sobre la interacción entre ibuprofeno y losartán que toma su familiar.',
                'observaciones' => 'Se explicó el riesgo de la combinación y se sugirió consultar con su médico tratante.',
            ],
            [
                'exp' => $exp(1), 'usuario' => $farmaceutico, 'fecha' => now()->subDays(4)->setTime(11, 30),
                'tipo' => 'seguimiento', 'estado' => 'completada',
                'motivo' => 'Control de presión arterial a dos semanas del refuerzo de apego.',
                'observaciones' => 'Mejoría notable; continuar igual y nueva revisión en un mes.',
                'triage' => ['peso' => 87.0, 'talla' => 1.72, 'temperatura' => 36.7, 'presionSistolica' => 134, 'presionDiastolica' => 86, 'pulsoPpm' => 74, 'frecuenciaRespiratoria' => 16, 'saturacionOxigeno' => 97],
            ],
            [
                'exp' => null, 'nombre' => 'José Antonio Ramírez Torres', 'contacto' => 'jose.ramirez@example.com',
                'usuario' => $farmaceutico, 'fecha' => now()->subDays(3)->setTime(16, 10),
                'tipo' => 'orientacion', 'estado' => 'completada',
                'motivo' => 'Técnica de uso del inhalador de salbutamol recetado recientemente.',
                'observaciones' => 'Se demostró la técnica correcta de inhalación y el enjuague bucal posterior.',
                'medicacion' => [['Ventolin', 100, 'mcg/dosis', 'Inhalada', '2 inhalaciones cada 6 horas por razón necesaria', 'Agitar antes de usar; enjuagar la boca después de cada aplicación.']],
            ],

            // --- Cancelada e inasistencia ---
            [
                'exp' => $exp(2), 'usuario' => $farmaceutico, 'fecha' => now()->subDays(2)->setTime(10, 0),
                'tipo' => 'seguimiento', 'estado' => 'cancelada',
                'motivo' => 'Seguimiento de orientación sobre analgésicos.',
                'motivoCancelacion' => 'El paciente llamó para cancelar por motivos de trabajo; pidió reagendar la próxima semana.',
            ],
            [
                'exp' => $exp(3), 'usuario' => $farmaceutico, 'fecha' => now()->subDay()->setTime(9, 0),
                'tipo' => 'seguimiento', 'estado' => 'no_asistio',
                'motivo' => 'Revisión de tolerancia a metformina.',
                'observaciones' => 'El paciente no se presentó; se intentará contactar para reagendar.',
            ],

            // --- Agenda (citas futuras) ---
            [
                'exp' => $exp(0), 'usuario' => $farmaceutico, 'fecha' => now()->addDays(2)->setTime(10, 0),
                'tipo' => 'seguimiento', 'estado' => 'agendada',
                'motivo' => 'Revisión general y actualización de historia de medicación.',
            ],
            [
                'exp' => $exp(1), 'usuario' => $estudiante, 'fecha' => now()->addDays(5)->setTime(12, 30),
                'tipo' => 'seguimiento', 'estado' => 'agendada',
                'motivo' => 'Control mensual de presión arterial.',
            ],
        ];

        foreach ($visitas as $v) {
            $visita = Visita::create([
                'folioVisita'       => Visita::generarFolio($v['fecha']),
                'folioExpediente'   => $v['exp'],
                'nombreVisitante'   => $v['nombre'] ?? null,
                'contactoVisitante' => $v['contacto'] ?? null,
                'idUsuario'         => $v['usuario']->idUsuario,
                'fecha'             => $v['fecha'],
                'tipo'              => $v['tipo'],
                'motivo'            => $v['motivo'],
                'observaciones'     => $v['observaciones'] ?? null,
                'estado'            => $v['estado'],
                'motivoCancelacion' => $v['motivoCancelacion'] ?? null,
            ]);

            if (! empty($v['triage'])) {
                $t = $v['triage'];
                DatosAntropometricos::create($t + [
                        'idVisita' => $visita->idVisita,
                        'imc'      => round($t['peso'] / ($t['talla'] ** 2), 2),
                    ]);
            }

            foreach ($v['medicacion'] ?? [] as [$nombreMed, $dosisValor, $dosisUnidad, $via, $frecuencia, $indicaciones]) {
                $medicamento = Medicamento::where('nombre', $nombreMed)->first();
                if (! $medicamento) {
                    $this->command->warn("Medicamento '{$nombreMed}' no encontrado; corre antes MedicamentoSeeder.");
                    continue;
                }
                $visita->registrosMedicacion()->create([
                    'idMedicamento'     => $medicamento->idMedicamento,
                    'dosis'             => Medicamento::textoConcentracion($dosisValor, $dosisUnidad),
                    'viaAdministracion' => $via,
                    'frecuenciaUso'     => $frecuencia,
                    'indicaciones'      => $indicaciones,
                ]);
            }
        }

        $this->command->info('Visitas sembradas: ' . count($visitas) . ' (completadas, cancelada, inasistencia y agenda).');
    }
}
