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

        @php
            $categoriasHome = collect([
                (object)[
                    'nombre' => 'Tecnología',
                    'productos_count' => 24,
                    'imagen' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?q=80&w=1200&auto=format&fit=crop',
                    'icono' => 'bi-cpu',
                ],

                (object)[
                    'nombre' => 'Moda',
                    'productos_count' => 18,
                    'imagen' => 'https://images.unsplash.com/photo-1483985988355-763728e1935b?q=80&w=1200&auto=format&fit=crop',
                    'icono' => 'bi-bag',
                ],

                (object)[
                    'nombre' => 'Gaming',
                    'productos_count' => 15,
                    'imagen' => 'https://images.unsplash.com/photo-1542751371-adc38448a05e?q=80&w=1200&auto=format&fit=crop',
                    'icono' => 'bi-controller',
                ],

                (object)[
                    'nombre' => 'Tenis',
                    'productos_count' => 32,
                    'imagen' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?q=80&w=1200&auto=format&fit=crop',
                    'icono' => 'bi-lightning-charge',
                ],
            ]);
        @endphp

        <div class="d-flex align-items-end justify-content-between flex-wrap gap-3 mb-4 mb-lg-5">

            <div>
                <div class="store-mini-label">
                    Exploración
                </div>

                <h2 class="store-section-title mb-2">
                    Categorías destacadas
                </h2>

                <p class="store-section-subtitle">
                    Explora rápidamente las categorías principales de la tienda
                    con una presentación más moderna y consistente.
                </p>
            </div>

            <a href="{{ route('tienda.categorias.index') }}"
               class="btn btn-store-outline px-4">
                Ver todas
            </a>

        </div>

        <div class="row g-3 g-md-4">

            @foreach($categoriasHome as $index => $categoria)

                <div class="col-6 col-lg-3">

                    <a href="{{ route('tienda.categorias.show', 'categoria-demo-' . ($index + 1)) }}"
                       class="store-category-card">

                        <div class="store-category-image-wrap">

                            <img src="{{ $categoria->imagen }}"
                                 alt="{{ $categoria->nombre }}"
                                 class="store-category-image">

                            <div class="store-category-overlay"></div>

                            <div class="store-category-icon">
                                <i class="bi {{ $categoria->icono }}"></i>
                            </div>

                            <span class="store-category-badge">
                                {{ $categoria->productos_count }} productos
                            </span>

                        </div>

                        <div class="store-category-body">

                            <div>
                                <h3 class="store-category-title">
                                    {{ $categoria->nombre }}
                                </h3>

                                <p class="store-category-text mb-0">
                                    Explorar productos y descubrir nuevas opciones.
                                </p>
                            </div>

                            <div class="store-category-footer mt-3">

                                <span>
                                    Explorar categoría
                                </span>

                                <i class="bi bi-arrow-right"></i>

                            </div>

                        </div>

                    </a>

                </div>

            @endforeach

        </div>

    </div>
</section>

