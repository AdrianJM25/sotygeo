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

class ReporteInspeccionesExport implements
    FromCollection,
    WithHeadings,
    WithStyles,
    WithColumnWidths,
    WithTitle,
    WithStrictNullComparison
{
    protected Collection $inspecciones;

    public function __construct(
        Collection $inspecciones
    ) {
        $this->inspecciones =
            $inspecciones;
    }


    /*
    |--------------------------------------------------------------------------
    | DATOS
    |--------------------------------------------------------------------------
    */

    public function collection(): Collection
    {
        $filas =
            $this->inspecciones
                ->map(
                    function ($inspeccion) {

                        $detalleIncidencias =
                            $inspeccion['incidencias']
                                ->map(
                                    function ($incidencia) {

                                        $texto =
                                            $incidencia[
                                                'elemento'
                                            ];

                                        if (
                                            !empty(
                                                $incidencia[
                                                    'observaciones'
                                                ]
                                            )
                                        ) {

                                            $texto .=
                                                ': '
                                                .
                                                $incidencia[
                                                    'observaciones'
                                                ];
                                        }

                                        return $texto;
                                    }
                                )
                                ->implode(
                                    ' | '
                                );


                        if (
                            $detalleIncidencias
                            ===
                            ''
                        ) {

                            $detalleIncidencias =
                                'Sin incidencias';
                        }


                        return [

                            $inspeccion[
                                'fecha'
                            ]->format(
                                'd/m/Y H:i'
                            ),

                            $inspeccion[
                                'vehiculo'
                            ],

                            $inspeccion[
                                'placas'
                            ]
                            ?:
                            'Sin placas',

                            $inspeccion[
                                'conductor'
                            ],

                            $inspeccion[
                                'resultado_texto'
                            ],

                            $inspeccion[
                                'total_incidencias'
                            ],

                            $detalleIncidencias,

                            $inspeccion[
                                'observaciones'
                            ]
                            ?:
                            'Sin observaciones',

                        ];
                    }
                );


        /*
        |--------------------------------------------------------------------------
        | TOTALES
        |--------------------------------------------------------------------------
        */

        $total =
            $this->inspecciones
                ->count();


        $correctas =
            $this->inspecciones
                ->where(
                    'resultado',
                    'correcto'
                )
                ->count();


        $conIncidencias =
            $this->inspecciones
                ->where(
                    'resultado',
                    'con_incidencias'
                )
                ->count();


        $incidencias =
            (int)
            $this->inspecciones
                ->sum(
                    'total_incidencias'
                );


        $filas->push([

            'TOTALES',

            'Inspecciones: '
            .
            $total,

            '',

            '',

            'Correctas: '
            .
            $correctas,

            $incidencias,

            'Con incidencias: '
            .
            $conIncidencias,

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

            'Fecha',

            'Vehículo',

            'Placas',

            'Conductor',

            'Resultado',

            'N.º Incidencias',

            'Detalle de incidencias',

            'Observaciones',

        ];
    }


    /*
    |--------------------------------------------------------------------------
    | NOMBRE DE HOJA
    |--------------------------------------------------------------------------
    */

    public function title(): string
    {
        return 'Historial inspecciones';
    }


    /*
    |--------------------------------------------------------------------------
    | ANCHO DE COLUMNAS
    |--------------------------------------------------------------------------
    */

    public function columnWidths(): array
    {
        return [

            'A' => 20,

            'B' => 32,

            'C' => 16,

            'D' => 30,

            'E' => 20,

            'F' => 16,

            'G' => 55,

            'H' => 45,

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
            $this->inspecciones
                ->count()
            +
            2;


        /*
        |--------------------------------------------------------------------------
        | ENCABEZADO
        |--------------------------------------------------------------------------
        */

        $sheet
            ->getStyle(
                'A1:H1'
            )
            ->getFont()
            ->setBold(
                true
            );


        $sheet
            ->getStyle(
                'A1:H1'
            )
            ->getFont()
            ->getColor()
            ->setRGB(
                'FFFFFF'
            );


        $sheet
            ->getStyle(
                'A1:H1'
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
                'A1:H1'
            )
            ->getAlignment()
            ->setVertical(
                Alignment::VERTICAL_CENTER
            );


        $sheet
            ->getStyle(
                'A1:H1'
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
        | TABLA COMPLETA
        |--------------------------------------------------------------------------
        */

        $sheet
            ->getStyle(
                'A1:H'
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
                'A1:H'
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
        | AJUSTE DE TEXTO
        |--------------------------------------------------------------------------
        */

        $sheet
            ->getStyle(
                'A1:H'
                .
                $ultimaFila
            )
            ->getAlignment()
            ->setVertical(
                Alignment::VERTICAL_TOP
            );


        $sheet
            ->getStyle(
                'G2:H'
                .
                $ultimaFila
            )
            ->getAlignment()
            ->setWrapText(
                true
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
                        ':H'
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
        | COLORES SEGÚN RESULTADO
        |--------------------------------------------------------------------------
        */

        for (
            $fila = 2;
            $fila < $ultimaFila;
            $fila++
        ) {

            $resultado =
                $sheet
                    ->getCell(
                        'E'
                        .
                        $fila
                    )
                    ->getValue();


            if (
                $resultado
                ===
                'Correcta'
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
                $resultado
                ===
                'Con incidencias'
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
                ':H'
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
                ':H'
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
            $this->inspecciones
                ->isNotEmpty()
        ) {

            $sheet
                ->setAutoFilter(
                    'A1:H'
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