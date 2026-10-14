<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\HorarioSucursal;
use App\Models\Service;
use App\Models\Trabajador;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrabajadorTraslapeServiciosTest extends TestCase
{
    use RefreshDatabase;

    protected User $cliente1;
    protected User $cliente2;
    protected User $recepcionista;
    protected User $trabajadorUser;
    protected Trabajador $trabajador;
    protected Service $servicioBarberia30m;
    protected Service $servicioEstetica60m;
    protected Service $servicioExpress15m;
    protected Service $servicioTratamiento45m;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\TestUsersSeeder::class);

        // Clientes
        $this->cliente1 = User::where('role_id', 4)->first();
        $this->cliente2 = User::create([
            'name' => 'Mariana',
            'apellidos' => 'López Gómez',
            'email' => 'mariana.cliente@test.com',
            'telefono' => '5551112233',
            'password' => bcrypt('Password123!'),
            'role_id' => 4,
        ]);

        // Recepcionista
        $this->recepcionista = User::where('role_id', 2)->first();

        // Especialista multidisciplinario (capacitado en barbería y estética)
        $this->trabajadorUser = User::where('role_id', 3)->first();
        $this->trabajador = Trabajador::create([
            'user_id' => $this->trabajadorUser->id,
            'direccion' => 'Estudio BBS Sucursal Principal',
            'experiencia' => 7,
            'activo' => true,
            'horario' => [
                'lunes' => ['entrada' => '09:00', 'salida' => '19:00'],
                'martes' => ['entrada' => '09:00', 'salida' => '19:00'],
                'miercoles' => ['entrada' => '09:00', 'salida' => '19:00'],
                'jueves' => ['entrada' => '09:00', 'salida' => '19:00'],
                'viernes' => ['entrada' => '09:00', 'salida' => '19:00'],
            ],
        ]);

        // Servicios con diferentes duraciones y categorías
        $this->servicioBarberia30m = Service::create([
            'name' => 'Corte Caballero Barbería',
            'description' => 'Corte clásico y perfilado de barba',
            'price' => 250.00,
            'duration_minutes' => 30,
            'category' => 'barberia',
            'is_active' => true,
        ]);

        $this->servicioEstetica60m = Service::create([
            'name' => 'Tinte y Efectos de Color',
            'description' => 'Colorimetría profesional y aplicación',
            'price' => 750.00,
            'duration_minutes' => 60,
            'category' => 'estetica',
            'is_active' => true,
        ]);

        $this->servicioExpress15m = Service::create([
            'name' => 'Arreglo Rápido de Patillas',
            'description' => 'Servicio express de 15 minutos',
            'price' => 100.00,
            'duration_minutes' => 15,
            'category' => 'barberia',
            'is_active' => true,
        ]);

        $this->servicioTratamiento45m = Service::create([
            'name' => 'Mascarilla Capilar Hidratante',
            'description' => 'Tratamiento intensivo de 45 minutos',
            'price' => 400.00,
            'duration_minutes' => 45,
            'category' => 'estetica',
            'is_active' => true,
        ]);

        // Asignar todos los servicios al trabajador
        $this->trabajador->servicios()->attach([
            $this->servicioBarberia30m->id,
            $this->servicioEstetica60m->id,
            $this->servicioExpress15m->id,
            $this->servicioTratamiento45m->id,
        ]);

        // Configuración de sucursal abierta
        HorarioSucursal::ensureTableExists();
        foreach (['lunes', 'martes', 'miercoles', 'jueves', 'viernes'] as $dia) {
            HorarioSucursal::updateOrCreate(
                ['dia' => $dia],
                ['abierto' => true, 'apertura' => '08:00', 'cierre' => '20:00']
            );
        }
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
     * 1. Dos servicios distintos de categorías diferentes (barbería 30m vs estética 60m):
     *    Si el trabajador ya tiene una cita de barbería a las 10:00 (10:00-10:30),
     *    se rechaza una cita de estética en el mismo horario (10:00-11:00).
     */
    public function test_rechaza_cita_mismo_horario_aunque_servicios_y_categorias_sean_distintas(): void
    {
        $fecha = $this->getNextDateForDay(1); // Lunes
        $hora = '10:00';

        // Cliente 1 agenda corte de barbería (30 min: 10:00 - 10:30)
        $res1 = $this->actingAs($this->cliente1)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->servicioBarberia30m->id,
            'worker_id' => $this->trabajador->id,
            'date' => $fecha,
            'time' => $hora,
            'notas' => 'Corte de barbería regular',
        ]);
        $res1->assertStatus(200);
        $this->assertTrue($res1->json('success'));

        // Cliente 2 intenta agendar tinte de estética (60 min: 10:00 - 11:00) con el mismo trabajador
        $res2 = $this->actingAs($this->cliente2)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->servicioEstetica60m->id,
            'worker_id' => $this->trabajador->id,
            'date' => $fecha,
            'time' => $hora,
            'notas' => 'Tinte de estética',
        ]);
        $res2->assertStatus(422);
        $this->assertFalse($res2->json('success'));
        $this->assertStringContainsString('no está disponible', $res2->json('mensaje'));

        // Debe haber únicamente 1 cita registrada en la base de datos para ese especialista
        $this->assertEquals(1, Cita::where('trabajador_id', $this->trabajador->id)->count());
    }

    /**
     * 2. Duraciones diferentes:
     *    Cita previa larga de estética (60m: 10:00 - 11:00).
     *    Intento de agendar servicio de barbería (30m) a las 10:30 (inicio en medio de la cita).
     *    Debe ser rechazada por traslape.
     */
    public function test_rechaza_segundo_servicio_que_inicia_durante_cita_previa_de_mayor_duracion(): void
    {
        $fecha = $this->getNextDateForDay(1); // Lunes

        // Cita 1: Estética 60 min (10:00 a 11:00)
        $res1 = $this->actingAs($this->cliente1)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->servicioEstetica60m->id,
            'worker_id' => $this->trabajador->id,
            'date' => $fecha,
            'time' => '10:00',
        ]);
        $res1->assertStatus(200);

        // Cita 2: Barbería 30 min intentando iniciar a las 10:30 (se traslapa con 10:00-11:00)
        $res2 = $this->actingAs($this->cliente2)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->servicioBarberia30m->id,
            'worker_id' => $this->trabajador->id,
            'date' => $fecha,
            'time' => '10:30',
        ]);
        $res2->assertStatus(422);
        $this->assertFalse($res2->json('success'));
        $this->assertStringContainsString('no está disponible', $res2->json('mensaje'));

        $this->assertEquals(1, Cita::where('trabajador_id', $this->trabajador->id)->count());
    }

    /**
     * 3. Duraciones diferentes:
     *    Cita previa corta (30m: 11:00 - 11:30).
     *    Intento de agendar servicio largo (60m) que inicia antes a las 10:30 y terminaría a las 11:30.
     *    Debe ser rechazada por traslape en el rango [11:00 - 11:30).
     */
    public function test_rechaza_servicio_largo_que_inicia_antes_y_se_traslapa_con_servicio_previo(): void
    {
        $fecha = $this->getNextDateForDay(1); // Lunes

        // Cita 1: Barbería 30 min (11:00 a 11:30)
        $res1 = $this->actingAs($this->cliente1)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->servicioBarberia30m->id,
            'worker_id' => $this->trabajador->id,
            'date' => $fecha,
            'time' => '11:00',
        ]);
        $res1->assertStatus(200);

        // Cita 2: Estética 60 min a las 10:30 (su intervalo sería 10:30 a 11:30, solapando con 11:00-11:30)
        $res2 = $this->actingAs($this->cliente2)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->servicioEstetica60m->id,
            'worker_id' => $this->trabajador->id,
            'date' => $fecha,
            'time' => '10:30',
        ]);
        $res2->assertStatus(422);
        $this->assertFalse($res2->json('success'));

        $this->assertEquals(1, Cita::where('trabajador_id', $this->trabajador->id)->count());
    }

    /**
     * 4. Duración corta (15 min) dentro de servicio de 45 min:
     *    Cita previa: Tratamiento capilar 45 min (14:00 - 14:45).
     *    Intento: Servicio express 15 min a las 14:15 o 14:30.
     *    Ambos deben ser rechazados.
     */
    public function test_rechaza_servicio_express_contenido_dentro_de_intervalo_de_servicio_largo(): void
    {
        $fecha = $this->getNextDateForDay(1); // Lunes

        // Cita 1: 45 minutos (14:00 - 14:45)
        $res1 = $this->actingAs($this->cliente1)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->servicioTratamiento45m->id,
            'worker_id' => $this->trabajador->id,
            'date' => $fecha,
            'time' => '14:00',
        ]);
        $res1->assertStatus(200);

        // Cita 2: 15 minutos a las 14:15 (completamente dentro del intervalo 14:00-14:45)
        $res2 = $this->actingAs($this->cliente2)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->servicioExpress15m->id,
            'worker_id' => $this->trabajador->id,
            'date' => $fecha,
            'time' => '14:15',
        ]);
        $res2->assertStatus(422);

        // Cita 3: 15 minutos a las 14:30 (dentro de 14:00-14:45)
        $res3 = $this->actingAs($this->cliente2)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->servicioExpress15m->id,
            'worker_id' => $this->trabajador->id,
            'date' => $fecha,
            'time' => '14:30',
        ]);
        $res3->assertStatus(422);

        $this->assertEquals(1, Cita::where('trabajador_id', $this->trabajador->id)->count());
    }

    /**
     * 5. Citas consecutivas adyacentes:
     *    Cita 1 (Barbería 30m): 10:00 a 10:30.
     *    Cita 2 (Estética 60m): 10:30 a 11:30.
     *    Cita 3 (Express 15m): 11:30 a 11:45.
     *    No hay traslape en las fronteras de los intervalos [10:00, 10:30), [10:30, 11:30) y [11:30, 11:45).
     *    Todas deben ser permitidas consecutivamente para el mismo trabajador.
     */
    public function test_permite_citas_consecutivas_adyacentes_de_servicios_distintos(): void
    {
        $fecha = $this->getNextDateForDay(1); // Lunes

        // Cita 1: 10:00 - 10:30 (30 min barbería)
        $res1 = $this->actingAs($this->cliente1)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->servicioBarberia30m->id,
            'worker_id' => $this->trabajador->id,
            'date' => $fecha,
            'time' => '10:00',
        ]);
        $res1->assertStatus(200);

        // Cita 2: 10:30 - 11:30 (60 min estética)
        $res2 = $this->actingAs($this->cliente2)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->servicioEstetica60m->id,
            'worker_id' => $this->trabajador->id,
            'date' => $fecha,
            'time' => '10:30',
        ]);
        $res2->assertStatus(200);

        // Cita 3: 11:30 - 11:45 (15 min express)
        $res3 = $this->actingAs($this->cliente1)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->servicioExpress15m->id,
            'worker_id' => $this->trabajador->id,
            'date' => $fecha,
            'time' => '11:30',
        ]);
        $res3->assertStatus(200);

        // Las 3 citas deben estar registradas para el mismo especialista
        $this->assertEquals(3, Cita::where('trabajador_id', $this->trabajador->id)->count());
    }

    /**
     * 6. Citas canceladas no bloquean el horario para otro servicio distinto:
     *    Una cita previa de barbería a las 10:00 (10:00-10:30) cancelada
     *    no debe bloquear la reservación de un servicio de estética a las 10:00 (10:00-11:00).
     */
    public function test_cita_cancelada_no_bloquea_horario_para_otro_servicio_del_mismo_trabajador(): void
    {
        $fecha = $this->getNextDateForDay(1); // Lunes

        // Crear una cita cancelada previamente para este especialista
        Cita::create([
            'user_id' => $this->cliente1->id,
            'trabajador_id' => $this->trabajador->id,
            'service_id' => $this->servicioBarberia30m->id,
            'fecha' => $fecha,
            'hora_inicio' => '10:00',
            'hora_fin' => '10:30',
            'duracion_minutos' => 30,
            'precio' => 250.00,
            'estado' => 'cancelada',
        ]);

        // Cliente 2 agenda servicio de estética (60 min) en el mismo horario 10:00
        $res = $this->actingAs($this->cliente2)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->servicioEstetica60m->id,
            'worker_id' => $this->trabajador->id,
            'date' => $fecha,
            'time' => '10:00',
        ]);

        $res->assertStatus(200);
        $this->assertTrue($res->json('success'));

        // Citas activas debe ser 1 (la nueva)
        $citasActivas = Cita::where('trabajador_id', $this->trabajador->id)
            ->whereNotIn('estado', ['cancelada', 'cancelled', 'anulada', 'rechazada'])
            ->count();
        $this->assertEquals(1, $citasActivas);
    }

    /**
     * 7. Misma regla aplicada para agendamiento desde la recepcionista:
     *    Cliente tiene reservado Corte Caballero (30m, 11:00-11:30).
     *    Recepcionista intenta agendar a otro cliente un Tratamiento Capilar (45m, 11:15-12:00)
     *    con el mismo especialista.
     *    Debe ser rechazada con HTTP 422.
     */
    public function test_recepcionista_no_puede_agendar_servicio_traslapado_para_el_mismo_trabajador(): void
    {
        $fecha = $this->getNextDateForDay(1); // Lunes

        // Cliente 1 agenda corte de barbería (11:00 - 11:30)
        $resCliente = $this->actingAs($this->cliente1)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->servicioBarberia30m->id,
            'worker_id' => $this->trabajador->id,
            'date' => $fecha,
            'time' => '11:00',
        ]);
        $resCliente->assertStatus(200);

        // Recepcionista intenta agendar tratamiento capilar (45m) a las 11:15 para Cliente 2 con el mismo trabajador
        $resRecepcion = $this->actingAs($this->recepcionista)->postJson(route('recepcion.agendamiento.confirmar'), [
            'client_id' => $this->cliente2->id,
            'service_id' => $this->servicioTratamiento45m->id,
            'worker_id' => $this->trabajador->id,
            'date' => $fecha,
            'time' => '11:15',
        ]);

        $resRecepcion->assertStatus(422);
        $this->assertFalse($resRecepcion->json('success'));
        $this->assertStringContainsString('no está disponible', $resRecepcion->json('mensaje'));

        $this->assertEquals(1, Cita::where('trabajador_id', $this->trabajador->id)->count());
    }

    /**
     * 8. Consulta de disponibilidad excluye horarios afectados por citas de otra categoría/duración:
     *    Si el trabajador tiene una cita de 60m (10:00 a 11:00),
     *    al consultar disponibilidad para un servicio de 30m,
     *    los horarios 10:00, 10:15, 10:30 y 10:45 no deben aparecer,
     *    mientras que el horario 11:00 sí debe aparecer.
     */
    public function test_consultar_disponibilidad_excluye_intervalos_traslapados_de_servicio_largo(): void
    {
        $fecha = $this->getNextDateForDay(1); // Lunes

        // Registrar cita de estética de 60m (10:00 - 11:00)
        Cita::create([
            'user_id' => $this->cliente1->id,
            'trabajador_id' => $this->trabajador->id,
            'service_id' => $this->servicioEstetica60m->id,
            'fecha' => $fecha,
            'hora_inicio' => '10:00',
            'hora_fin' => '11:00',
            'duracion_minutos' => 60,
            'precio' => 750.00,
            'estado' => 'confirmada',
        ]);

        // Consultar disponibilidad para servicio de barbería (30 min)
        $res = $this->actingAs($this->cliente2)->getJson(route('cliente.agendamiento.disponibilidad', [
            'service_id' => $this->servicioBarberia30m->id,
            'worker_id' => $this->trabajador->id,
            'date' => $fecha,
        ]));

        $res->assertStatus(200);
        $horarios = $res->json('horarios');

        // Horarios dentro del intervalo [10:00, 11:00) no deben estar disponibles para 30 min
        $this->assertNotContains('10:00', $horarios);
        $this->assertNotContains('10:15', $horarios);
        $this->assertNotContains('10:30', $horarios);
        $this->assertNotContains('10:45', $horarios);

        // El horario inmediatamente consecutivo (11:00) sí debe estar disponible
        $this->assertContains('11:00', $horarios);
    }

    /**
     * 9. Endpoint validarHorario detecta y rechaza traslape de servicios de distinto tipo:
     */
    public function test_validar_horario_detecta_conflicto_de_servicio_distinto_para_el_mismo_trabajador(): void
    {
        $fecha = $this->getNextDateForDay(1); // Lunes

        // Cita previa de barbería 30m (15:00 a 15:30)
        Cita::create([
            'user_id' => $this->cliente1->id,
            'trabajador_id' => $this->trabajador->id,
            'service_id' => $this->servicioBarberia30m->id,
            'fecha' => $fecha,
            'hora_inicio' => '15:00',
            'hora_fin' => '15:30',
            'duracion_minutos' => 30,
            'precio' => 250.00,
            'estado' => 'confirmada',
        ]);

        // Validar horario para servicio de estética 60m a las 14:45 (traslape de 15:00 a 15:30)
        $res = $this->actingAs($this->cliente2)->postJson(route('cliente.agendamiento.validar-horario'), [
            'service_id' => $this->servicioEstetica60m->id,
            'worker_id' => $this->trabajador->id,
            'date' => $fecha,
            'time' => '14:45',
        ]);

        $res->assertStatus(422);
        $this->assertFalse($res->json('valido'));
        $this->assertStringContainsString('no está disponible', $res->json('mensaje'));
    }
}
