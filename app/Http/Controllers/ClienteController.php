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
            'horarios_bloqueados' => $disponibilidad['horarios_bloqueados'] ?? [],
            'bloqueos' => $disponibilidad['bloqueos_detalle'] ?? [],
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

        $disponibilidadService = app(\App\Services\DisponibilidadService::class);
        $resultado = $disponibilidadService
            ->validarHorarioSeleccionado($trabajador, $validated['date'], $validated['time'], $servicio);

        if (!$resultado['disponible']) {
            return response()->json([
                'success' => false,
                'valido' => false,
                'motivo_tipo' => $resultado['motivo_tipo'] ?? 'no_disponible',
                'mensaje' => $resultado['mensaje'] ?: 'El horario seleccionado ya no está disponible. Por favor elige otro horario.',
            ], 422);
        }

        // Validación de conflicto de horario para el cliente autenticado
        $cliente = Auth::user();
        if ($cliente) {
            $horaInicio = $disponibilidadService->normalizarHora($validated['time']);
            $duracion = (int) ($servicio->duration_minutes ?: 30);
            $horaInicioMin = $disponibilidadService->horaAMinutos($horaInicio);
            $horaFinMin = $horaInicioMin + $duracion;

            $citaConflicto = $disponibilidadService->obtenerCitaConflictoCliente(
                cliente: $cliente,
                fecha: $validated['date'],
                horaInicioMin: $horaInicioMin,
                horaFinMin: $horaFinMin
            );

            if ($citaConflicto) {
                $servicioNom = $citaConflicto->service?->name ?? 'otro servicio';
                $finIntervalo = $citaConflicto->hora_fin ?: $disponibilidadService->minutosAHora($disponibilidadService->horaAMinutos($citaConflicto->hora_inicio) + ($citaConflicto->duracion_minutos ?: 30));

                return response()->json([
                    'success' => false,
                    'valido' => false,
                    'mensaje' => "Ya cuentas con una cita reservada para ese horario ({$servicioNom} de {$citaConflicto->hora_inicio} a {$finIntervalo} hrs). Por favor selecciona otro horario.",
                    'motivo_tipo' => 'conflicto_cliente',
                    'cita_conflicto' => $citaConflicto,
                ], 422);
            }
        }

        return response()->json([
            'success' => true,
            'valido' => true,
            'mensaje' => 'Horario verificado y disponible.',
        ]);
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
        $cliente = Auth::user();

        $disponibilidadService = app(DisponibilidadService::class);

        $resultado = $disponibilidadService->registrarCitaConcurrente(
            trabajador: $trabajador,
            servicio: $servicio,
            cliente: $cliente,
            fecha: $validated['date'],
            hora: $validated['time'],
            notas: $validated['notas'] ?? null,
            origen: 'cliente'
        );

        $status = $resultado['status'] ?? ($resultado['success'] ? 200 : 422);
        unset($resultado['status']);

        return response()->json($resultado, $status);
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
