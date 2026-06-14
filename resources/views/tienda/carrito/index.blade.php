{{-- resources/views/tienda/carrito/index.blade.php --}}
@extends('tienda.layouts.app')

@section('title', 'Carrito | Tienda')
@section('meta_description', 'Revisa los productos agregados al carrito antes de finalizar tu compra.')

@section('content')

    <section class="store-cart-page">

        <div class="container py-4 py-lg-5">

            <div class="store-detail-breadcrumb mb-4">
                <a href="{{ route('tienda.home') }}">Inicio</a>
                <i class="bi bi-chevron-right"></i>
                <span>Carrito</span>
            </div>

            <div class="store-cart-hero mb-4 mb-lg-5">
                <div>
                    <span class="store-section-eyebrow">Carrito de compra</span>

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

            @if ($carrito->count())

                <div class="row g-4 g-xl-5 align-items-start">

                    <div class="col-12 col-lg-8">

                        <div class="store-cart-list-card">

                            <div class="store-cart-list-header">
                                <div>
                                    <h2>Productos agregados</h2>
                                    <p>{{ $carrito->count() }} producto(s) en tu carrito</p>
                                </div>

                                <a href="{{ route('tienda.productos.index') }}"
                                    class="btn btn-store-outline d-none d-md-inline-flex">
                                    Seguir comprando
                                </a>
                            </div>

                            <div class="store-cart-items">

                                @foreach ($carrito as $cartKey => $item)
                                    @php
                                        $imagen = $item['imagen']
                                            ? asset('storage/' . $item['imagen'])
                                            : asset('assets/img/no-image.png');

                                        $tienePromo = $item['tiene_promocion'] ?? false;
                                        $precioVenta = (float) ($item['precio'] ?? 0);
                                        $precioNormal = (float) ($item['precio_normal'] ?? $precioVenta);
                                        $porcentaje = (int) ($item['porcentaje_descuento'] ?? 0);
                                        $ahorro = (float) ($item['ahorro'] ?? 0);
                                    @endphp

                                    <article class="store-cart-item">

                                        <div class="store-cart-item-image">
                                            <img src="{{ $imagen }}" alt="{{ $item['nombre'] }}">
                                        </div>

                                        <div class="store-cart-item-info">

                                            <div class="store-cart-item-meta">
                                                {{ $item['marca'] ?? 'Sin marca' }}
                                                ·
                                                {{ $item['categoria'] ?? 'Sin categoría' }}
                                            </div>

                                            <h3 class="store-cart-item-title">
                                                <a href="{{ route('tienda.productos.show', $item['slug']) }}">
                                                    {{ $item['nombre'] }}
                                                </a>
                                            </h3>

                                            @if (!empty($item['variante']))
                                                <div class="text-muted small mt-1">
                                                    Variante: <strong>{{ $item['variante'] }}</strong>
                                                </div>
                                            @endif

                                            @if ($tienePromo)
                                                <span class="badge bg-danger mb-2">
                                                    Promoción aplicada
                                                </span>
                                            @endif

                                            <div class="store-cart-item-stock">
                                                <i class="bi bi-check-circle"></i>
                                                Disponible · {{ $item['stock'] }} unidades
                                            </div>

                                            <div class="store-cart-mobile-price">
                                                @if ($tienePromo)
                                                    <span class="badge bg-danger mb-1">
                                                        -{{ $porcentaje }}% OFF
                                                    </span>

                                                    <div class="text-muted text-decoration-line-through small">
                                                        ₡{{ number_format($precioNormal, 2) }}
                                                    </div>

                                                    <strong class="text-danger">
                                                        ₡{{ number_format($precioVenta, 2) }}
                                                    </strong>
                                                @else
                                                    ₡{{ number_format($precioVenta, 2) }}
                                                @endif
                                            </div>

                                        </div>

                                        <div class="store-cart-item-controls">

                                            <div class="store-cart-price d-none d-md-block">
                                                @if ($tienePromo)
                                                    <span class="badge bg-danger mb-1">
                                                        -{{ $porcentaje }}% OFF
                                                    </span>

                                                    <div class="text-muted text-decoration-line-through small">
                                                        ₡{{ number_format($precioNormal, 2) }}
                                                    </div>

                                                    <strong class="text-danger">
                                                        ₡{{ number_format($precioVenta, 2) }}
                                                    </strong>
                                                @else
                                                    ₡{{ number_format($precioVenta, 2) }}
                                                @endif
                                            </div>

                                            <form action="{{ route('tienda.carrito.actualizar', $item['id_producto']) }}"
                                                method="POST" class="store-cart-qty-form">

                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="cart_key"
                                                    value="{{ $item['cart_key'] ?? $cartKey }}">
                                                <div class="store-cart-qty-control">

                                                    <button type="button" data-cart-qty="minus"
                                                        data-target="cartQty{{ md5($item['cart_key'] ?? $cartKey) }}">
                                                        <i class="bi bi-dash"></i>
                                                    </button>

                                                    <input type="number" name="cantidad"
                                                        id="cartQty{{ md5($item['cart_key'] ?? $cartKey) }}"
                                                        value="{{ $item['cantidad'] }}" min="1"
                                                        max="{{ $item['stock'] }}">

                                                    <button type="button" data-cart-qty="plus"
                                                        data-target="cartQty{{ md5($item['cart_key'] ?? $cartKey) }}">
                                                        <i class="bi bi-plus"></i>
                                                    </button>

                                                </div>

                                            </form>

                                            <div class="store-cart-line-total">
                                                ₡{{ number_format($precioVenta * $item['cantidad'], 2) }}
                                            </div>

                                            <form action="{{ route('tienda.carrito.eliminar', $item['id_producto']) }}"
                                                method="POST" class="store-cart-remove-form">

                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="cart_key"
                                                    value="{{ $item['cart_key'] ?? $cartKey }}">

                                                <button type="submit" class="store-cart-remove-btn">
                                                    <i class="bi bi-trash3"></i>
                                                    <span>Borrar</span>
                                                </button>

                                            </form>

                                        </div>

                                    </article>
                                @endforeach

                            </div>

                        </div>

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

                    <div class="col-12 col-lg-4">

                        <aside class="store-cart-summary-card">

                            <div class="store-cart-summary-header">
                                <h2>Resumen</h2>

                                <span>
                                    {{ $carrito->sum('cantidad') }} artículos
                                </span>
                            </div>

                            {{-- CUPÓN --}}
                            <div class="store-cart-coupon-box">

                                <label class="store-form-label">
                                    Cupón de descuento
                                </label>

                                @if ($cuponAplicado)

                                    <div class="bg-light rounded-4 p-3 border">

                                        <div class="d-flex justify-content-between align-items-start gap-3">

                                            <div>

                                                <span class="badge bg-success mb-2">
                                                    Cupón aplicado
                                                </span>

                                                <h6 class="fw-bold mb-1">
                                                    {{ $cuponAplicado['codigo'] }}
                                                </h6>

                                                <small class="text-muted d-block">
                                                    Descuento aplicado:
                                                    ₡{{ number_format($descuento, 2) }}
                                                </small>

                                                @if ($cuponAplicado['tipo'] === 'porcentaje')
                                                    <small class="text-success">
                                                        {{ number_format($cuponAplicado['valor'], 0) }}% OFF
                                                    </small>
                                                @else
                                                    <small class="text-success">
                                                        ₡{{ number_format($cuponAplicado['valor'], 2) }} OFF
                                                    </small>
                                                @endif

                                            </div>

                                            <form action="{{ route('tienda.carrito.cupon.eliminar') }}" method="POST">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn btn-store-outline btn-sm">

                                                    <i class="bi bi-x-lg"></i>

                                                </button>

                                            </form>

                                        </div>

                                    </div>
                                @else
                                    <form action="{{ route('tienda.carrito.cupon.aplicar') }}" method="POST">

                                        @csrf

                                        <div class="store-cart-coupon-control">

                                            <input type="text" name="codigo_cupon" value="{{ old('codigo_cupon') }}"
                                                class="form-control store-filter-control @error('codigo_cupon') is-invalid @enderror"
                                                placeholder="Ingresa tu cupón" autocomplete="off">

                                            <button class="btn btn-store-outline" type="submit">
                                                Aplicar
                                            </button>

                                        </div>

                                        @error('codigo_cupon')
                                            <div class="text-danger small mt-2">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </form>

                                @endif

                            </div>

                            {{-- TOTALES --}}
                            <div class="store-cart-total-list">

                                <div class="store-cart-total-row">
                                    <span>Subtotal</span>

                                    <strong>
                                        ₡{{ number_format($subtotal, 2) }}
                                    </strong>
                                </div>

                                <div class="store-cart-total-row">
                                    <span>Envío</span>

                                    <strong>
                                        Se calcula en checkout
                                    </strong>
                                </div>

                                <div class="store-cart-total-row text-success">
                                    <span>Cupon de Descuento</span>

                                    <strong>
                                        -₡{{ number_format($descuento, 2) }}
                                    </strong>
                                </div>

                                <div class="store-cart-total-row total">
                                    <span>Total estimado</span>

                                    <strong>
                                        ₡{{ number_format($total, 2) }}
                                    </strong>
                                </div>

                            </div>

                            <a href="{{ route('tienda.checkout.index') }}"
                                class="btn btn-store-primary store-cart-checkout-btn">

                                <i class="bi bi-credit-card me-1"></i>

                                Finalizar compra

                            </a>

                            <a href="{{ route('tienda.productos.index') }}"
                                class="btn btn-store-outline store-cart-continue-btn">

                                Seguir comprando

                            </a>

                            <form action="{{ route('tienda.carrito.vaciar') }}" method="POST">

                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-store-danger w-100 mt-2">

                                    Vaciar carrito

                                </button>

                            </form>

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
        document.addEventListener('DOMContentLoaded', function() {

            const qtyButtons = document.querySelectorAll('[data-cart-qty]');

            qtyButtons.forEach((button) => {

                button.addEventListener('click', function() {

                    const targetId = this.dataset.target;
                    const action = this.dataset.cartQty;

                    const input = document.getElementById(targetId);

                    if (!input) return;

                    const form = input.closest('form');

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

                    form.submit();

                });

            });

            const qtyInputs = document.querySelectorAll('.store-cart-qty-control input');

            qtyInputs.forEach((input) => {

                input.addEventListener('change', function() {

                    const form = this.closest('form');

                    form.submit();

                });

            });

        });
    </script>
@endpush
