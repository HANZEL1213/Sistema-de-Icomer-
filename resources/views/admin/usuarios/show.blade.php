{{-- resources/views/admin/usuarios/show.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Detalle Usuario')

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
                    <li class="breadcrumb-item active">Detalle</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card card-form">
        <div class="card-body">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="fw-bold text-uppercase mb-1">Detalle del Usuario</h4>
                    <small class="text-muted">Información completa del usuario</small>
                </div>

                <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary-custom btn-back">
                    <i class="bx bx-arrow-back"></i>
                    <span class="btn-text">Volver</span>
                </a>
            </div>

            <hr>

            <div class="row g-4">

                {{-- IZQUIERDA --}}
                <div class="col-md-6">

                    {{-- PERFIL --}}
                    <div class="card border-0 bg-light mb-3">
                        <div class="card-body text-center">

                            <div class="highlight-icon mb-2">
                                <i class="bx bx-user"></i>
                            </div>

                            <div class="fw-bold fs-5">{{ $item->nombre }}</div>
                            <small class="text-muted">Usuario</small>

                        </div>
                    </div>

                    {{-- ESTADO --}}
                    <div class="card border-0 bg-light">
                        <div class="card-body d-flex justify-content-between align-items-center">

                            <div>
                                <label class="fw-semibold d-block mb-1">Estado</label>
                                <small class="text-muted">Acceso al sistema</small>
                            </div>

                            @if ($item->activo)
                                <span class="estado-badge bg-success text-white">
                                    <i class="bx bx-check-circle"></i> Activo
                                </span>
                            @else
                                <span class="estado-badge bg-danger text-white">
                                    <i class="bx bx-x-circle"></i> Inactivo
                                </span>
                            @endif

                        </div>
                    </div>

                </div>

                {{-- DERECHA --}}
                <div class="col-md-6">

                    {{-- INFO PERSONAL --}}
                    <div class="card border-0 bg-light mb-3">
                        <div class="card-body">

                            <div class="mb-3">
                                <small class="text-muted">Correo</small>
                                <div class="fw-semibold">{{ $item->correo ?? 'Sin correo' }}</div>
                            </div>

                            <div>
                                <small class="text-muted">Teléfono</small>
                                <div class="fw-semibold">
                                    {{ $item->telefono ?? 'Sin teléfono' }}
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- INFO SISTEMA --}}
                    <div class="card border-0 bg-light mb-3">
                        <div class="card-body">

                            <div class="mb-3">
                                <small class="text-muted">Rol</small>
                                <div class="fw-semibold">
                                    {{ $item->rol->nombre ?? 'Sin rol' }}
                                </div>
                            </div>

                            <div>
                                <small class="text-muted">Correo verificado</small>
                                <div>
                                    @if ($item->correo_verificado_en)
                                        <span class="estado-badge bg-success text-white">
                                            <i class="bx bx-badge-check"></i> Verificado
                                        </span>
                                        <div class="text-muted small">
                                            {{ $item->correo_verificado_en->format('d/m/Y H:i') }}
                                        </div>
                                    @else
                                        <span class="estado-badge bg-danger text-white">
                                            <i class="bx bx-error-circle"></i> Pendiente
                                        </span>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- REGISTRO --}}
                    <div class="card border-0 bg-light">
                        <div class="card-body">

                            <label class="fw-semibold mb-2">Registro</label>

                            <div class="mb-3">
                                <small class="text-muted">Creado el</small>
                                <div class="fw-semibold">
                                    {{ optional($item->created_at)->format('Y-m-d H:i') }}
                                </div>
                            </div>

                            <div>
                                <small class="text-muted">Última actualización</small>
                                <div class="fw-semibold">
                                    {{ optional($item->updated_at)->format('Y-m-d H:i') }}
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

            </div>

            {{-- BOTONES --}}
            <div class="d-flex justify-content-end gap-3 mt-4">

                <a href="{{ route('admin.usuarios.edit', $item->id_usuario) }}" class="btn btn-primary-custom">
                    <i class="bx bx-edit"></i>
                    <span>Editar</span>
                </a>

                <form action="{{ route('admin.usuarios.destroy', $item->id_usuario) }}" method="POST"
                    style="display:inline;">
                    @csrf
                    @method('DELETE')

                    <button type="button" class="btn btn-danger-custom btn-delete-modal" data-clave="Usuario"
                        data-valor="{{ $item->nombre }}">
                        <i class="bx bx-trash"></i>
                        <span>Eliminar</span>
                    </button>
                </form>

            </div>

        </div>
    </div>


@endsection
