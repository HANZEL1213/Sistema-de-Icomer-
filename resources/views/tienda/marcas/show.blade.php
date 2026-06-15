{{-- resources/views/tienda/marcas/show.blade.php 

@extends('tienda.layouts.app')

@section('title', $marca->nombre . ' | Marca')
@section('meta_description', $marca->descripcion ?? 'Productos de la marca ' . $marca->nombre)

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/modules/carrito.css') }}">

    @php
        $carritoIds = collect(session('carrito', []))->keys()->toArray();
    @endphp
    <section class="store-brand-show-page">

       
        <div class="store-brand-show-hero">

            @if ($marca->imagen)
                <img src="{{ asset('storage/' . $marca->imagen) }}" alt="{{ $marca->nombre }}" class="store-brand-show-banner">
            @else
                <div class="store-brand-show-placeholder"></div>
            @endif

            <div class="store-brand-show-overlay"></div>

            <div class="container position-relative">

                <div class="store-brand-show-content">

                  
                    <div class="store-detail-breadcrumb text-white mb-3">

                        <a href="{{ route('tienda.home') }}" class="text-white">

                            Inicio

                        </a>

                        <i class="bi bi-chevron-right"></i>

                        <a href="{{ route('tienda.marcas.index') }}" class="text-white">

                            Marcas

                        </a>

                        <i class="bi bi-chevron-right"></i>

                        <span>
                            {{ $marca->nombre }}
                        </span>

                    </div>


                    <div class="store-brand-show-logo">

                        {{ strtoupper(substr($marca->nombre, 0, 2)) }}

                    </div>


                    <span class="store-section-eyebrow">
                        Marca
                    </span>

                    <h1 class="store-brand-show-title">
                        {{ $marca->nombre }}
                    </h1>

                    <p class="store-brand-show-description">

                        {{ $marca->descripcion ?: 'Explora todos los productos disponibles de esta marca en nuestra tienda.' }}

                    </p>


                    
                    <div class="store-brand-show-stats">

                        <div class="store-brand-show-stat">

                            <strong>
                                {{ $productos->count() }}
                            </strong>

                            <span>
                                Productos
                            </span>

                        </div>

                        <div class="store-brand-show-stat">

                            <strong>
                                Premium
                            </strong>

                            <span>
                                Calidad
                            </span>

                        </div>



                    </div>

                </div>

            </div>

        </div>


   
        <div class="container py-4 py-lg-5">

        
            <div class="store-products-toolbar">

                <div>

                    <h5 class="store-toolbar-title mb-1">

                        Productos de {{ $marca->nombre }}

                    </h5>

                    <p class="store-toolbar-subtitle mb-0">

                        {{ $productos->count() }}
                        {{ $productos->count() == 1 ? 'producto encontrado' : 'productos encontrados' }}

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

                                @if ($producto->imagenPrincipal)
                                    <img src="{{ asset('storage/' . $producto->imagenPrincipal->ruta) }}"
                                        alt="{{ $producto->nombre }}" class="store-product-image">
                                @else
                                    <div class="store-product-placeholder">

                                        <i class="bi bi-image"></i>

                                    </div>
                                @endif


                                
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

                                    {{ $producto->marca?->nombre }}

                                </div>

                                <a href="{{ route('tienda.productos.show', $producto->slug) }}" class="store-product-name">

                                    {{ $producto->nombre }}

                                </a>

                                <div class="store-product-category">

                                    {{ $producto->categoriaPrincipal?->nombre ?? 'Sin categoría' }}

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

                        <div class="store-empty-state">

                            <div class="store-empty-icon">
                                <i class="bi bi-box-seam"></i>
                            </div>

                            <h4>
                                Esta marca aún no tiene productos
                            </h4>

                            <p>
                                Pronto agregaremos nuevos productos disponibles.
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