@extends('tienda.layouts.app')

@section('title', 'Inicio | ' . ($configTienda['tienda_nombre'] ?? 'Mi Tienda'))

@section('content')

    @php
        $placeholder = asset('assets/img/no-image.png');
    @endphp

    {{-- =========================================================
        HERO PRINCIPAL DINÁMICO
    ========================================================== --}}
    <section class="store-section pt-3 pt-lg-4">
        <div class="container-fluid px-0">

            @if ($carruselItems->isNotEmpty())

                <div id="storeHeroCarousel" class="carousel slide store-hero-carousel" data-bs-ride="carousel">

                    @if ($carruselItems->count() > 1)
                        <div class="carousel-indicators store-hero-indicators">
                            @foreach ($carruselItems as $index => $item)
                                <button type="button" data-bs-target="#storeHeroCarousel"
                                    data-bs-slide-to="{{ $index }}" class="{{ $index === 0 ? 'active' : '' }}"
                                    @if ($index === 0) aria-current="true" @endif
                                    aria-label="Slide {{ $index + 1 }}">
                                </button>
                            @endforeach
                        </div>
                    @endif

                    <div class="carousel-inner">

                        @foreach ($carruselItems as $index => $item)
                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">

                                <div class="store-hero-slide"
                                    style="
                                    background:
                                    linear-gradient(90deg, rgba(17,24,39,0.88) 0%, rgba(17,24,39,0.58) 42%, rgba(17,24,39,0.22) 100%),
                                    url('{{ asset('storage/' . $item->ruta_imagen) }}') center/cover no-repeat;
                                ">

                                    <div class="container">
                                        <div class="store-hero-content">

                                            @if ($item->tipo_destino)
                                                <span class="store-badge-soft mb-3">
                                                    <i class="bi bi-stars"></i>
                                                    {{ ucfirst($item->tipo_destino) }}
                                                </span>
                                            @endif

                                            <h1 class="store-hero-title text-white">
                                                {{ $item->titulo ?: 'Descubrí nuestras novedades' }}
                                            </h1>

                                            <p class="store-hero-text text-white-50">
                                                {{ $item->subtitulo ?: 'Explorá productos, categorías y marcas disponibles en nuestra tienda.' }}
                                            </p>

                                            <div class="d-flex flex-wrap gap-3">
                                                <a href="{{ $item->destino_url ?? route('tienda.productos.index') }}"
                                                    class="btn btn-store-primary px-4">
                                                    {{ $item->texto_boton ?: 'Ver productos' }}
                                                </a>
                                            </div>

                                        </div>
                                    </div>

                                </div>

                            </div>
                        @endforeach

                    </div>

                    @if ($carruselItems->count() > 1)
                        <button class="carousel-control-prev store-hero-control" type="button"
                            data-bs-target="#storeHeroCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                            <span class="visually-hidden">Anterior</span>
                        </button>

                        <button class="carousel-control-next store-hero-control" type="button"
                            data-bs-target="#storeHeroCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                            <span class="visually-hidden">Siguiente</span>
                        </button>
                    @endif

                </div>
            @else
                <div class="store-hero-slide"
                    style="
                    background:
                    linear-gradient(90deg, rgba(17,24,39,0.90) 0%, rgba(17,24,39,0.70) 50%, rgba(17,24,39,0.45) 100%);
                ">

                    <div class="container">
                        <div class="store-hero-content">

                            <span class="store-badge-soft mb-3">
                                <i class="bi bi-stars"></i>
                                Tienda
                            </span>

                            <h1 class="store-hero-title text-white">
                                Bienvenido a nuestra tienda
                            </h1>

                            <p class="store-hero-text text-white-50">
                                Explorá productos, categorías y marcas disponibles.
                            </p>

                            <div class="d-flex flex-wrap gap-3">
                                <a href="{{ route('tienda.productos.index') }}" class="btn btn-store-primary px-4">
                                    Ver productos
                                </a>
                            </div>

                        </div>
                    </div>

                </div>

            @endif

        </div>
    </section>
    {{-- =========================================================
        CATEGORÍAS DESTACADAS DINÁMICAS
    ========================================================== --}}
    @if ($categoriasHome->isNotEmpty())

        <section class="store-section">
            <div class="container">

                <div class="d-flex align-items-end justify-content-between flex-wrap gap-3 mb-4 mb-lg-5">

                    <div>
                        <div class="store-mini-label">
                            Exploración
                        </div>

                        <h2 class="store-section-title mb-2">
                            Categorías destacadas
                        </h2>

                        <p class="store-section-subtitle">
                            Explora rápidamente las categorías principales de la tienda.
                        </p>
                    </div>

                    <a href="{{ route('tienda.categorias.index') }}" class="btn btn-store-outline px-4">
                        Ver todas
                    </a>

                </div>

                <div class="row g-3 g-md-4">

                    @foreach ($categoriasHome as $categoria)
                        @php
                            $categoriaImagen = $categoria->imagen
                                ? asset('storage/' . $categoria->imagen)
                                : $placeholder;
                        @endphp

                        <div class="col-6 col-lg-3">

                            <a href="{{ route('tienda.categorias.show', $categoria->slug) }}" class="store-category-card">

                                <div class="store-category-image-wrap">

                                    <img src="{{ $categoriaImagen }}" alt="{{ $categoria->nombre }}"
                                        class="store-category-image">

                                    <div class="store-category-overlay"></div>

                                    <div class="store-category-icon">
                                        <i class="bi bi-grid"></i>
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
                                            {{ $categoria->descripcion ?: 'Explorar productos y descubrir nuevas opciones.' }}
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

    @endif

    {{-- =========================================================
        PRODUCTOS DESTACADOS DINÁMICOS
    ========================================================== --}}
    @if ($productosHome->isNotEmpty())

        <section class="store-section store-products-minimal-section">
            <div class="container">

                <div class="d-flex align-items-end justify-content-between flex-wrap gap-3 mb-4 mb-lg-5">

                    <div>
                        <div class="store-mini-label">
                            Selección
                        </div>

                        <h2 class="store-section-title mb-2">
                            Productos destacados
                        </h2>

                        <p class="store-section-subtitle">
                            Productos disponibles en el catálogo principal.
                        </p>
                    </div>

                    <a href="{{ route('tienda.productos.index') }}" class="btn btn-store-outline px-4">
                        Ver catálogo
                    </a>

                </div>

                <div class="row g-3 g-md-4">

                    @foreach ($productosHome as $producto)
                        @php
                            $productoImagen = $producto->imagenPrincipal?->ruta
                                ? asset('storage/' . $producto->imagenPrincipal->ruta)
                                : $placeholder;
                        @endphp

                        <div class="col-6 col-md-4 col-xl-3">

                            <div class="store-product-card">

                                {{-- IMAGEN --}}
                                <a href="{{ route('tienda.productos.show', $producto->slug) }}"
                                    class="store-product-image-wrap">

                                    <img src="{{ $productoImagen }}" alt="{{ $producto->nombre }}"
                                        class="store-product-image">

                                    <button type="button"
                                        class="store-product-heart js-favorite-btn {{ in_array($producto->id_producto, $favoritosIds ?? []) ? 'is-active' : '' }}"
                                        data-url="{{ route('tienda.favoritos.toggle', $producto->id_producto) }}">

                                        <i
                                            class="bi {{ in_array($producto->id_producto, $favoritosIds ?? []) ? 'bi-heart-fill' : 'bi-heart' }}"></i>

                                    </button>

                                    @if ($producto->stock_actual <= 0)
                                        <span class="store-product-badge store-product-badge-muted">
                                            Agotado
                                        </span>
                                    @else
                                    @endif

                                </a>

                                {{-- INFO --}}
                                <div class="store-product-body">

                                    <div class="store-product-meta">
                                        {{ $producto->marca?->nombre ?? 'Sin marca' }}
                                    </div>

                                    <a href="{{ route('tienda.productos.show', $producto->slug) }}"
                                        class="store-product-name">

                                        {{ $producto->nombre }}

                                    </a>

                                    <div class="store-product-category">
                                        {{ $producto->categoriaPrincipal?->nombre ?? 'Sin categoría' }}
                                    </div>

                                    <div class="store-product-footer">

                                        <div>
                                            <div class="store-product-price">
                                                ₡{{ number_format($producto->precio, 2) }}
                                            </div>

                                            <small class="store-product-stock">
                                                Stock: {{ $producto->stock_actual }}
                                            </small>
                                        </div>

                                        <a href="{{ route('tienda.productos.show', $producto->slug) }}"
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

    @endif

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

                    @php
                        $bannerProducto = $productosHome->first();

                        $bannerImagen = $bannerProducto?->imagenPrincipal?->ruta
                            ? asset('storage/' . $bannerProducto->imagenPrincipal->ruta)
                            : $placeholder;

                        $bannerUrl = $bannerProducto
                            ? route('tienda.productos.show', $bannerProducto->slug)
                            : route('tienda.productos.index');
                    @endphp

                    <div class="col-lg-7">

                        <div class="p-4 p-lg-5 text-white">

                            <span class="store-badge-soft mb-3">
                                <i class="bi bi-lightning-charge"></i>

                                {{ $configTienda['home_banner_badge'] ?? 'Compra fácil' }}
                            </span>

                            <h2 class="fw-bold mb-3 text-white"
                                style="font-size: clamp(1.7rem, 3vw, 2.7rem); line-height: 1.1;">

                                {{ $configTienda['home_banner_titulo'] ?? 'Encontrá productos, marcas y categorías en un solo lugar' }}

                            </h2>

                            <p class="text-white-50 mb-4" style="line-height: 1.8; max-width: 560px;">

                                {{ $configTienda['home_banner_texto'] ??
                                    'Navegá el catálogo, agregá productos al carrito y finalizá tu pedido de forma sencilla.' }}

                            </p>

                            <div class="d-flex flex-wrap gap-3">

                                <a href="{{ $bannerUrl }}" class="btn btn-store-primary px-4">

                                    {{ $configTienda['home_banner_boton_1'] ?? 'Ver producto' }}

                                </a>

                            </div>

                        </div>

                    </div>

                    <div class="col-lg-5">

                        <a href="{{ $bannerUrl }}" class="d-block text-decoration-none">

                            <div
                                style="
                    min-height: 360px;
                    background:
                    linear-gradient(to top, rgba(17,24,39,0.12), rgba(17,24,39,0)),
                    url('{{ $bannerImagen }}') center/cover no-repeat;
                ">
                            </div>

                        </a>

                    </div>

                </div>

            </div>
        </div>
    </section>

    {{-- =========================================================
        MARCAS DESTACADAS DINÁMICAS
    ========================================================== --}}
    @if ($marcasHome->isNotEmpty())

        <section class="store-section" style="background: linear-gradient(to bottom, #f8fafc 0%, #f3f4f6 100%);">
            <div class="container">

                <div class="d-flex align-items-end justify-content-between flex-wrap gap-3 mb-4 mb-lg-5">

                    <div>
                        <div class="store-mini-label">
                            Alianzas
                        </div>

                        <h2 class="store-section-title mb-2">
                            Marcas destacadas
                        </h2>

                        <p class="store-section-subtitle">
                            Conocé las principales marcas disponibles en la tienda.
                        </p>
                    </div>

                    <a href="{{ route('tienda.marcas.index') }}" class="btn btn-store-outline px-4">
                        Ver marcas
                    </a>

                </div>

                <div class="row g-3 g-md-4">

                    @foreach ($marcasHome as $marca)
                        @php
                            $marcaImagen = $marca->imagen ? asset('storage/' . $marca->imagen) : $placeholder;

                            $marcaLogo = strtoupper(mb_substr($marca->nombre, 0, 2));
                        @endphp

                        <div class="col-6 col-lg-3">

                            <a href="{{ route('tienda.marcas.show', $marca->slug) }}" class="store-brand-card">

                                <div class="store-brand-image-wrap">

                                    <img src="{{ $marcaImagen }}" alt="{{ $marca->nombre }}"
                                        class="store-brand-image">

                                    <div class="store-brand-overlay"></div>

                                    <div class="store-brand-logo">
                                        {{ $marcaLogo }}
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
                                            Explorar productos de esta marca.
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

    @endif

    {{-- =========================================================
        BENEFICIOS / CONFIANZA
    ========================================================== --}}
    <section class="store-section">
        <div class="container">

            <div class="text-center mb-4 mb-lg-5">
                <div class="store-mini-label justify-content-center">
                    Confianza
                </div>

                <h2 class="store-section-title mb-2">
                    ¿Por qué comprar con nosotros?
                </h2>

                <p class="store-section-subtitle mx-auto" style="max-width: 720px;">
                    Compra de forma cómoda, segura y con seguimiento de tus pedidos.
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

                            <h5 class="fw-bold mb-2">
                                Envíos disponibles
                            </h5>

                            <p class="text-muted mb-0" style="line-height: 1.7;">
                                Entregas planificadas según las zonas disponibles.
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

                            <h5 class="fw-bold mb-2">
                                Compra segura
                            </h5>

                            <p class="text-muted mb-0" style="line-height: 1.7;">
                                Seguimiento de pedidos y pagos de forma controlada.
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

                            <h5 class="fw-bold mb-2">
                                Catálogo organizado
                            </h5>

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
