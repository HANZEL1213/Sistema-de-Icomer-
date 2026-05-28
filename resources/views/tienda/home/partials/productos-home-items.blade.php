@php
    $placeholder = asset('assets/img/no-image.png');
@endphp

@foreach ($productos as $producto)
    @php
        $productoImagen = $producto->imagenPrincipal?->ruta
            ? asset('storage/' . $producto->imagenPrincipal->ruta)
            : $placeholder;
    @endphp

    <div class="store-home-slide">

        <div class="store-product-card">

            <a href="{{ route('tienda.productos.show', $producto->slug) }}"
                class="store-product-image-wrap">

                <img src="{{ $productoImagen }}" alt="{{ $producto->nombre }}"
                    class="store-product-image">

                <button type="button"
                    class="store-product-heart js-favorite-btn {{ in_array($producto->id_producto, $favoritosIds ?? []) ? 'is-active' : '' }}"
                    data-url="{{ route('tienda.favoritos.toggle', $producto->id_producto) }}">

                    <i class="bi {{ in_array($producto->id_producto, $favoritosIds ?? []) ? 'bi-heart-fill' : 'bi-heart' }}"></i>

                </button>

            </a>

            <div class="store-product-body">

                <div class="store-product-meta">
                    {{ $producto->marca?->nombre ?? 'Sin marca' }}
                </div>

                <a href="{{ route('tienda.productos.show', $producto->slug) }}"
                    class="store-product-name">
                    {{ $producto->nombre }}
                </a>

                <div class="store-product-category">
                    {{ $producto->categoriaPrincipal?->nombre ?? 'Sin categoría' }}
                </div>

                <div class="store-product-footer">

                    <div>
                        <div class="store-product-price">
                            ₡{{ number_format($producto->precio, 2) }}
                        </div>

                        <small class="store-product-stock">
                            Stock: {{ $producto->stock_actual }}
                        </small>
                    </div>

                    <a href="{{ route('tienda.productos.show', $producto->slug) }}"
                        class="store-product-action">
                        <i class="bi bi-eye"></i>
                    </a>

                </div>

            </div>

        </div>

    </div>
@endforeach