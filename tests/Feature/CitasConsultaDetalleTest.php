<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\Service;
use App\Models\Trabajador;
use App\Models\User;
use App\Services\CitaCalendarioService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CitasConsultaDetalleTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $recepcionistaUser;
    protected User $trabajadorUser;
    protected User $clienteUser;
    protected User $clienteUser2;
    protected Trabajador $trabajador1;
    protected Trabajador $trabajador2;
    protected Service $servicioCorte;
    protected Service $servicioColor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\TestUsersSeeder::class);

        $this->adminUser = User::where('role_id', 1)->first();
        $this->recepcionistaUser = User::where('role_id', 2)->first();
        $this->trabajadorUser = User::where('role_id', 3)->first();
        $this->clienteUser = User::where('role_id', 4)->first();

        // Crear un segundo cliente para pruebas de búsqueda
        $this->clienteUser2 = User::create([
            'name' => 'Mariana',
            'apellidos' => 'González López',
            'email' => 'mariana.gonzalez@example.com',
            'telefono' => '5559876543',
            'password' => bcrypt('password123'),
            'role_id' => 4,
        ]);

        // Crear trabajador 1
        $this->trabajador1 = Trabajador::create([
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

        // Crear trabajador 2 con un nuevo usuario trabajador
        $userTrabajador2 = User::create([
            'name' => 'Carlos',
            'apellidos' => 'Ramírez Morales',
            'email' => 'carlos.ramirez@barberstudio.com',
            'telefono' => '5551122334',
            'password' => bcrypt('password123'),
            'role_id' => 3,
        ]);

        $this->trabajador2 = Trabajador::create([
            'user_id' => $userTrabajador2->id,
            'direccion' => 'Beauty & Barber Studio - Estación 2',
            'experiencia' => 3,
            'activo' => true,
            'horario' => [
                'lunes' => ['inicio' => '10:00', 'fin' => '18:00', 'laboral' => true],
                'martes' => ['inicio' => '10:00', 'fin' => '18:00', 'laboral' => true],
                'miercoles' => ['inicio' => '10:00', 'fin' => '18:00', 'laboral' => true],
                'jueves' => ['inicio' => '10:00', 'fin' => '18:00', 'laboral' => true],
                'viernes' => ['inicio' => '10:00', 'fin' => '18:00', 'laboral' => true],
                'sabado' => ['inicio' => '10:00', 'fin' => '18:00', 'laboral' => true],
                'domingo' => ['laboral' => false],
            ]
        ]);

        // Crear servicios
        $this->servicioCorte = Service::create([
            'name' => 'Corte Degradado Barber',
            'category' => 'barberia',
            'duration_minutes' => 45,
            'price' => 250.00,
            'is_active' => true,
        ]);

        $this->servicioColor = Service::create([
            'name' => 'Balayage Premium Estética',
            'category' => 'estetica',
            'duration_minutes' => 120,
            'price' => 1200.00,
            'is_active' => true,
        ]);

        Cita::ensureTableExists();
    }

    /**
     * 1. Consulta de citas existentes con sus relaciones.
     */
    public function test_1_consulta_citas_existentes_con_sus_relaciones(): void
    {
        $cita = Cita::create([
            'user_id' => $this->clienteUser->id,
            'trabajador_id' => $this->trabajador1->id,
            'service_id' => $this->servicioCorte->id,
            'fecha' => Carbon::today()->format('Y-m-d'),
            'hora_inicio' => '10:00',
            'hora_fin' => '10:45',
            'duracion_minutos' => 45,
            'precio' => 250.00,
            'estado' => 'confirmada',
            'notas' => 'Cliente prefiere corte con tijera arriba',
        ]);

        // Consulta Recepción
        $responseRec = $this->actingAs($this->recepcionistaUser)->get('/recepcion/citas');
        $responseRec->assertStatus(200);
        $responseRec->assertSee($cita->folio);
        $responseRec->assertSee($this->clienteUser->name);
        $responseRec->assertSee('Corte Degradado Barber');
        $responseRec->assertSee('250.00');

        // Consulta Administrador
        $responseAdm = $this->actingAs($this->adminUser)->get('/admin/citas?tab=listado');
        $responseAdm->assertStatus(200);
        $responseAdm->assertSee($cita->folio);
        $responseAdm->assertSee($this->clienteUser->name);

        // Consulta vía JSON
        $responseJson = $this->actingAs($this->recepcionistaUser)
            ->getJson('/recepcion/citas');
        $responseJson->assertStatus(200);
        $responseJson->assertJsonFragment([
            'id' => $cita->id,
            'folio' => $cita->folio,
        ]);
    }

    /**
     * 2. Consulta de citas canceladas en el historial.
     */
    public function test_2_consulta_citas_canceladas_en_el_historial(): void
    {
        $citaCancelada = Cita::create([
            'user_id' => $this->clienteUser->id,
            'trabajador_id' => $this->trabajador1->id,
            'service_id' => $this->servicioCorte->id,
            'fecha' => Carbon::yesterday()->format('Y-m-d'),
            'hora_inicio' => '11:00',
            'hora_fin' => '11:45',
            'duracion_minutos' => 45,
            'precio' => 250.00,
            'estado' => 'cancelada',
            'observaciones' => 'Cancelada por solicitud del cliente vía telefónica',
        ]);

        // Verificación: la cita cancelada sigue existiendo y visible en el listado general
        $response = $this->actingAs($this->recepcionistaUser)->get('/recepcion/citas');
        $response->assertStatus(200);
        $response->assertSee($citaCancelada->folio);
        $response->assertSee('Cancelada');

        // Verificación con filtro explícito por estado cancelada
        $responseFiltro = $this->actingAs($this->recepcionistaUser)
            ->get('/recepcion/citas?estado=cancelada');
        $responseFiltro->assertStatus(200);
        $responseFiltro->assertSee($citaCancelada->folio);

        // Verificación en Admin
        $responseAdmin = $this->actingAs($this->adminUser)
            ->get('/admin/citas?tab=listado&estado=cancelada');
        $responseAdmin->assertStatus(200);
        $responseAdmin->assertSee($citaCancelada->folio);
    }

    /**
     * 3. Visualización correcta de los detalles de diferentes citas.
     */
    public function test_3_visualizacion_correcta_de_detalles_de_diferentes_citas(): void
    {
        $cita1 = Cita::create([
            'user_id' => $this->clienteUser->id,
            'trabajador_id' => $this->trabajador1->id,
            'service_id' => $this->servicioCorte->id,
            'fecha' => '2026-10-20',
            'hora_inicio' => '09:00',
            'hora_fin' => '09:45',
            'duracion_minutos' => 45,
            'precio' => 250.00,
            'estado' => 'confirmada',
            'notas' => 'Primera cita de prueba',
        ]);

        $cita2 = Cita::create([
            'user_id' => $this->clienteUser2->id,
            'trabajador_id' => $this->trabajador2->id,
            'service_id' => $this->servicioColor->id,
            'fecha' => '2026-10-21',
            'hora_inicio' => '14:00',
            'hora_fin' => '16:00',
            'duracion_minutos' => 120,
            'precio' => 1200.00,
            'estado' => 'completada',
            'notas' => 'Tratamiento capilar previo',
            'observaciones' => 'Resultado excelente con matiz dorado',
        ]);

        // Detalle de Cita 1
        $res1 = $this->actingAs($this->recepcionistaUser)->getJson("/recepcion/citas/{$cita1->id}/detalle");
        $res1->assertStatus(200);
        $res1->assertJsonPath('cita.id', $cita1->id);
        $res1->assertJsonPath('cita.folio', $cita1->folio);
        $res1->assertJsonPath('cita.cliente.nombre_completo', trim($this->clienteUser->name . ' ' . ($this->clienteUser->apellidos ?? '')));
        $res1->assertJsonPath('cita.servicio.nombre', 'Corte Degradado Barber');
        $res1->assertJsonPath('cita.notas', 'Primera cita de prueba');

        // Detalle de Cita 2
        $res2 = $this->actingAs($this->adminUser)->getJson("/admin/citas/{$cita2->id}/detalle");
        $res2->assertStatus(200);
        $res2->assertJsonPath('cita.id', $cita2->id);
        $res2->assertJsonPath('cita.folio', $cita2->folio);
        $res2->assertJsonPath('cita.cliente.nombre_completo', trim($this->clienteUser2->name . ' ' . $this->clienteUser2->apellidos));
        $res2->assertJsonPath('cita.servicio.nombre', 'Balayage Premium Estética');
        $res2->assertJsonPath('cita.precio', 1200.00);
        $res2->assertJsonPath('cita.observaciones', 'Resultado excelente con matiz dorado');
    }

    /**
     * 4. Filtrado por fecha.
     */
    public function test_4_filtrado_por_fecha(): void
    {
        $fechaObjetivo = '2026-11-05';
        $fechaOtra = '2026-11-12';

        $citaEnFecha = Cita::create([
            'user_id' => $this->clienteUser->id,
            'trabajador_id' => $this->trabajador1->id,
            'service_id' => $this->servicioCorte->id,
            'fecha' => $fechaObjetivo,
            'hora_inicio' => '10:00',
            'hora_fin' => '10:45',
            'duracion_minutos' => 45,
            'precio' => 250.00,
            'estado' => 'confirmada',
        ]);

        $citaFueraFecha = Cita::create([
            'user_id' => $this->clienteUser->id,
            'trabajador_id' => $this->trabajador1->id,
            'service_id' => $this->servicioCorte->id,
            'fecha' => $fechaOtra,
            'hora_inicio' => '10:00',
            'hora_fin' => '10:45',
            'duracion_minutos' => 45,
            'precio' => 250.00,
            'estado' => 'confirmada',
        ]);

        // Filtrar por fecha puntual
        $response = $this->actingAs($this->recepcionistaUser)
            ->get("/recepcion/citas?fecha_desde={$fechaObjetivo}&fecha_hasta={$fechaObjetivo}");
        $response->assertStatus(200);
        $response->assertSee($citaEnFecha->folio);
        $response->assertDontSee($citaFueraFecha->folio);
    }

    /**
     * 5. Filtrado por estado.
     */
    public function test_5_filtrado_por_estado(): void
    {
        $citaConfirmada = Cita::create([
            'user_id' => $this->clienteUser->id,
            'trabajador_id' => $this->trabajador1->id,
            'service_id' => $this->servicioCorte->id,
            'fecha' => '2026-12-01',
            'hora_inicio' => '10:00',
            'hora_fin' => '10:45',
            'duracion_minutos' => 45,
            'precio' => 250.00,
            'estado' => 'confirmada',
        ]);

        $citaCompletada = Cita::create([
            'user_id' => $this->clienteUser->id,
            'trabajador_id' => $this->trabajador1->id,
            'service_id' => $this->servicioCorte->id,
            'fecha' => '2026-12-01',
            'hora_inicio' => '11:00',
            'hora_fin' => '11:45',
            'duracion_minutos' => 45,
            'precio' => 250.00,
            'estado' => 'completada',
        ]);

        $response = $this->actingAs($this->recepcionistaUser)
            ->get('/recepcion/citas?estado=completada');
        $response->assertStatus(200);
        $response->assertSee($citaCompletada->folio);
        $response->assertDontSee($citaConfirmada->folio);
    }

    /**
     * 6. Búsqueda por cliente.
     */
    public function test_6_busqueda_por_cliente(): void
    {
        $citaMariana = Cita::create([
            'user_id' => $this->clienteUser2->id,
            'trabajador_id' => $this->trabajador1->id,
            'service_id' => $this->servicioColor->id,
            'fecha' => '2026-11-20',
            'hora_inicio' => '12:00',
            'hora_fin' => '14:00',
            'duracion_minutos' => 120,
            'precio' => 1200.00,
            'estado' => 'confirmada',
        ]);

        $citaOtro = Cita::create([
            'user_id' => $this->clienteUser->id,
            'trabajador_id' => $this->trabajador1->id,
            'service_id' => $this->servicioCorte->id,
            'fecha' => '2026-11-20',
            'hora_inicio' => '15:00',
            'hora_fin' => '15:45',
            'duracion_minutos' => 45,
            'precio' => 250.00,
            'estado' => 'confirmada',
        ]);

        // Búsqueda por nombre de Mariana
        $responseNombre = $this->actingAs($this->recepcionistaUser)
            ->get('/recepcion/citas?buscar=Mariana');
        $responseNombre->assertStatus(200);
        $responseNombre->assertSee($citaMariana->folio);
        $responseNombre->assertDontSee($citaOtro->folio);

        // Búsqueda por teléfono
        $responseTel = $this->actingAs($this->recepcionistaUser)
            ->get('/recepcion/citas?buscar=5559876543');
        $responseTel->assertStatus(200);
        $responseTel->assertSee($citaMariana->folio);
        $responseTel->assertDontSee($citaOtro->folio);

        // Búsqueda por folio exacto
        $responseFolio = $this->actingAs($this->recepcionistaUser)
            ->get("/recepcion/citas?buscar={$citaMariana->folio}");
        $responseFolio->assertStatus(200);
        $responseFolio->assertSee($citaMariana->folio);
        $responseFolio->assertDontSee($citaOtro->folio);
    }

    /**
     * 7. Filtrado por trabajador.
     */
    public function test_7_filtrado_por_trabajador(): void
    {
        $citaTrabajador1 = Cita::create([
            'user_id' => $this->clienteUser->id,
            'trabajador_id' => $this->trabajador1->id,
            'service_id' => $this->servicioCorte->id,
            'fecha' => '2026-11-25',
            'hora_inicio' => '10:00',
            'hora_fin' => '10:45',
            'duracion_minutos' => 45,
            'precio' => 250.00,
            'estado' => 'confirmada',
        ]);

        $citaTrabajador2 = Cita::create([
            'user_id' => $this->clienteUser->id,
            'trabajador_id' => $this->trabajador2->id,
            'service_id' => $this->servicioColor->id,
            'fecha' => '2026-11-25',
            'hora_inicio' => '12:00',
            'hora_fin' => '14:00',
            'duracion_minutos' => 120,
            'precio' => 1200.00,
            'estado' => 'confirmada',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get("/admin/citas?tab=listado&trabajador_id={$this->trabajador2->id}");
        $response->assertStatus(200);
        $response->assertSee($citaTrabajador2->folio);
        $response->assertDontSee($citaTrabajador1->folio);
    }

    /**
     * 8. Combinación de varios filtros.
     */
    public function test_8_combinacion_de_varios_filtros(): void
    {
        $fecha = '2026-12-15';

        // Coincide con todos los criterios: trabajador 1 + fecha + estado confirmada
        $citaCoincidente = Cita::create([
            'user_id' => $this->clienteUser->id,
            'trabajador_id' => $this->trabajador1->id,
            'service_id' => $this->servicioCorte->id,
            'fecha' => $fecha,
            'hora_inicio' => '09:00',
            'hora_fin' => '09:45',
            'duracion_minutos' => 45,
            'precio' => 250.00,
            'estado' => 'confirmada',
        ]);

        // Mismo trabajador y fecha, pero estado cancelada
        $citaDistintoEstado = Cita::create([
            'user_id' => $this->clienteUser->id,
            'trabajador_id' => $this->trabajador1->id,
            'service_id' => $this->servicioCorte->id,
            'fecha' => $fecha,
            'hora_inicio' => '10:00',
            'hora_fin' => '10:45',
            'duracion_minutos' => 45,
            'precio' => 250.00,
            'estado' => 'cancelada',
        ]);

        // Misma fecha y estado confirmada, pero otro trabajador
        $citaDistintoTrabajador = Cita::create([
            'user_id' => $this->clienteUser->id,
            'trabajador_id' => $this->trabajador2->id,
            'service_id' => $this->servicioColor->id,
            'fecha' => $fecha,
            'hora_inicio' => '11:00',
            'hora_fin' => '13:00',
            'duracion_minutos' => 120,
            'precio' => 1200.00,
            'estado' => 'confirmada',
        ]);

        $queryUrl = "/recepcion/citas?trabajador_id={$this->trabajador1->id}&estado=confirmada&fecha_desde={$fecha}&fecha_hasta={$fecha}";
        $response = $this->actingAs($this->recepcionistaUser)->get($queryUrl);

        $response->assertStatus(200);
        $response->assertSee($citaCoincidente->folio);
        $response->assertDontSee($citaDistintoEstado->folio);
        $response->assertDontSee($citaDistintoTrabajador->folio);
    }

    /**
     * 9. Verificación de permisos para administrador, recepcionista y roles no autorizados.
     */
    public function test_9_verificacion_de_permisos_admin_recepcionista_y_roles_no_autorizados(): void
    {
        $cita = Cita::create([
            'user_id' => $this->clienteUser->id,
            'trabajador_id' => $this->trabajador1->id,
            'service_id' => $this->servicioCorte->id,
            'fecha' => Carbon::today()->format('Y-m-d'),
            'hora_inicio' => '10:00',
            'hora_fin' => '10:45',
            'duracion_minutos' => 45,
            'precio' => 250.00,
            'estado' => 'confirmada',
        ]);

        // Administrador tiene acceso a panel admin y recepcion
        $this->actingAs($this->adminUser)->get('/admin/citas')->assertStatus(200);
        $this->actingAs($this->adminUser)->get("/admin/citas/{$cita->id}/detalle")->assertStatus(200);
        $this->actingAs($this->adminUser)->get('/recepcion/citas')->assertStatus(200);

        // Recepcionista tiene acceso a recepcion pero NO al panel admin
        $this->actingAs($this->recepcionistaUser)->get('/recepcion/citas')->assertStatus(200);
        $this->actingAs($this->recepcionistaUser)->get("/recepcion/citas/{$cita->id}/detalle")->assertStatus(200);
        $this->actingAs($this->recepcionistaUser)->get('/admin/citas')->assertStatus(403);

        // Cliente no tiene acceso a las rutas administrativas de gestión ni detalle
        $this->actingAs($this->clienteUser)->get('/recepcion/citas')->assertStatus(403);
        $this->actingAs($this->clienteUser)->get('/admin/citas')->assertStatus(403);
        $this->actingAs($this->clienteUser)->get("/recepcion/citas/{$cita->id}/detalle")->assertStatus(403);

        // Usuario sin autenticar es redirigido a login
        $this->get('/recepcion/citas')->assertRedirect('/login');
        $this->get('/admin/citas')->assertRedirect('/login');
    }

    /**
     * 10. Manejo correcto de periodos sin resultados.
     */
    public function test_10_manejo_correcto_de_periodos_sin_resultados(): void
    {
        $fechaFutura = '2030-01-01';

        $response = $this->actingAs($this->recepcionistaUser)
            ->get("/recepcion/citas?fecha_desde={$fechaFutura}&fecha_hasta={$fechaFutura}");

        $response->assertStatus(200);
        $response->assertSee('No se encontraron citas con los criterios seleccionados');
        $response->assertSee('citas encontradas');
    }

    /**
     * 11. Comprobación de que consultar los detalles no modifica los datos de la cita.
     */
    public function test_11_comprobacion_de_que_consultar_detalles_no_modifica_los_datos_de_la_cita(): void
    {
        $cita = Cita::create([
            'user_id' => $this->clienteUser->id,
            'trabajador_id' => $this->trabajador1->id,
            'service_id' => $this->servicioCorte->id,
            'fecha' => '2026-10-15',
            'hora_inicio' => '10:00',
            'hora_fin' => '10:45',
            'duracion_minutos' => 45,
            'precio' => 250.00,
            'estado' => 'confirmada',
            'notas' => 'Nota original inmutable',
            'observaciones' => 'Observación original',
        ]);

        $updatedAtOriginal = $cita->fresh()->updated_at;
        $estadoOriginal = $cita->estado;

        // Realizar varias consultas del detalle
        $this->actingAs($this->recepcionistaUser)->getJson("/recepcion/citas/{$cita->id}/detalle")->assertStatus(200);
        $this->actingAs($this->adminUser)->getJson("/admin/citas/{$cita->id}/detalle")->assertStatus(200);

        $citaDespues = $cita->fresh();

        $this->assertEquals($estadoOriginal, $citaDespues->estado);
        $this->assertEquals('Nota original inmutable', $citaDespues->notas);
        $this->assertEquals('Observación original', $citaDespues->observaciones);
        $this->assertEquals(250.00, (float)$citaDespues->precio);
        $this->assertEquals($updatedAtOriginal->toISOString(), $citaDespues->updated_at->toISOString());
    }

    /**
     * 12. Verificación de que los resultados sean coherentes con los datos almacenados.
     */
    public function test_12_verificacion_de_que_los_resultados_sean_coherentes_con_los_datos_almacenados(): void
    {
        $cita = Cita::create([
            'user_id' => $this->clienteUser->id,
            'trabajador_id' => $this->trabajador1->id,
            'service_id' => $this->servicioCorte->id,
            'fecha' => '2026-10-18',
            'hora_inicio' => '16:00',
            'hora_fin' => '16:45',
            'duracion_minutos' => 45,
            'precio' => 320.50,
            'estado' => 'confirmada',
            'notas' => 'Prueba de consistencia exacta',
        ]);

        $servicio = app(CitaCalendarioService::class);
        $detalle = $servicio->obtenerDetalleCita($cita);

        $this->assertEquals($cita->id, $detalle['id']);
        $this->assertEquals('CITA-' . str_pad((string)$cita->id, 5, '0', STR_PAD_LEFT), $detalle['folio']);
        $this->assertEquals('16:00', $detalle['hora_inicio']);
        $this->assertEquals('16:45', $detalle['hora_fin']);
        $this->assertEquals(45, $detalle['duracion_minutos']);
        $this->assertEquals(320.50, $detalle['precio']);
        $this->assertEquals('$320.50', $detalle['precio_formateado']);
        $this->assertEquals($this->clienteUser->id, $detalle['cliente']['id']);
        $this->assertEquals($this->trabajador1->id, $detalle['trabajador']['id']);
        $this->assertEquals($this->servicioCorte->id, $detalle['servicio']['id']);
        $this->assertEquals('Prueba de consistencia exacta', $detalle['notas']);
    }
}
