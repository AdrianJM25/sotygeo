<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inspeccion extends Model
{
    use HasFactory;

    protected $table = 'inspecciones';

    protected $fillable = [
        'vehiculo_id',
        'user_id',
        'conductor_id',
        'fecha_inspeccion',
        'resultado_general',
        'observaciones',
    ];

    protected $casts = [
        'fecha_inspeccion' => 'datetime',
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
    | USUARIO RESPONSABLE
    |--------------------------------------------------------------------------
    */

    public function usuario()
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CONDUCTOR
    |--------------------------------------------------------------------------
    */

    public function conductor()
    {
        return $this->belongsTo(
            Conductor::class
        );
    }


    /*
    |--------------------------------------------------------------------------
    | DETALLES
    |--------------------------------------------------------------------------
    */

    public function detalles()
    {
        return $this->hasMany(
            DetalleInspeccion::class
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ¿TIENE INCIDENCIAS?
    |--------------------------------------------------------------------------
    */

    public function tieneIncidencias(): bool
    {
        return $this->resultado_general
            ===
            'con_incidencias';
    }
}