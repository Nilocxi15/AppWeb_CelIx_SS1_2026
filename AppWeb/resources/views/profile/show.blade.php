@extends($layout)

@section('title', 'Mi Perfil | CelIx')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/profile/profile.css') }}">
@endpush

@section('content')
    <div class="container-fluid px-0">
        <!-- Encabezado de la Página de Perfil -->
        <div
            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">Mi Perfil de Usuario</h1>
            </div>
        </div>

        @php
            $roleName = strtoupper(trim($user->role->name ?? ''));
            $roleBadgeClass = match ($roleName) {
                'ADMINISTRADOR' => 'badge-role-admin',
                'RECEPCIONISTA' => 'badge-role-recep',
                'TECNICO', 'TÉCNICO' => 'badge-role-tech',
                default => 'badge-role-default',
            };

            $initials = strtoupper(substr($user->name ?? 'U', 0, 1) . substr($user->lastname ?? '', 0, 1));
            if (empty($initials)) {
                $initials = strtoupper(substr($user->username ?? 'U', 0, 2));
            }
        @endphp

        <div class="row g-4">
            <!-- Columna Izquierda: Tarjeta Resumen del Perfil -->
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 bg-white profile-card text-center p-4">
                    <div class="profile-avatar-wrapper mx-auto mb-3 d-flex align-items-center justify-content-center">
                        <span class="profile-avatar-initials">{{ $initials }}</span>
                    </div>

                    <h2 class="h5 fw-bold text-dark mb-1">{{ $user->name }} {{ $user->lastname }}</h2>
                    <p class="text-muted small mb-3">&#64;{{ $user->username }}</p>

                    <div class="d-flex align-items-center justify-content-center gap-2 mb-4">
                        <span class="badge {{ $roleBadgeClass }}">
                            <i class="bi bi-shield-check me-1"></i>{{ $user->role->name ?? 'Sin Rol' }}
                        </span>

                        @if ($user->state)
                            <span class="badge badge-status-active">
                                <i class="bi bi-check-circle me-1"></i>Activo
                            </span>
                        @else
                            <span class="badge badge-status-inactive">
                                <i class="bi bi-dash-circle me-1"></i>Inactivo
                            </span>
                        @endif
                    </div>

                    <hr class="profile-divider my-3">

                    <!-- Metadatos rápidos -->
                    <div class="text-start profile-quick-info">
                        <div class="d-flex align-items-center gap-2 mb-2 small text-muted">
                            <i class="bi bi-envelope text-primary"></i>
                            <span class="text-dark fw-medium text-truncate">{{ $user->email }}</span>
                        </div>

                        <div class="d-flex align-items-center gap-2 mb-2 small text-muted">
                            <i class="bi bi-calendar3 text-primary"></i>
                            <span>Miembro desde: <strong
                                    class="text-dark">{{ $user->created_at ? $user->created_at->format('d/m/Y') : 'N/A' }}</strong></span>
                        </div>

                        <div class="d-flex align-items-center gap-2 small text-muted">
                            <i class="bi bi-shield-lock text-primary"></i>
                            <span>Acceso al sistema: <strong class="text-success">Habilitado</strong></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna Derecha: Información Detallada y Modificación de Contraseña -->
            <div class="col-12 col-lg-8">
                <div class="d-flex flex-column gap-4">
                    <!-- Tarjeta 1: Información de la Cuenta -->
                    <div class="card border-0 shadow-sm rounded-3 bg-white p-3 p-lg-4">
                        <div class="d-flex align-items-center gap-2 border-bottom pb-3 mb-3">
                            <div
                                class="profile-section-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-person-lines-fill"></i>
                            </div>
                            <div>
                                <h2 class="h5 fw-bold text-dark mb-0">Información de la Cuenta</h2>
                                <span class="small text-muted">Datos personales y configuración de identidad</span>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-12 col-sm-6">
                                <label class="form-label small fw-medium text-muted mb-1">Nombres</label>
                                <input type="text" class="form-control bg-light" value="{{ $user->name }}" readonly>
                            </div>

                            <div class="col-12 col-sm-6">
                                <label class="form-label small fw-medium text-muted mb-1">Apellidos</label>
                                <input type="text" class="form-control bg-light" value="{{ $user->lastname }}" readonly>
                            </div>

                            <div class="col-12 col-sm-6">
                                <label class="form-label small fw-medium text-muted mb-1">Nombre de Usuario</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted">&#64;</span>
                                    <input type="text" class="form-control bg-light" value="{{ $user->username }}" readonly>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <label class="form-label small fw-medium text-muted mb-1">Correo Electrónico</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control bg-light" value="{{ $user->email }}" readonly>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <label class="form-label small fw-medium text-muted mb-1">Rol Asignado</label>
                                <input type="text" class="form-control bg-light"
                                    value="{{ $user->role->name ?? 'Sin Rol' }}" readonly>
                            </div>

                            <div class="col-12 col-sm-6">
                                <label class="form-label small fw-medium text-muted mb-1">Fecha de Registro</label>
                                <input type="text" class="form-control bg-light"
                                    value="{{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : 'N/A' }}"
                                    readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta 2: Seguridad y Cambio de Contraseña -->
                    <div class="card border-0 shadow-sm rounded-3 bg-white p-3 p-lg-4">
                        <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <div
                                    class="profile-section-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-shield-lock"></i>
                                </div>
                                <div>
                                    <h2 class="h5 fw-bold text-dark mb-0">Seguridad y Contraseña</h2>
                                    <span class="small text-muted">Actualiza tu contraseña de acceso</span>
                                </div>
                            </div>
                        </div>

                        <!-- Mensaje de Feedback Dinámico -->
                        <div id="passwordFeedbackAlert" class="alert alert-success alert-dismissible fade d-none"
                            role="alert">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-check-circle-fill fs-5" id="passwordFeedbackIcon"></i>
                                <span id="passwordFeedbackMessage">¡Contraseña modificada exitosamente!</span>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                        </div>

                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-check-circle-fill fs-5"></i>
                                    <span>{{ session('success') }}</span>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                            </div>
                        @endif

                        <form id="profileChangePasswordForm" method="POST" action="{{ route('profile.password.update') }}" novalidate>
                            @csrf
                            @method('PUT')
                            <div class="row g-3">
                                <!-- Contraseña Actual -->
                                <div class="col-12">
                                    <label for="current_password" class="form-label fw-medium small">
                                        Contraseña Actual <span class="text-danger">*</span>
                                    </label>
                                    <div class="password-input-group position-relative d-flex flex-wrap align-items-center">
                                        <input type="password" class="form-control pe-5" id="current_password"
                                            name="current_password" placeholder="Ingresa tu contraseña actual" required>
                                        <button type="button" class="btn-toggle-password" data-target="current_password"
                                            aria-label="Mostrar u ocultar contraseña">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <div class="invalid-feedback" id="current_password_feedback"></div>
                                    </div>
                                </div>

                                <!-- Nueva Contraseña -->
                                <div class="col-12 col-md-6">
                                    <label for="new_password" class="form-label fw-medium small">
                                        Nueva Contraseña <span class="text-danger">*</span>
                                    </label>
                                    <div class="password-input-group position-relative d-flex flex-wrap align-items-center">
                                        <input type="password" class="form-control pe-5" id="new_password"
                                            name="new_password" placeholder="Mínimo 8 caracteres" minlength="8" required>
                                        <button type="button" class="btn-toggle-password" data-target="new_password"
                                            aria-label="Mostrar u ocultar contraseña">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <div class="invalid-feedback" id="new_password_feedback"></div>
                                    </div>
                                </div>

                                <!-- Confirmar Nueva Contraseña -->
                                <div class="col-12 col-md-6">
                                    <label for="new_password_confirmation" class="form-label fw-medium small">
                                        Confirmar Nueva Contraseña <span class="text-danger">*</span>
                                    </label>
                                    <div class="password-input-group position-relative d-flex flex-wrap align-items-center">
                                        <input type="password" class="form-control pe-5" id="new_password_confirmation"
                                            name="new_password_confirmation" placeholder="Repite la nueva contraseña"
                                            minlength="8" required>
                                        <button type="button" class="btn-toggle-password"
                                            data-target="new_password_confirmation"
                                            aria-label="Mostrar u ocultar contraseña">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <div class="invalid-feedback" id="new_password_confirmation_feedback"></div>
                                    </div>
                                </div>

                                <!-- Botón de Envío -->
                                <div class="col-12 pt-2">
                                    <button type="submit" id="btnSubmitPasswordChange"
                                        class="btn btn-primary d-inline-flex align-items-center gap-2 px-4">
                                        <i class="bi bi-key"></i>
                                        <span>Modificar Contraseña</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/profile/profile.js') }}"></script>
@endpush