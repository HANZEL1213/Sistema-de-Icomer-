<!DOCTYPE html>
<html lang="es">

<head>
      @include('tienda.layouts.head')
</head>

<body>
    <div class="store-page-shell">
        @include('tienda.layouts.header')

        <main class="store-main store-mobile-safe-bottom">
            @yield('content')
        </main>

        @include('tienda.layouts.footer')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.querySelectorAll('input, select, textarea').forEach((el) => {
            el.addEventListener('focus', () => {
                el.style.fontSize = '16px';
            });
        });

        document.querySelectorAll('.store-card, .btn-store-primary, .btn-store-outline, .store-icon-btn').forEach((el) => {
            el.addEventListener('touchstart', () => {
                if (window.navigator && window.navigator.vibrate) {
                    window.navigator.vibrate(15);
                }
            }, { passive: true });
        });

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
    </script>

    @stack('scripts')
</body>

</html>