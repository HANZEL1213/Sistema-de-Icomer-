{{-- resources/views/admin/carrusel/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Carrusel')

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
                    <li class="breadcrumb-item active">Carrusel</li>
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
                    <h4 class="mb-1 text-uppercase fw-bold">Carrusel</h4>
                    <small class="text-muted">Gestión de banners y promociones</small>
                </div>

                <a href="{{ route('admin.carrusel-items.create') }}"
                    class="btn btn-nuevo d-inline-flex align-items-center gap-2">
                    <i class="bx bx-plus-circle"></i>
                    <span>Nuevo</span>
                </a>
            </div>

            <hr class="my-2" />

            {{-- Top bar --}}
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
                            placeholder="Buscar título, destino, estado..." autocomplete="off">
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
                            <th class="fw-semibold">ID</th>
                            <th class="fw-semibold">Banner</th>
                            <th class="fw-semibold">Destino</th>
                            <th class="fw-semibold">Orden actual</th>
                            <th class="fw-semibold">Orden programado</th>
                            <th class="fw-semibold">Inicio</th>
                            <th class="fw-semibold">Fin</th>
                            <th class="fw-semibold">Permiso manual</th>
                            <th class="fw-semibold">Estado calculado</th>
                            <th class="fw-semibold">Estado en carrusel</th>
                            <th class="fw-semibold">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($items as $item)
                            @php
                                /*
                                     |----------------------------------------------------------
                                     | LÓGICA ACTUAL
                                     | activo_manual = decisión del administrador
                                     | activo        = estado efectivo real según fecha + permiso
                                     | habilitado    = visible realmente en carrusel ahora
                                     | orden         = posición real actual
                                     | orden_programado = posición futura deseada
                                     |----------------------------------------------------------
                                     */

                                $enRango = $item->estaVigente();
                                $yaInicio = $item->yaInicio();
                                $estaVencido = $item->estaVencido();

                                // Permiso manual
                                if ($item->activo_manual) {
                                    $textoActivoManual = 'Activo';
                                    $iconoActivoManual = 'bx bx-check-circle me-1';
                                    $claseActivoManual = 'status-badge status-active';
                                } else {
                                    $textoActivoManual = 'Bloqueado';
                                    $iconoActivoManual = 'bx bx-x-circle me-1';
                                    $claseActivoManual = 'status-badge status-inactive';
                                }

                                // Estado calculado (activo real)
                                if ($item->activo) {
                                    $textoActivoCalculado = 'Activo';
                                    $iconoActivoCalculado = 'bx bx-check-circle me-1';
                                    $claseActivoCalculado = 'status-badge status-active';
                                } else {
                                    if (!$yaInicio) {
                                        $textoActivoCalculado = 'Pendiente';
                                        $iconoActivoCalculado = 'bx bx-time-five me-1';
                                        $claseActivoCalculado = 'status-badge status-pending';
                                    } elseif ($estaVencido) {
                                        $textoActivoCalculado = 'Vencido';
                                        $iconoActivoCalculado = 'bx bx-calendar-x me-1';
                                        $claseActivoCalculado = 'status-badge status-inactive';
                                    } elseif ($enRango && !$item->activo_manual) {
                                        $textoActivoCalculado = 'Bloqueado manual';
                                        $iconoActivoCalculado = 'bx bx-power-off me-1';
                                        $claseActivoCalculado = 'status-badge status-inactive';
                                    } else {
                                        $textoActivoCalculado = 'Inactivo';
                                        $iconoActivoCalculado = 'bx bx-x-circle me-1';
                                        $claseActivoCalculado = 'status-badge status-inactive';
                                    }
                                }

                                // Estado del carrusel
                                if ($item->activo && $item->orden > 0) {
                                    $textoCarrusel = 'Activo';
                                    $iconoCarrusel = 'bx bx-check-circle me-1';
                                    $claseCarrusel = 'status-badge status-active';
                                } elseif (!$yaInicio) {
                                    $textoCarrusel = 'Programado';
                                    $iconoCarrusel = 'bx bx-calendar me-1';
                                    $claseCarrusel = 'status-badge status-pending';
                                } elseif ($estaVencido) {
                                    $textoCarrusel = 'Fuera de rango';
                                    $iconoCarrusel = 'bx bx-calendar-x me-1';
                                    $claseCarrusel = 'status-badge status-inactive';
                                } elseif ($enRango && !$item->activo_manual) {
                                    $textoCarrusel = 'Bloqueado manual';
                                    $iconoCarrusel = 'bx bx-power-off me-1';
                                    $claseCarrusel = 'status-badge status-inactive';
                                } else {
                                    $textoCarrusel = 'Fuera del carrusel';
                                    $iconoCarrusel = 'bx bx-x-circle me-1';
                                    $claseCarrusel = 'status-badge status-inactive';
                                }

                                $ordenActual = $item->activo && $item->orden > 0 ? $item->orden : 0;
                            @endphp

                            <tr>
                                <td class="text-muted fw-semibold">{{ $item->id_carrusel_item }}</td>

                                {{-- BANNER --}}
                                <td class="text-start">
                                    <div class="d-flex align-items-center gap-3">

                                        <div class="flex-shrink-0">
                                            @if ($item->ruta_imagen)
                                                <img src="{{ \Illuminate\Support\Str::startsWith($item->ruta_imagen, ['http://', 'https://'])
                                                    ? $item->ruta_imagen
                                                    : asset('storage/' . $item->ruta_imagen) }}"
                                                    class="img-thumb" loading="lazy"
                                                    alt="{{ $item->titulo ?: 'Banner ' . $item->id_carrusel_item }}">
                                            @else
                                                <div
                                                    class="img-thumb d-flex align-items-center justify-content-center bg-light text-muted small border">
                                                    <i class="bx bx-image"></i>
                                                </div>
                                            @endif
                                        </div>

                                        <div>
                                            <div class="fw-semibold">
                                                {{ $item->titulo ?: 'Sin título' }}
                                            </div>
                                            <small class="text-muted">
                                                {{ $item->subtitulo ?: 'Sin subtítulo' }}
                                            </small>
                                        </div>

                                    </div>
                                </td>

                                {{-- DESTINO --}}
                                <td>
                                    @if ($item->tipo_destino === 'url')
                                        @if ($item->url_destino)
                                            <a href="{{ $item->url_destino }}" target="_blank" class="link-pill">
                                                {{ $item->url_destino }}
                                            </a>
                                        @else
                                            <span class="text-muted">URL no definida</span>
                                        @endif
                                    @elseif ($item->tipo_destino === 'producto')
                                        <span class="order-badge">
                                            {{ $item->producto?->nombre ?? 'Producto no disponible' }}
                                        </span>
                                    @elseif ($item->tipo_destino === 'categoria')
                                        <span class="order-badge">
                                            {{ $item->categoria?->nombre ?? 'Categoría no disponible' }}
                                        </span>
                                    @else
                                        <span class="text-muted">Sin destino</span>
                                    @endif
                                </td>

                                {{-- ORDEN ACTUAL --}}
                                <td>
                                    <span class="order-badge">
                                        {{ $ordenActual }}
                                    </span>
                                </td>

                                {{-- ORDEN PROGRAMADO --}}
                                <td>
                                    <span class="order-badge">
                                        {{ $item->orden_programado }}
                                    </span>
                                </td>

                                {{-- INICIO --}}
                                <td>
                                    <div class="fw-semibold">
                                        {{ $item->inicia_en->format('d/m/Y') }}
                                    </div>
                                    <small class="text-muted">
                                        {{ $item->inicia_en->format('H:i') }}
                                    </small>
                                </td>

                                {{-- FIN --}}
                                <td>
                                    <div class="fw-semibold">
                                        {{ $item->termina_en->format('d/m/Y') }}
                                    </div>
                                    <small class="text-muted">
                                        {{ $item->termina_en->format('H:i') }}
                                    </small>
                                </td>

                                {{-- PERMISO MANUAL --}}
                                <td>
                                    <span class="{{ $claseActivoManual }}">
                                        <i class="{{ $iconoActivoManual }}"></i>{{ $textoActivoManual }}
                                    </span>
                                </td>

                                {{-- ESTADO CALCULADO --}}
                                <td>
                                    <span class="{{ $claseActivoCalculado }}">
                                        <i class="{{ $iconoActivoCalculado }}"></i>{{ $textoActivoCalculado }}
                                    </span>
                                </td>

                                {{-- ESTADO EN CARRUSEL --}}
                                <td>
                                    <span class="{{ $claseCarrusel }}">
                                        <i class="{{ $iconoCarrusel }}"></i>{{ $textoCarrusel }}
                                    </span>
                                </td>

                                {{-- ACCIONES --}}
                                <td>
                                    <div class="d-flex justify-content-center gap-2 flex-wrap">

                                        <a class="btn-action btn-view"
                                            href="{{ route('admin.carrusel-items.show', $item->id_carrusel_item) }}">
                                            <i class="bx bx-show"></i>
                                        </a>

                                        <a class="btn-action btn-edit"
                                            href="{{ route('admin.carrusel-items.edit', $item->id_carrusel_item) }}">
                                            <i class="bx bx-edit"></i>
                                        </a>

                                        <form action="{{ route('admin.carrusel-items.destroy', $item->id_carrusel_item) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')

                                            <button type="button" class="btn-action btn-delete btn-delete-modal"
                                                data-clave="Banner"
                                                data-valor="{{ $item->titulo ?: 'Banner #' . $item->id_carrusel_item }}">
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
                        <nav>
                            <ul class="pagination pagination-modern mb-0">
                                <li class="page-item disabled" id="pagination-prev">
                                    <a class="page-link" href="#"><i class="bx bx-chevron-left"></i></a>
                                </li>
                                {{-- JS generará los números --}}
                                <li class="page-item disabled" id="pagination-next">
                                    <a class="page-link" href="#"><i class="bx bx-chevron-right"></i></a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>

        </div>
    </div>



@endsection
