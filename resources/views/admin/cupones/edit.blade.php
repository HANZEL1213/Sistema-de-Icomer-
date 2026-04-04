@extends('admin.layouts.app')

@section('title', 'Editar Cupón')

@section('content')

@php
    // 🔥 Datos simulados para el EDIT (como hacemos en Marcas y Productos)
    $cupon = (object)[
        'id' => 10,
        'codigo' => 'BIENVENIDA10',
        'tipo' => 'porcentaje',
        'valor' => 10,
        'minimo_subtotal' => 5000,
        'inicia_en' => '2026-03-22T09:00',
        'termina_en' => '2026-04-30T23:59',
        'max_usos_total' => 100,
        'max_usos_por_usuario' => 1,
        'activo' => true,
    ];
@endphp

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
                    <li class="breadcrumb-item active">Editar</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card card-index">
        <div class="card-body">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="fw-bold text-uppercase mb-1">Editar Cupón</h4>
                    <small class="text-muted">Modifica los datos del cupón</small>
                </div>

                <a href="{{ route('admin.cupones.index') }}" class="btn btn-secondary-custom btn-back">
                    <i class="bx bx-arrow-back"></i>
                    <span class="btn-text">Volver</span>
                </a>
            </div>

            <hr>

            <form action="#" method="POST">
                @csrf

                <div class="row g-4">

                    {{-- 🔥 COLUMNA IZQUIERDA --}}
                    <div class="col-md-6">

                        {{-- Código --}}
                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body">
                                <label class="fw-semibold mb-2">Código</label>
                                <div class="input-group custom-dark-input">
                                    <span class="input-group-text">
                                        <i class="bx bx-barcode"></i>
                                    </span>
                                    <input type="text"
                                           name="codigo"
                                           class="form-control"
                                           value="{{ $cupon->codigo }}"
                                           required>
                                </div>
                            </div>
                        </div>

                        {{-- Tipo --}}
                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body">
                                <label class="fw-semibold mb-2">Tipo de descuento</label>
                                <select name="tipo" class="form-select" required>
                                    <option value="">Seleccione</option>
                                    <option value="porcentaje" {{ $cupon->tipo === 'porcentaje' ? 'selected' : '' }}>
                                        Porcentaje (%)
                                    </option>
                                    <option value="monto_fijo" {{ $cupon->tipo === 'monto_fijo' ? 'selected' : '' }}>
                                        Monto fijo (₡)
                                    </option>
                                </select>
                            </div>
                        </div>

                        {{-- Valor --}}
                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body">
                                <label class="fw-semibold mb-2">Valor</label>
                                <div class="input-group custom-dark-input">
                                    <span class="input-group-text">₡ / %</span>
                                    <input type="number"
                                           step="0.01"
                                           name="valor"
                                           class="form-control"
                                           value="{{ $cupon->valor }}"
                                           required>
                                </div>
                            </div>
                        </div>

                        {{-- Mínimo subtotal --}}
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <label class="fw-semibold mb-2">Mínimo subtotal</label>
                                <div class="input-group custom-dark-input">
                                    <span class="input-group-text">₡</span>
                                    <input type="number"
                                           step="0.01"
                                           name="minimo_subtotal"
                                           class="form-control"
                                           value="{{ $cupon->minimo_subtotal }}">
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- 🔥 COLUMNA DERECHA --}}
                    <div class="col-md-6">

                        {{-- Programación --}}
                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body">

                                <label class="fw-semibold mb-3 d-block">Programación</label>

                                <input type="datetime-local"
                                       name="inicia_en"
                                       class="form-control mb-2"
                                       value="{{ $cupon->inicia_en }}">

                                <input type="datetime-local"
                                       name="termina_en"
                                       class="form-control"
                                       value="{{ $cupon->termina_en }}">

                            </div>
                        </div>

                        {{-- Límites de uso --}}
                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body">

                                <label class="fw-semibold mb-3 d-block">Límites de uso</label>

                                <div class="mb-3">
                                    <label class="small text-muted">Máximo usos total</label>
                                    <input type="number"
                                           name="max_usos_total"
                                           class="form-control"
                                           min="1"
                                           value="{{ $cupon->max_usos_total }}">
                                </div>

                                <div>
                                    <label class="small text-muted">Máximo usos por usuario</label>
                                    <input type="number"
                                           name="max_usos_por_usuario"
                                           class="form-control"
                                           min="1"
                                           value="{{ $cupon->max_usos_por_usuario }}">
                                </div>

                            </div>
                        </div>

                        {{-- Estado --}}
                        <div class="card border-0 bg-light">
                            <div class="card-body d-flex justify-content-between align-items-center">

                                <div>
                                    <label class="fw-semibold d-block mb-1">Estado</label>
                                    <small class="text-muted">Disponible para usar</small>
                                </div>

                                <div class="d-flex align-items-center gap-3">

                                    <span id="estadoTexto"
                                          class="badge estado-badge px-3 py-2 {{ $cupon->activo ? 'bg-success' : 'bg-secondary' }}">
                                        {!! $cupon->activo
                                            ? '<i class="bx bx-check-circle me-1"></i> Activo'
                                            : '<i class="bx bx-x-circle me-1"></i> Inactivo' !!}
                                    </span>

                                    <label class="switch">
                                        <input type="checkbox"
                                               id="activoSwitch"
                                               name="activo"
                                               {{ $cupon->activo ? 'checked' : '' }}>
                                        <span class="slider round"></span>
                                    </label>

                                </div>

                            </div>
                        </div>

                    </div>

                </div>

                {{-- BOTONES --}}
                <div class="d-flex justify-content-end gap-3 mt-4">

                    <a href="{{ route('admin.cupones.index') }}" class="btn btn-secondary-custom">
                        Cancelar
                    </a>

                    <button type="submit" class="btn btn-primary-custom">
                        Actualizar
                    </button>

                </div>

            </form>

        </div>
    </div>

</div>

@endsection