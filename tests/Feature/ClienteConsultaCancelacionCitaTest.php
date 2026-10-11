<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\Service;
use App\Models\Trabajador;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClienteConsultaCancelacionCitaTest extends TestCase
{
    use RefreshDatabase;

    protected User $clienteA;
    protected User $clienteB;
    protected User $trabajadorUser;
    protected Trabajador $trabajador;
    protected Service $servicioCorte;
    protected Service $servicioColor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\TestUsersSeeder::class);

        // Cliente principal (Rol 4)
        $this->clienteA = User::where('role_id', 4)->first();

        // Segundo cliente para pruebas de autorización y aislamiento
        $this->clienteB = User::create([
            'name' => 'Marcos',
            'apellidos' => 'Hernández',
            'email' => 'marcos.cliente@test.com',
            'telefono' => '5559998877',
            'password' => bcrypt('password123'),
            'role_id' => 4,
        ]);

        // Trabajador
        $this->trabajadorUser = User::where('role_id', 3)->first();
        $this->trabajador = Trabajador::firstOrCreate(
            ['user_id' => $this->trabajadorUser->id],
            [
                'direccion' => 'Av. Principal 123',
                'experiencia' => 5,
                'activo' => true,
                'horario' => [
                    'lunes' => ['entrada' => '09:00', 'salida' => '18:00'],
                    'martes' => ['entrada' => '09:00', 'salida' => '18:00'],
                    'miercoles' => ['entrada' => '09:00', 'salida' => '18:00'],
                    'jueves' => ['entrada' => '09:00', 'salida' => '18:00'],
                    'viernes' => ['entrada' => '09:00', 'salida' => '18:00'],
                ],
            ]
        );

        // Servicios
        $this->servicioCorte = Service::create([
            'name' => 'Corte Degradado Barber',
            'description' => 'Corte moderno con degradado fino.',
            'price' => 200.00,
            'duration_minutes' => 30,
            'category' => 'barberia',
            'is_active' => true,
        ]);

        $this->servicioColor = Service::create([
            'name' => 'Coloración & Balayage',
            'description' => 'Servicio estético de color.',
            'price' => 650.00,
            'duration_minutes' => 60,
            'category' => 'estetica',
            'is_active' => true,
        ]);

        $this->trabajador->servicios()->syncWithoutDetaching([
            $this->servicioCorte->id,
            $this->servicioColor->id,
        ]);

        Cita::ensureTableExists();
    }

    /**
     * Caso 1: El cliente puede consultar sus propias citas.
     * Muestra la información principal: servicio, trabajador, fecha, hora, precio y estado.
     * Si no tiene citas registradas, muestra el mensaje informativo conservando el diseño.
     */
    public function test_cliente_puede_consultar_sus_propias_citas_con_toda_la_informacion_principal(): void
    {
        // 1.1 Si el cliente no tiene citas registradas, muestra el mensaje informativo y diseño
        $responseVacio = $this->actingAs($this->clienteA)->get('/cliente/historial');
        $responseVacio->assertStatus(200);
        $responseVacio->assertSee('Historial de citas');
        $responseVacio->assertSee('No tienes citas registradas');
        $responseVacio->assertSee('Agendar mi primera cita');

        // 1.2 Creamos una cita para el cliente A
        $fechaFutura = Carbon::now()->addDays(2)->format('Y-m-d');
        $cita = Cita::create([
            'user_id' => $this->clienteA->id,
            'trabajador_id' => $this->trabajador->id,
            'service_id' => $this->servicioCorte->id,
            'fecha' => $fechaFutura,
            'hora_inicio' => '11:00',
            'hora_fin' => '11:30',
            'duracion_minutos' => 30,
            'precio' => 200.00,
            'estado' => 'confirmada',
        ]);

        // Consulta en vista Blade
        $response = $this->actingAs($this->clienteA)->get('/cliente/historial');
        $response->assertStatus(200);
        $response->assertSee('Historial de citas');
        $response->assertSee('Corte Degradado Barber');
        $response->assertSee($this->trabajadorUser->name);
        $response->assertSee('11:00 - 11:30');
        $response->assertSee('$200.00');
        $response->assertSee('Confirmada');
        $response->assertSee('Cancelar cita');

        // Consulta vía API JSON (/cliente/citas o con header Accept: application/json)
        $responseJson = $this->actingAs($this->clienteA)->json('GET', '/cliente/citas');
        $responseJson->assertStatus(200);
        $responseJson->assertJsonFragment(['total' => 1]);
        $responseJson->assertJsonFragment(['name' => 'Corte Degradado Barber']);
    }

    /**
     * Caso 2: Un cliente no puede consultar ni cancelar citas pertenecientes a otra persona.
     * Protección contra URL tampering y manipulación de IDs.
     */
    public function test_cliente_no_puede_consultar_ni_cancelar_citas_de_otro_usuario(): void
    {
        // Cita perteneciente al cliente B
        $citaClienteB = Cita::create([
            'user_id' => $this->clienteB->id,
            'trabajador_id' => $this->trabajador->id,
            'service_id' => $this->servicioColor->id,
            'fecha' => Carbon::now()->addDays(3)->format('Y-m-d'),
            'hora_inicio' => '15:00',
            'hora_fin' => '16:00',
            'duracion_minutos' => 60,
            'precio' => 650.00,
            'estado' => 'confirmada',
        ]);

        // 2.1 El cliente A consulta su historial: NO debe ver la cita del cliente B
        $responseHistorialA = $this->actingAs($this->clienteA)->get('/cliente/historial');
        $responseHistorialA->assertStatus(200);
        $responseHistorialA->assertDontSee('Coloración & Balayage');
        $responseHistorialA->assertDontSee('$650.00');

        // 2.2 El cliente A intenta consultar directamente la cita del cliente B vía URL (/cliente/citas/{id})
        $responseShowAjeno = $this->actingAs($this->clienteA)->get("/cliente/citas/{$citaClienteB->id}");
        $responseShowAjeno->assertStatus(403);

        $responseShowAjenoJson = $this->actingAs($this->clienteA)->json('GET', "/cliente/citas/{$citaClienteB->id}");
        $responseShowAjenoJson->assertStatus(403);
        $responseShowAjenoJson->assertJsonFragment(['success' => false]);

        // 2.3 El cliente A intenta cancelar la cita del cliente B vía POST/PATCH (/cliente/citas/{id}/cancelar)
        $responseCancelarAjeno = $this->actingAs($this->clienteA)->json('POST', "/cliente/citas/{$citaClienteB->id}/cancelar");
        $responseCancelarAjeno->assertStatus(403);
        $responseCancelarAjeno->assertJsonFragment([
            'success' => false,
            'mensaje' => 'No tienes autorización para cancelar esta cita.',
        ]);

        // La cita del cliente B permanece confirmada sin alteración
        $this->assertDatabaseHas('citas', [
            'id' => $citaClienteB->id,
            'estado' => 'confirmada',
        ]);
    }

    /**
     * Caso 3: Se puede cancelar una cita cuando faltan más de 30 minutos.
     */
    public function test_se_puede_cancelar_cita_cuando_faltan_mas_de_30_minutos(): void
    {
        // Programamos una cita con 2 horas de anticipación
        $fechaHoy = Carbon::now()->format('Y-m-d');
        $horaFutura = Carbon::now()->addHours(2);

        $cita = Cita::create([
            'user_id' => $this->clienteA->id,
            'trabajador_id' => $this->trabajador->id,
            'service_id' => $this->servicioCorte->id,
            'fecha' => $fechaHoy,
            'hora_inicio' => $horaFutura->format('H:i'),
            'hora_fin' => $horaFutura->copy()->addMinutes(30)->format('H:i'),
            'duracion_minutos' => 30,
            'precio' => 200.00,
            'estado' => 'confirmada',
        ]);

        $this->assertTrue($cita->puedeCancelar());

        $response = $this->actingAs($this->clienteA)->json('POST', "/cliente/citas/{$cita->id}/cancelar");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $response->assertJsonFragment([
            'mensaje' => "Tu cita para {$this->servicioCorte->name} ha sido cancelada exitosamente.",
        ]);

        // Verificamos que el estado en la base de datos se actualizó a 'cancelada'
        $this->assertDatabaseHas('citas', [
            'id' => $cita->id,
            'estado' => 'cancelada',
        ]);
    }

    /**
     * Caso 4: No se puede cancelar cuando faltan 30 minutos o menos.
     */
    public function test_no_se_puede_cancelar_cuando_faltan_30_minutos_o_menos(): void
    {
        // Programamos una cita que inicia en 20 minutos
        $fechaHoy = Carbon::now()->format('Y-m-d');
        $horaInminente = Carbon::now()->addMinutes(20);

        $cita = Cita::create([
            'user_id' => $this->clienteA->id,
            'trabajador_id' => $this->trabajador->id,
            'service_id' => $this->servicioCorte->id,
            'fecha' => $fechaHoy,
            'hora_inicio' => $horaInminente->format('H:i'),
            'hora_fin' => $horaInminente->copy()->addMinutes(30)->format('H:i'),
            'duracion_minutos' => 30,
            'precio' => 200.00,
            'estado' => 'confirmada',
        ]);

        $this->assertFalse($cita->puedeCancelar());

        $response = $this->actingAs($this->clienteA)->json('POST', "/cliente/citas/{$cita->id}/cancelar");

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
        $this->assertStringContainsString('Solo se permite cancelar con más de 30 minutos de anticipación', $response->json('mensaje'));

        // El estado debe permanecer como 'confirmada'
        $this->assertDatabaseHas('citas', [
            'id' => $cita->id,
            'estado' => 'confirmada',
        ]);
    }

    /**
     * Caso 5: No se pueden cancelar citas que ya pasaron, que ya están canceladas o cuyo estado no lo permita.
     */
    public function test_no_se_pueden_cancelar_citas_pasadas_ya_canceladas_o_completadas(): void
    {
        // 5.1 Cita en el pasado (ayer)
        $citaPasada = Cita::create([
            'user_id' => $this->clienteA->id,
            'trabajador_id' => $this->trabajador->id,
            'service_id' => $this->servicioCorte->id,
            'fecha' => Carbon::now()->subDays(1)->format('Y-m-d'),
            'hora_inicio' => '10:00',
            'hora_fin' => '10:30',
            'duracion_minutos' => 30,
            'precio' => 200.00,
            'estado' => 'confirmada',
        ]);

        $responsePasada = $this->actingAs($this->clienteA)->json('POST', "/cliente/citas/{$citaPasada->id}/cancelar");
        $responsePasada->assertStatus(422);
        $responsePasada->assertJsonFragment(['success' => false]);
        $this->assertStringContainsString('ya ha transcurrido', $responsePasada->json('mensaje'));

        // 5.2 Cita ya cancelada previamente
        $citaCancelada = Cita::create([
            'user_id' => $this->clienteA->id,
            'trabajador_id' => $this->trabajador->id,
            'service_id' => $this->servicioCorte->id,
            'fecha' => Carbon::now()->addDays(2)->format('Y-m-d'),
            'hora_inicio' => '12:00',
            'hora_fin' => '12:30',
            'duracion_minutos' => 30,
            'precio' => 200.00,
            'estado' => 'cancelada',
        ]);

        $responseCancelada = $this->actingAs($this->clienteA)->json('POST', "/cliente/citas/{$citaCancelada->id}/cancelar");
        $responseCancelada->assertStatus(422);
        $responseCancelada->assertJsonFragment([
            'success' => false,
            'mensaje' => 'La cita ya se encuentra cancelada.',
        ]);

        // 5.3 Cita completada
        $citaCompletada = Cita::create([
            'user_id' => $this->clienteA->id,
            'trabajador_id' => $this->trabajador->id,
            'service_id' => $this->servicioCorte->id,
            'fecha' => Carbon::now()->addDays(2)->format('Y-m-d'),
            'hora_inicio' => '14:00',
            'hora_fin' => '14:30',
            'duracion_minutos' => 30,
            'precio' => 200.00,
            'estado' => 'completada',
        ]);

        $responseCompletada = $this->actingAs($this->clienteA)->json('POST', "/cliente/citas/{$citaCompletada->id}/cancelar");
        $responseCompletada->assertStatus(422);
        $responseCompletada->assertJsonFragment([
            'success' => false,
            'mensaje' => 'La cita ya ha sido completada.',
        ]);
    }

    /**
     * Caso 6: La cancelación actualiza el estado en la base de datos sin eliminar el registro físico.
     */
    public function test_cancelacion_actualiza_estado_en_bd_sin_eliminar_el_registro(): void
    {
        $cita = Cita::create([
            'user_id' => $this->clienteA->id,
            'trabajador_id' => $this->trabajador->id,
            'service_id' => $this->servicioCorte->id,
            'fecha' => Carbon::now()->addDays(5)->format('Y-m-d'),
            'hora_inicio' => '09:00',
            'hora_fin' => '09:30',
            'duracion_minutos' => 30,
            'precio' => 200.00,
            'estado' => 'confirmada',
        ]);

        $conteoAntes = Cita::count();

        $response = $this->actingAs($this->clienteA)->json('POST', "/cliente/citas/{$cita->id}/cancelar");
        $response->assertStatus(200);

        // El conteo físico en la tabla de citas no disminuye (conservación íntegra de registros)
        $this->assertEquals($conteoAntes, Cita::count());

        // El registro existe con su mismo ID y sus datos íntegros, pero estado = 'cancelada'
        $this->assertDatabaseHas('citas', [
            'id' => $cita->id,
            'user_id' => $this->clienteA->id,
            'trabajador_id' => $this->trabajador->id,
            'service_id' => $this->servicioCorte->id,
            'precio' => 200.00,
            'estado' => 'cancelada',
        ]);
    }

    /**
     * Caso 7: La pantalla refleja el estado actualizado y muestra los mensajes correspondientes.
     */
    public function test_pantalla_refleja_estado_actualizado_y_mensajes_apropiados(): void
    {
        // Cita cancelada
        $citaCancelada = Cita::create([
            'user_id' => $this->clienteA->id,
            'trabajador_id' => $this->trabajador->id,
            'service_id' => $this->servicioCorte->id,
            'fecha' => Carbon::now()->addDays(2)->format('Y-m-d'),
            'hora_inicio' => '10:00',
            'hora_fin' => '10:30',
            'duracion_minutos' => 30,
            'precio' => 200.00,
            'estado' => 'cancelada',
        ]);

        // Cita próxima activa con más de 30 min
        $citaProximaActiva = Cita::create([
            'user_id' => $this->clienteA->id,
            'trabajador_id' => $this->trabajador->id,
            'service_id' => $this->servicioColor->id,
            'fecha' => Carbon::now()->addDays(4)->format('Y-m-d'),
            'hora_inicio' => '16:00',
            'hora_fin' => '17:00',
            'duracion_minutos' => 60,
            'precio' => 650.00,
            'estado' => 'confirmada',
        ]);

        $response = $this->actingAs($this->clienteA)->get('/cliente/historial');

        $response->assertStatus(200);

        // Refleja badge de 'Cancelada' y texto informativo
        $response->assertSee('status-cancelada', false);
        $response->assertSee('Cita cancelada');

        // Refleja badge de 'Confirmada' y botón activo de cancelar solo en la cita que lo permite
        $response->assertSee('status-confirmada', false);
        $response->assertSee('data-id="' . $citaProximaActiva->id . '"', false);
        $response->assertSee('Cancelar cita');

        // Distingue próximas vs canceladas/pasadas en la navegación/filtros
        $response->assertSee('data-filter="proximas"', false);
        $response->assertSee('data-filter="canceladas"', false);
    }
}
