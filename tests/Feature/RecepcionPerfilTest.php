<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class RecepcionPerfilTest extends TestCase
{
    use RefreshDatabase;

    protected User $recepcionUser;
    protected User $otroUsuario;
    protected User $clienteUser;
    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        if (Role::count() === 0) {
            Role::create(['id' => 1, 'nombre' => 'Administrador']);
            Role::create(['id' => 2, 'nombre' => 'Recepcionista']);
            Role::create(['id' => 3, 'nombre' => 'Trabajador']);
            Role::create(['id' => 4, 'nombre' => 'Cliente']);
        }

        $this->recepcionUser = User::create([
            'name' => 'Laura',
            'apellidos' => 'García Gómez',
            'email' => 'laura.recepcion@bbs.com',
            'telefono' => '5559876543',
            'password' => Hash::make('RecepSeguro123!'),
            'role_id' => 2,
        ]);

        $this->otroUsuario = User::create([
            'name' => 'Pedro',
            'apellidos' => 'Martínez',
            'email' => 'pedro.otro@bbs.com',
            'telefono' => '5551114444',
            'password' => Hash::make('OtroPass123!'),
            'role_id' => 3,
        ]);

        $this->clienteUser = User::create([
            'name' => 'Camila',
            'apellidos' => 'Fernández',
            'email' => 'camila@cliente.com',
            'telefono' => '5552225555',
            'password' => Hash::make('ClientePass123!'),
            'role_id' => 4,
        ]);

        $this->adminUser = User::create([
            'name' => 'Carlos',
            'apellidos' => 'Mendoza',
            'email' => 'carlos.admin@bbs.com',
            'telefono' => '5553336666',
            'password' => Hash::make('AdminPass123!'),
            'role_id' => 1,
        ]);
    }

    /**
     * Test: Personal de recepción autenticado puede acceder a su pantalla de perfil.
     */
    public function test_recepcionista_puede_acceder_a_su_pantalla_de_perfil(): void
    {
        $response = $this->actingAs($this->recepcionUser)
            ->get(route('recepcion.perfil'));

        $response->assertStatus(200);
        $response->assertSee('Perfil de Recepción');
        $response->assertSee('Laura García Gómez');
        $response->assertSee('laura.recepcion@bbs.com');
        $response->assertSee('Personal de Recepción');
    }

    /**
     * Test: Usuario no autenticado es redirigido al intentar ver el perfil de recepción.
     */
    public function test_usuario_no_autenticado_es_redirigido(): void
    {
        $response = $this->get(route('recepcion.perfil'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Test: Cliente (rol 4) o Trabajador (rol 3) no pueden acceder al perfil de recepción (403).
     */
    public function test_usuario_no_autorizado_es_bloqueado(): void
    {
        $response = $this->actingAs($this->clienteUser)
            ->get(route('recepcion.perfil'));

        $response->assertStatus(403);
    }

    /**
     * Test: Administrador (rol 1) tiene permiso de acceso por middleware role:1,2.
     */
    public function test_admin_puede_acceder_al_perfil_de_recepcion(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('recepcion.perfil'));

        $response->assertStatus(200);
    }

    /**
     * Test: Personal de recepción puede actualizar sus datos de contacto correctamente.
     */
    public function test_recepcionista_puede_actualizar_datos_personales(): void
    {
        $response = $this->actingAs($this->recepcionUser)
            ->putJson(route('recepcion.perfil.datos'), [
                'name' => 'Laura Elena',
                'apellidos' => 'García Gómez',
                'email' => 'laura.nuevo@bbs.com',
                'telefono' => '5558889999',
                'fecha_nacimiento' => '1992-04-10',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'user' => [
                    'name' => 'Laura Elena',
                    'apellidos' => 'García Gómez',
                    'email' => 'laura.nuevo@bbs.com',
                    'telefono' => '5558889999',
                    'fecha_nacimiento' => '1992-04-10',
                ],
            ]);

        $this->recepcionUser->refresh();
        $this->assertEquals('Laura Elena', $this->recepcionUser->name);
        $this->assertEquals('laura.nuevo@bbs.com', $this->recepcionUser->email);
        $this->assertEquals('5558889999', $this->recepcionUser->telefono);
    }

    /**
     * Test: Falla si intenta usar un email o teléfono ya registrado por otro usuario.
     */
    public function test_recepcionista_no_puede_duplicar_email_o_telefono(): void
    {
        $response = $this->actingAs($this->recepcionUser)
            ->putJson(route('recepcion.perfil.datos'), [
                'name' => 'Laura',
                'apellidos' => 'García',
                'email' => 'pedro.otro@bbs.com', // Usado por otroUsuario
                'telefono' => '5551114444',      // Usado por otroUsuario
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'telefono']);
    }

    /**
     * Test: Falla si la fecha de nacimiento es de mañana o futura.
     */
    public function test_recepcionista_no_puede_ingresar_fecha_nacimiento_futura(): void
    {
        $fechaManana = Carbon::now()->addDay()->format('Y-m-d');

        $response = $this->actingAs($this->recepcionUser)
            ->putJson(route('recepcion.perfil.datos'), [
                'name' => 'Laura',
                'apellidos' => 'García',
                'email' => 'laura.recepcion@bbs.com',
                'fecha_nacimiento' => $fechaManana,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['fecha_nacimiento']);
    }

    /**
     * Test: Personal de recepción puede cambiar su contraseña de forma autónoma.
     */
    public function test_recepcionista_puede_actualizar_password_exitosamente(): void
    {
        $response = $this->actingAs($this->recepcionUser)
            ->putJson(route('recepcion.perfil.password'), [
                'current_password' => 'RecepSeguro123!',
                'password' => 'NuevaRecepPass456!',
                'password_confirmation' => 'NuevaRecepPass456!',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->recepcionUser->refresh();
        $this->assertTrue(Hash::check('NuevaRecepPass456!', $this->recepcionUser->password));
    }

    /**
     * Test: Falla si la contraseña actual no es correcta.
     */
    public function test_recepcionista_falla_si_password_actual_es_incorrecta(): void
    {
        $response = $this->actingAs($this->recepcionUser)
            ->putJson(route('recepcion.perfil.password'), [
                'current_password' => 'PassMala999',
                'password' => 'NuevaValida123',
                'password_confirmation' => 'NuevaValida123',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['current_password']);
    }

    /**
     * Test: Falla si la nueva contraseña es insegura o no coincide.
     */
    public function test_recepcionista_falla_si_nueva_password_es_insegura(): void
    {
        // Caso 1: Menos de 8 caracteres
        $response1 = $this->actingAs($this->recepcionUser)
            ->putJson(route('recepcion.perfil.password'), [
                'current_password' => 'RecepSeguro123!',
                'password' => 'Corta1',
                'password_confirmation' => 'Corta1',
            ]);

        $response1->assertStatus(422)
            ->assertJsonValidationErrors(['password']);

        // Caso 2: Sin números
        $response2 = $this->actingAs($this->recepcionUser)
            ->putJson(route('recepcion.perfil.password'), [
                'current_password' => 'RecepSeguro123!',
                'password' => 'SoloLetrasSinNumero',
                'password_confirmation' => 'SoloLetrasSinNumero',
            ]);

        $response2->assertStatus(422)
            ->assertJsonValidationErrors(['password']);

        // Caso 3: Desacuerdo con confirmación
        $response3 = $this->actingAs($this->recepcionUser)
            ->putJson(route('recepcion.perfil.password'), [
                'current_password' => 'RecepSeguro123!',
                'password' => 'PasswordValida1',
                'password_confirmation' => 'OtraPasswordDiferente',
            ]);

        $response3->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * Test: Falla si la nueva contraseña es igual a la contraseña actual.
     */
    public function test_recepcionista_falla_si_nueva_password_es_igual_a_la_actual(): void
    {
        $response = $this->actingAs($this->recepcionUser)
            ->putJson(route('recepcion.perfil.password'), [
                'current_password' => 'RecepSeguro123!',
                'password' => 'RecepSeguro123!',
                'password_confirmation' => 'RecepSeguro123!',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }
}
