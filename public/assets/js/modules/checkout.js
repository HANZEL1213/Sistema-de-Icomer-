
document.addEventListener('DOMContentLoaded', function () {

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

        const camposDireccion = [
            provinciaSelect,
            cantonSelect,
            distritoSelect,
            document.querySelector('textarea[name="direccion_envio"]')
        ];

        if (tipo === 'retiro') {

            addressBox.style.display = 'none';

            camposDireccion.forEach(campo => {
                if (!campo) return;
                campo.removeAttribute('required');
                campo.value = '';
            });

            actualizarResumenEnvio(0);

        } else {

            addressBox.style.display = '';

            camposDireccion.forEach(campo => {
                if (!campo) return;
                campo.setAttribute('required', 'required');
            });

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

    provinciaSelect.addEventListener('change', function () {
        cargarCantones(this.value);
    });

    cantonSelect.addEventListener('change', function () {
        cargarDistritos(this.value);
    });

    distritoSelect.addEventListener('change', function () {
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

        marker.on('dragend', function () {
            const position = marker.getLatLng();

            obtenerDireccionDesdeCoordenadas(
                position.lat,
                position.lng,
                'Ubicación seleccionada en el mapa'
            );
        });

        map.on('click', function (e) {
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

                button.addEventListener('click', function () {

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

    btnUseCurrentLocation?.addEventListener('click', function () {

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

            function (position) {

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

            function () {

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

    btnShowMap?.addEventListener('click', function () {

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

    addressSearch?.addEventListener('input', function () {

        clearTimeout(suggestionTimeout);

        suggestionTimeout = setTimeout(() => {

            buscarSugerencias(this.value);

        }, 500);
    });

    addressSearch?.addEventListener('keydown', function (e) {

        if (e.key !== 'Enter') return;

        e.preventDefault();

        const referencia =
            obtenerReferenciaBusqueda();

        const busqueda =
            `${this.value}, ${referencia}`;

        addressSuggestions.style.display = 'none';

        buscarDireccion(busqueda);
    });

    btnLoadManualLocation?.addEventListener('click', function () {

        cargarUbicacionManual();
    });

    manualLocationInput?.addEventListener('keydown', function (e) {

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

    comprobanteInput.addEventListener('change', function (e) {

        const file = e.target.files[0];

        if (!file) {

            comprobantePreview.src = '';

            comprobantePreviewWrap.classList.add('d-none');

            return;
        }

        const reader = new FileReader();

        reader.onload = function (event) {

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

    const tipoEntrega = document.querySelector('input[name="tipo_entrega"]:checked')?.value || 'envio';

    if (tipoEntrega === 'envio') {

        const step2 = document.getElementById('checkoutStep2');

        const campos = step2.querySelectorAll(
            '#checkoutAddressBox input, #checkoutAddressBox select, #checkoutAddressBox textarea');

        for (const campo of campos) {

            if (!campo.checkValidity()) {

                campo.reportValidity();
                return;
            }
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
