<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $table = 'planes';

    protected $fillable = [
        'nombre',
        'precio',
        'limite_activos',
        'caracteristicas',
        'activo',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'limite_activos' => 'integer',
        'caracteristicas' => 'array',
        'activo' => 'boolean',
    ];
}