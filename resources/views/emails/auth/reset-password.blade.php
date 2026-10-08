<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <style>
        body {
            font-family: 'Montserrat', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #0b0e14;
            color: #ffffff;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        .card {
            background-color: #141824;
            border-radius: 12px;
            padding: 40px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
        }
        .logo {
            margin-bottom: 25px;
        }
        .title {
            font-size: 22px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 15px;
        }
        .text {
            font-size: 15px;
            color: #a0aec0;
            margin-bottom: 30px;
        }
        .button {
            display: inline-block;
            background-color: #d1121d;
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 30px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(209, 18, 29, 0.4);
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #718096;
            text-align: center;
        }
        .link-text {
            word-break: break-all;
            color: #d1121d;
            font-size: 13px;
        }
    </style>
</head>
<body style="background-color: #0b0e14; padding: 20px;">
    <div class="container">
        <div class="card">
            
            <div class="logo">
                <h1 style="color: #ffffff; margin: 0; font-size: 28px; font-family: 'Playfair Display', serif;">
                    Beauty <span style="color: #d1121d;">&amp;</span> Barber <span style="font-weight: 300;">Studio</span>
                </h1>
            </div>
            
            <h2 class="title">Hola, {{ $user->name }}</h2>
            
            <p class="text">
                Recibimos una solicitud para restablecer la contraseña de tu cuenta. 
                Si no realizaste esta solicitud, puedes ignorar este mensaje sin problema.
            </p>
            
            <a href="{{ $url }}" class="button">Restablecer Contraseña</a>
            
            <div class="footer">
                <p>Este enlace de recuperación expirará en 60 minutos.</p>
                <p style="margin-top: 20px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.1);">
                    Si tienes problemas para hacer clic en el botón "Restablecer Contraseña", copia y pega la siguiente URL en tu navegador web:
                    <br><br>
                    <a href="{{ $url }}" class="link-text">{{ $url }}</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
