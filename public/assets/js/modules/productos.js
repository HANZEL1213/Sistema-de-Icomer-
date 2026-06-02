document.addEventListener('DOMContentLoaded', function () {

    /* =========================================
       AUTO SLUG
    ========================================= */
    const nombre = document.getElementById('nombre');
    const slug = document.getElementById('slug');

    if (nombre && slug) {
        nombre.addEventListener('input', function () {
            let value = this.value
                .toLowerCase()
                .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                .replace(/[^a-z0-9\s-]/g, '')
                .trim()
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');

            slug.value = value;
        });
    }

    /* =========================================
       ESTADO ACTIVO / INACTIVO
    ========================================= */
    const switchActivo = document.getElementById('activoSwitch');
    const estadoTexto = document.getElementById('estadoTexto');

    if (switchActivo && estadoTexto) {
        switchActivo.addEventListener('change', function () {
            if (this.checked) {
                estadoTexto.innerHTML = '<i class="bx bx-check-circle me-1"></i> Activo';
                estadoTexto.classList.remove('bg-secondary');
                estadoTexto.classList.add('bg-success');
            } else {
                estadoTexto.innerHTML = '<i class="bx bx-x-circle me-1"></i> Inactivo';
                estadoTexto.classList.remove('bg-success');
                estadoTexto.classList.add('bg-secondary');
            }
        });
    }

    /* =========================================
       GESTOR DE IMÁGENES
    ========================================= */
    const inputFile = document.getElementById('imagenes');
    const previewContainer = document.getElementById('previewContainer');
    const principalIndexInput = document.getElementById('principal_index');
    const principalTipoInput = document.getElementById('imagen_principal_tipo');
    const principalExistenteInput = document.getElementById('imagen_principal_existente');
    const eliminadasContainer = document.getElementById('imagenesEliminadasContainer');
    const existentesOrdenContainer = document.getElementById('imagenesExistentesOrdenContainer');

    let sortableInstance = null;
    let filesArray = [];
    let existingImages = [];
    let deletedExistingIds = new Set();

    if (previewContainer) {
        const existingNodes = previewContainer.querySelectorAll('.existing-image-item');

        existingImages = Array.from(existingNodes).map((node, index) => {
            return {
                id: parseInt(node.dataset.id, 10),
                src: node.querySelector('img')?.getAttribute('src') || '',
                isPrincipal: !!node.querySelector('.principal-badge'),
                order: index + 1
            };
        });
    }

    const revokeAllObjectUrls = () => {
        filesArray.forEach(item => {
            if (item.objectUrl) {
                URL.revokeObjectURL(item.objectUrl);
                item.objectUrl = null;
            }
        });
    };

    const updateFileInput = () => {
        if (!inputFile) return;

        const dataTransfer = new DataTransfer();
        filesArray.forEach(item => dataTransfer.items.add(item.file));
        inputFile.files = dataTransfer.files;
    };

    const renderDeletedExistingInputs = () => {
        if (!eliminadasContainer) return;
        eliminadasContainer.innerHTML = '';

        deletedExistingIds.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'imagenes_eliminadas[]';
            input.value = String(id);
            eliminadasContainer.appendChild(input);
        });
    };

    const renderExistingOrderInputs = () => {
        if (!existentesOrdenContainer) return;
        existentesOrdenContainer.innerHTML = '';

        existingImages.forEach((img) => {
            const inputId = document.createElement('input');
            inputId.type = 'hidden';
            inputId.name = 'imagenes_existentes_orden[]';
            inputId.value = String(img.id);

            existentesOrdenContainer.appendChild(inputId);
        });

        existingImages.forEach((img, index) => {
            img.order = index + 1;
        });
    };

    const updatePrincipalInputs = () => {
        if (!principalTipoInput || !principalIndexInput || !principalExistenteInput) return;

        const existingPrincipalIndex = existingImages.findIndex(img => img.isPrincipal);
        const newPrincipalIndex = filesArray.findIndex(img => img.isPrincipal);

        if (existingPrincipalIndex !== -1) {
            principalTipoInput.value = 'existente';
            principalExistenteInput.value = String(existingImages[existingPrincipalIndex].id);
            principalIndexInput.value = '0';
            return;
        }

        if (newPrincipalIndex !== -1) {
            principalTipoInput.value = 'nueva';
            principalExistenteInput.value = '';
            principalIndexInput.value = String(newPrincipalIndex);
            return;
        }

        if (existingImages.length > 0) {
            existingImages[0].isPrincipal = true;
            principalTipoInput.value = 'existente';
            principalExistenteInput.value = String(existingImages[0].id);
            principalIndexInput.value = '0';
            return;
        }

        if (filesArray.length > 0) {
            filesArray[0].isPrincipal = true;
            principalTipoInput.value = 'nueva';
            principalExistenteInput.value = '';
            principalIndexInput.value = '0';
            return;
        }

        principalTipoInput.value = 'existente';
        principalExistenteInput.value = '';
        principalIndexInput.value = '0';
    };

    const clearPrincipalFlags = () => {
        existingImages = existingImages.map(img => ({ ...img, isPrincipal: false }));
        filesArray = filesArray.map(img => ({ ...img, isPrincipal: false }));
    };

    const renderGallery = () => {
        if (!previewContainer) return;

        previewContainer.innerHTML = '';

        if (existingImages.length === 0 && filesArray.length === 0) {
            const placeholderItem = document.createElement('div');
            placeholderItem.className = 'image-placeholder';
            placeholderItem.innerHTML = '<i class="bx bx-image"></i><span>Sin imágenes</span>';
            previewContainer.appendChild(placeholderItem);
            updatePrincipalInputs();
            renderDeletedExistingInputs();
            renderExistingOrderInputs();
            return;
        }

        existingImages.forEach((item, idx) => {
            const div = document.createElement('div');
            div.className = 'image-preview-item existing-image-item';
            div.dataset.id = item.id;
            div.dataset.type = 'existing';
            div.dataset.index = idx;

            div.innerHTML = `
                <img src="${item.src}" alt="Preview">
                <button type="button" class="btn-remove-image" data-id="${item.id}" data-type="existing" title="Eliminar">✕</button>
                ${item.isPrincipal ? '<span class="principal-badge">Principal</span>' : ''}
                <button type="button" class="btn-set-principal" data-id="${item.id}" data-type="existing" title="Marcar como principal">★</button>
            `;

            previewContainer.appendChild(div);
        });

        filesArray.forEach((item, idx) => {
            if (!item.objectUrl) {
                item.objectUrl = URL.createObjectURL(item.file);
            }

            const div = document.createElement('div');
            div.className = 'image-preview-item new-image-item';
            div.dataset.type = 'new';
            div.dataset.index = idx;

            div.innerHTML = `
                <img src="${item.objectUrl}" alt="Preview">
                <button type="button" class="btn-remove-image" data-index="${idx}" data-type="new" title="Eliminar">✕</button>
                ${item.isPrincipal ? '<span class="principal-badge">Principal</span>' : ''}
                <button type="button" class="btn-set-principal" data-index="${idx}" data-type="new" title="Marcar como principal">★</button>
            `;

            previewContainer.appendChild(div);
        });

        updatePrincipalInputs();
        renderDeletedExistingInputs();
        renderExistingOrderInputs();
        initSortable();
    };

    const initSortable = () => {
        if (!previewContainer) return;

        if (sortableInstance) {
            sortableInstance.destroy();
        }

        if (previewContainer.children.length > 0 && !previewContainer.querySelector('.image-placeholder')) {
            sortableInstance = new Sortable(previewContainer, {
                animation: 150,
                handle: '.image-preview-item',
                onEnd: function () {
                    const orderedNodes = previewContainer.querySelectorAll('.image-preview-item');
                    const newExisting = [];
                    const newFiles = [];

                    orderedNodes.forEach(node => {
                        const type = node.dataset.type;

                        if (type === 'existing') {
                            const id = parseInt(node.dataset.id, 10);
                            const found = existingImages.find(img => img.id === id);
                            if (found) newExisting.push(found);
                        } else {
                            const idx = parseInt(node.dataset.index, 10);
                            const found = filesArray[idx];
                            if (found) newFiles.push(found);
                        }
                    });

                    existingImages = newExisting;
                    filesArray = newFiles;

                    updateFileInput();
                    renderGallery();
                }
            });
        }
    };

    const setPrincipalExisting = (imageId) => {
        clearPrincipalFlags();
        existingImages = existingImages.map(img => ({
            ...img,
            isPrincipal: img.id === imageId
        }));
        renderGallery();
    };

    const setPrincipalNew = (index) => {
        clearPrincipalFlags();
        filesArray = filesArray.map((img, i) => ({
            ...img,
            isPrincipal: i === index
        }));
        renderGallery();
        updateFileInput();
    };

    const removeExistingImage = (imageId) => {
        const removed = existingImages.find(img => img.id === imageId);
        existingImages = existingImages.filter(img => img.id !== imageId);
        deletedExistingIds.add(imageId);

        if (removed && removed.isPrincipal) {
            if (existingImages.length > 0) {
                existingImages[0].isPrincipal = true;
            } else if (filesArray.length > 0) {
                filesArray[0].isPrincipal = true;
            }
        }

        renderGallery();
    };

    const removeNewImage = (index) => {
        if (filesArray[index]?.objectUrl) {
            URL.revokeObjectURL(filesArray[index].objectUrl);
        }

        const wasPrincipal = !!filesArray[index]?.isPrincipal;
        filesArray.splice(index, 1);

        if (wasPrincipal) {
            if (existingImages.length > 0) {
                clearPrincipalFlags();
                existingImages[0].isPrincipal = true;
            } else if (filesArray.length > 0) {
                clearPrincipalFlags();
                filesArray[0].isPrincipal = true;
            }
        }

        renderGallery();
        updateFileInput();
    };

    if (inputFile) {
        inputFile.addEventListener('change', (e) => {
            const newFiles = Array.from(e.target.files || []);
            if (newFiles.length === 0) return;

            newFiles.forEach((file, idx) => {
                filesArray.push({
                    file: file,
                    isPrincipal: existingImages.length === 0 && filesArray.length === 0 && idx === 0,
                    objectUrl: null
                });
            });

            const hasPrincipal = existingImages.some(img => img.isPrincipal) || filesArray.some(img => img.isPrincipal);
            if (!hasPrincipal && filesArray.length > 0) {
                filesArray[0].isPrincipal = true;
            }

            renderGallery();
            updateFileInput();
        });
    }

    if (previewContainer) {
        previewContainer.addEventListener('click', (e) => {
            const removeBtn = e.target.closest('.btn-remove-image');
            const principalBtn = e.target.closest('.btn-set-principal');

            if (removeBtn) {
                const type = removeBtn.dataset.type;

                if (type === 'existing') {
                    removeExistingImage(parseInt(removeBtn.dataset.id, 10));
                } else {
                    removeNewImage(parseInt(removeBtn.dataset.index, 10));
                }
            }

            if (principalBtn) {
                const type = principalBtn.dataset.type;

                if (type === 'existing') {
                    setPrincipalExisting(parseInt(principalBtn.dataset.id, 10));
                } else {
                    setPrincipalNew(parseInt(principalBtn.dataset.index, 10));
                }
            }
        });
    }

    window.addEventListener('beforeunload', () => {
        revokeAllObjectUrls();
    });

    renderGallery();

    /* =========================================
       PRODUCTOS RELACIONADOS
    ========================================= */
    const gridContainer = document.getElementById('productosRelacionadosGrid');
    const searchInput = document.getElementById('searchProductoRel');
    const selectMultiple = document.getElementById('productosRelacionadosSelect');
    const previewRelacionados = document.getElementById('relacionadosPreview');
    const selectedCountSpan = document.getElementById('selectedCount');

    let selectedProductIds = new Set();

    function updateRelatedUI() {
        if (selectedCountSpan) {
            selectedCountSpan.textContent = selectedProductIds.size;
        }

        if (selectMultiple) {
            selectMultiple.innerHTML = '';
            selectedProductIds.forEach(id => {
                const option = document.createElement('option');
                option.value = id;
                option.selected = true;
                selectMultiple.appendChild(option);
            });
        }

        if (previewRelacionados) {
            previewRelacionados.innerHTML = '';

            if (selectedProductIds.size === 0) {
                previewRelacionados.innerHTML = '<span class="text-muted small">Ningún producto relacionado seleccionado.</span>';
            } else {
                selectedProductIds.forEach(id => {
                    const productCard = document.querySelector(`.producto-rel-item[data-id="${id}"]`);
                    const nombre = productCard?.getAttribute('data-nombre') || `Producto ID: ${id}`;

                    const badge = document.createElement('span');
                    badge.className = 'selected-product-badge';
                    badge.innerHTML = `
                        <i class="bx bx-package"></i>
                        ${nombre.length > 30 ? nombre.substring(0, 27) + '...' : nombre}
                        <i class="bx bx-x remove-related" data-id="${id}" style="cursor:pointer;"></i>
                    `;
                    previewRelacionados.appendChild(badge);
                });
            }
        }

        document.querySelectorAll('.producto-rel-item').forEach(item => {
            const productId = parseInt(item.getAttribute('data-id'), 10);
            const card = item.querySelector('.producto-rel-card');
            const checkbox = item.querySelector('.product-checkbox');

            if (selectedProductIds.has(productId)) {
                card?.classList.add('selected');
                if (checkbox) checkbox.checked = true;
            } else {
                card?.classList.remove('selected');
                if (checkbox) checkbox.checked = false;
            }
        });
    }

    function toggleProduct(productId) {
        if (selectedProductIds.has(productId)) {
            selectedProductIds.delete(productId);
        } else {
            selectedProductIds.add(productId);
        }

        updateRelatedUI();
    }

    if (gridContainer) {
        gridContainer.addEventListener('click', (e) => {
            const productCard = e.target.closest('.producto-rel-card');
            if (!productCard) return;
            if (e.target.classList.contains('product-checkbox')) return;

            const parentItem = productCard.closest('.producto-rel-item');
            if (parentItem) {
                const productId = parseInt(parentItem.getAttribute('data-id'), 10);
                toggleProduct(productId);
            }
        });

        gridContainer.addEventListener('change', (e) => {
            if (e.target.classList.contains('product-checkbox')) {
                const parentItem = e.target.closest('.producto-rel-item');
                if (parentItem) {
                    const productId = parseInt(parentItem.getAttribute('data-id'), 10);
                    toggleProduct(productId);
                }
            }
        });
    }

    if (searchInput && gridContainer) {
        searchInput.addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase().trim();
            const items = gridContainer.querySelectorAll('.producto-rel-item');

            items.forEach(item => {
                const nombre = item.getAttribute('data-nombre')?.toLowerCase() || '';
                const sku = item.getAttribute('data-sku')?.toLowerCase() || '';
                const codigo = item.getAttribute('data-codigo')?.toLowerCase() || '';

                const matches = searchTerm === '' ||
                    nombre.includes(searchTerm) ||
                    sku.includes(searchTerm) ||
                    codigo.includes(searchTerm);

                item.style.display = matches ? '' : 'none';
            });
        });
    }

    if (previewRelacionados) {
        previewRelacionados.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-related') || e.target.parentElement?.classList.contains('remove-related')) {
                const target = e.target.classList.contains('remove-related') ? e.target : e.target.parentElement;
                const productId = parseInt(target.getAttribute('data-id'), 10);

                if (!isNaN(productId)) {
                    toggleProduct(productId);
                }
            }
        });
    }

    if (selectMultiple && selectMultiple.options.length > 0) {
        const existingIds = Array.from(selectMultiple.options).map(opt => parseInt(opt.value, 10));
        existingIds.forEach(id => {
            if (!isNaN(id)) selectedProductIds.add(id);
        });
    }

    updateRelatedUI();

    /* =========================================
       COLAPSABLE RELACIONADOS
    ========================================= */
    const toggleButton = document.getElementById('toggleRelatedProducts');
    const content = document.getElementById('relatedProductsContent');
    const icon = document.getElementById('toggleIcon');

    let isExpanded = false;

    function toggleContent() {
        if (!content || !icon) return;

        if (isExpanded) {
            content.style.display = 'none';
            icon.classList.remove('bx-chevron-up');
            icon.classList.add('bx-chevron-down');
            isExpanded = false;
        } else {
            content.style.display = 'block';
            icon.classList.remove('bx-chevron-down');
            icon.classList.add('bx-chevron-up');
            isExpanded = true;
        }
    }

    if (toggleButton) {
        toggleButton.addEventListener('click', toggleContent);
    }

    if (selectMultiple && selectMultiple.options.length > 0 && content && icon) {
        content.style.display = 'block';
        icon.classList.remove('bx-chevron-down');
        icon.classList.add('bx-chevron-up');
        isExpanded = true;
    }

    /* =========================================
       CATEGORÍAS ADICIONALES
    ========================================= */
    const categoriasGrid = document.getElementById('categoriasAdicionalesGrid');
    const categoriasSearchInput = document.getElementById('searchCategoriaAdicional');
    const categoriasSelectMultiple = document.getElementById('categoriasAdicionalesSelect');
    const categoriasPreview = document.getElementById('categoriasAdicionalesPreview');
    const categoriasCountSpan = document.getElementById('selectedCategoriasCount');
    const categoriaPrincipalSelect = document.getElementById('id_categoria_principal');

    const toggleCategoriasButton = document.getElementById('toggleCategoriasAdicionales');
    const categoriasContent = document.getElementById('categoriasAdicionalesContent');
    const categoriasIcon = document.getElementById('toggleCategoriasIcon');

    let selectedCategoryIds = new Set();
    let categoriasExpanded = false;

    function updateCategoriasUI() {
        if (categoriasCountSpan) {
            categoriasCountSpan.textContent = selectedCategoryIds.size;
        }

        if (categoriasSelectMultiple) {
            categoriasSelectMultiple.innerHTML = '';

            selectedCategoryIds.forEach(id => {
                const option = document.createElement('option');
                option.value = id;
                option.selected = true;
                categoriasSelectMultiple.appendChild(option);
            });
        }

        if (categoriasPreview) {
            categoriasPreview.innerHTML = '';

            if (selectedCategoryIds.size === 0) {
                categoriasPreview.innerHTML = '<span class="text-muted small">Ninguna categoría adicional seleccionada.</span>';
            } else {
                selectedCategoryIds.forEach(id => {
                    const categoriaItem = document.querySelector(`.categoria-adicional-item[data-id="${id}"]`);
                    const nombre = categoriaItem?.getAttribute('data-nombre') || `Categoría ID: ${id}`;

                    const badge = document.createElement('span');
                    badge.className = 'selected-category-badge';
                    badge.innerHTML = `
                        <i class="bx bx-category"></i>
                        ${nombre.length > 30 ? nombre.substring(0, 27) + '...' : nombre}
                        <i class="bx bx-x remove-category" data-id="${id}" style="cursor:pointer;"></i>
                    `;
                    categoriasPreview.appendChild(badge);
                });
            }
        }

        document.querySelectorAll('.categoria-adicional-item').forEach(item => {
            const categoryId = parseInt(item.getAttribute('data-id'), 10);
            const card = item.querySelector('.categoria-adicional-card');
            const checkbox = item.querySelector('.categoria-checkbox');

            if (selectedCategoryIds.has(categoryId)) {
                card?.classList.add('selected');
                if (checkbox) checkbox.checked = true;
            } else {
                card?.classList.remove('selected');
                if (checkbox) checkbox.checked = false;
            }
        });
    }

    function syncCategoriaPrincipalExclusion() {
        if (!categoriaPrincipalSelect) return;

        const principalId = parseInt(categoriaPrincipalSelect.value, 10);

        document.querySelectorAll('.categoria-adicional-item').forEach(item => {
            const categoryId = parseInt(item.getAttribute('data-id'), 10);

            if (!isNaN(principalId) && categoryId === principalId) {
                item.style.display = 'none';
                selectedCategoryIds.delete(categoryId);
            } else {
                item.style.display = '';
            }
        });

        updateCategoriasUI();
    }

    function toggleCategoria(categoryId) {
        if (selectedCategoryIds.has(categoryId)) {
            selectedCategoryIds.delete(categoryId);
        } else {
            selectedCategoryIds.add(categoryId);
        }

        updateCategoriasUI();
    }

    function toggleCategoriasContent() {
        if (!categoriasContent || !categoriasIcon) return;

        if (categoriasExpanded) {
            categoriasContent.style.display = 'none';
            categoriasIcon.classList.remove('bx-chevron-up');
            categoriasIcon.classList.add('bx-chevron-down');
            categoriasExpanded = false;
        } else {
            categoriasContent.style.display = 'block';
            categoriasIcon.classList.remove('bx-chevron-down');
            categoriasIcon.classList.add('bx-chevron-up');
            categoriasExpanded = true;
        }
    }

    if (categoriasGrid) {
        categoriasGrid.addEventListener('click', (e) => {
            const categoryCard = e.target.closest('.categoria-adicional-card');
            if (!categoryCard) return;
            if (e.target.classList.contains('categoria-checkbox')) return;

            const parentItem = categoryCard.closest('.categoria-adicional-item');
            if (parentItem) {
                const categoryId = parseInt(parentItem.getAttribute('data-id'), 10);
                toggleCategoria(categoryId);
            }
        });

        categoriasGrid.addEventListener('change', (e) => {
            if (e.target.classList.contains('categoria-checkbox')) {
                const parentItem = e.target.closest('.categoria-adicional-item');
                if (parentItem) {
                    const categoryId = parseInt(parentItem.getAttribute('data-id'), 10);
                    toggleCategoria(categoryId);
                }
            }
        });
    }

    if (categoriasSearchInput && categoriasGrid) {
        categoriasSearchInput.addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase().trim();
            const items = categoriasGrid.querySelectorAll('.categoria-adicional-item');
            const principalId = parseInt(categoriaPrincipalSelect?.value, 10);

            items.forEach(item => {
                const categoryId = parseInt(item.getAttribute('data-id'), 10);
                const nombre = item.getAttribute('data-nombre')?.toLowerCase() || '';
                const slug = item.getAttribute('data-slug')?.toLowerCase() || '';

                const isPrincipal = !isNaN(principalId) && categoryId === principalId;
                const matches = searchTerm === '' ||
                    nombre.includes(searchTerm) ||
                    slug.includes(searchTerm);

                item.style.display = (!isPrincipal && matches) ? '' : 'none';
            });
        });
    }

    if (categoriasPreview) {
        categoriasPreview.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-category') || e.target.parentElement?.classList.contains('remove-category')) {
                const target = e.target.classList.contains('remove-category') ? e.target : e.target.parentElement;
                const categoryId = parseInt(target.getAttribute('data-id'), 10);

                if (!isNaN(categoryId)) {
                    toggleCategoria(categoryId);
                }
            }
        });
    }

    if (categoriasSelectMultiple && categoriasSelectMultiple.options.length > 0) {
        const existingIds = Array.from(categoriasSelectMultiple.options).map(opt => parseInt(opt.value, 10));
        existingIds.forEach(id => {
            if (!isNaN(id)) selectedCategoryIds.add(id);
        });
    }

    if (categoriaPrincipalSelect) {
        categoriaPrincipalSelect.addEventListener('change', () => {
            syncCategoriaPrincipalExclusion();
        });
    }

    if (toggleCategoriasButton) {
        toggleCategoriasButton.addEventListener('click', toggleCategoriasContent);
    }

    updateCategoriasUI();
    syncCategoriaPrincipalExclusion();

    if (categoriasSelectMultiple && categoriasSelectMultiple.options.length > 0 && categoriasContent && categoriasIcon) {
        categoriasContent.style.display = 'block';
        categoriasIcon.classList.remove('bx-chevron-down');
        categoriasIcon.classList.add('bx-chevron-up');
        categoriasExpanded = true;
    }

