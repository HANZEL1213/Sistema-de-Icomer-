{{-- resources/views/admin/cupones/usos_cupones/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Usos de Cupones')

@section('content')

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
                    <li class="breadcrumb-item active">Usos de Cupones</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Card --}}
    <div class="card card-form">
        <div class="card-body">

            {{-- Header --}}
            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-3">
                <div>
                    <h4 class="mb-1 text-uppercase fw-bold">Usos de Cupones</h4>
                    <small class="text-muted">Historial de cupones aplicados en pedidos</small>
                </div>
            </div>

            <hr class="my-2" />

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
                        <input type="text" id="searchInput" class="search-input"
                            placeholder="Buscar cupón, pedido, usuario, correo..." autocomplete="off">
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

                <table id="tabla_index" data-order-column="5"
                    class="table table-hover table-bordered align-middle text-center w-100">
                    <thead class="table-light">
                        <tr>
                            <th class="fw-semibold">ID</th>
                            <th class="fw-semibold">Cupón</th>
                            <th class="fw-semibold">Pedido</th>
                            <th class="fw-semibold">Usuario</th>
                            <th class="fw-semibold">Descuento</th>
                            <th class="fw-semibold">Fecha de uso</th>
                            <th class="fw-semibold">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($items as $item)
                            <tr>
                                <td class="text-muted fw-semibold">{{ $item->id_uso_cupon }}</td>

                                <td class="text-start">
                                    <div class="fw-semibold">
                                        {{ $item->cupon?->codigo ?: 'Cupón no disponible' }}
                                    </div>
                                    <small class="text-muted">
                                        ID Cupón: {{ $item->id_cupon }}
                                    </small>
                                </td>

                                <td class="text-start">
                                    <div class="fw-semibold">
                                        {{ $item->pedido?->numero_pedido ?: 'Pedido no disponible' }}
                                    </div>
                                    <small class="text-muted">
                                        ID Pedido: {{ $item->id_pedido }}
                                    </small>
                                </td>

                                <td class="text-start">
                                    @if ($item->usuario)
                                        <div class="fw-semibold">{{ $item->usuario->nombre }}</div>
                                        <small class="text-muted">
                                            {{ $item->usuario->correo ?: 'Sin correo registrado' }}
                                        </small>
                                    @else
                                        <div class="fw-semibold">Cliente invitado</div>
                                        <small class="text-muted">
                                            {{ $item->correo_invitado ?: 'Sin correo registrado' }}
                                        </small>
                                    @endif
                                </td>

                                <td>
                                    <span class="order-badge">
                                        ₡{{ number_format((float) $item->monto_descuento, 2, '.', ',') }}
                                    </span>
                                </td>

                                <td data-order="{{ optional($item->usado_en)->format('Y-m-d H:i:s') }}">
                                    <div class="fw-semibold">
                                        {{ optional($item->usado_en)->format('d/m/Y') }}
                                    </div>
                                    <small class="text-muted">
                                        {{ optional($item->usado_en)->format('H:i') }}
                                    </small>
                                </td>

                                <td>
                                    <div class="d-flex justify-content-center gap-2 flex-wrap">

                                        <a class="btn-action btn-view"
                                            href="{{ route('admin.usos-cupones.show', $item->id_uso_cupon) }}">
                                            <i class="bx bx-show"></i>
                                        </a>

                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>

            {{-- Paginación --}}
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

@endsection
