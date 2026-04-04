{{-- resources/views/admin/ventas/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Reporte de ventas')

@section('content')
@php
    /**
     * Tabla ventas:
     * - id_venta, canal(online|local), id_pedido, id_venta_local, created_at
     * (Para reporte unificamos: canal + referencia + total + fecha)
     */

    $ventas = [];

    $canales = ['online', 'local'];

    for ($i = 1; $i <= 120; $i++) {
        $canal = $canales[$i % 2];

        $fecha = now()->subDays(rand(0, 45))->setTime(rand(8, 20), rand(0, 59));
        $subtotal = rand(8, 120) * 1000;
        $descuento = (rand(0, 3) === 0) ? rand(1, 20) * 500 : 0;
        $total = max(0, $subtotal - $descuento);

        $ventas[] = (object)[
            'id_venta' => $i,
            'canal' => $canal,
            'referencia' => $canal === 'online'
                ? 'PED-' . str_pad($i, 6, '0', STR_PAD_LEFT)
                : 'TCK-' . str_pad($i, 6, '0', STR_PAD_LEFT),
            'subtotal' => (float)$subtotal,
            'descuento' => (float)$descuento,
            'total' => (float)$total,
            'fecha' => $fecha->format('Y-m-d'),
            'hora' => $fecha->format('H:i'),
        ];
    }
@endphp

<div class="page-content">

    {{-- Breadcrumb --}}
    <div class="page-breadcrumb d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}">
                            <i class="bx bx-home-alt"></i>
                        </a>
                    </li>
                    <li class="breadcrumb-item active">Reporte de ventas</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card card-index">
        <div class="card-body">

            {{-- Header --}}
            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-3">
                <div>
                    <h4 class="mb-1 text-uppercase fw-bold">Reporte de ventas</h4>
                    <small class="text-muted">Consolidado (online + físicas)</small>
                </div>

                {{-- (Opcional) Botón exportar --}}
                <button class="btn btn-nuevo d-inline-flex align-items-center gap-2" type="button">
                    <i class="bx bx-download"></i>
                    <span>Exportar</span>
                </button>
            </div>

            <hr class="my-2"/>

            {{-- Top bar --}}
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">

                {{-- Por página --}}
                <div class="pagination-perpage-top">
                    <div class="input-group input-group-perpage">
                        <span class="input-group-text bg-transparent border-end-0">
                            <i class="bx bx-list-ul"></i>
                        </span>
                        <select class="form-select form-select-sm border-start-0" id="perPageSelect">
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                        <span class="input-group-text bg-transparent border-start-0">
                            <span class="perpage-label">por página</span>
                        </span>
                    </div>
                </div>

                {{-- Buscador --}}
                <div class="search-container ms-auto">
                    <div class="search-box" role="search">
                        <i class="bx bx-search search-icon"></i>
                        <input type="text"
                               id="searchInput"
                               class="search-input"
                               placeholder="Buscar ID, canal, referencia..."
                               autocomplete="off">
                        <div class="search-actions">
                            <button class="btn-search-clear" id="clearSearch" type="button" title="Limpiar">
                                <i class="bx bx-x"></i>
                            </button>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Tabla --}}
            <div class="table-responsive">
                <table id="tabla_index" class="table table-hover table-bordered align-middle text-center w-100">
                    <thead class="table-light">
                        <tr>
                            <th class="fw-semibold">ID</th>
                            <th class="fw-semibold">Canal</th>
                            <th class="fw-semibold">Referencia</th>
                            <th class="fw-semibold">Subtotal</th>
                            <th class="fw-semibold">Descuento</th>
                            <th class="fw-semibold">Total</th>
                            <th class="fw-semibold">Fecha</th>
                            <th class="fw-semibold">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($ventas as $v)
                        <tr>
                            <td class="text-muted fw-semibold">{{ $v->id_venta }}</td>

                            <td class="fw-semibold">
                                @if($v->canal === 'online')
                                    <span class="status-badge status-active">
                                        <i class="bx bx-globe me-1"></i>Online
                                    </span>
                                @else
                                    <span class="status-badge status-inactive">
                                        <i class="bx bx-store me-1"></i>Física
                                    </span>
                                @endif
                            </td>

                            <td class="fw-semibold">{{ $v->referencia }}</td>

                            <td class="fw-semibold">₡{{ number_format($v->subtotal, 0, '.', ',') }}</td>
                            <td class="fw-semibold">₡{{ number_format($v->descuento, 0, '.', ',') }}</td>
                            <td class="fw-semibold">₡{{ number_format($v->total, 0, '.', ',') }}</td>

                            <td>
                                <div class="fw-semibold">{{ $v->fecha }}</div>
                                <small class="text-muted">{{ $v->hora }}</small>
                            </td>

                            <td>
                                <div class="d-flex justify-content-center gap-2 flex-wrap">
                                    @if($v->canal === 'online')
                                        <a class="btn-action btn-view" title="Ver pedido"
                                           href="{{ route('admin.pedidos.show', 1) }}">
                                            <i class="bx bx-show"></i>
                                        </a>
                                    @else
                                        <a class="btn-action btn-view" title="Ver venta física"
                                           href="{{ route('admin.ventas-locales.show', 1) }}">
                                            <i class="bx bx-show"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>

            {{-- Paginación custom --}}
            <div class="custom-pagination-container mt-3">
                <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3 py-2">
                    <div class="pagination-info">
                        <span class="text-muted">Mostrando</span>
                        <span class="fw-semibold text-dark ms-1" id="pagination-from">0</span>
                        <span class="text-muted">a</span>
                        <span class="fw-semibold text-dark mx-1" id="pagination-to">0</span>
                        <span class="text-muted">de</span>
                        <span class="fw-semibold text-dark ms-1" id="pagination-total">0</span>
                        <span class="text-muted">resultados</span>
                    </div>

                    <div class="pagination-controls">
                        <nav aria-label="Page navigation">
                            <ul class="pagination pagination-modern mb-0">
                                <li class="page-item disabled" id="pagination-prev">
                                    <a class="page-link" href="#" aria-label="Previous">
                                        <i class="bx bx-chevron-left"></i>
                                    </a>
                                </li>

                                {{-- números generados por JS --}}

                                <li class="page-item disabled" id="pagination-next">
                                    <a class="page-link" href="#" aria-label="Next">
                                        <i class="bx bx-chevron-right"></i>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
