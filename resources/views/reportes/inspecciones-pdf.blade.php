<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <title>
        Reporte Histórico de Inspecciones Vehiculares
    </title>

    <style>

        @page {
            margin: 14px 18px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: DejaVu Sans, sans-serif;
            font-size: 8px;
            color: #1f2937;
            background: #ffffff;
        }

        .header {
            width: 100%;
            background: #0056b3;
            color: white;
            padding: 13px 16px;
            border-radius: 8px;
            margin-bottom: 9px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: middle;
        }

        .brand {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .subtitle {
            font-size: 7px;
            color: #dbeafe;
        }

        .period {
            text-align: right;
            font-size: 7px;
            line-height: 1.4;
        }

        .section {
            margin-bottom: 9px;
        }

        .section-title {
            font-size: 10px;
            font-weight: bold;
            color: #003d82;
            margin-bottom: 4px;
        }

        .section-subtitle {
            margin-top: -2px;
            margin-bottom: 5px;
            font-size: 6px;
            color: #64748b;
        }

        .filters-table {
            width: 100%;
            border-collapse: collapse;
        }

        .filters-table td {
            width: 33.33%;
            border: 1px solid #dfe5ec;
            padding: 5px 7px;
            background: #ffffff;
        }

        .filter-label {
            font-size: 5.5px;
            color: #94a3b8;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .filter-value {
            font-size: 7px;
            font-weight: bold;
            color: #334155;
        }

        .kpi-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 4px 0;
            margin-left: -4px;
            margin-right: -4px;
        }

        .kpi-box {
            width: 25%;
            border: 1px solid #dbe3ec;
            background: #f8fafc;
            padding: 7px 8px;
            border-radius: 6px;
        }

        .kpi-label {
            font-size: 6px;
            color: #64748b;
            margin-bottom: 2px;
        }

        .kpi-number {
            font-size: 14px;
            font-weight: bold;
            color: #0056b3;
        }

        .green {
            color: #059669;
        }

        .red {
            color: #dc2626;
        }

        .amber {
            color: #d97706;
        }

        .summary-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 4px 0;
            margin-left: -4px;
            margin-right: -4px;
        }

        .summary-box {
            width: 50%;
            border: 1px solid #dbe3ec;
            padding: 6px 8px;
            background: #ffffff;
            border-radius: 6px;
        }

        .summary-label {
            font-size: 5.5px;
            text-transform: uppercase;
            color: #94a3b8;
        }

        .summary-value {
            margin-top: 2px;
            font-size: 8px;
            font-weight: bold;
            color: #334155;
        }

        .chart-box {
            border: 1px solid #dbe3ec;
            border-radius: 6px;
            padding: 7px 9px;
            background: #ffffff;
        }

        .chart-table {
            width: 100%;
            border-collapse: collapse;
        }

        .chart-table td {
            padding: 3px 2px;
            vertical-align: middle;
        }

        .chart-label {
            width: 25%;
        }

        .chart-name {
            font-size: 6.5px;
            font-weight: bold;
            color: #334155;
        }

        .chart-detail {
            margin-top: 1px;
            font-size: 5.5px;
            color: #94a3b8;
        }

        .chart-bar-cell {
            width: 58%;
            padding-left: 6px !important;
            padding-right: 6px !important;
        }

        .chart-track {
            width: 100%;
            height: 10px;
            overflow: hidden;
            border-radius: 5px;
            background: #e8eef5;
        }

        .chart-bar-green {
            height: 10px;
            border-radius: 5px;
            background: #059669;
        }

        .chart-bar-red {
            height: 10px;
            border-radius: 5px;
            background: #dc2626;
        }

        .chart-value {
            width: 17%;
            text-align: right;
            font-size: 6px;
            font-weight: bold;
            color: #0056b3;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #dbe3ec;
        }

        .report-table th {
            background: #edf3f9;
            color: #475569;
            font-size: 5.5px;
            text-transform: uppercase;
            padding: 4px 3px;
            border: 1px solid #dbe3ec;
            text-align: left;
        }

        .report-table td {
            padding: 4px 3px;
            border: 1px solid #e5e7eb;
            vertical-align: top;
            font-size: 6px;
            line-height: 1.25;
        }

        .report-table tr {
            page-break-inside: avoid;
        }

        .report-table tr:nth-child(even) td {
            background: #fbfcfd;
        }

        .vehicle-name {
            font-weight: bold;
            color: #1f2937;
        }

        .vehicle-detail {
            margin-top: 1px;
            color: #94a3b8;
            font-size: 5px;
        }

        .status-ok {
            color: #059669;
            font-weight: bold;
        }

        .status-bad {
            color: #dc2626;
            font-weight: bold;
        }

        .incidence-item {
            margin-bottom: 2px;
        }

        .incidence-name {
            font-weight: bold;
            color: #b91c1c;
        }

        .incidence-note {
            color: #7f1d1d;
        }

        .empty {
            padding: 16px;
            text-align: center;
            border: 1px solid #dbe3ec;
            background: #f8fafc;
            border-radius: 6px;
            color: #64748b;
        }

        .footer {
            margin-top: 7px;
            padding-top: 5px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 5.5px;
            color: #94a3b8;
            line-height: 1.3;
        }

    </style>