{{-- =========================================================
    PRODUCTOS DESTACADOS
========================================================== --}}
<section class="store-section store-products-minimal-section">
    <div class="container">

        @php
            $productosHome = collect([
                (object)[
                    'nombre' => 'Nike Air Max Urban',
                    'precio' => 45990,
                    'stock' => 12,
                    'destacado' => true,
                    'marca' => (object)['nombre' => 'Nike'],
                    'categoriaPrincipal' => (object)['nombre' => 'Tenis'],
                    'imagen' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?q=80&w=1200&auto=format&fit=crop',
                ],

                (object)[
                    'nombre' => 'Audífonos Bluetooth Pro',
                    'precio' => 32990,
                    'stock' => 4,
                    'destacado' => false,
                    'marca' => (object)['nombre' => 'Sony'],
                    'categoriaPrincipal' => (object)['nombre' => 'Tecnología'],
                    'imagen' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?q=80&w=1200&auto=format&fit=crop',
                ],

                (object)[
                    'nombre' => 'Smart Watch Active',
                    'precio' => 68990,
                    'stock' => 7,
                    'destacado' => true,
                    'marca' => (object)['nombre' => 'Samsung'],
                    'categoriaPrincipal' => (object)['nombre' => 'Wearables'],
                    'imagen' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?q=80&w=1200&auto=format&fit=crop',
                ],

                (object)[
                    'nombre' => 'Mouse Gamer RGB',
                    'precio' => 18990,
                    'stock' => 0,
                    'destacado' => false,
                    'marca' => (object)['nombre' => 'Logitech'],
                    'categoriaPrincipal' => (object)['nombre' => 'Gaming'],
                    'imagen' => 'https://images.unsplash.com/photo-1527814050087-3793815479db?q=80&w=1200&auto=format&fit=crop',
                ],

                (object)[
                    'nombre' => 'Chaqueta Minimal Premium',
                    'precio' => 52990,
                    'stock' => 15,
                    'destacado' => true,
                    'marca' => (object)['nombre' => 'Zara'],
                    'categoriaPrincipal' => (object)['nombre' => 'Moda'],
                    'imagen' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?q=80&w=1200&auto=format&fit=crop',
                ],

                (object)[
                    'nombre' => 'Laptop Ultra Slim',
                    'precio' => 489990,
                    'stock' => 2,
                    'destacado' => false,
                    'marca' => (object)['nombre' => 'HP'],
                    'categoriaPrincipal' => (object)['nombre' => 'Computadoras'],
                    'imagen' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?q=80&w=1200&auto=format&fit=crop',
                ],

                (object)[
                    'nombre' => 'Consola Gamer Elite',
                    'precio' => 289990,
                    'stock' => 6,
                    'destacado' => true,
                    'marca' => (object)['nombre' => 'PlayStation'],
                    'categoriaPrincipal' => (object)['nombre' => 'Gaming'],
                    'imagen' => 'https://images.unsplash.com/photo-1606144042614-b2417e99c4e3?q=80&w=1200&auto=format&fit=crop',
                ],

                (object)[
                    'nombre' => 'Cámara Profesional X',
                    'precio' => 359990,
                    'stock' => 3,
                    'destacado' => false,
                    'marca' => (object)['nombre' => 'Canon'],
                    'categoriaPrincipal' => (object)['nombre' => 'Fotografía'],
                    'imagen' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?q=80&w=1200&auto=format&fit=crop',
                ],
            ]);
        @endphp

        <div class="d-flex align-items-end justify-content-between flex-wrap gap-3 mb-4 mb-lg-5">

            <div>
                <div class="store-mini-label">
                    Selección
                </div>

                <h2 class="store-section-title mb-2">
                    Productos destacados
                </h2>

                <p class="store-section-subtitle">
                    Una presentación moderna, limpia y visualmente consistente
                    con el catálogo principal.
                </p>
            </div>

            <a href="{{ route('tienda.productos.index') }}"
               class="btn btn-store-outline px-4">
                Ver catálogo
            </a>

        </div>

        <div class="row g-3 g-md-4">

            @foreach($productosHome as $producto)

                <div class="col-6 col-md-4 col-xl-3">

                    <div class="store-product-card">

                        {{-- IMAGEN --}}
                        <a href="#"
                           class="store-product-image-wrap">

                            <img src="{{ $producto->imagen }}"
                                 alt="{{ $producto->nombre }}"
                                 class="store-product-image">

                            <button type="button"
                                    class="store-product-heart">

                                <i class="bi bi-heart"></i>

                            </button>

                            @if($producto->stock <= 0)

                                <span class="store-product-badge store-product-badge-muted">
                                    Agotado
                                </span>

                            @elseif($producto->destacado)

                                <span class="store-product-badge">
                                    Destacado
                                </span>

                            @endif

                        </a>

                        {{-- INFO --}}
                        <div class="store-product-body">

                            <div class="store-product-meta">
                                {{ $producto->marca->nombre }}
                            </div>

                            <a href="#"
                               class="store-product-name">

                                {{ $producto->nombre }}

                            </a>

                            <div class="store-product-category">
                                {{ $producto->categoriaPrincipal->nombre }}
                            </div>

                            <div class="store-product-footer">

                                <div>

                                    <div class="store-product-price">
                                        ₡{{ number_format($producto->precio, 2) }}
                                    </div>

                                    <small class="store-product-stock">
                                        Stock: {{ $producto->stock }}
                                    </small>

                                </div>

                                <a href="#"
                                   class="store-product-action">

                                    <i class="bi bi-eye"></i>

                                </a>

                            </div>

                        </div>

                    </div>

                </div>

            @endforeach

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
    MARCAS DESTACADAS
