{{-- resources/views/admin/zonas_envio/show.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Detalle Zona de Envío')

@section('content')

    {{-- Breadcrumb --}}
    <div class="page-breadcrumb d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav>
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}">
                            <i class="bx bx-home-alt"></i>
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.zonas-envio.index') }}">Zonas de envío</a>
                    </li>
                    <li class="breadcrumb-item active">Detalle</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card card-form">
        <div class="card-body">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="fw-bold text-uppercase mb-1">Detalle de Zona de Envío</h4>
                    <small class="text-muted">Información de la zona configurada</small>
                </div>

                <a href="{{ route('admin.zonas-envio.index') }}" class="btn btn-secondary-custom btn-back">
                    <i class="bx bx-arrow-back"></i>
                    <span class="btn-text">Volver</span>
                </a>
            </div>

            <hr>

            <div class="row g-4">

                {{-- IZQUIERDA --}}
                <div class="col-md-6">

                    {{-- UBICACIÓN --}}
                    <div class="card border-0 bg-light mb-3">
                        <div class="highlight-card text-center">
                            <div class="highlight-icon">
                                <i class="bx bx-map"></i>
                            </div>
                            <div class="highlight-label">Ubicación</div>
                            <div class="highlight-value fw-bold">
                                {{ optional($item->provincia)->nombre ?? '—' }}
                            </div>
                        </div>
                    </div>

                    {{-- DETALLE UBICACIÓN --}}
                    <div class="card border-0 bg-light mb-3">
                        <div class="card-body">

                            <div class="mb-3">
                                <small class="text-muted">Provincia</small>
                                <div class="fw-semibold">
                                    {{ optional($item->provincia)->nombre ?? '—' }}
                                </div>
                                <small class="text-muted">
                                    Código: {{ optional($item->provincia)->codigo ?? '—' }}
                                </small>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted">Cantón</small>
                                <div class="fw-semibold">
                                    {{ optional($item->canton)->nombre ?? '—' }}
                                </div>
                                <small class="text-muted">
                                    Código: {{ optional($item->canton)->codigo ?? '—' }}
                                </small>
                            </div>

                            <div>
                                <small class="text-muted">Distrito</small>
                                <div class="fw-semibold">
                                    {{ optional($item->distrito)->nombre ?? '—' }}
                                </div>
                                <small class="text-muted">
                                    Código: {{ optional($item->distrito)->codigo ?? '—' }}
                                </small>
                            </div>

                        </div>
                    </div>

                    {{-- ESTADO --}}
                    <div class="card border-0 bg-light">
                        <div class="card-body d-flex justify-content-between align-items-center">

                            <div>
                                <label class="fw-semibold d-block mb-1">Estado</label>
                                <small class="text-muted">Disponibilidad</small>
                            </div>

                            @if ($item->activo)
                                <span class="estado-badge bg-success text-white">
                                    <i class="bx bx-check-circle"></i> Activo
                                </span>
                            @else
                                <span class="estado-badge bg-danger text-white">
                                    <i class="bx bx-x-circle"></i> Inactivo
                                </span>
                            @endif

                        </div>
                    </div>

                </div>

                {{-- DERECHA --}}
                <div class="col-md-6">

                    {{-- COSTO --}}
                    <div class="card border-0 bg-light mb-3">
                        <div class="card-body text-center">

                            <div class="mb-2 text-muted">Costo de envío</div>

                            <div class="fw-bold fs-3">
                                ₡{{ number_format($item->costo, 2, '.', ',') }}
                            </div>

                        </div>
                    </div>

                    {{-- REGISTRO --}}
                    <div class="card border-0 bg-light">
                        <div class="card-body">

                            <label class="fw-semibold mb-2">Registro</label>

                            <div class="mb-3">
                                <small class="text-muted">Creado el</small>
                                <div class="fw-semibold">
                                    {{ optional($item->created_at)->format('Y-m-d H:i') }}
                                </div>
                            </div>

                            <div>
                                <small class="text-muted">Última actualización</small>
                                <div class="fw-semibold">
                                    {{ optional($item->updated_at)->format('Y-m-d H:i') }}
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

            </div>

            {{-- BOTONES --}}
            <div class="d-flex justify-content-end gap-3 mt-4">

                <a href="{{ route('admin.zonas-envio.edit', $item->id_zona_envio) }}" class="btn btn-primary-custom">
                    <i class="bx bx-edit"></i>
                    <span>Editar</span>
                </a>

                <form action="{{ route('admin.zonas-envio.destroy', $item->id_zona_envio) }}" method="POST">
                    @csrf
                    @method('DELETE')

                    <button type="button" class="btn btn-danger-custom btn-delete-modal" data-clave="Zona de envío"
                        data-valor="{{ (optional($item->provincia)->nombre ?? '—') .
                            ' / ' .
                            (optional($item->canton)->nombre ?? '—') .
                            ' / ' .
                            (optional($item->distrito)->nombre ?? '—') }}">
                        <i class="bx bx-trash"></i>
                        <span>Eliminar</span>
                    </button>
                </form>

            </div>

        </div>
    </div>

@endsection
