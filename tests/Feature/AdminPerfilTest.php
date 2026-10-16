<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Service;
use App\Models\Cita;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminPerfilTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $otroUsuario;
    protected User $clienteUser;

    protected function setUp(): void
    {
        parent::setUp();

        if (Role::count() === 0) {
            Role::create(['id' => 1, 'nombre' => 'Administrador']);
            Role::create(['id' => 2, 'nombre' => 'Recepcionista']);
            Role::create(['id' => 3, 'nombre' => 'Trabajador']);
            Role::create(['id' => 4, 'nombre' => 'Cliente']);
        }

        $this->adminUser = User::create([
            'name' => 'Carlos',
            'apellidos' => 'Mendoza',
            'email' => 'carlos.admin@bbs.com',
            'telefono' => '5551234567',
            'password' => Hash::make('AdminActual123!'),
            'role_id' => 1,
        ]);

        $this->otroUsuario = User::create([
            'name' => 'Alejandro',
            'apellidos' => 'Torres',
            'email' => 'alejandro.staff@bbs.com',
            'telefono' => '5559870000',
            'password' => Hash::make('Staff123!'),
            'role_id' => 3,
        ]);

        $this->clienteUser = User::create([
            'name' => 'Mariana',
            'apellidos' => 'López',
            'email' => 'mariana@cliente.com',
            'telefono' => '5550001111',
            'password' => Hash::make('Cliente123!'),
            'role_id' => 4,
        ]);
    }

    /**
     * Test: Administrador autenticado puede acceder a su pantalla de perfil.
     */
    public function test_admin_puede_acceder_a_su_pantalla_de_perfil(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.perfil'));

        $response->assertStatus(200);
        $response->assertSee('Gestión de Perfil de Administrador');
        $response->assertSee('Carlos Mendoza');
        $response->assertSee('carlos.admin@bbs.com');
        $response->assertSee('Nivel 1');
    }

    /**
     * Test: Usuario no autenticado es redirigido al intentar ver el perfil de administrador.
     */
    public function test_usuario_no_autenticado_es_redirigido(): void
    {
        $response = $this->get(route('admin.perfil'));
        $response->assertRedirect('/');
    }

    /**
     * Test: Usuario no administrador (ej. cliente) es bloqueado por middleware de rol.
     */
    public function test_usuario_no_admin_es_bloqueado(): void
    {
        $response = $this->actingAs($this->clienteUser)
            ->get(route('admin.perfil'));

        $response->assertStatus(403);
    }

    /**
     * Test: Administrador puede actualizar sus datos personales correctamente.
     */
    public function test_admin_puede_actualizar_datos_personales(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->putJson(route('admin.perfil.datos'), [
                'name' => 'Carlos Alberto',
                'apellidos' => 'Mendoza Silva',
                'email' => 'carlos.nuevo@bbs.com',
                'telefono' => '5553334444',
                'fecha_nacimiento' => '1985-06-15',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'user' => [
                    'name' => 'Carlos Alberto',
                    'apellidos' => 'Mendoza Silva',
                    'email' => 'carlos.nuevo@bbs.com',
                    'telefono' => '5553334444',
                    'fecha_nacimiento' => '1985-06-15',
                ],
            ]);

        $this->adminUser->refresh();
        $this->assertEquals('Carlos Alberto', $this->adminUser->name);
        $this->assertEquals('Mendoza Silva', $this->adminUser->apellidos);
        $this->assertEquals('carlos.nuevo@bbs.com', $this->adminUser->email);
        $this->assertEquals('5553334444', $this->adminUser->telefono);
    }

    /**
     * Test: Administrador no puede duplicar el email o teléfono de otro usuario.
     */
    public function test_admin_no_puede_duplicar_email_o_telefono(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->putJson(route('admin.perfil.datos'), [
                'name' => 'Carlos',
                'apellidos' => 'Mendoza',
                'email' => 'alejandro.staff@bbs.com', // Ya registrado por otroUsuario
                'telefono' => '5559870000',           // Ya registrado por otroUsuario
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'telefono']);
    }

    /**
     * Test: Administrador no puede ingresar fecha de nacimiento futura o de mañana.
     */
    public function test_admin_no_puede_ingresar_fecha_nacimiento_futura(): void
    {
        $fechaFutura = Carbon::now()->addDay()->format('Y-m-d');

        $response = $this->actingAs($this->adminUser)
            ->putJson(route('admin.perfil.datos'), [
                'name' => 'Carlos',
                'apellidos' => 'Mendoza',
                'email' => 'carlos.admin@bbs.com',
                'fecha_nacimiento' => $fechaFutura,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['fecha_nacimiento']);
    }

    /**
     * Test: Administrador puede actualizar su contraseña de nivel 1 con éxito.
     */
    public function test_admin_puede_actualizar_password_exitosamente(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->putJson(route('admin.perfil.password'), [
                'current_password' => 'AdminActual123!',
                'password' => 'AdminNuevo456!',
                'password_confirmation' => 'AdminNuevo456!',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->adminUser->refresh();
        $this->assertTrue(Hash::check('AdminNuevo456!', $this->adminUser->password));
    }

    /**
     * Test: Falla si la contraseña actual es errónea.
     */
    public function test_admin_falla_si_password_actual_es_incorrecta(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->putJson(route('admin.perfil.password'), [
                'current_password' => 'PassEquivocada999',
                'password' => 'NuevoValido123',
                'password_confirmation' => 'NuevoValido123',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['current_password']);
    }

    /**
     * Test: Falla si la nueva contraseña no cumple los requisitos (ej. menor a 8 caracteres o solo letras).
     */
    public function test_admin_falla_si_nueva_password_es_insegura(): void
    {
        // Caso 1: Menos de 8 caracteres
        $response = $this->actingAs($this->adminUser)
            ->putJson(route('admin.perfil.password'), [
                'current_password' => 'AdminActual123!',
                'password' => 'Corta1',
                'password_confirmation' => 'Corta1',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);

        // Caso 2: No alfanumérica (solo letras sin números)
        $response2 = $this->actingAs($this->adminUser)
            ->putJson(route('admin.perfil.password'), [
                'current_password' => 'AdminActual123!',
                'password' => 'SoloLetrasSinNumero',
                'password_confirmation' => 'SoloLetrasSinNumero',
            ]);

        $response2->assertStatus(422)
            ->assertJsonValidationErrors(['password']);

        // Caso 3: Confirmación no coincide
        $response3 = $this->actingAs($this->adminUser)
            ->putJson(route('admin.perfil.password'), [
                'current_password' => 'AdminActual123!',
                'password' => 'ValidaPass123',
                'password_confirmation' => 'OtraCosaTotalmenteDistinta',
            ]);

        $response3->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * Test: Falla si la nueva contraseña es idéntica a la contraseña actual.
     */
    public function test_admin_falla_si_nueva_password_es_igual_a_la_actual(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->putJson(route('admin.perfil.password'), [
                'current_password' => 'AdminActual123!',
                'password' => 'AdminActual123!',
                'password_confirmation' => 'AdminActual123!',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }
}
