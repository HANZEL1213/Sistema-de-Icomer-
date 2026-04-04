@extends('admin.layouts.app')

@section('title', 'Nueva Venta')

@section('content')


   <link rel="stylesheet" href="{{ asset('assets/css/modules/ventas_ficicas.css') }}">

    <div class="page-content">

        {{-- Breadcrumb --}}
        <div class="page-breadcrumb d-sm-flex align-items-center mb-3">
            <div class="ps-3">
                <nav>
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="bx bx-home-alt"></i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.ventas-locales.index') }}">Ventas</a>
                        </li>
                        <li class="breadcrumb-item active">Nueva Venta</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="card card-index">
            <div class="card-body">

                {{-- HEADER --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="fw-bold text-uppercase mb-1">
                            <i class="bx bx-cart-add text-primary fs-3 align-middle"></i>
                            <span class="align-middle">Nueva Venta Física</span>
                        </h4>
                        <small class="text-muted">
                            <i class="bx bx-store"></i> Punto de Venta Local |
                            <i class="bx bx-barcode"></i> Escanea o busca productos
                        </small>
                    </div>
                    <a href="{{ route('admin.ventas-locales.index') }}" class="btn btn-secondary-custom btn-back">
                        <i class="bx bx-arrow-back"></i>
                        <span class="btn-text">Volver</span>
                    </a>
                </div>

                <hr>

                <form id="formVenta" action="{{ route('admin.ventas-locales.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="subtotal" id="inputSubtotal" value="0">
                    <input type="hidden" name="total" id="inputTotal" value="0">

                    <div class="row g-4">






{{-- 🛒 COLUMNA IZQUIERDA --}}
<div class="col-lg-8">

    {{-- CARD DE PRODUCTOS --}}
    <div class="form-card bg-light mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center gap-2 mb-4">
                <i class="bx bx-package fs-4 text-primary"></i>
                <h5 class="fw-bold mb-0">Detalle de Productos</h5>
            </div>

            {{-- BUSCADOR --}}
            <div class="bg-light p-4 rounded-3 mb-4">
                <div class="row g-3 align-items-end">

                    {{-- BUSCAR PRODUCTO --}}
                    <div class="col-md-6 position-relative">
                        <label class="fw-semibold mb-2">
                            <i class="bx bx-search-alt text-primary"></i> Buscar Producto
                        </label>

                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="bx bx-barcode text-muted"></i>
                            </span>

                            <input type="text"
                                   id="searchProduct"
                                   class="form-control"
                                   placeholder="Nombre, código de barras o SKU..."
                                   autocomplete="off">

                            <button type="button"
                                    id="btnClearSearch"
                                    class="btn btn-outline-secondary">
                                <i class="bx bx-eraser"></i>
                            </button>
                        </div>

                        {{-- RESULTADOS FLOTANTES --}}
                        <div id="searchResults"
                             class="list-group shadow"
                             style="display: none;">
                        </div>
                    </div>

                    {{-- CANTIDAD --}}
                    <div class="col-md-3">
                        <label class="fw-semibold mb-2">
                            <i class="bx bx-plus-circle text-success"></i> Cantidad
                        </label>

                        <input type="number"
                               id="productQuantity"
                               class="form-control text-center"
                               value="1"
                               min="1">
                    </div>

                    {{-- BOTÓN AGREGAR --}}
                    <div class="col-md-3">
                        <button type="button"
                                id="btnAddProduct"
                                class="btn btn-primary-custom w-100">
                            <i class="bx bx-cart-add"></i> Agregar
                        </button>
                    </div>

                </div>
            </div>

            {{-- TABLA DE PRODUCTOS --}}
            <div class="table-responsive"
                 style="max-height: 400px; overflow-y: auto;">
                <table class="table table-hover align-middle">
                    <thead class="table-light"
                           style="position: sticky; top: 0; z-index: 10;">
                        <tr class="text-uppercase small">
                            <th style="width: 35%;">
                                <i class="bx bx-package me-1"></i> Producto
                            </th>
                            <th style="width: 20%;">
                                <i class="bx bx-barcode me-1"></i> Código
                            </th>
                            <th style="width: 15%;">
                                <i class="bx bx-dollar me-1"></i> Precio (₡)
                            </th>
                            <th style="width: 15%;">
                                <i class="bx bx-calculator me-1"></i> Cantidad
                            </th>
                            <th style="width: 15%;">
                                <i class="bx bx-trending-up me-1"></i> Total
                            </th>
                            <th style="width: 5%;"></th>
                        </tr>
                    </thead>
                    <tbody id="productosBody"></tbody>
                </table>
            </div>

            <div id="emptyState"
                 class="text-center py-5 text-muted">
                <i class="bx bx-basket fs-1 d-block mb-3 text-secondary"></i>
                <p class="mb-0">No hay productos agregados</p>
                <small>Busca un producto por nombre o código de barras</small>
            </div>

        </div>
    </div>

    {{-- CARD DE NOTAS --}}
    <div class="form-card bg-light mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center gap-2 mb-3">
                <i class="bx bx-note fs-4 text-warning"></i>
                <h5 class="fw-bold mb-0">Notas de la Venta</h5>
            </div>

            <div class="form-floating">
                <textarea name="notas"
                          id="notasVenta"
                          class="form-control"
                          placeholder="Información adicional"
                          style="height: 100px"></textarea>
                <label for="notasVenta">
                    <i class="bx bx-info-circle"></i>
                    Información adicional (opcional)
                </label>
            </div>
        </div>
    </div>

    {{-- CARD DEL CLIENTE --}}
    <div class="form-card bg-light">
        <div class="card-body">

            <div class="d-flex align-items-center gap-2 mb-3">
                <i class="bx bx-user fs-4 text-info"></i>
                <h5 class="fw-bold mb-0">Información del Cliente</h5>
                <small class="text-muted fw-normal">(Opcional)</small>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="fw-semibold mb-2">
                        Nombre / Razón Social
                    </label>

                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-user-circle"></i>
                        </span>
                        <input type="text"
                               name="nombre_cliente"
                               class="form-control"
                               placeholder="Consumidor Final">
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="fw-semibold mb-2">
                        Teléfono
                    </label>

                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-phone"></i>
                        </span>
                        <input type="text"
                               name="telefono_cliente"
                               class="form-control"
                               placeholder="8888-8888">
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

{{-- 💰 COLUMNA DERECHA: RESUMEN --}}
<div class="col-lg-4">

    {{-- TOTAL CARD --}}
    <div class="form-card bg-light mb-4">
        <div class="card-body p-4 text-center">
            <p class="mb-1 text-muted">
                <i class="bx bx-calculator"></i> Total a Cobrar
            </p>
            <h2 class="display-5 fw-bold mb-0" style="color: var(--color-primary);">₡ <span
                    id="totalLabel">0.00</span></h2>
        </div>
    </div>

    {{-- RESUMEN CARD --}}
    <div class="form-card bg-light">
        <div class="card-body">
            <div class="d-flex justify-content-between mb-3 pb-2 border-bottom">
                <span class="text-muted">
                    <i class="bx bx-receipt"></i> Subtotal:
                </span>
                <span class="fw-bold">₡ <span id="subtotalLabel">0.00</span></span>
            </div>

            <div class="mb-4">
                <label class="fw-semibold mb-2">
                    <i class="bx bx-discount text-danger"></i> Descuento General (₡)
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-white">₡</span>
                    <input type="number" id="descuentoInput" name="descuento"
                        class="form-control text-end fw-bold" value="0" step="1"
                        min="0">
                </div>
            </div>

            <div class="alert alert-info small mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <span><i class="bx bx-package"></i> Total productos:</span>
                    <strong><span id="totalProductosCount">0</span> unidades</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-1">
                    <span><i class="bx bx-list-ul"></i> Productos distintos:</span>
                    <strong><span id="productosDistintosCount">0</span> items</strong>
                </div>
            </div>

            <button type="button" id="btnProcesar"
                class="btn btn-primary-custom w-100 py-3 fw-bold text-uppercase">
                <i class="bx bx-check-double fs-5 align-middle"></i>
                <span class="align-middle">Registrar Venta</span>
            </button>
        </div>
    </div>

</div>

</div>

{{-- 💳 MODAL DE PAGO --}}
<div class="modal fade" id="modalPago" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content modal-admin-clean">

            <!-- HEADER -->
            <div class="modal-header">
                <div>
                    <h5 class="text-uppercase mb-1">
                        Finalizar Venta
                    </h5>
                    <small class="text-muted">
                        Complete la información del pago
                    </small>
                </div>

                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <!-- TOTAL -->
                <div class="card border-0 bg-light mb-4">
                    <div class="card-body text-center">

                        <small class="text-muted text-uppercase d-block mb-1">
                            Monto Total a Recibir
                        </small>

                        <h2 class="fw-bold total-admin mb-0">
                            ₡ <span id="modalTotalLabel">0.00</span>
                        </h2>

                    </div>
                </div>

                <!-- MÉTODO DE PAGO -->
                <div class="card border-0 bg-light mb-4">
                    <div class="card-body">

                        <label class="fw-semibold mb-3 d-block">
                            Método de Pago
                        </label>

                        <div class="row g-3">
                            <div class="col-3">
                                <button type="button"
                                    class="btn-metodo-admin w-100 metodo-btn active"
                                    data-metodo="efectivo">
                                    <i class="bx bx-money"></i>
                                    <span>Efectivo</span>
                                </button>
                            </div>

                            <div class="col-3">
                                <button type="button"
                                    class="btn-metodo-admin w-100 metodo-btn"
                                    data-metodo="tarjeta">
                                    <i class="bx bx-credit-card"></i>
                                    <span>Tarjeta</span>
                                </button>
                            </div>

                            <div class="col-3">
                                <button type="button"
                                    class="btn-metodo-admin w-100 metodo-btn"
                                    data-metodo="sinpe">
                                    <i class="bx bx-mobile-alt"></i>
                                    <span>SINPE</span>
                                </button>
                            </div>

                            <div class="col-3">
                                <button type="button"
                                    class="btn-metodo-admin w-100 metodo-btn"
                                    data-metodo="mixto">
                                    <i class="bx bx-shuffle"></i>
                                    <span>Mixto</span>
                                </button>
                            </div>
                        </div>

                        <input type="hidden" name="metodo_pago" id="metodoPagoInput" value="efectivo">

                    </div>
                </div>

                <!-- CAMPOS DINÁMICOS -->
                <div class="card border-0 bg-light">
                    <div class="card-body">

                        <!-- EFECTIVO -->
                        <div id="efectivoFields">
                            <label class="fw-semibold mb-2">Monto Recibido</label>

                            <div class="input-group mb-3">
                                <span class="input-group-text">₡</span>
                                <input type="number" id="montoRecibido"
                                    class="form-control"
                                    placeholder="0.00"
                                    step="0.01">
                            </div>

                            <div class="vuelto-box">
                                <span>Vuelto:</span>
                                <strong>₡ <span id="vueltoLabel">0.00</span></strong>
                            </div>
                        </div>

                        <!-- REFERENCIA -->
                        <div id="referenciaFields" class="d-none">
                            <label class="fw-semibold mb-2">Número de Comprobante</label>
                            <input type="text"
                                name="referencia_pago"
                                id="referenciaPago"
                                class="form-control"
                                placeholder="Referencia de transacción">
                        </div>

                        <!-- MIXTO -->
                        <div id="mixtoFields" class="d-none">

                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="fw-semibold mb-2">Efectivo</label>
                                    <input type="number"
                                        id="mixtoEfectivo"
                                        name="monto_mixto_efectivo"
                                        class="form-control"
                                        placeholder="0.00"
                                        step="0.01">
                                </div>

                                <div class="col-6">
                                    <label class="fw-semibold mb-2">Tarjeta / SINPE</label>
                                    <input type="number"
                                        id="mixtoDigital"
                                        name="monto_mixto_digital"
                                        class="form-control"
                                        placeholder="0.00"
                                        step="0.01">
                                </div>
                            </div>

                            <div id="mixtoWarning" class="mt-2 small text-muted"></div>

                            <div id="mixtoVueltoInfo"
                                class="mt-3 vuelto-box d-none">
                                Vuelto en efectivo:
                                <strong>₡ <span id="mixtoVueltoSpan">0.00</span></strong>
                            </div>

                        </div>

                    </div>
                </div>

            </div>

            <!-- FOOTER -->
            <div class="modal-footer">

                <button type="button"
                    class="btn btn-secondary-custom btn-back"
                    data-bs-dismiss="modal">
                    
                    Cancelar
                </button>

                <button type="submit"
                    class="btn btn-primary-custom">
                    Confirmar Venta
                </button>

            </div>

        </div>
    </div>
</div>

</form>

</div>
</div>

</div>
@endsection







@push('scripts')
    <script>
        // Productos de ejemplo para frontend (si no hay productos desde backend)
        const productosDemo = [{
                id: 1,
                nombre: "Laptop HP Pavilion 15",
                codigo_barras: "7501234567890",
                sku: "HP-PAV-15",
                precio_venta: 450000,
                stock: 10
            },
            {
                id: 2,
                nombre: "Mouse Logitech M170",
                codigo_barras: "7501234567891",
                sku: "LOG-M170",
                precio_venta: 8500,
                stock: 25
            },
            {
                id: 3,
                nombre: "Teclado Mecánico RGB",
                codigo_barras: "7501234567892",
                sku: "TEC-MEC-RGB",
                precio_venta: 35000,
                stock: 15
            },
            {
                id: 4,
                nombre: "Monitor Samsung 24\"",
                codigo_barras: "7501234567893",
                sku: "SAM-MON24",
                precio_venta: 125000,
                stock: 8
            },
            {
                id: 5,
                nombre: "Audífonos Sony WH-1000XM4",
                codigo_barras: "7501234567894",
                sku: "SNY-WH1000",
                precio_venta: 185000,
                stock: 5
            },
            {
                id: 6,
                nombre: "Disco Duro Externo 1TB",
                codigo_barras: "7501234567895",
                sku: "HDD-1TB",
                precio_venta: 45000,
                stock: 12
            },
            {
                id: 7,
                nombre: "Memoria USB 64GB",
                codigo_barras: "7501234567896",
                sku: "USB-64GB",
                precio_venta: 12000,
                stock: 30
            },
            {
                id: 8,
                nombre: "Cámara Web Logitech C920",
                codigo_barras: "7501234567897",
                sku: "LOG-C920",
                precio_venta: 55000,
                stock: 7
            }
        ];

        document.addEventListener('DOMContentLoaded', function() {
            // Obtener productos del backend o usar demo
            const productosDisponibles = @json($productos ?? []);
            const productos = productosDisponibles.length > 0 ? productosDisponibles : productosDemo;

            // Elementos DOM
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

            // Buscar productos
            function searchProducts(query) {
                if (!query.trim()) {
                    searchResults.style.display = 'none';
                    currentProductSelected = null;
                    return;
                }

                const filtered = productos.filter(p =>
                    p.nombre.toLowerCase().includes(query.toLowerCase()) ||
                    (p.codigo_barras && p.codigo_barras.toLowerCase().includes(query.toLowerCase())) ||
                    (p.sku && p.sku.toLowerCase().includes(query.toLowerCase()))
                );

                if (filtered.length === 0) {
                    searchResults.innerHTML =
                        '<div class="list-group-item text-muted text-center"><i class="bx bx-search-alt"></i> No se encontraron productos</div>';
                    searchResults.style.display = 'block';
                    return;
                }

                const exactMatch = filtered.find(p =>
                    (p.codigo_barras && p.codigo_barras.toLowerCase() === query.toLowerCase()) ||
                    p.nombre.toLowerCase() === query.toLowerCase()
                );

                if (exactMatch && filtered.length === 1) {
                    currentProductSelected = {
                        id: exactMatch.id,
                        nombre: exactMatch.nombre,
                        codigo: exactMatch.codigo_barras || '',
                        precio: exactMatch.precio_venta,
                        sku: exactMatch.sku || '',
                        stock: exactMatch.stock
                    };
                    searchResults.style.display = 'none';
                    addProductFromSearch(currentProductSelected, parseInt(productQuantity.value) || 1);
                    searchInput.value = '';
                    productQuantity.value = 1;
                    currentProductSelected = null;
                    return;
                }

                searchResults.innerHTML = filtered.map(p => `
            <a href="#" class="list-group-item list-group-item-action" data-product='${JSON.stringify(p)}'>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>${escapeHtml(p.nombre)}</strong>
                        ${p.codigo_barras ? `<br><small class="text-muted"><i class="bx bx-barcode"></i> ${escapeHtml(p.codigo_barras)}</small>` : ''}
                    </div>
                    <div class="text-end">
                        <span class="badge bg-primary">₡ ${p.precio_venta.toLocaleString()}</span>
                        <br><small class="text-muted">Stock: ${p.stock}</small>
                    </div>
                </div>
            </a>
        `).join('');
                searchResults.style.display = 'block';

                document.querySelectorAll('.list-group-item[data-product]').forEach(el => {
                    el.addEventListener('click', function(e) {
                        e.preventDefault();
                        const p = JSON.parse(this.dataset.product);
                        currentProductSelected = {
                            id: p.id,
                            nombre: p.nombre,
                            codigo: p.codigo_barras || '',
                            precio: p.precio_venta,
                            sku: p.sku || '',
                            stock: p.stock
                        };
                        addProductFromSearch(currentProductSelected, parseInt(productQuantity
                            .value) || 1);
                        searchInput.value = '';
                        searchResults.style.display = 'none';
                        productQuantity.value = 1;
                        currentProductSelected = null;
                    });
                });
            }

            // Función para escapar HTML
            function escapeHtml(text) {
                if (!text) return '';
                return text.replace(/[&<>]/g, function(m) {
                    if (m === '&') return '&amp;';
                    if (m === '<') return '&lt;';
                    if (m === '>') return '&gt;';
                    return m;
                });
            }

            // Agregar producto
            function addProductFromSearch(productData, cantidad = 1) {
                if (productData.stock < cantidad) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Stock insuficiente',
                        text: `Solo hay ${productData.stock} unidades disponibles.`
                    });
                    return false;
                }

                const existingRows = productosBody.querySelectorAll('tr');
                let productExists = false,
                    existingRow = null;

                existingRows.forEach(row => {
                    const idInput = row.querySelector('input[name*="[producto_id]"]');
                    if (idInput && idInput.value === productData.id.toString()) {
                        productExists = true;
                        existingRow = row;
                    }
                });

                if (productExists && existingRow) {
                    const cantInput = existingRow.querySelector('.input-cantidad');
                    const nuevaCant = parseInt(cantInput.value) + cantidad;
                    if (productData.stock < nuevaCant) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Stock insuficiente',
                            text: `Stock disponible: ${productData.stock}`
                        });
                        return false;
                    }
                    cantInput.value = nuevaCant;
                    calcularTotales();
                    existingRow.style.backgroundColor = '#d4edda';
                    setTimeout(() => existingRow.style.backgroundColor = '', 500);
                } else {
                    emptyState.classList.add('d-none');
                    const index = Date.now() + Math.random();
                    const row = document.createElement('tr');
                    row.innerHTML = `
                <td><strong>${escapeHtml(productData.nombre)}</strong>
                    <input type="hidden" name="items[${index}][producto_id]" value="${productData.id}">
                    <input type="hidden" name="items[${index}][nombre]" value="${escapeHtml(productData.nombre)}">
                    <input type="hidden" name="items[${index}][sku_snapshot]" value="${escapeHtml(productData.sku || 'N/A')}">
                    <input type="hidden" name="items[${index}][codigo_barras]" value="${escapeHtml(productData.codigo || '')}">
                </td>
                <td><small class="text-muted">${escapeHtml(productData.codigo || 'N/A')}</small></td>
                <td><input type="number" name="items[${index}][precio]" class="form-control form-control-sm input-precio" value="${parseFloat(productData.precio).toFixed(2)}" readonly style="background:#e9ecef; width:110px;"></td>
                <td><input type="number" name="items[${index}][cantidad]" class="form-control form-control-sm input-cantidad" value="${cantidad}" min="1" max="${productData.stock}" style="width:80px;"></td>
                <td><span class="fw-bold">₡ <span class="label-fila-total">${(parseFloat(productData.precio) * cantidad).toFixed(2)}</span></span></td>
                <td><button type="button" class="btn btn-sm btn-outline-danger btn-remove"><i class="bx bx-trash"></i></button></td>
            `;
                    productosBody.appendChild(row);
                    calcularTotales();
                    row.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
                return true;
            }

            // Calcular totales
            function calcularTotales() {
                let subtotal = 0,
                    totalUnidades = 0;
                document.querySelectorAll('#productosBody tr').forEach(row => {
                    const precio = parseFloat(row.querySelector('.input-precio').value) || 0;
                    const cantidad = parseInt(row.querySelector('.input-cantidad').value) || 0;
                    const totalFila = precio * cantidad;
                    const totalSpan = row.querySelector('.label-fila-total');
                    if (totalSpan) totalSpan.innerText = totalFila.toLocaleString('en-US', {
                        minimumFractionDigits: 2
                    });
                    subtotal += totalFila;
                    totalUnidades += cantidad;
                });
                const descuento = parseFloat(descuentoInput.value) || 0;
                const total = Math.max(0, subtotal - descuento);
                subtotalLabel.innerText = subtotal.toLocaleString('en-US', {
                    minimumFractionDigits: 2
                });
                totalLabel.innerText = total.toLocaleString('en-US', {
                    minimumFractionDigits: 2
                });
                totalProductosCount.innerText = totalUnidades;
                productosDistintosSpan.innerText = document.querySelectorAll('#productosBody tr').length;
                inputSubtotalHidden.value = subtotal.toFixed(2);
                inputTotalHidden.value = total.toFixed(2);
            }

            // Eventos
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => searchProducts(this.value), 300);
            });

            btnAddProduct.addEventListener('click', () => {
                if (currentProductSelected) {
                    addProductFromSearch(currentProductSelected, parseInt(productQuantity.value) || 1);
                    searchInput.value = '';
                    searchResults.style.display = 'none';
                    productQuantity.value = 1;
                    currentProductSelected = null;
                } else if (searchInput.value.trim()) {
                    const firstResult = document.querySelector('.list-group-item[data-product]');
                    firstResult ? firstResult.click() : Swal.fire({
                        icon: 'warning',
                        title: 'No encontrado',
                        text: 'Producto no encontrado'
                    });
                } else {
                    Swal.fire({
                        icon: 'info',
                        title: 'Buscar producto',
                        text: 'Escribe el nombre o código del producto'
                    });
                    searchInput.focus();
                }
            });

            searchInput.addEventListener('keypress', e => {
                if (e.key === 'Enter') btnAddProduct.click();
            });
            document.addEventListener('click', e => {
                if (!searchInput.contains(e.target) && !searchResults.contains(e.target) && e.target !==
                    btnAddProduct) searchResults.style.display = 'none';
            });
            document.getElementById('btnClearSearch').addEventListener('click', () => {
                searchInput.value = '';
                searchResults.style.display = 'none';
                searchInput.focus();
            });

            productosBody.addEventListener('click', e => {
                if (e.target.closest('.btn-remove')) {
                    e.target.closest('tr').remove();
                    if (!productosBody.children.length) emptyState.classList.remove('d-none');
                    calcularTotales();
                }
            });

            productosBody.addEventListener('change', e => {
                if (e.target.classList.contains('input-cantidad')) {
                    const row = e.target.closest('tr');
                    const id = row.querySelector('input[name*="[producto_id]"]').value;
                    const producto = productos.find(p => p.id == id);
                    const cantidad = parseInt(e.target.value);
                    if (cantidad < 1) e.target.value = 1;
                    else if (producto && cantidad > producto.stock) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Stock insuficiente',
                            text: `Máximo: ${producto.stock}`
                        });
                        e.target.value = producto.stock;
                    }
                    calcularTotales();
                }
            });

            productosBody.addEventListener('input', e => {
                if (e.target.classList.contains('input-cantidad') && e.target.value < 1) e.target.value = 1;
                calcularTotales();
            });
            descuentoInput.addEventListener('input', calcularTotales);

            // Modal pago con botones clickeables
            const btnProcesar = document.getElementById('btnProcesar');
            const modalPago = new bootstrap.Modal(document.getElementById('modalPago'));
            const modalTotalLabel = document.getElementById('modalTotalLabel');
            const metodoPagoInput = document.getElementById('metodoPagoInput');
            const metodoBtns = document.querySelectorAll('.metodo-btn');
            const efectivoDiv = document.getElementById('efectivoFields');
            const referenciaDiv = document.getElementById('referenciaFields');
            const mixtoDiv = document.getElementById('mixtoFields');
            const inputRecibido = document.getElementById('montoRecibido');
            const vueltoLabel = document.getElementById('vueltoLabel');
            const mixtoEfectivo = document.getElementById('mixtoEfectivo');
            const mixtoDigital = document.getElementById('mixtoDigital');
            const mixtoWarning = document.getElementById('mixtoWarning');
            const mixtoVueltoInfo = document.getElementById('mixtoVueltoInfo');
            const mixtoVueltoSpan = document.getElementById('mixtoVueltoSpan');

            let metodoActual = 'efectivo';

            function actualizarVistaMetodo(metodo) {
                metodoActual = metodo;
                metodoPagoInput.value = metodo;

                // Actualizar estilos de botones
                metodoBtns.forEach(btn => {
                    if (btn.dataset.metodo === metodo) {
                        btn.classList.add('active');
                    } else {
                        btn.classList.remove('active');
                    }
                });

                // Mostrar/ocultar secciones
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
                    mixtoWarning.innerHTML =
                        `<i class="bx bx-error-circle"></i> Faltan ₡ ${(total - suma).toLocaleString()} para completar el total.`;
                    mixtoVueltoInfo.classList.add('d-none');
                } else if (suma >= total) {
                    mixtoWarning.innerHTML = `<i class="bx bx-check-circle text-success"></i> Pago completo.`;
                    const vueltoEfectivo = efectivo - (total - digital);
                    if (vueltoEfectivo > 0) {
                        mixtoVueltoSpan.innerText = vueltoEfectivo.toLocaleString('en-US', {
                            minimumFractionDigits: 2
                        });
                        mixtoVueltoInfo.classList.remove('d-none');
                    } else {
                        mixtoVueltoInfo.classList.add('d-none');
                    }
                } else {
                    mixtoWarning.innerHTML = '';
                    mixtoVueltoInfo.classList.add('d-none');
                }
            }

            // Eventos para los botones de método de pago
            metodoBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    const metodo = btn.dataset.metodo;
                    actualizarVistaMetodo(metodo);
                });
            });

            mixtoEfectivo.addEventListener('input', actualizarMixtoVuelto);
            mixtoDigital.addEventListener('input', actualizarMixtoVuelto);

            btnProcesar.addEventListener('click', () => {
                const total = parseFloat(inputTotalHidden.value);
                if (total <= 0 || productosBody.children.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Venta inválida',
                        text: 'Agregue al menos un producto válido.'
                    });
                    return;
                }
                modalTotalLabel.innerText = totalLabel.innerText;
                inputRecibido.value = "";
                vueltoLabel.innerText = "0.00";
                mixtoEfectivo.value = "";
                mixtoDigital.value = "";
                mixtoWarning.innerHTML = "";
                mixtoVueltoInfo.classList.add('d-none');

                // Resetear al método efectivo por defecto
                actualizarVistaMetodo('efectivo');
                modalPago.show();
            });

            inputRecibido.addEventListener('input', function() {
                const total = parseFloat(inputTotalHidden.value) || 0;
                const recibido = parseFloat(this.value) || 0;
                vueltoLabel.innerText = (recibido - total >= 0 ? (recibido - total) : 0).toLocaleString(
                    'en-US', {
                        minimumFractionDigits: 2
                    });
            });

            document.getElementById('formVenta').addEventListener('submit', function(e) {
                const total = parseFloat(inputTotalHidden.value);
                const metodo = metodoPagoInput.value;

                if (metodo === 'efectivo') {
                    const recibido = parseFloat(document.getElementById('montoRecibido').value);
                    if (!recibido || recibido < total) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Monto insuficiente',
                            text: 'El monto recibido debe ser mayor o igual al total.'
                        });
                        return false;
                    }
                } else if (metodo === 'mixto') {
                    const efectivo = parseFloat(mixtoEfectivo.value) || 0;
                    const digital = parseFloat(mixtoDigital.value) || 0;
                    if (efectivo + digital < total) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Pago incompleto',
                            text: 'La suma de efectivo y digital debe ser mayor o igual al total.'
                        });
                        return false;
                    }
                } else if (metodo === 'tarjeta' || metodo === 'sinpe') {
                    const referencia = document.getElementById('referenciaPago').value;
                    if (!referencia.trim()) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Referencia requerida',
                            text: 'Ingrese el número de comprobante.'
                        });
                        return false;
                    }
                }

                return true;
            });
        });
    </script>
@endpush
