<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flotilla extends Model
{
    use HasFactory;

    protected $table = 'flotillas';

    protected $fillable = [
        'user_id',
        'nombre',
        'descripcion'
    ];

    // Relación: Una flotilla pertenece a un usuario (gestor/responsable)
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relación: Una flotilla tiene muchos activos
    public function activos()
    {
        return $this->hasMany(Activo::class);
    }
}