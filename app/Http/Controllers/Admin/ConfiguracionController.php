<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FechaEspecial;
use App\Models\HorarioSucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

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

        $validated = $request->validate([
            'fecha' => ['required', 'date', 'after_or_equal:today', 'unique:fechas_especiales,fecha'],
            'motivo' => ['required', 'string', 'max:255'],
            'cerrado_todo_el_dia' => ['boolean'],
            'hora_apertura' => ['nullable', 'string', 'max:10'],
            'hora_cierre' => ['nullable', 'string', 'max:10'],
        ], [
            'fecha.required' => 'La fecha es obligatoria.',
            'fecha.date' => 'La fecha ingresada no tiene un formato válido.',
            'fecha.after_or_equal' => 'No puedes registrar fechas especiales en días o años pasados.',
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

        return response()->json([
            'success' => true,
            'message' => '¡Horarios de la sucursal actualizados exitosamente!',
            'data' => HorarioSucursal::getHorariosMap(),
        ]);
    }
}
