@extends('cliente.layout')

@section('title', 'Beauty & Barber Studio - Inicio')

@push('styles')
<style>
    /* ===================================================
       ESTILOS DEL DASHBOARD DE CLIENTE (INICIO)
       =================================================== */
    .dashboard-wrapper {
        display: flex;
        flex-direction: column;
        gap: 28px;
    }

    /* ENCABEZADO Y BANNER DE BIENVENIDA PREMIUM */
    .welcome-banner {
        background: linear-gradient(135deg, rgba(10, 17, 36, 0.96) 0%, rgba(13, 22, 48, 0.98) 100%);
        border: 1px solid var(--border-subtle);
        border-radius: 20px;
        padding: 36px 42px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.05);
    }

    /* Resplandores ambientales de profundidad */
    .welcome-banner-decor-right {
        position: absolute;
        top: -60px;
        right: -60px;
        width: 320px;
        height: 320px;
        background: radial-gradient(circle, rgba(37, 99, 235, 0.16) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .welcome-banner-decor-left {
        position: absolute;
        bottom: -80px;
        left: 10%;
        width: 280px;
        height: 280px;
        background: radial-gradient(circle, rgba(127, 29, 29, 0.1) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    /* Distribución en 2 columnas */
    .welcome-layout {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 36px;
    }

    /* Contenido principal del banner */
    .welcome-content {
        flex: 1;
        min-width: 0;
        max-width: 720px;
    }

    /* Fila de badges superiores */
    .welcome-badge-row {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 14px;
    }

    .welcome-banner-badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        background: var(--barber-blue-pale);
        color: var(--barber-blue-light);
        border: 1px solid var(--barber-blue-border);
        padding: 5px 14px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }

    .welcome-banner-badge::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background-color: var(--barber-blue-light);
        display: inline-block;
        box-shadow: 0 0 8px rgba(96, 165, 250, 0.8);
    }

    .welcome-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(16, 185, 129, 0.1);
        color: #34d399;
        border: 1px solid rgba(16, 185, 129, 0.25);
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.04em;
    }

    .welcome-status-pill .status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background-color: #10b981;
        box-shadow: 0 0 6px rgba(16, 185, 129, 0.7);
    }

    /* Tipografía del encabezado */
    .welcome-title {
        font-size: 32px;
        font-weight: 800;
        color: var(--text-white);
        letter-spacing: -0.025em;
        margin-bottom: 10px;
        line-height: 1.25;
        overflow-wrap: break-word;
        word-break: break-word;
        text-shadow: 0 2px 12px rgba(0, 0, 0, 0.35);
    }

    .welcome-subtitle {
        font-size: 14.5px;
        color: var(--text-muted);
        line-height: 1.65;
        overflow-wrap: break-word;
        word-break: break-word;
        margin-bottom: 18px;
    }

    /* Fila de características del portal */
    .welcome-features-row {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
        padding-top: 14px;
        border-top: 1px solid var(--border-subtle);
    }

    .feature-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12.5px;
        font-weight: 500;
        color: #cbd5e1;
    }

    .feature-icon {
        width: 22px;
        height: 22px;
        border-radius: 6px;
        background: rgba(30, 58, 138, 0.28);
        color: var(--barber-blue-light);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .feature-icon svg {
        width: 13px;
        height: 13px;
    }

    .feature-divider {
        width: 4px;
        height: 4px;
        border-radius: 50%;
        background: var(--border-strong);
    }

    /* Tarjeta informativa del estudio en el lado derecho */
    .welcome-studio-card {
        background: linear-gradient(145deg, rgba(16, 27, 58, 0.7) 0%, rgba(9, 15, 33, 0.85) 100%);
        border: 1px solid var(--border-subtle);
        border-radius: 16px;
        padding: 20px 22px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        flex-shrink: 0;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        backdrop-filter: blur(8px);
        min-width: 250px;
        max-width: 280px;
    }

    .studio-card-header {
        display: flex;
        align-items: center;
        gap: 12px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--border-subtle);
    }

    .studio-card-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: linear-gradient(135deg, var(--barber-blue), #1d4ed8);
        color: var(--text-white);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.35);
    }

    .studio-card-icon svg {
        width: 20px;
        height: 20px;
    }

    .studio-card-titles {
        display: flex;
        flex-direction: column;
    }

    .studio-card-brand {
        font-size: 13px;
        font-weight: 700;
        color: var(--text-white);
        line-height: 1.2;
    }

    .studio-card-tag {
        font-size: 11px;
        font-weight: 600;
        color: var(--barber-blue-light);
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-top: 2px;
    }

    .studio-card-items {
        display: flex;
        flex-direction: column;
        gap: 9px;
    }

    .studio-card-item {
        display: flex;
        align-items: center;
        gap: 9px;
        font-size: 12px;
        color: #cbd5e1;
    }

    .studio-card-item svg {
        width: 15px;
        height: 15px;
        color: var(--barber-blue-light);
        flex-shrink: 0;
    }

    /* Adaptación para pantallas medianas y móviles */
    @media (max-width: 992px) {
        .welcome-layout {
            flex-direction: column;
            align-items: flex-start;
            gap: 22px;
        }

        .welcome-studio-card {
            width: 100%;
            max-width: 100%;
            min-width: 0;
        }
    }

    @media (max-width: 768px) {
        .welcome-banner {
            padding: 24px 20px;
            border-radius: 16px;
        }

        .welcome-title {
            font-size: 24px;
            margin-bottom: 8px;
        }

        .welcome-subtitle {
            font-size: 13.5px;
            line-height: 1.5;
            margin-bottom: 14px;
        }

        .welcome-banner-badge {
            font-size: 10px;
            padding: 4px 10px;
        }

        .feature-divider {
            display: none;
        }
    }

    @media (max-width: 480px) {
        .welcome-banner {
            padding: 20px 16px;
        }

        .welcome-title {
            font-size: 20px;
        }

        .welcome-features-row {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }
    }

    /* ===================================================
       TARJETA DE RESUMEN DE LA PRÓXIMA CITA (SUBTAREA 2)
       =================================================== */
    .next-appointment-card {
        background: linear-gradient(145deg, rgba(14, 23, 48, 0.94) 0%, rgba(9, 15, 33, 0.98) 100%);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 20px;
        padding: 30px 34px;
        box-shadow: 0 14px 36px -6px rgba(0, 0, 0, 0.45), inset 0 1px 0 rgba(255, 255, 255, 0.06);
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(12px);
        transition: border-color 0.25s ease, box-shadow 0.25s ease;
    }

    .next-appointment-card:hover {
        border-color: rgba(96, 165, 250, 0.22);
        box-shadow: 0 18px 42px -6px rgba(0, 0, 0, 0.55), inset 0 1px 0 rgba(255, 255, 255, 0.08);
    }

    .next-appointment-decor {
        position: absolute;
        top: -70px;
        right: -70px;
        width: 260px;
        height: 260px;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.12) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .appointment-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
        padding-bottom: 22px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        margin-bottom: 24px;
        position: relative;
        z-index: 1;
    }

    .appointment-title-group {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .appointment-header-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.25) 0%, rgba(30, 58, 138, 0.35) 100%);
        color: #60a5fa;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(59, 130, 246, 0.32);
        flex-shrink: 0;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.25);
    }

    .appointment-header-icon svg {
        width: 22px;
        height: 22px;
    }

    .appointment-eyebrow {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        color: var(--barber-blue-light);
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 3px;
    }

    .appointment-eyebrow::before {
        content: '';
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background-color: var(--barber-blue-light);
        box-shadow: 0 0 6px rgba(96, 165, 250, 0.8);
    }

    .appointment-main-title {
        font-size: 21px;
        font-weight: 800;
        color: #f8fafc;
        letter-spacing: -0.02em;
        margin: 0;
        line-height: 1.25;
    }

    .appointment-header-actions {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .appointment-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.03em;
        background: rgba(16, 185, 129, 0.12);
        color: #34d399;
        border: 1px solid rgba(16, 185, 129, 0.28);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }

    .appointment-status-badge .status-indicator-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background-color: #10b981;
        box-shadow: 0 0 8px rgba(16, 185, 129, 0.9);
        animation: pulseDot 2s infinite;
    }

    .btn-appointment-agendar {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, #1e40af 0%, #1d4ed8 100%);
        color: #ffffff;
        font-size: 13px;
        font-weight: 700;
        padding: 7px 18px;
        border-radius: 10px;
        text-decoration: none;
        border: 1px solid rgba(147, 197, 253, 0.3);
        box-shadow: 0 4px 14px rgba(30, 64, 175, 0.3);
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        white-space: nowrap;
    }

    .btn-appointment-agendar:hover {
        background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(37, 99, 235, 0.45);
        border-color: rgba(147, 197, 253, 0.5);
        color: #ffffff;
    }

    .btn-appointment-agendar:focus-visible {
        outline: 2px solid var(--barber-blue-light);
        outline-offset: 3px;
    }

    .btn-appointment-agendar svg {
        width: 15px;
        height: 15px;
        transition: transform 0.2s ease;
    }

    .btn-appointment-agendar:hover svg {
        transform: scale(1.1);
    }

    .appointment-card-footer {
        margin-top: 24px;
        padding-top: 18px;
        border-top: 1px solid rgba(255, 255, 255, 0.06);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
        position: relative;
        z-index: 1;
    }

    .appointment-footer-info {
        display: flex;
        align-items: center;
        gap: 9px;
        color: #94a3b8;
        font-size: 13px;
        font-weight: 500;
    }

    .appointment-footer-info svg {
        width: 16px;
        height: 16px;
        color: var(--barber-blue-light);
        flex-shrink: 0;
    }

    .appointment-footer-link {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        color: #60a5fa;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        transition: color 0.2s ease, transform 0.2s ease;
    }

    .appointment-footer-link:hover {
        color: #93c5fd;
        text-decoration: underline;
        transform: translateX(2px);
    }

    .appointment-footer-link svg {
        width: 14px;
        height: 14px;
        transition: transform 0.2s ease;
    }

    .appointment-footer-link:hover svg {
        transform: translateX(3px);
    }

    .appointment-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
        position: relative;
        z-index: 1;
    }

    .appointment-data-box {
        background: rgba(18, 29, 60, 0.4);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 15px;
        padding: 18px 20px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        transition: transform 0.2s ease, border-color 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
    }

    .appointment-data-box:hover {
        transform: translateY(-2px);
        border-color: rgba(255, 255, 255, 0.12);
        background: rgba(18, 29, 60, 0.65);
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.25);
    }

    .data-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.09em;
        color: #94a3b8;
    }

    .data-row {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .data-icon {
        width: 40px;
        height: 40px;
        border-radius: 11px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .data-icon svg {
        width: 19px;
        height: 19px;
    }

    .data-icon.icon-blue {
        background: rgba(37, 99, 235, 0.16);
        color: #60a5fa;
        border: 1px solid rgba(37, 99, 235, 0.28);
    }

    .data-icon.icon-amber {
        background: rgba(245, 158, 11, 0.16);
        color: #fbbf24;
        border: 1px solid rgba(245, 158, 11, 0.28);
    }

    .data-icon.icon-green {
        background: rgba(16, 185, 129, 0.16);
        color: #34d399;
        border: 1px solid rgba(16, 185, 129, 0.28);
    }

    .data-icon.icon-purple {
        background: rgba(168, 85, 247, 0.16);
        color: #c084fc;
        border: 1px solid rgba(168, 85, 247, 0.28);
    }

    .data-text {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .data-value {
        font-size: 15px;
        font-weight: 700;
        color: #f8fafc;
        line-height: 1.35;
        overflow-wrap: break-word;
        word-break: break-word;
    }

    .data-sub {
        font-size: 12px;
        color: #94a3b8;
        margin-top: 3px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .data-pill {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 10.5px;
        font-weight: 600;
        letter-spacing: 0.04em;
    }

    .data-pill-blue {
        background: rgba(37, 99, 235, 0.18);
        color: #93c5fd;
        border: 1px solid rgba(59, 130, 246, 0.25);
    }

    .data-pill-amber {
        background: rgba(245, 158, 11, 0.16);
        color: #fcd34d;
        border: 1px solid rgba(245, 158, 11, 0.25);
    }

    .text-capitalize {
        text-transform: capitalize;
    }

    @media (max-width: 768px) {
        .next-appointment-card {
            padding: 22px 20px;
            border-radius: 16px;
        }

        .appointment-main-title {
            font-size: 18px;
        }

        .appointment-grid {
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .appointment-header-actions {
            width: 100%;
            justify-content: space-between;
        }

        .appointment-card-footer {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }

        .appointment-footer-link {
            align-self: flex-start;
        }
    }

    @media (max-width: 480px) {
        .appointment-header-actions {
            flex-direction: column;
            align-items: stretch;
            gap: 10px;
        }

        .btn-appointment-agendar {
            width: 100%;
            justify-content: center;
        }
    }

    /* ===================================================
       ESTADO VACÍO: SIN CITAS PROGRAMADAS (SUBTAREA 3)
       =================================================== */
    .empty-appointment-section {
        width: 100%;
    }

    .empty-appointment-card {
        background: linear-gradient(145deg, rgba(14, 23, 48, 0.94) 0%, rgba(9, 15, 33, 0.98) 100%);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 20px;
        padding: 56px 36px;
        box-shadow: 0 14px 36px -6px rgba(0, 0, 0, 0.45), inset 0 1px 0 rgba(255, 255, 255, 0.06);
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(12px);
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        transition: border-color 0.25s ease, box-shadow 0.25s ease;
    }

    .empty-appointment-card:hover {
        border-color: rgba(96, 165, 250, 0.2);
        box-shadow: 0 18px 42px -6px rgba(0, 0, 0, 0.55), inset 0 1px 0 rgba(255, 255, 255, 0.08);
    }

    .empty-appointment-decor {
        position: absolute;
        top: -80px;
        right: -80px;
        width: 280px;
        height: 280px;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.1) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .empty-appointment-content {
        position: relative;
        z-index: 1;
        max-width: 580px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .empty-icon-wrap {
        width: 68px;
        height: 68px;
        border-radius: 20px;
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.2) 0%, rgba(30, 58, 138, 0.3) 100%);
        color: var(--barber-blue-light);
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(59, 130, 246, 0.32);
        margin-bottom: 22px;
        box-shadow: 0 6px 20px rgba(37, 99, 235, 0.22);
    }

    .empty-icon-wrap svg {
        width: 32px;
        height: 32px;
    }

    .empty-badge-pill {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 4px 14px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        background: rgba(96, 165, 250, 0.1);
        color: var(--barber-blue-light);
        border: 1px solid rgba(96, 165, 250, 0.25);
        margin-bottom: 14px;
    }

    .empty-pulse-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background-color: var(--barber-blue-light);
        box-shadow: 0 0 6px rgba(96, 165, 250, 0.8);
    }

    .empty-main-title {
        font-size: 22px;
        font-weight: 800;
        color: #f8fafc;
        letter-spacing: -0.02em;
        margin-bottom: 12px;
        line-height: 1.3;
    }

    .empty-description {
        font-size: 14.5px;
        color: var(--text-muted);
        line-height: 1.65;
        margin-bottom: 28px;
        max-width: 500px;
    }

    .empty-action-group {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 14px;
    }

    .btn-empty-primary {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: linear-gradient(135deg, #1e40af 0%, #1d4ed8 100%);
        color: #ffffff;
        font-size: 14px;
        font-weight: 700;
        padding: 12px 26px;
        border-radius: 12px;
        text-decoration: none;
        border: 1px solid rgba(147, 197, 253, 0.3);
        box-shadow: 0 4px 16px rgba(30, 64, 175, 0.35);
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .btn-empty-primary:hover {
        background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(37, 99, 235, 0.45);
        border-color: rgba(147, 197, 253, 0.5);
        color: #ffffff;
    }

    .btn-empty-primary:focus-visible {
        outline: 2px solid var(--barber-blue-light);
        outline-offset: 3px;
    }

    .btn-empty-primary svg {
        width: 18px;
        height: 18px;
        transition: transform 0.2s ease;
    }

    .btn-empty-primary:hover svg {
        transform: scale(1.1);
    }

    @media (max-width: 768px) {
        .empty-appointment-card {
            padding: 38px 20px;
            border-radius: 16px;
        }

        .empty-icon-wrap {
            width: 58px;
            height: 58px;
            border-radius: 16px;
            margin-bottom: 18px;
        }

        .empty-icon-wrap svg {
            width: 28px;
            height: 28px;
        }

        .empty-main-title {
            font-size: 19px;
            margin-bottom: 10px;
        }

        .empty-description {
            font-size: 13.5px;
            margin-bottom: 22px;
            line-height: 1.55;
        }

        .btn-empty-primary {
            width: 100%;
            justify-content: center;
            padding: 13px 20px;
        }
    }

    /* ===================================================
       ACCESOS RÁPIDOS A SECCIONES PRINCIPALES (SUBTAREA 4)
       =================================================== */
    .quick-access-section {
        width: 100%;
        margin-top: 4px;
    }

    .quick-access-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 18px;
    }

    .quick-access-header-left {
        display: flex;
        flex-direction: column;
    }

    .quick-access-eyebrow {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--barber-blue-light);
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 4px;
    }

    .quick-access-eyebrow::before {
        content: '';
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background-color: var(--barber-blue-light);
        box-shadow: 0 0 6px rgba(96, 165, 250, 0.8);
    }

    .quick-access-title {
        font-size: 20px;
        font-weight: 800;
        color: #f8fafc;
        letter-spacing: -0.02em;
        margin: 0;
        line-height: 1.25;
    }

    .quick-access-subtitle {
        font-size: 13px;
        color: #94a3b8;
        margin: 0;
    }

    .quick-access-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 18px;
    }

    .quick-access-card {
        background: linear-gradient(145deg, rgba(14, 23, 48, 0.94) 0%, rgba(9, 15, 33, 0.98) 100%);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 18px;
        padding: 24px 26px;
        box-shadow: 0 10px 28px -6px rgba(0, 0, 0, 0.38), inset 0 1px 0 rgba(255, 255, 255, 0.05);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 20px;
        text-decoration: none;
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(12px);
        transition: transform 0.22s cubic-bezier(0.16, 1, 0.3, 1),
                    border-color 0.22s ease,
                    box-shadow 0.22s ease;
    }

    .quick-access-card:hover {
        transform: translateY(-3px);
        border-color: rgba(96, 165, 250, 0.32);
        box-shadow: 0 14px 34px -4px rgba(0, 0, 0, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.08);
    }

    .quick-access-card:focus-visible {
        outline: 2px solid #60a5fa;
        outline-offset: 3px;
    }

    .quick-access-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
    }

    .quick-access-icon-wrap {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: transform 0.2s ease;
    }

    .quick-access-card:hover .quick-access-icon-wrap {
        transform: scale(1.05);
    }

    .quick-access-icon-wrap svg {
        width: 22px;
        height: 22px;
    }

    .quick-access-icon-wrap.icon-blue {
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.25) 0%, rgba(30, 58, 138, 0.35) 100%);
        color: #60a5fa;
        border: 1px solid rgba(59, 130, 246, 0.32);
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.25);
    }

    .quick-access-icon-wrap.icon-purple {
        background: rgba(168, 85, 247, 0.18);
        color: #c084fc;
        border: 1px solid rgba(168, 85, 247, 0.3);
        box-shadow: 0 4px 14px rgba(168, 85, 247, 0.2);
    }

    .quick-access-tag {
        font-size: 10.5px;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        padding: 4px 10px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.08);
        color: #94a3b8;
    }

    .quick-access-info {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .quick-access-card-title {
        font-size: 18px;
        font-weight: 800;
        color: #f8fafc;
        margin: 0;
        letter-spacing: -0.015em;
        line-height: 1.3;
        transition: color 0.2s ease;
    }

    .quick-access-card:hover .quick-access-card-title {
        color: #93c5fd;
    }

    .quick-access-card-desc {
        font-size: 13.5px;
        color: #94a3b8;
        line-height: 1.55;
        margin: 0;
    }

    .quick-access-action {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 14px;
        border-top: 1px solid rgba(255, 255, 255, 0.05);
        color: #60a5fa;
        font-size: 13px;
        font-weight: 700;
        transition: color 0.2s ease;
    }

    .quick-access-card:hover .quick-access-action {
        color: #93c5fd;
    }

    .action-arrow {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: rgba(59, 130, 246, 0.12);
        border: 1px solid rgba(59, 130, 246, 0.25);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #60a5fa;
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.2s ease;
    }

    .quick-access-card:hover .action-arrow {
        transform: translateX(4px);
        background: rgba(59, 130, 246, 0.24);
        color: #ffffff;
    }

    .action-arrow svg {
        width: 14px;
        height: 14px;
    }

    @media (max-width: 768px) {
        .quick-access-grid {
            grid-template-columns: 1fr;
            gap: 14px;
        }

        .quick-access-card {
            padding: 20px 22px;
            border-radius: 16px;
        }

        .quick-access-card-title {
            font-size: 17px;
        }

        .quick-access-card-desc {
            font-size: 13px;
        }
    }
</style>
@endpush

@section('content')
<div class="dashboard-wrapper">
    @php
        $clientName = trim($user?->name ?? (auth()->user()?->name ?? ''));
    @endphp

    <!-- 1. SALUDO Y BIENVENIDA AL CLIENTE -->
    <section class="welcome-banner" aria-labelledby="welcomeTitle">
        <div class="welcome-banner-decor-right" aria-hidden="true"></div>
        <div class="welcome-banner-decor-left" aria-hidden="true"></div>

        <div class="welcome-layout">
            <!-- Columna Principal: Saludo e información del cliente -->
            <div class="welcome-content">
                <div class="welcome-badge-row">
                    <div class="welcome-banner-badge">
                        Portal Cliente &bull; Beauty & Barber Studio
                    </div>
                    <div class="welcome-status-pill">
                        <span class="status-dot"></span>
                        <span>Cuenta Activa</span>
                    </div>
                </div>

                @if($clientName !== '')
                    <h1 id="welcomeTitle" class="welcome-title">
                        ¡Hola, {{ $clientName }}! Te damos la bienvenida
                    </h1>
                @else
                    <h1 id="welcomeTitle" class="welcome-title">
                        ¡Te damos la bienvenida a Beauty & Barber Studio!
                    </h1>
                @endif

                <p class="welcome-subtitle">
                    Este es tu panel personal en Beauty & Barber Studio. Desde aquí podrás agendar citas de barbería y estilismo, revisar tu historial de atención y gestionar tu información personal de manera intuitiva.
                </p>

                <!-- Micro-características del portal -->
                <div class="welcome-features-row">
                    <div class="feature-item">
                        <div class="feature-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path>
                            </svg>
                        </div>
                        <span>Servicios de barbería y estilismo</span>
                    </div>
                </div>
            </div>

            <!-- Columna Lateral: Tarjeta informativa del estudio -->
            <div class="welcome-studio-card" aria-hidden="true">
                <div class="studio-card-header">
                    <div class="studio-card-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path>
                        </svg>
                    </div>
                    <div class="studio-card-titles">
                        <span class="studio-card-brand">Beauty & Barber Studio</span>
                        <span class="studio-card-tag">Sucursal Principal</span>
                    </div>
                </div>
                <div class="studio-card-items">
                    <div class="studio-card-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        <span>Lun - Sáb: 9:00 AM – 8:00 PM</span>
                    </div>
                    <div class="studio-card-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                        <span>Atención con cita previa</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if($proximaCita)
        <!-- 2. RESUMEN DE LA PRÓXIMA CITA -->
        <section class="next-appointment-section" aria-labelledby="nextAppointmentHeading">
            <div class="next-appointment-card">
                <div class="next-appointment-decor" aria-hidden="true"></div>

                <!-- Cabecera de la tarjeta -->
                <div class="appointment-card-header">
                    <div class="appointment-title-group">
                        <div class="appointment-header-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </div>
                        <div>
                            <span class="appointment-eyebrow">Tu Próxima Cita</span>
                            <h2 id="nextAppointmentHeading" class="appointment-main-title">Resumen de Cita Programada</h2>
                        </div>
                    </div>

                    <div class="appointment-header-actions">
                        <div class="appointment-status-badge">
                            <span class="status-indicator-dot"></span>
                            <span>{{ $proximaCita->estado_label ?? 'Confirmada' }}</span>
                        </div>
                        <a href="{{ route('cliente.agendamiento') }}" class="btn-appointment-agendar" id="btnAgendamientoHeader" title="Ir a la sección Agendamiento">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                                <line x1="12" y1="14" x2="12" y2="18"></line>
                                <line x1="10" y1="16" x2="14" y2="16"></line>
                            </svg>
                            <span>Ir a Agendamiento</span>
                        </a>
                    </div>
                </div>

                <!-- Grid de Datos Clave -->
                <div class="appointment-grid">
                    <!-- 1. Servicio -->
                    <div class="appointment-data-box">
                        <span class="data-label">Servicio</span>
                        <div class="data-row">
                            <div class="data-icon icon-blue" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path>
                                </svg>
                            </div>
                            <div class="data-text">
                                <span class="data-value">{{ $proximaCita->service?->name ?? 'Servicio programado' }}</span>
                                @if($proximaCita->service?->category)
                                    <span class="data-sub">
                                        <span class="data-pill data-pill-blue">{{ ucfirst($proximaCita->service->category) }}</span>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- 2. Fecha -->
                    <div class="appointment-data-box">
                        <span class="data-label">Fecha</span>
                        <div class="data-row">
                            <div class="data-icon icon-amber" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                            </div>
                            <div class="data-text">
                                @php
                                    $fechaCarbon = $proximaCita->fecha ? $proximaCita->fecha_hora_inicio : null;
                                    $fechaFormateada = $fechaCarbon ? $fechaCarbon->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') : 'Fecha por confirmar';
                                    $fechaSubtexto = $fechaCarbon 
                                        ? ($fechaCarbon->isToday() ? 'Hoy' : ($fechaCarbon->isTomorrow() ? 'Mañana' : 'En ' . max(1, (int)ceil($proximaCita->minutos_para_inicio / 1440)) . ' días'))
                                        : 'Próximamente';
                                @endphp
                                <span class="data-value text-capitalize">{{ $fechaFormateada }}</span>
                                <span class="data-sub">
                                    <span class="data-pill data-pill-amber">{{ $fechaSubtexto }}</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Hora Programada -->
                    <div class="appointment-data-box">
                        <span class="data-label">Hora programada</span>
                        <div class="data-row">
                            <div class="data-icon icon-green" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                            </div>
                            <div class="data-text">
                                <span class="data-value">{{ substr((string) ($proximaCita->hora_inicio ?? ''), 0, 5) ?: '--:--' }} hrs</span>
                                <span class="data-sub">Duración: {{ $proximaCita->duracion_minutos ?? 30 }} min</span>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Especialista Asignado -->
                    <div class="appointment-data-box">
                        <span class="data-label">Especialista asignado</span>
                        <div class="data-row">
                            <div class="data-icon icon-purple" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </div>
                            <div class="data-text">
                                @php
                                    $trabajadorNombre = trim(($proximaCita->trabajador?->user?->name ?? '') . ' ' . ($proximaCita->trabajador?->user?->apellidos ?? ''));
                                    if ($trabajadorNombre === '') {
                                        $trabajadorNombre = $proximaCita->trabajador?->nombre_completo ?: ($proximaCita->trabajador?->nombre ?: 'Especialista asignado');
                                    }
                                @endphp
                                <span class="data-value">{{ $trabajadorNombre }}</span>
                                <span class="data-sub">Atención personalizada</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pie de la tarjeta: Enlace al Historial de esta cita -->
                <div class="appointment-card-footer">
                    <div class="appointment-footer-info">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="16" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                        </svg>
                        <span>¿Deseas consultar el detalle completo o gestionar esta cita?</span>
                    </div>
                    <a href="{{ route('cliente.historial') }}{{ $proximaCita?->id ? '#citaCard-' . $proximaCita->id : '' }}" class="appointment-footer-link" id="btnHistorialCitaFooter" title="Ver cita en el historial">
                        <span>Ver cita en el historial</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </a>
                </div>
            </div>
        </section>
    @else
        <!-- 3. ESTADO VACÍO: SIN CITAS PROGRAMADAS (SUBTAREA 3) -->
        <section class="empty-appointment-section" aria-labelledby="emptyAppointmentHeading">
            <div class="empty-appointment-card">
                <div class="empty-appointment-decor" aria-hidden="true"></div>

                <div class="empty-appointment-content">
                    <div class="empty-icon-wrap" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                            <path d="M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01"></path>
                        </svg>
                    </div>

                    <div class="empty-badge-pill">
                        <span class="empty-pulse-dot" aria-hidden="true"></span>
                        <span>Sin citas pendientes</span>
                    </div>

                    <h2 id="emptyAppointmentHeading" class="empty-main-title">
                        Aún no tienes citas programadas
                    </h2>

                    <p class="empty-description">
                        Cuando agendes tu próxima cita, aquí podrás consultar los detalles de tu servicio, fecha, hora y especialista.
                    </p>

                    <div class="empty-action-group">
                        <a href="{{ route('cliente.agendamiento') }}" class="btn-empty-primary" id="btnAgendarCitaVacio">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                                <line x1="12" y1="14" x2="12" y2="18"></line>
                                <line x1="10" y1="16" x2="14" y2="16"></line>
                            </svg>
                            <span>Agendar una cita</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- 4. ACCESOS RÁPIDOS A SECCIONES PRINCIPALES (SUBTAREA 4) -->
    <section class="quick-access-section" aria-labelledby="quickAccessHeading">
        <div class="quick-access-header">
            <div class="quick-access-header-left">
                <span class="quick-access-eyebrow">Accesos Rápidos</span>
                <h2 id="quickAccessHeading" class="quick-access-title">¿Qué deseas hacer hoy?</h2>
            </div>
            <p class="quick-access-subtitle">Accede rápidamente a las secciones principales de tu cuenta</p>
        </div>

        <div class="quick-access-grid">
            <!-- Acceso 1: Agendamiento -->
            <a href="{{ route('cliente.agendamiento') }}" class="quick-access-card" id="quickAccessAgendamiento" aria-label="Agendar una cita: Explora nuestros servicios y reserva tu próxima cita">
                <div class="quick-access-top">
                    <div class="quick-access-icon-wrap icon-blue" aria-hidden="true">
                        <svg width="22" height="22" style="width: 22px; height: 22px; max-width: 22px; max-height: 22px; display: block;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                            <line x1="12" y1="14" x2="12" y2="18"></line>
                            <line x1="10" y1="16" x2="14" y2="16"></line>
                        </svg>
                    </div>
                    <span class="quick-access-tag">Reserva online</span>
                </div>
                <div class="quick-access-info">
                    <h3 class="quick-access-card-title">Agendar una cita</h3>
                    <p class="quick-access-card-desc">Explora nuestros servicios y reserva tu próxima cita</p>
                </div>
                <div class="quick-access-action" aria-hidden="true">
                    <span>Ir a Agendamiento</span>
                    <div class="action-arrow">
                        <svg width="14" height="14" style="width: 14px; height: 14px; max-width: 14px; max-height: 14px; display: block;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </div>
                </div>
            </a>

            <!-- Acceso 2: Historial -->
            <a href="{{ route('cliente.historial') }}" class="quick-access-card" id="quickAccessHistorial" aria-label="Mi historial de citas: Consulta tus citas anteriores y programadas">
                <div class="quick-access-top">
                    <div class="quick-access-icon-wrap icon-purple" aria-hidden="true">
                        <svg width="22" height="22" style="width: 22px; height: 22px; max-width: 22px; max-height: 22px; display: block;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <span class="quick-access-tag">Mis registros</span>
                </div>
                <div class="quick-access-info">
                    <h3 class="quick-access-card-title">Mi historial de citas</h3>
                    <p class="quick-access-card-desc">Consulta tus citas anteriores y programadas</p>
                </div>
                <div class="quick-access-action" aria-hidden="true">
                    <span>Ver historial</span>
                    <div class="action-arrow">
                        <svg width="14" height="14" style="width: 14px; height: 14px; max-width: 14px; max-height: 14px; display: block;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </div>
                </div>
            </a>
        </div>
    </section>
</div>
@endsection
