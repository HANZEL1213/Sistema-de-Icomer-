{{-- resources/views/admin/zonas_envio/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Zonas de Envío')

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
                    <li class="breadcrumb-item active">Zonas de Envío</li>
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
                    <h4 class="mb-1 text-uppercase fw-bold">Zonas de Envío</h4>
                    <small class="text-muted">Gestión de cobros por ubicación de entrega</small>
                </div>

                <a href="{{ route('admin.zonas-envio.create') }}"
                    class="btn btn-nuevo d-inline-flex align-items-center gap-2">
                    <i class="bx bx-plus-circle"></i>
                    <span>Nueva</span>
                </a>
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
                            placeholder="Buscar provincia, cantón, distrito, estado..." autocomplete="off">
                        <div class="search-actions">
                            <button class="btn-search-clear" id="clearSearch" type="button">
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
                            <th>ID</th>
                            <th>Provincia</th>
                            <th>Cantón</th>
                            <th>Distrito</th>
                            <th>Costo</th>
                            <th>Estado</th>
                            <th>Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($items as $item)
                            <tr>
                                <td class="text-muted fw-semibold">
                                    {{ $item->id_zona_envio }}
                                </td>

                                {{-- Provincia --}}
                                <td class="text-start">
                                    <div class="fw-semibold">
                                        {{ optional($item->provincia)->nombre ?? '—' }}
                                    </div>
                                    <small class="text-muted">
                                        Código: {{ optional($item->provincia)->codigo ?? '—' }}
                                    </small>
                                </td>

                                {{-- Cantón --}}
                                <td class="text-start">
                                    <div class="fw-semibold">
                                        {{ optional($item->canton)->nombre ?? '—' }}
                                    </div>
                                    <small class="text-muted">
                                        Código: {{ optional($item->canton)->codigo ?? '—' }}
                                    </small>
                                </td>

                                {{-- Distrito --}}
                                <td class="text-start">
                                    <div class="fw-semibold">
                                        {{ optional($item->distrito)->nombre ?? '—' }}
                                    </div>
                                    <small class="text-muted">
                                        Código: {{ optional($item->distrito)->codigo ?? '—' }}
                                    </small>
                                </td>

                                {{-- Costo --}}
                                <td>
                                    <span class="order-badge">
                                        ₡{{ number_format($item->costo, 2, '.', ',') }}
                                    </span>
                                </td>

                                {{-- Estado --}}
                                <td>
                                    @if ($item->activo)
                                        <span class="status-badge status-active">
                                            <i class="bx bx-check-circle me-1"></i>Activo
                                        </span>
                                    @else
                                        <span class="status-badge status-inactive">
                                            <i class="bx bx-x-circle me-1"></i>Inactivo
                                        </span>
                                    @endif
                                </td>

                                {{-- Registro --}}
                                <td>
                                    <div class="fw-semibold">
                                        {{ optional($item->created_at)->format('Y-m-d') }}
                                    </div>
                                    <small class="text-muted">
                                        {{ optional($item->created_at)->format('H:i') }}
                                    </small>
                                </td>

                                {{-- Acciones --}}
                                <td>
                                    <div class="d-flex justify-content-center gap-2 flex-wrap">

                                        <a class="btn-action btn-view"
                                            href="{{ route('admin.zonas-envio.show', $item->id_zona_envio) }}">
                                            <i class="bx bx-show"></i>
                                        </a>

                                        <a class="btn-action btn-edit"
                                            href="{{ route('admin.zonas-envio.edit', $item->id_zona_envio) }}">
                                            <i class="bx bx-edit"></i>
                                        </a>

                                        <form action="{{ route('admin.zonas-envio.destroy', $item->id_zona_envio) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')

                                            <button type="button" class="btn-action btn-delete btn-delete-modal"
                                                data-clave="Zona de envío"
                                                data-valor="{{ (optional($item->provincia)->nombre ?? '—') .
                                                    ' / ' .
                                                    (optional($item->canton)->nombre ?? '—') .
                                                    ' / ' .
                                                    (optional($item->distrito)->nombre ?? '—') }}">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </form>

                                    </div>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
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
                    <ul class="pagination pagination-modern mb-0">
                        <li class="page-item disabled" id="pagination-prev">
                            <a class="page-link" href="#"><i class="bx bx-chevron-left"></i></a>
                        </li>

                        <li class="page-item disabled" id="pagination-next">
                            <a class="page-link" href="#"><i class="bx bx-chevron-right"></i></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
    

@endsection
