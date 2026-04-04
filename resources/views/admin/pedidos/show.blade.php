@extends('admin.layouts.app')

@section('title', 'Detalle del Pedido')

@section('content')

@php
    // 🔥 DATA DEMO (Como en tus otras vistas)
    $pedido = (object)[
        'id_pedido' => $id,
        'numero_pedido' => 'PED-2026-00123',
        'estado' => 'pendiente_pago',
        'created_at' => '2026-03-20 14:21:00',

        'nombre_cliente' => 'Juan Pérez',
        'telefono_cliente' => '8888-9999',
        'correo_cliente' => 'juan@example.com',

        'tipo_entrega' => 'envio',
        'provincia_envio' => 'San José',
        'canton_envio' => 'Central',
        'distrito_envio' => 'Catedral',
        'direccion_envio' => '300m norte del parque',
        'referencia_envio' => 'Casa portón negro',
        'costo_envio' => 2500,

        'codigo_cupon' => 'DESCUENTO10',
        'descuento' => 1500,

        'subtotal' => 25000,
        'subtotal_con_descuento' => 23500,
        'total' => 26000,

        'notas' => 'Cliente solicita empaque de regalo.'
    ];

    $productos = [
        (object)[
            'nombre_producto' => 'Audífonos Gamer HyperX',
            'sku_snapshot' => 'HX-998',
            'precio_unitario' => 15000,
            'cantidad' => 1,
            'total_linea' => 15000,
            'img' => 'https://via.placeholder.com/90'
        ],
        (object)[
            'nombre_producto' => 'Mouse Logitech G203',
            'sku_snapshot' => 'LG-203',
            'precio_unitario' => 10000,
            'cantidad' => 1,
            'total_linea' => 10000,
            'img' => 'https://via.placeholder.com/90'
        ]
    ];

    $pagos = [
        (object)[
            'metodo' => 'sinpe',
            'intento' => 1,
            'ruta_comprobante' => 'https://via.placeholder.com/200',
            'numero_comprobante' => 'ABC12345',
            'monto_reportado' => 26000,
            'estado' => 'enviado',
            'enviado_en' => '2026-03-20 16:05:00',
            'verificado_en' => null,
            'motivo_rechazo' => null
        ]
    ];

    $badgeColor = [
        'pendiente_pago' => 'warning',
        'en_revision' => 'info',
        'pagado_verificado' => 'success',
        'preparando' => 'primary',
        'enviado' => 'secondary',
        'entregado' => 'success',
        'rechazado' => 'danger',
        'cancelado' => 'dark'
    ][$pedido->estado] ?? 'secondary';
@endphp


