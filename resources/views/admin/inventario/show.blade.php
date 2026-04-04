{{-- resources/views/admin/inventario/show.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Detalle Movimiento')

@section('content')

    @php
        $tipoLabel = match ($item->tipo) {
            'entrada' => 'Entrada',
            'salida' => 'Salida',
            default => 'Ajuste',
        };

        $tipoIcon = match ($item->tipo) {
            'entrada' => 'bx-log-in-circle',
            'salida' => 'bx-log-out-circle',
            default => 'bx-adjust',
        };

        $tipoClass = match ($item->tipo) {
            'entrada' => 'estado-verificado',
            'salida' => 'estado-rechazado',
            default => 'estado-enviado',
        };

        $referenciaTitulo = 'Manual';
        $referenciaValor = 'Sin referencia asociada';

        if ($item->pedido) {
            $referenciaTitulo = 'Pedido';
            $referenciaValor = '#' . $item->id_pedido;
        } elseif ($item->ventaLocal) {
            $referenciaTitulo = 'Venta local';
            $referenciaValor = '#' . $item->id_venta_local;
        }
    @endphp

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
                        <a href="{{ route('admin.inventario-movimientos.index') }}">Inventario</a>
                    </li>
                    <li class="breadcrumb-item active">Detalle</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card card-form">
        <div class="card-body">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
                <div>
                    <h4 class="fw-bold text-uppercase mb-1">Detalle del Movimiento</h4>
                    <small class="text-muted">Información completa del movimiento de inventario</small>
                </div>

                <a href="{{ route('admin.inventario-movimientos.index') }}" class="btn btn-secondary-custom btn-back">
                    <i class="bx bx-arrow-back"></i>
                    <span>Volver</span>
                </a>
            </div>

            <hr>

            {{-- PRODUCTO --}}
            <div class="card border-0 bg-light mb-4 text-center">
                <div class="card-body">
                    <label class="fw-semibold mb-2 d-block">Producto</label>
                    <div class="fw-bold fs-4">{{ $item->producto?->nombre ?? 'Producto no disponible' }}</div>
                    <small class="text-muted">{{ $item->producto?->sku ?? 'Sin SKU' }}</small>
                </div>
            </div>

            {{-- TIPO + CANTIDAD --}}
            <div class="row g-4 mb-4">

                <div class="col-md-6">
                    <div class="highlight-card bg-light">
                        <div class="highlight-icon">
                            <i class="bx bx-transfer"></i>
                        </div>
                        <div class="highlight-label">Tipo</div>
                        <div class="highlight-value">
                            <span class="estado-badge-large {{ $tipoClass }} w-100 justify-content-center">
                                <i class="bx {{ $tipoIcon }}"></i> {{ $tipoLabel }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="highlight-card bg-light">
                        <div class="highlight-icon">
                            <i class="bx bx-sort"></i>
                        </div>
                        <div class="highlight-label">Cantidad</div>
                        <div class="highlight-value">
                            <span class="monto-badge w-100 justify-content-center">
                                <i class="bx bx-package"></i>
                                {{ number_format((int) $item->cantidad, 0, '.', ',') }}
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
                                <small class="text-muted">ID Movimiento</small>
                                <div class="fw-semibold">{{ $item->id_movimiento_inventario }}</div>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted">Fecha de registro</small>
                                <div class="fw-semibold">
                                    {{ optional($item->created_at)->format('d/m/Y') ?: '—' }}
                                </div>
                            </div>

                            <div>
                                <small class="text-muted">Hora de registro</small>
                                <div class="fw-semibold">
                                    {{ optional($item->created_at)->format('H:i') ?: '—' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <div class="mb-3">
                                <small class="text-muted">Usuario realizador</small>
                                <div class="fw-semibold">
                                    {{ $item->realizador?->nombre ?? 'Usuario no disponible' }}
                                </div>
                            </div>

                            <div>
                                <small class="text-muted">ID Usuario</small>
                                <div>{{ $item->id_usuario_realizador ?? '—' }}</div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- DERECHA --}}
                <div class="col-md-6">

                    <div class="card border-0 bg-light mb-3">
                        <div class="card-body">

                            <div class="mb-3">
                                <small class="text-muted">Motivo</small>
                                <div class="fw-semibold">{{ $item->motivo }}</div>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted">Referencia</small>
                                <div class="fw-semibold">{{ $referenciaTitulo }}</div>
                                <small class="text-muted">{{ $referenciaValor }}</small>
                            </div>

                            <div>
                                <small class="text-muted">Última actualización</small>
                                <div>
                                    {{ optional($item->updated_at)->format('d/m/Y H:i') ?: '—' }}
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <label class="fw-semibold mb-2">Notas</label>
                            <div>
                                {{ $item->notas ?: 'Sin notas registradas.' }}
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            {{-- BOTONES --}}


        </div>
    </div>

@endsection
