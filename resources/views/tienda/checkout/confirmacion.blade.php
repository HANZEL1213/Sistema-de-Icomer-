{{-- resources/views/tienda/checkout/confirmacion.blade.php --}}
@extends('tienda.layouts.app')

@section('title', 'Pedido confirmado | Tienda')
@section('meta_description', 'Tu pedido fue creado correctamente.')

@section('content')

    @php
        $pago = $pedido->pagoUltimo;

        $estadoTexto =
            [
                'pendiente_pago' => 'Pendiente de pago',
                'en_revision' => 'En revisión',
                'pagado_verificado' => 'Pago verificado',
                'preparando' => 'Preparando',
                'enviado' => 'Enviado',
                'entregado' => 'Entregado',
                'rechazado' => 'Rechazado',
                'cancelado' => 'Cancelado',
            ][$pedido->estado] ?? ucfirst($pedido->estado);

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
    @endphp

    <section class="store-confirmation-page">

        <div class="container py-4 py-lg-5">

            <div class="store-detail-breadcrumb mb-4">
                <a href="{{ route('tienda.home') }}">Inicio</a>
                <i class="bi bi-chevron-right"></i>
                <a href="{{ route('tienda.checkout.index') }}">Checkout</a>
                <i class="bi bi-chevron-right"></i>
                <span>Confirmación</span>
            </div>

            <div class="store-confirmation-hero mb-4 mb-lg-5">

                <div class="store-confirmation-icon">
                    <i class="bi bi-check2"></i>
                </div>

                <span class="store-section-eyebrow">
                    Pedido recibido
                </span>

                <h1 class="store-confirmation-title">
                    ¡Tu pedido fue enviado correctamente!
                </h1>

                <p class="store-confirmation-subtitle">
                    Recibimos tu pedido y el comprobante de pago. Ahora será revisado por la tienda
                    antes de continuar con la preparación.
                </p>

                <div class="store-confirmation-actions">
                    <a href="{{ route('tienda.pedidos.seguimiento', $pedido->numero_pedido) }}"
                        class="btn btn-store-primary">
                        <i class="bi bi-search me-1"></i>
                        Dar seguimiento
                    </a>

                    <a href="{{ route('tienda.productos.index') }}" class="btn btn-store-outline">
                        Seguir comprando
                    </a>
                </div>

            </div>

            <div class="row g-4 g-xl-5 align-items-start">

                <div class="col-12 col-lg-7">

                    <div class="store-confirmation-card mb-4">

                        <div class="store-confirmation-card-header">
                            <h2>
                                <i class="bi bi-receipt"></i>
                                Detalle del pedido
                            </h2>
                        </div>

                        <div class="store-confirmation-card-body">

                            <div class="store-confirmation-info-grid">

                                <div class="store-confirmation-info-item">
                                    <span>Número de pedido</span>
                                    <strong>{{ $pedido->numero_pedido }}</strong>
                                </div>

                                <div class="store-confirmation-info-item">
                                    <span>Estado</span>
                                    <strong>{{ $estadoTexto }}</strong>
                                </div>

                                <div class="store-confirmation-info-item">
                                    <span>Fecha</span>
                                    <strong>{{ $pedido->created_at?->format('d/m/Y H:i') }}</strong>
                                </div>

                                <div class="store-confirmation-info-item">
                                    <span>Método de pago</span>
                                    <strong>{{ strtoupper($pago?->metodo ?? 'SINPE') }}</strong>
                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="store-confirmation-card mb-4">

                        <div class="store-confirmation-card-header">
                            <h2>
                                <i class="bi bi-credit-card"></i>
                                Comprobante de pago
                            </h2>
                        </div>

                        <div class="store-confirmation-card-body">

                            <ul class="store-confirmation-list">

                                <li>
                                    <span>Estado del pago</span>
                                    <strong>
                                        {{ $pago ? 'Enviado para revisión' : 'No registrado' }}
                                    </strong>
                                </li>

                                <li>
                                    <span>Número de comprobante</span>
                                    <strong>
                                        {{ $pago?->numero_comprobante ?: 'No indicado' }}
                                    </strong>
                                </li>

                                <li>
                                    <span>Monto reportado</span>
                                    <strong>
                                        ₡{{ number_format($pago?->monto_reportado ?? $pedido->total, 2) }}
                                    </strong>
                                </li>

                                <li>
                                    <span>Enviado en</span>
                                    <strong>
                                        {{ $pago?->enviado_en?->format('d/m/Y H:i') ?? 'No disponible' }}
                                    </strong>
                                </li>

                            </ul>

                            @if ($pago?->ruta_comprobante)
                                <div class="mt-4">
                                    <label class="store-form-label">
                                        Imagen enviada
                                    </label>

                                    <div class="bg-light rounded p-3">
                                        <img src="{{ asset('storage/' . $pago->ruta_comprobante) }}"
                                            alt="Comprobante de pago" class="img-fluid rounded-4 border">
                                    </div>
                                </div>
                            @endif

                        </div>

                    </div>

                    <div class="store-confirmation-card">

                        <div class="store-confirmation-card-header">
                            <h2>
                                <i class="bi bi-person-lines-fill"></i>
                                Información del cliente
                            </h2>
                        </div>

                        <div class="store-confirmation-card-body">

                            <ul class="store-confirmation-list">
                                <li>
                                    <span>Cliente</span>
                                    <strong>{{ $pedido->nombre_cliente }}</strong>
                                </li>

                                <li>
                                    <span>Teléfono</span>
                                    <strong>{{ $pedido->telefono_cliente }}</strong>
                                </li>

                                <li>
                                    <span>Correo</span>
                                    <strong>{{ $pedido->correo_cliente ?: 'No indicado' }}</strong>
                                </li>

                                <li>
                                    <span>Entrega</span>
                                    <strong>
                                        {{ $pedido->tipo_entrega === 'envio' ? 'Envío a domicilio' : 'Retiro en tienda' }}
                                    </strong>
                                </li>

                                <li>
                                    <span>Dirección</span>
                                    <strong>{{ $direccion }}</strong>
                                </li>
                            </ul>

                        </div>

                    </div>

                </div>

                <div class="col-12 col-lg-5">

                    <div class="store-confirmation-summary-card">

                     <div class="store-confirmation-summary-card">

    <div class="store-confirmation-summary-header">
        <h2>Resumen</h2>

        <span class="store-confirmation-status">
            {{ $estadoTexto }}
        </span>
    </div>

    <div class="store-checkout-products">

        @foreach ($pedido->detalle as $item)
            @php
                $imagenProducto = $item->producto?->imagenPrincipal?->ruta
                    ? asset('storage/' . $item->producto->imagenPrincipal->ruta)
                    : asset('assets/img/no-image.png');
            @endphp

            <div class="store-checkout-product">

                <div class="store-checkout-product-image">
                    <img src="{{ $imagenProducto }}" alt="{{ $item->nombre_producto }}">
                </div>

