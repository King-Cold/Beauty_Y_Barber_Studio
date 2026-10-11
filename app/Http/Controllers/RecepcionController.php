<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Service;
use App\Models\Trabajador;
use App\Models\User;
use App\Services\DisponibilidadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RecepcionController extends Controller
{
    /**
     * Página principal (Dashboard) del panel de la recepcionista.
     */
    public function index()
    {
        $user = Auth::user();
        return view('recepcion.dashboard', compact('user'));
    }

    /**
     * Módulo de gestión de citas: Muestra la vista principal o redirige al agendamiento.
     */
    public function citas()
    {
        $user = Auth::user();

        // Obtener citas recientes para consulta informativa
        Cita::ensureTableExists();
        $citas = Cita::with(['cliente', 'service', 'trabajador.user'])
            ->orderBy('fecha', 'desc')
            ->orderBy('hora_inicio', 'desc')
            ->take(20)
            ->get();

        return view('recepcion.citas', compact('user', 'citas'));
    }

    /**
     * Formulario interactivo de agendamiento de citas desde la recepción.
     */
    public function agendamiento()
    {
        $user = Auth::user();

        // Clientes registrados en el sistema (rol 4)
        $clientes = User::where('role_id', 4)
            ->orderBy('name', 'asc')
            ->get();

        // Servicios activos
        $servicios = Schema::hasTable('services')
            ? Service::where('is_active', true)->with('trabajadoresActivos')->get()
            : collect();

        $serviciosEstetica = $servicios->filter(fn($s) => strtolower($s->category ?? '') === 'estetica');
        $serviciosBarberia = $servicios->filter(fn($s) => strtolower($s->category ?? '') === 'barberia');

        // Trabajadores activos
        $trabajadores = Schema::hasTable('trabajadores')
            ? Trabajador::where('activo', true)->with(['user', 'serviciosActivos'])->get()
            : collect();

        return view('recepcion.agendamiento', compact(
            'user',
            'clientes',
            'servicios',
            'serviciosEstetica',
            'serviciosBarberia',
            'trabajadores'
        ));
    }

    /**
     * Consulta disponibilidad de horarios para recepcionista reutilizando DisponibilidadService.
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

        $servicio = Service::findOrFail($validated['service_id']);
        $trabajador = Trabajador::findOrFail($validated['worker_id']);

        $disponibilidad = app(DisponibilidadService::class)
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
     * Valida en el servidor si un horario específico sigue estando disponible antes de confirmar.
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
            'worker_id.required' => 'Debe seleccionar un especialista.',
            'date.required' => 'Debe seleccionar una fecha de cita.',
            'time.required' => 'Debe seleccionar un horario de cita.',
        ]);

        $servicio = Service::findOrFail($validated['service_id']);
        $trabajador = Trabajador::findOrFail($validated['worker_id']);

        $resultado = app(DisponibilidadService::class)
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
            'mensaje' => $resultado['mensaje'] ?: 'El horario seleccionado ya no está disponible. Por favor selecciona otro horario.',
        ], 422);
    }

    /**
     * Registra y confirma la cita del cliente desde la recepción, asociándola al cliente seleccionado.
     */
    public function confirmarCita(Request $request)
    {
        Cita::ensureTableExists();

        $validated = $request->validate([
            'client_id' => 'required|integer|exists:users,id',
            'service_id' => 'required|integer|exists:services,id',
            'worker_id' => 'required|integer|exists:trabajadores,id',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|string',
            'notas' => 'nullable|string|max:500',
        ], [
            'client_id.required' => 'Debe seleccionar un cliente registrado.',
            'client_id.exists' => 'El cliente seleccionado no existe en el sistema.',
            'service_id.required' => 'Debe seleccionar un servicio del catálogo.',
            'service_id.exists' => 'El servicio seleccionado no existe.',
            'worker_id.required' => 'Debe seleccionar un especialista.',
            'worker_id.exists' => 'El especialista seleccionado no existe.',
            'date.required' => 'Debe seleccionar una fecha para la cita.',
            'date.after_or_equal' => 'La fecha no puede ser anterior al día de hoy.',
            'time.required' => 'Debe seleccionar un horario de atención disponible.',
        ]);

        // Verificación estricta de seguridad: el cliente debe tener rol de cliente (role_id 4)
        $cliente = User::findOrFail($validated['client_id']);
        if ((int) $cliente->role_id !== 4 && (int) $cliente->role_id !== 1) {
            return response()->json([
                'success' => false,
                'valido' => false,
                'mensaje' => 'El usuario seleccionado no es un cliente válido.',
            ], 422);
        }

        $servicio = Service::findOrFail($validated['service_id']);
        $trabajador = Trabajador::findOrFail($validated['worker_id']);
        $disponibilidadService = app(DisponibilidadService::class);

        // Duración y precio tomados directamente de la base de datos (seguridad contra manipulación)
        $duracionMinutos = (int) ($servicio->duration_minutes ?: 30);
        $precio = (float) $servicio->price;

        $horaInicio = $disponibilidadService->normalizarHora($validated['time']);
        $horaInicioMin = $disponibilidadService->horaAMinutos($horaInicio);
        $horaFinMin = $horaInicioMin + $duracionMinutos;
        $horaFin = $disponibilidadService->minutosAHora($horaFinMin);

        // 1. Idempotencia y prevención de duplicados exactos
        $citaDuplicada = Cita::where('user_id', $cliente->id)
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
                'mensaje' => 'Esta cita ya había sido registrada exitosamente para este cliente.',
                'cita' => $citaDuplicada->load(['service', 'trabajador.user', 'cliente']),
            ]);
        }

        // 2. Re-validación estricta de disponibilidad en el servidor
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

        // 3. Persistencia atómica protegida contra traslapes simultáneos
        DB::beginTransaction();
        try {
            // Comprobación con lock de citas activas del trabajador
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

            // Registro real: ASOCIADO AL CLIENTE SELECCIONADO, no a la recepcionista
            $cita = Cita::create([
                'user_id' => $cliente->id,
                'trabajador_id' => $trabajador->id,
                'service_id' => $servicio->id,
                'fecha' => $validated['date'],
                'hora_inicio' => $horaInicio,
                'hora_fin' => $horaFin,
                'duracion_minutos' => $duracionMinutos,
                'precio' => $precio,
                'estado' => 'confirmada', // Estado inicial establecido en el proyecto
                'notas' => $validated['notas'] ?? null,
            ]);

            DB::commit();

            $fechaFormateada = \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y');
            $clienteNombre = $cliente->name . ' ' . ($cliente->apellidos ?? '');

            return response()->json([
                'success' => true,
                'valido' => true,
                'mensaje' => "Cita agendada exitosamente para {$clienteNombre} ({$servicio->name} el {$fechaFormateada} a las {$horaInicio} hrs).",
                'cita' => $cita->load(['service', 'trabajador.user', 'cliente']),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'valido' => false,
                'mensaje' => 'Ocurrió un error al guardar la cita en la base de datos: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Vista de Calendario de recepción.
     */
    public function calendario()
    {
        $user = Auth::user();
        $title = 'Calendario de Citas';
        $description = 'Vista general de la agenda y programación de especialistas del estudio.';
        return view('recepcion.placeholder', compact('user', 'title', 'description'));
    }

    /**
     * Vista de Clientes desde recepción.
     */
    public function clientes()
    {
        $user = Auth::user();

        // Consulta de clientes reales existentes en el sistema (rol 4)
        $clientes = User::where('role_id', 4)
            ->withCount('citas')
            ->orderBy('name', 'asc')
            ->get();

        return view('recepcion.clientes', compact('user', 'clientes'));
    }
}
