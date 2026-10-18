<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\Service;
use App\Models\Trabajador;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImpedirCancelacionCitasPasadasTest extends TestCase
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

        $this->trabajador = Trabajador::firstOrCreate(
            ['user_id' => $this->trabajadorUser->id],
            [
                'direccion' => 'Av. Insurgentes 456, CDMX',
                'experiencia' => 6,
                'activo' => true,
                'horario' => [
                    'lunes' => ['entrada' => '09:00', 'salida' => '20:00'],
                    'martes' => ['entrada' => '09:00', 'salida' => '20:00'],
                    'miercoles' => ['entrada' => '09:00', 'salida' => '20:00'],
                    'jueves' => ['entrada' => '09:00', 'salida' => '20:00'],
                    'viernes' => ['entrada' => '09:00', 'salida' => '20:00'],
                    'sabado' => ['entrada' => '09:00', 'salida' => '20:00'],
                    'domingo' => ['entrada' => '09:00', 'salida' => '20:00'],
                ],
            ]
        );

        $this->servicio = Service::create([
            'name' => 'Corte Clásico Barber',
            'description' => 'Corte con lavado y peinado.',
            'price' => 250.00,
            'duration_minutes' => 30,
            'category' => 'barberia',
            'is_active' => true,
        ]);

        $this->trabajador->servicios()->syncWithoutDetaching([$this->servicio->id]);

        Cita::ensureTableExists();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    /**
     * Helper para crear citas.
     */
    protected function crearCita(array $atributos = []): Cita
    {
        return Cita::create(array_merge([
            'user_id' => $this->clienteUser->id,
            'trabajador_id' => $this->trabajador->id,
            'service_id' => $this->servicio->id,
            'fecha' => Carbon::today()->addDays(2)->format('Y-m-d'),
            'hora_inicio' => '10:00',
            'hora_fin' => '10:30',
            'duracion_minutos' => 30,
            'precio' => 250.00,
            'estado' => 'confirmada',
        ], $atributos));
    }

    /**
     * Escenario 1: Una cita cuya fecha y hora ya transcurrieron no permite la cancelación desde la interfaz.
     */
    public function test_escenario_1_cita_pasada_no_permite_cancelacion_desde_interfaz(): void
    {
        Carbon::setTestNow('2026-10-18 15:00:00');

        // Cita en fecha pasada (ayer)
        $citaAyer = $this->crearCita([
            'fecha' => '2026-10-17',
            'hora_inicio' => '10:00',
            'hora_fin' => '10:30',
            'estado' => 'confirmada',
        ]);

        // Cita de hoy cuya hora ya pasó (10:00 a 10:30, ahora es 15:00)
        $citaHoyPasada = $this->crearCita([
            'fecha' => '2026-10-18',
            'hora_inicio' => '10:00',
            'hora_fin' => '10:30',
            'estado' => 'confirmada',
        ]);

        // Cita futura (mañana)
        $citaFutura = $this->crearCita([
            'fecha' => '2026-10-19',
            'hora_inicio' => '10:00',
            'hora_fin' => '10:30',
            'estado' => 'confirmada',
        ]);

        // 1.1 Verificación en la interfaz de Cliente (historial)
        $respCliente = $this->actingAs($this->clienteUser)->get(route('cliente.historial'));
        $respCliente->assertStatus(200);

        // Para las citas pasadas, NO debe aparecer el botón de cancelar
        $respCliente->assertDontSee("data-id=\"{$citaAyer->id}\"\n                                    data-servicio", false);
        $respCliente->assertDontSee("data-id=\"{$citaHoyPasada->id}\"\n                                    data-servicio", false);
        // Debe mostrar el estado Vencida / no asistida para citas pasadas
        $respCliente->assertSee('Vencida');

        // Para la cita futura, SÍ debe estar el botón cancelar
        $respCliente->assertSee("data-id=\"{$citaFutura->id}\"", false);
        $respCliente->assertSee('Cancelar cita');

        // 1.2 Verificación en la interfaz de Recepción (listado de citas)
        $respRecepcion = $this->actingAs($this->recepcionistaUser)->get(route('recepcion.citas'));
        $respRecepcion->assertStatus(200);

        // En la tabla de recepción, las citas pasadas NO deben tener el botón de cancelar
        $respRecepcion->assertDontSee("cancelarCitaPrompt({$citaAyer->id},", false);
        $respRecepcion->assertDontSee("cancelarCitaPrompt({$citaHoyPasada->id},", false);

        // La cita futura SÍ debe tener el botón de cancelar
        $respRecepcion->assertSee("cancelarCitaPrompt({$citaFutura->id},", false);
    }

    /**
     * Escenario 2: Un intento de cancelación directa en el backend para una cita pasada es rechazado.
     */
    public function test_escenario_2_intento_cancelacion_directa_backend_para_cita_pasada_es_rechazado(): void
    {
        Carbon::setTestNow('2026-10-18 15:00:00');

        $citaPasada = $this->crearCita([
            'fecha' => '2026-10-17',
            'hora_inicio' => '11:00',
            'hora_fin' => '11:30',
            'estado' => 'confirmada',
        ]);

        // 2.1 Intento de cancelación directa por el cliente vía POST
        $respCliente = $this->actingAs($this->clienteUser)
            ->postJson("/cliente/citas/{$citaPasada->id}/cancelar");

        $respCliente->assertStatus(422);
        $respCliente->assertJson(['success' => false]);
        $this->assertStringContainsString('ya ha transcurrido', $respCliente->json('mensaje'));

        // 2.2 Intento de cancelación directa por recepcionista vía endpoint de cancelación
        $respRecepcion = $this->actingAs($this->recepcionistaUser)
            ->postJson("/recepcion/citas/{$citaPasada->id}/cancelar", [
                'motivo' => 'Intento recepcionista cita pasada',
            ]);

        $respRecepcion->assertStatus(422);
        $respRecepcion->assertJson(['success' => false]);
        $this->assertStringContainsString('ya ha transcurrido', $respRecepcion->json('mensaje') ?? $respRecepcion->json('error'));

        // 2.3 Intento de cambio de estado a 'cancelada' vía PATCH por recepcionista
        $respEstado = $this->actingAs($this->recepcionistaUser)
            ->patchJson("/recepcion/citas/{$citaPasada->id}/estado", [
                'estado' => 'cancelada',
                'observaciones' => 'Forzar cancelada',
            ]);

        $respEstado->assertStatus(422);
        $respEstado->assertJson(['success' => false]);
        $this->assertStringContainsString('ya ha transcurrido', $respEstado->json('mensaje') ?? $respEstado->json('error'));

        // 2.4 Intento de cancelación por administrador (solo lectura -> 403)
        $respAdmin = $this->actingAs($this->adminUser)
            ->postJson("/admin/citas/{$citaPasada->id}/cancelar");
        $respAdmin->assertStatus(403);
    }

    /**
     * Escenario 3: Una cancelación rechazada no modifica el estado ni el historial de la cita.
     */
    public function test_escenario_3_cancelacion_rechazada_no_modifica_estado_ni_historial(): void
    {
        Carbon::setTestNow('2026-10-18 15:00:00');

        $cita = $this->crearCita([
            'fecha' => '2026-10-18',
            'hora_inicio' => '09:00',
            'hora_fin' => '09:30',
            'estado' => 'confirmada',
            'observaciones' => 'Observación original intacta',
            'notas' => 'Nota cliente intacta',
        ]);

        $updatedAtOriginal = $cita->updated_at;

        // Intento de cancelación
        $response = $this->actingAs($this->recepcionistaUser)
            ->postJson("/recepcion/citas/{$cita->id}/cancelar", [
                'motivo' => 'Cancelar evento pasado',
            ]);

        $response->assertStatus(422);

        // Recargar datos desde la base de datos
        $cita->refresh();

        $this->assertEquals('confirmada', $cita->estado, 'El estado de la cita no debe cambiar tras una cancelación rechazada.');
        $this->assertEquals('Observación original intacta', $cita->observaciones, 'Las observaciones no deben modificarse.');
        $this->assertEquals('Nota cliente intacta', $cita->notas, 'Las notas no deben modificarse.');
        $this->assertDatabaseHas('citas', [
            'id' => $cita->id,
            'estado' => 'confirmada',
            'observaciones' => 'Observación original intacta',
        ]);
    }

    /**
     * Escenario 4: Una cita futura puede cancelarse cuando cumple las reglas vigentes del sistema.
     */
    public function test_escenario_4_cita_futura_puede_cancelarse_cuando_cumple_reglas(): void
    {
        Carbon::setTestNow('2026-10-18 10:00:00');

        // Cita para mañana
        $citaFutura = $this->crearCita([
            'fecha' => '2026-10-19',
            'hora_inicio' => '12:00',
            'hora_fin' => '12:30',
            'estado' => 'confirmada',
        ]);

        $this->assertTrue($citaFutura->puedeCancelar(), 'El cliente debe poder cancelar una cita futura con > 30 min.');
        $this->assertTrue($citaFutura->puedeCancelarAdministrativamente(), 'Recepción debe poder cancelar una cita futura.');

        // Recepcionista cancela la cita
        $response = $this->actingAs($this->recepcionistaUser)
            ->postJson("/recepcion/citas/{$citaFutura->id}/cancelar", [
                'motivo' => 'Cancelación justificada con anticipación',
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $citaFutura->refresh();
        $this->assertEquals('cancelada', $citaFutura->estado);
        $this->assertStringContainsString('Cancelación justificada', (string)$citaFutura->observaciones);
    }

    /**
     * Escenario 5: Una cita futura que incumple las restricciones de cancelación existentes continúa siendo rechazada.
     */
    public function test_escenario_5_cita_futura_que_incumple_restricciones_continua_siendo_rechazada(): void
    {
        Carbon::setTestNow('2026-10-18 10:00:00');

        // 5.1 Cita futura que inicia en 20 minutos (10:20). Incumple regla de > 30 min para clientes
        $citaProxima = $this->crearCita([
            'fecha' => '2026-10-18',
            'hora_inicio' => '10:20',
            'hora_fin' => '10:50',
            'estado' => 'confirmada',
        ]);

        $this->assertFalse($citaProxima->puedeCancelar(), 'Cliente no debe poder cancelar con <= 30 minutos de anticipación.');

        $respCliente = $this->actingAs($this->clienteUser)
            ->postJson("/cliente/citas/{$citaProxima->id}/cancelar");

        $respCliente->assertStatus(422);
        $this->assertStringContainsString('Solo se permite cancelar con más de 30 minutos', $respCliente->json('mensaje'));

        // 5.2 Cita que ya fue completada (no puede ser cancelada)
        $citaCompletada = $this->crearCita([
            'fecha' => '2026-10-19',
            'hora_inicio' => '16:00',
            'hora_fin' => '16:30',
            'estado' => 'completada',
        ]);

        $respCompletada = $this->actingAs($this->recepcionistaUser)
            ->postJson("/recepcion/citas/{$citaCompletada->id}/cancelar");

        $respCompletada->assertStatus(422);
        $this->assertStringContainsString('completada', $respCompletada->json('mensaje') ?? $respCompletada->json('error'));
    }

    /**
     * Escenario 6: La comparación considera correctamente la fecha y la hora,
     * incluyendo citas del día actual cuya hora ya pasó y citas del mismo día cuya hora todavía no llega.
     */
    public function test_escenario_6_comparacion_considera_fecha_y_hora_mismo_dia(): void
    {
        // Fijar fecha y hora actual en 2026-10-18 a las 14:00
        Carbon::setTestNow('2026-10-18 14:00:00');

        // Cita A: Mismo día a las 10:00 (hora ya pasó, las 10:00 son anteriores a las 14:00)
        $citaMismoDiaPasada = $this->crearCita([
            'fecha' => '2026-10-18',
            'hora_inicio' => '10:00',
            'hora_fin' => '10:30',
            'estado' => 'confirmada',
        ]);

        // Cita B: Mismo día a las 18:00 (hora todavía no llega, las 18:00 son posteriores a las 14:00)
        $citaMismoDiaFutura = $this->crearCita([
            'fecha' => '2026-10-18',
            'hora_inicio' => '18:00',
            'hora_fin' => '18:30',
            'estado' => 'confirmada',
        ]);

        // Comprobación de métodos del modelo Cita
        $this->assertTrue($citaMismoDiaPasada->esPasada(), 'Cita de hoy a las 10:00 debe considerarse pasada a las 14:00.');
        $this->assertFalse($citaMismoDiaPasada->puedeCancelar(), 'Cita de hoy a las 10:00 no debe poder cancelarse a las 14:00.');
        $this->assertFalse($citaMismoDiaPasada->puedeCancelarAdministrativamente(), 'Cita de hoy a las 10:00 no debe ser cancelable administrativamente.');

        $this->assertFalse($citaMismoDiaFutura->esPasada(), 'Cita de hoy a las 18:00 NO debe considerarse pasada a las 14:00.');
        $this->assertTrue($citaMismoDiaFutura->puedeCancelar(), 'Cita de hoy a las 18:00 con 4 horas de anticipación debe poder cancelarse.');
        $this->assertTrue($citaMismoDiaFutura->puedeCancelarAdministrativamente(), 'Cita de hoy a las 18:00 debe poder cancelarse administrativamente.');

        // Intentar cancelar la cita A (misma fecha, hora ya transcurrida) -> Debe ser rechazada
        $respA = $this->actingAs($this->recepcionistaUser)
            ->postJson("/recepcion/citas/{$citaMismoDiaPasada->id}/cancelar", [
                'motivo' => 'Prueba cancelación hora pasada mismo día',
            ]);
        $respA->assertStatus(422);
        $this->assertStringContainsString('ya ha transcurrido', $respA->json('mensaje') ?? $respA->json('error'));
        $this->assertEquals('confirmada', $citaMismoDiaPasada->fresh()->estado);

        // Cancelar la cita B (misma fecha, hora aún no llega) -> Debe permitirse
        $respB = $this->actingAs($this->recepcionistaUser)
            ->postJson("/recepcion/citas/{$citaMismoDiaFutura->id}/cancelar", [
                'motivo' => 'Cliente avisó con 4 horas de anticipación',
            ]);
        $respB->assertStatus(200);
        $this->assertEquals('cancelada', $citaMismoDiaFutura->fresh()->estado);
    }

    /**
     * Escenario 7: Las demás funciones relacionadas con las citas siguen funcionando correctamente.
     */
    public function test_escenario_7_demas_funciones_de_citas_siguen_funcionando_correctamente(): void
    {
        Carbon::setTestNow('2026-10-18 10:00:00');

        $cita = $this->crearCita([
            'fecha' => '2026-10-18',
            'hora_inicio' => '10:00',
            'hora_fin' => '10:30',
            'estado' => 'confirmada',
        ]);

        // 7.1 Consultar detalle de cita por recepción
        $respDetalle = $this->actingAs($this->recepcionistaUser)
            ->getJson("/recepcion/citas/{$cita->id}/detalle");
        $respDetalle->assertStatus(200);
        $respDetalle->assertJsonPath('cita.folio', $cita->folio);

        // 7.2 Iniciar atención (cambio de estado a en_atencion)
        $respIniciar = $this->actingAs($this->recepcionistaUser)
            ->patchJson("/recepcion/citas/{$cita->id}/estado", [
                'estado' => 'en_proceso',
            ]);

        $respIniciar->assertStatus(200);
        $this->assertEquals('en_atencion', $cita->fresh()->estado);

        // 7.3 Finalizar atención (cambio de estado a completada)
        $respCompletar = $this->actingAs($this->recepcionistaUser)
            ->patchJson("/recepcion/citas/{$cita->id}/estado", [
                'estado' => 'completada',
                'observaciones' => 'Atención finalizada exitosamente',
            ]);

        $respCompletar->assertStatus(200);
        $this->assertEquals('completada', $cita->fresh()->estado);
    }
}
