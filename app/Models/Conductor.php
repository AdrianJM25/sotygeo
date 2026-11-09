<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Conductor extends Model
{
    use HasFactory;


    /*
    |--------------------------------------------------------------------------
    | TABLA
    |--------------------------------------------------------------------------
    */

    protected $table = 'conductores';


    /*
    |--------------------------------------------------------------------------
    | CAMPOS ASIGNABLES
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'empresa_id',

        'vehiculo_id',

        'nombre',

        'apellido_paterno',

        'apellido_materno',

        'telefono',

        'email',

        'numero_licencia',

        'vigencia_licencia',

        'licencia_frente_path',

        'licencia_reverso_path',

        'activo',

    ];


    /*
    |--------------------------------------------------------------------------
    | CASTS
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [

            'vigencia_licencia' =>
                'date',

            'activo' =>
                'boolean',

        ];
    }


    /*
    |--------------------------------------------------------------------------
    | NOMBRE COMPLETO
    |--------------------------------------------------------------------------
    */

    protected function nombreCompleto(): Attribute
    {
        return Attribute::make(

            get: fn () => trim(

                "{$this->nombre} "
                .
                "{$this->apellido_paterno} "
                .
                "{$this->apellido_materno}"

            )

        );
    }


    /*
    |--------------------------------------------------------------------------
    | EMPRESA
    |--------------------------------------------------------------------------
    */

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(
            Empresa::class
        );
    }


    /*
    |--------------------------------------------------------------------------
    | VEHÍCULO
    |--------------------------------------------------------------------------
    */

    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(
            Vehiculo::class
        );
    }
}