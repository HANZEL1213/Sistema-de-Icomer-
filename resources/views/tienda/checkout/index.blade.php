{{-- resources/views/tienda/checkout/index.blade.php --}}
@extends('tienda.layouts.app')

<style>
    .terminos-link {
        color: var(--bs-primary);
        font-weight: 600;
        text-decoration: none;
    }

    .terminos-link:hover {
        text-decoration: underline;
    }

    .checkout-progress {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 2rem;
    }

    .checkout-progress-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: .5rem;
    }

    .checkout-progress-circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #dee2e6;
        color: #6c757d;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        transition: .3s;
    }

    .checkout-progress-line {
        width: 120px;
        height: 3px;
        background: #dee2e6;
        margin: 0 10px;
    }

    .checkout-progress-step.active .checkout-progress-circle {
        background: #111827;
        color: #fff;
    }

    .checkout-progress-step.completed .checkout-progress-circle {
        background: #198754;
        color: #fff;
    }

    .checkout-progress-step span {
        font-size: .85rem;
        font-weight: 600;
    }
</style>

@section('title', 'Finalizar compra | Tienda')
@section('meta_description', 'Finaliza tu compra de forma rápida y segura.')

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
                                            <label class="store-form-label">Correo electrónico</label>

                                            <input type="email" name="correo_cliente" value="{{ old('correo_cliente') }}"
                                                class="form-control store-filter-control @error('correo_cliente') is-invalid @enderror"
                                                placeholder="correo@email.com">

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
                                                    {{ old('tipo_entrega', 'envio') === 'envio' ? 'checked' : '' }} required>

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
                                                    <h5>Retiro en tienda</h5>
                                                    <p>No se cobra envío.</p>
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
                                            <strong>Nombre:</strong>

                                            {{ $configTienda['checkout_nombre_pago'] ?? 'Mi Tienda Online' }}
                                        </p>

                                         <p class="mb-1">
                                            <strong>Cuenta bancaria:</strong>

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
                                                Imagen del comprobante
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="file" name="comprobante_pago" id="checkoutComprobanteInput"
                                                accept="image/png,image/jpeg,image/jpg,image/webp"
                                                class="form-control store-filter-control @error('comprobante_pago') is-invalid @enderror"
                                                required>

                                            <div class="store-checkout-proof-preview d-none mt-3"
                                                id="checkoutProofPreviewWrap">

                                                <img src="" id="checkoutProofPreview"
                                                    class="img-fluid rounded-4 border" alt="Preview comprobante">

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
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const tipoEntregaRadios = document.querySelectorAll('input[name="tipo_entrega"]');
            const addressBox = document.getElementById('checkoutAddressBox');

            const provinciaSelect = document.getElementById('checkoutProvincia');
            const cantonSelect = document.getElementById('checkoutCanton');
            const distritoSelect = document.getElementById('checkoutDistrito');

            const envioText = document.getElementById('checkoutEnvioText');
            const totalText = document.getElementById('checkoutTotalText');
            const paymentTotalText = document.getElementById('checkoutPaymentTotalText');

            const subtotalValue = Number(document.getElementById('checkoutSubtotalValue')?.value || 0);
            const descuentoValue = Number(document.getElementById('checkoutDescuentoValue')?.value || 0);

            const oldCanton = "{{ old('id_canton') }}";
            const oldDistrito = "{{ old('id_distrito') }}";

            function formatoCRC(valor) {
                return '₡' + Number(valor).toLocaleString('es-CR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            function actualizarResumenEnvio(costo) {

                const totalFinal =
                    subtotalValue - descuentoValue + Number(costo || 0);

                if (envioText) {

                    envioText.textContent =
                        Number(costo) > 0 ?
                        formatoCRC(costo) :
                        'Gratis';
                }

                if (totalText) {
                    totalText.textContent = formatoCRC(totalFinal);
                }

                if (paymentTotalText) {
                    paymentTotalText.textContent = formatoCRC(totalFinal);
                }
            }

            function resetResumenEnvio(texto = 'Seleccione una zona') {

                const totalBase = subtotalValue - descuentoValue;

                if (envioText) {
                    envioText.textContent = texto;
                }

                if (totalText) {
                    totalText.textContent = formatoCRC(totalBase);
                }

                if (paymentTotalText) {
                    paymentTotalText.textContent = formatoCRC(totalBase);
                }
            }

            function consultarCostoEnvio(idDistrito) {

                if (!idDistrito) {

                    resetResumenEnvio();

                    return;
                }

                if (envioText) {
                    envioText.textContent = 'Calculando...';
                }

                fetch(`/checkout/costo-envio/${idDistrito}`)
                    .then(response => response.json())
                    .then(data => {

                        if (data.success) {

                            actualizarResumenEnvio(
                                Number(data.costo || 0)
                            );

                        } else {

                            resetResumenEnvio('No disponible');
                        }
                    })
                    .catch(() => {
                        resetResumenEnvio('No disponible');
                    });
            }

            function toggleEntrega() {

                const tipo =
                    document.querySelector('input[name="tipo_entrega"]:checked')
                    ?.value || 'envio';

                if (tipo === 'retiro') {

                    addressBox.style.display = 'none';

                    actualizarResumenEnvio(0);

                } else {

                    addressBox.style.display = '';

                    if (distritoSelect.value) {

                        consultarCostoEnvio(distritoSelect.value);

                    } else {

                        resetResumenEnvio();
                    }
                }

                document.querySelectorAll('.store-payment-option')
                    .forEach(option => {

                        const input =
                            option.querySelector('input[type="radio"]');

                        if (!input) return;

                        option.classList.toggle('active', input.checked);
                    });
            }

            function limpiarSelect(select, texto = 'Seleccione') {

                select.innerHTML =
                    `<option value="">${texto}</option>`;
            }

            function cargarCantones(idProvincia, selected = null) {

                limpiarSelect(cantonSelect);
                limpiarSelect(distritoSelect);

                resetResumenEnvio();

                if (!idProvincia) return;

                fetch(`/checkout/cantones/${idProvincia}`)
                    .then(response => response.json())
                    .then(data => {

                        data.forEach(canton => {

                            const option =
                                document.createElement('option');

                            option.value = canton.id_canton;
                            option.textContent = canton.nombre;

                            if (
                                selected &&
                                String(selected) === String(canton.id_canton)
                            ) {
                                option.selected = true;
                            }

                            cantonSelect.appendChild(option);
                        });

                        if (selected) {
                            cargarDistritos(selected, oldDistrito);
                        }
                    });
            }

            function cargarDistritos(idCanton, selected = null) {

                limpiarSelect(distritoSelect);

                resetResumenEnvio();

                if (!idCanton) return;

                fetch(`/checkout/distritos/${idCanton}`)
                    .then(response => response.json())
                    .then(data => {

                        data.forEach(distrito => {

                            const option =
                                document.createElement('option');

                            option.value = distrito.id_distrito;
                            option.textContent = distrito.nombre;

                            if (
                                selected &&
                                String(selected) === String(distrito.id_distrito)
                            ) {
                                option.selected = true;
                            }

                            distritoSelect.appendChild(option);
                        });

                        if (selected) {
                            consultarCostoEnvio(selected);
                        }
                    });
            }

            tipoEntregaRadios.forEach(radio => {
                radio.addEventListener('change', toggleEntrega);
            });

            provinciaSelect.addEventListener('change', function() {
                cargarCantones(this.value);
            });

            cantonSelect.addEventListener('change', function() {
                cargarDistritos(this.value);
            });

            distritoSelect.addEventListener('change', function() {
                consultarCostoEnvio(this.value);
            });

            toggleEntrega();

            if (provinciaSelect.value) {
                cargarCantones(
                    provinciaSelect.value,
                    oldCanton
                );
            }

            /*
            |--------------------------------------------------------------------------
            | MAPA
            |--------------------------------------------------------------------------
            */

            const btnUseCurrentLocation =
                document.getElementById('btnUseCurrentLocation');

            const btnShowMap =
                document.getElementById('btnShowMap');

            const mapBox =
                document.getElementById('mapBox');

            const addressSearch =
                document.getElementById('addressSearch');

            const addressSuggestions =
                document.getElementById('addressSuggestions');

            const manualLocationInput =
                document.getElementById('manualLocationInput');

            const btnLoadManualLocation =
                document.getElementById('btnLoadManualLocation');

            const inputDireccion =
                document.querySelector('textarea[name="direccion_envio"]');

            const inputReferencia =
                document.querySelector('input[name="referencia_envio"]');

            const inputLat =
                document.getElementById('latitud');

            const inputLng =
                document.getElementById('longitud');

            const inputLink =
                document.getElementById('googleMapsLink');

            const inputDireccionMapa =
                document.getElementById('direccionMapa');

            const selectedAddressText =
                document.getElementById('selectedAddressText');

            const selectedCoordsText =
                document.getElementById('selectedCoordsText');

            const openGoogleMapsLink =
                document.getElementById('openGoogleMapsLink');

            const infoBox =
                document.getElementById('selectedLocationInfo');

            let map = null;
            let marker = null;
            let suggestionTimeout = null;

            const defaultLat = 9.9281;
            const defaultLng = -84.0907;

            function activarModoUbicacion(tipo) {

                if (tipo === 'actual') {

                    btnUseCurrentLocation.classList.remove('btn-store-outline');
                    btnUseCurrentLocation.classList.add('btn-store-primary');

                    btnShowMap.classList.remove('btn-store-primary');
                    btnShowMap.classList.add('btn-store-outline');

                } else {

                    btnShowMap.classList.remove('btn-store-outline');
                    btnShowMap.classList.add('btn-store-primary');

                    btnUseCurrentLocation.classList.remove('btn-store-primary');
                    btnUseCurrentLocation.classList.add('btn-store-outline');
                }
            }

            function obtenerReferenciaBusqueda() {

                const provincia =
                    provinciaSelect.options[provinciaSelect.selectedIndex]?.text || '';

                const canton =
                    cantonSelect.options[cantonSelect.selectedIndex]?.text || '';

                const distrito =
                    distritoSelect.options[distritoSelect.selectedIndex]?.text || '';

                return [
                        distrito,
                        canton,
                        provincia,
                        'Costa Rica'
                    ]
                    .filter(Boolean)
                    .join(', ');
            }

            function setLocation(lat, lng, texto) {

                const link =
                    `https://www.google.com/maps?q=${lat},${lng}`;

                inputLat.value = lat;
                inputLng.value = lng;
                inputLink.value = link;

                if (manualLocationInput) {
                    manualLocationInput.value = link;
                }

                if (inputDireccionMapa) {
                    inputDireccionMapa.value = texto;
                }

                if (selectedAddressText) {
                    selectedAddressText.textContent = texto;
                }

                if (selectedCoordsText) {

                    selectedCoordsText.textContent =
                        `Coordenadas: ${Number(lat).toFixed(6)}, ${Number(lng).toFixed(6)}`;
                }

                if (openGoogleMapsLink) {
                    openGoogleMapsLink.href = link;
                }

                if (infoBox) {
                    infoBox.style.display = '';
                }
            }

            async function obtenerDireccionDesdeCoordenadas(
                lat,
                lng,
                textoFallback = 'Ubicación seleccionada'
            ) {

                setLocation(
                    lat,
                    lng,
                    'Buscando dirección aproximada...'
                );

                try {

                    const response = await fetch(
                        `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`
                    );

                    const data = await response.json();

                    setLocation(
                        lat,
                        lng,
                        data.display_name || textoFallback
                    );

                } catch (error) {

                    setLocation(
                        lat,
                        lng,
                        textoFallback
                    );
                }
            }

            function initMap(lat = defaultLat, lng = defaultLng) {

                if (map) {
                    map.invalidateSize();
                    return;
                }

                map = L.map('pickupMap', {
                    maxZoom: 19,
                    minZoom: 8,
                    zoomControl: true
                }).setView([lat, lng], 16);

                const mapaNormal = L.tileLayer(
                    'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; OpenStreetMap'
                    }
                );

                const mapaSatelite = L.tileLayer(
                    'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                        maxZoom: 18,
                        attribution: 'Tiles &copy; Esri'
                    }
                );

                mapaNormal.addTo(map);

                L.control.layers({
                        'Mapa normal': mapaNormal,
                        'Satélite': mapaSatelite
                    },
                    null, {
                        position: 'topright',
                        collapsed: false
                    }
                ).addTo(map);

                marker = L.marker([lat, lng], {
                    draggable: true
                }).addTo(map);

                marker.bindPopup('Mové este marcador hasta la ubicación exacta.').openPopup();

                marker.on('dragend', function() {
                    const position = marker.getLatLng();

                    obtenerDireccionDesdeCoordenadas(
                        position.lat,
                        position.lng,
                        'Ubicación seleccionada en el mapa'
                    );
                });

                map.on('click', function(e) {
                    marker.setLatLng(e.latlng);

                    obtenerDireccionDesdeCoordenadas(
                        e.latlng.lat,
                        e.latlng.lng,
                        'Ubicación seleccionada en el mapa'
                    );
                });
            }

            async function buscarDireccion(textoBusqueda) {

                if (!textoBusqueda) return;

                try {

                    const response = await fetch(
                        `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(textoBusqueda)}&limit=1&countrycodes=cr`
                    );

                    const data = await response.json();

                    if (!data.length) return;

                    const resultado = data[0];

                    const lat = Number(resultado.lat);
                    const lng = Number(resultado.lon);

                    initMap(lat, lng);

                    map.setView([lat, lng], 16);

                    marker.setLatLng([lat, lng]);

                    setLocation(
                        lat,
                        lng,
                        resultado.display_name
                    );

                    setTimeout(() => {

                        if (map) {
                            map.invalidateSize();
                        }

                    }, 500);

                } catch (error) {

                    console.log(error);
                }
            }

            function extraerCoordenadas(texto) {

                const regex =
                    /(-?\d+\.\d+)\s*,\s*(-?\d+\.\d+)/;

                const match = texto.match(regex);

                if (!match) return null;

                return {
                    lat: Number(match[1]),
                    lng: Number(match[2])
                };
            }

            async function buscarSugerencias(textoBusqueda) {

                if (!textoBusqueda || textoBusqueda.length < 3) {

                    addressSuggestions.style.display = 'none';
                    addressSuggestions.innerHTML = '';

                    return;
                }

                const referencia =
                    obtenerReferenciaBusqueda();

                const busqueda =
                    `${textoBusqueda}, ${referencia}`;

                try {

                    const response = await fetch(
                        `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(busqueda)}&limit=5&countrycodes=cr`
                    );

                    const data = await response.json();

                    addressSuggestions.innerHTML = '';

                    if (!data.length) {

                        addressSuggestions.style.display = 'none';

                        return;
                    }

                    data.forEach(item => {

                        const button =
                            document.createElement('button');

                        button.type = 'button';

                        button.className =
                            'list-group-item list-group-item-action small';

                        button.textContent =
                            item.display_name;

                        button.addEventListener('click', function() {

                            const lat = Number(item.lat);
                            const lng = Number(item.lon);

                            initMap(lat, lng);

                            map.setView([lat, lng], 16);

                            marker.setLatLng([lat, lng]);

                            setLocation(
                                lat,
                                lng,
                                item.display_name
                            );

                            addressSearch.value =
                                item.display_name;

                            addressSuggestions.style.display = 'none';

                            addressSuggestions.innerHTML = '';

                            setTimeout(() => {

                                if (map) {
                                    map.invalidateSize();
                                }

                            }, 500);
                        });

                        addressSuggestions.appendChild(button);
                    });

                    addressSuggestions.style.display = 'block';

                } catch (error) {

                    addressSuggestions.style.display = 'none';
                }
            }

            function cargarUbicacionManual() {

                const texto =
                    manualLocationInput?.value?.trim();

                if (!texto) return;

                const coordenadas =
                    extraerCoordenadas(texto);

                if (!coordenadas) {

                    alert(
                        'Pegá coordenadas válidas. Ejemplo: 9.9281,-84.0907'
                    );

                    return;
                }

                mapBox.style.display = '';

                initMap(
                    coordenadas.lat,
                    coordenadas.lng
                );

                map.setView(
                    [coordenadas.lat, coordenadas.lng],
                    17
                );

                marker.setLatLng(
                    [coordenadas.lat, coordenadas.lng]
                );

                obtenerDireccionDesdeCoordenadas(
                    coordenadas.lat,
                    coordenadas.lng,
                    'Ubicación cargada manualmente'
                );

                setTimeout(() => {

                    if (map) {
                        map.invalidateSize();
                    }

                }, 500);
            }

            btnUseCurrentLocation?.addEventListener('click', function() {

                activarModoUbicacion('actual');

                mapBox.style.display = '';

                if (!navigator.geolocation) {

                    alert(
                        'Tu navegador no soporta ubicación.'
                    );

                    return;
                }

                btnUseCurrentLocation.disabled = true;

                btnUseCurrentLocation.innerHTML =
                    '<i class="bi bi-hourglass-split me-1"></i> Obteniendo ubicación...';

                navigator.geolocation.getCurrentPosition(

                    function(position) {

                        const lat =
                            position.coords.latitude;

                        const lng =
                            position.coords.longitude;

                        initMap(lat, lng);

                        map.setView([lat, lng], 17);
                        marker.setLatLng([lat, lng]);

                        obtenerDireccionDesdeCoordenadas(
                            lat,
                            lng,
                            'Ubicación actual seleccionada'
                        );

                        setTimeout(() => {

                            if (map) {
                                map.invalidateSize();
                            }

                        }, 500);

                        btnUseCurrentLocation.disabled = false;

                        btnUseCurrentLocation.innerHTML =
                            '<i class="bi bi-check-circle me-1"></i> Ubicación actual';

                    },

                    function() {

                        btnUseCurrentLocation.disabled = false;

                        btnUseCurrentLocation.innerHTML =
                            '<i class="bi bi-crosshair me-1"></i> Usar mi ubicación actual';

                        alert(
                            'No se pudo obtener tu ubicación.'
                        );
                    },

                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            });

            btnShowMap?.addEventListener('click', function() {

                activarModoUbicacion('mapa');

                mapBox.style.display = '';

                const referencia =
                    obtenerReferenciaBusqueda();

                if (referencia) {

                    buscarDireccion(referencia);

                } else {

                    initMap();

                    setTimeout(() => {

                        if (map) {
                            map.invalidateSize();
                        }

                    }, 500);
                }
            });

            addressSearch?.addEventListener('input', function() {

                clearTimeout(suggestionTimeout);

                suggestionTimeout = setTimeout(() => {

                    buscarSugerencias(this.value);

                }, 500);
            });

            addressSearch?.addEventListener('keydown', function(e) {

                if (e.key !== 'Enter') return;

                e.preventDefault();

                const referencia =
                    obtenerReferenciaBusqueda();

                const busqueda =
                    `${this.value}, ${referencia}`;

                addressSuggestions.style.display = 'none';

                buscarDireccion(busqueda);
            });

            btnLoadManualLocation?.addEventListener('click', function() {

                cargarUbicacionManual();
            });

            manualLocationInput?.addEventListener('keydown', function(e) {

                if (e.key !== 'Enter') return;

                e.preventDefault();

                cargarUbicacionManual();
            });

        });


        /*
        |--------------------------------------------------------------------------
        | PREVIEW COMPROBANTE
        |--------------------------------------------------------------------------
        */

        const comprobanteInput =
            document.getElementById('checkoutComprobanteInput');

        const comprobantePreview =
            document.getElementById('checkoutProofPreview');

        const comprobantePreviewWrap =
            document.getElementById('checkoutProofPreviewWrap');

        if (comprobanteInput) {

            comprobanteInput.addEventListener('change', function(e) {

                const file = e.target.files[0];

                if (!file) {

                    comprobantePreview.src = '';

                    comprobantePreviewWrap.classList.add('d-none');

                    return;
                }

                const reader = new FileReader();

                reader.onload = function(event) {

                    comprobantePreview.src =
                        event.target.result;

                    comprobantePreviewWrap.classList.remove('d-none');
                };

                reader.readAsDataURL(file);
            });
        }

        // PASOS DE DATOS SEGREGADOS 

        document.getElementById('nextStep1').addEventListener('click', () => {

            const step1 = document.getElementById('checkoutStep1');

            const campos = step1.querySelectorAll('input, select, textarea');

            for (const campo of campos) {

                if (!campo.checkValidity()) {

                    campo.reportValidity();
                    return;
                }
            }

            goToStep(2);
        });

        document.getElementById('nextStep2').addEventListener('click', () => {

            const step2 = document.getElementById('checkoutStep2');

            const campos = step2.querySelectorAll('input, select, textarea');

            for (const campo of campos) {

                if (!campo.checkValidity()) {

                    campo.reportValidity();
                    return;
                }
            }

            goToStep(3);
        });

        function goToStep(step) {

            document.querySelectorAll('.checkout-step').forEach(el => {
                el.style.display = 'none';
            });

            document.getElementById(`checkoutStep${step}`).style.display = 'block';

            document.querySelectorAll('.checkout-progress-step').forEach(el => {
                el.classList.remove('active', 'completed');
            });

            if (step === 1) {
                document.getElementById('indicatorStep1').classList.add('active');
            }

            if (step === 2) {
                document.getElementById('indicatorStep1').classList.add('completed');
                document.getElementById('indicatorStep2').classList.add('active');
            }

            if (step === 3) {
                document.getElementById('indicatorStep1').classList.add('completed');
                document.getElementById('indicatorStep2').classList.add('completed');
                document.getElementById('indicatorStep3').classList.add('active');
            }
        }

        // document.getElementById('nextStep1').addEventListener('click', () => {
        //     goToStep(2);
        // });

        document.getElementById('prevStep2').addEventListener('click', () => {
            goToStep(1);
        });

        // document.getElementById('nextStep2').addEventListener('click', () => {
        //     goToStep(3);
        // });

        document.getElementById('prevStep3').addEventListener('click', () => {
            goToStep(2);
        });

        /* Iniciar en paso 1 */
        goToStep(1);
        
    </script>
@endpush
