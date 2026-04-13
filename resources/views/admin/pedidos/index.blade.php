{{-- resources/views/admin/pedidos/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Pedidos (Online)')

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
                    <li class="breadcrumb-item active">Pedidos (Online)</li>
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
                    <h4 class="mb-1 text-uppercase fw-bold">Pedidos (Ventas Online)</h4>
                    <small class="text-muted">Gestión de pedidos generados desde la tienda en línea</small>
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
                        <input type="text" id="searchInput" class="search-input"
                            placeholder="Buscar N° pedido, cliente, teléfono, correo, estado..." autocomplete="off">
                        <div class="search-actions">
                            <button class="btn-search-clear" id="clearSearch" type="button" title="Limpiar">
                                <i class="bx bx-x"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>












            {{-- Tabla pedidos --}}
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
                            @endphp

                            <tr>
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
