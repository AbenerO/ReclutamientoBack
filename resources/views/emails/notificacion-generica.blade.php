<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $asuntoMensaje }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f6f9;font-family:Arial,sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f9;padding:30px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:10px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,0.08);">

                    <tr>
                        <td style="background-color:#002A52;padding:28px 40px;text-align:center;">
                            <h1 style="color:#ffffff;margin:0;font-size:22px;font-weight:700;letter-spacing:1px;">
                                {{ config('app.name') }}
                            </h1>
                            <p style="color:#90caf9;margin:6px 0 0;font-size:13px;">Notificación del sistema</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:36px 40px;">
                            <p style="color:#333333;font-size:15px;line-height:1.7;margin:0;white-space:pre-wrap;">{{ $cuerpo }}</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="background-color:#f8f9fa;border-top:1px solid #e9ecef;padding:20px 40px;text-align:center;">
                            <p style="color:#888;font-size:12px;margin:0 0 4px;">
                                {{ config('app.name') }}
                            </p>
                            <p style="color:#aaa;font-size:11px;margin:0;">
                                Este correo es generado automáticamente, por favor no responda a este mensaje.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
