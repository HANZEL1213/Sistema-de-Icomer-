{{-- resources/views/admin/productos/create.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Crear Producto')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/modules/productos.css') }}">

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
                        <a href="{{ route('admin.productos.index') }}">Productos</a>
                    </li>
                    <li class="breadcrumb-item active">Crear</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card card-form">
        <div class="card-body">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="fw-bold text-uppercase mb-1">Nuevo Producto</h4>
                    <small class="text-muted">Registra un nuevo producto</small>
                </div>

                <a href="{{ route('admin.productos.index') }}" class="btn btn-secondary-custom btn-back">
                    <i class="bx bx-arrow-back"></i>
                    <span class="btn-text">Volver</span>
                </a>
            </div>

            <hr>

            <form action="{{ route('admin.productos.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Hidden para imagen principal --}}
                <input type="hidden" name="principal_index" id="principal_index" value="{{ old('principal_index', 0) }}">

                <div class="row g-4">

                    {{-- IZQUIERDA --}}
                    <div class="col-md-6">

                        {{-- IMÁGENES --}}
                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body text-center">
                                <label class="fw-semibold mb-3 d-block">
                                    Imágenes del Producto
                                </label>

                                <div class="image-box mb-3">
                                    <div id="previewContainer" class="d-flex flex-wrap gap-2 justify-content-center"></div>
                                </div>

                                <input type="file" name="imagenes[]" id="imagenes"
                                    class="form-control @error('imagenes') is-invalid @enderror @error('imagenes.*') is-invalid @enderror"
                                    accept="image/*" multiple>

                                @error('imagenes')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror

                                @error('imagenes.*')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror

                                <small class="text-muted d-block mt-2">
                                    JPG, PNG, WEBP | Puedes subir varias imágenes y arrastrarlas para ordenarlas
                                </small>
                            </div>
                        </div>

                        {{-- DESCRIPCIÓN --}}
                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body">
                                <div class="form-floating">
                                    <textarea name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" id="descripcion"
                                        style="height: 120px;">{{ old('descripcion') }}</textarea>
                                    <label for="descripcion">Descripción</label>
                                </div>
                                @error('descripcion')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- SKU --}}
                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body">
                                <div class="form-floating">
                                    <input type="text" name="sku"
                                        class="form-control @error('sku') is-invalid @enderror" id="sku"
                                        value="{{ old('sku') }}">
                                    <label for="sku">SKU</label>
                                </div>
                                @error('sku')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- CÓDIGO --}}
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <div class="form-floating">
                                    <input type="text" name="codigo"
                                        class="form-control @error('codigo') is-invalid @enderror" id="codigo"
                                        value="{{ old('codigo') }}">
                                    <label for="codigo">Código</label>
                                </div>
                                @error('codigo')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>





                   











