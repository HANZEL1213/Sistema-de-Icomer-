{{-- resources/views/admin/cupones/usos_cupones/show.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Detalle Uso de Cupón')

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
                        <a href="{{ route('admin.usos-cupones.index') }}">
                            Usos de Cupones
                        </a>
                    </li>
                    <li class="breadcrumb-item active">Detalle</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Card --}}
    <div class="card card-form">
        <div class="card-body">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="fw-bold text-uppercase mb-1">Detalle del Uso</h4>
                    <small class="text-muted">Información del cupón aplicado</small>
                </div>

                <a href="{{ route('admin.usos-cupones.index') }}" class="btn btn-secondary-custom btn-back">
                    <i class="bx bx-arrow-back"></i>
                    <span class="btn-text">Volver</span>
                </a>

            </div>

            <hr>

            {{-- DESTACADOS --}}
            <div class="row g-4 mb-4">

                <div class="col-md-6">
                    <div class="highlight-card bg-light text-center">
                        <div class="highlight-icon">
                            <i class="bx bx-purchase-tag"></i>
                        </div>
                        <div class="highlight-label">Cupón</div>
                        <div class="highlight-value">
                            <span class="fw-bold fs-5">
                                {{ $item->cupon?->codigo ?: 'Cupón no disponible' }}
                            </span>
                            <small class="d-block text-muted">
                                ID: {{ $item->id_cupon }}
                            </small>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="highlight-card bg-light text-center">
                        <div class="highlight-icon">
                            <i class="bx bx-receipt"></i>
                        </div>
                        <div class="highlight-label">Pedido</div>
                        <div class="highlight-value">
                            <span class="fw-bold fs-5">
                                {{ $item->pedido?->numero_pedido ?: 'Pedido no disponible' }}
                            </span>
                            <small class="d-block text-muted">
                                ID: {{ $item->id_pedido }}
                            </small>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row g-4">

                {{-- IZQUIERDA --}}
                <div class="col-md-6">

                    {{-- CLIENTE / USUARIO --}}
                    <div class="card border-0 bg-light mb-3">
                        <div class="card-body">

                            <label class="fw-semibold mb-2 d-block">Usuario</label>

                            @if ($item->usuario)
                                <div class="mb-2">
                                    <small class="text-muted">Nombre</small>
                                    <div class="fw-semibold">{{ $item->usuario->nombre }}</div>
                                </div>

                                <div class="mb-2">
                                    <small class="text-muted">Correo</small>
                                    <div class="fw-semibold">{{ $item->usuario->correo }}</div>
                                </div>

                                <div>
                                    <small class="text-muted">ID Usuario</small>
                                    <div class="fw-semibold">{{ $item->id_usuario }}</div>
                                </div>
                            @else
                                <div class="mb-2">
                                    <small class="text-muted">Tipo de cliente</small>
                                    <div class="fw-semibold">Cliente invitado</div>
                                </div>

                                <div>
                                    <small class="text-muted">Correo registrado</small>
                                    <div class="fw-semibold">
                                        {{ $item->correo_invitado ?: 'Sin correo registrado' }}
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>

                    {{-- FECHA DE USO --}}
                    <div class="card border-0 bg-light">
                        <div class="card-body">

                            <label class="fw-semibold mb-2 d-block">Fecha de uso</label>

                            <div class="mb-3">
                                <small class="text-muted">Fecha</small>
                                <div class="fw-semibold">
                                    {{ optional($item->usado_en)->format('Y-m-d') ?: '—' }}
                                </div>
                            </div>

                            <div>
                                <small class="text-muted">Hora</small>
                                <div class="fw-semibold">
                                    {{ optional($item->usado_en)->format('H:i') ?: '—' }}
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

                {{-- DERECHA --}}
                <div class="col-md-6">

                    {{-- DESCUENTO --}}
                    <div class="card border-0 bg-light mb-3">
                        <div class="card-body text-center">

                            <label class="fw-semibold mb-2 d-block">Descuento aplicado</label>

                            <span class="monto-badge w-100 justify-content-center">
                                ₡{{ number_format((float) $item->monto_descuento, 2, '.', ',') }}
                            </span>

                        </div>
                    </div>

                    {{-- REGISTRO --}}
                    <div class="card border-0 bg-light">
                        <div class="card-body">

                            <label class="fw-semibold mb-2 d-block">Registro</label>

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

        </div>
    </div>



@endsection
