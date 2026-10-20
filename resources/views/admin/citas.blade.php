<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Beauty & Barber Studio - Calendario de Citas (Administración)</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* ===================================================
           SISTEMA DE DISEÑO BASE DEL PANEL DE ADMINISTRACIÓN
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
            --bg-card: #0a1124;
            
            --border-subtle: rgba(255, 255, 255, 0.06);
            --border-strong: rgba(255, 255, 255, 0.12);

            --barber-red: #7f1d1d;
            --barber-red-light: rgba(127, 29, 29, 0.18);
            --barber-blue: #1e3a8a;
            --barber-blue-light: #60a5fa; 
            --barber-blue-pale: rgba(30, 58, 138, 0.18);

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
        .skip-link:focus { top: 20px; }

        /* HEADER */
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

        .brand-section {
            display: flex;
            align-items: center;
            gap: 16px;
            text-decoration: none;
            color: inherit;
        }

        .brand-logo-img { height: 38px; width: auto; display: block; }

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

        .user-nav { display: flex; align-items: center; gap: 16px; }
        .profile-dropdown-wrapper { position: relative; }
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

        /* CONTENIDO PRINCIPAL */
        .main-content {
            flex: 1;
            padding: 40px;
            background-color: var(--bg-canvas);
            min-width: 0;
            overflow-y: auto;
        }

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

        /* SELECTOR DE VISTAS (CALENDARIO / LISTADO) */
        .btn-admin-agendar-cta {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 22px;
            background: linear-gradient(135deg, var(--barber-blue) 0%, #1d4ed8 100%);
            color: #ffffff !important;
            font-size: 13.5px;
            font-weight: 700;
            border-radius: 10px;
            text-decoration: none;
            transition: all 0.25s var(--ease-fluid);
            border: 1px solid rgba(37, 99, 235, 0.4);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.35);
            white-space: nowrap;
        }
        .btn-admin-agendar-cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.4);
            border-color: #60a5fa;
            color: #ffffff !important;
        }

        .admin-view-switcher {
            display: inline-flex;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--border-subtle);
            border-radius: 12px;
            padding: 4px;
            margin-bottom: 0;
            gap: 4px;
        }

        .view-tab-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: transparent;
            border: none;
            border-radius: 8px;
            color: var(--text-muted);
            font-size: 13.5px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .view-tab-btn:hover {
            color: var(--text-white);
            background: rgba(255, 255, 255, 0.05);
        }

        .view-tab-btn.active {
            background: #1e3a8a;
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .tab-badge {
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff;
        }

        /* FILTROS Y TABLA DEL LISTADO */
        .admin-filtros-card {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .admin-filtros-form {
            display: grid;
            grid-template-columns: 2fr 1.2fr 1.2fr 1fr 1fr auto;
            gap: 14px;
            align-items: flex-end;
        }

        @media (max-width: 1200px) {
            .admin-filtros-form {
                grid-template-columns: 1fr 1fr 1fr;
            }
        }

        @media (max-width: 768px) {
            .admin-filtros-form {
                grid-template-columns: 1fr;
            }
        }

        .admin-filtro-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .admin-filtro-label {
            font-size: 11.5px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .admin-filtro-input,
        .admin-filtro-select {
            width: 100%;
            padding: 10px 14px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--border-subtle);
            border-radius: 8px;
            color: var(--text-white);
            font-size: 13.5px;
            font-family: inherit;
            outline: none;
            transition: all 0.2s ease;
        }

        .admin-filtro-input:focus,
        .admin-filtro-select:focus {
            border-color: var(--barber-blue-light);
            background: rgba(37, 99, 235, 0.08);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
        }

        .admin-filtro-select option {
            background-color: #0a1124;
            color: #ffffff;
        }

        .admin-filtros-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-admin-filtrar {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 18px;
            background: #1e40af;
            color: #ffffff;
            border: 1px solid rgba(147, 197, 253, 0.28);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.35);
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-admin-filtrar:hover {
            background: #1d4ed8;
            color: #ffffff;
            border-color: rgba(147, 197, 253, 0.45);
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
        }

        .btn-admin-limpiar {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 14px;
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-muted);
            border: 1px solid var(--border-subtle);
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-admin-limpiar:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
        }

        .admin-panel-card {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .admin-table-wrapper {
            overflow-x: auto;
        }

        .admin-citas-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .admin-citas-table th {
            padding: 12px 12px;
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid var(--border-subtle);
            background: rgba(255, 255, 255, 0.02);
            white-space: nowrap;
        }

        .admin-citas-table td {
            padding: 11px 12px;
            font-size: 13px;
            color: var(--text-main);
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            vertical-align: middle;
        }

        .admin-citas-table tr:hover td {
            background: rgba(255, 255, 255, 0.02);
        }

        .admin-folio-tag {
            font-family: monospace;
            font-size: 12px;
            font-weight: 700;
            color: #93c5fd;
            background: rgba(37, 99, 235, 0.12);
            border: 1px solid rgba(37, 99, 235, 0.25);
            padding: 3px 8px;
            border-radius: 6px;
            white-space: nowrap;
        }

        .admin-cliente-cell {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .admin-cliente-cell strong {
            color: #ffffff;
            font-size: 14px;
        }

        .admin-cliente-subtext {
            font-size: 12px;
            color: var(--text-muted);
        }

        .admin-servicio-cell {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .admin-servicio-tag {
            display: inline-block;
            font-size: 10.5px;
            text-transform: uppercase;
            padding: 2px 6px;
            border-radius: 4px;
            background: rgba(255, 255, 255, 0.06);
            color: #94a3b8;
            width: fit-content;
        }

        .admin-horario-cell {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .admin-horario-cell strong {
            color: #ffffff;
            font-size: 13.5px;
        }

        .admin-duracion-subtext {
            font-size: 11.5px;
            color: var(--text-muted);
        }

        .admin-precio-highlight {
            color: #60a5fa;
            font-weight: 800;
            font-size: 14px;
            white-space: nowrap;
        }

        .pago-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 7px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }
        .pago-badge.pendiente {
            background: rgba(245, 158, 11, 0.15);
            color: #f59e0b;
            border: 1px solid rgba(245, 158, 11, 0.35);
        }
        .pago-badge.pagado {
            background: rgba(16, 185, 129, 0.15);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.35);
        }

        .admin-btn-ver-detalle {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-white);
            border: 1px solid var(--border-subtle);
            border-radius: 8px;
            font-size: 12.5px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
        }
        .admin-btn-ver-detalle:hover {
            background: rgba(37, 99, 235, 0.2);
            border-color: rgba(37, 99, 235, 0.4);
            color: #93c5fa;
            transform: translateY(-1px);
        }
        .admin-btn-ver-detalle * {
            pointer-events: none;
        }

        /* CONTENEDOR DE ACCIONES ESCORADO Y COMPACTO */
        .admin-table-actions-group {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            justify-content: flex-end;
            white-space: nowrap;
        }

        /* BOTONES OPERATIVOS PRINCIPALES */
        .admin-btn-iniciar {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 6px 11px;
            background: rgba(245, 158, 11, 0.14);
            color: #f59e0b;
            border: 1px solid rgba(245, 158, 11, 0.35);
            border-radius: 7px;
            font-size: 11.5px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
        }
        .admin-btn-iniciar:hover {
            background: #d97706;
            color: #ffffff;
            border-color: #d97706;
            transform: translateY(-1px);
        }

        .admin-btn-finalizar {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 6px 11px;
            background: rgba(16, 185, 129, 0.14);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.35);
            border-radius: 7px;
            font-size: 11.5px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
        }
        .admin-btn-finalizar:hover {
            background: #059669;
            color: #ffffff;
            border-color: #059669;
            transform: translateY(-1px);
        }

        .admin-btn-checkin {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 6px 10px;
            background: rgba(16, 185, 129, 0.12);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 7px;
            font-size: 11.5px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
        }
        .admin-btn-checkin:hover {
            background: #059669;
            color: #ffffff;
            border-color: #059669;
            transform: translateY(-1px);
        }

        .admin-btn-reagendar {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 6px 10px;
            background: rgba(37, 99, 235, 0.12);
            color: #60a5fa;
            border: 1px solid rgba(37, 99, 235, 0.3);
            border-radius: 7px;
            font-size: 11.5px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
        }
        .admin-btn-reagendar:hover {
            background: #1e3a8a;
            color: #ffffff;
            border-color: #1e3a8a;
            transform: translateY(-1px);
        }

        /* MINI-BARRA DE HERRAMIENTAS RÁPIDAS (Detalles, Editar, Cancelar) */
        .admin-table-tool-group {
            display: inline-flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 8px;
            padding: 2px;
            gap: 2px;
        }

        .admin-btn-tool-icon {
            width: 29px;
            height: 29px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            border: none;
            border-radius: 6px;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.18s ease;
        }
        .admin-btn-tool-icon:hover {
            color: #ffffff;
            transform: scale(1.08);
        }
        .admin-btn-tool-icon.tool-detalles:hover {
            background: rgba(37, 99, 235, 0.2);
            color: #93c5fd;
        }
        .admin-btn-tool-icon.tool-editar:hover {
            background: rgba(37, 99, 235, 0.2);
            color: #60a5fa;
        }
        .admin-btn-tool-icon.tool-cancelar:hover {
            background: rgba(220, 38, 38, 0.25);
            color: #fca5a5;
        }

        .admin-badge-checkin {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 7px;
            background: rgba(16, 185, 129, 0.12);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.28);
            border-radius: 12px;
            font-size: 10.5px;
            font-weight: 700;
            margin-top: 4px;
            white-space: nowrap;
        }

        /* BADGES */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 700;
            text-transform: capitalize;
            white-space: nowrap;
        }

        .status-badge::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            display: inline-block;
        }

        .status-badge.confirmada {
            background: rgba(16, 185, 129, 0.12);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.28);
        }
        .status-badge.confirmada::before { background: #10b981; }

        .status-badge.cancelada {
            background: rgba(220, 38, 38, 0.12);
            color: #f87171;
            border: 1px solid rgba(220, 38, 38, 0.28);
        }
        .status-badge.cancelada::before { background: #f87171; }

        .status-badge.completada {
            background: rgba(13, 148, 136, 0.12);
            color: #2dd4bf;
            border: 1px solid rgba(13, 148, 136, 0.28);
        }
        .status-badge.completada::before { background: #2dd4bf; }

        .status-badge.en_atencion,
        .status-badge.en_proceso {
            background: rgba(37, 99, 235, 0.12);
            color: #60a5fa;
            border: 1px solid rgba(37, 99, 235, 0.28);
        }
        .status-badge.en_atencion::before,
        .status-badge.en_proceso::before { background: #60a5fa; }

        .status-badge.vencida {
            background: rgba(245, 158, 11, 0.12);
            color: #f59e0b;
            border: 1px solid rgba(245, 158, 11, 0.28);
        }
        .status-badge.vencida::before { background: #f59e0b; }

        /* ==========================================================================
           PANEL DE CORTE DE CAJA Y CONTROL FINANCIERO (Subtarea 24.4)
           ========================================================================== */
        .corte-caja-container {
            background: rgba(10, 17, 36, 0.65);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 18px;
            padding: 20px 22px;
            margin-bottom: 24px;
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        .corte-caja-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
            padding-bottom: 14px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            flex-wrap: wrap;
        }

        .corte-caja-title-box {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .corte-caja-icon-badge {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.28);
            color: #10b981;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .corte-caja-title {
            font-size: 16px;
            font-weight: 800;
            color: #ffffff;
            margin: 0;
            letter-spacing: -0.01em;
        }

        .corte-caja-sub {
            font-size: 12px;
            color: #94a3b8;
            display: block;
            margin-top: 2px;
        }

        .corte-caja-date-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11.5px;
            font-weight: 600;
            color: #94a3b8;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            padding: 5px 12px;
            border-radius: 20px;
        }

        .corte-caja-cards-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 18px;
        }

        @media (max-width: 820px) {
            .corte-caja-cards-grid {
                grid-template-columns: 1fr;
            }
        }

        .corte-card {
            background: rgba(15, 23, 42, 0.55);
            border: 1px solid rgba(255, 255, 255, 0.07);
            border-radius: 14px;
            padding: 18px 20px;
            display: flex;
            flex-direction: column;
            transition: all 0.22s ease;
            position: relative;
            overflow: hidden;
        }

        .corte-card:hover {
            transform: translateY(-2px);
            border-color: rgba(255, 255, 255, 0.15);
            box-shadow: 0 12px 24px -10px rgba(0, 0, 0, 0.6);
        }

        .corte-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            opacity: 0.8;
        }

        .corte-card-dia::before {
            background: linear-gradient(90deg, #10b981, transparent);
        }

        .corte-card-mes::before {
            background: linear-gradient(90deg, #38bdf8, transparent);
        }

        .corte-card-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .corte-card-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .corte-card-icon.icon-emerald {
            background: rgba(16, 185, 129, 0.12);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.28);
        }

        .corte-card-icon.icon-blue {
            background: rgba(37, 99, 235, 0.12);
            color: #60a5fa;
            border: 1px solid rgba(37, 99, 235, 0.28);
        }

        .corte-card-badge {
            font-size: 11px;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 6px;
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }

        .badge-emerald {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .badge-blue {
            background: rgba(37, 99, 235, 0.15);
            color: #93c5fd;
            border: 1px solid rgba(37, 99, 235, 0.3);
        }

        .corte-card-body {
            display: flex;
            flex-direction: column;
        }

        .corte-card-label {
            font-size: 12px;
            font-weight: 600;
            color: #94a3b8;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .corte-card-amount {
            font-size: 26px;
            font-weight: 900;
            letter-spacing: -0.02em;
            line-height: 1.15;
            margin-bottom: 12px;
        }

        .amount-emerald { color: #34d399; }
        .amount-blue    { color: #60a5fa; }

        .corte-card-footer {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            padding-top: 10px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        .corte-metric-tag {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11.5px;
            color: #cbd5e1;
            background: rgba(255, 255, 255, 0.04);
            padding: 3px 8px;
            border-radius: 6px;
        }

        .corte-metric-tag svg {
            color: #34d399;
        }
    </style>

    @stack('styles')
</head>
<body>
    <a href="#main-content" class="skip-link">Saltar al contenido principal</a>

    <!-- HEADER SUPERIOR -->
    <header class="admin-header" role="banner">
        <div class="header-top-accent-line" aria-hidden="true"></div>
        <div class="header-container">
            <div class="header-left">
                <button type="button" class="mobile-toggle-btn" id="mobileToggleBtn" aria-label="Abrir menú" aria-expanded="false" aria-controls="adminSidebar">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>
                <a href="{{ url('/admin') }}" class="brand-section" aria-label="Beauty & Barber Studio - Panel de Inicio">
                    <img src="{{ asset('images/logo.png') }}" alt="Logotipo Beauty & Barber Studio" class="brand-logo-img" onerror="this.style.display='none'">
                    <div class="brand-info">
                        <span class="brand-name">Beauty & Barber Studio</span>
                        <div class="brand-badge-container">
                            <span class="brand-role-badge">Panel Administrativo</span>
                        </div>
                    </div>
                </a>
            </div>

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
            <script>
                (function() {
                    try {
                        if (localStorage.getItem('admin_sidebar_collapsed') === 'true') {
                            document.getElementById('adminSidebar')?.classList.add('collapsed');
                        }
                    } catch (e) {}
                })();
            </script>
            
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

            <!-- Lista de Módulos Operativos -->
            <div class="sidebar-inner">
                <nav class="sidebar-nav" aria-label="Secciones del sistema">
                    
                    <!-- 1. Dashboard -->
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
                    <a href="{{ route('admin.clientes') }}" class="nav-item" title="Clientes Registrados">
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

                    <!-- 6. Citas (ACTIVO) -->
                    <a href="{{ route('admin.citas') }}" class="nav-item active" aria-current="page" title="Citas">
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

        <!-- ÁREA PRINCIPAL -->
        <main id="main-content" class="main-content" role="main">
            @php
                $tabActiva = (request('tab') === 'listado' || request()->filled('buscar') || request()->filled('trabajador_id') || (request()->filled('estado') && request('estado') !== 'todas') || request()->filled('fecha_desde') || request()->filled('fecha_hasta')) ? 'listado' : 'calendario';
            @endphp

            {{-- BLOQUE DE CORTE DE CAJA / INDICADORES FINANCIEROS (Subtarea 24.4) --}}
            <div class="corte-caja-container" id="adminCorteCajaContainer">
                <div class="corte-caja-header">
                    <div class="corte-caja-title-box">
                        <div class="corte-caja-icon-badge">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                                <line x1="12" y1="1" x2="12" y2="23"></line>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="corte-caja-title">Corte de Caja y Control de Cobros</h2>
                            <span class="corte-caja-sub">Indicadores de ingresos devengados en el salón (Modo Auditoría)</span>
                        </div>
                    </div>
                    <div class="corte-caja-date-badge">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        <span>Zona Horaria Oficial: {{ $corteCaja['zona_horaria'] ?? 'America/Mexico_City' }}</span>
                    </div>
                </div>

                <div class="corte-caja-cards-grid">
                    {{-- TARJETA 1: INGRESOS DEL DÍA --}}
                    <div class="corte-card corte-card-dia">
                        <div class="corte-card-top">
                            <div class="corte-card-icon icon-emerald">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="22" height="22">
                                    <rect x="2" y="5" width="20" height="14" rx="2"></rect>
                                    <line x1="2" y1="10" x2="22" y2="10"></line>
                                </svg>
                            </div>
                            <div class="corte-card-badge badge-emerald">
                                <span>Ingresos del Día</span>
                            </div>
                        </div>
                        <div class="corte-card-body">
                            <span class="corte-card-label">Ingresos del Día</span>
                            <strong class="corte-card-amount amount-emerald">{{ $corteCaja['dia']['total_ingresos_mxn'] ?? (($corteCaja['dia']['total_ingresos_formateado'] ?? '$0.00') . ' MXN') }}</strong>
                            <div class="corte-card-footer">
                                <span class="corte-metric-tag">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="12" height="12">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                    <b>{{ $corteCaja['dia']['citas_pagadas_count'] ?? 0 }}</b> servicios cobrados hoy
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- TARJETA 2: INGRESOS DEL MES --}}
                    <div class="corte-card corte-card-mes">
                        <div class="corte-card-top">
                            <div class="corte-card-icon icon-blue">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="22" height="22">
                                    <line x1="12" y1="1" x2="12" y2="23"></line>
                                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                </svg>
                            </div>
                            <div class="corte-card-badge badge-blue">
                                <span>{{ $corteCaja['mes']['mes_nombre'] ?? 'Mes Actual' }} {{ $corteCaja['mes']['anio'] ?? '' }}</span>
                            </div>
                        </div>
                        <div class="corte-card-body">
                            <span class="corte-card-label">Ingresos del Mes</span>
                            <strong class="corte-card-amount amount-blue">{{ $corteCaja['mes']['total_ingresos_mxn'] ?? (($corteCaja['mes']['total_ingresos_formateado'] ?? '$0.00') . ' MXN') }}</strong>
                            <div class="corte-card-footer">
                                <span class="corte-metric-tag">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="12" height="12">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                    <b>{{ $corteCaja['mes']['citas_pagadas_count'] ?? 0 }}</b> servicios cobrados este mes
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ENCABEZADO Y SELECTOR DE VISTAS (ADMINISTRACIÓN) -->
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
                <div class="admin-view-switcher">
                    <button 
                        type="button" 
                        class="view-tab-btn {{ $tabActiva === 'calendario' ? 'active' : '' }}" 
                        id="tabBtnCalendario" 
                        onclick="cambiarVistaAdmin('calendario')"
                    >
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                            <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <span>Calendario Interactivo</span>
                    </button>
                    <button 
                        type="button" 
                        class="view-tab-btn {{ $tabActiva === 'listado' ? 'active' : '' }}" 
                        id="tabBtnListado" 
                        onclick="cambiarVistaAdmin('listado')"
                    >
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                            <line x1="8" y1="6" x2="21" y2="6"></line>
                            <line x1="8" y1="12" x2="21" y2="12"></line>
                            <line x1="8" y1="18" x2="21" y2="18"></line>
                            <line x1="3" y1="6" x2="3.01" y2="6"></line>
                            <line x1="3" y1="12" x2="3.01" y2="12"></line>
                            <line x1="3" y1="18" x2="3.01" y2="18"></line>
                        </svg>
                        <span>Listado de Citas</span>
                        <span class="tab-badge">{{ isset($citas) ? $citas->count() : 0 }}</span>
                    </button>
                </div>

                {{-- Insignia informativa de modo auditoría / solo consulta para el Administrador --}}
                <div style="display: inline-flex; align-items: center; gap: 8px; padding: 7px 15px; background: rgba(56, 189, 248, 0.08); border: 1px solid rgba(56, 189, 248, 0.25); border-radius: 20px; font-size: 12.5px; color: #7dd3fc; font-weight: 600;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    <span>Modo Consulta • Solo Visualización</span>
                </div>
            </div>

            <!-- VISTA 1: CALENDARIO INTERACTIVO (SOLO VISUALIZACIÓN) -->
            <div id="vistaCalendarioContainer" style="{{ $tabActiva === 'calendario' ? '' : 'display: none;' }}">
                @include('partials.calendario-citas', [
                    'apiEventosUrl' => route('admin.calendario.eventos'),
                    'apiDetalleUrlBase' => url('/admin/citas'),
                    'apiEstadoUrlBase' => null,
                    'nuevaCitaUrl' => null,
                    'trabajadores' => $trabajadores ?? collect(),
                    'servicios' => $servicios ?? collect(),
                    'rolUsuario' => 'admin',
                ])
            </div>

            <!-- VISTA 2: LISTADO Y CONSULTA DE CITAS -->
            <div id="vistaListadoContainer" style="{{ $tabActiva === 'listado' ? '' : 'display: none;' }}">
                {{-- Barra de filtros de búsqueda para el Administrador --}}
                <div class="admin-filtros-card">
                    <form action="{{ route('admin.citas') }}" method="GET" class="admin-filtros-form" id="adminFiltrosForm">
                        <input type="hidden" name="tab" value="listado">

                        {{-- 1. Búsqueda por cliente, folio, teléfono o correo --}}
                        <div class="admin-filtro-group">
                            <label class="admin-filtro-label" for="adminInputBuscar">Buscar Cliente o Folio</label>
                            <input 
                                type="text" 
                                id="adminInputBuscar" 
                                name="buscar" 
                                class="admin-filtro-input" 
                                placeholder="Nombre, teléfono, correo o folio..." 
                                value="{{ $filtros['buscar'] ?? '' }}"
                            >
                        </div>

                        {{-- 2. Filtro por Trabajador / Especialista --}}
                        <div class="admin-filtro-group">
                            <label class="admin-filtro-label" for="adminSelectTrabajador">Especialista</label>
                            <select id="adminSelectTrabajador" name="trabajador_id" class="admin-filtro-select">
                                <option value="">Todos los especialistas</option>
                                @foreach($trabajadores as $t)
                                    <option value="{{ $t->id }}" {{ (isset($filtros['trabajador_id']) && (string)$filtros['trabajador_id'] === (string)$t->id) ? 'selected' : '' }}>
                                        {{ $t->user->name ?? 'Especialista' }} {{ $t->user->apellidos ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 3. Filtro por Estado --}}
                        <div class="admin-filtro-group">
                            <label class="admin-filtro-label" for="adminSelectEstado">Estado</label>
                            <select id="adminSelectEstado" name="estado" class="admin-filtro-select">
                                <option value="todas" {{ (!isset($filtros['estado']) || $filtros['estado'] === 'todas') ? 'selected' : '' }}>Todos los estados</option>
                                <option value="confirmada" {{ (isset($filtros['estado']) && $filtros['estado'] === 'confirmada') ? 'selected' : '' }}>Confirmada</option>
                                <option value="en_proceso" {{ (isset($filtros['estado']) && in_array($filtros['estado'], ['en_proceso', 'en_atencion'])) ? 'selected' : '' }}>En proceso</option>
                                <option value="completada" {{ (isset($filtros['estado']) && $filtros['estado'] === 'completada') ? 'selected' : '' }}>Completada</option>
                                <option value="cancelada" {{ (isset($filtros['estado']) && $filtros['estado'] === 'cancelada') ? 'selected' : '' }}>Cancelada (Historial)</option>
                                <option value="vencida" {{ (isset($filtros['estado']) && $filtros['estado'] === 'vencida') ? 'selected' : '' }}>Vencida</option>
                            </select>
                        </div>

                        {{-- 4. Fecha Desde --}}
                        <div class="admin-filtro-group">
                            <label class="admin-filtro-label" for="adminInputFechaDesde">Desde</label>
                            <input 
                                type="date" 
                                id="adminInputFechaDesde" 
                                name="fecha_desde" 
                                class="admin-filtro-input" 
                                value="{{ $filtros['fecha_desde'] ?? '' }}"
                            >
                        </div>

                        {{-- 5. Fecha Hasta --}}
                        <div class="admin-filtro-group">
                            <label class="admin-filtro-label" for="adminInputFechaHasta">Hasta</label>
                            <input 
                                type="date" 
                                id="adminInputFechaHasta" 
                                name="fecha_hasta" 
                                class="admin-filtro-input" 
                                value="{{ $filtros['fecha_hasta'] ?? '' }}"
                            >
                        </div>

                        {{-- 6. Botones de Acción --}}
                        <div class="admin-filtros-actions">
                            <button type="submit" class="btn-admin-filtrar" title="Aplicar filtros de búsqueda">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="16" height="16">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                                <span>Filtrar</span>
                            </button>
                            <a href="{{ route('admin.citas', ['tab' => 'listado']) }}" class="btn-admin-limpiar" title="Restablecer filtros">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                                    <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path>
                                    <polyline points="3 3 3 8 8 8"></polyline>
                                </svg>
                                <span>Limpiar</span>
                            </a>
                        </div>
                    </form>
                </div>

                {{-- Barra de Resumen --}}
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; padding: 0 4px;">
                    <span style="font-size: 13px; color: var(--text-muted);">
                        Mostrando <strong style="color: #ffffff;">{{ isset($citas) ? $citas->count() : 0 }}</strong> {{ (isset($citas) && $citas->count() === 1) ? 'cita registrada' : 'citas registradas' }} en el sistema
                    </span>
                    @if(!empty($filtros['buscar']) || !empty($filtros['trabajador_id']) || (!empty($filtros['estado']) && $filtros['estado'] !== 'todas') || !empty($filtros['fecha_desde']) || !empty($filtros['fecha_hasta']))
                        <span style="font-size: 12px; color: #60a5fa; font-weight: 600;">
                            Filtros de consulta activos
                        </span>
                    @endif
                </div>

                {{-- Tabla de Citas para Administrador --}}
                <div class="admin-panel-card">
                    @if(!isset($citas) || $citas->isEmpty())
                        <div style="text-align: center; padding: 48px 24px;">
                            <div style="width: 56px; height: 56px; margin: 0 auto 16px; border-radius: 12px; background: rgba(255,255,255,0.04); display: flex; align-items: center; justify-content: center; color: var(--text-muted);">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="32" height="32">
                                    <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                            </div>
                            <h3 style="font-size: 18px; font-weight: 700; color: #ffffff; margin-bottom: 8px;">
                                No se encontraron citas con los criterios seleccionados
                            </h3>
                            <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 24px; max-width: 480px; margin-left: auto; margin-right: auto;">
                                Modifica o restablece los filtros de consulta para explorar el historial completo de citas del estudio.
                            </p>
                            <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
                                <a href="{{ route('admin.citas', ['tab' => 'listado']) }}" class="btn-admin-limpiar" style="color: #ffffff; background: rgba(255,255,255,0.08); display: inline-flex;">
                                    Restablecer filtros
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="admin-table-wrapper">
                            <table class="admin-citas-table">
                                <thead>
                                    <tr>
                                        <th style="width: 85px;">Folio</th>
                                        <th>Cliente</th>
                                        <th>Servicio</th>
                                        <th>Especialista</th>
                                        <th style="width: 95px;">Fecha</th>
                                        <th style="width: 120px;">Horario</th>
                                        <th style="width: 85px;">Precio</th>
                                        <th style="width: 105px;">Pago</th>
                                        <th style="width: 120px;">Estado</th>
                                        <th style="text-align: right; width: 130px;">Detalle</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($citas as $cita)
                                        <tr>
                                            {{-- Folio --}}
                                            <td>
                                                <span class="admin-folio-tag">{{ $cita->folio }}</span>
                                            </td>

                                            {{-- Cliente --}}
                                            <td>
                                                <div class="admin-cliente-cell">
                                                    <strong>
                                                        {{ $cita->cliente ? trim($cita->cliente->name . ' ' . ($cita->cliente->apellidos ?? '')) : 'Cliente #' . $cita->user_id }}
                                                    </strong>
                                                    <span class="admin-cliente-subtext">
                                                        {{ $cita->cliente?->telefono ?? $cita->cliente?->email ?? 'Sin contacto' }}
                                                    </span>
                                                </div>
                                            </td>

                                            {{-- Servicio --}}
                                            <td>
                                                <div class="admin-servicio-cell">
                                                    <strong>{{ $cita->service->name ?? 'Servicio general' }}</strong>
                                                    <span class="admin-servicio-tag">{{ ucfirst($cita->service->category ?? 'Estudio') }}</span>
                                                </div>
                                            </td>

                                            {{-- Especialista --}}
                                            <td>
                                                <span style="font-weight: 600; color: #f1f5f9;">
                                                    {{ $cita->trabajador && $cita->trabajador->user ? trim($cita->trabajador->user->name . ' ' . ($cita->trabajador->user->apellidos ?? '')) : 'Especialista #' . $cita->trabajador_id }}
                                                </span>
                                            </td>

                                            {{-- Fecha --}}
                                            <td>
                                                <span style="font-weight: 600;">
                                                    {{ $cita->fecha ? \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') : '—' }}
                                                </span>
                                            </td>

                                            {{-- Horario --}}
                                            <td>
                                                <div class="admin-horario-cell">
                                                    <strong>{{ substr($cita->hora_inicio, 0, 5) }} - {{ substr($cita->hora_fin, 0, 5) }}</strong>
                                                    <span class="admin-duracion-subtext">{{ $cita->duracion_minutos }} min</span>
                                                </div>
                                            </td>

                                            {{-- Precio --}}
                                            <td>
                                                <span class="admin-precio-highlight">{{ $cita->precio_formateado }}</span>
                                            </td>

                                            {{-- Pago --}}
                                            <td>
                                                @if($cita->estado_pago === 'pagado')
                                                    <span class="pago-badge pagado" title="Servicio liquidado y pagado">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="10" height="10">
                                                            <polyline points="20 6 9 17 4 12"></polyline>
                                                        </svg>
                                                        Pagado
                                                    </span>
                                                @else
                                                    <span class="pago-badge pendiente" title="Pago pendiente de liquidar en recepción">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="10" height="10">
                                                            <circle cx="12" cy="12" r="10"></circle>
                                                            <line x1="12" y1="8" x2="12" y2="12"></line>
                                                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                                        </svg>
                                                        Pendiente
                                                    </span>
                                                @endif
                                            </td>

                                            {{-- Estado --}}
                                            <td>
                                                <div style="display: flex; flex-direction: column; align-items: flex-start; gap: 4px;">
                                                    <span class="status-badge {{ $cita->estado_visual }}">
                                                        {{ $cita->estado_label }}
                                                    </span>
                                                    @if($cita->isCheckedIn())
                                                        <span class="admin-badge-checkin" title="Check-in registrado el {{ $cita->check_in_at?->format('d/m/Y') }} a las {{ $cita->check_in_hora }} hrs">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="10" height="10">
                                                                <polyline points="20 6 9 17 4 12"></polyline>
                                                            </svg>
                                                            Llegó {{ $cita->check_in_hora }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>

                                            {{-- Consulta y Detalle de la Cita (Solo Lectura) --}}
                                            <td style="text-align: right;">
                                                <button 
                                                    type="button" 
                                                    class="admin-btn-ver-detalle" 
                                                    data-cita-id="{{ $cita->id }}"
                                                    data-detalle-url="{{ route('admin.citas.detalle', $cita->id) }}"
                                                    onclick="abrirModalDetalleCita({{ $cita->id }}, '{{ route('admin.citas.detalle', $cita->id) }}'); event.preventDefault();"
                                                    title="Consultar detalles completos de la cita registrados por recepción"
                                                >
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                        <circle cx="12" cy="12" r="3"></circle>
                                                    </svg>
                                                    <span>Ver detalle</span>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>

    {{-- Modal de Consulta de Detalle para Admin (Solo Visualización) --}}
    @include('partials.modal-detalle-cita')

    <!-- SCRIPTS DE INTERACCIÓN DEL PANEL ADMIN -->
    <script>
        // Manejador de respaldo delegado para el botón "Ver detalle"
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.admin-btn-ver-detalle');
            if (btn && !e.defaultPrevented) {
                e.preventDefault();
                const citaId = btn.getAttribute('data-cita-id');
                const detalleUrl = btn.getAttribute('data-detalle-url');
                if (typeof window.abrirModalDetalleCita === 'function') {
                    window.abrirModalDetalleCita(citaId, detalleUrl);
                }
            }
        });

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

            // Restaurar estado colapsado previo desde localStorage
            if (localStorage.getItem('admin_sidebar_collapsed') === 'true') {
                if (adminSidebar) adminSidebar.classList.add('collapsed');
                if (sidebarToggleBtn) {
                    sidebarToggleBtn.setAttribute('aria-expanded', 'false');
                    sidebarToggleBtn.setAttribute('title', 'Expandir menú');
                }
            }

            if (sidebarToggleBtn && adminSidebar) {
                sidebarToggleBtn.addEventListener('click', function () {
                    const isCollapsed = adminSidebar.classList.toggle('collapsed');
                    localStorage.setItem('admin_sidebar_collapsed', isCollapsed ? 'true' : 'false');
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

        function cambiarVistaAdmin(vista, persist = true) {
            const calContainer = document.getElementById('vistaCalendarioContainer');
            const listContainer = document.getElementById('vistaListadoContainer');
            const btnCal = document.getElementById('tabBtnCalendario');
            const btnList = document.getElementById('tabBtnListado');

            if (vista === 'listado') {
                if (calContainer) calContainer.style.display = 'none';
                if (listContainer) listContainer.style.display = 'block';
                if (btnCal) btnCal.classList.remove('active');
                if (btnList) btnList.classList.add('active');
            } else {
                if (listContainer) listContainer.style.display = 'none';
                if (calContainer) calContainer.style.display = 'block';
                if (btnList) btnList.classList.remove('active');
                if (btnCal) btnCal.classList.add('active');

                // Forzar refresco/redibujado del calendario si fue redimensionado
                window.dispatchEvent(new Event('resize'));
            }

            if (persist) {
                try {
                    sessionStorage.setItem('admin_citas_tab', vista);
                    const currentUrl = new URL(window.location.href);
                    currentUrl.searchParams.set('tab', vista);
                    window.history.replaceState({ tab: vista }, '', currentUrl.toString());
                } catch (e) {}
            }
        }

        // Restaurar estado de pestaña activa y scroll tras confirmaciones y recargas
        document.addEventListener('DOMContentLoaded', function () {
            try {
                const urlParams = new URLSearchParams(window.location.search);
                const tabInUrl = urlParams.get('tab');
                const savedTab = sessionStorage.getItem('admin_citas_tab');

                if (tabInUrl === 'listado' || (!tabInUrl && savedTab === 'listado')) {
                    cambiarVistaAdmin('listado', false);
                } else if (tabInUrl === 'calendario' || (!tabInUrl && savedTab === 'calendario')) {
                    cambiarVistaAdmin('calendario', false);
                }

                const savedScroll = sessionStorage.getItem('citas_scroll_y');
                if (savedScroll !== null) {
                    sessionStorage.removeItem('citas_scroll_y');
                    setTimeout(() => {
                        window.scrollTo({ top: parseInt(savedScroll, 10), behavior: 'instant' });
                    }, 60);
                }
            } catch (e) {}
        });
    </script>
    @stack('scripts')
</body>
</html>
