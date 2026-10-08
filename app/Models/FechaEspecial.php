<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class FechaEspecial extends Model
{
    use HasFactory;

    protected $table = 'fechas_especiales';

    protected $fillable = [
        'fecha',
        'motivo',
        'cerrado_todo_el_dia',
        'hora_apertura',
        'hora_cierre',
    ];

    protected $casts = [
        'fecha' => 'date:Y-m-d',
        'cerrado_todo_el_dia' => 'boolean',
    ];

    /**
     * Asegura la creación de la tabla de forma resiliente si aún no se ha corrido php artisan migrate.
     */
    public static function ensureTableExists(): void
    {
        try {
            if (!Schema::hasTable('fechas_especiales')) {
                Schema::create('fechas_especiales', function (Blueprint $table) {
                    $table->id();
                    $table->date('fecha')->unique();
                    $table->string('motivo');
                    $table->boolean('cerrado_todo_el_dia')->default(false);
                    $table->time('hora_apertura')->nullable();
                    $table->time('hora_cierre')->nullable();
                    $table->timestamps();
                });
            }
        } catch (\Throwable $e) {
            // Silencioso si ocurre concurrencia o problemas de conexión
        }
    }

    /**
     * Etiqueta formateada del horario o indicador de Cerrado.
     */
    public function getHorarioFormateadoAttribute(): string
    {
        if ($this->cerrado_todo_el_dia) {
            return 'Cerrado';
        }

        $apertura = $this->hora_apertura ? substr((string)$this->hora_apertura, 0, 5) : '--:--';
        $cierre = $this->hora_cierre ? substr((string)$this->hora_cierre, 0, 5) : '--:--';

        return "{$apertura} - {$cierre}";
    }

    /**
     * Fecha formateada en español legible (ej. "25 Dic 2026").
     */
    public function getFechaLegibleAttribute(): string
    {
        if (!$this->fecha) {
            return '';
        }

        $meses = [
            1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr',
            5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago',
            9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic'
        ];

        $carbon = Carbon::parse($this->fecha);
        $mesNombre = $meses[$carbon->month] ?? $carbon->format('M');

        return "{$carbon->day} {$mesNombre} {$carbon->year}";
    }
}
