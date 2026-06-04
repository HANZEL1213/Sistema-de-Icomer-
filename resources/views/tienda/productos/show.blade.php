{{-- resources/views/tienda/productos/show.blade.php --}}

@extends('tienda.layouts.app')

@section('title', $producto->nombre . ' | Tienda')
@section('meta_description', $producto->descripcion ?? 'Detalle del producto')

@section('content')

    @php
        $imagenes = $producto->imagenes->count() ? $producto->imagenes : collect();

        $imagenPrincipal = $producto->imagenPrincipal
            ? asset('storage/' . $producto->imagenPrincipal->ruta)
            : ($imagenes->first()
                ? asset('storage/' . $imagenes->first()->ruta)
                : asset('assets/img/no-image.png'));

        $stock = $producto->stock_actual ?? 0;
    @endphp

    <section class="store-product-detail-page">

        <div class="container py-4 py-lg-5">

            {{-- BREADCRUMB --}}
            <div class="store-detail-breadcrumb mb-3 mb-lg-4">
                <a href="{{ route('tienda.home') }}">Inicio</a>
                <i class="bi bi-chevron-right"></i>
                <a href="{{ route('tienda.productos.index') }}">Productos</a>
                <i class="bi bi-chevron-right"></i>
                <span>{{ $producto->nombre }}</span>
            </div>


            {{-- DETALLE PRINCIPAL --}}
            <div class="row g-4 g-lg-5 align-items-start">

                {{-- GALERÍA --}}
                <div class="col-12 col-lg-6">

                    <div class="store-product-gallery-card">

                        <div class="store-product-main-image-wrap">

                            <img src="{{ $imagenPrincipal }}" alt="{{ $producto->nombre }}" class="store-product-main-image"
                                id="storeProductMainImage">

                            @if ($stock <= 0)
                                <span class="store-product-detail-badge">
                                    Agotado
                                </span>
                            @endif

                            <button type="button"
                                class="store-product-detail-heart js-favorite-btn {{ in_array($producto->id_producto, $favoritosIds ?? []) ? 'is-active' : '' }}"
                                data-url="{{ route('tienda.favoritos.toggle', $producto->id_producto) }}"
                                aria-label="Agregar a favoritos">

                                <i
                                    class="bi {{ in_array($producto->id_producto, $favoritosIds ?? []) ? 'bi-heart-fill' : 'bi-heart' }}"></i>

                            </button>
                        </div>

                        @if ($imagenes->count())

                            <div class="store-product-thumbs">

                                @foreach ($imagenes as $imagen)
                                    @php
                                        $rutaImagen = asset('storage/' . $imagen->ruta);
                                    @endphp

                                    <button type="button" class="store-product-thumb {{ $loop->first ? 'active' : '' }}"
                                        data-product-image="{{ $rutaImagen }}">

                                        <img src="{{ $rutaImagen }}" alt="{{ $producto->nombre }}">

                                    </button>
                                @endforeach

                            </div>

                        @endif

                    </div>

                </div>


                {{-- INFO --}}
                <div class="col-12 col-lg-6">

                    <div class="store-product-info-card">

                        <div class="store-product-detail-meta">
                            <span>{{ $producto->marca?->nombre ?? 'Sin marca' }}</span>
                            <span>•</span>
                            <span>{{ $producto->categoriaPrincipal?->nombre ?? 'Sin categoría' }}</span>
                        </div>

                        <h1 class="store-product-detail-title">
                            {{ $producto->nombre }}
                        </h1>

                        @if ($producto->descripcion)
                            <p class="store-product-detail-description">
                                {{ $producto->descripcion }}
                            </p>
                        @endif

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

@if($tienePromo)

    <div class="mb-2">
        <span class="badge bg-danger text-white">
            -{{ $porcentaje }}% OFF
        </span>
    </div>

    <div class="text-muted text-decoration-line-through mb-1">
        ₡{{ number_format($precioNormal, 2) }}
    </div>

    <div class="store-product-detail-price text-danger">
        ₡{{ number_format($precioVenta, 2) }}
    </div>

