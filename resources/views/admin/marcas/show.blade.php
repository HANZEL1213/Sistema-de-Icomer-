{{-- resources/views/admin/marcas/show.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Detalle Marca')

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
                    <h4 class="fw-bold text-uppercase mb-1">Detalle de la Marca</h4>
                    <small class="text-muted">Información completa de la marca</small>
                </div>

                <a href="{{ route('admin.marcas.index') }}" class="btn btn-secondary-custom btn-back">
                    <i class="bx bx-arrow-back"></i>
                    <span class="btn-text">Volver</span>
                </a>
            </div>

            <hr>

            <div class="row g-4">

                {{-- IZQUIERDA --}}
                <div class="col-md-6">

                    {{-- IMAGEN --}}
                    <div class="card border-0 bg-light mb-3">
                        <div class="card-body text-center">

                            <label class="fw-semibold mb-3 d-block">Imagen</label>

                            @php
                                $imagenMarca = $item->imagen
                                    ? (\Illuminate\Support\Str::startsWith($item->imagen, ['http://', 'https://'])
                                        ? $item->imagen
                                        : asset('storage/' . $item->imagen))
                                    : 'https://via.placeholder.com/300x300?text=Marca';
                            @endphp

                            <div class="image-box">
                                <img src="{{ $imagenMarca }}" class="img-fluid rounded shadow-sm"
                                    alt="{{ $item->nombre }}">
                            </div>

                        </div>
                    </div>

                    {{-- ESTADO --}}
                    <div class="card border-0 bg-light">
                        <div class="card-body d-flex justify-content-between align-items-center">

                            <div>
                                <label class="fw-semibold d-block mb-1">Estado</label>
                                <small class="text-muted">Visibilidad</small>
                            </div>

                            @if ($item->activo)
                                <span class="estado-badge bg-success text-white">
                                    <i class="bx bx-check-circle"></i> Activa
                                </span>
                            @else
                                <span class="estado-badge bg-danger text-white">
                                    <i class="bx bx-x-circle"></i> Inactiva
                                </span>
                            @endif

                        </div>
                    </div>

                </div>

                {{-- DERECHA --}}
                <div class="col-md-6">

                    {{-- INFO --}}
                    <div class="card border-0 bg-light mb-3">
                        <div class="card-body">

                            <div class="mb-3">
                                <small class="text-muted">Nombre</small>
                                <div class="fw-semibold fs-5">{{ $item->nombre }}</div>
                            </div>

                            <div>
                                <small class="text-muted">Slug</small>
                                <div class="fw-semibold">{{ $item->slug }}</div>
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

                <a href="{{ route('admin.marcas.edit', $item->id_marca) }}" class="btn btn-primary-custom">
                    <i class="bx bx-edit"></i>
                    <span>Editar</span>
                </a>

                <form action="{{ route('admin.marcas.destroy', $item->id_marca) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')

                    <button type="button" class="btn btn-danger-custom btn-delete-modal" data-clave="Marca"
                        data-valor="{{ $item->nombre }}">
                        <i class="bx bx-trash"></i>
                        <span>Eliminar</span>
                    </button>
                </form>

            </div>

        </div>
    </div>



@endsection
