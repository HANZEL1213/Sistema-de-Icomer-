document.addEventListener('DOMContentLoaded', () => {
    const nombre = document.getElementById('nombre');
    const slug = document.getElementById('slug');

    const switchActivo = document.getElementById('activoSwitch');
    const estadoTexto = document.getElementById('estadoTexto');

    const inputImagen = document.getElementById('imagen');
    const preview = document.getElementById('preview');
    const removeImage = document.getElementById('removeImage');
    const eliminarImagen = document.getElementById('eliminar_imagen');

    const MAX_IMAGE_SIZE = 2 * 1024 * 1024; // 2MB

    let slugEditadoManualmente = false;
    let ultimoSlugGenerado = slug ? slug.value.trim() : '';

    const imagenOriginal = preview ? preview.getAttribute('src') : '';
    const placeholder = preview ? (preview.dataset.placeholder || '') : '';
    const teniaImagenInicial = !!(preview && imagenOriginal && imagenOriginal !== placeholder);

    function generarSlug(texto) {
        return texto
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9\s-]/g, '')
            .trim()
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');
    }

    function actualizarEstadoVisual() {
        if (!switchActivo || !estadoTexto) return;

        if (switchActivo.checked) {
            estadoTexto.innerHTML = '<i class="bx bx-check-circle me-1"></i> Activo';
            estadoTexto.classList.remove('bg-secondary');
            estadoTexto.classList.add('bg-success');
        } else {
            estadoTexto.innerHTML = '<i class="bx bx-x-circle me-1"></i> Inactivo';
            estadoTexto.classList.remove('bg-success');
            estadoTexto.classList.add('bg-secondary');
        }
    }

    function restaurarPreviewPlaceholder() {
        if (!preview) return;
        preview.src = preview.dataset.placeholder || '';
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

    function resetearImagenSeleccionada(marcarEliminacion = false) {
        if (inputImagen) {
            inputImagen.value = '';
        }

        restaurarPreviewPlaceholder();

        if (eliminarImagen) {
            eliminarImagen.value = marcarEliminacion ? '1' : '0';
        }

        ocultarBotonEliminarImagen();
    }

    if (slug) {
        slug.addEventListener('input', () => {
            const valorActual = slug.value.trim();
            slugEditadoManualmente = valorActual !== '' && valorActual !== ultimoSlugGenerado;
        });
    }

    if (nombre && slug) {
        nombre.addEventListener('input', () => {
            if (!slugEditadoManualmente || slug.value.trim() === '' || slug.value.trim() === ultimoSlugGenerado) {
                const nuevoSlug = generarSlug(nombre.value);
                slug.value = nuevoSlug;
                ultimoSlugGenerado = nuevoSlug;
                slugEditadoManualmente = false;
            }
        });
    }

    if (switchActivo && estadoTexto) {
        switchActivo.addEventListener('change', actualizarEstadoVisual);
        actualizarEstadoVisual();
    }

    if (inputImagen && preview && removeImage) {
        inputImagen.addEventListener('change', (event) => {
            const file = event.target.files && event.target.files[0];

            limpiarErrorImagen();

            if (!file) {
                return;
            }

            if (!file.type.startsWith('image/')) {
                mostrarErrorImagen('Selecciona un archivo de imagen válido.');
                resetearImagenSeleccionada(false);
                return;
            }

            if (file.size > MAX_IMAGE_SIZE) {
                mostrarErrorImagen('La imagen no debe superar los 2MB.');
                resetearImagenSeleccionada(false);
                return;
            }

            const reader = new FileReader();

            reader.onload = (e) => {
                preview.src = e.target.result;
                mostrarBotonEliminarImagen();

                if (eliminarImagen) {
                    eliminarImagen.value = '0';
                }
            };

            reader.readAsDataURL(file);
        });

        removeImage.addEventListener('click', () => {
            limpiarErrorImagen();
            resetearImagenSeleccionada(teniaImagenInicial);
        });
    }
});