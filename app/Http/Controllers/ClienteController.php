<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Service;
use App\Models\Trabajador;
use App\Services\DisponibilidadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClienteController extends Controller
{
    /**
     * Muestra la interfaz principal del cliente (Inicio).
     */
    public function index()
    {
        $user = Auth::user();

        // Control de acceso según rol: Si no es cliente ni administrador, redirigir a su panel correspondiente
        if ($user->role_id !== 4 && $user->role_id !== 1) {
            return $this->redirectByRole($user->role_id);
        }

        return view('cliente.dashboard', compact('user'));
    }

    /**
     * Muestra la sección de agendamiento de citas.
     */
    public function agendamiento()
    {
        $user = Auth::user();

        if ($user->role_id !== 4 && $user->role_id !== 1) {
            return $this->redirectByRole($user->role_id);
        }

        $servicios = \Illuminate\Support\Facades\Schema::hasTable('services') 
            ? \App\Models\Service::where('is_active', true)->with('trabajadoresActivos')->get() 
            : collect();

        $serviciosEstetica = $servicios->filter(fn($s) => strtolower($s->category ?? '') === 'estetica');
        $serviciosBarberia = $servicios->filter(fn($s) => strtolower($s->category ?? '') === 'barberia');

        $trabajadores = \Illuminate\Support\Facades\Schema::hasTable('trabajadores') 
            ? \App\Models\Trabajador::where('activo', true)->with(['user', 'serviciosActivos'])->get() 
            : collect();

        return view('cliente.agendamiento', compact('user', 'servicios', 'serviciosEstetica', 'serviciosBarberia', 'trabajadores'));
    }

    /**
     * Muestra la sección de historial de citas del cliente autenticado.
     */
    public function historial(Request $request)
    {
        $user = Auth::user();

        if ($user->role_id !== 4 && $user->role_id !== 1) {
            return $this->redirectByRole($user->role_id);
        }

        Cita::ensureTableExists();

        $citas = ($user && $user->id)
            ? Cita::where('user_id', $user->id)
                ->with(['service', 'trabajador.user'])
                ->orderBy('fecha', 'desc')
                ->orderBy('hora_inicio', 'desc')
                ->get()
            : collect();

        $citasProximas = $citas->filter(fn($c) => $c->esProxima())->values();
        $citasPasadas = $citas->reject(fn($c) => $c->esProxima())->values();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'total' => $citas->count(),
                'proximas_count' => $citasProximas->count(),
                'pasadas_count' => $citasPasadas->count(),
                'citas' => $citas,
                'proximas' => $citasProximas,
                'pasadas' => $citasPasadas,
            ]);
        }

        return view('cliente.historial', compact('user', 'citas', 'citasProximas', 'citasPasadas'));
    }

    /**
     * Consulta individual de una cita garantizando que pertenezca al cliente autenticado.
     */
    public function showCita(Request $request, Cita $cita)
    {
        $user = Auth::user();

        // 1. Autorización estricta: Cada cliente solo puede consultar sus propias citas
        if ((int) $cita->user_id !== (int) $user->id && $user->role_id !== 1) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'mensaje' => 'No tienes autorización para consultar esta cita.',
                ], 403);
            }
            abort(403, 'No tienes autorización para consultar esta cita.');
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'cita' => $cita->load(['service', 'trabajador.user']),
            ]);
        }

        return redirect()->route('cliente.historial');
    }

    /**
     * Cancela una cita del cliente autenticado si cumple con la política de más de 30 min de anticipación.
     */
    public function cancelarCita(Request $request, Cita $cita)
    {
        $user = Auth::user();

        // 1. Autorización estricta: Cada cliente solo puede cancelar sus propias citas
        if ((int) $cita->user_id !== (int) $user->id && $user->role_id !== 1) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'mensaje' => 'No tienes autorización para cancelar esta cita.',
                ], 403);
            }
            abort(403, 'No tienes autorización para cancelar esta cita.');
        }

        // 2. Validación de política de tiempo y estado en el servidor
        if (!$cita->puedeCancelar()) {
            $motivo = $cita->motivo_no_cancelable ?: 'La cita no cumple con las condiciones para ser cancelada.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'mensaje' => $motivo,
                    'motivo' => $motivo,
                    'minutos_restantes' => $cita->minutos_para_inicio,
                ], 422);
            }
            return redirect()->route('cliente.historial')->with('error', $motivo);
        }

        // 3. Persistencia segura del cambio de estado en la base de datos (sin borrar el registro)
        DB::beginTransaction();
        try {
            $cita->estado = 'cancelada';
            $cita->save();
            DB::commit();

            $mensajeExito = "Tu cita para {$cita->service->name} ha sido cancelada exitosamente.";
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'mensaje' => $mensajeExito,
                    'cita' => $cita->fresh(['service', 'trabajador.user']),
                ]);
            }

            return redirect()->route('cliente.historial')->with('success', $mensajeExito);
        } catch (\Throwable $e) {
            DB::rollBack();
            $mensajeError = 'Ocurrió un error al actualizar el estado de la cita en la base de datos: ' . $e->getMessage();
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'mensaje' => $mensajeError,
                ], 500);
            }
            return redirect()->route('cliente.historial')->with('error', $mensajeError);
        }
    }

    /**
     * Consulta la disponibilidad de horarios en el servidor basándose en los datos seleccionados.
     */
    public function consultarDisponibilidad(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'worker_id' => 'required|exists:trabajadores,id',
            'date' => 'required|date|after_or_equal:today',
        ], [
            'service_id.required' => 'Debe seleccionar un servicio válido.',
            'service_id.exists' => 'El servicio seleccionado no existe.',
            'worker_id.required' => 'Debe seleccionar un trabajador especialista.',
            'worker_id.exists' => 'El especialista seleccionado no existe.',
            'date.required' => 'Debe seleccionar una fecha de cita.',
            'date.date' => 'La fecha proporcionada no es válida.',
            'date.after_or_equal' => 'La fecha no puede ser anterior al día de hoy.',
        ]);

        $servicio = \App\Models\Service::findOrFail($validated['service_id']);
        $trabajador = \App\Models\Trabajador::findOrFail($validated['worker_id']);

        $disponibilidad = app(\App\Services\DisponibilidadService::class)
            ->obtenerDisponibilidadCompleta($trabajador, $validated['date'], $servicio);

        $horarios = $disponibilidad['horarios_disponibles'] ?? [];

        return response()->json([
            'success' => true,
            'disponible' => count($horarios) > 0,
            'horarios' => $horarios,
            'mensaje' => $disponibilidad['mensaje'] ?? '',
            'aviso_cierre' => $disponibilidad['aviso_cierre'] ?? null,
            'hora_cierre_efectiva' => $disponibilidad['hora_cierre_efectiva'] ?? null,
            'ultimo_horario' => $disponibilidad['ultimo_horario'] ?? null,
            'duracion_minutos' => $servicio->duration_minutes,
            'fecha' => $validated['date'],
            'trabajador_nombre' => $trabajador->nombre_completo,
            'servicio_nombre' => $servicio->name,
        ]);
    }

    /**
     * Valida en el servidor si un horario específico sigue estando disponible antes de continuar.
     */
    public function validarHorario(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'worker_id' => 'required|exists:trabajadores,id',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|string',
        ], [
            'service_id.required' => 'Debe seleccionar un servicio válido.',
            'worker_id.required' => 'Debe seleccionar un trabajador especialista.',
            'date.required' => 'Debe seleccionar una fecha de cita.',
            'time.required' => 'Debe seleccionar un horario de cita.',
        ]);

        $servicio = \App\Models\Service::findOrFail($validated['service_id']);
        $trabajador = \App\Models\Trabajador::findOrFail($validated['worker_id']);

        $resultado = app(\App\Services\DisponibilidadService::class)
            ->validarHorarioSeleccionado($trabajador, $validated['date'], $validated['time'], $servicio);

        if ($resultado['disponible']) {
            return response()->json([
                'success' => true,
                'valido' => true,
                'mensaje' => 'Horario verificado y disponible.',
            ]);
        }

        return response()->json([
            'success' => false,
            'valido' => false,
            'mensaje' => $resultado['mensaje'] ?: 'El horario seleccionado ya no está disponible. Por favor elige otro horario.',
        ], 422);
    }

    /**
     * Registra y confirma la cita del cliente en la base de datos tras re-validar disponibilidad.
     */
    public function confirmarCita(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|integer|exists:services,id',
            'worker_id' => 'required|integer|exists:trabajadores,id',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|string',
            'notas' => 'nullable|string|max:500',
        ], [
            'service_id.required' => 'Debe seleccionar un servicio del catálogo.',
            'service_id.exists' => 'El servicio seleccionado no existe.',
            'worker_id.required' => 'Debe seleccionar un trabajador especialista.',
            'worker_id.exists' => 'El especialista seleccionado no existe.',
            'date.required' => 'Debe seleccionar una fecha para su cita.',
            'date.after_or_equal' => 'La fecha no puede ser anterior al día de hoy.',
            'time.required' => 'Debe seleccionar un horario de atención disponible.',
        ]);

        $servicio = Service::findOrFail($validated['service_id']);
        $trabajador = Trabajador::findOrFail($validated['worker_id']);
        $disponibilidadService = app(DisponibilidadService::class);

        $duracionMinutos = (int) ($servicio->duration_minutes ?: 30);
        $precio = (float) $servicio->price;

        $horaInicio = $disponibilidadService->normalizarHora($validated['time']);
        $horaInicioMin = $disponibilidadService->horaAMinutos($horaInicio);
        $horaFinMin = $horaInicioMin + $duracionMinutos;
        $horaFin = $disponibilidadService->minutosAHora($horaFinMin);

        // 1. Prevención idempotente de duplicados por reenvío o doble clic del mismo cliente
        $citaDuplicada = Cita::where('user_id', Auth::id())
            ->where('trabajador_id', $trabajador->id)
            ->where('service_id', $servicio->id)
            ->whereDate('fecha', $validated['date'])
            ->where('hora_inicio', $horaInicio)
            ->whereNotIn('estado', ['cancelada', 'cancelled', 'anulada'])
            ->first();

        if ($citaDuplicada) {
            return response()->json([
                'success' => true,
                'valido' => true,
                'ya_existia' => true,
                'mensaje' => 'Esta cita ya había sido registrada exitosamente.',
                'cita' => $citaDuplicada->load(['service', 'trabajador.user']),
            ]);
        }

        // 2. Re-validación estricta de disponibilidad del horario en el servidor
        $resultado = $disponibilidadService->validarHorarioSeleccionado(
            $trabajador,
            $validated['date'],
            $validated['time'],
            $servicio
        );

        if (!$resultado['disponible']) {
            return response()->json([
                'success' => false,
                'valido' => false,
                'mensaje' => $resultado['mensaje'] ?: 'El horario seleccionado ya no está disponible. Por favor selecciona otro horario.',
                'motivo_tipo' => $resultado['motivo_tipo'] ?? 'no_disponible',
            ], 422);
        }

        // 3. Persistencia atómica protegida contra citas simultáneas y duplicadas
        DB::beginTransaction();
        try {
            // Protección contra colisión simultánea de citas para el mismo especialista
            $citasExistentes = Cita::where('trabajador_id', $trabajador->id)
                ->whereDate('fecha', $validated['date'])
                ->whereNotIn('estado', ['cancelada', 'cancelled', 'anulada'])
                ->lockForUpdate()
                ->get();

            foreach ($citasExistentes as $citaPrevia) {
                $previaIni = $disponibilidadService->horaAMinutos($citaPrevia->hora_inicio);
                $previaFin = $disponibilidadService->horaAMinutos($citaPrevia->hora_fin);

                if ($disponibilidadService->solapanIntervalos($horaInicioMin, $horaFinMin, $previaIni, $previaFin)) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'valido' => false,
                        'mensaje' => "El horario seleccionado ya no está disponible debido a otra cita reservada en el mismo intervalo ({$citaPrevia->hora_inicio} a {$citaPrevia->hora_fin} hrs).",
                        'motivo_tipo' => 'cita_simultanea',
                    ], 422);
                }
            }

            // Registro real en la base de datos
            $cita = Cita::create([
                'user_id' => Auth::id(),
                'trabajador_id' => $trabajador->id,
                'service_id' => $servicio->id,
                'fecha' => $validated['date'],
                'hora_inicio' => $horaInicio,
                'hora_fin' => $horaFin,
                'duracion_minutos' => $duracionMinutos,
                'precio' => $precio,
                'estado' => 'confirmada',
                'notas' => $validated['notas'] ?? null,
            ]);

            DB::commit();

            $fechaFormateada = \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y');

            return response()->json([
                'success' => true,
                'valido' => true,
                'mensaje' => "¡Cita confirmada exitosamente! Tu cita para {$servicio->name} con {$trabajador->nombre_completo} ha sido registrada para el {$fechaFormateada} a las {$cita->hora_inicio} hrs.",
                'cita' => $cita->load(['service', 'trabajador.user']),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'valido' => false,
                'mensaje' => 'No se pudo guardar la cita en la base de datos: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Redirige al usuario según su rol en caso de intentar acceder a un módulo fuera de su perfil.
     */
    private function redirectByRole(int $roleId)
    {
        return match ($roleId) {
            1 => redirect()->route('admin.dashboard'),
            2 => redirect()->route('recepcion.dashboard'),
            3 => redirect()->route('trabajador.dashboard'),
            default => redirect()->route('login'),
        };
    }
}
