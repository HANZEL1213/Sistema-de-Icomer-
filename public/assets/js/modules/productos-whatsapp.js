document.addEventListener('click', async function (e) {
    const btn = e.target.closest('.btn-whatsapp-status');
    if (!btn) return;

    const imagenUrl = btn.dataset.imagen;
    const productoUrl = btn.dataset.url;
    const nombre = btn.dataset.nombre;
    const texto = `${nombre}\n${productoUrl}`;

    if (!navigator.share) {
        alert('Tu navegador no soporta compartir directo. Ábrelo desde el celular (Chrome Android o Safari iOS) para usar esta opción.');
        return;
    }

    btn.classList.add('is-loading');

    // Por defecto compartimos solo texto+link (esto SIEMPRE funciona,
    // incluso si la imagen falla o si iOS bloquea el share con archivo)
    let shareData = { text: texto, title: nombre };

    if (imagenUrl) {
        try {
            const response = await fetch(imagenUrl);

            if (response.ok) {
                const blob = await response.blob();

                // Extensión dinámica según el tipo real del blob,
                // en vez de forzar siempre .jpg
                const mime = blob.type || 'image/jpeg';
                const ext = mime.split('/')[1]?.split('+')[0] || 'jpg';
                const file = new File([blob], `producto.${ext}`, { type: mime });

                if (navigator.canShare && navigator.canShare({ files: [file] })) {
                    shareData.files = [file];
                }
            }
        } catch (err) {
            // Si falla traer la imagen (red lenta, CORS, 404, etc.)
            // no tumbamos el compartir: seguimos solo con texto+link
            console.warn('No se pudo adjuntar la imagen, se comparte solo texto:', err);
        }
    }

    try {
        await navigator.share(shareData);
        // El usuario elige "WhatsApp" -> "Estado" en el menú del sistema
        // y ya le queda solo darle "Subir" / "Enviar" allá.
    } catch (err) {
        if (err.name !== 'AbortError') {
            console.error('Error al compartir:', err);
            alert('No se pudo abrir el compartir. Intenta de nuevo desde el celular.');
        }
    } finally {
        btn.classList.remove('is-loading');
    }
});
