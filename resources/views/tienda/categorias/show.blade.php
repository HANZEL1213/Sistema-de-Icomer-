{{-- resources/views/tienda/categorias/show.blade.php 

@extends('tienda.layouts.app')

@section('title', $categoria->nombre . ' | Categoría')
@section('meta_description', $categoria->descripcion)

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/modules/carrito.css') }}">

    @php
        $carritoIds = collect(session('carrito', []))->keys()->toArray();
    @endphp
    <section class="store-category-show-page">


        <div class="store-category-show-hero">

            <img src="{{ $categoria->imagen
                ? asset('storage/' . $categoria->imagen)
                : asset('assets/img/placeholder/category-banner.jpg') }}"
                alt="{{ $categoria->nombre }}" class="store-category-show-banner">

            <div class="store-category-show-overlay"></div>

            <div class="container position-relative">

                <div class="store-category-show-content">

            
                    <div class="store-detail-breadcrumb text-white mb-3">

                        <a href="{{ route('tienda.home') }}" class="text-white">
                            Inicio
                        </a>

                        <i class="bi bi-chevron-right"></i>

                        <a href="{{ route('tienda.categorias.index') }}" class="text-white">
                            Categorías
                        </a>

                        <i class="bi bi-chevron-right"></i>

                        <span>
                            {{ $categoria->nombre }}
                        </span>

                    </div>

                    <span class="store-section-eyebrow">
                        Categoría
                    </span>

                    <h1 class="store-category-show-title">
                        {{ $categoria->nombre }}
                    </h1>

                    <p class="store-category-show-description">
                        {{ $categoria->descripcion ?: 'Explora los productos disponibles en esta categoría.' }}
                    </p>

                    <div class="store-category-show-stats">

                        <div class="store-category-show-stat">
                            <strong>{{ $productos->count() }}</strong>
                            <span>Productos</span>
                        </div>

                        <div class="store-category-show-stat">
                            <strong>Online</strong>
                            <span>Disponibles</span>
                        </div>



                    </div>

                </div>

            </div>

        </div>


        <div class="container py-4 py-lg-5">

   
            <div class="store-products-toolbar">

                <div>

                    <h5 class="store-toolbar-title mb-1">
                        Productos de {{ $categoria->nombre }}
                    </h5>

                    <p class="store-toolbar-subtitle mb-0">
                        {{ $productos->count() }} productos encontrados
                    </p>

                </div>

                <a href="{{ route('tienda.productos.index') }}" class="btn btn-store-outline d-none d-md-inline-flex">
                    Ver catálogo
                </a>

            </div>
      
            <div class="row g-3 g-md-4">

                @forelse($productos as $producto)
                    @php
                        $productoEnCarrito = in_array($producto->id_producto, $carritoIds);

                        $varianteBase = $producto->usa_variantes
                            ? $producto->variantePrincipal ?? $producto->variantesActivas->first()
                            : null;

                        $precioBase = $producto->usa_variantes ? $varianteBase?->precio ?? 0 : $producto->precioVenta();

                        $stockBase = $producto->usa_variantes
                            ? $varianteBase?->stock_actual ?? 0
                            : $producto->stock_actual;

                        $agotado = $stockBase <= 0;

                        $tienePromo = !$producto->usa_variantes && $producto->tienePromocionActiva();

                        $precioNormal = (float) $precioBase;
                        $precioVenta = $precioBase;

                        $ahorro = $tienePromo ? max(0, $precioNormal - $precioVenta) : 0;

                        $porcentaje = $tienePromo && $precioNormal > 0 ? round(($ahorro / $precioNormal) * 100) : 0;
                    @endphp

                    <div class="col-6 col-md-4 col-xl-3">

                        <div class="store-product-card">

                            <a href="{{ route('tienda.productos.show', $producto->slug) }}"
                                class="store-product-image-wrap">

                                <img src="{{ $producto->imagenPrincipal
                                    ? asset('storage/' . $producto->imagenPrincipal->ruta)
                                    : asset('assets/img/placeholder/product.jpg') }}"
                                    alt="{{ $producto->nombre }}" class="store-product-image">

                                <button type="button"
                                    class="store-product-heart js-favorite-btn {{ in_array($producto->id_producto, $favoritosIds ?? []) ? 'is-active' : '' }}"
                                    data-url="{{ route('tienda.favoritos.toggle', $producto->id_producto) }}"
                                    aria-label="Agregar a favoritos">

                                    <i
                                        class="bi {{ in_array($producto->id_producto, $favoritosIds ?? []) ? 'bi-heart-fill' : 'bi-heart' }}"></i>

                                </button>

                                @if ($agotado)
                                    <span class="store-product-badge store-product-badge-muted">
                                        Agotado
                                    </span>
                                @elseif($producto->destacado)
                                    <span class="store-product-badge">
                                        Destacado
                                    </span>
                                @endif

                            </a>

                     
                            <div class="store-product-body">

                         
                                <div class="store-product-meta">
                                    {{ $producto->marca->nombre ?? 'Sin marca' }}
                                </div>

                        
                                <a href="{{ route('tienda.productos.show', $producto->slug) }}" class="store-product-name">

                                    {{ $producto->nombre }}

                                </a>

                    
                                <div class="store-product-category">
                                    {{ $producto->categoriaPrincipal->nombre ?? 'General' }}
                                </div>

                           
                                <div class="store-product-footer">

                                    <div>
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
                                            @if ($stockBase > 0)
                                                Stock: {{ $stockBase }}
                                            @else
                                                Agotado
                                            @endif
                                        </small>
                                    </div>

                                    <div class="d-flex gap-2">

                                        @if ($producto->usa_variantes)
                                            <a href="{{ route('tienda.productos.show', $producto->slug) }}"
                                                class="store-product-action" title="Seleccionar variante">

                                                <i class="bi bi-ui-checks"></i>

                                            </a>
                                        @else
                                            <button type="button"
                                                class="store-product-action js-add-cart {{ $productoEnCarrito ? 'is-added' : '' }}"
                                                data-url="{{ route('tienda.carrito.agregar', $producto->id_producto) }}"
                                                data-product-id="{{ $producto->id_producto }}"
                                                title="{{ $productoEnCarrito ? 'Producto agregado' : 'Agregar al carrito' }}"
                                                {{ $agotado || $productoEnCarrito ? 'disabled' : '' }}>

                                                <i
                                                    class="bi {{ $productoEnCarrito ? 'bi-check-lg' : 'bi-cart-plus' }}"></i>

                                            </button>
                                        @endif

                                        <a href="{{ route('tienda.productos.show', $producto->slug) }}"
                                            class="store-product-action">

                                            <i class="bi bi-eye"></i>

                                        </a>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                @empty

                    <div class="col-12">

                        <div class="alert alert-light border text-center py-5">

                            <i class="bi bi-box-seam display-5 d-block mb-3 text-muted"></i>

                            <h5 class="mb-2">
                                No hay productos disponibles
                            </h5>

                            <p class="text-muted mb-0">
                                Esta categoría todavía no tiene productos publicados.
                            </p>

                        </div>

                    </div>
                @endforelse

            </div>

        </div>

    </section>

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/carrito.js') }}"></script>
@endpush
--}}