{{-- resources/views/admin/usuarios/edit.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Editar Usuario')

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
                        <a href="{{ route('admin.usuarios.index') }}">Usuarios</a>
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
                    <h4 class="fw-bold text-uppercase mb-1">Editar Usuario</h4>
                    <small class="text-muted">Actualiza la información del usuario</small>
                </div>

                <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary-custom btn-back">
                    <i class="bx bx-arrow-back"></i>
                    <span class="btn-text">Volver</span>
                </a>
            </div>

            <hr>

            <form action="{{ route('admin.usuarios.update', $item->id_usuario) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-4">

                    {{-- IZQUIERDA --}}
                    <div class="col-md-6">

                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body">

                                <label class="fw-semibold mb-3 d-block">
                                    Información Personal
                                </label>

                                {{-- Nombre --}}
                                <div class="mb-3">
                                    <label class="fw-semibold mb-2">
                                        Nombre <span class="text-danger">*</span>
                                    </label>

                                    <div class="input-group custom-dark-input">
                                        <span class="input-group-text">
                                            <i class="bx bx-user"></i>
                                        </span>
                                        <input type="text" name="nombre"
                                            class="form-control @error('nombre') is-invalid @enderror"
                                            placeholder="Ej: Juan Pérez" value="{{ old('nombre', $item->nombre) }}"
                                            required>
                                    </div>

                                    @error('nombre')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- Correo --}}
                                <div class="mb-3">
                                    <label class="fw-semibold mb-2">
                                        Correo <span class="text-danger">*</span>
                                    </label>

                                    <div class="input-group custom-dark-input">
                                        <span class="input-group-text">
                                            <i class="bx bx-envelope"></i>
                                        </span>
                                        <input type="email" name="correo"
                                            class="form-control @error('correo') is-invalid @enderror"
                                            placeholder="correo@ejemplo.com" value="{{ old('correo', $item->correo) }}"
                                            required>
                                    </div>

                                    @error('correo')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- Teléfono --}}
                                <div>
                                    <label class="fw-semibold mb-2">Teléfono</label>

                                    <div class="input-group custom-dark-input">
                                        <span class="input-group-text">
                                            <i class="bx bx-phone"></i>
                                        </span>
                                        <input type="text" name="telefono"
                                            class="form-control @error('telefono') is-invalid @enderror"
                                            placeholder="Ej: 8888-8888" value="{{ old('telefono', $item->telefono) }}">
                                    </div>

                                    @error('telefono')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                            </div>
                        </div>

                    </div>

                    {{-- DERECHA --}}
                    <div class="col-md-6">

                        {{-- SEGURIDAD --}}
                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body">

                                <label class="fw-semibold mb-3 d-block">
                                    Seguridad
                                </label>

                                {{-- Contraseña --}}
                                <div class="mb-3">
                                    <label class="fw-semibold mb-2">Nueva Contraseña</label>

                                    <div class="input-group custom-dark-input">
                                        <span class="input-group-text">
                                            <i class="bx bx-lock"></i>
                                        </span>
                                        <input type="password" name="password"
                                            class="form-control @error('password') is-invalid @enderror"
                                            placeholder="••••••••">
                                    </div>

                                    @error('password')
                                        <small class="text-danger">{{ $message }}</small>
                                    @else
                                        <small class="text-muted">
                                            Déjala en blanco si no deseas cambiarla. Mínimo 8 caracteres.
                                        </small>
                                    @enderror
                                </div>

                                {{-- Correo verificado --}}
                                <div>
                                    <label class="fw-semibold mb-2">Correo verificado en</label>

                                    <input type="datetime-local" name="correo_verificado_en"
                                        class="form-control @error('correo_verificado_en') is-invalid @enderror"
                                        value="{{ old('correo_verificado_en', $item->correo_verificado_en ? \Carbon\Carbon::parse($item->correo_verificado_en)->format('Y-m-d\TH:i') : '') }}">

                                    @error('correo_verificado_en')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                            </div>
                        </div>

                        {{-- ROL --}}
                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body">

                                <label class="fw-semibold mb-2">
                                    Rol del Usuario <span class="text-danger">*</span>
                                </label>

                                <select name="id_rol" class="form-select @error('id_rol') is-invalid @enderror" required>
                                    <option value="">Seleccionar rol</option>
                                    @foreach ($roles as $rol)
                                        <option value="{{ $rol->id_rol }}"
                                            {{ old('id_rol', $item->id_rol) == $rol->id_rol ? 'selected' : '' }}>
                                            {{ $rol->nombre }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('id_rol')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror

                            </div>
                        </div>

                        {{-- ESTADO --}}
                        <div class="card border-0 bg-light">
                            <div class="card-body d-flex justify-content-between align-items-center">

                                <div>
                                    <label class="fw-semibold d-block mb-1">Estado</label>
                                    <small class="text-muted">Acceso al sistema</small>
                                </div>

                                <div class="d-flex align-items-center gap-3">
                                    <span id="estadoTexto"
                                        class="badge estado-badge px-3 py-2 {{ old('activo', $item->activo) ? 'bg-success' : 'bg-secondary' }}">
                                        @if (old('activo', $item->activo))
                                            <i class="bx bx-check-circle me-1"></i> Activo
                                        @else
                                            <i class="bx bx-x-circle me-1"></i> Inactivo
                                        @endif
                                    </span>

                                    <label class="switch">
                                        <input type="checkbox" id="activoSwitch" name="activo" value="1"
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

                    <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary-custom">
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
