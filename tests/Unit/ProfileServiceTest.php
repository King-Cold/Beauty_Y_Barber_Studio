<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Services\ProfileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ProfileService $service;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new ProfileService();

        // Crear roles base si no existen
        if (Role::count() === 0) {
            Role::create(['id' => 1, 'nombre' => 'Administrador']);
            Role::create(['id' => 2, 'nombre' => 'Recepcionista']);
            Role::create(['id' => 3, 'nombre' => 'Trabajador']);
            Role::create(['id' => 4, 'nombre' => 'Cliente']);
        }

        // Crear usuario de prueba
        $this->user = User::create([
            'name' => 'Carlos',
            'apellidos' => 'Mendoza',
            'telefono' => '5551234567',
            'email' => 'carlos.test@bbs.com',
            'password' => Hash::make('ClaveActual123'),
            'role_id' => 1,
        ]);
    }

    /**
     * Test: Cambio de contraseña exitoso cuando cumple todas las reglas de negocio.
     */
    public function test_cambio_de_contrasena_exitoso(): void
    {
        $data = [
            'current_password' => 'ClaveActual123',
            'password' => 'NuevaClaveSegura2026',
            'password_confirmation' => 'NuevaClaveSegura2026',
        ];

        $resultado = $this->service->changePassword($this->user, $data);

        $this->assertTrue($resultado['success']);
        $this->user->refresh();

        // Validar que la nueva contraseña funcione y esté hasheada
        $this->assertTrue(Hash::check('NuevaClaveSegura2026', $this->user->password));
        $this->assertFalse(Hash::check('ClaveActual123', $this->user->password));
    }

    /**
     * Test: Soporta alias de nombres de campo alternativos (currentPass, newPass, confirmPass).
     */
    public function test_soporta_nombres_de_campo_alternativos(): void
    {
        $data = [
            'currentPass' => 'ClaveActual123',
            'newPass' => 'PasswordAlternativo99',
            'confirmPass' => 'PasswordAlternativo99',
        ];

        $resultado = $this->service->changePassword($this->user, $data);

        $this->assertTrue($resultado['success']);
        $this->user->refresh();
        $this->assertTrue(Hash::check('PasswordAlternativo99', $this->user->password));
    }

    /**
     * Test: Falla si la contraseña actual es incorrecta.
     */
    public function test_falla_si_contrasena_actual_es_incorrecta(): void
    {
        $this->expectException(ValidationException::class);

        $data = [
            'current_password' => 'ContrasenaErronea999',
            'password' => 'NuevaClave2026',
            'password_confirmation' => 'NuevaClave2026',
        ];

        try {
            $this->service->validatePasswordChange($this->user, $data);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('current_password', $e->errors());
            $this->assertStringContainsString('incorrecta', $e->errors()['current_password'][0]);
            throw $e;
        }
    }

    /**
     * Test: Falla si la nueva contraseña tiene menos de 8 caracteres.
     */
    public function test_falla_si_nueva_contrasena_tiene_menos_de_8_caracteres(): void
    {
        $this->expectException(ValidationException::class);

        $data = [
            'current_password' => 'ClaveActual123',
            'password' => 'Corta1',
            'password_confirmation' => 'Corta1',
        ];

        try {
            $this->service->validatePasswordChange($this->user, $data);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('password', $e->errors());
            $this->assertStringContainsString('al menos 8 caracteres', $e->errors()['password'][0]);
            throw $e;
        }
    }

    /**
     * Test: Falla si la nueva contraseña no es alfanumérica (no tiene números).
     */
    public function test_falla_si_nueva_contrasena_no_tiene_numeros(): void
    {
        $this->expectException(ValidationException::class);

        $data = [
            'current_password' => 'ClaveActual123',
            'password' => 'SoloLetrasSinNumeros',
            'password_confirmation' => 'SoloLetrasSinNumeros',
        ];

        try {
            $this->service->validatePasswordChange($this->user, $data);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('password', $e->errors());
            $this->assertStringContainsString('alfanumérica', $e->errors()['password'][0]);
            throw $e;
        }
    }

    /**
     * Test: Falla si la confirmación de la contraseña no coincide.
     */
    public function test_falla_si_confirmacion_no_coincide(): void
    {
        $this->expectException(ValidationException::class);

        $data = [
            'current_password' => 'ClaveActual123',
            'password' => 'PasswordValido123',
            'password_confirmation' => 'PasswordDiferente999',
        ];

        try {
            $this->service->validatePasswordChange($this->user, $data);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('password', $e->errors());
            $this->assertStringContainsString('coincide', $e->errors()['password'][0]);
            throw $e;
        }
    }

    /**
     * Test: Falla si la nueva contraseña es idéntica a la actual.
     */
    public function test_falla_si_nueva_contrasena_es_igual_a_la_actual(): void
    {
        $this->expectException(ValidationException::class);

        $data = [
            'current_password' => 'ClaveActual123',
            'password' => 'ClaveActual123',
            'password_confirmation' => 'ClaveActual123',
        ];

        try {
            $this->service->validatePasswordChange($this->user, $data);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('password', $e->errors());
            $this->assertStringContainsString('diferente', $e->errors()['password'][0]);
            throw $e;
        }
    }

    /**
     * Test: Actualización de datos personales de perfil exitosa conservando su propio email.
     */
    public function test_actualizacion_de_perfil_exitosa_conservando_su_propio_email(): void
    {
        $data = [
            'name' => 'Carlos Alberto',
            'apellidos' => 'Mendoza Silva',
            'email' => 'carlos.test@bbs.com', // Mismo email
            'telefono' => '5559998888',
        ];

        $updatedUser = $this->service->updateProfile($this->user, $data);

        $this->assertEquals('Carlos Alberto', $updatedUser->name);
        $this->assertEquals('Mendoza Silva', $updatedUser->apellidos);
        $this->assertEquals('5559998888', $updatedUser->telefono);
    }

    /**
     * Test: Falla al actualizar perfil si el correo ya pertenece a otro usuario.
     */
    public function test_falla_si_correo_pertenece_a_otro_usuario(): void
    {
        // Crear otro usuario
        User::create([
            'name' => 'Otro',
            'apellidos' => 'Usuario',
            'telefono' => '5550001111',
            'email' => 'otro.usuario@bbs.com',
            'password' => Hash::make('ClaveOtro123'),
            'role_id' => 4,
        ]);

        $this->expectException(ValidationException::class);

        $data = [
            'name' => 'Carlos',
            'apellidos' => 'Mendoza',
            'email' => 'otro.usuario@bbs.com', // Ya usado por otro
            'telefono' => '5551234567',
        ];

        try {
            $this->service->validateProfileData($this->user, $data);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('email', $e->errors());
            $this->assertStringContainsString('registrado', $e->errors()['email'][0]);
            throw $e;
        }
    }

    /**
     * Test: Soporta validación de reglas adicionales específicas de rol (ej. dirección).
     */
    public function test_soporta_reglas_adicionales_personalizadas(): void
    {
        $data = [
            'name' => 'Alejandro',
            'apellidos' => 'Torres',
            'email' => 'alejandro.nuevo@bbs.com',
            'telefono' => '5553332211',
            'direccion' => 'Av. Central 456, Barber Studio',
        ];

        $extraRules = [
            'direccion' => ['required', 'string', 'max:255'],
        ];

        $validated = $this->service->validateProfileData($this->user, $data, $extraRules);

        $this->assertEquals('Av. Central 456, Barber Studio', $validated['direccion']);
    }
}
