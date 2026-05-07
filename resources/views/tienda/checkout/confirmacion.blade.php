{{-- resources/views/tienda/checkout/confirmacion.blade.php --}}

@php

$pedido = (object)[
    'numero_pedido' => 'PED-2026-00018',
    'estado' => 'Pendiente de pago',
    'total' => 117480,
    'metodo_pago' => 'SINPE Móvil',
    'cliente' => 'Cliente Demo',
    'telefono' => '8888-8888',
    'correo' => 'cliente@email.com',
    'direccion' => 'San José, Costa Rica',
    'fecha' => now()->format('d/m/Y H:i'),
];

$items = collect([
    (object)[
        'nombre' => 'Nike Air Max Urban',
        'precio' => 45990,
        'cantidad' => 1,
        'imagen' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?q=80&w=1200&auto=format&fit=crop',
    ],
    (object)[
        'nombre' => 'Smart Watch Active',
        'precio' => 68990,
        'cantidad' => 1,
        'imagen' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?q=80&w=1200&auto=format&fit=crop',
    ],
]);

@endphp

@extends('tienda.layouts.app')

@section('title', 'Pedido confirmado | Tienda')
@section('meta_description', 'Tu pedido fue creado correctamente.')

@section('content')

<section class="store-confirmation-page">

    <div class="container py-4 py-lg-5">

        {{-- BREADCRUMB --}}
        <div class="store-detail-breadcrumb mb-4">
            <a href="{{ route('tienda.home') }}">Inicio</a>
            <i class="bi bi-chevron-right"></i>
            <a href="{{ route('tienda.checkout.index') }}">Checkout</a>
            <i class="bi bi-chevron-right"></i>
            <span>Confirmación</span>
        </div>


        {{-- HERO CONFIRMACIÓN --}}
        <div class="store-confirmation-hero mb-4 mb-lg-5">

            <div class="store-confirmation-icon">
                <i class="bi bi-check2"></i>
            </div>

            <span class="store-section-eyebrow">
                Pedido creado
            </span>

            <h1 class="store-confirmation-title">
                ¡Tu pedido fue recibido correctamente!
            </h1>

            <p class="store-confirmation-subtitle">
                Hemos registrado tu solicitud. Ahora puedes completar el pago y dar seguimiento
                al estado del pedido desde la sección de pedidos.
            </p>

            <div class="store-confirmation-actions">
                <a href="{{ route('tienda.pedidos.mis') }}" class="btn btn-store-primary">
                    <i class="bi bi-box-seam me-1"></i>
                    Ver mis pedidos
                </a>

                <a href="{{ route('tienda.productos.index') }}" class="btn btn-store-outline">
                    Seguir comprando
                </a>
            </div>

        </div>


        <div class="row g-4 g-xl-5 align-items-start">

            {{-- DETALLE DEL PEDIDO --}}
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
                                <strong>{{ $pedido->estado }}</strong>
                            </div>

                            <div class="store-confirmation-info-item">
                                <span>Fecha</span>
                                <strong>{{ $pedido->fecha }}</strong>
                            </div>

                            <div class="store-confirmation-info-item">
                                <span>Método de pago</span>
                                <strong>{{ $pedido->metodo_pago }}</strong>
                            </div>

                        </div>

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
                                <strong>{{ $pedido->cliente }}</strong>
                            </li>

                            <li>
                                <span>Teléfono</span>
                                <strong>{{ $pedido->telefono }}</strong>
                            </li>

                            <li>
                                <span>Correo</span>
                                <strong>{{ $pedido->correo }}</strong>
                            </li>

                            <li>
                                <span>Dirección</span>
                                <strong>{{ $pedido->direccion }}</strong>
                            </li>
                        </ul>

                    </div>

                </div>

            </div>


            {{-- RESUMEN / PAGO --}}
            <div class="col-12 col-lg-5">

                <div class="store-confirmation-summary-card">

                    <div class="store-confirmation-summary-header">
                        <h2>Resumen</h2>

                        <span class="store-confirmation-status">
                            {{ $pedido->estado }}
                        </span>
                    </div>


                    <div class="store-checkout-products">

                        @foreach($items as $item)

                            <div class="store-checkout-product">

                                <div class="store-checkout-product-image">
                                    <img src="{{ $item->imagen }}"
                                         alt="{{ $item->nombre }}">
                                </div>

                                <div class="store-checkout-product-info">
                                    <h5>{{ $item->nombre }}</h5>
                                    <span>Cantidad: {{ $item->cantidad }}</span>
                                </div>

                                <div class="store-checkout-product-price">
                                    ₡{{ number_format($item->precio * $item->cantidad, 2) }}
                                </div>

                            </div>

                        @endforeach

                    </div>


                    <div class="store-confirmation-total-box">

                        <span>Total del pedido</span>

                        <strong>
                            ₡{{ number_format($pedido->total, 2) }}
                        </strong>

                    </div>


                    <div class="store-confirmation-payment-box">

                        <div class="store-confirmation-payment-icon">
                            <i class="bi bi-phone"></i>
                        </div>

                        <div>
                            <h5>Pago pendiente por SINPE</h5>

                            <p>
                                Realiza el pago al número indicado por la tienda y conserva el comprobante.
                                Luego podrás enviarlo para revisión.
                            </p>
                        </div>

                    </div>


                    <div class="d-grid gap-2">

                     <a href="{{ route('tienda.pedidos.seguimiento', $pedido->numero_pedido) }}" class="btn btn-store-primary">
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