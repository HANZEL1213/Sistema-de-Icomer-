/* =========================================
carrito
========================================= */

document.addEventListener('DOMContentLoaded', function () {

    const cargasEnCurso = new Map(); // fila -> petición en curso

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

    function cargarMasProductos(row) {
        if (!row || row.dataset.hasMore !== '1') return Promise.resolve(false);
        if (cargasEnCurso.has(row)) return cargasEnCurso.get(row);

        const page = parseInt(row.dataset.nextPage || '2', 10);
        row.dataset.loading = '1';

        const promesa = fetch(`${row.dataset.loadUrl}?page=${page}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
            .then(response => {
                if (!response.ok) throw new Error('HTTP ' + response.status);
                return response.json();
            })
            .then(data => {
                row.insertAdjacentHTML('beforeend', data.html || '');
                row.dataset.nextPage = page + 1;
                row.dataset.hasMore = data.has_more ? '1' : '0';
                return true;
            })
            .catch(error => {
                console.error(error);
                row.dataset.hasMore = '0';
                return false;
            })
            .finally(() => {
                row.dataset.loading = '0';
                cargasEnCurso.delete(row);
            });

        cargasEnCurso.set(row, promesa);
        return promesa;
    }

    function estaCercaDelFinal(target) {
        return target.scrollLeft + target.clientWidth >= target.scrollWidth - (target.clientWidth * 1.4);
    }

    async function moverCarrusel(target, direction) {
        if (!target) return;

        const esLazy = target.classList.contains('js-lazy-row');

        if (esLazy && direction === 1 && estaCercaDelFinal(target)) {
            await cargarMasProductos(target);
        }

        requestAnimationFrame(() => {
            target.scrollBy({
                left: target.clientWidth * 0.85 * direction,
                behavior: 'smooth'
            });
        });

        if (esLazy && direction === 1) {
            setTimeout(() => {
                if (estaCercaDelFinal(target)) {
                    cargarMasProductos(target);
                }
            }, 500);
        }
    }

    function activarCargaPorScrollManual() {
        document.querySelectorAll('.js-lazy-row').forEach(function (row) {
            let timer = null;

            row.addEventListener('scroll', function () {
                clearTimeout(timer);

                timer = setTimeout(function () {
                    if (estaCercaDelFinal(row)) {
                        cargarMasProductos(row);
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