<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\Service;
use App\Models\Trabajador;
use App\Models\User;
use App\Services\IngresosService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Subtarea24_3_CalculoIngresosDiariosYMensualesTest extends TestCase
{
    use RefreshDatabase;

    protected User $recepcionistaUser;
    protected User $clienteUser;
    protected Trabajador $trabajador;
    protected Service $servicio;
    protected IngresosService $ingresosService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\TestUsersSeeder::class);

        $this->recepcionistaUser = User::where('role_id', 2)->first();
        $this->clienteUser = User::where('role_id', 4)->first();
        $trabajadorUser = User::where('role_id', 3)->first();

        $this->trabajador = Trabajador::create([
            'user_id' => $trabajadorUser->id,
            'direccion' => 'Calle Estudio 123',
            'experiencia' => 5,
            'activo' => true,
            'horario' => [
                'lunes' => ['entrada' => '09:00', 'salida' => '19:00'],
                'martes' => ['entrada' => '09:00', 'salida' => '19:00'],
            ],
        ]);

        $this->servicio = Service::create([
            'name' => 'Corte Barba Test',
            'duration_minutes' => 30,
            'price' => 300.00,
            'category' => 'barberia',
            'is_active' => true,
        ]);

        $this->trabajador->servicios()->attach($this->servicio->id);
        $this->ingresosService = app(IngresosService::class);
    }

    private function crearCita(array $datos): Cita
    {
        return Cita::create(array_merge([
            'user_id' => $this->clienteUser->id,
            'service_id' => $this->servicio->id,
            'trabajador_id' => $this->trabajador->id,
            'fecha' => Carbon::now($this->ingresosService->getBusinessTimezone())->format('Y-m-d'),
            'hora_inicio' => '10:00:00',
            'hora_fin' => '10:30:00',
            'duracion_minutos' => 30,
            'precio' => 300.00,
            'estado' => 'completada',
            'estado_pago' => 'pagado',
        ], $datos));
    }

    /**
     * CRITERIO 1: Regla estricta de agregación:
     * La sumatoria económica de ingresos debe considerar el campo precio exclusivamente de aquellas
     * citas que cumplan de manera simultánea: estado_pago = 'pagado' y estado != 'cancelada'.
     */
    public function test_criterio_1_regla_estricta_de_agregacion_suma_exclusivamente_pagadas_y_no_canceladas(): void
    {
        $hoy = Carbon::now($this->ingresosService->getBusinessTimezone())->format('Y-m-d');

        // Cita completada y pagada ($350)
        $this->crearCita([
            'precio' => 350.00,
            'estado' => 'completada',
            'estado_pago' => 'pagado',
            'fecha' => $hoy,
        ]);

        // Cita confirmada prepagada ($250)
        $this->crearCita([
            'precio' => 250.00,
            'estado' => 'confirmada',
            'estado_pago' => 'pagado',
            'fecha' => $hoy,
        ]);

        $resumenDia = $this->ingresosService->calcularIngresosDia($hoy);

        // Sumatoria estricta: 350 + 250 = 600
        $this->assertEquals(600.00, $resumenDia['total_ingresos']);
        $this->assertEquals('$600.00', $resumenDia['total_ingresos_formateado']);
        $this->assertEquals(2, $resumenDia['citas_pagadas_count']);
    }

    /**
     * CRITERIO 2: Regla de exclusión absoluta de pendientes:
     * Ninguna cita con estado_pago = 'pendiente' debe sumarse al total de ingresos,
     * independientemente de que su atención ya haya sido finalizada físicamente en el local.
     */
    public function test_criterio_2_regla_de_exclusion_absoluta_de_pendientes_no_suma_a_ingresos_aunque_este_completada(): void
    {
        $hoy = Carbon::now($this->ingresosService->getBusinessTimezone())->format('Y-m-d');

        // 1. Cita pagada legítima ($300)
        $this->crearCita([
            'precio' => 300.00,
            'estado' => 'completada',
            'estado_pago' => 'pagado',
            'fecha' => $hoy,
        ]);

        // 2. Cita físicamente finalizada en el local (completada) pero con pago PENDIENTE ($800)
        $this->crearCita([
            'precio' => 800.00,
            'estado' => 'completada',
            'estado_pago' => 'pendiente',
            'fecha' => $hoy,
        ]);

        // 3. Cita en atención con pago pendiente ($450)
        $this->crearCita([
            'precio' => 450.00,
            'estado' => 'en_atencion',
            'estado_pago' => 'pendiente',
            'fecha' => $hoy,
        ]);

        $resumenDia = $this->ingresosService->calcularIngresosDia($hoy);

        // El total de ingresos NO debe incluir los 800 ni los 450 de las pendientes: solo los 300
        $this->assertEquals(300.00, $resumenDia['total_ingresos']);
        $this->assertEquals(1, $resumenDia['citas_pagadas_count']);

        // Las pendientes se contabilizan por separado como saldo por cobrar: 800 + 450 = 1250
        $this->assertEquals(1250.00, $resumenDia['total_pendiente']);
        $this->assertEquals(2, $resumenDia['citas_pendientes_count']);
    }

    /**
     * CRITERIO 3: Exclusión de citas canceladas:
     * Las citas con estado = 'cancelada' quedan totalmente excluidas del cálculo de ingresos.
     */
    public function test_criterio_3_exclusion_de_citas_canceladas_quedan_totalmente_excluidas_del_calculo(): void
    {
        $hoy = Carbon::now($this->ingresosService->getBusinessTimezone())->format('Y-m-d');

        // 1. Cita completada y pagada válida ($200)
        $this->crearCita([
            'precio' => 200.00,
            'estado' => 'completada',
            'estado_pago' => 'pagado',
            'fecha' => $hoy,
        ]);

        // 2. Cita cancelada (incluso si tuviese residual pagado) ($600)
        $this->crearCita([
            'precio' => 600.00,
            'estado' => 'cancelada',
            'estado_pago' => 'pagado',
            'fecha' => $hoy,
        ]);

        // 3. Cita cancelada con pago pendiente ($500)
        $this->crearCita([
            'precio' => 500.00,
            'estado' => 'cancelada',
            'estado_pago' => 'pendiente',
            'fecha' => $hoy,
        ]);

        $resumenDia = $this->ingresosService->calcularIngresosDia($hoy);

        // Ninguna cancelada debe figurar en ingresos ni en pendientes por cobrar
        $this->assertEquals(200.00, $resumenDia['total_ingresos']);
        $this->assertEquals(1, $resumenDia['citas_pagadas_count']);
        $this->assertEquals(0.00, $resumenDia['total_pendiente']);
        $this->assertEquals(0, $resumenDia['citas_pendientes_count']);
    }

    /**
     * CA-24.3.2: Agregación mensual respeta el rango del mes en curso y excluye otros meses.
     */
    public function test_calculo_ingresos_mensuales_agrega_fechas_del_mes_en_curso(): void
    {
        $tz = $this->ingresosService->getBusinessTimezone();
        $now = Carbon::now($tz);

        $fechaMesActual = Carbon::createFromDate($now->year, $now->month, 10, $tz)->format('Y-m-d');
        $fechaMesAnterior = Carbon::createFromDate($now->year, $now->month, 1, $tz)->subMonth()->format('Y-m-d');
        $fechaMesSiguiente = Carbon::createFromDate($now->year, $now->month, 1, $tz)->addMonth()->format('Y-m-d');

        // Cita mes actual: $450 pagado
        $this->crearCita([
            'fecha' => $fechaMesActual,
            'precio' => 450.00,
            'estado_pago' => 'pagado',
            'estado' => 'completada',
        ]);

        // Cita mes anterior: $900 pagado (fuera del mes)
        $this->crearCita([
            'fecha' => $fechaMesAnterior,
            'precio' => 900.00,
            'estado_pago' => 'pagado',
            'estado' => 'completada',
        ]);

        // Cita mes siguiente: $1,200 pagado (fuera del mes)
        $this->crearCita([
            'fecha' => $fechaMesSiguiente,
            'precio' => 1200.00,
            'estado_pago' => 'pagado',
            'estado' => 'completada',
        ]);

        $resumenMes = $this->ingresosService->calcularIngresosMes($now->year, $now->month);

        $this->assertEquals(450.00, $resumenMes['total_ingresos']);
        $this->assertEquals('$450.00', $resumenMes['total_ingresos_formateado']);
        $this->assertEquals(1, $resumenMes['citas_pagadas_count']);
    }

    /**
     * CA-24.3.3: Integración del servicio de ingresos en el controlador de recepción.
     */
    public function test_recepcion_citas_expone_corte_de_caja_en_controlador_y_json(): void
    {
        $hoy = Carbon::now($this->ingresosService->getBusinessTimezone())->format('Y-m-d');

        $this->crearCita([
            'fecha' => $hoy,
            'precio' => 380.00,
            'estado_pago' => 'pagado',
            'estado' => 'completada',
        ]);

        // Petición HTML
        $response = $this->actingAs($this->recepcionistaUser)
            ->get(route('recepcion.citas'));

        $response->assertStatus(200);
        $response->assertViewHas('corteCaja');

        // Petición AJAX / JSON
        $responseJson = $this->actingAs($this->recepcionistaUser)
            ->getJson(route('recepcion.citas'));

        $responseJson->assertStatus(200);
        $responseJson->assertJsonPath('corte_caja.dia.total_ingresos', 380);
        $responseJson->assertJsonPath('corte_caja.dia.citas_pagadas_count', 1);
    }
}
