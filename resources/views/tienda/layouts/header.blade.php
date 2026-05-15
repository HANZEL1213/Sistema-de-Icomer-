@php
    use App\Models\Favorito;

    $cantidadCarrito = collect(session('carrito', []))->sum('cantidad');

    $cantidadFavoritos = Favorito::where(function ($query) {
        if (auth()->check()) {
            $query->where('id_usuario', auth()->id());
        } else {
            $query->where('session_id', session()->getId());
        }
    })->count();
@endphp

<header>
    {{-- TOP BAR --}}
    <div class="store-topbar d-none d-md-block">
        <div class="container">
            <div class="store-topbar-wrap d-flex align-items-center justify-content-between gap-3 flex-wrap">
                <div class="store-topbar-left">
                    <span class="store-topbar-item">
                        <i class="bi bi-truck me-1"></i>
                        Envíos disponibles
                    </span>

                    <span class="store-topbar-item">
                        <i class="bi bi-shield-check me-1"></i>
                        Compra segura
                    </span>

                    <span class="store-topbar-item">
                        <i class="bi bi-patch-check me-1"></i>
                        Experiencia confiable
                    </span>
                </div>

                <div class="store-topbar-right">
                    <a href="{{ route('tienda.pedidos.mis') }}" class="store-topbar-link">
                        Mis pedidos
                    </a>

                    <a href="{{ route('tienda.auth.login') }}" class="store-topbar-link">
                        Mi cuenta
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- HEADER PRINCIPAL --}}
    <div class="store-header-main sticky-blur-header">
        <div class="container">
            <div class="store-header-main-inner d-flex align-items-center justify-content-between gap-3">

                {{-- IZQUIERDA --}}
                <div class="d-flex align-items-center gap-2 gap-md-3">
                    <button class="store-icon-btn d-lg-none" type="button" data-bs-toggle="offcanvas"
                        data-bs-target="#mobileStoreMenu" aria-controls="mobileStoreMenu" aria-label="Abrir menú">
                        <i class="bi bi-list fs-5"></i>
                    </button>

                    <a href="{{ route('tienda.home') }}" class="store-logo-link">
                        <div class="store-logo-box">
                            T
                        </div>

                        <div class="store-logo-text d-none d-sm-flex">
                            <span class="store-logo-title">Mi Tienda</span>
                            <span class="store-logo-subtitle">E-commerce premium</span>
                        </div>
                    </a>
                </div>

                {{-- BUSCADOR DESKTOP --}}
                <div class="store-header-search-wrap flex-grow-1 d-none d-lg-block"
                    data-search-url="{{ route('tienda.productos.sugerencias') }}">

                    <form action="{{ route('tienda.productos.index') }}" method="GET" autocomplete="off">

                        <div class="input-group store-search-group">

                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>

                            <input type="text" name="q" class="form-control store-live-search-input"
                                value="{{ request('q') }}" placeholder="Buscar productos, categorías o marcas...">

                            <button class="btn btn-store-primary" type="submit">
                                Buscar
                            </button>

                        </div>

                        <div class="store-search-suggestions"></div>

                    </form>

                </div>

                {{-- DERECHA --}}
                <div class="d-flex align-items-center gap-2 gap-md-3">
                  

                    <a href="{{ route('tienda.pedidos.mis') }}" class="store-icon-btn d-none d-md-inline-flex"
                        aria-label="Mis pedidos">
                        <i class="bi bi-box-seam fs-5"></i>
                    </a>

                    <a href="{{ route('tienda.auth.login') }}" class="store-icon-btn d-none d-md-inline-flex"
                        aria-label="Mi cuenta">
                        <i class="bi bi-person fs-5"></i>
                    </a>

                    <a href="{{ route('tienda.favoritos.index') }}"
                        class="store-icon-btn d-none d-md-inline-flex position-relative" aria-label="Favoritos">
                        <i class="bi bi-heart fs-5"></i>

                      <span class="store-cart-badge js-favorites-count"
      style="{{ $cantidadFavoritos > 0 ? '' : 'display: none;' }}">
    {{ $cantidadFavoritos }}
