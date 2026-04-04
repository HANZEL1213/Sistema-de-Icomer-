{{-- resources/views/admin/carrusel/show.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Detalle Banner')

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
                        <a href="{{ route('admin.carrusel-items.index') }}">Carrusel</a>
                    </li>
                    <li class="breadcrumb-item active">Detalle</li>
                </ol>
            </nav>
        </div>
    </div>

    @php
        $imagenBanner = $item->ruta_imagen
            ? (\Illuminate\Support\Str::startsWith($item->ruta_imagen, ['http://', 'https://'])
                ? $item->ruta_imagen
                : asset('storage/' . $item->ruta_imagen))
            : 'https://via.placeholder.com/480x240?text=Banner';

        $ahora = now();

        $yaInicio = $item->inicia_en && $item->inicia_en <= $ahora;
        $yaVencio = $item->termina_en && $item->termina_en <= $ahora;
        $estaVigente = $item->inicia_en && $item->termina_en
            ? ($item->inicia_en <= $ahora && $item->termina_en > $ahora)
            : false;

        $tipoDestino = match ($item->tipo_destino) {
            'url' => 'URL',
            'producto' => 'Producto',
            'categoria' => 'Categoría',
            default => 'Sin destino',
        };

        // Permiso manual
        if ($item->activo_manual) {
            $textoActivoManual = 'Activo
';
            $claseActivoManual = 'estado-badge bg-success text-white';
            $iconoActivoManual = 'bx bx-check-circle';
        } else {
            $textoActivoManual = 'Bloqueado';
            $claseActivoManual = 'estado-badge bg-secondary text-white';
            $iconoActivoManual = 'bx bx-x-circle';
        }

        // Estado calculado (activo real)
        if ($item->activo) {
            $textoActivoCalculado = 'Activo';
            $claseActivoCalculado = 'estado-badge bg-success text-white';
            $iconoActivoCalculado = 'bx bx-check-circle';
        } else {
            if (!$yaInicio) {
                $textoActivoCalculado = 'Pendiente';
                $claseActivoCalculado = 'estado-badge bg-info text-white';
                $iconoActivoCalculado = 'bx bx-time-five';
            } elseif ($yaVencio) {
                $textoActivoCalculado = 'Vencido';
                $claseActivoCalculado = 'estado-badge bg-danger text-white';
                $iconoActivoCalculado = 'bx bx-calendar-x';
            } elseif ($estaVigente && !$item->activo_manual) {
                $textoActivoCalculado = 'Bloqueado manual';
                $claseActivoCalculado = 'estado-badge bg-secondary text-white';
                $iconoActivoCalculado = 'bx bx-power-off';
            } else {
                $textoActivoCalculado = 'Inactivo';
                $claseActivoCalculado = 'estado-badge bg-secondary text-white';
                $iconoActivoCalculado = 'bx bx-x-circle';
            }
        }

        // Estado del carrusel
      if ($item->activo && $item->orden > 0) {
            $textoCarrusel = 'En carrusel';
            $claseCarrusel = 'estado-badge bg-success text-white';
            $iconoCarrusel = 'bx bx-check-circle';
        } elseif (!$yaInicio) {
            $textoCarrusel = 'Programado';
            $claseCarrusel = 'estado-badge bg-info text-white';
            $iconoCarrusel = 'bx bx-calendar';
        } elseif ($yaVencio) {
            $textoCarrusel = 'Fuera de rango';
            $claseCarrusel = 'estado-badge bg-danger text-white';
            $iconoCarrusel = 'bx bx-calendar-x';
        } elseif ($estaVigente && !$item->activo_manual) {
            $textoCarrusel = 'Bloqueado manual';
            $claseCarrusel = 'estado-badge bg-secondary text-white';
            $iconoCarrusel = 'bx bx-power-off';
        } else {
            $textoCarrusel = 'Fuera del carrusel';
            $claseCarrusel = 'estado-badge bg-secondary text-white';
            $iconoCarrusel = 'bx bx-x-circle';
        }

        // Estado de programación
        if ($yaVencio) {
            $textoProgramacion = 'Vencido';
            $claseProgramacion = 'badge bg-danger-subtle text-danger border px-3 py-2';
            $iconoProgramacion = 'bx bx-time-five me-1';
        } elseif (!$yaInicio) {
            $textoProgramacion = 'Pendiente de inicio';
            $claseProgramacion = 'badge bg-info-subtle text-info border px-3 py-2';
            $iconoProgramacion = 'bx bx-calendar-event me-1';
        } else {
            $textoProgramacion = 'Dentro de rango';
            $claseProgramacion = 'badge bg-success-subtle text-success border px-3 py-2';
            $iconoProgramacion = 'bx bx-check-circle me-1';
        }

       $ordenActual = $item->activo && $item->orden > 0 ? $item->orden : 0;
    @endphp

    <div class="card card-form">
        <div class="card-body">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
                <div>
                    <h4 class="fw-bold text-uppercase mb-1">Detalle del Banner</h4>
                    <small class="text-muted">Información completa del elemento del carrusel</small>
                </div>

                <a href="{{ route('admin.carrusel-items.index') }}" class="btn btn-secondary-custom btn-back">
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

                            <div class="image-box">
                                <img src="{{ $imagenBanner }}"
                                    class="img-fluid rounded shadow-sm"
                                    alt="{{ $item->titulo ?: 'Banner' }}">
                            </div>

                        </div>
                    </div>

                    {{-- PROGRAMACIÓN --}}
                    <div class="card border-0 bg-light mb-3">
                        <div class="card-body">

                            <label class="fw-semibold mb-3 d-block">Programación</label>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Inicia en</small>
                                    <div class="fw-semibold">
                                        {{ optional($item->inicia_en)->format('d/m/Y') ?: 'No definido' }}
                                    </div>
                                    @if ($item->inicia_en)
                                        <small class="text-muted">
                                            {{ $item->inicia_en->format('H:i') }}
                                        </small>
                                    @endif
                                </div>

                                <div class="col-md-6">
                                    <small class="text-muted d-block">Termina en</small>
                                    <div class="fw-semibold">
                                        {{ optional($item->termina_en)->format('d/m/Y') ?: 'No definido' }}
                                    </div>
                                    @if ($item->termina_en)
                                        <small class="text-muted">
                                            {{ $item->termina_en->format('H:i') }}
                                        </small>
                                    @endif
                                </div>
                            </div>

                            <hr>

                            <div>
                                <small class="text-muted d-block mb-2">Estado de programación</small>
                                <span class="{{ $claseProgramacion }}">
                                    <i class="{{ $iconoProgramacion }}"></i>{{ $textoProgramacion }}
                                </span>
                            </div>

                        </div>
                    </div>

                    {{-- ESTADOS --}}
                    <div class="card border-0 bg-light">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                                <div>
                                    <label class="fw-semibold d-block mb-1">Permiso manual</label>
                                    <small class="text-muted">Decisión del administrador</small>
                                </div>

                                <span class="{{ $claseActivoManual }}">
                                    <i class="{{ $iconoActivoManual }}"></i> {{ $textoActivoManual }}
                                </span>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                                <div>
                                    <label class="fw-semibold d-block mb-1">Estado calculado</label>
                                    <small class="text-muted">Resultado real según fecha y permiso manual</small>
                                </div>

                                <span class="{{ $claseActivoCalculado }}">
                                    <i class="{{ $iconoActivoCalculado }}"></i> {{ $textoActivoCalculado }}
                                </span>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                                <div>
                                    <label class="fw-semibold d-block mb-1">Estado en carrusel</label>
                                    <small class="text-muted">Visibilidad real del banner</small>
                                </div>

                                <span class="{{ $claseCarrusel }}">
                                    <i class="{{ $iconoCarrusel }}"></i> {{ $textoCarrusel }}
                                </span>
                            </div>

                        </div>
                    </div>

                </div>

                {{-- DERECHA --}}
                <div class="col-md-6">

                    {{-- ORDEN ACTUAL --}}
                    <div class="highlight-card bg-light mb-3">
                        <div class="highlight-icon">
                            <i class="bx bx-sort-alt-2"></i>
                        </div>

                        <div class="highlight-label">Orden actual</div>

                        <div class="highlight-value">
                            <span class="monto-badge w-100 justify-content-center">
                                <i class="bx bx-hash"></i> {{ $ordenActual }}
                            </span>
                        </div>
                    </div>

                    {{-- ORDEN PROGRAMADO --}}
                    <div class="highlight-card bg-light mb-3">
                        <div class="highlight-icon">
                            <i class="bx bx-calendar-star"></i>
                        </div>

                        <div class="highlight-label">Orden programado</div>

                        <div class="highlight-value">
                            <span class="monto-badge w-100 justify-content-center">
                                <i class="bx bx-hash"></i> {{ $item->orden_programado }}
                            </span>
                        </div>
                    </div>

                    {{-- INFO --}}
                    <div class="card border-0 bg-light mb-3">
                        <div class="card-body">

                            <div class="mb-3">
                                <small class="text-muted">Título</small>
                                <div class="fw-semibold fs-5">{{ $item->titulo ?: 'Sin título' }}</div>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted">Subtítulo</small>
                                <div class="fw-semibold">{{ $item->subtitulo ?: 'Sin subtítulo' }}</div>
                            </div>

                            <div>
                                <small class="text-muted">Texto del botón</small>
                                <div class="fw-semibold">{{ $item->texto_boton ?: 'Sin texto' }}</div>
                            </div>

                        </div>
                    </div>

                    {{-- DESTINO --}}
                    <div class="card border-0 bg-light mb-3">
                        <div class="card-body">

                            <label class="fw-semibold mb-3 d-block">Destino</label>

                            <div class="mb-3">
                                <small class="text-muted">Tipo de destino</small>
                                <div class="fw-semibold">{{ $tipoDestino }}</div>
                            </div>

                            @if ($item->tipo_destino === 'url')
                                <div>
                                    <small class="text-muted">URL</small>
                                    <div class="fw-semibold mt-1">
                                        @if ($item->url_destino)
                                            <a href="{{ $item->url_destino }}" target="_blank" class="link-pill">
                                                {{ $item->url_destino }}
                                            </a>
                                        @else
                                            <span class="text-muted">No definida</span>
                                        @endif
                                    </div>
                                </div>
                            @elseif ($item->tipo_destino === 'producto')
                                <div>
                                    <small class="text-muted">Producto relacionado</small>
                                    <div class="fw-semibold">
                                        {{ $item->producto?->nombre ?? 'Producto no disponible' }}
                                    </div>
                                </div>
                            @elseif ($item->tipo_destino === 'categoria')
                                <div>
                                    <small class="text-muted">Categoría relacionada</small>
                                    <div class="fw-semibold">
                                        {{ $item->categoria?->nombre ?? 'Categoría no disponible' }}
                                    </div>
                                </div>
                            @else
                                <div>
                                    <small class="text-muted">Detalle</small>
                                    <div class="fw-semibold text-muted">Este banner no tiene destino configurado.</div>
                                </div>
                            @endif

                        </div>
                    </div>

                    {{-- REGISTRO --}}
                    <div class="card border-0 bg-light">
                        <div class="card-body">

                            <label class="fw-semibold mb-3 d-block">Registro</label>

                            <div class="mb-3">
                                <small class="text-muted">Creado el</small>
                                <div class="fw-semibold">
                                    {{ optional($item->created_at)->format('d/m/Y H:i') }}
                                </div>
                            </div>

                            <div>
                                <small class="text-muted">Última actualización</small>
                                <div class="fw-semibold">
                                    {{ optional($item->updated_at)->format('d/m/Y H:i') }}
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

            </div>

            {{-- BOTONES --}}
            <div class="d-flex justify-content-end gap-3 mt-4 flex-wrap">

                <a href="{{ route('admin.carrusel-items.edit', $item->id_carrusel_item) }}" class="btn btn-primary-custom">
                    <i class="bx bx-edit"></i>
                    <span>Editar</span>
                </a>

                <form action="{{ route('admin.carrusel-items.destroy', $item->id_carrusel_item) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')

                    <button type="button"
                        class="btn btn-danger-custom btn-delete-modal"
                        data-clave="Banner"
                        data-valor="{{ $item->titulo ?: 'Banner #' . $item->id_carrusel_item }}">
                        <i class="bx bx-trash"></i>
                        <span>Eliminar</span>
                    </button>
                </form>

            </div>

        </div>
    </div>



@endsection