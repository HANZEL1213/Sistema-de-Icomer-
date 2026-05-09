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


            <form action="{{ route('tienda.checkout.confirmar') }}" method="POST" enctype="multipart/form-data">
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

                                        <input type="text" name="nombre_cliente" value="{{ old('nombre_cliente') }}"
                                            class="form-control store-filter-control @error('nombre_cliente') is-invalid @enderror"
                                            placeholder="Nombre completo">

                                        @error('nombre_cliente')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label class="store-form-label">Teléfono</label>

                                        <input type="text" name="telefono_cliente" value="{{ old('telefono_cliente') }}"
                                            class="form-control store-filter-control @error('telefono_cliente') is-invalid @enderror"
                                            placeholder="8888-8888">

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
                                            <input type="radio" id="tipoEntregaEnvio" name="tipo_entrega" value="envio"
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
                                            <input type="radio" id="tipoEntregaRetiro" name="tipo_entrega" value="retiro"
                                                class="d-none" {{ old('tipo_entrega') === 'retiro' ? 'checked' : '' }}>

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

                                        <select name="id_provincia" id="checkoutProvincia"
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

                                        <select name="id_canton" id="checkoutCanton"
                                            class="form-select store-filter-control @error('id_canton') is-invalid @enderror">
                                            <option value="">Seleccione</option>
                                        </select>

                                        @error('id_canton')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <label class="store-form-label">Distrito</label>

                                        <select name="id_distrito" id="checkoutDistrito"
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
                                            class="form-control store-filter-control @error('direccion_envio') is-invalid @enderror" rows="4"
                                            placeholder="Señas exactas, casa, local, color, punto de referencia">{{ old('direccion_envio') }}</textarea>

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
                                        <label class="store-form-label">Link de Google Maps</label>

                                        <div class="d-grid gap-2 d-md-flex">
                                            <input type="url" name="link_google_maps" id="checkoutGoogleMaps"
                                                value="{{ old('link_google_maps') }}"
                                                class="form-control store-filter-control @error('link_google_maps') is-invalid @enderror"
                                                placeholder="Pega aquí el link de tu ubicación">

                                            <a href="https://www.google.com/maps" target="_blank"
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

                                <textarea name="notas" class="form-control store-filter-control @error('notas') is-invalid @enderror"
                                    rows="3" placeholder="Ejemplo: llamar antes de entregar">{{ old('notas') }}</textarea>

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
                                        <strong>SINPE:</strong> 8888-8888
                                    </p>

                                    <p class="mb-1">
                                        <strong>Nombre:</strong> Mi Tienda Online
                                    </p>

                                    <p class="mb-0">
                                        <strong>Total inicial:</strong>
                                        ₡{{ number_format($total, 2) }}
                                    </p>

                                </div>

                                <div class="row g-3 mt-3">

                                    <div class="col-12">
                                        <label class="store-form-label">
                                            Número de comprobante o referencia
                                        </label>

                                        <input type="text" name="numero_comprobante"
                                            value="{{ old('numero_comprobante') }}"
                                            class="form-control store-filter-control @error('numero_comprobante') is-invalid @enderror"
                                            placeholder="Ejemplo: 154848484">

                                        <small class="text-muted d-block mt-2">
                                            Podés escribir el número del voucher, referencia SINPE o comprobante bancario.
                                        </small>

                                        @error('numero_comprobante')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <label class="store-form-label">
                                            Imagen del comprobante
                                        </label>

                                        <input type="file" name="comprobante_pago" id="checkoutComprobanteInput"
                                            accept="image/png,image/jpeg,image/jpg,image/webp"
                                            class="form-control store-filter-control @error('comprobante_pago') is-invalid @enderror">

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

        {{-- CUPÓN APLICADO --}}
        @if($cuponAplicado)
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

                            @if($cuponAplicado['tipo'] === 'porcentaje')
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
                <span>Descuento</span>

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
                const totalFinal = subtotalValue - descuentoValue + Number(costo || 0);

                if (envioText) {
                    envioText.textContent = Number(costo) > 0 ?
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
                            actualizarResumenEnvio(Number(data.costo || 0));
                        } else {
                            resetResumenEnvio('No disponible');
                        }
                    })
                    .catch(() => {
                        resetResumenEnvio('No disponible');
                    });
            }

            function toggleEntrega() {
                const tipo = document.querySelector('input[name="tipo_entrega"]:checked')?.value || 'envio';

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
                resetResumenEnvio();

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
                resetResumenEnvio();

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
                cargarCantones(provinciaSelect.value, oldCanton);
            }
        });


        /*
        |--------------------------------------------------------------------------
        | Preview comprobante
        |--------------------------------------------------------------------------
        */

        const comprobanteInput = document.getElementById('checkoutComprobanteInput');
        const comprobantePreview = document.getElementById('checkoutProofPreview');
        const comprobantePreviewWrap = document.getElementById('checkoutProofPreviewWrap');

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

                    comprobantePreview.src = event.target.result;

                    comprobantePreviewWrap.classList.remove('d-none');
                };

                reader.readAsDataURL(file);
            });
        }
    </script>
@endpush
