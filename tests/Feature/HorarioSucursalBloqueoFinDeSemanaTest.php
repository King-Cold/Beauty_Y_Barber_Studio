<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\HorarioSucursal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class HorarioSucursalBloqueoFinDeSemanaTest extends TestCase
{
    use RefreshDatabase;

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

        $this->adminUser = User::create([
            'name' => 'Admin',
            'apellidos' => 'Sistema',
            'email' => 'admin.test@bbs.com',
            'telefono' => '5551234567',
            'password' => Hash::make('Admin123!'),
            'role_id' => 1,
        ]);
    }

    /**
     * Prueba 1: El domingo no se puede habilitar desde la vista habitual de configuración.
     */
    public function test_el_domingo_permanece_no_laborable_y_no_se_puede_habilitar_en_la_vista(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.configuracion'));

        $response->assertStatus(200);

        // La fila del domingo debe estar marcada visual y estructuralmente como cerrada
        $response->assertSee('id="row-domingo"', false);
        $response->assertSee('day-closed', false);

        // El switch del domingo debe estar deshabilitado para impedir que se active
        $response->assertSee('id="toggle-domingo"', false);
        $response->assertSee('disabled', false);

        // El domingo debe mostrar estado Cerrado
        $response->assertSee('id="badge-domingo"', false);
        $response->assertSee('Cerrado');
        $response->assertSee('Cerrado todo el día');
    }

    /**
     * Prueba 2: El sábado conserva el horario de 08:00 a 12:00 y sus campos no permiten modificaciones.
     */
    public function test_el_sabado_conserva_horario_fijo_08_a_12_y_campos_no_permiten_modificaciones(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.configuracion'));

        $response->assertStatus(200);

        // El sábado debe estar habilitado pero con toggle bloqueado
        $response->assertSee('id="row-sabado"', false);
        $response->assertSee('id="toggle-sabado"', false);
        $response->assertSee('id="badge-sabado"', false);
        $response->assertSee('Abierto');

        // Los campos de apertura y cierre del sábado deben mostrarse pero estar deshabilitados (bloqueados)
        $response->assertSee('id="open-sabado"', false);
        $response->assertSee('id="close-sabado"', false);
        $response->assertSee('time-select-locked', false);

        // Debe conservar 08:00 y 12:00 fijados
        $horariosMap = HorarioSucursal::getHorariosMap();
        $this->assertTrue($horariosMap['sabado']['abierto']);
        $this->assertEquals('08:00', $horariosMap['sabado']['apertura']);
        $this->assertEquals('12:00', $horariosMap['sabado']['cierre']);
    }

    /**
     * Prueba 3: Las restricciones de domingo no se pueden eludir enviando una solicitud directa al servidor.
     */
    public function test_las_restricciones_del_domingo_no_se_pueden_eludir_enviando_solicitud_directa(): void
    {
        // Intento malicioso o directo de habilitar el domingo con horario laboral
        $payload = [
            'dias' => [
                'domingo' => [
                    'abierto' => true,
                    'apertura' => '09:00',
                    'cierre' => '18:00',
                ],
            ],
        ];

        $response = $this->actingAs($this->adminUser)
            ->postJson(route('admin.configuracion.horarios.save'), $payload);

        // El servidor debe rechazar la solicitud con 422
        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Los domingos deben permanecer como días no laborables y no pueden habilitarse.',
            ]);

        // Y en la base de datos o modelo, el domingo debe seguir siendo no laborable
        $horariosMap = HorarioSucursal::getHorariosMap();
        $this->assertFalse($horariosMap['domingo']['abierto']);
    }

    /**
     * Prueba 4: Las restricciones de horario del sábado no se pueden eludir enviando una solicitud directa al servidor.
     */
    public function test_las_restricciones_del_sabado_no_se_pueden_eludir_enviando_solicitud_directa(): void
    {
        // Intento 1: Modificar hora de apertura del sábado a las 10:00
        $responseApertura = $this->actingAs($this->adminUser)
            ->postJson(route('admin.configuracion.horarios.save'), [
                'dias' => [
                    'sabado' => [
                        'abierto' => true,
                        'apertura' => '10:00',
                        'cierre' => '12:00',
                    ],
                ],
            ]);

        $responseApertura->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'El horario del sábado es fijo de 08:00 a 12:00 horas y no permite modificaciones.',
            ]);

        // Intento 2: Modificar hora de cierre del sábado a las 15:00
        $responseCierre = $this->actingAs($this->adminUser)
            ->postJson(route('admin.configuracion.horarios.save'), [
                'dias' => [
                    'sabado' => [
                        'abierto' => true,
                        'apertura' => '08:00',
                        'cierre' => '15:00',
                    ],
                ],
            ]);

        $responseCierre->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'El horario del sábado es fijo de 08:00 a 12:00 horas y no permite modificaciones.',
            ]);

        // Intento 3: Deshabilitar el sábado
        $responseDeshabilitar = $this->actingAs($this->adminUser)
            ->postJson(route('admin.configuracion.horarios.save'), [
                'dias' => [
                    'sabado' => [
                        'abierto' => false,
                        'apertura' => '08:00',
                        'cierre' => '12:00',
                    ],
                ],
            ]);

        $responseDeshabilitar->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'El sábado debe tener un horario fijo de 08:00 a 12:00 horas y no permite modificaciones.',
            ]);

        // Verificar que el horario del sábado se conserva estrictamente intacto
        $horariosMap = HorarioSucursal::getHorariosMap();
        $this->assertTrue($horariosMap['sabado']['abierto']);
        $this->assertEquals('08:00', $horariosMap['sabado']['apertura']);
        $this->assertEquals('12:00', $horariosMap['sabado']['cierre']);
    }

    /**
     * Prueba 5: Los cambios en lunes a viernes siguen funcionando sin alterar sábado y domingo.
     */
    public function test_cambios_de_lunes_a_viernes_siguen_funcionando_sin_alterar_sabado_y_domingo(): void
    {
        $payload = [
            'dias' => [
                'lunes' => ['abierto' => true, 'apertura' => '08:30', 'cierre' => '19:30'],
                'martes' => ['abierto' => true, 'apertura' => '09:00', 'cierre' => '18:00'],
                'miercoles' => ['abierto' => false, 'apertura' => '09:00', 'cierre' => '20:00'],
                'jueves' => ['abierto' => true, 'apertura' => '10:00', 'cierre' => '21:00'],
                'viernes' => ['abierto' => true, 'apertura' => '08:00', 'cierre' => '22:00'],
                'sabado' => ['abierto' => true, 'apertura' => '08:00', 'cierre' => '12:00'],
                'domingo' => ['abierto' => false, 'apertura' => '10:00', 'cierre' => '15:00'],
            ],
        ];

        $response = $this->actingAs($this->adminUser)
            ->postJson(route('admin.configuracion.horarios.save'), $payload);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $horariosMap = HorarioSucursal::getHorariosMap();

        // Lunes a viernes actualizados correctamente
        $this->assertTrue($horariosMap['lunes']['abierto']);
        $this->assertEquals('08:30', $horariosMap['lunes']['apertura']);
        $this->assertEquals('19:30', $horariosMap['lunes']['cierre']);

        $this->assertFalse($horariosMap['miercoles']['abierto']);

        $this->assertTrue($horariosMap['viernes']['abierto']);
        $this->assertEquals('08:00', $horariosMap['viernes']['apertura']);
        $this->assertEquals('22:00', $horariosMap['viernes']['cierre']);

        // Sábado y domingo conservan estrictamente sus restricciones
        $this->assertTrue($horariosMap['sabado']['abierto']);
        $this->assertEquals('08:00', $horariosMap['sabado']['apertura']);
        $this->assertEquals('12:00', $horariosMap['sabado']['cierre']);
        $this->assertFalse($horariosMap['domingo']['abierto']);
    }

    /**
     * Prueba 6: Las restricciones permanecen después de guardar y volver a consultar la configuración.
     */
    public function test_las_restricciones_permanecen_despues_de_guardar_y_volver_a_consultar(): void
    {
        // 1. Guardar cambios en días entre semana
        $this->actingAs($this->adminUser)
            ->postJson(route('admin.configuracion.horarios.save'), [
                'dias' => [
                    'lunes' => ['abierto' => true, 'apertura' => '07:30', 'cierre' => '21:30'],
                    'sabado' => ['abierto' => true, 'apertura' => '08:00', 'cierre' => '12:00'],
                    'domingo' => ['abierto' => false],
                ],
            ])
            ->assertStatus(200);

        // 2. Volver a consultar la vista de configuración
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.configuracion'));

        $response->assertStatus(200);

        // Verificar que el domingo sigue deshabilitado y cerrado
        $response->assertSee('id="toggle-domingo"', false);
        $response->assertSee('id="badge-domingo"', false);
        $response->assertSee('Cerrado todo el día');

        // Verificar que el sábado sigue con horario fijo 08:00 a 12:00 y campos deshabilitados
        $response->assertSee('id="open-sabado"', false);
        $response->assertSee('id="close-sabado"', false);
        $response->assertSee('value="08:00" selected', false);
        $response->assertSee('value="12:00" selected', false);

        // Y en base de datos / modelo persistido
        $horariosMap = HorarioSucursal::getHorariosMap();
        $this->assertEquals('07:30', $horariosMap['lunes']['apertura']);
        $this->assertEquals('21:30', $horariosMap['lunes']['cierre']);
        $this->assertTrue($horariosMap['sabado']['abierto']);
        $this->assertEquals('08:00', $horariosMap['sabado']['apertura']);
        $this->assertEquals('12:00', $horariosMap['sabado']['cierre']);
        $this->assertFalse($horariosMap['domingo']['abierto']);
    }

    /**
     * Prueba 7: No se puede registrar una fecha especial en domingo (día no laborable).
     */
    public function test_no_se_puede_registrar_fecha_especial_en_domingo(): void
    {
        // 2026-10-25 es un domingo
        $payload = [
            'fecha' => '2026-10-25',
            'motivo' => 'Intento Festivo en Domingo',
            'cerrado_todo_el_dia' => false,
            'hora_apertura' => '09:00',
            'hora_cierre' => '15:00',
        ];

        $response = $this->actingAs($this->adminUser)
            ->postJson(route('admin.configuracion.fechas_especiales.store'), $payload);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Los domingos son días no laborables de la sucursal y no se pueden registrar como fechas especiales.',
            ]);

        $this->assertDatabaseMissing('fechas_especiales', [
            'fecha' => '2026-10-25',
        ]);
    }

    /**
     * Prueba 8: No se puede actualizar una fecha especial para que caiga en domingo.
     */
    public function test_no_se_puede_actualizar_fecha_especial_a_un_domingo(): void
    {
        // Crear una fecha especial válida en un lunes (2026-10-26)
        $fechaEspecial = \App\Models\FechaEspecial::create([
            'fecha' => '2026-10-26',
            'motivo' => 'Día de Capacitación',
            'cerrado_todo_el_dia' => true,
        ]);

        // Intentar actualizar la fecha para que sea domingo 2026-11-01
        $response = $this->actingAs($this->adminUser)
            ->putJson(route('admin.configuracion.fechas_especiales.update', $fechaEspecial), [
                'fecha' => '2026-11-01',
                'motivo' => 'Intento Mover a Domingo',
                'cerrado_todo_el_dia' => true,
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Los domingos son días no laborables de la sucursal y no se pueden registrar como fechas especiales.',
            ]);

        // La fecha original debe mantenerse
        $this->assertDatabaseHas('fechas_especiales', [
            'id' => $fechaEspecial->id,
            'fecha' => '2026-10-26',
        ]);
    }

    /**
     * Prueba 9: Se puede eliminar una fecha especial correctamente mediante la ruta DELETE.
     */
    public function test_se_puede_eliminar_una_fecha_especial_correctamente(): void
    {
        $fechaEspecial = \App\Models\FechaEspecial::create([
            'fecha' => '2026-10-27',
            'motivo' => 'Excepción Temporal de Prueba',
            'cerrado_todo_el_dia' => true,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->deleteJson(route('admin.configuracion.fechas_especiales.destroy', $fechaEspecial));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Fecha especial eliminada correctamente.',
            ]);

        $this->assertDatabaseMissing('fechas_especiales', [
            'id' => $fechaEspecial->id,
        ]);
    }
}

