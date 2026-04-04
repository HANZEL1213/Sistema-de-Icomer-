@extends('admin.layouts.app')

@section('title', 'Detalle de Venta Local')

@section('content')

@php
    /**
     * DATOS DEMO - Misma estructura que el show de pedidos
     * Simulación de datos reales de la BD
     */
    
    $usuariosFake = [
        1 => (object) ['id_usuario' => 1, 'nombre' => 'Cajero 1', 'correo' => 'cajero1@tienda.com'],
        2 => (object) ['id_usuario' => 2, 'nombre' => 'Cajero 2', 'correo' => 'cajero2@tienda.com'],
        3 => (object) ['id_usuario' => 3, 'nombre' => 'Cajero 3', 'correo' => 'cajero3@tienda.com'],
        4 => (object) ['id_usuario' => 4, 'nombre' => 'Cajero 4', 'correo' => 'cajero4@tienda.com'],
    ];

    $ventaId = $id ?? 1;
    
    // Datos de la venta
    $subtotal = 25000;
    $descuento = 1500;
    $total = $subtotal - $descuento;
    
    $cajeroId = ($ventaId % 4) + 1;
    $cajero = $usuariosFake[$cajeroId];
    
    $venta = (object)[
        'id_venta_local' => $ventaId,
        'numero_ticket' => 'TK-' . str_pad($ventaId, 6, '0', STR_PAD_LEFT),
        'estado_pago' => 'pagado', // pagado o pendiente
        'created_at' => '2026-03-24 15:30:00',
        
        'nombre_cliente' => 'María Rodríguez',
        'telefono_cliente' => '8888-1234',
        
        'subtotal' => $subtotal,
        'descuento' => $descuento,
        'total' => $total,
        
        'notas' => 'Cliente solicitó factura electrónica. Pago verificado correctamente.'
    ];
    
    // Productos vendidos
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
    
    // Pagos realizados
    $pagos = [
        (object)[
            'metodo' => 'efectivo',
            'monto' => 15000,
            'referencia' => null,
            'created_at' => '2026-03-24 15:30:15'
        ],
        (object)[
            'metodo' => 'tarjeta',
            'monto' => 8500,
            'referencia' => 'REF-123456',
            'created_at' => '2026-03-24 15:30:30'
        ]
    ];
    
    $montoPagado = array_sum(array_column($pagos, 'monto'));
    $estaPagado = $montoPagado >= $venta->total;
    
    $badgeColor = $estaPagado ? 'success' : 'warning';
    $badgeLabel = $estaPagado ? 'pagado' : 'pendiente';
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
                        <a href="{{ route('admin.ventas-locales.index') }}">Ventas Locales</a>
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
                    <h4 class="fw-bold text-uppercase mb-1">Detalle de Venta Local</h4>
                    <small class="text-muted">Información completa de la venta #{{ $venta->numero_ticket }}</small>
                </div>

                <a href="{{ route('admin.ventas-locales.index') }}"
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
                                <small class="text-muted">Número de Ticket</small>
                                <div class="fw-semibold">{{ $venta->numero_ticket }}</div>
                            </div>

                            <div class="mb-2">
                                <small class="text-muted">Estado de Pago</small>
                                <div>
                                    <span class="estado-badge bg-{{ $badgeColor }} text-white">
                                        {{ ucfirst($badgeLabel) }}
                                    </span>
                                </div>
                            </div>

                            <div>
                                <small class="text-muted">Fecha y Hora</small>
                                <div class="fw-semibold">{{ $venta->created_at }}</div>
                            </div>

                        </div>
                    </div>

                    {{-- CAJERO --}}
                    <div class="card border-0 bg-light mb-3">
                        <div class="card-body">

                            <label class="fw-semibold mb-2">Cajero Responsable</label>

                            <div class="mb-2">
                                <small class="text-muted">Nombre</small>
                                <div class="fw-semibold">{{ $cajero->nombre }}</div>
                            </div>

                            <div>
                                <small class="text-muted">Correo</small>
                                <div>{{ $cajero->correo }}</div>
                            </div>

                        </div>
                    </div>

                    {{-- CLIENTE --}}
                    <div class="card border-0 bg-light">
                        <div class="card-body">

                            <label class="fw-semibold mb-2">Cliente</label>

                            <div class="mb-2">
                                <small class="text-muted">Nombre</small>
                                <div class="fw-semibold">{{ $venta->nombre_cliente ?? 'Consumidor Final' }}</div>
                            </div>

                            <div>
                                <small class="text-muted">Teléfono</small>
                                <div>{{ $venta->telefono_cliente ?? '—' }}</div>
                            </div>

                        </div>
                    </div>

                </div>



                {{-- DERECHA --}}
                <div class="col-md-6">

                    {{-- TOTALES --}}
                    <div class="card border-0 bg-light mb-3">
                        <div class="card-body">

                            <label class="fw-semibold mb-2">Totales</label>

                            <div class="mb-2">
                                <small class="text-muted">Subtotal</small>
                                <div class="fw-semibold">
                                    ₡{{ number_format($venta->subtotal, 0) }}
                                </div>
                            </div>

                            @if($venta->descuento > 0)
                            <div class="mb-2">
                                <small class="text-muted">Descuento</small>
                                <div class="fw-semibold text-success">
                                    - ₡{{ number_format($venta->descuento, 0) }}
                                </div>
                            </div>
                            @endif

                            <div>
                                <small class="text-muted">Total Final</small>
                                <div class="monto-badge fw-bold">
                                    ₡{{ number_format($venta->total, 0) }}
                                </div>
                            </div>

                            <div class="mt-3 pt-2 border-top">
                                <small class="text-muted">Monto Pagado</small>
                                <div class="fw-semibold text-success">
                                    ₡{{ number_format($montoPagado, 0) }}
                                </div>
                                @if(!$estaPagado)
                                <small class="text-danger">Pendiente: ₡{{ number_format($venta->total - $montoPagado, 0) }}</small>
                                @endif
                            </div>

                        </div>
                    </div>

                </div>

            </div>



            {{-- PRODUCTOS --}}
            <div class="card border-0 bg-light mt-4">
                <div class="card-body">

                    <label class="fw-semibold mb-3 d-block">Productos de la Venta</label>

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
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="5" class="text-end fw-bold">SUBTOTAL:</td>
                                    <td class="fw-bold">₡{{ number_format($venta->subtotal, 0) }}</td>
                                </tr>
                                @if($venta->descuento > 0)
                                <tr>
                                    <td colspan="5" class="text-end text-success">DESCUENTO:</td>
                                    <td class="text-success">- ₡{{ number_format($venta->descuento, 0) }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td colspan="5" class="text-end fw-bold fs-5">TOTAL:</td>
                                    <td class="fw-bold fs-5">₡{{ number_format($venta->total, 0) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>
            </div>



            {{-- PAGOS --}}
            <div class="card border-0 bg-light mt-4">
                <div class="card-body">

                    <label class="fw-semibold mb-3 d-block">Registro de Pagos</label>

                    @forelse($pagos as $pg)
                        <div class="border rounded p-3 mb-3 bg-light shadow-sm">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <small class="text-muted">Método</small>
                                    <div class="fw-semibold">{{ strtoupper($pg->metodo) }}</div>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Monto</small>
                                    <div class="fw-semibold">₡{{ number_format($pg->monto, 0) }}</div>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Referencia</small>
                                    <div class="fw-semibold">{{ $pg->referencia ?? '—' }}</div>
                                </div>
                                <div class="col-md-12">
                                    <small class="text-muted">Fecha</small>
                                    <div>{{ $pg->created_at }}</div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted">No hay pagos registrados.</p>
                    @endforelse

                </div>
            </div>



            {{-- NOTAS --}}
            @if($venta->notas)
            <div class="card border-0 bg-light mt-4">
                <div class="card-body">
                    <label class="fw-semibold mb-2">Notas de la Venta</label>
                    <div class="text-muted">{{ $venta->notas }}</div>
                </div>
            </div>
            @endif



            {{-- BOTONES --}}
            <div class="d-flex justify-content-end gap-3 mt-4">
                <a href="#" class="btn btn-primary-custom">
                    <i class="bx bx-edit"></i>
                    <span>Editar Venta</span>
                </a>

                <button type="button" class="btn btn-danger-custom" id="btnAnularVenta">
                    <i class="bx bx-block"></i>
                    <span>Anular Venta</span>
                </button>

                <button type="button" class="btn btn-secondary-custom" id="btnImprimirTicket">
                    <i class="bx bx-printer"></i>
                    <span>Imprimir Ticket</span>
                </button>
            </div>

        </div>
    </div>

</div>

@endsection


@push('styles')
<style>
    .estado-badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        gap: 6px;
    }
    
    .monto-badge {
        background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
        color: white;
        padding: 8px 16px;
        border-radius: 12px;
        display: inline-block;
    }
    
    .btn-danger-custom {
        background-color: #dc3545;
        border-color: #dc3545;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-danger-custom:hover {
        background-color: #bb2d3b;
        border-color: #b02a37;
        color: white;
        transform: translateY(-1px);
    }
    
    @media print {
        .page-breadcrumb,
        .d-flex.justify-content-between.align-items-center .btn:not(#btnImprimirTicket),
        .btn-primary-custom,
        .btn-danger-custom,
        .btn-back {
            display: none !important;
        }
        
        #btnImprimirTicket {
            display: block !important;
        }
        
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        
        .bg-light {
            background-color: white !important;
        }
        
        body {
            padding: 0;
            margin: 0;
        }
    }
</style>
@endpush


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // Imprimir ticket
        const btnImprimir = document.getElementById('btnImprimirTicket');
        if (btnImprimir) {
            btnImprimir.addEventListener('click', function() {
                window.print();
            });
        }
        
        // Anular venta
        const btnAnular = document.getElementById('btnAnularVenta');
        if (btnAnular) {
            btnAnular.addEventListener('click', function() {
                Swal.fire({
                    title: '¿Anular esta venta?',
                    text: `Ticket: ${@json($venta->numero_ticket)}. Esta acción no se puede deshacer.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, anular venta',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Aquí iría la lógica de anulación
                        Swal.fire({
                            title: 'Venta Anulada',
                            text: `La venta ${@json($venta->numero_ticket)} ha sido anulada.`,
                            icon: 'success'
                        }).then(() => {
                            window.location.href = "{{ route('admin.ventas-locales.index') }}";
                        });
                    }
                });
            });
        }
        
    });
</script>
@endpush