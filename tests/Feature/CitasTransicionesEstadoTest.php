<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\Service;
use App\Models\Trabajador;
use App\Models\User;
use App\Services\CitaCalendarioService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CitasTransicionesEstadoTest extends TestCase
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

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\TestUsersSeeder::class);

        $this->adminUser = User::where('role_id', 1)->first();
        $this->recepcionistaUser = User::where('role_id', 2)->first();
        $this->trabajadorUser1 = User::where('role_id', 3)->first();
        $this->clienteUser = User::where('role_id', 4)->first();

        // Especialista 1
        $this->trabajador1 = Trabajador::create([
            'user_id' => $this->trabajadorUser1->id,
            'direccion' => 'Beauty & Barber Studio - Estación 1',
            'experiencia' => 5,
            'activo' => true,
        ]);

        // Especialista 2
        $this->trabajadorUser2 = User::create([
            'name' => 'Marcos',
            'apellidos' => 'Herrera',
            'email' => 'marcos.herrera@barberstudio.com',
            'password' => bcrypt('password123'),
            'role_id' => 3,
        ]);

        $this->trabajador2 = Trabajador::create([
            'user_id' => $this->trabajadorUser2->id,
            'direccion' => 'Beauty & Barber Studio - Estación 2',
            'experiencia' => 4,
            'activo' => true,
        ]);

        // Servicio base
        $this->servicio = Service::create([
            'name' => 'Corte Degradado Barber',
            'category' => 'barberia',
            'duration_minutes' => 45,
            'price' => 250.00,
            'is_active' => true,
        ]);

        Cita::ensureTableExists();
    }

    /**
     * Helper para crear citas en fechas y estados específicos.
     */
    protected function crearCita(string $estado = 'confirmada', ?string $fecha = null, string $horaInicio = '10:00', string $horaFin = '10:45', ?int $trabajadorId = null): Cita
    {
        $fechaCita = $fecha ?: Carbon::today()->addDays(2)->format('Y-m-d');

        return Cita::create([
            'user_id' => $this->clienteUser->id,
            'trabajador_id' => $trabajadorId ?: $this->trabajador1->id,
            'service_id' => $this->servicio->id,
            'fecha' => $fechaCita,
            'hora_inicio' => $horaInicio,
            'hora_fin' => $horaFin,
            'duracion_minutos' => 45,
            'precio' => 250.00,
            'estado' => $estado,
            'notas' => 'Cita de prueba',
            'observaciones' => null,
        ]);
    }

    /**
     * 1. Transiciones permitidas: Confirmada -> En proceso / En atención y Confirmada -> Cancelada.
     */
    public function test_1_transiciones_permitidas_confirmada_a_en_proceso_y_cancelada(): void
    {
        $cita1 = $this->crearCita(estado: 'confirmada');
        $cita2 = $this->crearCita(estado: 'confirmada');

        // Confirmada -> En proceso (Recepcionista)
        $res1 = $this->actingAs($this->recepcionistaUser)->patchJson("/recepcion/citas/{$cita1->id}/estado", [
            'estado' => 'en_proceso',
            'observaciones' => 'Atención iniciada en cabina 1',
        ]);
        $res1->assertStatus(200);
        $res1->assertJsonPath('success', true);
        $this->assertEquals('en_atencion', $cita1->fresh()->estado);

        // Confirmada -> Cancelada (Recepcionista)
        $res2 = $this->actingAs($this->recepcionistaUser)->patchJson("/recepcion/citas/{$cita2->id}/estado", [
            'estado' => 'cancelada',
            'observaciones' => 'Cancelación solicitada por el cliente',
        ]);
        $res2->assertStatus(200);
        $res2->assertJsonPath('success', true);
        $this->assertEquals('cancelada', $cita2->fresh()->estado);

        // Administrador cuenta únicamente con permisos de visualización (rechazo 403)
        $citaAdmin = $this->crearCita(estado: 'confirmada');
        $resAdmin = $this->actingAs($this->adminUser)->patchJson("/admin/citas/{$citaAdmin->id}/estado", [
            'estado' => 'cancelada',
        ]);
        $resAdmin->assertStatus(403);
    }

    /**
     * 2. Transiciones permitidas: En proceso -> Completada y En proceso -> Cancelada.
     */
    public function test_2_transiciones_permitidas_en_proceso_a_completada_y_cancelada(): void
    {
        $cita1 = $this->crearCita(estado: 'en_atencion');
        $cita2 = $this->crearCita(estado: 'en_atencion');

        // En atención -> Completada
        $res1 = $this->actingAs($this->recepcionistaUser)->patchJson("/recepcion/citas/{$cita1->id}/estado", [
            'estado' => 'completada',
            'observaciones' => 'Servicio concluido a satisfacción',
        ]);
        $res1->assertStatus(200);
        $res1->assertJsonPath('success', true);
        $this->assertEquals('completada', $cita1->fresh()->estado);

        // En atención -> Cancelada (Interrupción justificada por Recepcionista)
        $res2 = $this->actingAs($this->recepcionistaUser)->patchJson("/recepcion/citas/{$cita2->id}/estado", [
            'estado' => 'cancelada',
            'observaciones' => 'Servicio interrumpido por emergencia',
        ]);
        $res2->assertStatus(200);
        $res2->assertJsonPath('success', true);
        $this->assertEquals('cancelada', $cita2->fresh()->estado);

        // Administrador cuenta únicamente con permisos de visualización (rechazo 403)
        $resAdmin = $this->actingAs($this->adminUser)->patchJson("/admin/citas/{$cita1->id}/estado", [
            'estado' => 'cancelada',
        ]);
        $resAdmin->assertStatus(403);
    }

    /**
     * 3. Rechazo de transición no permitida: Confirmada -> Completada directamente.
     */
    public function test_3_rechazo_transicion_invalida_confirmada_a_completada(): void
    {
        $cita = $this->crearCita(estado: 'confirmada');

        $response = $this->actingAs($this->recepcionistaUser)->patchJson("/recepcion/citas/{$cita->id}/estado", [
            'estado' => 'completada',
        ]);

        $response->assertStatus(422);
        $response->assertJsonPath('success', false);
        $response->assertJsonFragment([
            'error' => 'Una cita confirmada no puede pasar directamente a completada sin antes haber iniciado su atención.',
        ]);

        // Verificar que el estado original en la base de datos se mantiene intacto
        $this->assertEquals('confirmada', $cita->fresh()->estado);
    }

    /**
     * 4. Rechazo de transiciones inválidas desde En atención: En atención -> Confirmada y En atención -> Vencida.
     */
    public function test_4_rechazo_transiciones_invalidas_desde_en_atencion(): void
    {
        $cita = $this->crearCita(estado: 'en_atencion');

        // En atención -> Confirmada (No permitido regresar)
        $res1 = $this->actingAs($this->recepcionistaUser)->patchJson("/recepcion/citas/{$cita->id}/estado", [
            'estado' => 'confirmada',
        ]);
        $res1->assertStatus(422);
        $res1->assertJsonFragment([
            'error' => 'Una cita que ya ha iniciado atención no puede regresar al estado de confirmada.',
        ]);

        // En atención -> Vencida (No permitido)
        $res2 = $this->actingAs($this->recepcionistaUser)->patchJson("/recepcion/citas/{$cita->id}/estado", [
            'estado' => 'vencida',
        ]);
        $res2->assertStatus(422);
        $res2->assertJsonFragment([
            'error' => 'Una cita que ya está en proceso de atención no puede marcarse como vencida.',
        ]);

        $this->assertEquals('en_atencion', $cita->fresh()->estado);
    }

    /**
     * 5. Rechazo de transiciones desde estado terminal Cancelada (Inmutabilidad).
     */
    public function test_5_rechazo_transiciones_desde_estado_terminal_cancelada(): void
    {
        $cita = $this->crearCita(estado: 'cancelada');

        // Intento 1: Cancelada -> Confirmada
        $res1 = $this->actingAs($this->recepcionistaUser)->patchJson("/recepcion/citas/{$cita->id}/estado", [
            'estado' => 'confirmada',
        ]);
        $res1->assertStatus(422);
        $res1->assertJsonFragment([
            'error' => 'Una cita cancelada no puede reactivarse ni cambiarse a otro estado.',
        ]);

        // Intento 2: Cancelada -> En proceso
        $res2 = $this->actingAs($this->recepcionistaUser)->patchJson("/recepcion/citas/{$cita->id}/estado", [
            'estado' => 'en_proceso',
        ]);
        $res2->assertStatus(422);
        $res2->assertJsonFragment([
            'error' => 'Una cita cancelada no puede reactivarse ni cambiarse a otro estado.',
        ]);

        // Intento 3: Cancelada -> Completada
        $res3 = $this->actingAs($this->recepcionistaUser)->patchJson("/recepcion/citas/{$cita->id}/estado", [
            'estado' => 'completada',
        ]);
        $res3->assertStatus(422);
        $res3->assertJsonFragment([
            'error' => 'Una cita cancelada no puede reactivarse ni cambiarse a otro estado.',
        ]);

        $this->assertEquals('cancelada', $cita->fresh()->estado);
    }

    /**
     * 6. Rechazo de transiciones desde estado terminal Completada (Inmutabilidad).
     */
    public function test_6_rechazo_transiciones_desde_estado_terminal_completada(): void
    {
        $cita = $this->crearCita(estado: 'completada');

        // Intento 1: Completada -> Confirmada
        $res1 = $this->actingAs($this->recepcionistaUser)->patchJson("/recepcion/citas/{$cita->id}/estado", [
            'estado' => 'confirmada',
        ]);
        $res1->assertStatus(422);
        $res1->assertJsonFragment([
            'error' => 'Una cita completada es un registro final y no puede volver a confirmada, en atención ni cancelada.',
        ]);

        // Intento 2: Completada -> En proceso
        $res2 = $this->actingAs($this->recepcionistaUser)->patchJson("/recepcion/citas/{$cita->id}/estado", [
            'estado' => 'en_proceso',
        ]);
        $res2->assertStatus(422);
        $res2->assertJsonFragment([
            'error' => 'Una cita completada es un registro final y no puede volver a confirmada, en atención ni cancelada.',
        ]);

        // Intento 3: Completada -> Cancelada
        $res3 = $this->actingAs($this->recepcionistaUser)->patchJson("/recepcion/citas/{$cita->id}/estado", [
            'estado' => 'cancelada',
        ]);
        $res3->assertStatus(422);
        $res3->assertJsonFragment([
            'error' => 'Una cita completada es un registro final y no puede volver a confirmada, en atención ni cancelada.',
        ]);

        $this->assertEquals('completada', $cita->fresh()->estado);
    }

    /**
     * 7. Rechazo de valores de estado desconocidos o inventados.
     */
    public function test_7_rechazo_de_valores_de_estado_desconocidos(): void
    {
        $cita = $this->crearCita(estado: 'confirmada');

        $response = $this->actingAs($this->recepcionistaUser)->patchJson("/recepcion/citas/{$cita->id}/estado", [
            'estado' => 'estado_ficticio_no_existente',
        ]);

        $response->assertStatus(422);
        $this->assertEquals('confirmada', $cita->fresh()->estado);
    }

    /**
     * 8. Persistencia y atomicidad de cambios válidos y observaciones.
     */
    public function test_8_persistencia_de_cambios_validos_y_observaciones(): void
    {
        $cita = $this->crearCita(estado: 'confirmada');

        $response = $this->actingAs($this->recepcionistaUser)->patchJson("/recepcion/citas/{$cita->id}/estado", [
            'estado' => 'en_proceso',
            'observaciones' => 'Cliente solicita corte con máquina 2 en costados.',
        ]);

        $response->assertStatus(200);

        $citaActualizada = $cita->fresh();
        $this->assertEquals('en_atencion', $citaActualizada->estado);
        $this->assertEquals('Cliente solicita corte con máquina 2 en costados.', $citaActualizada->observaciones);
    }

    /**
     * 9. Ausencia de modificaciones y rollback tras un intento de transición rechazado.
     */
    public function test_9_ausencia_de_modificaciones_tras_intento_rechazado(): void
    {
        $cita = $this->crearCita(estado: 'cancelada');
        $cita->observaciones = 'Observación original antes del intento';
        $cita->save();

        $updatedAtOriginal = $cita->fresh()->updated_at;

        $response = $this->actingAs($this->recepcionistaUser)->patchJson("/recepcion/citas/{$cita->id}/estado", [
            'estado' => 'confirmada',
            'observaciones' => 'Texto malicioso que no debe guardarse',
        ]);

        $response->assertStatus(422);

        $citaDespues = $cita->fresh();
        $this->assertEquals('cancelada', $citaDespues->estado);
        $this->assertEquals('Observación original antes del intento', $citaDespues->observaciones);
        $this->assertEquals($updatedAtOriginal->toISOString(), $citaDespues->updated_at->toISOString());
    }

    /**
     * 10. Tratamiento de citas vencidas: No pueden iniciarse ni completarse como citas ordinarias.
     */
    public function test_10_tratamiento_de_citas_vencidas(): void
    {
        // Cita con fecha en el pasado
        $fechaPasada = Carbon::yesterday()->format('Y-m-d');
        $citaPasada = $this->crearCita(estado: 'confirmada', fecha: $fechaPasada, horaInicio: '09:00', horaFin: '09:45');

        // Intento de iniciar una cita vencida
        $res = $this->actingAs($this->recepcionistaUser)->patchJson("/recepcion/citas/{$citaPasada->id}/estado", [
            'estado' => 'en_proceso',
        ]);

        $res->assertStatus(422);
        $res->assertJsonFragment([
            'error' => 'La fecha y horario de la cita ya han transcurrido sin atención. No puede iniciarse ni completarse como una cita ordinaria.',
        ]);

        // Cita con estado persistido vencida
        $citaVencidaDb = $this->crearCita(estado: 'vencida');
        $res2 = $this->actingAs($this->recepcionistaUser)->patchJson("/recepcion/citas/{$citaVencidaDb->id}/estado", [
            'estado' => 'completada',
        ]);
        $res2->assertStatus(422);

        // Sin embargo, una cita vencida SÍ puede ser formalmente cancelada
        $res3 = $this->actingAs($this->recepcionistaUser)->patchJson("/recepcion/citas/{$citaVencidaDb->id}/estado", [
            'estado' => 'cancelada',
            'observaciones' => 'Cancelación formal por inasistencia',
        ]);
        $res3->assertStatus(200);
        $this->assertEquals('cancelada', $citaVencidaDb->fresh()->estado);
    }

    /**
     * 11. Restricciones de permisos por rol en transiciones de estado.
     */
    public function test_11_restricciones_de_permisos_por_rol_en_transiciones(): void
    {
        $cita1 = $this->crearCita(estado: 'confirmada', trabajadorId: $this->trabajador1->id);
        $cita2 = $this->crearCita(estado: 'confirmada', trabajadorId: $this->trabajador2->id);

        // 1. Recepcionista no puede modificar citas mediante el endpoint de administración
        $this->actingAs($this->recepcionistaUser)->patchJson("/admin/citas/{$cita1->id}/estado", [
            'estado' => 'en_proceso',
        ])->assertStatus(403);

        // 2. Especialista 1 puede actualizar su propia cita asignada en su módulo
        $resEspec1 = $this->actingAs($this->trabajadorUser1)->patchJson("/trabajador/citas/{$cita1->id}/estado", [
            'estado' => 'en_atencion',
        ]);
        $resEspec1->assertStatus(200);
        $this->assertEquals('en_atencion', $cita1->fresh()->estado);

        // 3. Especialista 1 NO puede modificar la cita asignada al Especialista 2
        $this->actingAs($this->trabajadorUser1)->patchJson("/trabajador/citas/{$cita2->id}/estado", [
            'estado' => 'en_atencion',
        ])->assertStatus(403);

        // 4. Especialista no puede acceder a las rutas de recepción ni de administración
        $this->actingAs($this->trabajadorUser1)->patchJson("/recepcion/citas/{$cita1->id}/estado", [
            'estado' => 'completada',
        ])->assertStatus(403);

        // 5. Cliente no puede acceder a los endpoints de recepción, administración ni trabajador
        $this->actingAs($this->clienteUser)->patchJson("/recepcion/citas/{$cita1->id}/estado", [
            'estado' => 'cancelada',
        ])->assertStatus(403);

        // 6. Invitado no autenticado es redirigido a login
        $this->patchJson("/recepcion/citas/{$cita1->id}/estado", [
            'estado' => 'cancelada',
        ])->assertStatus(401);
    }
}