const destacadoSwitch = document.getElementById('destacadoSwitch');
const destacadoTexto = document.getElementById('destacadoTexto');

if (destacadoSwitch && destacadoTexto) {

    const actualizarDestacado = () => {

        if (destacadoSwitch.checked) {

            destacadoTexto.classList.remove('bg-secondary');
            destacadoTexto.classList.add('bg-success');

            destacadoTexto.innerHTML =
                '<i class="bx bx-star me-1"></i> Destacado';

        } else {

            destacadoTexto.classList.remove('bg-success');
            destacadoTexto.classList.add('bg-secondary');

            destacadoTexto.innerHTML =
                '<i class="bx bx-package me-1"></i> Normal';
        }
    };

    destacadoSwitch.addEventListener('change', actualizarDestacado);

    actualizarDestacado();
}





/* =========================================
   DESCUENTO PRODUCTO
========================================= */
const descuentoSwitch = document.getElementById('descuentoSwitch');
const descuentoTexto = document.getElementById('descuentoTexto');
const descuentoCampos = document.getElementById('descuentoCampos');
const precioInput = document.querySelector('input[name="precio"]');
const precioDescuentoInput = document.getElementById('precio_descuento');
const descuentoPreview = document.getElementById('descuentoPreview');
const descuentoPreviewPorcentaje = document.getElementById('descuentoPreviewPorcentaje');
const descuentoPreviewAhorro = document.getElementById('descuentoPreviewAhorro');
const productoForm = precioInput ? precioInput.closest('form') : null;

