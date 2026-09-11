<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Zona extends Model
{
    use HasFactory;

    protected $table = 'zonas';

    protected $fillable = [
        'empresa_id',
        'user_id',
        'nombre',
        'color_hex',
        'poligono'
    ];

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
    // RELACIONES DE GEOCERCAS Y EVENTOS
    // ==========================================

    /**
     * Vehículos asignados a esta geocerca con sus reglas específicas.
     */
    public function vehiculos(): BelongsToMany
    {
        return $this->belongsToMany(Vehiculo::class, 'vehiculo_zona')
                    ->withPivot([
                        'tipo_regla',
                        'notificar_entrada',
                        'notificar_salida',
                        'permanencia_minima_minutos',
                        'permanencia_maxima_minutos',
                    ])
                    ->withTimestamps();
    }

    /**
     * Historial de entradas/salidas registradas en esta zona.
     */
    public function eventos(): HasMany
    {
        return $this->hasMany(EventoGeocerca::class, 'zona_id');
    }
}