<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\Trabajador;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrabajadorServiciosTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected Service $servicio1;
    protected Service $servicio2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\TestUsersSeeder::class);
        $this->adminUser = User::where('role_id', 1)->first();

        $this->servicio1 = Service::create([
            'name' => 'Corte Clásico',
            'description' => 'Corte clásico con tijera y máquina.',
            'price' => 250.00,
            'duration_minutes' => 45,
            'category' => 'barberia',
            'is_active' => true,
        ]);

        $this->servicio2 = Service::create([
            'name' => 'Corte Fade',
            'description' => 'Degradado limpio a navaja.',
            'price' => 280.00,
            'duration_minutes' => 50,
            'category' => 'barberia',
            'is_active' => true,
        ]);
    }

    /**
     * Comprueba que un administrador puede consultar los IDs de servicios asignados a un trabajador.
     */
    public function test_un_administrador_puede_obtener_los_servicios_asociados_a_un_trabajador(): void
    {
        $trabajador = Trabajador::create([
            'nombre' => 'Javier',
            'apellidos' => 'Solís',
            'telefono' => '5551112233',
            'email' => 'javier@bbs.com',
            'direccion' => 'Calle 50',
            'experiencia' => 5,
            'activo' => true,
        ]);

        $trabajador->servicios()->attach([$this->servicio1->id]);

        $response = $this->actingAs($this->adminUser)
            ->getJson(route('admin.trabajadores.services', $trabajador));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'trabajador_id' => $trabajador->id,
            'service_ids' => [$this->servicio1->id],
        ]);
    }

    /**
     * Comprueba que un administrador puede asociar servicios exitosamente mediante sync.
     */
    public function test_un_administrador_puede_asociar_servicios_a_un_trabajador(): void
    {
        $trabajador = Trabajador::create([
            'nombre' => 'Marcos',
            'apellidos' => 'Canul',
            'telefono' => '5553334455',
            'email' => 'marcos@bbs.com',
            'direccion' => 'Calle 60',
            'experiencia' => 3,
            'activo' => true,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->postJson(route('admin.trabajadores.sync_services', $trabajador), [
                'servicios' => [$this->servicio1->id, $this->servicio2->id],
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Servicios asociados exitosamente al especialista.',
        ]);

        $this->assertDatabaseHas('servicio_trabajador', [
            'trabajador_id' => $trabajador->id,
            'service_id' => $this->servicio1->id,
        ]);

        $this->assertDatabaseHas('servicio_trabajador', [
            'trabajador_id' => $trabajador->id,
            'service_id' => $this->servicio2->id,
        ]);

        $this->assertEquals(2, $trabajador->fresh()->servicios()->count());
    }

    /**
     * Comprueba que se pueden desasociar servicios enviando una lista vacía.
     */
    public function test_se_pueden_desasociar_servicios_enviando_un_arreglo_vacio(): void
    {
        $trabajador = Trabajador::create([
            'nombre' => 'Luis',
            'apellidos' => 'Pérez',
            'telefono' => '5556667788',
            'email' => 'luis@bbs.com',
            'direccion' => 'Calle 70',
            'experiencia' => 2,
            'activo' => true,
        ]);

        $trabajador->servicios()->attach([$this->servicio1->id, $this->servicio2->id]);
        $this->assertEquals(2, $trabajador->servicios()->count());

        $response = $this->actingAs($this->adminUser)
            ->postJson(route('admin.trabajadores.sync_services', $trabajador), [
                'servicios' => [],
            ]);

        $response->assertStatus(200);
        $this->assertEquals(0, $trabajador->fresh()->servicios()->count());
        $this->assertDatabaseMissing('servicio_trabajador', [
            'trabajador_id' => $trabajador->id,
        ]);
    }

    /**
     * Comprueba que no se permite asociar un servicio inexistente.
     */
    public function test_no_permite_asociar_un_servicio_inexistente(): void
    {
        $trabajador = Trabajador::create([
            'nombre' => 'Raúl',
            'apellidos' => 'Díaz',
            'telefono' => '5559990011',
            'email' => 'raul@bbs.com',
            'direccion' => 'Calle 80',
            'experiencia' => 4,
            'activo' => true,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->postJson(route('admin.trabajadores.sync_services', $trabajador), [
                'servicios' => [999999],
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['servicios.0']);
    }

    /**
     * Comprueba la relación bidireccional entre Trabajador y Service.
     */
    public function test_relacion_many_to_many_bidireccional_entre_trabajador_y_service(): void
    {
        $trabajador = Trabajador::create([
            'nombre' => 'Carlos',
            'apellidos' => 'Vela',
            'telefono' => '5558889900',
            'email' => 'carlos.vela@bbs.com',
            'direccion' => 'Av. Reforma',
            'experiencia' => 6,
            'activo' => true,
        ]);

        $trabajador->servicios()->attach($this->servicio1->id);

        $this->assertTrue($trabajador->servicios->contains($this->servicio1));
        $this->assertTrue($this->servicio1->trabajadores->contains($trabajador));
    }

    /**
     * Comprueba que la vista de trabajadores despliega los servicios asignados.
     */
    public function test_la_vista_de_trabajadores_despliega_los_servicios_asociados(): void
    {
        $trabajador = Trabajador::create([
            'nombre' => 'Emilio',
            'apellidos' => 'Azcárraga',
            'telefono' => '5554443322',
            'email' => 'emilio@bbs.com',
            'direccion' => 'Lomas',
            'experiencia' => 10,
            'activo' => true,
        ]);

        $trabajador->servicios()->attach([$this->servicio1->id]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.trabajadores.index'));

        $response->assertStatus(200);
        $response->assertSee('Corte Clásico');
    }

    /**
     * Comprueba que un usuario invitado no puede asociar servicios.
     */
    public function test_usuario_no_autenticado_no_puede_asociar_servicios(): void
    {
        $trabajador = Trabajador::create([
            'nombre' => 'Guest',
            'apellidos' => 'Worker',
            'telefono' => '5557776655',
            'email' => 'guest2@bbs.com',
            'direccion' => 'Calle 100',
            'experiencia' => 1,
            'activo' => true,
        ]);

        $response = $this->postJson(route('admin.trabajadores.sync_services', $trabajador), [
            'servicios' => [$this->servicio1->id],
        ]);

        $response->assertStatus(401);
    }

    /**
     * Comprueba que si un servicio se desactiva, deja de aparecer como asignado en la vista de trabajadores.
     */
    public function test_un_servicio_desactivado_deja_de_aparecer_en_la_vista_de_trabajadores(): void
    {
        $trabajador = Trabajador::create([
            'nombre' => 'Andrés',
            'apellidos' => 'López',
            'telefono' => '5551234567',
            'email' => 'andres@bbs.com',
            'direccion' => 'Av. Principal 123',
            'experiencia' => 4,
            'activo' => true,
        ]);

        $trabajador->servicios()->attach([$this->servicio1->id]);

        // Mientras está activo, se visualiza en la vista
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.trabajadores.index'));
        $response->assertStatus(200);
        $response->assertSee('Corte Clásico');

        // Desactivamos el servicio
        $this->servicio1->update(['is_active' => false]);

        // Ahora no debe figurar en la vista como servicio asignado
        $responseAfter = $this->actingAs($this->adminUser)
            ->get(route('admin.trabajadores.index'));
        $responseAfter->assertStatus(200);
        $responseAfter->assertSee('Sin servicios asignados');
        $responseAfter->assertDontSee('<span class="chip-service">Corte Clásico</span>', false);
    }

    /**
     * Comprueba que la API getServices omite servicios que han sido desactivados.
     */
    public function test_la_api_de_servicios_del_trabajador_omite_servicios_inactivos(): void
    {
        $trabajador = Trabajador::create([
            'nombre' => 'Rodrigo',
            'apellidos' => 'Herrera',
            'telefono' => '5559876543',
            'email' => 'rodrigo@bbs.com',
            'direccion' => 'Calle Reforma 45',
            'experiencia' => 2,
            'activo' => true,
        ]);

        $trabajador->servicios()->attach([$this->servicio1->id, $this->servicio2->id]);

        // Desactivamos el servicio 1
        $this->servicio1->update(['is_active' => false]);

        $response = $this->actingAs($this->adminUser)
            ->getJson(route('admin.trabajadores.services', $trabajador));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'trabajador_id' => $trabajador->id,
            'service_ids' => [$this->servicio2->id],
        ]);
        $response->assertJsonMissing(['id' => $this->servicio1->id]);
    }

    /**
     * Comprueba que al reactivar el servicio, vuelve a aparecer en el trabajador automáticamente.
     */
    public function test_al_reactivar_un_servicio_vuelve_a_mostrarse_en_el_trabajador_automaticamente(): void
    {
        $trabajador = Trabajador::create([
            'nombre' => 'Gabriel',
            'apellidos' => 'Méndez',
            'telefono' => '5554321098',
            'email' => 'gabriel@bbs.com',
            'direccion' => 'Calle Juárez 88',
            'experiencia' => 5,
            'activo' => true,
        ]);

        $trabajador->servicios()->attach([$this->servicio1->id]);

        // Desactivamos
        $this->servicio1->update(['is_active' => false]);
        $response = $this->actingAs($this->adminUser)
            ->getJson(route('admin.trabajadores.services', $trabajador));
        $this->assertEmpty($response->json('service_ids'));

        // Reactivamos
        $this->servicio1->update(['is_active' => true]);
        $responseReactivated = $this->actingAs($this->adminUser)
            ->getJson(route('admin.trabajadores.services', $trabajador));
        $this->assertEquals([$this->servicio1->id], $responseReactivated->json('service_ids'));
    }

    /**
     * Comprueba que sincronizar servicios preserva en la base de datos las asociaciones inactivas.
     */
    public function test_sincronizacion_preserva_servicios_inactivos_previamente_asociados(): void
    {
        $trabajador = Trabajador::create([
            'nombre' => 'Federico',
            'apellidos' => 'Ruiz',
            'telefono' => '5558765432',
            'email' => 'federico@bbs.com',
            'direccion' => 'Calle Hidalgo 12',
            'experiencia' => 3,
            'activo' => true,
        ]);

        // Asociar servicio 1
        $trabajador->servicios()->attach([$this->servicio1->id]);

        // Desactivar servicio 1
        $this->servicio1->update(['is_active' => false]);

        // Admin asocia servicio 2 desde la interfaz (donde solo ve servicios activos)
        $response = $this->actingAs($this->adminUser)
            ->postJson(route('admin.trabajadores.sync_services', $trabajador), [
                'servicios' => [$this->servicio2->id],
            ]);

        $response->assertStatus(200);

        // En la BD debe seguir teniendo ambos (el inactivo preservado y el nuevo activo)
        $this->assertDatabaseHas('servicio_trabajador', [
            'trabajador_id' => $trabajador->id,
            'service_id' => $this->servicio1->id,
        ]);
        $this->assertDatabaseHas('servicio_trabajador', [
            'trabajador_id' => $trabajador->id,
            'service_id' => $this->servicio2->id,
        ]);

        // Pero en la respuesta y vistas solo se reporta el activo (servicio 2)
        $response->assertJson([
            'success' => true,
            'data' => [
                'servicios_count' => 1,
            ],
        ]);
    }
}
