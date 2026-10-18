<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Service;
use App\Models\Trabajador;
use App\Models\Cita;
use App\Models\FechaEspecial;
use App\Models\HorarioSucursal;
use App\Services\DisponibilidadService;
use App\Services\CitaCalendarioService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class CalendarioDiasNoLaborablesTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $recepcionistaUser;
    protected User $clienteUser;
    protected User $trabajadorUser;
    protected Trabajador $trabajador;
    protected Service $servicio;

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
            'name' => 'Admin',
            'apellidos' => 'Sistema',
            'email' => 'admin.test@bbs.com',
            'telefono' => '5551234567',
            'password' => Hash::make('Admin123!'),
            'role_id' => 1,
        ]);

        $this->recepcionistaUser = User::create([
            'name' => 'Recepcionista',
            'apellidos' => 'Turno',
            'email' => 'recepcion.test@bbs.com',
            'telefono' => '5551234568',
            'password' => Hash::make('Recep123!'),
            'role_id' => 2,
        ]);

        $this->clienteUser = User::create([
            'name' => 'Cliente',
            'apellidos' => 'Frecuente',
            'email' => 'cliente.test@bbs.com',
            'telefono' => '5551234569',
            'password' => Hash::make('Cliente123!'),
            'role_id' => 4,
        ]);

        $this->trabajadorUser = User::create([
            'name' => 'Roberto',
            'apellidos' => 'Sánchez',
            'email' => 'roberto.barber@bbs.com',
            'telefono' => '5551234570',
            'password' => Hash::make('Worker123!'),
            'role_id' => 3,
        ]);

        $this->trabajador = Trabajador::create([
            'user_id' => $this->trabajadorUser->id,
            'direccion' => 'Av. Insurgentes 123',
            'experiencia' => 5,
            'activo' => true,
            'horario' => [
                'lunes'     => ['entrada' => '08:00', 'salida' => '20:00'],
                'martes'    => ['entrada' => '08:00', 'salida' => '20:00'],
                'miercoles' => ['entrada' => '08:00', 'salida' => '20:00'],
                'jueves'    => ['entrada' => '08:00', 'salida' => '20:00'],
                'viernes'   => ['entrada' => '08:00', 'salida' => '20:00'],
                'sabado'    => ['entrada' => '08:00', 'salida' => '12:00'],
                'domingo'   => null,
            ],
        ]);

        $this->servicio = Service::create([
            'name' => 'Corte Caballero BBS',
            'price' => 250.00,
            'duration_minutes' => 30,
            'category' => 'barberia',
            'is_active' => true,
        ]);

        $this->trabajador->servicios()->attach($this->servicio->id);
    }

    /**
     * Prueba 1: La API de eventos del calendario incluye la configuración de horarios y fechas especiales.
     */
    public function test_api_eventos_calendario_incluye_horarios_de_sucursal_y_domingo_cerrado(): void
    {
        $response = $this->actingAs($this->recepcionistaUser)
            ->getJson(route('recepcion.calendario.eventos', [
                'start' => '2026-10-12',
                'end' => '2026-10-18',
            ]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'total',
            'eventos',
            'horarios_sucursal' => [
                'domingo',
                'lunes',
                'sabado',
            ],
            'fechas_especiales',
        ]);

        $horarios = $response->json('horarios_sucursal');
        $this->assertFalse($horarios['domingo']['abierto'], 'El domingo debe estar cerrado en los horarios retornados');
        $this->assertTrue($horarios['sabado']['abierto'], 'El sábado debe estar abierto');
        $this->assertEquals('08:00', $horarios['sabado']['apertura']);
        $this->assertEquals('12:00', $horarios['sabado']['cierre']);
    }

    /**
     * Prueba 2: La vista del calendario contiene los estilos y el texto exacto «Cerrado, no se labora».
     */
    public function test_calendario_vista_contiene_aviso_cerrado_no_se_labora_y_estilos(): void
    {
        $response = $this->actingAs($this->recepcionistaUser)
            ->get(route('recepcion.calendario'));

        $response->assertStatus(200);
        $response->assertSee('Cerrado, no se labora');
        $response->assertSee('cal-month-cell.is-closed', false);
        $response->assertSee('cal-week-column.is-closed', false);
        $response->assertSee('cal-week-closed-notice', false);
        $response->assertSee('cal-day-closed-card', false);

        // También verificar en el panel de administrador
        $responseAdmin = $this->actingAs($this->adminUser)
            ->get(route('admin.citas'));
        $responseAdmin->assertStatus(200);
        $responseAdmin->assertSee('Cerrado, no se labora');
    }

    /**
     * Prueba 3: HorarioSucursal::esFechaLaborable detecta correctamente domingos como no laborables.
     */
    public function test_horario_sucursal_es_fecha_laborable_detecta_domingo(): void
    {
        // 2026-10-18 es domingo
        $resDomingo = HorarioSucursal::esFechaLaborable('2026-10-18');
        $this->assertFalse($resDomingo['laborable']);
        $this->assertEquals('dia_no_laborable', $resDomingo['tipo']);
        $this->assertStringContainsString('Cerrado, no se labora', $resDomingo['mensaje']);

        // 2026-10-19 es lunes
        $resLunes = HorarioSucursal::esFechaLaborable('2026-10-19');
        $this->assertTrue($resLunes['laborable']);

        // 2026-10-17 es sábado
        $resSabado = HorarioSucursal::esFechaLaborable('2026-10-17');
        $this->assertTrue($resSabado['laborable']);
        $this->assertEquals('08:00', $resSabado['horario']['apertura']);
        $this->assertEquals('12:00', $resSabado['horario']['cierre']);
    }

    /**
     * Prueba 4: El servidor rechaza la validación de un horario en domingo.
     */
    public function test_servidor_rechaza_validacion_de_horario_en_domingo(): void
    {
        $disponibilidadService = app(DisponibilidadService::class);
        $val = $disponibilidadService->validarHorarioSeleccionado(
            $this->trabajador,
            '2026-10-18', // Domingo
            '10:00',
            $this->servicio
        );

        $this->assertFalse($val['disponible']);
        $this->assertEquals('dia_no_laborable', $val['motivo_tipo']);
        $this->assertStringContainsString('Cerrado, no se labora', $val['mensaje']);
    }

    /**
     * Prueba 5: El servidor rechaza el registro de una cita en domingo.
     */
    public function test_servidor_rechaza_registro_de_cita_en_domingo(): void
    {
        $disponibilidadService = app(DisponibilidadService::class);
        $resultado = $disponibilidadService->registrarCitaConcurrente(
            $this->trabajador,
            $this->servicio,
            $this->clienteUser,
            '2026-10-18', // Domingo
            '10:00'
        );

        $this->assertFalse($resultado['success']);
        $this->assertEquals(422, $resultado['status']);
        $this->assertEquals('dia_no_laborable', $resultado['motivo_tipo']);
        $this->assertStringContainsString('Cerrado, no se labora', $resultado['mensaje']);
    }

    /**
     * Prueba 6: CitaCalendarioService::editarCita rechaza reprogramar una cita a un domingo.
     */
    public function test_servidor_rechaza_editar_cita_hacia_un_domingo(): void
    {
        // Crear una cita válida en lunes
        $cita = Cita::create([
            'folio' => 'CITA-TEST-01',
            'user_id' => $this->clienteUser->id,
            'trabajador_id' => $this->trabajador->id,
            'service_id' => $this->servicio->id,
            'fecha' => '2026-10-19', // Lunes
            'hora_inicio' => '10:00',
            'hora_fin' => '10:30',
            'duracion_minutos' => 30,
            'precio' => 250.00,
            'estado' => 'confirmada',
        ]);

        $calendarioService = app(CitaCalendarioService::class);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Cerrado, no se labora');

        $calendarioService->editarCita($cita, [
            'fecha' => '2026-10-25', // Domingo siguiente
            'hora_inicio' => '10:00',
        ]);
    }

    /**
     * Prueba 7: Los días laborables y horarios vigentes funcionan normalmente (Lunes y Sábado en horario).
     */
    public function test_dias_laborables_y_sabado_permiten_agendar_en_horario_vigente(): void
    {
        $disponibilidadService = app(DisponibilidadService::class);

        // Lunes 10:00: disponible
        $valLunes = $disponibilidadService->validarHorarioSeleccionado(
            $this->trabajador,
            '2026-10-19',
            '10:00',
            $this->servicio
        );
        $this->assertTrue($valLunes['disponible']);

        // Sábado 10:00 (dentro de 08:00 a 12:00): disponible
        $valSabado = $disponibilidadService->validarHorarioSeleccionado(
            $this->trabajador,
            '2026-10-24',
            '10:00',
            $this->servicio
        );
        $this->assertTrue($valSabado['disponible']);

        // Sábado 12:00 o posterior: fuera del horario de atención de la sucursal (cierre 12:00)
        $valSabadoTarde = $disponibilidadService->validarHorarioSeleccionado(
            $this->trabajador,
            '2026-10-24',
            '13:00',
            $this->servicio
        );
        $this->assertFalse($valSabadoTarde['disponible']);
        $this->assertEquals('cierre_sucursal', $valSabadoTarde['motivo_tipo']);
    }

    /**
     * Prueba 8: Fechas especiales con cierre total también se comportan como no laborables.
     */
    public function test_fecha_especial_cerrada_se_comporta_como_no_laborable(): void
    {
        FechaEspecial::create([
            'fecha' => '2026-12-25',
            'cerrado_todo_el_dia' => true,
            'motivo' => 'Navidad',
        ]);

        $check = HorarioSucursal::esFechaLaborable('2026-12-25');
        $this->assertFalse($check['laborable']);
        $this->assertEquals('fecha_especial', $check['tipo']);
        $this->assertStringContainsString('Cerrado, no se labora', $check['mensaje']);

        $disponibilidadService = app(DisponibilidadService::class);
        $val = $disponibilidadService->validarHorarioSeleccionado(
            $this->trabajador,
            '2026-12-25',
            '10:00',
            $this->servicio
        );
        $this->assertFalse($val['disponible']);
        $this->assertEquals('dia_no_laborable', $val['motivo_tipo']);
        $this->assertStringContainsString('Cerrado, no se labora', $val['mensaje']);
    }
}
