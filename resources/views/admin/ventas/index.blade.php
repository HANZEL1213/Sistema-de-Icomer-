{{-- resources/views/admin/ventas/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Reporte de ventas')

@section('content')

    <div class="page-breadcrumb d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}">
                            <i class="bx bx-home-alt"></i>
                        </a>
                    </li>
                    <li class="breadcrumb-item active">Reporte de ventas</li>
                </ol>
            </nav>
        </div>
    </div>


    <div class="card card-form">
        <div class="card-body">

            {{-- Header --}}
            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-3">
                <div>
                    <h4 class="mb-1 text-uppercase fw-bold">Reporte de ventas</h4>
                    <small class="text-muted">Consolidado (online + físicas)</small>
                </div>

                <button class="btn btn-nuevo d-inline-flex align-items-center gap-2" type="button">
                    <i class="bx bx-download"></i>
                    <span>Exportar</span>
                </button>
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
                            placeholder="Buscar ID, canal, referencia, cliente, cajero..." autocomplete="off">
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
                <table id="tabla_index" data-order-column="9"
                    class="table table-hover table-bordered align-middle text-center w-100">
                    <thead class="table-light">
                        <tr>
                            <th class="fw-semibold">ID</th>
                            <th class="fw-semibold">Canal</th>
                            <th class="fw-semibold">Referencia</th>
                            <th class="fw-semibold">Cliente / Cajero</th>
                            <th class="fw-semibold">Subtotal</th>
                            <th class="fw-semibold">Descuento</th>
                            <th class="fw-semibold">Total</th>
                            <th class="fw-semibold">Promos</th>
                            <th class="fw-semibold">Cupón</th>
                            <th class="fw-semibold">Fecha</th>
                            <th class="fw-semibold">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($items as $item)
                            @php
                                $esOnline = $item->canal === 'online';

                                $referencia = $esOnline
                                    ? ($item->pedido?->numero_pedido ?:
                                    'Sin pedido')
                                    : ($item->ventaLocal?->numero_ticket ?:
                                    'Sin ticket');

                                $nombrePrincipal = $esOnline
                                    ? ($item->pedido?->nombre_cliente ?:
                                    ($item->pedido?->usuario?->nombre ?:
                                    'Sin cliente'))
                                    : ($item->ventaLocal?->nombre_cliente ?:
                                    'Cliente mostrador');

                                $subtexto = $esOnline
                                    ? ($item->pedido?->usuario?->correo ?:
                                    'Venta online')
                                    : ($item->ventaLocal?->cajero?->nombre
                                        ? 'Cajero: ' . $item->ventaLocal->cajero->nombre
                                        : 'Venta local');

                                $subtotal = $esOnline
                                    ? (float) ($item->pedido?->subtotal ?? 0)
                                    : (float) ($item->ventaLocal?->subtotal ?? 0);

                                $total = $esOnline
                                    ? (float) ($item->pedido?->total ?? 0)
                                    : (float) ($item->ventaLocal?->total ?? 0);

                                $detalleVenta = $esOnline
                                    ? $item->pedido?->detalle ?? collect()
                                    : $item->ventaLocal?->detalle ?? collect();

                                $descuentoProductos = $detalleVenta->sum(function ($d) {
                                    $precioOriginal = (float) ($d->precio_original ?? ($d->precio_unitario ?? 0));
                                    $precioVenta = (float) ($d->precio_unitario ?? 0);
                                    $cantidad = (int) ($d->cantidad ?? 0);

                                    return max(0, $precioOriginal - $precioVenta) * $cantidad;
                                });

                                $descuentoCupon = $esOnline
                                    ? (float) ($item->pedido?->descuento ?? 0)
                                    : (float) ($item->ventaLocal?->descuento ?? 0);

                                $descuento = $descuentoProductos + $descuentoCupon;

                                $tienePromociones = $detalleVenta->contains(function ($d) {
                                    return $d->promocion_aplicada ?? false;
                                });

                                $codigoCupon = $esOnline ? $item->pedido?->cupon?->codigo ?? null : null;
                            @endphp

                            <tr>
                                <td class="text-muted fw-semibold">
                                    {{ $item->id_venta }}
                                </td>

                                <td class="fw-semibold">
                                    @if ($esOnline)
                                        <span class="status-badge status-active">
                                            <i class="bx bx-globe me-1"></i>Online
                                        </span>
                                    @else
                                        <span class="status-badge status-inactive">
                                            <i class="bx bx-store me-1"></i>Física
                                        </span>
                                    @endif
                                </td>

                                <td class="text-start">
                                    <div class="fw-semibold">
                                        {{ $referencia }}
                                    </div>
                                </td>

                                <td class="text-start">
                                    <div class="fw-semibold">
                                        {{ $nombrePrincipal }}
                                    </div>

                                    <small class="text-muted">
                                        {{ $subtexto }}
                                    </small>
                                </td>

                                <td class="fw-semibold">
                                    ₡{{ number_format($subtotal, 2) }}
                                </td>

                                <td class="fw-semibold text-success">
                                    ₡{{ number_format($descuento, 2) }}
                                </td>

                                <td class="fw-semibold">
                                    ₡{{ number_format($total, 2) }}
                                </td>

                                <td>
                                    @if ($tienePromociones)
                                        <span class="status-badge status-danger">
                                            <i class="bx bx-purchase-tag-alt me-1"></i>
                                            PROMO
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>

                                <td>
                                    @if ($codigoCupon)
                                        <span class="status-badge status-primary">
                                            <i class="bx bx-purchase-tag me-1"></i>CUPÓN
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>

                                <td data-order="{{ optional($item->created_at)->format('Y-m-d H:i:s') }}">
                                    <div class="fw-semibold">
                                        {{ optional($item->created_at)->format('d/m/Y') ?: '—' }}
                                    </div>

                                    <small class="text-muted">
                                        {{ optional($item->created_at)->format('H:i') ?: '—' }}
                                    </small>
                                </td>

                                <td>
                                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                                        @if ($esOnline && $item->pedido)
                                            <a class="btn-action btn-view"
                                                href="{{ route('admin.pedidos.show', $item->pedido->id_pedido) }}">
                                                <i class="bx bx-show"></i>
                                            </a>
                                        @elseif(!$esOnline && $item->ventaLocal)
                                            <a class="btn-action btn-view"
                                                href="{{ route('admin.ventas-locales.show', $item->ventaLocal->id_venta_local) }}">
                                                <i class="bx bx-show"></i>
                                            </a>
                                        @endif
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


@push('scripts')
    <script src="{{ asset('assets/js/modules/ventas.js') }}"></script>
@endpush
