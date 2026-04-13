{{-- resources/views/admin/pagos_pedidos/show.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Detalle Pago Pedido')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/modules/pagos_pedidos.css') }}">

    @php
        $estadoConfig = [
            'enviado' => [
                'label' => 'Enviado',
                'badge' => 'status-warning',
                'icon' => 'bx-time-five',
                'desc' => 'El cliente envió el comprobante y está pendiente de revisión.',
            ],
            'verificado' => [
                'label' => 'Verificado',
                'badge' => 'status-active',
                'icon' => 'bx-check-circle',
                'desc' => 'El pago fue revisado y aprobado correctamente.',
            ],
            'rechazado' => [
                'label' => 'Rechazado',
                'badge' => 'status-danger',
                'icon' => 'bx-x-circle',
                'desc' => 'El pago fue revisado y rechazado.',
            ],
        ];

        $estadoActual = $estadoConfig[$item->estado] ?? [
            'label' => strtoupper((string) $item->estado),
            'badge' => 'status-inactive',
            'icon' => 'bx-info-circle',
            'desc' => 'Estado no configurado.',
        ];
    @endphp

    
    <div class="page-breadcrumb d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav>
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}">
                            <i class="bx bx-home-alt"></i>
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.pagos-pedidos.index') }}">Pagos de Pedidos</a>
                    </li>
                    <li class="breadcrumb-item active">Detalle del Pago</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- HERO --}}
    <div class="card border-0 bg-light mb-4">
        <div class="card-body p-4 p-lg-4">
            <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-4">
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                        <span class="payment-hero-badge">
                            <i class="bx bx-receipt"></i>
                            Pago #{{ $item->id_pago_pedido }}
                        </span>

                        <span class="status-badge {{ $estadoActual['badge'] }}">
                            <i class="bx {{ $estadoActual['icon'] }} me-1"></i>{{ strtoupper($estadoActual['label']) }}
                        </span>
                    </div>

                    <h4 class="fw-bold text-uppercase mb-1">Detalle del Pago de Pedido</h4>
                    <small class="text-muted">
                        Consulta operativa del pago enviado por el cliente, su comprobante y estado de validación.
                    </small>
                </div>

                <div class="d-flex gap-2 flex-wrap">


                    <a href="{{ route('admin.pagos-pedidos.index') }}" class="btn btn-secondary-custom btn-back">
                        <i class="bx bx-arrow-back"></i>
                        <span class="btn-text">Volver</span>
                    </a>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-3">
                    <div class="payment-mini-stat">
                        <small class="text-muted d-block">Pedido</small>
                        <div class="fw-bold">{{ $item->pedido?->numero_pedido ?: 'Sin pedido' }}</div>
                        <div class="text-muted small">ID Pedido: {{ $item->id_pedido }}</div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="payment-mini-stat">
                        <small class="text-muted d-block">Cliente</small>
                        <div class="fw-bold">{{ $item->pedido?->nombre_cliente ?: 'Sin cliente' }}</div>
                        <div class="text-muted small">{{ strtoupper($item->metodo ?: 'N/D') }}</div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="payment-mini-stat">
                        <small class="text-muted d-block">Fecha de envío</small>
                        <div class="fw-bold">{{ $item->enviado_en?->format('Y-m-d') ?: '—' }}</div>
                        <div class="text-muted small">{{ $item->enviado_en?->format('H:i') ?: '—' }}</div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="payment-mini-stat">
                        <small class="text-muted d-block">Monto reportado</small>
                        <div class="monto-badge fw-bold">
                            {{ $item->moneda === 'CRC' ? '₡' : $item->moneda . ' ' }}
                            {{ number_format((float) $item->monto_reportado, 2, '.', ',') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- BLOQUES DESTACADOS --}}
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="payment-highlight-card">
                <div class="payment-highlight-icon">
                    <i class="bx bx-check-circle"></i>
                </div>
                <div class="payment-highlight-label">Estado del Pago</div>
                <div class="payment-highlight-value">
                    @if ($item->estado === 'verificado')
                        <span class="estado-badge-large estado-verificado">
                            <i class="bx bx-check-circle"></i> Verificado
                        </span>
                    @elseif($item->estado === 'rechazado')
                        <span class="estado-badge-large estado-rechazado">
                            <i class="bx bx-x-circle"></i> Rechazado
                        </span>
                    @else
                        <span class="estado-badge-large estado-enviado">
                            <i class="bx bx-time-five"></i> Enviado
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="payment-highlight-card">
                <div class="payment-highlight-icon">
                    <i class="bx bx-money"></i>
                </div>
                <div class="payment-highlight-label">Monto Reportado</div>
                <div class="payment-highlight-value">
                    <span class="monto-badge w-100 justify-content-center">
                        {{ $item->moneda === 'CRC' ? '₡' : $item->moneda . ' ' }}
                        {{ number_format((float) $item->monto_reportado, 2, '.', ',') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">

        {{-- COLUMNA IZQUIERDA --}}
        <div class="col-xl-7">

            <div class="payment-section-card mb-4">
                <div class="card-header-soft">
                    <h6 class="mb-0 fw-bold">Información General del Pago</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <div class="payment-kv">
                                <small class="text-muted">Pedido</small>
                                <div class="fw-semibold">{{ $item->pedido?->numero_pedido ?: 'Sin pedido asociado' }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="payment-kv">
                                <small class="text-muted">Cliente</small>
                                <div class="fw-semibold">{{ $item->pedido?->nombre_cliente ?: 'Sin cliente asociado' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="payment-kv">
                                <small class="text-muted">Método</small>
                                <div class="fw-semibold">{{ strtoupper($item->metodo ?: '—') }}</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="payment-kv">
                                <small class="text-muted">Intento</small>
                                <div class="fw-semibold">
                                    {{ $item->intento }}
                                    @if ($item->es_ultimo)
                                        <span class="text-muted">(Último)</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="payment-kv">
                                <small class="text-muted">Moneda</small>
                                <div class="fw-semibold">{{ $item->moneda ?: '—' }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="payment-kv">
                                <small class="text-muted">Número de comprobante</small>
                                <div class="fw-semibold" id="numeroComprobanteTexto">
                                    {{ $item->numero_comprobante ?: 'Sin número registrado' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="payment-kv">
                                <small class="text-muted">Monto reportado</small>
                                <div class="fw-semibold">
                                    {{ $item->moneda === 'CRC' ? '₡' : $item->moneda . ' ' }}
                                    {{ number_format((float) $item->monto_reportado, 2, '.', ',') }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="payment-kv">
                                <small class="text-muted">Enviado en</small>
                                <div class="fw-semibold">{{ $item->enviado_en?->format('Y-m-d H:i') ?: '—' }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="payment-kv">
                                <small class="text-muted">Verificado en</small>
                                <div class="fw-semibold">{{ $item->verificado_en?->format('Y-m-d H:i') ?: 'Pendiente' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="payment-kv">
                                <small class="text-muted">Verificador</small>
                                <div class="fw-semibold">{{ $item->verificador?->nombre ?: 'Sin asignar' }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="payment-kv">
                                <small class="text-muted">Estado descriptivo</small>
                                <div class="fw-semibold">{{ $estadoActual['desc'] }}</div>
                            </div>
                        </div>

                        @if ($item->estado === 'rechazado' && $item->motivo_rechazo)
                            <div class="col-12">
                                <div class="alert alert-danger mb-0">
                                    <strong>Motivo de rechazo:</strong> {{ $item->motivo_rechazo }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="payment-section-card">
                <div class="card-header-soft">
                    <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">
                        <div>
                            <h6 class="mb-1 fw-bold">Comprobante</h6>
                            <small class="text-muted">Haz clic sobre la imagen para ampliarla.</small>
                        </div>

                        @if ($item->numero_comprobante)
                            <button type="button" class="btn btn-light border" id="copyComprobanteBtn">
                                <i class="bx bx-copy"></i>
                                <span>Copiar comprobante</span>
                            </button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if ($item->ruta_comprobante)
                        <div class="payment-preview">
                            <div class="payment-overlay-actions">
                                <a href="{{ asset('storage/' . $item->ruta_comprobante) }}" target="_blank"
                                    class="payment-overlay-btn">
                                    <i class="bx bx-link-external"></i>
                                    Abrir
                                </a>

                                <button type="button" class="payment-overlay-btn" id="openVoucherModalBtn"
                                    data-voucher="{{ asset('storage/' . $item->ruta_comprobante) }}">
                                    <i class="bx bx-zoom-in"></i>
                                    Ampliar
                                </button>
                            </div>

                            <img src="{{ asset('storage/' . $item->ruta_comprobante) }}" alt="Comprobante de pago"
                                id="voucherPreviewImage">
                        </div>
                    @else
                        <div class="soft-alert text-center text-muted py-5">
                            <i class="bx bx-image-alt fs-1 d-block mb-2"></i>
                            No hay comprobante registrado para este pago.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- COLUMNA DERECHA --}}
        <div class="col-xl-5">

            <div class="payment-section-card mb-4">
                <div class="card-header-soft">
                    <h6 class="mb-0 fw-bold">Resumen Rápido</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="payment-kv">
                                <small class="text-muted">ID del pago</small>
                                <div class="fw-semibold">{{ $item->id_pago_pedido }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="payment-kv">
                                <small class="text-muted">¿Es el último intento?</small>
                                <div class="fw-semibold">{{ $item->es_ultimo ? 'Sí' : 'No' }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="payment-kv">
                                <small class="text-muted">Estado actual</small>
                                <div class="fw-semibold">{{ $estadoActual['label'] }}</div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="payment-kv">
                                <small class="text-muted">Pedido relacionado</small>
                                <div class="fw-semibold">
                                    {{ $item->pedido?->numero_pedido ?: 'Sin pedido asociado' }}
                                </div>
                            </div>
                        </div>

                        @if ($item->pedido)
                            <div class="col-12">
                                <a href="{{ route('admin.pedidos.show', $item->pedido->id_pedido) }}"
                                    class="btn btn-primary-custom w-100">
                                    <i class="bx bx-show"></i>
                                    <span>Ir al pedido relacionado</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="payment-section-card">
                <div class="card-header-soft">
                    <h6 class="mb-0 fw-bold">Observaciones</h6>
                </div>
                <div class="card-body">
                    @if ($item->estado === 'verificado')
                        <div class="soft-alert">
                            <div class="fw-semibold mb-1 text-success">
                                <i class="bx bx-check-circle me-1"></i>Pago aprobado
                            </div>
                            <small class="text-muted">
                                Este pago ya fue validado correctamente y cuenta con registro de verificación.
                            </small>
                        </div>
                    @elseif($item->estado === 'rechazado')
                        <div class="soft-alert">
                            <div class="fw-semibold mb-1 text-danger">
                                <i class="bx bx-x-circle me-1"></i>Pago rechazado
                            </div>
                            <small class="text-muted">
                                Revisa el motivo registrado y el comprobante adjunto para el seguimiento administrativo.
                            </small>
                        </div>
                    @else
                        <div class="soft-alert">
                            <div class="fw-semibold mb-1 text-primary">
                                <i class="bx bx-time-five me-1"></i>Pago pendiente de revisión
                            </div>
                            <small class="text-muted">
                                Este pago fue enviado por el cliente y aún no muestra una validación final.
                            </small>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    {{-- MODAL COMPROBANTE --}}
    <div class="modal fade" id="voucherModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 overflow-hidden">
                <div class="modal-header border-0 bg-light">
                    <h5 class="modal-title fw-bold">Vista ampliada del comprobante</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body bg-white p-3 p-md-4 text-center">
                    <img src="" alt="Comprobante ampliado" id="voucherModalImage"
                        class="img-fluid rounded border">
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/modules/pagos_pedidos.js') }}"></script>
@endpush
