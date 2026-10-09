<?php

namespace Tests\Feature;

use App\Models\Trabajador;
use App\Models\User;
use Database\Seeders\TestUsersSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TrabajadorRegistroTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TestUsersSeeder::class);

        // Obtener usuario administrador (role_id 1)
        $this->adminUser = User::where('role_id', 1)->first();
    }

    /**
     * Comprueba que un administrador puede registrar exitosamente un trabajador con datos válidos.
     */
    public function test_un_administrador_autenticado_puede_registrar_un_trabajador_exitosamente(): void
    {
        $payload = [
            'nombre' => 'Mateo',
            'apellidos' => 'Ramírez Vargas',
            'telefono' => '5551234567',
            'email' => 'mateo.barber@bbs.com',
            'direccion' => 'Calle Sonora #120, Col. Condesa',
            'experiencia' => 5,
        ];

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.trabajadores.store'), $payload);

        $response->assertRedirect(route('admin.trabajadores.index'));
        $response->assertSessionHas('success', 'Trabajador registrado exitosamente.');

        $this->assertDatabaseHas('trabajadores', [
            'nombre' => 'Mateo',
            'apellidos' => 'Ramírez Vargas',
            'email' => 'mateo.barber@bbs.com',
            'telefono' => '5551234567',
            'direccion' => 'Calle Sonora #120, Col. Condesa',
            'experiencia' => 5,
            'activo' => true,
        ]);
    }

    /**
     * Comprueba que se puede registrar vía API / AJAX y retorna respuesta JSON 201.
     */
    public function test_registro_via_json_retorna_201_y_objeto_creado(): void
    {
        $payload = [
            'nombre' => 'Laura',
            'apellidos' => 'Castillo Díaz',
            'telefono' => '5559876543',
            'email' => 'laura.estilo@bbs.com',
            'direccion' => 'Av. Revolución #340, San Ángel',
            'experiencia' => 8,
        ];

        $response = $this->actingAs($this->adminUser)
            ->postJson(route('admin.trabajadores.store'), $payload);

        $response->assertStatus(201);
        $response->assertJson([
            'success' => true,
            'message' => 'Trabajador registrado exitosamente.',
            'data' => [
                'email' => 'laura.estilo@bbs.com',
                'nombre' => 'Laura',
            ],
        ]);
    }

    /**
     * Comprueba la subida y almacenamiento correcto de la fotografía.
     */
    public function test_se_puede_subir_una_fotografia_valida_al_registrar_trabajador(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('fotografia_trabajador.jpg', 400, 400);

        $payload = [
            'nombre' => 'Gabriel',
            'apellidos' => 'Navarro',
            'telefono' => '5553334444',
            'email' => 'gabriel.fade@bbs.com',
            'direccion' => 'Av. Insurgentes #890',
            'experiencia' => 4,
            'fotografia' => $file,
        ];

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.trabajadores.store'), $payload);

        $response->assertRedirect(route('admin.trabajadores.index'));

        $trabajador = Trabajador::where('email', 'gabriel.fade@bbs.com')->first();
        $this->assertNotNull($trabajador);
        $this->assertNotNull($trabajador->fotografia);

        Storage::disk('public')->assertExists($trabajador->fotografia);
    }

    /**
     * Comprueba validaciones de campos requeridos.
     */
    public function test_validaciones_de_campos_requeridos(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.trabajadores.store'), []);

        $response->assertSessionHasErrors([
            'nombre',
            'apellidos',
            'telefono',
            'email',
            'direccion',
            'experiencia',
        ]);
    }

    /**
     * Comprueba que no se permite registrar un trabajador con correo electrónico ya existente.
     */
    public function test_email_debe_ser_unico(): void
    {
        Trabajador::create([
            'nombre' => 'Existente',
            'apellidos' => 'Prueba',
            'telefono' => '5551112233',
            'email' => 'correo.existente@bbs.com',
            'direccion' => 'Calle Falsa 123',
            'experiencia' => 2,
            'activo' => true,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.trabajadores.store'), [
                'nombre' => 'Nuevo',
                'apellidos' => 'Intento',
                'telefono' => '5559998877',
                'email' => 'correo.existente@bbs.com',
                'direccion' => 'Otra calle 456',
                'experiencia' => 3,
            ]);

        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Comprueba que el teléfono debe tener exactamente 10 dígitos.
     */
    public function test_el_telefono_debe_tener_exactamente_10_digitos(): void
    {
        // Prueba con menos de 10 dígitos
        $responseCorto = $this->actingAs($this->adminUser)
            ->post(route('admin.trabajadores.store'), [
                'nombre' => 'Test',
                'apellidos' => 'Corto',
                'telefono' => '12345',
                'email' => 'corto@bbs.com',
                'direccion' => 'Calle 1',
                'experiencia' => 2,
            ]);
        $responseCorto->assertSessionHasErrors(['telefono']);

        // Prueba con más de 10 dígitos
        $responseLargo = $this->actingAs($this->adminUser)
            ->post(route('admin.trabajadores.store'), [
                'nombre' => 'Test',
                'apellidos' => 'Largo',
                'telefono' => '123456789012',
                'email' => 'largo@bbs.com',
                'direccion' => 'Calle 1',
                'experiencia' => 2,
            ]);
        $responseLargo->assertSessionHasErrors(['telefono']);

        // Prueba con letras o símbolos en el teléfono
        $responseLetras = $this->actingAs($this->adminUser)
            ->post(route('admin.trabajadores.store'), [
                'nombre' => 'Test',
                'apellidos' => 'Letras',
                'telefono' => '555abc1234',
                'email' => 'letras@bbs.com',
                'direccion' => 'Calle 1',
                'experiencia' => 2,
            ]);
        $responseLetras->assertSessionHasErrors(['telefono']);
    }

    /**
     * Comprueba que no se permite registrar un teléfono duplicado.
     */
    public function test_no_permite_telefono_duplicado(): void
    {
        Trabajador::create([
            'nombre' => 'Existente',
            'apellidos' => 'Tel',
            'telefono' => '5551112233',
            'email' => 'unico1@bbs.com',
            'direccion' => 'Calle 123',
            'experiencia' => 2,
            'activo' => true,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.trabajadores.store'), [
                'nombre' => 'Nuevo',
                'apellidos' => 'Duplicado',
                'telefono' => '5551112233',
                'email' => 'unico2@bbs.com',
                'direccion' => 'Calle 456',
                'experiencia' => 3,
            ]);

        $response->assertSessionHasErrors(['telefono']);

        // Comprueba también rechazo vía API / JSON
        $responseJson = $this->actingAs($this->adminUser)
            ->postJson(route('admin.trabajadores.store'), [
                'nombre' => 'Nuevo Json',
                'apellidos' => 'Duplicado',
                'telefono' => '5551112233',
                'email' => 'unico3@bbs.com',
                'direccion' => 'Calle 456',
                'experiencia' => 3,
            ]);

        $responseJson->assertStatus(422);
        $responseJson->assertJsonValidationErrors(['telefono']);
    }

    /**
     * Comprueba que el correo electrónico debe tener un formato válido con dominio y TLD.
     */
    public function test_el_correo_debe_tener_formato_valido(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.trabajadores.store'), [
                'nombre' => 'Test',
                'apellidos' => 'Email Invalido',
                'telefono' => '5559998877',
                'email' => 'correo-invalido-sin-arroba',
                'direccion' => 'Calle 1',
                'experiencia' => 2,
            ]);

        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Comprueba que la experiencia debe ser un número entero no negativo.
     */
    public function test_la_experiencia_no_puede_ser_negativa(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.trabajadores.store'), [
                'nombre' => 'Carlos',
                'apellidos' => 'Mora',
                'telefono' => '5550001122',
                'email' => 'carlos.mora@bbs.com',
                'direccion' => 'Calle Sur #12',
                'experiencia' => -3,
            ]);

        $response->assertSessionHasErrors(['experiencia']);
    }

    /**
     * Comprueba que un usuario invitado (no autenticado) no puede registrar trabajadores.
     */
    public function test_usuario_no_autenticado_no_puede_registrar_trabajador(): void
    {
        $response = $this->post(route('admin.trabajadores.store'), [
            'nombre' => 'Intruso',
            'apellidos' => 'Anonimo',
            'telefono' => '5551112222',
            'email' => 'intruso@bbs.com',
            'direccion' => 'Calle Secreta #1',
            'experiencia' => 1,
        ]);

        $response->assertRedirect(route('login'));
    }

    /**
     * Subtarea 1: Comprueba que el nombre del barbero debe ser único dentro del sistema.
     */
    public function test_nombre_debe_ser_unico_en_el_sistema(): void
    {
        Trabajador::create([
            'nombre' => 'Carlos',
            'apellidos' => 'Ramírez',
            'telefono' => '5551112233',
            'email' => 'carlos1@bbs.com',
            'direccion' => 'Calle 10 #12',
            'experiencia' => 3,
            'activo' => true,
        ]);

        // Intento de registrar otro barbero con el mismo nombre 'Carlos' vía formulario web
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.trabajadores.store'), [
                'nombre' => 'Carlos',
                'apellidos' => 'Gómez',
                'telefono' => '5559998877',
                'email' => 'carlos2@bbs.com',
                'direccion' => 'Calle 20 #34',
                'experiencia' => 4,
            ]);

        $response->assertSessionHasErrors(['nombre']);

        // Intento vía API / JSON
        $responseJson = $this->actingAs($this->adminUser)
            ->postJson(route('admin.trabajadores.store'), [
                'nombre' => 'Carlos',
                'apellidos' => 'Gómez',
                'telefono' => '5559998877',
                'email' => 'carlos2@bbs.com',
                'direccion' => 'Calle 20 #34',
                'experiencia' => 4,
            ]);

        $responseJson->assertStatus(422);
        $responseJson->assertJsonValidationErrors(['nombre']);
    }

    /**
     * Subtarea 1: Comprueba que el nombre únicamente permite letras del abecedario y rechaza números y caracteres especiales.
     */
    public function test_nombre_solo_debe_permitir_letras_del_abecedario(): void
    {
        // Intento con números
        $responseConNumeros = $this->actingAs($this->adminUser)
            ->post(route('admin.trabajadores.store'), [
                'nombre' => 'Carlos123',
                'apellidos' => 'Pérez',
                'telefono' => '5551113344',
                'email' => 'num@bbs.com',
                'direccion' => 'Calle 1',
                'experiencia' => 2,
            ]);
        $responseConNumeros->assertSessionHasErrors(['nombre']);

        // Intento con caracteres especiales y símbolos
        $responseConSimbolos = $this->actingAs($this->adminUser)
            ->post(route('admin.trabajadores.store'), [
                'nombre' => 'Carlos$#@!',
                'apellidos' => 'Pérez',
                'telefono' => '5551113344',
                'email' => 'simb@bbs.com',
                'direccion' => 'Calle 1',
                'experiencia' => 2,
            ]);
        $responseConSimbolos->assertSessionHasErrors(['nombre']);

        // Intento con guiones bajos o caracteres no permitidos
        $responseConGuiones = $this->actingAs($this->adminUser)
            ->post(route('admin.trabajadores.store'), [
                'nombre' => 'Carlos_Barber',
                'apellidos' => 'Pérez',
                'telefono' => '5551113344',
                'email' => 'guion@bbs.com',
                'direccion' => 'Calle 1',
                'experiencia' => 2,
            ]);
        $responseConGuiones->assertSessionHasErrors(['nombre']);
    }

    /**
     * Subtarea 1: Comprueba que el nombre permite letras con acentos y espacios.
     */
    public function test_nombre_permite_letras_acentuadas_y_espacios(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.trabajadores.store'), [
                'nombre' => 'José María',
                'apellidos' => 'Hernández',
                'telefono' => '5557778899',
                'email' => 'jose.maria@bbs.com',
                'direccion' => 'Calle Central 5',
                'experiencia' => 5,
            ]);

        $response->assertRedirect(route('admin.trabajadores.index'));
        $this->assertDatabaseHas('trabajadores', [
            'nombre' => 'José María',
            'email' => 'jose.maria@bbs.com',
        ]);
    }

    /**
     * Subtarea 1: Comprueba que los apellidos únicamente permiten letras del abecedario y rechazan números y caracteres especiales.
     */
    public function test_apellidos_solo_debe_permitir_letras_del_abecedario(): void
    {
        // Intento con números en apellidos
        $responseConNumeros = $this->actingAs($this->adminUser)
            ->post(route('admin.trabajadores.store'), [
                'nombre' => 'Carlos',
                'apellidos' => 'Pérez123',
                'telefono' => '5551113344',
                'email' => 'ape.num@bbs.com',
                'direccion' => 'Calle 1',
                'experiencia' => 2,
            ]);
        $responseConNumeros->assertSessionHasErrors(['apellidos']);

        // Intento con símbolos en apellidos
        $responseConSimbolos = $this->actingAs($this->adminUser)
            ->post(route('admin.trabajadores.store'), [
                'nombre' => 'Carlos',
                'apellidos' => 'Pérez$#@!',
                'telefono' => '5551113344',
                'email' => 'ape.simb@bbs.com',
                'direccion' => 'Calle 1',
                'experiencia' => 2,
            ]);
        $responseConSimbolos->assertSessionHasErrors(['apellidos']);

        // Acepta apellidos con acentos y espacios
        $responseValido = $this->actingAs($this->adminUser)
            ->post(route('admin.trabajadores.store'), [
                'nombre' => 'Carlos',
                'apellidos' => 'Gómez Ruiz',
                'telefono' => '5551113344',
                'email' => 'ape.valido@bbs.com',
                'direccion' => 'Calle 1',
                'experiencia' => 2,
            ]);
        $responseValido->assertRedirect(route('admin.trabajadores.index'));
        $this->assertDatabaseHas('trabajadores', [
            'nombre' => 'Carlos',
            'apellidos' => 'Gómez Ruiz',
            'email' => 'ape.valido@bbs.com',
        ]);
    }
}
