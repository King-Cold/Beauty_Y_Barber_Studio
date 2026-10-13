<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Service;
use App\Models\Trabajador;
use App\Models\User;
use Illuminate\Http\Request;
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
     * Actualiza el estado de la cita asignada al trabajador.
     */
    public function actualizarEstado(Request $request, Cita $cita)
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
            'estado' => ['required', 'string', 'in:confirmada,en_atencion,completada,cancelada'],
            'observaciones' => ['nullable', 'string', 'max:2500'],
        ]);

        $updateData = ['estado' => $validated['estado']];
        if ($request->has('observaciones')) {
            $updateData['observaciones'] = $request->input('observaciones');
        }

        $cita->update($updateData);

        $mensajes = [
            'confirmada' => 'La cita ha sido marcada como confirmada.',
            'en_atencion' => '¡Servicio iniciado! La cita está ahora en atención.',
            'completada' => '¡Excelente trabajo! La cita ha sido marcada como completada.',
            'cancelada' => 'La cita ha sido cancelada.',
        ];

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'mensaje' => $mensajes[$validated['estado']] ?? 'Estado actualizado correctamente.',
                'nuevo_estado' => $validated['estado'],
                'observaciones' => $cita->observaciones ?: '',
            ]);
        }

        return back()->with('success', $mensajes[$validated['estado']] ?? 'Estado actualizado correctamente.');
    }

    /**
     * Guarda o actualiza las observaciones técnicas del servicio de la cita.
     * Permite opcionalmente marcar la cita como completada en la misma acción.
     */
    public function guardarObservaciones(Request $request, Cita $cita)
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

        $cita->observaciones = $validated['observaciones'] ?? '';

        if (!empty($validated['marcar_completada'])) {
            $cita->estado = 'completada';
        }

        $cita->save();

        $mensaje = !empty($validated['marcar_completada'])
            ? '¡Servicio completado y observaciones técnicas registradas con éxito!'
            : 'Observaciones del servicio guardadas correctamente.';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'mensaje' => $mensaje,
                'observaciones' => $cita->observaciones ?: '',
                'nuevo_estado' => $cita->estado,
            ]);
        }

        return back()->with('success', $mensaje);
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
}
