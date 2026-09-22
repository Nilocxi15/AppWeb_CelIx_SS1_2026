<?php

namespace App\Services;

use App\Models\CategoryProduct;
use App\Models\Client;
use App\Models\Device;
use App\Models\DeviceType;
use App\Models\FinancialTransaction;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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

        // Filtro por estado activo/inactivo (borrado lógico)
        if (!empty($filters['status'])) {
            if ($filters['status'] === 'activo') {
                $query->where('status', true);
            } elseif ($filters['status'] === 'inactivo') {
                $query->where('status', false);
            }
        }

        // Ordenamiento dinámico seguro mediante lista blanca
        $allowedSorts = [
            'barcode'  => 'bar_code',
            'bar_code' => 'bar_code',
            'name'     => 'name',
            'stock'    => 'stock',
            'price'    => 'price',
            'status'   => 'status',
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
                    'reason'           => "Venta en mostrador #{$sale->id}",
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

    /**
     * Obtener métricas rápidas (KPIs) en tiempo real para el inventario.
     */
    public function getInventoryKPIs(): array
    {
        return [
            'total_products'    => Product::count(),
            'total_stock'       => (int) (Product::sum('stock') ?? 0),
            'low_stock_count'   => Product::whereColumn('stock', '<=', 'minium_stock')->where('stock', '>', 0)->count(),
            'active_categories' => CategoryProduct::where('status', true)->count(),
        ];
    }

    /**
     * Obtener categorías de productos con conteo de artículos y filtros para la pestaña de categorías.
     */
    public function getInventoryCategories(array $filters = []): Collection
    {
        $query = CategoryProduct::withCount('products');

        if (!empty($filters['category_search'])) {
            $search = mb_strtolower(trim($filters['category_search']));
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(description) LIKE ?', ["%{$search}%"]);
            });
        }

        if (!empty($filters['category_status'])) {
            if ($filters['category_status'] === 'activa') {
                $query->where('status', true);
            } elseif ($filters['category_status'] === 'inactiva') {
                $query->where('status', false);
            }
        }

        return $query->orderBy('name', 'asc')->get();
    }

    /**
     * Registrar un nuevo producto en catálogo con movimiento inicial de Kardex si tiene stock.
     */
    public function storeProduct(array $data, int $userId): Product
    {
        return DB::transaction(function () use ($data, $userId) {
            $product = Product::create([
                'bar_code'     => $data['bar_code'],
                'name'         => $data['name'],
                'id_category'  => $data['id_category'],
                'description'  => $data['description'] ?? null,
                'price'        => $data['price'],
                'stock'        => $data['stock'],
                'minium_stock' => $data['minium_stock'],
                'status'       => $data['status'] ?? true,
            ]);

            if ($product->stock > 0) {
                InventoryMovement::create([
                    'product_bar_code' => $product->bar_code,
                    'id_user'          => $userId,
                    'id_sale'          => null,
                    'quantity'         => $product->stock,
                    'movement_type'    => 'ENTRADA',
                    'reason'           => 'Registro inicial de existencias en almacén',
                    'date'             => now(),
                ]);
            }

            return $product;
        });
    }

    /**
     * Actualizar los datos de un producto del catálogo.
     */
    public function updateProduct(string $barcode, array $data): Product
    {
        $product = Product::findOrFail($barcode);

        $product->update([
            'name'         => $data['name'],
            'id_category'  => $data['id_category'],
            'description'  => $data['description'] ?? null,
            'price'        => $data['price'],
            'minium_stock' => $data['minium_stock'],
            'status'       => $data['status'] ?? $product->status,
        ]);

        return $product;
    }

    /**
     * Alternar estado activo / inactivo de un producto (borrado lógico CelIx).
     */
    public function toggleProductStatus(string $barcode): Product
    {
        $product = Product::findOrFail($barcode);
        $product->status = !$product->status;
        $product->save();

        return $product;
    }

    /**
     * Registrar una nueva categoría de productos.
     */
    public function storeCategory(array $data): CategoryProduct
    {
        return CategoryProduct::create([
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
            'status'      => $data['status'] ?? true,
        ]);
    }

    /**
     * Actualizar una categoría existente.
     */
    public function updateCategory(int $id, array $data): CategoryProduct
    {
        $category = CategoryProduct::findOrFail($id);
        $category->update([
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
            'status'      => $data['status'] ?? $category->status,
        ]);

        return $category;
    }

    /**
     * Alternar estado activo / inactivo de una categoría (borrado lógico CelIx).
     */
    public function toggleCategoryStatus(int $id): CategoryProduct
    {
        $category = CategoryProduct::findOrFail($id);
        $category->status = !$category->status;
        $category->save();

        return $category;
    }

    /**
     * Registrar un movimiento de almacén (Entrada, Salida por baja/merma o Ajuste físico) con trazabilidad.
     */
    public function storeInventoryMovement(array $data, int $userId): InventoryMovement
    {
        return DB::transaction(function () use ($data, $userId) {
            $product = Product::where('bar_code', $data['product_bar_code'])->lockForUpdate()->firstOrFail();

            $type = $data['movement_type'];
            $qty = (int) $data['quantity'];
            $movementQty = $qty;

            if ($type === 'ENTRADA') {
                $product->increment('stock', $qty);
            } elseif ($type === 'SALIDA') {
                if ($product->stock < $qty) {
                    throw ValidationException::withMessages([
                        'quantity' => ["Stock insuficiente para procesar la baja. Stock actual disponible: {$product->stock} unidades."],
                    ]);
                }
                $product->decrement('stock', $qty);
                $movementQty = -$qty;
            } elseif ($type === 'AJUSTE') {
                $discrepancy = $qty - $product->stock;
                $product->stock = $qty;
                $product->save();
                $movementQty = $discrepancy;
            }

            $reason = $data['reason'];
            if (!empty($data['notes'])) {
                $reason .= ' - ' . trim($data['notes']);
            }

            return InventoryMovement::create([
                'product_bar_code' => $product->bar_code,
                'id_user'          => $userId,
                'id_sale'          => null,
                'quantity'         => $movementQty,
                'movement_type'    => $type,
                'reason'           => $reason,
                'date'             => now(),
            ]);
        });
    }

    /**
     * Obtener movimientos de Kardex paginados con filtros de auditoría.
     */
    public function getKardexMovements(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = InventoryMovement::with(['product.category', 'user', 'sale']);

        if (!empty($filters['search'])) {
            $search = mb_strtolower(trim($filters['search']));
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(product_bar_code) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(reason) LIKE ?', ["%{$search}%"])
                  ->orWhereHas('product', function ($pq) use ($search) {
                      $pq->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
                  })
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                        ->orWhereRaw('LOWER(lastname) LIKE ?', ["%{$search}%"]);
                  });
            });
        }

        if (!empty($filters['movement_type'])) {
            $query->where('movement_type', $filters['movement_type']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('date', '<=', $filters['date_to']);
        }

        return $query->orderBy('date', 'desc')->orderBy('id', 'desc')
                     ->paginate($perPage)
                     ->withQueryString();
    }

    /**
     * Métricas rápidas (KPIs) para la vista de Kardex / Historiales.
     */
    public function getKardexKPIs(): array
    {
        return [
            'total_movements' => InventoryMovement::count(),
            'total_in'        => InventoryMovement::where('movement_type', 'ENTRADA')->count(),
            'total_out'       => InventoryMovement::where('movement_type', 'SALIDA')->count(),
            'total_adj'       => InventoryMovement::where('movement_type', 'AJUSTE')->count(),
        ];
    }

    /**
     * Obtener el catálogo de clientes ordenados alfabéticamente.
     */
    public function getClients(): Collection
    {
        return Client::orderBy('name', 'asc')->orderBy('lastname', 'asc')->get();
    }

    /**
     * Obtener la lista de usuarios elegibles para atender reparaciones (Técnicos y Administradores activos),
     * calculando su carga de trabajo en tickets activos (no finalizados ni entregados) para sugerir al de menor carga.
     */
    public function getActiveTechnicians(): Collection
    {
        return User::whereHas('role', function ($q) {
            $q->whereIn(DB::raw('UPPER(name)'), ['TECNICO', 'TÉCNICO', 'ADMINISTRADOR']);
        })
        ->where('state', true)
        ->with('role')
        ->withCount(['assignedTickets as active_tickets_count' => function ($q) {
            $q->whereNotIn('state', ['Finalizado', 'Entregado']);
        }])
        ->orderBy('active_tickets_count', 'asc')
        ->orderBy('name', 'asc')
        ->get();
    }

    /**
     * Obtener los tipos de dispositivos activos.
     */
    public function getActiveDeviceTypes(): Collection
    {
        return DeviceType::where('status', true)->orderBy('name', 'asc')->get();
    }

    /**
     * Obtener el listado de tickets de servicio técnico para el módulo de entregas,
     * permitiendo visualizar todos los tickets y filtrar opcionalmente por estado.
     */
    public function getCompletedTicketsPaginated(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = Ticket::with(['device.client', 'device.deviceType', 'technician', 'receptionist']);

        // Filtro por estado de ticket (opcional)
        if (!empty($filters['state'])) {
            $query->where('state', $filters['state']);
        }

        // Filtro por término de búsqueda (ID ticket, cliente, teléfono, marca o modelo)
        if (!empty($filters['search'])) {
            $search = mb_strtolower(trim($filters['search']));
            $query->where(function ($q) use ($search) {
                if (is_numeric($search)) {
                    $q->where('id', (int) $search);
                }
                $q->orWhereHas('device.client', function ($clientQ) use ($search) {
                    $clientQ->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                            ->orWhereRaw('LOWER(lastname) LIKE ?', ["%{$search}%"])
                            ->orWhereRaw('LOWER(phone) LIKE ?', ["%{$search}%"])
                            ->orWhereRaw('LOWER(dpi) LIKE ?', ["%{$search}%"]);
                })
                ->orWhereHas('device', function ($deviceQ) use ($search) {
                    $deviceQ->whereRaw('LOWER(brand) LIKE ?', ["%{$search}%"])
                            ->orWhereRaw('LOWER(model) LIKE ?', ["%{$search}%"])
                            ->orWhereRaw('LOWER(serial_number) LIKE ?', ["%{$search}%"]);
                });
            });
        }

        // Filtro por rango de fechas de recepción
        if (!empty($filters['date_from'])) {
            $query->whereDate('intake_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('intake_date', '<=', $filters['date_to']);
        }

        return $query->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
    }

    /**
     * Obtener métricas rápidas (KPIs) sobre tickets de servicio técnico y entregas.
     */
    public function getCompletedTicketsKPIs(): array
    {
        return [
            'in_workshop'          => Ticket::whereIn('state', ['Recibido', 'Diagnóstico', 'Reparación'])->count(),
            'total_completed'      => Ticket::where('state', 'Finalizado')->count(),
            'total_pending_amount' => (float) Ticket::where('state', 'Finalizado')->sum('remaining_balance'),
            'delivered_today'      => Ticket::where('state', 'Entregado')->whereDate('return_date', today())->count(),
            'total_delivered'      => Ticket::where('state', 'Entregado')->count(),
            'collected_today'      => (float) FinancialTransaction::whereNotNull('id_ticket')
                                        ->where('type', 'INGRESO')
                                        ->whereDate('date', today())
                                        ->sum('amount'),
        ];
    }

    /**
     * Procesar la entrega de un dispositivo reparado:
     * - Cambia el estado del ticket a 'Entregado'.
     * - Registra la fecha de entrega (return_date).
     * - Si hay monto liquidado, crea un registro de INGRESO en financial_transactions.
     */
    public function deliverTicket(int $ticketId, array $data, int $userId): Ticket
    {
        return DB::transaction(function () use ($ticketId, $data, $userId) {
            $ticket = Ticket::with(['device.client', 'device.deviceType'])->findOrFail($ticketId);

            if ($ticket->state !== 'Finalizado') {
                throw ValidationException::withMessages([
                    'ticket' => 'Solo se pueden entregar tickets que se encuentren en estado Finalizado.',
                ]);
            }

            $returnDate    = !empty($data['return_date']) ? $data['return_date'] : now();
            $amountToPay   = isset($data['amount_to_pay']) ? (float) $data['amount_to_pay'] : (float) $ticket->remaining_balance;
            $paymentMethod = $data['payment_method'] ?? 'EFECTIVO';

            // Agregar notas de entrega si fueron provistas
            if (!empty($data['delivery_notes'])) {
                $notes = trim($ticket->reception_notes ? $ticket->reception_notes . "\n" : '');
                $ticket->reception_notes = $notes . "[Nota de Entrega " . date('d/m/Y H:i') . "]: " . trim($data['delivery_notes']);
            }

            // Cambiar estado a Entregado y registrar fecha de entrega
            $ticket->state       = 'Entregado';
            $ticket->return_date = $returnDate;
            $ticket->save();

            // Registrar movimiento en el libro mayor financiero (financial_transactions) si hay monto liquidado
            if ($amountToPay > 0) {
                $brand  = $ticket->device->brand ?? 'Dispositivo';
                $model  = $ticket->device->model ?? '';
                $client = ($ticket->device->client->name ?? '') . ' ' . ($ticket->device->client->lastname ?? '');

                FinancialTransaction::create([
                    'id_sale'   => null,
                    'id_ticket' => $ticket->id,
                    'amount'    => $amountToPay,
                    'type'      => 'INGRESO',
                    'concept'   => "Liquidación y entrega de ticket #{$ticket->id} [{$brand} {$model}] - Cliente: {$client} [{$paymentMethod}]",
                    'date'      => $returnDate,
                ]);
            }

            return $ticket->fresh(['device.client', 'device.deviceType', 'financialTransactions']);
        });
    }

    /**
     * Registrar la recepción de un dispositivo generando el cliente (si es nuevo o asociando el existente),
     * el dispositivo y el ticket de servicio asociado dentro de una transacción.
     * Si se abona un anticipo, registra la transacción financiera correspondiente.
     *
     * @param array $data Datos validados de la recepción
     * @param int $receptionistId ID del usuario recepcionista en sesión
     * @return Ticket
     */
    public function registerDeviceIntake(array $data, int $receptionistId): Ticket
    {
        return DB::transaction(function () use ($data, $receptionistId) {
            // 1. Resolver Cliente
            if ($data['client_mode'] === 'existing') {
                $client = Client::findOrFail($data['existing_client_id']);
            } else {
                $client = Client::create([
                    'name'     => trim($data['client_name']),
                    'lastname' => trim($data['client_lastname']),
                    'phone'    => trim($data['client_phone']),
                    'dpi'      => !empty($data['client_dpi']) ? trim($data['client_dpi']) : null,
                ]);
            }

            // 2. Registrar Dispositivo
            $device = Device::create([
                'id_client'      => $client->id,
                'id_device_type' => (int) $data['device_type'],
                'brand'          => trim($data['device_brand']),
                'model'          => trim($data['device_model']),
                'serial_number'  => !empty($data['device_serial']) ? trim($data['device_serial']) : null,
            ]);

            // 3. Crear Ticket
            $totalCharged = (float) $data['total_charged'];
            $deposit = isset($data['deposit']) ? (float) $data['deposit'] : 0.0;
            $technicianId = !empty($data['id_user_technician']) ? (int) $data['id_user_technician'] : null;

            $ticket = Ticket::create([
                'id_user_receptionist' => $receptionistId,
                'id_device'            => $device->id,
                'id_user_technician'   => $technicianId,
                'state'                => 'Recibido',
                'reported_issue'       => trim($data['reported_issue']),
                'device_password'      => !empty($data['device_password']) ? trim($data['device_password']) : null,
                'reception_notes'      => !empty($data['reception_notes']) ? trim($data['reception_notes']) : null,
                'total_charged'        => $totalCharged,
                'deposit'              => $deposit,
                'qr_token'             => Str::uuid()->toString(),
                'intake_date'          => now(),
            ]);

            // 4. Si hay anticipo mayor a 0, registrar la transacción financiera
            if ($deposit > 0) {
                FinancialTransaction::create([
                    'id_sale'   => null,
                    'id_ticket' => $ticket->id,
                    'amount'    => $deposit,
                    'type'      => 'INGRESO',
                    'concept'   => "Anticipo por recepción de equipo Ticket #{$ticket->id} [{$device->brand} {$device->model}] - Cliente: {$client->name} {$client->lastname}",
                    'date'      => now(),
                ]);
            }

            return $ticket->fresh(['device.client', 'device.deviceType', 'technician', 'receptionist']);
        });
    }
}
