<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\StoreInventoryMovementRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\StoreSaleRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Services\ReceptionistService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReceptionistController extends Controller
{
    public function __construct(
        protected ReceptionistService $receptionistService
    ) {}

    /**
     * Vista principal del módulo de Recepción (catálogo paginado, filtros en backend, KPIs y catálogo para ventas).
     */
    public function home(Request $request): View
    {
        $filters = $request->only(['search', 'category', 'stock_status', 'sort_by', 'sort_direction']);
        $perPage = (int) $request->input('per_page', 10);

        $products        = $this->receptionistService->getProductsPaginated($filters, $perPage);
        $productsForSale = $this->receptionistService->getAllProductsForSale();
        $kpis            = $this->receptionistService->getCatalogKPIs();
        $categories      = $this->receptionistService->getCategories();

        return view('receptionist.home', compact('products', 'productsForSale', 'kpis', 'categories'));
    }

    /**
     * Endpoint asíncrono para registrar una venta y generar movimientos de Kardex.
     */
    public function storeSale(StoreSaleRequest $request): JsonResponse
    {
        $sale = $this->receptionistService->registerSale(
            $request->validated(),
            auth()->id()
        );

        return response()->json([
            'success' => true,
            'message' => "¡Venta #{$sale->id} registrada exitosamente con comprobante emitido!",
            'sale'    => [
                'id'         => $sale->id,
                'total_sale' => (float) $sale->total_sale,
                'sale_date'  => $sale->sale_date ? $sale->sale_date->format('d/m/Y H:i') : now()->format('d/m/Y H:i'),
            ],
        ], 201);
    }

    /**
     * Vista de Gestión de Inventario (Productos y Categorías con paginación server-side y métricas).
     */
    public function inventory(Request $request): View
    {
        $productFilters = $request->only(['search', 'category', 'stock_status', 'status', 'sort_by', 'sort_direction']);
        $categoryFilters = $request->only(['category_search', 'category_status']);
        $perPage = (int) $request->input('per_page', 10);

        $products      = $this->receptionistService->getProductsPaginated($productFilters, $perPage);
        $categories    = $this->receptionistService->getInventoryCategories($categoryFilters);
        $allCategories = $this->receptionistService->getCategories();
        $allProducts   = $this->receptionistService->getAllProductsForSale();
        $kpis          = $this->receptionistService->getInventoryKPIs();

        return view('receptionist.inventory.index', compact(
            'products',
            'categories',
            'allCategories',
            'allProducts',
            'kpis'
        ));
    }

    /**
     * Registrar un nuevo producto en inventario.
     */
    public function storeProduct(StoreProductRequest $request): JsonResponse|RedirectResponse
    {
        $product = $this->receptionistService->storeProduct($request->validated(), auth()->id());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "¡Producto '{$product->name}' registrado exitosamente en el inventario!",
                'product' => $product,
            ], 201);
        }

        return redirect()->route('receptionist.inventory')
            ->with('success', "¡Producto '{$product->name}' registrado exitosamente en el inventario!");
    }

    /**
     * Actualizar datos de un producto existente.
     */
    public function updateProduct(UpdateProductRequest $request, string $barcode): JsonResponse|RedirectResponse
    {
        $product = $this->receptionistService->updateProduct($barcode, $request->validated());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "¡Producto '{$product->name}' actualizado correctamente!",
                'product' => $product,
            ]);
        }

        return redirect()->route('receptionist.inventory')
            ->with('success', "¡Producto '{$product->name}' actualizado correctamente!");
    }

    /**
     * Alternar estado activo / inactivo de un producto (borrado lógico).
     */
    public function toggleProductStatus(string $barcode, Request $request): JsonResponse|RedirectResponse
    {
        $product = $this->receptionistService->toggleProductStatus($barcode);
        $stateText = $product->status ? 'activado' : 'desactivado';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "¡Producto '{$product->name}' {$stateText} exitosamente!",
                'product' => $product,
            ]);
        }

        return redirect()->route('receptionist.inventory')
            ->with('success', "¡Producto '{$product->name}' {$stateText} exitosamente!");
    }

    /**
     * Registrar una nueva categoría de productos.
     */
    public function storeCategory(StoreCategoryRequest $request): JsonResponse|RedirectResponse
    {
        $category = $this->receptionistService->storeCategory($request->validated());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'  => true,
                'message'  => "¡Categoría '{$category->name}' registrada exitosamente!",
                'category' => $category,
            ], 201);
        }

        return redirect()->route('receptionist.inventory')
            ->with('success', "¡Categoría '{$category->name}' registrada exitosamente!");
    }

    /**
     * Actualizar una categoría existente.
     */
    public function updateCategory(UpdateCategoryRequest $request, int $id): JsonResponse|RedirectResponse
    {
        $category = $this->receptionistService->updateCategory($id, $request->validated());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'  => true,
                'message'  => "¡Categoría '{$category->name}' actualizada correctamente!",
                'category' => $category,
            ]);
        }

        return redirect()->route('receptionist.inventory')
            ->with('success', "¡Categoría '{$category->name}' actualizada correctamente!");
    }

    /**
     * Alternar estado activo / inactivo de una categoría (borrado lógico).
     */
    public function toggleCategoryStatus(int $id, Request $request): JsonResponse|RedirectResponse
    {
        $category = $this->receptionistService->toggleCategoryStatus($id);
        $stateText = $category->status ? 'activada' : 'desactivada';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'  => true,
                'message'  => "¡Categoría '{$category->name}' {$stateText} exitosamente!",
                'category' => $category,
            ]);
        }

        return redirect()->route('receptionist.inventory')
            ->with('success', "¡Categoría '{$category->name}' {$stateText} exitosamente!");
    }

    /**
     * Registrar movimiento de inventario (Entrada, Salida por baja/merma o Ajuste físico).
     */
    public function storeInventoryMovement(StoreInventoryMovementRequest $request): JsonResponse|RedirectResponse
    {
        $movement = $this->receptionistService->storeInventoryMovement($request->validated(), auth()->id());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'  => true,
                'message'  => "¡Movimiento de inventario #{$movement->id} registrado correctamente en Kardex!",
                'movement' => $movement,
            ], 201);
        }

        return redirect()->back()
            ->with('success', "¡Movimiento de inventario #{$movement->id} registrado correctamente en Kardex!");
    }

    /**
     * Vista dedicada de Historial de Movimientos de Inventario (Kardex).
     */
    public function kardex(Request $request): View
    {
        $filters = $request->only(['search', 'movement_type', 'date_from', 'date_to']);
        $perPage = (int) $request->input('per_page', 10);

        $movements   = $this->receptionistService->getKardexMovements($filters, $perPage);
        $kpis        = $this->receptionistService->getKardexKPIs();
        $allProducts = $this->receptionistService->getAllProductsForSale();

        return view('receptionist.inventory.kardex', compact('movements', 'kpis', 'allProducts'));
    }
}
