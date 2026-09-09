<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - Página no encontrada | CelIx</title>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Estilos específicos de la página -->
    <link rel="stylesheet" href="{{ asset('css/public/not-found.css') }}">
</head>
<body>
    <!-- Header / Navbar -->
    <header class="header-navbar">
        <a href="{{ url('/') }}" class="brand-logo" title="CelIx">
            <span class="brand-badge">
                <i class="bi bi-boxes"></i>
            </span>
            <span>Cel<span class="brand-highlight">Ix</span></span>
        </a>

        <nav>
            <a href="{{ url('/') }}" class="btn btn-secondary navbar-btn-login">
                <i class="bi bi-box-arrow-in-right"></i>
                <span>Iniciar sesión</span>
            </a>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="error-card">
            <!-- Badge -->
            <div>
                <span class="status-badge">
                    <i class="bi bi-exclamation-triangle"></i>
                    <span>Error 404</span>
                </span>
            </div>

            <!-- Error Code Visual -->
            <div class="error-code">
                <span>4</span>
                <i class="bi bi-search icon-alert"></i>
                <span>4</span>
            </div>

            <!-- Title & Description -->
            <h1 class="page-title">Página no encontrada</h1>
            <p class="page-description">
                Lo sentimos, la página que buscas no existe, ha sido movida o la dirección web ingresada no es válida.
            </p>

            <!-- Actions -->
            <div class="actions-group">
                <a href="{{ url('/') }}" class="btn btn-primary">
                    <i class="bi bi-house-door"></i>
                    <span>Ir al inicio</span>
                </a>
                <button type="button" onclick="window.history.back()" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i>
                    <span>Regresar</span>
                </button>
            </div>

            <!-- Divider -->
            <hr class="card-divider">

            <!-- Help Info -->
            <div class="help-info">
                <i class="bi bi-info-circle"></i>
                <span>Si el problema persiste, contacta con el administrador del sistema.</span>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="page-footer">
        <p>&copy; {{ date('Y') }} CelIx. Todos los derechos reservados.</p>
    </footer>
</body>
</html>