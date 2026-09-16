<?php

namespace Database\Seeders;

use App\Models\CategoryProduct;
use App\Models\Product;
use App\Models\InventoryMovement;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryAndProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // 1. Categorías del Catálogo
            $categoriesData = [
                ['name' => 'Cargadores', 'description' => 'Cargadores de pared, cables y adaptadores.'],
                ['name' => 'Audio', 'description' => 'Audífonos, bocinas y accesorios de sonido.'],
                ['name' => 'Fundas', 'description' => 'Protectores, estuches y vidrios templados.'],
                ['name' => 'Repuestos', 'description' => 'Pantallas, baterías y componentes de reemplazo.'],
                ['name' => 'Accesorios', 'description' => 'Soportes, correas y complementos para dispositivos.'],
            ];

            $categoryMap = [];
            foreach ($categoriesData as $cat) {
                $category = CategoryProduct::firstOrCreate(
                    ['name' => $cat['name']],
                    ['description' => $cat['description']]
                );
                $categoryMap[$cat['name']] = $category->id;
            }

            // Usuario para registrar entradas de inventario iniciales (admin)
            $adminUser = User::first();
            $adminId = $adminUser ? $adminUser->id : 1;

            // 2. Exactamente 5 Productos de Prueba
            $productsData = [
                [
                    'bar_code'     => '7401001001',
                    'category'     => 'Cargadores',
                    'name'         => 'Cargador Rápido 25W USB-C Ultra Fast',
                    'description'  => 'Cargador de pared de carga súper rápida compatible con Samsung y dispositivos Tipo-C.',
                    'stock'        => 18,
                    'minium_stock' => 5,
                    'price'        => 145.00,
                ],
                [
                    'bar_code'     => '7401001002',
                    'category'     => 'Cargadores',
                    'name'         => 'Cable Reforzado USB-C a Lightning 1.2m',
                    'description'  => 'Cable trenzado de alta resistencia con certificación MFi para iPhone e iPad.',
                    'stock'        => 4,
                    'minium_stock' => 5,
                    'price'        => 85.00,
                ],
                [
                    'bar_code'     => '7401001003',
                    'category'     => 'Audio',
                    'name'         => 'Audífonos Inalámbricos Bluetooth TWS-500',
                    'description'  => 'Cancelación activa de ruido, estuche con batería de 24h y control táctil.',
                    'stock'        => 12,
                    'minium_stock' => 5,
                    'price'        => 220.00,
                ],
                [
                    'bar_code'     => '7401001004',
                    'category'     => 'Fundas',
                    'name'         => 'Vidrio Templado Cerámico 9D para iPhone 15',
                    'description'  => 'Protector de pantalla flexible antihuellas con cobertura completa bordes curvos.',
                    'stock'        => 25,
                    'minium_stock' => 5,
                    'price'        => 45.00,
                ],
                [
                    'bar_code'     => '7401001005',
                    'category'     => 'Accesorios',
                    'name'         => 'Soporte Magnético Ajustable para Tablero Auto',
                    'description'  => 'Fijación de alta potencia con imanes de neodimio y brazo telescópico.',
                    'stock'        => 0,
                    'minium_stock' => 5,
                    'price'        => 65.00,
                ],
            ];

            foreach ($productsData as $item) {
                $product = Product::updateOrCreate(
                    ['bar_code' => $item['bar_code']],
                    [
                        'id_category'  => $categoryMap[$item['category']],
                        'name'         => $item['name'],
                        'description'  => $item['description'],
                        'stock'        => $item['stock'],
                        'minium_stock' => $item['minium_stock'],
                        'price'        => $item['price'],
                        'image'        => null,
                    ]
                );

                // Registrar movimiento inicial de Kardex (ENTRADA) si hay stock
                if ($item['stock'] > 0) {
                    InventoryMovement::firstOrCreate(
                        [
                            'product_bar_code' => $product->bar_code,
                            'movement_type'    => 'ENTRADA',
                            'id_sale'          => null,
                        ],
                        [
                            'id_user'  => $adminId,
                            'quantity' => $item['stock'],
                            'date'     => now(),
                        ]
                    );
                }
            }
        });
    }
}
