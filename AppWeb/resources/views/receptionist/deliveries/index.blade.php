@extends('layouts.receptionist')

@section('title', 'Entregas de Dispositivos | CelIx')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/receptionist/deliveries.css') }}">
@endpush

@section('content')
    <div class="container-fluid px-0">
        <!-- Encabezado de la Página -->
        <div
            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">Control de Tickets y Entregas</h1>
                <p class="text-muted small mb-0">Consulta el estado de todos los tickets del taller y despacha los equipos
                    finalizados al cliente.</p>
            </div>

            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light text-secondary border px-3 py-2">
                    <i class="bi bi-clock-history me-1"></i>Fecha: {{ date('d/m/Y') }}
                </span>
            </div>
        </div>

        <!-- Tarjetas de Métricas Rápidas (KPIs) -->
        <div class="row g-3 mb-4">
            <!-- Card 1: Equipos en Taller / Proceso -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 bg-white p-3 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon-wrapper rounded-3 d-flex align-items-center justify-content-center icon-info">
                            <i class="bi bi-tools"></i>
                        </div>
                        <div>
                            <span class="text-muted small fw-medium d-block">En Taller / Proceso</span>
                            <h2 class="h4 fw-bold text-dark mb-0">{{ $kpis['in_workshop'] ?? 0 }}</h2>
                            <span
                                class="small text-muted">{{ ($kpis['in_workshop'] ?? 0) == 1 ? '1 equipo en revisión' : ($kpis['in_workshop'] ?? 0) . ' equipos en revisión' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Listos para Retiro -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 bg-white p-3 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div
                            class="stat-icon-wrapper rounded-3 d-flex align-items-center justify-content-center icon-warning">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <div>
                            <span class="text-muted small fw-medium d-block">Listos para Retiro</span>
                            <h2 class="h4 fw-bold text-dark mb-0">{{ $kpis['total_completed'] ?? 0 }}</h2>
                            <span
                                class="small text-muted">{{ ($kpis['total_completed'] ?? 0) == 1 ? '1 equipo finalizado' : ($kpis['total_completed'] ?? 0) . ' equipos finalizados' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 3: Saldo Total por Cobrar -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 bg-white p-3 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div
                            class="stat-icon-wrapper rounded-3 d-flex align-items-center justify-content-center icon-danger">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                        <div>
                            <span class="text-muted small fw-medium d-block">Saldo Total por Cobrar</span>
                            <h2 class="h4 fw-bold text-dark mb-0">
                                Q{{ number_format($kpis['total_pending_amount'] ?? 0, 2) }}</h2>
                            <span class="small text-muted">En equipos por entregar</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 4: Entregados Hoy -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 bg-white p-3 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div
                            class="stat-icon-wrapper rounded-3 d-flex align-items-center justify-content-center icon-success">
                            <i class="bi bi-box-arrow-right"></i>
                        </div>
                        <div>
                            <span class="text-muted small fw-medium d-block">Entregados Hoy</span>
                            <h2 class="h4 fw-bold text-dark mb-0">{{ $kpis['delivered_today'] ?? 0 }}</h2>
                            <span class="small text-muted">Histórico: {{ $kpis['total_delivered'] ?? 0 }} entregados</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenedor Principal: Tabla de Tickets Finalizados -->
        <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
            <div class="card-header bg-white border-bottom p-3 p-lg-4">
                <!-- Barra de Búsqueda y Filtros Server-Side -->
                <form id="deliveriesFilterForm" method="GET" action="{{ route('receptionist.deliveries') }}">
                    <div class="row g-3 align-items-center">
                        <!-- Buscador General -->
                        <div class="col-12 col-md-4">
                            <div class="input-group">
                                <span class="input-group-text bg-white text-muted">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" name="search" class="form-control"
                                    placeholder="Buscar por #Ticket, cliente, teléfono, marca o modelo..."
                                    value="{{ request('search') }}" aria-label="Buscar tickets">
                                @if(request('search'))
                                    <a href="{{ route('receptionist.deliveries') }}" class="btn btn-outline-secondary"
                                        title="Limpiar búsqueda">
                                        <i class="bi bi-x"></i>
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Filtro por Estado -->
                        <div class="col-12 col-sm-6 col-md-3">
                            <select name="state" id="deliveriesStateSelect" class="form-select"
                                aria-label="Filtrar por estado">
                                <option value="">Todos los Estados</option>
                                <option value="Recibido" {{ request('state') == 'Recibido' ? 'selected' : '' }}>Recibido
                                </option>
                                <option value="Diagnóstico" {{ request('state') == 'Diagnóstico' ? 'selected' : '' }}>En
                                    Diagnóstico</option>
                                <option value="Reparación" {{ request('state') == 'Reparación' ? 'selected' : '' }}>En
                                    Reparación</option>
                                <option value="Finalizado" {{ request('state') == 'Finalizado' ? 'selected' : '' }}>Finalizado
                                    (Listo para Entrega)</option>
                                <option value="Entregado" {{ request('state') == 'Entregado' ? 'selected' : '' }}>Entregado
                                </option>
                            </select>
                        </div>

                        <!-- Filtro por Rango de Fechas -->
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="input-group">
                                <span class="input-group-text bg-white text-muted small">Desde</span>
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                                <span class="input-group-text bg-white text-muted small">Hasta</span>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                        </div>

                        <!-- Registros por Página y Botón Filtrar -->
                        <div class="col-12 col-md-2">
                            <div class="d-flex align-items-center justify-content-md-end gap-2">
                                <select name="per_page" id="deliveriesPerPageSelect" class="form-select w-auto">
                                    <option value="5" {{ request('per_page', 10) == 5 ? 'selected' : '' }}>5</option>
                                    <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50</option>
                                </select>
                                <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1">
                                    <i class="bi bi-funnel"></i>
                                    <span>Filtrar</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-body p-0">
                <!-- Datatable de Tickets Finalizados -->
                <div class="table-responsive">
                    <table class="table deliveries-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th scope="col" class="text-center"># Folio</th>
                                <th scope="col">Fecha Ingreso</th>
                                <th scope="col">Cliente</th>
                                <th scope="col">Dispositivo</th>
                                <th scope="col">Diagnóstico / Trabajo Realizado</th>
                                <th scope="col" class="text-center">Estado</th>
                                <th scope="col" class="text-end">Saldo Pendiente</th>
                                <th scope="col" class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tickets as $ticket)
                                @php
                                    $client = $ticket->device->client ?? null;
                                    $clientName = $client ? "{$client->name} {$client->lastname}" : 'Cliente no disponible';
                                    $clientPhone = $client->phone ?? 'Sin teléfono';
                                    $device = $ticket->device;
                                    $deviceTypeName = $device->deviceType->name ?? 'Dispositivo';
                                    $deviceSummary = "{$deviceTypeName} - " . ($device->brand ?? '') . " " . ($device->model ?? '');
                                    $remaining = (float) $ticket->remaining_balance;
                                @endphp
                                <tr>
                                    <!-- Folio del Ticket -->
                                    <td class="text-center fw-bold text-dark">
                                        <span class="badge bg-light text-dark border">
                                            #TK-{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}
                                        </span>
                                    </td>

                                    <!-- Fecha de Ingreso -->
                                    <td class="text-muted small">
                                        <div class="d-flex align-items-center gap-1">
                                            <span>{{ $ticket->intake_date ? $ticket->intake_date->format('d/m/Y H:i') : 'N/A' }}</span>
                                        </div>
                                    </td>

                                    <!-- Cliente -->
                                    <td>
                                        <div>
                                            <span class="fw-bold text-dark d-block">{{ $clientName }}</span>
                                            <span class="small text-muted"> {{ $clientPhone }}
                                            </span>
                                        </div>
                                    </td>

                                    <!-- Dispositivo -->
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div>
                                                <span class="fw-semibold text-dark d-block">{{ $device->brand ?? 'N/A' }}
                                                    {{ $device->model ?? '' }}</span>
                                                <span class="small text-muted">{{ $deviceTypeName }} &bull; S/N:
                                                    {{ $device->serial_number ?? 'S/N' }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Diagnóstico Técnico -->
                                    <td>
                                        <span class="text-truncate d-inline-block text-dark small" style="max-width: 200px;"
                                            title="{{ $ticket->technical_diagnosis ?? $ticket->reported_issue }}">
                                            {{ $ticket->technical_diagnosis ?: $ticket->reported_issue }}
                                        </span>
                                        @if ($ticket->technician)
                                            <span class="small text-muted d-block">
                                                {{ $ticket->technician->name }}
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Estado -->
                                    <td class="text-center">
                                        @if ($ticket->state === 'Recibido')
                                            <span class="badge badge-state-recibido">
                                                <i class="bi bi-inbox me-1"></i>Recibido
                                            </span>
                                        @elseif ($ticket->state === 'Diagnóstico')
                                            <span class="badge badge-state-diagnostico">
                                                <i class="bi bi-tools me-1"></i>Diagnóstico
                                            </span>
                                        @elseif ($ticket->state === 'Reparación')
                                            <span class="badge badge-state-reparacion">
                                                <i class="bi bi-gear-wide-connected me-1"></i>Reparación
                                            </span>
                                        @elseif ($ticket->state === 'Finalizado')
                                            <span class="badge badge-state-finalizado">
                                                <i class="bi bi-check-circle me-1"></i>Finalizado
                                            </span>
                                        @elseif ($ticket->state === 'Entregado')
                                            <span class="badge badge-state-entregado">
                                                <i class="bi bi-check2-all me-1"></i>Entregado
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                {{ $ticket->state }}
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Saldo Pendiente -->
                                    <td class="text-end">
                                        @if ($remaining > 0)
                                            <span class="badge badge-balance-pending">
                                                Q{{ number_format($remaining, 2) }}
                                            </span>
                                        @else
                                            <span class="badge badge-balance-paid">
                                                <i class="bi bi-check2 me-1"></i>Liquidado
                                            </span>
                                        @endif
                                        <div class="small text-muted mt-1">
                                            Total: Q{{ number_format((float) $ticket->total_charged, 2) }}
                                        </div>
                                    </td>

                                    <!-- Acciones -->
                                    <td class="text-end">
                                        <div class="d-inline-flex align-items-center gap-1">
                                            <!-- Botón Re-descargar Código QR (PDF) -->
                                            <a href="{{ route('receptionist.tickets.pdf', $ticket->id) }}"
                                                class="btn btn-outline-danger btn-action-icon"
                                                title="Re-descargar Ticket con Código QR (PDF)" target="_blank">
                                                <i class="bi bi-qr-code"></i>
                                            </a>

                                            <!-- Botón Ver Ficha Detallada -->
                                            <button type="button"
                                                class="btn btn-outline-secondary btn-action-icon btn-view-ticket"
                                                title="Ver detalles del ticket y dispositivo" data-bs-toggle="modal"
                                                data-bs-target="#viewTicketModal" data-id="{{ $ticket->id }}"
                                                data-client-name="{{ $clientName }}" data-client-phone="{{ $clientPhone }}"
                                                data-client-dpi="{{ $client->dpi ?? 'No registrado' }}"
                                                data-device-summary="{{ $deviceSummary }}"
                                                data-device-serial="{{ $device->serial_number ?? 'No registrado' }}"
                                                data-technician-name="{{ $ticket->technician ? $ticket->technician->name . ' ' . $ticket->technician->lastname : 'Sin técnico asignado' }}"
                                                data-intake-date="{{ $ticket->intake_date ? $ticket->intake_date->format('d/m/Y H:i') : 'N/A' }}"
                                                data-reported-issue="{{ $ticket->reported_issue }}"
                                                data-technical-diagnosis="{{ $ticket->technical_diagnosis ?: 'Reparación completada sin observaciones técnicas.' }}"
                                                data-total-charged="{{ $ticket->total_charged }}"
                                                data-deposit="{{ $ticket->deposit }}"
                                                data-remaining-balance="{{ $ticket->remaining_balance }}">
                                                <i class="bi bi-eye"></i>
                                            </button>

                                            <!-- Botón Entregar Dispositivo (SOLO permitido para tickets en estado 'Finalizado') -->
                                            @if ($ticket->state === 'Finalizado')
                                                <button type="button"
                                                    class="btn btn-primary d-inline-flex align-items-center gap-1 px-3 py-1 btn-deliver-ticket"
                                                    title="Entregar equipo al cliente y liquidar saldo" data-bs-toggle="modal"
                                                    data-bs-target="#deliverTicketModal" data-id="{{ $ticket->id }}"
                                                    data-client-name="{{ $clientName }}" data-client-phone="{{ $clientPhone }}"
                                                    data-device-summary="{{ $deviceSummary }}"
                                                    data-reported-issue="{{ $ticket->reported_issue }}"
                                                    data-technical-diagnosis="{{ $ticket->technical_diagnosis ?: 'Reparación completada.' }}"
                                                    data-total-charged="{{ $ticket->total_charged }}"
                                                    data-deposit="{{ $ticket->deposit }}"
                                                    data-remaining-balance="{{ $ticket->remaining_balance }}"
                                                    data-deliver-url="{{ route('receptionist.deliveries.process', $ticket->id) }}">
                                                    <i class="bi bi-box-arrow-right"></i>
                                                    <span>Entregar</span>
                                                </button>
                                            @elseif ($ticket->state === 'Entregado')
                                                <button type="button"
                                                    class="btn btn-outline-secondary d-inline-flex align-items-center gap-1 px-2 py-1"
                                                    disabled title="Este equipo ya fue entregado al cliente">
                                                    <i class="bi bi-check2-all"></i>
                                                    <span class="small">Entregado</span>
                                                </button>
                                            @else
                                                <button type="button"
                                                    class="btn btn-light text-muted border d-inline-flex align-items-center gap-1 px-2 py-1"
                                                    disabled title="Solo los tickets con estado Finalizado pueden ser entregados">
                                                    <i class="bi bi-hourglass-split"></i>
                                                    <span class="small">En Taller</span>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center justify-content-center text-muted">
                                            <i class="bi bi-inbox fs-1 mb-2 text-secondary"></i>
                                            <span class="fw-semibold">No se encontraron tickets de servicio</span>
                                            <p class="small text-muted mb-3">
                                                @if(request('search') || request('state') || request('date_from') || request('date_to'))
                                                    No se encontraron coincidencias con los filtros aplicados.
                                                @else
                                                    Los tickets registrados en el sistema aparecerán en esta lista para seguimiento
                                                    y entrega.
                                                @endif
                                            </p>
                                            @if(request('search') || request('state') || request('date_from') || request('date_to'))
                                                <a href="{{ route('receptionist.deliveries') }}"
                                                    class="btn btn-sm btn-outline-secondary">
                                                    <i class="bi bi-arrow-clockwise me-1"></i>Limpiar Filtros
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación de la Tabla -->
                @if ($tickets->hasPages())
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 p-3 border-top">
                        <span class="small text-muted">
                            Mostrando del {{ $tickets->firstItem() }} al {{ $tickets->lastItem() }} de {{ $tickets->total() }}
                            tickets
                        </span>
                        <div>
                            {{ $tickets->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- ==========================================================================
                             MODALES DEL MÓDULO DE ENTREGAS
                             ========================================================================== -->

    <!-- 1. Modal: Entregar Dispositivo y Liquidar Saldo -->
    <div class="modal fade" id="deliverTicketModal" tabindex="-1" aria-labelledby="deliverTicketModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header modal-header-custom bg-light">
                    <div class="d-flex align-items-center gap-2">
                        <div
                            class="rounded-circle p-2 bg-white text-primary border shadow-sm d-flex align-items-center justify-content-center">
                            <i class="bi bi-box2-check-fill fs-5"></i>
                        </div>
                        <div>
                            <h2 class="modal-title h5 fw-bold text-dark mb-0" id="deliverTicketModalLabel">
                                Entregar Dispositivo &bull; <span id="deliverTicketFolioText"
                                    class="text-primary">#TK-0000</span>
                            </h2>
                            <span class="small text-muted">Confirma la entrega al cliente y registra la liquidación contable
                                en caja</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <form id="deliverTicketForm" method="POST">
                    @csrf
                    <div class="modal-body modal-body-custom">
                        <!-- Resumen del Ticket y Dispositivo -->
                        <div class="row g-3 mb-4">
                            <div class="col-12 col-md-6">
                                <div class="p-3 bg-light rounded-3 border h-100">
                                    <span class="small fw-bold text-secondary text-uppercase d-block mb-2">Información del
                                        Cliente</span>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <i class="bi bi-person-circle text-primary"></i>
                                        <strong class="text-dark" id="deliverClientNameText">---</strong>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 small text-muted">
                                        <i class="bi bi-telephone text-primary"></i>
                                        <span id="deliverClientPhoneText">---</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="p-3 bg-light rounded-3 border h-100">
                                    <span class="small fw-bold text-secondary text-uppercase d-block mb-2">Dispositivo
                                        Reparado</span>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <strong class="text-dark" id="deliverDeviceText">---</strong>
                                    </div>
                                    <div class="small text-muted">
                                        <span class="fw-medium">Diagnóstico:</span> <span
                                            id="deliverDiagnosisText">---</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Desglose Económico -->
                        <div class="p-3 rounded-3 breakdown-card mb-4">
                            <span class="small fw-bold text-secondary text-uppercase d-block mb-3">Estado de Cobro /
                                Liquidación</span>
                            <div class="row text-center g-2">
                                <div class="col-4">
                                    <span class="small text-muted d-block">Precio Total</span>
                                    <span class="h6 fw-bold text-dark mb-0" id="deliverTotalChargedSpan">Q0.00</span>
                                </div>
                                <div class="col-4 border-start border-end">
                                    <span class="small text-muted d-block">Anticipo Pagado</span>
                                    <span class="h6 fw-bold text-success mb-0" id="deliverDepositSpan">Q0.00</span>
                                </div>
                                <div class="col-4">
                                    <span class="small text-muted d-block">Saldo Pendiente</span>
                                    <span class="h5 fw-bold breakdown-value-pending mb-0"
                                        id="deliverRemainingBalanceSpan">Q0.00</span>
                                </div>
                            </div>
                        </div>

                        <!-- Formulario de Entrega y Transacción Financiera -->
                        <div class="row g-3">
                            <!-- Fecha y Hora de Entrega -->
                            <div class="col-12 col-md-6">
                                <label for="deliver_return_date" class="form-label fw-medium small">
                                    Fecha y Hora de Entrega <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted">
                                        <i class="bi bi-calendar-check"></i>
                                    </span>
                                    <input type="datetime-local" name="return_date" id="deliver_return_date"
                                        class="form-control" required>
                                </div>
                            </div>

                            <!-- Monto Liquidado / Cobrado -->
                            <div class="col-12 col-md-6">
                                <label for="deliver_amount_to_pay" class="form-label fw-medium small">
                                    Monto a Cobrar al Entregar (Q) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted fw-bold">Q</span>
                                    <input type="number" step="0.01" min="0" name="amount_to_pay" id="deliver_amount_to_pay"
                                        class="form-control fw-bold" required>
                                </div>
                            </div>

                            <!-- Método de Pago -->
                            <div class="col-12 col-md-6">
                                <label for="deliver_payment_method" class="form-label fw-medium small">
                                    Método de Pago <span class="text-danger">*</span>
                                </label>
                                <select name="payment_method" id="deliver_payment_method" class="form-select" required>
                                    <option value="EFECTIVO" selected>Efectivo</option>
                                    <option value="TARJETA">Tarjeta de Débito / Crédito</option>
                                    <option value="TRANSFERENCIA">Transferencia Bancaria</option>
                                </select>
                            </div>

                            <!-- Observaciones / Notas de Entrega -->
                            <div class="col-12 col-md-6">
                                <label for="deliver_notes" class="form-label fw-medium small">
                                    Observaciones de Entrega <span class="text-muted small">(Opcional)</span>
                                </label>
                                <input type="text" name="delivery_notes" id="deliver_notes" class="form-control"
                                    placeholder="Ej: Equipo probado y entregado con cargador original">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer modal-footer-custom bg-light">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2 px-4"
                            id="btnConfirmDelivery">
                            <i class="bi bi-check2-circle"></i>
                            <span>Confirmar Entrega y Registrar Cobro</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 2. Modal: Ver Ficha Completa del Ticket -->
    <div class="modal fade" id="viewTicketModal" tabindex="-1" aria-labelledby="viewTicketModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header modal-header-custom bg-light">
                    <div class="d-flex align-items-center gap-2">
                        <div
                            class="rounded-circle p-2 bg-white text-secondary border shadow-sm d-flex align-items-center justify-content-center">
                            <i class="bi bi-file-earmark-text-fill fs-5"></i>
                        </div>
                        <div>
                            <h2 class="modal-title h5 fw-bold text-dark mb-0" id="viewTicketModalLabel">
                                Ficha Técnica &bull; <span id="viewTicketFolio" class="text-primary">#TK-0000</span>
                            </h2>
                            <span class="small text-muted">Detalles completos del servicio técnico y diagnóstico</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body modal-body-custom">
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-6">
                            <label class="form-label small text-muted mb-1 fw-medium">Cliente</label>
                            <div class="p-2 bg-light rounded border text-dark fw-bold" id="viewClientName">---</div>
                        </div>

                        <div class="col-12 col-md-3">
                            <label class="form-label small text-muted mb-1 fw-medium">Teléfono</label>
                            <div class="p-2 bg-light rounded border text-dark" id="viewClientPhone">---</div>
                        </div>

                        <div class="col-12 col-md-3">
                            <label class="form-label small text-muted mb-1 fw-medium">DPI</label>
                            <div class="p-2 bg-light rounded border text-dark" id="viewClientDpi">---</div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label small text-muted mb-1 fw-medium">Dispositivo</label>
                            <div class="p-2 bg-light rounded border text-dark fw-semibold" id="viewDeviceSummary">---</div>
                        </div>

                        <div class="col-12 col-md-3">
                            <label class="form-label small text-muted mb-1 fw-medium">Número de Serie / IMEI</label>
                            <div class="p-2 bg-light rounded border text-dark" id="viewDeviceSerial">---</div>
                        </div>

                        <div class="col-12 col-md-3">
                            <label class="form-label small text-muted mb-1 fw-medium">Fecha de Ingreso</label>
                            <div class="p-2 bg-light rounded border text-dark" id="viewIntakeDate">---</div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label small text-muted mb-1 fw-medium">Falla Reportada</label>
                            <div class="p-2 bg-light rounded border text-dark" id="viewReportedIssue">---</div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label small text-muted mb-1 fw-medium">Técnico Asignado</label>
                            <div class="p-2 bg-light rounded border text-dark fw-medium" id="viewTechnicianName">---</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label small text-muted mb-1 fw-medium">Diagnóstico y Solución Técnica</label>
                            <div class="p-3 bg-light rounded border text-dark" id="viewTechnicalDiagnosis">---</div>
                        </div>
                    </div>

                    <!-- Desglose de Precios -->
                    <div class="p-3 bg-light rounded-3 border">
                        <span class="small fw-bold text-secondary text-uppercase d-block mb-2">Resumen Financiero</span>
                        <div class="row text-center g-2">
                            <div class="col-4">
                                <span class="small text-muted d-block">Precio Total Pactado</span>
                                <span class="h6 fw-bold text-dark" id="viewTotalCharged">Q0.00</span>
                            </div>
                            <div class="col-4 border-start border-end">
                                <span class="small text-muted d-block">Anticipo Entregado</span>
                                <span class="h6 fw-bold text-success" id="viewDeposit">Q0.00</span>
                            </div>
                            <div class="col-4">
                                <span class="small text-muted d-block">Saldo Pendiente</span>
                                <span class="h6 fw-bold text-danger" id="viewRemainingBalance">Q0.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer modal-footer-custom bg-light">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/receptionist/deliveries.js') }}"></script>
@endpush