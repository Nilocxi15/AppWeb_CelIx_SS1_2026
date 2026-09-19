<header class="navbar navbar-expand-lg bg-white border-bottom shadow-sm sticky-top admin-navbar py-2 py-lg-1">
    <div class="container-fluid px-3 px-lg-4">
        <!-- Logotipo CelIx -->
        <a href="{{ route('admin.home') }}" class="navbar-brand d-flex align-items-center gap-2 brand-link"
            title="Ir al Inicio del Administrador">
            <span class="brand-badge d-flex align-items-center justify-content-center">
                <i class="bi bi-boxes"></i>
            </span>
            <span class="fw-bold fs-5 text-dark">Cel<span class="brand-highlight">Ix</span></span>
        </a>

        <!-- Botón Toggle para Móviles (Bootstrap Collapse) -->
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarCollapseMenu" aria-controls="navbarCollapseMenu" aria-expanded="false"
            aria-label="Abrir menú de navegación">
            <i class="bi bi-list fs-2 text-dark"></i>
        </button>

        <!-- Menú Colapsable -->
        <div class="collapse navbar-collapse" id="navbarCollapseMenu">
            <!-- Enlaces Principales de Navegación -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-1 gap-lg-2">
                <li class="nav-item">
                    <a href="{{ route('admin.home') }}"
                        class="nav-link d-flex align-items-center gap-2 px-3 py-2 rounded {{ request()->routeIs('admin.home') ? 'active fw-semibold text-primary' : 'text-dark' }}">
                        <i class="bi bi-grid-1x2"></i>
                        <span>Inicio</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('receptionist.home') }}"
                        class="nav-link d-flex align-items-center gap-2 px-3 py-2 rounded {{ request()->routeIs('receptionist.*') ? 'active fw-semibold text-primary' : 'text-dark' }}">
                        <i class="bi bi-box"></i>
                        <span>Apartado de Recepción</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('technician.home') }}"
                        class="nav-link d-flex align-items-center gap-2 px-3 py-2 rounded {{ request()->routeIs('technician.*') ? 'active fw-semibold text-primary' : 'text-dark' }}">
                        <i class="bi bi-tools"></i>
                        <span>Apartado de Técnico</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.users.index') }}"
                        class="nav-link d-flex align-items-center gap-2 px-3 py-2 rounded {{ request()->routeIs('admin.users.*') ? 'active fw-semibold text-primary' : 'text-dark' }}">
                        <i class="bi bi-people"></i>
                        <span>Gestión de Usuarios</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="" class="nav-link d-flex align-items-center gap-2 px-3 py-2 rounded">
                        <i class="bi bi-graph-up"></i>
                        <span>Reportes / Estadísticas</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="" class="nav-link d-flex align-items-center gap-2 px-3 py-2 rounded">
                        <i class="bi bi-person"></i>
                        <span>Perfil</span>
                    </a>
                </li>
            </ul>

            <!-- Sección de Usuario y Salir -->
            <div class="d-flex align-items-center flex-wrap gap-2 pt-2 pt-lg-0 border-top border-lg-0">
                <div class="user-info-pill d-flex align-items-center gap-2 px-3 py-1 bg-light rounded-pill border">
                    <div class="user-avatar-circle">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <span class="user-name-text small fw-medium text-dark">
                        {{ auth()->user()->name ?? 'Administrador' }} {{ auth()->user()->lastname ?? '' }}
                    </span>
                    <span class="role-badge-tag badge">ADMIN</span>
                </div>

                <form action="{{ route('logout') }}" method="POST" class="navbar-logout-form m-0">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm d-flex align-items-center gap-1"
                        title="Cerrar sesión en el sistema">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Salir</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>