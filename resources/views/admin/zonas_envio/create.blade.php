{{-- resources/views/admin/zonas_envio/create.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Nueva Zona de Envío')

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
                    <li class="breadcrumb-item active">Nueva</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card card-form">
        <div class="card-body">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="fw-bold text-uppercase mb-1">Nueva Zona de Envío</h4>
                    <small class="text-muted">Configurar costo de envío por ubicación geográfica</small>
                </div>

                <a href="{{ route('admin.zonas-envio.index') }}" class="btn btn-secondary-custom btn-back">
                    <i class="bx bx-arrow-back"></i>
                    <span class="btn-text">Volver</span>
                </a>
            </div>
            <hr>
            <form action="{{ route('admin.zonas-envio.store') }}" method="POST">
                @csrf

                <div class="row g-4">

                    {{-- Provincia --}}
                    <div class="col-md-4">
                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body">
                                <label for="id_provincia" class="fw-semibold mb-2">Provincia <span class="text-danger">*</span></label>
                                <select name="id_provincia" id="id_provincia" class="form-select" required>
                                    <option value="">Seleccione una provincia</option>
                                    @foreach ($provincias as $provincia)
                                        <option value="{{ $provincia->id_provincia }}"
                                            {{ old('id_provincia') == $provincia->id_provincia ? 'selected' : '' }}>
                                            {{ $provincia->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_provincia')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Cantón --}}
                    <div class="col-md-4">
                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body">
                                <label for="id_canton" class="fw-semibold mb-2">Cantón <span class="text-danger">*</span></label>
                                <select name="id_canton" id="id_canton" class="form-select" required disabled
                                    data-selected="{{ old('id_canton') }}">
                                    <option value="">Seleccione primero una provincia</option>
                                </select>
                                @error('id_canton')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Distrito --}}
                    <div class="col-md-4">
                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body">
                                <label for="id_distrito" class="fw-semibold mb-2">Distrito <span class="text-danger">*</span></label>
                                <select name="id_distrito" id="id_distrito" class="form-select" required disabled
                                    data-selected="{{ old('id_distrito') }}">
                                    <option value="">Seleccione primero un cantón</option>
                                </select>
                                @error('id_distrito')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Costo de envío --}}
                    <div class="col-md-6">
                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body">
                                <label class="fw-semibold mb-2">Costo de envío (₡) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="costo" class="form-control"
                                    value="{{ old('costo') }}" required>
                                @error('costo')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Zona activa --}}
                    <div class="col-md-6">
                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <label class="fw-semibold d-block mb-1">Zona activa</label>
                                    <small class="text-muted">Visible en el sistema</small>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <span id="estadoTexto" class="badge estado-badge bg-success px-3 py-2">
                                        <i class="bx bx-check-circle me-1"></i> Activo
                                    </span>

                                    <label class="switch">
                                        <input type="checkbox" id="activoSwitch" name="activo" value="1"
                                            {{ old('activo', true) ? 'checked' : '' }}>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- BOTONES --}}
                <div class="d-flex justify-content-end gap-3 mt-4">
                    <a href="{{ route('admin.zonas-envio.index') }}" class="btn btn-secondary-custom">
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
    <script src="{{ asset('assets/js/modules/zonas_envio.js') }}"></script>
@endpush
