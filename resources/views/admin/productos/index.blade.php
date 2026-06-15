{{-- resources/views/admin/productos/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Productos')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/modules/productos.css') }}">
    {{-- Breadcrumb --}}
    <div class="page-breadcrumb d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}">
                            <i class="bx bx-home-alt"></i>
                        </a>
                    </li>
                    <li class="breadcrumb-item active">Productos</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Card --}}
    <div class="card card-form">
        <div class="card-body">

            {{-- Header --}}
            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-3">
                <div>
                    <h4 class="mb-1 text-uppercase fw-bold">Productos</h4>
                    <small class="text-muted">Gestión de productos, stock y estado</small>
                </div>

                <a href="{{ route('admin.productos.create') }}"
                    class="btn btn-nuevo d-inline-flex align-items-center gap-2">
                    <i class="bx bx-plus-circle"></i>
                    <span>Nuevo</span>
                </a>
            </div>

            <hr class="my-2" />

            {{-- Top bar --}}
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">

                {{-- Por página --}}
                <div class="pagination-perpage-top">
                    <div class="input-group input-group-perpage">
                        <span class="input-group-text bg-transparent border-end-0">
                            <i class="bx bx-list-ul"></i>
                        </span>
                        <select class="form-select form-select-sm border-start-0" id="perPageSelect">
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                        <span class="input-group-text bg-transparent border-start-0">
                            <span class="perpage-label">por página</span>
                        </span>
                    </div>
                </div>

                {{-- Buscador --}}
                <div class="search-container ms-auto">
                    <div class="search-box" role="search">
                        <i class="bx bx-search search-icon"></i>
                        <input type="text" id="searchInput" class="search-input"
                            placeholder="Buscar producto, slug, código, SKU, marca, categoría..." autocomplete="off">
                        <div class="search-actions">
                            <button class="btn-search-clear" id="clearSearch" type="button" title="Limpiar">
                                <i class="bx bx-x"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabla --}}

            <div class="table-responsive">
                <table id="tabla_index" class="table table-hover table-bordered align-middle text-center w-100">
                    <thead class="table-light">
                        <tr>
                            <th class="fw-semibold">ID</th>
                            <th class="fw-semibold">Producto</th>
                            <th class="fw-semibold">Marca</th>
                            <th class="fw-semibold">Categoría</th>
                            <th class="fw-semibold">Precio</th>
                            <th class="fw-semibold">Promoción</th>
                            <th class="fw-semibold">Stock</th>
                            <th class="fw-semibold">Estado</th>
                            <th class="fw-semibold">Destacado</th>
                            <th class="fw-semibold">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($items as $item)
                            @php
                                $imagenProducto = $item->imagenPrincipal?->ruta
                                    ? (\Illuminate\Support\Str::startsWith($item->imagenPrincipal->ruta, [
                                        'http://',
                                        'https://',
                                    ])
                                        ? $item->imagenPrincipal->ruta
                                        : asset('storage/' . $item->imagenPrincipal->ruta))
                                    : null;

                                $variantesActivas = $item->variantesActivas ?? collect();

                                $variantePrincipalIndex = $item->variantePrincipal ?: $variantesActivas->first();

                                $variantesConDescuento = $variantesActivas->filter(
                                    fn($variante) => $variante->descuento_activo && $variante->precio_descuento,
                                );

                                $variantesPromocionActiva = $variantesConDescuento->filter(function ($variante) {
                                    if (!$variante->descuento_inicio || !$variante->descuento_fin) {
                                        return false;
                                    }

                                    return now()->gte($variante->descuento_inicio) &&
                                        now()->lte($variante->descuento_fin);
                                });

                                $variantesPromocionProgramada = $variantesConDescuento->filter(function ($variante) {
                                    return $variante->descuento_inicio && now()->lt($variante->descuento_inicio);
                                });

                                $stockReal = $item->usa_variantes
                                    ? $variantesActivas->sum('stock_actual')
                                    : $item->stock_actual;
                            @endphp

                            <tr>
                                {{-- ID --}}
                                <td class="text-muted fw-semibold">{{ $item->id_producto }}</td>

                                {{-- PRODUCTO --}}
                                <td class="text-start">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="flex-shrink-0">
                                            @if ($imagenProducto)
                                                <img src="{{ $imagenProducto }}" class="img-thumb" loading="lazy"
                                                    alt="{{ $item->nombre }}">
                                            @else
                                                <div
                                                    class="img-thumb d-flex align-items-center justify-content-center bg-light text-muted small border">
                                                    <i class="bx bx-image"></i>
                                                </div>
                                            @endif
                                        </div>

                                        <div>
                                            <div class="fw-semibold">{{ $item->nombre }}</div>

                                            @if ($item->usa_variantes)
                                                <small class="text-primary">
                                                    <i class="bx bx-git-branch"></i>
                                                    Con variantes
                                                </small>
                                            @else
                                                <small class="text-muted">
                                                    {{ $item->marca?->nombre ?: 'Sin marca' }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- MARCA --}}
                                <td>
                                    {{ $item->marca?->nombre ?: '—' }}
                                </td>

                                {{-- CATEGORÍA --}}
                                <td>
                                    {{ $item->categoriaPrincipal?->nombre ?: '—' }}
                                </td>

                                {{-- PRECIO --}}

                                <td class="fw-semibold">
                                    @if ($item->usa_variantes)

                                        @if ($variantePrincipalIndex)
                                            @if ($variantePrincipalIndex->descuento_activo && $variantePrincipalIndex->precio_descuento)
                                                @php
                                                    $ahorroVariante =
                                                        $variantePrincipalIndex->precio -
                                                        $variantePrincipalIndex->precio_descuento;

                                                    $porcentajeVariante =
                                                        $variantePrincipalIndex->precio > 0
                                                            ? round(
                                                                ($ahorroVariante / $variantePrincipalIndex->precio) *
                                                                    100,
                                                            )
                                                            : 0;
                                                @endphp

                                                <div class="index-price-discount">

                                                    <span class="index-discount-badge">
                                                        -{{ $porcentajeVariante }}%
                                                    </span>

                                                    <span class="index-old-price">
                                                        ₡{{ number_format((float) $variantePrincipalIndex->precio, 0, ',', '.') }}
                                                    </span>

                                                    <span class="index-current-price">
                                                        ₡{{ number_format((float) $variantePrincipalIndex->precio_descuento, 0, ',', '.') }}
                                                    </span>

                                                </div>
                                            @else
                                                <span class="index-normal-price">
                                                    ₡{{ number_format((float) $variantePrincipalIndex->precio, 0, ',', '.') }}
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-muted">
                                                Sin variante principal
                                            </span>
                                        @endif
                                    @else
                                        @if ($item->descuento_activo && $item->precio_descuento)
                                            @php
                                                $ahorro = $item->precio - $item->precio_descuento;

                                                $porcentaje =
                                                    $item->precio > 0 ? round(($ahorro / $item->precio) * 100) : 0;
                                            @endphp

                                            <div class="index-price-discount">

                                                <span class="index-discount-badge">
                                                    -{{ $porcentaje }}%
                                                </span>

                                                <span class="index-old-price">
                                                    ₡{{ number_format((float) $item->precio, 0, ',', '.') }}
                                                </span>

                                                <span class="index-current-price">
                                                    ₡{{ number_format((float) $item->precio_descuento, 0, ',', '.') }}
                                                </span>

                                            </div>
                                        @else
                                            <span class="index-normal-price">
                                                ₡{{ number_format((float) $item->precio, 0, ',', '.') }}
                                            </span>
                                        @endif

                                    @endif
                                </td>
                                {{-- PROMOCIÓN --}}
                                <td>
                                    @if ($item->usa_variantes)
                                        @if ($variantesPromocionActiva->count() > 0)
                                            <span class="status-badge status-active">
                                                <i class="bx bx-purchase-tag-alt me-1"></i>
                                                Activa
                                            </span>

                                            <div class="small text-muted mt-1">
                                                {{ $variantesPromocionActiva->count() }}
                                                {{ $variantesPromocionActiva->count() === 1 ? 'variante' : 'variantes' }}
                                            </div>
                                        @elseif ($variantesPromocionProgramada->count() > 0)
                                            <span class="status-badge status-inactive">
                                                <i class="bx bx-time-five me-1"></i>
                                                Programada
                                            </span>

                                            <div class="small text-muted mt-1">
                                                {{ $variantesPromocionProgramada->count() }}
                                                {{ $variantesPromocionProgramada->count() === 1 ? 'variante' : 'variantes' }}
                                            </div>
                                        @else
                                            <span class="status-badge status-inactive">
                                                <i class="bx bx-x-circle me-1"></i>
                                                Desactivada
                                            </span>
                                        @endif
                                    @else
                                        @if ($item->descuento_activo && $item->precio_descuento && $item->descuento_inicio && $item->descuento_fin)
                                            @php
                                                $activaAhora =
                                                    now()->gte($item->descuento_inicio) &&
                                                    now()->lte($item->descuento_fin);
                                            @endphp

                                            @if ($activaAhora)
                                                <span class="status-badge status-active">
                                                    <i class="bx bx-purchase-tag-alt me-1"></i>
                                                    Activa
                                                </span>
                                            @else
                                                <span class="status-badge status-inactive">
                                                    <i class="bx bx-time-five me-1"></i>
                                                    Programada
                                                </span>
                                            @endif
                                        @else
                                            <span class="status-badge status-inactive">
                                                <i class="bx bx-x-circle me-1"></i>
                                                Desactivada
                                            </span>
                                        @endif
                                    @endif
                                </td>

                                {{-- STOCK --}}
                                <td>
                                    @if ((int) $stockReal > 0)
                                        <span class="order-badge">
                                            {{ $stockReal }}
                                        </span>
                                    @else
                                        <span class="status-badge status-inactive">
                                            <i class="bx bx-package me-1"></i>0
                                        </span>
                                    @endif
                                </td>

                                {{-- ESTADO --}}
                                <td>
                                    @if ($item->activo)
                                        <span class="status-badge status-active">
                                            <i class="bx bx-check-circle me-1"></i>Activo
                                        </span>
                                    @else
                                        <span class="status-badge status-inactive">
                                            <i class="bx bx-x-circle me-1"></i>Inactivo
                                        </span>
                                    @endif
                                </td>

                                {{-- DESTACADO --}}
                                <td>
                                    @if ($item->destacado)
                                        <span class="status-badge status-active">
                                            <i class="bx bx-star me-1"></i>Destacado
                                        </span>
                                    @else
                                        <span class="status-badge status-inactive">
                                            <i class="bx bx-package me-1"></i>Normal
                                        </span>
                                    @endif
                                </td>

                                {{-- ACCIONES --}}
                                <td>
                                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                                        <a class="btn-action btn-view"
                                            href="{{ route('admin.productos.show', $item->id_producto) }}"
                                            title="Ver">
                                            <i class="bx bx-show"></i>
                                        </a>

                                        <a class="btn-action btn-edit"
                                            href="{{ route('admin.productos.edit', $item->id_producto) }}"
                                            title="Editar">
                                            <i class="bx bx-edit"></i>
                                        </a>

                                        <form action="{{ route('admin.productos.destroy', $item->id_producto) }}"
                                            method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')

                                            <button type="button" class="btn-action btn-delete btn-delete-modal"
                                                data-clave="Producto" data-valor="{{ $item->nombre }}" title="Eliminar">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{-- Paginación --}}
            <div class="custom-pagination-container mt-3">
                <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3 py-2">
                    <div class="pagination-info">
                        <span class="text-muted">Mostrando</span>
                        <span class="fw-semibold text-dark ms-1" id="pagination-from">0</span>
                        <span class="text-muted">a</span>
                        <span class="fw-semibold text-dark mx-1" id="pagination-to">0</span>
                        <span class="text-muted">de</span>
                        <span class="fw-semibold text-dark ms-1" id="pagination-total">0</span>
                        <span class="text-muted">resultados</span>
                    </div>

                    <div class="pagination-controls">
                        <nav aria-label="Page navigation">
                            <ul class="pagination pagination-modern mb-0">
                                <li class="page-item disabled" id="pagination-prev">
                                    <a class="page-link" href="#" aria-label="Previous">
                                        <i class="bx bx-chevron-left"></i>
                                    </a>
                                </li>

                                {{-- números generados por JS --}}

                                <li class="page-item disabled" id="pagination-next">
                                    <a class="page-link" href="#" aria-label="Next">
                                        <i class="bx bx-chevron-right"></i>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection
