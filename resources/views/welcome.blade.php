<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beauty & Barber Studio - Inicio de sesión</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html, body {
            width: 100%;
            min-height: 100vh;
            overflow-x: hidden;
        }

        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background-color: #0b0e14;
            background-image: 
                radial-gradient(circle at center, rgba(11, 14, 20, 0.72) 0%, rgba(7, 9, 13, 0.94) 100%),
                url('https://images.unsplash.com/photo-1503951914875-452162b0f3f1?auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            font-family: 'Montserrat', sans-serif;
            color: #ffffff;
            padding: 30px 16px;
            position: relative;
        }

        /* ==========================================
           ESQUINA SUPERIOR DERECHA: LEMAS Y BARRAS
           ========================================== */
        .top-header-right {
            position: fixed;
            top: 24px;
            right: 32px;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 6px;
            z-index: 20;
            pointer-events: none;
            text-shadow: 0 2px 5px rgba(0, 0, 0, 0.8);
        }

        .top-header-right .tagline {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 2px;
            color: #ffffff;
            text-transform: uppercase;
        }

        .barber-stripes-mini {
            display: flex;
            height: 5px;
            width: 44px;
            border-radius: 2px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.6);
        }

        .barber-stripes-mini .stripe-blue { flex: 1; background: #143b8c; }
        .barber-stripes-mini .stripe-red { flex: 1; background: #d1121d; }
        .barber-stripes-mini .stripe-white { flex: 1; background: #ffffff; }

        /* ==========================================
           POSTE DE BARBERÍA VINTAGE LATERAL (DESKTOP)
           ========================================== */
        .desktop-barber-pole {
            position: fixed;
            left: 55px;
            top: 50%;
            transform: translateY(-50%);
            width: 76px;
            display: flex;
            flex-direction: column;
            align-items: center;
            z-index: 15;
            filter: drop-shadow(14px 18px 24px rgba(4, 12, 32, 0.85)) 
                    drop-shadow(0 0 16px rgba(20, 55, 130, 0.35));
            pointer-events: none;
        }

        /* Sombra proyectada detrás del poste sobre la pared (Azul oscuro tenue) */
        .desktop-barber-pole::before {
            content: '';
            position: absolute;
            top: 15px;
            left: 18px;
            width: 70px;
            height: calc(100% - 12px);
            background: radial-gradient(ellipse at center, rgba(20, 50, 120, 0.45) 0%, rgba(12, 30, 75, 0.3) 50%, rgba(5, 12, 35, 0.15) 75%, transparent 95%);
            filter: blur(16px);
            border-radius: 35px;
            z-index: -1;
            pointer-events: none;
        }

        /* Sombra de apoyo elíptica en la base para asentar el poste */
        .desktop-barber-pole::after {
            content: '';
            position: absolute;
            bottom: -22px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 20px;
            background: radial-gradient(ellipse at center, rgba(16, 45, 110, 0.45) 0%, rgba(8, 20, 55, 0.35) 45%, rgba(0, 0, 0, 0.7) 70%, transparent 90%);
            border-radius: 50%;
            filter: blur(5px);
            z-index: -1;
            pointer-events: none;
        }

        /* Remate superior: esfera de latón antiguo */
        .pole-ball {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: radial-gradient(circle at 35% 30%, #fff2d1 0%, #d8be82 35%, #8f723b 70%, #3e2e13 100%);
            margin-bottom: -4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.6);
            z-index: 3;
            position: relative;
        }

        /* Cúpula superior de latón / bronce vintage con moldura escalonada */
        .pole-cap-top {
            width: 76px;
            height: 52px;
            background: linear-gradient(90deg, 
                #23170a 0%, 
                #4e391b 15%, 
                #8c713b 32%, 
                #cfb578 48%, 
                #fbf0cb 53%, 
                #cfb578 58%, 
                #8c713b 72%, 
                #4e391b 88%, 
                #23170a 100%
            );
            border-radius: 38px 38px 4px 4px / 44px 44px 6px 6px;
            position: relative;
            box-shadow: 
                inset 0 2px 4px rgba(255, 240, 195, 0.5),
                inset 0 -3px 4px rgba(20, 12, 4, 0.8),
                0 3px 6px rgba(0,0,0,0.6);
            z-index: 2;
        }

        /* Moldura de anillo inferior de la cúpula */
        .pole-cap-top::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: -2px;
            right: -2px;
            height: 12px;
            background: linear-gradient(90deg, 
                #1e1408 0%, 
                #5c4520 18%, 
                #b89c5f 46%, 
                #fae8b4 52%, 
                #b89c5f 58%, 
                #5c4520 82%, 
                #1e1408 100%
            );
            border-radius: 6px;
            box-shadow: 
                0 2px 4px rgba(0,0,0,0.7),
                inset 0 1px 1px rgba(255, 245, 210, 0.7),
                inset 0 -2px 2px rgba(0, 0, 0, 0.8);
        }

        /* Cilindro de cristal exterior */
        .pole-cylinder {
            width: 66px;
            height: 310px;
            position: relative;
            overflow: hidden;
            border-left: 2px solid rgba(255, 255, 255, 0.35);
            border-right: 2px solid rgba(255, 255, 255, 0.35);
            box-shadow: 
                inset 0 0 12px rgba(0, 0, 0, 0.85),
                0 0 15px rgba(0, 0, 0, 0.7);
            background: #0d1117;
            z-index: 1;
        }

        /* Franjas rotatorias clásicas de barbería (Rojo, Blanco y Azul) */
        .pole-stripes {
            position: absolute;
            top: -120px;
            left: 0;
            width: 100%;
            height: calc(100% + 240px);
            background: repeating-linear-gradient(
                -42deg,
                #c81c2b 0px,
                #c81c2b 20px,
                #f5f5f7 20px,
                #f5f5f7 40px,
                #173f91 40px,
                #173f91 60px,
                #f5f5f7 60px,
                #f5f5f7 80px
            );
            animation: barberPoleScroll 4s linear infinite;
        }

        /* Reflejo realista del tubo de cristal */
        .pole-glass-reflection {
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, 
                rgba(0, 0, 0, 0.6) 0%, 
                rgba(255, 255, 255, 0.5) 18%, 
                rgba(255, 255, 255, 0.12) 28%, 
                transparent 48%, 
                rgba(0, 0, 0, 0.1) 68%, 
                rgba(255, 255, 255, 0.28) 90%, 
                rgba(0, 0, 0, 0.65) 100%
            );
            z-index: 2;
            pointer-events: none;
        }

        /* Base inferior de latón / bronce con copa redondeada */
        .pole-cap-bottom {
            width: 76px;
            height: 52px;
            background: linear-gradient(90deg, 
                #23170a 0%, 
                #4e391b 15%, 
                #8c713b 32%, 
                #cfb578 48%, 
                #fbf0cb 53%, 
                #cfb578 58%, 
                #8c713b 72%, 
                #4e391b 88%, 
                #23170a 100%
            );
            border-radius: 4px 4px 38px 38px / 6px 6px 44px 44px;
            position: relative;
            box-shadow: 
                inset 0 3px 4px rgba(20, 12, 4, 0.8),
                inset 0 -2px 4px rgba(255, 240, 195, 0.35),
                0 4px 8px rgba(0,0,0,0.6);
            z-index: 2;
        }

        /* Moldura de anillo superior de la base */
        .pole-cap-bottom::before {
            content: '';
            position: absolute;
            top: 0;
            left: -2px;
            right: -2px;
            height: 12px;
            background: linear-gradient(90deg, 
                #1e1408 0%, 
                #5c4520 18%, 
                #b89c5f 46%, 
                #fae8b4 52%, 
                #b89c5f 58%, 
                #5c4520 82%, 
                #1e1408 100%
            );
            border-radius: 6px;
            box-shadow: 
                0 2px 4px rgba(0,0,0,0.7),
                inset 0 1px 1px rgba(255, 245, 210, 0.7),
                inset 0 -2px 2px rgba(0, 0, 0, 0.8);
        }

        /* Remate esférico inferior de latón antiguo */
        .pole-finial {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: radial-gradient(circle at 35% 30%, #fff2d1 0%, #d8be82 35%, #8f723b 70%, #3e2e13 100%);
            margin-top: -3px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.6);
            z-index: 3;
            position: relative;
        }

        @keyframes barberPoleScroll {
            0% { transform: translateY(0); }
            100% { transform: translateY(80px); }
        }

        /* ==========================================
           CONTENEDOR PRINCIPAL CENTRADO
           ========================================== */
        .main-auth-wrapper {
            width: 100%;
            max-width: 410px;
            display: flex;
            flex-direction: column;
            align-items: center;
            z-index: 10;
            margin: auto;
        }

        /* ==========================================
           LOGO Y MARCA SUPERIOR
           ========================================== */
        .brand-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            margin-bottom: 14px;
        }

        .brand-logo-wrapper {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px;
            transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1), filter 0.35s ease;
        }

        .brand-logo-wrapper:hover {
            transform: translateY(-2px) scale(1.025);
        }

        .brand-logo-img {
            width: 330px;
            max-width: 88vw;
            height: auto;
            object-fit: contain;
            display: block;
            filter: drop-shadow(0 10px 22px rgba(0, 0, 0, 0.75)) drop-shadow(0 0 20px rgba(209, 18, 29, 0.18));
            transition: filter 0.35s ease;
        }

        .brand-logo-wrapper:hover .brand-logo-img {
            filter: drop-shadow(0 14px 28px rgba(0, 0, 0, 0.85)) drop-shadow(0 0 30px rgba(209, 18, 29, 0.35));
        }

        /* Mini postes a los costados del logo */
        .mini-pole {
            width: 12px;
            height: 48px;
            display: flex;
            flex-direction: column;
            align-items: center;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.6));
        }

        .mini-cap {
            width: 12px;
            height: 5px;
            background: #d1d5db;
            border-radius: 2px;
        }

        .mini-body {
            width: 9px;
            height: 38px;
            position: relative;
            overflow: hidden;
            border-left: 1px solid rgba(255,255,255,0.7);
            border-right: 1px solid rgba(255,255,255,0.7);
        }

        .mini-stripes {
            position: absolute;
            top: -50px;
            left: 0;
            width: 100%;
            height: 120px;
            background: repeating-linear-gradient(
                -45deg,
                #d1121d 0px,
                #d1121d 5px,
                #ffffff 5px,
                #ffffff 10px,
                #143b8c 10px,
                #143b8c 15px,
                #ffffff 15px,
                #ffffff 20px
            );
            animation: barberPoleScroll 3s linear infinite;
        }

        .mini-glass {
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, rgba(0,0,0,0.3) 0%, rgba(255,255,255,0.5) 40%, transparent 100%);
        }

        .barber-icon-center {
            width: 74px;
            height: 74px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .barber-icon-center svg {
            width: 100%;
            height: 100%;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.6));
        }

        .brand-title {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 26px;
            font-weight: 700;
            color: #ffffff;
            line-height: 1.15;
            letter-spacing: -0.01em;
            text-shadow: 0 3px 8px rgba(0, 0, 0, 0.7);
        }

        .brand-subtitle {
            font-size: 11px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.85);
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-top: 4px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.6);
        }

        /* ==========================================
           TARJETA DEL FORMULARIO (GLASSMORPHISM)
           ========================================== */
        .login-card {
            width: 100%;
            background: rgba(14, 18, 24, 0.76);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 20px;
            padding: 32px 28px 26px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.75), 0 0 0 1px rgba(255, 255, 255, 0.05);
            margin-top: 14px;
            display: flex;
            flex-direction: column;
        }

        .card-heading {
            text-align: center;
            margin-bottom: 22px;
        }

        .card-title {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 24px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 6px;
            letter-spacing: -0.01em;
        }

        .card-subtitle {
            font-size: 13.5px;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 400;
        }

        /* ==========================================
           CAMPOS DE ENTRADA CON ICONOS INTERNOS
           ========================================== */
        .input-group {
            position: relative;
            width: 100%;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
        }

        .field-icon {
            position: absolute;
            left: 14px;
            width: 18px;
            height: 18px;
            color: rgba(255, 255, 255, 0.65);
            pointer-events: none;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
            transition: color 0.2s ease;
        }

        .field-icon svg {
            width: 100%;
            height: 100%;
        }

        .form-input {
            width: 100%;
            height: 48px;
            padding: 0 16px 0 44px;
            font-size: 14.5px;
            font-family: 'Montserrat', sans-serif;
            color: #ffffff;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.24);
            border-radius: 10px;
            outline: none;
            transition: border-color 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
        }

        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.45);
            font-size: 13.5px;
        }

        .form-input:focus {
            border-color: rgba(255, 255, 255, 0.6);
            background: rgba(255, 255, 255, 0.09);
            box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.12);
        }

        .input-group:focus-within .field-icon {
            color: #ffffff;
        }

        /* Ajuste específico para contraseña con botón de ojo */
        .password-group .form-input {
            padding-right: 46px;
        }

        .toggle-password-btn {
            position: absolute;
            right: 12px;
            width: 32px;
            height: 32px;
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, 0.65);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            transition: color 0.2s ease;
            z-index: 3;
            outline: none;
        }

        .toggle-password-btn:hover {
            color: #ffffff;
        }

        .toggle-password-btn svg {
            width: 20px;
            height: 20px;
        }

        .hidden {
            display: none !important;
        }

        /* ==========================================
           CHECKBOX RECORDARME (FONDO ROJO)
           ========================================== */
        .remember-row {
            display: flex;
            align-items: center;
            width: 100%;
            margin-top: 2px;
            margin-bottom: 6px;
        }

        .remember-container {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            user-select: none;
        }

        .real-checkbox {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
            pointer-events: none;
        }

        .custom-checkbox-box {
            width: 18px;
            height: 18px;
            border-radius: 4px;
            background: #d1121d;
            border: 1px solid #d1121d;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .real-checkbox:not(:checked) + .custom-checkbox-box {
            background: transparent;
            border: 1.5px solid rgba(255, 255, 255, 0.4);
        }

        .check-svg {
            width: 11px;
            height: 10px;
            display: block;
        }

        .real-checkbox:not(:checked) + .custom-checkbox-box .check-svg {
            display: none;
        }

        .remember-label-text {
            font-size: 13.5px;
            font-weight: 500;
            color: #ffffff;
        }

        /* ==========================================
           BOTÓN DE ENVÍO ROJO CON FLECHA
           ========================================== */
        .submit-btn {
            width: 100%;
            height: 48px;
            margin-top: 18px;
            padding: 0 20px;
            background: #d1121d;
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-family: 'Montserrat', sans-serif;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background 0.2s ease, transform 0.15s ease, box-shadow 0.2s ease;
            box-shadow: 0 4px 18px rgba(209, 18, 29, 0.45);
        }

        .submit-btn:hover {
            background: #b50f19;
            transform: translateY(-1px);
            box-shadow: 0 6px 22px rgba(209, 18, 29, 0.6);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .btn-arrow {
            width: 17px;
            height: 17px;
            transition: transform 0.2s ease;
        }

        .submit-btn:hover .btn-arrow {
            transform: translateX(3px);
        }

        /* ==========================================
           ENLACE DE REGISTRO
           ========================================== */
        .signup-prompt {
            margin-top: 22px;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.75);
            text-align: center;
        }

        .signup-link {
            color: #d1121d;
            text-decoration: none;
            font-weight: 600;
            margin-left: 4px;
            transition: color 0.2s ease, text-decoration 0.2s ease;
        }

        .signup-link:hover {
            color: #ff3b47;
            text-decoration: underline;
        }

        /* ==========================================
           ESQUINA INFERIOR IZQUIERDA: TIJERAS Y TEXTO
           ========================================== */
        .bottom-left-tagline {
            position: fixed;
            bottom: 24px;
            left: 28px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #ffffff;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 1.5px;
            z-index: 20;
            pointer-events: none;
            text-shadow: 0 2px 5px rgba(0,0,0,0.8);
        }

        .scissor-icon {
            width: 26px;
            height: 26px;
            color: #ffffff;
            transform: rotate(-45deg);
        }

        /* ==========================================
           ESQUINA INFERIOR DERECHA: FRANJAS CURVAS
           ========================================== */
        .bottom-right-swoosh {
            position: fixed;
            bottom: 0;
            right: 0;
            width: 270px;
            height: 180px;
            pointer-events: none;
            z-index: 10;
        }

        .bottom-right-swoosh svg {
            width: 100%;
            height: 100%;
            display: block;
        }

        /* ==========================================
           ADAPTACIÓN RESPONSIVA (MÓVIL / TABLET)
           ========================================== */
        @media (max-width: 1100px) {
            .desktop-barber-pole {
                display: none;
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 24px 16px 50px;
                justify-content: flex-start;
            }

            /* En móviles el encabezado se coloca arriba en flujo natural, sin superponerse */
            .top-header-right {
                position: relative;
                top: auto;
                right: auto;
                width: 100%;
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
                margin-bottom: 12px;
                z-index: 5;
            }

            .top-header-right .tagline {
                font-size: 10.5px;
                letter-spacing: 1.8px;
                text-align: center;
            }

            .barber-stripes-mini {
                width: 44px;
                height: 4px;
                margin-top: 2px;
            }

            /* Tamaño del logo optimizado para celulares sin desbordar ni chocar */
            .brand-logo-img {
                width: 285px;
                max-width: 86vw;
            }

            .bottom-left-tagline {
                position: relative;
                bottom: auto;
                left: auto;
                margin-top: 26px;
                font-size: 11.5px;
                letter-spacing: 1px;
                text-align: center;
                justify-content: center;
            }

            .bottom-right-swoosh {
                width: 150px;
                height: 105px;
                opacity: 0.35;
                pointer-events: none;
                z-index: 1;
            }

            .login-card {
                padding: 26px 20px 22px;
                border-radius: 16px;
            }

            .card-title {
                font-size: 21px;
            }
        }
    </style>
