<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Alerta extends Model
{
    use HasFactory;

    protected $table = 'alertas';

    protected $fillable = [
        'vehiculo_id',
        'dispositivo_id',
        'tipo',
        'mensaje',
        'leida',
    ];

    protected $casts = [
        'leida' => 'boolean',
    ];


    /*
    |--------------------------------------------------------------------------
    | VEHÍCULO
    |--------------------------------------------------------------------------
    */

    public function vehiculo()
    {
        return $this->belongsTo(
            Vehiculo::class
        );
    }


    /*
    |--------------------------------------------------------------------------
    | DISPOSITIVO GPS
    |--------------------------------------------------------------------------
    */

    public function dispositivo()
    {
        return $this->belongsTo(
            Dispositivo::class
        );
    }
}