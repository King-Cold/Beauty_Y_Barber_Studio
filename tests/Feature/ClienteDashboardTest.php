<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class ClienteDashboardTest extends TestCase
{
    /**
     * Comprueba que usuarios sin sesión son redirigidos al login.
     */
    public function test_usuario_no_autenticado_es_redirigido_al_login(): void
    {
        $response = $this->get('/cliente');
        $response->assertRedirect(route('login'));

        $responseAgendamiento = $this->get('/cliente/agendamiento');
        $responseAgendamiento->assertRedirect(route('login'));

        $responseHistorial = $this->get('/cliente/historial');
        $responseHistorial->assertRedirect(route('login'));
    }

    /**
     * Comprueba que un cliente autenticado puede ver el panel principal con su información y navegación.
     */
    public function test_cliente_autenticado_puede_ver_el_panel_principal(): void
    {
        $user = new User([
            'id' => 999,
            'name' => 'Roberto',
            'apellidos' => 'Sánchez',
            'email' => 'roberto.cliente@test.com',
            'telefono' => '5556543210',
            'role_id' => 4,
        ]);

        $response = $this->actingAs($user)->get('/cliente');

        $response->assertStatus(200);
        $response->assertSee('¡Hola, Roberto!');
        $response->assertSee('Portal Cliente');
        $response->assertSee('Inicio');
        $response->assertSee('Agendamiento');
        $response->assertSee('Historial de citas');
        $response->assertSee('Cerrar sesión');
        $response->assertSee('roberto.cliente@test.com');
    }

    /**
     * Comprueba que el cliente puede acceder visualmente al apartado de agendamiento.
     */
    public function test_cliente_puede_acceder_al_apartado_de_agendamiento(): void
    {
        $user = new User([
            'id' => 999,
            'name' => 'Roberto',
            'apellidos' => 'Sánchez',
            'email' => 'roberto.cliente@test.com',
            'telefono' => '5556543210',
            'role_id' => 4,
        ]);

        $response = $this->actingAs($user)->get('/cliente/agendamiento');

        $response->assertStatus(200);
        $response->assertSee('Agendamiento');
        $response->assertSee('Catálogo de Servicios');
        $response->assertSee('Barbería');
        $response->assertSee('Estética');
        $response->assertSee('Selecciona un servicio');
        $response->assertSee('Selecciona un trabajador');
        $response->assertSee('Fecha de la cita');
        $response->assertSee('Horario de atención');
        $response->assertSee('Resumen del Servicio');
        $response->assertSee('Continuar');
        $response->assertSee('Regresar');
        $response->assertSee('Inicio');
        $response->assertSee('Historial de citas');
    }

    /**
     * Comprueba que el catálogo organiza los servicios en categorías Barbería y Estética con sus tarjetas.
     */
    public function test_catalogo_de_servicios_distingue_categorias_barberia_y_estetica(): void
    {
        $user = new User([
            'id' => 999,
            'name' => 'Roberto',
            'email' => 'roberto.cliente@test.com',
            'role_id' => 4,
        ]);

        $response = $this->actingAs($user)->get('/cliente/agendamiento');

        $response->assertStatus(200);
        $response->assertSee('Catálogo de Servicios');
        $response->assertSee('Barbería');
        $response->assertSee('Estética');
        $response->assertSee('id="categoryBlockBarberia"', false);
        $response->assertSee('id="categoryBlockEstetica"', false);
    }

    /**
     * Comprueba que el cliente puede acceder visualmente al apartado de historial de citas.
     */
    public function test_cliente_puede_acceder_al_apartado_de_historial_de_citas(): void
    {
        $user = new User([
            'id' => 999,
            'name' => 'Roberto',
            'apellidos' => 'Sánchez',
            'email' => 'roberto.cliente@test.com',
            'telefono' => '5556543210',
            'role_id' => 4,
        ]);

        $response = $this->actingAs($user)->get('/cliente/historial');

        $response->assertStatus(200);
        $response->assertSee('Historial de citas');
        $response->assertSee('Inicio');
        $response->assertSee('Agendamiento');
    }

    /**
     * Comprueba que usuarios con rol trabajador o recepcionista son redirigidos a su respectivo panel.
     */
    public function test_otros_roles_no_pueden_acceder_al_panel_de_cliente(): void
    {
        $trabajador = new User([
            'id' => 1001,
            'name' => 'Alejandro',
            'role_id' => 3,
        ]);

        $responseTrabajador = $this->actingAs($trabajador)->get('/cliente');
        $responseTrabajador->assertStatus(403);

        $recepcionista = new User([
            'id' => 1002,
            'name' => 'Laura',
            'role_id' => 2,
        ]);

        $responseRecepcionista = $this->actingAs($recepcionista)->get('/cliente');
        $responseRecepcionista->assertStatus(403);
    }
}
