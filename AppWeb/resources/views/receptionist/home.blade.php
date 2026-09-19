@extends('layouts.receptionist')

@section('title', 'Inicio - Recepción | CelIx')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/receptionist/home.css') }}">
@endpush

@section('content')
    <div class="container-fluid px-0">
        <!-- Encabezado de la Página y Botones Principales de Acción -->
        <div
            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">Punto de Atención y Venta</h1>
            </div>

            <!-- Botones en la parte superior derecha (arriba de la datatable) -->
            <div class="d-flex flex-wrap align-items-center gap-2 w-100 w-md-auto">
                <button type="button"
                    class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-2 flex-grow-1 flex-md-grow-0"
                    data-bs-toggle="modal" data-bs-target="#registerDeviceModal">
                    <i class="bi bi-phone text-primary"></i>
                    <span>Registrar Dispositivo</span>
                </button>

                <button type="button"
                    class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-2 flex-grow-1 flex-md-grow-0"
                    data-bs-toggle="modal" data-bs-target="#registerSaleModal">
                    <i class="bi bi-cart-plus"></i>
                    <span>Registrar Venta</span>
                </button>
            </div>
        </div>

        <!-- Tarjetas de Métricas Rápidas -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-4">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div
                            class="stat-icon-wrapper rounded-3 d-flex align-items-center justify-content-center icon-primary">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <div>
                            <span class="text-muted small fw-medium d-block">Catálogo para Venta</span>
                            <h2 class="h4 fw-bold text-dark mb-0" id="statTotalProducts">
                                {{ $kpis['totalProducts'] ?? count($products ?? []) }}
                            </h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-4">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div
                            class="stat-icon-wrapper rounded-3 d-flex align-items-center justify-content-center icon-success">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div>
                            <span class="text-muted small fw-medium d-block">Stock Disponible</span>
                            <h2 class="h4 fw-bold text-dark mb-0" id="statInStock">
                                {{ $kpis['inStock'] ?? collect($products ?? [])->where('stock', '>', 5)->count() }}
                            </h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-4">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div
                            class="stat-icon-wrapper rounded-3 d-flex align-items-center justify-content-center icon-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                        <div>
                            <span class="text-muted small fw-medium d-block">Stock Bajo / Crítico</span>
                            <h2 class="h4 fw-bold text-dark mb-0" id="statLowStock">
                                {{ $kpis['lowStock'] ?? collect($products ?? [])->where('stock', '<=', 5)->count() }}
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenedor Principal de la Datatable -->
        <!-- Contenedor Principal de la Datatable (Server-Side) -->
        <div class="card border-0 shadow-sm rounded-3 bg-white overflow-hidden mb-4">
            <!-- Encabezado de la Datatable con Formulario de Búsqueda y Filtros en Backend -->
            <div class="card-header bg-white border-bottom p-3 p-lg-4">
                <form method="GET" action="{{ route('receptionist.home') }}" id="catalogFilterForm">
                    <input type="hidden" name="sort_by" value="{{ request('sort_by', 'name') }}">
                    <input type="hidden" name="sort_direction" value="{{ request('sort_direction', 'asc') }}">

                    <div
                        class="d-flex flex-column flex-lg-row justify-content-between align-items-stretch align-items-lg-center gap-3 mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-tag text-primary fs-5"></i>
                            <h2 class="h5 fw-bold text-dark mb-0">Catálogo de Productos para Venta</h2>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <span class="small text-muted text-nowrap">Mostrar:</span>
                            <select name="per_page" id="perPageSelect" class="form-select form-select-sm w-auto"
                                aria-label="Cantidad de registros por página">
                                <option value="5" {{ request('per_page', '10') == '5' ? 'selected' : '' }}>5 registros
                                </option>
                                <option value="10" {{ request('per_page', '10') == '10' ? 'selected' : '' }}>10 registros
                                </option>
                                <option value="25" {{ request('per_page', '10') == '25' ? 'selected' : '' }}>25 registros
                                </option>
                                <option value="50" {{ request('per_page', '10') == '50' ? 'selected' : '' }}>50 registros
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Fila de Búsqueda y Filtros Rápidos -->
                    <div class="row g-2 align-items-center">
                        <div class="col-12 col-md-5 col-lg-5">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" name="search" id="productSearchInput"
                                    class="form-control border-start-0 ps-0"
                                    placeholder="Buscar por código de barras, nombre o descripción..."
                                    value="{{ request('search') }}" aria-label="Buscar producto">
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-md-3 col-lg-3">
                            <select name="category" id="categoryFilter" class="form-select"
                                aria-label="Filtrar por categoría">
                                <option value="">Todas las categorías</option>
                                @if(isset($categories) && count($categories) > 0)
                                    @foreach($categories as $category)
                                        <option value="{{ $category->name }}" {{ request('category') === $category->name ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                            <select name="stock_status" id="stockFilter" class="form-select"
                                aria-label="Filtrar por estado de stock">
                                <option value="">Todos los estados</option>
                                <option value="disponible" {{ request('stock_status') === 'disponible' ? 'selected' : '' }}>En
                                    Stock (Normal)</option>
                                <option value="bajo" {{ request('stock_status') === 'bajo' ? 'selected' : '' }}>Stock Bajo
                                </option>
                                <option value="agotado" {{ request('stock_status') === 'agotado' ? 'selected' : '' }}>Agotado
                                </option>
                            </select>
                        </div>

                        <div class="col-12 col-lg-2 d-flex gap-2">
                            <button type="submit"
                                class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-1 w-100"
                                title="Aplicar filtros">
                                <i class="bi bi-funnel"></i>
                                <span>Filtrar</span>
                            </button>
                            @if(request()->hasAny(['search', 'category', 'stock_status']))
                                <a href="{{ route('receptionist.home') }}"
                                    class="btn btn-outline-danger d-inline-flex align-items-center justify-content-center px-2"
                                    title="Limpiar filtros">
                                    <i class="bi bi-x-circle"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <!-- Tabla Responsiva de Productos -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 datatable-products" id="productsTable">
                    <thead class="table-light">
                        @php
                            $currentSort = request('sort_by', 'name');
                            $currentDir = request('sort_direction', 'asc');
                            $getSortUrl = function ($col) use ($currentSort, $currentDir) {
                                $newDir = ($currentSort === $col && $currentDir === 'asc') ? 'desc' : 'asc';
                                return request()->fullUrlWithQuery(['sort_by' => $col, 'sort_direction' => $newDir]);
                            };
                            $getSortIcon = function ($col) use ($currentSort, $currentDir) {
                                if ($currentSort !== $col)
                                    return 'bi-arrow-down-up text-muted';
                                return $currentDir === 'asc' ? 'bi-sort-up text-primary fw-bold' : 'bi-sort-down text-primary fw-bold';
                            };
                        @endphp
                        <tr>
                            <th scope="col" class="text-nowrap">
                                <a href="{{ $getSortUrl('barcode') }}"
                                    class="text-decoration-none text-dark d-flex align-items-center gap-1">
                                    <span>Código</span>
                                    <i class="bi {{ $getSortIcon('barcode') }}"></i>
                                </a>
                            </th>
                            <th scope="col">
                                <a href="{{ $getSortUrl('name') }}"
                                    class="text-decoration-none text-dark d-flex align-items-center gap-1">
                                    <span>Producto</span>
                                    <i class="bi {{ $getSortIcon('name') }}"></i>
                                </a>
                            </th>
                            <th scope="col" class="text-nowrap">
                                <span>Categoría</span>
                            </th>
                            <th scope="col" class="text-nowrap text-center">
                                <a href="{{ $getSortUrl('stock') }}"
                                    class="text-decoration-none text-dark d-flex align-items-center justify-content-center gap-1">
                                    <span>Stock</span>
                                    <i class="bi {{ $getSortIcon('stock') }}"></i>
                                </a>
                            </th>
                            <th scope="col" class="text-nowrap text-end">
                                <a href="{{ $getSortUrl('price') }}"
                                    class="text-decoration-none text-dark d-flex align-items-center justify-content-end gap-1">
                                    <span>Precio Venta</span>
                                    <i class="bi {{ $getSortIcon('price') }}"></i>
                                </a>
                            </th>
                            <th scope="col" class="text-end text-nowrap">Visualizar / Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="productsTableBody">
                        @forelse($products as $product)
                            @php
                                $isOutOfStock = $product->stock <= 0;
                                $isLowStock = !$isOutOfStock && $product->stock <= $product->minium_stock;
                            @endphp
                            <tr class="product-row">
                                <!-- Código de Barras -->
                                <td class="text-nowrap">
                                    <span class="font-monospace small fw-semibold text-secondary barcode-badge">
                                        <i class="bi bi-upc-scan me-1"></i>{{ $product->bar_code }}
                                    </span>
                                </td>

                                <!-- Nombre y Miniatura -->
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div
                                            class="product-thumb-wrapper rounded bg-light border d-flex align-items-center justify-content-center flex-shrink-0">
                                            @if(!empty($product->image))
                                                <img src="{{ $product->image }}" alt="{{ $product->name }}"
                                                    class="product-thumb-img">
                                            @else
                                                <i class="bi bi-box-seam text-muted"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <span
                                                class="fw-semibold text-dark product-title d-block">{{ $product->name }}</span>
                                            <span
                                                class="text-muted small text-truncate product-desc-preview d-block">{{ $product->description ?? 'Producto para venta en mostrador' }}</span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Categoría -->
                                <td class="text-nowrap">
                                    <span class="badge category-badge">{{ $product->category?->name ?? 'Sin Categoría' }}</span>
                                </td>

                                <!-- Stock con Indicador de Estado -->
                                <td class="text-center text-nowrap">
                                    @if($isOutOfStock)
                                        <span class="badge badge-stock-out d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-x-circle"></i> Agotado (0)
                                        </span>
                                    @elseif($isLowStock)
                                        <span class="badge badge-stock-low d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-exclamation-triangle"></i> Bajo ({{ $product->stock }})
                                        </span>
                                    @else
                                        <span class="badge badge-stock-ok d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-check2"></i> {{ $product->stock }} en stock
                                        </span>
                                    @endif
                                </td>

                                <!-- Precio de Venta -->
                                <td class="text-end text-nowrap">
                                    <span class="fw-bold text-dark">Q {{ number_format($product->price, 2) }}</span>
                                </td>

                                <!-- Visualizar / Acciones con Bootstrap 5 Data Attributes -->
                                <td class="text-end text-nowrap">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <!-- Modal nativo de Bootstrap 5 para detalle de producto -->
                                        <button type="button" class="btn-action btn-action-view" data-bs-toggle="modal"
                                            data-bs-target="#viewProductModal" data-bs-barcode="{{ $product->bar_code }}"
                                            data-bs-name="{{ $product->name }}"
                                            data-bs-category="{{ $product->category?->name ?? 'Sin Categoría' }}"
                                            data-bs-description="{{ $product->description ?? 'Sin descripción detallada disponible.' }}"
                                            data-bs-stock="{{ $product->stock }}"
                                            data-bs-minstock="{{ $product->minium_stock }}"
                                            data-bs-price="{{ number_format($product->price, 2) }}"
                                            data-bs-image="{{ $product->image ?? '' }}"
                                            title="Visualizar producto con detalle ampliado"
                                            aria-label="Visualizar detalle de {{ $product->name }}">
                                            <i class="bi bi-eye"></i>
                                        </button>

                                        <!-- Botón directo para iniciar venta preseleccionando el producto en el modal de ventas -->
                                        <button type="button" class="btn-action btn-action-sale" data-bs-toggle="modal"
                                            data-bs-target="#registerSaleModal" data-bs-barcode="{{ $product->bar_code }}"
                                            title="Agregar a nueva venta" aria-label="Vender {{ $product->name }}">
                                            <i class="bi bi-cart-plus"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="emptyProductsRow">
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                    <span class="fw-medium">No se encontraron productos disponibles en el catálogo.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pie de la Datatable: Información y Paginación Server-Side con Bootstrap 5 -->
            <div
                class="card-footer bg-white border-top d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 py-3 px-3 px-lg-4">
                <div class="small text-muted" id="paginationInfo">
                    Mostrando <strong id="paginationStart">{{ $products->firstItem() ?? 0 }}</strong> a <strong
                        id="paginationEnd">{{ $products->lastItem() ?? 0 }}</strong> de <strong
                        id="paginationTotal">{{ $products->total() }}</strong> productos
                </div>

                <nav aria-label="Paginación de productos">
                    @if($products->hasPages())
                        {{ $products->links('pagination::bootstrap-5') }}
                    @else
                        <ul class="pagination pagination-sm mb-0">
                            <li class="page-item disabled">
                                <span class="page-link"><i class="bi bi-chevron-left me-1"></i>Anterior</span>
                            </li>
                            <li class="page-item active">
                                <span class="page-link">1</span>
                            </li>
                            <li class="page-item disabled">
                                <span class="page-link">Siguiente<i class="bi bi-chevron-right ms-1"></i></span>
                            </li>
                        </ul>
                    @endif
                </nav>
            </div>
        </div>
    </div>

    <!-- MODAL 1: Registrar Dispositivo -->
    <div class="modal fade" id="registerDeviceModal" tabindex="-1" aria-labelledby="registerDeviceModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="modal-title-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-phone"></i>
                        </span>
                        <div>
                            <h2 class="h5 modal-title fw-bold text-dark mb-0" id="registerDeviceModalLabel">Registrar
                                Dispositivo para Servicio</h2>
                            <span class="small text-muted">Ingresa los datos del cliente, el equipo y la falla
                                reportada</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <form id="registerDeviceForm">
                    <div class="modal-body p-3 p-lg-4">
                        <!-- Sección 1: Datos del Cliente -->
                        <div class="modal-section-header mb-3 pb-2 border-bottom d-flex align-items-center gap-2">
                            <i class="bi bi-person text-primary"></i>
                            <h3 class="h6 fw-bold text-dark mb-0">1. Información del Cliente</h3>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-12 col-md-6">
                                <label for="client_name" class="form-label fw-medium">Nombres <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="client_name" name="client_name"
                                    placeholder="Ej. Juan Carlos" required>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="client_lastname" class="form-label fw-medium">Apellidos <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="client_lastname" name="client_lastname"
                                    placeholder="Ej. Gómez Pérez" required>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="client_phone" class="form-label fw-medium">Teléfono / WhatsApp <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i
                                            class="bi bi-telephone"></i></span>
                                    <input type="tel" class="form-control" id="client_phone" name="client_phone"
                                        placeholder="Ej. 55554433" required>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="client_dpi" class="form-label fw-medium">DPI / Identificación</label>
                                <input type="text" class="form-control" id="client_dpi" name="client_dpi"
                                    placeholder="Ej. 2999123450901">
                            </div>
                        </div>

                        <!-- Sección 2: Datos del Dispositivo -->
                        <div class="modal-section-header mb-3 pb-2 border-bottom d-flex align-items-center gap-2">
                            <i class="bi bi-cpu text-primary"></i>
                            <h3 class="h6 fw-bold text-dark mb-0">2. Información del Dispositivo</h3>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-12 col-md-4">
                                <label for="device_type" class="form-label fw-medium">Tipo de Equipo <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="device_type" name="device_type" required>
                                    <option value="" selected disabled>Seleccionar tipo...</option>
                                    <option value="1">Celular / Smartphone</option>
                                    <option value="2">Tablet</option>
                                    <option value="3">Consola de Videojuegos</option>
                                    <option value="4">Smartwatch</option>
                                    <option value="5">Laptop / Computadora</option>
                                    <option value="6">Otro dispositivo</option>
                                </select>
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="device_brand" class="form-label fw-medium">Marca <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="device_brand" name="device_brand"
                                    placeholder="Ej. Samsung, Apple, Xiaomi" required>
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="device_model" class="form-label fw-medium">Modelo <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="device_model" name="device_model"
                                    placeholder="Ej. Galaxy A54, iPhone 13" required>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="device_serial" class="form-label fw-medium">Número de Serie o IMEI</label>
                                <input type="text" class="form-control" id="device_serial" name="device_serial"
                                    placeholder="Opcional pero recomendado">
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="device_password" class="form-label fw-medium">PIN / Contraseña de
                                    Desbloqueo</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="bi bi-key"></i></span>
                                    <input type="text" class="form-control" id="device_password" name="device_password"
                                        placeholder="PIN, patrón o 'Sin contraseña'">
                                </div>
                            </div>
                        </div>

                        <!-- Sección 3: Datos del Ticket / Falla Reportada -->
                        <div class="modal-section-header mb-3 pb-2 border-bottom d-flex align-items-center gap-2">
                            <i class="bi bi-tools text-primary"></i>
                            <h3 class="h6 fw-bold text-dark mb-0">3. Recepción y Falla Técnica</h3>
                        </div>

                        <div class="row g-3">
                            <div class="col-12">
                                <label for="reported_issue" class="form-label fw-medium">Falla Reportada por el Cliente
                                    <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="reported_issue" name="reported_issue" rows="2"
                                    placeholder="Describe claramente el problema reportado (no enciende, pantalla rota, no carga, etc.)"
                                    required></textarea>
                            </div>

                            <div class="col-12 col-md-8">
                                <label for="reception_notes" class="form-label fw-medium">Observaciones Iniciales (Estado
                                    Físico)</label>
                                <input type="text" class="form-control" id="reception_notes" name="reception_notes"
                                    placeholder="Ej. Rayones en tapa trasera, sin bandeja SIM, golpe en esquina superior">
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="deposit" class="form-label fw-medium">Anticipo Abonado (Q)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light fw-bold text-dark">Q</span>
                                    <input type="number" step="0.01" min="0" class="form-control" id="deposit"
                                        name="deposit" placeholder="0.00" value="0.00">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light border-top py-3">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2">
                            <i class="bi bi-check-circle"></i>
                            <span>Guardar e Imprimir Ticket</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 2: Registrar Venta -->
    <div class="modal fade" id="registerSaleModal" tabindex="-1" aria-labelledby="registerSaleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center gap-2">
                        <span
                            class="modal-title-icon rounded-circle d-flex align-items-center justify-content-center icon-primary">
                            <i class="bi bi-cart-plus"></i>
                        </span>
                        <div>
                            <h2 class="h5 modal-title fw-bold text-dark mb-0" id="registerSaleModalLabel">Registrar Nueva
                                Venta</h2>
                            <span class="small text-muted">Agrega uno o varios artículos al comprobante de venta</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <form id="registerSaleForm">
                    <div class="modal-body p-3 p-lg-4">
                        <!-- Fila Superior: Selector rápido para agregar artículos -->
                        <div class="card border rounded-3 p-3 bg-light mb-4">
                            <h3 class="h6 fw-bold text-dark mb-2">Agregar Producto a la Venta</h3>
                            <div class="row g-2 align-items-end">
                                <div class="col-12 col-md-6 col-lg-7">
                                    <label for="saleProductSelector" class="form-label small fw-medium mb-1">Seleccionar
                                        Producto</label>
                                    <select id="saleProductSelector" class="form-select">
                                        <option value="" selected disabled>Selecciona un producto del catálogo...</option>
                                        @foreach($productsForSale ?? $products as $prod)
                                            @php
                                                $pBarCode = is_array($prod) ? $prod['bar_code'] : $prod->bar_code;
                                                $pName = is_array($prod) ? $prod['name'] : $prod->name;
                                                $pPrice = is_array($prod) ? $prod['price'] : $prod->price;
                                                $pStock = is_array($prod) ? $prod['stock'] : $prod->stock;
                                            @endphp
                                            <option value="{{ $pBarCode }}" data-name="{{ $pName }}" data-price="{{ $pPrice }}"
                                                data-stock="{{ $pStock }}" {{ $pStock <= 0 ? 'disabled' : '' }}>
                                                {{ $pBarCode }} - {{ $pName }} (Stock: {{ $pStock }}) - Q
                                                {{ number_format($pPrice, 2) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-6 col-md-3 col-lg-2">
                                    <label for="saleProductQuantity"
                                        class="form-label small fw-medium mb-1">Cantidad</label>
                                    <input type="number" id="saleProductQuantity" class="form-control" min="1" value="1">
                                </div>

                                <div class="col-6 col-md-3 col-lg-3">
                                    <button type="button" id="btnAddProductToSale"
                                        class="btn btn-outline-primary w-100 d-inline-flex align-items-center justify-content-center gap-1">
                                        <i class="bi bi-plus-lg"></i>
                                        <span>Agregar Ítem</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Tabla de Detalle de Venta -->
                        <div class="table-responsive border rounded-3 mb-4">
                            <table class="table table-hover align-middle mb-0" id="saleItemsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" class="ps-3">Código</th>
                                        <th scope="col">Artículo</th>
                                        <th scope="col" class="text-center">Cant.</th>
                                        <th scope="col" class="text-end">Precio Unit.</th>
                                        <th scope="col" class="text-end">Subtotal</th>
                                        <th scope="col" class="text-center pe-3">Quitar</th>
                                    </tr>
                                </thead>
                                <tbody id="saleItemsTableBody">
                                    <tr id="saleEmptyRow">
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            <i class="bi bi-cart-x fs-3 d-block mb-1 text-secondary"></i>
                                            <span>No hay productos agregados a la venta todavía.</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Fila Inferior: Métodos de Pago y Resumen de Totales -->
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <div class="p-3 bg-light rounded-3 border h-100">
                                    <label for="paymentMethod" class="form-label fw-medium">Método de Pago <span
                                            class="text-danger">*</span></label>
                                    <select id="paymentMethod" class="form-select mb-3" required>
                                        <option value="EFECTIVO" selected>Efectivo</option>
                                        <option value="TARJETA">Tarjeta de Débito / Crédito</option>
                                        <option value="TRANSFERENCIA">Transferencia / Depósito</option>
                                    </select>

                                    <div id="cashPaymentFields">
                                        <label for="amountReceived" class="form-label fw-medium">Monto Recibido</label>
                                        <div class="input-group mb-2">
                                            <span class="input-group-text bg-white fw-bold">Q</span>
                                            <input type="number" step="0.01" min="0" class="form-control"
                                                id="amountReceived" placeholder="0.00">
                                        </div>
                                        <div class="d-flex justify-content-between small text-muted">
                                            <span>Cambio a devolver:</span>
                                            <strong id="changeToReturn" class="text-success fs-6">Q 0.00</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="p-3 bg-light rounded-3 border h-100 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Cantidad de Artículos:</span>
                                            <strong id="summaryTotalItems" class="text-dark">0</strong>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Subtotal:</span>
                                            <strong id="summarySubtotal" class="text-dark">Q 0.00</strong>
                                        </div>
                                    </div>

                                    <div class="border-top pt-2 mt-3 d-flex justify-content-between align-items-center">
                                        <span class="h5 fw-bold text-dark mb-0">Total a Pagar:</span>
                                        <span class="h4 fw-bold text-primary mb-0" id="summaryGrandTotal">Q 0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light border-top py-3">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" id="btnSubmitSale"
                            class="btn btn-primary d-inline-flex align-items-center gap-2" disabled>
                            <i class="bi bi-check2-circle"></i>
                            <span>Confirmar Venta y Cobrar</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 3: Visualizar Producto -->
    <div class="modal fade" id="viewProductModal" tabindex="-1" aria-labelledby="viewProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center gap-2">
                        <span
                            class="modal-title-icon rounded-circle d-flex align-items-center justify-content-center icon-primary">
                            <i class="bi bi-eye"></i>
                        </span>
                        <div>
                            <h2 class="h5 modal-title fw-bold text-dark mb-0" id="viewProductModalLabel">Detalle Ampliado
                                del Producto</h2>
                            <span class="small text-muted">Información técnica y disponibilidad para venta</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body p-3 p-lg-4">
                    <div class="row g-4 align-items-center">
                        <!-- Vista previa visual del Producto -->
                        <div class="col-12 col-md-5 text-center">
                            <div
                                class="product-detail-image-box p-4 bg-light rounded-3 border d-flex align-items-center justify-content-center">
                                <div id="modalProductImageContainer">
                                    <i class="bi bi-box-seam fs-1 text-secondary"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <span class="badge bg-light text-secondary border font-monospace px-3 py-2 fs-6"
                                    id="modalProductBarcode">
                                    <i class="bi bi-upc me-1"></i>0000000000
                                </span>
                            </div>
                        </div>

                        <!-- Datos y Especificaciones -->
                        <div class="col-12 col-md-7">
                            <div class="mb-2">
                                <span class="badge category-badge fs-7 mb-2" id="modalProductCategory">Categoría</span>
                                <h3 class="h4 fw-bold text-dark mb-2" id="modalProductName">Nombre del Producto</h3>
                                <p class="text-muted small mb-3" id="modalProductDescription">Descripción detallada del
                                    producto.</p>
                            </div>

                            <!-- Tarjeta de Precios y Disponibilidad -->
                            <div class="p-3 bg-light rounded-3 border mb-3">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <span class="text-muted small d-block">Precio Unitario</span>
                                        <span class="h4 fw-bold text-primary mb-0" id="modalProductPrice">Q 0.00</span>
                                    </div>
                                    <div class="col-6">
                                        <span class="text-muted small d-block">Stock en Tienda</span>
                                        <div id="modalProductStockBadge" class="mt-1">
                                            <span class="badge badge-stock-ok">0 unidades</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Indicador de Stock Mínimo -->
                            <div
                                class="d-flex align-items-center justify-content-between p-2 rounded border bg-white small mb-3">
                                <span class="text-muted">
                                    <i class="bi bi-shield-check text-success me-1"></i>Stock mínimo de seguridad:
                                </span>
                                <strong id="modalProductMinStock" class="text-dark">5 unidades</strong>
                            </div>

                            <!-- Acciones directas dentro del modal de detalle -->
                            <div class="d-flex align-items-center gap-2 pt-2">
                                <button type="button" id="btnModalAddSale"
                                    class="btn btn-primary d-inline-flex align-items-center gap-2 flex-grow-1"
                                    data-bs-toggle="modal" data-bs-target="#registerSaleModal">
                                    <i class="bi bi-cart-plus"></i>
                                    <span>Agregar a la Venta</span>
                                </button>
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                    Cerrar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ==========================================================================
                 BOOTSTRAP 5 TOAST CONTAINER: NOTIFICACIONES DEL SISTEMA
                 ========================================================================== -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100;">
        <div id="receptionistToast" class="toast align-items-center border-0 shadow" role="alert" aria-live="assertive"
            aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2" id="toastBody">
                    <i class="bi bi-check-circle-fill text-success fs-5" id="toastIcon"></i>
                    <span id="toastMessage" class="fw-medium">Notificación del sistema</span>
                </div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/receptionist/home.js') }}"></script>
@endpush