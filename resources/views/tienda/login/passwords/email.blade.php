@extends('tienda.layouts.app')

@section('content')

<main class="store-auth-page">

    <section class="store-auth-card">

        <div class="store-auth-header">

            <div class="store-auth-logo">
                <i class="bi bi-shield-lock"></i>
            </div>

            <h1>Recuperar contraseña</h1>

            <p>
                Ingresa tu correo y te enviaremos un enlace seguro para crear una nueva contraseña.
            </p>

        </div>

        <form method="POST" action="{{ route('tienda.auth.password.email') }}">
            @csrf

            <div class="store-auth-field">
                <label for="correo">Correo electrónico</label>

                <div class="store-auth-input">
                    <i class="bi bi-envelope"></i>

                    <input type="email"
                           id="correo"
                           name="correo"
                           value="{{ old('correo') }}"
                           placeholder="correo@ejemplo.com"
                           required>
                </div>

                @error('correo')
                    <small class="store-auth-field-error">
                        {{ $message }}
                    </small>
                @enderror
            </div>

            <button type="submit" class="store-auth-submit">
                Enviar enlace
            </button>

        </form>

        <p class="store-auth-register">
            ¿Recordaste tu contraseña?
            <a href="{{ route('tienda.auth.login') }}">
                Iniciar sesión
            </a>
        </p>

    </section>

</main>

@endsection