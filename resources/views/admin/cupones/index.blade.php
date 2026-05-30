{{-- resources/views/admin/cupones/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Cupones')

@section('content')

    <div class="page-content">

        <div class="page-breadcrumb d-sm-flex align-items-center mb-3">
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="bx bx-home-alt"></i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">Cupones</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="card card-form">
            <div class="card-body">

                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-3">
                    <div>
                        <h4 class="mb-1 text-uppercase fw-bold">Cupones</h4>
                        <small class="text-muted">Gestión de descuentos y promociones</small>
                    </div>

                    <a href="{{ route('admin.cupones.create') }}"
                        class="btn btn-nuevo d-inline-flex align-items-center gap-2">
                        <i class="bx bx-plus-circle"></i>
                        <span>Nuevo</span>
                    </a>
                </div>

                <hr class="my-2" />

                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">

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

                    <div class="search-container ms-auto">
                        <div class="search-box" role="search">
                            <i class="bx bx-search search-icon"></i>
                            <input type="text" id="searchInput" class="search-input"
                                placeholder="Buscar código, tipo, valor, estado..." autocomplete="off">
                            <div class="search-actions">
                                <button class="btn-search-clear" id="clearSearch" type="button" title="Limpiar">
                                    <i class="bx bx-x"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="tabla_index" data-order-column="8"
                        class="table table-hover table-bordered align-middle text-center w-100">

                        <thead class="table-light">
                            <tr>
                                <th class="fw-semibold">ID</th>
                                <th class="fw-semibold">Código</th>
                                <th class="fw-semibold">Tipo</th>
                                <th class="fw-semibold">Valor</th>
                                <th class="fw-semibold">Mínimo</th>
                                <th class="fw-semibold">Inicio</th>
                                <th class="fw-semibold">Fin</th>
                                <th class="fw-semibold">Estado</th>
                                <th class="fw-semibold">Registro</th>
                                <th class="fw-semibold">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($items as $cupon)
                                <tr>
                                    <td class="text-muted fw-semibold">{{ $cupon->id_cupon }}</td>

                                    <td class="text-start">
                                        <div class="fw-semibold">{{ $cupon->codigo }}</div>
                                        <small class="text-muted">
                                            Total usos: {{ $cupon->max_usos_total ?? 'Ilimitado' }} |
                                            Por usuario: {{ $cupon->max_usos_por_usuario ?? 'Ilimitado' }}
                                        </small>
                                    </td>

                                    <td>
                                        @if ($cupon->tipo === 'porcentaje')
                                            <span class="status-badge status-active">
                                                <i class="bx bx-pie-chart-alt-2 me-1"></i>Porcentaje
                                            </span>
                                        @else
                                            <span class="status-badge status-inactive">
                                                <i class="bx bx-money me-1"></i>Monto fijo
                                            </span>
                                        @endif
                                    </td>

                                    <td class="fw-semibold">
                                        @if ($cupon->tipo === 'porcentaje')
                                            {{ number_format((float) $cupon->valor, 2, '.', ',') }}%
                                        @else
                                            ₡{{ number_format((float) $cupon->valor, 2, '.', ',') }}
                                        @endif
                                    </td>

                                    <td class="fw-semibold">
                                        ₡{{ number_format((float) $cupon->minimo_subtotal, 2, '.', ',') }}
                                    </td>

                                    <td>
                                        @if ($cupon->inicia_en)
                                            <div class="fw-semibold">
                                                {{ $cupon->inicia_en->format('d/m/Y') }}
                                            </div>
                                            <small class="text-muted">
                                                {{ $cupon->inicia_en->format('H:i') }}
                                            </small>
                                        @else
                                            <span class="text-muted">Sin inicio</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if ($cupon->termina_en)
                                            <div class="fw-semibold">
                                                {{ $cupon->termina_en->format('d/m/Y') }}
                                            </div>
                                            <small class="text-muted">
                                                {{ $cupon->termina_en->format('H:i') }}
                                            </small>
                                        @else
                                            <span class="text-muted">Sin fin</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if ($cupon->activo)
                                            <span class="status-badge status-active">
                                                <i class="bx bx-check-circle me-1"></i>Activo
                                            </span>
                                        @else
                                            <span class="status-badge status-inactive">
                                                <i class="bx bx-x-circle me-1"></i>Inactivo
                                            </span>
                                        @endif
                                    </td>

                                    <td data-order="{{ optional($cupon->created_at)->format('Y-m-d H:i:s') }}">
                                        <div class="fw-semibold">
                                            {{ optional($cupon->created_at)->format('d/m/Y') }}
                                        </div>
                                        <small class="text-muted">
                                            {{ optional($cupon->created_at)->format('H:i') }}
                                        </small>
                                    </td>

                                    <td>
                                        <div class="d-flex justify-content-center gap-2 flex-wrap">

                                            <a class="btn-action btn-view"
                                                href="{{ route('admin.cupones.show', $cupon->id_cupon) }}">
                                                <i class="bx bx-show"></i>
                                            </a>

                                            <a class="btn-action btn-edit"
                                                href="{{ route('admin.cupones.edit', $cupon->id_cupon) }}">
                                                <i class="bx bx-edit"></i>
                                            </a>

                                            <form action="{{ route('admin.cupones.destroy', $cupon->id_cupon) }}"
                                                method="POST">
                                                @csrf
                                                @method('DELETE')

                                                <button type="button" class="btn-action btn-delete btn-delete-modal"
                                                    data-clave="Cupón" data-valor="{{ $cupon->codigo }}">
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
        </div>
    </div>
@endsection
