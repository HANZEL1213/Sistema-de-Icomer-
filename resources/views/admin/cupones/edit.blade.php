@extends('admin.layouts.app')

@section('title', 'Editar Cupón')

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
                        <a href="{{ route('admin.cupones.index') }}">Cupones</a>
                    </li>
                    <li class="breadcrumb-item active">Editar</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card card-form">
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

            @php
                $activoOld = old('activo', $item->activo ? '1' : '0');
                $estaActivo = $activoOld == '1' || $activoOld === 1 || $activoOld === true || $activoOld === 'on';
            @endphp

            <form action="{{ route('admin.cupones.update', $item->id_cupon) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-4">

                    {{-- COLUMNA IZQUIERDA --}}
                    <div class="col-md-6">

                        {{-- Código --}}
                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body">

                                <label class="fw-semibold mb-2">Código <span class="text-danger">*</span></label>

                                <div class="input-group custom-dark-input">
                                    <span class="input-group-text">
                                        <i class="bx bx-barcode"></i>
                                    </span>
                                    <input type="text" id="codigo" name="codigo"
                                        class="form-control @error('codigo') is-invalid @enderror"
                                        placeholder="Ej: BIENVENIDA10" value="{{ old('codigo', $item->codigo) }}" required>
                                </div>

                                @error('codigo')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror

                                <small class="text-muted d-block mt-2">
                                    Usa un código fácil de recordar y escribir.
                                </small>

                            </div>
                        </div>

                        {{-- Tipo --}}
                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body">

                                <label class="fw-semibold mb-2">Tipo de descuento <span class="text-danger">*</span></label>

                                <select name="tipo" id="tipo"
                                    class="form-select @error('tipo') is-invalid @enderror" required>
                                    <option value="">Seleccione</option>
                                    <option value="porcentaje"
                                        {{ old('tipo', $item->tipo) === 'porcentaje' ? 'selected' : '' }}>
                                        Porcentaje (%)
                                    </option>
                                    <option value="monto_fijo"
                                        {{ old('tipo', $item->tipo) === 'monto_fijo' ? 'selected' : '' }}>
                                        Monto fijo (₡)
                                    </option>
                                </select>

                                @error('tipo')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror

                                <small class="text-muted d-block mt-2">
                                    Selecciona si el descuento será porcentual o un monto fijo.
                                </small>

                            </div>
                        </div>

                        {{-- Valor --}}
                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body">

                                <label class="fw-semibold mb-2">Valor <span class="text-danger">*</span></label>

                                <div class="input-group custom-dark-input">
                                    <span class="input-group-text">₡ / %</span>
                                    <input type="number" step="0.01" min="0.01" id="valor" name="valor"
                                        class="form-control @error('valor') is-invalid @enderror" placeholder="Ej: 10.00"
                                        value="{{ old('valor', $item->valor) }}" required>
                                </div>

                                @error('valor')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror

                                <small class="text-muted d-block mt-2">
                                    Si es porcentaje, el valor no debe ser mayor a 100.
                                </small>

                            </div>
                        </div>

                        {{-- Mínimo subtotal --}}
                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body">

                                <label class="fw-semibold mb-2">Mínimo subtotal</label>

                                <div class="input-group custom-dark-input">
                                    <span class="input-group-text">₡</span>
                                    <input type="number" step="0.01" min="0" id="minimo_subtotal"
                                        name="minimo_subtotal"
                                        class="form-control @error('minimo_subtotal') is-invalid @enderror"
                                        placeholder="0.00" value="{{ old('minimo_subtotal', $item->minimo_subtotal) }}">
                                </div>

                                @error('minimo_subtotal')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror

                                <small class="text-muted d-block mt-2">
                                    Opcional. Déjalo en 0 o vacío para aplicarlo sin compra mínima.
                                </small>

                            </div>
                        </div>

                    </div>

                    {{-- COLUMNA DERECHA --}}
                    <div class="col-md-6">

                        {{-- Programación --}}
                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body">

                                <label class="fw-semibold mb-3 d-block">Programación</label>

                                <div class="mb-3">
                                    <label for="inicia_en" class="small text-muted">Fecha de inicio</label>
                                    <input type="datetime-local" id="inicia_en" name="inicia_en"
                                        class="form-control @error('inicia_en') is-invalid @enderror"
                                        value="{{ old('inicia_en', optional($item->inicia_en)->format('Y-m-d\TH:i')) }}">
                                    @error('inicia_en')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted d-block mt-2">
                                        Opcional. Déjalo vacío para que el cupón pueda usarse desde cualquier momento.
                                    </small>
                                </div>

                                <div>
                                    <label for="termina_en" class="small text-muted">Fecha de finalización</label>
                                    <input type="datetime-local" id="termina_en" name="termina_en"
                                        class="form-control @error('termina_en') is-invalid @enderror"
                                        value="{{ old('termina_en', optional($item->termina_en)->format('Y-m-d\TH:i')) }}">
                                    @error('termina_en')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted d-block mt-2">
                                        Opcional. Déjalo vacío para que el cupón no tenga vencimiento.
                                    </small>
                                </div>

                            </div>
                        </div>

                        {{-- Límites de uso --}}
                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body">

                                <label class="fw-semibold mb-3 d-block">Límites de uso</label>

                                <div class="mb-3">
                                    <label for="max_usos_total" class="small text-muted">Máximo usos total</label>
                                    <input type="number" min="1" id="max_usos_total" name="max_usos_total"
                                        class="form-control @error('max_usos_total') is-invalid @enderror"
                                        placeholder="Ej: 100" value="{{ old('max_usos_total', $item->max_usos_total) }}">
                                    @error('max_usos_total')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted d-block mt-2">
                                        Opcional. Déjalo vacío para permitir usos ilimitados.
                                    </small>
                                </div>

                                <div>
                                    <label for="max_usos_por_usuario" class="small text-muted">Máximo usos por
                                        usuario</label>
                                    <input type="number" min="1" id="max_usos_por_usuario"
                                        name="max_usos_por_usuario"
                                        class="form-control @error('max_usos_por_usuario') is-invalid @enderror"
                                        placeholder="Ej: 1"
                                        value="{{ old('max_usos_por_usuario', $item->max_usos_por_usuario) }}">
                                    @error('max_usos_por_usuario')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted d-block mt-2">
                                        Opcional. Déjalo vacío para no limitar cuántas veces lo usa un mismo cliente.
                                    </small>
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
                                        class="badge estado-badge px-3 py-2 {{ $estaActivo ? 'bg-success' : 'bg-secondary' }}">
                                        @if ($estaActivo)
                                            <i class="bx bx-check-circle me-1"></i> Activo
                                        @else
                                            <i class="bx bx-x-circle me-1"></i> Inactivo
                                        @endif
                                    </span>

                                    <label class="switch">
                                        <input type="checkbox" id="activoSwitch" name="activo" value="1"
                                            {{ $estaActivo ? 'checked' : '' }}>
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

@endsection
