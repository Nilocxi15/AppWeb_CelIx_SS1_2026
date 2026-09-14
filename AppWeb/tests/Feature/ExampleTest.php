<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

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

        $response->assertStatus(404);
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

    public function test_admin_dashboard_loads_for_authenticated_admin(): void
    {
        $admin = \App\Models\User::first();

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('Inicio');
        $response->assertSee('Gestión de Usuarios');
        $response->assertSee('Cel');
    }

    public function test_admin_users_index_loads_with_crud_elements(): void
    {
        $admin = \App\Models\User::first();

        $response = $this->actingAs($admin)->get('/admin/users');

        $response->assertStatus(200);
        $response->assertSee('Gestión de Usuarios');
        $response->assertSee('Total Usuarios');
        $response->assertSee('Usuarios Activos');
        $response->assertSee('Nuevo Usuario');
        $response->assertSee('Anterior');
        $response->assertSee('Siguiente');
        $response->assertSee('Mostrando');
        $response->assertSee('5 registros');
        $response->assertSee('25 registros');
        $response->assertSee('50 registros');
        $response->assertSee('Cambiar Contraseña de Usuario');
        $response->assertDontSee('¿Eliminar este usuario?');
    }
}

