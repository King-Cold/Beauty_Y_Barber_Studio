<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    use HasFactory;

    protected $table = 'citas';

    protected $fillable = [
        'user_id',
        'trabajador_id',
        'service_id',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'duracion_minutos',
        'precio',
        'estado',
        'estado_pago',
        'notas',
        'observaciones',
        'check_in_at',
    ];

    protected $casts = [
        'fecha' => 'date:Y-m-d',
        'duracion_minutos' => 'integer',
        'precio' => 'decimal:2',
        'check_in_at' => 'datetime',
    ];

    protected $appends = [
        'estado_visual',
        'estado_label',
        'estado_pago_label',
        'precio_formateado',
        'folio',
        'checked_in',
        'check_in_hora',
        'check_in_formateado',
        'es_pasada',
        'puede_cancelar',
        'puede_cancelar_administrativamente',
    ];

    /**
     * Garantiza la existencia de la tabla citas en SQLite en memoria o testing.
     */
    public static function ensureTableExists(): void
    {
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('citas')) {
                \Illuminate\Support\Facades\Schema::create('citas', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('user_id');
                    $table->unsignedBigInteger('trabajador_id');
                    $table->unsignedBigInteger('service_id');
                    $table->date('fecha');
                    $table->string('hora_inicio', 10);
                    $table->string('hora_fin', 10);
                    $table->integer('duracion_minutos')->default(30);
                    $table->decimal('precio', 10, 2)->default(0.00);
                    $table->string('estado', 50)->default('confirmada');
                    $table->string('estado_pago', 20)->default('pendiente');
                    $table->text('notas')->nullable();
                    $table->text('observaciones')->nullable();
                    $table->timestamps();
                });
            } else {
                if (!\Illuminate\Support\Facades\Schema::hasColumn('citas', 'estado_pago')) {
                    \Illuminate\Support\Facades\Schema::table('citas', function (\Illuminate\Database\Schema\Blueprint $table) {
                        $table->string('estado_pago', 20)->default('pendiente');
                    });
                }
                if (!\Illuminate\Support\Facades\Schema::hasColumn('citas', 'observaciones')) {
                    \Illuminate\Support\Facades\Schema::table('citas', function (\Illuminate\Database\Schema\Blueprint $table) {
                        $table->text('observaciones')->nullable();
                    });
                }
                if (!\Illuminate\Support\Facades\Schema::hasColumn('citas', 'check_in_at')) {
                    \Illuminate\Support\Facades\Schema::table('citas', function (\Illuminate\Database\Schema\Blueprint $table) {
                        $table->timestamp('check_in_at')->nullable();
                    });
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Error en ensureTableExists de Cita: ' . $e->getMessage());
        }
    }

    /**
     * Usuario (Cliente) asociado a la cita.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Alias de cliente para facilitar legibilidad.
     */
    public function cliente()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Especialista o trabajador asignado a la cita.
     */
    public function trabajador()
    {
        return $this->belongsTo(Trabajador::class, 'trabajador_id');
    }

    /**
     * Servicio asignado a la cita.
     */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /**
     * Alias en español del servicio asignado a la cita.
     */
    public function servicio()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /**
     * Accesor para obtener la hora de inicio con compatibilidad.
     */
    public function getHoraAttribute(): ?string
    {
        return $this->hora_inicio;
    }

    /**
     * Folio formateado de la cita (ej. CITA-00001).
     */
    public function getFolioAttribute(): string
    {
        return 'CITA-' . str_pad((string)$this->id, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Precio formateado en formato de moneda (ej. $250.00).
     */
    public function getPrecioFormateadoAttribute(): string
    {
        return '$' . number_format((float)$this->precio, 2);
    }

    /**
     * Determina el estado visual calculado (confirmada, en_proceso, completada, cancelada, vencida).
     */
    public function getEstadoVisualAttribute(): string
    {
        $raw = strtolower(trim((string)$this->estado));
        if ($raw === 'en_atencion') {
            return 'en_proceso';
        }
        if ($raw === 'confirmada') {
            $tz = config('app.timezone') ?: 'America/Mexico_City';
            $now = \Carbon\Carbon::now($tz);
            $today = $now->format('Y-m-d');
            $currentTime = $now->format('H:i');
            $fechaStr = $this->fecha instanceof \Carbon\Carbon ? $this->fecha->format('Y-m-d') : (string)$this->fecha;

            $esPasada = ($fechaStr < $today) || ($fechaStr === $today && $this->hora_fin < $currentTime);
            if ($esPasada) {
                return 'vencida';
            }
        }
        return $raw ?: 'confirmada';
    }

    /**
     * Etiqueta legible del estado para interfaces y reportes.
     */
    public function getEstadoLabelAttribute(): string
    {
        $labels = [
            'confirmada' => 'Confirmada',
            'en_proceso' => 'En proceso',
            'en_atencion' => 'En proceso',
            'completada' => 'Completada',
            'cancelada' => 'Cancelada',
            'vencida' => 'Vencida',
        ];
        return $labels[$this->estado_visual] ?? ucfirst(str_replace('_', ' ', $this->estado_visual));
    }

    /**
     * Rango formateado de horario de la cita (ej. 09:00 - 09:30).
     */
    public function getRangoHorarioAttribute(): string
    {
        return "{$this->hora_inicio} - {$this->hora_fin}";
    }

    /**
     * Instancia Carbon con la fecha y hora de inicio en la zona horaria del sistema.
     */
    public function getFechaHoraInicioAttribute(): \Carbon\Carbon
    {
        $tz = config('app.timezone') ?: 'America/Mexico_City';
        $fechaStr = $this->fecha instanceof \Carbon\Carbon ? $this->fecha->format('Y-m-d') : substr(trim((string) $this->fecha), 0, 10);
        $hora = trim((string) $this->hora_inicio);
        if (strlen($hora) === 5) {
            $hora .= ':00';
        }
        return \Carbon\Carbon::parse($fechaStr . ' ' . $hora, $tz);
    }

    /**
     * Minutos restantes desde el momento actual hasta la hora de inicio de la cita.
     * Retorna positivo si es en el futuro, o negativo si ya pasó.
     */
    public function getMinutosParaInicioAttribute(): int
    {
        $tz = config('app.timezone') ?: 'America/Mexico_City';
        $ahora = \Carbon\Carbon::now($tz);
        $inicio = $this->fecha_hora_inicio;
        return (int) $ahora->diffInMinutes($inicio, false);
    }

    /**
     * Determina si la cita ya ha transcurrido (su fecha y hora son anteriores al momento actual).
     * La comparación toma en cuenta tanto la fecha como la hora en la zona horaria del sistema.
     */
    public function esPasada(): bool
    {
        $tz = config('app.timezone') ?: 'America/Mexico_City';
        $ahora = \Carbon\Carbon::now($tz);
        return $this->fecha_hora_inicio->lt($ahora);
    }

    /**
     * Accesor para es_pasada.
     */
    public function getEsPasadaAttribute(): bool
    {
        return $this->esPasada();
    }

    /**
     * Determina si la cita cumple con las condiciones para ser cancelada administrativamente (Recepción / Personal):
     * - No estar en estado terminal (cancelada o completada).
     * - Su fecha y hora no deben haber transcurrido (no ser pasada).
     */
    public function puedeCancelarAdministrativamente(): bool
    {
        $estado = strtolower(trim((string) $this->estado));
        if (in_array($estado, ['cancelada', 'cancelled', 'anulada', 'completada', 'completed'], true)) {
            return false;
        }

        return !$this->esPasada();
    }

    /**
     * Accesor para puede_cancelar_administrativamente.
     */
    public function getPuedeCancelarAdministrativamenteAttribute(): bool
    {
        return $this->puedeCancelarAdministrativamente();
    }

    /**
     * Determina si la cita cumple con las condiciones para ser cancelada por el cliente:
     * - Cumplir con las condiciones administrativas generales (no terminal, no pasada).
     * - Faltar más de 30 minutos para la hora de inicio.
     */
    public function puedeCancelar(): bool
    {
        if (!$this->puedeCancelarAdministrativamente()) {
            return false;
        }

        return $this->minutos_para_inicio > 30;
    }

    /**
     * Accesor para puede_cancelar.
     */
    public function getPuedeCancelarAttribute(): bool
    {
        return $this->puedeCancelar();
    }

    /**
     * Retorna el motivo explicativo por el cual la cita no puede ser cancelada, o null si sí se puede.
     */
    public function getMotivoNoCancelableAttribute(): ?string
    {
        $estado = strtolower((string) $this->estado);
        if (in_array($estado, ['cancelada', 'cancelled', 'anulada'], true)) {
            return 'La cita ya se encuentra cancelada.';
        }
        if (in_array($estado, ['completada', 'completed'], true)) {
            return 'La cita ya ha sido completada.';
        }

        if ($this->esPasada() || $this->minutos_para_inicio <= 0) {
            return 'No se puede cancelar una cita cuya fecha u hora ya ha transcurrido.';
        }

        $minutos = $this->minutos_para_inicio;
        if ($minutos <= 30) {
            return "Solo se permite cancelar con más de 30 minutos de anticipación. Faltan {$minutos} minutos o menos para el inicio.";
        }

        return null;
    }

    /**
     * Determina si la cita es considerada próxima (futura y activa).
     */
    public function esProxima(): bool
    {
        $estado = strtolower((string) $this->estado);
        return !in_array($estado, ['cancelada', 'cancelled', 'anulada', 'completada', 'completed'])
            && $this->minutos_para_inicio > 0;
    }

    /**
     * Determina si la llegada del cliente ya fue registrada.
     */
    public function isCheckedIn(): bool
    {
        return !is_null($this->check_in_at);
    }

    /**
     * Accesor booleano para serialización JSON.
     */
    public function getCheckedInAttribute(): bool
    {
        return $this->isCheckedIn();
    }

    /**
     * Hora formateada del check-in (ej. 10:25).
     */
    public function getCheckInHoraAttribute(): ?string
    {
        return $this->check_in_at ? $this->check_in_at->format('H:i') : null;
    }

    /**
     * Fecha y hora formateada legible del check-in.
     */
    public function getCheckInFormateadoAttribute(): ?string
    {
        if (!$this->check_in_at) {
            return null;
        }
        $tz = config('app.timezone') ?: 'America/Mexico_City';
        return \Carbon\Carbon::parse($this->check_in_at, $tz)->locale('es')->isoFormat('D [de] MMMM [a las] HH:mm [hrs]');
    }

    /**
     * Determina si la cita cumple con las condiciones para registrar check-in:
     * - No tener check-in registrado previamente.
     * - No estar cancelada ni completada.
     * - No estar vencida.
     * - Estar en estado confirmada o en atención.
     */
    public function puedeRegistrarCheckIn(): bool
    {
        if ($this->isCheckedIn()) {
            return false;
        }

        $estado = strtolower(trim((string) $this->estado));
        if (in_array($estado, ['cancelada', 'completada'], true)) {
            return false;
        }

        if ($this->estado_visual === 'vencida') {
            return false;
        }

        return in_array($estado, ['confirmada', 'en_atencion', 'en_proceso'], true);
    }

    /**
     * Retorna el motivo explicativo por el cual no se puede registrar check-in, o null si sí se puede.
     */
    public function getMotivoNoCheckInAttribute(): ?string
    {
        if ($this->isCheckedIn()) {
            return 'La llegada del cliente ya fue registrada previamente a las ' . ($this->check_in_hora ?? '') . ' hrs.';
        }

        $estado = strtolower(trim((string) $this->estado));
        if ($estado === 'cancelada') {
            return 'No se puede registrar la llegada de una cita que se encuentra cancelada.';
        }

        if ($estado === 'completada') {
            return 'No se puede registrar la llegada de una cita que ya fue completada.';
        }

        if ($this->estado_visual === 'vencida') {
            return 'La cita se encuentra vencida. Debe ser reagendada a un horario válido antes de registrar la llegada.';
        }

        return null;
    }

    /**
     * Determina si la cita cumple con las condiciones para iniciar la atención (Subtarea 6):
     * - Estar en estado confirmada.
     * - No estar cancelada ni completada.
     * - No estar vencida por fecha y hora transcurrida.
     */
    public function puedeIniciarAtencion(): bool
    {
        $estado = strtolower(trim((string) $this->estado));
        if ($estado !== 'confirmada') {
            return false;
        }

        if ($this->estado_visual === 'vencida') {
            return false;
        }

        return true;
    }

    /**
     * Accesor booleano para serialización JSON.
     */
    public function getPuedeIniciarAtencionAttribute(): bool
    {
        return $this->puedeIniciarAtencion();
    }

    /**
     * Determina si la cita cumple con las condiciones para finalizar la atención (Subtarea 6):
     * - Estar actualmente en estado en_atencion / en_proceso.
     * - No estar cancelada, completada previamente ni confirmada sin haber iniciado.
     */
    public function puedeFinalizarAtencion(): bool
    {
        $estado = strtolower(trim((string) $this->estado));
        return in_array($estado, ['en_atencion', 'en_proceso'], true);
    }

    /**
     * Accesor booleano para serialización JSON.
     */
    public function getPuedeFinalizarAtencionAttribute(): bool
    {
        return $this->puedeFinalizarAtencion();
    }

    /**
     * Normaliza el estado de pago por defecto a 'pendiente'.
     */
    public function getEstadoPagoAttribute($value): string
    {
        return strtolower(trim((string) $value)) === 'pagado' ? 'pagado' : 'pendiente';
    }

    /**
     * Etiqueta formateada del estado de pago.
     */
    public function getEstadoPagoLabelAttribute(): string
    {
        return $this->estado_pago === 'pagado' ? 'Pagado' : 'Pendiente';
    }

    /**
     * Determina si la cita ya se encuentra liquidada.
     */
    public function getEstaPagadaAttribute(): bool
    {
        return $this->estado_pago === 'pagado';
    }

    /**
     * Determina si la cita cumple con las condiciones para ser marcada como pagada:
     * - No estar en estado cancelada.
     * - No estar pagada previamente.
     */
    public function puedeMarcarComoPagado(): bool
    {
        $estado = strtolower(trim((string) $this->estado));
        if (in_array($estado, ['cancelada', 'cancelled', 'anulada', 'vencida'], true)) {
            return false;
        }

        if ($this->estado_visual === 'vencida') {
            return false;
        }

        return $this->estado_pago !== 'pagado';
    }

    /**
     * Accesor para puede_marcar_como_pagado.
     */
    public function getPuedeMarcarComoPagadoAttribute(): bool
    {
        return $this->puedeMarcarComoPagado();
    }
}
