@extends('layouts.admin')

@section('title', 'Gestión de Usuarios | CelIx')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/users.css') }}">
@endpush

@section('content')
    <!-- Header de la Sección -->
    <header class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 page-header">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1 page-title">Gestión de Usuarios</h1>
            <p class="text-muted small mb-0 page-subtitle">Administra las cuentas de acceso, roles y estados de los usuarios
                del sistema.</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2" id="btnOpenCreateModal"
                data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="bi bi-person-plus"></i>
                <span>Nuevo Usuario</span>
            </button>
        </div>
    </header>

    <!-- Métricas Rápidas (KPIs) con Cards de Bootstrap -->
    <section class="row g-3 mb-4 stats-grid">
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="stat-icon-wrapper icon-primary d-flex align-items-center justify-content-center rounded-3">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-label text-muted small d-block">Total Usuarios</span>
                        <span class="stat-value h4 fw-bold text-dark mb-0">{{ $totalUsers }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="stat-icon-wrapper icon-success d-flex align-items-center justify-content-center rounded-3">
                        <i class="bi bi-person-check-fill"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-label text-muted small d-block">Usuarios Activos</span>
                        <span class="stat-value h4 fw-bold text-dark mb-0">{{ $activeUsers }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="stat-icon-wrapper icon-warning d-flex align-items-center justify-content-center rounded-3">
                        <i class="bi bi-person-x-fill"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-label text-muted small d-block">Usuarios Inactivos</span>
                        <span class="stat-value h4 fw-bold text-dark mb-0">{{ $inactiveUsers }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contenedor Principal de Tabla y Filtros -->
    <section class="card border-0 shadow-sm mb-4 table-card">
        <!-- Barra de Búsqueda y Filtros con Grid de Bootstrap -->
        <!-- Barra de Búsqueda y Filtros con Grid de Bootstrap -->
        <div class="card-body p-3 border-bottom filter-bar">
            <form id="filterUsersForm" action="{{ route('admin.users.index') }}" method="GET" class="row g-2 align-items-center filter-form">
                <input type="hidden" name="sort_by" value="{{ request('sort_by', 'created_at') }}">
                <input type="hidden" name="sort_direction" value="{{ request('sort_direction', 'desc') }}">
                <input type="hidden" name="per_page" id="hiddenPerPage" value="{{ request('per_page', '5') }}">

                <!-- Búsqueda por texto -->
                <div class="col-12 col-md-5 col-lg-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Buscar por nombre, usuario o correo..."
                            class="form-control border-start-0 ps-0 bg-light input-search">
                    </div>
                </div>

                <!-- Filtro por Rol -->
                <div class="col-12 col-sm-6 col-md-3 col-lg-3">
                    <select name="role" class="form-select select-filter">
                        <option value="">Todos los Roles</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id_rol }}" {{ request('role') == $role->id_rol ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtro por Estado -->
                <div class="col-12 col-sm-6 col-md-2 col-lg-2">
                    <select name="state" class="form-select select-filter">
                        <option value="">Todos los Estados</option>
                        <option value="1" {{ request('state') === '1' ? 'selected' : '' }}>Solo Activos</option>
                        <option value="0" {{ request('state') === '0' ? 'selected' : '' }}>Solo Inactivos</option>
                    </select>
                </div>

                <!-- Botones de Acción -->
                <div class="col-12 col-md-auto d-flex gap-2">
                    <button type="submit" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1"
                        title="Aplicar filtros">
                        <i class="bi bi-funnel"></i>
                        <span>Filtrar</span>
                    </button>

                    @if(request()->hasAny(['search', 'role', 'state', 'sort_by']))
                        <a href="{{ route('admin.users.index') }}"
                            class="btn btn-outline-secondary d-inline-flex align-items-center gap-1"
                            title="Limpiar todos los filtros">
                            <i class="bi bi-x-circle"></i>
                            <span>Limpiar</span>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabla de Datos Responsiva de Bootstrap -->
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 data-table">
                <thead class="table-light">
                    @php
                        $currentSort = request('sort_by', 'created_at');
                        $currentDir = request('sort_direction', 'desc');
                        $getSortUrl = function($col) use ($currentSort, $currentDir) {
                            $newDir = ($currentSort === $col && $currentDir === 'asc') ? 'desc' : 'asc';
                            return request()->fullUrlWithQuery(['sort_by' => $col, 'sort_direction' => $newDir]);
                        };
                        $getSortIcon = function($col) use ($currentSort, $currentDir) {
                            if ($currentSort !== $col) return 'bi-arrow-down-up text-muted';
                            return $currentDir === 'asc' ? 'bi-sort-up text-primary fw-bold' : 'bi-sort-down text-primary fw-bold';
                        };
                    @endphp
                    <tr>
                        <th scope="col" class="py-3 px-3">
                            <a href="{{ $getSortUrl('name') }}" class="text-decoration-none text-dark d-flex align-items-center gap-1">
                                <span>Usuario</span>
                                <i class="bi {{ $getSortIcon('name') }}"></i>
                            </a>
                        </th>
                        <th scope="col" class="py-3 px-3">
                            <a href="{{ $getSortUrl('email') }}" class="text-decoration-none text-dark d-flex align-items-center gap-1">
                                <span>Correo Electrónico</span>
                                <i class="bi {{ $getSortIcon('email') }}"></i>
                            </a>
                        </th>
                        <th scope="col" class="py-3 px-3">
                            <a href="{{ $getSortUrl('role') }}" class="text-decoration-none text-dark d-flex align-items-center gap-1">
                                <span>Rol Asignado</span>
                                <i class="bi {{ $getSortIcon('role') }}"></i>
                            </a>
                        </th>
                        <th scope="col" class="py-3 px-3">
                            <a href="{{ $getSortUrl('state') }}" class="text-decoration-none text-dark d-flex align-items-center gap-1">
                                <span>Estado</span>
                                <i class="bi {{ $getSortIcon('state') }}"></i>
                            </a>
                        </th>
                        <th scope="col" class="py-3 px-3">
                            <a href="{{ $getSortUrl('created_at') }}" class="text-decoration-none text-dark d-flex align-items-center gap-1">
                                <span>Fecha de Registro</span>
                                <i class="bi {{ $getSortIcon('created_at') }}"></i>
                            </a>
                        </th>
                        <th scope="col" class="py-3 px-3 th-actions">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <!-- Columna: Usuario (Avatar, Nombre y Username) -->
                            <td>
                                <div class="user-cell">
                                    <div class="user-avatar">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr($user->lastname, 0, 1)) }}
                                    </div>
                                    <div class="user-meta">
                                        <span class="user-fullname">{{ $user->name }} {{ $user->lastname }}</span>
                                        <span class="user-username">&#64;{{ $user->username }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Columna: Correo -->
                            <td>{{ $user->email }}</td>

                            <!-- Columna: Rol con Badge -->
                            <td>
                                @php
                                    $roleBadgeClasses = [
                                        1 => 'badge-role-admin',
                                        2 => 'badge-role-recep',
                                        3 => 'badge-role-tech',
                                    ];
                                    $roleName = strtoupper(trim($user->role->name ?? ''));
                                    $badgeClass = match ($roleName) {
                                        'ADMINISTRADOR' => 'badge-role-admin',
                                        'RECEPCIONISTA' => 'badge-role-recep',
                                        'TECNICO', 'TÉCNICO' => 'badge-role-tech',
                                        default => $roleBadgeClasses[$user->id_rol] ?? 'badge-role-default',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">
                                    {{ $user->role->name ?? 'Sin Rol' }}                                    
                                </span>
                            </td>

                            <!-- Columna: Estado (Activo / Inactivo) -->
                            <td>
                                @if ($user->state)
                                    <span class="badge badge-status-active">                                        
                                        <span>Activo</span>
                                    </span>
                                @else
                                    <span class="badge badge-status-inactive">                                        
                                        <span>Inactivo</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Columna: Fecha de Creación -->
                            <td class="text-muted small">
                                {{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : 'N/A' }}
                            </td>

                            <!-- Columna: Acciones con Bootstrap 5 Modal Data Attributes -->
                            <td>
                                <div class="action-buttons">
                                    <!-- Botón Editar con Bootstrap Modal -->
                                    <button type="button" class="btn-action btnEditUser" title="Editar datos del usuario"
                                        data-bs-toggle="modal" data-bs-target="#editModal"
                                        data-bs-id="{{ $user->id }}" data-bs-name="{{ $user->name }}"
                                        data-bs-lastname="{{ $user->lastname }}" data-bs-username="{{ $user->username }}"
                                        data-bs-email="{{ $user->email }}" data-bs-role="{{ $user->id_rol }}"
                                        data-bs-state="{{ $user->state ? '1' : '0' }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    <!-- Botón Alternar Estado (Activar/Desactivar) con Bootstrap Modal -->
                                    <button type="button" class="btn-action btn-action-toggle btnToggleUser"
                                        title="{{ $user->state ? 'Desactivar usuario' : 'Activar usuario' }}"
                                        data-bs-toggle="modal" data-bs-target="#toggleStateModal"
                                        data-bs-id="{{ $user->id }}" data-bs-name="{{ $user->name }} {{ $user->lastname }}"
                                        data-bs-username="{{ $user->username }}" data-bs-state="{{ $user->state ? '1' : '0' }}">
                                        @if ($user->state)
                                            <i class="bi bi-toggle-on toggle-icon-active"></i>
                                        @else
                                            <i class="bi bi-toggle-off toggle-icon-inactive"></i>
                                        @endif
                                    </button>

                                    <!-- Botón Cambiar Contraseña con Bootstrap Modal -->
                                    <button type="button" class="btn-action btn-action-key btnChangePasswordUser"
                                        title="Cambiar contraseña del usuario"
                                        data-bs-toggle="modal" data-bs-target="#changePasswordModal"
                                        data-bs-id="{{ $user->id }}"
                                        data-bs-display="{{ $user->name }} {{ $user->lastname }} (@{{ $user->username }})">
                                        <i class="bi bi-key"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <div class="empty-icon">
                                        <i class="bi bi-people"></i>
                                    </div>
                                    <h2 class="empty-title">No se encontraron usuarios</h2>
                                    <p>Intenta ajustar los criterios de búsqueda o agrega un nuevo usuario al sistema.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación de la Tabla con Componentes Bootstrap -->
        <div class="card-footer bg-white border-top p-3 d-flex flex-column flex-md-row align-items-center justify-content-between gap-3 pagination-wrapper">
            <!-- Selector de Paginación Variable -->
            <div class="d-flex align-items-center gap-2 pagination-per-page">
                <label for="perPageSelect" class="form-label mb-0 small text-muted per-page-label">Mostrar:</label>
                <select id="perPageSelect" class="form-select form-select-sm w-auto select-per-page"
                    aria-label="Cantidad de registros por página">
                    <option value="5" {{ request('per_page', '5') == '5' ? 'selected' : '' }}>5</option>
                    <option value="10" {{ request('per_page', '5') == '10' ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('per_page', '5') == '25' ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page', '50') == '50' ? 'selected' : '' }}>50</option>
                </select>
                <span class="small text-muted per-page-info">registros por página</span>
            </div>

            <!-- Contador de Registros Real -->
            <div class="small text-muted pagination-info" id="paginationInfo">
                Mostrando <span class="fw-semibold text-dark">{{ $users->firstItem() ?? 0 }}</span> a <span
                    class="fw-semibold text-dark">{{ $users->lastItem() ?? 0 }}</span> de <span class="fw-semibold text-dark">{{ $users->total() }}</span> usuarios
            </div>

            <!-- Paginador de Usuarios -->
            <nav aria-label="Paginación de usuarios">
                @if($users->hasPages())
                    {{ $users->links('pagination::bootstrap-5') }}
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
    </section>

    <!-- Modal 1: Crear Nuevo Usuario con Bootstrap Modal -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('admin.users.create') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-white border-bottom py-3">
                        <h2 class="modal-title fs-5 fw-bold d-flex align-items-center gap-2" id="createModalLabel">
                            <i class="bi bi-person-plus text-primary"></i>
                            <span>Registrar Nuevo Usuario</span>
                        </h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar modal"></button>
                    </div>

                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-medium" for="create_name">Nombres <span
                                        class="text-danger">*</span></label>
                                <input type="text" id="create_name" name="name" class="form-control"
                                    placeholder="Ej. Carlos" required>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-medium" for="create_lastname">Apellidos <span
                                        class="text-danger">*</span></label>
                                <input type="text" id="create_lastname" name="lastname" class="form-control"
                                    placeholder="Ej. Gómez" required>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-medium" for="create_username">Nombre de Usuario <span
                                        class="text-danger">*</span></label>
                                <input type="text" id="create_username" name="username" class="form-control"
                                    placeholder="Ej. cgomez" required>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-medium" for="create_email">Correo Electrónico <span
                                        class="text-danger">*</span></label>
                                <input type="email" id="create_email" name="email" class="form-control"
                                    placeholder="carlos@empresa.com" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-medium" for="create_role">Rol del Usuario <span
                                        class="text-danger">*</span></label>
                                <select id="create_role" name="id_rol" class="form-select" required>
                                    <option value="">Selecciona un rol...</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id_rol }}">{{ $role->name }} - {{ $role->description }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-medium" for="create_password">Contraseña <span
                                        class="text-danger">*</span></label>
                                <input type="password" id="create_password" name="password" class="form-control"
                                    placeholder="Mínimo 8 caracteres" required minlength="8">
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-medium" for="create_password_confirmation">Confirmar Contraseña
                                    <span class="text-danger">*</span></label>
                                <input type="password" id="create_password_confirmation" name="password_confirmation"
                                    class="form-control" placeholder="Repite la contraseña" required minlength="8">
                            </div>

                            <div class="col-12">
                                <div class="form-check">
                                    <input type="checkbox" name="state" id="create_state" value="1" class="form-check-input"
                                        checked>
                                    <label for="create_state" class="form-check-label small text-muted">
                                        Usuario activo (permitir acceso inmediato al sistema)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light border-top py-3">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2">
                            <i class="bi bi-check-circle"></i>
                            <span>Guardar Usuario</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal 2: Editar Usuario Existente con Bootstrap Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <form action="#" id="editUserForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-white border-bottom py-3">
                        <h2 class="modal-title fs-5 fw-bold d-flex align-items-center gap-2" id="editModalLabel">
                            <i class="bi bi-pencil-square text-primary"></i>
                            <span>Editar Información de Usuario</span>
                        </h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar modal"></button>
                    </div>

                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-medium" for="edit_name">Nombres <span
                                        class="text-danger">*</span></label>
                                <input type="text" id="edit_name" name="name" class="form-control" required>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-medium" for="edit_lastname">Apellidos <span
                                        class="text-danger">*</span></label>
                                <input type="text" id="edit_lastname" name="lastname" class="form-control" required>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-medium" for="edit_username">Nombre de Usuario</label>
                                <input type="text" id="edit_username" name="username" class="form-control bg-light" readonly
                                    tabindex="-1" title="El nombre de usuario no es editable">
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-medium" for="edit_email">Correo Electrónico</label>
                                <input type="email" id="edit_email" name="email" class="form-control bg-light" readonly
                                    tabindex="-1" title="El correo electrónico no es editable">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-medium" for="edit_role">Rol Asignado <span
                                        class="text-danger">*</span></label>
                                <select id="edit_role" name="id_rol" class="form-select" required>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id_rol }}">{{ $role->name }} - {{ $role->description }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <div class="form-check">
                                    <input type="checkbox" name="state" id="edit_state" value="1" class="form-check-input">
                                    <label for="edit_state" class="form-check-label small text-muted">
                                        Cuenta activa en el sistema
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light border-top py-3">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2">
                            <i class="bi bi-check-circle"></i>
                            <span>Actualizar Usuario</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal 3: Cambiar Contraseña del Usuario con Bootstrap Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form id="changePasswordUserForm" method="POST" action="#">
                    @csrf
                    @method('PATCH')
                    <div class="modal-header bg-white border-bottom py-3">
                        <h2 class="modal-title fs-5 fw-bold d-flex align-items-center gap-2" id="changePasswordModalLabel">
                            <i class="bi bi-key text-primary"></i>
                            <span>Cambiar Contraseña de Usuario</span>
                        </h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar modal"></button>
                    </div>

                    <div class="modal-body p-4">
                        <!-- Banner de contexto con el usuario seleccionado -->
                        <div class="user-context-banner d-flex align-items-center gap-2 p-2 bg-light rounded border mb-3">
                            <i class="bi bi-person-badge text-primary fs-5"></i>
                            <span class="small text-dark">Usuario: <strong id="changePasswordUserDisplay"></strong></span>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium" for="change_password">Nueva Contraseña <span
                                    class="text-danger">*</span></label>
                            <div class="input-password-wrapper position-relative d-flex align-items-center">
                                <input type="password" id="change_password" name="password" class="form-control pe-5"
                                    placeholder="Mínimo 8 caracteres" required minlength="8">
                                <button type="button" class="btn-toggle-input-pwd" data-toggle-target="change_password"
                                    aria-label="Mostrar u ocultar contraseña">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label fw-medium" for="change_password_confirmation">Confirmar Nueva
                                Contraseña <span class="text-danger">*</span></label>
                            <div class="input-password-wrapper position-relative d-flex align-items-center">
                                <input type="password" id="change_password_confirmation" name="password_confirmation"
                                    class="form-control pe-5" placeholder="Repite la nueva contraseña" required
                                    minlength="8">
                                <button type="button" class="btn-toggle-input-pwd"
                                    data-toggle-target="change_password_confirmation"
                                    aria-label="Mostrar u ocultar contraseña">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light border-top py-3">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2">
                            <i class="bi bi-check-circle"></i>
                            <span>Actualizar Contraseña</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal 4: Confirmar Cambio de Estado del Usuario con Bootstrap Modal -->
    <div class="modal fade" id="toggleStateModal" tabindex="-1" aria-labelledby="toggleStateModalTitleText"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered toggle-state-dialog">
            <div class="modal-content border-0 shadow">
                <form id="toggleStateUserForm" method="POST" action="#">
                    @csrf
                    @method('PATCH')
                    <div class="modal-header bg-white border-bottom py-3">
                        <h2 class="modal-title fs-5 fw-bold d-flex align-items-center gap-2" id="toggleStateModalTitle">
                            <i class="bi bi-person-gear text-primary" id="toggleStateModalIcon"></i>
                            <span id="toggleStateModalTitleText">Cambiar Estado de Usuario</span>
                        </h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar modal"></button>
                    </div>

                    <div class="modal-body p-4">
                        <div class="status-confirm-card d-flex align-items-start gap-3">
                            <div class="status-confirm-icon-wrapper flex-shrink-0" id="toggleStateIconWrapper">
                                <i class="bi bi-person-exclamation" id="toggleStateBigIcon"></i>
                            </div>
                            <div class="status-confirm-content">
                                <p class="status-confirm-prompt text-dark mb-2" id="toggleStatePrompt">
                                    ¿Estás seguro de que deseas cambiar el estado de la cuenta del usuario?
                                </p>
                                <p class="status-user-target mb-2">
                                    <strong id="toggleStateUserDisplay"></strong>
                                </p>
                                <p class="status-confirm-notice small text-muted mb-0" id="toggleStateNotice"></p>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light border-top py-3">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger d-inline-flex align-items-center gap-2"
                            id="btnConfirmToggleState">
                            <i class="bi bi-check-circle" id="toggleConfirmBtnIcon"></i>
                            <span id="toggleConfirmBtnText">Confirmar Cambio</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/admin/users.js') }}"></script>
@endpush