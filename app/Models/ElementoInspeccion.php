<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ElementoInspeccion extends Model
{
    use HasFactory;

    protected $table =
        'elementos_inspeccion';

    protected $fillable = [
        'nombre',
        'categoria',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];


    /*
    |--------------------------------------------------------------------------
    | DETALLES DE INSPECCIÓN
    |--------------------------------------------------------------------------
    */

    public function detalles()
    {
        return $this->hasMany(
            DetalleInspeccion::class,
            'elemento_id'
        );
    }
}