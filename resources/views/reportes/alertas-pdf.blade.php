<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <title>
        Reporte de Excesos de Velocidad y Alertas
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
        }


        .blue {
            color: #0056b3;
        }


        .red {
            color: #dc2626;
        }


        .amber {
            color: #d97706;
        }


        .kpi-unit {
            font-size: 5.5px;

            color: #94a3b8;

            margin-top: 1px;
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


        .chart-plate {
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


        .chart-total {
            height: 10px;

            overflow: hidden;

            border-radius: 5px;
        }


        .chart-red {
            height: 10px;

            background: #ef4444;

            float: left;
        }


        .chart-amber {
            height: 10px;

            background: #f4b63f;

            float: left;
        }


        .chart-values {
            width: 17%;

            text-align: right;

            font-size: 5.5px;
        }


        .chart-values .excesos {
            color: #dc2626;

            font-weight: bold;
        }


        .chart-values .alertas {
            color: #d97706;

            font-weight: bold;
        }


        .legend {
            margin-top: 5px;

            font-size: 5.5px;

            color: #64748b;
        }


        .legend-item {
            margin-right: 12px;
        }


        .legend-red {
            color: #ef4444;

            font-size: 9px;
        }


        .legend-amber {
            color: #f4b63f;

            font-size: 9px;
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


        .badge-speed {
            display: inline-block;

            padding: 2px 4px;

            border-radius: 6px;

            background: #fef2f2;

            color: #dc2626;

            font-weight: bold;
        }


        .badge-alert {
            display: inline-block;

            padding: 2px 4px;

            border-radius: 6px;

            background: #fffbeb;

            color: #b45309;

            font-weight: bold;
        }


        .speed-value {
            text-align: right;

            font-weight: bold;

            color: #dc2626;
        }


        .number {
            text-align: right;
        }


        .description {
            line-height: 1.2;
        }


        .excess {
            margin-top: 2px;

            font-size: 5.5px;

            font-weight: bold;

            color: #dc2626;
        }


        /* ====================================================== */
        /* SIN DATOS */
        /* ====================================================== */

        .empty {
            padding: 16px;

            text-align: center;

            border: 1px solid #dbe3ec;

            border-radius: 6px;

            background: #f8fafc;

            color: #64748b;
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
                        Reporte de Excesos de Velocidad y Alertas
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
                        Eventos registrados
                    </div>

                    <div class="kpi-number blue">
                        {{ $totalEventos }}
                    </div>

                </td>


                <td class="kpi-box">

                    <div class="kpi-label">
                        Excesos de velocidad
                    </div>

                    <div class="kpi-number red">
                        {{ $totalExcesos }}
                    </div>

                </td>


                <td class="kpi-box">

                    <div class="kpi-label">
                        Otras alertas
                    </div>

                    <div class="kpi-number amber">
                        {{ $totalAlertas }}
                    </div>

                </td>


                <td class="kpi-box">

                    <div class="kpi-label">
                        Velocidad máxima
                    </div>

                    <div class="kpi-number blue">

                        {{
                            number_format(
                                $velocidadMaxima,
                                2
                            )
                        }}

                    </div>

                    <div class="kpi-unit">
                        km/h
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
            Incidencias por vehículo
        </div>

        <div class="section-subtitle">
            Comparativa de excesos de velocidad y otras alertas registradas.
        </div>


        @if($grafica->isEmpty())

            <div class="empty">
                No existe información suficiente para generar la gráfica.
            </div>

        @else

            @php

                $maximoEventos =
                    max(
                        1,
                        (int)
                        $grafica->max(
                            'total'
                        )
                    );

            @endphp


            <div class="chart-box">

                <table class="chart-table">

                    @foreach($grafica as $item)

                        @php

                            $total =
                                (int)
                                $item['total'];


                            $excesos =
                                (int)
                                $item['excesos'];


                            $alertas =
                                (int)
                                $item['alertas'];


                            $porcentajeTotal =
                                $total > 0
                                    ?
                                    min(
                                        100,
                                        (
                                            $total
                                            /
                                            $maximoEventos
                                        )
                                        *
                                        100
                                    )
                                    :
                                    0;


                            $porcentajeExcesos =
                                $total > 0
                                    ?
                                    (
                                        $excesos
                                        /
                                        $total
                                    )
                                    *
                                    100
                                    :
                                    0;


                            $porcentajeAlertas =
                                $total > 0
                                    ?
                                    (
                                        $alertas
                                        /
                                        $total
                                    )
                                    *
                                    100
                                    :
                                    0;

                        @endphp


                        <tr>

                            <td class="chart-label">

                                <div class="chart-name">
                                    {{ $item['vehiculo'] }}
                                </div>

                                <div class="chart-plate">

                                    {{
                                        $item['placas']
                                        ?:
                                        'Sin placas'
                                    }}

                                </div>

                            </td>


                            <td class="chart-bar-cell">

                                <div class="chart-track">

                                    @if($total > 0)

                                        <div
                                            class="chart-total"

                                            style="
                                                width:
                                                {{ $porcentajeTotal }}%;
                                            "
                                        >

                                            @if($excesos > 0)

                                                <div
                                                    class="chart-red"

                                                    style="
                                                        width:
                                                        {{ $porcentajeExcesos }}%;
                                                    "
                                                ></div>

                                            @endif


                                            @if($alertas > 0)

                                                <div
                                                    class="chart-amber"

                                                    style="
                                                        width:
                                                        {{ $porcentajeAlertas }}%;
                                                    "
                                                ></div>

                                            @endif

                                        </div>

                                    @endif

                                </div>

                            </td>


                            <td class="chart-values">

                                <span class="excesos">
                                    {{ $excesos }} excesos
                                </span>

                                <br>

                                <span class="alertas">
                                    {{ $alertas }} alertas
                                </span>

                            </td>

                        </tr>

                    @endforeach

                </table>


                <div class="legend">

                    <span class="legend-item">

                        <span class="legend-red">
                            ●
                        </span>

                        Excesos de velocidad

                    </span>


                    <span class="legend-item">

                        <span class="legend-amber">
                            ●
                        </span>

                        Otras alertas

                    </span>

                </div>

            </div>

        @endif

    </div>


    {{-- ====================================================== --}}
    {{-- EVENTOS --}}
    {{-- ====================================================== --}}

    <div class="section">

        <div class="section-title">
            Eventos registrados
        </div>

        <div class="section-subtitle">
            Detalle de excesos de velocidad y alertas durante el periodo consultado.
        </div>


        @if($eventos->isEmpty())

            <div class="empty">

                No se encontraron eventos para los criterios seleccionados.

            </div>

        @else

            <table class="report-table">

                <thead>

                    <tr>

                        <th style="width:11%;">
                            Fecha
                        </th>

                        <th style="width:17%;">
                            Vehículo
                        </th>

                        <th style="width:14%;">
                            Tipo
                        </th>

                        <th style="width:9%; text-align:right;">
                            Velocidad
                        </th>

                        <th style="width:8%; text-align:right;">
                            Límite
                        </th>

                        <th style="width:41%;">
                            Descripción
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @foreach($eventos as $evento)

                        <tr>

                            <td>

                                {{
                                    $evento['fecha']
                                        ->format(
                                            'd/m/Y H:i'
                                        )
                                }}

                            </td>


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

                                @if($evento['origen'] === 'gps')

                                    <span class="badge-speed">

                                        Exceso de velocidad

                                    </span>

                                @else

                                    <span class="badge-alert">

                                        {{ $evento['tipo'] }}

                                    </span>

                                @endif

                            </td>


                            <td
                                class="
                                    @if($evento['velocidad'] !== null)
                                        speed-value
                                    @else
                                        number
                                    @endif
                                "
                            >

                                @if($evento['velocidad'] !== null)

                                    {{
                                        number_format(
                                            $evento['velocidad'],
                                            2
                                        )
                                    }}

                                    km/h

                                @else

                                    —

                                @endif

                            </td>


                            <td class="number">

                                @if($evento['limite'] !== null)

                                    {{
                                        number_format(
                                            $evento['limite'],
                                            2
                                        )
                                    }}

                                    km/h

                                @else

                                    —

                                @endif

                            </td>


                            <td class="description">

                                {{ $evento['mensaje'] }}


                                @if($evento['excedente'] !== null)

                                    <div class="excess">

                                        Excedente:

                                        {{
                                            number_format(
                                                $evento['excedente'],
                                                2
                                            )
                                        }}

                                        km/h

                                    </div>

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