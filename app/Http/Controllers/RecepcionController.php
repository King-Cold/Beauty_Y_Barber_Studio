<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Service;
use App\Models\Trabajador;
use App\Models\User;
use App\Services\DisponibilidadService;
use App\Services\CitaCalendarioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RecepcionController extends Controller
{
    /**
     * Página principal (Dashboard) del panel de la recepcionista.
     * Integra la captura de datos del cliente, turnos inmediatos y agendamiento interactivo.
     */
    public function index()
    {
        $user = Auth::user();

        // 1. Catálogo de servicios activos clasificados
        $servicios = Schema::hasTable('services')
            ? Service::where('is_active', true)->with('trabajadoresActivos')->orderBy('name', 'asc')->get()
            : collect();

        $serviciosEstetica = $servicios->filter(fn($s) => strtolower($s->category ?? '') === 'estetica');
        $serviciosBarberia = $servicios->filter(fn($s) => strtolower($s->category ?? '') === 'barberia');

        // 2. Especialistas activos
        $trabajadores = Schema::hasTable('trabajadores')
            ? Trabajador::where('activo', true)->with(['user', 'serviciosActivos'])->get()
            : collect();

        // 3. Clientes registrados para autocompletado y búsqueda en tiempo real
        $clientes = User::where('role_id', 4)
            ->select('id', 'name', 'apellidos', 'telefono', 'email')
            ->orderBy('name', 'asc')
            ->take(50)
            ->get();

        // 4. Fecha de hoy y formato amigable
        $today = now()->format('Y-m-d');
        $diasEsp = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        $mesesEsp = [1 => 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        $now = now();
        $todayFormatted = $diasEsp[$now->dayOfWeek] . ', ' . $now->day . ' de ' . $mesesEsp[$now->month] . ' de ' . $now->year;

        // 5. Turnos inmediatos disponibles para el día de hoy
        $disponibilidadService = app(DisponibilidadService::class);
        $turnosInmediatosHoy = [];

        foreach ($trabajadores as $trabajador) {
            try {
                $slots = $disponibilidadService->calcularHorariosDisponibles($trabajador, $today, 30);
                $turnosInmediatosHoy[$trabajador->id] = $slots;
            } catch (\Throwable $e) {
                $turnosInmediatosHoy[$trabajador->id] = [];
            }
        }

        // 6. Citas del día para monitoreo de ocupación
        $citasHoy = Schema::hasTable('citas')
            ? Cita::whereDate('fecha', $today)
                ->whereNotIn('estado', ['cancelada'])
                ->with(['trabajador', 'service', 'user'])
                ->orderBy('hora_inicio', 'asc')
                ->get()
            : collect();

        // 7. Estadísticas rápidas para el encabezado del panel
        $stats = [
            'total_especialistas' => $trabajadores->count(),
            'total_servicios' => $servicios->count(),
            'citas_hoy_count' => $citasHoy->count(),
            'clientes_count' => User::where('role_id', 4)->count(),
        ];

        return view('recepcion.dashboard', compact(
            'user',
            'servicios',
            'serviciosEstetica',
            'serviciosBarberia',
            'trabajadores',
            'clientes',
            'turnosInmediatosHoy',
            'citasHoy',
            'today',
            'todayFormatted',
            'stats'
        ));
    }

    /**
     * Módulo de gestión y consulta de citas: Muestra el listado con filtros combinables o responde en JSON.
     */
    public function citas(Request $request, CitaCalendarioService $calendarioService)
    {
        $user = Auth::user();
        Cita::ensureTableExists();

        $filtros = [
            'fecha' => $request->input('fecha'),
            'fecha_desde' => $request->input('fecha_desde'),
            'fecha_hasta' => $request->input('fecha_hasta'),
            'estado' => $request->input('estado', 'todas'),
            'trabajador_id' => $request->input('trabajador_id'),
            'buscar' => trim($request->input('buscar', '')),
        ];

        $citas = $calendarioService->consultarListadoCitas($filtros);

        $trabajadores = Schema::hasTable('trabajadores')
            ? Trabajador::where('activo', true)->with(['user', 'servicios'])->get()
            : collect();

        $servicios = Schema::hasTable('services')
            ? Service::where('is_active', true)->get()
            : collect();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'total' => $citas->count(),
                'citas' => $citas->map(fn($c) => $calendarioService->obtenerDetalleCita($c)),
            ]);
        }

        return view('recepcion.citas', compact('user', 'citas', 'trabajadores', 'servicios', 'filtros'));
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
        $cleanPhone = preg_replace('/[^0-9]/', '', $term);

        $query = User::where('role_id', 4);

        if ($term !== '') {
            $words = array_filter(explode(' ', $term));
            $query->where(function ($q) use ($term, $cleanPhone, $words) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('apellidos', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%")
                  ->orWhere('telefono', 'like', "%{$term}%");

                if (!empty($cleanPhone)) {
                    $q->orWhere('telefono', 'like', "%{$cleanPhone}%");
                }

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
                'tiene_cuenta' => !empty($c->email),
                'telefono' => $c->telefono ?? '',
                'iniciales' => strtoupper(substr($c->name, 0, 1) . substr($c->apellidos ?? '', 0, 1)),
            ]),
        ]);
    }

    /**
     * Registro simplificado de clientes desde recepción.
     * Únicamente solicita nombre, apellidos y número telefónico sin exigir correo ni generar cuenta de usuario.
     */
    public function registrarClienteRapido(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/u'],
            'apellidos' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/u'],
            'telefono' => 'required|string',
        ], [
            'nombre.required' => 'El nombre del cliente es obligatorio.',
            'nombre.min' => 'El nombre debe contener al menos 2 caracteres.',
            'nombre.regex' => 'El nombre no puede incluir números ni caracteres especiales.',
            'apellidos.required' => 'Los apellidos del cliente son obligatorios.',
            'apellidos.min' => 'Los apellidos deben contener al menos 2 caracteres.',
            'apellidos.regex' => 'Los apellidos no pueden incluir números ni caracteres especiales.',
            'telefono.required' => 'El número telefónico es obligatorio.',
        ]);

        // 1. Validación de la estructura del número telefónico: exactamente 10 dígitos numéricos
        $cleanPhone = preg_replace('/[^0-9]/', '', $validated['telefono']);
        if (strlen($cleanPhone) !== 10) {
            return response()->json([
                'success' => false,
                'mensaje' => 'El número telefónico no es válido. Debe contener exactamente 10 dígitos numéricos.',
                'errors' => [
                    'telefono' => ['El número telefónico debe contener exactamente 10 dígitos numéricos.']
                ]
            ], 422);
        }

        // 2. Control de duplicidad de registros (Criterio 4)
        $clienteExistente = User::where('role_id', 4)
            ->where(function ($q) use ($validated, $cleanPhone) {
                $q->where('telefono', $validated['telefono'])
                  ->orWhere('telefono', $cleanPhone);
            })
            ->first();

        if ($clienteExistente) {
            return response()->json([
                'success' => false,
                'duplicado' => true,
                'mensaje' => "El número telefónico {$cleanPhone} ya se encuentra registrado a nombre de {$clienteExistente->name} {$clienteExistente->apellidos}. Se ha seleccionado automáticamente.",
                'cliente' => [
                    'id' => $clienteExistente->id,
                    'nombre' => $clienteExistente->name,
                    'apellidos' => $clienteExistente->apellidos ?? '',
                    'nombre_completo' => trim("{$clienteExistente->name} " . ($clienteExistente->apellidos ?? '')),
                    'telefono' => $clienteExistente->telefono,
                    'iniciales' => strtoupper(substr($clienteExistente->name, 0, 1) . substr($clienteExistente->apellidos ?? '', 0, 1)),
                    'es_nuevo' => false,
                ]
            ], 409);
        }

        // 3. Creación sin cuenta de usuario ni credenciales de portal (Criterio 3)
        self::ensureUsersColumnsNullable();

        $cliente = null;
        try {
            $cliente = User::create([
                'name' => trim($validated['nombre']),
                'apellidos' => trim($validated['apellidos']),
                'telefono' => $cleanPhone,
                'email' => null,
                'password' => null,
                'role_id' => 4,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            self::ensureUsersColumnsNullable();
            $cliente = User::create([
                'name' => trim($validated['nombre']),
                'apellidos' => trim($validated['apellidos']),
                'telefono' => $cleanPhone,
                'email' => null,
                'password' => null,
                'role_id' => 4,
            ]);
        }

        return response()->json([
            'success' => true,
            'mensaje' => 'Cliente registrado sin cuenta de usuario. Perfil guardado y listo para continuar agendamiento.',
            'cliente' => [
                'id' => $cliente->id,
                'nombre' => $cliente->name,
                'apellidos' => $cliente->apellidos ?? '',
                'nombre_completo' => trim("{$cliente->name} " . ($cliente->apellidos ?? '')),
                'telefono' => $cliente->telefono,
                'email' => null,
                'tiene_cuenta' => false,
                'iniciales' => strtoupper(substr($cliente->name, 0, 1) . substr($cliente->apellidos ?? '', 0, 1)),
                'es_nuevo' => true,
            ]
        ], 201);
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
     * Registra un turno de atención inmediata o cita programada desde el panel principal de recepción.
     * Permite asociar o crear clientes automáticamente a partir de Nombre, Apellidos y Teléfono.
     */
    public function registrarTurnoRecepcion(Request $request)
    {
        Cita::ensureTableExists();

        $validated = $request->validate([
            'client_id' => 'nullable|integer|exists:users,id',
            'nombre' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/u'],
            'apellidos' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/u'],
            'telefono' => 'required|string',
            'modalidad' => 'required|in:momento,posterior',
            'service_id' => 'required|integer|exists:services,id',
            'worker_id' => 'required|integer|exists:trabajadores,id',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|string',
            'notas' => 'nullable|string|max:500',
        ], [
            'nombre.required' => 'El nombre del cliente es obligatorio.',
            'nombre.min' => 'El nombre debe contener al menos 2 caracteres.',
            'nombre.regex' => 'El nombre no puede incluir números ni caracteres especiales.',
            'apellidos.required' => 'Los apellidos del cliente son obligatorios.',
            'apellidos.min' => 'Los apellidos deben contener al menos 2 caracteres.',
            'apellidos.regex' => 'Los apellidos no pueden incluir números ni caracteres especiales.',
            'telefono.required' => 'El número telefónico es obligatorio.',
            'service_id.required' => 'Debe seleccionar un servicio del catálogo.',
            'worker_id.required' => 'Debe seleccionar un especialista.',
            'date.required' => 'Debe seleccionar una fecha.',
            'time.required' => 'Debe seleccionar un turno u horario de atención.',
        ]);

        $cleanPhone = preg_replace('/[^0-9]/', '', $validated['telefono']);
        if (strlen($cleanPhone) !== 10) {
            return response()->json([
                'success' => false,
                'mensaje' => 'El número telefónico no es válido. Debe contener exactamente 10 dígitos numéricos.',
                'errors' => [
                    'telefono' => ['El número telefónico debe contener exactamente 10 dígitos numéricos.']
                ]
            ], 422);
        }

        // 1. Asociar cliente previamente seleccionado o buscar por teléfono
        $cliente = null;
        if (!empty($validated['client_id'])) {
            $cliente = User::where('role_id', 4)->find($validated['client_id']);
        }

        if (!$cliente && !empty($cleanPhone)) {
            $cliente = User::where('role_id', 4)
                ->where(function ($q) use ($cleanPhone, $validated) {
                    $q->where('telefono', $validated['telefono'])
                      ->orWhere('telefono', $cleanPhone);
                })
                ->first();
        }

        // 2. Si no existe, crear perfil simplificado sin requerir correo ni generar credenciales de acceso (Criterio 3)
        if (!$cliente) {
            self::ensureUsersColumnsNullable();
            try {
                $cliente = User::create([
                    'name' => trim($validated['nombre']),
                    'apellidos' => trim($validated['apellidos']),
                    'telefono' => $cleanPhone ?: trim($validated['telefono']),
                    'email' => null,
                    'password' => null,
                    'role_id' => 4,
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
                self::ensureUsersColumnsNullable();
                $cliente = User::create([
                    'name' => trim($validated['nombre']),
                    'apellidos' => trim($validated['apellidos']),
                    'telefono' => $cleanPhone ?: trim($validated['telefono']),
                    'email' => null,
                    'password' => null,
                    'role_id' => 4,
                ]);
            }
        } else {
            if (empty($cliente->apellidos) && !empty($validated['apellidos'])) {
                $cliente->update(['apellidos' => trim($validated['apellidos'])]);
            }
        }

        $servicio = Service::findOrFail($validated['service_id']);
        $trabajador = Trabajador::findOrFail($validated['worker_id']);

        $disponibilidadService = app(DisponibilidadService::class);

        $prefijoNota = $validated['modalidad'] === 'momento' ? '[Atención Inmediata - Turno Sala] ' : '[Cita Programada - Recepción] ';
        $notasFinales = $prefijoNota . ($validated['notas'] ?? '');

        $resultado = $disponibilidadService->registrarCitaConcurrente(
            trabajador: $trabajador,
            servicio: $servicio,
            cliente: $cliente,
            fecha: $validated['date'],
            hora: $validated['time'],
            notas: $notasFinales,
            origen: 'recepcion'
        );

        $status = $resultado['status'] ?? ($resultado['success'] ? 200 : 422);
        unset($resultado['status']);

        if (!empty($resultado['success'])) {
            $citaId = $resultado['cita']['id'] ?? ($resultado['cita']->id ?? rand(100, 999));
            $resultado['cliente'] = [
                'id' => $cliente->id,
                'nombre' => $cliente->name,
                'apellidos' => $cliente->apellidos ?? '',
                'nombre_completo' => trim($cliente->name . ' ' . ($cliente->apellidos ?? '')),
                'telefono' => $cliente->telefono ?? '',
            ];
            $resultado['modalidad'] = $validated['modalidad'];
            $resultado['servicio'] = [
                'id' => $servicio->id,
                'name' => $servicio->name,
                'price' => number_format((float)$servicio->price, 2),
                'duration_minutes' => $servicio->duration_minutes,
            ];
            $resultado['especialista'] = [
                'id' => $trabajador->id,
                'nombre' => $trabajador->nombre_completo,
            ];
            $resultado['fecha'] = $validated['date'];
            $resultado['hora'] = $validated['time'];
            $resultado['folio_turno'] = ($validated['modalidad'] === 'momento' ? 'TURNO-' : 'CITA-') . str_pad($citaId, 4, '0', STR_PAD_LEFT);
        }

        return response()->json($resultado, $status);
    }

    /**
     * Vista de Calendario de recepción interactivo con datos reales de trabajadores y servicios.
     */
    public function calendario()
    {
        $user = Auth::user();
        Cita::ensureTableExists();

        $trabajadores = Schema::hasTable('trabajadores')
            ? Trabajador::where('activo', true)->with(['user', 'servicios'])->get()
            : collect();

        $servicios = Schema::hasTable('services')
            ? Service::where('is_active', true)->get()
            : collect();

        return view('recepcion.calendario', compact('user', 'trabajadores', 'servicios'));
    }

    /**
     * API AJAX para consultar citas del calendario según periodo y filtros.
     */
    public function obtenerCitasCalendario(Request $request, CitaCalendarioService $calendarioService)
    {
        $validated = $request->validate([
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start',
            'worker_id' => 'nullable|integer',
            'estado' => 'nullable|string',
        ]);

        $eventos = $calendarioService->obtenerEventosCalendario(
            startDate: $validated['start'],
            endDate: $validated['end'],
            workerId: !empty($validated['worker_id']) ? (int) $validated['worker_id'] : null,
            estado: $validated['estado'] ?? null
        );

        return response()->json([
            'success' => true,
            'total' => count($eventos),
            'eventos' => $eventos,
        ]);
    }

    /**
     * API AJAX para obtener el detalle de una cita específica.
     */
    public function detalleCita(Cita $cita, CitaCalendarioService $calendarioService)
    {
        $detalle = $calendarioService->obtenerDetalleCita($cita);

        return response()->json([
            'success' => true,
            'cita' => $detalle,
        ]);
    }

    /**
     * API AJAX para actualizar el estado y observaciones de una cita.
     */
    public function actualizarEstadoCita(Request $request, Cita $cita, CitaCalendarioService $calendarioService)
    {
        $validated = $request->validate([
            'estado' => 'required|string|in:confirmada,en_proceso,en_atencion,completada,cancelada,vencida',
            'observaciones' => 'nullable|string|max:2500',
            'notas' => 'nullable|string|max:1000',
        ]);

        try {
            $resultado = $calendarioService->actualizarEstado(
                cita: $cita,
                nuevoEstado: $validated['estado'],
                observaciones: $validated['observaciones'] ?? null,
                notas: $validated['notas'] ?? null,
                usuario: $request->user()
            );

            return response()->json($resultado);
        } catch (\DomainException | \InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'mensaje' => $e->getMessage(),
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al actualizar la cita: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API AJAX / Form para actualizar/editar una cita existente (Recepción).
     */
    public function actualizarCita(Request $request, Cita $cita, CitaCalendarioService $calendarioService)
    {
        $validated = $request->validate([
            'service_id' => 'nullable|exists:services,id',
            'trabajador_id' => 'nullable|exists:trabajadores,id',
            'worker_id' => 'nullable|exists:trabajadores,id',
            'fecha' => 'nullable|date|after_or_equal:today',
            'date' => 'nullable|date|after_or_equal:today',
            'hora_inicio' => 'nullable|string',
            'time' => 'nullable|string',
            'notas' => 'nullable|string|max:1000',
            'observaciones' => 'nullable|string|max:2500',
        ], [
            'fecha.after_or_equal' => 'No es posible reprogramar una cita para una fecha anterior al día de hoy.',
            'date.after_or_equal' => 'No es posible reprogramar una cita para una fecha anterior al día de hoy.',
        ]);

        try {
            $resultado = $calendarioService->editarCita(
                cita: $cita,
                datos: $validated,
                usuario: $request->user()
            );

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json($resultado);
            }

            return back()->with('status', $resultado['mensaje'] ?? 'Cita actualizada correctamente.');
        } catch (\DomainException | \InvalidArgumentException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage(),
                    'mensaje' => $e->getMessage(),
                ], 422);
            }
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        } catch (\Throwable $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Error al actualizar la cita: ' . $e->getMessage(),
                ], 500);
            }
            return back()->withErrors(['error' => 'Error al actualizar la cita.']);
        }
    }

    /**
     * API AJAX / Form para cancelar una cita (Recepción).
     */
    public function cancelarCita(Request $request, Cita $cita, CitaCalendarioService $calendarioService)
    {
        $validated = $request->validate([
            'motivo' => 'nullable|string|max:500',
        ]);

        try {
            $resultado = $calendarioService->cancelarCita(
                cita: $cita,
                motivo: $validated['motivo'] ?? null,
                usuario: $request->user()
            );

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json($resultado);
            }

            return back()->with('status', $resultado['mensaje'] ?? 'Cita cancelada correctamente.');
        } catch (\DomainException | \InvalidArgumentException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage(),
                    'mensaje' => $e->getMessage(),
                ], 422);
            }
            return back()->withErrors(['error' => $e->getMessage()]);
        } catch (\Throwable $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Error al cancelar la cita: ' . $e->getMessage(),
                ], 500);
            }
            return back()->withErrors(['error' => 'Error al cancelar la cita.']);
        }
    }

    /**
     * API AJAX / Form para confirmar una cita (Recepción).
     */
    public function confirmarCitaDirecta(Request $request, Cita $cita, CitaCalendarioService $calendarioService)
    {
        try {
            $resultado = $calendarioService->confirmarCita(
                cita: $cita,
                usuario: $request->user()
            );

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json($resultado);
            }

            return back()->with('status', $resultado['mensaje'] ?? 'Cita confirmada correctamente.');
        } catch (\DomainException | \InvalidArgumentException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage(),
                    'mensaje' => $e->getMessage(),
                ], 422);
            }
            return back()->withErrors(['error' => $e->getMessage()]);
        } catch (\Throwable $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Error al confirmar la cita: ' . $e->getMessage(),
                ], 500);
            }
            return back()->withErrors(['error' => 'Error al confirmar la cita.']);
        }
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

    /**
     * API AJAX / Form para registrar check-in (llegada del cliente) a una cita (Recepción).
     */
    public function checkInCita(Request $request, Cita $cita, CitaCalendarioService $calendarioService)
    {
        try {
            $resultado = $calendarioService->registrarCheckIn(
                cita: $cita,
                usuario: $request->user()
            );

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json($resultado);
            }

            return back()->with('status', $resultado['mensaje'] ?? 'Check-in registrado correctamente.');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage(),
                    'mensaje' => $e->getMessage(),
                ], 403);
            }
            abort(403, $e->getMessage());
        } catch (\DomainException | \InvalidArgumentException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage(),
                    'mensaje' => $e->getMessage(),
                ], 422);
            }
            return back()->withErrors(['error' => $e->getMessage()]);
        } catch (\Throwable $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Error al registrar el check-in: ' . $e->getMessage(),
                ], 500);
            }
            return back()->withErrors(['error' => 'Error al registrar el check-in.']);
        }
    }

    /**
     * Asegura que las columnas email y password de users admitan NULL en la base de datos
     * para permitir el registro de clientes sin cuenta de usuario ni credenciales.
     */
    public static function ensureUsersColumnsNullable(): void
    {
        try {
            if (Schema::hasTable('users')) {
                $driver = DB::getDriverName();
                if ($driver === 'mysql' || $driver === 'mariadb') {
                    DB::statement('ALTER TABLE `users` MODIFY `email` VARCHAR(255) NULL');
                    DB::statement('ALTER TABLE `users` MODIFY `password` VARCHAR(255) NULL');
                }
            }
        } catch (\Throwable $e) {
            // Silencioso si ya están modificadas o no hay permisos DDL
        }
    }
}