========================================================== --}}
<section class="store-section" style="background: linear-gradient(to bottom, #f8fafc 0%, #f3f4f6 100%);">
    <div class="container">

        @php
            $marcasHome = collect([
                (object)[
                    'nombre' => 'Nike',
                    'descripcion' => 'Moda urbana y deportiva premium.',
                    'productos_count' => 32,
                    'logo' => 'N',
                    'imagen' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?q=80&w=1200&auto=format&fit=crop',
                ],

                (object)[
                    'nombre' => 'Sony',
                    'descripcion' => 'Tecnología y experiencia de audio.',
                    'productos_count' => 18,
                    'logo' => 'S',
                    'imagen' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?q=80&w=1200&auto=format&fit=crop',
                ],

                (object)[
                    'nombre' => 'Samsung',
                    'descripcion' => 'Dispositivos inteligentes y premium.',
                    'productos_count' => 21,
                    'logo' => 'SA',
                    'imagen' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?q=80&w=1200&auto=format&fit=crop',
                ],

                (object)[
                    'nombre' => 'Logitech',
                    'descripcion' => 'Gaming y accesorios modernos.',
                    'productos_count' => 14,
                    'logo' => 'L',
                    'imagen' => 'https://images.unsplash.com/photo-1527814050087-3793815479db?q=80&w=1200&auto=format&fit=crop',
                ],
            ]);
        @endphp

        <div class="d-flex align-items-end justify-content-between flex-wrap gap-3 mb-4 mb-lg-5">

            <div>
                <div class="store-mini-label">
                    Alianzas
                </div>

                <h2 class="store-section-title mb-2">
                    Marcas destacadas
                </h2>

                <p class="store-section-subtitle">
                    Una presentación más moderna y visualmente consistente
                    para las principales marcas de la tienda.
                </p>
            </div>

            <a href="{{ route('tienda.marcas.index') }}"
               class="btn btn-store-outline px-4">
                Ver marcas
            </a>

        </div>

        <div class="row g-3 g-md-4">

            @foreach($marcasHome as $index => $marca)

                <div class="col-6 col-lg-3">

                    <a href="{{ route('tienda.marcas.show', 'marca-demo-' . ($index + 1)) }}"
                       class="store-brand-card">

                        <div class="store-brand-image-wrap">

                            <img src="{{ $marca->imagen }}"
                                 alt="{{ $marca->nombre }}"
                                 class="store-brand-image">

                            <div class="store-brand-overlay"></div>

                            <div class="store-brand-logo">
                                {{ $marca->logo }}
                            </div>

                            <span class="store-brand-badge">
                                {{ $marca->productos_count }} productos
                            </span>

                        </div>

                        <div class="store-brand-body">

                            <div>

                                <h3 class="store-brand-title">
                                    {{ $marca->nombre }}
                                </h3>

                                <p class="store-brand-text mb-0">
                                    {{ $marca->descripcion }}
                                </p>

                            </div>

                            <div class="store-brand-footer mt-3">

                                <span>
                                    Ver marca
                                </span>

                                <i class="bi bi-arrow-right"></i>

                            </div>

                        </div>

                    </a>

                </div>

            @endforeach

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