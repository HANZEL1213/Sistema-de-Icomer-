@extends('tienda.layouts.app')

@section('title', 'Productos | Tienda')

@section('content')


    <main class="store-auth-page">

        <section class="store-auth-card">

            @if ($errors->any())
                <div class="store-auth-alert">
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- LOGIN --}}
            <form method="POST" action="{{ route('tienda.auth.login.post') }}" class="store-auth-form is-active"
                id="storeLoginForm">
                @csrf

                <h1 class="store-auth-title">Mi cuenta</h1>
                <p class="store-auth-text">Ingresa para consultar tus pedidos.</p>

                <div class="store-auth-field">
                    <label for="correo">Correo electrónico</label>
                    <div class="store-auth-input">
                        <i class='bx bx-envelope'></i>
                        <input type="email" id="correo" name="correo" placeholder="correo@ejemplo.com" required>
                    </div>
                </div>

                <div class="store-auth-field">
                    <label for="password">Contraseña</label>
                    <div class="store-auth-input">
                        <i class='bx bx-lock-alt'></i>
                        <input type="password" id="password" name="password" placeholder="Tu contraseña" required>

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

                    <a href="{{ route('tienda.auth.password.forgot') }}">Olvidé mi contraseña</a>
                </div>

                <button type="submit" class="store-auth-submit">
                    Iniciar sesión
                </button>

                <div class="store-auth-divider">
                    <span>o continúa con</span>
                </div>

                <div class="store-auth-social">
                    <a href="#" class="store-auth-social-btn">
                        <i class='bx bxl-google'></i>
                        Google
                    </a>

                    <a href="#" class="store-auth-social-btn">
                        <i class='bx bxl-facebook'></i>
                        Facebook
                    </a>
                </div>

                <p class="store-auth-register">
                    ¿No tienes cuenta?
                    <button type="button" class="store-auth-link" id="showRegisterForm">
                        Crear cuenta rápida
                    </button>
                </p>
            </form>

            {{-- REGISTRO RÁPIDO --}}
            <form method="POST" action="{{ route('tienda.auth.register.post') }}" class="store-auth-form" id="storeRegisterForm">
                @csrf

                <h1 class="store-auth-title">Crear cuenta</h1>
                <p class="store-auth-text">Solo necesitamos estos datos para empezar.</p>

                <div class="store-auth-field">
                    <label for="nombre">Nombre</label>
                    <div class="store-auth-input">
                        <i class='bx bx-user'></i>
                        <input type="text" id="nombre" name="nombre" placeholder="Tu nombre" required>
                    </div>
                </div>

                <div class="store-auth-field">
                    <label for="correo">Correo electrónico</label>
                    <div class="store-auth-input">
                        <i class='bx bx-envelope'></i>
                        <input type="email" id="correo" name="correo" placeholder="correo@ejemplo.com" required>
                    </div>
                </div>

                <div class="store-auth-field">
                    <label for="password">Contraseña</label>
                    <div class="store-auth-input">
                        <i class='bx bx-lock-alt'></i>
                        <input type="password" id="password" name="password" placeholder="Crea una contraseña" required>

                        <button type="button" class="store-auth-password-toggle">
                            <i class='bx bx-show'></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="store-auth-submit">
                    Crear cuenta
                </button>

                <p class="store-auth-register">
                    ¿Ya tienes cuenta?
                    <button type="button" class="store-auth-link" id="showLoginForm">
                        Iniciar sesión
                    </button>
                </p>
            </form>

        </section>

    </main>

    <script src="{{ asset('assets/js/login.js') }}"></script>

@endsection
