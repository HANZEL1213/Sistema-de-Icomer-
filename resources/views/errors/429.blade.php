@extends('tienda.layouts.app')

@section('title', 'Demasiados intentos')

@section('content')

<main class="store-auth-page">

    <section class="store-auth-card text-center">

        <div class="store-auth-logo mx-auto">
            <i class="bi bi-hourglass-split"></i>
        </div>

        <h1 class="store-auth-title mt-3">
            Espera un momento
        </h1>

        <p class="store-auth-text">
            Has realizado demasiadas acciones en poco tiempo.
            Por seguridad, intenta nuevamente en unos minutos.
        </p>

        <a href="{{ route('tienda.home') }}"
           class="store-auth-submit d-inline-flex align-items-center justify-content-center text-decoration-none">
            Volver al inicio
        </a>

    </section>

</main>

@endsection