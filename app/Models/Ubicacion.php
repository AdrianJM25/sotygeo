<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ubicacion extends Model 
{
    use HasFactory;

    protected $table = 'ubicaciones';
    
    protected $fillable = [
        'dispositivo_id', 
        'vehiculo_id', 
        'ruta_id', // <-- NUEVO CAMPO (FK a la tabla rutas)
        'latitud', 
        'longitud', 
        'punto', 
        'velocidad', 
        'altitud', 
        'rumbo', 
        'porcentaje_bateria', 
        'esta_cargando', 
        'en_movimiento', 
        'estado_reposo', 
        'fecha_gps'
    ];

    protected function casts(): array 
    {
        return [
            'fecha_gps' => 'datetime',
            'esta_cargando' => 'boolean',
            'en_movimiento' => 'boolean',
            'estado_reposo' => 'boolean',
        ];
    }

    // ==========================================
    // RELACIONES
    // ==========================================

    public function dispositivo(): BelongsTo 
    { 
        return $this->belongsTo(Dispositivo::class); 
    }

    public function vehiculo(): BelongsTo 
    { 
        return $this->belongsTo(Vehiculo::class); 
    }

    // ==========================================
    // NUEVA RELACIÓN: RUTA
    // ==========================================
    public function ruta(): BelongsTo 
    {
        return $this->belongsTo(Ruta::class);
    }
}