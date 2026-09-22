<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel de Administración | CelIx')</title>

    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Estilos base del layout y navegación (Sobrescribe tema CelIx) -->
    <link rel="stylesheet" href="{{ asset('css/admin/admin-layout.css') }}">

    <!-- Estilos adicionales específicos de cada vista -->
    @stack('styles')
</head>

<body class="min-vh-100 d-flex flex-column">
    <!-- Barra de Navegación Modular -->
    <x-admin-navbar />

    <!-- Contenido Principal -->
    <main class="admin-main flex-grow-1 container-fluid px-3 px-lg-4 py-4">
        <x-alert />
        @yield('content')
    </main>

    <!-- Footer Discreto -->
    <footer class="admin-footer">
        <p class="mb-0">&copy; {{ date('Y') }} CelIx. Todos los derechos reservados.</p>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Scripts del Layout -->
    <script src="{{ asset('js/admin/admin-navbar.js') }}"></script>

    <!-- Scripts de cada vista -->
    @stack('scripts')
</body>

</html>