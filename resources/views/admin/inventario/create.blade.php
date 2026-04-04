{{-- resources/views/admin/inventario/create.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Nuevo Movimiento')

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
                        <a href="{{ route('admin.inventario-movimientos.index') }}">Inventario</a>
                    </li>
                    <li class="breadcrumb-item active">Nuevo</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card card-form">
        <div class="card-body">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
                <div>
                    <h4 class="fw-bold text-uppercase mb-1">Nuevo Movimiento</h4>
                    <small class="text-muted">Registra una entrada, salida o ajuste de inventario</small>
                </div>

                <a href="{{ route('admin.inventario-movimientos.index') }}" class="btn btn-secondary-custom btn-back"
                    aria-label="Volver" title="Volver">
                    <i class="bx bx-arrow-back"></i>
                    <span class="btn-text">Volver</span>
                </a>
            </div>

            <hr>

            <form action="{{ route('admin.inventario-movimientos.store') }}" method="POST">
                @csrf

                <div class="row g-4">

                    {{-- COLUMNA IZQUIERDA --}}
                    <div class="col-md-6">

                        {{-- Producto --}}
                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body">
                                <label class="fw-semibold mb-2">Producto <span class="text-danger">*</span></label>
                                <div class="input-group custom-dark-input">
                                    <span class="input-group-text">
                                        <i class="bx bx-package"></i>
                                    </span>
                                    <select name="id_producto"
                                        class="form-select @error('id_producto') is-invalid @enderror" required>
                                        <option value="">Seleccione un producto</option>
                                        @foreach ($productos as $producto)
                                            <option value="{{ $producto->id_producto }}"
                                                {{ old('id_producto') == $producto->id_producto ? 'selected' : '' }}>
                                                {{ $producto->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('id_producto')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Tipo de Movimiento --}}
                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body">
                                <label class="fw-semibold mb-2">Tipo de movimiento <span
                                        class="text-danger">*</span></label>
                                <div class="input-group custom-dark-input">
                                    <span class="input-group-text">
                                        <i class="bx bx-transfer"></i>
                                    </span>
                                    <select name="tipo" id="tipo"
                                        class="form-select @error('tipo') is-invalid @enderror" required>
                                        <option value="">Seleccione tipo</option>
                                        <option value="entrada" {{ old('tipo') == 'entrada' ? 'selected' : '' }}>Entrada
                                        </option>
                                        <option value="salida" {{ old('tipo') == 'salida' ? 'selected' : '' }}>Salida
                                        </option>
                                        <option value="ajuste" {{ old('tipo') == 'ajuste' ? 'selected' : '' }}>Ajuste
                                        </option>
                                    </select>
                                </div>
                                @error('tipo')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror

                                <small class="text-muted mt-2 d-block">
                                    <strong>Nota:</strong>
                                    En <b>ajuste</b>, la cantidad representa el <b>nuevo stock final</b> del producto.
                                </small>
                            </div>
                        </div>

                        {{-- Cantidad --}}
                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body">
                                <label class="fw-semibold mb-2">Cantidad <span class="text-danger">*</span></label>
                                <div class="input-group custom-dark-input">
                                    <span class="input-group-text">
                                        <i class="bx bx-plus-medical"></i>
                                    </span>
                                    <input type="number" name="cantidad"
                                        class="form-control @error('cantidad') is-invalid @enderror" min="1"
                                        step="1" required value="{{ old('cantidad') }}" placeholder="Ej: 10">
                                </div>
                                @error('cantidad')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                    </div>

                    {{-- COLUMNA DERECHA --}}
                    <div class="col-md-6">

                        {{-- Motivo --}}
                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body">
                                <label class="fw-semibold mb-2">Motivo <span class="text-danger">*</span></label>
                                <div class="input-group custom-dark-input">
                                    <span class="input-group-text">
                                        <i class="bx bx-note"></i>
                                    </span>
                                    <input type="text" name="motivo"
                                        class="form-control @error('motivo') is-invalid @enderror"
                                        value="{{ old('motivo') }}"
                                        placeholder="Ej: Venta #123, devolución, error de conteo, reposición..." required>
                                </div>
                                @error('motivo')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Usuario realizador --}}
                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body">
                                <label class="fw-semibold mb-2">Realizado por <span class="text-danger">*</span></label>
                                <div class="input-group custom-dark-input">
                                    <span class="input-group-text">
                                        <i class="bx bx-user"></i>
                                    </span>
                                    <select name="id_usuario_realizador"
                                        class="form-select @error('id_usuario_realizador') is-invalid @enderror" required>
                                        <option value="">Seleccione usuario</option>
                                        @foreach ($usuarios as $usuario)
                                            <option value="{{ $usuario->id_usuario }}"
                                                {{ old('id_usuario_realizador') == $usuario->id_usuario ? 'selected' : '' }}>
                                                {{ $usuario->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('id_usuario_realizador')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Referencias --}}
                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body">
                                <label class="fw-semibold mb-3">Referencia (opcional)</label>

                                <div class="mb-3">
                                    <label class="fw-semibold mb-2">
                                        <i class="bx bx-cart"></i> Pedido
                                    </label>
                                    <select name="id_pedido" id="id_pedido"
                                        class="form-select @error('id_pedido') is-invalid @enderror">
                                        <option value="">Sin pedido</option>
                                        @foreach ($pedidos as $pedido)
                                            <option value="{{ $pedido->id_pedido }}"
                                                {{ old('id_pedido') == $pedido->id_pedido ? 'selected' : '' }}>
                                                Pedido #{{ $pedido->id_pedido }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_pedido')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted d-block mt-1">Úsalo cuando el movimiento esté ligado a un
                                        pedido.</small>
                                </div>

                                <div>
                                    <label class="fw-semibold mb-2">
                                        <i class="bx bx-receipt"></i> Venta local
                                    </label>
                                    <select name="id_venta_local" id="id_venta_local"
                                        class="form-select @error('id_venta_local') is-invalid @enderror">
                                        <option value="">Sin venta local</option>
                                        @foreach ($ventasLocales as $venta)
                                            <option value="{{ $venta->id_venta_local }}"
                                                {{ old('id_venta_local') == $venta->id_venta_local ? 'selected' : '' }}>
                                                Venta #{{ $venta->id_venta_local }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_venta_local')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted d-block mt-1">Úsalo cuando el movimiento esté ligado a una
                                        venta local.</small>
                                </div>
                            </div>
                        </div>

                        {{-- Notas --}}
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <div class="form-floating">
                                    <textarea name="notas" class="form-control @error('notas') is-invalid @enderror"
                                        placeholder="Notas adicionales, observaciones..." id="notas" style="height: 140px">{{ old('notas') }}</textarea>
                                    <label for="notas">Notas / Observaciones</label>
                                </div>
                                @error('notas')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                    </div>

                </div>

                {{-- BOTONES --}}
                <div class="d-flex justify-content-end gap-3 mt-4">
                    <a href="{{ route('admin.inventario-movimientos.index') }}" class="btn btn-secondary-custom">
                        Cancelar
                    </a>

                    <button type="submit" class="btn btn-primary-custom d-flex align-items-center gap-2">

                        <span>Guardar</span>
                    </button>
                </div>

            </form>

        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/modules/inventario.js') }}"></script>
@endpush
