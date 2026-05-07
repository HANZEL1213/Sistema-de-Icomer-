{{-- resources/views/tienda/pedidos/seguimiento.blade.php --}}

@php
    $pedido = (object)[
        'numero_pedido' => 'PED-10003',
        'estado' => 'enviado',
        'estado_label' => 'Enviado',
        'cliente' => 'Cliente Demo 3',
        'telefono' => '88888803',
        'correo' => 'demo3@mail.com',
        'tipo_entrega' => 'Envío a domicilio',
        'direccion' => 'San José, Santa Ana, Pozos, Dirección 3',
        'subtotal' => 20000,
        'envio' => 2500,
        'descuento' => 0,
        'total' => 22500,
        'fecha' => '12/04/2026 19:46',
        'metodo_pago' => 'SINPE Móvil',
        'numero_comprobante' => '12',
        'monto_reportado' => 22500,
        'estado_pago' => 'verificado',
        'estado_pago_label' => 'Comprobante verificado',
        'verificado_en' => '12/04/2026 19:52',
    ];

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

@section('title', 'Seguimiento del pedido | Tienda')
@section('meta_description', 'Consulta el estado y avance de tu pedido.')

@section('content')

<section class="store-tracking-page">

    <div class="container py-4 py-lg-5">

        {{-- BREADCRUMB --}}
        <div class="store-detail-breadcrumb mb-4">
            <a href="{{ route('tienda.home') }}">Inicio</a>
            <i class="bi bi-chevron-right"></i>
            <a href="{{ route('tienda.pedidos.mis') }}">Mis pedidos</a>
            <i class="bi bi-chevron-right"></i>
            <span>Seguimiento</span>
        </div>

        {{-- HERO --}}
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

            {{-- COLUMNA PRINCIPAL --}}
            <div class="col-12 col-lg-8">

                {{-- ESTADO ACTUAL --}}
                <div class="store-tracking-status-card mb-4">

                    <div class="store-tracking-status-icon {{ $estadoClass[$pedido->estado] ?? 'is-muted' }}">
                        <i class="bi bi-truck"></i>
                    </div>

                    <div class="store-tracking-status-content">
                        <span class="store-order-status {{ $estadoClass[$pedido->estado] ?? 'is-muted' }}">
                            {{ $pedido->estado_label }}
                        </span>

                        <h2>
                            Tu pedido va en proceso: {{ $pedido->estado_label }}
                        </h2>

                        <p>
                            Última actualización registrada para el pedido
                            <strong>{{ $pedido->numero_pedido }}</strong>.
                        </p>
                    </div>

                </div>


                {{-- TIMELINE --}}
                <div class="store-tracking-card mb-4">

                    <div class="store-tracking-card-header">
                        <h2>
                            <i class="bi bi-list-check"></i>
                            Proceso del pedido
                        </h2>
                    </div>

                    <div class="store-tracking-timeline">

                        @foreach($pasos as $index => $paso)

                            @php
                                $numeroPaso = $ordenEstados[$paso['key']];
                                $completado = $numeroPaso < $estadoActual;
                                $actual = $numeroPaso === $estadoActual;
                            @endphp

                            <div class="store-tracking-step {{ $completado ? 'is-completed' : '' }} {{ $actual ? 'is-current' : '' }}">

                                <div class="store-tracking-step-marker">
                                    <i class="bi {{ $paso['icono'] }}"></i>
                                </div>

                                <div class="store-tracking-step-content">
                                    <h3>{{ $paso['titulo'] }}</h3>
                                    <p>{{ $paso['descripcion'] }}</p>

                                    @if($actual)
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


                {{-- PAGO / COMPROBANTE --}}
                <div class="store-tracking-card">

                    <div class="store-tracking-card-header">
                        <h2>
                            <i class="bi bi-credit-card"></i>
                            Estado del comprobante
                        </h2>
                    </div>

                    <div class="store-tracking-payment-box">

                        <div class="store-tracking-payment-icon {{ $pagoClass[$pedido->estado_pago] ?? 'is-muted' }}">
                            <i class="bi bi-file-earmark-check"></i>
                        </div>

                        <div class="store-tracking-payment-content">
                            <span class="store-order-status {{ $pagoClass[$pedido->estado_pago] ?? 'is-muted' }}">
                                {{ $pedido->estado_pago_label }}
                            </span>

                            <h3>{{ $pedido->metodo_pago }}</h3>

                            <p>
                                Comprobante: <strong>{{ $pedido->numero_comprobante ?? 'Pendiente' }}</strong>
                            </p>

                            <p>
                                Monto reportado:
                                <strong>₡{{ number_format($pedido->monto_reportado, 2) }}</strong>
                            </p>

                            @if($pedido->estado_pago === 'verificado')
                                <div class="store-tracking-payment-note is-success">
                                    <i class="bi bi-check-circle"></i>
                                    Pago verificado el {{ $pedido->verificado_en }}.
                                </div>
                            @elseif($pedido->estado_pago === 'rechazado')
                                <div class="store-tracking-payment-note is-danger">
                                    <i class="bi bi-x-circle"></i>
                                    El comprobante fue rechazado. Debes enviar uno nuevo.
                                </div>
                            @else
                                <div class="store-tracking-payment-note is-info">
                                    <i class="bi bi-clock"></i>
                                    Tu comprobante está pendiente de revisión.
                                </div>
                            @endif
                        </div>

                    </div>

                </div>

            </div>


            {{-- RESUMEN --}}
            <div class="col-12 col-lg-4">

                <aside class="store-tracking-summary-card">

                    <div class="store-tracking-summary-header">
                        <h2>Resumen</h2>

                        <span class="store-order-status {{ $estadoClass[$pedido->estado] ?? 'is-muted' }}">
                            {{ $pedido->estado_label }}
                        </span>
                    </div>

                    <ul class="store-confirmation-list">
                        <li>
                            <span>Pedido</span>
                            <strong>{{ $pedido->numero_pedido }}</strong>
                        </li>

                        <li>
                            <span>Cliente</span>
                            <strong>{{ $pedido->cliente }}</strong>
                        </li>

                        <li>
                            <span>Teléfono</span>
                            <strong>{{ $pedido->telefono }}</strong>
                        </li>

                        <li>
                            <span>Entrega</span>
                            <strong>{{ $pedido->tipo_entrega }}</strong>
                        </li>

                        <li>
                            <span>Dirección</span>
                            <strong>{{ $pedido->direccion }}</strong>
                        </li>

                        <li>
                            <span>Fecha</span>
                            <strong>{{ $pedido->fecha }}</strong>
                        </li>
                    </ul>

                    <div class="store-tracking-total-box">

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

                    <div class="d-grid gap-2">

                        <a href="{{ route('tienda.pedidos.show', $pedido->numero_pedido) }}"
                           class="btn btn-store-primary">
                            Ver detalle completo
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