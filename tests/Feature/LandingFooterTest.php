<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingFooterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Criterio 1: En la parte inferior de la página deben mostrarse los íconos visibles
     * de las redes sociales oficiales del negocio (Facebook, Instagram, TikTok).
     */
    public function test_redes_sociales_oficiales_iconos_visibles(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('id="footerSocialNetworks"', false);
        $response->assertSee('id="socialFacebookBtn"', false);
        $response->assertSee('id="socialInstagramBtn"', false);
        $response->assertSee('id="socialTiktokBtn"', false);

        // Nombres legibles e iconos SVG
        $response->assertSee('Facebook');
        $response->assertSee('Instagram');
        $response->assertSee('TikTok');
    }

    /**
     * Criterio 2: Al hacer clic en cualquiera de los íconos de redes sociales,
     * debe abrirse la página de la red social correspondiente en una pestaña nueva
     * del navegador sin cerrar la landing page (target="_blank" y rel="noopener noreferrer").
     */
    public function test_redes_sociales_abren_en_pestana_nueva_sin_cerrar_landing(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);

        // Facebook
        $response->assertSee('href="https://www.facebook.com/beautyandbarberstudio"', false);
        // Instagram
        $response->assertSee('href="https://www.instagram.com/beautyandbarberstudio"', false);
        // TikTok
        $response->assertSee('href="https://www.tiktok.com/@beautyandbarberstudio"', false);

        // Verificación de apertura en nueva pestaña sin cerrar el sitio
        $content = $response->getContent();
        $this->assertMatchesRegularExpression(
            '/id="socialFacebookBtn"[^>]*target="_blank"[^>]*rel="noopener noreferrer"/',
            $content
        );
        $this->assertMatchesRegularExpression(
            '/id="socialInstagramBtn"[^>]*target="_blank"[^>]*rel="noopener noreferrer"/',
            $content
        );
        $this->assertMatchesRegularExpression(
            '/id="socialTiktokBtn"[^>]*target="_blank"[^>]*rel="noopener noreferrer"/',
            $content
        );
    }

    /**
     * Criterio 3: Debe mostrarse el texto de derechos reservados indicando el nombre
     * del estudio y el año vigente de manera correcta.
     */
    public function test_texto_derechos_reservados_nombre_estudio_y_ano_vigente(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('id="footerCopyright"', false);

        $currentYear = date('Y');
        $response->assertSee($currentYear);
        $response->assertSee('Beauty &amp; Barber Studio', false);
        $response->assertSee('Todos los derechos reservados');
    }

    /**
     * Criterio 4: Deben ser visibles los textos o enlaces para consultar
     * el "Aviso de Privacidad" y los "Términos y Condiciones".
     */
    public function test_enlaces_aviso_privacidad_y_terminos_condiciones_visibles(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('id="footerLegalLinks"', false);
        $response->assertSee('id="btnAvisoPrivacidad"', false);
        $response->assertSee('Aviso de Privacidad');
        $response->assertSee('id="btnTerminosCondiciones"', false);
        $response->assertSee('Términos y Condiciones');
    }

    /**
     * Verificación del modal interactivo que despliega el contenido legal
     * sin recargar ni salir de la página.
     */
    public function test_modal_legal_informativo_presente_con_contenido(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('id="legalModalBackdrop"', false);
        $response->assertSee('id="legalModalCard"', false);
        $response->assertSee('id="legalModalTitle"', false);
        $response->assertSee('id="legalModalBody"', false);
        $response->assertSee('id="legalModalCloseBtn"', false);
        $response->assertSee('id="btnLegalDismiss"', false);
        $response->assertSee('Entendido');
    }
}
