document:addEventListener ? document.addEventListener('DOMContentLoaded', initProfile) : initProfile();

function initProfile() {
    // 1. Alternar Visibilidad de Contraseñas (Mostrar / Ocultar)
    const toggleButtons = document.querySelectorAll('.btn-toggle-password');
    toggleButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const targetId = this.dataset.target;
            if (!targetId) return;

            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');
            if (input && icon) {
                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';
                icon.classList.toggle('bi-eye', !isPassword);
                icon.classList.toggle('bi-eye-slash', isPassword);
            }
        });
    });

    // 2. Procesamiento Asíncrono de Modificación de Contraseña
    const form = document.getElementById('profileChangePasswordForm');
    const submitBtn = document.getElementById('btnSubmitPasswordChange');
    const feedbackAlert = document.getElementById('passwordFeedbackAlert');
    const feedbackMsg = document.getElementById('passwordFeedbackMessage');
    const feedbackIcon = document.getElementById('passwordFeedbackIcon');

    if (!form || !submitBtn) return;

    form.addEventListener('submit', async function (event) {
        event.preventDefault();
        clearValidationErrors();

        const currentPassInput = document.getElementById('current_password');
        const newPassInput = document.getElementById('new_password');
        const confirmPassInput = document.getElementById('new_password_confirmation');

        const currentVal = currentPassInput ? currentPassInput.value.trim() : '';
        const newVal = newPassInput ? newPassInput.value.trim() : '';
        const confirmVal = confirmPassInput ? confirmPassInput.value.trim() : '';

        let hasClientError = false;

        if (!currentVal) {
            setFieldError(currentPassInput, 'current_password_feedback', 'Ingresa tu contraseña actual.');
            hasClientError = true;
        }

        if (!newVal) {
            setFieldError(newPassInput, 'new_password_feedback', 'Ingresa tu nueva contraseña.');
            hasClientError = true;
        } else if (newVal.length < 8) {
            setFieldError(newPassInput, 'new_password_feedback', 'La nueva contraseña debe tener al menos 8 caracteres.');
            hasClientError = true;
        } else if (newVal === currentVal) {
            setFieldError(newPassInput, 'new_password_feedback', 'La nueva contraseña debe ser distinta a la actual.');
            hasClientError = true;
        }

        if (!confirmVal) {
            setFieldError(confirmPassInput, 'new_password_confirmation_feedback', 'Confirma tu nueva contraseña.');
            hasClientError = true;
        } else if (newVal && newVal !== confirmVal) {
            setFieldError(confirmPassInput, 'new_password_confirmation_feedback', 'La confirmación de la contraseña no coincide.');
            hasClientError = true;
        }

        if (hasClientError) {
            showAlert('danger', 'bi-exclamation-triangle-fill', 'Por favor, corrige los campos señalados.');
            return;
        }

        // Estado de carga en botón
        const originalBtnHtml = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Procesando...';

        try {
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json().catch(() => ({}));

            if (response.ok) {
                form.reset();
                showAlert('success', 'bi-check-circle-fill', data.message || '¡Contraseña modificada exitosamente!');
            } else if (response.status === 422) {
                if (data.errors) {
                    for (const [field, messages] of Object.entries(data.errors)) {
                        const input = document.getElementById(field);
                        const feedbackId = `${field}_feedback`;
                        setFieldError(input, feedbackId, messages[0]);
                    }
                }
                showAlert('danger', 'bi-exclamation-triangle-fill', data.message || 'Datos de contraseña no válidos.');
            } else {
                showAlert('danger', 'bi-exclamation-octagon-fill', data.message || 'Ocurrió un problema en el servidor. Intenta de nuevo.');
            }
        } catch (error) {
            showAlert('danger', 'bi-exclamation-octagon-fill', 'Error de conexión. Verifica tu conexión a la red.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnHtml;
        }
    });

    function setFieldError(input, feedbackId, message) {
        if (input) input.classList.add('is-invalid');
        const feedbackElem = document.getElementById(feedbackId);
        if (feedbackElem) {
            feedbackElem.textContent = message;
        }
    }

    function clearValidationErrors() {
        if (feedbackAlert) {
            feedbackAlert.classList.add('d-none');
            feedbackAlert.classList.remove('show', 'alert-success', 'alert-danger');
        }
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
    }

    function showAlert(type, iconClass, message) {
        if (!feedbackAlert) return;
        feedbackAlert.classList.remove('alert-success', 'alert-danger', 'd-none');
        feedbackAlert.classList.add(`alert-${type}`, 'show');

        if (feedbackIcon) {
            feedbackIcon.className = `bi ${iconClass} fs-5`;
        }
        if (feedbackMsg) {
            feedbackMsg.textContent = message;
        }
        feedbackAlert.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
}
