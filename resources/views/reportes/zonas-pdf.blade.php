<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <title>
        Reporte de Visitas y Tiempos en Zonas
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

            font-family:
                DejaVu Sans,
                sans-serif;

            font-size: 8px;

            color: #1f2937;

            background: #ffffff;
        }


        /* ====================================================== */
        /* HEADER */
        /* ====================================================== */

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


        /* ====================================================== */
        /* GENERAL */
        /* ====================================================== */

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


        /* ====================================================== */
        /* CRITERIOS */
        /* ====================================================== */

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


        /* ====================================================== */
        /* KPIS */
        /* ====================================================== */

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


        .amber {
            color: #d97706;
        }


        /* ====================================================== */
        /* GRÁFICA */
        /* ====================================================== */

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

            padding-right: 6px !important;
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

            padding-left: 4px !important;

            padding-right: 6px !important;
        }


        .chart-track {
            width: 100%;

            height: 10px;

            overflow: hidden;

            border-radius: 5px;

            background: #e8eef5;
        }


        .chart-bar {
            height: 10px;

            border-radius: 5px;

            background: #0056b3;
        }


        .chart-value {
            width: 17%;

            text-align: right;

            font-size: 6px;

            font-weight: bold;

            color: #0056b3;
        }


        /* ====================================================== */
        /* TABLA */
        /* ====================================================== */

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

            line-height: 1.2;
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


        .zone-name {
            font-weight: bold;

            color: #0056b3;
        }


        .duration {
            font-weight: bold;

            color: #0056b3;
        }


        .status-finished {
            color: #64748b;

            font-weight: bold;
        }


        .status-active {
            color: #059669;

            font-weight: bold;
        }


        /* ====================================================== */
        /* EXTRA */
        /* ====================================================== */

        .summary-table {
            width: 100%;

            border-collapse: separate;

            border-spacing: 4px 0;

            margin-left: -4px;

            margin-right: -4px;
        }


        .summary-box {
            width: 33.33%;

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


        /* ====================================================== */
        /* FOOTER */
        /* ====================================================== */

        .footer {
            margin-top: 7px;

            padding-top: 5px;

            border-top: 1px solid #e5e7eb;

            text-align: center;

            font-size: 5.5px;

            color: #94a3b8;

            line-height: 1.3;
        }


        .empty {
            padding: 16px;

            text-align: center;

            border: 1px solid #dbe3ec;

            background: #f8fafc;

            border-radius: 6px;

            color: #64748b;
        }

    </style>

</head>


<body>


    {{-- ====================================================== --}}
    {{-- HEADER --}}
    {{-- ====================================================== --}}

    <div class="header">

        <table class="header-table">

            <tr>

                <td>

                    <div class="brand">
                        SotyGeo
                    </div>

                    <div class="subtitle">
                        Reporte de Visitas y Tiempos en Zonas
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


    {{-- ====================================================== --}}
    {{-- CRITERIOS --}}
    {{-- ====================================================== --}}

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
                            trim(
                                (
                                    auth()->user()->nombre
                                    ??
                                    auth()->user()->name
                                    ??
                                    ''
                                )
                                .
                                ' '
                                .
                                (
                                    auth()->user()->apellido_paterno
                                    ??
                                    ''
                                )
                            )
                            ?:
                            auth()->user()->email
                        }}

                    </div>

                </td>

            </tr>

        </table>

    </div>


    {{-- ====================================================== --}}
    {{-- RESUMEN --}}
    {{-- ====================================================== --}}

    <div class="section">

        <div class="section-title">
            Resumen general
        </div>


        <table class="kpi-table">

            <tr>

                <td class="kpi-box">

                    <div class="kpi-label">
                        Visitas registradas
                    </div>

                    <div class="kpi-number">
                        {{ $totalVisitas }}
                    </div>

                </td>


                <td class="kpi-box">

                    <div class="kpi-label">
                        Tiempo total en zonas
                    </div>

                    <div class="kpi-number">
                        {{ $tiempoTotalFormateado }}
                    </div>

                </td>


                <td class="kpi-box">

                    <div class="kpi-label">
                        Promedio de permanencia
                    </div>

                    <div class="kpi-number green">
                        {{ $promedioFormateado }}
                    </div>

                </td>


                <td class="kpi-box">

                    <div class="kpi-label">
                        Zonas visitadas
                    </div>

                    <div class="kpi-number amber">
                        {{ $zonasVisitadas }}
                    </div>

                </td>

            </tr>

        </table>

    </div>


    {{-- ====================================================== --}}
    {{-- DATOS ADICIONALES --}}
    {{-- ====================================================== --}}

    <div class="section">

        <table class="summary-table">

            <tr>

                <td class="summary-box">

                    <div class="summary-label">
                        Visitas finalizadas
                    </div>

                    <div class="summary-value">
                        {{ $visitasFinalizadas }}
                    </div>

                </td>


                <td class="summary-box">

                    <div class="summary-label">
                        Vehículos actualmente en zona
                    </div>

                    <div class="summary-value">
                        {{ $visitasActivas }}
                    </div>

                </td>


                <td class="summary-box">

                    <div class="summary-label">
                        Zona con más visitas
                    </div>

                    <div class="summary-value">

                        {{
                            $zonaMasVisitada['zona']
                            ??
                            'Sin información'
                        }}

                        @if($zonaMasVisitada)

                            ·

                            {{ $zonaMasVisitada['visitas'] }}

                            visita(s)

                        @endif

                    </div>

                </td>

            </tr>

        </table>

    </div>


    {{-- ====================================================== --}}
    {{-- GRÁFICA --}}
    {{-- ====================================================== --}}

    <div class="section">

        <div class="section-title">
            Permanencia por zona
        </div>

        <div class="section-subtitle">
            Tiempo acumulado y cantidad de visitas registradas en cada zona.
        </div>


        @if($grafica->isEmpty())

            <div class="empty">
                No existe información para generar la gráfica.
            </div>

        @else

            @php

                $maximoMinutos =
                    max(
                        1,
                        (int)
                        $grafica->max(
                            'minutos'
                        )
                    );

            @endphp


            <div class="chart-box">

                <table class="chart-table">

                    @foreach($grafica as $item)

                        @php

                            $minutos =
                                (int)
                                $item['minutos'];


                            $porcentaje =
                                $minutos > 0

                                    ?

                                    max(
                                        3,
                                        min(
                                            100,
                                            (
                                                $minutos
                                                /
                                                $maximoMinutos
                                            )
                                            *
                                            100
                                        )
                                    )

                                    :

                                    0;

                        @endphp


                        <tr>

                            <td class="chart-label">

                                <div class="chart-name">
                                    {{ $item['zona'] }}
                                </div>

                                <div class="chart-detail">

                                    {{ $item['visitas'] }}

                                    visita(s)

                                </div>

                            </td>


                            <td class="chart-bar-cell">

                                <div class="chart-track">

                                    @if($minutos > 0)

                                        <div
                                            class="chart-bar"

                                            style="
                                                width:
                                                {{ $porcentaje }}%;
                                            "
                                        ></div>

                                    @endif

                                </div>

                            </td>


                            <td class="chart-value">

                                {{ $item['tiempo'] }}

                            </td>

                        </tr>

                    @endforeach

                </table>

            </div>

        @endif

    </div>


    {{-- ====================================================== --}}
    {{-- VISITAS --}}
    {{-- ====================================================== --}}

    <div class="section">

        <div class="section-title">
            Visitas registradas
        </div>

        <div class="section-subtitle">
            Entradas, salidas y tiempos de permanencia correspondientes al periodo consultado.
        </div>


        @if($eventos->isEmpty())

            <div class="empty">
                No se encontraron visitas para los criterios seleccionados.
            </div>

        @else

            <table class="report-table">

                <thead>

                    <tr>

                        <th style="width:22%;">
                            Vehículo
                        </th>

                        <th style="width:15%;">
                            Zona
                        </th>

                        <th style="width:16%;">
                            Entrada
                        </th>

                        <th style="width:16%;">
                            Salida
                        </th>

                        <th style="width:15%;">
                            Permanencia
                        </th>

                        <th style="width:16%;">
                            Estado
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @foreach($eventos as $evento)

                        <tr>

                            <td>

                                <div class="vehicle-name">

                                    {{ $evento['vehiculo'] }}

                                </div>


                                <div class="vehicle-detail">

                                    {{
                                        $evento['placas']
                                        ?:
                                        'Sin placas'
                                    }}


                                    @if($evento['flotilla'])

                                        · {{ $evento['flotilla'] }}

                                    @endif

                                </div>

                            </td>


                            <td>

                                <span class="zone-name">
                                    {{ $evento['zona'] }}
                                </span>

                            </td>


                            <td>

                                {{
                                    $evento['fecha_entrada']
                                        ->format(
                                            'd/m/Y H:i'
                                        )
                                }}

                            </td>


                            <td>

                                @if($evento['fecha_salida'])

                                    {{
                                        $evento['fecha_salida']
                                            ->format(
                                                'd/m/Y H:i'
                                            )
                                    }}

                                @else

                                    Aún en zona

                                @endif

                            </td>


                            <td class="duration">

                                {{ $evento['duracion_formateada'] }}

                            </td>


                            <td>

                                @if($evento['estado'] === 'Finalizada')

                                    <span class="status-finished">
                                        Finalizada
                                    </span>

                                @else

                                    <span class="status-active">
                                        En zona
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        @endif

    </div>


    {{-- ====================================================== --}}
    {{-- FOOTER --}}
    {{-- ====================================================== --}}

    <div class="footer">

        SotyGeo · Sistema Web de Rastreo GPS y Gestión de Flotas

        <br>

        Documento generado el

        {{ now()->format('d/m/Y H:i') }}

    </div>


</body>

</html>