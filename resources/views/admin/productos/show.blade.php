{{-- resources/views/admin/productos/show.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Detalle del Producto')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/modules/productos.css') }}">

    {{-- BREADCRUMB --}}
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
                    <li class="breadcrumb-item active">Detalle</li>
                </ol>
            </nav>
        </div>
    </div>

    @php
        $imagenes = $item->imagenes
            ->map(function ($img) {
                $ruta = $img->ruta
                    ? (\Illuminate\Support\Str::startsWith($img->ruta, ['http://', 'https://'])
                        ? $img->ruta
                        : asset('storage/' . $img->ruta))
                    : 'https://via.placeholder.com/1200x900?text=Producto';

                return (object) [
                    'id_imagen_producto' => $img->id_imagen_producto,
                    'ruta' => $ruta,
                    'es_principal' => (bool) $img->es_principal,
                    'orden' => $img->orden,
                ];
            })
            ->values();

        $principal = $imagenes->firstWhere('es_principal', true) ?? $imagenes->first();

        $rutaPrincipal = $principal?->ruta ?? 'https://via.placeholder.com/1200x900?text=Producto';
        $indicePrincipal = $principal
            ? $imagenes->search(fn($img) => $img->id_imagen_producto === $principal->id_imagen_producto)
            : 0;

        $badgeColor = $item->activo ? 'success' : 'danger';
        $badgeLabel = $item->activo ? 'Activo' : 'Inactivo';
    @endphp



    {{-- CARD PRINCIPAL --}}
    <div class="card card-form">
        <div class="card-body">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
                <div>
                    <h4 class="fw-bold text-uppercase mb-1">{{ $item->nombre }}</h4>
                    <small class="text-muted">
                        <i class="bx bx-barcode"></i> SKU: {{ $item->sku ?: '—' }} |
                        <i class="bx bx-purchase-tag"></i> Código: {{ $item->codigo ?: '—' }}
                    </small>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('admin.productos.index') }}" class="btn btn-secondary-custom btn-back">
                        <i class="bx bx-arrow-back"></i>
                        <span class="btn-text">Volver</span>
                    </a>
                </div>
            </div>

            <hr>

            {{-- ========== GALERÍA PROFESIONAL CON MINIATURAS CUADRADAS ========== --}}

            <div class="card product-gallery-card bg-light mb-4">
                <div class="card-body p-3 p-md-4">

                    <div class="product-gallery-toolbar">
                        <div>
                            <h5 class="fw-bold mb-1 d-flex align-items-center gap-2">
                                <i class="bx bx-photo-album text-primary fs-4"></i>
                                Galería del Producto
                            </h5>
                            <small class="text-muted">
                                Visualización detallada de las imágenes registradas
                            </small>
                        </div>

                        <div class="product-gallery-meta">
                            <span class="product-gallery-chip">
                                <i class="bx bx-images"></i>
                                {{ $imagenes->count() }} {{ $imagenes->count() === 1 ? 'imagen' : 'imágenes' }}
                            </span>

                            @if ($principal)
                                <span class="product-gallery-chip">
                                    <i class="bx bx-star"></i>
                                    Principal definida
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="product-gallery-stage mb-3">
                        <span class="badge bg-primary product-gallery-badge">
                            <i class="bx bx-star me-1"></i> Principal
                        </span>

                        <div class="product-gallery-counter" id="galleryCounter">
                            {{ $imagenes->count() > 0 ? $indicePrincipal + 1 : 0 }}/{{ $imagenes->count() }}
                        </div>

                        <button type="button" class="product-gallery-nav prev" id="galleryPrev"
                            aria-label="Imagen anterior">
                            <i class="bx bx-chevron-left fs-4"></i>
                        </button>

                        <button type="button" class="product-gallery-nav next" id="galleryNext"
                            aria-label="Imagen siguiente">
                            <i class="bx bx-chevron-right fs-4"></i>
                        </button>

                        <div class="product-gallery-main-wrap">
                            <img id="galleryMainImage" src="{{ $rutaPrincipal }}" class="product-gallery-main"
                                alt="Imagen principal del producto">
                        </div>
                    </div>

                    @if ($imagenes->count() > 0)
                        <div class="product-gallery-thumbs" id="galleryThumbs">
                            @foreach ($imagenes as $index => $img)
                                <button type="button"
                                    class="product-thumb {{ $index === $indicePrincipal ? 'active' : '' }}"
                                    data-index="{{ $index }}" data-image="{{ $img->ruta }}"
                                    data-principal="{{ $img->es_principal ? '1' : '0' }}"
                                    aria-label="Ver imagen {{ $index + 1 }}">
                                    <img src="{{ $img->ruta }}" alt="Miniatura {{ $index + 1 }}">

                                    @if ($img->es_principal)
                                        <span class="product-thumb-principal">
                                            <i class="bx bx-star fs-10 me-1"></i> Principal
                                        </span>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-light border text-center mb-0">
                            <i class="bx bx-image fs-4 d-block mb-2 text-muted"></i>
                            <span class="text-muted">Este producto no tiene imágenes registradas.</span>
                        </div>
                    @endif

                </div>
            </div>
            






