/* =========================================
carrito
========================================= */

document.addEventListener('DOMContentLoaded', function () {

    let productosPreloadPromise = null;
    let scrollTimer = null;

    function actualizarContadorCarrito(totalItems) {
        document.querySelectorAll('.js-cart-count').forEach(function (cartCount) {
            cartCount.textContent = totalItems;
            cartCount.style.display = totalItems > 0 ? '' : 'none';
        });
    }

    function marcarProductoAgregado(btnAgregado) {
        const cartKey = btnAgregado.dataset.cartKey;
        const productId = btnAgregado.dataset.productId;

        const selector = cartKey
            ? `.js-add-cart[data-cart-key="${cartKey}"]`
            : `.js-add-cart[data-product-id="${productId}"]`;

        document.querySelectorAll(selector).forEach(function (button) {
            button.classList.add('is-added');
            button.innerHTML = '<i class="bi bi-check-lg"></i>';
            button.title = 'Producto agregado';
            button.disabled = true;
        });
    }

    async function cargarMasProductos() {
        const fila1 = document.querySelector('#homeProductsCarouselOne');
        const fila2 = document.querySelector('#homeProductsCarouselTwo');

        if (!fila1 || !fila2) return false;
        if (fila1.dataset.hasMore !== '1') return false;

        if (productosPreloadPromise) {
            return productosPreloadPromise;
        }

        const page = parseInt(fila1.dataset.nextPage || '2', 10);
        const url = `${fila1.dataset.loadUrl}?page=${page}`;

        fila1.dataset.loading = '1';

        productosPreloadPromise = fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {
                fila1.insertAdjacentHTML('beforeend', data.html_fila_1 || '');
                fila2.insertAdjacentHTML('beforeend', data.html_fila_2 || '');

                fila1.dataset.nextPage = page + 1;
                fila1.dataset.hasMore = data.has_more ? '1' : '0';

                return true;
            })
            .catch(error => {
                console.error(error);
                return false;
            })
            .finally(() => {
                fila1.dataset.loading = '0';
                productosPreloadPromise = null;
            });

        return productosPreloadPromise;
    }

    function estaCercaDelFinal(target) {
        return target.scrollLeft + target.clientWidth >= target.scrollWidth - (target.clientWidth * 1.4);
    }

    async function moverCarrusel(target, direction) {
        if (!target) return;

        const esProducto =
            target.id === 'homeProductsCarouselOne' ||
            target.id === 'homeProductsCarouselTwo';

        if (esProducto && direction === 1 && estaCercaDelFinal(target)) {
            await cargarMasProductos();
        }

        requestAnimationFrame(() => {
            target.scrollBy({
                left: target.clientWidth * 0.85 * direction,
                behavior: 'smooth'
            });
        });

        if (esProducto && direction === 1) {
            setTimeout(() => {
                if (estaCercaDelFinal(target)) {
                    cargarMasProductos();
                }
            }, 500);
        }
    }

    function activarCargaPorScrollManual() {
        const carruselesProductos = [
            document.querySelector('#homeProductsCarouselOne'),
            document.querySelector('#homeProductsCarouselTwo')
        ].filter(Boolean);

        carruselesProductos.forEach(function (carousel) {
            carousel.addEventListener('scroll', function () {
                clearTimeout(scrollTimer);

                scrollTimer = setTimeout(function () {
                    if (estaCercaDelFinal(carousel)) {
                        cargarMasProductos();
                    }
                }, 120);
            }, { passive: true });
        });
    }

    document.querySelectorAll('.js-home-carousel-prev, .js-home-carousel-next').forEach(function (button) {
        button.addEventListener('click', function () {
            const target = document.querySelector(this.dataset.target);
            const direction = this.classList.contains('js-home-carousel-next') ? 1 : -1;

            moverCarrusel(target, direction);
        });
    });

    document.addEventListener('click', async function (e) {
        const btn = e.target.closest('.js-add-cart');

        if (!btn) return;

        e.preventDefault();
        e.stopPropagation();

        if (btn.disabled || btn.classList.contains('is-added')) return;

        const iconoOriginal = btn.innerHTML;

        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i>';

        const payload = {
            cantidad: 1
        };

        if (btn.dataset.productVariantId) {
            payload.id_producto_variante = btn.dataset.productVariantId;
        }

        try {
            const response = await fetch(btn.dataset.url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                btn.innerHTML = iconoOriginal;
                btn.disabled = false;
                alert(data.message || 'No se pudo agregar al carrito.');
                return;
            }

            actualizarContadorCarrito(data.total_items);
            marcarProductoAgregado(btn);

        } catch (error) {
            console.error(error);
            btn.innerHTML = iconoOriginal;
            btn.disabled = false;
            alert('Error al agregar al carrito.');
        }
    });

    activarCargaPorScrollManual();

});