<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beauty & Barber Studio - Panel de Administración</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">

    <style>
        /* ===================================================
           SISTEMA DE DISEÑO MINIMALISTA, SENIOR Y ELEGANTE (ROJO, BLANCO, AZUL)
           =================================================== */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            /* Superficies oscuras, profundas y muy elegantes */
            --bg-canvas: #050a15;
            --bg-header: #0a1124;
            --bg-sidebar: #0a1124;
            
            /* Bordes sutiles para delimitar en modo oscuro sin recargar */
            --border-subtle: rgba(255, 255, 255, 0.06);
            --border-strong: rgba(255, 255, 255, 0.12);

            /* Paleta cromática corporativa (Barber Pole: Red, White, Blue) */
            --barber-red: #7f1d1d;        /* Borgoña sobrio y elegante */
            --barber-red-light: rgba(127, 29, 29, 0.18);
            --barber-blue: #1e3a8a;       /* Azul marino sobrio */
            --barber-blue-light: #60a5fa; 
            --barber-blue-pale: rgba(30, 58, 138, 0.18);

            /* Tipografía */
            --text-main: #f8fafc;         /* Blanco roto casi puro para buena legibilidad */
            --text-muted: #94a3b8;        /* Gris pizarra claro para no competir */
            --text-white: #ffffff;

            /* Dimensiones */
            --header-height: 72px;
            --sidebar-width: 260px;
            --sidebar-collapsed-width: 80px;

            /* Curva de animación premium */
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

        /* ===================================================
           HEADER SUPERIOR (BLANCO CON TOQUES ROJOS/AZULES)
           =================================================== */
        .admin-header {
            background: var(--bg-header);
            border-bottom: 1px solid var(--border-subtle);
            position: sticky;
            top: 0;
            z-index: 50;
            height: var(--header-height);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        }

        /* Barra decorativa minimalista en el tope */
        .header-top-accent-line {
            height: 3px;
            width: 100%;
            background: linear-gradient(90deg, var(--barber-blue) 0%, var(--barber-red) 100%);
        }

        .header-container {
            width: 100%;
            height: calc(var(--header-height) - 3px);
            padding: 0 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        /* Botón móvil */
        .mobile-toggle-btn {
            display: none;
            background: transparent;
            border: 1px solid var(--border-subtle);
            border-radius: 6px;
            width: 40px;
            height: 40px;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--text-muted);
            transition: all 0.2s var(--ease-fluid);
        }

        .mobile-toggle-btn:hover {
            color: var(--barber-blue);
            border-color: rgba(37, 99, 235, 0.28);
            background: var(--barber-blue-pale);
            transform: scale(1.05);
        }

        .mobile-toggle-btn svg {
            width: 20px;
            height: 20px;
        }

        /* Marca y Logotipo */
        .brand-section {
            display: flex;
            align-items: center;
            gap: 16px;
            text-decoration: none;
            color: inherit;
        }

        .brand-logo-img {
            height: 38px;
            width: auto;
            display: block;
        }

        .brand-info {
            display: flex;
            flex-direction: column;
        }

        .brand-name {
            font-size: 19px;
            font-weight: 800;
            color: var(--text-white);
            letter-spacing: -0.01em;
            line-height: 1.2;
        }

        .brand-badge-container {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 2px;
        }

        .brand-role-badge {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            background: rgba(255,255,255,0.05);
            color: var(--text-white);
            border: 1px solid rgba(255,255,255,0.1);
            padding: 2px 8px;
            border-radius: 4px;
        }

        /* --- Opciones de Perfil --- */
        .user-nav {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .profile-dropdown-wrapper {
            position: relative;
        }

        .profile-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            background: transparent;
            border: 1px solid transparent;
            padding: 6px 12px 6px 6px;
            border-radius: 8px;
            cursor: pointer;
            font-family: inherit;
            color: var(--text-main);
            transition: all 0.2s var(--ease-fluid);
        }

        .profile-btn:hover,
        .profile-btn:focus-visible {
            background: var(--barber-blue-pale);
            border-color: var(--border-subtle);
            outline: none;
        }

        /* Avatar del Administrador minimalista */
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--barber-blue);
            color: var(--text-white);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
        }

        .user-meta {
            text-align: left;
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-white);
            line-height: 1.2;
        }

        .user-role {
            font-size: 12px;
            color: var(--text-muted);
            line-height: 1.2;
        }

        .dropdown-arrow {
            width: 16px;
            height: 16px;
            color: var(--text-muted);
            transition: transform 0.2s ease;
        }

        .profile-dropdown-wrapper.open .dropdown-arrow {
            transform: rotate(180deg);
        }

        /* Menú flotante ultra limpio */
        .profile-menu {
            position: absolute;
            top: calc(100% + 12px);
            right: 0;
            width: 220px;
            background: var(--bg-header);
            border: 1px solid var(--border-subtle);
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
            padding: 8px;
            display: none;
            flex-direction: column;
            z-index: 60;
        }

        .profile-dropdown-wrapper.open .profile-menu {
            display: flex;
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-main);
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .menu-item:hover,
        .menu-item:focus {
            background: rgba(255,255,255,0.05);
            color: var(--text-white);
            outline: none;
        }

        .menu-item .menu-item-icon {
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: var(--text-muted);
        }

        .menu-item:hover .menu-item-icon {
            color: var(--barber-blue);
        }

        .menu-divider {
            height: 1px;
            background: var(--border-subtle);
            margin: 8px 0;
        }

        .menu-item.logout-item {
            color: var(--barber-red);
        }

        .menu-item.logout-item .menu-item-icon {
            color: var(--barber-red);
        }

        .menu-item.logout-item:hover {
            background: var(--barber-red-light);
        }

        /* ===================================================
           SIDEBAR MINIMALISTA Y SOFISTICADO
           =================================================== */
        .admin-layout {
            display: flex;
            flex: 1;
            min-height: calc(100vh - var(--header-height));
            position: relative;
            overflow-x: clip;
        }

        /* SIDEBAR MINIMALISTA (FIJO AL HACER SCROLL, SIN SCROLL INTERNO) */
        .admin-sidebar {
            width: var(--sidebar-width);
            background-color: var(--bg-sidebar);
            border-right: 1px solid var(--border-subtle);
            flex-shrink: 0;
            transition: width 0.3s var(--ease-fluid), transform 0.3s var(--ease-fluid);
            display: flex;
            flex-direction: column;
            z-index: 30;
            position: sticky;
            top: var(--header-height);
            height: calc(100vh - var(--header-height));
            align-self: flex-start;
            overflow: hidden;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .admin-sidebar::-webkit-scrollbar {
            display: none;
            width: 0;
            height: 0;
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            height: 60px;
            border-bottom: 1px solid var(--border-subtle);
            box-sizing: border-box;
        }

        .sidebar-title {
            font-size: 12px;
            font-weight: 800;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.15em;
            white-space: nowrap;
        }

        .sidebar-toggle-btn {
            background: transparent;
            border: none;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--text-muted);
            border-radius: 4px;
            transition: all 0.2s var(--ease-fluid);
            flex-shrink: 0;
        }

        .sidebar-toggle-btn:hover,
        .sidebar-toggle-btn:focus-visible {
            background: var(--barber-blue-pale);
            color: var(--barber-blue);
            outline: none;
            transform: scale(1.05);
        }

        .sidebar-toggle-btn svg {
            width: 18px;
            height: 18px;
        }

        /* ===================================================
           ESTADO COLAPSADO (80px)
           =================================================== */
        .admin-sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        .admin-sidebar.collapsed .sidebar-header {
            justify-content: center;
            padding: 0;
        }

        .admin-sidebar.collapsed .sidebar-title {
            display: none;
        }

        .admin-sidebar.collapsed .nav-label {
            display: none;
        }

        .admin-sidebar.collapsed .nav-badge {
            display: none;
        }

        .admin-sidebar.collapsed .nav-item {
            justify-content: center;
            padding: 12px 0;
            margin: 8px 12px;
            border-left: none;
            border-radius: 8px;
        }

        .admin-sidebar.collapsed .nav-item.active {
            background: rgba(255,255,255,0.05);
            border-left: none;
        }
        
        .admin-sidebar.collapsed .nav-item.active .nav-icon-box {
            color: var(--barber-blue);
        }

        .sidebar-inner {
            padding: 16px 0;
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 0;
            overflow: hidden;
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        /* Enlaces de Navegación */
        .nav-item {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 12px 24px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s var(--ease-fluid);
            white-space: nowrap;
            position: relative;
            border-left: 3px solid transparent;
        }

        .nav-item:hover,
        .nav-item:focus-visible {
            background: rgba(255,255,255,0.03);
            color: var(--barber-blue);
            outline: none;
            border-left-color: var(--barber-blue);
        }

        .nav-item:hover .nav-icon-box {
            color: var(--barber-blue);
        }

        .nav-icon-box {
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.2s ease;
        }

        .nav-icon-box svg {
            width: 20px;
            height: 20px;
        }

        /* Opción activa elegante (Modo Oscuro) */
        .nav-item.active {
            color: var(--barber-blue-light);
            font-weight: 700;
            background: var(--barber-blue-pale);
            border-left-color: var(--barber-blue);
        }

        .nav-item.active .nav-icon-box {
            color: var(--barber-blue);
        }

        .nav-label {
            white-space: nowrap;
        }

        .nav-badge {
            margin-left: auto;
            font-size: 11px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 12px;
            background: var(--border-subtle);
            color: var(--text-muted);
        }

        .nav-badge.badge-hot {
            background: var(--barber-red-light);
            color: var(--barber-red);
        }

        .sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background-color: rgba(10, 25, 47, 0.4);
            backdrop-filter: blur(2px);
            z-index: 25;
        }

        /* ===================================================
           ÁREA PRINCIPAL LIMPIA
           =================================================== */
        .main-content {
            flex: 1;
            padding: 40px;
            background-color: var(--bg-canvas);
            min-width: 0;
        }

        .welcome-header {
            padding-bottom: 24px;
        }

        .welcome-header h1 {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-white);
            letter-spacing: -0.03em;
        }

        /* ===================================================
           RESPONSIVIDAD Y DISPOSITIVOS MÓVILES
           =================================================== */
        @media (max-width: 768px) {
            .header-container {
                padding: 0 20px;
            }

            .mobile-toggle-btn {
                display: flex;
            }

            .brand-badge-container,
            .user-meta {
                display: none;
            }

            .main-content {
                padding: 24px 20px;
            }

            .admin-sidebar {
                position: fixed;
                top: var(--header-height);
                bottom: 0;
                left: 0;
                width: 280px;
                transform: translateX(-100%);
                border-right: none;
                box-shadow: 4px 0 24px rgba(0, 0, 0, 0.5);
            }

            .admin-sidebar.open-mobile {
                transform: translateX(0);
            }

            .sidebar-backdrop.active {
                display: block;
            }
        }
        /* ===================================================
           SISTEMA DE DISEÑO DEL DASHBOARD ADMINISTRATIVO
           Beauty & Barber Studio (Dark Mode, Senior & Elegant)
           =================================================== */

        /* --- Envoltorio General del Dashboard --- */
        .dashboard-container {
            display: flex;
            flex-direction: column;
            gap: 28px;
            width: 100%;
            max-width: 1600px;
            margin: 0 auto;
        }

        /* --- 1. ENCABEZADO IDENTIFICABLE DEL DASHBOARD --- */
        .dashboard-header-pane {
            background: linear-gradient(135deg, rgba(10, 17, 36, 0.95) 0%, rgba(13, 23, 51, 0.85) 100%);
            border: 1px solid var(--border-subtle);
            border-radius: 16px;
            padding: 28px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
            position: relative;
            overflow: hidden;
        }

        .dashboard-header-pane::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--barber-blue-light) 0%, var(--barber-blue) 50%, var(--barber-red) 100%);
        }

        .dashboard-title-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .dashboard-badge-row {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 4px 10px;
            border-radius: 20px;
        }

        .status-pill.pill-live {
            background: rgba(16, 185, 129, 0.12);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.25);
        }

        .status-pill.pill-live .pulse-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background-color: #34d399;
            box-shadow: 0 0 8px #34d399;
            animation: pulse-glow 2s infinite;
        }

        @keyframes pulse-glow {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.85); }
        }

        .status-pill.pill-provisional {
            background: rgba(245, 158, 11, 0.12);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.25);
        }

        .status-pill.pill-time {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-muted);
            border: 1px solid var(--border-subtle);
        }

        .dashboard-header-pane h1 {
            font-size: 26px;
            font-weight: 800;
            color: var(--text-white);
            letter-spacing: -0.02em;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .dashboard-header-pane h1 .header-title-icon {
            color: var(--barber-blue-light);
            display: flex;
            align-items: center;
        }

        .dashboard-header-pane p {
            font-size: 14px;
            color: var(--text-muted);
            margin: 0;
            max-width: 680px;
            line-height: 1.5;
        }

        .dashboard-header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn-action-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, var(--barber-blue) 0%, #172554 100%);
            color: var(--text-white);
            border: 1px solid rgba(96, 165, 250, 0.3);
            padding: 10px 18px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.25s var(--ease-fluid);
            box-shadow: 0 4px 12px rgba(30, 58, 138, 0.25);
        }

        .btn-action-primary:hover,
        .btn-action-primary:focus-visible {
            background: linear-gradient(135deg, #1d4ed8 0%, var(--barber-blue) 100%);
            border-color: var(--barber-blue-light);
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(37, 99, 235, 0.35);
            outline: none;
        }

        .btn-action-secondary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.04);
            color: var(--text-main);
            border: 1px solid var(--border-subtle);
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.25s var(--ease-fluid);
        }

        .btn-action-secondary:hover,
        .btn-action-secondary:focus-visible {
            background: rgba(255, 255, 255, 0.08);
            border-color: var(--border-strong);
            color: var(--text-white);
            transform: translateY(-2px);
            outline: none;
        }

        /* --- 2. TARJETAS DE RESUMEN (KPIs PRINCIPALES) --- */
        .dashboard-kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .kpi-card {
            background: var(--bg-header);
            border: 1px solid var(--border-subtle);
            border-radius: 14px;
            padding: 22px 24px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 16px;
            transition: transform 0.25s var(--ease-fluid), box-shadow 0.25s var(--ease-fluid), border-color 0.25s var(--ease-fluid);
            position: relative;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
        }

        .kpi-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: transparent;
            transition: background 0.3s ease;
        }

        .kpi-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.35);
            border-color: var(--border-strong);
        }

        .kpi-card.card-blue::before { background: var(--barber-blue-light); }
        .kpi-card.card-emerald::before { background: #34d399; }
        .kpi-card.card-amber::before { background: #fbbf24; }
        .kpi-card.card-red::before { background: #f87171; }

        .kpi-top-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
        }

        .kpi-icon-box {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: var(--text-white);
            transition: transform 0.25s ease;
        }

        .kpi-card:hover .kpi-icon-box {
            transform: scale(1.08);
        }

        .kpi-card.card-blue .kpi-icon-box {
            background: rgba(30, 58, 138, 0.25);
            color: var(--barber-blue-light);
            border-color: rgba(96, 165, 250, 0.25);
        }

        .kpi-card.card-emerald .kpi-icon-box {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border-color: rgba(52, 211, 153, 0.25);
        }

        .kpi-card.card-amber .kpi-icon-box {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
            border-color: rgba(251, 191, 36, 0.25);
        }

        .kpi-card.card-red .kpi-icon-box {
            background: rgba(127, 29, 29, 0.25);
            color: #f87171;
            border-color: rgba(248, 113, 113, 0.25);
        }

        .kpi-tag-badge {
            font-size: 11px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-muted);
            border: 1px solid var(--border-subtle);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .kpi-body {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .kpi-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .kpi-value-row {
            display: flex;
            align-items: baseline;
            gap: 8px;
        }

        .kpi-value {
            font-size: 32px;
            font-weight: 800;
            color: var(--text-white);
            line-height: 1.1;
            font-feature-settings: "tnum";
            font-variant-numeric: tabular-nums;
        }

        .kpi-unit {
            font-size: 13px;
            font-weight: 500;
            color: var(--text-muted);
        }

        .kpi-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            padding-top: 12px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            font-size: 12px;
        }

        .kpi-status-note {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            color: var(--text-muted);
            font-size: 12px;
        }

        .kpi-status-note.note-empty {
            color: #94a3b8;
        }

        .kpi-link-btn {
            color: var(--barber-blue-light);
            text-decoration: none;
            font-weight: 600;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: color 0.2s ease, transform 0.2s ease;
        }

        .kpi-link-btn:hover {
            color: #93c5fd;
            transform: translateX(2px);
        }

        /* --- SUBTAREA 3: SECCIÓN DE INDICADORES DE CITAS POR ESTADO --- */
        .citas-status-section {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .section-header-compact {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            padding: 0 4px;
        }

        .section-header-left {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .section-pill-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--barber-blue-light);
            background: rgba(30, 58, 138, 0.25);
            border: 1px solid rgba(96, 165, 250, 0.25);
            padding: 3px 10px;
            border-radius: 20px;
            width: fit-content;
        }

        .section-title-sm {
            font-size: 18px;
            font-weight: 800;
            color: var(--text-white);
            display: flex;
            align-items: center;
            gap: 8px;
            letter-spacing: -0.01em;
            margin: 0;
        }

        .section-desc-sm {
            font-size: 13px;
            color: var(--text-muted);
            margin: 0;
        }

        .status-summary-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-subtle);
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 12px;
            color: var(--text-muted);
        }

        .status-summary-badge .summary-badge-val {
            color: var(--text-white);
            font-weight: 800;
            font-size: 14px;
        }

        .citas-status-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .status-tag-completada {
            background: rgba(16, 185, 129, 0.15) !important;
            color: #34d399 !important;
            border-color: rgba(52, 211, 153, 0.3) !important;
        }

        .status-tag-cancelada {
            background: rgba(239, 68, 68, 0.15) !important;
            color: #f87171 !important;
            border-color: rgba(248, 113, 113, 0.3) !important;
        }

        .status-tag-vencida {
            background: rgba(245, 158, 11, 0.15) !important;
            color: #fbbf24 !important;
            border-color: rgba(251, 191, 36, 0.3) !important;
        }

        /* --- 3. TIRA DE INDICADORES ESTADÍSTICOS OPERACIONALES --- */
        .operational-indicators-bar {
            background: var(--bg-header);
            border: 1px solid var(--border-subtle);
            border-radius: 12px;
            padding: 16px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
        }

        .operational-title-box {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .operational-title-box svg {
            color: var(--barber-blue-light);
        }

        .operational-heading {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-white);
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .operational-sub {
            font-size: 12px;
            color: var(--text-muted);
        }

        .operational-metrics-list {
            display: flex;
            align-items: center;
            gap: 24px;
            flex-wrap: wrap;
        }

        .operational-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding-right: 20px;
            border-right: 1px solid var(--border-subtle);
        }

        .operational-item:last-child {
            border-right: none;
            padding-right: 0;
        }

        .operational-bullet {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .bullet-today { background: var(--barber-blue-light); box-shadow: 0 0 6px rgba(96, 165, 250, 0.5); }
        .bullet-confirmed { background: #34d399; box-shadow: 0 0 6px rgba(52, 211, 153, 0.5); }
        .bullet-pending { background: #fbbf24; box-shadow: 0 0 6px rgba(251, 191, 36, 0.5); }
        .bullet-workers { background: #c084fc; box-shadow: 0 0 6px rgba(192, 132, 252, 0.5); }

        .operational-info {
            display: flex;
            flex-direction: column;
        }

        .operational-name {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .operational-count {
            font-size: 16px;
            font-weight: 800;
            color: var(--text-white);
            line-height: 1.2;
        }

        /* --- 4. DISTRIBUCIÓN EN 2 COLUMNAS (CONTENIDO DETALLADO INFERIOR) --- */
        .dashboard-content-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 340px;
            gap: 24px;
            align-items: start;
        }

        .dashboard-main-column {
            display: flex;
            flex-direction: column;
            gap: 24px;
            min-width: 0;
        }

        .dashboard-side-column {
            display: flex;
            flex-direction: column;
            gap: 24px;
            position: sticky;
            top: calc(var(--header-height) + 24px);
        }

        /* Estructura Base de Paneles */
        .dashboard-panel {
            background: var(--bg-header);
            border: 1px solid var(--border-subtle);
            border-radius: 14px;
            padding: 24px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
            display: flex;
            flex-direction: column;
            gap: 20px;
            position: relative;
        }

        .panel-top-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border-subtle);
        }

        .panel-heading-group {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .panel-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-white);
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0;
        }

        .panel-title-icon {
            color: var(--barber-blue-light);
            display: flex;
            align-items: center;
        }

        .panel-subtitle {
            font-size: 12px;
            color: var(--text-muted);
            margin: 0;
        }

        .panel-controls {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .timeframe-pill-group {
            display: inline-flex;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--border-subtle);
            border-radius: 8px;
            padding: 2px;
        }

        .timeframe-pill {
            background: transparent;
            border: none;
            color: var(--text-muted);
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .timeframe-pill.active,
        .timeframe-pill:hover {
            background: var(--barber-blue-pale);
            color: var(--barber-blue-light);
        }

        /* --- 5. ESPACIO DESTINADO A GRÁFICO ESTADÍSTICO --- */
        .chart-visual-wrapper {
            background: rgba(5, 10, 21, 0.6);
            border: 1px dashed var(--border-strong);
            border-radius: 12px;
            padding: 28px 24px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            position: relative;
            min-height: 290px;
            justify-content: center;
            overflow: hidden;
        }

        .chart-grid-mock {
            display: flex;
            flex-direction: column;
            gap: 24px;
            width: 100%;
            opacity: 0.25;
            pointer-events: none;
            user-select: none;
        }

        .chart-grid-line {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
        }

        .chart-grid-label {
            font-size: 11px;
            color: var(--text-muted);
            width: 32px;
            text-align: right;
            font-family: monospace;
        }

        .chart-grid-track {
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0.02) 100%);
        }

        .chart-mock-svg {
            position: absolute;
            top: 24px;
            left: 56px;
            right: 24px;
            bottom: 40px;
            width: calc(100% - 80px);
            height: calc(100% - 64px);
            opacity: 0.35;
            pointer-events: none;
        }

        .chart-overlay-empty {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 12px;
            background: rgba(10, 17, 36, 0.88);
            border: 1px solid var(--border-subtle);
            border-radius: 12px;
            padding: 24px 32px;
            max-width: 520px;
            margin: 0 auto;
            backdrop-filter: blur(4px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.4);
        }

        .chart-empty-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--barber-blue-pale);
            color: var(--barber-blue-light);
            border: 1px solid rgba(96, 165, 250, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .chart-empty-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-white);
            margin: 0;
        }

        .chart-empty-desc {
            font-size: 13px;
            color: var(--text-muted);
            margin: 0;
            line-height: 1.5;
        }

        .chart-legend-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
            padding-top: 10px;
        }

        .legend-item {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: var(--text-muted);
        }

        .legend-chip {
            width: 12px;
            height: 12px;
            border-radius: 3px;
        }

        .legend-chip.chip-citas { background: var(--barber-blue-light); }
        .legend-chip.chip-servicios { background: #34d399; }
        .legend-chip.chip-provisional { background: #fbbf24; }

        /* --- 6. ESPACIO DESTINADO A TABLA DE CITAS / REGISTROS DETALLADOS --- */
        .table-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .table-filter-pills {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .filter-pill {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--border-subtle);
            color: var(--text-muted);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .filter-pill.active,
        .filter-pill:hover {
            background: rgba(255, 255, 255, 0.08);
            color: var(--text-white);
            border-color: var(--border-strong);
        }

        .filter-pill.active {
            border-color: var(--barber-blue-light);
            color: var(--barber-blue-light);
        }

        .data-table-wrapper {
            width: 100%;
            overflow-x: auto;
            border-radius: 8px;
            border: 1px solid var(--border-subtle);
            background: rgba(5, 10, 21, 0.3);
            scrollbar-width: thin;
            scrollbar-color: var(--border-strong) transparent;
        }

        .data-table-wrapper::-webkit-scrollbar {
            height: 6px;
        }

        .data-table-wrapper::-webkit-scrollbar-thumb {
            background: var(--border-strong);
            border-radius: 4px;
        }

        .dashboard-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            min-width: 680px;
        }

        .dashboard-table th {
            padding: 14px 18px;
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            background: rgba(255, 255, 255, 0.02);
            border-bottom: 1px solid var(--border-subtle);
            white-space: nowrap;
        }

        .dashboard-table td {
            padding: 16px 18px;
            font-size: 13px;
            color: var(--text-main);
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            vertical-align: middle;
        }

        .dashboard-table tr:hover td {
            background: rgba(255, 255, 255, 0.02);
        }

        .dashboard-table tr:last-child td {
            border-bottom: none;
        }

        .user-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-cell-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(30, 58, 138, 0.4);
            color: var(--barber-blue-light);
            border: 1px solid rgba(96, 165, 250, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 11px;
            flex-shrink: 0;
        }

        .user-cell-info {
            display: flex;
            flex-direction: column;
        }

        .user-cell-name {
            font-weight: 600;
            color: var(--text-white);
        }

        .user-cell-contact {
            font-size: 11px;
            color: var(--text-muted);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            white-space: nowrap;
        }

        .status-badge.status-pending {
            background: rgba(245, 158, 11, 0.12);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .status-badge.status-confirmed {
            background: var(--barber-blue-pale);
            color: var(--barber-blue-light);
            border: 1px solid rgba(96, 165, 250, 0.3);
        }

        .status-badge.status-completed {
            background: rgba(16, 185, 129, 0.12);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .status-badge.status-cancelled {
            background: var(--barber-red-light);
            color: #f87171;
            border: 1px solid rgba(248, 113, 113, 0.3);
        }

        .sample-row-badge {
            font-size: 10px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 4px;
            background: rgba(255, 255, 255, 0.06);
            color: #94a3b8;
            border: 1px dashed rgba(255, 255, 255, 0.15);
            text-transform: uppercase;
            margin-left: 6px;
        }

        /* --- 7. ESTADOS SIN INFORMACIÓN (EMPTY STATES) --- */
        .dashboard-empty-state {
            padding: 44px 24px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 14px;
            background: rgba(5, 10, 21, 0.4);
            border-radius: 10px;
            border: 1px dashed var(--border-subtle);
        }

        .empty-icon-wrap {
            width: 54px;
            height: 54px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-subtle);
            color: var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .empty-icon-wrap svg {
            width: 24px;
            height: 24px;
        }

        .empty-state-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-white);
            margin: 0;
        }

        .empty-state-text {
            font-size: 13px;
            color: var(--text-muted);
            max-width: 480px;
            margin: 0;
            line-height: 1.5;
        }

        .empty-state-hint {
            font-size: 11px;
            color: #64748b;
            background: rgba(255, 255, 255, 0.02);
            padding: 4px 12px;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.04);
        }

        /* Botón de alternancia de vista de estructura */
        .toggle-preview-btn {
            background: transparent;
            border: 1px dashed var(--border-strong);
            color: var(--barber-blue-light);
            font-size: 12px;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 6px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }

        .toggle-preview-btn:hover {
            background: var(--barber-blue-pale);
            border-color: var(--barber-blue-light);
        }

        /* --- 8. COLUMNA LATERAL: DISTRIBUCIÓN, ACCIONES Y ESTADO --- */
        .category-distribution-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .category-item {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .category-row-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13px;
        }

        .category-name-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-white);
            font-weight: 500;
        }

        .category-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .cat-barber { background: var(--barber-blue-light); }
        .cat-styling { background: #34d399; }
        .cat-hair { background: #fbbf24; }
        .cat-facial { background: #f87171; }

        .category-val-wrap {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 600;
        }

        .category-progress-track {
            width: 100%;
            height: 6px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.05);
            overflow: hidden;
            position: relative;
        }

        .category-progress-fill {
            height: 100%;
            border-radius: 10px;
            width: 0%;
            transition: width 0.6s ease;
        }

        /* Acciones Rápidas */
        .quick-actions-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
        }

        .quick-action-link {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 16px;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--border-subtle);
            border-radius: 10px;
            text-decoration: none;
            color: var(--text-white);
            transition: all 0.25s var(--ease-fluid);
        }

        .quick-action-link:hover,
        .quick-action-link:focus-visible {
            background: var(--barber-blue-pale);
            border-color: rgba(96, 165, 250, 0.3);
            transform: translateX(4px);
            outline: none;
        }

        .quick-action-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.04);
            color: var(--barber-blue-light);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: background 0.2s ease, color 0.2s ease;
        }

        .quick-action-link:hover .quick-action-icon {
            background: var(--barber-blue);
            color: var(--text-white);
        }

        .quick-action-link.action-red .quick-action-icon {
            color: #f87171;
        }

        .quick-action-link.action-red:hover .quick-action-icon {
            background: var(--barber-red);
            color: var(--text-white);
        }

        .quick-action-info {
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .quick-action-title {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-white);
            margin: 0;
        }

        .quick-action-desc {
            font-size: 11px;
            color: var(--text-muted);
            margin: 0;
        }

        .quick-action-arrow {
            color: var(--text-muted);
            opacity: 0.5;
            transition: opacity 0.2s, transform 0.2s;
        }

        .quick-action-link:hover .quick-action-arrow {
            opacity: 1;
            transform: translateX(3px);
            color: var(--barber-blue-light);
        }

        /* Estado del Establecimiento / Sucursal */
        .branch-info-card {
            background: rgba(5, 10, 21, 0.4);
            border: 1px solid var(--border-subtle);
            border-radius: 10px;
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .branch-info-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12px;
        }

        .branch-info-label {
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .branch-info-val {
            color: var(--text-white);
            font-weight: 600;
        }

        /* --- 9. RESPONSIVIDAD INTEGRAL --- */
        @media (max-width: 1280px) {
            .dashboard-kpi-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .citas-status-grid {
                grid-template-columns: repeat(3, 1fr);
            }
            .dashboard-content-grid {
                grid-template-columns: 1fr;
            }
            .dashboard-side-column {
                position: static;
            }
        }

        @media (max-width: 1024px) {
            .citas-status-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .dashboard-container {
                gap: 20px;
            }

            .dashboard-header-pane {
                padding: 20px;
                flex-direction: column;
                align-items: flex-start;
            }

            .dashboard-header-actions {
                width: 100%;
            }

            .dashboard-header-actions .btn-action-primary,
            .dashboard-header-actions .btn-action-secondary {
                flex: 1;
                justify-content: center;
            }

            .dashboard-kpi-grid {
                grid-template-columns: 1fr;
                gap: 14px;
            }

            .citas-status-grid {
                grid-template-columns: 1fr;
                gap: 14px;
            }

            .operational-indicators-bar {
                flex-direction: column;
                align-items: flex-start;
                padding: 16px;
            }

            .operational-metrics-list {
                width: 100%;
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 14px;
            }

            .operational-item {
                border-right: none;
                padding-right: 0;
            }

            .dashboard-panel {
                padding: 18px 16px;
            }

            .chart-visual-wrapper {
                padding: 18px 12px;
                min-height: 240px;
            }

            .chart-overlay-empty {
                padding: 18px 16px;
            }
        }

        @media (max-width: 480px) {
            .operational-metrics-list {
                grid-template-columns: 1fr;
            }
        }

        /* ===================================================
           SUBTAREA 5: ESTILOS PARA GRÁFICOS Y TABLAS DINÁMICAS
           =================================================== */
        .chart-fallback-alert {
            display: flex;
            align-items: center;
            gap: 14px;
            background: rgba(245, 158, 11, 0.12);
            border: 1px solid rgba(245, 158, 11, 0.35);
            border-radius: 10px;
            padding: 14px 18px;
            margin-bottom: 24px;
            color: #fbbf24;
        }

        .chart-fallback-icon {
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: rgba(245, 158, 11, 0.2);
            color: #f59e0b;
        }

        .chart-fallback-title {
            font-size: 14px;
            font-weight: 700;
            color: #fef3c7;
            margin-bottom: 2px;
        }

        .chart-fallback-text {
            font-size: 13px;
            color: #fde68a;
            line-height: 1.4;
        }

        .charts-summary-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 24px;
        }

        .chart-card-wrapper {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .chart-canvas-container {
            position: relative;
            height: 250px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding-top: 8px;
        }

        .chart-canvas-container canvas {
            max-height: 100% !important;
            max-width: 100% !important;
        }

        .chart-table-split-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 22px;
            align-items: start;
            margin-top: 14px;
        }

        .chart-split-col {
            min-width: 0;
        }

        .financial-chart-note {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            font-size: 11.5px;
            line-height: 1.45;
            color: var(--text-muted);
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--border-subtle);
            border-radius: 6px;
            padding: 8px 12px;
            margin-bottom: 12px;
        }

        .financial-chart-note svg {
            flex-shrink: 0;
            margin-top: 2px;
            color: #60a5fa;
        }

        .chart-empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 32px 20px;
            background: rgba(255, 255, 255, 0.015);
            border: 1px dashed var(--border-subtle);
            border-radius: 8px;
            color: var(--text-muted);
            font-size: 13px;
            width: 100%;
            min-height: 190px;
        }

        .chart-empty-state svg {
            margin-bottom: 10px;
            color: var(--text-muted);
            opacity: 0.6;
        }

        .table-responsive-container {
            overflow-x: auto;
            border: 1px solid var(--border-subtle);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.01);
        }

        .dashboard-table-compact {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            text-align: left;
        }

        .dashboard-table-compact th {
            background: rgba(255, 255, 255, 0.03);
            color: var(--text-muted);
            font-weight: 600;
            font-size: 11.5px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 10px 14px;
            border-bottom: 1px solid var(--border-subtle);
            white-space: nowrap;
        }

        .dashboard-table-compact td {
            padding: 10px 14px;
            border-bottom: 1px solid var(--border-subtle);
            color: var(--text-main);
            vertical-align: middle;
        }

        .dashboard-table-compact tr:last-child td {
            border-bottom: none;
        }

        .dashboard-table-compact tr:hover td {
            background: rgba(255, 255, 255, 0.02);
        }

        .badge-position {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            border-radius: 6px;
            font-weight: 800;
            font-size: 11px;
        }

        .badge-position.pos-1 {
            background: rgba(245, 158, 11, 0.2);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.4);
        }

        .badge-position.pos-2 {
            background: rgba(148, 163, 184, 0.2);
            color: #cbd5e1;
            border: 1px solid rgba(148, 163, 184, 0.4);
        }

        .badge-position.pos-3 {
            background: rgba(217, 119, 6, 0.2);
            color: #f59e0b;
            border: 1px solid rgba(217, 119, 6, 0.4);
        }

        .badge-position.pos-default {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-muted);
            border: 1px solid var(--border-subtle);
        }

        .badge-count {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 700;
            background: rgba(59, 130, 246, 0.15);
            color: #60a5fa;
            border: 1px solid rgba(59, 130, 246, 0.25);
        }

        .badge-count.emerald {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.25);
        }

        @media (max-width: 992px) {
            .charts-summary-grid {
                grid-template-columns: 1fr;
            }

            .chart-table-split-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <!-- Enlace accesible de salto al contenido -->
    <a href="#main-content" class="skip-link">Saltar al contenido principal</a>

    <!-- HEADER DEL ADMINISTRADOR CON VIBRACIÓN DE MARCA -->
    <header class="admin-header" role="banner">
        
        <!-- Línea superior multicolor de barbería & salón -->
        <div class="header-top-accent-line" aria-hidden="true"></div>

        <div class="header-container">
            
            <!-- Zona Izquierda: Logotipo y Nombre de la Empresa -->
            <div class="header-left">
                <!-- Botón visible en dispositivos móviles -->
                <button 
                    type="button" 
                    class="mobile-toggle-btn" 
                    id="mobileToggleBtn" 
                    aria-label="Abrir menú de navegación" 
                    aria-expanded="false" 
                    aria-controls="adminSidebar"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>

                <a href="{{ url('/admin') }}" class="brand-section" aria-label="Beauty & Barber Studio - Panel de Inicio">
                    <img 
                        src="{{ asset('images/logo.png') }}" 
                        alt="Logotipo Beauty & Barber Studio" 
                        class="brand-logo-img"
                        onerror="this.style.display='none'"
                    >
                    <div class="brand-info">
                        <span class="brand-name">Beauty & Barber Studio</span>
                        <div class="brand-badge-container">
                            <span class="brand-role-badge">Panel Administrativo</span>
                        </div>
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
                        <div class="user-avatar" aria-hidden="true">{{ strtoupper(substr(Auth::user()->name ?? 'AD', 0, 2)) }}</div>
                        <div class="user-meta">
                            <span class="user-name">{{ Auth::user()->name ?? 'Administrador' }}</span>
                            <span class="user-role">{{ Auth::user()->email ?? 'admin@barberstudio.com' }}</span>
                        </div>
                        <svg class="dropdown-arrow" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <!-- Opciones de perfil accesibles con iconos temáticos -->
                    <div class="profile-menu" id="profileMenu" role="menu" aria-labelledby="profileBtn">
                        <a href="{{ route('admin.perfil') }}" class="menu-item profile-item" role="menuitem">
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

    <!-- CONTENEDOR ESTRUCTURAL: SIDEBAR + ÁREA DE CONTENIDO -->
    <div class="admin-layout" id="adminLayout">

        <!-- MENÚ LATERAL (SIDEBAR) COLAPSABLE CON COLORES VIBRANTES -->
        <aside class="admin-sidebar" id="adminSidebar" aria-label="Menú de navegación lateral">
            
            <!-- Encabezado del menú con el botón para colapsar / expandir -->
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
                    
                    <!-- 1. Dashboard -->
                    <a href="{{ route('admin.dashboard') }}" class="nav-item active" aria-current="page" title="Dashboard">
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

                    <!-- 2. Usuarios -->
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

                    <!-- 3. Trabajadores -->
                    <a href="{{ route('admin.trabajadores.index') }}" class="nav-item" title="Trabajadores">
                        <div class="nav-icon-box">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect x="2" y="7" width="20" height="14" rx="2"></rect>
                                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                            </svg>
                        </div>
                        <span class="nav-label">Trabajadores</span>
                        <span class="nav-badge">Staff</span>
                    </a>

                    <!-- 4. Clientes -->
                    <a href="{{ route('admin.usuarios', ['role_id' => 4]) }}" class="nav-item" title="Clientes Registrados">
                        <div class="nav-icon-box">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>
                        <span class="nav-label">Clientes</span>
                        <span class="nav-badge">Cartera</span>
                    </a>

                    <!-- 5. Servicios -->
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

                    <!-- 6. Horarios Laborales -->
                    <a href="{{ route('admin.configuracion') }}" class="nav-item" title="Consulta y Administración de Horarios Laborales">
                        <div class="nav-icon-box">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </div>
                        <span class="nav-label">Horarios</span>
                        <span class="nav-badge">Turnos</span>
                    </a>

                    <!-- 7. Citas -->
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
                        <span class="nav-badge badge-hot">Hoy</span>
                    </a>

                    <!-- 7. Configuración General -->
                    <a href="{{ route('admin.configuracion') }}" class="nav-item" title="Configuración general">
                        <div class="nav-icon-box">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="12" cy="12" r="3"></circle>
                                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                            </svg>
                        </div>
                        <span class="nav-label">Configuración general</span>
                    </a>

                </nav>
            </div>
        </aside>

        <!-- Fondo para cerrar sidebar en dispositivos móviles -->
        <div class="sidebar-backdrop" id="sidebarBackdrop" aria-hidden="true"></div>

        <!-- ÁREA DE CONTENIDO PRINCIPAL: DASHBOARD ADMINISTRATIVO -->
        <main id="main-content" class="main-content" role="main">
            <div class="dashboard-container">

                <!-- 1. ENCABEZADO IDENTIFICABLE -->
                <header class="dashboard-header-pane" aria-label="Encabezado del Dashboard">
                    <div class="dashboard-title-group">
                        <div class="dashboard-badge-row">
                            <span class="status-pill pill-live">
                                <span class="pulse-dot" aria-hidden="true"></span>
                                <span>Establecimiento Activo</span>
                            </span>
                            <span class="status-pill pill-time" title="Zona Horaria Oficial">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13" aria-hidden="true">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                <span>{{ now()->timezone('America/Merida')->translatedFormat('d \d\e F, Y') }}</span>
                            </span>
                        </div>
                        <h1>
                            <span class="header-title-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="28" height="28">
                                    <rect x="3" y="3" width="7" height="7" rx="1.5"></rect>
                                    <rect x="14" y="3" width="7" height="7" rx="1.5"></rect>
                                    <rect x="14" y="14" width="7" height="7" rx="1.5"></rect>
                                    <rect x="3" y="14" width="7" height="7" rx="1.5"></rect>
                                </svg>
                            </span>
                            <span>Panel de Control Administrativo</span>
                        </h1>
                        <p>Bienvenido al centro de mando de <strong>Beauty & Barber Studio</strong>. Visualiza el resumen general de la empresa, el estado de los colaboradores, el catálogo de servicios y la actividad de la agenda.</p>
                    </div>

                    <!-- Accesos Rápidos del Encabezado -->
                    <div class="dashboard-header-actions" role="toolbar" aria-label="Acciones rápidas del encabezado">
                        <a href="{{ route('admin.citas') }}" class="btn-action-primary" title="Consultar agenda de citas">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16" aria-hidden="true">
                                <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            <span>Auditoría de Citas</span>
                        </a>
                        <a href="{{ route('admin.trabajadores.index') }}" class="btn-action-secondary" title="Ver plantilla de trabajadores">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16" aria-hidden="true">
                                <rect x="2" y="7" width="20" height="14" rx="2"></rect>
                                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                            </svg>
                            <span>Trabajadores</span>
                        </a>
                    </div>
                </header>

                <!-- 2. NIVEL SUPERIOR: TARJETAS DE RESUMEN (KPIs PRINCIPALES) -->
                <section class="dashboard-kpi-grid" aria-label="Tarjetas de Resumen General">
                    
                    <!-- KPI 1: Clientes Registrados -->
                    <article class="kpi-card card-blue" id="kpiCardClientes">
                        <div class="kpi-top-row">
                            <div class="kpi-icon-box" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="24" height="24">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                            </div>
                            <span class="kpi-tag-badge">Comunidad</span>
                        </div>
                        <div class="kpi-body">
                            <span class="kpi-label">Clientes Registrados</span>
                            <div class="kpi-value-row">
                                <span class="kpi-value" id="kpiValueClientes">{{ $totalClientes ?? 0 }}</span>
                                <span class="kpi-unit">cuentas</span>
                            </div>
                        </div>
                        <div class="kpi-footer">
                            <span class="kpi-status-note {{ !isset($totalClientes) || $totalClientes == 0 ? 'note-empty' : '' }}">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13" aria-hidden="true">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="16" x2="12" y2="12"></line>
                                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                </svg>
                                <span>{{ isset($totalClientes) && $totalClientes > 0 ? 'Cartera activa' : '0 registrados en plataforma' }}</span>
                            </span>
                            <a href="{{ route('admin.usuarios', ['role_id' => 4]) }}" class="kpi-link-btn" title="Gestionar clientes">
                                <span>Ver clientes</span>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="12" height="12" aria-hidden="true">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </a>
                        </div>
                    </article>

                    <!-- KPI 2: Trabajadores -->
                    <article class="kpi-card card-emerald" id="kpiCardTrabajadores">
                        <div class="kpi-top-row">
                            <div class="kpi-icon-box" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="24" height="24">
                                    <rect x="2" y="7" width="20" height="14" rx="2"></rect>
                                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                                </svg>
                            </div>
                            <span class="kpi-tag-badge">Equipo</span>
                        </div>
                        <div class="kpi-body">
                            <span class="kpi-label">Trabajadores</span>
                            <div class="kpi-value-row">
                                <span class="kpi-value" id="kpiValueTrabajadores">{{ $totalTrabajadores ?? 0 }}</span>
                                <span class="kpi-unit">especialistas</span>
                            </div>
                        </div>
                        <div class="kpi-footer">
                            <span class="kpi-status-note {{ !isset($totalTrabajadores) || $totalTrabajadores == 0 ? 'note-empty' : '' }}">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13" aria-hidden="true">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="16" x2="12" y2="12"></line>
                                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                </svg>
                                <span>{{ isset($totalTrabajadores) && $totalTrabajadores > 0 ? 'Plantilla activa' : '0 en plantilla de staff' }}</span>
                            </span>
                            <a href="{{ route('admin.trabajadores.index') }}" class="kpi-link-btn" title="Gestionar trabajadores">
                                <span>Ver equipo</span>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="12" height="12" aria-hidden="true">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </a>
                        </div>
                    </article>

                    <!-- KPI 3: Servicios Disponibles -->
                    <article class="kpi-card card-amber" id="kpiCardServicios">
                        <div class="kpi-top-row">
                            <div class="kpi-icon-box" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="24" height="24">
                                    <circle cx="6" cy="6" r="3"></circle>
                                    <circle cx="6" cy="18" r="3"></circle>
                                    <line x1="20" y1="4" x2="8.12" y2="15.88"></line>
                                    <line x1="14.47" y1="14.48" x2="20" y2="20"></line>
                                    <line x1="8.12" y1="8.12" x2="12" y2="12"></line>
                                </svg>
                            </div>
                            <span class="kpi-tag-badge">Catálogo</span>
                        </div>
                        <div class="kpi-body">
                            <span class="kpi-label">Servicios Disponibles</span>
                            <div class="kpi-value-row">
                                <span class="kpi-value" id="kpiValueServicios">{{ $totalServicios ?? 0 }}</span>
                                <span class="kpi-unit">activos</span>
                            </div>
                        </div>
                        <div class="kpi-footer">
                            <span class="kpi-status-note {{ !isset($totalServicios) || $totalServicios == 0 ? 'note-empty' : '' }}">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13" aria-hidden="true">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="16" x2="12" y2="12"></line>
                                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                </svg>
                                <span>{{ isset($totalServicios) && $totalServicios > 0 ? 'Servicios en catálogo' : '0 servicios catalogados' }}</span>
                            </span>
                            <a href="{{ route('admin.services') }}" class="kpi-link-btn" title="Gestionar catálogo de servicios">
                                <span>Ver servicios</span>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="12" height="12" aria-hidden="true">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </a>
                        </div>
                    </article>

                    <!-- KPI 4: Citas Registradas -->
                    <article class="kpi-card card-red" id="kpiCardCitas">
                        <div class="kpi-top-row">
                            <div class="kpi-icon-box" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="24" height="24">
                                    <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                            </div>
                            <span class="kpi-tag-badge">Agenda</span>
                        </div>
                        <div class="kpi-body">
                            <span class="kpi-label">Citas Registradas</span>
                            <div class="kpi-value-row">
                                <span class="kpi-value" id="kpiValueCitas">{{ $totalCitas ?? 0 }}</span>
                                <span class="kpi-unit">agendadas</span>
                            </div>
                        </div>
                        <div class="kpi-footer">
                            <span class="kpi-status-note {{ !isset($totalCitas) || $totalCitas == 0 ? 'note-empty' : '' }}">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13" aria-hidden="true">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="16" x2="12" y2="12"></line>
                                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                </svg>
                                <span>{{ isset($totalCitas) && $totalCitas > 0 ? 'Historial acumulado' : '0 en historial general' }}</span>
                            </span>
                            <a href="{{ route('admin.citas') }}" class="kpi-link-btn" title="Ver agenda de citas">
                                <span>Ver agenda</span>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="12" height="12" aria-hidden="true">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </a>
                        </div>
                    </article>

                </section>

                <!-- ===================================================
                     SUBTAREA 3: CLASIFICACIÓN DE CITAS SEGÚN SU ESTADO
                     (Completada, Cancelada, Vencida)
                     =================================================== -->
                <section class="citas-status-section" aria-labelledby="statusSectionHeading" id="sectionCitasPorEstado">
                    <div class="section-header-compact">
                        <div class="section-header-left">
                            <div class="section-pill-tag">
                                <span class="status-bullet-dot" aria-hidden="true"></span>
                                <span>Clasificación Operativa</span>
                            </div>
                            <h2 class="section-title-sm" id="statusSectionHeading">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="18" height="18" aria-hidden="true">
                                    <path d="M9 11l3 3L22 4"></path>
                                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                                </svg>
                                <span>Citas por Estado</span>
                            </h2>
                            <p class="section-desc-sm">Contadores independientes por estado de atención en base de datos (independientes del estado de cobro)</p>
                        </div>
                        <div class="status-summary-badge" title="Subtotal de citas en las tres categorías auditadas vs total de reservaciones">
                            <span class="summary-badge-label">Subtotal agrupado:</span>
                            <strong class="summary-badge-val" id="subtotalAuditadoVal">{{ ($citasCompletadas ?? 0) + ($citasCanceladas ?? 0) + ($citasVencidas ?? 0) }}</strong>
                            <span class="summary-badge-total">/ {{ $totalCitas ?? 0 }} total general</span>
                        </div>
                    </div>

                    <div class="citas-status-grid" role="region" aria-label="Indicadores individuales de citas por estado">
                        
                        <!-- ESTADO 1: Citas Completadas -->
                        <article class="kpi-card status-card card-emerald" id="kpiCardCitasCompletadas">
                            <div class="kpi-top-row">
                                <div class="kpi-icon-box" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="24" height="24">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                    </svg>
                                </div>
                                <span class="kpi-tag-badge status-tag-completada" title="Estado: Completada">Completada</span>
                            </div>
                            <div class="kpi-body">
                                <h3 class="kpi-label">Citas Completadas</h3>
                                <div class="kpi-value-row">
                                    <span class="kpi-value" id="kpiValueCitasCompletadas">{{ $citasCompletadas ?? 0 }}</span>
                                    <span class="kpi-unit">concluidas</span>
                                </div>
                            </div>
                            <div class="kpi-footer">
                                <span class="kpi-status-note {{ !isset($citasCompletadas) || $citasCompletadas == 0 ? 'note-empty' : '' }}">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13" aria-hidden="true">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="12" y1="16" x2="12" y2="12"></line>
                                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                    </svg>
                                    <span>{{ isset($citasCompletadas) && $citasCompletadas > 0 ? 'Servicios finalizados con éxito' : '0 citas concluidas' }}</span>
                                </span>
                                <a href="{{ route('admin.citas') }}" class="kpi-link-btn" title="Consultar agenda de citas">
                                    <span>Ver agenda</span>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="12" height="12" aria-hidden="true">
                                        <polyline points="9 18 15 12 9 6"></polyline>
                                    </svg>
                                </a>
                            </div>
                        </article>

                        <!-- ESTADO 2: Citas Canceladas -->
                        <article class="kpi-card status-card card-red" id="kpiCardCitasCanceladas">
                            <div class="kpi-top-row">
                                <div class="kpi-icon-box" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="24" height="24">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="15" y1="9" x2="9" y2="15"></line>
                                        <line x1="9" y1="9" x2="15" y2="15"></line>
                                    </svg>
                                </div>
                                <span class="kpi-tag-badge status-tag-cancelada" title="Estado: Cancelada">Cancelada</span>
                            </div>
                            <div class="kpi-body">
                                <h3 class="kpi-label">Citas Canceladas</h3>
                                <div class="kpi-value-row">
                                    <span class="kpi-value" id="kpiValueCitasCanceladas">{{ $citasCanceladas ?? 0 }}</span>
                                    <span class="kpi-unit">anuladas</span>
                                </div>
                            </div>
                            <div class="kpi-footer">
                                <span class="kpi-status-note {{ !isset($citasCanceladas) || $citasCanceladas == 0 ? 'note-empty' : '' }}">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13" aria-hidden="true">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="12" y1="16" x2="12" y2="12"></line>
                                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                    </svg>
                                    <span>{{ isset($citasCanceladas) && $citasCanceladas > 0 ? 'Anuladas por cliente o recepción' : '0 citas canceladas' }}</span>
                                </span>
                                <a href="{{ route('admin.citas') }}" class="kpi-link-btn" title="Consultar agenda de citas">
                                    <span>Ver agenda</span>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="12" height="12" aria-hidden="true">
                                        <polyline points="9 18 15 12 9 6"></polyline>
                                    </svg>
                                </a>
                            </div>
                        </article>

                        <!-- ESTADO 3: Citas Vencidas -->
                        <article class="kpi-card status-card card-amber" id="kpiCardCitasVencidas">
                            <div class="kpi-top-row">
                                <div class="kpi-icon-box" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="24" height="24">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12 6 12 12 16 14"></polyline>
                                        <line x1="12" y1="2" x2="12" y2="4"></line>
                                    </svg>
                                </div>
                                <span class="kpi-tag-badge status-tag-vencida" title="Estado: Vencida">Vencida</span>
                            </div>
                            <div class="kpi-body">
                                <h3 class="kpi-label">Citas Vencidas</h3>
                                <div class="kpi-value-row">
                                    <span class="kpi-value" id="kpiValueCitasVencidas">{{ $citasVencidas ?? 0 }}</span>
                                    <span class="kpi-unit">no asistidas</span>
                                </div>
                            </div>
                            <div class="kpi-footer">
                                <span class="kpi-status-note {{ !isset($citasVencidas) || $citasVencidas == 0 ? 'note-empty' : '' }}">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13" aria-hidden="true">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="12" y1="16" x2="12" y2="12"></line>
                                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                    </svg>
                                    <span>{{ isset($citasVencidas) && $citasVencidas > 0 ? 'Horario expirado sin atención' : '0 citas vencidas' }}</span>
                                </span>
                                <a href="{{ route('admin.citas') }}" class="kpi-link-btn" title="Consultar agenda de citas">
                                    <span>Ver agenda</span>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="12" height="12" aria-hidden="true">
                                        <polyline points="9 18 15 12 9 6"></polyline>
                                    </svg>
                                </a>
                            </div>
                        </article>

                    </div>
                </section>

                <!-- ===================================================
                     SUBTAREA 4: INDICADORES DE INGRESOS FINANCIEROS
                     (Día y Mes en curso)
                     =================================================== -->
                <section class="citas-status-section" aria-labelledby="revenueSectionHeading" id="sectionIngresosFinancieros">
                    <div class="section-header-compact">
                        <div class="section-header-left">
                            <div class="section-pill-tag" style="background: rgba(16, 185, 129, 0.15); border-color: rgba(52, 211, 153, 0.3); color: #34d399;">
                                <span class="status-bullet-dot" style="background: #34d399;" aria-hidden="true"></span>
                                <span>Cálculo Financiero</span>
                            </div>
                            <h2 class="section-title-sm" id="revenueSectionHeading">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="18" height="18" aria-hidden="true">
                                    <line x1="12" y1="1" x2="12" y2="23"></line>
                                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                </svg>
                                <span>Ingresos del Establecimiento</span>
                            </h2>
                            <p class="section-desc-sm">Importes efectivos de citas pagadas y no canceladas (cálculo basado en citas programadas para el periodo)</p>
                        </div>
                    </div>

                    <div class="citas-status-grid" style="grid-template-columns: repeat(2, 1fr);" role="region" aria-label="Tarjetas de ingresos del establecimiento">
                        
                        <!-- Tarjeta 1: Ingresos del Día -->
                        <article class="kpi-card status-card card-emerald" id="kpiCardIngresosDia">
                            <div class="kpi-top-row">
                                <div class="kpi-icon-box" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="24" height="24">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12 6 12 12 16 14"></polyline>
                                    </svg>
                                </div>
                                <span class="kpi-tag-badge status-tag-completada" title="Corte del día">Corte Diario</span>
                            </div>
                            <div class="kpi-body">
                                <h3 class="kpi-label">Ingresos por Citas de Hoy</h3>
                                <div class="kpi-value-row">
                                    <span class="kpi-value" id="kpiValueIngresosDia" style="font-size: 24px;">{{ $ingresosDia['total_ingresos_mxn'] ?? '$0.00 MXN' }}</span>
                                </div>
                            </div>
                            <div class="kpi-footer">
                                <span class="kpi-status-note {{ ($ingresosDia['citas_pagadas_count'] ?? 0) == 0 ? 'note-empty' : '' }}">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13" aria-hidden="true">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                    </svg>
                                    <span>{{ $ingresosDia['citas_pagadas_count'] ?? 0 }} citas pagadas con fecha de hoy</span>
                                </span>
                                <a href="{{ route('admin.citas') }}" class="kpi-link-btn" title="Ver detalle de citas">
                                    <span>Auditar</span>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="12" height="12" aria-hidden="true">
                                        <polyline points="9 18 15 12 9 6"></polyline>
                                    </svg>
                                </a>
                            </div>
                        </article>

                        <!-- Tarjeta 2: Ingresos del Mes -->
                        <article class="kpi-card status-card card-blue" id="kpiCardIngresosMes">
                            <div class="kpi-top-row">
                                <div class="kpi-icon-box" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="24" height="24">
                                        <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                    </svg>
                                </div>
                                <span class="kpi-tag-badge" style="background: rgba(59, 130, 246, 0.15); color: #60a5fa; border: 1px solid rgba(96, 165, 250, 0.3);" title="Mes calendario actual">{{ $ingresosMes['mes_nombre'] ?? 'Mes Actual' }}</span>
                            </div>
                            <div class="kpi-body">
                                <h3 class="kpi-label">Ingresos por Citas del Mes</h3>
                                <div class="kpi-value-row">
                                    <span class="kpi-value" id="kpiValueIngresosMes" style="font-size: 24px;">{{ $ingresosMes['total_ingresos_mxn'] ?? '$0.00 MXN' }}</span>
                                </div>
                            </div>
                            <div class="kpi-footer">
                                <span class="kpi-status-note {{ ($ingresosMes['citas_pagadas_count'] ?? 0) == 0 ? 'note-empty' : '' }}">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13" aria-hidden="true">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                    </svg>
                                    <span>{{ $ingresosMes['citas_pagadas_count'] ?? 0 }} citas pagadas en el mes</span>
                                </span>
                                <a href="{{ route('admin.citas') }}" class="kpi-link-btn" title="Ver detalle de citas">
                                    <span>Auditar</span>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="12" height="12" aria-hidden="true">
                                        <polyline points="9 18 15 12 9 6"></polyline>
                                    </svg>
                                </a>
                            </div>
                        </article>

                    </div>
                </section>

                <!-- 3. SECCIÓN INTERMEDIA: INDICADORES ESTADÍSTICOS OPERACIONALES -->
                <section class="operational-indicators-bar" aria-label="Indicadores Operacionales del Día">
                    <div class="operational-title-box">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20" aria-hidden="true">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                        </svg>
                        <div>
                            <span class="operational-heading">Actividad Operativa del Día</span>
                            <div class="operational-sub">Monitoreo de estado de citas y disponibilidad de turnos</div>
                        </div>
                    </div>

                    <div class="operational-metrics-list">
                        <!-- Citas Hoy -->
                        <div class="operational-item">
                            <span class="operational-bullet bullet-today" aria-hidden="true"></span>
                            <div class="operational-info">
                                <span class="operational-name">Citas para Hoy</span>
                                <span class="operational-count">{{ $citasHoy ?? 0 }}</span>
                            </div>
                        </div>

                        <!-- Confirmadas -->
                        <div class="operational-item">
                            <span class="operational-bullet bullet-confirmed" aria-hidden="true"></span>
                            <div class="operational-info">
                                <span class="operational-name">Confirmadas</span>
                                <span class="operational-count">{{ $citasConfirmadas ?? 0 }}</span>
                            </div>
                        </div>

                        <!-- Pendientes -->
                        <div class="operational-item">
                            <span class="operational-bullet bullet-pending" aria-hidden="true"></span>
                            <div class="operational-info">
                                <span class="operational-name">Pendientes</span>
                                <span class="operational-count">{{ $citasPendientes ?? 0 }}</span>
                            </div>
                        </div>

                        <!-- En Turno -->
                        <div class="operational-item">
                            <span class="operational-bullet bullet-workers" aria-hidden="true"></span>
                            <div class="operational-info">
                                <span class="operational-name">Staff en Turno</span>
                                <span class="operational-count">{{ $trabajadoresActivos ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- 4. NIVEL INFERIOR: GRÁFICOS Y TABLAS (ORGANIZACIÓN EN 2 COLUMNAS) -->
                <div class="dashboard-content-grid">

                    <!-- COLUMNA PRINCIPAL (IZQUIERDA) -->
                    <div class="dashboard-main-column">

                        <!-- AVISO DE FALLBACK EN CASO DE QUE CHART.JS NO CARGUE -->
                        <div id="chartFallbackAlert" class="chart-fallback-alert" style="display: none;" role="alert">
                            <div class="chart-fallback-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="8" x2="12" y2="12"></line>
                                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                </svg>
                            </div>
                            <div class="chart-fallback-content">
                                <p class="chart-fallback-title">Visualización gráfica no disponible temporalmente</p>
                                <p class="chart-fallback-text">La biblioteca gráfica (Chart.js) no pudo cargarse en este momento. La información completa, conteos y montos continúan disponibles en las tablas y tarjetas complementarias del panel.</p>
                            </div>
                        </div>

                        <!-- 1. BLOQUE DE RESUMEN: CITAS POR ESTADO Y COMPARATIVO FINANCIERO -->
                        <div class="charts-summary-grid">

                            <!-- GRÁFICO 1: DISTRIBUCIÓN DE CITAS POR ESTADO -->
                            <section class="dashboard-panel chart-card-wrapper" id="sectionChartCitasEstado" aria-labelledby="chartCitasTitle">
                                <div class="panel-top-bar">
                                    <div class="panel-heading-group">
                                        <h2 class="panel-title" id="chartCitasTitle">
                                            <span class="panel-title-icon" aria-hidden="true">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="18" height="18">
                                                    <circle cx="12" cy="12" r="10"></circle>
                                                    <polyline points="12 6 12 12 16 14"></polyline>
                                                </svg>
                                            </span>
                                            <span>Estados de Citas</span>
                                        </h2>
                                        <span class="panel-subtitle">Completadas, canceladas y vencidas</span>
                                    </div>
                                </div>

                                <div class="chart-canvas-container" role="region" aria-label="Gráfico de distribución de citas">
                                    <canvas id="chartCitasEstadoCanvas" aria-label="Distribución proporcional de citas por estado" role="img"></canvas>
                                    <div id="emptyChartCitasEstado" class="chart-empty-state" style="display: none;">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="32" height="32" aria-hidden="true">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <line x1="12" y1="8" x2="12" y2="12"></line>
                                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                        </svg>
                                        <span>No hay datos disponibles para mostrar</span>
                                    </div>
                                </div>
                            </section>

                            <!-- GRÁFICO 2: COMPARATIVO FINANCIERO DE INGRESOS -->
                            <section class="dashboard-panel chart-card-wrapper" id="sectionChartIngresos" aria-labelledby="chartIngresosTitle">
                                <div class="panel-top-bar">
                                    <div class="panel-heading-group">
                                        <h2 class="panel-title" id="chartIngresosTitle">
                                            <span class="panel-title-icon" aria-hidden="true">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="18" height="18">
                                                    <line x1="12" y1="1" x2="12" y2="23"></line>
                                                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                                </svg>
                                            </span>
                                            <span>Comparativo de Ingresos</span>
                                        </h2>
                                        <span class="panel-subtitle">Ingresos cobrados (Día vs. Mes en curso)</span>
                                    </div>
                                </div>

                                <div class="financial-chart-note" role="note">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14" aria-hidden="true">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="12" y1="16" x2="12" y2="12"></line>
                                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                    </svg>
                                    <span>Los ingresos mensuales comprenden el acumulado del mes calendario en curso e incluyen los del día. Se calculan según la fecha programada de las citas.</span>
                                </div>

                                <div class="chart-canvas-container" role="region" aria-label="Gráfico comparativo de ingresos">
                                    <canvas id="chartIngresosCanvas" aria-label="Comparativo de ingresos del día versus mes" role="img"></canvas>
                                    <div id="emptyChartIngresos" class="chart-empty-state" style="display: none;">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="32" height="32" aria-hidden="true">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <line x1="12" y1="8" x2="12" y2="12"></line>
                                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                        </svg>
                                        <span>No hay datos disponibles para mostrar</span>
                                    </div>
                                </div>
                            </section>

                        </div>

                        <!-- 2. BLOQUE: DEMANDA DE SERVICIOS (GRÁFICO + TABLA COMPLEMENTARIA) -->
                        <section class="dashboard-panel" id="sectionServiciosDemanda" aria-labelledby="serviciosDemandaTitle" style="margin-bottom: 24px;">
                            <div class="panel-top-bar">
                                <div class="panel-heading-group">
                                    <h2 class="panel-title" id="serviciosDemandaTitle">
                                        <span class="panel-title-icon" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="18" height="18">
                                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                            </svg>
                                        </span>
                                        <span>Servicios Más Solicitados</span>
                                    </h2>
                                    <span class="panel-subtitle">Demanda en orden descendente (excluye citas canceladas)</span>
                                </div>
                            </div>

                            <div class="chart-table-split-grid">
                                <!-- Gráfico de Servicios -->
                                <div class="chart-split-col">
                                    <div class="chart-canvas-container" style="height: 230px;" role="region" aria-label="Gráfico de servicios más solicitados">
                                        <canvas id="chartServiciosCanvas" aria-label="Gráfico de barras de servicios más solicitados" role="img"></canvas>
                                        <div id="emptyChartServicios" class="chart-empty-state" style="display: none;">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="32" height="32" aria-hidden="true">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                            </svg>
                                            <span>No hay citas registradas para generar el ranking de servicios.</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tabla Complementaria de Servicios -->
                                <div class="chart-split-col">
                                    <div class="table-responsive-container">
                                        <table class="dashboard-table-compact" id="tablaServiciosMasSolicitados" aria-label="Tabla complementaria de servicios más solicitados">
                                            <thead>
                                                <tr>
                                                    <th scope="col" style="width: 44px; text-align: center;">Pos</th>
                                                    <th scope="col">Servicio</th>
                                                    <th scope="col">Categoría</th>
                                                    <th scope="col" style="text-align: right;">Citas</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($serviciosMasSolicitados as $idx => $servicio)
                                                    <tr>
                                                        <td style="text-align: center;">
                                                            <span class="badge-position {{ $idx === 0 ? 'pos-1' : ($idx === 1 ? 'pos-2' : ($idx === 2 ? 'pos-3' : 'pos-default')) }}">
                                                                {{ $idx + 1 }}
                                                            </span>
                                                        </td>
                                                        <td><strong>{{ $servicio->nombre }}</strong></td>
                                                        <td>
                                                            <span style="color: var(--text-muted); font-size: 12px;">{{ ucfirst($servicio->categoria ?? 'General') }}</span>
                                                        </td>
                                                        <td style="text-align: right;">
                                                            <span class="badge-count">{{ $servicio->total_citas }}</span>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" style="text-align: center; padding: 24px; color: var(--text-muted);">
                                                            No hay citas registradas para generar el ranking de servicios.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- 3. BLOQUE: PERSONAL CON MAYOR DEMANDA (GRÁFICO + TABLA COMPLEMENTARIA) -->
                        <section class="dashboard-panel" id="sectionPersonalDemanda" aria-labelledby="personalDemandaTitle">
                            <div class="panel-top-bar">
                                <div class="panel-heading-group">
                                    <h2 class="panel-title" id="personalDemandaTitle">
                                        <span class="panel-title-icon" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="18" height="18">
                                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                                <circle cx="9" cy="7" r="4"></circle>
                                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                            </svg>
                                        </span>
                                        <span>Personal con Mayor Demanda</span>
                                    </h2>
                                    <span class="panel-subtitle">Barberos y estilistas en orden descendente (excluye citas canceladas)</span>
                                </div>
                            </div>

                            <div class="chart-table-split-grid">
                                <!-- Gráfico de Trabajadores -->
                                <div class="chart-split-col">
                                    <div class="chart-canvas-container" style="height: 230px;" role="region" aria-label="Gráfico de personal con mayor demanda">
                                        <canvas id="chartTrabajadoresCanvas" aria-label="Gráfico de barras de personal con mayor cantidad de citas" role="img"></canvas>
                                        <div id="emptyChartTrabajadores" class="chart-empty-state" style="display: none;">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="32" height="32" aria-hidden="true">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                            </svg>
                                            <span>No hay citas registradas para generar el ranking de trabajadores.</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tabla Complementaria de Trabajadores -->
                                <div class="chart-split-col">
                                    <div class="table-responsive-container">
                                        <table class="dashboard-table-compact" id="tablaTrabajadoresMasCitas" aria-label="Tabla complementaria de personal con mayor demanda">
                                            <thead>
                                                <tr>
                                                    <th scope="col" style="width: 44px; text-align: center;">Pos</th>
                                                    <th scope="col">Trabajador</th>
                                                    <th scope="col">Función</th>
                                                    <th scope="col" style="text-align: right;">Citas</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($trabajadoresConMasCitas as $idx => $trabajador)
                                                    <tr>
                                                        <td style="text-align: center;">
                                                            <span class="badge-position {{ $idx === 0 ? 'pos-1' : ($idx === 1 ? 'pos-2' : ($idx === 2 ? 'pos-3' : 'pos-default')) }}">
                                                                {{ $idx + 1 }}
                                                            </span>
                                                        </td>
                                                        <td><strong>{{ $trabajador->nombre }}</strong></td>
                                                        <td>
                                                            <span style="color: var(--text-muted); font-size: 12px;">{{ $trabajador->funcion }}</span>
                                                        </td>
                                                        <td style="text-align: right;">
                                                            <span class="badge-count emerald">{{ $trabajador->total_citas }}</span>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" style="text-align: center; padding: 24px; color: var(--text-muted);">
                                                            No hay citas registradas para generar el ranking de trabajadores.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>

                    </div>

                    <!-- COLUMNA LATERAL (DERECHA) -->
                    <aside class="dashboard-side-column" aria-label="Paneles secundarios del dashboard">

                        <!-- PANEL DE ACCIONES RÁPIDAS ADMINISTRATIVAS -->
                        <section class="dashboard-panel" aria-labelledby="quickActionsTitle">
                            <div class="panel-top-bar">
                                <div class="panel-heading-group">
                                    <h2 class="panel-title" id="quickActionsTitle">
                                        <span class="panel-title-icon" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="18" height="18">
                                                <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                                            </svg>
                                        </span>
                                        <span>Gestión Directa</span>
                                    </h2>
                                    <span class="panel-subtitle">Accesos rápidos a módulos principales</span>
                                </div>
                            </div>

                            <div class="quick-actions-grid" role="navigation" aria-label="Enlaces rápidos de administración">
                                <!-- Enlace a Trabajadores -->
                                <a href="{{ route('admin.trabajadores.index') }}" class="quick-action-link">
                                    <div class="quick-action-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                                            <rect x="2" y="7" width="20" height="14" rx="2"></rect>
                                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                                        </svg>
                                    </div>
                                    <div class="quick-action-info">
                                        <h3 class="quick-action-title">Gestionar Trabajadores</h3>
                                        <span class="quick-action-desc">Alta de staff, asignación y turnos</span>
                                    </div>
                                    <svg class="quick-action-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="14" height="14" aria-hidden="true">
                                        <polyline points="9 18 15 12 9 6"></polyline>
                                    </svg>
                                </a>

                                <!-- Enlace a Servicios -->
                                <a href="{{ route('admin.services') }}" class="quick-action-link">
                                    <div class="quick-action-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                                            <circle cx="6" cy="6" r="3"></circle>
                                            <circle cx="6" cy="18" r="3"></circle>
                                            <line x1="20" y1="4" x2="8.12" y2="15.88"></line>
                                            <line x1="14.47" y1="14.48" x2="20" y2="20"></line>
                                            <line x1="8.12" y1="8.12" x2="12" y2="12"></line>
                                        </svg>
                                    </div>
                                    <div class="quick-action-info">
                                        <h3 class="quick-action-title">Catálogo de Servicios</h3>
                                        <span class="quick-action-desc">Tarifas, tiempos y categorías</span>
                                    </div>
                                    <svg class="quick-action-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="14" height="14" aria-hidden="true">
                                        <polyline points="9 18 15 12 9 6"></polyline>
                                    </svg>
                                </a>

                                <!-- Enlace a Clientes -->
                                <a href="{{ route('admin.usuarios', ['role_id' => 4]) }}" class="quick-action-link">
                                    <div class="quick-action-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="12" cy="7" r="4"></circle>
                                        </svg>
                                    </div>
                                    <div class="quick-action-info">
                                        <h3 class="quick-action-title">Cartera de Clientes</h3>
                                        <span class="quick-action-desc">Cuentas y contactos registrados</span>
                                    </div>
                                    <svg class="quick-action-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="14" height="14" aria-hidden="true">
                                        <polyline points="9 18 15 12 9 6"></polyline>
                                    </svg>
                                </a>

                                <!-- Enlace a Citas -->
                                <a href="{{ route('admin.citas') }}" class="quick-action-link">
                                    <div class="quick-action-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                                            <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                                            <line x1="16" y1="2" x2="16" y2="6"></line>
                                            <line x1="8" y1="2" x2="8" y2="6"></line>
                                            <line x1="3" y1="10" x2="21" y2="10"></line>
                                        </svg>
                                    </div>
                                    <div class="quick-action-info">
                                        <h3 class="quick-action-title">Auditoría de Citas</h3>
                                        <span class="quick-action-desc">Consulta de agenda y reservaciones</span>
                                    </div>
                                    <svg class="quick-action-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="14" height="14" aria-hidden="true">
                                        <polyline points="9 18 15 12 9 6"></polyline>
                                    </svg>
                                </a>

                                <!-- Enlace a Configuración / Horarios -->
                                <a href="{{ route('admin.configuracion') }}" class="quick-action-link action-red">
                                    <div class="quick-action-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                                            <circle cx="12" cy="12" r="3"></circle>
                                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                                        </svg>
                                    </div>
                                    <div class="quick-action-info">
                                        <h3 class="quick-action-title">Horarios & Fechas Especiales</h3>
                                        <span class="quick-action-desc">Días festivos, turnos y apertura</span>
                                    </div>
                                    <svg class="quick-action-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="14" height="14" aria-hidden="true">
                                        <polyline points="9 18 15 12 9 6"></polyline>
                                    </svg>
                                </a>

                                <!-- Enlace a Usuarios General -->
                                <a href="{{ route('admin.usuarios') }}" class="quick-action-link">
                                    <div class="quick-action-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="9" cy="7" r="4"></circle>
                                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                        </svg>
                                    </div>
                                    <div class="quick-action-info">
                                        <h3 class="quick-action-title">Gestión de Usuarios</h3>
                                        <span class="quick-action-desc">Roles, contraseñas y permisos</span>
                                    </div>
                                    <svg class="quick-action-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="14" height="14" aria-hidden="true">
                                        <polyline points="9 18 15 12 9 6"></polyline>
                                    </svg>
                                </a>
                            </div>
                        </section>

                        <!-- ESTADO DE LA SUCURSAL -->
                        <section class="dashboard-panel" aria-labelledby="branchStatusTitle">
                            <div class="panel-top-bar">
                                <div class="panel-heading-group">
                                    <h2 class="panel-title" id="branchStatusTitle">
                                        <span class="panel-title-icon" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="18" height="18">
                                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                                            </svg>
                                        </span>
                                        <span>Sucursal Matriz</span>
                                    </h2>
                                    <span class="panel-subtitle">Parámetros operativos actuales</span>
                                </div>
                            </div>

                            <div class="branch-info-card">
                                <div class="branch-info-row">
                                    <span class="branch-info-label">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13" aria-hidden="true">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <polyline points="12 6 12 12 16 14"></polyline>
                                        </svg>
                                        <span>Horario de Atención</span>
                                    </span>
                                    <span class="branch-info-val">Lunes a Domingo</span>
                                </div>
                                <div class="branch-info-row">
                                    <span class="branch-info-label">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13" aria-hidden="true">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <line x1="2" y1="12" x2="22" y2="12"></line>
                                            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                                        </svg>
                                        <span>Zona Horaria</span>
                                    </span>
                                    <span class="branch-info-val">America/Merida</span>
                                </div>
                                <div class="branch-info-row">
                                    <span class="branch-info-label">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13" aria-hidden="true">
                                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                        </svg>
                                        <span>Seguridad & Rol</span>
                                    </span>
                                    <span class="branch-info-val" style="color: var(--barber-blue-light);">Administrador (Rol 1)</span>
                                </div>
                                <div style="margin-top: 6px; padding-top: 10px; border-top: 1px solid var(--border-subtle);">
                                    <a href="{{ route('admin.configuracion') }}" class="kpi-link-btn" style="width: 100%; justify-content: center; gap: 8px;" title="Consultar y modificar horarios laborales">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13" aria-hidden="true">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <polyline points="12 6 12 12 16 14"></polyline>
                                        </svg>
                                        <span>Administrar Horarios Laborales</span>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="12" height="12" aria-hidden="true">
                                            <polyline points="9 18 15 12 9 6"></polyline>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </section>

                    </aside>

                </div>

            </div>
        </main>
    </div>

    <!-- DATOS DE SUBTAREA 5 EN FORMATO JSON SEGURO PARA CHART.JS -->
    <script id="dashboard-chart-data" type="application/json">
    {
        "citas": {
            "completadas": {{ (int) ($citasCompletadas ?? 0) }},
            "canceladas": {{ (int) ($citasCanceladas ?? 0) }},
            "vencidas": {{ (int) ($citasVencidas ?? 0) }}
        },
        "ingresos": {
            "dia": {{ (float) (is_array($ingresosDia) ? ($ingresosDia['total_ingresos'] ?? 0) : ($ingresosDia ?? 0)) }},
            "mes": {{ (float) (is_array($ingresosMes) ? ($ingresosMes['total_ingresos'] ?? 0) : ($ingresosMes ?? 0)) }}
        },
        "servicios": {!! json_encode(isset($serviciosMasSolicitados) ? $serviciosMasSolicitados->map(fn($s) => [
            'nombre' => $s->nombre,
            'categoria' => $s->categoria,
            'total' => (int) $s->total_citas
        ])->values() : []) !!},
        "trabajadores": {!! json_encode(isset($trabajadoresConMasCitas) ? $trabajadoresConMasCitas->map(fn($t) => [
            'nombre' => $t->nombre,
            'funcion' => $t->funcion,
            'total' => (int) $t->total_citas
        ])->values() : []) !!}
    }
    </script>

    <!-- CHART.JS V4 (VERSIÓN ESPECÍFICA Y ESTABLE: 4.4.4) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>

    <!-- INICIALIZACIÓN DE GRÁFICOS DINÁMICOS -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Verificación de disponibilidad de Chart.js (Manejo de caída de CDN / Modo Offline)
            if (typeof Chart === 'undefined') {
                const fallbackAlert = document.getElementById('chartFallbackAlert');
                if (fallbackAlert) {
                    fallbackAlert.style.display = 'flex';
                }
                document.querySelectorAll('.chart-canvas-container').forEach(function (container) {
                    const emptyEl = container.querySelector('.chart-empty-state');
                    if (emptyEl) {
                        emptyEl.style.display = 'flex';
                    }
                    const canvas = container.querySelector('canvas');
                    if (canvas) {
                        canvas.style.display = 'none';
                    }
                });
                return;
            }

            // Lectura segura del payload de datos sin duplicar consultas
            const chartDataEl = document.getElementById('dashboard-chart-data');
            if (!chartDataEl) return;
            
            let data;
            try {
                data = JSON.parse(chartDataEl.textContent);
            } catch (err) {
                console.error('Error al decodificar datos del dashboard:', err);
                return;
            }

            // Configuración estética global para Chart.js en modo oscuro
            Chart.defaults.color = '#94a3b8';
            Chart.defaults.font.family = "'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif";
            Chart.defaults.font.size = 12;

            // ----------------------------------------------------
            // 1. GRÁFICO: ESTADOS DE CITAS (Doughnut)
            // ----------------------------------------------------
            const canvasCitas = document.getElementById('chartCitasEstadoCanvas');
            const emptyCitas = document.getElementById('emptyChartCitasEstado');
            const totalEstados = (data.citas.completadas || 0) + (data.citas.canceladas || 0) + (data.citas.vencidas || 0);

            if (canvasCitas) {
                if (totalEstados === 0) {
                    canvasCitas.style.display = 'none';
                    if (emptyCitas) emptyCitas.style.display = 'flex';
                } else {
                    if (emptyCitas) emptyCitas.style.display = 'none';
                    canvasCitas.style.display = 'block';
                    new Chart(canvasCitas, {
                        type: 'doughnut',
                        data: {
                            labels: ['Completadas', 'Canceladas', 'Vencidas'],
                            datasets: [{
                                data: [data.citas.completadas, data.citas.canceladas, data.citas.vencidas],
                                backgroundColor: [
                                    'rgba(16, 185, 129, 0.85)',
                                    'rgba(239, 68, 68, 0.85)',
                                    'rgba(245, 158, 11, 0.85)'
                                ],
                                borderColor: ['#10b981', '#ef4444', '#f59e0b'],
                                borderWidth: 1.5,
                                hoverOffset: 6
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '68%',
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        boxWidth: 12,
                                        padding: 14,
                                        color: '#f8fafc',
                                        font: { weight: '600' }
                                    }
                                },
                                tooltip: {
                                    backgroundColor: '#0a1124',
                                    titleColor: '#f8fafc',
                                    bodyColor: '#cbd5e1',
                                    borderColor: 'rgba(255, 255, 255, 0.12)',
                                    borderWidth: 1,
                                    padding: 10,
                                    callbacks: {
                                        label: function (ctx) {
                                            const val = ctx.raw || 0;
                                            const pct = totalEstados > 0 ? ((val / totalEstados) * 100).toFixed(1) : 0;
                                            return ` ${ctx.label}: ${val} citas (${pct}%)`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            }

            // ----------------------------------------------------
            // 2. GRÁFICO: COMPARATIVO DE INGRESOS (Barra: Día vs. Mes)
            // ----------------------------------------------------
            const canvasIngresos = document.getElementById('chartIngresosCanvas');
            const emptyIngresos = document.getElementById('emptyChartIngresos');
            const ingresosDia = Number(data.ingresos.dia) || 0;
            const ingresosMes = Number(data.ingresos.mes) || 0;
            const maxIngreso = Math.max(ingresosDia, ingresosMes);

            if (canvasIngresos) {
                if (ingresosDia === 0 && ingresosMes === 0) {
                    canvasIngresos.style.display = 'none';
                    if (emptyIngresos) emptyIngresos.style.display = 'flex';
                } else {
                    if (emptyIngresos) emptyIngresos.style.display = 'none';
                    canvasIngresos.style.display = 'block';
                    new Chart(canvasIngresos, {
                        type: 'bar',
                        data: {
                            labels: ['Ingresos del Día', 'Ingresos del Mes (Acumulado)'],
                            datasets: [{
                                label: 'Importe Cobrado (MXN)',
                                data: [ingresosDia, ingresosMes],
                                backgroundColor: [
                                    'rgba(52, 211, 153, 0.8)',
                                    'rgba(96, 165, 250, 0.8)'
                                ],
                                borderColor: ['#34d399', '#60a5fa'],
                                borderWidth: 1.5,
                                borderRadius: 6,
                                maxBarThickness: 56
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: '#0a1124',
                                    titleColor: '#f8fafc',
                                    bodyColor: '#cbd5e1',
                                    borderColor: 'rgba(255, 255, 255, 0.12)',
                                    borderWidth: 1,
                                    padding: 10,
                                    callbacks: {
                                        label: function (ctx) {
                                            return ` Importe: $${Number(ctx.raw).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} MXN`;
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    max: maxIngreso > 0 ? maxIngreso : undefined,
                                    grid: { color: 'rgba(255, 255, 255, 0.06)' },
                                    ticks: {
                                        color: '#94a3b8',
                                        callback: function (val) {
                                            return '$' + Number(val).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                        }
                                    }
                                },
                                x: {
                                    grid: { display: false },
                                    ticks: { color: '#f8fafc', font: { weight: '600' } }
                                }
                            }
                        }
                    });
                }
            }

            // ----------------------------------------------------
            // 3. GRÁFICO: SERVICIOS MÁS SOLICITADOS (Barras Horizontales)
            // ----------------------------------------------------
            const canvasServicios = document.getElementById('chartServiciosCanvas');
            const emptyServicios = document.getElementById('emptyChartServicios');
            const servicios = Array.isArray(data.servicios) ? data.servicios : [];

            if (canvasServicios) {
                if (servicios.length === 0) {
                    canvasServicios.style.display = 'none';
                    if (emptyServicios) emptyServicios.style.display = 'flex';
                } else {
                    if (emptyServicios) emptyServicios.style.display = 'none';
                    canvasServicios.style.display = 'block';
                    new Chart(canvasServicios, {
                        type: 'bar',
                        data: {
                            labels: servicios.map(s => s.nombre),
                            datasets: [{
                                label: 'Citas Solicitadas',
                                data: servicios.map(s => s.total),
                                backgroundColor: 'rgba(59, 130, 246, 0.8)',
                                borderColor: '#3b82f6',
                                borderWidth: 1.5,
                                borderRadius: 6,
                                maxBarThickness: 24
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: '#0a1124',
                                    titleColor: '#f8fafc',
                                    bodyColor: '#cbd5e1',
                                    borderColor: 'rgba(255, 255, 255, 0.12)',
                                    borderWidth: 1,
                                    padding: 10,
                                    callbacks: {
                                        label: function (ctx) {
                                            return ` Citas: ${ctx.raw} solicitadas`;
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    grid: { color: 'rgba(255, 255, 255, 0.06)' },
                                    ticks: {
                                        precision: 0,
                                        color: '#94a3b8'
                                    }
                                },
                                y: {
                                    grid: { display: false },
                                    ticks: {
                                        color: '#f8fafc',
                                        font: { weight: '500' }
                                    }
                                }
                            }
                        }
                    });
                }
            }

            // ----------------------------------------------------
            // 4. GRÁFICO: PERSONAL CON MAYOR DEMANDA (Barras Horizontales)
            // ----------------------------------------------------
            const canvasTrabajadores = document.getElementById('chartTrabajadoresCanvas');
            const emptyTrabajadores = document.getElementById('emptyChartTrabajadores');
            const trabajadores = Array.isArray(data.trabajadores) ? data.trabajadores : [];

            if (canvasTrabajadores) {
                if (trabajadores.length === 0) {
                    canvasTrabajadores.style.display = 'none';
                    if (emptyTrabajadores) emptyTrabajadores.style.display = 'flex';
                } else {
                    if (emptyTrabajadores) emptyTrabajadores.style.display = 'none';
                    canvasTrabajadores.style.display = 'block';
                    new Chart(canvasTrabajadores, {
                        type: 'bar',
                        data: {
                            labels: trabajadores.map(t => t.nombre),
                            datasets: [{
                                label: 'Citas Asignadas',
                                data: trabajadores.map(t => t.total),
                                backgroundColor: 'rgba(16, 185, 129, 0.8)',
                                borderColor: '#10b981',
                                borderWidth: 1.5,
                                borderRadius: 6,
                                maxBarThickness: 24
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: '#0a1124',
                                    titleColor: '#f8fafc',
                                    bodyColor: '#cbd5e1',
                                    borderColor: 'rgba(255, 255, 255, 0.12)',
                                    borderWidth: 1,
                                    padding: 10,
                                    callbacks: {
                                        afterTitle: function (ctx) {
                                            const idx = ctx[0].dataIndex;
                                            return `Función: ${trabajadores[idx].funcion || 'Especialista'}`;
                                        },
                                        label: function (ctx) {
                                            return ` Citas: ${ctx.raw} asignadas`;
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    grid: { color: 'rgba(255, 255, 255, 0.06)' },
                                    ticks: {
                                        precision: 0,
                                        color: '#94a3b8'
                                    }
                                },
                                y: {
                                    grid: { display: false },
                                    ticks: {
                                        color: '#f8fafc',
                                        font: { weight: '500' }
                                    }
                                }
                            }
                        }
                    });
                }
            }
        });
    </script>

    <!-- SCRIPTS PARA INTERACTIVIDAD ACCESIBLE (SIDEBAR Y PERFIL) -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // --- Menú de Perfil ---
            const dropdownWrapper = document.getElementById('profileDropdownWrapper');
            const profileBtn = document.getElementById('profileBtn');

            if (dropdownWrapper && profileBtn) {
                profileBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    const isOpen = dropdownWrapper.classList.toggle('open');
                    profileBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                });

                document.addEventListener('click', function (e) {
                    if (!dropdownWrapper.contains(e.target)) {
                        dropdownWrapper.classList.remove('open');
                        profileBtn.setAttribute('aria-expanded', 'false');
                    }
                });

                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape' && dropdownWrapper.classList.contains('open')) {
                        dropdownWrapper.classList.remove('open');
                        profileBtn.setAttribute('aria-expanded', 'false');
                        profileBtn.focus();
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
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: '{{ session("success") }}',
                showConfirmButton: false,
                timer: 3500,
                timerProgressBar: true,
                background: '#10b981',
                color: '#ffffff',
                iconColor: '#ffffff',
                customClass: {
                    popup: 'colored-toast'
                }
            });
        });
    </script>
    @endif

    @include('partials.inactivity-timer')
</body>
</html>
