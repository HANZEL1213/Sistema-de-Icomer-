@extends('tienda.layouts.app')

@section('title', 'Registro | Tienda')

@section('content')

    <main class="store-auth-page">

        <section class="store-auth-card">

            @if ($errors->any())
                <div class="alert alert-danger d-flex justify-content-between align-items-start rounded-4 small mb-3">
                    <span>{{ $errors->first() }}</span>

                    <button type="button" class="btn-close ms-2" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            @endif

            <a href="{{ route('tienda.auth.login') }}" class="store-auth-back">
                <i class='bx bx-chevron-left'></i>
                Volver
            </a>

            <form method="POST" action="{{ route('tienda.auth.register.post') }}">
                @csrf

                <h1 class="store-auth-title">Crear cuenta</h1>
                <p class="store-auth-text">Solo necesitamos estos datos para empezar.</p>

                <div class="store-auth-field">
                    <label for="register_nombre">
                        Nombre de Usuario <span class="text-danger">*</span>
                    </label>

                    <div class="store-auth-input">
                        <i class='bx bx-user'></i>

                        <input type="text" id="register_nombre" name="nombre" value="{{ old('nombre') }}"
                            placeholder="Tu nombre" required>
                    </div>
                </div>

                <div class="store-auth-field">
                    <label for="register_correo">
                        Correo electrónico <span class="text-danger">*</span>
                    </label>

                    <div class="store-auth-input">
                        <i class='bx bx-envelope'></i>

                        <input type="email" id="register_correo" name="correo" value="{{ old('correo') }}"
                            placeholder="correo@ejemplo.com" required>
                    </div>
                </div>

                <div class="store-auth-field">
                    <label for="register_password">
                        Contraseña <span class="text-danger">*</span>
                    </label>

                    <div class="store-auth-input">
                        <i class='bx bx-lock-alt'></i>

                        <input type="password" id="register_password" name="password" placeholder="Crea una contraseña"
                            required>

                        <button type="button" class="store-auth-password-toggle">
                            <i class='bx bx-show'></i>
                        </button>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-center">
                        <div
                            class="g-recaptcha"
                            data-sitekey="{{ config('services.recaptcha.site_key') }}">
                        </div>
                    </div>

                    @error('g-recaptcha-response')
                        <small class="text-danger d-block text-center mt-2">
                            {{ $message }}
                        </small>
                    @enderror
                </div>

                <button type="submit" class="store-auth-submit">
                    Crear cuenta
                </button>

                <div class="store-auth-divider">
                    <span>o regístrate con</span>
                </div>

                <div class="store-auth-social">
                    <a href="{{ route('tienda.auth.google.redirect') }}" class="store-auth-social-btn">
                        <i class='bx bxl-google'></i>
                        Continuar con Google
                    </a>
                </div>

                <p class="store-auth-register">
                    ¿Ya tienes cuenta?

                    <a href="{{ route('tienda.auth.login') }}">
                        Iniciar sesión
                    </a>
                </p>

            </form>

        </section>

    </main>

    <script src="{{ asset('assets/js/login.js') }}"></script>

@endsection
