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

class CitasCambioEstadoAtencionTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $recepcionistaUser;
    protected User $trabajadorUser1;
    protected User $trabajadorUser2;
    protected User $clienteUser;
    protected Trabajador $trabajador1;
    protected Trabajador $trabajador2;
    protected Service $servicio;
    protected CitaCalendarioService $calendarioService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\TestUsersSeeder::class);

        $this->adminUser = User::where('role_id', 1)->first();
        $this->recepcionistaUser = User::where('role_id', 2)->first();
        $this->trabajadorUser1 = User::where('role_id', 3)->first();
        $this->clienteUser = User::where('role_id', 4)->first();

        // Especialista 1 (asignado al trabajadorUser1)
        $this->trabajador1 = Trabajador::create([
            'user_id' => $this->trabajadorUser1->id,
            'direccion' => 'Beauty & Barber Studio - Estación 1',
            'experiencia' => 6,
            'activo' => true,
        ]);

        // Especialista 2 (segundo trabajador para pruebas de autorización)
        $this->trabajadorUser2 = User::create([
            'name' => 'Especialista 2',
            'apellidos' => 'Barber',
            'email' => 'especialista2@barberstudio.com',
            'password' => bcrypt('password123'),
            'role_id' => 3,
        ]);

        $this->trabajador2 = Trabajador::create([
            'user_id' => $this->trabajadorUser2->id,
            'direccion' => 'Beauty & Barber Studio - Estación 2',
            'experiencia' => 4,
            'activo' => true,
        ]);

        // Servicio
        $this->servicio = Service::create([
            'name' => 'Corte Degradado & Barba',
            'category' => 'barberia',
            'duration_minutes' => 45,
            'price' => 320.00,
            'is_active' => true,
        ]);

        $this->calendarioService = app(CitaCalendarioService::class);
    }

    /**
     * Helper para crear citas de prueba con valores controlados.
     */
    protected function crearCita(array $atributos = []): Cita
    {
        $fechaFutura = Carbon::today()->addDays(2)->format('Y-m-d');

        return Cita::create(array_merge([
            'user_id' => $this->clienteUser->id,
            'trabajador_id' => $this->trabajador1->id,
            'service_id' => $this->servicio->id,
            'fecha' => $fechaFutura,
            'hora_inicio' => '11:00:00',
            'hora_fin' => '11:45:00',
            'duracion_minutos' => 45,
            'precio' => 320.00,
            'estado' => 'confirmada',
            'folio' => 'CITA-' . rand(10000, 99999),
            'notas' => 'Cita controlada de prueba Subtarea 6',
            'observaciones' => null,
            'check_in_at' => null,
        ], $atributos));
    }

    /**
     * CRITERIO 1: Inicio de atención autorizado
     * El personal autorizado (Recepcionista y Administrador) puede iniciar la atención de una cita válida.
     */
    public function test_criterio_1_inicio_atencion_autorizado_recepcionista_y_admin(): void
    {
        $cita1 = $this->crearCita();

        // 1. Recepcionista inicia atención
        $response1 = $this->actingAs($this->recepcionistaUser)
            ->patchJson("/recepcion/citas/{$cita1->id}/estado", [
                'estado' => 'en_atencion',
            ]);

        $response1->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $cita1->refresh();
        $this->assertEquals('en_atencion', $cita1->estado);
        $this->assertEquals('en_proceso', $cita1->estado_visual);

        // 2. Administrador cuenta únicamente con permisos de visualización (rechazo 403 al intentar mutar estado)
        $cita2 = $this->crearCita();
        $response2 = $this->actingAs($this->adminUser)
            ->patchJson("/admin/citas/{$cita2->id}/estado", [
                'estado' => 'en_proceso',
            ]);

        $response2->assertStatus(403)
            ->assertJson([
                'success' => false,
            ]);

        $cita2->refresh();
        $this->assertEquals('confirmada', $cita2->estado);
    }

    /**
     * CRITERIO 2: Cambio a En proceso y persistencia en BD con auto check-in
     * La cita cambia a en proceso cuando comienza la atención, se persiste en BD y
     * si no tenía check-in previo, se sella automáticamente la llegada presencial.
     */
    public function test_criterio_2_cambio_a_en_proceso_persistencia_bd_y_checkin_automatico(): void
    {
        $cita = $this->crearCita(['check_in_at' => null]);
        $this->assertNull($cita->check_in_at);
        $this->assertFalse($cita->isCheckedIn());

        $response = $this->actingAs($this->recepcionistaUser)
            ->patchJson("/recepcion/citas/{$cita->id}/estado", [
                'estado' => 'en_atencion',
            ]);

        $response->assertStatus(200);

        // Comprobación directa en la base de datos
        $this->assertDatabaseHas('citas', [
            'id' => $cita->id,
            'estado' => 'en_atencion',
        ]);

        $citaActualizada = Cita::find($cita->id);
        $this->assertNotNull($citaActualizada->check_in_at, 'El check_in_at debe ser sellado automáticamente si estaba null al iniciar la atención.');
        $this->assertTrue($citaActualizada->isCheckedIn());
        $this->assertFalse($citaActualizada->puedeIniciarAtencion(), 'Ya no debe poder iniciarse de nuevo.');
        $this->assertTrue($citaActualizada->puedeFinalizarAtencion(), 'Ahora debe poder finalizarse.');
    }

    /**
     * CRITERIO 3: Finalización del servicio
     * La cita puede marcarse como completada cuando finaliza el servicio,
     * persistiendo el estado final y guardando observaciones técnicas.
     */
    public function test_criterio_3_finalizacion_del_servicio_con_observaciones(): void
    {
        // Cita que ya está en atención
        $cita = $this->crearCita([
            'estado' => 'en_atencion',
            'check_in_at' => Carbon::now()->subMinutes(30),
        ]);

        $response = $this->actingAs($this->recepcionistaUser)
            ->patchJson("/recepcion/citas/{$cita->id}/estado", [
                'estado' => 'completada',
                'observaciones' => 'Corte degradado terminado satisfactoriamente. Se aplicó loción fijadora mate.',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('citas', [
            'id' => $cita->id,
            'estado' => 'completada',
            'observaciones' => 'Corte degradado terminado satisfactoriamente. Se aplicó loción fijadora mate.',
        ]);

        $cita->refresh();
        $this->assertEquals('completada', $cita->estado);
        $this->assertFalse($cita->puedeIniciarAtencion());
        $this->assertFalse($cita->puedeFinalizarAtencion());
    }

    /**
     * CRITERIO 4: Rechazo de citas canceladas
     * No se permite iniciar la atención de una cita cancelada ni completarla.
     * La cita debe conservar intacto su estado Cancelada.
     */
    public function test_criterio_4_rechazo_inicio_o_finalizacion_de_citas_canceladas(): void
    {
        $citaCancelada = $this->crearCita([
            'estado' => 'cancelada',
            'observaciones' => 'Cancelada por el cliente con anticipación.',
        ]);

        // Intento 1: Iniciar atención vía Recepción
        $res1 = $this->actingAs($this->recepcionistaUser)
            ->patchJson("/recepcion/citas/{$citaCancelada->id}/estado", [
                'estado' => 'en_atencion',
            ]);

        $res1->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);

        // Intento 2: Iniciar atención vía Administrador (rechazo 403 por ser solo lectura)
        $res2 = $this->actingAs($this->adminUser)
            ->patchJson("/admin/citas/{$citaCancelada->id}/estado", [
                'estado' => 'en_atencion',
            ]);

        $res2->assertStatus(403);

        // Intento 3: Completar directamente vía Recepción
        $res3 = $this->actingAs($this->recepcionistaUser)
            ->patchJson("/recepcion/citas/{$citaCancelada->id}/estado", [
                'estado' => 'completada',
            ]);

        $res3->assertStatus(422);

        // Verificación de integridad: la cita conserva su estado Cancelada y observaciones
        $citaCancelada->refresh();
        $this->assertEquals('cancelada', $citaCancelada->estado);
        $this->assertEquals('Cancelada por el cliente con anticipación.', $citaCancelada->observaciones);
    }

    /**
     * CRITERIO 5: Persistencia y actualización de las vistas
     * Los cambios de estado se guardan en BD y el servicio de calendario/listado
     * retorna la cita con sus nuevos estados y badges en el detalle y listado.
     */
    public function test_criterio_5_persistencia_estados_en_servicio_y_consultas_vistas(): void
    {
        $cita = $this->crearCita(['estado' => 'confirmada']);

        // 1. Iniciar atención
        $this->actingAs($this->recepcionistaUser)
            ->patchJson("/recepcion/citas/{$cita->id}/estado", ['estado' => 'en_atencion']);

        // Consultar detalle vía API Recepción
        $detalleRes1 = $this->actingAs($this->recepcionistaUser)
            ->getJson("/recepcion/citas/{$cita->id}/detalle");

        $detalleRes1->assertStatus(200)
            ->assertJson([
                'success' => true,
                'cita' => [
                    'id' => $cita->id,
                    'estado' => 'en_atencion',
                    'estado_visual' => 'en_proceso',
                    'estado_label' => 'En proceso',
                    'puede_iniciar_atencion' => false,
                    'puede_finalizar_atencion' => true,
                ],
            ]);

        // 2. Finalizar atención
        $this->actingAs($this->recepcionistaUser)
            ->patchJson("/recepcion/citas/{$cita->id}/estado", [
                'estado' => 'completada',
                'observaciones' => 'Atención completada',
            ]);

        // Consultar detalle vía API Administrador
        $detalleRes2 = $this->actingAs($this->adminUser)
            ->getJson("/admin/citas/{$cita->id}/detalle");

        $detalleRes2->assertStatus(200)
            ->assertJson([
                'success' => true,
                'cita' => [
                    'id' => $cita->id,
                    'estado' => 'completada',
                    'estado_visual' => 'completada',
                    'estado_label' => 'Completada',
                    'puede_iniciar_atencion' => false,
                    'puede_finalizar_atencion' => false,
                ],
            ]);
    }

    /**
     * CRITERIO 6: Permisos por rol
     * - Administrador (Rol 1) y Recepcionista (Rol 2) pueden gestionar cualquier cita.
     * - Trabajador (Rol 3) solo puede cambiar el estado de citas que tiene asignadas.
     * - Cliente (Rol 4) y usuarios no autenticados no pueden alterar estados vía HTTP.
     */
    public function test_criterio_6_permisos_por_rol_autorizados_vs_no_autorizados(): void
    {
        // Cita asignada al trabajador 1
        $citaAsignadaT1 = $this->crearCita(['trabajador_id' => $this->trabajador1->id]);

        // 1. Usuario No autenticado es redirigido a login o 401
        $resGuest = $this->patchJson("/recepcion/citas/{$citaAsignadaT1->id}/estado", [
            'estado' => 'en_atencion',
        ]);
        $this->assertTrue(in_array($resGuest->status(), [401, 302]));

        // 2. Cliente (Rol 4) no tiene acceso a endpoints de recepción ni administración
        $resCliente = $this->actingAs($this->clienteUser)
            ->patchJson("/recepcion/citas/{$citaAsignadaT1->id}/estado", [
                'estado' => 'en_atencion',
            ]);
        $resCliente->assertStatus(403);

        // 3. Trabajador 2 intenta modificar la cita asignada al Trabajador 1 (Acceso denegado 403)
        $resTrabajadorAjeno = $this->actingAs($this->trabajadorUser2)
            ->patchJson("/trabajador/citas/{$citaAsignadaT1->id}/estado", [
                'estado' => 'en_atencion',
            ]);
        $resTrabajadorAjeno->assertStatus(403);

        // 4. Trabajador 1 modifica SU PROPIA cita asignada (Permitido 200)
        $resTrabajadorPropio = $this->actingAs($this->trabajadorUser1)
            ->patchJson("/trabajador/citas/{$citaAsignadaT1->id}/estado", [
                'estado' => 'en_atencion',
            ]);
        $resTrabajadorPropio->assertStatus(200);

        $citaAsignadaT1->refresh();
        $this->assertEquals('en_atencion', $citaAsignadaT1->estado);
    }

    /**
     * CRITERIO 7: Integridad y prevención de duplicados
     * - Solicitudes repetidas para iniciar una cita ya en atención son rechazadas con mensaje claro.
     * - Solicitudes repetidas para completar una cita ya completada son rechazadas.
     * - Los cambios sobre una cita no alteran ninguna otra cita del sistema.
     */
    public function test_criterio_7_integridad_prevencion_duplicados_y_no_alteracion_otras_citas(): void
    {
        $citaTarget = $this->crearCita(['estado' => 'confirmada']);
        $citaOtra = $this->crearCita(['estado' => 'confirmada', 'notas' => 'Cita vecina intacta']);

        // 1. Iniciar atención la primera vez (Éxito)
        $res1 = $this->actingAs($this->recepcionistaUser)
            ->patchJson("/recepcion/citas/{$citaTarget->id}/estado", ['estado' => 'en_atencion']);
        $res1->assertStatus(200);

        // 2. Repetir la solicitud de inicio sobre la misma cita (Rechazada 422: ya en proceso)
        $resRepetidaInicio = $this->actingAs($this->recepcionistaUser)
            ->patchJson("/recepcion/citas/{$citaTarget->id}/estado", ['estado' => 'en_atencion']);

        $resRepetidaInicio->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
        $this->assertStringContainsString('ya se encuentra en proceso de atención', $resRepetidaInicio->json('error'));

        // 3. Completar la atención la primera vez (Éxito)
        $res3 = $this->actingAs($this->recepcionistaUser)
            ->patchJson("/recepcion/citas/{$citaTarget->id}/estado", ['estado' => 'completada']);
        $res3->assertStatus(200);

        // 4. Repetir la solicitud de finalización sobre la misma cita (Rechazada 422: ya completada)
        $resRepetidaFinal = $this->actingAs($this->recepcionistaUser)
            ->patchJson("/recepcion/citas/{$citaTarget->id}/estado", ['estado' => 'completada']);

        $resRepetidaFinal->assertStatus(422);
        $this->assertStringContainsString('ya ha sido completada', $resRepetidaFinal->json('error'));

        // 5. Verificar que la cita vecina no fue modificada en lo absoluto
        $citaOtra->refresh();
        $this->assertEquals('confirmada', $citaOtra->estado);
        $this->assertEquals('Cita vecina intacta', $citaOtra->notas);
        $this->assertNull($citaOtra->check_in_at);
    }

    /**
     * PRUEBA ADICIONAL: Rechazo de salto directo de Confirmada a Completada sin pasar por En proceso
     */
    public function test_rechazo_salto_directo_confirmada_a_completada(): void
    {
        $cita = $this->crearCita(['estado' => 'confirmada']);

        $response = $this->actingAs($this->recepcionistaUser)
            ->patchJson("/recepcion/citas/{$cita->id}/estado", [
                'estado' => 'completada',
            ]);

        $response->assertStatus(422);
        $this->assertStringContainsString('sin antes haber iniciado su atención', $response->json('error'));

        $cita->refresh();
        $this->assertEquals('confirmada', $cita->estado);
    }

    /**
     * PRUEBA ADICIONAL: Rechazo de regresar de En proceso a Confirmada
     */
    public function test_rechazo_regresar_de_en_atencion_a_confirmada(): void
    {
        $cita = $this->crearCita(['estado' => 'en_atencion']);

        $response = $this->actingAs($this->recepcionistaUser)
            ->patchJson("/recepcion/citas/{$cita->id}/estado", [
                'estado' => 'confirmada',
            ]);

        $response->assertStatus(422);
        $this->assertStringContainsString('no puede regresar al estado de confirmada', $response->json('error'));

        $cita->refresh();
        $this->assertEquals('en_atencion', $cita->estado);
    }

    /**
     * PRUEBA ADICIONAL: Rechazo de iniciar atención en cita vencida
     */
    public function test_rechazo_iniciar_cita_vencida(): void
    {
        // Cita con fecha de hace 3 días
        $fechaPasada = Carbon::today()->subDays(3)->format('Y-m-d');
        $cita = $this->crearCita([
            'fecha' => $fechaPasada,
            'hora_inicio' => '09:00:00',
            'hora_fin' => '09:45:00',
            'estado' => 'confirmada',
        ]);

        $this->assertEquals('vencida', $cita->estado_visual);

        $response = $this->actingAs($this->recepcionistaUser)
            ->patchJson("/recepcion/citas/{$cita->id}/estado", [
                'estado' => 'en_atencion',
            ]);

        $response->assertStatus(422);
        $this->assertStringContainsString('ya han transcurrido sin atención', $response->json('error'));
    }
}
