@extends('layouts.receptionist')

@section('title', 'Historial de Movimientos | CelIx')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/receptionist/inventory.css') }}">
@endpush

@section('content')
    <div class="container-fluid px-0">
        <!-- Encabezado del Módulo y Acciones Principales -->
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3 mb-4">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">Historial de Movimientos de Inventario</h1>                
            </div>

            <!-- Botones de Acción Superior Derecha -->
            <div class="d-flex flex-wrap align-items-center gap-2 w-100 w-lg-auto">
                <a href="{{ route('receptionist.inventory') }}" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-box-seam"></i>
                    <span>Ir al Catálogo de Inventario</span>
                </a>

                <button type="button" class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-2"
                    data-bs-toggle="modal" data-bs-target="#inventoryMovementModal">
                    <i class="bi bi-arrow-left-right"></i>
                    <span>Registrar Movimiento</span>
                </button>
            </div>
        </div>

        <!-- Tarjetas de Métricas Rápidas (KPIs) de Kardex -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon-wrapper rounded-3 d-flex align-items-center justify-content-center icon-info">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div>
                            <span class="text-muted small fw-medium d-block">Movimientos Registrados</span>
                            <h2 class="h4 fw-bold text-dark mb-0">{{ $kpis['total_movements'] ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon-wrapper rounded-3 d-flex align-items-center justify-content-center icon-success">
                            <i class="bi bi-arrow-down-left-circle"></i>
                        </div>
                        <div>
                            <span class="text-muted small fw-medium d-block">Entradas de Mercancía</span>
                            <h2 class="h4 fw-bold text-dark mb-0">{{ $kpis['total_in'] ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon-wrapper rounded-3 d-flex align-items-center justify-content-center icon-primary">
                            <i class="bi bi-arrow-up-right-circle"></i>
                        </div>
                        <div>
                            <span class="text-muted small fw-medium d-block">Salidas y Bajas por Daño</span>
                            <h2 class="h4 fw-bold text-dark mb-0">{{ $kpis['total_out'] ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon-wrapper rounded-3 d-flex align-items-center justify-content-center icon-warning">
                            <i class="bi bi-sliders"></i>
                        </div>
                        <div>
                            <span class="text-muted small fw-medium d-block">Ajustes de Conteo Físico</span>
                            <h2 class="h4 fw-bold text-dark mb-0">{{ $kpis['total_adj'] ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Datatable Card con Búsqueda y Filtros Server-Side -->
        <div class="card border-0 shadow-sm rounded-3 bg-white overflow-hidden mb-4">
            <div class="card-header bg-white border-bottom p-3 p-lg-4">
                <form method="GET" action="{{ route('receptionist.kardex') }}" id="kardexFilterForm">
                    <div class="row g-2 align-items-center">
                        <!-- Campo de Búsqueda -->
                        <div class="col-12 col-md-4 col-xl-4">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" name="search" id="kardexSearchInput"
                                    class="form-control border-start-0 ps-0"
                                    placeholder="Buscar por producto, motivo, código o usuario..."
                                    value="{{ request('search') }}"
                                    aria-label="Buscar en historial">
                            </div>
                        </div>

                        <!-- Filtro por Tipo de Movimiento -->
                        <div class="col-12 col-sm-6 col-md-3 col-xl-3">
                            <select name="movement_type" id="kardexTypeFilter" class="form-select" aria-label="Filtrar por tipo de movimiento">
                                <option value="">Todos los tipos (Entrada, Salida, Ajuste)</option>
                                <option value="ENTRADA" {{ request('movement_type') === 'ENTRADA' ? 'selected' : '' }}>ENTRADA (Compras / Reingresos)</option>
                                <option value="SALIDA" {{ request('movement_type') === 'SALIDA' ? 'selected' : '' }}>SALIDA (Ventas / Bajas por Daño)</option>
                                <option value="AJUSTE" {{ request('movement_type') === 'AJUSTE' ? 'selected' : '' }}>AJUSTE (Conteo Físico / Discrepancias)</option>
                            </select>
                        </div>

                        <!-- Filtro por Rango de Fechas -->
                        <div class="col-6 col-sm-3 col-md-2 col-xl-2">
                            <input type="date" name="date_from" class="form-control" title="Fecha inicial" value="{{ request('date_from') }}">
                        </div>

                        <div class="col-6 col-sm-3 col-md-2 col-xl-2">
                            <input type="date" name="date_to" class="form-control" title="Fecha final" value="{{ request('date_to') }}">
                        </div>

                        <!-- Botones de Filtrar y Limpiar -->
                        <div class="col-12 col-xl-1 d-flex gap-1 justify-content-end">
                            <button type="submit" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center w-100" title="Aplicar filtros">
                                <i class="bi bi-funnel"></i>
                            </button>
                            @if(request()->hasAny(['search', 'movement_type', 'date_from', 'date_to']))
                                <a href="{{ route('receptionist.kardex') }}" class="btn btn-outline-danger d-inline-flex align-items-center justify-content-center" title="Limpiar filtros">
                                    <i class="bi bi-x-circle"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <!-- Tabla de Movimientos -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="movementsTable">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="ps-3 text-nowrap">Folio</th>
                            <th scope="col" class="text-nowrap">Fecha y Hora</th>
                            <th scope="col">Producto Involucrado</th>
                            <th scope="col" class="text-center text-nowrap">Tipo</th>
                            <th scope="col" class="text-center text-nowrap">Cantidad Afectada</th>
                            <th scope="col">Motivo / Justificación</th>
                            <th scope="col" class="text-end pe-3 text-nowrap">Responsable</th>
                        </tr>
                    </thead>
                    <tbody id="movementsTableBody">
                        @forelse($movements as $mov)
                            @php
                                $isPositive = $mov->quantity > 0;
                            @endphp
                            <tr class="movement-item-row">
                                <!-- Folio -->
                                <td class="ps-3 font-monospace small text-muted">
                                    #MOV-{{ str_pad($mov->id, 4, '0', STR_PAD_LEFT) }}
                                </td>

                                <!-- Fecha -->
                                <td class="text-nowrap small text-muted">
                                    {{ $mov->date ? $mov->date->format('d/m/Y H:i') : $mov->created_at->format('d/m/Y H:i') }}
                                </td>

                                <!-- Producto -->
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div>
                                            <span class="fw-semibold text-dark d-block">
                                                {{ $mov->product?->name ?? 'Producto no encontrado' }}
                                            </span>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="font-monospace small text-muted">
                                                    {{ $mov->product_bar_code }}
                                                </span>
                                                @if($mov->product?->category)
                                                    <span class="badge category-badge">{{ $mov->product->category->name }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Badge de Tipo de Movimiento -->
                                <td class="text-center text-nowrap">
                                    @if($mov->movement_type === 'ENTRADA')
                                        <span class="badge badge-movement-in d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-arrow-down-left-circle"></i> ENTRADA
                                        </span>
                                    @elseif($mov->movement_type === 'SALIDA')
                                        <span class="badge badge-movement-out d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-arrow-up-right-circle"></i> SALIDA
                                        </span>
                                    @else
                                        <span class="badge badge-movement-adj d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-sliders"></i> AJUSTE
                                        </span>
                                    @endif
                                </td>

                                <!-- Cantidad Afectada (+/-) -->
                                <td class="text-center text-nowrap fw-bold {{ $isPositive ? 'text-success' : 'text-danger' }}">
                                    {{ $isPositive ? '+' . $mov->quantity : $mov->quantity }} unid.
                                </td>

                                <!-- Motivo / Justificación -->
                                <td class="small text-muted">
                                    {{ $mov->reason ?? 'Movimiento estándar de inventario' }}
                                </td>

                                <!-- Responsable -->
                                <td class="text-end pe-3 small text-dark text-nowrap">
                                    <i class="bi bi-person me-1 text-muted"></i>
                                    {{ $mov->user?->name ?? 'Sistema' }} {{ $mov->user?->lastname ?? '' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-clock-history fs-1 d-block mb-2 text-secondary"></i>
                                    <span>No se han registrado movimientos de inventario con los criterios seleccionados.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación de Movimientos -->
            @if($movements->hasPages())
                <div class="card-footer bg-white border-top p-3 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
                    <div class="small text-muted">
                        Mostrando <strong>{{ $movements->firstItem() }}</strong> a <strong>{{ $movements->lastItem() }}</strong> de <strong>{{ $movements->total() }}</strong> movimientos
                    </div>
                    <div>
                        {{ $movements->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- MODAL: REGISTRAR MOVIMIENTO DE INVENTARIO -->
    <div class="modal fade" id="inventoryMovementModal" tabindex="-1" aria-labelledby="inventoryMovementModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="modal-title-icon rounded-circle d-flex align-items-center justify-content-center icon-primary">
                            <i class="bi bi-arrow-left-right"></i>
                        </span>
                        <div>
                            <h2 class="h5 modal-title fw-bold text-dark mb-0" id="inventoryMovementModalLabel">Registrar Movimiento de Inventario</h2>
                            <span class="small text-muted">Bajas por daño, compras a proveedores y regularización de conteo físico</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <form method="POST" action="{{ route('receptionist.inventory.movements.store') }}" id="inventoryMovementForm">
                    @csrf
                    <div class="modal-body p-3 p-lg-4">
                        <div class="row g-3">
                            <!-- Selección de Producto -->
                            <div class="col-12">
                                <label for="movement_product" class="form-label fw-medium">Producto <span class="text-danger">*</span></label>
                                <select class="form-select" id="movement_product" name="product_bar_code" required>
                                    <option value="" selected disabled>Seleccionar producto del catálogo...</option>
                                    @foreach($allProducts as $prod)
                                        <option value="{{ $prod->bar_code }}" data-name="{{ $prod->name }}" data-stock="{{ $prod->stock }}">
                                            {{ $prod->bar_code }} - {{ $prod->name }} (Stock actual: {{ $prod->stock }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Tipo de Movimiento -->
                            <div class="col-12 col-md-6">
                                <label for="movement_type" class="form-label fw-medium">Tipo de Movimiento <span class="text-danger">*</span></label>
                                <select class="form-select" id="movement_type" name="movement_type" required>
                                    <option value="" selected disabled>Seleccionar tipo...</option>
                                    <option value="SALIDA">SALIDA (Baja por daño, producto roto, descontinuado)</option>
                                    <option value="ENTRADA">ENTRADA (Compra a proveedor, reingreso)</option>
                                    <option value="AJUSTE">AJUSTE (Conteo físico, descuadre de inventario)</option>
                                </select>
                            </div>

                            <!-- Motivo / Concepto Específico -->
                            <div class="col-12 col-md-6">
                                <label for="movement_reason_select" class="form-label fw-medium">Motivo Frecuente <span class="text-danger">*</span></label>
                                <select class="form-select" id="movement_reason_select" name="reason" required>
                                    <option value="" selected disabled>Selecciona un motivo...</option>
                                    <optgroup label="Motivos de Salida">
                                        <option value="Producto dañado o en mal estado">Producto dañado o en mal estado</option>
                                        <option value="Empaque roto / Producto no apto para venta">Empaque roto / No apto para venta</option>
                                        <option value="Descontinuado / Dejar de vender artículo">Descontinuado / Dejar de vender artículo</option>
                                        <option value="Merma técnica en reparación">Merma técnica en reparación</option>
                                        <option value="Uso interno del taller">Uso interno del taller</option>
                                    </optgroup>
                                    <optgroup label="Motivos de Entrada">
                                        <option value="Compra a proveedor mayorista">Compra a proveedor mayorista</option>
                                        <option value="Reabastecimiento de mostrador">Reabastecimiento de mostrador</option>
                                        <option value="Devolución de cliente aprobada">Devolución de cliente aprobada</option>
                                    </optgroup>
                                    <optgroup label="Motivos de Ajuste">
                                        <option value="Discrepancia en conteo físico de inventario">Discrepancia en conteo físico</option>
                                        <option value="Regularización por auditoría de almacén">Regularización por auditoría</option>
                                    </optgroup>
                                </select>
                            </div>

                            <!-- Cantidad -->
                            <div class="col-12 col-md-6">
                                <label for="movement_quantity" class="form-label fw-medium">Cantidad a Afectar <span class="text-danger">*</span></label>
                                <input type="number" min="1" class="form-control" id="movement_quantity" name="quantity" value="1" required>
                                <span class="small text-muted" id="movementQtyHelper">Ingresa la cantidad en unidades</span>
                            </div>

                            <!-- Tarjeta de Cálculo de Stock Proyectado -->
                            <div class="col-12 col-md-6">
                                <div class="p-3 bg-light rounded-3 border h-100 d-flex flex-column justify-content-center">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="small text-muted">Stock actual:</span>
                                        <strong id="displayCurrentStock" class="text-dark">0 unidades</strong>
                                    </div>
                                    <div class="d-flex justify-content-between border-top pt-1 mt-1">
                                        <span class="small fw-semibold text-dark">Nuevo stock estimado:</span>
                                        <strong id="displayNewStock" class="text-primary fs-6">0 unidades</strong>
                                    </div>
                                </div>
                            </div>

                            <!-- Observaciones Adicionales -->
                            <div class="col-12">
                                <label for="movement_notes" class="form-label fw-medium">Observaciones / Justificación Detallada</label>
                                <textarea class="form-control" id="movement_notes" name="notes" rows="2"
                                    placeholder="Detalles de la baja (ej. caja golpeada, no enciende pantalla, etc.)"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light border-top py-3">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2" id="btnSubmitMovement">
                            <i class="bi bi-check2-circle"></i>
                            <span>Confirmar Movimiento</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/receptionist/inventory.js') }}"></script>
@endpush
