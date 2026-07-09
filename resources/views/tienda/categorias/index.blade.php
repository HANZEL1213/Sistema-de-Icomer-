{{-- resources/views/tienda/categorias/index.blade.php --}}

@extends('tienda.layouts.app')

@section('title', 'Inicio | ' . ($configTienda['tienda_nombre'] ?? 'Mi Tienda'))
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

                            <form action="{{ route('tienda.categorias.index') }}" method="GET">

                                <div class="input-group store-search-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-search"></i>
                                    </span>

                                    <input type="text" name="q" value="{{ request('q') }}" class="form-control"
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

                <a href="{{ route('tienda.productos.index') }}" class="btn btn-store-outline d-none d-md-inline-flex">
                    Ver productos
                </a>

            </div>


            {{-- GRID CATEGORÍAS --}}
            <div class="row g-3 g-md-4">

                @forelse($categorias as $categoria)
                    <div class="col-12 col-sm-6 col-lg-4">

                        <a href="{{ route('tienda.productos.index', ['categoria' => $categoria->id_categoria]) }}"
                            class="store-category-card">

                            <div class="store-category-image-wrap">

                                <img src="{{ $categoria->imagen ? asset('storage/' . $categoria->imagen) : asset('assets/img/placeholder/category.jpg') }}"
                                    alt="{{ $categoria->nombre }}" class="store-category-image">

                                <div class="store-category-overlay"></div>

                                <div class="store-category-icon">
                                    <i class="bi bi-grid"></i>
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
                                        {{ $categoria->descripcion ?: 'Explora productos disponibles en esta categoría.' }}
                                    </p>

                                </div>

                                <div class="store-category-footer">

                                    <span>
                                        Explorar categoría
                                    </span>

                                    <i class="bi bi-arrow-right"></i>

                                </div>

                            </div>

                        </a>

                    </div>

                @empty

                    <div class="col-12">

                        <div class="alert alert-light border text-center py-5">

                            <i class="bi bi-grid display-5 d-block mb-3 text-muted"></i>

                            <h5 class="mb-2">
                                No hay categorías disponibles
                            </h5>

                            <p class="text-muted mb-0">
                                Intenta nuevamente más tarde.
                            </p>

                        </div>

                    </div>
                @endforelse

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