<div class="row g-4">

    {{-- COLUMNA IZQUIERDA --}}
    <div class="col-lg-7">



{{-- RESUMEN PRINCIPAL --}}
<div class="card border-0 bg-light product-section-card mb-4">
    <div class="card-body p-4">
        <label class="section-title mb-4 d-flex align-items-center gap-2">
            <i class="bx bx-package fs-5 text-primary"></i>
            <span>Resumen del Producto</span>
        </label>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="info-stack">

                    <div class="info-row">
                        <span class="info-label">Nombre del producto</span>
                        <span class="info-main-value">{{ $item->nombre }}</span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Estado general</span>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <span class="estado-badge bg-{{ $badgeColor }} text-white">
                                <i class="bx {{ $item->activo ? 'bx-check-circle' : 'bx-x-circle' }} me-1"></i>
                                {{ $badgeLabel }}
                            </span>

                            @if ($item->stock_actual <= 5 && $item->stock_actual > 0)
                                <span class="soft-status-text text-warning">
                                    <i class="bx bx-error-circle me-1"></i>Stock bajo
                                </span>
                            @elseif ($item->stock_actual == 0)
                                <span class="soft-status-text text-danger">
                                    <i class="bx bx-x-circle me-1"></i>Agotado
                                </span>
                            @else
                                <span class="soft-status-text text-success">
                                    <i class="bx bx-check-circle me-1"></i>Disponible
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <span class="info-label">ID Producto</span>
                                <div class="summary-data-box">
                                    {{ $item->id_producto }}
                                </div>
                            </div>

                            <div class="col-md-4">
                                <span class="info-label">SKU</span>
                                <div class="summary-data-box">
                                    {{ $item->sku ?: '—' }}
                                </div>
                            </div>

                            <div class="col-md-4">
                                <span class="info-label">Stock</span>
                                <div class="summary-data-box">
                                    {{ number_format((int) $item->stock_actual) }} unidades
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="col-lg-4">
                <span class="info-label">Precio del producto</span>

                <div class="summary-price-box">
                    ₡{{ number_format((float) $item->precio, 0) }}
                </div>

                @if ($item->precio > 500000)
                    <div class="mt-2">
                        <small class="text-success fw-semibold d-inline-flex align-items-center gap-1">
                            <i class="bx bx-trending-up"></i>
                            Producto de alta gama
                        </small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>





        {{-- DESCRIPCIÓN --}}
        <div class="card border-0 bg-light product-section-card mb-4">
            <div class="card-body p-4">
                <label class="section-title mb-3 d-flex align-items-center gap-2">
                    <i class="bx bx-file-blank fs-5 text-primary"></i>
                    <span>Descripción del Producto</span>
                </label>

                <div class="description-box">
                    {{ $item->descripcion ?: 'Sin descripción registrada para este producto.' }}
                </div>
            </div>
        </div>

        {{-- MARCA Y CATEGORÍAS --}}
        <div class="card border-0 bg-light product-section-card">
            <div class="card-body p-4">
                <label class="section-title mb-4 d-flex align-items-center gap-2">
                    <i class="bx bx-category-alt fs-5 text-primary"></i>
                    <span>Clasificación del Producto</span>
                </label>

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="info-block">
                            <small class="info-label">Marca</small>
                            <div class="info-value d-flex align-items-start gap-2">
                                <i class="bx bx-building fs-4 text-primary"></i>
                                <div>
                                    <div class="fw-semibold">{{ $item->marca?->nombre ?: 'Sin marca' }}</div>
                                    <small class="text-muted">
                                        Slug: {{ $item->marca?->slug ?: '—' }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-block">
                            <small class="info-label">Categoría Principal</small>
                            <div class="info-value d-flex align-items-start gap-2">
                                <i class="bx bx-category fs-4 text-primary"></i>
                                <div class="fw-semibold">
                                    {{ $item->categoriaPrincipal?->nombre ?: 'Sin categoría principal' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <small class="info-label d-block mb-2">Categorías Adicionales</small>

                    @if ($item->categorias->count() > 0)
                        <div class="category-list">
                            @foreach ($item->categorias as $cat)
                                <div class="category-row">
                                    <i class="bx bx-category-alt"></i>
                                    <span>{{ $cat->nombre }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-soft-box">
                            Sin categorías adicionales
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- COLUMNA DERECHA --}}
    <div class="col-lg-5">

        {{-- INVENTARIO --}}
        <div class="card border-0 bg-light product-section-card mb-4">
            <div class="card-body p-4">
                <label class="section-title mb-4 d-flex align-items-center gap-2">
                    <i class="bx bx-box fs-5 text-primary"></i>
                    <span>Inventario</span>
                </label>

                <div class="info-stack">
                    <div class="info-row">
                        <span class="info-label">Stock Actual</span>
                        <span class="info-main-value">{{ number_format((int) $item->stock_actual) }} unidades</span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">SKU</span>
                        <span class="fw-semibold">{{ $item->sku ?: '—' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- IDENTIFICADORES --}}
        <div class="card border-0 bg-light product-section-card mb-4">
            <div class="card-body p-4">
                <label class="section-title mb-4 d-flex align-items-center gap-2">
                    <i class="bx bx-barcode fs-5 text-primary"></i>
                    <span>Identificadores</span>
                </label>

                <div class="info-stack">
                    <div class="info-row">
                        <span class="info-label">Código de Barras</span>
                        <span class="fw-semibold font-monospace">{{ $item->codigo ?: '—' }}</span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Slug (URL)</span>
                        <span class="text-muted small text-break">{{ $item->slug }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- FECHAS --}}
        <div class="card border-0 bg-light product-section-card">
            <div class="card-body p-4">
                <label class="section-title mb-4 d-flex align-items-center gap-2">
                    <i class="bx bx-time-five fs-5 text-primary"></i>
                    <span>Trazabilidad</span>
                </label>

                <div class="info-stack">
                    <div class="info-row">
                        <span class="info-label">Fecha de creación</span>
                        <span class="fw-semibold">
                            {{ optional($item->created_at)->format('d/m/Y H:i:s') }}
                        </span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Última actualización</span>
                        <span class="fw-semibold">
                            {{ optional($item->updated_at)->format('d/m/Y H:i:s') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>





            {{-- PRODUCTOS RELACIONADOS --}}
            @if ($item->relacionados->count() > 0)
                <div class="card border-0 bg-light mt-4">
                    <div class="card-body">
                        <label class="fw-semibold mb-3 d-block">
                            <i class="bx bx-link-alt me-1"></i> Productos Relacionados
                            <small class="text-muted">({{ $item->relacionados->count() }} productos sugeridos)</small>
                        </label>

                        <div class="row g-3">
                            @foreach ($item->relacionados as $rel)
                                @php
                                    $imagenRel = $rel->imagenPrincipal?->ruta
                                        ? (\Illuminate\Support\Str::startsWith($rel->imagenPrincipal->ruta, [
                                            'http://',
                                            'https://',
                                        ])
                                            ? $rel->imagenPrincipal->ruta
                                            : asset('storage/' . $rel->imagenPrincipal->ruta))
                                        : 'https://via.placeholder.com/300x300?text=Producto';
                                @endphp

                                <div class="col-md-4">
                                    <div class="card product-related-card h-100 shadow-sm">
                                        <div class="row g-0 h-100">
                                            <div class="col-4">
                                                <img src="{{ $imagenRel }}" class="product-related-image"
                                                    alt="{{ $rel->nombre }}">
                                            </div>
                                            <div class="col-8">
                                                <div class="card-body p-3">
                                                    <h6 class="card-title fw-bold mb-1 text-truncate">
                                                        {{ $rel->nombre }}
                                                    </h6>
                                                    <p class="card-text small text-muted mb-1">
                                                        SKU: {{ $rel->sku ?: '—' }}
                                                    </p>
                                                    <p class="card-text fw-bold text-primary mb-2">
                                                        ₡{{ number_format((float) $rel->precio, 0) }}
                                                    </p>
                                                    <a href="{{ route('admin.productos.show', $rel->id_producto) }}"
                                                        class="btn btn-sm btn-outline-primary">
                                                        Ver detalle
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- BOTONES DE ACCIÓN --}}

            <div class="d-flex justify-content-end align-items-center gap-3 mt-4 flex-wrap">

                <a href="{{ route('admin.productos.edit', $item->id_producto) }}" class="btn btn-primary-custom">
                    <i class="bx bx-edit"></i>
                    <span>Editar</span>
                </a>

                <form action="{{ route('admin.productos.destroy', $item->id_producto) }}" method="POST"
                    style="display:inline;">
                    @csrf
                    @method('DELETE')

                    <button type="button" class="btn btn-danger-custom btn-delete-modal" data-clave="Producto"
                        data-valor="{{ $item->nombre }}">
                        <i class="bx bx-trash"></i>
                        <span>Eliminar</span>
                    </button>
                </form>

            </div>
        </div>
    </div>

    {{-- MODAL IMAGEN --}}
    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold text-white">Vista ampliada</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body text-center">
                    <img id="modalImage" src="" class="img-fluid" alt="Imagen ampliada del producto">
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const thumbs = Array.from(document.querySelectorAll('.product-thumb'));
            const mainImage = document.getElementById('galleryMainImage');
            const counter = document.getElementById('galleryCounter');
            const prevBtn = document.getElementById('galleryPrev');
            const nextBtn = document.getElementById('galleryNext');
            const modalElement = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');

            if (!mainImage || thumbs.length === 0) {
                if (prevBtn) prevBtn.disabled = true;
                if (nextBtn) nextBtn.disabled = true;
                return;
            }

            const modalInstance = modalElement ? new bootstrap.Modal(modalElement) : null;

            const images = thumbs.map((thumb, index) => ({
                index,
                url: thumb.dataset.image,
                principal: thumb.dataset.principal === '1',
                thumb
            }));

            let currentIndex = images.findIndex(img => img.thumb.classList.contains('active'));
            if (currentIndex < 0) currentIndex = 0;

            function updateButtons() {
                const multiple = images.length > 1;
                if (prevBtn) prevBtn.disabled = !multiple;
                if (nextBtn) nextBtn.disabled = !multiple;
            }

            function updateCounter() {
                if (counter) {
                    counter.textContent = `${currentIndex + 1}/${images.length}`;
                }
            }

            function updateActiveThumb() {
                images.forEach(img => img.thumb.classList.remove('active'));
                images[currentIndex].thumb.classList.add('active');
            }

            function swapMainImage(newUrl) {
                mainImage.classList.add('is-changing');
                setTimeout(() => {
                    mainImage.src = newUrl;
                    setTimeout(() => {
                        mainImage.classList.remove('is-changing');
                    }, 60);
                }, 140);
            }

            function goToImage(index) {
                if (index < 0) index = images.length - 1;
                if (index >= images.length) index = 0;
                currentIndex = index;
                swapMainImage(images[currentIndex].url);
                updateActiveThumb();
                updateCounter();
            }

            thumbs.forEach((thumb, index) => {
                thumb.addEventListener('click', function() {
                    goToImage(index);
                });
            });

            if (prevBtn) {
                prevBtn.addEventListener('click', function() {
                    goToImage(currentIndex - 1);
                });
            }

            if (nextBtn) {
                nextBtn.addEventListener('click', function() {
                    goToImage(currentIndex + 1);
                });
            }

            mainImage.addEventListener('click', function() {
                if (!modalInstance || !modalImage) return;
                modalImage.src = images[currentIndex].url;
                modalInstance.show();
            });

            document.addEventListener('keydown', function(event) {
                const modalOpen = modalElement && modalElement.classList.contains('show');
                if (modalOpen) return;
                if (event.key === 'ArrowLeft') goToImage(currentIndex - 1);
                if (event.key === 'ArrowRight') goToImage(currentIndex + 1);
                if (event.key === 'Enter' && document.activeElement === mainImage) {
                    if (!modalInstance || !modalImage) return;
                    modalImage.src = images[currentIndex].url;
                    modalInstance.show();
                }
            });

            updateButtons();
            updateCounter();
            updateActiveThumb();
        });
    </script>
@endpush
