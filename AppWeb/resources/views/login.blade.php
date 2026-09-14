<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar Sesión | CelIx</title>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Estilos específicos de inicio de sesión -->
    <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
</head>

<body>
    <main class="login-card">
        <!-- Lado Izquierdo: Imagen decorativa corporativa -->
        <section class="login-media-side">
            <img src="{{ asset('images/side_login_two.jpg') }}" alt="CelIx Plataforma" class="login-image">
            <div class="login-media-overlay">
                <div class="login-media-brand">
                    <span class="badge-icon">
                        <i class="bi bi-boxes"></i>
                    </span>
                    <span>Cel<span class="brand-highlight">Ix</span></span>
                </div>
                <p class="login-media-text">
                    Sistema integral de gestión y control empresarial.
                </p>
            </div>
        </section>

        <!-- Lado Derecho: Formulario de inicio de sesión -->
        <section class="login-form-side">
            <header class="form-header">
                <a href="{{ url('/') }}" class="brand-title-mobile">
                    <span class="badge-icon">
                        <i class="bi bi-boxes"></i>
                    </span>
                    <span>Cel<span class="brand-highlight">Ix</span></span>
                </a>
                <h1 class="login-title">Iniciar sesión</h1>
                <p class="login-subtitle">Ingresa tus credenciales para acceder al sistema</p>
            </header>

            @if ($errors->any())
                <div class="alert-box alert-error">
                    <i class="bi bi-exclamation-circle"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form action="{{ url('/login') }}" method="POST" class="login-form">
                @csrf

                <!-- Campo: Nombre de Usuario -->
                <div class="form-group">
                    <label for="username" class="form-label">Nombre de Usuario</label>
                    <div class="input-wrapper">
                        <span class="input-icon">
                            <i class="bi bi-person"></i>
                        </span>
                        <input type="text" name="username" id="username" class="form-input"
                            placeholder="Ingresa tu nombre de usuario" value="{{ old('username') }}" required
                            autocomplete="username" autofocus>
                    </div>
                </div>

                <!-- Campo: Contraseña -->
                <div class="form-group">
                    <div class="label-row">
                        <label for="password" class="form-label">Contraseña</label>
                    </div>
                    <div class="input-wrapper">
                        <span class="input-icon">
                            <i class="bi bi-lock"></i>
                        </span>
                        <input type="password" name="password" id="password" class="form-input"
                            placeholder="Ingresa tu contraseña" required autocomplete="current-password">
                        <button type="button" class="btn-toggle-password" id="togglePassword"
                            aria-label="Mostrar o ocultar contraseña">
                            <i class="bi bi-eye" id="togglePasswordIcon"></i>
                        </button>
                    </div>
                    <div class="label-row forgot-password">
                        <a href="{{ url('/forgot-password') }}" class="forgot-link">¿Olvidaste tu contraseña?</a>
                    </div>
                </div>

                <!-- Botón de Envío -->
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="bi bi-box-arrow-in-right"></i>
                    <span>Iniciar sesión</span>
                </button>
            </form>

            <footer class="login-footer">
                <p>&copy; {{ date('Y') }} CelIx. Todos los derechos reservados.</p>
            </footer>
        </section>
    </main>

    <!-- Script externo de autenticación -->
    <script src="{{ asset('js/auth/login.js') }}"></script>
</body>

</html>