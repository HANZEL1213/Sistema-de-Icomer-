{{-- resources/views/admin/configuracion/create.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Nueva Configuración')

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
                        <a href="{{ route('admin.configuracion.index') }}">Configuración</a>
                    </li>
                    <li class="breadcrumb-item active">Crear</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Card --}}
    <div class="card card-form">
        <div class="card-body">

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div>
                    <h4 class="mb-1 text-uppercase fw-bold">Nueva Configuración</h4>
                    <small class="text-muted">Crear un nuevo parámetro (clave → valor)</small>
                </div>

                <a href="{{ route('admin.configuracion.index') }}" class="btn btn-secondary-custom btn-back">
                    <i class="bx bx-arrow-back"></i>
                    <span class="btn-text">Volver</span>
                </a>
            </div>

            <hr />

            {{-- Form --}}
            <form action="{{ route('admin.configuracion.store') }}" method="POST">
                @csrf

                <div class="row g-3">

                    {{-- Clave --}}
                    <div class="col-md-6">
                        <label for="claveInput" class="form-label fw-semibold">
                            Clave <span class="text-danger">*</span>
                        </label>

                        <div class="input-group custom-dark-input">
                            <span class="input-group-text">
                                <i class="bx bx-key"></i>
                            </span>
                            <input type="text" name="clave" id="claveInput"
                                class="form-control @error('clave') is-invalid @enderror" placeholder="Ej: tienda_nombre"
                                value="{{ old('clave') }}" required>
                        </div>

                        @error('clave')
                            <small class="text-danger">{{ $message }}</small>
                        @else
                            <small class="text-muted">Identificador único, preferiblemente sin espacios.</small>
                        @enderror
                    </div>

                    {{-- Valor --}}
                    <div class="col-md-6">
                        <label for="valorInput" class="form-label fw-semibold">
                            Valor
                        </label>

                        <input type="text" name="valor" id="valorInput"
                            class="form-control @error('valor') is-invalid @enderror" placeholder="Ej: Rukada Store"
                            value="{{ old('valor') }}">

                        @error('valor')
                            <small class="text-danger">{{ $message }}</small>
                        @else
                            <small class="text-muted">Puede dejarse vacío según la validación actual.</small>
                        @enderror
                    </div>

                    {{-- Preview --}}
                    <div class="col-12">
                        <div class="alert alert-light border mt-2">
                            <strong>Preview:</strong>
                            <span class="ms-2 text-muted" id="previewTexto">
                                {{ old('clave', 'clave') }} → {{ old('valor', 'valor') }}
                            </span>
                        </div>
                    </div>

                </div>

                {{-- Botones --}}
                <div class="d-flex justify-content-end gap-3 mt-4">
                    <a href="{{ route('admin.configuracion.index') }}" class="btn btn-secondary-custom">
                        Cancelar
                    </a>

                    <button type="submit" class="btn btn-primary-custom">
                        Guardar
                    </button>
                </div>
            </form>

        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/modules/configuracion.js') }}"></script>
@endpush
