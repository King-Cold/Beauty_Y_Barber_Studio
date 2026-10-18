<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Service;
use App\Models\Trabajador;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class RecepcionServicioSelectorTest extends TestCase
{
    use RefreshDatabase;

    protected User $recepcionistaUser;
    protected Service $servicioBarberia;
    protected Service $servicioEstetica;

    protected function setUp(): void
    {
        parent::setUp();

        if (Role::count() === 0) {
            Role::create(['id' => 1, 'nombre' => 'Administrador']);
            Role::create(['id' => 2, 'nombre' => 'Recepcionista']);
            Role::create(['id' => 3, 'nombre' => 'Trabajador']);
            Role::create(['id' => 4, 'nombre' => 'Cliente']);
        }

        $this->recepcionistaUser = User::create([
            'name' => 'Laura',
            'apellidos' => 'García',
            'email' => 'laura.recepcion@bbs.com',
            'telefono' => '5551234568',
            'password' => Hash::make('Secret123!'),
            'role_id' => 2,
        ]);

        $this->servicioBarberia = Service::create([
            'name' => 'Corte Clásico Barber',
            'price' => 200.00,
            'duration_minutes' => 30,
            'category' => 'barberia',
            'is_active' => true,
        ]);

        $this->servicioEstetica = Service::create([
            'name' => 'Tinte y Peinado Spa',
            'price' => 450.00,
            'duration_minutes' => 60,
            'category' => 'estetica',
            'is_active' => true,
        ]);
    }

    /**
     * Escenario 1 y 2: Los botones anteriores ya no aparecen y el selector desplegable está visible con las opciones requeridas.
     */
    public function test_selector_desplegable_reemplaza_botones_con_opciones_requeridas(): void
    {
        $response = $this->actingAs($this->recepcionistaUser)->get(route('recepcion.dashboard'));

        $response->assertStatus(200);

        // 1. Los botones anteriores ya no aparecen
        $response->assertDontSee('service-filter-btn');

        // 2. El selector desplegable existe con su id y clase
        $response->assertSee('id="serviceCategoryFilter"', false);
        $response->assertSee('class="service-category-select"', false);

        // 3. El selector contiene exactamente las opciones: Todos, Barbería y Estética
        $response->assertSee('<option value="all">Todos</option>', false);
        $response->assertSee('<option value="barberia">Barbería</option>', false);
        $response->assertSee('<option value="estetica">Estética</option>', false);

        // 4. Los servicios tienen sus atributos de categoría para el filtrado en frontend
        $response->assertSee('data-service-category="barberia"', false);
        $response->assertSee('data-service-category="estetica"', false);
        $response->assertSee('Corte Clásico Barber');
        $response->assertSee('Tinte y Peinado Spa');
    }

    /**
     * Escenario 7 y 8: El proceso de recepción y agendamiento conserva compatibilidad completa.
     */
    public function test_panel_recepcion_conserva_componentes_y_procesos(): void
    {
        $response = $this->actingAs($this->recepcionistaUser)->get(route('recepcion.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Servicio Requerido');
        $response->assertSee('servicesGrid');
        $response->assertSee('summaryCardSection');
        $response->assertSee('btnConfirmTurno');
    }
}
