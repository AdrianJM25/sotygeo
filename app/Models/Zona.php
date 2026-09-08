<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zona extends Model
{
    protected $table = 'zonas';

    protected $fillable = [
        'user_id',
        'nombre',
        'color_hex',
        'poligono'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}