@php
    $placeholder = asset('assets/img/no-image.png');
    $carritoIds = $carritoIds ?? collect(session('carrito', []))->keys()->toArray();
@endphp

@foreach ($productos as $producto)
    @php
        $productoImagen = $producto->imagenPrincipal?->ruta
            ? asset('storage/' . $producto->imagenPrincipal->ruta)
            : $placeholder;

        $varianteBase = $producto->usa_variantes
            ? ($producto->variantePrincipal ?? $producto->variantesActivas->first())
            : null;

        if ($producto->usa_variantes && $varianteBase) {
            $cartKeyVariante = $producto->id_producto . '_v_' . $varianteBase->id_producto_variante;

            $productoEnCarrito = in_array($cartKeyVariante, $carritoIds);

            $precioNormal = (float) $varianteBase->precio;
            $precioVenta = (float) $varianteBase->precioVenta();
            $stockBase = (int) $varianteBase->stock_actual;
            $tienePromo = $varianteBase->promocionVigente();
        } else {
            $productoEnCarrito = in_array($producto->id_producto, $carritoIds);

            $precioNormal = (float) $producto->precio;
            $precioVenta = (float) $producto->precioVenta();
            $stockBase = (int) $producto->stock_actual;
            $tienePromo = $producto->tienePromocionActiva();
        }

        $agotado = $stockBase <= 0;

        $ahorro = $tienePromo
            ? max(0, $precioNormal - $precioVenta)
            : 0;

        $porcentaje = ($tienePromo && $precioNormal > 0)
            ? round(($ahorro / $precioNormal) * 100)
            : 0;
    @endphp

    <div class="store-home-slide">
        <div class="store-product-card">

            <a href="{{ route('tienda.productos.show', $producto->slug) }}" class="store-product-image-wrap">
                <img src="{{ $productoImagen }}" alt="{{ $producto->nombre }}" class="store-product-image">

                <button type="button"
                    class="store-product-heart js-favorite-btn {{ in_array($producto->id_producto, $favoritosIds ?? []) ? 'is-active' : '' }}"
                    data-url="{{ route('tienda.favoritos.toggle', $producto->id_producto) }}">
                    <i class="bi {{ in_array($producto->id_producto, $favoritosIds ?? []) ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                </button>

                @if ($agotado)
                    <span class="store-product-badge store-product-badge-muted">
                        Agotado
                    </span>
                @endif
            </a>

            <div class="store-product-body">
                <div class="store-product-meta">
                    {{ $producto->marca?->nombre ?? 'Sin marca' }}
                </div>

                <a href="{{ route('tienda.productos.show', $producto->slug) }}" class="store-product-name">
                    {{ $producto->nombre }}
                </a>

                <div class="store-product-category">
                    {{ $producto->categoriaPrincipal?->nombre ?? 'Sin categoría' }}
                </div>

                <div class="store-product-footer store-product-footer-catalog">
                    <div class="store-product-price-area">
                        @if ($tienePromo)
                            <div class="mb-1">
                                <span class="badge bg-danger text-white">
                                    -{{ $porcentaje }}% OFF
                                </span>
                            </div>

                            <div class="text-muted text-decoration-line-through small">
                                ₡{{ number_format($precioNormal, 2) }}
                            </div>

                            <div class="store-product-price text-danger">
                                ₡{{ number_format($precioVenta, 2) }}
                            </div>
                        @else
                            <div class="store-product-price">
                                ₡{{ number_format($precioVenta, 2) }}
                            </div>
                        @endif

                        <small class="store-product-stock">
                            Stock: {{ $stockBase }}
                        </small>
                    </div>

                    <div class="store-product-card-actions">
                        @if ($producto->usa_variantes)
                            <button type="button"
                                class="store-product-action store-product-action-catalog js-add-cart {{ $productoEnCarrito ? 'is-added' : '' }}"
                                data-url="{{ route('tienda.carrito.agregar', $producto->id_producto) }}"
                                data-product-id="{{ $producto->id_producto }}"
                                data-product-variant-id="{{ $varianteBase?->id_producto_variante }}"
                                data-cart-key="{{ $cartKeyVariante ?? '' }}"
                                title="{{ $productoEnCarrito ? 'Producto agregado' : 'Agregar al carrito' }}"
                                {{ $agotado || !$varianteBase || $productoEnCarrito ? 'disabled' : '' }}>

                                <i class="bi {{ $productoEnCarrito ? 'bi-check-lg' : 'bi-cart-plus' }}"></i>
                            </button>
                        @else
                            <button type="button"
                                class="store-product-action store-product-action-catalog js-add-cart {{ $productoEnCarrito ? 'is-added' : '' }}"
                                data-url="{{ route('tienda.carrito.agregar', $producto->id_producto) }}"
                                data-product-id="{{ $producto->id_producto }}"
                                data-cart-key="{{ $producto->id_producto }}"
                                title="{{ $productoEnCarrito ? 'Producto agregado' : 'Agregar al carrito' }}"
                                {{ $agotado || $productoEnCarrito ? 'disabled' : '' }}>

                                <i class="bi {{ $productoEnCarrito ? 'bi-check-lg' : 'bi-cart-plus' }}"></i>
                            </button>
                        @endif

                        <a href="{{ route('tienda.productos.show', $producto->slug) }}"
                            class="store-product-action store-product-action-catalog"
                            title="Ver producto">
                            <i class="bi bi-eye"></i>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endforeach