@extends('tienda.layouts.app')

@section('title', 'Mi cuenta')

@section('content')

    <main class="store-account-page">

        <div class="container py-4 py-lg-5">

            {{-- HERO --}}
            <section class="account-hero">

                <div class="account-hero-user">
                    <div class="account-avatar">
                        {{ strtoupper(substr($usuario->nombre, 0, 1)) }}
                    </div>

                    <div>
                        <span>Mi cuenta</span>

                        <h1>
                            Hola, {{ $usuario->nombre }}
                        </h1>

                        <p>
                            Gestiona tus datos, seguridad y pedidos desde un solo lugar.
                        </p>
                    </div>
                </div>

                <form action="{{ route('tienda.logout') }}" method="POST">
                    @csrf

                    <button type="submit" class="account-logout-btn">
                        <i class="bi bi-box-arrow-right"></i>
                        Cerrar sesión
                    </button>
                </form>

            </section>


            {{-- RESUMEN --}}
            <section class="account-summary-grid mt-4">

                <div class="account-summary-card">
                    <i class="bi bi-bag-check"></i>
                    <div>
                        <strong>{{ $cantidadPedidos }}</strong>
                        <span>Pedidos realizados</span>
                    </div>
                </div>

                <div class="account-summary-card">
                    <i class="bi bi-heart"></i>
                    <div>
                        <strong>{{ $cantidadFavoritos }}</strong>
                        <span>Favoritos guardados</span>
                    </div>
                </div>

                <div class="account-summary-card">
                    <i class="bi bi-person-check"></i>
                    <div>
                        <strong>{{ $usuario->activo ? 'Activa' : 'Inactiva' }}</strong>
                        <span>Estado de cuenta</span>
                    </div>
                </div>

            </section>


            <div class="row g-4 mt-1">

                {{-- COLUMNA PRINCIPAL --}}
                <div class="col-lg-7">

                    {{-- PEDIDOS --}}
                    <section class="account-panel">

                        <div class="account-panel-header">
                            <div>
                                <h2>Pedidos recientes</h2>
                                <p>Consulta el estado de tus últimas compras.</p>
                            </div>

                            <a href="{{ route('tienda.pedidos.mis') }}">
                                Ver todos
                            </a>
                        </div>

                        @forelse ($pedidosRecientes as $pedido)
                            <div class="account-order-item">

                                <div class="account-order-left">
                                    <div class="account-order-icon">
                                        <i class="bi bi-box-seam"></i>
                                    </div>

                                    <div>
                                        <strong>{{ $pedido->numero_pedido }}</strong>
                                        <span>{{ $pedido->created_at->format('d/m/Y') }}</span>
                                    </div>
                                </div>

                                <div class="account-order-right">
                                    <strong>₡{{ number_format($pedido->total, 2) }}</strong>

                                    <span class="account-status">
                                        {{ ucfirst(str_replace('_', ' ', $pedido->estado)) }}
                                    </span>
                                </div>

                            </div>

                        @empty

                            <div class="account-empty">
                                <i class="bi bi-bag-x"></i>
                                <strong>Aún no tienes pedidos</strong>
                                <span>Cuando realices una compra, aparecerá aquí.</span>

                                <a href="{{ route('tienda.productos.index') }}">
                                    Explorar productos
                                </a>
                            </div>
                        @endforelse

                    </section>

                </div>


                {{-- COLUMNA LATERAL --}}
                <div class="col-lg-5">

                    <div class="account-side-stack">

                        {{-- DATOS --}}
                        <section class="account-panel">

                            <div class="account-panel-header simple">
                                <div>
                                    <h2>Datos personales</h2>
                                    <p>Actualiza tu información básica.</p>
                                </div>

                                <i class="bi bi-person-lines-fill"></i>
                            </div>

                            <form id="datosForm" action="{{ route('tienda.cuenta.update') }}#datosForm" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="account-form-group">
                                    <label for="nombre">Nombre completo</label>
                                    <input type="text" id="nombre" name="nombre" maxlength="50"
                                        value="{{ old('nombre', $usuario->nombre) }}" required>

                                    @error('nombre')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="account-form-group">
                                    <label for="correo">Correo electrónico</label>
                                    <input type="email" id="correo" name="correo" maxlength="190"
                                        value="{{ old('correo', $usuario->correo) }}" required>

                                    @error('correo')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="account-form-group">
                                    <label for="telefono">Teléfono</label>
                                    <input type="number" id="telefono" name="telefono"
                                        value="{{ old('telefono', $usuario->telefono) }}" placeholder="No registrado">

                                    @error('telefono')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <button type="submit" class="account-primary-btn">
                                    Guardar cambios
                                </button>
                            </form>

                        </section>


                        {{-- SEGURIDAD --}}
                        <section class="account-panel">

                            <div class="account-panel-header simple">
                                <div>
                                    <h2>Seguridad</h2>
                                    <p>Cambia tu contraseña de acceso.</p>
                                </div>

                                <i class="bi bi-shield-lock"></i>
                            </div>

                            <form id="passwordForm" action="{{ route('tienda.cuenta.password') }}#passwordForm"
                                method="POST">
                                @csrf
                                @method('PUT')

                                <div class="account-form-group">
                                    <label for="password_actual">Contraseña actual</label>
                                    <input type="password" id="password_actual" name="password_actual" required>

                                    @error('password_actual')
                                        <small class="account-field-error">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="account-form-group">
                                    <label for="password">Nueva contraseña</label>
                                    <input type="password" id="password" name="password" required>

                                    @error('password')
                                        <small class="account-field-error">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="account-form-group">
                                    <label for="password_confirmation">Confirmar contraseña</label>
                                    <input type="password" id="password_confirmation" name="password_confirmation" required>
                                </div>

                                <button type="submit" class="account-primary-btn">
                                    Actualizar contraseña
                                </button>
                            </form>

                        </section>

                    </div>

                </div>

            </div>

        </div>

    </main>

@endsection


@if ($errors->has('password_actual') || $errors->has('password'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('passwordForm');

            if (form) {
                form.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
        });
    </script>
@endif
