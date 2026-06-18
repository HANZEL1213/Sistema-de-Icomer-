@extends('tienda.layouts.app')

@section('content')
<section class="store-section">
    <div class="container" style="max-width: 560px;">

        <h1 class="store-section-title mb-3">
            Seguir pedido
        </h1>

        <p class="text-muted mb-4">
            Ingresa tu número de pedido o código de seguimiento.
        </p>

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('tienda.pedidos.buscarSeguimiento') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">
                    Código del pedido
                </label>

                <input type="text"
                    name="codigo"
                    class="form-control @error('codigo') is-invalid @enderror"
                    value="{{ old('codigo') }}"
                    placeholder="Ej: PED-000123"
                    required>

                @error('codigo')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <button type="submit" class="btn btn-dark w-100">
                Buscar pedido
            </button>
        </form>

    </div>
</section>
@endsection