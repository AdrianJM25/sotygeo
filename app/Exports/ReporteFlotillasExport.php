<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReporteFlotillasExport implements
    FromCollection,
    WithHeadings,
    WithStyles,
    WithColumnWidths,
    WithTitle,
    WithStrictNullComparison
{
    protected Collection $resultados;
    protected int $totalVehiculos;
    protected int $totalActivos;
    protected int $totalInactivos;
    protected float $porcentajeUtilizacion;

    public function __construct(
        Collection $resultados,
        int $totalVehiculos,
        int $totalActivos,
        int $totalInactivos,
        float $porcentajeUtilizacion
    ) {
        $this->resultados =
            $resultados;

        $this->totalVehiculos =
            $totalVehiculos;

        $this->totalActivos =
            $totalActivos;

        $this->totalInactivos =
            $totalInactivos;

        $this->porcentajeUtilizacion =
            $porcentajeUtilizacion;
    }

    /*
    |--------------------------------------------------------------------------
    | DATOS
    |--------------------------------------------------------------------------
    */

    public function collection(): Collection
    {
        $filas =
            $this->resultados
                ->map(
                    function ($resultado) {

                        return [

                            $resultado['flotilla']
                                ?: 'Sin flotilla',

                            $resultado['vehiculo'],

                            $resultado['placas']
                                ?: 'Sin placas',

                            trim(
                                ($resultado['marca'] ?? '')
                                .
                                ' '
                                .
                                ($resultado['modelo'] ?? '')
                            )
                                ?: 'Sin información',

                            $resultado['estado'],

                            $resultado[
                                'registros_gps'
                            ],

                            $resultado[
                                'registros_movimiento'
                            ],

                            $resultado[
                                'porcentaje_actividad'
                            ],

                            $resultado[
                                'primer_movimiento'
                            ]
                                ? $resultado[
                                    'primer_movimiento'
                                ]->format(
                                    'd/m/Y H:i'
                                )
                                : 'Sin movimiento',

                            $resultado[
                                'ultimo_movimiento'
                            ]
                                ? $resultado[
                                    'ultimo_movimiento'
                                ]->format(
                                    'd/m/Y H:i'
                                )
                                : 'Sin movimiento',

                        ];
                    }
                );

        /*
        |--------------------------------------------------------------------------
        | TOTALES
        |--------------------------------------------------------------------------
        */

        $filas->push([

            'TOTALES',

            'Vehículos: '
            .
            $this->totalVehiculos,

            '',

            '',

            'Activos: '
            .
            $this->totalActivos,

            'Inactivos: '
            .
            $this->totalInactivos,

            '',

            'Utilización general: '
            .
            number_format(
                $this->porcentajeUtilizacion,
                2
            )
            .
            '%',

            '',

            '',

        ]);

        return $filas;
    }

    /*
    |--------------------------------------------------------------------------
    | ENCABEZADOS
    |--------------------------------------------------------------------------
    */

    public function headings(): array
    {
        return [

            'Flotilla',

            'Vehículo',

            'Placas',

            'Marca / modelo',

            'Estado',

            'Registros GPS',

            'Registros en movimiento',

            'Actividad GPS (%)',

            'Primer movimiento',

            'Último movimiento',

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | NOMBRE DE HOJA
    |--------------------------------------------------------------------------
    */

    public function title(): string
    {
        return 'Utilización de flotilla';
    }

    /*
    |--------------------------------------------------------------------------
    | ANCHO DE COLUMNAS
    |--------------------------------------------------------------------------
    */

    public function columnWidths(): array
    {
        return [

            'A' => 28,

            'B' => 30,

            'C' => 16,

            'D' => 26,

            'E' => 18,

            'F' => 18,

            'G' => 22,

            'H' => 28,

            'I' => 22,

            'J' => 22,

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | ESTILOS
    |--------------------------------------------------------------------------
    */

    public function styles(
        Worksheet $sheet
    ): ?array {
        $ultimaFila =
            $this->resultados
                ->count()
            +
            2;

        /*
        |--------------------------------------------------------------------------
        | ENCABEZADOS
        |--------------------------------------------------------------------------
        */

        $sheet
            ->getStyle(
                'A1:J1'
            )
            ->getFont()
            ->setBold(
                true
            );

        $sheet
            ->getStyle(
                'A1:J1'
            )
            ->getFont()
            ->getColor()
            ->setRGB(
                'FFFFFF'
            );

        $sheet
            ->getStyle(
                'A1:J1'
            )
            ->getFill()
            ->setFillType(
                Fill::FILL_SOLID
            )
            ->getStartColor()
            ->setRGB(
                '0056B3'
            );

        $sheet
            ->getStyle(
                'A1:J1'
            )
            ->getAlignment()
            ->setVertical(
                Alignment::VERTICAL_CENTER
            );

        $sheet
            ->getStyle(
                'A1:J1'
            )
            ->getAlignment()
            ->setHorizontal(
                Alignment::HORIZONTAL_LEFT
            );

        $sheet
            ->getRowDimension(
                1
            )
            ->setRowHeight(
                24
            );

        /*
        |--------------------------------------------------------------------------
        | BORDES
        |--------------------------------------------------------------------------
        */

        $sheet
            ->getStyle(
                'A1:J'
                .
                $ultimaFila
            )
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(
                Border::BORDER_THIN
            );

        $sheet
            ->getStyle(
                'A1:J'
                .
                $ultimaFila
            )
            ->getBorders()
            ->getAllBorders()
            ->getColor()
            ->setRGB(
                'D1D5DB'
            );

        /*
        |--------------------------------------------------------------------------
        | ALINEACIÓN GENERAL
        |--------------------------------------------------------------------------
        */

        $sheet
            ->getStyle(
                'A1:J'
                .
                $ultimaFila
            )
            ->getAlignment()
            ->setVertical(
                Alignment::VERTICAL_TOP
            );

        /*
        |--------------------------------------------------------------------------
        | FILAS ALTERNADAS
        |--------------------------------------------------------------------------
        */

        for (
            $fila = 2;
            $fila < $ultimaFila;
            $fila++
        ) {

            if (
                $fila % 2
                ===
                0
            ) {

                $sheet
                    ->getStyle(
                        'A'
                        .
                        $fila
                        .
                        ':J'
                        .
                        $fila
                    )
                    ->getFill()
                    ->setFillType(
                        Fill::FILL_SOLID
                    )
                    ->getStartColor()
                    ->setRGB(
                        'F8FAFC'
                    );
            }

            /*
            |--------------------------------------------------------------------------
            | COLORES SEGÚN ESTADO
            |--------------------------------------------------------------------------
            */

            $estado =
                $sheet
                    ->getCell(
                        'E'
                        .
                        $fila
                    )
                    ->getValue();

            if (
                $estado
                ===
                'Con actividad'
            ) {

                $sheet
                    ->getStyle(
                        'E'
                        .
                        $fila
                    )
                    ->getFont()
                    ->getColor()
                    ->setRGB(
                        '059669'
                    );

                $sheet
                    ->getStyle(
                        'E'
                        .
                        $fila
                    )
                    ->getFont()
                    ->setBold(
                        true
                    );

            } elseif (
                $estado
                ===
                'Sin actividad'
            ) {

                $sheet
                    ->getStyle(
                        'E'
                        .
                        $fila
                    )
                    ->getFont()
                    ->getColor()
                    ->setRGB(
                        'DC2626'
                    );

                $sheet
                    ->getStyle(
                        'E'
                        .
                        $fila
                    )
                    ->getFont()
                    ->setBold(
                        true
                    );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | FILA DE TOTALES
        |--------------------------------------------------------------------------
        */

        $sheet
            ->getStyle(
                'A'
                .
                $ultimaFila
                .
                ':J'
                .
                $ultimaFila
            )
            ->getFont()
            ->setBold(
                true
            );

        $sheet
            ->getStyle(
                'A'
                .
                $ultimaFila
                .
                ':J'
                .
                $ultimaFila
            )
            ->getFill()
            ->setFillType(
                Fill::FILL_SOLID
            )
            ->getStartColor()
            ->setRGB(
                'E2E8F0'
            );

        /*
        |--------------------------------------------------------------------------
        | UNIR CELDAS DEL PORCENTAJE GENERAL
        |--------------------------------------------------------------------------
        */

        $sheet
            ->mergeCells(
                'H'
                .
                $ultimaFila
                .
                ':J'
                .
                $ultimaFila
            );

        $sheet
            ->getStyle(
                'H'
                .
                $ultimaFila
                .
                ':J'
                .
                $ultimaFila
            )
            ->getAlignment()
            ->setHorizontal(
                Alignment::HORIZONTAL_CENTER
            );

        $sheet
            ->getStyle(
                'H'
                .
                $ultimaFila
                .
                ':J'
                .
                $ultimaFila
            )
            ->getAlignment()
            ->setVertical(
                Alignment::VERTICAL_CENTER
            );

        /*
        |--------------------------------------------------------------------------
        | AJUSTAR ALTURA DE TOTALES
        |--------------------------------------------------------------------------
        */

        $sheet
            ->getRowDimension(
                $ultimaFila
            )
            ->setRowHeight(
                22
            );

        /*
        |--------------------------------------------------------------------------
        | CONGELAR ENCABEZADO
        |--------------------------------------------------------------------------
        */

        $sheet
            ->freezePane(
                'A2'
            );

        /*
        |--------------------------------------------------------------------------
        | FILTROS
        |--------------------------------------------------------------------------
        */

        if (
            $this->resultados
                ->isNotEmpty()
        ) {

            $sheet
                ->setAutoFilter(
                    'A1:J'
                    .
                    (
                        $ultimaFila
                        -
                        1
                    )
                );
        }

        return [];
    }
}