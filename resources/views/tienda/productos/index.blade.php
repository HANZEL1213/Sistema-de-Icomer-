{{-- resources/views/tienda/productos/index.blade.php --}}

@php

$productos = collect([
    (object)[
        'id_producto' => 1,
        'nombre' => 'Nike Air Max Urban',
        'precio' => 45990,
        'stock' => 12,
        'destacado' => true,
        'marca' => (object)['nombre' => 'Nike'],
        'categoriaPrincipal' => (object)['nombre' => 'Tenis'],
        'imagen' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?q=80&w=1200&auto=format&fit=crop',
    ],

    (object)[
        'id_producto' => 2,
        'nombre' => 'Audífonos Bluetooth Pro',
        'precio' => 32990,
        'stock' => 4,
        'destacado' => false,
        'marca' => (object)['nombre' => 'Sony'],
        'categoriaPrincipal' => (object)['nombre' => 'Tecnología'],
        'imagen' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?q=80&w=1200&auto=format&fit=crop',
    ],

    (object)[
        'id_producto' => 3,
        'nombre' => 'Smart Watch Active',
        'precio' => 68990,
        'stock' => 7,
        'destacado' => true,
        'marca' => (object)['nombre' => 'Samsung'],
        'categoriaPrincipal' => (object)['nombre' => 'Wearables'],
        'imagen' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?q=80&w=1200&auto=format&fit=crop',
    ],

    (object)[
        'id_producto' => 4,
        'nombre' => 'Mouse Gamer RGB',
        'precio' => 18990,
        'stock' => 0,
        'destacado' => false,
        'marca' => (object)['nombre' => 'Logitech'],
        'categoriaPrincipal' => (object)['nombre' => 'Gaming'],
        'imagen' => 'https://images.unsplash.com/photo-1527814050087-3793815479db?q=80&w=1200&auto=format&fit=crop',
    ],

    (object)[
        'id_producto' => 5,
        'nombre' => 'Chaqueta Minimal Premium',
        'precio' => 52990,
        'stock' => 15,
        'destacado' => true,
        'marca' => (object)['nombre' => 'Zara'],
        'categoriaPrincipal' => (object)['nombre' => 'Moda'],
        'imagen' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?q=80&w=1200&auto=format&fit=crop',
    ],

    (object)[
        'id_producto' => 6,
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

@section('title', 'Productos | Tienda')

@section('content')

<section class="store-products-page">

    {{-- HERO --}}
    <div class="store-products-hero">
        <div class="container">

            <div class="store-products-hero-card">

                <div class="row g-4 align-items-center">

                    <div class="col-12 col-lg-7">

                        <span class="store-section-eyebrow">
                            Catálogo premium
                        </span>

                        <h1 class="store-products-title">
                            Encuentra tus productos favoritos
                        </h1>

                        <p class="store-products-subtitle mb-0">
                            Explora productos, categorías y marcas con una experiencia moderna,
                            rápida y totalmente optimizada para móviles.
                        </p>

                    </div>

                    <div class="col-12 col-lg-5">

                        <form action="#" method="GET">

                            <div class="input-group store-search-group">

                                <span class="input-group-text">
                                    <i class="bi bi-search"></i>
                                </span>

                                <input type="text"
                                       class="form-control"
                                       placeholder="Buscar productos...">

                                <button class="btn btn-store-primary">
                                    Buscar
                                </button>

                            </div>

                        </form>

                    </div>

                </div>

            </div>

        </div>
    </div>


    {{-- CONTENIDO --}}
    <div class="container py-4 py-lg-5">

        <div class="row g-4">

            {{-- FILTROS --}}
            <div class="col-12 col-lg-3 order-2 order-lg-1">

                <div class="store-filter-card sticky-lg-top">

                    <div class="store-filter-header">

                        <div>
                            <h5 class="store-filter-title mb-1">
                                Filtros
                            </h5>

                            <p class="store-filter-subtitle mb-0">
                                Refina tu búsqueda
                            </p>
                        </div>

                        <i class="bi bi-sliders"></i>

                    </div>

                    <form>

                        {{-- Categoría --}}
                        <div class="store-filter-group">

                            <label class="store-filter-label">
                                Categoría
                            </label>

                            <select class="form-select store-filter-control">
                                <option>Todas las categorías</option>
                                <option>Tecnología</option>
                                <option>Moda</option>
                                <option>Gaming</option>
                                <option>Tenis</option>
                            </select>

                        </div>

                        {{-- Marca --}}
                        <div class="store-filter-group">

                            <label class="store-filter-label">
                                Marca
                            </label>

                            <select class="form-select store-filter-control">
                                <option>Todas las marcas</option>
                                <option>Nike</option>
                                <option>HP</option>
                                <option>Samsung</option>
                                <option>Sony</option>
                            </select>

                        </div>

                        {{-- Orden --}}
                        <div class="store-filter-group">

                            <label class="store-filter-label">
                                Ordenar por
                            </label>

                            <select class="form-select store-filter-control">
                                <option>Más recientes</option>
                                <option>Precio menor</option>
                                <option>Precio mayor</option>
                                <option>A-Z</option>
                            </select>

                        </div>

                        <div class="d-grid gap-2 mt-4">

                            <button class="btn btn-store-primary">
                                <i class="bi bi-funnel me-1"></i>
                                Aplicar filtros
                            </button>

                            <button class="btn btn-store-outline">
                                Limpiar
                            </button>

                        </div>

                    </form>

                </div>

            </div>


            {{-- PRODUCTOS --}}
            <div class="col-12 col-lg-9 order-1 order-lg-2">

                {{-- TOOLBAR --}}
                <div class="store-products-toolbar">

                    <div>
                        <h5 class="store-toolbar-title mb-1">
                            Productos disponibles
                        </h5>

                        <p class="store-toolbar-subtitle mb-0">
                            {{ $productos->count() }} productos encontrados
                        </p>
                    </div>

                    <a href="#"
                       class="btn btn-store-outline d-none d-md-inline-flex">
                        Ver categorías
                    </a>

                </div>


                {{-- GRID --}}
                <div class="row g-3 g-md-4">

                    @foreach($productos as $producto)

                        <div class="col-6 col-md-4 col-xl-3">

                            <div class="store-product-card">

                                {{-- IMAGEN --}}
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

                                {{-- INFO --}}
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

        </div>

    </div>

</section>

@endsection


