<?php

namespace App\Services;

use App\Models\Cita;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class IngresosService
{
    /**
     * Obtiene la zona horaria oficial del negocio.
     */
    public function getBusinessTimezone(): string
    {
        $tz = config('app.timezone') ?: 'America/Mexico_City';
        if ($tz === 'UTC') {
            $tz = env('APP_TIMEZONE', 'America/Mexico_City');
        }
        return $tz;
    }

    /**
     * Construye la consulta base para ingresos efectivos.
     * Criterios estrictos de la regla de oro:
     * - estado_pago = 'pagado'
     * - estado != 'cancelada' (excluye citas canceladas/anuladas)
     */
    public function obtenerQueryIngresosEfectivos(): Builder
    {
        Cita::ensureTableExists();

        return Cita::query()
            ->where('estado_pago', 'pagado')
            ->whereNotIn('estado', ['cancelada', 'cancelled', 'anulada']);
    }

    /**
     * Construye la consulta base para cuentas pendientes de cobro (deudas activas):
     * - estado_pago = 'pendiente' (o distinto de 'pagado')
     * - estado != 'cancelada'
     */
    public function obtenerQueryPendientesCobro(): Builder
    {
        Cita::ensureTableExists();

        return Cita::query()
            ->where('estado_pago', '!=', 'pagado')
            ->whereNotIn('estado', ['cancelada', 'cancelled', 'anulada']);
    }

    /**
     * Calcula los ingresos correspondientes a un día específico (por defecto, hoy en zona horaria del negocio).
     */
    public function calcularIngresosDia(?string $fecha = null): array
    {
        $tz = $this->getBusinessTimezone();
        $fechaConsulta = $fecha ?: Carbon::now($tz)->format('Y-m-d');

        $queryPagados = $this->obtenerQueryIngresosEfectivos()
            ->whereDate('fecha', $fechaConsulta);

        $totalIngresos = (float) $queryPagados->sum('precio');
        $cantidadCitasPagadas = $queryPagados->count();

        $queryPendientes = $this->obtenerQueryPendientesCobro()
            ->whereDate('fecha', $fechaConsulta);

        $totalPendiente = (float) $queryPendientes->sum('precio');
        $cantidadCitasPendientes = $queryPendientes->count();

        return [
            'fecha' => $fechaConsulta,
            'total_ingresos' => $totalIngresos,
            'total_ingresos_formateado' => '$' . number_format($totalIngresos, 2),
            'total_ingresos_mxn' => '$' . number_format($totalIngresos, 2) . ' MXN',
            'citas_pagadas_count' => $cantidadCitasPagadas,
            'total_pendiente' => $totalPendiente,
            'total_pendiente_formateado' => '$' . number_format($totalPendiente, 2),
            'total_pendiente_mxn' => '$' . number_format($totalPendiente, 2) . ' MXN',
            'citas_pendientes_count' => $cantidadCitasPendientes,
        ];
    }

    /**
     * Calcula los ingresos acumulados del mes correspondiente (por defecto, mes y año actual en zona horaria del negocio).
     */
    public function calcularIngresosMes(?int $anio = null, ?int $mes = null): array
    {
        $tz = $this->getBusinessTimezone();
        $now = Carbon::now($tz);

        $anioConsulta = $anio ?: (int) $now->year;
        $mesConsulta = $mes ?: (int) $now->month;

        $inicioMes = Carbon::createFromDate($anioConsulta, $mesConsulta, 1, $tz)->startOfMonth()->format('Y-m-d');
        $finMes = Carbon::createFromDate($anioConsulta, $mesConsulta, 1, $tz)->endOfMonth()->format('Y-m-d');

        $queryPagados = $this->obtenerQueryIngresosEfectivos()
            ->whereBetween('fecha', [$inicioMes, $finMes]);

        $totalIngresos = (float) $queryPagados->sum('precio');
        $cantidadCitasPagadas = $queryPagados->count();

        $queryPendientes = $this->obtenerQueryPendientesCobro()
            ->whereBetween('fecha', [$inicioMes, $finMes]);

        $totalPendiente = (float) $queryPendientes->sum('precio');
        $cantidadCitasPendientes = $queryPendientes->count();

        $mesesEsp = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];

        return [
            'anio' => $anioConsulta,
            'mes' => $mesConsulta,
            'mes_nombre' => $mesesEsp[$mesConsulta] ?? 'Mes ' . $mesConsulta,
            'inicio_mes' => $inicioMes,
            'fin_mes' => $finMes,
            'total_ingresos' => $totalIngresos,
            'total_ingresos_formateado' => '$' . number_format($totalIngresos, 2),
            'total_ingresos_mxn' => '$' . number_format($totalIngresos, 2) . ' MXN',
            'citas_pagadas_count' => $cantidadCitasPagadas,
            'total_pendiente' => $totalPendiente,
            'total_pendiente_formateado' => '$' . number_format($totalPendiente, 2),
            'total_pendiente_mxn' => '$' . number_format($totalPendiente, 2) . ' MXN',
            'citas_pendientes_count' => $cantidadCitasPendientes,
        ];
    }

    /**
     * Obtiene el resumen consolidado del corte de caja diario y mensual.
     */
    public function obtenerResumenCorteCaja(?string $fecha = null): array
    {
        $tz = $this->getBusinessTimezone();
        $fechaCarbon = $fecha ? Carbon::parse($fecha, $tz) : Carbon::now($tz);

        $corteDia = $this->calcularIngresosDia($fechaCarbon->format('Y-m-d'));
        $corteMes = $this->calcularIngresosMes((int)$fechaCarbon->year, (int)$fechaCarbon->month);

        return [
            'dia' => $corteDia,
            'mes' => $corteMes,
            'zona_horaria' => $tz,
        ];
    }
}
