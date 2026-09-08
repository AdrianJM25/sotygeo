<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Direccion extends Model
{
    protected $table = 'direcciones';

    protected $fillable = [
        'direccionable_id', 'direccionable_type', 'tipo',
        'calle', 'numero_exterior', 'numero_interior',
        'colonia', 'ciudad', 'estado', 'codigo_postal', 'pais',
        'referencias', 'punto', 'es_principal', 'activo',
    ];

    protected $casts = [
        'es_principal' => 'boolean',
        'activo' => 'boolean',
    ];

    public function direccionable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getDireccionCompletaAttribute(): string
    {
        return trim(sprintf(
            '%s %s, %s, %s, %s, C.P. %s, %s',
            $this->calle,
            $this->numero_exterior ?? 'S/N',
            $this->colonia,
            $this->ciudad,
            $this->estado,
            $this->codigo_postal,
            $this->pais
        ));
    }

    public function scopePrincipal($query)
    {
        return $query->where('es_principal', true);
    }
}