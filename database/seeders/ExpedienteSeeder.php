<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Paciente;
use App\Models\PacienteTelefono;
use App\Models\PacienteCorreo;
use App\Models\ContactoEmergencia;
use App\Models\ContactoTelefono;
use App\Models\ContactoCorreo;
use App\Models\Expediente;
use App\Models\Habito;
use App\Models\AntecedenteGinecoObstetrico;
use App\Models\Antecedente;
use App\Models\Alergia;
use App\Models\Visita;
use App\Models\DatosAntropometricos;
use App\Models\User;

class ExpedienteSeeder extends Seeder
{
    public function run(): void
    {
        // Tomamos el primer usuario disponible (ej. el admin) para que sea el creador
        $usuarioId = User::value('idUsuario') ?? 1;

        $pacientesPrueba = [
            [
                'nombres' => 'María Fernanda',
                'apellido1' => 'López',
                'apellido2' => 'Hernández',
                'fechaNac' => '1995-05-12',
                'curp' => 'LOHM950512MSRHRN01',
                'sexo' => 'F',
                'ocupacion' => 'Enfermera',
                'escolaridad' => 'Universidad',
                'servicioMedico' => 'IMSS',
                'lugarProcedencia' => 'San Luis Potosí, S.L.P.',
                'lugarResidencia' => 'Soledad de Graciano Sánchez',
                'telefonos' => [['numero' => '4441234567', 'tipo' => 'Celular']],
                'correos' => ['mfer.lopez@example.com'],
                'contacto' => ['nombres' => 'Carlos', 'apellido1' => 'López', 'apellido2' => 'Pérez', 'parentesco' => 'Familiar', 'telefono' => '4449876543'],
                'triage' => ['peso' => 65.5, 'talla' => 1.68, 'saturacion' => 98, 'sistolica' => 120, 'diastolica' => 80, 'pulso' => 72, 'resp' => 18],
                'gineco' => ['embarazoActual' => 0, 'semanas' => null, 'menstruacion' => 1, 'lactancia' => 0, 'obs' => 'Sin alteraciones.'],
                'alergias' => [['sustancia' => 'Penicilina', 'reaccion' => 'Urticaria', 'severidad' => 'Moderada']],
                'antecedentes' => [['tipo' => 'Quirúrgico', 'nombre' => 'Apendicectomía', 'descripcion' => 'Operada en 2018 sin complicaciones.']]
            ],
            [
                'nombres' => 'Juan Carlos',
                'apellido1' => 'Pérez',
                'apellido2' => 'García',
                'fechaNac' => '1988-10-20',
                'curp' => 'PEGJ881020HSRGRC04',
                'sexo' => 'M',
                'ocupacion' => 'Comerciante',
                'escolaridad' => 'Medio superior',
                'servicioMedico' => 'Ninguno',
                'lugarProcedencia' => 'Matehuala, S.L.P.',
                'lugarResidencia' => 'San Luis Potosí, S.L.P.',
                'telefonos' => [['numero' => '4881122334', 'tipo' => 'Celular']],
                'correos' => ['juanc.perez@example.com'],
                'contacto' => ['nombres' => 'Ana', 'apellido1' => 'García', 'apellido2' => 'López', 'parentesco' => 'Pareja', 'telefono' => '4445556677'],
                'triage' => ['peso' => 82.0, 'talla' => 1.75, 'saturacion' => 97, 'sistolica' => 130, 'diastolica' => 85, 'pulso' => 78, 'resp' => 20],
                'gineco' => null,
                'alergias' => [['sustancia' => 'Aspirina', 'reaccion' => 'Gastritis severa', 'severidad' => 'Leve']],
                'antecedentes' => [['tipo' => 'Enfermedad', 'nombre' => 'Hipertensión arterial', 'descripcion' => 'Controlada con Enalapril.']]
            ],
            [
                'nombres' => 'Sofía Valentina',
                'apellido1' => 'Ramírez',
                'apellido2' => 'Torres',
                'fechaNac' => '2001-03-04',
                'curp' => 'RATS010304MSRMTRS2',
                'sexo' => 'F',
                'ocupacion' => 'Estudiante',
                'escolaridad' => 'Universidad',
                'servicioMedico' => 'Particular',
                'lugarProcedencia' => 'Guadalajara, Jalisco',
                'lugarResidencia' => 'San Luis Potosí, S.L.P.',
                'telefonos' => [['numero' => '4447788990', 'tipo' => 'Celular']],
                'correos' => ['sofia.ramirez@example.com'],
                'contacto' => ['nombres' => 'Roberto', 'apellido1' => 'Ramírez', 'apellido2' => 'Sánchez', 'parentesco' => 'Familiar', 'telefono' => '4443332211'],
                'triage' => ['peso' => 58.0, 'talla' => 1.62, 'saturacion' => 99, 'sistolica' => 110, 'diastolica' => 70, 'pulso' => 68, 'resp' => 16],
                'gineco' => ['embarazoActual' => 1, 'semanas' => 14, 'menstruacion' => 0, 'lactancia' => 0, 'obs' => 'Control prenatal regular.'],
                'alergias' => [],
                'antecedentes' => []
            ],
            [
                'nombres' => 'Luis Miguel',
                'apellido1' => 'Morales',
                'apellido2' => 'Castro',
                'fechaNac' => '1975-12-01',
                'curp' => 'MOCL751201HSRMCR08',
                'sexo' => 'M',
                'ocupacion' => 'Contador',
                'escolaridad' => 'Universidad',
                'servicioMedico' => 'ISSSTE',
                'lugarProcedencia' => 'San Luis Potosí, S.L.P.',
                'lugarResidencia' => 'San Luis Potosí, S.L.P.',
                'telefonos' => [['numero' => '4449900112', 'tipo' => 'Fijo']],
                'correos' => ['luism.morales@example.com'],
                'contacto' => ['nombres' => 'Claudia', 'apellido1' => 'Morales', 'apellido2' => 'Castro', 'parentesco' => 'Familiar', 'telefono' => '4441112233'],
                'triage' => ['peso' => 90.5, 'talla' => 1.70, 'saturacion' => 96, 'sistolica' => 140, 'diastolica' => 90, 'pulso' => 82, 'resp' => 22],
                'gineco' => null,
                'alergias' => [['sustancia' => 'Mariscos', 'reaccion' => 'Anafilaxia', 'severidad' => 'Severa']],
                'antecedentes' => [['tipo' => 'Enfermedad', 'nombre' => 'Diabetes Mellitus Tipo 2', 'descripcion' => 'En tratamiento con Metformina.']]
            ],
            [
                'nombres' => 'Ana Karen',
                'apellido1' => 'Flores',
                'apellido2' => 'Jiménez',
                'fechaNac' => '1999-07-15',
                'curp' => 'FOJA990715MSRFJM03',
                'sexo' => 'F',
                'ocupacion' => 'Diseñadora gráfica',
                'escolaridad' => 'Universidad',
                'servicioMedico' => 'IMSS',
                'lugarProcedencia' => 'Rioverde, S.L.P.',
                'lugarResidencia' => 'Soledad de Graciano Sánchez',
                'telefonos' => [['numero' => '4871234567', 'tipo' => 'Celular']],
                'correos' => ['anak.flores@example.com'],
                'contacto' => ['nombres' => 'Guadalupe', 'apellido1' => 'Jiménez', 'apellido2' => 'Rojas', 'parentesco' => 'Familiar', 'telefono' => '4879876543'],
                'triage' => ['peso' => 61.0, 'talla' => 1.65, 'saturacion' => 99, 'sistolica' => 115, 'diastolica' => 75, 'pulso' => 75, 'resp' => 18],
                'gineco' => ['embarazoActual' => 0, 'semanas' => null, 'menstruacion' => 1, 'lactancia' => 1, 'obs' => 'Lactancia materna exclusiva.'],
                'alergias' => [],
                'antecedentes' => []
            ],
            [
                'nombres' => 'David Alejandro',
                'apellido1' => 'Vázquez',
                'apellido2' => 'Ortiz',
                'fechaNac' => '2004-01-30',
                'curp' => 'VAOD040130HSRVRT05',
                'sexo' => 'M',
                'ocupacion' => 'Estudiante',
                'escolaridad' => 'Medio superior',
                'servicioMedico' => 'IMSS-Bienestar',
                'lugarProcedencia' => 'San Luis Potosí, S.L.P.',
                'lugarResidencia' => 'San Luis Potosí, S.L.P.',
                'telefonos' => [['numero' => '4443344556', 'tipo' => 'Celular']],
                'correos' => ['david.vazquez@example.com'],
                'contacto' => ['nombres' => 'Patricia', 'apellido1' => 'Ortiz', 'apellido2' => 'Vega', 'parentesco' => 'Familiar', 'telefono' => '4446655443'],
                'triage' => ['peso' => 70.0, 'talla' => 1.80, 'saturacion' => 100, 'sistolica' => 120, 'diastolica' => 80, 'pulso' => 70, 'resp' => 16],
                'gineco' => null,
                'alergias' => [['sustancia' => 'Lidocaína', 'reaccion' => 'Mareos y palpitaciones', 'severidad' => 'Leve']],
                'antecedentes' => [['tipo' => 'Otro', 'nombre' => 'Esguince cervical', 'descripcion' => 'Lesión deportiva en 2024, rehabilitado.']]
            ]
        ];

        foreach ($pacientesPrueba as $index => $data) {
            DB::transaction(function () use ($data, $usuarioId, $index) {
                // 1. Paciente
                $paciente = Paciente::create([
                    'nombres' => $data['nombres'],
                    'apellido1' => $data['apellido1'],
                    'apellido2' => $data['apellido2'],
                    'fechaNac' => $data['fechaNac'],
                    'curp' => $data['curp'],
                    'sexo' => $data['sexo'],
                    'ocupacion' => $data['ocupacion'],
                    'escolaridad' => $data['escolaridad'],
                    'servicioMedico' => $data['servicioMedico'],
                    'lugarProcedencia' => $data['lugarProcedencia'],
                    'lugarResidencia' => $data['lugarResidencia'],
                ]);

                // 2. Teléfonos y correos del paciente
                foreach ($data['telefonos'] as $tel) {
                    PacienteTelefono::create(['idPaciente' => $paciente->idPaciente, 'numero' => $tel['numero'], 'tipo' => $tel['tipo']]);
                }
                foreach ($data['correos'] as $correo) {
                    PacienteCorreo::create(['idPaciente' => $paciente->idPaciente, 'correo' => $correo]);
                }

                // 3. Contacto de emergencia
                $contacto = ContactoEmergencia::create([
                    'idPaciente' => $paciente->idPaciente,
                    'nombres' => $data['contacto']['nombres'],
                    'apellido1' => $data['contacto']['apellido1'],
                    'apellido2' => $data['contacto']['apellido2'],
                    'parentesco' => $data['contacto']['parentesco'],
                ]);
                ContactoTelefono::create([
                    'idContacto' => $contacto->idContacto,
                    'numero'     => $data['contacto']['telefono'],
                    'tipo'       => 'Celular',
                ]);

                // 4. Folio único de expediente (Ej: EXP-2026-0001, etc.)
                $folio = sprintf('EXP-2026-%04d', $index + 1);
                Expediente::create([
                    'folioExpediente' => $folio,
                    'idPaciente' => $paciente->idPaciente,
                    'idUsuarioCreador' => $usuarioId,
                    'estado' => 'activo'
                ]);

                // 5. Hábitos por defecto
                Habito::create([
                    'folioExpediente' => $folio,
                    'frecuenciaTabaquismo' => 'Nunca',
                    'frecuenciaAlcoholismo' => 'Ocasionalmente',
                    'frecuenciaActFisica' => 'Con frecuencia',
                    'frecuenciaDrogas' => 'Nunca',
                    'horaSueno' => 8
                ]);

                // 6. Gineco si es Femenino
                if ($data['sexo'] === 'F' && $data['gineco']) {
                    AntecedenteGinecoObstetrico::create([
                        'folioExpediente' => $folio,
                        'embarazoActual' => $data['gineco']['embarazoActual'],
                        'semanasGestacion' => $data['gineco']['semanas'],
                        'menstruacion' => $data['gineco']['menstruacion'],
                        'lactancia' => $data['gineco']['lactancia'],
                        'observaciones' => $data['gineco']['obs']
                    ]);
                }

                // 7. Alergias y Antecedentes
                foreach ($data['alergias'] as $al) {
                    Alergia::create(array_merge(['folioExpediente' => $folio], $al));
                }
                foreach ($data['antecedentes'] as $ant) {
                    Antecedente::create(array_merge(['folioExpediente' => $folio], $ant));
                }

                // 8. Visita inicial y Triage
                $fechaVisita = now();

                $visita = Visita::create([
                    'folioExpediente' => $folio,
                    'idUsuario'       => $usuarioId,
                    'fecha'           => $fechaVisita,
                    'folioVisita'     => Visita::generarFolio($fechaVisita),
                    'motivo'          => 'Apertura de expediente y valoración inicial',
                    'estado'          => 'completada',
                ]);

                $peso = $data['triage']['peso'];
                $talla = $data['triage']['talla'];
                $imc = round($peso / ($talla * $talla), 2);

                DatosAntropometricos::create([
                    'idVisita' => $visita->idVisita,
                    'peso' => $peso,
                    'talla' => $talla,
                    'imc' => $imc,
                    'saturacionOxigeno' => $data['triage']['saturacion'],
                    'presionSistolica' => $data['triage']['sistolica'],
                    'presionDiastolica' => $data['triage']['diastolica'],
                    'pulsoPpm' => $data['triage']['pulso'],
                    'frecuenciaRespiratoria' => $data['triage']['resp'],
                ]);
            });
        }
    }
}
