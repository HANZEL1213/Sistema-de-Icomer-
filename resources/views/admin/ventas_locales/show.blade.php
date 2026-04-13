@extends('admin.layouts.app')

@section('title', 'Detalle de Venta Local')

@section('content')

    @php
        $pagos = $item->pagos ?? collect();
        $detalle = $item->detalle ?? collect();
        $movimientos = $item->movimientosInventario ?? collect();

        $montoPagado = (float) $pagos->sum('monto');
        $totalVenta = (float) $item->total;
        $estaPagado = abs($montoPagado - $totalVenta) < 0.01 || $montoPagado > $totalVenta;

        $estadoBadge = $estaPagado ? 'status-active' : 'status-warning';
        $estadoIcon = $estaPagado ? 'bx-check-circle' : 'bx-time-five';
        $estadoTexto = $estaPagado ? 'PAGADO' : 'INCOMPLETO';

        $cantidadUnidades = (int) $detalle->sum('cantidad');
        $cantidadLineas = (int) $detalle->count();

        $metodosUsados = $pagos
            ->pluck('metodo')
            ->filter()
            ->map(fn ($m) => strtoupper($m))
            ->unique()
            ->implode(' + ');

        $metodosUsados = $metodosUsados ?: 'SIN PAGO';

        $clienteNombre = $item->nombre_cliente ?: 'Consumidor final';
        $clienteTelefono = $item->telefono_cliente ?: 'Sin teléfono';
        $notasVenta = $item->notas ?: 'Sin notas registradas';
    @endphp

   
        <style>
            .show-hero-badge {
                display: inline-flex;
                align-items: center;
                gap: .45rem;
                padding: .55rem .9rem;
                border-radius: 999px;
                background: rgba(255, 255, 255, .85);
                border: 1px solid rgba(0, 0, 0, .06);
                font-weight: 700;
            }

            .show-mini-stat {
                border-radius: 16px;
                background: #fff;
                border: 1px solid rgba(0, 0, 0, .06);
                padding: 1rem;
                box-shadow: 0 8px 18px rgba(0, 0, 0, .04);
                transition: transform .2s ease, box-shadow .2s ease;
                height: 100%;
            }

            .show-mini-stat:hover {
                transform: translateY(-2px);
                box-shadow: 0 12px 24px rgba(0, 0, 0, .08);
            }

            .show-section-card {
                border: 0;
                border-radius: 18px;
                overflow: hidden;
                background: #fff;
                box-shadow: 0 10px 24px rgba(0, 0, 0, 0.05);
            }

            .show-section-card .card-header-soft {
                background: linear-gradient(135deg, rgba(248, 249, 250, 1) 0%, rgba(255, 255, 255, 1) 100%);
                border-bottom: 1px solid rgba(0, 0, 0, .05);
                padding: 1rem 1.25rem;
            }

            .show-kv {
                padding: .8rem .95rem;
                border-radius: 14px;
                background: #f8f9fa;
                height: 100%;
                border: 1px solid rgba(0, 0, 0, .04);
            }

            .show-kv small {
                display: block;
                margin-bottom: .2rem;
            }

            .show-side-panel {
                position: sticky;
                top: 95px;
            }

            .soft-alert {
                border-radius: 16px;
                border: 1px dashed rgba(0, 0, 0, .12);
                background: #fff;
                padding: 1rem;
            }

            .payment-history-card {
                border: 1px solid rgba(0, 0, 0, .06);
                border-radius: 18px;
                background: #fff;
                transition: all .2s ease;
            }

            .payment-history-card:hover {
                box-shadow: 0 12px 24px rgba(0, 0, 0, .06);
                transform: translateY(-2px);
            }

            .show-table thead th {
                white-space: nowrap;
            }

            .show-floating-note {
                font-size: .86rem;
                color: #6c757d;
            }

            @media (max-width: 1199.98px) {
                .show-side-panel {
                    position: static;
                }
            }

            @media print {
                .page-breadcrumb,
                .btn,
                .show-side-panel {
                    display: none !important;
                }

                .show-section-card,
                .show-mini-stat,
                .card,
                .soft-alert,
                .show-kv {
                    box-shadow: none !important;
                }

                .bg-light {
                    background: #fff !important;
                }

                body {
                    margin: 0;
                    padding: 0;
                }
            }
        </style>
   

    {{-- BREADCRUMB --}}
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
                        <a href="{{ route('admin.ventas-locales.index') }}">Ventas Locales</a>
                    </li>
                    <li class="breadcrumb-item active">Detalle de Venta</li>
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
                        <span class="show-hero-badge">
                            <i class="bx bx-receipt"></i>
                            Ticket {{ $item->numero_ticket }}
                        </span>

                        <span class="status-badge {{ $estadoBadge }}">
                            <i class="bx {{ $estadoIcon }} me-1"></i>{{ $estadoTexto }}
                        </span>
                    </div>

                    <h4 class="fw-bold text-uppercase mb-1">Detalle de Venta Local</h4>
                    <small class="text-muted">
                        Vista completa de la venta, su detalle de productos, pagos registrados y movimientos de inventario.
                    </small>
                </div>

                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('admin.ventas-locales.index') }}" class="btn btn-secondary-custom btn-back">
                        <i class="bx bx-arrow-back"></i>
                        <span class="btn-text">Volver</span>
                    </a>

                    <button type="button" class="btn btn-primary-custom" onclick="window.print()">
                        <i class="bx bx-printer"></i>
                        <span>Imprimir</span>
                    </button>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-3">
                    <div class="show-mini-stat h-100">
                        <small class="text-muted d-block">Cliente</small>
                        <div class="fw-bold">{{ $clienteNombre }}</div>
                        <div class="text-muted small">{{ $clienteTelefono }}</div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="show-mini-stat h-100">
                        <small class="text-muted d-block">Cajero</small>
                        <div class="fw-bold">{{ $item->cajero?->nombre ?: 'Sin cajero' }}</div>
                        <div class="text-muted small">
                            {{ $item->cajero?->correo ?: 'Sin correo registrado' }}
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="show-mini-stat h-100">
                        <small class="text-muted d-block">Fecha de registro</small>
                        <div class="fw-bold">{{ optional($item->created_at)->format('Y-m-d') }}</div>
                        <div class="text-muted small">{{ optional($item->created_at)->format('H:i') }}</div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="show-mini-stat h-100">
                        <small class="text-muted d-block">Total</small>
                        <div class="monto-badge fw-bold">
                            ₡{{ number_format((float) $item->total, 2, '.', ',') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">

        {{-- COLUMNA PRINCIPAL --}}
        <div class="col-xl-8">

            {{-- INFORMACIÓN GENERAL + CLIENTE --}}
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="show-section-card h-100">
                        <div class="card-header-soft">
                            <h6 class="mb-0 fw-bold">Información General</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="show-kv">
                                        <small class="text-muted">Número de Ticket</small>
                                        <div class="fw-semibold">{{ $item->numero_ticket }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="show-kv">
                                        <small class="text-muted">ID Venta Local</small>
                                        <div class="fw-semibold">{{ $item->id_venta_local }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="show-kv">
                                        <small class="text-muted">Canal consolidado</small>
                                        <div class="fw-semibold">
                                            {{ strtoupper($item->venta?->canal ?? 'LOCAL') }}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="show-kv">
                                        <small class="text-muted">Estado de pago</small>
                                        <div>
                                            <span class="status-badge {{ $estadoBadge }}">
                                                <i class="bx {{ $estadoIcon }} me-1"></i>{{ $estadoTexto }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="show-kv">
                                        <small class="text-muted">Métodos utilizados</small>
                                        <div class="fw-semibold">{{ $metodosUsados }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="show-section-card h-100">
                        <div class="card-header-soft">
                            <h6 class="mb-0 fw-bold">Cliente y Cajero</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="show-kv">
                                        <small class="text-muted">Cliente</small>
                                        <div class="fw-semibold">{{ $clienteNombre }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="show-kv">
                                        <small class="text-muted">Teléfono</small>
                                        <div>{{ $clienteTelefono }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="show-kv">
                                        <small class="text-muted">Cajero responsable</small>
                                        <div class="fw-semibold">
                                            {{ $item->cajero?->nombre ?: 'No registrado' }}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="show-kv">
                                        <small class="text-muted">Correo del cajero</small>
                                        <div>{{ $item->cajero?->correo ?: 'Sin correo registrado' }}</div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="show-kv">
                                        <small class="text-muted">ID usuario cajero</small>
                                        <div class="fw-semibold">{{ $item->id_usuario_cajero }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RESUMEN FINANCIERO --}}
            <div class="show-section-card mt-4">
                <div class="card-header-soft">
                    <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">
                        <div>
                            <h6 class="mb-1 fw-bold">Resumen Financiero</h6>
                            <small class="text-muted">Totales de la venta y conciliación de pagos registrados.</small>
                        </div>

                        <div class="show-floating-note">
                            Total pagado: ₡{{ number_format($montoPagado, 2, '.', ',') }}
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="show-kv">
                                <small class="text-muted">Subtotal</small>
                                <div class="fw-semibold">₡{{ number_format((float) $item->subtotal, 2, '.', ',') }}</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="show-kv">
                                <small class="text-muted">Descuento</small>
                                <div class="fw-semibold text-success">
                                    ₡{{ number_format((float) $item->descuento, 2, '.', ',') }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="show-kv">
                                <small class="text-muted">Total final</small>
                                <div class="monto-badge fw-bold">
                                    ₡{{ number_format((float) $item->total, 2, '.', ',') }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="show-kv">
                                <small class="text-muted">Monto pagado</small>
                                <div class="fw-semibold text-success">
                                    ₡{{ number_format($montoPagado, 2, '.', ',') }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="show-kv">
                                <small class="text-muted">Diferencia</small>
                                <div class="fw-semibold {{ $estaPagado ? 'text-success' : 'text-danger' }}">
                                    ₡{{ number_format(max(0, $totalVenta - $montoPagado), 2, '.', ',') }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="show-kv">
                                <small class="text-muted">Estado</small>
                                <div>
                                    <span class="status-badge {{ $estadoBadge }}">
                                        <i class="bx {{ $estadoIcon }} me-1"></i>{{ $estadoTexto }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PRODUCTOS --}}
            <div class="show-section-card mt-4">
                <div class="card-header-soft">
                    <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">
                        <div>
                            <h6 class="mb-1 fw-bold">Productos de la Venta</h6>
                            <small class="text-muted">Detalle exacto de líneas vendidas registradas en la base.</small>
                        </div>

                        <div class="show-floating-note">
                            {{ $cantidadLineas }} línea(s) / {{ $cantidadUnidades }} unidad(es)
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle show-table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Producto</th>
                                    <th>SKU</th>
                                    <th>Precio</th>
                                    <th>Cantidad</th>
                                    <th>Total Línea</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($detalle as $d)
                                    <tr>
                                        <td>{{ $d->nombre_producto }}</td>
                                        <td>{{ $d->sku_snapshot ?: '—' }}</td>
                                        <td>₡{{ number_format((float) $d->precio_unitario, 2, '.', ',') }}</td>
                                        <td>{{ $d->cantidad }}</td>
                                        <td class="fw-bold">₡{{ number_format((float) $d->total_linea, 2, '.', ',') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            No hay productos registrados en esta venta.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                            @if ($detalle->count())
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="4" class="text-end fw-bold">SUBTOTAL:</td>
                                        <td class="fw-bold">₡{{ number_format((float) $item->subtotal, 2, '.', ',') }}</td>
                                    </tr>

                                    @if ((float) $item->descuento > 0)
                                        <tr>
                                            <td colspan="4" class="text-end text-success fw-semibold">DESCUENTO:</td>
                                            <td class="text-success fw-semibold">
                                                ₡{{ number_format((float) $item->descuento, 2, '.', ',') }}
                                            </td>
                                        </tr>
                                    @endif

                                    <tr>
                                        <td colspan="4" class="text-end fw-bold">TOTAL:</td>
                                        <td class="fw-bold">₡{{ number_format((float) $item->total, 2, '.', ',') }}</td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

            {{-- PAGOS --}}
            <div class="show-section-card mt-4">
                <div class="card-header-soft">
                    <h6 class="mb-0 fw-bold">Registro de Pagos</h6>
                </div>
                <div class="card-body">
                    @forelse ($pagos as $index => $pg)
                        @php
                            $metodo = strtoupper($pg->metodo ?? '—');

                            $pgBadge = match ($pg->metodo ?? null) {
                                'efectivo' => 'status-active',
                                'tarjeta' => 'status-info',
                                'sinpe' => 'status-primary',
                                'mixto' => 'status-warning',
                                default => 'status-inactive',
                            };

                            $pgIcon = match ($pg->metodo ?? null) {
                                'efectivo' => 'bx-money',
                                'tarjeta' => 'bx-credit-card',
                                'sinpe' => 'bx-mobile-alt',
                                'mixto' => 'bx-shuffle',
                                default => 'bx-info-circle',
                            };
                        @endphp

                        <div class="payment-history-card p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                                <div class="fw-bold">Pago #{{ $index + 1 }}</div>

                                <span class="status-badge {{ $pgBadge }}">
                                    <i class="bx {{ $pgIcon }} me-1"></i>{{ $metodo }}
                                </span>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="show-kv">
                                        <small class="text-muted">Método</small>
                                        <div class="fw-semibold">{{ $metodo }}</div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="show-kv">
                                        <small class="text-muted">Monto</small>
                                        <div class="fw-semibold">
                                            ₡{{ number_format((float) $pg->monto, 2, '.', ',') }}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="show-kv">
                                        <small class="text-muted">Referencia</small>
                                        <div class="fw-semibold">{{ $pg->referencia ?: '—' }}</div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="show-kv">
                                        <small class="text-muted">Fecha de registro</small>
                                        <div>{{ optional($pg->created_at)->format('Y-m-d H:i') ?: '—' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="soft-alert text-center text-muted py-4">
                            No hay pagos registrados para esta venta.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- MOVIMIENTOS DE INVENTARIO --}}
            <div class="show-section-card mt-4">
                <div class="card-header-soft">
                    <h6 class="mb-0 fw-bold">Movimientos de Inventario</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle show-table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Producto ID</th>
                                    <th>Tipo</th>
                                    <th>Cantidad</th>
                                    <th>Motivo</th>
                                    <th>Usuario</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($movimientos as $mov)
                                    <tr>
                                        <td>{{ $mov->id_producto }}</td>
                                        <td>
                                            <span class="status-badge status-danger">
                                                <i class="bx bx-log-out me-1"></i>{{ strtoupper($mov->tipo) }}
                                            </span>
                                        </td>
                                        <td>{{ $mov->cantidad }}</td>
                                        <td>{{ $mov->motivo ?: '—' }}</td>
                                        <td>{{ $mov->id_usuario_realizador ?: '—' }}</td>
                                        <td>{{ optional($mov->created_at)->format('Y-m-d H:i') ?: '—' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            No hay movimientos de inventario asociados a esta venta.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- NOTAS --}}
            <div class="show-section-card mt-4">
                <div class="card-header-soft">
                    <h6 class="mb-0 fw-bold">Notas de la Venta</h6>
                </div>
                <div class="card-body">
                    <div class="soft-alert">
                        {{ $notasVenta }}
                    </div>
                </div>
            </div>

        </div>

        {{-- PANEL LATERAL --}}
        <div class="col-xl-4">
            <div class="show-side-panel">

                <div class="show-section-card mb-4">
                    <div class="card-header-soft">
                        <h6 class="mb-0 fw-bold">Estado Actual</h6>
                    </div>
                    <div class="card-body">
                        <div class="soft-alert">
                            <div class="d-flex align-items-start gap-3">
                                <div class="fs-2 text-primary">
                                    <i class="bx {{ $estadoIcon }}"></i>
                                </div>
                                <div>
                                    <div class="fw-bold mb-1">{{ $estadoTexto }}</div>
                                    <small class="text-muted">
                                        {{ $estaPagado
                                            ? 'La venta ya tiene cobertura completa en sus pagos registrados.'
                                            : 'La suma de pagos registrados todavía no cubre el total de la venta.' }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

          
                <div class="show-section-card">
                    <div class="card-header-soft">
                        <h6 class="mb-0 fw-bold">Acciones</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                         

                            <button type="button" class="btn btn-primary-custom" onclick="window.print()">
                                <i class="bx bx-printer"></i>
                                <span>Imprimir</span>
                            </button>

                            <button type="button" class="btn btn-light border text-muted" disabled>
                                <i class="bx bx-lock-alt"></i>
                                <span>Edición bloqueada</span>
                            </button>
                        </div>

                        <div class="soft-alert mt-3 small text-muted">
                            La edición y eliminación directa de ventas locales está deshabilitada para proteger
                            inventario, pagos y trazabilidad.
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection