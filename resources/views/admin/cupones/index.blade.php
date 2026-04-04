@extends('admin.layouts.app')

@section('title', 'Cupones')

@section('content')
@php
    $cupones = [];

    $codigos = [
        'BIENVENIDA10',
        'ENVIOGRATIS',
        'BLACK20',
        'PROMO15',
        'NAVIDAD25',
        'CLIENTEVIP',
        'FLASH10',
        'SUPERDESC'
    ];

    for ($i = 1; $i <= 60; $i++) {
        $tipo = $i % 2 === 0 ? 'porcentaje' : 'monto_fijo';

        $cupones[] = (object)[
            'id_cupon' => $i,
            'codigo' => $codigos[($i - 1) % count($codigos)] . $i,
            'tipo' => $tipo,
            'valor' => $tipo === 'porcentaje' ? rand(5, 30) : rand(2000, 15000),
            'minimo_subtotal' => rand(0, 30000),
            'inicia_en' => now()->subDays(rand(0, 10))->format('Y-m-d H:i'),
            'termina_en' => now()->addDays(rand(5, 30))->format('Y-m-d H:i'),
            'max_usos_total' => rand(20, 300),
            'max_usos_por_usuario' => rand(1, 5),
            'activo' => $i % 5 !== 0,
        ];
    }
@endphp

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

    <div class="card card-index">
        <div class="card-body">

            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-3">
                <div>
                    <h4 class="mb-1 text-uppercase fw-bold">Cupones</h4>
                    <small class="text-muted">Gestión de descuentos y promociones</small>
                </div>

                <a href="{{ route('admin.cupones.create') }}" class="btn btn-nuevo d-inline-flex align-items-center gap-2">
                    <i class="bx bx-plus-circle"></i>
                    <span>Nuevo</span>
                </a>
            </div>

            <hr class="my-2"/>

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
                        <input
                            type="text"
                            id="searchInput"
                            class="search-input"
                            placeholder="Buscar código, tipo, valor, estado..."
                            autocomplete="off"
                        >
                        <div class="search-actions">
                            <button class="btn-search-clear" id="clearSearch" type="button" title="Limpiar">
                                <i class="bx bx-x"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table id="tabla_index" class="table table-hover table-bordered align-middle text-center w-100">
                    <thead class="table-light">
                        <tr>
                            <th class="fw-semibold">ID</th>
                            <th class="fw-semibold">Código</th>
                            <th class="fw-semibold">Tipo</th>
                            <th class="fw-semibold">Valor</th>
                            <th class="fw-semibold">Mínimo</th>
                            <th class="fw-semibold">Vigencia</th>
                            <th class="fw-semibold">Estado</th>
                            <th class="fw-semibold">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($cupones as $cupon)
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
                                @if($cupon->tipo === 'porcentaje')
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
                                @if($cupon->tipo === 'porcentaje')
                                    {{ number_format($cupon->valor, 0) }}%
                                @else
                                    ₡{{ number_format($cupon->valor, 0, '.', ',') }}
                                @endif
                            </td>

                            <td class="fw-semibold">
                                ₡{{ number_format($cupon->minimo_subtotal, 0, '.', ',') }}
                            </td>

                            <td>
                                <div class="fw-semibold">{{ \Carbon\Carbon::parse($cupon->inicia_en)->format('Y-m-d') }}</div>
                                <small class="text-muted">
                                    hasta {{ \Carbon\Carbon::parse($cupon->termina_en)->format('Y-m-d') }}
                                </small>
                            </td>

                            <td>
                                @if($cupon->activo)
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
                                <div class="d-flex justify-content-center gap-2 flex-wrap">
                                    <a class="btn-action btn-view" title="Ver"
                                       href="{{ route('admin.cupones.show', $cupon->id_cupon) }}">
                                        <i class="bx bx-show"></i>
                                    </a>

                                    <a class="btn-action btn-edit" title="Editar"
                                       href="{{ route('admin.cupones.edit', $cupon->id_cupon) }}">
                                        <i class="bx bx-edit"></i>
                                    </a>

                                    <form action="{{ route('admin.cupones.destroy', $cupon->id_cupon) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn-action btn-delete" type="submit" title="Eliminar">
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