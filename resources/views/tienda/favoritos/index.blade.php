{{-- resources/views/tienda/favoritos/index.blade.php --}}

@extends('tienda.layouts.app')

@section('title', 'Mis Favoritos')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/modules/carrito.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/modules/favoritos.css') }}">
@endpush

@section('content')

    @php
        $placeholder = asset('assets/img/no-image.png');
        $carritoIds = collect(session('carrito', []))->keys()->toArray();
    @endphp

    <section class="store-section store-favorites-page pt-4 pt-lg-5">

        <div class="container">

            <div class="mb-4 mb-lg-5">

                <div class="store-mini-label">
                    Favoritos
                </div>

                <h1 class="store-section-title mb-2">
                    Mis productos favoritos
                </h1>

                <p class="store-section-subtitle">
                    Guarda productos y accede rápidamente a ellos cuando quieras.
                </p>

            </div>

            @if ($favoritos->isEmpty())

                <div class="store-card border-0 text-center">

                    <div class="p-4 p-lg-5">

                        <div class="mb-4">

                            <div class="mx-auto d-flex align-items-center justify-content-center"
                                style="
                                    width: 90px;
                                    height: 90px;
                                    border-radius: 50%;
                                    background: rgba(220,161,23,.10);
                                    color: var(--color-primary);
                                    font-size: 2rem;
                                ">

                                <i class="bi bi-heart"></i>

                            </div>

                        </div>

                        <h2 class="fw-bold mb-3">
                            No tienes favoritos guardados
                        </h2>

                        <p class="text-muted mb-4 mx-auto" style="max-width: 580px; line-height: 1.8;">
                            Explora el catálogo y agrega productos usando el ícono del corazón.
                        </p>

                        <a href="{{ route('tienda.productos.index') }}" class="btn btn-store-primary px-4">
                            Explorar productos
                        </a>

                    </div>

                </div>
            @else
                <div class="row g-3 g-md-4">

                    @foreach ($favoritos as $favorito)
                        @php
                            $producto = $favorito->producto;

                            if (!$producto) {
                                continue;
                            }

                            $imagen = $producto->imagenPrincipal?->ruta
                                ? asset('storage/' . $producto->imagenPrincipal->ruta)
                                : $placeholder;

                            $varianteBase = $producto->usa_variantes
                                ? $producto->variantePrincipal ?? $producto->variantesActivas->first()
                                : null;

                            if ($producto->usa_variantes && $varianteBase) {
                                $cartKey = $producto->id_producto . '_v_' . $varianteBase->id_producto_variante;

                                $productoEnCarrito = in_array($cartKey, $carritoIds ?? []);

                                $stock = (int) $varianteBase->stock_actual;

                                $tienePromo = $varianteBase->promocionVigente();

                                $precioNormal = (float) $varianteBase->precio;

                                $precioVenta = (float) $varianteBase->precioVenta();
                            } else {
                                $cartKey = (string) $producto->id_producto;

                                $productoEnCarrito = in_array($producto->id_producto, $carritoIds ?? []);

                                $stock = (int) $producto->stock_actual;

                                $tienePromo = $producto->tienePromocionActiva();

                                $precioNormal = (float) $producto->precio;

                                $precioVenta = (float) $producto->precioVenta();
                            }

                            $agotado = $stock <= 0;

                            $ahorro = $tienePromo ? max(0, $precioNormal - $precioVenta) : 0;

                            $porcentaje = $tienePromo && $precioNormal > 0 ? round(($ahorro / $precioNormal) * 100) : 0;
                        @endphp

                        <div class="col-6 col-md-4 col-xl-3">

                            <div class="store-product-card">

                                <a href="{{ route('tienda.productos.show', $producto->slug) }}"
                                    class="store-product-image-wrap">

                                    <img src="{{ $imagen }}" alt="{{ $producto->nombre }}"
                                        class="store-product-image">

                                    <button type="button" class="store-product-heart js-favorite-btn is-active"
                                        data-url="{{ route('tienda.favoritos.toggle', $producto->id_producto) }}">

                                        <i class="bi bi-heart-fill"></i>

                                    </button>

                                    @if ($agotado)
                                        <span class="store-product-badge store-product-badge-muted">
                                            Agotado
                                        </span>
                                    @endif

                                </a>

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

                                    <div class="store-product-footer store-product-footer-catalog">

                                        <div class="store-product-price-area">

                                            @if ($tienePromo)
                                                <div class="mb-1">
                                                    <span class="badge bg-danger text-white">
                                                        -{{ $porcentaje }}% OFF
                                                    </span>
                                                </div>

                                                <div class="text-muted text-decoration-line-through small">
                                                    ₡{{ number_format($precioNormal, 2) }}
                                                </div>

                                                <div class="store-product-price text-danger">
                                                    ₡{{ number_format($precioVenta, 2) }}
                                                </div>
                                            @else
                                                <div class="store-product-price">
                                                    ₡{{ number_format($precioVenta, 2) }}
                                                </div>
                                            @endif

                                            <small class="store-product-stock">
                                                Stock: {{ $stock }}
                                            </small>

                                        </div>

                                        <div class="store-product-card-actions">

                                            @if ($producto->usa_variantes)
                                                <button type="button"
                                                    class="store-product-action store-product-action-catalog js-add-cart {{ $productoEnCarrito ? 'is-added' : '' }}"
                                                    data-url="{{ route('tienda.carrito.agregar', $producto->id_producto) }}"
                                                    data-product-id="{{ $producto->id_producto }}"
                                                    data-product-variant-id="{{ $varianteBase?->id_producto_variante }}"
                                                    data-cart-key="{{ $cartKey }}"
                                                    title="{{ $productoEnCarrito ? 'Producto agregado' : 'Agregar al carrito' }}"
                                                    {{ $agotado || !$varianteBase || $productoEnCarrito ? 'disabled' : '' }}>

                                                    <i
                                                        class="bi {{ $productoEnCarrito ? 'bi-check-lg' : 'bi-cart-plus' }}"></i>

                                                </button>
                                            @else
                                                <button type="button"
                                                    class="store-product-action store-product-action-catalog js-add-cart {{ $productoEnCarrito ? 'is-added' : '' }}"
                                                    data-url="{{ route('tienda.carrito.agregar', $producto->id_producto) }}"
                                                    data-product-id="{{ $producto->id_producto }}"
                                                    data-cart-key="{{ $cartKey }}"
                                                    title="{{ $productoEnCarrito ? 'Producto agregado' : 'Agregar al carrito' }}"
                                                    {{ $agotado || $productoEnCarrito ? 'disabled' : '' }}>

                                                    <i
                                                        class="bi {{ $productoEnCarrito ? 'bi-check-lg' : 'bi-cart-plus' }}"></i>

                                                </button>
                                            @endif

                                            <a href="{{ route('tienda.productos.show', $producto->slug) }}"
                                                class="store-product-action store-product-action-catalog"
                                                title="Ver producto">

                                                <i class="bi bi-eye"></i>

                                            </a>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>
                    @endforeach

                </div>

            @endif

        </div>

    </section>

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/carrito.js') }}"></script>
@endpush