</span>
                    </a>

                {{-- FAVORITOS MOBILE --}}
<a href="{{ route('tienda.favoritos.index') }}"
   class="store-icon-btn d-inline-flex d-md-none position-relative"
   aria-label="Favoritos">

    <i class="bi bi-heart fs-5"></i>

    <span class="store-cart-badge js-favorites-count"
          style="{{ $cantidadFavoritos > 0 ? '' : 'display: none;' }}">

        {{ $cantidadFavoritos }}

    </span>

</a>
                </div>
            </div>

            {{-- BUSCADOR MOBILE --}}
            <div class="store-mobile-search-wrap d-lg-none">

                <div class="store-mobile-search-card">

                    <div class="store-header-search-wrap"
                        data-search-url="{{ route('tienda.productos.sugerencias') }}">

                        <form action="{{ route('tienda.productos.index') }}" method="GET" autocomplete="off">

                            <div class="input-group store-search-group">

                                <span class="input-group-text">
                                    <i class="bi bi-search"></i>
                                </span>

                                <input type="text" name="q" class="form-control store-live-search-input"
                                    value="{{ request('q') }}" placeholder="Buscar productos...">

                                <button class="btn btn-store-primary" type="submit">

                                    <i class="bi bi-arrow-right"></i>

                                </button>

                            </div>

                            {{-- SUGERENCIAS --}}
                            <div class="store-search-suggestions"></div>

                        </form>

                    </div>

                </div>

            </div>
        </div>
    </div>

    {{-- NAV DESKTOP --}}
    <div class="store-desktop-nav d-none d-lg-block">
        <div class="container">
            <ul class="store-desktop-nav-list">
                <li>
                    <a href="{{ route('tienda.home') }}" class="store-desktop-nav-link">
                        Inicio
                    </a>
                </li>

                <li>
                    <a href="{{ route('tienda.productos.index') }}" class="store-desktop-nav-link">
                        Productos
                    </a>
                </li>

                <li>
                    <a href="{{ route('tienda.categorias.index') }}" class="store-desktop-nav-link">
                        Categorías
                    </a>
                </li>

                <li>
                    <a href="{{ route('tienda.marcas.index') }}" class="store-desktop-nav-link">
                        Marcas
                    </a>
                </li>

                <li>
                    <a href="{{ route('tienda.checkout.index') }}" class="store-desktop-nav-link">
                        Checkout
                    </a>
                </li>

                <li>
                    <a href="{{ route('tienda.pedidos.mis') }}" class="store-desktop-nav-link">
                        Mis pedidos
                    </a>
                </li>
            </ul>
        </div>
    </div>

    {{-- OFFCANVAS MOBILE --}}
    <div class="offcanvas offcanvas-start store-offcanvas" tabindex="-1" id="mobileStoreMenu"
        aria-labelledby="mobileStoreMenuLabel">

        <div class="offcanvas-header border-bottom">
            <div class="store-offcanvas-brand">
                <div class="store-logo-box">
                    T
                </div>

                <div class="store-logo-text d-flex">
                    <span class="store-logo-title" id="mobileStoreMenuLabel">
                        Mi Tienda
                    </span>

                    <span class="store-logo-subtitle">
                        Menú principal
                    </span>
                </div>
            </div>

            <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas" aria-label="Cerrar">
            </button>
        </div>

        <div class="offcanvas-body">
            <div class="store-offcanvas-menu">
                <a href="{{ route('tienda.home') }}" class="store-offcanvas-link" data-store-close-offcanvas="true">
                    <span>Inicio</span>
                    <i class="bi bi-house"></i>
                </a>

                <a href="{{ route('tienda.productos.index') }}" class="store-offcanvas-link"
                    data-store-close-offcanvas="true">
                    <span>Productos</span>
                    <i class="bi bi-grid"></i>
                </a>

                <a href="{{ route('tienda.categorias.index') }}" class="store-offcanvas-link"
                    data-store-close-offcanvas="true">
                    <span>Categorías</span>
                    <i class="bi bi-collection"></i>
                </a>

                <a href="{{ route('tienda.marcas.index') }}" class="store-offcanvas-link"
                    data-store-close-offcanvas="true">
                    <span>Marcas</span>
                    <i class="bi bi-bookmark-star"></i>
                </a>

                <a href="{{ route('tienda.favoritos.index') }}"
   class="store-offcanvas-link"
   data-store-close-offcanvas="true">

    <span>
        Favoritos
    </span>

    <span class="d-inline-flex align-items-center gap-2">

        <span class="store-offcanvas-count js-favorites-count"
              style="{{ $cantidadFavoritos > 0 ? '' : 'display: none;' }}">

            {{ $cantidadFavoritos }}

        </span>

        <i class="bi bi-heart"></i>

    </span>

