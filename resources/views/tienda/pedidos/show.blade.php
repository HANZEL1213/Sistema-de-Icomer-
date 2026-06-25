{{-- resources/views/tienda/pedidos/show.blade.php --}}
@extends('tienda.layouts.app')

@section('title', 'Detalle del pedido | Tienda')
@section('meta_description', 'Consulta el detalle completo de tu pedido.')

@section('content')

    @php

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

        $estadoLabel = [
            'pendiente_pago' => 'Pendiente de pago',
            'en_revision' => 'Pago en revisión',
            'pagado_verificado' => 'Pago verificado',
            'preparando' => 'Preparando',
            'enviado' => 'Enviado',
            'entregado' => 'Entregado',
            'rechazado' => 'Rechazado',
            'cancelado' => 'Cancelado',
        ];

        $pagoClass = [
            'enviado' => 'is-info',
            'verificado' => 'is-success',
            'rechazado' => 'is-danger',
        ];

        $estadoPedido = $estadoLabel[$pedido->estado] ?? ucfirst($pedido->estado);

        $pago = $pedido->pagoUltimo;

        $estadoPago = $pago?->estado ?? 'pendiente';

        $estadoPagoLabel = match ($estadoPago) {
            'enviado' => 'Comprobante enviado',
            'verificado' => 'Pago verificado',
            'rechazado' => 'Pago rechazado',
            default => 'Pendiente de pago',
        };

    @endphp

    <section class="store-order-show-page">

        <div class="container py-4 py-lg-5">

            {{-- BREADCRUMB --}}
            <div class="store-detail-breadcrumb mb-4">
                <a href="{{ route('tienda.home') }}">Inicio</a>

                <i class="bi bi-chevron-right"></i>

                <a href="{{ route('tienda.pedidos.mis') }}">
                    Mis pedidos
                </a>

                <i class="bi bi-chevron-right"></i>

                <span>
                    {{ $pedido->numero_pedido }}
                </span>
            </div>


            {{-- HERO --}}
            <div class="store-order-show-hero mb-4 mb-lg-5">

                <div>

                    <span class="store-section-eyebrow">
                        Detalle del pedido
                    </span>

                    <h1 class="store-order-show-title">
                        {{ $pedido->numero_pedido }}
                    </h1>

                    <p class="store-order-show-subtitle mb-0">
                        Revisa los productos comprados, datos de entrega,
                        estado del pago y el resumen completo de tu pedido.
                    </p>

                </div>

                <div class="store-order-show-hero-status">

                    <span>Estado actual</span>

                    <strong>
                        {{ $estadoPedido }}
                    </strong>

                </div>

            </div>


            <div class="row g-4 g-xl-5 align-items-start">

                {{-- CONTENIDO --}}
                <div class="col-12 col-lg-8">

                    {{-- ESTADO --}}
                    <div class="store-order-show-status-card mb-4">

                        <div class="store-order-show-status-icon {{ $estadoClass[$pedido->estado] ?? 'is-muted' }}">
                            <i class="bi bi-box-seam"></i>
                        </div>

                        <div>

                            <span class="store-order-status {{ $estadoClass[$pedido->estado] ?? 'is-muted' }}">
                                {{ $estadoPedido }}
                            </span>

                            <h2>
                                Pedido {{ strtolower($estadoPedido) }}
                            </h2>

                            <p>
                                Este pedido fue creado el
                                <strong>
                                    {{ $pedido->created_at?->format('d/m/Y h:i A') }}
                                </strong>.
                            </p>

                        </div>

                    </div>


                 
{{-- PRODUCTOS --}}
<div class="store-order-show-card mb-4">

    <div class="store-order-show-card-header">

        <h2>
            <i class="bi bi-bag-check"></i>
            Productos del pedido
        </h2>

    </div>

    <div class="store-order-show-products">

        @foreach ($pedido->detalle as $detalle)
            @php
                $producto = $detalle->producto;
                $variante = $detalle->variante;
                $opcion = $variante?->opcion;

                $imagen = $producto?->imagenPrincipal?->ruta
                    ? asset('storage/' . $producto->imagenPrincipal->ruta)
                    : asset('assets/img/no-image.png');

                $precioVenta = (float) $detalle->precio_unitario;
                $precioNormal = (float) ($detalle->precio_original ?? $precioVenta);

                $tienePromo = (bool) $detalle->promocion_aplicada
                    && $precioNormal > $precioVenta;

                $porcentaje = $tienePromo && $precioNormal > 0
                    ? round((($precioNormal - $precioVenta) / $precioNormal) * 100)
                    : 0;

                $totalLinea = (float) $detalle->total_linea;

                $skuMostrar = $detalle->sku_snapshot
                    ?? $variante?->sku
                    ?? $producto?->sku;
            @endphp

            <article class="store-order-show-product">

                <div class="store-order-show-product-image">
                    <img src="{{ $imagen }}" alt="{{ $detalle->nombre_producto }}">
                </div>

                <div class="store-order-show-product-info">

                    @if ($skuMostrar)
                        <span>
                            SKU: {{ $skuMostrar }}
                        </span>
                    @endif

                    <h3>
                        {{ $detalle->nombre_producto }}
                    </h3>

                    @if ($detalle->tieneVariante() && $variante)
                        <div class="mb-2">
                            <span class="badge bg-light text-dark border">
                                Variante:
                                {{ $opcion?->etiqueta ?? ($opcion?->valor ?? $variante->nombre) }}
                            </span>
                        </div>
                    @endif

                    @if ($tienePromo)
                        <span class="badge bg-danger text-white mb-2">
                            -{{ $porcentaje }}% OFF
                        </span>
                    @endif

                    <p>
                        Cantidad:
                        {{ $detalle->cantidad }}

                        <br>

                        Precio unitario:

                        @if ($tienePromo)
                            <span class="text-muted text-decoration-line-through">
                                ₡{{ number_format($precioNormal, 2) }}
                            </span>

                            <strong class="text-danger">
                                ₡{{ number_format($precioVenta, 2) }}
                            </strong>
                        @else
                            ₡{{ number_format($precioVenta, 2) }}
                        @endif
                    </p>

                </div>

                <div class="store-order-show-product-total">

                    <span>Total línea</span>

                    @if ($tienePromo)
                        <div class="text-muted text-decoration-line-through small">
                            ₡{{ number_format($precioNormal * $detalle->cantidad, 2) }}
                        </div>

                        <strong class="text-danger">
                            ₡{{ number_format($totalLinea, 2) }}
                        </strong>
                    @else
                        <strong>
                            ₡{{ number_format($totalLinea, 2) }}
                        </strong>
                    @endif

                </div>

            </article>
        @endforeach

    </div>