@else

    <div class="store-product-detail-price">
        ₡{{ number_format($precioNormal, 2) }}
    </div>

@endif

                        <div class="store-product-detail-stock {{ $stock > 0 ? 'is-available' : 'is-empty' }}">
                            <i class="bi {{ $stock > 0 ? 'bi-check-circle' : 'bi-x-circle' }}"></i>

                            @if ($stock > 0)
                                Disponible · {{ $stock }} unidades
                            @else
                                Producto agotado
                            @endif
                        </div>

                        <div class="store-product-detail-actions">

                            <div class="store-qty-control">

                                <button type="button" data-qty-action="minus">
                                    <i class="bi bi-dash"></i>
                                </button>

                                <input type="number" value="1" min="1" max="{{ max($stock, 1) }}"
                                    id="storeProductQty">

                                <button type="button" data-qty-action="plus">
                                    <i class="bi bi-plus"></i>
                                </button>

                            </div>

                            <form action="{{ route('tienda.carrito.agregar', $producto->id_producto) }}" method="POST"
                                class="m-0 p-0">

                                @csrf

                                <input type="hidden" name="cantidad" id="storeProductQtyHidden" value="1">

                                <button type="submit" class="btn btn-store-primary store-add-cart-btn"
                                    {{ $stock <= 0 ? 'disabled' : '' }}>

                                    <i class="bi bi-cart3 me-1"></i>
                                    Agregar al carrito

                                </button>

                            </form>

                        </div>

                        <div class="store-product-detail-benefits">

                            <div class="store-product-benefit">
                                <i class="bi bi-truck"></i>
                                <span>Envíos disponibles</span>
                            </div>

                            <div class="store-product-benefit">
                                <i class="bi bi-shield-check"></i>
                                <span>Compra segura</span>
                            </div>

                            <div class="store-product-benefit">
                                <i class="bi bi-arrow-repeat"></i>
                                <span>Proceso ágil</span>
                            </div>

                        </div>



                    </div>

                </div>

            </div>


            {{-- PRODUCTOS RELACIONADOS --}}
            @if ($relacionados->count())

                <section class="store-section px-0 pb-0">

                    <div class="d-flex align-items-end justify-content-between flex-wrap gap-3 mb-4">

                        <div>

                            <div class="store-mini-label">
                                También te puede gustar
                            </div>

                            <h2 class="store-section-title mb-2">
                                Productos relacionados
                            </h2>

                            <p class="store-section-subtitle">
                                Más opciones con la misma línea visual del catálogo.
                            </p>

                        </div>

                        <a href="{{ route('tienda.productos.index') }}" class="btn btn-store-outline px-4">
                            Ver catálogo
                        </a>

                    </div>

                    <div class="row g-3 g-md-4">

                        @foreach ($relacionados as $item)
                            @php
                                $imagenRelacionado = $item->imagenPrincipal
                                    ? asset('storage/' . $item->imagenPrincipal->ruta)
                                    : asset('assets/img/no-image.png');

                                $stockRelacionado = $item->stock_actual ?? 0;
                            @endphp

                            <div class="col-6 col-md-4 col-xl-3">

                                <div class="store-product-card">

                                    <a href="{{ route('tienda.productos.show', $item->slug) }}"
                                        class="store-product-image-wrap">

                                        <img src="{{ $imagenRelacionado }}" alt="{{ $item->nombre }}"
                                            class="store-product-image">

                                        <button type="button"
                                            class="store-product-heart js-favorite-btn {{ in_array($item->id_producto, $favoritosIds ?? []) ? 'is-active' : '' }}"
                                            data-url="{{ route('tienda.favoritos.toggle', $item->id_producto) }}"
                                            aria-label="Agregar a favoritos">

                                            <i
                                                class="bi {{ in_array($item->id_producto, $favoritosIds ?? []) ? 'bi-heart-fill' : 'bi-heart' }}"></i>

                                        </button>

                                        @if ($stockRelacionado <= 0)
                                            <span class="store-product-badge store-product-badge-muted">
                                                Agotado
                                            </span>
                                        @endif

                                    </a>

                                    <div class="store-product-body">

                                        <div class="store-product-meta">
                                            {{ $item->marca?->nombre ?? 'Sin marca' }}
                                        </div>

                                        <a href="{{ route('tienda.productos.show', $item->slug) }}"
                                            class="store-product-name">
                                            {{ $item->nombre }}
                                        </a>

                                        <div class="store-product-category">
                                            {{ $item->categoriaPrincipal?->nombre ?? 'Sin categoría' }}
                                        </div>

                                        <div class="store-product-footer">

                                            <div>
                                           @php
    $tienePromo = $item->tienePromocionActiva();

    $precioNormal = (float) $item->precio;
    $precioVenta = $item->precioVenta();

    $ahorro = $tienePromo
        ? max(0, $precioNormal - $precioVenta)
        : 0;

    $porcentaje = $tienePromo && $precioNormal > 0
        ? round(($ahorro / $precioNormal) * 100)
        : 0;
