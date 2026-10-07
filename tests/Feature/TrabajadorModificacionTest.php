<?php

namespace Tests\Feature;

use App\Models\Trabajador;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TrabajadorModificacionTest extends TestCase
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
     * Comprueba que un administrador autenticado puede actualizar los datos de un trabajador.
     */
    public function test_un_administrador_autenticado_puede_actualizar_la_informacion_de_un_trabajador(): void
    {
        $trabajador = Trabajador::create([
            'nombre' => 'Roberto',
            'apellidos' => 'Hernández Solís',
            'telefono' => '5551239870',
            'email' => 'roberto.barber@bbs.com',
            'direccion' => 'Calle 50 #100',
            'experiencia' => 3,
            'activo' => true,
        ]);

        $updateData = [
            'nombre' => 'Roberto Carlos',
            'apellidos' => 'Hernández Pérez',
            'telefono' => '5551239870',
            'email' => 'roberto.carlos@bbs.com',
            'direccion' => 'Av. Revolución #200, Benito Juárez',
            'experiencia' => 5,
            'status' => 'active',
        ];

        $response = $this->actingAs($this->adminUser)
            ->putJson(route('admin.trabajadores.update', $trabajador), $updateData);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Información del trabajador actualizada exitosamente.',
        ]);

        $this->assertDatabaseHas('trabajadores', [
            'id' => $trabajador->id,
            'nombre' => 'Roberto Carlos',
            'apellidos' => 'Hernández Pérez',
            'email' => 'roberto.carlos@bbs.com',
            'direccion' => 'Av. Revolución #200, Benito Juárez',
            'experiencia' => 5,
            'activo' => true,
        ]);
    }

    /**
     * Comprueba que actualizar la información permite mantener el mismo teléfono y correo sin error de duplicado.
     */
    public function test_actualizar_permite_mantener_el_mismo_telefono_y_correo_del_trabajador(): void
    {
        $trabajador = Trabajador::create([
            'nombre' => 'Mauricio',
            'apellidos' => 'Peña Lara',
            'telefono' => '5559876543',
            'email' => 'mauricio.fade@bbs.com',
            'direccion' => 'Calle 21 #45',
            'experiencia' => 4,
            'activo' => true,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->putJson(route('admin.trabajadores.update', $trabajador), [
                'nombre' => 'Mauricio Alberto',
                'apellidos' => 'Peña Lara',
                'telefono' => '5559876543',
                'email' => 'mauricio.fade@bbs.com',
                'direccion' => 'Calle 21 #45 Modificada',
                'experiencia' => 6,
                'status' => 'active',
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('trabajadores', [
            'id' => $trabajador->id,
            'nombre' => 'Mauricio Alberto',
            'experiencia' => 6,
        ]);
    }

    /**
     * Comprueba que no permite actualizar con un teléfono que ya pertenece a otro trabajador.
     */
    public function test_no_permite_actualizar_con_un_telefono_que_pertenece_a_otro_trabajador(): void
    {
        $trabajador1 = Trabajador::create([
            'nombre' => 'Trabajador',
            'apellidos' => 'Uno',
            'telefono' => '5551111111',
            'email' => 'uno@bbs.com',
            'direccion' => 'Calle 1',
            'experiencia' => 2,
            'activo' => true,
        ]);

        $trabajador2 = Trabajador::create([
            'nombre' => 'Trabajador',
            'apellidos' => 'Dos',
            'telefono' => '5552222222',
            'email' => 'dos@bbs.com',
            'direccion' => 'Calle 2',
            'experiencia' => 3,
            'activo' => true,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->putJson(route('admin.trabajadores.update', $trabajador2), [
                'nombre' => 'Trabajador',
                'apellidos' => 'Dos Modificado',
                'telefono' => '5551111111', // Teléfono de trabajador1
                'email' => 'dos.nuevo@bbs.com',
                'direccion' => 'Calle 2',
                'experiencia' => 3,
                'status' => 'active',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['telefono']);
    }

    /**
     * Comprueba que no permite actualizar con un correo que ya pertenece a otro trabajador.
     */
    public function test_no_permite_actualizar_con_un_correo_que_pertenece_a_otro_trabajador(): void
    {
        $trabajador1 = Trabajador::create([
            'nombre' => 'Trabajador',
            'apellidos' => 'Uno',
            'telefono' => '5551111111',
            'email' => 'uno@bbs.com',
            'direccion' => 'Calle 1',
            'experiencia' => 2,
            'activo' => true,
        ]);

        $trabajador2 = Trabajador::create([
            'nombre' => 'Trabajador',
            'apellidos' => 'Dos',
            'telefono' => '5552222222',
            'email' => 'dos@bbs.com',
            'direccion' => 'Calle 2',
            'experiencia' => 3,
            'activo' => true,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->putJson(route('admin.trabajadores.update', $trabajador2), [
                'nombre' => 'Trabajador',
                'apellidos' => 'Dos Modificado',
                'telefono' => '5552222222',
                'email' => 'uno@bbs.com', // Email de trabajador1
                'direccion' => 'Calle 2',
                'experiencia' => 3,
                'status' => 'active',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    /**
     * Comprueba que el teléfono debe tener exactamente 10 dígitos numéricos al modificar.
     */
    public function test_el_telefono_debe_tener_exactamente_10_digitos_al_modificar(): void
    {
        $trabajador = Trabajador::create([
            'nombre' => 'Tel',
            'apellidos' => 'Test',
            'telefono' => '5550001111',
            'email' => 'tel@bbs.com',
            'direccion' => 'Calle 10',
            'experiencia' => 2,
            'activo' => true,
        ]);

        // Menos de 10 dígitos
        $response1 = $this->actingAs($this->adminUser)
            ->putJson(route('admin.trabajadores.update', $trabajador), [
                'nombre' => 'Tel',
                'apellidos' => 'Test',
                'telefono' => '555123',
                'email' => 'tel@bbs.com',
                'direccion' => 'Calle 10',
                'experiencia' => 2,
                'status' => 'active',
            ]);
        $response1->assertStatus(422);
        $response1->assertJsonValidationErrors(['telefono']);

        // Con caracteres alfabéticos
        $response2 = $this->actingAs($this->adminUser)
            ->putJson(route('admin.trabajadores.update', $trabajador), [
                'nombre' => 'Tel',
                'apellidos' => 'Test',
                'telefono' => '555123456A',
                'email' => 'tel@bbs.com',
                'direccion' => 'Calle 10',
                'experiencia' => 2,
                'status' => 'active',
            ]);
        $response2->assertStatus(422);
        $response2->assertJsonValidationErrors(['telefono']);
    }

    /**
     * Comprueba que el correo debe tener formato válido con dominio y extensión al modificar.
     */
    public function test_el_correo_debe_tener_formato_valido_al_modificar(): void
    {
        $trabajador = Trabajador::create([
            'nombre' => 'Email',
            'apellidos' => 'Test',
            'telefono' => '5550002222',
            'email' => 'emailtest@bbs.com',
            'direccion' => 'Calle 10',
            'experiencia' => 2,
            'activo' => true,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->putJson(route('admin.trabajadores.update', $trabajador), [
                'nombre' => 'Email',
                'apellidos' => 'Test',
                'telefono' => '5550002222',
                'email' => 'email-invalido-sin-arroba',
                'direccion' => 'Calle 10',
                'experiencia' => 2,
                'status' => 'active',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    /**
     * Comprueba que la experiencia no puede ser negativa al modificar.
     */
    public function test_la_experiencia_no_puede_ser_negativa_al_modificar(): void
    {
        $trabajador = Trabajador::create([
            'nombre' => 'Exp',
            'apellidos' => 'Test',
            'telefono' => '5550003333',
            'email' => 'exptest@bbs.com',
            'direccion' => 'Calle 10',
            'experiencia' => 2,
            'activo' => true,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->putJson(route('admin.trabajadores.update', $trabajador), [
                'nombre' => 'Exp',
                'apellidos' => 'Test',
                'telefono' => '5550003333',
                'email' => 'exptest@bbs.com',
                'direccion' => 'Calle 10',
                'experiencia' => -4,
                'status' => 'active',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['experiencia']);
    }

    /**
     * Comprueba que se puede actualizar la fotografía y se borra la anterior del storage.
     */
    public function test_se_puede_actualizar_la_fotografia_del_trabajador_y_elimina_la_anterior(): void
    {
        Storage::fake('public');

        $fotoAnterior = UploadedFile::fake()->image('anterior.jpg');
        $rutaAnterior = $fotoAnterior->store('trabajadores', 'public');

        $trabajador = Trabajador::create([
            'nombre' => 'Foto',
            'apellidos' => 'Worker',
            'telefono' => '5550004444',
            'email' => 'foto@bbs.com',
            'direccion' => 'Calle Foto 1',
            'fotografia' => $rutaAnterior,
            'experiencia' => 3,
            'activo' => true,
        ]);

        Storage::disk('public')->assertExists($rutaAnterior);

        $nuevaFoto = UploadedFile::fake()->image('nueva.png');

        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.trabajadores.update', $trabajador), [
                'nombre' => 'Foto',
                'apellidos' => 'Worker',
                'telefono' => '5550004444',
                'email' => 'foto@bbs.com',
                'direccion' => 'Calle Foto 1',
                'experiencia' => 3,
                'status' => 'active',
                'fotografia' => $nuevaFoto,
            ], [
                'Accept' => 'application/json',
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        $response->assertStatus(200);

        // La foto anterior se debió eliminar
        Storage::disk('public')->assertMissing($rutaAnterior);

        // La nueva foto debe existir
        $trabajadorActualizado = $trabajador->fresh();
        $this->assertNotNull($trabajadorActualizado->fotografia);
        $this->assertNotEquals($rutaAnterior, $trabajadorActualizado->fotografia);
        Storage::disk('public')->assertExists($trabajadorActualizado->fotografia);
    }

    /**
     * Comprueba que se puede cambiar el estado de actividad (activo e inactivo).
     */
    public function test_se_puede_cambiar_el_estado_activo_e_inactivo_al_modificar(): void
    {
        $trabajador = Trabajador::create([
            'nombre' => 'Estado',
            'apellidos' => 'Test',
            'telefono' => '5550005555',
            'email' => 'estado@bbs.com',
            'direccion' => 'Calle Estado 1',
            'experiencia' => 1,
            'activo' => true,
        ]);

        // Cambiar a inactivo
        $response = $this->actingAs($this->adminUser)
            ->putJson(route('admin.trabajadores.update', $trabajador), [
                'nombre' => 'Estado',
                'apellidos' => 'Test',
                'telefono' => '5550005555',
                'email' => 'estado@bbs.com',
                'direccion' => 'Calle Estado 1',
                'experiencia' => 1,
                'status' => 'inactive',
            ]);

        $response->assertStatus(200);
        $this->assertFalse($trabajador->fresh()->activo);

        // Cambiar de regreso a activo
        $response2 = $this->actingAs($this->adminUser)
            ->putJson(route('admin.trabajadores.update', $trabajador), [
                'nombre' => 'Estado',
                'apellidos' => 'Test',
                'telefono' => '5550005555',
                'email' => 'estado@bbs.com',
                'direccion' => 'Calle Estado 1',
                'experiencia' => 1,
                'status' => 'active',
            ]);

        $response2->assertStatus(200);
        $this->assertTrue($trabajador->fresh()->activo);
    }

    /**
     * Comprueba que un usuario invitado no puede modificar un trabajador.
     */
    public function test_usuario_no_autenticado_no_puede_modificar_trabajador(): void
    {
        $trabajador = Trabajador::create([
            'nombre' => 'Guest',
            'apellidos' => 'Worker',
            'telefono' => '5550006666',
            'email' => 'guest@bbs.com',
            'direccion' => 'Calle 99',
            'experiencia' => 1,
            'activo' => true,
        ]);

        $response = $this->putJson(route('admin.trabajadores.update', $trabajador), [
            'nombre' => 'Hack',
            'apellidos' => 'Worker',
            'telefono' => '5550006666',
            'email' => 'guest@bbs.com',
            'direccion' => 'Calle 99',
            'experiencia' => 1,
            'status' => 'active',
        ]);

        $response->assertStatus(401);
    }
}
