<?php

namespace Tests\Feature;

use App\Models\CategoryProduct;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReceptionistInventoryKardexTest extends TestCase
{
    use RefreshDatabase;

    protected User $receptionist;
    protected CategoryProduct $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $role = Role::firstOrCreate(
            ['name' => 'RECEPCIONISTA'],
            ['description' => 'Rol de Recepcionista']
        );

        $this->receptionist = User::create([
            'id_rol'   => $role->id_rol,
            'name'     => 'Carlos',
            'lastname' => 'Gómez',
            'username' => 'carlos_recep',
            'email'    => 'carlos@celix.com',
            'password' => bcrypt('password123'),
            'state'    => true,
        ]);

        $this->category = CategoryProduct::create([
            'name'        => 'Pantallas y Displays',
            'description' => 'Módulos de pantalla originales y OLED',
            'status'      => true,
        ]);
    }

    public function test_can_create_product_and_generates_initial_kardex_movement(): void
    {
        $response = $this->actingAs($this->receptionist)->post('/recepcion/inventario/productos', [
            'bar_code'     => '7401999888',
            'name'         => 'Pantalla OLED Galaxy S22',
            'id_category'  => $this->category->id,
            'description'  => 'Pantalla completa con marco',
            'price'        => 550.00,
            'stock'        => 12,
            'minium_stock' => 3,
            'status'       => '1',
        ]);

        $response->assertRedirect('/recepcion/inventario');
        $this->assertDatabaseHas('products', [
            'bar_code' => '7401999888',
            'name'     => 'Pantalla OLED Galaxy S22',
            'stock'    => 12,
            'status'   => true,
        ]);

        $this->assertDatabaseHas('inventory_movements', [
            'product_bar_code' => '7401999888',
            'movement_type'    => 'ENTRADA',
            'quantity'         => 12,
            'id_user'          => $this->receptionist->id,
        ]);
    }

    public function test_can_update_product(): void
    {
        $product = Product::create([
            'bar_code'     => '7401555444',
            'name'         => 'Batería Original iPhone 13',
            'id_category'  => $this->category->id,
            'description'  => 'Batería de repuesto',
            'price'        => 320.00,
            'stock'        => 5,
            'minium_stock' => 2,
            'status'       => true,
        ]);

        $response = $this->actingAs($this->receptionist)->put("/recepcion/inventario/productos/{$product->bar_code}", [
            'name'         => 'Batería Original iPhone 13 Pro',
            'id_category'  => $this->category->id,
            'description'  => 'Batería de repuesto mejorada',
            'price'        => 350.00,
            'minium_stock' => 4,
            'status'       => '1',
        ]);

        $response->assertRedirect('/recepcion/inventario');
        $this->assertDatabaseHas('products', [
            'bar_code'     => '7401555444',
            'name'         => 'Batería Original iPhone 13 Pro',
            'price'        => 350.00,
            'minium_stock' => 4,
        ]);
    }

    public function test_can_toggle_product_status(): void
    {
        $product = Product::create([
            'bar_code'     => '7401333222',
            'name'         => 'Cable Lightning 1m',
            'id_category'  => $this->category->id,
            'price'        => 75.00,
            'stock'        => 10,
            'minium_stock' => 3,
            'status'       => true,
        ]);

        $response = $this->actingAs($this->receptionist)
            ->patch("/recepcion/inventario/productos/{$product->bar_code}/toggle-status");

        $response->assertRedirect('/recepcion/inventario');
        $this->assertDatabaseHas('products', [
            'bar_code' => '7401333222',
            'status'   => false,
        ]);
    }

    public function test_can_create_and_toggle_category(): void
    {
        $response = $this->actingAs($this->receptionist)->post('/recepcion/inventario/categorias', [
            'name'        => 'Herramientas de Precisión',
            'description' => 'Pinzas y destornilladores',
            'status'      => '1',
        ]);

        $response->assertRedirect('/recepcion/inventario');
        $category = CategoryProduct::where('name', 'Herramientas de Precisión')->first();
        $this->assertNotNull($category);

        // Toggle state
        $toggleResponse = $this->actingAs($this->receptionist)
            ->patch("/recepcion/inventario/categorias/{$category->id}/toggle-status");

        $toggleResponse->assertRedirect('/recepcion/inventario');
        $this->assertDatabaseHas('category_products', [
            'id'     => $category->id,
            'status' => false,
        ]);
    }

    public function test_can_register_manual_inventory_movements(): void
    {
        $product = Product::create([
            'bar_code'     => '7401111999',
            'name'         => 'Vidrio Templado Cerámico',
            'id_category'  => $this->category->id,
            'price'        => 50.00,
            'stock'        => 20,
            'minium_stock' => 5,
            'status'       => true,
        ]);

        // Registrar SALIDA por daño
        $responseOut = $this->actingAs($this->receptionist)->post('/recepcion/inventario/movimientos', [
            'product_bar_code' => $product->bar_code,
            'movement_type'    => 'SALIDA',
            'quantity'         => 3,
            'reason'           => 'Producto dañado o en mal estado',
            'notes'            => 'Caja aplastada durante transporte',
        ]);

        $responseOut->assertSessionHasNoErrors();
        $product->refresh();
        $this->assertEquals(17, $product->stock);

        // Registrar AJUSTE por conteo físico
        $responseAdj = $this->actingAs($this->receptionist)->post('/recepcion/inventario/movimientos', [
            'product_bar_code' => $product->bar_code,
            'movement_type'    => 'AJUSTE',
            'quantity'         => 15,
            'reason'           => 'Discrepancia en conteo físico de inventario',
        ]);

        $responseAdj->assertSessionHasNoErrors();
        $product->refresh();
        $this->assertEquals(15, $product->stock);

        // Verificar que ambos movimientos están en la base de datos
        $this->assertEquals(2, InventoryMovement::where('product_bar_code', $product->bar_code)->count());
    }
}