function formatoColones(valor) {
    return '₡' + Math.round(valor).toLocaleString('es-CR');
}

function limpiarErrorDescuento() {
    if (!precioDescuentoInput) return;

    precioDescuentoInput.classList.remove('is-invalid');

    const errorActual = document.getElementById('precioDescuentoJsError');

    if (errorActual) {
        errorActual.remove();
    }
}

function mostrarErrorDescuento(mensaje) {
    if (!precioDescuentoInput) return;

    limpiarErrorDescuento();

    precioDescuentoInput.classList.add('is-invalid');

    const error = document.createElement('div');
    error.id = 'precioDescuentoJsError';
    error.className = 'invalid-feedback d-block';
    error.textContent = mensaje;

    const grupoInput = precioDescuentoInput.closest('.input-group');

    if (grupoInput) {
        grupoInput.insertAdjacentElement('afterend', error);
    } else {
        precioDescuentoInput.insertAdjacentElement('afterend', error);
    }
}

function descuentoEsValido() {
    if (!descuentoSwitch || !precioInput || !precioDescuentoInput) {
        return true;
    }

    if (!descuentoSwitch.checked) {
        limpiarErrorDescuento();
        return true;
    }

    const precio = parseFloat(precioInput.value || 0);
    const precioDescuento = parseFloat(precioDescuentoInput.value || 0);

    if (precioDescuento <= 0) {
        limpiarErrorDescuento();
        return true;
    }

    if (precio <= 0) {
        limpiarErrorDescuento();
        return true;
    }

    if (precioDescuento >= precio) {
        mostrarErrorDescuento('El precio con descuento debe ser menor que el precio normal.');
        descuentoPreview.style.display = 'none';
        return false;
    }

    limpiarErrorDescuento();
    return true;
}

