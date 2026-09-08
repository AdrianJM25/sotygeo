<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Flotilla extends Model 
{
    protected $table = 'flotillas';
    
    // Añadimos empresa_id para el soporte SaaS
    protected $fillable = ['empresa_id', 'user_id', 'nombre', 'descripcion'];

    public function empresa(): BelongsTo 
    { 
        return $this->belongsTo(Empresa::class); 
    }

    public function usuario(): BelongsTo 
    { 
        return $this->belongsTo(User::class, 'user_id'); 
    }

    public function activos(): HasMany 
    { 
        return $this->hasMany(Activo::class); 
    }
}