<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Vehiculo extends Model
{
    use HasFactory;

    protected $table = 'vehiculos';

    protected $fillable = [
        'empresa_id',
        'user_id',
        'flotilla_id',
        'nombre', 
        'tipo_vehiculo',
        'marca',
        'modelo',
        'anio',
        'placas',
        'color',
        'vin',
        'rendimiento_km_litro',
        'vencimiento_seguro',
        'icono',
        'color_icono',
        'horas_corte_ruta', // <-- NUEVO CAMPO
    ];

    public const ICONOS = [
        'sedan' => [
            'label' => 'Sedán',
            'svg' => '<rect x="3" y="13" width="18" height="4" rx="1.5"/><polygon points="8.5,9.5 15.5,9.5 17,13 7,13"/><circle cx="7.5" cy="17.5" r="1.6"/><circle cx="16.5" cy="17.5" r="1.6"/>',
        ],
        'suv' => [
            'label' => 'SUV',
            'svg' => '<rect x="3" y="12.5" width="18" height="4.5" rx="1.2"/><rect x="7" y="8.5" width="10" height="4.5" rx="1"/><circle cx="7.5" cy="17.8" r="1.8"/><circle cx="16.5" cy="17.8" r="1.8"/>',
        ],
        'pickup' => [
            'label' => 'Camioneta Pickup',
            'svg' => '<rect x="3" y="9" width="7" height="4.5" rx="1"/><rect x="10" y="12" width="8" height="1.5"/><rect x="3" y="13" width="18" height="3.5" rx="1"/><circle cx="7" cy="17.5" r="1.6"/><circle cx="17" cy="17.5" r="1.6"/>',
        ],
        'van' => [
            'label' => 'Van / Furgoneta',
            'svg' => '<rect x="3" y="8.5" width="18" height="8.5" rx="1.5"/><circle cx="7" cy="17.5" r="1.6"/><circle cx="17" cy="17.5" r="1.6"/>',
        ],
        'camion' => [
            'label' => 'Camión de Caja',
            'svg' => '<rect x="3" y="11" width="5" height="5.5" rx="1"/><rect x="9" y="7.5" width="12" height="9" rx="1"/><circle cx="6" cy="17.5" r="1.5"/><circle cx="13" cy="17.5" r="1.5"/><circle cx="18" cy="17.5" r="1.5"/>',
        ],
        'tractocamion' => [
            'label' => 'Tractocamión',
            'svg' => '<rect x="2" y="11" width="4" height="5.5" rx="1"/><rect x="8" y="8.5" width="14" height="8" rx="1"/><circle cx="5" cy="17.5" r="1.4"/><circle cx="11" cy="17.5" r="1.4"/><circle cx="16" cy="17.5" r="1.4"/><circle cx="20" cy="17.5" r="1.4"/>',
        ],
        'motocicleta' => [
            'label' => 'Motocicleta',
            'svg' => '<circle cx="6" cy="17.5" r="2.2"/><circle cx="18" cy="17.5" r="2.2"/><polygon points="6,17.5 11,10.5 14,10.5 12,15 18,17.5"/><rect x="9" y="9.5" width="4" height="1.5" rx="0.5"/>',
        ],
        'autobus' => [
            'label' => 'Autobús',
            'svg' => '<rect x="2" y="8" width="20" height="9.5" rx="1.5"/><circle cx="6" cy="17.5" r="1.6"/><circle cx="12" cy="17.5" r="1.6"/><circle cx="18" cy="17.5" r="1.6"/>',
        ],
    ];

    protected function casts(): array
    {
        return [
            'anio' => 'integer',
            'rendimiento_km_litro' => 'decimal:2',
            'vencimiento_seguro' => 'date',
            'horas_corte_ruta' => 'integer', // <-- NUEVO CAST
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

    public function alertas(): HasMany
    {
        return $this->hasMany(Alerta::class);
    }

    public function dispositivo(): HasOne
    {
        return $this->hasOne(Dispositivo::class);
    }

    // ==========================================
    // NUEVA RELACIÓN: RUTAS (Historial)
    // ==========================================
    public function rutas(): HasMany
    {
        return $this->hasMany(Ruta::class);
    }
}