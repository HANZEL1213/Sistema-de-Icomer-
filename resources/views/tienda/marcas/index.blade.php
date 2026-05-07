{{-- resources/views/tienda/marcas/index.blade.php --}}

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
                            moderna, rápida y totalmente adaptada a móviles.
                        </p>

                    </div>

                    <div class="col-12 col-lg-5">

                        <form action="{{ route('tienda.marcas.index') }}" method="GET">

                            <div class="input-group store-search-group">

                                <span class="input-group-text">
                                    <i class="bi bi-search"></i>
                                </span>

                                <input type="text"
                                       name="q"
                                       class="form-control"
                                       placeholder="Buscar marca..."
                                       value="{{ request('q') }}">

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


        {{-- GRID --}}
        <div class="row g-3 g-md-4">

            @forelse($marcas as $marca)

                <div class="col-6 col-lg-4">

                    <a href="{{ route('tienda.marcas.show', $marca->slug) }}"
                       class="store-brand-card">

                        {{-- IMAGEN --}}
                        <div class="store-brand-image-wrap">

                            @if($marca->imagen)

                                <img src="{{ asset('storage/' . $marca->imagen) }}"
                                     alt="{{ $marca->nombre }}"
                                     class="store-brand-image">

                            @else

                                <div class="store-brand-placeholder">

                                    <span>
                                        {{ strtoupper(substr($marca->nombre, 0, 2)) }}
                                    </span>

                                </div>

                            @endif

                            <div class="store-brand-overlay"></div>

                            {{-- BADGE --}}
                            <span class="store-brand-badge">

                                {{ $marca->productos_count }}
                                {{ $marca->productos_count == 1 ? 'producto' : 'productos' }}

                            </span>

                        </div>


                        {{-- BODY --}}
                        <div class="store-brand-body">

                            <div>

                                <h3 class="store-brand-title">
                                    {{ $marca->nombre }}
                                </h3>

                                <p class="store-brand-text">
                                    Descubre productos de la marca
                                    {{ $marca->nombre }}
                                    disponibles en nuestra tienda.
                                </p>

                            </div>

                            <div class="store-brand-footer">

                                <span>
                                    Ver marca
                                </span>

                                <i class="bi bi-arrow-right"></i>

                            </div>

                        </div>

                    </a>

                </div>

            @empty

                <div class="col-12">

                    <div class="store-empty-state">

                        <div class="store-empty-icon">
                            <i class="bi bi-shop"></i>
                        </div>

                        <h4>
                            No se encontraron marcas
                        </h4>

                        <p>
                            Intenta realizar otra búsqueda o vuelve más tarde.
                        </p>

                    </div>

                </div>

            @endforelse

        </div>


        {{-- MOBILE BUTTON --}}
        <div class="d-grid mt-4 d-md-none">

            <a href="{{ route('tienda.productos.index') }}"
               class="btn btn-store-primary">

                Ver todos los productos

            </a>

        </div>

    </div>

</section>

@endsection