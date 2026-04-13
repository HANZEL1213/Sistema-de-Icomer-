{{-- resources/views/admin/pagos_pedidos/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Pagos de Pedidos')

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
                    <li class="breadcrumb-item active">Pagos de Pedidos</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card card-form">
        <div class="card-body">

            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-3">
                <div>
                    <h4 class="mb-1 text-uppercase fw-bold">Pagos de Pedidos</h4>
                    <small class="text-muted">Revisión y control de comprobantes enviados por pedidos online</small>
                </div>
            </div>

            <hr class="my-2" />

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
                            placeholder="Buscar pedido, cliente, comprobante, estado..." autocomplete="off">
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
                            <th class="fw-semibold">Pedido</th>
                            <th class="fw-semibold">Cliente</th>
                            <th class="fw-semibold">Método</th>
                            <th class="fw-semibold">Intento</th>
                            <th class="fw-semibold">Comprobante</th>
                            <th class="fw-semibold">Monto</th>
                            <th class="fw-semibold">Estado</th>
                            <th class="fw-semibold">Fecha envío</th>
                            <th class="fw-semibold">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($items as $pago)
                            <tr>
                                <td class="text-muted fw-semibold">{{ $pago->id_pago_pedido }}</td>

                                <td class="text-start">
                                    <div class="fw-semibold">
                                        {{ $pago->pedido?->numero_pedido ?: 'Sin pedido' }}
                                    </div>
                                    <small class="text-muted">ID Pedido: {{ $pago->id_pedido }}</small>
                                </td>

                                <td class="text-start">
                                    <div class="fw-semibold">
                                        {{ $pago->pedido?->nombre_cliente ?: 'Sin cliente' }}
                                    </div>

                                    @if ($pago->verificador)
                                        <small class="text-muted">
                                            Verificado por: {{ $pago->verificador->nombre }}
                                        </small>
                                    @endif
                                </td>

                                <td>
                                    @if ($pago->metodo === 'sinpe')
                                        <span class="status-badge status-active">
                                            <i class="bx bx-mobile-alt me-1"></i>{{ strtoupper($pago->metodo) }}
                                        </span>
                                    @else
                                        <span class="status-badge status-inactive">
                                            <i class="bx bx-credit-card me-1"></i>{{ strtoupper($pago->metodo ?: 'N/D') }}
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <span class="order-badge">{{ $pago->intento }}</span>
                                    @if ($pago->es_ultimo)
                                        <div><small class="text-muted">Último intento</small></div>
                                    @endif
                                </td>

                                <td class="text-start">
                                    <div class="fw-semibold">{{ $pago->numero_comprobante ?: 'Sin número' }}</div>
                                    <small class="text-muted">
                                        {{ $pago->ruta_comprobante ? 'Comprobante adjunto' : 'Sin comprobante adjunto' }}
                                    </small>
                                </td>

                                <td class="fw-semibold">
                                    {{ $pago->moneda === 'CRC' ? '₡' : $pago->moneda . ' ' }}
                                    {{ number_format((float) $pago->monto_reportado, 2, '.', ',') }}
                                </td>

                                <td>
                                    @if ($pago->estado === 'verificado')
                                        <span class="status-badge status-active">
                                            <i class="bx bx-check-circle me-1"></i>Verificado
                                        </span>
                                    @elseif($pago->estado === 'rechazado')
                                        <span class="status-badge status-inactive">
                                            <i class="bx bx-x-circle me-1"></i>Rechazado
                                        </span>
                                        @if ($pago->motivo_rechazo)
                                            <div><small class="text-muted">{{ $pago->motivo_rechazo }}</small></div>
                                        @endif
                                    @else
                                        <span class="status-badge"
                                            style="background: rgba(13, 110, 253, .12); color: #0d6efd; border: 1px solid rgba(13, 110, 253, .20);">
                                            <i class="bx bx-time-five me-1"></i>Enviado
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <div class="fw-semibold">
                                        {{ $pago->enviado_en?->format('d/m/Y') ?: '—' }}
                                    </div>
                                    <small class="text-muted">
                                        {{ $pago->enviado_en?->format('H:i') ?: '—' }}
                                    </small>
                                </td>

                                <td>
                                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                                        <a class="btn-action btn-view" title="Ver pago"
                                            href="{{ route('admin.pagos-pedidos.show', $pago->id_pago_pedido) }}">
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
                        <nav aria-label="Page navigation">
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