@php
    $precioUnitario = (float) $item->precio_unitario;
    $cantidad = (int) $item->cantidad;

    $precioOriginal = (float) ($item->producto?->precio ?? $precioUnitario);

    $tienePromo = $precioOriginal > $precioUnitario;

    $porcentaje = $tienePromo && $precioOriginal > 0
        ? round((($precioOriginal - $precioUnitario) / $precioOriginal) * 100)
        : 0;
@endphp

<div class="store-checkout-product-info">

    <h5>{{ $item->nombre_producto }}</h5>

    @if($tienePromo)
        <span class="badge bg-danger text-white mb-1">
            -{{ $porcentaje }}% OFF
        </span>
    @endif

    <span>
        Cantidad: {{ $cantidad }}
    </span>

</div>

<div class="store-checkout-product-price">

    @if($tienePromo)

        <div class="text-muted text-decoration-line-through small">
            ₡{{ number_format($precioOriginal * $cantidad, 2) }}
        </div>

        <strong class="text-danger">
            ₡{{ number_format($item->total_linea, 2) }}
        </strong>

    @else

        ₡{{ number_format($item->total_linea, 2) }}

    @endif

</div>

            </div>
        @endforeach

    </div>

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

    <div class="store-checkout-totals mt-4">

        <div class="store-checkout-total-row">
            <span>Subtotal</span>
            <strong>₡{{ number_format($pedido->subtotal, 2) }}</strong>
        </div>

        <div class="store-checkout-total-row">
            <span>Envío</span>
            <strong>₡{{ number_format($pedido->costo_envio, 2) }}</strong>
        </div>

        <div class="store-checkout-total-row text-success">
            <span>Descuento</span>
            <strong>-₡{{ number_format($pedido->descuento, 2) }}</strong>
        </div>

        <div class="store-checkout-total-row total">
            <span>Total</span>
            <strong>₡{{ number_format($pedido->total, 2) }}</strong>
        </div>

    </div>

    <div class="store-confirmation-payment-box mt-4">

        <div class="store-confirmation-payment-icon">
            <i class="bi bi-clock-history"></i>
        </div>

        <div>
            <h5>Pago en revisión</h5>

            <p>
                La tienda revisará tu comprobante. Cuando sea aprobado,
                el pedido continuará con el proceso de preparación.
            </p>
        </div>

    </div>

    <div class="d-grid gap-2 mt-4">

        <a href="{{ route('tienda.pedidos.seguimiento', $pedido->numero_pedido) }}"
            class="btn btn-store-primary">
            <i class="bi bi-search me-1"></i>
            Dar seguimiento
        </a>

        <a href="{{ route('tienda.home') }}" class="btn btn-store-outline">
            Volver al inicio
        </a>

    </div>

</div>

                </div>

            </div>

        </div>

    </section>

@endsection
