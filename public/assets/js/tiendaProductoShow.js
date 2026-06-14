document.addEventListener('DOMContentLoaded', function () {

    const mainImage = document.getElementById('storeProductMainImage');
    const thumbs = document.querySelectorAll('.store-product-thumb');

    thumbs.forEach((thumb) => {
        thumb.addEventListener('click', function () {
            const image = this.dataset.productImage;

            if (mainImage && image) {
                mainImage.src = image;
            }

            thumbs.forEach((item) => item.classList.remove('active'));
            this.classList.add('active');
        });
    });

    const qtyInput = document.getElementById('storeProductQty');
    const qtyHidden = document.getElementById('storeProductQtyHidden');
    const qtyButtons = document.querySelectorAll('[data-qty-action]');

    const variantButtons = document.querySelectorAll('.store-variant-btn');
    const variantHidden = document.getElementById('storeProductVariantHidden');
    const priceText = document.getElementById('storeProductPrice');
    const stockText = document.getElementById('storeProductStockText');
    const addCartBtn = document.getElementById('storeAddCartBtn');
    const variantHelp = document.getElementById('storeVariantHelp');
    const cartForm = document.getElementById('storeCartForm');

    let precioUnitarioActual = Number(priceText?.dataset.precioBase || 0);

    function formatCRC(amount) {
        return '₡' + Number(amount).toLocaleString('es-CR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function actualizarPrecioTotal() {
        if (!priceText || !qtyInput) return;

        const cantidad = parseInt(qtyInput.value || 1);
        const total = precioUnitarioActual * cantidad;

        priceText.textContent = formatCRC(total);
    }

    function seleccionarVariante(button) {
        if (!button) return;

        variantButtons.forEach((btn) => {
            btn.classList.remove('active');
            btn.classList.remove('is-active');
        });

        button.classList.add('active');
        button.classList.add('is-active');

        const id = button.dataset.id;
        const stock = parseInt(button.dataset.stock || 0);
        const precio = parseFloat(button.dataset.precio || 0);

        precioUnitarioActual = precio;

        if (variantHidden) {
            variantHidden.value = id;
        }

        if (qtyInput) {
            qtyInput.max = Math.max(stock, 1);
            qtyInput.value = 1;
        }

        if (qtyHidden) {
            qtyHidden.value = 1;
        }

        actualizarPrecioTotal();

        if (stockText) {
            stockText.classList.remove('is-available', 'is-empty');

            if (stock > 0) {
                stockText.classList.add('is-available');
                stockText.innerHTML = '<i class="bi bi-check-circle"></i> Disponible · ' + stock + ' unidades';
            } else {
                stockText.classList.add('is-empty');
                stockText.innerHTML = '<i class="bi bi-x-circle"></i> Producto agotado';
            }
        }

        if (addCartBtn) {
            addCartBtn.disabled = stock <= 0;
        }

        if (variantHelp) {
            variantHelp.textContent = stock > 0
                ? 'Opción seleccionada.'
                : 'Esta opción está agotada.';

            variantHelp.classList.remove('text-danger');
            variantHelp.classList.add('text-muted');
        }
    }

    if (qtyInput && qtyHidden) {
        qtyButtons.forEach((button) => {
            button.addEventListener('click', function () {
                const action = this.dataset.qtyAction;

                const min = parseInt(qtyInput.min || 1);
                const max = parseInt(qtyInput.max || 999);

                let value = parseInt(qtyInput.value || 1);

                if (action === 'minus') {
                    value = Math.max(min, value - 1);
                }

                if (action === 'plus') {
                    value = Math.min(max, value + 1);
                }

                qtyInput.value = value;
                qtyHidden.value = value;

                actualizarPrecioTotal();
            });
        });

        qtyInput.addEventListener('input', function () {
            let value = parseInt(this.value || 1);

            const min = parseInt(this.min || 1);
            const max = parseInt(this.max || 999);

            if (value < min) value = min;
            if (value > max) value = max;

            this.value = value;
            qtyHidden.value = value;

            actualizarPrecioTotal();
        });
    }

    variantButtons.forEach((button) => {
        button.addEventListener('click', function () {
            seleccionarVariante(this);
        });
    });

    const varianteInicialBtn =
        document.querySelector('.store-variant-btn.active') ||
        document.querySelector('.store-variant-btn.is-active') ||
        document.querySelector('.store-variant-btn:not(:disabled)');

    if (varianteInicialBtn && variantHidden) {
        seleccionarVariante(varianteInicialBtn);
    }

    if (cartForm && variantHidden) {
        cartForm.addEventListener('submit', function (event) {
            if (!variantHidden.value) {
                event.preventDefault();

                if (variantHelp) {
                    variantHelp.textContent = 'Debes seleccionar una opción antes de agregar al carrito.';
                    variantHelp.classList.remove('text-muted');
                    variantHelp.classList.add('text-danger');
                }
            }
        });
    }

    actualizarPrecioTotal();
});