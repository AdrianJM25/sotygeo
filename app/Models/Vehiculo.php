<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
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
        'horas_corte_ruta',
    ];

    /**
     * Se incluye automáticamente al convertir el modelo a JSON,
     * para que el Mapa en Vivo reciba la URL del ícono sin pasos extra.
     */
    protected $appends = ['icono_url'];

    protected function casts(): array
    {
        return [
            'anio' => 'integer',
            'rendimiento_km_litro' => 'decimal:2',
            'vencimiento_seguro' => 'date',
            'horas_corte_ruta' => 'integer',
        ];
    }

// ==========================================
    // ÍCONOS DEL MAPA (public/images/icons_vehiculos)
    // ==========================================

    public static function iconosDisponibles(): array
    {
        $rutaDirectorio = public_path('images/icons_vehiculos');

        if (!\Illuminate\Support\Facades\File::exists($rutaDirectorio)) {
            return [];
        }

        return collect(\Illuminate\Support\Facades\File::files($rutaDirectorio))
            ->map(function ($file) {
                $ruta = 'images/icons_vehiculos/' . $file->getFilename();
                $nombreBase = $file->getFilenameWithoutExtension();
                
                $label = preg_replace('/^icons8-/', '', $nombreBase);
                preg_match('/-(\d+)$/', $label, $m);
                $tamano = isset($m[1]) ? (int) $m[1] : 0;
                $label = preg_replace('/-\d+$/', '', $label);
                $label = mb_convert_case(str_replace('-', ' ', $label), MB_CASE_TITLE, 'UTF-8');

                return [
                    'path' => $ruta, 
                    'url' => asset($ruta), // <-- Generamos la URL real directamente
                    'label' => $label, 
                    'tamano' => $tamano
                ];
            })
            ->groupBy('label')
            ->map(fn ($grupo) => $grupo->sortByDesc('tamano')->first())
            ->values()
            ->all();
    }

    protected function iconoUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->icono) {
                    $fallback = static::iconosDisponibles()[0]['path'] ?? null;
                    return $fallback ? asset($fallback) : null;
                }

                // 1. Si es un archivo subido manualmente (está en storage)
                if (\Illuminate\Support\Str::startsWith($this->icono, 'icons_vehiculos/personalizados')) {
                    return \Illuminate\Support\Facades\Storage::disk('public')->url($this->icono);
                }

                // 2. Si es un ícono predefinido (está en public/images)
                return asset($this->icono);
            }
        );
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
    // RELACIONES LOGÍSTICAS Y TELEMETRÍA
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

    public function rutas(): HasMany
    {
        return $this->hasMany(Ruta::class);
    }

    // ==========================================
    // RELACIONES DE GEOCERCAS Y EVENTOS
    // ==========================================

    /**
     * Geocercas a las que este vehículo está asignado y sus reglas.
     */
    public function zonas(): BelongsToMany
    {
        return $this->belongsToMany(Zona::class, 'vehiculo_zona')
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
     * Historial completo de eventos de geocerca del vehículo.
     */
    public function eventosGeocerca(): HasMany
    {
        return $this->hasMany(EventoGeocerca::class, 'vehiculo_id');
    }

    /**
     * Eventos actualmente activos (el vehículo se encuentra dentro de la geocerca).
     */
    public function eventosGeocercaActivos(): HasMany
    {
        return $this->hasMany(EventoGeocerca::class, 'vehiculo_id')->whereNull('fecha_salida');
    }
}