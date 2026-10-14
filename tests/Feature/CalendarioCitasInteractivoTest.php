<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\Service;
use App\Models\Trabajador;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalendarioCitasInteractivoTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $recepcionistaUser;
    protected User $trabajadorUser;
    protected User $clienteUser;
    protected Trabajador $trabajador;
    protected Service $servicio;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\TestUsersSeeder::class);

        $this->adminUser = User::where('role_id', 1)->first();
        $this->recepcionistaUser = User::where('role_id', 2)->first();
        $this->trabajadorUser = User::where('role_id', 3)->first();
        $this->clienteUser = User::where('role_id', 4)->first();

        // Crear trabajador y servicio base
        $this->trabajador = Trabajador::create([
            'user_id' => $this->trabajadorUser->id,
            'direccion' => 'Beauty & Barber Studio - Estación 1',
            'experiencia' => 5,
            'activo' => true,
            'horario' => [
                'lunes' => ['inicio' => '09:00', 'fin' => '19:00', 'laboral' => true],
                'martes' => ['inicio' => '09:00', 'fin' => '19:00', 'laboral' => true],
                'miercoles' => ['inicio' => '09:00', 'fin' => '19:00', 'laboral' => true],
                'jueves' => ['inicio' => '09:00', 'fin' => '19:00', 'laboral' => true],
                'viernes' => ['inicio' => '09:00', 'fin' => '20:00', 'laboral' => true],
                'sabado' => ['inicio' => '09:00', 'fin' => '20:00', 'laboral' => true],
                'domingo' => ['laboral' => false],
            ]
        ]);

        $this->servicio = Service::create([
            'name' => 'Corte Degradado Barber',
            'category' => 'barberia',
            'duration_minutes' => 45,
            'price' => 280.00,
            'is_active' => true,
        ]);

        Cita::ensureTableExists();
    }

    /**
     * Criterio 1: Consulta de citas reales según la fecha y periodo seleccionado.
     */
    public function test_consulta_citas_segun_rango_de_fechas_recepcion(): void
    {
        $hoy = Carbon::today()->format('Y-m-d');
        $manana = Carbon::tomorrow()->format('Y-m-d');
        $proximoMes = Carbon::today()->addMonth()->format('Y-m-d');

        // Cita 1: Hoy
        Cita::create([
            'user_id' => $this->clienteUser->id,
            'trabajador_id' => $this->trabajador->id,
            'service_id' => $this->servicio->id,
            'fecha' => $hoy,
            'hora_inicio' => '10:00',
            'hora_fin' => '10:45',
            'duracion_minutos' => 45,
            'precio' => 280.00,
            'estado' => 'confirmada',
        ]);

        // Cita 2: Mañana
        Cita::create([
            'user_id' => $this->clienteUser->id,
            'trabajador_id' => $this->trabajador->id,
            'service_id' => $this->servicio->id,
            'fecha' => $manana,
            'hora_inicio' => '12:00',
            'hora_fin' => '12:45',
            'duracion_minutos' => 45,
            'precio' => 280.00,
            'estado' => 'confirmada',
        ]);

        // Cita 3: Próximo mes (fuera del rango consultado)
        Cita::create([
            'user_id' => $this->clienteUser->id,
            'trabajador_id' => $this->trabajador->id,
            'service_id' => $this->servicio->id,
            'fecha' => $proximoMes,
            'hora_inicio' => '15:00',
            'hora_fin' => '15:45',
            'duracion_minutos' => 45,
            'precio' => 280.00,
            'estado' => 'confirmada',
        ]);

        // Consultar solo hoy y mañana
        $response = $this->actingAs($this->recepcionistaUser)
            ->getJson(route('recepcion.calendario.eventos', [
                'start' => $hoy,
                'end' => $manana,
            ]));

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('total', 2);

        $eventos = $response->json('eventos');
        $this->assertCount(2, $eventos);
        $this->assertEquals($hoy, $eventos[0]['fecha']);
        $this->assertEquals($manana, $eventos[1]['fecha']);
    }

    /**
     * Criterio 1: Gestión correcta de periodos sin reservaciones.
     */
    public function test_periodos_sin_reservaciones_retornan_lista_vacia_exitosa(): void
    {
        $fechaSinCitas = Carbon::today()->addYears(2)->format('Y-m-d');

        $response = $this->actingAs($this->recepcionistaUser)
            ->getJson(route('recepcion.calendario.eventos', [
                'start' => $fechaSinCitas,
                'end' => $fechaSinCitas,
            ]));

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('total', 0)
            ->assertJsonPath('eventos', []);
    }

    /**
     * Criterio 2: Visualización de información completa en horarios correspondientes.
     */
    public function test_citas_incluyen_informacion_completa_y_horarios(): void
    {
        $hoy = Carbon::today()->format('Y-m-d');

        $cita = Cita::create([
            'user_id' => $this->clienteUser->id,
            'trabajador_id' => $this->trabajador->id,
            'service_id' => $this->servicio->id,
            'fecha' => $hoy,
            'hora_inicio' => '11:00',
            'hora_fin' => '11:45',
            'duracion_minutos' => 45,
            'precio' => 280.00,
            'estado' => 'confirmada',
            'notas' => 'Corte con desvanecido alto.',
            'observaciones' => 'Piel sensible.',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->getJson(route('admin.citas.detalle', $cita));

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('cita.id', $cita->id)
            ->assertJsonPath('cita.hora_inicio', '11:00')
            ->assertJsonPath('cita.hora_fin', '11:45')
            ->assertJsonPath('cita.duracion_minutos', 45)
            ->assertJsonPath('cita.precio', 280)
            ->assertJsonPath('cita.notas', 'Corte con desvanecido alto.')
            ->assertJsonPath('cita.observaciones', 'Piel sensible.')
            ->assertJsonPath('cita.cliente.id', $this->clienteUser->id)
            ->assertJsonPath('cita.trabajador.id', $this->trabajador->id)
            ->assertJsonPath('cita.servicio.id', $this->servicio->id);
    }

    /**
     * Criterio 4: Distinción visual de estados (confirmada, en_proceso/en_atencion, completada, cancelada, vencida).
     */
    public function test_distincion_visual_de_estados_incluyendo_vencidas(): void
    {
        $ayer = Carbon::yesterday()->format('Y-m-d');
        $hoy = Carbon::today()->format('Y-m-d');
        $manana = Carbon::tomorrow()->format('Y-m-d');

        // Cita 1: Ayer confirmada -> debe computarse visualmente como 'vencida'
        Cita::create([
            'user_id' => $this->clienteUser->id,
            'trabajador_id' => $this->trabajador->id,
            'service_id' => $this->servicio->id,
            'fecha' => $ayer,
            'hora_inicio' => '10:00',
            'hora_fin' => '10:45',
            'duracion_minutos' => 45,
            'precio' => 280.00,
            'estado' => 'confirmada',
        ]);

        // Cita 2: Hoy en atención
        Cita::create([
            'user_id' => $this->clienteUser->id,
            'trabajador_id' => $this->trabajador->id,
            'service_id' => $this->servicio->id,
            'fecha' => $hoy,
            'hora_inicio' => '12:00',
            'hora_fin' => '12:45',
            'duracion_minutos' => 45,
            'precio' => 280.00,
            'estado' => 'en_atencion',
        ]);

        // Cita 3: Mañana confirmada
        Cita::create([
            'user_id' => $this->clienteUser->id,
            'trabajador_id' => $this->trabajador->id,
            'service_id' => $this->servicio->id,
            'fecha' => $manana,
            'hora_inicio' => '15:00',
            'hora_fin' => '15:45',
            'duracion_minutos' => 45,
            'precio' => 280.00,
            'estado' => 'confirmada',
        ]);

        $response = $this->actingAs($this->recepcionistaUser)
            ->getJson(route('recepcion.calendario.eventos', [
                'start' => $ayer,
                'end' => $manana,
            ]));

        $response->assertOk();
        $eventos = $response->json('eventos');

        $citaAyer = collect($eventos)->firstWhere('fecha', $ayer);
        $this->assertEquals('vencida', $citaAyer['estado_visual']);

        $citaHoy = collect($eventos)->firstWhere('fecha', $hoy);
        $this->assertEquals('en_proceso', $citaHoy['estado_visual']);

        $citaManana = collect($eventos)->firstWhere('fecha', $manana);
        $this->assertEquals('confirmada', $citaManana['estado_visual']);
    }

    /**
     * Criterio 5: Actualización del calendario y backend tras modificar estados de citas.
     */
    public function test_modificacion_y_persistencia_de_estado_de_cita(): void
    {
        $hoy = Carbon::today()->format('Y-m-d');

        $cita = Cita::create([
            'user_id' => $this->clienteUser->id,
            'trabajador_id' => $this->trabajador->id,
            'service_id' => $this->servicio->id,
            'fecha' => $hoy,
            'hora_inicio' => '14:00',
            'hora_fin' => '14:45',
            'duracion_minutos' => 45,
            'precio' => 280.00,
            'estado' => 'confirmada',
        ]);

        // Recepcionista cambia estado a 'completada' con observaciones
        $response = $this->actingAs($this->recepcionistaUser)
            ->patchJson(route('recepcion.citas.actualizar-estado', $cita), [
                'estado' => 'completada',
                'observaciones' => 'Cliente satisfecho con el peinado final.',
            ]);

        $response->assertOk()
            ->assertJsonPath('success', true);

        // Verificar en BD
        $cita->refresh();
        $this->assertEquals('completada', $cita->estado);
        $this->assertEquals('Cliente satisfecho con el peinado final.', $cita->observaciones);

        // Administrador cancela la cita
        $responseAdmin = $this->actingAs($this->adminUser)
            ->patchJson(route('admin.citas.actualizar-estado', $cita), [
                'estado' => 'cancelada',
                'observaciones' => 'Cancelación por reajuste administrativo.',
            ]);

        $responseAdmin->assertOk()
            ->assertJsonPath('success', true);

        $cita->refresh();
        $this->assertEquals('cancelada', $cita->estado);
    }

    /**
     * Criterio 6: Control de acceso según roles y permisos.
     */
    public function test_permisos_y_autorizacion_de_rutas_de_calendario(): void
    {
        // 1. Recepcionista puede ver calendario y gestión de citas de recepción
        $this->actingAs($this->recepcionistaUser)
            ->get(route('recepcion.calendario'))
            ->assertOk();

        $this->actingAs($this->recepcionistaUser)
            ->get(route('recepcion.citas'))
            ->assertOk();

        // 2. Administrador puede ver calendario de admin y calendario de recepción
        $this->actingAs($this->adminUser)
            ->get(route('admin.citas'))
            ->assertOk();

        $this->actingAs($this->adminUser)
            ->get(route('admin.calendario'))
            ->assertOk();

        $this->actingAs($this->adminUser)
            ->get(route('recepcion.calendario'))
            ->assertOk();

        // 3. Cliente (rol 4) NO puede acceder a rutas de administración ni recepción
        $this->actingAs($this->clienteUser)
            ->get(route('admin.citas'))
            ->assertStatus(403);

        $this->actingAs($this->clienteUser)
            ->get(route('recepcion.calendario'))
            ->assertStatus(403);

        // 4. Trabajador (rol 3) NO puede acceder a rutas de administración ni recepción
        $this->actingAs($this->trabajadorUser)
            ->get(route('admin.citas'))
            ->assertStatus(403);

        $this->actingAs($this->trabajadorUser)
            ->get(route('recepcion.calendario'))
            ->assertStatus(403);

        // 5. Invitado no autenticado es redirigido al login
        $this->post('/logout');
        $this->get(route('admin.citas'))
            ->assertRedirect(route('login'));

        $this->get(route('recepcion.calendario'))
            ->assertRedirect(route('login'));
    }
}
