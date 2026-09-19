// CelIx - Lógica Frontend de Inventario y Kardex (Bootstrap 5 Nativo)
document.addEventListener("DOMContentLoaded", function () {
    // Conservación de Pestaña Activa (Productos o Categorías)
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get("active_tab") === "categories") {
        const catTabEl = document.getElementById("categories-tab");
        if (catTabEl && window.bootstrap) {
            bootstrap.Tab.getOrCreateInstance(catTabEl).show();
        }
    }

    // Auto-envío ágil de filtros en cambios de selector
    const productFilterForm = document.getElementById("productFilterForm");
    if (productFilterForm) {
        [
            "productCategoryFilter",
            "productStockFilter",
            "productStatusFilter",
            "productPerPageSelect",
        ].forEach((id) => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener("change", () => productFilterForm.submit());
            }
        });
    }

    const kardexFilterForm = document.getElementById("kardexFilterForm");
    if (kardexFilterForm) {
        const kardexTypeFilter = document.getElementById("kardexTypeFilter");
        if (kardexTypeFilter) {
            kardexTypeFilter.addEventListener("change", () =>
                kardexFilterForm.submit(),
            );
        }
    }

    // Modal: Ver Detalle de Producto (#viewProductModal)
    const viewProductModal = document.getElementById("viewProductModal");
    let currentDetailBarcode = null;

    if (viewProductModal) {
        viewProductModal.addEventListener("show.bs.modal", function (event) {
            const btn = event.relatedTarget;
            if (!btn) return;

            const ds = btn.dataset;
            currentDetailBarcode = ds.barcode;

            const setText = (id, val) => {
                const el = document.getElementById(id);
                if (el) el.textContent = val;
            };

            setText("detailProductBarcode", ds.barcode || "---");
            setText("detailProductName", ds.name || "---");
            setText("detailProductCategory", ds.category || "Sin categoría");
            setText(
                "detailProductPrice",
                "Q " + parseFloat(ds.price || 0).toFixed(2),
            );
            setText("detailProductStock", (ds.stock || 0) + " unid.");
            setText("detailProductMinStock", (ds.minstock || 5) + " unid.");
            setText(
                "detailProductDescription",
                ds.description || "Sin descripción registrada.",
            );

            const badge = document.getElementById("detailProductStatusBadge");
            if (badge) {
                const isActive = ds.status === "activo";
                badge.className =
                    "badge " + (isActive ? "badge-active" : "badge-inactive");
                badge.innerHTML = isActive
                    ? '<i class="bi bi-check-circle me-1"></i>Activo'
                    : '<i class="bi bi-dash-circle me-1"></i>Inactivo';
            }
        });

        const btnDetailOpenMovement = document.getElementById(
            "btnDetailOpenMovement",
        );
        if (btnDetailOpenMovement) {
            btnDetailOpenMovement.addEventListener("click", function () {
                const viewInstance =
                    bootstrap.Modal.getInstance(viewProductModal);
                if (viewInstance) viewInstance.hide();

                const movModalEl = document.getElementById(
                    "inventoryMovementModal",
                );
                if (movModalEl) {
                    const movInstance =
                        bootstrap.Modal.getOrCreateInstance(movModalEl);
                    const prodSelect =
                        document.getElementById("movement_product");
                    if (prodSelect && currentDetailBarcode) {
                        prodSelect.value = currentDetailBarcode;
                        updateProjectedStock();
                    }
                    movInstance.show();
                }
            });
        }
    }

    // Modal: Editar Producto (#editProductModal)
    const editProductModal = document.getElementById("editProductModal");
    const editProductForm = document.getElementById("editProductForm");

    if (editProductModal && editProductForm) {
        editProductModal.addEventListener("show.bs.modal", function (event) {
            const btn = event.relatedTarget;
            if (!btn) return;

            const ds = btn.dataset;
            editProductForm.action = `/recepcion/inventario/productos/${encodeURIComponent(ds.barcode)}`;

            const setVal = (id, val) => {
                const el = document.getElementById(id);
                if (el) el.value = val;
            };

            setVal("edit_bar_code", ds.barcode || "");
            setVal("edit_name", ds.name || "");
            setVal("edit_category", ds.categoryId || "");
            setVal("edit_price", ds.price || "");
            setVal("edit_minium_stock", ds.minstock || 5);
            setVal("edit_description", ds.description || "");

            const statusCheck = document.getElementById("edit_status");
            if (statusCheck) {
                statusCheck.checked = ds.status === "1" || ds.status === "true";
            }

            const subtitle = document.getElementById("editBarcodeSubtitle");
            if (subtitle) {
                subtitle.textContent = `Código: ${ds.barcode}`;
            }
        });
    }

    // Modal: Alternar Estado de Producto (Borrado Lógico) (#toggleProductStateModal)
    const toggleProductStateModal = document.getElementById(
        "toggleProductStateModal",
    );
    const toggleProductStateForm = document.getElementById(
        "toggleProductStateForm",
    );

    if (toggleProductStateModal && toggleProductStateForm) {
        toggleProductStateModal.addEventListener(
            "show.bs.modal",
            function (event) {
                const btn = event.relatedTarget;
                if (!btn) return;

                const ds = btn.dataset;
                toggleProductStateForm.action = `/recepcion/inventario/productos/${encodeURIComponent(ds.barcode)}/toggle-status`;

                const targetEl = document.getElementById("productStateTarget");
                const promptEl = document.getElementById("productStatePrompt");
                const iconWrapper = document.getElementById(
                    "productStateIconWrapper",
                );
                const confirmBtn = document.getElementById(
                    "btnConfirmProductState",
                );

                if (targetEl)
                    targetEl.textContent = `${ds.barcode} - ${ds.name}`;

                const isCurrentlyActive = ds.status === "activo";
                if (isCurrentlyActive) {
                    if (promptEl)
                        promptEl.textContent =
                            "¿Estás seguro de que deseas desactivar este producto? Dejará de aparecer en la venta de mostrador.";
                    if (iconWrapper) {
                        iconWrapper.classList.remove("is-activating");
                        iconWrapper.innerHTML =
                            '<i class="bi bi-dash-circle"></i>';
                    }
                    if (confirmBtn) {
                        confirmBtn.className = "btn btn-danger";
                        confirmBtn.textContent = "Desactivar Producto";
                    }
                } else {
                    if (promptEl)
                        promptEl.textContent =
                            "¿Deseas reactivar este producto para habilitarlo nuevamente en el catálogo de ventas?";
                    if (iconWrapper) {
                        iconWrapper.classList.add("is-activating");
                        iconWrapper.innerHTML =
                            '<i class="bi bi-check-circle"></i>';
                    }
                    if (confirmBtn) {
                        confirmBtn.className = "btn btn-success";
                        confirmBtn.textContent = "Reactivar Producto";
                    }
                }
            },
        );
    }

    // Modal: Editar Categoría (#editCategoryModal)
    const editCategoryModal = document.getElementById("editCategoryModal");
    const editCategoryForm = document.getElementById("editCategoryForm");

    if (editCategoryModal && editCategoryForm) {
        editCategoryModal.addEventListener("show.bs.modal", function (event) {
            const btn = event.relatedTarget;
            if (!btn) return;

            const ds = btn.dataset;
            editCategoryForm.action = `/recepcion/inventario/categorias/${encodeURIComponent(ds.id)}`;

            const setVal = (id, val) => {
                const el = document.getElementById(id);
                if (el) el.value = val;
            };

            setVal("edit_cat_id", ds.id || "");
            setVal("edit_cat_name", ds.name || "");
            setVal("edit_cat_description", ds.description || "");

            const statusCheck = document.getElementById("edit_cat_status");
            if (statusCheck) {
                statusCheck.checked = ds.status === "1" || ds.status === "true";
            }
        });
    }

    // Modal: Alternar Estado de Categoría (#toggleCategoryStateModal)
    const toggleCategoryStateModal = document.getElementById(
        "toggleCategoryStateModal",
    );
    const toggleCategoryStateForm = document.getElementById(
        "toggleCategoryStateForm",
    );

    if (toggleCategoryStateModal && toggleCategoryStateForm) {
        toggleCategoryStateModal.addEventListener(
            "show.bs.modal",
            function (event) {
                const btn = event.relatedTarget;
                if (!btn) return;

                const ds = btn.dataset;
                toggleCategoryStateForm.action = `/recepcion/inventario/categorias/${encodeURIComponent(ds.id)}/toggle-status`;

                const targetEl = document.getElementById("categoryStateTarget");
                const promptEl = document.getElementById("categoryStatePrompt");
                const iconWrapper = document.getElementById(
                    "categoryStateIconWrapper",
                );
                const confirmBtn = document.getElementById(
                    "btnConfirmCategoryState",
                );

                if (targetEl)
                    targetEl.textContent = `Categoría #${ds.id}: ${ds.name}`;

                const isCurrentlyActive = ds.status === "activa";
                if (isCurrentlyActive) {
                    if (promptEl)
                        promptEl.textContent =
                            "¿Deseas desactivar esta categoría? No estará disponible para asociar nuevos productos.";
                    if (iconWrapper) {
                        iconWrapper.classList.remove("is-activating");
                        iconWrapper.innerHTML =
                            '<i class="bi bi-dash-circle"></i>';
                    }
                    if (confirmBtn) {
                        confirmBtn.className = "btn btn-danger";
                        confirmBtn.textContent = "Desactivar Categoría";
                    }
                } else {
                    if (promptEl)
                        promptEl.textContent =
                            "¿Deseas reactivar esta categoría en el catálogo?";
                    if (iconWrapper) {
                        iconWrapper.classList.add("is-activating");
                        iconWrapper.innerHTML =
                            '<i class="bi bi-check-circle"></i>';
                    }
                    if (confirmBtn) {
                        confirmBtn.className = "btn btn-success";
                        confirmBtn.textContent = "Reactivar Categoría";
                    }
                }
            },
        );
    }

    // Modal: Movimientos de Inventario y Cálculo de Stock Proyectado
    const inventoryMovementModal = document.getElementById(
        "inventoryMovementModal",
    );
    const movementProductSelect = document.getElementById("movement_product");
    const movementTypeSelect = document.getElementById("movement_type");
    const movementQuantity = document.getElementById("movement_quantity");
    const displayCurrentStock = document.getElementById("displayCurrentStock");
    const displayNewStock = document.getElementById("displayNewStock");
    const movementQtyHelper = document.getElementById("movementQtyHelper");

    function updateProjectedStock() {
        if (!movementProductSelect) return;

        const selectedOption =
            movementProductSelect.options[movementProductSelect.selectedIndex];
        if (!selectedOption || !selectedOption.value) {
            if (displayCurrentStock)
                displayCurrentStock.textContent = "0 unidades";
            if (displayNewStock) displayNewStock.textContent = "0 unidades";
            return;
        }

        const currentStock = parseInt(selectedOption.dataset.stock, 10) || 0;
        const qty =
            parseInt(movementQuantity ? movementQuantity.value : "1", 10) || 0;
        const type = movementTypeSelect ? movementTypeSelect.value : "";

        if (displayCurrentStock)
            displayCurrentStock.textContent = `${currentStock} unidades`;

        let projected = currentStock;

        if (type === "ENTRADA") {
            projected = currentStock + qty;
            if (movementQtyHelper)
                movementQtyHelper.textContent = `Se sumarán ${qty} unidades al stock disponible.`;
        } else if (type === "SALIDA") {
            projected = Math.max(0, currentStock - qty);
            if (movementQtyHelper)
                movementQtyHelper.textContent = `Se darán de baja ${qty} unidades del inventario.`;
        } else if (type === "AJUSTE") {
            projected = qty;
            if (movementQtyHelper)
                movementQtyHelper.textContent = `El inventario se fijará exactamente en ${qty} unidades tras el conteo físico.`;
        } else {
            if (movementQtyHelper)
                movementQtyHelper.textContent =
                    "Ingresa la cantidad en unidades";
        }

        if (displayNewStock)
            displayNewStock.textContent = `${projected} unidades`;
    }

    if (inventoryMovementModal) {
        inventoryMovementModal.addEventListener(
            "show.bs.modal",
            function (event) {
                const btn = event.relatedTarget;
                if (btn && btn.dataset.barcode && movementProductSelect) {
                    movementProductSelect.value = btn.dataset.barcode;
                }
                updateProjectedStock();
            },
        );
    }

    if (movementProductSelect)
        movementProductSelect.addEventListener("change", updateProjectedStock);
    if (movementTypeSelect)
        movementTypeSelect.addEventListener("change", updateProjectedStock);
    if (movementQuantity)
        movementQuantity.addEventListener("input", updateProjectedStock);
});
