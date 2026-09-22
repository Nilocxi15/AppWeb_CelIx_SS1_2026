@extends('layouts.technician')

@section('title', 'Taller Técnico | CelIx')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/technician/home.css') }}">
@endpush

@section('content')
<div class="container-fluid px-0">
    <!-- Encabezado de la Página -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            @if ($user->hasRole('ADMINISTRADOR'))
                <h1 class="h3 fw-bold text-dark mb-1">Taller Técnico &bull; Gestión Integral</h1>
                <p class="text-muted mb-0 small">
                    Supervisa todos los tickets del taller, revisa los avances técnicos y monitorea la carga de los especialistas.
                </p>
            @else
                <h1 class="h3 fw-bold text-dark mb-1">Taller Técnico &bull; Mis Tickets de Trabajo</h1>
                <p class="text-muted mb-0 small">
                    Diagnostica, repara y finaliza los dispositivos que se te han asignado. Registra notas técnicas de seguimiento.
                </p>
            @endif
        </div>

        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-light text-secondary border px-3 py-2">
                <i class="bi bi-clock-history me-1"></i>Fecha: {{ date('d/m/Y') }}
            </span>
        </div>
    </div>

    <!-- Tarjetas de Métricas Rápidas (KPIs) -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 tech-stat-card h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon-wrapper rounded-3 d-flex align-items-center justify-content-center icon-primary">
                        <i class="bi bi-tools"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium d-block">Tickets Activos en Taller</span>
                        <h2 class="h4 fw-bold text-dark mb-0">{{ $kpis['total_active'] ?? 0 }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 tech-stat-card h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon-wrapper rounded-3 d-flex align-items-center justify-content-center icon-warning">
                        <i class="bi bi-search"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium d-block">En Diagnóstico</span>
                        <h2 class="h4 fw-bold text-dark mb-0">{{ $kpis['in_diagnostic'] ?? 0 }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 tech-stat-card h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon-wrapper rounded-3 d-flex align-items-center justify-content-center icon-info">
                        <i class="bi bi-wrench-adjustable"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium d-block">En Reparación</span>
                        <h2 class="h4 fw-bold text-dark mb-0">{{ $kpis['in_repair'] ?? 0 }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 tech-stat-card h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon-wrapper rounded-3 d-flex align-items-center justify-content-center icon-success">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium d-block">Finalizados (Listos para Entrega)</span>
                        <h2 class="h4 fw-bold text-dark mb-0">{{ $kpis['completed'] ?? 0 }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenedor de la Tabla de Tickets -->
    <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
        <div class="card-header bg-white border-bottom p-3 p-lg-4">
            <form id="techTicketsFilterForm" method="GET" action="{{ route('technician.home') }}">
                <div class="row g-3 align-items-center">
                    <!-- Buscador General -->
                    <div class="col-12 col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" name="search" class="form-control"
                                placeholder="Buscar por #Ticket, cliente, marca, modelo o falla..."
                                value="{{ request('search') }}" aria-label="Buscar tickets">
                            @if(request('search'))
                                <a href="{{ route('technician.home') }}" class="btn btn-outline-secondary" title="Limpiar búsqueda">
                                    <i class="bi bi-x"></i>
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Filtro por Estado -->
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="d-flex align-items-center gap-2">
                            <label for="techStateFilter" class="form-label small text-muted mb-0 text-nowrap">Estado:</label>
                            <select name="state" id="techStateFilter" class="form-select">
                                <option value="">Todos los estados</option>
                                <option value="Recibido" {{ request('state') === 'Recibido' ? 'selected' : '' }}>Recibido</option>
                                <option value="Diagnóstico" {{ request('state') === 'Diagnóstico' ? 'selected' : '' }}>Diagnóstico</option>
                                <option value="Reparación" {{ request('state') === 'Reparación' ? 'selected' : '' }}>Reparación</option>
                                <option value="Finalizado" {{ request('state') === 'Finalizado' ? 'selected' : '' }}>Finalizado</option>
                                <option value="Entregado" {{ request('state') === 'Entregado' ? 'selected' : '' }}>Entregado</option>
                            </select>
                        </div>
                    </div>

                    <!-- Filtro por Técnico (Solo para Administrador) -->
                    @if ($user->hasRole('ADMINISTRADOR'))
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="d-flex align-items-center gap-2">
                                <label for="techTechnicianFilter" class="form-label small text-muted mb-0 text-nowrap">Técnico:</label>
                                <select name="technician_id" id="techTechnicianFilter" class="form-select">
                                    <option value="">Todos los técnicos</option>
                                    @foreach($technicians as $tech)
                                        <option value="{{ $tech->id }}" {{ request('technician_id') == $tech->id ? 'selected' : '' }}>
                                            {{ $tech->name }} {{ $tech->lastname }} (&#64;{{ $tech->username }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endif

                    <!-- Registros por Página -->
                    <div class="col-12 col-sm-6 {{ $user->hasRole('ADMINISTRADOR') ? 'col-md-2' : 'col-md-5' }}">
                        <div class="d-flex align-items-center justify-content-sm-end gap-2">
                            <label for="techPerPageSelect" class="form-label small text-muted mb-0 text-nowrap">Mostrar:</label>
                            <select name="per_page" id="techPerPageSelect" class="form-select w-auto">
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
            <div class="table-responsive">
                <table class="table tech-table mb-0 align-middle">
                    <thead>
                        <tr>
                            <th scope="col" class="text-center"># Folio</th>
                            <th scope="col">Fecha Ingreso</th>
                            <th scope="col">Cliente</th>
                            <th scope="col">Dispositivo</th>
                            <th scope="col">Falla Reportada / Diagnóstico</th>
                            <th scope="col" class="text-center">Estado Actual</th>
                            @if ($user->hasRole('ADMINISTRADOR'))
                                <th scope="col">Técnico Asignado</th>
                            @endif
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
                                
                                // Determinar estados válidos hacia adelante según regla de avance irreversible
                                $allowedNextStates = match($ticket->state) {
                                    'Recibido'    => ['Diagnóstico', 'Reparación', 'Finalizado'],
                                    'Diagnóstico' => ['Reparación', 'Finalizado'],
                                    'Reparación'  => ['Finalizado'],
                                    default       => [],
                                };

                                $canChangeState = !empty($allowedNextStates);
                                $stateClass = match($ticket->state) {
                                    'Recibido'    => 'badge-state-recibido',
                                    'Diagnóstico' => 'badge-state-diagnostico',
                                    'Reparación'  => 'badge-state-reparacion',
                                    'Finalizado'  => 'badge-state-finalizado',
                                    default       => 'badge-state-entregado',
                                };
                            @endphp
                            <tr>
                                <!-- Folio -->
                                <td class="text-center fw-bold text-dark">
                                    <span class="badge bg-light text-dark border">
                                        #TK-{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}
                                    </span>
                                </td>

                                <!-- Fecha -->
                                <td class="text-muted small">
                                    <div class="d-flex align-items-center gap-1">                                        
                                        <span>{{ $ticket->intake_date ? $ticket->intake_date->format('d/m/Y H:i') : 'N/A' }}</span>
                                    </div>
                                </td>

                                <!-- Cliente -->
                                <td>
                                    <span class="fw-bold text-dark d-block">{{ $clientName }}</span>
                                    <span class="small text-muted">{{ $clientPhone }}</span>
                                </td>

                                <!-- Dispositivo -->
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div>
                                            <span class="fw-semibold text-dark d-block">{{ $device->brand ?? 'N/A' }} {{ $device->model ?? '' }}</span>
                                            <span class="small text-muted">{{ $deviceTypeName }} &bull; S/N: {{ $device->serial_number ?? 'S/N' }}</span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Falla y Diagnóstico -->
                                <td>
                                    <div class="small text-dark fw-medium text-truncate tech-table-truncate" title="{{ $ticket->reported_issue }}">
                                        <span class="text-muted fw-normal">Falla:</span> {{ $ticket->reported_issue }}
                                    </div>
                                    @if ($ticket->technical_diagnosis)
                                        <div class="small text-success text-truncate tech-table-truncate mt-1" title="{{ $ticket->technical_diagnosis }}">
                                            <i class="bi bi-check2 me-1"></i>{{ $ticket->technical_diagnosis }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Estado Actual -->
                                <td class="text-center">
                                    <span class="badge {{ $stateClass }}">
                                        <i class="bi bi-circle-fill me-1 badge-dot"></i>{{ $ticket->state }}
                                    </span>
                                </td>

                                <!-- Técnico Asignado (Visible si es Admin) -->
                                @if ($user->hasRole('ADMINISTRADOR'))
                                    <td class="small">
                                        @if ($ticket->technician)
                                            <span class="fw-semibold text-dark d-block">{{ $ticket->technician->name }} {{ $ticket->technician->lastname }}</span>
                                            <span class="text-muted">&#64;{{ $ticket->technician->username }}</span>
                                        @else
                                            <span class="badge bg-light text-muted border">Sin asignar</span>
                                        @endif
                                    </td>
                                @endif

                                <!-- Acciones -->
                                <td class="text-end">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <!-- Botón Cambiar Estado -->
                                        @if ($canChangeState)
                                            <button type="button" class="btn btn-outline-primary btn-action-icon btn-change-state"
                                                title="Avanzar etapa del ticket"
                                                data-bs-toggle="modal" data-bs-target="#changeStateModal"
                                                data-id="{{ $ticket->id }}"
                                                data-current-state="{{ $ticket->state }}"
                                                data-allowed-states="{{ json_encode($allowedNextStates) }}"
                                                data-technical-diagnosis="{{ $ticket->technical_diagnosis }}"
                                                data-device-summary="{{ $deviceSummary }}"
                                                data-update-url="{{ route('technician.tickets.state', $ticket->id) }}">
                                                <i class="bi bi-arrow-right-circle"></i>
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-light btn-action-icon text-muted" disabled
                                                title="Este ticket está en su etapa final de taller o ya fue entregado">
                                                <i class="bi bi-check-all"></i>
                                            </button>
                                        @endif

                                        <!-- Botón Bitácora de Notas -->
                                        <button type="button" class="btn btn-outline-secondary btn-action-icon btn-ticket-notes"
                                            title="Ver o agregar notas técnicas de seguimiento"
                                            data-bs-toggle="modal" data-bs-target="#ticketNotesModal"
                                            data-id="{{ $ticket->id }}"
                                            data-device-summary="{{ $deviceSummary }}"
                                            data-notes-url="{{ route('technician.tickets.notes.list', $ticket->id) }}"
                                            data-store-note-url="{{ route('technician.tickets.notes.store', $ticket->id) }}">
                                            <i class="bi bi-chat-left-text"></i>
                                            <span class="badge bg-secondary action-count-badge">{{ $ticket->notes->count() }}</span>
                                        </button>

                                        <!-- Botón Ver Ficha Completa -->
                                        <button type="button" class="btn btn-outline-secondary btn-action-icon btn-view-ticket"
                                            title="Ver ficha técnica completa"
                                            data-bs-toggle="modal" data-bs-target="#viewTicketModal"
                                            data-id="{{ $ticket->id }}"
                                            data-client-name="{{ $clientName }}"
                                            data-client-phone="{{ $clientPhone }}"
                                            data-device-summary="{{ $deviceSummary }}"
                                            data-device-serial="{{ $device->serial_number ?? 'No registrado' }}"
                                            data-device-password="{{ $ticket->device_password ?: 'Sin contraseña' }}"
                                            data-technician-name="{{ $ticket->technician ? $ticket->technician->name . ' ' . $ticket->technician->lastname : 'Sin técnico' }}"
                                            data-intake-date="{{ $ticket->intake_date ? $ticket->intake_date->format('d/m/Y H:i') : 'N/A' }}"
                                            data-reported-issue="{{ $ticket->reported_issue }}"
                                            data-reception-notes="{{ $ticket->reception_notes ?: 'Ninguna' }}"
                                            data-technical-diagnosis="{{ $ticket->technical_diagnosis ?: 'Diagnóstico en proceso' }}"
                                            data-total-charged="{{ $ticket->total_charged }}"
                                            data-deposit="{{ $ticket->deposit }}"
                                            data-remaining-balance="{{ $ticket->remaining_balance }}">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $user->hasRole('ADMINISTRADOR') ? 8 : 7 }}" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center text-muted">
                                        <i class="bi bi-inbox fs-1 mb-2 text-secondary"></i>
                                        <span class="fw-semibold">No se encontraron tickets de trabajo</span>
                                        <p class="small text-muted mb-3">
                                            @if(request('search') || request('state') || request('technician_id'))
                                                No hay coincidencias con los filtros aplicados.
                                            @else
                                                Los nuevos dispositivos recibidos en recepción asignados a tu cuenta aparecerán en esta lista.
                                            @endif
                                        </p>
                                        @if(request('search') || request('state') || request('technician_id'))
                                            <a href="{{ route('technician.home') }}" class="btn btn-sm btn-outline-secondary">
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

            <!-- Paginación -->
            @if ($tickets->hasPages())
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 p-3 border-top">
                    <span class="small text-muted">
                        Mostrando del {{ $tickets->firstItem() }} al {{ $tickets->lastItem() }} de {{ $tickets->total() }} tickets
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
     MODALES DEL PANEL TÉCNICO
     ========================================================================== -->

<!-- 1. Modal: Cambiar Estado Técnico (Flujo Unidireccional Irreversible) -->
<div class="modal fade" id="changeStateModal" tabindex="-1" aria-labelledby="changeStateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header modal-header-custom bg-light">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle p-2 bg-white text-primary border shadow-sm d-flex align-items-center justify-content-center">
                        <i class="bi bi-arrow-right-circle-fill fs-5"></i>
                    </div>
                    <div>
                        <h2 class="modal-title h5 fw-bold text-dark mb-0" id="changeStateModalLabel">
                            Avanzar Etapa &bull; <span id="stateModalFolioText" class="text-primary">#TK-0000</span>
                        </h2>
                        <span class="small text-muted" id="stateModalDeviceText">Dispositivo</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form id="changeStateForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body modal-body-custom">
                    <!-- Estado Actual y Regla de No Retroceso -->
                    <div class="state-step-box mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="small fw-semibold text-muted">Estado Actual:</span>
                            <span id="stateModalCurrentBadge" class="badge">---</span>
                        </div>
                        <div class="alert alert-light border mb-0 py-2 small text-muted">
                            <i class="bi bi-shield-lock me-1 text-primary"></i>
                            <strong>Regla de avance:</strong> Una vez avanzado el estado de trabajo, este es irreversible y no se permite retroceder a etapas anteriores.
                        </div>
                    </div>

                    <!-- Aviso si no hay más estados disponibles -->
                    <div id="noMoreStatesNotice" class="alert alert-info py-2 small d-none mb-3">
                        <i class="bi bi-info-circle me-1"></i>Este ticket se encuentra en estado <strong>Finalizado</strong>. La entrega al cliente y el cierre contable es gestionado por Recepción.
                    </div>

                    <!-- Selector de Siguiente Estado -->
                    <div class="mb-3" id="stateSelectContainer">
                        <label for="new_state" class="form-label fw-medium small">
                            Siguiente Etapa del Flujo Técnico <span class="text-danger">*</span>
                        </label>
                        <select name="state" id="new_state" class="form-select" required>
                            <!-- Opciones cargadas dinámicamente -->
                        </select>
                    </div>

                    <!-- Diagnóstico Técnico / Solución -->
                    <div class="mb-3">
                        <label for="state_technical_diagnosis" class="form-label fw-medium small">
                            Diagnóstico y Solución Técnica Realizada
                        </label>
                        <textarea name="technical_diagnosis" id="state_technical_diagnosis" class="form-control" rows="3"
                            placeholder="Describe las reparaciones efectuadas, piezas cambiadas o pruebas de funcionamiento realizadas..."></textarea>
                    </div>
                </div>

                <div class="modal-footer modal-footer-custom bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2 px-4" id="btnSubmitChangeState">
                        <i class="bi bi-check-lg"></i>
                        <span>Actualizar Estado</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 2. Modal: Bitácora de Notas Técnicas de Seguimiento -->
<div class="modal fade" id="ticketNotesModal" tabindex="-1" aria-labelledby="ticketNotesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header modal-header-custom bg-light">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle p-2 bg-white text-secondary border shadow-sm d-flex align-items-center justify-content-center">
                        <i class="bi bi-chat-left-dots-fill fs-5"></i>
                    </div>
                    <div>
                        <h2 class="modal-title h5 fw-bold text-dark mb-0" id="ticketNotesModalLabel">
                            Bitácora de Notas &bull; <span id="notesModalFolioText" class="text-primary">#TK-0000</span>
                        </h2>
                        <span class="small text-muted" id="notesModalDeviceText">Dispositivo</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body modal-body-custom">
                <!-- Formulario para Agregar Nota Rápida -->
                <form id="addNoteForm" class="mb-4">
                    @csrf
                    <label for="note_text_input" class="form-label fw-medium small text-dark mb-1">
                        Agregar Nueva Nota de Seguimiento
                    </label>
                    <div class="input-group">
                        <textarea name="note" id="note_text_input" class="form-control" rows="2"
                            placeholder="Escribe una observación sobre el estado de la reparación, piezas faltantes, contacto con cliente..." required minlength="3"></textarea>
                        <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1 px-3" id="btnSubmitNote">
                            <i class="bi bi-send"></i>
                            <span>Agregar Nota</span>
                        </button>
                    </div>
                </form>

                <!-- Historial de Notas -->
                <span class="small fw-bold text-secondary text-uppercase d-block mb-2">Historial de Observaciones</span>
                <div class="notes-timeline-container" id="notesTimelineContainer">
                    <!-- Notas cargadas dinámicamente vía AJAX -->
                </div>
            </div>

            <div class="modal-footer modal-footer-custom bg-light">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- 3. Modal: Ver Ficha Técnica Completa -->
<div class="modal fade" id="viewTicketModal" tabindex="-1" aria-labelledby="viewTicketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header modal-header-custom bg-light">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle p-2 bg-white text-secondary border shadow-sm d-flex align-items-center justify-content-center">
                        <i class="bi bi-file-earmark-text-fill fs-5"></i>
                    </div>
                    <div>
                        <h2 class="modal-title h5 fw-bold text-dark mb-0" id="viewTicketModalLabel">
                            Ficha Técnica &bull; <span id="viewTicketFolio" class="text-primary">#TK-0000</span>
                        </h2>
                        <span class="small text-muted">Información detallada para el taller técnico</span>
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

                    <div class="col-12 col-md-6">
                        <label class="form-label small text-muted mb-1 fw-medium">Teléfono de Contacto</label>
                        <div class="p-2 bg-light rounded border text-dark" id="viewClientPhone">---</div>
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
                        <label class="form-label small text-danger mb-1 fw-medium">PIN / Patrón de Desbloqueo</label>
                        <div class="p-2 bg-light rounded border text-danger fw-bold" id="viewDevicePassword">---</div>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label small text-muted mb-1 fw-medium">Fecha de Ingreso</label>
                        <div class="p-2 bg-light rounded border text-dark" id="viewIntakeDate">---</div>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label small text-muted mb-1 fw-medium">Técnico Asignado</label>
                        <div class="p-2 bg-light rounded border text-dark fw-medium" id="viewTechnicianName">---</div>
                    </div>

                    <div class="col-12">
                        <label class="form-label small text-muted mb-1 fw-medium">Falla Reportada por el Cliente</label>
                        <div class="p-2 bg-light rounded border text-dark" id="viewReportedIssue">---</div>
                    </div>

                    <div class="col-12">
                        <label class="form-label small text-muted mb-1 fw-medium">Observaciones Iniciales de Recepción (Golpes, Estética)</label>
                        <div class="p-2 bg-light rounded border text-dark" id="viewReceptionNotes">---</div>
                    </div>

                    <div class="col-12">
                        <label class="form-label small text-muted mb-1 fw-medium">Diagnóstico Técnico y Solución</label>
                        <div class="p-3 bg-light rounded border text-dark fw-medium" id="viewTechnicalDiagnosis">---</div>
                    </div>
                </div>

                <!-- Resumen Económico -->
                <div class="p-3 bg-light rounded-3 border">
                    <span class="small fw-bold text-secondary text-uppercase d-block mb-2">Condiciones Económicas</span>
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
                            <span class="small text-muted d-block">Saldo Pendiente al Entregar</span>
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
    <script src="{{ asset('js/technician/home.js') }}"></script>
@endpush
