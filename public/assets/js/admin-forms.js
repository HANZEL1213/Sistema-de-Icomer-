 /* JS admin-forms */

document.addEventListener('DOMContentLoaded', function () {

    /* ======================================================
       🔥 PREVIEW UNIVERSAL DE IMAGEN (Carrusel / Marca / Categoría)
       ====================================================== */

    const input = document.getElementById('imagen');
    const preview = document.getElementById('preview');
    const removeBtn = document.getElementById('removeImage');

    let currentImage = null;

    if (input && preview) {

        input.addEventListener('change', function () {

            const file = this.files[0];
            if (!file) return;

            // liberar memoria anterior
            if (currentImage) {
                URL.revokeObjectURL(currentImage);
            }

            currentImage = URL.createObjectURL(file);
            preview.src = currentImage;

            if (removeBtn) {
                removeBtn.classList.remove('d-none');
            }
        });

    }

    if (removeBtn && input && preview) {

        removeBtn.addEventListener('click', function () {

            if (currentImage) {
                URL.revokeObjectURL(currentImage);
                currentImage = null;
            }

            input.value = '';
            preview.src = preview.dataset.placeholder || '';

            removeBtn.classList.add('d-none');
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

});