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
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

            /* Paleta cromática estricta (Red, White, Blue) */
            --barber-red: #ef4444;        /* Rojo vivo y vibrante */
            --barber-red-light: rgba(239, 68, 68, 0.1);
            --barber-blue: #0055ff;       /* Azul eléctrico vibrante */
            --barber-blue-light: #3377ff; 
            --barber-blue-pale: rgba(0, 85, 255, 0.15);

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
            overflow-x: hidden;
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
            border-color: rgba(0, 85, 255, 0.3);
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
            overflow-x: hidden;
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
            overflow: hidden;
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
           DASHBOARD COMPONENTES (TARJETAS Y TABLAS)
           =================================================== */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: var(--bg-header);
            border: 1px solid var(--border-subtle);
            border-radius: 12px;
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 20px;
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.4);
            border-color: var(--border-strong);
        }

        /* Indicador decorativo lateral */
        .stat-card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: var(--text-white);
            opacity: 0.8;
        }
        .stat-card.card-blue::before {
            background: var(--barber-blue);
        }
        .stat-card.card-red::before {
            background: var(--barber-red);
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: var(--text-white);
        }
        
        .stat-card.card-blue .stat-icon {
            color: var(--barber-blue);
            background: var(--barber-blue-pale);
            border-color: rgba(0, 85, 255, 0.2);
        }
        
        .stat-card.card-red .stat-icon {
            color: var(--barber-red);
            background: var(--barber-red-light);
            border-color: rgba(239, 68, 68, 0.2);
        }

        .stat-icon svg {
            width: 28px;
            height: 28px;
        }

        .stat-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .stat-title {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 800;
            color: var(--text-white);
            line-height: 1;
        }

        /* Sección Inferior: Tablas y Acciones */
        .dashboard-layout-bottom {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
        }

        .panel-card {
            background: var(--bg-header);
            border: 1px solid var(--border-subtle);
            border-radius: 12px;
            padding: 24px;
        }

        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .panel-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-white);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .panel-title svg {
            color: var(--barber-blue);
        }

        .btn-outline {
            background: transparent;
            color: var(--text-white);
            border: 1px solid var(--border-subtle);
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: var(--text-white);
        }

        /* Tabla Estilizada */
        .data-table-wrapper {
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .data-table th {
            padding: 12px 16px;
            font-size: 12px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid var(--border-subtle);
            background: rgba(255, 255, 255, 0.02);
        }

        .data-table td {
            padding: 16px;
            font-size: 14px;
            color: var(--text-white);
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            vertical-align: middle;
        }

        .data-table tr:hover td {
            background: rgba(255, 255, 255, 0.02);
        }

        .data-table tr:last-child td {
            border-bottom: none;
        }

        .status-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .status-pending {
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-white);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .status-confirmed {
            background: var(--barber-blue-pale);
            color: var(--barber-blue-light);
            border: 1px solid rgba(0, 85, 255, 0.3);
        }

        .status-cancelled {
            background: var(--barber-red-light);
            color: var(--barber-red);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        /* Lista de acciones rápidas */
        .action-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .action-card {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--border-subtle);
            border-radius: 8px;
            text-decoration: none;
            color: var(--text-white);
            transition: all 0.2s ease;
        }

        .action-card:hover {
            background: var(--barber-blue-pale);
            border-color: rgba(0, 85, 255, 0.3);
            transform: translateX(4px);
        }

        .action-card .action-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.05);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--barber-blue);
        }
        
        .action-card.action-red:hover {
            background: var(--barber-red-light);
            border-color: rgba(239, 68, 68, 0.3);
        }
        
        .action-card.action-red .action-icon {
            color: var(--barber-red);
        }

        .action-content h4 {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 2px;
        }

        .action-content p {
            font-size: 12px;
            color: var(--text-muted);
        }

        @media (max-width: 1024px) {
            .dashboard-layout-bottom {
                grid-template-columns: 1fr;
            }
        }

        /* ===================================================
           MODALES Y FORMULARIOS (DISEÑO COHERENTE CON TRABAJADORES)
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
            max-width: 580px;
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
            position: sticky;
            bottom: 0;
            z-index: 5;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        @media (max-width: 540px) {
            .form-row {
                grid-template-columns: 1fr;
            }
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
            background: rgba(0, 85, 255, 0.03);
        }

        /* Estilos estrictos para desplegables (evita texto blanco sobre fondo blanco nativo) */
        select,
        select.form-control {
            color-scheme: dark !important;
            background-color: #0d1527 !important;
            color: #ffffff !important;
        }

        select:focus,
        select.form-control:focus {
            background-color: #0d1527 !important;
            color: #ffffff !important;
        }

        select option,
        select.form-control option {
            background-color: #0d1527 !important;
            color: #ffffff !important;
            font-size: 14px;
            padding: 8px 12px;
        }

        select option:disabled,
        select.form-control option:disabled {
            background-color: #0d1527 !important;
            color: #64748b !important;
        }

        select option:checked,
        select.form-control option:checked {
            background-color: #0055ff !important;
            color: #ffffff !important;
        }

        .password-input-group {
            position: relative;
            width: 100%;
            display: flex;
            align-items: center;
        }

        .password-input-group .form-control {
            padding-right: 42px;
        }

        .toggle-password-btn {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            transition: color 0.2s ease;
        }

        .toggle-password-btn:hover {
            color: var(--text-white);
        }

        .toggle-password-btn svg {
            width: 18px;
            height: 18px;
        }

        .hidden {
            display: none !important;
        }

        .btn-cancel {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--border-subtle);
            color: var(--text-muted);
            padding: 10px 18px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            font-family: inherit;
        }

        .btn-cancel:hover {
            background: rgba(255, 255, 255, 0.08);
            color: var(--text-white);
        }

        .btn-submit-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, var(--barber-blue) 0%, var(--barber-blue-light) 100%);
            color: var(--text-white);
            padding: 10px 22px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(0, 85, 255, 0.35);
            transition: all 0.25s var(--ease-fluid);
            font-family: inherit;
        }

        .btn-submit-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 85, 255, 0.5);
            filter: brightness(1.1);
        }

        .btn-submit-action:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }

        .btn-submit-action.btn-submit-continue {
            background: linear-gradient(135deg, #0284c7 0%, #06b6d4 100%);
            box-shadow: 0 4px 14px rgba(6, 182, 212, 0.35);
        }

        .btn-submit-action.btn-submit-continue:hover {
            box-shadow: 0 8px 24px rgba(6, 182, 212, 0.5);
        }

        .role-info-card {
            background: rgba(0, 85, 255, 0.06);
            border: 1px solid rgba(0, 85, 255, 0.2);
            border-radius: 8px;
            padding: 12px 14px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 12px;
            color: #93c5fd;
            line-height: 1.4;
            transition: all 0.25s ease;
        }

        .role-info-card.role-info-worker {
            background: rgba(6, 182, 212, 0.08);
            border-color: rgba(6, 182, 212, 0.3);
            color: #67e8f9;
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
                    <a href="{{ route('admin.usuarios') }}" class="nav-item active" aria-current="page" title="Usuarios">
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

        <!-- ÁREA DE CONTENIDO PRINCIPAL -->
        <main id="main-content" class="main-content" role="main">
            <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
                <div class="page-title">
                    <h1 style="font-size: 24px; font-weight: 700; color: var(--text-white); letter-spacing: -0.03em;">Gestión de Usuarios</h1>
                    <p style="color: var(--text-muted); font-size: 14px; margin-top: 4px;">Administra los usuarios registrados en el sistema y sus roles correspondientes.</p>
                </div>
                <div class="page-actions" style="display: flex; gap: 12px;">
                    <button type="button" class="btn-primary" style="background: var(--barber-blue); color: var(--text-white); border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: all 0.2s ease; box-shadow: 0 4px 12px rgba(0, 85, 255, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(0, 85, 255, 0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(0, 85, 255, 0.3)';" onclick="openModal('modalCreateUser');">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        Agregar Usuario
                    </button>
                </div>
            </div>

            <!-- Stats Overview Cards -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 24px;">
                <div class="panel-card" style="padding: 16px 20px; display: flex; align-items: center; gap: 14px;">
                    <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(0, 85, 255, 0.12); color: var(--barber-blue); display: flex; align-items: center; justify-content: center;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    </div>
                    <div>
                        <div style="font-size: 12px; color: var(--text-muted); font-weight: 500;">Total Usuarios</div>
                        <div style="font-size: 20px; font-weight: 700; color: var(--text-white);" id="statTotalUsers">{{ $stats['total'] ?? count($users) }}</div>
                    </div>
                </div>

                <div class="panel-card" style="padding: 16px 20px; display: flex; align-items: center; gap: 14px;">
                    <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(56, 189, 248, 0.12); color: #38bdf8; display: flex; align-items: center; justify-content: center;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    </div>
                    <div>
                        <div style="font-size: 12px; color: var(--text-muted); font-weight: 500;">Administradores</div>
                        <div style="font-size: 20px; font-weight: 700; color: var(--text-white);" id="statAdminUsers">{{ $stats['admins'] ?? 0 }}</div>
                    </div>
                </div>

                <div class="panel-card" style="padding: 16px 20px; display: flex; align-items: center; gap: 14px;">
                    <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(52, 211, 153, 0.12); color: #34d399; display: flex; align-items: center; justify-content: center;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                    </div>
                    <div>
                        <div style="font-size: 12px; color: var(--text-muted); font-weight: 500;">Staff / Barberos</div>
                        <div style="font-size: 20px; font-weight: 700; color: var(--text-white);" id="statTrabajadorUsers">{{ $stats['trabajadores'] ?? 0 }}</div>
                    </div>
                </div>

                <div class="panel-card" style="padding: 16px 20px; display: flex; align-items: center; gap: 14px;">
                    <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(192, 132, 252, 0.12); color: #c084fc; display: flex; align-items: center; justify-content: center;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                    </div>
                    <div>
                        <div style="font-size: 12px; color: var(--text-muted); font-weight: 500;">Recepcionistas</div>
                        <div style="font-size: 20px; font-weight: 700; color: var(--text-white);" id="statRecepcionUsers">{{ $stats['recepcionistas'] ?? 0 }}</div>
                    </div>
                </div>

                <div class="panel-card" style="padding: 16px 20px; display: flex; align-items: center; gap: 14px;">
                    <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(251, 191, 36, 0.12); color: #fbbf24; display: flex; align-items: center; justify-content: center;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    </div>
                    <div>
                        <div style="font-size: 12px; color: var(--text-muted); font-weight: 500;">Clientes</div>
                        <div style="font-size: 20px; font-weight: 700; color: var(--text-white);" id="statClienteUsers">{{ $stats['clientes'] ?? 0 }}</div>
                    </div>
                </div>
            </div>

            <!-- Toolbar: Search and Filters -->
            <div class="toolbar-card panel-card" style="margin-bottom: 24px; padding: 16px; display: flex; flex-wrap: wrap; gap: 16px; align-items: center; justify-content: space-between;">
                <div class="search-box" style="position: relative; flex: 1; min-width: 280px;">
                    <svg style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted);" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <input type="text" id="userSearchInput" placeholder="Buscar por nombre, correo, teléfono o ID..." style="width: 100%; background: rgba(255,255,255,0.02); border: 1px solid var(--border-subtle); color: var(--text-white); padding: 10px 10px 10px 40px; border-radius: 8px; font-family: inherit; font-size: 14px; outline: none; transition: all 0.2s ease;" onfocus="this.style.borderColor='var(--barber-blue)'; this.style.background='rgba(0,85,255,0.05)';" onblur="this.style.borderColor='var(--border-subtle)'; this.style.background='rgba(255,255,255,0.02)';">
                </div>
                
                <div class="filters" style="display: flex; gap: 12px; flex-wrap: wrap;">
                    <div style="position: relative;">
                        <select id="roleFilterSelect" style="background-color: #0d1527; color-scheme: dark; border: 1px solid var(--border-subtle); color: var(--text-main); padding: 10px 36px 10px 16px; border-radius: 8px; font-family: inherit; font-size: 14px; outline: none; appearance: none; min-width: 160px; cursor: pointer; transition: all 0.2s ease;" onfocus="this.style.borderColor='var(--barber-blue)';" onblur="this.style.borderColor='var(--border-subtle)';">
                            <option value="" style="background-color: #0d1527; color: #ffffff;">Todos los Roles</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" style="background-color: #0d1527; color: #ffffff;">{{ $role->nombre }}</option>
                            @endforeach
                        </select>
                        <svg style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); pointer-events: none; color: var(--text-muted);" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </div>
                    
                    <div style="position: relative;">
                        <select id="statusFilterSelect" style="background-color: #0d1527; color-scheme: dark; border: 1px solid var(--border-subtle); color: var(--text-main); padding: 10px 36px 10px 16px; border-radius: 8px; font-family: inherit; font-size: 14px; outline: none; appearance: none; min-width: 150px; cursor: pointer; transition: all 0.2s ease;" onfocus="this.style.borderColor='var(--barber-blue)';" onblur="this.style.borderColor='var(--border-subtle)';">
                            <option value="" style="background-color: #0d1527; color: #ffffff;">Todos los Estados</option>
                            <option value="activo" style="background-color: #0d1527; color: #ffffff;">Activo</option>
                            <option value="inactivo" style="background-color: #0d1527; color: #ffffff;">Inactivo</option>
                        </select>
                        <svg style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); pointer-events: none; color: var(--text-muted);" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </div>

                    <button type="button" id="resetFiltersBtn" style="background: transparent; border: 1px solid var(--border-subtle); color: var(--text-muted); padding: 10px 16px; border-radius: 8px; font-family: inherit; font-size: 14px; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: all 0.2s ease;" onmouseover="this.style.color='var(--text-white)'; this.style.borderColor='var(--barber-blue)';" onmouseout="this.style.color='var(--text-muted)'; this.style.borderColor='var(--border-subtle)';">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path><path d="M3 3v5h5"></path></svg>
                        Limpiar
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="panel-card" style="padding: 0; overflow: hidden;">
                <div class="data-table-wrapper">
                    <table class="data-table" id="usersTable">
                        <thead>
                            <tr>
                                <th style="padding-left: 24px;">ID</th>
                                <th>Usuario</th>
                                <th>Rol asignado</th>
                                <th>Teléfono</th>
                                <th>Estado</th>
                                <th>Fecha de Registro</th>
                                <th style="text-align: right; padding-right: 24px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody">
                            @forelse($users as $user)
                                @php
                                    $firstName = $user->name ?? '';
                                    $lastName = $user->apellidos ?? '';
                                    $fullName = trim($firstName . ' ' . $lastName);
                                    
                                    // Initials
                                    $initials = strtoupper(mb_substr($firstName, 0, 1) . mb_substr($lastName, 0, 1));
                                    if (empty(trim($initials))) {
                                        $initials = strtoupper(mb_substr($user->email ?? 'U', 0, 2));
                                    }

                                    // Role styling
                                    $roleId = (int)$user->role_id;
                                    $roleName = $user->role->nombre ?? 'Sin Rol';
                                    $roleColor = match($roleId) {
                                        1 => ['text' => '#38bdf8', 'bg' => 'rgba(56, 189, 248, 0.12)', 'border' => 'rgba(56, 189, 248, 0.3)'],
                                        2 => ['text' => '#c084fc', 'bg' => 'rgba(192, 132, 252, 0.12)', 'border' => 'rgba(192, 132, 252, 0.3)'],
                                        3 => ['text' => '#34d399', 'bg' => 'rgba(52, 211, 153, 0.12)', 'border' => 'rgba(52, 211, 153, 0.3)'],
                                        4 => ['text' => '#fbbf24', 'bg' => 'rgba(251, 191, 36, 0.12)', 'border' => 'rgba(251, 191, 36, 0.3)'],
                                        default => ['text' => '#94a3b8', 'bg' => 'rgba(148, 163, 184, 0.12)', 'border' => 'rgba(148, 163, 184, 0.3)']
                                    };

                                    // Active state
                                    $isActive = $user->trabajador ? (bool)$user->trabajador->activo : true;
                                    $statusStr = $isActive ? 'activo' : 'inactivo';

                                    // Worker photo if exists
                                    $photoUrl = ($user->trabajador && !empty($user->trabajador->fotografia)) 
                                        ? asset('storage/' . $user->trabajador->fotografia) 
                                        : null;
                                @endphp

                                <tr class="user-row" 
                                    data-id="{{ $user->id }}"
                                    data-name="{{ strtolower($fullName) }}"
                                    data-email="{{ strtolower($user->email) }}"
                                    data-phone="{{ strtolower($user->telefono ?? '') }}"
                                    data-role-id="{{ $user->role_id }}"
                                    data-status="{{ $statusStr }}"
                                    style="transition: background 0.2s ease;">
                                    
                                    <!-- ID -->
                                    <td style="padding-left: 24px; color: var(--text-muted); font-size: 13px; font-weight: 600;">
                                        #{{ $user->id }}
                                    </td>

                                    <!-- Usuario (Avatar + Nombre + Email) -->
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 14px;">
                                            @if($photoUrl)
                                                <img src="{{ $photoUrl }}" alt="{{ $fullName }}" style="width: 40px; height: 40px; border-radius: 10px; object-fit: cover; border: 1px solid {{ $roleColor['border'] }};">
                                            @else
                                                <div class="user-avatar" style="width: 40px; height: 40px; border-radius: 10px; background: {{ $roleColor['bg'] }}; color: {{ $roleColor['text'] }}; border: 1px solid {{ $roleColor['border'] }}; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px;">
                                                    {{ $initials }}
                                                </div>
                                            @endif
                                            <div>
                                                <div style="font-weight: 600; color: var(--text-white); font-size: 14px;">{{ $fullName }}</div>
                                                <div style="font-size: 13px; color: var(--text-muted); margin-top: 2px;">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Rol -->
                                    <td>
                                        <span style="background: {{ $roleColor['bg'] }}; border: 1px solid {{ $roleColor['border'] }}; color: {{ $roleColor['text'] }}; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                                            <span style="width: 6px; height: 6px; border-radius: 50%; background: {{ $roleColor['text'] }};"></span>
                                            {{ $roleName }}
                                        </span>
                                    </td>

                                    <!-- Teléfono -->
                                    <td style="color: var(--text-main); font-size: 13px;">
                                        {{ $user->telefono ?: '—' }}
                                    </td>

                                    <!-- Estado -->
                                    <td>
                                        @if($isActive)
                                            <span class="status-badge status-confirmed">Activo</span>
                                        @else
                                            <span class="status-badge status-cancelled">Inactivo</span>
                                        @endif
                                    </td>

                                    <!-- Fecha Registro -->
                                    <td style="color: var(--text-muted); font-size: 13px;">
                                        {{ $user->created_at ? $user->created_at->format('d M Y, H:i') : 'Sin registro' }}
                                    </td>

                                    <!-- Acciones -->
                                    <td style="text-align: right; padding-right: 24px;">
                                        <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                            <button type="button" class="btn-action-view" title="Ver Detalles" 
                                                data-id="{{ $user->id }}"
                                                data-fullname="{{ $fullName }}"
                                                data-email="{{ $user->email }}"
                                                data-phone="{{ $user->telefono ?: 'No registrado' }}"
                                                data-birth="{{ $user->fecha_nacimiento ?: 'No registrada' }}"
                                                data-role="{{ $roleName }}"
                                                data-status="{{ $isActive ? 'Activo' : 'Inactivo' }}"
                                                data-created="{{ $user->created_at ? $user->created_at->format('d/m/Y H:i:s') : 'N/A' }}"
                                                onclick="showUserDetailModalFromBtn(this)"
                                                style="background: transparent; border: 1px solid var(--border-subtle); color: var(--text-muted); width: 34px; height: 34px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s ease;" 
                                                onmouseover="this.style.color='var(--barber-blue)'; this.style.borderColor='rgba(0,85,255,0.3)'; this.style.background='rgba(0,85,255,0.05)';" 
                                                onmouseout="this.style.color='var(--text-muted)'; this.style.borderColor='var(--border-subtle)'; this.style.background='transparent';">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                            </button>
                                            <button type="button" class="btn-action-edit" title="Editar Usuario" 
                                                data-id="{{ $user->id }}"
                                                data-name="{{ $user->name }}"
                                                data-apellidos="{{ $user->apellidos }}"
                                                data-email="{{ $user->email }}"
                                                data-phone="{{ $user->telefono }}"
                                                data-role-id="{{ $user->role_id }}"
                                                data-has-worker="{{ $user->trabajador ? '1' : '0' }}"
                                                onclick="openEditUserModalFromBtn(this)"
                                                style="background: transparent; border: 1px solid var(--border-subtle); color: var(--text-muted); width: 34px; height: 34px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s ease;" 
                                                onmouseover="this.style.color='#f59e0b'; this.style.borderColor='rgba(245,158,11,0.3)'; this.style.background='rgba(245,158,11,0.05)';" 
                                                onmouseout="this.style.color='var(--text-muted)'; this.style.borderColor='var(--border-subtle)'; this.style.background='transparent';">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                            </button>
                                            <button type="button" class="btn-action-delete" title="Eliminar Cuenta" 
                                                data-id="{{ $user->id }}"
                                                data-fullname="{{ addslashes($fullName) }}"
                                                onclick="confirmDeleteUserFromBtn(this)"
                                                style="background: transparent; border: 1px solid var(--border-subtle); color: var(--text-muted); width: 34px; height: 34px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s ease;" 
                                                onmouseover="this.style.color='var(--barber-red)'; this.style.borderColor='rgba(239,68,68,0.3)'; this.style.background='rgba(239,68,68,0.05)';" 
                                                onmouseout="this.style.color='var(--text-muted)'; this.style.borderColor='var(--border-subtle)'; this.style.background='transparent';">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr id="noUsersInitialRow">
                                    <td colspan="7" style="text-align: center; padding: 48px 24px; color: var(--text-muted);">
                                        <div style="font-size: 15px; font-weight: 500; color: var(--text-main);">No se encontraron usuarios en la base de datos</div>
                                    </td>
                                </tr>
                            @endforelse

                            <!-- Mensaje de Sin Resultados por Filtro -->
                            <tr id="noResultsFilterRow" style="display: none;">
                                <td colspan="7" style="text-align: center; padding: 48px 24px; color: var(--text-muted);">
                                    <svg style="margin: 0 auto 12px; color: var(--text-muted);" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line><line x1="8" y1="11" x2="14" y2="11"></line></svg>
                                    <div style="font-size: 15px; font-weight: 500; color: var(--text-main);">No se encontraron usuarios coincidentes</div>
                                    <p style="font-size: 13px; margin-top: 4px;">Intenta ajustar los términos de búsqueda o los filtros de rol y estado.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination Footer -->
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 24px; border-top: 1px solid var(--border-subtle); background: rgba(0,0,0,0.1); flex-wrap: wrap; gap: 12px;">
                    <div style="color: var(--text-muted); font-size: 13px;" id="paginationInfo">
                        Mostrando <span id="visibleCount" style="color: var(--text-white); font-weight: 600;">{{ count($users) }}</span> de <span id="totalCount" style="color: var(--text-white); font-weight: 600;">{{ count($users) }}</span> usuarios
                    </div>
                    <div style="display: flex; gap: 6px;">
                        <button type="button" style="background: rgba(255,255,255,0.03); border: 1px solid var(--border-subtle); color: var(--text-muted); padding: 6px 12px; border-radius: 6px; font-size: 13px; cursor: not-allowed; opacity: 0.5;" disabled>Anterior</button>
                        <button type="button" style="background: var(--barber-blue); border: 1px solid var(--barber-blue); color: var(--text-white); padding: 6px 12px; border-radius: 6px; font-size: 13px; cursor: pointer; font-weight: 600; box-shadow: 0 2px 8px rgba(0, 85, 255, 0.3);">1</button>
                        <button type="button" style="background: rgba(255,255,255,0.03); border: 1px solid var(--border-subtle); color: var(--text-muted); padding: 6px 12px; border-radius: 6px; font-size: 13px; cursor: not-allowed; opacity: 0.5;" disabled>Siguiente</button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- =========================================================================
         MODAL: REGISTRO DE NUEVO USUARIO (DISEÑO COHERENTE CON TRABAJADORES)
         ========================================================================= -->
    <div class="modal-overlay" id="modalCreateUser" role="dialog" aria-modal="true" aria-labelledby="createUserModalTitle">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 class="modal-title" id="createUserModalTitle">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <line x1="19" y1="8" x2="19" y2="14"></line>
                        <line x1="22" y1="11" x2="16" y2="11"></line>
                    </svg>
                    <span>Registrar Nuevo Usuario</span>
                </h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalCreateUser')" aria-label="Cerrar modal">&times;</button>
            </div>

            <form id="formCreateUser" action="{{ route('admin.usuarios.store') }}" method="POST" autocomplete="off">
                @csrf
                <div class="modal-body">
                    <!-- Nombre y Apellidos -->
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="newUserName">Nombre(s) <span class="required">*</span></label>
                            <input 
                                type="text" 
                                id="newUserName" 
                                name="name" 
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
                            <label class="form-label" for="newUserLastName">Apellidos <span class="required">*</span></label>
                            <input 
                                type="text" 
                                id="newUserLastName" 
                                name="apellidos" 
                                class="form-control" 
                                placeholder="Ej. Morales Ruiz" 
                                pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+" 
                                onkeypress="return /^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]$/.test(event.key)" 
                                oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]/g, '');" 
                                title="El campo Apellidos únicamente debe contener letras del abecedario" 
                                required
                            >
                        </div>
                    </div>

                    <!-- Teléfono y Correo Electrónico -->
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="newUserPhone">Teléfono de Contacto (10 dígitos) <span class="required">*</span></label>
                            <input 
                                type="tel" 
                                id="newUserPhone" 
                                name="telefono" 
                                class="form-control" 
                                placeholder="Ej. 5551234567" 
                                pattern="[0-9]{10}" 
                                maxlength="10" 
                                minlength="10" 
                                inputmode="numeric" 
                                onkeypress="return (event.charCode >= 48 && event.charCode <= 57)" 
                                oninput="validateUserPhoneInput(this);"
                                title="El teléfono debe tener exactamente 10 dígitos numéricos" 
                                required
                            >
                            <span id="newUserPhoneHelp" style="font-size: 11px; color: var(--text-muted); margin-top: 2px; display: block;">Exactamente 10 dígitos numéricos</span>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="newUserEmail">Correo Electrónico <span class="required">*</span></label>
                            <input 
                                type="email" 
                                id="newUserEmail" 
                                name="email" 
                                class="form-control" 
                                placeholder="usuario@dominio.com" 
                                pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}" 
                                oninput="validateUserEmailInput(this);"
                                title="Introduce un correo electrónico válido" 
                                required
                            >
                            <span id="newUserEmailHelp" style="font-size: 11px; color: var(--text-muted); margin-top: 2px; display: block;">Formato: usuario@dominio.com</span>
                        </div>
                    </div>

                    <!-- Contraseña -->
                    <div class="form-group">
                        <label class="form-label" for="newUserPassword">Contraseña <span class="required">*</span></label>
                        <div class="password-input-group">
                            <input 
                                type="password" 
                                id="newUserPassword" 
                                name="password" 
                                class="form-control" 
                                placeholder="Mínimo 8 caracteres, 1 mayúscula, 1 número y 1 símbolo" 
                                minlength="8" 
                                maxlength="50" 
                                required
                            >
                            <button 
                                type="button" 
                                id="toggleNewUserPassword" 
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
                        <span style="font-size: 11px; color: var(--text-muted); margin-top: 2px; display: block;">Mínimo 8 caracteres, al menos 1 letra mayúscula, 1 número y un carácter especial</span>
                    </div>

                    <!-- Rol Asignado -->
                    <div class="form-group">
                        <label class="form-label" for="newUserRole">Rol en el Sistema <span class="required">*</span></label>
                        <select id="newUserRole" name="role_id" class="form-control" required onchange="handleUserRoleChange(this.value)" style="background-color: #0d1527; color: #ffffff; color-scheme: dark;">
                            <option value="" disabled selected style="background-color: #0d1527; color: #94a3b8;">Seleccionar rol para el usuario...</option>
                            <option value="4" style="background-color: #0d1527; color: #ffffff;">Cliente (Acceso a reservas y perfil)</option>
                            <option value="1" style="background-color: #0d1527; color: #ffffff;">Administrador (Acceso total al sistema)</option>
                            <option value="2" style="background-color: #0d1527; color: #ffffff;">Recepcionista (Control de citas y agenda)</option>
                            <option value="3" style="background-color: #0d1527; color: #ffffff;">Barbero o Estilista (Especialista / Staff)</option>
                        </select>
                    </div>

                    <!-- Tarjeta Informativa Dinámica según el Rol -->
                    <div id="roleInfoBanner" class="role-info-card">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0; margin-top: 1px;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                        <span id="roleInfoText">Selecciona un rol para ver la configuración correspondiente de la cuenta.</span>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal('modalCreateUser')">Cancelar</button>
                    <button type="submit" id="btnSubmitUserAction" class="btn-submit-action">
                        <svg id="btnSubmitUserIcon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                            <polyline points="17 21 17 13 7 13 7 21"></polyline>
                            <polyline points="7 3 7 8 15 8"></polyline>
                        </svg>
                        <span id="btnSubmitUserText">Guardar Usuario</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para Editar Usuario -->
    <div class="modal-overlay" id="modalEditUser" role="dialog" aria-modal="true" aria-labelledby="editUserModalTitle">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 class="modal-title" id="editUserModalTitle">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                    <span>Editar Cuenta de Usuario</span>
                </h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalEditUser')" aria-label="Cerrar modal">&times;</button>
            </div>

            <form id="formEditUser" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="editUserId" name="id">
                <input type="hidden" id="editUserHasWorker" value="0">

                <div class="modal-body">
                    <!-- Nombre(s) -->
                    <div class="form-group">
                        <label class="form-label" for="editUserName">Nombre(s) <span class="required">*</span></label>
                        <input type="text" id="editUserName" name="name" class="form-control" placeholder="Ej. Juan Carlos" required autocomplete="off">
                    </div>

                    <!-- Apellidos -->
                    <div class="form-group">
                        <label class="form-label" for="editUserApellidos">Apellidos <span class="required">*</span></label>
                        <input type="text" id="editUserApellidos" name="apellidos" class="form-control" placeholder="Ej. Pérez Gómez" required autocomplete="off">
                    </div>

                    <!-- Teléfono -->
                    <div class="form-group">
                        <label class="form-label" for="editUserPhone">Teléfono de Contacto (10 dígitos) <span class="required">*</span></label>
                        <input type="tel" id="editUserPhone" name="telefono" class="form-control" placeholder="Ej. 5512345678" maxlength="10" required autocomplete="off">
                    </div>

                    <!-- Correo Electrónico -->
                    <div class="form-group">
                        <label class="form-label" for="editUserEmail">Correo Electrónico <span class="required">*</span></label>
                        <input type="email" id="editUserEmail" name="email" class="form-control" placeholder="Ej. usuario@ejemplo.com" required autocomplete="off">
                    </div>

                    <!-- Contraseña (Opcional) -->
                    <div class="form-group">
                        <label class="form-label" for="editUserPassword">Contraseña (Opcional)</label>
                        <div class="password-input-group">
                            <input 
                                type="password" 
                                id="editUserPassword" 
                                name="password" 
                                class="form-control" 
                                placeholder="Dejar en blanco para conservar la actual" 
                                autocomplete="new-password"
                            >
                            <button 
                                type="button" 
                                id="toggleEditUserPassword" 
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
                        <span style="font-size: 11px; color: var(--text-muted); margin-top: 2px; display: block;">Solo llenar si deseas cambiar la contraseña (Mín. 8 caracteres, 1 mayúscula, 1 número y 1 especial)</span>
                    </div>

                    <!-- Rol Asignado -->
                    <div class="form-group">
                        <label class="form-label" for="editUserRole">Rol en el Sistema <span class="required">*</span></label>
                        <select id="editUserRole" name="role_id" class="form-control" required onchange="handleEditUserRoleChange(this.value)" style="background-color: #0d1527; color: #ffffff; color-scheme: dark;">
                            <option value="4" style="background-color: #0d1527; color: #ffffff;">Cliente (Acceso a reservas y perfil)</option>
                            <option value="1" style="background-color: #0d1527; color: #ffffff;">Administrador (Acceso total al sistema)</option>
                            <option value="2" style="background-color: #0d1527; color: #ffffff;">Recepcionista (Control de citas y agenda)</option>
                            <option value="3" style="background-color: #0d1527; color: #ffffff;">Barbero o Estilista (Especialista / Staff)</option>
                        </select>
                    </div>

                    <!-- Tarjeta Informativa Dinámica según el Rol -->
                    <div id="editRoleInfoBanner" class="role-info-card">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0; margin-top: 1px;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                        <span id="editRoleInfoText">Los cambios se aplicarán inmediatamente a la cuenta del usuario.</span>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal('modalEditUser')">Cancelar</button>
                    <button type="submit" id="btnSubmitEditUserAction" class="btn-submit-action">
                        <svg id="btnSubmitEditUserIcon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                            <polyline points="17 21 17 13 7 13 7 21"></polyline>
                            <polyline points="7 3 7 8 15 8"></polyline>
                        </svg>
                        <span id="btnSubmitEditUserText">Guardar Cambios</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para Detalles del Usuario (Estilo Servicios) -->
    <div id="userDetailModal" class="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="detailModalTitle" style="display: none; position: fixed; inset: 0; background: rgba(5,10,21,0.8); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(4px); padding: 16px;">
        <div style="background: var(--bg-header); border: 1px solid var(--border-strong); border-radius: 12px; width: 100%; max-width: 520px; max-height: 95vh; overflow-y: auto; box-shadow: 0 20px 40px rgba(0,0,0,0.5);">
            <!-- Header -->
            <div style="padding: 16px 24px; border-bottom: 1px solid var(--border-subtle); display: flex; justify-content: space-between; align-items: center;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(0, 85, 255, 0.1); color: var(--barber-blue); display: flex; align-items: center; justify-content: center;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <h2 id="detailModalTitle" style="font-size: 1.15rem; font-weight: 700; color: #ffffff; margin: 0;">Detalles del Usuario</h2>
                </div>
                <button type="button" onclick="closeModal('userDetailModal')" style="background: none; border: none; color: var(--text-muted); cursor: pointer; padding: 4px; border-radius: 6px; display: flex; align-items: center; justify-content: center; transition: color 0.2s;" onmouseover="this.style.color='#ffffff';" onmouseout="this.style.color='var(--text-muted)';">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <!-- Body -->
            <div style="padding: 24px;">
                <!-- User Avatar & Primary Title Banner -->
                <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid var(--border-subtle);">
                    <div id="modalUserAvatar" style="width: 56px; height: 56px; border-radius: 50%; background: linear-gradient(135deg, var(--barber-blue), #1e293b); color: #ffffff; font-weight: 700; font-size: 20px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0, 85, 255, 0.25); flex-shrink: 0;">
                        U
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px; flex-wrap: wrap;">
                            <h3 id="modalFullName" style="font-size: 1.35rem; font-weight: 700; color: #ffffff; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">-</h3>
                            <span id="modalUserIdBadge" style="font-size: 0.75rem; font-weight: 700; color: var(--barber-blue); background: rgba(0, 85, 255, 0.12); padding: 2px 8px; border-radius: 12px; border: 1px solid rgba(0, 85, 255, 0.2);">#0</span>
                        </div>
                        <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                            <span id="modalRoleBadge" style="font-size: 0.8rem; font-weight: 600; padding: 3px 10px; border-radius: 6px; background: rgba(255,255,255,0.06); border: 1px solid var(--border-subtle); color: var(--text-white);">Cliente</span>
                            <span id="modalStatusBadge" style="font-size: 0.8rem; font-weight: 600; padding: 3px 10px; border-radius: 6px; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: #10b981;">Activo</span>
                        </div>
                    </div>
                </div>

                <!-- Contact Info Card -->
                <div style="display: flex; gap: 16px; background: rgba(255,255,255,0.02); padding: 16px; border-radius: 8px; border: 1px solid var(--border-subtle); margin-bottom: 16px;">
                    <div style="flex: 1; min-width: 0;">
                        <span style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Correo Electrónico</span>
                        <span id="modalEmail" style="font-size: 0.95rem; font-weight: 600; color: white; word-break: break-all;">-</span>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <span style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Teléfono</span>
                        <span id="modalPhone" style="font-size: 0.95rem; font-weight: 600; color: white;">-</span>
                    </div>
                </div>

                <!-- Registration & Birth Info Card -->
                <div style="display: flex; gap: 16px; background: rgba(255,255,255,0.02); padding: 16px; border-radius: 8px; border: 1px solid var(--border-subtle); margin-bottom: 16px;">
                    <div style="flex: 1;">
                        <span style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Fecha de Nacimiento</span>
                        <span id="modalBirth" style="font-size: 0.95rem; font-weight: 600; color: white;">-</span>
                    </div>
                    <div style="flex: 1;">
                        <span style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Fecha de Registro</span>
                        <span id="modalCreated" style="font-size: 0.95rem; font-weight: 600; color: white;">-</span>
                    </div>
                </div>

                <!-- Footer button -->
                <div style="margin-top: 24px; text-align: right;">
                    <button type="button" onclick="closeModal('userDetailModal')" style="background: var(--barber-blue); color: white; padding: 10px 28px; border-radius: 8px; border: none; font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 12px rgba(0, 85, 255, 0.3);" onmouseover="this.style.transform='translateY(-1px)';" onmouseout="this.style.transform='translateY(0)';">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- SCRIPTS PARA INTERACTIVIDAD Y GESTIÓN DE USUARIOS -->
    <script>
        // Funciones Globales para Control de Modales
        function openModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.add('open');
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
            if (id === 'modalCreateUser') {
                resetPasswordToggleState('toggleNewUserPassword', 'newUserPassword');
            }
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.remove('open');
                modal.style.display = 'none';
                document.body.style.overflow = '';
            }
            if (id === 'modalCreateUser') {
                resetPasswordToggleState('toggleNewUserPassword', 'newUserPassword');
            }
        }

        // Alternador de visibilidad de contraseña
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
            }
            if (btn) {
                const eye = btn.querySelector('.eye-icon');
                const eyeOff = btn.querySelector('.eye-off-icon');
                if (eye) eye.classList.remove('hidden');
                if (eyeOff) eyeOff.classList.add('hidden');
            }
        }

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Funciones de validación en tiempo real para teléfono y correo
        function validateUserPhoneInput(input) {
            input.value = input.value.replace(/[^0-9]/g, '').slice(0, 10);
            const helpEl = document.getElementById('newUserPhoneHelp');
            const currentPhone = input.value.trim();

            const isDuplicate = Array.from(document.querySelectorAll('.user-row'))
                .some(row => (row.getAttribute('data-phone') || '').trim() === currentPhone);

            if (currentPhone.length === 10) {
                if (isDuplicate) {
                    input.style.borderColor = '#ef4444';
                    if (helpEl) {
                        helpEl.textContent = '✗ Este teléfono ya está registrado por otro usuario';
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

        function validateUserEmailInput(input) {
            const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            const helpEl = document.getElementById('newUserEmailHelp');
            const currentEmail = input.value.trim().toLowerCase();

            const isDuplicate = Array.from(document.querySelectorAll('.user-row'))
                .some(row => (row.getAttribute('data-email') || '').trim().toLowerCase() === currentEmail);

            if (emailRegex.test(currentEmail)) {
                if (isDuplicate) {
                    input.style.borderColor = '#ef4444';
                    if (helpEl) {
                        helpEl.textContent = '✗ Este correo ya está registrado por otro usuario';
                        helpEl.style.color = '#ef4444';
                    }
                } else {
                    input.style.borderColor = '#10b981';
                    if (helpEl) {
                        helpEl.textContent = '✓ Formato de correo válido (disponible)';
                        helpEl.style.color = '#10b981';
                    }
                }
            } else if (currentEmail.length > 0) {
                input.style.borderColor = '#f59e0b';
                if (helpEl) {
                    helpEl.textContent = 'Introduce un formato válido: usuario@dominio.com';
                    helpEl.style.color = '#f59e0b';
                }
            } else {
                input.style.borderColor = '';
                if (helpEl) {
                    helpEl.textContent = 'Formato: usuario@dominio.com';
                    helpEl.style.color = 'var(--text-muted)';
                }
            }
        }

        // Comportamiento dinámico según el rol seleccionado
        function handleUserRoleChange(roleId) {
            const roleInfoBanner = document.getElementById('roleInfoBanner');
            const roleInfoText = document.getElementById('roleInfoText');
            const btnSubmit = document.getElementById('btnSubmitUserAction');
            const btnText = document.getElementById('btnSubmitUserText');
            const btnIcon = document.getElementById('btnSubmitUserIcon');

            if (!roleInfoBanner || !roleInfoText || !btnSubmit || !btnText || !btnIcon) return;

            if (roleId === '3') {
                // Barbero o Estilista (Trabajador) -> Flujo de continuación al módulo de Trabajadores
                roleInfoBanner.classList.add('role-info-worker');
                roleInfoText.innerHTML = '✂️ <strong>Registro de Especialista:</strong> Los Barberos y Estilistas requieren datos laborales complementarios (dirección, experiencia, fotografía, servicios y horario). Al pulsar <strong>«Continuar»</strong>, se transferirán estos datos directamente al formulario de Trabajadores.';
                btnText.textContent = 'Continuar con datos laborales';
                btnSubmit.classList.add('btn-submit-continue');
                btnIcon.innerHTML = '<polyline points="9 18 15 12 9 6"></polyline><line x1="5" y1="12" x2="15" y2="12"></line>';
            } else {
                roleInfoBanner.classList.remove('role-info-worker');
                btnSubmit.classList.remove('btn-submit-continue');
                btnIcon.innerHTML = '<path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline>';

                if (roleId === '1') {
                    roleInfoText.innerHTML = '🛡️ <strong>Administrador:</strong> La cuenta tendrá permisos globales sobre la plataforma, personal, finanzas y configuración.';
                    btnText.textContent = 'Guardar Administrador';
                } else if (roleId === '2') {
                    roleInfoText.innerHTML = '📋 <strong>Recepcionista:</strong> La cuenta tendrá acceso al panel de recepción, gestión de citas, caja y asignación de turnos.';
                    btnText.textContent = 'Guardar Recepcionista';
                } else if (roleId === '4') {
                    roleInfoText.innerHTML = '👤 <strong>Cliente:</strong> Se creará una cuenta de cliente estándar con acceso a reservas en línea y catálogo de servicios.';
                    btnText.textContent = 'Guardar Cliente';
                } else {
                    roleInfoText.textContent = 'Selecciona un rol para ver la configuración correspondiente de la cuenta.';
                    btnText.textContent = 'Guardar Usuario';
                }
            }
        }

        // Construcción de Fila HTML de Usuario para Inserción Dinámica
        function createUserRowHtml(user) {
            const roleId = parseInt(user.role_id, 10);
            const roleColor = {
                1: { text: '#38bdf8', bg: 'rgba(56, 189, 248, 0.12)', border: 'rgba(56, 189, 248, 0.3)' },
                2: { text: '#c084fc', bg: 'rgba(192, 132, 252, 0.12)', border: 'rgba(192, 132, 252, 0.3)' },
                3: { text: '#34d399', bg: 'rgba(52, 211, 153, 0.12)', border: 'rgba(52, 211, 153, 0.3)' },
                4: { text: '#fbbf24', bg: 'rgba(251, 191, 36, 0.12)', border: 'rgba(251, 191, 36, 0.3)' }
            }[roleId] || { text: '#94a3b8', bg: 'rgba(148, 163, 184, 0.12)', border: 'rgba(148, 163, 184, 0.3)' };

            const fullName = escapeHtml(user.nombre_completo || (user.name + ' ' + (user.apellidos || '')));
            const email = escapeHtml(user.email);
            const phone = escapeHtml(user.telefono || '—');
            const roleName = escapeHtml(user.role_nombre || 'Usuario');
            const dateStr = user.created_at || 'Hoy';

            return `
                <tr class="user-row" 
                    data-id="${user.id}"
                    data-name="${fullName.toLowerCase()}"
                    data-email="${email.toLowerCase()}"
                    data-phone="${phone.toLowerCase()}"
                    data-role-id="${user.role_id}"
                    data-status="activo"
                    style="transition: background 0.2s ease; animation: alertFlash 1.2s ease-out;">
                    
                    <td style="padding-left: 24px; color: var(--text-muted); font-size: 13px; font-weight: 600;">
                        #${user.id}
                    </td>

                    <td>
                        <div style="display: flex; align-items: center; gap: 14px;">
                            <div class="user-avatar" style="width: 40px; height: 40px; border-radius: 10px; background: ${roleColor.bg}; color: ${roleColor.text}; border: 1px solid ${roleColor.border}; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px;">
                                ${escapeHtml(user.initials || 'U')}
                            </div>
                            <div>
                                <div style="font-weight: 600; color: var(--text-white); font-size: 14px;">${fullName}</div>
                                <div style="font-size: 13px; color: var(--text-muted); margin-top: 2px;">${email}</div>
                            </div>
                        </div>
                    </td>

                    <td>
                        <span style="background: ${roleColor.bg}; border: 1px solid ${roleColor.border}; color: ${roleColor.text}; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                            <span style="width: 6px; height: 6px; border-radius: 50%; background: ${roleColor.text};"></span>
                            ${roleName}
                        </span>
                    </td>

                    <td style="color: var(--text-main); font-size: 13px;">
                        ${phone}
                    </td>

                    <td>
                        <span class="status-badge status-confirmed">Activo</span>
                    </td>

                    <td style="color: var(--text-muted); font-size: 13px;">
                        ${dateStr}
                    </td>

                    <td style="text-align: right; padding-right: 24px; white-space: nowrap;">
                        <div style="display: inline-flex; gap: 6px;">
                            <button type="button" 
                                class="btn-action-view" 
                                title="Ver detalles del usuario"
                                data-id="${user.id}"
                                data-fullname="${fullName}"
                                data-email="${email}"
                                data-phone="${phone}"
                                data-birth="No especificada"
                                data-role="${roleName}"
                                data-status="Activo"
                                data-created="${dateStr}"
                                onclick="showUserDetailModalFromBtn(this)"
                                style="background: rgba(255,255,255,0.04); border: 1px solid var(--border-subtle); color: var(--text-muted); width: 34px; height: 34px; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s ease;">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            </button>
                            <button type="button" 
                                class="btn-action-edit" 
                                title="Editar Usuario"
                                data-id="${user.id}"
                                data-name="${user.name}"
                                data-apellidos="${user.apellidos}"
                                data-email="${email}"
                                data-phone="${phone}"
                                data-role-id="${user.role_id}"
                                data-has-worker="0"
                                onclick="openEditUserModalFromBtn(this)"
                                style="background: rgba(255,255,255,0.04); border: 1px solid var(--border-subtle); color: var(--text-muted); width: 34px; height: 34px; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s ease;">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </button>
                            <button type="button" 
                                class="btn-action-delete" 
                                title="Eliminar Cuenta"
                                data-id="${user.id}"
                                data-fullname="${fullName}"
                                onclick="confirmDeleteUserFromBtn(this)"
                                style="background: rgba(255,255,255,0.04); border: 1px solid var(--border-subtle); color: var(--text-muted); width: 34px; height: 34px; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s ease;">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }

        function showUserDetailModalFromBtn(btn) {
            const id = btn.getAttribute('data-id') || '0';
            const fullName = btn.getAttribute('data-fullname') || 'Usuario';
            const email = btn.getAttribute('data-email') || '-';
            const phone = btn.getAttribute('data-phone') || '-';
            const birth = btn.getAttribute('data-birth') || '-';
            const role = btn.getAttribute('data-role') || 'Usuario';
            const status = btn.getAttribute('data-status') || 'Activo';
            const created = btn.getAttribute('data-created') || '-';

            const initials = fullName.trim() ? fullName.trim().split(' ').filter(n => n.length > 0).map(n => n[0]).slice(0, 2).join('').toUpperCase() : 'U';

            const avatarEl = document.getElementById('modalUserAvatar');
            if (avatarEl) avatarEl.textContent = initials;

            const idBadgeEl = document.getElementById('modalUserIdBadge');
            if (idBadgeEl) idBadgeEl.textContent = '#' + id;

            const fullNameEl = document.getElementById('modalFullName');
            if (fullNameEl) fullNameEl.textContent = fullName;

            const emailEl = document.getElementById('modalEmail');
            if (emailEl) emailEl.textContent = email;

            const phoneEl = document.getElementById('modalPhone');
            if (phoneEl) phoneEl.textContent = phone;

            const birthEl = document.getElementById('modalBirth');
            if (birthEl) birthEl.textContent = birth;

            const createdEl = document.getElementById('modalCreated');
            if (createdEl) createdEl.textContent = created;

            const roleBadgeEl = document.getElementById('modalRoleBadge');
            if (roleBadgeEl) roleBadgeEl.textContent = role;

            const statusBadgeEl = document.getElementById('modalStatusBadge');
            if (statusBadgeEl) {
                statusBadgeEl.textContent = status;
                const isActivo = status.toLowerCase().includes('activ');
                statusBadgeEl.style.background = isActivo ? 'rgba(16, 185, 129, 0.1)' : 'rgba(239, 68, 68, 0.1)';
                statusBadgeEl.style.border = isActivo ? '1px solid rgba(16, 185, 129, 0.2)' : '1px solid rgba(239, 68, 68, 0.2)';
                statusBadgeEl.style.color = isActivo ? '#10b981' : '#ef4444';
            }

            openModal('userDetailModal');
        }

        function openEditUserModalFromBtn(btn) {
            const id = btn.getAttribute('data-id');
            const name = btn.getAttribute('data-name');
            const apellidos = btn.getAttribute('data-apellidos');
            const email = btn.getAttribute('data-email');
            const phone = btn.getAttribute('data-phone');
            const roleId = btn.getAttribute('data-role-id');
            const hasWorker = btn.getAttribute('data-has-worker');

            document.getElementById('editUserId').value = id;
            document.getElementById('editUserName').value = name;
            document.getElementById('editUserApellidos').value = apellidos;
            document.getElementById('editUserEmail').value = email;
            document.getElementById('editUserPhone').value = phone;
            document.getElementById('editUserRole').value = roleId;
            document.getElementById('editUserHasWorker').value = hasWorker || '0';
            document.getElementById('editUserPassword').value = '';

            handleEditUserRoleChange(roleId);
            openModal('modalEditUser');
        }

        function handleEditUserRoleChange(roleVal) {
            const banner = document.getElementById('editRoleInfoBanner');
            const bannerText = document.getElementById('editRoleInfoText');
            const submitBtn = document.getElementById('btnSubmitEditUserAction');
            const submitText = document.getElementById('btnSubmitEditUserText');
            const hasWorker = document.getElementById('editUserHasWorker')?.value === '1';

            if (roleVal === '3' && !hasWorker) {
                if (banner) {
                    banner.style.background = 'rgba(168, 85, 247, 0.1)';
                    banner.style.borderColor = 'rgba(168, 85, 247, 0.3)';
                    banner.style.color = '#c084fc';
                }
                if (bannerText) {
                    bannerText.textContent = 'Al convertir este usuario a Barbero o Estilista, se reutilizará esta misma cuenta sin crear duplicados y serás redirigido para completar la información laboral.';
                }
                if (submitBtn) {
                    submitBtn.style.background = 'linear-gradient(135deg, #a855f7, #0055ff)';
                }
                if (submitText) {
                    submitText.textContent = 'Continuar con Datos Laborales →';
                }
            } else {
                if (banner) {
                    banner.style.background = 'rgba(0, 85, 255, 0.08)';
                    banner.style.borderColor = 'rgba(0, 85, 255, 0.2)';
                    banner.style.color = 'var(--text-main)';
                }
                if (bannerText) {
                    bannerText.textContent = 'Los cambios se aplicarán inmediatamente a la cuenta del usuario.';
                }
                if (submitBtn) {
                    submitBtn.style.background = 'var(--barber-blue)';
                }
                if (submitText) {
                    submitText.textContent = 'Guardar Cambios';
                }
            }
        }

        async function confirmDeleteUserFromBtn(btn) {
            const id = btn.getAttribute('data-id');
            const fullName = btn.getAttribute('data-fullname');

            const result = await Swal.fire({
                title: '¿Eliminar usuario?',
                html: `¿Estás seguro de que deseas eliminar permanentemente la cuenta de <strong>"${fullName}"</strong>?<br><span style="font-size: 13px; color: #ef4444; margin-top: 6px; display: inline-block;">Esta acción no se puede deshacer y eliminará todos sus datos asociados.</span>`,
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
                const response = await fetch(`/admin/usuarios/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    const row = document.querySelector(`tr.user-row[data-id="${id}"]`);
                    if (row) {
                        row.style.transition = 'all 0.3s ease';
                        row.style.opacity = '0';
                        setTimeout(() => row.remove(), 300);
                    }

                    if (data.stats) {
                        const statTotal = document.getElementById('statTotalUsers');
                        const statAdmin = document.getElementById('statAdminUsers');
                        const statTrabajador = document.getElementById('statTrabajadorUsers');
                        const statRecepcion = document.getElementById('statRecepcionUsers');
                        const statCliente = document.getElementById('statClienteUsers');
                        const totalCountEl = document.getElementById('totalCount');
                        const visibleCountEl = document.getElementById('visibleCount');

                        if (statTotal) statTotal.textContent = data.stats.total;
                        if (totalCountEl) totalCountEl.textContent = data.stats.total;
                        if (visibleCountEl) visibleCountEl.textContent = data.stats.total;
                        if (statAdmin) statAdmin.textContent = data.stats.admins;
                        if (statTrabajador) statTrabajador.textContent = data.stats.trabajadores;
                        if (statRecepcion) statRecepcion.textContent = data.stats.recepcionistas;
                        if (statCliente) statCliente.textContent = data.stats.clientes;
                    }

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: data.message || `Usuario "${fullName}" eliminado exitosamente.`,
                        showConfirmButton: false,
                        timer: 2500,
                        timerProgressBar: true,
                        background: '#10b981',
                        color: '#ffffff',
                        iconColor: '#ffffff'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al eliminar',
                        text: data.message || 'No se pudo eliminar la cuenta.',
                        background: '#1e293b',
                        color: '#ffffff',
                        confirmButtonColor: '#ef4444'
                    });
                }
            } catch (err) {
                console.error(err);
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

        function updateUserRow(user) {
            const row = document.querySelector(`tr.user-row[data-id="${user.id}"]`);
            if (!row) return;

            row.setAttribute('data-name', user.nombre_completo.toLowerCase());
            row.setAttribute('data-email', user.email.toLowerCase());
            row.setAttribute('data-phone', (user.telefono || '').toLowerCase());
            row.setAttribute('data-role-id', user.role_id);
            row.setAttribute('data-status', user.activo ? 'activo' : 'inactivo');

            const roleColorMap = {
                1: { text: '#38bdf8', bg: 'rgba(56, 189, 248, 0.12)', border: 'rgba(56, 189, 248, 0.3)' },
                2: { text: '#c084fc', bg: 'rgba(192, 132, 252, 0.12)', border: 'rgba(192, 132, 252, 0.3)' },
                3: { text: '#34d399', bg: 'rgba(52, 211, 153, 0.12)', border: 'rgba(52, 211, 153, 0.3)' },
                4: { text: '#fbbf24', bg: 'rgba(251, 191, 36, 0.12)', border: 'rgba(251, 191, 36, 0.3)' }
            };
            const roleStyle = roleColorMap[user.role_id] || roleColorMap[4];

            const nameEl = row.querySelector('td:nth-child(2) div div div:first-child');
            if (nameEl) nameEl.textContent = user.nombre_completo;

            const emailEl = row.querySelector('td:nth-child(2) div div div:last-child');
            if (emailEl) emailEl.textContent = user.email;

            const roleTd = row.querySelector('td:nth-child(3)');
            if (roleTd) {
                roleTd.innerHTML = `
                    <span style="background: ${roleStyle.bg}; border: 1px solid ${roleStyle.border}; color: ${roleStyle.text}; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                        <span style="width: 6px; height: 6px; border-radius: 50%; background: ${roleStyle.text};"></span>
                        ${user.role_nombre}
                    </span>
                `;
            }

            const phoneTd = row.querySelector('td:nth-child(4)');
            if (phoneTd) phoneTd.textContent = user.telefono || '—';

            const editBtn = row.querySelector('.btn-action-edit');
            if (editBtn) {
                editBtn.setAttribute('data-name', user.name);
                editBtn.setAttribute('data-apellidos', user.apellidos);
                editBtn.setAttribute('data-email', user.email);
                editBtn.setAttribute('data-phone', user.telefono);
                editBtn.setAttribute('data-role-id', user.role_id);
                editBtn.setAttribute('data-has-worker', user.has_worker ? '1' : '0');
            }

            const viewBtn = row.querySelector('.btn-action-view');
            if (viewBtn) {
                viewBtn.setAttribute('data-fullname', user.nombre_completo);
                viewBtn.setAttribute('data-email', user.email);
                viewBtn.setAttribute('data-phone', user.telefono || 'No registrado');
                viewBtn.setAttribute('data-role', user.role_nombre);
            }
        }

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

            // --- Alternador de Visibilidad de Contraseña ---
            initPasswordToggle('toggleNewUserPassword', 'newUserPassword');

            // --- Cierre de Modales al hacer clic fuera o pulsar ESC ---
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

            // --- Filtrado y Búsqueda en Tiempo Real en la Tabla ---
            const searchInput = document.getElementById('userSearchInput');
            const roleFilter = document.getElementById('roleFilterSelect');
            const statusFilter = document.getElementById('statusFilterSelect');
            const resetBtn = document.getElementById('resetFiltersBtn');
            const noResultsRow = document.getElementById('noResultsFilterRow');
            const visibleCountEl = document.getElementById('visibleCount');

            function applyFilters() {
                const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
                const selectedRole = roleFilter ? roleFilter.value : '';
                const selectedStatus = statusFilter ? statusFilter.value : '';

                let visibleRows = 0;
                const userRows = document.querySelectorAll('.user-row');

                userRows.forEach(row => {
                    const name = row.getAttribute('data-name') || '';
                    const email = row.getAttribute('data-email') || '';
                    const phone = row.getAttribute('data-phone') || '';
                    const id = row.getAttribute('data-id') || '';
                    const roleId = row.getAttribute('data-role-id') || '';
                    const status = row.getAttribute('data-status') || '';

                    const matchesSearch = !query || 
                        name.includes(query) || 
                        email.includes(query) || 
                        phone.includes(query) || 
                        id === query;

                    const matchesRole = !selectedRole || roleId === selectedRole;
                    const matchesStatus = !selectedStatus || status === selectedStatus;

                    if (matchesSearch && matchesRole && matchesStatus) {
                        row.style.display = '';
                        visibleRows++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                if (noResultsRow) {
                    noResultsRow.style.display = (visibleRows === 0 && userRows.length > 0) ? '' : 'none';
                }

                if (visibleCountEl) {
                    visibleCountEl.textContent = visibleRows;
                }
            }

            if (searchInput) searchInput.addEventListener('input', applyFilters);
            if (roleFilter) roleFilter.addEventListener('change', applyFilters);
            if (statusFilter) statusFilter.addEventListener('change', applyFilters);

            if (resetBtn) {
                resetBtn.addEventListener('click', function () {
                    if (searchInput) searchInput.value = '';
                    if (roleFilter) roleFilter.value = '';
                    if (statusFilter) statusFilter.value = '';
                    applyFilters();
                });
            }

            // --- Evento de Ver Detalles en filas existentes ---
            document.querySelectorAll('.btn-action-view').forEach(btn => {
                btn.addEventListener('click', function () {
                    showUserDetailModalFromBtn(this);
                });
            });

            // ===================================================
            // PROCESAMIENTO DEL FORMULARIO DE AGREGAR USUARIO
            // ===================================================
            const formCreateUser = document.getElementById('formCreateUser');
            if (formCreateUser) {
                formCreateUser.addEventListener('submit', async function (e) {
                    e.preventDefault();

                    const nameInput = document.getElementById('newUserName');
                    const lastNameInput = document.getElementById('newUserLastName');
                    const phoneInput = document.getElementById('newUserPhone');
                    const emailInput = document.getElementById('newUserEmail');
                    const passwordInput = document.getElementById('newUserPassword');
                    const roleSelect = document.getElementById('newUserRole');
                    const submitBtn = document.getElementById('btnSubmitUserAction');

                    const name = (nameInput.value || '').trim();
                    const apellidos = (lastNameInput.value || '').trim();
                    const phone = (phoneInput.value || '').trim();
                    const email = (emailInput.value || '').trim().toLowerCase();
                    const password = passwordInput.value || '';
                    const roleId = roleSelect.value;

                    // 1. Validaciones previas cliente
                    const nameRegex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/;
                    if (!name || !nameRegex.test(name)) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Nombre inválido',
                            text: 'El campo Nombre únicamente debe contener letras del abecedario.',
                            background: '#1e293b',
                            color: '#ffffff',
                            confirmButtonColor: '#0055ff'
                        });
                        nameInput.focus();
                        return;
                    }

                    if (!apellidos || !nameRegex.test(apellidos)) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Apellidos inválidos',
                            text: 'El campo Apellidos únicamente debe contener letras del abecedario.',
                            background: '#1e293b',
                            color: '#ffffff',
                            confirmButtonColor: '#0055ff'
                        });
                        lastNameInput.focus();
                        return;
                    }

                    if (!/^[0-9]{10}$/.test(phone)) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Teléfono inválido',
                            text: 'El número de teléfono debe contener exactamente 10 dígitos numéricos.',
                            background: '#1e293b',
                            color: '#ffffff',
                            confirmButtonColor: '#0055ff'
                        });
                        phoneInput.focus();
                        return;
                    }

                    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                    if (!emailRegex.test(email)) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Correo inválido',
                            text: 'Introduce un correo electrónico válido (ejemplo: usuario@dominio.com).',
                            background: '#1e293b',
                            color: '#ffffff',
                            confirmButtonColor: '#0055ff'
                        });
                        emailInput.focus();
                        return;
                    }

                    if (password.length < 8 || !/[A-Z]/.test(password) || !/[0-9]/.test(password) || !/[\W_]/.test(password)) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Contraseña débil',
                            text: 'La contraseña debe tener al menos 8 caracteres, una mayúscula, un número y un carácter especial.',
                            background: '#1e293b',
                            color: '#ffffff',
                            confirmButtonColor: '#0055ff'
                        });
                        passwordInput.focus();
                        return;
                    }

                    if (!roleId) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Rol requerido',
                            text: 'Por favor selecciona el rol para el usuario.',
                            background: '#1e293b',
                            color: '#ffffff',
                            confirmButtonColor: '#0055ff'
                        });
                        roleSelect.focus();
                        return;
                    }

                    // ===================================================
                    // CASO 1: BARBERO O ESTILISTA (ROL 3)
                    // Transferir datos precargados al formulario de Trabajadores
                    // ===================================================
                    if (roleId === '3') {
                        try {
                            const workerTransferData = {
                                nombre: name,
                                apellidos: apellidos,
                                telefono: phone,
                                email: email,
                                password: password,
                                fromUsers: true
                            };
                            sessionStorage.setItem('preloadedWorkerData', JSON.stringify(workerTransferData));

                            closeModal('modalCreateUser');

                            Swal.fire({
                                icon: 'info',
                                title: 'Transferencia a Trabajadores',
                                text: 'Redirigiendo al formulario de Trabajadores con tus datos generales precargados...',
                                background: '#1e293b',
                                color: '#ffffff',
                                showConfirmButton: false,
                                timer: 1200,
                                timerProgressBar: true
                            });

                            setTimeout(() => {
                                window.location.href = "{{ route('admin.trabajadores.index') }}?from_usuarios=1";
                            }, 800);
                        } catch (err) {
                            console.error(err);
                            window.location.href = "{{ route('admin.trabajadores.index') }}?from_usuarios=1";
                        }
                        return;
                    }

                    // ===================================================
                    // CASO 2: CLIENTE, ADMIN O RECEPCIONISTA
                    // Guardado directo en la base de datos vía AJAX
                    // ===================================================
                    const originalBtnHtml = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = `
                        <svg class="animate-spin" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10" stroke-opacity="0.25"></circle>
                            <path d="M12 2a10 10 0 0 1 10 10" stroke-linecap="round"></path>
                        </svg>
                        <span>Guardando...</span>
                    `;

                    try {
                        const response = await fetch("{{ route('admin.usuarios.store') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                name: name,
                                apellidos: apellidos,
                                telefono: phone,
                                email: email,
                                password: password,
                                role_id: roleId
                            })
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            closeModal('modalCreateUser');
                            formCreateUser.reset();
                            handleUserRoleChange('');

                            // Resetear ayudas visuales
                            phoneInput.style.borderColor = '';
                            emailInput.style.borderColor = '';
                            document.getElementById('newUserPhoneHelp').textContent = 'Exactamente 10 dígitos numéricos';
                            document.getElementById('newUserPhoneHelp').style.color = 'var(--text-muted)';
                            document.getElementById('newUserEmailHelp').textContent = 'Formato: usuario@dominio.com';
                            document.getElementById('newUserEmailHelp').style.color = 'var(--text-muted)';

                            // 1. Mostrar notificación SweetAlert2 de éxito
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: data.message || 'Usuario registrado exitosamente.',
                                showConfirmButton: false,
                                timer: 3500,
                                timerProgressBar: true,
                                background: '#10b981',
                                color: '#ffffff',
                                iconColor: '#ffffff'
                            });

                            // 2. Insertar fila en la tabla dinámicamente sin recargar página
                            const tableBody = document.getElementById('usersTableBody');
                            if (tableBody && data.user) {
                                const emptyRow = document.getElementById('noUsersRow');
                                if (emptyRow) emptyRow.remove();

                                const newRowHtml = createUserRowHtml(data.user);
                                tableBody.insertAdjacentHTML('afterbegin', newRowHtml);
                            }

                            // 3. Actualizar KPIs de estadísticas en vivo
                            if (data.stats) {
                                const statTotal = document.getElementById('statTotalUsers');
                                const statAdmin = document.getElementById('statAdminUsers');
                                const statTrabajador = document.getElementById('statTrabajadorUsers');
                                const statRecepcion = document.getElementById('statRecepcionUsers');
                                const statCliente = document.getElementById('statClienteUsers');
                                const totalCountEl = document.getElementById('totalCount');

                                if (statTotal) statTotal.textContent = data.stats.total;
                                if (totalCountEl) totalCountEl.textContent = data.stats.total;
                                if (statAdmin) statAdmin.textContent = data.stats.admins;
                                if (statTrabajador) statTrabajador.textContent = data.stats.trabajadores;
                                if (statRecepcion) statRecepcion.textContent = data.stats.recepcionistas;
                                if (statCliente) statCliente.textContent = data.stats.clientes;
                            }

                            applyFilters();
                        } else {
                            let errorHtml = data.message || 'Ocurrió un error al registrar el usuario.';
                            if (data.errors) {
                                errorHtml = Object.values(data.errors).flat().join('<br>');
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Error de validación',
                                html: errorHtml,
                                background: '#1e293b',
                                color: '#ffffff',
                                confirmButtonColor: '#ef4444'
                            });
                        }
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error de conexión',
                            text: 'No se pudo comunicar con el servidor para registrar el usuario.',
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
                });
            }

            // --- Formulario de Edición de Usuario ---
            const formEditUser = document.getElementById('formEditUser');
            if (formEditUser) {
                initPasswordToggle('toggleEditUserPassword', 'editUserPassword');

                formEditUser.addEventListener('submit', async function (e) {
                    e.preventDefault();

                    const userId = document.getElementById('editUserId').value;
                    const roleId = document.getElementById('editUserRole').value;
                    const hasWorker = document.getElementById('editUserHasWorker').value === '1';

                    const name = document.getElementById('editUserName').value.trim();
                    const apellidos = document.getElementById('editUserApellidos').value.trim();
                    const email = document.getElementById('editUserEmail').value.trim();
                    const phone = document.getElementById('editUserPhone').value.trim();
                    const password = document.getElementById('editUserPassword').value;

                    // Si se convierte a rol 3 (Trabajador) y no tiene perfil de trabajador aún
                    if (roleId === '3' && !hasWorker) {
                        const preloaded = {
                            user_id: userId,
                            nombre: name,
                            apellidos: apellidos,
                            email: email,
                            telefono: phone,
                            password: password
                        };
                        sessionStorage.setItem('preloadedWorkerData', JSON.stringify(preloaded));
                    }

                    const submitBtn = document.getElementById('btnSubmitEditUserAction');
                    const originalBtnHtml = submitBtn ? submitBtn.innerHTML : '';
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = `
                            <svg class="animate-spin" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10" stroke-opacity="0.25"></circle>
                                <path d="M12 2a10 10 0 0 1 10 10" stroke-linecap="round"></path>
                            </svg>
                            <span>Guardando...</span>
                        `;
                    }

                    try {
                        const response = await fetch(`/admin/usuarios/${userId}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                _method: 'PUT',
                                name: name,
                                apellidos: apellidos,
                                telefono: phone,
                                email: email,
                                password: password,
                                role_id: roleId
                            })
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            closeModal('modalEditUser');

                            if (data.redirect) {
                                Swal.fire({
                                    title: 'Redirigiendo...',
                                    text: data.message,
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false,
                                    background: '#0f172a',
                                    color: '#ffffff',
                                }).then(() => {
                                    window.location.href = data.redirect;
                                });
                                return;
                            }

                            if (data.user) {
                                updateUserRow(data.user);
                            }
                            if (data.stats) {
                                updateKpiStats(data.stats);
                            }

                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: data.message || 'Usuario actualizado correctamente.',
                                showConfirmButton: false,
                                timer: 3500,
                                timerProgressBar: true,
                                background: '#10b981',
                                color: '#ffffff',
                                iconColor: '#ffffff'
                            });
                        } else {
                            let errorHtml = data.message || 'Ocurrió un error al actualizar la cuenta de usuario.';
                            if (data.errors) {
                                errorHtml = Object.values(data.errors).flat().join('<br>');
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Error de validación',
                                html: errorHtml,
                                background: '#1e293b',
                                color: '#ffffff',
                                confirmButtonColor: '#ef4444'
                            });
                        }
                    } catch (error) {
                        console.error(error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error de conexión',
                            text: 'No se pudo comunicar con el servidor para actualizar el usuario.',
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
                });
            }
        });
    </script>
</body>
</html>


