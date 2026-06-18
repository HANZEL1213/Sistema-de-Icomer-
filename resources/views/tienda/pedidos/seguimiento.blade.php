{{-- resources/views/tienda/pedidos/seguimiento.blade.php --}}
@extends('tienda.layouts.app')

@section('title', 'Seguimiento del pedido | Tienda')
@section('meta_description', 'Consulta el estado y avance de tu pedido.')

@section('content')

    @php
        $pago = $pedido->pagoUltimo;

        $estadoLabel =
            [
                'pendiente_pago' => 'Pendiente de pago',
                'en_revision' => 'Pago en revisión',
                'pagado_verificado' => 'Pago verificado',
                'preparando' => 'Preparando',
                'enviado' => 'Enviado',
                'entregado' => 'Entregado',
                'rechazado' => 'Rechazado',
                'cancelado' => 'Cancelado',
            ][$pedido->estado] ?? ucfirst($pedido->estado);

        $estadoClass = [
            'pendiente_pago' => 'is-warning',
            'en_revision' => 'is-info',
            'pagado_verificado' => 'is-success',
            'preparando' => 'is-primary',
            'enviado' => 'is-primary',
            'entregado' => 'is-success',
            'rechazado' => 'is-danger',
            'cancelado' => 'is-muted',
        ];

        $pagoEstado = $pago?->estado;

        $pagoLabel =
            [
                'enviado' => 'Comprobante en revisión',
                'verificado' => 'Comprobante verificado',
                'rechazado' => 'Comprobante rechazado',
            ][$pagoEstado] ?? 'Comprobante no registrado';

        $pagoClass = [
            'enviado' => 'is-info',
            'verificado' => 'is-success',
            'rechazado' => 'is-danger',
        ];

        $tipoEntregaLabel = $pedido->tipo_entrega === 'envio' ? 'Envío a domicilio' : 'Retiro en tienda';

        $direccion =
            $pedido->tipo_entrega === 'envio'
                ? collect([
                    $pedido->provincia_envio,
                    $pedido->canton_envio,
                    $pedido->distrito_envio,
                    $pedido->direccion_envio,
                ])
                    ->filter()
                    ->implode(', ')
                : 'Retiro en tienda';

        $pasos = collect([
            [
                'key' => 'pendiente_pago',
                'titulo' => 'Pedido creado',
                'descripcion' => 'Tu pedido fue registrado correctamente.',
                'icono' => 'bi-receipt',
            ],
            [
                'key' => 'en_revision',
                'titulo' => 'Comprobante enviado',
                'descripcion' => 'Recibimos el comprobante y está pendiente de revisión.',
                'icono' => 'bi-file-earmark-check',
            ],
            [
                'key' => 'pagado_verificado',
                'titulo' => 'Pago verificado',
                'descripcion' => 'El comprobante fue revisado y aprobado.',
                'icono' => 'bi-shield-check',
            ],
            [
                'key' => 'preparando',
                'titulo' => 'Preparando pedido',
                'descripcion' => 'Estamos preparando tus productos.',
                'icono' => 'bi-box-seam',
            ],
            [
                'key' => 'enviado',
                'titulo' => 'Pedido enviado',
                'descripcion' => 'Tu pedido ya salió para entrega.',
                'icono' => 'bi-truck',
            ],
            [
                'key' => 'entregado',
                'titulo' => 'Pedido entregado',
                'descripcion' => 'El pedido fue entregado correctamente.',
                'icono' => 'bi-check2-circle',
            ],
        ]);

        $ordenEstados = [
            'pendiente_pago' => 1,
            'en_revision' => 2,
            'pagado_verificado' => 3,
            'preparando' => 4,
            'enviado' => 5,
            'entregado' => 6,
        ];

        $estadoActual = $ordenEstados[$pedido->estado] ?? 1;
    @endphp

    <section class="store-tracking-page">

        <div class="container py-4 py-lg-5">

            <div class="store-detail-breadcrumb mb-4">
                <a href="{{ route('tienda.home') }}">Inicio</a>
                <i class="bi bi-chevron-right"></i>
                <a href="{{ route('tienda.pedidos.mis') }}">Mis pedidos</a>
                <i class="bi bi-chevron-right"></i>
                <span>Seguimiento</span>
            </div>

            <div class="store-tracking-hero mb-4 mb-lg-5">

                <div>
                    <span class="store-section-eyebrow">
                        Seguimiento
                    </span>

                    <h1 class="store-tracking-title">
                        Estado de tu pedido
                    </h1>

                    <p class="store-tracking-subtitle mb-0">
                        Aquí puedes revisar si el comprobante ya fue validado y en qué etapa
                        se encuentra el proceso de preparación, envío o entrega.
                    </p>
                </div>

                <div class="store-tracking-hero-code">
                    <span>Pedido</span>
                    <strong>{{ $pedido->numero_pedido }}</strong>
                </div>

            </div>

            <div class="row g-4 g-xl-5 align-items-start">

                <div class="col-12 col-lg-8">

                    <div class="store-tracking-status-card mb-4">

                        <div class="store-tracking-status-icon {{ $estadoClass[$pedido->estado] ?? 'is-muted' }}">
                            <i class="bi bi-truck"></i>
                        </div>

                        <div class="store-tracking-status-content">
                            <span class="store-order-status {{ $estadoClass[$pedido->estado] ?? 'is-muted' }}">
                                {{ $estadoLabel }}
                            </span>

                            <h2>
                                Tu pedido va en proceso: {{ $estadoLabel }}
                            </h2>

                            <p>
                                Última actualización registrada para el pedido
                                <strong>{{ $pedido->numero_pedido }}</strong>.
                            </p>
                        </div>

                    </div>

                    <div class="store-tracking-card mb-4">

                        <div class="store-tracking-card-header">
                            <h2>
                                <i class="bi bi-list-check"></i>
                                Proceso del pedido
                            </h2>
                        </div>

                        <div class="store-tracking-timeline">

                            @foreach ($pasos as $paso)
                                @php
                                    $numeroPaso = $ordenEstados[$paso['key']];
                                    $completado = $numeroPaso < $estadoActual;
                                    $actual = $numeroPaso === $estadoActual;
                                @endphp

                                <div
                                    class="store-tracking-step {{ $completado ? 'is-completed' : '' }} {{ $actual ? 'is-current' : '' }}">

                                    <div class="store-tracking-step-marker">
                                        <i class="bi {{ $paso['icono'] }}"></i>
                                    </div>

                                    <div class="store-tracking-step-content">
                                        <h3>{{ $paso['titulo'] }}</h3>
                                        <p>{{ $paso['descripcion'] }}</p>

                                        @if ($actual)
                                            <span class="store-tracking-current-label">
                                                Etapa actual
                                            </span>
                                        @elseif($completado)
                                            <span class="store-tracking-completed-label">
                                                Completado
                                            </span>
                                        @endif
                                    </div>

                                </div>
                            @endforeach

                        </div>

                    </div>

                    <div class="store-tracking-card">

                        <div class="store-tracking-card-header">
                            <h2>
                                <i class="bi bi-credit-card"></i>
                                Estado del comprobante
                            </h2>
                        </div>

                        <div class="store-tracking-payment-box">

                            <div class="store-tracking-payment-icon {{ $pagoClass[$pagoEstado] ?? 'is-muted' }}">
                                <i class="bi bi-file-earmark-check"></i>
                            </div>

                            <div class="store-tracking-payment-content">
                                <span class="store-order-status {{ $pagoClass[$pagoEstado] ?? 'is-muted' }}">
                                    {{ $pagoLabel }}
                                </span>

                                <h3>{{ strtoupper($pago?->metodo ?? 'SINPE') }}</h3>

                                <p>
                                    Comprobante:
                                    <strong>{{ $pago?->numero_comprobante ?: 'No indicado' }}</strong>
                                </p>

                                <p>
                                    Monto reportado:
                                    <strong>₡{{ number_format($pago?->monto_reportado ?? $pedido->total, 2) }}</strong>
                                </p>

                                @if ($pago?->ruta_comprobante)
                                    <div class="mt-3">
                                        <span class="d-block mb-2 fw-semibold">
                                            Imagen del comprobante
                                        </span>

                                        <div class="bg-light rounded p-3">
                                            <img src="{{ asset('storage/' . $pago->ruta_comprobante) }}"
                                                alt="Comprobante de pago" class="rounded-4 border comprobante-preview">
                                        </div>
                                    </div>
                                @endif

                             @if ($pedido->estado !== 'cancelado' && $pago?->estado === 'rechazado')

    <div class="alert alert-danger rounded-4 mt-4">

        <div class="d-flex align-items-start gap-3">

            <div class="fs-4">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>

            <div>

                <h5 class="fw-bold mb-1">
                    Comprobante rechazado
                </h5>

                @if ($pago->motivo_rechazo)
                    <p class="mb-3">
                        {{ $pago->motivo_rechazo }}
                    </p>
                @else
                    <p class="mb-3">
                        El comprobante enviado no pudo ser validado.
                    </p>
                @endif

                <a href="{{ route('tienda.pedidos.show', $pedido->numero_pedido) }}"
                    class="btn btn-store-primary">

                    <i class="bi bi-arrow-repeat me-1"></i>

                    Corregir pago

                </a>

            </div>

        </div>

    </div>

