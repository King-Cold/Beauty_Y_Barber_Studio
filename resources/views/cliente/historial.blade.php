@extends('cliente.layout')

@section('title', 'Beauty & Barber Studio - Historial de Citas')

@push('styles')
<style>
    /* ===================================================
       SISTEMA VISUAL PREMIUM - HISTORIAL DE CITAS
       =================================================== */

    /* Encabezado */
    .historial-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 28px;
    }

    .historial-header-info {
        max-width: 650px;
    }

    .historial-badge-top {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        background: rgba(37, 99, 235, 0.12);
        border: 1px solid rgba(37, 99, 235, 0.28);
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #60a5fa;
        margin-bottom: 8px;
    }

    .historial-badge-top::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #3b82f6;
        box-shadow: 0 1px 3px rgba(37, 99, 235, 0.3);
    }

    .section-title {
        font-size: 28px;
        font-weight: 800;
        color: var(--text-white);
        letter-spacing: -0.025em;
        margin-bottom: 6px;
        line-height: 1.2;
    }

    .section-subtitle {
        font-size: 14px;
        color: var(--text-muted);
        line-height: 1.5;
    }

    .btn-agendar-nueva {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 10px 20px;
        background: #1e40af;
        color: #ffffff;
        font-size: 13.5px;
        font-weight: 600;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.2s ease;
        border: 1px solid rgba(147, 197, 253, 0.28);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.35);
    }

    .btn-agendar-nueva:hover {
        transform: translateY(-1px);
        background: #1d4ed8;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
        border-color: rgba(147, 197, 253, 0.45);
        color: #ffffff;
    }

    .btn-agendar-nueva svg {
        width: 18px;
        height: 18px;
        transition: transform 0.2s ease;
    }

    .btn-agendar-nueva:hover svg {
        transform: rotate(90deg);
    }

    /* KPI Summary Cards */
    .kpi-summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
        margin-bottom: 28px;
    }

    .kpi-card {
        background: linear-gradient(145deg, rgba(14, 22, 45, 0.7) 0%, rgba(9, 14, 30, 0.85) 100%);
        border: 1px solid var(--border-subtle);
        border-radius: 14px;
        padding: 18px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.18);
        transition: all 0.25s var(--ease-fluid);
        backdrop-filter: blur(8px);
    }

    .kpi-card:hover {
        border-color: rgba(255, 255, 255, 0.14);
        transform: translateY(-2px);
    }

    .kpi-icon-wrap {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .kpi-icon-wrap.blue {
        background: rgba(37, 99, 235, 0.15);
        color: #60a5fa;
        border: 1px solid rgba(37, 99, 235, 0.3);
    }

    .kpi-icon-wrap.green {
        background: rgba(16, 185, 129, 0.15);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    .kpi-icon-wrap.purple {
        background: rgba(139, 92, 246, 0.15);
        color: #a78bfa;
        border: 1px solid rgba(139, 92, 246, 0.3);
    }

    .kpi-icon-wrap.red {
        background: rgba(239, 68, 68, 0.15);
        color: #f87171;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }

    .kpi-icon-wrap.amber {
        background: rgba(245, 158, 11, 0.15);
        color: #f59e0b;
        border: 1px solid rgba(245, 158, 11, 0.3);
    }

    .kpi-icon-wrap svg {
        width: 22px;
        height: 22px;
    }

    .kpi-content {
        display: flex;
        flex-direction: column;
    }

    .kpi-number {
        font-size: 24px;
        font-weight: 800;
        color: var(--text-white);
        line-height: 1.1;
        letter-spacing: -0.02em;
    }

    .kpi-label {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-muted);
        margin-top: 2px;
    }

    /* Barra de Filtros Segmentada */
    .filter-wrapper {
        background: rgba(11, 18, 38, 0.65);
        border: 1px solid var(--border-subtle);
        border-radius: 14px;
        padding: 6px;
        margin-bottom: 24px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        overflow-x: auto;
        max-width: 100%;
        backdrop-filter: blur(8px);
    }

    .filter-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: transparent;
        border: 1px solid transparent;
        color: var(--text-muted);
        padding: 8px 18px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s var(--ease-fluid);
        white-space: nowrap;
    }

    .filter-btn:hover {
        color: var(--text-white);
        background: rgba(255, 255, 255, 0.04);
    }

    .filter-btn.active {
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.2) 0%, rgba(30, 64, 175, 0.25) 100%);
        color: #60a5fa;
        border-color: rgba(37, 99, 235, 0.4);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.25);
    }

    .filter-btn svg {
        width: 15px;
        height: 15px;
        opacity: 0.8;
    }

    .filter-badge-count {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 800;
        background: rgba(255, 255, 255, 0.07);
        color: inherit;
    }

    .filter-btn.active .filter-badge-count {
        background: rgba(37, 99, 235, 0.35);
        color: #bfdbfe;
    }

    /* Grid de Citas */
    .citas-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(370px, 1fr));
        gap: 22px;
    }

    /* Tarjeta de Cita */
    .cita-card {
        background: linear-gradient(155deg, rgba(13, 22, 46, 0.85) 0%, rgba(8, 14, 32, 0.95) 100%);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 18px;
        padding: 24px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
        backdrop-filter: blur(12px);
    }

    .cita-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: transparent;
        transition: background 0.3s ease;
    }

    .cita-card.proxima {
        border-color: rgba(37, 99, 235, 0.3);
    }
    .cita-card.proxima::before {
        background: linear-gradient(90deg, #2563eb 0%, #3b82f6 100%);
        box-shadow: none;
    }

    .cita-card.cancelada {
        border-color: rgba(239, 68, 68, 0.25);
        opacity: 0.92;
    }
    .cita-card.cancelada::before {
        background: linear-gradient(90deg, #dc2626 0%, #ef4444 100%);
    }

    .cita-card.vencida {
        border-color: rgba(245, 158, 11, 0.3);
        opacity: 0.95;
    }
    .cita-card.vencida::before {
        background: linear-gradient(90deg, #d97706 0%, #f59e0b 100%);
        box-shadow: none;
    }

    .cita-card.pasada:not(.cancelada):not(.vencida) {
        border-color: rgba(16, 185, 129, 0.25);
    }
    .cita-card.pasada:not(.cancelada):not(.vencida)::before {
        background: linear-gradient(90deg, #059669 0%, #10b981 100%);
    }

    .cita-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 34px rgba(0, 0, 0, 0.35);
        border-color: rgba(255, 255, 255, 0.18);
    }

    /* Cabecera de la Tarjeta */
    .cita-card-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 14px;
        gap: 10px;
    }

    .cita-category-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #94a3b8;
        background: rgba(255, 255, 255, 0.05);
        padding: 4px 10px;
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .cita-category-badge svg {
        width: 13px;
        height: 13px;
        color: #60a5fa;
    }

    /* Estado Badge */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
    }

    .status-badge::before {
        content: '';
        width: 7px;
        height: 7px;
        border-radius: 50%;
        display: inline-block;
    }

    .status-badge.status-confirmada,
    .status-badge.status-programada {
        background: rgba(16, 185, 129, 0.15);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.35);
    }
    .status-badge.status-confirmada::before,
    .status-badge.status-programada::before {
        background-color: #10b981;
        box-shadow: 0 1px 3px rgba(16, 185, 129, 0.25);
        animation: pulseDot 2s infinite;
    }

    @keyframes pulseDot {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(0.85); }
    }

    .status-badge.status-pendiente {
        background: rgba(245, 158, 11, 0.15);
        color: #f59e0b;
        border: 1px solid rgba(245, 158, 11, 0.35);
    }
    .status-badge.status-pendiente::before {
        background-color: #f59e0b;
    }

    .status-badge.status-en_proceso {
        background: rgba(59, 130, 246, 0.15);
        color: #60a5fa;
        border: 1px solid rgba(59, 130, 246, 0.35);
    }
    .status-badge.status-en_proceso::before {
        background-color: #60a5fa;
    }

    .status-badge.status-completada {
        background: rgba(13, 148, 136, 0.12);
        color: #2dd4bf;
        border: 1px solid rgba(13, 148, 136, 0.28);
    }
    .status-badge.status-completada::before {
        background-color: #2dd4bf;
    }

    .status-badge.status-cancelada {
        background: rgba(239, 68, 68, 0.15);
        color: #f87171;
        border: 1px solid rgba(239, 68, 68, 0.35);
    }
    .status-badge.status-cancelada::before {
        background-color: #f87171;
    }

    .status-badge.status-vencida {
        background: rgba(245, 158, 11, 0.15);
        color: #f59e0b;
        border: 1px solid rgba(245, 158, 11, 0.35);
    }
    .status-badge.status-vencida::before {
        background-color: #f59e0b;
        box-shadow: 0 1px 3px rgba(245, 158, 11, 0.25);
    }

    /* Nombre del Servicio y Precio */
    .cita-headline-row {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 18px;
    }

    .cita-service-name {
        font-size: 20px;
        font-weight: 800;
        color: var(--text-white);
        letter-spacing: -0.015em;
        line-height: 1.25;
        margin: 0;
    }

    .price-pill {
        display: inline-flex;
        align-items: baseline;
        gap: 3px;
        padding: 5px 12px;
        background: rgba(37, 99, 235, 0.1);
        border: 1px solid rgba(37, 99, 235, 0.25);
        border-radius: 10px;
        color: #60a5fa;
        font-weight: 800;
        font-size: 16px;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .price-pill small {
        font-size: 11px;
        font-weight: 700;
        opacity: 0.8;
    }

    .pago-cliente-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }
    .pago-cliente-badge.pendiente {
        background: rgba(245, 158, 11, 0.12);
        color: #f59e0b;
        border: 1px solid rgba(245, 158, 11, 0.3);
    }
    .pago-cliente-badge.pagado {
        background: rgba(16, 185, 129, 0.12);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    /* Schedule Highlight Box (Fecha y Horario) */
    .schedule-box {
        background: rgba(18, 29, 60, 0.5);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 12px;
        padding: 12px 14px;
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 14px;
    }

    .schedule-calendar-tile {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.2) 0%, rgba(30, 64, 175, 0.3) 100%);
        border: 1px solid rgba(37, 99, 235, 0.3);
        border-radius: 10px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .calendar-tile-month {
        font-size: 9px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #93c5fd;
        line-height: 1;
    }

    .calendar-tile-day {
        font-size: 18px;
        font-weight: 800;
        color: #ffffff;
        line-height: 1.1;
    }

    .schedule-info {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .schedule-date-text {
        font-size: 13.5px;
        font-weight: 700;
        color: var(--text-white);
    }

    .schedule-time-row {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12.5px;
        color: var(--text-muted);
    }

    .schedule-time-row svg {
        width: 14px;
        height: 14px;
        color: #60a5fa;
    }

    .duration-tag {
        font-size: 11px;
        font-weight: 700;
        background: rgba(255, 255, 255, 0.06);
        padding: 1px 6px;
        border-radius: 6px;
        color: #cbd5e1;
    }

    /* Especialista Box */
    .specialist-box {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 14px;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.04);
        border-radius: 12px;
        margin-bottom: 18px;
    }

    .specialist-avatar {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        border: 1px solid rgba(255, 255, 255, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #f1f5f9;
        font-size: 13px;
        font-weight: 800;
        flex-shrink: 0;
    }

    .specialist-meta {
        display: flex;
        flex-direction: column;
        line-height: 1.3;
    }

    .specialist-meta-title {
        font-size: 11px;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .specialist-name {
        font-size: 13.5px;
        font-weight: 700;
        color: var(--text-white);
    }

    /* Footer y Acciones */
    .cita-card-footer {
        padding-top: 14px;
        border-top: 1px solid var(--border-subtle);
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .btn-cancelar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 10px 16px;
        background: rgba(239, 68, 68, 0.1);
        color: #fca5a5;
        border: 1px solid rgba(239, 68, 68, 0.35);
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.25s var(--ease-fluid);
    }

    .btn-cancelar:hover {
        background: #dc2626;
        color: #ffffff;
        border-color: #dc2626;
        transform: translateY(-2px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.35);
    }

    .btn-cancelar svg {
        width: 16px;
        height: 16px;
        transition: transform 0.2s ease;
    }

    .btn-cancelar:hover svg {
        transform: scale(1.15);
    }

    .cancel-policy-note {
        font-size: 11.5px;
        color: var(--text-muted);
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
    }

    .cancel-policy-note svg {
        width: 13px;
        height: 13px;
        color: #94a3b8;
    }

    /* Pills de Estados Inactivos */
    .action-status-pill {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 14px;
        border-radius: 10px;
        font-size: 12.5px;
        font-weight: 700;
        text-align: center;
    }

    .action-status-pill.cancelled {
        background: rgba(239, 68, 68, 0.08);
        border: 1px solid rgba(239, 68, 68, 0.22);
        color: #fca5a5;
    }

    .action-status-pill.completed {
        background: rgba(16, 185, 129, 0.08);
        border: 1px solid rgba(16, 185, 129, 0.22);
        color: #6ee7b7;
    }

    .action-status-pill.expired,
    .action-status-pill.vencida {
        background: rgba(245, 158, 11, 0.08);
        border: 1px solid rgba(245, 158, 11, 0.25);
        color: #fcd34d;
    }

    .action-status-pill.passed {
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
        color: var(--text-muted);
    }

    .action-status-pill.restricted {
        background: rgba(245, 158, 11, 0.08);
        border: 1px solid rgba(245, 158, 11, 0.25);
        color: #fcd34d;
        font-size: 12px;
        line-height: 1.4;
    }

    .action-status-pill svg {
        width: 16px;
        height: 16px;
        flex-shrink: 0;
    }

    /* Estado Vacío */
    .placeholder-panel {
        background: linear-gradient(145deg, rgba(14, 22, 45, 0.7) 0%, rgba(9, 14, 30, 0.85) 100%);
        border: 1px solid var(--border-subtle);
        border-radius: 20px;
        padding: 64px 28px;
        text-align: center;
        min-height: 400px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.25);
        backdrop-filter: blur(12px);
    }

    .placeholder-icon-wrap {
        width: 72px;
        height: 72px;
        border-radius: 20px;
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.15) 0%, rgba(30, 64, 175, 0.25) 100%);
        color: #60a5fa;
        border: 1px solid rgba(37, 99, 235, 0.35);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 22px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.3);
    }

    .placeholder-icon-wrap svg {
        width: 36px;
        height: 36px;
    }

    .placeholder-heading {
        font-size: 22px;
        font-weight: 800;
        color: var(--text-white);
        margin-bottom: 8px;
        letter-spacing: -0.015em;
    }

    .placeholder-text {
        font-size: 14.5px;
        color: var(--text-muted);
        max-width: 490px;
        line-height: 1.6;
        margin-bottom: 26px;
    }

    @media (max-width: 1024px) {
        .kpi-summary-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .kpi-summary-grid {
            grid-template-columns: 1fr;
        }

        .citas-grid {
            grid-template-columns: 1fr;
        }

        .historial-header {
            flex-direction: column;
            align-items: stretch;
        }

        .btn-agendar-nueva {
            justify-content: center;
        }

        .filter-wrapper {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
    <!-- ENCABEZADO DE LA SECCIÓN -->
    <div class="historial-header">
        <div class="historial-header-info">
            <div class="historial-badge-top">
                Portal Cliente &bull; Gestión de Citas
            </div>
            <h1 class="section-title">Historial de citas</h1>
            <p class="section-subtitle">Consulta el estado de tus citas programadas, fechas de atención y gestiona cancelaciones con anticipación.</p>
        </div>
        <a href="{{ route('cliente.agendamiento') }}" class="btn-agendar-nueva">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Agendar nueva cita
        </a>
    </div>

    @if($citas->isEmpty())
        <!-- ESTADO VACÍO: EL CLIENTE AÚN NO TIENE CITAS REGISTRADAS -->
        <div class="placeholder-panel" id="panelSinCitas">
            <div class="placeholder-icon-wrap" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
            </div>
            <h2 class="placeholder-heading">No tienes citas registradas</h2>
            <p class="placeholder-text">
                Aún no has registrado ninguna cita en el estudio. Reserva tu servicio favorito con nuestros especialistas para empezar.
            </p>
            <a href="{{ route('cliente.agendamiento') }}" class="btn-agendar-nueva">
                Agendar mi primera cita
            </a>
        </div>
    @else
        @php
            $totalCitas = $citas->count();
            $proximasCount = $citasProximas->count();
            $completadasCount = $citas->filter(fn($c) => in_array($c->estado_visual, ['completada', 'completed']))->count();
            $vencidasCount = $citas->filter(fn($c) => $c->estado_visual === 'vencida')->count();
            $canceladasCount = $citas->filter(fn($c) => $c->estado_visual === 'cancelada')->count();
        @endphp

        <!-- TARJETAS DE RESUMEN KPI -->
        <div class="kpi-summary-grid">
            <div class="kpi-card">
                <div class="kpi-icon-wrap blue">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                </div>
                <div class="kpi-content">
                    <span class="kpi-number">{{ $totalCitas }}</span>
                    <span class="kpi-label">Total de Citas</span>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon-wrap green">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <div class="kpi-content">
                    <span class="kpi-number">{{ $proximasCount }}</span>
                    <span class="kpi-label">Próximas Activas</span>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon-wrap purple">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                </div>
                <div class="kpi-content">
                    <span class="kpi-number">{{ $completadasCount }}</span>
                    <span class="kpi-label">Completadas</span>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon-wrap amber">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>
                <div class="kpi-content">
                    <span class="kpi-number">{{ $vencidasCount }}</span>
                    <span class="kpi-label">Vencidas</span>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon-wrap red">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="15" y1="9" x2="9" y2="15"></line>
                        <line x1="9" y1="9" x2="15" y2="15"></line>
                    </svg>
                </div>
                <div class="kpi-content">
                    <span class="kpi-number">{{ $canceladasCount }}</span>
                    <span class="kpi-label">Canceladas</span>
                </div>
            </div>
        </div>

        <!-- BARRA DE FILTROS DE CITAS -->
        <div class="filter-wrapper" role="tablist" aria-label="Filtro de citas">
            <button type="button" class="filter-btn active" data-filter="todas">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7"></rect>
                    <rect x="14" y="3" width="7" height="7"></rect>
                    <rect x="14" y="14" width="7" height="7"></rect>
                    <rect x="3" y="14" width="7" height="7"></rect>
                </svg>
                Todas <span class="filter-badge-count">{{ $totalCitas }}</span>
            </button>
            <button type="button" class="filter-btn" data-filter="proximas">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                Próximas <span class="filter-badge-count">{{ $proximasCount }}</span>
            </button>
            <button type="button" class="filter-btn" data-filter="completadas">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                Completadas <span class="filter-badge-count">{{ $completadasCount }}</span>
            </button>
            <button type="button" class="filter-btn" data-filter="vencidas">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                Vencidas <span class="filter-badge-count">{{ $vencidasCount }}</span>
            </button>
            <button type="button" class="filter-btn" data-filter="canceladas">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="15" y1="9" x2="9" y2="15"></line>
                    <line x1="9" y1="9" x2="15" y2="15"></line>
                </svg>
                Canceladas <span class="filter-badge-count">{{ $canceladasCount }}</span>
            </button>
        </div>

        <!-- LISTADO DE TARJETAS DE CITAS -->
        <div class="citas-grid" id="citasGrid">
            @foreach($citas as $cita)
                @php
                    $estadoVisual = $cita->estado_visual;
                    $estadoLabel = $cita->estado_label;
                    $esProxima = $cita->esProxima();
                    $puedeCancelar = $cita->puedeCancelar();
                    $minutosRestantes = $cita->minutos_para_inicio;
                    $fechaCarbon = $cita->fecha ? \Carbon\Carbon::parse($cita->fecha) : null;
                    $trabajadorNombre = trim(($cita->trabajador->user->name ?? 'Especialista') . ' ' . ($cita->trabajador->user->apellidos ?? ''));
                    $iniciales = '';
                    if ($cita->trabajador && $cita->trabajador->user) {
                        $n = $cita->trabajador->user->name ?? '';
                        $a = $cita->trabajador->user->apellidos ?? '';
                        $iniciales = strtoupper(substr($n, 0, 1) . substr($a, 0, 1));
                    }
                    if (!$iniciales) { $iniciales = 'BB'; }

                    $cardClass = 'pasada';
                    if ($esProxima) {
                        $cardClass = 'proxima';
                    } elseif ($estadoVisual === 'cancelada') {
                        $cardClass = 'cancelada';
                    } elseif ($estadoVisual === 'vencida') {
                        $cardClass = 'vencida';
                    }
                @endphp
                <div class="cita-card {{ $cardClass }}"
                     id="citaCard-{{ $cita->id }}"
                     data-id="{{ $cita->id }}"
                     data-tipo="{{ $esProxima ? 'proxima' : 'pasada' }}"
                     data-estado="{{ $estadoVisual }}">
                    
                    <div>
                        <!-- FILA SUPERIOR: CATEGORÍA Y ESTADO -->
                        <div class="cita-card-top">
                            <span class="cita-category-badge">
                                @if(strtolower($cita->service->category ?? '') === 'barberia')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="6" cy="6" r="3"></circle>
                                        <circle cx="6" cy="18" r="3"></circle>
                                        <line x1="20" y1="4" x2="8.12" y2="15.88"></line>
                                        <line x1="14.47" y1="14.48" x2="20" y2="20"></line>
                                        <line x1="8.12" y1="8.12" x2="12" y2="12"></line>
                                    </svg>
                                @else
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                    </svg>
                                @endif
                                {{ ucfirst($cita->service->category ?? 'Estudio') }}
                            </span>

                            <span class="status-badge status-{{ $estadoVisual }}" id="statusBadge-{{ $cita->id }}">
                                {{ $estadoLabel }}
                            </span>
                        </div>

                        <!-- SERVICIO Y PRECIO -->
                        <div class="cita-headline-row">
                            <h2 class="cita-service-name">{{ $cita->service->name ?? 'Servicio' }}</h2>
                            <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 4px;">
                                <div class="price-pill">
                                    ${{ number_format($cita->precio, 2) }} <small>MXN</small>
                                </div>
                                @if($cita->estado_pago === 'pagado')
                                    <span class="pago-cliente-badge pagado" title="Servicio liquidado y pagado en el estudio">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="11" height="11">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                        ✓ Pagado
                                    </span>
                                @else
                                    <span class="pago-cliente-badge pendiente" title="Pago pendiente a liquidar presencialmente en recepción">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="11" height="11">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <line x1="12" y1="8" x2="12" y2="12"></line>
                                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                        </svg>
                                        Pago en recepción: Pendiente
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- RECUADRO DE FECHA Y HORARIO -->
                        <div class="schedule-box">
                            <div class="schedule-calendar-tile">
                                <span class="calendar-tile-month">{{ $fechaCarbon ? $fechaCarbon->translatedFormat('M') : 'CITA' }}</span>
                                <span class="calendar-tile-day">{{ $fechaCarbon ? $fechaCarbon->format('d') : '--' }}</span>
                            </div>
                            <div class="schedule-info">
                                <span class="schedule-date-text">
                                    {{ $fechaCarbon ? $fechaCarbon->translatedFormat('l, d \d\e F \d\e Y') : 'Fecha no especificada' }}
                                </span>
                                <div class="schedule-time-row">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12 6 12 12 16 14"></polyline>
                                    </svg>
                                    <span><strong>{{ $cita->hora_inicio }} - {{ $cita->hora_fin }}</strong></span>
                                    <span class="duration-tag">{{ $cita->duracion_minutos }} min</span>
                                </div>
                            </div>
                        </div>

                        <!-- ESPECIALISTA ASIGNADO -->
                        <div class="specialist-box">
                            <div class="specialist-avatar">
                                {{ $iniciales }}
                            </div>
                            <div class="specialist-meta">
                                <span class="specialist-meta-title">Especialista asignado</span>
                                <span class="specialist-name">{{ $trabajadorNombre }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- FOOTER Y ACCIONES DE CANCELACIÓN -->
                    <div class="cita-card-footer" id="cardFooter-{{ $cita->id }}">
                        @if($puedeCancelar)
                            <button type="button" 
                                    class="btn-cancelar js-btn-cancelar"
                                    data-id="{{ $cita->id }}"
                                    data-servicio="{{ $cita->service->name ?? 'Servicio' }}"
                                    data-especialista="{{ $trabajadorNombre }}"
                                    data-fecha="{{ $fechaCarbon ? $fechaCarbon->format('d/m/Y') : '' }}"
                                    data-hora="{{ $cita->hora_inicio }}"
                                    data-minutos="{{ $minutosRestantes }}">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="15" y1="9" x2="9" y2="15"></line>
                                    <line x1="9" y1="9" x2="15" y2="15"></line>
                                </svg>
                                Cancelar cita
                            </button>
                            <span class="cancel-policy-note">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                Cancelación permitida hasta 30 minutos antes del inicio.
                            </span>
                        @elseif($estadoVisual === 'cancelada')
                            <div class="action-status-pill cancelled">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="15" y1="9" x2="9" y2="15"></line>
                                    <line x1="9" y1="9" x2="15" y2="15"></line>
                                </svg>
                                Cita cancelada
                            </div>
                        @elseif($estadoVisual === 'vencida')
                            <div class="action-status-pill expired">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="8" x2="12" y2="12"></line>
                                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                </svg>
                                Cita no asistida / Vencida
                            </div>
                        @elseif(in_array($estadoVisual, ['completada', 'completed']))
                            <div class="action-status-pill completed">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                </svg>
                                Cita finalizada exitosamente
                            </div>
                        @elseif($minutosRestantes <= 0)
                            <div class="action-status-pill passed">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                Cita transcurrida
                            </div>
                        @else
                            {{-- Faltan 30 minutos o menos para una cita próxima activa --}}
                            <div class="action-status-pill restricted" title="{{ $cita->motivo_no_cancelable }}">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="8" x2="12" y2="12"></line>
                                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                </svg>
                                <div>
                                    <strong>No cancelable:</strong> Faltan {{ $minutosRestantes > 0 ? $minutosRestantes . ' min' : 'pocos minutos' }} para el inicio (límite superado).
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div id="noMatchFilter" class="placeholder-panel" style="display: none; margin-top: 20px; min-height: 240px; padding: 40px 20px;">
            <p class="placeholder-text" style="margin-bottom: 0;">
                No se encontraron citas en esta categoría seleccionada.
            </p>
        </div>
    @endif
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        // ==========================================
        // 1. FILTRADO DINÁMICO DE CITAS
        // ==========================================
        const filterBtns = document.querySelectorAll('.filter-btn');
        const citaCards = document.querySelectorAll('.cita-card');
        const noMatchFilter = document.getElementById('noMatchFilter');

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                filterBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                const filter = this.getAttribute('data-filter');
                let visibleCount = 0;

                citaCards.forEach(card => {
                    const tipo = card.getAttribute('data-tipo');
                    const estado = card.getAttribute('data-estado');

                    let show = false;
                    if (filter === 'todas') {
                        show = true;
                    } else if (filter === 'proximas') {
                        show = (tipo === 'proxima');
                    } else if (filter === 'completadas') {
                        show = (estado === 'completada' || estado === 'completed');
                    } else if (filter === 'vencidas') {
                        show = (estado === 'vencida');
                    } else if (filter === 'canceladas') {
                        show = (estado === 'cancelada');
                    }

                    if (show) {
                        card.style.display = 'flex';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                if (noMatchFilter) {
                    noMatchFilter.style.display = (visibleCount === 0) ? 'flex' : 'none';
                }
            });
        });

        // ==========================================
        // 2. CANCELACIÓN DE CITAS CON SWEETALERT2
        // ==========================================
        document.querySelectorAll('.js-btn-cancelar').forEach(btn => {
            btn.addEventListener('click', function () {
                const citaId = this.getAttribute('data-id');
                const servicio = this.getAttribute('data-servicio') || 'el servicio';
                const especialista = this.getAttribute('data-especialista') || 'el especialista';
                const fecha = this.getAttribute('data-fecha') || '';
                const hora = this.getAttribute('data-hora') || '';
                const minutos = parseInt(this.getAttribute('data-minutos'), 10);

                // Verificación en el cliente: si faltan 30 minutos o menos, impedir operación y explicar motivo
                if (minutos <= 30) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Cancelación no permitida',
                        html: `
                            <p style="font-size:14px;color:#cbd5e1;">No es posible cancelar tu cita para <strong>${servicio}</strong> porque faltan <strong>${minutos > 0 ? minutos : 0} minutos</strong> para su inicio.</p>
                            <p style="margin-top:12px;font-size:12.5px;color:#94a3b8;background:rgba(245,158,11,0.1);padding:8px 12px;border-radius:8px;border:1px solid rgba(245,158,11,0.25);">
                                La política del estudio establece que las cancelaciones solo son válidas con <strong>más de 30 minutos de anticipación</strong>.
                            </p>
                        `,
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#1e40af',
                        background: '#0c142b',
                        color: '#ffffff',
                        customClass: {
                            popup: 'swal2-dark-custom'
                        }
                    });
                    return;
                }

                // Modal de confirmación previa con diseño oscuro refinado
                Swal.fire({
                    title: '¿Deseas cancelar esta cita?',
                    html: `
                        <div style="text-align:left;margin-top:10px;">
                            <p style="margin-bottom:10px;font-size:14.5px;color:#f1f5f9;">
                                Estás a punto de cancelar tu servicio programado:
                            </p>
                            <div style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);border-radius:10px;padding:12px;margin-bottom:12px;">
                                <div style="font-weight:800;color:#60a5fa;font-size:15px;margin-bottom:4px;">${servicio}</div>
                                <div style="font-size:13px;color:#94a3b8;">Especialista: <strong style="color:#e2e8f0;">${especialista}</strong></div>
                                <div style="font-size:13px;color:#94a3b8;">Horario: <strong style="color:#e2e8f0;">${fecha} a las ${hora} hrs</strong></div>
                            </div>
                            <div style="padding:10px 12px;background:rgba(239,68,68,0.1);border-left:3px solid #dc2626;border-radius:6px;color:#fca5a5;font-size:12px;">
                                <strong>Aviso importante:</strong> Esta acción liberará el horario del especialista y no se podrá revertir.
                            </div>
                        </div>
                    `,
                    icon: 'warning',
                    iconColor: '#f87171',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#334155',
                    confirmButtonText: 'Sí, cancelar cita',
                    cancelButtonText: 'Conservar mi cita',
                    reverseButtons: true,
                    background: '#0c142b',
                    color: '#ffffff'
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Cancelando cita...',
                            text: 'Por favor espera un momento.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            },
                            background: '#0c142b',
                            color: '#ffffff'
                        });

                        try {
                            const response = await fetch(`/cliente/citas/${citaId}/cancelar`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken
                                }
                            });

                            const data = await response.json();

                            if (response.ok && data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    iconColor: '#10b981',
                                    title: '¡Cita Cancelada!',
                                    text: data.mensaje || 'Tu cita ha sido cancelada exitosamente.',
                                    confirmButtonText: 'Aceptar',
                                    confirmButtonColor: '#1e40af',
                                    background: '#0c142b',
                                    color: '#ffffff'
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    iconColor: '#f87171',
                                    title: 'No se pudo cancelar',
                                    text: data.mensaje || 'No se pudo procesar la cancelación.',
                                    confirmButtonText: 'Entendido',
                                    confirmButtonColor: '#dc2626',
                                    background: '#0c142b',
                                    color: '#ffffff'
                                });
                            }
                        } catch (error) {
                            Swal.fire({
                                icon: 'error',
                                iconColor: '#f87171',
                                title: 'Error de conexión',
                                text: 'Ocurrió un error al comunicarse con el servidor.',
                                confirmButtonText: 'Entendido',
                                confirmButtonColor: '#dc2626',
                                background: '#0c142b',
                                color: '#ffffff'
                            });
                        }
                    }
                });
            });
        });
    });
</script>
@endpush
