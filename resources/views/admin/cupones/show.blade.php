@extends('admin.layouts.app')

@section('title', 'Detalle Cupón')

@section('content')

@php
    $cupon = (object)[
        'id_cupon' => 1,
        'codigo' => 'BLACK20',
        'tipo' => 'porcentaje', // porcentaje | monto
        'valor' => 20,
        'minimo_subtotal' => 30000,
        'inicia_en' => now()->subDays(2),
        'termina_en' => now()->addDays(5),
        'max_usos_total' => 100,
        'max_usos_por_usuario' => 2,
        'activo' => true,
    ];
@endphp

<div class="page-content">

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
                        <a href="{{ route('admin.cupones.index') }}">Cupones</a>
                    </li>
                    <li class="breadcrumb-item active">Detalle</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card card-index">
        <div class="card-body">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="fw-bold text-uppercase mb-1">Detalle del Cupón</h4>
                    <small class="text-muted">Información completa del cupón</small>
                </div>

                <a href="{{ route('admin.cupones.index') }}" 
                   class="btn btn-secondary-custom btn-back">
                    <i class="bx bx-arrow-back"></i>
                    <span class="btn-text">Volver</span>
                </a>
            </div>

            <hr>

            <div class="row g-4">

                {{-- IZQUIERDA --}}
                <div class="col-md-6">

                    {{-- CÓDIGO --}}
                    <div class="card border-0 bg-light mb-3">
                       <div class="highlight-card bg-light text-center">
                        <div class="highlight-icon">
                            <i class="bx bx-purchase-tag"></i>
                        </div>
                        <div class="highlight-label">Código</div>
                        <div class="highlight-value fw-bold fs-5">
                            {{ $cupon->codigo }}
                        </div>
                    </div>
                    </div>

                    {{-- PROGRAMACIÓN --}}
                    <div class="card border-0 bg-light mb-3">
                        <div class="card-body">

                            <label class="fw-semibold mb-2">Programación</label>

                            <div class="mb-2">
                                <small class="text-muted">Inicio</small>
                                <div class="fw-semibold">
                                    {{ $cupon->inicia_en ? \Carbon\Carbon::parse($cupon->inicia_en)->format('d/m/Y H:i') : 'No definida' }}
                                </div>
                            </div>

                            <div>
                                <small class="text-muted">Fin</small>
                                <div class="fw-semibold">
                                    {{ $cupon->termina_en ? \Carbon\Carbon::parse($cupon->termina_en)->format('d/m/Y H:i') : 'No definida' }}
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- ESTADO (AL FINAL COMO CATEGORÍA) --}}
                    <div class="card border-0 bg-light">
                        <div class="card-body d-flex justify-content-between align-items-center">

                            <div>
                                <label class="fw-semibold d-block mb-1">Estado</label>
                                <small class="text-muted">Disponibilidad</small>
                            </div>

                            @if($cupon->activo)
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

                    {{-- TIPO Y VALOR --}}
                    <div class="card border-0 bg-light mb-3">
                        <div class="card-body">

                            <div class="mb-3">
                                <small class="text-muted">Tipo</small>
                                <div class="fw-semibold">
                                    @if($cupon->tipo === 'porcentaje')
                                        Porcentaje
                                    @else
                                        Monto fijo
                                    @endif
                                </div>
                            </div>

                            <div>
                                <small class="text-muted">Valor</small>
                                <div class="fw-semibold fs-5">
                                    @if($cupon->tipo === 'porcentaje')
                                        {{ $cupon->valor }}%
                                    @else
                                        ₡{{ number_format($cupon->valor, 0, '.', ',') }}
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- CONDICIONES --}}
                    <div class="card border-0 bg-light mb-3">
                        <div class="card-body">

                            <div class="mb-3">
                                <small class="text-muted">Mínimo de compra</small>
                                <div class="fw-semibold">
                                    ₡{{ number_format($cupon->minimo_subtotal, 0, '.', ',') }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted">Máximo usos total</small>
                                <div class="fw-semibold">
                                    {{ $cupon->max_usos_total ?? 'Ilimitado' }}
                                </div>
                            </div>

                            <div>
                                <small class="text-muted">Máximo por usuario</small>
                                <div class="fw-semibold">
                                    {{ $cupon->max_usos_por_usuario ?? 'Ilimitado' }}
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

            </div>

            {{-- BOTONES --}}
            <div class="d-flex justify-content-end gap-3 mt-4">

                <a href="{{ route('admin.cupones.edit', $cupon->id_cupon) }}"
                   class="btn btn-primary-custom">
                    <i class="bx bx-edit"></i>
                    <span>Editar</span>
                </a>
                
                <form action="{{ route('admin.cupones.destroy', $cupon->id_cupon) }}"
                      method="POST"
                      onsubmit="return confirm('¿Eliminar este cupón?')"
                      style="display:inline;">
                    @csrf
                    @method('DELETE')

                    <button type="submit" class="btn btn-danger-custom">
                        <i class="bx bx-trash"></i>
                        <span>Eliminar</span>
                    </button>
                </form>

            </div>

        </div>
    </div>

</div>

@endsection