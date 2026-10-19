<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\Service;
use App\Models\Trabajador;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Subtarea24_1_RegistroPrecioYEstadoPagoTest extends TestCase
{
    use RefreshDatabase;

    protected User $recepcionistaUser;
    protected User $clienteUser;
    protected Trabajador $trabajador;
    protected Service $servicio;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\TestUsersSeeder::class);

        $this->recepcionistaUser = User::where('role_id', 2)->first();
        $this->clienteUser = User::where('role_id', 4)->first();
        $trabajadorUser = User::where('role_id', 3)->first();

        $this->trabajador = Trabajador::create([
            'user_id' => $trabajadorUser->id,
            'direccion' => 'Calle Estudio 123',
            'experiencia' => 5,
            'activo' => true,
            'horario' => [
                'lunes' => ['entrada' => '09:00', 'salida' => '19:00'],
                'martes' => ['entrada' => '09:00', 'salida' => '19:00'],
                'miercoles' => ['entrada' => '09:00', 'salida' => '19:00'],
                'jueves' => ['entrada' => '09:00', 'salida' => '19:00'],
                'viernes' => ['entrada' => '09:00', 'salida' => '19:00'],
                'sabado' => ['entrada' => '09:00', 'salida' => '15:00'],
            ],
        ]);

        $this->servicio = Service::create([
            'name' => 'Corte Caballero Test',
            'duration_minutes' => 30,
            'price' => 280.00,
            'category' => 'barberia',
            'is_active' => true,
        ]);

        $this->trabajador->servicios()->attach($this->servicio->id);
    }

    /**
     * CA-24.1.1: Guarda el precio vigente oficial del servicio al crear la cita, ignorando valores enviados.
     */
    public function test_creacion_de_cita_guarda_precio_vigente_del_servicio_desde_backend(): void
    {
        $fecha = Carbon::now()->next(Carbon::MONDAY)->format('Y-m-d');

        $payload = [
            'client_id' => $this->clienteUser->id,
            'service_id' => $this->servicio->id,
            'worker_id' => $this->trabajador->id,
            'date' => $fecha,
            'time' => '10:00',
            'notas' => 'Cita de prueba',
            'precio' => 10.00, // Intento malicioso de inyectar precio menor
        ];

        $response = $this->actingAs($this->recepcionistaUser)
            ->postJson(route('recepcion.agendamiento.confirmar'), $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $cita = Cita::where('user_id', $this->clienteUser->id)->latest('id')->first();
        $this->assertNotNull($cita);
        $this->assertEquals(280.00, (float) $cita->precio);
        $this->assertEquals('pendiente', $cita->estado_pago);
    }

    /**
     * CA-24.1.2 & CA-24.1.3: Cambios posteriores en el catálogo de servicios NO alteran el precio histórico guardado.
     */
    public function test_cambio_posterior_en_catalogo_no_modifica_precio_historico_de_citas_existentes(): void
    {
        $fecha = Carbon::now()->next(Carbon::TUESDAY)->format('Y-m-d');

        $cita = Cita::create([
            'user_id' => $this->clienteUser->id,
            'trabajador_id' => $this->trabajador->id,
            'service_id' => $this->servicio->id,
            'fecha' => $fecha,
            'hora_inicio' => '11:00',
            'hora_fin' => '11:30',
            'duracion_minutos' => 30,
            'precio' => 280.00,
            'estado' => 'confirmada',
            'estado_pago' => 'pendiente',
        ]);

        // Aumentar precio en el catálogo a 450.00
        $this->servicio->update(['price' => 450.00]);

        $citaFresca = $cita->fresh();
        $this->assertEquals(280.00, (float) $citaFresca->precio);
        $this->assertEquals(450.00, (float) $this->servicio->fresh()->price);
    }

    /**
     * CA-24.1.4: Estado de pago se inicializa como 'pendiente' por defecto en el modelo.
     */
    public function test_estado_pago_se_inicializa_como_pendiente(): void
    {
        $fecha = Carbon::now()->next(Carbon::WEDNESDAY)->format('Y-m-d');

        $cita = Cita::create([
            'user_id' => $this->clienteUser->id,
            'trabajador_id' => $this->trabajador->id,
            'service_id' => $this->servicio->id,
            'fecha' => $fecha,
            'hora_inicio' => '12:00',
            'hora_fin' => '12:30',
            'duracion_minutos' => 30,
            'precio' => 280.00,
            'estado' => 'confirmada',
        ]);

        $this->assertEquals('pendiente', $cita->estado_pago);
        $this->assertEquals('Pendiente', $cita->estado_pago_label);
        $this->assertFalse($cita->esta_pagada);
    }

    /**
     * CA-24.1.5: La vista de recepción muestra el precio formateado y la etiqueta de pago.
     */
    public function test_vista_recepcion_muestra_precio_y_etiqueta_estado_pago(): void
    {
        $fecha = Carbon::now()->format('Y-m-d');

        // Cita 1: pendiente
        $citaPendiente = Cita::create([
            'user_id' => $this->clienteUser->id,
            'trabajador_id' => $this->trabajador->id,
            'service_id' => $this->servicio->id,
            'fecha' => $fecha,
            'hora_inicio' => '09:00',
            'hora_fin' => '09:30',
            'duracion_minutos' => 30,
            'precio' => 280.00,
            'estado' => 'confirmada',
            'estado_pago' => 'pendiente',
        ]);

        // Cita 2: pagada
        $citaPagada = Cita::create([
            'user_id' => $this->clienteUser->id,
            'trabajador_id' => $this->trabajador->id,
            'service_id' => $this->servicio->id,
            'fecha' => $fecha,
            'hora_inicio' => '10:00',
            'hora_fin' => '10:30',
            'duracion_minutos' => 30,
            'precio' => 350.00,
            'estado' => 'confirmada',
            'estado_pago' => 'pagado',
        ]);

        $response = $this->actingAs($this->recepcionistaUser)
            ->get(route('recepcion.citas'));

        $response->assertStatus(200);
        $response->assertSee('$280.00');
        $response->assertSee('$350.00');
        $response->assertSee('pago-badge pendiente', false);
        $response->assertSee('pago-badge pagado', false);
        $response->assertSee('Pendiente');
        $response->assertSee('Pagado');
    }

    /**
     * CA-24.1.6: La vista del historial del cliente muestra el precio y la etiqueta informativa de pago.
     */
    public function test_vista_historial_cliente_muestra_precio_y_etiqueta_informativa_de_pago(): void
    {
        $fecha = Carbon::now()->addDays(2)->format('Y-m-d');

        // Cita pendiente
        Cita::create([
            'user_id' => $this->clienteUser->id,
            'trabajador_id' => $this->trabajador->id,
            'service_id' => $this->servicio->id,
            'fecha' => $fecha,
            'hora_inicio' => '14:00',
            'hora_fin' => '14:30',
            'duracion_minutos' => 30,
            'precio' => 280.00,
            'estado' => 'confirmada',
            'estado_pago' => 'pendiente',
        ]);

        $response = $this->actingAs($this->clienteUser)
            ->get(route('cliente.historial'));

        $response->assertStatus(200);
        $response->assertSee('$280.00');
        $response->assertSee('Pago en recepción: Pendiente');

        // Modificar a pagada y verificar etiqueta
        $cita = Cita::where('user_id', $this->clienteUser->id)->latest('id')->first();
        $cita->update(['estado_pago' => 'pagado']);

        $responsePagada = $this->actingAs($this->clienteUser)
            ->get(route('cliente.historial'));

        $responsePagada->assertStatus(200);
        $responsePagada->assertSee('✓ Pagado');
    }
}
