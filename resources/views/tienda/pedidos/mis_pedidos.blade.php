{{-- resources/views/tienda/pedidos/mis_pedidos.blade.php --}}
@extends('tienda.layouts.app')

@section('title', 'Mis pedidos | ' . ($configTienda['tienda_nombre'] ?? 'Mi Tienda') . ' | Historial y seguimiento')
@section('meta_description', 'Consulta el historial y el estado de todos tus pedidos realizados en ' . ($configTienda['tienda_nombre'] ?? 'Mi Tienda') . '.')

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
    @endphp

    <section class="store-orders-page">

        <div class="container py-4 py-lg-5">

            <div class="store-detail-breadcrumb mb-4">
                <a href="{{ route('tienda.home') }}">Inicio</a>
                <i class="bi bi-chevron-right"></i>
                <span>Mis pedidos</span>
            </div>

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

                            <input type="text" id="storeOrdersSearch" class="form-control"
                                placeholder="Ej: PED-20260509">
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-3">
                        <label class="store-form-label">
                            Estado
                        </label>

                        <select class="form-select store-filter-control" id="storeOrdersStatus">
                            <option value="">Todos</option>
                            <option value="pendiente_pago">Pendiente de pago</option>
                            <option value="en_revision">En revisión</option>
                            <option value="pagado_verificado">Pago verificado</option>
                            <option value="preparando">Preparando</option>
                            <option value="enviado">Enviado</option>
                            <option value="entregado">Entregado</option>
                            <option value="rechazado">Rechazado</option>
                            <option value="cancelado">Cancelado</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-6 col-lg-3">
                        <a href="{{ route('tienda.productos.index') }}" class="btn btn-store-outline w-100">
                            Seguir comprando
                        </a>
                    </div>

                </div>

            </div>

            @if ($pedidos->count())

                <div class="store-orders-list" id="storeOrdersList">

                    @foreach ($pedidos as $pedido)
                        @php
                            $pago = $pedido->pagoUltimo;

                            $label = $estadoLabel[$pedido->estado] ?? ucfirst($pedido->estado);

                            $metodoPago = $pago?->metodo ? strtoupper($pago->metodo) : 'No registrado';

                            $cantidadProductos = $pedido->detalle->sum('cantidad');

                            $items = $pedido->detalle->pluck('nombre_producto')->filter()->take(3);

                            $busqueda = strtolower(
                                $pedido->numero_pedido .
                                    ' ' .
                                    $pedido->nombre_cliente .
                                    ' ' .
                                    $pedido->telefono_cliente .
                                    ' ' .
                                    $label,
                            );
                        @endphp

                        <article class="store-order-card" data-order-card data-status="{{ $pedido->estado }}"
                            data-search="{{ $busqueda }}">

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
                                            {{ $label }}
                                        </span>
                                    </div>

                                    <div class="store-order-meta">
                                        <span>
                                            <i class="bi bi-calendar3"></i>
                                            {{ $pedido->created_at?->format('d/m/Y') }}
                                        </span>

                                        <span>
                                            <i class="bi bi-credit-card"></i>
                                            {{ $metodoPago }}
                                        </span>

                                        <span>
                                            <i class="bi bi-bag"></i>
                                            {{ $cantidadProductos }} producto(s)
                                        </span>
                                    </div>

                                    <div class="store-order-products">
                                        @forelse($items as $item)
                                            <span>{{ $item }}</span>
                                        @empty
                                            <span>Sin productos registrados</span>
                                        @endforelse

                                        @if ($pedido->detalle->count() > 3)
                                            <span>+{{ $pedido->detalle->count() - 3 }} más</span>
                                        @endif
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

                <div class="store-orders-empty-card d-none" id="storeOrdersNoResults">

                    <div class="store-orders-empty-icon">
                        <i class="bi bi-search"></i>
                    </div>

                    <h2>No encontramos pedidos</h2>

                    <p>
                        Intenta cambiar el texto de búsqueda o seleccionar otro estado.
                    </p>

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

@push('scripts')
    <script src="{{ asset('assets/js/modules/mis_pedidos_tienda.js') }}"></script>
@endpush
