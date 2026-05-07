{{-- resources/views/tienda/carrito/index.blade.php --}}

@php

$carrito = collect([
    (object)[
        'nombre' => 'Nike Air Max Urban',
        'marca' => 'Nike',
        'categoria' => 'Tenis',
        'precio' => 45990,
        'cantidad' => 1,
        'stock' => 12,
        'imagen' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?q=80&w=1200&auto=format&fit=crop',
    ],
    (object)[
        'nombre' => 'Smart Watch Active',
        'marca' => 'Samsung',
        'categoria' => 'Wearables',
        'precio' => 68990,
        'cantidad' => 1,
        'stock' => 7,
        'imagen' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?q=80&w=1200&auto=format&fit=crop',
    ],
    (object)[
        'nombre' => 'Audífonos Bluetooth Pro',
        'marca' => 'Sony',
        'categoria' => 'Tecnología',
        'precio' => 32990,
        'cantidad' => 2,
        'stock' => 4,
        'imagen' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?q=80&w=1200&auto=format&fit=crop',
    ],
]);

$subtotal = $carrito->sum(fn($item) => $item->precio * $item->cantidad);
$envio = 3500;
$descuento = 5000;
$total = $subtotal + $envio - $descuento;

@endphp

@extends('tienda.layouts.app')

@section('title', 'Carrito | Tienda')
@section('meta_description', 'Revisa los productos agregados al carrito antes de finalizar tu compra.')

@section('content')

