<?php

namespace Tests\Feature;

use App\Models\Trabajador;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrabajadorConsultaTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\TestUsersSeeder::class);
        $this->adminUser = User::where('role_id', 1)->first();
    }

    /**
     * Comprueba que un administrador puede ver el listado de trabajadores con datos reales de la BD.
     */
    public function test_un_administrador_autenticado_puede_consultar_el_listado_de_trabajadores(): void
    {
        $trabajador1 = Trabajador::create([
            'nombre' => 'Hugo',
            'apellidos' => 'Sánchez Morales',
            'telefono' => '5551112233',
            'email' => 'hugo.barber@bbs.com',
            'direccion' => 'Calle Amsterdam #22, Hipódromo',
            'experiencia' => 7,
            'activo' => true,
        ]);

        $trabajador2 = Trabajador::create([
            'nombre' => 'Valeria',
            'apellidos' => 'Ríos Vega',
            'telefono' => '5554445566',
            'email' => 'valeria.estilo@bbs.com',
            'direccion' => 'Av. Mazatlán #85, Condesa',
            'experiencia' => 4,
            'activo' => true,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.trabajadores.index'));

        $response->assertStatus(200);
        $response->assertSee('Hugo Sánchez Morales');
        $response->assertSee('5551112233');
        $response->assertSee('hugo.barber@bbs.com');
        $response->assertSee('Valeria Ríos Vega');
        $response->assertSee('5554445566');
        $response->assertSee('valeria.estilo@bbs.com');
    }

    /**
     * Comprueba que la visualización contempla estados activos e inactivos y métricas correctas.
     */
    public function test_el_listado_muestra_estados_activos_e_inactivos(): void
    {
        Trabajador::create([
            'nombre' => 'Activo',
            'apellidos' => 'Especialista',
            'telefono' => '5551002000',
            'email' => 'activo@bbs.com',
            'direccion' => 'Calle 10 #20',
            'experiencia' => 3,
            'activo' => true,
        ]);

        Trabajador::create([
            'nombre' => 'Inactivo',
            'apellidos' => 'Especialista',
            'telefono' => '5553004000',
            'email' => 'inactivo@bbs.com',
            'direccion' => 'Calle 30 #40',
            'experiencia' => 5,
            'activo' => false,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.trabajadores.index'));

        $response->assertStatus(200);
        $response->assertSee('Activo');
        $response->assertSee('Inactivo');
    }

    /**
     * Comprueba el filtrado en backend por estado de actividad.
     */
    public function test_se_pueden_consultar_trabajadores_filtrados_por_estado(): void
    {
        Trabajador::create([
            'nombre' => 'Solo',
            'apellidos' => 'Activo',
            'telefono' => '5551110001',
            'email' => 'solo.activo@bbs.com',
            'direccion' => 'Calle A #1',
            'experiencia' => 2,
            'activo' => true,
        ]);

        Trabajador::create([
            'nombre' => 'Solo',
            'apellidos' => 'Inactivo',
            'telefono' => '5551110002',
            'email' => 'solo.inactivo@bbs.com',
            'direccion' => 'Calle B #2',
            'experiencia' => 4,
            'activo' => false,
        ]);

        // Filtrar activos
        $responseActivos = $this->actingAs($this->adminUser)
            ->getJson(route('admin.trabajadores.index', ['estado' => 'activos']));

        $responseActivos->assertStatus(200);
        $responseActivos->assertJsonCount(1, 'data');
        $responseActivos->assertJsonFragment(['email' => 'solo.activo@bbs.com']);
        $responseActivos->assertJsonMissing(['email' => 'solo.inactivo@bbs.com']);

        // Filtrar inactivos
        $responseInactivos = $this->actingAs($this->adminUser)
            ->getJson(route('admin.trabajadores.index', ['estado' => 'inactivos']));

        $responseInactivos->assertStatus(200);
        $responseInactivos->assertJsonCount(1, 'data');
        $responseInactivos->assertJsonFragment(['email' => 'solo.inactivo@bbs.com']);
        $responseInactivos->assertJsonMissing(['email' => 'solo.activo@bbs.com']);
    }

    /**
     * Comprueba la búsqueda de trabajadores exclusivamente por nombre y correo electrónico.
     */
    public function test_se_pueden_buscar_trabajadores_por_termino_de_busqueda(): void
    {
        Trabajador::create([
            'nombre' => 'Rodrigo',
            'apellidos' => 'Palacios',
            'telefono' => '5559998877',
            'email' => 'rodrigo.fade@bbs.com',
            'direccion' => 'Av. Coyoacán #500',
            'experiencia' => 6,
            'activo' => true,
        ]);

        Trabajador::create([
            'nombre' => 'Carla',
            'apellidos' => 'Mendoza',
            'telefono' => '5556667788',
            'email' => 'carla.estilo@bbs.com',
            'direccion' => 'Av. Cuauhtémoc #120',
            'experiencia' => 3,
            'activo' => true,
        ]);

        // Búsqueda por nombre
        $responseNombre = $this->actingAs($this->adminUser)
            ->getJson(route('admin.trabajadores.index', ['buscar' => 'Rodrigo']));
        $responseNombre->assertStatus(200);
        $responseNombre->assertJsonCount(1, 'data');
        $responseNombre->assertJsonFragment(['email' => 'rodrigo.fade@bbs.com']);
        $responseNombre->assertJsonMissing(['email' => 'carla.estilo@bbs.com']);

        // Búsqueda por email
        $responseEmail = $this->actingAs($this->adminUser)
            ->getJson(route('admin.trabajadores.index', ['buscar' => 'carla.estilo@bbs.com']));
        $responseEmail->assertStatus(200);
        $responseEmail->assertJsonCount(1, 'data');
        $responseEmail->assertJsonFragment(['email' => 'carla.estilo@bbs.com']);
        $responseEmail->assertJsonMissing(['email' => 'rodrigo.fade@bbs.com']);

        // Búsqueda por teléfono no debe filtrar coincidencias
        $responseTelefono = $this->actingAs($this->adminUser)
            ->getJson(route('admin.trabajadores.index', ['buscar' => '5559998877']));
        $responseTelefono->assertStatus(200);
        $responseTelefono->assertJsonCount(0, 'data');

        // Búsqueda por dirección no debe filtrar coincidencias
        $responseDireccion = $this->actingAs($this->adminUser)
            ->getJson(route('admin.trabajadores.index', ['buscar' => 'Coyoacán']));
        $responseDireccion->assertStatus(200);
        $responseDireccion->assertJsonCount(0, 'data');
    }

    /**
     * Comprueba que se puede consultar la ficha detallada de un trabajador específico por su ID.
     */
    public function test_se_puede_consultar_la_ficha_detallada_de_un_trabajador_especifico_por_id(): void
    {
        $trabajador = Trabajador::create([
            'nombre' => 'Esteban',
            'apellidos' => 'Quirino Santos',
            'telefono' => '5557778899',
            'email' => 'esteban.barber@bbs.com',
            'direccion' => 'Calle Jalapa #150, Roma Norte',
            'experiencia' => 8,
            'activo' => true,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->getJson(route('admin.trabajadores.show', $trabajador));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'id' => $trabajador->id,
                'nombre' => 'Esteban',
                'apellidos' => 'Quirino Santos',
                'nombre_completo' => 'Esteban Quirino Santos',
                'telefono' => '5557778899',
                'email' => 'esteban.barber@bbs.com',
                'direccion' => 'Calle Jalapa #150, Roma Norte',
                'experiencia' => 8,
                'activo' => true,
            ]
        ]);
    }

    /**
     * Comprueba que consultar un ID inexistente retorna error 404.
     */
    public function test_consulta_de_trabajador_inexistente_retorna_404(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->getJson('/admin/trabajadores/999999');

        $response->assertStatus(404);
    }

    /**
     * Comprueba que un usuario invitado no puede consultar trabajadores y es redirigido a login.
     */
    public function test_usuario_no_autenticado_no_puede_consultar_trabajadores(): void
    {
        $response = $this->get(route('admin.trabajadores.index'));
        $response->assertRedirect(route('login'));

        $responseJson = $this->getJson(route('admin.trabajadores.index'));
        $responseJson->assertStatus(401);
    }
}
