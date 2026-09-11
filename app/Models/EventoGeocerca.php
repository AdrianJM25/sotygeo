<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventoGeocerca extends Model
{
    use HasFactory;

    protected $table = 'eventos_geocerca';

    protected $fillable = [
        'vehiculo_id',
        'zona_id',
        'fecha_entrada',
        'fecha_salida',
        'duracion_minutos',
        'latitud_entrada',
        'longitud_entrada',
        'latitud_salida',
        'longitud_salida',
        'alerta_generada',
        'tipo_evento',
    ];

    protected function casts(): array
    {
        return [
            'fecha_entrada' => 'datetime',
            'fecha_salida' => 'datetime',
            'duracion_minutos' => 'integer',
            'latitud_entrada' => 'decimal:7',
            'longitud_entrada' => 'decimal:7',
            'latitud_salida' => 'decimal:7',
            'longitud_salida' => 'decimal:7',
            'alerta_generada' => 'boolean',
        ];
    }

    // ==========================================
    // RELACIONES
    // ==========================================

    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehiculo::class, 'vehiculo_id');
    }

    public function zona(): BelongsTo
    {
        return $this->belongsTo(Zona::class, 'zona_id');
    }
}