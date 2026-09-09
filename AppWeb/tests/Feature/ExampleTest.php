<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_fallback_route_returns_not_found_view(): void
    {
        $response = $this->get('/ruta-inexistente-12345');

        $response->assertStatus(200);
        $response->assertSee('Página no encontrada');
        $response->assertSee('Error 404');
    }

    public function test_forgot_password_route_returns_recover_password_view(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
        $response->assertSee('Recuperar contraseña');
        $response->assertSee('Correo Electrónico');
        $response->assertSee('Genera Código');
        $response->assertSee('Nueva Contraseña');
        $response->assertSee('Confirmar Nueva Contraseña');
        $response->assertSee('Código de Verificación');
        $response->assertSee('Confirmar cambio de contraseña');
    }
}
