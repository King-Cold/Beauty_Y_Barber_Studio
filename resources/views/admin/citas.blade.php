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

        .brand-info { display: flex; flex-direction: column; }
        .brand-name { font-size: 19px; font-weight: 800; color: var(--text-white); }
        .brand-role-badge {
            font-size: 10px; font-weight: 700; text-transform: uppercase;
            background: rgba(255,255,255,0.05); color: var(--text-white);
            border: 1px solid rgba(255,255,255,0.1); padding: 2px 8px; border-radius: 4px;
        }

        .user-nav { display: flex; align-items: center; gap: 16px; }
        .profile-dropdown-wrapper { position: relative; }
        .profile-btn {
            display: flex; align-items: center; gap: 12px; padding: 6px 12px;
            background: transparent; border: 1px solid transparent; border-radius: 8px;
            cursor: pointer; color: inherit; transition: all 0.2s ease;
        }
        .profile-btn:hover { background: rgba(255,255,255,0.05); border-color: var(--border-subtle); }
        .user-avatar {
            width: 38px; height: 38px; border-radius: 8px; background: var(--barber-blue-pale);
            color: var(--barber-blue-light); display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 13px; border: 1px solid rgba(0, 85, 255, 0.3);
        }
        .user-meta { display: flex; flex-direction: column; text-align: left; }
        .user-name { font-size: 13px; font-weight: 700; color: var(--text-white); }
        .user-role { font-size: 11px; color: var(--text-muted); }
        .dropdown-arrow { width: 16px; height: 16px; color: var(--text-muted); }

        .profile-menu {
            position: absolute; right: 0; top: calc(100% + 8px); width: 220px;
            background: var(--bg-header); border: 1px solid var(--border-strong);
            border-radius: 10px; box-shadow: 0 10px 25px rgba(0,0,0,0.5);
            display: none; flex-direction: column; padding: 6px; z-index: 60;
        }
        .profile-menu.show { display: flex; }
        .menu-item {
            display: flex; align-items: center; gap: 10px; padding: 10px 12px;
            font-size: 13px; color: var(--text-main); text-decoration: none; border-radius: 6px;
            border: none; background: transparent; width: 100%; text-align: left; cursor: pointer;
        }
        .menu-item:hover { background: rgba(255,255,255,0.05); color: var(--text-white); }
        .menu-item.logout-item { color: var(--barber-red); }
        .menu-item.logout-item:hover { background: var(--barber-red-light); }

        /* LAYOUT & SIDEBAR */
        .admin-layout {
            display: flex; flex: 1; min-height: calc(100vh - var(--header-height));
            position: relative; overflow-x: clip;
        }

        .admin-sidebar {
            width: var(--sidebar-width); background-color: var(--bg-sidebar);
            border-right: 1px solid var(--border-subtle); flex-shrink: 0;
            display: flex; flex-direction: column; z-index: 30;
            position: sticky; top: var(--header-height); height: calc(100vh - var(--header-height));
            align-self: flex-start; overflow: hidden;
            transition: width 0.3s var(--ease-fluid);
        }

        .admin-sidebar.collapsed { width: var(--sidebar-collapsed-width); }
        .admin-sidebar.collapsed .sidebar-title,
        .admin-sidebar.collapsed .nav-label,
        .admin-sidebar.collapsed .nav-badge { display: none; }
        .admin-sidebar.collapsed .nav-item { justify-content: center; padding: 12px 0; }
        .admin-sidebar.collapsed .sidebar-header { justify-content: center; padding: 0; }

        .sidebar-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 24px; height: 60px; border-bottom: 1px solid var(--border-subtle);
        }
        .sidebar-title { font-size: 12px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.15em; }
        .sidebar-toggle-btn {
            background: transparent; border: none; width: 32px; height: 32px; display: flex;
            align-items: center; justify-content: center; cursor: pointer; color: var(--text-muted);
            border-radius: 4px; transition: all 0.2s var(--ease-fluid);
        }
        .sidebar-toggle-btn:hover { background: var(--barber-blue-pale); color: var(--barber-blue); }

        .sidebar-inner { flex: 1; overflow-y: auto; padding: 16px 12px; }
        .sidebar-nav { display: flex; flex-direction: column; gap: 4px; }
        .nav-item {
            display: flex; align-items: center; gap: 12px; padding: 10px 14px;
            color: var(--text-muted); text-decoration: none; border-radius: 8px; font-size: 13.5px;
            font-weight: 600; transition: all 0.2s ease;
        }
        .nav-item:hover { background: rgba(255,255,255,0.04); color: var(--text-white); }
        .nav-item.active { background: var(--barber-blue); color: #ffffff; box-shadow: 0 4px 12px rgba(0, 85, 255, 0.3); }
        .nav-icon-box { width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; }
        .nav-icon-box svg { width: 18px; height: 18px; }
        .nav-badge {
            margin-left: auto; font-size: 10px; font-weight: 700; padding: 2px 6px;
            border-radius: 10px; background: rgba(255,255,255,0.08); color: var(--text-muted);
        }
        .nav-item.active .nav-badge { background: rgba(255,255,255,0.25); color: #ffffff; }

        /* CONTENIDO PRINCIPAL */
        .main-content {
            flex: 1; padding: 32px; overflow-y: auto; max-width: 100%;
            background-color: var(--bg-canvas);
        }

        .sidebar-backdrop {
            display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7);
            backdrop-filter: blur(4px); z-index: 25;
        }

        @media (max-width: 1024px) {
            .mobile-toggle-btn { display: flex; }
            .admin-sidebar {
                position: fixed; top: var(--header-height); bottom: 0; left: 0;
                width: 280px; transform: translateX(-100%);
                box-shadow: 4px 0 24px rgba(0,0,0,0.5);
                transition: transform 0.3s var(--ease-fluid);
            }
            .admin-sidebar.open-mobile { transform: translateX(0); }
            .sidebar-backdrop.active { display: block; }
            .main-content { padding: 20px 16px; }
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
            border: 1px solid rgba(0, 85, 255, 0.4);
            box-shadow: 0 4px 14px rgba(0, 85, 255, 0.25);
            white-space: nowrap;
        }
        .btn-admin-agendar-cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 85, 255, 0.4);
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
            background: var(--barber-blue);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 85, 255, 0.3);
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
            background: rgba(0, 85, 255, 0.08);
            box-shadow: 0 0 0 3px rgba(0, 85, 255, 0.2);
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
            padding: 10px 18px;
            background: var(--barber-blue);
            color: #ffffff;
            border: 1px solid rgba(0, 85, 255, 0.4);
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-admin-filtrar:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
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
            background: rgba(0, 85, 255, 0.12);
            border: 1px solid rgba(0, 85, 255, 0.25);
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
            color: #38bdf8;
            font-weight: 800;
            font-size: 14px;
            white-space: nowrap;
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
            background: rgba(0, 85, 255, 0.2);
            border-color: rgba(0, 85, 255, 0.4);
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
            color: #fbbf24;
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
            color: #34d399;
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
            color: #34d399;
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
            background: rgba(0, 85, 255, 0.12);
            color: #60a5fa;
            border: 1px solid rgba(0, 85, 255, 0.3);
            border-radius: 7px;
            font-size: 11.5px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
        }
        .admin-btn-reagendar:hover {
            background: #0055ff;
            color: #ffffff;
            border-color: #0055ff;
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
            background: rgba(0, 85, 255, 0.25);
            color: #93c5fd;
        }
        .admin-btn-tool-icon.tool-editar:hover {
            background: rgba(56, 189, 248, 0.25);
            color: #38bdf8;
        }
        .admin-btn-tool-icon.tool-cancelar:hover {
            background: rgba(239, 68, 68, 0.25);
            color: #fca5a5;
        }

        .admin-badge-checkin {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 7px;
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.3);
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
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
        .status-badge.confirmada::before { background: #34d399; }

        .status-badge.cancelada {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        .status-badge.cancelada::before { background: #f87171; }

        .status-badge.completada {
            background: rgba(6, 182, 212, 0.15);
            color: #22d3ee;
            border: 1px solid rgba(6, 182, 212, 0.3);
        }
        .status-badge.completada::before { background: #22d3ee; }

        .status-badge.en_atencion,
        .status-badge.en_proceso {
            background: rgba(0, 85, 255, 0.15);
            color: #60a5fa;
            border: 1px solid rgba(0, 85, 255, 0.3);
        }
        .status-badge.en_atencion::before,
        .status-badge.en_proceso::before { background: #60a5fa; }

        .status-badge.vencida {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }
        .status-badge.vencida::before { background: #fbbf24; }
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
                <a href="{{ url('/admin') }}" class="brand-section">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="brand-logo-img" onerror="this.style.display='none'">
                    <div class="brand-info">
                        <span class="brand-name">Beauty & Barber Studio</span>
                        <div class="brand-badge-container">
                            <span class="brand-role-badge">Panel Administrativo</span>
                        </div>
                    </div>
                </a>
            </div>

            <nav class="user-nav" aria-label="Cuenta">
                <div class="profile-dropdown-wrapper" id="profileDropdownWrapper">
                    <button type="button" class="profile-btn" id="profileBtn" aria-expanded="false" aria-haspopup="true" aria-controls="profileMenu">
                        <div class="user-avatar" aria-hidden="true">AD</div>
                        <div class="user-meta">
                            <span class="user-name">{{ Auth::user()->name ?? 'Administrador' }}</span>
                            <span class="user-role">{{ Auth::user()->email ?? 'admin@barberstudio.com' }}</span>
                        </div>
                        <svg class="dropdown-arrow" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="profile-menu" id="profileMenu" role="menu">
                        <button type="button" class="menu-item logout-item" onclick="document.getElementById('logout-form').submit();">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                <polyline points="16 17 21 12 16 7"></polyline>
                                <line x1="21" y1="12" x2="9" y2="12"></line>
                            </svg>
                            <span>Cerrar sesión</span>
                        </button>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <!-- CONTENEDOR ESTRUCTURAL -->
    <div class="admin-layout" id="adminLayout">
        <!-- SIDEBAR -->
        <aside class="admin-sidebar" id="adminSidebar" aria-label="Navegación lateral">
            <div class="sidebar-header">
                <span class="sidebar-title">Módulos del Sistema</span>
                <button type="button" class="sidebar-toggle-btn" id="sidebarToggleBtn" aria-label="Colapsar menú" title="Colapsar o expandir menú">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>
            </div>

            <div class="sidebar-inner">
                <nav class="sidebar-nav">
                    <!-- 1. Dashboard -->
                    <a href="{{ url('/admin') }}" class="nav-item" title="Dashboard">
                        <div class="nav-icon-box">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
                    <a href="{{ url('/admin/trabajadores') }}" class="nav-item" title="Trabajadores">
                        <div class="nav-icon-box">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="2" y="7" width="20" height="14" rx="2"></rect>
                                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                            </svg>
                        </div>
                        <span class="nav-label">Trabajadores</span>
                        <span class="nav-badge">Staff</span>
                    </a>

                    <!-- 4. Clientes -->
                    <a href="{{ route('recepcion.clientes') }}" class="nav-item" title="Clientes">
                        <div class="nav-icon-box">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>
                        <span class="nav-label">Clientes</span>
                    </a>

                    <!-- 5. Servicios -->
                    <a href="{{ route('admin.services') }}" class="nav-item" title="Servicios">
                        <div class="nav-icon-box">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </div>
                        <span class="nav-label">Citas</span>
                        <span class="nav-badge">Agenda</span>
                    </a>


                    <!-- 7. Configuración General -->
                    <a href="{{ route('admin.configuracion') }}" class="nav-item" title="Configuración general">
                        <div class="nav-icon-box">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="3"></circle>
                                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                            </svg>
                        </div>
                        <span class="nav-label">Configuración</span>
                    </a>
                </nav>
            </div>
        </aside>

        <div class="sidebar-backdrop" id="sidebarBackdrop" aria-hidden="true"></div>

        <!-- ÁREA PRINCIPAL -->
        <main id="main-content" class="main-content" role="main">
            @php
                $tabActiva = (request('tab') === 'listado' || request()->filled('buscar') || request()->filled('trabajador_id') || (request()->filled('estado') && request('estado') !== 'todas') || request()->filled('fecha_desde') || request()->filled('fecha_hasta')) ? 'listado' : 'calendario';
            @endphp

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
                        <span style="font-size: 12px; color: #38bdf8; font-weight: 600;">
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
            const profileDropdown = document.getElementById('profileDropdownWrapper');
            const profileBtn = document.getElementById('profileBtn');
            const profileMenu = document.getElementById('profileMenu');

            if (profileBtn && profileMenu) {
                profileBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    profileMenu.classList.toggle('show');
                });

                document.addEventListener('click', function (e) {
                    if (!profileDropdown.contains(e.target)) {
                        profileMenu.classList.remove('show');
                    }
                });
            }

            const sidebar = document.getElementById('adminSidebar');
            const toggleBtn = document.getElementById('sidebarToggleBtn');
            const mobileBtn = document.getElementById('mobileToggleBtn');
            const backdrop = document.getElementById('sidebarBackdrop');

            if (toggleBtn && sidebar) {
                toggleBtn.addEventListener('click', function () {
                    sidebar.classList.toggle('collapsed');
                });
            }

            if (mobileBtn && sidebar && backdrop) {
                mobileBtn.addEventListener('click', function () {
                    sidebar.classList.toggle('open-mobile');
                    backdrop.classList.toggle('active');
                });

                backdrop.addEventListener('click', function () {
                    sidebar.classList.remove('open-mobile');
                    backdrop.classList.remove('active');
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
