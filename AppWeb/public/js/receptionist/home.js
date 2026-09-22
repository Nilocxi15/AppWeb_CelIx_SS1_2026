document.addEventListener('DOMContentLoaded', function () {
    // Notificaciones del Sistema (Bootstrap 5 Toasts)
    const toastEl = document.getElementById('receptionistToast');
    const toastMessage = document.getElementById('toastMessage');
    const toastIcon = document.getElementById('toastIcon');
    const toastInstance = toastEl && window.bootstrap ? bootstrap.Toast.getOrCreateInstance(toastEl) : null;

    function showToast(message, type = 'success') {
        if (!toastInstance) {
            alert(message);
            return;
        }

        if (toastIcon) {
            if (type === 'success') {
                toastIcon.className = 'bi bi-check-circle-fill text-success fs-5';
            } else if (type === 'danger') {
                toastIcon.className = 'bi bi-exclamation-octagon-fill text-danger fs-5';
            } else {
                toastIcon.className = 'bi bi-info-circle-fill text-primary fs-5';
            }
        }

        if (toastMessage) {
            toastMessage.textContent = message;
        }

        toastInstance.show();
    }

    // Filtros de Catálogo (Envío Automático al Backend)
    const catalogFilterForm = document.getElementById('catalogFilterForm');
    const categoryFilter = document.getElementById('categoryFilter');
    const stockFilter = document.getElementById('stockFilter');
    const perPageSelect = document.getElementById('perPageSelect');

    if (catalogFilterForm) {
        [categoryFilter, stockFilter, perPageSelect].forEach(select => {
            if (select) {
                select.addEventListener('change', function () {
                    catalogFilterForm.submit();
                });
            }
        });
    }

    // Modal Detalle de Producto (Aprovechamiento de Bootstrap show.bs.modal)
    const viewProductModal = document.getElementById('viewProductModal');
    const modalProductBarcode = document.getElementById('modalProductBarcode');
    const modalProductName = document.getElementById('modalProductName');
    const modalProductCategory = document.getElementById('modalProductCategory');
    const modalProductDescription = document.getElementById('modalProductDescription');
    const modalProductPrice = document.getElementById('modalProductPrice');
    const modalProductMinStock = document.getElementById('modalProductMinStock');
    const modalProductStockBadge = document.getElementById('modalProductStockBadge');
    const modalProductImageContainer = document.getElementById('modalProductImageContainer');
    const btnModalAddSale = document.getElementById('btnModalAddSale');

    if (viewProductModal) {
        viewProductModal.addEventListener('show.bs.modal', function (event) {
            const btn = event.relatedTarget;
            if (!btn) return;

            const barcode     = btn.getAttribute('data-bs-barcode') || '';
            const name        = btn.getAttribute('data-bs-name') || '';
            const category    = btn.getAttribute('data-bs-category') || '';
            const description = btn.getAttribute('data-bs-description') || '';
            const price       = btn.getAttribute('data-bs-price') || '0.00';
            const stock       = parseInt(btn.getAttribute('data-bs-stock'), 10) || 0;
            const minStock    = parseInt(btn.getAttribute('data-bs-minstock'), 10) || 0;
            const image       = btn.getAttribute('data-bs-image') || '';

            if (modalProductBarcode) modalProductBarcode.innerHTML = `<i class="bi bi-upc me-1"></i>${barcode}`;
            if (modalProductName) modalProductName.textContent = name;
            if (modalProductCategory) modalProductCategory.textContent = category;
            if (modalProductDescription) modalProductDescription.textContent = description;
            if (modalProductPrice) modalProductPrice.textContent = `Q ${price}`;
            if (modalProductMinStock) modalProductMinStock.textContent = `${minStock} unidades`;

            if (modalProductStockBadge) {
                if (stock <= 0) {
                    modalProductStockBadge.innerHTML = '<span class="badge badge-stock-out"><i class="bi bi-x-circle me-1"></i>Agotado (0 unidades)</span>';
                } else if (stock <= minStock) {
                    modalProductStockBadge.innerHTML = `<span class="badge badge-stock-low"><i class="bi bi-exclamation-triangle me-1"></i>Bajo (${stock} unidades)</span>`;
                } else {
                    modalProductStockBadge.innerHTML = `<span class="badge badge-stock-ok"><i class="bi bi-check2 me-1"></i>${stock} unidades en tienda</span>`;
                }
            }

            if (modalProductImageContainer) {
                modalProductImageContainer.innerHTML = image
                    ? `<img src="${image}" alt="${name}" class="img-fluid rounded" style="max-height: 180px; object-fit: contain;">`
                    : '<i class="bi bi-box-seam fs-1 text-secondary"></i>';
            }

            // Transferir código de barras al botón de "Agregar a la Venta" dentro del modal
            if (btnModalAddSale) {
                btnModalAddSale.setAttribute('data-bs-barcode', barcode);
                btnModalAddSale.disabled = (stock <= 0);
            }
        });
    }

    // Modal Registrar Venta (Punto de Venta Asíncrono)
    const registerSaleModal = document.getElementById('registerSaleModal');
    const saleProductSelector = document.getElementById('saleProductSelector');
    const saleProductQuantity = document.getElementById('saleProductQuantity');
    const btnAddProductToSale = document.getElementById('btnAddProductToSale');
    const saleItemsTableBody = document.getElementById('saleItemsTableBody');
    const summaryTotalItems = document.getElementById('summaryTotalItems');
    const summarySubtotal = document.getElementById('summarySubtotal');
    const summaryGrandTotal = document.getElementById('summaryGrandTotal');
    const amountReceived = document.getElementById('amountReceived');
    const changeToReturn = document.getElementById('changeToReturn');
    const paymentMethod = document.getElementById('paymentMethod');
    const cashPaymentFields = document.getElementById('cashPaymentFields');
    const btnSubmitSale = document.getElementById('btnSubmitSale');
    const registerSaleForm = document.getElementById('registerSaleForm');

    let currentSaleItems = [];

    // Preseleccionar producto al abrir el modal desde botón de venta
    if (registerSaleModal) {
        registerSaleModal.addEventListener('show.bs.modal', function (event) {
            const btn = event.relatedTarget;
            if (btn && btn.getAttribute('data-bs-barcode') && saleProductSelector) {
                saleProductSelector.value = btn.getAttribute('data-bs-barcode');
                if (saleProductQuantity) saleProductQuantity.value = 1;
            }
        });
    }

    function updateSaleCalculations() {
        let totalQty = 0;
        let grandTotal = 0;

        currentSaleItems.forEach(item => {
            totalQty += item.quantity;
            grandTotal += (item.quantity * item.price);
        });

        if (summaryTotalItems) summaryTotalItems.textContent = totalQty;
        if (summarySubtotal) summarySubtotal.textContent = `Q ${grandTotal.toFixed(2)}`;
        if (summaryGrandTotal) summaryGrandTotal.textContent = `Q ${grandTotal.toFixed(2)}`;

        if (amountReceived && changeToReturn) {
            const received = parseFloat(amountReceived.value) || 0;
            const change = Math.max(0, received - grandTotal);
            changeToReturn.textContent = `Q ${change.toFixed(2)}`;
        }

        if (btnSubmitSale) {
            btnSubmitSale.disabled = currentSaleItems.length === 0;
        }
    }

    function renderSaleItemsTable() {
        if (!saleItemsTableBody) return;

        saleItemsTableBody.innerHTML = '';

        if (currentSaleItems.length === 0) {
            saleItemsTableBody.innerHTML = `
                <tr id="saleEmptyRow">
                    <td colspan="6" class="text-center py-4 text-muted">
                        <i class="bi bi-cart-x fs-3 d-block mb-1 text-secondary"></i>
                        <span>No hay productos agregados a la venta todavía.</span>
                    </td>
                </tr>
            `;
            updateSaleCalculations();
            return;
        }

        currentSaleItems.forEach((item, index) => {
            const subtotal = item.quantity * item.price;
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="ps-3 font-monospace small text-muted">${item.barcode}</td>
                <td>
                    <strong class="text-dark d-block">${item.name}</strong>
                    <span class="small text-muted">Stock disponible: ${item.stock}</span>
                </td>
                <td class="text-center" style="max-width: 90px;">
                    <input type="number" class="form-control form-control-sm text-center sale-item-qty-input"
                           min="1" max="${item.stock}" value="${item.quantity}" data-index="${index}">
                </td>
                <td class="text-end text-nowrap">Q ${item.price.toFixed(2)}</td>
                <td class="text-end fw-bold text-dark text-nowrap">Q ${subtotal.toFixed(2)}</td>
                <td class="text-center pe-3">
                    <button type="button" class="btn btn-sm btn-outline-danger border-0 btn-remove-sale-item" data-index="${index}" title="Quitar este artículo">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
            saleItemsTableBody.appendChild(tr);
        });

        updateSaleCalculations();
    }

    // Agregar producto al carrito de venta
    if (btnAddProductToSale && saleProductSelector && saleProductQuantity) {
        btnAddProductToSale.addEventListener('click', function () {
            const selectedOpt = saleProductSelector.options[saleProductSelector.selectedIndex];
            if (!selectedOpt || !selectedOpt.value) {
                showToast('Por favor, selecciona un producto del catálogo.', 'danger');
                return;
            }

            const barcode  = selectedOpt.value;
            const name     = selectedOpt.dataset.name || 'Producto';
            const price    = parseFloat(selectedOpt.dataset.price) || 0;
            const stock    = parseInt(selectedOpt.dataset.stock, 10) || 0;
            const quantity = parseInt(saleProductQuantity.value, 10) || 1;

            if (quantity <= 0) {
                showToast('La cantidad debe ser de al menos 1 unidad.', 'danger');
                return;
            }

            if (stock <= 0) {
                showToast('El producto seleccionado se encuentra agotado.', 'danger');
                return;
            }

            const existingIndex = currentSaleItems.findIndex(i => i.barcode === barcode);
            if (existingIndex >= 0) {
                const newTotal = currentSaleItems[existingIndex].quantity + quantity;
                if (newTotal > stock) {
                    showToast(`La cantidad excede el stock disponible (${stock} unidades).`, 'danger');
                    return;
                }
                currentSaleItems[existingIndex].quantity = newTotal;
            } else {
                if (quantity > stock) {
                    showToast(`La cantidad (${quantity}) supera el stock (${stock}).`, 'danger');
                    return;
                }
                currentSaleItems.push({ barcode, name, price, quantity, stock });
            }

            renderSaleItemsTable();
            saleProductQuantity.value = 1;
        });
    }

    // Delegación de eventos en tabla de venta (cambio de cantidad y eliminar)
    if (saleItemsTableBody) {
        saleItemsTableBody.addEventListener('change', function (e) {
            if (e.target.classList.contains('sale-item-qty-input')) {
                const index = parseInt(e.target.dataset.index, 10);
                const item = currentSaleItems[index];
                if (!item) return;

                let val = parseInt(e.target.value, 10) || 1;
                if (val > item.stock) {
                    showToast(`Stock máximo disponible: ${item.stock}`, 'danger');
                    val = item.stock;
                    e.target.value = val;
                } else if (val < 1) {
                    val = 1;
                    e.target.value = 1;
                }
                item.quantity = val;
                renderSaleItemsTable();
            }
        });

        saleItemsTableBody.addEventListener('click', function (e) {
            const removeBtn = e.target.closest('.btn-remove-sale-item');
            if (removeBtn) {
                const index = parseInt(removeBtn.dataset.index, 10);
                if (index >= 0 && index < currentSaleItems.length) {
                    currentSaleItems.splice(index, 1);
                    renderSaleItemsTable();
                }
            }
        });
    }

    // Método de pago y cálculo de cambio
    if (paymentMethod && cashPaymentFields) {
        paymentMethod.addEventListener('change', function () {
            cashPaymentFields.style.display = (this.value === 'EFECTIVO') ? 'block' : 'none';
        });
    }

    if (amountReceived) {
        amountReceived.addEventListener('input', updateSaleCalculations);
    }

    // Envío del formulario de venta al backend
    if (registerSaleForm) {
        registerSaleForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            if (currentSaleItems.length === 0) {
                showToast('Debes agregar al menos un artículo para registrar la venta.', 'danger');
                return;
            }

            const paymentMethodVal = paymentMethod ? paymentMethod.value : 'EFECTIVO';
            const amountReceivedVal = amountReceived ? parseFloat(amountReceived.value) || 0 : 0;
            const submitBtn = btnSubmitSale || registerSaleForm.querySelector('button[type="submit"]');

            const payload = {
                items: currentSaleItems.map(item => ({
                    barcode: item.barcode,
                    quantity: parseInt(item.quantity, 10)
                })),
                payment_method: paymentMethodVal,
                amount_received: amountReceivedVal
            };

            const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '';
            const originalBtnHtml = submitBtn ? submitBtn.innerHTML : '';

            try {
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Procesando...';
                }

                const endpoint = registerSaleForm.getAttribute('action') || '/receptionist/sales';

                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (!response.ok) {
                    let errorMsg = data.message || 'Error al procesar la venta.';
                    if (data.errors) {
                        errorMsg += ' ' + Object.values(data.errors).flat().join(' ');
                    }
                    throw new Error(errorMsg);
                }

                showToast(data.message || '¡Venta completada con éxito!', 'success');
                currentSaleItems = [];
                renderSaleItemsTable();
                registerSaleForm.reset();

                const modalInst = bootstrap.Modal.getInstance(registerSaleModal);
                if (modalInst) modalInst.hide();

                // Recargar página para actualizar stock y KPIs en tiempo real
                setTimeout(() => window.location.reload(), 1000);
            } catch (err) {
                showToast(err.message, 'danger');
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = currentSaleItems.length === 0;
                    submitBtn.innerHTML = originalBtnHtml;
                }
            }
        });
    }

    // Modal Registrar Dispositivo (Interacción Dinámica: Cliente, Saldo Económico y Técnico)
    const registerDeviceForm = document.getElementById('registerDeviceForm');
    const registerDeviceModalEl = document.getElementById('registerDeviceModal');

    if (registerDeviceForm) {
        const clientModeNew = document.getElementById('client_mode_new');
        const clientModeExisting = document.getElementById('client_mode_existing');
        const existingClientWrapper = document.getElementById('existingClientWrapper');
        const existingClientSelect = document.getElementById('existing_client_id');
        const clientStatusBadge = document.getElementById('clientSelectionStatusBadge');

        const clientNameInput = document.getElementById('client_name');
        const clientLastnameInput = document.getElementById('client_lastname');
        const clientPhoneInput = document.getElementById('client_phone');
        const clientDpiInput = document.getElementById('client_dpi');

        const totalChargedInput = document.getElementById('total_charged');
        const depositInput = document.getElementById('deposit');
        const remainingBalanceDisplay = document.getElementById('remainingBalanceDisplay');

        function setClientInputsReadonly(readonly) {
            [clientNameInput, clientLastnameInput, clientPhoneInput, clientDpiInput].forEach(input => {
                if (!input) return;
                input.readOnly = readonly;
                input.classList.toggle('bg-light', readonly);
            });
        }

        function clearClientInputs() {
            [clientNameInput, clientLastnameInput, clientPhoneInput, clientDpiInput].forEach(input => {
                if (input) input.value = '';
            });
        }

        if (clientModeNew && clientModeExisting) {
            clientModeNew.addEventListener('change', function () {
                if (this.checked) {
                    if (existingClientWrapper) existingClientWrapper.classList.add('d-none');
                    if (existingClientSelect) {
                        existingClientSelect.value = '';
                        existingClientSelect.required = false;
                    }
                    setClientInputsReadonly(false);
                    clearClientInputs();
                    if (clientStatusBadge) {
                        clientStatusBadge.innerHTML = '<i class="bi bi-plus-circle me-1 text-primary"></i>Nuevo Registro';
                        clientStatusBadge.className = 'badge bg-light text-muted border';
                    }
                }
            });

            clientModeExisting.addEventListener('change', function () {
                if (this.checked) {
                    if (existingClientWrapper) existingClientWrapper.classList.remove('d-none');
                    if (existingClientSelect) existingClientSelect.required = true;
                    setClientInputsReadonly(true);
                    clearClientInputs();
                    if (clientStatusBadge) {
                        clientStatusBadge.innerHTML = '<i class="bi bi-person-check me-1 text-secondary"></i>Selecciona un cliente';
                        clientStatusBadge.className = 'badge bg-secondary-subtle text-secondary border';
                    }
                }
            });
        }

        if (existingClientSelect) {
            existingClientSelect.addEventListener('change', function () {
                const selectedOpt = this.options[this.selectedIndex];
                if (!selectedOpt || !selectedOpt.value) {
                    clearClientInputs();
                    return;
                }

                if (clientNameInput) clientNameInput.value = selectedOpt.getAttribute('data-name') || '';
                if (clientLastnameInput) clientLastnameInput.value = selectedOpt.getAttribute('data-lastname') || '';
                if (clientPhoneInput) clientPhoneInput.value = selectedOpt.getAttribute('data-phone') || '';
                if (clientDpiInput) clientDpiInput.value = selectedOpt.getAttribute('data-dpi') || '';

                setClientInputsReadonly(true);

                if (clientStatusBadge) {
                    clientStatusBadge.innerHTML = '<i class="bi bi-check-circle-fill me-1 text-success"></i>Cliente Seleccionado';
                    clientStatusBadge.className = 'badge bg-success-subtle text-success border border-success-subtle';
                }
            });
        }

        // Cálculo dinámico del saldo restante por pagar
        function updateRemainingBalance() {
            const cost = parseFloat(totalChargedInput ? totalChargedInput.value : 0) || 0;
            const deposit = parseFloat(depositInput ? depositInput.value : 0) || 0;
            const balance = Math.max(0, cost - deposit);

            if (remainingBalanceDisplay) {
                remainingBalanceDisplay.textContent = `Q ${balance.toFixed(2)}`;
            }
        }

        if (totalChargedInput) totalChargedInput.addEventListener('input', updateRemainingBalance);
        if (depositInput) depositInput.addEventListener('input', updateRemainingBalance);

        const btnSubmitDeviceIntake = document.getElementById('btnSubmitDeviceIntake');

        // Envío y Validación del Formulario al Backend
        registerDeviceForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            if (!this.checkValidity()) {
                e.stopPropagation();
                this.classList.add('was-validated');
                showToast('Por favor, completa correctamente todos los campos obligatorios.', 'danger');
                return;
            }

            const totalCharged = parseFloat(totalChargedInput ? totalChargedInput.value : 0) || 0;
            const deposit = parseFloat(depositInput ? depositInput.value : 0) || 0;

            if (deposit > totalCharged) {
                showToast('El anticipo abonado no puede ser superior al costo del trabajo.', 'danger');
                if (depositInput) depositInput.focus();
                return;
            }

            const formData = new FormData(this);
            const endpoint = this.getAttribute('action') || '/receptionist/reception/dispositivos';

            const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '';

            const submitBtn = btnSubmitDeviceIntake || this.querySelector('button[type="submit"]');
            const originalBtnHtml = submitBtn ? submitBtn.innerHTML : '';

            try {
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Registrando...';
                }

                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData
                });

                const data = await response.json();

                if (!response.ok) {
                    let errorMsg = data.message || 'Error al registrar la recepción del dispositivo.';
                    if (data.errors) {
                        const errorList = Object.values(data.errors).flat().join(' ');
                        errorMsg += ' ' + errorList;
                    }
                    throw new Error(errorMsg);
                }

                showToast(data.message || '¡Dispositivo y Ticket registrados exitosamente! Generando PDF...', 'success');

                // Descargar o abrir automáticamente el PDF del ticket generado
                if (data.pdf_url) {
                    const downloadLink = document.createElement('a');
                    downloadLink.href = data.pdf_url;
                    downloadLink.target = '_blank';
                    downloadLink.rel = 'noopener noreferrer';
                    document.body.appendChild(downloadLink);
                    downloadLink.click();
                    document.body.removeChild(downloadLink);
                }

                registerDeviceForm.reset();
                registerDeviceForm.classList.remove('was-validated');
                setClientInputsReadonly(false);
                if (existingClientWrapper) existingClientWrapper.classList.add('d-none');
                if (clientModeNew) clientModeNew.checked = true;
                if (clientStatusBadge) {
                    clientStatusBadge.innerHTML = '<i class="bi bi-plus-circle me-1 text-primary"></i>Nuevo Registro';
                    clientStatusBadge.className = 'badge bg-light text-muted border';
                }
                updateRemainingBalance();

                const modalInst = bootstrap.Modal.getInstance(registerDeviceModalEl);
                if (modalInst) modalInst.hide();

                // Poblar y desplegar modal con el código QR generado
                const qrModalEl = document.getElementById('qrGeneratedModal');
                if (qrModalEl && window.bootstrap) {
                    const qrModalFolioText = document.getElementById('qrModalFolioText');
                    const qrModalDeviceText = document.getElementById('qrModalDeviceText');
                    const qrModalClientText = document.getElementById('qrModalClientText');
                    const qrModalSvgContainer = document.getElementById('qrModalSvgContainer');
                    const qrModalDownloadPdfBtn = document.getElementById('qrModalDownloadPdfBtn');
                    const qrModalTrackingUrlBtn = document.getElementById('qrModalTrackingUrlBtn');

                    if (qrModalFolioText) qrModalFolioText.textContent = `#TK-${String(data.ticket?.id || 0).padStart(4, '0')}`;
                    if (qrModalDeviceText) qrModalDeviceText.textContent = data.ticket?.device || 'Dispositivo';
                    if (qrModalClientText) qrModalClientText.textContent = `Cliente: ${data.ticket?.client || 'Registrado'}`;
                    if (qrModalSvgContainer && data.qr_svg) qrModalSvgContainer.innerHTML = data.qr_svg;
                    if (qrModalDownloadPdfBtn && data.pdf_url) qrModalDownloadPdfBtn.href = data.pdf_url;
                    if (qrModalTrackingUrlBtn && data.tracking_url) qrModalTrackingUrlBtn.href = data.tracking_url;

                    bootstrap.Modal.getOrCreateInstance(qrModalEl).show();
                }

            } catch (err) {
                showToast(err.message, 'danger');
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnHtml;
                }
            }
        });

        // Limpiar estado al cerrar modal
        if (registerDeviceModalEl) {
            registerDeviceModalEl.addEventListener('hidden.bs.modal', function () {
                registerDeviceForm.reset();
                registerDeviceForm.classList.remove('was-validated');
                setClientInputsReadonly(false);
                if (existingClientWrapper) existingClientWrapper.classList.add('d-none');
                if (clientModeNew) clientModeNew.checked = true;
                if (clientStatusBadge) {
                    clientStatusBadge.innerHTML = '<i class="bi bi-plus-circle me-1 text-primary"></i>Nuevo Registro';
                    clientStatusBadge.className = 'badge bg-light text-muted border';
                }
                updateRemainingBalance();
            });
        }
    }
});