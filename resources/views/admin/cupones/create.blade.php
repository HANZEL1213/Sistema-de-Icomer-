@extends('admin.layouts.app')

@section('title', 'Crear Cupón')

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
                        <li class="breadcrumb-item active">Crear</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="card card-index">
            <div class="card-body">

                {{-- HEADER --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="fw-bold text-uppercase mb-1">Nuevo Cupón</h4>
                        <small class="text-muted">Registra un nuevo cupón promocional</small>
                    </div>

                    <a href="{{ route('admin.cupones.index') }}" class="btn btn-secondary-custom btn-back">
                        <i class="bx bx-arrow-back"></i>
                        <span class="btn-text">Volver</span>
                    </a>
                </div>

                <hr>

                <form action="{{ route('admin.cupones.store') }}" method="POST">
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
                                        <input type="text" name="codigo" class="form-control"
                                            placeholder="Ej: BIENVENIDA10" required>
                                    </div>
                                </div>
                            </div>

                            {{-- Tipo --}}
                            <div class="card border-0 bg-light mb-3">
                                <div class="card-body">
                                    <label class="fw-semibold mb-2">Tipo de descuento</label>
                                    <select name="tipo" class="form-select" required>
                                        <option value="">Seleccione</option>
                                        <option value="porcentaje">Porcentaje (%)</option>
                                        <option value="monto_fijo">Monto fijo (₡)</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Valor --}}
                            <div class="card border-0 bg-light mb-3">
                                <div class="card-body">
                                    <label class="fw-semibold mb-2">Valor</label>
                                    <div class="input-group custom-dark-input">
                                        <span class="input-group-text">
                                            ₡ / %
                                        </span>
                                        <input type="number" step="0.01" name="valor" class="form-control"
                                            placeholder="0.00" required>
                                    </div>
                                </div>
                            </div>

                            {{-- Mínimo subtotal --}}
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <label class="fw-semibold mb-2">Mínimo subtotal</label>
                                    <div class="input-group custom-dark-input">
                                        <span class="input-group-text">₡</span>
                                        <input type="number" step="0.01" name="minimo_subtotal" class="form-control"
                                            value="0">
                                    </div>
                                </div>
                            </div>

                        </div>

                        {{-- 🔥 COLUMNA DERECHA --}}
                        <div class="col-md-6">

                            {{-- Programación --}}
                            <div class="card border-0 bg-light mb-3">
                                <div class="card-body">

                                    <label class="fw-semibold mb-3 d-block">
                                        Programación
                                    </label>

                                    <input type="datetime-local" name="inicia_en" class="form-control mb-2">

                                    <input type="datetime-local" name="termina_en" class="form-control">

                                </div>
                            </div>

                            {{-- Límites de uso --}}
                            <div class="card border-0 bg-light mb-3">
                                <div class="card-body">

                                    <label class="fw-semibold mb-3 d-block">
                                        Límites de uso
                                    </label>

                                    <div class="mb-3">
                                        <label class="small text-muted">Máximo usos total</label>
                                        <input type="number" name="max_usos_total" class="form-control" min="1">
                                    </div>

                                    <div>
                                        <label class="small text-muted">Máximo usos por usuario</label>
                                        <input type="number" name="max_usos_por_usuario" class="form-control"
                                            min="1">
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

                                        <span id="estadoTexto" class="badge estado-badge bg-success px-3 py-2">
                                            <i class="bx bx-check-circle me-1"></i> Activo
                                        </span>

                                        <label class="switch">
                                            <input type="checkbox" name="activo" value="1" id="activoSwitch" checked>
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
                            Guardar
                        </button>

                    </div>

                </form>

            </div>
        </div>

    </div>

@endsection
