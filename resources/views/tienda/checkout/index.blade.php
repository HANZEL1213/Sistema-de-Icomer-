{{-- resources/views/tienda/checkout/index.blade.php --}}

@php

$carrito = collect([
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

$subtotal = $carrito->sum(fn($item) => $item->precio * $item->cantidad);
$envio = 3500;
$descuento = 5000;
$total = $subtotal + $envio - $descuento;

@endphp

@extends('tienda.layouts.app')

@section('title', 'Checkout | Tienda')
@section('meta_description', 'Finaliza tu compra de forma rápida y segura.')

@section('content')

<section class="store-checkout-page">

    <div class="container py-4 py-lg-5">

        {{-- BREADCRUMB --}}
        <div class="store-detail-breadcrumb mb-4">
            <a href="{{ route('tienda.home') }}">Inicio</a>

            <i class="bi bi-chevron-right"></i>

            <a href="{{ route('tienda.carrito.index') }}">
                Carrito
            </a>

            <i class="bi bi-chevron-right"></i>

            <span>Checkout</span>
        </div>


        {{-- HERO --}}
        <div class="store-checkout-hero mb-4 mb-lg-5">

            <div class="store-checkout-hero-content">

                <span class="store-section-eyebrow">
                    Finalizar compra
                </span>

                <h1 class="store-checkout-title">
                    Completa tu pedido
                </h1>

                <p class="store-checkout-subtitle mb-0">
                    Revisa tus productos, completa tus datos y confirma tu compra
                    desde una experiencia optimizada para móvil.
                </p>

            </div>

        </div>


        <div class="row g-4 g-xl-5 align-items-start">

            {{-- FORMULARIO --}}
            <div class="col-12 col-lg-7">

                {{-- DATOS PERSONALES --}}
                <div class="store-checkout-card mb-4">

                    <div class="store-checkout-card-header">
                        <h2>
                            <i class="bi bi-person"></i>
                            Información personal
                        </h2>
                    </div>

                    <div class="store-checkout-card-body">

                        <div class="row g-3">

                            <div class="col-12 col-md-6">
                                <label class="store-form-label">
                                    Nombre completo
                                </label>

                                <input type="text"
                                       class="form-control store-filter-control"
                                       placeholder="Tu nombre">
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="store-form-label">
                                    Teléfono
                                </label>

                                <input type="text"
                                       class="form-control store-filter-control"
                                       placeholder="8888-8888">
                            </div>

                            <div class="col-12">
                                <label class="store-form-label">
                                    Correo electrónico
                                </label>

                                <input type="email"
                                       class="form-control store-filter-control"
                                       placeholder="correo@email.com">
                            </div>

                        </div>

                    </div>

                </div>


                {{-- ENVÍO --}}
                <div class="store-checkout-card mb-4">

                    <div class="store-checkout-card-header">
                        <h2>
                            <i class="bi bi-geo-alt"></i>
                            Dirección de entrega
                        </h2>
                    </div>

                    <div class="store-checkout-card-body">

                        <div class="row g-3">

                            <div class="col-12 col-md-6">
                                <label class="store-form-label">
                                    Provincia
                                </label>

                                <select class="form-select store-filter-control">
                                    <option>San José</option>
                                    <option>Alajuela</option>
                                    <option>Cartago</option>
                                </select>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="store-form-label">
                                    Cantón
                                </label>

                                <input type="text"
                                       class="form-control store-filter-control"
                                       placeholder="Cantón">
                            </div>

                            <div class="col-12">
                                <label class="store-form-label">
                                    Dirección exacta
                                </label>

                                <textarea class="form-control store-filter-control"
                                          rows="4"
                                          placeholder="Dirección detallada"></textarea>
                            </div>

                        </div>

                    </div>

                </div>


                {{-- MÉTODO PAGO --}}
                <div class="store-checkout-card">

                    <div class="store-checkout-card-header">
                        <h2>
                            <i class="bi bi-credit-card"></i>
                            Método de pago
                        </h2>
                    </div>

                    <div class="store-checkout-card-body">

                        <div class="store-payment-option active">

                            <div class="store-payment-radio">
                                <i class="bi bi-check"></i>
                            </div>

                            <div>
                                <h5>SINPE Móvil</h5>
                                <p>
                                    Envía el comprobante después de confirmar el pedido.
                                </p>
                            </div>

                        </div>

                        <div class="store-payment-option">

                            <div class="store-payment-radio"></div>

                            <div>
                                <h5>Transferencia bancaria</h5>
                                <p>
                                    Realiza la transferencia y sube el comprobante.
                                </p>
                            </div>

                        </div>

                        <div class="store-payment-option">

                            <div class="store-payment-radio"></div>

                            <div>
                                <h5>Pago contra entrega</h5>
                                <p>
                                    Disponible únicamente en zonas seleccionadas.
                                </p>
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- RESUMEN --}}
            <div class="col-12 col-lg-5">

                <div class="store-checkout-summary-card">

                    <div class="store-checkout-summary-header">
                        <h2>Resumen del pedido</h2>
                    </div>

                    <div class="store-checkout-products">

                        @foreach($carrito as $item)

                            <div class="store-checkout-product">

                                <div class="store-checkout-product-image">
                                    <img src="{{ $item->imagen }}"
                                         alt="{{ $item->nombre }}">
                                </div>

                                <div class="store-checkout-product-info">

                                    <h5>
                                        {{ $item->nombre }}
                                    </h5>

                                    <span>
                                        Cantidad: {{ $item->cantidad }}
                                    </span>

                                </div>

                                <div class="store-checkout-product-price">
                                    ₡{{ number_format($item->precio, 2) }}
                                </div>

                            </div>

                        @endforeach

                    </div>


                    <div class="store-checkout-cupon">

                        <input type="text"
                               class="form-control store-filter-control"
                               placeholder="Código de cupón">

                        <button class="btn btn-store-outline">
                            Aplicar
                        </button>

                    </div>


                    <div class="store-checkout-totals">

                        <div class="store-checkout-total-row">
                            <span>Subtotal</span>
                            <strong>
                                ₡{{ number_format($subtotal, 2) }}
                            </strong>
                        </div>

                        <div class="store-checkout-total-row">
                            <span>Envío</span>
                            <strong>
                                ₡{{ number_format($envio, 2) }}
                            </strong>
                        </div>

                        <div class="store-checkout-total-row text-success">
                            <span>Descuento</span>
                            <strong>
                                -₡{{ number_format($descuento, 2) }}
                            </strong>
                        </div>

                        <div class="store-checkout-total-row total">
                            <span>Total</span>
                            <strong>
                                ₡{{ number_format($total, 2) }}
                            </strong>
                        </div>

                    </div>


                    <button class="btn btn-store-primary store-checkout-submit">
                        <i class="bi bi-shield-check me-1"></i>
                        Confirmar pedido
                    </button>

                    <div class="store-checkout-security">

                        <div>
                            <i class="bi bi-lock"></i>
                            Compra segura
                        </div>

                        <div>
                            <i class="bi bi-truck"></i>
                            Envío rápido
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection