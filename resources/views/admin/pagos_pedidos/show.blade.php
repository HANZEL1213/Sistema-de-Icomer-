@extends('admin.layouts.app')

@section('title', 'Detalle Pago Pedido')

@section('content')

@php
    $item = (object)[
        'id_pago_pedido' => 15,
        'id_pedido' => 234,
        'numero_pedido' => 'PED-000015',
        'cliente' => 'Carlos Ramírez',
        'metodo' => 'sinpe',
        'intento' => 2,
        'es_ultimo' => 1,
        'numero_comprobante' => 'CMP-456789',
        'monto_reportado' => 85000,
        'moneda' => 'CRC',
        'estado' => 'rechazado',
        'enviado_en_fecha' => now()->subDays(2)->format('Y-m-d'),
        'enviado_en_hora' => now()->format('H:i'),
        'verificador' => 'Admin General',
        'motivo_rechazo' => 'El monto no coincide con el comprobante',
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
                        <a href="{{ route('admin.pagos-pedidos.index') }}">Pagos de Pedidos</a>
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
                    <h4 class="fw-bold text-uppercase mb-1">Detalle del Pago</h4>
                    <small class="text-muted">Información completa del pago</small>
                </div>

                <a href="{{ route('admin.pagos-pedidos.index') }}" 
                   class="btn btn-secondary-custom btn-back">
                    <i class="bx bx-arrow-back"></i> 
                    <span>Volver</span>
                </a>
            </div>

            <hr>

            {{-- 🔥 PEDIDO ARRIBA FULL WIDTH --}}
            <div class="card border-0 bg-light mb-4 text-center">
                <div class="card-body">
                    <label class="fw-semibold mb-2 d-block">Pedido</label>
                    <div class="fw-bold fs-4">{{ $item->numero_pedido }}</div>
                    <small class="text-muted">ID: {{ $item->id_pedido }}</small>
                </div>
            </div>

            {{-- 🔥 ESTADO + MONTO (MISMO ESTÁNDAR QUE MOVIMIENTO) --}}
            <div class="row g-4 mb-4">

                {{-- ESTADO --}}
                <div class="col-md-6">
                    <div class="highlight-card bg-light">
                        <div class="highlight-icon">
                            <i class="bx bx-check-circle"></i>
                        </div>
                        <div class="highlight-label">Estado</div>
                        <div class="highlight-value">

                            @if($item->estado === 'verificado')
                                <span class="estado-badge-large estado-verificado w-100 justify-content-center">
                                    <i class="bx bx-check-circle"></i> Verificado
                                </span>
                            @elseif($item->estado === 'rechazado')
                                <span class="estado-badge-large estado-rechazado w-100 justify-content-center">
                                    <i class="bx bx-x-circle"></i> Rechazado
                                </span>
                            @else
                                <span class="estado-badge-large estado-enviado w-100 justify-content-center">
                                    <i class="bx bx-time-five"></i> Enviado
                                </span>
                            @endif

                        </div>
                    </div>
                </div>

                {{-- MONTO --}}
                <div class="col-md-6">
                    <div class="highlight-card bg-light">
                        <div class="highlight-icon">
                            <i class="bx bx-money"></i>
                        </div>
                        <div class="highlight-label">Monto</div>
                        <div class="highlight-value">
                            <span class="monto-badge w-100 justify-content-center">
                            ₡{{ number_format($item->monto_reportado, 0, '.', ',') }}
                            </span>
                        </div>
                    </div>
                </div>

            </div>

            {{-- RESTO --}}
            <div class="row g-4">

                <div class="col-md-6">

                    <div class="card border-0 bg-light mb-3">
                        <div class="card-body">

                            <div class="mb-3">
                                <small class="text-muted">Cliente</small>
                                <div class="fw-semibold">{{ $item->cliente }}</div>
                            </div>

                            <div>
                                <small class="text-muted">Método</small>
                                <div class="fw-semibold">SINPE</div>
                            </div>

                        </div>
                    </div>

                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <small class="text-muted">Fecha envío</small>
                            <div class="fw-semibold">{{ $item->enviado_en_fecha }}</div>

                            <small class="text-muted">Hora</small>
                            <div class="fw-semibold">{{ $item->enviado_en_hora }}</div>
                        </div>
                    </div>

                </div>

                <div class="col-md-6">

                    <div class="card border-0 bg-light mb-3">
                        <div class="card-body">

                            <div class="mb-3">
                                <small class="text-muted">Intento</small>
                                <div class="fw-semibold">
                                    {{ $item->intento }}
                                    @if($item->es_ultimo)
                                        <small class="text-muted">(Último)</small>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted">Comprobante</small>
                                <div class="fw-semibold">{{ $item->numero_comprobante }}</div>
                            </div>

                            @if($item->verificador)
                                <small class="text-muted">Verificado por</small>
                                <div>{{ $item->verificador }}</div>
                            @endif

                        </div>
                    </div>

                    @if($item->estado === 'rechazado')
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <label class="fw-semibold mb-2 text-danger">Motivo</label>
                            <div class="text-muted">{{ $item->motivo_rechazo }}</div>
                        </div>
                    </div>
                    @endif

                </div>

            </div>

              {{-- 🔥 BOTONES (MISMO ESTÁNDAR GLOBAL) --}}
            <div class="d-flex justify-content-end gap-3 mt-4">

                <a href="#"
                   class="btn btn-primary-custom">
                    <i class="bx bx-edit"></i>
                    <span>Editar</span>
                </a>
                
                <form action="#"
                      method="POST"
                      onsubmit="return confirm('¿Eliminar este movimiento?')"
                      style="display:inline;">
                    @csrf
                    @method('DELETE')

                    <button type="submit" class="btn btn-danger-custom">
                        <i class="bx bx-trash"></i>
                        <span>Eliminar</span>
                    </button>
                </form>

            </div>

        </div>
    </div>

</div>

@endsection