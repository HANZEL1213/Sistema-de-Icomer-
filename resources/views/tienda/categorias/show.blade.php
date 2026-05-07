{{-- resources/views/tienda/categorias/show.blade.php --}}

@php

$categoria = (object)[
    'nombre' => 'Tecnología',
    'descripcion' => 'Explora productos tecnológicos modernos, accesorios y dispositivos premium para el día a día.',
    'banner' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?q=80&w=1600&auto=format&fit=crop',
    'productos_count' => 24,
];

$productos = collect([
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
        'nombre' => 'Laptop Ultra Slim',
        'precio' => 489990,
        'stock' => 2,
        'destacado' => false,
        'marca' => (object)['nombre' => 'HP'],
        'categoriaPrincipal' => (object)['nombre' => 'Computadoras'],
        'imagen' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?q=80&w=1200&auto=format&fit=crop',
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

@extends('tienda.layouts.app')

@section('title', $categoria->nombre . ' | Categoría')
@section('meta_description', $categoria->descripcion)

@section('content')

<section class="store-category-show-page">

    {{-- HERO --}}
    <div class="store-category-show-hero">

        <img src="{{ $categoria->banner }}"
             alt="{{ $categoria->nombre }}"
             class="store-category-show-banner">

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

                    <span>{{ $categoria->nombre }}</span>
                </div>

                <span class="store-section-eyebrow">
                    Categoría
                </span>

                <h1 class="store-category-show-title">
                    {{ $categoria->nombre }}
                </h1>

                <p class="store-category-show-description">
                    {{ $categoria->descripcion }}
                </p>

                <div class="store-category-show-stats">

                    <div class="store-category-show-stat">
                        <strong>{{ $categoria->productos_count }}</strong>
                        <span>Productos</span>
                    </div>

                    <div class="store-category-show-stat">
                        <strong>Premium</strong>
                        <span>Selección</span>
                    </div>

                    <div class="store-category-show-stat">
                        <strong>Mobile</strong>
                        <span>Optimizado</span>
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


        {{-- GRID --}}
        <div class="row g-3 g-md-4">

            @foreach($productos as $producto)

                <div class="col-6 col-md-4 col-xl-3">

                    <div class="store-product-card">

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

@endsection