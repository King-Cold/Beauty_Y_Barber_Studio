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



        /* Botones de acción en tarjeta */
        .worker-card-actions {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
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
                    <input type="text" class="workers-search-input" id="searchWorkerInput" placeholder="Buscar por nombre, teléfono o email..." onkeyup="filterWorkers()">
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
                <article class="worker-profile-card" data-status="{{ $trabajador->activo ? 'active' : 'inactive' }}" data-name="{{ $trabajador->nombre_completo }}" data-email="{{ $trabajador->email }}" data-phone="{{ $trabajador->telefono }}" data-address="{{ $trabajador->direccion }}">
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
                        <div>
                            <div class="addon-heading">
                                <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="6" cy="6" r="3"></circle>
                                    <circle cx="6" cy="18" r="3"></circle>
                                    <line x1="20" y1="4" x2="8.12" y2="15.88"></line>
                                </svg>
                                Servicios Asociados ({{ $trabajador->servicios->count() }})
                            </div>
                            <div class="chips-container">
                                @forelse($trabajador->servicios as $servicio)
                                    <span class="chip-service">{{ $servicio->name }}</span>
                                @empty
                                    <span class="chip-service" style="opacity: 0.6; border-style: dashed;">Sin servicios asignados</span>
                                @endforelse
                            </div>
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
                        <tr class="worker-table-row" data-status="{{ $trabajador->activo ? 'active' : 'inactive' }}" data-name="{{ $trabajador->nombre_completo }}" data-email="{{ $trabajador->email }}" data-phone="{{ $trabajador->telefono }}" data-address="{{ $trabajador->direccion }}">
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
                            <input type="text" id="workerName" name="nombre" class="form-control" placeholder="Ej. Carlos" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="workerLastName">Apellidos <span class="required">*</span></label>
                            <input type="text" id="workerLastName" name="apellidos" class="form-control" placeholder="Ej. Gómez Ruiz" required>
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
                            <input type="text" id="editWorkerName" name="nombre" class="form-control" placeholder="Ej. Carlos" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="editWorkerLastName">Apellidos <span class="required">*</span></label>
                            <input type="text" id="editWorkerLastName" name="apellidos" class="form-control" placeholder="Ej. Gómez Ruiz" required>
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
        });

        // Funciones de Modales para Trabajadores
        function openModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.add('open');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.remove('open');
                document.body.style.overflow = '';
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

            // Filtrar tarjetas
            cards.forEach(card => {
                const name = (card.getAttribute('data-name') || '').toLowerCase();
                const email = (card.getAttribute('data-email') || '').toLowerCase();
                const phone = (card.getAttribute('data-phone') || '').toLowerCase();
                const address = (card.getAttribute('data-address') || '').toLowerCase();
                const status = card.getAttribute('data-status');

                const matchesQuery = !query || name.includes(query) || email.includes(query) || phone.includes(query) || address.includes(query);
                const matchesStatus = currentStatusFilter === 'all' || status === currentStatusFilter;

                if (matchesQuery && matchesStatus) {
                    card.style.display = 'flex';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Filtrar filas de tabla
            rows.forEach(row => {
                const name = (row.getAttribute('data-name') || '').toLowerCase();
                const email = (row.getAttribute('data-email') || '').toLowerCase();
                const phone = (row.getAttribute('data-phone') || '').toLowerCase();
                const address = (row.getAttribute('data-address') || '').toLowerCase();
                const status = row.getAttribute('data-status');

                const matchesQuery = !query || name.includes(query) || email.includes(query) || phone.includes(query) || address.includes(query);
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
            if (input.value.length === 10) {
                input.style.borderColor = '#10b981';
                if (helpEl) {
                    helpEl.textContent = '✓ 10 dígitos numéricos correctos';
                    helpEl.style.color = '#10b981';
                }
            } else if (input.value.length > 0) {
                input.style.borderColor = '#f59e0b';
                if (helpEl) {
                    helpEl.textContent = `Faltan ${10 - input.value.length} dígitos (debe tener 10 exactos)`;
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

        // Envío AJAX funcional para el registro de trabajador (Subtarea 3)
        const formCreate = document.getElementById('formCreateWorker');
        if (formCreate) {
            formCreate.addEventListener('submit', async function (e) {
                e.preventDefault();
                const submitBtn = formCreate.querySelector('.btn-submit-action');
                const originalText = submitBtn.textContent;

                const formData = new FormData(formCreate);
                const phone = (formData.get('telefono') || '').toString().trim();
                const email = (formData.get('email') || '').toString().trim();

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
                const phone = (formData.get('telefono') || '').toString().trim();
                const email = (formData.get('email') || '').toString().trim();

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
    </script>
</body>
</html>
