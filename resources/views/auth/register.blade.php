<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beauty & Barber Studio - Registro de Cliente</title>
    
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
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background-color: #0b0e14;
            background-image: 
                radial-gradient(circle at center, rgba(11, 14, 20, 0.75) 0%, rgba(7, 9, 13, 0.95) 100%),
                url('https://images.unsplash.com/photo-1503951914875-452162b0f3f1?auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            font-family: 'Montserrat', sans-serif;
            color: #ffffff;
            padding: 6px 16px;
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
            left: 50px;
            top: 50%;
            transform: translateY(-50%);
            width: 76px;
            display: flex;
            flex-direction: column;
            align-items: center;
            z-index: 15;
            pointer-events: none;
            filter: drop-shadow(14px 20px 35px rgba(0, 0, 0, 0.95))
                    drop-shadow(25px 0 50px rgba(16, 52, 138, 0.5));
        }

        /* Halo y sombra azul ambiental que integra el poste al fondo */
        .desktop-barber-pole::before {
            content: '';
            position: absolute;
            top: -25px;
            left: -20px;
            width: 150px;
            height: calc(100% + 50px);
            background: radial-gradient(ellipse at 45% 50%, rgba(20, 65, 160, 0.45) 0%, rgba(10, 32, 85, 0.28) 42%, rgba(5, 15, 45, 0.12) 65%, transparent 78%);
            filter: blur(28px);
            z-index: -1;
            pointer-events: none;
        }

        .pole-ball {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: radial-gradient(circle at 35% 30%, #fff7d6 0%, #e2be72 35%, #b88d37 65%, #5a3e11 95%);
            margin-bottom: -4px;
            z-index: 3;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.6), inset -1px -1px 2px rgba(0, 0, 0, 0.4);
        }

        .pole-cap-top {
            width: 72px;
            height: 40px;
            background: 
                radial-gradient(ellipse at 50% 20%, rgba(255, 255, 255, 0.75) 0%, transparent 60%),
                linear-gradient(90deg, 
                    #432d0c 0%, 
                    #7f5b1d 15%, 
                    #d4a954 35%, 
                    #fff4c2 50%, 
                    #d4a954 65%, 
                    #7f5b1d 85%, 
                    #432d0c 100%
                );
            border-radius: 36px 36px 4px 4px;
            box-shadow: 
                inset 0 2px 4px rgba(255, 255, 255, 0.85),
                inset 0 -3px 4px rgba(0, 0, 0, 0.6),
                0 4px 10px rgba(0, 0, 0, 0.7);
            position: relative;
            z-index: 2;
        }

        .pole-cap-top::after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: -2px;
            width: 76px;
            height: 7px;
            background: linear-gradient(90deg, #48300d 0%, #946c25 20%, #fff2ba 50%, #946c25 80%, #48300d 100%);
            border-radius: 3px;
            box-shadow: 0 3px 5px rgba(0, 0, 0, 0.8);
        }

        .pole-cylinder {
            width: 60px;
            height: 290px;
            position: relative;
            overflow: hidden;
            border-left: 2px solid rgba(255, 255, 255, 0.45);
            border-right: 2px solid rgba(255, 255, 255, 0.45);
            box-shadow: 
                0 0 30px rgba(16, 52, 138, 0.45),
                0 15px 35px rgba(0, 0, 0, 0.85),
                inset 0 0 22px rgba(0, 0, 0, 0.7),
                inset 0 12px 16px rgba(0, 0, 0, 0.65),
                inset 0 -12px 16px rgba(0, 0, 0, 0.65);
            z-index: 1;
        }

        .pole-stripes {
            position: absolute;
            top: -120px;
            left: 0;
            width: 100%;
            height: calc(100% + 240px);
            background: repeating-linear-gradient(
                -45deg,
                #d1121d 0px,
                #d1121d 22px,
                #ffffff 22px,
                #ffffff 44px,
                #143b8c 44px,
                #143b8c 66px,
                #ffffff 66px,
                #ffffff 88px
            );
            animation: barberPoleScroll 4s linear infinite;
        }

        .pole-glass-reflection {
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, 
                rgba(0, 0, 0, 0.65) 0%, 
                rgba(0, 0, 0, 0.15) 16%, 
                rgba(255, 255, 255, 0.55) 32%, 
                rgba(255, 255, 255, 0.12) 50%, 
                transparent 72%, 
                rgba(0, 0, 0, 0.45) 88%, 
                rgba(255, 255, 255, 0.3) 100%
            );
            z-index: 2;
            pointer-events: none;
        }

        .pole-cap-bottom {
            width: 72px;
            height: 40px;
            background: 
                radial-gradient(ellipse at 50% 80%, rgba(255, 255, 255, 0.55) 0%, transparent 60%),
                linear-gradient(90deg, 
                    #432d0c 0%, 
                    #7f5b1d 15%, 
                    #d4a954 35%, 
                    #fff4c2 50%, 
                    #d4a954 65%, 
                    #7f5b1d 85%, 
                    #432d0c 100%
                );
            border-radius: 4px 4px 36px 36px;
            box-shadow: 
                inset 0 -2px 4px rgba(255, 255, 255, 0.7),
                inset 0 3px 4px rgba(0, 0, 0, 0.6),
                0 8px 16px rgba(0, 0, 0, 0.8);
            position: relative;
            z-index: 2;
        }

        .pole-cap-bottom::before {
            content: '';
            position: absolute;
            top: -3px;
            left: -2px;
            width: 76px;
            height: 7px;
            background: linear-gradient(90deg, #48300d 0%, #946c25 20%, #fff2ba 50%, #946c25 80%, #48300d 100%);
            border-radius: 3px;
            box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.6);
        }

        .pole-finial {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: radial-gradient(circle at 35% 30%, #fff7d6 0%, #e2be72 35%, #b88d37 65%, #5a3e11 95%);
            margin-top: -4px;
            z-index: 3;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.75), inset -1px -1px 2px rgba(0, 0, 0, 0.4);
        }

        @keyframes barberPoleScroll {
            0% { transform: translateY(0); }
            100% { transform: translateY(88px); }
        }

        /* ==========================================
           CONTENEDOR PRINCIPAL CENTRADO
           ========================================== */
        .main-auth-wrapper {
            width: 100%;
            max-width: 800px;
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
            margin-bottom: 6px;
        }

        .brand-emblem {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
        }

        .mini-pole {
            width: 14px;
            height: 54px;
            display: flex;
            flex-direction: column;
            align-items: center;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.75))
                    drop-shadow(0 0 8px rgba(20, 59, 140, 0.35));
        }

        .mini-cap {
            width: 14px;
            height: 4px;
            background: linear-gradient(90deg, #533a12 0%, #e2be72 50%, #533a12 100%);
            border-radius: 2px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.6);
        }

        .mini-body {
            width: 10px;
            height: 46px;
            position: relative;
            overflow: hidden;
            border-left: 1px solid rgba(255, 255, 255, 0.55);
            border-right: 1px solid rgba(255, 255, 255, 0.55);
            box-shadow: inset 0 0 4px rgba(0, 0, 0, 0.65);
        }

        .mini-stripes {
            position: absolute;
            top: -120px;
            left: 0;
            width: 100%;
            height: calc(100% + 240px);
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
            width: 104px;
            height: 94px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .barber-icon-center img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            filter: drop-shadow(0 4px 10px rgba(0,0,0,0.7));
            transition: transform 0.25s ease;
        }

        .barber-icon-center img:hover {
            transform: scale(1.03);
        }

        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border-width: 0;
        }

        /* ==========================================
           TARJETA DEL FORMULARIO (GLASSMORPHISM)
           ========================================== */
        .register-card {
            width: 100%;
            background: rgba(14, 18, 24, 0.82);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 16px;
            padding: 12px 24px 10px;
            box-shadow: 
                0 30px 70px rgba(0, 0, 0, 0.88),
                0 10px 25px rgba(0, 0, 0, 0.6),
                0 0 45px rgba(20, 59, 140, 0.18),
                0 0 0 1px rgba(255, 255, 255, 0.06);
            margin-top: 2px;
            display: flex;
            flex-direction: column;
        }

        .card-heading {
            text-align: center;
            margin-bottom: 8px;
        }

        .card-badge {
            display: inline-block;
            padding: 2px 8px;
            background: rgba(209, 18, 29, 0.15);
            border: 1px solid rgba(209, 18, 29, 0.4);
            border-radius: 20px;
            font-size: 9.5px;
            font-weight: 600;
            letter-spacing: 1.2px;
            color: #ff525e;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .card-title {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 19px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 2px;
            letter-spacing: -0.01em;
        }

        .card-subtitle {
            font-size: 11.5px;
            color: rgba(255, 255, 255, 0.75);
            font-weight: 400;
        }

        /* ==========================================
           DISTRIBUCIÓN Y GRID DE 2 CAMPOS POR FILA
           ========================================== */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            row-gap: 7px;
            column-gap: 14px;
            width: 100%;
            margin-bottom: 8px;
        }

        .field-container {
            display: flex;
            flex-direction: column;
            gap: 2px;
            width: 100%;
        }

        .field-label {
            font-size: 11px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.85);
            letter-spacing: 0.4px;
            margin-left: 2px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .field-label .required-mark {
            color: #ff3b47;
            font-weight: 700;
        }

        .field-label .optional-mark {
            color: rgba(255, 255, 255, 0.45);
            font-weight: 400;
            font-size: 10px;
        }

        .input-group {
            position: relative;
            width: 100%;
            display: flex;
            align-items: center;
        }

        .field-icon {
            position: absolute;
            left: 11px;
            width: 15px;
            height: 15px;
            color: rgba(255, 255, 255, 0.6);
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
            height: 36px;
            padding: 0 12px 0 36px;
            font-size: 13px;
            font-family: 'Montserrat', sans-serif;
            color: #ffffff;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.22);
            border-radius: 8px;
            outline: none;
            transition: border-color 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
        }

        /* Selector de fecha nativo en dark mode */
        input[type="date"].form-input {
            color-scheme: dark;
        }

        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.42);
            font-size: 13px;
        }

        .form-input:focus {
            border-color: rgba(255, 255, 255, 0.65);
            background: rgba(255, 255, 255, 0.09);
            box-shadow: 0 0 0 2px rgba(209, 18, 29, 0.25);
        }

        .input-group:focus-within .field-icon {
            color: #ffffff;
        }

        /* Ajuste específico para contraseña con botón de ojo */
        .password-group .form-input {
            padding-right: 44px;
        }

        /* Ocultar el ícono de ojo nativo de navegadores como Edge */
        input[type="password"]::-ms-reveal,
        input[type="password"]::-ms-clear {
            display: none;
        }

        .toggle-password-btn {
            position: absolute;
            right: 10px;
            width: 32px;
            height: 32px;
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, 0.6);
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
            width: 19px;
            height: 19px;
        }

        .hidden {
            display: none !important;
        }

        /* ==========================================
           ELEMENTOS DE ACEPTACIÓN LEGAL
           ========================================== */
        .legal-section {
            display: flex;
            flex-direction: column;
            gap: 5px;
            margin-top: 0;
            margin-bottom: 0;
            width: 100%;
        }

        .legal-group {
            display: flex;
            align-items: flex-start;
            width: 100%;
        }

        .legal-checkbox-container {
            display: flex;
            align-items: flex-start;
            gap: 8px;
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
            width: 15px;
            height: 15px;
            min-width: 15px;
            border-radius: 3px;
            background: #d1121d;
            border: 1px solid #d1121d;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            margin-top: 1px;
        }

        .real-checkbox:not(:checked) + .custom-checkbox-box {
            background: transparent;
            border: 1.5px solid rgba(255, 255, 255, 0.45);
        }

        .check-svg {
            width: 10px;
            height: 9px;
            display: block;
        }

        .real-checkbox:not(:checked) + .custom-checkbox-box .check-svg {
            display: none;
        }

        .legal-text {
            font-size: 11px;
            line-height: 1.35;
            color: rgba(255, 255, 255, 0.85);
            font-weight: 400;
        }

        .legal-link {
            color: #ff3b47;
            text-decoration: underline;
            text-underline-offset: 2px;
            transition: color 0.2s ease;
        }

        .legal-link:hover {
            color: #ffffff;
        }

        /* ==========================================
           BOTÓN DE ACCIÓN PRINCIPAL (ROJO DE MARCA)
           ========================================== */
        .submit-btn {
            width: 100%;
            height: 38px;
            margin-top: 8px;
            padding: 0 18px;
            background: #d1121d;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-family: 'Montserrat', sans-serif;
            font-size: 14px;
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
            width: 16px;
            height: 16px;
            transition: transform 0.2s ease;
        }

        .submit-btn:hover .btn-arrow {
            transform: translateX(3px);
        }

        /* ==========================================
           ENLACE HACIA INICIO DE SESIÓN
           ========================================== */
        .login-prompt {
            margin-top: 8px;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.75);
            text-align: center;
        }

        .login-link {
            color: #d1121d;
            text-decoration: none;
            font-weight: 600;
            margin-left: 4px;
            transition: color 0.2s ease, text-decoration 0.2s ease;
        }

        .login-link:hover {
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
            filter: drop-shadow(0 3px 8px rgba(0, 0, 0, 0.9));
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
            filter: drop-shadow(0 10px 25px rgba(0, 0, 0, 0.85))
                    drop-shadow(0 0 35px rgba(20, 59, 140, 0.25));
        }

        .bottom-right-swoosh svg {
            width: 100%;
            height: 100%;
            display: block;
        }

        /* ==========================================
           ADAPTACIÓN RESPONSIVA (DESKTOP SIN SCROLL)
           ========================================== */
        @media (min-width: 769px) {
            html, body {
                height: 100vh;
                overflow: hidden;
            }
        }

        @media (max-width: 1100px) {
            .desktop-barber-pole {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .top-header-right,
            .bottom-right-swoosh {
                transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                            transform 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                            visibility 0.3s;
            }

            .top-header-right {
                z-index: 5;
            }

            .bottom-right-swoosh {
                z-index: 1;
            }

            .top-header-right.scrolled-hidden {
                opacity: 0 !important;
                visibility: hidden !important;
                transform: translateY(-12px);
                pointer-events: none !important;
            }

            .bottom-right-swoosh.scrolled-hidden {
                opacity: 0 !important;
                visibility: hidden !important;
                transform: translateY(16px);
                pointer-events: none !important;
            }
        }

        @media (max-width: 600px) {
            body {
                padding: 40px 14px 60px;
                justify-content: flex-start;
            }

            .barber-icon-center {
                width: 90px;
                height: 82px;
            }

            .mini-pole {
                width: 12px;
                height: 46px;
            }

            .mini-cap {
                width: 12px;
                height: 3px;
            }

            .mini-body {
                width: 9px;
                height: 40px;
            }

            .brand-emblem {
                gap: 12px;
            }

            .form-grid {
                grid-template-columns: 1fr;
                row-gap: 18px;
                column-gap: 12px;
                margin-bottom: 20px;
            }

            .top-header-right {
                top: 14px;
                right: 14px;
            }

            .top-header-right .tagline {
                font-size: 9px;
                letter-spacing: 1.2px;
            }

            .barber-stripes-mini {
                width: 36px;
                height: 4px;
            }

            .register-card {
                padding: 22px 18px 20px;
                border-radius: 16px;
            }

            .card-title {
                font-size: 21px;
            }

            .card-subtitle {
                font-size: 12px;
            }

            .bottom-left-tagline {
                position: relative;
                bottom: auto;
                left: auto;
                margin-top: 24px;
                font-size: 11.5px;
                text-align: center;
                justify-content: center;
            }

            .bottom-right-swoosh {
                width: 150px;
                height: 100px;
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
            <h1 class="sr-only">Beauty &amp; Barber Studio - Registro de Cliente</h1>
            <div class="brand-emblem">
                <!-- Mini poste izquierdo -->
                <div class="mini-pole" aria-hidden="true">
                    <div class="mini-cap"></div>
                    <div class="mini-body">
                        <div class="mini-stripes"></div>
                        <div class="mini-glass"></div>
                    </div>
                    <div class="mini-cap"></div>
                </div>

                <!-- Logo oficial Beauty & Barber Studio -->
                <div class="barber-icon-center">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo Beauty & Barber Studio" class="brand-logo-img">
                </div>

                <!-- Mini poste derecho -->
                <div class="mini-pole" aria-hidden="true">
                    <div class="mini-cap"></div>
                    <div class="mini-body">
                        <div class="mini-stripes"></div>
                        <div class="mini-glass"></div>
                    </div>
                    <div class="mini-cap"></div>
                </div>
            </div>
        </header>

        <!-- Tarjeta del formulario (Glassmorphism oscuro) -->
        <main class="register-card">
            <div class="card-heading">
                <span class="card-badge">Nuevo Cliente</span>
                <h2 class="card-title">Crea tu cuenta</h2>
                <p class="card-subtitle">Completa tus datos para agendar tus citas con facilidad</p>
            </div>

            <form action="{{ route('register.post') }}" method="POST" id="registerForm">
                @csrf
                
                <!-- Cuadrícula con 2 campos por fila -->
                <div class="form-grid">
                    <!-- Fila 1: Nombre -->
                    <div class="field-container">
                        <label for="name" class="field-label">
                            <span>Nombre</span>
                            <span class="required-mark" title="Obligatorio">*</span>
                        </label>
                        <div class="input-group">
                            <span class="field-icon" aria-hidden="true">
                                <svg viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                </svg>
                            </span>
                            <input 
                                type="text" 
                                id="name"
                                name="name" 
                                placeholder="Nombre(s)" 
                                aria-label="Nombre" 
                                class="form-input" 
                                required 
                                autofocus
                            >
                        </div>
                    </div>

                    <!-- Fila 1: Apellidos -->
                    <div class="field-container">
                        <label for="apellidos" class="field-label">
                            <span>Apellidos</span>
                            <span class="required-mark" title="Obligatorio">*</span>
                        </label>
                        <div class="input-group">
                            <span class="field-icon" aria-hidden="true">
                                <svg viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                </svg>
                            </span>
                            <input 
                                type="text" 
                                id="apellidos"
                                name="apellidos" 
                                placeholder="Apellidos" 
                                aria-label="Apellidos" 
                                class="form-input" 
                                required
                            >
                        </div>
                    </div>
                
                    <!-- Fila 2: Teléfono -->
                    <div class="field-container">
                        <label for="telefono" class="field-label">
                            <span>Teléfono</span>
                            <span class="required-mark" title="Obligatorio">*</span>
                        </label>
                        <div class="input-group">
                            <span class="field-icon" aria-hidden="true">
                                <svg viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 4V3z" />
                                </svg>
                            </span>
                            <input 
                                type="tel" 
                                id="telefono"
                                name="telefono" 
                                placeholder="10 dígitos" 
                                aria-label="Teléfono" 
                                class="form-input" 
                                pattern="[0-9]{10}"
                                maxlength="10"
                                required
                            >
                        </div>
                    </div>
                
                    <!-- Fila 2: Correo Electrónico -->
                    <div class="field-container">
                        <label for="email" class="field-label">
                            <span>Correo electrónico</span>
                            <span class="required-mark" title="Obligatorio">*</span>
                        </label>
                        <div class="input-group">
                            <span class="field-icon" aria-hidden="true">
                                <svg viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                </svg>
                            </span>
                            <input 
                                type="email" 
                                id="email"
                                name="email" 
                                placeholder="correo@ejemplo.com" 
                                aria-label="Correo electrónico" 
                                class="form-input" 
                                required
                            >
                        </div>
                    </div>

                    <!-- Fila 3: Contraseña -->
                    <div class="field-container">
                        <label for="password" class="field-label">
                            <span>Contraseña</span>
                            <span class="required-mark" title="Obligatorio">*</span>
                        </label>
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
                                minlength="8"
                                maxlength="12"
                                required
                            >
                            <button 
                                type="button" 
                                id="togglePassword" 
                                class="toggle-password-btn" 
                                aria-label="Mostrar u ocultar contraseña"
                            >
                                <svg id="eyeOffIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <svg id="eyeIcon" class="hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                    <line x1="1" y1="1" x2="23" y2="23"></line>
                                </svg>
                            </button>
                        </div>
                        <!-- Validaciones visuales -->
                        <ul id="password-requirements" style="list-style: none; padding: 0; margin-top: 5px; font-size: 10.5px; color: rgba(255,255,255,0.5);">
                            <li id="req-length" style="display: flex; align-items: center; gap: 4px; transition: color 0.2s;"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13l4 4L19 7"></path></svg> Entre 8 y 12 caracteres</li>
                            <li id="req-upper" style="display: flex; align-items: center; gap: 4px; transition: color 0.2s;"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13l4 4L19 7"></path></svg> Una mayúscula</li>
                            <li id="req-lower" style="display: flex; align-items: center; gap: 4px; transition: color 0.2s;"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13l4 4L19 7"></path></svg> Una minúscula</li>
                            <li id="req-number" style="display: flex; align-items: center; gap: 4px; transition: color 0.2s;"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13l4 4L19 7"></path></svg> Un número</li>
                            <li id="req-special" style="display: flex; align-items: center; gap: 4px; transition: color 0.2s;"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13l4 4L19 7"></path></svg> Carácter especial</li>
                        </ul>
                    </div>

                    <!-- Fila 3: Fecha de Nacimiento (Opcional) -->
                    <div class="field-container">
                        <label for="fecha_nacimiento" class="field-label">
                            <span>Fecha de nacimiento</span>
                            <span class="optional-mark">(opcional)</span>
                        </label>
                        <div class="input-group">
                            <span class="field-icon" aria-hidden="true">
                                <svg viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                </svg>
                            </span>
                            <input 
                                type="date" 
                                id="fecha_nacimiento" 
                                name="fecha_nacimiento" 
                                aria-label="Fecha de nacimiento" 
                                class="form-input"
                                min="1920-01-01"
                                max="{{ date('Y-m-d', strtotime('-5 years')) }}"
                            >
                        </div>
                    </div>
                </div>

                <!-- Elementos de aceptación legal -->
                <div class="legal-section">
                    <!-- Casilla de aceptación de Términos y Condiciones -->
                    <div class="legal-group">
                        <label class="legal-checkbox-container" for="terms">
                            <input type="checkbox" id="terms" name="terms" class="real-checkbox">
                            <span class="custom-checkbox-box">
                                <svg class="check-svg" viewBox="0 0 12 10" fill="none">
                                    <path d="M1.5 5L4.5 8L10.5 1.5" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="legal-text">
                                Acepto los <a href="#" id="linkTerms" class="legal-link">Términos y Condiciones</a> del estudio <span class="required-mark" style="color: #ff3b47; font-weight: 700;" title="Obligatorio">*</span>
                            </span>
                        </label>
                    </div>

                    <!-- Casilla de aceptación del Aviso de Privacidad -->
                    <div class="legal-group">
                        <label class="legal-checkbox-container" for="privacy">
                            <input type="checkbox" id="privacy" name="privacy" class="real-checkbox">
                            <span class="custom-checkbox-box">
                                <svg class="check-svg" viewBox="0 0 12 10" fill="none">
                                    <path d="M1.5 5L4.5 8L10.5 1.5" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="legal-text">
                                He leído y acepto el <a href="#" id="linkPrivacy" class="legal-link">Aviso de Privacidad</a> <span class="required-mark" style="color: #ff3b47; font-weight: 700;" title="Obligatorio">*</span>
                            </span>
                        </label>
                    </div>
                </div>

                <!-- Botón de acción principal (Rojo de marca) -->
                <button type="submit" class="submit-btn" id="submitRegisterBtn">
                    <span>Registrarse</span>
                    <svg class="btn-arrow" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </form>

            <!-- Enlace hacia el Inicio de Sesión -->
            <p class="login-prompt">
                ¿Ya tienes una cuenta? <a href="{{ route('login') }}" class="login-link">Inicia sesión</a>
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
            <path d="M0 220 C100 220, 220 170, 320 0 L320 40 C230 190, 120 220, 35 220 Z" fill="#d1121d" />
            <path d="M35 220 C120 220, 230 190, 320 40 L320 75 C240 205, 140 220, 70 220 Z" fill="#ffffff" />
            <path d="M70 220 C140 220, 240 205, 320 75 L320 110 C250 218, 160 220, 105 220 Z" fill="#143b8c" />
        </svg>
    </div>

    <!-- Scripts de interactividad -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Manejo de errores de validación con SweetAlert2
            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: '{{ $errors->first() }}',
                    background: '#0b0e14',
                    color: '#fff',
                    confirmButtonColor: '#d1121d'
                });
            @endif

            // Validación del formulario antes de enviar
            const registerForm = document.getElementById('registerForm');
            const termsCheckbox = document.getElementById('terms');
            const privacyCheckbox = document.getElementById('privacy');

            if(registerForm) {
                registerForm.addEventListener('submit', function(e) {
                    if (!termsCheckbox.checked || !privacyCheckbox.checked) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'warning',
                            title: 'Políticas requeridas',
                            text: 'Por favor, asegúrate de aceptar los Términos y Condiciones y el Aviso de Privacidad para continuar.',
                            background: '#0b0e14',
                            color: '#fff',
                            confirmButtonColor: '#d1121d'
                        });
                    }
                });
            }

            // Ventana modal de Términos y Condiciones
            const linkTerms = document.getElementById('linkTerms');
            if (linkTerms) {
                linkTerms.addEventListener('click', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Términos y Condiciones',
                        html: '<div style="text-align: left; font-size: 13.5px; line-height: 1.6; color: rgba(255,255,255,0.85); max-height: 320px; overflow-y: auto; padding-right: 12px; font-family: \'Montserrat\', sans-serif;">' +
                              '<h4 style="color: #fff; margin-bottom: 6px; font-weight: 600;">1. Uso del Servicio</h4>' +
                              '<p style="margin-bottom: 14px;">Al registrarte en Beauty & Barber Studio, aceptas utilizar nuestros servicios para uso personal, cumpliendo con nuestras políticas de respeto hacia nuestro personal y otros clientes.</p>' +
                              '<h4 style="color: #fff; margin-bottom: 6px; font-weight: 600;">2. Política de Cancelaciones</h4>' +
                              '<p style="margin-bottom: 14px;">Las citas deben cancelarse o reprogramarse con al menos 24 horas de anticipación. De lo contrario, nos reservamos el derecho de generar una penalización en su próxima visita.</p>' +
                              '<h4 style="color: #fff; margin-bottom: 6px; font-weight: 600;">3. Responsabilidad</h4>' +
                              '<p>No nos hacemos responsables por la pérdida, daño o robo de objetos personales olvidados en nuestras instalaciones. Le sugerimos mantener sus pertenencias cerca.</p>' +
                              '</div>',
                        background: 'rgba(14, 18, 24, 0.95)',
                        color: '#ffffff',
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#d1121d',
                        width: '550px',
                        backdrop: `rgba(0,0,0,0.6) backdrop-filter: blur(4px)`
                    });
                });
            }

            // Ventana modal de Aviso de Privacidad
            const linkPrivacy = document.getElementById('linkPrivacy');
            if (linkPrivacy) {
                linkPrivacy.addEventListener('click', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Aviso de Privacidad',
                        html: '<div style="text-align: left; font-size: 13.5px; line-height: 1.6; color: rgba(255,255,255,0.85); max-height: 320px; overflow-y: auto; padding-right: 12px; font-family: \'Montserrat\', sans-serif;">' +
                              '<h4 style="color: #fff; margin-bottom: 6px; font-weight: 600;">1. Datos Recopilados</h4>' +
                              '<p style="margin-bottom: 14px;">Recopilamos tu nombre, apellidos, teléfono, correo electrónico y opcionalmente tu fecha de nacimiento únicamente con el propósito de gestionar tus citas, crear un perfil de cliente y brindarte un mejor servicio.</p>' +
                              '<h4 style="color: #fff; margin-bottom: 6px; font-weight: 600;">2. Uso de la Información</h4>' +
                              '<p style="margin-bottom: 14px;">Tus datos son estrictamente confidenciales. No serán vendidos, alquilados ni compartidos con terceros bajo ninguna circunstancia ajena a nuestra operación interna.</p>' +
                              '<h4 style="color: #fff; margin-bottom: 6px; font-weight: 600;">3. Seguridad</h4>' +
                              '<p>Tus contraseñas y datos personales están cifrados en nuestros sistemas y resguardados en servidores seguros con protocolos de autenticación estrictos para prevenir cualquier acceso no autorizado.</p>' +
                              '</div>',
                        background: 'rgba(14, 18, 24, 0.95)',
                        color: '#ffffff',
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#d1121d',
                        width: '550px',
                        backdrop: `rgba(0,0,0,0.6) backdrop-filter: blur(4px)`
                    });
                });
            }
        });

        // Alternancia de visibilidad de contraseña
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

        // Validación de contraseña en tiempo real
        if (passwordInput) {
            const reqLength = document.getElementById('req-length');
            const reqUpper = document.getElementById('req-upper');
            const reqLower = document.getElementById('req-lower');
            const reqNumber = document.getElementById('req-number');
            const reqSpecial = document.getElementById('req-special');

            passwordInput.addEventListener('input', function() {
                const val = passwordInput.value;
                
                // Longitud 8 a 12
                if (val.length >= 8 && val.length <= 12) {
                    reqLength.style.color = '#10b981';
                } else {
                    reqLength.style.color = 'rgba(255,255,255,0.5)';
                }

                // Mayúscula
                if (/[A-Z]/.test(val)) {
                    reqUpper.style.color = '#10b981';
                } else {
                    reqUpper.style.color = 'rgba(255,255,255,0.5)';
                }

                // Minúscula
                if (/[a-z]/.test(val)) {
                    reqLower.style.color = '#10b981';
                } else {
                    reqLower.style.color = 'rgba(255,255,255,0.5)';
                }

                // Número
                if (/[0-9]/.test(val)) {
                    reqNumber.style.color = '#10b981';
                } else {
                    reqNumber.style.color = 'rgba(255,255,255,0.5)';
                }

                // Carácter especial
                if (/[\W_]/.test(val)) {
                    reqSpecial.style.color = '#10b981';
                } else {
                    reqSpecial.style.color = 'rgba(255,255,255,0.5)';
                }
            });
        }

        // Ocultar elementos decorativos al desplazarse en diseño móvil
        const topHeaderRight = document.querySelector('.top-header-right');
        const bottomRightSwoosh = document.querySelector('.bottom-right-swoosh');

        function updateMobileDecorations() {
            if (window.innerWidth <= 768) {
                const isScrolled = window.scrollY > 15;
                if (topHeaderRight) {
                    topHeaderRight.classList.toggle('scrolled-hidden', isScrolled);
                }
                if (bottomRightSwoosh) {
                    bottomRightSwoosh.classList.toggle('scrolled-hidden', isScrolled);
                }
            } else {
                if (topHeaderRight) {
                    topHeaderRight.classList.remove('scrolled-hidden');
                }
                if (bottomRightSwoosh) {
                    bottomRightSwoosh.classList.remove('scrolled-hidden');
                }
            }
        }

        window.addEventListener('scroll', updateMobileDecorations, { passive: true });
        window.addEventListener('resize', updateMobileDecorations, { passive: true });
        updateMobileDecorations();
    </script>

</body>
</html>
