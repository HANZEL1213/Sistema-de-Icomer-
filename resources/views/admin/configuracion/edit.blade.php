{{-- resources/views/admin/configuracion/edit.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Editar Configuración')

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
                    <li class="breadcrumb-item active">Editar</li>
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
                    <h4 class="mb-1 text-uppercase fw-bold">Editar Configuración</h4>
                    <small class="text-muted">Modificar parámetro (clave → valor)</small>
                </div>

                <a href="{{ route('admin.configuracion.index') }}"
                   class="btn btn-secondary-custom btn-back">
                    <i class="bx bx-arrow-back"></i>
                    <span class="btn-text">Volver</span>
                </a>
            </div>

            <hr />

            {{-- Form --}}
            <form action="{{ route('admin.configuracion.update', $item->clave) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">

                    {{-- Clave --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Clave</label>

                        <div class="input-group custom-dark-input">
                            <span class="input-group-text">
                                <i class="bx bx-key"></i>
                            </span>
                            <input
                                type="text"
                                class="form-control"
                                value="{{ $item->clave }}"
                                disabled
                            >
                        </div>

                        <small class="text-muted">
                            No editable porque corresponde a la clave primaria.
                        </small>
                    </div>

                    {{-- Valor --}}
                    <div class="col-md-6">
                        <label for="valorInput" class="form-label fw-semibold">Valor</label>

                        <input
                            type="text"
                            name="valor"
                            id="valorInput"
                            class="form-control @error('valor') is-invalid @enderror"
                            placeholder="Ej: Rukada Store"
                            value="{{ old('valor', $item->valor) }}"
                        >

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
                                {{ $item->clave }} → {{ old('valor', $item->valor ?? 'valor') }}
                            </span>
                        </div>
                    </div>

                </div>

                {{-- Botones --}}
                <div class="d-flex justify-content-end gap-3 mt-4">
                    <a href="{{ route('admin.configuracion.index') }}"
                       class="btn btn-secondary-custom">
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

@push('scripts')
<script src="{{ asset('assets/js/modules/configuracion.js') }}"></script>
@endpush