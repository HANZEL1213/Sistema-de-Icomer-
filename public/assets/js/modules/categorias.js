document.addEventListener('DOMContentLoaded', () => {
    const nombre = document.getElementById('nombre');
    const slug = document.getElementById('slug');

    const switchActivo = document.getElementById('activoSwitch');
    const estadoTexto = document.getElementById('estadoTexto');

    let slugEditadoManualmente = false;
    let ultimoSlugGenerado = slug ? slug.value.trim() : '';

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
});