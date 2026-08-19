{{-- resources/views/tienda/checkout/index.blade.php --}}
@extends('tienda.layouts.app')


@section('title', 'Finalizar compra | ' . ($configTienda['tienda_nombre'] ?? 'Mi Tienda') . ' | Pago seguro')
@section('meta_description', 'Completa los datos de entrega y pago para finalizar tu compra en ' . ($configTienda['tienda_nombre'] ?? 'Mi Tienda') . ' de forma rápida y segura.')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/modules/checkout.css') }}">
@endpush

@section('content')

    <section class="store-checkout-page">

        <div class="container py-4 py-lg-5">

            <div class="store-detail-breadcrumb mb-4">
                <a href="{{ route('tienda.home') }}">Inicio</a>
                <i class="bi bi-chevron-right"></i>
                <a href="{{ route('tienda.carrito.index') }}">Carrito</a>
                <i class="bi bi-chevron-right"></i>
                <span>Finalizar compra</span>
            </div>

            @if (session('success'))
                <div class="alert alert-success mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger mb-4">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger mb-4">
                    <strong>Revisa los campos del formulario.</strong>
                </div>
            @endif

            <div class="store-checkout-hero mb-4 mb-lg-5">
                <div class="store-checkout-hero-content">
                    <span class="store-section-eyebrow">Finalizar compra</span>

                    <h1 class="store-checkout-title">
                        Completa tu pedido
                    </h1>

                    <p class="store-checkout-subtitle mb-0">
                        Ingresa tus datos, selecciona una zona disponible y confirma tu pedido.
                        Después podrás subir el comprobante de pago.
                    </p>
                    <p class="small mt-2 mb-0">
                        Los campos marcados con
                        <span class="text-danger">*</span>
                        son obligatorios.
                    </p>
                </div>
            </div>


            <form action="{{ route('tienda.checkout.confirmar') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row g-4 g-xl-5 align-items-start">

                    <div class="col-12 col-lg-7">

                        {{-- =========================================================
                            CHECKOUT STEPPER
                        ========================================================= --}}
                        <div class="checkout-progress mb-4">

                            <div class="checkout-progress-step active" id="indicatorStep1">
                                <div class="checkout-progress-circle">1</div>
                                <span>Datos</span>
                            </div>

                            <div class="checkout-progress-line"></div>

                            <div class="checkout-progress-step" id="indicatorStep2">
                                <div class="checkout-progress-circle">2</div>
                                <span>Entrega</span>
                            </div>

                            <div class="checkout-progress-line"></div>

                            <div class="checkout-progress-step" id="indicatorStep3">
                                <div class="checkout-progress-circle">3</div>
                                <span>Pago</span>
                            </div>

                        </div>

                        {{-- DATOS PERSONALES --}}
                        <div id="checkoutStep1" class="checkout-step">

                            <div class="store-checkout-card mb-4">
                                <div class="store-checkout-card-header">
                                    <h2>
                                        <i class="bi bi-person"></i>
                                        Información personal
                                    </h2>
                                </div>

                                <div class="store-checkout-card-body">
                                    <div class="row g-3">

                                        <div class="col-12 col-md-6">
                                            <label class="store-form-label">
                                                Nombre completo <span class="text-danger">*</span>
                                            </label>

                                            <input type="text" name="nombre_cliente" value="{{ old('nombre_cliente') }}"
                                                class="form-control store-filter-control @error('nombre_cliente') is-invalid @enderror"
                                                placeholder="Nombre completo" required>

                                            @error('nombre_cliente')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <label class="store-form-label">
                                                Teléfono <span class="text-danger">*</span>
                                            </label>

                                            <input type="text" name="telefono_cliente"
                                                value="{{ old('telefono_cliente') }}"
                                                class="form-control store-filter-control @error('telefono_cliente') is-invalid @enderror"
                                                placeholder="8888-8888" required>

                                            @error('telefono_cliente')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-12">
                                            <label class="store-form-label">Correo electrónico <span
                                                    class="text-danger">*</span></label>

                                            <input type="email" name="correo_cliente" value="{{ old('correo_cliente') }}"
                                                class="form-control store-filter-control @error('correo_cliente') is-invalid @enderror"
                                                placeholder="correo@email.com" required>

                                            @error('correo_cliente')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                    </div>
                                </div>
                            </div>

                            {{-- TIPO ENTREGA --}}
                            <div class="store-checkout-card mb-4">
                                <div class="store-checkout-card-header">
                                    <h2>
                                        <i class="bi bi-truck"></i>
                                        Tipo de entrega
                                    </h2>
                                </div>

                                <div class="store-checkout-card-body">
                                    <div class="row g-3">

                                        <div class="col-12 col-md-6">
                                            <label class="store-payment-option active" for="tipoEntregaEnvio">
                                                <input type="radio" id="tipoEntregaEnvio" name="tipo_entrega"
                                                    value="envio" class="d-none"
                                                    {{ old('tipo_entrega', 'envio') === 'envio' ? 'checked' : '' }}
                                                    required>

                                                <div class="store-payment-radio">
                                                    <i class="bi bi-check"></i>
                                                </div>

                                                <div>
                                                    <h5>Envío a domicilio</h5>
                                                    <p>Solo disponible en zonas registradas.</p>
                                                </div>
                                            </label>
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <label class="store-payment-option" for="tipoEntregaRetiro">
                                                <input type="radio" id="tipoEntregaRetiro" name="tipo_entrega"
                                                    value="retiro" class="d-none"
                                                    {{ old('tipo_entrega') === 'retiro' ? 'checked' : '' }} required>

                                                <div class="store-payment-radio">
                                                    <i class="bi bi-check"></i>
                                                </div>

                                                <div>
                                                    <h5>📍 Retiro</h5>
                                                    <p>Se coordina por WhatsApp.</p>
                                                </div>
                                            </label>
                                        </div>

                                        @error('tipo_entrega')
                                            <div class="col-12">
                                                <div class="text-danger small">{{ $message }}</div>
                                            </div>
                                        @enderror

                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="button" id="nextStep1" class="btn btn-store-primary">
                                    Siguiente
                                    <i class="bi bi-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>

                        <div id="checkoutStep2" class="checkout-step" style="display:none;">

                            {{-- DIRECCIÓN --}}
                            <div class="store-checkout-card mb-4" id="checkoutAddressBox">
                                <div class="store-checkout-card-header">
                                    <h2>
                                        <i class="bi bi-geo-alt"></i>
                                        Dirección de entrega
                                    </h2>
                                </div>

                                <div class="store-checkout-card-body">
                                    <div class="alert alert-warning mb-3">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Solo se muestran provincias, cantones y distritos con envío disponible.
                                    </div>

                                    <div class="row g-3">

                                        <div class="col-12 col-md-4">
                                            <label class="store-form-label">
                                                Provincia <span class="text-danger">*</span>
                                            </label>

                                            <select name="id_provincia" id="checkoutProvincia"
                                                class="form-select store-filter-control @error('id_provincia') is-invalid @enderror"
                                                required>
                                                <option value="">Seleccione</option>

                                                @foreach ($provincias as $provincia)
                                                    <option value="{{ $provincia->id_provincia }}"
                                                        {{ old('id_provincia') == $provincia->id_provincia ? 'selected' : '' }}>
                                                        {{ $provincia->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>

                                            @error('id_provincia')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-12 col-md-4">
                                            <label class="store-form-label">
                                                Cantón <span class="text-danger">*</span>
                                            </label>

                                            <select name="id_canton" id="checkoutCanton"
                                                class="form-select store-filter-control @error('id_canton') is-invalid @enderror"
                                                required>
                                                <option value="">Seleccione</option>
                                            </select>

                                            @error('id_canton')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-12 col-md-4">
                                            <label class="store-form-label">
                                                Distrito <span class="text-danger">*</span>
                                            </label>

                                            <select name="id_distrito" id="checkoutDistrito"
                                                class="form-select store-filter-control @error('id_distrito') is-invalid @enderror"
                                                required>
                                                <option value="">Seleccione</option>
                                            </select>

                                            @error('id_distrito')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-12">
                                            <label class="store-form-label">
                                                Dirección exacta <span class="text-danger">*</span>
                                            </label>

                                            <textarea name="direccion_envio"
                                                class="form-control store-filter-control @error('direccion_envio') is-invalid @enderror" rows="4"
                                                placeholder="Señas exactas, casa, local, color, punto de referencia" required>{{ old('direccion_envio') }}</textarea>

                                            @error('direccion_envio')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-12">
                                            <label class="store-form-label">Referencia de entrega</label>

                                            <input type="text" name="referencia_envio"
                                                value="{{ old('referencia_envio') }}"
                                                class="form-control store-filter-control @error('referencia_envio') is-invalid @enderror"
                                                placeholder="Ejemplo: frente a la escuela, portón negro">

                                            @error('referencia_envio')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-12">
                                            <label class="store-form-label">
                                                <i class="bi bi-map"></i>
                                                Ubicación en mapa
                                            </label>

                                            <div class="alert alert-info mb-3">
                                                <i class="bi bi-info-circle me-2"></i>
                                                La dirección escrita arriba se usará solo como referencia para ubicar mejor
                                                el
                                                mapa.
                                            </div>

                                            <div class="row g-2 mb-3">
                                                <div class="col-12 col-md-6">
                                                    <button type="button" class="btn btn-store-primary w-100"
                                                        id="btnUseCurrentLocation">
                                                        <i class="bi bi-crosshair me-1"></i>
                                                        Usar mi ubicación actual
                                                    </button>
                                                </div>

                                                <div class="col-12 col-md-6">
                                                    <button type="button" class="btn btn-store-outline w-100"
                                                        id="btnShowMap">
                                                        <i class="bi bi-map me-1"></i>
                                                        Elegir otra ubicación
                                                    </button>
                                                </div>
                                            </div>

                                            <div id="mapBox" style="display: none;">

                                                <div class="mb-3 position-relative">
                                                    <label class="store-form-label">Buscar ubicación</label>

                                                    <input type="text" id="addressSearch"
                                                        class="form-control store-filter-control"
                                                        placeholder="Ejemplo: escuela, iglesia, supermercado, barrio...">

                                                    <div id="addressSuggestions"
                                                        class="list-group position-absolute w-100 shadow-sm"
                                                        style="z-index: 9999; display: none; max-height: 220px; overflow-y: auto;">
                                                    </div>

                                                    <small class="text-muted d-block mt-2">
                                                        Escribí una referencia y seleccioná una sugerencia. También podés
                                                        mover
                                                        el marcador.
                                                    </small>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="store-form-label">
                                                        coordenadas
                                                    </label>

                                                    <div class="input-group">
                                                        <input type="text" id="manualLocationInput"
                                                            class="form-control store-filter-control"
                                                            placeholder="Pegá un link de Google Maps o coordenadas: 9.9281,-84.0907">

                                                        <button type="button" class="btn btn-store-outline"
                                                            id="btnLoadManualLocation">
                                                            Cargar
                                                        </button>
                                                    </div>

                                                    <small class="text-muted d-block mt-2">
                                                        Este campo se llena solo cuando seleccionás una ubicación.
                                                    </small>
                                                </div>

                                                <div id="pickupMap"
                                                    style="height: 350px; width: 100%; border-radius: 16px; overflow: hidden; z-index: 1;">
                                                </div>
                                            </div>

                                            <input type="hidden" name="link_google_maps" id="googleMapsLink"
                                                value="{{ old('link_google_maps') }}">
                                            <input type="hidden" name="latitud" id="latitud"
                                                value="{{ old('latitud') }}">
                                            <input type="hidden" name="longitud" id="longitud"
                                                value="{{ old('longitud') }}">
                                            <input type="hidden" name="direccion_mapa" id="direccionMapa"
                                                value="{{ old('direccion_mapa') }}">

                                            <div class="mt-3 p-3 bg-light rounded-4 border" id="selectedLocationInfo"
                                                style="{{ old('link_google_maps') ? '' : 'display: none;' }}">

                                                <strong class="d-block mb-1">Ubicación seleccionada:</strong>

                                                <span id="selectedAddressText" class="small d-block">
                                                    {{ old('direccion_mapa') ?: 'Ubicación cargada correctamente' }}
                                                </span>

                                                <code id="selectedCoordsText" class="small d-block mt-2 text-muted">
                                                    @if (old('latitud') && old('longitud'))
                                                        Coordenadas: {{ old('latitud') }}, {{ old('longitud') }}
                                                    @endif
                                                </code>

                                                <a href="{{ old('link_google_maps') ?: '#' }}" id="openGoogleMapsLink"
                                                    target="_blank"
                                                    class="small d-inline-flex align-items-center gap-1 mt-2">
                                                    <i class="bi bi-box-arrow-up-right"></i>
                                                    Ver en Google Maps
                                                </a>
                                            </div>

                                            @error('link_google_maps')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                    </div>
                                </div>
                            </div>

                            {{-- NOTAS --}}
                            <div class="store-checkout-card mb-4">
                                <div class="store-checkout-card-header">
                                    <h2>
                                        <i class="bi bi-chat-left-text"></i>
                                        Notas del pedido
                                    </h2>
                                </div>

                                <div class="store-checkout-card-body">
                                    <label class="store-form-label">Nota adicional</label>

                                    <textarea name="notas" class="form-control store-filter-control @error('notas') is-invalid @enderror"
                                        rows="3" placeholder="Ejemplo: llamar antes de entregar">{{ old('notas') }}</textarea>

                                    @error('notas')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="button" id="prevStep2" class="btn btn-store-outline">
                                    <i class="bi bi-arrow-left me-1"></i>
                                    Atrás
                                </button>

                                <button type="button" id="nextStep2" class="btn btn-store-primary">
                                    Siguiente
                                    <i class="bi bi-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>

                        {{-- PAGO --}}
                        <div id="checkoutStep3" class="checkout-step" style="display:none;">

                            <div class="store-checkout-card">
                                <div class="store-checkout-card-header">
                                    <h2>
                                        <i class="bi bi-credit-card"></i>
                                        Método de pago
                                    </h2>
                                </div>

                                <div class="store-checkout-card-body">

                                    <div class="alert alert-warning mb-4">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Realiza el pago antes de confirmar el pedido. Podés adjuntar el comprobante,
                                        escribir el número de referencia o enviar ambos.
                                    </div>

                                    <label class="store-payment-option active" for="metodoPagoSinpe">

                                        <input type="radio" id="metodoPagoSinpe" name="metodo_pago" value="sinpe"
                                            class="d-none" {{ old('metodo_pago', 'sinpe') === 'sinpe' ? 'checked' : '' }}>

                                        <div class="store-payment-radio">
                                            <i class="bi bi-check"></i>
                                        </div>

                                        <div>
                                            <h5>SINPE Móvil / Transferencia</h5>

                                            <p>
                                                Tu pedido quedará en revisión hasta validar el comprobante de pago.
                                            </p>
                                        </div>

                                    </label>

                                    @error('metodo_pago')
                                        <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror

                                    <div class="bg-light rounded p-3 mt-4">

                                        <h6 class="fw-bold mb-3">
                                            <i class="bi bi-phone me-1"></i>
                                            Datos para realizar el pago
                                        </h6>

                                        <p class="mb-1">
                                            <strong>SINPE:</strong>

                                            {{ $configTienda['checkout_sinpe'] ?? '8888-8888' }}
                                        </p>

                                        <p class="mb-1">
                                            <strong>Cuenta bancaria:</strong>

                                            {{ $configTienda['checkout_cuenta'] ?? 'Mi Tienda Online' }}
                                        </p>

                                        <p class="mb-1">
                                            <strong>Nombre:</strong>

                                            {{ $configTienda['checkout_nombre_pago'] ?? 'Mi Tienda Online' }}
                                        </p>


                                    </div>

                                    <div class="row g-3 mt-3">

                                        <div class="col-12">
                                            <label class="store-form-label">
                                                Número de comprobante o referencia
                                                <span class="text-danger">*</span>
                                            </label>

                                            <input type="text" name="numero_comprobante"
                                                value="{{ old('numero_comprobante') }}"
                                                class="form-control store-filter-control @error('numero_comprobante') is-invalid @enderror"
                                                placeholder="Ejemplo: 154848484" required>

                                            <small class="text-muted d-block mt-2">
                                                Podés escribir el número del voucher, referencia SINPE o comprobante
                                                bancario.
                                            </small>

                                            @error('numero_comprobante')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-12">
                                            <label class="store-form-label">
                                                Imagen del comprobante (Opcional)
                                            </label>
                                            <input type="file" name="comprobante_pago" id="checkoutComprobanteInput"
                                                accept="image/png,image/jpeg,image/jpg,image/webp"
                                                class="form-control store-filter-control @error('comprobante_pago') is-invalid @enderror">

                                            <div class="store-checkout-proof-preview d-none mt-3"
                                                id="checkoutProofPreviewWrap">

                                                <img src="" id="checkoutProofPreview" class="rounded-4 border"
                                                    alt="Preview comprobante">

                                            </div>

                                            <small class="text-muted d-block mt-2">
                                                Adjunta una captura del SINPE o comprobante bancario.
                                                Formatos permitidos: JPG, PNG y WEBP.
                                            </small>

                                            @error('comprobante_pago')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                    </div>

                                </div>
                            </div>

                            <div class="d-flex justify-content-start mt-4">
                                <button type="button" id="prevStep3" class="btn btn-store-outline">
                                    <i class="bi bi-arrow-left me-1"></i>
                                    Atrás
                                </button>
                            </div>

                        </div>

                    </div>


                    {{-- RESUMEN --}}
                    <div class="col-12 col-lg-5">
                        <div class="store-checkout-summary-card">

                            <div class="store-checkout-summary-header">
                                <h2>Resumen del pedido</h2>
                            </div>
                            <div class="store-checkout-products">
                                @foreach ($carrito as $item)
                                    @php
                                        $imagen = $item['imagen']
                                            ? asset('storage/' . $item['imagen'])
                                            : asset('assets/img/no-image.png');

                                        $tienePromo = $item['tiene_promocion'] ?? false;
                                        $precioVenta = (float) ($item['precio'] ?? 0);
                                        $precioNormal = (float) ($item['precio_normal'] ?? $precioVenta);
                                        $porcentaje = (int) ($item['porcentaje_descuento'] ?? 0);
                                    @endphp

                                    <div class="store-checkout-product">
                                        <div class="store-checkout-product-image">
                                            <img src="{{ $imagen }}" alt="{{ $item['nombre'] }}">
                                        </div>

                                        <div class="store-checkout-product-info">
                                            <h5>{{ $item['nombre'] }}</h5>

                                            @if (!empty($item['variante']))
                                                <small class="d-block text-muted mb-1">
                                                    Variante: {{ $item['variante'] }}
                                                </small>
                                            @endif

                                            <span>Cantidad: {{ $item['cantidad'] }}</span>
                                        </div>

                                        <div class="store-checkout-product-price">
                                            @if ($tienePromo)
                                                <div class="text-muted text-decoration-line-through small">
                                                    ₡{{ number_format($precioNormal * $item['cantidad'], 2) }}
                                                </div>

                                                <strong class="text-danger">
                                                    ₡{{ number_format($precioVenta * $item['cantidad'], 2) }}
                                                </strong>
                                            @else
                                                ₡{{ number_format($precioVenta * $item['cantidad'], 2) }}
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- CUPÓN APLICADO --}}
                            @if ($cuponAplicado)
                                <div class="store-cart-coupon-box mt-3 mb-3">

                                    <label class="store-form-label">
                                        Cupón de descuento
                                    </label>

                                    <div class="bg-light rounded-4 p-3 border">

                                        <div class="d-flex justify-content-between align-items-start gap-3">

                                            <div>
                                                <span class="badge bg-success mb-2">
                                                    Cupón aplicado
                                                </span>

                                                <h6 class="fw-bold mb-1">
                                                    {{ $cuponAplicado['codigo'] }}
                                                </h6>

                                                <small class="text-muted d-block">
                                                    Descuento aplicado:
                                                    ₡{{ number_format($descuento, 2) }}
                                                </small>

                                                @if ($cuponAplicado['tipo'] === 'porcentaje')
                                                    <small class="text-success">
                                                        {{ number_format($cuponAplicado['valor'], 0) }}% OFF
                                                    </small>
                                                @else
                                                    <small class="text-success">
                                                        ₡{{ number_format($cuponAplicado['valor'], 2) }} OFF
                                                    </small>
                                                @endif
                                            </div>

                                            <a href="{{ route('tienda.carrito.index') }}"
                                                class="btn btn-store-outline btn-sm">
                                                Cambiar
                                            </a>

                                        </div>

                                    </div>

                                </div>
                            @endif

                            <input type="hidden" id="checkoutSubtotalValue" value="{{ $subtotal }}">
                            <input type="hidden" id="checkoutDescuentoValue" value="{{ $descuento }}">

                            <div class="store-checkout-totals">

                                <div class="store-checkout-total-row">
                                    <span>Subtotal</span>

                                    <strong id="checkoutSubtotalText">
                                        ₡{{ number_format($subtotal, 2) }}
                                    </strong>
                                </div>

                                <div class="store-checkout-total-row">
                                    <span>Envío</span>

                                    <strong id="checkoutEnvioText">
                                        Seleccione una zona
                                    </strong>
                                </div>

                                <div class="store-checkout-total-row text-success">
                                    <span>Cupon de Descuento</span>

                                    <strong id="checkoutDescuentoText">
                                        -₡{{ number_format($descuento, 2) }}
                                    </strong>
                                </div>

                                <div class="store-checkout-total-row total">
                                    <span>Total final</span>

                                    <strong id="checkoutTotalText">
                                        ₡{{ number_format($total, 2) }}
                                    </strong>
                                </div>

                            </div>

                            <div class="alert alert-warning mb-3">
                                <i class="bi bi-clock-history me-1"></i>
                                Tu pedido será revisado manualmente después de validar el pago.
                            </div>

                            <div class="form-check mt-4 mb-3">
                                <input class="form-check-input" type="checkbox" name="acepta_terminos"
                                    id="acepta_terminos" required>

                                <label class="form-check-label" for="acepta_terminos">
                                    He leído y acepto los
                                    <a href="#" class="terminos-link" data-bs-toggle="modal"
                                        data-bs-target="#modalTerminos">
                                        Términos, Condiciones y Políticas de Cora CR.
                                    </a>
                                </label>
                            </div>

                            <button type="submit" class="btn btn-store-primary store-checkout-submit">
                                <i class="bi bi-shield-check me-1"></i>
                                Confirmar pedido
                            </button>

                            <a href="{{ route('tienda.carrito.index') }}" class="btn btn-store-outline w-100 mb-3">
                                Volver al carrito
                            </a>

                            <div class="store-checkout-security">
                                <div>
                                    <i class="bi bi-lock"></i>
                                    Compra segura
                                </div>

                                <div>
                                    <i class="bi bi-truck"></i>
                                    Envío según zona
                                </div>
                            </div>

                        </div>
                    </div>


                </div>
            </form>

            {{-- MODAL TERMINOS Y CONDICIONES --}}

            <div class="modal fade" id="modalTerminos" tabindex="-1" aria-labelledby="modalTerminosLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title fw-bold" id="modalTerminosLabel">
                                Términos, Condiciones y Políticas de Cora CR
                            </h5>

                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Cerrar"></button>
                        </div>

                        <div class="modal-body">

                            <h6 class="fw-bold">1. Términos y condiciones</h6>
                            <p>
                                Al realizar una compra en Cora CR, el cliente acepta que la información
                                proporcionada es correcta y que el pedido estará sujeto a verificación
                                de disponibilidad, pago y datos de entrega.
                            </p>

                            <h6 class="fw-bold mt-4">2. Pedidos y pagos</h6>
                            <p>
                                Todos los pedidos deben ser confirmados mediante el método de pago
                                disponible. Cora CR podrá cancelar pedidos por falta de inventario,
                                errores evidentes en precios, información incorrecta o sospecha de fraude.
                            </p>

                            <h6 class="fw-bold mt-4">3. Envíos y entregas</h6>
                            <p>
                                Los tiempos de entrega son estimados y pueden variar por ubicación,
                                disponibilidad del producto o situaciones externas. El cliente es
                                responsable de brindar una dirección correcta.
                            </p>

                            <h6 class="fw-bold mt-4">4. Cambios y devoluciones</h6>
                            <p>
                                Los cambios podrán solicitarse dentro del plazo establecido por Cora CR,
                                siempre que el producto esté en buen estado, sin uso indebido y con su
                                comprobante de compra.
                            </p>

                            <p>
                                No aplican cambios ni devoluciones en productos personalizados, alterados,
                                usados indebidamente o dañados por el cliente.
                            </p>

                            <h6 class="fw-bold mt-4">5. Garantías</h6>
                            <p>
                                Las garantías cubren únicamente defectos de fabricación. No cubren daños
                                por golpes, caídas, humedad, mala manipulación o desgaste normal por uso.
                            </p>

                            <h6 class="fw-bold mt-4">6. Política de privacidad</h6>
                            <p>
                                La información del cliente será utilizada para procesar pedidos, coordinar
                                entregas, brindar soporte y mejorar el servicio. Cora CR no vende ni
                                comercializa información personal de sus clientes.
                            </p>

                            <h6 class="fw-bold mt-4">7. Modificaciones</h6>
                            <p>
                                Cora CR podrá actualizar estos términos y políticas cuando sea necesario.
                            </p>

                            <p class="text-muted small mb-0">
                                Última actualización: {{ now()->format('d/m/Y') }}
                            </p>

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-store-primary" data-bs-dismiss="modal">
                                Entendido
                            </button>
                        </div>

                    </div>
                </div>
            </div>

        </div>

    </section>

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/modules/checkout.js') }}"></script>
@endpush
