{{-- resources/views/admin/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.layouts.head')
</head>

<body


@if(session('success')) data-flash-success="{{ session('success') }}" @endif
    @if(session('error')) data-flash-error="{{ session('error') }}" @endif>

    <div class="wrapper">
        {{-- Sidebar / Menu --}}
        @include('admin.layouts.sidebar')

        {{-- Header / Topbar --}}
        @include('admin.layouts.header')

        {{-- Contenido dinámico --}}
        <div class="page-wrapper">
            <div class="page-content">
                @yield('content')
            </div>
        </div>

        {{-- Footer --}}
        @include('admin.layouts.footer')
    </div>

    @include('admin.layouts.scripts')
     {{--  Modal global para eliminar --}}
    @include('admin.partials.modal-eliminar')
</body>
</html>
