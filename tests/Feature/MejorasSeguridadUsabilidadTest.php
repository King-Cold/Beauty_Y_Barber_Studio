<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\Role;
use App\Models\Service;
use App\Models\Trabajador;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MejorasSeguridadUsabilidadTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $recepcionista;
    protected User $trabajadorUser;
    protected User $cliente1;
    protected User $cliente2;
    protected Trabajador $trabajador;
    protected Service $servicio;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear roles base si no existen
        $roles = [
            1 => 'Administrador',
            2 => 'Recepcionista',
            3 => 'Trabajador',
            4 => 'Cliente',
        ];

        foreach ($roles as $id => $nombre) {
            Role::firstOrCreate(['id' => $id], ['nombre' => $nombre]);
        }

        // Crear usuarios de prueba
        $this->admin = User::factory()->create([
            'role_id' => 1,
            'name' => 'Super',
            'apellidos' => 'Administrador',
            'email' => 'admin.test@bbs.com',
            'telefono' => '9991000001',
        ]);

        $this->recepcionista = User::factory()->create([
            'role_id' => 2,
            'name' => 'Lucia',
            'apellidos' => 'Mendez',
            'email' => 'recepcion.test@bbs.com',
            'telefono' => '9991000002',
        ]);

        $this->trabajadorUser = User::factory()->create([
            'role_id' => 3,
            'name' => 'Carlos',
            'apellidos' => 'Barbero',
            'email' => 'carlos.barbero@bbs.com',
            'telefono' => '9991000003',
        ]);

        $this->cliente1 = User::factory()->create([
            'role_id' => 4,
            'name' => 'Roberto',
            'apellidos' => 'Gomez',
            'email' => 'roberto.cliente@bbs.com',
            'telefono' => '9991000004',
        ]);

        $this->cliente2 = User::factory()->create([
            'role_id' => 4,
            'name' => 'Mariana',
            'apellidos' => 'Torres',
            'email' => 'mariana.cliente@bbs.com',
            'telefono' => '9991000005',
        ]);

        $this->trabajador = Trabajador::create([
            'user_id' => $this->trabajadorUser->id,
            'direccion' => 'Calle 50 #123, Centro',
            'experiencia' => 5,
            'especialidad' => 'Barbero Senior',
            'activo' => true,
            'horario' => [
                'lunes' => [
                    'entrada' => '09:00',
                    'salida' => '18:00',
                ],
            ],
        ]);

        $this->servicio = Service::create([
            'name' => 'Corte Clásico Ejecutivo',
            'description' => 'Corte de cabello masculino con navaja.',
            'price' => 180.00,
            'duration_minutes' => 30,
            'category' => 'barberia',
            'is_active' => true,
        ]);

        $this->trabajador->servicios()->sync([$this->servicio->id]);
        Cita::ensureTableExists();
    }

    /**
     * 1. SEGURIDAD EN URLS: Usuarios sin permiso no pueden acceder a rutas restringidas
     */
    public function test_usuario_sin_permisos_no_puede_acceder_a_rutas_restringidas_escribiendo_la_url(): void
    {
        // Cliente (rol 4) intentando entrar al módulo de administración
        $respClienteAdmin = $this->actingAs($this->cliente1)->get('/admin/usuarios');
        $respClienteAdmin->assertStatus(403);

        // Cliente intentando entrar al módulo de recepcionista
        $respClienteRecepcion = $this->actingAs($this->cliente1)->get('/recepcion/agendamiento');
        $respClienteRecepcion->assertStatus(403);

        // Cliente intentando entrar vía AJAX a endpoint administrativo
        $respClienteAjax = $this->actingAs($this->cliente1)->json('GET', '/admin/usuarios');
        $respClienteAjax->assertStatus(403);
        $respClienteAjax->assertJsonFragment(['success' => false]);

        // Recepcionista intentando entrar al panel administrativo
        $respRecepcionAdmin = $this->actingAs($this->recepcionista)->get('/admin/usuarios');
        $respRecepcionAdmin->assertStatus(403);
    }

    /**
     * 1. SEGURIDAD EN REGISTROS: Un usuario no puede consultar ni cancelar citas ajenas modificando el ID
     */
    public function test_usuario_no_puede_consultar_o_cancelar_citas_ajenas_modificando_id(): void
    {
        $citaCliente2 = Cita::create([
            'user_id' => $this->cliente2->id,
            'trabajador_id' => $this->trabajador->id,
            'service_id' => $this->servicio->id,
            'fecha' => Carbon::now()->addDays(2)->format('Y-m-d'),
            'hora_inicio' => '10:00',
            'hora_fin' => '10:30',
            'duracion_minutos' => 30,
            'precio' => 180.00,
            'estado' => 'confirmada',
        ]);

        // Cliente 1 intenta consultar la cita de Cliente 2 modificando la URL
        $respShow = $this->actingAs($this->cliente1)->get("/cliente/citas/{$citaCliente2->id}");
        $respShow->assertStatus(403);

        // Cliente 1 intenta cancelar la cita de Cliente 2 vía JSON
        $respCancel = $this->actingAs($this->cliente1)->json('POST', "/cliente/citas/{$citaCliente2->id}/cancelar");
        $respCancel->assertStatus(403);
        $respCancel->assertJsonFragment(['success' => false]);

        // El estado de la cita se mantiene sin alteración en la BD
        $this->assertDatabaseHas('citas', [
            'id' => $citaCliente2->id,
            'estado' => 'confirmada',
        ]);
    }

    /**
     * 2. SWEETALERT Y EDICIÓN DE USUARIOS: Actualización exitosa guarda en BD y retorna respuesta correcta
     */
    public function test_al_editar_usuario_correctamente_se_guarda_en_bd_y_retorna_mensaje_exitoso(): void
    {
        $payload = [
            '_method' => 'PUT',
            'name' => 'Roberto Carlos',
            'apellidos' => 'Gomez Bolaños',
            'telefono' => '9998887766',
            'email' => 'roberto.modificado@bbs.com',
            'role_id' => 4,
        ];

        $response = $this->actingAs($this->admin)->json('POST', "/admin/usuarios/{$this->cliente1->id}", $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Usuario actualizado exitosamente.',
        ]);

        // Verificar que realmente se guardó en la base de datos
        $this->assertDatabaseHas('users', [
            'id' => $this->cliente1->id,
            'name' => 'Roberto Carlos',
            'apellidos' => 'Gomez Bolaños',
            'email' => 'roberto.modificado@bbs.com',
            'telefono' => '9998887766',
        ]);
    }

    /**
     * 2. SWEETALERT Y EDICIÓN DE USUARIOS: Si la edición falla por validación, no retorna éxito y muestra errores
     */
    public function test_si_la_edicion_falla_por_validacion_no_muestra_exito_y_retorna_errores(): void
    {
        // Intentar actualizar con el teléfono de otro usuario existente (duplicado)
        $payloadInvalido = [
            '_method' => 'PUT',
            'name' => 'Roberto',
            'apellidos' => 'Gomez',
            'telefono' => $this->cliente2->telefono, // Duplicado de cliente2
            'email' => 'nuevo.email@bbs.com',
            'role_id' => 4,
        ];

        $response = $this->actingAs($this->admin)->json('POST', "/admin/usuarios/{$this->cliente1->id}", $payloadInvalido);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['telefono']);
        $response->assertJsonMissing(['success' => true]);
    }

    /**
     * 3. BUSCADOR DINÁMICO DE CLIENTES: Permite filtrar clientes por nombre o apellidos
     */
    public function test_buscador_dinamico_encuentra_clientes_por_nombre_o_apellidos(): void
    {
        // Buscar "Mariana" desde recepción
        $respBusqueda = $this->actingAs($this->recepcionista)->json('GET', route('recepcion.agendamiento.buscar-clientes'), [
            'q' => 'Mariana',
        ]);

        $respBusqueda->assertStatus(200);
        $respBusqueda->assertJson([
            'success' => true,
        ]);
        $respBusqueda->assertJsonFragment([
            'id' => $this->cliente2->id,
            'name' => 'Mariana',
        ]);
        $respBusqueda->assertJsonMissing([
            'name' => 'Roberto',
        ]);

        // Buscar por apellido "Gomez"
        $respApellido = $this->actingAs($this->recepcionista)->json('GET', route('recepcion.agendamiento.buscar-clientes'), [
            'q' => 'Gomez',
        ]);

        $respApellido->assertStatus(200);
        $respApellido->assertJsonFragment([
            'id' => $this->cliente1->id,
            'name' => 'Roberto',
        ]);
    }

    /**
     * 3. BUSCADOR DINÁMICO DE CLIENTES: No incluye usuarios con roles que no sean clientes
     */
    public function test_buscador_dinamico_solo_retorna_clientes_rol_4(): void
    {
        $respAdmin = $this->actingAs($this->recepcionista)->json('GET', route('recepcion.agendamiento.buscar-clientes'), [
            'q' => 'Carlos', // Es un trabajador
        ]);

        $respAdmin->assertStatus(200);
        $respAdmin->assertJsonMissing([
            'id' => $this->trabajadorUser->id,
        ]);
    }

    /**
     * 3 y 4. AGENDAMIENTO RECEPCIÓN: Valida en backend que el cliente seleccionado exista y sea válido
     */
    public function test_agendamiento_recepcion_valida_existencia_de_cliente_y_disponibilidad(): void
    {
        $fechaLunes = Carbon::now()->next(Carbon::MONDAY)->format('Y-m-d');

        // Intento con ID de cliente inexistente
        $payloadInexistente = [
            'client_id' => 999999,
            'service_id' => $this->servicio->id,
            'worker_id' => $this->trabajador->id,
            'date' => $fechaLunes,
            'time' => '11:00',
        ];

        $respInexistente = $this->actingAs($this->recepcionista)->json('POST', route('recepcion.agendamiento.confirmar'), $payloadInexistente);
        $respInexistente->assertStatus(422);

        // Agendamiento con cliente real y válido
        $payloadValido = [
            'client_id' => $this->cliente1->id,
            'service_id' => $this->servicio->id,
            'worker_id' => $this->trabajador->id,
            'date' => $fechaLunes,
            'time' => '11:00',
        ];

        $respValido = $this->actingAs($this->recepcionista)->json('POST', route('recepcion.agendamiento.confirmar'), $payloadValido);
        $respValido->assertStatus(200);
        $respValido->assertJson([
            'success' => true,
            'valido' => true,
        ]);

        $this->assertDatabaseHas('citas', [
            'user_id' => $this->cliente1->id,
            'trabajador_id' => $this->trabajador->id,
            'service_id' => $this->servicio->id,
            'fecha' => $fechaLunes,
            'hora_inicio' => '11:00',
            'estado' => 'confirmada',
        ]);
    }
}
