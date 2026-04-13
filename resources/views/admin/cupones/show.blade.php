@extends('admin.layouts.app')

@section('title', 'Detalle Cupón')

@section('content')

    <div class="page-content">

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
                            <a href="{{ route('admin.cupones.index') }}">Cupones</a>
                        </li>
                        <li class="breadcrumb-item active">Detalle</li>
                    </ol>
                </nav>
            </div>
        </div>

        {{-- Card --}}
        <div class="card card-form">
            <div class="card-body">

                {{-- HEADER --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="fw-bold text-uppercase mb-1">Detalle del Cupón</h4>
                        <small class="text-muted">Información completa del cupón</small>
                    </div>

                    <a href="{{ route('admin.cupones.index') }}" class="btn btn-secondary-custom btn-back">
                        <i class="bx bx-arrow-back"></i>
                        <span class="btn-text">Volver</span>
                    </a>
                </div>

                <hr>

                <div class="row g-4">

                    {{-- IZQUIERDA --}}
                    <div class="col-md-6">

                        {{-- CÓDIGO --}}
                        <div class="card border-0 bg-light mb-3">
                            <div class="highlight-card bg-light text-center">
                                <div class="highlight-icon">
                                    <i class="bx bx-purchase-tag"></i>
                                </div>
                                <div class="highlight-label">Código</div>
                                <div class="highlight-value fw-bold fs-5">
                                    {{ $item->codigo }}
                                </div>
                            </div>
                        </div>

                        {{-- PROGRAMACIÓN --}}
                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body">

                                <label class="fw-semibold mb-2">Programación</label>

                                <div class="mb-2">
                                    <small class="text-muted">Inicio</small>
                                    <div class="fw-semibold">
                                        {{ $item->inicia_en ? $item->inicia_en->format('d/m/Y H:i') : 'No definida' }}
                                    </div>
                                </div>

                                <div>
                                    <small class="text-muted">Fin</small>
                                    <div class="fw-semibold">
                                        {{ $item->termina_en ? $item->termina_en->format('d/m/Y H:i') : 'No definida' }}
                                    </div>
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

                        {{-- TIPO Y VALOR --}}
                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body">

                                <div class="mb-3">
                                    <small class="text-muted">Tipo</small>
                                    <div class="fw-semibold">
                                        @if ($item->tipo === 'porcentaje')
                                            Porcentaje
                                        @else
                                            Monto fijo
                                        @endif
                                    </div>
                                </div>

                                <div>
                                    <small class="text-muted">Valor</small>
                                    <div class="fw-semibold fs-5">
                                        @if ($item->tipo === 'porcentaje')
                                            {{ number_format((float) $item->valor, 2, '.', ',') }}%
                                        @else
                                            ₡{{ number_format((float) $item->valor, 2, '.', ',') }}
                                        @endif
                                    </div>
                                </div>

                            </div>
                        </div>

                        {{-- CONDICIONES --}}
                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body">

                                <div class="mb-3">
                                    <small class="text-muted">Mínimo de compra</small>
                                    <div class="fw-semibold">
                                        ₡{{ number_format((float) $item->minimo_subtotal, 2, '.', ',') }}
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <small class="text-muted">Máximo usos total</small>
                                    <div class="fw-semibold">
                                        {{ $item->max_usos_total ?? 'Ilimitado' }}
                                    </div>
                                </div>

                                <div>
                                    <small class="text-muted">Máximo por usuario</small>
                                    <div class="fw-semibold">
                                        {{ $item->max_usos_por_usuario ?? 'Ilimitado' }}
                                    </div>
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

                    <a href="{{ route('admin.cupones.edit', $item->id_cupon) }}" class="btn btn-primary-custom">
                        <i class="bx bx-edit"></i>
                        <span>Editar</span>
                    </a>

                    <form action="{{ route('admin.cupones.destroy', $item->id_cupon) }}" method="POST"
                        style="display:inline;">
                        @csrf
                        @method('DELETE')

                        <button type="button" class="btn btn-danger-custom btn-delete-modal" data-clave="Cupón"
                            data-valor="{{ $item->codigo }}">
                            <i class="bx bx-trash"></i>
                            <span>Eliminar</span>
                        </button>
                    </form>

                </div>

            </div>
        </div>

    </div>

@endsection
