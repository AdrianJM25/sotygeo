<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetalleInspeccion extends Model
{
    use HasFactory;

    protected $table =
        'detalles_inspeccion';

    protected $fillable = [
        'inspeccion_id',
        'elemento_id',
        'resultado',
        'observaciones',
    ];


    /*
    |--------------------------------------------------------------------------
    | INSPECCIÓN
    |--------------------------------------------------------------------------
    */

    public function inspeccion()
    {
        return $this->belongsTo(
            Inspeccion::class
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ELEMENTO REVISADO
    |--------------------------------------------------------------------------
    */

    public function elemento()
    {
        return $this->belongsTo(
            ElementoInspeccion::class,
            'elemento_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ¿ES INCIDENCIA?
    |--------------------------------------------------------------------------
    */

    public function esIncidencia(): bool
    {
        return $this->resultado
            ===
            'incidencia';
    }
}