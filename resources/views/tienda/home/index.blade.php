@extends('tienda.layouts.app')

@section('title', 'Inicio | Mi Tienda')

@section('content')

    @php
        $imagenesProductos = [
            'https://images.unsplash.com/photo-1600180758890-6b94519a8ba8?auto=format&fit=crop&w=900&q=80',
            'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=900&q=80',
            'https://images.unsplash.com/photo-1549298916-b41d501d3772?auto=format&fit=crop&w=900&q=80',
            'https://images.unsplash.com/photo-1606813909353-66b6b7c6b4f3?auto=format&fit=crop&w=900&q=80',
            'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?auto=format&fit=crop&w=900&q=80',
            'https://images.unsplash.com/photo-1584735175315-9d5df23be620?auto=format&fit=crop&w=900&q=80',
            'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=900&q=80',
            'https://images.unsplash.com/photo-1608231387042-66d1773070a5?auto=format&fit=crop&w=900&q=80',
        ];
    @endphp

    {{-- =========================================================
        HERO PRINCIPAL PREMIUM
    ========================================================== --}}
    <section class="store-section pt-3 pt-lg-4">
        <div class="container-fluid px-0">
            <div id="storeHeroCarousel" class="carousel slide store-hero-carousel" data-bs-ride="carousel">
                <div class="carousel-indicators store-hero-indicators">
                    <button type="button" data-bs-target="#storeHeroCarousel" data-bs-slide-to="0" class="active"
                        aria-current="true" aria-label="Slide 1"></button>
                    <button type="button" data-bs-target="#storeHeroCarousel" data-bs-slide-to="1"
                        aria-label="Slide 2"></button>
                    <button type="button" data-bs-target="#storeHeroCarousel" data-bs-slide-to="2"
                        aria-label="Slide 3"></button>
                </div>

                <div class="carousel-inner">

                    <div class="carousel-item active">
                        <div class="store-hero-slide"
                            style="
                                background:
                                linear-gradient(90deg, rgba(17,24,39,0.88) 0%, rgba(17,24,39,0.58) 42%, rgba(17,24,39,0.22) 100%),
                                url('https://images.unsplash.com/photo-1523381210434-271e8be1f52b?auto=format&fit=crop&w=1800&q=80') center/cover no-repeat;
                            ">
                            <div class="container">
                                <div class="store-hero-content">
                                    <span class="store-badge-soft mb-3">
                                        <i class="bi bi-stars"></i>
                                        Nueva colección
                                    </span>

                                    <h1 class="store-hero-title text-white">
                                        Diseñá una experiencia de compra moderna y elegante
                                    </h1>

                                    <p class="store-hero-text text-white-50">
                                        Productos, categorías y marcas en una tienda visualmente potente,
                                        optimizada especialmente para móviles.
                                    </p>

                                    <div class="d-flex flex-wrap gap-3">
                                        <a href="{{ route('tienda.productos.index') }}" class="btn btn-store-primary px-4">
                                            Ver productos
                                        </a>

                                        <a href="{{ route('tienda.categorias.index') }}"
                                            class="btn btn-light fw-semibold rounded-4 px-4">
                                            Explorar categorías
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="carousel-item">
                        <div class="store-hero-slide"
                            style="
                                background:
                                linear-gradient(90deg, rgba(17,24,39,0.88) 0%, rgba(17,24,39,0.58) 42%, rgba(17,24,39,0.22) 100%),
                                url('https://images.unsplash.com/photo-1441984904996-e0b6ba687e04?auto=format&fit=crop&w=1800&q=80') center/cover no-repeat;
                            ">
                            <div class="container">
                                <div class="store-hero-content">
                                    <span class="store-badge-soft mb-3">
                                        <i class="bi bi-lightning-charge"></i>
                                        Tendencia
                                    </span>

                                    <h2 class="store-hero-title text-white">
                                        Descubrí colecciones con estilo y mejor presentación
                                    </h2>

                                    <p class="store-hero-text text-white-50">
                                        Una home más visual, clara y fuerte para que tu tienda se sienta de otro nivel.
                                    </p>

                                    <div class="d-flex flex-wrap gap-3">
                                        <a href="{{ route('tienda.marcas.index') }}" class="btn btn-store-primary px-4">
                                            Ver marcas
                                        </a>

                                        <a href="{{ route('tienda.productos.index') }}"
                                            class="btn btn-light fw-semibold rounded-4 px-4">
                                            Ir al catálogo
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="carousel-item">
                        <div class="store-hero-slide"
                            style="
                                background:
                                linear-gradient(90deg, rgba(17,24,39,0.88) 0%, rgba(17,24,39,0.58) 42%, rgba(17,24,39,0.22) 100%),
                                url('https://images.unsplash.com/photo-1483985988355-763728e1935b?auto=format&fit=crop&w=1800&q=80') center/cover no-repeat;
                            ">
                            <div class="container">
                                <div class="store-hero-content">
                                    <span class="store-badge-soft mb-3">
                                        <i class="bi bi-gem"></i>
                                        Premium
                                    </span>

                                    <h2 class="store-hero-title text-white">
                                        Llevá tu tienda a una imagen más limpia y profesional
                                    </h2>

                                    <p class="store-hero-text text-white-50">
                                        Un carrusel grande, elegante y protagonista siempre genera un inicio mucho más fuerte.
                                    </p>

                                    <div class="d-flex flex-wrap gap-3">
                                        <a href="{{ route('tienda.checkout.index') }}" class="btn btn-store-primary px-4">
                                            Comprar ahora
                                        </a>

                                        <a href="{{ route('tienda.pedidos.mis') }}"
                                            class="btn btn-light fw-semibold rounded-4 px-4">
                                            Mis pedidos
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <button class="carousel-control-prev store-hero-control" type="button" data-bs-target="#storeHeroCarousel"
                    data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                    <span class="visually-hidden">Anterior</span>
                </button>

                <button class="carousel-control-next store-hero-control" type="button" data-bs-target="#storeHeroCarousel"
                    data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                    <span class="visually-hidden">Siguiente</span>
                </button>
            </div>
        </div>
    </section>

    {{-- =========================================================
        CATEGORÍAS DESTACADAS
    ========================================================== --}}
    <section class="store-section">
        <div class="container">
            <div class="d-flex align-items-end justify-content-between flex-wrap gap-3 mb-4 mb-lg-5">
                <div>
                    <div class="store-mini-label">Exploración</div>
                    <h2 class="store-section-title mb-2">Categorías destacadas</h2>
                    <p class="store-section-subtitle">
                        Una entrada visual más moderna para guiar al usuario desde el inicio.
                    </p>
                </div>

                <a href="{{ route('tienda.categorias.index') }}" class="btn btn-store-outline px-4">
                    Ver todas
                </a>
            </div>

            <div class="row g-3 g-md-4">
                @for ($i = 1; $i <= 4; $i++)
                    <div class="col-6 col-lg-3">
                        <a href="{{ route('tienda.categorias.show', 'categoria-demo-' . $i) }}" class="text-dark d-block h-100">
                            <div class="store-card h-100 border-0 overflow-hidden"
                                style="border-radius: 24px; box-shadow: 0 14px 34px rgba(15, 23, 42, 0.06);">
                                <div class="position-relative"
                                    style="
                                        height: 260px;
                                        background:
                                        linear-gradient(to top, rgba(17,24,39,0.55), rgba(17,24,39,0.08)),
                                        url('https://images.unsplash.com/photo-1441986300917-64674bd600d8?auto=format&fit=crop&w=900&q=80') center/cover no-repeat;
                                    ">
                                    <div class="position-absolute bottom-0 start-0 w-100 p-3 p-md-4 text-white">
                                        <span class="badge rounded-pill text-bg-light text-dark px-3 py-2 mb-2">
                                            Colección
                                        </span>

                                        <h5 class="fw-bold mb-1 text-white">Categoría {{ $i }}</h5>
                                        <small class="text-white-50">Explorar productos</small>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endfor
            </div>
        </div>
    </section>

  {{-- =========================================================
    PRODUCTOS DESTACADOS
========================================================== --}}
<section class="store-section store-products-minimal-section">
    <div class="container">
        @php
            $imagenesProductos = [
                'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=900&q=80',
                'https://images.unsplash.com/photo-1549298916-b41d501d3772?auto=format&fit=crop&w=900&q=80',
                  'https://images.unsplash.com/photo-1549298916-b41d501d3772?auto=format&fit=crop&w=900&q=80',
                'https://images.unsplash.com/photo-1608231387042-66d1773070a5?auto=format&fit=crop&w=900&q=80',
                'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?auto=format&fit=crop&w=900&q=80',
                 'https://images.unsplash.com/photo-1549298916-b41d501d3772?auto=format&fit=crop&w=900&q=80',
                 'https://images.unsplash.com/photo-1549298916-b41d501d3772?auto=format&fit=crop&w=900&q=80',
                 'https://images.unsplash.com/photo-1549298916-b41d501d3772?auto=format&fit=crop&w=900&q=80',
            ];
        @endphp

        <div class="d-flex align-items-end justify-content-between flex-wrap gap-3 mb-4 mb-lg-5">
            <div>
                <div class="store-mini-label">Selección</div>
                <h2 class="store-section-title mb-2">Productos destacados</h2>
                <p class="store-section-subtitle">
                    Una presentación más limpia, moderna y visual para el catálogo.
                </p>
            </div>

            <a href="{{ route('tienda.productos.index') }}" class="btn btn-store-outline px-4">
                Ver catálogo
            </a>
        </div>

        <div class="row g-3 g-md-4">
            @for ($i = 1; $i <= 8; $i++)
                <div class="col-6 col-xl-3">
                    <article class="store-product-minimal h-100">
                        <a href="{{ route('tienda.productos.show', 'producto-demo-' . $i) }}"
                            class="store-product-minimal-image-wrap">

                            <div class="store-product-minimal-image"
                                style="
                                    background-image: url('{{ $imagenesProductos[$i - 1] }}');
                                ">
                            </div>

                            <button type="button" class="store-product-fav-btn" aria-label="Agregar a favoritos">
                                <i class="bi bi-heart"></i>
                            </button>

                            @if ($i == 2 || $i == 8)
                                <span class="store-product-discount-badge">-30%</span>
                            @endif
                        </a>

                        <div class="store-product-minimal-body">
                            <a href="{{ route('tienda.productos.show', 'producto-demo-' . $i) }}"
                                class="store-product-minimal-title">
                                Producto demo {{ $i }}
                            </a>

                            <div class="store-product-minimal-price-wrap">
                                <span class="store-product-minimal-price">
                                    ₡{{ number_format(12500 * $i, 2) }}
                                </span>

                                @if ($i == 2 || $i == 8)
                                    <span class="store-product-minimal-old-price">
                                        ₡{{ number_format((12500 * $i) + 9000, 2) }}
                                    </span>
                                @endif
                            </div>

                            <div class="store-product-minimal-meta">
                                Marca demo
                            </div>

                            <div class="store-product-minimal-submeta">
                                {{ $i % 2 == 0 ? 'Running' : 'Casual' }}
                            </div>
                        </div>
                    </article>
                </div>
            @endfor
        </div>
    </div>
