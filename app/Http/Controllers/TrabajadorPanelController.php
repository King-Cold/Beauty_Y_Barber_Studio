<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Service;
use App\Models\Trabajador;
use App\Models\User;
use App\Services\CitaCalendarioService;
use App\Services\ProfileService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class TrabajadorPanelController extends Controller
{
    /**
     * Obtiene o inicializa la ficha técnica del trabajador para el usuario autenticado.
     */
    protected function getOrCreateTrabajador(User $user): ?Trabajador
    {
        if ($user->trabajador) {
            return $user->trabajador;
        }

        if (Schema::hasTable('trabajadores')) {
            $trabajador = Trabajador::where('user_id', $user->id)->first();
            if ($trabajador) {
                return $trabajador;
            }

            // Si es un usuario con rol de trabajador o admin, creamos su registro base
            if ($user->role_id === 3 || $user->role_id === 1) {
                $trabajador = Trabajador::create([
                    'user_id' => $user->id,
                    'direccion' => 'Beauty & Barber Studio - Estación Principal',
                    'fotografia' => null,
                    'experiencia' => 4,
                    'activo' => true,
                    'horario' => [
                        'lunes' => ['inicio' => '09:00', 'fin' => '19:00', 'laboral' => true],
                        'martes' => ['inicio' => '09:00', 'fin' => '19:00', 'laboral' => true],
                        'miercoles' => ['inicio' => '09:00', 'fin' => '19:00', 'laboral' => true],
                        'jueves' => ['inicio' => '09:00', 'fin' => '19:00', 'laboral' => true],
                        'viernes' => ['inicio' => '09:00', 'fin' => '20:00', 'laboral' => true],
                        'sabado' => ['inicio' => '09:00', 'fin' => '20:00', 'laboral' => true],
                        'domingo' => ['laboral' => false],
                    ],
                ]);

                // Asignar servicios si existe la tabla pivote
                if (Schema::hasTable('servicio_trabajador') && Schema::hasTable('services')) {
                    $services = Service::where('is_active', true)->pluck('id');
                    if ($services->isNotEmpty()) {
                        $trabajador->servicios()->sync($services);
                    }
                }

                return $trabajador;
            }
        }

        return null;
    }

    /**
     * Panel Principal (Dashboard) del Trabajador.
     */
    public function dashboard()
    {
        $user = Auth::user();
        $trabajador = $this->getOrCreateTrabajador($user);

        if (class_exists(Cita::class) && method_exists(Cita::class, 'ensureTableExists')) {
            Cita::ensureTableExists();
        }

        $hoy = Carbon::today()->format('Y-m-d');
        $citasHoy = collect();
        $citasProximas = collect();

        if ($trabajador && Schema::hasTable('citas')) {
            $citasHoy = Cita::with(['cliente', 'service'])
                ->where('trabajador_id', $trabajador->id)
                ->whereDate('fecha', $hoy)
                ->orderBy('hora_inicio', 'asc')
                ->get();

            $citasProximas = Cita::with(['cliente', 'service'])
                ->where('trabajador_id', $trabajador->id)
                ->whereDate('fecha', '>=', $hoy)
                ->orderBy('fecha', 'asc')
                ->orderBy('hora_inicio', 'asc')
                ->take(6)
                ->get();
        }

        return view('trabajador.dashboard', compact('user', 'trabajador', 'citasHoy', 'citasProximas'));
    }

    /**
     * Agenda del Trabajador organizada por fecha.
     * Muestra las citas agrupadas cronológicamente por día, con tira semanal interactiva
     * y filtros por estado y término de búsqueda.
     */
    public function agenda(Request $request)
    {
        $user = Auth::user();
        $trabajador = $this->getOrCreateTrabajador($user);

        if (class_exists(Cita::class) && method_exists(Cita::class, 'ensureTableExists')) {
            Cita::ensureTableExists();
        }

        $fechaSeleccionada = $request->input('fecha', 'todas');
        $filtroEstado = $request->input('estado', 'todas');
        $terminoBusqueda = trim($request->input('buscar', ''));

        $query = Cita::with(['cliente', 'service'])
            ->where('trabajador_id', $trabajador ? $trabajador->id : 0);

        // Filtro por fecha (a menos que se seleccione 'todas')
        if ($fechaSeleccionada && $fechaSeleccionada !== 'todas') {
            $query->whereDate('fecha', $fechaSeleccionada);
        }

        // Filtro por estado
        if ($filtroEstado && $filtroEstado !== 'todas') {
            $query->where('estado', $filtroEstado);
        }

        // Búsqueda por cliente o servicio
        if (!empty($terminoBusqueda)) {
            $query->where(function ($q) use ($terminoBusqueda) {
                $q->whereHas('cliente', function ($cq) use ($terminoBusqueda) {
                    $cq->where('name', 'like', "%{$terminoBusqueda}%")
                        ->orWhere('apellidos', 'like', "%{$terminoBusqueda}%")
                        ->orWhere('telefono', 'like', "%{$terminoBusqueda}%")
                        ->orWhere('email', 'like', "%{$terminoBusqueda}%");
                })->orWhereHas('service', function ($sq) use ($terminoBusqueda) {
                    $sq->where('name', 'like', "%{$terminoBusqueda}%")
                        ->orWhere('category', 'like', "%{$terminoBusqueda}%");
                });
            });
        }

        // Citas ordenadas estrictamente por fecha y hora
        $citas = $query->orderBy('fecha', 'asc')
            ->orderBy('hora_inicio', 'asc')
            ->get();

        // Agrupación organizada por fecha
        $citasAgrupadas = $citas->groupBy(function ($cita) {
            return $cita->fecha instanceof Carbon ? $cita->fecha->format('Y-m-d') : (string)$cita->fecha;
        });

        // Construcción de la Tira Semanal Interactiva (Day Strip)
        $centroFecha = ($fechaSeleccionada && $fechaSeleccionada !== 'todas')
            ? Carbon::parse($fechaSeleccionada)
            : Carbon::today();

        $inicioSemana = $centroFecha->copy()->startOfWeek();
        $diasSemana = collect();

        for ($i = 0; $i < 7; $i++) {
            $diaIter = $inicioSemana->copy()->addDays($i);
            $diaStr = $diaIter->format('Y-m-d');
            $cantidadEnDia = 0;

            if ($trabajador) {
                $cantidadEnDia = Cita::where('trabajador_id', $trabajador->id)
                    ->whereDate('fecha', $diaStr)
                    ->count();
            }

            $diasSemana->push([
                'fecha' => $diaStr,
                'nombre_dia' => ucfirst($diaIter->locale('es')->isoFormat('ddd')),
                'numero_dia' => $diaIter->format('d'),
                'mes' => ucfirst($diaIter->locale('es')->isoFormat('MMM')),
                'es_hoy' => $diaIter->isToday(),
                'es_seleccionado' => ($fechaSeleccionada === $diaStr),
                'citas_count' => $cantidadEnDia,
            ]);
        }

        $semanaAnterior = $inicioSemana->copy()->subWeek()->format('Y-m-d');
        $semanaSiguiente = $inicioSemana->copy()->addWeek()->format('Y-m-d');

        // Métricas globales
        $totalCitas = $citas->count();
        $citasEnAtencion = $citas->where('estado', 'en_atencion')->count();
        $citasCompletadas = $citas->where('estado', 'completada')->count();
        $citasConfirmadas = $citas->where('estado', 'confirmada')->count();
        $ingresosEstimados = $citas->whereIn('estado', ['confirmada', 'en_atencion', 'completada'])->sum('precio');

        return view('trabajador.agenda', compact(
            'user',
            'trabajador',
            'fechaSeleccionada',
            'filtroEstado',
            'terminoBusqueda',
            'citas',
            'citasAgrupadas',
            'diasSemana',
            'inicioSemana',
            'semanaAnterior',
            'semanaSiguiente',
            'totalCitas',
            'citasEnAtencion',
            'citasCompletadas',
            'citasConfirmadas',
            'ingresosEstimados'
        ));
    }

    /**
     * Consulta detallada de una cita específica del trabajador (para modal / AJAX).
     */
    public function showCita(Cita $cita)
    {
        $user = Auth::user();
        $trabajador = $this->getOrCreateTrabajador($user);

        if (!$trabajador || ($cita->trabajador_id !== $trabajador->id && $user->role_id !== 1)) {
            return response()->json([
                'success' => false,
                'error' => 'Acceso denegado: No tienes permiso para consultar citas de otros especialistas.'
            ], 403);
        }

        $cita->load(['cliente', 'service']);

        $fechaFormateada = Carbon::parse($cita->fecha)->locale('es')->isoFormat('dddd D [de] MMMM, YYYY');

        return response()->json([
            'success' => true,
            'cita' => [
                'id' => $cita->id,
                'fecha' => $cita->fecha->format('Y-m-d'),
                'fecha_formateada' => ucfirst($fechaFormateada),
                'hora_inicio' => $cita->hora_inicio,
                'hora_fin' => $cita->hora_fin,
                'rango_horario' => $cita->rango_horario,
                'duracion_minutos' => $cita->duracion_minutos,
                'precio' => number_format($cita->precio, 2),
                'estado' => $cita->estado,
                'notas' => $cita->notas ?: 'Sin especificaciones o notas adicionales.',
                'observaciones' => $cita->observaciones ?: '',
                'cliente' => [
                    'id' => $cita->cliente->id ?? null,
                    'nombre_completo' => ($cita->cliente->name ?? 'Cliente') . ' ' . ($cita->cliente->apellidos ?? ''),
                    'telefono' => $cita->cliente->telefono ?? 'No registrado',
                    'email' => $cita->cliente->email ?? 'No registrado',
                ],
                'servicio' => [
                    'id' => $cita->service->id ?? null,
                    'nombre' => $cita->service->name ?? 'Servicio General',
                    'descripcion' => $cita->service->description ?? 'Servicio profesional de estética y barbería.',
                    'categoria' => ucfirst($cita->service->category ?? 'Barbería'),
                    'duracion_minutos' => $cita->service->duration_minutes ?? $cita->duracion_minutos,
                ],
            ]
        ]);
    }

    /**
     * Actualiza el estado de la cita asignada al trabajador aplicando las reglas de transición.
     */
    public function actualizarEstado(Request $request, Cita $cita, CitaCalendarioService $calendarioService)
    {
        $user = Auth::user();
        $trabajador = $this->getOrCreateTrabajador($user);

        if (!$trabajador || ($cita->trabajador_id !== $trabajador->id && $user->role_id !== 1)) {
            return response()->json([
                'success' => false,
                'error' => 'Acceso denegado: No tienes permiso para modificar citas de otros especialistas.'
            ], 403);
        }

        $validated = $request->validate([
            'estado' => ['required', 'string', 'in:confirmada,en_atencion,en_proceso,completada,cancelada'],
            'observaciones' => ['nullable', 'string', 'max:2500'],
        ]);

        try {
            $resultado = $calendarioService->actualizarEstado(
                cita: $cita,
                nuevoEstado: $validated['estado'],
                observaciones: $request->has('observaciones') ? $validated['observaciones'] : null,
                notas: null,
                usuario: $user
            );

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'mensaje' => $resultado['mensaje'],
                    'nuevo_estado' => $resultado['cita']['estado'],
                    'observaciones' => $resultado['cita']['observaciones'] ?? '',
                ]);
            }

            return back()->with('success', $resultado['mensaje']);
        } catch (\DomainException | \InvalidArgumentException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage(),
                    'mensaje' => $e->getMessage(),
                ], 422);
            }

            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Error al actualizar la cita: ' . $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Error al actualizar la cita.');
        }
    }

    /**
     * Guarda o actualiza las observaciones técnicas del servicio de la cita.
     * Permite opcionalmente marcar la cita como completada en la misma acción bajo las reglas de transición.
     */
    public function guardarObservaciones(Request $request, Cita $cita, CitaCalendarioService $calendarioService)
    {
        $user = Auth::user();
        $trabajador = $this->getOrCreateTrabajador($user);

        if (!$trabajador || ($cita->trabajador_id !== $trabajador->id && $user->role_id !== 1)) {
            return response()->json([
                'success' => false,
                'error' => 'Acceso denegado: No tienes permiso para registrar observaciones en citas de otros especialistas.'
            ], 403);
        }

        $validated = $request->validate([
            'observaciones' => ['nullable', 'string', 'max:2500'],
            'marcar_completada' => ['nullable', 'boolean'],
        ]);

        try {
            if (!empty($validated['marcar_completada'])) {
                $resultado = $calendarioService->actualizarEstado(
                    cita: $cita,
                    nuevoEstado: 'completada',
                    observaciones: $validated['observaciones'] ?? null,
                    notas: null,
                    usuario: $user
                );

                $mensaje = '¡Servicio completado y observaciones técnicas registradas con éxito!';
            } else {
                $cita->observaciones = $validated['observaciones'] ?? '';
                $cita->save();
                $mensaje = 'Observaciones del servicio guardadas correctamente.';
            }

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'mensaje' => $mensaje,
                    'observaciones' => $cita->observaciones ?: '',
                    'nuevo_estado' => $cita->estado,
                ]);
            }

            return back()->with('success', $mensaje);
        } catch (\DomainException | \InvalidArgumentException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage(),
                    'mensaje' => $e->getMessage(),
                ], 422);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Configuración de la cuenta del Trabajador.
     */
    public function configuracion()
    {
        $user = Auth::user();
        $trabajador = $this->getOrCreateTrabajador($user);

        $serviciosAsignados = collect();
        if ($trabajador && method_exists($trabajador, 'servicios')) {
            $serviciosAsignados = $trabajador->servicios()->get();
        }

        return view('trabajador.configuracion', compact('user', 'trabajador', 'serviciosAsignados'));
    }

    /**
     * Actualiza los datos personales del trabajador (nombre, apellidos, teléfono en User y dirección en Trabajador).
     */
    public function actualizarDatos(Request $request, ProfileService $profileService)
    {
        /** @var User $user */
        $user = Auth::user();
        $trabajador = $this->getOrCreateTrabajador($user);

        try {
            $validated = $profileService->validateProfileData($user, $request->all(), [
                'direccion' => ['nullable', 'string', 'max:255'],
            ]);

            $user->name = $validated['name'];
            $user->apellidos = $validated['apellidos'];
            if (array_key_exists('telefono', $validated)) {
                $user->telefono = $validated['telefono'];
            }
            $user->save();

            if ($trabajador && array_key_exists('direccion', $validated)) {
                $trabajador->direccion = $validated['direccion'];
                $trabajador->save();
            }

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => '¡Datos personales actualizados correctamente!',
                    'user' => [
                        'name' => $user->name,
                        'apellidos' => $user->apellidos,
                        'telefono' => $user->telefono,
                        'direccion' => $trabajador ? $trabajador->direccion : null,
                    ],
                ]);
            }

            return back()->with('success', '¡Datos personales actualizados correctamente!');
        } catch (ValidationException $e) {
            $primerError = collect($e->errors())->flatten()->first() ?? 'Por favor verifica los datos ingresados.';

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $primerError,
                    'errors' => $e->errors(),
                ], 422);
            }

            return back()->withErrors($e->errors())->withInput();
        } catch (\Throwable $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error inesperado al actualizar los datos: ' . $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Ocurrió un error al actualizar los datos.');
        }
    }

    /**
     * Actualiza la contraseña del trabajador validando su contraseña actual y las reglas de seguridad.
     */
    public function actualizarPassword(Request $request, ProfileService $profileService)
    {
        /** @var User $user */
        $user = Auth::user();

        try {
            $resultado = $profileService->changePassword($user, $request->all());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $resultado['message'] ?? '¡Tu contraseña ha sido actualizada con éxito!',
                ]);
            }

            return back()->with('success', $resultado['message'] ?? '¡Tu contraseña ha sido actualizada con éxito!');
        } catch (ValidationException $e) {
            $primerError = collect($e->errors())->flatten()->first() ?? 'Error al actualizar la contraseña.';

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $primerError,
                    'errors' => $e->errors(),
                ], 422);
            }

            return back()->withErrors($e->errors());
        } catch (\Throwable $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar la contraseña: ' . $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Ocurrió un error al actualizar la contraseña.');
        }
    }
}
