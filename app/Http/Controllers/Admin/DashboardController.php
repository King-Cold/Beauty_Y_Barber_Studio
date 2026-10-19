<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Trabajador;
use App\Models\Service;
use App\Models\Cita;
use App\Services\IngresosService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    /**
     * Muestra el dashboard administrativo con los conteos reales de la base de datos.
     */
    public function index(IngresosService $ingresosService)
    {
        // Asegurar estructura de citas si aplica
        if (class_exists(Cita::class) && method_exists(Cita::class, 'ensureTableExists')) {
            Cita::ensureTableExists();
        }

        // 1. Conteo de Clientes: Usuarios registrados exclusivamente con rol Cliente (Rol 4)
        // Evitando contabilizar administradores, recepcionistas ni trabajadores
        $clienteRoleIds = Role::whereRaw('LOWER(nombre) = ?', ['cliente'])->pluck('id')->toArray();
        if (empty($clienteRoleIds)) {
            $clienteRoleIds = [4];
        } elseif (!in_array(4, $clienteRoleIds)) {
            $clienteRoleIds[] = 4;
        }

        $totalClientes = User::whereIn('role_id', $clienteRoleIds)
            ->whereNotIn('role_id', [1, 2, 3])
            ->distinct()
            ->count('id');

        // 2. Conteo de Trabajadores: Plantilla registrada en la tabla 'trabajadores',
        // siguiendo el mismo criterio que emplea TrabajadorController@index.
        // No contabiliza cuentas que únicamente tengan el rol trabajador si no cuentan con ficha en 'trabajadores'.
        // Excluye registros vinculados a usuarios de otros roles (Admin 1, Recepcionista 2, Cliente 4).
        // Conserva la contabilización de fichas históricas sin user_id.
        if (class_exists(Trabajador::class) && Schema::hasTable('trabajadores')) {
            if (Schema::hasColumn('trabajadores', 'user_id')) {
                $totalTrabajadores = Trabajador::where(function ($query) {
                    $query->whereNull('user_id')
                          ->orWhereDoesntHave('user', function ($q) {
                              $q->whereIn('role_id', [1, 2, 4]);
                          });
                })->count();
            } else {
                $totalTrabajadores = Trabajador::count();
            }
        } else {
            $totalTrabajadores = 0;
        }

        // 3. Conteo de Servicios: Servicios disponibles y activos dentro del catálogo
        $totalServicios = Service::where('is_active', true)->count();

        // 4. Conteo de Citas: Total de reservaciones almacenadas en la base de datos (independientemente de su estado)
        $totalCitas = Cita::count();

        // =========================================================================
        // SUBTAREA 3: Clasificación de citas según su estado
        // (completada, cancelada y vencida)
        // - Directo de la base de datos, sin duplicidades.
        // - Estrictamente independiente de estado_pago.
        // =========================================================================

        // 5. Citas Completadas: Citas finalizadas exitosamente (estado = 'completada')
        $citasCompletadas = Cita::where('estado', 'completada')->count();

        // 6. Citas Canceladas: Citas formalmente anuladas o canceladas (estado = 'cancelada')
        $citasCanceladas = Cita::where('estado', 'cancelada')->count();

        // 7. Citas Vencidas:
        // Citas con estado explícito 'vencida' en base de datos o aquellas en 'confirmada'
        // cuya fecha y horario de finalización ya han transcurrido en la zona horaria del establecimiento (Mérida).
        // Idéntica a la lógica implementada en Cita::getEstadoVisualAttribute() y CitaCalendarioService.
        $tz = 'America/Merida';
        $now = \Carbon\Carbon::now($tz);
        $today = $now->format('Y-m-d');
        $currentTime = $now->format('H:i');

        $citasVencidas = Cita::where(function ($q) use ($today, $currentTime) {
            $q->where('estado', 'vencida')
              ->orWhere(function ($sub) use ($today, $currentTime) {
                  $sub->where('estado', 'confirmada')
                      ->where(function ($dateQ) use ($today, $currentTime) {
                          $dateQ->whereDate('fecha', '<', $today)
                                ->orWhere(function ($timeQ) use ($today, $currentTime) {
                                    $timeQ->whereDate('fecha', $today)
                                          ->where('hora_fin', '<', $currentTime);
                                });
                      });
              });
        })->count();

        // =========================================================================
        // SUBTAREA 4: Integración de ingresos y rankings de demanda
        // =========================================================================

        // 8. Ingresos del Día y del Mes: Reutiliza el motor financiero existente (IngresosService)
        // Mantiene la regla estricta: estado_pago = 'pagado' y estado != 'cancelada'
        // Utiliza el precio histórico registrado en cada cita
        $ingresosDia = $ingresosService->calcularIngresosDia();
        $ingresosMes = $ingresosService->calcularIngresosMes();

        // 9. Ranking de Servicios Más Solicitados:
        // - Excluye exclusivamente el estado 'cancelada'
        // - Ordena descendente por cantidad de citas y criterio secundario alfabético
        $serviciosMasSolicitados = Service::whereHas('citas', function ($q) {
                $q->where('estado', '!=', 'cancelada');
            })
            ->withCount(['citas' => function ($q) {
                $q->where('estado', '!=', 'cancelada');
            }])
            ->orderByDesc('citas_count')
            ->orderBy('name', 'asc')
            ->take(5)
            ->get()
            ->map(function ($servicio) {
                return (object) [
                    'id' => $servicio->id,
                    'nombre' => $servicio->name,
                    'categoria' => $servicio->category,
                    'total_citas' => $servicio->citas_count,
                ];
            });

        // 10. Ranking de Trabajadores con Mayor Cantidad de Citas:
        // - Exclusivamente barberos y estilistas de la plantilla
        // - Excluye citas con estado 'cancelada'
        // - Determina la función/especialidad a partir de sus servicios asignados (sin inventar especialidades)
        // - Criterio secundario de desempate estable
        $trabajadoresConMasCitas = Trabajador::where(function ($query) {
                $query->whereNull('user_id')
                      ->orWhereDoesntHave('user', function ($q) {
                          $q->whereIn('role_id', [1, 2, 4]);
                      });
            })
            ->whereHas('citas', function ($q) {
                $q->where('estado', '!=', 'cancelada');
            })
            ->withCount(['citas' => function ($q) {
                $q->where('estado', '!=', 'cancelada');
            }])
            ->with(['user', 'servicios'])
            ->orderByDesc('citas_count')
            ->orderBy('id', 'asc')
            ->take(5)
            ->get()
            ->map(function ($trabajador) {
                $cats = $trabajador->servicios->pluck('category')->map(fn($c) => strtolower(trim((string)$c)))->unique();
                $tieneBarberia = $cats->contains('barberia');
                $tieneEstetica = $cats->contains('estetica');

                if ($tieneBarberia && $tieneEstetica) {
                    $funcion = 'Barbero / Estilista';
                } elseif ($tieneBarberia) {
                    $funcion = 'Barbero';
                } elseif ($tieneEstetica) {
                    $funcion = 'Estilista';
                } elseif ($trabajador->servicios->isNotEmpty()) {
                    $funcion = ucfirst($cats->first());
                } else {
                    $funcion = 'Especialidad no asignada';
                }

                return (object) [
                    'id' => $trabajador->id,
                    'nombre' => $trabajador->nombre_completo,
                    'funcion' => $funcion,
                    'total_citas' => $trabajador->citas_count,
                ];
            });

        return view('admin.dashboard', compact(
            'totalClientes',
            'totalTrabajadores',
            'totalServicios',
            'totalCitas',
            'citasCompletadas',
            'citasCanceladas',
            'citasVencidas',
            'ingresosDia',
            'ingresosMes',
            'serviciosMasSolicitados',
            'trabajadoresConMasCitas'
        ));
    }
}
