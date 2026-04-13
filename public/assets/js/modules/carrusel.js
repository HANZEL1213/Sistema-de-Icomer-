document.addEventListener('DOMContentLoaded', () => {
    /* ======================================================
       🔥 ORDEN PROGRAMADO
    ====================================================== */
    const numero = document.getElementById('ordenNumero');
    const valorSpan = document.getElementById('valorOrden');

    function actualizarPosicion(valor) {
        let valorNormalizado = parseInt(valor, 10);

        if (Number.isNaN(valorNormalizado) || valorNormalizado < 1) {
            valorNormalizado = 1;
        }

        if (numero) numero.value = valorNormalizado;
        if (valorSpan) valorSpan.textContent = valorNormalizado;
    }

    if (numero) {
        numero.addEventListener('input', function () {
            actualizarPosicion(this.value);
        });

        numero.addEventListener('blur', function () {
            actualizarPosicion(this.value);
        });

        actualizarPosicion(numero.value);
    }

    /* ======================================================
       🔥 ACTIVO MANUAL + ESTADO CALCULADO
    ====================================================== */
    const switchActivoVisual = document.getElementById('activoSwitchVisual');
    const activoManualHidden = document.getElementById('activoManualHidden');
    const estadoActivoTexto = document.getElementById('estadoActivoTexto');

    const inputInicia = document.getElementById('inicia_en');
    const inputTermina = document.getElementById('termina_en');

    const estadoCalculadoTexto = document.getElementById('estadoHabilitadoTexto');
    const estadoFechaTexto = document.getElementById('estadoFechaTexto');
    const estadoHelper = document.getElementById('estadoHelper');

    function parseFechaLocal(valor) {
        if (!valor) return null;

        const fecha = new Date(valor);
        return Number.isNaN(fecha.getTime()) ? null : fecha;
    }

    function setButtonState(elemento, clasesAgregar, clasesQuitar, html) {
        if (!elemento) return;

        elemento.classList.remove(...clasesQuitar);
        elemento.classList.add(...clasesAgregar);
        elemento.innerHTML = html;
    }

    function obtenerActivoManual() {
        return !!(activoManualHidden && activoManualHidden.value === '1');
    }

    function setActivoManual(valor) {
        if (activoManualHidden) {
            activoManualHidden.value = valor ? '1' : '0';
        }
    }

    function actualizarEstadoManual() {
        if (!estadoActivoTexto) return;

        if (obtenerActivoManual()) {
            setButtonState(
                estadoActivoTexto,
                ['btn', 'btn-primary-custom', 'btn-sm', 'px-3', 'py-2', 'd-inline-flex', 'align-items-center'],
                ['btn-secondary-custom', 'btn-danger-custom', 'btn-warning-custom', 'btn-info-custom'],
                '<i class="bx bx-check-circle me-1"></i> Permitido manualmente'
            );
        } else {
            setButtonState(
                estadoActivoTexto,
                ['btn', 'btn-danger-custom', 'btn-sm', 'px-3', 'py-2', 'd-inline-flex', 'align-items-center'],
                ['btn-primary-custom', 'btn-secondary-custom', 'btn-warning-custom', 'btn-info-custom'],
                '<i class="bx bx-x-circle me-1"></i> Bloqueado manualmente'
            );
        }
    }

    function actualizarSwitchSegunFechas() {
        if (!switchActivoVisual) return;

        switchActivoVisual.checked = obtenerActivoManual();
        switchActivoVisual.disabled = false;
    }

    function actualizarEstadoCalculado() {
        if (!estadoCalculadoTexto || !estadoFechaTexto || !estadoHelper) return;

        const ahora = new Date();
        const inicia = parseFechaLocal(inputInicia ? inputInicia.value : '');
        const termina = parseFechaLocal(inputTermina ? inputTermina.value : '');
        const activoManual = obtenerActivoManual();

        let rangoValido = false;

        if (inicia && termina && termina > inicia) {
            rangoValido = ahora >= inicia && ahora < termina;
        }

        if (!inicia || !termina) {
            setButtonState(
                estadoFechaTexto,
                ['btn', 'btn-secondary-custom', 'btn-sm', 'px-3', 'py-2', 'd-inline-flex', 'align-items-center'],
                ['btn-primary-custom', 'btn-danger-custom', 'btn-warning-custom', 'btn-info-custom'],
                '<i class="bx bx-calendar me-1"></i> Sin rango definido'
            );

            setButtonState(
                estadoCalculadoTexto,
                ['btn', 'btn-secondary-custom', 'btn-sm', 'px-3', 'py-2', 'd-inline-flex', 'align-items-center'],
                ['btn-primary-custom', 'btn-danger-custom', 'btn-warning-custom', 'btn-info-custom'],
                '<i class="bx bx-error me-1"></i> Falta completar fechas'
            );

            estadoHelper.innerHTML =
                '<i class="bx bx-info-circle me-1"></i> Debes completar correctamente las fechas para que el sistema pueda determinar si el banner entra o no al carrusel.';
            return;
        }

        if (termina <= inicia) {
            setButtonState(
                estadoFechaTexto,
                ['btn', 'btn-danger-custom', 'btn-sm', 'px-3', 'py-2', 'd-inline-flex', 'align-items-center'],
                ['btn-primary-custom', 'btn-secondary-custom', 'btn-warning-custom', 'btn-info-custom'],
                '<i class="bx bx-error-circle me-1"></i> Rango inválido'
            );

            setButtonState(
                estadoCalculadoTexto,
                ['btn', 'btn-danger-custom', 'btn-sm', 'px-3', 'py-2', 'd-inline-flex', 'align-items-center'],
                ['btn-primary-custom', 'btn-secondary-custom', 'btn-warning-custom', 'btn-info-custom'],
                '<i class="bx bx-x-circle me-1"></i> No podrá activarse'
            );

            estadoHelper.innerHTML =
                '<i class="bx bx-info-circle me-1"></i> La fecha final debe ser mayor que la fecha de inicio. Con ese rango el banner no puede entrar al carrusel.';
            return;
        }

        if (ahora < inicia) {
            setButtonState(
                estadoFechaTexto,
                ['btn', 'btn-secondary-custom', 'btn-sm', 'px-3', 'py-2', 'd-inline-flex', 'align-items-center'],
                ['btn-primary-custom', 'btn-danger-custom', 'btn-warning-custom', 'btn-info-custom'],
                '<i class="bx bx-time me-1"></i> Aún no inicia'
            );
        } else if (ahora >= termina) {
            setButtonState(
                estadoFechaTexto,
                ['btn', 'btn-danger-custom', 'btn-sm', 'px-3', 'py-2', 'd-inline-flex', 'align-items-center'],
                ['btn-primary-custom', 'btn-secondary-custom', 'btn-warning-custom', 'btn-info-custom'],
                '<i class="bx bx-calendar-x me-1"></i> Fuera de rango'
            );
        } else {
            setButtonState(
                estadoFechaTexto,
                ['btn', 'btn-primary-custom', 'btn-sm', 'px-3', 'py-2', 'd-inline-flex', 'align-items-center'],
                ['btn-secondary-custom', 'btn-danger-custom', 'btn-warning-custom', 'btn-info-custom'],
                '<i class="bx bx-calendar-check me-1"></i> Dentro de rango'
            );
        }

        if (!activoManual) {
            setButtonState(
                estadoCalculadoTexto,
                ['btn', 'btn-danger-custom', 'btn-sm', 'px-3', 'py-2', 'd-inline-flex', 'align-items-center'],
                ['btn-primary-custom', 'btn-secondary-custom', 'btn-warning-custom', 'btn-info-custom'],
                '<i class="bx bx-power-off me-1"></i> Quedará bloqueado'
            );

            if (rangoValido) {
                estadoHelper.innerHTML =
                    '<i class="bx bx-info-circle me-1"></i> El banner está dentro del rango, pero fue bloqueado manualmente. No entrará al carrusel hasta que vuelvas a permitirlo.';
            } else {
                estadoHelper.innerHTML =
                    '<i class="bx bx-info-circle me-1"></i> El banner está bloqueado manualmente y además no cumple condiciones para entrar al carrusel.';
            }
            return;
        }

        if (rangoValido) {
            setButtonState(
                estadoCalculadoTexto,
                ['btn', 'btn-primary-custom', 'btn-sm', 'px-3', 'py-2', 'd-inline-flex', 'align-items-center'],
                ['btn-secondary-custom', 'btn-danger-custom', 'btn-warning-custom', 'btn-info-custom'],
                '<i class="bx bx-check-circle me-1"></i> Entrará al carrusel al guardar'
            );

            estadoHelper.innerHTML =
                '<i class="bx bx-info-circle me-1"></i> El banner está permitido manualmente y dentro del rango de fechas. Si guardas ahora, entrará al carrusel con su posición programada.';
            return;
        }

        if (ahora < inicia) {
            setButtonState(
                estadoCalculadoTexto,
                ['btn', 'btn-secondary-custom', 'btn-sm', 'px-3', 'py-2', 'd-inline-flex', 'align-items-center'],
                ['btn-primary-custom', 'btn-danger-custom', 'btn-warning-custom', 'btn-info-custom'],
                '<i class="bx bx-time-five me-1"></i> Quedará pendiente'
            );

            estadoHelper.innerHTML =
                '<i class="bx bx-info-circle me-1"></i> El banner queda permitido manualmente, pero todavía no entra al carrusel porque su rango aún no inicia. Se activará automáticamente cuando llegue su fecha.';
            return;
        }

        if (ahora >= termina) {
            setButtonState(
                estadoCalculadoTexto,
                ['btn', 'btn-danger-custom', 'btn-sm', 'px-3', 'py-2', 'd-inline-flex', 'align-items-center'],
                ['btn-primary-custom', 'btn-secondary-custom', 'btn-warning-custom', 'btn-info-custom'],
                '<i class="bx bx-calendar-x me-1"></i> Quedará fuera del carrusel'
            );

            estadoHelper.innerHTML =
                '<i class="bx bx-info-circle me-1"></i> El banner está permitido manualmente, pero su rango ya expiró. Quedará fuera del carrusel y con posición real en 0.';
        }
    }

    function actualizarEstados() {
        actualizarSwitchSegunFechas();
        actualizarEstadoManual();
        actualizarEstadoCalculado();
    }

    if (switchActivoVisual && activoManualHidden) {
        switchActivoVisual.addEventListener('change', () => {
            setActivoManual(switchActivoVisual.checked);
            actualizarEstados();
        });
    }

    if (inputInicia) {
        inputInicia.addEventListener('input', actualizarEstados);
        inputInicia.addEventListener('change', actualizarEstados);
    }

    if (inputTermina) {
        inputTermina.addEventListener('input', actualizarEstados);
        inputTermina.addEventListener('change', actualizarEstados);
    }

    actualizarEstados();

    /* ======================================================
       🔥 IMAGEN (CREATE + EDIT)
    ====================================================== */
    const inputImagen = document.getElementById('ruta_imagen');
    const preview = document.getElementById('preview');
    const previewPlaceholder = document.getElementById('previewPlaceholder');
    const removeImage = document.getElementById('removeImage');
    const eliminarImagen = document.getElementById('eliminar_imagen');
    const imageReplaceHelper = document.getElementById('imageReplaceHelper');

    const MAX_IMAGE_SIZE = 2 * 1024 * 1024;
    let objectUrlActual = null;

    const imagenOriginal = preview ? preview.getAttribute('src') : '';
    const placeholder = preview ? (preview.dataset.placeholder || '') : '';
    const teniaImagenInicial = !!(preview && imagenOriginal && imagenOriginal !== placeholder);
    let imagenMarcadaParaEliminar = false;

    function restaurarPreviewPlaceholder() {
        if (preview) {
            preview.src = '';
            preview.classList.add('d-none');
        }

        if (previewPlaceholder) {
            previewPlaceholder.classList.remove('d-none');
        }
    }

    function restaurarPreviewOriginal() {
        if (!preview) return;

        preview.src = imagenOriginal || '';
        preview.classList.remove('d-none');

        if (previewPlaceholder) {
            previewPlaceholder.classList.add('d-none');
        }
    }

    function ocultarBotonEliminarImagen() {
        if (removeImage) {
            removeImage.classList.add('d-none');
        }
    }

    function mostrarBotonEliminarImagen() {
        if (removeImage) {
            removeImage.classList.remove('d-none');
        }
    }

    function mostrarHelperReemplazo() {
        if (imageReplaceHelper) {
            imageReplaceHelper.classList.remove('d-none');
        }
    }

    function ocultarHelperReemplazo() {
        if (imageReplaceHelper) {
            imageReplaceHelper.classList.add('d-none');
        }
    }

    function obtenerContenedorErrorImagen() {
        if (!inputImagen || !inputImagen.parentElement) return null;

        let errorBox = inputImagen.parentElement.querySelector('.js-image-error');

        if (!errorBox) {
            errorBox = document.createElement('div');
            errorBox.className = 'text-danger small mt-2 js-image-error';
            inputImagen.parentElement.appendChild(errorBox);
        }

        return errorBox;
    }

    function mostrarErrorImagen(mensaje) {
        if (!inputImagen) return;

        inputImagen.classList.add('is-invalid');

        const errorBox = obtenerContenedorErrorImagen();
        if (errorBox) {
            errorBox.textContent = mensaje;
        }
    }

    function limpiarErrorImagen() {
        if (!inputImagen) return;

        inputImagen.classList.remove('is-invalid');

        const errorBox = obtenerContenedorErrorImagen();
        if (errorBox) {
            errorBox.textContent = '';
        }
    }

    function limpiarObjectUrlActual() {
        if (objectUrlActual) {
            URL.revokeObjectURL(objectUrlActual);
            objectUrlActual = null;
        }
    }

    function actualizarEstadoImagenOculta() {
        if (eliminarImagen) {
            eliminarImagen.value = imagenMarcadaParaEliminar ? '1' : '0';
        }
    }

    function restaurarEstadoInicialImagen() {
        if (inputImagen) {
            inputImagen.value = '';
        }

        limpiarObjectUrlActual();
        imagenMarcadaParaEliminar = false;
        actualizarEstadoImagenOculta();
        ocultarHelperReemplazo();

        if (teniaImagenInicial) {
            restaurarPreviewOriginal();
            mostrarBotonEliminarImagen();
        } else {
            restaurarPreviewPlaceholder();
            ocultarBotonEliminarImagen();
        }
    }

    function dejarImagenPendienteDeReemplazo() {
        if (inputImagen) {
            inputImagen.value = '';
        }

        limpiarObjectUrlActual();
        imagenMarcadaParaEliminar = true;
        actualizarEstadoImagenOculta();
        restaurarPreviewPlaceholder();
        ocultarBotonEliminarImagen();
        mostrarHelperReemplazo();
    }

    if (inputImagen && preview && removeImage) {
        inputImagen.addEventListener('change', (event) => {
            const file = event.target.files?.[0];

            limpiarErrorImagen();

            if (!file) {
                if (imagenMarcadaParaEliminar) {
                    restaurarPreviewPlaceholder();
                    ocultarBotonEliminarImagen();
                    mostrarHelperReemplazo();
                } else if (teniaImagenInicial) {
                    restaurarPreviewOriginal();
                    mostrarBotonEliminarImagen();
                    ocultarHelperReemplazo();
                } else {
                    restaurarPreviewPlaceholder();
                    ocultarBotonEliminarImagen();
                    ocultarHelperReemplazo();
                }
                return;
            }

            if (!file.type.startsWith('image/')) {
                mostrarErrorImagen('Selecciona un archivo de imagen válido.');

                if (imagenMarcadaParaEliminar) {
                    dejarImagenPendienteDeReemplazo();
                } else {
                    restaurarEstadoInicialImagen();
                }
                return;
            }

            if (file.size > MAX_IMAGE_SIZE) {
                mostrarErrorImagen('La imagen no debe superar los 2MB.');

                if (imagenMarcadaParaEliminar) {
                    dejarImagenPendienteDeReemplazo();
                } else {
                    restaurarEstadoInicialImagen();
                }
                return;
            }

            limpiarObjectUrlActual();
            objectUrlActual = URL.createObjectURL(file);

            preview.src = objectUrlActual;
            preview.classList.remove('d-none');

            if (previewPlaceholder) {
                previewPlaceholder.classList.add('d-none');
            }

            imagenMarcadaParaEliminar = false;
            actualizarEstadoImagenOculta();
            mostrarBotonEliminarImagen();
            ocultarHelperReemplazo();
        });

        removeImage.addEventListener('click', () => {
            limpiarErrorImagen();

            const hayNuevaImagenSeleccionada = !!(inputImagen.files && inputImagen.files.length > 0);

            if (hayNuevaImagenSeleccionada) {
                if (teniaImagenInicial) {
                    restaurarEstadoInicialImagen();
                } else {
                    if (inputImagen) {
                        inputImagen.value = '';
                    }

                    limpiarObjectUrlActual();
                    restaurarPreviewPlaceholder();
                    ocultarBotonEliminarImagen();
                    ocultarHelperReemplazo();
                    imagenMarcadaParaEliminar = false;
                    actualizarEstadoImagenOculta();
                }
                return;
            }

            if (teniaImagenInicial) {
                dejarImagenPendienteDeReemplazo();
            } else {
                restaurarPreviewPlaceholder();
                ocultarBotonEliminarImagen();
                ocultarHelperReemplazo();
                imagenMarcadaParaEliminar = false;
                actualizarEstadoImagenOculta();
            }
        });

        if (teniaImagenInicial) {
            preview.classList.remove('d-none');

            if (previewPlaceholder) {
                previewPlaceholder.classList.add('d-none');
            }

            mostrarBotonEliminarImagen();
        } else {
            restaurarPreviewPlaceholder();
            ocultarBotonEliminarImagen();
        }

        actualizarEstadoImagenOculta();
    }

    /* ======================================================
       🔥 TIPO DESTINO
    ====================================================== */
    const tipo = document.getElementById('tipoDestino');
    const campoUrl = document.getElementById('campoUrl');
    const campoProducto = document.getElementById('campoProducto');
    const campoCategoria = document.getElementById('campoCategoria');

    function actualizarDestino(limpiar = false) {
        if (!tipo || !campoUrl || !campoProducto || !campoCategoria) return;

        const inputUrl = campoUrl.querySelector('input');
        const selectProducto = campoProducto.querySelector('select');
        const selectCategoria = campoCategoria.querySelector('select');

        campoUrl.classList.add('d-none');
        campoProducto.classList.add('d-none');
        campoCategoria.classList.add('d-none');

        if (inputUrl) inputUrl.disabled = true;
        if (selectProducto) selectProducto.disabled = true;
        if (selectCategoria) selectCategoria.disabled = true;

        if (limpiar) {
            if (tipo.value !== 'url' && inputUrl) inputUrl.value = '';
            if (tipo.value !== 'producto' && selectProducto) selectProducto.value = '';
            if (tipo.value !== 'categoria' && selectCategoria) selectCategoria.value = '';
        }

        if (tipo.value === 'url') {
            campoUrl.classList.remove('d-none');
            if (inputUrl) inputUrl.disabled = false;
        }

        if (tipo.value === 'producto') {
            campoProducto.classList.remove('d-none');
            if (selectProducto) selectProducto.disabled = false;
        }

        if (tipo.value === 'categoria') {
            campoCategoria.classList.remove('d-none');
            if (selectCategoria) selectCategoria.disabled = false;
        }
    }

    if (tipo && campoUrl && campoProducto && campoCategoria) {
        tipo.addEventListener('change', () => actualizarDestino(true));
        actualizarDestino(false);
    }

    window.addEventListener('beforeunload', () => {
        limpiarObjectUrlActual();
    });
});