{{-- resources/views/tienda/pedidos/show.blade.php --}}

@php
    $pedido = (object)[
        'numero_pedido' => 'PED-10003',
        'estado' => 'enviado',
        'estado_label' => 'Enviado',
        'cliente' => 'Cliente Demo 3',
        'telefono' => '88888803',
        'correo' => 'demo3@mail.com',
        'tipo_entrega' => 'Envío a domicilio',
        'provincia' => 'San José',
        'canton' => 'Santa Ana',
        'distrito' => 'Pozos',
        'direccion' => 'Dirección 3',
        'referencia' => 'Casa color blanco, portón negro.',
        'fecha' => '12/04/2026 19:46',
        'subtotal' => 20000,
        'envio' => 2500,
        'descuento' => 0,
        'total' => 22500,
        'metodo_pago' => 'SINPE Móvil',
        'estado_pago' => 'verificado',
        'estado_pago_label' => 'Pago verificado',
        'numero_comprobante' => '12',
        'monto_reportado' => 22500,
        'verificado_en' => '12/04/2026 19:52',
        'notas' => 'Pedido generado correctamente desde tienda online.',
    ];

    $items = collect([
        (object)[
            'nombre' => 'Tenis de Running Runfalcon 5 TR',
            'sku' => 'SKU-DEMO-001',
            'precio' => 45000,
            'cantidad' => 1,
            'total' => 45000,
            'imagen' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?q=80&w=1200&auto=format&fit=crop',
        ],
        (object)[
            'nombre' => 'GORRA DNA AUDI REVOLUT F1 TEAM',
            'sku' => 'SKU-DEMO-002',
            'precio' => 10000,
            'cantidad' => 1,
            'total' => 10000,
            'imagen' => 'https://images.unsplash.com/photo-1521369909029-2afed882baee?q=80&w=1200&auto=format&fit=crop',
        ],
    ]);

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

    $pagoClass = [
        'enviado' => 'is-info',
        'verificado' => 'is-success',
        'rechazado' => 'is-danger',
    ];
@endphp

@extends('tienda.layouts.app')

@section('title', 'Detalle del pedido | Tienda')
@section('meta_description', 'Consulta el detalle completo de tu pedido.')

@section('content')

