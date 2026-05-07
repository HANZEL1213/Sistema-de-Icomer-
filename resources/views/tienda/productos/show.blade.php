{{-- resources/views/tienda/productos/show.blade.php --}}

@php
    $producto = (object)[
        'nombre' => 'Nike Air Max Urban',
        'precio' => 45990,
        'stock' => 12,
        'destacado' => true,
        'descripcion' => 'Tenis urbanos con diseño moderno, cómodos para uso diario y con una estética premium para combinar con diferentes estilos.',
        'marca' => (object)['nombre' => 'Nike'],
        'categoriaPrincipal' => (object)['nombre' => 'Tenis'],
        'imagenes' => collect([
            'https://images.unsplash.com/photo-1542291026-7eec264c27ff?q=80&w=1400&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1549298916-b41d501d3772?q=80&w=1400&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1608231387042-66d1773070a5?q=80&w=1400&auto=format&fit=crop',
        ]),
    ];

    $relacionados = collect([
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
            'nombre' => 'Laptop Ultra Slim',
            'precio' => 489990,
            'stock' => 2,
            'destacado' => false,
            'marca' => (object)['nombre' => 'HP'],
            'categoriaPrincipal' => (object)['nombre' => 'Computadoras'],
            'imagen' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?q=80&w=1200&auto=format&fit=crop',
        ],
    ]);
@endphp

@extends('tienda.layouts.app')

@section('title', $producto->nombre . ' | Tienda')
@section('meta_description', $producto->descripcion)

@section('content')

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
                        <img src="{{ $producto->imagenes->first() }}"
                             alt="{{ $producto->nombre }}"
                             class="store-product-main-image"
                             id="storeProductMainImage">

                        @if($producto->destacado)
                            <span class="store-product-detail-badge">
                                Destacado
                            </span>
                        @endif

                        <button type="button"
                                class="store-product-detail-heart"
                                aria-label="Agregar a favoritos">
                            <i class="bi bi-heart"></i>
                        </button>
                    </div>

                    <div class="store-product-thumbs">
                        @foreach($producto->imagenes as $imagen)
                            <button type="button"
                                    class="store-product-thumb {{ $loop->first ? 'active' : '' }}"
                                    data-product-image="{{ $imagen }}">
                                <img src="{{ $imagen }}" alt="{{ $producto->nombre }}">
                            </button>
                        @endforeach
                    </div>

                </div>

            </div>


            {{-- INFO --}}
            <div class="col-12 col-lg-6">

                <div class="store-product-info-card">

                    <div class="store-product-detail-meta">
                        <span>{{ $producto->marca->nombre }}</span>
                        <span>•</span>
                        <span>{{ $producto->categoriaPrincipal->nombre }}</span>
                    </div>

                    <h1 class="store-product-detail-title">
                        {{ $producto->nombre }}
                    </h1>

                    <p class="store-product-detail-description">
                        {{ $producto->descripcion }}
                    </p>

                    <div class="store-product-detail-price">
                        ₡{{ number_format($producto->precio, 2) }}
                    </div>

                    <div class="store-product-detail-stock {{ $producto->stock > 0 ? 'is-available' : 'is-empty' }}">
                        <i class="bi {{ $producto->stock > 0 ? 'bi-check-circle' : 'bi-x-circle' }}"></i>

                        @if($producto->stock > 0)
                            Disponible · {{ $producto->stock }} unidades
                        @else
                            Producto agotado
                        @endif
                    </div>

                    <div class="store-product-detail-actions">

                        <div class="store-qty-control">
                            <button type="button" data-qty-action="minus">
                                <i class="bi bi-dash"></i>
                            </button>

                            <input type="number"
                                   value="1"
                                   min="1"
                                   max="{{ max($producto->stock, 1) }}"
                                   id="storeProductQty">

                            <button type="button" data-qty-action="plus">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>

                        <button type="button"
                                class="btn btn-store-primary store-add-cart-btn"
                                {{ $producto->stock <= 0 ? 'disabled' : '' }}>
                            <i class="bi bi-cart3 me-1"></i>
                            Agregar al carrito
                        </button>

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


        {{-- DESCRIPCIÓN / INFO EXTRA --}}
        <div class="row g-4 mt-4">

            <div class="col-12 col-lg-8">
                <div class="store-detail-section-card">
                    <h2 class="store-detail-section-title">
                        Descripción del producto
                    </h2>

                    <p class="store-detail-section-text mb-0">
                        {{ $producto->descripcion }}
                        Este espacio puede usarse después para mostrar características reales,
                        materiales, medidas, garantía, detalles técnicos o información adicional del producto.
                    </p>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="store-detail-section-card">
                    <h2 class="store-detail-section-title">
                        Información rápida
                    </h2>

                    <ul class="store-product-info-list">
                        <li>
                            <span>Marca</span>
                            <strong>{{ $producto->marca->nombre }}</strong>
                        </li>
                        <li>
                            <span>Categoría</span>
                            <strong>{{ $producto->categoriaPrincipal->nombre }}</strong>
                        </li>
                        <li>
                            <span>Estado</span>
                            <strong>{{ $producto->stock > 0 ? 'Disponible' : 'Agotado' }}</strong>
                        </li>
                    </ul>
                </div>
            </div>

        </div>


        {{-- PRODUCTOS RELACIONADOS --}}
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

                <a href="{{ route('tienda.productos.index') }}"
                   class="btn btn-store-outline px-4">
                    Ver catálogo
                </a>

            </div>

            <div class="row g-3 g-md-4">

                @foreach($relacionados as $item)

                    <div class="col-6 col-md-4 col-xl-3">

                        <div class="store-product-card">

                            <a href="#" class="store-product-image-wrap">

                                <img src="{{ $item->imagen }}"
                                     alt="{{ $item->nombre }}"
                                     class="store-product-image">

                                <button type="button"
                                        class="store-product-heart"
                                        aria-label="Agregar a favoritos">
                                    <i class="bi bi-heart"></i>
                                </button>

                                @if($item->stock <= 0)
                                    <span class="store-product-badge store-product-badge-muted">
                                        Agotado
                                    </span>
                                @elseif($item->destacado)
                                    <span class="store-product-badge">
                                        Destacado
                                    </span>
                                @endif

                            </a>

                            <div class="store-product-body">

                                <div class="store-product-meta">
                                    {{ $item->marca->nombre }}
                                </div>

                                <a href="#" class="store-product-name">
                                    {{ $item->nombre }}
                                </a>

                                <div class="store-product-category">
                                    {{ $item->categoriaPrincipal->nombre }}
                                </div>

                                <div class="store-product-footer">

                                    <div>
                                        <div class="store-product-price">
                                            ₡{{ number_format($item->precio, 2) }}
                                        </div>

                                        <small class="store-product-stock">
                                            Stock: {{ $item->stock }}
                                        </small>
                                    </div>

                                    <a href="#" class="store-product-action">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                </div>

                            </div>

                        </div>

                    </div>

                @endforeach

            </div>
        </section>

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
    const qtyButtons = document.querySelectorAll('[data-qty-action]');

    qtyButtons.forEach((button) => {
        button.addEventListener('click', function () {
            if (!qtyInput) return;

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
        });
    });

});
</script>
@endpush