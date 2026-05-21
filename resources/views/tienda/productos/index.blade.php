{{-- resources/views/tienda/productos/index.blade.php --}}
@extends('tienda.layouts.app')

@section('title', 'Inicio | ' . ($configTienda['tienda_nombre'] ?? 'Mi Tienda'))

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

                            {{-- BUSCADOR --}}
                            <form action="{{ route('tienda.productos.index') }}" method="GET">

                                <div class="input-group store-search-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-search"></i>
                                    </span>

                                    <input type="text" name="q" value="{{ request('q') }}" class="form-control"
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

        {{-- FILTRO COLABSABLE PARA MÓVIL --}}
        <div class="container mt-3 d-lg-none">
            <div class="store-filter-collapsible">
                <button class="store-filter-toggle" type="button" id="filterToggleBtn">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <div>
                            <i class="bi bi-sliders2 me-2"></i>
                            <span>Filtrar productos</span>
                            @if (request('categoria') || request('marca') || request('orden'))
                                <span class="store-filter-badge">!</span>
                            @endif
                        </div>
                        <i class="bi bi-chevron-down filter-icon"></i>
                    </div>
                </button>

                <div class="store-filter-collapsible-content" id="filterCollapsibleContent" style="display: none;">
                    <div class="store-filter-card-mobile">
                        <form action="{{ route('tienda.productos.index') }}" method="GET" id="mobileFilterForm">

                            {{-- Mantener búsqueda --}}
                            <input type="hidden" name="q" value="{{ request('q') }}">

                            {{-- Categoría --}}
                            <div class="store-filter-group">
                                <label class="store-filter-label">
                                    <i class="bi bi-tag me-1"></i> Categoría
                                </label>
                                <select name="categoria" class="form-select store-filter-control">
                                    <option value="">Todas las categorías</option>
                                    @foreach ($categorias as $categoria)
                                        <option value="{{ $categoria->id_categoria }}"
                                            {{ request('categoria') == $categoria->id_categoria ? 'selected' : '' }}>
                                            {{ $categoria->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Marca --}}
                            <div class="store-filter-group">
                                <label class="store-filter-label">
                                    <i class="bi bi-shop me-1"></i> Marca
                                </label>
                                <select name="marca" class="form-select store-filter-control">
                                    <option value="">Todas las marcas</option>
                                    @foreach ($marcas as $marca)
                                        <option value="{{ $marca->id_marca }}"
                                            {{ request('marca') == $marca->id_marca ? 'selected' : '' }}>
                                            {{ $marca->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Orden --}}
                            <div class="store-filter-group">
                                <label class="store-filter-label">
                                    <i class="bi bi-arrow-repeat me-1"></i> Ordenar por
                                </label>
                                <select name="orden" class="form-select store-filter-control">
                                    <option value="recientes" {{ request('orden') == 'recientes' ? 'selected' : '' }}>
                                        Más recientes
                                    </option>
                                    <option value="precio_menor"
                                        {{ request('orden') == 'precio_menor' ? 'selected' : '' }}>
                                        Precio menor
                                    </option>
                                    <option value="precio_mayor"
                                        {{ request('orden') == 'precio_mayor' ? 'selected' : '' }}>
                                        Precio mayor
                                    </option>
                                    <option value="az" {{ request('orden') == 'az' ? 'selected' : '' }}>
                                        A-Z
                                    </option>
                                </select>
                            </div>

                            {{-- BOTONES --}}
                            <div class="d-grid gap-2 mt-4">
                                <button class="btn btn-store-primary">
                                    <i class="bi bi-funnel me-1"></i>
                                    Aplicar filtros
                                </button>
                                <a href="{{ route('tienda.productos.index') }}" class="btn btn-store-outline">
                                    Limpiar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- CONTENIDO --}}
        <div class="container py-4 py-lg-5">
            <div class="row g-4">

                {{-- FILTROS LATERAL (solo desktop) --}}
                <div class="col-12 col-lg-3 order-2 order-lg-1 d-none d-lg-block">
                    <div class="store-filter-card sticky-lg-top">
                        <div class="store-filter-header">
                            <div>
                                <h5 class="store-filter-title mb-1">Filtros</h5>
                                <p class="store-filter-subtitle mb-0">Refina tu búsqueda</p>
                            </div>
                            <i class="bi bi-sliders"></i>
                        </div>

                        <form action="{{ route('tienda.productos.index') }}" method="GET">
                            <input type="hidden" name="q" value="{{ request('q') }}">

                            {{-- Categoría --}}
                            <div class="store-filter-group">
                                <label class="store-filter-label">Categoría</label>
                                <select name="categoria" class="form-select store-filter-control">
                                    <option value="">Todas las categorías</option>
                                    @foreach ($categorias as $categoria)
                                        <option value="{{ $categoria->id_categoria }}"
                                            {{ request('categoria') == $categoria->id_categoria ? 'selected' : '' }}>
                                            {{ $categoria->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Marca --}}
                            <div class="store-filter-group">
                                <label class="store-filter-label">Marca</label>
                                <select name="marca" class="form-select store-filter-control">
                                    <option value="">Todas las marcas</option>
                                    @foreach ($marcas as $marca)
                                        <option value="{{ $marca->id_marca }}"
                                            {{ request('marca') == $marca->id_marca ? 'selected' : '' }}>
                                            {{ $marca->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Orden --}}
                            <div class="store-filter-group">
                                <label class="store-filter-label">Ordenar por</label>
                                <select name="orden" class="form-select store-filter-control">
                                    <option value="recientes" {{ request('orden') == 'recientes' ? 'selected' : '' }}>
                                        Más recientes
                                    </option>
                                    <option value="precio_menor"
                                        {{ request('orden') == 'precio_menor' ? 'selected' : '' }}>
                                        Precio menor
                                    </option>
                                    <option value="precio_mayor"
                                        {{ request('orden') == 'precio_mayor' ? 'selected' : '' }}>
                                        Precio mayor
                                    </option>
                                    <option value="az" {{ request('orden') == 'az' ? 'selected' : '' }}>
                                        A-Z
                                    </option>
                                </select>
                            </div>

                            <div class="d-grid gap-2 mt-4">
                                <button class="btn btn-store-primary">
                                    <i class="bi bi-funnel me-1"></i>
                                    Aplicar filtros
                                </button>
                                <a href="{{ route('tienda.productos.index') }}" class="btn btn-store-outline">
                                    Limpiar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- PRODUCTOS --}}
                <div class="col-12 col-lg-9 order-1 order-lg-2">
                    <div class="store-products-toolbar">
                        <div>
                            <h5 class="store-toolbar-title mb-1">Productos disponibles</h5>
                            <p class="store-toolbar-subtitle mb-0">
                                {{ $productos->count() }} productos encontrados
                            </p>
                        </div>
                        <a href="{{ route('tienda.categorias.index') }}"
                            class="btn btn-store-outline d-none d-md-inline-flex">
                            Ver categorías
                        </a>
                    </div>

                    {{-- GRID --}}
                    <div class="row g-3 g-md-4">
                        @forelse($productos as $producto)
                            @php
                                $imagen = $producto->imagenPrincipal
                                    ? asset('storage/' . $producto->imagenPrincipal->ruta)
                                    : asset('assets/img/no-image.png');
                            @endphp
                            <div class="col-6 col-md-4 col-xl-3">
                                <div class="store-product-card">
                                    <a href="{{ route('tienda.productos.show', $producto->slug) }}"
                                        class="store-product-image-wrap">
                                        <img src="{{ $imagen }}" alt="{{ $producto->nombre }}"
                                            class="store-product-image">
                                        <button type="button"
                                            class="store-product-heart js-favorite-btn {{ in_array($producto->id_producto, $favoritosIds ?? []) ? 'is-active' : '' }}"
                                            data-url="{{ route('tienda.favoritos.toggle', $producto->id_producto) }}"
                                            aria-label="Agregar a favoritos">

                                            <i
                                                class="bi {{ in_array($producto->id_producto, $favoritosIds ?? []) ? 'bi-heart-fill' : 'bi-heart' }}"></i>

                                        </button>
                                        @if ($producto->stock_actual <= 0)
                                            <span class="store-product-badge store-product-badge-muted">Agotado</span>
                                        @endif
                                    </a>
                                    <div class="store-product-body">
                                        <div class="store-product-meta">{{ $producto->marca?->nombre ?? 'Sin marca' }}
                                        </div>
                                        <a href="{{ route('tienda.productos.show', $producto->slug) }}"
                                            class="store-product-name">
                                            {{ $producto->nombre }}
                                        </a>
                                        <div class="store-product-category">
                                            {{ $producto->categoriaPrincipal?->nombre ?? 'Sin categoría' }}</div>
                                        <div class="store-product-footer">
                                            <div>
                                                <div class="store-product-price">
                                                    ₡{{ number_format($producto->precio, 2) }}</div>
                                                <small class="store-product-stock">Stock:
                                                    {{ $producto->stock_actual }}</small>
                                            </div>
                                            <a href="{{ route('tienda.productos.show', $producto->slug) }}"
                                                class="store-product-action">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="store-empty-products">
                                    <div class="store-empty-products-visual">
                                        <div class="store-empty-products-icon"><i class="bi bi-search"></i></div>
                                        <div class="store-empty-products-glow"></div>
                                    </div>
                                    <span class="store-empty-products-badge">Sin resultados</span>
                                    <h3 class="store-empty-products-title">No encontramos productos</h3>
                                    <p class="store-empty-products-text">
                                        No hay coincidencias con los filtros o términos de búsqueda seleccionados.
                                    </p>
                                    <div class="store-empty-products-actions">
                                        <a href="{{ route('tienda.productos.index') }}" class="btn btn-store-primary">
                                            <i class="bi bi-arrow-repeat me-2"></i>Ver todos
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterToggleBtn = document.getElementById('filterToggleBtn');
            const filterContent = document.getElementById('filterCollapsibleContent');
            const filterIcon = document.querySelector('.filter-icon');

            if (filterToggleBtn && filterContent) {
                filterToggleBtn.addEventListener('click', function() {
                    if (filterContent.style.display === 'none' || filterContent.style.display === '') {
                        filterContent.style.display = 'block';
                        if (filterIcon) filterIcon.classList.remove('bi-chevron-down');
                        if (filterIcon) filterIcon.classList.add('bi-chevron-up');
                    } else {
                        filterContent.style.display = 'none';
                        if (filterIcon) filterIcon.classList.remove('bi-chevron-up');
                        if (filterIcon) filterIcon.classList.add('bi-chevron-down');
                    }
                });
            }
        });
    </script>

@endsection
