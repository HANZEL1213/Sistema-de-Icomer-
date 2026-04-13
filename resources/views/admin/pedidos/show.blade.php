{{-- resources/views/admin/pedidos/show.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Detalle del Pedido')

@section('content')
  <link rel="stylesheet" href="{{ asset('assets/css/modules/pedidos_show.css') }}">

    @php
        $estadoConfig = [
            'pendiente_pago' => [
                'label' => 'Pendiente de pago',
                'badge' => 'status-inactive',
                'icon' => 'bx-time-five',
                'desc' => 'El pedido fue creado, pero todavía no tiene un pago validado.',
            ],
            'en_revision' => [
                'label' => 'En revisión',
                'badge' => 'status-warning',
                'icon' => 'bx-search-alt',
                'desc' => 'El cliente envió un pago y está pendiente de revisión administrativa.',
            ],
            'pagado_verificado' => [
                'label' => 'Pagado verificado',
                'badge' => 'status-active',
                'icon' => 'bx-check-circle',
                'desc' => 'El pago fue revisado y aprobado correctamente.',
            ],
            'preparando' => [
                'label' => 'Preparando',
                'badge' => 'status-info',
                'icon' => 'bx-package',
                'desc' => 'El pedido ya está entrando a preparación.',
            ],
            'enviado' => [
                'label' => 'Enviado',
                'badge' => 'status-primary',
                'icon' => 'bx-send',
                'desc' => 'El pedido ya salió a entrega o despacho.',
            ],
            'entregado' => [
                'label' => 'Entregado',
                'badge' => 'status-dark',
                'icon' => 'bx-check-shield',
                'desc' => 'El pedido fue entregado al cliente.',
            ],
            'rechazado' => [
                'label' => 'Rechazado',
                'badge' => 'status-danger',
                'icon' => 'bx-x-circle',
                'desc' => 'El pago fue rechazado o el pedido quedó en estado rechazado.',
            ],
            'cancelado' => [
                'label' => 'Cancelado',
                'badge' => 'status-danger',
                'icon' => 'bx-block',
                'desc' => 'El pedido fue cancelado y no continúa en el flujo.',
            ],
        ];

        $estadoActual = $estadoConfig[$item->estado] ?? [
            'label' => strtoupper(str_replace('_', ' ', $item->estado)),
            'badge' => 'status-inactive',
            'icon' => 'bx-info-circle',
            'desc' => 'Estado no configurado.',
        ];

        $pago = $item->pagoUltimo;

        $estadoPagoBadge = match ($pago->estado ?? null) {
            'enviado' => 'status-warning',
            'verificado' => 'status-active',
            'rechazado' => 'status-danger',
            default => 'status-inactive',
        };

        $estadoPagoIcon = match ($pago->estado ?? null) {
            'enviado' => 'bx-upload',
            'verificado' => 'bx-check-circle',
            'rechazado' => 'bx-x-circle',
            default => 'bx-minus-circle',
        };

        $estadoPagoTexto = $pago ? strtoupper($pago->estado) : 'SIN PAGO';

        $flujoPrincipal = ['pendiente_pago', 'en_revision', 'pagado_verificado', 'preparando', 'enviado', 'entregado'];

        $indiceEstadoActual = array_search($item->estado, $flujoPrincipal, true);
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
                        <a href="{{ route('admin.pedidos.index') }}">Pedidos</a>
                    </li>
                    <li class="breadcrumb-item active">Detalle del Pedido</li>
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
                            Pedido #{{ $item->numero_pedido }}
                        </span>

                        <span class="status-badge {{ $estadoActual['badge'] }}">
                            <i class="bx {{ $estadoActual['icon'] }} me-1"></i>{{ strtoupper($estadoActual['label']) }}
                        </span>
                    </div>

                    <h4 class="fw-bold text-uppercase mb-1">Detalle del Pedido</h4>
                    <small class="text-muted">
                        Vista completa del pedido, su estado actual, productos, pago relacionado y datos del cliente.
                    </small>
                </div>

                <div class="d-flex gap-2 flex-wrap">

                    <a href="{{ route('admin.pedidos.index') }}" class="btn btn-secondary-custom btn-back">
                        <i class="bx bx-arrow-back"></i>
                        <span class="btn-text">Volver</span>
                    </a>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-3">
                    <div class="show-mini-stat h-100">
                        <small class="text-muted d-block">Cliente</small>
                        <div class="fw-bold">{{ $item->nombre_cliente }}</div>
                        <div class="text-muted small">{{ $item->correo_cliente ?: 'Sin correo' }}</div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="show-mini-stat h-100">
                        <small class="text-muted d-block">Tipo de entrega</small>
                        <div class="fw-bold">{{ strtoupper($item->tipo_entrega) }}</div>
                        <div class="text-muted small">
                            {{ $item->tipo_entrega === 'envio' ? ($item->provincia_envio ?: 'Ubicación pendiente') : 'Retiro en tienda' }}
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="show-mini-stat h-100">
                        <small class="text-muted d-block">Fecha del pedido</small>
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

    {{-- FLUJO --}}
    <div class="show-section-card mb-4">
        <div class="card-header-soft">
            <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">
                <div>
                    <h6 class="mb-1 fw-bold">Flujo Visual del Pedido</h6>
                    <small class="text-muted">{{ $estadoActual['desc'] }}</small>
                </div>

                <div class="show-floating-note">
                    Aquí puedes ver rápidamente en qué parte del flujo real se encuentra este pedido.
                </div>
            </div>
        </div>
        <div class="card-body p-3 p-lg-4">
            <div class="show-flow">
                @foreach ($flujoPrincipal as $index => $estadoKey)
                    @php
                        $cfg = $estadoConfig[$estadoKey];
                        $esActual = $item->estado === $estadoKey;
                        $estaHecho = is_int($indiceEstadoActual) && $index < $indiceEstadoActual;
                        $estaBloqueado = !is_int($indiceEstadoActual) || $index > $indiceEstadoActual;
                    @endphp

                    <div
                        class="show-step {{ $esActual ? 'is-current show-pulse' : '' }} {{ $estaHecho ? 'is-done' : '' }} {{ $estaBloqueado && !$esActual ? 'is-blocked' : '' }}">
                        <div class="show-step-bullet">
                            <i class="bx {{ $cfg['icon'] }}"></i>
                        </div>

                        <div class="mb-2">
                            <span class="status-badge {{ $cfg['badge'] }}">
                                {{ strtoupper($cfg['label']) }}
                            </span>
                        </div>

                        <div class="small text-muted">
                            {{ $cfg['desc'] }}
                        </div>

                        <div class="mt-3">
                            @if ($esActual)
                                <span class="badge bg-primary">Actual</span>
                            @elseif ($estaHecho)
                                <span class="badge bg-success">Completado</span>
                            @else
                                <span class="badge bg-light text-dark border">Pendiente</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="row g-4">

        {{-- COLUMNA PRINCIPAL --}}
        <div class="col-xl-8">

            {{-- CLIENTE + RESUMEN --}}
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="show-section-card h-100">
                        <div class="card-header-soft">
                            <h6 class="mb-0 fw-bold">Información del Cliente</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="show-kv">
                                        <small class="text-muted">Nombre</small>
                                        <div class="fw-semibold">{{ $item->nombre_cliente }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="show-kv">
                                        <small class="text-muted">Teléfono</small>
                                        <div>{{ $item->telefono_cliente }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="show-kv">
                                        <small class="text-muted">Correo</small>
                                        <div>{{ $item->correo_cliente ?: 'Sin correo registrado' }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="show-kv">
                                        <small class="text-muted">Tipo de cliente</small>
                                        <div class="fw-semibold">
                                            {{ $item->usuario ? 'Usuario registrado' : 'Cliente invitado' }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="show-kv">
                                        <small class="text-muted">Seguimiento público</small>
                                        <div class="fw-semibold">{{ $item->codigo_seguimiento_publico ?: '—' }}</div>
                                    </div>
                                </div>

                                @if ($item->usuario)
                                    <div class="col-12">
                                        <div class="show-kv">
                                            <small class="text-muted">Usuario vinculado</small>
                                            <div class="fw-semibold">
                                                {{ $item->usuario->nombre }} (ID: {{ $item->usuario->id_usuario }})
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="show-section-card h-100">
                        <div class="card-header-soft">
                            <h6 class="mb-0 fw-bold">Resumen Financiero</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="show-kv">
                                        <small class="text-muted">Subtotal</small>
                                        <div class="fw-semibold">
                                            ₡{{ number_format((float) $item->subtotal, 2, '.', ',') }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="show-kv">
                                        <small class="text-muted">Descuento</small>
                                        <div class="fw-semibold text-success">
                                            ₡{{ number_format((float) $item->descuento, 2, '.', ',') }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="show-kv">
                                        <small class="text-muted">Subtotal con descuento</small>
                                        <div class="fw-semibold">
                                            ₡{{ number_format((float) $item->subtotal_con_descuento, 2, '.', ',') }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="show-kv">
                                        <small class="text-muted">Costo de envío</small>
                                        <div class="fw-semibold">
                                            ₡{{ number_format((float) $item->costo_envio, 2, '.', ',') }}</div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="show-kv">
                                        <small class="text-muted">Total final</small>
                                        <div class="monto-badge fw-bold">
                                            ₡{{ number_format((float) $item->total, 2, '.', ',') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ENTREGA --}}
            <div class="show-section-card mt-4">
                <div class="card-header-soft">
                    <h6 class="mb-0 fw-bold">Entrega y Dirección</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="show-kv">
                                <small class="text-muted">Tipo de entrega</small>
                                <div class="fw-semibold">{{ strtoupper($item->tipo_entrega) }}</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="show-kv">
                                <small class="text-muted">Provincia</small>
                                <div>{{ $item->provincia_envio ?: '—' }}</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="show-kv">
                                <small class="text-muted">Cantón</small>
                                <div>{{ $item->canton_envio ?: '—' }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="show-kv">
                                <small class="text-muted">Distrito</small>
                                <div>{{ $item->distrito_envio ?: '—' }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="show-kv">
                                <small class="text-muted">Costo de envío</small>
                                <div class="fw-semibold">₡{{ number_format((float) $item->costo_envio, 2, '.', ',') }}
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="show-kv">
                                <small class="text-muted">Dirección</small>
                                <div>{{ $item->direccion_envio ?: '—' }}</div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="show-kv">
                                <small class="text-muted">Referencia</small>
                                <div>{{ $item->referencia_envio ?: '—' }}</div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="show-kv">
                                <small class="text-muted">Google Maps</small>
                                <div>
                                    @if ($item->link_google_maps)
                                        <a href="{{ $item->link_google_maps }}" target="_blank" class="fw-semibold">
                                            Ver ubicación
                                        </a>
                                    @else
                                        —
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- DETALLE DEL PAGO ACTUAL --}}
            <div class="show-section-card mt-4">
                <div class="card-header-soft">
                    <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">
                        <div>
                            <h6 class="mb-1 fw-bold">Detalle del Pago Actual</h6>
                            <small class="text-muted">Vista del último intento de pago relacionado con este pedido.</small>
                        </div>

                        <span class="status-badge {{ $estadoPagoBadge }}">
                            <i class="bx {{ $estadoPagoIcon }} me-1"></i>{{ $estadoPagoTexto }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    @if ($pago)
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="show-kv">
                                    <small class="text-muted">Método</small>
                                    <div class="fw-semibold">{{ strtoupper($pago->metodo ?? '—') }}</div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="show-kv">
                                    <small class="text-muted">Intento</small>
                                    <div class="fw-semibold">{{ $pago->intento ?? '—' }}</div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="show-kv">
                                    <small class="text-muted">Monto reportado</small>
                                    <div class="fw-semibold">
                                        ₡{{ number_format((float) ($pago->monto_reportado ?? 0), 2, '.', ',') }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="show-kv">
                                    <small class="text-muted">N° comprobante</small>
                                    <div class="fw-semibold" id="numeroComprobanteTexto">
                                        {{ $pago->numero_comprobante ?: '—' }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="show-kv">
                                    <small class="text-muted">Enviado en</small>
                                    <div class="fw-semibold">{{ optional($pago->enviado_en)->format('Y-m-d H:i') ?: '—' }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="show-kv">
                                    <small class="text-muted">Verificado en</small>
                                    <div class="fw-semibold">
                                        {{ optional($pago->verificado_en)->format('Y-m-d H:i') ?: 'Pendiente' }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="show-kv">
                                    <small class="text-muted">Usuario verificador</small>
                                    <div class="fw-semibold">
                                        {{ $pago->verificador?->nombre ?: ($pago->id_usuario_verificador ?: 'Sin asignar') }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="show-kv">
                                    <small class="text-muted">Último intento</small>
                                    <div class="fw-semibold">{{ $pago->es_ultimo ?? false ? 'Sí' : 'No' }}</div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="soft-alert text-center text-muted py-4">
                            No hay un pago actual registrado para este pedido.
                        </div>
                    @endif
                </div>
            </div>

            {{-- COMPROBANTE --}}
            <div class="show-section-card mt-4">
                <div class="card-header-soft">
                    <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">
                        <div>
                            <h6 class="mb-1 fw-bold">Comprobante</h6>
                            <small class="text-muted">Haz clic sobre la imagen para ampliarla.</small>
                        </div>

                        @if ($pago && $pago->numero_comprobante)
                            <button type="button" class="btn btn-light border" id="copyComprobanteBtn">
                                <i class="bx bx-copy"></i>
                                <span>Copiar comprobante</span>
                            </button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if ($pago && $pago->ruta_comprobante)
                        <div class="show-payment-preview">
                            <div class="show-overlay-actions">
                                <a href="{{ asset('storage/' . $pago->ruta_comprobante) }}" target="_blank"
                                    class="show-overlay-btn text-decoration-none text-dark">
                                    <i class="bx bx-link-external"></i>
                                    Abrir
                                </a>

                                <button type="button" class="show-overlay-btn" id="openVoucherModalBtn"
                                    data-voucher="{{ asset('storage/' . $pago->ruta_comprobante) }}">
                                    <i class="bx bx-zoom-in"></i>
                                    Ampliar
                                </button>
                            </div>

                            <img src="{{ asset('storage/' . $pago->ruta_comprobante) }}" alt="Comprobante de pago"
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

            {{-- PRODUCTOS --}}
            <div class="show-section-card mt-4">
                <div class="card-header-soft">
                    <h6 class="mb-0 fw-bold">Productos del Pedido</h6>
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
                                @forelse ($item->detalle as $detalle)
                                    <tr>
                                        <td>{{ $detalle->nombre_producto }}</td>
                                        <td>{{ $detalle->sku_snapshot ?: '—' }}</td>
                                        <td>₡{{ number_format((float) $detalle->precio_unitario, 2, '.', ',') }}</td>
                                        <td>{{ $detalle->cantidad }}</td>
                                        <td class="fw-bold">
                                            ₡{{ number_format((float) $detalle->total_linea, 2, '.', ',') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            No hay productos registrados en este pedido.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- HISTORIAL DE PAGOS --}}
            <div class="show-section-card mt-4">
                <div class="card-header-soft">
                    <h6 class="mb-0 fw-bold">Historial de Pagos</h6>
                </div>
                <div class="card-body">
                    @forelse ($item->pagos as $pg)
                        @php
                            $pgBadge = match ($pg->estado) {
                                'enviado' => 'status-warning',
                                'verificado' => 'status-active',
                                'rechazado' => 'status-danger',
                                default => 'status-inactive',
                            };

                            $pgIcon = match ($pg->estado) {
                                'enviado' => 'bx-upload',
                                'verificado' => 'bx-check-circle',
                                'rechazado' => 'bx-x-circle',
                                default => 'bx-info-circle',
                            };
                        @endphp

                        <div class="payment-history-card p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                                <div class="fw-bold">Intento #{{ $pg->intento }}</div>

                                <span class="status-badge {{ $pgBadge }}">
                                    <i class="bx {{ $pgIcon }} me-1"></i>{{ strtoupper($pg->estado) }}
                                </span>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="show-kv">
                                        <small class="text-muted">Método</small>
                                        <div class="fw-semibold">{{ strtoupper($pg->metodo) }}</div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="show-kv">
                                        <small class="text-muted">Monto</small>
                                        <div class="fw-semibold">
                                            ₡{{ number_format((float) $pg->monto_reportado, 2, '.', ',') }}</div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="show-kv">
                                        <small class="text-muted">Comprobante</small>
                                        <div class="fw-semibold">{{ $pg->numero_comprobante ?: '—' }}</div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="show-kv">
                                        <small class="text-muted">Enviado en</small>
                                        <div>{{ optional($pg->enviado_en)->format('Y-m-d H:i') ?: '—' }}</div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="show-kv">
                                        <small class="text-muted">Verificado en</small>
                                        <div>{{ optional($pg->verificado_en)->format('Y-m-d H:i') ?: '—' }}</div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="show-kv">
                                        <small class="text-muted">Último intento</small>
                                        <div class="fw-semibold">{{ $pg->es_ultimo ? 'Sí' : 'No' }}</div>
                                    </div>
                                </div>

                                @if ($pg->motivo_rechazo)
                                    <div class="col-12">
                                        <div class="alert alert-danger mb-0 py-2">
                                            <strong>Motivo de rechazo:</strong> {{ $pg->motivo_rechazo }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No hay pagos registrados.</p>
                    @endforelse
                </div>
            </div>

            {{-- NOTAS --}}
            @if ($item->notas)
                <div class="show-section-card mt-4">
                    <div class="card-header-soft">
                        <h6 class="mb-0 fw-bold">Notas del Pedido</h6>
                    </div>
                    <div class="card-body">
                        <div class="soft-alert">
                            {{ $item->notas }}
                        </div>
                    </div>
                </div>
            @endif

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
                                    <i class="bx {{ $estadoActual['icon'] }}"></i>
                                </div>
                                <div>
                                    <div class="fw-bold mb-1">{{ $estadoActual['label'] }}</div>
                                    <small class="text-muted">{{ $estadoActual['desc'] }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="show-section-card mb-4">
                    <div class="card-header-soft">
                        <h6 class="mb-0 fw-bold">Información General</h6>
                    </div>
                    <div class="card-body">
                        <div class="show-kv mb-3">
                            <small class="text-muted">ID Pedido</small>
                            <div class="fw-semibold">{{ $item->id_pedido }}</div>
                        </div>

                        <div class="show-kv mb-3">
                            <small class="text-muted">Fecha de registro</small>
                            <div class="fw-semibold">{{ optional($item->created_at)->format('Y-m-d H:i') }}</div>
                        </div>

                        <div class="show-kv">
                            <small class="text-muted">Código de seguimiento</small>
                            <div class="fw-semibold">{{ $item->codigo_seguimiento_publico ?: '—' }}</div>
                        </div>
                    </div>
                </div>

                @if ($item->cupon || $item->codigo_cupon || (float) $item->descuento > 0)
                    <div class="show-section-card">
                        <div class="card-header-soft">
                            <h6 class="mb-0 fw-bold">Cupón y Descuento</h6>
                        </div>
                        <div class="card-body">
                            <div class="show-kv mb-3">
                                <small class="text-muted">Código</small>
                                <div class="fw-semibold">
                                    {{ $item->cupon?->codigo ?: ($item->codigo_cupon ?: 'Sin cupón') }}
                                </div>
                            </div>

                            <div class="show-kv mb-3">
                                <small class="text-muted">Descuento aplicado</small>
                                <div class="fw-semibold text-success">
                                    ₡{{ number_format((float) $item->descuento, 2, '.', ',') }}
                                </div>
                            </div>

                            <div class="show-kv">
                                <small class="text-muted">Uso registrado</small>
                                <div class="fw-semibold">{{ $item->usoCupon ? 'Sí' : 'No' }}</div>
                            </div>
                        </div>
                    </div>
                @endif

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
    <script src="{{ asset('assets/js/modules/pedidos.js') }}"></script>
@endpush
