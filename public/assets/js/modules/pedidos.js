document.addEventListener('DOMContentLoaded', function () {
    const scrollButtons = document.querySelectorAll('[data-scroll-target]');

    scrollButtons.forEach((btn) => {
        btn.addEventListener('click', function () {
            const target = document.querySelector(this.dataset.scrollTarget);

            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start',
                });
            }
        });
    });

    const rejectForm = document.getElementById('rejectPaymentForm');
    const motivoInput = document.getElementById('motivo_rechazo');
    const accionesPanel = document.getElementById('accionesPanel');

    if (rejectForm && motivoInput) {
        rejectForm.addEventListener('submit', function (e) {
            const motivo = motivoInput.value.trim();

            if (motivo.length < 5) {
                e.preventDefault();
                motivoInput.focus();
            }
        });

        const hasMotivoError = motivoInput.dataset.hasError === '1';

        if (hasMotivoError) {
            if (accionesPanel) {
                setTimeout(() => {
                    accionesPanel.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start',
                    });
                }, 150);
            }

            motivoInput.focus();
        }
    }

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

            if (!text || text === '—') {
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

    const currentStep = document.querySelector('.verify-step.is-current');

    if (currentStep && window.innerWidth > 991) {
        currentStep.scrollIntoView({
            behavior: 'smooth',
            block: 'nearest',
            inline: 'center',
        });
    }

    const estadoForm = document.getElementById('estadoForm');
    const estadoSelect = document.getElementById('estadoSelect');

    if (estadoForm && estadoSelect) {
        estadoForm.addEventListener('submit', function (e) {
            const estado = estadoSelect.value;

            if (!estado) {
                e.preventDefault();
                estadoSelect.focus();
            }
        });
    }
});