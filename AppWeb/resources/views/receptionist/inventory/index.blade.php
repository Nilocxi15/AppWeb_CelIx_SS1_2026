@extends('layouts.receptionist')

@section('title', 'Gestión de Inventario | CelIx')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/receptionist/inventory.css') }}">
@endpush

@section('content')
    <div class="container-fluid px-0">
        <!-- Encabezado del Módulo y Acciones Principales -->
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3 mb-4">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">Gestión de Inventario y Almacén</h1>                
            </div>

            <!-- Botones de Acción Superior Derecha -->
            <div class="d-flex flex-wrap align-items-center gap-2 w-100 w-lg-auto">

                <button type="button"
                    class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-2 flex-grow-1 flex-lg-grow-0"
                    data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                    <i class="bi bi-folder-plus text-primary"></i>
                    <span>Nueva Categoría</span>
                </button>

                <button type="button"
                    class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-2 flex-grow-1 flex-lg-grow-0"
                    data-bs-toggle="modal" data-bs-target="#createProductModal">
                    <i class="bi bi-plus-circle"></i>
                    <span>Nuevo Producto</span>
                </button>
            </div>
        </div>

        <!-- Tarjetas de Métricas Rápidas (KPIs) -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon-wrapper rounded-3 d-flex align-items-center justify-content-center icon-primary">
                            <i class="bi bi-boxes"></i>
                        </div>
                        <div>
                            <span class="text-muted small fw-medium d-block">Productos Registrados</span>
                            <h2 class="h4 fw-bold text-dark mb-0">{{ $kpis['total_products'] ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon-wrapper rounded-3 d-flex align-items-center justify-content-center icon-success">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div>
                            <span class="text-muted small fw-medium d-block">Unidades en Stock Total</span>
                            <h2 class="h4 fw-bold text-dark mb-0">{{ $kpis['total_stock'] ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon-wrapper rounded-3 d-flex align-items-center justify-content-center icon-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                        <div>
                            <span class="text-muted small fw-medium d-block">Stock Bajo / Crítico</span>
                            <h2 class="h4 fw-bold text-dark mb-0">{{ $kpis['low_stock_count'] ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon-wrapper rounded-3 d-flex align-items-center justify-content-center icon-info">
                            <i class="bi bi-tags"></i>
                        </div>
                        <div>
                            <span class="text-muted small fw-medium d-block">Categorías Activas</span>
                            <h2 class="h4 fw-bold text-dark mb-0">{{ $kpis['active_categories'] ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navegación por Pestañas (Tabs) CelIx -->
        <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
            <div class="card-header bg-white border-bottom px-3 px-lg-4 pt-3 pb-0">
                <ul class="nav nav-tabs border-bottom-0 inventory-tabs" id="inventoryTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-medium" id="products-tab" data-bs-toggle="tab"
                            data-bs-target="#tab-products" type="button" role="tab" aria-controls="tab-products"
                            aria-selected="true">
                            <i class="bi bi-box-seam me-2"></i>Gestión de Productos
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-medium" id="categories-tab" data-bs-toggle="tab"
                            data-bs-target="#tab-categories" type="button" role="tab" aria-controls="tab-categories"
                            aria-selected="false">
                            <i class="bi bi-tags me-2"></i>Gestión de Categorías
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-0">
                <div class="tab-content" id="inventoryTabsContent">
                    <!-- TAB 1: GESTIÓN DE PRODUCTOS -->
                    <div class="tab-pane fade show active p-3 p-lg-4" id="tab-products" role="tabpanel"
                        aria-labelledby="products-tab">
                        <!-- Barra de Búsqueda y Filtros de Productos (Server-Side) -->
                        <form method="GET" action="{{ route('receptionist.inventory') }}" id="productFilterForm" class="mb-3">
                            <div class="row g-2 align-items-center">
                                <div class="col-12 col-md-5 col-xl-4">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0 text-muted">
                                            <i class="bi bi-search"></i>
                                        </span>
                                        <input type="text" name="search" id="productSearchInput" class="form-control border-start-0 ps-0"
                                            placeholder="Buscar por código, nombre o descripción..."
                                            value="{{ request('search') }}" aria-label="Buscar producto">
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6 col-md-3 col-xl-3">
                                    <select name="category" id="productCategoryFilter" class="form-select" aria-label="Filtrar por categoría">
                                        <option value="">Todas las categorías</option>
                                        @foreach($allCategories as $cat)
                                            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                                                {{ $cat->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-6 col-sm-3 col-md-2 col-xl-2">
                                    <select name="stock_status" id="productStockFilter" class="form-select" aria-label="Filtrar por stock">
                                        <option value="">Todos los stocks</option>
                                        <option value="disponible" {{ request('stock_status') === 'disponible' ? 'selected' : '' }}>En Stock</option>
                                        <option value="bajo" {{ request('stock_status') === 'bajo' ? 'selected' : '' }}>Stock Bajo</option>
                                        <option value="agotado" {{ request('stock_status') === 'agotado' ? 'selected' : '' }}>Agotado</option>
                                    </select>
                                </div>

                                <div class="col-6 col-sm-3 col-md-2 col-xl-2">
                                    <select name="status" id="productStatusFilter" class="form-select" aria-label="Filtrar por estado activo o inactivo">
                                        <option value="">Todos los estados</option>
                                        <option value="activo" {{ request('status') === 'activo' ? 'selected' : '' }}>Solo Activos</option>
                                        <option value="inactivo" {{ request('status') === 'inactivo' ? 'selected' : '' }}>Solo Inactivos</option>
                                    </select>
                                </div>

                                <div class="col-12 col-xl-1 d-flex gap-1 justify-content-end">
                                    <select name="per_page" id="productPerPageSelect" class="form-select form-select-sm w-auto" aria-label="Registros por página">
                                        <option value="5" {{ request('per_page', '10') == '5' ? 'selected' : '' }}>5</option>
                                        <option value="10" {{ request('per_page', '10') == '10' ? 'selected' : '' }}>10</option>
                                        <option value="25" {{ request('per_page', '10') == '25' ? 'selected' : '' }}>25</option>
                                        <option value="50" {{ request('per_page', '10') == '50' ? 'selected' : '' }}>50</option>
                                    </select>
                                    <button type="submit" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center justify-content-center px-2" title="Filtrar">
                                        <i class="bi bi-funnel"></i>
                                    </button>
                                    @if(request()->hasAny(['search', 'category', 'stock_status', 'status']))
                                        <a href="{{ route('receptionist.inventory') }}" class="btn btn-outline-danger btn-sm d-inline-flex align-items-center justify-content-center px-2" title="Limpiar filtros">
                                            <i class="bi bi-x-circle"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </form>

                        <!-- Tabla de Productos -->
                        <div class="table-responsive border rounded-3 mb-3">
                            <table class="table table-hover align-middle mb-0" id="productsInventoryTable">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" class="ps-3 text-nowrap">Código</th>
                                        <th scope="col">Producto</th>
                                        <th scope="col" class="text-nowrap">Categoría</th>
                                        <th scope="col" class="text-center text-nowrap">Stock / Mínimo</th>
                                        <th scope="col" class="text-end text-nowrap">Precio Venta</th>
                                        <th scope="col" class="text-center text-nowrap">Estado</th>
                                        <th scope="col" class="text-end pe-3 text-nowrap">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="productsTableBody">
                                    @forelse($products as $prod)
                                        @php
                                            $isOutOfStock = $prod->stock <= 0;
                                            $isLowStock = !$isOutOfStock && $prod->stock <= $prod->minium_stock;
                                            $isActive = (bool) $prod->status;
                                        @endphp
                                        <tr class="product-item-row">
                                            <!-- Código -->
                                            <td class="ps-3 text-nowrap">
                                                <span class="font-monospace small fw-semibold text-secondary barcode-badge">
                                                    <i class="bi bi-upc-scan me-1"></i>{{ $prod->bar_code }}
                                                </span>
                                            </td>

                                            <!-- Nombre y Miniatura -->
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="product-thumb-wrapper rounded bg-light border d-flex align-items-center justify-content-center flex-shrink-0">
                                                        <i class="bi bi-box-seam text-muted"></i>
                                                    </div>
                                                    <div>
                                                        <span class="fw-semibold text-dark d-block product-name-display">{{ $prod->name }}</span>
                                                        <span class="text-muted small text-truncate d-block max-w-desc">{{ $prod->description ?? 'Sin descripción.' }}</span>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Categoría -->
                                            <td class="text-nowrap">
                                                <span class="badge category-badge">{{ $prod->category?->name ?? 'Sin Categoría' }}</span>
                                            </td>

                                            <!-- Stock / Mínimo -->
                                            <td class="text-center text-nowrap">
                                                @if($isOutOfStock)
                                                    <span class="badge badge-stock-out d-inline-flex align-items-center gap-1">
                                                        <i class="bi bi-x-circle"></i> 0 unid. (Agotado)
                                                    </span>
                                                @elseif($isLowStock)
                                                    <span class="badge badge-stock-low d-inline-flex align-items-center gap-1">
                                                        <i class="bi bi-exclamation-triangle"></i> {{ $prod->stock }} / {{ $prod->minium_stock }} unid.
                                                    </span>
                                                @else
                                                    <span class="badge badge-stock-ok d-inline-flex align-items-center gap-1">
                                                        <i class="bi bi-check-circle"></i> {{ $prod->stock }} unid.
                                                    </span>
                                                @endif
                                            </td>

                                            <!-- Precio Venta -->
                                            <td class="text-end text-nowrap fw-semibold text-dark">
                                                Q {{ number_format($prod->price, 2) }}
                                            </td>

                                            <!-- Estado (Borrado Lógico) -->
                                            <td class="text-center text-nowrap">
                                                @if($isActive)
                                                    <span class="badge badge-active d-inline-flex align-items-center gap-1">
                                                        <i class="bi bi-check-circle"></i> Activo
                                                    </span>
                                                @else
                                                    <span class="badge badge-inactive d-inline-flex align-items-center gap-1">
                                                        <i class="bi bi-dash-circle"></i> Inactivo
                                                    </span>
                                                @endif
                                            </td>

                                            <!-- Acciones -->
                                            <td class="text-end pe-3 text-nowrap">
                                                <div class="d-inline-flex align-items-center gap-1">
                                                    <!-- Ver Detalle -->
                                                    <button type="button" class="btn-action btn-action-view"
                                                        data-bs-toggle="modal" data-bs-target="#viewProductModal"
                                                        data-barcode="{{ $prod->bar_code }}"
                                                        data-name="{{ $prod->name }}"
                                                        data-category="{{ $prod->category?->name ?? 'Sin Categoría' }}"
                                                        data-price="{{ $prod->price }}"
                                                        data-stock="{{ $prod->stock }}"
                                                        data-minstock="{{ $prod->minium_stock }}"
                                                        data-description="{{ $prod->description ?? 'Sin descripción registrada.' }}"
                                                        data-status="{{ $isActive ? 'activo' : 'inactivo' }}"
                                                        title="Ver detalles del producto"
                                                        aria-label="Ver detalles">
                                                        <i class="bi bi-eye"></i>
                                                    </button>

                                                    <!-- Editar Producto -->
                                                    <button type="button" class="btn-action btn-action-edit"
                                                        data-bs-toggle="modal" data-bs-target="#editProductModal"
                                                        data-barcode="{{ $prod->bar_code }}"
                                                        data-name="{{ $prod->name }}"
                                                        data-category-id="{{ $prod->id_category }}"
                                                        data-price="{{ $prod->price }}"
                                                        data-minstock="{{ $prod->minium_stock }}"
                                                        data-description="{{ $prod->description ?? '' }}"
                                                        data-status="{{ $isActive ? '1' : '0' }}"
                                                        title="Editar datos del producto"
                                                        aria-label="Editar">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>

                                                    <!-- Movimiento Rápido de Inventario -->
                                                    <button type="button" class="btn-action btn-action-move"
                                                        data-bs-toggle="modal" data-bs-target="#inventoryMovementModal"
                                                        data-barcode="{{ $prod->bar_code }}"
                                                        data-stock="{{ $prod->stock }}"
                                                        title="Registrar movimiento o ajuste para este producto"
                                                        aria-label="Registrar movimiento">
                                                        <i class="bi bi-arrow-left-right"></i>
                                                    </button>

                                                    <!-- Alternar Estado Activo / Inactivo (Borrado Lógico) -->
                                                    <button type="button" class="btn-action btn-action-toggle"
                                                        data-bs-toggle="modal" data-bs-target="#toggleProductStateModal"
                                                        data-barcode="{{ $prod->bar_code }}"
                                                        data-name="{{ $prod->name }}"
                                                        data-status="{{ $isActive ? 'activo' : 'inactivo' }}"
                                                        title="{{ $isActive ? 'Desactivar producto (baja lógica)' : 'Activar producto' }}"
                                                        aria-label="Alternar estado">
                                                        @if($isActive)
                                                            <i class="bi bi-toggle-on text-success fs-5"></i>
                                                        @else
                                                            <i class="bi bi-toggle-off text-muted fs-5"></i>
                                                        @endif
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-5 text-muted">
                                                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                                <span>No se encontraron productos en el inventario con los filtros aplicados.</span>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación de Productos (Server-Side) -->
                        @if($products->hasPages())
                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3">
                                <div class="small text-muted">
                                    Mostrando <strong>{{ $products->firstItem() }}</strong> a <strong>{{ $products->lastItem() }}</strong> de <strong>{{ $products->total() }}</strong> productos
                                </div>
                                <div>
                                    {{ $products->links('pagination::bootstrap-5') }}
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- TAB 2: GESTIÓN DE CATEGORÍAS DE PRODUCTOS -->
                    <div class="tab-pane fade p-3 p-lg-4" id="tab-categories" role="tabpanel"
                        aria-labelledby="categories-tab">
                        <!-- Barra de Búsqueda y Filtro de Categorías -->
                        <form method="GET" action="{{ route('receptionist.inventory') }}" id="categoryFilterForm" class="mb-3">
                            <input type="hidden" name="active_tab" value="categories">
                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-stretch align-items-sm-center gap-2">
                                <div class="d-flex align-items-center gap-2 flex-grow-1 max-w-search">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0 text-muted">
                                            <i class="bi bi-search"></i>
                                        </span>
                                        <input type="text" name="category_search" id="categorySearchInput" class="form-control border-start-0 ps-0"
                                            placeholder="Buscar categoría por nombre o descripción..."
                                            value="{{ request('category_search') }}" aria-label="Buscar categoría">
                                    </div>
                                </div>

                                <div class="d-flex align-items-center gap-2">
                                    <select name="category_status" id="categoryStatusFilter" class="form-select form-select-sm w-auto" aria-label="Filtrar por estado">
                                        <option value="">Todos los estados</option>
                                        <option value="activa" {{ request('category_status') === 'activa' ? 'selected' : '' }}>Solo Activas</option>
                                        <option value="inactiva" {{ request('category_status') === 'inactiva' ? 'selected' : '' }}>Solo Inactivas</option>
                                    </select>

                                    <button type="submit" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-1" title="Filtrar categorías">
                                        <i class="bi bi-funnel"></i>
                                    </button>

                                    <button type="button" class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1 text-nowrap"
                                        data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                                        <i class="bi bi-plus-lg"></i>
                                        <span>Nueva Categoría</span>
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Tabla de Categorías -->
                        <div class="table-responsive border rounded-3 mb-3">
                            <table class="table table-hover align-middle mb-0" id="categoriesTable">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" class="ps-3 text-nowrap">ID</th>
                                        <th scope="col" class="text-nowrap">Nombre de Categoría</th>
                                        <th scope="col">Descripción</th>
                                        <th scope="col" class="text-center text-nowrap">Productos Vinculados</th>
                                        <th scope="col" class="text-center text-nowrap">Fecha Registro</th>
                                        <th scope="col" class="text-center text-nowrap">Estado</th>
                                        <th scope="col" class="text-end pe-3 text-nowrap">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="categoriesTableBody">
                                    @forelse($categories as $cat)
                                        @php
                                            $catActive = (bool) $cat->status;
                                        @endphp
                                        <tr class="category-item-row">
                                            <td class="ps-3 font-monospace small text-muted">#{{ $cat->id }}</td>
                                            <td>
                                                <span class="fw-semibold text-dark category-name-display">{{ $cat->name }}</span>
                                            </td>
                                            <td class="text-muted small">
                                                {{ $cat->description ?? 'Sin descripción asignada.' }}
                                            </td>
                                            <td class="text-center text-nowrap">
                                                <span class="badge bg-light text-dark border">{{ $cat->products_count }} productos</span>
                                            </td>
                                            <td class="text-center text-muted small text-nowrap">
                                                {{ $cat->created_at ? $cat->created_at->format('d/m/Y') : '-' }}
                                            </td>
                                            <!-- Estado con Borrado Lógico -->
                                            <td class="text-center text-nowrap">
                                                @if($catActive)
                                                    <span class="badge badge-active d-inline-flex align-items-center gap-1">
                                                        <i class="bi bi-check-circle"></i> Activa
                                                    </span>
                                                @else
                                                    <span class="badge badge-inactive d-inline-flex align-items-center gap-1">
                                                        <i class="bi bi-dash-circle"></i> Inactiva
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-end pe-3 text-nowrap">
                                                <div class="d-inline-flex align-items-center gap-1">
                                                    <!-- Editar Categoría -->
                                                    <button type="button" class="btn-action btn-action-edit"
                                                        data-bs-toggle="modal" data-bs-target="#editCategoryModal"
                                                        data-id="{{ $cat->id }}"
                                                        data-name="{{ $cat->name }}"
                                                        data-description="{{ $cat->description ?? '' }}"
                                                        data-status="{{ $catActive ? '1' : '0' }}"
                                                        title="Editar categoría"
                                                        aria-label="Editar">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>

                                                    <!-- Alternar Estado Activa / Inactiva (Borrado Lógico) -->
                                                    <button type="button" class="btn-action btn-action-toggle"
                                                        data-bs-toggle="modal" data-bs-target="#toggleCategoryStateModal"
                                                        data-id="{{ $cat->id }}"
                                                        data-name="{{ $cat->name }}"
                                                        data-status="{{ $catActive ? 'activa' : 'inactiva' }}"
                                                        title="{{ $catActive ? 'Desactivar categoría (baja lógica)' : 'Activar categoría' }}"
                                                        aria-label="Alternar estado">
                                                        @if($catActive)
                                                            <i class="bi bi-toggle-on text-success fs-5"></i>
                                                        @else
                                                            <i class="bi bi-toggle-off text-muted fs-5"></i>
                                                        @endif
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-5 text-muted">
                                                <i class="bi bi-tags fs-1 d-block mb-2 text-secondary"></i>
                                                <span>No se encontraron categorías registradas.</span>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: NUEVO PRODUCTO -->
    <div class="modal fade" id="createProductModal" tabindex="-1" aria-labelledby="createProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="modal-title-icon rounded-circle d-flex align-items-center justify-content-center icon-primary">
                            <i class="bi bi-plus-circle"></i>
                        </span>
                        <div>
                            <h2 class="h5 modal-title fw-bold text-dark mb-0" id="createProductModalLabel">Registrar Nuevo Producto</h2>
                            <span class="small text-muted">Añadir un artículo al catálogo para venta e inventario</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <form method="POST" action="{{ route('receptionist.inventory.products.store') }}" id="createProductForm">
                    @csrf
                    <div class="modal-body p-3 p-lg-4">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="create_bar_code" class="form-label fw-medium">Código de Barras / SKU <span class="text-danger">*</span></label>
                                <input type="text" class="form-control font-monospace" id="create_bar_code" name="bar_code"
                                    placeholder="Ej. 7401002001" required value="{{ old('bar_code') }}">
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="create_category" class="form-label fw-medium">Categoría <span class="text-danger">*</span></label>
                                <select class="form-select" id="create_category" name="id_category" required>
                                    <option value="" selected disabled>Selecciona una categoría...</option>
                                    @foreach($allCategories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('id_category') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label for="create_name" class="form-label fw-medium">Nombre del Producto / Accesorio <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="create_name" name="name"
                                    placeholder="Ej. Cargador Rápido 30W Tipo-C" required value="{{ old('name') }}">
                            </div>

                            <div class="col-12">
                                <label for="create_description" class="form-label fw-medium">Descripción y Compatibilidad</label>
                                <textarea class="form-control" id="create_description" name="description" rows="2"
                                    placeholder="Especificaciones técnicas, modelos compatibles, etc.">{{ old('description') }}</textarea>
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="create_price" class="form-label fw-medium">Precio de Venta (Q) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white">Q</span>
                                    <input type="number" step="0.01" min="0.01" class="form-control" id="create_price"
                                        name="price" placeholder="0.00" required value="{{ old('price') }}">
                                </div>
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="create_stock" class="form-label fw-medium">Stock Inicial <span class="text-danger">*</span></label>
                                <input type="number" min="0" class="form-control" id="create_stock" name="stock"
                                    value="{{ old('stock', 0) }}" required>
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="create_minium_stock" class="form-label fw-medium">Stock Mínimo Alerta <span class="text-danger">*</span></label>
                                <input type="number" min="1" class="form-control" id="create_minium_stock" name="minium_stock"
                                    value="{{ old('minium_stock', 5) }}" required>
                            </div>

                            <div class="col-12">
                                <div class="form-check form-switch pt-1">
                                    <input class="form-check-input" type="checkbox" role="switch" id="create_status" name="status" checked>
                                    <label class="form-check-label fw-medium text-dark small" for="create_status">
                                        Producto Activo (habilitado inmediatamente para venta)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light border-top py-3">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2">
                            <i class="bi bi-check-circle"></i>
                            <span>Guardar Producto</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: EDITAR PRODUCTO -->
    <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="modal-title-icon rounded-circle d-flex align-items-center justify-content-center icon-primary">
                            <i class="bi bi-pencil"></i>
                        </span>
                        <div>
                            <h2 class="h5 modal-title fw-bold text-dark mb-0" id="editProductModalLabel">Editar Producto</h2>
                            <span class="small text-muted font-monospace" id="editBarcodeSubtitle">Código: ---</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <form method="POST" id="editProductForm" action="">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-3 p-lg-4">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-medium text-muted">Código de Barras</label>
                                <input type="text" class="form-control font-monospace bg-light" id="edit_bar_code" disabled>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="edit_category" class="form-label fw-medium">Categoría <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_category" name="id_category" required>
                                    @foreach($allCategories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label for="edit_name" class="form-label fw-medium">Nombre del Producto <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_name" name="name" required>
                            </div>

                            <div class="col-12">
                                <label for="edit_description" class="form-label fw-medium">Descripción</label>
                                <textarea class="form-control" id="edit_description" name="description" rows="2"></textarea>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="edit_price" class="form-label fw-medium">Precio de Venta (Q) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white">Q</span>
                                    <input type="number" step="0.01" min="0.01" class="form-control" id="edit_price" name="price" required>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="edit_minium_stock" class="form-label fw-medium">Stock Mínimo de Alerta <span class="text-danger">*</span></label>
                                <input type="number" min="1" class="form-control" id="edit_minium_stock" name="minium_stock" required>
                            </div>

                            <div class="col-12">
                                <div class="form-check form-switch pt-1">
                                    <input class="form-check-input" type="checkbox" role="switch" id="edit_status" name="status" value="1">
                                    <label class="form-check-label fw-medium text-dark small" for="edit_status">
                                        Producto Activo (habilitado para venta)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light border-top py-3">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2">
                            <i class="bi bi-check-circle"></i>
                            <span>Actualizar Producto</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: VER DETALLE DEL PRODUCTO -->
    <div class="modal fade" id="viewProductModal" tabindex="-1" aria-labelledby="viewProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="modal-title-icon rounded-circle d-flex align-items-center justify-content-center icon-primary">
                            <i class="bi bi-eye"></i>
                        </span>
                        <h2 class="h5 modal-title fw-bold text-dark mb-0" id="viewProductModalLabel">Ficha del Producto</h2>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body p-3 p-lg-4">
                    <div class="row g-4">
                        <div class="col-12 col-md-4 text-center">
                            <div class="p-4 bg-light rounded-3 border d-flex align-items-center justify-content-center min-h-preview">
                                <i class="bi bi-box-seam fs-1 text-secondary"></i>
                            </div>
                            <div class="mt-3">
                                <span class="badge bg-light text-secondary border font-monospace px-3 py-2 fs-6" id="detailProductBarcode">
                                    0000000000
                                </span>
                            </div>
                        </div>

                        <div class="col-12 col-md-8">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge category-badge" id="detailProductCategory">Categoría</span>
                                <span class="badge" id="detailProductStatusBadge">Estado</span>
                            </div>

                            <h3 class="h4 fw-bold text-dark mb-2" id="detailProductName">Nombre del Producto</h3>
                            <p class="text-muted small mb-3" id="detailProductDescription">Descripción.</p>

                            <div class="p-3 bg-light rounded-3 border mb-3">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <span class="text-muted small d-block">Precio de Venta</span>
                                        <span class="h4 fw-bold text-primary mb-0" id="detailProductPrice">Q 0.00</span>
                                    </div>
                                    <div class="col-6">
                                        <span class="text-muted small d-block">Stock en Almacén</span>
                                        <span class="h4 fw-bold text-dark mb-0" id="detailProductStock">0 unid.</span>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-between p-2 rounded border bg-white small mb-3">
                                <span class="text-muted">Stock mínimo requerido:</span>
                                <strong id="detailProductMinStock" class="text-dark">5 unid.</strong>
                            </div>

                            <button type="button" class="btn btn-outline-primary w-100 d-inline-flex align-items-center justify-content-center gap-2" id="btnDetailOpenMovement">
                                <i class="bi bi-arrow-left-right"></i>
                                <span>Registrar Movimiento / Ajuste para este producto</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
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

                            <div class="col-12 col-md-6">
                                <label for="movement_type" class="form-label fw-medium">Tipo de Movimiento <span class="text-danger">*</span></label>
                                <select class="form-select" id="movement_type" name="movement_type" required>
                                    <option value="" selected disabled>Seleccionar tipo...</option>
                                    <option value="SALIDA">SALIDA (Baja por daño, producto roto, descontinuado)</option>
                                    <option value="ENTRADA">ENTRADA (Compra a proveedor, reingreso)</option>
                                    <option value="AJUSTE">AJUSTE (Conteo físico, descuadre de inventario)</option>
                                </select>
                            </div>

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

                            <div class="col-12 col-md-6">
                                <label for="movement_quantity" class="form-label fw-medium">Cantidad a Afectar <span class="text-danger">*</span></label>
                                <input type="number" min="1" class="form-control" id="movement_quantity" name="quantity" value="1" required>
                                <span class="small text-muted" id="movementQtyHelper">Ingresa la cantidad en unidades</span>
                            </div>

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

    <!-- MODAL: NUEVA CATEGORÍA -->
    <div class="modal fade" id="createCategoryModal" tabindex="-1" aria-labelledby="createCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="modal-title-icon rounded-circle d-flex align-items-center justify-content-center icon-primary">
                            <i class="bi bi-folder-plus"></i>
                        </span>
                        <div>
                            <h2 class="h5 modal-title fw-bold text-dark mb-0" id="createCategoryModalLabel">Nueva Categoría</h2>
                            <span class="small text-muted">Organizar productos en grupos específicos</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <form method="POST" action="{{ route('receptionist.inventory.categories.store') }}" id="createCategoryForm">
                    @csrf
                    <div class="modal-body p-3 p-lg-4">
                        <div class="mb-3">
                            <label for="create_cat_name" class="form-label fw-medium">Nombre de la Categoría <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="create_cat_name" name="name"
                                placeholder="Ej. Baterías y Energía" required value="{{ old('name') }}">
                        </div>

                        <div class="mb-3">
                            <label for="create_cat_description" class="form-label fw-medium">Descripción</label>
                            <textarea class="form-control" id="create_cat_description" name="description" rows="2"
                                placeholder="Resumen de artículos incluidos en esta categoría">{{ old('description') }}</textarea>
                        </div>

                        <div class="form-check form-switch pt-1">
                            <input class="form-check-input" type="checkbox" role="switch" id="create_cat_status" name="status" checked>
                            <label class="form-check-label fw-medium text-dark small" for="create_cat_status">
                                Categoría Activa (disponible para asignar productos)
                            </label>
                        </div>
                    </div>

                    <div class="modal-footer bg-light border-top py-3">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2">
                            <i class="bi bi-check-circle"></i>
                            <span>Guardar Categoría</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: EDITAR CATEGORÍA -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="modal-title-icon rounded-circle d-flex align-items-center justify-content-center icon-primary">
                            <i class="bi bi-pencil"></i>
                        </span>
                        <h2 class="h5 modal-title fw-bold text-dark mb-0" id="editCategoryModalLabel">Editar Categoría</h2>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <form method="POST" id="editCategoryForm" action="">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-3 p-lg-4">
                        <input type="hidden" id="edit_cat_id" name="id">
                        <div class="mb-3">
                            <label for="edit_cat_name" class="form-label fw-medium">Nombre de Categoría <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_cat_name" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit_cat_description" class="form-label fw-medium">Descripción</label>
                            <textarea class="form-control" id="edit_cat_description" name="description" rows="2"></textarea>
                        </div>

                        <div class="form-check form-switch pt-1">
                            <input class="form-check-input" type="checkbox" role="switch" id="edit_cat_status" name="status" value="1">
                            <label class="form-check-label fw-medium text-dark small" for="edit_cat_status">
                                Categoría Activa
                            </label>
                        </div>
                    </div>

                    <div class="modal-footer bg-light border-top py-3">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2">
                            <i class="bi bi-check-circle"></i>
                            <span>Actualizar Categoría</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: CAMBIO DE ESTADO DE PRODUCTO (BORRADO LÓGICO) -->
    <div class="modal fade" id="toggleProductStateModal" tabindex="-1" aria-labelledby="toggleProductStateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered state-confirm-dialog">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-white border-bottom py-3">
                    <h2 class="modal-title fs-5 fw-bold d-flex align-items-center gap-2" id="toggleProductStateModalLabel">
                        <i class="bi bi-shield-exclamation text-primary"></i>
                        <span>Cambiar Estado de Producto</span>
                    </h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <form method="POST" id="toggleProductStateForm" action="">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body p-4">
                        <div class="d-flex align-items-start gap-3">
                            <div class="status-confirm-icon-wrapper flex-shrink-0" id="productStateIconWrapper">
                                <i class="bi bi-exclamation-triangle"></i>
                            </div>
                            <div>
                                <p class="mb-2 text-dark" id="productStatePrompt">
                                    ¿Estás seguro de que deseas cambiar el estado de este producto?
                                </p>
                                <div class="p-2 bg-light rounded border mb-2 font-monospace small" id="productStateTarget">
                                    Producto
                                </div>
                                <p class="small text-muted mb-0">
                                    <strong>Nota de Trazabilidad:</strong> Se aplicará un borrado lógico para conservar el historial de ventas y movimientos de inventario sin pérdida de datos.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light border-top py-3">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger" id="btnConfirmProductState">Confirmar Cambio</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: CAMBIO DE ESTADO DE CATEGORÍA (BORRADO LÓGICO) -->
    <div class="modal fade" id="toggleCategoryStateModal" tabindex="-1" aria-labelledby="toggleCategoryStateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered state-confirm-dialog">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-white border-bottom py-3">
                    <h2 class="modal-title fs-5 fw-bold d-flex align-items-center gap-2" id="toggleCategoryStateModalLabel">
                        <i class="bi bi-tags text-primary"></i>
                        <span>Cambiar Estado de Categoría</span>
                    </h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <form method="POST" id="toggleCategoryStateForm" action="">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body p-4">
                        <div class="d-flex align-items-start gap-3">
                            <div class="status-confirm-icon-wrapper flex-shrink-0" id="categoryStateIconWrapper">
                                <i class="bi bi-exclamation-triangle"></i>
                            </div>
                            <div>
                                <p class="mb-2 text-dark" id="categoryStatePrompt">
                                    ¿Estás seguro de que deseas cambiar el estado de esta categoría?
                                </p>
                                <div class="p-2 bg-light rounded border mb-2 font-monospace small" id="categoryStateTarget">
                                    Categoría
                                </div>
                                <p class="small text-muted mb-0">
                                    <strong>Nota de Trazabilidad:</strong> La categoría se desactivará lógicamente preservando la asignación de productos existentes.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light border-top py-3">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger" id="btnConfirmCategoryState">Confirmar Cambio</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/receptionist/inventory.js') }}"></script>
@endpush