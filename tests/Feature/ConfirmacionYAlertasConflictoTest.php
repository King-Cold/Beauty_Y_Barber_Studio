<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\HorarioSucursal;
use App\Models\Service;
use App\Models\Trabajador;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConfirmacionYAlertasConflictoTest extends TestCase
{
    use RefreshDatabase;

    protected User $cliente1;
    protected User $cliente2;
    protected User $recepcionista;
    protected User $trabajadorUser;
    protected Trabajador $trabajador;
    protected Service $servicioCorte;
    protected Service $servicioColor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\TestUsersSeeder::class);

        // Clientes
        $this->cliente1 = User::where('role_id', 4)->first();
        $this->cliente2 = User::create([
            'name' => 'Lucía',
            'apellidos' => 'Martínez Reyes',
            'email' => 'lucia.martinez@test.com',
            'telefono' => '5552223344',
            'password' => bcrypt('Password123!'),
            'role_id' => 4,
        ]);

        // Recepcionista
        $this->recepcionista = User::where('role_id', 2)->first();

        // Trabajador
        $this->trabajadorUser = User::where('role_id', 3)->first();
        $this->trabajador = Trabajador::create([
            'user_id' => $this->trabajadorUser->id,
            'direccion' => 'Estudio BBS Centro',
            'experiencia' => 6,
            'activo' => true,
            'horario' => [
                'lunes' => ['entrada' => '09:00', 'salida' => '19:00'],
                'martes' => ['entrada' => '09:00', 'salida' => '19:00'],
                'miercoles' => ['entrada' => '09:00', 'salida' => '19:00'],
                'jueves' => ['entrada' => '09:00', 'salida' => '19:00'],
                'viernes' => ['entrada' => '09:00', 'salida' => '19:00'],
            ],
        ]);

        // Servicios
        $this->servicioCorte = Service::create([
            'name' => 'Corte Caballero BBS',
            'description' => 'Corte regular de cabello',
            'price' => 220.00,
            'duration_minutes' => 30,
            'category' => 'barberia',
            'is_active' => true,
        ]);

        $this->servicioColor = Service::create([
            'name' => 'Coloración y Peinado',
            'description' => 'Servicio estético de 60 minutos',
            'price' => 600.00,
            'duration_minutes' => 60,
            'category' => 'estetica',
            'is_active' => true,
        ]);

        $this->trabajador->servicios()->attach([
            $this->servicioCorte->id,
            $this->servicioColor->id,
        ]);

        // Horario sucursal abierto
        HorarioSucursal::ensureTableExists();
        foreach (['lunes', 'martes', 'miercoles', 'jueves', 'viernes'] as $dia) {
            HorarioSucursal::updateOrCreate(
                ['dia' => $dia],
                ['abierto' => true, 'apertura' => '08:00', 'cierre' => '20:00']
            );
        }
    }

    protected function getNextDateForDay(int $dayOfWeek): string
    {
        $date = now()->addDays(1);
        while ($date->dayOfWeek !== $dayOfWeek) {
            $date->addDay();
        }
        return $date->format('Y-m-d');
    }

    /**
     * 1. Éxito en Agendamiento de Cliente:
     *    El servidor responde 200 con éxito únicamente cuando la cita se guardó en BD.
     */
    public function test_cliente_confirmacion_exitosa_responde_200_y_guarda_en_bd(): void
    {
        $fecha = $this->getNextDateForDay(1); // Lunes
        $hora = '10:00';

        $response = $this->actingAs($this->cliente1)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->servicioCorte->id,
            'worker_id' => $this->trabajador->id,
            'date' => $fecha,
            'time' => $hora,
            'notas' => 'Sin preferencias',
        ]);

        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));
        $this->assertTrue($response->json('valido'));
        $this->assertNotNull($response->json('cita.id'));
        $this->assertStringContainsString('confirmada exitosamente', $response->json('mensaje'));

        // Comprobar registro físico real en base de datos
        $this->assertDatabaseHas('citas', [
            'id' => $response->json('cita.id'),
            'user_id' => $this->cliente1->id,
            'trabajador_id' => $this->trabajador->id,
            'service_id' => $this->servicioCorte->id,
            'hora_inicio' => $hora,
            'estado' => 'confirmada',
        ]);
    }

    /**
     * 2. Éxito en Agendamiento de Recepcionista:
     *    El servidor responde 200 con éxito únicamente cuando la cita se guardó en BD para el cliente indicado.
     */
    public function test_recepcion_confirmacion_exitosa_responde_200_y_guarda_en_bd(): void
    {
        $fecha = $this->getNextDateForDay(1); // Lunes
        $hora = '11:00';

        $response = $this->actingAs($this->recepcionista)->postJson(route('recepcion.agendamiento.confirmar'), [
            'client_id' => $this->cliente2->id,
            'service_id' => $this->servicioColor->id,
            'worker_id' => $this->trabajador->id,
            'date' => $fecha,
            'time' => $hora,
        ]);

        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));
        $this->assertTrue($response->json('valido'));
        $this->assertNotNull($response->json('cita.id'));
        $this->assertStringContainsString('agendada exitosamente', $response->json('mensaje'));

        // Comprobar registro físico real en base de datos
        $this->assertDatabaseHas('citas', [
            'id' => $response->json('cita.id'),
            'user_id' => $this->cliente2->id,
            'trabajador_id' => $this->trabajador->id,
            'service_id' => $this->servicioColor->id,
            'hora_inicio' => $hora,
            'duracion_minutos' => 60,
            'estado' => 'confirmada',
        ]);
    }

    /**
     * 3. Conflicto de Disponibilidad de Horario (Cliente):
     *    Si el horario ya no está disponible por otra cita del trabajador, responde 422,
     *    success es false, mensaje claro de conflicto y NO se guarda en BD.
     */
    public function test_cliente_conflicto_disponibilidad_responde_422_con_mensaje_claro(): void
    {
        $fecha = $this->getNextDateForDay(1);
        $hora = '10:00';

        // Pre-crear cita existente
        Cita::create([
            'user_id' => $this->cliente2->id,
            'trabajador_id' => $this->trabajador->id,
            'service_id' => $this->servicioCorte->id,
            'fecha' => $fecha,
            'hora_inicio' => $hora,
            'hora_fin' => '10:30',
            'duracion_minutos' => 30,
            'precio' => 220.00,
            'estado' => 'confirmada',
        ]);

        $response = $this->actingAs($this->cliente1)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->servicioCorte->id,
            'worker_id' => $this->trabajador->id,
            'date' => $fecha,
            'time' => $hora,
        ]);

        $response->assertStatus(422);
        $this->assertFalse($response->json('success'));
        $this->assertFalse($response->json('valido'));
        $this->assertStringContainsString('no está disponible', $response->json('mensaje'));

        // La base de datos debe contener únicamente la cita preexistente
        $this->assertEquals(1, Cita::where('trabajador_id', $this->trabajador->id)->count());
        $this->assertDatabaseMissing('citas', [
            'user_id' => $this->cliente1->id,
            'hora_inicio' => $hora,
        ]);
    }

    /**
     * 4. Conflicto de Disponibilidad de Horario (Recepcionista):
     *    Recepcionista intentando agendar en horario ocupado recibe 422 con mensaje claro.
     */
    public function test_recepcion_conflicto_disponibilidad_responde_422_y_rechaza(): void
    {
        $fecha = $this->getNextDateForDay(1);
        $hora = '14:00';

        // Pre-crear cita activa
        Cita::create([
            'user_id' => $this->cliente1->id,
            'trabajador_id' => $this->trabajador->id,
            'service_id' => $this->servicioColor->id,
            'fecha' => $fecha,
            'hora_inicio' => $hora,
            'hora_fin' => '15:00',
            'duracion_minutos' => 60,
            'precio' => 600.00,
            'estado' => 'confirmada',
        ]);

        $response = $this->actingAs($this->recepcionista)->postJson(route('recepcion.agendamiento.confirmar'), [
            'client_id' => $this->cliente2->id,
            'service_id' => $this->servicioCorte->id,
            'worker_id' => $this->trabajador->id,
            'date' => $fecha,
            'time' => '14:30', // Dentro de 14:00 a 15:00
        ]);

        $response->assertStatus(422);
        $this->assertFalse($response->json('success'));
        $this->assertFalse($response->json('valido'));
        $this->assertStringContainsString('no está disponible', $response->json('mensaje'));

        $this->assertEquals(1, Cita::where('trabajador_id', $this->trabajador->id)->count());
    }

    /**
     * 5. Error de Validación en Solicitud (Cliente):
     *    Falta de campos requeridos responde 422 y NO indica éxito ni guarda cita.
     */
    public function test_error_validacion_cliente_no_indica_exito_ni_guarda_cita(): void
    {
        $response = $this->actingAs($this->cliente1)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->servicioCorte->id,
            // Sin worker_id, date, time
        ]);

        $response->assertStatus(422);
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotTrue($response->json('success'));

        $this->assertEquals(0, Cita::count());
    }

    /**
     * 6. Error de Validación en Solicitud (Recepcionista):
     *    Cliente inválido o ausente responde 422 y no guarda cita.
     */
    public function test_error_validacion_recepcion_no_indica_exito_ni_guarda_cita(): void
    {
        $fecha = $this->getNextDateForDay(1);

        $response = $this->actingAs($this->recepcionista)->postJson(route('recepcion.agendamiento.confirmar'), [
            'client_id' => 99999, // Cliente inexistente
            'service_id' => $this->servicioCorte->id,
            'worker_id' => $this->trabajador->id,
            'date' => $fecha,
            'time' => '10:00',
        ]);

        $response->assertStatus(422);
        $this->assertEquals(0, Cita::count());
    }

    /**
     * 7. Reintentos tras error no crean citas duplicadas:
     *    Si un cliente intenta un horario ocupado y luego reintenta con un horario libre,
     *    únicamente se genera la cita válida.
     */
    public function test_reintento_despues_de_conflicto_crea_unicamente_una_cita(): void
    {
        $fecha = $this->getNextDateForDay(1);

        // Cita existente a las 10:00
        Cita::create([
            'user_id' => $this->cliente2->id,
            'trabajador_id' => $this->trabajador->id,
            'service_id' => $this->servicioCorte->id,
            'fecha' => $fecha,
            'hora_inicio' => '10:00',
            'hora_fin' => '10:30',
            'duracion_minutos' => 30,
            'precio' => 220.00,
            'estado' => 'confirmada',
        ]);

        // Intento 1: Falla por conflicto a las 10:00
        $res1 = $this->actingAs($this->cliente1)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->servicioCorte->id,
            'worker_id' => $this->trabajador->id,
            'date' => $fecha,
            'time' => '10:00',
        ]);
        $res1->assertStatus(422);

        // Intento 2: Reintento exitoso a las 10:30 (horario libre)
        $res2 = $this->actingAs($this->cliente1)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->servicioCorte->id,
            'worker_id' => $this->trabajador->id,
            'date' => $fecha,
            'time' => '10:30',
        ]);
        $res2->assertStatus(200);
        $this->assertTrue($res2->json('success'));

        // En la base de datos debe haber exactamente 2 citas en total (la previa y la del reintento)
        $this->assertEquals(2, Cita::count());
        $this->assertEquals(1, Cita::where('user_id', $this->cliente1->id)->count());
    }

    /**
     * 8. Comprobación de UI: Las vistas incluyen el modal azul con icono blanco de alerta
     *    y los contenedores de resumen para confirmación.
     */
    public function test_vistas_contienen_resumen_y_modal_azul_con_icono_blanco(): void
    {
        // 1. Vista de agendamiento del cliente
        $resCliente = $this->actingAs($this->cliente1)->get(route('cliente.agendamiento'));
        $resCliente->assertStatus(200);
        $resCliente->assertSee('id="unavailableSlotModal"', false);
        $resCliente->assertSee('id="unavailableModalMessage"', false);
        $resCliente->assertSee('id="btnCloseUnavailableModal"', false);
        $resCliente->assertSee('linear-gradient(135deg, #091a3e 0%, #0d2868 50%, #11388f 100%)', false);
        $resCliente->assertSee('stroke="#ffffff"', false);
        $resCliente->assertSee('id="summaryDateVal"', false);
        $resCliente->assertSee('id="summaryTimeVal"', false);
        $resCliente->assertSee('id="btnContinue"', false);

        // 2. Vista de agendamiento de la recepcionista
        $resRecepcion = $this->actingAs($this->recepcionista)->get(route('recepcion.agendamiento'));
        $resRecepcion->assertStatus(200);
        $resRecepcion->assertSee('id="unavailableSlotModal"', false);
        $resRecepcion->assertSee('id="unavailableModalMessage"', false);
        $resRecepcion->assertSee('id="btnCloseUnavailableModal"', false);
        $resRecepcion->assertSee('linear-gradient(135deg, #091a3e 0%, #0d2868 50%, #11388f 100%)', false);
        $resRecepcion->assertSee('stroke="#ffffff"', false);
        $resRecepcion->assertSee('id="btnContinue"', false);
    }

    /**
     * Criterio 1: La alerta aparece cuando el horario seleccionado deja de estar disponible.
     * Al cambiar la disponibilidad (horario ocupado), el servidor responde con 422, valido: false y motivo_tipo.
     */
    public function test_criterio_1_alerta_aparece_cuando_horario_seleccionado_deja_de_estar_disponible(): void
    {
        $fecha = $this->getNextDateForDay(1);
        $hora = '10:00';

        // 1. Horario inicialmente libre se valida con éxito
        $resValido = $this->actingAs($this->cliente1)->postJson(route('cliente.agendamiento.validar-horario'), [
            'service_id' => $this->servicioCorte->id,
            'worker_id' => $this->trabajador->id,
            'date' => $fecha,
            'time' => $hora,
        ]);
        $resValido->assertStatus(200);
        $this->assertTrue($resValido->json('valido'));

        // 2. Otro cliente reserva ese mismo horario en el ínterin
        Cita::create([
            'user_id' => $this->cliente2->id,
            'trabajador_id' => $this->trabajador->id,
            'service_id' => $this->servicioCorte->id,
            'fecha' => $fecha,
            'hora_inicio' => $hora,
            'hora_fin' => '10:30',
            'duracion_minutos' => 30,
            'precio' => 220.00,
            'estado' => 'confirmada',
        ]);

        // 3. El primer cliente intenta continuar o confirmar: se detecta que dejó de estar disponible
        $resConflictoVal = $this->actingAs($this->cliente1)->postJson(route('cliente.agendamiento.validar-horario'), [
            'service_id' => $this->servicioCorte->id,
            'worker_id' => $this->trabajador->id,
            'date' => $fecha,
            'time' => $hora,
        ]);
        $resConflictoVal->assertStatus(422);
        $this->assertFalse($resConflictoVal->json('valido'));
        $this->assertStringContainsString('no está disponible', $resConflictoVal->json('mensaje'));

        $resConflictoConf = $this->actingAs($this->cliente1)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->servicioCorte->id,
            'worker_id' => $this->trabajador->id,
            'date' => $fecha,
            'time' => $hora,
        ]);
        $resConflictoConf->assertStatus(422);
        $this->assertFalse($resConflictoConf->json('success'));
        $this->assertFalse($resConflictoConf->json('valido'));
        $this->assertContains($resConflictoConf->json('motivo_tipo'), ['cita', 'cita_simultanea']);
    }

    /**
     * Criterio 2: La ventana presenta el diseño azul y el icono de alerta blanco solicitado.
     */
    public function test_criterio_2_ventana_presenta_diseno_azul_con_icono_blanco_solicitado(): void
    {
        foreach (['cliente.agendamiento', 'recepcion.agendamiento'] as $ruta) {
            $user = ($ruta === 'cliente.agendamiento') ? $this->cliente1 : $this->recepcionista;
            $response = $this->actingAs($user)->get(route($ruta));
            $response->assertStatus(200);

            // Ventana modal con fondo gradiente azul
            $response->assertSee('linear-gradient(135deg, #091a3e 0%, #0d2868 50%, #11388f 100%)', false);
            // Borde y sombra azul
            $response->assertSee('rgba(59, 130, 246, 0.45)', false);
            $response->assertSee('rgba(0, 50, 180, 0.5)', false);
            // Icono de alerta de color blanco puro
            $response->assertSee('stroke="#ffffff"', false);
            // Estructura y botones
            $response->assertSee('id="unavailableSlotModal"', false);
            $response->assertSee('id="unavailableModalMessage"', false);
            $response->assertSee('id="btnCloseUnavailableModal"', false);
            $response->assertSee('Seleccionar otro horario', false);
        }
    }

    /**
     * Criterio 3: El sistema no registra la cita en el horario ocupado.
     */
    public function test_criterio_3_sistema_no_registra_cita_en_horario_ocupado(): void
    {
        $fecha = $this->getNextDateForDay(1);
        $hora = '12:00';

        // Cita existente en el especialista
        Cita::create([
            'user_id' => $this->cliente2->id,
            'trabajador_id' => $this->trabajador->id,
            'service_id' => $this->servicioCorte->id,
            'fecha' => $fecha,
            'hora_inicio' => $hora,
            'hora_fin' => '12:30',
            'duracion_minutos' => 30,
            'precio' => 220.00,
            'estado' => 'confirmada',
        ]);

        $citasAntes = Cita::count();

        // Intento de reservación en el mismo horario ocupado
        $response = $this->actingAs($this->cliente1)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->servicioCorte->id,
            'worker_id' => $this->trabajador->id,
            'date' => $fecha,
            'time' => $hora,
        ]);

        $response->assertStatus(422);

        // El conteo no cambia y la cita rechazada no existe en BD
        $this->assertEquals($citasAntes, Cita::count());
        $this->assertDatabaseMissing('citas', [
            'user_id' => $this->cliente1->id,
            'hora_inicio' => $hora,
        ]);
    }

    /**
     * Criterio 4: El usuario puede volver a consultar y seleccionar un horario disponible.
     */
    public function test_criterio_4_usuario_puede_volver_a_consultar_y_seleccionar_horario_disponible(): void
    {
        $fecha = $this->getNextDateForDay(1);
        $horaOcupada = '15:00';
        $horaLibre = '15:30';

        // 1. Se ocupa 15:00
        Cita::create([
            'user_id' => $this->cliente2->id,
            'trabajador_id' => $this->trabajador->id,
            'service_id' => $this->servicioCorte->id,
            'fecha' => $fecha,
            'hora_inicio' => $horaOcupada,
            'hora_fin' => '15:30',
            'duracion_minutos' => 30,
            'precio' => 220.00,
            'estado' => 'confirmada',
        ]);

        // 2. Consulta disponibilidad fresca (equivalente a la acción tras cerrar el modal de alerta)
        $resDisponibilidad = $this->actingAs($this->cliente1)->getJson(route('cliente.agendamiento.disponibilidad', [
            'service_id' => $this->servicioCorte->id,
            'worker_id' => $this->trabajador->id,
            'date' => $fecha,
        ]));

        $resDisponibilidad->assertStatus(200);
        $horarios = $resDisponibilidad->json('horarios');

        // El horario ocupado 15:00 no está disponible
        $this->assertNotContains($horaOcupada, $horarios);
        // El horario libre 15:30 sí está disponible
        $this->assertContains($horaLibre, $horarios);

        // 3. El usuario selecciona el horario disponible y confirma exitosamente
        $resConfirmar = $this->actingAs($this->cliente1)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->servicioCorte->id,
            'worker_id' => $this->trabajador->id,
            'date' => $fecha,
            'time' => $horaLibre,
        ]);

        $resConfirmar->assertStatus(200);
        $this->assertTrue($resConfirmar->json('success'));
        $this->assertDatabaseHas('citas', [
            'user_id' => $this->cliente1->id,
            'hora_inicio' => $horaLibre,
            'estado' => 'confirmada',
        ]);
    }

    /**
     * Criterio 5: La alerta no aparece cuando la reservación se realiza en un horario válido que continúa disponible.
     */
    public function test_criterio_5_alerta_no_aparece_cuando_reservacion_se_realiza_en_horario_valido_disponible(): void
    {
        $fecha = $this->getNextDateForDay(1);
        $horaDisponible = '16:00';

        // 1. Verificación previa de horario libre responde valido: true sin activar alerta
        $resVal = $this->actingAs($this->cliente1)->postJson(route('cliente.agendamiento.validar-horario'), [
            'service_id' => $this->servicioCorte->id,
            'worker_id' => $this->trabajador->id,
            'date' => $fecha,
            'time' => $horaDisponible,
        ]);

        $resVal->assertStatus(200);
        $this->assertTrue($resVal->json('valido'));
        $this->assertTrue($resVal->json('success'));

        // 2. Registro definitivo responde 200 con éxito, confirmando guardado en BD
        $resConf = $this->actingAs($this->cliente1)->postJson(route('cliente.agendamiento.confirmar'), [
            'service_id' => $this->servicioCorte->id,
            'worker_id' => $this->trabajador->id,
            'date' => $fecha,
            'time' => $horaDisponible,
        ]);

        $resConf->assertStatus(200);
        $this->assertTrue($resConf->json('success'));
        $this->assertTrue($resConf->json('valido'));
        $this->assertNotNull($resConf->json('cita.id'));

        // Cita guardada correctamente
        $this->assertDatabaseHas('citas', [
            'id' => $resConf->json('cita.id'),
            'hora_inicio' => $horaDisponible,
            'estado' => 'confirmada',
        ]);
    }

    /**
     * Prueba que los bloqueos de disponibilidad del trabajador se retornan con su mensaje/leyenda
     * para que al seleccionarlos en la interfaz se despliegue el aviso correspondiente.
     */
    public function test_bloqueo_trabajador_retorna_horarios_bloqueados_con_leyenda_explicativa_en_recepcion_y_cliente(): void
    {
        $fecha = $this->getNextDateForDay(2); // Martes

        // Asignar un bloqueo al trabajador para esa fecha
        $horario = $this->trabajador->horario;
        $horario['bloqueos'] = [
            [
                'fecha' => $fecha,
                'inicio' => '14:00',
                'fin' => '16:00',
                'motivo' => 'Capacitación Interna de Barbería',
            ],
        ];
        $this->trabajador->update(['horario' => $horario]);

        // 1. Consulta desde Recepción
        $resRecepcion = $this->actingAs($this->recepcionista)->getJson(
            route('recepcion.agendamiento.disponibilidad', [
                'service_id' => $this->servicioCorte->id,
                'worker_id' => $this->trabajador->id,
                'date' => $fecha,
            ])
        );

        $resRecepcion->assertStatus(200);
        $resRecepcion->assertJsonStructure([
            'horarios',
            'horarios_bloqueados',
        ]);

        $bloqueadosRec = $resRecepcion->json('horarios_bloqueados');
        $this->assertNotEmpty($bloqueadosRec);
        $horasBloqueadasRec = collect($bloqueadosRec)->pluck('hora')->all();
        $this->assertContains('14:00', $horasBloqueadasRec);

        $primerBloqueoRec = collect($bloqueadosRec)->firstWhere('hora', '14:00');
        $this->assertEquals('bloqueo', $primerBloqueoRec['tipo']);
        $this->assertStringContainsString('Capacitación Interna de Barbería', $primerBloqueoRec['mensaje']);
        $this->assertStringContainsString('14:00 a 16:00 hrs', $primerBloqueoRec['mensaje']);

        // 2. Consulta desde Cliente
        $resCliente = $this->actingAs($this->cliente1)->getJson(
            route('cliente.agendamiento.disponibilidad', [
                'service_id' => $this->servicioCorte->id,
                'worker_id' => $this->trabajador->id,
                'date' => $fecha,
            ])
        );

        $resCliente->assertStatus(200);
        $bloqueadosCli = $resCliente->json('horarios_bloqueados');
        $this->assertNotEmpty($bloqueadosCli);
        $primerBloqueoCli = collect($bloqueadosCli)->firstWhere('hora', '14:00');
        $this->assertEquals('bloqueo', $primerBloqueoCli['tipo']);
        $this->assertStringContainsString('Capacitación Interna de Barbería', $primerBloqueoCli['mensaje']);

        // 3. Validación del horario bloqueado devuelve mensaje de no disponibilidad con la leyenda
        $resVal = $this->actingAs($this->recepcionista)->postJson(
            route('recepcion.agendamiento.validar-horario'),
            [
                'client_id' => $this->cliente1->id,
                'service_id' => $this->servicioCorte->id,
                'worker_id' => $this->trabajador->id,
                'date' => $fecha,
                'time' => '14:00',
            ]
        );

        $this->assertFalse($resVal->json('valido'));
        $this->assertEquals('bloqueo', $resVal->json('motivo_tipo'));
        $this->assertStringContainsString('Capacitación Interna de Barbería', $resVal->json('mensaje'));
    }

    /**
     * Prueba que las plantillas de recepcion y cliente incluyen el soporte de data-bloqueado
     * para reaccionar al seleccionar un horario con bloqueo de disponibilidad.
     */
    public function test_vistas_contienen_soporte_para_seleccion_de_horarios_bloqueados(): void
    {
        $this->actingAs($this->recepcionista);
        $viewRecepcion = view('recepcion.agendamiento', [
            'user' => $this->recepcionista,
            'servicios' => collect([$this->servicioCorte]),
            'serviciosBarberia' => collect([$this->servicioCorte]),
            'serviciosEstetica' => collect(),
            'trabajadores' => collect([$this->trabajador]),
            'clientes' => collect([$this->cliente1]),
            'clientePreseleccionado' => null,
        ])->render();

        $this->assertStringContainsString('data-bloqueado', $viewRecepcion);
        $this->assertStringContainsString('horarios_bloqueados', $viewRecepcion);
        $this->assertStringContainsString('mostrarAvisoHorarioNoDisponible', $viewRecepcion);

        $this->actingAs($this->cliente1);
        $viewCliente = view('cliente.agendamiento', [
            'user' => $this->cliente1,
            'servicios' => collect([$this->servicioCorte]),
            'serviciosBarberia' => collect([$this->servicioCorte]),
            'serviciosEstetica' => collect(),
            'trabajadores' => collect([$this->trabajador]),
        ])->render();

        $this->assertStringContainsString('data-bloqueado', $viewCliente);
        $this->assertStringContainsString('horarios_bloqueados', $viewCliente);
        $this->assertStringContainsString('mostrarAvisoHorarioNoDisponible', $viewCliente);
    }
}

