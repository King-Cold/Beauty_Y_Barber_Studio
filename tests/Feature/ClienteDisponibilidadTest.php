<?php

namespace Tests\Feature;

use App\Models\FechaEspecial;
use App\Models\HorarioSucursal;
use App\Models\Service;
use App\Models\Trabajador;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClienteDisponibilidadTest extends TestCase
{
    use RefreshDatabase;

    protected User $clienteUser;
    protected User $trabajadorUser;
    protected Trabajador $trabajador;
    protected Service $servicio30;
    protected Service $servicio60;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\TestUsersSeeder::class);

        $this->clienteUser = User::where('role_id', 4)->first();
        $this->trabajadorUser = User::where('role_id', 3)->first();

        // Horario del trabajador: Lunes a viernes de 09:00 a 14:00, con descanso de 11:00 a 12:00
        $this->trabajador = Trabajador::create([
            'user_id' => $this->trabajadorUser->id,
            'direccion' => 'Av. Principal 123',
            'experiencia' => 5,
            'activo' => true,
            'horario' => [
                'lunes' => [
                    'entrada' => '09:00',
                    'salida' => '14:00',
                    'descanso_inicio' => '11:00',
                    'descanso_fin' => '12:00',
                ],
                'martes' => [
                    'entrada' => '09:00',
                    'salida' => '14:00',
                ],
                'miercoles' => [
                    'entrada' => '09:00',
                    'salida' => '14:00',
                ],
                'jueves' => [
                    'entrada' => '09:00',
                    'salida' => '14:00',
                ],
                'viernes' => [
                    'entrada' => '09:00',
                    'salida' => '14:00',
                ],
                // Sábado y domingo sin turno (días de descanso)
            ],
        ]);

        // Servicios con diferentes duraciones
        $this->servicio30 = Service::create([
            'name' => 'Corte Clásico Test',
            'description' => 'Servicio de corte estándar.',
            'price' => 200.00,
            'duration_minutes' => 30,
            'category' => 'barberia',
            'is_active' => true,
        ]);

        $this->servicio60 = Service::create([
            'name' => 'Corte y Barba Completo Test',
            'description' => 'Servicio completo de una hora.',
            'price' => 350.00,
            'duration_minutes' => 60,
            'category' => 'barberia',
            'is_active' => true,
        ]);

        // Asignar los servicios al trabajador
        $this->trabajador->servicios()->attach([$this->servicio30->id, $this->servicio60->id]);

        // Configuración de sucursal: abierta lunes a viernes 08:00 a 20:00, domingo cerrada
        HorarioSucursal::ensureTableExists();
        HorarioSucursal::updateOrCreate(
            ['dia' => 'lunes'],
            ['abierto' => true, 'apertura' => '08:00', 'cierre' => '20:00']
        );
        HorarioSucursal::updateOrCreate(
            ['dia' => 'domingo'],
            ['abierto' => false, 'apertura' => '09:00', 'cierre' => '14:00']
        );
    }

    /**
     * Busca la próxima fecha que corresponda a un día de la semana específico.
     */
    protected function getNextDateForDay(int $dayOfWeek): string
    {
        $date = now()->addDays(1);
        while ($date->dayOfWeek !== $dayOfWeek) {
            $date->addDay();
        }
        return $date->format('Y-m-d');
    }

    /**
     * Test que consulta disponibilidad exitosa respetando jornada laboral y descansos.
     */
    public function test_consultar_disponibilidad_excluye_descansos_y_respeta_jornada(): void
    {
        // Próximo lunes (dayOfWeek = 1)
        $proximoLunes = $this->getNextDateForDay(1);

        $response = $this->actingAs($this->clienteUser)->getJson(route('cliente.agendamiento.disponibilidad', [
            'service_id' => $this->servicio30->id,
            'worker_id' => $this->trabajador->id,
            'date' => $proximoLunes,
        ]));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'disponible' => true,
            'duracion_minutos' => 30,
        ]);

        $horarios = $response->json('horarios');

        // Debe contener horarios laborales válidos como 09:00, 09:30, 10:00, 10:30
        $this->assertContains('09:00', $horarios);
        $this->assertContains('09:30', $horarios);
        $this->assertContains('10:00', $horarios);
        $this->assertContains('10:30', $horarios);

        // NO debe contener horarios dentro del descanso de 11:00 a 12:00
        $this->assertNotContains('11:00', $horarios);
        $this->assertNotContains('11:15', $horarios);
        $this->assertNotContains('11:30', $horarios);
        $this->assertNotContains('11:45', $horarios);

        // A las 10:45 no cabe un servicio de 30 minutos antes de las 11:00 (descanso)
        $this->assertNotContains('10:45', $horarios);

        // Debe reanudar a las 12:00
        $this->assertContains('12:00', $horarios);
        $this->assertContains('13:30', $horarios);

        // A las 14:00 o después ya no cabe porque la salida es 14:00
        $this->assertNotContains('14:00', $horarios);
        $this->assertNotContains('14:30', $horarios);
    }

    /**
     * Test que respeta la duración del servicio seleccionado (30 vs 60 minutos).
     */
    public function test_respeta_duracion_del_servicio_seleccionado(): void
    {
        $proximoLunes = $this->getNextDateForDay(1);

        // Servicio de 60 minutos
        $response60 = $this->actingAs($this->clienteUser)->getJson(route('cliente.agendamiento.disponibilidad', [
            'service_id' => $this->servicio60->id,
            'worker_id' => $this->trabajador->id,
            'date' => $proximoLunes,
        ]));

        $response60->assertStatus(200);
        $horarios60 = $response60->json('horarios');

        // Un servicio de 60 minutos no puede empezar a las 10:15 ni 10:30 porque solaparía con el descanso (11:00 a 12:00)
        $this->assertNotContains('10:15', $horarios60);
        $this->assertNotContains('10:30', $horarios60);

        // Puede empezar a las 09:00 o 10:00
        $this->assertContains('09:00', $horarios60);
        $this->assertContains('10:00', $horarios60);

        // Y para la salida a las 14:00, el último horario válido de 60 min es 13:00 (no 13:30)
        $this->assertContains('13:00', $horarios60);
        $this->assertNotContains('13:30', $horarios60);
    }

    /**
     * Test que excluye días de descanso del trabajador (ej. sábado o domingo sin turno).
     */
    public function test_sin_disponibilidad_en_dias_de_descanso_del_trabajador(): void
    {
        // Próximo domingo (dayOfWeek = 0)
        $proximoDomingo = $this->getNextDateForDay(0);

        $response = $this->actingAs($this->clienteUser)->getJson(route('cliente.agendamiento.disponibilidad', [
            'service_id' => $this->servicio30->id,
            'worker_id' => $this->trabajador->id,
            'date' => $proximoDomingo,
        ]));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'disponible' => false,
            'horarios' => [],
        ]);
    }

    /**
     * Test que respeta fechas especiales cerradas todo el día.
     */
    public function test_sin_disponibilidad_en_fechas_especiales_cerradas(): void
    {
        $proximoLunes = $this->getNextDateForDay(1);

        FechaEspecial::create([
            'fecha' => $proximoLunes,
            'motivo' => 'Día Festivo Nacional',
            'cerrado_todo_el_dia' => true,
        ]);

        $response = $this->actingAs($this->clienteUser)->getJson(route('cliente.agendamiento.disponibilidad', [
            'service_id' => $this->servicio30->id,
            'worker_id' => $this->trabajador->id,
            'date' => $proximoLunes,
        ]));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'disponible' => false,
            'horarios' => [],
        ]);
        $this->assertStringContainsString('Día Festivo Nacional', $response->json('mensaje'));
    }

    /**
     * Test que respeta bloqueos de disponibilidad del trabajador.
     */
    public function test_respeta_bloqueos_de_disponibilidad_del_trabajador(): void
    {
        $proximoLunes = $this->getNextDateForDay(1);

        // Asignar un bloqueo administrativo de 09:30 a 10:30 para ese día
        $horarioConBloqueo = $this->trabajador->horario;
        $horarioConBloqueo['bloqueos'] = [
            [
                'fecha' => $proximoLunes,
                'inicio' => '09:30',
                'fin' => '10:30',
                'motivo' => 'Capacitación del trabajador',
            ]
        ];
        $this->trabajador->horario = $horarioConBloqueo;
        $this->trabajador->save();

        $response = $this->actingAs($this->clienteUser)->getJson(route('cliente.agendamiento.disponibilidad', [
            'service_id' => $this->servicio30->id,
            'worker_id' => $this->trabajador->id,
            'date' => $proximoLunes,
        ]));

        $response->assertStatus(200);
        $horarios = $response->json('horarios');

        // 09:00 está libre (termina a las 09:30 justo antes del bloqueo)
        $this->assertContains('09:00', $horarios);

        // Durante el bloqueo de 09:30 a 10:30 NO debe haber horarios
        $this->assertNotContains('09:30', $horarios);
        $this->assertNotContains('09:45', $horarios);
        $this->assertNotContains('10:00', $horarios);
        $this->assertNotContains('10:15', $horarios);

        // A las 10:30 reanuda
        $this->assertContains('10:30', $horarios);
    }

    /**
     * Test de validación en tiempo real de horario seleccionado al continuar.
     */
    public function test_validar_horario_en_tiempo_real(): void
    {
        $proximoLunes = $this->getNextDateForDay(1);

        // Horario válido (09:00)
        $responseValido = $this->actingAs($this->clienteUser)->postJson(route('cliente.agendamiento.validar-horario'), [
            'service_id' => $this->servicio30->id,
            'worker_id' => $this->trabajador->id,
            'date' => $proximoLunes,
            'time' => '09:00',
        ]);

        $responseValido->assertStatus(200);
        $responseValido->assertJson([
            'success' => true,
            'valido' => true,
        ]);

        // Horario inválido / no disponible (11:30 en descanso)
        $responseInvalido = $this->actingAs($this->clienteUser)->postJson(route('cliente.agendamiento.validar-horario'), [
            'service_id' => $this->servicio30->id,
            'worker_id' => $this->trabajador->id,
            'date' => $proximoLunes,
            'time' => '11:30',
        ]);

        $responseInvalido->assertStatus(422);
        $responseInvalido->assertJson([
            'success' => false,
            'valido' => false,
        ]);
    }

    /**
     * Test que verifica que no se creen citas en la BD durante consultas ni validaciones.
     */
    public function test_no_se_crean_citas_en_la_base_de_datos(): void
    {
        $proximoLunes = $this->getNextDateForDay(1);

        $this->actingAs($this->clienteUser)->getJson(route('cliente.agendamiento.disponibilidad', [
            'service_id' => $this->servicio30->id,
            'worker_id' => $this->trabajador->id,
            'date' => $proximoLunes,
        ]));

        $this->actingAs($this->clienteUser)->postJson(route('cliente.agendamiento.validar-horario'), [
            'service_id' => $this->servicio30->id,
            'worker_id' => $this->trabajador->id,
            'date' => $proximoLunes,
            'time' => '09:00',
        ]);

        // Comprobar que ninguna cita haya sido registrada en BD (no existe tabla de citas o tiene 0 registros)
        if (\Illuminate\Support\Facades\Schema::hasTable('citas')) {
            $this->assertEquals(0, \Illuminate\Support\Facades\DB::table('citas')->count());
        } elseif (\Illuminate\Support\Facades\Schema::hasTable('appointments')) {
            $this->assertEquals(0, \Illuminate\Support\Facades\DB::table('appointments')->count());
        } else {
            $this->assertFalse(\Illuminate\Support\Facades\Schema::hasTable('citas'));
        }
    }

    /**
     * Test que verifica la alerta diagnóstica cuando un servicio sobrepasa la hora de salida/cierre.
     */
    public function test_alerta_diagnostica_cuando_servicio_excede_hora_cierre_o_salida(): void
    {
        $proximoLunes = $this->getNextDateForDay(1);

        // El trabajador sale a las 14:00. Un servicio de 60 min a las 13:30 terminaría a las 14:30 (excede por 30 min).
        $response = $this->actingAs($this->clienteUser)->postJson(route('cliente.agendamiento.validar-horario'), [
            'service_id' => $this->servicio60->id,
            'worker_id' => $this->trabajador->id,
            'date' => $proximoLunes,
            'time' => '13:30',
        ]);

        $response->assertStatus(422);
        $mensaje = $response->json('mensaje');
        $this->assertStringContainsString('no está disponible', $mensaje);
        $this->assertStringContainsString('14:00', $mensaje);
        $this->assertStringContainsString('30 minutos', $mensaje);
    }

    /**
     * Test que verifica la alerta diagnóstica cuando un horario coincide con un bloqueo del trabajador.
     */
    public function test_alerta_diagnostica_cuando_horario_coincide_con_bloqueo(): void
    {
        $proximoLunes = $this->getNextDateForDay(1);

        $horarioConBloqueo = $this->trabajador->horario;
        $horarioConBloqueo['bloqueos'] = [
            [
                'fecha' => $proximoLunes,
                'inicio' => '10:00',
                'fin' => '11:00',
                'motivo' => 'Reunión de equipo',
            ]
        ];
        $this->trabajador->horario = $horarioConBloqueo;
        $this->trabajador->save();

        $response = $this->actingAs($this->clienteUser)->postJson(route('cliente.agendamiento.validar-horario'), [
            'service_id' => $this->servicio30->id,
            'worker_id' => $this->trabajador->id,
            'date' => $proximoLunes,
            'time' => '10:15',
        ]);

        $response->assertStatus(422);
        $mensaje = $response->json('mensaje');
        $this->assertStringContainsString('bloqueo de disponibilidad', $mensaje);
        $this->assertStringContainsString('Reunión de equipo', $mensaje);
    }

    /**
     * Test que verifica que los horarios de comida y bloqueos no aparezcan en la selección.
     */
    public function test_excluye_horarios_de_comida_y_bloqueos_en_selector(): void
    {
        $proximoMartes = $this->getNextDateForDay(2);

        // Asignar horario con comida de 12:00 a 13:00 para martes
        $horario = $this->trabajador->horario;
        $horario['martes']['comida_inicio'] = '12:00';
        $horario['martes']['comida_fin'] = '13:00';
        $this->trabajador->horario = $horario;
        $this->trabajador->save();

        $response = $this->actingAs($this->clienteUser)->getJson(route('cliente.agendamiento.disponibilidad', [
            'service_id' => $this->servicio30->id,
            'worker_id' => $this->trabajador->id,
            'date' => $proximoMartes,
        ]));

        $response->assertStatus(200);
        $horarios = $response->json('horarios');

        // Durante la comida de 12:00 a 13:00 NO debe haber horarios
        $this->assertNotContains('12:00', $horarios);
        $this->assertNotContains('12:15', $horarios);
        $this->assertNotContains('12:30', $horarios);
        $this->assertNotContains('12:45', $horarios);

        // Antes de las 12:00 (11:30 termina a las 12:00) y a las 13:00 sí debe haber
        $this->assertContains('11:30', $horarios);
        $this->assertContains('13:00', $horarios);
    }

    /**
     * Test que impide citas simultáneas y solapadas para el mismo trabajador.
     */
    public function test_impide_citas_simultaneas_para_el_mismo_trabajador_y_horario(): void
    {
        $proximoLunes = $this->getNextDateForDay(1);

        // Agregamos una cita existente de 09:00 a 09:30 para ese día
        $horarioConCita = $this->trabajador->horario;
        $horarioConCita['citas'] = [
            [
                'fecha' => $proximoLunes,
                'inicio' => '09:00',
                'fin' => '09:30',
            ]
        ];
        $this->trabajador->horario = $horarioConCita;
        $this->trabajador->save();

        // 1. En la consulta de disponibilidad, el horario de las 09:00 NO debe aparecer
        $response = $this->actingAs($this->clienteUser)->getJson(route('cliente.agendamiento.disponibilidad', [
            'service_id' => $this->servicio30->id,
            'worker_id' => $this->trabajador->id,
            'date' => $proximoLunes,
        ]));

        $response->assertStatus(200);
        $horarios = $response->json('horarios');
        $this->assertNotContains('09:00', $horarios);
        $this->assertNotContains('09:15', $horarios);
        $this->assertContains('09:30', $horarios);

        // 2. Al intentar validar dicho horario (simulando que el cliente lo tenía abierto antes de reservarse), se rechaza
        $responseValidar = $this->actingAs($this->clienteUser)->postJson(route('cliente.agendamiento.validar-horario'), [
            'service_id' => $this->servicio30->id,
            'worker_id' => $this->trabajador->id,
            'date' => $proximoLunes,
            'time' => '09:00',
        ]);

        $responseValidar->assertStatus(422);
        $responseValidar->assertJson([
            'success' => false,
            'valido' => false,
        ]);
        $mensaje = $responseValidar->json('mensaje');
        $this->assertStringContainsString('otra cita reservada previamente', $mensaje);
        $this->assertStringContainsString('09:00 a 09:30', $mensaje);
    }
}