</head>
<body>

    <!-- Esquina superior derecha: Corte • Estilo • Confianza con barras tricolor -->
    <div class="top-header-right">
        <span class="tagline">CORTE • ESTILO • CONFIANZA</span>
        <div class="barber-stripes-mini">
            <span class="stripe-blue"></span>
            <span class="stripe-red"></span>
            <span class="stripe-white"></span>
        </div>
    </div>

    <!-- Poste de barbería vintage lateral (visible en pantallas de escritorio) -->
    <div class="desktop-barber-pole" aria-hidden="true">
        <div class="pole-ball"></div>
        <div class="pole-cap-top"></div>
        <div class="pole-cylinder">
            <div class="pole-stripes"></div>
            <div class="pole-glass-reflection"></div>
        </div>
        <div class="pole-cap-bottom"></div>
        <div class="pole-finial"></div>
    </div>

    <!-- Contenedor central -->
    <div class="main-auth-wrapper">

        <!-- Logo e identificación de marca -->
        <header class="brand-header">
            <div class="brand-logo-wrapper">
                <img 
                    src="{{ asset('images/logo.png') }}" 
                    alt="Beauty & Barber Studio" 
                    class="brand-logo-img"
                >
            </div>
        </header>

        <!-- Tarjeta del formulario (Glassmorphism oscuro) -->
        <main class="login-card">
            <div class="card-heading">
                <h2 class="card-title">¡Bienvenido de nuevo!</h2>
                <p class="card-subtitle">Inicia sesión para continuar...</p>
            </div>

            <form action="#" method="POST">
                @csrf
                
                <!-- Campo de Correo Electrónico o Usuario -->
                <div class="input-group">
                    <span class="field-icon" aria-hidden="true">
                        <svg viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    <input 
                        type="email" 
                        name="email" 
                        placeholder="Iniciar sesión por correo"
                        aria-label="Iniciar sesión por correo"
                        class="form-input" 
                        pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}" 
                        title="Introduce un correo electrónico válido (ej. usuario@dominio.com)" 
                        required 
                        autofocus
                    >
                </div>

                <!-- Campo de Contraseña con límite de 8 caracteres y botón de mostrar/ocultar con ojo -->
                <div class="input-group password-group">
                    <span class="field-icon" aria-hidden="true">
                        <svg viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Contraseña" 
                        aria-label="Contraseña" 
                        class="form-input" 
                        maxlength="8" 
                        required
                    >
                    <button 
                        type="button" 
                        id="togglePassword" 
                        class="toggle-password-btn" 
                        aria-label="Mostrar u ocultar contraseña"
                    >
                        <!-- Ícono de ojo abierto (predeterminado) -->
                        <svg id="eyeIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        <!-- Ícono de ojo cerrado (al mostrar texto) -->
                        <svg id="eyeOffIcon" class="hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                            <line x1="1" y1="1" x2="23" y2="23"></line>
                        </svg>
                    </button>
                </div>

                <!-- Checkbox Recordarme con fondo rojo (omitido el enlace de olvido de contraseña según instrucción) -->
                <div class="remember-row">
                    <label class="remember-container" for="remember">
                        <input type="checkbox" name="remember" id="remember" class="real-checkbox" checked>
                        <span class="custom-checkbox-box">
                            <svg class="check-svg" viewBox="0 0 12 10" fill="none">
                                <path d="M1.5 5L4.5 8L10.5 1.5" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <span class="remember-label-text">Recordarme</span>
                    </label>
                </div>

                <!-- Botón de Iniciar sesión con fondo rojo y flecha -->
                <button type="submit" class="submit-btn">
                    <span>Iniciar sesión</span>
                    <svg class="btn-arrow" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </form>

            <!-- Enlace de registro inferior -->
            <p class="signup-prompt">
                ¿No tienes una cuenta? <a href="#" class="signup-link">Regístrate</a>
            </p>
        </main>

    </div>

    <!-- Esquina inferior izquierda: Tijeras y lema -->
    <div class="bottom-left-tagline">
        <svg class="scissor-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <circle cx="6" cy="6" r="3"></circle>
            <circle cx="6" cy="18" r="3"></circle>
            <line x1="20" y1="4" x2="8.12" y2="15.88"></line>
            <line x1="14.47" y1="14.48" x2="20" y2="20"></line>
            <line x1="8.12" y1="8.12" x2="12" y2="12"></line>
        </svg>
        <span>TU ESTILO, NUESTRA PASIÓN</span>
    </div>

    <!-- Esquina inferior derecha: Franjas curvas de barbería (Rojo, Blanco y Azul) -->
    <div class="bottom-right-swoosh" aria-hidden="true">
        <svg viewBox="0 0 320 220" fill="none" preserveAspectRatio="none">
            <!-- Franja roja exterior -->
            <path d="M0 220 C100 220, 220 170, 320 0 L320 40 C230 190, 120 220, 35 220 Z" fill="#d1121d" />
            <!-- Franja blanca intermedia -->
            <path d="M35 220 C120 220, 230 190, 320 40 L320 75 C240 205, 140 220, 70 220 Z" fill="#ffffff" />
            <!-- Franja azul interior -->
            <path d="M70 220 C140 220, 240 205, 320 75 L320 110 C250 218, 160 220, 105 220 Z" fill="#143b8c" />
        </svg>
    </div>

    <!-- Lógica JavaScript para alternar visibilidad de contraseña con ícono de ojo -->
    <script>
        const togglePasswordBtn = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        const eyeOffIcon = document.getElementById('eyeOffIcon');

        if (togglePasswordBtn && passwordInput) {
            togglePasswordBtn.addEventListener('click', function () {
                const isPassword = passwordInput.getAttribute('type') === 'password';
                passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                eyeIcon.classList.toggle('hidden', isPassword);
                eyeOffIcon.classList.toggle('hidden', !isPassword);
            });
        }
    </script>

</body>
</html>