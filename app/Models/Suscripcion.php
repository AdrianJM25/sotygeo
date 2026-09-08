<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Suscripcion extends Model {
    protected $table = 'suscripciones';
    protected $fillable = ['user_id', 'plan_id', 'estatus', 'fecha_inicio', 'fecha_fin'];
    protected $casts = ['fecha_inicio' => 'datetime', 'fecha_fin' => 'datetime'];

    public function usuario() { return $this->belongsTo(User::class, 'user_id'); }
    public function plan() { return $this->belongsTo(Plan::class); }
}