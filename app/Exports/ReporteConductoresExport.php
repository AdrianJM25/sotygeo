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

class ReporteConductoresExport implements
    FromCollection,
    WithHeadings,
    WithStyles,
    WithColumnWidths,
    WithTitle,
    WithStrictNullComparison
{
    protected Collection $resultados;

    protected int $totalConductores;

    protected int $conductoresConActividad;

    protected int $conductoresSinActividad;

    protected int $totalRecorridos;

    protected float $distanciaTotal;

    public function __construct(
        Collection $resultados,
        int $totalConductores,
        int $conductoresConActividad,
        int $conductoresSinActividad,
        int $totalRecorridos,
        float $distanciaTotal
    ) {
        $this->resultados =
            $resultados;

        $this->totalConductores =
            $totalConductores;

        $this->conductoresConActividad =
            $conductoresConActividad;

        $this->conductoresSinActividad =
            $conductoresSinActividad;

        $this->totalRecorridos =
            $totalRecorridos;

        $this->distanciaTotal =
            $distanciaTotal;
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
                                'conductor'
                            ],

                            $resultado[
                                'empresa'
                            ],

                            $resultado[
                                'vehiculo'
                            ],

                            $resultado[
                                'placas'
                            ],

                            $resultado[
                                'fecha_inicio'
                            ]
                                ? $resultado[
                                    'fecha_inicio'
                                ]->format(
                                    'd/m/Y H:i'
                                )
                                : '—',

                            $resultado[
                                'fecha_fin'
                            ]
                                ? $resultado[
                                    'fecha_fin'
                                ]->format(
                                    'd/m/Y H:i'
                                )
                                : (
                                    $resultado[
                                        'estado'
                                    ] === 'En curso'
                                        ? 'En curso'
                                        : '—'
                                ),

                            $resultado[
                                'estado'
                            ],

                            $resultado[
                                'distancia_km'
                            ],

                            $resultado[
                                'duracion'
                            ],

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

            'Conductores: '
            .
            $this->totalConductores,

            'Con actividad: '
            .
            $this->conductoresConActividad,

            'Sin actividad: '
            .
            $this->conductoresSinActividad,

            'Recorridos: '
            .
            $this->totalRecorridos,

            '',

            '',

            'Distancia total: '
            .
            number_format(
                $this->distanciaTotal,
                2
            )
            .
            ' km',

            '',

        ]);

        return $filas;
    }

    public function headings(): array
    {
        return [

            'Conductor',

            'Empresa',

            'Vehículo',

            'Placas',

            'Inicio',

            'Fin',

            'Estado',

            'Distancia (km)',

            'Duración',

        ];
    }

    public function title(): string
    {
        return 'Actividad conductores';
    }

    public function columnWidths(): array
    {
        return [

            'A' => 30,

            'B' => 30,

            'C' => 32,

            'D' => 16,

            'E' => 22,

            'F' => 22,

            'G' => 18,

            'H' => 22,

            'I' => 18,

        ];
    }

    public function styles(
        Worksheet $sheet
    ): ?array {

        $ultimaFila =
            $this->resultados->count()
            +
            2;

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
            );

        $sheet
            ->getStyle(
                'A1:I1'
            )
            ->getFont()
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
                24
            );

        /*
        |--------------------------------------------------------------------------
        | TABLA
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
            );

        $sheet
            ->getStyle(
                'A1:I'
                .
                $ultimaFila
            )
            ->getBorders()
            ->getAllBorders()
            ->getColor()
            ->setRGB(
                'D1D5DB'
            );

        $sheet
            ->getStyle(
                'A1:I'
                .
                $ultimaFila
            )
            ->getAlignment()
            ->setVertical(
                Alignment::VERTICAL_TOP
            );

        /*
        |--------------------------------------------------------------------------
        | FILAS
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

            $estado =
                $sheet
                    ->getCell(
                        'G'
                        .
                        $fila
                    )
                    ->getValue();

            if (
                $estado
                ===
                'Finalizado'
            ) {

                $sheet
                    ->getStyle(
                        'G'
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
                        'G'
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
                'En curso'
            ) {

                $sheet
                    ->getStyle(
                        'G'
                        .
                        $fila
                    )
                    ->getFont()
                    ->getColor()
                    ->setRGB(
                        '2563EB'
                    );

                $sheet
                    ->getStyle(
                        'G'
                        .
                        $fila
                    )
                    ->getFont()
                    ->setBold(
                        true
                    );

            } else {

                $sheet
                    ->getStyle(
                        'G'
                        .
                        $fila
                    )
                    ->getFont()
                    ->getColor()
                    ->setRGB(
                        'DC2626'
                    );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | TOTALES
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
                'E2E8F0'
            );

        $sheet
            ->mergeCells(
                'H'
                .
                $ultimaFila
                .
                ':I'
                .
                $ultimaFila
            );

        $sheet
            ->getStyle(
                'H'
                .
                $ultimaFila
                .
                ':I'
                .
                $ultimaFila
            )
            ->getAlignment()
            ->setHorizontal(
                Alignment::HORIZONTAL_CENTER
            );

        $sheet
            ->freezePane(
                'A2'
            );

        if (
            $this->resultados
                ->isNotEmpty()
        ) {

            $sheet
                ->setAutoFilter(
                    'A1:I'
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