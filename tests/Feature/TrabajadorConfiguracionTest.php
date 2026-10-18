<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Trabajador;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class TrabajadorConfiguracionTest extends TestCase
{
    use RefreshDatabase;

    protected User $trabajadorUser;
    protected Trabajador $trabajador;
    protected Service $servicio;

    protected function setUp(): void
    {
        parent::setUp();

        if (Role::count() === 0) {
            Role::create(['id' => 1, 'nombre' => 'Administrador']);
            Role::create(['id' => 2, 'nombre' => 'Recepcionista']);
            Role::create(['id' => 3, 'nombre' => 'Trabajador']);
            Role::create(['id' => 4, 'nombre' => 'Cliente']);
        }

        $this->trabajadorUser = User::create([
            'name' => 'Alejandro',
            'apellidos' => 'Torres',
            'email' => 'alejandro.barbero@bbs.com',
            'telefono' => '5554567890',
            'password' => Hash::make('Barbero123!'),
            'role_id' => 3,
        ]);

        $this->trabajador = Trabajador::create([
            'user_id' => $this->trabajadorUser->id,
            'direccion' => 'Av. de los Barberos #101',
            'experiencia' => 5,
            'activo' => true,
        ]);

        $this->servicio = Service::create([
            'name' => 'Corte Fade Clásico',
            'description' => 'Degradado profesional con navaja y tijera.',
            'price' => 250.00,
            'duration_minutes' => 45,
            'category' => 'barberia',
            'is_active' => true,
        ]);

        if (Schema::hasTable('servicio_trabajador')) {
            $this->trabajador->servicios()->attach($this->servicio->id);
        }
    }

    /**
     * Test: Vista de configuración accesible por el trabajador y muestra sus servicios reales.
     */
    public function test_trabajador_puede_ver_vista_de_configuracion_con_servicios_asignados(): void
    {
        $response = $this->actingAs($this->trabajadorUser)
            ->get(route('trabajador.configuracion'));

        $response->assertStatus(200);
        $response->assertSee('Datos Personales');
        $response->assertSee('Seguridad de la Cuenta');
        $response->assertSee('Alejandro');
        $response->assertSee('Torres');
        $response->assertSee('Corte Fade Clásico');
    }

    /**
     * Test: Actualización exitosa de datos personales (nombre, apellidos, teléfono y dirección).
     */
    public function test_actualizar_datos_personales_exitoso_via_json(): void
    {
        $payload = [
            'name' => 'Alejandro Manuel',
            'apellidos' => 'Torres Navarro',
            'telefono' => '5559871234',
            'direccion' => 'Nueva Sucursal Centro #50',
        ];

        $response = $this->actingAs($this->trabajadorUser)
            ->putJson(route('trabajador.configuracion.datos'), $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => '¡Datos personales actualizados correctamente!',
                'user' => [
                    'name' => 'Alejandro Manuel',
                    'apellidos' => 'Torres Navarro',
                    'telefono' => '5559871234',
                    'direccion' => 'Nueva Sucursal Centro #50',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->trabajadorUser->id,
            'name' => 'Alejandro Manuel',
            'apellidos' => 'Torres Navarro',
            'telefono' => '5559871234',
        ]);

        $this->assertDatabaseHas('trabajadores', [
            'id' => $this->trabajador->id,
            'direccion' => 'Nueva Sucursal Centro #50',
        ]);
    }

    /**
     * Test: Validación de datos personales obligatorios.
     */
    public function test_validacion_falla_si_faltan_campos_obligatorios(): void
    {
        $payload = [
            'name' => '',
            'apellidos' => '',
        ];

        $response = $this->actingAs($this->trabajadorUser)
            ->putJson(route('trabajador.configuracion.datos'), $payload);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }

    /**
     * Test: Actualización exitosa de contraseña mediante API JSON.
     */
    public function test_actualizar_password_exitoso(): void
    {
        $payload = [
            'current_password' => 'Barbero123!',
            'password' => 'NuevaClaveSegura2026',
            'password_confirmation' => 'NuevaClaveSegura2026',
        ];

        $response = $this->actingAs($this->trabajadorUser)
            ->putJson(route('trabajador.configuracion.password'), $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->trabajadorUser->refresh();
        $this->assertTrue(Hash::check('NuevaClaveSegura2026', $this->trabajadorUser->password));
    }

    /**
     * Test: Falla si la contraseña actual no es válida.
     */
    public function test_actualizar_password_falla_si_password_actual_es_incorrecto(): void
    {
        $payload = [
            'current_password' => 'PasswordIncorrecto999',
            'password' => 'NuevaClaveSegura2026',
            'password_confirmation' => 'NuevaClaveSegura2026',
        ];

        $response = $this->actingAs($this->trabajadorUser)
            ->putJson(route('trabajador.configuracion.password'), $payload);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);

        $this->trabajadorUser->refresh();
        $this->assertTrue(Hash::check('Barbero123!', $this->trabajadorUser->password));
    }

    /**
     * Test: Falla si las contraseñas nuevas no coinciden.
     */
    public function test_actualizar_password_falla_si_confirmacion_no_coincide(): void
    {
        $payload = [
            'current_password' => 'Barbero123!',
            'password' => 'NuevaClaveSegura2026',
            'password_confirmation' => 'NoCoincide999',
        ];

        $response = $this->actingAs($this->trabajadorUser)
            ->putJson(route('trabajador.configuracion.password'), $payload);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }

    /**
     * Test: El menú de perfil no contiene la opción 'Ver mi agenda', pero conserva Mi Perfil y Cerrar sesión.
     */
    public function test_menu_perfil_no_contiene_opcion_ver_mi_agenda(): void
    {
        $response = $this->actingAs($this->trabajadorUser)
            ->get(route('trabajador.configuracion'));

        $response->assertStatus(200);
        $response->assertDontSee('Ver mi agenda');
        $response->assertSee('Mi Perfil');
        $response->assertSee('Cerrar sesión');

        // La agenda sigue disponible desde el sidebar
        $response->assertSee(route('trabajador.agenda'));
        $response->assertSee('Agenda del Trabajador');
    }

    /**
     * Test: La agenda sigue accesible y funcional desde sus apartados del sistema.
     */
    public function test_agenda_del_trabajador_sigue_accesible(): void
    {
        $response = $this->actingAs($this->trabajadorUser)
            ->get(route('trabajador.agenda'));

        $response->assertStatus(200);
        $response->assertDontSee('Ver mi agenda');
        $response->assertSee('Agenda del Trabajador');
    }
}

