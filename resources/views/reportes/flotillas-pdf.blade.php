<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <title>
        Reporte de Utilización de la Flotilla
    </title>

    <style>

        @page {
            margin: 18px 20px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: DejaVu Sans, sans-serif;
            font-size: 8px;
            color: #1f2937;
        }

        .header {
            background: #0056b3;
            color: #ffffff;
            padding: 14px 16px;
            border-radius: 6px;
        }

        .header-table,
        .criteria-table,
        .kpi-table,
        .summary-table,
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .brand {
            font-size: 18px;
            font-weight: bold;
        }

        .subtitle {
            margin-top: 3px;
            font-size: 8px;
            color: #dbeafe;
        }

        .period {
            text-align: right;
            font-size: 8px;
            white-space: nowrap;
        }

        .section-title {
            margin: 10px 0 5px;
            font-size: 10px;
            font-weight: bold;
            color: #003d82;
        }

        .muted {
            color: #6b7280;
        }

        .criteria-table td {
            width: 25%;
            padding: 6px 8px;
            border: 1px solid #dbe3ec;
            vertical-align: top;
        }

        .label {
            display: block;
            margin-bottom: 2px;
            font-size: 6px;
            color: #94a3b8;
            text-transform: uppercase;
        }

        .value {
            font-size: 8px;
            font-weight: bold;
            color: #334155;
        }

        .kpi-table {
            margin-top: 4px;
        }

        .kpi-table td {
            width: 25%;
            padding: 8px;
            border: 1px solid #dbe3ec;
            background: #f8fafc;
        }

        .kpi-label {
            font-size: 6px;
            color: #64748b;
        }

        .kpi-value {
            margin-top: 3px;
            font-size: 14px;
            font-weight: bold;
            color: #0056b3;
        }

        .green {
            color: #059669 !important;
        }

        .red {
            color: #dc2626 !important;
        }

        .summary-box {
            margin-top: 5px;
            padding: 8px;
            border: 1px solid #dbe3ec;
            border-radius: 5px;
        }

        .summary-table td {
            padding: 3px 4px;
            vertical-align: middle;
        }

        .bar-bg {
            height: 7px;
            background: #e5e7eb;
            border-radius: 4px;
        }

        .bar-active {
            height: 7px;
            background: #059669;
            border-radius: 4px;
        }

        .bar-inactive {
            height: 7px;
            background: #dc2626;
            border-radius: 4px;
        }

        .data-table {
            margin-top: 5px;
        }

        .data-table th {
            background: #eaf1f8;
            color: #334155;
            padding: 5px 4px;
            border: 1px solid #d6dee8;
            text-align: left;
            font-size: 6.5px;
        }

        .data-table td {
            padding: 5px 4px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
            font-size: 6.5px;
        }

        .row-alt {
            background: #fafcff;
        }

        .status-active {
            color: #059669;
            font-weight: bold;
        }

        .status-inactive {
            color: #dc2626;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .nowrap {
            white-space: nowrap;
        }

        .footer {
            margin-top: 9px;
            padding-top: 5px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #94a3b8;
            font-size: 6px;
        }

    </style>

</head>


