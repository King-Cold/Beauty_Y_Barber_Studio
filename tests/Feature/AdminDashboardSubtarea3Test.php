<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\Service;
use App\Models\Trabajador;
use App\Models\User;
use App\Services\CitaCalendarioService;
use Carbon\Carbon;
use Database\Seeders\TestUsersSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminDashboardSubtarea3Test extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $clienteUser;
    protected User $workerUser;
    protected Service $servicio;
    protected int $trabajadorId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(TestUsersSeeder::class);

        $this->adminUser = User::where('role_id', 1)->first();
        $this->clienteUser = User::where('role_id', 4)->first();
        $this->workerUser = User::where('role_id', 3)->first();

        // Servicio base para asociar a las citas
        $this->servicio = Service::create([
            'name' => 'Corte Caballero Premium',
            'description' => 'Servicio de corte clásico y estilizado',
            'price' => 250.00,
            'duration_minutes' => 30,
            'category' => 'barberia',
            'is_active' => true,
        ]);

        // Ficha de trabajador en la tabla trabajadores si existe
        $this->trabajadorId = $this->workerUser->id;
        if (Schema::hasTable('trabajadores')) {
            $trabajadorModel = Trabajador::create([
                'user_id' => $this->workerUser->id,
                'direccion' => 'Av. Reforma #200',
                'experiencia' => 6,
                'activo' => true,
            ]);
            $this->trabajadorId = $trabajadorModel->id;
        }

        Cita::ensureTableExists();
        Cita::query()->delete();
    }

    /**
     * Helper para crear citas de prueba con valores controlados.
     */
    protected function crearCita(array $atributos = []): Cita
    {
        $default = [
            'user_id' => $this->clienteUser->id,
            'trabajador_id' => $this->trabajadorId,
            'service_id' => $this->servicio->id,
            'fecha' => Carbon::now('America/Merida')->addDays(2)->format('Y-m-d'),
            'hora_inicio' => '10:00',
            'hora_fin' => '10:30',
            'duracion_minutos' => 30,
            'precio' => 250.00,
            'estado' => 'confirmada',
            'estado_pago' => 'pendiente',
        ];

        return Cita::create(array_merge($default, $atributos));
    }

    /**
     * Criterio 1 & 2: El dashboard retorna y visualiza las variables de estado para citas completadas, canceladas y vencidas.
     */
    public function test_administrador_ve_variables_de_estado_en_dashboard(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHasAll([
            'citasCompletadas',
            'citasCanceladas',
            'citasVencidas',
            'totalCitas'
        ]);
    }

    /**
     * Criterio 1 & 2: Citas completadas se contabilizan correctamente.
     */
    public function test_citas_completadas_se_contabilizan_correctamente(): void
    {
        // 3 citas completadas
        $this->crearCita(['estado' => 'completada']);
        $this->crearCita(['estado' => 'completada']);
        $this->crearCita(['estado' => 'completada']);

        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('citasCompletadas', 3);
        $response->assertSee('<span class="kpi-value" id="kpiValueCitasCompletadas">3</span>', false);
    }

    /**
     * Criterio 1 & 2: Citas canceladas se contabilizan correctamente.
     */
    public function test_citas_canceladas_se_contabilizan_correctamente(): void
    {
        // 2 citas canceladas
        $this->crearCita(['estado' => 'cancelada']);
        $this->crearCita(['estado' => 'cancelada']);

        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('citasCanceladas', 2);
        $response->assertSee('<span class="kpi-value" id="kpiValueCitasCanceladas">2</span>', false);
    }

    /**
     * Criterio 1 & 2: Citas vencidas se contabilizan correctamente (por estado explícito 'vencida' o confirmada de fecha/hora pasada).
     */
    public function test_citas_vencidas_se_contabilizan_correctamente(): void
    {
        $tz = 'America/Merida';
        $ayer = Carbon::now($tz)->subDay()->format('Y-m-d');
        $antier = Carbon::now($tz)->subDays(2)->format('Y-m-d');

        // Caso 1: Cita con estado persistido 'vencida'
        $this->crearCita(['estado' => 'vencida']);

        // Caso 2: Cita con estado 'confirmada' cuya fecha ya pasó (ayer)
        $this->crearCita([
            'fecha' => $ayer,
            'hora_inicio' => '10:00',
            'hora_fin' => '10:30',
            'estado' => 'confirmada'
        ]);

        // Caso 3: Cita con estado 'confirmada' de hace dos días
        $this->crearCita([
            'fecha' => $antier,
            'hora_inicio' => '11:00',
            'hora_fin' => '11:30',
            'estado' => 'confirmada'
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('citasVencidas', 3);
        $response->assertSee('<span class="kpi-value" id="kpiValueCitasVencidas">3</span>', false);
    }

    /**
     * Criterio 3 & 7: Citas con otros estados (confirmada futura, en atención, pendiente) NO se incluyen en los tres indicadores.
     */
    public function test_citas_con_otros_estados_no_se_incluyen_en_las_tres_categorias(): void
    {
        $tz = 'America/Merida';
        $manana = Carbon::now($tz)->addDay()->format('Y-m-d');
        $hoy = Carbon::now($tz)->format('Y-m-d');

        // Cita futura confirmada
        $this->crearCita([
            'fecha' => $manana,
            'hora_inicio' => '15:00',
            'hora_fin' => '15:30',
            'estado' => 'confirmada',
        ]);

        // Cita en atención activa hoy
        $this->crearCita([
            'fecha' => $hoy,
            'hora_inicio' => '14:00',
            'hora_fin' => '14:30',
            'estado' => 'en_atencion',
        ]);

        // Cita pendiente futura
        $this->crearCita([
            'fecha' => $manana,
            'hora_inicio' => '16:00',
            'hora_fin' => '16:30',
            'estado' => 'pendiente',
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        // Ninguna de estas tres debe entrar a completada, cancelada o vencida
        $response->assertViewHas('citasCompletadas', 0);
        $response->assertViewHas('citasCanceladas', 0);
        $response->assertViewHas('citasVencidas', 0);

        // Sin embargo, todas forman parte del total general de reservaciones
        $response->assertViewHas('totalCitas', 3);
    }

    /**
     * Criterio 3: Conteo sin duplicidades: Cada cita se contabiliza exclusivamente una sola vez.
     */
    public function test_cada_cita_se_contabiliza_una_sola_vez(): void
    {
        $tz = 'America/Merida';
        $pasado = Carbon::now($tz)->subDays(3)->format('Y-m-d');
        $futuro = Carbon::now($tz)->addDays(3)->format('Y-m-d');

        // 1 completada
        $this->crearCita(['estado' => 'completada']);
        // 1 cancelada
        $this->crearCita(['estado' => 'cancelada']);
        // 1 vencida
        $this->crearCita(['fecha' => $pasado, 'estado' => 'confirmada']);
        // 1 confirmada futura (activa)
        $this->crearCita(['fecha' => $futuro, 'estado' => 'confirmada']);

        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('citasCompletadas', 1);
        $response->assertViewHas('citasCanceladas', 1);
        $response->assertViewHas('citasVencidas', 1);
        $response->assertViewHas('totalCitas', 4);

        $subtotal = $response->viewData('citasCompletadas')
            + $response->viewData('citasCanceladas')
            + $response->viewData('citasVencidas');

        $this->assertEquals(3, $subtotal);
        $this->assertEquals(4, $response->viewData('totalCitas'));
    }

    /**
     * Criterio 4: Actualización de estados: Cuando una cita cambia de estado, se refleja al recargar el dashboard.
     */
    public function test_cambio_de_estado_se_refleja_al_consultar_nuevamente_el_dashboard(): void
    {
        $tz = 'America/Merida';
        $futuro = Carbon::now($tz)->addDays(1)->format('Y-m-d');

        // Inicialmente la cita está confirmada futura
        $cita = $this->crearCita([
            'fecha' => $futuro,
            'estado' => 'confirmada'
        ]);

        $res1 = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));
        $this->assertEquals(0, $res1->viewData('citasCompletadas'));
        $this->assertEquals(0, $res1->viewData('citasCanceladas'));

        // Se completa la atención
        $cita->update(['estado' => 'completada']);

        $res2 = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));
        $this->assertEquals(1, $res2->viewData('citasCompletadas'));
        $this->assertEquals(0, $res2->viewData('citasCanceladas'));

        // Se anula o cancela posteriormente
        $cita->update(['estado' => 'cancelada']);

        $res3 = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));
        $this->assertEquals(0, $res3->viewData('citasCompletadas'));
        $this->assertEquals(1, $res3->viewData('citasCanceladas'));
    }

    /**
     * Criterio 6: Independencia del estado de pago: El campo estado_pago no altera la clasificación operativa.
     */
    public function test_estado_de_pago_no_afecta_los_conteos(): void
    {
        // Completada con pago pendiente
        $this->crearCita(['estado' => 'completada', 'estado_pago' => 'pendiente']);
        // Completada con pago pagado
        $this->crearCita(['estado' => 'completada', 'estado_pago' => 'pagado']);

        // Cancelada con pago pagado (ej. cobro previo reembolsado o no)
        $this->crearCita(['estado' => 'cancelada', 'estado_pago' => 'pagado']);

        // Vencida con pago pendiente
        $this->crearCita(['estado' => 'vencida', 'estado_pago' => 'pendiente']);

        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        // Debe contabilizar 2 completadas, 1 cancelada, 1 vencida, sin importar si estado_pago es pagado o pendiente
        $response->assertViewHas('citasCompletadas', 2);
        $response->assertViewHas('citasCanceladas', 1);
        $response->assertViewHas('citasVencidas', 1);
        $response->assertViewHas('totalCitas', 4);
    }

    /**
     * Criterio 8: Ausencia de registros: Cuando no existen citas, los indicadores muestran 0 sin error.
     */
    public function test_indicadores_muestran_cero_cuando_no_existen_registros(): void
    {
        // Base de datos de citas vacía
        Cita::query()->delete();

        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('citasCompletadas', 0);
        $response->assertViewHas('citasCanceladas', 0);
        $response->assertViewHas('citasVencidas', 0);
        $response->assertViewHas('totalCitas', 0);

        // Se visualizan en HTML sin desaparecer
        $response->assertSee('<span class="kpi-value" id="kpiValueCitasCompletadas">0</span>', false);
        $response->assertSee('<span class="kpi-value" id="kpiValueCitasCanceladas">0</span>', false);
        $response->assertSee('<span class="kpi-value" id="kpiValueCitasVencidas">0</span>', false);
    }

    /**
     * Criterio 5: Representación visual diferenciada, etiquetas descriptivas y accesibilidad.
     */
    public function test_representacion_visual_diferenciada_y_etiquetas_descriptivas(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));

        $response->assertStatus(200);

        // Identificadores de tarjeta
        $response->assertSee('id="kpiCardCitasCompletadas"', false);
        $response->assertSee('id="kpiCardCitasCanceladas"', false);
        $response->assertSee('id="kpiCardCitasVencidas"', false);

        // Títulos identificables
        $response->assertSee('Citas Completadas');
        $response->assertSee('Citas Canceladas');
        $response->assertSee('Citas Vencidas');

        // Etiquetas visuales descriptivas (no solo color)
        $response->assertSee('status-tag-completada', false);
        $response->assertSee('status-tag-cancelada', false);
        $response->assertSee('status-tag-vencida', false);

        // Enlaces al módulo de agenda con filtros predeterminados por estado
        $response->assertSee(route('admin.citas'));
        $response->assertSee(route('admin.citas', ['estado' => 'completada']));
        $response->assertSee(route('admin.citas', ['estado' => 'cancelada']));
        $response->assertSee(route('admin.citas', ['estado' => 'vencida']));
    }

    /**
     * Verifica que los enlaces del dashboard dirijan a la agenda con el estado predeterminado.
     */
    public function test_enlaces_de_agenda_abren_la_agenda_con_filtro_predeterminado(): void
    {
        $this->crearCita(['estado' => 'completada']);
        $this->crearCita(['estado' => 'cancelada']);

        // 1. Filtrado de completadas
        $responseCompletada = $this->actingAs($this->adminUser)
            ->get(route('admin.citas', ['estado' => 'completada']));
        $responseCompletada->assertStatus(200);
        $responseCompletada->assertViewHas('filtros', function ($filtros) {
            return ($filtros['estado'] ?? null) === 'completada';
        });

        // 2. Filtrado de canceladas
        $responseCancelada = $this->actingAs($this->adminUser)
            ->get(route('admin.citas', ['estado' => 'cancelada']));
        $responseCancelada->assertStatus(200);
        $responseCancelada->assertViewHas('filtros', function ($filtros) {
            return ($filtros['estado'] ?? null) === 'cancelada';
        });

        // 3. Filtrado de vencidas
        $responseVencida = $this->actingAs($this->adminUser)
            ->get(route('admin.citas', ['estado' => 'vencida']));
        $responseVencida->assertStatus(200);
        $responseVencida->assertViewHas('filtros', function ($filtros) {
            return ($filtros['estado'] ?? null) === 'vencida';
        });
    }

    /**
     * Criterio 7: Consistencia de subtotales agrupados vs total general del establecimiento.
     */
    public function test_consistencia_de_totales_y_subtotales(): void
    {
        $tz = 'America/Merida';
        $pasado = Carbon::now($tz)->subDays(1)->format('Y-m-d');
        $futuro = Carbon::now($tz)->addDays(1)->format('Y-m-d');

        // 3 completadas
        $this->crearCita(['estado' => 'completada']);
        $this->crearCita(['estado' => 'completada']);
        $this->crearCita(['estado' => 'completada']);

        // 2 canceladas
        $this->crearCita(['estado' => 'cancelada']);
        $this->crearCita(['estado' => 'cancelada']);

        // 1 vencida
        $this->crearCita(['fecha' => $pasado, 'estado' => 'confirmada']);

        // 2 confirmadas futuras
        $this->crearCita(['fecha' => $futuro, 'estado' => 'confirmada']);
        $this->crearCita(['fecha' => $futuro, 'estado' => 'confirmada']);

        // 1 en atención activa
        $this->crearCita(['fecha' => Carbon::now($tz)->format('Y-m-d'), 'estado' => 'en_atencion']);

        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $completadas = $response->viewData('citasCompletadas');
        $canceladas = $response->viewData('citasCanceladas');
        $vencidas = $response->viewData('citasVencidas');
        $totalCitas = $response->viewData('totalCitas');

        $this->assertEquals(3, $completadas);
        $this->assertEquals(2, $canceladas);
        $this->assertEquals(1, $vencidas);

        // Suma de los 3 estados auditados
        $subtotal = $completadas + $canceladas + $vencidas;
        $this->assertEquals(6, $subtotal);

        // Total general conserva todas las citas existentes (6 auditadas + 2 futuras + 1 en atención = 9)
        $this->assertEquals(9, $totalCitas);
        $this->assertGreaterThanOrEqual($subtotal, $totalCitas);
    }

    /**
     * Verificación 1 & 2: Alineación exacta entre Dashboard, Cita::getEstadoVisualAttribute() y CitaCalendarioService.
     */
    public function test_alineacion_exacta_entre_dashboard_y_modulo_gestion_citas_y_modelo(): void
    {
        $tz = 'America/Merida';
        $now = Carbon::now($tz);
        $ayer = $now->copy()->subDay()->format('Y-m-d');
        $hoy = $now->format('Y-m-d');
        $manana = $now->copy()->addDay()->format('Y-m-d');

        // Cita 1: Confirmada de ayer -> Vencida
        $c1 = $this->crearCita(['fecha' => $ayer, 'estado' => 'confirmada']);

        // Cita 2: Con estado persistido 'vencida' -> Vencida
        $c2 = $this->crearCita(['fecha' => $manana, 'estado' => 'vencida']);

        // Cita 3: Confirmada futura (mañana) -> Confirmada (vigente)
        $c3 = $this->crearCita(['fecha' => $manana, 'estado' => 'confirmada']);

        // Cita 4: Completada ayer -> Completada
        $c4 = $this->crearCita(['fecha' => $ayer, 'estado' => 'completada']);

        // Cita 5: Cancelada ayer -> Cancelada
        $c5 = $this->crearCita(['fecha' => $ayer, 'estado' => 'cancelada']);

        // 1. Verificación del Dashboard
        $dashboardResponse = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));
        $dashboardResponse->assertStatus(200);
        $dashboardVencidas = $dashboardResponse->viewData('citasVencidas');

        // 2. Verificación del Servicio de Gestión de Citas (CitaCalendarioService)
        $calendarioService = app(CitaCalendarioService::class);
        $citasVencidasModulo = $calendarioService->consultarListadoCitas(['estado' => 'vencida']);

        // 3. Verificación de los accesores de modelo
        $this->assertEquals('vencida', $c1->fresh()->estado_visual);
        $this->assertEquals('vencida', $c2->fresh()->estado_visual);
        $this->assertEquals('confirmada', $c3->fresh()->estado_visual);
        $this->assertEquals('completada', $c4->fresh()->estado_visual);
        $this->assertEquals('cancelada', $c5->fresh()->estado_visual);

        // Ambas fuentes deben dar exactamente 2 citas vencidas
        $this->assertEquals(2, $dashboardVencidas);
        $this->assertEquals(2, $citasVencidasModulo->count());
        $this->assertEquals($citasVencidasModulo->count(), $dashboardVencidas);

        // Los IDs deben coincidir con precisión
        $idsVencidasModulo = $citasVencidasModulo->pluck('id')->sort()->values()->toArray();
        $idsEsperadas = collect([$c1->id, $c2->id])->sort()->values()->toArray();
        $this->assertEquals($idsEsperadas, $idsVencidasModulo);
    }

    /**
     * Verificación 3: Casos límite de citas futuras, en curso, que acaban de vencer y pasadas.
     */
    public function test_casos_limite_de_vencimiento_por_horario_y_fecha(): void
    {
        $tz = 'America/Merida';
        $now = Carbon::now($tz);
        $hoy = $now->format('Y-m-d');
        $manana = $now->copy()->addDay()->format('Y-m-d');
        $ayer = $now->copy()->subDay()->format('Y-m-d');

        // Límite A: Cita futura (mañana) -> NO vencida
        $citaFutura = $this->crearCita([
            'fecha' => $manana,
            'hora_inicio' => '10:00',
            'hora_fin' => '10:30',
            'estado' => 'confirmada',
        ]);

        // Límite B: Cita completada con fecha de ayer -> NUNCA vencida (solo completada)
        $citaCompletadaPasada = $this->crearCita([
            'fecha' => $ayer,
            'hora_inicio' => '08:00',
            'hora_fin' => '08:30',
            'estado' => 'completada',
        ]);

        // Límite C: Cita cancelada con fecha de ayer -> NUNCA vencida (solo cancelada)
        $citaCanceladaPasada = $this->crearCita([
            'fecha' => $ayer,
            'hora_inicio' => '09:00',
            'hora_fin' => '09:30',
            'estado' => 'cancelada',
        ]);

        // Límite D: Cita de hoy cuya hora de fin ya pasó hace 1 hora (acaba de vencer)
        $horaFinPasada = $now->copy()->subMinutes(60)->format('H:i');
        $horaInicioPasada = $now->copy()->subMinutes(90)->format('H:i');
        $citaRecienVencida = $this->crearCita([
            'fecha' => $hoy,
            'hora_inicio' => $horaInicioPasada,
            'hora_fin' => $horaFinPasada,
            'estado' => 'confirmada',
        ]);

        // Límite E: Cita de hoy cuya hora de fin es dentro de 2 horas (aún no llega)
        $horaInicioFutura = $now->copy()->addMinutes(60)->format('H:i');
        $horaFinFutura = $now->copy()->addMinutes(90)->format('H:i');
        $citaPorVencer = $this->crearCita([
            'fecha' => $hoy,
            'hora_inicio' => $horaInicioFutura,
            'hora_fin' => $horaFinFutura,
            'estado' => 'confirmada',
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));
        $response->assertStatus(200);

        // De las 5 citas:
        // Completadas: 1 ($citaCompletadaPasada)
        // Canceladas: 1 ($citaCanceladaPasada)
        // Vencidas: 1 ($citaRecienVencida)
        // Total: 5
        $this->assertEquals(1, $response->viewData('citasCompletadas'));
        $this->assertEquals(1, $response->viewData('citasCanceladas'));
        $this->assertEquals(1, $response->viewData('citasVencidas'));
        $this->assertEquals(5, $response->viewData('totalCitas'));

        // Comprobamos en el modelo también
        $this->assertEquals('confirmada', $citaFutura->fresh()->estado_visual);
        $this->assertEquals('completada', $citaCompletadaPasada->fresh()->estado_visual);
        $this->assertEquals('cancelada', $citaCanceladaPasada->fresh()->estado_visual);
        $this->assertEquals('vencida', $citaRecienVencida->fresh()->estado_visual);
        $this->assertEquals('confirmada', $citaPorVencer->fresh()->estado_visual);
    }

    /**
     * Verificación 4: La zona horaria corresponde estrictamente a Mérida y no es alterada por otras configuraciones.
     */
    public function test_zona_horaria_merida_independiente_de_otra_configuracion(): void
    {
        // Forzamos temporalmente la configuración global a UTC (6 horas adelante de Mérida)
        config(['app.timezone' => 'UTC']);

        // Supongamos que son las 20:00 UTC (14:00 en Mérida)
        // Una cita hoy programada a las 15:00 - 15:30 en Mérida:
        // Si se evaluara en UTC (20:00), parecería erróneamente vencida (15:30 < 20:00).
        // Al evaluarse estrictamente en Mérida (14:00), NO debe considerarse vencida (15:30 > 14:00).
        $nowMerida = Carbon::now('America/Merida');
        $hoyMerida = $nowMerida->format('Y-m-d');
        $horaFinPosterior = $nowMerida->copy()->addMinutes(45)->format('H:i');
        $horaInicioPosterior = $nowMerida->copy()->addMinutes(15)->format('H:i');

        $citaVigenteEnMerida = $this->crearCita([
            'fecha' => $hoyMerida,
            'hora_inicio' => $horaInicioPosterior,
            'hora_fin' => $horaFinPosterior,
            'estado' => 'confirmada',
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));
        $response->assertStatus(200);

        // La cita NO debe figurar como vencida en el dashboard
        $this->assertEquals(0, $response->viewData('citasVencidas'));
        $this->assertEquals(1, $response->viewData('totalCitas'));

        // Restauramos config
        config(['app.timezone' => 'America/Merida']);
    }

    /**
     * Verificación 5: Ninguna cita se contabiliza simultáneamente en dos indicadores.
     */
    public function test_ninguna_cita_se_contabiliza_simultaneamente_en_dos_indicadores(): void
    {
        $tz = 'America/Merida';
        $ayer = Carbon::now($tz)->subDay()->format('Y-m-d');

        // Cita 1: Completada con fecha de ayer (potencialmente candidata a vencida si no se respetara el estado completada)
        $this->crearCita(['fecha' => $ayer, 'estado' => 'completada']);

        // Cita 2: Cancelada con fecha de ayer
        $this->crearCita(['fecha' => $ayer, 'estado' => 'cancelada']);

        // Cita 3: Vencida
        $this->crearCita(['fecha' => $ayer, 'estado' => 'confirmada']);

        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));
        $response->assertStatus(200);

        $completadas = $response->viewData('citasCompletadas');
        $canceladas = $response->viewData('citasCanceladas');
        $vencidas = $response->viewData('citasVencidas');
        $total = $response->viewData('totalCitas');

        $this->assertEquals(1, $completadas);
        $this->assertEquals(1, $canceladas);
        $this->assertEquals(1, $vencidas);

        // La suma de los tres es exactamente 3 y coincide con el total de 3 citas creadas
        $this->assertEquals(3, $completadas + $canceladas + $vencidas);
        $this->assertEquals(3, $total);
    }
}
