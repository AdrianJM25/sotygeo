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

class ReporteZonasExport implements
    FromCollection,
    WithHeadings,
    WithStyles,
    WithColumnWidths,
    WithTitle,
    WithStrictNullComparison
{
    private Collection $eventos;


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

                        return [

                            $evento['vehiculo'],

                            $evento['placas']
                                ?:
                                'Sin placas',

                            $evento['flotilla']
                                ?:
                                'Sin flotilla',

                            $evento['zona'],

                            $evento['fecha_entrada']
                                ->format(
                                    'd/m/Y H:i'
                                ),

                            $evento['fecha_salida']
                                ?
                                $evento['fecha_salida']
                                    ->format(
                                        'd/m/Y H:i'
                                    )
                                :
                                'Aún en zona',

                            (int)
                            $evento['duracion_minutos'],

                            $evento['duracion_formateada'],

                            $evento['estado'],

                        ];
                    }
                );


        /*
        |--------------------------------------------------------------------------
        | TOTALES
        |--------------------------------------------------------------------------
        */

        $totalVisitas =
            $this->eventos
                ->count();


        $totalMinutos =
            (int)
            $this->eventos
                ->sum(
                    'duracion_minutos'
                );


        $filas->push([

            'TOTALES',

            '',

            '',

            'Visitas: '
            .
            $totalVisitas,

            '',

            '',

            $totalMinutos,

            $this->formatearDuracion(
                $totalMinutos
            ),

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

            'Flotilla',

            'Zona',

            'Entrada',

            'Salida',

            'Minutos',

            'Permanencia',

            'Estado',

        ];
    }


    /*
    |--------------------------------------------------------------------------
    | NOMBRE DE HOJA
    |--------------------------------------------------------------------------
    */

    public function title(): string
    {
        return 'Visitas en zonas';
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

            'C' => 22,

            'D' => 24,

            'E' => 20,

            'F' => 20,

            'G' => 13,

            'H' => 18,

            'I' => 16,

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
                'A1:I1'
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
                'A1:I1'
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
                'A1:I'
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
                        ':I'
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
        | ESTADO
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
                $evento['estado']
                ===
                'En zona'
            ) {

                $sheet
                    ->getStyle(
                        'I'
                        .
                        $filaExcel
                    )
                    ->getFont()
                    ->setBold(
                        true
                    )
                    ->getColor()
                    ->setRGB(
                        '059669'
                    );

            } else {

                $sheet
                    ->getStyle(
                        'I'
                        .
                        $filaExcel
                    )
                    ->getFont()
                    ->setBold(
                        true
                    )
                    ->getColor()
                    ->setRGB(
                        '64748B'
                    );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | MINUTOS
        |--------------------------------------------------------------------------
        */

        if (
            $ultimaFilaDatos
            >=
            2
        ) {

            $sheet
                ->getStyle(
                    'G2:G'
                    .
                    $ultimaFilaDatos
                )
                ->getNumberFormat()
                ->setFormatCode(
                    '0'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | ALINEACIÓN
        |--------------------------------------------------------------------------
        */

        $sheet
            ->getStyle(
                'E2:I'
                .
                $ultimaFila
            )
            ->getAlignment()
            ->setHorizontal(
                'center'
            );


        $sheet
            ->getStyle(
                'A1:I'
                .
                $ultimaFila
            )
            ->getAlignment()
            ->setVertical(
                'center'
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
                ':I'
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
                ':I'
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
                    24
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
        | FILTROS
        |--------------------------------------------------------------------------
        */

        if (
            $ultimaFilaDatos
            >=
            2
        ) {

            $sheet->setAutoFilter(
                'A1:I'
                .
                $ultimaFilaDatos
            );
        }


        return [];
    }


    /*
    |--------------------------------------------------------------------------
    | FORMATEAR DURACIÓN
    |--------------------------------------------------------------------------
    */

    private function formatearDuracion(
        int $minutos
    ): string {

        $minutos =
            max(
                0,
                $minutos
            );


        $horas =
            intdiv(
                $minutos,
                60
            );


        $restantes =
            $minutos
            %
            60;


        if (
            $horas === 0
        ) {

            return
                $restantes
                .
                ' min';
        }


        if (
            $restantes === 0
        ) {

            return
                $horas
                .
                ' h';
        }


        return
            $horas
            .
            ' h '
            .
            $restantes
            .
            ' min';
    }
}