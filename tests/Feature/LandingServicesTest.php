<?php

namespace Tests\Feature;

use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingServicesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Criterio 1: Cada servicio activo debe presentarse en una tarjeta individual que contenga:
     * - Fotografía ilustrativa del servicio
     * - Nombre oficial del servicio
     * - Descripción breve del procedimiento o corte
     * - Precio claramente visible con signo de pesos (ejemplo: $250.00 MXN)
     * - Duración estimada expresada en minutos (ejemplo: 45 min)
     */
    public function test_servicios_activos_se_muestran_con_tarjeta_individual_foto_nombre_descripcion_precio_y_duracion(): void
    {
        // Creamos un servicio activo de prueba
        $service = Service::create([
            'name' => 'Corte Diamante VIP',
            'description' => 'Servicio exclusivo con lavado capilar y masaje relajante craneal.',
            'price' => 380.00,
            'duration_minutes' => 50,
            'category' => 'barberia',
            'is_active' => true,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Corte Diamante VIP');
        $response->assertSee('Servicio exclusivo con lavado capilar y masaje relajante craneal.');
        $response->assertSee('$380.00');
        $response->assertSee('MXN');
        $response->assertSee('50 min');
        $response->assertSee('serviceCard' . $service->id);
    }

    /**
     * Criterio 2: Comprobación en base de datos (Servicio inactivo):
     * Si en la base de datos se cambia un servicio a inactivo (is_active = 0) y se recarga la página,
     * dicho servicio debe desaparecer de inmediato de la pantalla.
     */
    public function test_servicio_inactivo_desaparece_de_inmediato_de_la_pantalla(): void
    {
        $inactiveService = Service::create([
            'name' => 'Servicio Descontinuado Inactivo',
            'description' => 'Este servicio no debe ser visible para los clientes.',
            'price' => 150.00,
            'duration_minutes' => 30,
            'category' => 'barberia',
            'is_active' => false,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('Servicio Descontinuado Inactivo');
        $response->assertDontSee('serviceCard' . $inactiveService->id);
    }

    /**
     * Criterio 3: Comprobación en base de datos (Servicio activo):
     * Si el servicio tiene su estado activo (is_active = 1), debe aparecer listado en la pantalla al recargar.
     */
    public function test_servicio_activo_aparece_listado_en_la_pantalla(): void
    {
        $activeService = Service::create([
            'name' => 'Tratamiento Oro 24K',
            'description' => 'Tratamiento reparador intensivo con brillo instantáneo.',
            'price' => 550.00,
            'duration_minutes' => 60,
            'category' => 'estetica',
            'is_active' => true,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Tratamiento Oro 24K');
        $response->assertSee('serviceCard' . $activeService->id);
    }

    /**
     * Criterio 4: En caso de que un servicio activo no tenga fotografía registrada en la base de datos,
     * debe mostrarse una imagen institucional o logotipo por defecto, sin cuadros de imagen rota en el navegador.
     */
    public function test_servicio_sin_foto_registrada_cuenta_con_imagen_o_fallback_institucional(): void
    {
        $serviceWithoutPhoto = Service::create([
            'name' => 'Afeitado Imperial Sin Foto',
            'description' => 'Afeitado tradicional con navaja japonesa.',
            'price' => 290.00,
            'duration_minutes' => 45,
            'category' => 'barberia',
            'image_path' => null,
            'is_active' => true,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('serviceCard' . $serviceWithoutPhoto->id);
        // Debe contener el manejador onerror para garantizar que nunca muestre una imagen rota
        $response->assertSee('onerror="this.onerror=null;', false);
        $response->assertSee("this.src='" . asset('images/logo.png') . "'", false);
    }

    /**
     * Criterio 5 y 6: Deben mostrarse botones de filtro para seleccionar categorías
     * (por ejemplo: 'Todos', 'Barbería', 'Estilismo y Estética').
     */
    public function test_botones_de_filtro_por_categoria_se_renderizan_correctamente(): void
    {
        Service::create([
            'name' => 'Corte Clásico Test',
            'description' => 'Descripción prueba.',
            'price' => 200,
            'duration_minutes' => 30,
            'category' => 'barberia',
            'is_active' => true,
        ]);
        Service::create([
            'name' => 'Peinado Test',
            'description' => 'Descripción prueba.',
            'price' => 300,
            'duration_minutes' => 40,
            'category' => 'estetica',
            'is_active' => true,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('id="servicesFilterBar"', false);
        $response->assertSee('id="filterBtnAll"', false);
        $response->assertSee('data-filter="all"', false);
        $response->assertSee('data-filter="barberia"', false);
        $response->assertSee('data-filter="estetica"', false);
    }

    /**
     * Criterio 7: Si no existen servicios activos en la base de datos,
     * la pantalla debe mostrar un mensaje visible y amigable que indique que pronto se publicarán servicios,
     * sin dejar huecos en blanco rotos.
     */
    public function test_muestra_mensaje_amigable_de_estado_vacio_si_no_hay_servicios(): void
    {
        // Desactivamos temporalmente todos los servicios en la transacción
        Service::query()->update(['is_active' => false]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('id="servicesEmptyState"', false);
        $response->assertSee('Próximamente Nuevos Servicios');
        $response->assertSee('actualizando nuestro catálogo');
    }
}
