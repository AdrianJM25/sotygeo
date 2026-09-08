<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Vehiculo extends Model
{
    use HasFactory;

    protected $table = 'vehiculos';

    protected $fillable = [
        'empresa_id',
        'user_id',
        'flotilla_id',
        'nombre', // Alias del vehículo (Ej: "Unidad 01")
        'tipo_vehiculo',
        'marca',
        'modelo',
        'anio',
        'placas',
        'color',
        'vin',
        'rendimiento_km_litro',
        'vencimiento_seguro'
    ];

    protected function casts(): array
    {
        return [
            'anio' => 'integer',
            'rendimiento_km_litro' => 'decimal:2',
            'vencimiento_seguro' => 'date',
        ];
    }

    // ==========================================
    // RELACIONES MULTI-TENANT (SaaS)
    // ==========================================

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ==========================================
    // RELACIONES LOGÍSTICAS
    // ==========================================

    public function flotilla(): BelongsTo
    {
        return $this->belongsTo(Flotilla::class);
    }


    public function alertas()
{
    return $this->hasMany(Alerta::class);
}

// Relación directa con el GPS físico
    public function dispositivo(): HasOne
    {
        return $this->hasOne(Dispositivo::class);
    }
}