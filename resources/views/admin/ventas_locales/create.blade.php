@extends('admin.layouts.app')

@section('title', 'Nueva Venta Local')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/modules/ventas_ficicas.css') }}">

@php
    $productosJs = $productos
        ->flatMap(function ($p) {
            $imagenUrl = $p->imagenPrincipal?->ruta
                ? asset('storage/' . $p->imagenPrincipal->ruta)
                : null;

            if ($p->usa_variantes) {
                return $p->variantesActivas->map(function ($v) use ($p, $imagenUrl) {
                    $nombreVariante = $v->nombre
                        ?: ($v->opcion?->etiqueta ?? $v->opcion?->valor ?? 'Variante');

                    return [
                        'id' => $p->id_producto,
                        'id_producto_variante' => $v->id_producto_variante,

                        'nombre' => $p->nombre . ' - ' . $nombreVariante,
                        'nombre_base' => $p->nombre,
                        'variante' => $nombreVariante,

                        'codigo_barras' => $p->codigo,
                        'sku' => $v->sku ?? $p->sku,

                        'precio_venta' => $v->precioVenta(),
                        'precio_normal' => $v->precioOriginal(),
                        'tiene_promocion' => $v->promocionVigente() && $v->precioOriginal() > $v->precioVenta(),

                        'stock' => (int) $v->stock_actual,
                        'usa_variantes' => true,
                        'imagen_url' => $imagenUrl,
                    ];
                });
            }

            return [[
                'id' => $p->id_producto,
                'id_producto_variante' => null,

                'nombre' => $p->nombre,
                'nombre_base' => $p->nombre,
                'variante' => null,

                'codigo_barras' => $p->codigo,
                'sku' => $p->sku,

                'precio_venta' => $p->precioVenta(),
                'precio_normal' => (float) $p->precio,
                'tiene_promocion' => $p->tienePromocionActiva() && (float) $p->precio > $p->precioVenta(),

                'stock' => (int) $p->stock_actual,
                'usa_variantes' => false,
                'imagen_url' => $imagenUrl,
            ]];
        })
        ->values()
        ->all();
