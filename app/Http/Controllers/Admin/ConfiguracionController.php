<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FechaEspecial;
use App\Models\HorarioSucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class ConfiguracionController extends Controller
{
    /**
     * Muestra la vista de Configuración General con horarios de sucursal y fechas especiales.
     */
    public function index()
    {
        FechaEspecial::ensureTableExists();
        HorarioSucursal::ensureTableExists();

        $fechasEspeciales = FechaEspecial::orderBy('fecha', 'asc')->get();
        $horariosSucursal = HorarioSucursal::getHorariosMap();

        return view('admin.configuracion', compact('fechasEspeciales', 'horariosSucursal'));
    }

    /**
     * Devuelve el listado de fechas especiales en formato JSON.
     */
    public function getFechasEspeciales()
    {
        FechaEspecial::ensureTableExists();
        $fechas = FechaEspecial::orderBy('fecha', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $fechas,
        ]);
    }

    /**
     * Registra una nueva fecha especial o excepción en la base de datos.
     */
    public function storeFechaEspecial(Request $request)
    {
        FechaEspecial::ensureTableExists();

        $request->merge([
            'cerrado_todo_el_dia' => $request->boolean('cerrado_todo_el_dia'),
        ]);

        $currentYear = date('Y');
        $validated = $request->validate([
            'fecha' => ['required', 'date', 'after_or_equal:today', 'before_or_equal:' . $currentYear . '-12-31', 'unique:fechas_especiales,fecha'],
            'motivo' => ['required', 'string', 'max:255'],
            'cerrado_todo_el_dia' => ['boolean'],
            'hora_apertura' => ['nullable', 'string', 'max:10'],
            'hora_cierre' => ['nullable', 'string', 'max:10'],
        ], [
            'fecha.required' => 'La fecha es obligatoria.',
            'fecha.date' => 'La fecha ingresada no tiene un formato válido.',
            'fecha.after_or_equal' => 'No puedes registrar fechas especiales en días pasados.',
            'fecha.before_or_equal' => 'Solo se admiten fechas correspondientes al año en curso (' . $currentYear . ').',
            'fecha.unique' => 'Ya existe una fecha especial registrada para este día.',
            'motivo.required' => 'El motivo es obligatorio (ej. Navidad, Día Festivo).',
        ]);

        $cerrado = (bool) $validated['cerrado_todo_el_dia'];

        if ($cerrado) {
            $validated['hora_apertura'] = null;
            $validated['hora_cierre'] = null;
        } else {
            if (empty($validated['hora_apertura']) || empty($validated['hora_cierre'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Si el estudio no está cerrado todo el día, debes indicar la hora de apertura y de cierre.',
                ], 422);
            }

            if ($validated['hora_apertura'] >= $validated['hora_cierre']) {
                return response()->json([
                    'success' => false,
                    'message' => 'La hora de apertura debe ser anterior a la hora de cierre.',
                ], 422);
            }
        }

        $fechaEspecial = FechaEspecial::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Fecha especial registrada exitosamente.',
            'data' => [
                'id' => $fechaEspecial->id,
                'fecha' => $fechaEspecial->fecha->format('Y-m-d'),
                'fecha_legible' => $fechaEspecial->fecha_legible,
                'motivo' => $fechaEspecial->motivo,
                'cerrado_todo_el_dia' => $fechaEspecial->cerrado_todo_el_dia,
                'horario_formateado' => $fechaEspecial->horario_formateado,
                'hora_apertura' => $fechaEspecial->hora_apertura,
                'hora_cierre' => $fechaEspecial->hora_cierre,
            ],
        ], 201);
    }

    /**
     * Actualiza una fecha especial ya registrada (fecha, horario, motivo o cerrado).
     */
    public function updateFechaEspecial(Request $request, FechaEspecial $fechaEspecial)
    {
        $request->merge([
            'cerrado_todo_el_dia' => $request->boolean('cerrado_todo_el_dia'),
        ]);

        $currentYear = date('Y');
        $validated = $request->validate([
            'fecha' => [
                'required',
                'date',
                'after_or_equal:today',
                'before_or_equal:' . $currentYear . '-12-31',
                Rule::unique('fechas_especiales', 'fecha')->ignore($fechaEspecial->id),
            ],
            'motivo' => ['required', 'string', 'max:255'],
            'cerrado_todo_el_dia' => ['boolean'],
            'hora_apertura' => ['nullable', 'string', 'max:10'],
            'hora_cierre' => ['nullable', 'string', 'max:10'],
        ], [
            'fecha.required' => 'La fecha es obligatoria.',
            'fecha.date' => 'La fecha ingresada no tiene un formato válido.',
            'fecha.after_or_equal' => 'No puedes asignar fechas especiales en días pasados.',
            'fecha.before_or_equal' => 'Solo se admiten fechas correspondientes al año en curso (' . $currentYear . ').',
            'fecha.unique' => 'Ya existe otra fecha especial registrada para ese día.',
            'motivo.required' => 'El motivo es obligatorio.',
        ]);

        $cerrado = (bool) $validated['cerrado_todo_el_dia'];

        if ($cerrado) {
            $validated['hora_apertura'] = null;
            $validated['hora_cierre'] = null;
        } else {
            if (empty($validated['hora_apertura']) || empty($validated['hora_cierre'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Si el estudio no está cerrado todo el día, debes indicar la hora de apertura y de cierre.',
                ], 422);
            }

            if ($validated['hora_apertura'] >= $validated['hora_cierre']) {
                return response()->json([
                    'success' => false,
                    'message' => 'La hora de apertura debe ser anterior a la hora de cierre.',
                ], 422);
            }
        }

        $fechaEspecial->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Fecha especial actualizada exitosamente.',
            'data' => [
                'id' => $fechaEspecial->id,
                'fecha' => $fechaEspecial->fecha ? Carbon::parse($fechaEspecial->fecha)->format('Y-m-d') : '',
                'fecha_legible' => $fechaEspecial->fecha_legible,
                'motivo' => $fechaEspecial->motivo,
                'cerrado_todo_el_dia' => (bool)$fechaEspecial->cerrado_todo_el_dia,
                'horario_formateado' => $fechaEspecial->horario_formateado,
                'hora_apertura' => $fechaEspecial->hora_apertura ? substr((string)$fechaEspecial->hora_apertura, 0, 5) : '',
                'hora_cierre' => $fechaEspecial->hora_cierre ? substr((string)$fechaEspecial->hora_cierre, 0, 5) : '',
            ],
        ]);
    }

    /**
     * Elimina una fecha especial registrada.
     */
    public function destroyFechaEspecial(FechaEspecial $fechaEspecial)
    {
        $fechaEspecial->delete();

        return response()->json([
            'success' => true,
            'message' => 'Fecha especial eliminada correctamente.',
        ]);
    }

    /**
     * Guarda / actualiza la configuración global de horarios de la sucursal.
     */
    public function saveBranchSchedule(Request $request)
    {
        HorarioSucursal::ensureTableExists();

        $diasConfig = $request->input('dias', []);
        $diasPermitidos = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];

        foreach ($diasConfig as $diaKey => $data) {
            $diaKey = strtolower(trim($diaKey));
            if (!in_array($diaKey, $diasPermitidos, true)) {
                continue;
            }

            $abierto = isset($data['abierto']) ? filter_var($data['abierto'], FILTER_VALIDATE_BOOLEAN) : true;
            $apertura = $data['apertura'] ?? '09:00';
            $cierre = $data['cierre'] ?? '20:00';

            $variantes = [$diaKey];
            if ($diaKey === 'miercoles') $variantes[] = 'miércoles';
            if ($diaKey === 'sabado') $variantes[] = 'sábado';

            // Actualizar cualquier registro existente con variantes de tilde a la clave canónica
            HorarioSucursal::whereIn('dia', $variantes)->update([
                'dia' => $diaKey,
                'abierto' => $abierto,
                'apertura' => $apertura,
                'cierre' => $cierre,
            ]);

            HorarioSucursal::updateOrCreate(
                ['dia' => $diaKey],
                [
                    'abierto' => $abierto,
                    'apertura' => $apertura,
                    'cierre' => $cierre,
                ]
            );
        }

        $nuevoHorarioSucursal = HorarioSucursal::getHorariosMap();

        // =========================================================================
        // AUTOMATIZACIÓN EN CASCADA: Actualizar automáticamente el horario de todos
        // los trabajadores para que se adapte inmediatamente a los nuevos límites
        // de la sucursal sin requerir guardado manual.
        // =========================================================================
        $trabajadores = \App\Models\Trabajador::all();
        foreach ($trabajadores as $trabajador) {
            $horarioTrabajador = $trabajador->horario ?? [];
            if (!is_array($horarioTrabajador)) {
                $horarioTrabajador = [];
            }

            $modificado = false;

            // Procesar los días habituales laborales (Lunes a Viernes)
            foreach (['lunes', 'martes', 'miercoles', 'jueves', 'viernes'] as $dKey) {
                $branchDay = $nuevoHorarioSucursal[$dKey] ?? null;
                $branchAbierto = $branchDay ? (bool)$branchDay['abierto'] : false;
                $branchApertura = $branchDay['apertura'] ?? '09:00';
                $branchCierre = $branchDay['cierre'] ?? '20:00';

                // Si el trabajador ya tenía turno configurado para este día
                if (isset($horarioTrabajador[$dKey]) && is_array($horarioTrabajador[$dKey])) {
                    $entrada = $horarioTrabajador[$dKey]['entrada'] ?? $branchApertura;
                    $salida = $horarioTrabajador[$dKey]['salida'] ?? $branchCierre;

                    // Ajustar límites: si la entrada es menor que la apertura, elevar a la apertura
                    if ($entrada < $branchApertura) {
                        $entrada = $branchApertura;
                        $modificado = true;
                    }

                    // Si la salida excede el cierre, recortar al cierre
                    if ($salida > $branchCierre) {
                        $salida = $branchCierre;
                        $modificado = true;
                    }

                    // Si la sucursal abre más tarde que la salida o cierra más temprano que la entrada
                    if ($entrada >= $salida) {
                        $entrada = $branchApertura;
                        $salida = $branchCierre;
                        $modificado = true;
                    }

                    $horarioTrabajador[$dKey] = [
                        'entrada' => $entrada,
                        'salida' => $salida,
                    ];
                } else if ($branchAbierto) {
                    // Si el trabajador no tenía configurado el día pero la sucursal está abierta,
                    // asignarle el turno dentro de los límites operativos por defecto
                    $horarioTrabajador[$dKey] = [
                        'entrada' => $branchApertura,
                        'salida' => $branchCierre,
                    ];
                    $modificado = true;
                }
            }

            if ($modificado || empty($trabajador->horario)) {
                $trabajador->horario = $horarioTrabajador;
                $trabajador->save();
            }
        }

        return response()->json([
            'success' => true,
            'message' => '¡Horarios de la sucursal y de los trabajadores actualizados exitosamente!',
            'data' => $nuevoHorarioSucursal,
        ]);
    }
}
