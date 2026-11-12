<?php

namespace Database\Seeders;

use App\Models\Medicamento;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MedicamentoSeeder extends Seeder
{
    use WithoutModelEvents; // No llenar la auditoría durante el seed

    public function run(): void
    {
        // [nombre, tipo, principio activo, laboratorio, forma, cantidad, unidad envase, conc. valor, conc. unidad, receta, controlado]
        $medicamentos = [
            ['Tempra',       'comercial', 'Paracetamol',                'Reckitt Benckiser',      'Tableta',             20,  'piezas', 500,  'mg',        false, false],
            ['Paracetamol',  'generico',  'Paracetamol',                'Laboratorios Pisa',      'Tableta',             20,  'piezas', 500,  'mg',        false, false],
            ['Advil',        'comercial', 'Ibuprofeno',                 'Pfizer',                 'Cápsula',             10,  'piezas', 400,  'mg',        false, false],
            ['Ibuprofeno',   'generico',  'Ibuprofeno',                 'Sandoz',                 'Tableta',             10,  'piezas', 600,  'mg',        false, false],
            ['Amoxicilina',  'generico',  'Amoxicilina',                'Laboratorios Liomont',   'Cápsula',             12,  'piezas', 500,  'mg',        true,  false],
            ['Pentrexyl',    'comercial', 'Ampicilina',                 'Bristol-Myers Squibb',   'Cápsula',             20,  'piezas', 500,  'mg',        true,  false],
            ['Losartán',     'generico',  'Losartán potásico',          'Laboratorios Pisa',      'Tableta',             30,  'piezas', 50,   'mg',        true,  false],
            ['Metformina',   'generico',  'Clorhidrato de metformina',  'Laboratorios Silanes',   'Tableta',             30,  'piezas', 850,  'mg',        true,  false],
            ['Electrolit',   'comercial', 'Glucosa y electrolitos orales', 'Laboratorios Pisa',   'Solución oral',       625, 'ml',     null, null,        false, false],
            ['Omeprazol',    'generico',  'Omeprazol',                  'Apotex',                 'Cápsula',             14,  'piezas', 20,   'mg',        false, false],
            ['Ambroxol',     'generico',  'Clorhidrato de ambroxol',    'Laboratorios Chinoin',   'Jarabe',              120, 'ml',     30,   'mg/5 ml',   false, false],
            ['Ventolin',     'comercial', 'Salbutamol',                 'GSK',                    'Aerosol / inhalador', 200, 'dosis',  100,  'mcg/dosis', true,  false],
            ['Clonazepam',   'generico',  'Clonazepam',                 'Psicofarma',             'Tableta',             30,  'piezas', 2,    'mg',        true,  true],
            ['Tramadol',     'generico',  'Clorhidrato de tramadol',    'Grünenthal',             'Cápsula',             20,  'piezas', 50,   'mg',        true,  true],
        ];

        foreach ($medicamentos as [$nombre, $tipo, $principio, $lab, $forma, $cant, $uEnv, $cVal, $cUni, $receta, $controlado]) {
            $presentacion = Medicamento::textoPresentacion($forma, $cant, $uEnv);
            $concentracion = ($cVal && $cUni) ? Medicamento::textoConcentracion($cVal, $cUni) : null;

            Medicamento::firstOrCreate(
                [
                    'nombre'          => $nombre,
                    'principioActivo' => $principio,
                    'laboratorio'     => $lab,
                    'presentacion'    => $presentacion,
                    'concentracion'   => $concentracion,
                ],
                [
                    'tipo'                => $tipo,
                    'formaFarmaceutica'   => $forma,
                    'cantidadEnvase'      => $cant,
                    'unidadEnvase'        => $uEnv,
                    'concentracionValor'  => $cVal,
                    'concentracionUnidad' => $cUni,
                    'requiereReceta'      => $controlado || $receta, // un controlado siempre requiere receta
                    'esControlado'        => $controlado,
                ]
            );
        }

        $this->command->info('Catálogo de medicamentos: ' . Medicamento::count() . ' registros.');
    }
}