</a>
                <a href="{{ route('tienda.checkout.index') }}" class="store-offcanvas-link"
                    data-store-close-offcanvas="true">
                    <span>Checkout</span>
                    <i class="bi bi-credit-card"></i>
                </a>
            </div>

            <div class="store-offcanvas-divider"></div>

            <div class="store-offcanvas-menu">
                <a href="{{ route('tienda.carrito.index') }}" class="store-offcanvas-link"
                    data-store-close-offcanvas="true">
                    <span>Carrito</span>

                    <span class="d-inline-flex align-items-center gap-2">
                        @if ($cantidadCarrito > 0)
                            <span class="store-offcanvas-count">
                                {{ $cantidadCarrito }}
                            </span>
                        @endif

                        <i class="bi bi-cart3"></i>
                    </span>
                </a>

                <a href="{{ route('tienda.pedidos.mis') }}" class="store-offcanvas-link"
                    data-store-close-offcanvas="true">
                    <span>Mis pedidos</span>
                    <i class="bi bi-box-seam"></i>
                </a>

                <a href="{{ route('tienda.auth.login') }}" class="store-offcanvas-link"
                    data-store-close-offcanvas="true">
                    <span>Mi cuenta</span>
                    <i class="bi bi-person"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- BOTTOM NAV MOBILE --}}
 <div class="store-mobile-bottom-nav d-lg-none">
    <div class="container px-2">

        <div class="store-mobile-bottom-nav-wrap">

            {{-- INICIO --}}
            <a href="{{ route('tienda.home') }}"
               class="store-mobile-bottom-link active">

                <i class="bi bi-house-door"></i>

                <span>
                    Inicio
                </span>

            </a>

            {{-- PRODUCTOS --}}
            <a href="{{ route('tienda.productos.index') }}"
               class="store-mobile-bottom-link">

                <i class="bi bi-grid"></i>

                <span>
                    Productos
                </span>

            </a>

            {{-- CARRITO CENTRO --}}
            <a href="{{ route('tienda.carrito.index') }}"
               class="store-mobile-bottom-link store-mobile-cart-center">

                <span class="store-mobile-cart-pill">

                    <i class="bi bi-cart3"></i>

                </span>

                @if ($cantidadCarrito > 0)

                    <span class="store-mobile-bottom-badge">
                        {{ $cantidadCarrito }}
                    </span>

                @endif

            </a>

            {{-- CATEGORÍAS --}}
            <a href="{{ route('tienda.categorias.index') }}"
               class="store-mobile-bottom-link">

                <i class="bi bi-collection"></i>

                <span>
                    Categorías
                </span>

            </a>

            {{-- PEDIDOS --}}
            <a href="{{ route('tienda.pedidos.mis') }}"
               class="store-mobile-bottom-link">

                <i class="bi bi-box-seam"></i>

                <span>
                    Pedidos
                </span>

            </a>

        </div>

    </div>
</div>
</header>
