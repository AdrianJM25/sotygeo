<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReporteKilometrajeExport implements
    FromCollection,
    WithHeadings,
    WithStyles,
    WithColumnWidths,
    WithTitle,
    WithStrictNullComparison
{
    private Collection $resultados;


    /*
    |--------------------------------------------------------------------------
    | CONSTRUCTOR
    |--------------------------------------------------------------------------
    */

    public function __construct(
        Collection $resultados
    ) {
        $this->resultados =
            $resultados;
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

                            $resultado[
                                'vehiculo'
                            ],

                            $resultado[
                                'placas'
                            ]
                            ?:
                            'Sin placas',

                            $resultado[
                                'marca'
                            ]
                            ??
                            '',

                            $resultado[
                                'modelo'
                            ]
                            ??
                            '',

                            $resultado[
                                'flotilla'
                            ]
                            ?:
                            'Sin flotilla',

                            round(
                                (float)
                                $resultado[
                                    'kilometros'
                                ],
                                2
                            ),

                            (int)
                            (
                                $resultado[
                                    'recorridos'
                                ]
                                ??
                                0
                            ),

                            (int)
                            (
                                $resultado[
                                    'registros_gps'
                                ]
                                ??
                                0
                            ),

                            $resultado[
                                'primer_reporte'
                            ]
                                ?
                                $resultado[
                                    'primer_reporte'
                                ]
                                ->format(
                                    'd/m/Y H:i'
                                )
                                :
                                'Sin datos',

                            $resultado[
                                'ultimo_reporte'
                            ]
                                ?
                                $resultado[
                                    'ultimo_reporte'
                                ]
                                ->format(
                                    'd/m/Y H:i'
                                )
                                :
                                'Sin datos',
                        ];
                    }
                );


        /*
        |--------------------------------------------------------------------------
        | TOTALES
        |--------------------------------------------------------------------------
        */

        $totalKilometros =
            round(
                $this->resultados
                    ->sum(
                        'kilometros'
                    ),
                2
            );


        $totalRecorridos =
            (int)
            $this->resultados
                ->sum(
                    'recorridos'
                );


        $totalGps =
            (int)
            $this->resultados
                ->sum(
                    'registros_gps'
                );


        $filas->push([

            'TOTALES',

            '',

            '',

            '',

            '',

            $totalKilometros,

            $totalRecorridos,

            $totalGps,

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

            'Vehículo',

            'Placas',

            'Marca',

            'Modelo',

            'Flotilla',

            'Kilómetros',

            'Recorridos',

            'Registros GPS',

            'Primer reporte',

            'Último reporte',

        ];
    }


    /*
    |--------------------------------------------------------------------------
    | NOMBRE DE LA HOJA
    |--------------------------------------------------------------------------
    */

    public function title(): string
    {
        return 'Kilometraje';
    }


    /*
    |--------------------------------------------------------------------------
    | ANCHO DE COLUMNAS
    |--------------------------------------------------------------------------
    */

    public function columnWidths(): array
    {
        return [

            'A' => 34,

            'B' => 15,

            'C' => 17,

            'D' => 18,

            'E' => 22,

            'F' => 15,

            'G' => 14,

            'H' => 16,

            'I' => 20,

            'J' => 20,

        ];
    }


    /*
    |--------------------------------------------------------------------------
    | ESTILOS
    |--------------------------------------------------------------------------
    */

    public function styles(
        Worksheet $sheet
    ): array {

        /*
        |--------------------------------------------------------------------------
        | FILA FINAL
        |--------------------------------------------------------------------------
        |
        | +1 por encabezado.
        | +1 por fila de totales.
        |
        */

        $ultimaFila =
            $this->resultados->count()
            +
            2;


        $ultimaFilaDatos =
            $ultimaFila
            -
            1;


        /*
        |--------------------------------------------------------------------------
        | ENCABEZADO
        |--------------------------------------------------------------------------
        */

        $sheet
            ->getStyle(
                'A1:J1'
            )
            ->getFont()
            ->setBold(
                true
            )
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
                'center'
            );


        $sheet
            ->getRowDimension(
                1
            )
            ->setRowHeight(
                26
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
            )
            ->getColor()
            ->setRGB(
                'D9E2EC'
            );


        /*
        |--------------------------------------------------------------------------
        | FILAS ALTERNADAS
        |--------------------------------------------------------------------------
        */

        for (
            $fila = 2;
            $fila <= $ultimaFilaDatos;
            $fila++
        ) {

            if (
                $fila % 2 === 0
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
                'E8F1FB'
            );


        /*
        |--------------------------------------------------------------------------
        | KILOMETRAJE
        |--------------------------------------------------------------------------
        */

        $sheet
            ->getStyle(
                'F2:F'
                .
                $ultimaFila
            )
            ->getNumberFormat()
            ->setFormatCode(
                '0.00'
            );


        /*
        |--------------------------------------------------------------------------
        | ALINEACIÓN
        |--------------------------------------------------------------------------
        */

        $sheet
            ->getStyle(
                'F2:H'
                .
                $ultimaFila
            )
            ->getAlignment()
            ->setHorizontal(
                'center'
            );


        $sheet
            ->getStyle(
                'I2:J'
                .
                $ultimaFila
            )
            ->getAlignment()
            ->setHorizontal(
                'center'
            );


        $sheet
            ->getStyle(
                'A1:J'
                .
                $ultimaFila
            )
            ->getAlignment()
            ->setVertical(
                'center'
            );


        /*
        |--------------------------------------------------------------------------
        | ALTURA DE FILAS
        |--------------------------------------------------------------------------
        */

        for (
            $fila = 2;
            $fila <= $ultimaFila;
            $fila++
        ) {

            $sheet
                ->getRowDimension(
                    $fila
                )
                ->setRowHeight(
                    22
                );
        }


        /*
        |--------------------------------------------------------------------------
        | CONGELAR ENCABEZADOS
        |--------------------------------------------------------------------------
        */

        $sheet->freezePane(
            'A2'
        );


        /*
        |--------------------------------------------------------------------------
        | FILTROS DE EXCEL
        |--------------------------------------------------------------------------
        */

        if (
            $ultimaFilaDatos
            >=
            2
        ) {

            $sheet->setAutoFilter(
                'A1:J'
                .
                $ultimaFilaDatos
            );
        }


        return [];
    }
}