@endif
                            </div>

                        </div>

                    </div>

                </div>

             <div class="col-12 col-lg-4">

    <aside class="store-tracking-summary-card">

        <div class="store-tracking-summary-header">
            <h2>Resumen</h2>

            <span class="store-order-status {{ $estadoClass[$pedido->estado] ?? 'is-muted' }}">
                {{ $estadoLabel }}
            </span>
        </div>

        <ul class="store-confirmation-list">
            <li>
                <span>Pedido</span>
                <strong>{{ $pedido->numero_pedido }}</strong>
            </li>

            <li>
                <span>Cliente</span>
                <strong>{{ $pedido->nombre_cliente }}</strong>
            </li>

            <li>
                <span>Teléfono</span>
                <strong>{{ $pedido->telefono_cliente }}</strong>
            </li>

            <li>
                <span>Entrega</span>
                <strong>{{ $tipoEntregaLabel }}</strong>
            </li>

            <li>
                <span>Dirección</span>
                <strong>{{ $direccion }}</strong>
            </li>

            <li>
                <span>Fecha</span>
                <strong>{{ $pedido->created_at?->format('d/m/Y H:i') }}</strong>
            </li>
        </ul>

        {{-- CUPÓN APLICADO --}}
        @if($pedido->id_cupon || $pedido->codigo_cupon || $pedido->descuento > 0)

            <div class="store-cart-coupon-box mt-3 mb-3">

                <label class="store-form-label">
                    Cupón de descuento
                </label>

                <div class="bg-light rounded-4 p-3 border">

                    <div class="d-flex justify-content-between align-items-start gap-3">

                        <div>

                            <span class="badge bg-success mb-2">
                                Cupón aplicado
                            </span>

                            <h6 class="fw-bold mb-1">
                                {{ $pedido->codigo_cupon ?? $pedido->cupon?->codigo ?? 'Cupón aplicado' }}
                            </h6>

                            <small class="text-muted d-block">
                                Descuento aplicado:
                                ₡{{ number_format($pedido->descuento, 2) }}
                            </small>

                            @if($pedido->cupon?->tipo === 'porcentaje')

                                <small class="text-success">
                                    {{ number_format($pedido->cupon->valor, 0) }}% OFF
                                </small>

                            @elseif($pedido->cupon?->tipo === 'monto_fijo')

                                <small class="text-success">
                                    ₡{{ number_format($pedido->cupon->valor, 2) }} OFF
                                </small>

                            @endif

                        </div>

                        <div class="text-success fs-5">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>

                    </div>

                </div>

            </div>

        @endif

        <div class="store-tracking-total-box">

            <div>
                <span>Subtotal</span>
                <strong>₡{{ number_format($pedido->subtotal, 2) }}</strong>
            </div>

            <div>
                <span>Envío</span>
                <strong>₡{{ number_format($pedido->costo_envio, 2) }}</strong>
            </div>

            <div>
                <span>Cupon de Descuento</span>
                <strong>-₡{{ number_format($pedido->descuento, 2) }}</strong>
            </div>

            <div class="total">
                <span>Total</span>
                <strong>₡{{ number_format($pedido->total, 2) }}</strong>
            </div>

        </div>

        <div class="d-grid gap-2">

            <a href="{{ route('tienda.pedidos.show', $pedido->numero_pedido) }}"
                class="btn btn-store-primary">
                Ver detalle completo
            </a>

            @auth
                <a href="{{ route('tienda.pedidos.mis') }}" class="btn btn-store-outline">
                    Volver a mis pedidos
                </a>
            @else
                <a href="{{ route('tienda.home') }}" class="btn btn-store-outline">
                    Volver a la tienda
                </a>
            @endauth

        </div>

    </aside>

</div>

            </div>

        </div>

    </section>

@endsection
