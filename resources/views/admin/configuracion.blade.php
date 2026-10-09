<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beauty & Barber Studio - Configuración General</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* ===================================================
           SISTEMA DE DISEÑO BASE DEL SISTEMA (DARK MODE)
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

        /* HEADER SUPERIOR */
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

        /* PERFIL DE USUARIO */
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
            border: 1px solid var(--border-subtle);
            border-radius: 30px;
            padding: 6px 14px 6px 6px;
            cursor: pointer;
            transition: all 0.2s var(--ease-fluid);
        }

        .profile-btn:hover,
        .profile-btn:focus-visible {
            border-color: rgba(0, 85, 255, 0.3);
            background: rgba(255, 255, 255, 0.02);
            outline: none;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--barber-blue);
            color: var(--text-white);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
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
            line-height: 1.2;
        }

        .user-role {
            font-size: 11px;
            color: var(--text-muted);
            line-height: 1.2;
        }

        .dropdown-arrow {
            width: 16px;
            height: 16px;
            color: var(--text-muted);
            transition: transform 0.2s var(--ease-fluid);
        }

        .profile-dropdown-wrapper.open .dropdown-arrow {
            transform: rotate(180deg);
        }

        .profile-menu {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            width: 220px;
            background: var(--bg-header);
            border: 1px solid var(--border-strong);
            border-radius: 8px;
            padding: 6px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
            display: none;
            flex-direction: column;
            gap: 2px;
            z-index: 100;
        }

        .profile-dropdown-wrapper.open .profile-menu {
            display: flex;
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            color: var(--text-main);
            text-decoration: none;
            font-size: 13px;
            border-radius: 6px;
            transition: all 0.15s ease;
        }

        .menu-item:hover,
        .menu-item:focus-visible {
            background: rgba(255, 255, 255, 0.05);
            outline: none;
        }

        .menu-divider {
            height: 1px;
            background: var(--border-subtle);
            margin: 4px 0;
        }

        .logout-item {
            color: var(--barber-red);
        }

        .logout-item:hover {
            background: var(--barber-red-light);
        }

        /* ESTRUCTURA PRINCIPAL */
        .admin-layout {
            display: flex;
            flex: 1;
            position: relative;
            height: calc(100vh - var(--header-height));
            max-height: calc(100vh - var(--header-height));
            overflow: hidden;
        }

        /* MENÚ LATERAL (SIDEBAR) - FIJO AL HACER SCROLL */
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
            top: 0;
            height: 100%;
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

        .sidebar-toggle-btn:hover {
            background: var(--barber-blue-pale);
            color: var(--barber-blue);
            transform: scale(1.05);
        }

        .sidebar-toggle-btn svg {
            width: 18px;
            height: 18px;
        }

        .admin-sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        .admin-sidebar.collapsed .sidebar-title,
        .admin-sidebar.collapsed .nav-label,
        .admin-sidebar.collapsed .nav-badge {
            display: none;
        }

        .admin-sidebar.collapsed .nav-item {
            justify-content: center;
            padding: 12px 0;
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
            border-left: 3px solid transparent;
        }

        .nav-item:hover,
        .nav-item:focus-visible {
            background: rgba(255, 255, 255, 0.03);
            color: var(--barber-blue);
            border-left-color: var(--barber-blue);
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
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .sidebar-backdrop {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(5, 10, 21, 0.75);
            backdrop-filter: blur(4px);
            z-index: 25;
        }

        /* ÁREA DE CONTENIDO PRINCIPAL (SCROLL INTERNO MANTENIENDO EL MENÚ LATERAL FIJO) */
        .main-content {
            flex: 1;
            padding: 32px;
            background: var(--bg-canvas);
            height: 100%;
            overflow-y: auto;
            overflow-x: hidden;
            box-sizing: border-box;
            scroll-behavior: smooth;
        }

        .main-content::-webkit-scrollbar {
            width: 6px;
        }

        .main-content::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.12);
            border-radius: 4px;
        }

        .main-content::-webkit-scrollbar-thumb:hover {
            background: var(--barber-blue);
        }

        /* ===================================================
           TARJETA CONTENEDORA: FECHAS ESPECIALES
           =================================================== */
        .special-dates-card {
            background: var(--bg-header);
            border: 1px solid var(--border-strong);
            border-radius: 14px;
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35);
            overflow: hidden;
            max-width: 860px;
            margin-top: 28px;
        }

        .special-dates-header {
            padding: 24px 28px;
            border-bottom: 1px solid var(--border-subtle);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            background: rgba(255, 255, 255, 0.015);
        }

        .special-dates-header-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .card-icon-badge.icon-amber {
            background: rgba(245, 158, 11, 0.12);
            border: 1px solid rgba(245, 158, 11, 0.3);
            color: #fbbf24;
        }

        /* Formulario en línea */
        .special-form-container {
            padding: 24px 28px;
            background: rgba(255, 255, 255, 0.015);
            border-bottom: 1px solid var(--border-subtle);
        }

        .special-form-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: flex-start;
        }

        .special-field-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .special-field-group label {
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .special-form-control {
            background-color: #090f1d;
            border: 1px solid var(--border-subtle);
            border-radius: 6px;
            color: #ffffff;
            font-family: inherit;
            font-size: 13px;
            font-weight: 600;
            padding: 8px 12px;
            height: 38px;
            box-sizing: border-box;
            transition: all 0.2s ease;
        }

        .special-date-input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .special-date-input-wrap input[type="text"] {
            padding-right: 38px;
            width: 100%;
        }

        .btn-calendar-trigger {
            position: absolute;
            right: 8px;
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
            filter: drop-shadow(0 0 4px rgba(255, 255, 255, 0.8));
        }

        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1) brightness(100%) contrast(100%) !important;
            cursor: pointer;
            opacity: 1 !important;
        }

        .special-date-help-text {
            font-size: 11px;
            color: #ef4444;
            font-weight: 600;
            margin-top: 4px;
            line-height: 1.3;
            letter-spacing: 0.02em;
        }

        .special-form-control:focus {
            outline: none;
            border-color: var(--barber-blue);
            box-shadow: 0 0 0 2px rgba(0, 85, 255, 0.2);
        }

        .special-checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
            height: 38px;
            padding: 0 12px;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--border-subtle);
            border-radius: 6px;
            cursor: pointer;
            user-select: none;
            white-space: nowrap;
        }

        .special-checkbox-wrapper input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--barber-blue);
            cursor: pointer;
        }

        .special-checkbox-wrapper span {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-main);
        }

        .special-time-picker-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .special-form-actions-row {
            display: flex;
            justify-content: flex-end;
            margin-top: 14px;
        }

        .btn-add-special {
            background: linear-gradient(135deg, var(--barber-blue) 0%, #0044cc 100%);
            color: #ffffff;
            border: none;
            border-radius: 6px;
            padding: 0 18px;
            height: 38px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(0, 85, 255, 0.3);
        }

        .btn-add-special:hover {
            background: linear-gradient(135deg, var(--barber-blue-light) 0%, var(--barber-blue) 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(0, 85, 255, 0.45);
        }

        /* Tabla de Fechas Especiales */
        .special-table-container {
            padding: 18px 28px 24px 28px;
            overflow-x: auto;
        }

        .special-custom-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .special-custom-table th {
            padding: 12px 14px;
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.06em;
            border-bottom: 1px solid var(--border-subtle);
            background: rgba(255, 255, 255, 0.015);
        }

        .special-custom-table td {
            padding: 14px 14px;
            font-size: 13px;
            color: var(--text-white);
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            vertical-align: middle;
        }

        .special-custom-table tr:hover td {
            background: rgba(255, 255, 255, 0.02);
        }

        .badge-special-closed {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: var(--barber-red-light);
            color: var(--barber-red);
            border: 1px solid rgba(239, 68, 68, 0.25);
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
        }

        .badge-special-hours {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: rgba(0, 85, 255, 0.12);
            color: #93c5fd;
            border: 1px solid rgba(0, 85, 255, 0.25);
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .btn-edit-special {
            background: transparent;
            border: 1px solid var(--border-subtle);
            color: var(--text-muted);
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-edit-special:hover {
            background: var(--barber-blue-pale);
            color: #60a5fa;
            border-color: rgba(0, 85, 255, 0.4);
            transform: scale(1.05);
        }

        .special-actions-cell {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 6px;
        }

        .btn-delete-special {
            background: transparent;
            border: 1px solid var(--border-subtle);
            color: var(--text-muted);
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-delete-special:hover {
            background: var(--barber-red-light);
            color: var(--barber-red);
            border-color: rgba(239, 68, 68, 0.4);
            transform: scale(1.05);
        }

        /* Modal para Edición de Fecha Especial */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(5, 10, 21, 0.8);
            backdrop-filter: blur(6px);
            z-index: 1000;
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
            max-width: 520px;
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.7);
            transform: scale(0.95);
            transition: transform 0.25s var(--ease-fluid);
            overflow: hidden;
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
            background: var(--bg-header);
        }

        .modal-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-white);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-title svg {
            color: var(--barber-blue-light);
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
            font-size: 22px;
            line-height: 1;
        }

        .modal-close-btn:hover {
            background: rgba(255, 255, 255, 0.08);
            color: var(--text-white);
        }

        .modal-body {
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .modal-footer {
            padding: 16px 24px;
            border-top: 1px solid var(--border-subtle);
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
            background: rgba(10, 17, 36, 0.95);
        }

        .btn-modal-cancel {
            background: transparent;
            border: 1px solid var(--border-subtle);
            color: var(--text-muted);
            border-radius: 6px;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-modal-cancel:hover {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-white);
        }

        .btn-modal-save {
            background: linear-gradient(135deg, var(--barber-blue) 0%, #0044cc 100%);
            color: #ffffff;
            border: none;
            border-radius: 6px;
            padding: 8px 18px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(0, 85, 255, 0.3);
        }

        .btn-modal-save:hover {
            background: linear-gradient(135deg, var(--barber-blue-light) 0%, var(--barber-blue) 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(0, 85, 255, 0.45);
        }

        .special-empty-row {
            text-align: center;
            padding: 32px !important;
            color: var(--text-muted);
            font-size: 13px;
        }

        /* HEADER DE PÁGINA */
        .settings-page-header {
            margin-bottom: 28px;
        }

        .settings-title-group h1 {
            font-size: 24px;
            font-weight: 800;
            color: var(--text-white);
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 6px;
        }

        .settings-title-group p {
            font-size: 14px;
            color: var(--text-muted);
        }

        /* ===================================================
           TARJETA CONTENEDORA: HORARIO DE LA SUCURSAL
           =================================================== */
        .schedule-config-card {
            background: var(--bg-header);
            border: 1px solid var(--border-strong);
            border-radius: 14px;
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35);
            overflow: hidden;
            max-width: 860px;
        }

        .schedule-card-header {
            padding: 24px 28px;
            border-bottom: 1px solid var(--border-subtle);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            background: rgba(255, 255, 255, 0.015);
        }

        .schedule-card-header-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .card-icon-badge {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: var(--barber-blue-pale);
            border: 1px solid rgba(0, 85, 255, 0.3);
            color: var(--barber-blue-light);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .card-icon-badge svg {
            width: 22px;
            height: 22px;
        }

        .schedule-card-title {
            font-size: 17px;
            font-weight: 700;
            color: var(--text-white);
            margin-bottom: 2px;
        }

        .schedule-card-desc {
            font-size: 13px;
            color: var(--text-muted);
            margin: 0;
        }

        .status-summary-pill {
            font-size: 12px;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 20px;
            background: rgba(16, 185, 129, 0.12);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.25);
            display: flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }

        .status-summary-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 6px #10b981;
        }

        /* CUERPO DEL PANEL: LISTA DE 7 DÍAS */
        .schedule-card-body {
            padding: 24px 28px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .branch-day-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 14px 18px;
            background: rgba(10, 17, 36, 0.65);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            transition: all 0.25s var(--ease-fluid);
        }

        .branch-day-row:hover {
            border-color: rgba(0, 85, 255, 0.3);
            background: rgba(15, 25, 52, 0.8);
            transform: translateX(2px);
        }

        .branch-day-row.day-closed {
            opacity: 0.6;
            background: rgba(10, 17, 36, 0.35);
            border-color: rgba(255, 255, 255, 0.02);
        }

        .branch-day-row.day-closed:hover {
            transform: none;
            border-color: rgba(255, 255, 255, 0.06);
        }

        /* Lado Izquierdo del Día (Toggle, Nombre, Badge de Estado) */
        .branch-day-info {
            display: flex;
            align-items: center;
            gap: 16px;
            min-width: 240px;
        }

        /* Switch (Toggle) */
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
            flex-shrink: 0;
            margin: 0;
            cursor: pointer;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
            margin: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: rgba(255, 255, 255, 0.12);
            transition: all 0.25s var(--ease-fluid);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 3px;
            bottom: 3px;
            background-color: #94a3b8;
            transition: all 0.25s var(--ease-fluid);
            border-radius: 50%;
            box-shadow: 0 1px 3px rgba(0,0,0,0.4);
        }

        .toggle-switch input:checked + .toggle-slider {
            background-color: var(--barber-blue);
            border-color: var(--barber-blue);
        }

        .toggle-switch input:checked + .toggle-slider:before {
            transform: translateX(20px);
            background-color: #ffffff;
        }

        .branch-day-title-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .branch-day-name {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-white);
            min-width: 80px;
        }

        .branch-day-status-pill {
            font-size: 11px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 6px;
            letter-spacing: 0.02em;
            transition: all 0.2s ease;
        }

        .branch-day-status-pill.status-open {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .branch-day-status-pill.status-closed {
            background: rgba(239, 68, 68, 0.12);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.25);
        }

        /* Lado Derecho del Día (Selectores de Hora o Mensaje de Cerrado) */
        .branch-day-times {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: nowrap;
            flex-shrink: 0;
            justify-content: flex-end;
            transition: all 0.2s ease;
        }

        .time-picker-item {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
        }

        .time-picker-item label {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            white-space: nowrap;
        }

        .time-picker-item select.time-select {
            background-color: #090f1d;
            border: 1px solid var(--border-subtle);
            border-radius: 6px;
            color: #ffffff;
            font-family: inherit;
            font-size: 13px;
            font-weight: 700;
            padding: 7px 24px 7px 10px;
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

        .time-picker-item select.time-select:hover:not(:disabled) {
            border-color: rgba(0, 85, 255, 0.4);
            background-color: #0d162a;
        }

        .time-picker-item select.time-select:focus {
            outline: none;
            border-color: var(--barber-blue);
            box-shadow: 0 0 0 2px rgba(0, 85, 255, 0.2);
            background-color: #0d162a;
        }

        .time-picker-item select.time-select:disabled {
            opacity: 0.35;
            cursor: not-allowed;
            background-color: rgba(255, 255, 255, 0.02);
            border-color: rgba(255, 255, 255, 0.04);
            color: var(--text-muted);
        }

        .time-picker-item select.time-select option {
            background-color: #0a1124 !important;
            color: #ffffff !important;
            font-size: 13px;
            padding: 8px 12px;
        }

        .time-picker-item input[type="time"] {
            background: #090f1d;
            border: 1px solid var(--border-subtle);
            border-radius: 6px;
            color: #ffffff;
            font-family: inherit;
            font-size: 13px;
            font-weight: 600;
            padding: 7px 10px;
            color-scheme: dark;
            width: 96px;
            text-align: center;
            transition: all 0.2s ease;
            -webkit-appearance: none;
            -moz-appearance: textfield;
            appearance: none;
        }

        /* Desactivar el icono de reloj y el menú emergente nativo (salto) del navegador */
        .time-picker-item input[type="time"]::-webkit-calendar-picker-indicator {
            display: none !important;
            -webkit-appearance: none;
            appearance: none;
            background: transparent;
            cursor: pointer;
        }

        .time-picker-item input[type="time"]::-webkit-inner-spin-button,
        .time-picker-item input[type="time"]::-webkit-clear-button {
            display: none !important;
            -webkit-appearance: none;
        }

        .time-picker-item input[type="time"]:focus {
            outline: none;
            border-color: var(--barber-blue);
            box-shadow: 0 0 0 2px rgba(0, 85, 255, 0.2);
            background: #0d162a;
        }

        .time-sep-arrow {
            color: var(--text-muted);
            font-size: 13px;
            opacity: 0.5;
        }

        /* Banner cuando el día está cerrado */
        .day-closed-msg {
            display: none;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            background: rgba(255, 255, 255, 0.02);
            border: 1px dashed var(--border-subtle);
            border-radius: 6px;
            padding: 7px 16px;
        }

        .branch-day-row.day-closed .time-picker-item {
            display: none;
        }

        .branch-day-row.day-closed .time-sep-arrow {
            display: none;
        }

        .branch-day-row.day-closed .day-closed-msg {
            display: inline-flex;
        }

        /* PIE DEL PANEL: ACCIÓN DE GUARDAR HORARIOS */
        .schedule-card-footer {
            padding: 20px 28px;
            border-top: 1px solid var(--border-subtle);
            background: rgba(255, 255, 255, 0.015);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .schedule-footer-hint {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: var(--text-muted);
        }

        .schedule-footer-hint svg {
            color: var(--barber-blue-light);
            flex-shrink: 0;
        }

        .btn-save-schedule {
            background: var(--barber-blue);
            border: none;
            color: var(--text-white);
            padding: 10px 24px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s var(--ease-fluid);
            box-shadow: 0 4px 14px rgba(0, 85, 255, 0.35);
        }

        .schedule-card-actions {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .schedule-autosave-indicator {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: 12px;
            font-weight: 500;
            color: var(--text-muted);
            transition: all 0.3s ease;
            opacity: 0;
            pointer-events: none;
        }

        .schedule-autosave-indicator.is-visible {
            opacity: 1;
        }

        .schedule-autosave-indicator.is-saving {
            color: #38bdf8;
        }

        .schedule-autosave-indicator.is-saved {
            color: #34d399;
        }

        .schedule-autosave-indicator svg {
            flex-shrink: 0;
        }

        .btn-save-schedule:hover {
            background: var(--barber-blue-light);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(0, 85, 255, 0.45);
        }

        .btn-save-schedule:active {
            transform: translateY(0);
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            body {
                height: auto;
                overflow-y: auto;
            }
            .admin-layout {
                height: auto;
                max-height: none;
                overflow: visible;
            }
            .header-container {
                padding: 0 16px;
            }
            .mobile-toggle-btn {
                display: flex;
            }
            .admin-sidebar {
                position: fixed;
                top: 0;
                bottom: 0;
                left: 0;
                z-index: 40;
                transform: translateX(-100%);
            }
            .admin-sidebar.open-mobile {
                transform: translateX(0);
            }
            .sidebar-backdrop.active {
                display: block;
            }
            .main-content {
                height: auto;
                overflow-y: visible;
                padding: 20px 16px;
            }
            .special-form-grid {
                flex-direction: column;
                align-items: stretch;
            }
            .special-field-group,
            .special-checkbox-wrapper,
            .btn-add-special {
                width: 100%;
            }
            .schedule-card-header {
                flex-direction: column;
                align-items: flex-start;
                padding: 20px;
            }
            .schedule-card-body {
                padding: 20px 16px;
            }
            .branch-day-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
                padding: 14px;
            }
            .branch-day-info {
                width: 100%;
                justify-content: space-between;
                min-width: 0;
            }
            .branch-day-times {
                width: 100%;
                justify-content: space-between;
            }
            .schedule-card-footer {
                flex-direction: column;
                align-items: stretch;
                padding: 20px;
            }
            .btn-save-schedule {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <a href="#main-content" class="skip-link">Saltar al contenido principal</a>

    <!-- HEADER SUPERIOR -->
    <header class="admin-header" role="banner">
        <div class="header-top-accent-line" aria-hidden="true"></div>

        <div class="header-container">
            <div class="header-left">
                <button type="button" class="mobile-toggle-btn" id="mobileToggleBtn" aria-label="Abrir menú de navegación" aria-expanded="false" aria-controls="adminSidebar">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
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
                    <button type="button" class="profile-btn" id="profileBtn" aria-expanded="false" aria-haspopup="true" aria-controls="profileMenu" aria-label="Menú de perfil del administrador">
                        <div class="user-avatar" aria-hidden="true">AD</div>
                        <div class="user-meta">
                            <span class="user-name">Administrador</span>
                            <span class="user-role">admin@barberstudio.com</span>
                        </div>
                        <svg class="dropdown-arrow" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <div class="profile-menu" id="profileMenu" role="menu" aria-labelledby="profileBtn">
                        <a href="#perfil" class="menu-item profile-item" role="menuitem">
                            <div class="menu-item-icon">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </div>
                            <span>Mi Perfil</span>
                        </a>
                        <a href="{{ route('admin.configuracion') }}" class="menu-item settings-item" role="menuitem">
                            <div class="menu-item-icon">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="3"></circle>
                                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                                </svg>
                            </div>
                            <span>Configuración</span>
                        </a>
                        <div class="menu-divider" role="separator"></div>
                        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="menu-item logout-item" role="menuitem">
                            <div class="menu-item-icon">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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

    <!-- CONTENEDOR ESTRUCTURAL -->
    <div class="admin-layout" id="adminLayout">

        <!-- MENÚ LATERAL (SIDEBAR) -->
        <aside class="admin-sidebar" id="adminSidebar" aria-label="Menú de navegación lateral">
            <div class="sidebar-header">
                <span class="sidebar-title">Módulos del Sistema</span>
                <button type="button" class="sidebar-toggle-btn" id="sidebarToggleBtn" aria-label="Colapsar o expandir menú lateral" aria-expanded="true" aria-controls="adminSidebar" title="Colapsar o expandir menú">
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

                    <!-- 7. Configuración General (ACTIVO) -->
                    <a href="{{ route('admin.configuracion') }}" class="nav-item active" aria-current="page" title="Configuración general">
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

        <!-- Fondo para cerrar sidebar en móviles -->
        <div class="sidebar-backdrop" id="sidebarBackdrop" aria-hidden="true"></div>

        <!-- ÁREA DE CONTENIDO PRINCIPAL -->
        <main id="main-content" class="main-content" role="main">
            <!-- Encabezado de la Página -->
            <div class="settings-page-header">
                <div class="settings-title-group">
                    <h1>
                        <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--barber-blue);">
                            <circle cx="12" cy="12" r="3"></circle>
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                        </svg>
                        Configuración General
                    </h1>
                    <p>Gestiona los parámetros globales de la sucursal, horarios comerciales y políticas operativas del estudio.</p>
                </div>
            </div>

            <!-- 1. CONTENEDOR PRINCIPAL: HORARIO DE APERTURA Y CIERRE DE LA SUCURSAL -->
            <div class="schedule-config-card">
                <!-- Encabezado del Panel -->
                <div class="schedule-card-header">
                    <div class="schedule-card-header-left">
                        <div class="card-icon-badge">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </div>
                        <div>
                            <h2 class="schedule-card-title">Horario de Apertura y Cierre de la Sucursal</h2>
                            <p class="schedule-card-desc">Define los días operativos y los rangos de atención al cliente del establecimiento.</p>
                        </div>
                    </div>
                    <div class="status-summary-pill" id="scheduleSummaryPill">
                        <span class="status-summary-dot"></span>
                        <span id="scheduleSummaryText">5 Días Abiertos • 2 Días Cerrados</span>
                    </div>
                </div>

                <!-- 2. CONFIGURACIÓN DE HORARIOS (LUNES A DOMINGO) -->
                <div class="schedule-card-body">
                    @php
                        $daysMapList = [
                            'lunes' => 'Lunes',
                            'martes' => 'Martes',
                            'miercoles' => 'Miércoles',
                            'jueves' => 'Jueves',
                            'viernes' => 'Viernes',
                            'sabado' => 'Sábado',
                            'domingo' => 'Domingo'
                        ];
                        $branchDaysConfig = [];
                        foreach ($daysMapList as $dId => $dNom) {
                            $savedH = $horariosSucursal[$dId] ?? null;
                            $branchDaysConfig[] = [
                                'id' => $dId,
                                'name' => $dNom,
                                'open' => $savedH ? (bool)$savedH['abierto'] : (!in_array($dId, ['sabado', 'domingo'])),
                                'start' => $savedH['apertura'] ?? ($dId === 'sabado' || $dId === 'domingo' ? '10:00' : '09:00'),
                                'end' => $savedH['cierre'] ?? ($dId === 'domingo' ? '15:00' : ($dId === 'sabado' ? '18:00' : '20:00')),
                            ];
                        }

                        $timeSlots = [];
                        $timeSlots[] = '00:00';
                        $timeSlots[] = '00:30';
                        for ($h = 1; $h <= 23; $h++) {
                            $timeSlots[] = sprintf('%02d:00', $h);
                            $timeSlots[] = sprintf('%02d:30', $h);
                        }
                        $timeSlots[] = '24:00';
                    @endphp

                    @foreach($branchDaysConfig as $day)
                        <div class="branch-day-row {{ $day['open'] ? '' : 'day-closed' }}" id="row-{{ $day['id'] }}">
                            <div class="branch-day-info">
                                <label class="toggle-switch" title="Alternar Abierto / Cerrado para {{ $day['name'] }}">
                                    <input type="checkbox" id="toggle-{{ $day['id'] }}" {{ $day['open'] ? 'checked' : '' }} onchange="toggleBranchDay('{{ $day['id'] }}', this.checked)">
                                    <span class="toggle-slider"></span>
                                </label>
                                <div class="branch-day-title-wrap">
                                    <span class="branch-day-name">{{ $day['name'] }}</span>
                                    <span class="branch-day-status-pill {{ $day['open'] ? 'status-open' : 'status-closed' }}" id="badge-{{ $day['id'] }}">
                                        {{ $day['open'] ? 'Abierto' : 'Cerrado' }}
                                    </span>
                                </div>
                            </div>
                            <div class="branch-day-times" id="times-{{ $day['id'] }}">
                                <div class="time-picker-item">
                                    <label for="open-{{ $day['id'] }}">Apertura</label>
                                    <select id="open-{{ $day['id'] }}" class="time-select" {{ $day['open'] ? '' : 'disabled' }}>
                                        @foreach($timeSlots as $slot)
                                            <option value="{{ $slot }}" {{ $slot === $day['start'] ? 'selected' : '' }}>{{ $slot }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <span class="time-sep-arrow">→</span>
                                <div class="time-picker-item">
                                    <label for="close-{{ $day['id'] }}">Cierre</label>
                                    <select id="close-{{ $day['id'] }}" class="time-select" {{ $day['open'] ? '' : 'disabled' }}>
                                        @foreach($timeSlots as $slot)
                                            <option value="{{ $slot }}" {{ $slot === $day['end'] ? 'selected' : '' }}>{{ $slot }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <span class="day-closed-msg">
                                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line>
                                    </svg>
                                    Cerrado todo el día
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- 3. ACCIONES: BOTÓN GUARDAR HORARIOS EN LA ESQUINA INFERIOR DERECHA -->
                <div class="schedule-card-footer">
                    <div class="schedule-footer-hint">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="16" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                        </svg>
                        <span>Los clientes únicamente podrán reservar servicios dentro de los horarios de apertura configurados.</span>
                    </div>

                    <div class="schedule-card-actions">
                        <div class="schedule-autosave-indicator" id="scheduleAutosaveIndicator" aria-live="polite">
                            <!-- Estado dinámico de sincronización automática -->
                        </div>

                        <button type="button" class="btn-save-schedule" id="btnSaveSchedule" onclick="saveBranchSchedule()">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                <polyline points="7 3 7 8 15 8"></polyline>
                            </svg>
                            <span>Guardar Horarios</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- ===================================================
                 SECCIÓN: FECHAS ESPECIALES (DÍAS FESTIVOS O EXCEPCIONES)
                 =================================================== -->
            <div class="special-dates-card">
                <!-- Encabezado del Panel -->
                <div class="special-dates-header">
                    <div class="special-dates-header-left">
                        <div class="card-icon-badge icon-amber">
                            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                                <polygon points="12 14 13.5 17 17 17.5 14.5 20 15 23.5 12 22 9 23.5 9.5 20 7 17.5 10.5 17"></polygon>
                            </svg>
                        </div>
                        <div>
                            <h2 class="schedule-card-title">Fechas Especiales (Días Festivos o Excepciones)</h2>
                            <p class="schedule-card-desc">Configura días festivos o modificaciones extraordinarias de horario de la sucursal.</p>
                        </div>
                    </div>
                </div>

                <!-- Formulario en Línea -->
                <div class="special-form-container">
                    <form id="formSpecialDate" onsubmit="submitSpecialDate(event)">
                        <!-- Fila de Campos alineados -->
                        <div class="special-form-grid">
                            <!-- Selector de Fecha con máscara DD/MM/AAAA -->
                            <div class="special-field-group" style="width: 145px; flex-shrink: 0;">
                                <label for="specialDateInput">Fecha</label>
                                <div class="special-date-input-wrap">
                                    <input type="text" id="specialDateInput" class="special-form-control" placeholder="DD/MM/AAAA" maxlength="10" autocomplete="off" required>
                                    <input type="date" id="specialDateNativePicker" style="position: absolute; opacity: 0; pointer-events: none; width: 0; height: 0;" min="{{ date('Y-m-d') }}" max="{{ date('Y') }}-12-31" tabindex="-1">
                                    <button type="button" class="btn-calendar-trigger" onclick="openNativeDatePicker('specialDateNativePicker')" title="Abrir calendario" aria-label="Abrir calendario">
                                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="16" y1="2" x2="16" y2="6"></line>
                                            <line x1="8" y1="2" x2="8" y2="6"></line>
                                            <line x1="3" y1="10" x2="21" y2="10"></line>
                                        </svg>
                                    </button>
                                </div>
                                <span class="special-date-help-text">Orden de captura manual: día, mes y año</span>
                            </div>

                            <!-- Campo de texto para el Motivo -->
                            <div class="special-field-group" style="flex: 1; min-width: 180px;">
                                <label for="specialReasonInput">Motivo</label>
                                <input type="text" id="specialReasonInput" class="special-form-control" placeholder="Ej. Navidad, Día Festivo..." required>
                            </div>

                            <!-- Casilla de verificación (Checkbox): Cerrado todo el día -->
                            <div class="special-field-group" style="flex-shrink: 0;">
                                <label style="visibility: hidden;">Cerrado</label>
                                <label class="special-checkbox-wrapper" title="Marcar si el estudio no abrirá este día">
                                    <input type="checkbox" id="specialClosedAllDay" onchange="toggleSpecialHours(this.checked)">
                                    <span>Cerrado todo el día</span>
                                </label>
                            </div>

                            <!-- Selector de Hora Apertura (deshabilitable) -->
                            <div class="special-field-group" style="width: 105px; flex-shrink: 0;">
                                <label for="specialOpenInput">Apertura</label>
                                <select id="specialOpenInput" class="special-form-control time-select">
                                    @foreach($timeSlots as $slot)
                                        <option value="{{ $slot }}" {{ $slot === '09:00' ? 'selected' : '' }}>{{ $slot }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Selector de Hora Cierre (deshabilitable) -->
                            <div class="special-field-group" style="width: 105px; flex-shrink: 0;">
                                <label for="specialCloseInput">Cierre</label>
                                <select id="specialCloseInput" class="special-form-control time-select">
                                    @foreach($timeSlots as $slot)
                                        <option value="{{ $slot }}" {{ $slot === '15:00' ? 'selected' : '' }}>{{ $slot }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Fila Inferior: Botón + Agregar Excepción alineado abajo -->
                        <div class="special-form-actions-row">
                            <button type="submit" class="btn-add-special" id="btnAddSpecial">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                                <span>Agregar Excepción</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Tabla de Fechas Especiales Registradas -->
                <div class="special-table-container">
                    <table class="special-custom-table" id="specialDatesTable">
                        <thead>
                            <tr>
                                <th style="width: 22%;">Fecha</th>
                                <th style="width: 38%;">Motivo</th>
                                <th style="width: 25%;">Horario Modificado</th>
                                <th style="width: 15%; text-align: right;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="specialDatesTableBody">
                            @forelse($fechasEspeciales ?? [] as $fechaEsp)
                                <tr id="special-row-{{ $fechaEsp->id }}">
                                    <td style="font-weight: 700; color: #ffffff;">
                                        {{ $fechaEsp->fecha_legible }}
                                    </td>
                                    <td>
                                        {{ $fechaEsp->motivo }}
                                    </td>
                                    <td>
                                        @if($fechaEsp->cerrado_todo_el_dia)
                                            <span class="badge-special-closed">
                                                <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <circle cx="12" cy="12" r="10"></circle>
                                                    <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line>
                                                </svg>
                                                Cerrado
                                            </span>
                                        @else
                                            <span class="badge-special-hours">
                                                <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <circle cx="12" cy="12" r="10"></circle>
                                                    <polyline points="12 6 12 12 16 14"></polyline>
                                                </svg>
                                                {{ $fechaEsp->horario_formateado }}
                                            </span>
                                        @endif
                                    </td>
                                    <td style="text-align: right;">
                                        <div class="special-actions-cell">
                                            <button type="button" class="btn-edit-special" title="Editar horario y datos de la fecha especial" 
                                                onclick="openEditSpecialDateModal({{ $fechaEsp->id }}, '{{ $fechaEsp->fecha ? \Illuminate\Support\Carbon::parse($fechaEsp->fecha)->format('Y-m-d') : '' }}', '{{ addslashes($fechaEsp->fecha_legible) }}', '{{ addslashes($fechaEsp->motivo) }}', {{ $fechaEsp->cerrado_todo_el_dia ? 'true' : 'false' }}, '{{ $fechaEsp->hora_apertura ? substr((string)$fechaEsp->hora_apertura, 0, 5) : '09:00' }}', '{{ $fechaEsp->hora_cierre ? substr((string)$fechaEsp->hora_cierre, 0, 5) : '15:00' }}')">
                                                <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                </svg>
                                            </button>
                                            <button type="button" class="btn-delete-special" title="Eliminar excepción" onclick="deleteSpecialDate({{ $fechaEsp->id }}, '{{ addslashes($fechaEsp->motivo) }}')">
                                                <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                    <line x1="10" y1="11" x2="10" y2="17"></line>
                                                    <line x1="14" y1="11" x2="14" y2="17"></line>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="special-empty-row">
                                        No hay fechas especiales registradas en la base de datos.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- MODAL: EDITAR HORARIO Y DETALLES DE FECHA ESPECIAL -->
    <div class="modal-overlay" id="modalEditSpecialDate" role="dialog" aria-modal="true" aria-labelledby="modalEditSpecialTitle">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 class="modal-title" id="modalEditSpecialTitle">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                    <span>Editar Fecha Especial</span>
                </h3>
                <button type="button" class="modal-close-btn" onclick="closeEditSpecialModal()" aria-label="Cerrar modal">&times;</button>
            </div>
            <form id="formEditSpecialDate" onsubmit="submitEditSpecialDate(event)">
                <div class="modal-body">
                    <input type="hidden" id="editSpecialId">

                    <!-- Selector de Fecha / Día Editable con máscara DD/MM/AAAA -->
                    <div class="special-field-group">
                        <label for="editSpecialDateInput">Fecha / Día de la Excepción</label>
                        <div class="special-date-input-wrap">
                            <input type="text" id="editSpecialDateInput" class="special-form-control" placeholder="DD/MM/AAAA" maxlength="10" autocomplete="off" required>
                            <input type="date" id="editSpecialDateNativePicker" style="position: absolute; opacity: 0; pointer-events: none; width: 0; height: 0;" min="{{ date('Y-m-d') }}" max="{{ date('Y') }}-12-31" tabindex="-1">
                            <button type="button" class="btn-calendar-trigger" onclick="openNativeDatePicker('editSpecialDateNativePicker')" title="Abrir calendario" aria-label="Abrir calendario">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                            </button>
                        </div>
                        <span class="special-date-help-text">Orden de captura manual: día, mes y año</span>
                    </div>

                    <!-- Motivo -->
                    <div class="special-field-group">
                        <label for="editSpecialReason">Motivo de la excepción</label>
                        <input type="text" id="editSpecialReason" class="special-form-control" required placeholder="Ej. Navidad, Aniversario, etc.">
                    </div>

                    <!-- Checkbox Cerrado todo el día -->
                    <div class="special-field-group">
                        <label class="special-checkbox-wrapper" style="justify-content: flex-start; cursor: pointer;">
                            <input type="checkbox" id="editSpecialClosedAllDay" onchange="toggleEditSpecialHours(this.checked)">
                            <span>Cerrado todo el día</span>
                        </label>
                    </div>

                    <!-- Horarios de Apertura y Cierre -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;" id="editSpecialHoursContainer">
                        <div class="special-field-group">
                            <label for="editSpecialOpen">Hora Apertura</label>
                            <select id="editSpecialOpen" class="special-form-control time-select">
                                @foreach($timeSlots as $slot)
                                    <option value="{{ $slot }}">{{ $slot }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="special-field-group">
                            <label for="editSpecialClose">Hora Cierre</label>
                            <select id="editSpecialClose" class="special-form-control time-select">
                                @foreach($timeSlots as $slot)
                                    <option value="{{ $slot }}">{{ $slot }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-modal-cancel" onclick="closeEditSpecialModal()">Cancelar</button>
                    <button type="submit" class="btn-modal-save" id="btnSaveEditSpecial">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                            <polyline points="17 21 17 13 7 13 7 21"></polyline>
                            <polyline points="7 3 7 8 15 8"></polyline>
                        </svg>
                        <span>Guardar Cambios</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- SCRIPTS DE INTERACCIÓN (FRONTEND) -->
    <script>
        const BRANCH_DAYS = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];

        document.addEventListener('DOMContentLoaded', function () {
            // Dropdown de perfil
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

            // Menú Lateral (Sidebar) Colapsar / Expandir (Desktop)
            const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
            const adminSidebar = document.getElementById('adminSidebar');

            if (sidebarToggleBtn && adminSidebar) {
                sidebarToggleBtn.addEventListener('click', function () {
                    const isCollapsed = adminSidebar.classList.toggle('collapsed');
                    sidebarToggleBtn.setAttribute('aria-expanded', isCollapsed ? 'false' : 'true');
                    sidebarToggleBtn.setAttribute('title', isCollapsed ? 'Expandir menú' : 'Colapsar menú');
                });
            }

            // Menú Lateral (Sidebar) en Móvil (Drawer)
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

            // Desactivar el menú emergente / rueda nativa del navegador al hacer clic o presionar atajos
            document.querySelectorAll('input[type="time"]').forEach(function (input) {
                input.showPicker = function () {};
                input.addEventListener('keydown', function (e) {
                    if (e.altKey && e.key === 'ArrowDown') {
                        e.preventDefault();
                    }
                });
            });

            // Escuchar cambios en los selectores de hora de apertura y cierre de la sucursal para auto-guardado
            BRANCH_DAYS.forEach(day => {
                const openSel = document.getElementById(`open-${day}`);
                const closeSel = document.getElementById(`close-${day}`);
                if (openSel) {
                    openSel.addEventListener('change', () => triggerBranchScheduleAutoSave());
                }
                if (closeSel) {
                    closeSel.addEventListener('change', () => triggerBranchScheduleAutoSave());
                }
            });
        });

        // Temporizador de auto-guardado (Debounce)
        let branchScheduleAutoSaveTimer = null;
        let isSavingBranchSchedule = false;

        function triggerBranchScheduleAutoSave() {
            clearTimeout(branchScheduleAutoSaveTimer);

            const indicator = document.getElementById('scheduleAutosaveIndicator');
            if (indicator) {
                indicator.className = 'schedule-autosave-indicator is-visible is-saving';
                indicator.innerHTML = `
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" style="animation: spin 1s linear infinite;">
                        <circle cx="12" cy="12" r="10" stroke-opacity="0.25"></circle>
                        <path d="M12 2a10 10 0 0 1 10 10" stroke-linecap="round"></path>
                    </svg>
                    <span>Sincronizando con trabajadores...</span>
                `;
            }

            branchScheduleAutoSaveTimer = setTimeout(() => {
                saveBranchSchedule(true);
            }, 600);
        }

        // Alternar estado Abierto / Cerrado de un día específico
        function toggleBranchDay(day, isOpen) {
            const row = document.getElementById(`row-${day}`);
            const badge = document.getElementById(`badge-${day}`);
            const openInput = document.getElementById(`open-${day}`);
            const closeInput = document.getElementById(`close-${day}`);

            if (!row || !badge) return;

            if (isOpen) {
                row.classList.remove('day-closed');
                badge.textContent = 'Abierto';
                badge.className = 'branch-day-status-pill status-open';
                if (openInput) openInput.disabled = false;
                if (closeInput) closeInput.disabled = false;
            } else {
                row.classList.add('day-closed');
                badge.textContent = 'Cerrado';
                badge.className = 'branch-day-status-pill status-closed';
                if (openInput) openInput.disabled = true;
                if (closeInput) closeInput.disabled = true;
            }

            updateSummaryCount();
            triggerBranchScheduleAutoSave();
        }

        // Actualizar contador del pill en el encabezado
        function updateSummaryCount() {
            let openCount = 0;
            let closedCount = 0;

            BRANCH_DAYS.forEach(day => {
                const toggle = document.getElementById(`toggle-${day}`);
                if (toggle && toggle.checked) {
                    openCount++;
                } else {
                    closedCount++;
                }
            });

            const summaryText = document.getElementById('scheduleSummaryText');
            if (summaryText) {
                summaryText.textContent = `${openCount} Días Abiertos • ${closedCount} Días Cerrados`;
            }
        }

        // Guardar Horarios de la Sucursal en Base de Datos (AJAX)
        // isAuto: si es true, es disparo automático en tiempo real sin bloquear modal
        async function saveBranchSchedule(isAuto = false) {
            if (isSavingBranchSchedule) return;
            isSavingBranchSchedule = true;

            const btn = document.getElementById('btnSaveSchedule');
            const originalHtml = btn ? btn.innerHTML : '';
            const indicator = document.getElementById('scheduleAutosaveIndicator');

            const dias = {};
            BRANCH_DAYS.forEach(day => {
                const toggle = document.getElementById(`toggle-${day}`);
                const openSel = document.getElementById(`open-${day}`);
                const closeSel = document.getElementById(`close-${day}`);

                dias[day] = {
                    abierto: toggle ? toggle.checked : true,
                    apertura: openSel ? openSel.value : '09:00',
                    cierre: closeSel ? closeSel.value : '20:00'
                };
            });

            if (!isAuto && btn) {
                btn.disabled = true;
                btn.innerHTML = `
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" style="animation: spin 1s linear infinite;">
                        <circle cx="12" cy="12" r="10" stroke-opacity="0.25"></circle>
                        <path d="M12 2a10 10 0 0 1 10 10" stroke-linecap="round"></path>
                    </svg>
                    <span>Guardando...</span>
                `;
            }

            if (indicator) {
                indicator.className = 'schedule-autosave-indicator is-visible is-saving';
                indicator.innerHTML = `
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" style="animation: spin 1s linear infinite;">
                        <circle cx="12" cy="12" r="10" stroke-opacity="0.25"></circle>
                        <path d="M12 2a10 10 0 0 1 10 10" stroke-linecap="round"></path>
                    </svg>
                    <span>Sincronizando con trabajadores...</span>
                `;
            }

            try {
                const response = await fetch('{{ route("admin.configuracion.horarios.save") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ dias })
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    if (indicator) {
                        indicator.className = 'schedule-autosave-indicator is-visible is-saved';
                        indicator.innerHTML = `
                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 6L9 17l-5-5"></path>
                            </svg>
                            <span>Horarios de trabajadores actualizados</span>
                        `;
                        setTimeout(() => {
                            if (indicator.classList.contains('is-saved')) {
                                indicator.classList.remove('is-visible');
                            }
                        }, 3000);
                    }

                    if (!isAuto) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: result.message || '¡Horarios de la sucursal actualizados exitosamente!',
                            showConfirmButton: false,
                            timer: 3500,
                            timerProgressBar: true,
                            background: '#10b981',
                            color: '#ffffff',
                            iconColor: '#ffffff'
                        });
                    }
                } else {
                    if (indicator) {
                        indicator.className = 'schedule-autosave-indicator is-visible';
                        indicator.style.color = '#ef4444';
                        indicator.innerHTML = `<span>Error al sincronizar</span>`;
                    }
                    if (!isAuto) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error al guardar horarios',
                            text: result.message || 'No se pudieron actualizar los horarios de la sucursal.',
                            background: '#1e293b',
                            color: '#ffffff',
                            confirmButtonColor: '#ef4444'
                        });
                    }
                }
            } catch (err) {
                if (indicator) {
                    indicator.className = 'schedule-autosave-indicator is-visible';
                    indicator.style.color = '#ef4444';
                    indicator.innerHTML = `<span>Error de conexión</span>`;
                }
                if (!isAuto) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de conexión',
                        text: 'No se pudo conectar con el servidor para guardar los horarios.',
                        background: '#1e293b',
                        color: '#ffffff',
                        confirmButtonColor: '#ef4444'
                    });
                }
            } finally {
                isSavingBranchSchedule = false;
                if (!isAuto && btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            }
        }

        // Deshabilitar visualmente y funcionalmente selectores de hora si está cerrado todo el día
        function toggleSpecialHours(isClosed) {
            const openSel = document.getElementById('specialOpenInput');
            const closeSel = document.getElementById('specialCloseInput');

            if (openSel) {
                openSel.disabled = isClosed;
                openSel.style.opacity = isClosed ? '0.35' : '1';
                openSel.style.cursor = isClosed ? 'not-allowed' : 'pointer';
            }
            if (closeSel) {
                closeSel.disabled = isClosed;
                closeSel.style.opacity = isClosed ? '0.35' : '1';
                closeSel.style.cursor = isClosed ? 'not-allowed' : 'pointer';
            }
        }

        // Registrar nueva Fecha Especial (AJAX)
        async function submitSpecialDate(e) {
            if (e) e.preventDefault();

            const dateInput = document.getElementById('specialDateInput');
            const reasonInput = document.getElementById('specialReasonInput');
            const closedCheckbox = document.getElementById('specialClosedAllDay');
            const openSel = document.getElementById('specialOpenInput');
            const closeSel = document.getElementById('specialCloseInput');
            const submitBtn = document.getElementById('btnAddSpecial');

            let rawDateValue = dateInput.value.trim();
            if (!rawDateValue) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Fecha requerida',
                    text: 'Por favor ingresa la fecha de la excepción en formato DD/MM/AAAA.',
                    background: '#1e293b',
                    color: '#ffffff',
                    confirmButtonColor: '#0055ff'
                });
                dateInput.focus();
                return;
            }

            if (!validateSpecialDateField(dateInput)) {
                dateInput.focus();
                return;
            }

            // Convertir de DD/MM/AAAA a YYYY-MM-DD para la petición AJAX al backend
            const fecha = formatDmyToYmd(dateInput.value.trim());
            const motivo = reasonInput.value.trim();
            const cerrado_todo_el_dia = closedCheckbox.checked;
            const hora_apertura = cerrado_todo_el_dia ? null : openSel.value;
            const hora_cierre = cerrado_todo_el_dia ? null : closeSel.value;

            if (!motivo) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Motivo requerido',
                    text: 'Por favor escribe el motivo de la excepción (ej. Navidad, Día Festivo).',
                    background: '#1e293b',
                    color: '#ffffff',
                    confirmButtonColor: '#0055ff'
                });
                reasonInput.focus();
                return;
            }

            if (!cerrado_todo_el_dia && hora_apertura >= hora_cierre) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Horario inválido',
                    text: 'La hora de apertura debe ser anterior a la hora de cierre.',
                    background: '#1e293b',
                    color: '#ffffff',
                    confirmButtonColor: '#0055ff'
                });
                return;
            }

            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = `<span>Guardando...</span>`;

            try {
                const response = await fetch('{{ route("admin.configuracion.fechas_especiales.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        fecha,
                        motivo,
                        cerrado_todo_el_dia,
                        hora_apertura,
                        hora_cierre
                    })
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: result.message || 'Fecha especial agregada exitosamente.',
                        showConfirmButton: false,
                        timer: 3500,
                        timerProgressBar: true,
                        background: '#10b981',
                        color: '#ffffff',
                        iconColor: '#ffffff'
                    });

                    // Limpiar campos del formulario
                    dateInput.value = '';
                    reasonInput.value = '';
                    closedCheckbox.checked = false;
                    toggleSpecialHours(false);

                    setTimeout(() => window.location.reload(), 900);
                } else {
                    let errorMsg = result.message || 'No se pudo guardar la fecha especial.';
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
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        }

        // Eliminar Fecha Especial (AJAX)
        async function deleteSpecialDate(id, motivo) {
            const confirmResult = await Swal.fire({
                title: '¿Eliminar fecha especial?',
                text: `Se eliminará la excepción para "${motivo}".`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                background: '#1e293b',
                color: '#ffffff'
            });

            if (!confirmResult.isConfirmed) return;

            try {
                const response = await fetch(`/admin/configuracion/fechas-especiales/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: result.message || 'Fecha especial eliminada.',
                        showConfirmButton: false,
                        timer: 3500,
                        timerProgressBar: true,
                        background: '#10b981',
                        color: '#ffffff',
                        iconColor: '#ffffff'
                    });

                    const row = document.getElementById(`special-row-${id}`);
                    if (row) {
                        row.remove();
                        const tbody = document.getElementById('specialDatesTableBody');
                        if (tbody && tbody.children.length === 0) {
                            tbody.innerHTML = `<tr><td colspan="4" class="special-empty-row">No hay fechas especiales registradas en la base de datos.</td></tr>`;
                        }
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: result.message || 'No se pudo eliminar el registro.',
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

        // ===================================================
        // EDICIÓN DE FECHAS ESPECIALES (MODAL & AJAX)
        // ===================================================
        function openEditSpecialDateModal(id, fechaRaw, fechaLegible, motivo, cerrado, horaApertura, horaCierre) {
            document.getElementById('editSpecialId').value = id;
            
            const dateInput = document.getElementById('editSpecialDateInput');
            if (dateInput) {
                dateInput.value = formatYmdToDmy(fechaRaw) || '';
            }

            document.getElementById('editSpecialReason').value = motivo || '';
            
            const closedCheckbox = document.getElementById('editSpecialClosedAllDay');
            closedCheckbox.checked = !!cerrado;
            
            const openSel = document.getElementById('editSpecialOpen');
            const closeSel = document.getElementById('editSpecialClose');
            if (openSel && horaApertura) openSel.value = horaApertura;
            if (closeSel && horaCierre) closeSel.value = horaCierre;

            toggleEditSpecialHours(!!cerrado);

            const modal = document.getElementById('modalEditSpecialDate');
            if (modal) {
                modal.classList.add('open');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeEditSpecialModal() {
            const modal = document.getElementById('modalEditSpecialDate');
            if (modal) {
                modal.classList.remove('open');
                document.body.style.overflow = '';
            }
        }

        function toggleEditSpecialHours(isClosed) {
            const openSel = document.getElementById('editSpecialOpen');
            const closeSel = document.getElementById('editSpecialClose');

            if (openSel) {
                openSel.disabled = isClosed;
                openSel.style.opacity = isClosed ? '0.35' : '1';
                openSel.style.cursor = isClosed ? 'not-allowed' : 'pointer';
            }
            if (closeSel) {
                closeSel.disabled = isClosed;
                closeSel.style.opacity = isClosed ? '0.35' : '1';
                closeSel.style.cursor = isClosed ? 'not-allowed' : 'pointer';
            }
        }

        async function submitEditSpecialDate(e) {
            if (e) e.preventDefault();

            const id = document.getElementById('editSpecialId').value;
            const dateInput = document.getElementById('editSpecialDateInput');
            const motivo = document.getElementById('editSpecialReason').value.trim();
            const cerrado = document.getElementById('editSpecialClosedAllDay').checked;
            const horaApertura = cerrado ? null : document.getElementById('editSpecialOpen').value;
            const horaCierre = cerrado ? null : document.getElementById('editSpecialClose').value;
            const submitBtn = document.getElementById('btnSaveEditSpecial');

            const rawDate = dateInput ? dateInput.value.trim() : '';
            if (!rawDate) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Fecha requerida',
                    text: 'Por favor ingresa la fecha de la excepción en formato DD/MM/AAAA.',
                    background: '#1e293b',
                    color: '#ffffff',
                    confirmButtonColor: '#0055ff'
                });
                if (dateInput) dateInput.focus();
                return;
            }

            if (!validateSpecialDateField(dateInput)) {
                if (dateInput) dateInput.focus();
                return;
            }

            const fecha = formatDmyToYmd(rawDate);

            if (!motivo) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Motivo requerido',
                    text: 'Por favor escribe el motivo de la excepción.',
                    background: '#1e293b',
                    color: '#ffffff',
                    confirmButtonColor: '#0055ff'
                });
                return;
            }

            if (!cerrado && horaApertura >= horaCierre) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Horario inválido',
                    text: 'La hora de apertura debe ser anterior a la hora de cierre.',
                    background: '#1e293b',
                    color: '#ffffff',
                    confirmButtonColor: '#0055ff'
                });
                return;
            }

            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = `<span>Guardando...</span>`;

            try {
                const response = await fetch(`/admin/configuracion/fechas-especiales/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        fecha: fecha,
                        motivo: motivo,
                        cerrado_todo_el_dia: cerrado,
                        hora_apertura: horaApertura,
                        hora_cierre: horaCierre
                    })
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: result.message || 'Fecha especial actualizada exitosamente.',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        background: '#10b981',
                        color: '#ffffff',
                        iconColor: '#ffffff'
                    });

                    closeEditSpecialModal();

                    // Actualizar dinámicamente la fila en la tabla
                    const row = document.getElementById(`special-row-${id}`);
                    if (row && result.data) {
                        const data = result.data;
                        const badgeHtml = data.cerrado_todo_el_dia
                            ? `<span class="badge-special-closed">
                                <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line>
                                </svg>
                                Cerrado
                               </span>`
                            : `<span class="badge-special-hours">
                                <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                ${data.horario_formateado}
                               </span>`;

                        const escapedMotivo = data.motivo.replace(/'/g, "\\'");
                        const escapedFechaLegible = data.fecha_legible.replace(/'/g, "\\'");
                        const openTime = data.hora_apertura || '09:00';
                        const closeTime = data.hora_cierre || '15:00';

                        row.innerHTML = `
                            <td style="font-weight: 700; color: #ffffff;">
                                ${data.fecha_legible}
                            </td>
                            <td>
                                ${data.motivo}
                            </td>
                            <td>
                                ${badgeHtml}
                            </td>
                            <td style="text-align: right;">
                                <div class="special-actions-cell">
                                    <button type="button" class="btn-edit-special" title="Editar horario y datos de la fecha especial" 
                                        onclick="openEditSpecialDateModal(${data.id}, '${data.fecha}', '${escapedFechaLegible}', '${escapedMotivo}', ${data.cerrado_todo_el_dia ? 'true' : 'false'}, '${openTime}', '${closeTime}')">
                                        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                        </svg>
                                    </button>
                                    <button type="button" class="btn-delete-special" title="Eliminar excepción" onclick="deleteSpecialDate(${data.id}, '${escapedMotivo}')">
                                        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                            <line x1="10" y1="11" x2="10" y2="17"></line>
                                            <line x1="14" y1="11" x2="14" y2="17"></line>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        `;
                    } else {
                        setTimeout(() => window.location.reload(), 800);
                    }
                } else {
                    let errorMsg = result.message || 'No se pudo actualizar la fecha especial.';
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
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        }

        // ===================================================
        // MANEJO Y VALIDACIÓN DE INPUTS DE FECHAS ESPECIALES
        // Formato DD/MM/AAAA, máscara estricta, validación diferida (onBlur / 8 dígitos)
        // y restricción estricta al año actual del sistema.
        // ===================================================

        // Función para abrir el selector nativo de fecha asociado
        function openNativeDatePicker(pickerId) {
            const picker = document.getElementById(pickerId);
            if (!picker) return;
            if (typeof picker.showPicker === 'function') {
                picker.showPicker();
            } else {
                picker.click();
            }
        }

        // Convierte fecha YYYY-MM-DD a DD/MM/AAAA
        function formatYmdToDmy(ymd) {
            if (!ymd || !ymd.includes('-')) return '';
            const parts = ymd.split('-');
            if (parts.length !== 3) return '';
            return `${parts[2]}/${parts[1]}/${parts[0]}`;
        }

        // Convierte fecha DD/MM/AAAA a YYYY-MM-DD
        function formatDmyToYmd(dmy) {
            if (!dmy || !dmy.includes('/')) return '';
            const parts = dmy.split('/');
            if (parts.length !== 3) return '';
            return `${parts[2]}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}`;
        }

        // Validación estricta diferida (al perder foco o al completar los 8 dígitos)
        function validateSpecialDateField(input) {
            const val = input.value.trim();
            if (!val) return true;

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
                return false;
            }

            // Validar que no sea fecha pasada dentro del mismo año
            const todayStart = new Date(currentYear, now.getMonth(), now.getDate()).getTime();
            if (testDate.getTime() < todayStart) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Fecha no permitida',
                    text: 'No puedes seleccionar fechas de días pasados.',
                    background: '#1e293b',
                    color: '#ffffff',
                    confirmButtonColor: '#0055ff'
                });
                input.value = '';
                return false;
            }

            return true;
        }

        // Aplicar máscara DD/MM/AAAA y configurar eventos deferred
        function setupDateInputMaskAndEvents(textInputId, nativePickerId) {
            const textInput = document.getElementById(textInputId);
            const nativePicker = document.getElementById(nativePickerId);

            if (!textInput) return;

            // Evento input: formateo dinámico con máscara DD/MM/AAAA sin disparar alertas prematuras
            textInput.addEventListener('input', function (e) {
                // Solo dígitos y máximo 8 dígitos (DDMMAAAA)
                let rawDigits = this.value.replace(/\D/g, '').slice(0, 8);
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

                this.value = formatted;

                // Si ha completado exactamente los 8 dígitos (10 caracteres "DD/MM/AAAA"), validar
                if (formatted.length === 10) {
                    validateSpecialDateField(this);
                }
            });

            // Evento blur: validar cuando pierde el foco si hay contenido
            textInput.addEventListener('blur', function () {
                if (this.value.trim().length > 0) {
                    validateSpecialDateField(this);
                }
            });

            // Sincronización cuando se selecciona una fecha mediante el selector nativo del icono de calendario
            if (nativePicker) {
                nativePicker.addEventListener('change', function () {
                    if (this.value) {
                        textInput.value = formatYmdToDmy(this.value);
                        validateSpecialDateField(textInput);
                    }
                });
            }
        }

        // Inicializar máscaras de fechas al cargar el DOM
        document.addEventListener('DOMContentLoaded', function () {
            setupDateInputMaskAndEvents('specialDateInput', 'specialDateNativePicker');
            setupDateInputMaskAndEvents('editSpecialDateInput', 'editSpecialDateNativePicker');
        });
    </script>
</body>
</html>
