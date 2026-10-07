<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'duration_minutes',
        'category',
        'image_path',
        'is_active',
    ];

    /**
     * Trabajadores que realizan este servicio (Subtarea 7).
     */
    public function trabajadores()
    {
        return $this->belongsToMany(Trabajador::class, 'servicio_trabajador', 'service_id', 'trabajador_id')
            ->withTimestamps();
    }

    /**
     * Trabajadores activos que realizan este servicio.
     */
    public function trabajadoresActivos()
    {
        return $this->belongsToMany(Trabajador::class, 'servicio_trabajador', 'service_id', 'trabajador_id')
            ->where('trabajadores.activo', true)
            ->withTimestamps();
    }
}
