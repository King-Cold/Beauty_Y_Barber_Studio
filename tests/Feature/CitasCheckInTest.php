<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\Service;
use App\Models\Trabajador;
use App\Models\User;
use App\Services\CitaCalendarioService;
use Carbon\Carbon;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CitasCheckInTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $recepcionistaUser;
    protected User $trabajadorUser;
    protected User $clienteUser;
    protected Trabajador $trabajador;
    protected Service $servicio;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\TestUsersSeeder::class);

        $this->adminUser = User::where('role_id', 1)->first();
        $this->recepcionistaUser = User::where('role_id', 2)->first();
        $this->trabajadorUser = User::where('role_id', 3)->first();
        $this->clienteUser = User::where('role_id', 4)->first();

        // Especialista
        $this->trabajador = Trabajador::create([
            'user_id' => $this->trabajadorUser->id,
            'direccion' => 'Estación 1 - Beauty Studio',
            'experiencia' => 5,
            'activo' => true,
            'horario' => [
                'lunes' => ['entrada' => '09:00', 'salida' => '19:00'],
                'martes' => ['entrada' => '09:00', 'salida' => '19:00'],
                'miercoles' => ['entrada' => '09:00', 'salida' => '19:00'],
                'jueves' => ['entrada' => '09:00', 'salida' => '19:00'],
                'viernes' => ['entrada' => '09:00', 'salida' => '19:00'],
                'sabado' => ['entrada' => '09:00', 'salida' => '15:00'],
            ],
        ]);

        // Servicio
        $this->servicio = Service::create([
            'name' => 'Corte & Peinado Studio',
            'category' => 'estudio',
            'duration_minutes' => 45,
            'price' => 280.00,
            'is_active' => true,
        ]);
    }

    protected function crearCita(array $atributos = []): Cita
    {
        $fechaFutura = Carbon::today()->addDays(2)->format('Y-m-d');

        return Cita::create(array_merge([
            'user_id' => $this->clienteUser->id,
            'trabajador_id' => $this->trabajador->id,
            'service_id' => $this->servicio->id,
            'fecha' => $fechaFutura,
            'hora_inicio' => '10:00:00',
            'hora_fin' => '10:45:00',
            'duracion_minutos' => 45,
            'precio' => 280.00,
            'estado' => 'confirmada',
            'folio' => 'CITA-' . rand(10000, 99999),
            'notas' => 'Cita de prueba para check-in',
            'check_in_at' => null,
        ], $atributos));
    }

    /**
     * Test 1: Check-in exitoso en cita confirmada como Recepcionista (HTTP 200).
     */
    public function test_01_checkin_exitoso_como_recepcionista(): void
    {
        $cita = $this->crearCita();

        $response = $this->actingAs($this->recepcionistaUser)
            ->postJson("/recepcion/citas/{$cita->id}/checkin");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'checked_in' => true,
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'checked_in',
                'check_in_at',
                'check_in_hora',
                'check_in_formateado',
                'cita' => ['id', 'folio', 'estado', 'check_in_at', 'checked_in'],
            ]);

        $this->assertNotNull($cita->fresh()->check_in_at);
        $this->assertTrue($cita->fresh()->isCheckedIn());
        $this->assertEquals('confirmada', $cita->fresh()->estado);
    }

    /**
     * Test 2: El Administrador cuenta únicamente con permisos de visualización y consulta (HTTP 403 al intentar check-in).
     */
    public function test_02_administrador_solo_permisos_visualizacion_rechazo_checkin(): void
    {
        $cita = $this->crearCita();

        $response = $this->actingAs($this->adminUser)
            ->postJson("/admin/citas/{$cita->id}/checkin");

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
            ]);

        $this->assertNull($cita->fresh()->check_in_at);
        $this->assertFalse($cita->fresh()->isCheckedIn());
    }

    /**
     * Test 3: Persistencia real y formato del timestamp check_in_at en MySQL / SQLite.
     */
    public function test_03_persistencia_real_del_timestamp_check_in_at(): void
    {
        $cita = $this->crearCita();
        $ahora = Carbon::now();

        $this->actingAs($this->recepcionistaUser)
            ->postJson("/recepcion/citas/{$cita->id}/checkin")
            ->assertStatus(200);

        $citaRecargada = $cita->fresh();

        $this->assertNotNull($citaRecargada->check_in_at);
        $this->assertInstanceOf(Carbon::class, $citaRecargada->check_in_at);
        $this->assertEquals($ahora->format('Y-m-d H:i'), $citaRecargada->check_in_at->format('Y-m-d H:i'));
        $this->assertEquals($citaRecargada->check_in_at->format('H:i'), $citaRecargada->check_in_hora);
        $this->assertEquals($citaRecargada->check_in_at->format('d/m/Y H:i'), $citaRecargada->check_in_formateado);
    }

    /**
     * Test 4: Error 404 al intentar registrar check-in en cita inexistente.
     */
    public function test_04_error_404_en_cita_inexistente(): void
    {
        $responseRecepcion = $this->actingAs($this->recepcionistaUser)
            ->postJson("/recepcion/citas/999999/checkin");

        $responseRecepcion->assertStatus(404);

        $responseAdmin = $this->actingAs($this->adminUser)
            ->postJson("/admin/citas/999999/checkin");

        $responseAdmin->assertStatus(404);
    }

    /**
     * Test 5: Rechazo de check-in en cita cancelada (HTTP 422).
     */
    public function test_05_rechazo_de_checkin_en_cita_cancelada(): void
    {
        $cita = $this->crearCita(['estado' => 'cancelada']);

        $response = $this->actingAs($this->recepcionistaUser)
            ->postJson("/recepcion/citas/{$cita->id}/checkin");

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);

        $this->assertNull($cita->fresh()->check_in_at);
        $this->assertFalse($cita->fresh()->isCheckedIn());
    }

    /**
     * Test 6: Rechazo de check-in en cita completada (HTTP 422).
     */
    public function test_06_rechazo_de_checkin_en_cita_completada(): void
    {
        $cita = $this->crearCita(['estado' => 'completada']);

        $response = $this->actingAs($this->recepcionistaUser)
            ->postJson("/recepcion/citas/{$cita->id}/checkin");

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);

        $this->assertNull($cita->fresh()->check_in_at);
        $this->assertFalse($cita->fresh()->isCheckedIn());
    }

    /**
     * Test 7: Rechazo de check-in en cita vencida (HTTP 422).
     */
    public function test_07_rechazo_de_checkin_en_cita_vencida(): void
    {
        // Cita de hace 3 días
        $fechaPasada = Carbon::today()->subDays(3)->format('Y-m-d');
        $cita = $this->crearCita([
            'fecha' => $fechaPasada,
            'hora_inicio' => '09:00:00',
            'hora_fin' => '09:45:00',
            'estado' => 'confirmada', // En DB puede decir confirmada pero vencida lógicamente
        ]);

        $this->assertTrue($cita->isVencida());

        $response = $this->actingAs($this->recepcionistaUser)
            ->postJson("/recepcion/citas/{$cita->id}/checkin");

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);

        $this->assertNull($cita->fresh()->check_in_at);
    }

    /**
     * Test 8: Rechazo de check-in duplicado (HTTP 422) y preservación del timestamp original.
     */
    public function test_08_rechazo_de_checkin_duplicado(): void
    {
        $cita = $this->crearCita();

        // 1er Check-in
        $this->actingAs($this->recepcionistaUser)
            ->postJson("/recepcion/citas/{$cita->id}/checkin")
            ->assertStatus(200);

        $timestampOriginal = $cita->fresh()->check_in_at;
        $this->assertNotNull($timestampOriginal);

        // 2do Check-in (intento duplicado)
        $responseDuplicado = $this->actingAs($this->recepcionistaUser)
            ->postJson("/recepcion/citas/{$cita->id}/checkin");

        $responseDuplicado->assertStatus(422)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonFragment([
                'message' => 'La cita ya cuenta con check-in registrado previamente.',
            ]);

        // Verificar que el timestamp original no fue modificado
        $this->assertEquals(
            $timestampOriginal->toIso8601String(),
            $cita->fresh()->check_in_at->toIso8601String()
        );
    }

    /**
     * Test 9: Control de acceso RBAC — Trabajador (3), Cliente (4) y Guest son rechazados.
     */
    public function test_09_permisos_denegados_para_trabajador_cliente_y_guest(): void
    {
        $cita = $this->crearCita();

        // 1. Trabajador (Rol 3)
        $this->actingAs($this->trabajadorUser)
            ->postJson("/recepcion/citas/{$cita->id}/checkin")
            ->assertStatus(403);

        $this->actingAs($this->trabajadorUser)
            ->postJson("/admin/citas/{$cita->id}/checkin")
            ->assertStatus(403);

        // 2. Cliente (Rol 4)
        $this->actingAs($this->clienteUser)
            ->postJson("/recepcion/citas/{$cita->id}/checkin")
            ->assertStatus(403);

        $this->actingAs($this->clienteUser)
            ->postJson("/admin/citas/{$cita->id}/checkin")
            ->assertStatus(403);

        // 3. Guest (no autenticado)
        $this->postJson("/recepcion/citas/{$cita->id}/checkin")
            ->assertStatus(401);

        $this->postJson("/admin/citas/{$cita->id}/checkin")
            ->assertStatus(401);

        $this->assertNull($cita->fresh()->check_in_at);
    }

    /**
     * Test 10: Integración con detalles y consultas: check-in se refleja en endpoints AJAX.
     */
    public function test_10_checkin_se_refleja_en_detalle_y_calendario_eventos(): void
    {
        $cita = $this->crearCita();

        // Antes del check-in
        $resDetalleAntes = $this->actingAs($this->recepcionistaUser)
            ->getJson("/recepcion/citas/{$cita->id}/detalle")
            ->assertStatus(200);

        $this->assertFalse($resDetalleAntes->json('cita.checked_in'));
        $this->assertTrue($resDetalleAntes->json('cita.puede_check_in'));
        $this->assertNull($resDetalleAntes->json('cita.check_in_at'));

        // Realizar check-in
        $this->actingAs($this->recepcionistaUser)
            ->postJson("/recepcion/citas/{$cita->id}/checkin")
            ->assertStatus(200);

        // Después del check-in en detalle
        $resDetalleDespues = $this->actingAs($this->recepcionistaUser)
            ->getJson("/recepcion/citas/{$cita->id}/detalle")
            ->assertStatus(200);

        $this->assertTrue($resDetalleDespues->json('cita.checked_in'));
        $this->assertFalse($resDetalleDespues->json('cita.puede_check_in'));
        $this->assertNotNull($resDetalleDespues->json('cita.check_in_at'));
        $this->assertNotNull($resDetalleDespues->json('cita.check_in_hora'));

        // Después del check-in en eventos de calendario
        $resEventos = $this->actingAs($this->adminUser)
            ->getJson("/admin/calendario/eventos?start={$cita->fecha}&end={$cita->fecha}")
            ->assertStatus(200);

        $evento = collect($resEventos->json('eventos'))->firstWhere('id', $cita->id);
        $this->assertNotNull($evento);
        $this->assertTrue($evento['checked_in']);
        $this->assertNotNull($evento['check_in_at']);
    }

    /**
     * Test 11: Atomicidad en Service Layer y manejo de excepciones de dominio.
     */
    public function test_11_atomicidad_en_servicio_con_pessimistic_lock(): void
    {
        $cita = $this->crearCita();
        $servicio = app(CitaCalendarioService::class);

        // Ejecución válida
        $resultado = $servicio->registrarCheckIn($cita, $this->recepcionistaUser);
        $this->assertTrue($resultado['success']);
        $this->assertTrue($resultado['checked_in']);
        $this->assertNotNull($resultado['check_in_at']);

        // Intento directo en service sobre cita ya registrada lanza DomainException
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('La cita ya cuenta con check-in registrado previamente.');
        $servicio->registrarCheckIn($cita->fresh(), $this->adminUser);
    }

    /**
     * Test 12: Métodos helpers del modelo Cita para check-in y compatibilidad con reagendamiento.
     */
    public function test_12_metodos_helpers_del_modelo_cita_y_reagendamiento(): void
    {
        $cita = $this->crearCita();

        // Inicial: no checked-in, puede registrar
        $this->assertFalse($cita->isCheckedIn());
        $this->assertTrue($cita->puedeRegistrarCheckIn());
        $this->assertNull($cita->motivo_no_check_in);

        // Estampar check-in
        $cita->marcarCheckIn();
        $this->assertTrue($cita->isCheckedIn());
        $this->assertFalse($cita->puedeRegistrarCheckIn());
        $this->assertEquals('La cita ya cuenta con check-in registrado.', $cita->motivo_no_check_in);

        // Al reagendar a nueva fecha/hora con updateCita, check_in_at se restablece para requerir nueva llegada
        $servicio = app(CitaCalendarioService::class);
        $nuevaFecha = Carbon::today()->addDays(5)->format('Y-m-d');

        $resUpdate = $servicio->actualizarCita($cita, [
            'fecha' => $nuevaFecha,
            'hora_inicio' => '11:00',
            'servicio_id' => $this->servicio->id,
            'trabajador_id' => $this->trabajador->id,
        ], $this->recepcionistaUser);

        $this->assertTrue($resUpdate['success']);
        $this->assertNull($cita->fresh()->check_in_at);
        $this->assertFalse($cita->fresh()->isCheckedIn());
        $this->assertTrue($cita->fresh()->puedeRegistrarCheckIn());
    }
}
