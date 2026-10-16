<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Cita;
use App\Models\Service;
use App\Models\Trabajador;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ClientePerfilTest extends TestCase
{
    use RefreshDatabase;

    protected User $clienteUser;
    protected User $otroUsuario;

    protected function setUp(): void
    {
        parent::setUp();

        if (Role::count() === 0) {
            Role::create(['id' => 1, 'nombre' => 'Administrador']);
            Role::create(['id' => 2, 'nombre' => 'Recepcionista']);
            Role::create(['id' => 3, 'nombre' => 'Trabajador']);
            Role::create(['id' => 4, 'nombre' => 'Cliente']);
        }

        $this->clienteUser = User::create([
            'name' => 'Mariana',
            'apellidos' => 'López Solís',
            'email' => 'mariana.cliente@bbs.com',
            'telefono' => '5551112233',
            'password' => Hash::make('ClienteSeguro123'),
            'role_id' => 4,
        ]);

        $this->otroUsuario = User::create([
            'name' => 'Roberto',
            'apellidos' => 'Gómez',
            'email' => 'roberto.otro@bbs.com',
            'telefono' => '5559990000',
            'password' => Hash::make('OtroPass123'),
            'role_id' => 4,
        ]);
    }

    /**
     * Test: Cliente autenticado puede acceder a su pantalla de perfil.
     */
    public function test_cliente_puede_ver_su_pantalla_de_perfil(): void
    {
        $response = $this->actingAs($this->clienteUser)
            ->get(route('cliente.perfil'));

        $response->assertStatus(200);
        $response->assertSee('Mariana');
        $response->assertSee('López Solís');
        $response->assertSee('mariana.cliente@bbs.com');
        $response->assertSee('Datos Personales');
        $response->assertSee('Seguridad de la Cuenta');
    }

    /**
     * Test: Usuario no autenticado es redirigido al login al intentar acceder a perfil.
     */
    public function test_usuario_no_autenticado_no_puede_acceder_a_perfil(): void
    {
        $response = $this->get(route('cliente.perfil'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Test: Actualización exitosa de datos personales del cliente vía JSON.
     */
    public function test_actualizar_datos_personales_del_cliente_exitoso(): void
    {
        $payload = [
            'name' => 'Mariana Sofía',
            'apellidos' => 'López Hernández',
            'email' => 'mariana.nuevo@bbs.com',
            'telefono' => '5554443322',
            'fecha_nacimiento' => '1995-06-15',
        ];

        $response = $this->actingAs($this->clienteUser)
            ->putJson(route('cliente.perfil.datos'), $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'user' => [
                    'name' => 'Mariana Sofía',
                    'apellidos' => 'López Hernández',
                    'email' => 'mariana.nuevo@bbs.com',
                    'telefono' => '5554443322',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->clienteUser->id,
            'name' => 'Mariana Sofía',
            'email' => 'mariana.nuevo@bbs.com',
            'telefono' => '5554443322',
        ]);
    }

    /**
     * Test: Falla al actualizar datos si el correo ya pertenece a otro cliente.
     */
    public function test_actualizar_datos_falla_si_correo_ya_existe(): void
    {
        $payload = [
            'name' => 'Mariana',
            'apellidos' => 'López',
            'email' => 'roberto.otro@bbs.com', // Correo del otro usuario
            'telefono' => '5551112233',
        ];

        $response = $this->actingAs($this->clienteUser)
            ->putJson(route('cliente.perfil.datos'), $payload);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }

    /**
     * Test: Actualización exitosa de contraseña del cliente.
     */
    public function test_actualizar_password_del_cliente_exitoso(): void
    {
        $payload = [
            'current_password' => 'ClienteSeguro123',
            'password' => 'NuevaSuperClave2026',
            'password_confirmation' => 'NuevaSuperClave2026',
        ];

        $response = $this->actingAs($this->clienteUser)
            ->putJson(route('cliente.perfil.password'), $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->clienteUser->refresh();
        $this->assertTrue(Hash::check('NuevaSuperClave2026', $this->clienteUser->password));
    }

    /**
     * Test: Falla al actualizar contraseña si la actual es errónea.
     */
    public function test_actualizar_password_falla_si_contrasena_actual_no_coincide(): void
    {
        $payload = [
            'current_password' => 'ClaveEquivocada999',
            'password' => 'NuevaSuperClave2026',
            'password_confirmation' => 'NuevaSuperClave2026',
        ];

        $response = $this->actingAs($this->clienteUser)
            ->putJson(route('cliente.perfil.password'), $payload);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);

        $this->clienteUser->refresh();
        $this->assertTrue(Hash::check('ClienteSeguro123', $this->clienteUser->password));
    }

    /**
     * Test: Falla al actualizar perfil si la fecha de nacimiento es mañana o futura.
     */
    public function test_actualizar_datos_falla_si_fecha_de_nacimiento_es_manana_o_futura(): void
    {
        $manana = Carbon::tomorrow()->format('Y-m-d');

        $payload = [
            'name' => 'Mariana',
            'apellidos' => 'López',
            'email' => 'mariana.cliente@bbs.com',
            'telefono' => '5551112233',
            'fecha_nacimiento' => $manana,
        ];

        $response = $this->actingAs($this->clienteUser)
            ->putJson(route('cliente.perfil.datos'), $payload);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);

        $this->assertStringContainsString('mañana', $response->json('message'));
    }
}