</div>

                    {{-- ENTREGA --}}
                    <div class="store-order-show-card mb-4">

                        <div class="store-order-show-card-header">

                            <h2>
                                <i class="bi bi-truck"></i>
                                Información de entrega
                            </h2>

                        </div>

                        <div class="store-order-show-card-body">

                            <div class="store-order-show-info-grid">

                                <div class="store-order-show-info-item">
                                    <span>Tipo de entrega</span>

                                    <strong>
                                        {{ $pedido->tipo_entrega === 'envio' ? 'Envío a domicilio' : 'Retiro en tienda' }}
                                    </strong>
                                </div>

                                <div class="store-order-show-info-item">
                                    <span>Provincia</span>

                                    <strong>
                                        {{ $pedido->provincia_envio ?? 'No definido' }}
                                    </strong>
                                </div>

                                <div class="store-order-show-info-item">
                                    <span>Cantón</span>

                                    <strong>
                                        {{ $pedido->canton_envio ?? 'No definido' }}
                                    </strong>
                                </div>

                                <div class="store-order-show-info-item">
                                    <span>Distrito</span>

                                    <strong>
                                        {{ $pedido->distrito_envio ?? 'No definido' }}
                                    </strong>
                                </div>

                            </div>

                            <div class="store-order-show-address-box mt-3">

                                <span>Dirección exacta</span>

                                <strong>
                                    {{ $pedido->direccion_envio ?? 'No registrada' }}
                                </strong>

                                @if ($pedido->referencia_envio)
                                    <p>
                                        {{ $pedido->referencia_envio }}
                                    </p>
                                @endif

                            </div>

                        </div>
                    </div>


                    {{-- PAGO --}}
                    <div class="store-order-show-card">

                        <div class="store-order-show-card-header">

                            <h2>
                                <i class="bi bi-credit-card"></i>
                                Información de pago
                            </h2>

                        </div>

                        <div class="store-order-show-card-body">

                            <div class="store-order-payment-detail">

                                <div class="store-order-payment-icon {{ $pagoClass[$estadoPago] ?? 'is-muted' }}">
                                    <i class="bi bi-receipt"></i>
                                </div>

                                <div class="store-order-payment-content">

                                    <span class="store-order-status {{ $pagoClass[$estadoPago] ?? 'is-muted' }}">
                                        {{ $estadoPagoLabel }}
                                    </span>

                                    <h3>
                                        {{ strtoupper($pago?->metodo ?? 'Pendiente') }}
                                    </h3>

                                    <div class="store-order-payment-data">

                                        <div>
                                            <span>Comprobante</span>

                                            <strong>
                                                {{ $pago?->numero_comprobante ?? 'Pendiente' }}
                                            </strong>
                                        </div>

                                        <div>
                                            <span>Monto reportado</span>

                                            <strong>
                                                ₡{{ number_format($pago?->monto_reportado ?? 0, 2) }}
                                            </strong>
                                        </div>

                                        <div>
                                            <span>Verificado en</span>

                                            <strong>
                                                {{ $pago?->verificado_en?->format('d/m/Y h:i A') ?? 'Pendiente' }}
                                            </strong>
                                        </div>

                                    </div>

                                    @if ($pago?->ruta_comprobante)
                                        <div class="store-order-payment-proof mt-4">

                                            <span class="d-block mb-2 fw-semibold">
                                                Imagen del comprobante
                                            </span>

                                            <div class="bg-light rounded p-3">

                                                <img src="{{ asset('storage/' . $pago->ruta_comprobante) }}"
                                                    alt="Comprobante de pago" class="img-fluid rounded-4 border">

                                            </div>

                                        </div>
                                    @endif




                                    @if ($pedido->estado !== 'cancelado' && $pago?->estado === 'rechazado')
                                        <div class="alert alert-danger rounded-4 mt-4">
                                            <strong>Pago rechazado</strong>

                                            @if ($pago->motivo_rechazo)
                                                <p class="mb-0 mt-1">
                                                    Motivo: {{ $pago->motivo_rechazo }}
                                                </p>
                                            @else
                                                <p class="mb-0 mt-1">
                                                    El comprobante no pudo ser validado.
                                                </p>
                                            @endif
                                        </div>

                                        <div class="bg-light rounded-4 border p-4 mt-4">
                                            <h4 class="fw-bold mb-2">
                                                Reenviar pago
                                            </h4>

                                            <p class="text-muted mb-3">
                                                Puedes ingresar un nuevo número de comprobante o referencia, subir una imagen del comprobante o enviar
                                                ambos.
                                            </p>

                                            <form action="{{ route('tienda.pagos.store', $pedido->numero_pedido) }}"
                                                method="POST" enctype="multipart/form-data">
                                                @csrf

                                                <div class="mb-3">
                                                    <label class="form-label">
                                                        Número de comprobante o referencia <span class="text-danger">*</span>
                                                    </label>

                                                    <input type="text" name="numero_comprobante" class="form-control"
                                                        value="{{ old('numero_comprobante') }}"
                                                        placeholder="Ej: 123456789" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">
                                                        Imagen del comprobante (Opcional)
                                                    </label>

                                                    <input type="file" name="comprobante" class="form-control"
                                                        accept="image/*">
                                                </div>

                                                <button type="submit" class="btn btn-store-primary">
                                                    <i class="bi bi-arrow-repeat me-1"></i>
                                                    Reenviar pago
                                                </button>
                                            </form>
                                        </div>
                                    @endif











                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- SIDEBAR --}}
                <div class="col-12 col-lg-4">

                    <aside class="store-order-show-summary-card">

                        <div class="store-order-show-summary-header">

                            <h2>
                                Resumen
                            </h2>

                            <span class="store-order-status {{ $estadoClass[$pedido->estado] ?? 'is-muted' }}">
                                {{ $estadoPedido }}
                            </span>

                        </div>

                        <ul class="store-confirmation-list">

                            <li>
                                <span>Cliente</span>

                                <strong>
                                    {{ $pedido->nombre_cliente }}
                                </strong>
                            </li>

                            <li>
                                <span>Teléfono</span>

                                <strong>
                                    {{ $pedido->telefono_cliente }}
                                </strong>
                            </li>

                            <li>
                                <span>Correo</span>

                                <strong>
                                    {{ $pedido->correo_cliente }}
                                </strong>
                            </li>

                            <li>
                                <span>Fecha</span>

                                <strong>
                                    {{ $pedido->created_at?->format('d/m/Y h:i A') }}
                                </strong>
                            </li>

                        </ul>

                        {{-- CUPÓN APLICADO --}}
                        @if ($pedido->id_cupon || $pedido->codigo_cupon || $pedido->descuento > 0)

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
                                                {{ $pedido->codigo_cupon ?? ($pedido->cupon?->codigo ?? 'Cupón aplicado') }}
                                            </h6>

                                            <small class="text-muted d-block">
                                                Descuento aplicado:
                                                ₡{{ number_format($pedido->descuento, 2) }}
                                            </small>

                                            @if ($pedido->cupon?->tipo === 'porcentaje')
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

                        <div class="store-order-show-total-box">

                            <div>
                                <span>Subtotal</span>

                                <strong>
                                    ₡{{ number_format($pedido->subtotal, 2) }}
                                </strong>
                            </div>

                            <div>
                                <span>Envío</span>

                                <strong>
                                    ₡{{ number_format($pedido->costo_envio ?? 0, 2) }}
                                </strong>
                            </div>

                            <div>
                                <span>Cupon de Descuento</span>

                                <strong>
                                    -₡{{ number_format($pedido->descuento ?? 0, 2) }}
                                </strong>
                            </div>

                            <div class="total">

                                <span>Total</span>

                                <strong>
                                    ₡{{ number_format($pedido->total, 2) }}
                                </strong>

                            </div>

                        </div>

                        @if ($pedido->notas)
                            <div class="store-order-show-note">

                                <i class="bi bi-info-circle"></i>

                                <span>
                                    {{ $pedido->notas }}
                                </span>

                            </div>
                        @endif

                        <div class="d-grid gap-2">

                            <a href="{{ route('tienda.pedidos.seguimiento', ['codigo' => $pedido->numero_pedido]) }}"
                                class="btn btn-store-primary">

                                <i class="bi bi-search me-1"></i>

                                Ver seguimiento

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