</head>

<body>

    {{-- HEADER --}}

    <div class="header">

        <table class="header-table">

            <tr>

                <td>

                    <div class="brand">
                        SotyGeo
                    </div>

                    <div class="subtitle">
                        Reporte Histórico de Inspecciones Vehiculares
                    </div>

                </td>

                <td class="period">

                    Periodo:

                    <strong>
                        {{ $fechaInicio->format('d/m/Y') }}
                    </strong>

                    —

                    <strong>
                        {{ $fechaFin->format('d/m/Y') }}
                    </strong>

                </td>

            </tr>

        </table>

    </div>


    {{-- CRITERIOS --}}

    <div class="section">

        <div class="section-title">
            Criterios del reporte
        </div>

        <table class="filters-table">

            <tr>

                <td>

                    <div class="filter-label">
                        Fecha inicial
                    </div>

                    <div class="filter-value">
                        {{ $fechaInicio->format('d/m/Y') }}
                    </div>

                </td>

                <td>

                    <div class="filter-label">
                        Fecha final
                    </div>

                    <div class="filter-value">
                        {{ $fechaFin->format('d/m/Y') }}
                    </div>

                </td>

                <td>

                    <div class="filter-label">
                        Generado por
                    </div>

                    <div class="filter-value">
                        {{
                            auth()->user()->name
                            ??
                            auth()->user()->email
                        }}
                    </div>

                </td>

            </tr>

        </table>

    </div>


    {{-- KPIs --}}

    <div class="section">

        <div class="section-title">
            Resumen general
        </div>

        <table class="kpi-table">

            <tr>

                <td class="kpi-box">

                    <div class="kpi-label">
                        Inspecciones registradas
                    </div>

                    <div class="kpi-number">
                        {{ $totalInspecciones }}
                    </div>

                </td>

                <td class="kpi-box">

                    <div class="kpi-label">
                        Correctas
                    </div>

                    <div class="kpi-number green">
                        {{ $totalCorrectas }}
                    </div>

                </td>

                <td class="kpi-box">

                    <div class="kpi-label">
                        Con incidencias
                    </div>

                    <div class="kpi-number red">
                        {{ $totalConIncidencias }}
                    </div>

                </td>

                <td class="kpi-box">

                    <div class="kpi-label">
                        Vehículos inspeccionados
                    </div>

                    <div class="kpi-number amber">
                        {{ $vehiculosInspeccionados }}
                    </div>

                </td>

            </tr>

        </table>

    </div>


    {{-- INFORMACIÓN ADICIONAL --}}

    <div class="section">

        <table class="summary-table">

            <tr>

                <td class="summary-box">

                    <div class="summary-label">
                        Incidencias detectadas
                    </div>

                    <div class="summary-value">
                        {{ $totalIncidencias }}
                    </div>

                </td>

                <td class="summary-box">

                    <div class="summary-label">
                        Vehículo con más incidencias
                    </div>

                    <div class="summary-value">
                        {{
                            $vehiculoMasIncidencias['vehiculo']
                            ??
                            'Sin información'
                        }}

                        @if(
                            $vehiculoMasIncidencias
                            &&
                            $vehiculoMasIncidencias['incidencias'] > 0
                        )

                            ·
                            {{ $vehiculoMasIncidencias['incidencias'] }}
                            incidencia(s)

                        @endif
                    </div>

                </td>

            </tr>

        </table>

    </div>


    {{-- GRÁFICA --}}

    <div class="section">

        <div class="section-title">
            Resultado de inspecciones
        </div>

        <div class="section-subtitle">
            Comparativa entre inspecciones correctas y con incidencias.
        </div>

        <div class="chart-box">

            <table class="chart-table">

                @foreach($graficaResultados as $item)

                    <tr>

                        <td class="chart-label">

                            <div class="chart-name">
                                {{ $item['nombre'] }}
                            </div>

                            <div class="chart-detail">
                                {{ $item['cantidad'] }}
                                inspección(es)
                            </div>

                        </td>

                        <td class="chart-bar-cell">

                            <div class="chart-track">

                                @if($item['porcentaje'] > 0)

                                    <div
                                        class="{{ $item['nombre'] === 'Correctas' ? 'chart-bar-green' : 'chart-bar-red' }}"
                                        style="width: {{ $item['porcentaje'] }}%;"
                                    ></div>

                                @endif

                            </div>

                        </td>

                        <td class="chart-value">
                            {{ $item['porcentaje'] }}%
                        </td>

                    </tr>

                @endforeach

            </table>

        </div>

    </div>


    {{-- TABLA --}}

    <div class="section">

        <div class="section-title">
            Historial de inspecciones
        </div>

        <div class="section-subtitle">
            Inspecciones realizadas e incidencias detectadas.
        </div>

        @if($inspecciones->isEmpty())

            <div class="empty">
                No se encontraron inspecciones para los criterios seleccionados.
            </div>

        @else

            <table class="report-table">

                <thead>

                    <tr>

                        <th style="width: 12%;">
                            Fecha
                        </th>

                        <th style="width: 18%;">
                            Vehículo
                        </th>

                        <th style="width: 15%;">
                            Conductor
                        </th>

                        <th style="width: 12%;">
                            Resultado
                        </th>

                        <th style="width: 25%;">
                            Incidencias
                        </th>

                        <th style="width: 18%;">
                            Observaciones
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @foreach($inspecciones as $inspeccion)

                        <tr>

                            <td>
                                {{ $inspeccion['fecha']->format('d/m/Y H:i') }}
                            </td>

                            <td>

                                <div class="vehicle-name">
                                    {{ $inspeccion['vehiculo'] }}
                                </div>

                                <div class="vehicle-detail">
                                    {{ $inspeccion['placas'] ?: 'Sin placas' }}
                                </div>

                            </td>

                            <td>
                                {{ $inspeccion['conductor'] }}
                            </td>

                            <td>

                                @if($inspeccion['resultado'] === 'correcto')

                                    <span class="status-ok">
                                        Correcta
                                    </span>

                                @else

                                    <span class="status-bad">
                                        Con incidencias
                                    </span>

                                @endif

                            </td>

                            <td>

                                @if($inspeccion['incidencias']->isEmpty())

                                    Sin incidencias

                                @else

                                    @foreach($inspeccion['incidencias'] as $incidencia)

                                        <div class="incidence-item">

                                            <span class="incidence-name">
                                                {{ $incidencia['elemento'] }}
                                            </span>

                                            @if($incidencia['observaciones'])

                                                <span class="incidence-note">
                                                    :
                                                    {{ $incidencia['observaciones'] }}
                                                </span>

                                            @endif

                                        </div>

                                    @endforeach

                                @endif

                            </td>

                            <td>
                                {{
                                    $inspeccion['observaciones']
                                    ?:
                                    'Sin observaciones'
                                }}
                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        @endif

    </div>


    {{-- FOOTER --}}

    <div class="footer">

        SotyGeo · Sistema Web de Rastreo GPS y Gestión de Flotas

        <br>

        Documento generado el {{ now()->format('d/m/Y H:i') }}

    </div>

</body>

</html>