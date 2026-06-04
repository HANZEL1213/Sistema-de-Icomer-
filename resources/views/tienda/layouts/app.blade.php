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

    {{-- WHATSAPP FLOTANTE PREMIUM --}}
    @php
        $numeroWhatsapp = preg_replace(
            '/[^0-9]/',
            '',
            $configTienda['tienda_whatsapp'] ?? '87790346'
        );

        $mensajeWhatsapp = urlencode(
            ''
        );

        $whatsappLink = "https://wa.me/506{$numeroWhatsapp}?text={$mensajeWhatsapp}";
    @endphp

    <div class="whatsapp-premium-container">
        <a href="{{ $whatsappLink }}"
            class="store-whatsapp-float"
            target="_blank"
            rel="noopener noreferrer"
            aria-label="Contactar por WhatsApp">

            <span class="whatsapp-pulse-ring"></span>

            <i class="bi bi-whatsapp"></i>

            <span class="whatsapp-tooltip whatsapp-pixel-tooltip">
                ¿Necesitas ayuda?
                <span class="whatsapp-pixel-cursor"></span>
            </span>

        </a>
    </div>

    @include('tienda.layouts.scripts')

    @stack('scripts')

</body>

</html>