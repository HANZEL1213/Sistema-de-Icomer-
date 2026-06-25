document.addEventListener('DOMContentLoaded', function () {

    const searchWraps = document.querySelectorAll(
        '.store-header-search-wrap'
    );

    searchWraps.forEach(function (wrap) {

        const searchInput = wrap.querySelector(
            '.store-live-search-input'
        );

        const suggestions = wrap.querySelector(
            '.store-search-suggestions'
        );

        const searchUrl = wrap.dataset.searchUrl;

        let currentIndex = -1;

        if (!searchInput || !suggestions || !searchUrl) {
            return;
        }

        async function cargarSugerencias(value) {

            try {

                const response = await fetch(
                    `${searchUrl}?q=${encodeURIComponent(value)}`
                );

                const data = await response.json();

                currentIndex = -1;

                if (!data.length) {

                    suggestions.innerHTML = `
                        <div class="store-search-empty">
                            No se encontraron productos
                        </div>
                    `;

                    suggestions.classList.add('show');

                    return;
                }

                suggestions.innerHTML = data.map(producto => `
                    <a href="${producto.url}"
                       class="store-search-item">

                        <img src="${producto.imagen}"
                             alt="${producto.nombre}">

                        <div class="store-search-item-body">

                            <div class="store-search-item-title">
                                ${producto.nombre}
                            </div>

                            <div class="store-search-item-meta">
                                ${producto.marca ?? ''}
                            </div>

                        </div>

                        <div class="store-search-item-price">
                            ₡${producto.precio}
                        </div>

                    </a>
                `).join('');

                suggestions.classList.add('show');

            } catch (error) {

                console.error(error);

            }

        }

        function actualizarActivo(items) {

            items.forEach(item => {
                item.classList.remove('active');
            });

            if (currentIndex >= 0 && items[currentIndex]) {

                items[currentIndex].classList.add('active');

                items[currentIndex].scrollIntoView({
                    block: 'nearest'
                });

            }

        }

        searchInput.addEventListener('input', function () {

            const value = this.value.trim();

            if (value.length < 2) {

                suggestions.innerHTML = '';

                suggestions.classList.remove('show');

                return;
            }

            cargarSugerencias(value);

        });

        searchInput.addEventListener('keydown', function (e) {

            const items = suggestions.querySelectorAll(
                '.store-search-item'
            );

            if (!items.length) return;

            // ⬇️
            if (e.key === 'ArrowDown') {

                e.preventDefault();

                currentIndex++;

                if (currentIndex >= items.length) {
                    currentIndex = 0;
                }

                actualizarActivo(items);

            }

            // ⬆️
            if (e.key === 'ArrowUp') {

                e.preventDefault();

                currentIndex--;

                if (currentIndex < 0) {
                    currentIndex = items.length - 1;
                }

                actualizarActivo(items);

            }

            // ENTER
            if (e.key === 'Enter') {

                if (currentIndex >= 0 && items[currentIndex]) {

                    e.preventDefault();

                    window.location.href =
                        items[currentIndex].href;

                }

            }

            // ESC
            if (e.key === 'Escape') {

                suggestions.classList.remove('show');

            }

        });

    });

    document.addEventListener('click', function (e) {

        if (!e.target.closest('.store-header-search-wrap')) {

            document.querySelectorAll(
                '.store-search-suggestions'
            ).forEach(item => {

                item.classList.remove('show');

            });

        }

    });

});
/* ==========================================================
   FAVORITOS / CORAZONES
========================================================== */

document.addEventListener('click', async function (event) {

    const button = event.target.closest('.js-favorite-btn');

    if (!button) return;

    event.preventDefault();
    event.stopPropagation();

    const url = button.dataset.url;
    const icon = button.querySelector('i');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    if (!url || !csrfToken || !icon) return;

    button.disabled = true;
    button.classList.add('is-loading');

    try {

        const response = await fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
        });

        const data = await response.json();

        if (!response.ok || !data.success) return;

        if (data.favorito) {

            button.classList.add('is-active');
            icon.classList.remove('bi-heart');
            icon.classList.add('bi-heart-fill');

        } else {

            button.classList.remove('is-active');
            icon.classList.remove('bi-heart-fill');
            icon.classList.add('bi-heart');

            const favoritosPage = document.querySelector('.store-favorites-page');

            if (favoritosPage) {

                const card = button.closest('.col-6, .col-md-4, .col-xl-3');

                if (card) {
                    card.remove();
                }

            }

        }

        /*
        |--------------------------------------------------------------------------
        | ACTUALIZAR CONTADORES FAVORITOS
        |--------------------------------------------------------------------------
        */

        const counters = document.querySelectorAll('.js-favorites-count');

        counters.forEach(function (counter) {

            counter.textContent = data.cantidadFavoritos;

            if (data.cantidadFavoritos > 0) {
                counter.style.display = 'inline-flex';
            } else {
                counter.style.display = 'none';
            }

        });

    } catch (error) {

        console.error('Error al actualizar favorito:', error);

    } finally {

        button.disabled = false;
        button.classList.remove('is-loading');

    }

});


window.addEventListener('pageshow', function (event) {
    const navEntries = performance.getEntriesByType('navigation');
    const isBackForward = navEntries.length && navEntries[0].type === 'back_forward';

    if (event.persisted || isBackForward) {
        window.location.reload();
    }
});

/* ==========================================================
   EVITAR DOBLE ENVÍO DE FORMULARIOS
========================================================== */

document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('form:not([data-skip-double-submit])').forEach(function (form) {

        form.addEventListener('submit', function (e) {

            if (!form.checkValidity()) {
                return;
            }

            const submitBtn = form.querySelector('[type="submit"]');

            if (!submitBtn) return;

            if (submitBtn.disabled) {
                e.preventDefault();
                return;
            }

            submitBtn.disabled = true;
            submitBtn.dataset.textoOriginal = submitBtn.innerHTML;
            submitBtn.innerHTML = 'Procesando...';

        });

    });

});