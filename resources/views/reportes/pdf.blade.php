<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe de Indicadores - Unifarmacia</title>
    <style>
        @page {
            margin: 2.5cm 1.5cm 2cm 1.5cm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #2D3748;
            line-height: 1.4;
        }
        header {
            position: fixed;
            top: -2cm;
            left: 0;
            right: 0;
            height: 1.8cm;
            border-bottom: 2px solid #23297A;
            padding-bottom: 5px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: middle;
        }
        .inst-title {
            font-size: 13px;
            font-weight: bold;
            color: #23297A;
            text-transform: uppercase;
        }
        .inst-sub {
            font-size: 10px;
            color: #4A5568;
        }
        .doc-tag {
            text-align: right;
            font-size: 9px;
            font-weight: bold;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #1A202C;
            border-bottom: 1px solid #009B8C;
            padding-bottom: 4px;
            margin-top: 15px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        .kpi-table, .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .kpi-table td {
            width: 25%;
            padding: 8px;
            border: 1px solid #E2E8F0;
            text-align: center;
            background-color: #F7FAFC;
        }
        .kpi-val {
            font-size: 16px;
            font-weight: bold;
            color: #23297A;
        }
        .kpi-lbl {
            font-size: 9px;
            color: #718096;
            text-transform: uppercase;
        }
        .data-table th, .data-table td {
            border: 1px solid #CBD5E0;
            padding: 6px 8px;
            text-align: left;
        }
        .data-table th {
            background-color: #EDF2F7;
            font-weight: bold;
            font-size: 10px;
        }
        .signatures-area {
            margin-top: 40px;
            width: 100%;
            page-break-inside: avoid;
        }
        .sig-box {
            width: 48%;
            text-align: center;
            vertical-align: top;
        }
        .linea-firma {
            margin: 50px auto 5px auto;
            width: 80%;
            border-top: 1px solid #2D3748;
        }
        .recuadro-sello {
            margin: 10px auto;
            width: 120px;
            height: 90px;
            border: 1px dashed #A0AEC0;
            line-height: 90px;
            color: #A0AEC0;
            font-size: 9px;
            text-transform: uppercase;
        }
        .practica-box {
            margin-top: 25px;
            padding: 12px;
            border: 1px dashed #DD6B20;
            background-color: #FFFAF0;
            text-align: center;
            color: #C05621;
        }
        footer {
            position: fixed;
            bottom: -1cm;
            left: 0;
            right: 0;
            height: 0.8cm;
            font-size: 8px;
            color: #A0AEC0;
            border-top: 1px solid #E2E8F0;
            padding-top: 4px;
        }
    </style>
</head>
<body>

<header>
    <table class="header-table">
        <tr>
            {{-- Espacio reservado para el Logotipo / Escudo oficial --}}
            <td style="width: 22%; text-align: left;">
                <img src="{{ public_path('images/unifarmacia_horizontal.svg') }}" style="height: 45px; width: auto;" alt="Unifarmacia">
            </td>

            {{-- Datos institucionales según NOM-004 --}}
            <td style="width: 48%; padding-left: 8px;">
                <div class="inst-title">Universidad Autónoma de San Luis Potosí</div>
                <div class="inst-sub">Facultad de Ciencias Químicas · Servicio de Unifarmacia</div>
                <div class="inst-sub" style="font-size: 8px; color: #718096;">
                    [Espacio reservado para domicilio oficial del establecimiento]
                </div>
                <div class="inst-sub" style="margin-top: 2px;"><strong>INFORME ESTADÍSTICO DE INDICADORES</strong></div>
            </td>

            {{-- Tipo de formato y periodo --}}
            <td class="doc-tag" style="width: 30%;">
                @if($tipoFormato === 'oficial')
                    <span style="color: #23297A; border: 1px solid #23297A; padding: 2px 6px;">DOCUMENTO OFICIAL</span>
                @else
                    <span style="color: #DD6B20; border: 1px solid #DD6B20; padding: 2px 6px;">FORMATO DE PRÁCTICA</span>
                @endif
                <div style="margin-top: 4px; color: #718096; text-transform: capitalize;">Periodo: {{ $periodo }}</div>
            </td>
        </tr>
    </table>
</header>

<footer>
    <table style="width: 100%;">
        <tr>
            <td>Sistema de Expediente Clínico Farmacéutico · Unifarmacia FCQ UASLP</td>
            <td style="text-align: right;">
                Generado por: {{ auth()->user()->nombres ?? 'Usuario' }} {{ auth()->user()->apellido1 ?? '' }} | {{ date('d/m/Y H:i') }}
            </td>
        </tr>
    </table>
</footer>

<div style="margin-top: 10px;">
    <div class="section-title">1. Resumen de Usuarios y Cobertura</div>
    <table class="kpi-table">
        <tr>
            <td>
                <div class="kpi-val">{{ $usuariosServicio }}</div>
                <div class="kpi-lbl">Usuarios Activos</div>
            </td>
            <td>
                <div class="kpi-val">{{ $usuariosNuevos }}</div>
                <div class="kpi-lbl">Usuarios Nuevos</div>
            </td>
            <td>
                <div class="kpi-val">{{ $usuariosPausa }}</div>
                <div class="kpi-lbl">En Pausa</div>
            </td>
            <td>
                <div class="kpi-val">{{ $variacion }}</div>
                <div class="kpi-lbl">Variación Periodo</div>
            </td>
        </tr>
    </table>

    <table style="width: 100%; margin-top: 10px;" cellspacing="0" cellpadding="0">
        <tr>
            <td style="width: 48%; vertical-align: top;">
                <div class="section-title">2. Problemas de Salud (Casos)</div>
                <table class="data-table">
                    <thead>
                    <tr>
                        <th>Diagnóstico / Condición</th>
                        <th style="text-align: center; width: 55px;">Casos</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($problemasSalud as $prob)
                        <tr>
                            <td>{{ $prob->nombre }}</td>
                            <td style="text-align: center;">{{ $prob->total }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" style="text-align: center;">Sin registros</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </td>

            <td style="width: 4%;"></td>

            <td style="width: 48%; vertical-align: top;">
                <div class="section-title">3. Medicamentos en Seguimiento</div>
                <table class="data-table">
                    <thead>
                    <tr>
                        <th>Principio Activo</th>
                        <th style="text-align: center; width: 55px;">Casos</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($medicamentosAnalizados as $med)
                        <tr>
                            <td>{{ $med->nombre }}</td>
                            <td style="text-align: center;">{{ $med->total }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" style="text-align: center;">Sin registros</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
    @if(!empty($imgDona) || !empty($imgNuevos))
        <div class="section-title" style="margin-top: 15px;">4. Gráficas Estadísticas</div>

        {{-- Fila 1: Dona y Usuarios Nuevos --}}
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 12px;">
            <tr>
                <td style="width: 36%; text-align: center; vertical-align: middle; padding: 8px; border: 1px solid #E2E8F0; background-color: #FFFFFF;">
                    <div style="font-size: 10px; font-weight: bold; color: #23297A; margin-bottom: 6px;">Estado actual de usuarios</div>
                    @if(!empty($imgDona))
                        <img src="{{ $imgDona }}" style="width: 150px; height: auto;">
                    @endif
                </td>
                <td style="width: 2%;"></td>
                <td style="width: 62%; text-align: center; vertical-align: middle; padding: 8px; border: 1px solid #E2E8F0; background-color: #FFFFFF;">
                    <div style="font-size: 10px; font-weight: bold; color: #23297A; margin-bottom: 6px;">Usuarios nuevos por periodo</div>
                    @if(!empty($imgNuevos))
                        <img src="{{ $imgNuevos }}" style="width: 100%; height: auto;">
                    @endif
                </td>
            </tr>
        </table>

        {{-- Fila 2: Variación de usuarios --}}
        @if(!empty($imgVariacion))
            <div style="text-align: center; padding: 8px; border: 1px solid #E2E8F0; background-color: #FFFFFF; margin-bottom: 12px; page-break-inside: avoid;">
                <div style="font-size: 10px; font-weight: bold; color: #23297A; margin-bottom: 6px;">Variación de usuarios (Activos vs En pausa)</div>
                <img src="{{ $imgVariacion }}" style="width: 95%; height: auto;">
            </div>
        @endif

        {{-- Fila 3: Problemas de salud y Medicamentos --}}
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 12px; page-break-inside: avoid;">
            <tr>
                <td style="width: 49%; text-align: center; vertical-align: top; padding: 8px; border: 1px solid #E2E8F0; background-color: #FFFFFF;">
                    <div style="font-size: 10px; font-weight: bold; color: #23297A; margin-bottom: 6px;">Problemas de salud analizados</div>
                    @if(!empty($imgProblemas))
                        <img src="{{ $imgProblemas }}" style="width: 100%; height: auto;">
                    @endif
                </td>
                <td style="width: 2%;"></td>
                <td style="width: 49%; text-align: center; vertical-align: top; padding: 8px; border: 1px solid #E2E8F0; background-color: #FFFFFF;">
                    <div style="font-size: 10px; font-weight: bold; color: #23297A; margin-bottom: 6px;">Medicamentos analizados</div>
                    @if(!empty($imgMedicamentos))
                        <img src="{{ $imgMedicamentos }}" style="width: 100%; height: auto;">
                    @endif
                </td>
            </tr>
        </table>
    @endif
    @if($tipoFormato === 'oficial')
        <div class="signatures-area">
            <table style="width: 100%;">
                <tr>
                    <td class="sig-box">
                        <div class="linea-firma"></div>
                        <strong>
                            Q.F.B. {{ $responsable ? trim($responsable->nombres . ' ' . $responsable->apellido1 . ' ' . ($responsable->apellido2 ?? '')) : 'Responsable Sanitario' }}
                        </strong><br>
                        <span style="font-size: 9px; color: #718096;">Responsable Sanitario · Firma y Cédula Profesional</span>
                    </td>
                    <td class="sig-box">
                        <div class="recuadro-sello">Sello Físico Oficial</div>
                        <span style="font-size: 9px; color: #718096;">Unifarmacia · FCQ UASLP</span>
                    </td>
                </tr>
            </table>
        </div>
    @else
        <div class="practica-box">
            <strong>INSTRUMENTO DE PRÁCTICA CLÍNICO-FARMACÉUTICA</strong><br>
            Uso exclusivo para actividades académicas y formativas de la Licenciatura en Farmacia / Q.F.B.<br>
            <em>Sin validez sanitaria oficial · No requiere firma del Responsable Sanitario.</em>
        </div>
    @endif
</div>

</body>
</html>