function actualizarPreviewDescuento() {
    if (
        !descuentoSwitch ||
        !precioInput ||
        !precioDescuentoInput ||
        !descuentoPreview ||
        !descuentoPreviewPorcentaje ||
        !descuentoPreviewAhorro
    ) return;

    const precio = parseFloat(precioInput.value || 0);
    const precioDescuento = parseFloat(precioDescuentoInput.value || 0);

    if (!descuentoEsValido()) {
        descuentoPreview.style.display = 'none';
        return;
    }

    if (
        !descuentoSwitch.checked ||
        precio <= 0 ||
        precioDescuento <= 0
    ) {
        descuentoPreview.style.display = 'none';
        return;
    }

    const ahorro = precio - precioDescuento;
    const porcentaje = Math.round((ahorro / precio) * 100);

    descuentoPreviewPorcentaje.textContent = `-${porcentaje}% OFF`;
    descuentoPreviewAhorro.textContent = formatoColones(ahorro);
    descuentoPreview.style.display = 'block';
}

function actualizarDescuentoProducto() {
    if (!descuentoSwitch || !descuentoTexto || !descuentoCampos) return;

    if (descuentoSwitch.checked) {
        descuentoTexto.classList.remove('bg-secondary');
        descuentoTexto.classList.add('bg-success');
        descuentoTexto.innerHTML = '<i class="bx bx-check-circle me-1"></i> Con descuento';
        descuentoCampos.style.display = 'block';
    } else {
        descuentoTexto.classList.remove('bg-success');
        descuentoTexto.classList.add('bg-secondary');
        descuentoTexto.innerHTML = '<i class="bx bx-x-circle me-1"></i> Sin descuento';
        descuentoCampos.style.display = 'none';
        limpiarErrorDescuento();
    }

    actualizarPreviewDescuento();
}

if (descuentoSwitch) {
    descuentoSwitch.addEventListener('change', actualizarDescuentoProducto);
}

if (precioInput) {
    precioInput.addEventListener('input', actualizarPreviewDescuento);
}

if (precioDescuentoInput) {
    precioDescuentoInput.addEventListener('input', actualizarPreviewDescuento);
}

if (productoForm) {
    productoForm.addEventListener('submit', function (e) {
        if (!descuentoEsValido()) {
            e.preventDefault();
            precioDescuentoInput.focus();
        }
    });
}

actualizarDescuentoProducto();

});