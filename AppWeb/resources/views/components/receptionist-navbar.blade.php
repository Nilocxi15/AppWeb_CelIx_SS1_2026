<header class="navbar navbar-expand-lg bg-white border-bottom shadow-sm sticky-top receptionist-navbar py-2 py-lg-1">
    <div class="container-fluid px-3 px-lg-4">
        <!-- Logotipo CelIx -->
        <a href="{{ route('receptionist.home') }}" class="navbar-brand d-flex align-items-center gap-2 brand-link"
            title="Ir al Inicio de Recepción">
            <span class="brand-badge d-flex align-items-center justify-content-center">
                <i class="bi bi-boxes"></i>
            </span>
            <span class="fw-bold fs-5 text-dark">Cel<span class="brand-highlight">Ix</span></span>
        </a>

        <!-- Botón Toggle para Móviles (Bootstrap Collapse) -->
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse"
            data-bs-target="#receptionistNavbarCollapse" aria-controls="receptionistNavbarCollapse"
            aria-expanded="false" aria-label="Abrir menú de navegación">
            <i class="bi bi-list fs-2 text-dark"></i>
        </button>

        <!-- Menú Colapsable -->
        <div class="collapse navbar-collapse" id="receptionistNavbarCollapse">
            <!-- Enlaces Principales de Navegación -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-1 gap-lg-2">
                <li class="nav-item">
                    <a href="{{ route('receptionist.home') }}"
                        class="nav-link d-flex align-items-center gap-2 px-3 py-2 rounded {{ request()->routeIs('receptionist.home') ? 'active fw-semibold text-primary' : 'text-dark' }}">
                        <i class="bi bi-house-door"></i>
                        <span>Inicio</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('receptionist.inventory') }}"
                        class="nav-link d-flex align-items-center gap-2 px-3 py-2 rounded {{ request()->routeIs('receptionist.inventory*') ? 'active fw-semibold text-primary' : 'text-dark' }}">
                        <i class="bi bi-box-seam"></i>
                        <span>Inventario</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('receptionist.kardex') }}"
                        class="nav-link d-flex align-items-center gap-2 px-3 py-2 rounded {{ request()->routeIs('receptionist.kardex*') ? 'active fw-semibold text-primary' : 'text-dark' }}">
                        <i class="bi bi-clock-history"></i>
                        <span>Historiales</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('receptionist.deliveries') }}"
                        class="nav-link d-flex align-items-center gap-2 px-3 py-2 rounded {{ request()->routeIs('receptionist.deliveries*') ? 'active fw-semibold text-primary' : 'text-dark' }}">
                        <i class="bi bi-card-checklist"></i>
                        <span>Entregas</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('profile.show') }}"
                        class="nav-link d-flex align-items-center gap-2 px-3 py-2 rounded {{ request()->routeIs('profile*') ? 'active fw-semibold text-primary' : 'text-dark' }}">
                        <i class="bi bi-person"></i>
                        <span>Perfil</span>
                    </a>
                </li>
                @if (auth()->user()->hasRole('ADMINISTRADOR')) <!-- Mostrar solo si el usuario es Administrador -->
                    <li class="nav-item">
                        <a href="{{ route('admin.home') }}"
                            class="nav-link d-flex align-items-center gap-2 px-3 py-2 rounded text-dark">
                            <i class="bi bi-gear"></i>
                            <span>Panel de Administrador</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('technician.home') }}"
                            class="nav-link d-flex align-items-center gap-2 px-3 py-2 rounded text-dark">
                            <i class="bi bi-tools"></i>
                            <span>Panel de Técnico</span>
                        </a>
                    </li>
                @endif
            </ul>

            <!-- Sección de Usuario y Cerrar Sesión -->
            <div class="d-flex align-items-center flex-wrap gap-2 pt-2 pt-lg-0 border-top border-lg-0">
                <div class="user-info-pill d-flex align-items-center gap-2 px-3 py-1 bg-light rounded-pill border">
                    <div class="user-avatar-circle">
                        {{ strtoupper(substr(auth()->user()->name ?? 'R', 0, 1)) }}
                    </div>
                    <span class="user-name-text small fw-medium text-dark">
                        {{ auth()->user()->name ?? 'Recepcionista' }} {{ auth()->user()->lastname ?? '' }}
                    </span>
                    <span class="role-badge-tag badge">RECEPCIÓN</span>
                </div>

                <form action="{{ route('logout') }}" method="POST" class="navbar-logout-form m-0">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm d-flex align-items-center gap-1"
                        title="Cerrar sesión en el sistema">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Cerrar Sesión</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>