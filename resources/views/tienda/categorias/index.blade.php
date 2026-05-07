{{-- resources/views/tienda/categorias/index.blade.php --}}

@php
    $categorias = collect([
        (object)[
            'nombre' => 'Tecnología',
            'descripcion' => 'Dispositivos, accesorios y productos modernos para el día a día.',
            'productos_count' => 24,
            'imagen' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?q=80&w=1200&auto=format&fit=crop',
            'icono' => 'bi-cpu',
        ],
        (object)[
            'nombre' => 'Moda',
            'descripcion' => 'Prendas, estilos y accesorios para una imagen más actual.',
            'productos_count' => 18,
            'imagen' => 'https://images.unsplash.com/photo-1483985988355-763728e1935b?q=80&w=1200&auto=format&fit=crop',
            'icono' => 'bi-bag',
        ],
        (object)[
            'nombre' => 'Gaming',
            'descripcion' => 'Accesorios, periféricos y productos para jugadores.',
            'productos_count' => 15,
            'imagen' => 'https://images.unsplash.com/photo-1542751371-adc38448a05e?q=80&w=1200&auto=format&fit=crop',
            'icono' => 'bi-controller',
        ],
        (object)[
            'nombre' => 'Tenis',
            'descripcion' => 'Calzado casual, deportivo y urbano para diferentes estilos.',
            'productos_count' => 32,
            'imagen' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?q=80&w=1200&auto=format&fit=crop',
            'icono' => 'bi-lightning-charge',
        ],
        (object)[
            'nombre' => 'Computadoras',
            'descripcion' => 'Laptops, equipos y accesorios para productividad.',
            'productos_count' => 9,
            'imagen' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?q=80&w=1200&auto=format&fit=crop',
            'icono' => 'bi-laptop',
        ],
        (object)[
            'nombre' => 'Wearables',
            'descripcion' => 'Relojes inteligentes y accesorios conectados.',
            'productos_count' => 11,
            'imagen' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?q=80&w=1200&auto=format&fit=crop',
            'icono' => 'bi-smartwatch',
        ],
    ]);
@endphp

@extends('tienda.layouts.app')

@section('title', 'Categorías | Tienda')
@section('meta_description', 'Explora las categorías disponibles en nuestra tienda en línea.')

@section('content')

<section class="store-categories-page">

    {{-- HERO --}}
    <div class="store-products-hero">
        <div class="container">

            <div class="store-products-hero-card">

                <div class="row g-4 align-items-center">

                    <div class="col-12 col-lg-7">

                        <span class="store-section-eyebrow">
                            Explorar tienda
                        </span>

                        <h1 class="store-products-title">
                            Categorías para encontrar más rápido
                        </h1>

                        <p class="store-products-subtitle mb-0">
                            Navega por las principales áreas de la tienda y encuentra productos
                            de forma más simple, visual y ordenada.
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
                                       placeholder="Buscar categoría...">

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

        {{-- TOOLBAR --}}
        <div class="store-products-toolbar">

            <div>
                <h5 class="store-toolbar-title mb-1">
                    Categorías disponibles
                </h5>

                <p class="store-toolbar-subtitle mb-0">
                    {{ $categorias->count() }} categorías encontradas
                </p>
            </div>

            <a href="{{ route('tienda.productos.index') }}"
               class="btn btn-store-outline d-none d-md-inline-flex">
                Ver productos
            </a>

        </div>


        {{-- GRID CATEGORÍAS --}}
        <div class="row g-3 g-md-4">

            @foreach($categorias as $index => $categoria)

                <div class="col-12 col-sm-6 col-lg-4">

                    <a href="{{ route('tienda.categorias.show', 'categoria-demo-' . ($index + 1)) }}"
                       class="store-category-card">

                        <div class="store-category-image-wrap">

                            <img src="{{ $categoria->imagen }}"
                                 alt="{{ $categoria->nombre }}"
                                 class="store-category-image">

                            <div class="store-category-overlay"></div>

                            <div class="store-category-icon">
                                <i class="bi {{ $categoria->icono }}"></i>
                            </div>

                            <span class="store-category-badge">
                                {{ $categoria->productos_count }} productos
                            </span>

                        </div>

                        <div class="store-category-body">

                            <div>
                                <h3 class="store-category-title">
                                    {{ $categoria->nombre }}
                                </h3>

                                <p class="store-category-text">
                                    {{ $categoria->descripcion }}
                                </p>
                            </div>

                            <div class="store-category-footer">
                                <span>Explorar categoría</span>
                                <i class="bi bi-arrow-right"></i>
                            </div>

                        </div>

                    </a>

                </div>

            @endforeach

        </div>


        {{-- BLOQUE CTA MOBILE --}}
        <div class="d-grid mt-4 d-md-none">
            <a href="{{ route('tienda.productos.index') }}" class="btn btn-store-primary">
                Ver todos los productos
            </a>
        </div>

    </div>

</section>

@endsection


