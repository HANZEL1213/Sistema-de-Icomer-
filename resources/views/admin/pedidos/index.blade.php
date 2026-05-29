{{-- resources/views/admin/pedidos/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Pedidos (Online)')

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
                    <li class="breadcrumb-item active">Pedidos (Online)</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="orders-admin-hero">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h4>PEDIDOS ONLINE</h4>
                <small>Vista visual y tabla para gestionar pedidos por orden de llegada.</small>
            </div>

            <div class="orders-view-toggle">
                <button type="button" class="orders-view-btn active" data-view="board">
                    <i class="bx bx-grid-alt me-1"></i> Visual
                </button>
                <button type="button" class="orders-view-btn" data-view="table">
                    <i class="bx bx-table me-1"></i> Tabla
                </button>
            </div>
        </div>
    </div>

    <div class="card card-form">
        <div class="card-body">

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
                            placeholder="Buscar pedido, cliente, teléfono, correo, estado..." autocomplete="off">
                        <div class="search-actions">
                            <button class="btn-search-clear" id="clearSearch" type="button" title="Limpiar">
                                <i class="bx bx-x"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="orders-filter-bar">
                <button type="button" class="orders-filter-btn active" data-status="all">
                    <i class="bx bx-list-ul"></i> Todos
                </button>
                <button type="button" class="orders-filter-btn" data-status="pendiente_pago">
                    <i class="bx bx-time-five"></i> Pendiente pago
                </button>
                <button type="button" class="orders-filter-btn" data-status="en_revision">
                    <i class="bx bx-search-alt"></i> En revisión
                </button>
                <button type="button" class="orders-filter-btn" data-status="pagado_verificado">
                    <i class="bx bx-check-circle"></i> Pagado
                </button>
                <button type="button" class="orders-filter-btn" data-status="preparando">
                    <i class="bx bx-package"></i> Preparando
                </button>
                <button type="button" class="orders-filter-btn" data-status="enviado">
                    <i class="bx bx-send"></i> Enviado
                </button>
                <button type="button" class="orders-filter-btn" data-status="entregado">
                    <i class="bx bx-check-shield"></i> Entregado
                </button>
                <button type="button" class="orders-filter-btn" data-status="cancelado">
                    <i class="bx bx-block"></i> Cancelado
                </button>
            </div>

            {{-- VISTA VISUAL --}}
            <div class="orders-board-view">
                <div class="orders-board">

                    @forelse ($items as $item)
                        @php
                            $estadoBadgeClass = match ($item->estado) {
                                'pendiente_pago' => 'status-inactive',
                                'en_revision' => 'status-warning',
                                'pagado_verificado' => 'status-active',
                                'preparando' => 'status-info',
                                'enviado' => 'status-primary',
                                'entregado' => 'status-dark',
                                'rechazado' => 'status-danger',
                                'cancelado' => 'status-danger',
                                default => 'status-inactive',
                            };

                            $estadoIcon = match ($item->estado) {
                                'pendiente_pago' => 'bx-time-five',
                                'en_revision' => 'bx-search-alt',
                                'pagado_verificado' => 'bx-check-circle',
                                'preparando' => 'bx-package',
                                'enviado' => 'bx-send',
                                'entregado' => 'bx-check-shield',
                                'rechazado' => 'bx-x-circle',
                                'cancelado' => 'bx-block',
                                default => 'bx-info-circle',
                            };

                            $estadoLabel = strtoupper(str_replace('_', ' ', $item->estado));

                            $pago = $item->pagoUltimo;

                            $pagoBadgeClass = $pago
                                ? match ($pago->estado) {
                                    'enviado' => 'status-warning',
                                    'verificado' => 'status-active',
                                    'rechazado' => 'status-danger',
                                    default => 'status-inactive',
                                }
                                : 'status-inactive';

                            $pagoIcon = $pago
                                ? match ($pago->estado) {
                                    'enviado' => 'bx-upload',
                                    'verificado' => 'bx-check-circle',
                                    'rechazado' => 'bx-x-circle',
                                    default => 'bx-info-circle',
                                }
                                : 'bx-minus-circle';

                            $pagoLabel = $pago ? strtoupper($pago->estado) : 'SIN PAGO';

                            $searchText = strtolower(
                                $item->numero_pedido .
                                    ' ' .
                                    $item->nombre_cliente .
                                    ' ' .
                                    $item->telefono_cliente .
                                    ' ' .
                                    $item->correo_cliente .
                                    ' ' .
                                    $item->estado .
                                    ' ' .
                                    $pagoLabel,
                            );
                        @endphp

                        <div class="order-card order-filter-item order-board-item" data-status="{{ $item->estado }}"
                            data-search="{{ $searchText }}">

                            <div class="order-card-top">
                                <div>
                                    <div class="order-number">
                                        <i class="bx bx-receipt me-1"></i>
                                        {{ $item->numero_pedido }}
                                    </div>
                                    <div class="order-time">
                                        {{ optional($item->created_at)->format('Y-m-d H:i') }}
                                        · {{ optional($item->created_at)->diffForHumans() }}
                                    </div>
                                </div>

                                <div class="order-total">
                                    ₡{{ number_format((float) $item->total, 2, '.', ',') }}
                                </div>
                            </div>

                            <div class="order-grid">
                                <div>
                                    <span class="order-label">Cliente</span>
                                    <div class="order-main-text">{{ $item->nombre_cliente }}</div>
                                    <div class="order-sub-text">
                                        {{ $item->telefono_cliente }}
                                        · {{ $item->correo_cliente ?: 'Sin correo' }}
                                    </div>
                                </div>

                                <div>
                                    <span class="order-label">Entrega</span>
                                    @if ($item->tipo_entrega === 'envio')
                                        <span class="status-badge status-active">
                                            <i class="bx bx-map me-1"></i> ENVÍO
                                        </span>
                                        <div class="order-sub-text mt-1">
                                            {{ $item->provincia_envio ?: '—' }} / {{ $item->canton_envio ?: '—' }}
                                        </div>
                                    @else
                                        <span class="status-badge status-inactive">
                                            <i class="bx bx-store me-1"></i> RETIRO
                                        </span>
                                    @endif
                                </div>

                                <div>
                                    <span class="order-label">Estado</span>
                                    <span class="status-badge {{ $estadoBadgeClass }}">
                                        <i class="bx {{ $estadoIcon }} me-1"></i>{{ $estadoLabel }}
                                    </span>
                                </div>

                                <div>
                                    <span class="order-label">Pago</span>
                                    <span class="status-badge {{ $pagoBadgeClass }}">
                                        <i class="bx {{ $pagoIcon }} me-1"></i>{{ $pagoLabel }}
                                    </span>
                                    @if ($pago && $pago->numero_comprobante)
                                        <div class="order-sub-text mt-1">
                                            {{ strtoupper($pago->metodo) }} · {{ $pago->numero_comprobante }}
                                        </div>
                                    @endif
                                </div>

                                <div class="order-actions">
                                    <a href="{{ route('admin.pedidos.show', $item->id_pedido) }}"
                                        class="btn-action btn-view" title="Ver pedido">
                                        <i class="bx bx-show"></i>
                                    </a>

                                    @if (!in_array($item->estado, ['cancelado'], true))
                                        <a href="{{ route('admin.pedidos.verificar', $item->id_pedido) }}"
                                            class="btn-action btn-edit"
                                            title="{{ $item->estado === 'en_revision' ? 'Verificar pago' : 'Gestionar pedido' }}">
                                            <i
                                                class="bx {{ $item->estado === 'en_revision' ? 'bx-check-circle' : 'bx-transfer-alt' }}"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="orders-empty">
                            <i class="bx bx-receipt fs-1 d-block mb-2"></i>
                            No hay pedidos registrados.
                        </div>
                    @endforelse

                </div>

                <div class="orders-empty d-none" id="ordersEmptyFiltered">
                    <i class="bx bx-search fs-1 d-block mb-2"></i>
                    No se encontraron pedidos con ese filtro.
                </div>
            </div>

            {{-- TABLA --}}
            <div class="orders-table-view d-none">
                <div class="table-responsive">
                    <table id="tabla_index" class="table table-hover table-bordered align-middle text-center w-100">
                        <thead class="table-light">
                            <tr>
                                <th class="fw-semibold">ID</th>
                                <th class="fw-semibold">N° Pedido</th>
                                <th class="fw-semibold">Cliente</th>
                                <th class="fw-semibold">Entrega</th>
                                <th class="fw-semibold">Estado</th>
                                <th class="fw-semibold">Pago</th>
                                <th class="fw-semibold">Total</th>
                                <th class="fw-semibold">Fecha</th>
                                <th class="text-end fw-semibold">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($items as $item)
                                @php
                                    $estadoBadgeClass = match ($item->estado) {
                                        'pendiente_pago' => 'status-inactive',
                                        'en_revision' => 'status-warning',
                                        'pagado_verificado' => 'status-active',
                                        'preparando' => 'status-info',
                                        'enviado' => 'status-primary',
                                        'entregado' => 'status-dark',
                                        'rechazado' => 'status-danger',
                                        'cancelado' => 'status-danger',
                                        default => 'status-inactive',
                                    };

                                    $estadoIcon = match ($item->estado) {
                                        'pendiente_pago' => 'bx-time-five',
                                        'en_revision' => 'bx-search-alt',
                                        'pagado_verificado' => 'bx-check-circle',
                                        'preparando' => 'bx-package',
                                        'enviado' => 'bx-send',
                                        'entregado' => 'bx-check-shield',
                                        'rechazado' => 'bx-x-circle',
                                        'cancelado' => 'bx-block',
                                        default => 'bx-info-circle',
                                    };

                                    $estadoLabel = strtoupper(str_replace('_', ' ', $item->estado));
                                    $pago = $item->pagoUltimo;

                                    $pagoBadgeClass = $pago
                                        ? match ($pago->estado) {
                                            'enviado' => 'status-warning',
                                            'verificado' => 'status-active',
                                            'rechazado' => 'status-danger',
                                            default => 'status-inactive',
                                        }
                                        : 'status-inactive';

                                    $pagoIcon = $pago
                                        ? match ($pago->estado) {
                                            'enviado' => 'bx-upload',
                                            'verificado' => 'bx-check-circle',
                                            'rechazado' => 'bx-x-circle',
                                            default => 'bx-info-circle',
                                        }
                                        : 'bx-minus-circle';

                                    $pagoLabel = $pago ? strtoupper($pago->estado) : 'SIN PAGO';

                                    $searchText = strtolower(
                                        $item->numero_pedido .
                                            ' ' .
                                            $item->nombre_cliente .
                                            ' ' .
                                            $item->telefono_cliente .
                                            ' ' .
                                            $item->correo_cliente .
                                            ' ' .
                                            $item->estado .
                                            ' ' .
                                            $pagoLabel,
                                    );
                                @endphp

                                <tr class="order-filter-item order-table-item" data-status="{{ $item->estado }}"
                                    data-search="{{ $searchText }}">
                                    <td class="text-muted fw-semibold">{{ $item->id_pedido }}</td>

                                    <td class="text-start">
                                        <div class="fw-semibold">{{ $item->numero_pedido }}</div>

                                        <small class="text-muted d-block">
                                            Subtotal: ₡{{ number_format((float) $item->subtotal, 2, '.', ',') }}
                                        </small>

                                        @if ((float) $item->descuento > 0)
                                            <div class="small text-success">
                                                <i class="bx bx-tag-alt me-1"></i>
                                                Descuento: ₡{{ number_format((float) $item->descuento, 2, '.', ',') }}
                                            </div>
                                        @endif

                                        @if ((float) $item->costo_envio > 0)
                                            <div class="small text-muted">
                                                <i class="bx bx-truck me-1"></i>
                                                Envío: ₡{{ number_format((float) $item->costo_envio, 2, '.', ',') }}
                                            </div>
                                        @endif

                                        @if ($item->cupon)
                                            <div class="small text-primary">
                                                <i class="bx bx-purchase-tag-alt me-1"></i>
                                                Cupón: {{ $item->cupon->codigo }}
                                            </div>
                                        @endif
                                    </td>

                                    <td class="text-start">
                                        <div class="fw-semibold">{{ $item->nombre_cliente }}</div>
                                        <small class="text-muted d-block">{{ $item->telefono_cliente }}</small>
                                        <small
                                            class="text-muted d-block">{{ $item->correo_cliente ?: 'Sin correo registrado' }}</small>

                                        @if ($item->usuario)
                                            <small class="text-primary d-block mt-1">
                                                <i class="bx bx-user me-1"></i>Usuario registrado
                                            </small>
                                        @else
                                            <small class="text-muted d-block mt-1">
                                                <i class="bx bx-user-circle me-1"></i>Invitado
                                            </small>
                                        @endif
                                    </td>

                                    <td>
                                        @if ($item->tipo_entrega === 'envio')
                                            <span class="status-badge status-active">
                                                <i class="bx bx-map me-1"></i>ENVÍO
                                            </span>

                                            <div class="small text-muted mt-1">
                                                {{ $item->provincia_envio ?: '—' }} / {{ $item->canton_envio ?: '—' }}
                                            </div>
                                        @else
                                            <span class="status-badge status-inactive">
                                                <i class="bx bx-store me-1"></i>RETIRO
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        <span class="status-badge {{ $estadoBadgeClass }}">
                                            <i class="bx {{ $estadoIcon }} me-1"></i>{{ $estadoLabel }}
                                        </span>
                                    </td>

                                    <td class="text-start">
                                        <span class="status-badge {{ $pagoBadgeClass }}">
                                            <i class="bx {{ $pagoIcon }} me-1"></i>{{ $pagoLabel }}
                                        </span>

                                        @if ($pago)
                                            <div class="small text-muted mt-1">
                                                {{ strtoupper($pago->metodo) }}
                                                @if ($pago->numero_comprobante)
                                                    · {{ $pago->numero_comprobante }}
                                                @endif
                                            </div>
                                        @endif
                                    </td>

                                    <td class="fw-semibold">
                                        ₡{{ number_format((float) $item->total, 2, '.', ',') }}
                                    </td>

                                    <td>
                                        <div class="small">
                                            {{ optional($item->created_at)->format('Y-m-d') }}
                                        </div>
                                        <div class="small text-muted">
                                            {{ optional($item->created_at)->format('H:i') }}
                                        </div>
                                    </td>

                                    <td class="text-end">
                                        <div class="d-inline-flex justify-content-end gap-2 flex-wrap">
                                            <a href="{{ route('admin.pedidos.show', $item->id_pedido) }}"
                                                class="btn-action btn-view" title="Ver pedido">
                                                <i class="bx bx-show"></i>
                                            </a>

                                            @if (!in_array($item->estado, ['cancelado'], true))
                                                <a href="{{ route('admin.pedidos.verificar', $item->id_pedido) }}"
                                                    class="btn-action btn-edit"
                                                    title="{{ $item->estado === 'en_revision' ? 'Verificar pago' : 'Gestionar pedido' }}">
                                                    <i
                                                        class="bx {{ $item->estado === 'en_revision' ? 'bx-check-circle' : 'bx-transfer-alt' }}"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="custom-pagination-container mt-3" id="ordersPaginationWrap">
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
                            <ul class="pagination pagination-modern mb-0" id="ordersPagination"></ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>



@endsection

@push('scripts')
    <script src="{{ asset('assets/js/modules/pedidos.js') }}"></script>
@endpush
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/modules/pedidos_index.css') }}">
@endpush

