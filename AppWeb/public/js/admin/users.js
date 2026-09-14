document.addEventListener('DOMContentLoaded', function () {
    // Manejo de apertura y cierre de modales utilizando Bootstrap 5 Modal API
    function openModal(id) {
        const modalEl = document.getElementById(id);
        if (modalEl && window.bootstrap) {
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }
    }

    function closeModal(id) {
        const modalEl = document.getElementById(id);
        if (modalEl && window.bootstrap) {
            bootstrap.Modal.getOrCreateInstance(modalEl).hide();
        }
    }

    // Botón para abrir modal de creación
    const btnOpenCreate = document.getElementById('btnOpenCreateModal');
    if (btnOpenCreate) {
        btnOpenCreate.addEventListener('click', function () {
            openModal('createModal');
        });
    }

    // Llenar y abrir Modal de Edición (SIN campos de contraseña)
    document.querySelectorAll('.btnEditUser').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.dataset.id;
            const name = this.dataset.name;
            const lastname = this.dataset.lastname;
            const username = this.dataset.username;
            const email = this.dataset.email;
            const role = this.dataset.role;
            const state = this.dataset.state === '1';

            // Asignar ruta dinámica con el ID del usuario seleccionado
            const editForm = document.getElementById('editUserForm');
            if (editForm) {
                editForm.action = `/admin/users/${id}`;
            }

            document.getElementById('edit_name').value = name;
            document.getElementById('edit_lastname').value = lastname;
            document.getElementById('edit_username').value = username;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_role').value = role;
            document.getElementById('edit_state').checked = state;

            openModal('editModal');
        });
    });

    // Configurar y abrir Modal de Cambio de Contraseña
    document.querySelectorAll('.btnChangePasswordUser').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.dataset.id;
            const username = this.dataset.username;
            const name = this.dataset.name;

            // Asignar ruta dinámica con el ID del usuario seleccionado
            const passwordForm = document.getElementById('changePasswordUserForm');
            if (passwordForm) {
                passwordForm.action = `/admin/users/${id}/password`;
            }

            document.getElementById('changePasswordUserDisplay').textContent = `${name} (@${username})`;
            document.getElementById('change_password').value = '';
            document.getElementById('change_password_confirmation').value = '';

            openModal('changePasswordModal');
        });
    });

    // Alternar Estado de Usuario (Activar / Desactivar) con Ventana Modal Visual
    document.querySelectorAll('.btnToggleUser').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.dataset.id;
            const username = this.dataset.username;
            const name = this.dataset.name || username;
            const isCurrentlyActive = this.dataset.state === '1';

            const form = document.getElementById('toggleStateUserForm');
            const modalTitleText = document.getElementById('toggleStateModalTitleText');
            const modalIcon = document.getElementById('toggleStateModalIcon');
            const iconWrapper = document.getElementById('toggleStateIconWrapper');
            const bigIcon = document.getElementById('toggleStateBigIcon');
            const promptEl = document.getElementById('toggleStatePrompt');
            const userDisplay = document.getElementById('toggleStateUserDisplay');
            const noticeEl = document.getElementById('toggleStateNotice');
            const confirmBtn = document.getElementById('btnConfirmToggleState');
            const confirmBtnText = document.getElementById('toggleConfirmBtnText');
            const confirmBtnIcon = document.getElementById('toggleConfirmBtnIcon');

            if (form) {
                form.action = `/admin/users/${id}/toggle-state`;
            }

            if (userDisplay) {
                userDisplay.textContent = `${name} (@${username})`;
            }

            if (isCurrentlyActive) {
                // Configurar para Desactivar
                if (modalTitleText) modalTitleText.textContent = 'Desactivar Cuenta de Usuario';
                if (modalIcon) {
                    modalIcon.className = 'bi bi-person-dash text-error';
                }
                if (iconWrapper) {
                    iconWrapper.className = 'status-confirm-icon-wrapper is-deactivating';
                }
                if (bigIcon) {
                    bigIcon.className = 'bi bi-slash-circle';
                }
                if (promptEl) {
                    promptEl.textContent = '¿Estás seguro de que deseas desactivar la cuenta del siguiente usuario?';
                }
                if (noticeEl) {
                    noticeEl.textContent = 'El usuario no podrá iniciar sesión en la plataforma mientras su cuenta permanezca desactivada.';
                }
                if (confirmBtn) {
                    confirmBtn.className = 'btn btn-danger';
                }
                if (confirmBtnText) {
                    confirmBtnText.textContent = 'Desactivar Usuario';
                }
                if (confirmBtnIcon) {
                    confirmBtnIcon.className = 'bi bi-person-x';
                }
            } else {
                // Configurar para Activar
                if (modalTitleText) modalTitleText.textContent = 'Activar Cuenta de Usuario';
                if (modalIcon) {
                    modalIcon.className = 'bi bi-person-check text-success';
                }
                if (iconWrapper) {
                    iconWrapper.className = 'status-confirm-icon-wrapper is-activating';
                }
                if (bigIcon) {
                    bigIcon.className = 'bi bi-check-circle';
                }
                if (promptEl) {
                    promptEl.textContent = '¿Estás seguro de que deseas activar la cuenta del siguiente usuario?';
                }
                if (noticeEl) {
                    noticeEl.textContent = 'El usuario podrá volver a iniciar sesión y acceder con sus credenciales y permisos correspondientes.';
                }
                if (confirmBtn) {
                    confirmBtn.className = 'btn btn-success';
                }
                if (confirmBtnText) {
                    confirmBtnText.textContent = 'Activar Usuario';
                }
                if (confirmBtnIcon) {
                    confirmBtnIcon.className = 'bi bi-person-check';
                }
            }

            openModal('toggleStateModal');
        });
    });

    // Función global para alternar visibilidad de contraseñas en inputs
    window.toggleInputVisibility = function (inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');
        if (input && icon) {
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            icon.classList.toggle('bi-eye', !isPassword);
            icon.classList.toggle('bi-eye-slash', isPassword);
        }
    };

    // Event listener para botones de alternar visibilidad de contraseñas
    document.querySelectorAll('.btn-toggle-input-pwd').forEach(btn => {
        btn.addEventListener('click', function () {
            const targetId = this.getAttribute('data-toggle-target');
            if (targetId) {
                window.toggleInputVisibility(targetId, this);
            }
        });
    });

    // Paginación Variable (5, 25, 50 registros)
    const perPageSelect = document.getElementById('perPageSelect');
    const paginationStart = document.getElementById('paginationStart');
    const paginationEnd = document.getElementById('paginationEnd');
    const paginationTotal = document.getElementById('paginationTotal');
    const btnPrevPage = document.getElementById('btnPrevPage');
    const btnNextPage = document.getElementById('btnNextPage');
    const pageItemNumbers = document.querySelectorAll('.page-item-number');
    const pageButtons = document.querySelectorAll('#paginationList .pagination-btn[data-page]');

    const totalRecords = parseInt(paginationTotal ? paginationTotal.textContent : '15', 10);
    let currentPage = 1;

    function updatePaginationUI() {
        const perPage = parseInt(perPageSelect.value, 10);
        const totalPages = Math.ceil(totalRecords / perPage);

        if (currentPage > totalPages) {
            currentPage = 1;
        }

        const startRecord = ((currentPage - 1) * perPage) + 1;
        const endRecord = Math.min(currentPage * perPage, totalRecords);

        if (paginationStart) paginationStart.textContent = startRecord;
        if (paginationEnd) paginationEnd.textContent = endRecord;

        // Actualizar botones numéricos
        pageButtons.forEach(btn => {
            const pageNum = parseInt(btn.getAttribute('data-page'), 10);
            const parentLi = btn.closest('.page-item-number');

            if (parentLi) {
                parentLi.classList.toggle('d-none', pageNum > totalPages);
            }

            if (pageNum <= totalPages) {
                btn.classList.toggle('active', pageNum === currentPage);
                btn.removeAttribute('disabled');
            }
        });

        // Actualizar estado de botón Anterior y Siguiente
        if (btnPrevPage) {
            btnPrevPage.disabled = (currentPage <= 1);
            btnPrevPage.classList.toggle('disabled', currentPage <= 1);
        }

        if (btnNextPage) {
            btnNextPage.disabled = (currentPage >= totalPages);
            btnNextPage.classList.toggle('disabled', currentPage >= totalPages);
        }
    }

    if (perPageSelect) {
        perPageSelect.addEventListener('change', function () {
            currentPage = 1;
            updatePaginationUI();
        });
    }

    pageButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            currentPage = parseInt(this.getAttribute('data-page'), 10);
            updatePaginationUI();
        });
    });

    if (btnPrevPage) {
        btnPrevPage.addEventListener('click', function () {
            if (currentPage > 1) {
                currentPage--;
                updatePaginationUI();
            }
        });
    }

    if (btnNextPage) {
        btnNextPage.addEventListener('click', function () {
            const perPage = parseInt(perPageSelect.value, 10);
            const totalPages = Math.ceil(totalRecords / perPage);
            if (currentPage < totalPages) {
                currentPage++;
                updatePaginationUI();
            }
        });
    }
});
