document.addEventListener('DOMContentLoaded', function () {

    const productos = window.InventarioProductos || [];

    const inputHidden = document.getElementById('id_producto');
    const varianteHidden = document.getElementById('id_producto_variante');

    const searchInput = document.getElementById('inventoryProductSearch');
    const resultsBox = document.getElementById('inventoryProductResults');
    const selectedBox = document.getElementById('inventoryProductSelected');

    const pedidoSelect = document.getElementById('id_pedido');
    const ventaLocalSelect = document.getElementById('id_venta_local');

    if (pedidoSelect && ventaLocalSelect) {
        pedidoSelect.addEventListener('change', function () {
            if (this.value) ventaLocalSelect.value = '';
        });

        ventaLocalSelect.addEventListener('change', function () {
            if (this.value) pedidoSelect.value = '';
        });
    }

    if (!inputHidden || !searchInput || !resultsBox || !selectedBox) return;

    let selectedIndex = -1;
    let productosFiltrados = [];

    function escapeHtml(text) {
        return String(text ?? '').replace(/[&<>"']/g, function (m) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            }[m];
        });
    }

    function tieneVariantes(producto) {
        return Boolean(producto?.usa_variantes);
    }

    function getVariantes(producto) {
        return Array.isArray(producto?.variantes) ? producto.variantes : [];
    }

    function getStock(producto) {
        if (tieneVariantes(producto)) {
            return getVariantes(producto).reduce(function (total, variante) {
                const stockVariante = Number(variante?.stock);
                return total + (Number.isFinite(stockVariante) ? stockVariante : 0);
            }, 0);
        }

        const stock = Number(producto?.stock);
        return Number.isFinite(stock) ? stock : 0;
    }

    function getCodigo(producto) {
        return producto.codigo_barras || producto.sku || 'Sin código';
    }

    function getNombreVariante(variante) {
        return variante.nombre || variante.opcion || 'Variante';
    }

    function renderImage(producto) {
        if (producto.imagen_url) {
            return `
                <div class="inventory-product-img-box">
                    <img src="${escapeHtml(producto.imagen_url)}"
                         alt="${escapeHtml(producto.nombre)}"
                         class="inventory-product-img"
                         loading="lazy"
                         onerror="this.parentElement.innerHTML='<div class=&quot;inventory-product-no-img&quot;><i class=&quot;bx bx-package&quot;></i></div>'">
                </div>
            `;
        }

        return `
            <div class="inventory-product-img-box">
                <div class="inventory-product-no-img">
                    <i class="bx bx-package"></i>
                </div>
            </div>
        `;
    }

    function limpiarResultados() {
        resultsBox.innerHTML = '';
        resultsBox.style.display = 'none';
        selectedIndex = -1;
        productosFiltrados = [];
    }

    function limpiarSeleccion() {
        inputHidden.value = '';

        if (varianteHidden) {
            varianteHidden.value = '';
        }

        selectedBox.innerHTML = '';
    }

    function renderResults(query) {
        const value = String(query || '').trim().toLowerCase();

        if (!value) {
            limpiarResultados();
            return;
        }

        productosFiltrados = productos.filter(function (producto) {
            return String(producto.nombre || '').toLowerCase().includes(value) ||
                   String(producto.sku || '').toLowerCase().includes(value) ||
                   String(producto.codigo_barras || '').toLowerCase().includes(value);
        }).slice(0, 12);

        if (!productosFiltrados.length) {
            resultsBox.innerHTML = `
                <div class="list-group-item text-center text-muted py-3">
                    <i class="bx bx-search-alt"></i>
                    No se encontraron productos
                </div>
            `;

            resultsBox.style.display = 'block';
            selectedIndex = -1;
            return;
        }

        resultsBox.innerHTML = productosFiltrados.map(function (producto, index) {
            const badgeTexto = tieneVariantes(producto)
                ? `Variantes: ${getVariantes(producto).length}`
                : `Stock: ${getStock(producto)}`;

            return `
                <button type="button"
                        class="list-group-item list-group-item-action inventory-product-option"
                        data-index="${index}">
                    <div class="d-flex align-items-center gap-3">
                        ${renderImage(producto)}

                        <div class="flex-grow-1 text-start">
                            <div class="fw-semibold">
                                ${escapeHtml(producto.nombre)}
                            </div>

                            <small class="text-muted">
                                ${escapeHtml(getCodigo(producto))}
                            </small>

                            ${
                                tieneVariantes(producto)
                                    ? `<div class="small text-primary fw-semibold">
                                            Producto con variantes
                                       </div>`
                                    : ''
                            }
                        </div>

                        <span class="badge bg-primary">
                            ${escapeHtml(badgeTexto)}
                        </span>
                    </div>
                </button>
            `;
        }).join('');

        resultsBox.style.display = 'block';
        selectedIndex = -1;
    }

    function updateSelection() {
        const items = resultsBox.querySelectorAll('.inventory-product-option');

        items.forEach(function (item, index) {
            item.classList.toggle('inventory-search-selected', index === selectedIndex);

            if (index === selectedIndex) {
                item.scrollIntoView({
                    block: 'nearest'
                });
            }
        });
    }

    function renderVariantSelector(producto) {
        if (!tieneVariantes(producto)) {
            return '';
        }

        const variantes = getVariantes(producto);

        if (!variantes.length) {
            return `
                <div class="alert alert-warning mt-3 mb-0">
                    Este producto usa variantes, pero no tiene variantes activas.
                </div>
            `;
        }

        const opciones = variantes.map(function (variante) {
            const selected = varianteHidden && String(varianteHidden.value) === String(variante.id)
                ? 'selected'
                : '';

            const nombre = getNombreVariante(variante);
            const sku = variante.sku ? ` | SKU: ${variante.sku}` : '';

            return `
                <option value="${escapeHtml(variante.id)}" ${selected}>
                    ${escapeHtml(nombre)}${escapeHtml(sku)} | Stock: ${escapeHtml(variante.stock)}
                </option>
            `;
        }).join('');

        return `
            <div class="mt-3 pt-3 border-top">
                <label class="fw-semibold mb-2">
                    Variante <span class="text-danger">*</span>
                </label>

                <select id="inventoryVariantSelect"
                        class="form-select"
                        required>
                    <option value="">Seleccione una variante</option>
                    ${opciones}
                </select>

                <small class="text-muted d-block mt-1">
                    Este producto maneja inventario por variante.
                </small>
            </div>
        `;
    }

    function actualizarVarianteSeleccionada(producto) {
        const variantSelect = document.getElementById('inventoryVariantSelect');

        if (!variantSelect || !varianteHidden) {
            return;
        }

        variantSelect.addEventListener('change', function () {
            varianteHidden.value = this.value;

            const variante = getVariantes(producto).find(function (item) {
                return String(item.id) === String(variantSelect.value);
            });

            const infoBox = document.getElementById('inventoryVariantInfo');

            if (infoBox && variante) {
                infoBox.innerHTML = `
                    Stock de la variante seleccionada:
                    <strong>${escapeHtml(variante.stock)}</strong>
                `;
            } else if (infoBox) {
                infoBox.innerHTML = '';
            }
        });

        if (variantSelect.value) {
            varianteHidden.value = variantSelect.value;
            variantSelect.dispatchEvent(new Event('change'));
        }
    }

    function selectProduct(producto) {
        if (!producto) return;

        inputHidden.value = producto.id;
        searchInput.value = producto.nombre;

        if (varianteHidden) {
            if (!tieneVariantes(producto)) {
                varianteHidden.value = '';
            }
        }

        selectedBox.innerHTML = `
            <div class="p-3 bg-white border rounded-4 shadow-sm">
                <div class="d-flex align-items-center gap-3">
                    ${renderImage(producto)}

                    <div class="flex-grow-1">
                        <div class="fw-bold">
                            ${escapeHtml(producto.nombre)}
                        </div>

                        <small class="text-muted">
                            ${escapeHtml(getCodigo(producto))}
                        </small>

                        <div class="small text-muted">
                            ${
                                tieneVariantes(producto)
                                    ? `Stock total en variantes: <strong>${escapeHtml(getStock(producto))}</strong>`
                                    : `Stock actual: <strong>${escapeHtml(getStock(producto))}</strong>`
                            }
                        </div>

                        ${
                            tieneVariantes(producto)
                                ? `<div class="small text-primary fw-semibold">
                                        Producto con variantes${producto.tipo_variante ? ': ' + escapeHtml(producto.tipo_variante) : ''}
                                   </div>`
                                : ''
                        }
                    </div>
                </div>

                ${renderVariantSelector(producto)}

                <div id="inventoryVariantInfo" class="small text-muted mt-2"></div>
            </div>
        `;

        actualizarVarianteSeleccionada(producto);
        limpiarResultados();
    }

    searchInput.addEventListener('input', function () {
        limpiarSeleccion();
        renderResults(this.value);
    });

    searchInput.addEventListener('keydown', function (event) {
        const total = productosFiltrados.length;

        if (!total) return;

        if (event.key === 'ArrowDown') {
            event.preventDefault();
            selectedIndex = selectedIndex < total - 1 ? selectedIndex + 1 : 0;
            updateSelection();
            return;
        }

        if (event.key === 'ArrowUp') {
            event.preventDefault();
            selectedIndex = selectedIndex > 0 ? selectedIndex - 1 : total - 1;
            updateSelection();
            return;
        }

        if (event.key === 'Enter') {
            event.preventDefault();

            const index = selectedIndex >= 0 ? selectedIndex : 0;
            selectProduct(productosFiltrados[index]);
            return;
        }

        if (event.key === 'Escape') {
            event.preventDefault();
            limpiarResultados();
        }
    });

    resultsBox.addEventListener('mousedown', function (event) {
        event.preventDefault();

        const item = event.target.closest('.inventory-product-option');
        if (!item) return;

        const index = Number(item.dataset.index);
        selectProduct(productosFiltrados[index]);
    });

    document.addEventListener('click', function (event) {
        if (!searchInput.contains(event.target) && !resultsBox.contains(event.target)) {
            limpiarResultados();
        }
    });

    if (inputHidden.value) {
        const producto = productos.find(function (p) {
            return String(p.id) === String(inputHidden.value);
        });

        if (producto) {
            selectProduct(producto);
        }
    }

});