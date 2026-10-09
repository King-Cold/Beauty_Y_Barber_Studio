<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beauty & Barber Studio - Gestión de Trabajadores</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* ===================================================
           SISTEMA DE DISEÑO BASE DEL SISTEMA (IDÉNTICO A DASHBOARD)
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
            
            --border-subtle: rgba(255, 255, 255, 0.06);
            --border-strong: rgba(255, 255, 255, 0.12);

            --barber-red: #ef4444;
            --barber-red-light: rgba(239, 68, 68, 0.1);
            --barber-blue: #0055ff;
            --barber-blue-light: #3377ff; 
            --barber-blue-pale: rgba(0, 85, 255, 0.15);

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
            height: 100vh;
            display: flex;
            flex-direction: column;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            overflow: hidden;
        }

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

        /* HEADER SUPERIOR (ORIGINAL DEL SISTEMA) */
        .admin-header {
            background: var(--bg-header);
            border-bottom: 1px solid var(--border-subtle);
            position: sticky;
            top: 0;
            z-index: 50;
            height: var(--header-height);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        }

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
            border-color: rgba(0, 85, 255, 0.3);
            background: var(--barber-blue-pale);
            transform: scale(1.05);
        }

        .mobile-toggle-btn svg {
            width: 20px;
            height: 20px;
        }

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

        /* SIDEBAR (ORIGINAL DEL SISTEMA CON ANCLAJE STICKY PARA SCROLL) */
        .admin-layout {
            display: flex;
            flex: 1;
            min-height: calc(100vh - var(--header-height));
            position: relative;
        }

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
            overflow-y: auto;
            overflow-x: hidden;
        }

        .admin-sidebar::-webkit-scrollbar {
            width: 4px;
        }

        .admin-sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.12);
            border-radius: 4px;
        }

        .admin-sidebar::-webkit-scrollbar-thumb:hover {
            background: var(--barber-blue);
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            height: 60px;
            border-bottom: 1px solid var(--border-subtle);
            box-sizing: border-box;
            flex-shrink: 0;
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

        .admin-sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        .admin-sidebar.collapsed .sidebar-header {
            justify-content: center;
            padding: 0;
        }

        .admin-sidebar.collapsed .sidebar-title,
        .admin-sidebar.collapsed .nav-label,
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
            padding: 24px 0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

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

        .nav-item.active {
            color: var(--barber-blue-light);
            font-weight: 700;
            background: var(--barber-blue-pale);
            border-left-color: var(--barber-blue);
        }

        .nav-item.active .nav-icon-box {
            color: var(--barber-blue);
            filter: drop-shadow(0 0 8px rgba(0, 85, 255, 0.5));
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

        /* ÁREA PRINCIPAL: LAYOUT FIJO CON SCROLL EXCLUSIVO PARA TRABAJADORES */
        .main-content {
            flex: 1;
            height: calc(100vh - var(--header-height));
            max-height: calc(100vh - var(--header-height));
            display: flex;
            flex-direction: column;
            padding: 20px 32px 0 32px;
            background-color: var(--bg-canvas);
            min-width: 0;
            overflow: hidden;
            box-sizing: border-box;
        }

        /* Panel Fijo Superior (Encabezado, KPIs y Barra de Búsqueda/Filtros) */
        .workers-fixed-header-pane {
            flex-shrink: 0;
            background-color: var(--bg-canvas);
            z-index: 10;
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 14px;
        }

        /* Contenedor con Scroll Exclusivo para la Lista de Trabajadores */
        .workers-scrollable-body {
            flex: 1;
            min-height: 0;
            overflow-y: auto;
            overflow-x: hidden;
            padding-right: 6px;
            padding-bottom: 28px;
            scroll-behavior: smooth;
        }

        .workers-scrollable-body::-webkit-scrollbar {
            width: 6px;
        }

        .workers-scrollable-body::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.02);
            border-radius: 6px;
        }

        .workers-scrollable-body::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 6px;
        }

        .workers-scrollable-body::-webkit-scrollbar-thumb:hover {
            background: var(--barber-blue);
        }

        @media (max-width: 768px) {
            body {
                height: auto;
                min-height: 100vh;
                overflow-y: auto;
            }

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
                padding: 16px 14px;
                height: auto;
                max-height: none;
                overflow: visible;
            }

            .workers-fixed-header-pane {
                position: sticky;
                top: var(--header-height);
                z-index: 20;
                padding-top: 4px;
            }

            .workers-scrollable-body {
                overflow: visible;
                height: auto;
                padding-right: 0;
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
           DISEÑO EXCLUSIVO DEL APARTADO DE TRABAJADORES
           =================================================== */
        .workers-page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 0;
        }

        .workers-title-group h1 {
            font-size: clamp(20px, 1.8vw, 24px);
            font-weight: 800;
            color: var(--text-white);
            letter-spacing: -0.02em;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 2px;
        }

        .workers-title-group p {
            color: var(--text-muted);
            font-size: 13px;
            max-width: 650px;
            margin: 0;
        }

        .btn-register-worker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, var(--barber-blue) 0%, var(--barber-blue-light) 100%);
            color: var(--text-white);
            padding: 9px 18px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(0, 85, 255, 0.35);
            transition: all 0.25s var(--ease-fluid);
            text-decoration: none;
            font-family: inherit;
            flex-shrink: 0;
        }

        .btn-register-worker:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 85, 255, 0.5);
            filter: brightness(1.1);
        }

        .btn-register-worker svg {
            width: 16px;
            height: 16px;
        }

        /* Tarjetas de Estadísticas: Estrictamente en una misma línea y adaptativas */
        .workers-stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: clamp(8px, 1.1vw, 16px);
            margin-bottom: 0;
            width: 100%;
        }

        .workers-stat-card {
            background: var(--bg-header);
            border: 1px solid var(--border-subtle);
            border-radius: 12px;
            padding: clamp(10px, 0.9vw, 14px) clamp(10px, 1.1vw, 16px);
            display: flex;
            align-items: center;
            gap: clamp(8px, 0.9vw, 14px);
            position: relative;
            overflow: hidden;
            min-width: 0;
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .workers-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
            border-color: var(--border-strong);
        }

        .workers-stat-card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: var(--barber-blue);
        }

        .workers-stat-card.card-green::before { background: #10b981; }
        .workers-stat-card.card-red::before { background: var(--barber-red); }
        .workers-stat-card.card-amber::before { background: #f59e0b; }

        .workers-stat-icon {
            width: clamp(34px, 2.6vw, 44px);
            height: clamp(34px, 2.6vw, 44px);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: var(--barber-blue-pale);
            color: var(--barber-blue-light);
            border: 1px solid rgba(0, 85, 255, 0.2);
        }

        .workers-stat-card.card-green .workers-stat-icon {
            background: rgba(16, 185, 129, 0.15);
            color: #10b981;
            border-color: rgba(16, 185, 129, 0.25);
        }

        .workers-stat-card.card-red .workers-stat-icon {
            background: var(--barber-red-light);
            color: var(--barber-red);
            border-color: rgba(239, 68, 68, 0.25);
        }

        .workers-stat-card.card-amber .workers-stat-icon {
            background: rgba(245, 158, 11, 0.15);
            color: #f59e0b;
            border-color: rgba(245, 158, 11, 0.25);
        }

        .workers-stat-icon svg {
            width: clamp(17px, 1.3vw, 22px);
            height: clamp(17px, 1.3vw, 22px);
        }

        .workers-stat-info {
            display: flex;
            flex-direction: column;
            gap: 1px;
            min-width: 0;
            overflow: hidden;
        }

        .workers-stat-label {
            font-size: clamp(9px, 0.7vw, 11px);
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.04em;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .workers-stat-value {
            font-size: clamp(18px, 1.5vw, 24px);
            font-weight: 800;
            color: var(--text-white);
            line-height: 1.1;
        }

        /* Barra de Herramientas: Búsqueda y Filtros Fijos */
        .workers-toolbar {
            background: var(--bg-header);
            border: 1px solid var(--border-subtle);
            border-radius: 12px;
            padding: 10px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 0;
        }

        .workers-search-box {
            position: relative;
            flex: 1;
            min-width: 180px;
            max-width: 420px;
        }

        .workers-search-box svg {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            color: var(--text-muted);
            pointer-events: none;
        }

        .workers-search-input {
            width: 100%;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-subtle);
            border-radius: 8px;
            padding: 8px 12px 8px 36px;
            color: var(--text-white);
            font-family: inherit;
            font-size: 13px;
            transition: all 0.2s ease;
        }

        .workers-search-input:focus {
            outline: none;
            border-color: var(--barber-blue);
            background: rgba(0, 85, 255, 0.04);
            box-shadow: 0 0 0 3px rgba(0, 85, 255, 0.15);
        }

        .workers-filters-group {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
        }

        .btn-filter {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-subtle);
            color: var(--text-muted);
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            font-family: inherit;
            white-space: nowrap;
        }

        .btn-filter:hover {
            color: var(--text-white);
            border-color: var(--border-strong);
        }

        .btn-filter.active {
            background: var(--barber-blue-pale);
            color: var(--barber-blue-light);
            border-color: rgba(0, 85, 255, 0.3);
        }

        .workers-view-toggle {
            display: flex;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-subtle);
            border-radius: 8px;
            padding: 2px;
        }

        .btn-view-mode {
            background: transparent;
            border: none;
            color: var(--text-muted);
            padding: 5px 9px;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .btn-view-mode.active {
            background: var(--barber-blue);
            color: var(--text-white);
        }

        .btn-view-mode svg {
            width: 16px;
            height: 16px;
        }

        /* Cuadrícula de Tarjetas de Trabajadores */
        .workers-cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(330px, 1fr));
            gap: 24px;
        }

        .worker-profile-card {
            background: var(--bg-header);
            border: 1px solid var(--border-subtle);
            border-radius: 14px;
            padding: 24px;
            position: relative;
            transition: all 0.25s var(--ease-fluid);
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .worker-profile-card:hover {
            transform: translateY(-4px);
            border-color: var(--border-strong);
            box-shadow: 0 14px 34px rgba(0, 0, 0, 0.4);
        }

        .card-top-row {
            display: flex;
            align-items: flex-start;
            gap: 16px;
        }

        .worker-avatar-frame {
            position: relative;
            flex-shrink: 0;
        }

        .worker-avatar-box {
            width: 60px;
            height: 60px;
            border-radius: 14px;
            background: #141f38;
            border: 2px solid var(--border-strong);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 20px;
            color: var(--barber-blue-light);
            overflow: hidden;
        }

        .worker-avatar-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .worker-dot-status {
            position: absolute;
            bottom: -2px;
            right: -2px;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: #10b981;
            border: 2px solid var(--bg-header);
        }

        .worker-dot-status.inactive {
            background: #64748b;
        }

        .worker-headline {
            flex: 1;
            min-width: 0;
        }

        .worker-fullname {
            font-size: 17px;
            font-weight: 700;
            color: var(--text-white);
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .worker-specialty-title {
            font-size: 13px;
            color: var(--barber-blue-light);
            font-weight: 600;
            margin-top: 3px;
        }

        .badge-experience {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: rgba(245, 158, 11, 0.12);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.25);
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            margin-top: 6px;
        }

        .badge-worker-status {
            font-size: 11px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .badge-worker-status.active {
            background: rgba(16, 185, 129, 0.15);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .badge-worker-status.inactive {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-muted);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Lista de detalles de contacto */
        .worker-meta-details {
            display: flex;
            flex-direction: column;
            gap: 8px;
            padding: 12px 14px;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.04);
        }

        .meta-detail-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: var(--text-muted);
        }

        .meta-detail-item svg {
            width: 15px;
            height: 15px;
            color: var(--barber-blue-light);
            flex-shrink: 0;
        }

        .meta-detail-item span {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Secciones secundarias: Horario, Servicios, Cuenta */
        .worker-addons {
            display: flex;
            flex-direction: column;
            gap: 10px;
            font-size: 12px;
        }

        .addon-heading {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--text-muted);
            letter-spacing: 0.08em;
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 4px;
        }

        .addon-heading-toggle {
            width: 100%;
            background: transparent;
            border: none;
            padding: 3px 6px;
            margin: -3px -6px 4px -6px;
            border-radius: 6px;
            cursor: pointer;
            text-align: left;
            font-family: inherit;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: color 0.2s ease, background 0.2s ease;
            user-select: none;
        }

        .addon-heading-toggle:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.05);
        }

        .addon-heading-toggle:focus-visible {
            outline: 2px solid var(--barber-blue);
            outline-offset: 1px;
        }

        .addon-heading-title {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .services-toggle-arrow {
            transition: transform 0.25s ease;
            flex-shrink: 0;
            color: var(--text-muted);
        }

        .addon-heading-toggle:hover .services-toggle-arrow {
            color: #ffffff;
        }

        .services-toggle-arrow.expanded,
        .addon-heading-toggle[aria-expanded="true"] .services-toggle-arrow {
            transform: rotate(180deg);
        }

        .worker-services-collapse {
            margin-bottom: 6px;
        }

        .chips-container {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .chip-service {
            background: var(--barber-blue-pale);
            color: #93c5fd;
            border: 1px solid rgba(0, 85, 255, 0.25);
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
        }

        .chip-service.more {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-muted);
            border-color: var(--border-subtle);
        }

        /* Sección: Disponibilidad de Hoy y Alerta en Tarjetas */
        .worker-availability-section {
            margin-top: 4px;
            padding-top: 8px;
            border-top: 1px dashed rgba(255, 255, 255, 0.08);
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .worker-alert-available {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(16, 185, 129, 0.12);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 6px;
            padding: 4px 9px;
            font-size: 11px;
            font-weight: 600;
            margin-bottom: 2px;
            letter-spacing: 0.02em;
            transition: all 0.3s ease;
            width: fit-content;
        }

        .worker-alert-unavailable {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.03);
            color: var(--text-muted);
            border: 1px dashed rgba(255, 255, 255, 0.12);
            border-radius: 6px;
            padding: 4px 9px;
            font-size: 11px;
            font-weight: 500;
            margin-bottom: 2px;
            letter-spacing: 0.02em;
            transition: all 0.3s ease;
            width: fit-content;
        }

        .worker-alert-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 6px rgba(16, 185, 129, 0.8);
            animation: pulse-dot 2s infinite;
            flex-shrink: 0;
        }

        .worker-alert-dot-off {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #64748b;
            flex-shrink: 0;
        }

        @keyframes pulse-dot {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { transform: scale(1.1); box-shadow: 0 0 0 5px rgba(16, 185, 129, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        .alert-flash-animation {
            animation: alertFlash 1.2s ease-out;
        }

        @keyframes alertFlash {
            0% { transform: scale(0.9); opacity: 0; }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); opacity: 1; }
        }

        .card-highlight-pulse {
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.5), 0 10px 28px rgba(16, 185, 129, 0.2) !important;
            border-color: rgba(16, 185, 129, 0.6) !important;
            transition: all 0.4s ease;
        }

        .today-slots-scroll {
            display: flex;
            align-items: center;
            gap: 6px;
            overflow-x: auto;
            padding: 4px 2px 6px 2px;
            min-width: 0;
            width: 100%;
            scrollbar-width: thin;
            scrollbar-color: rgba(0, 85, 255, 0.4) rgba(255, 255, 255, 0.02);
            -webkit-overflow-scrolling: touch;
        }

        .today-slots-scroll::-webkit-scrollbar {
            height: 4px;
        }

        .today-slots-scroll::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.02);
            border-radius: 4px;
        }

        .today-slots-scroll::-webkit-scrollbar-thumb {
            background: rgba(0, 85, 255, 0.35);
            border-radius: 4px;
        }

        .today-slots-scroll::-webkit-scrollbar-thumb:hover {
            background: var(--barber-blue);
        }

        .badge-today-slot {
            display: inline-flex;
            align-items: center;
            background: rgba(0, 85, 255, 0.12);
            color: #93c5fd;
            border: 1px solid rgba(0, 85, 255, 0.28);
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
            flex-shrink: 0;
            transition: all 0.2s ease;
            letter-spacing: 0.02em;
        }

        .badge-today-slot:hover {
            background: rgba(0, 85, 255, 0.24);
            border-color: rgba(0, 85, 255, 0.5);
            color: #ffffff;
            transform: translateY(-1px);
        }

        .empty-today-slots {
            font-size: 11px;
            color: var(--text-muted);
            font-style: italic;
            padding: 4px 8px;
            background: rgba(255, 255, 255, 0.02);
            border: 1px dashed rgba(255, 255, 255, 0.07);
            border-radius: 6px;
            display: inline-block;
        }

        /* Botón de consultar bloqueos debajo del apartado de disponibilidad */
        .btn-view-blocks {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: 100%;
            background: rgba(245, 158, 11, 0.08);
            border: 1px solid rgba(245, 158, 11, 0.25);
            color: #fbbf24;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            font-family: inherit;
            margin-top: 4px;
        }

        .btn-view-blocks:hover {
            background: rgba(245, 158, 11, 0.18);
            border-color: rgba(245, 158, 11, 0.45);
            color: #fef3c7;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.15);
        }

        .btn-view-blocks:active {
            transform: translateY(0);
        }

        .btn-view-blocks svg {
            flex-shrink: 0;
            color: #fbbf24;
        }

        /* Estilos de la vista detallada de bloqueos (Modal) */
        .blocks-detail-summary-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 8px;
        }

        .blocks-detail-worker-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .blocks-detail-avatar {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .blocks-detail-worker-name {
            font-size: 14px;
            font-weight: 700;
            color: #ffffff;
        }

        .blocks-detail-worker-role {
            font-size: 11px;
            color: var(--text-muted);
        }

        .blocks-detail-badge-count {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: rgba(245, 158, 11, 0.12);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.3);
            border-radius: 20px;
            padding: 4px 10px;
            font-size: 11px;
            font-weight: 700;
        }

        .blocks-detail-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
            max-height: 340px;
            overflow-y: auto;
            padding-right: 4px;
        }

        .blocks-detail-card-item {
            background: rgba(10, 17, 36, 0.65);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-left: 3px solid #f59e0b;
            border-radius: 8px;
            padding: 12px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            transition: all 0.2s ease;
        }

        .blocks-detail-card-item:hover {
            background: rgba(20, 31, 56, 0.85);
            border-color: rgba(245, 158, 11, 0.35);
        }

        .blocks-detail-item-left {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
            flex-wrap: wrap;
        }

        .blocks-detail-item-date-badge {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.3);
            border-radius: 6px;
            padding: 4px 8px;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }

        .blocks-detail-item-time {
            font-size: 12px;
            font-weight: 600;
            color: #93c5fd;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .blocks-detail-item-reason {
            font-size: 12px;
            color: var(--text-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 260px;
        }

        .blocks-detail-empty-state {
            text-align: center;
            padding: 36px 20px;
            background: rgba(255, 255, 255, 0.015);
            border: 1px dashed var(--border-subtle);
            border-radius: 10px;
            color: var(--text-muted);
        }

        .blocks-detail-empty-state svg {
            margin-bottom: 10px;
            color: rgba(245, 158, 11, 0.6);
        }



        /* Botones de acción en tarjeta */
        .worker-card-actions {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 8px;
            padding-top: 14px;
            border-top: 1px solid var(--border-subtle);
            margin-top: auto;
        }

        .btn-card-action {
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-subtle);
            color: var(--text-muted);
            height: 36px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-card-action:hover {
            background: var(--barber-blue-pale);
            color: var(--barber-blue-light);
            border-color: rgba(0, 85, 255, 0.3);
            transform: translateY(-2px);
        }

        .btn-card-action svg {
            width: 16px;
            height: 16px;
        }

        .btn-card-action.btn-toggle-active {
            color: #10b981;
        }

        .btn-card-action.btn-toggle-active:hover {
            background: rgba(239, 68, 68, 0.15);
            color: var(--barber-red);
            border-color: rgba(239, 68, 68, 0.35);
        }

        .btn-card-action.btn-toggle-inactive {
            color: #64748b;
        }

        .btn-card-action.btn-toggle-inactive:hover {
            background: rgba(16, 185, 129, 0.15);
            color: #10b981;
            border-color: rgba(16, 185, 129, 0.35);
        }

        .btn-card-action.btn-action-delete:hover {
            background: rgba(239, 68, 68, 0.15);
            color: var(--barber-red);
            border-color: rgba(239, 68, 68, 0.35);
        }

        /* Tabla de Trabajadores */
        .workers-table-wrapper {
            display: none;
            background: var(--bg-header);
            border: 1px solid var(--border-subtle);
            border-radius: 12px;
            overflow-x: auto;
        }

        .workers-custom-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .workers-custom-table th {
            padding: 14px 18px;
            font-size: 12px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid var(--border-subtle);
            background: rgba(255, 255, 255, 0.02);
        }

        .workers-custom-table td {
            padding: 16px 18px;
            font-size: 13px;
            color: var(--text-white);
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            vertical-align: middle;
        }

        .workers-custom-table tr:hover td {
            background: rgba(255, 255, 255, 0.02);
        }

        /* Empty State */
        .workers-empty-box {
            display: none;
            text-align: center;
            padding: 60px 20px;
            background: var(--bg-header);
            border: 1px dashed var(--border-strong);
            border-radius: 14px;
            margin-top: 20px;
        }

        .workers-empty-box svg {
            width: 48px;
            height: 48px;
            color: var(--text-muted);
            margin-bottom: 16px;
        }

        .workers-empty-box h3 {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-white);
            margin-bottom: 6px;
        }

        .workers-empty-box p {
            font-size: 14px;
            color: var(--text-muted);
            margin-bottom: 18px;
        }

        /* ===================================================
           MODALES PARA LAS SUBTAREAS DE TRABAJADORES
           =================================================== */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(5, 10, 21, 0.75);
            backdrop-filter: blur(6px);
            z-index: 100;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            opacity: 0;
            transition: opacity 0.25s ease;
        }

        .modal-overlay.open {
            display: flex;
            opacity: 1;
        }

        .modal-dialog {
            background: var(--bg-header);
            border: 1px solid var(--border-strong);
            border-radius: 14px;
            width: 100%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.6);
            transform: scale(0.95);
            transition: transform 0.25s var(--ease-fluid);
        }

        .modal-overlay.open .modal-dialog {
            transform: scale(1);
        }

        .modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border-subtle);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            background: var(--bg-header);
            z-index: 5;
        }

        .modal-title {
            font-size: 17px;
            font-weight: 700;
            color: var(--text-white);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-title svg {
            color: var(--barber-blue-light);
            width: 20px;
            height: 20px;
        }

        .modal-close-btn {
            background: transparent;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            font-size: 20px;
        }

        .modal-close-btn:hover {
            background: rgba(255, 255, 255, 0.08);
            color: var(--text-white);
        }

        .modal-body {
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .modal-footer {
            padding: 18px 24px;
            border-top: 1px solid var(--border-subtle);
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
            background: rgba(10, 17, 36, 0.9);
            position: sticky;
            bottom: 0;
            z-index: 5;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-white);
        }

        .form-label .required {
            color: var(--barber-red);
        }

        .form-control {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-subtle);
            border-radius: 6px;
            padding: 10px 14px;
            color: var(--text-white);
            font-family: inherit;
            font-size: 14px;
            transition: all 0.2s ease;
            width: 100%;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--barber-blue);
            box-shadow: 0 0 0 3px rgba(0, 85, 255, 0.15);
        }

        /* Campo de contraseña con botón de ojo (Diseño y funcionalidad idénticos a Login) */
        .password-input-group {
            position: relative;
            width: 100%;
            display: flex;
            align-items: center;
        }

        .password-input-group .form-control {
            padding-right: 46px;
        }

        .password-input-group .toggle-password-btn {
            position: absolute;
            right: 10px;
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
            transition: color 0.2s ease, background-color 0.2s ease;
            z-index: 3;
            outline: none;
        }

        .password-input-group .toggle-password-btn:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.08);
        }

        .password-input-group .toggle-password-btn svg {
            width: 20px;
            height: 20px;
        }

        .hidden {
            display: none !important;
        }

        input[type="password"]::-ms-reveal,
        input[type="password"]::-ms-clear {
            display: none;
        }

        /* Selectores y Opciones de Formularios (Alta Visibilidad) */
        select.form-control {
            background-color: #0f172a;
            color: #ffffff;
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            padding-right: 36px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
        }

        select.form-control:focus {
            background-color: #0f172a;
            border-color: var(--barber-blue);
        }

        select.form-control option {
            background-color: #0f172a !important;
            color: #ffffff !important;
            font-size: 14px;
            padding: 10px 14px;
        }

        select.form-control option:checked {
            background-color: #0055ff !important;
            color: #ffffff !important;
        }

        .photo-dropzone {
            border: 2px dashed var(--border-strong);
            border-radius: 10px;
            padding: 18px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            background: rgba(255, 255, 255, 0.01);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        .photo-dropzone:hover {
            border-color: var(--barber-blue);
            background: var(--barber-blue-pale);
        }

        .photo-dropzone img {
            width: 76px;
            height: 76px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--barber-blue);
            display: none;
        }



        .btn-cancel {
            background: transparent;
            border: 1px solid var(--border-subtle);
            color: var(--text-white);
            padding: 9px 18px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
        }

        .btn-cancel:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: var(--border-strong);
        }

        .btn-submit-action {
            background: var(--barber-blue);
            border: none;
            color: var(--text-white);
            padding: 9px 20px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-submit-action:hover {
            background: var(--barber-blue-light);
        }

        /* ===================================================
           ESTILOS DEL MODAL DE CONFIGURACIÓN DE HORARIOS (DARK MODE)
           =================================================== */
        .modal-dialog-schedule {
            max-width: 680px;
            width: 100%;
        }

        .schedule-section-card {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--border-subtle);
            border-radius: 12px;
            padding: 18px 20px;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .schedule-section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .schedule-header-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .schedule-badge-icon {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--barber-blue-pale);
            color: var(--barber-blue-light);
            border: 1px solid rgba(0, 85, 255, 0.25);
            flex-shrink: 0;
        }

        .schedule-badge-icon.icon-amber {
            background: rgba(245, 158, 11, 0.12);
            color: #fbbf24;
            border-color: rgba(245, 158, 11, 0.25);
        }

        .schedule-section-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-white);
            margin-bottom: 2px;
        }

        .schedule-section-desc {
            font-size: 12px;
            color: var(--text-muted);
            margin: 0;
        }

        .schedule-notice-pill {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            background: rgba(255, 255, 255, 0.05);
            color: #94a3b8;
            border: 1px solid var(--border-subtle);
            padding: 4px 10px;
            border-radius: 20px;
            white-space: nowrap;
        }

        /* Filas de Horario Laboral (Lunes a Viernes) */
        .schedule-days-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .schedule-day-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            background: rgba(10, 17, 36, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            padding: 10px 14px;
            transition: all 0.2s ease;
        }

        .schedule-day-row:hover {
            border-color: rgba(0, 85, 255, 0.3);
            background: rgba(14, 23, 48, 0.8);
        }

        .schedule-day-label {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 110px;
        }

        .schedule-status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 6px rgba(16, 185, 129, 0.5);
            flex-shrink: 0;
        }

        .schedule-day-name {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-white);
        }

        .schedule-inputs-pair {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: nowrap;
            flex-shrink: 0;
            justify-content: flex-end;
        }

        .schedule-time-box {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
        }

        .schedule-time-box label {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
            white-space: nowrap;
        }

        .schedule-time-box select.time-select,
        .block-input-group select.time-select {
            background-color: #090f1d;
            border: 1px solid var(--border-subtle);
            border-radius: 6px;
            color: #ffffff;
            font-family: inherit;
            font-size: 13px;
            font-weight: 700;
            padding: 6px 24px 6px 10px;
            width: 82px;
            min-width: 82px;
            max-width: 82px;
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 7px center;
            background-size: 12px;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }

        .schedule-time-box select.time-select:hover,
        .block-input-group select.time-select:hover {
            border-color: rgba(0, 85, 255, 0.4);
            background-color: #0d162a;
        }

        .schedule-time-box select.time-select:focus,
        .block-input-group select.time-select:focus {
            outline: none;
            border-color: var(--barber-blue);
            box-shadow: 0 0 0 2px rgba(0, 85, 255, 0.2);
            background-color: #0d162a;
        }

        .schedule-time-box select.time-select option,
        .block-input-group select.time-select option {
            background-color: #0a1124 !important;
            color: #ffffff !important;
            font-size: 13px;
            padding: 8px 12px;
        }

        .schedule-time-box input[type="time"],
        .block-input-group input[type="time"] {
            background: #090f1d;
            border: 1px solid var(--border-subtle);
            border-radius: 6px;
            color: #ffffff;
            font-family: inherit;
            font-size: 13px;
            font-weight: 600;
            padding: 6px 10px;
            color-scheme: dark;
            width: 96px;
            text-align: center;
            transition: all 0.2s ease;
            -webkit-appearance: none;
            -moz-appearance: textfield;
            appearance: none;
        }

        /* Ocultar el icono del reloj y deshabilitar el pop-up emergente nativo */
        .schedule-time-box input[type="time"]::-webkit-calendar-picker-indicator,
        .block-input-group input[type="time"]::-webkit-calendar-picker-indicator {
            display: none !important;
            -webkit-appearance: none;
            appearance: none;
            background: transparent;
            cursor: pointer;
        }

        .schedule-time-box input[type="time"]::-webkit-inner-spin-button,
        .schedule-time-box input[type="time"]::-webkit-clear-button,
        .block-input-group input[type="time"]::-webkit-inner-spin-button,
        .block-input-group input[type="time"]::-webkit-clear-button {
            display: none !important;
            -webkit-appearance: none;
        }

        .schedule-time-box input[type="time"]:focus,
        .block-input-group input[type="time"]:focus {
            outline: none;
            border-color: var(--barber-blue);
            box-shadow: 0 0 0 2px rgba(0, 85, 255, 0.2);
            background: #0d162a;
        }

        /* Sección B: Bloqueos de Disponibilidad */
        .blocks-inputs-grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr 1fr 1.6fr;
            gap: 10px;
            background: rgba(10, 17, 36, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            padding: 12px;
        }

        .block-input-group {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .block-input-group label {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
        }

        .block-input-group input {
            background: #090f1d;
            border: 1px solid var(--border-subtle);
            border-radius: 6px;
            color: #ffffff;
            font-family: inherit;
            font-size: 12px;
            padding: 7px 10px;
            color-scheme: dark;
            width: 100%;
            transition: all 0.2s ease;
        }

        .special-date-input-wrap {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
        }

        .special-date-input-wrap input[type="text"] {
            padding-right: 32px;
            width: 100%;
        }

        .btn-calendar-trigger {
            position: absolute;
            right: 6px;
            background: transparent;
            border: none;
            color: #ffffff !important;
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 1 !important;
            transition: all 0.2s ease;
        }

        .btn-calendar-trigger svg {
            color: #ffffff !important;
            stroke: #ffffff !important;
            opacity: 1 !important;
        }

        .btn-calendar-trigger:hover {
            opacity: 1 !important;
            transform: scale(1.15);
            color: #ffffff !important;
        }

        .btn-calendar-trigger:hover svg {
            stroke: #ffffff !important;
            filter: drop-shadow(0 0 4px rgba(255, 255, 255, 0.9));
        }

        .block-input-group input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1) brightness(100%) contrast(100%) !important;
            cursor: pointer;
            opacity: 1 !important;
            transition: opacity 0.2s ease, transform 0.2s ease;
            font-size: 15px;
            padding: 2px;
        }

        .block-input-group input[type="date"]::-webkit-calendar-picker-indicator:hover {
            opacity: 1 !important;
            transform: scale(1.15);
            filter: invert(1) brightness(120%) contrast(100%) drop-shadow(0 0 3px rgba(255, 255, 255, 0.8)) !important;
        }

        .block-input-group input:focus {
            outline: none;
            border-color: var(--barber-blue);
            box-shadow: 0 0 0 2px rgba(0, 85, 255, 0.2);
        }

        .btn-add-block-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            background: rgba(0, 85, 255, 0.12);
            border: 1px solid rgba(0, 85, 255, 0.35);
            color: var(--barber-blue-light);
            font-size: 12px;
            font-weight: 700;
            padding: 7px 14px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-add-block-action:hover {
            background: var(--barber-blue);
            color: #ffffff;
            border-color: var(--barber-blue);
            box-shadow: 0 4px 12px rgba(0, 85, 255, 0.3);
            transform: translateY(-1px);
        }

        .blocks-registry-container {
            display: flex;
            flex-direction: column;
            gap: 6px;
            max-height: 160px;
            overflow-y: auto;
            padding-right: 4px;
        }

        .block-row-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(15, 23, 42, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-left: 3px solid #f59e0b;
            border-radius: 6px;
            padding: 8px 12px;
            transition: all 0.2s ease;
        }

        .block-row-item:hover {
            background: rgba(20, 31, 56, 0.85);
            border-color: rgba(255, 255, 255, 0.1);
        }

        .block-row-data {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 12px;
            color: var(--text-white);
            min-width: 0;
            flex-wrap: wrap;
        }

        .block-tag-date {
            font-weight: 700;
            color: #fbbf24;
            white-space: nowrap;
        }

        .block-tag-sep {
            color: rgba(255, 255, 255, 0.2);
        }

        .block-tag-time {
            font-weight: 600;
            color: #93c5fd;
            white-space: nowrap;
        }

        .block-tag-reason {
            color: var(--text-muted);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 250px;
        }

        .btn-remove-block {
            background: transparent;
            border: none;
            color: #ef4444;
            cursor: pointer;
            width: 28px;
            height: 28px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }

        .btn-remove-block:hover {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            transform: scale(1.1);
        }

        .btn-remove-block svg {
            width: 15px;
            height: 15px;
        }

        @media (max-width: 640px) {
            .schedule-day-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
            .schedule-inputs-pair {
                width: 100%;
                justify-content: space-between;
            }
            .blocks-inputs-grid {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Enlace accesible de salto al contenido (Idéntico a Dashboard) -->
    <a href="#main-content" class="skip-link">Saltar al contenido principal</a>

    <!-- HEADER DEL ADMINISTRADOR CON VIBRACIÓN DE MARCA (IDÉNTICO A DASHBOARD) -->
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
                        <div class="user-avatar" aria-hidden="true">AD</div>
                        <div class="user-meta">
                            <span class="user-name">Administrador</span>
                            <span class="user-role">admin@barberstudio.com</span>
                        </div>
                        <svg class="dropdown-arrow" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <!-- Opciones de perfil accesibles con iconos temáticos -->
                    <div class="profile-menu" id="profileMenu" role="menu" aria-labelledby="profileBtn">
                        <a href="#perfil" class="menu-item profile-item" role="menuitem">
                            <div class="menu-item-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </div>
                            <span>Mi Perfil</span>
                        </a>
                        <a href="#configuracion" class="menu-item settings-item" role="menuitem">
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

    <!-- CONTENEDOR ESTRUCTURAL: SIDEBAR + ÁREA DE CONTENIDO (IDÉNTICO A DASHBOARD) -->
    <div class="admin-layout" id="adminLayout">

        <!-- MENÚ LATERAL (SIDEBAR) COLAPSABLE CON COLORES VIBRANTES (IDÉNTICO A DASHBOARD) -->
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
                    <a href="{{ url('/admin') }}" class="nav-item" title="Dashboard">
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
                    <a href="#usuarios" class="nav-item" title="Usuarios">
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

                    <!-- 3. Trabajadores (ACTIVO) -->
                    <a href="{{ url('/admin/trabajadores') }}" class="nav-item active" aria-current="page" title="Trabajadores">
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
                    <a href="#clientes" class="nav-item" title="Clientes">
                        <div class="nav-icon-box">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>
                        <span class="nav-label">Clientes</span>
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

                    <!-- 6. Citas -->
                    <a href="#citas" class="nav-item" title="Citas">
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

        <!-- ÁREA DE CONTENIDO PRINCIPAL: EXCLUSIVO GESTIÓN DE TRABAJADORES -->
        <main id="main-content" class="main-content" role="main">
            
            <!-- PANEL ESTÁTICO SUPERIOR: Header, KPIs en 1 sola línea, y Barra de Búsqueda/Filtros -->
            <div class="workers-fixed-header-pane">
                <!-- Encabezado del Módulo de Trabajadores -->
                <div class="workers-page-header">
                <div class="workers-title-group">
                    <h1>
                        <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--barber-blue);">
                            <rect x="2" y="7" width="20" height="14" rx="2"></rect>
                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                        </svg>
                        Gestión de Trabajadores
                    </h1>
                    <p>Administra al personal del estudio: datos de contacto, años de experiencia, servicios asociados y estado de actividad.</p>
                </div>

                <button type="button" class="btn-register-worker" id="btnOpenCreateModal" onclick="openModal('modalCreateWorker')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    <span>Nuevo Trabajador</span>
                </button>
            </div>

            <!-- Resumen de Métricas / KPIs de Trabajadores -->
            <div class="workers-stats-grid">
                <div class="workers-stat-card">
                    <div class="workers-stat-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <div class="workers-stat-info">
                        <span class="workers-stat-label">Total Equipo</span>
                        <span class="workers-stat-value">{{ $totalEquipo ?? 0 }}</span>
                    </div>
                </div>

                <div class="workers-stat-card card-green">
                    <div class="workers-stat-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                    </div>
                    <div class="workers-stat-info">
                        <span class="workers-stat-label">Activos en Turno</span>
                        <span class="workers-stat-value">{{ $activosCount ?? 0 }}</span>
                    </div>
                </div>

                <div class="workers-stat-card card-red">
                    <div class="workers-stat-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="15" y1="9" x2="9" y2="15"></line>
                            <line x1="9" y1="9" x2="15" y2="15"></line>
                        </svg>
                    </div>
                    <div class="workers-stat-info">
                        <span class="workers-stat-label">Inactivos / Pausa</span>
                        <span class="workers-stat-value">{{ $inactivosCount ?? 0 }}</span>
                    </div>
                </div>

                <div class="workers-stat-card card-amber">
                    <div class="workers-stat-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="6" cy="6" r="3"></circle>
                            <circle cx="6" cy="18" r="3"></circle>
                            <line x1="20" y1="4" x2="8.12" y2="15.88"></line>
                            <line x1="14.47" y1="14.48" x2="20" y2="20"></line>
                            <line x1="8.12" y1="8.12" x2="12" y2="12"></line>
                        </svg>
                    </div>
                    <div class="workers-stat-info">
                        <span class="workers-stat-label">Servicios Habilitados</span>
                        <span class="workers-stat-value" id="kpiServiciosCount">{{ $serviciosCount ?? ($servicios ? $servicios->count() : 0) }}</span>
                    </div>
                </div>
            </div>

            <!-- Barra de Herramientas: Búsqueda y Filtros de Trabajadores -->
            <div class="workers-toolbar">
                <div class="workers-search-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" class="workers-search-input" id="searchWorkerInput" placeholder="Buscar por nombre o email" onkeyup="filterWorkers()">
                </div>

                <div class="workers-filters-group">
                    <button type="button" class="btn-filter active" id="filterAll" onclick="setFilter('all', this)">Todos</button>
                    <button type="button" class="btn-filter" id="filterActive" onclick="setFilter('active', this)">Activos</button>
                    <button type="button" class="btn-filter" id="filterInactive" onclick="setFilter('inactive', this)">Inactivos</button>
                </div>

                    <div style="display: flex; align-items: center; gap: 12px; margin-left: auto;">
                        <span class="workers-count-badge" id="workersCountBadge" style="font-size: 12px; color: var(--text-muted); font-weight: 600; padding: 6px 12px; background: rgba(255,255,255,0.03); border: 1px solid var(--border-subtle); border-radius: 8px; white-space: nowrap;">Mostrando {{ $totalEquipo }} de {{ $totalEquipo }} especialistas</span>

                        <div class="workers-view-toggle">
                            <button type="button" class="btn-view-mode active" id="btnViewGrid" onclick="toggleView('grid')" title="Vista en tarjetas">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="7" height="7"></rect>
                                    <rect x="14" y="3" width="7" height="7"></rect>
                                    <rect x="14" y="14" width="7" height="7"></rect>
                                    <rect x="3" y="14" width="7" height="7"></rect>
                                </svg>
                            </button>
                            <button type="button" class="btn-view-mode" id="btnViewTable" onclick="toggleView('table')" title="Vista en tabla">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="8" y1="6" x2="21" y2="6"></line>
                                    <line x1="8" y1="12" x2="21" y2="12"></line>
                                    <line x1="8" y1="18" x2="21" y2="18"></line>
                                    <line x1="3" y1="6" x2="3.01" y2="6"></line>
                                    <line x1="3" y1="12" x2="3.01" y2="12"></line>
                                    <line x1="3" y1="18" x2="3.01" y2="18"></line>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CUERPO CON SCROLL EXCLUSIVO PARA TRABAJADORES -->
            <div class="workers-scrollable-body" id="workersScrollableBody">
                <!-- Contenedor de Tarjetas de Trabajadores (Dinámico desde Base de Datos) -->
                <div class="workers-cards-grid" id="workersGrid">
                @forelse($trabajadores as $trabajador)
                <article class="worker-profile-card" id="worker-card-{{ $trabajador->id }}" data-status="{{ $trabajador->activo ? 'active' : 'inactive' }}" data-name="{{ $trabajador->nombre_completo }}" data-first-name="{{ mb_strtolower(trim($trabajador->nombre)) }}" data-last-name="{{ mb_strtolower(trim($trabajador->apellidos)) }}" data-full-name="{{ mb_strtolower(trim($trabajador->nombre . ' ' . $trabajador->apellidos)) }}" data-email="{{ $trabajador->email }}" data-phone="{{ $trabajador->telefono }}" data-address="{{ $trabajador->direccion }}">
                    <div class="card-top-row">
                        <div class="worker-avatar-frame">
                            @if($trabajador->fotografia)
                                <img src="{{ asset('storage/' . $trabajador->fotografia) }}" alt="{{ $trabajador->nombre_completo }}" style="width: 58px; height: 58px; border-radius: 12px; object-fit: cover; border: 2px solid var(--barber-blue);">
                            @else
                                <div class="worker-avatar-box">{{ strtoupper(substr($trabajador->nombre, 0, 1) . substr($trabajador->apellidos, 0, 1)) }}</div>
                            @endif
                            <span class="worker-dot-status {{ $trabajador->activo ? '' : 'inactive' }}" title="{{ $trabajador->activo ? 'Especialista Activo' : 'Especialista Inactivo' }}"></span>
                        </div>
                        <div class="worker-headline">
                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                                <h2 class="worker-fullname">{{ $trabajador->nombre_completo }}</h2>
                                <span class="badge-worker-status {{ $trabajador->activo ? 'active' : 'inactive' }}">{{ $trabajador->activo ? 'Activo' : 'Inactivo' }}</span>
                            </div>
                            <div class="badge-experience">
                                <svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                </svg>
                                {{ $trabajador->experiencia }} {{ $trabajador->experiencia == 1 ? 'año' : 'años' }} de experiencia
                            </div>
                        </div>
                    </div>

                    <div class="worker-meta-details">
                        <div class="meta-detail-item" title="Teléfono">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                            </svg>
                            <span>{{ $trabajador->telefono }}</span>
                        </div>
                        <div class="meta-detail-item" title="Correo">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>
                            <span>{{ $trabajador->email }}</span>
                        </div>
                        <div class="meta-detail-item" title="Dirección">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                            <span>{{ $trabajador->direccion }}</span>
                        </div>
                    </div>

                    <div class="worker-addons">
                        <div class="worker-services-accordion" id="accordion-services-{{ $trabajador->id }}">
                            <button type="button" 
                                    class="addon-heading-toggle" 
                                    onclick="toggleWorkerServices({{ $trabajador->id }})" 
                                    aria-expanded="false" 
                                    aria-controls="worker-services-list-{{ $trabajador->id }}"
                                    title="Desplegar u ocultar servicios asignados">
                                <span class="addon-heading-title">
                                    <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="6" cy="6" r="3"></circle>
                                        <circle cx="6" cy="18" r="3"></circle>
                                        <line x1="20" y1="4" x2="8.12" y2="15.88"></line>
                                    </svg>
                                    <span>Servicios asignados ({{ $trabajador->servicios->count() }})</span>
                                </span>
                                <svg class="services-toggle-arrow" id="services-arrow-{{ $trabajador->id }}" viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </button>
                            <div class="chips-container worker-services-collapse" id="worker-services-list-{{ $trabajador->id }}" style="display: none;">
                                @forelse($trabajador->servicios as $servicio)
                                    <span class="chip-service">{{ $servicio->name }}</span>
                                @empty
                                    <span class="chip-service" style="opacity: 0.6; border-style: dashed;">Sin servicios asignados</span>
                                @endforelse
                            </div>
                        </div>

                        <!-- Estado de Disponibilidad Hoy -->
                        <div class="worker-availability-section" id="worker-availability-{{ $trabajador->id }}">
                            <div class="addon-heading">
                                <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                DISPONIBILIDAD DE HOY
                            </div>

                            @php
                                $estadoTexto = $trabajador->getEstadoDisponibilidadHoy();
                            @endphp

                            <div id="worker-status-today-{{ $trabajador->id }}">
                                @if($estadoTexto === 'Con disponibilidad de horario')
                                    <div class="worker-alert-available">
                                        <span class="worker-alert-dot"></span>
                                        <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                        <span>Con disponibilidad de horario</span>
                                    </div>
                                @else
                                    <div class="worker-alert-unavailable">
                                        <span class="worker-alert-dot-off"></span>
                                        <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <line x1="15" y1="9" x2="9" y2="15"></line>
                                            <line x1="9" y1="9" x2="15" y2="15"></line>
                                        </svg>
                                        <span>Sin disponibilidad</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Botón para abrir vista detallada de bloqueos configurados -->
                            <button type="button" class="btn-view-blocks" onclick="openWorkerBlocksDetailModal({{ $trabajador->id }}, '{{ addslashes($trabajador->nombre_completo) }}')">
                                <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line>
                                </svg>
                                <span>Consultar Bloqueos</span>
                            </button>
                        </div>
                    </div>

                    <div class="worker-card-actions">
                        <button type="button" class="btn-card-action" title="Editar Información" onclick="openEditWorker({{ $trabajador->id }}, '{{ addslashes($trabajador->nombre) }}', '{{ addslashes($trabajador->apellidos) }}', '{{ $trabajador->telefono }}', '{{ $trabajador->email }}', '{{ addslashes($trabajador->direccion) }}', {{ $trabajador->experiencia }}, '{{ $trabajador->activo ? 'active' : 'inactive' }}', '{{ $trabajador->fotografia ? asset('storage/' . $trabajador->fotografia) : '' }}')">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </button>
                        <button type="button" class="btn-card-action" title="Asociar Servicios" onclick="openServicesModal({{ $trabajador->id }}, '{{ addslashes($trabajador->nombre_completo) }}', {{ json_encode($trabajador->servicios->pluck('id')) }})">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="6" cy="6" r="3"></circle>
                                <circle cx="6" cy="18" r="3"></circle>
                                <line x1="20" y1="4" x2="8.12" y2="15.88"></line>
                                <line x1="14.47" y1="14.48" x2="20" y2="20"></line>
                                <line x1="8.12" y1="8.12" x2="12" y2="12"></line>
                            </svg>
                        </button>
                        <button type="button" class="btn-card-action btn-action-schedule" title="Configurar Horario" onclick="openScheduleModal({{ $trabajador->id }}, '{{ addslashes($trabajador->nombre_completo) }}')">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </button>
                        <button type="button" class="btn-card-action btn-action-toggle {{ $trabajador->activo ? 'btn-toggle-active' : 'btn-toggle-inactive' }}" title="{{ $trabajador->activo ? 'Desactivar especialista (Bloquear disponibilidad)' : 'Activar especialista (Habilitar disponibilidad)' }}" onclick="toggleWorkerStatus({{ $trabajador->id }}, '{{ addslashes($trabajador->nombre_completo) }}', {{ $trabajador->activo ? 'true' : 'false' }})">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path>
                                <line x1="12" y1="2" x2="12" y2="12"></line>
                            </svg>
                        </button>
                        <button type="button" class="btn-card-action btn-action-delete" title="Eliminar Especialista" onclick="confirmDeleteWorker({{ $trabajador->id }}, '{{ addslashes($trabajador->nombre_completo) }}')">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                <line x1="14" y1="11" x2="14" y2="17"></line>
                            </svg>
                        </button>
                    </div>
                </article>
                @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: 48px; background: rgba(255, 255, 255, 0.02); border: 1px dashed var(--border-subtle); border-radius: 12px; color: var(--text-muted);">
                    <p style="font-size: 15px; margin-bottom: 8px;">No hay trabajadores registrados en la base de datos.</p>
                    <p style="font-size: 13px;">Presiona el botón superior <strong>"Nuevo Trabajador"</strong> para registrar al primero.</p>
                </div>
                @endforelse
            </div>

            <!-- Tabla de Trabajadores (Modo Alterno) -->
            <div class="workers-table-wrapper" id="workersTableContainer">
                <table class="workers-custom-table">
                    <thead>
                        <tr>
                            <th>Especialista</th>
                            <th>Contacto</th>
                            <th>Dirección</th>
                            <th>Experiencia</th>
                            <th>Servicios</th>
                            <th>Estado</th>
                            <th style="text-align: right;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($trabajadores as $trabajador)
                        <tr class="worker-table-row" id="worker-row-{{ $trabajador->id }}" data-status="{{ $trabajador->activo ? 'active' : 'inactive' }}" data-name="{{ $trabajador->nombre_completo }}" data-first-name="{{ mb_strtolower(trim($trabajador->nombre)) }}" data-last-name="{{ mb_strtolower(trim($trabajador->apellidos)) }}" data-full-name="{{ mb_strtolower(trim($trabajador->nombre . ' ' . $trabajador->apellidos)) }}" data-email="{{ $trabajador->email }}" data-phone="{{ $trabajador->telefono }}" data-address="{{ $trabajador->direccion }}">
                            <td>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    @if($trabajador->fotografia)
                                        <img src="{{ asset('storage/' . $trabajador->fotografia) }}" alt="{{ $trabajador->nombre_completo }}" style="width: 38px; height: 38px; border-radius: 8px; object-fit: cover;">
                                    @else
                                        <div style="width: 38px; height: 38px; border-radius: 8px; background: #141f38; display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--barber-blue-light);">
                                            {{ strtoupper(substr($trabajador->nombre, 0, 1) . substr($trabajador->apellidos, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div style="font-weight: 700; color: #fff;">{{ $trabajador->nombre_completo }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>{{ $trabajador->telefono }}</div>
                                <div style="font-size: 11px; color: var(--text-muted);">{{ $trabajador->email }}</div>
                            </td>
                            <td>{{ $trabajador->direccion }}</td>
                            <td><span class="badge-experience">{{ $trabajador->experiencia }} {{ $trabajador->experiencia == 1 ? 'año' : 'años' }}</span></td>
                            <td>
                                @if($trabajador->servicios->count() > 0)
                                    <span class="chip-service" style="display: inline-block;">{{ $trabajador->servicios->count() }} {{ $trabajador->servicios->count() === 1 ? 'servicio' : 'servicios' }}</span>
                                @else
                                    <span class="chip-service" style="opacity: 0.5;">0 servicios</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge-worker-status {{ $trabajador->activo ? 'active' : 'inactive' }}">
                                    {{ $trabajador->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <button type="button" class="btn-card-action" style="display: inline-flex;" title="Editar Información" onclick="openEditWorker({{ $trabajador->id }}, '{{ addslashes($trabajador->nombre) }}', '{{ addslashes($trabajador->apellidos) }}', '{{ $trabajador->telefono }}', '{{ $trabajador->email }}', '{{ addslashes($trabajador->direccion) }}', {{ $trabajador->experiencia }}, '{{ $trabajador->activo ? 'active' : 'inactive' }}', '{{ $trabajador->fotografia ? asset('storage/' . $trabajador->fotografia) : '' }}')">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                </button>
                                <button type="button" class="btn-card-action" style="display: inline-flex; margin-left: 6px;" title="Asociar Servicios" onclick="openServicesModal({{ $trabajador->id }}, '{{ addslashes($trabajador->nombre_completo) }}', {{ json_encode($trabajador->servicios->pluck('id')) }})">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="6" cy="6" r="3"></circle>
                                        <circle cx="6" cy="18" r="3"></circle>
                                        <line x1="20" y1="4" x2="8.12" y2="15.88"></line>
                                        <line x1="14.47" y1="14.48" x2="20" y2="20"></line>
                                        <line x1="8.12" y1="8.12" x2="12" y2="12"></line>
                                    </svg>
                                </button>
                                <button type="button" class="btn-card-action btn-action-schedule" style="display: inline-flex; margin-left: 6px;" title="Configurar Horario" onclick="openScheduleModal({{ $trabajador->id }}, '{{ addslashes($trabajador->nombre_completo) }}')">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12 6 12 12 16 14"></polyline>
                                    </svg>
                                </button>
                                <button type="button" class="btn-card-action btn-action-toggle {{ $trabajador->activo ? 'btn-toggle-active' : 'btn-toggle-inactive' }}" style="display: inline-flex; margin-left: 6px;" title="{{ $trabajador->activo ? 'Desactivar especialista (Bloquear disponibilidad)' : 'Activar especialista (Habilitar disponibilidad)' }}" onclick="toggleWorkerStatus({{ $trabajador->id }}, '{{ addslashes($trabajador->nombre_completo) }}', {{ $trabajador->activo ? 'true' : 'false' }})">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path>
                                        <line x1="12" y1="2" x2="12" y2="12"></line>
                                    </svg>
                                </button>
                                <button type="button" class="btn-card-action btn-action-delete" style="display: inline-flex; margin-left: 6px;" title="Eliminar Especialista" onclick="confirmDeleteWorker({{ $trabajador->id }}, '{{ addslashes($trabajador->nombre_completo) }}')">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                        <line x1="10" y1="11" x2="10" y2="17"></line>
                                        <line x1="14" y1="11" x2="14" y2="17"></line>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 24px; color: var(--text-muted);">
                                No hay trabajadores registrados en la base de datos.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

                <!-- Empty State -->
                <div class="workers-empty-box" id="emptyStateContainer">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <h3>No se encontraron especialistas</h3>
                    <p>No hay trabajadores que coincidan con la búsqueda o filtro aplicado.</p>
                    <button type="button" class="btn-cancel" onclick="resetFilters()">Restablecer filtros</button>
                </div>
            </div>
        </main>
    </div>

    <!-- MODAL 1: REGISTRO DE TRABAJADOR (SUBTAREA 3) -->
    <div class="modal-overlay" id="modalCreateWorker" role="dialog" aria-modal="true" aria-labelledby="createModalTitle">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 class="modal-title" id="createModalTitle">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <line x1="19" y1="8" x2="19" y2="14"></line>
                        <line x1="22" y1="11" x2="16" y2="11"></line>
                    </svg>
                    Registrar Nuevo Trabajador
                </h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalCreateWorker')" aria-label="Cerrar modal">&times;</button>
            </div>

            <form id="formCreateWorker" action="{{ route('admin.trabajadores.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <!-- Fotografía -->
                    <div class="form-group">
                        <label class="form-label">Fotografía</label>
                        <div class="photo-dropzone" onclick="document.getElementById('workerPhotoInput').click()">
                            <img id="workerPhotoPreview" alt="Vista previa de foto">
                            <div id="photoPlaceholder">
                                <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--barber-blue-light);">
                                    <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                                    <circle cx="12" cy="13" r="4"></circle>
                                </svg>
                                <div style="font-size: 13px; color: var(--text-white); margin-top: 4px;">Seleccionar imagen</div>
                                <div style="font-size: 11px; color: var(--text-muted);">JPG, PNG o WEBP</div>
                            </div>
                            <input type="file" id="workerPhotoInput" name="fotografia" accept="image/*" style="display: none;" onchange="previewImage(this, 'workerPhotoPreview', 'photoPlaceholder')">
                        </div>
                    </div>

                    <!-- Nombre y Apellidos -->
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="workerName">Nombre(s) <span class="required">*</span></label>
                            <input 
                                type="text" 
                                id="workerName" 
                                name="nombre" 
                                class="form-control" 
                                placeholder="Ej. Carlos" 
                                pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+" 
                                onkeypress="return /^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]$/.test(event.key)" 
                                oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]/g, '');" 
                                title="El campo Nombre únicamente debe contener letras del abecedario" 
                                required
                            >
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="workerLastName">Apellidos <span class="required">*</span></label>
                            <input 
                                type="text" 
                                id="workerLastName" 
                                name="apellidos" 
                                class="form-control" 
                                placeholder="Ej. Gómez Ruiz" 
                                pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+" 
                                onkeypress="return /^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]$/.test(event.key)" 
                                oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]/g, '');" 
                                title="El campo Apellidos únicamente debe contener letras del abecedario" 
                                required
                            >
                            <span id="workerFullNameHelp" style="font-size: 11px; color: var(--text-muted); margin-top: 3px; display: block;">El nombre completo (nombre y apellidos) debe ser único en el sistema.</span>
                        </div>
                    </div>

                    <!-- Teléfono y Correo Electrónico -->
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="workerPhone">Teléfono de Contacto (10 dígitos) <span class="required">*</span></label>
                            <input 
                                type="tel" 
                                id="workerPhone" 
                                name="telefono" 
                                class="form-control" 
                                placeholder="Ej. 5551234567 (10 dígitos)" 
                                pattern="[0-9]{10}" 
                                maxlength="10" 
                                minlength="10" 
                                inputmode="numeric" 
                                onkeypress="return (event.charCode >= 48 && event.charCode <= 57)" 
                                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10); validatePhoneInput(this);"
                                title="El teléfono debe tener exactamente 10 dígitos numéricos" 
                                required
                            >
                            <span id="workerPhoneHelp" style="font-size: 11px; color: var(--text-muted); margin-top: 3px; display: block;">Exactamente 10 dígitos numéricos</span>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="workerEmail">Correo Electrónico <span class="required">*</span></label>
                            <input 
                                type="email" 
                                id="workerEmail" 
                                name="email" 
                                class="form-control" 
                                placeholder="usuario@dominio.com" 
                                pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}" 
                                oninput="validateEmailInput(this);"
                                title="Introduce un correo electrónico válido (ej. usuario@dominio.com)" 
                                required
                            >
                            <span id="workerEmailHelp" style="font-size: 11px; color: var(--text-muted); margin-top: 3px; display: block;">Formato válido: usuario@dominio.com</span>
                        </div>
                    </div>

                    <!-- Contraseña -->
                    <div class="form-row">
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label class="form-label" for="workerPassword">Contraseña <span class="required">*</span></label>
                            <div class="password-input-group">
                                <input 
                                    type="password" 
                                    id="workerPassword" 
                                    name="password" 
                                    class="form-control" 
                                    placeholder="Al menos 8 caracteres, 1 mayúscula, 1 número y un carácter especial" 
                                    minlength="8"
                                    maxlength="50"
                                    required
                                >
                                <button 
                                    type="button" 
                                    id="toggleCreatePassword" 
                                    class="toggle-password-btn" 
                                    aria-label="Mostrar u ocultar contraseña"
                                    title="Mostrar u ocultar contraseña"
                                >
                                    <svg class="eye-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    <svg class="eye-off-icon hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                        <line x1="1" y1="1" x2="23" y2="23"></line>
                                    </svg>
                                </button>
                            </div>
                            <span style="font-size: 11px; color: var(--text-muted); margin-top: 3px; display: block;">Mínimo 8 caracteres, al menos 1 letra mayúscula, 1 número y un carácter especial</span>
                        </div>
                    </div>

                    <!-- Dirección -->
                    <div class="form-group">
                        <label class="form-label" for="workerAddress">Dirección Domiciliaria <span class="required">*</span></label>
                        <input type="text" id="workerAddress" name="direccion" class="form-control" placeholder="Ej. Av. Universidad 1200, Col. del Valle" required>
                    </div>

                    <!-- Años de Experiencia (Solo números enteros positivos >= 0) -->
                    <div class="form-group">
                        <label class="form-label" for="workerExperience">Años de Experiencia <span class="required">*</span></label>
                        <input 
                            type="number" 
                            id="workerExperience" 
                            name="experiencia" 
                            min="0" 
                            max="50" 
                            class="form-control" 
                            placeholder="Ej. 5 (solo números positivos)" 
                            onkeypress="return (event.charCode >= 48 && event.charCode <= 57)" 
                            oninput="if(this.value < 0 || this.value.includes('-')) this.value = Math.max(0, parseInt(this.value) || 0);" 
                            title="La experiencia no puede ser negativa" 
                            required
                        >
                        <span style="font-size: 11px; color: var(--text-muted); margin-top: 3px; display: block;">Número entero mayor o igual a 0 (máx. 50 años). No se permiten números negativos.</span>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal('modalCreateWorker')">Cancelar</button>
                    <button type="submit" class="btn-submit-action">Guardar Trabajador</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: MODIFICACIÓN DE TRABAJADOR (SUBTAREA 5) -->
    <div class="modal-overlay" id="modalEditWorker" role="dialog" aria-modal="true" aria-labelledby="editModalTitle">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 class="modal-title" id="editModalTitle">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                    Modificar Información del Trabajador
                </h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalEditWorker')" aria-label="Cerrar modal">&times;</button>
            </div>

            <form id="formEditWorker" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="editWorkerId" name="worker_id">
                <div class="modal-body">
                    <!-- Fotografía (opcional para actualizar) -->
                    <div class="form-group">
                        <label class="form-label">Fotografía (opcional para actualizar)</label>
                        <div class="photo-dropzone" onclick="document.getElementById('editWorkerPhotoInput').click()">
                            <img id="editWorkerPhotoPreview" alt="Vista previa de foto" style="display: none;">
                            <div id="editPhotoPlaceholder">
                                <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--barber-blue-light);">
                                    <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                                    <circle cx="12" cy="13" r="4"></circle>
                                </svg>
                                <div style="font-size: 13px; color: var(--text-white); margin-top: 4px;">Cambiar imagen</div>
                                <div style="font-size: 11px; color: var(--text-muted);">JPG, PNG o WEBP (máx. 2 MB)</div>
                            </div>
                            <input type="file" id="editWorkerPhotoInput" name="fotografia" accept="image/*" style="display: none;" onchange="previewImage(this, 'editWorkerPhotoPreview', 'editPhotoPlaceholder')">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="editWorkerName">Nombre(s) <span class="required">*</span></label>
                            <input 
                                type="text" 
                                id="editWorkerName" 
                                name="nombre" 
                                class="form-control" 
                                placeholder="Ej. Carlos" 
                                pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+" 
                                onkeypress="return /^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]$/.test(event.key)" 
                                oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]/g, '');" 
                                title="El campo Nombre únicamente debe contener letras del abecedario" 
                                required
                            >
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="editWorkerLastName">Apellidos <span class="required">*</span></label>
                            <input 
                                type="text" 
                                id="editWorkerLastName" 
                                name="apellidos" 
                                class="form-control" 
                                placeholder="Ej. Gómez Ruiz" 
                                pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+" 
                                onkeypress="return /^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]$/.test(event.key)" 
                                oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]/g, '');" 
                                title="El campo Apellidos únicamente debe contener letras del abecedario" 
                                required
                            >
                            <span id="editWorkerFullNameHelp" style="font-size: 11px; color: var(--text-muted); margin-top: 3px; display: block;">El nombre completo (nombre y apellidos) debe ser único en el sistema.</span>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="editWorkerPhone">Teléfono de Contacto (10 dígitos) <span class="required">*</span></label>
                            <input 
                                type="tel" 
                                id="editWorkerPhone" 
                                name="telefono" 
                                class="form-control" 
                                placeholder="Ej. 5551234567 (10 dígitos)" 
                                pattern="[0-9]{10}" 
                                maxlength="10" 
                                minlength="10" 
                                inputmode="numeric" 
                                onkeypress="return (event.charCode >= 48 && event.charCode <= 57)" 
                                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10); validatePhoneInput(this);" 
                                title="El teléfono debe tener exactamente 10 dígitos numéricos" 
                                required
                            >
                            <span id="editWorkerPhoneHelp" style="font-size: 11px; color: var(--text-muted); margin-top: 3px; display: block;">Exactamente 10 dígitos numéricos</span>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="editWorkerEmail">Correo Electrónico <span class="required">*</span></label>
                            <input 
                                type="email" 
                                id="editWorkerEmail" 
                                name="email" 
                                class="form-control" 
                                placeholder="usuario@dominio.com" 
                                pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}" 
                                oninput="validateEmailInput(this);" 
                                title="Introduce un correo electrónico válido (ej. usuario@dominio.com)" 
                                required
                            >
                            <span id="editWorkerEmailHelp" style="font-size: 11px; color: var(--text-muted); margin-top: 3px; display: block;">Formato válido: usuario@dominio.com</span>
                        </div>
                    </div>

                    <!-- Contraseña (Edición Opcional) -->
                    <div class="form-row">
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label class="form-label" for="editWorkerPassword">
                                Contraseña
                            </label>
                            <div class="password-input-group">
                                <input 
                                    type="password" 
                                    id="editWorkerPassword" 
                                    name="password" 
                                    class="form-control" 
                                    placeholder="Nueva contraseña (dejar vacío si no deseas modificarla)" 
                                    minlength="8"
                                    maxlength="50"
                                    autocomplete="new-password"
                                >
                                <button 
                                    type="button" 
                                    id="toggleEditPassword" 
                                    class="toggle-password-btn" 
                                    aria-label="Mostrar u ocultar contraseña"
                                    title="Mostrar u ocultar contraseña"
                                >
                                    <svg class="eye-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    <svg class="eye-off-icon hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                        <line x1="1" y1="1" x2="23" y2="23"></line>
                                    </svg>
                                </button>
                            </div>
                            <span id="editWorkerPasswordHelp" style="font-size: 11px; color: var(--text-muted); margin-top: 3px; display: block;">Solo llenar si deseas cambiar la contraseña (mínimo 8 caracteres, 1 mayúscula, 1 número y 1 carácter especial)</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="editWorkerAddress">Dirección Domiciliaria <span class="required">*</span></label>
                        <input type="text" id="editWorkerAddress" name="direccion" class="form-control" placeholder="Ej. Av. Universidad 1200, Col. del Valle" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="editWorkerExperience">Años de Experiencia <span class="required">*</span></label>
                            <input 
                                type="number" 
                                id="editWorkerExperience" 
                                name="experiencia" 
                                min="0" 
                                max="50" 
                                class="form-control" 
                                placeholder="Ej. 5" 
                                onkeypress="return (event.charCode >= 48 && event.charCode <= 57)" 
                                oninput="if(this.value < 0 || this.value.includes('-')) this.value = Math.max(0, parseInt(this.value) || 0);" 
                                title="La experiencia no puede ser negativa" 
                                required
                            >
                            <span style="font-size: 11px; color: var(--text-muted); margin-top: 3px; display: block;">Número entero mayor o igual a 0 (máx. 50 años). No se permiten números negativos.</span>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="editWorkerStatus">Estado del Especialista</label>
                            <select id="editWorkerStatus" name="status" class="form-control" style="background-color: #0f172a; color: #ffffff;">
                                <option value="active" style="background-color: #0f172a; color: #ffffff;">Activo</option>
                                <option value="inactive" style="background-color: #0f172a; color: #ffffff;">Inactivo</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal('modalEditWorker')">Cancelar</button>
                    <button type="submit" class="btn-submit-action">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 3: ASOCIAR SERVICIOS (SUBTAREA 7) -->
    <div class="modal-overlay" id="modalServices" role="dialog" aria-modal="true" aria-labelledby="servicesModalTitle">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 class="modal-title" id="servicesModalTitle">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="6" cy="6" r="3"></circle>
                        <circle cx="6" cy="18" r="3"></circle>
                        <line x1="20" y1="4" x2="8.12" y2="15.88"></line>
                        <line x1="14.47" y1="14.48" x2="20" y2="20"></line>
                        <line x1="8.12" y1="8.12" x2="12" y2="12"></line>
                    </svg>
                    Servicios Asignados a: <span id="servicesWorkerName" style="color: var(--barber-blue-light); margin-left: 4px;"></span>
                </h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalServices')">&times;</button>
            </div>

            <form id="formServicesWorker" method="POST">
                @csrf
                <input type="hidden" id="servicesWorkerId" name="worker_id">
                <div class="modal-body">
                    <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 12px;">Marca o desmarca los servicios del catálogo que este especialista realiza:</p>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; max-height: 320px; overflow-y: auto; padding-right: 4px;">
                        @forelse($servicios as $servicio)
                        <label style="background: rgba(255,255,255,0.02); border: 1px solid var(--border-subtle); padding: 12px; border-radius: 8px; display: flex; gap: 10px; cursor: pointer; transition: all 0.2s ease;">
                            <input type="checkbox" name="servicios[]" value="{{ $servicio->id }}" class="service-checkbox" style="margin-top: 3px; accent-color: var(--barber-blue); cursor: pointer; width: 16px; height: 16px;">
                            <div style="min-width: 0; flex: 1;">
                                <div style="font-size: 13px; font-weight: 700; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $servicio->name }}</div>
                                <div style="font-size: 11px; color: var(--text-muted);">{{ $servicio->duration_minutes }} min • ${{ number_format($servicio->price, 0) }} MXN</div>
                                <div style="font-size: 10px; color: var(--barber-blue-light); text-transform: uppercase; font-weight: 700; margin-top: 2px;">{{ ucfirst($servicio->category) }}</div>
                            </div>
                        </label>
                        @empty
                        <div style="grid-column: 1 / -1; text-align: center; padding: 24px; color: var(--text-muted);">
                            No hay servicios activos registrados en el catálogo.
                        </div>
                        @endforelse
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal('modalServices')">Cancelar</button>
                    <button type="submit" class="btn-submit-action">Guardar Servicios</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 4: CONFIGURACIÓN DE HORARIOS (FRONTEND) -->
    <div class="modal-overlay" id="modalSchedule" role="dialog" aria-modal="true" aria-labelledby="scheduleModalTitle">
        <div class="modal-dialog modal-dialog-schedule">
            <div class="modal-header">
                <h3 class="modal-title" id="scheduleModalTitle">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    <span>Configuración de Horario - <span id="scheduleWorkerName" style="color: var(--barber-blue-light);">[Nombre del Trabajador]</span></span>
                </h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalSchedule')" aria-label="Cerrar modal">&times;</button>
            </div>

            <form id="formWorkerSchedule" onsubmit="saveWorkerSchedule(event)">
                <input type="hidden" id="scheduleWorkerId" name="worker_id">

                <div class="modal-body" style="gap: 20px;">
                    <!-- SECCIÓN A: Horario Laboral (Lunes a Viernes) -->
                    <div class="schedule-section-card">
                        <div class="schedule-section-header">
                            <div class="schedule-header-left">
                                <div class="schedule-badge-icon">
                                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="schedule-section-title">SECCIÓN A: Horario Laboral</h4>
                                    <p class="schedule-section-desc">Jornada habitual de atención (Lunes a Viernes)</p>
                                </div>
                            </div>
                            <span class="schedule-notice-pill">Lunes a Viernes</span>
                        </div>

                        <div class="schedule-days-list">
                            @php
                                $workerDays = [
                                    ['id' => 'lunes', 'name' => 'Lunes', 'in' => '09:00', 'out' => '19:00'],
                                    ['id' => 'martes', 'name' => 'Martes', 'in' => '09:00', 'out' => '19:00'],
                                    ['id' => 'miercoles', 'name' => 'Miércoles', 'in' => '09:00', 'out' => '19:00'],
                                    ['id' => 'jueves', 'name' => 'Jueves', 'in' => '09:00', 'out' => '19:00'],
                                    ['id' => 'viernes', 'name' => 'Viernes', 'in' => '09:00', 'out' => '19:00'],
                                ];

                                $workerTimeSlots = [];
                                // Formato 24 horas continuo: desde 00:00 / 01:00 hasta las 24:00
                                $workerTimeSlots[] = '00:00';
                                $workerTimeSlots[] = '00:30';
                                for ($wh = 1; $wh <= 23; $wh++) {
                                    $workerTimeSlots[] = sprintf('%02d:00', $wh);
                                    $workerTimeSlots[] = sprintf('%02d:30', $wh);
                                }
                                $workerTimeSlots[] = '24:00';
                            @endphp

                            @php
                                $branchLimitsMap = $branchLimits ?? \App\Models\HorarioSucursal::getHorariosMap();
                            @endphp

                            @foreach($workerDays as $wDay)
                                @php
                                    $bDay = $branchLimitsMap[$wDay['id']] ?? null;
                                    $bOpen = $bDay ? (bool)$bDay['abierto'] : true;
                                    $bStart = $bDay['apertura'] ?? '09:00';
                                    $bEnd = $bDay['cierre'] ?? '20:00';
                                @endphp
                                <div class="schedule-day-row {{ $bOpen ? '' : 'day-closed' }}" id="sched-row-{{ $wDay['id'] }}">
                                    <div class="schedule-day-label">
                                        <span class="schedule-status-dot" id="sched-dot-{{ $wDay['id'] }}" style="{{ $bOpen ? '' : 'background: #64748b; box-shadow: none;' }}"></span>
                                        <span class="schedule-day-name">{{ $wDay['name'] }}</span>
                                    </div>
                                    <div class="schedule-inputs-column" style="display: flex; flex-direction: column; align-items: flex-end; gap: 4px;">
                                        <div class="schedule-inputs-pair">
                                            <div class="schedule-time-box">
                                                <label for="sched-{{ $wDay['id'] }}-in">Desde</label>
                                                <select id="sched-{{ $wDay['id'] }}-in" name="horario[{{ $wDay['id'] }}][entrada]" class="time-select" required {{ $bOpen ? '' : 'disabled' }}>
                                                    @foreach($workerTimeSlots as $wSlot)
                                                        @if($wSlot >= $bStart && $wSlot < $bEnd)
                                                            <option value="{{ $wSlot }}" {{ $wSlot === ($wDay['in'] < $bStart ? $bStart : $wDay['in']) ? 'selected' : '' }}>{{ $wSlot }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <span style="color: var(--text-muted); font-size: 13px;">—</span>
                                            <div class="schedule-time-box">
                                                <label for="sched-{{ $wDay['id'] }}-out">Hasta</label>
                                                <select id="sched-{{ $wDay['id'] }}-out" name="horario[{{ $wDay['id'] }}][salida]" class="time-select" required {{ $bOpen ? '' : 'disabled' }}>
                                                    @foreach($workerTimeSlots as $wSlot)
                                                        @if($wSlot > $bStart && $wSlot <= $bEnd)
                                                            <option value="{{ $wSlot }}" {{ $wSlot === ($wDay['out'] > $bEnd ? $bEnd : $wDay['out']) ? 'selected' : '' }}>{{ $wSlot }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <!-- Texto de ayuda visual debajo de los campos de hora que indique los límites -->
                                        <div class="branch-limit-help-text" id="sched-hint-{{ $wDay['id'] }}" style="font-size: 11px; color: var(--text-muted); font-weight: 500;">
                                            @if($bOpen)
                                                (Límite sucursal: {{ $bStart }} a {{ $bEnd }})
                                            @else
                                                (Sucursal cerrada este día)
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- SECCIÓN B: Bloqueos de Disponibilidad (Pausas y Permisos) -->
                    <div class="schedule-section-card">
                        <div class="schedule-section-header">
                            <div class="schedule-header-left">
                                <div class="schedule-badge-icon icon-amber">
                                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="schedule-section-title">SECCIÓN B: Bloqueos de Disponibilidad</h4>
                                    <p class="schedule-section-desc">Pausas programadas y permisos específicos en la agenda</p>
                                </div>
                            </div>
                        </div>

                        <!-- Formulario para registrar nuevo bloqueo -->
                        <div class="blocks-inputs-grid">
                            <div class="block-input-group">
                                <label for="newBlockDate">Fecha</label>
                                <div class="special-date-input-wrap">
                                    <input type="text" id="newBlockDate" placeholder="DD/MM/AAAA" maxlength="10" autocomplete="off" oninput="handleBlockDateInput(this)" onblur="validateAndFormatBlockDate(this)">
                                    <input type="date" id="newBlockDateNativePicker" style="position: absolute; opacity: 0; pointer-events: none; width: 0; height: 0;" min="{{ date('Y-m-d') }}" max="{{ date('Y') }}-12-31" onchange="syncBlockNativeDate(this)" tabindex="-1">
                                    <button type="button" class="btn-calendar-trigger" onclick="openWorkerBlockDatePicker('newBlockDateNativePicker')" title="Abrir calendario" aria-label="Abrir calendario">
                                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="16" y1="2" x2="16" y2="6"></line>
                                            <line x1="8" y1="2" x2="8" y2="6"></line>
                                            <line x1="3" y1="10" x2="21" y2="10"></line>
                                        </svg>
                                    </button>
                                </div>
                                <span class="special-date-help-text" style="font-size: 11px; color: var(--text-muted); margin-top: 3px; display: block;">Orden de captura manual: día, mes y año</span>
                            </div>
                            <div class="block-input-group">
                                <label for="newBlockStartTime">Hora inicio</label>
                                <select id="newBlockStartTime" class="time-select">
                                    <option value="">Seleccionar...</option>
                                    @foreach($workerTimeSlots as $wSlot)
                                        <option value="{{ $wSlot }}">{{ $wSlot }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="block-input-group">
                                <label for="newBlockEndTime">Hora fin</label>
                                <select id="newBlockEndTime" class="time-select">
                                    <option value="">Seleccionar...</option>
                                    @foreach($workerTimeSlots as $wSlot)
                                        <option value="{{ $wSlot }}">{{ $wSlot }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="block-input-group">
                                <label for="newBlockReason">Motivo</label>
                                <input type="text" id="newBlockReason" placeholder="Ej. Comida, cita médica...">
                            </div>
                        </div>

                        <!-- Texto informativo sobre los límites laborales y operativos -->
                        <div id="newBlockRangeHint" style="font-size: 11px; color: var(--text-muted); margin-top: 6px; margin-bottom: 4px; display: none; font-weight: 500;">
                            <span id="newBlockRangeHintText"></span>
                        </div>

                        <div style="display: flex; justify-content: flex-end; margin-top: 8px;">
                            <button type="button" class="btn-add-block-action" onclick="addAvailabilityBlock()">
                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                                <span>+ Agregar Bloqueo</span>
                            </button>
                        </div>

                        <!-- Lista de Bloqueos Registrados -->
                        <div>
                            <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 8px;">
                                Bloqueos Registrados
                            </div>
                            <div class="blocks-registry-container" id="blocksListContainer"></div>
                            <div id="blocksEmptyMsg" style="display: block; text-align: center; padding: 16px; font-size: 12px; color: var(--text-muted); background: rgba(255,255,255,0.01); border: 1px dashed var(--border-subtle); border-radius: 8px;">
                                No hay bloqueos registrados para este especialista.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer del Modal: Botones Cancelar y Guardar Horario -->
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal('modalSchedule')">Cancelar</button>
                    <button type="submit" class="btn-submit-action">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                            <polyline points="17 21 17 13 7 13 7 21"></polyline>
                            <polyline points="7 3 7 8 15 8"></polyline>
                        </svg>
                        <span>Guardar Horario</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- =========================================================================
         MODAL: VISTA DETALLADA DE BLOQUEOS CONFIGURADOS DEL TRABAJADOR
         ========================================================================= -->
    <div class="modal-overlay" id="modalWorkerBlocksDetail">
        <div class="modal-container" style="max-width: 620px;">
            <div class="modal-header">
                <div class="modal-title">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line>
                    </svg>
                    <span>Bloqueos de Disponibilidad</span>
                </div>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalWorkerBlocksDetail')" title="Cerrar modal">&times;</button>
            </div>

            <div class="modal-body">
                <!-- Tarjeta resumen del especialista -->
                <div class="blocks-detail-summary-card">
                    <div class="blocks-detail-worker-info">
                        <div class="blocks-detail-avatar" id="detailModalAvatar">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>
                        <div>
                            <div class="blocks-detail-worker-name" id="detailModalWorkerName">Nombre del Especialista</div>
                            <div class="blocks-detail-worker-role">Especialista de Barbería / Estética</div>
                        </div>
                    </div>
                    <div class="blocks-detail-badge-count" id="detailModalBadgeCount">
                        <span id="detailModalCountNumber">0</span> Bloqueos
                    </div>
                </div>

                <!-- Lista detallada de bloqueos configurados -->
                <div>
                    <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 10px; display: flex; align-items: center; justify-content: space-between;">
                        <span>Historial de Bloqueos Programados</span>
                        <span style="font-size: 11px; color: var(--text-muted); font-weight: 500;" id="detailModalDateHint">Pausas y permisos de agenda</span>
                    </div>

                    <div class="blocks-detail-list" id="detailModalBlocksList">
                        <!-- Render dinámico vía JS -->
                    </div>

                    <div class="blocks-detail-empty-state" id="detailModalEmptyState" style="display: none;">
                        <svg viewBox="0 0 24 24" width="36" height="36" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 14 14"></polyline>
                        </svg>
                        <p style="font-size: 13px; font-weight: 600; color: var(--text-white); margin-bottom: 4px;">Sin bloqueos registrados</p>
                        <p style="font-size: 12px; margin: 0;">Este especialista cuenta con disponibilidad completa sin pausas ni permisos configurados.</p>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('modalWorkerBlocksDetail')">Cerrar</button>
                <button type="button" class="btn-submit-action" id="btnDetailManageSchedule" onclick="handleDetailManageSchedule()">
                    <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    <span>Configurar / Editar Horario</span>
                </button>
            </div>
        </div>
    </div>



    <!-- SCRIPTS DE INTERACCIÓN (IDÉNTICOS A DASHBOARD + MODALES DE TRABAJADORES) -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Dropdown de perfil (Idéntico a Dashboard)
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

            // Menú Lateral (Sidebar) Colapsar / Expandir (Desktop) (Idéntico a Dashboard)
            const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
            const adminSidebar = document.getElementById('adminSidebar');

            if (sidebarToggleBtn && adminSidebar) {
                sidebarToggleBtn.addEventListener('click', function () {
                    const isCollapsed = adminSidebar.classList.toggle('collapsed');
                    sidebarToggleBtn.setAttribute('aria-expanded', isCollapsed ? 'false' : 'true');
                    sidebarToggleBtn.setAttribute('title', isCollapsed ? 'Expandir menú' : 'Colapsar menú');
                });
            }

            // Menú Lateral (Sidebar) en Móvil (Drawer) (Idéntico a Dashboard)
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

            // Inicializar visibilidad de contraseñas con ojito (Crear y Editar)
            initPasswordToggle('toggleCreatePassword', 'workerPassword');
            initPasswordToggle('toggleEditPassword', 'editWorkerPassword');
        });

        // Alternador de visibilidad de contraseña (Diseño y comportamiento idéntico a Login)
        function initPasswordToggle(btnId, inputId) {
            const btn = document.getElementById(btnId);
            const input = document.getElementById(inputId);
            if (!btn || !input) return;

            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const isPassword = input.getAttribute('type') === 'password';
                input.setAttribute('type', isPassword ? 'text' : 'password');
                const eye = btn.querySelector('.eye-icon');
                const eyeOff = btn.querySelector('.eye-off-icon');
                if (eye && eyeOff) {
                    eye.classList.toggle('hidden', isPassword);
                    eyeOff.classList.toggle('hidden', !isPassword);
                }
            });
        }

        function resetPasswordToggleState(btnId, inputId) {
            const btn = document.getElementById(btnId);
            const input = document.getElementById(inputId);
            if (input) {
                input.setAttribute('type', 'password');
                input.value = '';
            }
            if (btn) {
                const eye = btn.querySelector('.eye-icon');
                const eyeOff = btn.querySelector('.eye-off-icon');
                if (eye) eye.classList.remove('hidden');
                if (eyeOff) eyeOff.classList.add('hidden');
            }
        }

        // Funciones de Modales para Trabajadores
        function openModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.add('open');
                document.body.style.overflow = 'hidden';
            }
            if (id === 'modalCreateWorker') {
                resetPasswordToggleState('toggleCreatePassword', 'workerPassword');
            }
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.remove('open');
                document.body.style.overflow = '';
            }
            if (id === 'modalEditWorker') {
                resetPasswordToggleState('toggleEditPassword', 'editWorkerPassword');
            }
            if (id === 'modalCreateWorker') {
                resetPasswordToggleState('toggleCreatePassword', 'workerPassword');
            }
        }

        document.querySelectorAll('.modal-overlay').forEach(modal => {
            modal.addEventListener('click', function (e) {
                if (e.target === this) {
                    closeModal(this.id);
                }
            });
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal-overlay.open').forEach(modal => {
                    closeModal(modal.id);
                });
            }
        });

        function previewImage(input, previewId, placeholderId) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const preview = document.getElementById(previewId);
                    const placeholder = document.getElementById(placeholderId);
                    if (preview) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    }
                    if (placeholder) {
                        placeholder.style.display = 'none';
                    }
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function openEditWorker(id, name, lastname, phone, email, address, exp, status, photoUrl) {
            document.getElementById('editWorkerId').value = id;
            document.getElementById('editWorkerName').value = name;
            document.getElementById('editWorkerLastName').value = lastname;
            
            const phoneInput = document.getElementById('editWorkerPhone');
            phoneInput.value = phone;
            validatePhoneInput(phoneInput);

            const emailInput = document.getElementById('editWorkerEmail');
            emailInput.value = email;
            validateEmailInput(emailInput);

            // Resetear campo de contraseña y visibilidad en edición
            const editPassInput = document.getElementById('editWorkerPassword');
            if (editPassInput) {
                editPassInput.value = '';
            }
            resetPasswordToggleState('toggleEditPassword', 'editWorkerPassword');

            document.getElementById('editWorkerAddress').value = address;
            document.getElementById('editWorkerExperience').value = exp;
            
            const statusSelect = document.getElementById('editWorkerStatus');
            if (statusSelect) {
                statusSelect.value = (status === 'active' || status == '1' || status === true) ? 'active' : 'inactive';
            }

            const photoPreview = document.getElementById('editWorkerPhotoPreview');
            const placeholder = document.getElementById('editPhotoPlaceholder');
            const photoInput = document.getElementById('editWorkerPhotoInput');
            if (photoInput) photoInput.value = '';

            if (photoUrl && photoUrl.trim() !== '') {
                photoPreview.src = photoUrl;
                photoPreview.style.display = 'block';
                placeholder.style.display = 'none';
            } else {
                photoPreview.src = '';
                photoPreview.style.display = 'none';
                placeholder.style.display = 'block';
            }

            openModal('modalEditWorker');
        }

        function toggleWorkerServices(workerId) {
            const list = document.getElementById(`worker-services-list-${workerId}`);
            const arrow = document.getElementById(`services-arrow-${workerId}`);
            const btn = document.querySelector(`#accordion-services-${workerId} .addon-heading-toggle`);
            if (!list) return;

            const isCurrentlyHidden = list.style.display === 'none' || getComputedStyle(list).display === 'none';
            if (isCurrentlyHidden) {
                list.style.display = 'flex';
                if (arrow) arrow.classList.add('expanded');
                if (btn) btn.setAttribute('aria-expanded', 'true');
            } else {
                list.style.display = 'none';
                if (arrow) arrow.classList.remove('expanded');
                if (btn) btn.setAttribute('aria-expanded', 'false');
            }
        }

        function openServicesModal(id, workerName, assignedServiceIds = []) {
            document.getElementById('servicesWorkerId').value = id;
            document.getElementById('servicesWorkerName').textContent = workerName;

            // Desmarcar todos los checkboxes
            const checkboxes = document.querySelectorAll('.service-checkbox');
            checkboxes.forEach(cb => {
                cb.checked = false;
            });

            // Marcar los asignados al trabajador
            if (Array.isArray(assignedServiceIds)) {
                assignedServiceIds.forEach(serviceId => {
                    const cb = document.querySelector(`.service-checkbox[value="${serviceId}"]`);
                    if (cb) cb.checked = true;
                });
            }

            openModal('modalServices');
        }

        // Almacenamiento frontend de bloqueos por especialista (Inicia completamente vacío para cada trabajador)
        const workerBlocksStore = {};

        // Límites globales de la sucursal disponibles en el Frontend (PARTE 1)
        const branchLimits = @json($branchLimits ?? \App\Models\HorarioSucursal::getHorariosMap());

        // Aplica restricciones dinámicas de horas y texto de ayuda visual de la sucursal
        function applyBranchLimitsToWorkerModal(limits, currentSchedule = null) {
            const days = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes'];

            days.forEach(day => {
                const bDay = limits[day] || { abierto: true, apertura: '09:00', cierre: '20:00' };
                const row = document.getElementById(`sched-row-${day}`);
                const dot = document.getElementById(`sched-dot-${day}`);
                const selectIn = document.getElementById(`sched-${day}-in`);
                const selectOut = document.getElementById(`sched-${day}-out`);
                const hintEl = document.getElementById(`sched-hint-${day}`);

                const isOpen = bDay.abierto;
                const bStart = bDay.apertura || '09:00';
                const bEnd = bDay.cierre || '20:00';

                // Texto de ayuda visual debajo de los campos de hora
                if (hintEl) {
                    if (isOpen) {
                        hintEl.textContent = `(Límite sucursal: ${bStart} a ${bEnd})`;
                        hintEl.style.color = 'var(--text-muted)';
                    } else {
                        hintEl.textContent = '(Sucursal cerrada este día)';
                        hintEl.style.color = 'var(--barber-red)';
                    }
                }

                if (dot) {
                    dot.style.background = isOpen ? '#10b981' : '#64748b';
                    dot.style.boxShadow = isOpen ? '0 0 6px rgba(16, 185, 129, 0.5)' : 'none';
                }

                if (row) {
                    if (isOpen) {
                        row.classList.remove('day-closed');
                    } else {
                        row.classList.add('day-closed');
                    }
                }

                const currentDay = currentSchedule && currentSchedule[day] ? currentSchedule[day] : null;
                const selectedIn = currentDay && currentDay.entrada ? currentDay.entrada : bStart;
                const selectedOut = currentDay && currentDay.salida ? currentDay.salida : (bEnd <= '19:00' ? bEnd : '19:00');

                // Lista de intervalos continuos en formato 24 horas
                const allSlots = [];
                allSlots.push('00:00');
                allSlots.push('00:30');
                for (let h = 1; h <= 23; h++) {
                    const hh = String(h).padStart(2, '0');
                    allSlots.push(`${hh}:00`);
                    allSlots.push(`${hh}:30`);
                }
                allSlots.push('24:00');

                // Restricción dinámica en campos "Desde" y "Hasta": no se pueden seleccionar horas fuera del rango de la sucursal
                if (selectIn) {
                    selectIn.disabled = !isOpen;
                    selectIn.innerHTML = '';
                    const inSlots = allSlots.filter(s => s >= bStart && s < bEnd);
                    inSlots.forEach(s => {
                        const opt = document.createElement('option');
                        opt.value = s;
                        opt.textContent = s;
                        if (s === selectedIn) opt.selected = true;
                        selectIn.appendChild(opt);
                    });
                    if (selectIn.selectedIndex === -1 && inSlots.length > 0) {
                        selectIn.selectedIndex = 0;
                    }
                    // Al modificar el horario laboral del trabajador en tiempo real, actualizar el bloqueo
                    selectIn.onchange = function () {
                        if (!currentModalWorkerSchedule) currentModalWorkerSchedule = {};
                        if (!currentModalWorkerSchedule[day]) currentModalWorkerSchedule[day] = {};
                        currentModalWorkerSchedule[day].entrada = this.value;
                        onBlockDateChanged();
                    };
                }

                if (selectOut) {
                    selectOut.disabled = !isOpen;
                    selectOut.innerHTML = '';
                    const outSlots = allSlots.filter(s => s > bStart && s <= bEnd);
                    outSlots.forEach(s => {
                        const opt = document.createElement('option');
                        opt.value = s;
                        opt.textContent = s;
                        if (s === selectedOut) opt.selected = true;
                        selectOut.appendChild(opt);
                    });
                    if (selectOut.selectedIndex === -1 && outSlots.length > 0) {
                        selectOut.selectedIndex = outSlots.length - 1;
                    }
                    // Al modificar el horario laboral del trabajador en tiempo real, actualizar el bloqueo
                    selectOut.onchange = function () {
                        if (!currentModalWorkerSchedule) currentModalWorkerSchedule = {};
                        if (!currentModalWorkerSchedule[day]) currentModalWorkerSchedule[day] = {};
                        currentModalWorkerSchedule[day].salida = this.value;
                        onBlockDateChanged();
                    };
                }
            });
        }

        // Abrir Modal de Horarios (Frontend y consulta de límites/horario guardado)
        async function openScheduleModal(id, workerName) {
            const idInput = document.getElementById('scheduleWorkerId');
            const nameEl = document.getElementById('scheduleWorkerName');
            if (idInput) idInput.value = id;
            if (nameEl) nameEl.textContent = workerName;

            // Limpiar campos del formulario de nuevo bloqueo y restringir a fechas actuales o futuras
            const dateInput = document.getElementById('newBlockDate');
            const startInput = document.getElementById('newBlockStartTime');
            const endInput = document.getElementById('newBlockEndTime');
            const reasonInput = document.getElementById('newBlockReason');
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const todayStr = `${year}-${month}-${day}`;

            if (dateInput) {
                dateInput.value = '';
            }
            const nativePicker = document.getElementById('newBlockDateNativePicker');
            if (nativePicker) {
                nativePicker.value = '';
                nativePicker.min = todayStr;
                nativePicker.max = `${year}-12-31`;
            }
            if (startInput) startInput.value = '';
            if (endInput) endInput.value = '';
            if (reasonInput) reasonInput.value = '';

            // Restablecer variables de horario del modal
            currentModalWorkerSchedule = null;
            currentModalBranchLimits = branchLimits;
            const hintBox = document.getElementById('newBlockRangeHint');
            if (hintBox) hintBox.style.display = 'none';

            // Cada especialista inicia sin bloqueos predeterminados
            if (!workerBlocksStore[id]) {
                workerBlocksStore[id] = [];
            }
            renderWorkerBlocks(id);

            // Aplicar límites base inmediatos
            applyBranchLimitsToWorkerModal(branchLimits);

            // Obtener el horario guardado del especialista y límites actualizados
            try {
                const response = await fetch(`/admin/trabajadores/${id}/horario`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (response.ok) {
                    const data = await response.json();
                    const limits = data.branch_limits || branchLimits;
                    currentModalBranchLimits = limits;
                    currentModalWorkerSchedule = data.horario || null;
                    applyBranchLimitsToWorkerModal(limits, data.horario);
                }
            } catch (err) {
                // Silencioso, mantiene la configuración previa cargada
            }

            openModal('modalSchedule');
        }

        // Renderizar lista de bloqueos del especialista
        function renderWorkerBlocks(workerId) {
            const container = document.getElementById('blocksListContainer');
            const emptyMsg = document.getElementById('blocksEmptyMsg');
            if (!container) return;

            container.innerHTML = '';
            const blocks = workerBlocksStore[workerId] || [];

            if (blocks.length === 0) {
                if (emptyMsg) emptyMsg.style.display = 'block';
                return;
            }

            if (emptyMsg) emptyMsg.style.display = 'none';

            blocks.forEach((block, index) => {
                const row = document.createElement('div');
                row.className = 'block-row-item';
                row.innerHTML = `
                    <div class="block-row-data">
                        <span class="block-tag-date">${escapeHtml(block.formattedDate)}</span>
                        <span class="block-tag-sep">|</span>
                        <span class="block-tag-time">${escapeHtml(block.start)} - ${escapeHtml(block.end)}</span>
                        <span class="block-tag-sep">|</span>
                        <span class="block-tag-reason">${escapeHtml(block.reason)}</span>
                    </div>
                    <button type="button" class="btn-remove-block" title="Eliminar bloqueo" onclick="removeAvailabilityBlock(${workerId}, ${index})">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            <line x1="10" y1="11" x2="10" y2="17"></line>
                            <line x1="14" y1="11" x2="14" y2="17"></line>
                        </svg>
                    </button>
                `;
                container.appendChild(row);
            });
        }

        // Cache del horario del trabajador y límites actuales del modal
        let currentModalWorkerSchedule = null;
        let currentModalBranchLimits = branchLimits;

        // Mapea fecha YYYY-MM-DD al día de la semana correspondiente
        function getDayKeyFromDateString(dateStr) {
            if (!dateStr || !dateStr.includes('-')) return null;
            const parts = dateStr.split('-');
            const year = parseInt(parts[0], 10);
            const month = parseInt(parts[1], 10) - 1;
            const day = parseInt(parts[2], 10);
            const dt = new Date(year, month, day);
            const daysOfWeek = ['domingo', 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
            return daysOfWeek[dt.getDay()] || null;
        }

        // Obtiene el rango de horario laboral efectivo del trabajador y la sucursal para un día específico
        function getEffectiveWorkingHoursForDate(dateStr) {
            const dayKey = getDayKeyFromDateString(dateStr);
            if (!dayKey) return { valid: false, message: 'Fecha no válida.' };

            const dayNames = {
                lunes: 'Lunes',
                martes: 'Martes',
                miercoles: 'Miércoles',
                jueves: 'Jueves',
                viernes: 'Viernes',
                sabado: 'Sábado',
                domingo: 'Domingo'
            };
            const nombreDia = dayNames[dayKey] || dayKey;

            // 1. Validar horario operativo de la sucursal
            const bDay = currentModalBranchLimits[dayKey] || branchLimits[dayKey] || null;
            if (!bDay || !bDay.abierto) {
                return {
                    valid: false,
                    dayKey,
                    nombreDia,
                    message: `La sucursal permanece cerrada los días ${nombreDia}. No se pueden registrar bloqueos en este día.`
                };
            }

            const branchOpen = bDay.apertura || '09:00';
            const branchClose = bDay.cierre || '20:00';

            // 2. Validar horario laboral asignado al trabajador en Sección A
            // Si hay inputs en el DOM para ese día, leerlos directamente; sino, revisar currentModalWorkerSchedule
            let workerIn = null;
            let workerOut = null;

            const selectIn = document.getElementById(`sched-${dayKey}-in`);
            const selectOut = document.getElementById(`sched-${dayKey}-out`);

            if (selectIn && selectOut && !selectIn.disabled && selectIn.value && selectOut.value) {
                workerIn = selectIn.value;
                workerOut = selectOut.value;
            } else if (currentModalWorkerSchedule && currentModalWorkerSchedule[dayKey]) {
                workerIn = currentModalWorkerSchedule[dayKey].entrada;
                workerOut = currentModalWorkerSchedule[dayKey].salida;
            }

            // Los trabajadores laboran de lunes a viernes en Sección A
            if (!workerIn || !workerOut || ['sabado', 'domingo'].includes(dayKey)) {
                return {
                    valid: false,
                    dayKey,
                    nombreDia,
                    message: `El especialista no tiene jornada laboral asignada para el día ${nombreDia}. Los bloqueos solo pueden programarse dentro de su horario de trabajo.`
                };
            }

            // Rango permitido: intersección entre el horario laboral del trabajador y el horario de la sucursal
            const allowedStart = workerIn > branchOpen ? workerIn : branchOpen;
            const allowedEnd = workerOut < branchClose ? workerOut : branchClose;

            if (allowedStart >= allowedEnd) {
                return {
                    valid: false,
                    dayKey,
                    nombreDia,
                    message: `No existe un rango de trabajo válido disponible para el día ${nombreDia}.`
                };
            }

            return {
                valid: true,
                dayKey,
                nombreDia,
                branchOpen,
                branchClose,
                workerIn,
                workerOut,
                allowedStart,
                allowedEnd
            };
        }

        // ===================================================
        // MANEJO Y VALIDACIÓN DE INPUTS DE BLOQUEOS DE DISPONIBILIDAD
        // Formato DD/MM/AAAA, máscara estricta, validación diferida (onBlur / 8 dígitos)
        // y restricción estricta al año actual del sistema (igual que en Fechas Especiales).
        // ===================================================

        function openWorkerBlockDatePicker(pickerId) {
            const picker = document.getElementById(pickerId);
            if (!picker) return;
            if (typeof picker.showPicker === 'function') {
                picker.showPicker();
            } else {
                picker.click();
            }
        }

        // Convierte fecha YYYY-MM-DD a DD/MM/AAAA
        function formatBlockYmdToDmy(ymd) {
            if (!ymd || !ymd.includes('-')) return '';
            const parts = ymd.split('-');
            if (parts.length !== 3) return '';
            return `${parts[2]}/${parts[1]}/${parts[0]}`;
        }

        // Convierte fecha DD/MM/AAAA a YYYY-MM-DD
        function formatBlockDmyToYmd(dmy) {
            if (!dmy || !dmy.includes('/')) return '';
            const parts = dmy.split('/');
            if (parts.length !== 3) return '';
            return `${parts[2]}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}`;
        }

        // Validación estricta diferida (al perder foco o al completar los 8 dígitos)
        function validateWorkerBlockDateField(input) {
            const val = input.value.trim();
            if (!val) {
                const nativePicker = document.getElementById('newBlockDateNativePicker');
                if (nativePicker) nativePicker.value = '';
                onBlockDateChanged();
                return true;
            }

            // Si el usuario no ha completado los 10 caracteres (DD/MM/AAAA), avisar y limpiar
            if (val.length < 10) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Fecha incompleta',
                    text: 'Por favor ingresa la fecha completa en formato DD/MM/AAAA.',
                    background: '#1e293b',
                    color: '#ffffff',
                    confirmButtonColor: '#0055ff'
                });
                input.value = '';
                const nativePicker = document.getElementById('newBlockDateNativePicker');
                if (nativePicker) nativePicker.value = '';
                onBlockDateChanged();
                return false;
            }

            const regex = /^(\d{2})\/(\d{2})\/(\d{4})$/;
            const match = val.match(regex);
            if (!match) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Formato incorrecto',
                    text: 'El formato de fecha debe ser DD/MM/AAAA (día, mes y año).',
                    background: '#1e293b',
                    color: '#ffffff',
                    confirmButtonColor: '#0055ff'
                });
                input.value = '';
                const nativePicker = document.getElementById('newBlockDateNativePicker');
                if (nativePicker) nativePicker.value = '';
                onBlockDateChanged();
                return false;
            }

            const day = parseInt(match[1], 10);
            const month = parseInt(match[2], 10);
            const year = parseInt(match[3], 10);

            const now = new Date();
            const currentYear = now.getFullYear();

            // Regla estricta: Únicamente fechas del año actual
            if (year !== currentYear) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Año no permitido',
                    text: `Únicamente se admiten fechas correspondientes al año en curso (${currentYear}).`,
                    background: '#1e293b',
                    color: '#ffffff',
                    confirmButtonColor: '#0055ff'
                });
                input.value = '';
                const nativePicker = document.getElementById('newBlockDateNativePicker');
                if (nativePicker) nativePicker.value = '';
                onBlockDateChanged();
                return false;
            }

            // Validar existencia de fecha real (días válidos por mes y año bisiesto)
            if (month < 1 || month > 12) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Mes inválido',
                    text: 'El mes ingresado no es válido (debe estar entre 01 y 12).',
                    background: '#1e293b',
                    color: '#ffffff',
                    confirmButtonColor: '#0055ff'
                });
                input.value = '';
                const nativePicker = document.getElementById('newBlockDateNativePicker');
                if (nativePicker) nativePicker.value = '';
                onBlockDateChanged();
                return false;
            }

            const testDate = new Date(year, month - 1, day);
            if (testDate.getFullYear() !== year || (testDate.getMonth() + 1) !== month || testDate.getDate() !== day) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Fecha inválida',
                    text: 'La fecha ingresada no existe en el calendario.',
                    background: '#1e293b',
                    color: '#ffffff',
                    confirmButtonColor: '#0055ff'
                });
                input.value = '';
                const nativePicker = document.getElementById('newBlockDateNativePicker');
                if (nativePicker) nativePicker.value = '';
                onBlockDateChanged();
                return false;
            }

            // Validar que no sea fecha pasada dentro del mismo año
            const todayStart = new Date(currentYear, now.getMonth(), now.getDate()).getTime();
            if (testDate.getTime() < todayStart) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Fecha no permitida',
                    text: 'No puedes seleccionar fechas de días anteriores en los bloqueos de disponibilidad.',
                    background: '#1e293b',
                    color: '#ffffff',
                    confirmButtonColor: '#0055ff'
                });
                input.value = '';
                const nativePicker = document.getElementById('newBlockDateNativePicker');
                if (nativePicker) nativePicker.value = '';
                onBlockDateChanged();
                return false;
            }

            const nativePicker = document.getElementById('newBlockDateNativePicker');
            if (nativePicker) {
                nativePicker.value = formatBlockDmyToYmd(val);
            }
            onBlockDateChanged();
            return true;
        }

        // Manejador de evento input: formateo dinámico con máscara DD/MM/AAAA sin disparar alertas prematuras
        function handleBlockDateInput(input) {
            let rawDigits = input.value.replace(/\D/g, '').slice(0, 8);
            let formatted = '';

            if (rawDigits.length > 0) {
                formatted += rawDigits.slice(0, 2);
            }
            if (rawDigits.length > 2) {
                formatted += '/' + rawDigits.slice(2, 4);
            }
            if (rawDigits.length > 4) {
                formatted += '/' + rawDigits.slice(4, 8);
            }

            input.value = formatted;

            // Si ha completado exactamente los 8 dígitos (10 caracteres "DD/MM/AAAA"), validar
            if (formatted.length === 10) {
                validateWorkerBlockDateField(input);
            }
        }

        function validateAndFormatBlockDate(input) {
            if (input.value.trim().length > 0) {
                validateWorkerBlockDateField(input);
            }
        }

        function syncBlockNativeDate(picker) {
            const ymd = picker.value;
            const textInput = document.getElementById('newBlockDate');
            if (textInput && ymd) {
                textInput.value = formatBlockYmdToDmy(ymd);
                validateWorkerBlockDateField(textInput);
            }
        }

        function getBlockDateYmd() {
            const dateInput = document.getElementById('newBlockDate');
            if (!dateInput || !dateInput.value) return '';
            const val = dateInput.value.trim();
            if (val.includes('/')) {
                return formatBlockDmyToYmd(val);
            }
            return val;
        }

        // Evento cuando cambia la fecha del nuevo bloqueo para actualizar selectores y mostrar límites visuales
        function onBlockDateChanged() {
            const dateVal = getBlockDateYmd();
            const startInput = document.getElementById('newBlockStartTime');
            const endInput = document.getElementById('newBlockEndTime');
            const hintBox = document.getElementById('newBlockRangeHint');
            const hintText = document.getElementById('newBlockRangeHintText');

            if (!startInput || !endInput) return;

            if (!dateVal || dateVal.length < 10) {
                if (hintBox) hintBox.style.display = 'none';
                return;
            }

            const info = getEffectiveWorkingHoursForDate(dateVal);

            // Generar todos los slots estándar
            const allSlots = [];
            allSlots.push('00:00');
            allSlots.push('00:30');
            for (let wh = 1; wh <= 23; wh++) {
                const hh = String(wh).padStart(2, '0');
                allSlots.push(`${hh}:00`);
                allSlots.push(`${hh}:30`);
            }
            allSlots.push('24:00');

            if (!info.valid) {
                if (hintBox && hintText) {
                    hintBox.style.display = 'block';
                    hintText.style.color = 'var(--barber-red)';
                    hintText.textContent = `⚠️ ${info.message}`;
                }
                startInput.innerHTML = '<option value="">No disponible</option>';
                endInput.innerHTML = '<option value="">No disponible</option>';
                startInput.disabled = true;
                endInput.disabled = true;
                return;
            }

            startInput.disabled = false;
            endInput.disabled = false;

            if (hintBox && hintText) {
                hintBox.style.display = 'block';
                hintText.style.color = 'var(--barber-blue-light)';
                hintText.textContent = `ℹ️ Rango laboral disponible para ${info.nombreDia}: ${info.allowedStart} a ${info.allowedEnd} (Horario especialista: ${info.workerIn}–${info.workerOut} | Sucursal: ${info.branchOpen}–${info.branchClose})`;
            }

            const prevStart = startInput.value;
            const prevEnd = endInput.value;

            // Filtrar slots de inicio (desde allowedStart hasta antes de allowedEnd)
            startInput.innerHTML = '<option value="">Seleccionar...</option>';
            allSlots.filter(s => s >= info.allowedStart && s < info.allowedEnd).forEach(slot => {
                const opt = document.createElement('option');
                opt.value = slot;
                opt.textContent = slot;
                if (slot === prevStart) opt.selected = true;
                startInput.appendChild(opt);
            });

            // Filtrar slots de fin (después de allowedStart hasta allowedEnd)
            endInput.innerHTML = '<option value="">Seleccionar...</option>';
            allSlots.filter(s => s > info.allowedStart && s <= info.allowedEnd).forEach(slot => {
                const opt = document.createElement('option');
                opt.value = slot;
                opt.textContent = slot;
                if (slot === prevEnd) opt.selected = true;
                endInput.appendChild(opt);
            });
        }

        // Agregar Bloqueo de Disponibilidad (Frontend)
        function addAvailabilityBlock() {
            const idInput = document.getElementById('scheduleWorkerId');
            const workerId = idInput ? idInput.value : null;
            if (!workerId) return;

            const dateInput = document.getElementById('newBlockDate');
            const startInput = document.getElementById('newBlockStartTime');
            const endInput = document.getElementById('newBlockEndTime');
            const reasonInput = document.getElementById('newBlockReason');

            if (!dateInput || !startInput || !endInput || !reasonInput) return;

            const dateVal = getBlockDateYmd();
            const startVal = startInput.value;
            const endVal = endInput.value;
            const reasonVal = reasonInput.value.trim();

            if (!dateVal || dateVal.length < 10 || !startVal || !endVal || !reasonVal) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Campos requeridos',
                    text: 'Completa la fecha (DD/MM/AAAA), hora de inicio, hora de fin y motivo para registrar el bloqueo.',
                    background: '#1e293b',
                    color: '#ffffff',
                    confirmButtonColor: '#0055ff'
                });
                return;
            }

            if (!validateWorkerBlockDateField(dateInput)) {
                return;
            }

            // 2. Validación de coherencia de horas
            if (startVal >= endVal) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Horario inválido',
                    text: 'La hora de inicio debe ser anterior a la hora de fin.',
                    background: '#1e293b',
                    color: '#ffffff',
                    confirmButtonColor: '#0055ff'
                });
                return;
            }

            // 3. VALIDACIÓN ESTRICTA: Restringir dentro del horario laboral del trabajador y operativo de la sucursal
            const workingInfo = getEffectiveWorkingHoursForDate(dateVal);
            if (!workingInfo.valid) {
                Swal.fire({
                    icon: 'error',
                    title: 'Día no disponible',
                    text: workingInfo.message,
                    background: '#1e293b',
                    color: '#ffffff',
                    confirmButtonColor: '#ef4444'
                });
                return;
            }

            // Validar que el inicio no sea antes de la hora permitida
            if (startVal < workingInfo.allowedStart) {
                Swal.fire({
                    icon: 'error',
                    title: 'Hora fuera de rango',
                    html: `La hora de inicio (<b>${startVal}</b>) no puede ser anterior al inicio del horario laboral para el ${workingInfo.nombreDia} (<b>${workingInfo.allowedStart}</b>).<br><small style="color: #94a3b8;">Horario del trabajador: ${workingInfo.workerIn}–${workingInfo.workerOut} | Sucursal: ${workingInfo.branchOpen}–${workingInfo.branchClose}</small>`,
                    background: '#1e293b',
                    color: '#ffffff',
                    confirmButtonColor: '#ef4444'
                });
                return;
            }

            // Validar que el fin no exceda el límite permitido
            if (endVal > workingInfo.allowedEnd) {
                Swal.fire({
                    icon: 'error',
                    title: 'Hora fuera de rango',
                    html: `La hora de fin (<b>${endVal}</b>) no puede exceder el fin del horario laboral para el ${workingInfo.nombreDia} (<b>${workingInfo.allowedEnd}</b>).<br><small style="color: #94a3b8;">Horario del trabajador: ${workingInfo.workerIn}–${workingInfo.workerOut} | Sucursal: ${workingInfo.branchOpen}–${workingInfo.branchClose}</small>`,
                    background: '#1e293b',
                    color: '#ffffff',
                    confirmButtonColor: '#ef4444'
                });
                return;
            }

            // Formato de fecha corto (ej. 12 Oct)
            const parts = dateVal.split('-');
            const months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
            const formattedDate = `${parseInt(parts[2], 10)} ${months[parseInt(parts[1], 10) - 1]}`;

            if (!workerBlocksStore[workerId]) {
                workerBlocksStore[workerId] = [];
            }

            workerBlocksStore[workerId].push({
                date: dateVal,
                formattedDate: formattedDate,
                start: startVal,
                end: endVal,
                reason: reasonVal
            });

            renderWorkerBlocks(workerId);

            // Limpiar formulario
            dateInput.value = '';
            const nativePicker = document.getElementById('newBlockDateNativePicker');
            if (nativePicker) nativePicker.value = '';
            startInput.value = '';
            endInput.value = '';
            reasonInput.value = '';
            const hintBox = document.getElementById('newBlockRangeHint');
            if (hintBox) hintBox.style.display = 'none';
        }

        // Eliminar Bloqueo de Disponibilidad (Frontend)
        function removeAvailabilityBlock(workerId, index) {
            if (workerBlocksStore[workerId] && workerBlocksStore[workerId][index] !== undefined) {
                workerBlocksStore[workerId].splice(index, 1);
                renderWorkerBlocks(workerId);
            }
        }

        // Variable global para rastrear el especialista seleccionado en el modal de detalle
        let currentDetailWorkerId = null;
        let currentDetailWorkerName = '';

        // Abrir Modal de Vista Detallada de Bloqueos de Disponibilidad del Especialista
        function openWorkerBlocksDetailModal(workerId, workerName) {
            currentDetailWorkerId = workerId;
            currentDetailWorkerName = workerName;

            const nameEl = document.getElementById('detailModalWorkerName');
            const avatarEl = document.getElementById('detailModalAvatar');
            if (nameEl) nameEl.textContent = workerName;

            // Generar iniciales en el avatar
            if (avatarEl) {
                const words = (workerName || '').trim().split(' ');
                const initials = (words[0] ? words[0][0] : '') + (words[1] ? words[1][0] : '');
                avatarEl.textContent = initials.toUpperCase() || 'E';
            }

            renderDetailModalBlocks(workerId);
            openModal('modalWorkerBlocksDetail');
        }

        // Renderizar el listado en la vista detallada de bloqueos
        function renderDetailModalBlocks(workerId) {
            const listEl = document.getElementById('detailModalBlocksList');
            const emptyEl = document.getElementById('detailModalEmptyState');
            const countNumEl = document.getElementById('detailModalCountNumber');
            const blocks = workerBlocksStore[workerId] || [];

            if (countNumEl) countNumEl.textContent = blocks.length;

            if (!listEl) return;
            listEl.innerHTML = '';

            if (blocks.length === 0) {
                if (emptyEl) emptyEl.style.display = 'block';
                return;
            }

            if (emptyEl) emptyEl.style.display = 'none';

            blocks.forEach((block, index) => {
                const item = document.createElement('div');
                item.className = 'blocks-detail-card-item';
                item.innerHTML = `
                    <div class="blocks-detail-item-left">
                        <span class="blocks-detail-item-date-badge">${escapeHtml(block.formattedDate || block.date)}</span>
                        <span class="blocks-detail-item-time">
                            <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            ${escapeHtml(block.start)} - ${escapeHtml(block.end)}
                        </span>
                        <span style="color: rgba(255,255,255,0.2);">|</span>
                        <span class="blocks-detail-item-reason" title="${escapeHtml(block.reason)}">
                            ${escapeHtml(block.reason || 'Sin motivo especificado')}
                        </span>
                    </div>
                    <button type="button" class="btn-remove-block" title="Eliminar bloqueo" onclick="removeDetailModalBlock(${workerId}, ${index})">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            <line x1="10" y1="11" x2="10" y2="17"></line>
                            <line x1="14" y1="11" x2="14" y2="17"></line>
                        </svg>
                    </button>
                `;
                listEl.appendChild(item);
            });
        }

        // Eliminar un bloqueo directamente desde la vista detallada
        function removeDetailModalBlock(workerId, index) {
            removeAvailabilityBlock(workerId, index);
            renderDetailModalBlocks(workerId);
        }

        // Acceso directo a configurar horario y gestionar bloqueos desde el modal de detalle
        function handleDetailManageSchedule() {
            closeModal('modalWorkerBlocksDetail');
            if (currentDetailWorkerId && currentDetailWorkerName) {
                openScheduleModal(currentDetailWorkerId, currentDetailWorkerName);
            }
        }

        // Guardar Horario del Trabajador en Backend con Validación Estricta (PARTE 1)
        async function saveWorkerSchedule(e) {
            e.preventDefault();
            const idInput = document.getElementById('scheduleWorkerId');
            const workerId = idInput ? idInput.value : null;
            if (!workerId) return;

            const workerName = document.getElementById('scheduleWorkerName').textContent || 'Trabajador';
            const days = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes'];
            const horario = {};

            // Validación estricta previa en el cliente
            for (const day of days) {
                const selectIn = document.getElementById(`sched-${day}-in`);
                const selectOut = document.getElementById(`sched-${day}-out`);
                const bDay = branchLimits[day] || { abierto: true, apertura: '09:00', cierre: '20:00' };
                const nombreDia = bDay.nombre || (day.charAt(0).toUpperCase() + day.slice(1));

                if (selectIn && selectOut && !selectIn.disabled) {
                    const entrada = selectIn.value;
                    const salida = selectOut.value;

                    if (entrada >= salida) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Horario inválido',
                            text: `La hora de entrada debe ser anterior a la hora de salida para el ${nombreDia}.`,
                            background: '#1e293b',
                            color: '#ffffff',
                            confirmButtonColor: '#0055ff'
                        });
                        return;
                    }

                    // Validación contra el horario global de la sucursal
                    if (entrada < bDay.apertura || salida > bDay.cierre) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Excede límites de sucursal',
                            text: `El horario asignado para el ${nombreDia} excede el horario de la sucursal (${bDay.apertura} - ${bDay.cierre})`,
                            background: '#1e293b',
                            color: '#ffffff',
                            confirmButtonColor: '#ef4444'
                        });
                        return;
                    }

                    horario[day] = { entrada, salida };
                }
            }

            const submitBtn = document.querySelector('#formWorkerSchedule button[type="submit"]');
            const originalBtnHtml = submitBtn ? submitBtn.innerHTML : '';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `<span>Guardando horario...</span>`;
            }

            try {
                const response = await fetch(`/admin/trabajadores/${workerId}/horario`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ horario })
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    closeModal('modalSchedule');

                    // 1. Actualizar dinámicamente el apartadito abajo de Servicios Asociados
                    const statusEl = document.getElementById(`worker-status-today-${workerId}`);
                    const cardEl = document.getElementById(`worker-card-${workerId}`);

                    const isConDisponibilidad = (result.estado_hoy === 'Con disponibilidad de horario' || (result.laborando_hoy && result.estado_hoy !== 'Sin disponibilidad'));

                    if (statusEl) {
                        if (isConDisponibilidad) {
                            statusEl.innerHTML = `
                                <div class="worker-alert-available alert-flash-animation">
                                    <span class="worker-alert-dot"></span>
                                    <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                    <span>Con disponibilidad de horario</span>
                                </div>
                            `;
                        } else {
                            statusEl.innerHTML = `
                                <div class="worker-alert-unavailable alert-flash-animation">
                                    <span class="worker-alert-dot-off"></span>
                                    <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="15" y1="9" x2="9" y2="15"></line>
                                        <line x1="9" y1="9" x2="15" y2="15"></line>
                                    </svg>
                                    <span>Sin disponibilidad</span>
                                </div>
                            `;
                        }
                    }

                    if (cardEl) {
                        cardEl.classList.add('card-highlight-pulse');
                        setTimeout(() => cardEl.classList.remove('card-highlight-pulse'), 3000);
                    }

                    // 2. Alerta visual inmediata luego de guardar el horario
                    if (isConDisponibilidad) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Horario guardado!',
                            html: `El horario de <strong>${workerName}</strong> se guardó correctamente.<br><br><div style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 14px; background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 8px; font-weight: 600; font-size: 13px;">✔ Con disponibilidad de horario</div>`,
                            background: '#141f38',
                            color: '#ffffff',
                            confirmButtonColor: '#0055ff',
                            confirmButtonText: 'Aceptar'
                        });
                    } else {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: result.message || `Horario de ${workerName} configurado correctamente.`,
                            showConfirmButton: false,
                            timer: 3500,
                            timerProgressBar: true,
                            background: '#10b981',
                            color: '#ffffff',
                            iconColor: '#ffffff'
                        });
                    }
                } else {
                    let errorMsg = result.message || 'No se pudo guardar el horario.';
                    if (result.errors) {
                        errorMsg = Object.values(result.errors).flat().join('<br>');
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de validación',
                        html: errorMsg,
                        background: '#1e293b',
                        color: '#ffffff',
                        confirmButtonColor: '#ef4444'
                    });
                }
            } catch (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo comunicar con el servidor.',
                    background: '#1e293b',
                    color: '#ffffff',
                    confirmButtonColor: '#ef4444'
                });
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnHtml;
                }
            }
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }



        let currentStatusFilter = 'all';

        function setFilter(status, btn) {
            currentStatusFilter = status;
            document.querySelectorAll('.btn-filter').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            filterWorkers();
        }

        function filterWorkers() {
            const query = (document.getElementById('searchWorkerInput').value || '').toLowerCase().trim();
            const cards = document.querySelectorAll('.worker-profile-card');
            const rows = document.querySelectorAll('.worker-table-row');
            const totalCount = cards.length;
            let visibleCount = 0;

            // Filtrar tarjetas (únicamente por nombre y correo electrónico)
            cards.forEach(card => {
                const name = (card.getAttribute('data-name') || '').toLowerCase();
                const email = (card.getAttribute('data-email') || '').toLowerCase();
                const status = card.getAttribute('data-status');

                const matchesQuery = !query || name.includes(query) || email.includes(query);
                const matchesStatus = currentStatusFilter === 'all' || status === currentStatusFilter;

                if (matchesQuery && matchesStatus) {
                    card.style.display = 'flex';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Filtrar filas de tabla (únicamente por nombre y correo electrónico)
            rows.forEach(row => {
                const name = (row.getAttribute('data-name') || '').toLowerCase();
                const email = (row.getAttribute('data-email') || '').toLowerCase();
                const status = row.getAttribute('data-status');

                const matchesQuery = !query || name.includes(query) || email.includes(query);
                const matchesStatus = currentStatusFilter === 'all' || status === currentStatusFilter;

                row.style.display = (matchesQuery && matchesStatus) ? '' : 'none';
            });

            // Actualizar contador dinámico de especialistas visibles
            const countBadge = document.getElementById('workersCountBadge');
            if (countBadge) {
                countBadge.textContent = `Mostrando ${visibleCount} de ${totalCount} especialistas`;
            }

            const emptyState = document.getElementById('emptyStateContainer');
            if (emptyState) {
                emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
            }
        }

        function resetFilters() {
            document.getElementById('searchWorkerInput').value = '';
            setFilter('all', document.getElementById('filterAll'));
        }

        function toggleView(view) {
            const grid = document.getElementById('workersGrid');
            const table = document.getElementById('workersTableContainer');
            const btnGrid = document.getElementById('btnViewGrid');
            const btnTable = document.getElementById('btnViewTable');

            if (view === 'grid') {
                grid.style.display = 'grid';
                table.style.display = 'none';
                btnGrid.classList.add('active');
                btnTable.classList.remove('active');
            } else {
                grid.style.display = 'none';
                table.style.display = 'block';
                btnGrid.classList.remove('active');
                btnTable.classList.add('active');
            }
        }

        function handleFormSubmit(e, message) {
            e.preventDefault();
            document.querySelectorAll('.modal-overlay.open').forEach(modal => {
                closeModal(modal.id);
            });
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: message,
                showConfirmButton: false,
                timer: 3500,
                timerProgressBar: true,
                background: '#10b981',
                color: '#ffffff',
                iconColor: '#ffffff'
            });
        }

        // Funciones de validación en tiempo real para teléfono y correo
        function validatePhoneInput(input) {
            input.value = input.value.replace(/[^0-9]/g, '').slice(0, 10);
            const helpEl = document.getElementById(input.id + 'Help');
            const currentPhone = input.value.trim();
            const isEdit = input.id === 'editWorkerPhone';
            const currentEditId = isEdit ? (document.getElementById('editWorkerId')?.value || '') : '';

            // Verificar si el teléfono ya está registrado por otro trabajador (Subtarea 2)
            const isDuplicate = Array.from(document.querySelectorAll('.worker-profile-card'))
                .some(card => {
                    if (isEdit && card.id === `worker-card-${currentEditId}`) {
                        return false;
                    }
                    return (card.getAttribute('data-phone') || '').trim() === currentPhone;
                });

            if (currentPhone.length === 10) {
                if (isDuplicate) {
                    input.style.borderColor = '#ef4444';
                    if (helpEl) {
                        helpEl.textContent = '✗ Este número de teléfono ya está registrado por otro trabajador';
                        helpEl.style.color = '#ef4444';
                    }
                } else {
                    input.style.borderColor = '#10b981';
                    if (helpEl) {
                        helpEl.textContent = '✓ 10 dígitos numéricos correctos (disponible)';
                        helpEl.style.color = '#10b981';
                    }
                }
            } else if (currentPhone.length > 0) {
                input.style.borderColor = '#f59e0b';
                if (helpEl) {
                    helpEl.textContent = `Faltan ${10 - currentPhone.length} dígitos (debe tener 10 exactos)`;
                    helpEl.style.color = '#f59e0b';
                }
            } else {
                input.style.borderColor = '';
                if (helpEl) {
                    helpEl.textContent = 'Exactamente 10 dígitos numéricos';
                    helpEl.style.color = 'var(--text-muted)';
                }
            }
        }

        function validateEmailInput(input) {
            const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            const helpEl = document.getElementById(input.id + 'Help');
            if (emailRegex.test(input.value.trim())) {
                input.style.borderColor = '#10b981';
                if (helpEl) {
                    helpEl.textContent = '✓ Formato de correo válido';
                    helpEl.style.color = '#10b981';
                }
            } else if (input.value.trim().length > 0) {
                input.style.borderColor = '#f59e0b';
                if (helpEl) {
                    helpEl.textContent = 'Introduce un formato válido: usuario@dominio.com';
                    helpEl.style.color = '#f59e0b';
                }
            } else {
                input.style.borderColor = '';
                if (helpEl) {
                    helpEl.textContent = 'Formato válido: usuario@dominio.com';
                    helpEl.style.color = 'var(--text-muted)';
                }
            }
        }

        // Función centralizada para detectar duplicidad en nombre completo (nombre y apellidos)
        function checkWorkerFullNameDuplicate(nameVal, lastNameVal, excludeWorkerId = null) {
            const clean = (s) => (s || '').toString().trim().toLowerCase().replace(/\s+/g, ' ');
            const n = clean(nameVal);
            const ln = clean(lastNameVal);
            if (!n || !ln) return false;
            const targetFull = `${n} ${ln}`;

            return Array.from(document.querySelectorAll('.worker-profile-card, .worker-table-row'))
                .some(card => {
                    const cardId = (card.id || '').replace('worker-card-', '').replace('worker-row-', '');
                    if (excludeWorkerId && cardId === excludeWorkerId.toString()) return false;
                    const cardFirst = clean(card.getAttribute('data-first-name'));
                    const cardLast = clean(card.getAttribute('data-last-name'));
                    const cardFull = clean(card.getAttribute('data-full-name') || card.getAttribute('data-name'));

                    if (cardFirst && cardLast) {
                        return cardFirst === n && cardLast === ln;
                    }
                    return cardFull === targetFull;
                });
        }

        // Monitoreo en tiempo real de duplicidad de nombre completo en formulario de creación
        const createNameInput = document.getElementById('workerName');
        const createLastNameInput = document.getElementById('workerLastName');
        const createFullNameHelp = document.getElementById('workerFullNameHelp');

        function updateCreateFullNameStatus() {
            if (!createNameInput || !createLastNameInput || !createFullNameHelp) return;
            const nameVal = createNameInput.value.trim();
            const lastNameVal = createLastNameInput.value.trim();
            if (nameVal && lastNameVal && checkWorkerFullNameDuplicate(nameVal, lastNameVal)) {
                createNameInput.style.borderColor = '#ef4444';
                createLastNameInput.style.borderColor = '#ef4444';
                createFullNameHelp.textContent = '⚠️ Ya existe un trabajador registrado con este nombre y apellidos.';
                createFullNameHelp.style.color = '#ef4444';
            } else {
                createNameInput.style.borderColor = '';
                createLastNameInput.style.borderColor = '';
                createFullNameHelp.textContent = 'El nombre completo (nombre y apellidos) debe ser único en el sistema.';
                createFullNameHelp.style.color = 'var(--text-muted)';
            }
        }

        if (createNameInput && createLastNameInput) {
            createNameInput.addEventListener('input', updateCreateFullNameStatus);
            createLastNameInput.addEventListener('input', updateCreateFullNameStatus);
        }

        // Monitoreo en tiempo real de duplicidad de nombre completo en formulario de edición
        const editNameInput = document.getElementById('editWorkerName');
        const editLastNameInput = document.getElementById('editWorkerLastName');
        const editWorkerIdInput = document.getElementById('editWorkerId');
        const editFullNameHelp = document.getElementById('editWorkerFullNameHelp');

        function updateEditFullNameStatus() {
            if (!editNameInput || !editLastNameInput || !editFullNameHelp) return;
            const nameVal = editNameInput.value.trim();
            const lastNameVal = editLastNameInput.value.trim();
            const workerId = editWorkerIdInput ? editWorkerIdInput.value : null;
            if (nameVal && lastNameVal && checkWorkerFullNameDuplicate(nameVal, lastNameVal, workerId)) {
                editNameInput.style.borderColor = '#ef4444';
                editLastNameInput.style.borderColor = '#ef4444';
                editFullNameHelp.textContent = '⚠️ Ya existe otro trabajador registrado con este nombre y apellidos.';
                editFullNameHelp.style.color = '#ef4444';
            } else {
                editNameInput.style.borderColor = '';
                editLastNameInput.style.borderColor = '';
                editFullNameHelp.textContent = 'El nombre completo (nombre y apellidos) debe ser único en el sistema.';
                editFullNameHelp.style.color = 'var(--text-muted)';
            }
        }

        if (editNameInput && editLastNameInput) {
            editNameInput.addEventListener('input', updateEditFullNameStatus);
            editLastNameInput.addEventListener('input', updateEditFullNameStatus);
        }

        // Envío AJAX funcional para el registro de trabajador (Subtarea 3)
        const formCreate = document.getElementById('formCreateWorker');
        if (formCreate) {
            formCreate.addEventListener('submit', async function (e) {
                e.preventDefault();
                const submitBtn = formCreate.querySelector('.btn-submit-action');
                const originalText = submitBtn.textContent;

                const formData = new FormData(formCreate);
                const name = (formData.get('nombre') || '').toString().trim();
                const lastName = (formData.get('apellidos') || '').toString().trim();
                const phone = (formData.get('telefono') || '').toString().trim();
                const email = (formData.get('email') || '').toString().trim();

                // Validación estricta previa: Nombre únicamente letras del abecedario (Subtarea 1)
                const nameLettersRegex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/;
                if (!name || !nameLettersRegex.test(name)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Nombre inválido',
                        text: 'El campo Nombre únicamente debe permitir letras del abecedario (sin números, caracteres especiales ni símbolos).',
                        background: '#1e293b',
                        color: '#ffffff',
                        confirmButtonColor: '#0055ff'
                    });
                    document.getElementById('workerName').focus();
                    return;
                }

                // Validación estricta previa: Apellidos únicamente letras del abecedario (Subtarea 1)
                if (!lastName || !nameLettersRegex.test(lastName)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Apellidos inválidos',
                        text: 'El campo Apellidos únicamente debe permitir letras del abecedario (sin números, caracteres especiales ni símbolos).',
                        background: '#1e293b',
                        color: '#ffffff',
                        confirmButtonColor: '#0055ff'
                    });
                    document.getElementById('workerLastName').focus();
                    return;
                }

                // Validación estricta previa: Bloqueo de duplicidad en nombre completo (tanto en el campo nombre como en el de apellidos)
                const duplicateWorker = checkWorkerFullNameDuplicate(name, lastName);
                if (duplicateWorker) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Nombre completo duplicado',
                        text: `Ya existe un trabajador registrado con el nombre completo "${name} ${lastName}". Debe ser único dentro del sistema (duplicidad en nombre y apellidos).`,
                        background: '#1e293b',
                        color: '#ffffff',
                        confirmButtonColor: '#0055ff'
                    });
                    document.getElementById('workerName').focus();
                    return;
                }

                // Validación estricta previa: Teléfono exactamente 10 dígitos numéricos
                if (!/^[0-9]{10}$/.test(phone)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Teléfono inválido',
                        text: 'El número de teléfono debe contener exactamente 10 dígitos numéricos (sin letras ni caracteres especiales).',
                        background: '#1e293b',
                        color: '#ffffff',
                        confirmButtonColor: '#0055ff'
                    });
                    document.getElementById('workerPhone').focus();
                    return;
                }

                // Validación estricta previa: Teléfono único dentro del sistema (Subtarea 2)
                const duplicatePhoneCard = Array.from(document.querySelectorAll('.worker-profile-card'))
                    .some(card => (card.getAttribute('data-phone') || '').trim() === phone);

                if (duplicatePhoneCard) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Teléfono ya registrado',
                        text: `El número de teléfono "${phone}" ya se encuentra registrado por otro trabajador. Debe ser único en el sistema.`,
                        background: '#1e293b',
                        color: '#ffffff',
                        confirmButtonColor: '#0055ff'
                    });
                    document.getElementById('workerPhone').focus();
                    return;
                }

                // Validación estricta previa: Correo electrónico válido con dominio
                const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                if (!emailRegex.test(email)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Correo inválido',
                        text: 'Introduce un correo electrónico válido (ejemplo: usuario@dominio.com). Debe contener un dominio y extensión válidos.',
                        background: '#1e293b',
                        color: '#ffffff',
                        confirmButtonColor: '#0055ff'
                    });
                    document.getElementById('workerEmail').focus();
                    return;
                }

                // Validación estricta previa: Años de experiencia no negativos
                const expVal = parseInt(formData.get('experiencia'), 10);
                if (isNaN(expVal) || expVal < 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Experiencia inválida',
                        text: 'Los años de experiencia deben ser un número positivo igual o mayor a 0 (no se permiten números negativos).',
                        background: '#1e293b',
                        color: '#ffffff',
                        confirmButtonColor: '#0055ff'
                    });
                    document.getElementById('workerExperience').focus();
                    return;
                }

                // Validación estricta previa: Contraseña obligatoria (al menos 8 caracteres, 1 mayúscula, 1 número y 1 carácter especial)
                const createPass = (formData.get('password') || '').toString().trim();
                if (!createPass || createPass.length < 8) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Contraseña requerida',
                        text: 'La contraseña debe tener al menos 8 caracteres.',
                        background: '#1e293b',
                        color: '#ffffff',
                        confirmButtonColor: '#0055ff'
                    });
                    document.getElementById('workerPassword').focus();
                    return;
                }
                if (!/[A-Z]/.test(createPass) || !/[0-9]/.test(createPass) || !/[\W_]/.test(createPass)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Requisitos de contraseña',
                        text: 'La contraseña debe contener al menos una letra mayúscula, un número y un carácter especial.',
                        background: '#1e293b',
                        color: '#ffffff',
                        confirmButtonColor: '#0055ff'
                    });
                    document.getElementById('workerPassword').focus();
                    return;
                }

                submitBtn.disabled = true;
                submitBtn.textContent = 'Guardando en BD...';

                try {
                    const response = await fetch(formCreate.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        body: formData
                    });

                    const result = await response.json();

                    if (response.ok && result.success) {
                        closeModal('modalCreateWorker');
                        formCreate.reset();
                        const preview = document.getElementById('workerPhotoPreview');
                        const placeholder = document.getElementById('photoPlaceholder');
                        if (preview) preview.style.display = 'none';
                        if (placeholder) placeholder.style.display = 'block';

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: result.message || 'Trabajador registrado exitosamente en la base de datos.',
                            showConfirmButton: false,
                            timer: 3500,
                            timerProgressBar: true,
                            background: '#10b981',
                            color: '#ffffff',
                            iconColor: '#ffffff'
                        });

                        setTimeout(() => window.location.reload(), 1200);
                    } else {
                        let errorMsg = result.message || 'Ocurrió un error al registrar el trabajador.';
                        if (result.errors) {
                            errorMsg = Object.values(result.errors).flat().join('<br>');
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error de validación',
                            html: errorMsg,
                            background: '#1e293b',
                            color: '#ffffff',
                            confirmButtonColor: '#ef4444'
                        });
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de conexión',
                        text: 'No se pudo comunicar con el servidor.',
                        background: '#1e293b',
                        color: '#ffffff',
                        confirmButtonColor: '#ef4444'
                    });
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
            });
        }

        // Envío AJAX funcional para la modificación de trabajador (Subtarea 5)
        const formEdit = document.getElementById('formEditWorker');
        if (formEdit) {
            formEdit.addEventListener('submit', async function (e) {
                e.preventDefault();
                const workerId = document.getElementById('editWorkerId').value;
                if (!workerId) return;

                const submitBtn = formEdit.querySelector('.btn-submit-action');
                const originalText = submitBtn.textContent;

                const formData = new FormData(formEdit);
                const name = (formData.get('nombre') || '').toString().trim();
                const lastName = (formData.get('apellidos') || '').toString().trim();
                const phone = (formData.get('telefono') || '').toString().trim();
                const email = (formData.get('email') || '').toString().trim();

                const nameLettersRegex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/;

                // Validación estricta previa: Nombre únicamente letras del abecedario (Subtarea 1)
                if (!name || !nameLettersRegex.test(name)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Nombre inválido',
                        text: 'El campo Nombre únicamente debe permitir letras del abecedario (sin números, caracteres especiales ni símbolos).',
                        background: '#1e293b',
                        color: '#ffffff',
                        confirmButtonColor: '#0055ff'
                    });
                    document.getElementById('editWorkerName').focus();
                    return;
                }

                // Validación estricta previa: Apellidos únicamente letras del abecedario (Subtarea 1)
                if (!lastName || !nameLettersRegex.test(lastName)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Apellidos inválidos',
                        text: 'El campo Apellidos únicamente debe permitir letras del abecedario (sin números, caracteres especiales ni símbolos).',
                        background: '#1e293b',
                        color: '#ffffff',
                        confirmButtonColor: '#0055ff'
                    });
                    document.getElementById('editWorkerLastName').focus();
                    return;
                }

                // Validación estricta previa: Bloqueo de duplicidad en nombre completo (nombre y apellidos) excluyendo al trabajador actual
                const duplicateWorker = checkWorkerFullNameDuplicate(name, lastName, workerId);
                if (duplicateWorker) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Nombre completo duplicado',
                        text: `Ya existe otro trabajador registrado con el nombre completo "${name} ${lastName}". Debe ser único dentro del sistema (duplicidad en nombre y apellidos).`,
                        background: '#1e293b',
                        color: '#ffffff',
                        confirmButtonColor: '#0055ff'
                    });
                    document.getElementById('editWorkerName').focus();
                    return;
                }

                // Validación estricta previa: Teléfono exactamente 10 dígitos numéricos
                if (!/^[0-9]{10}$/.test(phone)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Teléfono inválido',
                        text: 'El número de teléfono debe contener exactamente 10 dígitos numéricos (sin letras ni caracteres especiales).',
                        background: '#1e293b',
                        color: '#ffffff',
                        confirmButtonColor: '#0055ff'
                    });
                    document.getElementById('editWorkerPhone').focus();
                    return;
                }

                // Validación estricta previa: Teléfono único (excluyendo el trabajador actual)
                const duplicatePhoneCard = Array.from(document.querySelectorAll('.worker-profile-card'))
                    .some(card => {
                        const cardId = card.id.replace('worker-card-', '');
                        if (cardId === workerId.toString()) return false;
                        return (card.getAttribute('data-phone') || '').trim() === phone;
                    });

                if (duplicatePhoneCard) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Teléfono ya registrado',
                        text: `El número de teléfono "${phone}" ya se encuentra registrado por otro trabajador. Debe ser único en el sistema.`,
                        background: '#1e293b',
                        color: '#ffffff',
                        confirmButtonColor: '#0055ff'
                    });
                    document.getElementById('editWorkerPhone').focus();
                    return;
                }

                // Validación estricta previa: Correo electrónico válido con dominio
                const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                if (!emailRegex.test(email)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Correo inválido',
                        text: 'Introduce un correo electrónico válido (ejemplo: usuario@dominio.com). Debe contener un dominio y extensión válidos.',
                        background: '#1e293b',
                        color: '#ffffff',
                        confirmButtonColor: '#0055ff'
                    });
                    document.getElementById('editWorkerEmail').focus();
                    return;
                }

                // Validación estricta previa: Años de experiencia no negativos
                const expVal = parseInt(formData.get('experiencia'), 10);
                if (isNaN(expVal) || expVal < 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Experiencia inválida',
                        text: 'Los años de experiencia deben ser un número positivo igual o mayor a 0 (no se permiten números negativos).',
                        background: '#1e293b',
                        color: '#ffffff',
                        confirmButtonColor: '#0055ff'
                    });
                    document.getElementById('editWorkerExperience').focus();
                    return;
                }

                // Validación estricta previa: Contraseña en edición (opcional, pero si se escribe debe cumplir reglas)
                const editPassword = (formData.get('password') || '').toString().trim();
                if (editPassword.length > 0) {
                    if (editPassword.length < 8) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Contraseña muy corta',
                            text: 'La nueva contraseña debe tener al menos 8 caracteres.',
                            background: '#1e293b',
                            color: '#ffffff',
                            confirmButtonColor: '#0055ff'
                        });
                        document.getElementById('editWorkerPassword').focus();
                        return;
                    }
                    if (!/[A-Z]/.test(editPassword) || !/[0-9]/.test(editPassword) || !/[\W_]/.test(editPassword)) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Requisitos de contraseña',
                            text: 'La nueva contraseña debe contener al menos una letra mayúscula, un número y un carácter especial.',
                            background: '#1e293b',
                            color: '#ffffff',
                            confirmButtonColor: '#0055ff'
                        });
                        document.getElementById('editWorkerPassword').focus();
                        return;
                    }
                } else {
                    // Si se deja vacía, remover del formData para conservar la contraseña actual
                    formData.delete('password');
                }

                submitBtn.disabled = true;
                submitBtn.textContent = 'Actualizando en BD...';

                try {
                    // Spoofing PUT en FormData para soportar multipart/form-data en PHP/Laravel
                    formData.set('_method', 'PUT');

                    const response = await fetch(`/admin/trabajadores/${workerId}`, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        body: formData
                    });

                    const result = await response.json();

                    if (response.ok && result.success) {
                        closeModal('modalEditWorker');

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: result.message || 'Información del trabajador actualizada exitosamente en la base de datos.',
                            showConfirmButton: false,
                            timer: 3500,
                            timerProgressBar: true,
                            background: '#10b981',
                            color: '#ffffff',
                            iconColor: '#ffffff'
                        });

                        setTimeout(() => window.location.reload(), 1200);
                    } else {
                        let errorMsg = result.message || 'Ocurrió un error al actualizar el trabajador.';
                        if (result.errors) {
                            errorMsg = Object.values(result.errors).flat().join('<br>');
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error de validación',
                            html: errorMsg,
                            background: '#1e293b',
                            color: '#ffffff',
                            confirmButtonColor: '#ef4444'
                        });
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de conexión',
                        text: 'No se pudo comunicar con el servidor.',
                        background: '#1e293b',
                        color: '#ffffff',
                        confirmButtonColor: '#ef4444'
                    });
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
            });
        }

        // Envío AJAX funcional para asociar servicios (Subtarea 7)
        const formServices = document.getElementById('formServicesWorker');
        if (formServices) {
            formServices.addEventListener('submit', async function (e) {
                e.preventDefault();
                const workerId = document.getElementById('servicesWorkerId').value;
                if (!workerId) return;

                const submitBtn = formServices.querySelector('.btn-submit-action');
                const originalText = submitBtn.textContent;

                const selectedServices = Array.from(document.querySelectorAll('.service-checkbox:checked')).map(cb => parseInt(cb.value));

                submitBtn.disabled = true;
                submitBtn.textContent = 'Guardando servicios...';

                try {
                    const response = await fetch(`/admin/trabajadores/${workerId}/servicios`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ servicios: selectedServices })
                    });

                    const result = await response.json();

                    if (response.ok && result.success) {
                        closeModal('modalServices');

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: result.message || 'Servicios asociados exitosamente.',
                            showConfirmButton: false,
                            timer: 3500,
                            timerProgressBar: true,
                            background: '#10b981',
                            color: '#ffffff',
                            iconColor: '#ffffff'
                        });

                        setTimeout(() => window.location.reload(), 1200);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error al asociar servicios',
                            text: result.message || 'Ocurrió un error al guardar los servicios.',
                            background: '#1e293b',
                            color: '#ffffff',
                            confirmButtonColor: '#ef4444'
                        });
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de conexión',
                        text: 'No se pudo comunicar con el servidor.',
                        background: '#1e293b',
                        color: '#ffffff',
                        confirmButtonColor: '#ef4444'
                    });
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
            });
        }

        // Alternar Estado / Bloqueo de Disponibilidad de Especialista (AJAX)
        async function toggleWorkerStatus(id, nombre, currentActive) {
            const isActivating = !currentActive;
            const actionTitle = isActivating ? `¿Activar a ${nombre}?` : `¿Desactivar a ${nombre}?`;
            const actionDesc = isActivating 
                ? 'El especialista volverá a estar activo y disponible para agendar citas en el sistema.' 
                : 'Se bloqueará la disponibilidad del especialista y no podrá recibir citas ni agendamientos mientras permanezca inactivo.';
            const confirmBtnText = isActivating ? 'Sí, activar' : 'Sí, desactivar';
            const confirmBtnColor = isActivating ? '#10b981' : '#ef4444';

            const result = await Swal.fire({
                title: actionTitle,
                text: actionDesc,
                icon: isActivating ? 'question' : 'warning',
                showCancelButton: true,
                confirmButtonColor: confirmBtnColor,
                cancelButtonColor: '#64748b',
                confirmButtonText: confirmBtnText,
                cancelButtonText: 'Cancelar',
                background: '#1e293b',
                color: '#ffffff'
            });

            if (!result.isConfirmed) return;

            try {
                const response = await fetch(`/admin/trabajadores/${id}/toggle-status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: data.message || `Estado de ${nombre} actualizado.`,
                        showConfirmButton: false,
                        timer: 2500,
                        timerProgressBar: true,
                        background: '#10b981',
                        color: '#ffffff',
                        iconColor: '#ffffff'
                    });

                    setTimeout(() => window.location.reload(), 700);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'No se pudo cambiar el estado del especialista.',
                        background: '#1e293b',
                        color: '#ffffff',
                        confirmButtonColor: '#ef4444'
                    });
                }
            } catch (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo comunicar con el servidor.',
                    background: '#1e293b',
                    color: '#ffffff',
                    confirmButtonColor: '#ef4444'
                });
            }
        }

        // Eliminar Trabajador con Confirmación y Limpieza UI (Subtarea 4)
        async function confirmDeleteWorker(id, nombre) {
            const result = await Swal.fire({
                title: '¿Eliminar especialista?',
                html: `¿Estás seguro de que deseas eliminar permanentemente a <strong>"${nombre}"</strong>?<br><span style="font-size: 13px; color: #ef4444; margin-top: 6px; display: inline-block;">Esta acción no se puede deshacer y desvinculará sus servicios asociados.</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, eliminar definitivamente',
                cancelButtonText: 'Cancelar',
                background: '#1e293b',
                color: '#ffffff',
                reverseButtons: true,
                focusCancel: true
            });

            if (!result.isConfirmed) return;

            try {
                const response = await fetch(`/admin/trabajadores/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    const card = document.getElementById(`worker-card-${id}`);
                    const row = document.getElementById(`worker-row-${id}`);

                    if (card) {
                        card.style.transition = 'all 0.3s ease';
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.9)';
                        setTimeout(() => card.remove(), 300);
                    }
                    if (row) {
                        row.style.transition = 'all 0.3s ease';
                        row.style.opacity = '0';
                        setTimeout(() => row.remove(), 300);
                    }

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: data.message || `Especialista "${nombre}" eliminado exitosamente.`,
                        showConfirmButton: false,
                        timer: 2500,
                        timerProgressBar: true,
                        background: '#10b981',
                        color: '#ffffff',
                        iconColor: '#ffffff'
                    });

                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al eliminar',
                        text: data.message || 'No se pudo eliminar al especialista seleccionado.',
                        background: '#1e293b',
                        color: '#ffffff',
                        confirmButtonColor: '#ef4444'
                    });
                }
            } catch (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo comunicar con el servidor.',
                    background: '#1e293b',
                    color: '#ffffff',
                    confirmButtonColor: '#ef4444'
                });
            }
        }

        // Desactivar el menú emergente / rueda nativa del navegador en los campos de hora
        document.querySelectorAll('input[type="time"]').forEach(function (input) {
            input.showPicker = function () {};
            input.addEventListener('keydown', function (e) {
                if (e.altKey && e.key === 'ArrowDown') {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>
