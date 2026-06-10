<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bienvenido a Cora CR</title>
</head>
<body style="margin:0; padding:0; background:#f5f6f8; font-family:Arial, Helvetica, sans-serif; color:#111827;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f6f8; padding:32px 16px;">
    <tr>
        <td align="center">
            <table width="100%" cellpadding="0" cellspacing="0" style="max-width:540px; background:#ffffff; border-radius:22px; overflow:hidden; border:1px solid #e5e7eb;">
                <tr>
                    <td style="padding:34px 30px; text-align:center;">

                        <div style="width:58px; height:58px; border-radius:18px; background:#111827; color:#ffffff; display:inline-flex; align-items:center; justify-content:center; font-size:24px; font-weight:800;">
                            C
                        </div>

                        <h1 style="margin:20px 0 8px; font-size:24px; font-weight:800;">
                            Bienvenido a Cora CR
                        </h1>

                        <p style="margin:0; color:#6b7280; font-size:14px; line-height:1.7;">
                            Hola {{ $usuario->nombre }}, tu cuenta fue creada correctamente usando Google.
                        </p>

                        <div style="margin-top:24px; background:#f9fafb; border:1px solid #e5e7eb; border-radius:18px; padding:22px; text-align:left;">
                            <p style="margin:0; color:#374151; font-size:14px; line-height:1.7;">
                                Ahora puedes consultar tus pedidos, guardar favoritos y gestionar tu información desde tu cuenta.
                            </p>
                        </div>

                        <p style="margin:24px 0 0; color:#9ca3af; font-size:12px; line-height:1.7;">
                            Este correo fue enviado automáticamente por <strong>Cora CR</strong>.
                        </p>

                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

</body>
</html>