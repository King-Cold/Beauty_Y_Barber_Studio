<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class LandingHeroTest extends TestCase
{
    /**
     * Criterio 1: Al cargar la página debe apreciarse la imagen de fondo,
     * el nombre del estudio y una frase de bienvenida totalmente legibles.
     */
    public function test_portada_hero_muestra_nombre_estudio_y_frase_bienvenida(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Beauty & Barber Studio');
        $response->assertSee('hero-studio-name');
        $response->assertSee('Bienvenido a');
        $response->assertSee('Arte, Distinción y Cuidado');
        $response->assertSee('hero-section');
    }

    /**
     * Criterio 2 y 3: Debe figurar de manera destacada el botón de llamada a la acción con el texto 'Agendar Cita'
     * y estilos interactivos.
     */
    public function test_boton_agendar_cita_esta_presente_y_destacado(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Agendar Cita');
        $response->assertSee(route('landing.agendar'));
        $response->assertSee('id="btnHeroAgendar"', false);
        $response->assertSee('btn-hero-cta');
    }

    /**
     * Criterio 4: Si el usuario presiona 'Agendar Cita' sin haber iniciado sesión,
     * la página debe dirigirlo al login con mensaje en pantalla indicando que debe identificarse.
     */
    public function test_usuario_no_autenticado_al_agendar_cita_es_redirigido_al_login_con_mensaje(): void
    {
        $response = $this->get(route('landing.agendar'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('warning', 'Por favor, inicia sesión para agendar tu cita.');

        // Seguir la redirección para confirmar que el formulario de login muestra el mensaje en pantalla
        $loginResponse = $this->get(route('login'));
        $loginResponse->assertStatus(200);
        $loginResponse->assertSee('Por favor, inicia sesión para agendar tu cita.');
    }

    /**
     * Criterio 5: Si el usuario ya inició sesión con su cuenta de cliente y hace clic en 'Agendar Cita',
     * debe ser enviado de forma directa al formulario para elegir servicio, trabajador, fecha y horario.
     */
    public function test_cliente_autenticado_al_agendar_cita_es_enviado_directamente_al_formulario_de_citas(): void
    {
        $cliente = new User([
            'id' => 888,
            'name' => 'Carlos Mendoza',
            'email' => 'carlos.mendoza@bbs.com',
            'role_id' => 4, // Cliente
        ]);

        $response = $this->actingAs($cliente)->get(route('landing.agendar'));

        $response->assertRedirect(route('cliente.agendamiento'));

        // Al acceder a la ruta destino, carga el formulario interactivo de citas
        $citasResponse = $this->actingAs($cliente)->get(route('cliente.agendamiento'));
        $citasResponse->assertStatus(200);
        $citasResponse->assertSee('Agendamiento');
    }
}