{{-- DERECHA --}}
<div class="col-md-6">

    {{-- Nombre --}}
    <div class="card border-0 bg-light mb-3">
        <div class="card-body">
            <label class="fw-semibold mb-2">Nombre <span class="text-danger">*</span></label>
            <div class="input-group custom-dark-input">
                <span class="input-group-text">
                    <i class="bx bx-category"></i>
                </span>
                <input type="text" name="nombre" id="nombre"
                    class="form-control @error('nombre') is-invalid @enderror"
                    value="{{ old('nombre') }}" required>
            </div>
            @error('nombre')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- Slug --}}
    <div class="card border-0 bg-light mb-3">
        <div class="card-body">
            <label class="fw-semibold mb-2">Slug <span class="text-danger">*</span></label>
            <div class="input-group custom-dark-input">
                <span class="input-group-text">
                    <i class="bx bx-link"></i>
                </span>
                <input type="text" name="slug" id="slug"
                    class="form-control @error('slug') is-invalid @enderror"
                    value="{{ old('slug') }}">
            </div>
            @error('slug')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- Marca --}}
    <div class="card border-0 bg-light mb-3">
        <div class="card-body">
            <label class="fw-semibold mb-2">Marca</label>
            <select name="id_marca" class="form-select @error('id_marca') is-invalid @enderror">
                <option value="">Seleccione una marca</option>
                @foreach ($marcas as $marca)
                    <option value="{{ $marca->id_marca }}"
                        {{ old('id_marca') == $marca->id_marca ? 'selected' : '' }}>
                        {{ $marca->nombre }}
                    </option>
                @endforeach
            </select>
            @error('id_marca')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- Categoría principal --}}
    <div class="card border-0 bg-light mb-3">
        <div class="card-body">
            <label class="fw-semibold mb-2">Categoría</label>
            <select name="id_categoria_principal" id="id_categoria_principal"
                class="form-select @error('id_categoria_principal') is-invalid @enderror">
                <option value="">Seleccione una categoría</option>
                @foreach ($categorias as $categoria)
                    <option value="{{ $categoria->id_categoria }}"
                        {{ old('id_categoria_principal') == $categoria->id_categoria ? 'selected' : '' }}>
                        {{ $categoria->nombre }}
                    </option>
                @endforeach
            </select>
            @error('id_categoria_principal')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- PRECIO --}}
    <div class="card border-0 bg-light mb-3" id="precioPrincipalCard">
        <div class="card-body">
            <label class="fw-semibold mb-2">Precio <span class="text-danger">*</span></label>
            <div class="input-group custom-dark-input">
                <span class="input-group-text">₡</span>
                <input type="number" step="1" min="0" name="precio" id="precio"
                    class="form-control @error('precio') is-invalid @enderror"
                    value="{{ old('precio') }}" required>
            </div>
            @error('precio')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- DESCUENTO --}}
    <div class="card border-0 bg-light mb-3" id="descuentoPrincipalCard">
        <div class="card-body">

            @php
                $descuentoActivoOld = old('descuento_activo', '0');
                $descuentoActivo =
                    $descuentoActivoOld == '1' ||
                    $descuentoActivoOld === 1 ||
                    $descuentoActivoOld === true ||
                    $descuentoActivoOld === 'on';
            @endphp

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <label class="fw-semibold d-block mb-1">Descuento</label>
                    <small class="text-muted">
                        Activa una oferta para este producto.
                    </small>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <span id="descuentoTexto"
                        class="badge estado-badge px-3 py-2 {{ $descuentoActivo ? 'bg-success' : 'bg-secondary' }}">
                        @if ($descuentoActivo)
                            <i class="bx bx-check-circle me-1"></i> Con descuento
                        @else
                            <i class="bx bx-x-circle me-1"></i> Sin descuento
                        @endif
                    </span>

                    <label class="switch">
                        <input type="checkbox" id="descuentoSwitch" name="descuento_activo"
                            value="1" {{ $descuentoActivo ? 'checked' : '' }}>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>

            <div id="descuentoCampos" style="{{ $descuentoActivo ? 'display:block;' : 'display:none;' }}">
                <hr class="my-3">

                <div class="mb-3">
                    <label class="fw-semibold mb-2">Precio con descuento</label>

                    <div class="input-group custom-dark-input">
                        <span class="input-group-text">₡</span>
                        <input type="number" step="1" min="0" name="precio_descuento"
                            id="precio_descuento"
                            class="form-control @error('precio_descuento') is-invalid @enderror"
                            value="{{ old('precio_descuento') }}">
                    </div>

                    @error('precio_descuento')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror

                    <div class="discount-preview-admin mt-3" id="descuentoPreview" style="display: none;">
                        <div class="discount-preview-badge" id="descuentoPreviewPorcentaje">
                            -0% OFF
                        </div>

                        <div class="discount-preview-text">
                            Ahorro estimado:
                            <strong id="descuentoPreviewAhorro">₡0</strong>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Inicia en</label>
                        <input type="datetime-local" name="descuento_inicio" id="descuento_inicio"
                            class="form-control @error('descuento_inicio') is-invalid @enderror"
                            value="{{ old('descuento_inicio') }}">
                        @error('descuento_inicio')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Termina en</label>
                        <input type="datetime-local" name="descuento_fin" id="descuento_fin"
                            class="form-control @error('descuento_fin') is-invalid @enderror"
                            value="{{ old('descuento_fin') }}">
                        @error('descuento_fin')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <small class="text-muted d-block mt-3">
                    Si activa la promoción debe indicar precio de descuento, fecha de inicio y fecha de finalización.
                </small>
            </div>
        </div>
    </div>

    {{-- STOCK --}}
    <div class="card border-0 bg-light mb-3" id="stockPrincipalCard">
        <div class="card-body">
            <label class="fw-semibold mb-2">Stock <span class="text-danger">*</span></label>
            <div class="input-group custom-dark-input">
                <span class="input-group-text">
                    <i class="bx bx-layer"></i>
                </span>
                <input type="number" step="1" min="0" name="stock_actual" id="stock_actual"
                    class="form-control @error('stock_actual') is-invalid @enderror"
                    value="{{ old('stock_actual', 0) }}">
            </div>
            @error('stock_actual')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- VARIANTES --}}
    @php
        $usaVariantesOld = old('usa_variantes', '0');
        $usaVariantes =
            $usaVariantesOld == '1' ||
            $usaVariantesOld === 1 ||
            $usaVariantesOld === true ||
            $usaVariantesOld === 'on';
    @endphp

    <div class="card border-0 bg-light mb-3">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <label class="fw-semibold d-block mb-1">
                        Variantes del producto
                    </label>
                    <small class="text-muted">
                        Activa esta opción para manejar tallas, colores u otras variantes.
                    </small>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <span id="variantesTexto"
                        class="badge estado-badge px-3 py-2 {{ $usaVariantes ? 'bg-success' : 'bg-secondary' }}">
                        @if ($usaVariantes)
                            <i class="bx bx-check-circle me-1"></i> Con variantes
                        @else
                            <i class="bx bx-x-circle me-1"></i> Sin variantes
                        @endif
                    </span>

                    <label class="switch">
                        <input type="checkbox" id="usaVariantesSwitch" name="usa_variantes"
                            value="1" {{ $usaVariantes ? 'checked' : '' }}>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>

            <div id="variantesCampos" style="{{ $usaVariantes ? 'display:block;' : 'display:none;' }}">
                <hr class="my-3">

                <div class="mb-3">
                    <label class="fw-semibold mb-2">
                        Tipo de variante <span class="text-danger">*</span>
                    </label>

                    <select name="id_tipo_variante" id="id_tipo_variante"
                        class="form-select @error('id_tipo_variante') is-invalid @enderror">
                        <option value="">Seleccione un tipo de variante</option>

                        @foreach ($tiposVariantes as $tipo)
                            <option value="{{ $tipo->id_tipo_variante }}"
                                data-opciones='@json($tipo->opcionesActivas)'
                                {{ old('id_tipo_variante') == $tipo->id_tipo_variante ? 'selected' : '' }}>
                                {{ $tipo->etiqueta ?? $tipo->nombre }}
                            </option>
                        @endforeach
                    </select>

                    @error('id_tipo_variante')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror

                    <small class="text-muted d-block mt-2">
                        Ejemplo: Talla, Color, Tamaño, Presentación.
                    </small>
                </div>

                <div class="border rounded bg-white p-3">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <div>
                            <label class="fw-semibold mb-0">
                                Opciones de variantes
                            </label>
                            <small class="text-muted d-block">
                                Puedes ajustar SKU, precio y stock por cada variante.
                            </small>
                        </div>

                        <button type="button" class="btn btn-sm btn-outline-primary" id="generarVariantesBtn">
                            <i class="bx bx-refresh"></i>
                            Generar opciones
                        </button>
                    </div>

                    <div id="variantesContainer"></div>

                    @error('variantes')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror

                    @error('variantes.*.id_opcion_variante')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror

                    @error('variantes.*.nombre')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror

                    @error('variantes.*.sku')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror

                    @error('variantes.*.precio')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror

                    @error('variantes.*.stock_actual')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror

                    <small class="text-muted d-block mt-3">
                        Cada variante debe tener su propio precio y stock. La variante principal será la que se muestra primero en la tienda.
                    </small>
                </div>
            </div>

        </div>
    </div>

    {{-- Estado --}}
    @php
        $activoOld = old('activo', '1');
        $estaActivo =
            $activoOld == '1' || $activoOld === 1 || $activoOld === true || $activoOld === 'on';
    @endphp

    <div class="card border-0 bg-light">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <label class="fw-semibold d-block mb-1">Estado</label>
                <small class="text-muted">Visible en el sistema</small>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span id="estadoTexto"
                    class="badge estado-badge px-3 py-2 {{ $estaActivo ? 'bg-success' : 'bg-secondary' }}">
                    @if ($estaActivo)
                        <i class="bx bx-check-circle me-1"></i> Activo
                    @else
                        <i class="bx bx-x-circle me-1"></i> Inactivo
                    @endif
                </span>
                <label class="switch">
                    <input type="checkbox" id="activoSwitch" name="activo" value="1"
                        {{ $estaActivo ? 'checked' : '' }}>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
    </div>

    {{-- Destacado --}}
    @php
        $estaDestacado = old('destacado');
    @endphp

    <div class="card border-0 bg-light mt-3">
        <div class="card-body d-flex justify-content-between align-items-center">

            <div>
                <label class="fw-semibold d-block mb-1">
                    Producto destacado
                </label>

                <small class="text-muted">
                    Mostrar este producto en la sección destacada del home
                </small>
            </div>

            <div class="d-flex align-items-center gap-3">

                <span id="destacadoTexto"
                    class="badge estado-badge px-3 py-2 {{ $estaDestacado ? 'bg-success' : 'bg-secondary' }}">
                    @if ($estaDestacado)
                        <i class="bx bx-star me-1"></i> Destacado
                    @else
                        <i class="bx bx-package me-1"></i> Normal
                    @endif
                </span>

                <label class="switch">
                    <input type="checkbox" id="destacadoSwitch" name="destacado" value="1"
                        {{ $estaDestacado ? 'checked' : '' }}>
                    <span class="slider round"></span>
                </label>

            </div>

        </div>
    </div>