@endphp

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
                        <a href="{{ route('admin.ventas-locales.index') }}">Ventas Locales</a>
                    </li>
                    <li class="breadcrumb-item active">Nueva Venta</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card card-form">
        <div class="card-body">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
                <div>
                    <h4 class="fw-bold text-uppercase mb-1">
                        <i class="bx bx-cart-add text-primary fs-3 align-middle"></i>
                        <span class="align-middle">Nueva Venta Local</span>
                    </h4>
                    <small class="text-muted">
                        Registro de venta en punto físico con detalle, pago y control de stock
                    </small>
                </div>

                <a href="{{ route('admin.ventas-locales.index') }}" class="btn btn-secondary-custom btn-back">
                    <i class="bx bx-arrow-back"></i>
                    <span class="btn-text">Volver</span>
                </a>
            </div>

            <hr class="my-2" />

            <form id="formVenta" action="{{ route('admin.ventas-locales.store') }}" method="POST">
                @csrf

                {{-- hidden de apoyo visual --}}
                <input type="hidden" id="inputSubtotal" value="0.00">
                <input type="hidden" id="inputTotal" value="0.00">

                {{-- hidden reales que sí viajan al backend --}}
                <div id="detalleInputsContainer"></div>
                <div id="pagosInputsContainer"></div>

                <div class="row g-4">

                    {{-- IZQUIERDA --}}
                    <div class="col-lg-8">


                        {{-- PRODUCTOS --}}
                        <div class="card border-0 bg-light mb-4">
                            <div class="card-body">
                                <div class="d-flex align-items-center gap-2 mb-4">
                                    <i class="bx bx-package fs-4 text-primary"></i>
                                    <h5 class="fw-bold mb-0">Detalle de Productos</h5>
                                </div>

                                <div class="bg-light rounded-3 mb-4">
                                    <div class="row g-3 align-items-end">

                                        <div class="col-md-6 position-relative">
                                            <label class="fw-semibold mb-2">
                                                <i class="bx bx-search-alt text-primary"></i>
                                                Buscar Producto <span class="text-danger">*</span>
                                            </label>

                                            <div class="input-group">
                                                <span class="input-group-text bg-white">
                                                    <i class="bx bx-barcode text-muted"></i>
                                                </span>

                                                <input type="text" id="searchProduct" class="form-control"
                                                    placeholder="Nombre, código o SKU..." autocomplete="off">

                                                <button type="button" id="btnClearSearch"
                                                    class="btn btn-outline-secondary">
                                                    <i class="bx bx-eraser"></i>
                                                </button>
                                            </div>

                                            <div id="searchResults" class="list-group shadow" style="display:none;"></div>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="fw-semibold mb-2">
                                                Cantidad <span class="text-danger">*</span>
                                            </label>
                                            <input type="number" id="productQuantity" class="form-control text-center"
                                                value="1" min="1">
                                        </div>

                                        <div class="col-md-3">
                                            <button type="button" id="btnAddProduct" class="btn btn-primary-custom w-100">
                                                <i class="bx bx-cart-add"></i> Agregar
                                            </button>
                                        </div>

                                    </div>
                                </div>

                                <div class="table-responsive" style="max-height: 400px; overflow-y:auto;">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light" style="position: sticky; top: 0; z-index: 10;">
                                            <tr class="text-uppercase small">
                                                <th style="width:35%;">Producto</th>
                                                <th style="width:18%;">Código / SKU</th>
                                                <th style="width:14%;">Precio</th>
                                                <th style="width:14%;">Cantidad</th>
                                                <th style="width:14%;">Total</th>
                                                <th style="width:5%;"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="productosBody"></tbody>
                                    </table>
                                </div>

                                <div id="emptyState" class="text-center py-5 text-muted">
                                    <i class="bx bx-basket fs-1 d-block mb-3 text-secondary"></i>
                                    <p class="mb-0">No hay productos agregados</p>
                                    <small>Busca un producto por nombre, código o SKU</small>
                                </div>

                                @error('detalle')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                                @error('detalle.*.id_producto')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                                @error('detalle.*.cantidad')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>



                    </div>

                    {{-- DERECHA --}}
                    <div class="col-lg-4">

                        <div class="card border-0 bg-light mb-4">
                            <div class="card-body p-4 text-center">
                                <p class="mb-1 text-muted">
                                    <i class="bx bx-calculator"></i> Total a Cobrar
                                </p>
                                <h2 class="display-5 fw-bold mb-0" style="color: var(--color-primary);">
                                    ₡ <span id="totalLabel">0.00</span>
                                </h2>
                            </div>
                        </div>

                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3 pb-2 border-bottom">
                                    <span class="text-muted">
                                        <i class="bx bx-receipt"></i> Subtotal:
                                    </span>
                                    <span class="fw-bold">₡ <span id="subtotalLabel">0.00</span></span>
                                </div>

                                <div class="mb-4">
                                    <label class="fw-semibold mb-2">
                                        <i class="bx bx-discount text-danger"></i> Descuento General (₡)
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white">₡</span>
                                        <input type="number" id="descuentoInput" name="descuento"
                                            class="form-control text-end fw-bold @error('descuento') is-invalid @enderror"
                                            value="{{ old('descuento', 0) }}" step="0.01" min="0">
                                    </div>
                                    @error('descuento')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="alert alert-info small mb-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span><i class="bx bx-package"></i> Total productos:</span>
                                        <strong><span id="totalProductosCount">0</span> unidades</strong>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                        <span><i class="bx bx-list-ul"></i> Productos distintos:</span>
                                        <strong><span id="productosDistintosCount">0</span> items</strong>
                                    </div>
                                </div>

                                @error('pagos')
                                    <div class="text-danger small mb-3">{{ $message }}</div>
                                @enderror
                                @error('pagos.*.metodo')
                                    <div class="text-danger small mb-3">{{ $message }}</div>
                                @enderror
                                @error('pagos.*.monto')
                                    <div class="text-danger small mb-3">{{ $message }}</div>
                                @enderror
                                @error('pagos.*.referencia')
                                    <div class="text-danger small mb-3">{{ $message }}</div>
                                @enderror

                                <button type="button" id="btnProcesar"
                                    class="btn btn-primary-custom w-100 py-3 fw-bold text-uppercase">
                                    <i class="bx bx-check-double fs-5 align-middle"></i>
                                    <span class="align-middle">Registrar Venta</span>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
{{-- MODAL PAGO --}}
<div class="modal fade" id="modalPago" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content modal-admin-clean">

            <div class="modal-header">
                <div>
                    <h5 class="text-uppercase mb-1">Finalizar Venta</h5>
                    <small class="text-muted">Complete la información del pago</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                {{-- TOTAL --}}
                <div class="card border-0 bg-light mb-3">
                    <div class="card-body text-center">
                        <small class="text-muted text-uppercase d-block mb-1">
                            Monto Total a Recibir
                        </small>

                        <h2 class="fw-bold total-admin mb-0">
                            ₡ <span id="modalTotalLabel">0.00</span>
                        </h2>
                    </div>
                </div>

                {{-- MÉTODOS --}}
                <div class="card border-0 bg-light mb-3">
                    <div class="card-body">

                        <label class="fw-semibold mb-3 d-block">
                            Método de Pago
                        </label>

                        <div class="row g-3">

                            <div class="col-3">
                                <button type="button"
                                    class="btn-metodo-admin w-100 metodo-btn active"
                                    data-metodo="efectivo">
                                    <i class="bx bx-money"></i>
                                    <span>Efectivo</span>
                                </button>
                            </div>

                            <div class="col-3">
                                <button type="button"
                                    class="btn-metodo-admin w-100 metodo-btn"
                                    data-metodo="tarjeta">
                                    <i class="bx bx-credit-card"></i>
                                    <span>Tarjeta</span>
                                </button>
                            </div>

                            <div class="col-3">
                                <button type="button"
                                    class="btn-metodo-admin w-100 metodo-btn"
                                    data-metodo="sinpe">
                                    <i class="bx bx-mobile-alt"></i>
                                    <span>SINPE</span>
                                </button>
                            </div>

                            <div class="col-3">
                                <button type="button"
                                    class="btn-metodo-admin w-100 metodo-btn"
                                    data-metodo="mixto">
                                    <i class="bx bx-shuffle"></i>
                                    <span>Mixto</span>
                                </button>
                            </div>

                        </div>

                        <input type="hidden" id="metodoPagoInput" value="efectivo">

                    </div>
                </div>

                {{-- DATOS DEL PAGO --}}
                <div class="card border-0 bg-light mb-3">
                    <div class="card-body">

                        <div id="efectivoFields">

                            <label class="fw-semibold mb-2">
                                Monto Recibido
                            </label>

                            <div class="input-group mb-3">
                                <span class="input-group-text">₡</span>
                                <input type="number"
                                    id="montoRecibido"
                                    class="form-control"
                                    placeholder="0.00"
                                    step="0.01">
                            </div>

                            <div class="vuelto-box">
                                <span>Vuelto:</span>
                                <strong>
                                    ₡ <span id="vueltoLabel">0.00</span>
                                </strong>
                            </div>

                        </div>

                        <div id="referenciaFields" class="d-none">

                            <label class="fw-semibold mb-2">
                                Número de Comprobante
                            </label>

                            <input type="text"
                                id="referenciaPago"
                                class="form-control"
                                placeholder="Referencia de transacción">

                        </div>

                        <div id="mixtoFields" class="d-none">

                            <div class="row g-3">

                                <div class="col-6">
                                    <label class="fw-semibold mb-2">
                                        Efectivo
                                    </label>

                                    <input type="number"
                                        id="mixtoEfectivo"
                                        class="form-control"
                                        placeholder="0.00"
                                        step="0.01">
                                </div>

                                <div class="col-6">
                                    <label class="fw-semibold mb-2">
                                        Tarjeta / SINPE
                                    </label>

                                    <input type="number"
                                        id="mixtoDigital"
                                        class="form-control"
                                        placeholder="0.00"
                                        step="0.01">
                                </div>

                            </div>

                            <div id="mixtoWarning" class="mt-2 small text-muted"></div>

                            <div id="mixtoVueltoInfo"
                                class="mt-3 vuelto-box d-none">

                                Vuelto en efectivo:

                                <strong>
                                    ₡ <span id="mixtoVueltoSpan">0.00</span>
                                </strong>

                            </div>

                            <div class="mt-3">

                                <label class="fw-semibold mb-2">
                                    Referencia digital (opcional)
                                </label>

                                <input type="text"
                                    id="mixtoReferencia"
                                    class="form-control"
                                    placeholder="Comprobante de la parte digital">

                            </div>

                        </div>

                    </div>
                </div>

                {{-- CLIENTE Y NOTAS --}}
                <div class="payment-extra-compact">

                    <div class="extra-item">

                        <button type="button"
                            class="extra-toggle"
                            id="toggleClienteModal">

                            <span>
                                <i class="bx bx-user"></i>
                                Cliente
                            </span>

                            <small>Opcional</small>

                            <i class="bx bx-chevron-down extra-icon"
                                id="toggleClienteModalIcon"></i>

                        </button>

                        <div class="extra-content"
                            id="clienteModalContent">

                            <div class="row g-3">

                                <div class="col-md-6">
                                    <input type="text"
                                        name="nombre_cliente"
                                        class="form-control"
                                        placeholder="Nombre del cliente">
                                </div>

                                <div class="col-md-6">
                                    <input type="text"
                                        name="telefono_cliente"
                                        class="form-control"
                                        placeholder="Teléfono">
                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="extra-item">

                        <button type="button"
                            class="extra-toggle"
                            id="toggleNotasModal">

                            <span>
                                <i class="bx bx-note"></i>
                                Notas
                            </span>

                            <small>Opcional</small>

                            <i class="bx bx-chevron-down extra-icon"
                                id="toggleNotasModalIcon"></i>

                        </button>

                        <div class="extra-content"
                            id="notasModalContent">

                            <textarea
                                name="notas"
                                class="form-control"
                                rows="3"
                                placeholder="Información adicional de la venta"></textarea>

                        </div>

                    </div>

                </div>

            </div>

            <div class="modal-footer">

                <button type="button"
                    class="btn btn-secondary-custom btn-back"
                    data-bs-dismiss="modal">
                    Cancelar
                </button>

                <button type="submit"
                    class="btn btn-primary-custom">
                    Confirmar Venta
                </button>

            </div>

        </div>
    </div>
</div>
         
            </form>

        </div>
    </div>
@endsection



@push('scripts')
    <script>
        window.VentaConfig = {
            productosBackend: @json($productosJs)
        };
    </script>
    <script src="{{ asset('assets/js/modules/ventas_ficicas.js') }}"></script>
    <script>
        function initToggleCard(toggleId, contentId, iconId) {
            const toggle = document.getElementById(toggleId);
            const content = document.getElementById(contentId);
            const icon = document.getElementById(iconId);

            if (!toggle || !content || !icon) return;

            toggle.addEventListener('click', function() {
                const isOpen = content.style.display === 'block';

                content.style.display = isOpen ? 'none' : 'block';
                icon.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
            });
        }

        initToggleCard('toggleNotasVenta', 'notasVentaContent', 'toggleNotasVentaIcon');
        initToggleCard('toggleClienteVenta', 'clienteVentaContent', 'toggleClienteVentaIcon');
    </script>
@endpush
