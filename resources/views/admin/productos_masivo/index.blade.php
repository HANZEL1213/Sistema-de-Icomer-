{{-- resources/views/admin/productos_masivo/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'CRUD Masivo de productos')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/modules/productos_masivo.css') }}">
@endpush

@section('content')

    {{-- Breadcrumb --}}
    <div class="page-breadcrumb d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.productos.index') }}">Productos</a>
                    </li>
                    <li class="breadcrumb-item active">CRUD Masivo</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Raíz del módulo: el JS lee las URLs desde aquí (nada hardcodeado en el .js) --}}

    <div id="masivo-app" data-url-listar="{{ route('admin.productos-masivo.listar') }}"
        data-url-crear="{{ route('admin.productos-masivo.crear') }}"
        data-url-editar="{{ route('admin.productos-masivo.editar') }}"
        data-url-cambiar-activo="{{ route('admin.productos-masivo.cambiar-activo') }}"
        data-url-eliminar="{{ route('admin.productos-masivo.eliminar') }}"
        data-url-previsualizar-borrado="{{ route('admin.productos-masivo.previsualizar-borrado') }}"
        data-url-ver="{{ route('admin.productos.show', ':id') }}"
        data-url-editar-uno="{{ route('admin.productos.edit', ':id') }}">


        {{-- Reporte de resultado de operaciones masivas (lo llena el JS) --}}
        <div id="masivoResultado" class="d-none"></div>

        {{-- ─────────── 1. Contexto: Categoría + Marca ─────────── --}}
        <div class="card card-form mb-3">
            <div class="card-body">

                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-2">
                    <div>
                        <h4 class="mb-1 fw-bold">CRUD Masivo por Categoría y Marca</h4>
                        <small class="text-muted">
                            Sube, edita o elimina productos en bloque a partir de un lote de imágenes.
                        </small>
                    </div>

                    <a href="{{ route('admin.productos.index') }}"
                        class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
                        <i class="bx bx-arrow-back"></i>
                        <span>Volver a productos</span>
                    </a>
                </div>

                <hr class="my-3" />

                <h6 class="fw-bold mb-1">1. Elegí categoría y marca</h6>
                <small class="text-muted d-block mb-3">
                    Solo se listan categorías y marcas ya existentes. Este módulo no crea nuevas.
                </small>

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label for="ctxCategoria" class="form-label fw-semibold">Categoría</label>
                        <select id="ctxCategoria" class="form-select">
                            <option value="">Elegí una categoría…</option>
                            @foreach ($categorias as $categoria)
                                <option value="{{ $categoria->id_categoria }}">{{ $categoria->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="ctxMarca" class="form-label fw-semibold">Marca</label>
                        <select id="ctxMarca" class="form-select">
                            <option value="">Elegí una marca…</option>
                            @foreach ($marcas as $marca)
                                <option value="{{ $marca->id_marca }}">{{ $marca->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Resumen del par elegido (el JS lo muestra/oculta y lo rellena) --}}
                <div id="ctxResumen" class="ctx-resumen d-none mt-3">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="ctx-badge" id="ctxBadge">—</span>
                        <span class="text-muted small"><b id="ctxTotal">0</b> productos en este par</span>
                    </div>
                    <div class="text-muted small">
                        Próximo número de lote: <b id="ctxProximoLote">#0001</b>
                    </div>
                </div>

            </div>
        </div>

        {{-- ─────────── 2. Lista de productos del par ─────────── --}}
        <div class="card card-form">
            <div class="card-body">

                {{-- Acción principal --}}
                <div class="d-flex justify-content-end mb-3">
                    <button type="button" id="btnCargar" class="btn btn-nuevo d-inline-flex align-items-center gap-2"
                        disabled>
                        <i class="bx bx-plus"></i>
                        <span>Cargar imágenes (Bulk Create)</span>
                    </button>
                </div>

                {{-- Buscador + por página --}}
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
                    <div class="search-container">
                        <div class="search-box" role="search">
                            <i class="bx bx-search search-icon"></i>
                            <input type="text" id="masivoBuscar" class="search-input"
                                placeholder="Buscar por nombre o número de lote…" autocomplete="off">
                            <div class="search-actions">
                                <button class="btn-search-clear" id="masivoLimpiar" type="button" title="Limpiar">
                                    <i class="bx bx-x"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="pagination-perpage-top">
                        <div class="input-group input-group-perpage">
                            <span class="input-group-text bg-transparent border-end-0">
                                <i class="bx bx-list-ul"></i>
                            </span>
                            <select class="form-select form-select-sm border-start-0" id="masivoPorPagina">
                                <option value="25">25</option>
                                <option value="50" selected>50</option>
                                <option value="100">100</option>
                            </select>
                            <span class="input-group-text bg-transparent border-start-0">
                                <span class="perpage-label">por página</span>
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Barra de selección (aparece cuando hay productos marcados) --}}
                <div id="barraSeleccion" class="barra-seleccion d-none mb-3">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span><b id="selCount">0</b> <span id="selTexto">productos seleccionados</span></span>
                        <a href="#" id="btnSelTodosFiltro" class="small d-none">
                            seleccionar los <b id="selTotalFiltro">0</b> del filtro completo
                        </a>
                        <a href="#" id="btnLimpiarSel" class="small d-none">limpiar selección</a>
                    </div>

                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <button type="button" id="btnEditarSel"
                            class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1">
                            <i class="bx bx-edit"></i> Editar selección
                        </button>
                        <button type="button" id="btnEliminarSel"
                            class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1">
                            <i class="bx bx-trash"></i> Eliminar selección
                        </button>
                    </div>
                </div>

                {{-- Tabla propia: NO usa #tabla_index, así admin-table.js la ignora --}}
                <div class="table-responsive">
                    <table id="tabla_masivo" class="table table-hover table-bordered align-middle text-center w-100">
                        <thead class="table-light">
                            <tr>
                                <th style="width:42px">
                                    <input type="checkbox" id="chkTodos" class="form-check-input"
                                        title="Seleccionar los de esta página">
                                </th>
                                <th class="fw-semibold text-start">Producto</th>
                                <th class="fw-semibold">Precio</th>
                                <th class="fw-semibold">Stock</th>
                                <th class="fw-semibold">Activo</th>
                                <th class="fw-semibold">Destacado</th>
                                <th class="fw-semibold">Acciones</th>
                            </tr>
                        </thead>

                        {{-- Lo llena productos_masivo.js --}}
                        <tbody id="tbodyMasivo"></tbody>
                    </table>
                </div>

                {{-- Estados vacíos (el JS muestra el que corresponda) --}}
                <div id="estadoInicial" class="masivo-vacio">
                    <i class="bx bx-filter-alt"></i>
                    <p class="mb-0">Elegí una categoría y una marca para ver sus productos.</p>
                </div>
                <div id="estadoSinProductos" class="masivo-vacio d-none">
                    <i class="bx bx-package"></i>
                    <p class="mb-0">Este par todavía no tiene productos. Usá “Cargar imágenes” para crear el primer lote.
                    </p>
                </div>
                <div id="estadoSinResultados" class="masivo-vacio d-none">
                    <i class="bx bx-search-alt"></i>
                    <p class="mb-0">No hay productos que coincidan con la búsqueda.</p>
                </div>

                {{-- Paginación (mismas clases que el resto del panel; los números los genera el JS) --}}
                <div class="custom-pagination-container mt-3">
                    <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3 py-2">
                        <div class="pagination-info">
                            <span class="text-muted">Mostrando</span>
                            <span class="fw-semibold text-dark ms-1" id="pgDesde">0</span>
                            <span class="text-muted">a</span>
                            <span class="fw-semibold text-dark mx-1" id="pgHasta">0</span>
                            <span class="text-muted">de</span>
                            <span class="fw-semibold text-dark ms-1" id="pgTotal">0</span>
                            <span class="text-muted">resultados</span>
                        </div>

                        <div class="pagination-controls">
                            <nav aria-label="Paginación del CRUD masivo">
                                <ul class="pagination pagination-modern mb-0" id="pgLista">
                                    <li class="page-item disabled" id="pgPrev">
                                        <a class="page-link" href="#" aria-label="Anterior">
                                            <i class="bx bx-chevron-left"></i>
                                        </a>
                                    </li>
                                    {{-- números generados por JS --}}
                                    <li class="page-item disabled" id="pgNext">
                                        <a class="page-link" href="#" aria-label="Siguiente">
                                            <i class="bx bx-chevron-right"></i>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ─────────── Modal: Eliminar selección ─────────── --}}
        <div class="modal fade" id="modalEliminar" tabindex="-1" aria-labelledby="modalEliminarTitulo"
            aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title fw-bold" id="modalEliminarTitulo">Eliminar selección definitivamente
                            </h5>
                            <small class="text-muted">Esta acción no se puede deshacer</small>
                        </div>
                        <button type="button" class="btn-close" id="delCerrarX" data-bs-dismiss="modal"
                            aria-label="Cerrar"></button>
                    </div>

                    <div class="modal-body">
                        <div class="alert alert-danger d-flex align-items-start gap-2" role="alert">
                            <i class="bx bx-error fs-5"></i>
                            <div id="delMensaje"></div>
                        </div>

                    
                        <div id="delCarrusel" class="alert alert-warning small d-none" role="alert"></div>

                        <ul id="delLista" class="masivo-lista-borrado"></ul>

                        <label for="delInput" class="form-label small mt-3 mb-1">
                            Escribí <b class="text-danger">BORRAR</b> para confirmar
                        </label>
                        <input type="text" id="delInput" class="form-control" placeholder="BORRAR"
                            autocomplete="off" spellcheck="false">
                    </div>

                    <div class="modal-footer">
                        <button type="button" id="delCancelar" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" id="delConfirmar"
                            class="btn btn-danger d-inline-flex align-items-center gap-2" disabled>
                            <span id="delSpinner" class="spinner-border spinner-border-sm d-none"
                                aria-hidden="true"></span>
                            <i id="delIcono" class="bx bx-trash"></i>
                            <span>Eliminar definitivamente</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─────────── Modal: Editar selección en bloque ─────────── --}}
        <div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarTitulo" aria-hidden="true"
            data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title fw-bold" id="modalEditarTitulo">Editar selección en bloque</h5>
                            <small class="text-muted" id="edSubtitulo"></small>
                        </div>
                        <button type="button" class="btn-close" id="edCerrarX" data-bs-dismiss="modal"
                            aria-label="Cerrar"></button>
                    </div>

                    <div class="modal-body">

                        {{-- Cada campo: casilla + control. El control queda apagado hasta marcar la casilla. --}}
                        <div class="ed-campo" data-campo="precio">
                            <div class="form-check">
                                <input class="form-check-input ed-check" type="checkbox" id="edChkPrecio">
                                <label class="form-check-label fw-semibold" for="edChkPrecio">Precio</label>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text">₡</span>
                                <input type="number" min="1" step="1" id="edPrecio"
                                    class="form-control ed-control" placeholder="32000" disabled>
                            </div>
                        </div>

                        <div class="ed-campo" data-campo="stock">
                            <div class="form-check">
                                <input class="form-check-input ed-check" type="checkbox" id="edChkStock">
                                <label class="form-check-label fw-semibold" for="edChkStock">Stock</label>
                            </div>
                            <div class="d-flex gap-2">
                                <select id="edStockModo" class="form-select ed-control" disabled>
                                    <option value="fijar">Fijar en un valor exacto</option>
                                    <option value="sumar">Sumar cantidad</option>
                                    <option value="restar">Restar cantidad</option>
                                </select>
                                <input type="number" min="0" step="1" id="edStock"
                                    class="form-control ed-control" placeholder="0" style="max-width: 110px" disabled>
                            </div>
                        </div>

                        <div class="ed-campo" data-campo="activo">
                            <div class="form-check">
                                <input class="form-check-input ed-check" type="checkbox" id="edChkActivo">
                                <label class="form-check-label fw-semibold" for="edChkActivo">Estado</label>
                            </div>
                            <select id="edActivo" class="form-select ed-control" disabled>
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>

                        <div class="ed-campo" data-campo="destacado">
                            <div class="form-check">
                                <input class="form-check-input ed-check" type="checkbox" id="edChkDestacado">
                                <label class="form-check-label fw-semibold" for="edChkDestacado">Destacado</label>
                            </div>
                            <select id="edDestacado" class="form-select ed-control" disabled>
                                <option value="1">Marcar como destacado</option>
                                <option value="0">Quitar destacado</option>
                            </select>
                        </div>

                        <div class="ed-campo activo" data-campo="descuento">
                            <label class="form-label fw-semibold mb-1" for="edDescAccion">Descuento</label>
                            <select id="edDescAccion" class="form-select">
                                <option value="">No tocar el descuento</option>
                                <option value="activar">Activar descuento</option>
                                <option value="quitar">Quitar descuento</option>
                            </select>

                            <div id="edDescCampos" class="ed-desc-campos mt-2 d-none">
                                <div class="input-group mb-2">
                                    <span class="input-group-text">₡</span>
                                    <input type="number" min="1" step="1" id="edDescPrecio"
                                        class="form-control" placeholder="Precio con descuento">
                                </div>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label for="edDescInicio" class="form-label small mb-1">Desde</label>
                                        <input type="datetime-local" id="edDescInicio" class="form-control">
                                    </div>
                                    <div class="col-6">
                                        <label for="edDescFin" class="form-label small mb-1">Hasta</label>
                                        <input type="datetime-local" id="edDescFin" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="edAviso" class="alert alert-warning small mt-3 mb-0 d-none" role="alert"></div>
                        <div id="edError" class="alert alert-danger small mt-3 mb-0 d-none" role="alert"></div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" id="edCancelar" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" id="edAplicar" class="btn btn-dark d-inline-flex align-items-center gap-2"
                            disabled>
                            <span id="edSpinner" class="spinner-border spinner-border-sm d-none"
                                aria-hidden="true"></span>
                            <i id="edIcono" class="bx bx-check"></i>
                            <span>Aplicar cambios</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─────────── Modal: Cargar imágenes en bloque ─────────── --}}
        <div class="modal fade" id="modalCargar" tabindex="-1" aria-labelledby="modalCargarTitulo" aria-hidden="true"
            data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title fw-bold" id="modalCargarTitulo">Cargar imágenes en bloque</h5>
                            <small class="text-muted" id="cgSubtitulo"></small>
                        </div>
                        <button type="button" class="btn-close" id="cgCerrarX" data-bs-dismiss="modal"
                            aria-label="Cerrar"></button>
                    </div>

                    <div class="modal-body">

                        {{-- Formulario (se oculta mientras se sube) --}}
                        <div id="cgFormulario">
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label for="cgPrecio" class="form-label fw-semibold">
                                        Precio para todo el lote <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">₡</span>
                                        <input type="number" min="1" step="1" id="cgPrecio"
                                            class="form-control" placeholder="20000">
                                    </div>
                                    <small class="text-muted">
                                        Se copia a cada producto creado. Después se puede editar 1, 10 o todos sin
                                        afectar a los demás.
                                    </small>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="cgStock" class="form-label fw-semibold">
                                        Stock por producto <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" min="0" step="1" id="cgStock"
                                        class="form-control" value="1">
                                    <small class="text-muted">
                                        Con stock 0 el producto queda “agotado” de inmediato.
                                    </small>
                                </div>
                            </div>

                            <label class="form-label fw-semibold mt-3 mb-1">Imágenes (una imagen = un producto)</label>

                            <div id="cgZona" class="cg-zona" role="button" tabindex="0"
                                aria-label="Arrastrá las imágenes acá o hacé clic para elegirlas">
                                <i class="bx bx-cloud-upload"></i>
                                <div>
                                    <strong>Arrastrá las imágenes acá</strong> o hacé clic para elegirlas
                                </div>
                                <small class="text-muted">JPG, PNG o WEBP · hasta 8 MB cada una · máximo 100 por
                                    carga</small>
                                <input type="file" id="cgArchivo" class="d-none" multiple
                                    accept="image/jpeg,image/png,image/webp">
                            </div>

                            <div id="cgAviso" class="alert alert-warning small mt-3 mb-0 d-none" role="alert"></div>

                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-3">
                                <small class="text-muted" id="cgResumen"></small>
                                <a href="#" id="cgQuitarTodas" class="small d-none">quitar todas</a>
                            </div>
                            <div id="cgLista" class="cg-lista"></div>

                            <div id="cgError" class="alert alert-danger small mt-3 mb-0 d-none" role="alert"></div>
                        </div>

                        {{-- Progreso (se muestra mientras se sube) --}}
                        <div id="cgProgreso" class="d-none py-3">
                            <div class="d-flex justify-content-between small mb-2">
                                <span id="cgProgresoTexto">Preparando…</span>
                                <span id="cgProgresoPct">0%</span>
                            </div>
                            <div class="progress" style="height: 12px">
                                <div id="cgBarra" class="progress-bar" role="progressbar" style="width: 0%"
                                    aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"></div>
                            </div>
                            <small class="text-muted d-block mt-3">
                                No cierres esta ventana hasta que termine. Si la detenés, lo que ya se creó se conserva.
                            </small>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" id="cgCancelar" class="btn btn-outline-secondary">Cancelar</button>
                        <button type="button" id="cgCrear"
                            class="btn btn-nuevo d-inline-flex align-items-center gap-2" disabled>
                            <span id="cgSpinner" class="spinner-border spinner-border-sm d-none"
                                aria-hidden="true"></span>
                            <i class="bx bx-check"></i>
                            <span id="cgCrearTexto">Crear productos</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>


    </div>

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/modules/productos_masivo.js') }}"></script>
@endpush
