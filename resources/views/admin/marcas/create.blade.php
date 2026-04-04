{{-- resources/views/admin/marcas/create.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Crear Marca')

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
                        <a href="{{ route('admin.marcas.index') }}">Marcas</a>
                    </li>
                    <li class="breadcrumb-item active">Crear</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card card-form">
        <div class="card-body">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="fw-bold text-uppercase mb-1">Nueva Marca</h4>
                    <small class="text-muted">Registra una nueva marca</small>
                </div>

                <a href="{{ route('admin.marcas.index') }}" class="btn btn-secondary-custom btn-back">
                    <i class="bx bx-arrow-back"></i>
                    <span class="btn-text">Volver</span>
                </a>
            </div>

            <hr>

            <form action="{{ route('admin.marcas.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row g-4">

                    {{-- 🔥 COLUMNA IZQUIERDA --}}
                    <div class="col-md-6">

                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body text-center">

                                <label class="fw-semibold mb-3 d-block">
                                    Imagen de la Marca
                                </label>

                                <div class="image-box mb-3 position-relative">
                                    <img id="preview" src="https://via.placeholder.com/300x300?text=Marca"
                                        data-placeholder="https://via.placeholder.com/300x300?text=Marca"
                                        class="img-fluid rounded shadow-sm" alt="Vista previa de imagen">

                                    <button type="button" id="removeImage"
                                        class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 d-none">
                                        ✕
                                    </button>
                                </div>

                                <input type="file" name="imagen" id="imagen"
                                    class="form-control @error('imagen') is-invalid @enderror" accept="image/*">

                                @error('imagen')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                @enderror

                                <small class="text-muted d-block mt-2">
                                    JPG, PNG, WEBP | Máx 2MB
                                </small>

                            </div>
                        </div>

                    </div>

                    {{-- 🔥 COLUMNA DERECHA --}}
                    <div class="col-md-6">

                        {{-- Nombre --}}
                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body">

                                <label class="fw-semibold mb-2">Nombre <span class="text-danger">*</span></label>

                                <div class="input-group custom-dark-input">
                                    <span class="input-group-text">
                                        <i class="bx bx-category"></i>
                                    </span>
                                    <input type="text" id="nombre" name="nombre"
                                        class="form-control @error('nombre') is-invalid @enderror" placeholder="Ej: Nike"
                                        value="{{ old('nombre') }}" required>
                                </div>

                                @error('nombre')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror

                            </div>
                        </div>

                        {{-- Slug --}}
                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body">

                                <label class="fw-semibold mb-2">Slug <span class="text-danger">*</span></label>

                                <div class="input-group custom-dark-input">
                                    <span class="input-group-text">
                                        <i class="bx bx-link"></i>
                                    </span>
                                    <input type="text" id="slug" name="slug"
                                        class="form-control @error('slug') is-invalid @enderror" placeholder="nike"
                                        value="{{ old('slug') }}" required>
                                </div>

                                @error('slug')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror

                            </div>
                        </div>

                        {{-- ESTADO --}}
                        <div class="card border-0 bg-light">
                            <div class="card-body d-flex justify-content-between align-items-center">

                                <div>
                                    <label class="fw-semibold d-block mb-1">Estado</label>
                                    <small class="text-muted">Visible en el sistema</small>
                                </div>

                                @php
                                    $activoOld = old('activo', '1');
                                    $estaActivo =
                                        $activoOld == '1' ||
                                        $activoOld === 1 ||
                                        $activoOld === true ||
                                        $activoOld === 'on';
                                @endphp

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

                    <a href="{{ route('admin.marcas.index') }}" class="btn btn-secondary-custom">
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

{{-- JS --}}
@push('scripts')
    <script src="{{ asset('assets/js/modules/marcas.js') }}"></script>
@endpush
