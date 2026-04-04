@extends('admin.layouts.app')

@section('title', 'Pagos de Ventas Locales')

@section('content')
@php
    $pagosVentasLocales = [];

    $metodos = ['efectivo', 'tarjeta', 'sinpe', 'mixto'];
    $clientes = ['Cliente Mostrador', 'María López', 'Carlos Ramírez', 'Ana Gómez', 'Consumidor Final'];
    $cajeros = ['Caja 1', 'Caja 2', 'Admin General'];

    for ($i = 1; $i <= 70; $i++) {
        $metodo = $metodos[array_rand($metodos)];
        $monto = rand(5, 180) * 1000;

        $pagosVentasLocales[] = (object)[
            'id_pago_venta_local' => $i,
            'id_venta_local' => rand(100, 999),
            'numero_ticket' => 'TCK-' . str_pad($i, 6, '0', STR_PAD_LEFT),
            'cliente' => $clientes[array_rand($clientes)],
            'cajero' => $cajeros[array_rand($cajeros)],
            'metodo' => $metodo,
            'monto' => $monto,
            'referencia' => in_array($metodo, ['tarjeta', 'sinpe', 'mixto']) ? 'REF-' . rand(100000, 999999) : null,
            'fecha' => now()->subDays(rand(0, 20))->format('Y-m-d'),
            'hora' => now()->subMinutes(rand(0, 1440))->format('H:i'),
        ];
    }
@endphp

<div class="page-content">

    <div class="page-breadcrumb d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
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

    <div class="card card-index">
        <div class="card-body">

            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-3">
                <div>
                    <h4 class="mb-1 text-uppercase fw-bold">Pagos de Ventas Locales</h4>
                    <small class="text-muted">Registro de métodos de pago aplicados a ventas físicas</small>
                </div>
            </div>

            <hr class="my-2"/>

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
                        <input
                            type="text"
                            id="searchInput"
                            class="search-input"
                            placeholder="Buscar ticket, cliente, método, referencia..."
                            autocomplete="off"
                        >
                        <div class="search-actions">
                            <button class="btn-search-clear" id="clearSearch" type="button" title="Limpiar">
                                <i class="bx bx-x"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table id="tabla_index" class="table table-hover table-bordered align-middle text-center w-100">
                    <thead class="table-light">
                        <tr>
                            <th class="fw-semibold">ID</th>
                            <th class="fw-semibold">Ticket</th>
                            <th class="fw-semibold">Cliente</th>
                            <th class="fw-semibold">Cajero</th>
                            <th class="fw-semibold">Método</th>
                            <th class="fw-semibold">Monto</th>
                            <th class="fw-semibold">Referencia</th>
                            <th class="fw-semibold">Fecha</th>
                            <th class="fw-semibold">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($pagosVentasLocales as $pago)
                        <tr>
                            <td class="text-muted fw-semibold">{{ $pago->id_pago_venta_local }}</td>

                            <td class="text-start">
                                <div class="fw-semibold">{{ $pago->numero_ticket }}</div>
                                <small class="text-muted">ID Venta: {{ $pago->id_venta_local }}</small>
                            </td>

                            <td class="text-start">
                                <div class="fw-semibold">{{ $pago->cliente }}</div>
                            </td>

                            <td>
                                <span class="fw-semibold">{{ $pago->cajero }}</span>
                            </td>

                            <td>
                                @if($pago->metodo === 'efectivo')
                                    <span class="status-badge status-active">
                                        <i class="bx bx-money me-1"></i>Efectivo
                                    </span>
                                @elseif($pago->metodo === 'tarjeta')
                                    <span class="status-badge" style="background: rgba(13, 110, 253, .12); color: #0d6efd; border: 1px solid rgba(13, 110, 253, .20);">
                                        <i class="bx bx-credit-card me-1"></i>Tarjeta
                                    </span>
                                @elseif($pago->metodo === 'sinpe')
                                    <span class="status-badge" style="background: rgba(25, 135, 84, .12); color: #198754; border: 1px solid rgba(25, 135, 84, .20);">
                                        <i class="bx bx-mobile-alt me-1"></i>SINPE
                                    </span>
                                @else
                                    <span class="status-badge status-inactive">
                                        <i class="bx bx-layer me-1"></i>Mixto
                                    </span>
                                @endif
                            </td>

                            <td class="fw-semibold">
                                ₡{{ number_format($pago->monto, 0, '.', ',') }}
                            </td>

                            <td>
                                @if($pago->referencia)
                                    <span class="fw-semibold">{{ $pago->referencia }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            <td>
                                <div class="fw-semibold">{{ $pago->fecha }}</div>
                                <small class="text-muted">{{ $pago->hora }}</small>
                            </td>

                            <td>
                                <div class="d-flex justify-content-center gap-2 flex-wrap">
                                    <a class="btn-action btn-view" title="Ver"
                                       href="{{ route('admin.pagos-ventas-locales.show', $pago->id_pago_venta_local) }}">
                                        <i class="bx bx-show"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

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
</div>
@endsection