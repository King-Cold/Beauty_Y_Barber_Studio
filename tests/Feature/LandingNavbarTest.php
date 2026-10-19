<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class LandingNavbarTest extends TestCase
{
    /**
     * Criterio 1: La ruta raíz (GET /) carga exitosamente la Landing Page pública.
     */
    public function test_landing_page_carga_exitosamente_en_ruta_raiz(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Beauty');
        $response->assertSee('Barber');
        $response->assertSee('Navegación principal');
    }

    /**
     * Criterio 1 y 2: Botón 'Iniciar Sesión' está visible para visitantes y apunta a /login.
     */
    public function test_boton_iniciar_sesion_apunta_a_formulario_de_login(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee(route('login'));
        $response->assertSee('Iniciar Sesión');

        // Al visitar la ruta del botón, muestra el formulario de login
        $loginResponse = $this->get(route('login'));
        $loginResponse->assertStatus(200);
        $loginResponse->assertSee('¡Bienvenido de nuevo!');
        $loginResponse->assertSee('name="email"', false);
        $loginResponse->assertSee('name="password"', false);
    }

    /**
     * Criterio 1 y 3: Botón 'Crear Cuenta' está visible para visitantes y apunta a /registro.
     */
    public function test_boton_crear_cuenta_apunta_a_formulario_de_registro(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee(route('register'));
        $response->assertSee('Crear Cuenta');

        // Al visitar la ruta del botón, muestra el formulario de registro
        $registerResponse = $this->get(route('register'));
        $registerResponse->assertStatus(200);
        $registerResponse->assertSee('Registro de Cliente');
    }

    /**
     * Criterio 4: Si el usuario ya inició sesión, los botones de 'Iniciar Sesión' y 'Crear Cuenta'
     * siguen estando visibles y operativos en la barra superior.
     */
    public function test_botones_iniciar_sesion_y_crear_cuenta_permanecen_visibles_con_sesion_activa(): void
    {
        $user = new User([
            'id' => 999,
            'name' => 'Cliente Test',
            'email' => 'cliente.test@bbs.com',
            'role_id' => 4,
        ]);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertSee(route('login'));
        $response->assertSee('Iniciar Sesión');
        $response->assertSee(route('register'));
        $response->assertSee('Crear Cuenta');
    }

    /**
     * Criterio 5: Los enlaces de navegación del menú apuntan a las secciones internas para scroll suave.
     */
    public function test_enlaces_de_navegacion_apuntan_a_las_secciones_correspondientes(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('href="#inicio"', false);
        $response->assertSee('href="#servicios"', false);
        $response->assertSee('href="#equipo"', false);
        $response->assertSee('href="#horarios"', false);
        $response->assertSee('href="#ubicacion"', false);

        // Validar que las secciones receptoras existen en el DOM
        $response->assertSee('id="inicio"', false);
        $response->assertSee('id="servicios"', false);
        $response->assertSee('id="equipo"', false);
        $response->assertSee('id="horarios"', false);
        $response->assertSee('id="ubicacion"', false);
    }

    /**
     * Criterio 6, 7 y 8: Elementos de menú hamburguesa, botón de cierre y cajón móvil presentes.
     */
    public function test_elementos_de_menu_movil_y_boton_hamburguesa_estan_presentes(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('id="hamburgerBtn"', false);
        $response->assertSee('id="mobileDrawer"', false);
        $response->assertSee('id="closeDrawerBtn"', false);
        $response->assertSee('id="mobileDrawerBackdrop"', false);
        $response->assertSee('id="drawerBtnLogin"', false);
        $response->assertSee('id="drawerBtnRegister"', false);
    }
}
