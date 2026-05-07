<!DOCTYPE html>
<html lang="es">

<head>
    @include('tienda.layouts.head')
</head>

<body>
    <div class="store-page-shell">

        @include('tienda.layouts.header')

        <main class="store-main store-mobile-safe-bottom">
            @yield('content')
        </main>

        @include('tienda.layouts.footer')

    </div>

    @include('tienda.layouts.scripts')

    @stack('scripts')
</body>

</html>