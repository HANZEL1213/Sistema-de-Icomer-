{{-- resources/views/tienda/marcas/index.blade.php --}}

@php
    $marcas = collect([
        (object)[
            'nombre' => 'Nike',
            'descripcion' => 'Productos deportivos, urbanos y de alto rendimiento.',
            'productos_count' => 32,
            'logo' => 'N',
            'imagen' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?q=80&w=1200&auto=format&fit=crop',
        ],
        (object)[
            'nombre' => 'Sony',
            'descripcion' => 'Tecnología, audio y dispositivos para una experiencia premium.',
            'productos_count' => 18,
            'logo' => 'S',
            'imagen' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?q=80&w=1200&auto=format&fit=crop',
        ],
        (object)[
            'nombre' => 'Samsung',
            'descripcion' => 'Innovación, wearables y dispositivos inteligentes.',
            'productos_count' => 21,
            'logo' => 'SA',
            'imagen' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?q=80&w=1200&auto=format&fit=crop',
        ],
        (object)[
            'nombre' => 'Logitech',
            'descripcion' => 'Accesorios, periféricos y productos para gaming.',
            'productos_count' => 14,
            'logo' => 'L',
            'imagen' => 'https://images.unsplash.com/photo-1527814050087-3793815479db?q=80&w=1200&auto=format&fit=crop',
        ],
        (object)[
            'nombre' => 'HP',
            'descripcion' => 'Computadoras, laptops y equipo para productividad.',
            'productos_count' => 9,
            'logo' => 'HP',
            'imagen' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?q=80&w=1200&auto=format&fit=crop',
        ],
        (object)[
            'nombre' => 'Canon',
            'descripcion' => 'Fotografía, cámaras y accesorios profesionales.',
            'productos_count' => 7,
            'logo' => 'C',
            'imagen' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?q=80&w=1200&auto=format&fit=crop',
        ],
    ]);
@endphp

@extends('tienda.layouts.app')

@section('title', 'Marcas | Tienda')
@section('meta_description', 'Explora las marcas disponibles en nuestra tienda en línea.')

@section('content')

<section class="store-brands-page">

    {{-- HERO --}}
    <div class="store-products-hero">
        <div class="container">

            <div class="store-products-hero-card">

                <div class="row g-4 align-items-center">

                    <div class="col-12 col-lg-7">

                        <span class="store-section-eyebrow">
                            Marcas
                        </span>

                        <h1 class="store-products-title">
                            Explora tus marcas favoritas
                        </h1>

                        <p class="store-products-subtitle mb-0">
                            Encuentra productos organizados por marca con una experiencia
                            clara, moderna y pensada para comprar desde el celular.
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
                                       placeholder="Buscar marca...">

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
                    Marcas disponibles
                </h5>

                <p class="store-toolbar-subtitle mb-0">
                    {{ $marcas->count() }} marcas encontradas
                </p>
            </div>

            <a href="{{ route('tienda.productos.index') }}"
               class="btn btn-store-outline d-none d-md-inline-flex">
                Ver productos
            </a>

        </div>


        {{-- GRID MARCAS --}}
        <div class="row g-3 g-md-4">

            @foreach($marcas as $index => $marca)

                <div class="col-6 col-lg-4">

                    <a href="{{ route('tienda.marcas.show', 'marca-demo-' . ($index + 1)) }}"
                       class="store-brand-card">

                        <div class="store-brand-image-wrap">

                            <img src="{{ $marca->imagen }}"
                                 alt="{{ $marca->nombre }}"
                                 class="store-brand-image">

                            <div class="store-brand-overlay"></div>

                            <div class="store-brand-logo">
                                {{ $marca->logo }}
                            </div>

                            <span class="store-brand-badge">
                                {{ $marca->productos_count }} productos
                            </span>

                        </div>

                        <div class="store-brand-body">

                            <div>
                                <h3 class="store-brand-title">
                                    {{ $marca->nombre }}
                                </h3>

                                <p class="store-brand-text">
                                    {{ $marca->descripcion }}
                                </p>
                            </div>

                            <div class="store-brand-footer">
                                <span>Ver marca</span>
                                <i class="bi bi-arrow-right"></i>
                            </div>

                        </div>

                    </a>

                </div>

            @endforeach

        </div>


        <div class="d-grid mt-4 d-md-none">
            <a href="{{ route('tienda.productos.index') }}" class="btn btn-store-primary">
                Ver todos los productos
            </a>
        </div>

    </div>

</section>

@endsection