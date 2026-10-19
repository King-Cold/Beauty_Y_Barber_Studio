<?php

namespace Tests\Feature;

use App\Models\HorarioSucursal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class LandingScheduleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        HorarioSucursal::ensureTableExists();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow(); // Restablecer el reloj global de Carbon
        parent::tearDown();
    }

    /**
     * Criterio 1: Debe mostrarse una tabla o lista ordenada que abarque los 7 días de la semana (Lunes a Domingo).
     */
    public function test_tabla_muestra_los_7_dias_de_la_semana_lunes_a_domingo(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('id="scheduleWeekTable"', false);
        $response->assertSee('id="scheduleRow_lunes"', false);
        $response->assertSee('id="scheduleRow_martes"', false);
        $response->assertSee('id="scheduleRow_miercoles"', false);
        $response->assertSee('id="scheduleRow_jueves"', false);
        $response->assertSee('id="scheduleRow_viernes"', false);
        $response->assertSee('id="scheduleRow_sabado"', false);
        $response->assertSee('id="scheduleRow_domingo"', false);

        $response->assertSee('Lunes');
        $response->assertSee('Martes');
        $response->assertSee('Miércoles');
        $response->assertSee('Jueves');
        $response->assertSee('Viernes');
        $response->assertSee('Sábado');
        $response->assertSee('Domingo');
    }

    /**
     * Criterio 2: Para los días en los que el estudio tiene servicio,
     * debe mostrarse el horario en formato legible de 12 horas con indicador de mañana y tarde (ejemplo: 09:00 AM a 08:00 PM).
     */
    public function test_horario_en_formato_12_horas_con_indicador_am_pm_para_dias_abiertos(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        // Lunes a Viernes default: 09:00 AM a 08:00 PM
        $response->assertSee('09:00 AM a 08:00 PM');
        // Sábado default: 08:00 AM a 12:00 PM
        $response->assertSee('08:00 AM a 12:00 PM');
    }

    /**
     * Criterio 3: Para los días en los que el estudio no labora (marcado como no abierto en la base de datos),
     * debe exhibirse claramente el texto 'Cerrado' en un color distintivo.
     */
    public function test_dias_no_abiertos_muestran_claramente_texto_cerrado_en_color_distintivo(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('badge-closed');
        $response->assertSee('Cerrado todo el día');
        $response->assertSee('Cerrado');
    }

    /**
     * Criterio 4 (Parte A): Si la consulta se realiza en un horario y día en que el local está abierto:
     * debe mostrar una etiqueta verde con el texto 'Abierto ahora - Cierre a las [hora de cierre]'.
     */
    public function test_indicador_en_tiempo_real_muestra_abierto_ahora_cuando_el_local_esta_operando(): void
    {
        // Simulamos un Miércoles a las 11:30 AM (zona horaria del negocio)
        Carbon::setTestNow(Carbon::parse('2026-10-21 11:30:00', 'America/Mexico_City'));

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('id="scheduleLiveStatus"', false);
        $response->assertSee('status-open');
        $response->assertSee('dot-pulse-green');
        $response->assertSee('Abierto ahora - Cierre a las 08:00 PM');
    }

    /**
     * Criterio 4 (Parte B): Si la consulta se realiza fuera de horario o en día inhábil:
     * debe mostrar una etiqueta con el texto 'Cerrado en este momento - Abrimos el [próximo día hábil] a las [hora de apertura]'.
     */
    public function test_indicador_en_tiempo_real_muestra_cerrado_y_proxima_apertura_en_dia_inhabiles_o_noche(): void
    {
        // Caso 1: Domingo a las 10:00 AM (Día no laborable -> Próxima apertura el Lunes a las 09:00 AM)
        Carbon::setTestNow(Carbon::parse('2026-10-18 10:00:00', 'America/Mexico_City'));

        $responseDomingo = $this->get('/');

        $responseDomingo->assertStatus(200);
        $responseDomingo->assertSee('status-closed');
        $responseDomingo->assertSee('dot-pulse-wine');
        $responseDomingo->assertSee('Cerrado en este momento - Abrimos el Lunes a las 09:00 AM');

        // Caso 2: Miércoles en la noche a las 22:30 PM (Cerrado -> Próxima apertura el Jueves a las 09:00 AM)
        Carbon::setTestNow(Carbon::parse('2026-10-21 22:30:00', 'America/Mexico_City'));

        $responseNoche = $this->get('/');

        $responseNoche->assertStatus(200);
        $responseNoche->assertSee('status-closed');
        $responseNoche->assertSee('Cerrado en este momento - Abrimos el Jueves a las 09:00 AM');
    }

    /**
     * Criterio 5: Comprobación en base de datos:
     * Si se edita la hora de cierre o apertura de cualquier día en la base de datos y se recarga la página,
     * el cambio debe verse reflejado inmediatamente en pantalla.
     */
    public function test_edicion_de_horarios_en_base_de_datos_se_refleja_inmediatamente_al_recargar(): void
    {
        // Modificamos el horario del Martes en la base de datos
        HorarioSucursal::updateOrCreate(
            ['dia' => 'martes'],
            [
                'abierto' => true,
                'apertura' => '10:30',
                'cierre' => '19:45',
            ]
        );

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('10:30 AM a 07:45 PM');
    }
}
