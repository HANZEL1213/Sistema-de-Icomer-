<!DOCTYPE html>

<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Pedido recibido</title>
</head>

<body style="margin:0;padding:0;background:#f3f4f6;font-family:Arial, Helvetica, sans-serif;">

    @php
        $direccionEntrega =
            $pedido->tipo_entrega === 'envio'
                ? collect([
                    $pedido->provincia_envio,
                    $pedido->canton_envio,
                    $pedido->distrito_envio,
                    $pedido->direccion_envio,
                ])
                    ->filter()
                    ->implode(', ')
                : 'Retiro en tienda';
    @endphp

    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6;padding:24px 12px;">
        <tr>
            <td align="center">

                <table width="650" cellpadding="0" cellspacing="0"
                    style="max-width:650px;background:#ffffff;border-radius:16px;overflow:hidden;">

                    {{-- HEADER --}}
                    <tr>
                        <td align="center" style="background:#111827;padding:28px 24px;color:#ffffff;">

                            <h1 style="margin:0;font-size:28px;letter-spacing:1px;">
                                CORA CR
                            </h1>

                            <p style="margin:8px 0 0;font-size:14px;color:#e5e7eb;">
                                Pedido recibido correctamente
                            </p>

                        </td>
                    </tr>

                    {{-- CONTENIDO --}}
                    <tr>
                        <td style="padding:32px;">

                            <div
                                style="
                                        width:54px;
                                        height:54px;
                                        line-height:54px;
                                        border-radius:50%;
                                        background:#fef3c7;
                                        color:#d97706;
                                        font-size:28px;
                                        text-align:center;
                                        margin:0 auto 18px;
                                    ">
                                ⏳
                            </div>

                            <h2
                                style="
                                        margin:0 0 10px;
                                        text-align:center;
                                        color:#111827;
                                        font-size:24px;
                                    ">
                                ¡Gracias por tu compra!
                            </h2>

                            <p
                                style="
                                        margin:0 0 10px;
                                        text-align:center;
                                        color:#374151;
                                        line-height:1.6;
                                    ">
                                Hola <strong>{{ $pedido->nombre_cliente }}</strong>,
                            </p>

                            <p
                                style="
                                        margin:0 0 28px;
                                        text-align:center;
                                        color:#374151;
                                        line-height:1.6;
                                    ">
                                Hemos recibido tu pedido correctamente y estamos verificando
                                tu comprobante de pago. Te notificaremos cuando sea validado.
                            </p>

                            {{-- RESUMEN DEL PEDIDO --}}
                            <div
                                style="
                                        background:#f8fafc;
                                        border:1px solid #e5e7eb;
                                        border-radius:12px;
                                        padding:20px;
                                        margin-bottom:24px;
                                    ">

                                <table width="100%" cellpadding="0" cellspacing="0">

                                    <tr>
                                        <td
                                            style="
                                                    padding:8px 0;
                                                    color:#6b7280;
                                                    width:140px;
                                                ">
                                            Pedido #
                                        </td>

                                        <td
                                            style="
                                                    padding:8px 0;
                                                    color:#111827;
                                                    font-weight:bold;
                                                ">
                                            {{ $pedido->numero_pedido }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td style="padding:8px 0;color:#6b7280;">
                                            Estado
                                        </td>

                                        <td
                                            style="
                                                    padding:8px 0;
                                                    color:#d97706;
                                                    font-weight:bold;
                                                ">
                                            En revisión
                                        </td>
                                    </tr>

                                    <tr>
                                        <td style="padding:8px 0;color:#6b7280;">
                                            Total
                                        </td>

                                        <td
                                            style="
                                                    padding:8px 0;
                                                    color:#111827;
                                                    font-weight:bold;
                                                ">
                                            ₡{{ number_format($pedido->total, 2) }}
                                        </td>
                                    </tr>

                                    @if ($pedido->telefono_cliente)
                                        <tr>
                                            <td style="padding:8px 0;color:#6b7280;">
                                                Teléfono
                                            </td>

                                            <td style="padding:8px 0;color:#111827;">
                                                {{ $pedido->telefono_cliente }}
                                            </td>
                                        </tr>
                                    @endif

                                    <tr>
                                        <td style="padding:8px 0;color:#6b7280;">
                                            Entrega
                                        </td>

                                        <td style="padding:8px 0;color:#111827;">
                                            {{ $pedido->tipo_entrega === 'retiro' ? 'Retiro en tienda' : 'Envío a domicilio' }}
                                        </td>
                                    </tr>

                                    @if ($pedido->tipo_entrega === 'envio')
                                        <tr>
                                            <td style="padding:8px 0;color:#6b7280;vertical-align:top;">
                                                Dirección
                                            </td>

                                            <td style="padding:8px 0;color:#111827;line-height:1.5;">
                                                {{ $direccionEntrega }}
                                            </td>
                                        </tr>
                                    @endif

                                </table>

                            </div>

                            {{-- NOTAS --}}
                            @if (!empty($pedido->notas))
                                <div
                                    style="
                            background:#fff7ed;
                            border:1px solid #fed7aa;
                            border-radius:12px;
                            padding:16px;
                            margin-bottom:24px;
                        ">

                                    <p
                                        style="
                                margin:0 0 8px;
                                color:#9a3412;
                                font-weight:bold;
                            ">
                                        Nota del pedido
                                    </p>

                                    <p
                                        style="
                                margin:0;
                                color:#7c2d12;
                                line-height:1.6;
                            ">
                                        {{ $pedido->notas }}
                                    </p>

                                </div>
                            @endif

                            {{-- BOTÓN --}}
                            <div style="text-align:center;margin-bottom:22px;">

                                <a href="{{ route('tienda.pedidos.seguimiento', $pedido->codigo_seguimiento_publico) }}"
                                    style="
                                    display:inline-block;
                                    background:#111827;
                                    color:#ffffff;
                                    text-decoration:none;
                                    padding:14px 28px;
                                    border-radius:999px;
                                    font-size:14px;
                                    font-weight:700;
                                ">
                                    Dar seguimiento al pedido
                                </a>

                            </div>

                            <p
                                style="
                            margin:0;
                            text-align:center;
                            color:#6b7280;
                            font-size:13px;
                            line-height:1.6;
                        ">
                                Guarda tu número de pedido para futuras consultas.
                            </p>

                        </td>
                    </tr>

                    {{-- FOOTER --}}
                    <tr>
                        <td align="center"
                            style="
                            padding:22px;
                            background:#f8fafc;
                            color:#6b7280;
                            font-size:12px;
                            line-height:1.6;
                        ">

                            Gracias por confiar en Cora CR.

                            <br>

                            Este correo fue generado automáticamente.

                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>
