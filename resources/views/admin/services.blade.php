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

        /* Subtarea 7: Botón interactivo para ver trabajadores asignados */
        .btn-toggle-workers {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 6px;
            background: rgba(0, 85, 255, 0.08);
            border: 1px solid rgba(0, 85, 255, 0.25);
            color: #bfdbfe;
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
            transition: all 0.2s ease;
            user-select: none;
        }

        .btn-toggle-workers:hover {
            background: rgba(0, 85, 255, 0.16);
            border-color: rgba(0, 85, 255, 0.45);
            color: #ffffff;
        }

        .btn-toggle-workers:focus-visible {
            outline: 2px solid var(--barber-blue);
            outline-offset: 1px;
        }

        .service-workers-arrow {
            transition: transform 0.25s ease;
            flex-shrink: 0;
        }

        .btn-toggle-workers[aria-expanded="true"] .service-workers-arrow,
        .service-workers-arrow.expanded {
            transform: rotate(180deg);
        }

        .service-workers-collapse {
            margin-top: 8px;
            animation: fadeInWorkers 0.2s ease-in-out;
        }

        @keyframes fadeInWorkers {
            from { opacity: 0; transform: translateY(-4px); }
            to { opacity: 1; transform: translateY(0); }
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
                    <a href="{{ route('admin.dashboard') }}" class="nav-item" aria-current="page" title="Dashboard">
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
                    <a href="{{ route('admin.services') }}" class="nav-item active" title="Servicios">
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
                                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2-2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
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
            <!-- Header Section -->
            <div class="welcome-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                <h1>Catálogo de servicios</h1>
                <a href="#" onclick="event.preventDefault(); document.getElementById('createServiceModal').style.display='flex'; document.body.style.overflow='hidden';" style="background: var(--barber-red); color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3); transition: all 0.2s;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                    Nuevo servicio
                </a>
            </div>





            <!-- Filters Section -->
            <div class="panel-card" style="margin-bottom: 24px; padding: 20px;">
                <div style="display: flex; gap: 16px; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 200px;">
                        <div style="position: relative;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="position: absolute; left: 12px; top: 12px; color: var(--text-muted);"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                            <input type="text" id="searchInput" placeholder="Buscar servicios por nombre o descripción..." style="width: 100%; padding: 10px 16px 10px 40px; border-radius: 8px; background: rgba(255,255,255,0.03); border: 1px solid var(--border-subtle); color: white; font-family: inherit;">
                        </div>
                    </div>
                    <div>
                        <select id="categoryFilter" style="padding: 10px 16px; border-radius: 8px; background: rgba(255,255,255,0.03); border: 1px solid var(--border-subtle); color: white; font-family: inherit; appearance: none; padding-right: 32px; background-image: url('data:image/svg+xml;utf8,<svg viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"white\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><polyline points=\"6 9 12 15 18 9\"/></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;">
                            <option value="" style="background: var(--bg-header);">Categoría: Todas</option>
                            <option value="barberia" style="background: var(--bg-header);">Barbería</option>
                            <option value="estetica" style="background: var(--bg-header);">Estética</option>
                        </select>
                    </div>
                    <div>
                        <select id="statusFilter" style="padding: 10px 16px; border-radius: 8px; background: rgba(255,255,255,0.03); border: 1px solid var(--border-subtle); color: white; font-family: inherit; appearance: none; padding-right: 32px; background-image: url('data:image/svg+xml;utf8,<svg viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"white\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><polyline points=\"6 9 12 15 18 9\"/></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;">
                            <option value="" style="background: var(--bg-header);">Estado: Todos</option>
                            <option value="activo" style="background: var(--bg-header);">Activo</option>
                            <option value="inactivo" style="background: var(--bg-header);">Inactivo</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Table Section -->
            <div class="panel-card" style="padding: 0;">
                <div class="data-table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Imagen</th>
                                <th>Servicio</th>
                                <th>Categoría</th>
                                <th>Precio</th>
                                <th>Duración</th>
                                <th>Trabajadores</th>
                                <th>Estado</th>
                                <th style="text-align: right;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($services as $service)
                            <tr class="service-row" data-name="{{ strtolower($service->name) }}" data-description="{{ strtolower($service->description) }}" data-category="{{ $service->category }}" data-status="{{ $service->is_active ? 'activo' : 'inactivo' }}">
                                <td>
                                    <div style="width: 48px; height: 48px; border-radius: 8px; background: rgba(255,255,255,0.05); overflow: hidden; border: 1px solid var(--border-subtle); display: flex; align-items: center; justify-content: center; color: var(--text-muted);">
                                        @if($service->image_path)
                                        <img src="{{ asset($service->image_path) }}" style="width: 100%; height: 100%; object-fit: cover;" alt="{{ $service->name }}">
                                        @else
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight: 700; color: white; margin-bottom: 4px; font-size: 14.5px;">{{ $service->name }}</div>
                                    <div style="font-size: 12.5px; color: var(--text-muted); max-width: 220px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $service->description ?? 'Sin descripción' }}</div>
                                </td>
                                <td><span class="status-badge" style="background: rgba(255,255,255,0.05); border: 1px solid var(--border-subtle); color: var(--text-muted);">{{ ucfirst($service->category) }}</span></td>
                                <td><span style="font-weight: 700; color: white;">${{ number_format($service->price, 2) }}</span></td>
                                <td><span style="color: var(--text-muted); font-size: 13.5px;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: text-bottom; margin-right: 4px;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>{{ $service->duration_minutes }} min</span></td>
                                <td>
                                    @php
                                        $workersCount = $service->trabajadores ? $service->trabajadores->count() : 0;
                                    @endphp
                                    @if($workersCount > 0)
                                        <div class="service-workers-dropdown" id="dropdown-workers-{{ $service->id }}">
                                            <button type="button" 
                                                    class="btn-toggle-workers" 
                                                    onclick="toggleServiceWorkers({{ $service->id }})" 
                                                    id="btn-toggle-workers-{{ $service->id }}"
                                                    aria-expanded="false" 
                                                    aria-controls="service-workers-list-{{ $service->id }}"
                                                    title="Ver trabajadores asignados">
                                                <span>Ver trabajadores ({{ $workersCount }})</span>
                                                <svg class="service-workers-arrow" id="arrow-service-workers-{{ $service->id }}" viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="6 9 12 15 18 9"></polyline>
                                                </svg>
                                            </button>
                                            <div class="service-workers-collapse" id="service-workers-list-{{ $service->id }}" style="display: none;">
                                                <div style="display: flex; flex-wrap: wrap; gap: 6px; max-width: 260px; margin-top: 6px;">
                                                    @foreach($service->trabajadores as $trabajador)
                                                        <span class="status-badge" style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 6px; background: rgba(0, 85, 255, 0.08); border: 1px solid rgba(0, 85, 255, 0.25); color: #bfdbfe; font-size: 12px; font-weight: 500;" title="{{ $trabajador->nombre_completo }}{{ $trabajador->activo ? '' : ' (Inactivo)' }}">
                                                            <span style="width: 7px; height: 7px; border-radius: 50%; background: {{ $trabajador->activo ? '#10b981' : '#f59e0b' }}; display: inline-block;"></span>
                                                            <span>{{ $trabajador->nombre }}</span>
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span style="font-size: 12.5px; color: var(--text-muted); font-style: italic;">Sin trabajadores asignados (0)</span>
                                    @endif
                                </td>
                                <td>
                                    @if($service->is_active)
                                        <span class="status-badge status-confirmed">Activo</span>
                                    @else
                                        <span class="status-badge status-cancelled">Inactivo</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                        <button type="button" onclick="openViewServiceModal({{ $service->id }}, {{ json_encode($service->name) }}, {{ json_encode($service->description) }}, {{ $service->price }}, {{ $service->duration_minutes }}, '{{ $service->category }}', {{ json_encode($service->image_path) }}, {{ json_encode($service->trabajadores->map(fn($t) => ['id' => $t->id, 'nombre' => $t->nombre, 'nombre_completo' => $t->nombre_completo, 'activo' => $t->activo])) }})" class="btn-outline" style="padding: 6px; border-radius: 6px; color: var(--text-muted); border-color: var(--border-subtle);" title="Ver detalle">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        </button>
                                        <button type="button" onclick="openEditServiceModal({{ $service->id }}, {{ json_encode($service->name) }}, {{ json_encode($service->description) }}, {{ $service->price }}, {{ $service->duration_minutes }}, '{{ $service->category }}', {{ json_encode($service->image_path) }})" class="btn-outline" style="padding: 6px; border-radius: 6px;" title="Modificar servicio">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        </button>
                                        <form action="{{ route('admin.services.toggle_status', $service->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('PATCH')
                                            @if($service->is_active)
                                            <button type="submit" class="btn-outline" style="padding: 6px; border-radius: 6px; color: var(--barber-red); border-color: rgba(239, 68, 68, 0.3);" title="Desactivar servicio">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"/><line x1="12" y1="2" x2="12" y2="12"/></svg>
                                            </button>
                                            @else
                                            <button type="submit" class="btn-outline" style="padding: 6px; border-radius: 6px; color: var(--barber-blue-light); border-color: rgba(0, 85, 255, 0.3);" title="Activar servicio">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"/><line x1="12" y1="2" x2="12" y2="12"/></svg>
                                            </button>
                                            @endif
                                        </form>
                                        <button type="button" onclick="deleteService({{ $service->id }}, {{ json_encode($service->name) }})" class="btn-outline" style="padding: 6px; border-radius: 6px; color: var(--barber-red); border-color: rgba(239, 68, 68, 0.3);" title="Eliminar servicio">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 40px 20px; color: var(--text-muted);">
                                    No hay servicios registrados. Haz clic en "Nuevo servicio" para agregar uno.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </main>

        <!-- MODAL NUEVO SERVICIO -->
        <div id="createServiceModal" style="display: {{ $errors->any() && old('_method') !== 'PUT' ? 'flex' : 'none' }}; position: fixed; inset: 0; background: rgba(5,10,21,0.8); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(4px); padding: 16px;">
            <div style="background: var(--bg-header); border: 1px solid var(--border-strong); border-radius: 12px; width: 100%; max-width: 600px; max-height: 95vh; overflow-y: auto; box-shadow: 0 20px 40px rgba(0,0,0,0.5);">
                <div style="padding: 16px 24px; border-bottom: 1px solid var(--border-subtle); display: flex; justify-content: space-between; align-items: center;">
                    <h2 style="font-size: 1.15rem; font-weight: 700;">Registrar Nuevo Servicio</h2>
                    <button type="button" onclick="document.getElementById('createServiceForm').reset(); document.getElementById('createServiceModal').style.display='none'; document.body.style.overflow='auto';" style="background: none; border: none; color: var(--text-muted); cursor: pointer; padding: 4px;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>
                <form id="createServiceForm" action="{{ route('admin.services.store') }}" method="POST" enctype="multipart/form-data" style="padding: 16px 24px;">
                    @csrf

                    @if($errors->any())
                    <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid var(--barber-red); color: var(--barber-red); padding: 12px 16px; border-radius: 8px; margin-bottom: 16px;">
                        <ul style="margin-left: 20px; font-size: 0.9rem;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    
                    <div style="margin-bottom: 12px;">
                        <label style="display: block; margin-bottom: 6px; font-size: 0.85rem; color: var(--text-muted);">Nombre del servicio <span style="color: var(--barber-red);">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="Ej. Corte Clásico" style="width: 100%; padding: 8px 12px; border-radius: 8px; background: rgba(255,255,255,0.03); border: 1px solid var(--border-subtle); color: white; font-family: inherit;">
                        <small style="color: var(--text-muted); display: block; margin-top: 4px; font-size: 0.75rem;">Solo letras y números. No se permiten signos especiales ni nombres compuestos solo por números.</small>
                    </div>

                    <div style="display: flex; gap: 12px; margin-bottom: 12px; flex-wrap: wrap;">
                        <div style="flex: 1; min-width: 180px;">
                            <label style="display: block; margin-bottom: 6px; font-size: 0.85rem; color: var(--text-muted);">Precio ($) <span style="color: var(--barber-red);">*</span></label>
                            <input type="number" step="0.01" min="0.01" name="price" value="{{ old('price') }}" required placeholder="0.00" style="width: 100%; padding: 8px 12px; border-radius: 8px; background: rgba(255,255,255,0.03); border: 1px solid var(--border-subtle); color: white; font-family: inherit;">
                        </div>
                        <div style="flex: 1; min-width: 180px;">
                            <label style="display: block; margin-bottom: 6px; font-size: 0.85rem; color: var(--text-muted);">Duración (min) <span style="color: var(--barber-red);">*</span></label>
                            <input type="number" min="20" name="duration_minutes" value="{{ old('duration_minutes') }}" required placeholder="45" style="width: 100%; padding: 8px 12px; border-radius: 8px; background: rgba(255,255,255,0.03); border: 1px solid var(--border-subtle); color: white; font-family: inherit;">
                        </div>
                    </div>

                    <div style="margin-bottom: 12px;">
                        <label style="display: block; margin-bottom: 6px; font-size: 0.85rem; color: var(--text-muted);">Categoría <span style="color: var(--barber-red);">*</span></label>
                        <select name="category" required style="width: 100%; padding: 8px 12px; border-radius: 8px; background: rgba(255,255,255,0.03); border: 1px solid var(--border-subtle); color: white; font-family: inherit; appearance: none; padding-right: 32px; background-image: url('data:image/svg+xml;utf8,<svg viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'white\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><polyline points=\'6 9 12 15 18 9\'/></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;">
                            <option value="" disabled {{ old('category') ? '' : 'selected' }} style="background: var(--bg-header);">Selecciona una categoría</option>
                            <option value="barberia" {{ old('category') == 'barberia' ? 'selected' : '' }} style="background: var(--bg-header);">Barbería</option>
                            <option value="estetica" {{ old('category') == 'estetica' ? 'selected' : '' }} style="background: var(--bg-header);">Estética</option>
                        </select>
                    </div>

                    <div style="margin-bottom: 12px;">
                        <label style="display: block; margin-bottom: 6px; font-size: 0.85rem; color: var(--text-muted);">Descripción <span style="color: var(--barber-red);">*</span></label>
                        <textarea name="description" rows="2" required placeholder="Detalles del servicio..." style="width: 100%; padding: 8px 12px; border-radius: 8px; background: rgba(255,255,255,0.03); border: 1px solid var(--border-subtle); color: white; font-family: inherit; resize: vertical;">{{ old('description') }}</textarea>
                    </div>

                    <div style="margin-bottom: 16px;">
                        <label style="display: block; margin-bottom: 6px; font-size: 0.85rem; color: var(--text-muted);">Imagen Referencial <span style="color: var(--barber-red);">*</span></label>
                        <input type="file" name="image" accept="image/*" required style="width: 100%; padding: 6px 12px; border-radius: 8px; background: rgba(255,255,255,0.03); border: 1px solid var(--border-subtle); color: white; font-family: inherit; font-size: 0.85rem;">
                        <small style="color: var(--text-muted); display: block; margin-top: 4px;">Solo imágenes (JPG, PNG). Máx 2MB.</small>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid var(--border-subtle); padding-top: 16px;">
                        <button type="button" onclick="document.getElementById('createServiceForm').reset(); document.getElementById('createServiceModal').style.display='none'; document.body.style.overflow='auto';" class="btn-outline" style="padding: 8px 16px; color: white;">Cancelar</button>
                        <button type="submit" style="background: var(--barber-blue); color: white; padding: 8px 16px; border-radius: 8px; border: none; font-weight: 600; cursor: pointer; transition: all 0.2s;">Guardar Servicio</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL VER DETALLE SERVICIO -->
    <div id="viewServiceModal" style="display: none; position: fixed; inset: 0; background: rgba(5,10,21,0.8); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(4px); padding: 16px;">
        <div style="background: var(--bg-header); border: 1px solid var(--border-strong); border-radius: 12px; width: 100%; max-width: 500px; max-height: 95vh; overflow-y: auto; box-shadow: 0 20px 40px rgba(0,0,0,0.5);">
            <div style="padding: 16px 24px; border-bottom: 1px solid var(--border-subtle); display: flex; justify-content: space-between; align-items: center;">
                <h2 style="font-size: 1.15rem; font-weight: 700;">Detalles del Servicio</h2>
                <button type="button" onclick="document.getElementById('viewServiceModal').style.display='none'; document.body.style.overflow='auto';" style="background: none; border: none; color: var(--text-muted); cursor: pointer; padding: 4px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <div style="padding: 24px;">
                <div id="view_image_container" style="width: 100%; height: 280px; border-radius: 8px; background: rgba(0,0,0,0.2); overflow: hidden; margin-bottom: 20px; display: none; align-items: center; justify-content: center;">
                    <img id="view_image" src="" style="max-width: 100%; max-height: 100%; object-fit: contain; border-radius: 4px;">
                </div>
                
                <h3 id="view_name" style="font-size: 1.5rem; font-weight: 700; margin-bottom: 8px; color: white;"></h3>
                <div style="display: flex; gap: 8px; margin-bottom: 16px;">
                    <span id="view_category" class="status-badge" style="background: rgba(255,255,255,0.05); border: 1px solid var(--border-subtle); color: var(--text-muted);"></span>
                </div>
                
                <p id="view_description" style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 20px; line-height: 1.5;"></p>
                
                <div style="display: flex; gap: 16px; background: rgba(255,255,255,0.02); padding: 16px; border-radius: 8px; border: 1px solid var(--border-subtle);">
                    <div style="flex: 1;">
                        <span style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Precio</span>
                        <span id="view_price" style="font-size: 1.1rem; font-weight: 700; color: white;"></span>
                    </div>
                    <div style="flex: 1;">
                        <span style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Duración</span>
                        <span id="view_duration" style="font-size: 1.1rem; font-weight: 700; color: white;"></span>
                    </div>
                </div>

                <div style="margin-top: 16px; background: rgba(255,255,255,0.02); padding: 16px; border-radius: 8px; border: 1px solid var(--border-subtle);">
                    <span style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 8px;">Trabajadores Asignados</span>
                    <div id="view_workers" style="display: flex; flex-wrap: wrap; gap: 6px;"></div>
                </div>

                <div style="margin-top: 24px; text-align: right;">
                    <button type="button" onclick="document.getElementById('viewServiceModal').style.display='none'; document.body.style.overflow='auto';" style="background: var(--barber-blue); color: white; padding: 8px 24px; border-radius: 8px; border: none; font-weight: 600; cursor: pointer; transition: all 0.2s;">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL EDITAR SERVICIO -->
    <div id="editServiceModal" style="display: {{ $errors->any() && old('_method') === 'PUT' ? 'flex' : 'none' }}; position: fixed; inset: 0; background: rgba(5,10,21,0.8); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(4px); padding: 16px;">
        <div style="background: var(--bg-header); border: 1px solid var(--border-strong); border-radius: 12px; width: 100%; max-width: 600px; max-height: 95vh; overflow-y: auto; box-shadow: 0 20px 40px rgba(0,0,0,0.5);">
            <div style="padding: 16px 24px; border-bottom: 1px solid var(--border-subtle); display: flex; justify-content: space-between; align-items: center;">
                <h2 style="font-size: 1.15rem; font-weight: 700;">Modificar Servicio</h2>
                <button type="button" onclick="document.getElementById('editServiceForm').reset(); document.getElementById('editServiceModal').style.display='none'; document.body.style.overflow='auto';" style="background: none; border: none; color: var(--text-muted); cursor: pointer; padding: 4px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <form id="editServiceForm" action="{{ old('service_id') ? route('admin.services.update', old('service_id')) : '' }}" method="POST" enctype="multipart/form-data" style="padding: 16px 24px;">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_service_id" name="service_id" value="{{ old('service_id') }}">
                
                @if($errors->any() && old('_method') === 'PUT')
                <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid var(--barber-red); color: var(--barber-red); padding: 12px 16px; border-radius: 8px; margin-bottom: 16px;">
                    <ul style="margin-left: 20px; font-size: 0.9rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div style="margin-bottom: 12px;">
                    <label style="display: block; margin-bottom: 6px; font-size: 0.85rem; color: var(--text-muted);">Nombre del servicio <span style="color: var(--barber-red);">*</span></label>
                    <input type="text" id="edit_name" name="name" value="{{ old('name') }}" required placeholder="Ej. Corte Clásico" style="width: 100%; padding: 8px 12px; border-radius: 8px; background: rgba(255,255,255,0.03); border: 1px solid var(--border-subtle); color: white; font-family: inherit;">
                    <small style="color: var(--text-muted); display: block; margin-top: 4px; font-size: 0.75rem;">Solo letras y números. No se permiten signos especiales ni nombres compuestos solo por números.</small>
                </div>

                <div style="display: flex; gap: 12px; margin-bottom: 12px; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 180px;">
                        <label style="display: block; margin-bottom: 6px; font-size: 0.85rem; color: var(--text-muted);">Precio ($) <span style="color: var(--barber-red);">*</span></label>
                        <input type="number" step="0.01" min="0.01" id="edit_price" name="price" value="{{ old('price') }}" required placeholder="0.00" style="width: 100%; padding: 8px 12px; border-radius: 8px; background: rgba(255,255,255,0.03); border: 1px solid var(--border-subtle); color: white; font-family: inherit;">
                    </div>
                    <div style="flex: 1; min-width: 180px;">
                        <label style="display: block; margin-bottom: 6px; font-size: 0.85rem; color: var(--text-muted);">Duración (min) <span style="color: var(--barber-red);">*</span></label>
                        <input type="number" min="20" id="edit_duration" name="duration_minutes" value="{{ old('duration_minutes') }}" required placeholder="45" style="width: 100%; padding: 8px 12px; border-radius: 8px; background: rgba(255,255,255,0.03); border: 1px solid var(--border-subtle); color: white; font-family: inherit;">
                    </div>
                </div>

                <div style="margin-bottom: 12px;">
                    <label style="display: block; margin-bottom: 6px; font-size: 0.85rem; color: var(--text-muted);">Categoría <span style="color: var(--barber-red);">*</span></label>
                    <select id="edit_category" name="category" required style="width: 100%; padding: 8px 12px; border-radius: 8px; background: rgba(255,255,255,0.03); border: 1px solid var(--border-subtle); color: white; font-family: inherit; appearance: none; padding-right: 32px; background-image: url('data:image/svg+xml;utf8,<svg viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'white\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><polyline points=\'6 9 12 15 18 9\'/></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;">
                        <option value="" disabled {{ old('category', '') == '' ? 'selected' : '' }} style="background: var(--bg-header);">Selecciona una categoría</option>
                        <option value="barberia" {{ old('category') == 'barberia' ? 'selected' : '' }} style="background: var(--bg-header);">Barbería</option>
                        <option value="estetica" {{ old('category') == 'estetica' ? 'selected' : '' }} style="background: var(--bg-header);">Estética</option>
                    </select>
                </div>

                <div style="margin-bottom: 12px;">
                    <label style="display: block; margin-bottom: 6px; font-size: 0.85rem; color: var(--text-muted);">Descripción <span style="color: var(--barber-red);">*</span></label>
                    <textarea id="edit_description" name="description" rows="2" required placeholder="Detalles del servicio..." style="width: 100%; padding: 8px 12px; border-radius: 8px; background: rgba(255,255,255,0.03); border: 1px solid var(--border-subtle); color: white; font-family: inherit; resize: vertical;">{{ old('description') }}</textarea>
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="display: block; margin-bottom: 6px; font-size: 0.85rem; color: var(--text-muted);">Imagen Referencial (Opcional)</label>
                    
                    <div id="edit_image_preview_container" style="display: none; margin-bottom: 12px; align-items: center; gap: 12px; background: rgba(255,255,255,0.03); padding: 8px; border-radius: 8px; border: 1px solid var(--border-subtle);">
                        <img id="edit_current_image" src="" style="width: 48px; height: 48px; object-fit: cover; border-radius: 6px; border: 1px solid var(--border-subtle);">
                        <span style="font-size: 0.85rem; color: var(--text-muted);">Imagen actual (Sube una nueva para reemplazarla)</span>
                    </div>

                    <input type="file" name="image" accept="image/*" style="width: 100%; padding: 6px 12px; border-radius: 8px; background: rgba(255,255,255,0.03); border: 1px solid var(--border-subtle); color: white; font-family: inherit; font-size: 0.85rem;">
                    <small style="color: var(--text-muted); display: block; margin-top: 4px;">Dejar vacío para conservar la imagen actual. Solo imágenes (JPG, PNG). Máx 2MB.</small>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid var(--border-subtle); padding-top: 16px;">
                    <button type="button" onclick="document.getElementById('editServiceForm').reset(); document.getElementById('editServiceModal').style.display='none'; document.body.style.overflow='auto';" class="btn-outline" style="padding: 8px 16px; color: white;">Cancelar</button>
                    <button type="submit" style="background: var(--barber-blue); color: white; padding: 8px 16px; border-radius: 8px; border: none; font-weight: 600; cursor: pointer; transition: all 0.2s;">Actualizar Servicio</button>
                </div>
            </form>
        </div>
    </div>

    <!-- SCRIPTS PARA INTERACTIVIDAD ACCESIBLE -->
    <script>
        // Si hay un error de validación en la edición, mostrar scroll locked
        @if($errors->any() && old('_method') === 'PUT')
            document.body.style.overflow = 'hidden';
        @endif

        function openEditServiceModal(id, name, description, price, duration, category, imagePath) {
            const modal = document.getElementById('editServiceModal');
            const form = document.getElementById('editServiceForm');
            
            // Set form action dynamically
            form.action = `/admin/servicios/${id}`;
            document.getElementById('edit_service_id').value = id;
            
            // Populate fields ONLY IF there is no previous old data being shown from validation error
            @if(!($errors->any() && old('_method') === 'PUT'))
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_price').value = price;
            document.getElementById('edit_duration').value = duration;
            document.getElementById('edit_category').value = category;
            document.getElementById('edit_description').value = description;
            @endif

            const imgContainer = document.getElementById('edit_image_preview_container');
            if (imagePath) {
                document.getElementById('edit_current_image').src = imagePath;
                imgContainer.style.display = 'flex';
            } else {
                imgContainer.style.display = 'none';
            }
            
            // Show modal and lock scroll
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function openViewServiceModal(id, name, description, price, duration, category, imagePath, workers) {
            const modal = document.getElementById('viewServiceModal');
            
            document.getElementById('view_name').textContent = name;
            document.getElementById('view_description').textContent = description || 'Sin descripción detallada.';
            document.getElementById('view_price').textContent = '$' + parseFloat(price).toFixed(2);
            document.getElementById('view_duration').textContent = duration + ' min';
            document.getElementById('view_category').textContent = category.charAt(0).toUpperCase() + category.slice(1);
            
            const imgContainer = document.getElementById('view_image_container');
            if (imagePath) {
                document.getElementById('view_image').src = imagePath;
                imgContainer.style.display = 'flex';
            } else {
                imgContainer.style.display = 'none';
            }

            const workersContainer = document.getElementById('view_workers');
            if (workersContainer) {
                if (workers && workers.length > 0) {
                    workersContainer.innerHTML = workers.map(w => `
                        <span class="status-badge" style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 6px; background: rgba(0, 85, 255, 0.08); border: 1px solid rgba(0, 85, 255, 0.25); color: #bfdbfe; font-size: 12px; font-weight: 500;">
                            <span style="width: 7px; height: 7px; border-radius: 50%; background: ${w.activo ? '#10b981' : '#f59e0b'}; display: inline-block;"></span>
                            ${w.nombre_completo || w.nombre}
                        </span>
                    `).join('');
                } else {
                    workersContainer.innerHTML = `<span style="font-size: 12.5px; color: var(--text-muted); font-style: italic;">Sin trabajadores asignados</span>`;
                }
            }

            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function toggleServiceWorkers(serviceId) {
            const list = document.getElementById(`service-workers-list-${serviceId}`);
            const arrow = document.getElementById(`arrow-service-workers-${serviceId}`);
            const btn = document.getElementById(`btn-toggle-workers-${serviceId}`);
            if (!list) return;

            const isHidden = list.style.display === 'none' || getComputedStyle(list).display === 'none';
            if (isHidden) {
                list.style.display = 'block';
                if (arrow) arrow.classList.add('expanded');
                if (btn) btn.setAttribute('aria-expanded', 'true');
            } else {
                list.style.display = 'none';
                if (arrow) arrow.classList.remove('expanded');
                if (btn) btn.setAttribute('aria-expanded', 'false');
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

            // --- Filtros de la Tabla de Servicios ---
            const searchInput = document.getElementById('searchInput');
            const categoryFilter = document.getElementById('categoryFilter');
            const statusFilter = document.getElementById('statusFilter');
            const rows = document.querySelectorAll('.service-row');

            function filterTable() {
                if (!searchInput || !categoryFilter || !statusFilter) return;
                
                const search = searchInput.value.toLowerCase();
                const category = categoryFilter.value;
                const status = statusFilter.value;

                // Guardar en sessionStorage para persistir entre recargas
                sessionStorage.setItem('serviceSearch', searchInput.value);
                sessionStorage.setItem('serviceCategory', category);
                sessionStorage.setItem('serviceStatus', status);

                rows.forEach(row => {
                    const name = row.getAttribute('data-name') || '';
                    const desc = row.getAttribute('data-description') || '';
                    const rowCat = row.getAttribute('data-category') || '';
                    const rowStatus = row.getAttribute('data-status') || '';

                    const matchesSearch = name.includes(search) || desc.includes(search);
                    const matchesCategory = category === '' || rowCat === category;
                    const matchesStatus = status === '' || rowStatus === status;

                    if (matchesSearch && matchesCategory && matchesStatus) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            if (searchInput && categoryFilter && statusFilter) {
                // Recuperar filtros guardados, si no hay estado guardado, por defecto es 'activo'
                const savedSearch = sessionStorage.getItem('serviceSearch');
                const savedCategory = sessionStorage.getItem('serviceCategory');
                const savedStatus = sessionStorage.getItem('serviceStatus');

                @if(session('success') || $errors->any())
                    // Venimos de recargar la página tras crear, editar o activar/desactivar un servicio
                    if (savedSearch !== null) searchInput.value = savedSearch;
                    if (savedCategory !== null) categoryFilter.value = savedCategory;
                    if (savedStatus !== null) statusFilter.value = savedStatus;
                @else
                    // Es una entrada "fresca" desde el menú lateral (Dashboard, etc.)
                    // Reseteamos todo a la vista por defecto: solo activos
                    sessionStorage.removeItem('serviceSearch');
                    sessionStorage.removeItem('serviceCategory');
                    sessionStorage.removeItem('serviceStatus');
                    
                    searchInput.value = '';
                    categoryFilter.value = '';
                    statusFilter.value = 'activo';
                @endif

                // Aplicar filtros iniciales
                filterTable();

                searchInput.addEventListener('input', filterTable);
                categoryFilter.addEventListener('change', filterTable);
                statusFilter.addEventListener('change', filterTable);
            }
        });

        // Eliminar Servicio con confirmación estilo Trabajadores
        async function deleteService(id, name) {
            const result = await Swal.fire({
                title: `¿Eliminar "${name}"?`,
                text: 'Esta acción no se puede deshacer y el servicio será eliminado permanentemente del catálogo.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                background: '#1e293b',
                color: '#ffffff'
            });

            if (!result.isConfirmed) return;

            try {
                const response = await fetch(`/admin/servicios/${id}`, {
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
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: data.message || `Servicio "${name}" eliminado correctamente.`,
                        showConfirmButton: false,
                        timer: 2500,
                        timerProgressBar: true,
                        background: '#10b981',
                        color: '#ffffff',
                        iconColor: '#ffffff',
                        customClass: {
                            popup: 'colored-toast'
                        }
                    });

                    setTimeout(() => window.location.reload(), 700);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'No se pudo eliminar el servicio.',
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
    </script>

    <!-- SweetAlert2 CDN -->
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
</body>
</html>