<section class="store-order-show-page">

    <div class="container py-4 py-lg-5">

        {{-- BREADCRUMB --}}
        <div class="store-detail-breadcrumb mb-4">
            <a href="{{ route('tienda.home') }}">Inicio</a>
            <i class="bi bi-chevron-right"></i>
            <a href="{{ route('tienda.pedidos.mis') }}">Mis pedidos</a>
            <i class="bi bi-chevron-right"></i>
            <span>{{ $pedido->numero_pedido }}</span>
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
                    Revisa los productos comprados, datos de entrega, estado del pago
                    y el resumen completo de tu pedido.
                </p>
            </div>

            <div class="store-order-show-hero-status">
                <span>Estado actual</span>
                <strong>{{ $pedido->estado_label }}</strong>
            </div>

        </div>


        <div class="row g-4 g-xl-5 align-items-start">

            {{-- CONTENIDO PRINCIPAL --}}
            <div class="col-12 col-lg-8">

                {{-- ESTADO --}}
                <div class="store-order-show-status-card mb-4">

                    <div class="store-order-show-status-icon {{ $estadoClass[$pedido->estado] ?? 'is-muted' }}">
                        <i class="bi bi-box-seam"></i>
                    </div>

                    <div>
                        <span class="store-order-status {{ $estadoClass[$pedido->estado] ?? 'is-muted' }}">
                            {{ $pedido->estado_label }}
                        </span>

                        <h2>
                            Pedido {{ strtolower($pedido->estado_label) }}
                        </h2>

                        <p>
                            Este pedido fue creado el <strong>{{ $pedido->fecha }}</strong>.
                            Puedes revisar el proceso completo desde la vista de seguimiento.
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

                        @foreach($items as $item)

                            <article class="store-order-show-product">

                                <div class="store-order-show-product-image">
                                    <img src="{{ $item->imagen }}" alt="{{ $item->nombre }}">
                                </div>

                                <div class="store-order-show-product-info">
                                    <span>{{ $item->sku }}</span>

                                    <h3>
                                        {{ $item->nombre }}
                                    </h3>

                                    <p>
                                        Cantidad: {{ $item->cantidad }} · Precio unitario:
                                        ₡{{ number_format($item->precio, 2) }}
                                    </p>
                                </div>

                                <div class="store-order-show-product-total">
                                    <span>Total línea</span>
                                    <strong>
                                        ₡{{ number_format($item->total, 2) }}
                                    </strong>
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
                                <strong>{{ $pedido->tipo_entrega }}</strong>
                            </div>

                            <div class="store-order-show-info-item">
                                <span>Provincia</span>
                                <strong>{{ $pedido->provincia }}</strong>
                            </div>

                            <div class="store-order-show-info-item">
                                <span>Cantón</span>
                                <strong>{{ $pedido->canton }}</strong>
                            </div>

                            <div class="store-order-show-info-item">
                                <span>Distrito</span>
                                <strong>{{ $pedido->distrito }}</strong>
                            </div>

                        </div>

                        <div class="store-order-show-address-box mt-3">
                            <span>Dirección exacta</span>
                            <strong>{{ $pedido->direccion }}</strong>

                            @if($pedido->referencia)
                                <p>{{ $pedido->referencia }}</p>
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

                            <div class="store-order-payment-icon {{ $pagoClass[$pedido->estado_pago] ?? 'is-muted' }}">
                                <i class="bi bi-receipt"></i>
                            </div>

                            <div class="store-order-payment-content">

                                <span class="store-order-status {{ $pagoClass[$pedido->estado_pago] ?? 'is-muted' }}">
                                    {{ $pedido->estado_pago_label }}
                                </span>

                                <h3>{{ $pedido->metodo_pago }}</h3>

                                <div class="store-order-payment-data">
                                    <div>
                                        <span>Comprobante</span>
                                        <strong>{{ $pedido->numero_comprobante ?? 'Pendiente' }}</strong>
                                    </div>

                                    <div>
                                        <span>Monto reportado</span>
                                        <strong>₡{{ number_format($pedido->monto_reportado, 2) }}</strong>
                                    </div>

                                    <div>
                                        <span>Verificado en</span>
                                        <strong>{{ $pedido->verificado_en ?? 'Pendiente' }}</strong>
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- RESUMEN --}}
            <div class="col-12 col-lg-4">

                <aside class="store-order-show-summary-card">

                    <div class="store-order-show-summary-header">
                        <h2>Resumen</h2>

                        <span class="store-order-status {{ $estadoClass[$pedido->estado] ?? 'is-muted' }}">
                            {{ $pedido->estado_label }}
                        </span>
                    </div>

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
                            <span>Fecha</span>
                            <strong>{{ $pedido->fecha }}</strong>
                        </li>
                    </ul>

                    <div class="store-order-show-total-box">

                        <div>
                            <span>Subtotal</span>
                            <strong>₡{{ number_format($pedido->subtotal, 2) }}</strong>
                        </div>

                        <div>
                            <span>Envío</span>
                            <strong>₡{{ number_format($pedido->envio, 2) }}</strong>
                        </div>

                        <div>
                            <span>Descuento</span>
                            <strong>-₡{{ number_format($pedido->descuento, 2) }}</strong>
                        </div>

                        <div class="total">
                            <span>Total</span>
                            <strong>₡{{ number_format($pedido->total, 2) }}</strong>
                        </div>

                    </div>

                    @if($pedido->notas)
                        <div class="store-order-show-note">
                            <i class="bi bi-info-circle"></i>
                            <span>{{ $pedido->notas }}</span>
                        </div>
                    @endif

                    <div class="d-grid gap-2">

                        <a href="{{ route('tienda.pedidos.seguimiento', ['codigo' => $pedido->numero_pedido]) }}"
                           class="btn btn-store-primary">
                            <i class="bi bi-search me-1"></i>
                            Ver seguimiento
                        </a>

                        <a href="{{ route('tienda.pedidos.mis') }}"
                           class="btn btn-store-outline">
                            Volver a mis pedidos
                        </a>

                    </div>

                </aside>

            </div>

        </div>

    </div>

</section>

@endsection