<div class="page-content">

    {{-- BREADCRUMB --}}
    <div class="page-breadcrumb d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav>
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.pedidos.index') }}">Pedidos</a>
                    </li>
                    <li class="breadcrumb-item active">Detalle</li>
                </ol>
            </nav>
        </div>
    </div>


    {{-- CARD PRINCIPAL --}}
    <div class="card card-index">
        <div class="card-body">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="fw-bold text-uppercase mb-1">Detalle del Pedido</h4>
                    <small class="text-muted">Información completa del pedido #{{ $pedido->numero_pedido }}</small>
                </div>

                <a href="{{ route('admin.pedidos.index') }}"
                   class="btn btn-secondary-custom btn-back">
                    <i class="bx bx-arrow-back"></i>
                    <span class="btn-text">Volver</span>
                </a>
            </div>

            <hr>

            <div class="row g-4">

                {{-- IZQUIERDA --}}
                <div class="col-md-6">

                    {{-- INFORMACIÓN GENERAL --}}
                    <div class="card border-0 bg-light mb-3">
                        <div class="card-body">

                            <label class="fw-semibold mb-2">Información General</label>

                            <div class="mb-2">
                                <small class="text-muted">Número de Pedido</small>
                                <div class="fw-semibold">{{ $pedido->numero_pedido }}</div>
                            </div>

                            <div class="mb-2">
                                <small class="text-muted">Estado</small>
                                <div>
                                    <span class="estado-badge bg-{{ $badgeColor }} text-white">
                                        {{ ucfirst(str_replace('_',' ', $pedido->estado)) }}
                                    </span>
                                </div>
                            </div>

                            <div>
                                <small class="text-muted">Fecha</small>
                                <div class="fw-semibold">{{ $pedido->created_at }}</div>
                            </div>

                        </div>
                    </div>

                    {{-- CLIENTE --}}
                    <div class="card border-0 bg-light mb-3">
                        <div class="card-body">

                            <label class="fw-semibold mb-2">Cliente</label>

                            <div class="mb-2">
                                <small class="text-muted">Nombre</small>
                                <div class="fw-semibold">{{ $pedido->nombre_cliente }}</div>
                            </div>

                            <div class="mb-2">
                                <small class="text-muted">Teléfono</small>
                                <div>{{ $pedido->telefono_cliente }}</div>
                            </div>

                            <div>
                                <small class="text-muted">Correo</small>
                                <div>{{ $pedido->correo_cliente }}</div>
                            </div>

                        </div>
                    </div>

                    {{-- ENTREGA --}}
                    <div class="card border-0 bg-light">
                        <div class="card-body">

                            <label class="fw-semibold mb-2">Método de Entrega</label>

                            @if($pedido->tipo_entrega === 'retiro')

                                <div class="fw-semibold">Retiro en tienda</div>

                            @else

                                <div class="mb-2">
                                    <small class="text-muted">Provincia</small>
                                    <div class="fw-semibold">{{ $pedido->provincia_envio }}</div>
                                </div>

                                <div class="mb-2">
                                    <small class="text-muted">Cantón</small>
                                    <div>{{ $pedido->canton_envio }}</div>
                                </div>

                                <div class="mb-2">
                                    <small class="text-muted">Distrito</small>
                                    <div>{{ $pedido->distrito_envio }}</div>
                                </div>

                                <div class="mb-2">
                                    <small class="text-muted">Dirección</small>
                                    <div>{{ $pedido->direccion_envio }}</div>
                                </div>

                                <div class="mb-2">
                                    <small class="text-muted">Referencia</small>
                                    <div>{{ $pedido->referencia_envio }}</div>
                                </div>

                                <div>
                                    <small class="text-muted">Costo de Envío</small>
                                    <div class="fw-semibold">₡{{ number_format($pedido->costo_envio, 0) }}</div>
                                </div>

                            @endif

                        </div>
                    </div>

                </div>



                {{-- DERECHA --}}
                <div class="col-md-6">

                    {{-- CUPÓN / DESCUENTOS --}}
                    <div class="card border-0 bg-light mb-3">
                        <div class="card-body">

                            <label class="fw-semibold mb-2">Cupón y Descuentos</label>

                            <div class="mb-2">
                                <small class="text-muted">Código Cupón</small>
                                <div class="fw-semibold">{{ $pedido->codigo_cupon ?? '—' }}</div>
                            </div>

                            <div>
                                <small class="text-muted">Descuento</small>
                                <div class="fw-semibold">₡{{ number_format($pedido->descuento, 0) }}</div>
                            </div>

                        </div>
                    </div>

                    {{-- TOTALES --}}
                    <div class="card border-0 bg-light mb-3">
                        <div class="card-body">

                            <label class="fw-semibold mb-2">Totales</label>

                            <div class="mb-2">
                                <small class="text-muted">Subtotal</small>
                                <div class="fw-semibold">
                                    ₡{{ number_format($pedido->subtotal, 0) }}
                                </div>
                            </div>

                            <div class="mb-2">
                                <small class="text-muted">Subtotal con Descuento</small>
                                <div class="fw-semibold">
                                    ₡{{ number_format($pedido->subtotal_con_descuento, 0) }}
                                </div>
                            </div>

                            <div>
                                <small class="text-muted">Total Final</small>
                                <div class="monto-badge fw-bold">
                                    ₡{{ number_format($pedido->total, 0) }}
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

            </div>



            {{-- PRODUCTOS --}}
            <div class="card border-0 bg-light mt-4">
                <div class="card-body">

                    <label class="fw-semibold mb-3 d-block">Productos del Pedido</label>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Imagen</th>
                                    <th>Producto</th>
                                    <th>SKU</th>
                                    <th>Precio</th>
                                    <th>Cant.</th>
                                    <th>Total Línea</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($productos as $p)
                                    <tr>
                                        <td><img src="{{ $p->img }}" width="60" class="rounded shadow-sm"></td>
                                        <td>{{ $p->nombre_producto }}</td>
                                        <td>{{ $p->sku_snapshot }}</td>
                                        <td>₡{{ number_format($p->precio_unitario,0) }}</td>
                                        <td>{{ $p->cantidad }}</td>
                                        <td class="fw-bold">₡{{ number_format($p->total_linea,0) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>



            {{-- PAGOS --}}
            <div class="card border-0 bg-light mt-4">
                <div class="card-body">

                    <label class="fw-semibold mb-3 d-block">Pagos del Pedido</label>

                    @forelse($pagos as $pg)

                        @php
                            $pagoColor = [
                                'enviado' => 'warning',
                                'verificado' => 'success',
                                'rechazado' => 'danger'
                            ][$pg->estado] ?? 'secondary';
                        @endphp

                        <div class="border rounded p-3 mb-3 bg-light shadow-sm">

                            <div class="row g-3">

                                <div class="col-md-4">
                                    <small class="text-muted">Método</small>
                                    <div class="fw-semibold">{{ strtoupper($pg->metodo) }}</div>
                                </div>

                                <div class="col-md-4">
                                    <small class="text-muted">Intento</small>
                                    <div>{{ $pg->intento }}</div>
                                </div>

                                <div class="col-md-4">
                                    <small class="text-muted">Monto Reportado</small>
                                    <div class="fw-semibold">₡{{ number_format($pg->monto_reportado,0) }}</div>
                                </div>

                                <div class="col-md-4">
                                    <small class="text-muted">Estado</small>
                                    <div>
                                        <span class="estado-badge bg-{{ $pagoColor }} text-white">
                                            {{ ucfirst($pg->estado) }}
                                        </span>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <small class="text-muted">Enviado en</small>
                                    <div>{{ $pg->enviado_en }}</div>
                                </div>

                                <div class="col-md-4">
                                    <small class="text-muted">Verificado en</small>
                                    <div>{{ $pg->verificado_en ?? '—' }}</div>
                                </div>

                                @if($pg->motivo_rechazo)
                                    <div class="col-12 text-danger fw-semibold">
                                        Motivo: {{ $pg->motivo_rechazo }}
                                    </div>
                                @endif

                                <div class="col-12">
                                    <small class="text-muted">Comprobante</small><br>
                                    <img src="{{ $pg->ruta_comprobante }}" width="200" class="rounded border">
                                </div>

                            </div>

                        </div>

                    @empty
                        <p class="text-muted">No hay pagos registrados.</p>
                    @endforelse

                </div>
            </div>



            {{-- NOTAS --}}
            @if($pedido->notas)
            <div class="card border-0 bg-light mt-4">
                <div class="card-body">
                    <label class="fw-semibold mb-2">Notas del Pedido</label>
                    <div class="text-muted">{{ $pedido->notas }}</div>
                </div>
            </div>
            @endif



            {{-- BOTONES --}}
            <div class="d-flex justify-content-end gap-3 mt-4">
             

                <a href="#" class="btn btn-primary-custom">
                    <i class="bx bx-edit"></i>
                    <span>Editar</span>
                </a>

            
            </div>

        </div>
    </div>

</div>

@endsection