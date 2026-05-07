{{-- resources/views/tienda/pedidos/mis_pedidos.blade.php --}}

@php

$pedidos = collect([
    (object)[
        'numero_pedido' => 'PED-2026-00018',
        'estado' => 'pendiente_pago',
        'estado_label' => 'Pendiente de pago',
        'fecha' => '07/05/2026',
        'total' => 117480,
        'metodo_pago' => 'SINPE Móvil',
        'cantidad_productos' => 3,
        'items' => [
            'Nike Air Max Urban',
            'Smart Watch Active',
            'Audífonos Bluetooth Pro',
        ],
    ],
    (object)[
        'numero_pedido' => 'PED-2026-00017',
        'estado' => 'en_revision',
        'estado_label' => 'Pago en revisión',
        'fecha' => '05/05/2026',
        'total' => 68990,
        'metodo_pago' => 'Transferencia',
        'cantidad_productos' => 1,
        'items' => [
            'Smart Watch Active',
        ],
    ],
    (object)[
        'numero_pedido' => 'PED-2026-00016',
        'estado' => 'preparando',
        'estado_label' => 'Preparando',
        'fecha' => '02/05/2026',
        'total' => 45990,
        'metodo_pago' => 'SINPE Móvil',
        'cantidad_productos' => 1,
        'items' => [
            'Nike Air Max Urban',
        ],
    ],
    (object)[
        'numero_pedido' => 'PED-2026-00015',
        'estado' => 'entregado',
        'estado_label' => 'Entregado',
        'fecha' => '28/04/2026',
        'total' => 32990,
        'metodo_pago' => 'Pago contra entrega',
        'cantidad_productos' => 1,
        'items' => [
            'Audífonos Bluetooth Pro',
        ],
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

@endphp

@extends('tienda.layouts.app')

@section('title', 'Mis pedidos | Tienda')
@section('meta_description', 'Consulta el historial y seguimiento de tus pedidos.')

@section('content')

<section class="store-orders-page">

    <div class="container py-4 py-lg-5">

        {{-- BREADCRUMB --}}
        <div class="store-detail-breadcrumb mb-4">
            <a href="{{ route('tienda.home') }}">Inicio</a>
            <i class="bi bi-chevron-right"></i>
            <span>Mis pedidos</span>
        </div>


        {{-- HERO --}}
        <div class="store-orders-hero mb-4 mb-lg-5">

            <div>
                <span class="store-section-eyebrow">
                    Pedidos
                </span>

                <h1 class="store-orders-title">
                    Mis pedidos
                </h1>

                <p class="store-orders-subtitle mb-0">
                    Consulta el estado de tus compras, revisa el detalle del pedido
                    y dale seguimiento al proceso de entrega.
                </p>
            </div>

            <div class="store-orders-hero-counter">
                <strong>{{ $pedidos->count() }}</strong>
                <span>pedidos</span>
            </div>

        </div>


        {{-- BUSCADOR / FILTROS --}}
        <div class="store-orders-filter-card mb-4">

            <div class="row g-3 align-items-end">

                <div class="col-12 col-lg-6">
                    <label class="store-form-label">
                        Buscar pedido
                    </label>

                    <div class="input-group store-search-group">
                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>

                        <input type="text"
                               class="form-control"
                               placeholder="Ej: PED-2026-00018">

                        <button class="btn btn-store-primary">
                            Buscar
                        </button>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <label class="store-form-label">
                        Estado
                    </label>

                    <select class="form-select store-filter-control">
                        <option>Todos</option>
                        <option>Pendiente de pago</option>
                        <option>En revisión</option>
                        <option>Preparando</option>
                        <option>Enviado</option>
                        <option>Entregado</option>
                    </select>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <a href="{{ route('tienda.productos.index') }}"
                       class="btn btn-store-outline w-100">
                        Seguir comprando
                    </a>
                </div>

            </div>

        </div>


        {{-- LISTADO --}}
        @if($pedidos->count())

            <div class="store-orders-list">

                @foreach($pedidos as $pedido)

                    <article class="store-order-card">

                        <div class="store-order-main">

                            <div class="store-order-icon">
                                <i class="bi bi-box-seam"></i>
                            </div>

                            <div class="store-order-info">

                                <div class="store-order-topline">
                                    <h2>
                                        {{ $pedido->numero_pedido }}
                                    </h2>

                                    <span class="store-order-status {{ $estadoClass[$pedido->estado] ?? 'is-muted' }}">
                                        {{ $pedido->estado_label }}
                                    </span>
                                </div>

                                <div class="store-order-meta">
                                    <span>
                                        <i class="bi bi-calendar3"></i>
                                        {{ $pedido->fecha }}
                                    </span>

                                    <span>
                                        <i class="bi bi-credit-card"></i>
                                        {{ $pedido->metodo_pago }}
                                    </span>

                                    <span>
                                        <i class="bi bi-bag"></i>
                                        {{ $pedido->cantidad_productos }} producto(s)
                                    </span>
                                </div>

                                <div class="store-order-products">
                                    @foreach($pedido->items as $item)
                                        <span>{{ $item }}</span>
                                    @endforeach
                                </div>

                            </div>

                        </div>


                        <div class="store-order-side">

                            <div class="store-order-total">
                                <span>Total</span>
                                <strong>
                                    ₡{{ number_format($pedido->total, 2) }}
                                </strong>
                            </div>

                            <div class="store-order-actions">

                                <a href="{{ route('tienda.pedidos.show', $pedido->numero_pedido) }}"
                                   class="btn btn-store-outline">
                                    Ver detalle
                                </a>

                                <a href="{{ route('tienda.pedidos.seguimiento', ['codigo' => $pedido->numero_pedido]) }}"
                                   class="btn btn-store-primary">
                                    Seguimiento
                                </a>

                            </div>

                        </div>

                    </article>

                @endforeach

            </div>

        @else

            <div class="store-orders-empty-card">

                <div class="store-orders-empty-icon">
                    <i class="bi bi-box-seam"></i>
                </div>

                <h2>No tienes pedidos todavía</h2>

                <p>
                    Cuando realices una compra, aquí podrás consultar el estado
                    y el seguimiento del pedido.
                </p>

                <a href="{{ route('tienda.productos.index') }}" class="btn btn-store-primary">
                    Explorar productos
                </a>

            </div>

        @endif

    </div>

</section>

@endsection