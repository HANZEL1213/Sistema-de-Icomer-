<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Pedido recibido</title>
</head>

<body style="
    margin:0;
    padding:0;
    background:#f5f5f5;
    font-family:Arial, Helvetica, sans-serif;
">

    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5;padding:30px 0;">
        <tr>
            <td align="center">

                <table width="650" cellpadding="0" cellspacing="0"
                    style="background:#ffffff;border-radius:12px;overflow:hidden;">

                    {{-- HEADER --}}
                    <tr>
                        <td align="center"
                            style="background:#111827;padding:30px;color:#ffffff;">

                            <h1 style="margin:0;font-size:28px;">
                                CORA CR
                            </h1>

                            <p style="margin-top:10px;">
                                Pedido recibido correctamente
                            </p>

                        </td>
                    </tr>

                    {{-- CONTENIDO --}}
                    <tr>
                        <td style="padding:35px;">

                            <h2 style="margin-top:0;">
                                ¡Gracias por tu compra!
                            </h2>

                            <p>
                                Hola <strong>{{ $pedido->nombre_cliente }}</strong>,
                            </p>

                            <p>
                                Hemos recibido tu pedido correctamente y ya se encuentra en proceso de revisión.
                            </p>

                            <div style="
                                background:#f8fafc;
                                border:1px solid #e5e7eb;
                                border-radius:10px;
                                padding:20px;
                                margin:25px 0;
                            ">

                                <p style="margin:0 0 10px;">
                                    <strong>Número de pedido:</strong>
                                    {{ $pedido->numero_pedido }}
                                </p>

                                <p style="margin:0 0 10px;">
                                    <strong>Estado:</strong>
                                    En revisión
                                </p>

                                <p style="margin:0;">
                                    <strong>Total:</strong>
                                    ₡{{ number_format($pedido->total, 2) }}
                                </p>

                            </div>

                            <p>
                                Estamos verificando tu comprobante de pago.
                            </p>

                            <p>
                                Una vez validado, te notificaremos nuevamente.
                            </p>

                            <p>
                                Puedes guardar este número de pedido para futuras consultas:
                            </p>

                            <div style="
                                background:#111827;
                                color:white;
                                text-align:center;
                                padding:15px;
                                border-radius:8px;
                                font-size:18px;
                                font-weight:bold;
                                letter-spacing:1px;
                            ">
                                {{ $pedido->numero_pedido }}
                            </div>

                        </td>
                    </tr>

                    {{-- FOOTER --}}
                    <tr>
                        <td align="center"
                            style="
                                padding:25px;
                                background:#f8fafc;
                                color:#6b7280;
                                font-size:13px;
                            ">

                            Gracias por confiar en Cora CR.

                            <br><br>

                            Este correo fue generado automáticamente.

                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>