document.addEventListener('DOMContentLoaded', function () {
    // Filtros Automáticos del Formulario
    const filterUsersForm = document.getElementById('filterUsersForm');
    const roleFilter = document.querySelector('select[name="role"]');
    const stateFilter = document.querySelector('select[name="state"]');
    const perPageSelect = document.getElementById('perPageSelect');

    if (filterUsersForm) {
        [roleFilter, stateFilter].forEach(select => {
            if (select) {
                select.addEventListener('change', function () {
                    filterUsersForm.submit();
                });
            }
        });

        if (perPageSelect) {
            perPageSelect.addEventListener('change', function () {
                const hiddenPerPage = document.getElementById('hiddenPerPage');
                if (hiddenPerPage) {
                    hiddenPerPage.value = this.value;
                }
                filterUsersForm.submit();
            });
        }
    }

    // Modal de Edición de Usuario (Bootstrap show.bs.modal)
    const editModal = document.getElementById('editModal');
    const editUserForm = document.getElementById('editUserForm');

    if (editModal && editUserForm) {
        editModal.addEventListener('show.bs.modal', function (event) {
            const btn = event.relatedTarget;
            if (!btn) return;

            const id       = btn.getAttribute('data-bs-id') || btn.getAttribute('data-id');
            const name     = btn.getAttribute('data-bs-name') || btn.getAttribute('data-name') || '';
            const lastname = btn.getAttribute('data-bs-lastname') || btn.getAttribute('data-lastname') || '';
            const username = btn.getAttribute('data-bs-username') || btn.getAttribute('data-username') || '';
            const email    = btn.getAttribute('data-bs-email') || btn.getAttribute('data-email') || '';
            const role     = btn.getAttribute('data-bs-role') || btn.getAttribute('data-role') || '';
            const state    = (btn.getAttribute('data-bs-state') || btn.getAttribute('data-state')) === '1';

            editUserForm.action = `/admin/users/${id}`;

            const inputName = document.getElementById('edit_name');
            const inputLastname = document.getElementById('edit_lastname');
            const inputUsername = document.getElementById('edit_username');
            const inputEmail = document.getElementById('edit_email');
            const selectRole = document.getElementById('edit_role');
            const checkState = document.getElementById('edit_state');

            if (inputName) inputName.value = name;
            if (inputLastname) inputLastname.value = lastname;
            if (inputUsername) inputUsername.value = username;
            if (inputEmail) inputEmail.value = email;
            if (selectRole) selectRole.value = role;
            if (checkState) checkState.checked = state;
        });
    }

    // Modal de Cambio de Contraseña (Bootstrap show.bs.modal)
    const changePasswordModal = document.getElementById('changePasswordModal');
    const changePasswordUserForm = document.getElementById('changePasswordUserForm');

    if (changePasswordModal && changePasswordUserForm) {
        changePasswordModal.addEventListener('show.bs.modal', function (event) {
            const btn = event.relatedTarget;
            if (!btn) return;

            const id      = btn.getAttribute('data-bs-id') || btn.getAttribute('data-id');
            const display = btn.getAttribute('data-bs-display') || `${btn.getAttribute('data-name')} (@${btn.getAttribute('data-username')})`;

            changePasswordUserForm.action = `/admin/users/${id}/password`;

            const displayEl = document.getElementById('changePasswordUserDisplay');
            if (displayEl) displayEl.textContent = display;

            const pwd = document.getElementById('change_password');
            const pwdConf = document.getElementById('change_password_confirmation');
            if (pwd) pwd.value = '';
            if (pwdConf) pwdConf.value = '';
        });
    }

    // Modal de Alternar Estado (Activar / Desactivar)
    const toggleStateModal = document.getElementById('toggleStateModal');
    const toggleStateUserForm = document.getElementById('toggleStateUserForm');

    if (toggleStateModal && toggleStateUserForm) {
        toggleStateModal.addEventListener('show.bs.modal', function (event) {
            const btn = event.relatedTarget;
            if (!btn) return;

            const id = btn.getAttribute('data-bs-id') || btn.getAttribute('data-id');
            const isCurrentlyActive = (btn.getAttribute('data-bs-state') || btn.getAttribute('data-state')) === '1';
            const name = btn.getAttribute('data-bs-name') || btn.getAttribute('data-name') || '';
            const username = btn.getAttribute('data-bs-username') || btn.getAttribute('data-username') || '';

            toggleStateUserForm.action = `/admin/users/${id}/toggle-state`;

            const userDisplay = document.getElementById('toggleStateUserDisplay');
            if (userDisplay) userDisplay.textContent = `${name} (@${username})`;

            const modalTitleText = document.getElementById('toggleStateModalTitleText');
            const modalIcon      = document.getElementById('toggleStateModalIcon');
            const iconWrapper    = document.getElementById('toggleStateIconWrapper');
            const bigIcon        = document.getElementById('toggleStateBigIcon');
            const promptEl       = document.getElementById('toggleStatePrompt');
            const noticeEl       = document.getElementById('toggleStateNotice');
            const confirmBtn     = document.getElementById('btnConfirmToggleState');
            const confirmBtnText = document.getElementById('toggleConfirmBtnText');
            const confirmBtnIcon = document.getElementById('toggleConfirmBtnIcon');

            if (isCurrentlyActive) {
                if (modalTitleText) modalTitleText.textContent = 'Desactivar Cuenta de Usuario';
                if (modalIcon) modalIcon.className = 'bi bi-person-dash text-danger';
                if (iconWrapper) iconWrapper.className = 'status-confirm-icon-wrapper is-deactivating';
                if (bigIcon) bigIcon.className = 'bi bi-slash-circle';
                if (promptEl) promptEl.textContent = '¿Estás seguro de que deseas desactivar la cuenta del siguiente usuario?';
                if (noticeEl) noticeEl.textContent = 'El usuario no podrá iniciar sesión en la plataforma mientras su cuenta permanezca desactivada.';
                if (confirmBtn) confirmBtn.className = 'btn btn-danger';
                if (confirmBtnText) confirmBtnText.textContent = 'Desactivar Usuario';
                if (confirmBtnIcon) confirmBtnIcon.className = 'bi bi-person-x';
            } else {
                if (modalTitleText) modalTitleText.textContent = 'Activar Cuenta de Usuario';
                if (modalIcon) modalIcon.className = 'bi bi-person-check text-success';
                if (iconWrapper) iconWrapper.className = 'status-confirm-icon-wrapper is-activating';
                if (bigIcon) bigIcon.className = 'bi bi-check-circle';
                if (promptEl) promptEl.textContent = '¿Estás seguro de que deseas activar la cuenta del siguiente usuario?';
                if (noticeEl) noticeEl.textContent = 'El usuario podrá volver a iniciar sesión y acceder con sus credenciales y permisos correspondientes.';
                if (confirmBtn) confirmBtn.className = 'btn btn-success';
                if (confirmBtnText) confirmBtnText.textContent = 'Activar Usuario';
                if (confirmBtnIcon) confirmBtnIcon.className = 'bi bi-person-check';
            }
        });
    }

    // Visibilidad de Contraseñas en Inputs
    document.querySelectorAll('.btn-toggle-input-pwd').forEach(btn => {
        btn.addEventListener('click', function () {
            const targetId = this.getAttribute('data-toggle-target');
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');
            if (input && icon) {
                const isPassword = (input.type === 'password');
                input.type = isPassword ? 'text' : 'password';
                icon.classList.toggle('bi-eye', !isPassword);
                icon.classList.toggle('bi-eye-slash', isPassword);
            }
        });
    });

    // Validación Nativa de Formularios con Bootstrap 5 (was-validated)
    document.querySelectorAll('.needs-validation').forEach(form => {
        form.addEventListener('submit', function (e) {
            if (!this.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            this.classList.add('was-validated');
        });
    });
});