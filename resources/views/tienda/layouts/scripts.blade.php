<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="{{ asset('assets/js/tienda.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {

        /* ============================================
           EVITAR ZOOM EXTRA EN INPUTS MOBILE
        ============================================ */
        document.querySelectorAll('input, select, textarea').forEach((el) => {
            el.addEventListener('focus', () => {
                el.style.fontSize = '16px';
            });
        });


        /* ============================================
           MICRO FEEDBACK TÁCTIL MOBILE
        ============================================ */
        document.querySelectorAll('.store-card, .btn-store-primary, .btn-store-outline, .store-icon-btn').forEach((el) => {
            el.addEventListener('touchstart', () => {
                if (window.navigator && window.navigator.vibrate) {
                    window.navigator.vibrate(15);
                }
            }, { passive: true });
        });


        /* ============================================
           CERRAR OFFCANVAS AL TOCAR ENLACE
        ============================================ */
        document.querySelectorAll('[data-store-close-offcanvas="true"]').forEach((link) => {
            link.addEventListener('click', () => {
                const offcanvasEl = document.getElementById('mobileStoreMenu');
                if (!offcanvasEl) return;

                const instance = bootstrap.Offcanvas.getInstance(offcanvasEl);

                if (instance) {
                    instance.hide();
                }
            });
        });


        /* ============================================
           CERRAR OFFCANVAS SI PASA A DESKTOP
        ============================================ */
        window.addEventListener('resize', () => {
            if (window.innerWidth > 991) {
                const offcanvasEl = document.getElementById('mobileStoreMenu');
                if (!offcanvasEl) return;

                const instance = bootstrap.Offcanvas.getInstance(offcanvasEl);

                if (instance) {
                    instance.hide();
                }
            }
        });

    });
</script>