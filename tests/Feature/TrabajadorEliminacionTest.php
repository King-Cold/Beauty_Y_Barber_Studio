<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\Trabajador;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TrabajadorEliminacionTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected Service $servicio;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\TestUsersSeeder::class);
        $this->adminUser = User::where('role_id', 1)->first();

        $this->servicio = Service::create([
            'name' => 'Corte Degradado',
            'description' => 'Corte moderno degradado.',
            'price' => 200.00,
            'duration_minutes' => 35,
            'category' => 'barberia',
            'is_active' => true,
        ]);
    }

    /**
     * Comprueba que un administrador autenticado puede eliminar a un trabajador exitosamente vía JSON.
     */
    public function test_un_administrador_autenticado_puede_eliminar_un_trabajador_exitosamente(): void
    {
        $trabajador = Trabajador::create([
            'nombre' => 'Ernesto',
            'apellidos' => 'Zedillo',
            'telefono' => '5551239876',
            'email' => 'ernesto@bbs.com',
            'direccion' => 'Av. Insurgentes 10',
            'experiencia' => 4,
            'activo' => true,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->deleteJson(route('admin.trabajadores.destroy', $trabajador));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'id' => $trabajador->id,
        ]);

        $this->assertDatabaseMissing('trabajadores', [
            'id' => $trabajador->id,
        ]);
    }

    /**
     * Comprueba que al eliminar un trabajador se borra su fotografía física del almacenamiento.
     */
    public function test_eliminacion_elimina_la_fotografia_del_disco_de_almacenamiento(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('barbero.jpg', 300, 300);
        $path = $file->store('trabajadores', 'public');

        $trabajador = Trabajador::create([
            'nombre' => 'Gonzalo',
            'apellidos' => 'Pineda',
            'telefono' => '5557766554',
            'email' => 'gonzalo@bbs.com',
            'direccion' => 'Calle 55',
            'fotografia' => $path,
            'experiencia' => 3,
            'activo' => true,
        ]);

        Storage::disk('public')->assertExists($path);

        $response = $this->actingAs($this->adminUser)
            ->deleteJson(route('admin.trabajadores.destroy', $trabajador));

        $response->assertStatus(200);
        Storage::disk('public')->assertMissing($path);
        $this->assertDatabaseMissing('trabajadores', ['id' => $trabajador->id]);
    }

    /**
     * Comprueba que al eliminar un trabajador se desvinculan limpiamente sus servicios asociados en la tabla pivote.
     */
    public function test_eliminacion_desvincula_servicios_asociados_limpiamente(): void
    {
        $trabajador = Trabajador::create([
            'nombre' => 'Vicente',
            'apellidos' => 'Fox',
            'telefono' => '5553322110',
            'email' => 'vicente@bbs.com',
            'direccion' => 'Rancho San Cristóbal',
            'experiencia' => 8,
            'activo' => true,
        ]);

        $trabajador->servicios()->attach([$this->servicio->id]);

        $this->assertDatabaseHas('servicio_trabajador', [
            'trabajador_id' => $trabajador->id,
            'service_id' => $this->servicio->id,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->deleteJson(route('admin.trabajadores.destroy', $trabajador));

        $response->assertStatus(200);

        $this->assertDatabaseMissing('trabajadores', [
            'id' => $trabajador->id,
        ]);

        $this->assertDatabaseMissing('servicio_trabajador', [
            'trabajador_id' => $trabajador->id,
        ]);

        // El servicio original permanece intacto en la base de datos
        $this->assertDatabaseHas('services', [
            'id' => $this->servicio->id,
        ]);
    }

    /**
     * Comprueba que un usuario invitado o no autenticado no puede eliminar un trabajador.
     */
    public function test_usuario_no_autenticado_no_puede_eliminar_un_trabajador(): void
    {
        $trabajador = Trabajador::create([
            'nombre' => 'Inmune',
            'apellidos' => 'Seguro',
            'telefono' => '5559988776',
            'email' => 'inmune@bbs.com',
            'direccion' => 'Calle Protegida',
            'experiencia' => 2,
            'activo' => true,
        ]);

        $response = $this->deleteJson(route('admin.trabajadores.destroy', $trabajador));

        $response->assertStatus(401);
        $this->assertDatabaseHas('trabajadores', ['id' => $trabajador->id]);
    }

    /**
     * Comprueba que intentar eliminar un trabajador inexistente retorna error 404.
     */
    public function test_intentar_eliminar_trabajador_inexistente_retorna_404(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->deleteJson('/admin/trabajadores/999999');

        $response->assertStatus(404);
    }

    /**
     * Comprueba que la vista de trabajadores renderiza la acción de eliminar en tarjetas y tabla.
     */
    public function test_la_vista_de_trabajadores_renderiza_accion_de_eliminar(): void
    {
        $trabajador = Trabajador::create([
            'nombre' => 'Alberto',
            'apellidos' => 'Aguilera',
            'telefono' => '5554433221',
            'email' => 'alberto@bbs.com',
            'direccion' => 'Cd. Juárez',
            'experiencia' => 12,
            'activo' => true,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.trabajadores.index'));

        $response->assertStatus(200);
        $response->assertSee("confirmDeleteWorker({$trabajador->id}");
        $response->assertSee("btn-action-delete");
    }
}
