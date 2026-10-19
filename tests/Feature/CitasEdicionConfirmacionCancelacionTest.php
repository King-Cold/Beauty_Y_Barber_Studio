<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\FechaEspecial;
use App\Models\HorarioSucursal;
use App\Models\Service;
use App\Models\Trabajador;
use App\Models\User;
use App\Services\CitaCalendarioService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CitasEdicionConfirmacionCancelacionTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $recepcionistaUser;
    protected User $trabajadorUser1;
    protected User $trabajadorUser2;
    protected User $clienteUser1;
    protected User $clienteUser2;
    protected Trabajador $trabajador1;
    protected Trabajador $trabajador2;
    protected Service $servicio30;
    protected Service $servicio60;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\TestUsersSeeder::class);

        $this->adminUser = User::where('role_id', 1)->first();
        $this->recepcionistaUser = User::where('role_id', 2)->first();
        $this->trabajadorUser1 = User::where('role_id', 3)->first();
        $this->clienteUser1 = User::where('role_id', 4)->first();

        // Cliente 2
        $this->clienteUser2 = User::create([
            'name' => 'Roberto',
            'apellidos' => 'Gómez',
            'email' => 'roberto.gomez@test.com',
            'password' => bcrypt('password123'),
            'role_id' => 4,
            'telefono' => '5551234567',
        ]);

        // Trabajador 2 User
        $this->trabajadorUser2 = User::create([
            'name' => 'Alejandro',
            'apellidos' => 'Vargas',
            'email' => 'alejandro.vargas@barberstudio.com',
            'password' => bcrypt('password123'),
            'role_id' => 3,
        ]);

        // Horario estándar: lunes a viernes 09:00 a 19:00 con descanso de 13:00 a 14:00
        $horarioEstandar = [
            'lunes' => ['entrada' => '09:00', 'salida' => '19:00', 'descanso_inicio' => '13:00', 'descanso_fin' => '14:00'],
            'martes' => ['entrada' => '09:00', 'salida' => '19:00', 'descanso_inicio' => '13:00', 'descanso_fin' => '14:00'],
            'miercoles' => ['entrada' => '09:00', 'salida' => '19:00', 'descanso_inicio' => '13:00', 'descanso_fin' => '14:00'],
            'jueves' => ['entrada' => '09:00', 'salida' => '19:00', 'descanso_inicio' => '13:00', 'descanso_fin' => '14:00'],
            'viernes' => ['entrada' => '09:00', 'salida' => '19:00', 'descanso_inicio' => '13:00', 'descanso_fin' => '14:00'],
            'sabado' => ['entrada' => '09:00', 'salida' => '15:00'],
        ];

        // Especialista 1
        $this->trabajador1 = Trabajador::create([
            'user_id' => $this->trabajadorUser1->id,
            'direccion' => 'Estación 1 - Beauty Studio',
            'experiencia' => 5,
            'activo' => true,
            'horario' => $horarioEstandar,
        ]);

        // Especialista 2
        $this->trabajador2 = Trabajador::create([
            'user_id' => $this->trabajadorUser2->id,
            'direccion' => 'Estación 2 - Barber Studio',
            'experiencia' => 4,
            'activo' => true,
            'horario' => $horarioEstandar,
        ]);

        // Servicios de 30 y 60 min
        $this->servicio30 = Service::create([
            'name' => 'Corte Express',
            'category' => 'barberia',
            'duration_minutes' => 30,
            'price' => 150.00,
            'is_active' => true,
        ]);

        $this->servicio60 = Service::create([
            'name' => 'Tratamiento Completo Deluxe',
            'category' => 'estudio',
            'duration_minutes' => 60,
            'price' => 350.00,
            'is_active' => true,
        ]);

        // Asociar servicios a trabajadores
        $this->trabajador1->servicios()->attach([$this->servicio30->id, $this->servicio60->id]);
        $this->trabajador2->servicios()->attach([$this->servicio30->id, $this->servicio60->id]);

        // Sucursal abierta lunes a sábado de 08:00 a 20:00
        HorarioSucursal::ensureTableExists();
        $dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
        foreach ($dias as $dia) {
            HorarioSucursal::updateOrCreate(
                ['dia' => $dia],
                ['abierto' => true, 'apertura' => '08:00', 'cierre' => '20:00']
            );
        }
        HorarioSucursal::updateOrCreate(
            ['dia' => 'domingo'],
            ['abierto' => false, 'apertura' => '09:00', 'cierre' => '14:00']
        );

        Cita::ensureTableExists();
    }

    /**
     * Retorna la próxima fecha que coincida con el día de semana deseado (ej. lunes = 1).
     */
    protected function obtenerFechaDia(int $diaSemana = 1): string
    {
        $d = Carbon::today()->addDays(1);
        while ($d->dayOfWeek !== $diaSemana) {
            $d->addDay();
        }
        return $d->format('Y-m-d');
    }

    /**
     * Helper para crear cita inicial de prueba.
     */
    protected function crearCita(
        ?string $fecha = null,
        string $horaInicio = '10:00',
        string $horaFin = '10:30',
        ?Trabajador $trabajador = null,
        ?Service $servicio = null,
        string $estado = 'confirmada',
        ?User $cliente = null
    ): Cita {
        $fecha = $fecha ?: $this->obtenerFechaDia(1); // Próximo lunes
        $trabajador = $trabajador ?: $this->trabajador1;
        $servicio = $servicio ?: $this->servicio30;
        $cliente = $cliente ?: $this->clienteUser1;

        return Cita::create([
            'user_id' => $cliente->id,
            'trabajador_id' => $trabajador->id,
            'service_id' => $servicio->id,
            'fecha' => $fecha,
            'hora_inicio' => $horaInicio,
            'hora_fin' => $horaFin,
            'duracion_minutos' => $servicio->duration_minutes ?: 30,
            'precio' => $servicio->price ?: 150.00,
            'estado' => $estado,
            'notas' => 'Nota original del cliente',
            'observaciones' => 'Observación original',
        ]);
    }

    /**
     * 1. Edición exitosa de una cita cambiando de horario y/o fecha dentro de la disponibilidad del especialista.
     */
    public function test_01_edicion_exitosa_cambiando_horario_y_fecha(): void
    {
        $fecha1 = $this->obtenerFechaDia(1); // Lunes
        $cita = $this->crearCita(fecha: $fecha1, horaInicio: '10:00', horaFin: '10:30');

        $nuevaHora = '11:00';

        // Recepcionista edita el horario a las 11:00
        $response = $this->actingAs($this->recepcionistaUser)
            ->putJson(route('recepcion.citas.update', $cita->id), [
                'fecha' => $fecha1,
                'hora_inicio' => $nuevaHora,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $cita->refresh();
        $this->assertEquals('11:00', substr($cita->hora_inicio, 0, 5));
        $this->assertEquals('11:30', substr($cita->hora_fin, 0, 5));
        $this->assertEquals($fecha1, $cita->fecha);
    }

    /**
     * 2. Edición exitosa de una cita cambiando de especialista disponible.
     */
    public function test_02_edicion_exitosa_cambiando_especialista(): void
    {
        $fecha = $this->obtenerFechaDia(1);
        $cita = $this->crearCita(fecha: $fecha, horaInicio: '10:00', horaFin: '10:30', trabajador: $this->trabajador1);

        // Administrador transfiere la cita al especialista 2
        $response = $this->actingAs($this->adminUser)
            ->putJson(route('admin.citas.update', $cita->id), [
                'trabajador_id' => $this->trabajador2->id,
                'fecha' => $fecha,
                'hora_inicio' => '10:00',
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $cita->refresh();
        $this->assertEquals($this->trabajador2->id, $cita->trabajador_id);
    }

    /**
     * 3. Edición exitosa cambiando el servicio (recalculando duración y hora de finalización).
     */
    public function test_03_edicion_exitosa_cambiando_servicio_recalcula_duracion_y_fin(): void
    {
        $fecha = $this->obtenerFechaDia(1);
        // Cita con servicio de 30 min (10:00 a 10:30)
        $cita = $this->crearCita(fecha: $fecha, horaInicio: '10:00', horaFin: '10:30', servicio: $this->servicio30);

        // Cambiar al servicio de 60 minutos
        $response = $this->actingAs($this->recepcionistaUser)
            ->putJson(route('recepcion.citas.update', $cita->id), [
                'service_id' => $this->servicio60->id,
                'hora_inicio' => '10:00',
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $cita->refresh();
        $this->assertEquals($this->servicio60->id, $cita->service_id);
        $this->assertEquals(60, $cita->duracion_minutos);
        $this->assertEquals('10:00', substr($cita->hora_inicio, 0, 5));
        $this->assertEquals('11:00', substr($cita->hora_fin, 0, 5));
        $this->assertEquals(350.00, (float)$cita->precio);
    }

    /**
     * 4. Edición de notas u observaciones persistiendo correctamente.
     */
    public function test_04_edicion_notas_u_observaciones_persisten_correctamente(): void
    {
        $cita = $this->crearCita();

        $response = $this->actingAs($this->recepcionistaUser)
            ->putJson(route('recepcion.citas.update', $cita->id), [
                'notas' => 'El cliente solicita atención rápida.',
                'observaciones' => 'Se reprogramó a petición del cliente por llamada telefónica.',
            ]);

        $response->assertStatus(200);

        $cita->refresh();
        $this->assertEquals('El cliente solicita atención rápida.', $cita->notas);
        $this->assertEquals('Se reprogramó a petición del cliente por llamada telefónica.', $cita->observaciones);
    }

    /**
     * 5. Intento de reprogramación a un horario en conflicto con otra cita del especialista (rechazado con 422).
     */
    public function test_05_reprogramacion_rechazada_por_conflicto_con_otra_cita_del_especialista(): void
    {
        $fecha = $this->obtenerFechaDia(1);

        // Cita A del trabajador 1: 10:00 - 10:30
        $citaA = $this->crearCita(fecha: $fecha, horaInicio: '10:00', horaFin: '10:30', trabajador: $this->trabajador1);

        // Cita B del trabajador 1: 11:00 - 11:30
        $citaB = $this->crearCita(fecha: $fecha, horaInicio: '11:00', horaFin: '11:30', trabajador: $this->trabajador1, cliente: $this->clienteUser2);

        // Intentar reprogramar Cita B a las 10:00 (ocupado por Cita A)
        $response = $this->actingAs($this->recepcionistaUser)
            ->putJson(route('recepcion.citas.update', $citaB->id), [
                'fecha' => $fecha,
                'hora_inicio' => '10:00',
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);

        // Cita B permanece en su horario original
        $citaB->refresh();
        $this->assertEquals('11:00', substr($citaB->hora_inicio, 0, 5));
    }

    /**
     * 6. Intento de reprogramación fuera del horario laboral, en descanso o bloqueo administrativo (rechazado con 422).
     */
    public function test_06_reprogramacion_rechazada_fuera_de_horario_descanso_o_bloqueo(): void
    {
        $fecha = $this->obtenerFechaDia(1); // Lunes (descanso de 13:00 a 14:00, salida a las 19:00)
        $cita = $this->crearCita(fecha: $fecha, horaInicio: '10:00', horaFin: '10:30');

        // Intento 1: Durante el descanso del especialista (13:00)
        $respDescanso = $this->actingAs($this->adminUser)
            ->putJson(route('admin.citas.update', $cita->id), [
                'hora_inicio' => '13:00',
            ]);
        $respDescanso->assertStatus(422);

        // Intento 2: Fuera de horario laboral (21:00)
        $respFueraHorario = $this->actingAs($this->adminUser)
            ->putJson(route('admin.citas.update', $cita->id), [
                'hora_inicio' => '21:00',
            ]);
        $respFueraHorario->assertStatus(422);
    }

    /**
     * 7. Reprogramación conservando el mismo horario/especialista (la propia cita no debe bloquearse a sí misma, exclusión de $cita->id exitosa).
     */
    public function test_07_reprogramacion_conservando_mismo_horario_no_se_bloquea_a_si_misma(): void
    {
        $fecha = $this->obtenerFechaDia(1);
        $cita = $this->crearCita(fecha: $fecha, horaInicio: '10:00', horaFin: '10:30', trabajador: $this->trabajador1);

        // Modificar únicamente las observaciones manteniendo fecha, hora y especialista
        $response = $this->actingAs($this->recepcionistaUser)
            ->putJson(route('recepcion.citas.update', $cita->id), [
                'fecha' => $fecha,
                'hora_inicio' => '10:00',
                'trabajador_id' => $this->trabajador1->id,
                'observaciones' => 'Confirmación telefónica de asistencia puntual.',
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $cita->refresh();
        $this->assertEquals('Confirmación telefónica de asistencia puntual.', $cita->observaciones);
        $this->assertEquals('10:00', substr($cita->hora_inicio, 0, 5));
    }

    /**
     * 8. Confirmación válida de una cita agendada reflejando estado confirmada.
     */
    public function test_08_confirmacion_valida_de_cita_refleja_estado_confirmada(): void
    {
        // Se crea cita en estado 'confirmada' o se prueba el endpoint de confirmación
        $cita = $this->crearCita(estado: 'confirmada');

        $response = $this->actingAs($this->recepcionistaUser)
            ->postJson(route('recepcion.citas.confirmar', $cita->id));

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $cita->refresh();
        $this->assertEquals('confirmada', $cita->estado);
    }

    /**
     * 9. Cancelación de cita por recepcionista o administrador con registro de motivo/observaciones y liberación del horario en el calendario.
     */
    public function test_09_cancelacion_por_recepcionista_o_admin_registra_motivo_y_libera_horario(): void
    {
        $fecha = $this->obtenerFechaDia(1);
        $cita = $this->crearCita(fecha: $fecha, horaInicio: '10:00', horaFin: '10:30', trabajador: $this->trabajador1);

        // Recepcionista cancela la cita con motivo
        $response = $this->actingAs($this->recepcionistaUser)
            ->postJson(route('recepcion.citas.cancelar', $cita->id), [
                'motivo' => 'Cliente canceló por viaje de trabajo imprevisto.',
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $cita->refresh();
        $this->assertEquals('cancelada', $cita->estado);
        $this->assertStringContainsString('Cliente canceló por viaje', (string)$cita->observaciones);

        // Verificar que el horario de las 10:00 queda liberado para una nueva cita
        $disponibilidad = app(\App\Services\DisponibilidadService::class)
            ->validarHorarioSeleccionado($this->trabajador1, $fecha, '10:00', 30, $this->servicio30);

        $this->assertTrue($disponibilidad['disponible'], 'El horario de la cita cancelada debe quedar libre para nuevos agendamientos.');
    }

    /**
     * 10. La cita cancelada se conserva en base de datos con estado 'cancelada' (no se elimina físicamente).
     */
    public function test_10_cita_cancelada_se_conserva_en_base_de_datos_sin_eliminacion_fisica(): void
    {
        $cita = $this->crearCita();
        $citaId = $cita->id;

        $this->actingAs($this->adminUser)
            ->postJson(route('admin.citas.cancelar', $citaId), [
                'motivo' => 'Cancelación administrativa de prueba.',
            ])
            ->assertStatus(200);

        // Verificar existencia en la base de datos
        $this->assertDatabaseHas('citas', [
            'id' => $citaId,
            'estado' => 'cancelada',
        ]);
    }

    /**
     * 11. Restricción de permisos: Rol 1 (Admin) y Rol 2 (Recepcionista) autorizados; Rol 3 (Trabajador) y Rol 4 (Cliente) denegados (403) en endpoints de gestión/edición.
     */
    public function test_11_restriccion_permisos_admin_y_recepcion_permitidos_trabajador_y_cliente_denegados_403(): void
    {
        $cita = $this->crearCita();

        // 1. Trabajador (Rol 3) intenta acceder a endpoint de recepción -> 403
        $respTrabajador = $this->actingAs($this->trabajadorUser1)
            ->putJson(route('recepcion.citas.update', $cita->id), [
                'notas' => 'Intento trabajador',
            ]);
        $respTrabajador->assertStatus(403);

        // 2. Cliente (Rol 4) intenta acceder a endpoint de recepción -> 403
        $respCliente = $this->actingAs($this->clienteUser1)
            ->putJson(route('recepcion.citas.update', $cita->id), [
                'notas' => 'Intento cliente',
            ]);
        $respCliente->assertStatus(403);

        // 3. Cliente (Rol 4) intenta acceder a endpoint de administración -> 403
        $respClienteAdmin = $this->actingAs($this->clienteUser1)
            ->putJson(route('admin.citas.update', $cita->id), [
                'notas' => 'Intento cliente admin',
            ]);
        $respClienteAdmin->assertStatus(403);

        // 4. Usuario no autenticado (Invitado) es redirigido o no autorizado
        auth()->logout();
        $respGuest = $this->putJson(route('recepcion.citas.update', $cita->id), [
            'notas' => 'Intento invitado',
        ]);
        $this->assertTrue(in_array($respGuest->status(), [401, 302]));
    }

    /**
     * 12. Reglas de estados: Cita completada es inmutable (422), mientras que cita cancelada puede reagendarse reactivándose a confirmada.
     */
    public function test_12_intento_de_editar_cita_cancelada_o_completada_es_rechazado_inmutabilidad(): void
    {
        $fecha = $this->obtenerFechaDia(1);

        // 1. Cita completada rechaza cambios de horario (servicio ya prestado)
        $citaCompletada = $this->crearCita(fecha: $fecha, horaInicio: '15:00', horaFin: '15:30', estado: 'completada');

        $respCompletada = $this->actingAs($this->adminUser)
            ->putJson(route('admin.citas.update', $citaCompletada->id), [
                'hora_inicio' => '16:00',
            ]);

        $respCompletada->assertStatus(422)
            ->assertJson(['success' => false]);

        // 2. Cita cancelada puede ser reagendada a un nuevo horario válido y se reactiva a confirmada
        $citaCancelada = $this->crearCita(fecha: $fecha, horaInicio: '10:00', horaFin: '10:30', estado: 'cancelada');

        $respCancelada = $this->actingAs($this->recepcionistaUser)
            ->putJson(route('recepcion.citas.update', $citaCancelada->id), [
                'hora_inicio' => '12:00',
            ]);

        $respCancelada->assertStatus(200)
            ->assertJson(['success' => true]);

        $citaCancelada->refresh();
        $this->assertEquals('confirmada', $citaCancelada->estado);
        $this->assertEquals('12:00', substr($citaCancelada->hora_inicio, 0, 5));
    }

    /**
     * 13. Validación de fecha: No permite reprogramar ni reagendar a fechas anteriores al día actual.
     */
    public function test_13_no_permite_reprogramar_a_fechas_anteriores(): void
    {
        $fechaFutura = $this->obtenerFechaDia(1);
        $fechaPasada = Carbon::yesterday()->format('Y-m-d');

        $cita = $this->crearCita(fecha: $fechaFutura, horaInicio: '10:00', horaFin: '10:30', estado: 'confirmada');

        $resp = $this->actingAs($this->adminUser)
            ->putJson(route('admin.citas.update', $cita->id), [
                'fecha' => $fechaPasada,
                'hora_inicio' => '10:00',
            ]);

        $resp->assertStatus(422);
    }

    /**
     * 14. Especialista asignado: Valida que el especialista tenga asignado el servicio seleccionado.
     */
    public function test_14_valida_compatibilidad_de_servicio_con_especialista(): void
    {
        $fecha = $this->obtenerFechaDia(1);

        // Asociamos únicamente el servicio30 al trabajador1
        $this->trabajador1->servicios()->sync([$this->servicio30->id]);

        $cita = $this->crearCita(
            fecha: $fecha,
            horaInicio: '11:00',
            horaFin: '11:30',
            estado: 'confirmada',
            trabajador: $this->trabajador1,
            servicio: $this->servicio30
        );

        // Intento de asignar servicio60 (que no ofrece el trabajador1)
        $resp = $this->actingAs($this->recepcionistaUser)
            ->putJson(route('recepcion.citas.update', $cita->id), [
                'service_id' => $this->servicio60->id,
                'trabajador_id' => $this->trabajador1->id,
            ]);

        $resp->assertStatus(422)
            ->assertJson(['success' => false]);
    }
}


