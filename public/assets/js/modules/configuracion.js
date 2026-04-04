document.addEventListener('DOMContentLoaded', function () {

    const valor = document.getElementById('valorInput');
    const preview = document.getElementById('previewTexto');

    // Detectamos si existe claveInput (CREATE) o no (EDIT)
    const claveInput = document.getElementById('claveInput');

    // Si es CREATE, la clave es editable
    let claveFija = null;

    // Si es EDIT, la clave viene desde un input deshabilitado SIN ID
    // así que la tomamos desde el span del preview inicial.
    if (!claveInput) {
        // Extraemos la clave del preview inicial: "clave → valor"
        const previewInicial = preview.textContent.trim();
        claveFija = previewInicial.split("→")[0].trim();
    }

    function actualizarPreview() {
        let claveFinal = '';

        if (claveInput) {
            // CREATE
            claveFinal = claveInput.value.trim() || 'clave';
        } else {
            // EDIT
            claveFinal = claveFija;
        }

        const valorFinal = valor.value.trim() || 'valor';

        preview.textContent = `${claveFinal} → ${valorFinal}`;
    }

    // EVENTOS
    if (claveInput) {
        claveInput.addEventListener('input', actualizarPreview);
    }

    valor.addEventListener('input', actualizarPreview);

    // PREVIEW INICIAL
    actualizarPreview();
});