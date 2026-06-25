@extends('tienda.layouts.app')

@section('title', 'Login | Tienda')

@section('content')

    <main class="store-auth-page">

        <section class="store-auth-card">

            @if ($errors->any())
                <div class="alert alert-danger d-flex justify-content-between align-items-start rounded-4 small mb-3">
                    <span>{{ $errors->first() }}</span>

                    <button type="button" class="btn-close ms-2" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            @elseif (session('correo_verificacion') || session('reenviar_verificacion'))
                @php
                    $correoVerificacion = session('reenviar_verificacion') ?? session('correo_verificacion');
                @endphp

                <div class="alert alert-success d-flex justify-content-between align-items-start rounded-4 small mb-3">

                    <div>
                        <strong>Cuenta creada correctamente.</strong>

                        <div class="mt-1">
                            Revisa el correo enviado a
                            <strong>{{ $correoVerificacion }}</strong>
                            para activar tu cuenta.
                        </div>

                        <form method="POST" action="{{ route('tienda.auth.email.resend') }}" class="mt-2">

                            @csrf

                            <input type="hidden" name="correo" value="{{ $correoVerificacion }}">

                            <button type="submit" class="btn btn-link btn-sm p-0 fw-semibold">
                                Reenviar correo de verificación
                            </button>

                        </form>
                    </div>

                    <button type="button" class="btn-close ms-2" data-bs-dismiss="alert" aria-label="Cerrar"></button>

                </div>
            @endif

            <form method="POST" action="{{ route('tienda.auth.login.post') }}">
                @csrf

                <h1 class="store-auth-title">Mi cuenta</h1>
                <p class="store-auth-text">Ingresa para consultar tus pedidos.</p>

                <div class="store-auth-field">
                    <label for="login_correo">
                        Correo electrónico <span class="text-danger">*</span>
                    </label>

                    <div class="store-auth-input">
                        <i class='bx bx-envelope'></i>

                        <input type="email" id="login_correo" name="correo" value="{{ old('correo') }}"
                            placeholder="correo@ejemplo.com" required>
                    </div>
                </div>

                <div class="store-auth-field">
                    <label for="login_password">
                        Contraseña <span class="text-danger">*</span>
                    </label>

                    <div class="store-auth-input">
                        <i class='bx bx-lock-alt'></i>

                        <input type="password" id="login_password" name="password" placeholder="Tu contraseña" required>

                        <button type="button" class="store-auth-password-toggle">
                            <i class='bx bx-show'></i>
                        </button>
                    </div>
                </div>

                <div class="store-auth-options">
                    <label>
                        <input type="checkbox" name="remember">
                        Recuérdame
                    </label>

                    <a href="{{ route('tienda.auth.password.forgot') }}">
                        Olvidé mi contraseña
                    </a>
                </div>

                <button type="submit" class="store-auth-submit">
                    Iniciar sesión
                </button>

                <div class="store-auth-divider">
                    <span>o continúa con</span>
                </div>

                <div class="store-auth-social">
                    <a href="{{ route('tienda.auth.google.redirect') }}" class="store-auth-social-btn">
                        <i class='bx bxl-google'></i>
                        Continuar con Google
                    </a>
                </div>

                <p class="store-auth-register">
                    ¿No tienes cuenta?

                    <a href="{{ route('tienda.auth.register') }}">
                        Crear una cuenta
                    </a>
                </p>

            </form>

        </section>

    </main>

@endsection
