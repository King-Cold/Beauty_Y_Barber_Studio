<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Beauty & Barber Studio - Mi Perfil y Seguridad (Administrador)</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* ===================================================
           SISTEMA DE DISEÑO ADMIN (DARK MODE & VIBRANT ACCENTS)
           =================================================== */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --bg-canvas: #050a15;
            --bg-header: #0a1124;
            --bg-sidebar: #0a1124;
            --bg-card: #0d172e;
            --bg-card-hover: #111e3b;
            --bg-input: #070d1e;
            
            --border-subtle: rgba(255, 255, 255, 0.08);
            --border-strong: rgba(255, 255, 255, 0.16);

            --barber-red: #7f1d1d;
            --barber-red-light: rgba(127, 29, 29, 0.18);
            --barber-blue: #1e3a8a;
            --barber-blue-light: #60a5fa; 
            --barber-blue-pale: rgba(30, 58, 138, 0.18);
            --barber-gold: #f59e0b;
            --barber-green: #10b981;

            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --text-white: #ffffff;

            --header-height: 72px;
            --sidebar-width: 260px;
            --sidebar-collapsed-width: 80px;

            --ease-fluid: cubic-bezier(0.25, 1, 0.5, 1);
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--bg-canvas);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            overflow-x: clip;
        }

        /* Enlace de accesibilidad para teclado */
        .skip-link {
            position: absolute;
            top: -50px;
            left: 20px;
            background: var(--barber-blue);
            color: var(--text-white);
            padding: 8px 16px;
            z-index: 100;
            text-decoration: none;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 600;
            transition: top 0.3s var(--ease-fluid);
        }
        .skip-link:focus {
            top: 20px;
        }

        /* HEADER DEL ADMINISTRADOR */
        .admin-header {
            background: var(--bg-header);
            border-bottom: 1px solid var(--border-subtle);
            position: sticky;
            top: 0;
            z-index: 50;
            height: var(--header-height);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.35);
        }

        .header-top-accent-line {
            height: 3px;
            background: linear-gradient(90deg, var(--barber-red) 0%, #ffffff 50%, var(--barber-blue) 100%);
            width: 100%;
        }

        .header-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: calc(var(--header-height) - 3px);
            padding: 0 24px;
            max-width: 1920px;
            margin: 0 auto;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .mobile-toggle-btn {
            display: none;
            background: transparent;
            border: 1px solid var(--border-subtle);
            color: var(--text-main);
            padding: 8px;
            border-radius: 8px;
            cursor: pointer;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }
        .mobile-toggle-btn:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: var(--border-strong);
        }
        .mobile-toggle-btn svg {
            width: 20px;
            height: 20px;
        }

        .brand-section {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: inherit;
        }

        .brand-logo-img {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid var(--border-subtle);
        }

        .brand-info {
            display: flex;
            flex-direction: column;
        }

        .brand-name {
            font-family: 'Playfair Display', Georgia, serif;
            font-weight: 700;
            font-size: 17px;
            letter-spacing: -0.01em;
            color: var(--text-white);
        }

        .brand-role-badge {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--barber-blue-light);
            background: var(--barber-blue-pale);
            padding: 2px 8px;
            border-radius: 12px;
            display: inline-block;
            width: fit-content;
            margin-top: 2px;
            border: 1px solid rgba(37, 99, 235, 0.3);
        }

        /* Dropdown de usuario */
        .user-nav {
            display: flex;
            align-items: center;
        }

        .profile-dropdown-wrapper {
            position: relative;
        }

        .profile-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-subtle);
            padding: 6px 14px 6px 8px;
            border-radius: 9999px;
            cursor: pointer;
            color: var(--text-main);
            transition: all 0.2s ease;
        }
        .profile-btn:hover {
            background: rgba(255, 255, 255, 0.06);
            border-color: var(--border-strong);
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--barber-red) 0%, var(--barber-blue) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            color: var(--text-white);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .user-meta {
            display: flex;
            flex-direction: column;
            text-align: left;
        }

        .user-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-white);
        }

        .user-role {
            font-size: 11px;
            color: var(--text-muted);
        }

        .dropdown-arrow {
            width: 14px;
            height: 14px;
            color: var(--text-muted);
            transition: transform 0.2s ease;
        }
        .profile-btn[aria-expanded="true"] .dropdown-arrow {
            transform: rotate(180deg);
        }

        .profile-menu {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            width: 220px;
            background: var(--bg-card);
            border: 1px solid var(--border-strong);
            border-radius: 12px;
            padding: 6px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            display: none;
            flex-direction: column;
            z-index: 60;
            backdrop-filter: blur(12px);
        }
        .profile-menu.show {
            display: flex;
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            border-radius: 8px;
            color: var(--text-main);
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.15s ease;
        }
        .menu-item:hover, .menu-item.active {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-white);
        }
        .menu-item.active {
            color: var(--barber-blue-light);
            background: var(--barber-blue-pale);
        }

        .menu-item-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            color: var(--text-muted);
        }
        .menu-item:hover .menu-item-icon, .menu-item.active .menu-item-icon {
            color: var(--barber-blue-light);
        }

        .menu-divider {
            height: 1px;
            background: var(--border-subtle);
            margin: 4px 0;
        }

        .logout-item:hover {
            background: var(--barber-red-light) !important;
            color: var(--barber-red) !important;
        }
        .logout-item:hover .menu-item-icon {
            color: var(--barber-red) !important;
        }

        /* LAYOUT PRINCIPAL: SIDEBAR + CONTENT */
        .admin-layout {
            display: flex;
            flex: 1;
            min-height: calc(100vh - var(--header-height));
            position: relative;
        }

        /* SIDEBAR */
        .admin-sidebar {
            width: var(--sidebar-width);
            background: var(--bg-sidebar);
            border-right: 1px solid var(--border-subtle);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            transition: width 0.3s var(--ease-fluid);
            z-index: 40;
        }

        .admin-sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 20px;
            border-bottom: 1px solid var(--border-subtle);
        }

        .sidebar-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-muted);
            white-space: nowrap;
            transition: opacity 0.2s ease;
        }
        .admin-sidebar.collapsed .sidebar-title {
            opacity: 0;
            pointer-events: none;
            width: 0;
        }

        .sidebar-toggle-btn {
            background: transparent;
            border: 1px solid var(--border-subtle);
            color: var(--text-muted);
            padding: 6px;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }
        .sidebar-toggle-btn:hover {
            color: var(--text-white);
            background: rgba(255, 255, 255, 0.05);
            border-color: var(--border-strong);
        }
        .sidebar-toggle-btn svg {
            width: 16px;
            height: 16px;
        }

        .sidebar-inner {
            padding: 14px 12px;
            overflow-y: auto;
            flex: 1;
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 11px 14px;
            border-radius: 10px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s ease;
            position: relative;
            white-space: nowrap;
        }
        .nav-item:hover {
            background: rgba(255, 255, 255, 0.04);
            color: var(--text-white);
        }
        .nav-item.active {
            background: var(--barber-blue-pale);
            color: var(--barber-blue-light);
            font-weight: 600;
            border-left: 3px solid var(--barber-blue);
        }

        .nav-icon-box {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            flex-shrink: 0;
        }
        .nav-icon-box svg {
            width: 19px;
            height: 19px;
        }

        .nav-label {
            transition: opacity 0.2s ease;
        }
        .admin-sidebar.collapsed .nav-label {
            opacity: 0;
            pointer-events: none;
            width: 0;
        }

        .nav-badge {
            margin-left: auto;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.06);
            color: var(--text-muted);
        }
        .admin-sidebar.collapsed .nav-badge {
            display: none;
        }

        /* BACKDROP MÓVIL */
        .sidebar-backdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.7);
            z-index: 35;
            backdrop-filter: blur(4px);
        }
        .sidebar-backdrop.active {
            display: block;
        }

        /* ÁREA DE CONTENIDO PRINCIPAL */
        .admin-main {
            flex: 1;
            padding: 32px 40px;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }

        /* BREADCRUMB Y TITULO */
        .page-header {
            margin-bottom: 28px;
        }
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 8px;
        }
        .breadcrumb a {
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.15s ease;
        }
        .breadcrumb a:hover {
            color: var(--barber-blue-light);
        }
        .breadcrumb span {
            color: var(--border-strong);
        }
        .page-title {
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--text-white);
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .page-title-badge {
            font-size: 12px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 20px;
            background: var(--barber-red-light);
            color: var(--barber-red);
            border: 1px solid rgba(239, 68, 68, 0.3);
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        /* HERO CARD PROFILE BANNER */
        .profile-hero-card {
            background: linear-gradient(135deg, rgba(13, 23, 46, 0.9) 0%, rgba(10, 17, 36, 0.95) 100%);
            border: 1px solid var(--border-strong);
            border-radius: 18px;
            padding: 30px;
            margin-bottom: 30px;
            display: grid;
            grid-template-columns: auto 1fr auto;
            gap: 24px;
            align-items: center;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
        }
        .profile-hero-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, var(--barber-red) 0%, var(--barber-blue) 100%);
        }

        .hero-avatar-wrap {
            position: relative;
        }
        .hero-avatar {
            width: 86px;
            height: 86px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--barber-red) 0%, var(--barber-blue) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: 800;
            color: #ffffff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.45);
            border: 3px solid rgba(255, 255, 255, 0.15);
        }
        .hero-badge-dot {
            position: absolute;
            bottom: 2px;
            right: 2px;
            width: 20px;
            height: 20px;
            background: var(--barber-green);
            border: 3px solid var(--bg-card);
            border-radius: 50%;
            box-shadow: 0 2px 6px rgba(16, 185, 129, 0.25);
        }

        .hero-info h2 {
            font-size: 22px;
            font-weight: 700;
            color: var(--text-white);
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .hero-info p {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .hero-info p svg {
            width: 15px;
            height: 15px;
            color: var(--barber-blue-light);
        }

        .hero-role-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(239, 68, 68, 0.15);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.35);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.03em;
        }

        .hero-stats-grid {
            display: flex;
            gap: 16px;
        }
        .hero-stat-pill {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--border-subtle);
            border-radius: 12px;
            padding: 12px 18px;
            text-align: center;
            min-width: 105px;
            transition: all 0.2s ease;
        }
        .hero-stat-pill:hover {
            background: rgba(255, 255, 255, 0.07);
            border-color: var(--border-strong);
            transform: translateY(-2px);
        }
        .hero-stat-pill .stat-num {
            font-size: 20px;
            font-weight: 800;
            color: var(--text-white);
            display: block;
        }
        .hero-stat-pill .stat-lbl {
            font-size: 11px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
            margin-top: 2px;
            display: block;
        }

        /* GRID DE DOS COLUMNAS PARA FORMULARIOS */
        .profile-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 28px;
        }

        .form-card {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 16px;
            padding: 28px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
            display: flex;
            flex-direction: column;
            transition: border-color 0.2s ease;
        }
        .form-card:hover {
            border-color: var(--border-strong);
        }

        .card-header-custom {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 22px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border-subtle);
        }

        .card-icon-box {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .card-icon-blue {
            background: var(--barber-blue-pale);
            color: var(--barber-blue-light);
            border: 1px solid rgba(37, 99, 235, 0.3);
        }
        .card-icon-red {
            background: var(--barber-red-light);
            color: var(--barber-red);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        .card-icon-box svg {
            width: 22px;
            height: 22px;
        }

        .card-title-group h3 {
            font-size: 17px;
            font-weight: 700;
            color: var(--text-white);
        }
        .card-title-group p {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        /* FORM ELEMENTS */
        .form-group {
            margin-bottom: 18px;
        }
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #cbd5e1;
            margin-bottom: 7px;
        }
        .form-group label .required-star {
            color: var(--barber-red);
            margin-left: 2px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon-left {
            position: absolute;
            left: 14px;
            width: 18px;
            height: 18px;
            color: var(--text-muted);
            pointer-events: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .input-icon-left svg {
            width: 17px;
            height: 17px;
        }

        .form-control-custom {
            width: 100%;
            background: var(--bg-input);
            border: 1px solid var(--border-subtle);
            border-radius: 10px;
            padding: 11px 14px 11px 42px;
            font-size: 14px;
            color: var(--text-white);
            font-family: inherit;
            transition: all 0.2s ease;
        }
        .form-control-custom:focus {
            outline: none;
            border-color: var(--barber-blue);
            background: #09122a;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
        }
        .form-control-custom::placeholder {
            color: #475569;
        }

        .toggle-password-btn {
            position: absolute;
            right: 12px;
            background: transparent;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.15s ease;
        }
        .toggle-password-btn:hover {
            color: var(--text-white);
        }
        .toggle-password-btn svg {
            width: 18px;
            height: 18px;
        }

        .field-hint {
            font-size: 11.5px;
            color: var(--text-muted);
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* REQUERIMIENTOS DE PASSWORD */
        .password-rules-box {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--border-subtle);
            border-radius: 10px;
            padding: 14px;
            margin-bottom: 20px;
        }
        .password-rules-title {
            font-size: 12px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 8px;
        }
        .rule-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 5px;
            transition: color 0.2s ease;
        }
        .rule-item:last-child {
            margin-bottom: 0;
        }
        .rule-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #475569;
            transition: all 0.2s ease;
        }
        .rule-item.valid {
            color: #10b981;
        }
        .rule-item.valid .rule-dot {
            background: #10b981;
            box-shadow: 0 2px 6px rgba(16, 185, 129, 0.25);
        }

        /* NIVEL DE SEGURIDAD / ADVERTENCIA */
        .admin-security-alert {
            display: flex;
            gap: 12px;
            background: rgba(239, 68, 68, 0.08);
            border: 1px solid rgba(239, 68, 68, 0.25);
            border-radius: 10px;
            padding: 12px 14px;
            margin-bottom: 20px;
        }
        .admin-security-alert svg {
            width: 20px;
            height: 20px;
            color: var(--barber-red);
            flex-shrink: 0;
            margin-top: 1px;
        }
        .admin-security-alert p {
            font-size: 12px;
            color: #fca5a5;
            line-height: 1.4;
        }

        /* BOTONES DE ACCIÓN */
        .btn-submit {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 11px 22px;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid rgba(255, 255, 255, 0.18);
            transition: all 0.2s ease;
            margin-top: auto;
            width: 100%;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }
        .btn-blue {
            background: #1e40af;
            color: #ffffff;
            border: 1px solid rgba(147, 197, 253, 0.28);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.35);
        }
        .btn-blue:hover:not(:disabled) {
            background: #1d4ed8;
            color: #ffffff;
            border-color: rgba(147, 197, 253, 0.45);
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
        }
        .btn-red {
            background: #1e293b;
            color: #f8fafc;
        }
        .btn-red:hover:not(:disabled) {
            background: #334155;
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.4);
        }
        .btn-submit:disabled {
            opacity: 0.65;
            cursor: not-allowed;
            transform: none !important;
        }
        .btn-spinner {
            display: none;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #ffffff;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* RESPONSIVE DESIGN */
        @media (max-width: 1024px) {
            .profile-grid {
                grid-template-columns: 1fr;
            }
            .profile-hero-card {
                grid-template-columns: 1fr;
                text-align: center;
                justify-items: center;
            }
            .hero-stats-grid {
                justify-content: center;
                flex-wrap: wrap;
            }
            .hero-info h2 {
                justify-content: center;
            }
            .hero-info p {
                justify-content: center;
            }
            .admin-main {
                padding: 24px 20px;
            }
        }

        @media (max-width: 768px) {
            .mobile-toggle-btn {
                display: flex;
            }
            .admin-sidebar {
                position: fixed;
                top: var(--header-height);
                left: -260px;
                height: calc(100vh - var(--header-height));
                transition: left 0.3s var(--ease-fluid);
            }
            .admin-sidebar.open-mobile {
                left: 0;
            }
            .user-meta {
                display: none;
            }
        }
    </style>
</head>
<body>

    <!-- Enlace accesible de salto al contenido -->
    <a href="#main-content" class="skip-link">Saltar al contenido principal</a>

    <!-- HEADER DEL ADMINISTRADOR -->
    <header class="admin-header" role="banner">
        <div class="header-top-accent-line" aria-hidden="true"></div>

        <div class="header-container">
            <!-- Zona Izquierda: Logotipo y Nombre -->
            <div class="header-left">
                <button 
                    type="button" 
                    class="mobile-toggle-btn" 
                    id="mobileToggleBtn" 
                    aria-label="Abrir menú de navegación lateral" 
                    aria-expanded="false" 
                    aria-controls="adminSidebar"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>

                <a href="{{ route('admin.dashboard') }}" class="brand-section" aria-label="Beauty & Barber Studio - Panel de Inicio">
                    <img 
                        src="{{ asset('images/logo.png') }}" 
                        alt="Logotipo Beauty & Barber Studio" 
                        class="brand-logo-img"
                        onerror="this.style.display='none'"
                    >
                    <div class="brand-info">
                        <span class="brand-name">Beauty & Barber Studio</span>
                        <span class="brand-role-badge">Panel Administrativo</span>
                    </div>
                </a>
            </div>

            <!-- Zona Derecha: Perfil de Administrador -->
            <nav class="user-nav" aria-label="Navegación de usuario y cuenta">
                <div class="profile-dropdown-wrapper" id="profileDropdownWrapper">
                    <button 
                        type="button" 
                        class="profile-btn" 
                        id="profileBtn" 
                        aria-expanded="false" 
                        aria-haspopup="true" 
                        aria-controls="profileMenu" 
                        aria-label="Menú de perfil del administrador"
                    >
                        <div class="user-avatar" id="headerAvatar" aria-hidden="true">{{ strtoupper(substr($user->name ?? 'AD', 0, 2)) }}</div>
                        <div class="user-meta">
                            <span class="user-name" id="headerUserName">{{ $user->name ?? 'Administrador' }}</span>
                            <span class="user-role" id="headerUserEmail">{{ $user->email ?? 'admin@barberstudio.com' }}</span>
                        </div>
                        <svg class="dropdown-arrow" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <!-- Opciones de perfil accesibles -->
                    <div class="profile-menu" id="profileMenu" role="menu" aria-labelledby="profileBtn">
                        <a href="{{ route('admin.perfil') }}" class="menu-item profile-item active" role="menuitem">
                            <div class="menu-item-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </div>
                            <span>Mi Perfil</span>
                        </a>
                        <a href="{{ route('admin.configuracion') }}" class="menu-item settings-item" role="menuitem">
                            <div class="menu-item-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <circle cx="12" cy="12" r="3"></circle>
                                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                                </svg>
                            </div>
                            <span>Configuración</span>
                        </a>
                        <div class="menu-divider" role="separator"></div>
                        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="menu-item logout-item" role="menuitem">
                            <div class="menu-item-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                    <polyline points="16 17 21 12 16 7"></polyline>
                                    <line x1="21" y1="12" x2="9" y2="12"></line>
                                </svg>
                            </div>
                            <span>Cerrar sesión</span>
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <!-- CONTENEDOR ESTRUCTURAL: SIDEBAR + MAIN -->
    <div class="admin-layout" id="adminLayout">

        <!-- MENÚ LATERAL (SIDEBAR) -->
        <aside class="admin-sidebar" id="adminSidebar" aria-label="Menú de navegación lateral">
            <div class="sidebar-header">
                <span class="sidebar-title">Módulos del Sistema</span>
                <button 
                    type="button" 
                    class="sidebar-toggle-btn" 
                    id="sidebarToggleBtn" 
                    aria-label="Colapsar o expandir menú lateral" 
                    aria-expanded="true" 
                    aria-controls="adminSidebar"
                    title="Colapsar o expandir menú"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>
            </div>

            <div class="sidebar-inner">
                <nav class="sidebar-nav" aria-label="Secciones del sistema">
                    <!-- Dashboard -->
                    <a href="{{ route('admin.dashboard') }}" class="nav-item" title="Dashboard">
                        <div class="nav-icon-box">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect x="3" y="3" width="7" height="7" rx="1.5"></rect>
                                <rect x="14" y="3" width="7" height="7" rx="1.5"></rect>
                                <rect x="14" y="14" width="7" height="7" rx="1.5"></rect>
                                <rect x="3" y="14" width="7" height="7" rx="1.5"></rect>
                            </svg>
                        </div>
                        <span class="nav-label">Dashboard</span>
                    </a>

                    <!-- Usuarios -->
                    <a href="{{ route('admin.usuarios') }}" class="nav-item" title="Usuarios">
                        <div class="nav-icon-box">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                        <span class="nav-label">Usuarios</span>
                        <span class="nav-badge">Total</span>
                    </a>

                    <!-- Trabajadores -->
                    <a href="{{ url('/admin/trabajadores') }}" class="nav-item" title="Trabajadores">
                        <div class="nav-icon-box">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect x="2" y="7" width="20" height="14" rx="2"></rect>
                                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                            </svg>
                        </div>
                        <span class="nav-label">Trabajadores</span>
                        <span class="nav-badge">Staff</span>
                    </a>

                    <!-- Servicios -->
                    <a href="{{ route('admin.services') }}" class="nav-item" title="Servicios">
                        <div class="nav-icon-box">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="6" cy="6" r="3"></circle>
                                <circle cx="6" cy="18" r="3"></circle>
                                <line x1="20" y1="4" x2="8.12" y2="15.88"></line>
                                <line x1="14.47" y1="14.48" x2="20" y2="20"></line>
                                <line x1="8.12" y1="8.12" x2="12" y2="12"></line>
                            </svg>
                        </div>
                        <span class="nav-label">Servicios</span>
                        <span class="nav-badge">Catálogo</span>
                    </a>

                    <!-- Citas -->
                    <a href="{{ route('admin.citas') }}" class="nav-item" title="Citas">
                        <div class="nav-icon-box">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </div>
                        <span class="nav-label">Citas</span>
                        <span class="nav-badge">Hoy</span>
                    </a>

                    <!-- Configuración General -->
                    <a href="{{ route('admin.configuracion') }}" class="nav-item" title="Configuración general">
                        <div class="nav-icon-box">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="12" cy="12" r="3"></circle>
                                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                            </svg>
                        </div>
                        <span class="nav-label">Configuración</span>
                    </a>

                    <!-- Mi Perfil (Activo) -->
                    <a href="{{ route('admin.perfil') }}" class="nav-item active" aria-current="page" title="Mi Perfil">
                        <div class="nav-icon-box">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>
                        <span class="nav-label">Mi Perfil</span>
                        <span class="nav-badge" style="background: var(--barber-red-light); color: var(--barber-red);">Admin</span>
                    </a>
                </nav>
            </div>
        </aside>

        <!-- BACKDROP MÓVIL -->
        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

        <!-- ÁREA PRINCIPAL -->
        <main class="admin-main" id="main-content">
            <!-- BREADCRUMB -->
            <div class="page-header">
                <div class="breadcrumb">
                    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                    <span>/</span>
                    <span style="color: var(--text-white);">Mi Perfil y Seguridad</span>
                </div>
                <h1 class="page-title">
                    <span>Gestión de Perfil de Administrador</span>
                    <span class="page-title-badge">Nivel 1</span>
                </h1>
            </div>

            <!-- HERO CARD BANNER -->
            <div class="profile-hero-card">
                <div class="hero-avatar-wrap">
                    <div class="hero-avatar" id="heroAvatarCircle">
                        {{ strtoupper(substr($user->name ?? 'AD', 0, 2)) }}
                    </div>
                    <div class="hero-badge-dot" title="Sesión activa y verificada"></div>
                </div>

                <div class="hero-info">
                    <h2>
                        <span id="heroDisplayName">{{ $user->name }} {{ $user->apellidos }}</span>
                    </h2>
                    <p>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                        <span id="heroDisplayEmail">{{ $user->email }}</span>
                    </p>
                    <div class="hero-role-pill">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                        <span>Rol 1: Administrador del Sistema (Acceso Total)</span>
                    </div>
                </div>

                <div class="hero-stats-grid">
                    <div class="hero-stat-pill" title="Total de usuarios registrados en el sistema">
                        <span class="stat-num">{{ $totalUsers ?? 0 }}</span>
                        <span class="stat-lbl">Usuarios</span>
                    </div>
                    <div class="hero-stat-pill" title="Servicios activos en catálogo">
                        <span class="stat-num">{{ $totalServices ?? 0 }}</span>
                        <span class="stat-lbl">Servicios</span>
                    </div>
                    <div class="hero-stat-pill" title="Total de citas procesadas">
                        <span class="stat-num">{{ $totalCitas ?? 0 }}</span>
                        <span class="stat-lbl">Citas</span>
                    </div>
                </div>
            </div>

            <!-- GRID DE FORMULARIOS -->
            <div class="profile-grid">
                
                <!-- CARD 1: DATOS PERSONALES -->
                <div class="form-card">
                    <div class="card-header-custom">
                        <div class="card-icon-box card-icon-blue">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>
                        <div class="card-title-group">
                            <h3>Información del Administrador</h3>
                            <p>Actualiza tus datos de identidad y vías de contacto del sistema.</p>
                        </div>
                    </div>

                    <form id="formDatosPersonales" method="POST" action="{{ route('admin.perfil.datos') }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="admin_name">Nombre(s) <span class="required-star">*</span></label>
                            <div class="input-wrapper">
                                <span class="input-icon-left">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                </span>
                                <input 
                                    type="text" 
                                    id="admin_name" 
                                    name="name" 
                                    class="form-control-custom" 
                                    value="{{ old('name', $user->name) }}" 
                                    required 
                                    placeholder="Nombre del administrador"
                                    autocomplete="given-name"
                                >
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="admin_apellidos">Apellidos <span class="required-star">*</span></label>
                            <div class="input-wrapper">
                                <span class="input-icon-left">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="9" cy="7" r="4"></circle>
                                    </svg>
                                </span>
                                <input 
                                    type="text" 
                                    id="admin_apellidos" 
                                    name="apellidos" 
                                    class="form-control-custom" 
                                    value="{{ old('apellidos', $user->apellidos) }}" 
                                    required 
                                    placeholder="Apellidos del administrador"
                                    autocomplete="family-name"
                                >
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="admin_email">Correo Electrónico <span class="required-star">*</span></label>
                            <div class="input-wrapper">
                                <span class="input-icon-left">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                        <polyline points="22,6 12,13 2,6"></polyline>
                                    </svg>
                                </span>
                                <input 
                                    type="email" 
                                    id="admin_email" 
                                    name="email" 
                                    class="form-control-custom" 
                                    value="{{ old('email', $user->email) }}" 
                                    required 
                                    placeholder="correo@ejemplo.com"
                                    autocomplete="email"
                                >
                            </div>
                            <div class="field-hint">Utilizado como identificador para iniciar sesión en el panel.</div>
                        </div>

                        <div class="form-group">
                            <label for="admin_telefono">Teléfono de Contacto</label>
                            <div class="input-wrapper">
                                <span class="input-icon-left">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                    </svg>
                                </span>
                                <input 
                                    type="tel" 
                                    id="admin_telefono" 
                                    name="telefono" 
                                    class="form-control-custom" 
                                    value="{{ old('telefono', $user->telefono) }}" 
                                    placeholder="Ej. 5551234567"
                                    maxlength="20"
                                    autocomplete="tel"
                                >
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="admin_fecha_nacimiento">Fecha de Nacimiento</label>
                            <div class="input-wrapper">
                                <span class="input-icon-left">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                    </svg>
                                </span>
                                <input 
                                    type="date" 
                                    id="admin_fecha_nacimiento" 
                                    name="fecha_nacimiento" 
                                    class="form-control-custom" 
                                    value="{{ old('fecha_nacimiento', $user->fecha_nacimiento ? substr($user->fecha_nacimiento, 0, 10) : '') }}"
                                    min="1920-01-01"
                                    max="{{ date('Y-m-d') }}"
                                >
                            </div>
                            <div class="field-hint">No se permiten fechas futuras ni posteriores al día de hoy.</div>
                        </div>

                        <button type="submit" class="btn-submit btn-blue" id="btnGuardarDatos">
                            <span class="btn-spinner" id="spinnerDatos"></span>
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                <polyline points="7 3 7 8 15 8"></polyline>
                            </svg>
                            <span>Guardar Datos Personales</span>
                        </button>
                    </form>
                </div>

                <!-- CARD 2: SEGURIDAD Y CREDENCIALES DE NIVEL 1 -->
                <div class="form-card">
                    <div class="card-header-custom">
                        <div class="card-icon-box card-icon-red">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                        </div>
                        <div class="card-title-group">
                            <h3>Credenciales de Nivel 1</h3>
                            <p>Renovación criptográfica de contraseña para el Administrador.</p>
                        </div>
                    </div>

                    <div class="admin-security-alert">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                            <line x1="12" y1="9" x2="12" y2="13"></line>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                        <p><strong>Nivel de Seguridad Crítico:</strong> Esta cuenta posee privilegios totales sobre la base de datos, finanzas y configuración de todo el negocio.</p>
                    </div>

                    <form id="formPassword" method="POST" action="{{ route('admin.perfil.password') }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="admin_current_password">Contraseña Actual <span class="required-star">*</span></label>
                            <div class="input-wrapper">
                                <span class="input-icon-left">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                    </svg>
                                </span>
                                <input 
                                    type="password" 
                                    id="admin_current_password" 
                                    name="current_password" 
                                    class="form-control-custom" 
                                    required 
                                    placeholder="Ingresa tu contraseña actual"
                                    autocomplete="current-password"
                                >
                                <button type="button" class="toggle-password-btn" data-target="admin_current_password" aria-label="Mostrar u ocultar contraseña">
                                    <svg class="eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="admin_password">Nueva Contraseña <span class="required-star">*</span></label>
                            <div class="input-wrapper">
                                <span class="input-icon-left">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 2l-2 2m-1.5 1.5L14 9l-1.5-1.5L10 10l4 4 2.5-2.5L18 13l3.5-3.5a2.12 2.12 0 0 0 0-3l-2-2a2.12 2.12 0 0 0-3 0z"></path>
                                        <path d="M3 21l3.5-3.5"></path>
                                        <path d="M6 18l-1.5 1.5"></path>
                                        <path d="M7.5 19.5L6 21"></path>
                                    </svg>
                                </span>
                                <input 
                                    type="password" 
                                    id="admin_password" 
                                    name="password" 
                                    class="form-control-custom" 
                                    required 
                                    placeholder="Mínimo 8 caracteres alfanuméricos"
                                    autocomplete="new-password"
                                >
                                <button type="button" class="toggle-password-btn" data-target="admin_password" aria-label="Mostrar u ocultar contraseña">
                                    <svg class="eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="admin_password_confirmation">Confirmar Nueva Contraseña <span class="required-star">*</span></label>
                            <div class="input-wrapper">
                                <span class="input-icon-left">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </span>
                                <input 
                                    type="password" 
                                    id="admin_password_confirmation" 
                                    name="password_confirmation" 
                                    class="form-control-custom" 
                                    required 
                                    placeholder="Repite la nueva contraseña"
                                    autocomplete="new-password"
                                >
                                <button type="button" class="toggle-password-btn" data-target="admin_password_confirmation" aria-label="Mostrar u ocultar contraseña">
                                    <svg class="eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- REGLAS DE CONTRASEÑA -->
                        <div class="password-rules-box">
                            <div class="password-rules-title">Requisitos de Seguridad de Nivel 1</div>
                            <div class="rule-item" id="ruleMinLength">
                                <div class="rule-dot"></div>
                                <span>Mínimo 8 caracteres</span>
                            </div>
                            <div class="rule-item" id="ruleAlphaNum">
                                <div class="rule-dot"></div>
                                <span>Alfanumérica (al menos una letra y al menos un número)</span>
                            </div>
                            <div class="rule-item" id="ruleMatch">
                                <div class="rule-dot"></div>
                                <span>Ambas contraseñas deben coincidir exactamente</span>
                            </div>
                        </div>

                        <button type="submit" class="btn-submit btn-red" id="btnGuardarPassword">
                            <span class="btn-spinner" id="spinnerPassword"></span>
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            </svg>
                            <span>Actualizar Credenciales de Nivel 1</span>
                        </button>
                    </form>
                </div>

            </div>
        </main>
    </div>

    <!-- SCRIPTS -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            // --- Menú Desplegable de Usuario (Avatar) ---
            const profileBtn = document.getElementById('profileBtn');
            const profileMenu = document.getElementById('profileMenu');

            if (profileBtn && profileMenu) {
                profileBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    const isExpanded = profileBtn.getAttribute('aria-expanded') === 'true';
                    profileBtn.setAttribute('aria-expanded', !isExpanded);
                    profileMenu.classList.toggle('show', !isExpanded);
                });

                document.addEventListener('click', function (e) {
                    if (!profileBtn.contains(e.target) && !profileMenu.contains(e.target)) {
                        profileBtn.setAttribute('aria-expanded', 'false');
                        profileMenu.classList.remove('show');
                    }
                });
            }

            // --- Menú Lateral (Sidebar) Colapsar / Expandir (Desktop) ---
            const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
            const adminSidebar = document.getElementById('adminSidebar');

            if (sidebarToggleBtn && adminSidebar) {
                sidebarToggleBtn.addEventListener('click', function () {
                    const isCollapsed = adminSidebar.classList.toggle('collapsed');
                    sidebarToggleBtn.setAttribute('aria-expanded', isCollapsed ? 'false' : 'true');
                    sidebarToggleBtn.setAttribute('title', isCollapsed ? 'Expandir menú' : 'Colapsar menú');
                });
            }

            // --- Menú Lateral (Sidebar) en Móvil (Drawer) ---
            const mobileToggleBtn = document.getElementById('mobileToggleBtn');
            const sidebarBackdrop = document.getElementById('sidebarBackdrop');

            if (mobileToggleBtn && adminSidebar && sidebarBackdrop) {
                mobileToggleBtn.addEventListener('click', function () {
                    const isOpen = adminSidebar.classList.toggle('open-mobile');
                    sidebarBackdrop.classList.toggle('active', isOpen);
                    mobileToggleBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                });

                sidebarBackdrop.addEventListener('click', function () {
                    adminSidebar.classList.remove('open-mobile');
                    sidebarBackdrop.classList.remove('active');
                    mobileToggleBtn.setAttribute('aria-expanded', 'false');
                });
            }

            // --- Visibilidad de Contraseñas ---
            document.querySelectorAll('.toggle-password-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const targetId = this.getAttribute('data-target');
                    const input = document.getElementById(targetId);
                    if (!input) return;

                    const isPassword = input.getAttribute('type') === 'password';
                    input.setAttribute('type', isPassword ? 'text' : 'password');
                    this.style.color = isPassword ? 'var(--barber-blue-light)' : 'var(--text-muted)';
                });
            });

            // --- Validador en Tiempo Real de Reglas de Contraseña ---
            const newPassInput = document.getElementById('admin_password');
            const confirmPassInput = document.getElementById('admin_password_confirmation');
            const ruleMinLength = document.getElementById('ruleMinLength');
            const ruleAlphaNum = document.getElementById('ruleAlphaNum');
            const ruleMatch = document.getElementById('ruleMatch');

            function checkPasswordRules() {
                const val = newPassInput.value || '';
                const confirmVal = confirmPassInput.value || '';

                // Regla 1: Mínimo 8 caracteres
                const hasMinLength = val.length >= 8;
                ruleMinLength.classList.toggle('valid', hasMinLength);

                // Regla 2: Alfanumérica (al menos 1 letra y al menos 1 número)
                const hasLetter = /[a-zA-Z]/.test(val);
                const hasNumber = /\d/.test(val);
                const isAlphaNum = hasLetter && hasNumber;
                ruleAlphaNum.classList.toggle('valid', isAlphaNum);

                // Regla 3: Coincidencia
                const matches = val.length > 0 && val === confirmVal;
                ruleMatch.classList.toggle('valid', matches);
            }

            if (newPassInput && confirmPassInput) {
                newPassInput.addEventListener('input', checkPasswordRules);
                confirmPassInput.addEventListener('input', checkPasswordRules);
            }

            // --- Validación Estricta de Fecha de Nacimiento (No mañana ni futuras) ---
            const fechaInput = document.getElementById('admin_fecha_nacimiento');
            if (fechaInput) {
                const today = new Date().toISOString().split('T')[0];
                fechaInput.setAttribute('max', today);

                fechaInput.addEventListener('change', function () {
                    if (this.value && this.value > today) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Fecha inválida',
                            text: 'La fecha de nacimiento no puede ser el día de mañana ni una fecha futura.',
                            confirmButtonColor: '#1e40af'
                        });
                        this.value = '';
                    }
                });
            }

            // --- Envío AJAX de Formulario de Datos Personales ---
            const formDatos = document.getElementById('formDatosPersonales');
            const btnGuardarDatos = document.getElementById('btnGuardarDatos');
            const spinnerDatos = document.getElementById('spinnerDatos');

            if (formDatos) {
                formDatos.addEventListener('submit', async function (e) {
                    e.preventDefault();

                    // Validación de fecha previa al envío
                    if (fechaInput && fechaInput.value) {
                        const today = new Date().toISOString().split('T')[0];
                        if (fechaInput.value > today) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Fecha de nacimiento errónea',
                                text: 'La fecha de nacimiento no puede ser una fecha futura ni el día de mañana.',
                                confirmButtonColor: '#dc2626'
                            });
                            return;
                        }
                    }

                    btnGuardarDatos.disabled = true;
                    if (spinnerDatos) spinnerDatos.style.display = 'inline-block';

                    const formData = new FormData(formDatos);

                    try {
                        const response = await fetch(formDatos.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Datos Actualizados!',
                                text: data.message || 'La información del administrador ha sido actualizada con éxito.',
                                confirmButtonColor: '#1e40af',
                                timer: 3000,
                                timerProgressBar: true
                            });

                            // Actualizar textos en la interfaz dinámica
                            if (data.user) {
                                const fullName = `${data.user.name} ${data.user.apellidos}`.trim();
                                const initials = (data.user.name ? data.user.name.substring(0, 2) : 'AD').toUpperCase();

                                const heroDisplayName = document.getElementById('heroDisplayName');
                                if (heroDisplayName) heroDisplayName.textContent = fullName;

                                const heroDisplayEmail = document.getElementById('heroDisplayEmail');
                                if (heroDisplayEmail) heroDisplayEmail.textContent = data.user.email;

                                const heroAvatarCircle = document.getElementById('heroAvatarCircle');
                                if (heroAvatarCircle) heroAvatarCircle.textContent = initials;

                                const headerAvatar = document.getElementById('headerAvatar');
                                if (headerAvatar) headerAvatar.textContent = initials;

                                const headerUserName = document.getElementById('headerUserName');
                                if (headerUserName) headerUserName.textContent = data.user.name;

                                const headerUserEmail = document.getElementById('headerUserEmail');
                                if (headerUserEmail) headerUserEmail.textContent = data.user.email;
                            }
                        } else {
                            const errorMsg = data.message || (data.errors ? Object.values(data.errors).flat().join('<br>') : 'Error al guardar los datos.');
                            Swal.fire({
                                icon: 'error',
                                title: 'Error de validación',
                                html: errorMsg,
                                confirmButtonColor: '#dc2626'
                            });
                        }
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error de servidor',
                            text: 'No fue posible conectar con el servidor. Intenta nuevamente.',
                            confirmButtonColor: '#dc2626'
                        });
                    } finally {
                        btnGuardarDatos.disabled = false;
                        if (spinnerDatos) spinnerDatos.style.display = 'none';
                    }
                });
            }

            // --- Envío AJAX de Formulario de Contraseña ---
            const formPassword = document.getElementById('formPassword');
            const btnGuardarPassword = document.getElementById('btnGuardarPassword');
            const spinnerPassword = document.getElementById('spinnerPassword');

            if (formPassword) {
                formPassword.addEventListener('submit', async function (e) {
                    e.preventDefault();

                    const currentPass = document.getElementById('admin_current_password').value;
                    const newPass = document.getElementById('admin_password').value;
                    const confirmPass = document.getElementById('admin_password_confirmation').value;

                    if (!currentPass) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Campo obligatorio',
                            text: 'Debes ingresar tu contraseña actual para autenticar el cambio de credenciales.',
                            confirmButtonColor: '#1e40af'
                        });
                        return;
                    }

                    if (newPass.length < 8) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Contraseña insegura',
                            text: 'La nueva contraseña debe tener al menos 8 caracteres.',
                            confirmButtonColor: '#1e40af'
                        });
                        return;
                    }

                    if (newPass !== confirmPass) {
                        Swal.fire({
                            icon: 'error',
                            title: 'No coinciden',
                            text: 'La nueva contraseña y su confirmación no coinciden exactamente.',
                            confirmButtonColor: '#dc2626'
                        });
                        return;
                    }

                    btnGuardarPassword.disabled = true;
                    if (spinnerPassword) spinnerPassword.style.display = 'inline-block';

                    const formData = new FormData(formPassword);

                    try {
                        const response = await fetch(formPassword.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Credenciales Renovadas!',
                                text: data.message || 'Tu contraseña de Administrador ha sido actualizada correctamente.',
                                confirmButtonColor: '#1e40af'
                            });
                            formPassword.reset();
                            checkPasswordRules();
                        } else {
                            const errorMsg = data.message || (data.errors ? Object.values(data.errors).flat().join('<br>') : 'Error al actualizar la contraseña.');
                            Swal.fire({
                                icon: 'error',
                                title: 'Verificación de credenciales',
                                html: errorMsg,
                                confirmButtonColor: '#dc2626'
                            });
                        }
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error de servidor',
                            text: 'Ocurrió un error inesperado al procesar la actualización. Intenta de nuevo.',
                            confirmButtonColor: '#dc2626'
                        });
                    } finally {
                        btnGuardarPassword.disabled = false;
                        if (spinnerPassword) spinnerPassword.style.display = 'none';
                    }
                });
            }
        });
    </script>

    @include('partials.inactivity-timer')
</body>
</html>
