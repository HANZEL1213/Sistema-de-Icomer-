{{-- resources/views/tienda/categorias/show.blade.php --}}

@extends('tienda.layouts.app')

@section('title', $categoria->nombre . ' | Categoría')
@section('meta_description', $categoria->descripcion)

@section('content')

<section class="store-category-show-page">

    {{-- HERO --}}
    <div class="store-category-show-hero">

        <img
            src="{{ $categoria->imagen
                ? asset('storage/' . $categoria->imagen)
                : asset('assets/img/placeholder/category-banner.jpg') }}"
            alt="{{ $categoria->nombre }}"
            class="store-category-show-banner"
        >

        <div class="store-category-show-overlay"></div>

        <div class="container position-relative">

            <div class="store-category-show-content">

                {{-- BREADCRUMB --}}
                <div class="store-detail-breadcrumb text-white mb-3">

                    <a href="{{ route('tienda.home') }}"
                       class="text-white">
                        Inicio
                    </a>

                    <i class="bi bi-chevron-right"></i>

                    <a href="{{ route('tienda.categorias.index') }}"
                       class="text-white">
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


    {{-- PRODUCTOS --}}
    <div class="container py-4 py-lg-5">

        {{-- TOOLBAR --}}
        <div class="store-products-toolbar">

            <div>

                <h5 class="store-toolbar-title mb-1">
                    Productos de {{ $categoria->nombre }}
                </h5>

                <p class="store-toolbar-subtitle mb-0">
                    {{ $productos->count() }} productos encontrados
                </p>

            </div>

            <a href="{{ route('tienda.productos.index') }}"
               class="btn btn-store-outline d-none d-md-inline-flex">
                Ver catálogo
            </a>

        </div>


        {{-- GRID PRODUCTOS --}}
        <div class="row g-3 g-md-4">

            @forelse($productos as $producto)

                <div class="col-6 col-md-4 col-xl-3">

                    <div class="store-product-card">

                        {{-- IMAGEN --}}
                        <a href="{{ route('tienda.productos.show', $producto->slug) }}"
                           class="store-product-image-wrap">

                            <img
                                src="{{ $producto->imagenPrincipal
                                    ? asset('storage/' . $producto->imagenPrincipal->ruta)
                                    : asset('assets/img/placeholder/product.jpg') }}"
                                alt="{{ $producto->nombre }}"
                                class="store-product-image"
                            >

                          <button type="button"
        class="store-product-heart js-favorite-btn {{ in_array($producto->id_producto, $favoritosIds ?? []) ? 'is-active' : '' }}"
        data-url="{{ route('tienda.favoritos.toggle', $producto->id_producto) }}"
        aria-label="Agregar a favoritos">

    <i class="bi {{ in_array($producto->id_producto, $favoritosIds ?? []) ? 'bi-heart-fill' : 'bi-heart' }}"></i>

</button>

                   @if($producto->stock_actual <= 0)

    <span class="store-product-badge store-product-badge-muted">
        Agotado
    </span>

@elseif($producto->destacado)

    <span class="store-product-badge">
        Destacado
    </span>

@endif

                        </a>


                        {{-- BODY --}}
                        <div class="store-product-body">

                            {{-- MARCA --}}
                            <div class="store-product-meta">
                                {{ $producto->marca->nombre ?? 'Sin marca' }}
                            </div>

                            {{-- NOMBRE --}}
                            <a href="{{ route('tienda.productos.show', $producto->slug) }}"
                               class="store-product-name">

                                {{ $producto->nombre }}

                            </a>

                            {{-- CATEGORÍA --}}
                            <div class="store-product-category">
                                {{ $producto->categoriaPrincipal->nombre ?? 'General' }}
                            </div>

                            {{-- FOOTER --}}
                            <div class="store-product-footer">

                                <div>

                         @php
    $tienePromo = $producto->tienePromocionActiva();

    $precioNormal = (float) $producto->precio;
    $precioVenta = $producto->precioVenta();

    $ahorro = $tienePromo
        ? max(0, $precioNormal - $precioVenta)
        : 0;

    $porcentaje = $tienePromo && $precioNormal > 0
        ? round(($ahorro / $precioNormal) * 100)
        : 0;
@endphp

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

                                   @if($producto->stock_actual > 0)
    Stock: {{ $producto->stock_actual}}
@else
    Agotado
@endif

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