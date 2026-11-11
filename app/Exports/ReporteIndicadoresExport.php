<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCharts;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Layout;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ReporteIndicadoresExport implements FromArray, WithTitle, WithEvents, WithCharts
{
    protected int $filaFinResumen = 8;
    protected int $filaInicioMeses = 11;
    protected int $filaFinMeses = 21;
    protected int $filaInicioProblemas = 24;
    protected int $filaFinProblemas = 24;
    protected int $filaInicioMedicamentos = 27;
    protected int $filaFinMedicamentos = 27;

    public function title(): string
    {
        return 'Indicadores Unifarmacia';
    }

    public function array(): array
    {
        $periodo = Carbon::now()->locale('es')->isoFormat('MMMM YYYY');
        $fechaGeneracion = date('d/m/Y H:i');

        $filas = [
            // Filas 1 a 4: Membrete institucional (NOM-004)
            ['UNIVERSIDAD AUTÓNOMA DE SAN LUIS POTOSÍ', '', '', ''],
            ['Facultad de Ciencias Químicas · Servicio de Unifarmacia', '', '', ''],
            ['INFORME ESTADÍSTICO DE INDICADORES', '', '', ''],
            ['Periodo: ' . ucfirst($periodo) . '   |   Generado: ' . $fechaGeneracion, '', '', ''],
            ['', '', '', ''], // Fila 5 separadora
            // Fila 6: Encabezado Resumen
            ['Estado de Usuarios', 'Cantidad', '', ''],
        ];

        // 1. Resumen de Usuarios (Filas 7 y 8)
        $usuariosServicio = DB::table('expedientes')->where('estado', 'activo')->count();
        $usuariosPausa    = DB::table('expedientes')->where('estado', 'inactivo')->count();

        $filas[] = ['Activos', $usuariosServicio, '', ''];
        $filas[] = ['En pausa', $usuariosPausa, '', ''];
        $this->filaFinResumen = count($filas); // 8

        $filas[] = ['', '', '', '']; // Fila 9 separadora

        // 2. Evolución Mensual (Fila 10 encabezado, Filas 11..N datos)
        $filas[] = ['Mes', 'Nuevos', 'Activos', 'En pausa'];
        $this->filaInicioMeses = count($filas) + 1; // 11

        $mesActual = Carbon::now()->month;
        $anioActual = Carbon::now()->year;
        $nombresMeses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

        for ($m = 1; $m <= $mesActual; $m++) {
            $inicioMes = Carbon::create($anioActual, $m, 1)->startOfMonth();
            $finMes    = Carbon::create($anioActual, $m, 1)->endOfMonth();

            $nuevos  = DB::table('expedientes')->whereBetween('created_at', [$inicioMes, $finMes])->count();
            $activos = DB::table('expedientes')->where('estado', 'activo')->where('created_at', '<=', $finMes)->count();
            $pausa   = DB::table('expedientes')->where('estado', 'inactivo')->where('created_at', '<=', $finMes)->count();

            $filas[] = [$nombresMeses[$m - 1], $nuevos, $activos, $pausa];
        }
        $this->filaFinMeses = count($filas);

        $filas[] = ['', '', '', '']; // Separador

        // 3. Problemas de Salud
        $filas[] = ['Problema de Salud', 'Casos', '', ''];
        $this->filaInicioProblemas = count($filas) + 1;

        $problemasSalud = DB::table('antecedentes')
            ->select('nombre', DB::raw('COUNT(*) as total'))
            ->whereNotNull('nombre')
            ->where('nombre', '!=', '')
            ->groupBy('nombre')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        if ($problemasSalud->isEmpty()) {
            $filas[] = ['Sin registros', 0, '', ''];
        } else {
            foreach ($problemasSalud as $prob) {
                $filas[] = [$prob->nombre, $prob->total, '', ''];
            }
        }
        $this->filaFinProblemas = count($filas);

        $filas[] = ['', '', '', '']; // Separador

        // 4. Medicamentos en Seguimiento
        $filas[] = ['Principio Activo', 'Casos', '', ''];
        $this->filaInicioMedicamentos = count($filas) + 1;

        $medicamentos = DB::table('medicamentos')
            ->leftJoin('farmacovigilancia', function ($join) {
                $join->on(
                    DB::raw('LOWER(TRIM(medicamentos."principioActivo"))'),
                    '=',
                    DB::raw('LOWER(TRIM(farmacovigilancia.medicamento_sospechoso))')
                );
            })
            ->select('medicamentos.principioActivo as nombre', DB::raw('COUNT(farmacovigilancia.id) as total'))
            ->whereNotNull('medicamentos.principioActivo')
            ->where('medicamentos.principioActivo', '!=', '')
            ->groupBy('medicamentos.principioActivo')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        if ($medicamentos->isEmpty()) {
            $filas[] = ['Sin registros', 0, '', ''];
        } else {
            foreach ($medicamentos as $med) {
                $filas[] = [$med->nombre, $med->total, '', ''];
            }
        }
        $this->filaFinMedicamentos = count($filas);

        return $filas;
    }

    public function charts(): array
    {
        $hoja = "'" . $this->title() . "'";

        // 1. Gráfica de Dona: Estado actual de usuarios
        $donaLabels = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "{$hoja}!\$A\$7:\$A\${$this->filaFinResumen}", null, 2)];
        $donaValues = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "{$hoja}!\$B\$7:\$B\${$this->filaFinResumen}", null, 2)];
        $donaSeries = new DataSeries(DataSeries::TYPE_DONUTCHART, null, range(0, 0), [], $donaLabels, $donaValues);
        $chartDona = new Chart('chart_dona', new Title('Estado actual de los usuarios'), new Legend(Legend::POSITION_BOTTOM), new PlotArea(new Layout(), [$donaSeries]));
        $chartDona->setTopLeftPosition('F2');
        $chartDona->setBottomRightPosition('L15');

        // 2. Gráfica de Línea: Usuarios nuevos por periodo
        $cantMeses = $this->filaFinMeses - $this->filaInicioMeses + 1;
        $mesesLabels = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "{$hoja}!\$A\${$this->filaInicioMeses}:\$A\${$this->filaFinMeses}", null, $cantMeses)];
        $nuevosTitulo = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "{$hoja}!\$B\$" . ($this->filaInicioMeses - 1), null, 1)];
        $nuevosValues = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "{$hoja}!\$B\${$this->filaInicioMeses}:\$B\${$this->filaFinMeses}", null, $cantMeses)];
        $nuevosSeries = new DataSeries(DataSeries::TYPE_LINECHART, DataSeries::GROUPING_STANDARD, range(0, 0), $nuevosTitulo, $mesesLabels, $nuevosValues);
        $chartNuevos = new Chart('chart_nuevos', new Title('Usuarios nuevos por periodo'), new Legend(Legend::POSITION_BOTTOM), new PlotArea(null, [$nuevosSeries]));
        $chartNuevos->setTopLeftPosition('M2');
        $chartNuevos->setBottomRightPosition('T15');

        // 3. Gráfica de Líneas: Variación (Activos vs En pausa)
        $varTitulos = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "{$hoja}!\$C\$" . ($this->filaInicioMeses - 1), null, 1),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "{$hoja}!\$D\$" . ($this->filaInicioMeses - 1), null, 1),
        ];
        $varValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "{$hoja}!\$C\${$this->filaInicioMeses}:\$C\${$this->filaFinMeses}", null, $cantMeses),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "{$hoja}!\$D\${$this->filaInicioMeses}:\$D\${$this->filaFinMeses}", null, $cantMeses),
        ];
        $varSeries = new DataSeries(DataSeries::TYPE_LINECHART, DataSeries::GROUPING_STANDARD, range(0, 1), $varTitulos, $mesesLabels, $varValues);
        $chartVariacion = new Chart('chart_variacion', new Title('Variación de usuarios (Activos vs En pausa)'), new Legend(Legend::POSITION_BOTTOM), new PlotArea(null, [$varSeries]));
        $chartVariacion->setTopLeftPosition('F17');
        $chartVariacion->setBottomRightPosition('T30');

        // 4. Gráfica de Barras Horizontales: Problemas de salud
        $cantProb = $this->filaFinProblemas - $this->filaInicioProblemas + 1;
        $probLabels = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "{$hoja}!\$A\${$this->filaInicioProblemas}:\$A\${$this->filaFinProblemas}", null, $cantProb)];
        $probValues = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "{$hoja}!\$B\${$this->filaInicioProblemas}:\$B\${$this->filaFinProblemas}", null, $cantProb)];
        $probSeries = new DataSeries(DataSeries::TYPE_BARCHART, DataSeries::GROUPING_CLUSTERED, range(0, 0), [], $probLabels, $probValues);
        $probSeries->setPlotDirection(DataSeries::DIRECTION_BAR);
        $chartProblemas = new Chart('chart_problemas', new Title('Problemas de salud analizados'), null, new PlotArea(null, [$probSeries]));
        $chartProblemas->setTopLeftPosition('F32');
        $chartProblemas->setBottomRightPosition('L45');

        // 5. Gráfica de Barras Horizontales: Medicamentos analizados
        $cantMed = $this->filaFinMedicamentos - $this->filaInicioMedicamentos + 1;
        $medLabels = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "{$hoja}!\$A\${$this->filaInicioMedicamentos}:\$A\${$this->filaFinMedicamentos}", null, $cantMed)];
        $medValues = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "{$hoja}!\$B\${$this->filaInicioMedicamentos}:\$B\${$this->filaFinMedicamentos}", null, $cantMed)];
        $medSeries = new DataSeries(DataSeries::TYPE_BARCHART, DataSeries::GROUPING_CLUSTERED, range(0, 0), [], $medLabels, $medValues);
        $medSeries->setPlotDirection(DataSeries::DIRECTION_BAR);
        $chartMedicamentos = new Chart('chart_medicamentos', new Title('Medicamentos analizados'), null, new PlotArea(null, [$medSeries]));
        $chartMedicamentos->setTopLeftPosition('M32');
        $chartMedicamentos->setBottomRightPosition('T45');

        return [$chartDona, $chartNuevos, $chartVariacion, $chartProblemas, $chartMedicamentos];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                foreach ([1, 2, 3, 4] as $fila) {
                    $sheet->mergeCells("A{$fila}:D{$fila}");
                }

                $sheet->getStyle('A1')->applyFromArray(['font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '23297A']]]);
                $sheet->getStyle('A2')->applyFromArray(['font' => ['size' => 10, 'color' => ['rgb' => '4A5568']]]);
                $sheet->getStyle('A3')->applyFromArray(['font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '009B8C']]]);
                $sheet->getStyle('A4')->applyFromArray([
                    'font' => ['size' => 9, 'italic' => true, 'color' => ['rgb' => '718096']],
                    'borders' => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '23297A']]],
                ]);

                // Encabezados de cada bloque
                $encabezados = [
                    'A6:B6',
                    'A' . ($this->filaInicioMeses - 1) . ':D' . ($this->filaInicioMeses - 1),
                    'A' . ($this->filaInicioProblemas - 1) . ':B' . ($this->filaInicioProblemas - 1),
                    'A' . ($this->filaInicioMedicamentos - 1) . ':B' . ($this->filaInicioMedicamentos - 1),
                ];

                foreach ($encabezados as $rango) {
                    $sheet->getStyle($rango)->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '23297A']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                }

                $sheet->getColumnDimension('A')->setWidth(28);
                $sheet->getColumnDimension('B')->setWidth(14);
                $sheet->getColumnDimension('C')->setWidth(14);
                $sheet->getColumnDimension('D')->setWidth(14);
            },
        ];
    }
}
