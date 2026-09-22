document.addEventListener('DOMContentLoaded', function () {
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

    // Notificación y simulación de envío al presionar "Genera Código"
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
});
