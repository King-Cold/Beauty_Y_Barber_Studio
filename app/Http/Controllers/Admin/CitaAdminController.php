<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\Service;
use App\Models\Trabajador;
use App\Services\CitaCalendarioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class CitaAdminController extends Controller
{
    /**
     * Muestra la vista de Citas (Calendario y Listado) para el Administrador.
     */
    public function index(Request $request, CitaCalendarioService $calendarioService)
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

        return view('admin.citas', compact('user', 'citas', 'trabajadores', 'servicios', 'filtros'));
    }

    /**
     * Alias de la vista de calendario para el Administrador.
     */
    public function calendario(Request $request, CitaCalendarioService $calendarioService)
    {
        return $this->index($request, $calendarioService);
    }

    /**
     * API AJAX para consultar citas del calendario según periodo y filtros (Admin).
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
     * API AJAX para obtener el detalle de una cita específica (Admin).
     */
    public function detalleCita(mixed $cita, CitaCalendarioService $calendarioService)
    {
        $citaModel = $cita instanceof Cita ? $cita : Cita::findOrFail($cita);
        $detalle = $calendarioService->obtenerDetalleCita($citaModel);

        return response()->json([
            'success' => true,
            'cita' => $detalle,
        ]);
    }

    /**
     * El Administrador cuenta exclusivamente con permisos de visualización y consulta.
     * Cualquier intento de mutación sobre citas es denegado.
     */
    public function actualizarEstadoCita(Request $request, Cita $cita)
    {
        return response()->json([
            'success' => false,
            'error' => 'El administrador cuenta únicamente con permisos de visualización y consulta.',
            'mensaje' => 'El administrador cuenta únicamente con permisos de visualización y consulta.',
        ], 403);
    }

    public function actualizarCita(Request $request, Cita $cita)
    {
        return response()->json([
            'success' => false,
            'error' => 'El administrador cuenta únicamente con permisos de visualización y consulta.',
            'mensaje' => 'El administrador cuenta únicamente con permisos de visualización y consulta.',
        ], 403);
    }

    public function cancelarCita(Request $request, Cita $cita)
    {
        return response()->json([
            'success' => false,
            'error' => 'El administrador cuenta únicamente con permisos de visualización y consulta.',
            'mensaje' => 'El administrador cuenta únicamente con permisos de visualización y consulta.',
        ], 403);
    }

    public function confirmarCita(Request $request, Cita $cita)
    {
        return response()->json([
            'success' => false,
            'error' => 'El administrador cuenta únicamente con permisos de visualización y consulta.',
            'mensaje' => 'El administrador cuenta únicamente con permisos de visualización y consulta.',
        ], 403);
    }

    public function checkInCita(Request $request, Cita $cita)
    {
        return response()->json([
            'success' => false,
            'error' => 'El administrador cuenta únicamente con permisos de visualización y consulta.',
            'mensaje' => 'El administrador cuenta únicamente con permisos de visualización y consulta.',
        ], 403);
    }
}
