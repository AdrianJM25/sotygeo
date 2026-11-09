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

class ReporteAlertasExport implements
    FromCollection,
    WithHeadings,
    WithStyles,
    WithColumnWidths,
    WithTitle,
    WithStrictNullComparison
{
    private Collection $eventos;


    /*
    |--------------------------------------------------------------------------
    | CONSTRUCTOR
    |--------------------------------------------------------------------------
    */

    public function __construct(
        Collection $eventos
    ) {
        $this->eventos =
            $eventos;
    }


    /*
    |--------------------------------------------------------------------------
    | DATOS
    |--------------------------------------------------------------------------
    */

    public function collection(): Collection
    {
        $filas =
            $this->eventos
                ->map(
                    function ($evento) {

                        /*
                        |--------------------------------------------------------------------------
                        | ESTADO
                        |--------------------------------------------------------------------------
                        */

                        if (
                            $evento['origen']
                            ===
                            'gps'
                        ) {

                            $estado =
                                'No aplica';

                        } else {

                            $estado =
                                $evento['leida']
                                    ?
                                    'Leída'
                                    :
                                    'No leída';
                        }


                        return [

                            /*
                            | Fecha
                            */
                            $evento['fecha']
                                ->format(
                                    'd/m/Y H:i'
                                ),

                            /*
                            | Vehículo
                            */
                            $evento[
                                'vehiculo'
                            ],

                            /*
                            | Placas
                            */
                            $evento[
                                'placas'
                            ]
                            ?:
                            'Sin placas',

                            /*
                            | Flotilla
                            */
                            $evento[
                                'flotilla'
                            ]
                            ?:
                            'Sin flotilla',

                            /*
                            | Tipo
                            */
                            $evento[
                                'tipo'
                            ],

                            /*
                            | Velocidad
                            */
                            $evento[
                                'velocidad'
                            ]
                            !==
                            null

                                ?

                                round(
                                    (float)
                                    $evento[
                                        'velocidad'
                                    ],
                                    2
                                )

                                :

                                null,

                            /*
                            | Límite
                            */
                            $evento[
                                'limite'
                            ]
                            !==
                            null

                                ?

                                round(
                                    (float)
                                    $evento[
                                        'limite'
                                    ],
                                    2
                                )

                                :

                                null,

                            /*
                            | Excedente
                            */
                            $evento[
                                'excedente'
                            ]
                            !==
                            null

                                ?

                                round(
                                    (float)
                                    $evento[
                                        'excedente'
                                    ],
                                    2
                                )

                                :

                                null,

                            /*
                            | Descripción
                            */
                            $evento[
                                'mensaje'
                            ],

                            /*
                            | Estado
                            */
                            $estado,
                        ];
                    }
                );


        /*
        |--------------------------------------------------------------------------
        | RESUMEN FINAL
        |--------------------------------------------------------------------------
        */

        $totalEventos =
            $this->eventos
                ->count();


        $totalExcesos =
            $this->eventos
                ->where(
                    'origen',
                    'gps'
                )
                ->count();


        $totalAlertas =
            $this->eventos
                ->where(
                    'origen',
                    'alerta'
                )
                ->count();


        $filas->push([

            'TOTALES',

            '',

            '',

            '',

            'Eventos: '
            .
            $totalEventos,

            null,

            null,

            null,

            'Excesos de velocidad: '
            .
            $totalExcesos
            .
            ' | Otras alertas: '
            .
            $totalAlertas,

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

            'Flotilla',

            'Tipo de evento',

            'Velocidad (km/h)',

            'Límite (km/h)',

            'Excedente (km/h)',

            'Descripción',

            'Estado',

        ];
    }


    /*
    |--------------------------------------------------------------------------
    | NOMBRE DE LA HOJA
    |--------------------------------------------------------------------------
    */

    public function title(): string
    {
        return 'Alertas';
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

            'B' => 34,

            'C' => 15,

            'D' => 22,

            'E' => 24,

            'F' => 18,

            'G' => 17,

            'H' => 19,

            'I' => 60,

            'J' => 15,

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
        | FILAS
        |--------------------------------------------------------------------------
        */

        $ultimaFila =
            $this->eventos->count()
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
            ->getRowDimension(
                1
            )
            ->setRowHeight(
                27
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
        | COLOREAR EXCESOS DE VELOCIDAD
        |--------------------------------------------------------------------------
        */

        for (
            $indice = 0;
            $indice < $this->eventos->count();
            $indice++
        ) {

            $evento =
                $this->eventos[
                    $indice
                ];


            $filaExcel =
                $indice
                +
                2;


            if (
                $evento[
                    'origen'
                ]
                ===
                'gps'
            ) {

                /*
                | Tipo
                */

                $sheet
                    ->getStyle(
                        'E'
                        .
                        $filaExcel
                    )
                    ->getFont()
                    ->setBold(
                        true
                    )
                    ->getColor()
                    ->setRGB(
                        'DC2626'
                    );


                /*
                | Velocidad
                */

                $sheet
                    ->getStyle(
                        'F'
                        .
                        $filaExcel
                    )
                    ->getFont()
                    ->setBold(
                        true
                    )
                    ->getColor()
                    ->setRGB(
                        'DC2626'
                    );


                /*
                | Excedente
                */

                $sheet
                    ->getStyle(
                        'H'
                        .
                        $filaExcel
                    )
                    ->getFont()
                    ->setBold(
                        true
                    )
                    ->getColor()
                    ->setRGB(
                        'DC2626'
                    );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | FORMATO NUMÉRICO
        |--------------------------------------------------------------------------
        */

        if (
            $ultimaFilaDatos
            >=
            2
        ) {

            $sheet
                ->getStyle(
                    'F2:H'
                    .
                    $ultimaFilaDatos
                )
                ->getNumberFormat()
                ->setFormatCode(
                    '0.00'
                );
        }


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
                'J2:J'
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
        | DESCRIPCIÓN
        |--------------------------------------------------------------------------
        */

        $sheet
            ->getStyle(
                'I2:I'
                .
                $ultimaFila
            )
            ->getAlignment()
            ->setWrapText(
                true
            );


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
                    30
                );
        }


        /*
        |--------------------------------------------------------------------------
        | CONGELAR ENCABEZADO
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