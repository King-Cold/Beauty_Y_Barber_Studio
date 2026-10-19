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
use Tests\TestCase;

class AdminDashboardSubtarea5Test extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $clienteUser;
    protected User $workerUser;
    protected Trabajador $trabajador1;
    protected Trabajador $trabajador2;
    protected Service $servicioCorte;
    protected Service $servicioBarba;
    protected Service $servicioTinte;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(TestUsersSeeder::class);

        $this->adminUser = User::where('role_id', 1)->first();
        $this->clienteUser = User::where('role_id', 4)->first();
        $this->workerUser = User::where('role_id', 3)->first();

        Cita::ensureTableExists();

        // 1. Trabajador 1 (Barbero)
        $this->trabajador1 = Trabajador::create([
            'user_id' => $this->workerUser->id,
            'direccion' => 'Calle 60 #100, Centro',
            'experiencia' => 5,
            'activo' => true,
        ]);

        // 2. Trabajador 2 (Estilista)
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
            'experiencia' => 4,
            'activo' => true,
        ]);

        // 3. Servicios en catálogo
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

        $this->servicioTinte = Service::create([
            'name' => 'Tinte y Coloración',
            'category' => 'estetica',
            'duration_minutes' => 60,
            'price' => 500.00,
            'is_active' => true,
        ]);

        // Asignar servicios a especialistas
        $this->trabajador1->servicios()->attach([$this->servicioCorte->id, $this->servicioBarba->id]);
        $this->trabajador2->servicios()->attach([$this->servicioTinte->id]);
    }

    /**
     * 1. Comprueba la inclusión de Chart.js v4 con versión estable específica y el banner de fallback.
     */
    public function test_chartjs_v4_estable_y_banner_de_fallback_estan_presentes(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        // Script con versión fija de Chart.js v4.4.4
        $response->assertSee('https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js', false);
        // Elemento informativo de fallback para caída de biblioteca o modo offline
        $response->assertSee('id="chartFallbackAlert"', false);
        $response->assertSee('Visualización gráfica no disponible temporalmente', false);
    }

    /**
     * 2. Comprueba la presencia de los cuatro lienzos de gráficos requeridos.
     */
    public function test_los_cuatro_graficos_canvas_estan_presentes_con_sus_identificadores(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));

        $response->assertStatus(200);

        // Gráfico 1: Estados de citas
        $response->assertSee('id="chartCitasEstadoCanvas"', false);
        $response->assertSee('id="sectionChartCitasEstado"', false);

        // Gráfico 2: Comparativo de ingresos
        $response->assertSee('id="chartIngresosCanvas"', false);
        $response->assertSee('id="sectionChartIngresos"', false);

        // Gráfico 3: Servicios más solicitados
        $response->assertSee('id="chartServiciosCanvas"', false);
        $response->assertSee('id="sectionServiciosDemanda"', false);

        // Gráfico 4: Trabajadores con mayor cantidad de citas
        $response->assertSee('id="chartTrabajadoresCanvas"', false);
        $response->assertSee('id="sectionPersonalDemanda"', false);
    }

    /**
     * 3. Comprueba la presencia de las dos tablas complementarias con sus columnas y encabezados.
     */
    public function test_las_dos_tablas_complementarias_estan_presentes_con_sus_estructuras(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));

        $response->assertStatus(200);

        // Tabla complementaria de servicios
        $response->assertSee('id="tablaServiciosMasSolicitados"', false);
        $response->assertSee('Pos');
        $response->assertSee('Servicio');
        $response->assertSee('Categoría');
        $response->assertSee('Citas');

        // Tabla complementaria de trabajadores
        $response->assertSee('id="tablaTrabajadoresMasCitas"', false);
        $response->assertSee('Trabajador');
        $response->assertSee('Función');
    }

    /**
     * 4. Comprueba que el gráfico financiero incluye la aclaración de que los ingresos del mes incluyen los del día.
     */
    public function test_aclaracion_de_ingresos_financieros_esta_presente_en_el_grafico(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Los ingresos mensuales comprenden el acumulado del mes calendario en curso e incluyen los del día', false);
        $response->assertSee('Se calculan según la fecha programada de las citas', false);
    }

    /**
     * 5. Comprueba la consistencia de datos entre el JSON del DOM, las tablas y la base de datos.
     */
    public function test_consistencia_de_datos_en_payload_json_y_tablas(): void
    {
        $tz = 'America/Merida';
        $today = Carbon::now($tz)->format('Y-m-d');

        // Crear citas de prueba
        // Cita 1: Completada y Pagada (Corte) con Trabajador 1
        Cita::create([
            'user_id' => $this->clienteUser->id,
            'service_id' => $this->servicioCorte->id,
            'trabajador_id' => $this->trabajador1->id,
            'fecha' => $today,
            'hora_inicio' => '10:00:00',
            'hora_fin' => '10:30:00',
            'estado' => 'completada',
            'estado_pago' => 'pagado',
            'precio' => 200.00,
        ]);

        // Cita 2: Confirmada y Pagada (Corte) con Trabajador 1
        Cita::create([
            'user_id' => $this->clienteUser->id,
            'service_id' => $this->servicioCorte->id,
            'trabajador_id' => $this->trabajador1->id,
            'fecha' => $today,
            'hora_inicio' => '11:00:00',
            'hora_fin' => '11:30:00',
            'estado' => 'confirmada',
            'estado_pago' => 'pagado',
            'precio' => 200.00,
        ]);

        // Cita 3: Completada y Pagada (Barba) con Trabajador 1
        Cita::create([
            'user_id' => $this->clienteUser->id,
            'service_id' => $this->servicioBarba->id,
            'trabajador_id' => $this->trabajador1->id,
            'fecha' => $today,
            'hora_inicio' => '12:00:00',
            'hora_fin' => '12:30:00',
            'estado' => 'completada',
            'estado_pago' => 'pagado',
            'precio' => 150.00,
        ]);

        // Cita 4: Cancelada (Tinte) con Trabajador 2 -> Debe ser excluida de los rankings de servicios y staff
        Cita::create([
            'user_id' => $this->clienteUser->id,
            'service_id' => $this->servicioTinte->id,
            'trabajador_id' => $this->trabajador2->id,
            'fecha' => $today,
            'hora_inicio' => '14:00:00',
            'hora_fin' => '15:00:00',
            'estado' => 'cancelada',
            'estado_pago' => 'pagado',
            'precio' => 500.00,
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));

        $response->assertStatus(200);

        // 1. Verificar JSON de intercambio seguro
        $response->assertSee('id="dashboard-chart-data"', false);
        $content = $response->getContent();
        $this->assertStringContainsString('"completadas": 2', $content);
        $this->assertStringContainsString('"canceladas": 1', $content);

        // 2. Verificar que en la tabla complementaria de servicios aparece el Corte con 2 citas y Barba con 1 cita
        $response->assertSee('Corte Fade Clásico');
        $response->assertSee('Afeitado Tradicional');

        // 3. Verificar que el servicio Tinte (cuya única cita fue cancelada) no figura en la tabla ni en el ranking
        $response->assertDontSee('Tinte y Coloración');

        // 4. Verificar orden descendente en la tabla de servicios
        $posCorte = strpos($content, 'Corte Fade Clásico');
        $posBarba = strpos($content, 'Afeitado Tradicional');
        $this->assertTrue($posCorte < $posBarba, 'El servicio con más citas debe aparecer antes en el DOM.');

        // 5. Verificar que el Trabajador 1 aparece con función 'Barbero' y total 3 citas (2 corte + 1 barba)
        $response->assertSee($this->trabajador1->nombre_completo);
        $response->assertSee('Barbero');

        // 6. Verificar que los ingresos en JSON reflejan los importes reales numéricos
        $this->assertStringContainsString('"dia": 550', $content);
        $this->assertStringContainsString('"mes": 550', $content);
    }

    /**
     * 6. Comprueba que el gráfico de ingresos recibe valores numéricos reales: día $0.00 y mes $200.00.
     */
    public function test_grafico_ingresos_representa_valores_reales_dia_cero_y_mes_positivo(): void
    {
        $tz = 'America/Merida';
        $now = Carbon::now($tz);
        
        // Fecha dentro del mes en curso pero distinta a hoy
        $fechaOtroDiaDelMes = $now->day > 1 
            ? $now->copy()->startOfMonth()->format('Y-m-d') 
            : $now->copy()->endOfMonth()->format('Y-m-d');

        // Cita con fecha en otro día del mes en curso por $200.00 (pagada y concluida)
        Cita::create([
            'user_id' => $this->clienteUser->id,
            'service_id' => $this->servicioCorte->id,
            'trabajador_id' => $this->trabajador1->id,
            'fecha' => $fechaOtroDiaDelMes,
            'hora_inicio' => '10:00:00',
            'hora_fin' => '10:30:00',
            'estado' => 'completada',
            'estado_pago' => 'pagado',
            'precio' => 200.00,
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));

        $response->assertStatus(200);

        // La tarjeta de hoy debe mostrar $0.00 MXN y la del mes $200.00 MXN
        $response->assertSee('$0.00 MXN');
        $response->assertSee('$200.00 MXN');

        // El payload JSON recibido por Chart.js debe contener números reales: dia: 0 y mes: 200
        $content = $response->getContent();
        $this->assertMatchesRegularExpression('/"ingresos":\s*\{\s*"dia":\s*0,\s*"mes":\s*200\s*\}/', $content);

        // El canvas del gráfico de ingresos debe estar presente en el DOM
        $response->assertSee('id="chartIngresosCanvas"', false);
    }

    /**
     * 7. Comprueba el correcto manejo de estados vacíos (empty state) sin registros.
     */
    public function test_estados_vacios_muestran_mensajes_informativos_apropiados(): void
    {
        // Sin citas creadas
        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));

        $response->assertStatus(200);

        // Mensajes de estado vacío
        $response->assertSee('No hay datos disponibles para mostrar');
        $response->assertSee('id="emptyChartCitasEstado"', false);
        $response->assertSee('id="emptyChartIngresos"', false);
        $response->assertSee('id="emptyChartServicios"', false);
        $response->assertSee('id="emptyChartTrabajadores"', false);
    }

    /**
     * 7. Comprueba que las tarjetas de métricas y rankings laterales de subtareas 1-4 se mantienen intactos.
     */
    public function test_conservacion_de_tarjetas_kpi_y_rankings_en_secciones_principales(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));

        $response->assertStatus(200);

        // Tarjetas principales de conteo general (Subtareas 1 y 2)
        $response->assertSee('Clientes Registrados');
        $response->assertSee('Trabajadores');
        $response->assertSee('Servicios Disponibles');
        $response->assertSee('Citas Registradas');

        // Tarjetas de estado de citas (Subtarea 3)
        $response->assertSee('Citas Completadas');
        $response->assertSee('Citas Canceladas');
        $response->assertSee('Citas Vencidas');

        // Tarjetas de ingresos (Subtarea 4)
        $response->assertSee('Ingresos del Día');
        $response->assertSee('Ingresos del Mes');

        // Tarjetas laterales redundantes eliminadas
        $response->assertDontSee('id="sectionServiciosRanking"', false);
        $response->assertDontSee('id="sectionTrabajadoresRanking"', false);

        // Rankings representados en secciones principales con gráficos y tablas (Subtarea 5)
        $response->assertSee('id="sectionServiciosDemanda"', false);
        $response->assertSee('id="sectionPersonalDemanda"', false);
    }

    /**
     * 8. Comprueba que usuarios sin rol administrativo no pueden acceder al dashboard con gráficos.
     */
    public function test_seguridad_acceso_restringido_a_no_administradores(): void
    {
        // Cliente -> 403 Forbidden
        $responseCliente = $this->actingAs($this->clienteUser)->get(route('admin.dashboard'));
        $responseCliente->assertStatus(403);

        // Trabajador -> 403 Forbidden
        $responseTrabajador = $this->actingAs($this->workerUser)->get(route('admin.dashboard'));
        $responseTrabajador->assertStatus(403);

        // Invitado -> Redirigido a login
        $this->app['auth']->forgetGuards();
        $responseGuest = $this->get(route('admin.dashboard'));
        $responseGuest->assertRedirect(route('login'));
    }
}
