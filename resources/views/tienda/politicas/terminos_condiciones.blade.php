@extends('tienda.layouts.app')

@section('title', 'Términos, Condiciones y Políticas')

@section('content')

<section class="py-5">
    <div class="container">

        <div class="text-center mb-5">

            <span class="badge bg-primary-subtle text-primary px-3 py-2">
                Información Legal
            </span>

            <h1 class="mt-3 fw-bold">
                Términos, Condiciones y Políticas
            </h1>

            <p class="text-muted mx-auto" style="max-width: 750px;">
                Al utilizar nuestro sitio web o realizar una compra en Cora CR,
                aceptas los siguientes términos, condiciones y políticas.
            </p>

        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-lg-5">

                {{-- TÉRMINOS --}}
                <h3 class="fw-bold mb-3">
                    1. Términos y Condiciones
                </h3>

                <p>
                    Cora CR opera como una tienda física y en línea dedicada a la
                    comercialización de productos para el público en general.
                </p>

                <p>
                    Al realizar una compra, el cliente declara que la información
                    suministrada es correcta y acepta los presentes términos.
                </p>

                <hr class="my-4">

                {{-- PEDIDOS --}}
                <h3 class="fw-bold mb-3">
                    2. Pedidos y Pagos
                </h3>

                <p>
                    Todos los pedidos están sujetos a verificación de disponibilidad
                    de inventario y validación del pago correspondiente.
                </p>

                <p>
                    Cora CR se reserva el derecho de cancelar pedidos cuando exista:
                </p>

                <ul>
                    <li>Error evidente en el precio publicado.</li>
                    <li>Falta de inventario.</li>
                    <li>Información incorrecta o incompleta.</li>
                    <li>Indicios de fraude o uso indebido del sistema.</li>
                </ul>

                <hr class="my-4">

                {{-- ENVÍOS --}}
                <h3 class="fw-bold mb-3">
                    3. Envíos y Entregas
                </h3>

                <p>
                    Los tiempos de entrega son estimados y pueden variar según la
                    ubicación del cliente, condiciones climáticas o situaciones
                    logísticas externas.
                </p>

                <p>
                    Es responsabilidad del cliente proporcionar información correcta
                    para la entrega del pedido.
                </p>

                <hr class="my-4">

                {{-- CAMBIOS --}}
                <h3 class="fw-bold mb-3">
                    4. Cambios y Devoluciones
                </h3>

                <p>
                    Los cambios podrán solicitarse dentro del plazo establecido por
                    Cora CR siempre que el producto se encuentre en condiciones
                    adecuadas y acompañado de su comprobante de compra.
                </p>

                <p>
                    No aplican cambios ni devoluciones para productos personalizados,
                    alterados o dañados por uso indebido.
                </p>

                <hr class="my-4">

                {{-- GARANTÍA --}}
                <h3 class="fw-bold mb-3">
                    5. Garantías
                </h3>

                <p>
                    Las garantías cubren únicamente defectos de fabricación.
                </p>

                <p>
                    No cubren daños ocasionados por golpes, caídas, humedad,
                    manipulación incorrecta o desgaste normal por uso.
                </p>

                <hr class="my-4">

                {{-- PRIVACIDAD --}}
                <h3 class="fw-bold mb-3">
                    6. Política de Privacidad
                </h3>

                <p>
                    La información proporcionada por los clientes será utilizada
                    únicamente para procesar pedidos, brindar soporte y mejorar
                    nuestros servicios.
                </p>

                <p>
                    Cora CR no vende ni comercializa información personal de sus
                    clientes.
                </p>

                <hr class="my-4">

                {{-- PROPIEDAD --}}
                <h3 class="fw-bold mb-3">
                    7. Propiedad Intelectual
                </h3>

                <p>
                    Todo el contenido presente en este sitio, incluyendo textos,
                    imágenes, logotipos y diseños, pertenece a Cora CR o a sus
                    respectivos propietarios y está protegido por la legislación
                    aplicable.
                </p>

                <hr class="my-4">

                {{-- MODIFICACIONES --}}
                <h3 class="fw-bold mb-3">
                    8. Modificaciones
                </h3>

                <p>
                    Cora CR podrá actualizar estos términos, condiciones y políticas
                    en cualquier momento sin previo aviso.
                </p>

                <p class="text-muted small mb-0">
                    Última actualización: {{ now()->format('d/m/Y') }}
                </p>

            </div>
        </div>

    </div>
</section>

@endsection