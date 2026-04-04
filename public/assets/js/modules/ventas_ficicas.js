document.addEventListener('DOMContentLoaded', function () {

    /* ================================
       PRODUCTOS DEMO
    ================================== */

    const productosDemo = [
        { id:1, nombre:"Laptop HP Pavilion 15", codigo_barras:"7501234567890", sku:"HP-PAV-15", precio_venta:450000, stock:10 },
        { id:2, nombre:"Mouse Logitech M170", codigo_barras:"7501234567891", sku:"LOG-M170", precio_venta:8500, stock:25 },
        { id:3, nombre:"Teclado Mecánico RGB", codigo_barras:"7501234567892", sku:"TEC-MEC-RGB", precio_venta:35000, stock:15 },
        { id:4, nombre:"Monitor Samsung 24\"", codigo_barras:"7501234567893", sku:"SAM-MON24", precio_venta:125000, stock:8 },
        { id:5, nombre:"Audífonos Sony WH-1000XM4", codigo_barras:"7501234567894", sku:"SNY-WH1000", precio_venta:185000, stock:5 },
        { id:6, nombre:"Disco Duro Externo 1TB", codigo_barras:"7501234567895", sku:"HDD-1TB", precio_venta:45000, stock:12 },
        { id:7, nombre:"Memoria USB 64GB", codigo_barras:"7501234567896", sku:"USB-64GB", precio_venta:12000, stock:30 },
        { id:8, nombre:"Cámara Web Logitech C920", codigo_barras:"7501234567897", sku:"LOG-C920", precio_venta:55000, stock:7 }
    ];

    const productosDisponibles = window.VentaConfig?.productosBackend || [];
    const productos = productosDisponibles.length > 0 ? productosDisponibles : productosDemo;

    /* ================================
       ELEMENTOS DOM
    ================================== */

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

    let searchTimeout = null;
    let currentProductSelected = null;

    /* ================================
       UTILIDADES
    ================================== */

    function escapeHtml(text) {
        if (!text) return '';
        return text.replace(/[&<>]/g, m =>
            m === '&' ? '&amp;' : m === '<' ? '&lt;' : '&gt;'
        );
    }

    /* ================================
       BUSCADOR
    ================================== */

    function searchProducts(query) {
        if (!query.trim()) {
            searchResults.style.display = 'none';
            return;
        }

        const filtered = productos.filter(p =>
            p.nombre.toLowerCase().includes(query.toLowerCase()) ||
            (p.codigo_barras && p.codigo_barras.includes(query)) ||
            (p.sku && p.sku.toLowerCase().includes(query.toLowerCase()))
        );

        if (filtered.length === 0) {
            searchResults.innerHTML =
                '<div class="list-group-item text-muted text-center">No se encontraron productos</div>';
            searchResults.style.display = 'block';
            return;
        }

        searchResults.innerHTML = filtered.map(p => `
            <a href="#" class="list-group-item list-group-item-action"
               data-product='${JSON.stringify(p)}'>
                <div class="d-flex justify-content-between">
                    <div>
                        <strong>${escapeHtml(p.nombre)}</strong><br>
                        <small class="text-muted">${p.codigo_barras || ''}</small>
                    </div>
                    <div class="text-end">
                        ₡ ${p.precio_venta.toLocaleString()}
                        <br><small>Stock: ${p.stock}</small>
                    </div>
                </div>
            </a>
        `).join('');

        searchResults.style.display = 'block';

        document.querySelectorAll('[data-product]').forEach(el => {
            el.addEventListener('click', function (e) {
                e.preventDefault();
                const p = JSON.parse(this.dataset.product);
                addProductFromSearch(p, parseInt(productQuantity.value) || 1);
                searchInput.value = '';
                searchResults.style.display = 'none';
            });
        });
    }

    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => searchProducts(this.value), 300);
    });

    /* ================================
       AGREGAR PRODUCTO
    ================================== */

    function addProductFromSearch(productData, cantidad = 1) {

        if (productData.stock < cantidad) {
            Swal.fire('Stock insuficiente');
            return;
        }

        emptyState.classList.add('d-none');

        const index = Date.now() + Math.random();
        const row = document.createElement('tr');

        row.innerHTML = `
            <td>
                ${productData.nombre}
                <input type="hidden" name="items[${index}][producto_id]" value="${productData.id}">
            </td>
            <td>${productData.codigo_barras || ''}</td>
            <td>
                <input type="number" class="form-control form-control-sm input-precio"
                       name="items[${index}][precio]"
                       value="${productData.precio_venta}" readonly>
            </td>
            <td>
                <input type="number" class="form-control form-control-sm input-cantidad"
                       name="items[${index}][cantidad]"
                       value="${cantidad}" min="1">
            </td>
            <td>
                ₡ <span class="label-fila-total">
                    ${(productData.precio_venta * cantidad).toFixed(2)}
                </span>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-outline-danger btn-remove">
                    X
                </button>
            </td>
        `;

        productosBody.appendChild(row);
        calcularTotales();
    }

    /* ================================
       TOTALES
    ================================== */

    function calcularTotales() {

        let subtotal = 0;
        let totalUnidades = 0;

        document.querySelectorAll('#productosBody tr').forEach(row => {
            const precio = parseFloat(row.querySelector('.input-precio').value) || 0;
            const cantidad = parseInt(row.querySelector('.input-cantidad').value) || 0;
            const totalFila = precio * cantidad;

            row.querySelector('.label-fila-total').innerText =
                totalFila.toLocaleString('en-US', { minimumFractionDigits: 2 });

            subtotal += totalFila;
            totalUnidades += cantidad;
        });

        const descuento = parseFloat(descuentoInput.value) || 0;
        const total = Math.max(0, subtotal - descuento);

        subtotalLabel.innerText = subtotal.toFixed(2);
        totalLabel.innerText = total.toFixed(2);

        totalProductosCount.innerText = totalUnidades;
        productosDistintosSpan.innerText =
            document.querySelectorAll('#productosBody tr').length;

        inputSubtotalHidden.value = subtotal;
        inputTotalHidden.value = total;
    }

    descuentoInput.addEventListener('input', calcularTotales);

    productosBody.addEventListener('click', e => {
        if (e.target.closest('.btn-remove')) {
            e.target.closest('tr').remove();
            if (!productosBody.children.length)
                emptyState.classList.remove('d-none');
            calcularTotales();
        }
    });

    productosBody.addEventListener('input', e => {
        if (e.target.classList.contains('input-cantidad'))
            calcularTotales();
    });

});