</section>

    {{-- =========================================================
        BANNER INTERMEDIO PREMIUM
    ========================================================== --}}
    <section class="store-section">
        <div class="container">
            <div class="store-card border-0 overflow-hidden"
                style="
                    border-radius: 30px;
                    background: linear-gradient(135deg, #111827 0%, #1f2937 100%);
                    box-shadow: 0 26px 60px rgba(15, 23, 42, 0.16);
                ">
                <div class="row g-0 align-items-center">
                    <div class="col-lg-7">
                        <div class="p-4 p-lg-5 text-white">
                            <span class="store-badge-soft mb-3">
                                <i class="bi bi-lightning-charge"></i>
                                Promoción especial
                            </span>

                            <h2 class="fw-bold mb-3 text-white"
                                style="font-size: clamp(1.7rem, 3vw, 2.7rem); line-height: 1.1;">
                                Dale a tu tienda una home con más impacto desde el primer vistazo
                            </h2>

                            <p class="text-white-50 mb-4" style="line-height: 1.8; max-width: 560px;">
                                Este bloque luego puede transformarse en campañas reales, cupones activos,
                                promociones por categoría o banners del carrusel principal.
                            </p>

                            <div class="d-flex flex-wrap gap-3">
                                <a href="{{ route('tienda.productos.index') }}" class="btn btn-store-primary px-4">
                                    Comprar ahora
                                </a>

                                <a href="{{ route('tienda.marcas.index') }}"
                                    class="btn btn-light fw-semibold rounded-4 px-4">
                                    Ver marcas
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div
                            style="
                                min-height: 360px;
                                background:
                                linear-gradient(to top, rgba(17,24,39,0.12), rgba(17,24,39,0)),
                                url('https://images.unsplash.com/photo-1483985988355-763728e1935b?auto=format&fit=crop&w=1200&q=80') center/cover no-repeat;
                            ">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- =========================================================
        MARCAS
    ========================================================== --}}
    <section class="store-section" style="background: linear-gradient(to bottom, #f8fafc 0%, #f3f4f6 100%);">
        <div class="container">
            <div class="d-flex align-items-end justify-content-between flex-wrap gap-3 mb-4 mb-lg-5">
                <div>
                    <div class="store-mini-label">Alianzas</div>
                    <h2 class="store-section-title mb-2">Marcas</h2>
                    <p class="store-section-subtitle">
                        Una presentación más limpia para logos, marcas premium o aliados estratégicos.
                    </p>
                </div>

                <a href="{{ route('tienda.marcas.index') }}" class="btn btn-store-outline px-4">
                    Ver marcas
                </a>
            </div>

            <div class="row g-3 g-md-4">
                @for ($i = 1; $i <= 4; $i++)
                    <div class="col-6 col-lg-3">
                        <a href="{{ route('tienda.marcas.show', 'marca-demo-' . $i) }}" class="text-dark d-block h-100">
                            <div class="store-card h-100 border-0"
                                style="border-radius: 24px; box-shadow: 0 14px 34px rgba(15, 23, 42, 0.05);">
                                <div class="p-4 p-lg-4 text-center d-flex flex-column justify-content-center align-items-center"
                                    style="min-height: 200px;">
                                    <div class="d-flex align-items-center justify-content-center mb-3 rounded-circle"
                                        style="
                                            width: 82px;
                                            height: 82px;
                                            background: #f3f4f6;
                                            font-size: 1.5rem;
                                            font-weight: 800;
                                            color: #111827;
                                            box-shadow: inset 0 0 0 1px #e5e7eb;
                                        ">
                                        M{{ $i }}
                                    </div>

                                    <h6 class="fw-bold mb-1" style="font-size: 1rem;">Marca {{ $i }}</h6>
                                    <small class="text-muted">Ver productos</small>
                                </div>
                            </div>
                        </a>
                    </div>
                @endfor
            </div>
        </div>
    </section>

    {{-- =========================================================
        BENEFICIOS / CONFIANZA
    ========================================================== --}}
    <section class="store-section">
        <div class="container">
            <div class="text-center mb-4 mb-lg-5">
                <div class="store-mini-label justify-content-center">Confianza</div>
                <h2 class="store-section-title mb-2">¿Por qué comprar con nosotros?</h2>
                <p class="store-section-subtitle mx-auto" style="max-width: 720px;">
                    Bloques ideales para reforzar seguridad, experiencia de compra y orden visual dentro de la tienda.
                </p>
            </div>

            <div class="row g-3 g-lg-4">
                <div class="col-md-4">
                    <div class="store-card h-100 border-0"
                        style="border-radius: 24px; box-shadow: 0 16px 38px rgba(15, 23, 42, 0.05);">
                        <div class="p-4 p-lg-4 text-center">
                            <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                                style="width: 74px; height: 74px; background: #fff7e0; color: #dca116; font-size: 1.55rem;">
                                <i class="bi bi-truck"></i>
                            </div>

                            <h5 class="fw-bold mb-2">Envíos disponibles</h5>
                            <p class="text-muted mb-0" style="line-height: 1.7;">
                                Entregas planificadas para que el proceso de compra se sienta más cómodo y claro.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="store-card h-100 border-0"
                        style="border-radius: 24px; box-shadow: 0 16px 38px rgba(15, 23, 42, 0.05);">
                        <div class="p-4 p-lg-4 text-center">
                            <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                                style="width: 74px; height: 74px; background: #eef6ff; color: #2563eb; font-size: 1.55rem;">
                                <i class="bi bi-shield-lock"></i>
                            </div>

                            <h5 class="fw-bold mb-2">Compra segura</h5>
                            <p class="text-muted mb-0" style="line-height: 1.7;">
                                Integración ideal para seguimiento de pagos, validaciones y control de estados.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="store-card h-100 border-0"
                        style="border-radius: 24px; box-shadow: 0 16px 38px rgba(15, 23, 42, 0.05);">
                        <div class="p-4 p-lg-4 text-center">
                            <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                                style="width: 74px; height: 74px; background: #eefbf3; color: #16a34a; font-size: 1.55rem;">
                                <i class="bi bi-grid"></i>
                            </div>

                            <h5 class="fw-bold mb-2">Catálogo organizado</h5>
                            <p class="text-muted mb-0" style="line-height: 1.7;">
                                Productos, categorías y marcas bien estructurados para facilitar la exploración.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection