<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\Service;
use App\Models\Trabajador;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ClienteDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('roles')->insertOrIgnore([
            ['id' => 1, 'nombre' => 'Administrador'],
            ['id' => 2, 'nombre' => 'Recepcionista'],
            ['id' => 3, 'nombre' => 'Trabajador'],
            ['id' => 4, 'nombre' => 'Cliente'],
        ]);

        Cita::ensureTableExists();
    }

    private function crearCliente(array $attributes = []): User
    {
        return User::create(array_merge([
            'name' => 'Cliente',
            'apellidos' => 'Prueba',
            'email' => 'cliente_' . uniqid() . '@test.com',
            'telefono' => '55' . rand(10000000, 99999999),
            'password' => bcrypt('password123'),
            'role_id' => 4,
        ], $attributes));
    }

    private function crearTrabajador(string $nombre = 'Alejandro', string $apellidos = 'Torres'): Trabajador
    {
        $user = User::create([
            'name' => $nombre,
            'apellidos' => $apellidos,
            'email' => 'trabajador_' . uniqid() . '@test.com',
            'telefono' => '55' . rand(10000000, 99999999),
            'password' => bcrypt('password123'),
            'role_id' => 3,
        ]);

        return Trabajador::create([
            'user_id' => $user->id,
            'direccion' => 'Av. Central 123',
            'experiencia' => 4,
            'activo' => true,
        ]);
    }

    private function crearServicio(string $nombre = 'Corte Clásico Barber', string $categoria = 'barberia'): Service
    {
        return Service::create([
            'name' => $nombre,
            'description' => 'Descripción del servicio ' . $nombre,
            'price' => 250.00,
            'duration_minutes' => 30,
            'category' => $categoria,
            'is_active' => true,
        ]);
    }

    private function crearCita(array $attributes = []): Cita
    {
        return Cita::create(array_merge([
            'fecha' => Carbon::now()->addDays(2)->format('Y-m-d'),
            'hora_inicio' => '10:00',
            'hora_fin' => '10:30',
            'duracion_minutos' => 30,
            'precio' => 200.00,
            'estado' => 'confirmada',
        ], $attributes));
    }

    /**
     * Comprueba que usuarios sin sesión son redirigidos al login.
     */
    public function test_usuario_no_autenticado_es_redirigido_al_login(): void
    {
        $response = $this->get('/cliente');
        $response->assertRedirect(route('login'));

        $responseAgendamiento = $this->get('/cliente/agendamiento');
        $responseAgendamiento->assertRedirect(route('login'));

        $responseHistorial = $this->get('/cliente/historial');
        $responseHistorial->assertRedirect(route('login'));
    }

    /**
     * Comprueba que un cliente autenticado puede ver el panel principal con su información y navegación.
     */
    public function test_cliente_autenticado_puede_ver_el_panel_principal(): void
    {
        $user = new User([
            'id' => 999,
            'name' => 'Roberto',
            'apellidos' => 'Sánchez',
            'email' => 'roberto.cliente@test.com',
            'telefono' => '5556543210',
            'role_id' => 4,
        ]);

        $response = $this->actingAs($user)->get('/cliente');

        $response->assertStatus(200);
        $response->assertSee('¡Hola, Roberto!');
        $response->assertSee('Portal Cliente');
        $response->assertSee('Inicio');
        $response->assertSee('Agendamiento');
        $response->assertSee('Historial de citas');
        $response->assertSee('Cerrar sesión');
        $response->assertSee('roberto.cliente@test.com');
    }

    /**
     * Comprueba que el cliente puede acceder visualmente al apartado de agendamiento.
     */
    public function test_cliente_puede_acceder_al_apartado_de_agendamiento(): void
    {
        $user = new User([
            'id' => 999,
            'name' => 'Roberto',
            'apellidos' => 'Sánchez',
            'email' => 'roberto.cliente@test.com',
            'telefono' => '5556543210',
            'role_id' => 4,
        ]);

        $response = $this->actingAs($user)->get('/cliente/agendamiento');

        $response->assertStatus(200);
        $response->assertSee('Agendamiento');
        $response->assertSee('Catálogo de Servicios');
        $response->assertSee('Barbería');
        $response->assertSee('Estética');
        $response->assertSee('Selecciona un servicio');
        $response->assertSee('Selecciona un trabajador');
        $response->assertSee('Fecha de la cita');
        $response->assertSee('Horario de atención');
        $response->assertSee('Resumen del Servicio');
        $response->assertSee('Continuar');
        $response->assertSee('Regresar');
        $response->assertSee('Inicio');
        $response->assertSee('Historial de citas');
    }

    /**
     * Comprueba que el catálogo organiza los servicios en categorías Barbería y Estética con sus tarjetas.
     */
    public function test_catalogo_de_servicios_distingue_categorias_barberia_y_estetica(): void
    {
        $user = new User([
            'id' => 999,
            'name' => 'Roberto',
            'email' => 'roberto.cliente@test.com',
            'role_id' => 4,
        ]);

        $response = $this->actingAs($user)->get('/cliente/agendamiento');

        $response->assertStatus(200);
        $response->assertSee('Catálogo de Servicios');
        $response->assertSee('Barbería');
        $response->assertSee('Estética');
        $response->assertSee('id="categoryBlockBarberia"', false);
        $response->assertSee('id="categoryBlockEstetica"', false);
    }

    /**
     * Comprueba que el cliente puede acceder visualmente al apartado de historial de citas.
     */
    public function test_cliente_puede_acceder_al_apartado_de_historial_de_citas(): void
    {
        $user = new User([
            'id' => 999,
            'name' => 'Roberto',
            'apellidos' => 'Sánchez',
            'email' => 'roberto.cliente@test.com',
            'telefono' => '5556543210',
            'role_id' => 4,
        ]);

        $response = $this->actingAs($user)->get('/cliente/historial');

        $response->assertStatus(200);
        $response->assertSee('Historial de citas');
        $response->assertSee('Inicio');
        $response->assertSee('Agendamiento');
    }

    /**
     * Comprueba que usuarios con rol trabajador o recepcionista son redirigidos a su respectivo panel.
     */
    public function test_otros_roles_no_pueden_acceder_al_panel_de_cliente(): void
    {
        $trabajador = new User([
            'id' => 1001,
            'name' => 'Alejandro',
            'role_id' => 3,
        ]);

        $responseTrabajador = $this->actingAs($trabajador)->get('/cliente');
        $responseTrabajador->assertStatus(403);

        $recepcionista = new User([
            'id' => 1002,
            'name' => 'Laura',
            'role_id' => 2,
        ]);

        $responseRecepcionista = $this->actingAs($recepcionista)->get('/cliente');
        $responseRecepcionista->assertStatus(403);
    }

    /**
     * Comprueba que la bienvenida muestra el saludo personalizado con el nombre del cliente autenticado.
     */
    public function test_bienvenida_muestra_saludo_personalizado_con_nombre_del_cliente_autenticado(): void
    {
        $cliente = new User([
            'id' => 777,
            'name' => 'Carlos',
            'apellidos' => 'Mendoza',
            'email' => 'carlos.mendoza@test.com',
            'role_id' => 4,
        ]);

        $response = $this->actingAs($cliente)->get('/cliente');

        $response->assertStatus(200);
        $response->assertSee('¡Hola, Carlos! Te damos la bienvenida');
        $response->assertSee('Portal Cliente &bull; Beauty & Barber Studio', false);
        $response->assertSee('Este es tu panel personal en Beauty & Barber Studio', false);
        $response->assertDontSee('¡Te damos la bienvenida a Beauty & Barber Studio!', false);
    }

    /**
     * Comprueba que la bienvenida muestra un mensaje alternativo coherente si el nombre está vacío o no disponible.
     */
    public function test_bienvenida_muestra_mensaje_alternativo_coherente_cuando_el_nombre_no_esta_disponible(): void
    {
        $clienteSinNombre = new User([
            'id' => 888,
            'name' => '',
            'apellidos' => 'Gómez',
            'email' => 'cliente.anonimo@test.com',
            'role_id' => 4,
        ]);

        $response = $this->actingAs($clienteSinNombre)->get('/cliente');

        $response->assertStatus(200);
        $response->assertSee('¡Te damos la bienvenida a Beauty & Barber Studio!', false);
        $response->assertDontSee('¡Hola, !');
        $response->assertDontSee('¡Hola, null!');
        $response->assertDontSee('¡Hola, undefined!');
    }

    /**
     * Comprueba que la bienvenida no expone información de otros clientes.
     */
    public function test_bienvenida_no_expone_informacion_de_otros_clientes(): void
    {
        $clienteA = new User([
            'id' => 101,
            'name' => 'Valeria',
            'apellidos' => 'Ríos',
            'email' => 'valeria@test.com',
            'role_id' => 4,
        ]);

        $clienteB = new User([
            'id' => 102,
            'name' => 'Rodrigo',
            'apellidos' => 'Herrera',
            'email' => 'rodrigo@test.com',
            'role_id' => 4,
        ]);

        $response = $this->actingAs($clienteA)->get('/cliente');

        $response->assertStatus(200);
        $response->assertSee('¡Hola, Valeria!');
        $response->assertDontSee('Rodrigo');
        $response->assertDontSee('Herrera');
        $response->assertDontSee('rodrigo@test.com');
    }

    /**
     * Comprueba que el encabezado del cliente incluye accesibilidad, jerarquía y estructura requerida.
     */
    public function test_encabezado_del_cliente_incluye_accesibilidad_y_jerarquia(): void
    {
        $cliente = new User([
            'id' => 500,
            'name' => 'Esteban',
            'role_id' => 4,
        ]);

        $response = $this->actingAs($cliente)->get('/cliente');

        $response->assertStatus(200);
        $response->assertSee('aria-labelledby="welcomeTitle"', false);
        $response->assertSee('id="welcomeTitle"', false);
        $response->assertSee('class="welcome-banner"', false);
        $response->assertSee('class="welcome-banner-badge"', false);
        $response->assertSee('class="welcome-title"', false);
        $response->assertSee('class="welcome-subtitle"', false);
    }

    // =========================================================================
    // PRUEBAS DE LA SUBTAREA 2: RESUMEN DE LA PRÓXIMA CITA
    // =========================================================================

    /**
     * Escenario 1: Un cliente autenticado tiene una cita futura válida y se muestra su información.
     */
    public function test_cliente_autenticado_tiene_cita_futura_valida_y_se_muestra_su_informacion(): void
    {
        $cliente = $this->crearCliente(['name' => 'Mario', 'apellidos' => 'Bros']);
        $trabajador = $this->crearTrabajador('Carlos', 'Santana');
        $servicio = $this->crearServicio('Corte Ejecutivo & Barba', 'barberia');
        $fechaFutura = Carbon::now()->addDays(2)->format('Y-m-d');

        $this->crearCita([
            'user_id' => $cliente->id,
            'trabajador_id' => $trabajador->id,
            'service_id' => $servicio->id,
            'fecha' => $fechaFutura,
            'hora_inicio' => '10:00',
            'hora_fin' => '10:45',
            'duracion_minutos' => 45,
            'precio' => 250.00,
            'estado' => 'confirmada',
        ]);

        $response = $this->actingAs($cliente)->get('/cliente');

        $response->assertStatus(200);
        $response->assertSee('Tu Próxima Cita');
        $response->assertSee('Resumen de Cita Programada');
        $response->assertSee('Corte Ejecutivo &amp; Barba', false);
        $response->assertSee('Carlos Santana');
        $response->assertSee('10:00 hrs');
        $response->assertSee('Confirmada');
        $response->assertSee(route('cliente.agendamiento'));
        $response->assertSee('Ir a Agendamiento');
        $response->assertSee(route('cliente.historial'));
        $response->assertSee('Ver cita en el historial');
    }

    /**
     * Escenario 2: Existen varias citas futuras válidas y se selecciona la más próxima cronológicamente.
     */
    public function test_multiples_citas_futuras_validas_selecciona_la_mas_proxima_en_fecha_y_hora(): void
    {
        $cliente = $this->crearCliente(['name' => 'Luis']);
        $trabajador = $this->crearTrabajador();
        $servicioLejano = $this->crearServicio('Corte en Cinco Dias');
        $servicioTarde = $this->crearServicio('Corte en Dos Dias Tarde');
        $servicioMasProximo = $this->crearServicio('Corte en Dos Dias Temprano');

        $fecha2Dias = Carbon::now()->addDays(2)->format('Y-m-d');
        $fecha5Dias = Carbon::now()->addDays(5)->format('Y-m-d');

        // Cita en 5 días
        $this->crearCita([
            'user_id' => $cliente->id,
            'trabajador_id' => $trabajador->id,
            'service_id' => $servicioLejano->id,
            'fecha' => $fecha5Dias,
            'hora_inicio' => '12:00',
            'hora_fin' => '12:30',
            'estado' => 'confirmada',
        ]);

        // Cita en 2 días a las 16:00
        $this->crearCita([
            'user_id' => $cliente->id,
            'trabajador_id' => $trabajador->id,
            'service_id' => $servicioTarde->id,
            'fecha' => $fecha2Dias,
            'hora_inicio' => '16:00',
            'hora_fin' => '16:30',
            'estado' => 'confirmada',
        ]);

        // Cita en 2 días a las 09:00 (la más próxima)
        $this->crearCita([
            'user_id' => $cliente->id,
            'trabajador_id' => $trabajador->id,
            'service_id' => $servicioMasProximo->id,
            'fecha' => $fecha2Dias,
            'hora_inicio' => '09:00',
            'hora_fin' => '09:30',
            'estado' => 'confirmada',
        ]);

        $response = $this->actingAs($cliente)->get('/cliente');

        $response->assertStatus(200);
        $response->assertSee('Corte en Dos Dias Temprano');
        $response->assertSee('09:00 hrs');
        $response->assertDontSee('Corte en Cinco Dias');
        $response->assertDontSee('Corte en Dos Dias Tarde');
    }

    /**
     * Escenario 3: Existen citas canceladas que no deben mostrarse como próximas citas vigentes.
     */
    public function test_citas_canceladas_no_se_muestran_como_proximas_citas_vigentes(): void
    {
        $cliente = $this->crearCliente();
        $trabajador = $this->crearTrabajador();
        $servicioCancelado = $this->crearServicio('Servicio Cancelado Manana');
        $servicioValido = $this->crearServicio('Servicio Vigente en Tres Dias');

        $manana = Carbon::now()->addDay()->format('Y-m-d');
        $en3Dias = Carbon::now()->addDays(3)->format('Y-m-d');

        // Cita cancelada mañana (no debe seleccionarse a pesar de ser anterior)
        $this->crearCita([
            'user_id' => $cliente->id,
            'trabajador_id' => $trabajador->id,
            'service_id' => $servicioCancelado->id,
            'fecha' => $manana,
            'hora_inicio' => '10:00',
            'hora_fin' => '10:30',
            'estado' => 'cancelada',
        ]);

        // Cita confirmada en 3 días (debe ser la seleccionada)
        $this->crearCita([
            'user_id' => $cliente->id,
            'trabajador_id' => $trabajador->id,
            'service_id' => $servicioValido->id,
            'fecha' => $en3Dias,
            'hora_inicio' => '11:00',
            'hora_fin' => '11:30',
            'estado' => 'confirmada',
        ]);

        $response = $this->actingAs($cliente)->get('/cliente');

        $response->assertStatus(200);
        $response->assertSee('Servicio Vigente en Tres Dias');
        $response->assertSee('11:00 hrs');
        $response->assertDontSee('Servicio Cancelado Manana');
    }

    /**
     * Escenario 4: Existen citas finalizadas o pasadas que deben excluirse del resumen.
     */
    public function test_citas_finalizadas_o_pasadas_se_excluyen_del_resumen(): void
    {
        $cliente = $this->crearCliente();
        $trabajador = $this->crearTrabajador();
        $servicioCompletado = $this->crearServicio('Cita Ya Completada');
        $servicioPasado = $this->crearServicio('Cita Pasada de Ayer');
        $servicioVigente = $this->crearServicio('Cita Activa Futura');

        $ayer = Carbon::now()->subDays(2)->format('Y-m-d');
        $en4Dias = Carbon::now()->addDays(4)->format('Y-m-d');

        // Cita completada
        $this->crearCita([
            'user_id' => $cliente->id,
            'trabajador_id' => $trabajador->id,
            'service_id' => $servicioCompletado->id,
            'fecha' => $en4Dias,
            'hora_inicio' => '08:00',
            'hora_fin' => '08:30',
            'estado' => 'completada',
        ]);

        // Cita pasada en fecha
        $this->crearCita([
            'user_id' => $cliente->id,
            'trabajador_id' => $trabajador->id,
            'service_id' => $servicioPasado->id,
            'fecha' => $ayer,
            'hora_inicio' => '09:00',
            'hora_fin' => '09:30',
            'estado' => 'confirmada',
        ]);

        // Cita válida futura
        $this->crearCita([
            'user_id' => $cliente->id,
            'trabajador_id' => $trabajador->id,
            'service_id' => $servicioVigente->id,
            'fecha' => $en4Dias,
            'hora_inicio' => '15:00',
            'hora_fin' => '15:30',
            'estado' => 'confirmada',
        ]);

        $response = $this->actingAs($cliente)->get('/cliente');

        $response->assertStatus(200);
        $response->assertSee('Cita Activa Futura');
        $response->assertSee('15:00 hrs');
        $response->assertDontSee('Cita Ya Completada');
        $response->assertDontSee('Cita Pasada de Ayer');
    }

    /**
     * Escenario 5: Citas de distintos clientes y el resumen solo muestra información del cliente autenticado.
     */
    public function test_citas_de_distintos_clientes_solo_muestra_informacion_del_cliente_autenticado(): void
    {
        $clienteA = $this->crearCliente(['name' => 'Cliente Alpha']);
        $clienteB = $this->crearCliente(['name' => 'Cliente Beta']);
        $trabajador = $this->crearTrabajador();
        $servicioA = $this->crearServicio('Servicio Exclusivo Alpha');
        $servicioB = $this->crearServicio('Servicio Exclusivo Beta');

        $fechaFutura = Carbon::now()->addDays(2)->format('Y-m-d');

        // Cita de Cliente A
        $this->crearCita([
            'user_id' => $clienteA->id,
            'trabajador_id' => $trabajador->id,
            'service_id' => $servicioA->id,
            'fecha' => $fechaFutura,
            'hora_inicio' => '12:00',
            'hora_fin' => '12:30',
            'estado' => 'confirmada',
        ]);

        // Cita de Cliente B más temprana
        $this->crearCita([
            'user_id' => $clienteB->id,
            'trabajador_id' => $trabajador->id,
            'service_id' => $servicioB->id,
            'fecha' => $fechaFutura,
            'hora_inicio' => '09:00',
            'hora_fin' => '09:30',
            'estado' => 'confirmada',
        ]);

        // Al consultar como Cliente A, debe ver solo su servicio y no el de Cliente B
        $responseA = $this->actingAs($clienteA)->get('/cliente');
        $responseA->assertStatus(200);
        $responseA->assertSee('Servicio Exclusivo Alpha');
        $responseA->assertSee('12:00 hrs');
        $responseA->assertDontSee('Servicio Exclusivo Beta');
        $responseA->assertDontSee('09:00 hrs');

        // Al consultar como Cliente B, debe ver solo su servicio y no el de Cliente A
        $responseB = $this->actingAs($clienteB)->get('/cliente');
        $responseB->assertStatus(200);
        $responseB->assertSee('Servicio Exclusivo Beta');
        $responseB->assertSee('09:00 hrs');
        $responseB->assertDontSee('Servicio Exclusivo Alpha');
        $responseB->assertDontSee('12:00 hrs');
    }

    /**
     * Escenario 6: La cita carece de algún dato opcional o relación (servicio o trabajador nulo), sin provocar errores en la vista.
     */
    public function test_cita_con_datos_opcionales_faltantes_no_provoca_errores_en_la_vista(): void
    {
        $cliente = $this->crearCliente();
        $trabajador = $this->crearTrabajador('Felipe', '');
        $servicio = Service::create([
            'name' => 'Perfilado de Barba',
            'description' => null,
            'price' => 120.00,
            'duration_minutes' => 20,
            'category' => 'barberia',
            'is_active' => true,
        ]);

        $fechaFutura = Carbon::now()->addDays(2)->format('Y-m-d');

        $this->crearCita([
            'user_id' => $cliente->id,
            'trabajador_id' => $trabajador->id,
            'service_id' => $servicio->id,
            'fecha' => $fechaFutura,
            'hora_inicio' => '14:00',
            'hora_fin' => '14:20',
            'duracion_minutos' => 20,
            'precio' => 120.00,
            'estado' => 'confirmada',
            'notas' => null,
        ]);

        $response = $this->actingAs($cliente)->get('/cliente');

        $response->assertStatus(200);
        $response->assertSee('Resumen de Cita Programada');
        $response->assertSee('Perfilado de Barba');
        $response->assertSee('Felipe');
        $response->assertDontSee('Felipe null');
        $response->assertSee('14:00 hrs');
        $response->assertSee('Duración: 20 min');

        // Comprobamos además la robustez ante relaciones completamente nulas
        $citaHuerfana = new Cita([
            'fecha' => $fechaFutura,
            'hora_inicio' => '16:00',
            'hora_fin' => '16:30',
            'duracion_minutos' => 30,
            'estado' => 'confirmada',
        ]);
        $citaHuerfana->setRelation('service', null);
        $citaHuerfana->setRelation('trabajador', null);

        $vista = $this->actingAs($cliente)->view('cliente.dashboard', [
            'user' => $cliente,
            'proximaCita' => $citaHuerfana,
        ]);

        $vista->assertSee('Servicio programado');
        $vista->assertSee('Especialista asignado');
        $vista->assertSee('16:00 hrs');
    }

    /**
     * Escenario 7: No existen citas futuras válidas y la vista puede manejar esa condición sin fallar.
     */
    public function test_sin_citas_futuras_validas_la_vista_no_falla_ni_muestra_tarjeta_resumen(): void
    {
        $cliente = $this->crearCliente(['name' => 'Patricio']);

        $response = $this->actingAs($cliente)->get('/cliente');

        $response->assertStatus(200);
        $response->assertSee('¡Hola, Patricio!');
        $response->assertDontSee('Resumen de Cita Programada');
        $response->assertDontSee('Tu Próxima Cita');
        $response->assertSee('Aún no tienes citas programadas');
    }

    // =========================================================================
    // PRUEBAS DE LA SUBTAREA 3: ESTADO VACÍO CUANDO NO EXISTAN CITAS PROGRAMADAS
    // =========================================================================

    /**
     * Escenario 1 (Subtarea 3): Un cliente sin citas debe visualizar el estado vacío con título, mensaje descriptivo y acción.
     */
    public function test_subtarea3_cliente_sin_citas_visualiza_estado_vacio(): void
    {
        $cliente = $this->crearCliente(['name' => 'Andrés']);

        $response = $this->actingAs($cliente)->get('/cliente');

        $response->assertStatus(200);
        $response->assertSee('Aún no tienes citas programadas');
        $response->assertSee('Cuando agendes tu próxima cita, aquí podrás consultar los detalles de tu servicio, fecha, hora y especialista.');
        $response->assertSee('Agendar una cita');
        $response->assertSee(route('cliente.agendamiento'));
        $response->assertDontSee('Resumen de Cita Programada');
        $response->assertDontSee('Tu Próxima Cita');
    }

    /**
     * Escenario 2 (Subtarea 3): Un cliente con una próxima cita válida debe visualizar la tarjeta de la Subtarea 2 y no el estado vacío.
     */
    public function test_subtarea3_cliente_con_proxima_cita_valida_visualiza_tarjeta_y_no_estado_vacio(): void
    {
        $cliente = $this->crearCliente(['name' => 'Camila']);
        $trabajador = $this->crearTrabajador();
        $servicio = $this->crearServicio('Corte Estilizado Barber', 'barberia');
        $fechaFutura = Carbon::now()->addDays(2)->format('Y-m-d');

        $this->crearCita([
            'user_id' => $cliente->id,
            'trabajador_id' => $trabajador->id,
            'service_id' => $servicio->id,
            'fecha' => $fechaFutura,
            'hora_inicio' => '10:00',
            'hora_fin' => '10:30',
            'estado' => 'confirmada',
        ]);

        $response = $this->actingAs($cliente)->get('/cliente');

        $response->assertStatus(200);
        $response->assertSee('Resumen de Cita Programada');
        $response->assertSee('Tu Próxima Cita');
        $response->assertSee('Corte Estilizado Barber');
        $response->assertDontSee('Aún no tienes citas programadas');
        $response->assertDontSee('id="btnAgendarCitaVacio"', false);
    }

    /**
     * Escenario 3 (Subtarea 3): Un cliente cuyas citas estén canceladas debe visualizar el estado vacío si no tiene otra cita válida.
     */
    public function test_subtarea3_cliente_con_citas_canceladas_visualiza_estado_vacio(): void
    {
        $cliente = $this->crearCliente(['name' => 'Gabriel']);
        $trabajador = $this->crearTrabajador();
        $servicio = $this->crearServicio('Barba Express', 'barberia');

        $this->crearCita([
            'user_id' => $cliente->id,
            'trabajador_id' => $trabajador->id,
            'service_id' => $servicio->id,
            'fecha' => Carbon::now()->addDays(2)->format('Y-m-d'),
            'hora_inicio' => '11:00',
            'hora_fin' => '11:30',
            'estado' => 'cancelada',
        ]);

        $response = $this->actingAs($cliente)->get('/cliente');

        $response->assertStatus(200);
        $response->assertSee('Aún no tienes citas programadas');
        $response->assertSee('Agendar una cita');
        $response->assertDontSee('Resumen de Cita Programada');
    }

    /**
     * Escenario 4 (Subtarea 3): Un cliente cuyas citas estén completadas debe visualizar el estado vacío si no tiene otra cita válida.
     */
    public function test_subtarea3_cliente_con_citas_completadas_visualiza_estado_vacio(): void
    {
        $cliente = $this->crearCliente(['name' => 'Daniela']);
        $trabajador = $this->crearTrabajador();
        $servicio = $this->crearServicio('Tinte Completo', 'estetica');

        $this->crearCita([
            'user_id' => $cliente->id,
            'trabajador_id' => $trabajador->id,
            'service_id' => $servicio->id,
            'fecha' => Carbon::now()->subDays(1)->format('Y-m-d'),
            'hora_inicio' => '09:00',
            'hora_fin' => '10:00',
            'estado' => 'completada',
        ]);

        $response = $this->actingAs($cliente)->get('/cliente');

        $response->assertStatus(200);
        $response->assertSee('Aún no tienes citas programadas');
        $response->assertSee('Agendar una cita');
        $response->assertDontSee('Resumen de Cita Programada');
    }

    /**
     * Escenario 5 (Subtarea 3): Un cliente cuyas citas sean pasadas en fecha/hora debe visualizar el estado vacío si no tiene otra cita válida.
     */
    public function test_subtarea3_cliente_con_citas_pasadas_visualiza_estado_vacio(): void
    {
        $cliente = $this->crearCliente(['name' => 'Fernando']);
        $trabajador = $this->crearTrabajador();
        $servicio = $this->crearServicio('Corte Clásico', 'barberia');

        // Cita con estado confirmada pero fecha pasada
        $this->crearCita([
            'user_id' => $cliente->id,
            'trabajador_id' => $trabajador->id,
            'service_id' => $servicio->id,
            'fecha' => Carbon::now()->subDays(3)->format('Y-m-d'),
            'hora_inicio' => '15:00',
            'hora_fin' => '15:30',
            'estado' => 'confirmada',
        ]);

        $response = $this->actingAs($cliente)->get('/cliente');

        $response->assertStatus(200);
        $response->assertSee('Aún no tienes citas programadas');
        $response->assertSee('Agendar una cita');
        $response->assertDontSee('Resumen de Cita Programada');
    }

    /**
     * Escenario 6 (Subtarea 3): Si hay varias citas, debe mostrarse la tarjeta cuando al menos una sea válida y no debe aparecer el estado vacío.
     */
    public function test_subtarea3_multiples_citas_con_al_menos_una_valida_muestra_tarjeta_y_no_estado_vacio(): void
    {
        $cliente = $this->crearCliente(['name' => 'Joaquín']);
        $trabajador = $this->crearTrabajador();
        $servicioCancelado = $this->crearServicio('Cita Cancelada', 'barberia');
        $servicioPasado = $this->crearServicio('Cita Pasada', 'barberia');
        $servicioValido = $this->crearServicio('Cita Futura Vigente', 'barberia');

        // Cita cancelada
        $this->crearCita([
            'user_id' => $cliente->id,
            'trabajador_id' => $trabajador->id,
            'service_id' => $servicioCancelado->id,
            'fecha' => Carbon::now()->addDay()->format('Y-m-d'),
            'hora_inicio' => '10:00',
            'hora_fin' => '10:30',
            'estado' => 'cancelada',
        ]);

        // Cita pasada
        $this->crearCita([
            'user_id' => $cliente->id,
            'trabajador_id' => $trabajador->id,
            'service_id' => $servicioPasado->id,
            'fecha' => Carbon::now()->subDays(2)->format('Y-m-d'),
            'hora_inicio' => '11:00',
            'hora_fin' => '11:30',
            'estado' => 'confirmada',
        ]);

        // Cita válida en 4 días
        $this->crearCita([
            'user_id' => $cliente->id,
            'trabajador_id' => $trabajador->id,
            'service_id' => $servicioValido->id,
            'fecha' => Carbon::now()->addDays(4)->format('Y-m-d'),
            'hora_inicio' => '16:00',
            'hora_fin' => '16:30',
            'estado' => 'confirmada',
        ]);

        $response = $this->actingAs($cliente)->get('/cliente');

        $response->assertStatus(200);
        $response->assertSee('Resumen de Cita Programada');
        $response->assertSee('Cita Futura Vigente');
        $response->assertDontSee('Aún no tienes citas programadas');
    }

    /**
     * Escenario 7 (Subtarea 3): El botón "Agendar una cita" debe utilizar la ruta real de agendamiento para clientes autenticados.
     */
    public function test_subtarea3_boton_agendar_cita_utiliza_ruta_real_de_agendamiento(): void
    {
        $cliente = $this->crearCliente(['name' => 'Lucía']);

        $response = $this->actingAs($cliente)->get('/cliente');

        $response->assertStatus(200);
        $response->assertSee(route('cliente.agendamiento'));
        $response->assertSee('id="btnAgendarCitaVacio"', false);

        // Verificamos que al seguir la ruta de agendamiento, el cliente accede correctamente
        $responseAgendamiento = $this->actingAs($cliente)->get(route('cliente.agendamiento'));
        $responseAgendamiento->assertStatus(200);
        $responseAgendamiento->assertSee('Agendamiento');
    }

    /**
     * Escenario 8 (Subtarea 3): Los datos de otros clientes no deben afectar la visualización del dashboard del usuario actual.
     */
    public function test_subtarea3_citas_de_otros_clientes_no_afectan_visualizacion_del_usuario_actual(): void
    {
        $clienteA = $this->crearCliente(['name' => 'Cliente A Sin Citas']);
        $clienteB = $this->crearCliente(['name' => 'Cliente B Con Citas']);
        $trabajador = $this->crearTrabajador();
        $servicioB = $this->crearServicio('Servicio Exclusivo de B', 'barberia');

        // Solo Cliente B tiene cita válida
        $this->crearCita([
            'user_id' => $clienteB->id,
            'trabajador_id' => $trabajador->id,
            'service_id' => $servicioB->id,
            'fecha' => Carbon::now()->addDays(2)->format('Y-m-d'),
            'hora_inicio' => '10:00',
            'hora_fin' => '10:30',
            'estado' => 'confirmada',
        ]);

        // Cliente A no tiene citas -> debe ver estado vacío y NUNCA los datos de Cliente B
        $responseA = $this->actingAs($clienteA)->get('/cliente');
        $responseA->assertStatus(200);
        $responseA->assertSee('Aún no tienes citas programadas');
        $responseA->assertDontSee('Resumen de Cita Programada');
        $responseA->assertDontSee('Servicio Exclusivo de B');

        // Cliente B tiene cita -> debe ver la tarjeta y NUNCA el estado vacío
        $responseB = $this->actingAs($clienteB)->get('/cliente');
        $responseB->assertStatus(200);
        $responseB->assertSee('Resumen de Cita Programada');
        $responseB->assertSee('Servicio Exclusivo de B');
        $responseB->assertDontSee('Aún no tienes citas programadas');
    }

    // =========================================================================
    // PRUEBAS DE LA SUBTAREA 4: ACCESOS RÁPIDOS A SECCIONES PRINCIPALES
    // =========================================================================

    /**
     * Subtarea 4 - Criterios 1, 2, 3, 4 y 5:
     * El dashboard muestra ambos accesos rápidos con sus textos principales, descripciones y rutas correspondientes.
     */
    public function test_subtarea4_dashboard_muestra_accesos_rapidos_a_agendamiento_e_historial(): void
    {
        $cliente = $this->crearCliente(['name' => 'Marcos']);

        $response = $this->actingAs($cliente)->get('/cliente');

        $response->assertStatus(200);

        // Acceso 1: Agendamiento
        $response->assertSee('Agendar una cita');
        $response->assertSee('Explora nuestros servicios y reserva tu próxima cita');
        $response->assertSee(route('cliente.agendamiento'));
        $response->assertSee('id="quickAccessAgendamiento"', false);

        // Acceso 2: Historial
        $response->assertSee('Mi historial de citas');
        $response->assertSee('Consulta tus citas anteriores y programadas');
        $response->assertSee(route('cliente.historial'));
        $response->assertSee('id="quickAccessHistorial"', false);
    }

    /**
     * Subtarea 4 - Criterio 6:
     * Los accesos rápidos aparecen cuando el cliente tiene una próxima cita válida.
     */
    public function test_subtarea4_accesos_rapidos_aparecen_cuando_el_cliente_tiene_proxima_cita_valida(): void
    {
        $cliente = $this->crearCliente(['name' => 'Valeria']);
        $trabajador = $this->crearTrabajador();
        $servicio = $this->crearServicio('Corte Estilizado');

        $this->crearCita([
            'user_id' => $cliente->id,
            'trabajador_id' => $trabajador->id,
            'service_id' => $servicio->id,
            'fecha' => Carbon::now()->addDays(2)->format('Y-m-d'),
            'hora_inicio' => '11:00',
            'hora_fin' => '11:45',
            'estado' => 'confirmada',
        ]);

        $response = $this->actingAs($cliente)->get('/cliente');

        $response->assertStatus(200);
        // Debe mostrar la próxima cita
        $response->assertSee('Resumen de Cita Programada');
        $response->assertSee('Corte Estilizado');
        // Y TAMBIÉN los accesos rápidos
        $response->assertSee('Agendar una cita');
        $response->assertSee('Mi historial de citas');
        $response->assertSee(route('cliente.agendamiento'));
        $response->assertSee(route('cliente.historial'));
    }

    /**
     * Subtarea 4 - Criterio 7:
     * Los accesos rápidos aparecen cuando se muestra el estado vacío (sin citas).
     */
    public function test_subtarea4_accesos_rapidos_aparecen_cuando_se_muestra_el_estado_vacio(): void
    {
        $cliente = $this->crearCliente(['name' => 'Diego']);

        $response = $this->actingAs($cliente)->get('/cliente');

        $response->assertStatus(200);
        // Debe mostrar el estado vacío
        $response->assertSee('Aún no tienes citas programadas');
        // Y TAMBIÉN los accesos rápidos
        $response->assertSee('Agendar una cita');
        $response->assertSee('Mi historial de citas');
        $response->assertSee(route('cliente.agendamiento'));
        $response->assertSee(route('cliente.historial'));
    }

    /**
     * Subtarea 4 - Criterio 8:
     * No se incorporan formularios de reserva ni tablas completas del historial al dashboard.
     */
    public function test_subtarea4_no_incorpora_formularios_de_reserva_ni_tablas_de_historial_en_dashboard(): void
    {
        $cliente = $this->crearCliente(['name' => 'Sandra']);

        $response = $this->actingAs($cliente)->get('/cliente');

        $response->assertStatus(200);
        // No debe haber formularios de agendamiento dentro del dashboard
        $response->assertDontSee('action="' . route('cliente.agendamiento.confirmar') . '"', false);
        $response->assertDontSee('name="service_id"', false);
        $response->assertDontSee('name="worker_id"', false);
        $response->assertDontSee('name="fecha_cita"', false);
        // No debe haber tablas de historial dentro del dashboard
        $response->assertDontSee('class="table-historial"', false);
        $response->assertDontSee('class="kpi-summary-grid"', false);
        $response->assertDontSee('class="filter-wrapper"', false);
    }

    /**
     * Subtarea 4 - Criterio 9 y 10:
     * Se conserva el comportamiento existente de la bienvenida y el aislamiento de datos.
     */
    public function test_subtarea4_conserva_bienvenida_y_acceso_a_rutas_reales(): void
    {
        $cliente = $this->crearCliente(['name' => 'Fernando']);

        $response = $this->actingAs($cliente)->get('/cliente');
        $response->assertStatus(200);
        $response->assertSee('¡Hola, Fernando!');

        // Comprobamos que al seguir el enlace de agendamiento se carga la vista real de agendamiento
        $responseAgendamiento = $this->actingAs($cliente)->get(route('cliente.agendamiento'));
        $responseAgendamiento->assertStatus(200);
        $responseAgendamiento->assertSee('Agendamiento');

        // Comprobamos que al seguir el enlace de historial se carga la vista real de historial
        $responseHistorial = $this->actingAs($cliente)->get(route('cliente.historial'));
        $responseHistorial->assertStatus(200);
        $responseHistorial->assertSee('Historial de citas');
    }
}