(function () {
    const table = document.getElementById('tabla_index');
    if (!table) return;

    const perPageSelect = document.getElementById('perPageSelect');
    const searchInput = document.getElementById('searchInput');
    const clearSearch = document.getElementById('clearSearch');

    const fromEl = document.getElementById('pagination-from');
    const toEl = document.getElementById('pagination-to');
    const totalEl = document.getElementById('pagination-total');

    const prevLi = document.getElementById('pagination-prev');
    const nextLi = document.getElementById('pagination-next');

    const tbody = table.querySelector('tbody');
    const allRows = Array.from(tbody.querySelectorAll('tr'));

    let currentPage = 1;

    function getPerPage() {
        return parseInt(perPageSelect.value || '10', 10);
    }

    function normalize(txt) {
        return (txt || '').toString().toLowerCase().trim();
    }

    function getFilteredRows() {
        const q = normalize(searchInput.value);
        if (!q) return allRows;
        return allRows.filter(row => normalize(row.innerText).includes(q));
    }

    function clearPaginationNumbers() {
        const ul = prevLi.parentElement;
        const dynamic = Array.from(ul.querySelectorAll('li.page-item.dynamic-page'));
        dynamic.forEach(li => li.remove());
    }

    function renderPaginationNumbers(totalPages) {
        const ul = prevLi.parentElement;
        clearPaginationNumbers();

        const makePageLi = (page) => {
            const li = document.createElement('li');
            li.className = 'page-item dynamic-page' + (page === currentPage ? ' active' : '');
            const a = document.createElement('a');
            a.className = 'page-link';
            a.href = '#';
            a.textContent = page;
            a.addEventListener('click', (e) => {
                e.preventDefault();
                currentPage = page;
                render();
            });
            li.appendChild(a);
            return li;
        };

        // Ventana de páginas (máx 5)
        const windowSize = 5;
        let start = Math.max(1, currentPage - Math.floor(windowSize / 2));
        let end = Math.min(totalPages, start + windowSize - 1);
        start = Math.max(1, end - windowSize + 1);

        for (let p = start; p <= end; p++) {
            ul.insertBefore(makePageLi(p), nextLi);
        }
    }

    function updatePrevNext(totalPages) {
        prevLi.classList.toggle('disabled', currentPage <= 1);
        nextLi.classList.toggle('disabled', currentPage >= totalPages);

        prevLi.querySelector('a').onclick = (e) => {
            e.preventDefault();
            if (currentPage <= 1) return;
            currentPage--;
            render();
        };

        nextLi.querySelector('a').onclick = (e) => {
            e.preventDefault();
            if (currentPage >= totalPages) return;
            currentPage++;
            render();
        };
    }

    function render() {
        const perPage = getPerPage();
        const filtered = getFilteredRows();
        const total = filtered.length;

        // ocultar todo
        allRows.forEach(r => r.style.display = 'none');

        const totalPages = Math.max(1, Math.ceil(total / perPage));
        if (currentPage > totalPages) currentPage = totalPages;

        const startIdx = (currentPage - 1) * perPage;
        const endIdx = Math.min(startIdx + perPage, total);

        filtered.slice(startIdx, endIdx).forEach(r => r.style.display = '');

        fromEl.textContent = total === 0 ? 0 : (startIdx + 1);
        toEl.textContent = endIdx;
        totalEl.textContent = total;

        renderPaginationNumbers(totalPages);
        updatePrevNext(totalPages);
    }

    perPageSelect.addEventListener('change', () => {
        currentPage = 1;
        render();
    });

    searchInput.addEventListener('input', () => {
        currentPage = 1;
        render();
    });

    clearSearch.addEventListener('click', () => {
        searchInput.value = '';
        currentPage = 1;
        render();
        searchInput.focus();
    });

    render();
})();
</script>
@endpush
