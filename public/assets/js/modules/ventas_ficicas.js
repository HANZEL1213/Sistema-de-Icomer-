document.addEventListener('DOMContentLoaded', function () {
    const productos = window.VentaConfig?.productosBackend || [];

    const productosBody = document.getElementById('productosBody');
    const emptyState = document.getElementById('emptyState');
    const subtotalLabel = document.getElementById('subtotalLabel');
    const totalLabel = document.getElementById('totalLabel');
    const descuentoInput = document.getElementById('descuentoInput');
    const inputSubtotalHidden = document.getElementById('inputSubtotal');
    const inputTotalHidden = document.getElementById('inputTotal');
    const totalProductosCount = document.getElementById('totalProductosCount');
    const productosDistintosSpan = document.getElementById('productosDistintosCount');
    const searchInput = document.getElementById('searchProduct');
    const searchResults = document.getElementById('searchResults');
    const productQuantity = document.getElementById('productQuantity');
    const btnAddProduct = document.getElementById('btnAddProduct');
    const detalleInputsContainer = document.getElementById('detalleInputsContainer');
    const pagosInputsContainer = document.getElementById('pagosInputsContainer');

    const btnProcesar = document.getElementById('btnProcesar');
    const modalPagoEl = document.getElementById('modalPago');
    const modalTotalLabel = document.getElementById('modalTotalLabel');
    const metodoPagoInput = document.getElementById('metodoPagoInput');
    const metodoBtns = document.querySelectorAll('.metodo-btn');
    const efectivoDiv = document.getElementById('efectivoFields');
    const referenciaDiv = document.getElementById('referenciaFields');
    const mixtoDiv = document.getElementById('mixtoFields');
    const inputRecibido = document.getElementById('montoRecibido');
    const vueltoLabel = document.getElementById('vueltoLabel');
    const referenciaPago = document.getElementById('referenciaPago');
    const mixtoEfectivo = document.getElementById('mixtoEfectivo');
    const mixtoDigital = document.getElementById('mixtoDigital');
    const mixtoReferencia = document.getElementById('mixtoReferencia');
    const mixtoWarning = document.getElementById('mixtoWarning');
    const mixtoVueltoInfo = document.getElementById('mixtoVueltoInfo');
    const mixtoVueltoSpan = document.getElementById('mixtoVueltoSpan');

    const formVenta = document.getElementById('formVenta');
    const modalPago = new bootstrap.Modal(modalPagoEl);

    let searchTimeout = null;
    let currentProductSelected = null;
    let metodoActual = 'efectivo';
    let selectedIndex = -1;

    function escapeHtml(text) {
        if (!text) return '';
        return String(text).replace(/[&<>"]/g, function (m) {
            return ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;'
            })[m];
        });
    }

    function formatMoney(value) {
        return Number(value || 0).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function searchProducts(query) {
        if (!query.trim()) {
            searchResults.style.display = 'none';
            currentProductSelected = null;
            return;
        }

        const filtered = productos.filter(p =>
            (p.nombre || '').toLowerCase().includes(query.toLowerCase()) ||
            (p.codigo_barras || '').toLowerCase().includes(query.toLowerCase()) ||
            (p.sku || '').toLowerCase().includes(query.toLowerCase())
        );

        if (filtered.length === 0) {
            searchResults.innerHTML =
                '<div class="list-group-item text-muted text-center"><i class="bx bx-search-alt"></i> No se encontraron productos</div>';
            searchResults.style.display = 'block';
            selectedIndex = -1;
            return;
        }

     searchResults.innerHTML = filtered.map(p => `
    <a href="#" class="list-group-item list-group-item-action" data-product='${JSON.stringify(p)}'>
        <div class="search-product-item">

            ${p.imagen_url ? `
                <img 
                    src="${escapeHtml(p.imagen_url)}" 
                    alt="${escapeHtml(p.nombre)}" 
                    class="search-product-img"
                >
            ` : `
                <div class="search-product-img search-product-no-img">
                    <i class="bx bx-image"></i>
                </div>
            `}

            <div class="search-product-info">
                <strong>${escapeHtml(p.nombre)}</strong>
                ${(p.codigo_barras || p.sku) ? `<br><small class="text-muted">${escapeHtml(p.codigo_barras || p.sku)}</small>` : ''}
            </div>

            <div class="search-product-price">
                <span class="badge bg-primary">₡ ${formatMoney(p.precio_venta)}</span>
                <br><small class="text-muted">Stock: ${p.stock}</small>
            </div>

        </div>
    </a>
`).join('');

        searchResults.style.display = 'block';
selectedIndex = -1;
        document.querySelectorAll('.list-group-item[data-product]').forEach(el => {
            el.addEventListener('click', function (e) {
                e.preventDefault();
                const p = JSON.parse(this.dataset.product);
                currentProductSelected = p;
                addProductFromSearch(p, parseInt(productQuantity.value) || 1);
                searchInput.value = '';
                searchResults.style.display = 'none';
                productQuantity.value = 1;
                currentProductSelected = null;
            });
        });
    }
function updateSelection() {
    const items = searchResults.querySelectorAll('.list-group-item[data-product]');

    items.forEach((item, index) => {
        item.classList.toggle('search-selected', index === selectedIndex);

        if (index === selectedIndex) {
            item.scrollIntoView({
                block: 'nearest'
            });
        }
    });
}
    function addProductFromSearch(productData, cantidad = 1) {
        if (!productData) return false;

        if (productData.stock < cantidad) {
            Swal.fire({
                icon: 'error',
                title: 'Stock insuficiente',
                text: `Solo hay ${productData.stock} unidades disponibles.`,
            });
            return false;
        }

        const existingRows = productosBody.querySelectorAll('tr');
        let existingRow = null;

        existingRows.forEach(row => {
            if (row.dataset.productId === String(productData.id)) {
                existingRow = row;
            }
        });

        if (existingRow) {
            const qtyInput = existingRow.querySelector('.input-cantidad');
            const nuevaCantidad = (parseInt(qtyInput.value) || 0) + cantidad;

            if (nuevaCantidad > productData.stock) {
                Swal.fire({
                    icon: 'error',
                    title: 'Stock insuficiente',
                    text: `Stock disponible: ${productData.stock}`,
                });
                return false;
            }

            qtyInput.value = nuevaCantidad;
            calcularTotales();
            existingRow.style.backgroundColor = '#d4edda';
            setTimeout(() => existingRow.style.backgroundColor = '', 500);
            return true;
        }

        emptyState.classList.add('d-none');

        const row = document.createElement('tr');
        row.dataset.productId = String(productData.id);
        row.dataset.stock = String(productData.stock);

        row.innerHTML = `
            <td class="text-start">
                <div class="fw-semibold">${escapeHtml(productData.nombre)}</div>
                <small class="text-muted">Stock disponible: ${productData.stock}</small>
            </td>
            <td>
                <small class="text-muted">${escapeHtml(productData.codigo_barras || productData.sku || 'N/A')}</small>
            </td>
            <td>
                <input type="number"
                    class="form-control form-control-sm input-precio"
                    value="${Number(productData.precio_venta).toFixed(2)}"
                    readonly>
            </td>
            <td>
                <input type="number"
                    class="form-control form-control-sm input-cantidad"
                    value="${cantidad}"
                    min="1"
                    max="${productData.stock}">
            </td>
            <td>
                <span class="fw-bold">₡ <span class="label-fila-total">${formatMoney(Number(productData.precio_venta) * cantidad)}</span></span>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-outline-danger btn-remove">
                    <i class="bx bx-trash"></i>
                </button>
            </td>
        `;

        productosBody.appendChild(row);
        calcularTotales();
        row.scrollIntoView({ behavior: 'smooth', block: 'center' });

        return true;
    }

    function rebuildDetalleInputs() {
        detalleInputsContainer.innerHTML = '';

        const rows = productosBody.querySelectorAll('tr');

        rows.forEach((row, index) => {
            const productId = row.dataset.productId;
            const qty = row.querySelector('.input-cantidad')?.value || 1;

            detalleInputsContainer.insertAdjacentHTML('beforeend', `
                <input type="hidden" name="detalle[${index}][id_producto]" value="${productId}">
                <input type="hidden" name="detalle[${index}][cantidad]" value="${qty}">
            `);
        });
    }

    function calcularTotales() {
        let subtotal = 0;
        let totalUnidades = 0;

        document.querySelectorAll('#productosBody tr').forEach(row => {
            const precio = parseFloat(row.querySelector('.input-precio')?.value) || 0;
            const cantidad = parseInt(row.querySelector('.input-cantidad')?.value) || 0;
            const totalFila = precio * cantidad;

            const totalSpan = row.querySelector('.label-fila-total');
            if (totalSpan) {
                totalSpan.innerText = formatMoney(totalFila);
            }

            subtotal += totalFila;
            totalUnidades += cantidad;
        });

        const descuento = parseFloat(descuentoInput.value) || 0;
        const total = Math.max(0, subtotal - descuento);

        subtotalLabel.innerText = formatMoney(subtotal);
        totalLabel.innerText = formatMoney(total);

        totalProductosCount.innerText = totalUnidades;
        productosDistintosSpan.innerText = document.querySelectorAll('#productosBody tr').length;

        inputSubtotalHidden.value = subtotal.toFixed(2);
        inputTotalHidden.value = total.toFixed(2);

        rebuildDetalleInputs();
    }

    function actualizarVistaMetodo(metodo) {
        metodoActual = metodo;
        metodoPagoInput.value = metodo;

        metodoBtns.forEach(btn => {
            btn.classList.toggle('active', btn.dataset.metodo === metodo);
        });

        efectivoDiv.classList.add('d-none');
        referenciaDiv.classList.add('d-none');
        mixtoDiv.classList.add('d-none');

        if (metodo === 'efectivo') {
            efectivoDiv.classList.remove('d-none');
        } else if (metodo === 'tarjeta' || metodo === 'sinpe') {
            referenciaDiv.classList.remove('d-none');
        } else if (metodo === 'mixto') {
            mixtoDiv.classList.remove('d-none');
            actualizarMixtoVuelto();
        }
    }

    function actualizarMixtoVuelto() {
        const total = parseFloat(inputTotalHidden.value) || 0;
        const efectivo = parseFloat(mixtoEfectivo.value) || 0;
        const digital = parseFloat(mixtoDigital.value) || 0;
        const suma = efectivo + digital;

        if (suma > 0 && suma < total) {
            mixtoWarning.innerHTML = `<i class="bx bx-error-circle"></i> Faltan ₡ ${formatMoney(total - suma)} para completar el total.`;
            mixtoVueltoInfo.classList.add('d-none');
            return;
        }

        if (suma >= total && suma > 0) {
            mixtoWarning.innerHTML = `<i class="bx bx-check-circle text-success"></i> Pago completo.`;

            const vueltoEfectivo = efectivo - Math.max(0, total - digital);

            if (vueltoEfectivo > 0) {
                mixtoVueltoSpan.innerText = formatMoney(vueltoEfectivo);
                mixtoVueltoInfo.classList.remove('d-none');
            } else {
                mixtoVueltoInfo.classList.add('d-none');
            }
            return;
        }

        mixtoWarning.innerHTML = '';
        mixtoVueltoInfo.classList.add('d-none');
    }

    function rebuildPagosInputs() {
        pagosInputsContainer.innerHTML = '';

        const total = parseFloat(inputTotalHidden.value) || 0;

        if (metodoActual === 'efectivo') {
            pagosInputsContainer.insertAdjacentHTML('beforeend', `
                <input type="hidden" name="pagos[0][metodo]" value="efectivo">
                <input type="hidden" name="pagos[0][monto]" value="${total.toFixed(2)}">
                <input type="hidden" name="pagos[0][referencia]" value="">
            `);
            return;
        }

        if (metodoActual === 'tarjeta' || metodoActual === 'sinpe') {
            pagosInputsContainer.insertAdjacentHTML('beforeend', `
                <input type="hidden" name="pagos[0][metodo]" value="${metodoActual}">
                <input type="hidden" name="pagos[0][monto]" value="${total.toFixed(2)}">
                <input type="hidden" name="pagos[0][referencia]" value="${escapeHtml(referenciaPago.value || '')}">
            `);
            return;
        }

        if (metodoActual === 'mixto') {
            const efectivo = parseFloat(mixtoEfectivo.value) || 0;
            const digital = parseFloat(mixtoDigital.value) || 0;
            let idx = 0;

            if (efectivo > 0) {
                pagosInputsContainer.insertAdjacentHTML('beforeend', `
                    <input type="hidden" name="pagos[${idx}][metodo]" value="efectivo">
                    <input type="hidden" name="pagos[${idx}][monto]" value="${efectivo.toFixed(2)}">
                    <input type="hidden" name="pagos[${idx}][referencia]" value="">
                `);
                idx++;
            }

            if (digital > 0) {
                pagosInputsContainer.insertAdjacentHTML('beforeend', `
                    <input type="hidden" name="pagos[${idx}][metodo]" value="mixto">
                    <input type="hidden" name="pagos[${idx}][monto]" value="${digital.toFixed(2)}">
                    <input type="hidden" name="pagos[${idx}][referencia]" value="${escapeHtml(mixtoReferencia.value || '')}">
                `);
            }
        }
    }

searchInput?.addEventListener('input', function () {
    selectedIndex = -1;
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => searchProducts(this.value), 250);
});

    btnAddProduct?.addEventListener('click', () => {
        if (currentProductSelected) {
            addProductFromSearch(currentProductSelected, parseInt(productQuantity.value) || 1);
            searchInput.value = '';
            searchResults.style.display = 'none';
            productQuantity.value = 1;
            currentProductSelected = null;
            return;
        }

        if (searchInput.value.trim()) {
            const firstResult = document.querySelector('.list-group-item[data-product]');
            if (firstResult) {
                firstResult.click();
                return;
            }
        }

        Swal.fire({
            icon: 'info',
            title: 'Buscar producto',
            text: 'Escribe el nombre, código o SKU del producto.',
        });
    });

    document.getElementById('btnClearSearch')?.addEventListener('click', () => {
        searchInput.value = '';
        searchResults.style.display = 'none';
        searchInput.focus();
    });

 searchInput?.addEventListener('keydown', e => {
    const items = searchResults.querySelectorAll('.list-group-item[data-product]');

    if (!items.length) return;

    if (e.key === 'ArrowDown') {
        e.preventDefault();

        selectedIndex++;

        if (selectedIndex >= items.length) {
            selectedIndex = 0;
        }

        updateSelection();

    } else if (e.key === 'ArrowUp') {
        e.preventDefault();

        selectedIndex--;

        if (selectedIndex < 0) {
            selectedIndex = items.length - 1;
        }

        updateSelection();

    } else if (e.key === 'Enter') {
        e.preventDefault();

        if (selectedIndex >= 0) {
            items[selectedIndex].click();
        } else {
            items[0].click();
        }
    }
});

    document.addEventListener('click', e => {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });

    productosBody?.addEventListener('click', e => {
        if (e.target.closest('.btn-remove')) {
            e.target.closest('tr').remove();

            if (!productosBody.children.length) {
                emptyState.classList.remove('d-none');
            }

            calcularTotales();
        }
    });

    productosBody?.addEventListener('input', e => {
        if (e.target.classList.contains('input-cantidad')) {
            const row = e.target.closest('tr');
            const stock = parseInt(row.dataset.stock) || 0;
            let cantidad = parseInt(e.target.value) || 1;

            if (cantidad < 1) cantidad = 1;
            if (cantidad > stock) cantidad = stock;

            e.target.value = cantidad;
            calcularTotales();
        }
    });

    descuentoInput?.addEventListener('input', calcularTotales);

    metodoBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            actualizarVistaMetodo(btn.dataset.metodo);
        });
    });

    mixtoEfectivo?.addEventListener('input', actualizarMixtoVuelto);
    mixtoDigital?.addEventListener('input', actualizarMixtoVuelto);

    inputRecibido?.addEventListener('input', function () {
        const total = parseFloat(inputTotalHidden.value) || 0;
        const recibido = parseFloat(this.value) || 0;
        const vuelto = Math.max(0, recibido - total);
        vueltoLabel.innerText = formatMoney(vuelto);
    });

    btnProcesar?.addEventListener('click', () => {
        const total = parseFloat(inputTotalHidden.value) || 0;
        const cantidadRows = productosBody.querySelectorAll('tr').length;

        if (cantidadRows === 0 || total <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Venta inválida',
                text: 'Agrega al menos un producto válido.',
            });
            return;
        }

        modalTotalLabel.innerText = totalLabel.innerText;
        inputRecibido.value = '';
        vueltoLabel.innerText = '0.00';
        referenciaPago.value = '';
        mixtoEfectivo.value = '';
        mixtoDigital.value = '';
        mixtoReferencia.value = '';
        mixtoWarning.innerHTML = '';
        mixtoVueltoInfo.classList.add('d-none');

        actualizarVistaMetodo('efectivo');
        modalPago.show();
    });

    formVenta?.addEventListener('submit', function (e) {
        const total = parseFloat(inputTotalHidden.value) || 0;

        if (productosBody.querySelectorAll('tr').length === 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Sin productos',
                text: 'Debe agregar al menos un producto.',
            });
            return false;
        }

        if (metodoActual === 'efectivo') {
            const recibido = parseFloat(inputRecibido.value) || 0;

            if (recibido < total) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Monto insuficiente',
                    text: 'El monto recibido debe ser mayor o igual al total.',
                });
                return false;
            }
        }

        if (metodoActual === 'tarjeta' || metodoActual === 'sinpe') {
            if (!String(referenciaPago.value || '').trim()) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Referencia requerida',
                    text: 'Ingrese el número de comprobante.',
                });
                return false;
            }
        }

        if (metodoActual === 'mixto') {
            const efectivo = parseFloat(mixtoEfectivo.value) || 0;
            const digital = parseFloat(mixtoDigital.value) || 0;

            if ((efectivo + digital) < total) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Pago incompleto',
                    text: 'La suma de efectivo y digital debe cubrir el total.',
                });
                return false;
            }
        }

        rebuildDetalleInputs();
        rebuildPagosInputs();
        return true;
    });

    calcularTotales();
});

function initToggleCard(toggleId, contentId, iconId) {

    const toggle = document.getElementById(toggleId);
    const content = document.getElementById(contentId);
    const icon = document.getElementById(iconId);

    if (!toggle || !content || !icon) return;

    toggle.addEventListener('click', () => {

        const isOpen = content.classList.contains('show');

        content.classList.toggle('show');

        icon.style.transform = isOpen
            ? 'rotate(0deg)'
            : 'rotate(180deg)';
    });
}

initToggleCard(
    'toggleNotasModal',
    'notasModalContent',
    'toggleNotasModalIcon'
);

initToggleCard(
    'toggleClienteModal',
    'clienteModalContent',
    'toggleClienteModalIcon'
);