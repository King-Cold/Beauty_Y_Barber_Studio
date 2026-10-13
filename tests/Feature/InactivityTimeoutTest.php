<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InactivityTimeoutTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $cliente;
    protected User $recepcionista;
    protected User $trabajador;

    protected function setUp(): void
    {
        parent::setUp();

        // Asegurar que existan los 4 roles
        $roles = [
            1 => 'Administrador',
            2 => 'Recepcionista',
            3 => 'Trabajador',
            4 => 'Cliente',
        ];

        foreach ($roles as $id => $nombre) {
            Role::firstOrCreate(['id' => $id], ['nombre' => $nombre]);
        }

        $this->admin = User::factory()->create(['role_id' => 1, 'name' => 'Admin', 'apellidos' => 'Sistema', 'telefono' => '9991111111', 'email' => 'admin@test.com']);
        $this->recepcionista = User::factory()->create(['role_id' => 2, 'name' => 'Recep', 'apellidos' => 'Sistema', 'telefono' => '9992222222', 'email' => 'recepcion@test.com']);
        $this->trabajador = User::factory()->create(['role_id' => 3, 'name' => 'Barbero', 'apellidos' => 'Sistema', 'telefono' => '9993333333', 'email' => 'trabajador@test.com']);
        $this->cliente = User::factory()->create(['role_id' => 4, 'name' => 'Cliente', 'apellidos' => 'Sistema', 'telefono' => '9994444444', 'email' => 'cliente@test.com']);
    }

    /**
     * Prueba 1: Un usuario con actividad reciente (< 5 minutos) mantiene su sesión activa
     */
    public function test_usuario_activo_dentro_del_limite_de_5_minutos_mantiene_su_sesion(): void
    {
        // Simulamos actividad hace 2 minutos (120 segundos)
        $response = $this->actingAs($this->cliente)
            ->withSession(['last_activity_time' => time() - 120])
            ->get('/cliente');

        $response->assertStatus(200);
        $this->assertAuthenticatedAs($this->cliente);
    }

    /**
     * Prueba 2: Un usuario con inactividad superior a 5 minutos (301s) es desconectado automáticamente
     */
    public function test_usuario_inactivo_mas_de_5_minutos_es_desconectado_automaticamente(): void
    {
        // Simulamos inactividad de 5 minutos y 1 segundo (301 segundos)
        $response = $this->actingAs($this->cliente)
            ->withSession(['last_activity_time' => time() - 301])
            ->get('/cliente');

        // Debe redirigir al login
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('warning', 'Tu sesión se ha cerrado automáticamente por 5 minutos de inactividad.');

        // Debe quedar desconectado (Guest)
        $this->assertGuest();
    }

    /**
     * Prueba 3: Peticiones AJAX tras 5 minutos de inactividad responden con 401 JSON
     */
    public function test_peticion_ajax_tras_5_minutos_de_inactividad_retorna_401_json(): void
    {
        $response = $this->actingAs($this->admin)
            ->withSession(['last_activity_time' => time() - 350])
            ->json('GET', '/admin/usuarios');

        $response->assertStatus(401);
        $response->assertJson([
            'success' => false,
            'message' => 'Tu sesión se ha cerrado por 5 minutos de inactividad.',
            'redirect' => route('login'),
        ]);

        $this->assertGuest();
    }

    /**
     * Prueba 4: La ruta logout con parámetro reason=inactivity redirecciona con advertencia
     */
    public function test_ruta_logout_con_parametro_inactivity_cierra_sesion_con_alerta_warning(): void
    {
        $response = $this->actingAs($this->recepcionista)
            ->get('/logout?reason=inactivity');

        $response->assertRedirect('/');
        $response->assertSessionHas('warning', 'Tu sesión se ha cerrado automáticamente por 5 minutos de inactividad.');
        $this->assertGuest();
    }

    /**
     * Prueba 5: El timeout aplica a cualquiera de los roles del sistema (Admin, Trabajador, Recepcionista, Cliente)
     */
    public function test_todos_los_roles_son_desconectados_por_inactividad(): void
    {
        $usuarios = [$this->admin, $this->recepcionista, $this->trabajador, $this->cliente];

        foreach ($usuarios as $usuario) {
            $resp = $this->actingAs($usuario)
                ->withSession(['last_activity_time' => time() - 305])
                ->get('/');

            $this->assertGuest();
        }
    }
}
