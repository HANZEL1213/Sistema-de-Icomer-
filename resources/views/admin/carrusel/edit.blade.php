{{-- resources/views/admin/carrusel/edit.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Editar Banner')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/modules/carrusel.css') }}">

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
                    <li class="breadcrumb-item active">Editar</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card card-form">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
                <div>
                    <h4 class="fw-bold text-uppercase mb-1">Editar Banner</h4>
                    <small class="text-muted">Modifica el elemento del carrusel</small>
                </div>

                <a href="{{ route('admin.carrusel-items.index') }}" class="btn btn-secondary-custom btn-back">
                    <i class="bx bx-arrow-back"></i>
                    <span class="btn-text">Volver</span>
                </a>
            </div>

            <hr>

            <form action="{{ route('admin.carrusel-items.update', $item->id_carrusel_item) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <input type="hidden" name="eliminar_imagen" id="eliminar_imagen" value="0">

                <div class="row g-4">

                    <div class="col-md-6">

                        {{-- IMAGEN --}}
                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body text-center">

                                <label class="fw-semibold mb-3 d-block">
                                    Imagen del Banner
                                </label>
                                @php
                                    $imagenActual = $item->ruta_imagen
                                        ? (\Illuminate\Support\Str::startsWith($item->ruta_imagen, [
                                            'http://',
                                            'https://',
                                        ])
                                            ? $item->ruta_imagen
                                            : asset('storage/' . $item->ruta_imagen))
                                        : null;

                                    $activoManualOld = old('activo_manual', $item->activo_manual ? '1' : '0');
                                    $activoManual =
                                        $activoManualOld == '1' ||
                                        $activoManualOld === 1 ||
                                        $activoManualOld === true ||
                                        $activoManualOld === 'on'
                                            ? 1
                                            : 0;
                                @endphp

                                <div class="image-box banner-image-box mb-3 position-relative">
                                    <div id="previewPlaceholder"
                                        class="image-placeholder {{ $imagenActual ? 'd-none' : '' }}">
                                        <i class="bx bx-image"></i>
                                        <span>Sin imágenes</span>
                                    </div>

                                    <img id="preview" src="{{ $imagenActual ?? '' }}" data-placeholder=""
                                        class="img-fluid rounded shadow-sm {{ $imagenActual ? '' : 'd-none' }}"
                                        alt="Vista previa de imagen">

                                    <button type="button" id="removeImage"
                                        class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 {{ $imagenActual ? '' : 'd-none' }}">
                                        ✕
                                    </button>
                                </div>

                                <input type="file" id="ruta_imagen" name="ruta_imagen"
                                    class="form-control @error('ruta_imagen') is-invalid @enderror" accept="image/*">

                                @error('ruta_imagen')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                @enderror

                                <small class="text-muted d-block mt-2">
                                    JPG, PNG, WEBP | Máx 2MB
                                </small>

                                <small id="imageReplaceHelper" class="text-muted d-block mt-2 d-none">
                                    Debes seleccionar una nueva imagen para reemplazar la actual.
                                </small>

                            </div>
                        </div>

                        {{-- PROGRAMACIÓN --}}
                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body">

                                <label class="fw-semibold mb-3 d-block">Programación</label>

                                <div class="mb-3">
                                    <label class="form-label">
                                        Inicia en <span class="text-danger">*</span>
                                    </label>
                                    <input type="datetime-local" name="inicia_en" id="inicia_en"
                                        class="form-control @error('inicia_en') is-invalid @enderror"
                                        value="{{ old('inicia_en', optional($item->inicia_en)->format('Y-m-d\TH:i')) }}"
                                        required>
                                    @error('inicia_en')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div>
                                    <label class="form-label">
                                        Termina en <span class="text-danger">*</span>
                                    </label>
                                    <input type="datetime-local" name="termina_en" id="termina_en"
                                        class="form-control @error('termina_en') is-invalid @enderror"
                                        value="{{ old('termina_en', optional($item->termina_en)->format('Y-m-d\TH:i')) }}"
                                        required>
                                    @error('termina_en')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>
                        </div>

                        {{-- ESTADO --}}
                        <div class="card border-0 bg-light">
                            <div class="card-body">

                                <input type="hidden" name="activo_manual" id="activoManualHidden"
                                    value="{{ $activoManual }}">

                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                                    <div>
                                        <label class="fw-semibold d-block mb-1">Activación manual</label>
                                        <small class="text-muted">
                                            Si está dentro del rango, este control define si el banner podrá entrar al
                                            carrusel.
                                        </small>
                                    </div>

                                    <div class="d-flex align-items-center gap-3">
                                        <span id="estadoActivoTexto"
                                            class="btn {{ $activoManual ? 'btn-primary-custom' : 'btn-danger-custom' }} btn-sm px-3 py-2 d-inline-flex align-items-center">
                                            @if ($activoManual)
                                                <i class="bx bx-check-circle me-1"></i> Permitido manualmente
                                            @else
                                                <i class="bx bx-x-circle me-1"></i> Bloqueado manualmente
                                            @endif
                                        </span>

                                        <label class="switch">
                                            <input type="checkbox" id="activoSwitchVisual"
                                                {{ $activoManual ? 'checked' : '' }}>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>

                                <hr class="my-3">

                                <div>
                                    <label class="fw-semibold d-block mb-2">Estado calculado al guardar</label>

                                    <div class="d-flex flex-wrap gap-2 mb-3">
                                        <span id="estadoHabilitadoTexto"
                                            class="btn btn-secondary-custom btn-sm px-3 py-2 d-inline-flex align-items-center">
                                            <i class="bx bx-time-five me-1"></i> Pendiente de evaluar
                                        </span>

                                        <span id="estadoFechaTexto"
                                            class="btn btn-secondary-custom btn-sm px-3 py-2 d-inline-flex align-items-center">
                                            <i class="bx bx-calendar me-1"></i> Sin rango definido
                                        </span>
                                    </div>

                                    <div class="bg-light border rounded p-2 small text-muted" id="estadoHelper">
                                        <i class="bx bx-info-circle me-1"></i>
                                        El sistema activará el banner solo si está permitido manualmente y además entra en
                                        su rango de fechas.
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>

                    <div class="col-md-6">

                        {{-- POSICIÓN --}}
                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body">

                                <label class="fw-semibold mb-3 d-flex align-items-center gap-2">
                                    <i class="bx bx-sort-alt-2"></i> Posición programada <span
                                        class="text-danger">*</span>
                                </label>

                                @php
                                    $ordenOld = old('orden_programado', $item->orden_programado ?? 1);
                                    $ordenOld = is_numeric($ordenOld) ? (int) $ordenOld : 1;
                                    $ordenOld = max(1, $ordenOld);
                                @endphp

                                <div class="row g-3 align-items-center">

                                    <div class="col-md-8">
                                        <div class="input-group custom-dark-input">
                                            <span class="input-group-text">
                                                <i class="bx bx-hash"></i>
                                            </span>
                                            <input type="number" name="orden_programado" id="ordenNumero"
                                                class="form-control @error('orden_programado') is-invalid @enderror"
                                                min="1" step="1" value="{{ $ordenOld }}"
                                                placeholder="Ingrese la posición" required>
                                        </div>

                                        @error('orden_programado')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror

                                        <small class="text-muted d-block mt-2">
                                            Esta será la posición cuando el banner entre en vigencia y quede activo en el
                                            carrusel.
                                        </small>
                                    </div>

                                    <div class="col-md-4 text-end">
                                        <span class="badge bg-primary px-3 py-2">
                                            Programada: <span id="valorOrden">{{ $ordenOld }}</span>
                                        </span>
                                    </div>

                                </div>

                            </div>
                        </div>

                        {{-- TEXTO --}}
                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body">

                                <div class="form-floating mb-3">
                                    <input type="text" name="titulo"
                                        class="form-control @error('titulo') is-invalid @enderror" id="titulo"
                                        placeholder=" " value="{{ old('titulo', $item->titulo) }}">
                                    <label for="titulo">Título</label>
                                    @error('titulo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-floating mb-3">
                                    <input type="text" name="subtitulo"
                                        class="form-control @error('subtitulo') is-invalid @enderror" id="subtitulo"
                                        placeholder=" " value="{{ old('subtitulo', $item->subtitulo) }}">
                                    <label for="subtitulo">Subtítulo</label>
                                    @error('subtitulo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-floating">
                                    <input type="text" name="texto_boton"
                                        class="form-control @error('texto_boton') is-invalid @enderror" id="texto_boton"
                                        placeholder=" " value="{{ old('texto_boton', $item->texto_boton) }}">
                                    <label for="texto_boton">Texto del botón</label>
                                    @error('texto_boton')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>
                        </div>

                        {{-- DESTINO --}}
                        <div class="card border-0 bg-light">
                            <div class="card-body">

                                <label class="fw-semibold mb-2">Destino</label>

                                @php
                                    $tipoDestinoOld = old('tipo_destino', $item->tipo_destino);
                                @endphp

                                <select name="tipo_destino" id="tipoDestino"
                                    class="form-select mb-3 @error('tipo_destino') is-invalid @enderror">
                                    <option value="">Seleccionar</option>
                                    <option value="url" {{ $tipoDestinoOld === 'url' ? 'selected' : '' }}>URL</option>
                                    <option value="producto" {{ $tipoDestinoOld === 'producto' ? 'selected' : '' }}>
                                        Producto</option>
                                    <option value="categoria" {{ $tipoDestinoOld === 'categoria' ? 'selected' : '' }}>
                                        Categoría</option>
                                </select>
                                @error('tipo_destino')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror

                                <div id="campoUrl" class="{{ $tipoDestinoOld === 'url' ? '' : 'd-none' }}">
                                    <input type="url" name="url_destino"
                                        class="form-control @error('url_destino') is-invalid @enderror"
                                        placeholder="https://..." value="{{ old('url_destino', $item->url_destino) }}">
                                    @error('url_destino')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div id="campoProducto" class="{{ $tipoDestinoOld === 'producto' ? '' : 'd-none' }}">
                                    <select name="id_producto"
                                        class="form-select @error('id_producto') is-invalid @enderror">
                                        <option value="">Producto</option>
                                        @foreach ($productos as $producto)
                                            <option value="{{ $producto->id_producto }}"
                                                {{ old('id_producto', $item->id_producto) == $producto->id_producto ? 'selected' : '' }}>
                                                {{ $producto->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_producto')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div id="campoCategoria" class="{{ $tipoDestinoOld === 'categoria' ? '' : 'd-none' }}">
                                    <select name="id_categoria"
                                        class="form-select @error('id_categoria') is-invalid @enderror">
                                        <option value="">Categoría</option>
                                        @foreach ($categorias as $categoria)
                                            <option value="{{ $categoria->id_categoria }}"
                                                {{ old('id_categoria', $item->id_categoria) == $categoria->id_categoria ? 'selected' : '' }}>
                                                {{ $categoria->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_categoria')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>
                        </div>

                    </div>

                </div>

                <div class="d-flex justify-content-end gap-3 mt-4">
                    <a href="{{ route('admin.carrusel-items.index') }}" class="btn btn-secondary-custom">
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

@push('scripts')
    <script src="{{ asset('assets/js/modules/carrusel.js') }}"></script>
@endpush
