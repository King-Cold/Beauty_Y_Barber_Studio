<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beauty & Barber Studio - Excelencia en Estilo y Cuidado Personal</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,600&display=swap" rel="stylesheet">

    <style>
        /* ==========================================================================
           REINICIO Y VARIABLES DE DISEÑO (ESTILO DE LUJO OSCURO CON DORADO Y VINO)
           ========================================================================== */
        :root {
            --bg-dark: #0b0e14;
            --bg-darker: #06080c;
            --bg-card: rgba(18, 24, 38, 0.75);
            --bg-navbar: rgba(11, 14, 20, 0.94);
            --gold-primary: #d4af37;
            --gold-light: #f3e5ab;
            --gold-dark: #997c1d;
            --wine-primary: #991b1b;
            --wine-hover: #b91c1c;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border-gold: rgba(212, 175, 55, 0.25);
            --border-subtle: rgba(255, 255, 255, 0.08);
            --navbar-height: 80px;
            --font-main: 'Montserrat', sans-serif;
            --font-title: 'Playfair Display', serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
            font-family: var(--font-main);
            background-color: var(--bg-dark);
            color: var(--text-main);
        }

        body {
            min-height: 100vh;
            overflow-x: hidden;
            background: radial-gradient(circle at 50% 0%, #151c2c 0%, #0b0e14 70%, #06080c 100%);
            padding-top: var(--navbar-height);
        }

        /* Bloqueo de scroll cuando el menú móvil está activo */
        body.menu-open {
            overflow: hidden;
        }

        /* ==========================================================================
           BARRA SUPERIOR DE NAVEGACIÓN (HEADER / NAVBAR)
           ========================================================================== */
        .main-navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: var(--navbar-height);
            background: var(--bg-navbar);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border-gold);
            z-index: 1000;
            transition: all 0.3s ease;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.6);
        }

        .navbar-container {
            max-width: 1320px;
            height: 100%;
            margin: 0 auto;
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        /* 1. Logotipo e Identidad de Marca */
        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: var(--text-main);
            flex-shrink: 0;
            transition: opacity 0.2s ease, transform 0.2s ease;
        }

        .navbar-brand:hover {
            opacity: 0.95;
            transform: scale(1.01);
        }

        .brand-logo-img {
            height: 48px;
            width: auto;
            max-width: 160px;
            object-fit: contain;
            filter: drop-shadow(0 2px 6px rgba(0, 0, 0, 0.5));
        }

        .brand-text-block {
            display: flex;
            flex-direction: column;
            line-height: 1.15;
        }

        .brand-title {
            font-family: var(--font-title);
            font-size: 19px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: 0.5px;
        }

        .brand-title span {
            color: var(--gold-primary);
        }

        .brand-subtitle {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 2.2px;
            color: var(--gold-light);
            font-weight: 500;
        }

        /* 2. Menú de Enlaces de Escritorio (Desktop Navigation) */
        .desktop-nav {
            display: flex;
            align-items: center;
        }

        .nav-menu {
            display: flex;
            align-items: center;
            gap: 32px;
            list-style: none;
        }

        .nav-item-link {
            text-decoration: none;
            color: #e2e8f0;
            font-size: 14.5px;
            font-weight: 500;
            letter-spacing: 0.3px;
            position: relative;
            padding: 8px 0;
            transition: color 0.25s ease;
        }

        .nav-item-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0%;
            height: 2px;
            background: linear-gradient(90deg, var(--gold-primary), var(--gold-light));
            transition: width 0.3s ease;
            border-radius: 2px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.4);
        }

        .nav-item-link:hover,
        .nav-item-link.active {
            color: var(--gold-light);
            font-weight: 600;
        }

        .nav-item-link:hover::after,
        .nav-item-link.active::after {
            width: 100%;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.5);
        }

        /* 3. Botones de Autenticación Permanentes (Desktop & Mobile) */
        .nav-actions-wrapper {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
        }

        .btn-auth {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 8px;
            font-family: var(--font-main);
            font-size: 13.5px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.25s ease;
            cursor: pointer;
            white-space: nowrap;
        }

        /* Botón Iniciar Sesión (Elegante con marco dorado y brillo) */
        .btn-auth-login {
            background: rgba(212, 175, 55, 0.08);
            border: 1.5px solid var(--gold-primary);
            color: var(--gold-light);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .btn-auth-login:hover {
            background: var(--gold-primary);
            color: #0b0e14;
            border-color: var(--gold-light);
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(212, 175, 55, 0.4);
        }

        /* Botón Crear Cuenta / Registrarse (Rojo vino distinguido con relieve) */
        .btn-auth-register {
            background: linear-gradient(135deg, var(--wine-primary) 0%, #7f1d1d 100%);
            border: 1.5px solid rgba(255, 255, 255, 0.15);
            color: #ffffff;
            box-shadow: 0 3px 12px rgba(153, 27, 27, 0.4);
        }

        .btn-auth-register:hover {
            background: linear-gradient(135deg, var(--wine-hover) 0%, #991b1b 100%);
            border-color: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(153, 27, 27, 0.55);
        }

        /* Indicador sutil de usuario conectado (si ya tiene sesión) */
        .user-session-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 12px;
            background: rgba(5, 150, 105, 0.12);
            border: 1px solid rgba(5, 150, 105, 0.35);
            border-radius: 8px;
            color: #d1fae5;
            font-size: 12.5px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .user-session-badge:hover {
            background: rgba(5, 150, 105, 0.22);
            border-color: rgba(5, 150, 105, 0.55);
            color: #ffffff;
        }

        .status-dot-active {
            width: 8px;
            height: 8px;
            background-color: #059669;
            border-radius: 50%;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.4);
        }

        /* 4. Botón de Menú Hamburguesa (Mobile) */
        .hamburger-btn {
            display: none;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 5px;
            width: 44px;
            height: 44px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-subtle);
            border-radius: 8px;
            cursor: pointer;
            padding: 8px;
            transition: background 0.2s ease, border-color 0.2s ease;
        }

        .hamburger-btn:hover {
            background: rgba(212, 175, 55, 0.15);
            border-color: var(--gold-primary);
        }

        .hamburger-bar {
            width: 24px;
            height: 2.5px;
            background-color: var(--text-main);
            border-radius: 2px;
            transition: all 0.3s ease;
        }

        /* ==========================================================================
           MENÚ LATERAL DESPLEGABLE PARA CELULARES (MOBILE DRAWER & BACKDROP)
           ========================================================================== */
        .mobile-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            z-index: 1100;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .mobile-backdrop.is-active {
            opacity: 1;
            visibility: visible;
        }

        .mobile-drawer {
            position: fixed;
            top: 0;
            right: 0;
            width: 320px;
            max-width: 85%;
            height: 100%;
            background: #0d121c;
            border-left: 1px solid var(--border-gold);
            z-index: 1200;
            transform: translateX(100%);
            transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            flex-direction: column;
            padding: 24px;
            box-shadow: -8px 0 30px rgba(0, 0, 0, 0.7);
        }

        .mobile-drawer.is-active {
            transform: translateX(0);
        }

        .drawer-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border-subtle);
            margin-bottom: 24px;
        }

        .drawer-brand-title {
            font-family: var(--font-title);
            font-size: 17px;
            font-weight: 700;
            color: var(--gold-light);
        }

        .close-drawer-btn {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid var(--border-subtle);
            color: #ffffff;
            width: 38px;
            height: 38px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            cursor: pointer;
            transition: all 0.2s ease;
            line-height: 1;
        }

        .close-drawer-btn:hover {
            background: var(--wine-primary);
            border-color: #b91c1c;
            color: #ffffff;
        }

        /* Botones de acción en la cabecera del menú móvil */
        .drawer-auth-section {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 28px;
        }

        .drawer-auth-section .btn-auth {
            display: flex !important;
            width: 100%;
            padding: 12px 16px;
            font-size: 14px;
        }

        /* Lista de enlaces del menú móvil */
        .drawer-menu-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .drawer-nav-item {
            width: 100%;
        }

        .drawer-nav-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-decoration: none;
            color: #cbd5e1;
            font-size: 16px;
            font-weight: 600;
            padding: 12px 14px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid transparent;
            transition: all 0.25s ease;
        }

        .drawer-nav-link:hover,
        .drawer-nav-link:active,
        .drawer-nav-link.active {
            color: var(--gold-light);
            background: rgba(212, 175, 55, 0.15);
            border-color: var(--border-gold);
            transform: translateX(4px);
            box-shadow: inset 3px 0 0 var(--gold-primary);
        }

        .drawer-link-arrow {
            font-size: 14px;
            color: var(--gold-primary);
        }

        /* ==========================================================================
           SECCIONES DE DESTINO (ESTRUCTURAS PREPARADAS PARA SMOOTH SCROLL)
           ========================================================================== */
        section {
            scroll-margin-top: calc(var(--navbar-height) + 20px);
            padding: 90px 24px;
        }

        section:not(.hero-section) {
            max-width: 1320px;
            margin: 0 auto;
        }

        .section-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-tag {
            display: inline-block;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 2.5px;
            color: var(--gold-primary);
            font-weight: 700;
            margin-bottom: 8px;
        }

        .section-title {
            font-family: var(--font-title);
            font-size: 36px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 14px;
        }

        .section-subtitle {
            font-size: 16px;
            color: var(--text-muted);
            max-width: 650px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .section-placeholder-card {
            background: var(--bg-card);
            border: 1px solid var(--border-gold);
            border-radius: 16px;
            padding: 60px 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(10px);
        }

        /* ==========================================================================
           SECCIÓN DE SERVICIOS (CATÁLOGO, TARJETAS, FILTROS Y ESTADO VACÍO)
           ========================================================================== */
        .services-filter-bar {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 45px;
        }

        .filter-btn {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--border-gold);
            border-radius: 30px;
            padding: 9px 24px;
            color: #cbd5e1;
            font-family: var(--font-main);
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 0.3px;
            cursor: pointer;
            transition: all 0.25s ease;
            backdrop-filter: blur(8px);
        }

        .filter-btn:hover {
            background: rgba(212, 175, 55, 0.15);
            color: var(--gold-light);
            border-color: rgba(212, 175, 55, 0.6);
            transform: translateY(-2px);
        }

        .filter-btn.active {
            background: linear-gradient(135deg, var(--gold-primary) 0%, #a8841a 100%);
            color: #0b0e14;
            border-color: var(--gold-light);
            font-weight: 700;
            box-shadow: 0 4px 18px rgba(212, 175, 55, 0.45);
            transform: translateY(-2px);
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
            gap: 28px;
            width: 100%;
        }

        .service-card {
            background: var(--bg-card);
            border: 1px solid var(--border-gold);
            border-radius: 16px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: all 0.32s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.45);
            backdrop-filter: blur(10px);
            position: relative;
        }

        .service-card.is-hidden {
            display: none !important;
        }

        .service-card:hover {
            transform: translateY(-6px);
            border-color: rgba(212, 175, 55, 0.7);
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.65), 0 0 20px rgba(212, 175, 55, 0.25);
        }

        .service-img-wrapper {
            position: relative;
            width: 100%;
            height: 205px;
            overflow: hidden;
            background: #111726;
        }

        .service-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
            display: block;
        }

        .service-img.is-fallback-logo {
            object-fit: contain;
            padding: 28px;
            background: radial-gradient(circle, #1a2336 0%, #0d121c 100%);
        }

        .service-card:hover .service-img {
            transform: scale(1.06);
        }

        .service-category-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background: rgba(11, 14, 20, 0.88);
            backdrop-filter: blur(8px);
            border: 1px solid var(--border-gold);
            color: var(--gold-light);
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 4px 10px;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
        }

        .service-duration-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            background: rgba(153, 27, 27, 0.92);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #ffffff;
            font-size: 11.5px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
        }

        .service-card-body {
            padding: 22px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            justify-content: space-between;
            gap: 16px;
        }

        .service-card-title {
            font-family: var(--font-title);
            font-size: 20px;
            font-weight: 700;
            color: #ffffff;
            line-height: 1.25;
            letter-spacing: 0.3px;
        }

        .service-card-desc {
            font-size: 13.5px;
            color: var(--text-muted);
            line-height: 1.55;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin-bottom: 4px;
        }

        .service-card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-top: 1px solid var(--border-subtle);
            padding-top: 16px;
            margin-top: auto;
            gap: 12px;
        }

        .service-price-block {
            display: flex;
            flex-direction: column;
            line-height: 1.15;
        }

        .price-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            font-weight: 600;
        }

        .price-amount {
            font-family: var(--font-main);
            font-size: 19px;
            font-weight: 800;
            color: var(--gold-light);
        }

        .price-currency {
            font-size: 11.5px;
            color: var(--gold-primary);
            font-weight: 600;
        }

        /* Estado Vacío de Servicios */
        .services-empty-state {
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 24px;
            text-align: center;
            background: var(--bg-card);
            border: 1px dashed var(--border-gold);
            border-radius: 16px;
            margin-top: 10px;
            backdrop-filter: blur(10px);
        }

        .services-empty-state.is-visible {
            display: flex;
        }

        .empty-state-icon {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: rgba(212, 175, 55, 0.1);
            border: 1px solid var(--border-gold);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gold-primary);
            margin-bottom: 18px;
        }

        .empty-state-title {
            font-family: var(--font-title);
            font-size: 22px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 8px;
        }

        .empty-state-text {
            font-size: 15px;
            color: var(--text-muted);
            max-width: 520px;
            margin-bottom: 22px;
            line-height: 1.6;
        }

        .btn-empty-reset {
            background: rgba(212, 175, 55, 0.15);
            border: 1px solid var(--gold-primary);
            color: var(--gold-light);
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.25s ease;
        }

        .btn-empty-reset:hover {
            background: var(--gold-primary);
            color: #0b0e14;
        }

        /* ==========================================================================
           SECCIÓN DE NUESTRO EQUIPO (TARJETAS DE PROFESIONALES, EXPERIENCIA, AVATARES)
           ========================================================================== */
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
            gap: 28px;
            width: 100%;
        }

        .team-card {
            background: var(--bg-card);
            border: 1px solid var(--border-gold);
            border-radius: 16px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: all 0.32s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.45);
            backdrop-filter: blur(10px);
            position: relative;
        }

        .team-card:hover {
            transform: translateY(-6px);
            border-color: rgba(212, 175, 55, 0.7);
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.65), 0 0 20px rgba(212, 175, 55, 0.25);
        }

        .team-img-wrapper {
            position: relative;
            width: 100%;
            height: 280px;
            overflow: hidden;
            background: radial-gradient(circle, #1a2336 0%, #0d121c 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .team-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center top;
            transition: transform 0.5s ease;
            display: block;
        }

        .team-card:hover .team-img {
            transform: scale(1.05);
        }

        .team-img.is-avatar-svg {
            object-fit: contain;
            padding: 18px;
        }

        .team-exp-badge {
            position: absolute;
            bottom: 12px;
            left: 12px;
            background: rgba(11, 14, 20, 0.90);
            backdrop-filter: blur(8px);
            border: 1px solid var(--border-gold);
            color: var(--gold-light);
            font-size: 11.5px;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
            letter-spacing: 0.3px;
        }

        .team-card-body {
            padding: 24px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            flex-grow: 1;
            gap: 8px;
        }

        .team-card-name {
            font-family: var(--font-title);
            font-size: 21px;
            font-weight: 700;
            color: #ffffff;
            line-height: 1.25;
            letter-spacing: 0.3px;
        }

        .team-card-role {
            font-size: 11.5px;
            text-transform: uppercase;
            letter-spacing: 1.6px;
            color: var(--gold-primary);
            font-weight: 600;
        }

        .team-tags-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 6px;
            margin-top: 8px;
        }

        .team-tag-pill {
            font-size: 11px;
            color: #cbd5e1;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            padding: 4px 10px;
            font-weight: 500;
        }

        .team-empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 24px;
            text-align: center;
            background: var(--bg-card);
            border: 1px dashed var(--border-gold);
            border-radius: 16px;
            backdrop-filter: blur(10px);
        }

        /* ==========================================================================
           SECCIÓN DE HORARIOS (TABLA SEMANAL Y AVISO DE ESTADO EN TIEMPO REAL)
           ========================================================================== */
        .schedule-section-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            max-width: 860px;
            margin: 0 auto;
            gap: 28px;
        }

        /* Banner de estado en tiempo real (Abierto / Cerrado) */
        .schedule-status-banner {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 12px 28px;
            border-radius: 40px;
            font-size: 14.5px;
            font-weight: 700;
            letter-spacing: 0.3px;
            backdrop-filter: blur(12px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
            transition: all 0.3s ease;
        }

        .schedule-status-banner.status-open {
            background: rgba(5, 150, 105, 0.14);
            border: 1.5px solid rgba(5, 150, 105, 0.4);
            color: #a7f3d0;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.35);
        }

        .schedule-status-banner.status-closed {
            background: rgba(153, 27, 27, 0.18);
            border: 1.5px solid rgba(153, 27, 27, 0.45);
            color: #fca5a5;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.35);
        }

        .status-indicator-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .dot-pulse-green {
            background-color: #059669;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.35);
            animation: pulseGreen 2.5s infinite ease-in-out;
        }

        .dot-pulse-wine {
            background-color: #991b1b;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.35);
            animation: pulseWine 2.5s infinite ease-in-out;
        }

        @keyframes pulseGreen {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.15); opacity: 0.85; }
        }

        @keyframes pulseWine {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.15); opacity: 0.85; }
        }

        .status-banner-text {
            font-family: var(--font-main);
            line-height: 1.2;
        }

        /* Tarjeta contenedora de la tabla semanal */
        .schedule-card-wrapper {
            background: var(--bg-card);
            border: 1px solid var(--border-gold);
            border-radius: 18px;
            overflow: hidden;
            width: 100%;
            box-shadow: 0 12px 36px rgba(0, 0, 0, 0.55);
            backdrop-filter: blur(12px);
        }

        .schedule-table-header {
            display: grid;
            grid-template-columns: 2fr 3fr 1.5fr;
            padding: 16px 28px;
            background: rgba(255, 255, 255, 0.03);
            border-bottom: 1px solid var(--border-subtle);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--gold-light);
            font-weight: 700;
        }

        .schedule-list {
            list-style: none;
            display: flex;
            flex-direction: column;
        }

        .schedule-row {
            display: grid;
            grid-template-columns: 2fr 3fr 1.5fr;
            align-items: center;
            padding: 16px 28px;
            border-bottom: 1px solid var(--border-subtle);
            transition: background 0.2s ease, transform 0.2s ease;
        }

        .schedule-row:last-child {
            border-bottom: none;
        }

        .schedule-row:hover {
            background: rgba(212, 175, 55, 0.06);
        }

        .schedule-row.is-today {
            background: rgba(212, 175, 55, 0.12);
            border-left: 3.5px solid var(--gold-primary);
        }

        .schedule-day-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .day-name {
            font-size: 15px;
            font-weight: 600;
            color: #ffffff;
        }

        .today-pill {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            background: linear-gradient(135deg, var(--gold-primary) 0%, #a8841a 100%);
            color: #0b0e14;
            padding: 2px 8px;
            border-radius: 12px;
            font-weight: 800;
            box-shadow: 0 2px 6px rgba(212, 175, 55, 0.3);
        }

        .schedule-hours-info {
            font-size: 14.5px;
            color: #e2e8f0;
            font-weight: 500;
        }

        .hours-range {
            color: #f1f5f9;
            font-family: var(--font-main);
            letter-spacing: 0.3px;
        }

        .hours-closed-text {
            color: var(--text-muted);
            font-style: italic;
            font-size: 13.5px;
        }

        .schedule-status-badge-wrapper {
            display: flex;
            justify-content: flex-end;
        }

        .schedule-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .schedule-badge.badge-open {
            background: rgba(5, 150, 105, 0.14);
            border: 1px solid rgba(5, 150, 105, 0.35);
            color: #6ee7b7;
        }

        .schedule-badge.badge-closed {
            background: rgba(153, 27, 27, 0.16);
            border: 1px solid rgba(153, 27, 27, 0.38);
            color: #fca5a5;
        }

        @media (max-width: 640px) {
            .schedule-table-header {
                display: none;
            }

            .schedule-row {
                grid-template-columns: 1fr;
                gap: 8px;
                padding: 16px 20px;
            }

            .schedule-status-badge-wrapper {
                justify-content: flex-start;
            }
        }

        /* ==========================================================================
           SECCIÓN DE UBICACIÓN Y CONTACTO (MAPA INTERACTIVO, FACHADA Y DIRECCIÓN)
           ========================================================================== */
        .location-container {
            display: flex;
            flex-direction: column;
            gap: 32px;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }

        .location-info-card {
            background: var(--bg-card);
            border: 1px solid var(--border-gold);
            border-radius: 18px;
            padding: 32px;
            display: grid;
            grid-template-columns: 1.3fr 1fr;
            gap: 32px;
            align-items: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(12px);
        }

        .location-address-block {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .location-block-header {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--gold-light);
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 700;
        }

        .location-address-text {
            font-size: 16.5px;
            color: #ffffff;
            line-height: 1.55;
            font-weight: 500;
        }

        .location-actions-row {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 4px;
        }

        .btn-copy-address {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: rgba(212, 175, 55, 0.12);
            border: 1.5px solid var(--gold-primary);
            color: var(--gold-light);
            border-radius: 10px;
            font-family: var(--font-main);
            font-size: 13.5px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.25s ease;
        }

        .btn-copy-address:hover {
            background: var(--gold-primary);
            color: #0b0e14;
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(212, 175, 55, 0.4);
        }

        .location-contact-block {
            display: flex;
            flex-direction: column;
            gap: 14px;
            padding-left: 32px;
            border-left: 1px solid var(--border-subtle);
        }

        .contact-block-title {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--gold-light);
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .contact-block-desc {
            font-size: 13.5px;
            color: var(--text-muted);
            line-height: 1.5;
        }

        .contact-buttons-wrapper {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn-phone-call {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 22px;
            background: linear-gradient(135deg, var(--wine-primary) 0%, #7f1d1d 100%);
            border: 1.5px solid rgba(255, 255, 255, 0.2);
            color: #ffffff;
            border-radius: 10px;
            font-family: var(--font-main);
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.25s ease;
            box-shadow: 0 4px 16px rgba(153, 27, 27, 0.4);
        }

        .btn-phone-call:hover {
            background: linear-gradient(135deg, var(--wine-hover) 0%, #991b1b 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(153, 27, 27, 0.6);
            color: #ffffff;
        }

        .btn-open-maps {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 22px;
            background: rgba(255, 255, 255, 0.05);
            border: 1.5px solid var(--border-gold);
            color: var(--gold-light);
            border-radius: 10px;
            font-family: var(--font-main);
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.25s ease;
        }

        .btn-open-maps:hover {
            background: rgba(212, 175, 55, 0.2);
            border-color: var(--gold-light);
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(212, 175, 55, 0.35);
        }

        /* Cuadrícula visual: Fachada al lado del mapa interactivo */
        .location-visual-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 28px;
            width: 100%;
        }

        .facade-card {
            background: var(--bg-card);
            border: 1px solid var(--border-gold);
            border-radius: 18px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(12px);
            position: relative;
        }

        .facade-img-wrapper {
            position: relative;
            width: 100%;
            height: 420px;
            overflow: hidden;
            background: #0b0e14;
        }

        .facade-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .facade-card:hover .facade-img {
            transform: scale(1.04);
        }

        .facade-badge-overlay {
            position: absolute;
            bottom: 16px;
            left: 16px;
            right: 16px;
            background: rgba(11, 14, 20, 0.90);
            border: 1px solid var(--border-gold);
            backdrop-filter: blur(10px);
            padding: 12px 18px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .facade-badge-title {
            font-family: var(--font-title);
            font-size: 15px;
            font-weight: 700;
            color: var(--gold-light);
        }

        .facade-badge-desc {
            font-size: 11.5px;
            color: var(--text-muted);
        }

        .map-card {
            background: var(--bg-card);
            border: 1px solid var(--border-gold);
            border-radius: 18px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(12px);
            position: relative;
            height: 420px;
        }

        .map-iframe {
            width: 100%;
            height: 100%;
            border: none;
            display: block;
            filter: contrast(102%) brightness(95%);
        }

        /* Notificación flotante Toast al copiar dirección */
        .toast-notification {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 2500;
            background: #0f172a;
            border: 1.5px solid var(--gold-primary);
            color: #ffffff;
            border-radius: 12px;
            padding: 14px 24px;
            font-size: 14px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 12px 36px rgba(0, 0, 0, 0.8), 0 2px 8px rgba(212, 175, 55, 0.15);
            opacity: 0;
            transform: translateY(20px);
            pointer-events: none;
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        .toast-notification.is-visible {
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }

        @media (max-width: 968px) {
            .location-info-card {
                grid-template-columns: 1fr;
                gap: 24px;
            }

            .location-contact-block {
                padding-left: 0;
                border-left: none;
                border-top: 1px solid var(--border-subtle);
                padding-top: 24px;
            }

            .location-visual-grid {
                grid-template-columns: 1fr;
            }

            .facade-img-wrapper,
            .map-card {
                height: 350px;
            }
        }

        /* ==========================================================================
           SECCIÓN HERO DE BIENVENIDA (PORTADA CON FOTO DE IMPACTO Y BOTÓN DE RESERVA)
           ========================================================================== */
        .hero-section {
            width: 100%;
            min-height: calc(92vh - var(--navbar-height));
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 90px 20px 100px;
            margin: 0;
            position: relative;
            background-color: #0b0e14;
            background-image: 
                radial-gradient(circle at center, rgba(11, 14, 20, 0.65) 0%, rgba(7, 9, 13, 0.90) 100%),
                url('{{ asset('images/hero-bg.jpg') }}');
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            border-bottom: 1px solid var(--border-gold);
            overflow: hidden;
        }

        .hero-inner-container {
            max-width: 1080px;
            width: 100%;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 8px 22px;
            background: rgba(212, 175, 55, 0.12);
            border: 1px solid var(--border-gold);
            border-radius: 30px;
            color: var(--gold-light);
            font-size: 12.5px;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 16px;
            backdrop-filter: blur(8px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
        }

        .hero-studio-name {
            font-family: var(--font-title);
            font-size: 22px;
            font-weight: 700;
            color: var(--gold-primary);
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 14px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.7);
        }

        .hero-title {
            font-family: var(--font-title);
            font-size: 52px;
            font-weight: 800;
            line-height: 1.16;
            margin-bottom: 20px;
            max-width: 920px;
            color: #ffffff;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.8);
        }

        .hero-title span {
            background: linear-gradient(135deg, #ffffff 10%, var(--gold-light) 50%, var(--gold-primary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-description {
            font-size: 18px;
            color: #e2e8f0;
            max-width: 760px;
            margin-bottom: 38px;
            line-height: 1.65;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.7);
        }

        .hero-description strong {
            color: var(--gold-light);
            font-weight: 600;
        }

        /* Contenedor de Botones de Acción (CTA) */
        .hero-cta-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 18px;
            flex-wrap: wrap;
            margin-bottom: 44px;
        }

        /* Botón Principal: Agendar Cita con cambio sutil de color y tamaño en hover */
        .btn-hero-cta {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 16px 36px;
            background: linear-gradient(135deg, var(--wine-primary) 0%, #7f1d1d 100%);
            color: #ffffff;
            border: 1.5px solid rgba(212, 175, 55, 0.4);
            border-radius: 12px;
            font-family: var(--font-main);
            font-size: 16.5px;
            font-weight: 700;
            letter-spacing: 0.4px;
            text-decoration: none;
            cursor: pointer;
            box-shadow: 0 6px 24px rgba(153, 27, 27, 0.5), 0 0 16px rgba(212, 175, 55, 0.25);
            transition: all 0.28s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .btn-hero-cta:hover {
            background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%);
            transform: translateY(-3px) scale(1.03);
            border-color: var(--gold-light);
            box-shadow: 0 10px 32px rgba(185, 28, 28, 0.65), 0 0 24px rgba(212, 175, 55, 0.45);
            color: #ffffff;
        }

        .btn-hero-cta:active {
            transform: translateY(-1px) scale(1.01);
        }

        .btn-hero-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 15px 28px;
            background: rgba(255, 255, 255, 0.05);
            color: #e2e8f0;
            border: 1.5px solid rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            font-family: var(--font-main);
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            backdrop-filter: blur(8px);
            transition: all 0.25s ease;
        }

        .btn-hero-secondary:hover {
            background: rgba(212, 175, 55, 0.12);
            color: var(--gold-light);
            border-color: var(--border-gold);
            transform: translateY(-2px);
        }

        /* Características destacadas al pie de la portada */
        .hero-highlights {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 32px;
            flex-wrap: wrap;
            padding-top: 24px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            max-width: 820px;
            width: 100%;
        }

        .hero-highlight-item {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            color: #cbd5e1;
            font-size: 13.5px;
            font-weight: 500;
        }

        .highlight-dot {
            width: 8px;
            height: 8px;
            background: var(--gold-primary);
            border-radius: 50%;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.5);
        }

        /* ==========================================================================
           PIE DE PÁGINA INSTITUCIONAL (FOOTER, REDES SOCIALES Y LEGAL - SUBTAREA 7)
           ========================================================================== */
        .site-footer {
            background: linear-gradient(180deg, #07090d 0%, #040508 100%);
            border-top: 1px solid var(--border-gold);
            color: #94a3b8;
            padding: 70px 24px 36px;
            position: relative;
            z-index: 10;
        }

        .footer-container {
            max-width: 1280px;
            margin: 0 auto;
            width: 100%;
        }

        .footer-top-grid {
            display: grid;
            grid-template-columns: 1.6fr 1fr 1.4fr;
            gap: 48px;
            margin-bottom: 50px;
        }

        /* Columna 1: Identidad y Dirección breve */
        .footer-col-brand {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .footer-brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .footer-brand-logo {
            height: 44px;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 2px 6px rgba(0, 0, 0, 0.6));
        }

        .footer-brand-title {
            font-family: var(--font-title);
            font-size: 21px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: 0.5px;
            line-height: 1.2;
            display: block;
        }

        .footer-brand-title .gold-accent {
            color: var(--gold-primary);
        }

        .footer-brand-subtitle {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--gold-light);
            font-weight: 600;
            display: block;
        }

        .footer-col-desc {
            font-size: 14px;
            line-height: 1.65;
            color: #94a3b8;
            max-width: 380px;
            margin: 0;
        }

        .footer-address-mini {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            color: #cbd5e1;
            font-size: 13px;
            font-weight: 500;
            margin-top: 4px;
        }

        .footer-address-mini svg {
            color: var(--gold-primary);
            flex-shrink: 0;
        }

        /* Columna 2: Enlaces Rápidos */
        .footer-col-heading {
            font-family: var(--font-title);
            font-size: 17px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: 0.5px;
            margin-bottom: 20px;
            position: relative;
            display: inline-block;
        }

        .footer-col-heading::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -6px;
            width: 28px;
            height: 2px;
            background: var(--gold-primary);
            border-radius: 2px;
        }

        .footer-links-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .footer-nav-link {
            color: #94a3b8;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: color 0.25s ease, transform 0.25s ease;
        }

        .footer-nav-link:hover {
            color: var(--gold-light);
            transform: translateX(4px);
        }

        /* Columna 3: Redes Sociales Oficiales */
        .footer-social-intro {
            font-size: 14px;
            line-height: 1.6;
            color: #94a3b8;
            margin-bottom: 20px;
            max-width: 360px;
        }

        .footer-social-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .social-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 18px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 12px;
            color: #e2e8f0;
            font-size: 13.5px;
            font-weight: 600;
            text-decoration: none;
            backdrop-filter: blur(8px);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            cursor: pointer;
        }

        .social-btn .social-icon {
            flex-shrink: 0;
            transition: transform 0.25s ease;
        }

        .social-btn:hover {
            transform: translateY(-2px);
        }

        .social-btn:hover .social-icon {
            transform: scale(1.12);
        }

        .social-facebook:hover {
            background: rgba(30, 64, 175, 0.18);
            border-color: rgba(59, 130, 246, 0.4);
            color: #bfdbfe;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.4);
        }

        .social-instagram:hover {
            background: rgba(159, 18, 57, 0.18);
            border-color: rgba(225, 29, 72, 0.4);
            color: #fbcfe8;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.4);
        }

        .social-tiktok:hover {
            background: rgba(15, 118, 110, 0.18);
            border-color: rgba(20, 184, 166, 0.4);
            color: #99f6e4;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.4);
        }

        /* Divisor */
        .footer-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent 0%, rgba(212, 175, 55, 0.35) 50%, transparent 100%);
            margin-bottom: 28px;
        }

        /* Franja Inferior: Copyright y Legal */
        .footer-bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            font-size: 13.5px;
        }

        .footer-copyright {
            color: #94a3b8;
            font-weight: 500;
        }

        .footer-legal-links {
            display: inline-flex;
            align-items: center;
            gap: 12px;
        }

        .legal-action-btn {
            background: none;
            border: none;
            color: #94a3b8;
            font-family: inherit;
            font-size: 13.5px;
            font-weight: 500;
            cursor: pointer;
            padding: 0;
            text-decoration: underline;
            text-underline-offset: 4px;
            transition: color 0.2s ease;
        }

        .legal-action-btn:hover {
            color: var(--gold-light);
        }

        .legal-dot {
            color: rgba(255, 255, 255, 0.3);
            font-size: 11px;
        }

        /* Modal Legal Informativo (Aviso de Privacidad y Términos y Condiciones) */
        .legal-modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.78);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.25s ease;
            padding: 20px;
        }

        .legal-modal-backdrop.is-open {
            opacity: 1;
            pointer-events: auto;
        }

        .legal-modal-card {
            background: #0d1117;
            border: 1px solid var(--border-gold);
            border-radius: 18px;
            max-width: 620px;
            width: 100%;
            max-height: 85vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.85), 0 0 24px rgba(212, 175, 55, 0.15);
            transform: scale(0.95) translateY(12px);
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            overflow: hidden;
        }

        .legal-modal-backdrop.is-open .legal-modal-card {
            transform: scale(1) translateY(0);
        }

        .legal-modal-header {
            padding: 22px 26px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(255, 255, 255, 0.02);
        }

        .legal-modal-title {
            font-family: var(--font-title);
            font-size: 20px;
            font-weight: 700;
            color: #ffffff;
            margin: 0;
            letter-spacing: 0.4px;
        }

        .legal-modal-close {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: #cbd5e1;
            border-radius: 8px;
            width: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .legal-modal-close:hover {
            background: rgba(153, 27, 27, 0.3);
            border-color: #b91c1c;
            color: #ffffff;
        }

        .legal-modal-body {
            padding: 26px;
            overflow-y: auto;
            color: #cbd5e1;
            font-size: 14px;
            line-height: 1.7;
        }

        .legal-modal-body h4 {
            color: #ffffff;
            font-family: var(--font-title);
            font-size: 15.5px;
            font-weight: 600;
            margin: 18px 0 6px;
            letter-spacing: 0.3px;
        }

        .legal-modal-body h4:first-child {
            margin-top: 0;
        }

        .legal-modal-body p {
            margin: 0 0 14px;
            color: #94a3b8;
        }

        .legal-modal-body p:last-child {
            margin-bottom: 0;
        }

        .legal-modal-footer {
            padding: 16px 26px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            justify-content: flex-end;
            background: rgba(255, 255, 255, 0.02);
        }

        .btn-legal-dismiss {
            padding: 10px 24px;
            background: linear-gradient(135deg, var(--wine-primary) 0%, #7f1d1d 100%);
            color: #ffffff;
            border: 1px solid rgba(212, 175, 55, 0.35);
            border-radius: 10px;
            font-family: var(--font-main);
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-legal-dismiss:hover {
            background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%);
            border-color: var(--gold-light);
            transform: translateY(-1px);
        }

        /* ==========================================================================
           ADAPTACIÓN RESPONSIVA (MEDIA QUERIES)
           ========================================================================== */
        @media (max-width: 1024px) {
            .nav-menu {
                gap: 20px;
            }

            .brand-title {
                font-size: 17px;
            }

            .btn-auth {
                padding: 8px 14px;
                font-size: 12.5px;
            }

            /* Adaptación del pie de página para tablets */
            .footer-top-grid {
                grid-template-columns: 1fr 1fr;
                gap: 40px;
            }

            .footer-col-brand {
                grid-column: 1 / -1;
            }

            .footer-col-desc {
                max-width: 600px;
            }
        }

        @media (max-width: 868px) {
            /* Ocultar menú horizontal tradicional */
            .desktop-nav {
                display: none !important;
            }

            /* En celulares, ocultar los botones de la barra superior fija porque se guardan en el menú hamburguesa */
            .main-navbar .btn-auth,
            .main-navbar .user-session-badge {
                display: none !important;
            }

            /* Mostrar botón de hamburguesa en la barra superior */
            .hamburger-btn {
                display: flex !important;
            }

            .nav-actions-wrapper {
                gap: 0;
            }

            .brand-subtitle {
                display: none;
            }

            .hero-title {
                font-size: 34px;
            }

            .hero-description {
                font-size: 15px;
            }

            .section-title {
                font-size: 28px;
            }

            /* Adaptación del pie de página para dispositivos medianos y móviles */
            .site-footer {
                padding: 55px 20px 32px;
            }

            .footer-top-grid {
                grid-template-columns: 1fr;
                gap: 36px;
                margin-bottom: 40px;
            }

            .footer-col-brand {
                grid-column: auto;
            }

            .footer-col-desc,
            .footer-social-intro {
                max-width: 100%;
            }

            .footer-social-buttons {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
            }

            .social-btn {
                flex: 1 1 calc(33.333% - 10px);
                min-width: 130px;
                justify-content: center;
                padding: 11px 14px;
                font-size: 13px;
            }

            .footer-bottom {
                flex-direction: column;
                text-align: center;
                gap: 16px;
                align-items: center;
                justify-content: center;
            }

            .footer-copyright {
                text-align: center;
                font-size: 13px;
                line-height: 1.5;
            }

            .footer-legal-links {
                justify-content: center;
                flex-wrap: wrap;
                gap: 12px;
            }
        }

        @media (max-width: 480px) {
            .navbar-container {
                padding: 0 16px;
            }

            .brand-logo-img {
                height: 40px;
            }

            .brand-title {
                font-size: 16px;
            }

            /* Pie de página para celulares estrechos */
            .site-footer {
                padding: 42px 16px 24px;
            }

            .footer-top-grid {
                gap: 28px;
                margin-bottom: 32px;
            }

            .footer-brand {
                gap: 10px;
            }

            .footer-brand-logo {
                height: 38px;
            }

            .footer-brand-title {
                font-size: 18.5px;
            }

            .footer-col-desc {
                font-size: 13px;
                line-height: 1.6;
            }

            .footer-address-mini {
                font-size: 12.5px;
                flex-wrap: wrap;
            }

            .footer-col-heading {
                font-size: 16px;
                margin-bottom: 16px;
            }

            .footer-nav-link {
                font-size: 13.5px;
            }

            .footer-social-intro {
                font-size: 13px;
            }

            .footer-social-buttons {
                flex-direction: column;
                width: 100%;
                gap: 8px;
            }

            .social-btn {
                width: 100%;
                min-width: 100%;
                justify-content: center;
                padding: 12px 16px;
            }

            .footer-bottom {
                gap: 14px;
            }

            .footer-copyright {
                font-size: 12.5px;
            }

            .footer-legal-links {
                flex-direction: column;
                gap: 10px;
                width: 100%;
            }

            .legal-dot {
                display: none;
            }

            .legal-action-btn {
                font-size: 13px;
            }

            /* Modal adaptativo a pantalla móvil */
            .legal-modal-backdrop {
                padding: 12px;
            }

            .legal-modal-card {
                max-height: 92vh;
                border-radius: 14px;
            }

            .legal-modal-header {
                padding: 16px 18px;
            }

            .legal-modal-title {
                font-size: 17.5px;
            }

            .legal-modal-body {
                padding: 18px;
                font-size: 13px;
                line-height: 1.6;
            }

            .legal-modal-body h4 {
                font-size: 14.5px;
                margin: 14px 0 4px;
            }

            .legal-modal-footer {
                padding: 12px 18px;
            }

            .btn-legal-dismiss {
                width: 100%;
                text-align: center;
                padding: 11px 18px;
            }
        }
    </style>
</head>
<body>

    <!-- ==========================================================================
         BARRA SUPERIOR DE NAVEGACIÓN (HEADER FIJO CON BRANDING Y ACCESOS)
         ========================================================================== -->
    <header class="main-navbar" id="mainNavbar">
        <div class="navbar-container">

            <!-- Identidad de Marca / Logo -->
            <a href="#inicio" class="navbar-brand" id="brandLogoLink" aria-label="Beauty & Barber Studio - Inicio">
                <img 
                    src="{{ asset('images/logo.png') }}" 
                    alt="Logo Beauty & Barber Studio" 
                    class="brand-logo-img"
                    onerror="this.style.display='none'"
                >
                <div class="brand-text-block">
                    <span class="brand-title">Beauty <span>& Barber</span></span>
                    <span class="brand-subtitle">Studio Profesional</span>
                </div>
            </a>

            <!-- Menú de Enlaces para Computadoras (Desktop Navigation) -->
            <nav class="desktop-nav" id="desktopNav" aria-label="Navegación principal">
                <ul class="nav-menu">
                    <li><a href="#inicio" class="nav-item-link active" id="navLinkInicio">Inicio</a></li>
                    <li><a href="#servicios" class="nav-item-link" id="navLinkServicios">Servicios</a></li>
                    <li><a href="#equipo" class="nav-item-link" id="navLinkEquipo">Nuestro Equipo</a></li>
                    <li><a href="#horarios" class="nav-item-link" id="navLinkHorarios">Horarios</a></li>
                    <li><a href="#ubicacion" class="nav-item-link" id="navLinkUbicacion">Ubicación</a></li>
                </ul>
            </nav>

            <!-- Botones Permanentes de Iniciar Sesión y Crear Cuenta -->
            <div class="nav-actions-wrapper" id="navAuthGroup">
                <!-- Botón Iniciar Sesión (Siempre visible y operativo) -->
                <a 
                    href="{{ route('login') }}" 
                    class="btn-auth btn-auth-login" 
                    id="btnNavLogin" 
                    title="Iniciar sesión en Beauty & Barber Studio"
                >
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                        <polyline points="10 17 15 12 10 7"></polyline>
                        <line x1="15" y1="12" x2="3" y2="12"></line>
                    </svg>
                    <span>Iniciar Sesión</span>
                </a>

                <!-- Botón Crear Cuenta (Siempre visible y operativo) -->
                <a 
                    href="{{ route('register') }}" 
                    class="btn-auth btn-auth-register" 
                    id="btnNavRegister" 
                    title="Crear una nueva cuenta de cliente"
                >
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="8.5" cy="7" r="4"></circle>
                        <line x1="20" y1="8" x2="20" y2="14"></line>
                        <line x1="23" y1="11" x2="17" y2="11"></line>
                    </svg>
                    <span>Crear Cuenta</span>
                </a>

                <!-- Botón Hamburguesa para Móviles -->
                <button 
                    type="button" 
                    class="hamburger-btn" 
                    id="hamburgerBtn" 
                    aria-label="Abrir menú de navegación" 
                    aria-expanded="false" 
                    aria-controls="mobileDrawer"
                >
                    <span class="hamburger-bar"></span>
                    <span class="hamburger-bar"></span>
                    <span class="hamburger-bar"></span>
                </button>
            </div>

        </div>
    </header>

    <!-- ==========================================================================
         MENÚ DESPLEGABLE PARA CELULARES (MOBILE DRAWER & TELÓN DE FONDO)
         ========================================================================== -->
    <div class="mobile-backdrop" id="mobileDrawerBackdrop" aria-hidden="true"></div>

    <aside class="mobile-drawer" id="mobileDrawer" aria-label="Menú de navegación para dispositivos móviles">
        
        <!-- Cabecera del Cajón Móvil con Botón Cerrar (X) -->
        <div class="drawer-header">
            <span class="drawer-brand-title">Beauty & Barber</span>
            <button 
                type="button" 
                class="close-drawer-btn" 
                id="closeDrawerBtn" 
                aria-label="Cerrar menú de navegación"
            >
                &times;
            </button>
        </div>

        <!-- Botones de Autenticación Permanentes dentro del Menú Móvil -->
        <div class="drawer-auth-section">
            <a href="{{ route('login') }}" class="btn-auth btn-auth-login" id="drawerBtnLogin">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                    <polyline points="10 17 15 12 10 7"></polyline>
                    <line x1="15" y1="12" x2="3" y2="12"></line>
                </svg>
                <span>Iniciar Sesión</span>
            </a>

            <a href="{{ route('register') }}" class="btn-auth btn-auth-register" id="drawerBtnRegister">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="8.5" cy="7" r="4"></circle>
                    <line x1="20" y1="8" x2="20" y2="14"></line>
                    <line x1="23" y1="11" x2="17" y2="11"></line>
                </svg>
                <span>Crear Cuenta</span>
            </a>
        </div>

        <!-- Lista de Enlaces en el Menú Móvil con Flecha -->
        <ul class="drawer-menu-list">
            <li class="drawer-nav-item">
                <a href="#inicio" class="drawer-nav-link active" id="drawerLinkInicio">
                    <span>Inicio</span>
                    <span class="drawer-link-arrow">&rarr;</span>
                </a>
            </li>
            <li class="drawer-nav-item">
                <a href="#servicios" class="drawer-nav-link" id="drawerLinkServicios">
                    <span>Servicios</span>
                    <span class="drawer-link-arrow">&rarr;</span>
                </a>
            </li>
            <li class="drawer-nav-item">
                <a href="#equipo" class="drawer-nav-link" id="drawerLinkEquipo">
                    <span>Nuestro Equipo</span>
                    <span class="drawer-link-arrow">&rarr;</span>
                </a>
            </li>
            <li class="drawer-nav-item">
                <a href="#horarios" class="drawer-nav-link" id="drawerLinkHorarios">
                    <span>Horarios</span>
                    <span class="drawer-link-arrow">&rarr;</span>
                </a>
            </li>
            <li class="drawer-nav-item">
                <a href="#ubicacion" class="drawer-nav-link" id="drawerLinkUbicacion">
                    <span>Ubicación</span>
                    <span class="drawer-link-arrow">&rarr;</span>
                </a>
            </li>
        </ul>

    </aside>

    <!-- ==========================================================================
         SECCIONES DE CONTENIDO DE LA PÁGINA (CONEXIÓN VISUAL DE ANCLAS)
         ========================================================================== -->
    <main>
        
        <!-- Sección 1: Portada (Hero Section) -->
        <section id="inicio" class="hero-section">
            <div class="hero-inner-container">
                <div class="hero-badge">
                    <span class="highlight-dot" aria-hidden="true"></span>
                    <span>Barbería Clásica &bull; Estilismo Integral &bull; Spa Facial</span>
                </div>

                <div class="hero-studio-name">Beauty &amp; Barber Studio</div>

                <h1 class="hero-title">
                    Arte, Distinción y Cuidado para tu <span>Mejor Versión</span>
                </h1>

                <p class="hero-description">
                    Bienvenido a <strong>Beauty & Barber Studio</strong>. Te ofrecemos una experiencia única de corte de cabello, afeitado tradicional y cuidado personal con los profesionales más destacados en un entorno sofisticado y de máxima comodidad.
                </p>

                <div class="hero-cta-wrapper">
                    <a href="{{ route('landing.agendar') }}" class="btn-hero-cta" id="btnHeroAgendar" title="Agendar cita en Beauty & Barber Studio">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <span>Agendar Cita</span>
                    </a>

                    <a href="#servicios" class="btn-hero-secondary" id="btnHeroServicios" title="Ver catálogo de servicios disponibles">
                        <span>Ver Servicios</span>
                        <span class="btn-arrow" aria-hidden="true">&darr;</span>
                    </a>
                </div>

                <div class="hero-highlights">
                    <div class="hero-highlight-item">
                        <span class="highlight-dot" aria-hidden="true"></span>
                        <span>Atención Personalizada</span>
                    </div>
                    <div class="hero-highlight-item">
                        <span class="highlight-dot" aria-hidden="true"></span>
                        <span>Profesionales Certificados</span>
                    </div>
                    <div class="hero-highlight-item">
                        <span class="highlight-dot" aria-hidden="true"></span>
                        <span>Instalaciones de Primer Nivel</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sección 2: Servicios Ofrecidos -->
        <section id="servicios">
            <div class="section-header">
                <span class="section-tag">Nuestras Especialidades</span>
                <h2 class="section-title">Servicios que Ofrecemos</h2>
                <p class="section-subtitle">
                    Descubre nuestro catálogo completo de cortes, afeitados, tratamientos capilares y faciales diseñados para realzar tu estilo.
                </p>
            </div>

            @php
                $categoryLabels = [
                    'barberia' => 'Barbería',
                    'estetica' => 'Estilismo y Estética',
                    'estilismo' => 'Estilismo',
                    'tratamientos' => 'Tratamientos',
                    'facial' => 'Faciales y Spa',
                ];

                $servicePhotos = [
                    1 => 'https://images.unsplash.com/photo-1503951914875-452162b0f3f1?auto=format&fit=crop&w=700&q=80',
                    2 => 'https://images.unsplash.com/photo-1622286342621-4bd786c2447c?auto=format&fit=crop&w=700&q=80',
                    3 => 'https://images.unsplash.com/photo-1599351431202-1e0f0137899a?auto=format&fit=crop&w=700&q=80',
                    4 => 'https://images.unsplash.com/photo-1517832606589-7629c3397143?auto=format&fit=crop&w=700&q=80',
                    5 => 'https://images.unsplash.com/photo-1560066984-138dadb4c035?auto=format&fit=crop&w=700&q=80',
                    6 => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&w=700&q=80',
                    7 => 'https://images.unsplash.com/photo-1527799820374-dcf8d9d4a388?auto=format&fit=crop&w=700&q=80',
                    8 => 'https://images.unsplash.com/photo-1516975080664-ed2fc6a32937?auto=format&fit=crop&w=700&q=80',
                ];

                $categoryFallbacks = [
                    'barberia' => 'https://images.unsplash.com/photo-1503951914875-452162b0f3f1?auto=format&fit=crop&w=700&q=80',
                    'estetica' => 'https://images.unsplash.com/photo-1560066984-138dadb4c035?auto=format&fit=crop&w=700&q=80',
                    'estilismo' => 'https://images.unsplash.com/photo-1560066984-138dadb4c035?auto=format&fit=crop&w=700&q=80',
                    'tratamientos' => 'https://images.unsplash.com/photo-1527799820374-dcf8d9d4a388?auto=format&fit=crop&w=700&q=80',
                ];
            @endphp

            @if(isset($servicios) && $servicios->isNotEmpty() && isset($categorias) && $categorias->isNotEmpty())
                <!-- Barra de Botones de Filtro por Categoría -->
                <div class="services-filter-bar" id="servicesFilterBar" role="tablist" aria-label="Filtrar servicios por categoría">
                    <button 
                        type="button" 
                        class="filter-btn active" 
                        data-filter="all" 
                        id="filterBtnAll"
                        role="tab"
                        aria-selected="true"
                    >
                        Todos ({{ $servicios->count() }})
                    </button>
                    @foreach($categorias as $cat)
                        @php
                            $catSlug = strtolower(trim($cat));
                            $catCount = $servicios->where('category', $cat)->count();
                            $catName = $categoryLabels[$catSlug] ?? ucfirst($cat);
                        @endphp
                        <button 
                            type="button" 
                            class="filter-btn" 
                            data-filter="{{ $catSlug }}" 
                            id="filterBtn{{ ucfirst($catSlug) }}"
                            role="tab"
                            aria-selected="false"
                        >
                            {{ $catName }} ({{ $catCount }})
                        </button>
                    @endforeach
                </div>
            @endif

            <!-- Cuadrícula de Tarjetas de Servicios -->
            <div class="services-grid" id="servicesGrid">
                @if(isset($servicios))
                    @foreach($servicios as $servicio)
                        @php
                            $catSlug = strtolower(trim($servicio->category ?? 'general'));
                            $catBadge = $categoryLabels[$catSlug] ?? ucfirst($servicio->category ?? 'General');
                            
                            if (!empty($servicio->image_path)) {
                                $imgSrc = \Illuminate\Support\Str::startsWith($servicio->image_path, ['http://', 'https://'])
                                    ? $servicio->image_path
                                    : (file_exists(public_path($servicio->image_path)) ? asset($servicio->image_path) : asset('storage/' . $servicio->image_path));
                                $isFallback = false;
                            } elseif (isset($servicePhotos[$servicio->id])) {
                                $imgSrc = $servicePhotos[$servicio->id];
                                $isFallback = false;
                            } elseif (isset($categoryFallbacks[$catSlug])) {
                                $imgSrc = $categoryFallbacks[$catSlug];
                                $isFallback = false;
                            } else {
                                $imgSrc = asset('images/logo.png');
                                $isFallback = true;
                            }
                        @endphp

                        <article 
                            class="service-card" 
                            data-category="{{ $catSlug }}" 
                            id="serviceCard{{ $servicio->id }}"
                        >
                            <div class="service-img-wrapper">
                                <img 
                                    src="{{ $imgSrc }}" 
                                    alt="{{ $servicio->name }}" 
                                    class="service-img {{ $isFallback ? 'is-fallback-logo' : '' }}"
                                    loading="lazy"
                                    onerror="this.onerror=null; this.src='{{ asset('images/logo.png') }}'; this.classList.add('is-fallback-logo');"
                                >
                                <span class="service-category-badge">{{ $catBadge }}</span>
                                <span class="service-duration-badge" title="Duración estimada: {{ $servicio->duration_minutes }} minutos">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12 6 12 12 16 14"></polyline>
                                    </svg>
                                    <span>{{ $servicio->duration_minutes }} min</span>
                                </span>
                            </div>

                            <div class="service-card-body">
                                <div>
                                    <h3 class="service-card-title">{{ $servicio->name }}</h3>
                                    <p class="service-card-desc">
                                        {{ $servicio->description ?? 'Servicio profesional ejecutado con técnicas de vanguardia y productos de primera calidad.' }}
                                    </p>
                                </div>

                                <div class="service-card-footer">
                                    <div class="service-price-block">
                                        <span class="price-label">Precio</span>
                                        <div>
                                            <span class="price-amount">${{ number_format($servicio->price, 2) }}</span>
                                            <span class="price-currency">MXN</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                @endif
            </div>

            <!-- Estado Vacío (Visible si no hay servicios o si el filtro arroja 0 resultados) -->
            <div class="services-empty-state {{ (!isset($servicios) || $servicios->isEmpty()) ? 'is-visible' : '' }}" id="servicesEmptyState">
                <div class="empty-state-icon">
                    <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>
                <h3 class="empty-state-title" id="emptyStateTitle">
                    {{ (!isset($servicios) || $servicios->isEmpty()) ? 'Próximamente Nuevos Servicios' : 'No se encontraron servicios' }}
                </h3>
                <p class="empty-state-text" id="emptyStateText">
                    {{ (!isset($servicios) || $servicios->isEmpty()) 
                        ? 'En este momento estamos actualizando nuestro catálogo para ofrecerte la mejor experiencia. Muy pronto podrás consultar y reservar nuestros servicios en línea.' 
                        : 'No encontramos servicios disponibles para la categoría seleccionada. Puedes consultar todas nuestras especialidades disponibles.' 
                    }}
                </p>
                <button type="button" class="btn-empty-reset" id="btnResetServicesFilter" style="{{ (!isset($servicios) || $servicios->isEmpty()) ? 'display: none;' : '' }}">
                    Ver Todos los Servicios
                </button>
            </div>
        </section>

        <!-- Sección 3: Nuestro Equipo de Trabajo -->
        <section id="equipo">
            <div class="section-header">
                <span class="section-tag">Talento y Pasión</span>
                <h2 class="section-title">Nuestro Equipo de Profesionales</h2>
                <p class="section-subtitle">
                    Conoce a los barberos y estilistas altamente capacitados que transformarán tu imagen con maestría y dedicación.
                </p>
            </div>

            @if(isset($trabajadores) && $trabajadores->isNotEmpty())
                <div class="team-grid" id="teamGrid">
                    @foreach($trabajadores as $trabajador)
                        @php
                            $nombreCompleto = $trabajador->nombre_completo ?: 'Profesional del Estudio';
                            $expAnios = (int)($trabajador->experiencia ?? 0);
                            $textoExperiencia = $expAnios > 0 
                                ? ($expAnios == 1 ? '1 año de experiencia' : $expAnios . ' años de experiencia')
                                : 'Especialista certificado';

                            // Determinación de la fotografía del trabajador
                            if (!empty($trabajador->fotografia)) {
                                $fotoUrl = \Illuminate\Support\Str::startsWith($trabajador->fotografia, ['http://', 'https://'])
                                    ? $trabajador->fotografia
                                    : (file_exists(public_path($trabajador->fotografia)) ? asset($trabajador->fotografia) : asset('storage/' . $trabajador->fotografia));
                                $isAvatar = false;
                            } else {
                                $fotoUrl = asset('images/avatar-default.svg');
                                $isAvatar = true;
                            }

                            // Especialidades destacadas a partir de sus servicios activos asociados
                            $serviciosNombres = $trabajador->serviciosActivos ? $trabajador->serviciosActivos->pluck('name')->take(3) : collect();
                        @endphp

                        <article class="team-card" id="teamCard{{ $trabajador->id }}">
                            <div class="team-img-wrapper">
                                <img 
                                    src="{{ $fotoUrl }}" 
                                    alt="Fotografía de {{ $nombreCompleto }}" 
                                    class="team-img {{ $isAvatar ? 'is-avatar-svg' : '' }}"
                                    loading="lazy"
                                    onerror="this.onerror=null; this.src='{{ asset('images/avatar-default.svg') }}'; this.classList.add('is-avatar-svg');"
                                >
                                <span class="team-exp-badge" title="Experiencia comprobada en el rubro">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12 6 12 12 16 14"></polyline>
                                    </svg>
                                    <span>{{ $textoExperiencia }}</span>
                                </span>
                            </div>

                            <div class="team-card-body">
                                <h3 class="team-card-name">{{ $nombreCompleto }}</h3>
                                <span class="team-card-role">Barbero &amp; Estilista Profesional</span>

                                @if($serviciosNombres->isNotEmpty())
                                    <div class="team-tags-container" aria-label="Especialidades">
                                        @foreach($serviciosNombres as $servicioNombre)
                                            <span class="team-tag-pill">{{ $servicioNombre }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="team-empty-state" id="teamEmptyState">
                    <div class="empty-state-icon">
                        <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <h3 class="empty-state-title">Equipo en Actualización</h3>
                    <p class="empty-state-text">
                        Nuestro equipo de profesionales se encuentra en proceso de actualización. Muy pronto podrás consultar la experiencia y especialidad de cada uno de nuestros barberos y estilistas.
                    </p>
                </div>
            @endif
        </section>

        <!-- Sección 4: Horarios de Atención -->
        <section id="horarios">
            <div class="section-header">
                <span class="section-tag">Disponibilidad Semanal</span>
                <h2 class="section-title">Horarios de Atención</h2>
                <p class="section-subtitle">
                    Consulta nuestros días y horas de atención presencial para visitarnos o agendar tu próxima cita con tranquilidad.
                </p>
            </div>

            <div class="schedule-section-content">
                <!-- Indicador Dinámico en Tiempo Real (Abierto / Cerrado) -->
                @if(isset($estadoSucursal))
                    <div 
                        class="schedule-status-banner {{ $estadoSucursal['abierto'] ? 'status-open' : 'status-closed' }}" 
                        id="scheduleLiveStatus"
                        role="status"
                        aria-live="polite"
                    >
                        <span class="status-indicator-dot {{ $estadoSucursal['abierto'] ? 'dot-pulse-green' : 'dot-pulse-wine' }}" aria-hidden="true"></span>
                        <span class="status-banner-text" id="scheduleStatusText">{{ $estadoSucursal['texto'] }}</span>
                    </div>
                @endif

                <!-- Tabla Visual de Horarios de los 7 Días de la Semana -->
                <div class="schedule-card-wrapper" id="scheduleWeekTable">
                    <div class="schedule-table-header">
                        <span class="col-day">Día de la Semana</span>
                        <span class="col-hours">Horario de Servicio</span>
                        <span class="col-status" style="text-align: right;">Estado</span>
                    </div>

                    <ul class="schedule-list">
                        @if(isset($horariosSemana))
                            @foreach($horariosSemana as $diaKey => $horario)
                                @php
                                    $esHoy = isset($estadoSucursal) && ($diaKey === ($estadoSucursal['dia_hoy'] ?? ''));
                                @endphp
                                <li 
                                    class="schedule-row {{ $esHoy ? 'is-today' : '' }} {{ $horario['abierto'] ? 'is-open-day' : 'is-closed-day' }}" 
                                    id="scheduleRow_{{ $diaKey }}"
                                >
                                    <div class="schedule-day-info">
                                        <span class="day-name">{{ $horario['nombre'] }}</span>
                                        @if($esHoy)
                                            <span class="today-pill" title="Día actual de la consulta">Hoy</span>
                                        @endif
                                    </div>

                                    <div class="schedule-hours-info">
                                        @if($horario['abierto'])
                                            <span class="hours-range">{{ $horario['apertura_12h'] }} a {{ $horario['cierre_12h'] }}</span>
                                        @else
                                            <span class="hours-closed-text">Cerrado todo el día</span>
                                        @endif
                                    </div>

                                    <div class="schedule-status-badge-wrapper">
                                        @if($horario['abierto'])
                                            <span class="schedule-badge badge-open">Abierto</span>
                                        @else
                                            <span class="schedule-badge badge-closed">Cerrado</span>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
        </section>

        <!-- Sección 5: Ubicación Física y Contacto -->
        <section id="ubicacion">
            <div class="section-header">
                <span class="section-tag">Encuéntranos Fácilmente</span>
                <h2 class="section-title">Ubicación y Contacto Directo</h2>
                <p class="section-subtitle">
                    Estamos convenientemente ubicados para brindarte la mejor atención. Conoce nuestra dirección y medios directos de comunicación.
                </p>
            </div>

            <div class="location-container">
                <!-- Tarjeta de Dirección Oficial y Canales de Comunicación -->
                <div class="location-info-card" id="locationInfoCard">
                    <!-- Bloque de Dirección Física Completa -->
                    <div class="location-address-block">
                        <div class="location-block-header">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                            <span>Dirección del Establecimiento</span>
                        </div>

                        <p class="location-address-text" id="fullAddressText">
                            Av. Insurgentes Sur 1285, Col. Del Valle Sur, Benito Juárez, Ciudad de México, C.P. 03100
                        </p>

                        <div class="location-actions-row">
                            <button 
                                type="button" 
                                class="btn-copy-address" 
                                id="btnCopyAddress"
                                title="Copiar dirección al portapapeles"
                            >
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                </svg>
                                <span>Copiar Dirección</span>
                            </button>
                        </div>
                    </div>

                    <!-- Bloque de Dudas, Aclaraciones y Navegación -->
                    <div class="location-contact-block">
                        <div class="contact-block-title">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                            </svg>
                            <span>Dudas y Aclaraciones</span>
                        </div>
                        <p class="contact-block-desc">
                            ¿Tienes preguntas sobre un servicio, paquete o cita? Comunícate directamente con nuestro equipo de recepción.
                        </p>
                        <div class="contact-buttons-wrapper">
                            <!-- Botón de Llamada Telefónica Directa (Móvil y Escritorio) -->
                            <a 
                                href="tel:+525556789012" 
                                class="btn-phone-call" 
                                id="btnCallStudio" 
                                title="Llamar a Beauty & Barber Studio para dudas o aclaraciones"
                            >
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                </svg>
                                <span>Llamar: +52 55 5678 9012</span>
                            </a>

                            <!-- Botón Abrir en Google Maps / Cómo llegar -->
                            <a 
                                href="https://www.google.com/maps/search/?api=1&query=Av.+Insurgentes+Sur+1285,+Del+Valle+Sur,+Benito+Juarez,+Ciudad+de+Mexico" 
                                target="_blank" 
                                rel="noopener noreferrer" 
                                class="btn-open-maps" 
                                id="btnOpenGoogleMaps"
                                title="Abrir ubicación en Google Maps en una pestaña nueva"
                            >
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <polygon points="3 11 22 2 13 21 11 13 3 11"></polygon>
                                </svg>
                                <span>Abrir en Google Maps</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Cuadrícula Visual: Fachada del Estudio al Lado del Mapa Interactivo -->
                <div class="location-visual-grid">
                    <!-- 1. Imagen Real de la Fachada de Beauty & Barber Studio -->
                    <div class="facade-card" id="facadeCard">
                        <div class="facade-img-wrapper">
                            <img 
                                src="{{ asset('images/fachada.jpg') }}" 
                                alt="Fachada exterior oficial de Beauty &amp; Barber Studio" 
                                class="facade-img"
                                loading="lazy"
                            >
                            <div class="facade-badge-overlay">
                                <div>
                                    <h3 class="facade-badge-title">Beauty &amp; Barber Studio</h3>
                                    <p class="facade-badge-desc">Fachada e instalaciones sobre Av. Insurgentes Sur 1285</p>
                                </div>
                                <span class="highlight-dot" aria-hidden="true"></span>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Mapa Interactivo Funcional -->
                    <div class="map-card" id="interactiveMapCard">
                        <iframe 
                            class="map-iframe"
                            title="Mapa interactivo de ubicación de Beauty & Barber Studio"
                            src="https://maps.google.com/maps?q=Av.+Insurgentes+Sur+1285,+Del+Valle+Sur,+Benito+Juarez,+Ciudad+de+Mexico&t=&z=16&ie=UTF8&iwloc=&output=embed"
                            loading="lazy"
                            allowfullscreen=""
                            aria-label="Mapa interactivo con la ubicación de Beauty & Barber Studio"
                        ></iframe>
                    </div>
                </div>
            </div>
        </section>

        <!-- Notificación Flotante Toast para Confirmación de Dirección Copiada -->
        <div class="toast-notification" id="copyToast" role="alert" aria-live="assertive">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
            <span id="toastMessage">Dirección copiada</span>
        </div>
    </main>

    <!-- ==========================================================================
         PIE DE PÁGINA INSTITUCIONAL (FOOTER, REDES SOCIALES Y LEGAL - SUBTAREA 7)
         ========================================================================== -->
    <footer class="site-footer" id="pie-pagina" role="contentinfo">
        <div class="footer-container">
            <!-- Cuadrícula Superior del Pie de Página -->
            <div class="footer-top-grid">
                <!-- Columna 1: Identidad de Marca y Dirección -->
                <div class="footer-col footer-col-brand">
                    <a href="#inicio" class="footer-brand" aria-label="Beauty &amp; Barber Studio - Inicio">
                        <img 
                            src="{{ asset('images/logo.png') }}" 
                            alt="Logo Beauty &amp; Barber Studio" 
                            class="footer-brand-logo"
                            onerror="this.style.display='none'"
                        >
                        <div class="footer-brand-text">
                            <span class="footer-brand-title">Beauty <span class="gold-accent">&amp; Barber</span></span>
                            <span class="footer-brand-subtitle">Studio Profesional</span>
                        </div>
                    </a>
                    <p class="footer-col-desc">
                        Excelencia y maestría en cada corte, afeitado y tratamiento estético. Brindamos una experiencia premium diseñada para realzar tu estilo y confianza personal.
                    </p>
                    <div class="footer-address-mini">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        <span>Av. Insurgentes Sur 1285, Col. Del Valle Sur, CDMX</span>
                    </div>
                </div>

                <!-- Columna 2: Enlaces Rápidos de Navegación -->
                <div class="footer-col footer-col-links">
                    <h3 class="footer-col-heading">Navegación</h3>
                    <ul class="footer-links-list">
                        <li><a href="#inicio" class="footer-nav-link">Inicio</a></li>
                        <li><a href="#servicios" class="footer-nav-link">Servicios</a></li>
                        <li><a href="#equipo" class="footer-nav-link">Nuestro Equipo</a></li>
                        <li><a href="#horarios" class="footer-nav-link">Horarios de Atención</a></li>
                        <li><a href="#ubicacion" class="footer-nav-link">Ubicación y Contacto</a></li>
                    </ul>
                </div>

                <!-- Columna 3: Redes Sociales Oficiales -->
                <div class="footer-col footer-col-social">
                    <h3 class="footer-col-heading">Redes Sociales Oficiales</h3>
                    <p class="footer-social-intro">
                        Síguenos en nuestros perfiles oficiales para conocer promociones exclusivas, nuevos estilos y el trabajo diario de nuestros especialistas.
                    </p>
                    
                    <div class="footer-social-buttons" id="footerSocialNetworks">
                        <!-- Facebook Oficial -->
                        <a 
                            id="socialFacebookBtn"
                            href="https://www.facebook.com/beautyandbarberstudio" 
                            target="_blank" 
                            rel="noopener noreferrer" 
                            class="social-btn social-facebook" 
                            aria-label="Página oficial de Facebook de Beauty &amp; Barber Studio (abre en nueva pestaña)"
                            title="Facebook - Beauty &amp; Barber Studio"
                        >
                            <svg class="social-icon" width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                            <span class="social-name">Facebook</span>
                        </a>

                        <!-- Instagram Oficial -->
                        <a 
                            id="socialInstagramBtn"
                            href="https://www.instagram.com/beautyandbarberstudio" 
                            target="_blank" 
                            rel="noopener noreferrer" 
                            class="social-btn social-instagram" 
                            aria-label="Perfil oficial de Instagram de Beauty &amp; Barber Studio (abre en nueva pestaña)"
                            title="Instagram - Beauty &amp; Barber Studio"
                        >
                            <svg class="social-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                                <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                            </svg>
                            <span class="social-name">Instagram</span>
                        </a>

                        <!-- TikTok Oficial -->
                        <a 
                            id="socialTiktokBtn"
                            href="https://www.tiktok.com/@beautyandbarberstudio" 
                            target="_blank" 
                            rel="noopener noreferrer" 
                            class="social-btn social-tiktok" 
                            aria-label="Cuenta oficial de TikTok de Beauty &amp; Barber Studio (abre en nueva pestaña)"
                            title="TikTok - Beauty &amp; Barber Studio"
                        >
                            <svg class="social-icon" width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64c.298-.002.595.042.88.13V9.4a6.33 6.33 0 0 0-1-.08A6.34 6.34 0 0 0 3 15.66a6.34 6.34 0 0 0 10.81 4.48 6.27 6.27 0 0 0 1.86-4.48V8.71a8.16 8.16 0 0 0 4.92 1.63V6.89a4.85 4.85 0 0 1-1-.2z"/>
                            </svg>
                            <span class="social-name">TikTok</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Línea Divisoria Decorativa -->
            <div class="footer-divider" aria-hidden="true"></div>

            <!-- Franja Inferior: Derechos Reservados y Enlaces Legales -->
            <div class="footer-bottom">
                <div class="footer-copyright" id="footerCopyright">
                    &copy; {{ date('Y') }} Beauty &amp; Barber Studio. Todos los derechos reservados.
                </div>

                <div class="footer-legal-links" id="footerLegalLinks">
                    <button type="button" class="legal-action-btn" id="btnAvisoPrivacidad">
                        Aviso de Privacidad
                    </button>
                    <span class="legal-dot" aria-hidden="true">&bull;</span>
                    <button type="button" class="legal-action-btn" id="btnTerminosCondiciones">
                        Términos y Condiciones
                    </button>
                </div>
            </div>
        </div>
    </footer>

    <!-- Ventana Modal Informativa Legal (Aviso de Privacidad y Términos y Condiciones) -->
    <div class="legal-modal-backdrop" id="legalModalBackdrop" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="legalModalTitle">
        <div class="legal-modal-card" id="legalModalCard">
            <div class="legal-modal-header">
                <h3 class="legal-modal-title" id="legalModalTitle">Información Legal</h3>
                <button type="button" class="legal-modal-close" id="legalModalCloseBtn" aria-label="Cerrar ventana emergente">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <div class="legal-modal-body" id="legalModalBody">
                <!-- Se inyecta dinámicamente según la opción elegida -->
            </div>
            <div class="legal-modal-footer">
                <button type="button" class="btn-legal-dismiss" id="btnLegalDismiss">Entendido</button>
            </div>
        </div>
    </div>

    <!-- ==========================================================================
         SCRIPTS: COMPORTAMIENTO INTERACTIVO Y NAVEGACIÓN SUAVE
         ========================================================================== -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const hamburgerBtn = document.getElementById('hamburgerBtn');
            const closeDrawerBtn = document.getElementById('closeDrawerBtn');
            const mobileDrawer = document.getElementById('mobileDrawer');
            const mobileDrawerBackdrop = document.getElementById('mobileDrawerBackdrop');
            const drawerLinks = document.querySelectorAll('.drawer-nav-link');
            const desktopLinks = document.querySelectorAll('.nav-item-link, .navbar-brand');

            // Abrir cajón móvil
            function openDrawer() {
                mobileDrawer.classList.add('is-active');
                mobileDrawerBackdrop.classList.add('is-active');
                hamburgerBtn.setAttribute('aria-expanded', 'true');
                document.body.classList.add('menu-open');
            }

            // Cerrar cajón móvil
            function closeDrawer() {
                mobileDrawer.classList.remove('is-active');
                mobileDrawerBackdrop.classList.remove('is-active');
                hamburgerBtn.setAttribute('aria-expanded', 'false');
                document.body.classList.remove('menu-open');
            }

            // Eventos del botón hamburguesa y botón de cierre
            if (hamburgerBtn) {
                hamburgerBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    if (mobileDrawer.classList.contains('is-active')) {
                        closeDrawer();
                    } else {
                        openDrawer();
                    }
                });
            }

            if (closeDrawerBtn) {
                closeDrawerBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    closeDrawer();
                });
            }

            // Cerrar al hacer clic en el telón de fondo
            if (mobileDrawerBackdrop) {
                mobileDrawerBackdrop.addEventListener('click', function () {
                    closeDrawer();
                });
            }

            // Cerrar con la tecla Escape
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && mobileDrawer.classList.contains('is-active')) {
                    closeDrawer();
                }
            });

            // Desplazamiento suave para enlaces móviles y cierre automático del menú
            drawerLinks.forEach(function (link) {
                link.addEventListener('click', function (e) {
                    const targetId = this.getAttribute('href');
                    if (targetId && targetId.startsWith('#')) {
                        e.preventDefault();
                        closeDrawer();

                        const targetElement = document.querySelector(targetId);
                        if (targetElement) {
                            setTimeout(function () {
                                targetElement.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'start'
                                });
                            }, 180);
                        }
                    }
                });
            });

            // Desplazamiento suave para enlaces de escritorio
            desktopLinks.forEach(function (link) {
                link.addEventListener('click', function (e) {
                    const targetId = this.getAttribute('href');
                    if (targetId && targetId.startsWith('#')) {
                        e.preventDefault();
                        const targetElement = document.querySelector(targetId);
                        if (targetElement) {
                            targetElement.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        }
                    }
                });
            });

            // ==============================================================
            // DETECCIÓN DINÁMICA DE LA SECCIÓN ACTIVA (SCROLL SPY / ENFOQUE)
            // ==============================================================
            const sections = document.querySelectorAll('main section[id]');
            const allNavLinks = document.querySelectorAll('.nav-item-link, .drawer-nav-link');

            function updateActiveNavFocus() {
                const scrollY = window.scrollY;
                const offsetThreshold = 140; // Compensación de altura de la barra superior fija
                let currentSectionId = 'inicio';

                sections.forEach(function (section) {
                    const sectionTop = section.offsetTop - offsetThreshold;
                    const sectionHeight = section.offsetHeight;
                    if (scrollY >= sectionTop && scrollY < sectionTop + sectionHeight) {
                        currentSectionId = section.getAttribute('id');
                    }
                });

                // Si el usuario llega al final de la página, enfocar la última sección
                if ((window.innerHeight + window.scrollY) >= (document.body.offsetHeight - 60)) {
                    const lastSection = sections[sections.length - 1];
                    if (lastSection) {
                        currentSectionId = lastSection.getAttribute('id');
                    }
                }

                allNavLinks.forEach(function (link) {
                    const targetHref = link.getAttribute('href');
                    if (targetHref === '#' + currentSectionId) {
                        link.classList.add('active');
                    } else {
                        link.classList.remove('active');
                    }
                });
            }

            // ==============================================================
            // FILTRO INTERACTIVO DE SERVICIOS POR CATEGORÍA (SUBTAREA 3)
            // ==============================================================
            const filterButtons = document.querySelectorAll('.filter-btn');
            const serviceCards = document.querySelectorAll('.service-card');
            const emptyState = document.getElementById('servicesEmptyState');
            const emptyStateTitle = document.getElementById('emptyStateTitle');
            const emptyStateText = document.getElementById('emptyStateText');
            const btnResetFilter = document.getElementById('btnResetServicesFilter');

            function applyCategoryFilter(category) {
                const targetCat = (category || 'all').toLowerCase().trim();
                let visibleCount = 0;

                serviceCards.forEach(function (card) {
                    const cardCat = (card.getAttribute('data-category') || '').toLowerCase().trim();
                    if (targetCat === 'all' || cardCat === targetCat) {
                        card.classList.remove('is-hidden');
                        visibleCount++;
                    } else {
                        card.classList.add('is-hidden');
                    }
                });

                if (emptyState) {
                    if (visibleCount === 0) {
                        emptyState.classList.add('is-visible');
                        if (emptyStateTitle) {
                            emptyStateTitle.textContent = 'No se encontraron servicios';
                        }
                        if (emptyStateText) {
                            emptyStateText.textContent = 'No encontramos servicios disponibles en esta categoría por el momento. Muy pronto agregaremos nuevas especialidades.';
                        }
                        if (btnResetFilter) {
                            btnResetFilter.style.display = 'inline-block';
                        }
                    } else {
                        emptyState.classList.remove('is-visible');
                    }
                }
            }

            filterButtons.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    filterButtons.forEach(function (b) {
                        b.classList.remove('active');
                        b.setAttribute('aria-selected', 'false');
                    });

                    this.classList.add('active');
                    this.setAttribute('aria-selected', 'true');

                    const selectedCategory = this.getAttribute('data-filter');
                    applyCategoryFilter(selectedCategory);
                });
            });

            if (btnResetFilter) {
                btnResetFilter.addEventListener('click', function () {
                    const allBtn = document.getElementById('filterBtnAll') || document.querySelector('.filter-btn[data-filter="all"]');
                    if (allBtn) {
                        allBtn.click();
                    } else {
                        applyCategoryFilter('all');
                    }
                });
            }

            // ==============================================================
            // COPIAR DIRECCIÓN FÍSICA AL PORTAPAPELES (SUBTAREA 6)
            // ==============================================================
            const btnCopyAddress = document.getElementById('btnCopyAddress');
            const copyToast = document.getElementById('copyToast');
            const addressTextElement = document.getElementById('fullAddressText');
            let toastTimer = null;

            if (btnCopyAddress && copyToast) {
                btnCopyAddress.addEventListener('click', function () {
                    const textToCopy = addressTextElement 
                        ? addressTextElement.innerText.trim() 
                        : 'Av. Insurgentes Sur 1285, Col. Del Valle Sur, Benito Juárez, Ciudad de México, C.P. 03100';

                    function triggerToast() {
                        copyToast.classList.add('is-visible');
                        if (toastTimer) {
                            clearTimeout(toastTimer);
                        }
                        toastTimer = setTimeout(function () {
                            copyToast.classList.remove('is-visible');
                        }, 3000);
                    }

                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(textToCopy)
                            .then(triggerToast)
                            .catch(function () {
                                fallbackClipboard(textToCopy);
                            });
                    } else {
                        fallbackClipboard(textToCopy);
                    }

                    function fallbackClipboard(text) {
                        const tempTextarea = document.createElement('textarea');
                        tempTextarea.value = text;
                        tempTextarea.style.position = 'fixed';
                        tempTextarea.style.left = '-9999px';
                        tempTextarea.style.top = '0';
                        document.body.appendChild(tempTextarea);
                        tempTextarea.focus();
                        tempTextarea.select();
                        try {
                            document.execCommand('copy');
                            triggerToast();
                        } catch (err) {}
                        document.body.removeChild(tempTextarea);
                    }
                });
            }

            // ==============================================================
            // VENTANA MODAL PARA AVISO DE PRIVACIDAD Y TÉRMINOS Y CONDICIONES (SUBTAREA 7)
            // ==============================================================
            const btnAvisoPrivacidad = document.getElementById('btnAvisoPrivacidad');
            const btnTerminosCondiciones = document.getElementById('btnTerminosCondiciones');
            const legalModalBackdrop = document.getElementById('legalModalBackdrop');
            const legalModalTitle = document.getElementById('legalModalTitle');
            const legalModalBody = document.getElementById('legalModalBody');
            const legalModalCloseBtn = document.getElementById('legalModalCloseBtn');
            const btnLegalDismiss = document.getElementById('btnLegalDismiss');

            const contenidoAvisoPrivacidad = `
                <h4>1. Responsable del Tratamiento</h4>
                <p>Beauty &amp; Barber Studio, con domicilio en Av. Insurgentes Sur 1285, Col. Del Valle Sur, Alcaldía Benito Juárez, C.P. 03100, Ciudad de México, es el responsable del uso y protección de sus datos personales.</p>
                <h4>2. Finalidad de los Datos Personales</h4>
                <p>Los datos que recopilamos (nombre completo, teléfono celular, correo electrónico y opcionalmente fecha de nacimiento) son utilizados exclusivamente para la gestión de sus citas, confirmación de reservaciones, aclaraciones sobre nuestros servicios y para brindarle una atención personalizada de alta calidad.</p>
                <h4>3. Confidencialidad y Seguridad</h4>
                <p>Sus datos personales son resguardados con estrictas medidas de seguridad físicas, electrónicas y administrativas para evitar el daño, pérdida, alteración o tratamiento no autorizado. En ningún caso venderemos ni transferiremos su información a terceros sin su consentimiento previo.</p>
                <h4>4. Ejercicio de Derechos ARCO</h4>
                <p>Usted tiene derecho a conocer qué datos personales conservamos, para qué los utilizamos y las condiciones de su tratamiento (Acceso). Asimismo, puede solicitar la corrección (Rectificación), eliminación de registros (Cancelación) u oponerse a su tratamiento (Oposición) comunicándose a nuestras líneas oficiales o acudiendo directamente a nuestro estudio.</p>
            `;

            const contenidoTerminosCondiciones = `
                <h4>1. Uso del Servicio y Reservaciones</h4>
                <p>Al programar citas o registrarse en Beauty &amp; Barber Studio, el cliente se compromete a hacer un uso lícito y respetuoso de las instalaciones, personal y plataforma del establecimiento.</p>
                <h4>2. Política de Cancelación y Puntualidad</h4>
                <p>Agradecemos presentarse con 5 a 10 minutos de anticipación a su cita programada. En caso de requerir cancelación o cambio de horario, solicitamos avisar con al menos 24 horas de antelación para permitir la asignación del espacio a otro cliente.</p>
                <h4>3. Estándares de Higiene y Calidad</h4>
                <p>Nos comprometemos a brindar servicios ejecutados bajo protocolos rigurosos de desinfección y esterilización de herramientas de corte y cuidado facial, garantizando la salud y satisfacción de cada cliente.</p>
                <h4>4. Cuidado de Objetos Personales</h4>
                <p>Recomendamos a todos nuestros visitantes mantener sus pertenencias personales y de valor consigo en todo momento durante su estancia en el estudio.</p>
            `;

            function openLegalModal(title, htmlContent) {
                if (!legalModalBackdrop || !legalModalTitle || !legalModalBody) return;
                legalModalTitle.textContent = title;
                legalModalBody.innerHTML = htmlContent;
                legalModalBackdrop.classList.add('is-open');
                legalModalBackdrop.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
            }

            function closeLegalModal() {
                if (!legalModalBackdrop) return;
                legalModalBackdrop.classList.remove('is-open');
                legalModalBackdrop.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
            }

            if (btnAvisoPrivacidad) {
                btnAvisoPrivacidad.addEventListener('click', function (e) {
                    e.preventDefault();
                    openLegalModal('Aviso de Privacidad', contenidoAvisoPrivacidad);
                });
            }

            if (btnTerminosCondiciones) {
                btnTerminosCondiciones.addEventListener('click', function (e) {
                    e.preventDefault();
                    openLegalModal('Términos y Condiciones', contenidoTerminosCondiciones);
                });
            }

            if (legalModalCloseBtn) {
                legalModalCloseBtn.addEventListener('click', closeLegalModal);
            }

            if (btnLegalDismiss) {
                btnLegalDismiss.addEventListener('click', closeLegalModal);
            }

            if (legalModalBackdrop) {
                legalModalBackdrop.addEventListener('click', function (e) {
                    if (e.target === legalModalBackdrop) {
                        closeLegalModal();
                    }
                });
            }

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && legalModalBackdrop && legalModalBackdrop.classList.contains('is-open')) {
                    closeLegalModal();
                }
            });

            window.addEventListener('scroll', updateActiveNavFocus, { passive: true });
            updateActiveNavFocus();
        });
    </script>
</body>
</html>
