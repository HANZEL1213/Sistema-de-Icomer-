<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}?v=5.3.8"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
{{-- SWEETALERT --}}
<script src="{{ asset('assets/js/sweetalert2.all.min.js') }}"></script>

<script src="{{ asset('assets/js/tienda.js') }}"></script>

{{-- google maps --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>

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


    @if (session('swal_success'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'success',
                title: '¡Listo!',
                text: @json(session('swal_success')),
                confirmButtonColor: '#28a745',
                confirmButtonText: 'Aceptar',
                showConfirmButton: true
            });
        });
    </script>
@endif

{{-- @if (session('swal_error'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: @json(session('swal_error')),
                confirmButtonColor: '#dc3545'
            });
        });
    </script>
@endif --}}
