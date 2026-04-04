@extends('admin.layouts.app')

@section('title', 'Usos de Cupones')

@section('content')
@php
    /**
     * ✅ DATOS FAKE PARA PRUEBAS (alineado con tu DB):
     * usos_cupones:
     * - id_uso_cupon
     * - id_cupon
     * - id_pedido
     * - id_usuario
     * - correo_invitado
     * - monto_descuento
     * - usado_en
     *
     * Relaciones esperadas:
     * - cupon
     * - pedido
     * - usuario
     */

    $usosCupones = [];

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

    $clientes = [
        'María López',
        'Carlos Ramírez',
        'Fernanda Solano',
        'Luis Herrera',
        'Ana Gómez',
        null,
        null
    ];

    for ($i = 1; $i <= 75; $i++) {
        $cliente = $clientes[array_rand($clientes)];
        $esInvitado = is_null($cliente);

        $usosCupones[] = (object)[
            'id_uso_cupon'     => $i,
            'id_cupon'         => rand(1, 20),
            'codigo_cupon'     => $codigos[array_rand($codigos)],
            'id_pedido'        => rand(100, 999),
            'numero_pedido'    => 'PED-' . str_pad($i, 6, '0', STR_PAD_LEFT),
            'id_usuario'       => $esInvitado ? null : rand(1, 30),
            'usuario_nombre'   => $cliente,
            'correo_invitado'  => $esInvitado ? 'invitado' . $i . '@correo.com' : null,
            'monto_descuento'  => rand(2, 25) * 1000,
            'usado_fecha'      => now()->subDays(rand(0, 30))->format('Y-m-d'),
            'usado_hora'       => now()->subMinutes(rand(0, 1440))->format('H:i'),
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
                    <li class="breadcrumb-item active">Usos de Cupones</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Card --}}
    <div class="card card-index">
        <div class="card-body">

            {{-- Header --}}
            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-3">
                <div>
                    <h4 class="mb-1 text-uppercase fw-bold">Usos de Cupones</h4>
                    <small class="text-muted">Historial de cupones aplicados en pedidos</small>
                </div>
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
                        <input
                            type="text"
                            id="searchInput"
                            class="search-input"
                            placeholder="Buscar cupón, pedido, usuario, correo..."
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

            {{-- Tabla --}}
            <div class="table-responsive">
                <table id="tabla_index" class="table table-hover table-bordered align-middle text-center w-100">
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
                        @foreach($usosCupones as $uso)
                        <tr>
                            <td class="text-muted fw-semibold">{{ $uso->id_uso_cupon }}</td>

                            <td class="text-start">
                                <div class="fw-semibold">{{ $uso->codigo_cupon }}</div>
                                <small class="text-muted">ID Cupón: {{ $uso->id_cupon }}</small>
                            </td>

                            <td class="text-start">
                                <div class="fw-semibold">{{ $uso->numero_pedido }}</div>
                                <small class="text-muted">ID Pedido: {{ $uso->id_pedido }}</small>
                            </td>

                            <td class="text-start">
                                @if($uso->usuario_nombre)
                                    <div class="fw-semibold">{{ $uso->usuario_nombre }}</div>
                                    <small class="text-muted">ID Usuario: {{ $uso->id_usuario }}</small>
                                @else
                                    <div class="fw-semibold">Cliente invitado</div>
                                    <small class="text-muted">{{ $uso->correo_invitado }}</small>
                                @endif
                            </td>

                            <td>
                                <span class="order-badge">
                                    ₡{{ number_format($uso->monto_descuento, 0, '.', ',') }}
                                </span>
                            </td>

                            <td>
                                <div class="fw-semibold">{{ $uso->usado_fecha }}</div>
                                <small class="text-muted">{{ $uso->usado_hora }}</small>
                            </td>

                            <td>
                                <div class="d-flex justify-content-center gap-2 flex-wrap">
                                    <a class="btn-action btn-view" title="Ver"
                                       href="{{ route('admin.usos-cupones.show', $uso->id_uso_cupon) }}">
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
</div>
@endsection