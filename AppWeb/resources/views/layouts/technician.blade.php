<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Taller Técnico | CelIx')</title>

    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Estilos base del layout y navegación de Técnico (Tema CelIx) -->
    <link rel="stylesheet" href="{{ asset('css/technician/technician-layout.css') }}">

    <!-- Estilos adicionales específicos de cada vista -->
    @stack('styles')
</head>

<body class="min-vh-100 d-flex flex-column bg-light">
    <!-- Barra de Navegación Modular para Técnico -->
    <x-technician-navbar />

    <!-- Contenido Principal -->
    <main class="technician-main flex-grow-1 container-fluid px-3 px-lg-4 py-4">
        <x-alert />
        @yield('content')
    </main>

    <!-- Footer Discreto -->
    <footer class="technician-footer text-center py-3 border-top bg-white">
        <p class="mb-0 text-muted small">&copy; {{ date('Y') }} CelIx. Todos los derechos reservados.</p>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Scripts del Layout -->
    <script src="{{ asset('js/technician/technician-navbar.js') }}"></script>

    <!-- Scripts de cada vista -->
    @stack('scripts')
</body>

</html>