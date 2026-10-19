<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\Service;
use App\Models\Trabajador;
use App\Models\User;
use App\Services\IngresosService;
use Carbon\Carbon;
use Database\Seeders\TestUsersSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminDashboardSubtarea4Test extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $clienteUser;
    protected User $workerUser;
    protected Trabajador $trabajador1;
    protected Trabajador $trabajador2;
    protected Service $servicioCorte;
    protected Service $servicioBarba;
    protected IngresosService $ingresosService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(TestUsersSeeder::class);

        $this->adminUser = User::where('role_id', 1)->first();
        $this->clienteUser = User::where('role_id', 4)->first();
        $this->workerUser = User::where('role_id', 3)->first();

        $this->ingresosService = app(IngresosService::class);

        Cita::ensureTableExists();

        // Crear Trabajador 1 (Barbero)
        $this->trabajador1 = Trabajador::create([
            'user_id' => $this->workerUser->id,
            'direccion' => 'Calle 60 #100, Centro',
            'experiencia' => 5,
            'activo' => true,
        ]);

        // Crear Trabajador 2 (Estilista)
        $workerUser2 = User::create([
            'name' => 'Brenda',
            'apellidos' => 'Flores',
            'email' => 'brenda.estilista@test.com',
            'password' => bcrypt('password123'),
            'telefono' => '5559988771',
            'role_id' => 3,
        ]);

        $this->trabajador2 = Trabajador::create([
            'user_id' => $workerUser2->id,
            'direccion' => 'Calle 62 #200, Centro',
            'experiencia' => 3,
            'activo' => true,
        ]);

        // Servicios del catálogo
        $this->servicioCorte = Service::create([
            'name' => 'Corte Fade Clásico',
            'category' => 'barberia',
            'duration_minutes' => 30,
            'price' => 200.00,
            'is_active' => true,
        ]);

        $this->servicioBarba = Service::create([
            'name' => 'Afeitado Tradicional',
            'category' => 'barberia',
            'duration_minutes' => 30,
            'price' => 150.00,
            'is_active' => true,
        ]);

        // Asignar especialidades
        $this->trabajador1->servicios()->attach([$this->servicioCorte->id, $this->servicioBarba->id]);
    }

    /**
     * 1. Comprueba que el dashboard carga con las variables de ingresos y rankings.
     */
    public function test_administrador_ve_variables_de_ingresos_y_rankings_en_dashboard(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHasAll([
            'ingresosDia',
            'ingresosMes',
            'serviciosMasSolicitados',
            'trabajadoresConMasCitas',
        ]);

        $response->assertSee('Ingresos del Establecimiento');
        $response->assertSee('Ingresos por Citas de Hoy');
        $response->assertSee('Ingresos por Citas del Mes');
        $response->assertSee('Servicios Más Solicitados');
        $response->assertSee('Personal con Mayor Demanda');
    }

    /**
     * 2. Comprueba que con base de datos vacía muestra $0.00 MXN y rankings vacíos.
     */
    public function test_indicadores_muestran_cero_y_rankings_vacios_por_defecto(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('$0.00 MXN');
        $response->assertSee('No hay citas registradas para generar el ranking de servicios');
        $response->assertSee('No hay citas registradas para generar el ranking de trabajadores');
    }

    /**
     * 3. Comprueba que el cálculo de ingresos suma exclusivamente citas pagadas y no canceladas.
     */
    public function test_calculo_ingresos_suma_exclusivamente_citas_pagadas_y_no_canceladas(): void
    {
        $tz = $this->ingresosService->getBusinessTimezone();
        $hoy = Carbon::now($tz)->format('Y-m-d');

        // Cita 1: Pagada, completada ($200.00) -> DEBE SUMAR
        Cita::create([
            'user_id' => $this->clienteUser->id,
            'service_id' => $this->servicioCorte->id,
            'trabajador_id' => $this->trabajador1->id,
            'fecha' => $hoy,
            'hora_inicio' => '10:00:00',
            'hora_fin' => '10:30:00',
            'precio' => 200.00,
            'estado' => 'completada',
            'estado_pago' => 'pagado',
        ]);

        // Cita 2: Pendiente de pago ($150.00) -> NO DEBE SUMAR
        Cita::create([
            'user_id' => $this->clienteUser->id,
            'service_id' => $this->servicioBarba->id,
            'trabajador_id' => $this->trabajador1->id,
            'fecha' => $hoy,
            'hora_inicio' => '11:00:00',
            'hora_fin' => '11:30:00',
            'precio' => 150.00,
            'estado' => 'confirmada',
            'estado_pago' => 'pendiente',
        ]);

        // Cita 3: Cancelada con pago registrado ($200.00) -> NO DEBE SUMAR
        Cita::create([
            'user_id' => $this->clienteUser->id,
            'service_id' => $this->servicioCorte->id,
            'trabajador_id' => $this->trabajador1->id,
            'fecha' => $hoy,
            'hora_inicio' => '12:00:00',
            'hora_fin' => '12:30:00',
            'precio' => 200.00,
            'estado' => 'cancelada',
            'estado_pago' => 'pagado',
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $ingresosDia = $response->viewData('ingresosDia');
        $this->assertEquals(200.00, $ingresosDia['total_ingresos']);
        $this->assertEquals('$200.00 MXN', $ingresosDia['total_ingresos_mxn']);
        $this->assertEquals(1, $ingresosDia['citas_pagadas_count']);
    }

    /**
     * 4. Comprueba que el ranking de servicios agrupa, excluye canceladas y ordena descendente.
     */
    public function test_ranking_servicios_agrupa_excluye_canceladas_y_ordena_descendente(): void
    {
        $tz = $this->ingresosService->getBusinessTimezone();
        $hoy = Carbon::now($tz)->format('Y-m-d');

        // Servicio Barba: 2 citas válidas
        Cita::create([
            'user_id' => $this->clienteUser->id,
            'service_id' => $this->servicioBarba->id,
            'trabajador_id' => $this->trabajador1->id,
            'fecha' => $hoy,
            'hora_inicio' => '10:00:00',
            'hora_fin' => '10:30:00',
            'precio' => 150.00,
            'estado' => 'confirmada',
            'estado_pago' => 'pendiente',
        ]);
        Cita::create([
            'user_id' => $this->clienteUser->id,
            'service_id' => $this->servicioBarba->id,
            'trabajador_id' => $this->trabajador1->id,
            'fecha' => $hoy,
            'hora_inicio' => '11:00:00',
            'hora_fin' => '11:30:00',
            'precio' => 150.00,
            'estado' => 'completada',
            'estado_pago' => 'pagado',
        ]);

        // Servicio Corte: 1 cita válida y 1 cancelada (la cancelada no debe contar)
        Cita::create([
            'user_id' => $this->clienteUser->id,
            'service_id' => $this->servicioCorte->id,
            'trabajador_id' => $this->trabajador1->id,
            'fecha' => $hoy,
            'hora_inicio' => '12:00:00',
            'hora_fin' => '12:30:00',
            'precio' => 200.00,
            'estado' => 'confirmada',
            'estado_pago' => 'pendiente',
        ]);
        Cita::create([
            'user_id' => $this->clienteUser->id,
            'service_id' => $this->servicioCorte->id,
            'trabajador_id' => $this->trabajador1->id,
            'fecha' => $hoy,
            'hora_inicio' => '13:00:00',
            'hora_fin' => '13:30:00',
            'precio' => 200.00,
            'estado' => 'cancelada',
            'estado_pago' => 'pendiente',
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $rankingServicios = $response->viewData('serviciosMasSolicitados');

        $this->assertCount(2, $rankingServicios);
        // Top 1: Afeitado Tradicional con 2 citas
        $this->assertEquals('Afeitado Tradicional', $rankingServicios->first()->nombre);
        $this->assertEquals(2, $rankingServicios->first()->total_citas);

        // Top 2: Corte Fade Clásico con 1 cita
        $this->assertEquals('Corte Fade Clásico', $rankingServicios->last()->nombre);
        $this->assertEquals(1, $rankingServicios->last()->total_citas);
    }

    /**
     * 5. Comprueba que el ranking de trabajadores agrupa, excluye canceladas y ordena descendente.
     */
    public function test_ranking_trabajadores_agrupa_excluye_canceladas_y_determina_funcion(): void
    {
        $tz = $this->ingresosService->getBusinessTimezone();
        $hoy = Carbon::now($tz)->format('Y-m-d');

        // Trabajador 2 (Estilista sin servicios asignados aún): 3 citas válidas
        for ($i = 0; $i < 3; $i++) {
            Cita::create([
                'user_id' => $this->clienteUser->id,
                'service_id' => $this->servicioCorte->id,
                'trabajador_id' => $this->trabajador2->id,
                'fecha' => $hoy,
                'hora_inicio' => sprintf('%02d:00:00', 10 + $i),
                'hora_fin' => sprintf('%02d:30:00', 10 + $i),
                'precio' => 200.00,
                'estado' => 'confirmada',
                'estado_pago' => 'pendiente',
            ]);
        }

        // Trabajador 1 (Barbero con servicios de barbería): 1 cita válida y 2 canceladas
        Cita::create([
            'user_id' => $this->clienteUser->id,
            'service_id' => $this->servicioBarba->id,
            'trabajador_id' => $this->trabajador1->id,
            'fecha' => $hoy,
            'hora_inicio' => '14:00:00',
            'hora_fin' => '14:30:00',
            'precio' => 150.00,
            'estado' => 'completada',
            'estado_pago' => 'pagado',
        ]);
        Cita::create([
            'user_id' => $this->clienteUser->id,
            'service_id' => $this->servicioBarba->id,
            'trabajador_id' => $this->trabajador1->id,
            'fecha' => $hoy,
            'hora_inicio' => '15:00:00',
            'hora_fin' => '15:30:00',
            'precio' => 150.00,
            'estado' => 'cancelada',
            'estado_pago' => 'pendiente',
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $rankingTrabajadores = $response->viewData('trabajadoresConMasCitas');

        $this->assertCount(2, $rankingTrabajadores);

        // Top 1: Trabajador 2 con 3 citas
        $top1 = $rankingTrabajadores->first();
        $this->assertEquals($this->trabajador2->id, $top1->id);
        $this->assertEquals(3, $top1->total_citas);
        $this->assertEquals('Especialidad no asignada', $top1->funcion);

        // Top 2: Trabajador 1 con 1 cita válida (función Barbero derivada de sus servicios)
        $top2 = $rankingTrabajadores->last();
        $this->assertEquals($this->trabajador1->id, $top2->id);
        $this->assertEquals(1, $top2->total_citas);
        $this->assertEquals('Barbero', $top2->funcion);
    }

    /**
     * 6. Comprueba la derivación de función Barbero / Estilista cuando tiene servicios de ambas categorías.
     */
    public function test_derivacion_correcta_de_especialidad_mixta(): void
    {
        $servicioEstetica = Service::create([
            'name' => 'Peinado y Secado',
            'category' => 'estetica',
            'duration_minutes' => 45,
            'price' => 300.00,
            'is_active' => true,
        ]);

        // Asignar servicio de estética al trabajador 1 (ahora tiene barbería + estética)
        $this->trabajador1->servicios()->attach($servicioEstetica->id);

        $tz = $this->ingresosService->getBusinessTimezone();
        $hoy = Carbon::now($tz)->format('Y-m-d');

        Cita::create([
            'user_id' => $this->clienteUser->id,
            'service_id' => $servicioEstetica->id,
            'trabajador_id' => $this->trabajador1->id,
            'fecha' => $hoy,
            'hora_inicio' => '16:00:00',
            'hora_fin' => '16:45:00',
            'precio' => 300.00,
            'estado' => 'confirmada',
            'estado_pago' => 'pendiente',
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $rankingTrabajadores = $response->viewData('trabajadoresConMasCitas');
        $top1 = $rankingTrabajadores->first();

        $this->assertEquals('Barbero / Estilista', $top1->funcion);
    }

    /**
     * 7. Comprueba que los ingresos del mes acumulan citas del mes en curso y excluyen otros meses.
     */
    public function test_ingresos_mes_acumula_citas_del_mes_en_curso_y_excluye_otros_meses(): void
    {
        $tz = $this->ingresosService->getBusinessTimezone();
        $now = Carbon::now($tz);
        $fechaEsteMes = $now->copy()->startOfMonth()->format('Y-m-d');
        $fechaMesAnterior = $now->copy()->subMonth()->startOfMonth()->format('Y-m-d');

        // Cita del mes en curso pagada ($200.00)
        Cita::create([
            'user_id' => $this->clienteUser->id,
            'service_id' => $this->servicioCorte->id,
            'trabajador_id' => $this->trabajador1->id,
            'fecha' => $fechaEsteMes,
            'hora_inicio' => '10:00:00',
            'hora_fin' => '10:30:00',
            'precio' => 200.00,
            'estado' => 'completada',
            'estado_pago' => 'pagado',
        ]);

        // Cita del mes anterior pagada ($150.00) -> NO DEBE SUMAR al mes en curso
        Cita::create([
            'user_id' => $this->clienteUser->id,
            'service_id' => $this->servicioBarba->id,
            'trabajador_id' => $this->trabajador1->id,
            'fecha' => $fechaMesAnterior,
            'hora_inicio' => '11:00:00',
            'hora_fin' => '11:30:00',
            'precio' => 150.00,
            'estado' => 'completada',
            'estado_pago' => 'pagado',
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $ingresosMes = $response->viewData('ingresosMes');

        $this->assertEquals(200.00, $ingresosMes['total_ingresos']);
        $this->assertEquals('$200.00 MXN', $ingresosMes['total_ingresos_mxn']);
        $this->assertEquals(1, $ingresosMes['citas_pagadas_count']);
    }

    /**
     * 8. Comprueba el criterio de desempate alfabético en servicios con igual número de citas.
     */
    public function test_desempate_alfabetico_en_servicios_mas_solicitados(): void
    {
        $tz = $this->ingresosService->getBusinessTimezone();
        $hoy = Carbon::now($tz)->format('Y-m-d');

        // 1 cita para Corte Fade Clásico y 1 cita para Afeitado Tradicional
        Cita::create([
            'user_id' => $this->clienteUser->id,
            'service_id' => $this->servicioCorte->id,
            'trabajador_id' => $this->trabajador1->id,
            'fecha' => $hoy,
            'hora_inicio' => '10:00:00',
            'hora_fin' => '10:30:00',
            'precio' => 200.00,
            'estado' => 'confirmada',
            'estado_pago' => 'pendiente',
        ]);

        Cita::create([
            'user_id' => $this->clienteUser->id,
            'service_id' => $this->servicioBarba->id,
            'trabajador_id' => $this->trabajador1->id,
            'fecha' => $hoy,
            'hora_inicio' => '11:00:00',
            'hora_fin' => '11:30:00',
            'precio' => 150.00,
            'estado' => 'confirmada',
            'estado_pago' => 'pendiente',
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $ranking = $response->viewData('serviciosMasSolicitados');

        // Al tener 1 cita cada uno, 'Afeitado Tradicional' precede alfabéticamente a 'Corte Fade Clásico'
        $this->assertEquals('Afeitado Tradicional', $ranking->first()->nombre);
        $this->assertEquals('Corte Fade Clásico', $ranking->last()->nombre);
    }

    /**
     * 9. Comprueba que usuarios no autenticados o con rol distinto de administrador son restringidos.
     */
    public function test_usuario_no_autenticado_o_no_admin_es_restringido(): void
    {
        // 1. Invitado
        $respGuest = $this->get(route('admin.dashboard'));
        $respGuest->assertRedirect(route('login'));

        // 2. Cliente
        $respCliente = $this->actingAs($this->clienteUser)->get(route('admin.dashboard'));
        $respCliente->assertStatus(403);

        // 3. Trabajador
        $respWorker = $this->actingAs($this->workerUser)->get(route('admin.dashboard'));
        $respWorker->assertStatus(403);
    }
}
