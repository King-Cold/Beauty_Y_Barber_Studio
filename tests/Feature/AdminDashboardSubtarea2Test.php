<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\Role;
use App\Models\Service;
use App\Models\Trabajador;
use App\Models\User;
use Database\Seeders\TestUsersSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminDashboardSubtarea2Test extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $recepcionUser;
    protected User $workerUser;
    protected User $clienteUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(TestUsersSeeder::class);

        $this->adminUser = User::where('role_id', 1)->first();
        $this->recepcionUser = User::where('role_id', 2)->first();
        $this->workerUser = User::where('role_id', 3)->first();
        $this->clienteUser = User::where('role_id', 4)->first();
    }

    /**
     * Criterio 2 & 5: Un administrador autenticado puede acceder al dashboard (código 200).
     */
    public function test_administrador_autenticado_puede_acceder_al_dashboard(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
        $response->assertViewHasAll([
            'totalClientes',
            'totalTrabajadores',
            'totalServicios',
            'totalCitas'
        ]);
    }

    /**
     * Criterio 2 & 5: Un usuario invitado (sin autenticación) es redirigido al login.
     */
    public function test_usuario_no_autenticado_es_redirigido_al_login(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));
    }

    /**
     * Criterio 2 & 5: Usuarios con otros roles (recepcionista, trabajador, cliente) no tienen acceso (403).
     */
    public function test_usuarios_sin_rol_administrador_no_pueden_acceder_al_dashboard(): void
    {
        // Recepcionista (Rol 2)
        $responseRecep = $this->actingAs($this->recepcionUser)->get(route('admin.dashboard'));
        $responseRecep->assertStatus(403);

        // Trabajador (Rol 3)
        $responseWorker = $this->actingAs($this->workerUser)->get(route('admin.dashboard'));
        $responseWorker->assertStatus(403);

        // Cliente (Rol 4)
        $responseCliente = $this->actingAs($this->clienteUser)->get(route('admin.dashboard'));
        $responseCliente->assertStatus(403);
    }

    /**
     * Criterio 3: Conteo real de clientes, contabilizando exclusivamente rol cliente (rol 4).
     */
    public function test_conteo_de_clientes_solo_incluye_rol_cliente(): void
    {
        // En TestUsersSeeder ya hay 1 cliente registrado (Roberto Sánchez, rol 4)
        // Agregamos 2 clientes adicionales
        User::create([
            'name' => 'Mariana',
            'apellidos' => 'Gómez',
            'telefono' => '5550001122',
            'email' => 'mariana.cliente@gmail.com',
            'password' => Hash::make('Secret123!'),
            'role_id' => 4,
        ]);

        User::create([
            'name' => 'Eduardo',
            'apellidos' => 'Peña',
            'telefono' => '5550003344',
            'email' => 'eduardo.cliente@gmail.com',
            'password' => Hash::make('Secret123!'),
            'role_id' => 4,
        ]);

        // Agregamos un usuario adicional con rol 2 (recepcionista) y rol 1 (admin) para comprobar exclusión
        User::create([
            'name' => 'Auxiliar',
            'apellidos' => 'Recepción',
            'telefono' => '5550005566',
            'email' => 'auxiliar.recep@bbs.com',
            'password' => Hash::make('Secret123!'),
            'role_id' => 2,
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        // Debe contabilizar exactamente 3 clientes (1 del seeder + 2 creados), ignorando el auxiliar de recepción
        $response->assertViewHas('totalClientes', 3);
        $response->assertSee('<span class="kpi-value" id="kpiValueClientes">3</span>', false);
    }

    /**
     * Criterio 4: Conteo real de trabajadores basado en la plantilla de 'trabajadores',
     * sin incluir cuentas que solo tengan rol trabajador sin ficha, excluyendo otros roles
     * y conservando fichas históricas sin user_id.
     */
    public function test_conteo_de_trabajadores_sin_duplicidades_y_excluyendo_otros_roles(): void
    {
        // 1. Alejandro Torres (TestUsersSeeder) tiene role_id = 3 en users.
        // Creamos su ficha oficial en 'trabajadores'.
        if (Schema::hasTable('trabajadores') && Schema::hasColumn('trabajadores', 'user_id')) {
            Trabajador::create([
                'user_id' => $this->workerUser->id,
                'direccion' => 'Calle Principal #100',
                'experiencia' => 5,
                'activo' => true,
            ]);
        }

        // 2. Daniel Navarro: segundo barbero con cuenta y ficha en trabajadores.
        $barbero2 = User::create([
            'name' => 'Daniel',
            'apellidos' => 'Navarro',
            'telefono' => '5559998877',
            'email' => 'daniel.barbero@bbs.com',
            'password' => Hash::make('Barbero123!'),
            'role_id' => 3,
        ]);

        if (Schema::hasTable('trabajadores') && Schema::hasColumn('trabajadores', 'user_id')) {
            Trabajador::create([
                'user_id' => $barbero2->id,
                'direccion' => 'Av. Central #200',
                'experiencia' => 3,
                'activo' => true,
            ]);
        }

        // 3. Ficha histórica en 'trabajadores' sin user_id (debe contabilizarse en la plantilla).
        if (Schema::hasTable('trabajadores')) {
            Trabajador::create([
                'user_id' => null,
                'direccion' => 'Sucursal Norte #50',
                'experiencia' => 8,
                'activo' => true,
            ]);
        }

        // 4. Cuenta de usuario huérfana con role_id = 3 pero SIN ficha en trabajadores (NO debe contabilizarse).
        User::create([
            'name' => 'Usuario',
            'apellidos' => 'Solo Rol Staff',
            'telefono' => '5557778899',
            'email' => 'huerfano.rol@bbs.com',
            'password' => Hash::make('Secret123!'),
            'role_id' => 3,
        ]);

        // 5. Ficha que por error estuviese vinculada a un Administrador (role_id = 1, NO debe contabilizarse).
        if (Schema::hasTable('trabajadores') && Schema::hasColumn('trabajadores', 'user_id')) {
            Trabajador::create([
                'user_id' => $this->adminUser->id,
                'direccion' => 'Oficina Administrativa',
                'experiencia' => 10,
                'activo' => true,
            ]);
        }

        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        // Total esperado: 3 (Alejandro + Daniel + Ficha histórica sin user_id).
        // La cuenta huérfana sin ficha y la ficha vinculada a admin NO se contabilizan.
        $response->assertViewHas('totalTrabajadores', 3);
        $response->assertSee('<span class="kpi-value" id="kpiValueTrabajadores">3</span>', false);
    }

    /**
     * Criterio 5: Conteo de servicios mostrando únicamente los disponibles y activos (is_active = true).
     */
    public function test_conteo_de_servicios_solo_incluye_servicios_activos(): void
    {
        // Limpiamos servicios para prueba controlada
        Service::query()->delete();

        // 3 Servicios activos
        Service::create([
            'name' => 'Corte Clásico Activo',
            'description' => 'Servicio activo',
            'price' => 250.00,
            'duration_minutes' => 45,
            'category' => 'barberia',
            'is_active' => true,
        ]);

        Service::create([
            'name' => 'Afeitado Toalla Caliente Activo',
            'description' => 'Servicio activo',
            'price' => 220.00,
            'duration_minutes' => 40,
            'category' => 'barberia',
            'is_active' => true,
        ]);

        Service::create([
            'name' => 'Perfilado de Barba Activo',
            'description' => 'Servicio activo',
            'price' => 180.00,
            'duration_minutes' => 30,
            'category' => 'barberia',
            'is_active' => true,
        ]);

        // 2 Servicios inactivos (dados de baja o pausados)
        Service::create([
            'name' => 'Servicio Temporal Inactivo',
            'description' => 'Servicio no disponible',
            'price' => 300.00,
            'duration_minutes' => 60,
            'category' => 'estetica',
            'is_active' => false,
        ]);

        Service::create([
            'name' => 'Tratamiento Descontinuado Inactivo',
            'description' => 'Servicio no disponible',
            'price' => 500.00,
            'duration_minutes' => 90,
            'category' => 'estetica',
            'is_active' => false,
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        // Debe contabilizar únicamente los 3 activos
        $response->assertViewHas('totalServicios', 3);
        $response->assertSee('<span class="kpi-value" id="kpiValueServicios">3</span>', false);
    }

    /**
     * Criterio 7: Conteo total de citas registradas sin importar su estado.
     */
    public function test_conteo_de_citas_totaliza_todas_las_reservaciones(): void
    {
        Cita::ensureTableExists();
        Cita::query()->delete();

        // Creamos un servicio para asociar
        $servicio = Service::create([
            'name' => 'Corte Express',
            'description' => 'Corte rápido',
            'price' => 200.00,
            'duration_minutes' => 30,
            'category' => 'barberia',
            'is_active' => true,
        ]);

        // Creamos un trabajador en la tabla trabajadores si existe la tabla
        $trabajadorId = $this->workerUser->id;
        if (Schema::hasTable('trabajadores')) {
            $trabajadorModel = Trabajador::create([
                'user_id' => $this->workerUser->id,
                'direccion' => 'Calle Principal #100',
                'experiencia' => 5,
                'activo' => true,
            ]);
            $trabajadorId = $trabajadorModel->id;
        }

        // Creamos citas con diversos estados: confirmada, en_atencion, completada, cancelada
        $estados = ['confirmada', 'en_atencion', 'completada', 'cancelada'];
        foreach ($estados as $index => $estado) {
            Cita::create([
                'user_id' => $this->clienteUser->id,
                'trabajador_id' => $trabajadorId,
                'service_id' => $servicio->id,
                'fecha' => now()->addDays($index)->format('Y-m-d'),
                'hora_inicio' => '10:00',
                'hora_fin' => '10:30',
                'duracion_minutos' => 30,
                'precio' => 200.00,
                'estado' => $estado,
                'estado_pago' => 'pendiente',
            ]);
        }

        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        // Debe mostrar 4 citas en total
        $response->assertViewHas('totalCitas', 4);
        $response->assertSee('<span class="kpi-value" id="kpiValueCitas">4</span>', false);
    }

    /**
     * Criterio 8: Tablas vacías muestran 0 sin arrojar excepciones ni datos simulados.
     */
    public function test_tablas_vacias_muestran_cero_en_los_indicadores(): void
    {
        Cita::ensureTableExists();
        Cita::query()->delete();
        Service::query()->delete();

        if (Schema::hasTable('trabajadores')) {
            Trabajador::query()->delete();
        }

        // Dejamos solo al admin para poder autenticar la petición
        User::where('role_id', '!=', 1)->delete();

        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('totalClientes', 0);
        $response->assertViewHas('totalTrabajadores', 0);
        $response->assertViewHas('totalServicios', 0);
        $response->assertViewHas('totalCitas', 0);

        $response->assertSee('<span class="kpi-value" id="kpiValueClientes">0</span>', false);
        $response->assertSee('<span class="kpi-value" id="kpiValueTrabajadores">0</span>', false);
        $response->assertSee('<span class="kpi-value" id="kpiValueServicios">0</span>', false);
        $response->assertSee('<span class="kpi-value" id="kpiValueCitas">0</span>', false);
    }

    /**
     * Criterio 1: El dashboard incluye enlaces a los 5 módulos administrativos.
     */
    public function test_presencia_de_enlaces_a_los_cinco_modulos(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));

        $response->assertStatus(200);

        // 1. Clientes (con filtro role_id=4)
        $response->assertSee(route('admin.usuarios', ['role_id' => 4]), false);

        // 2. Trabajadores
        $response->assertSee(route('admin.trabajadores.index'), false);

        // 3. Servicios
        $response->assertSee(route('admin.services'), false);

        // 4. Horarios
        $response->assertSee(route('admin.configuracion'), false);

        // 5. Citas
        $response->assertSee(route('admin.citas'), false);
    }

    /**
     * Criterio 2 & Pregunta 2: El enlace de Clientes con role_id=4 filtra correctamente en el controlador.
     */
    public function test_enlace_clientes_aplica_filtro_role_id_4_en_usuarios(): void
    {
        // En TestUsersSeeder hay: 1 Admin, 1 Recepcionista, 1 Trabajador, 1 Cliente
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.usuarios', ['role_id' => 4]));

        $response->assertStatus(200);

        // Comprobar que en los usuarios pasados a la vista solo hay clientes
        $usersPassed = $response->viewData('users');
        $this->assertNotEmpty($usersPassed);

        foreach ($usersPassed as $user) {
            $this->assertEquals(4, $user->role_id, "El usuario {$user->name} no tiene el rol 4 (Cliente)");
        }
    }
}
