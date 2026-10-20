<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\Service;
use App\Models\Trabajador;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Subtarea24_2_ActualizacionPagoYDeudasTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $recepcionistaUser;
    protected User $clienteUser;
    protected Trabajador $trabajador;
    protected Service $servicio;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\TestUsersSeeder::class);

        $this->adminUser = User::where('role_id', 1)->first();
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

    private function crearCitaEnAtencion(array $overrides = []): Cita
    {
        return Cita::create(array_merge([
            'user_id' => $this->clienteUser->id,
            'service_id' => $this->servicio->id,
            'trabajador_id' => $this->trabajador->id,
            'fecha' => Carbon::now()->format('Y-m-d'),
            'hora_inicio' => '10:00:00',
            'hora_fin' => '10:30:00',
            'duracion_minutos' => 30,
            'precio' => 280.00,
            'estado' => 'en_atencion',
            'estado_pago' => 'pendiente',
            'check_in_at' => Carbon::now()->subMinutes(15),
        ], $overrides));
    }

    /**
     * CA-24.2.1: Opción 1 de finalización - Finalizar y Marcar como Pagado.
     */
    public function test_finalizar_cita_y_marcar_como_pagado_actualiza_estado_y_pago_atomicamente(): void
    {
        $cita = $this->crearCitaEnAtencion();

        $response = $this->actingAs($this->recepcionistaUser)
            ->patchJson(route('recepcion.citas.actualizar-estado', $cita->id), [
                'estado' => 'completada',
                'estado_pago' => 'pagado',
                'observaciones' => 'Servicio concluido exitosamente y cobro recibido en recepción.',
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $citaActualizada = $cita->fresh();
        $this->assertEquals('completada', $citaActualizada->estado);
        $this->assertEquals('pagado', $citaActualizada->estado_pago);
        $this->assertTrue($citaActualizada->esta_pagada);
        $this->assertFalse($citaActualizada->puedeMarcarComoPagado());
        $this->assertStringContainsString('Servicio concluido', $citaActualizada->observaciones);
    }

    /**
     * CA-24.2.2: Opción 2 de finalización - Finalizar y Dejar Pendiente (mantiene deuda).
     */
    public function test_finalizar_cita_y_dejar_pendiente_actualiza_a_completada_y_mantiene_deuda(): void
    {
        $cita = $this->crearCitaEnAtencion();

        $response = $this->actingAs($this->recepcionistaUser)
            ->patchJson(route('recepcion.citas.actualizar-estado', $cita->id), [
                'estado' => 'completada',
                'estado_pago' => 'pendiente',
                'observaciones' => 'Servicio terminado, cliente pagará por transferencia más tarde.',
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $citaActualizada = $cita->fresh();
        $this->assertEquals('completada', $citaActualizada->estado);
        $this->assertEquals('pendiente', $citaActualizada->estado_pago);
        $this->assertFalse($citaActualizada->esta_pagada);
        // Debe poder marcarse como pagado posteriormente
        $this->assertTrue($citaActualizada->puedeMarcarComoPagado());
    }

    /**
     * CA-24.2.3: Cobro posterior de cita completada con pago pendiente.
     */
    public function test_marcar_como_pagado_cita_completada_posteriormente(): void
    {
        $cita = $this->crearCitaEnAtencion([
            'estado' => 'completada',
            'estado_pago' => 'pendiente',
        ]);

        $response = $this->actingAs($this->recepcionistaUser)
            ->patchJson(route('recepcion.citas.pagar', $cita->id));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'already_paid' => false,
        ]);

        $citaActualizada = $cita->fresh();
        $this->assertEquals('pagado', $citaActualizada->estado_pago);
        $this->assertTrue($citaActualizada->esta_pagada);
        $this->assertFalse($citaActualizada->puedeMarcarComoPagado());
    }

    /**
     * CA-24.2.4: El Administrador tiene acceso estrictamente de solo lectura y no puede cobrar (403).
     */
    public function test_administrador_tiene_acceso_estrictamente_solo_lectura_y_no_puede_cobrar(): void
    {
        $cita = $this->crearCitaEnAtencion([
            'estado' => 'completada',
            'estado_pago' => 'pendiente',
        ]);

        // Intento de cobro directo mediante endpoint de pago
        $response = $this->actingAs($this->adminUser)
            ->patchJson(route('recepcion.citas.pagar', $cita->id));

        $response->assertStatus(403);

        // Intento de manipulación de estado_pago mediante actualizar-estado
        $responseEstado = $this->actingAs($this->adminUser)
            ->patchJson(route('recepcion.citas.actualizar-estado', $cita->id), [
                'estado' => 'completada',
                'estado_pago' => 'pagado',
            ]);

        $responseEstado->assertStatus(403);

        // El estado en BD no fue alterado
        $this->assertEquals('pendiente', $cita->fresh()->estado_pago);
    }

    /**
     * CA-24.2.5: No se puede registrar el pago de una cita cancelada (422).
     */
    public function test_no_se_puede_registrar_pago_de_una_cita_cancelada(): void
    {
        $cita = $this->crearCitaEnAtencion([
            'estado' => 'cancelada',
            'estado_pago' => 'pendiente',
        ]);

        $response = $this->actingAs($this->recepcionistaUser)
            ->patchJson(route('recepcion.citas.pagar', $cita->id));

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'error' => 'No se puede registrar el pago de una cita cancelada.',
        ]);

        $this->assertEquals('pendiente', $cita->fresh()->estado_pago);
    }

    /**
     * CA-24.2.5b: No se puede registrar el pago de una cita vencida (422) ni mostrar botón de pago.
     */
    public function test_no_se_puede_registrar_pago_de_una_cita_vencida(): void
    {
        $citaVencida = $this->crearCitaEnAtencion([
            'estado' => 'vencida',
            'estado_pago' => 'pendiente',
        ]);

        $this->assertFalse($citaVencida->puedeMarcarComoPagado());

        $response = $this->actingAs($this->recepcionistaUser)
            ->patchJson(route('recepcion.citas.pagar', $citaVencida->id));

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'error' => 'No se puede registrar el pago de una cita vencida.',
        ]);

        $this->assertEquals('pendiente', $citaVencida->fresh()->estado_pago);
    }

    /**
     * CA-24.2.6: Prevención de pagos duplicados (idempotencia y bloqueo).
     */
    public function test_pago_es_idempotente_y_previene_pagos_duplicados(): void
    {
        $cita = $this->crearCitaEnAtencion([
            'estado' => 'completada',
            'estado_pago' => 'pagado',
        ]);

        $response = $this->actingAs($this->recepcionistaUser)
            ->patchJson(route('recepcion.citas.pagar', $cita->id));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'already_paid' => true,
        ]);

        $this->assertEquals('pagado', $cita->fresh()->estado_pago);
    }

    /**
     * CA-24.2.7: Detalle de cita para modal expone flags y datos para habilitar/deshabilitar botón.
     */
    public function test_detalle_de_cita_expone_flags_correctos_para_modal(): void
    {
        $citaPendiente = $this->crearCitaEnAtencion([
            'estado' => 'completada',
            'estado_pago' => 'pendiente',
        ]);

        $response = $this->actingAs($this->recepcionistaUser)
            ->getJson(route('recepcion.citas.detalle', $citaPendiente->id));

        $response->assertStatus(200);
        $response->assertJsonPath('cita.estado_pago', 'pendiente');
        $response->assertJsonPath('cita.esta_pagada', false);
        $response->assertJsonPath('cita.puede_marcar_como_pagado', true);

        // Al cambiar a pagada
        $citaPendiente->update(['estado_pago' => 'pagado']);

        $responsePagada = $this->actingAs($this->recepcionistaUser)
            ->getJson(route('recepcion.citas.detalle', $citaPendiente->id));

        $responsePagada->assertStatus(200);
        $responsePagada->assertJsonPath('cita.estado_pago', 'pagado');
        $responsePagada->assertJsonPath('cita.esta_pagada', true);
        $responsePagada->assertJsonPath('cita.puede_marcar_como_pagado', false);

        // Caso cita vencida: tampoco debe permitir marcarse como pagada
        $citaVencida = $this->crearCitaEnAtencion([
            'estado' => 'vencida',
            'estado_pago' => 'pendiente',
        ]);
        $responseVencida = $this->actingAs($this->recepcionistaUser)
            ->getJson(route('recepcion.citas.detalle', $citaVencida->id));
        $responseVencida->assertStatus(200);
        $responseVencida->assertJsonPath('cita.puede_marcar_como_pagado', false);
    }

    /**
     * CA-24.2.4 (Usuario): Actualización reactiva en vista del Cliente a "✓ Pagado".
     */
    public function test_actualizacion_reactiva_en_vista_del_cliente_muestra_comprobante_pagado(): void
    {
        $cita = $this->crearCitaEnAtencion([
            'estado' => 'completada',
            'estado_pago' => 'pendiente',
        ]);

        // Verificación inicial del cliente: Ve 'Pago en recepción: Pendiente'
        $responseHistorialInicial = $this->actingAs($this->clienteUser)
            ->get(route('cliente.historial'));
        $responseHistorialInicial->assertStatus(200);
        $responseHistorialInicial->assertSee('Pago en recepción: Pendiente');

        // Recepción cobra la cita
        $this->actingAs($this->recepcionistaUser)
            ->patchJson(route('recepcion.citas.pagar', $cita->id));

        // Verificación posterior del cliente: Ve '✓ Pagado'
        $responseHistorialFinal = $this->actingAs($this->clienteUser)
            ->get(route('cliente.historial'));
        $responseHistorialFinal->assertStatus(200);
        $responseHistorialFinal->assertSee('✓ Pagado');
    }

    /**
     * Citas Canceladas: Son registros terminales de auditoría.
     * No se pueden modificar/reagendar vía backend y no muestran botón de Reagendar en la vista.
     */
    public function test_cita_cancelada_no_se_puede_reagendar_ni_editar(): void
    {
        $citaCancelada = $this->crearCitaEnAtencion([
            'estado' => 'cancelada',
        ]);

        // Intentar actualizar/reagendar vía endpoint PUT/PATCH de recepción
        $response = $this->actingAs($this->recepcionistaUser)
            ->putJson(route('recepcion.citas.update', $citaCancelada->id), [
                'service_id' => $this->servicio->id,
                'trabajador_id' => $this->trabajador->id,
                'fecha' => now()->addDays(2)->format('Y-m-d'),
                'hora_inicio' => '10:00',
            ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'error' => 'Una cita cancelada no puede ser modificada ni reagendada.',
        ]);

        $this->assertEquals('cancelada', $citaCancelada->fresh()->estado);
    }
}

