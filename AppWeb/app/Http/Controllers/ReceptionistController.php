<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Services\ReceptionistService;
use Illuminate\Http\JsonResponse;
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
}
