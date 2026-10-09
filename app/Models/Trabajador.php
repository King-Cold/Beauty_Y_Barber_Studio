<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trabajador extends Model
{
    use HasFactory;

    protected $table = 'trabajadores';

    protected $fillable = [
        'direccion',
        'fotografia',
        'experiencia',
        'activo',
        'horario',
        'user_id',
    ];

    protected $casts = [
        'experiencia' => 'integer',
        'activo' => 'boolean',
        'horario' => 'array',
    ];

    /**
     * Scope para filtrar trabajadores activos (Subtarea 8).
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Get the user that owns the worker.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Nombre completo formateado del trabajador.
     */
    public function getNombreCompletoAttribute(): string
    {
        return trim(($this->user->name ?? '') . ' ' . ($this->user->apellidos ?? ''));
    }

    // Accessors para mantener la compatibilidad con el resto del sistema
    public function getNombreAttribute()
    {
        return $this->user->name ?? '';
    }

    public function getApellidosAttribute()
    {
        return $this->user->apellidos ?? '';
    }

    public function getTelefonoAttribute()
    {
        return $this->user->telefono ?? '';
    }

    public function getEmailAttribute()
    {
        return $this->user->email ?? '';
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

    /**
     * Retorna los horarios disponibles del trabajador para una fecha dada,
     * aplicando con prioridad las fechas especiales y las intersecciones correspondientes.
     *
     * @param string|\DateTimeInterface $fecha
     * @param int $duracionMinutos
     * @return array Lista de horas disponibles (ej. ['10:00', '10:30', ...])
     */
    public function getHorariosDisponibles(string|\DateTimeInterface $fecha, int $duracionMinutos = 30): array
    {
        return app(\App\Services\DisponibilidadService::class)->calcularHorariosDisponibles($this, $fecha, $duracionMinutos);
    }

    /**
     * Retorna el desglose completo de disponibilidad del trabajador para una fecha dada.
     *
     * @param string|\DateTimeInterface $fecha
     * @param int $duracionMinutos
     * @return array
     */
    public function getDisponibilidadFecha(string|\DateTimeInterface $fecha, int $duracionMinutos = 30): array
    {
        return app(\App\Services\DisponibilidadService::class)->obtenerDisponibilidadCompleta($this, $fecha, $duracionMinutos);
    }

    /**
     * Determina el estado de disponibilidad del trabajador para el día de hoy
     * basándose en la regla de negocio estricta:
     * 1. Si el trabajador está inactivo, retorna "Sin disponibilidad".
     * 2. Si la sucursal está cerrada el día de hoy (ya sea por su horario general o por una fecha especial registrada),
     *    retorna "Sin disponibilidad" ignorando cualquier otro dato.
     * 3. Si el trabajador no tiene horario registrado para laborar el día de hoy, retorna "Sin disponibilidad".
     * 4. Si pasa todas las validaciones anteriores (el trabajador está activo, la sucursal está abierta hoy
     *    y el trabajador tiene turno asignado para hoy), retorna "Con disponibilidad de horario".
     *
     * @return string "Con disponibilidad de horario" | "Sin disponibilidad"
     */
    public function getEstadoDisponibilidadHoy(): string
    {
        // 1. Si el trabajador está inactivo, retorna "Sin disponibilidad"
        if (!$this->activo) {
            return 'Sin disponibilidad';
        }

        // =========================================================================
        // 1. MAPEO EXACTO DEL DÍA ACTUAL
        // Se utiliza la zona horaria del sistema/negocio para no desfasar el día actual
        // =========================================================================
        $tz = config('app.timezone') ?: 'America/Mexico_City';
        if ($tz === 'UTC') {
            $tz = env('APP_TIMEZONE', 'America/Mexico_City');
        }
        $hoy = \Illuminate\Support\Carbon::now($tz);
        $fechaHoy = $hoy->format('Y-m-d');

        // Mapeo exhaustivo según el día de la semana (0=Domingo, 1=Lunes, ..., 6=Sábado)
        $mapaDias = [
            0 => ['domingo', 'Domingo', 'sunday', 'Sunday'],
            1 => ['lunes', 'Lunes', 'monday', 'Monday'],
            2 => ['martes', 'Martes', 'tuesday', 'Tuesday'],
            3 => ['miercoles', 'miércoles', 'Miércoles', 'Miercoles', 'wednesday', 'Wednesday'],
            4 => ['jueves', 'Jueves', 'thursday', 'Thursday'],
            5 => ['viernes', 'Viernes', 'friday', 'Friday'],
            6 => ['sabado', 'sábado', 'Sábado', 'Sabado', 'saturday', 'Saturday'],
        ];

        $variantesDia = $mapaDias[$hoy->dayOfWeek] ?? ['miercoles', 'miércoles', 'Miércoles'];
        $diaEstandar = $variantesDia[0];

        // =========================================================================
        // 2. VALIDACIÓN DE FECHA ESPECIAL (Si hoy es fecha especial cerrada)
        // =========================================================================
        \App\Models\FechaEspecial::ensureTableExists();
        $fechaEspecial = \App\Models\FechaEspecial::whereDate('fecha', $fechaHoy)->first();

        if ($fechaEspecial) {
            if ($fechaEspecial->cerrado_todo_el_dia || empty($fechaEspecial->hora_apertura) || empty($fechaEspecial->hora_cierre) || $fechaEspecial->hora_apertura >= $fechaEspecial->hora_cierre) {
                return 'Sin disponibilidad';
            }
        }

        // =========================================================================
        // 2 & 3. CONSULTA ESTRICTA Y BLOQUEO INMEDIATO DE LA SUCURSAL
        // Consulta directa al modelo HorarioSucursal filtrando por el día mapeado
        // =========================================================================
        \App\Models\HorarioSucursal::ensureTableExists();

        $registroSucursal = \App\Models\HorarioSucursal::where(function ($query) use ($variantesDia) {
            $query->whereIn('dia', $variantesDia);
            foreach ($variantesDia as $v) {
                $query->orWhere('dia', 'LIKE', '%' . $v . '%');
            }
        })->orderBy('updated_at', 'desc')->first();

        if ($registroSucursal) {
            // Verificar si el registro de la sucursal indica que hoy está cerrado
            $sucursalCerrada = false;

            // Verificación del campo 'abierto'
            if (isset($registroSucursal->abierto) && (!$registroSucursal->abierto || $registroSucursal->abierto === '0' || $registroSucursal->abierto === false)) {
                $sucursalCerrada = true;
            }

            // Verificación de campos adicionales de cierre si existiesen
            if (!empty($registroSucursal->is_closed) || !empty($registroSucursal->cerrado)) {
                $sucursalCerrada = true;
            }

            if (isset($registroSucursal->estado) && in_array(strtolower(trim((string)$registroSucursal->estado)), ['cerrado', 'closed', 'inactivo', 'inactive'])) {
                $sucursalCerrada = true;
            }

            // Verificación de horas vacías o inválidas
            if (empty($registroSucursal->apertura) || empty($registroSucursal->cierre) || $registroSucursal->apertura >= $registroSucursal->cierre) {
                $sucursalCerrada = true;
            }

            // BLOQUEO INMEDIATO: Si la sucursal está cerrada hoy, retornar inmediatamente "Sin disponibilidad"
            if ($sucursalCerrada) {
                return 'Sin disponibilidad';
            }
        } else {
            // Si no existe registro específico en la tabla, verificar mapa de horarios general
            $mapaSucursal = \App\Models\HorarioSucursal::getHorariosMap();
            $configDia = $mapaSucursal[$diaEstandar] ?? ($mapaSucursal['miercoles'] ?? null);

            if (!$configDia || empty($configDia['abierto'])) {
                return 'Sin disponibilidad';
            }
        }

        // =========================================================================
        // 4. VALIDAR HORARIO DEL TRABAJADOR
        // Solo si la sucursal está abierta, verificar si el trabajador labora hoy
        // =========================================================================
        $horarioData = $this->horario;
        if (is_string($horarioData)) {
            $horarioData = json_decode($horarioData, true);
        }

        if (!is_array($horarioData)) {
            return 'Sin disponibilidad';
        }

        // Buscar el turno de hoy en el horario del trabajador con cualquiera de las variantes del día
        $turnoHoy = null;
        foreach ($variantesDia as $v) {
            if (isset($horarioData[$v])) {
                $turnoHoy = $horarioData[$v];
                break;
            }
        }

        if (!$turnoHoy || empty($turnoHoy['entrada']) || empty($turnoHoy['salida']) || $turnoHoy['entrada'] >= $turnoHoy['salida']) {
            return 'Sin disponibilidad';
        }

        // Superó todas las validaciones: trabajador activo, sucursal abierta hoy y turno asignado hoy
        return 'Con disponibilidad de horario';
    }

    /**
     * Retorna el estado textual de disponibilidad de hoy.
     *
     * @return string
     */
    public function getDisponibilidadHoy(): string
    {
        return $this->getEstadoDisponibilidadHoy();
    }

    /**
     * Accesor para obtener el estado de disponibilidad de hoy:
     * $trabajador->estado_disponibilidad_hoy o $trabajador->disponibilidad_hoy
     *
     * @return string
     */
    public function getEstadoDisponibilidadHoyAttribute(): string
    {
        return $this->getEstadoDisponibilidadHoy();
    }

    public function getDisponibilidadHoyAttribute(): string
    {
        return $this->getEstadoDisponibilidadHoy();
    }

    /**
     * Método estructurado para compatibilidad con controladores y respuestas AJAX.
     *
     * @return array
     */
    public function estaLaborandoHoy(): array
    {
        $estado = $this->getEstadoDisponibilidadHoy();
        $disponible = ($estado === 'Con disponibilidad de horario');

        return [
            'disponible' => $disponible,
            'laborando' => $disponible,
            'estado' => $estado,
            'motivo' => $estado,
            'texto' => $estado,
        ];
    }
}
