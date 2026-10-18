<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class HorarioSucursal extends Model
{
    use HasFactory;

    protected $table = 'horarios_sucursal';

    protected $fillable = [
        'dia',
        'abierto',
        'apertura',
        'cierre',
    ];

    protected $casts = [
        'abierto' => 'boolean',
    ];

    /**
     * Asegura la creación de la tabla de forma resiliente si aún no se ha ejecutado la migración.
     */
    public static function ensureTableExists(): void
    {
        try {
            if (!Schema::hasTable('horarios_sucursal')) {
                Schema::create('horarios_sucursal', function (Blueprint $table) {
                    $table->id();
                    $table->string('dia', 20)->unique();
                    $table->boolean('abierto')->default(true);
                    $table->string('apertura', 10)->default('09:00');
                    $table->string('cierre', 10)->default('20:00');
                    $table->timestamps();
                });
            } else {
                // Si la tabla ya existía pero carece de algunas columnas requeridas
                if (!Schema::hasColumn('horarios_sucursal', 'dia')) {
                    Schema::table('horarios_sucursal', function (Blueprint $table) {
                        $table->string('dia', 20)->nullable();
                    });
                }
                if (!Schema::hasColumn('horarios_sucursal', 'abierto')) {
                    Schema::table('horarios_sucursal', function (Blueprint $table) {
                        $table->boolean('abierto')->default(true);
                    });
                }
                if (!Schema::hasColumn('horarios_sucursal', 'apertura')) {
                    Schema::table('horarios_sucursal', function (Blueprint $table) {
                        $table->string('apertura', 10)->default('09:00');
                    });
                }
                if (!Schema::hasColumn('horarios_sucursal', 'cierre')) {
                    Schema::table('horarios_sucursal', function (Blueprint $table) {
                        $table->string('cierre', 10)->default('20:00');
                    });
                }
                if (!Schema::hasColumn('horarios_sucursal', 'created_at')) {
                    Schema::table('horarios_sucursal', function (Blueprint $table) {
                        $table->timestamps();
                    });
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Error en ensureTableExists de HorarioSucursal: ' . $e->getMessage());
        }
    }

    /**
     * Devuelve el mapa consolidado de horarios de la sucursal para cada día de la semana.
     */
    public static function getHorariosMap(): array
    {
        static::ensureTableExists();

        $default = [
            'lunes'     => ['id' => 'lunes', 'nombre' => 'Lunes', 'abierto' => true, 'apertura' => '09:00', 'cierre' => '20:00'],
            'martes'    => ['id' => 'martes', 'nombre' => 'Martes', 'abierto' => true, 'apertura' => '09:00', 'cierre' => '20:00'],
            'miercoles' => ['id' => 'miercoles', 'nombre' => 'Miércoles', 'abierto' => true, 'apertura' => '09:00', 'cierre' => '20:00'],
            'jueves'    => ['id' => 'jueves', 'nombre' => 'Jueves', 'abierto' => true, 'apertura' => '09:00', 'cierre' => '20:00'],
            'viernes'   => ['id' => 'viernes', 'nombre' => 'Viernes', 'abierto' => true, 'apertura' => '09:00', 'cierre' => '20:00'],
            'sabado'    => ['id' => 'sabado', 'nombre' => 'Sábado', 'abierto' => true, 'apertura' => '08:00', 'cierre' => '12:00'],
            'domingo'   => ['id' => 'domingo', 'nombre' => 'Domingo', 'abierto' => false, 'apertura' => '10:00', 'cierre' => '15:00'],
        ];

        try {
            if (Schema::hasTable('horarios_sucursal')) {
                $dbRecords = static::all();
                if ($dbRecords->isNotEmpty()) {
                    foreach ($dbRecords as $rec) {
                        $diaRaw = strtolower(trim((string)$rec->dia));
                        $diaKey = str_replace(['á', 'é', 'í', 'ó', 'ú'], ['a', 'e', 'i', 'o', 'u'], $diaRaw);
                        if (isset($default[$diaKey])) {
                            $default[$diaKey]['abierto'] = (bool) $rec->abierto;
                            $default[$diaKey]['apertura'] = substr((string)$rec->apertura, 0, 5);
                            $default[$diaKey]['cierre'] = substr((string)$rec->cierre, 0, 5);
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            // Retorna los defaults seguros
        }

        // Restricciones obligatorias fijas (Subtarea 3):
        // Sábados: horario fijo de 08:00 a 12:00 horas
        // Domingos: días no laborables (no habilitables)
        $default['sabado']['abierto'] = true;
        $default['sabado']['apertura'] = '08:00';
        $default['sabado']['cierre'] = '12:00';
        $default['domingo']['abierto'] = false;

        return $default;
    }

    /**
     * Determina si una fecha específica corresponde a un día laborable de la sucursal.
     * Reutiliza la configuración centralizada de horarios y fechas especiales como fuente de verdad.
     *
     * @param string|\DateTimeInterface $fecha
     * @return array ['laborable' => bool, 'motivo' => string|null, 'mensaje' => string, 'dia_clave' => string|null, 'horario' => array|null]
     */
    public static function esFechaLaborable(string|\DateTimeInterface $fecha): array
    {
        try {
            $carbonFecha = $fecha instanceof \Carbon\CarbonInterface
                ? $fecha->copy()
                : \Illuminate\Support\Carbon::parse($fecha);
        } catch (\Throwable $e) {
            return [
                'laborable' => false,
                'motivo' => 'Fecha inválida',
                'tipo' => 'fecha_invalida',
                'mensaje' => 'La fecha proporcionada no tiene un formato válido.',
                'dia_clave' => null,
                'horario' => null,
            ];
        }

        $fechaStr = $carbonFecha->format('Y-m-d');
        $diasSemana = ['domingo', 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
        $diaClave = $diasSemana[$carbonFecha->dayOfWeek] ?? 'lunes';

        // 1. Revisar si coincide con una fecha especial cerrada todo el día
        try {
            if (Schema::hasTable('fechas_especiales')) {
                $fechaEspecial = \App\Models\FechaEspecial::whereDate('fecha', $fechaStr)->first();
                if ($fechaEspecial && $fechaEspecial->cerrado_todo_el_dia) {
                    return [
                        'laborable' => false,
                        'motivo' => $fechaEspecial->motivo,
                        'tipo' => 'fecha_especial',
                        'mensaje' => "No es posible agendar citas en días no laborables. La sucursal permanece cerrada por fecha especial ({$fechaEspecial->motivo}) (Cerrado, no se labora).",
                        'dia_clave' => $diaClave,
                        'horario' => null,
                    ];
                }
            }
        } catch (\Throwable $e) {
            // Continuar con horario regular
        }

        // 2. Revisar configuración de horarios de la sucursal (fuente de verdad)
        $horariosMap = static::getHorariosMap();
        $configDia = $horariosMap[$diaClave] ?? null;

        if (!$configDia || empty($configDia['abierto'])) {
            $nombreDia = $configDia['nombre'] ?? ucfirst($diaClave);
            $mensaje = ($diaClave === 'domingo')
                ? 'Los domingos son días no laborables de la sucursal. No se pueden agendar citas en este día (Cerrado, no se labora).'
                : "El día {$nombreDia} está configurado como día no laborable en la sucursal. No se pueden agendar citas en este día (Cerrado, no se labora).";

            return [
                'laborable' => false,
                'motivo' => $nombreDia,
                'tipo' => 'dia_no_laborable',
                'mensaje' => $mensaje,
                'dia_clave' => $diaClave,
                'horario' => $configDia,
            ];
        }

        return [
            'laborable' => true,
            'motivo' => null,
            'tipo' => 'laborable',
            'mensaje' => 'Día laborable habilitado.',
            'dia_clave' => $diaClave,
            'horario' => $configDia,
        ];
    }
}
