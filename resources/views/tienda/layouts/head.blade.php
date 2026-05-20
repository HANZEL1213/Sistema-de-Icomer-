<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes, viewport-fit=cover">

<meta name="theme-color" content="#111827">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

<title>@yield('title', 'Tienda')</title>
<meta name="description" content="@yield('meta_description', 'Tienda en línea')">

{{-- Bootstrap / Icons --}}
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

{{-- CSS TIENDA --}}
<link rel="stylesheet" href="{{ asset('assets/css/tienda.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/tienda-forms.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">

{{-- Swiper --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
@stack('styles')