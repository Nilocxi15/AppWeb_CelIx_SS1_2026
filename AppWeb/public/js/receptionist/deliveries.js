document.addEventListener('DOMContentLoaded', function () {
    // 1. Modal de Entrega y Liquidación de Ticket
    const deliverModalEl = document.getElementById('deliverTicketModal');
    const deliverForm = document.getElementById('deliverTicketForm');

    // Elementos del Modal de Entrega
    const deliverTicketFolioText = document.getElementById('deliverTicketFolioText');
    const deliverClientNameText = document.getElementById('deliverClientNameText');
    const deliverClientPhoneText = document.getElementById('deliverClientPhoneText');
    const deliverDeviceText = document.getElementById('deliverDeviceText');
    const deliverDiagnosisText = document.getElementById('deliverDiagnosisText');
    const deliverTotalChargedSpan = document.getElementById('deliverTotalChargedSpan');
    const deliverDepositSpan = document.getElementById('deliverDepositSpan');
    const deliverRemainingBalanceSpan = document.getElementById('deliverRemainingBalanceSpan');
    const deliverAmountToPayInput = document.getElementById('deliver_amount_to_pay');
    const deliverReturnDateInput = document.getElementById('deliver_return_date');

    if (deliverModalEl) {
        deliverModalEl.addEventListener('show.bs.modal', function (event) {
            const btn = event.relatedTarget;
            if (!btn) return;

            const ticketId = btn.getAttribute('data-id') || '';
            const clientName = btn.getAttribute('data-client-name') || 'N/A';
            const clientPhone = btn.getAttribute('data-client-phone') || 'Sin teléfono';
            const deviceSummary = btn.getAttribute('data-device-summary') || 'Dispositivo';
            const diagnosis = btn.getAttribute('data-technical-diagnosis') || 'Reparación técnica completada exitosamente.';
            const totalCharged = parseFloat(btn.getAttribute('data-total-charged') || '0').toFixed(2);
            const deposit = parseFloat(btn.getAttribute('data-deposit') || '0').toFixed(2);
            const remainingBalance = parseFloat(btn.getAttribute('data-remaining-balance') || '0').toFixed(2);
            const deliverUrl = btn.getAttribute('data-deliver-url') || '';

            // Asignar acción al formulario
            if (deliverForm && deliverUrl) {
                deliverForm.action = deliverUrl;
            }

            // Textos en el modal
            if (deliverTicketFolioText) deliverTicketFolioText.textContent = `#TK-${ticketId.padStart(4, '0')}`;
            if (deliverClientNameText) deliverClientNameText.textContent = clientName;
            if (deliverClientPhoneText) deliverClientPhoneText.textContent = clientPhone;
            if (deliverDeviceText) deliverDeviceText.textContent = deviceSummary;
            if (deliverDiagnosisText) deliverDiagnosisText.textContent = diagnosis;

            // Desglose económico
            if (deliverTotalChargedSpan) deliverTotalChargedSpan.textContent = `Q${totalCharged}`;
            if (deliverDepositSpan) deliverDepositSpan.textContent = `Q${deposit}`;
            if (deliverRemainingBalanceSpan) deliverRemainingBalanceSpan.textContent = `Q${remainingBalance}`;

            // Prellenar monto a liquidar con el saldo pendiente
            if (deliverAmountToPayInput) {
                deliverAmountToPayInput.value = remainingBalance;
            }

            // Prellenar fecha y hora actual en formato YYYY-MM-DDTHH:mm
            if (deliverReturnDateInput) {
                const now = new Date();
                const year = now.getFullYear();
                const month = String(now.getMonth() + 1).padStart(2, '0');
                const day = String(now.getDate()).padStart(2, '0');
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                deliverReturnDateInput.value = `${year}-${month}-${day}T${hours}:${minutes}`;
            }
        });
    }

    // 2. Modal de Inspección / Ver Ficha Técnica del Ticket
    const viewModalEl = document.getElementById('viewTicketModal');

    const viewTicketFolio = document.getElementById('viewTicketFolio');
    const viewClientName = document.getElementById('viewClientName');
    const viewClientPhone = document.getElementById('viewClientPhone');
    const viewClientDpi = document.getElementById('viewClientDpi');
    const viewDeviceSummary = document.getElementById('viewDeviceSummary');
    const viewDeviceSerial = document.getElementById('viewDeviceSerial');
    const viewTechnicianName = document.getElementById('viewTechnicianName');
    const viewIntakeDate = document.getElementById('viewIntakeDate');
    const viewReportedIssue = document.getElementById('viewReportedIssue');
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
            const clientDpi = btn.getAttribute('data-client-dpi') || 'N/A';
            const deviceSummary = btn.getAttribute('data-device-summary') || 'Dispositivo';
            const deviceSerial = btn.getAttribute('data-device-serial') || 'N/A';
            const technician = btn.getAttribute('data-technician-name') || 'Técnico General';
            const intakeDate = btn.getAttribute('data-intake-date') || 'N/A';
            const reportedIssue = btn.getAttribute('data-reported-issue') || 'Sin especificar';
            const technicalDiagnosis = btn.getAttribute('data-technical-diagnosis') || 'Diagnóstico no registrado';
            const totalCharged = parseFloat(btn.getAttribute('data-total-charged') || '0').toFixed(2);
            const deposit = parseFloat(btn.getAttribute('data-deposit') || '0').toFixed(2);
            const remainingBalance = parseFloat(btn.getAttribute('data-remaining-balance') || '0').toFixed(2);

            if (viewTicketFolio) viewTicketFolio.textContent = `#TK-${ticketId.padStart(4, '0')}`;
            if (viewClientName) viewClientName.textContent = clientName;
            if (viewClientPhone) viewClientPhone.textContent = clientPhone;
            if (viewClientDpi) viewClientDpi.textContent = clientDpi;
            if (viewDeviceSummary) viewDeviceSummary.textContent = deviceSummary;
            if (viewDeviceSerial) viewDeviceSerial.textContent = deviceSerial;
            if (viewTechnicianName) viewTechnicianName.textContent = technician;
            if (viewIntakeDate) viewIntakeDate.textContent = intakeDate;
            if (viewReportedIssue) viewReportedIssue.textContent = reportedIssue;
            if (viewTechnicalDiagnosis) viewTechnicalDiagnosis.textContent = technicalDiagnosis;
            if (viewTotalCharged) viewTotalCharged.textContent = `Q${totalCharged}`;
            if (viewDeposit) viewDeposit.textContent = `Q${deposit}`;
            if (viewRemainingBalance) viewRemainingBalance.textContent = `Q${remainingBalance}`;
        });
    }

    // 3. Envío Automático del Formulario de Filtros al Cambiar Selectores
    const filterForm = document.getElementById('deliveriesFilterForm');
    const perPageSelect = document.getElementById('deliveriesPerPageSelect');
    const stateSelect = document.getElementById('deliveriesStateSelect');

    if (filterForm) {
        if (perPageSelect) {
            perPageSelect.addEventListener('change', function () {
                filterForm.submit();
            });
        }
        if (stateSelect) {
            stateSelect.addEventListener('change', function () {
                filterForm.submit();
            });
        }
    }
});
