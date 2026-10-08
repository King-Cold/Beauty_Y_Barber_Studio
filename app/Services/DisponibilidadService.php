<?php

namespace App\Services;

use App\Models\FechaEspecial;
use App\Models\HorarioSucursal;
use App\Models\Trabajador;
use Illuminate\Support\Carbon;

class DisponibilidadService
{
    /**
     * Calcula la lista de horas disponibles para agendar una cita con un trabajador
     * en una fecha específica, aplicando con máxima prioridad las reglas de Fechas Especiales.
     *
     * @param Trabajador $trabajador
     * @param string|Carbon $fecha
     * @param int $duracionMinutos Duración de la cita o intervalo entre horarios (por defecto 30 min)
     * @return array Lista de horas disponibles (ej. ['10:00', '10:30', ...])
     */
    public function calcularHorariosDisponibles(Trabajador $trabajador, string|Carbon $fecha, int $duracionMinutos = 30): array
    {
        $detalle = $this->obtenerDisponibilidadCompleta($trabajador, $fecha, $duracionMinutos);
        return $detalle['horarios_disponibles'] ?? [];
    }

    /**
     * Obtiene el desglose completo de la disponibilidad, documentando el caso aplicado,
     * los límites de la sucursal, el turno del trabajador y la intersección horaria.
     *
     * @param Trabajador $trabajador
     * @param string|Carbon $fecha
     * @param int $duracionMinutos
     * @return array
     */
    public function obtenerDisponibilidadCompleta(Trabajador $trabajador, string|Carbon $fecha, int $duracionMinutos = 30): array
    {
        $carbonFecha = $fecha instanceof Carbon ? $fecha->copy() : Carbon::parse($fecha);
        $fechaString = $carbonFecha->format('Y-m-d');

        // Mapeo de día de la semana a español
        $diasSemana = ['domingo', 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
        $diaSemanaKey = $diasSemana[$carbonFecha->dayOfWeek] ?? 'lunes';

        // =========================================================================
        // 1. INTERCEPCIÓN DE FECHA ESPECIAL (Prioridad 1)
        // =========================================================================
        FechaEspecial::ensureTableExists();
        $fechaEspecial = FechaEspecial::whereDate('fecha', $fechaString)->first();

        if ($fechaEspecial) {
            // CASO A: Cerrado todo el día
            // Si cerrado_todo_el_dia es true, el algoritmo se detiene inmediatamente
            // y retorna un arreglo vacío (cero horarios disponibles para agendar ese día),
            // ignorando el horario regular del trabajador.
            if ($fechaEspecial->cerrado_todo_el_dia) {
                return [
                    'fecha' => $fechaString,
                    'dia_semana' => $diaSemanaKey,
                    'es_fecha_especial' => true,
                    'caso' => 'CASO_A_CERRADO',
                    'motivo' => $fechaEspecial->motivo,
                    'cerrado_todo_el_dia' => true,
                    'limite_sucursal' => null,
                    'horario_trabajador' => null,
                    'interseccion' => null,
                    'horarios_disponibles' => [],
                    'mensaje' => "La sucursal permanece cerrada todo el día por fecha especial ({$fechaEspecial->motivo}). Cero horarios disponibles.",
                ];
            }

            // CASO B: Horario modificado
            // Si la fecha especial NO está cerrada todo el día, pero tiene hora_apertura y hora_cierre,
            // el algoritmo usa temporalmente este horario especial como el 'límite máximo' de la sucursal
            // para ese día, reemplazando el horario regular.
            $horaAperturaEspecial = $this->normalizarHora($fechaEspecial->hora_apertura);
            $horaCierreEspecial = $this->normalizarHora($fechaEspecial->hora_cierre);

            if ($horaAperturaEspecial && $horaCierreEspecial) {
                $limiteApertura = $horaAperturaEspecial;
                $limiteCierre = $horaCierreEspecial;
                $esCasoB = true;
                $motivoEspecial = $fechaEspecial->motivo;
            } else {
                // Si faltasen las horas especiales de apertura o cierre
                return [
                    'fecha' => $fechaString,
                    'dia_semana' => $diaSemanaKey,
                    'es_fecha_especial' => true,
                    'caso' => 'CASO_A_CERRADO',
                    'motivo' => $fechaEspecial->motivo,
                    'cerrado_todo_el_dia' => true,
                    'limite_sucursal' => null,
                    'horario_trabajador' => null,
                    'interseccion' => null,
                    'horarios_disponibles' => [],
                    'mensaje' => "La sucursal permanece cerrada el día de hoy ({$fechaEspecial->motivo}).",
                ];
            }
        } else {
            // No existe fecha especial para este día: consultar horario regular de la sucursal
            $esCasoB = false;
            $motivoEspecial = null;

            HorarioSucursal::ensureTableExists();
            $branchLimits = HorarioSucursal::getHorariosMap();
            $branchDay = $branchLimits[$diaSemanaKey] ?? null;

            // Si la sucursal no abre en su horario regular este día de la semana
            if (!$branchDay || empty($branchDay['abierto'])) {
                return [
                    'fecha' => $fechaString,
                    'dia_semana' => $diaSemanaKey,
                    'es_fecha_especial' => false,
                    'caso' => 'SUCURSAL_CERRADA_REGULAR',
                    'motivo' => null,
                    'cerrado_todo_el_dia' => true,
                    'limite_sucursal' => null,
                    'horario_trabajador' => null,
                    'interseccion' => null,
                    'horarios_disponibles' => [],
                    'mensaje' => "La sucursal permanece cerrada regularmente los días {$diaSemanaKey}.",
                ];
            }

            $limiteApertura = $this->normalizarHora($branchDay['apertura'] ?? '09:00');
            $limiteCierre = $this->normalizarHora($branchDay['cierre'] ?? '20:00');
        }

        // =========================================================================
        // 2. APLICACIÓN DEL HORARIO DEL TRABAJADOR (Prioridad 2)
        // =========================================================================
        // Si el trabajador se encuentra inactivo, no tiene disponibilidad
        if (isset($trabajador->activo) && !$trabajador->activo) {
            return [
                'fecha' => $fechaString,
                'dia_semana' => $diaSemanaKey,
                'es_fecha_especial' => (bool)$fechaEspecial,
                'caso' => 'TRABAJADOR_INACTIVO',
                'motivo' => $motivoEspecial,
                'limite_sucursal' => ['apertura' => $limiteApertura, 'cierre' => $limiteCierre],
                'horario_trabajador' => null,
                'interseccion' => null,
                'horarios_disponibles' => [],
                'mensaje' => "El especialista {$trabajador->nombre_completo} se encuentra inactivo.",
            ];
        }

        $horarioTrabajador = $trabajador->horario ?? [];
        $turnoDia = $horarioTrabajador[$diaSemanaKey] ?? null;

        // Si el trabajador no tiene turno laboral configurado para este día de la semana
        if (!$turnoDia || empty($turnoDia['entrada']) || empty($turnoDia['salida'])) {
            return [
                'fecha' => $fechaString,
                'dia_semana' => $diaSemanaKey,
                'es_fecha_especial' => (bool)$fechaEspecial,
                'caso' => 'TRABAJADOR_SIN_TURNO',
                'motivo' => $motivoEspecial,
                'limite_sucursal' => ['apertura' => $limiteApertura, 'cierre' => $limiteCierre],
                'horario_trabajador' => null,
                'interseccion' => null,
                'horarios_disponibles' => [],
                'mensaje' => "El especialista {$trabajador->nombre_completo} no tiene turno laboral asignado para el día {$diaSemanaKey}.",
            ];
        }

        $workerEntrada = $this->normalizarHora($turnoDia['entrada']);
        $workerSalida = $this->normalizarHora($turnoDia['salida']);

        // INTERSECCIÓN HORARIA:
        // En el CASO B (o regular), la intersección determina las horas donde el turno
        // del trabajador coincide con el horario (especial o regular) de la sucursal.
        $inicioEfectivo = ($workerEntrada > $limiteApertura) ? $workerEntrada : $limiteApertura;
        $finEfectivo = ($workerSalida < $limiteCierre) ? $workerSalida : $limiteCierre;

        // Si no hay coincidencia / intersección entre el turno del trabajador y el horario de la sucursal
        if ($inicioEfectivo >= $finEfectivo) {
            return [
                'fecha' => $fechaString,
                'dia_semana' => $diaSemanaKey,
                'es_fecha_especial' => (bool)$fechaEspecial,
                'caso' => $esCasoB ? 'CASO_B_SIN_INTERSECCION' : 'SIN_INTERSECCION',
                'motivo' => $motivoEspecial,
                'cerrado_todo_el_dia' => false,
                'limite_sucursal' => [
                    'apertura' => $limiteApertura,
                    'cierre' => $limiteCierre,
                    'tipo' => $esCasoB ? 'especial' : 'regular',
                ],
                'horario_trabajador' => [
                    'entrada' => $workerEntrada,
                    'salida' => $workerSalida,
                ],
                'interseccion' => null,
                'horarios_disponibles' => [],
                'mensaje' => $esCasoB
                    ? "El horario del trabajador ({$workerEntrada} - {$workerSalida}) no coincide con el horario especial de la sucursal ({$limiteApertura} - {$limiteCierre})."
                    : "El horario del trabajador no tiene coincidencia horaria con la apertura de la sucursal.",
            ];
        }

        // Generar las horas / slots disponibles dentro de la intersección calculada
        $slots = $this->generarSlotsHorarios($inicioEfectivo, $finEfectivo, $duracionMinutos);

        return [
            'fecha' => $fechaString,
            'dia_semana' => $diaSemanaKey,
            'es_fecha_especial' => (bool)$fechaEspecial,
            'caso' => $esCasoB ? 'CASO_B_INTERSECCION' : 'REGULAR_INTERSECCION',
            'motivo' => $motivoEspecial,
            'cerrado_todo_el_dia' => false,
            'limite_sucursal' => [
                'apertura' => $limiteApertura,
                'cierre' => $limiteCierre,
                'tipo' => $esCasoB ? 'especial' : 'regular',
            ],
            'horario_trabajador' => [
                'entrada' => $workerEntrada,
                'salida' => $workerSalida,
            ],
            'interseccion' => [
                'inicio' => $inicioEfectivo,
                'fin' => $finEfectivo,
            ],
            'horarios_disponibles' => $slots,
            'mensaje' => $esCasoB
                ? "Horarios disponibles calculados con intersección de horario especial ({$motivoEspecial}: {$limiteApertura} a {$limiteCierre})."
                : "Horarios regulares calculados exitosamente.",
        ];
    }

    /**
     * Genera la lista de intervalos de tiempo válidos dentro del rango [inicio, fin].
     *
     * @param string $inicio Formato HH:MM
     * @param string $fin Formato HH:MM
     * @param int $duracionMinutos Intervalo o duración de la cita
     * @return array
     */
    public function generarSlotsHorarios(string $inicio, string $fin, int $duracionMinutos = 30): array
    {
        $slots = [];
        $inicioMin = $this->horaAMinutos($inicio);
        $finMin = $this->horaAMinutos($fin);

        if ($duracionMinutos <= 0) {
            $duracionMinutos = 30;
        }

        // Cada horario donde la cita cabe dentro del rango disponible
        for ($current = $inicioMin; ($current + $duracionMinutos) <= $finMin; $current += $duracionMinutos) {
            $slots[] = $this->minutosAHora($current);
        }

        return $slots;
    }

    /**
     * Convierte una cadena de hora a minutos desde las 00:00.
     */
    public function horaAMinutos(string $hora): int
    {
        $partes = explode(':', trim($hora));
        $h = isset($partes[0]) ? (int)$partes[0] : 0;
        $m = isset($partes[1]) ? (int)$partes[1] : 0;
        return ($h * 60) + $m;
    }

    /**
     * Convierte minutos desde las 00:00 a formato HH:MM.
     */
    public function minutosAHora(int $minutos): string
    {
        $h = intdiv($minutos, 60);
        $m = $minutos % 60;
        return sprintf('%02d:%02d', $h, $m);
    }

    /**
     * Normaliza cualquier formato de hora a HH:MM.
     */
    public function normalizarHora(?string $hora): ?string
    {
        if (empty($hora)) {
            return null;
        }
        $trimmed = trim((string)$hora);
        if (strlen($trimmed) >= 5) {
            return substr($trimmed, 0, 5);
        }
        return $trimmed;
    }
}
