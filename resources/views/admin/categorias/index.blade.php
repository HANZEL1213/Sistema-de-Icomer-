{{-- resources/views/admin/categorias/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Categorías')

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
                    <li class="breadcrumb-item active">Categorías</li>
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
                    <h4 class="mb-1 text-uppercase fw-bold">Categorías</h4>
                    <small class="text-muted">Gestión de categorías de productos</small>
                </div>

                <a href="{{ route('admin.categorias.create') }}"
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
                            placeholder="Buscar nombre, slug, descripción, estado..." autocomplete="off">
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
                            <th class="fw-semibold">Categoría</th>
                            <th class="fw-semibold">Slug</th>
                            <th class="fw-semibold">Descripción</th>
                            <th class="fw-semibold">Estado</th>
                            <th class="fw-semibold">Registro</th>
                            <th class="fw-semibold">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($items as $item)
                            <tr>
                                <td class="text-muted fw-semibold">{{ $item->id_categoria }}</td>

                                <td class="text-start">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="flex-shrink-0">
                                            @if ($item->imagen)
                                                <img src="{{ \Illuminate\Support\Str::startsWith($item->imagen, ['http://', 'https://'])
                                                    ? $item->imagen
                                                    : asset('storage/' . $item->imagen) }}"
                                                    class="img-thumb" loading="lazy" alt="{{ $item->nombre }}">
                                            @else
                                                <div
                                                    class="img-thumb d-flex align-items-center justify-content-center bg-light text-muted small border">
                                                    <i class="bx bx-image"></i>
                                                </div>
                                            @endif
                                        </div>

                                        <div>
                                            <div class="fw-semibold">{{ $item->nombre }}</div>
                                            <small class="text-muted">ID: {{ $item->id_categoria }}</small>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <span class="order-badge">
                                        {{ $item->slug }}
                                    </span>
                                </td>

                                <td class="text-start text-muted">
                                    @if ($item->descripcion)
                                        {{ \Illuminate\Support\Str::limit($item->descripcion, 80, '...') }}
                                    @else
                                        <span class="text-muted">Sin descripción</span>
                                    @endif
                                </td>

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

                                <td>
                                    <div class="fw-semibold">
                                        {{ optional($item->created_at)->format('Y-m-d') }}
                                    </div>
                                    <small class="text-muted">
                                        {{ optional($item->created_at)->format('H:i') }}
                                    </small>
                                </td>

                                <td>
                                    <div class="d-flex justify-content-center gap-2 flex-wrap">

                                        <a class="btn-action btn-view"
                                            href="{{ route('admin.categorias.show', $item->id_categoria) }}">
                                            <i class="bx bx-show"></i>
                                        </a>

                                        <a class="btn-action btn-edit"
                                            href="{{ route('admin.categorias.edit', $item->id_categoria) }}">
                                            <i class="bx bx-edit"></i>
                                        </a>

                                        <form action="{{ route('admin.categorias.destroy', $item->id_categoria) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')

                                            <button type="button" class="btn-action btn-delete btn-delete-modal"
                                                data-clave="Categoría" data-valor="{{ $item->nombre }}">
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
