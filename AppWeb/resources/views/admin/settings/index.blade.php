@extends('layouts.admin')

@section('title', 'Configuración del Sistema | CelIx')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/settings.css') }}">
@endpush

@section('content')
    <div class="container-fluid px-0">
        <!-- Encabezado de la Página de Configuración -->
        <header
            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4 settings-header">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1 page-title">Configuración del Sistema</h1>
            </div>

            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2" data-bs-toggle="modal"
                    data-bs-target="#createDeviceTypeModal">
                    <i class="bi bi-plus-circle"></i>
                    <span>Nuevo Tipo de Dispositivo</span>
                </button>
            </div>
        </header>

        <!-- Tarjetas de Métricas Rápidas (KPIs) -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-4">
                <div class="card border-0 shadow-sm rounded-3 bg-white p-3 settings-stat-card h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div
                            class="settings-stat-icon rounded-3 d-flex align-items-center justify-content-center icon-types-primary">
                            <i class="bi bi-phone"></i>
                        </div>
                        <div>
                            <span class="text-muted small fw-medium d-block">Total Tipos Registrados</span>
                            <h2 class="h4 fw-bold text-dark mb-0">{{ $totalTypes }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-4">
                <div class="card border-0 shadow-sm rounded-3 bg-white p-3 settings-stat-card h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div
                            class="settings-stat-icon rounded-3 d-flex align-items-center justify-content-center icon-types-success">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div>
                            <span class="text-muted small fw-medium d-block">Tipos Activos</span>
                            <h2 class="h4 fw-bold text-dark mb-0">{{ $activeTypes }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-4">
                <div class="card border-0 shadow-sm rounded-3 bg-white p-3 settings-stat-card h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div
                            class="settings-stat-icon rounded-3 d-flex align-items-center justify-content-center icon-types-warning">
                            <i class="bi bi-slash-circle"></i>
                        </div>
                        <div>
                            <span class="text-muted small fw-medium d-block">Tipos Inactivos (Bajas Lógicas)</span>
                            <h2 class="h4 fw-bold text-dark mb-0">{{ $inactiveTypes }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pestañas de Secciones de Configuración -->
        <div class="card border-0 shadow-sm rounded-3 bg-white mb-4 settings-table-card">
            <div class="card-header bg-white border-bottom pt-3 pb-0 px-3 px-lg-4">
                <ul class="nav nav-tabs border-0 settings-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active d-inline-flex align-items-center gap-2" id="device-types-tab"
                            data-bs-toggle="tab" data-bs-target="#device-types-pane" type="button" role="tab"
                            aria-controls="device-types-pane" aria-selected="true">
                            <i class="bi bi-cpu"></i>
                            <span>Tipos de Dispositivos</span>
                            <span class="badge bg-secondary rounded-pill ms-1">{{ $totalTypes }}</span>
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-3 p-lg-4">
                <div class="tab-content">
                    <!-- Panel 1: Tipos de Dispositivos -->
                    <div class="tab-pane fade show active" id="device-types-pane" role="tabpanel"
                        aria-labelledby="device-types-tab" tabindex="0">
                        <!-- Barra de Búsqueda y Filtros Server-Side -->
                        <form id="deviceTypesFilterForm" method="GET" action="{{ route('admin.settings.index') }}"
                            class="mb-4">
                            <input type="hidden" name="tab" value="device_types">
                            <div class="row g-3 align-items-center">
                                <!-- Buscador por Nombre -->
                                <div class="col-12 col-md-5">
                                    <div class="input-group search-input-group">
                                        <span class="input-group-text bg-white text-muted">
                                            <i class="bi bi-search"></i>
                                        </span>
                                        <input type="text" name="search" class="form-control"
                                            placeholder="Buscar por tipo (ej. Celular, Tablet...)"
                                            value="{{ request('search') }}" aria-label="Buscar tipo de dispositivo">
                                        @if(request('search'))
                                            <a href="{{ route('admin.settings.index', ['tab' => 'device_types']) }}"
                                                class="btn btn-outline-secondary" title="Limpiar búsqueda">
                                                <i class="bi bi-x"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                <!-- Filtro por Estado -->
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <label for="deviceTypeStatusFilter"
                                            class="form-label small text-muted mb-0 text-nowrap">Estado:</label>
                                        <select name="status" id="deviceTypeStatusFilter" class="form-select">
                                            <option value="">Todos los estados</option>
                                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Solo Activos
                                            </option>
                                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Solo Inactivos
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Selector de Registros por Página -->
                                <div class="col-12 col-sm-6 col-md-3">
                                    <div class="d-flex align-items-center justify-content-sm-end gap-2">
                                        <label for="deviceTypePerPageSelect"
                                            class="form-label small text-muted mb-0 text-nowrap">Mostrar:</label>
                                        <select name="per_page" id="deviceTypePerPageSelect" class="form-select w-auto">
                                            <option value="5" {{ request('per_page', 10) == 5 ? 'selected' : '' }}>5</option>
                                            <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10
                                            </option>
                                            <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25
                                            </option>
                                            <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- Datatable de Tipos de Dispositivos -->
                        <div class="table-responsive rounded-3 border">
                            <table class="table settings-table mb-0 align-middle">
                                <thead>
                                    <tr>
                                        <th scope="col" class="text-center">#</th>
                                        <th scope="col">Tipo de Dispositivo</th>
                                        <th scope="col" class="text-center">Estado</th>
                                        <th scope="col">Fecha de Registro</th>
                                        <th scope="col" class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($deviceTypes as $type)
                                        <tr>
                                            <!-- ID -->
                                            <td class="text-center text-muted fw-semibold">{{ $type->id }}</td>

                                            <!-- Nombre del Tipo con icono alusivo -->
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <div>
                                                        <span class="fw-bold text-dark d-block">{{ $type->name }}</span>
                                                        <span class="small text-muted">ID: {{ $type->id }}</span>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Estado (Borrado Lógico) -->
                                            <td class="text-center">
                                                @if ($type->status)
                                                    <span class="badge badge-status-active">
                                                        <i class="bi bi-check-circle me-1"></i>Activo
                                                    </span>
                                                @else
                                                    <span class="badge badge-status-inactive">
                                                        <i class="bi bi-dash-circle me-1"></i>Inactivo
                                                    </span>
                                                @endif
                                            </td>

                                            <!-- Fecha de Creación -->
                                            <td class="text-muted small">
                                                {{ $type->created_at ? $type->created_at->format('d/m/Y H:i') : 'N/A' }}
                                            </td>

                                            <!-- Acciones -->
                                            <td class="text-end">
                                                <div class="d-inline-flex align-items-center gap-1">
                                                    <!-- Botón Editar -->
                                                    <button type="button"
                                                        class="btn btn-outline-secondary btn-action-icon btn-edit-device-type"
                                                        title="Editar tipo de dispositivo" data-bs-toggle="modal"
                                                        data-bs-target="#editDeviceTypeModal" data-id="{{ $type->id }}"
                                                        data-name="{{ $type->name }}"
                                                        data-status="{{ $type->status ? '1' : '0' }}"
                                                        data-update-url="{{ route('admin.settings.device-types.update', $type->id) }}">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>

                                                    <!-- Botón Alternar Estado (Borrado Lógico) -->
                                                    <button type="button"
                                                        class="btn {{ $type->status ? 'btn-outline-danger' : 'btn-outline-success' }} btn-action-icon btn-toggle-status-device-type"
                                                        title="{{ $type->status ? 'Desactivar tipo (borrado lógico)' : 'Reactivar tipo' }}"
                                                        data-bs-toggle="modal" data-bs-target="#toggleDeviceTypeModal"
                                                        data-id="{{ $type->id }}" data-name="{{ $type->name }}"
                                                        data-status="{{ $type->status ? '1' : '0' }}"
                                                        data-toggle-url="{{ route('admin.settings.device-types.toggle-status', $type->id) }}">
                                                        <i
                                                            class="bi {{ $type->status ? 'bi-toggle-on' : 'bi-toggle-off' }}"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5">
                                                <div
                                                    class="d-flex flex-column align-items-center justify-content-center text-muted">
                                                    <i class="bi bi-inbox fs-1 mb-2 text-secondary"></i>
                                                    <span class="fw-semibold">No se encontraron tipos de dispositivos</span>
                                                    <p class="small text-muted mb-3">
                                                        @if(request('search') || request('status') !== null && request('status') !== '')
                                                            No hay resultados que coincidan con los filtros aplicados.
                                                        @else
                                                            Comienza registrando los tipos de dispositivos que atiende tu taller.
                                                        @endif
                                                    </p>
                                                    @if(request('search') || request('status') !== null && request('status') !== '')
                                                        <a href="{{ route('admin.settings.index', ['tab' => 'device_types']) }}"
                                                            class="btn btn-sm btn-outline-secondary">
                                                            <i class="bi bi-arrow-clockwise me-1"></i>Limpiar Filtros
                                                        </a>
                                                    @else
                                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                            data-bs-target="#createDeviceTypeModal">
                                                            <i class="bi bi-plus-circle me-1"></i>Agregar Primer Tipo
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación de la Tabla -->
                        @if ($deviceTypes->hasPages())
                            <div
                                class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 mt-4 pt-2">
                                <span class="small text-muted">
                                    Mostrando registros del {{ $deviceTypes->firstItem() }} al {{ $deviceTypes->lastItem() }} de
                                    un total de {{ $deviceTypes->total() }}
                                </span>
                                <div>
                                    {{ $deviceTypes->links('pagination::bootstrap-5') }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODALES DEL MÓDULO DE CONFIGURACIÓN -->

    <!-- 1. Modal: Crear Tipo de Dispositivo -->
    <div class="modal fade" id="createDeviceTypeModal" tabindex="-1" aria-labelledby="createDeviceTypeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header modal-header-custom bg-light">
                    <div class="d-flex align-items-center gap-2">
                        <div
                            class="rounded-circle p-2 bg-white text-primary border shadow-sm d-flex align-items-center justify-content-center">
                            <i class="bi bi-plus-circle-fill fs-5"></i>
                        </div>
                        <div>
                            <h2 class="modal-title h5 fw-bold text-dark mb-0" id="createDeviceTypeModalLabel">Nuevo Tipo de
                                Dispositivo</h2>
                            <span class="small text-muted">Define una nueva categoría para los equipos del taller</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <form action="{{ route('admin.settings.device-types.store') }}" method="POST">
                    @csrf
                    <div class="modal-body modal-body-custom">
                        <div class="mb-3">
                            <label for="create_name" class="form-label fw-medium small">
                                Nombre del Tipo <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted">
                                    <i class="bi bi-tag"></i>
                                </span>
                                <input type="text" name="name" id="create_name" class="form-control"
                                    placeholder="Ej: Celular, Tablet, Laptop, Smartwatch..." required maxlength="50"
                                    autofocus>
                            </div>
                            <span class="form-text small text-muted">Debe ser un nombre representativo y único en el
                                sistema.</span>
                        </div>

                        <div class="form-check form-switch mt-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="create_status" name="status"
                                value="1" checked>
                            <label class="form-check-label fw-medium small text-dark" for="create_status">
                                Habilitar tipo inmediatamente (Estado Activo)
                            </label>
                        </div>
                    </div>

                    <div class="modal-footer modal-footer-custom bg-light">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2 px-4">
                            <i class="bi bi-check-lg"></i>
                            <span>Guardar Tipo</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 2. Modal: Editar Tipo de Dispositivo -->
    <div class="modal fade" id="editDeviceTypeModal" tabindex="-1" aria-labelledby="editDeviceTypeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header modal-header-custom bg-light">
                    <div class="d-flex align-items-center gap-2">
                        <div
                            class="rounded-circle p-2 bg-white text-secondary border shadow-sm d-flex align-items-center justify-content-center">
                            <i class="bi bi-pencil-fill fs-5"></i>
                        </div>
                        <div>
                            <h2 class="modal-title h5 fw-bold text-dark mb-0" id="editDeviceTypeModalLabel">Editar Tipo de
                                Dispositivo</h2>
                            <span class="small text-muted">Actualiza el nombre o estado del catálogo maestro</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <form id="editDeviceTypeForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_device_type_id">

                    <div class="modal-body modal-body-custom">
                        <div class="mb-3">
                            <label for="edit_device_type_name" class="form-label fw-medium small">
                                Nombre del Tipo <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted">
                                    <i class="bi bi-tag"></i>
                                </span>
                                <input type="text" name="name" id="edit_device_type_name" class="form-control" required
                                    maxlength="50">
                            </div>
                        </div>

                        <div class="form-check form-switch mt-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="edit_device_type_status"
                                name="status" value="1">
                            <label class="form-check-label fw-medium small text-dark" for="edit_device_type_status">
                                Estado Activo para nuevas recepciones
                            </label>
                        </div>
                    </div>

                    <div class="modal-footer modal-footer-custom bg-light">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2 px-4">
                            <i class="bi bi-check-lg"></i>
                            <span>Actualizar Tipo</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 3. Modal: Alternar Estado / Borrado Lógico -->
    <div class="modal fade" id="toggleDeviceTypeModal" tabindex="-1" aria-labelledby="toggleDeviceTypeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-body p-4 text-center">
                    <div id="toggleModalIconWrapper" class="state-confirm-icon-wrapper icon-deactivate mb-3">
                        <i id="toggleModalIcon" class="bi bi-shield-x"></i>
                    </div>

                    <h2 class="h5 fw-bold text-dark mb-2" id="toggleDeviceTypeModalLabel">¿Confirmar cambio de estado?</h2>
                    <p class="text-muted small mb-3">
                        Estás a punto de <strong id="toggleDeviceTypeActionText" class="text-dark">desactivar</strong> el
                        tipo de dispositivo
                        <span class="fw-bold text-dark d-block fs-6 mt-1" id="toggleDeviceTypeNameText">---</span>
                    </p>

                    <div class="alert alert-light border small text-muted text-start mb-4">
                        <i class="bi bi-info-circle me-1 text-primary"></i>
                        <strong>Nota de trazabilidad:</strong> Esta acción realiza una eliminación lógica. Los dispositivos
                        físicos y tickets históricos asociados a este tipo no se eliminarán ni se alterarán.
                    </div>

                    <form id="toggleDeviceTypeForm" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="d-flex justify-content-center gap-2">
                            <button type="button" class="btn btn-outline-secondary px-4"
                                data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" id="btnConfirmToggleDeviceType"
                                class="btn btn-danger d-inline-flex align-items-center gap-2 px-4">
                                <span>Sí, Desactivar</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/admin/settings.js') }}"></script>
@endpush