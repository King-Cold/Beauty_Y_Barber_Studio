<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Beauty & Barber Studio - Panel de Recepción')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* ===================================================
           SISTEMA DE DISEÑO BASE DEL SISTEMA (DARK MODE)
           Consistencia visual con el panel administrativo y cliente
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
            --bg-card: #0c142b;
            
            --border-subtle: rgba(255, 255, 255, 0.06);
            --border-strong: rgba(255, 255, 255, 0.12);

            --barber-red: #7f1d1d;
            --barber-red-light: rgba(127, 29, 29, 0.18);
            --barber-red-border: rgba(255, 255, 255, 0.14);
            --barber-blue: #1e3a8a;
            --barber-blue-light: #60a5fa; 
            --barber-blue-pale: rgba(30, 58, 138, 0.18);
            --barber-blue-border: rgba(255, 255, 255, 0.14);

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

        /* HEADER SUPERIOR */
        .app-header {
            background: var(--bg-header);
            border-bottom: 1px solid var(--border-subtle);
            position: sticky;
            top: 0;
            z-index: 50;
            height: var(--header-height);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
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
            border-color: rgba(37, 99, 235, 0.3);
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
            background: rgba(37, 99, 235, 0.15);
            color: #60a5fa;
            border: 1px solid rgba(37, 99, 235, 0.3);
            padding: 2px 8px;
            border-radius: 4px;
        }

        /* --- Menú de Perfil de Usuario --- */
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
            background: linear-gradient(135deg, var(--barber-blue), #1e40af);
            color: var(--text-white);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
            letter-spacing: 0.05em;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25);
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
            width: 240px;
            background: var(--bg-header);
            border: 1px solid var(--border-subtle);
            border-radius: 10px;
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
            background: transparent;
            border: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
            transition: all 0.2s ease;
            font-family: inherit;
        }

        .menu-item:hover,
        .menu-item:focus {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-white);
            outline: none;
        }

        .menu-item .menu-item-icon {
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: var(--text-muted);
        }

        .menu-item:hover .menu-item-icon {
            color: var(--barber-blue-light);
        }

        .menu-divider {
            height: 1px;
            background: var(--border-subtle);
            margin: 6px 0;
        }

        .menu-item.logout-item {
            color: var(--barber-red);
        }

        .menu-item.logout-item .menu-item-icon {
            color: var(--barber-red);
        }

        .menu-item.logout-item:hover {
            background: var(--barber-red-light);
            color: #ff6b6b;
        }

        /* ===================================================
           LAYOUT PRINCIPAL (SIDEBAR + MAIN)
           =================================================== */
        .recepcion-layout {
            display: flex;
            flex: 1;
            min-height: calc(100vh - var(--header-height));
            position: relative;
            overflow-x: clip;
        }

        /* ===================================================
           SIDEBAR DE LA RECEPCIONISTA (FIJO AL HACER SCROLL)
           =================================================== */
        /* SIDEBAR DE RECEPCIÓN (FIJO AL HACER SCROLL, SIN SCROLL INTERNO) */
        .recepcion-sidebar {
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

        .recepcion-sidebar::-webkit-scrollbar {
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
            font-size: 11px;
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

        /* Estado Colapsado en Escritorio */
        .recepcion-sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        .recepcion-sidebar.collapsed .sidebar-header {
            justify-content: center;
            padding: 0;
        }

        .recepcion-sidebar.collapsed .sidebar-title {
            display: none;
        }

        .recepcion-sidebar.collapsed .nav-label {
            display: none;
        }

        .recepcion-sidebar.collapsed .nav-badge {
            display: none;
        }

        .recepcion-sidebar.collapsed .nav-item {
            justify-content: center;
            padding: 12px 0;
            margin: 8px 12px;
            border-left: none;
            border-radius: 8px;
        }

        .recepcion-sidebar.collapsed .nav-item.active {
            background: rgba(255, 255, 255, 0.05);
            border-left: none;
        }

        .recepcion-sidebar.collapsed .nav-item.active .nav-icon-box {
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
            background: rgba(255, 255, 255, 0.03);
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

        /* Opción Activa */
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

        .nav-badge.badge-primary {
            background: var(--barber-blue-pale);
            color: var(--barber-blue-light);
            border: 1px solid rgba(37, 99, 235, 0.3);
        }

        /* Fondo difuminado para móvil */
        .sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background-color: rgba(5, 10, 21, 0.7);
            backdrop-filter: blur(4px);
            z-index: 25;
        }

        /* ===================================================
           ÁREA DE CONTENIDO PRINCIPAL
           =================================================== */
        .main-content {
            flex: 1;
            padding: 32px 40px;
            background-color: var(--bg-canvas);
            min-width: 0;
        }

        .content-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* ===================================================
           RESPONSIVIDAD Y DISPOSITIVOS MÓVILES
           =================================================== */
        @media (max-width: 768px) {
            .header-container {
                padding: 0 16px;
            }

            .mobile-toggle-btn {
                display: flex;
            }

            .brand-badge-container,
            .user-meta {
                display: none;
            }

            .main-content {
                padding: 20px 16px;
            }

            .recepcion-sidebar {
                position: fixed;
                top: var(--header-height);
                bottom: 0;
                left: 0;
                width: 280px;
                transform: translateX(-100%);
                border-right: 1px solid var(--border-subtle);
                box-shadow: 4px 0 24px rgba(0, 0, 0, 0.7);
            }

            .recepcion-sidebar.open-mobile {
                transform: translateX(0);
            }

            .sidebar-backdrop.active {
                display: block;
            }
        }
    </style>

    @stack('styles')
</head>
<body>

    <!-- Enlace accesible de salto al contenido -->
    <a href="#main-content" class="skip-link">Saltar al contenido principal</a>

    <!-- HEADER SUPERIOR -->
    <header class="app-header" role="banner">
        <div class="header-top-accent-line" aria-hidden="true"></div>

        <div class="header-container">
            <!-- Zona Izquierda: Hamburguesa Móvil + Marca -->
            <div class="header-left">
                <button 
                    type="button" 
                    class="mobile-toggle-btn" 
                    id="mobileToggleBtn" 
                    aria-label="Abrir menú de navegación" 
                    aria-expanded="false" 
                    aria-controls="recepcionSidebar"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>

                <a href="{{ route('recepcion.dashboard') }}" class="brand-section" aria-label="Beauty & Barber Studio - Panel de Recepción">
                    <img 
                        src="{{ asset('images/logo.png') }}" 
                        alt="Logotipo Beauty & Barber Studio" 
                        class="brand-logo-img"
                        onerror="this.style.display='none'"
                    >
                    <div class="brand-info">
                        <span class="brand-name">Beauty & Barber Studio</span>
                        <div class="brand-badge-container">
                            <span class="brand-role-badge">{{ Auth::user() && (int)Auth::user()->role_id === 1 ? 'Panel Administración' : 'Panel Recepción' }}</span>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Zona Derecha: Perfil de Recepcionista y Cerrar Sesión -->
            <nav class="user-nav" aria-label="Navegación de usuario y cuenta">
                <div class="profile-dropdown-wrapper" id="profileDropdownWrapper">
                    <button 
                        type="button" 
                        class="profile-btn" 
                        id="profileBtn" 
                        aria-expanded="false" 
                        aria-haspopup="true" 
                        aria-controls="profileMenu"
                        aria-label="Menú de perfil"
                    >
                        @php
                            $user = Auth::user();
                            $initials = strtoupper(substr($user->name ?? 'R', 0, 1) . substr($user->apellidos ?? 'E', 0, 1));
                        @endphp
                        <div class="user-avatar" aria-hidden="true">{{ $initials }}</div>
                        <div class="user-meta">
                            <span class="user-name">{{ $user->name ?? 'Recepcionista' }} {{ $user->apellidos ?? '' }}</span>
                            <span class="user-role">{{ $user->email ?? 'recepcion@barberstudio.com' }}</span>
                        </div>
                        <svg class="dropdown-arrow" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <!-- Menú flotante del perfil -->
                    <div class="profile-menu" id="profileMenu" role="menu" aria-labelledby="profileBtn">
                        <div style="padding: 10px 12px; border-bottom: 1px solid var(--border-subtle); margin-bottom: 4px;">
                            <p style="font-size: 13px; font-weight: 700; color: var(--text-white); margin-bottom: 2px;">{{ $user->name }} {{ $user->apellidos ?? '' }}</p>
                            <p style="font-size: 11px; color: var(--text-muted); word-break: break-all;">{{ $user->email }}</p>
                        </div>

                        <!-- Opción Mi Perfil -->
                        <a 
                            href="{{ route('recepcion.perfil') }}" 
                            class="menu-item {{ request()->routeIs('recepcion.perfil*') ? 'active' : '' }}" 
                            role="menuitem"
                        >
                            <div class="menu-item-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </div>
                            <span>Mi Perfil</span>
                        </a>

                        <div class="menu-divider" role="separator"></div>

                        <!-- Opción para cerrar sesión -->
                        <button 
                            type="button" 
                            onclick="document.getElementById('logout-form').submit();" 
                            class="menu-item logout-item" 
                            role="menuitem"
                        >
                            <div class="menu-item-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                    <polyline points="16 17 21 12 16 7"></polyline>
                                    <line x1="21" y1="12" x2="9" y2="12"></line>
                                </svg>
                            </div>
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

    <!-- CONTENEDOR ESTRUCTURAL: SIDEBAR + MAIN -->
    <div class="recepcion-layout" id="recepcionLayout">

        <!-- SIDEBAR DE NAVEGACIÓN -->
        <aside class="recepcion-sidebar" id="recepcionSidebar" aria-label="Menú de navegación">
            <div class="sidebar-header">
                <span class="sidebar-title">{{ Auth::user() && (int)Auth::user()->role_id === 1 ? 'Administración' : 'Recepción' }}</span>
                <button 
                    type="button" 
                    class="sidebar-toggle-btn" 
                    id="sidebarToggleBtn" 
                    aria-label="Colapsar o expandir menú lateral" 
                    aria-expanded="true" 
                    aria-controls="recepcionSidebar"
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
                <nav class="sidebar-nav" aria-label="Secciones del panel">
                    @if(Auth::user() && (int)Auth::user()->role_id === 1)
                        <!-- 1. Dashboard Admin -->
                        <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" title="Dashboard">
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
                        <a href="{{ route('admin.usuarios') }}" class="nav-item {{ request()->routeIs('admin.usuarios*') ? 'active' : '' }}" title="Usuarios">
                            <div class="nav-icon-box">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                            </div>
                            <span class="nav-label">Usuarios</span>
                        </a>

                        <!-- 3. Trabajadores -->
                        <a href="{{ url('/admin/trabajadores') }}" class="nav-item {{ request()->is('admin/trabajadores*') ? 'active' : '' }}" title="Trabajadores">
                            <div class="nav-icon-box">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="2" y="7" width="20" height="14" rx="2"></rect>
                                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                                </svg>
                            </div>
                            <span class="nav-label">Trabajadores</span>
                        </a>

                        <!-- 4. Clientes -->
                        <a href="{{ route('recepcion.clientes') }}" class="nav-item {{ request()->routeIs('recepcion.clientes*') ? 'active' : '' }}" title="Clientes">
                            <div class="nav-icon-box">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </div>
                            <span class="nav-label">Clientes</span>
                        </a>

                        <!-- 5. Servicios -->
                        <a href="{{ route('admin.services') }}" class="nav-item {{ request()->routeIs('admin.services*') ? 'active' : '' }}" title="Servicios">
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
                        </a>

                        <!-- 6. Citas -->
                        <a href="{{ route('admin.citas') }}" class="nav-item {{ request()->routeIs('admin.citas*') ? 'active' : '' }}" title="Citas">
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
                        <a href="{{ route('admin.configuracion') }}" class="nav-item {{ request()->routeIs('admin.configuracion*') ? 'active' : '' }}" title="Configuración general">
                            <div class="nav-icon-box">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="3"></circle>
                                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                                </svg>
                            </div>
                            <span class="nav-label">Configuración</span>
                        </a>
                    @else
                        <!-- 1. Inicio -->
                        <a 
                            href="{{ route('recepcion.dashboard') }}" 
                            class="nav-item {{ request()->routeIs('recepcion.dashboard') ? 'active' : '' }}" 
                            aria-current="{{ request()->routeIs('recepcion.dashboard') ? 'page' : 'false' }}"
                            title="Inicio"
                        >
                            <div class="nav-icon-box">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                                </svg>
                            </div>
                            <span class="nav-label">Inicio</span>
                            <span class="nav-badge badge-primary" style="background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3);">Turnos</span>
                        </a>

                        <!-- 2. Gestión de citas -->
                        <a 
                            href="{{ route('recepcion.citas') }}" 
                            class="nav-item {{ (request()->routeIs('recepcion.citas*') || request()->routeIs('recepcion.agendamiento*')) ? 'active' : '' }}" 
                            aria-current="{{ (request()->routeIs('recepcion.citas*') || request()->routeIs('recepcion.agendamiento*')) ? 'page' : 'false' }}"
                            title="Gestión de citas"
                        >
                            <div class="nav-icon-box">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                    <path d="m9 16 2 2 4-4"></path>
                                </svg>
                            </div>
                            <span class="nav-label">Gestión de citas</span>
                            <span class="nav-badge badge-primary">Agendar</span>
                        </a>

                        <!-- 3. Calendario -->
                        <a 
                            href="{{ route('recepcion.calendario') }}" 
                            class="nav-item {{ request()->routeIs('recepcion.calendario') ? 'active' : '' }}" 
                            aria-current="{{ request()->routeIs('recepcion.calendario') ? 'page' : 'false' }}"
                            title="Calendario"
                        >
                            <div class="nav-icon-box">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                            </div>
                            <span class="nav-label">Calendario</span>
                        </a>

                        <!-- 4. Clientes -->
                        <a 
                            href="{{ route('recepcion.clientes') }}" 
                            class="nav-item {{ request()->routeIs('recepcion.clientes') ? 'active' : '' }}" 
                            aria-current="{{ request()->routeIs('recepcion.clientes') ? 'page' : 'false' }}"
                            title="Clientes"
                        >
                            <div class="nav-icon-box">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                            </div>
                            <span class="nav-label">Clientes</span>
                        </a>

                    @endif
                </nav>
            </div>
        </aside>

        <!-- Fondo difuminado para cerrar el sidebar en dispositivos móviles -->
        <div class="sidebar-backdrop" id="sidebarBackdrop" aria-hidden="true"></div>

        <!-- ÁREA PRINCIPAL -->
        <main id="main-content" class="main-content" role="main">
            <div class="content-container">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- SCRIPTS PARA INTERACTIVIDAD -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Menú de Perfil desplegable
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

            // Menú Lateral en Escritorio: Colapsar / Expandir
            const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
            const recepcionSidebar = document.getElementById('recepcionSidebar');

            if (sidebarToggleBtn && recepcionSidebar) {
                sidebarToggleBtn.addEventListener('click', function () {
                    const isCollapsed = recepcionSidebar.classList.toggle('collapsed');
                    sidebarToggleBtn.setAttribute('aria-expanded', isCollapsed ? 'false' : 'true');
                    sidebarToggleBtn.setAttribute('title', isCollapsed ? 'Expandir menú' : 'Colapsar menú');
                });
            }

            // Menú Lateral en Móviles (Drawer)
            const mobileToggleBtn = document.getElementById('mobileToggleBtn');
            const sidebarBackdrop = document.getElementById('sidebarBackdrop');

            if (mobileToggleBtn && recepcionSidebar && sidebarBackdrop) {
                mobileToggleBtn.addEventListener('click', function () {
                    const isOpen = recepcionSidebar.classList.toggle('open-mobile');
                    sidebarBackdrop.classList.toggle('active', isOpen);
                    mobileToggleBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                });

                sidebarBackdrop.addEventListener('click', function () {
                    recepcionSidebar.classList.remove('open-mobile');
                    sidebarBackdrop.classList.remove('active');
                    mobileToggleBtn.setAttribute('aria-expanded', 'false');
                });
            }
        });
    </script>

    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: @json(session('success')),
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

    @if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: @json(session('error')),
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                background: '#0c142b',
                color: '#ffffff',
                iconColor: '#ffffff'
            });
        });
    </script>
    @endif

    @stack('scripts')

    @include('partials.inactivity-timer')
</body>
</html>