<body>

    {{-- ENCABEZADO --}}
    <div class="header">

        <table class="header-table">

            <tr>

                <td>

                    <div class="brand">
                        SotyGeo
                    </div>

                    <div class="subtitle">
                        Reporte de Utilización de la Flotilla
                    </div>

                </td>

                <td class="period">

                    Periodo:

                    {{ $fechaInicio->format('d/m/Y') }}

                    —

                    {{ $fechaFin->format('d/m/Y') }}

                </td>

            </tr>

        </table>

    </div>


    {{-- CRITERIOS --}}
    <div class="section-title">
        Criterios del reporte
    </div>

    <table class="criteria-table">

        <tr>

            <td>

                <span class="label">
                    Fecha inicial
                </span>

                <span class="value">
                    {{ $fechaInicio->format('d/m/Y') }}
                </span>

            </td>


            <td>

                <span class="label">
                    Fecha final
                </span>

                <span class="value">
                    {{ $fechaFin->format('d/m/Y') }}
                </span>

            </td>


            <td>

                <span class="label">
                    Flotilla
                </span>

                <span class="value">
                    {{
                        $flotillaSeleccionada?->nombre
                        ??
                        'Todas'
                    }}
                </span>

            </td>


            <td>

                <span class="label">
                    Generado por
                </span>

                <span class="value">
                    {{ auth()->user()->email }}
                </span>

            </td>

        </tr>

    </table>


    {{-- RESUMEN --}}
    <div class="section-title">
        Resumen general
    </div>

    <table class="kpi-table">

        <tr>

            <td>

                <div class="kpi-label">
                    Vehículos analizados
                </div>

                <div class="kpi-value">
                    {{ $totalVehiculos }}
                </div>

            </td>


            <td>

                <div class="kpi-label">
                    Con actividad
                </div>

                <div class="kpi-value green">
                    {{ $totalActivos }}
                </div>

            </td>


            <td>

                <div class="kpi-label">
                    Sin actividad
                </div>

                <div class="kpi-value red">
                    {{ $totalInactivos }}
                </div>

            </td>


            <td>

                <div class="kpi-label">
                    Utilización de flotilla
                </div>

                <div class="kpi-value">

                    {{
                        number_format(
                            $porcentajeUtilizacion,
                            2
                        )
                    }}%

                </div>

            </td>

        </tr>

    </table>


    @php

        $porcentajeActivos =
            $totalVehiculos > 0
                ? (
                    $totalActivos
                    /
                    $totalVehiculos
                ) * 100
                : 0;

        $porcentajeInactivos =
            $totalVehiculos > 0
                ? (
                    $totalInactivos
                    /
                    $totalVehiculos
                ) * 100
                : 0;

    @endphp


    {{-- RESULTADO VISUAL --}}
    <div class="section-title">
        Resultado de utilización
    </div>


    <div class="summary-box">

        <table class="summary-table">

            <tr>

                <td style="width: 17%;">

                    <strong>
                        Con actividad
                    </strong>

                    <br>

                    <span class="muted">
                        {{ $totalActivos }} vehículo(s)
                    </span>

                </td>

                <td style="width: 73%;">

                    <div class="bar-bg">

                        <div
                            class="bar-active"
                            style="width:
                            {{
                                min(
                                    100,
                                    $porcentajeActivos
                                )
                            }}%;"
                        >
                        </div>

                    </div>

                </td>

                <td
                    style="
                        width: 10%;
                        text-align: right;
                    "
                >

                    <strong>

                        {{
                            number_format(
                                $porcentajeActivos,
                                2
                            )
                        }}%

                    </strong>

                </td>

            </tr>


            <tr>

                <td>

                    <strong>
                        Sin actividad
                    </strong>

                    <br>

                    <span class="muted">
                        {{ $totalInactivos }} vehículo(s)
                    </span>

                </td>

                <td>

                    <div class="bar-bg">

                        <div
                            class="bar-inactive"
                            style="width:
                            {{
                                min(
                                    100,
                                    $porcentajeInactivos
                                )
                            }}%;"
                        >
                        </div>

                    </div>

                </td>

                <td style="text-align: right;">

                    <strong>

                        {{
                            number_format(
                                $porcentajeInactivos,
                                2
                            )
                        }}%

                    </strong>

                </td>

            </tr>

        </table>

    </div>


    {{-- TABLA --}}
    <div class="section-title">
        Detalle de utilización
    </div>


    <div
        class="muted"
        style="margin-bottom: 4px;"
    >

        Se analizaron

        {{ $totalRegistrosGps }}

        registros GPS;

        {{ $totalRegistrosMovimiento }}

        correspondieron a movimiento.

    </div>


    <table class="data-table">

        <thead>

            <tr>

                <th>
                    Flotilla
                </th>

                <th>
                    Vehículo
                </th>

                <th>
                    Placas
                </th>

                <th>
                    Estado
                </th>

                <th class="text-center">
                    GPS
                </th>

                <th class="text-center">
                    Movimiento
                </th>

                <th class="text-center">
                    Actividad GPS
                </th>

                <th>
                    Primer movimiento
                </th>

                <th>
                    Último movimiento
                </th>

            </tr>

        </thead>


        <tbody>

            @forelse($resultados as $resultado)

                <tr
                    class="{{
                        $loop->even
                            ? 'row-alt'
                            : ''
                    }}"
                >

                    <td>

                        {{
                            $resultado['flotilla']
                            ?:
                            'Sin flotilla'
                        }}

                    </td>


                    <td>

                        <strong>
                            {{ $resultado['vehiculo'] }}
                        </strong>

                        <br>

                        <span class="muted">

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
                                ?:
                                'Sin marca/modelo'
                            }}

                        </span>

                    </td>


                    <td>

                        {{
                            $resultado['placas']
                            ?:
                            'Sin placas'
                        }}

                    </td>


                    <td
                        class="{{
                            $resultado['con_actividad']
                                ? 'status-active'
                                : 'status-inactive'
                        }}"
                    >

                        {{ $resultado['estado'] }}

                    </td>


                    <td class="text-center">

                        {{
                            $resultado[
                                'registros_gps'
                            ]
                        }}

                    </td>


                    <td class="text-center">

                        {{
                            $resultado[
                                'registros_movimiento'
                            ]
                        }}

                    </td>


                    <td class="text-center">

                        {{
                            number_format(
                                $resultado[
                                    'porcentaje_actividad'
                                ],
                                2
                            )
                        }}%

                    </td>


                    <td class="nowrap">

                        {{
                            $resultado[
                                'primer_movimiento'
                            ]
                                ? $resultado[
                                    'primer_movimiento'
                                ]->format(
                                    'd/m/Y H:i'
                                )
                                : 'Sin movimiento'
                        }}

                    </td>


                    <td class="nowrap">

                        {{
                            $resultado[
                                'ultimo_movimiento'
                            ]
                                ? $resultado[
                                    'ultimo_movimiento'
                                ]->format(
                                    'd/m/Y H:i'
                                )
                                : 'Sin movimiento'
                        }}

                    </td>

                </tr>

            @empty

                <tr>

                    <td
                        colspan="9"
                        class="text-center"
                    >
                        No se encontraron vehículos
                        para los filtros seleccionados.
                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>


    {{-- FOOTER --}}
    <div class="footer">

        SotyGeo · Sistema Web de Rastreo GPS y Gestión de Flotas

        · Documento generado el

        {{ now()->format('d/m/Y H:i') }}

    </div>

</body>

</html>