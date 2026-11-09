<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <title>
        Reporte de Actividad por Conductor
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
            font-size: 13px;
            font-weight: bold;
            color: #0056b3;
        }

        .green {
            color: #059669 !important;
        }

        .red {
            color: #dc2626 !important;
        }

        .summary-table {
            margin-top: 5px;
        }

        .summary-table th {
            background: #eef4fa;
            color: #334155;
            padding: 5px;
            border: 1px solid #d6dee8;
            text-align: left;
            font-size: 6.5px;
        }

        .summary-table td {
            padding: 5px;
            border: 1px solid #e2e8f0;
            font-size: 6.5px;
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

        .status-success {
            color: #059669;
            font-weight: bold;
        }

        .status-info {
            color: #2563eb;
            font-weight: bold;
        }

        .status-danger {
            color: #dc2626;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
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

    {{-- ====================================================== --}}
    {{-- ENCABEZADO --}}
    {{-- ====================================================== --}}

    <div class="header">

        <table class="header-table">

            <tr>

                <td>

                    <div class="brand">
                        SotyGeo
                    </div>

                    <div class="subtitle">
                        Reporte de Actividad por Conductor
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


    {{-- ====================================================== --}}
    {{-- CRITERIOS --}}
    {{-- ====================================================== --}}

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
                    Conductor
                </span>

                <span class="value">

                    @if($conductorSeleccionado)

                        {{
                            trim(
                                $conductorSeleccionado->nombre
                                . ' '
                                . (
                                    $conductorSeleccionado
                                        ->apellido_paterno
                                    ??
                                    ''
                                )
                                . ' '
                                . (
                                    $conductorSeleccionado
                                        ->apellido_materno
                                    ??
                                    ''
                                )
                            )
                        }}

                    @else

                        Todos

                    @endif

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


    {{-- ====================================================== --}}
    {{-- RESUMEN GENERAL --}}
    {{-- ====================================================== --}}

    <div class="section-title">
        Resumen general
    </div>

    <table class="kpi-table">

        <tr>

            <td>

                <div class="kpi-label">
                    Conductores analizados
                </div>

                <div class="kpi-value">
                    {{ $totalConductores }}
                </div>

            </td>


            <td>

                <div class="kpi-label">
                    Con actividad
                </div>

                <div class="kpi-value green">
                    {{ $conductoresConActividad }}
                </div>

            </td>


            <td>

                <div class="kpi-label">
                    Sin actividad
                </div>

                <div class="kpi-value red">
                    {{ $conductoresSinActividad }}
                </div>

            </td>


            <td>

                <div class="kpi-label">
                    Recorridos
                </div>

                <div class="kpi-value">
                    {{ $totalRecorridos }}
                </div>

            </td>


            <td>

                <div class="kpi-label">
                    Distancia total
                </div>

                <div class="kpi-value">
                    {{ number_format($distanciaTotal, 2) }}
                    km
                </div>

            </td>

        </tr>

    </table>


    {{-- ====================================================== --}}
    {{-- RESUMEN POR CONDUCTOR --}}
    {{-- ====================================================== --}}

    <div class="section-title">
        Resumen por conductor
    </div>


    <table class="summary-table">

        <thead>

            <tr>

                <th>
                    Conductor
                </th>

                <th>
                    Vehículo
                </th>

                <th class="text-center">
                    Recorridos
                </th>

                <th class="text-right">
                    Distancia
                </th>

                <th>
                    Actividad
                </th>

            </tr>

        </thead>


        <tbody>

            @forelse($resumenConductores as $resumen)

                <tr
                    class="{{
                        $loop->even
                            ? 'row-alt'
                            : ''
                    }}"
                >

                    <td>
                        <strong>
                            {{ $resumen['conductor'] }}
                        </strong>
                    </td>


                    <td>
                        {{ $resumen['vehiculo'] }}
                    </td>


                    <td class="text-center">
                        {{ $resumen['recorridos'] }}
                    </td>


                    <td class="text-right">

                        {{
                            number_format(
                                $resumen['distancia_km'],
                                2
                            )
                        }}
                        km

                    </td>


                    <td
                        class="{{
                            $resumen['tiene_actividad']
                                ? 'status-success'
                                : 'status-danger'
                        }}"
                    >

                        {{
                            $resumen['tiene_actividad']
                                ? 'Con actividad'
                                : 'Sin actividad'
                        }}

                    </td>

                </tr>

            @empty

                <tr>

                    <td
                        colspan="5"
                        class="text-center"
                    >
                        No se encontraron conductores
                        para los filtros seleccionados.
                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>


    {{-- ====================================================== --}}
    {{-- DETALLE DE ACTIVIDAD --}}
    {{-- ====================================================== --}}

    <div class="section-title">
        Detalle de actividad
    </div>


    <div
        class="muted"
        style="margin-bottom: 4px;"
    >

        Se identificaron

        {{ $totalRecorridos }}

        recorrido(s) asociados a

        {{ $vehiculosUtilizados }}

        vehículo(s).

    </div>


    <table class="data-table">

        <thead>

            <tr>

                <th>
                    Conductor
                </th>

                <th>
                    Vehículo
                </th>

                <th>
                    Placas
                </th>

                <th>
                    Inicio
                </th>

                <th>
                    Fin
                </th>

                <th>
                    Estado
                </th>

                <th class="text-right">
                    Distancia
                </th>

                <th>
                    Duración
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

                        <strong>
                            {{ $resultado['conductor'] }}
                        </strong>

                        <br>

                        <span class="muted">
                            {{ $resultado['empresa'] }}
                        </span>

                    </td>


                    <td>
                        {{ $resultado['vehiculo'] }}
                    </td>


                    <td>
                        {{ $resultado['placas'] }}
                    </td>


                    <td class="nowrap">

                        {{
                            $resultado['fecha_inicio']
                                ? $resultado[
                                    'fecha_inicio'
                                ]->format(
                                    'd/m/Y H:i'
                                )
                                : '—'
                        }}

                    </td>


                    <td class="nowrap">

                        @if($resultado['fecha_fin'])

                            {{
                                $resultado[
                                    'fecha_fin'
                                ]->format(
                                    'd/m/Y H:i'
                                )
                            }}

                        @elseif(
                            $resultado['estado']
                            ===
                            'En curso'
                        )

                            En curso

                        @else

                            —

                        @endif

                    </td>


                    <td
                        class="
                        @if(
                            $resultado['estado']
                            ===
                            'Finalizado'
                        )
                            status-success
                        @elseif(
                            $resultado['estado']
                            ===
                            'En curso'
                        )
                            status-info
                        @else
                            status-danger
                        @endif
                        "
                    >

                        {{ $resultado['estado'] }}

                    </td>


                    <td class="text-right nowrap">

                        {{
                            number_format(
                                $resultado[
                                    'distancia_km'
                                ],
                                2
                            )
                        }}
                        km

                    </td>


                    <td class="nowrap">
                        {{ $resultado['duracion'] }}
                    </td>

                </tr>

            @empty

                <tr>

                    <td
                        colspan="8"
                        class="text-center"
                    >
                        No se encontró actividad
                        durante el periodo seleccionado.
                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>


    {{-- ====================================================== --}}
    {{-- PIE --}}
    {{-- ====================================================== --}}

    <div class="footer">

        SotyGeo · Sistema Web de Rastreo GPS y Gestión de Flotas

        · Documento generado el

        {{ now()->format('d/m/Y H:i') }}

    </div>

</body>

</html>