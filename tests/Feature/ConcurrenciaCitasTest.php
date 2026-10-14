<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\HorarioSucursal;
use App\Models\Service;
use App\Models\Trabajador;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConcurrenciaCitasTest extends TestCase
{
    use RefreshDatabase;

    protected User $cliente1;
    protected User $cliente2;
    protected User $recepcionista;
    protected User $trabajadorUser1;
    protected User $trabajadorUser2;
    protected Trabajador $trabajador1;
    protected Trabajador $trabajador2;
    protected Service $servicio30m;
    protected Service $servicio60m;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\TestUsersSeeder::class);

        // Clientes
        $this->cliente1 = User::where('role_id', 4)->first();
        $this->cliente2 = User::create([
            'name' => 'Cliente',
            'apellidos' => 'Concurrente Dos',
            'email' => 'cliente2.concurrencia@test.com',
            'telefono' => '5550000001',
            'password' => bcrypt('Password123!'),
            'role_id' => 4,
        ]);

        // Recepcionista
        $this->recepcionista = User::where('role_id', 2)->first();

        // Trabajador 1 (Alejandro - role_id 3)
        $this->trabajadorUser1 = User::where('role_id', 3)->first();
        $this->trabajador1 = Trabajador::create([
            'user_id' => $this->trabajadorUser1->id,
            'direccion' => 'Estudio BBS Centro 1',
            'experiencia' => 5,
            'activo' => true,
            'horario' => [
                'lunes' => ['entrada' => '09:00', 'salida' => '19:00'],
                'martes' => ['entrada' => '09:00', 'salida' => '19:00'],
                'miercoles' => ['entrada' => '09:00', 'salida' => '19:00'],
                'jueves' => ['entrada' => '09:00', 'salida' => '19:00'],
                'viernes' => ['entrada' => '09:00', 'salida' => '19:00'],
            ],
        ]);

        // Trabajador 2 (Segundo especialista)
        $this->trabajadorUser2 = User::create([
            'name' => 'Carlos',
            'apellidos' => 'Barbero',
            'email' => 'carlos.trabajador@bbs.com',
            'telefono' => '5550000002',
            'password' => bcrypt('Password123!'),
            'role_id' => 3,
        ]);
        $this->trabajador2 = Trabajador::create([
            'user_id' => $this->trabajadorUser2->id,
            'direccion' => 'Estudio BBS Centro 2',
            'experiencia' => 3,
            'activo' => true,
            'horario' => [
                'lunes' => ['entrada' => '09:00', 'salida' => '19:00'],
                'martes' => ['entrada' => '09:00', 'salida' => '19:00'],
            ],
        ]);

        // Servicios
        $this->servicio30m = Service::create([
            'name' => 'Corte Rápido Clásico',
            'description' => 'Corte regular de 30 minutos',
            'price' => 200.00,
            'duration_minutes' => 30,
            'category' => 'barberia',
            'is_active' => true,
        ]);

        $this->servicio60m = Service::create([
            'name' => 'Tratamiento Capilar Profundo',
            'description' => 'Servicio completo de 60 minutos',
            'price' => 450.00,
            'duration_minutes' => 60,
            'category' => 'estetica',
            'is_active' => true,
        ]);

        // Asignar servicios a trabajadores
        $this->trabajador1->servicios()->attach([$this->servicio30m->id, $this->servicio60m->id]);
        $this->trabajador2->servicios()->attach([$this->servicio30m->id]);

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
     * 1. Dos clientes intentan reservar al mismo trabajador en el mismo horario:
     *    Únicamente una solicitud tiene éxito y la segunda es rechazada con HTTP 422.
     */
    public function test_dos_clientes_intentan_reservar_mismo_trabajador_mismo_horario_solo_uno_tiene_exito(): void
    {
        $fecha = $this->getNextDateForDay(1); // Lunes
        $hora = '10:00';

        // Cliente 1 confirma primero
        $res1 = $this->actingAs($this->cliente1)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->servicio30m->id,
            'worker_id' => $this->trabajador1->id,
            'date' => $fecha,
            'time' => $hora,
        ]);

        $res1->assertStatus(200);
        $res1->assertJson([
            'success' => true,
            'valido' => true,
        ]);
        $this->assertStringContainsString('Cita confirmada exitosamente', $res1->json('mensaje'));

        // Cliente 2 intenta confirmar el mismo horario
        $res2 = $this->actingAs($this->cliente2)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->servicio30m->id,
            'worker_id' => $this->trabajador1->id,
            'date' => $fecha,
            'time' => $hora,
        ]);

        $res2->assertStatus(422);
        $res2->assertJson([
            'success' => false,
            'valido' => false,
        ]);
        $this->assertStringContainsString('no está disponible', $res2->json('mensaje'));

        // En la base de datos únicamente debe existir UNA sola cita
        $this->assertEquals(1, Cita::where('trabajador_id', $this->trabajador1->id)
            ->whereDate('fecha', $fecha)
            ->where('hora_inicio', $hora)
            ->count());

        $this->assertEquals(1, Cita::count());
    }

    /**
     * 2. Consideración de la duración completa del servicio:
     *    Un servicio de 60 minutos bloquea citas intermedias que se traslapen.
     */
    public function test_servicio_largo_bloquea_citas_en_intervalo_traslapado(): void
    {
        $fecha = $this->getNextDateForDay(1); // Lunes

        // Cliente 1 reserva servicio de 60 minutos de 11:00 a 12:00
        $res1 = $this->actingAs($this->cliente1)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->servicio60m->id,
            'worker_id' => $this->trabajador1->id,
            'date' => $fecha,
            'time' => '11:00',
        ]);
        $res1->assertStatus(200);

        // Cliente 2 intenta reservar a las 11:30 (dentro del intervalo 11:00 - 12:00)
        $res2 = $this->actingAs($this->cliente2)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->servicio30m->id,
            'worker_id' => $this->trabajador1->id,
            'date' => $fecha,
            'time' => '11:30',
        ]);
        $res2->assertStatus(422);
        $this->assertStringContainsString('no está disponible', $res2->json('mensaje'));

        // Cliente 2 intenta reservar a las 10:45 un servicio de 30 minutos (10:45 a 11:15 - se traslapa con 11:00)
        $res3 = $this->actingAs($this->cliente2)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->servicio30m->id,
            'worker_id' => $this->trabajador1->id,
            'date' => $fecha,
            'time' => '10:45',
        ]);
        $res3->assertStatus(422);

        // Cliente 2 reserva a las 12:00 (justo al terminar la cita de 60m): Debe permitirse
        $res4 = $this->actingAs($this->cliente2)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->servicio30m->id,
            'worker_id' => $this->trabajador1->id,
            'date' => $fecha,
            'time' => '12:00',
        ]);
        $res4->assertStatus(200);

        // Debe haber exactamente 2 citas en la base de datos (11:00 a 12:00 y 12:00 a 12:30)
        $this->assertEquals(2, Cita::where('trabajador_id', $this->trabajador1->id)->count());
    }

    /**
     * 3. Concurrencia cruzada entre cliente y recepcionista:
     *    Si la recepcionista y el cliente intentan agendar al mismo trabajador simultáneamente,
     *    la protección mutua garantiza que solo una se confirme.
     */
    public function test_concurrencia_cruzada_entre_cliente_y_recepcion(): void
    {
        $fecha = $this->getNextDateForDay(2); // Martes
        $hora = '14:00';

        // Recepcionista agenda para Cliente 1
        $resRecepcion = $this->actingAs($this->recepcionista)->postJson(route('recepcion.agendamiento.confirmar'), [
            'client_id' => $this->cliente1->id,
            'service_id' => $this->servicio30m->id,
            'worker_id' => $this->trabajador1->id,
            'date' => $fecha,
            'time' => $hora,
        ]);
        $resRecepcion->assertStatus(200);
        $resRecepcion->assertJson(['success' => true]);

        // Cliente 2 intenta agendar exactamente el mismo horario desde su panel
        $resCliente = $this->actingAs($this->cliente2)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->servicio30m->id,
            'worker_id' => $this->trabajador1->id,
            'date' => $fecha,
            'time' => $hora,
        ]);
        $resCliente->assertStatus(422);
        $resCliente->assertJson([
            'success' => false,
            'valido' => false,
        ]);
        $this->assertStringContainsString('no está disponible', $resCliente->json('mensaje'));

        // Solo 1 cita guardada
        $this->assertEquals(1, Cita::where('trabajador_id', $this->trabajador1->id)
            ->whereDate('fecha', $fecha)
            ->where('hora_inicio', $hora)
            ->count());
    }

    /**
     * 4. Estado de las citas:
     *    Una cita en estado 'cancelada' libera el horario y permite una nueva reservación.
     *    Citas en 'confirmada', 'en_atencion' o 'completada' ocupan el horario y lo bloquean.
     */
    public function test_estados_de_citas_liberan_o_bloquean_segun_su_condicion(): void
    {
        $fecha = $this->getNextDateForDay(1); // Lunes

        // Cita cancelada previamente
        $citaCancelada = Cita::create([
            'user_id' => $this->cliente1->id,
            'trabajador_id' => $this->trabajador1->id,
            'service_id' => $this->servicio30m->id,
            'fecha' => $fecha,
            'hora_inicio' => '15:00',
            'hora_fin' => '15:30',
            'duracion_minutos' => 30,
            'precio' => 200.00,
            'estado' => 'cancelada',
        ]);

        // Cliente 2 puede reservar a las 15:00 porque la cita previa está cancelada
        $resLibre = $this->actingAs($this->cliente2)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->servicio30m->id,
            'worker_id' => $this->trabajador1->id,
            'date' => $fecha,
            'time' => '15:00',
        ]);
        $resLibre->assertStatus(200);

        // Ahora creamos una cita 'en_atencion' a las 16:00
        Cita::create([
            'user_id' => $this->cliente1->id,
            'trabajador_id' => $this->trabajador1->id,
            'service_id' => $this->servicio30m->id,
            'fecha' => $fecha,
            'hora_inicio' => '16:00',
            'hora_fin' => '16:30',
            'duracion_minutos' => 30,
            'precio' => 200.00,
            'estado' => 'en_atencion',
        ]);

        // Cliente 2 intenta a las 16:00 -> Bloqueado
        $resBloqueado = $this->actingAs($this->cliente2)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->servicio30m->id,
            'worker_id' => $this->trabajador1->id,
            'date' => $fecha,
            'time' => '16:00',
        ]);
        $resBloqueado->assertStatus(422);

        // Creamos una cita 'completada' a las 17:00
        Cita::create([
            'user_id' => $this->cliente1->id,
            'trabajador_id' => $this->trabajador1->id,
            'service_id' => $this->servicio30m->id,
            'fecha' => $fecha,
            'hora_inicio' => '17:00',
            'hora_fin' => '17:30',
            'duracion_minutos' => 30,
            'precio' => 200.00,
            'estado' => 'completada',
        ]);

        // Cliente 2 intenta a las 17:00 -> Bloqueado
        $resBloqueadoCompletada = $this->actingAs($this->cliente2)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->servicio30m->id,
            'worker_id' => $this->trabajador1->id,
            'date' => $fecha,
            'time' => '17:00',
        ]);
        $resBloqueadoCompletada->assertStatus(422);
    }

    /**
     * 5. Solicitudes simultáneas para trabajadores DIFERENTES en el mismo horario:
     *    Ambas deben confirmarse sin interferencia, probando que el bloqueo es a nivel de trabajador.
     */
    public function test_reservaciones_en_mismo_horario_para_trabajadores_distintos_ambas_tienen_exito(): void
    {
        $fecha = $this->getNextDateForDay(1); // Lunes
        $hora = '18:00';

        // Cliente 1 agenda con Trabajador 1 a las 18:00
        $res1 = $this->actingAs($this->cliente1)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->servicio30m->id,
            'worker_id' => $this->trabajador1->id,
            'date' => $fecha,
            'time' => $hora,
        ]);
        $res1->assertStatus(200);

        // Cliente 2 agenda con Trabajador 2 a las 18:00
        $res2 = $this->actingAs($this->cliente2)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->servicio30m->id,
            'worker_id' => $this->trabajador2->id,
            'date' => $fecha,
            'time' => $hora,
        ]);
        $res2->assertStatus(200);

        // Ambas citas coexisten legítimamente
        $this->assertEquals(2, Cita::whereDate('fecha', $fecha)->where('hora_inicio', $hora)->count());
    }

    /**
     * 6. Reenvíos repetidos por doble clic o reintento rápido del mismo cliente:
     *    No se duplican registros en BD y se retorna la cita ya existente con éxito.
     */
    public function test_reenvios_repetidos_del_mismo_cliente_no_duplican_citas(): void
    {
        $fecha = $this->getNextDateForDay(2); // Martes
        $hora = '10:30';

        // Primer envío
        $res1 = $this->actingAs($this->cliente1)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->servicio30m->id,
            'worker_id' => $this->trabajador1->id,
            'date' => $fecha,
            'time' => $hora,
        ]);
        $res1->assertStatus(200);

        // Segundo envío idéntico
        $res2 = $this->actingAs($this->cliente1)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->servicio30m->id,
            'worker_id' => $this->trabajador1->id,
            'date' => $fecha,
            'time' => $hora,
        ]);
        $res2->assertStatus(200);
        $res2->assertJson([
            'success' => true,
            'ya_existia' => true,
        ]);

        $this->assertEquals(1, Cita::where('trabajador_id', $this->trabajador1->id)
            ->whereDate('fecha', $fecha)
            ->where('hora_inicio', $hora)
            ->count());
    }

    /**
     * 7. Invocación atómica del servicio DisponibilidadService::registrarCitaConcurrente
     *    garantiza rollback total y bloqueo pesimista ante colisión directa.
     */
    public function test_servicio_disponibilidad_garantiza_exclusion_mutua(): void
    {
        $fecha = $this->getNextDateForDay(1); // Lunes
        $hora = '13:00';

        $disponibilidadService = app(\App\Services\DisponibilidadService::class);

        // Transacción 1: Registrar con éxito
        $res1 = $disponibilidadService->registrarCitaConcurrente(
            trabajador: $this->trabajador1,
            servicio: $this->servicio30m,
            cliente: $this->cliente1,
            fecha: $fecha,
            hora: $hora,
            origen: 'cliente'
        );

        $this->assertTrue($res1['success']);
        $this->assertEquals(200, $res1['status']);

        // Transacción 2: Intento concurrente en el mismo rango exacto
        $res2 = $disponibilidadService->registrarCitaConcurrente(
            trabajador: $this->trabajador1,
            servicio: $this->servicio30m,
            cliente: $this->cliente2,
            fecha: $fecha,
            hora: $hora,
            origen: 'cliente'
        );

        $this->assertFalse($res2['success']);
        $this->assertContains($res2['motivo_tipo'], ['cita', 'cita_simultanea']);
        $this->assertStringContainsString('no está disponible', $res2['mensaje']);

        // Total de citas en la base de datos es exactamente 1
        $this->assertEquals(1, Cita::where('trabajador_id', $this->trabajador1->id)
            ->whereDate('fecha', $fecha)
            ->where('hora_inicio', $hora)
            ->count());
    }
}
