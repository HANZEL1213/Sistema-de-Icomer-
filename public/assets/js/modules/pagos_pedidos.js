
document.addEventListener('DOMContentLoaded', function () {
    const openVoucherModalBtn = document.getElementById('openVoucherModalBtn');
    const voucherPreviewImage = document.getElementById('voucherPreviewImage');
    const voucherModalImage = document.getElementById('voucherModalImage');
    const voucherModalEl = document.getElementById('voucherModal');

    function openVoucherModal(imageSrc) {
        if (!voucherModalEl || !voucherModalImage || !imageSrc || typeof bootstrap === 'undefined') {
            return;
        }

        voucherModalImage.src = imageSrc;

        const modal = new bootstrap.Modal(voucherModalEl);
        modal.show();
    }

    if (openVoucherModalBtn) {
        openVoucherModalBtn.addEventListener('click', function () {
            openVoucherModal(this.dataset.voucher);
        });
    }

    if (voucherPreviewImage) {
        voucherPreviewImage.addEventListener('click', function () {
            openVoucherModal(this.src);
        });
    }

    const copyComprobanteBtn = document.getElementById('copyComprobanteBtn');
    const numeroComprobanteTexto = document.getElementById('numeroComprobanteTexto');

    if (copyComprobanteBtn && numeroComprobanteTexto) {
        copyComprobanteBtn.addEventListener('click', async function () {
            const text = numeroComprobanteTexto.textContent.trim();

            if (!text || text === 'Sin número registrado') {
                return;
            }

            try {
                await navigator.clipboard.writeText(text);

                const originalHtml = this.innerHTML;
                this.innerHTML = '<i class="bx bx-check"></i><span>Copiado</span>';

                setTimeout(() => {
                    this.innerHTML = originalHtml;
                }, 1800);
            } catch (error) {
                console.error('No se pudo copiar el comprobante.', error);
            }
        });
    }
});
