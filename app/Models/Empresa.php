<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    use HasFactory;

    protected $table = 'empresas';

    protected $fillable = [
        'nombre',
        'rfc',
        'telefono',
        'correo',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // Relación: Una empresa agrupa a múltiples usuarios (empleados y gerentes)
    public function usuarios()
    {
        return $this->hasMany(User::class);
    }

    // NUEVO: Relación directa con vehículos (reemplaza a los activos)
    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class);
    }

    // Relación: Una empresa tiene múltiples dispositivos físicos asignados
    public function dispositivos()
    {
        return $this->hasMany(Dispositivo::class);
    }

    // Relación: Una empresa puede organizar sus vehículos en múltiples flotillas
    public function flotillas()
    {
        return $this->hasMany(Flotilla::class);
    }

    // Relación: Una empresa tiene sus propias geocercas/zonas
    public function zonas()
    {
        return $this->hasMany(Zona::class);
    }
}