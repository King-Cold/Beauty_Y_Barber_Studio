<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingLocationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Criterio 1: La dirección física debe estar escrita en su totalidad
     * (calle, número exterior, colonia, ciudad y código postal).
     */
    public function test_direccion_fisica_escrita_en_su_totalidad(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('id="fullAddressText"', false);
        $response->assertSee('Av. Insurgentes Sur');
        $response->assertSee('1285');
        $response->assertSee('Col. Del Valle Sur');
        $response->assertSee('Benito Juárez');
        $response->assertSee('Ciudad de México');
        $response->assertSee('C.P. 03100');
    }

    /**
     * Criterio 2 y 3: Debe incluirse un botón con la leyenda 'Copiar Dirección'.
     * Al pulsar el botón, debe existir la notificación visual o mensaje flotante: 'Dirección copiada'.
     */
    public function test_boton_copiar_direccion_y_notificacion_toast_presentes(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('id="btnCopyAddress"', false);
        $response->assertSee('Copiar Dirección');

        // Notificación flotante Toast
        $response->assertSee('id="copyToast"', false);
        $response->assertSee('toast-notification');
        $response->assertSee('Dirección copiada');
    }

    /**
     * Criterio 4: Debe mostrarse un recuadro con un mapa interactivo funcional en el que se aprecie la ubicación.
     */
    public function test_mapa_interactivo_funcional_incrustado(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('id="interactiveMapCard"', false);
        $response->assertSee('map-iframe');
        $response->assertSee('<iframe', false);
        $response->assertSee('maps.google.com/maps', false);
    }

    /**
     * Criterio 5: Debe existir un botón con el texto 'Cómo llegar' o 'Abrir en Google Maps'
     * que abra la aplicación o sitio de Google Maps en una pestaña nueva del navegador.
     */
    public function test_boton_abrir_google_maps_en_pestana_nueva(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('id="btnOpenGoogleMaps"', false);
        $response->assertSee('Abrir en Google Maps');
        $response->assertSee('target="_blank"', false);
        $response->assertSee('rel="noopener noreferrer"', false);
        $response->assertSee('google.com/maps', false);
    }

    /**
     * Criterio 6: Debe incluirse un botón con el número telefónico oficial que abra la app de llamadas con tel:.
     */
    public function test_boton_telefonico_oficial_con_enlace_tel(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('id="btnCallStudio"', false);
        $response->assertSee('href="tel:+525556789012"', false);
        $response->assertSee('+52 55 5678 9012');
        $response->assertSee('Llamar:');
    }

    /**
     * Criterio 7: Debe incluir una imagen de como se ve la fachada de Beauty & Barber Studio
     * al lado del mapa interactivo usando la fotografía proporcionada.
     */
    public function test_imagen_fachada_beauty_barber_studio_presente_junto_al_mapa(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('id="facadeCard"', false);
        $response->assertSee('location-visual-grid');
        $response->assertSee(asset('images/fachada.jpg'));
        $response->assertSee('Fachada exterior oficial de Beauty & Barber Studio');
    }
}
