<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ruta extends Model
{
    use HasFactory;

    protected $table = 'rutas';

    protected $fillable = [
        'vehiculo_id',
        'fecha_inicio',
        'fecha_fin',
        'distancia_total_km',
        'estado',            // 'abierta' o 'cerrada'
        'trazado_polilinea', // Coordenadas en formato Polyline
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'datetime',
            'fecha_fin' => 'datetime',
            'distancia_total_km' => 'decimal:2',
        ];
    }

    // ==========================================
    // RELACIONES
    // ==========================================

    /**
     * El vehículo al que pertenece este bloque de ruta.
     */
    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehiculo::class);
    }

    /**
     * Todos los puntos (pings) individuales que conforman esta ruta.
     * Útil si en el historial el usuario quiere ver los detalles punto por punto.
     */
    public function ubicaciones(): HasMany
    {
        return $this->hasMany(Ubicacion::class);
    }
}