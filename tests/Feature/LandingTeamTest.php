<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Trabajador;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingTeamTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (Role::count() === 0) {
            Role::create(['id' => 1, 'nombre' => 'Administrador']);
            Role::create(['id' => 2, 'nombre' => 'Recepcionista']);
            Role::create(['id' => 3, 'nombre' => 'Trabajador']);
            Role::create(['id' => 4, 'nombre' => 'Cliente']);
        }
    }

    /**
     * Criterio 1: Cada profesional debe mostrarse dentro de una tarjeta visual que presente:
     * - Fotografía de perfil o en el área de trabajo
     * - Nombre y apellido completos del trabajador
     * - Años de experiencia (ejemplo: "6 años de experiencia")
     */
    public function test_profesionales_activos_se_muestran_con_tarjeta_individual_foto_nombre_y_experiencia(): void
    {
        $user = User::create([
            'name' => 'Ricardo',
            'apellidos' => 'Morales Vega',
            'email' => 'ricardo.morales@test.com',
            'password' => bcrypt('password123'),
            'telefono' => '5511223344',
            'role_id' => 3, // Trabajador
        ]);

        $trabajador = Trabajador::create([
            'user_id' => $user->id,
            'experiencia' => 6,
            'activo' => true,
            'direccion' => 'Av. Oculta 456',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Ricardo Morales Vega');
        $response->assertSee('6 años de experiencia');
        $response->assertSee('teamCard' . $trabajador->id);
    }

    /**
     * Criterio 2: Comprobación en base de datos (Personal inactivo):
     * Si en la base de datos se modifica el registro de un trabajador a inactivo (activo = 0)
     * y se refresca la página, el trabajador debe desaparecer automáticamente de la vista pública.
     */
    public function test_trabajador_inactivo_desaparece_automaticamente_de_la_pantalla(): void
    {
        $userInactivo = User::create([
            'name' => 'Empleado',
            'apellidos' => 'Inactivo Retirado',
            'email' => 'inactivo.retirado@test.com',
            'password' => bcrypt('password123'),
            'telefono' => '5599887766',
            'role_id' => 3,
        ]);

        $trabajadorInactivo = Trabajador::create([
            'user_id' => $userInactivo->id,
            'experiencia' => 3,
            'activo' => false,
            'direccion' => 'Calle Inactiva 123',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('Empleado Inactivo Retirado');
        $response->assertDontSee('teamCard' . $trabajadorInactivo->id);
    }

    /**
     * Criterio 3: Comprobación de foto faltante:
     * Si un profesional activo no cuenta con fotografía en el sistema, debe mostrarse
     * una silueta o avatar genérico de alta calidad, evitando íconos de error de imagen.
     */
    public function test_trabajador_sin_foto_muestra_avatar_o_silueta_generica_sin_romperse(): void
    {
        $userSinFoto = User::create([
            'name' => 'Estilista',
            'apellidos' => 'Sin Fotografia',
            'email' => 'sinfoto@test.com',
            'password' => bcrypt('password123'),
            'telefono' => '5544332211',
            'role_id' => 3,
        ]);

        $trabajador = Trabajador::create([
            'user_id' => $userSinFoto->id,
            'experiencia' => 5,
            'fotografia' => null,
            'activo' => true,
            'direccion' => 'Calle Sin Foto 456',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('teamCard' . $trabajador->id);
        $response->assertSee(asset('images/avatar-default.svg'));
        $response->assertSee('onerror="this.onerror=null;', false);
    }

    /**
     * Criterio 4: En ningún lugar de la tarjeta deben mostrarse datos confidenciales o personales
     * del trabajador, como su dirección particular, su número telefónico privado o su contraseña.
     */
    public function test_tarjeta_no_muestra_datos_confidenciales_del_trabajador(): void
    {
        $direccionPrivada = 'Calle Secreta #777 Col. Exclusiva';
        $telefonoPrivado = '5559876543';
        $correoPrivado = 'personal.confidencial@estudio.com';

        $user = User::create([
            'name' => 'Barbero',
            'apellidos' => 'Seguro Protegido',
            'email' => $correoPrivado,
            'password' => bcrypt('claveSuperSecreta123'),
            'telefono' => $telefonoPrivado,
            'role_id' => 3,
        ]);

        Trabajador::create([
            'user_id' => $user->id,
            'experiencia' => 8,
            'activo' => true,
            'direccion' => $direccionPrivada,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Barbero Seguro Protegido');
        // Aserciones de confidencialidad estricta
        $response->assertDontSee($direccionPrivada);
        $response->assertDontSee($telefonoPrivado);
        $response->assertDontSee($correoPrivado);
        $response->assertDontSee('claveSuperSecreta123');
    }

    /**
     * Criterio 5: Si no hay ningún trabajador activo configurado en el sistema,
     * la sección debe mostrar un mensaje informativo que indique que el equipo se encuentra en proceso de actualización.
     */
    public function test_mensaje_informativo_de_actualizacion_si_no_hay_trabajadores_activos(): void
    {
        // En base de datos limpia sin trabajadores activos
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('id="teamEmptyState"', false);
        $response->assertSee('Equipo en Actualización');
        $response->assertSee('en proceso de actualización');
    }
}
