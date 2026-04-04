document.addEventListener('DOMContentLoaded', () => {
    const provinciaSelect = document.getElementById('id_provincia');
    const cantonSelect = document.getElementById('id_canton');
    const distritoSelect = document.getElementById('id_distrito');
    const switchActivo = document.getElementById('activoSwitch');
    const estadoTexto = document.getElementById('estadoTexto');

    if (!provinciaSelect || !cantonSelect || !distritoSelect) {
        return;
    }

    const baseUrl = document.body.dataset.adminBaseUrl || '/admin';
    const oldCanton = cantonSelect.dataset.selected || '';
    const oldDistrito = distritoSelect.dataset.selected || '';

    function resetSelect(select, placeholder) {
        select.innerHTML = `<option value="">${placeholder}</option>`;
        select.disabled = true;
    }

    function fillSelect(select, items, valueKey, textKey, selectedValue = '') {
        select.innerHTML = '<option value="">Seleccione una opción</option>';

        items.forEach((item) => {
            const option = document.createElement('option');
            option.value = item[valueKey];
            option.textContent = item[textKey];

            if (String(item[valueKey]) === String(selectedValue)) {
                option.selected = true;
            }

            select.appendChild(option);
        });

        select.disabled = false;
    }

    async function fetchJson(url) {
        const response = await fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
        });

        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }

        return await response.json();
    }

    async function cargarCantones(idProvincia, selectedCanton = '', selectedDistrito = '') {
        resetSelect(cantonSelect, 'Cargando cantones...');
        resetSelect(distritoSelect, 'Seleccione primero un cantón');

        if (!idProvincia) {
            resetSelect(cantonSelect, 'Seleccione primero una provincia');
            return;
        }

        try {
            const data = await fetchJson(`${baseUrl}/zonas-envio/cantones/${idProvincia}`);
            fillSelect(cantonSelect, data, 'id_canton', 'nombre', selectedCanton);

            if (selectedCanton) {
                await cargarDistritos(selectedCanton, selectedDistrito);
            }
        } catch (error) {
            console.error('Error al cargar cantones:', error);
            resetSelect(cantonSelect, 'Error al cargar cantones');
        }
    }

    async function cargarDistritos(idCanton, selectedDistrito = '') {
        resetSelect(distritoSelect, 'Cargando distritos...');

        if (!idCanton) {
            resetSelect(distritoSelect, 'Seleccione primero un cantón');
            return;
        }

        try {
            const data = await fetchJson(`${baseUrl}/zonas-envio/distritos/${idCanton}`);
            fillSelect(distritoSelect, data, 'id_distrito', 'nombre', selectedDistrito);
        } catch (error) {
            console.error('Error al cargar distritos:', error);
            resetSelect(distritoSelect, 'Error al cargar distritos');
        }
    }

    function actualizarEstadoActivo() {
        if (!switchActivo || !estadoTexto) {
            return;
        }

        if (switchActivo.checked) {
            estadoTexto.classList.remove('bg-secondary', 'bg-danger');
            estadoTexto.classList.add('bg-success');
            estadoTexto.innerHTML = '<i class="bx bx-check-circle me-1"></i> Activo';
        } else {
            estadoTexto.classList.remove('bg-success', 'bg-danger');
            estadoTexto.classList.add('bg-secondary');
            estadoTexto.innerHTML = '<i class="bx bx-x-circle me-1"></i> Inactivo';
        }
    }

    provinciaSelect.addEventListener('change', function () {
        cargarCantones(this.value, '', '');
    });

    cantonSelect.addEventListener('change', function () {
        cargarDistritos(this.value, '');
    });

    if (switchActivo && estadoTexto) {
        switchActivo.addEventListener('change', actualizarEstadoActivo);
        actualizarEstadoActivo();
    }

    if (provinciaSelect.value) {
        const hayCantones = cantonSelect.options.length > 1;
        const hayDistritos = distritoSelect.options.length > 1;

        if (!hayCantones) {
            cargarCantones(provinciaSelect.value, oldCanton, oldDistrito);
        } else if (cantonSelect.value && !hayDistritos) {
            cargarDistritos(cantonSelect.value, oldDistrito);
        }
    }
});