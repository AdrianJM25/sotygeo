<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnlaceRastreo extends Model {
    protected $table = 'enlaces_rastreo';
    protected $fillable = ['activo_id', 'token', 'fecha_expiracion'];
    protected $casts = ['fecha_expiracion' => 'datetime'];

    public function activo() { return $this->belongsTo(Activo::class); }
}