 /* JS admin-forms */

document.addEventListener('DOMContentLoaded', function () {

/* ======================================================
   🔥 IMAGEN SIMPLE EDIT (Marca / Categoría)
====================================================== */

const inputImagen = document.getElementById('imagen');
const preview = document.getElementById('preview');
const previewPlaceholder = document.getElementById('previewPlaceholder');
const removeImage = document.getElementById('removeImage');
const eliminarImagen = document.getElementById('eliminar_imagen');

let objectUrlActual = null;

const imagenOriginal = preview ? preview.getAttribute('src') : '';
const placeholder = preview ? (preview.dataset.placeholder || '') : '';
const teniaImagenInicial = !!(preview && imagenOriginal && imagenOriginal !== placeholder);

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

function limpiarObjectUrlActual() {
    if (objectUrlActual) {
        URL.revokeObjectURL(objectUrlActual);
        objectUrlActual = null;
    }
}

function marcarEliminacion(valor) {
    if (eliminarImagen) {
        eliminarImagen.value = valor ? '1' : '0';
    }
}

if (inputImagen && preview && removeImage) {
    inputImagen.addEventListener('change', (event) => {
        const file = event.target.files?.[0];

        if (!file) {
            limpiarObjectUrlActual();

            if (teniaImagenInicial && (!eliminarImagen || eliminarImagen.value !== '1')) {
                restaurarPreviewOriginal();
                mostrarBotonEliminarImagen();
            } else {
                restaurarPreviewPlaceholder();
                ocultarBotonEliminarImagen();
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

        marcarEliminacion(false);
        mostrarBotonEliminarImagen();
    });

    removeImage.addEventListener('click', () => {
        inputImagen.value = '';
        limpiarObjectUrlActual();

        if (teniaImagenInicial) {
            marcarEliminacion(true);
            restaurarPreviewPlaceholder();
            ocultarBotonEliminarImagen();
        } else {
            marcarEliminacion(false);
            restaurarPreviewPlaceholder();
            ocultarBotonEliminarImagen();
        }
    });

    if (teniaImagenInicial) {
        marcarEliminacion(false);
        restaurarPreviewOriginal();
        mostrarBotonEliminarImagen();
    } else {
        marcarEliminacion(false);
        restaurarPreviewPlaceholder();
        ocultarBotonEliminarImagen();
    }

    window.addEventListener('beforeunload', () => {
        limpiarObjectUrlActual();
    });
}

    /* ======================================================
       🔥 ESTADO SWITCH UNIVERSAL
       ====================================================== */

    const switchActivo = document.getElementById('activoSwitch');
    const estadoTexto = document.getElementById('estadoTexto');

    if (switchActivo && estadoTexto) {

        switchActivo.addEventListener('change', function () {

            if (this.checked) {
                estadoTexto.innerHTML = '<i class="bx bx-check-circle me-1"></i> Activo';
                estadoTexto.classList.remove('bg-secondary');
                estadoTexto.classList.add('bg-success');
            } else {
                estadoTexto.innerHTML = '<i class="bx bx-x-circle me-1"></i> Inactivo';
                estadoTexto.classList.remove('bg-success');
                estadoTexto.classList.add('bg-secondary');
            }

        });

    }

});


// =========================================================
// 🌐 MODAL GLOBAL DE ELIMINACIÓN (Reutilizable en todo el sistema)
// =========================================================

document.addEventListener("DOMContentLoaded", () => {

    let globalFormDelete = null;
    let modalInstance = null;

    // Delegado para TODOS los botones con la clase .btn-delete-modal
    document.body.addEventListener("click", function (e) {
        const btn = e.target.closest(".btn-delete-modal");
        if (!btn) return;

        globalFormDelete = btn.closest("form");

        const clave = btn.dataset.clave || "—";
        const valor = btn.dataset.valor || "—";

        // Rellenar info del modal
        const labelClave = document.getElementById("labelClave");
        const labelValor = document.getElementById("labelValor");

        if (labelClave) labelClave.textContent = clave;
        if (labelValor) labelValor.textContent = valor;

        // Abrir modal
        const modalEl = document.getElementById("modalEliminar");
        modalInstance = new bootstrap.Modal(modalEl, {
            backdrop: "static",
            keyboard: false
        });

        modalInstance.show();

        // Animación de entrada
        const modalContent = modalEl.querySelector(".modal-content");
        modalContent.classList.add("modal-animate-in");
        setTimeout(() => modalContent.classList.remove("modal-animate-in"), 300);
    });

    // Confirmar eliminación
    const confirmBtn = document.getElementById("btnConfirmarEliminar");
    if (confirmBtn) {
        confirmBtn.addEventListener("click", function () {
            if (globalFormDelete) {
                confirmBtn.disabled = true;
                confirmBtn.innerHTML = `
                    <i class="bx bx-loader-circle bx-spin me-1"></i> Eliminando...
                `;
                globalFormDelete.submit();
            }
        });
    }

    // Limpiar estado al cerrar
    const modalEl = document.getElementById("modalEliminar");
    if (modalEl) {
        modalEl.addEventListener("hidden.bs.modal", function () {
            globalFormDelete = null;
            if (confirmBtn) {
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = `
                    <i class="bx bx-check-circle me-1"></i> Sí, eliminar
                `;
            }
        });
    }
});



document.addEventListener("DOMContentLoaded", function () {

    /* ======================================================
       🔥 ALERTAS GLOBALES DE SESIÓN (ÉXITO / ERROR)
       ====================================================== */

    const flashSuccess = document.body.dataset.flashSuccess || "";
    const flashError = document.body.dataset.flashError || "";

    if (flashSuccess) {
        Swal.fire({
            icon: "success",
            title: flashSuccess,
            toast: true,                 // ✅ activar estilo toast
            position: "top-end",         // ✅ arriba a la derecha
            timer: 2500,                 // ⏳ duración
            timerProgressBar: true,
            showConfirmButton: false,
        });
    }

    if (flashError) {
        Swal.fire({
            icon: "error",
            title: flashError,
            toast: true,                 // ✅ estilo toast
            position: "top-end",         // ✅ arriba a la derecha
            timer: 2800,
            timerProgressBar: true,
            showConfirmButton: false,
        });
    }


/* =========================================
   EVITAR CAMBIO ACCIDENTAL EN INPUTS NUMBER
========================================= */
document.addEventListener('focusin', function (e) {

    if (e.target.matches('input[type="number"]')) {

        e.target.addEventListener('wheel', bloquearScroll, {
            passive: false
        });

    }

});

function bloquearScroll(e) {

    e.preventDefault();

}
    
});