<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recuperar Contraseña | CelIx</title>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Estilos específicos de recuperación de contraseña -->
    <link rel="stylesheet" href="{{ asset('css/auth/recover-password.css') }}">
</head>
<body>
    <main class="recover-card">
        <!-- Lado Izquierdo: Imagen decorativa corporativa -->
        <section class="recover-media-side">
            <img src="{{ asset('images/side_login_two.jpg') }}" alt="CelIx Seguridad" class="recover-image">
            <div class="recover-media-overlay">
                <div class="recover-media-brand">
                    <span class="badge-icon">
                        <i class="bi bi-boxes"></i>
                    </span>
                    <span>Cel<span class="brand-highlight">Ix</span></span>
                </div>
                <p class="recover-media-text">
                    Recuperación segura de credenciales de acceso al sistema.
                </p>
            </div>
        </section>

        <!-- Lado Derecho: Formulario de recuperación de contraseña -->
        <section class="recover-form-side">
            <!-- Botón Superior Izquierda: Regresar a Login -->
            <div class="top-action-bar">
                <a href="{{ url('/') }}" class="btn-back-link" title="Regresar al inicio de sesión">
                    <i class="bi bi-arrow-left"></i>                    
                </a>
            </div>

            <header class="form-header">
                <h1 class="recover-title">Recuperar contraseña</h1>
                <p class="recover-subtitle">
                    Genera un código a tu correo institucional y actualiza tus credenciales de acceso.
                </p>
            </header>

            @if (session('status'))
                <div class="alert-box alert-success">
                    <i class="bi bi-check-circle"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert-box alert-error">
                    <i class="bi bi-exclamation-circle"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form action="{{ url('/reset-password') }}" method="POST" class="recover-form">
                @csrf

                <!-- Campo 1: Correo Electrónico + Botón 'Genera Código' a la par -->
                <div class="form-group">
                    <label for="email" class="form-label">Correo Electrónico</label>
                    <div class="email-action-row">
                        <div class="input-wrapper">
                            <span class="input-icon">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <input
                                type="email"
                                name="email"
                                id="email"
                                class="form-input"
                                placeholder="ejemplo@correo.com"
                                value="{{ old('email') }}"
                                required
                                autocomplete="email"
                                autofocus
                            >
                        </div>
                        <button
                            type="button"
                            class="btn btn-secondary btn-generate-code"
                            id="btnGenerateCode"
                        >
                            <i class="bi bi-key"></i>
                            <span>Genera Código</span>
                        </button>
                    </div>
                </div>

                <!-- Campo 2: Nueva Contraseña -->
                <div class="form-group">
                    <label for="password" class="form-label">Contraseña Nueva</label>
                    <div class="input-wrapper">
                        <span class="input-icon">
                            <i class="bi bi-lock"></i>
                        </span>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="form-input"
                            placeholder="Mínimo 8 caracteres"
                            required
                            autocomplete="new-password"
                        >
                        <button
                            type="button"
                            class="btn-toggle-password"
                            id="togglePassword"
                            aria-label="Mostrar o ocultar nueva contraseña"
                        >
                            <i class="bi bi-eye" id="togglePasswordIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Campo 3: Confirmar Nueva Contraseña -->
                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Confirmar Contraseña Nueva</label>
                    <div class="input-wrapper">
                        <span class="input-icon">
                            <i class="bi bi-lock-fill"></i>
                        </span>
                        <input
                            type="password"
                            name="password_confirmation"
                            id="password_confirmation"
                            class="form-input"
                            placeholder="Repite tu nueva contraseña"
                            required
                            autocomplete="new-password"
                        >
                        <button
                            type="button"
                            class="btn-toggle-password"
                            id="toggleConfirmPassword"
                            aria-label="Mostrar o ocultar confirmación de contraseña"
                        >
                            <i class="bi bi-eye" id="toggleConfirmPasswordIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Campo 4: Código de Verificación -->
                <div class="form-group">
                    <label for="verification_code" class="form-label">Código de Verificación</label>
                    <div class="input-wrapper">
                        <span class="input-icon">
                            <i class="bi bi-shield-check"></i>
                        </span>
                        <input
                            type="text"
                            name="verification_code"
                            id="verification_code"
                            class="form-input"
                            placeholder="Ingresa el código recibido"
                            required
                            autocomplete="off"
                            maxlength="10"
                        >
                    </div>
                </div>

                <!-- Botón: Confirmar cambio de contraseña -->
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="bi bi-check2-circle"></i>
                    <span>Confirmar cambio de contraseña</span>
                </button>
            </form>

            <footer class="recover-footer">
                <p>&copy; {{ date('Y') }} CelIx. Todos los derechos reservados.</p>
            </footer>
        </section>
    </main>

    <script>
        // Alternar visibilidad de contraseña
        function setupPasswordToggle(toggleBtnId, inputId, iconId) {
            const toggleBtn = document.getElementById(toggleBtnId);
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (toggleBtn && input && icon) {
                toggleBtn.addEventListener('click', function () {
                    const isPassword = input.getAttribute('type') === 'password';
                    input.setAttribute('type', isPassword ? 'text' : 'password');
                    icon.classList.toggle('bi-eye', !isPassword);
                    icon.classList.toggle('bi-eye-slash', isPassword);
                });
            }
        }

        setupPasswordToggle('togglePassword', 'password', 'togglePasswordIcon');
        setupPasswordToggle('toggleConfirmPassword', 'password_confirmation', 'toggleConfirmPasswordIcon');

        // Notificación de ejemplo al presionar "Genera Código"
        const btnGenerateCode = document.getElementById('btnGenerateCode');
        const emailInput = document.getElementById('email');

        if (btnGenerateCode && emailInput) {
            btnGenerateCode.addEventListener('click', function () {
                if (!emailInput.value.trim()) {
                    emailInput.focus();
                    alert('Por favor, ingresa tu correo electrónico para enviar el código de verificación.');
                    return;
                }

                btnGenerateCode.disabled = true;
                const originalContent = btnGenerateCode.innerHTML;
                btnGenerateCode.innerHTML = '<i class="bi bi-hourglass-split"></i> <span>Enviando...</span>';

                setTimeout(() => {
                    btnGenerateCode.disabled = false;
                    btnGenerateCode.innerHTML = '<i class="bi bi-check-lg"></i> <span>Código Enviado</span>';
                    setTimeout(() => {
                        btnGenerateCode.innerHTML = originalContent;
                    }, 3500);
                }, 1000);
            });
        }
    </script>
</body>
</html>