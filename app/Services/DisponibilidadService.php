<?php

namespace App\Services;

use App\Models\FechaEspecial;
use App\Models\HorarioSucursal;
use App\Models\Service;
use App\Models\Trabajador;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class DisponibilidadService
{
    /**
     * Calcula la lista de horas disponibles para agendar una cita con un trabajador
     * en una fecha específica, aplicando todas las restricciones del negocio.
     *
     * @param Trabajador $trabajador
     * @param string|Carbon $fecha
     * @param int|Service $duracionMinutos Duración en minutos o modelo Service
     * @param Service|null $servicio Opcional si se pasa duración como primer arg
     * @return array Lista de horas disponibles (ej. ['10:00', '10:30', ...])
     */
    public function calcularHorariosDisponibles(Trabajador $trabajador, string|Carbon $fecha, int|Service $duracionMinutos = 30, ?Service $servicio = null): array
    {
        $detalle = $this->obtenerDisponibilidadCompleta($trabajador, $fecha, $duracionMinutos, $servicio);
        return $detalle['horarios_disponibles'] ?? [];
    }

    /**
     * Valida si un horario específico sigue estando disponible en el servidor en tiempo real.
     *
     * @param Trabajador $trabajador
     * @param string|Carbon $fecha
     * @param string $hora 'HH:MM'
     * @param int|Service $duracionMinutos
     * @param Service|null $servicio
     * @return array ['disponible' => bool, 'mensaje' => string]
     */
    public function validarHorarioSeleccionado(Trabajador $trabajador, string|Carbon $fecha, string $hora, int|Service $duracionMinutos = 30, ?Service $servicio = null): array
    {
        $horaNormalizada = $this->normalizarHora($hora);
        if (!$horaNormalizada) {
            return [
                'disponible' => false,
                'mensaje' => 'El horario especificado no tiene un formato válido.',
            ];
        }

        $detalle = $this->obtenerDisponibilidadCompleta($trabajador, $fecha, $duracionMinutos, $servicio);
        $horarios = $detalle['horarios_disponibles'] ?? [];

        if (in_array($horaNormalizada, $horarios, true)) {
            return [
                'disponible' => true,
                'mensaje' => 'Horario disponible.',
                'detalle' => $detalle,
            ];
        }

        // Diagnóstico preciso del motivo de no disponibilidad
        $duracion = (int) ($detalle['duracion_minutos'] ?? 30);
        $horaInicioMin = $this->horaAMinutos($horaNormalizada);
        $horaFinMin = $horaInicioMin + $duracion;

        $limiteCierreStr = $detalle['limite_sucursal']['cierre'] ?? null;
        $limiteCierreMin = $limiteCierreStr ? $this->horaAMinutos($limiteCierreStr) : null;

        $workerSalidaStr = $detalle['horario_trabajador']['salida'] ?? null;
        $workerSalidaMin = $workerSalidaStr ? $this->horaAMinutos($workerSalidaStr) : null;

        $inicioEfectivoStr = $detalle['interseccion']['inicio'] ?? null;
        $inicioEfectivoMin = $inicioEfectivoStr ? $this->horaAMinutos($inicioEfectivoStr) : null;

        // 1. Si el horario seleccionado es previo al inicio de atención
        if ($inicioEfectivoMin !== null && $horaInicioMin < $inicioEfectivoMin) {
            return [
                'disponible' => false,
                'motivo_tipo' => 'previo_apertura',
                'mensaje' => "El horario de las {$horaNormalizada} hrs es previo a la hora de inicio de atención ({$inicioEfectivoStr} hrs).",
                'detalle' => $detalle,
            ];
        }

        // 2. Si el servicio excede la hora de cierre de la sucursal
        if ($limiteCierreMin !== null && $horaFinMin > $limiteCierreMin) {
            $tiempoRestante = max(0, $limiteCierreMin - $horaInicioMin);
            if ($tiempoRestante > 0) {
                $mensaje = "Por la hora de cierre de la sucursal ({$limiteCierreStr} hrs), el servicio seleccionado ({$duracion} min) no está disponible para las {$horaNormalizada} hrs. Para ese horario solo se pueden escoger servicios dentro del rango de {$tiempoRestante} minutos o menor (como 15 o 30 min).";
            } else {
                $mensaje = "El horario seleccionado ({$horaNormalizada} hrs) coincide o es posterior a la hora de cierre de la sucursal ({$limiteCierreStr} hrs).";
            }
            return [
                'disponible' => false,
                'motivo_tipo' => 'cierre_sucursal',
                'mensaje' => $mensaje,
                'detalle' => $detalle,
            ];
        }

        // 3. Si el servicio excede la hora de salida del especialista
        if ($workerSalidaMin !== null && $horaFinMin > $workerSalidaMin) {
            $tiempoRestante = max(0, $workerSalidaMin - $horaInicioMin);
            if ($tiempoRestante > 0) {
                $mensaje = "Por la hora de término de jornada del especialista {$trabajador->nombre_completo} ({$workerSalidaStr} hrs), el servicio seleccionado ({$duracion} min) no está disponible para las {$horaNormalizada} hrs. Para ese horario solo se pueden escoger servicios dentro del rango de {$tiempoRestante} minutos o menor (como 15 o 30 min).";
            } else {
                $mensaje = "El horario seleccionado ({$horaNormalizada} hrs) es posterior o igual al fin de turno del especialista ({$workerSalidaStr} hrs).";
            }
            return [
                'disponible' => false,
                'motivo_tipo' => 'salida_trabajador',
                'mensaje' => $mensaje,
                'detalle' => $detalle,
            ];
        }

        // 4. Conflicto con descansos, bloqueos de disponibilidad o citas previas
        $intervalosOcupados = $detalle['intervalos_ocupados'] ?? [];
        foreach ($intervalosOcupados as $ocupado) {
            if ($this->solapanIntervalos($horaInicioMin, $horaFinMin, $ocupado['inicio'], $ocupado['fin'])) {
                $iniStr = $this->minutosAHora($ocupado['inicio']);
                $finStr = $this->minutosAHora($ocupado['fin']);
                $tipo = $ocupado['tipo'] ?? 'ocupado';
                $motivo = $ocupado['motivo'] ?? '';

                if ($tipo === 'bloqueo') {
                    $mensaje = "El especialista cuenta con un bloqueo de disponibilidad registrado ({$motivo}) de {$iniStr} a {$finStr} hrs. Ese rango de horario no está disponible para citas.";
                } elseif ($tipo === 'descanso') {
                    $mensaje = "El horario seleccionado coincide con el tiempo de descanso del especialista (de {$iniStr} a {$finStr} hrs).";
                } elseif ($tipo === 'cita') {
                    $mensaje = "El horario seleccionado ya no está disponible debido a otra cita reservada previamente (de {$iniStr} a {$finStr} hrs).";
                } else {
                    $mensaje = "El horario seleccionado no está disponible por coincidir con un rango bloqueado de {$iniStr} a {$finStr} hrs.";
                }

                return [
                    'disponible' => false,
                    'motivo_tipo' => $tipo,
                    'mensaje' => $mensaje,
                    'detalle' => $detalle,
                ];
            }
        }

        // 5. Horario pasado del día de hoy
        $tz = config('app.timezone') ?: 'America/Mexico_City';
        if ($tz === 'UTC') $tz = env('APP_TIMEZONE', 'America/Mexico_City');
        $ahora = Carbon::now($tz);
        $fechaString = is_string($fecha) ? $fecha : $fecha->format('Y-m-d');
        if ($fechaString === $ahora->format('Y-m-d')) {
            $minutosAhora = ($ahora->hour * 60) + $ahora->minute;
            if ($horaInicioMin <= $minutosAhora) {
                return [
                    'disponible' => false,
                    'motivo_tipo' => 'hora_pasada',
                    'mensaje' => "El horario de las {$horaNormalizada} hrs ya ha pasado para el día de hoy.",
                    'detalle' => $detalle,
                ];
            }
        }

        return [
            'disponible' => false,
            'motivo_tipo' => 'no_disponible',
            'mensaje' => $detalle['mensaje'] ?: 'El horario seleccionado no está disponible para esta cita.',
            'detalle' => $detalle,
        ];
    }

    /**
     * Obtiene el desglose completo de la disponibilidad, validando:
     * 1. Asignación del servicio al especialista
     * 2. Estado activo del trabajador
     * 3. Fechas especiales / Días no laborables
     * 4. Horarios de apertura y cierre de la sucursal
     * 5. Jornada laboral del trabajador para el día
     * 6. Descansos configurados para el trabajador
     * 7. Bloqueos de disponibilidad registrados
     * 8. Citas existentes para evitar solapamientos
     * 9. Horas pasadas del día de hoy
     *
     * @param Trabajador $trabajador
     * @param string|Carbon $fecha
     * @param int|Service $duracionMinutos
     * @param Service|null $servicio
     * @return array
     */
    public function obtenerDisponibilidadCompleta(Trabajador $trabajador, string|Carbon $fecha, int|Service $duracionMinutos = 30, ?Service $servicio = null): array
    {
        // Resolver servicio y duración en minutos
        if ($duracionMinutos instanceof Service) {
            $servicio = $duracionMinutos;
            $duracion = (int) ($servicio->duration_minutes ?: 30);
        } else {
            $duracion = (int) $duracionMinutos;
            if ($servicio) {
                $duracion = (int) ($servicio->duration_minutes ?: $duracion);
            }
        }

        if ($duracion <= 0) {
            $duracion = 30;
        }

        $carbonFecha = $fecha instanceof Carbon ? $fecha->copy() : Carbon::parse($fecha);
        $fechaString = $carbonFecha->format('Y-m-d');

        // Mapeo de día de la semana a español
        $diasSemana = ['domingo', 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
        $diaSemanaKey = $diasSemana[$carbonFecha->dayOfWeek] ?? 'lunes';

        // -------------------------------------------------------------------------
        // 0. VALIDACIÓN DE SERVICIO ASIGNADO AL TRABAJADOR
        // -------------------------------------------------------------------------
        if ($servicio) {
            $tieneServicioAsignado = $trabajador->servicios()
                ->where('services.id', $servicio->id)
                ->exists();

            if (!$tieneServicioAsignado) {
                return [
                    'fecha' => $fechaString,
                    'dia_semana' => $diaSemanaKey,
                    'es_fecha_especial' => false,
                    'caso' => 'SERVICIO_NO_ASIGNADO',
                    'duracion_minutos' => $duracion,
                    'horarios_disponibles' => [],
                    'mensaje' => "El especialista {$trabajador->nombre_completo} no tiene asignado el servicio \"{$servicio->name}\".",
                ];
            }
        }

        // -------------------------------------------------------------------------
        // 1. VALIDACIÓN DE TRABAJADOR ACTIVO
        // -------------------------------------------------------------------------
        if (isset($trabajador->activo) && !$trabajador->activo) {
            return [
                'fecha' => $fechaString,
                'dia_semana' => $diaSemanaKey,
                'es_fecha_especial' => false,
                'caso' => 'TRABAJADOR_INACTIVO',
                'duracion_minutos' => $duracion,
                'horarios_disponibles' => [],
                'mensaje' => "El especialista {$trabajador->nombre_completo} se encuentra inactivo actualmente.",
            ];
        }

        // -------------------------------------------------------------------------
        // 2. VALIDACIÓN DE FECHA ESPECIAL (Prioridad máxima de sucursal)
        // -------------------------------------------------------------------------
        FechaEspecial::ensureTableExists();
        $fechaEspecial = FechaEspecial::whereDate('fecha', $fechaString)->first();

        if ($fechaEspecial) {
            if ($fechaEspecial->cerrado_todo_el_dia) {
                return [
                    'fecha' => $fechaString,
                    'dia_semana' => $diaSemanaKey,
                    'es_fecha_especial' => true,
                    'caso' => 'CASO_A_CERRADO',
                    'motivo' => $fechaEspecial->motivo,
                    'cerrado_todo_el_dia' => true,
                    'duracion_minutos' => $duracion,
                    'horarios_disponibles' => [],
                    'mensaje' => "La sucursal permanece cerrada el día de hoy por fecha especial ({$fechaEspecial->motivo}).",
                ];
            }

            $horaAperturaEspecial = $this->normalizarHora($fechaEspecial->hora_apertura);
            $horaCierreEspecial = $this->normalizarHora($fechaEspecial->hora_cierre);

            if ($horaAperturaEspecial && $horaCierreEspecial && $horaAperturaEspecial < $horaCierreEspecial) {
                $limiteApertura = $horaAperturaEspecial;
                $limiteCierre = $horaCierreEspecial;
                $esCasoB = true;
                $motivoEspecial = $fechaEspecial->motivo;
            } else {
                return [
                    'fecha' => $fechaString,
                    'dia_semana' => $diaSemanaKey,
                    'es_fecha_especial' => true,
                    'caso' => 'CASO_A_CERRADO',
                    'motivo' => $fechaEspecial->motivo,
                    'cerrado_todo_el_dia' => true,
                    'duracion_minutos' => $duracion,
                    'horarios_disponibles' => [],
                    'mensaje' => "La sucursal permanece cerrada el día de hoy ({$fechaEspecial->motivo}).",
                ];
            }
        } else {
            // Horario regular de la sucursal
            $esCasoB = false;
            $motivoEspecial = null;

            HorarioSucursal::ensureTableExists();
            $branchLimits = HorarioSucursal::getHorariosMap();
            $branchDay = $branchLimits[$diaSemanaKey] ?? null;

            if (!$branchDay || empty($branchDay['abierto'])) {
                return [
                    'fecha' => $fechaString,
                    'dia_semana' => $diaSemanaKey,
                    'es_fecha_especial' => false,
                    'caso' => 'SUCURSAL_CERRADA_REGULAR',
                    'motivo' => null,
                    'cerrado_todo_el_dia' => true,
                    'duracion_minutos' => $duracion,
                    'horarios_disponibles' => [],
                    'mensaje' => "La sucursal permanece cerrada los días " . ucfirst($diaSemanaKey) . ".",
                ];
            }

            $limiteApertura = $this->normalizarHora($branchDay['apertura'] ?? '09:00');
            $limiteCierre = $this->normalizarHora($branchDay['cierre'] ?? '20:00');
        }

        // -------------------------------------------------------------------------
        // 3. JORNADA LABORAL DEL TRABAJADOR
        // -------------------------------------------------------------------------
        $horarioTrabajador = $trabajador->horario ?? [];
        if (is_string($horarioTrabajador)) {
            $horarioTrabajador = json_decode($horarioTrabajador, true) ?: [];
        }

        $turnoDia = $horarioTrabajador[$diaSemanaKey] ?? null;

        if (!$turnoDia || empty($turnoDia['entrada']) || empty($turnoDia['salida'])) {
            return [
                'fecha' => $fechaString,
                'dia_semana' => $diaSemanaKey,
                'es_fecha_especial' => (bool)$fechaEspecial,
                'caso' => 'TRABAJADOR_SIN_TURNO',
                'motivo' => $motivoEspecial,
                'duracion_minutos' => $duracion,
                'horarios_disponibles' => [],
                'mensaje' => "El especialista {$trabajador->nombre_completo} no tiene jornada asignada para los días " . ucfirst($diaSemanaKey) . " (día de descanso).",
            ];
        }

        $workerEntrada = $this->normalizarHora($turnoDia['entrada']);
        $workerSalida = $this->normalizarHora($turnoDia['salida']);

        // Intersección horaria efectiva entre el turno del trabajador y la apertura del estudio
        $inicioEfectivo = ($workerEntrada > $limiteApertura) ? $workerEntrada : $limiteApertura;
        $finEfectivo = ($workerSalida < $limiteCierre) ? $workerSalida : $limiteCierre;

        if ($inicioEfectivo >= $finEfectivo) {
            return [
                'fecha' => $fechaString,
                'dia_semana' => $diaSemanaKey,
                'es_fecha_especial' => (bool)$fechaEspecial,
                'caso' => 'SIN_INTERSECCION',
                'motivo' => $motivoEspecial,
                'duracion_minutos' => $duracion,
                'horarios_disponibles' => [],
                'mensaje' => "El horario del especialista no tiene coincidencia operativa con la apertura de la sucursal para este día.",
            ];
        }

        // -------------------------------------------------------------------------
        // 4. IDENTIFICACIÓN DE INTERVALOS NO DISPONIBLES (Descansos, Bloqueos, Citas)
        // -------------------------------------------------------------------------
        $intervalosOcupados = [];

        // A) Descansos del trabajador configurados para este día
        $descansos = $this->obtenerDescansosTrabajador($turnoDia, $horarioTrabajador, $diaSemanaKey);
        foreach ($descansos as $descanso) {
            $intervalosOcupados[] = [
                'tipo' => 'descanso',
                'inicio' => $this->horaAMinutos($descanso['inicio']),
                'fin' => $this->horaAMinutos($descanso['fin']),
                'motivo' => $descanso['motivo'] ?? 'Descanso laboral',
            ];
        }

        // B) Bloqueos de disponibilidad del trabajador para esta fecha específica
        $bloqueos = $this->obtenerBloqueosTrabajador($trabajador, $fechaString, $diaSemanaKey);
        foreach ($bloqueos as $bloqueo) {
            $intervalosOcupados[] = [
                'tipo' => 'bloqueo',
                'inicio' => $this->horaAMinutos($bloqueo['inicio']),
                'fin' => $this->horaAMinutos($bloqueo['fin']),
                'motivo' => $bloqueo['motivo'] ?? 'Bloqueo administrativo',
            ];
        }

        // C) Citas existentes para esta fecha y trabajador
        $citas = $this->obtenerCitasExistentes($trabajador, $fechaString);
        foreach ($citas as $cita) {
            $intervalosOcupados[] = [
                'tipo' => 'cita',
                'inicio' => $this->horaAMinutos($cita['inicio']),
                'fin' => $this->horaAMinutos($cita['fin']),
                'motivo' => 'Cita reservada',
            ];
        }

        // -------------------------------------------------------------------------
        // 5. CÁLCULO DE HORARIOS VÁLIDOS (SLOTS)
        // -------------------------------------------------------------------------
        $inicioMin = $this->horaAMinutos($inicioEfectivo);
        $finMin = $this->horaAMinutos($finEfectivo);

        // Paso del intervalo de generación: 15 minutos para admitir servicios de 15, 30, 45 y 60 min
        $pasoMinutos = 15;

        // Si la fecha es hoy, calcular la hora actual del negocio para excluir horarios pasados
        $tz = config('app.timezone') ?: 'America/Mexico_City';
        if ($tz === 'UTC') {
            $tz = env('APP_TIMEZONE', 'America/Mexico_City');
        }
        $ahora = Carbon::now($tz);
        $esHoy = ($fechaString === $ahora->format('Y-m-d'));
        $minutosAhora = ($ahora->hour * 60) + $ahora->minute;

        $horariosDisponibles = [];

        for ($candidatoInicio = $inicioMin; ($candidatoInicio + $duracion) <= $finMin; $candidatoInicio += $pasoMinutos) {
            $candidatoFin = $candidatoInicio + $duracion;

            // 1. Si es hoy, no permitir horarios pasados
            if ($esHoy && $candidatoInicio <= $minutosAhora) {
                continue;
            }

            // 2. Verificar que no solape con ningún descanso, bloqueo o cita existente
            $tieneConflicto = false;
            foreach ($intervalosOcupados as $ocupado) {
                if ($this->solapanIntervalos($candidatoInicio, $candidatoFin, $ocupado['inicio'], $ocupado['fin'])) {
                    $tieneConflicto = true;
                    break;
                }
            }

            if (!$tieneConflicto) {
                $horariosDisponibles[] = $this->minutosAHora($candidatoInicio);
            }
        }

        $mensajeExito = count($horariosDisponibles) > 0
            ? "Disponibilidad calculada exitosamente (" . count($horariosDisponibles) . " horarios disponibles)."
            : "No hay horarios disponibles para la fecha seleccionada respetando la jornada y duración del servicio.";

        $ultimoHorario = count($horariosDisponibles) > 0 ? end($horariosDisponibles) : null;
        $avisoCierre = null;

        if ($finEfectivo && $ultimoHorario) {
            $ultimoInicioMin = $this->horaAMinutos($ultimoHorario);
            $finEfectivoStr = $this->minutosAHora($finMin);
            $siguienteSlot = $ultimoInicioMin + $pasoMinutos;
            $espacioEnSiguienteSlot = $finMin - $siguienteSlot;

            if ($espacioEnSiguienteSlot >= 15 && $espacioEnSiguienteSlot < $duracion) {
                $avisoCierre = "La atención concluye a las {$finEfectivoStr} hrs. Por la duración de este servicio ({$duracion} min), el último turno disponible es a las {$ultimoHorario} hrs. Cerca del cierre solo pueden agendarse servicios de menor duración (hasta {$espacioEnSiguienteSlot} min, ej. 15 o 30 min).";
            }
        }

        return [
            'fecha' => $fechaString,
            'dia_semana' => $diaSemanaKey,
            'es_fecha_especial' => (bool)$fechaEspecial,
            'caso' => $esCasoB ? 'CASO_B_INTERSECCION' : 'REGULAR_INTERSECCION',
            'motivo' => $motivoEspecial,
            'cerrado_todo_el_dia' => false,
            'duracion_minutos' => $duracion,
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
            'hora_cierre_efectiva' => $finEfectivo,
            'ultimo_horario' => $ultimoHorario,
            'aviso_cierre' => $avisoCierre,
            'intervalos_ocupados' => $intervalosOcupados,
            'horarios_disponibles' => $horariosDisponibles,
            'mensaje' => $mensajeExito,
        ];
    }

    /**
     * Determina si dos intervalos de tiempo se solapan.
     * Intervalo 1: [inicio1, fin1)
     * Intervalo 2: [inicio2, fin2)
     */
    public function solapanIntervalos(int $inicio1, int $fin1, int $inicio2, int $fin2): bool
    {
        return ($inicio1 < $fin2) && ($fin1 > $inicio2);
    }

    /**
     * Extrae descansos configurados en el turno del trabajador para el día.
     */
    protected function obtenerDescansosTrabajador(array $turnoDia, array $horarioCompleto, string $diaKey): array
    {
        $descansos = [];

        // Formato 1: descanso_inicio y descanso_fin en el turno del día
        if (!empty($turnoDia['descanso_inicio']) && !empty($turnoDia['descanso_fin'])) {
            $descansos[] = [
                'inicio' => $this->normalizarHora($turnoDia['descanso_inicio']),
                'fin' => $this->normalizarHora($turnoDia['descanso_fin']),
                'motivo' => $turnoDia['descanso_motivo'] ?? 'Descanso programado',
            ];
        }

        // Formato 2: comida_inicio / comida_fin
        if (!empty($turnoDia['comida_inicio']) && !empty($turnoDia['comida_fin'])) {
            $descansos[] = [
                'inicio' => $this->normalizarHora($turnoDia['comida_inicio']),
                'fin' => $this->normalizarHora($turnoDia['comida_fin']),
                'motivo' => 'Comida',
            ];
        }

        // Formato 3: almuerzo_inicio / almuerzo_fin o break_inicio / break_fin
        if (!empty($turnoDia['almuerzo_inicio']) && !empty($turnoDia['almuerzo_fin'])) {
            $descansos[] = [
                'inicio' => $this->normalizarHora($turnoDia['almuerzo_inicio']),
                'fin' => $this->normalizarHora($turnoDia['almuerzo_fin']),
                'motivo' => 'Almuerzo',
            ];
        }
        if (!empty($turnoDia['receso_inicio']) && !empty($turnoDia['receso_fin'])) {
            $descansos[] = [
                'inicio' => $this->normalizarHora($turnoDia['receso_inicio']),
                'fin' => $this->normalizarHora($turnoDia['receso_fin']),
                'motivo' => 'Receso',
            ];
        }
        if (!empty($turnoDia['break_inicio']) && !empty($turnoDia['break_fin'])) {
            $descansos[] = [
                'inicio' => $this->normalizarHora($turnoDia['break_inicio']),
                'fin' => $this->normalizarHora($turnoDia['break_fin']),
                'motivo' => 'Break',
            ];
        }

        // Formato 4: objetos anidados en el turno (comida, descanso, almuerzo, receso)
        foreach (['comida', 'descanso', 'almuerzo', 'receso', 'break'] as $subKey) {
            if (!empty($turnoDia[$subKey]) && is_array($turnoDia[$subKey])) {
                $sub = $turnoDia[$subKey];
                $ini = $sub['inicio'] ?? ($sub['start'] ?? ($sub['desde'] ?? null));
                $fin = $sub['fin'] ?? ($sub['end'] ?? ($sub['hasta'] ?? null));
                if ($ini && $fin) {
                    $descansos[] = [
                        'inicio' => $this->normalizarHora($ini),
                        'fin' => $this->normalizarHora($fin),
                        'motivo' => $sub['motivo'] ?? ucfirst($subKey),
                    ];
                }
            }
        }

        // Formato 5: arreglo de descansos dentro del día
        if (!empty($turnoDia['descansos']) && is_array($turnoDia['descansos'])) {
            foreach ($turnoDia['descansos'] as $d) {
                $ini = $d['inicio'] ?? ($d['start'] ?? ($d['desde'] ?? null));
                $fin = $d['fin'] ?? ($d['end'] ?? ($d['hasta'] ?? null));
                if (!empty($ini) && !empty($fin)) {
                    $descansos[] = [
                        'inicio' => $this->normalizarHora($ini),
                        'fin' => $this->normalizarHora($fin),
                        'motivo' => $d['motivo'] ?? ($d['reason'] ?? 'Descanso'),
                    ];
                }
            }
        }

        // Formato 6: descansos generales en la raíz del horario para ese día
        if (!empty($horarioCompleto['descansos'][$diaKey]) && is_array($horarioCompleto['descansos'][$diaKey])) {
            foreach ($horarioCompleto['descansos'][$diaKey] as $d) {
                $ini = $d['inicio'] ?? ($d['start'] ?? ($d['desde'] ?? null));
                $fin = $d['fin'] ?? ($d['end'] ?? ($d['hasta'] ?? null));
                if (!empty($ini) && !empty($fin)) {
                    $descansos[] = [
                        'inicio' => $this->normalizarHora($ini),
                        'fin' => $this->normalizarHora($fin),
                        'motivo' => $d['motivo'] ?? ($d['reason'] ?? 'Descanso'),
                    ];
                }
            }
        }

        return $descansos;
    }

    /**
     * Extrae bloqueos de disponibilidad del trabajador para una fecha dada.
     */
    protected function obtenerBloqueosTrabajador(Trabajador $trabajador, string $fechaString, ?string $diaSemanaKey = null): array
    {
        $bloqueos = [];

        // 1. Bloqueos almacenados dentro del campo JSON 'horario' o atributo 'bloqueos'
        $horarioData = $trabajador->horario ?? [];
        if (is_string($horarioData)) {
            $horarioData = json_decode($horarioData, true) ?: [];
        }

        $bloqueosArray = $horarioData['bloqueos'] ?? ($trabajador->bloqueos ?? []);
        if (is_array($bloqueosArray)) {
            foreach ($bloqueosArray as $b) {
                $bFecha = $b['fecha'] ?? ($b['date'] ?? null);
                $bDia = $b['dia'] ?? ($b['day'] ?? null);

                $aplica = false;
                if ($bFecha) {
                    $bFechaNormalizada = $this->normalizarFechaYmd($bFecha);
                    if ($bFechaNormalizada === $fechaString) {
                        $aplica = true;
                    }
                } elseif ($bDia && $diaSemanaKey) {
                    if (strtolower(trim($bDia)) === strtolower(trim($diaSemanaKey))) {
                        $aplica = true;
                    }
                }

                if ($aplica) {
                    $inicio = $b['inicio'] ?? ($b['hora_inicio'] ?? ($b['start'] ?? ($b['desde'] ?? null)));
                    $fin = $b['fin'] ?? ($b['hora_fin'] ?? ($b['end'] ?? ($b['hasta'] ?? null)));
                    if ($inicio && $fin) {
                        $bloqueos[] = [
                            'inicio' => $this->normalizarHora($inicio),
                            'fin' => $this->normalizarHora($fin),
                            'motivo' => $b['motivo'] ?? ($b['reason'] ?? ($b['descripcion'] ?? 'Bloqueo de disponibilidad')),
                        ];
                    }
                }
            }
        }

        // 2. Si existiese tabla 'bloqueos' en BD de forma desacoplada
        try {
            if (Schema::hasTable('bloqueos_trabajador') || Schema::hasTable('bloqueos')) {
                $tabla = Schema::hasTable('bloqueos_trabajador') ? 'bloqueos_trabajador' : 'bloqueos';
                $registros = DB::table($tabla)
                    ->where('trabajador_id', $trabajador->id)
                    ->whereDate('fecha', $fechaString)
                    ->get();

                foreach ($registros as $reg) {
                    $inicio = $reg->hora_inicio ?? ($reg->inicio ?? null);
                    $fin = $reg->hora_fin ?? ($reg->fin ?? null);
                    if ($inicio && $fin) {
                        $bloqueos[] = [
                            'inicio' => $this->normalizarHora($inicio),
                            'fin' => $this->normalizarHora($fin),
                            'motivo' => $reg->motivo ?? 'Bloqueo administrativo',
                        ];
                    }
                }
            }
        } catch (\Throwable $e) {
            // Silencioso ante tablas ausentes
        }

        return $bloqueos;
    }

    /**
     * Extrae citas existentes para el trabajador y la fecha dada.
     */
    protected function obtenerCitasExistentes(Trabajador $trabajador, string $fechaString): array
    {
        $citas = [];

        // 1. Citas registradas en el JSON horario del trabajador (si existieran)
        $horarioTrabajador = $trabajador->horario ?? [];
        if (is_string($horarioTrabajador)) {
            $horarioTrabajador = json_decode($horarioTrabajador, true) ?: [];
        }
        if (!empty($horarioTrabajador['citas']) && is_array($horarioTrabajador['citas'])) {
            foreach ($horarioTrabajador['citas'] as $c) {
                $f = $this->normalizarFechaYmd($c['fecha'] ?? ($c['date'] ?? null));
                if ($f === $fechaString) {
                    $inicio = $c['inicio'] ?? ($c['start'] ?? ($c['hora'] ?? ($c['hora_inicio'] ?? null)));
                    $fin = $c['fin'] ?? ($c['end'] ?? ($c['hora_fin'] ?? null));
                    if ($inicio && !$fin) {
                        $dur = (int)($c['duracion_minutos'] ?? ($c['duration'] ?? 30));
                        $inicioM = $this->horaAMinutos($inicio);
                        $fin = $this->minutosAHora($inicioM + $dur);
                    }
                    if ($inicio && $fin) {
                        $citas[] = [
                            'inicio' => $this->normalizarHora($inicio),
                            'fin' => $this->normalizarHora($fin),
                        ];
                    }
                }
            }
        }

        // 2. Consulta resiliente si la tabla de citas existe en la base de datos
        try {
            $nombreTablaCitas = null;
            if (Schema::hasTable('citas')) {
                $nombreTablaCitas = 'citas';
            } elseif (Schema::hasTable('appointments')) {
                $nombreTablaCitas = 'appointments';
            }

            if ($nombreTablaCitas) {
                $query = DB::table($nombreTablaCitas)
                    ->where('trabajador_id', $trabajador->id)
                    ->whereDate('fecha', $fechaString);

                // Excluir canceladas si existe columna de estado
                if (Schema::hasColumn($nombreTablaCitas, 'estado')) {
                    $query->whereNotIn('estado', ['cancelada', 'cancelled', 'anulada']);
                }

                $registros = $query->get();

                foreach ($registros as $c) {
                    $inicio = $c->hora_inicio ?? ($c->hora ?? null);
                    $fin = $c->hora_fin ?? null;

                    // Si no tiene hora_fin pero tiene duracion_minutos
                    if ($inicio && !$fin) {
                        $dur = $c->duracion_minutos ?? 30;
                        $inicioM = $this->horaAMinutos($inicio);
                        $fin = $this->minutosAHora($inicioM + $dur);
                    }

                    if ($inicio && $fin) {
                        $citas[] = [
                            'inicio' => $this->normalizarHora($inicio),
                            'fin' => $this->normalizarHora($fin),
                        ];
                    }
                }
            }
        } catch (\Throwable $e) {
            // Silencioso si la tabla aún no ha sido creada
        }

        return $citas;
    }

    /**
     * Genera la lista de intervalos de tiempo válidos dentro del rango [inicio, fin].
     * (Mantenido por compatibilidad con código existente)
     */
    public function generarSlotsHorarios(string $inicio, string $fin, int $duracionMinutos = 30): array
    {
        $slots = [];
        $inicioMin = $this->horaAMinutos($inicio);
        $finMin = $this->horaAMinutos($fin);

        if ($duracionMinutos <= 0) {
            $duracionMinutos = 30;
        }

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

    /**
     * Normaliza una fecha a YYYY-MM-DD.
     */
    protected function normalizarFechaYmd(?string $fecha): ?string
    {
        if (empty($fecha)) {
            return null;
        }
        $fecha = trim($fecha);
        // Si viene en formato DD/MM/YYYY
        if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $fecha, $m)) {
            return sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
        }
        try {
            return Carbon::parse($fecha)->format('Y-m-d');
        } catch (\Throwable $e) {
            return $fecha;
        }
    }
}
