<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Pago aprobado</title>
</head>

<body style="margin:0;padding:0;background:#f5f5f5;font-family:Arial, Helvetica, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5;padding:30px 0;">
        <tr>
            <td align="center">

                <table width="650" cellpadding="0" cellspacing="0"
                    style="background:#ffffff;border-radius:12px;overflow:hidden;">

                    <tr>
                        <td align="center" style="background:#000000;padding:30px;color:#ffffff;">
                            <h1 style="margin:0;font-size:28px;">CORA CR</h1>
                            <p style="margin-top:10px;">Pago aprobado correctamente</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:35px;">

                            <h2 style="margin-top:0;">¡Tu pago fue validado!</h2>

                            <p>
                                Hola <strong>{{ $pedido->nombre_cliente }}</strong>,
                            </p>

                            <p>
                                Tu pedido <strong>{{ $pedido->numero_pedido }}</strong> fue aprobado correctamente.
                            </p>

                            <div style="background:#f8fafc;border:1px solid #e5e7eb;border-radius:10px;padding:20px;margin:25px 0;">
                                <p style="margin:0 0 10px;">
                                    <strong>Número de pedido:</strong>
                                    {{ $pedido->numero_pedido }}
                                </p>

                                <p style="margin:0 0 10px;">
                                    <strong>Estado:</strong>
                                    Pago aprobado
                                </p>

                                <p style="margin:0;">
                                    <strong>Total:</strong>
                                    ₡{{ number_format($pedido->total, 2) }}
                                </p>
                            </div>

                            <p>
                                Ahora empezaremos a preparar tu pedido.
                            </p>

                            <p>
                                Te contactaremos si necesitamos confirmar algún detalle de entrega.
                            </p>

                        </td>
                    </tr>

                    <tr>
                        <td align="center"
                            style="padding:25px;background:#f8fafc;color:#6b7280;font-size:13px;">
                            Gracias por comprar en Cora CR.
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