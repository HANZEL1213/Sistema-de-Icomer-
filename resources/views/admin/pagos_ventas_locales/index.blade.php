@extends('admin.layouts.app')

@section('title', 'Pagos de Ventas Locales')

@section('content')

    {{-- Breadcrumb --}}
    <div class="page-breadcrumb d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav>
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}">
                            <i class="bx bx-home-alt"></i>
                        </a>
                    </li>
                    <li class="breadcrumb-item active">Pagos de Ventas Locales</li>
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
                    <h4 class="mb-1 text-uppercase fw-bold">Pagos de Ventas Locales</h4>
                    <small class="text-muted">
                        Registro de pagos aplicados a ventas físicas en caja
                    </small>
                </div>
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
                        <input type="text"
                               id="searchInput"
                               class="search-input"
                               placeholder="Buscar ticket, cliente, método, referencia..."
                               autocomplete="off">
                        <div class="search-actions">
                            <button class="btn-search-clear" id="clearSearch" type="button">
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
                            <th>ID</th>
                            <th>Venta</th>
                            <th>Cliente</th>
                            <th>Método</th>
                            <th>Monto</th>
                            <th>Referencia</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($items as $pago)

                            <tr>
                                <td class="text-muted fw-semibold">
                                    {{ $pago->id_pago_venta_local }}
                                </td>

                                {{-- Venta --}}
                                <td class="text-start">
                                    <div class="fw-semibold">
                                        {{ $pago->ventaLocal?->numero_ticket ?? '—' }}
                                    </div>

                                    <small class="text-muted d-block">
                                        ID Venta: {{ $pago->id_venta_local }}
                                    </small>

                                    <div class="small text-muted">
                                        Total: ₡{{ number_format((float) $pago->ventaLocal?->total, 2, '.', ',') }}
                                    </div>
                                </td>

                                {{-- Cliente --}}
                                <td class="text-start">
                                    <div class="fw-semibold">
                                        {{ $pago->ventaLocal?->nombre_cliente ?: 'Consumidor final' }}
                                    </div>
                                </td>

                                {{-- Método --}}
                                <td>
                                    @php
                                        $badge = match ($pago->metodo) {
                                            'efectivo' => 'status-active',
                                            'tarjeta' => 'status-info',
                                            'sinpe' => 'status-primary',
                                            default => 'status-inactive',
                                        };

                                        $icon = match ($pago->metodo) {
                                            'efectivo' => 'bx-money',
                                            'tarjeta' => 'bx-credit-card',
                                            'sinpe' => 'bx-mobile-alt',
                                            default => 'bx-layer',
                                        };
                                    @endphp

                                    <span class="status-badge {{ $badge }}">
                                        <i class="bx {{ $icon }} me-1"></i>
                                        {{ strtoupper($pago->metodo) }}
                                    </span>
                                </td>

                                {{-- Monto --}}
                                <td class="fw-semibold">
                                    ₡{{ number_format((float) $pago->monto, 2, '.', ',') }}
                                </td>

                                {{-- Referencia --}}
                                <td>
                                    {{ $pago->referencia ?: '—' }}
                                </td>

                                {{-- Fecha --}}
                                <td>
                                    <div class="fw-semibold">
                                        {{ optional($pago->created_at)->format('Y-m-d') }}
                                    </div>
                                    <div class="text-muted small">
                                        {{ optional($pago->created_at)->format('H:i') }}
                                    </div>
                                </td>

                                {{-- Acciones --}}
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('admin.pagos-ventas-locales.show', $pago->id_pago_venta_local) }}"
                                           class="btn-action btn-view"
                                           title="Ver pago">
                                            <i class="bx bx-show"></i>
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
                        <nav>
                            <ul class="pagination pagination-modern mb-0">
                                <li class="page-item disabled" id="pagination-prev">
                                    <a class="page-link" href="#">
                                        <i class="bx bx-chevron-left"></i>
                                    </a>
                                </li>

                                <li class="page-item disabled" id="pagination-next">
                                    <a class="page-link" href="#">
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