<header class="navbar navbar-expand-lg bg-white border-bottom shadow-sm sticky-top technician-navbar py-2 py-lg-1">
    <div class="container-fluid px-3 px-lg-4">
        <!-- Logotipo CelIx -->
        <a href="{{ route('technician.home') }}" class="navbar-brand d-flex align-items-center gap-2 brand-link"
            title="Ir al Inicio del Taller">
            <span class="brand-badge d-flex align-items-center justify-content-center">
                <i class="bi bi-tools"></i>
            </span>
            <span class="fw-bold fs-5 text-dark">Cel<span class="brand-highlight">Ix</span></span>
        </a>

        <!-- Botón Toggle para Móviles (Bootstrap Collapse) -->
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse"
            data-bs-target="#technicianNavbarCollapse" aria-controls="technicianNavbarCollapse" aria-expanded="false"
            aria-label="Abrir menú de navegación">
            <i class="bi bi-list fs-2 text-dark"></i>
        </button>

        <!-- Menú Colapsable -->
        <div class="collapse navbar-collapse" id="technicianNavbarCollapse">
            <!-- Enlaces Principales de Navegación -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-1 gap-lg-2">
                <li class="nav-item">
                    <a href="{{ route('technician.home') }}"
                        class="nav-link d-flex align-items-center gap-2 px-3 py-2 rounded {{ request()->routeIs('technician.*') ? 'active fw-semibold text-primary' : 'text-dark' }}">
                        <i class="bi bi-house-door"></i>
                        <span>Inicio</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('profile.show') }}"
                        class="nav-link d-flex align-items-center gap-2 px-3 py-2 rounded {{ request()->routeIs('profile*') ? 'active fw-semibold text-primary' : 'text-dark' }}">
                        <i class="bi bi-person"></i>
                        <span>Perfil</span>
                    </a>
                </li>
                @if (auth()->user()->hasRole('ADMINISTRADOR'))
                    <li class="nav-item">
                        <a href="{{ route('admin.home') }}"
                            class="nav-link d-flex align-items-center gap-2 px-3 py-2 rounded text-dark"
                            title="Regresar al panel de Administrador">
                            <i class="bi bi-gear"></i>
                            <span>Panel Administrador</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('receptionist.home') }}"
                            class="nav-link d-flex align-items-center gap-2 px-3 py-2 rounded text-dark"
                            title="Ir al panel de Recepción">
                            <i class="bi bi-box"></i>
                            <span>Panel Recepción</span>
                        </a>
                    </li>
                @endif
            </ul>

            <!-- Sección de Usuario y Cerrar Sesión -->
            <div class="d-flex align-items-center flex-wrap gap-2 pt-2 pt-lg-0 border-top border-lg-0">
                <div class="user-info-pill d-flex align-items-center gap-2 px-3 py-1 bg-light rounded-pill border">
                    <div class="user-avatar-circle">
                        {{ strtoupper(substr(auth()->user()->name ?? 'T', 0, 1)) }}
                    </div>
                    <span class="user-name-text small fw-medium text-dark">
                        {{ auth()->user()->name ?? 'Técnico' }} {{ auth()->user()->lastname ?? '' }}
                    </span>
                    <span class="role-badge-tag badge">
                        {{ auth()->user()->hasRole('ADMINISTRADOR') ? 'ADMIN' : 'TÉCNICO' }}
                    </span>
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