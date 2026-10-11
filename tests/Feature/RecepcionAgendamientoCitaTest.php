<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\HorarioSucursal;
use App\Models\Service;
use App\Models\Trabajador;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecepcionAgendamientoCitaTest extends TestCase
{
    use RefreshDatabase;

    protected User $recepcionistaUser;
    protected User $adminUser;
    protected User $clienteUser;
    protected User $trabajadorUser;
    protected Trabajador $trabajador;
    protected Service $servicio;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\TestUsersSeeder::class);

        $this->recepcionistaUser = User::where('role_id', 2)->first();
        $this->adminUser = User::where('role_id', 1)->first();
        $this->clienteUser = User::where('role_id', 4)->first();
        $this->trabajadorUser = User::where('role_id', 3)->first();

        // Configuración de trabajador
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
            'name' => 'Corte & Peinado Studio',
            'description' => 'Servicio de corte',
            'category' => 'barberia',
            'duration_minutes' => 30,
            'price' => 250.00,
            'is_active' => true,
        ]);

        $this->trabajador->servicios()->attach($this->servicio->id);

        Cita::ensureTableExists();
        HorarioSucursal::ensureTableExists();
        foreach (['lunes', 'martes', 'miercoles', 'jueves', 'viernes'] as $dia) {
            HorarioSucursal::updateOrCreate(
                ['dia' => $dia],
                ['abierto' => true, 'apertura' => '08:00', 'cierre' => '20:00']
            );
        }
    }

    protected function getNextMonday(): string
    {
        $date = now()->addDays(1);
        while ($date->dayOfWeek !== 1) { // 1 = Monday
            $date->addDay();
        }
        return $date->toDateString();
    }

    /**
     * Prueba 1: La recepcionista puede entrar a su panel y visualizar el sidebar
     * con las opciones Inicio, Gestión de citas, Calendario y Clientes.
     */
    public function test_recepcionista_puede_ingresar_al_panel_y_visualizar_el_sidebar()
    {
        $response = $this->actingAs($this->recepcionistaUser)->get(route('recepcion.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Panel de Recepción');
        $response->assertSee('Inicio');
        $response->assertSee('Gestión de citas');
        $response->assertSee('Calendario');
        $response->assertSee('Clientes');
    }

    /**
     * Prueba 2: La opción Gestión de citas permite acceder al agendamiento.
     */
    public function test_gestion_de_citas_permite_acceder_al_agendamiento()
    {
        $responseCitas = $this->actingAs($this->recepcionistaUser)->get(route('recepcion.citas'));
        $responseCitas->assertStatus(200);
        $responseCitas->assertSee(route('recepcion.agendamiento'));
        $responseCitas->assertSee('Agendar nueva cita');

        $responseAgendamiento = $this->actingAs($this->recepcionistaUser)->get(route('recepcion.agendamiento'));
        $responseAgendamiento->assertStatus(200);
        $responseAgendamiento->assertSee('Agendamiento de Citas');
        $responseAgendamiento->assertSee('Selecciona un cliente');
    }

    /**
     * Prueba 3: Puede seleccionar cliente, servicio, trabajador, fecha y consultar disponibilidad.
     */
    public function test_puede_consultar_disponibilidad_para_un_cliente_y_trabajador()
    {
        $nextMonday = $this->getNextMonday();

        $response = $this->actingAs($this->recepcionistaUser)->getJson(route('recepcion.agendamiento.disponibilidad', [
            'service_id' => $this->servicio->id,
            'worker_id' => $this->trabajador->id,
            'date' => $nextMonday,
        ]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'horarios',
            'duracion_minutos',
            'fecha',
            'trabajador_nombre',
            'servicio_nombre',
        ]);

        $this->assertNotEmpty($response->json('horarios'));
        $this->assertContains('09:00', $response->json('horarios'));
    }

    /**
     * Prueba 4: Solo se ofrecen horarios disponibles (respeta descansos y citas previas).
     */
    public function test_solo_se_ofrecen_horarios_disponibles_respetando_descanso_y_citas()
    {
        $nextMonday = $this->getNextMonday();

        // Crear una cita existente a las 10:00 - 10:30
        Cita::create([
            'user_id' => $this->clienteUser->id,
            'service_id' => $this->servicio->id,
            'trabajador_id' => $this->trabajador->id,
            'fecha' => $nextMonday,
            'hora_inicio' => '10:00:00',
            'hora_fin' => '10:30:00',
            'duracion_minutos' => 30,
            'precio' => 250.00,
            'estado' => 'confirmada',
        ]);

        $response = $this->actingAs($this->recepcionistaUser)->getJson(route('recepcion.agendamiento.disponibilidad', [
            'service_id' => $this->servicio->id,
            'worker_id' => $this->trabajador->id,
            'date' => $nextMonday,
        ]));

        $response->assertStatus(200);
        $horarios = $response->json('horarios');

        // 10:00 debe estar excluida porque ya existe una cita confirmada
        $this->assertNotContains('10:00', $horarios);

        // 14:00 y 14:30 deben estar excluidas porque son horario de descanso
        $this->assertNotContains('14:00', $horarios);
        $this->assertNotContains('14:30', $horarios);

        // 09:00 y 11:00 deben seguir disponibles
        $this->assertContains('09:00', $horarios);
        $this->assertContains('11:00', $horarios);
    }

    /**
     * Prueba 5: La cita se guarda correctamente, asociada al cliente seleccionado (no a la recepcionista),
     * con estado inicial 'confirmada' y precio/duración del servicio.
     */
    public function test_la_cita_se_guarda_asociada_al_cliente_seleccionado_con_estado_confirmada()
    {
        $nextMonday = $this->getNextMonday();

        $payload = [
            'client_id' => $this->clienteUser->id,
            'service_id' => $this->servicio->id,
            'worker_id' => $this->trabajador->id,
            'date' => $nextMonday,
            'time' => '11:00',
        ];

        $response = $this->actingAs($this->recepcionistaUser)
            ->postJson(route('recepcion.agendamiento.confirmar'), $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        // Verificar en BD que la cita quedó asociada al cliente, no a la recepcionista
        $this->assertDatabaseHas('citas', [
            'user_id' => $this->clienteUser->id,
            'service_id' => $this->servicio->id,
            'trabajador_id' => $this->trabajador->id,
            'fecha' => $nextMonday,
            'hora_inicio' => '11:00',
            'duracion_minutos' => 30,
            'precio' => 250.00,
            'estado' => 'confirmada',
        ]);

        $cita = Cita::where('fecha', $nextMonday)->where('hora_inicio', '11:00')->first();
        $this->assertNotNull($cita);
        $this->assertNotEquals($this->recepcionistaUser->id, $cita->user_id);
        $this->assertEquals($this->clienteUser->id, $cita->user_id);
    }

    /**
     * Prueba 6: La confirmación aparece únicamente si fue exitoso; si el horario se ocupó
     * o no está disponible, se rechaza y no se duplica.
     */
    public function test_rechaza_registro_si_horario_deja_de_estar_disponible_o_se_traslapa()
    {
        $nextMonday = $this->getNextMonday();

        // Primera reserva exitosa
        $payload = [
            'client_id' => $this->clienteUser->id,
            'service_id' => $this->servicio->id,
            'worker_id' => $this->trabajador->id,
            'date' => $nextMonday,
            'time' => '09:30',
        ];

        $resp1 = $this->actingAs($this->recepcionistaUser)
            ->postJson(route('recepcion.agendamiento.confirmar'), $payload);
        $resp1->assertStatus(200);
        $resp1->assertJson(['success' => true]);

        // Segundo intento sobre el mismo horario con otro cliente debe fallar por traslape
        $otroCliente = User::factory()->create([
            'role_id' => 4,
            'apellidos' => 'Gómez',
            'telefono' => '55' . rand(10000000, 99999999),
        ]);
        $payloadConflictivo = array_merge($payload, ['client_id' => $otroCliente->id]);

        $resp2 = $this->actingAs($this->recepcionistaUser)
            ->postJson(route('recepcion.agendamiento.confirmar'), $payloadConflictivo);
        $resp2->assertStatus(422);
        $resp2->assertJson([
            'success' => false,
            'valido' => false,
        ]);

        // Intentar registrar con un cliente inválido (inexistente)
        $payloadInvalido = array_merge($payload, ['client_id' => 999999, 'time' => '12:00']);
        $resp3 = $this->actingAs($this->recepcionistaUser)
            ->postJson(route('recepcion.agendamiento.confirmar'), $payloadInvalido);
        $resp3->assertStatus(422);
    }

    /**
     * Prueba 7: Los roles no autorizados (clientes rol 4, trabajadores rol 3, no autenticados)
     * no pueden acceder al panel de recepcionista ni a sus endpoints.
     */
    public function test_roles_no_autorizados_no_pueden_acceder_al_panel_ni_al_agendamiento()
    {
        // 1. Cliente rol 4 intenta acceder al dashboard de recepcion
        $respCliente = $this->actingAs($this->clienteUser)->get(route('recepcion.dashboard'));
        $respCliente->assertStatus(403);

        // 2. Cliente intenta agendar via endpoint de recepcion
        $respClientePost = $this->actingAs($this->clienteUser)->postJson(route('recepcion.agendamiento.confirmar'), [
            'client_id' => $this->clienteUser->id,
            'service_id' => $this->servicio->id,
            'trabajador_id' => $this->trabajador->id,
            'fecha' => now()->next('Monday')->format('Y-m-d'),
            'hora' => '10:00',
        ]);
        $respClientePost->assertStatus(403);

        // 3. Trabajador rol 3 intenta acceder
        $respTrabajador = $this->actingAs($this->trabajadorUser)->get(route('recepcion.dashboard'));
        $respTrabajador->assertStatus(403);

        // 4. Usuario no autenticado es redirigido al login
        auth()->logout();
        $respGuest = $this->get(route('recepcion.dashboard'));
        $respGuest->assertRedirect(route('login'));
    }
}
