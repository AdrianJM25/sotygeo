<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnlaceRastreo extends Model
{
    protected $table = 'enlaces_rastreo';

    protected $fillable = [
        'vehiculo_id',
        'token',
        'fecha_expiracion',
    ];

    protected $casts = [
        'fecha_expiracion' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIÓN CON VEHÍCULO
    |--------------------------------------------------------------------------
    */

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class);
    }

    /*
    |--------------------------------------------------------------------------
    | VERIFICAR SI EL ENLACE SIGUE VIGENTE
    |--------------------------------------------------------------------------
    */

    public function estaVigente(): bool
    {
        return $this->fecha_expiracion
            && $this->fecha_expiracion->isFuture();
    }

    /*
    |--------------------------------------------------------------------------
    | VERIFICAR SI YA EXPIRÓ
    |--------------------------------------------------------------------------
    */

    public function estaExpirado(): bool
    {
        return !$this->estaVigente();
    }
}