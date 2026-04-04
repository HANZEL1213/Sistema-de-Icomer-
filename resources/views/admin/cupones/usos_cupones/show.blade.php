@extends('admin.layouts.app')

@section('title', 'Detalle Uso de Cupón')

@section('content')

@php
    $uso = (object)[
        'id_uso_cupon'    => 12,
        'id_cupon'        => 5,
        'codigo_cupon'    => 'BLACK20',
        'id_pedido'       => 245,
        'numero_pedido'   => 'PED-000245',
        'usuario_nombre'  => 'Carlos Ramírez',
        'id_usuario'      => 8,
        'correo_invitado' => null,
        'monto_descuento' => 15000,
        'usado_fecha'     => now()->format('Y-m-d'),
        'usado_hora'      => now()->format('H:i'),
    ];
@endphp

<div class="page-content">

    {{-- Breadcrumb --}}
    <div class="page-breadcrumb d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav>
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}">
                            <i class="bx bx-home-alt"></i>
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.usos-cupones.index') }}">
                            Usos de Cupones
                        </a>
                    </li>
                    <li class="breadcrumb-item active">Detalle</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card card-index">
        <div class="card-body">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="fw-bold text-uppercase mb-1">Detalle del Uso</h4>
                    <small class="text-muted">Información del cupón aplicado</small>
                </div>

                <a href="{{ route('admin.usos-cupones.index') }}" 
                   class="btn btn-secondary-custom btn-back">
                    <i class="bx bx-arrow-back"></i>
                    <span>Volver</span>
                </a>
            </div>

            <hr>

            {{-- CUPÓN + PEDIDO DESTACADO --}}
            <div class="row g-4 mb-4">

                <div class="col-md-6">
                    <div class="highlight-card bg-light text-center">
                        <div class="highlight-icon">
                            <i class="bx bx-purchase-tag"></i>
                        </div>
                        <div class="highlight-label">Cupón</div>
                        <div class="highlight-value">
                            <span class="fw-bold fs-5">{{ $uso->codigo_cupon }}</span>
                            <small class="d-block text-muted">ID: {{ $uso->id_cupon }}</small>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="highlight-card bg-light text-center">
                        <div class="highlight-icon">
                            <i class="bx bx-receipt"></i>
                        </div>
                        <div class="highlight-label">Pedido</div>
                        <div class="highlight-value">
                            <span class="fw-bold fs-5">{{ $uso->numero_pedido }}</span>
                            <small class="d-block text-muted">ID: {{ $uso->id_pedido }}</small>
                        </div>
                    </div>
                </div>

            </div>

            {{-- RESTO --}}
            <div class="row g-4">

                {{-- IZQUIERDA --}}
                <div class="col-md-6">

                    <div class="card border-0 bg-light mb-3">
                        <div class="card-body">

                            <small class="text-muted">Usuario</small>

                            @if($uso->usuario_nombre)
                                <div class="fw-semibold">{{ $uso->usuario_nombre }}</div>
                                <small class="text-muted">ID: {{ $uso->id_usuario }}</small>
                            @else
                                <div class="fw-semibold">Cliente invitado</div>
                                <small class="text-muted">{{ $uso->correo_invitado }}</small>
                            @endif

                        </div>
                    </div>

                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <small class="text-muted">Fecha de uso</small>
                            <div class="fw-semibold">{{ $uso->usado_fecha }}</div>

                            <small class="text-muted">Hora</small>
                            <div class="fw-semibold">{{ $uso->usado_hora }}</div>
                        </div>
                    </div>

                </div>

                {{-- DERECHA --}}
                <div class="col-md-6">

                    <div class="card border-0 bg-light">
                        <div class="card-body text-center">

                            <label class="fw-semibold mb-2 d-block">Descuento aplicado</label>

                            <span class="monto-badge w-100 justify-content-center">
                                ₡{{ number_format($uso->monto_descuento, 0, '.', ',') }}
                            </span>

                        </div>
                    </div>

                </div>

            </div>

          

        </div>
    </div>

</div>

@endsection