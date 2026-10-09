<?php

namespace App\Http\Controllers;

use App\Models\Trabajador;
use App\Models\Service;
use App\Models\HorarioSucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class TrabajadorController extends Controller
{
    /**
     * Muestra la interfaz de administración y consulta de trabajadores (Subtarea 4).
     */
    public function index(Request $request)
    {
        $query = Trabajador::query();

        // Filtro por estado de actividad
        if ($request->filled('estado')) {
            if ($request->estado === 'activos' || $request->estado === 'active') {
                $query->where('activo', true);
            } elseif ($request->estado === 'inactivos' || $request->estado === 'inactive') {
                $query->where('activo', false);
            }
        }

        // Búsqueda por término (nombre, apellidos, teléfono, correo o dirección)
        if ($request->filled('buscar')) {
            $buscar = trim($request->buscar);
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('apellidos', 'like', "%{$buscar}%")
                  ->orWhere('telefono', 'like', "%{$buscar}%")
                  ->orWhere('email', 'like', "%{$buscar}%")
                  ->orWhere('direccion', 'like', "%{$buscar}%");
            });
        }

        $trabajadores = $query->with(['servicios' => function ($q) {
            $q->where('services.is_active', true);
        }])->orderBy('id', 'desc')->get();

        // Métricas globales de la base de datos
        $todos = Trabajador::all();
        $totalEquipo = $todos->count();
        $activosCount = $todos->where('activo', true)->count();
        $inactivosCount = $todos->where('activo', false)->count();

        // Catálogo de servicios activos disponibles
        $servicios = Service::where('is_active', true)->orderBy('category')->orderBy('name')->get();
        $serviciosCount = $servicios->count();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $trabajadores,
                'kpis' => [
                    'total' => $totalEquipo,
                    'activos' => $activosCount,
                    'inactivos' => $inactivosCount,
                    'servicios' => $serviciosCount,
                ],
                'servicios_disponibles' => $servicios,
            ]);
        }

        $branchLimits = HorarioSucursal::getHorariosMap();

        return view('admin.trabajadores.index', compact(
            'trabajadores',
            'totalEquipo',
            'activosCount',
            'inactivosCount',
            'servicios',
            'serviciosCount',
            'branchLimits'
        ));
    }

    /**
     * Consulta la información detallada de un trabajador específico (Subtarea 4).
     */
    public function show(Trabajador $trabajador, Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $trabajador->id,
                    'nombre' => $trabajador->nombre,
                    'apellidos' => $trabajador->apellidos,
                    'nombre_completo' => $trabajador->nombre_completo,
                    'telefono' => $trabajador->telefono,
                    'email' => $trabajador->email,
                    'direccion' => $trabajador->direccion,
                    'experiencia' => $trabajador->experiencia,
                    'fotografia' => $trabajador->fotografia,
                    'fotografia_url' => $trabajador->fotografia ? asset('storage/' . $trabajador->fotografia) : null,
                    'activo' => $trabajador->activo,
                    'fecha_registro' => $trabajador->created_at ? $trabajador->created_at->format('d/m/Y H:i') : null,
                    'fecha_actualizacion' => $trabajador->updated_at ? $trabajador->updated_at->format('d/m/Y H:i') : null,
                ]
            ]);
        }

        return redirect()->route('admin.trabajadores.index');
    }

    /**
     * Almacena un nuevo trabajador en el sistema (Subtarea 3).
     */
    public function store(Request $request)
    {
        // Normalizar nombre y apellidos
        if ($request->has('nombre')) {
            $request->merge(['nombre' => trim(preg_replace('/\s+/', ' ', $request->nombre))]);
        }
        if ($request->has('apellidos')) {
            $request->merge(['apellidos' => trim(preg_replace('/\s+/', ' ', $request->apellidos))]);
        }

        // Normalizar correo electrónico
        if ($request->has('email')) {
            $request->merge(['email' => strtolower(trim($request->email))]);
        }

        // Normalizar teléfono
        if ($request->has('telefono')) {
            $request->merge(['telefono' => trim($request->telefono)]);
        }

        $validated = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/u',
                'unique:trabajadores,nombre',
            ],
            'apellidos' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/u',
            ],
            'telefono' => ['required', 'string', 'size:10', 'regex:/^[0-9]{10}$/', 'unique:trabajadores,telefono'],
            'email' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', 'unique:trabajadores,email'],
            'direccion' => ['required', 'string', 'max:255'],
            'experiencia' => ['required', 'integer', 'min:0', 'max:50'],
            'fotografia' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'activo' => ['nullable', 'boolean'],
        ], [
            'nombre.required' => 'El nombre del trabajador es obligatorio.',
            'nombre.regex' => 'El campo Nombre únicamente debe permitir letras del abecedario.',
            'nombre.unique' => 'Ya existe un trabajador registrado con este nombre.',
            'apellidos.required' => 'Los apellidos son obligatorios.',
            'apellidos.regex' => 'El campo Apellidos únicamente debe permitir letras del abecedario.',
            'telefono.required' => 'El teléfono de contacto es obligatorio.',
            'telefono.size' => 'El número de teléfono debe contener exactamente 10 dígitos numéricos.',
            'telefono.regex' => 'El número de teléfono debe contener únicamente 10 dígitos numéricos (sin letras ni caracteres especiales).',
            'telefono.unique' => 'Ya existe un trabajador registrado con este número de teléfono.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.regex' => 'Introduce un correo electrónico válido (ej. usuario@dominio.com).',
            'email.unique' => 'Ya existe un trabajador registrado con este correo electrónico.',
            'direccion.required' => 'La dirección es obligatoria.',
            'experiencia.required' => 'Los años de experiencia son obligatorios.',
            'experiencia.integer' => 'La experiencia debe ser un número entero.',
            'experiencia.min' => 'La experiencia no puede ser un número negativo.',
            'fotografia.image' => 'El archivo seleccionado debe ser una imagen válida.',
            'fotografia.mimes' => 'La fotografía debe ser en formato JPG, JPEG, PNG o WEBP.',
            'fotografia.max' => 'La fotografía no debe superar 2 MB.',
        ]);

        // Manejo de la subida de fotografía
        if ($request->hasFile('fotografia')) {
            $path = $request->file('fotografia')->store('trabajadores', 'public');
            $validated['fotografia'] = $path;
        }

        // Estado por defecto activo (Subtarea 8)
        $validated['activo'] = $request->has('activo') ? $request->boolean('activo') : true;

        $trabajador = Trabajador::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Trabajador registrado exitosamente.',
                'data' => $trabajador,
            ], 201);
        }

        return redirect()
            ->route('admin.trabajadores.index')
            ->with('success', 'Trabajador registrado exitosamente.');
    }

    /**
     * Actualiza la información de un trabajador en la base de datos (Subtarea 5).
     */
    public function update(Request $request, Trabajador $trabajador)
    {
        // Normalizar nombre y apellidos si vienen
        if ($request->has('nombre')) {
            $request->merge(['nombre' => trim(preg_replace('/\s+/', ' ', $request->nombre))]);
        }
        if ($request->has('apellidos')) {
            $request->merge(['apellidos' => trim(preg_replace('/\s+/', ' ', $request->apellidos))]);
        }

        // Normalizar correo electrónico si viene
        if ($request->has('email')) {
            $request->merge(['email' => strtolower(trim($request->email))]);
        }

        // Normalizar teléfono si viene
        if ($request->has('telefono')) {
            $request->merge(['telefono' => trim($request->telefono)]);
        }

        $validated = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/u',
                Rule::unique('trabajadores', 'nombre')->ignore($trabajador->id),
            ],
            'apellidos' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/u',
            ],
            'telefono' => [
                'required',
                'string',
                'size:10',
                'regex:/^[0-9]{10}$/',
                Rule::unique('trabajadores', 'telefono')->ignore($trabajador->id),
            ],
            'email' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                Rule::unique('trabajadores', 'email')->ignore($trabajador->id),
            ],
            'direccion' => ['required', 'string', 'max:255'],
            'experiencia' => ['required', 'integer', 'min:0', 'max:50'],
            'fotografia' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'activo' => ['nullable'],
            'status' => ['nullable'],
        ], [
            'nombre.required' => 'El nombre del trabajador es obligatorio.',
            'nombre.regex' => 'El campo Nombre únicamente debe permitir letras del abecedario.',
            'nombre.unique' => 'Ya existe otro trabajador registrado con este nombre.',
            'apellidos.required' => 'Los apellidos son obligatorios.',
            'apellidos.regex' => 'El campo Apellidos únicamente debe permitir letras del abecedario.',
            'telefono.required' => 'El teléfono de contacto es obligatorio.',
            'telefono.size' => 'El número de teléfono debe contener exactamente 10 dígitos numéricos.',
            'telefono.regex' => 'El número de teléfono debe contener únicamente 10 dígitos numéricos (sin letras ni caracteres especiales).',
            'telefono.unique' => 'Ya existe otro trabajador registrado con este número de teléfono.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.regex' => 'Introduce un correo electrónico válido (ej. usuario@dominio.com).',
            'email.unique' => 'Ya existe otro trabajador registrado con este correo electrónico.',
            'direccion.required' => 'La dirección es obligatoria.',
            'experiencia.required' => 'Los años de experiencia son obligatorios.',
            'experiencia.integer' => 'La experiencia debe ser un número entero.',
            'experiencia.min' => 'La experiencia no puede ser un número negativo.',
            'fotografia.image' => 'El archivo seleccionado debe ser una imagen válida.',
            'fotografia.mimes' => 'La fotografía debe ser en formato JPG, JPEG, PNG o WEBP.',
            'fotografia.max' => 'La fotografía no debe superar 2 MB.',
        ]);

        // Manejo de reemplazo de fotografía
        if ($request->hasFile('fotografia')) {
            if ($trabajador->fotografia && Storage::disk('public')->exists($trabajador->fotografia)) {
                Storage::disk('public')->delete($trabajador->fotografia);
            }
            $path = $request->file('fotografia')->store('trabajadores', 'public');
            $validated['fotografia'] = $path;
        }

        // Manejo de estado activo / inactivo
        if ($request->has('activo')) {
            $validated['activo'] = $request->boolean('activo');
        } elseif ($request->has('status')) {
            $validated['activo'] = $request->input('status') === 'active' || $request->input('status') == '1';
        }

        unset($validated['status']);

        $trabajador->update($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Información del trabajador actualizada exitosamente.',
                'data' => $trabajador->fresh(),
            ]);
        }

        return redirect()
            ->route('admin.trabajadores.index')
            ->with('success', 'Información del trabajador actualizada exitosamente.');
    }

    /**
     * Obtiene los servicios asociados y activos a un trabajador (Subtarea 7).
     */
    public function getServices(Trabajador $trabajador)
    {
        $serviciosActivos = $trabajador->servicios()
            ->where('services.is_active', true)
            ->get();

        return response()->json([
            'success' => true,
            'trabajador_id' => $trabajador->id,
            'trabajador_nombre' => $trabajador->nombre_completo,
            'service_ids' => $serviciosActivos->pluck('id'),
            'servicios' => $serviciosActivos,
        ]);
    }

    /**
     * Asocia o sincroniza los servicios que realiza un trabajador (Subtarea 7).
     * Conserva los servicios inactivos previos en segundo plano para que se restablezcan
     * automáticamente al ser reactivados en el catálogo.
     */
    public function syncServices(Request $request, Trabajador $trabajador)
    {
        $validated = $request->validate([
            'servicios' => ['nullable', 'array'],
            'servicios.*' => ['integer', 'exists:services,id'],
        ], [
            'servicios.array' => 'El formato de los servicios debe ser una lista.',
            'servicios.*.exists' => 'Uno de los servicios seleccionados no existe en el catálogo.',
        ]);

        $selectedServiceIds = $validated['servicios'] ?? [];

        // Conservar las asociaciones a servicios inactivos que el especialista ya tenía,
        // para que si se reactivan en el catálogo en el futuro, sigan asignados sin requerir reconfiguración manual.
        $inactiveServiceIds = $trabajador->servicios()
            ->where('services.is_active', false)
            ->pluck('services.id')
            ->toArray();

        $allServiceIds = array_values(array_unique(array_merge($selectedServiceIds, $inactiveServiceIds)));
        $trabajador->servicios()->sync($allServiceIds);

        $serviciosActivos = $trabajador->fresh()->servicios()
            ->where('services.is_active', true)
            ->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Servicios asociados exitosamente al especialista.',
                'data' => [
                    'trabajador_id' => $trabajador->id,
                    'servicios_count' => $serviciosActivos->count(),
                    'servicios' => $serviciosActivos,
                ],
            ]);
        }

        return redirect()
            ->route('admin.trabajadores.index')
            ->with('success', 'Servicios asociados exitosamente al especialista.');
    }

    /**
     * Obtiene el horario configurado para un trabajador junto con los límites de la sucursal.
     */
    public function getSchedule(Trabajador $trabajador)
    {
        $branchLimits = HorarioSucursal::getHorariosMap();

        return response()->json([
            'success' => true,
            'trabajador_id' => $trabajador->id,
            'trabajador_nombre' => $trabajador->nombre_completo,
            'horario' => $trabajador->horario,
            'branch_limits' => $branchLimits,
        ]);
    }

    /**
     * Guarda y valida el horario asignado a un trabajador contra los límites de la sucursal día por día.
     */
    public function saveSchedule(Request $request, Trabajador $trabajador)
    {
        $branchLimits = HorarioSucursal::getHorariosMap();

        $validated = $request->validate([
            'horario' => ['required', 'array'],
        ], [
            'horario.required' => 'El horario del trabajador es obligatorio.',
            'horario.array' => 'El formato del horario no es válido.',
        ]);

        $horarioData = $validated['horario'];

        // Compara los horarios día por día contra la configuración de la sucursal
        foreach ($horarioData as $dayKey => $dayConfig) {
            if (!is_array($dayConfig) || empty($dayConfig['entrada']) || empty($dayConfig['salida'])) {
                continue;
            }

            $diaLower = strtolower(trim($dayKey));
            $branchDay = $branchLimits[$diaLower] ?? null;

            if (!$branchDay) {
                continue;
            }

            $nombreDia = $branchDay['nombre'] ?? ucfirst($diaLower);

            // Si la sucursal está cerrada este día
            if (!$branchDay['abierto']) {
                return response()->json([
                    'success' => false,
                    'message' => "La sucursal permanece cerrada el día {$nombreDia}. No se puede asignar horario al trabajador.",
                    'dia' => $nombreDia,
                ], 422);
            }

            $horaEntrada = trim($dayConfig['entrada']);
            $horaSalida = trim($dayConfig['salida']);

            if ($horaEntrada >= $horaSalida) {
                return response()->json([
                    'success' => false,
                    'message' => "La hora de entrada debe ser anterior a la hora de salida para el {$nombreDia}.",
                    'dia' => $nombreDia,
                ], 422);
            }

            $branchApertura = $branchDay['apertura'];
            $branchCierre = $branchDay['cierre'];

            // Si la entrada es más temprana que la apertura o la salida excede el cierre de la sucursal
            if ($horaEntrada < $branchApertura || $horaSalida > $branchCierre) {
                return response()->json([
                    'success' => false,
                    'message' => "El horario asignado para el {$nombreDia} excede el horario de la sucursal ({$branchApertura} - {$branchCierre})",
                    'dia' => $nombreDia,
                    'branch_limits' => [
                        'apertura' => $branchApertura,
                        'cierre' => $branchCierre,
                    ],
                ], 422);
            }
        }

        // Asegurar que la columna 'horario' exista de forma resiliente
        if (!Schema::hasColumn('trabajadores', 'horario')) {
            Schema::table('trabajadores', function ($table) {
                $table->json('horario')->nullable();
            });
        }

        $trabajador->horario = $horarioData;
        $trabajador->save();

        $freshTrabajador = $trabajador->fresh();
        $estadoTexto = $freshTrabajador->getEstadoDisponibilidadHoy();
        $estadoHoy = $freshTrabajador->estaLaborandoHoy();

        return response()->json([
            'success' => true,
            'message' => "Horario de {$trabajador->nombre_completo} configurado correctamente.",
            'data' => $freshTrabajador->horario,
            'trabajador_id' => $trabajador->id,
            'trabajador_nombre' => $trabajador->nombre_completo,
            'activo' => (bool)$freshTrabajador->activo,
            'laborando_hoy' => ($estadoTexto === 'Con disponibilidad de horario'),
            'disponible' => ($estadoTexto === 'Con disponibilidad de horario'),
            'estado_hoy' => $estadoTexto,
            'motivo_hoy' => $estadoTexto,
        ]);
    }

    /**
     * Consulta la disponibilidad de horarios de un trabajador para una fecha dada,
     * aplicando con prioridad 1 las Fechas Especiales (Caso A y Caso B) y prioridad 2 el horario del trabajador.
     */
    public function getDisponibilidad(Request $request, Trabajador $trabajador)
    {
        $request->validate([
            'fecha' => ['required', 'date'],
            'duracion' => ['nullable', 'integer', 'min:5', 'max:480'],
        ], [
            'fecha.required' => 'La fecha es obligatoria.',
            'fecha.date' => 'La fecha no tiene un formato válido.',
        ]);

        $fecha = $request->input('fecha');
        $duracion = (int) $request->input('duracion', 30);

        $disponibilidad = app(\App\Services\DisponibilidadService::class)
            ->obtenerDisponibilidadCompleta($trabajador, $fecha, $duracion);

        return response()->json([
            'success' => true,
            'trabajador_id' => $trabajador->id,
            'trabajador_nombre' => $trabajador->nombre_completo,
            'data' => $disponibilidad,
        ]);
    }

    /**
     * Alterna el estado activo / inactivo (bloqueo general de disponibilidad) de un trabajador.
     */
    public function toggleStatus(Request $request, Trabajador $trabajador)
    {
        $trabajador->activo = !$trabajador->activo;
        $trabajador->save();

        $freshTrabajador = $trabajador->fresh();
        $estadoTexto = $freshTrabajador->getEstadoDisponibilidadHoy();
        $estadoTextoGeneral = $freshTrabajador->activo ? 'activado' : 'desactivado (disponibilidad bloqueada)';
        $mensaje = "Especialista {$freshTrabajador->nombre_completo} {$estadoTextoGeneral} exitosamente.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $mensaje,
                'activo' => (bool)$freshTrabajador->activo,
                'status' => $freshTrabajador->activo ? 'active' : 'inactive',
                'estado_hoy' => $estadoTexto,
                'disponible' => ($estadoTexto === 'Con disponibilidad de horario'),
                'laborando_hoy' => ($estadoTexto === 'Con disponibilidad de horario'),
            ]);
        }

        return redirect()
            ->route('admin.trabajadores.index')
            ->with('success', $mensaje);
    }

    /**
     * Elimina a un trabajador del sistema (Subtarea 4).
     */
    public function destroy(Request $request, Trabajador $trabajador)
    {
        $nombre = $trabajador->nombre_completo;

        // 1. Eliminar fotografía del almacenamiento si existe
        if ($trabajador->fotografia) {
            Storage::disk('public')->delete($trabajador->fotografia);
        }

        // 2. Desasociar servicios de la tabla pivote de manera segura
        $trabajador->servicios()->detach();

        // 3. Eliminar el registro del trabajador
        $trabajador->delete();

        $mensaje = "El especialista \"{$nombre}\" ha sido eliminado exitosamente.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $mensaje,
                'id' => $trabajador->id,
            ]);
        }

        return redirect()
            ->route('admin.trabajadores.index')
            ->with('success', $mensaje);
    }
}

