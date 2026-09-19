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
        $response->assertSee('registros por página');
        $response->assertSee('Cambiar Contraseña de Usuario');
        $response->assertDontSee('¿Eliminar este usuario?');
    }

    public function test_receptionist_home_loads_with_required_elements(): void
    {
        $user = \App\Models\User::first();

        $category = \App\Models\CategoryProduct::create([
            'name' => 'Cargadores',
            'description' => 'Cargadores y cables'
        ]);

        \App\Models\Product::create([
            'bar_code' => '7401001001',
            'id_category' => $category->id,
            'name' => 'Cargador Rápido 25W',
            'description' => 'Cargador Tipo-C',
            'stock' => 10,
            'minium_stock' => 5,
            'price' => 145.00,
        ]);

        $response = $this->actingAs($user)->get('/recepcion');

        $response->assertStatus(200);
        $response->assertSee('Registrar Dispositivo');
        $response->assertSee('Registrar Venta');
        $response->assertSee('Inicio');
        $response->assertSee('Inventario');
        $response->assertSee('Historiales');
        $response->assertSee('Perfil');
        $response->assertSee('Cerrar Sesión');
        $response->assertSee('Catálogo de Productos para Venta');
        $response->assertSee('Cargador Rápido 25W');
        $response->assertSee('Anterior');
        $response->assertSee('Siguiente');
    }

    public function test_receptionist_inventory_loads_with_required_elements(): void
    {
        $user = \App\Models\User::first();

        $category = \App\Models\CategoryProduct::create([
            'name' => 'Cargadores y Cables',
            'description' => 'Cargadores de pared'
        ]);

        \App\Models\Product::create([
            'bar_code' => '7401001001',
            'id_category' => $category->id,
            'name' => 'Cargador Rápido 25W Ultra',
            'description' => 'Cargador Tipo-C',
            'stock' => 18,
            'minium_stock' => 5,
            'price' => 145.00,
            'status' => true,
        ]);

        $response = $this->actingAs($user)->get('/recepcion/inventario');

        $response->assertStatus(200);
        $response->assertSee('Gestión de Inventario y Almacén');
        $response->assertSee('Gestión de Productos');
        $response->assertSee('Gestión de Categorías');
        $response->assertDontSee('id="tab-movements"', false);
        $response->assertSee('Registrar Movimiento');
        $response->assertSee('Nueva Categoría');
        $response->assertSee('Nuevo Producto');
        $response->assertSee('Cargador Rápido 25W Ultra');
        $response->assertSee('Cargadores y Cables');
    }

    public function test_receptionist_kardex_loads_with_required_elements(): void
    {
        $user = \App\Models\User::first();

        $response = $this->actingAs($user)->get('/recepcion/historiales');

        $response->assertStatus(200);
        $response->assertSee('Historial de Movimientos de Inventario (Kardex)');
        $response->assertSee('Movimientos Registrados');
        $response->assertSee('Entradas de Mercancía');
        $response->assertSee('Salidas y Bajas por Daño');
        $response->assertSee('Ajustes de Conteo Físico');
        $response->assertSee('Registrar Movimiento');
    }
}



