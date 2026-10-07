<?php

namespace Tests\Feature;

use App\Models\Trabajador;
use Database\Seeders\TestUsersSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TrabajadoresStructureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Ejecutar seeder básico para roles y usuarios de prueba
        $this->seed(TestUsersSeeder::class);
    }

    /**
     * Comprueba que la tabla trabajadores existe y que horarios_trabajadores no está presente (Subtarea 6 descartada).
     */
    public function test_tabla_trabajadores_existe_y_horarios_no_existe(): void
    {
        $this->assertTrue(Schema::hasTable('trabajadores'), 'La tabla trabajadores debe existir');
        $this->assertFalse(Schema::hasTable('horarios_trabajadores'), 'La tabla horarios_trabajadores no debe existir (Subtarea 6 descartada)');
    }

    /**
     * Comprueba las columnas de la tabla trabajadores:
     * - Incluye activo (Subtarea 8 se mantiene)
     * - No incluye user_id (Subtarea 9 descartada)
     */
    public function test_tabla_trabajadores_tiene_columnas_correctas_sin_user_id_y_con_activo(): void
    {
        $columnasEsperadas = [
            'id',
            'nombre',
            'apellidos',
            'telefono',
            'email',
            'direccion',
            'fotografia',
            'experiencia',
            'activo',
            'created_at',
            'updated_at',
        ];

        foreach ($columnasEsperadas as $columna) {
            $this->assertTrue(
                Schema::hasColumn('trabajadores', $columna),
                "La columna {$columna} debe existir en trabajadores"
            );
        }

        // Subtarea 9 descartada: user_id no debe existir
        $this->assertFalse(
            Schema::hasColumn('trabajadores', 'user_id'),
            'La columna user_id no debe existir en trabajadores al descartarse la subtarea 9'
        );

        // Campo especialidad eliminado por solicitud del usuario
        $this->assertFalse(
            Schema::hasColumn('trabajadores', 'especialidad'),
            'La columna especialidad no debe existir en trabajadores'
        );
    }

    /**
     * Comprueba la persistencia en base de datos y métodos auxiliares del modelo Trabajador.
     */
    public function test_persistencia_del_modelo_trabajador_con_estado_activo(): void
    {
        // Crear trabajador independiente sin cuenta de usuario (Subtarea 9 descartada)
        $trabajador = Trabajador::create([
            'nombre' => 'Alejandro',
            'apellidos' => 'Torres',
            'telefono' => '5554567899',
            'email' => 'alejandro.test@bbs.com',
            'direccion' => 'Av. Insurgentes Sur #450, Col. Roma',
            'fotografia' => 'trabajadores/alejandro.jpg',
            'experiencia' => 6,
            'activo' => true,
        ]);

        $this->assertDatabaseHas('trabajadores', [
            'id' => $trabajador->id,
            'email' => 'alejandro.test@bbs.com',
            'nombre' => 'Alejandro',
            'telefono' => '5554567899',
            'activo' => true,
        ]);

        // Verificar atributos y scope de activos (Subtarea 8)
        $this->assertTrue($trabajador->activo);
        $this->assertEquals('Alejandro Torres', $trabajador->nombre_completo);
        $this->assertEquals(1, Trabajador::activos()->count());
    }
}
