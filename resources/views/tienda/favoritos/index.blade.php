{{-- resources/views/tienda/favoritos/index.blade.php --}}

@extends('tienda.layouts.app')

@section('title', 'Mis Favoritos')

@section('content')

    @php
        $placeholder = asset('assets/img/no-image.png');
    @endphp

    <section class="store-section pt-4 pt-lg-5">

        <div class="container">

            {{-- =========================================================
                HEADER
            ========================================================== --}}

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

            {{-- =========================================================
                EMPTY
            ========================================================== --}}

            @if($favoritos->isEmpty())

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

                        <p class="text-muted mb-4 mx-auto"
                           style="max-width: 580px; line-height: 1.8;">

                            Explora el catálogo y agrega productos usando el ícono del corazón.

                        </p>

                        <a href="{{ route('tienda.productos.index') }}"
                           class="btn btn-store-primary px-4">

                            Explorar productos

                        </a>

                    </div>

                </div>

            @else

                {{-- =========================================================
                    GRID PRODUCTOS
                ========================================================== --}}

                <div class="row g-3 g-md-4">

                    @foreach($favoritos as $favorito)

                        @php
                            $producto = $favorito->producto;

                            if (!$producto) {
                                continue;
                            }

                            $imagen = $producto->imagenPrincipal?->ruta
                                ? asset('storage/' . $producto->imagenPrincipal->ruta)
                                : $placeholder;
                        @endphp

                        <div class="col-6 col-md-4 col-xl-3">

                            <div class="store-product-card">

                                {{-- IMAGEN --}}
                                <a href="{{ route('tienda.productos.show', $producto->slug) }}"
                                   class="store-product-image-wrap">

                                    <img src="{{ $imagen }}"
                                         alt="{{ $producto->nombre }}"
                                         class="store-product-image">

                                    {{-- FAVORITO --}}
                                    <button type="button"
                                            class="store-product-heart js-favorite-btn is-active"
                                            data-url="{{ route('tienda.favoritos.toggle', $producto->id_producto) }}">

                                        <i class="bi bi-heart-fill"></i>

                                    </button>

                                    @if($producto->stock_actual <= 0)

                                        <span class="store-product-badge store-product-badge-muted">
                                            Agotado
                                        </span>

                                    @endif

                                </a>

                                {{-- BODY --}}
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

            @endif

        </div>

    </section>

@endsection