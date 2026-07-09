{{-- resources/views/admin/configuracion/show.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Ver Configuración')

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
                    <li class="breadcrumb-item active">Ver</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Card --}}
    <div class="card card-index">
        <div class="card-body">

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="fw-bold text-uppercase mb-1">Detalle de Configuración</h4>
                    <small class="text-muted">Parámetro del sistema (clave → valor)</small>
                </div>

                <a href="{{ route('admin.configuracion.index') }}" class="btn btn-secondary-custom btn-back">
                    <i class="bx bx-arrow-back"></i>
                    <span class="btn-text">Volver</span>
                </a>
            </div>

            <hr>

            {{-- INFO --}}

            <div class="row g-4">

                {{-- CLAVE --}}
                <div class="col-md-6">
                    <div class="card bg-light border-0 h-100">
                        <div class="card-body">
                            <label class="fw-semibold mb-2 d-block">Clave</label>
                            <div class="border p-3 rounded bg-white fw-bold">
                                {{ $item->clave }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- VALOR --}}
                <div class="col-md-6">
                    <div class="card bg-light border-0 h-100">
                        <div class="card-body">
                            <label class="fw-semibold mb-2 d-block">Valor</label>
                            <div class="border p-3 rounded bg-white fw-bold">
                                {{ $item->valor ?? 'Sin valor' }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- REGISTRO --}}
                <div class="col-md-12">
                    <div class="card bg-light border-0">
                        <div class="card-body">
                            <label class="fw-semibold mb-2 d-block">Registro</label>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Creado el</small>
                                    <div class="border p-3 rounded bg-white fw-semibold">
                                        {{ optional($item->created_at)->format('Y-m-d H:i') }}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <small class="text-muted d-block">Última actualización</small>
                                    <div class="border p-3 rounded bg-white fw-semibold text-muted">
                                        {{ optional($item->updated_at)->format('Y-m-d H:i') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- BOTONES --}}
            <div class="d-flex justify-content-end gap-3 mt-4">

                {{-- Editar --}}
                <a href="{{ route('admin.configuracion.edit', $item->clave) }}" class="btn btn-primary-custom">
                    <i class="bx bx-edit"></i>
                    <span>Editar</span>
                </a>

                {{-- Eliminar --}}
                <form action="{{ route('admin.configuracion.destroy', $item->clave) }}" method="POST"
                    class="form-eliminar-config" style="display:inline;">
                    @csrf
                    @method('DELETE')

                    <button type="button" class="btn btn-danger-custom btn-delete-modal" data-clave=" Configuracion"
                        data-valor="{{ $item->clave }}">
                        <i class="bx bx-trash"></i>
                        <span>Eliminar</span>
                    </button>
                </form>



            </div>

        </div>
    </div>


@endsection

@push('scripts')
    <script src="{{ asset('assets/js/modules/configuracion.js') }}"></script>
@endpush
