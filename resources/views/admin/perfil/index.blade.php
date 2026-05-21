@extends('admin.layouts.app')

@section('title', 'Perfil Administrativo')

@section('content')

    <div class="page-breadcrumb d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-uppercase">Perfil Administrativo</h4>
            <p class="text-muted mb-0">Información de cuenta, seguridad y actividad reciente.</p>
        </div>
    </div>

    <div class="row g-4">

        {{-- CARD PERFIL --}}
        <div class="col-lg-4">

            <div class="card card-form border-0 shadow-sm h-100">
                <div class="card-body text-center p-4">

                    <div class="profile-avatar mx-auto mb-3">
                        {{ strtoupper(substr($usuario->nombre, 0, 1)) }}
                    </div>

                    <h4 class="fw-bold mb-1">
                        {{ $usuario->nombre }}
                    </h4>

                    <p class="text-muted mb-2">
                        {{ $usuario->correo }}
                    </p>

                    <span class="badge bg-success mb-3">
                        {{ $usuario->activo ? 'Activo' : 'Inactivo' }}
                    </span>

                    <div class="card border-0 bg-light mt-3">
                        <div class="card-body py-3">
                            <small class="text-muted d-block">Rol asignado</small>
                            <strong class="text-uppercase">
                                {{ optional($usuario->rol)->nombre ?? 'Sin rol' }}
                            </strong>
                        </div>
                    </div>

                    {{-- SESIONES ACTIVAS --}}
                    <div class="card border-0 bg-light mt-3">

                        <div class="card-body">

                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div>
                                    <small class="text-muted d-block">
                                        Sesiones activas
                                    </small>

                                    <strong>
                                        {{ $sesiones->count() }}
                                        dispositivo(s)
                                    </strong>
                                </div>

                                <i class='bx bx-devices fs-3 text-warning'></i>
                            </div>

                            @forelse ($sesiones->take(3) as $sesion)
                                <div class="profile-session-item">

                                    <div class="profile-session-dot"></div>

                                    <div class="flex-grow-1">

                                        <small class="fw-semibold d-block">
                                            {{ $sesion->ip_address ?? 'IP no disponible' }}
                                        </small>

                                        <small class="text-muted d-block text-truncate">
                                            {{ Str::limit($sesion->user_agent, 55) }}
                                        </small>

                                    </div>

                                </div>

                            @empty

                                <small class="text-muted">
                                    No hay sesiones activas.
                                </small>
                            @endforelse

                        </div>

                    </div>

                </div>
            </div>

        </div>

        {{-- DATOS Y ACCIONES --}}
        <div class="col-lg-8">

            {{-- STATS --}}
            <div class="row g-3 mb-4">

                <div class="col-md-4">
                    <div class="card border-0 bg-light h-100">
                        <div class="card-body">
                            <i class="bx bx-check-shield fs-3 text-success"></i>
                            <h5 class="fw-bold mt-2 mb-0">{{ $stats['pagos_verificados'] }}</h5>
                            <small class="text-muted">Pagos verificados</small>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 bg-light h-100">
                        <div class="card-body">
                            <i class="bx bx-transfer fs-3 text-primary"></i>
                            <h5 class="fw-bold mt-2 mb-0">{{ $stats['movimientos_realizados'] }}</h5>
                            <small class="text-muted">Movimientos</small>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 bg-light h-100">
                        <div class="card-body">
                            <i class="bx bx-package fs-3 text-warning"></i>
                            <h5 class="fw-bold mt-2 mb-0">{{ $stats['pedidos_gestionados'] }}</h5>
                            <small class="text-muted">Pedidos gestionados</small>
                        </div>
                    </div>
                </div>

            </div>

            {{-- EDITAR PERFIL --}}
            <div class="card card-form border-0 shadow-sm mb-4">
                <div class="card-body p-4">

                    <h5 class="fw-bold text-uppercase mb-1">Editar perfil</h5>
                    <p class="text-muted mb-4">Actualiza tus datos principales.</p>

                    <form action="{{ route('admin.perfil.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label" for="nombre">Nombre</label>
                                <input type="text" id="nombre" name="nombre" class="form-control"
                                    value="{{ old('nombre', $usuario->nombre) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="correo">Correo</label>
                                <input type="email" id="correo" name="correo" class="form-control"
                                    value="{{ old('correo', $usuario->correo) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="telefono">Teléfono</label>
                                <input type="text" id="telefono" name="telefono" class="form-control"
                                    value="{{ old('telefono', $usuario->telefono) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="proveedor">Proveedor</label>
                                <input type="text" id="proveedor" name="proveedor" class="form-control"
                                    value="{{ $usuario->provider ?? 'manual' }}" disabled>
                            </div>

                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary-custom">
                                Guardar cambios
                            </button>
                        </div>

                    </form>

                </div>
            </div>

            {{-- CAMBIAR CONTRASEÑA --}}
            <div class="card card-form border-0 shadow-sm mb-4">
                <div class="card-body p-4">

                    <h5 class="fw-bold text-uppercase mb-1">Cambiar contraseña</h5>
                    <p class="text-muted mb-4">Mantén tu cuenta segura.</p>

                    <form action="{{ route('admin.perfil.password') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">

                            <div class="col-md-4">
                                <label class="form-label" for="password_actual">Contraseña actual</label>
                                <input type="password" id="password_actual" name="password_actual" class="form-control">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="password">Nueva contraseña</label>
                                <input type="password" id="password" name="password" class="form-control">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="password_confirmation">Confirmar contraseña</label>
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                    class="form-control">
                            </div>

                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-secondary-custom">
                                Actualizar contraseña
                            </button>
                        </div>

                    </form>

                </div>
            </div>

            {{-- TIMELINE --}}
            <div class="card card-form border-0 shadow-sm">
                <div class="card-body p-4">

                    <h5 class="fw-bold text-uppercase mb-1">Actividad reciente</h5>
                    <p class="text-muted mb-4">Resumen de actividad del perfil.</p>

                    <div class="profile-timeline">

                        @foreach ($timeline as $item)
                            <div class="profile-timeline-item">
                                <div class="profile-timeline-icon">
                                    <i class="{{ $item['icono'] }}"></i>
                                </div>

                                <div>
                                    <h6 class="fw-bold mb-1">{{ $item['titulo'] }}</h6>
                                    <p class="text-muted mb-1">{{ $item['descripcion'] }}</p>
                                    <small class="text-muted">{{ $item['fecha'] }}</small>
                                </div>
                            </div>
                        @endforeach

                    </div>

                </div>
            </div>

        </div>

    </div>

@endsection
