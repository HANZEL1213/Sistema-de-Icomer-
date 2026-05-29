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

    /*
    |--------------------------------------------------------------------------
    | PEDIDOS ADMIN - Vista visual / tabla / filtros / paginación
    |--------------------------------------------------------------------------
    */

    const viewButtons = document.querySelectorAll('.orders-view-btn');
    const boardView = document.querySelector('.orders-board-view');
    const tableView = document.querySelector('.orders-table-view');

    const filterButtons = document.querySelectorAll('.orders-filter-btn');
    const searchInput = document.getElementById('searchInput');
    const clearSearch = document.getElementById('clearSearch');
    const emptyFiltered = document.getElementById('ordersEmptyFiltered');

    const perPageSelect = document.getElementById('perPageSelect');
    const pagination = document.getElementById('ordersPagination');

    const paginationFrom = document.getElementById('pagination-from');
    const paginationTo = document.getElementById('pagination-to');
    const paginationTotal = document.getElementById('pagination-total');

    let ordersCurrentStatus = 'all';
    let ordersCurrentView = 'board';
    let ordersCurrentPage = 1;

    function ordersGetActiveItems() {
        if (ordersCurrentView === 'table') {
            return Array.from(document.querySelectorAll('.order-table-item'));
        }

        return Array.from(document.querySelectorAll('.order-board-item'));
    }

    function ordersGetAllItems() {
        return Array.from(document.querySelectorAll('.order-filter-item'));
    }

    function ordersItemMatches(item, search) {
        const status = item.dataset.status || '';
        const text = item.dataset.search || '';

        const matchStatus = ordersCurrentStatus === 'all' || status === ordersCurrentStatus;
        const matchSearch = !search || text.includes(search);

        return matchStatus && matchSearch;
    }

    function ordersApplyFilters() {
        if (!pagination) {
            return;
        }

        const search = (searchInput?.value || '').toLowerCase().trim();
        const perPage = parseInt(perPageSelect?.value || '10', 10);

        const allItems = ordersGetAllItems();
        const activeItems = ordersGetActiveItems();

        allItems.forEach(item => {
            item.style.display = 'none';
        });

        const matchedItems = activeItems.filter(item => ordersItemMatches(item, search));

        const total = matchedItems.length;
        const totalPages = Math.max(Math.ceil(total / perPage), 1);

        if (ordersCurrentPage > totalPages) {
            ordersCurrentPage = totalPages;
        }

        const start = (ordersCurrentPage - 1) * perPage;
        const end = start + perPage;

        matchedItems.slice(start, end).forEach(item => {
            item.style.display = '';
        });

        ordersUpdatePagination(total, perPage, totalPages);

        if (emptyFiltered) {
            emptyFiltered.classList.toggle(
                'd-none',
                !(ordersCurrentView === 'board' && total === 0)
            );
        }
    }

    function ordersUpdatePagination(total, perPage, totalPages) {
        const from = total === 0 ? 0 : ((ordersCurrentPage - 1) * perPage) + 1;
        const to = Math.min(ordersCurrentPage * perPage, total);

        if (paginationFrom) paginationFrom.textContent = from;
        if (paginationTo) paginationTo.textContent = to;
        if (paginationTotal) paginationTotal.textContent = total;

        pagination.innerHTML = '';

        const prevDisabled = ordersCurrentPage === 1 ? 'disabled' : '';
        const nextDisabled = ordersCurrentPage === totalPages ? 'disabled' : '';

        pagination.insertAdjacentHTML('beforeend', `
            <li class="page-item ${prevDisabled}">
                <a class="page-link" href="#" data-orders-page="${ordersCurrentPage - 1}">
                    <i class="bx bx-chevron-left"></i>
                </a>
            </li>
        `);

        for (let page = 1; page <= totalPages; page++) {
            if (
                page === 1 ||
                page === totalPages ||
                Math.abs(page - ordersCurrentPage) <= 1
            ) {
                pagination.insertAdjacentHTML('beforeend', `
                    <li class="page-item ${page === ordersCurrentPage ? 'active' : ''}">
                        <a class="page-link" href="#" data-orders-page="${page}">${page}</a>
                    </li>
                `);
            } else if (
                page === ordersCurrentPage - 2 ||
                page === ordersCurrentPage + 2
            ) {
                pagination.insertAdjacentHTML('beforeend', `
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                `);
            }
        }

        pagination.insertAdjacentHTML('beforeend', `
            <li class="page-item ${nextDisabled}">
                <a class="page-link" href="#" data-orders-page="${ordersCurrentPage + 1}">
                    <i class="bx bx-chevron-right"></i>
                </a>
            </li>
        `);

        pagination.querySelectorAll('a[data-orders-page]').forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();

                const page = parseInt(this.dataset.ordersPage, 10);

                if (page >= 1 && page <= totalPages && page !== ordersCurrentPage) {
                    ordersCurrentPage = page;
                    ordersApplyFilters();

                    document.querySelector('.card-form')?.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start',
                    });
                }
            });
        });
    }

function ordersRecalcDataTableResponsive() {
    if (typeof jQuery === 'undefined') {
        window.dispatchEvent(new Event('resize'));
        return;
    }

    setTimeout(() => {
        window.dispatchEvent(new Event('resize'));

        if (jQuery.fn.DataTable && jQuery.fn.DataTable.isDataTable('#tabla_index')) {
            const dataTable = jQuery('#tabla_index').DataTable();

            dataTable.columns.adjust();

            if (dataTable.responsive) {
                dataTable.responsive.recalc();
            }
        }
    }, 100);

    setTimeout(() => {
        window.dispatchEvent(new Event('resize'));

        if (jQuery.fn.DataTable && jQuery.fn.DataTable.isDataTable('#tabla_index')) {
            const dataTable = jQuery('#tabla_index').DataTable();

            dataTable.columns.adjust();

            if (dataTable.responsive) {
                dataTable.responsive.recalc();
            }
        }
    }, 350);
}

if (viewButtons.length && boardView && tableView) {
    viewButtons.forEach(button => {
        button.addEventListener('click', function () {
            viewButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            ordersCurrentView = this.dataset.view || 'board';
            ordersCurrentPage = 1;

            if (ordersCurrentView === 'table') {
                boardView.classList.add('d-none');
                tableView.classList.remove('d-none');

                ordersApplyFilters();

                requestAnimationFrame(() => {
                    ordersRecalcDataTableResponsive();
                });
            } else {
                tableView.classList.add('d-none');
                boardView.classList.remove('d-none');

                ordersApplyFilters();
            }
        });
    });
}

    if (filterButtons.length) {
        
        filterButtons.forEach(button => {
            button.addEventListener('click', function () {
                filterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');

                ordersCurrentStatus = this.dataset.status || 'all';
                ordersCurrentPage = 1;

                ordersApplyFilters();
            });
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            ordersCurrentPage = 1;
            ordersApplyFilters();
        });
    }

    if (clearSearch && searchInput) {
        clearSearch.addEventListener('click', function () {
            searchInput.value = '';
            ordersCurrentPage = 1;
            ordersApplyFilters();
            searchInput.focus();
        });
    }

    if (perPageSelect) {
        perPageSelect.addEventListener('change', function () {
            ordersCurrentPage = 1;
            ordersApplyFilters();
        });
    }

    if (document.querySelector('.order-filter-item')) {
        ordersApplyFilters();
    }
});