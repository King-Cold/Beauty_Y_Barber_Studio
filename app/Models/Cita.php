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
        'notas',
        'observaciones',
    ];

    protected $casts = [
        'fecha' => 'date:Y-m-d',
        'duracion_minutos' => 'integer',
        'precio' => 'decimal:2',
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
                    $table->text('notas')->nullable();
                    $table->text('observaciones')->nullable();
                    $table->timestamps();
                });
            } else if (!\Illuminate\Support\Facades\Schema::hasColumn('citas', 'observaciones')) {
                \Illuminate\Support\Facades\Schema::table('citas', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->text('observaciones')->nullable();
                });
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
        $fechaStr = $this->fecha instanceof \Carbon\Carbon ? $this->fecha->format('Y-m-d') : (string) $this->fecha;
        return \Carbon\Carbon::parse($fechaStr . ' ' . $this->hora_inicio, $tz);
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
     * Determina si la cita cumple con las condiciones para ser cancelada:
     * - No estar cancelada ni completada previamente.
     * - Faltar más de 30 minutos para la hora de inicio.
     */
    public function puedeCancelar(): bool
    {
        $estado = strtolower((string) $this->estado);
        if (in_array($estado, ['cancelada', 'cancelled', 'anulada', 'completada', 'completed'])) {
            return false;
        }

        return $this->minutos_para_inicio > 30;
    }

    /**
     * Retorna el motivo explicativo por el cual la cita no puede ser cancelada, o null si sí se puede.
     */
    public function getMotivoNoCancelableAttribute(): ?string
    {
        $estado = strtolower((string) $this->estado);
        if (in_array($estado, ['cancelada', 'cancelled', 'anulada'])) {
            return 'La cita ya se encuentra cancelada.';
        }
        if (in_array($estado, ['completada', 'completed'])) {
            return 'La cita ya ha sido completada.';
        }

        $minutos = $this->minutos_para_inicio;
        if ($minutos <= 0) {
            return 'La fecha u hora de la cita ya ha transcurrido.';
        }
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
}
