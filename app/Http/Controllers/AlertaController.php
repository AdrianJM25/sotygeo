<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alerta extends Model {
    protected $table = 'alertas';
    protected $fillable = ['activo_id', 'tipo', 'mensaje', 'leida'];
    protected $casts = ['leida' => 'boolean'];

    public function activo() { return $this->belongsTo(Activo::class); }
}