@extends('admin.layouts.app')

@section('title', 'Detalle Pago Venta Local')

@section('content')

@php
    $item = (object)[
        'id_pago_venta_local' => 15,
        'id_venta_local' => 234,
        'numero_ticket' => 'TCK-000234',
        'cliente' => 'Carlos Ramírez',
        'cajero' => 'Caja 1',
        'metodo' => 'tarjeta',
        'monto' => 85000,
        'referencia' => 'REF-456789',
        'fecha' => now()->subDays(1)->format('Y-m-d'),
        'hora' => now()->format('H:i'),
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
                        <a href="{{ route('admin.pagos-ventas-locales.index') }}">Pagos Ventas Locales</a>
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
                    <small class="text-muted">Información completa del pago en venta local</small>
                </div>

                <a href="{{ route('admin.pagos-ventas-locales.index') }}" 
                   class="btn btn-secondary-custom btn-back">
                    <i class="bx bx-arrow-back"></i> 
                    <span>Volver</span>
                </a>
            </div>

            <hr>

            {{-- 🔥 TICKET FULL WIDTH --}}
            <div class="card border-0 bg-light mb-4 text-center">
                <div class="card-body">
                    <label class="fw-semibold mb-2 d-block">Ticket</label>
                    <div class="fw-bold fs-4">{{ $item->numero_ticket }}</div>
                    <small class="text-muted">ID Venta: {{ $item->id_venta_local }}</small>
                </div>
            </div>

            {{-- 🔥 MÉTODO + MONTO --}}
            <div class="row g-4 mb-4">

                {{-- MÉTODO --}}
                <div class="col-md-6">
                    <div class="highlight-card bg-light">
                        <div class="highlight-icon">
                            <i class="bx bx-credit-card"></i>
                        </div>
                        <div class="highlight-label">Método</div>
                        <div class="highlight-value">

                            @if($item->metodo === 'efectivo')
                                <span class="estado-badge-large estado-verificado w-100 justify-content-center">
                                    <i class="bx bx-money"></i> Efectivo
                                </span>
                            @elseif($item->metodo === 'tarjeta')
                                <span class="estado-badge-large estado-enviado w-100 justify-content-center">
                                    <i class="bx bx-credit-card"></i> Tarjeta
                                </span>
                            @elseif($item->metodo === 'sinpe')
                                <span class="estado-badge-large estado-verificado w-100 justify-content-center">
                                    <i class="bx bx-mobile-alt"></i> SINPE
                                </span>
                            @else
                                <span class="estado-badge-large estado-rechazado w-100 justify-content-center">
                                    <i class="bx bx-layer"></i> Mixto
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
                                ₡{{ number_format($item->monto, 0, '.', ',') }}
                            </span>
                        </div>
                    </div>
                </div>

            </div>

            {{-- CONTENIDO --}}
            <div class="row g-4">

                {{-- IZQUIERDA --}}
                <div class="col-md-6">

                    <div class="card border-0 bg-light mb-3">
                        <div class="card-body">

                            <div class="mb-3">
                                <small class="text-muted">Cliente</small>
                                <div class="fw-semibold">{{ $item->cliente }}</div>
                            </div>

                            <div>
                                <small class="text-muted">Cajero</small>
                                <div class="fw-semibold">{{ $item->cajero }}</div>
                            </div>

                        </div>
                    </div>

                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <small class="text-muted">Fecha</small>
                            <div class="fw-semibold">{{ $item->fecha }}</div>

                            <small class="text-muted">Hora</small>
                            <div class="fw-semibold">{{ $item->hora }}</div>
                        </div>
                    </div>

                </div>

                {{-- DERECHA --}}
                <div class="col-md-6">

                    <div class="card border-0 bg-light">
                        <div class="card-body">

                            <small class="text-muted">Referencia</small>
                            <div class="fw-semibold">
                                {{ $item->referencia ?? '—' }}
                            </div>

                        </div>
                    </div>

                </div>

            </div>

            {{-- BOTONES --}}
            <div class="d-flex justify-content-end gap-3 mt-4">

                <a href="#"
                   class="btn btn-primary-custom">
                    <i class="bx bx-edit"></i>
                    <span>Editar</span>
                </a>
                
                <form action="#"
                      method="POST"
                      onsubmit="return confirm('¿Eliminar este pago?')"
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