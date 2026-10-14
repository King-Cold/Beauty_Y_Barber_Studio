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
    public function agendamiento(Request $request)
    {
        $user = Auth::user();

        // Cliente preseleccionado si viene especificado por parámetro en la URL
        $clientePreseleccionado = null;
        if ($request->filled('client_id')) {
            $clientePreseleccionado = User::where('role_id', 4)->find($request->client_id);
        }

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
            'clientePreseleccionado',
            'servicios',
            'serviciosEstetica',
            'serviciosBarberia',
            'trabajadores'
        ));
    }

    /**
     * Búsqueda dinámica de clientes mediante AJAX para el selector interactivo de agendamiento.
     */
    public function buscarClientes(Request $request)
    {
        $term = trim($request->input('q', ''));

        $query = User::where('role_id', 4);

        if ($term !== '') {
            $words = array_filter(explode(' ', $term));
            $query->where(function ($q) use ($term, $words) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('apellidos', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%")
                  ->orWhere('telefono', 'like', "%{$term}%");

                if (count($words) > 1) {
                    $q->orWhere(function ($sub) use ($words) {
                        foreach ($words as $w) {
                            $sub->where(function ($wordQ) use ($w) {
                                $wordQ->where('name', 'like', "%{$w}%")
                                      ->orWhere('apellidos', 'like', "%{$w}%");
                            });
                        }
                    });
                }
            });
        }

        $clientes = $query->select('id', 'name', 'apellidos', 'email', 'telefono')
            ->orderBy('name', 'asc')
            ->take(15)
            ->get();

        return response()->json([
            'success' => true,
            'total' => $clientes->count(),
            'clientes' => $clientes->map(fn($c) => [
                'id' => $c->id,
                'nombre_completo' => trim("{$c->name} " . ($c->apellidos ?? '')),
                'name' => $c->name,
                'apellidos' => $c->apellidos ?? '',
                'email' => $c->email,
                'telefono' => $c->telefono ?? '',
                'iniciales' => strtoupper(substr($c->name, 0, 1) . substr($c->apellidos ?? '', 0, 1)),
            ]),
        ]);
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

        $disponibilidadService = app(DisponibilidadService::class);
        $resultado = $disponibilidadService
            ->validarHorarioSeleccionado($trabajador, $validated['date'], $validated['time'], $servicio);

        if (!$resultado['disponible']) {
            return response()->json([
                'success' => false,
                'valido' => false,
                'motivo_tipo' => $resultado['motivo_tipo'] ?? 'no_disponible',
                'mensaje' => $resultado['mensaje'] ?: 'El horario seleccionado ya no está disponible. Por favor selecciona otro horario.',
            ], 422);
        }

        // Validación de conflicto para el cliente si se proporciona client_id
        if ($request->filled('client_id')) {
            $cliente = User::find($request->client_id);
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
                    $clienteNom = trim($cliente->name . ' ' . ($cliente->apellidos ?? ''));
                    $servicioNom = $citaConflicto->service?->name ?? 'otro servicio';
                    $trabajadorNom = $citaConflicto->trabajador?->nombre_completo ?? 'un especialista';
                    $finIntervalo = $citaConflicto->hora_fin ?: $disponibilidadService->minutosAHora($disponibilidadService->horaAMinutos($citaConflicto->hora_inicio) + ($citaConflicto->duracion_minutos ?: 30));

                    return response()->json([
                        'success' => false,
                        'valido' => false,
                        'mensaje' => "El cliente {$clienteNom} ya cuenta con una cita en ese horario ({$servicioNom} con {$trabajadorNom} de {$citaConflicto->hora_inicio} a {$finIntervalo} hrs). No es posible agendar citas traslapadas para el mismo cliente.",
                        'motivo_tipo' => 'conflicto_cliente',
                        'cita_conflicto' => $citaConflicto,
                    ], 422);
                }
            }
        }

        return response()->json([
            'success' => true,
            'valido' => true,
            'mensaje' => 'Horario verificado y disponible.',
        ]);
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

        $resultado = $disponibilidadService->registrarCitaConcurrente(
            trabajador: $trabajador,
            servicio: $servicio,
            cliente: $cliente,
            fecha: $validated['date'],
            hora: $validated['time'],
            notas: $validated['notas'] ?? null,
            origen: 'recepcion'
        );

        $status = $resultado['status'] ?? ($resultado['success'] ? 200 : 422);
        unset($resultado['status']);

        return response()->json($resultado, $status);
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
