<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\Service;
use App\Models\Trabajador;
use App\Models\User;
use App\Services\IngresosService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Subtarea24_4_VisualizacionCorteCajaTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $recepcionistaUser;
    protected User $clienteUser;
    protected Trabajador $trabajador;
    protected Service $servicio;
    protected IngresosService $ingresosService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\TestUsersSeeder::class);

        $this->adminUser = User::where('role_id', 1)->first();
        $this->recepcionistaUser = User::where('role_id', 2)->first();
        $this->clienteUser = User::where('role_id', 4)->first();
        $trabajadorUser = User::where('role_id', 3)->first();

        $this->trabajador = Trabajador::create([
            'user_id' => $trabajadorUser->id,
            'direccion' => 'Calle Estudio 123',
            'experiencia' => 5,
            'activo' => true,
            'horario' => [
                'lunes' => ['entrada' => '09:00', 'salida' => '19:00'],
                'martes' => ['entrada' => '09:00', 'salida' => '19:00'],
            ],
        ]);

        $this->servicio = Service::create([
            'name' => 'Corte Premium Test',
            'duration_minutes' => 30,
            'price' => 350.00,
            'category' => 'barberia',
            'is_active' => true,
        ]);

        $this->trabajador->servicios()->attach($this->servicio->id);
        $this->ingresosService = app(IngresosService::class);
    }

    /**
     * CA-24.4.1: La vista de recepción muestra el panel de Corte de Caja con las tarjetas financieras.
     */
    public function test_recepcion_citas_muestra_panel_de_corte_de_caja_con_tarjetas_de_ingresos(): void
    {
        $hoy = Carbon::now($this->ingresosService->getBusinessTimezone())->format('Y-m-d');

        // Cita pagada hoy ($350)
        Cita::create([
            'user_id' => $this->clienteUser->id,
            'service_id' => $this->servicio->id,
            'trabajador_id' => $this->trabajador->id,
            'fecha' => $hoy,
            'hora_inicio' => '11:00:00',
            'hora_fin' => '11:30:00',
            'duracion_minutos' => 30,
            'precio' => 350.00,
            'estado' => 'completada',
            'estado_pago' => 'pagado',
        ]);

        // Cita pendiente de cobro hoy ($350)
        Cita::create([
            'user_id' => $this->clienteUser->id,
            'service_id' => $this->servicio->id,
            'trabajador_id' => $this->trabajador->id,
            'fecha' => $hoy,
            'hora_inicio' => '12:00:00',
            'hora_fin' => '12:30:00',
            'duracion_minutos' => 30,
            'precio' => 350.00,
            'estado' => 'completada',
            'estado_pago' => 'pendiente',
        ]);

        $response = $this->actingAs($this->recepcionistaUser)
            ->get(route('recepcion.citas'));

        $response->assertStatus(200);

        // Verifica la presencia del contenedor del Corte de Caja
        $response->assertSee('Corte de Caja y Control de Cobros');
        $response->assertSee('Ingresos del Día');
        $response->assertSee('Ingresos del Mes');
        $response->assertDontSee('Cuentas por Cobrar');

        // Verifica formato estándar con MXN
        $response->assertSee('$350.00 MXN');

        // Verifica los contadores de soporte
        $response->assertSee('1</b> servicios cobrados hoy', false);
        $response->assertSee('1</b> servicios cobrados este mes', false);
    }

    /**
     * CA-24.4.2: La vista de citas de administración también muestra el panel de Corte de Caja (Modo Auditoría).
     */
    public function test_admin_citas_muestra_panel_de_corte_de_caja_con_tarjetas_de_ingresos(): void
    {
        $hoy = Carbon::now($this->ingresosService->getBusinessTimezone())->format('Y-m-d');

        // Cita pagada hoy ($350)
        Cita::create([
            'user_id' => $this->clienteUser->id,
            'service_id' => $this->servicio->id,
            'trabajador_id' => $this->trabajador->id,
            'fecha' => $hoy,
            'hora_inicio' => '15:00:00',
            'hora_fin' => '15:30:00',
            'duracion_minutos' => 30,
            'precio' => 350.00,
            'estado' => 'completada',
            'estado_pago' => 'pagado',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.citas'));

        $response->assertStatus(200);

        // Verifica la presencia del contenedor del Corte de Caja en admin
        $response->assertSee('adminCorteCajaContainer');
        $response->assertSee('Corte de Caja y Control de Cobros');
        $response->assertSee('Ingresos del Día');
        $response->assertSee('Ingresos del Mes');
        $response->assertDontSee('Cuentas por Cobrar');

        // Verifica formato estándar con MXN
        $response->assertSee('$350.00 MXN');

        // Verifica contadores de soporte
        $response->assertSee('1</b> servicios cobrados hoy', false);
        $response->assertSee('1</b> servicios cobrados este mes', false);
    }
}

