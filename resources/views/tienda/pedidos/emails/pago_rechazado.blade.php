<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Pago no validado</title>
</head>

<body style="margin:0;padding:0;background:#f3f4f6;font-family:Arial, Helvetica, sans-serif;">

@php
    $pago = $pedido->pagoUltimo;

    $direccionEntrega = $pedido->tipo_entrega === 'envio'
        ? collect([
            $pedido->provincia_envio,
            $pedido->canton_envio,
            $pedido->distrito_envio,
            $pedido->direccion_envio,
        ])->filter()->implode(', ')
        : 'Retiro en tienda';
@endphp

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6;padding:24px 12px;">
    <tr>
        <td align="center">

            <table width="650" cellpadding="0" cellspacing="0"
                style="max-width:650px;background:#ffffff;border-radius:16px;overflow:hidden;">

                <tr>
                    <td align="center" style="background:#991b1b;padding:28px 24px;color:#ffffff;">
                        <h1 style="margin:0;font-size:28px;letter-spacing:1px;">CORA CR</h1>
                        <p style="margin:8px 0 0;font-size:14px;color:#fee2e2;">
                            No pudimos validar tu comprobante
                        </p>
                    </td>
                </tr>

                <tr>
                    <td style="padding:30px 30px;text-align:center;">

                        <div style="
                            width:52px;
                            height:52px;
                            line-height:52px;
                            border-radius:50%;
                            background:#fee2e2;
                            color:#991b1b;
                            font-size:28px;
                            margin:0 auto 14px;
                        ">
                            !
                        </div>

                        <h2 style="margin:0 0 8px;font-size:24px;color:#111827;">
                            Tu comprobante necesita revisión
                        </h2>

                        <p style="margin:0 0 22px;color:#374151;font-size:15px;line-height:1.5;">
                            Hola <strong>{{ $pedido->nombre_cliente }}</strong>, no pudimos validar el comprobante
                            enviado para tu pedido. Puedes corregirlo y enviarlo nuevamente.
                        </p>

                        <div style="
                            background:#f8fafc;
                            border:1px solid #e5e7eb;
                            border-radius:12px;
                            padding:20px;
                            margin-bottom:22px;
                            text-align:left;
                        ">

                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="padding:8px 0;color:#6b7280;width:140px;">
                                        Pedido #
                                    </td>
                                    <td style="padding:8px 0;color:#111827;font-weight:bold;">
                                        {{ $pedido->numero_pedido }}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:8px 0;color:#6b7280;">
                                        Estado
                                    </td>
                                    <td style="padding:8px 0;color:#991b1b;font-weight:bold;">
                                        Pago rechazado
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:8px 0;color:#6b7280;">
                                        Total
                                    </td>
                                    <td style="padding:8px 0;color:#111827;font-weight:bold;">
                                        ₡{{ number_format($pedido->total, 2) }}
                                    </td>
                                </tr>

                                @if($pedido->telefono_cliente)
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
                                        {{ $pedido->tipo_entrega === 'envio' ? 'Envío a domicilio' : 'Retiro en tienda' }}
                                    </td>
                                </tr>

                                @if($pedido->tipo_entrega === 'envio')
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

                        @if($pago?->motivo_rechazo)
                            <div style="
                                background:#fff7ed;
                                border:1px solid #fed7aa;
                                border-radius:12px;
                                padding:16px;
                                margin-bottom:22px;
                                text-align:left;
                            ">
                                <p style="margin:0 0 8px;color:#9a3412;font-weight:bold;font-size:14px;">
                                    Motivo del rechazo
                                </p>

                                <p style="margin:0;color:#7c2d12;line-height:1.6;font-size:14px;">
                                    {{ $pago->motivo_rechazo }}
                                </p>
                            </div>
                        @endif

                        <div style="text-align:center;margin:0 0 22px;">
                            <a href="{{ route('tienda.pedidos.show', $pedido->codigo_seguimiento_publico) }}"
                                style="
                                    display:inline-block;
                                    background:#111827;
                                    color:#ffffff;
                                    text-decoration:none;
                                    padding:13px 28px;
                                    border-radius:999px;
                                    font-size:14px;
                                    font-weight:700;
                                ">
                                Corregir pago
                            </a>
                        </div>

                        <p style="margin:0;color:#6b7280;font-size:13px;line-height:1.5;">
                            Puedes enviar nuevamente el comprobante desde el botón anterior.
                        </p>

                    </td>
                </tr>

                <tr>
                    <td align="center"
                        style="padding:20px;background:#f8fafc;color:#6b7280;font-size:12px;line-height:1.5;">
                        Gracias por comprar en Cora CR.
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