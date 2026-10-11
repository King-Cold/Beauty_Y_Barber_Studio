<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\HorarioSucursal;
use App\Models\Service;
use App\Models\Trabajador;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClienteRegistroCitaTest extends TestCase
{
    use RefreshDatabase;

    protected User $clienteUser;
    protected User $trabajadorUser;
    protected Trabajador $trabajador;
    protected Service $servicio;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\TestUsersSeeder::class);

        $this->clienteUser = User::where('role_id', 4)->first();
        $this->trabajadorUser = User::where('role_id', 3)->first();

        // Trabajador disponible lunes a viernes 09:00 a 18:00 con descanso de 14:00 a 15:00
        $this->trabajador = Trabajador::create([
            'user_id' => $this->trabajadorUser->id,
            'direccion' => 'Calle Estudio 456',
            'experiencia' => 4,
            'activo' => true,
            'horario' => [
                'lunes' => [
                    'entrada' => '09:00',
                    'salida' => '18:00',
                    'descanso_inicio' => '14:00',
                    'descanso_fin' => '15:00',
                ],
                'martes' => [
                    'entrada' => '09:00',
                    'salida' => '18:00',
                ],
                'miercoles' => [
                    'entrada' => '09:00',
                    'salida' => '18:00',
                ],
                'jueves' => [
                    'entrada' => '09:00',
                    'salida' => '18:00',
                ],
                'viernes' => [
                    'entrada' => '09:00',
                    'salida' => '18:00',
                ],
            ],
        ]);

        $this->servicio = Service::create([
            'name' => 'Corte Premium & Estilo',
            'description' => 'Servicio completo de corte con lavado.',
            'price' => 250.00,
            'duration_minutes' => 30,
            'category' => 'barberia',
            'is_active' => true,
        ]);

        $this->trabajador->servicios()->attach($this->servicio->id);

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
     * 1. Una cita confirmada genera un registro real en la base de datos.
     */
    public function test_cita_confirmada_genera_registro_real_en_base_de_datos(): void
    {
        $fecha = $this->getNextDateForDay(1); // Lunes

        $response = $this->actingAs($this->clienteUser)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->servicio->id,
            'worker_id' => $this->trabajador->id,
            'date' => $fecha,
            'time' => '10:00',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'valido' => true,
        ]);

        $this->assertDatabaseHas('citas', [
            'user_id' => $this->clienteUser->id,
            'trabajador_id' => $this->trabajador->id,
            'service_id' => $this->servicio->id,
            'fecha' => $fecha,
            'hora_inicio' => '10:00',
            'hora_fin' => '10:30',
            'duracion_minutos' => 30,
            'precio' => 250.00,
            'estado' => 'confirmada',
        ]);

        $this->assertEquals(1, Cita::count());
    }

    /**
     * 2. Los datos guardados coinciden con los seleccionados en el resumen.
     */
    public function test_datos_guardados_coinciden_con_seleccion_y_servicio(): void
    {
        $fecha = $this->getNextDateForDay(2); // Martes

        $response = $this->actingAs($this->clienteUser)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->servicio->id,
            'worker_id' => $this->trabajador->id,
            'date' => $fecha,
            'time' => '11:30',
            'notas' => 'Corte sin máquina en la parte superior',
        ]);

        $response->assertStatus(200);
        $cita = Cita::first();

        $this->assertNotNull($cita);
        $this->assertEquals($this->servicio->id, $cita->service_id);
        $this->assertEquals($this->trabajador->id, $cita->trabajador_id);
        $this->assertEquals($fecha, $cita->fecha->format('Y-m-d'));
        $this->assertEquals('11:30', $cita->hora_inicio);
        $this->assertEquals('12:00', $cita->hora_fin);
        $this->assertEquals('Corte sin máquina en la parte superior', $cita->notas);
        $this->assertEquals('confirmada', $cita->estado);
    }

    /**
     * 3. La cita queda asociada al cliente autenticado.
     */
    public function test_cita_queda_asociada_al_cliente_autenticado(): void
    {
        $fecha = $this->getNextDateForDay(1);

        $response = $this->actingAs($this->clienteUser)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->servicio->id,
            'worker_id' => $this->trabajador->id,
            'date' => $fecha,
            'time' => '12:00',
        ]);

        $response->assertStatus(200);
        $cita = Cita::first();

        $this->assertEquals($this->clienteUser->id, $cita->user_id);
        $this->assertEquals($this->clienteUser->email, $cita->cliente->email);
    }

    /**
     * 4. No se puede registrar una cita en un horario que dejó de estar disponible.
     */
    public function test_no_se_puede_registrar_cita_en_horario_no_disponible(): void
    {
        $fecha = $this->getNextDateForDay(1);

        // El trabajador tiene descanso de 14:00 a 15:00
        $response = $this->actingAs($this->clienteUser)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->servicio->id,
            'worker_id' => $this->trabajador->id,
            'date' => $fecha,
            'time' => '14:00',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'valido' => false,
        ]);

        // La base de datos no debe tener ninguna cita creada
        $this->assertEquals(0, Cita::count());
    }

    /**
     * 4b. No se puede registrar una cita que colisione con otra cita existente.
     */
    public function test_no_se_puede_registrar_cita_simultanea_con_otra_existente(): void
    {
        $fecha = $this->getNextDateForDay(1);

        // Creamos una primera cita de 09:30 a 10:00
        Cita::create([
            'user_id' => $this->clienteUser->id,
            'trabajador_id' => $this->trabajador->id,
            'service_id' => $this->servicio->id,
            'fecha' => $fecha,
            'hora_inicio' => '09:30',
            'hora_fin' => '10:00',
            'duracion_minutos' => 30,
            'precio' => 250.00,
            'estado' => 'confirmada',
        ]);

        $otroCliente = User::create([
            'name' => 'Otro',
            'apellidos' => 'Cliente Test',
            'email' => 'otro.cliente@test.com',
            'telefono' => '9998887766',
            'password' => bcrypt('Password123!'),
            'role_id' => 4,
        ]);

        // El segundo cliente intenta agendar exactamente a las 09:30
        $response = $this->actingAs($otroCliente)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->servicio->id,
            'worker_id' => $this->trabajador->id,
            'date' => $fecha,
            'time' => '09:30',
        ]);

        $response->assertStatus(422);
        $this->assertStringContainsString('no está disponible', $response->json('mensaje'));

        // Solo debe haber la primera cita en la base de datos
        $this->assertEquals(1, Cita::count());
    }

    /**
     * 5. Si falla la validación o el guardado, no se muestra mensaje de éxito.
     */
    public function test_si_falla_guardado_no_muestra_exito(): void
    {
        // Enviar parámetros incompletos (sin trabajador ni horario)
        $response = $this->actingAs($this->clienteUser)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->servicio->id,
        ]);

        $response->assertStatus(422);
        $this->assertNotTrue($response->json('success'));
        $this->assertEquals(0, Cita::count());
    }

    /**
     * 6. Los envíos repetidos no generan citas duplicadas.
     */
    public function test_envios_repetidos_no_generan_citas_duplicadas(): void
    {
        $fecha = $this->getNextDateForDay(1);

        // Envío 1
        $res1 = $this->actingAs($this->clienteUser)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->servicio->id,
            'worker_id' => $this->trabajador->id,
            'date' => $fecha,
            'time' => '10:30',
        ]);
        $res1->assertStatus(200);

        // Envío 2 repetido inmediatamente con los mismos datos
        $res2 = $this->actingAs($this->clienteUser)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->servicio->id,
            'worker_id' => $this->trabajador->id,
            'date' => $fecha,
            'time' => '10:30',
        ]);
        $res2->assertStatus(200);

        // La base de datos debe contener únicamente 1 registro, no 2
        $this->assertEquals(1, Cita::where('trabajador_id', $this->trabajador->id)
            ->whereDate('fecha', $fecha)
            ->where('hora_inicio', '10:30')
            ->count());
    }

    /**
     * 7. El precio y duración se obtienen del servicio en BD sin confiar en valores manipulados del frontend.
     */
    public function test_precio_y_duracion_se_toman_del_servicio_en_bd(): void
    {
        $fecha = $this->getNextDateForDay(1);

        // El usuario intenta enviar un precio de $1.00 y duración de 5 min manipulados
        $response = $this->actingAs($this->clienteUser)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->servicio->id,
            'worker_id' => $this->trabajador->id,
            'date' => $fecha,
            'time' => '13:00',
            'precio' => 1.00,
            'duration_minutes' => 5,
        ]);

        $response->assertStatus(200);
        $cita = Cita::where('hora_inicio', '13:00')->first();

        // Debe conservar los valores reales del servicio en BD ($250.00 y 30 minutos)
        $this->assertEquals(250.00, (float)$cita->precio);
        $this->assertEquals(30, $cita->duracion_minutos);
        $this->assertEquals('13:30', $cita->hora_fin);
    }
}
