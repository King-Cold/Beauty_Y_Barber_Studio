<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminServiceDurationTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\TestUsersSeeder::class);
        $this->adminUser = User::where('role_id', 1)->first();
        Storage::fake('public');
    }

    /**
     * Test que la vista de administración renderiza las opciones predefinidas de duración.
     */
    public function test_admin_services_view_contains_duration_options(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.services'));

        $response->assertStatus(200);
        $response->assertSee('15 minutos');
        $response->assertSee('30 minutos');
        $response->assertSee('45 minutos');
        $response->assertSee('60 minutos');
    }

    /**
     * Test que permite registrar un servicio con cada una de las duraciones permitidas (15, 30, 45, 60).
     */
    public function test_can_register_service_with_allowed_durations(): void
    {
        $allowedDurations = [15, 30, 45, 60];

        foreach ($allowedDurations as $index => $duration) {
            $response = $this->actingAs($this->adminUser)->post(route('admin.services.store'), [
                'name' => "Servicio Test {$duration} {$index}",
                'description' => "Descripción para servicio con duración de {$duration} minutos.",
                'price' => 150.00,
                'duration_minutes' => $duration,
                'category' => 'barberia',
                'image' => UploadedFile::fake()->image("service_{$duration}.jpg"),
            ]);

            $response->assertSessionHasNoErrors();
            $response->assertRedirect(route('admin.services'));

            $this->assertDatabaseHas('services', [
                'name' => "Servicio Test {$duration} {$index}",
                'duration_minutes' => $duration,
            ]);
        }
    }

    /**
     * Test que rechaza el registro si la duración no es 15, 30, 45 o 60 (ej. 23, 42).
     */
    public function test_rejects_service_registration_with_invalid_durations(): void
    {
        $invalidDurations = [23, 42, 10, 50, 90, 0, -15];

        foreach ($invalidDurations as $duration) {
            $response = $this->actingAs($this->adminUser)->post(route('admin.services.store'), [
                'name' => "Servicio Invalido {$duration}",
                'description' => "Descripción con duración no permitida.",
                'price' => 200.00,
                'duration_minutes' => $duration,
                'category' => 'barberia',
                'image' => UploadedFile::fake()->image("invalid.jpg"),
            ]);

            $response->assertSessionHasErrors(['duration_minutes']);
            $this->assertDatabaseMissing('services', [
                'name' => "Servicio Invalido {$duration}",
            ]);
        }
    }

    /**
     * Test que permite editar un servicio con cada una de las duraciones permitidas.
     */
    public function test_can_update_service_with_allowed_durations(): void
    {
        $service = Service::create([
            'name' => 'Servicio Edicion',
            'description' => 'Servicio para probar edicion.',
            'price' => 300.00,
            'duration_minutes' => 30,
            'category' => 'estetica',
            'is_active' => true,
        ]);

        foreach ([15, 45, 60, 30] as $newDuration) {
            $response = $this->actingAs($this->adminUser)->put(route('admin.services.update', $service->id), [
                'name' => 'Servicio Edicion',
                'description' => 'Servicio para probar edicion actualizada.',
                'price' => 320.00,
                'duration_minutes' => $newDuration,
                'category' => 'estetica',
            ]);

            $response->assertSessionHasNoErrors();
            $response->assertRedirect(route('admin.services'));

            $this->assertDatabaseHas('services', [
                'id' => $service->id,
                'duration_minutes' => $newDuration,
            ]);
        }
    }

    /**
     * Test que rechaza la edición si la duración no es 15, 30, 45 o 60 (ej. 23, 42).
     */
    public function test_rejects_service_update_with_invalid_durations(): void
    {
        $service = Service::create([
            'name' => 'Servicio Test Rechazo',
            'description' => 'Servicio base.',
            'price' => 200.00,
            'duration_minutes' => 30,
            'category' => 'barberia',
            'is_active' => true,
        ]);

        foreach ([23, 42, 75, 100] as $invalidDuration) {
            $response = $this->actingAs($this->adminUser)->put(route('admin.services.update', $service->id), [
                'name' => 'Servicio Test Rechazo',
                'description' => 'Intentando poner duracion invalida.',
                'price' => 200.00,
                'duration_minutes' => $invalidDuration,
                'category' => 'barberia',
            ]);

            $response->assertSessionHasErrors(['duration_minutes']);

            // La duración en BD debe permanecer intacta en 30
            $this->assertDatabaseHas('services', [
                'id' => $service->id,
                'duration_minutes' => 30,
            ]);
        }
    }

    /**
     * Test que los servicios existentes con duraciones no estándar se mantienen intactos en la BD.
     */
    public function test_legacy_services_with_non_standard_durations_are_not_modified_silently(): void
    {
        $legacyService = Service::create([
            'name' => 'Servicio Antiguo Especial',
            'description' => 'Servicio que tenia 50 minutos desde antes.',
            'price' => 180.00,
            'duration_minutes' => 50,
            'category' => 'barberia',
            'is_active' => true,
        ]);

        // Cargar la vista no debe alterar el registro
        $response = $this->actingAs($this->adminUser)->get(route('admin.services'));
        $response->assertStatus(200);

        $fresh = $legacyService->fresh();
        $this->assertEquals(50, $fresh->duration_minutes);
    }
}
