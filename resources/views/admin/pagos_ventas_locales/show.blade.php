@extends('admin.layouts.app')

@section('title', 'Detalle Pago Venta Local')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/modules/pagos_ventas_locales.css') }}">

    @php
        $metodoConfig = [
            'efectivo' => [
                'label' => 'Efectivo',
                'badge' => 'status-active',
                'icon' => 'bx-money',
                'desc' => 'Pago recibido directamente en caja en efectivo.',
                'large_class' => 'estado-verificado',
            ],
            'tarjeta' => [
                'label' => 'Tarjeta',
                'badge' => 'status-info',
                'icon' => 'bx-credit-card',
                'desc' => 'Pago procesado mediante tarjeta.',
                'large_class' => 'estado-enviado',
            ],
            'sinpe' => [
                'label' => 'SINPE',
                'badge' => 'status-primary',
                'icon' => 'bx-mobile-alt',
                'desc' => 'Pago registrado mediante transferencia SINPE.',
                'large_class' => 'estado-verificado',
            ],
            'mixto' => [
                'label' => 'Mixto',
                'badge' => 'status-inactive',
                'icon' => 'bx-layer',
                'desc' => 'Pago compuesto por más de un método.',
                'large_class' => 'estado-rechazado',
            ],
        ];

        $metodoActual = $metodoConfig[$item->metodo] ?? [
            'label' => strtoupper((string) $item->metodo),
            'badge' => 'status-inactive',
            'icon' => 'bx-info-circle',
            'desc' => 'Método no configurado.',
            'large_class' => 'estado-rechazado',
        ];

        $venta = $item->ventaLocal;
        $clienteNombre = $venta?->nombre_cliente ?: 'Consumidor final';
        $ticket = $venta?->numero_ticket ?: 'Sin ticket';
        $totalVenta = (float) ($venta?->total ?? 0);
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
                        <a href="{{ route('admin.pagos-ventas-locales.index') }}">Pagos de Ventas Locales</a>
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
                            Pago #{{ $item->id_pago_venta_local }}
                        </span>

                        <span class="status-badge {{ $metodoActual['badge'] }}">
                            <i class="bx {{ $metodoActual['icon'] }} me-1"></i>{{ strtoupper($metodoActual['label']) }}
                        </span>
                    </div>

                    <h4 class="fw-bold text-uppercase mb-1">Detalle del Pago de Venta Local</h4>
                    <small class="text-muted">
                        Consulta operativa del pago registrado en una venta física, junto con su venta relacionada.
                    </small>
                </div>

                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('admin.pagos-ventas-locales.index') }}" class="btn btn-secondary-custom btn-back">
                        <i class="bx bx-arrow-back"></i>
                        <span class="btn-text">Volver</span>
                    </a>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-3">
                    <div class="payment-mini-stat">
                        <small class="text-muted d-block">Ticket</small>
                        <div class="fw-bold">{{ $ticket }}</div>
                        <div class="text-muted small">ID Venta: {{ $item->id_venta_local }}</div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="payment-mini-stat">
                        <small class="text-muted d-block">Cliente</small>
                        <div class="fw-bold">{{ $clienteNombre }}</div>
                        <div class="text-muted small">{{ $metodoActual['label'] }}</div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="payment-mini-stat">
                        <small class="text-muted d-block">Fecha de registro</small>
                        <div class="fw-bold">{{ optional($item->created_at)->format('Y-m-d') ?: '—' }}</div>
                        <div class="text-muted small">{{ optional($item->created_at)->format('H:i') ?: '—' }}</div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="payment-mini-stat">
                        <small class="text-muted d-block">Monto</small>
                        <div class="monto-badge fw-bold">
                            ₡{{ number_format((float) $item->monto, 2, '.', ',') }}
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
                    <i class="bx {{ $metodoActual['icon'] }}"></i>
                </div>
                <div class="payment-highlight-label">Método de Pago</div>
                <div class="payment-highlight-value">
                    <span class="estado-badge-large {{ $metodoActual['large_class'] }}">
                        <i class="bx {{ $metodoActual['icon'] }}"></i> {{ $metodoActual['label'] }}
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="payment-highlight-card">
                <div class="payment-highlight-icon">
                    <i class="bx bx-money"></i>
                </div>
                <div class="payment-highlight-label">Monto Registrado</div>
                <div class="payment-highlight-value">
                    <span class="monto-badge w-100 justify-content-center">
                        ₡{{ number_format((float) $item->monto, 2, '.', ',') }}
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
                                <small class="text-muted">Ticket</small>
                                <div class="fw-semibold">{{ $ticket }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="payment-kv">
                                <small class="text-muted">Cliente</small>
                                <div class="fw-semibold">{{ $clienteNombre }}</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="payment-kv">
                                <small class="text-muted">Método</small>
                                <div class="fw-semibold">{{ strtoupper($metodoActual['label']) }}</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="payment-kv">
                                <small class="text-muted">Monto</small>
                                <div class="fw-semibold">
                                    ₡{{ number_format((float) $item->monto, 2, '.', ',') }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="payment-kv">
                                <small class="text-muted">ID del pago</small>
                                <div class="fw-semibold">{{ $item->id_pago_venta_local }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="payment-kv">
                                <small class="text-muted">Referencia</small>
                                <div class="fw-semibold">
                                    {{ $item->referencia ?: 'Sin referencia registrada' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="payment-kv">
                                <small class="text-muted">Registrado en</small>
                                <div class="fw-semibold">
                                    {{ optional($item->created_at)->format('Y-m-d H:i') ?: '—' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="payment-kv">
                                <small class="text-muted">Descripción del método</small>
                                <div class="fw-semibold">{{ $metodoActual['desc'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="payment-section-card">
                <div class="card-header-soft">
                    <h6 class="mb-0 fw-bold">Venta Relacionada</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="payment-kv">
                                <small class="text-muted">Número de ticket</small>
                                <div class="fw-semibold">{{ $ticket }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="payment-kv">
                                <small class="text-muted">Total de la venta</small>
                                <div class="fw-semibold">
                                    ₡{{ number_format($totalVenta, 2, '.', ',') }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="payment-kv">
                                <small class="text-muted">Cliente de la venta</small>
                                <div class="fw-semibold">{{ $clienteNombre }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="payment-kv">
                                <small class="text-muted">Cajero</small>
                                <div class="fw-semibold">
                                    {{ $venta?->cajero?->nombre ?: 'Sin cajero registrado' }}
                                </div>
                            </div>
                        </div>

                        @if ($venta)
                            <div class="col-12">
                                <a href="{{ route('admin.ventas-locales.show', $venta->id_venta_local) }}"
                                    class="btn btn-primary-custom w-100">
                                    <i class="bx bx-show"></i>
                                    <span>Ir a la venta relacionada</span>
                                </a>
                            </div>
                        @endif
                    </div>
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
                                <div class="fw-semibold">{{ $item->id_pago_venta_local }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="payment-kv">
                                <small class="text-muted">Método actual</small>
                                <div class="fw-semibold">{{ $metodoActual['label'] }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="payment-kv">
                                <small class="text-muted">Venta asociada</small>
                                <div class="fw-semibold">{{ $item->id_venta_local }}</div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="payment-kv">
                                <small class="text-muted">Referencia visible</small>
                                <div class="fw-semibold">
                                    {{ $item->referencia ?: '—' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="payment-section-card">
                <div class="card-header-soft">
                    <h6 class="mb-0 fw-bold">Observaciones</h6>
                </div>
                <div class="card-body">
                    <div class="soft-alert">
                        <div class="fw-semibold mb-1 text-primary">
                            <i class="bx {{ $metodoActual['icon'] }} me-1"></i>Pago registrado
                        </div>
                        <small class="text-muted">
                            Este registro corresponde a un pago aplicado a una venta local física y se mantiene disponible
                            para consulta administrativa y trazabilidad.
                        </small>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection
