@extends('tienda.layouts.app')

@section('content')

<main class="store-auth-page">

    <section class="store-auth-card">

        <div class="store-auth-header">

            <div class="store-auth-logo">
                <i class="bi bi-key"></i>
            </div>

            <h1>Nueva contraseña</h1>

            <p>
                Crea una contraseña segura para recuperar el acceso a tu cuenta.
            </p>

        </div>

        <form method="POST" action="{{ route('tienda.auth.password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="store-auth-field">
                <label for="correo">Correo electrónico</label>

                <div class="store-auth-input">
                    <i class="bi bi-envelope"></i>

                    <input type="email"
                           id="correo"
                           name="correo"
                           value="{{ old('correo', $correo) }}"
                           placeholder="correo@ejemplo.com"
                           required>
                </div>

                @error('correo')
                    <small class="store-auth-field-error">
                        {{ $message }}
                    </small>
                @enderror
            </div>

            <div class="store-auth-field">
                <label for="password">Nueva contraseña</label>

                <div class="store-auth-input">
                    <i class="bi bi-lock"></i>

                    <input type="password"
                           id="password"
                           name="password"
                           placeholder="Mínimo 8 caracteres"
                           required>
                </div>

                @error('password')
                    <small class="store-auth-field-error">
                        {{ $message }}
                    </small>
                @enderror
            </div>

            <div class="store-auth-field">
                <label for="password_confirmation">Confirmar contraseña</label>

                <div class="store-auth-input">
                    <i class="bi bi-lock-fill"></i>

                    <input type="password"
                           id="password_confirmation"
                           name="password_confirmation"
                           placeholder="Repite tu contraseña"
                           required>
                </div>
            </div>

            <button type="submit" class="store-auth-submit">
                Cambiar contraseña
            </button>

        </form>

    </section>

</main>

@endsection