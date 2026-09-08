<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dispositivo extends Model
{
    use HasFactory;

    protected $table = 'dispositivos';

    protected $fillable = [
        'empresa_id',
        'user_id',
        'vehiculo_id',
        'imei',
        'numero_sim',
        'modelo',
        'capacidad_bateria_mah',
        'modo_reposo'
    ];

    protected function casts(): array
    {
        return [
            'modo_reposo' => 'boolean',
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
    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehiculo::class);
    }

    public function ubicaciones(): HasMany
    {
        return $this->hasMany(Ubicacion::class);
    }
}