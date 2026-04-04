{{-- resources/views/admin/ventas_locales/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Ventas Locales')

@section('content')

    @php
        /**
         * DEMO COMPLETO SIN NULLS
         * Todo sigue exactamente la estructura real de BD:
         *
         * ventas_locales:
         * id_venta_local, numero_ticket, id_usuario_cajero,
         * nombre_cliente, telefono_cliente,
         * subtotal, descuento, total, notas,
         * created_at, updated_at
         *
         * pagos_ventas_locales:
         * id_pago_venta_local, id_venta_local,
         * metodo, monto, referencia, created_at
         */

        $usuariosFake = [
            1 => 'Cajero 1',
            2 => 'Cajero 2',
            3 => 'Cajero 3',
            4 => 'Cajero 4',
        ];

        $ventasLocales = [];

        $metodosPago = ['efectivo', 'tarjeta', 'sinpe', 'mixto'];
        $nombres = ['Hanzel', 'María', 'Juan', 'Carlos', 'Ana', 'Karla', 'Pedro', 'Sofía', 'Luis', 'Valeria'];
        $notasDemo = [
            'Cliente solicitó factura electrónica.',
            'Pago verificado correctamente.',
            'Cliente frecuente.',
            'Entrega inmediata.',
            'Cliente pidió empaque especial.',
        ];

        for ($i = 1; $i <= 25; $i++) {
            $subtotal = 10000 + $i * 550;
            $descuento = $i % 4 === 0 ? 1500 : 0;
            $total = $subtotal - $descuento;

            $idCajero = rand(1, 4);
            $metodo = $metodosPago[array_rand($metodosPago)];
            $cantidadItems = rand(1, 6);

            $nombreCliente = $nombres[array_rand($nombres)] . " {$i}";
            $telefonoCliente = '8' . rand(1000000, 9999999);
            $notaVenta = $notasDemo[array_rand($notasDemo)];

            $pagos = [];

            if ($metodo === 'mixto') {
                $m1 = round($total * 0.6, 2);
                $m2 = $total - $m1;

                $pagos[] = (object) [
                    'id_pago_venta_local' => $i * 10 + 1,
                    'id_venta_local' => $i,
                    'metodo' => 'efectivo',
                    'monto' => $m1,
                    'referencia' => null,
                    'created_at' => now(),
                ];

                $pagos[] = (object) [
                    'id_pago_venta_local' => $i * 10 + 2,
                    'id_venta_local' => $i,
                    'metodo' => 'tarjeta',
                    'monto' => $m2,
                    'referencia' => 'REF-' . rand(10000, 99999),
                    'created_at' => now(),
                ];
            } else {
                $pagos[] = (object) [
                    'id_pago_venta_local' => $i * 10 + 1,
                    'id_venta_local' => $i,
                    'metodo' => $metodo,
                    'monto' => $total,
                    'referencia' => in_array($metodo, ['tarjeta', 'sinpe']) ? 'REF-' . rand(10000, 99999) : null,
                    'created_at' => now(),
                ];
            }

            $ventasLocales[] = (object) [
                'id_venta_local' => $i,
                'numero_ticket' => 'TK-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'id_usuario_cajero' => $idCajero,
                'nombre_cliente' => $nombreCliente,
                'telefono_cliente' => $telefonoCliente,
                'subtotal' => $subtotal,
                'descuento' => $descuento,
                'total' => $total,
                'notas' => $notaVenta,
                'created_at' => now()->subDays(rand(0, 10)),
                'updated_at' => now(),
                'pagos' => $pagos,
                'cantidad_items' => $cantidadItems,
            ];
        }
    @endphp


    <div class="page-content">

        {{-- Breadcrumb --}}
        <div class="page-breadcrumb d-sm-flex align-items-center mb-3">
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="bx bx-home-alt"></i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">Ventas Locales</li>
                    </ol>
                </nav>
            </div>
        </div>

        {{-- Card --}}
        <div class="card card-index">
            <div class="card-body">

                {{-- Header --}}
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-3">
                    <div>
                        <h4 class="mb-1 text-uppercase fw-bold">Ventas Locales</h4>
                        <small class="text-muted">Registro y control de ventas realizadas en el local</small>
                    </div>

                    <a href="{{ route('admin.ventas-locales.create') }}"
                        class="btn btn-nuevo d-inline-flex align-items-center gap-2">
                        <i class="bx bx-plus-circle"></i>
                        <span>Nueva</span>
                    </a>
                </div>

                <hr class="my-2" />

                {{-- Top bar --}}
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">

                    {{-- Por página --}}
                    <div class="pagination-perpage-top">
                        <div class="input-group input-group-perpage">
                            <span class="input-group-text bg-transparent border-end-0">
                                <i class="bx bx-list-ul"></i>
                            </span>
                            <select class="form-select form-select-sm border-start-0" id="perPageSelect">
                                <option value="5">5</option>
                                <option value="10" selected>10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                            </select>
                            <span class="input-group-text bg-transparent border-start-0">
                                <span class="perpage-label">por página</span>
                            </span>
                        </div>
                    </div>

                    {{-- Buscador --}}
                    <div class="search-container ms-auto">
                        <div class="search-box" role="search">
                            <i class="bx bx-search search-icon"></i>
                            <input type="text" id="searchInput" class="search-input"
                                placeholder="Buscar ticket, cliente, teléfono, cajero, método..." autocomplete="off">
                            <div class="search-actions">
                                <button class="btn-search-clear" id="clearSearch" type="button" title="Limpiar">
                                    <i class="bx bx-x"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="tabla_index" class="table table-hover table-bordered align-middle text-center w-100">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>N° Ticket</th>
                                <th>Cliente</th>
                                <th>Cajero</th>
                                <th>Ítems</th>
                                <th>Pago</th>
                                <th>Total</th>
                                <th>Fecha</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($ventasLocales as $v)
                                @php
                                    $pagos = collect($v->pagos);
                                    $montoPagado = $pagos->sum('monto');
                                    $estaPagado = $montoPagado >= $v->total;

                                    $pagoBadgeClass = $estaPagado ? 'status-active' : 'status-warning';
                                    $pagoIcon = $estaPagado ? 'bx-check-circle' : 'bx-time-five';
                                    $pagoLabel = $estaPagado ? 'PAGADO' : 'INCOMPLETO';

                                    $metodosUsados = strtoupper($pagos->pluck('metodo')->unique()->implode(' + '));
                                    $cajeroNombre = $usuariosFake[$v->id_usuario_cajero];
                                @endphp

                                <tr>
                                    <td class="fw-semibold text-muted">{{ $v->id_venta_local }}</td>

                                    <td class="text-start">
                                        <div class="fw-semibold">{{ $v->numero_ticket }}</div>
                                        <small class="text-muted">
                                            Subtotal: ₡{{ number_format($v->subtotal, 2) }}
                                        </small>

                                        @if ($v->descuento > 0)
                                            <div class="small text-success">
                                                Descuento: ₡{{ number_format($v->descuento, 2) }}
                                            </div>
                                        @endif

                                        <div class="small text-muted">
                                            Nota: {{ $v->notas }}
                                        </div>
                                    </td>

                                    <td class="text-start">
                                        <div class="fw-semibold">{{ $v->nombre_cliente }}</div>
                                        <small class="text-muted">{{ $v->telefono_cliente }}</small>
                                    </td>

                                    <td>
                                        <span class="status-badge status-info">
                                            <i class="bx bx-user me-1"></i>
                                            {{ strtoupper($cajeroNombre) }}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="fw-semibold">{{ $v->cantidad_items }}</span>
                                        <div class="small text-muted">producto(s)</div>
                                    </td>

                                    <td>
                                        <span class="status-badge {{ $pagoBadgeClass }}">
                                            <i class="bx {{ $pagoIcon }} me-1"></i>
                                            {{ $pagoLabel }}
                                        </span>

                                        <div class="small text-muted mt-1">
                                            {{ $metodosUsados }}
                                        </div>
                                    </td>

                                    <td class="fw-bold">
                                        ₡{{ number_format($v->total, 2) }}
                                    </td>

                                    <td>
                                        {{ \Carbon\Carbon::parse($v->created_at)->format('Y-m-d') }}
                                    </td>

                                


                                    <td>
                                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                                            <a class="btn-action btn-view" title="Ver"
                                                href="{{ route('admin.ventas-locales.show', $v->id_venta_local) }}">
                                                <i class="bx bx-show"></i>
                                            </a>

                                            <a class="btn-action btn-edit" title="Editar"
                                                href="">
                                                <i class="bx bx-edit"></i>
                                            </a>



                                            <button class="btn-action btn-edit">
                                                <i class="bx bx-printer"></i>
                                            </button>
                                        </div>
                                    </td>








                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>
                {{-- Paginación (JS global del layout, igual que tus otros index) --}}
                <div class="custom-pagination-container mt-3">
                    <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3 py-2">
                        <div class="pagination-info">
                            <span class="text-muted">Mostrando</span>
                            <span class="fw-semibold text-dark ms-1" id="pagination-from">0</span>
                            <span class="text-muted">a</span>
                            <span class="fw-semibold text-dark mx-1" id="pagination-to">0</span>
                            <span class="text-muted">de</span>
                            <span class="fw-semibold text-dark ms-1" id="pagination-total">0</span>
                            <span class="text-muted">resultados</span>
                        </div>

                        <div class="pagination-controls">
                            <nav aria-label="Page navigation">
                                <ul class="pagination pagination-modern mb-0">
                                    <li class="page-item disabled" id="pagination-prev">
                                        <a class="page-link" href="#" aria-label="Previous">
                                            <i class="bx bx-chevron-left"></i>
                                        </a>
                                    </li>

                                    {{-- números generados por JS --}}

                                    <li class="page-item disabled" id="pagination-next">
                                        <a class="page-link" href="#" aria-label="Next">
                                            <i class="bx bx-chevron-right"></i>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
