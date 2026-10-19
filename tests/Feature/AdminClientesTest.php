<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AdminClientesTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $clienteUser;
    protected User $trabajadorUser;

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
            'apellidos' => 'Administrador',
            'email' => 'admin@test.com',
            'telefono' => '5551112233',
            'password' => Hash::make('Secret123!'),
            'role_id' => 1,
        ]);

        $this->clienteUser = User::create([
            'name' => 'Juan',
            'apellidos' => 'Pérez',
            'email' => 'juan.perez@test.com',
            'telefono' => '5559998877',
            'password' => Hash::make('Secret123!'),
            'role_id' => 4,
        ]);

        $this->trabajadorUser = User::create([
            'name' => 'Roberto',
            'apellidos' => 'Barbero',
            'email' => 'roberto@test.com',
            'telefono' => '5554443322',
            'password' => Hash::make('Secret123!'),
            'role_id' => 3,
        ]);
    }

    public function test_guest_is_redirected_to_login_when_accessing_admin_clientes(): void
    {
        $response = $this->get(route('admin.clientes'));
        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_access_admin_clientes(): void
    {
        $response = $this->actingAs($this->clienteUser)->get(route('admin.clientes'));
        $response->assertForbidden();

        $response2 = $this->actingAs($this->trabajadorUser)->get(route('admin.clientes'));
        $response2->assertForbidden();
    }

    public function test_admin_can_view_clientes_directory_without_agendar_cita(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.clientes'));

        $response->assertOk();
        $response->assertViewIs('admin.clientes');
        $response->assertSee('Directorio de Clientes');
        $response->assertSee('Juan Pérez');
        $response->assertSee('juan.perez@test.com');
        $response->assertSee('5559998877');

        // Strictly verify that the admin DOES NOT have the Agendar cita option
        $response->assertDontSee('Agendar cita');
    }

    public function test_admin_clientes_search_filter(): void
    {
        User::create([
            'name' => 'María',
            'apellidos' => 'González',
            'email' => 'maria@test.com',
            'telefono' => '5550009988',
            'password' => Hash::make('Secret123!'),
            'role_id' => 4,
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.clientes', ['search' => 'María']));

        $response->assertOk();
        $response->assertSee('María González');
        $response->assertDontSee('Juan Pérez');

        // Búsqueda restringida exclusivamente a nombre (no email ni teléfono)
        $responseEmail = $this->actingAs($this->adminUser)->get(route('admin.clientes', ['search' => 'maria@test.com']));
        $responseEmail->assertDontSee('María González');
    }
}
