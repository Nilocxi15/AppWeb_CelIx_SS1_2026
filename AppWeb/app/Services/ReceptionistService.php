<?php

namespace App\Services;

use App\Models\CategoryProduct;
use App\Models\FinancialTransaction;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReceptionistService
{
    /**
     * Obtener productos paginados con filtros de búsqueda, categoría, estado de stock y ordenamiento.
     *
     * @param array $filters Filtros aplicables (search, category, stock_status, sort_by, sort_direction)
     * @param int $perPage Cantidad de elementos por página
     * @return LengthAwarePaginator
     */
    public function getProductsPaginated(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = Product::with('category');

        // Búsqueda insensible a mayúsculas por código de barras, nombre o descripción
        if (!empty($filters['search'])) {
            $search = mb_strtolower(trim($filters['search']));
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(bar_code) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(description) LIKE ?', ["%{$search}%"]);
            });
        }

        // Filtro por categoría (nombre o ID)
        if (!empty($filters['category'])) {
            $category = $filters['category'];
            $query->whereHas('category', function ($q) use ($category) {
                $q->where('name', $category)
                  ->orWhere('id', $category);
            });
        }

        // Filtro por estado de stock
        if (!empty($filters['stock_status'])) {
            switch ($filters['stock_status']) {
                case 'disponible':
                    $query->whereColumn('stock', '>', 'minium_stock');
                    break;
                case 'bajo':
                    $query->whereColumn('stock', '<=', 'minium_stock')
                          ->where('stock', '>', 0);
                    break;
                case 'agotado':
                    $query->where('stock', '<=', 0);
                    break;
            }
        }

        // Ordenamiento dinámico seguro mediante lista blanca
        $allowedSorts = [
            'barcode'  => 'bar_code',
            'bar_code' => 'bar_code',
            'name'     => 'name',
            'stock'    => 'stock',
            'price'    => 'price',
        ];

        $sortBy = $allowedSorts[$filters['sort_by'] ?? 'name'] ?? 'name';
        $sortDirection = strtolower($filters['sort_direction'] ?? 'asc') === 'desc' ? 'desc' : 'asc';

        return $query->orderBy($sortBy, $sortDirection)
                     ->paginate($perPage)
                     ->withQueryString();
    }

    /**
     * Obtener el catálogo completo de productos con sus categorías para el selector del modal de venta.
     *
     * @return Collection
     */
    public function getAllProductsForSale(): Collection
    {
        return Product::with('category')->orderBy('name', 'asc')->get();
    }

    /**
     * Obtener el catálogo completo de productos con su categoría asociada (legacy/fallback).
     *
     * @return array
     */
    public function getProductsForCatalog(): array
    {
        $products = Product::with('category')->orderBy('name', 'asc')->get();

        return $products->map(function ($p) {
            return [
                'bar_code'      => $p->bar_code,
                'name'          => $p->name,
                'category_name' => $p->category?->name ?? 'Sin Categoría',
                'description'   => $p->description,
                'stock'         => (int) $p->stock,
                'minium_stock'  => (int) $p->minium_stock,
                'price'         => (float) $p->price,
                'image'         => $p->image,
            ];
        })->toArray();
    }

    /**
     * Obtener los KPIs calculados en tiempo real para el catálogo de recepción.
     *
     * @return array
     */
    public function getCatalogKPIs(): array
    {
        $products = Product::all(['stock', 'minium_stock']);

        return [
            'totalProducts' => $products->count(),
            'inStock'       => $products->where('stock', '>', 5)->count(),
            'lowStock'      => $products->filter(fn ($p) => $p->stock <= $p->minium_stock && $p->stock > 0)->count(),
            'outOfStock'    => $products->where('stock', '<=', 0)->count(),
        ];
    }

    /**
     * Obtener todas las categorías de productos disponibles para filtros.
     *
     * @return Collection
     */
    public function getCategories(): Collection
    {
        return CategoryProduct::orderBy('name', 'asc')->get();
    }

    /**
     * Registrar una venta multi-artículos de manera transaccional:
     * 1. Valida disponibilidad de stock en tiempo real con bloqueo de fila.
     * 2. Registra la cabecera de venta (sales).
     * 3. Registra el detalle de ítems (sale_details).
     * 4. Descuenta el inventario físico en products.
     * 5. REGISTRA EL MOVIMIENTO EN EL KARDEX (inventory_movements como SALIDA).
     * 6. REGISTRA EL INGRESO EN EL LIBRO MAYOR (financial_transactions como INGRESO).
     *
     * @param array $data Datos validados de la venta
     * @param int $userId ID del recepcionista autenticado
     * @return Sale
     * @throws ValidationException
     */
    public function registerSale(array $data, int $userId): Sale
    {
        return DB::transaction(function () use ($data, $userId) {
            $itemsData = $data['items'];
            $barcodes = collect($itemsData)->pluck('barcode')->unique()->toArray();

            // Bloquear filas de productos seleccionados para prevenir condiciones de carrera (race conditions)
            $products = Product::whereIn('bar_code', $barcodes)->lockForUpdate()->get()->keyBy('bar_code');

            $totalSale = 0.0;
            $saleLines = [];

            // Validar stock y calcular montos basados en el precio verificado de la base de datos
            foreach ($itemsData as $item) {
                $barcode = $item['barcode'];
                $quantity = (int) $item['quantity'];

                if (!$products->has($barcode)) {
                    throw ValidationException::withMessages([
                        'items' => ["El producto con código {$barcode} no fue encontrado."],
                    ]);
                }

                $product = $products->get($barcode);

                if ($product->stock < $quantity) {
                    throw ValidationException::withMessages([
                        'items' => ["Stock insuficiente para '{$product->name}'. Disponible: {$product->stock} unidad(es), solicitadas: {$quantity}."],
                    ]);
                }

                $unitPrice = (float) $product->price;
                $subtotal = $unitPrice * $quantity;
                $totalSale += $subtotal;

                $saleLines[] = [
                    'product'    => $product,
                    'quantity'   => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal'   => $subtotal,
                ];
            }

            // Cabecera de Venta
            $sale = Sale::create([
                'id_user_receptionist' => $userId,
                'sale_date'            => now(),
                'total_sale'           => $totalSale,
            ]);

            // Detalle de Venta, Descuento de Stock y Registro en Kardex
            foreach ($saleLines as $line) {
                $product = $line['product'];
                $quantity = $line['quantity'];

                // 2.1 Detalle maestro-detalle
                SaleDetail::create([
                    'id_sale'          => $sale->id,
                    'product_bar_code' => $product->bar_code,
                    'quantity'         => $quantity,
                    'unit_price'       => $line['unit_price'],
                    'subtotal'         => $line['subtotal'],
                ]);

                // Descuento de stock en catálogo
                $product->decrement('stock', $quantity);

                // Registro en Kardex (inventory_movements)
                InventoryMovement::create([
                    'product_bar_code' => $product->bar_code,
                    'id_user'          => $userId,
                    'id_sale'          => $sale->id,
                    'quantity'         => $quantity,
                    'movement_type'    => 'SALIDA',
                    'date'             => now(),
                ]);
            }

            // Registro en el Libro Mayor Financiero (financial_transactions)
            $paymentMethod = $data['payment_method'] ?? 'EFECTIVO';
            FinancialTransaction::create([
                'id_sale'   => $sale->id,
                'id_ticket' => null,
                'amount'    => $totalSale,
                'type'      => 'INGRESO',
                'concept'   => "Venta en mostrador #{$sale->id} [{$paymentMethod}]",
                'date'      => now(),
            ]);

            return $sale->load(['details.product', 'receptionist']);
        });
    }
}
