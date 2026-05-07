{{-- resources/views/tienda/marcas/show.blade.php --}}

@php

$marca = (object)[
    'nombre' => 'Nike',
    'descripcion' => 'Explora productos deportivos, urbanos y premium diseñados para rendimiento, comodidad y estilo moderno.',
    'banner' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?q=80&w=1800&auto=format&fit=crop',
    'productos_count' => 32,
    'logo' => 'N',
];

$productos = collect([
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
        'nombre' => 'Nike Street Runner',
        'precio' => 51990,
        'stock' => 8,
        'destacado' => false,
        'marca' => (object)['nombre' => 'Nike'],
        'categoriaPrincipal' => (object)['nombre' => 'Moda'],
        'imagen' => 'https://images.unsplash.com/photo-1549298916-b41d501d3772?q=80&w=1200&auto=format&fit=crop',
    ],

    (object)[
        'nombre' => 'Nike Active Pro',
        'precio' => 68990,
        'stock' => 5,
        'destacado' => true,
        'marca' => (object)['nombre' => 'Nike'],
        'categoriaPrincipal' => (object)['nombre' => 'Running'],
        'imagen' => 'https://images.unsplash.com/photo-1608231387042-66d1773070a5?q=80&w=1200&auto=format&fit=crop',
    ],

    (object)[
        'nombre' => 'Nike Casual X',
        'precio' => 38990,
        'stock' => 0,
        'destacado' => false,
        'marca' => (object)['nombre' => 'Nike'],
        'categoriaPrincipal' => (object)['nombre' => 'Lifestyle'],
        'imagen' => 'https://images.unsplash.com/photo-1460353581641-37baddab0fa2?q=80&w=1200&auto=format&fit=crop',
    ],

    (object)[
        'nombre' => 'Nike Zoom Elite',
        'precio' => 79990,
        'stock' => 4,
        'destacado' => true,
        'marca' => (object)['nombre' => 'Nike'],
        'categoriaPrincipal' => (object)['nombre' => 'Running'],
        'imagen' => 'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?q=80&w=1200&auto=format&fit=crop',
    ],

    (object)[
        'nombre' => 'Nike Motion Flex',
        'precio' => 56990,
        'stock' => 7,
        'destacado' => false,
        'marca' => (object)['nombre' => 'Nike'],
        'categoriaPrincipal' => (object)['nombre' => 'Training'],
        'imagen' => 'https://images.unsplash.com/photo-1556906781-9a412961c28c?q=80&w=1200&auto=format&fit=crop',
    ],
]);

@endphp

@extends('tienda.layouts.app')

@section('title', $marca->nombre . ' | Marca')
@section('meta_description', $marca->descripcion)

@section('content')

<section class="store-brand-show-page">

    {{-- HERO --}}
    <div class="store-brand-show-hero">

        <img src="{{ $marca->banner }}"
             alt="{{ $marca->nombre }}"
             class="store-brand-show-banner">

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

                    <span>{{ $marca->nombre }}</span>
                </div>

                <div class="store-brand-show-logo">
                    {{ $marca->logo }}
                </div>

                <span class="store-section-eyebrow">
                    Marca
                </span>

                <h1 class="store-brand-show-title">
                    {{ $marca->nombre }}
                </h1>

                <p class="store-brand-show-description">
                    {{ $marca->descripcion }}
                </p>

                <div class="store-brand-show-stats">

                    <div class="store-brand-show-stat">
                        <strong>{{ $marca->productos_count }}</strong>
                        <span>Productos</span>
                    </div>

                    <div class="store-brand-show-stat">
                        <strong>Premium</strong>
                        <span>Selección</span>
                    </div>

                    <div class="store-brand-show-stat">
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
                    Productos de {{ $marca->nombre }}
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