<section class="store-cart-page">

    <div class="container py-4 py-lg-5">

        {{-- BREADCRUMB --}}
        <div class="store-detail-breadcrumb mb-4">
            <a href="{{ route('tienda.home') }}">Inicio</a>
            <i class="bi bi-chevron-right"></i>
            <span>Carrito</span>
        </div>


        {{-- HERO --}}
        <div class="store-cart-hero mb-4 mb-lg-5">

            <div>
                <span class="store-section-eyebrow">
                    Carrito de compra
                </span>

                <h1 class="store-cart-title">
                    Revisa tu pedido
                </h1>

                <p class="store-cart-subtitle mb-0">
                    Confirma cantidades, revisa el resumen y continúa al checkout
                    para finalizar tu compra.
                </p>
            </div>

            <div class="store-cart-hero-counter">
                <strong>{{ $carrito->sum('cantidad') }}</strong>
                <span>artículos</span>
            </div>

        </div>


        @if($carrito->count())

            <div class="row g-4 g-xl-5 align-items-start">

                {{-- LISTADO --}}
                <div class="col-12 col-lg-8">

                    <div class="store-cart-list-card">

                        <div class="store-cart-list-header">
                            <div>
                                <h2>Productos agregados</h2>
                                <p>{{ $carrito->count() }} producto(s) en tu carrito</p>
                            </div>

                            <a href="{{ route('tienda.productos.index') }}" class="btn btn-store-outline d-none d-md-inline-flex">
                                Seguir comprando
                            </a>
                        </div>

                        <div class="store-cart-items">

                            @foreach($carrito as $index => $item)

                                <article class="store-cart-item">

                                    <div class="store-cart-item-image">
                                        <img src="{{ $item->imagen }}" alt="{{ $item->nombre }}">
                                    </div>

                                    <div class="store-cart-item-info">

                                        <div class="store-cart-item-meta">
                                            {{ $item->marca }} · {{ $item->categoria }}
                                        </div>

                                        <h3 class="store-cart-item-title">
                                            {{ $item->nombre }}
                                        </h3>

                                        <div class="store-cart-item-stock">
                                            <i class="bi bi-check-circle"></i>
                                            Disponible · {{ $item->stock }} unidades
                                        </div>

                                        <div class="store-cart-mobile-price">
                                            ₡{{ number_format($item->precio, 2) }}
                                        </div>

                                    </div>

                                    <div class="store-cart-item-controls">

                                        <div class="store-cart-price d-none d-md-block">
                                            ₡{{ number_format($item->precio, 2) }}
                                        </div>

                                        <div class="store-cart-qty-control">
                                            <button type="button" data-cart-qty="minus" data-target="cartQty{{ $index }}">
                                                <i class="bi bi-dash"></i>
                                            </button>

                                            <input type="number"
                                                   id="cartQty{{ $index }}"
                                                   value="{{ $item->cantidad }}"
                                                   min="1"
                                                   max="{{ $item->stock }}">

                                            <button type="button" data-cart-qty="plus" data-target="cartQty{{ $index }}">
                                                <i class="bi bi-plus"></i>
                                            </button>
                                        </div>

                                        <div class="store-cart-line-total">
                                            ₡{{ number_format($item->precio * $item->cantidad, 2) }}
                                        </div>

                                        <button type="button" class="store-cart-remove-btn">
                                            <i class="bi bi-trash3"></i>
                                            <span>Quitar</span>
                                        </button>

                                    </div>

                                </article>

                            @endforeach

                        </div>

                    </div>


                    {{-- BLOQUE AYUDA --}}
                    <div class="store-cart-help-card mt-4">

                        <div class="store-cart-help-icon">
                            <i class="bi bi-shield-check"></i>
                        </div>

                        <div>
                            <h3>Compra segura y organizada</h3>
                            <p>
                                Tus productos se mantienen preparados para continuar al checkout.
                                Luego podrás completar datos de entrega y método de pago.
                            </p>
                        </div>

                    </div>

                </div>


                {{-- RESUMEN --}}
                <div class="col-12 col-lg-4">

                    <aside class="store-cart-summary-card">

                        <div class="store-cart-summary-header">
                            <h2>Resumen</h2>
                            <span>{{ $carrito->sum('cantidad') }} artículos</span>
                        </div>


                        <div class="store-cart-coupon-box">

                            <label class="store-form-label">
                                Cupón de descuento
                            </label>

                            <div class="store-cart-coupon-control">
                                <input type="text"
                                       class="form-control store-filter-control"
                                       placeholder="Código">

                                <button class="btn btn-store-outline">
                                    Aplicar
                                </button>
                            </div>

                        </div>


                        <div class="store-cart-total-list">

                            <div class="store-cart-total-row">
                                <span>Subtotal</span>
                                <strong>₡{{ number_format($subtotal, 2) }}</strong>
                            </div>

                            <div class="store-cart-total-row">
                                <span>Envío estimado</span>
                                <strong>₡{{ number_format($envio, 2) }}</strong>
                            </div>

                            <div class="store-cart-total-row text-success">
                                <span>Descuento</span>
                                <strong>-₡{{ number_format($descuento, 2) }}</strong>
                            </div>

                            <div class="store-cart-total-row total">
                                <span>Total</span>
                                <strong>₡{{ number_format($total, 2) }}</strong>
                            </div>

                        </div>


                        <a href="{{ route('tienda.checkout.index') }}" class="btn btn-store-primary store-cart-checkout-btn">
                            <i class="bi bi-credit-card me-1"></i>
                            Finalizar compra
                        </a>

                        <a href="{{ route('tienda.productos.index') }}" class="btn btn-store-outline store-cart-continue-btn">
                            Seguir comprando
                        </a>


                        <div class="store-cart-summary-benefits">

                            <div>
                                <i class="bi bi-lock"></i>
                                Pago seguro
                            </div>

                            <div>
                                <i class="bi bi-truck"></i>
                                Envío disponible
                            </div>

                            <div>
                                <i class="bi bi-receipt"></i>
                                Pedido rastreable
                            </div>

                        </div>

                    </aside>

                </div>

            </div>

        @else

            {{-- CARRITO VACÍO --}}
            <div class="store-cart-empty-card">

                <div class="store-cart-empty-icon">
                    <i class="bi bi-cart3"></i>
                </div>

                <h2>Tu carrito está vacío</h2>

                <p>
                    Agrega productos desde el catálogo para comenzar tu compra.
                </p>

                <a href="{{ route('tienda.productos.index') }}" class="btn btn-store-primary">
                    Explorar productos
                </a>

            </div>

        @endif

    </div>

</section>

@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const qtyButtons = document.querySelectorAll('[data-cart-qty]');

    qtyButtons.forEach((button) => {
        button.addEventListener('click', function () {
            const targetId = this.dataset.target;
            const action = this.dataset.cartQty;
            const input = document.getElementById(targetId);

            if (!input) return;

            const min = parseInt(input.min || 1);
            const max = parseInt(input.max || 999);
            let value = parseInt(input.value || 1);

            if (action === 'minus') {
                value = Math.max(min, value - 1);
            }

            if (action === 'plus') {
                value = Math.min(max, value + 1);
            }

            input.value = value;
        });
    });

});
</script>
@endpush