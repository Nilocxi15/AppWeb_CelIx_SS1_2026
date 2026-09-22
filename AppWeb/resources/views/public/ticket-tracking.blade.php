<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Seguimiento de Reparación #TK-{{ str_pad($ticket->id, 5, '0', STR_PAD_LEFT) }} | CelIx</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Estilos específicos de la página de seguimiento público -->
    <link rel="stylesheet" href="{{ asset('css/public/ticket-tracking.css') }}">
</head>
<body>

    <!-- Barra de Navegación Pública -->
    <header class="tracking-navbar py-3">
        <div class="container d-flex align-items-center justify-content-between">
            <a href="{{ url('/') }}" class="brand-logo-text d-flex align-items-center gap-2">
                <i class="bi bi-tools brand-highlight fs-4"></i>
                <span>Cel<span class="brand-highlight">Ix</span></span>
                <span class="badge bg-light text-muted border ms-2 small fw-normal d-none d-sm-inline-block">Portal de Clientes</span>
            </a>

            <div class="d-flex align-items-center gap-2">
                <a href="tel:+50222000000" class="btn btn-sm btn-outline-secondary d-none d-md-inline-flex align-items-center gap-1">
                    <i class="bi bi-telephone"></i>
                    <span>Soporte: 5933 - 3514</span>
                </a>
                <a href="{{ route('tickets.tracking.pdf', $ticket->qr_token) }}" class="btn-celix-primary btn-sm" title="Descargar comprobante en PDF">
                    <i class="bi bi-file-earmark-pdf"></i>
                    <span>Descargar PDF</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Contenido Principal -->
    <main class="container my-4 my-lg-5">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">

                <div class="tracking-main-card p-3 p-md-4 p-lg-5">

                    <!-- Banner Hero: Estado Actual -->
                    <div class="status-hero mb-4">
                        <div class="row align-items-center g-3">
                            <div class="col-12 col-md-8">
                                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                                    <span class="badge bg-white text-dark font-monospace px-2 py-1">
                                        #TK-{{ str_pad($ticket->id, 5, '0', STR_PAD_LEFT) }}
                                    </span>
                                    <span class="text-white-50 small">
                                        Ingreso: {{ $ticket->intake_date ? $ticket->intake_date->format('d/m/Y h:i A') : now()->format('d/m/Y h:i A') }}
                                    </span>
                                </div>
                                <h1 class="h3 fw-bold mb-1 text-white">
                                    {{ $ticket->device->brand ?? '' }} {{ $ticket->device->model ?? 'Dispositivo' }}
                                </h1>
                                <p class="text-white-50 mb-0 small">
                                    Cliente: <strong class="text-white">{{ $ticket->device->client->name ?? '' }} {{ $ticket->device->client->lastname ?? '' }}</strong>
                                    &bull; Tipo: <span class="text-white">{{ $ticket->device->deviceType->name ?? 'Equipo' }}</span>
                                </p>
                            </div>

                            <div class="col-12 col-md-4 text-md-end">
                                @php
                                    $badgeClasses = [
                                        'Recibido'    => 'bg-info text-dark',
                                        'Diagnóstico' => 'bg-warning text-dark',
                                        'Reparación'  => 'bg-primary text-white',
                                        'Finalizado'  => 'bg-success text-white',
                                        'Entregado'   => 'bg-secondary text-white',
                                    ];
                                    $currentBadge = $badgeClasses[$ticket->state] ?? 'bg-danger text-white';
                                @endphp
                                <span class="status-hero-badge {{ $currentBadge }}">
                                    <i class="bi bi-clock-history"></i>
                                    <span>Estado: {{ $ticket->state }}</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Stepper Interactivo del Ciclo de Reparación -->
                    <div class="mb-4">
                        <h2 class="h6 fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-bezier2 text-danger"></i>
                            <span>Progreso de la Orden de Reparación</span>
                        </h2>

                        <div class="stepper-wrapper">
                            @php
                                $progressPercent = ($currentStepIndex / (count($stages) - 1)) * 100;
                            @endphp
                            <div class="stepper-progress-bar d-none d-md-block">
                                <div class="stepper-progress-fill" style="width: {{ $progressPercent }}%;"></div>
                            </div>

                            @foreach($stages as $index => $stage)
                                @php
                                    $isCompleted = $index < $currentStepIndex;
                                    $isActive = $index === $currentStepIndex;
                                    $stepClass = $isCompleted ? 'completed' : ($isActive ? 'active' : '');
                                @endphp
                                <div class="step-item {{ $stepClass }}">
                                    <div class="step-circle">
                                        @if($isCompleted)
                                            <i class="bi bi-check-lg"></i>
                                        @else
                                            <i class="bi {{ $stage['icon'] }}"></i>
                                        @endif
                                    </div>
                                    <div class="step-title">{{ $stage['title'] }}</div>
                                    <div class="step-desc d-none d-md-block">{{ $stage['description'] }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Cuadrícula de Detalles -->
                    <div class="row g-3 g-md-4 mb-4">
                        <!-- Columna 1: Ficha del Dispositivo y Falla -->
                        <div class="col-12 col-lg-7">
                            <div class="info-section-card">
                                <div class="info-section-title">
                                    <i class="bi bi-phone text-danger"></i>
                                    <span>Datos Técnicos del Dispositivo</span>
                                </div>

                                <div class="info-row">
                                    <span class="info-row-label">Tipo de Equipo:</span>
                                    <span class="info-row-value">{{ $ticket->device->deviceType->name ?? 'Dispositivo' }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-row-label">Marca y Modelo:</span>
                                    <span class="info-row-value text-danger">{{ $ticket->device->brand ?? '' }} {{ $ticket->device->model ?? '' }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-row-label">Número de Serie / IMEI:</span>
                                    <span class="info-row-value font-monospace">{{ $ticket->device->serial_number ?: 'No registrado' }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-row-label">Especialista Asignado:</span>
                                    <span class="info-row-value">
                                        {{ $ticket->technician ? ($ticket->technician->name . ' ' . $ticket->technician->lastname) : 'En asignación de taller' }}
                                    </span>
                                </div>

                                <div class="mt-3 pt-3 border-top">
                                    <div class="small fw-bold text-dark mb-1">
                                        <i class="bi bi-exclamation-triangle text-warning me-1"></i>
                                        Falla Reportada al Ingreso:
                                    </div>
                                    <p class="small text-muted mb-0 bg-light p-2 rounded">
                                        {{ $ticket->reported_issue }}
                                    </p>
                                </div>

                                @if(!empty($ticket->technical_diagnosis))
                                <div class="mt-3 pt-3 border-top">
                                    <div class="small fw-bold text-success mb-1">
                                        <i class="bi bi-clipboard-check text-success me-1"></i>
                                        Diagnóstico Técnico Emitido:
                                    </div>
                                    <p class="small text-dark mb-0 bg-success-subtle p-2 rounded border border-success-subtle">
                                        {{ $ticket->technical_diagnosis }}
                                    </p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Columna 2: Resumen Económico y Código QR -->
                        <div class="col-12 col-lg-5">
                            <div class="financial-summary-card mb-3">
                                <div class="info-section-title text-danger mb-2">
                                    <i class="bi bi-cash-coin"></i>
                                    <span>Estado de Cuenta</span>
                                </div>

                                <div class="d-flex justify-content-between py-1 small">
                                    <span class="text-muted">Presupuesto del Trabajo:</span>
                                    <strong class="text-dark">Q {{ number_format((float) $ticket->total_charged, 2) }}</strong>
                                </div>
                                <div class="d-flex justify-content-between py-1 small">
                                    <span class="text-muted">Anticipo Abonado:</span>
                                    <strong class="text-success">- Q {{ number_format((float) $ticket->deposit, 2) }}</strong>
                                </div>
                                <div class="border-top border-danger-subtle pt-2 mt-2 d-flex justify-content-between align-items-center">
                                    <span class="fw-bold text-dark">Saldo Pendiente:</span>
                                    <span class="balance-amount">Q {{ number_format((float) $ticket->remaining_balance, 2) }}</span>
                                </div>
                                <div class="small text-muted mt-2">
                                    * El saldo restante se liquida al momento de retirar su equipo reparado.
                                </div>
                            </div>

                            <!-- Tarjeta de Código QR -->
                            <div class="info-section-card text-center">
                                <div class="mb-2">
                                    {!! $qrSvg !!}
                                </div>
                                <div class="small fw-bold text-dark">Código QR de Seguimiento</div>
                                <div class="text-muted" style="font-size: 0.75rem;">
                                    Escanea con tu celular o guarda este enlace para consultar actualizaciones posteriores.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de Acción Inferiores -->
                    <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center pt-3 border-top">
                        <span class="small text-muted">
                            <i class="bi bi-shield-check text-success me-1"></i>
                            Garantía de servicio técnico CelIx
                        </span>

                        <div class="d-flex gap-2">
                            <a href="{{ route('tickets.tracking.pdf', $ticket->qr_token) }}" class="btn-celix-primary">
                                <i class="bi bi-file-earmark-pdf"></i>
                                <span>Descargar Comprobante PDF</span>
                            </a>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </main>

    <!-- Footer Público -->
    <footer class="text-center py-4 text-muted small border-top bg-white mt-auto">
        <div class="container">
            <p class="mb-1">
                <strong>CelIx</strong> &bull; Centro de Reparaciones y Soluciones Tecnológicas &bull; Guatemala
            </p>
            <p class="mb-0 text-secondary" style="font-size: 0.75rem;">
                © {{ date('Y') }} CelIx. Todos los derechos reservados.
            </p>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
