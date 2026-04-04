{{-- resources/views/admin/roles/show.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Detalle Rol')

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
                    <h4 class="fw-bold text-uppercase mb-1">Detalle del Rol</h4>
                    <small class="text-muted">Información completa del rol</small>
                </div>

                <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary-custom btn-back">
                    <i class="bx bx-arrow-back"></i>
                    <span class="btn-text">Volver</span>
                </a>
            </div>

            <hr>

            <div class="row g-4">

                {{-- IZQUIERDA --}}
                <div class="col-md-6">

                    {{-- INFO --}}
                    <div class="card border-0 bg-light mb-3">
                        <div class="card-body">

                            <div class="mb-3">
                                <small class="text-muted">Nombre del rol</small>
                                <div class="fw-semibold fs-5">{{ $item->nombre }}</div>
                            </div>

                            <div>
                                <small class="text-muted">Descripción</small>
                                <div class="fw-semibold">
                                    {{ $item->descripcion ?? 'Sin descripción' }}
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- ESTADO --}}
                    <div class="card border-0 bg-light">
                        <div class="card-body d-flex justify-content-between align-items-center">

                            <div>
                                <label class="fw-semibold d-block mb-1">Estado</label>
                                <small class="text-muted">Disponibilidad del rol</small>
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

                    {{-- USUARIOS --}}
                    <div class="highlight-card bg-light mb-3 text-center">
                        <div class="highlight-icon">
                            <i class="bx bx-group"></i>
                        </div>
                        <div class="highlight-label">Usuarios asignados</div>
                        <div class="highlight-value fw-bold fs-4">
                            {{ $item->usuarios_count }}
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

                {{-- Editar --}}
                <a href="{{ route('admin.roles.edit', $item->id_rol) }}" class="btn btn-primary-custom">
                    <i class="bx bx-edit"></i>
                    <span>Editar</span>
                </a>

                {{-- Eliminar (MODAL GLOBAL) --}}
                <form action="{{ route('admin.roles.destroy', $item->id_rol) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')

                    <button type="button" class="btn btn-danger-custom btn-delete-modal" data-clave="Rol"
                        data-valor="{{ $item->nombre }}">
                        <i class="bx bx-trash"></i>
                        <span>Eliminar</span>
                    </button>
                </form>

            </div>

        </div>
    </div>



@endsection
