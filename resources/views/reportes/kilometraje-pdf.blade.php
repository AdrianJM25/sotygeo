<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <title>
        Reporte de Kilometraje y Recorridos
    </title>


    <style>

        @page {
            margin: 22px 26px;
        }


        * {
            box-sizing: border-box;
        }


        body {
            margin: 0;

            font-family:
                DejaVu Sans,
                sans-serif;

            font-size: 10px;

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

            padding: 18px 20px;

            border-radius: 9px;

            margin-bottom: 16px;
        }


        .header-table {
            width: 100%;

            border-collapse: collapse;
        }


        .header-table td {
            vertical-align: middle;
        }


        .brand {
            font-size: 21px;

            font-weight: bold;

            margin-bottom: 4px;
        }


        .subtitle {
            font-size: 9px;

            color: #dbeafe;
        }


        .period {
            text-align: right;

            font-size: 9px;

            line-height: 1.6;
        }


        .period strong {
            color: #ffffff;
        }


        /* ====================================================== */
        /* GENERAL */
        /* ====================================================== */

        .section {
            margin-bottom: 16px;
        }


        .section-title {
            font-size: 12px;

            font-weight: bold;

            color: #003d82;

            margin-bottom: 7px;
        }


        .section-subtitle {
            font-size: 8px;

            color: #64748b;

            margin-top: -4px;

            margin-bottom: 8px;
        }


        /* ====================================================== */
        /* CRITERIOS */
        /* ====================================================== */

        .filters-table {
            width: 100%;

            border-collapse: collapse;
        }


        .filters-table td {
            border: 1px solid #dfe5ec;

            padding: 8px 9px;

            background: #ffffff;
        }


        .filter-label {
            font-size: 7px;

            color: #94a3b8;

            text-transform: uppercase;

            margin-bottom: 3px;
        }


        .filter-value {
            font-size: 9px;

            font-weight: bold;

            color: #334155;
        }


        /* ====================================================== */
        /* KPIS */
        /* ====================================================== */

        .kpi-table {
            width: 100%;

            border-collapse: separate;

            border-spacing: 6px 0;

            margin-left: -6px;

            margin-right: -6px;
        }


        .kpi-box {
            width: 25%;

            border: 1px solid #dbe3ec;

            background: #f8fafc;

            padding: 11px 12px;

            border-radius: 7px;
        }


        .kpi-label {
            font-size: 8px;

            color: #64748b;

            margin-bottom: 4px;
        }


        .kpi-number {
            font-size: 18px;

            font-weight: bold;

            color: #0056b3;
        }


        .kpi-unit {
            font-size: 8px;

            color: #94a3b8;

            margin-top: 2px;
        }


        /* ====================================================== */
        /* GRÁFICA */
        /* ====================================================== */

        .chart-container {
            border: 1px solid #dbe3ec;

            background: #ffffff;

            padding: 12px 14px;

            border-radius: 7px;
        }


        .chart-table {
            width: 100%;

            border-collapse: collapse;
        }


        .chart-table td {
            padding: 5px 3px;

            vertical-align: middle;
        }


        .chart-label {
            width: 25%;

            font-size: 8px;

            color: #334155;

            padding-right: 8px !important;
        }


        .chart-label-name {
            font-weight: bold;
        }


        .chart-label-plate {
            margin-top: 2px;

            font-size: 7px;

            color: #94a3b8;
        }


        .chart-bar-cell {
            width: 60%;

            padding-left: 5px !important;

            padding-right: 8px !important;
        }


        .chart-track {
            width: 100%;

            height: 13px;

            background: #e8eef5;

            border-radius: 6px;

            overflow: hidden;
        }


        .chart-bar {
            height: 13px;

            background: #0056b3;

            border-radius: 6px;
        }


        .chart-value {
            width: 15%;

            text-align: right;

            font-size: 8px;

            font-weight: bold;

            color: #0056b3;
        }


        .chart-zero {
            color: #94a3b8;
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

            font-size: 7px;

            text-transform: uppercase;

            padding: 7px 5px;

            border: 1px solid #dbe3ec;

            text-align: left;
        }


        .report-table td {
            padding: 7px 5px;

            border: 1px solid #e5e7eb;

            vertical-align: top;

            font-size: 8px;
        }


        .report-table tr:nth-child(even) td {
            background: #fbfcfd;
        }


        .vehicle-name {
            font-weight: bold;

            color: #1f2937;
        }


        .vehicle-detail {
            margin-top: 2px;

            font-size: 7px;

            color: #94a3b8;
        }


        .km {
            text-align: right;

            font-weight: bold;

            color: #0056b3;
        }


        .number {
            text-align: right;
        }


        .totals td {
            background: #edf3f9 !important;

            font-weight: bold;
        }


        /* ====================================================== */
        /* SIN DATOS */
        /* ====================================================== */

        .empty {
            padding: 25px;

            text-align: center;

            border: 1px solid #dbe3ec;

            background: #f8fafc;

            color: #64748b;

            border-radius: 7px;
        }


        /* ====================================================== */
        /* FOOTER */
        /* ====================================================== */

        .footer {
            margin-top: 14px;

            padding-top: 8px;

            border-top: 1px solid #e5e7eb;

            text-align: center;

            font-size: 7px;

            color: #94a3b8;

            line-height: 1.5;
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
                        Reporte de Kilometraje y Recorridos
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
                        Vehículos analizados
                    </div>

                    <div class="kpi-number">
                        {{ $resultados->count() }}
                    </div>

                </td>


                <td class="kpi-box">

                    <div class="kpi-label">
                        Kilómetros totales
                    </div>

                    <div class="kpi-number">
                        {{ number_format($totalKilometros, 2) }}
                    </div>

                    <div class="kpi-unit">
                        km
                    </div>

                </td>


                <td class="kpi-box">

                    <div class="kpi-label">
                        Recorridos registrados
                    </div>

                    <div class="kpi-number">
                        {{ $totalRecorridos }}
                    </div>

                </td>


                <td class="kpi-box">

                    <div class="kpi-label">
                        Registros GPS
                    </div>

                    <div class="kpi-number">
                        {{ $totalRegistros }}
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
            Comparativa de kilometraje
        </div>

        <div class="section-subtitle">
            Distancia acumulada por vehículo durante el periodo seleccionado.
        </div>


        @if($resultados->isEmpty())

            <div class="empty">
                No existe información suficiente para generar la gráfica.
            </div>

        @else

            @php

                $maximoKilometros =
                    max(
                        0.01,
                        (float)
                        $resultados->max(
                            'kilometros'
                        )
                    );

            @endphp


            <div class="chart-container">

                <table class="chart-table">

                    @foreach($resultados as $resultado)

                        @php

                            $kilometros =
                                (float)
                                $resultado['kilometros'];


                            $porcentaje =
                                $kilometros > 0
                                    ?
                                    max(
                                        2,
                                        min(
                                            100,
                                            (
                                                $kilometros
                                                /
                                                $maximoKilometros
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

                                <div class="chart-label-name">

                                    {{ $resultado['vehiculo'] }}

                                </div>


                                <div class="chart-label-plate">

                                    {{
                                        $resultado['placas']
                                        ?:
                                        'Sin placas'
                                    }}

                                </div>

                            </td>


                            <td class="chart-bar-cell">

                                <div class="chart-track">

                                    @if($kilometros > 0)

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


                            <td
                                class="
                                    chart-value

                                    @if($kilometros == 0)
                                        chart-zero
                                    @endif
                                "
                            >

                                {{
                                    number_format(
                                        $kilometros,
                                        2
                                    )
                                }}

                                km

                            </td>

                        </tr>

                    @endforeach

                </table>

            </div>

        @endif

    </div>


    {{-- ====================================================== --}}
    {{-- RESULTADOS --}}
    {{-- ====================================================== --}}

    <div class="section">

        <div class="section-title">
            Resultados por vehículo
        </div>

        <div class="section-subtitle">
            Información obtenida del historial de posiciones GPS almacenadas.
        </div>


        @if($resultados->isEmpty())

            <div class="empty">

                No existe información para los criterios seleccionados.

            </div>

        @else

            <table class="report-table">

                <thead>

                    <tr>

                        <th>
                            Vehículo
                        </th>

                        <th>
                            Flotilla
                        </th>

                        <th style="text-align:right;">
                            Kilómetros
                        </th>

                        <th style="text-align:right;">
                            Recorridos
                        </th>

                        <th style="text-align:right;">
                            GPS
                        </th>

                        <th>
                            Primer reporte
                        </th>

                        <th>
                            Último reporte
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @foreach($resultados as $resultado)

                        <tr>

                            <td>

                                <div class="vehicle-name">

                                    {{ $resultado['vehiculo'] }}

                                </div>


                                <div class="vehicle-detail">

                                    {{
                                        trim(
                                            (
                                                $resultado['marca']
                                                ??
                                                ''
                                            )
                                            .
                                            ' '
                                            .
                                            (
                                                $resultado['modelo']
                                                ??
                                                ''
                                            )
                                        )
                                    }}


                                    @if($resultado['placas'])

                                        · {{ $resultado['placas'] }}

                                    @endif

                                </div>

                            </td>


                            <td>

                                {{
                                    $resultado['flotilla']
                                    ?:
                                    'Sin flotilla'
                                }}

                            </td>


                            <td class="km">

                                {{
                                    number_format(
                                        $resultado['kilometros'],
                                        2
                                    )
                                }}

                                km

                            </td>


                            <td class="number">

                                {{ $resultado['recorridos'] }}

                            </td>


                            <td class="number">

                                {{ $resultado['registros_gps'] }}

                            </td>


                            <td>

                                @if($resultado['primer_reporte'])

                                    {{
                                        $resultado['primer_reporte']
                                            ->format(
                                                'd/m/Y H:i'
                                            )
                                    }}

                                @else

                                    —

                                @endif

                            </td>


                            <td>

                                @if($resultado['ultimo_reporte'])

                                    {{
                                        $resultado['ultimo_reporte']
                                            ->format(
                                                'd/m/Y H:i'
                                            )
                                    }}

                                @else

                                    —

                                @endif

                            </td>

                        </tr>

                    @endforeach


                    <tr class="totals">

                        <td colspan="2">

                            Totales

                        </td>


                        <td class="km">

                            {{
                                number_format(
                                    $totalKilometros,
                                    2
                                )
                            }}

                            km

                        </td>


                        <td class="number">

                            {{ $totalRecorridos }}

                        </td>


                        <td class="number">

                            {{ $totalRegistros }}

                        </td>


                        <td colspan="2"></td>

                    </tr>

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