@endphp

@if($tienePromo)

    <span class="badge bg-danger mb-1">
        -{{ $porcentaje }}% OFF
    </span>

    <div class="text-muted text-decoration-line-through small">
        ₡{{ number_format($precioNormal, 2) }}
    </div>

    <div class="store-product-price text-danger">
        ₡{{ number_format($precioVenta, 2) }}
    </div>

@else

    <div class="store-product-price">
        ₡{{ number_format($precioNormal, 2) }}
    </div>

@endif

                                                <small class="store-product-stock">
                                                    Stock: {{ $stockRelacionado }}
                                                </small>
                                            </div>

                                            <a href="{{ route('tienda.productos.show', $item->slug) }}"
                                                class="store-product-action">
                                                <i class="bi bi-eye"></i>
                                            </a>

                                        </div>

                                    </div>

                                </div>

                            </div>
                        @endforeach

                    </div>

                </section>

            @endif

        </div>

    </section>

@endsection

@push('scripts')
  <script>
document.addEventListener('DOMContentLoaded', function () {

    const mainImage = document.getElementById('storeProductMainImage');
    const thumbs = document.querySelectorAll('.store-product-thumb');

    thumbs.forEach((thumb) => {

        thumb.addEventListener('click', function () {

            const image = this.dataset.productImage;

            if (mainImage && image) {
                mainImage.src = image;
            }

            thumbs.forEach((item) => item.classList.remove('active'));

            this.classList.add('active');

        });

    });

    const qtyInput = document.getElementById('storeProductQty');
    const qtyHidden = document.getElementById('storeProductQtyHidden');
    const qtyButtons = document.querySelectorAll('[data-qty-action]');

    if (!qtyInput || !qtyHidden) return;

    qtyButtons.forEach((button) => {

        button.addEventListener('click', function () {

            const action = this.dataset.qtyAction;

            const min = parseInt(qtyInput.min || 1);
            const max = parseInt(qtyInput.max || 999);

            let value = parseInt(qtyInput.value || 1);

            if (action === 'minus') {
                value = Math.max(min, value - 1);
            }

            if (action === 'plus') {
                value = Math.min(max, value + 1);
            }

            qtyInput.value = value;
            qtyHidden.value = value;

        });

    });

    qtyInput.addEventListener('input', function () {

        let value = parseInt(this.value || 1);

        const min = parseInt(this.min || 1);
        const max = parseInt(this.max || 999);

        if (value < min) value = min;
        if (value > max) value = max;

        this.value = value;

        qtyHidden.value = value;

    });

});
</script>
@endpush
