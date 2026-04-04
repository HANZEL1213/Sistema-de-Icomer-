{{-- resources/views/admin/roles/edit.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Editar Rol')

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
                        <a href="{{ route('admin.roles.index') }}">Roles</a>
                    </li>
                    <li class="breadcrumb-item active">Editar</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card card-form">
        <div class="card-body">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div>
                    <h4 class="fw-bold text-uppercase mb-1">Editar Rol</h4>
                    <small class="text-muted">Modifica la información del rol</small>
                </div>

                <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary-custom btn-back">
                    <i class="bx bx-arrow-back"></i>
                    <span class="btn-text">Volver</span>
                </a>
            </div>

            <hr>

            <form action="{{ route('admin.roles.update', $item->id_rol) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-4">

                    {{-- IZQUIERDA --}}
                    <div class="col-md-6">

                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body">

                                <label class="fw-semibold mb-3 d-block">
                                    Información del Rol
                                </label>

                                {{-- Nombre --}}
                                <div class="mb-3">
                                    <label class="fw-semibold mb-2">
                                        Nombre <span class="text-danger">*</span>
                                    </label>

                                    <div class="input-group custom-dark-input">
                                        <span class="input-group-text">
                                            <i class="bx bx-id-card"></i>
                                        </span>
                                        <input type="text"
                                               name="nombre"
                                               class="form-control @error('nombre') is-invalid @enderror"
                                               value="{{ old('nombre', $item->nombre) }}"
                                               placeholder="Ej: Administrador"
                                               required>
                                    </div>

                                    @error('nombre')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- Descripción --}}
                                <div>
                                    <label class="fw-semibold mb-2">Descripción</label>

                                    <div class="input-group custom-dark-input">
                                        <span class="input-group-text">
                                            <i class="bx bx-detail"></i>
                                        </span>
                                        <input type="text"
                                               name="descripcion"
                                               class="form-control @error('descripcion') is-invalid @enderror"
                                               value="{{ old('descripcion', $item->descripcion) }}"
                                               placeholder="Ej: Acceso total al sistema">
                                    </div>

                                    @error('descripcion')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                            </div>
                        </div>

                    </div>

                    {{-- DERECHA --}}
                    <div class="col-md-6">

                        <div class="card border-0 bg-light">
                            <div class="card-body d-flex justify-content-between align-items-center">

                                <div>
                                    <label class="fw-semibold d-block mb-1">Estado</label>
                                    <small class="text-muted">Disponibilidad del rol</small>
                                </div>

                                <div class="d-flex align-items-center gap-3">

                                    <span id="estadoTexto"
                                        class="badge estado-badge px-3 py-2 {{ old('activo', $item->activo) ? 'bg-success' : 'bg-secondary' }}">
                                        @if(old('activo', $item->activo))
                                            <i class="bx bx-check-circle me-1"></i> Activo
                                        @else
                                            <i class="bx bx-x-circle me-1"></i> Inactivo
                                        @endif
                                    </span>

                                    <label class="switch">
                                        <input type="checkbox"
                                               id="activoSwitch"
                                               name="activo"
                                               value="1"
                                               {{ old('activo', $item->activo) ? 'checked' : '' }}>
                                        <span class="slider round"></span>
                                    </label>

                                </div>

                            </div>
                        </div>

                    </div>

                </div>

                {{-- BOTONES --}}
                <div class="d-flex justify-content-end gap-3 mt-4">

                    <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary-custom">
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