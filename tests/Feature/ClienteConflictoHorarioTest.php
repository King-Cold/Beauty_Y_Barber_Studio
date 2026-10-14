<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\HorarioSucursal;
use App\Models\Service;
use App\Models\Trabajador;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClienteConflictoHorarioTest extends TestCase
{
    use RefreshDatabase;

    protected User $cliente;
    protected User $recepcionista;
    protected User $trabajadorUser1;
    protected User $trabajadorUser2;
    protected Trabajador $trabajador1;
    protected Trabajador $trabajador2;
    protected Service $corte30m;
    protected Service $tratamiento60m;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\TestUsersSeeder::class);

        // Cliente principal para pruebas de conflicto
        $this->cliente = User::where('role_id', 4)->first();

        // Recepcionista
        $this->recepcionista = User::where('role_id', 2)->first();

        // Trabajador 1 (Alejandro)
        $this->trabajadorUser1 = User::where('role_id', 3)->first();
        $this->trabajador1 = Trabajador::create([
            'user_id' => $this->trabajadorUser1->id,
            'direccion' => 'Estudio BBS Centro 1',
            'experiencia' => 6,
            'activo' => true,
            'horario' => [
                'lunes' => ['entrada' => '09:00', 'salida' => '19:00'],
                'martes' => ['entrada' => '09:00', 'salida' => '19:00'],
                'miercoles' => ['entrada' => '09:00', 'salida' => '19:00'],
            ],
        ]);

        // Trabajador 2 (Carlos)
        $this->trabajadorUser2 = User::create([
            'name' => 'Carlos',
            'apellidos' => 'Barbero Dos',
            'email' => 'carlos2.trabajador@bbs.com',
            'telefono' => '5550000010',
            'password' => bcrypt('Password123!'),
            'role_id' => 3,
        ]);
        $this->trabajador2 = Trabajador::create([
            'user_id' => $this->trabajadorUser2->id,
            'direccion' => 'Estudio BBS Centro 2',
            'experiencia' => 4,
            'activo' => true,
            'horario' => [
                'lunes' => ['entrada' => '09:00', 'salida' => '19:00'],
                'martes' => ['entrada' => '09:00', 'salida' => '19:00'],
            ],
        ]);

        // Servicios
        $this->corte30m = Service::create([
            'name' => 'Corte Clásico Barbershop',
            'description' => 'Servicio de 30 minutos',
            'price' => 200.00,
            'duration_minutes' => 30,
            'category' => 'barberia',
            'is_active' => true,
        ]);

        $this->tratamiento60m = Service::create([
            'name' => 'Tinte y Peinado Profesional',
            'description' => 'Servicio de 60 minutos',
            'price' => 500.00,
            'duration_minutes' => 60,
            'category' => 'estetica',
            'is_active' => true,
        ]);

        // Ambos trabajadores pueden realizar los servicios
        $this->trabajador1->servicios()->attach([$this->corte30m->id, $this->tratamiento60m->id]);
        $this->trabajador2->servicios()->attach([$this->corte30m->id, $this->tratamiento60m->id]);

        // Sucursal abierta
        HorarioSucursal::ensureTableExists();
        HorarioSucursal::updateOrCreate(
            ['dia' => 'lunes'],
            ['abierto' => true, 'apertura' => '08:00', 'cierre' => '20:00']
        );
        HorarioSucursal::updateOrCreate(
            ['dia' => 'martes'],
            ['abierto' => true, 'apertura' => '08:00', 'cierre' => '20:00']
        );
    }

    protected function getNextDateForDay(int $dayOfWeek): string
    {
        $date = now()->addDays(1);
        while ($date->dayOfWeek !== $dayOfWeek) {
            $date->addDay();
        }
        return $date->format('Y-m-d');
    }

    /**
     * 1. Un mismo cliente no puede registrar citas simultáneas en el mismo horario,
     *    incluso si corresponden a trabajadores diferentes.
     */
    public function test_cliente_no_puede_tener_dos_citas_en_el_mismo_horario_con_distintos_trabajadores(): void
    {
        $fecha = $this->getNextDateForDay(1); // Lunes
        $hora = '10:00';

        // Cita 1 con Trabajador 1 (10:00 - 10:30)
        $res1 = $this->actingAs($this->cliente)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->corte30m->id,
            'worker_id' => $this->trabajador1->id,
            'date' => $fecha,
            'time' => $hora,
        ]);
        $res1->assertStatus(200);
        $res1->assertJson(['success' => true]);

        // Intento de Cita 2 en el mismo horario pero con Trabajador 2
        $res2 = $this->actingAs($this->cliente)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->corte30m->id,
            'worker_id' => $this->trabajador2->id,
            'date' => $fecha,
            'time' => $hora,
        ]);

        $res2->assertStatus(422);
        $res2->assertJson([
            'success' => false,
            'valido' => false,
            'motivo_tipo' => 'conflicto_cliente',
        ]);
        $this->assertStringContainsString('Ya cuentas con una cita reservada para ese horario', $res2->json('mensaje'));

        // En la base de datos debe haber únicamente 1 cita para este cliente
        $this->assertEquals(1, Cita::where('user_id', $this->cliente->id)->count());
    }

    /**
     * 2. Detección de traslape parcial: la segunda cita inicia durante una cita previa del cliente.
     */
    public function test_traslape_parcial_donde_segunda_cita_inicia_durante_primera_cita_es_rechazada(): void
    {
        $fecha = $this->getNextDateForDay(1); // Lunes

        // Cita 1: Servicio de 60 minutos con Trabajador 1 (11:00 a 12:00)
        $res1 = $this->actingAs($this->cliente)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->tratamiento60m->id,
            'worker_id' => $this->trabajador1->id,
            'date' => $fecha,
            'time' => '11:00',
        ]);
        $res1->assertStatus(200);

        // Intento de Cita 2: 30 minutos a las 11:30 con Trabajador 2 (11:30 a 12:00) -> Se traslapa
        $res2 = $this->actingAs($this->cliente)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->corte30m->id,
            'worker_id' => $this->trabajador2->id,
            'date' => $fecha,
            'time' => '11:30',
        ]);

        $res2->assertStatus(422);
        $res2->assertJson([
            'success' => false,
            'motivo_tipo' => 'conflicto_cliente',
        ]);
        $this->assertStringContainsString('Ya cuentas con una cita reservada', $res2->json('mensaje'));

        $this->assertEquals(1, Cita::where('user_id', $this->cliente->id)->count());
    }

    /**
     * 3. Detección de traslape parcial: la segunda cita inicia antes y termina durante una cita previa del cliente.
     */
    public function test_traslape_parcial_donde_segunda_cita_inicia_antes_y_termina_durante_cita_previa_es_rechazada(): void
    {
        $fecha = $this->getNextDateForDay(1); // Lunes

        // Cita 1: 30 minutos a las 12:00 con Trabajador 1 (12:00 a 12:30)
        Cita::create([
            'user_id' => $this->cliente->id,
            'trabajador_id' => $this->trabajador1->id,
            'service_id' => $this->corte30m->id,
            'fecha' => $fecha,
            'hora_inicio' => '12:00',
            'hora_fin' => '12:30',
            'duracion_minutos' => 30,
            'precio' => 200.00,
            'estado' => 'confirmada',
        ]);

        // Intento de Cita 2: Servicio de 60 minutos a las 11:30 con Trabajador 2 (11:30 a 12:30) -> Se traslapa
        $res = $this->actingAs($this->cliente)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->tratamiento60m->id,
            'worker_id' => $this->trabajador2->id,
            'date' => $fecha,
            'time' => '11:30',
        ]);

        $res->assertStatus(422);
        $res->assertJson([
            'success' => false,
            'motivo_tipo' => 'conflicto_cliente',
        ]);

        $this->assertEquals(1, Cita::where('user_id', $this->cliente->id)->count());
    }

    /**
     * 4. Citas consecutivas y adyacentes del mismo cliente son permitidas sin conflicto:
     *    El cliente puede tener una cita de 10:00 a 10:30 y otra de 10:30 a 11:00 con otro especialista.
     */
    public function test_citas_consecutivas_adyacentes_del_mismo_cliente_son_permitidas(): void
    {
        $fecha = $this->getNextDateForDay(1); // Lunes

        // Cita 1: 10:00 a 10:30 con Trabajador 1
        $res1 = $this->actingAs($this->cliente)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->corte30m->id,
            'worker_id' => $this->trabajador1->id,
            'date' => $fecha,
            'time' => '10:00',
        ]);
        $res1->assertStatus(200);

        // Cita 2: 10:30 a 11:00 con Trabajador 2 (Justo cuando termina la anterior)
        $res2 = $this->actingAs($this->cliente)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->corte30m->id,
            'worker_id' => $this->trabajador2->id,
            'date' => $fecha,
            'time' => '10:30',
        ]);
        $res2->assertStatus(200);
        $res2->assertJson(['success' => true]);

        // Ambas citas coexisten válidamente
        $this->assertEquals(2, Cita::where('user_id', $this->cliente->id)->count());
    }

    /**
     * 5. Citas en diferentes fechas u horarios distantes son permitidas.
     */
    public function test_citas_en_distintas_fechas_o_horarios_sin_traslape_son_permitidas(): void
    {
        $fechaLunes = $this->getNextDateForDay(1);
        $fechaMartes = $this->getNextDateForDay(2);

        // Cita lunes 10:00 con Trabajador 1
        $res1 = $this->actingAs($this->cliente)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->corte30m->id,
            'worker_id' => $this->trabajador1->id,
            'date' => $fechaLunes,
            'time' => '10:00',
        ]);
        $res1->assertStatus(200);

        // Cita lunes 16:00 con Trabajador 2
        $res2 = $this->actingAs($this->cliente)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->corte30m->id,
            'worker_id' => $this->trabajador2->id,
            'date' => $fechaLunes,
            'time' => '16:00',
        ]);
        $res2->assertStatus(200);

        // Cita martes 10:00 con Trabajador 1
        $res3 = $this->actingAs($this->cliente)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->corte30m->id,
            'worker_id' => $this->trabajador1->id,
            'date' => $fechaMartes,
            'time' => '10:00',
        ]);
        $res3->assertStatus(200);

        $this->assertEquals(3, Cita::where('user_id', $this->cliente->id)->count());
    }

    /**
     * 6. Citas canceladas no generan conflicto de horario para el cliente:
     *    Si el cliente tenía una cita previa pero fue cancelada, puede reservar una nueva en ese mismo intervalo.
     */
    public function test_cita_cancelada_no_genera_conflicto_de_horario_para_el_cliente(): void
    {
        $fecha = $this->getNextDateForDay(1); // Lunes
        $hora = '13:00';

        // Cita previa en estado cancelada
        Cita::create([
            'user_id' => $this->cliente->id,
            'trabajador_id' => $this->trabajador1->id,
            'service_id' => $this->corte30m->id,
            'fecha' => $fecha,
            'hora_inicio' => $hora,
            'hora_fin' => '13:30',
            'duracion_minutos' => 30,
            'precio' => 200.00,
            'estado' => 'cancelada',
        ]);

        // El cliente reserva a las 13:00 con Trabajador 2
        $res = $this->actingAs($this->cliente)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->corte30m->id,
            'worker_id' => $this->trabajador2->id,
            'date' => $fecha,
            'time' => $hora,
        ]);

        $res->assertStatus(200);
        $res->assertJson(['success' => true]);

        // Existen 2 registros (1 cancelada + 1 confirmada)
        $this->assertEquals(2, Cita::where('user_id', $this->cliente->id)->count());
        $this->assertEquals(1, Cita::where('user_id', $this->cliente->id)->where('estado', 'confirmada')->count());
    }

    /**
     * 7. Citas en estados activos ('en_atencion' o 'completada') ocupan horario y bloquean nuevas citas.
     */
    public function test_citas_en_estados_activos_bloquean_traslapes_para_el_cliente(): void
    {
        $fecha = $this->getNextDateForDay(1);

        // Cita en atención a las 14:00
        Cita::create([
            'user_id' => $this->cliente->id,
            'trabajador_id' => $this->trabajador1->id,
            'service_id' => $this->corte30m->id,
            'fecha' => $fecha,
            'hora_inicio' => '14:00',
            'hora_fin' => '14:30',
            'duracion_minutos' => 30,
            'precio' => 200.00,
            'estado' => 'en_atencion',
        ]);

        // Intento a las 14:15 con Trabajador 2 -> Bloqueado
        $resEnAtencion = $this->actingAs($this->cliente)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->corte30m->id,
            'worker_id' => $this->trabajador2->id,
            'date' => $fecha,
            'time' => '14:15',
        ]);
        $resEnAtencion->assertStatus(422);
        $resEnAtencion->assertJson(['motivo_tipo' => 'conflicto_cliente']);

        // Cita completada a las 15:00
        Cita::create([
            'user_id' => $this->cliente->id,
            'trabajador_id' => $this->trabajador1->id,
            'service_id' => $this->corte30m->id,
            'fecha' => $fecha,
            'hora_inicio' => '15:00',
            'hora_fin' => '15:30',
            'duracion_minutos' => 30,
            'precio' => 200.00,
            'estado' => 'completada',
        ]);

        // Intento a las 15:00 con Trabajador 2 -> Bloqueado
        $resCompletada = $this->actingAs($this->cliente)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->corte30m->id,
            'worker_id' => $this->trabajador2->id,
            'date' => $fecha,
            'time' => '15:00',
        ]);
        $resCompletada->assertStatus(422);
        $resCompletada->assertJson(['motivo_tipo' => 'conflicto_cliente']);
    }

    /**
     * 8. La recepcionista no puede agendar una cita traslapada en nombre de un cliente que ya tiene cita.
     */
    public function test_recepcionista_no_puede_agendar_cita_traslapada_para_el_mismo_cliente(): void
    {
        $fecha = $this->getNextDateForDay(2); // Martes
        $hora = '11:00';

        // Cliente ya tiene una cita de 11:00 a 11:30 con Trabajador 1
        Cita::create([
            'user_id' => $this->cliente->id,
            'trabajador_id' => $this->trabajador1->id,
            'service_id' => $this->corte30m->id,
            'fecha' => $fecha,
            'hora_inicio' => $hora,
            'hora_fin' => '11:30',
            'duracion_minutos' => 30,
            'precio' => 200.00,
            'estado' => 'confirmada',
        ]);

        // Recepcionista intenta agendar a las 11:00 con Trabajador 2 para este mismo cliente
        $res = $this->actingAs($this->recepcionista)->postJson(route('recepcion.agendamiento.confirmar'), [
            'client_id' => $this->cliente->id,
            'service_id' => $this->corte30m->id,
            'worker_id' => $this->trabajador2->id,
            'date' => $fecha,
            'time' => $hora,
        ]);

        $res->assertStatus(422);
        $res->assertJson([
            'success' => false,
            'valido' => false,
            'motivo_tipo' => 'conflicto_cliente',
        ]);
        $this->assertStringContainsString('ya cuenta con una cita en ese horario', $res->json('mensaje'));

        // Solo 1 cita guardada
        $this->assertEquals(1, Cita::where('user_id', $this->cliente->id)->count());
    }

    /**
     * 9. El endpoint de validación previa (validar-horario) detecta conflicto de horario del cliente.
     */
    public function test_validar_horario_detecta_conflicto_previo_del_cliente(): void
    {
        $fecha = $this->getNextDateForDay(1); // Lunes

        // Cita existente a las 17:00
        Cita::create([
            'user_id' => $this->cliente->id,
            'trabajador_id' => $this->trabajador1->id,
            'service_id' => $this->corte30m->id,
            'fecha' => $fecha,
            'hora_inicio' => '17:00',
            'hora_fin' => '17:30',
            'duracion_minutos' => 30,
            'precio' => 200.00,
            'estado' => 'confirmada',
        ]);

        // Cliente consulta validación para las 17:00 con Trabajador 2
        $resCliente = $this->actingAs($this->cliente)->postJson(route('cliente.agendamiento.validar-horario'), [
            'service_id' => $this->corte30m->id,
            'worker_id' => $this->trabajador2->id,
            'date' => $fecha,
            'time' => '17:00',
        ]);

        $resCliente->assertStatus(422);
        $resCliente->assertJson([
            'success' => false,
            'valido' => false,
            'motivo_tipo' => 'conflicto_cliente',
        ]);

        // Recepcionista consulta validación enviando client_id
        $resRecepcion = $this->actingAs($this->recepcionista)->postJson(route('recepcion.agendamiento.validar-horario'), [
            'client_id' => $this->cliente->id,
            'service_id' => $this->corte30m->id,
            'worker_id' => $this->trabajador2->id,
            'date' => $fecha,
            'time' => '17:00',
        ]);

        $resRecepcion->assertStatus(422);
        $resRecepcion->assertJson([
            'success' => false,
            'valido' => false,
            'motivo_tipo' => 'conflicto_cliente',
        ]);
    }
}
