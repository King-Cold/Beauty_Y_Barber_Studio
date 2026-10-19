<?php

namespace App\Services;

use App\Models\Cita;
use App\Models\HorarioSucursal;
use App\Models\Service;
use App\Models\Trabajador;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CitaCalendarioService
{
    /**
     * Consulta las citas dentro de un rango de fechas con filtros opcionales.
     */
    public function obtenerEventosCalendario(string $startDate, string $endDate, ?int $workerId = null, ?string $estado = null): array
    {
        Cita::ensureTableExists();

        $query = Cita::with(['cliente', 'service', 'trabajador.user'])
            ->whereDate('fecha', '>=', $startDate)
            ->whereDate('fecha', '<=', $endDate);

        if ($workerId && $workerId > 0) {
            $query->where('trabajador_id', $workerId);
        }

        if ($estado && $estado !== 'todas') {
            if ($estado === 'vencida') {
                $tz = config('app.timezone') ?: 'America/Mexico_City';
                $now = Carbon::now($tz);
                $today = $now->format('Y-m-d');
                $currentTime = $now->format('H:i');

                $query->where(function ($q) use ($today, $currentTime) {
                    $q->where('estado', 'vencida')
                      ->orWhere(function ($sub) use ($today, $currentTime) {
                          $sub->where('estado', 'confirmada')
                              ->where(function ($dateQ) use ($today, $currentTime) {
                                  $dateQ->whereDate('fecha', '<', $today)
                                        ->orWhere(function ($timeQ) use ($today, $currentTime) {
                                            $timeQ->whereDate('fecha', $today)
                                                  ->where('hora_fin', '<', $currentTime);
                                        });
                              });
                      });
                });
            } elseif ($estado === 'confirmada') {
                $tz = config('app.timezone') ?: 'America/Mexico_City';
                $now = Carbon::now($tz);
                $today = $now->format('Y-m-d');
                $currentTime = $now->format('H:i');

                // Excluir citas expiradas/vencidas: solo citas confirmadas vigentes
                $query->where('estado', 'confirmada')
                      ->where(function ($dateQ) use ($today, $currentTime) {
                          $dateQ->whereDate('fecha', '>', $today)
                                ->orWhere(function ($timeQ) use ($today, $currentTime) {
                                    $timeQ->whereDate('fecha', $today)
                                          ->where('hora_fin', '>=', $currentTime);
                                });
                      });
            } elseif ($estado === 'en_proceso' || $estado === 'en_atencion') {
                $query->whereIn('estado', ['en_proceso', 'en_atencion']);
            } else {
                $query->where('estado', $estado);
            }
        }

        $citas = $query->orderBy('fecha', 'asc')
            ->orderBy('hora_inicio', 'asc')
            ->get();

        $tz = config('app.timezone') ?: 'America/Mexico_City';
        $now = Carbon::now($tz);
        $today = $now->format('Y-m-d');
        $currentTime = $now->format('H:i');

        return $citas->map(function ($cita) use ($today, $currentTime) {
            $fechaStr = $cita->fecha instanceof Carbon ? $cita->fecha->format('Y-m-d') : (string)$cita->fecha;
            
            // Determinar estado visual
            $rawEstado = strtolower(trim((string)$cita->estado));
            $estadoVisual = $rawEstado;
            
            if ($rawEstado === 'en_atencion') {
                $estadoVisual = 'en_proceso';
            } elseif ($rawEstado === 'confirmada') {
                $esPasada = ($fechaStr < $today) || ($fechaStr === $today && $cita->hora_fin < $currentTime);
                if ($esPasada) {
                    $estadoVisual = 'vencida';
                }
            }

            $etiquetasEstado = [
                'confirmada' => 'Confirmada',
                'en_proceso' => 'En proceso',
                'en_atencion' => 'En proceso',
                'completada' => 'Completada',
                'cancelada' => 'Cancelada',
                'vencida' => 'Vencida',
            ];

            $clienteNombre = $cita->cliente 
                ? trim($cita->cliente->name . ' ' . ($cita->cliente->apellidos ?? ''))
                : 'Cliente #' . $cita->user_id;

            $trabajadorNombre = $cita->trabajador && $cita->trabajador->user
                ? trim($cita->trabajador->user->name . ' ' . ($cita->trabajador->user->apellidos ?? ''))
                : 'Especialista #' . $cita->trabajador_id;

            return [
                'id' => $cita->id,
                'folio' => 'CITA-' . str_pad((string)$cita->id, 5, '0', STR_PAD_LEFT),
                'fecha' => $fechaStr,
                'hora_inicio' => substr($cita->hora_inicio, 0, 5),
                'hora_fin' => substr($cita->hora_fin, 0, 5),
                'duracion_minutos' => (int)$cita->duracion_minutos,
                'precio' => (float)$cita->precio,
                'precio_formateado' => '$' . number_format($cita->precio, 2),
                'estado' => $rawEstado,
                'estado_pago' => $cita->estado_pago,
                'estado_pago_label' => $cita->estado_pago_label,
                'esta_pagada' => $cita->esta_pagada,
                'puede_marcar_como_pagado' => $cita->puedeMarcarComoPagado(),
                'estado_visual' => $estadoVisual,
                'estado_label' => $etiquetasEstado[$estadoVisual] ?? ucfirst($estadoVisual),
                'cliente' => [
                    'id' => $cita->cliente?->id ?? $cita->user_id,
                    'nombre_completo' => $clienteNombre,
                    'telefono' => $cita->cliente?->telefono ?? 'No registrado',
                    'email' => $cita->cliente?->email ?? 'Sin correo',
                ],
                'trabajador' => [
                    'id' => $cita->trabajador_id,
                    'nombre_completo' => $trabajadorNombre,
                ],
                'servicio' => [
                    'id' => $cita->service_id,
                    'nombre' => $cita->service?->name ?? 'Servicio general',
                    'categoria' => $cita->service?->category ?? 'Estudio',
                    'duracion_minutos' => $cita->service?->duration_minutes ?? $cita->duracion_minutos,
                ],
                'notas' => $cita->notas ?? '',
                'observaciones' => $cita->observaciones ?? '',
                'checked_in' => !is_null($cita->check_in_at),
                'check_in_at' => $cita->check_in_at ? $cita->check_in_at->format('Y-m-d H:i:s') : null,
                'check_in_hora' => $cita->check_in_at ? $cita->check_in_at->format('H:i') : null,
                'puede_check_in' => $cita->puedeRegistrarCheckIn(),
                'es_pasada' => $cita->esPasada(),
                'puede_cancelar' => $cita->puedeCancelarAdministrativamente(),
            ];
        })->toArray();
    }

    /**
     * Obtiene el detalle formateado de una cita específica.
     */
    public function obtenerDetalleCita(Cita $cita): array
    {
        $cita->load(['cliente', 'service', 'trabajador.user']);
        $fechaStr = $cita->fecha instanceof Carbon ? $cita->fecha->format('Y-m-d') : (string)$cita->fecha;
        
        $tz = config('app.timezone') ?: 'America/Mexico_City';
        $now = Carbon::now($tz);
        $today = $now->format('Y-m-d');
        $currentTime = $now->format('H:i');

        $rawEstado = strtolower(trim((string)$cita->estado));
        $estadoVisual = $rawEstado;
        if ($rawEstado === 'en_atencion') {
            $estadoVisual = 'en_proceso';
        } elseif ($rawEstado === 'confirmada') {
            $esPasada = ($fechaStr < $today) || ($fechaStr === $today && $cita->hora_fin < $currentTime);
            if ($esPasada) {
                $estadoVisual = 'vencida';
            }
        }

        $etiquetasEstado = [
            'confirmada' => 'Confirmada',
            'en_proceso' => 'En proceso',
            'en_atencion' => 'En proceso',
            'completada' => 'Completada',
            'cancelada' => 'Cancelada',
            'vencida' => 'Vencida',
        ];

        return [
            'id' => $cita->id,
            'folio' => 'CITA-' . str_pad((string)$cita->id, 5, '0', STR_PAD_LEFT),
            'fecha' => $fechaStr,
            'fecha_formateada' => Carbon::parse($fechaStr)->locale('es')->isoFormat('dddd D [de] MMMM [de] YYYY'),
            'hora_inicio' => substr($cita->hora_inicio, 0, 5),
            'hora_fin' => substr($cita->hora_fin, 0, 5),
            'duracion_minutos' => (int)$cita->duracion_minutos,
            'precio' => (float)$cita->precio,
            'precio_formateado' => '$' . number_format($cita->precio, 2),
            'estado' => $rawEstado,
            'estado_pago' => $cita->estado_pago,
            'estado_pago_label' => $cita->estado_pago_label,
            'esta_pagada' => $cita->esta_pagada,
            'puede_marcar_como_pagado' => $cita->puedeMarcarComoPagado(),
            'estado_visual' => $estadoVisual,
            'estado_label' => $etiquetasEstado[$estadoVisual] ?? ucfirst($estadoVisual),
            'cliente' => [
                'id' => $cita->cliente?->id ?? $cita->user_id,
                'nombre_completo' => $cita->cliente ? trim($cita->cliente->name . ' ' . ($cita->cliente->apellidos ?? '')) : 'Cliente #' . $cita->user_id,
                'telefono' => $cita->cliente?->telefono ?? 'No registrado',
                'email' => $cita->cliente?->email ?? 'Sin correo',
            ],
            'trabajador' => [
                'id' => $cita->trabajador_id,
                'nombre_completo' => $cita->trabajador && $cita->trabajador->user ? trim($cita->trabajador->user->name . ' ' . ($cita->trabajador->user->apellidos ?? '')) : 'Especialista #' . $cita->trabajador_id,
            ],
            'servicio' => [
                'id' => $cita->service_id,
                'nombre' => $cita->service?->name ?? 'Servicio general',
                'categoria' => $cita->service?->category ?? 'Estudio',
                'duracion_minutos' => $cita->service?->duration_minutes ?? $cita->duracion_minutos,
            ],
            'notas' => $cita->notas ?? '',
            'observaciones' => $cita->observaciones ?? '',
            'checked_in' => $cita->isCheckedIn(),
            'check_in_at' => $cita->check_in_at ? $cita->check_in_at->format('Y-m-d H:i:s') : null,
            'check_in_hora' => $cita->check_in_at ? $cita->check_in_at->format('H:i') : null,
            'check_in_formateado' => $cita->check_in_formateado,
            'puede_check_in' => $cita->puedeRegistrarCheckIn(),
            'puede_iniciar_atencion' => $cita->puedeIniciarAtencion(),
            'puede_finalizar_atencion' => $cita->puedeFinalizarAtencion(),
            'es_pasada' => $cita->esPasada(),
            'puede_cancelar' => $cita->puedeCancelarAdministrativamente(),
        ];
    }

    /**
     * Valida si la transición desde el estado actual de la cita hacia el nuevo estado es legal.
     * Lanza DomainException o InvalidArgumentException si la transición no está permitida.
     */
    public function validarTransicion(Cita $cita, string $nuevoEstado, ?User $usuario = null): void
    {
        $estadoActual = strtolower(trim((string)$cita->estado));
        $estadoDestino = strtolower(trim((string)$nuevoEstado));

        // Normalizar compatibilidad en_proceso <-> en_atencion
        if ($estadoDestino === 'en_proceso') {
            $estadoDestino = 'en_atencion';
        }
        if ($estadoActual === 'en_proceso') {
            $estadoActual = 'en_atencion';
        }

        $estadosValidos = ['confirmada', 'en_atencion', 'completada', 'cancelada', 'vencida'];
        if (!in_array($estadoDestino, $estadosValidos, true)) {
            throw new \InvalidArgumentException("El estado '{$nuevoEstado}' no es reconocido por el sistema.");
        }

        // Prevención de operaciones duplicadas o solicitudes repetidas sobre el mismo estado
        if ($estadoActual === $estadoDestino) {
            if ($estadoDestino === 'en_atencion') {
                throw new \DomainException('La cita ya se encuentra en proceso de atención.');
            }
            if ($estadoDestino === 'completada') {
                throw new \DomainException('La cita ya ha sido completada previamente.');
            }
            if ($estadoDestino === 'cancelada') {
                throw new \DomainException('Una cita cancelada no puede reactivarse ni modificarse.');
            }
            if ($estadoDestino === 'confirmada') {
                throw new \DomainException('La cita ya se encuentra en estado confirmada.');
            }
            return;
        }

        // 1. REGLA DE ESTADOS TERMINALES (Cancelada y Completada)
        if ($estadoActual === 'cancelada') {
            throw new \DomainException('Una cita cancelada no puede reactivarse ni cambiarse a otro estado.');
        }

        if ($estadoActual === 'completada') {
            throw new \DomainException('Una cita completada es un registro final y no puede volver a confirmada, en atención ni cancelada.');
        }

        // 1.1 REGLA DE CANCELACIÓN: Impedir cancelación de citas pasadas
        if ($estadoDestino === 'cancelada' && $cita->esPasada()) {
            throw new \DomainException('No se puede cancelar una cita cuya fecha u hora ya ha transcurrido.');
        }

        // 2. REGLA PARA ESTADO CONFIRMADA
        if ($estadoActual === 'confirmada') {
            // Verificar si la cita ya venció por fecha y horario
            $tz = config('app.timezone') ?: 'America/Mexico_City';
            $now = Carbon::now($tz);
            $today = $now->format('Y-m-d');
            $currentTime = $now->format('H:i');
            $fechaStr = $cita->fecha instanceof Carbon ? $cita->fecha->format('Y-m-d') : (string)$cita->fecha;
            $esPasada = ($fechaStr < $today) || ($fechaStr === $today && $cita->hora_fin < $currentTime);

            if ($esPasada && in_array($estadoDestino, ['en_atencion', 'completada'], true)) {
                throw new \DomainException('La fecha y horario de la cita ya han transcurrido sin atención. No puede iniciarse ni completarse como una cita ordinaria.');
            }

            if ($estadoDestino === 'completada') {
                throw new \DomainException('Una cita confirmada no puede pasar directamente a completada sin antes haber iniciado su atención.');
            }

            // Transiciones permitidas desde confirmada: en_atencion, cancelada, vencida
            if (!in_array($estadoDestino, ['en_atencion', 'cancelada', 'vencida'], true)) {
                throw new \DomainException("Transición no permitida de '{$estadoActual}' hacia '{$estadoDestino}'.");
            }

            return;
        }

        // 3. REGLA PARA ESTADO EN ATENCIÓN (EN PROCESO)
        if ($estadoActual === 'en_atencion') {
            if ($estadoDestino === 'confirmada') {
                throw new \DomainException('Una cita que ya ha iniciado atención no puede regresar al estado de confirmada.');
            }

            if ($estadoDestino === 'vencida') {
                throw new \DomainException('Una cita que ya está en proceso de atención no puede marcarse como vencida.');
            }

            // Transiciones permitidas desde en_atencion: completada, cancelada (ej. interrupción justificada)
            if (!in_array($estadoDestino, ['completada', 'cancelada'], true)) {
                throw new \DomainException("Transición no permitida de '{$estadoActual}' hacia '{$estadoDestino}'.");
            }

            return;
        }

        // 4. REGLA PARA ESTADO VENCIDA
        if ($estadoActual === 'vencida') {
            if ($estadoDestino === 'confirmada') {
                throw new \DomainException('Una cita vencida no puede regresar al estado confirmada.');
            }

            if (in_array($estadoDestino, ['en_atencion', 'completada'], true)) {
                throw new \DomainException('Una cita vencida no puede iniciarse ni completarse sin autorización administrativa.');
            }

            // Permitido: cancelada
            if ($estadoDestino !== 'cancelada') {
                throw new \DomainException("Transición no permitida de '{$estadoActual}' hacia '{$estadoDestino}'.");
            }

            return;
        }

        throw new \DomainException("Transición no contemplada de '{$estadoActual}' a '{$estadoDestino}'.");
    }

    /**
     * Actualiza el estado y opcionalmente notas u observaciones de una cita aplicando
     * validación estricta de reglas de transición y bloqueo pesimista contra concurrencia.
     */
    public function actualizarEstado(Cita $cita, string $nuevoEstado, ?string $observaciones = null, ?string $notas = null, ?User $usuario = null, ?string $estadoPago = null): array
    {
        return DB::transaction(function () use ($cita, $nuevoEstado, $observaciones, $notas, $usuario, $estadoPago) {
            // Bloqueo pesimista para evitar concurrencia y transiciones simultáneas incompatibles
            /** @var Cita $citaBloqueada */
            $citaBloqueada = Cita::where('id', $cita->id)->lockForUpdate()->firstOrFail();

            // Validar la transición según la matriz de reglas de negocio centralizada
            $this->validarTransicion($citaBloqueada, $nuevoEstado, $usuario);

            $estadoNormalizado = strtolower(trim($nuevoEstado));
            if ($estadoNormalizado === 'en_proceso') {
                $estadoNormalizado = 'en_atencion';
            }

            // Integración Subtarea 5 y 6: Al iniciar la atención de la cita, si no tenía check-in
            // registrado previamente, se sella automáticamente la llegada presencial del cliente.
            if ($estadoNormalizado === 'en_atencion' && is_null($citaBloqueada->check_in_at)) {
                $tz = config('app.timezone') ?: 'America/Mexico_City';
                $citaBloqueada->check_in_at = Carbon::now($tz);
            }

            $citaBloqueada->estado = $estadoNormalizado;
            if ($observaciones !== null) {
                $citaBloqueada->observaciones = $observaciones;
            }
            if ($notas !== null) {
                $citaBloqueada->notas = $notas;
            }

            // Subtarea 24.2: Actualización de estado_pago al completar o actualizar estado
            if ($estadoPago !== null) {
                $estadoPagoNorm = strtolower(trim($estadoPago));
                if ($estadoNormalizado === 'cancelada' && $estadoPagoNorm === 'pagado') {
                    throw new \DomainException('No se puede marcar como pagada una cita cancelada.');
                }
                if (in_array($estadoPagoNorm, ['pendiente', 'pagado'], true)) {
                    $citaBloqueada->estado_pago = $estadoPagoNorm;
                }
            }

            $citaBloqueada->save();

            $etiquetasEstado = [
                'confirmada' => 'Confirmada',
                'en_atencion' => 'En proceso',
                'completada' => 'Completada',
                'cancelada' => 'Cancelada',
                'vencida' => 'Vencida',
            ];

            $mensaje = 'Estado de la cita actualizado correctamente a ' . ($etiquetasEstado[$estadoNormalizado] ?? ucfirst($estadoNormalizado)) . '.';

            return [
                'success' => true,
                'message' => $mensaje,
                'mensaje' => $mensaje,
                'cita' => $this->obtenerDetalleCita($citaBloqueada),
            ];
        });
    }

    /**
     * Registra el pago/cobro de una cita aplicando bloqueo pesimista contra concurrencia.
     * Criterios Subtarea 24.2:
     * - Citas canceladas no pueden pasar a pagadas (DomainException 422).
     * - Previene pagos duplicados (idempotente si ya está pagada).
     */
    public function registrarPago(Cita $cita, ?User $usuario = null): array
    {
        return DB::transaction(function () use ($cita, $usuario) {
            /** @var Cita $citaBloqueada */
            $citaBloqueada = Cita::where('id', $cita->id)->lockForUpdate()->firstOrFail();

            $estadoActual = strtolower(trim((string)$citaBloqueada->estado));
            if (in_array($estadoActual, ['cancelada', 'cancelled', 'anulada'], true)) {
                throw new \DomainException('No se puede registrar el pago de una cita cancelada.');
            }

            if ($citaBloqueada->estado_pago === 'pagado') {
                return [
                    'success' => true,
                    'already_paid' => true,
                    'message' => 'La cita ya se encuentra registrada como pagada.',
                    'mensaje' => 'La cita ya se encuentra registrada como pagada.',
                    'cita' => $this->obtenerDetalleCita($citaBloqueada),
                ];
            }

            $citaBloqueada->estado_pago = 'pagado';
            $citaBloqueada->save();

            return [
                'success' => true,
                'already_paid' => false,
                'message' => 'Cobro registrado correctamente.',
                'mensaje' => 'Cobro registrado correctamente. La cita ha sido marcada como pagada.',
                'cita' => $this->obtenerDetalleCita($citaBloqueada),
            ];
        });
    }

    /**
     * Consulta el listado de citas aplicando filtros de búsqueda en el servidor
     * (fecha/periodo, estado, especialista, término de búsqueda de cliente/folio),
     * garantizando que las citas canceladas se conserven en el historial.
     */
    public function consultarListadoCitas(array $filtros = [])
    {
        Cita::ensureTableExists();

        $query = Cita::with(['cliente', 'service', 'trabajador.user']);

        // 1. Filtro por Fecha o Rango de Fechas
        if (!empty($filtros['fecha'])) {
            $query->whereDate('fecha', $filtros['fecha']);
        } else {
            if (!empty($filtros['fecha_desde'])) {
                $query->whereDate('fecha', '>=', $filtros['fecha_desde']);
            }
            if (!empty($filtros['fecha_hasta'])) {
                $query->whereDate('fecha', '<=', $filtros['fecha_hasta']);
            }
        }

        // 2. Filtro por Especialista / Trabajador
        if (!empty($filtros['trabajador_id']) && (int) $filtros['trabajador_id'] > 0) {
            $query->where('trabajador_id', (int) $filtros['trabajador_id']);
        }

        // 3. Filtro por Estado
        if (!empty($filtros['estado']) && $filtros['estado'] !== 'todas') {
            $estado = strtolower(trim((string) $filtros['estado']));

            if ($estado === 'vencida') {
                $tz = config('app.timezone') ?: 'America/Mexico_City';
                $now = Carbon::now($tz);
                $today = $now->format('Y-m-d');
                $currentTime = $now->format('H:i');

                $query->where(function ($q) use ($today, $currentTime) {
                    $q->where('estado', 'vencida')
                      ->orWhere(function ($sub) use ($today, $currentTime) {
                          $sub->where('estado', 'confirmada')
                              ->where(function ($dateQ) use ($today, $currentTime) {
                                  $dateQ->whereDate('fecha', '<', $today)
                                        ->orWhere(function ($timeQ) use ($today, $currentTime) {
                                            $timeQ->whereDate('fecha', $today)
                                                  ->where('hora_fin', '<', $currentTime);
                                        });
                              });
                      });
                });
            } elseif ($estado === 'confirmada') {
                $tz = config('app.timezone') ?: 'America/Mexico_City';
                $now = Carbon::now($tz);
                $today = $now->format('Y-m-d');
                $currentTime = $now->format('H:i');

                // Excluir citas expiradas/vencidas: solo citas confirmadas vigentes
                $query->where('estado', 'confirmada')
                      ->where(function ($dateQ) use ($today, $currentTime) {
                          $dateQ->whereDate('fecha', '>', $today)
                                ->orWhere(function ($timeQ) use ($today, $currentTime) {
                                    $timeQ->whereDate('fecha', $today)
                                          ->where('hora_fin', '>=', $currentTime);
                                });
                      });
            } elseif ($estado === 'en_proceso' || $estado === 'en_atencion') {
                $query->whereIn('estado', ['en_proceso', 'en_atencion']);
            } else {
                $query->where('estado', $estado);
            }
        }

        // 4. Búsqueda por Cliente (nombre, apellidos, teléfono, correo) o Folio / ID de cita o Servicio
        if (!empty($filtros['buscar'])) {
            $term = trim($filtros['buscar']);
            $folioLimpio = preg_replace('/[^0-9]/', '', $term);

            $query->where(function ($q) use ($term, $folioLimpio) {
                // Coincidencia por ID numérico / Folio
                if (!empty($folioLimpio)) {
                    $q->where('id', (int) $folioLimpio);
                }

                // Coincidencia por datos del cliente
                $q->orWhereHas('cliente', function ($cq) use ($term) {
                    $cq->where('name', 'like', "%{$term}%")
                       ->orWhere('apellidos', 'like', "%{$term}%")
                       ->orWhere('telefono', 'like', "%{$term}%")
                       ->orWhere('email', 'like', "%{$term}%");
                });

                // Coincidencia por servicio
                $q->orWhereHas('service', function ($sq) use ($term) {
                    $sq->where('name', 'like', "%{$term}%")
                       ->orWhere('category', 'like', "%{$term}%");
                });

                // Coincidencia por especialista
                $q->orWhereHas('trabajador.user', function ($tq) use ($term) {
                    $tq->where('name', 'like', "%{$term}%")
                       ->orWhere('apellidos', 'like', "%{$term}%");
                });
            });
        }

        // Citas ordenadas cronológicamente (más recientes primero)
        return $query->orderBy('fecha', 'desc')
            ->orderBy('hora_inicio', 'desc')
            ->get();
    }

    /**
     * Edita los datos de una cita existente (servicio, especialista, fecha, horario, notas),
     * validando disponibilidad, duración y exclusión de la propia cita de la detección de conflictos.
     */
    public function editarCita(Cita $cita, array $datos, ?User $usuario = null): array
    {
        $estadoActual = strtolower(trim((string)$cita->estado));
        if ($estadoActual === 'completada') {
            // Una cita completada ya fue atendida; no se puede alterar horario, especialista o servicio
            $nuevoServicio = isset($datos['service_id']) && (int)$datos['service_id'] !== (int)$cita->service_id;
            $nuevoEspecialista = (isset($datos['trabajador_id']) || isset($datos['worker_id'])) && (int)($datos['trabajador_id'] ?? $datos['worker_id']) !== (int)$cita->trabajador_id;
            $nuevaFecha = (isset($datos['fecha']) || isset($datos['date'])) && ($datos['fecha'] ?? $datos['date']) !== $cita->fecha;
            $nuevaHora = (isset($datos['hora_inicio']) || isset($datos['time'])) && ($datos['hora_inicio'] ?? $datos['time']) !== $cita->hora_inicio;

            if ($nuevoServicio || $nuevoEspecialista || $nuevaFecha || $nuevaHora) {
                throw new \DomainException("No se puede reprogramar una cita que ya fue completada.");
            }
        }

        // 1. Validar existencia y estado del Servicio
        $serviceId = (int) ($datos['service_id'] ?? $cita->service_id);
        $servicio = Service::find($serviceId);
        if (!$servicio || !$servicio->is_active) {
            throw new \DomainException('El servicio seleccionado no existe o no se encuentra activo.');
        }

        // 2. Validar existencia y estado del Especialista
        $trabajadorId = (int) ($datos['trabajador_id'] ?? ($datos['worker_id'] ?? $cita->trabajador_id));
        $trabajador = Trabajador::with(['user', 'servicios'])->find($trabajadorId);
        if (!$trabajador || !$trabajador->activo) {
            throw new \DomainException('El especialista seleccionado no existe o se encuentra inactivo.');
        }

        // Validar que el especialista ofrezca este servicio (si tiene asignaciones en la BD)
        if (Schema::hasTable('servicio_trabajador') && $trabajador->servicios()->exists()) {
            if (!$trabajador->servicios()->where('services.id', $servicio->id)->exists()) {
                throw new \DomainException("El especialista {$trabajador->nombre_completo} no tiene asignado el servicio {$servicio->name}.");
            }
        }

        // 3. Resolver fecha y horarios
        $fecha = trim((string)($datos['fecha'] ?? ($datos['date'] ?? $cita->fecha)));
        $tz = config('app.timezone') ?: 'America/Mexico_City';
        $today = Carbon::now($tz)->format('Y-m-d');
        if ($fecha < $today) {
            throw new \DomainException('No se puede reprogramar una cita para una fecha anterior al día de hoy.');
        }

        // Validar que la fecha sea un día laborable de la sucursal
        $checkLaborable = HorarioSucursal::esFechaLaborable($fecha);
        if (!$checkLaborable['laborable']) {
            throw new \DomainException($checkLaborable['mensaje']);
        }

        $horaInicio = trim((string)($datos['hora_inicio'] ?? ($datos['time'] ?? $cita->hora_inicio)));

        $disponibilidadService = app(DisponibilidadService::class);
        $horaInicioNorm = $disponibilidadService->normalizarHora($horaInicio);
        if (!$horaInicioNorm) {
            throw new \DomainException('El horario de inicio no tiene un formato válido (HH:MM).');
        }

        $duracion = (int) ($servicio->duration_minutes ?: ($cita->duracion_minutos ?: 30));
        $horaInicioMin = $disponibilidadService->horaAMinutos($horaInicioNorm);
        $horaFinMin = $horaInicioMin + $duracion;
        $horaFinNorm = $disponibilidadService->minutosAHora($horaFinMin);

        // 4. Validar disponibilidad del especialista en la fecha y horario, excluyendo la cita actual
        $val = $disponibilidadService->validarHorarioSeleccionado(
            trabajador: $trabajador,
            fecha: $fecha,
            hora: $horaInicioNorm,
            duracionMinutos: $duracion,
            servicio: $servicio,
            excludeCitaId: $cita->id
        );

        if (!$val['disponible']) {
            throw new \DomainException($val['mensaje'] ?: 'El horario seleccionado no está disponible para el especialista.');
        }

        // 5. Validar conflicto de horario para el cliente, excluyendo la cita actual
        $clienteId = $cita->user_id;
        $citaConflictoCliente = $disponibilidadService->obtenerCitaConflictoCliente(
            cliente: $clienteId,
            fecha: $fecha,
            horaInicioMin: $horaInicioMin,
            horaFinMin: $horaFinMin,
            excluirCitaId: $cita->id,
            conBloqueo: false
        );

        if ($citaConflictoCliente) {
            $nomSrv = $citaConflictoCliente->service?->name ?? 'otro servicio';
            throw new \DomainException("El cliente ya cuenta con otra cita agendada en ese horario ({$nomSrv} de {$citaConflictoCliente->hora_inicio} a {$citaConflictoCliente->hora_fin} hrs).");
        }

        // 6. Actualización atómica en la base de datos
        return DB::transaction(function () use ($cita, $servicio, $trabajador, $fecha, $horaInicioNorm, $horaFinNorm, $duracion, $datos) {
            /** @var Cita $citaBloqueada */
            $citaBloqueada = Cita::where('id', $cita->id)->lockForUpdate()->firstOrFail();

            $citaBloqueada->service_id = $servicio->id;
            $citaBloqueada->trabajador_id = $trabajador->id;
            $citaBloqueada->fecha = $fecha;
            $citaBloqueada->hora_inicio = $horaInicioNorm;
            $citaBloqueada->hora_fin = $horaFinNorm;
            $citaBloqueada->duracion_minutos = $duracion;
            $citaBloqueada->precio = $servicio->price ?? $citaBloqueada->precio;

            if (array_key_exists('notas', $datos)) {
                $citaBloqueada->notas = $datos['notas'];
            }
            if (array_key_exists('observaciones', $datos)) {
                $citaBloqueada->observaciones = $datos['observaciones'];
            }

            // Si una cita cancelada o vencida es reprogramada a una nueva fecha/hora válida, se reactiva a confirmada
            if (in_array(strtolower(trim((string)$citaBloqueada->estado)), ['cancelada', 'vencida'], true)) {
                $citaBloqueada->estado = 'confirmada';
            }

            $citaBloqueada->save();

            return [
                'success' => true,
                'mensaje' => "La cita {$citaBloqueada->folio} fue modificada exitosamente.",
                'cita' => $this->obtenerDetalleCita($citaBloqueada),
            ];
        });
    }

    /**
     * Confirma una cita verificando sus reglas de estado y condiciones de vigencia.
     */
    public function confirmarCita(Cita $cita, ?User $usuario = null): array
    {
        $estadoActual = strtolower(trim((string)$cita->estado));

        if ($estadoActual === 'confirmada') {
            return [
                'success' => true,
                'mensaje' => "La cita {$cita->folio} ya se encuentra confirmada.",
                'cita' => $this->obtenerDetalleCita($cita),
            ];
        }

        if (in_array($estadoActual, ['cancelada', 'completada'], true)) {
            throw new \DomainException("No se puede confirmar una cita que se encuentra {$cita->estado_label}.");
        }

        if ($cita->estado_visual === 'vencida') {
            throw new \DomainException("La cita está vencida y no puede ser confirmada como cita ordinaria.");
        }

        return $this->actualizarEstado($cita, 'confirmada', null, null, $usuario);
    }

    /**
     * Cancela una cita aplicando las políticas del sistema y liberando la disponibilidad.
     */
    public function cancelarCita(Cita $cita, ?string $motivo = null, ?User $usuario = null): array
    {
        $estadoActual = strtolower(trim((string)$cita->estado));

        if ($estadoActual === 'cancelada') {
            return [
                'success' => true,
                'mensaje' => "La cita {$cita->folio} ya se encontraba cancelada en el historial.",
                'cita' => $this->obtenerDetalleCita($cita),
            ];
        }

        if ($estadoActual === 'completada') {
            throw new \DomainException("No se puede cancelar una cita que ya fue completada.");
        }

        // Restricción: No se permite cancelar citas correspondientes a fechas o momentos pasados
        if ($cita->esPasada()) {
            throw new \DomainException("No se puede cancelar una cita cuya fecha u hora ya ha transcurrido.");
        }

        // Si el usuario es un cliente (rol 4), aplicar restricción de 30 minutos
        if ($usuario && (int)$usuario->role_id === 4) {
            if (!$cita->puedeCancelar()) {
                throw new \DomainException($cita->motivo_no_cancelable ?: 'La cita no cumple con las condiciones para ser cancelada por el cliente.');
            }
        }

        $observaciones = $motivo ? (trim(($cita->observaciones ? $cita->observaciones . " | " : "") . "Cancelación: {$motivo}")) : $cita->observaciones;

        return $this->actualizarEstado($cita, 'cancelada', $observaciones, null, $usuario);
    }

    /**
     * Registra el check-in (llegada del cliente al establecimiento) para una cita elegible,
     * verificando permisos de recepcionista/administrador y aplicando bloqueo pesimista contra concurrencia.
     */
    public function registrarCheckIn(Cita $cita, ?User $usuario = null): array
    {
        // 1. Validar autorización del usuario
        if ($usuario) {
            $roleId = (int) ($usuario->role_id ?? 0);
            if (!in_array($roleId, [1, 2], true)) {
                throw new \Illuminate\Auth\Access\AuthorizationException('No tienes autorización para registrar la llegada de clientes.');
            }
        }

        return DB::transaction(function () use ($cita) {
            /** @var Cita $citaBloqueada */
            $citaBloqueada = Cita::where('id', $cita->id)->lockForUpdate()->firstOrFail();

            // 2. Verificar que no se duplique el check-in
            if ($citaBloqueada->isCheckedIn()) {
                $horaLlegada = $citaBloqueada->check_in_hora ?? 'la hora indicada';
                throw new \DomainException("La llegada del cliente ya fue registrada previamente para esta cita ({$horaLlegada} hrs).");
            }

            // 3. Validar estado de la cita
            $estado = strtolower(trim((string) $citaBloqueada->estado));
            if ($estado === 'cancelada') {
                throw new \DomainException('No se puede registrar la llegada de una cita cancelada.');
            }

            if ($estado === 'completada') {
                throw new \DomainException('No se puede registrar la llegada de una cita que ya fue completada.');
            }

            if ($citaBloqueada->estado_visual === 'vencida') {
                throw new \DomainException('La cita se encuentra vencida. Debe ser reagendada a un horario válido antes de registrar la llegada.');
            }

            // 4. Registrar fecha y hora exacta del check-in
            $tz = config('app.timezone') ?: 'America/Mexico_City';
            $now = Carbon::now($tz);
            $citaBloqueada->check_in_at = $now;
            $citaBloqueada->save();

            $mensaje = "Llegada del cliente {$clienteNombre} registrada exitosamente a las {$now->format('H:i')} hrs ({$citaBloqueada->folio}).";

            return [
                'success' => true,
                'message' => $mensaje,
                'mensaje' => $mensaje,
                'checked_in' => true,
                'check_in_at' => $now->format('Y-m-d H:i:s'),
                'check_in_hora' => $now->format('H:i'),
                'check_in_formateado' => $citaBloqueada->check_in_formateado,
                'cita' => $this->obtenerDetalleCita($citaBloqueada),
            ];
        });
    }
}
