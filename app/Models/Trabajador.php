<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trabajador extends Model
{
    use HasFactory;

    protected $table = 'trabajadores';

    protected $fillable = [
        'nombre',
        'apellidos',
        'telefono',
        'email',
        'direccion',
        'fotografia',
        'experiencia',
        'activo',
    ];

    protected $casts = [
        'experiencia' => 'integer',
        'activo' => 'boolean',
    ];

    /**
     * Scope para filtrar trabajadores activos (Subtarea 8).
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Nombre completo formateado del trabajador.
     */
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombre} {$this->apellidos}";
    }

    /**
     * URL o ruta para desplegar la fotografía del trabajador.
     */
    public function getFotoUrlAttribute(): ?string
    {
        if ($this->fotografia) {
            return asset('storage/' . $this->fotografia);
        }
        return null;
    }

    /**
     * Servicios asociados al trabajador (Subtarea 7).
     */
    public function servicios()
    {
        return $this->belongsToMany(Service::class, 'servicio_trabajador', 'trabajador_id', 'service_id')
            ->withTimestamps();
    }

    /**
     * Servicios activos asociados al trabajador (visibles en vistas y operaciones).
     */
    public function serviciosActivos()
    {
        return $this->belongsToMany(Service::class, 'servicio_trabajador', 'trabajador_id', 'service_id')
            ->where('services.is_active', true)
            ->withTimestamps();
    }
}