</div>























                    {{-- CATEGORÍAS ADICIONALES --}}
                    <div class="col-12 mt-4">
                        <div class="card border-0 bg-light shadow-sm">
                            <div class="card-body">

                                {{-- HEADER COLAPSABLE --}}
                                <div class="d-flex justify-content-between align-items-center" style="cursor: pointer;"
                                    id="toggleCategoriasAdicionales">
                                    <div>
                                        <h5 class="fw-bold mb-1">
                                            <i class="bx bx-category-alt me-1"></i> Categorías adicionales
                                        </h5>
                                        <small class="text-muted">
                                            Selecciona categorías secundarias para este producto.
                                            <span class="fw-bold text-primary" id="selectedCategoriasCount">0</span>
                                            categoría(s) seleccionada(s).
                                        </small>
                                    </div>
                                    <div>
                                        <i class="bx bx-chevron-down" id="toggleCategoriasIcon"
                                            style="font-size: 1.5rem; transition: transform 0.3s;"></i>
                                    </div>
                                </div>

                                {{-- CONTENIDO COLAPSABLE --}}
                                <div id="categoriasAdicionalesContent" style="display: none; margin-top: 1.5rem;">

                                    {{-- Campo real que se enviará al backend --}}
                                    <select name="categorias_adicionales[]" id="categoriasAdicionalesSelect"
                                        class="d-none" multiple>
                                        @foreach (old('categorias_adicionales', []) as $catId)
                                            <option value="{{ $catId }}" selected>{{ $catId }}</option>
                                        @endforeach
                                    </select>

                                    {{-- Campo búsqueda --}}
                                    <div class="mb-3">
                                        <div class="input-group">
                                            <span class="input-group-text bg-white border-end-0">
                                                <i class="bx bx-search"></i>
                                            </span>
                                            <input type="text" id="searchCategoriaAdicional"
                                                class="form-control border-start-0 ps-0"
                                                placeholder="Buscar categoría por nombre o slug...">
                                        </div>
                                    </div>

                                    {{-- Grid categorías --}}
                                    <div class="row g-3" id="categoriasAdicionalesGrid">
                                        @forelse($categorias as $categoria)
                                            <div class="col-sm-6 col-md-4 col-lg-3 categoria-adicional-item"
                                                data-id="{{ $categoria->id_categoria }}"
                                                data-nombre="{{ $categoria->nombre }}"
                                                data-slug="{{ $categoria->slug }}">

                                                <div class="card h-100 border shadow-sm categoria-adicional-card">
                                                    <div class="card-body p-3">
                                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                                            <h6 class="card-title fw-bold mb-0 text-truncate"
                                                                title="{{ $categoria->nombre }}">
                                                                {{ $categoria->nombre }}
                                                            </h6>

                                                            <div class="form-check">
                                                                <input class="form-check-input categoria-checkbox"
                                                                    type="checkbox"
                                                                    value="{{ $categoria->id_categoria }}"
                                                                    id="cat_{{ $categoria->id_categoria }}"
                                                                    {{ collect(old('categorias_adicionales', []))->contains($categoria->id_categoria) ? 'checked' : '' }}>
                                                            </div>
                                                        </div>

                                                        <div class="small text-muted">
                                                            <div>
                                                                <i class="bx bx-link"></i> Slug: {{ $categoria->slug }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="col-12 text-center py-5">
                                                <i class="bx bx-category bx-lg text-muted"></i>
                                                <p class="text-muted mt-2">No hay categorías disponibles.</p>
                                            </div>
                                        @endforelse
                                    </div>

                                    {{-- Preview --}}
                                    <div class="mt-3 pt-3 border-top">
                                        <label class="fw-semibold mb-2">
                                            <i class="bx bx-list-ul me-1"></i> Categorías seleccionadas:
                                        </label>
                                        <div id="categoriasAdicionalesPreview" class="d-flex flex-wrap gap-2"></div>
                                    </div>

                                    @error('categorias_adicionales')
                                        <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror

                                    @error('categorias_adicionales.*')
                                        <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror

                                </div>
                            </div>
                        </div>
                    </div>



















                    {{-- PRODUCTOS RELACIONADOS --}}
                    <div class="col-12 mt-4">
                        <div class="card border-0 bg-light shadow-sm">
                            <div class="card-body">

                                {{-- HEADER COLAPSABLE --}}
                                <div class="d-flex justify-content-between align-items-center" style="cursor: pointer;"
                                    id="toggleRelatedProducts">
                                    <div>
                                        <h5 class="fw-bold mb-1">
                                            <i class="bx bx-link-alt me-1"></i> Productos Relacionados
                                        </h5>
                                        <small class="text-muted">
                                            Selecciona productos que se mostrarán como sugerencias al cliente.
                                            <span class="fw-bold text-primary" id="selectedCount">0</span> producto(s)
                                            seleccionado(s).
                                        </small>
                                    </div>
                                    <div>
                                        <i class="bx bx-chevron-down" id="toggleIcon"
                                            style="font-size: 1.5rem; transition: transform 0.3s;"></i>
                                    </div>
                                </div>

                                {{-- CONTENIDO COLAPSABLE --}}
                                <div id="relatedProductsContent" style="display: none; margin-top: 1.5rem;">

                                    {{-- Campo real que se enviará al backend --}}
                                    <select name="relacionados[]" id="productosRelacionadosSelect" class="d-none"
                                        multiple>
                                        @foreach (old('relacionados', []) as $relId)
                                            <option value="{{ $relId }}" selected>{{ $relId }}</option>
                                        @endforeach
                                    </select>

                                    {{-- Campo búsqueda --}}
                                    <div class="mb-3">
                                        <div class="input-group">
                                            <span class="input-group-text bg-white border-end-0">
                                                <i class="bx bx-search"></i>
                                            </span>
                                            <input type="text" id="searchProductoRel"
                                                class="form-control border-start-0 ps-0"
                                                placeholder="Buscar producto por nombre, SKU o código...">
                                        </div>
                                    </div>

                                    {{-- Grid productos --}}
                                    <div class="row g-3" id="productosRelacionadosGrid">
                                        @forelse($productosRelacionados as $producto)

                                            @php
                                                $tieneDescuento =
                                                    $producto->descuento_activo &&
                                                    $producto->precio_descuento &&
                                                    (!$producto->descuento_inicio ||
                                                        now()->gte($producto->descuento_inicio)) &&
                                                    (!$producto->descuento_fin || now()->lte($producto->descuento_fin));
                                            @endphp

                                            <div class="col-sm-6 col-md-4 col-lg-3 producto-rel-item"
                                                data-id="{{ $producto->id_producto }}"
                                                data-nombre="{{ $producto->nombre }}"
                                                data-sku="{{ $producto->sku ?? '' }}"
                                                data-codigo="{{ $producto->codigo ?? '' }}">

                                                <div class="card h-100 border shadow-sm producto-rel-card">

                                                    <div class="card-body p-3">

                                                        <div class="d-flex gap-2 align-items-start">

                                                            {{-- Imagen --}}
                                                            <div style="width:50px;height:50px;flex:0 0 50px;">

                                                                @if ($producto->imagenPrincipal)
                                                                    <img src="{{ asset('storage/' . $producto->imagenPrincipal->ruta) }}"
                                                                        alt="{{ $producto->nombre }}"
                                                                        style="
                                        width:50px;
                                        height:50px;
                                        object-fit:cover;
                                        border-radius:10px;
                                        border:1px solid #dee2e6;
                                    ">
                                                                @else
                                                                    <div class="d-flex align-items-center justify-content-center bg-light border rounded"
                                                                        style="width:50px;height:50px;">

                                                                        <i class="bx bx-image text-muted"></i>

                                                                    </div>
                                                                @endif

                                                            </div>

                                                            {{-- Información --}}
                                                            <div class="flex-grow-1 overflow-hidden">

                                                                <h6 class="card-title fw-bold mb-1 text-truncate"
                                                                    title="{{ $producto->nombre }}">

                                                                    {{ $producto->nombre }}

                                                                </h6>

                                                                @if ($producto->sku)
                                                                    <div class="small text-muted">
                                                                        <i class="bx bx-barcode"></i>
                                                                        {{ $producto->sku }}
                                                                    </div>
                                                                @endif

                                                                @if ($producto->codigo)
                                                                    <div class="small text-muted">
                                                                        <i class="bx bx-purchase-tag"></i>
                                                                        {{ $producto->codigo }}
                                                                    </div>
                                                                @endif

                                                                {{-- Precio --}}
                                                                <div class="mt-2">

                                                                    @if ($tieneDescuento)
                                                                        <div class="fw-bold text-danger">
                                                                            ₡{{ number_format($producto->precio_descuento, 0, ',', '.') }}
                                                                        </div>

                                                                        <div
                                                                            class="small text-muted text-decoration-line-through">

                                                                            ₡{{ number_format($producto->precio, 0, ',', '.') }}

                                                                        </div>
                                                                    @else
                                                                        <div class="fw-bold text-primary">
                                                                            ₡{{ number_format($producto->precio, 0, ',', '.') }}
                                                                        </div>
                                                                    @endif

                                                                </div>

                                                            </div>

                                                            {{-- Check --}}
                                                            <div class="form-check ms-1">

                                                                <input class="form-check-input product-checkbox"
                                                                    type="checkbox" value="{{ $producto->id_producto }}"
                                                                    id="rel_{{ $producto->id_producto }}"
                                                                    {{ collect(old('relacionados', []))->contains($producto->id_producto) ? 'checked' : '' }}>

                                                            </div>

                                                        </div>

                                                    </div>

                                                </div>

                                            </div>

                                        @empty

                                            <div class="col-12 text-center py-5">

                                                <i class="bx bx-package bx-lg text-muted"></i>

                                                <p class="text-muted mt-2">
                                                    No hay otros productos disponibles para relacionar.
                                                </p>

                                            </div>
                                        @endforelse
                                    </div>

                                    {{-- Preview --}}
                                    <div class="mt-3 pt-3 border-top">
                                        <label class="fw-semibold mb-2">
                                            <i class="bx bx-list-ul me-1"></i> Productos seleccionados:
                                        </label>
                                        <div id="relacionadosPreview" class="d-flex flex-wrap gap-2"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="d-flex justify-content-end gap-3 mt-4">
                    <a href="{{ route('admin.productos.index') }}" class="btn btn-secondary-custom">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary-custom">
                        Guardar
                    </button>
                </div>

            </form>

        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script src="{{ asset('assets/js/modules/productos.js') }}"></script>
@endpush
