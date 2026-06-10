<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Verifica tu correo</title>
</head>

<body style="margin:0; padding:0; background:#f5f6f8; font-family:Arial, Helvetica, sans-serif; color:#111827;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f6f8; padding:32px 16px;">
    <tr>
        <td align="center">

            <table width="100%" cellpadding="0" cellspacing="0" style="max-width:540px; background:#ffffff; border-radius:22px; overflow:hidden; border:1px solid #e5e7eb;">

                <tr>
                    <td style="padding:34px 30px 24px; text-align:center;">

                        <div style="width:58px; height:58px; border-radius:18px; background:#111827; color:#ffffff; display:inline-flex; align-items:center; justify-content:center; font-size:24px; font-weight:800;">
                            C
                        </div>

                        <h1 style="margin:20px 0 8px; font-size:24px; font-weight:800; color:#111827;">
                            Verifica tu correo
                        </h1>

                        <p style="margin:0; color:#6b7280; font-size:14px; line-height:1.7;">
                            Hola {{ $usuario->nombre }}, confirma que este correo pertenece a tu cuenta de Cora CR.
                        </p>

                    </td>
                </tr>

                <tr>
                    <td style="padding:0 30px 30px;">

                        <div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:18px; padding:22px;">

                            <p style="margin:0 0 18px; color:#374151; font-size:14px; line-height:1.7;">
                                Para completar la verificación, presiona el siguiente botón. Este enlace estará disponible durante <strong>60 minutos</strong>.
                            </p>

                            <a href="{{ $url }}"
                               style="display:block; text-align:center; background:#111827; color:#ffffff; text-decoration:none; font-weight:800; padding:15px 18px; border-radius:14px; font-size:15px;">
                                Verificar correo
                            </a>

                            <p style="margin:18px 0 0; color:#6b7280; font-size:12px; line-height:1.7;">
                                Si el botón no funciona, copia y pega este enlace en tu navegador:
                            </p>

                            <p style="margin:8px 0 0; word-break:break-all; font-size:12px;">
                                <a href="{{ $url }}" style="color:#111827; text-decoration:underline;">
                                    {{ $url }}
                                </a>
                            </p>

                        </div>

                        <p style="margin:20px 0 0; color:#6b7280; font-size:12px; line-height:1.7;">
                            Si no creaste una cuenta en Cora CR, puedes ignorar este correo.
                        </p>

                        <hr style="border:0; border-top:1px solid #e5e7eb; margin:24px 0;">

                        <p style="margin:0; text-align:center; color:#9ca3af; font-size:12px; line-height:1.7;">
                            Este mensaje fue enviado automáticamente por <strong>Cora CR</strong>.<br>
                            Por seguridad, no respondas a este correo.
                        </p>

                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>