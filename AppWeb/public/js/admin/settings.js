document.addEventListener("DOMContentLoaded", function () {
    // 1. Carga Dinámica del Modal de Edición de Tipo de Dispositivo
    const editDeviceTypeModalEl = document.getElementById(
        "editDeviceTypeModal",
    );
    const editDeviceTypeForm = document.getElementById("editDeviceTypeForm");
    const editDeviceTypeId = document.getElementById("edit_device_type_id");
    const editDeviceTypeName = document.getElementById("edit_device_type_name");
    const editDeviceTypeStatus = document.getElementById(
        "edit_device_type_status",
    );

    if (editDeviceTypeModalEl) {
        editDeviceTypeModalEl.addEventListener(
            "show.bs.modal",
            function (event) {
                const triggerBtn = event.relatedTarget;
                if (!triggerBtn) return;

                const id = triggerBtn.getAttribute("data-id");
                const name = triggerBtn.getAttribute("data-name");
                const status = triggerBtn.getAttribute("data-status");
                const updateUrl = triggerBtn.getAttribute("data-update-url");

                if (editDeviceTypeForm && updateUrl) {
                    editDeviceTypeForm.action = updateUrl;
                }
                if (editDeviceTypeId) {
                    editDeviceTypeId.value = id || "";
                }
                if (editDeviceTypeName) {
                    editDeviceTypeName.value = name || "";
                    setTimeout(() => editDeviceTypeName.focus(), 150);
                }
                if (editDeviceTypeStatus) {
                    editDeviceTypeStatus.checked =
                        status === "1" || status === "true";
                }
            },
        );
    }

    // 2. Carga Dinámica del Modal de Confirmación de Borrado Lógico / Estado
    const toggleDeviceTypeModalEl = document.getElementById(
        "toggleDeviceTypeModal",
    );
    const toggleDeviceTypeForm = document.getElementById(
        "toggleDeviceTypeForm",
    );
    const toggleTargetNameSpan = document.getElementById(
        "toggleDeviceTypeNameText",
    );
    const toggleActionTextSpan = document.getElementById(
        "toggleDeviceTypeActionText",
    );
    const toggleModalIconWrapper = document.getElementById(
        "toggleModalIconWrapper",
    );
    const toggleModalIcon = document.getElementById("toggleModalIcon");
    const toggleSubmitBtn = document.getElementById(
        "btnConfirmToggleDeviceType",
    );

    if (toggleDeviceTypeModalEl) {
        toggleDeviceTypeModalEl.addEventListener(
            "show.bs.modal",
            function (event) {
                const triggerBtn = event.relatedTarget;
                if (!triggerBtn) return;

                const name = triggerBtn.getAttribute("data-name");
                const currentStatus = triggerBtn.getAttribute("data-status");
                const toggleUrl = triggerBtn.getAttribute("data-toggle-url");

                const isCurrentlyActive =
                    currentStatus === "1" || currentStatus === "true";

                if (toggleDeviceTypeForm && toggleUrl) {
                    toggleDeviceTypeForm.action = toggleUrl;
                }
                if (toggleTargetNameSpan) {
                    toggleTargetNameSpan.textContent = name || "este registro";
                }

                if (isCurrentlyActive) {
                    // Se va a desactivar (borrado lógico)
                    if (toggleActionTextSpan) {
                        toggleActionTextSpan.textContent =
                            "desactivar (borrado lógico)";
                    }
                    if (toggleModalIconWrapper) {
                        toggleModalIconWrapper.className =
                            "state-confirm-icon-wrapper icon-deactivate mb-3";
                    }
                    if (toggleModalIcon) {
                        toggleModalIcon.className = "bi bi-shield-x";
                    }
                    if (toggleSubmitBtn) {
                        toggleSubmitBtn.className =
                            "btn btn-danger d-inline-flex align-items-center gap-2 px-4";
                        toggleSubmitBtn.textContent = "Sí, Desactivar";
                    }
                } else {
                    // Se va a reactivar
                    if (toggleActionTextSpan) {
                        toggleActionTextSpan.textContent = "reactivar";
                    }
                    if (toggleModalIconWrapper) {
                        toggleModalIconWrapper.className =
                            "state-confirm-icon-wrapper icon-activate mb-3";
                    }
                    if (toggleModalIcon) {
                        toggleModalIcon.className = "bi bi-shield-check";
                    }
                    if (toggleSubmitBtn) {
                        toggleSubmitBtn.className =
                            "btn btn-success d-inline-flex align-items-center gap-2 px-4";
                        toggleSubmitBtn.textContent = "Sí, Reactivar";
                    }
                }
            },
        );
    }

    // 3. Envío Automático de Filtros al Cambiar Selectores
    const filterForm = document.getElementById("deviceTypesFilterForm");
    const statusFilterSelect = document.getElementById(
        "deviceTypeStatusFilter",
    );
    const perPageSelect = document.getElementById("deviceTypePerPageSelect");

    if (filterForm) {
        if (statusFilterSelect) {
            statusFilterSelect.addEventListener("change", function () {
                filterForm.submit();
            });
        }
        if (perPageSelect) {
            perPageSelect.addEventListener("change", function () {
                filterForm.submit();
            });
        }
    }
});
