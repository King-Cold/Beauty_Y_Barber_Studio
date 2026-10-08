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
            'sabado'    => ['id' => 'sabado', 'nombre' => 'Sábado', 'abierto' => false, 'apertura' => '10:00', 'cierre' => '18:00'],
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

        return $default;
    }
}
