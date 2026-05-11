<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Administrativo</title>

    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">
</head>

<body>

<main class="admin-auth-page">

    <section class="admin-auth-card">

        <div class="admin-auth-header">
            <div class="admin-auth-logo">
                <i class='bx bx-lock-alt'></i>
            </div>

            <h1>Panel Administrativo</h1>
            <p>Ingresa tus credenciales para continuar.</p>
        </div>

        @if ($errors->any())
    <div class="admin-auth-alert">
        {{ $errors->first() }}
    </div>
@endif

        <form method="POST" action="{{ route('admin.login.post') }}" class="admin-auth-form">
            @csrf

            <div class="admin-auth-field">
                <label>Correo electrónico</label>

                <div class="admin-auth-input">
                    <i class='bx bx-envelope'></i>
                    <input type="email" name="correo" placeholder="admin@tienda.com" required>
                </div>
            </div>

            <div class="admin-auth-field">
                <label>Contraseña</label>

                <div class="admin-auth-input">
                    <i class='bx bx-lock-alt'></i>
                    <input type="password" name="password" placeholder="Tu contraseña" required>

                    <button type="button" class="admin-auth-password-toggle">
                        <i class='bx bx-show'></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="admin-auth-submit">
                Entrar al panel
            </button>

        </form>

    </section>

</main>

<script src="{{ asset('assets/js/login.js') }}"></script>

</body>
</html>