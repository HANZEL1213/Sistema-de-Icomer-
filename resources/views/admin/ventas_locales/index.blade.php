{{-- resources/views/admin/ventas_locales/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Ventas Locales')

@section('content')

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
                    <li class="breadcrumb-item active">Ventas Locales</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card card-form">
        <div class="card-body">

            {{-- Header --}}
            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-3">
                <div>
                    <h4 class="mb-1 text-uppercase fw-bold">Ventas Locales</h4>
                    <small class="text-muted">Registro y control de ventas realizadas en el local</small>
                </div>

                <a href="{{ route('admin.ventas-locales.create') }}"
                    class="btn btn-nuevo d-inline-flex align-items-center gap-2">
                    <i class="bx bx-plus-circle"></i>
                    <span>Nueva</span>
                </a>
            </div>

            <hr class="my-2" />

            {{-- Top bar --}}
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">

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

                <div class="search-container ms-auto">
                    <div class="search-box" role="search">
                        <i class="bx bx-search search-icon"></i>
                        <input type="text" id="searchInput" class="search-input"
                            placeholder="Buscar ticket, cliente, teléfono, cajero, método..." autocomplete="off">
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
                <table id="tabla_index" data-order-column="7"
                    class="table table-hover table-bordered align-middle text-center w-100">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>N° Ticket</th>
                            <th>Cliente</th>
                            <th>Cajero</th>
                         <th>Ítems</th>
<th>Promos</th>
<th>Pago</th>
<th>Total</th>
                            <th>Fecha</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>

               <tbody>
    @foreach ($items as $item)
        @php
            $pagos = $item->pagos ?? collect();
            $detalle = $item->detalle ?? collect();

            $montoPagado = (float) $pagos->sum('monto');
            $totalVenta = (float) $item->total;

            $estaPagado = abs($montoPagado - $totalVenta) < 0.01 || $montoPagado > $totalVenta;

            $pagoBadgeClass = $estaPagado ? 'status-active' : 'status-warning';
            $pagoIcon = $estaPagado ? 'bx-check-circle' : 'bx-time-five';
            $pagoLabel = $estaPagado ? 'PAGADO' : 'INCOMPLETO';

            $metodosUsados = $pagos
                ->pluck('metodo')
                ->filter()
                ->unique()
                ->map(fn($m) => strtoupper($m))
                ->implode(' + ');

            $metodosUsados = $metodosUsados ?: 'SIN PAGO';

            $clienteNombre = $item->nombre_cliente ?: 'Cliente no registrado';
            $clienteTelefono = $item->telefono_cliente ?: 'Sin teléfono';
            $cantidadItems = (int) ($item->cantidad_items ?? 0);
            $notas = $item->notas ?: 'Sin nota';

            $tienePromociones = $detalle->contains(function ($d) {
                return $d->promocion_aplicada ?? false;
            });
        @endphp

        <tr>
            <td class="fw-semibold text-muted">
                {{ $item->id_venta_local }}
            </td>

            <td class="text-start">
                <div class="fw-semibold">
                    {{ $item->numero_ticket }}
                </div>

                <small class="text-muted">
                    Subtotal: ₡{{ number_format($item->subtotal, 2) }}
                </small>

                @if ((float) $item->descuento > 0)
                    <div class="small text-success">
                        Descuento: ₡{{ number_format($item->descuento, 2) }}
                    </div>
                @endif

                <div class="small text-muted">
                    Nota: {{ $notas }}
                </div>
            </td>

            <td class="text-start">
                <div class="fw-semibold">
                    {{ $clienteNombre }}
                </div>

                <small class="text-muted">
                    {{ $clienteTelefono }}
                </small>
            </td>

            <td>
                <span class="status-badge status-info">
                    <i class="bx bx-user me-1"></i>
                    {{ strtoupper($item->cajero?->nombre ?? 'SIN CAJERO') }}
                </span>
            </td>

            <td>
                <span class="fw-semibold">
                    {{ $cantidadItems }}
                </span>

                <div class="small text-muted">
                    producto(s)
                </div>
            </td>

            {{-- NUEVA COLUMNA PROMOCIONES --}}
            <td>
                @if($tienePromociones)
                    <span class="status-badge status-danger">
                        <i class="bx bx-purchase-tag-alt me-1"></i>
                        PROMO
                    </span>
                @else
                    <span class="text-muted">—</span>
                @endif
            </td>

            <td>
                <span class="status-badge {{ $pagoBadgeClass }}">
                    <i class="bx {{ $pagoIcon }} me-1"></i>
                    {{ $pagoLabel }}
                </span>

                <div class="small text-muted mt-1">
                    {{ $metodosUsados }}
                </div>
            </td>

            <td class="fw-bold">
                ₡{{ number_format($item->total, 2) }}
            </td>

            <td data-order="{{ optional($item->created_at)->format('Y-m-d H:i:s') }}">
                <div class="fw-semibold">
                    {{ optional($item->created_at)->format('d/m/Y') }}
                </div>

                <small class="text-muted">
                    {{ optional($item->created_at)->format('H:i') }}
                </small>
            </td>

            <td>
                <div class="d-flex justify-content-center gap-2 flex-wrap">

                    <a class="btn-action btn-view"
                        title="Ver"
                        href="{{ route('admin.ventas-locales.show', $item->id_venta_local) }}">
                        <i class="bx bx-show"></i>
                    </a>

                    <a class="btn-action btn-edit"
                        title="Imprimir Ticket"
                        href="{{ route('admin.ventas-locales.ticket', $item->id_venta_local) }}"
                        target="_blank">
                        <i class="bx bx-printer"></i>
                    </a>

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
