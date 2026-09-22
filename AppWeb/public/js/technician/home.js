// ==========================================================================
// Lógica de Frontend para el Panel de Trabajo del Técnico (CelIx)
// ==========================================================================

document.addEventListener('DOMContentLoaded', function () {
    // --------------------------------------------------------------------------
    // 1. Modal: Cambiar Estado Técnico del Ticket (Flujo Irreversible)
    // --------------------------------------------------------------------------
    const changeStateModalEl = document.getElementById('changeStateModal');
    const changeStateForm = document.getElementById('changeStateForm');
    const stateModalFolioText = document.getElementById('stateModalFolioText');
    const stateModalDeviceText = document.getElementById('stateModalDeviceText');
    const stateModalCurrentBadge = document.getElementById('stateModalCurrentBadge');
    const newStateSelect = document.getElementById('new_state');
    const stateDiagnosisTextarea = document.getElementById('state_technical_diagnosis');
    const btnSubmitChangeState = document.getElementById('btnSubmitChangeState');
    const noMoreStatesNotice = document.getElementById('noMoreStatesNotice');
    const stateSelectContainer = document.getElementById('stateSelectContainer');

    if (changeStateModalEl) {
        changeStateModalEl.addEventListener('show.bs.modal', function (event) {
            const btn = event.relatedTarget;
            if (!btn) return;

            const ticketId = btn.getAttribute('data-id') || '';
            const currentState = btn.getAttribute('data-current-state') || 'Recibido';
            const allowedStatesJson = btn.getAttribute('data-allowed-states') || '[]';
            const diagnosis = btn.getAttribute('data-technical-diagnosis') || '';
            const deviceSummary = btn.getAttribute('data-device-summary') || 'Dispositivo';
            const updateUrl = btn.getAttribute('data-update-url') || '';

            let allowedStates = [];
            try {
                allowedStates = JSON.parse(allowedStatesJson);
            } catch (e) {
                allowedStates = [];
            }

            if (changeStateForm && updateUrl) {
                changeStateForm.action = updateUrl;
            }

            if (stateModalFolioText) stateModalFolioText.textContent = `#TK-${ticketId.padStart(4, '0')}`;
            if (stateModalDeviceText) stateModalDeviceText.textContent = deviceSummary;

            // Mostrar estado actual
            if (stateModalCurrentBadge) {
                stateModalCurrentBadge.textContent = currentState;
                stateModalCurrentBadge.className = `badge badge-state-${currentState.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, "")}`;
            }

            // Diagnóstico
            if (stateDiagnosisTextarea) {
                stateDiagnosisTextarea.value = diagnosis;
            }

            // Poblar select únicamente con estados válidos hacia adelante
            if (newStateSelect) {
                newStateSelect.innerHTML = '<option value="" selected disabled>-- Seleccionar Siguiente Etapa --</option>';

                if (allowedStates.length > 0) {
                    allowedStates.forEach(state => {
                        const opt = document.createElement('option');
                        opt.value = state;
                        opt.textContent = `Avanzar a: ${state}`;
                        newStateSelect.appendChild(opt);
                    });

                    if (stateSelectContainer) stateSelectContainer.classList.remove('d-none');
                    if (noMoreStatesNotice) noMoreStatesNotice.classList.add('d-none');
                    if (btnSubmitChangeState) btnSubmitChangeState.removeAttribute('disabled');
                } else {
                    if (stateSelectContainer) stateSelectContainer.classList.add('d-none');
                    if (noMoreStatesNotice) noMoreStatesNotice.classList.remove('d-none');
                    if (btnSubmitChangeState) btnSubmitChangeState.setAttribute('disabled', 'disabled');
                }
            }
        });
    }

    // --------------------------------------------------------------------------
    // 2. Modal: Bitácora de Notas Técnicas de Seguimiento
    // --------------------------------------------------------------------------
    const notesModalEl = document.getElementById('ticketNotesModal');
    const notesModalFolioText = document.getElementById('notesModalFolioText');
    const notesModalDeviceText = document.getElementById('notesModalDeviceText');
    const notesTimelineContainer = document.getElementById('notesTimelineContainer');
    const addNoteForm = document.getElementById('addNoteForm');
    const noteTextInput = document.getElementById('note_text_input');
    const btnSubmitNote = document.getElementById('btnSubmitNote');
    let currentTriggerNotesBtn = null;

    function renderNotes(notes) {
        if (!notesTimelineContainer) return;

        if (!notes || notes.length === 0) {
            notesTimelineContainer.innerHTML = `
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-chat-left-dots fs-2 text-secondary mb-2 d-block"></i>
                    <span class="small">Aún no hay notas registradas para este ticket. Sé el primero en agregar una observación.</span>
                </div>`;
            return;
        }

        let html = '';
        notes.forEach(note => {
            const authorName = note.user ? `${note.user.name} ${note.user.lastname || ''}` : 'Usuario';
            const authorRole = note.user?.role?.name || 'TÉCNICO';
            const initial = (note.user?.name || 'U').substring(0, 1).toUpperCase();
            const isAdmin = authorRole === 'ADMINISTRADOR';

            let dateFormatted = 'Fecha no disponible';
            if (note.creation_date) {
                const d = new Date(note.creation_date);
                dateFormatted = d.toLocaleDateString('es-GT', {
                    day: '2-digit', month: '2-digit', year: 'numeric',
                    hour: '2-digit', minute: '2-digit'
                });
            }

            html += `
                <div class="note-card">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="note-author-avatar ${isAdmin ? 'admin' : ''}">
                                ${initial}
                            </div>
                            <div>
                                <span class="fw-bold text-dark small d-block">${authorName}</span>
                                <span class="badge ${isAdmin ? 'bg-danger' : 'bg-secondary'} rounded-pill" style="font-size: 0.65rem;">
                                    ${authorRole}
                                </span>
                            </div>
                        </div>
                        <span class="small text-muted" style="font-size: 0.75rem;">
                            <i class="bi bi-clock me-1"></i>${dateFormatted}
                        </span>
                    </div>
                    <p class="text-dark small mb-0 ps-1" style="white-space: pre-wrap;">${escapeHtml(note.note)}</p>
                </div>`;
        });

        notesTimelineContainer.innerHTML = html;
        notesTimelineContainer.scrollTop = 0;
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    if (notesModalEl) {
        notesModalEl.addEventListener('show.bs.modal', function (event) {
            currentTriggerNotesBtn = event.relatedTarget;
            if (!currentTriggerNotesBtn) return;

            const ticketId = currentTriggerNotesBtn.getAttribute('data-id') || '';
            const deviceSummary = currentTriggerNotesBtn.getAttribute('data-device-summary') || 'Dispositivo';
            const listUrl = currentTriggerNotesBtn.getAttribute('data-notes-url') || '';
            const storeUrl = currentTriggerNotesBtn.getAttribute('data-store-note-url') || '';

            if (notesModalFolioText) notesModalFolioText.textContent = `#TK-${ticketId.padStart(4, '0')}`;
            if (notesModalDeviceText) notesModalDeviceText.textContent = deviceSummary;

            if (addNoteForm && storeUrl) {
                addNoteForm.action = storeUrl;
            }

            if (notesTimelineContainer) {
                notesTimelineContainer.innerHTML = `
                    <div class="text-center py-4 text-muted">
                        <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
                        <div class="small">Cargando bitácora de notas...</div>
                    </div>`;
            }

            if (listUrl) {
                fetch(listUrl, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.notes) {
                        renderNotes(data.notes);
                    } else {
                        renderNotes([]);
                    }
                })
                .catch(() => renderNotes([]));
            }
        });
    }

    if (addNoteForm) {
        addNoteForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const noteText = noteTextInput ? noteTextInput.value.trim() : '';
            if (noteText.length < 3) {
                alert('La nota debe contener al menos 3 caracteres.');
                return;
            }

            if (btnSubmitNote) {
                btnSubmitNote.disabled = true;
                btnSubmitNote.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Guardando...';
            }

            const formData = new FormData(addNoteForm);

            fetch(addNoteForm.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (btnSubmitNote) {
                    btnSubmitNote.disabled = false;
                    btnSubmitNote.innerHTML = '<i class="bi bi-send"></i> <span>Agregar Nota</span>';
                }

                if (data.success && data.note) {
                    if (noteTextInput) noteTextInput.value = '';

                    // Volver a cargar las notas
                    if (currentTriggerNotesBtn) {
                        const listUrl = currentTriggerNotesBtn.getAttribute('data-notes-url');
                        if (listUrl) {
                            fetch(listUrl, {
                                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                            })
                            .then(res => res.json())
                            .then(d => {
                                if (d.notes) renderNotes(d.notes);
                            });
                        }

                        // Actualizar badge de conteo en el botón de la tabla
                        const badge = currentTriggerNotesBtn.querySelector('.action-count-badge');
                        if (badge) {
                            const cur = parseInt(badge.textContent || '0', 10);
                            badge.textContent = cur + 1;
                        }
                    }
                } else {
                    alert(data.message || 'Ocurrió un error al guardar la nota.');
                }
            })
            .catch(() => {
                if (btnSubmitNote) {
                    btnSubmitNote.disabled = false;
                    btnSubmitNote.innerHTML = '<i class="bi bi-send"></i> <span>Agregar Nota</span>';
                }
                alert('Error de comunicación con el servidor al registrar la nota.');
            });
        });
    }

    // --------------------------------------------------------------------------
    // 3. Modal: Ver Ficha Técnica Detallada
    // --------------------------------------------------------------------------
    const viewModalEl = document.getElementById('viewTicketModal');

    const viewTicketFolio = document.getElementById('viewTicketFolio');
    const viewClientName = document.getElementById('viewClientName');
    const viewClientPhone = document.getElementById('viewClientPhone');
    const viewDeviceSummary = document.getElementById('viewDeviceSummary');
    const viewDeviceSerial = document.getElementById('viewDeviceSerial');
    const viewDevicePassword = document.getElementById('viewDevicePassword');
    const viewTechnicianName = document.getElementById('viewTechnicianName');
    const viewIntakeDate = document.getElementById('viewIntakeDate');
    const viewReportedIssue = document.getElementById('viewReportedIssue');
    const viewReceptionNotes = document.getElementById('viewReceptionNotes');
    const viewTechnicalDiagnosis = document.getElementById('viewTechnicalDiagnosis');
    const viewTotalCharged = document.getElementById('viewTotalCharged');
    const viewDeposit = document.getElementById('viewDeposit');
    const viewRemainingBalance = document.getElementById('viewRemainingBalance');

    if (viewModalEl) {
        viewModalEl.addEventListener('show.bs.modal', function (event) {
            const btn = event.relatedTarget;
            if (!btn) return;

            const ticketId = btn.getAttribute('data-id') || '';
            const clientName = btn.getAttribute('data-client-name') || 'N/A';
            const clientPhone = btn.getAttribute('data-client-phone') || 'N/A';
            const deviceSummary = btn.getAttribute('data-device-summary') || 'Dispositivo';
            const deviceSerial = btn.getAttribute('data-device-serial') || 'No registrado';
            const devicePassword = btn.getAttribute('data-device-password') || 'Sin clave';
            const technician = btn.getAttribute('data-technician-name') || 'Técnico General';
            const intakeDate = btn.getAttribute('data-intake-date') || 'N/A';
            const reportedIssue = btn.getAttribute('data-reported-issue') || 'Sin especificar';
            const receptionNotes = btn.getAttribute('data-reception-notes') || 'Ninguna';
            const technicalDiagnosis = btn.getAttribute('data-technical-diagnosis') || 'Diagnóstico no registrado';
            const totalCharged = parseFloat(btn.getAttribute('data-total-charged') || '0').toFixed(2);
            const deposit = parseFloat(btn.getAttribute('data-deposit') || '0').toFixed(2);
            const remainingBalance = parseFloat(btn.getAttribute('data-remaining-balance') || '0').toFixed(2);

            if (viewTicketFolio) viewTicketFolio.textContent = `#TK-${ticketId.padStart(4, '0')}`;
            if (viewClientName) viewClientName.textContent = clientName;
            if (viewClientPhone) viewClientPhone.textContent = clientPhone;
            if (viewDeviceSummary) viewDeviceSummary.textContent = deviceSummary;
            if (viewDeviceSerial) viewDeviceSerial.textContent = deviceSerial;
            if (viewDevicePassword) viewDevicePassword.textContent = devicePassword;
            if (viewTechnicianName) viewTechnicianName.textContent = technician;
            if (viewIntakeDate) viewIntakeDate.textContent = intakeDate;
            if (viewReportedIssue) viewReportedIssue.textContent = reportedIssue;
            if (viewReceptionNotes) viewReceptionNotes.textContent = receptionNotes;
            if (viewTechnicalDiagnosis) viewTechnicalDiagnosis.textContent = technicalDiagnosis;
            if (viewTotalCharged) viewTotalCharged.textContent = `Q${totalCharged}`;
            if (viewDeposit) viewDeposit.textContent = `Q${deposit}`;
            if (viewRemainingBalance) viewRemainingBalance.textContent = `Q${remainingBalance}`;
        });
    }

    // --------------------------------------------------------------------------
    // 4. Envío Automático de Filtros al Cambiar Selectores
    // --------------------------------------------------------------------------
    const filterForm = document.getElementById('techTicketsFilterForm');
    const stateFilterSelect = document.getElementById('techStateFilter');
    const techFilterSelect = document.getElementById('techTechnicianFilter');
    const perPageSelect = document.getElementById('techPerPageSelect');

    if (filterForm) {
        if (stateFilterSelect) stateFilterSelect.addEventListener('change', () => filterForm.submit());
        if (techFilterSelect) techFilterSelect.addEventListener('change', () => filterForm.submit());
        if (perPageSelect) perPageSelect.addEventListener('change', () => filterForm.submit());
    }
});
