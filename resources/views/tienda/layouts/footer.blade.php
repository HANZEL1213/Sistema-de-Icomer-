<footer class="store-footer mt-5">

    {{-- BLOQUE SUPERIOR / BENEFICIOS --}}
    <div class="store-footer-top border-top border-bottom">
        <div class="container">
            <div class="row g-3 py-3 py-md-4">

                <div class="col-6 col-lg-3">
                    <div class="store-feature-item">
                        <div class="store-feature-icon">
                            <i class="bi bi-truck"></i>
                        </div>

                        <div>
                            <div class="store-feature-title">
                                {{ $configTienda['footer_beneficio_1_titulo'] ?? 'Envíos disponibles' }}
                            </div>

                            <div class="store-feature-text">
                                {{ $configTienda['footer_beneficio_1_texto'] ?? 'Cobertura clara y rápida' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="store-feature-item">
                        <div class="store-feature-icon">
                            <i class="bi bi-shield-check"></i>
                        </div>

                        <div>
                            <div class="store-feature-title">
                                {{ $configTienda['footer_beneficio_2_titulo'] ?? 'Compra segura' }}
                            </div>

                            <div class="store-feature-text">
                                {{ $configTienda['footer_beneficio_2_texto'] ?? 'Protección en cada pedido' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="store-feature-item">
                        <div class="store-feature-icon">
                            <i class="bi bi-arrow-repeat"></i>
                        </div>

                        <div>
                            <div class="store-feature-title">
                                {{ $configTienda['footer_beneficio_3_titulo'] ?? 'Proceso ágil' }}
                            </div>

                            <div class="store-feature-text">
                                {{ $configTienda['footer_beneficio_3_texto'] ?? 'Experiencia simple y moderna' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="store-feature-item">
                        <div class="store-feature-icon">
                            <i class="bi bi-headset"></i>
                        </div>

                        <div>
                            <div class="store-feature-title">
                                {{ $configTienda['footer_beneficio_4_titulo'] ?? 'Atención directa' }}
                            </div>

                            <div class="store-feature-text">
                                {{ $configTienda['footer_beneficio_4_texto'] ?? 'Soporte cercano al cliente' }}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- CUERPO PRINCIPAL --}}
    <div class="store-footer-main">
        <div class="container py-5 py-lg-6">

            <div class="row g-4 g-lg-5 align-items-start">

                {{-- MARCA --}}
                <div class="col-12 col-lg-4">

                    <div class="store-footer-brand-card">

                        <div class="d-flex align-items-center gap-3 mb-3">

                            <div class="store-footer-logo">
                                {{ strtoupper(substr($configTienda['tienda_nombre'] ?? 'T', 0, 1)) }}
                            </div>

                            <div>
                                <div class="store-footer-brand-name">
                                    {{ $configTienda['tienda_nombre'] ?? 'Mi Tienda' }}
                                </div>

                                <div class="store-footer-brand-subtitle">
                                    {{ $configTienda['tienda_subtitulo'] ?? 'E-commerce premium' }}
                                </div>
                            </div>

                        </div>

                        <p class="store-footer-description mb-4">
                            {{ $configTienda['tienda_descripcion']
                                ?? 'Una tienda diseñada para ofrecer una experiencia elegante, clara y confiable.' }}
                        </p>

                        <div class="store-footer-socials">

                            <a href="{{ $configTienda['tienda_facebook'] ?? '#' }}"
                               class="store-social-btn"
                               aria-label="Facebook">

                                <i class="bi bi-facebook"></i>
                            </a>

                            <a href="{{ $configTienda['tienda_instagram'] ?? '#' }}"
                               class="store-social-btn"
                               aria-label="Instagram">

                                <i class="bi bi-instagram"></i>
                            </a>

                            <a href="{{ $configTienda['tienda_whatsapp_link'] ?? '#' }}"
                               class="store-social-btn"
                               aria-label="WhatsApp">

                                <i class="bi bi-whatsapp"></i>
                            </a>

                            <a href="{{ $configTienda['tienda_tiktok'] ?? '#' }}"
                               class="store-social-btn"
                               aria-label="TikTok">

                                <i class="bi bi-tiktok"></i>
                            </a>

                        </div>

                    </div>

                </div>

                {{-- NAVEGACIÓN --}}
                <div class="col-6 col-lg-2">

                    <div class="store-footer-block">

                        <h6 class="store-footer-title">
                            Tienda
                        </h6>

                        <ul class="store-footer-links list-unstyled mb-0">

                            <li>
                                <a href="{{ route('tienda.home') }}">
                                    Inicio
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('tienda.productos.index') }}">
                                    Productos
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('tienda.categorias.index') }}">
                                    Categorías
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('tienda.marcas.index') }}">
                                    Marcas
                                </a>
                            </li>

                        </ul>

                    </div>

                </div>

                {{-- CLIENTE --}}
                <div class="col-6 col-lg-3">

                    <div class="store-footer-block">

                        <h6 class="store-footer-title">
                            Cliente
                        </h6>

                        <ul class="store-footer-links list-unstyled mb-0">

                            <li>
                                <a href="{{ route('tienda.carrito.index') }}">
                                    Carrito
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('tienda.checkout.index') }}">
                                    Checkout
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('tienda.pedidos.mis') }}">
                                    Mis pedidos
                                </a>
                            </li>

                            {{-- <li>
                                <a href="{{ route('tienda.auth.login') }}">
                                    Mi cuenta
                                </a>
                            </li> --}}

                        </ul>

                    </div>

                </div>

                {{-- CONTACTO --}}
                <div class="col-12 col-lg-3">

                    <div class="store-footer-block store-footer-contact">

                        <h6 class="store-footer-title">
                            Contacto
                        </h6>

                        <ul class="store-footer-contact-list list-unstyled mb-0">

                            <li>
                                <span class="store-contact-icon">
                                    <i class="bi bi-geo-alt"></i>
                                </span>

                                <span>
                                    {{ $configTienda['tienda_pais'] ?? 'Costa Rica' }}
                                </span>
                            </li>

                            <li>
                                <span class="store-contact-icon">
                                    <i class="bi bi-telephone"></i>
                                </span>

                                <span>
                                    {{ $configTienda['tienda_telefono'] ?? '8888-8888' }}
                                </span>
                            </li>

                            <li>
                                <span class="store-contact-icon">
                                    <i class="bi bi-envelope"></i>
                                </span>

                                <span>
                                    {{ $configTienda['tienda_email'] ?? 'contacto@mitienda.com' }}
                                </span>
                            </li>

                            <li>
                                <span class="store-contact-icon">
                                    <i class="bi bi-whatsapp"></i>
                                </span>

                                <span>
                                    {{ $configTienda['tienda_whatsapp'] ?? '8888-8888' }}
                                </span>
                            </li>

                            <li>
                                <span class="store-contact-icon">
                                    <i class="bi bi-clock"></i>
                                </span>

                                <span>
                                    {{ $configTienda['tienda_horario'] ?? 'Lunes a sábado' }}
                                </span>
                            </li>

                        </ul>

                    </div>

                </div>

            </div>

            {{-- FRANJA EXTRA --}}
            <div class="store-footer-extra mt-4 mt-lg-5">

                <div class="row g-3 align-items-center">

                    <div class="col-12 col-lg-7">

                        <div class="store-footer-extra-text">
                            {{ $configTienda['footer_texto_extra']
                                ?? 'Navegación optimizada para móvil, diseño limpio y una experiencia de compra moderna.' }}
                        </div>

                    </div>

                    <div class="col-12 col-lg-5">

                        <div class="store-payment-badges">
                    
                            <span class="store-payment-badge">SINPE</span>
                            <span class="store-payment-badge">Transferencia</span>
                        </div>

                    </div>

                </div>

            </div>

        </div>
    </div>

    {{-- PARTE INFERIOR --}}
    <div class="store-footer-bottom">

        <div class="container">

            <div class="store-footer-bottom-wrap">

                <small class="mb-0">
                    © {{ now()->year }}
                    {{ $configTienda['tienda_nombre'] ?? 'Mi Tienda' }}.
                    {{ $configTienda['footer_copyright']
                        ?? 'Todos los derechos reservados.' }}
                </small>

                <small class="mb-0">
                    {{ $configTienda['footer_credito']
                        ?? 'Diseñado para una experiencia e-commerce premium.' }}
                </small>

            </div>

        </div>

    </div>

</footer>