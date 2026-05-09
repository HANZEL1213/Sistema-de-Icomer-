{{-- resources/views/tienda/checkout/index.blade.php --}}
@extends('tienda.layouts.app')

@section('title', 'Checkout | Tienda')
@section('meta_description', 'Finaliza tu compra de forma rápida y segura.')

@section('content')

<section class="store-checkout-page">

    <div class="container py-4 py-lg-5">

        <div class="store-detail-breadcrumb mb-4">
            <a href="{{ route('tienda.home') }}">Inicio</a>
            <i class="bi bi-chevron-right"></i>
            <a href="{{ route('tienda.carrito.index') }}">Carrito</a>
            <i class="bi bi-chevron-right"></i>
            <span>Checkout</span>
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
            </div>
        </div>

        <form action="{{ route('tienda.checkout.confirmar') }}" method="POST">
            @csrf

            <div class="row g-4 g-xl-5 align-items-start">

                <div class="col-12 col-lg-7">

                    {{-- DATOS PERSONALES --}}
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
                                    <label class="store-form-label">Nombre completo</label>

                                    <input type="text"
                                           name="nombre_cliente"
                                           value="{{ old('nombre_cliente') }}"
                                           class="form-control store-filter-control @error('nombre_cliente') is-invalid @enderror"
                                           placeholder="Nombre completo">

                                    @error('nombre_cliente')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="store-form-label">Teléfono</label>

                                    <input type="text"
                                           name="telefono_cliente"
                                           value="{{ old('telefono_cliente') }}"
                                           class="form-control store-filter-control @error('telefono_cliente') is-invalid @enderror"
                                           placeholder="8888-8888">

                                    @error('telefono_cliente')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="store-form-label">Correo electrónico</label>

                                    <input type="email"
                                           name="correo_cliente"
                                           value="{{ old('correo_cliente') }}"
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
                                        <input type="radio"
                                               id="tipoEntregaEnvio"
                                               name="tipo_entrega"
                                               value="envio"
                                               class="d-none"
                                               {{ old('tipo_entrega', 'envio') === 'envio' ? 'checked' : '' }}>

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
                                        <input type="radio"
                                               id="tipoEntregaRetiro"
                                               name="tipo_entrega"
                                               value="retiro"
                                               class="d-none"
                                               {{ old('tipo_entrega') === 'retiro' ? 'checked' : '' }}>

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
                                    <label class="store-form-label">Provincia</label>

                                    <select name="id_provincia"
                                            id="checkoutProvincia"
                                            class="form-select store-filter-control @error('id_provincia') is-invalid @enderror">
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
                                    <label class="store-form-label">Cantón</label>

                                    <select name="id_canton"
                                            id="checkoutCanton"
                                            class="form-select store-filter-control @error('id_canton') is-invalid @enderror">
                                        <option value="">Seleccione</option>
                                    </select>

                                    @error('id_canton')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-4">
                                    <label class="store-form-label">Distrito</label>

                                    <select name="id_distrito"
                                            id="checkoutDistrito"
                                            class="form-select store-filter-control @error('id_distrito') is-invalid @enderror">
                                        <option value="">Seleccione</option>
                                    </select>

                                    @error('id_distrito')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="store-form-label">Dirección exacta</label>

                                    <textarea name="direccion_envio"
                                              class="form-control store-filter-control @error('direccion_envio') is-invalid @enderror"
                                              rows="4"
                                              placeholder="Señas exactas, casa, local, color, punto de referencia">{{ old('direccion_envio') }}</textarea>

                                    @error('direccion_envio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="store-form-label">Referencia de entrega</label>

                                    <input type="text"
                                           name="referencia_envio"
                                           value="{{ old('referencia_envio') }}"
                                           class="form-control store-filter-control @error('referencia_envio') is-invalid @enderror"
                                           placeholder="Ejemplo: frente a la escuela, portón negro">

                                    @error('referencia_envio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="store-form-label">Link de Google Maps</label>

                                    <div class="d-grid gap-2 d-md-flex">
                                        <input type="url"
                                               name="link_google_maps"
                                               id="checkoutGoogleMaps"
                                               value="{{ old('link_google_maps') }}"
                                               class="form-control store-filter-control @error('link_google_maps') is-invalid @enderror"
                                               placeholder="Pega aquí el link de tu ubicación">

                                        <a href="https://www.google.com/maps"
                                           target="_blank"
                                           class="btn btn-store-outline">
                                            <i class="bi bi-map"></i>
                                            Abrir mapa
                                        </a>
                                    </div>

                                    <small class="text-muted d-block mt-2">
                                        Abre Google Maps, busca tu ubicación, copia el enlace y pégalo aquí.
                                    </small>

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

                            <textarea name="notas"
                                      class="form-control store-filter-control @error('notas') is-invalid @enderror"
                                      rows="3"
                                      placeholder="Ejemplo: llamar antes de entregar">{{ old('notas') }}</textarea>

                            @error('notas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- PAGO --}}
                    <div class="store-checkout-card">
                        <div class="store-checkout-card-header">
                            <h2>
                                <i class="bi bi-credit-card"></i>
                                Método de pago
                            </h2>
                        </div>

                        <div class="store-checkout-card-body">
                            <div class="store-payment-option active">
                                <div class="store-payment-radio">
                                    <i class="bi bi-check"></i>
                                </div>

                                <div>
                                    <h5>SINPE Móvil / Transferencia</h5>
                                    <p>
                                        Primero confirma el pedido. Después podrás subir el comprobante de pago.
                                    </p>
                                </div>
                            </div>
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
                                @endphp

                                <div class="store-checkout-product">
                                    <div class="store-checkout-product-image">
                                        <img src="{{ $imagen }}" alt="{{ $item['nombre'] }}">
                                    </div>

                                    <div class="store-checkout-product-info">
                                        <h5>{{ $item['nombre'] }}</h5>
                                        <span>Cantidad: {{ $item['cantidad'] }}</span>
                                    </div>

                                    <div class="store-checkout-product-price">
                                        ₡{{ number_format($item['precio'] * $item['cantidad'], 2) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="store-checkout-totals">
                            <div class="store-checkout-total-row">
                                <span>Subtotal</span>
                                <strong>₡{{ number_format($subtotal, 2) }}</strong>
                            </div>

                            <div class="store-checkout-total-row">
                                <span>Envío</span>
                                <strong>Según zona seleccionada</strong>
                            </div>

                            <div class="store-checkout-total-row text-success">
                                <span>Descuento</span>
                                <strong>-₡{{ number_format($descuento, 2) }}</strong>
                            </div>

                            <div class="store-checkout-total-row total">
                                <span>Total inicial</span>
                                <strong>₡{{ number_format($total, 2) }}</strong>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-store-primary store-checkout-submit">
                            <i class="bi bi-shield-check me-1"></i>
                            Confirmar pedido
                        </button>

                        <a href="{{ route('tienda.carrito.index') }}"
                           class="btn btn-store-outline w-100 mb-3">
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

    </div>

</section>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tipoEntregaRadios = document.querySelectorAll('input[name="tipo_entrega"]');
    const addressBox = document.getElementById('checkoutAddressBox');

    const provinciaSelect = document.getElementById('checkoutProvincia');
    const cantonSelect = document.getElementById('checkoutCanton');
    const distritoSelect = document.getElementById('checkoutDistrito');

    const oldCanton = "{{ old('id_canton') }}";
    const oldDistrito = "{{ old('id_distrito') }}";

    function toggleEntrega() {
        const tipo = document.querySelector('input[name="tipo_entrega"]:checked')?.value || 'envio';

        if (tipo === 'retiro') {
            addressBox.style.display = 'none';
        } else {
            addressBox.style.display = '';
        }

        document.querySelectorAll('.store-payment-option').forEach(option => {
            const input = option.querySelector('input[type="radio"]');

            if (!input) return;

            option.classList.toggle('active', input.checked);
        });
    }

    function limpiarSelect(select, texto = 'Seleccione') {
        select.innerHTML = `<option value="">${texto}</option>`;
    }

    function cargarCantones(idProvincia, selected = null) {
        limpiarSelect(cantonSelect);
        limpiarSelect(distritoSelect);

        if (!idProvincia) return;

        fetch(`/checkout/cantones/${idProvincia}`)
            .then(response => response.json())
            .then(data => {
                data.forEach(canton => {
                    const option = document.createElement('option');
                    option.value = canton.id_canton;
                    option.textContent = canton.nombre;

                    if (selected && String(selected) === String(canton.id_canton)) {
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

        if (!idCanton) return;

        fetch(`/checkout/distritos/${idCanton}`)
            .then(response => response.json())
            .then(data => {
                data.forEach(distrito => {
                    const option = document.createElement('option');
                    option.value = distrito.id_distrito;
                    option.textContent = distrito.nombre;

                    if (selected && String(selected) === String(distrito.id_distrito)) {
                        option.selected = true;
                    }

                    distritoSelect.appendChild(option);
                });
            });
    }

    tipoEntregaRadios.forEach(radio => {
        radio.addEventListener('change', toggleEntrega);
    });

    provinciaSelect.addEventListener('change', function () {
        cargarCantones(this.value);
    });

    cantonSelect.addEventListener('change', function () {
        cargarDistritos(this.value);
    });

    toggleEntrega();

    if (provinciaSelect.value) {
        cargarCantones(provinciaSelect.value, oldCanton);
    }
});
</script>
@endpush