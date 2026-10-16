@extends('recepcion.layout')

@section('title', 'Beauty & Barber Studio - Panel de Recepción y Turnos')

@push('styles')
<style>
    /* ===================================================
       SISTEMA DE DISEÑO DEL PANEL DE RECEPCIÓN Y TURNOS
       Estética Dark Mode Premium • Coherencia con Beauty & Barber Studio
       Distribución balanceada de pantalla completa (sin espacios vacíos)
       =================================================== */

    .reception-container {
        padding: 4px 0 32px;
        max-width: 1440px;
        margin: 0 auto;
    }

    /* 1. HEADER Y BANNER DE ESTADO */
    .reception-header-card {
        background: linear-gradient(135deg, rgba(10, 17, 36, 0.95) 0%, rgba(12, 20, 43, 0.98) 100%);
        border: 1px solid var(--border-subtle);
        border-radius: 18px;
        padding: 22px 28px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
    }

    .reception-header-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, var(--barber-blue) 0%, var(--barber-red) 100%);
    }

    .header-info-box {
        max-width: 650px;
    }

    .reception-badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        background: var(--barber-blue-pale);
        color: var(--barber-blue-light);
        border: 1px solid var(--barber-blue-border);
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .pulse-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background-color: #10b981;
        display: inline-block;
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        animation: pulseGreen 2s infinite;
    }

    @keyframes pulseGreen {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }

    .reception-main-title {
        font-size: 25px;
        font-weight: 800;
        color: var(--text-white);
        letter-spacing: -0.02em;
        margin-bottom: 4px;
        line-height: 1.2;
    }

    .reception-main-subtitle {
        font-size: 13.5px;
        color: var(--text-muted);
        line-height: 1.45;
    }

    .header-quick-stats {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .stat-pill {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid var(--border-subtle);
        border-radius: 12px;
        padding: 10px 16px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .stat-icon {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .stat-icon.blue { background: rgba(0, 85, 255, 0.15); color: #60a5fa; }
    .stat-icon.green { background: rgba(16, 185, 129, 0.15); color: #34d399; }
    .stat-icon.amber { background: rgba(245, 158, 11, 0.15); color: #fbbf24; }

    .stat-value {
        font-size: 16px;
        font-weight: 800;
        color: var(--text-white);
        line-height: 1.1;
    }

    .stat-label {
        font-size: 11px;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    /* TARJETA SECCIÓN ESTÁNDAR */
    .section-card {
        background: var(--bg-card);
        border: 1px solid var(--border-subtle);
        border-radius: 16px;
        padding: 22px 24px;
        margin-bottom: 20px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.25);
        transition: border-color 0.25s ease;
        position: relative;
    }

    .section-card:hover {
        border-color: rgba(255, 255, 255, 0.12);
    }

    .section-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--border-subtle);
    }

    .section-header-title {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .step-indicator {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        background: var(--barber-blue);
        color: #ffffff;
        font-size: 13px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(0, 85, 255, 0.4);
        flex-shrink: 0;
    }

    .section-title {
        font-size: 16.5px;
        font-weight: 800;
        color: var(--text-white);
        letter-spacing: -0.01em;
        line-height: 1.2;
    }

    .section-subtitle {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 2px;
    }

    /* ===================================================
       CRITERIO 1: CAMPOS DE CAPTURA DEL CLIENTE (SUPERIOR)
       =================================================== */
    /* ===================================================
       CRITERIO 1: LOCALIZACIÓN Y CAPTURA DEL CLIENTE (SUPERIOR)
       =================================================== */
    .client-search-bar-container {
        position: relative;
        margin-bottom: 16px;
    }

    .search-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .search-icon-badge {
        position: absolute;
        left: 14px;
        width: 18px;
        height: 18px;
        color: var(--barber-blue-light);
        pointer-events: none;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .search-input-field {
        width: 100%;
        background: rgba(5, 10, 21, 0.85);
        border: 1px solid rgba(0, 85, 255, 0.35);
        border-radius: 12px;
        padding: 12px 42px 12px 44px;
        font-size: 13.5px;
        color: var(--text-white);
        outline: none;
        transition: all 0.25s var(--ease-fluid);
        box-shadow: 0 4px 15px rgba(0, 85, 255, 0.08);
        font-family: inherit;
    }

    .search-input-field:focus {
        border-color: var(--barber-blue-light);
        background: rgba(5, 10, 21, 0.98);
        box-shadow: 0 0 0 3px rgba(0, 85, 255, 0.25);
    }

    .search-input-field::placeholder {
        color: #64748b;
        font-size: 13px;
    }

    .btn-clear-search {
        position: absolute;
        right: 12px;
        background: transparent;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        padding: 5px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: color 0.15s ease;
    }

    .btn-clear-search:hover {
        color: #ffffff;
    }

    .search-spinner {
        position: absolute;
        right: 40px;
        width: 16px;
        height: 16px;
        border: 2px solid rgba(255, 255, 255, 0.15);
        border-top-color: var(--barber-blue-light);
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }

    /* Dropdown de resultados dinámicos */
    .search-results-dropdown {
        position: absolute;
        top: calc(100% + 6px);
        left: 0;
        right: 0;
        background: #0c142b;
        border: 1px solid var(--barber-blue-border);
        border-radius: 12px;
        box-shadow: 0 12px 35px rgba(0, 0, 0, 0.7);
        z-index: 80;
        max-height: 280px;
        overflow-y: auto;
    }

    .search-result-item {
        padding: 10px 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        cursor: pointer;
        border-bottom: 1px solid var(--border-subtle);
        transition: background 0.15s ease;
    }

    .search-result-item:last-child {
        border-bottom: none;
    }

    .search-result-item:hover {
        background: rgba(0, 85, 255, 0.15);
    }

    .search-result-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--barber-blue) 0%, #1e40af 100%);
        color: #ffffff;
        font-size: 13px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 2px 6px rgba(0, 85, 255, 0.3);
    }

    .search-result-info {
        flex: 1;
        min-width: 0;
    }

    .search-result-name {
        font-size: 13.5px;
        font-weight: 700;
        color: var(--text-white);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .search-result-meta {
        font-size: 11.5px;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 2px;
    }

    .search-result-phone-badge {
        color: #34d399;
        font-weight: 600;
    }

    .search-result-select-tag {
        font-size: 11px;
        font-weight: 700;
        color: #60a5fa;
        background: rgba(0, 85, 255, 0.15);
        border: 1px solid rgba(0, 85, 255, 0.35);
        padding: 4px 10px;
        border-radius: 6px;
        white-space: nowrap;
        transition: all 0.15s ease;
    }

    .search-result-item:hover .search-result-select-tag {
        background: var(--barber-blue);
        color: #ffffff;
    }

    .search-no-results {
        padding: 16px;
        text-align: center;
        color: var(--text-muted);
        font-size: 12.5px;
    }

    .client-form-divider {
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 14px 0 16px;
        color: #64748b;
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .client-form-divider::before,
    .client-form-divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: var(--border-subtle);
    }

    .client-fields-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr auto;
        gap: 14px;
        align-items: flex-end;
    }

    @media (max-width: 992px) {
        .client-fields-grid {
            grid-template-columns: 1fr;
        }
    }

    .form-group-custom {
        display: flex;
        flex-direction: column;
        gap: 6px;
        position: relative;
    }

    .form-group-custom.form-group-btn {
        justify-content: flex-end;
    }

    .form-label-custom {
        font-size: 12.5px;
        font-weight: 700;
        color: #cbd5e1;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .form-label-custom .req-star {
        color: var(--barber-red);
        font-weight: 800;
    }

    .input-wrapper-custom {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-icon-custom {
        position: absolute;
        left: 12px;
        width: 16px;
        height: 16px;
        color: var(--text-muted);
        pointer-events: none;
        transition: color 0.2s ease;
    }

    .input-field-custom {
        width: 100%;
        background: rgba(5, 10, 21, 0.7);
        border: 1px solid var(--border-strong);
        border-radius: 10px;
        padding: 10px 12px 10px 38px;
        font-size: 13.5px;
        color: var(--text-white);
        font-family: inherit;
        outline: none;
        transition: all 0.2s var(--ease-fluid);
    }

    .input-field-custom:focus {
        border-color: var(--barber-blue-light);
        background: rgba(5, 10, 21, 0.95);
        box-shadow: 0 0 0 3px rgba(0, 85, 255, 0.25);
    }

    .input-field-custom:focus + .input-icon-custom,
    .input-wrapper-custom:focus-within .input-icon-custom {
        color: var(--barber-blue-light);
    }

    .input-field-custom::placeholder {
        color: #64748b;
        font-size: 13px;
    }

    /* Botón Guardar Cliente Simplificado */
    .btn-save-client {
        height: 41px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 10px;
        color: #ffffff;
        font-weight: 800;
        font-size: 13px;
        padding: 0 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        white-space: nowrap;
        box-shadow: 0 3px 12px rgba(16, 185, 129, 0.3);
        transition: all 0.25s var(--ease-fluid);
        font-family: inherit;
    }

    .btn-save-client:hover {
        background: linear-gradient(135deg, #34d399 0%, #10b981 100%);
        transform: translateY(-1px);
        box-shadow: 0 5px 16px rgba(16, 185, 129, 0.45);
    }

    .btn-save-client:disabled {
        opacity: 0.65;
        cursor: not-allowed;
        transform: none;
    }

    /* Tarjeta de Cliente Detectado / Seleccionado */
    .client-detected-banner {
        display: none;
        align-items: center;
        justify-content: space-between;
        background: rgba(16, 185, 129, 0.08);
        border: 1px solid rgba(16, 185, 129, 0.35);
        border-radius: 12px;
        padding: 12px 16px;
        margin-top: 14px;
        font-size: 13px;
        color: #e2e8f0;
        animation: fadeIn 0.25s ease;
    }

    .client-detected-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .client-detected-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff;
        font-size: 14px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
        flex-shrink: 0;
    }

    .client-detected-info {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .client-detected-title-row {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .client-detected-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: rgba(16, 185, 129, 0.2);
        color: #34d399;
        font-size: 10.5px;
        font-weight: 800;
        padding: 2px 8px;
        border-radius: 6px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .client-detected-no-portal-badge {
        font-size: 11px;
        color: #94a3b8;
    }

    .client-detected-name-row {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .client-detected-name {
        font-size: 14.5px;
        font-weight: 800;
        color: #ffffff;
    }

    .client-detected-phone {
        font-size: 12.5px;
        color: #cbd5e1;
        font-weight: 600;
    }

    .btn-client-action-link {
        background: transparent;
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: #cbd5e1;
        font-size: 11.5px;
        font-weight: 700;
        padding: 6px 12px;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        font-family: inherit;
    }

    .btn-client-action-link:hover {
        background: rgba(239, 68, 68, 0.12);
        border-color: rgba(239, 68, 68, 0.35);
        color: #f87171;
    }

    .client-security-notice {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 11.5px;
        color: #94a3b8;
        margin-top: 12px;
        padding: 8px 12px;
        background: rgba(255, 255, 255, 0.02);
        border: 1px dashed rgba(255, 255, 255, 0.08);
        border-radius: 8px;
    }

    .client-security-notice svg {
        flex-shrink: 0;
        color: var(--barber-blue-light);
    }

    .client-security-notice strong {
        color: #e2e8f0;
    }

    /* ===================================================
       CRITERIO 2: MODALIDAD DE AGENDAMIENTO (INMEDIATAMENTE DEBAJO)
       =================================================== */
    .mode-selector-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    @media (max-width: 640px) {
        .mode-selector-grid {
            grid-template-columns: 1fr;
        }
    }

    .mode-card {
        background: rgba(5, 10, 21, 0.6);
        border: 2px solid var(--border-subtle);
        border-radius: 14px;
        padding: 16px 18px;
        cursor: pointer;
        transition: all 0.25s var(--ease-fluid);
        display: flex;
        flex-direction: column;
        gap: 8px;
        position: relative;
        user-select: none;
    }

    .mode-card:hover {
        background: rgba(10, 17, 36, 0.85);
        border-color: rgba(255, 255, 255, 0.2);
        transform: translateY(-2px);
    }

    .mode-card.active {
        background: linear-gradient(135deg, rgba(0, 85, 255, 0.12) 0%, rgba(12, 20, 43, 0.9) 100%);
        border-color: var(--barber-blue-light);
        box-shadow: 0 4px 20px rgba(0, 85, 255, 0.25);
    }

    .mode-card.active::after {
        content: '';
        position: absolute;
        top: 12px;
        right: 12px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: var(--barber-blue);
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='20 6 9 17 4 12'%3E%3C/polyline%3E%3C/svg%3E");
        background-size: 12px;
        background-position: center;
        background-repeat: no-repeat;
    }

    .mode-card-top {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .mode-icon-circle {
        width: 40px;
        height: 40px;
        border-radius: 11px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all 0.2s ease;
    }

    .mode-card[data-mode="momento"] .mode-icon-circle {
        background: rgba(0, 85, 255, 0.15);
        color: #60a5fa;
        border: 1px solid rgba(0, 85, 255, 0.3);
    }

    .mode-card[data-mode="posterior"] .mode-icon-circle {
        background: rgba(245, 158, 11, 0.15);
        color: #fbbf24;
        border: 1px solid rgba(245, 158, 11, 0.3);
    }

    .mode-card.active .mode-icon-circle {
        transform: scale(1.05);
    }

    .mode-title {
        font-size: 15px;
        font-weight: 800;
        color: var(--text-white);
        margin-bottom: 2px;
    }

    .mode-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 10.5px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 12px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .mode-card[data-mode="momento"] .mode-badge {
        background: rgba(16, 185, 129, 0.15);
        color: #34d399;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    .mode-card[data-mode="posterior"] .mode-badge {
        background: rgba(245, 158, 11, 0.15);
        color: #fbbf24;
        border: 1px solid rgba(245, 158, 11, 0.3);
    }

    .mode-desc {
        font-size: 12px;
        color: var(--text-muted);
        line-height: 1.4;
    }

    /* ===================================================
       NUEVA DISTRIBUCIÓN EQUILIBRADA EN 2 COLUMNAS (SIN HUECOS)
       Izquierda: Servicios, Resumen y Acciones (44%)
       Derecha: Turnos Inmediatos / Calendario y Horarios (56%)
       =================================================== */
    .reception-split-grid {
        display: grid;
        grid-template-columns: 1fr 1.35fr;
        gap: 20px;
        align-items: start;
    }

    @media (max-width: 1040px) {
        .reception-split-grid {
            grid-template-columns: 1fr;
        }
    }

    /* 3. SELECTOR DE SERVICIOS */
    .services-quick-bar {
        display: flex;
        gap: 8px;
        margin-bottom: 12px;
        flex-wrap: wrap;
    }

    .service-filter-btn {
        background: transparent;
        border: 1px solid var(--border-subtle);
        color: var(--text-muted);
        font-size: 11.5px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .service-filter-btn.active, .service-filter-btn:hover {
        background: var(--barber-blue-pale);
        border-color: var(--barber-blue-border);
        color: var(--barber-blue-light);
    }

    .services-chips-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 10px;
        max-height: 220px;
        overflow-y: auto;
        padding-right: 4px;
    }

    .service-chip {
        background: rgba(5, 10, 21, 0.7);
        border: 1px solid var(--border-subtle);
        border-radius: 10px;
        padding: 10px 14px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .service-chip:hover {
        border-color: rgba(255, 255, 255, 0.25);
        background: rgba(10, 17, 36, 0.9);
    }

    .service-chip.active {
        border-color: var(--barber-blue);
        background: rgba(0, 85, 255, 0.15);
        box-shadow: 0 0 0 1px var(--barber-blue);
    }

    .chip-title {
        font-size: 13.5px;
        font-weight: 700;
        color: var(--text-white);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .chip-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 12px;
    }

    .chip-price {
        font-weight: 800;
        color: #34d399;
    }

    .chip-duration {
        color: var(--text-muted);
    }

    /* 4. CRITERIO 3: ATENCIÓN AL MOMENTO (COLUMNA DERECHA COMPLETA) */
    .immediate-panel {
        display: block;
        animation: fadeIn 0.3s var(--ease-fluid);
    }

    .workers-immediate-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 16px;
    }

    .worker-immediate-card {
        background: rgba(5, 10, 21, 0.75);
        border: 1px solid var(--border-subtle);
        border-radius: 14px;
        padding: 16px 18px;
        transition: all 0.25s var(--ease-fluid);
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .worker-immediate-card:hover {
        border-color: rgba(255, 255, 255, 0.2);
        background: rgba(10, 17, 36, 0.9);
    }

    .worker-immediate-card.selected-worker {
        border-color: var(--barber-blue-light);
        background: rgba(0, 85, 255, 0.08);
        box-shadow: 0 4px 15px rgba(0, 85, 255, 0.2);
    }

    /* Estados de selección inicial y filtrado por servicio */
    .no-selection-placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 42px 24px;
        background: rgba(10, 17, 36, 0.5);
        border: 1px dashed rgba(0, 85, 255, 0.3);
        border-radius: 16px;
        margin: 6px 0 16px;
        animation: fadeIn 0.3s ease;
    }

    .no-selection-icon-circle {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: rgba(0, 85, 255, 0.12);
        color: #60a5fa;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 14px;
        border: 1px solid rgba(0, 85, 255, 0.3);
        box-shadow: 0 0 16px rgba(0, 85, 255, 0.2);
    }

    .no-selection-title {
        font-size: 15.5px;
        font-weight: 800;
        color: #f1f5f9;
        margin-bottom: 6px;
    }

    .no-selection-desc {
        font-size: 12.5px;
        color: var(--text-muted);
        max-width: 420px;
        line-height: 1.5;
        margin: 0;
    }

    .no-workers-found-box {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px 20px;
        background: rgba(245, 158, 11, 0.08);
        border: 1px solid rgba(245, 158, 11, 0.35);
        border-radius: 14px;
        color: #fbbf24;
        margin: 6px 0 16px;
        animation: fadeIn 0.3s ease;
    }

    .no-workers-found-box strong {
        display: block;
        font-size: 14px;
        margin-bottom: 3px;
        color: #fde68a;
    }

    .no-workers-found-box p {
        margin: 0;
        color: #cbd5e1;
        font-size: 12px;
        line-height: 1.4;
    }

    .worker-card-header {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .worker-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--barber-blue) 0%, #1e40af 100%);
        color: #ffffff;
        font-size: 15px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border: 2px solid rgba(255, 255, 255, 0.1);
        overflow: hidden;
    }

    .worker-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .worker-info {
        min-width: 0;
        flex: 1;
    }

    .worker-name {
        font-size: 14.5px;
        font-weight: 700;
        color: var(--text-white);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .worker-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 11px;
        font-weight: 600;
        color: #34d399;
        margin-top: 2px;
    }

    .shifts-list-label {
        font-size: 11px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 8px;
    }

    /* Grilla fluida y uniforme para turnos de Lara Croft y todos los especialistas */
    .shifts-pills-row {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(76px, 1fr));
        gap: 8px;
        max-height: 280px;
        overflow-y: auto;
        padding-right: 4px;
    }

    .shift-pill-btn {
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid var(--border-subtle);
        color: #cbd5e1;
        font-size: 12px;
        font-weight: 700;
        padding: 7px 4px;
        border-radius: 7px;
        cursor: pointer;
        text-align: center;
        transition: all 0.15s ease;
        font-family: inherit;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .shift-pill-btn:hover {
        background: var(--barber-blue-pale);
        border-color: var(--barber-blue-border);
        color: var(--barber-blue-light);
    }

    .shift-pill-btn.active {
        background: var(--barber-blue);
        border-color: var(--barber-blue);
        color: #ffffff;
        box-shadow: 0 2px 8px rgba(0, 85, 255, 0.4);
    }

    .shift-pill-btn.immediate-badge {
        border-color: rgba(16, 185, 129, 0.45);
        color: #34d399;
        background: rgba(16, 185, 129, 0.08);
    }

    /* 5. CRITERIO 4: FECHA POSTERIOR (COLUMNA DERECHA COMPLETA) */
    .posterior-panel {
        display: none;
        animation: fadeIn 0.3s var(--ease-fluid);
    }

    .calendar-schedule-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 16px;
    }

    .date-picker-box {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .quick-date-pills {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .quick-date-btn {
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid var(--border-subtle);
        color: #94a3b8;
        font-size: 11.5px;
        font-weight: 600;
        padding: 5px 10px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .quick-date-btn.active, .quick-date-btn:hover {
        background: var(--barber-blue-pale);
        border-color: var(--barber-blue-border);
        color: var(--barber-blue-light);
    }

    .future-slots-container {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .future-slots-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .slot-period-title {
        font-size: 11.5px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .future-slots-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(70px, 1fr));
        gap: 8px;
        max-height: 180px;
        overflow-y: auto;
        padding-right: 4px;
    }

    .time-slot-btn {
        background: rgba(5, 10, 21, 0.8);
        border: 1px solid var(--border-subtle);
        color: #e2e8f0;
        font-size: 12px;
        font-weight: 700;
        padding: 7px 4px;
        border-radius: 7px;
        cursor: pointer;
        text-align: center;
        transition: all 0.2s ease;
        font-family: inherit;
    }

    .time-slot-btn:hover {
        background: var(--barber-blue-pale);
        border-color: var(--barber-blue-border);
        color: var(--barber-blue-light);
    }

    .time-slot-btn.active {
        background: var(--barber-blue);
        border-color: var(--barber-blue);
        color: #ffffff;
        box-shadow: 0 2px 8px rgba(0, 85, 255, 0.4);
    }

    /* 6. CRITERIO 5: RESUMEN Y ACCIONES (COLUMNA IZQUIERDA) */
    .summary-card-inline {
        background: var(--bg-card);
        border: 1px solid var(--border-subtle);
        border-radius: 16px;
        padding: 20px 22px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.25);
        margin-bottom: 16px;
    }

    .summary-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 14px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--border-subtle);
    }

    .summary-icon-box {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        background: rgba(0, 85, 255, 0.15);
        color: #60a5fa;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .summary-title {
        font-size: 15px;
        font-weight: 800;
        color: var(--text-white);
    }

    .summary-rows {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-bottom: 16px;
    }

    .summary-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        font-size: 12.5px;
    }

    .summary-row-label {
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 6px;
        flex-shrink: 0;
    }

    .summary-row-val {
        font-weight: 700;
        color: var(--text-white);
        text-align: right;
        word-break: break-word;
    }

    .summary-tag-modalidad {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 2px 7px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
    }

    .summary-tag-modalidad.momento {
        background: rgba(16, 185, 129, 0.15);
        color: #34d399;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    .summary-tag-modalidad.posterior {
        background: rgba(245, 158, 11, 0.15);
        color: #fbbf24;
        border: 1px solid rgba(245, 158, 11, 0.3);
    }

    .summary-divider {
        height: 1px;
        background: var(--border-subtle);
        margin: 4px 0;
    }

    .summary-total-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 4px;
    }

    .total-label {
        font-size: 13.5px;
        font-weight: 800;
        color: #cbd5e1;
    }

    .total-amount {
        font-size: 19px;
        font-weight: 900;
        color: #34d399;
    }

    .actions-box {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .btn-confirm-turn {
        width: 100%;
        background: linear-gradient(135deg, var(--barber-blue) 0%, #0044cc 100%);
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: #ffffff;
        font-size: 14px;
        font-weight: 800;
        padding: 12px 18px;
        border-radius: 12px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 4px 14px rgba(0, 85, 255, 0.4);
        transition: all 0.25s var(--ease-fluid);
        font-family: inherit;
    }

    .btn-confirm-turn:hover {
        background: linear-gradient(135deg, var(--barber-blue-light) 0%, var(--barber-blue) 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 85, 255, 0.5);
    }

    .btn-reset-form {
        width: 100%;
        background: transparent;
        border: 1px solid var(--border-subtle);
        color: var(--text-muted);
        font-size: 12.5px;
        font-weight: 700;
        padding: 9px 14px;
        border-radius: 10px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.2s ease;
        font-family: inherit;
    }

    .btn-reset-form:hover {
        background: rgba(239, 68, 68, 0.1);
        border-color: rgba(239, 68, 68, 0.3);
        color: #f87171;
    }

    /* 7. MODAL DE TICKET DE TURNO */
    .ticket-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(3, 7, 18, 0.85);
        backdrop-filter: blur(8px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        padding: 20px;
        animation: fadeIn 0.2s ease-out;
    }

    .ticket-modal-card {
        background: #0a1124;
        border: 1px solid rgba(0, 85, 255, 0.3);
        border-radius: 20px;
        width: 100%;
        max-width: 440px;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.6);
        overflow: hidden;
        position: relative;
    }

    .ticket-top-bar {
        background: linear-gradient(90deg, var(--barber-blue) 0%, var(--barber-red) 100%);
        height: 6px;
    }

    .ticket-content {
        padding: 28px 24px 20px;
        text-align: center;
    }

    .ticket-badge-success {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: rgba(16, 185, 129, 0.15);
        color: #10b981;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 14px;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    .ticket-title {
        font-size: 20px;
        font-weight: 800;
        color: var(--text-white);
        margin-bottom: 4px;
    }

    .ticket-folio {
        font-size: 26px;
        font-weight: 900;
        color: #60a5fa;
        letter-spacing: 0.05em;
        margin: 10px 0;
        font-family: 'Playfair Display', serif, monospace;
    }

    .ticket-details-box {
        background: rgba(5, 10, 21, 0.7);
        border: 1px dashed rgba(255, 255, 255, 0.15);
        border-radius: 12px;
        padding: 14px 16px;
        margin: 16px 0;
        text-align: left;
        display: flex;
        flex-direction: column;
        gap: 8px;
        font-size: 13px;
    }

    .ticket-row {
        display: flex;
        justify-content: space-between;
    }

    .ticket-label {
        color: var(--text-muted);
    }

    .ticket-val {
        font-weight: 700;
        color: var(--text-white);
    }

    .ticket-actions {
        display: flex;
        margin-top: 18px;
    }

    .btn-ticket-done {
        width: 100%;
        background: var(--barber-blue);
        border: none;
        color: #ffffff;
        font-weight: 800;
        padding: 12px 18px;
        border-radius: 10px;
        cursor: pointer;
        font-size: 13.5px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.2s ease;
        box-shadow: 0 4px 14px rgba(0, 85, 255, 0.35);
    }

    .btn-ticket-done:hover {
        background: #0044cc;
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(0, 85, 255, 0.45);
    }

    /* Estilos del selector interactivo de fecha futura con formato DD/MM/AAAA */
    .special-date-input-wrap {
        position: relative;
        display: flex;
        align-items: center;
        width: 100%;
    }

    .date-masked-input {
        padding-left: 14px !important;
        padding-right: 48px !important;
        font-variant-numeric: tabular-nums;
        letter-spacing: 0.04em;
        font-size: 14px !important;
        font-weight: 600;
        cursor: text;
    }

    .native-date-hidden-picker {
        position: absolute;
        right: 6px;
        top: 50%;
        transform: translateY(-50%);
        width: 36px;
        height: 36px;
        opacity: 0.001;
        cursor: pointer;
        z-index: 4;
        border: none;
        background: transparent;
        padding: 0;
        margin: 0;
    }

    .native-date-hidden-picker::-webkit-calendar-picker-indicator {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        margin: 0;
        padding: 0;
        opacity: 0;
        cursor: pointer;
    }

    .btn-calendar-trigger-field {
        position: absolute;
        right: 6px;
        top: 50%;
        transform: translateY(-50%);
        width: 36px;
        height: 36px;
        border-radius: 9px;
        border: 1px solid rgba(0, 85, 255, 0.35);
        background: rgba(0, 85, 255, 0.12);
        color: #60a5fa;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s var(--ease-fluid);
        z-index: 2;
        pointer-events: none;
    }

    .special-date-input-wrap:hover .btn-calendar-trigger-field,
    .native-date-hidden-picker:hover + .btn-calendar-trigger-field,
    .native-date-hidden-picker:focus + .btn-calendar-trigger-field {
        background: rgba(0, 85, 255, 0.28);
        color: #93c5fd;
        border-color: rgba(0, 85, 255, 0.7);
        box-shadow: 0 0 14px rgba(0, 85, 255, 0.45);
        transform: translateY(-50%) scale(1.05);
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(4px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('content')
<div class="reception-container">

    <!-- ==============================================
         HEADER DEL PANEL DE RECEPCIÓN
         ============================================== -->
    <header class="reception-header-card" aria-labelledby="receptionTitle">
        <div class="header-info-box">
            <div class="reception-badge">
                <span class="pulse-dot"></span>
                <span>Recepción Activa &bull; Beauty & Barber Studio</span>
            </div>
            <h1 id="receptionTitle" class="reception-main-title">
                Panel de Recepción & Registro de Turnos
            </h1>
            <p class="reception-main-subtitle">
                Captura los datos del cliente y selecciona entre atención al momento (turnos inmediatos) o agendamiento para fecha posterior.
            </p>
        </div>

        <div class="header-quick-stats">
            <div class="stat-pill" title="Fecha del sistema">
                <div class="stat-icon blue">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                </div>
                <div>
                    <div class="stat-value" id="headerLiveDate">{{ $todayFormatted ?? date('d/m/Y') }}</div>
                    <div class="stat-label">Fecha de Atención</div>
                </div>
            </div>

            <div class="stat-pill" title="Especialistas activos hoy">
                <div class="stat-icon green">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <polyline points="16 11 18 13 22 9"></polyline>
                    </svg>
                </div>
                <div>
                    <div class="stat-value">{{ $stats['total_especialistas'] ?? (isset($trabajadores) ? $trabajadores->count() : 0) }}</div>
                    <div class="stat-label">Especialistas Hoy</div>
                </div>
            </div>

            <div class="stat-pill" title="Citas registradas hoy">
                <div class="stat-icon amber">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <div>
                    <div class="stat-value">{{ $stats['citas_hoy_count'] ?? (isset($citasHoy) ? $citasHoy->count() : 0) }}</div>
                    <div class="stat-label">Citas del Día</div>
                </div>
            </div>
        </div>
    </header>

    <!-- ==============================================
         CRITERIO 1: CAPTURA DE DATOS BÁSICOS DEL CLIENTE (ANCHO COMPLETO)
         (Nombre, Apellidos y Número telefónico)
         ============================================== -->
    <section class="section-card" id="clientSection" aria-label="Localización y registro de clientes">
        <div class="section-card-header">
            <div class="section-header-title">
                <span class="step-indicator" aria-hidden="true">1</span>
                <div>
                    <h2 class="section-title">Buscar o Registrar Cliente</h2>
                    <p class="section-subtitle">Localiza clientes por teléfono o nombre, o captura un nuevo perfil simplificado sin requerir cuenta ni correo.</p>
                </div>
            </div>

            <button type="button" class="btn-reset-form" id="btnQuickClearClient" style="width: auto; padding: 5px 12px; font-size: 11.5px;" title="Limpiar campos del cliente">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path>
                    <path d="M3 3v5h5"></path>
                </svg>
                <span>Limpiar campos</span>
            </button>
        </div>

        <!-- 1. BUSCADOR DINÁMICO POR NÚMERO TELEFÓNICO O NOMBRE (Criterio 1) -->
        <div class="client-search-bar-container">
            <div class="search-input-wrapper">
                <span class="search-icon-badge" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </span>
                <input 
                    type="text" 
                    id="buscadorClienteInput" 
                    class="search-input-field" 
                    placeholder="Buscar cliente registrado por número telefónico o nombre (Ej: 5512345678 o Carlos)..." 
                    autocomplete="off"
                    aria-label="Buscar cliente existente por teléfono o nombre"
                >
                <span id="searchSpinner" class="search-spinner" style="display: none;" title="Buscando clientes..."></span>
                <button type="button" id="btnClearSearch" class="btn-clear-search" style="display: none;" title="Limpiar búsqueda">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <!-- Menú desplegable con coincidencias dinámicas -->
            <div class="search-results-dropdown" id="buscadorResultadosList" style="display: none;"></div>
        </div>

        <div class="client-form-divider">
            <span>O captura los datos en el formulario simplificado</span>
        </div>

        <!-- 2. FORMULARIO SIMPLIFICADO: ÚNICAMENTE NOMBRE, APELLIDOS Y TELÉFONO (Criterios 2 y 4) -->
        <div class="client-fields-grid">
            <!-- Campo 1: Nombre -->
            <div class="form-group-custom">
                <label for="cliente_nombre" class="form-label-custom">
                    <span>Nombre(s)</span>
                    <span class="req-star">*</span>
                </label>
                <div class="input-wrapper-custom">
                    <input 
                        type="text" 
                        id="cliente_nombre" 
                        name="nombre" 
                        class="input-field-custom" 
                        placeholder="Ej. Carlos" 
                        pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+"
                        title="Solo se permiten letras y espacios, sin números ni caracteres especiales"
                        autocomplete="off"
                        required
                    >
                    <svg class="input-icon-custom" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </div>
            </div>

            <!-- Campo 2: Apellidos -->
            <div class="form-group-custom">
                <label for="cliente_apellidos" class="form-label-custom">
                    <span>Apellidos</span>
                    <span class="req-star">*</span>
                </label>
                <div class="input-wrapper-custom">
                    <input 
                        type="text" 
                        id="cliente_apellidos" 
                        name="apellidos" 
                        class="input-field-custom" 
                        placeholder="Ej. Mendoza Morales" 
                        pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+"
                        title="Solo se permiten letras y espacios, sin números ni caracteres especiales"
                        autocomplete="off"
                        required
                    >
                    <svg class="input-icon-custom" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
            </div>

            <!-- Campo 3: Número Telefónico (Exactamente 10 dígitos) -->
            <div class="form-group-custom">
                <label for="cliente_telefono" class="form-label-custom">
                    <span>Número Telefónico (10 dígitos)</span>
                    <span class="req-star">*</span>
                </label>
                <div class="input-wrapper-custom">
                    <input 
                        type="tel" 
                        id="cliente_telefono" 
                        name="telefono" 
                        class="input-field-custom" 
                        placeholder="Ej. 5512345678" 
                        maxlength="10" 
                        pattern="[0-9]{10}"
                        title="Debe contener exactamente 10 dígitos numéricos"
                        autocomplete="off"
                        required
                    >
                    <svg class="input-icon-custom" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                    </svg>
                </div>
            </div>

            <!-- Botón de Registro Rápido del Cliente -->
            <div class="form-group-custom form-group-btn">
                <label class="form-label-custom" style="visibility: hidden;">
                    <span>Acción</span>
                </label>
                <button 
                    type="button" 
                    id="btnGuardarClienteRapido" 
                    class="btn-save-client" 
                    title="Guardar cliente en el sistema y seleccionarlo automáticamente"
                >
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="8.5" cy="7" r="4"></circle>
                        <line x1="20" y1="8" x2="20" y2="14"></line>
                        <line x1="23" y1="11" x2="17" y2="11"></line>
                    </svg>
                    <span id="btnSaveClientText">Guardar Cliente</span>
                </button>
            </div>
        </div>

        <!-- 3. TARJETA DE CLIENTE DETECTADO O SELECCIONADO AUTOMÁTICAMENTE (Criterio 5) -->
        <div class="client-detected-banner" id="clientDetectedBanner" style="display: none;">
            <div class="client-detected-left">
                <div class="client-detected-avatar" id="clientDetectedAvatar">CL</div>
                <div class="client-detected-info">
                    <div class="client-detected-title-row">
                        <span class="client-detected-status-badge">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            <span id="clientDetectedBadgeLabel">Cliente Seleccionado</span>
                        </span>
                        <span class="client-detected-no-portal-badge" id="clientDetectedNoPortalBadge">&bull; Sin cuenta de usuario &bull; Sin acceso portal</span>
                    </div>
                    <div class="client-detected-name-row">
                        <strong class="client-detected-name" id="clientDetectedName">Nombre del Cliente</strong>
                        <span class="client-detected-phone" id="clientDetectedPhone">Tel: 00 0000 0000</span>
                    </div>
                </div>
            </div>
            <div class="client-detected-actions">
                <button type="button" id="btnDeselectClient" class="btn-client-action-link" title="Cambiar o desvincular este cliente">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                    <span>Cambiar / Desvincular</span>
                </button>
            </div>
        </div>

        <!-- 4. MENSAJE DE REGLA DE NEGOCIO (Criterio 3) -->
        <div class="client-security-notice">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="16" x2="12" y2="12"></line>
                <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
            <span>
                <strong>Registro Express:</strong> Únicamente se solicita Nombre, Apellidos y Teléfono. No exige correo electrónico ni genera cuenta de acceso al portal. Al guardar, queda seleccionado de forma inmediata para continuar con la asignación del turno.
            </span>
        </div>
    </section>

    <!-- ==============================================
         CRITERIO 2: MODALIDAD DE AGENDAMIENTO (INMEDIATAMENTE DEBAJO)
         Selección entre "Atención al momento" y "Fecha posterior"
         ============================================== -->
    <section class="section-card" id="modeSection" aria-label="Selección de modalidad de agendamiento">
        <div class="section-card-header">
            <div class="section-header-title">
                <span class="step-indicator" aria-hidden="true">2</span>
                <div>
                    <h2 class="section-title">Modalidad de Agendamiento</h2>
                    <p class="section-subtitle">Elige si el cliente será atendido de inmediato o si reservará para una fecha futura.</p>
                </div>
            </div>
        </div>

        <div class="mode-selector-grid" role="radiogroup" aria-label="Modalidad de atención">
            <!-- Opción A: Atención al momento -->
            <div 
                class="mode-card active" 
                data-mode="momento" 
                id="modeCardMomento" 
                role="radio" 
                aria-checked="true" 
                tabindex="0"
            >
                <div class="mode-card-top">
                    <div class="mode-icon-circle">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                        </svg>
                    </div>
                    <div>
                        <h3 class="mode-title">Atención al momento</h3>
                        <span class="mode-badge">
                            <span class="pulse-dot" style="width: 5px; height: 5px;"></span>
                            <span>Pase Directo &bull; Hoy</span>
                        </span>
                    </div>
                </div>
                <p class="mode-desc">
                    Para clientes que ya se encuentran en recepción. Despliega turnos inmediatos y especialistas disponibles hoy.
                </p>
            </div>

            <!-- Opción B: Fecha posterior -->
            <div 
                class="mode-card" 
                data-mode="posterior" 
                id="modeCardPosterior" 
                role="radio" 
                aria-checked="false" 
                tabindex="0"
            >
                <div class="mode-card-top">
                    <div class="mode-icon-circle">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                            <circle cx="12" cy="14" r="2"></circle>
                        </svg>
                    </div>
                    <div>
                        <h3 class="mode-title">Fecha posterior</h3>
                        <span class="mode-badge">
                            <span>Cita Anticipada</span>
                        </span>
                    </div>
                </div>
                <p class="mode-desc">
                    Para clientes que desean agendar una fecha futura. Habilita el calendario de citas y selector de horarios futuros.
                </p>
            </div>
        </div>
    </section>

    <!-- ==============================================
         DISTRIBUCIÓN EN 2 COLUMNAS INTEGRADA (SIN ESPACIOS VACÍOS)
         Izquierda: Servicio, Observaciones, Resumen y Acciones (44%)
         Derecha: Turnos Inmediatos / Calendario y Horarios (56%)
         ============================================== -->
    <div class="reception-split-grid">

        <!-- ==============================================
             COLUMNA IZQUIERDA: SERVICIO, OBSERVACIONES Y ACCIONES
             ============================================== -->
        <div class="reception-split-col-left">

            <!-- PASO 3: CATÁLOGO RÁPIDO DE SERVICIOS -->
            <section class="section-card" id="serviceSection" aria-label="Selección de servicio">
                <div class="section-card-header">
                    <div class="section-header-title">
                        <span class="step-indicator" aria-hidden="true">3</span>
                        <div>
                            <h2 class="section-title">Servicio Requerido</h2>
                            <p class="section-subtitle">Selecciona el servicio solicitado para calcular la duración y turnos compatibles.</p>
                        </div>
                    </div>

                    <!-- Filtros por categoría -->
                    <div class="services-quick-bar" role="tablist">
                        <button type="button" class="service-filter-btn active" data-cat="all">Todos</button>
                        <button type="button" class="service-filter-btn" data-cat="barberia">Barbería</button>
                        <button type="button" class="service-filter-btn" data-cat="estetica">Estética</button>
                    </div>
                </div>

                <div class="services-chips-grid" id="servicesGrid">
                    @forelse($servicios ?? [] as $servicio)
                        <div 
                            class="service-chip" 
                            data-service-id="{{ $servicio->id }}"
                            data-service-name="{{ $servicio->name }}"
                            data-service-price="{{ number_format((float)$servicio->price, 2) }}"
                            data-service-duration="{{ $servicio->duration_minutes }}"
                            data-service-category="{{ strtolower($servicio->category ?? '') }}"
                            tabindex="0"
                        >
                            <span class="chip-title">{{ $servicio->name }}</span>
                            <div class="chip-meta">
                                <span class="chip-price">${{ number_format((float)$servicio->price, 2) }}</span>
                                <span class="chip-duration">{{ $servicio->duration_minutes }} min</span>
                            </div>
                        </div>
                    @empty
                        <div style="color: var(--text-muted); font-size: 13px; grid-column: 1 / -1;">
                            No hay servicios configurados actualmente en el catálogo.
                        </div>
                    @endforelse
                </div>
            </section>

            <!-- CRITERIO 5: FICHA DE RESUMEN Y BOTONES DE ACCIÓN (CONFIRMAR / LIMPIAR) -->
            <section class="summary-card-inline" id="summaryCardSection" aria-label="Resumen y confirmación de turno">
                <div class="summary-header">
                    <div class="summary-icon-box">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                        </svg>
                    </div>
                    <div>
                        <h2 class="summary-title">Resumen de Atención</h2>
                        <div style="font-size: 11.5px; color: var(--text-muted);">Revisión antes de emitir turno</div>
                    </div>
                </div>

                <div class="summary-rows">
                    <!-- Cliente -->
                    <div class="summary-row">
                        <span class="summary-row-label">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            <span>Cliente:</span>
                        </span>
                        <span class="summary-row-val" id="resumenCliente">Sin cliente ingresado</span>
                    </div>

                    <!-- Teléfono -->
                    <div class="summary-row">
                        <span class="summary-row-label">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                            </svg>
                            <span>Teléfono:</span>
                        </span>
                        <span class="summary-row-val" id="resumenTelefono" style="color: #94a3b8;">-</span>
                    </div>

                    <!-- Modalidad -->
                    <div class="summary-row">
                        <span class="summary-row-label">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                            </svg>
                            <span>Modalidad:</span>
                        </span>
                        <span class="summary-row-val">
                            <span class="summary-tag-modalidad momento" id="resumenModalidadTag">
                                Atención al momento
                            </span>
                        </span>
                    </div>

                    <!-- Fecha y Turno -->
                    <div class="summary-row">
                        <span class="summary-row-label">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            <span>Horario / Turno:</span>
                        </span>
                        <span class="summary-row-val" id="resumenHorario" style="color: #60a5fa;">11:00 hrs (Hoy)</span>
                    </div>

                    <!-- Especialista -->
                    <div class="summary-row">
                        <span class="summary-row-label">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                            </svg>
                            <span>Especialista:</span>
                        </span>
                        <span class="summary-row-val" id="resumenEspecialista">-</span>
                    </div>

                    <!-- Servicio -->
                    <div class="summary-row">
                        <span class="summary-row-label">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="6" cy="6" r="3"></circle>
                                <circle cx="6" cy="18" r="3"></circle>
                                <line x1="20" y1="4" x2="8.12" y2="15.88"></line>
                                <line x1="14.47" y1="14.48" x2="20" y2="20"></line>
                                <line x1="8.12" y1="8.12" x2="12" y2="12"></line>
                            </svg>
                            <span>Servicio:</span>
                        </span>
                        <span class="summary-row-val" id="resumenServicio">-</span>
                    </div>

                    <div class="summary-divider"></div>

                    <!-- Total Estimado -->
                    <div class="summary-total-row">
                        <span class="total-label">Costo Estimado</span>
                        <span class="total-amount" id="resumenTotal">$0.00</span>
                    </div>
                </div>

                <!-- ACCIONES PRINCIPALES (CRITERIO 5: LIMPIAR / CONFIRMAR) -->
                <div class="actions-box">
                    <button type="button" class="btn-confirm-turn" id="btnConfirmTurno">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span id="btnConfirmText">Asignar Turno Inmediato</span>
                    </button>

                    <button type="button" class="btn-reset-form" id="btnResetForm" title="Reiniciar formulario y limpiar todos los campos">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path>
                            <path d="M3 3v5h5"></path>
                        </svg>
                        <span>Limpiar / Reiniciar Formulario</span>
                    </button>
                </div>
            </section>

            <!-- NOTAS ADICIONALES -->
            <section class="section-card" style="padding: 16px 20px;">
                <div class="form-group-custom">
                    <label for="cliente_notas" class="form-label-custom">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                        </svg>
                        <span>Observaciones / Preferencias (Opcional)</span>
                    </label>
                    <textarea 
                        id="cliente_notas" 
                        name="notas" 
                        class="input-field-custom" 
                        rows="2" 
                        placeholder="Ej. Prefiere tijera sobre máquina, primera visita, cliente alérgico a cierto tónico..."
                        style="padding-left: 12px; resize: none;"
                    ></textarea>
                </div>
            </section>

        </div>

        <!-- ==============================================
             COLUMNA DERECHA: TURNOS INMEDIATOS / HORARIOS FUTUROS
             (OCUPA TODO EL ESPACIO LATERAL SIN ESPACIOS VACÍOS)
             ============================================== -->
        <div class="reception-split-col-right">

            <!-- CRITERIO 3: ATENCIÓN AL MOMENTO (TURNOS Y ESPECIALISTAS DE HOY) -->
            <section class="section-card immediate-panel" id="panelAtencionMomento" aria-label="Turnos y especialistas disponibles hoy">
                <div class="section-card-header">
                    <div class="section-header-title">
                        <span class="step-indicator" style="background: #10b981;" aria-hidden="true">4</span>
                        <div>
                            <h2 class="section-title">Turnos Inmediatos & Especialistas de Hoy</h2>
                            <p class="section-subtitle">Selecciona al especialista disponible y el turno inmediato para atención directa.</p>
                        </div>
                    </div>
                    <span class="mode-badge" style="background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3);">
                        <span class="pulse-dot" style="width: 5px; height: 5px;"></span>
                        <span>Disponibilidad en vivo</span>
                    </span>
                </div>

                <!-- Placeholder al inicio: solo se muestran servicios hasta que se seleccione uno -->
                <div id="noServiceSelectedPlaceholder" class="no-selection-placeholder">
                    <div class="no-selection-icon-circle">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="6" cy="6" r="3"></circle>
                            <circle cx="6" cy="18" r="3"></circle>
                            <line x1="20" y1="4" x2="8.12" y2="15.88"></line>
                            <line x1="14.47" y1="14.48" x2="20" y2="20"></line>
                            <line x1="8.12" y1="8.12" x2="12" y2="12"></line>
                        </svg>
                    </div>
                    <div class="no-selection-content">
                        <h4 class="no-selection-title">Selecciona un servicio para comenzar</h4>
                        <p class="no-selection-desc">
                            Al inicio solo se muestran los servicios disponibles. Elige un servicio del catálogo para desplegar únicamente los especialistas capacitados y sus turnos inmediatos.
                        </p>
                    </div>
                </div>

                <!-- Mensaje cuando ningún trabajador ofrece el servicio seleccionado -->
                <div id="noWorkersForServiceMessage" class="no-workers-found-box" style="display: none;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <div>
                        <strong>Sin especialistas asignados</strong>
                        <p>No hay trabajadores activos capacitados para este servicio en este momento.</p>
                    </div>
                </div>

                <div class="workers-immediate-grid" id="workersImmediateGrid" style="display: none;">
                    @forelse($trabajadores ?? [] as $trabajador)
                        @php
                            $nombreTrabajador = $trabajador->nombre_completo ?: 'Especialista #' . $trabajador->id;
                            $fotoTrabajador = $trabajador->foto_url;
                            $inicialesTrabajador = strtoupper(substr($trabajador->nombre ?? 'E', 0, 1) . substr($trabajador->apellidos ?? 'S', 0, 1));
                            $slotsHoy = $turnosInmediatosHoy[$trabajador->id] ?? [];
                            $serviciosDelTrabajador = $trabajador->serviciosActivos ? $trabajador->serviciosActivos->pluck('id')->toArray() : [];
                        @endphp
                        <div 
                            class="worker-immediate-card" 
                            data-worker-id="{{ $trabajador->id }}"
                            data-worker-name="{{ $nombreTrabajador }}"
                            data-services="{{ implode(',', $serviciosDelTrabajador) }}"
                            style="display: none;"
                        >
                            <div class="worker-card-header">
                                <div class="worker-avatar">
                                    @if($fotoTrabajador)
                                        <img src="{{ $fotoTrabajador }}" alt="{{ $nombreTrabajador }}">
                                    @else
                                        {{ $inicialesTrabajador }}
                                    @endif
                                </div>
                                <div class="worker-info">
                                    <div class="worker-name">{{ $nombreTrabajador }}</div>
                                    <div class="worker-status-badge">
                                        @if(count($slotsHoy) > 0)
                                            <span class="pulse-dot" style="width: 5px; height: 5px;"></span>
                                            <span>Disponible hoy ({{ count($slotsHoy) }} turnos)</span>
                                        @else
                                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #ef4444; display: inline-block;"></span>
                                            <span style="color: #ef4444;">Sin turnos libres hoy</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div>
                                <div class="shifts-list-label">Turnos inmediatos:</div>
                                <div class="shifts-pills-row" id="workerImmediateSlots_{{ $trabajador->id }}">
                                    @forelse($slotsHoy as $idx => $slot)
                                        <button 
                                            type="button" 
                                            class="shift-pill-btn {{ $idx === 0 ? 'immediate-badge' : '' }}"
                                            data-slot-time="{{ $slot }}"
                                            data-worker-id="{{ $trabajador->id }}"
                                            title="Turno de las {{ $slot }} hrs"
                                        >
                                            @if($idx === 0)
                                                <span>Inmediato {{ $slot }}</span>
                                            @else
                                                <span>{{ $slot }}</span>
                                            @endif
                                        </button>
                                    @empty
                                        <div style="font-size: 11.5px; color: #f87171; grid-column: 1 / -1; padding: 6px 4px; display: flex; align-items: center; gap: 5px;">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line>
                                            </svg>
                                            <span>Horarios ocupados por citas o bloqueos para hoy</span>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @empty
                        <div style="color: var(--text-muted); font-size: 13px; grid-column: 1 / -1;">
                            No hay especialistas activos disponibles el día de hoy.
                        </div>
                    @endforelse
                </div>
            </section>

            <!-- CRITERIO 4: FECHA POSTERIOR (CALENDARIO Y HORARIOS FUTUROS) -->
            <section class="section-card posterior-panel" id="panelFechaPosterior" aria-label="Calendario y horarios futuros">
                <div class="section-card-header">
                    <div class="section-header-title">
                        <span class="step-indicator" style="background: #f59e0b;" aria-hidden="true">4</span>
                        <div>
                            <h2 class="section-title">Calendario & Horarios Futuros</h2>
                            <p class="section-subtitle">Selecciona la fecha y el horario para agendar la cita futura del cliente.</p>
                        </div>
                    </div>
                </div>

                <div class="calendar-schedule-grid">
                    <!-- Selector de Fecha y Accesos Rápidos -->
                    <div class="date-picker-box">
                        <div class="form-group-custom">
                            <label for="fechaPosteriorInput" class="form-label-custom">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                                <span>Seleccionar Fecha Futura</span>
                            </label>
                            
                            @php
                                $todayDate = now()->format('Y-m-d');
                                $tomorrowDate = now()->addDay()->format('Y-m-d');
                                $tomorrowDmy = now()->addDay()->format('d/m/Y');
                            @endphp
                            <div class="special-date-input-wrap">
                                <input 
                                    type="text" 
                                    id="fechaPosteriorInput" 
                                    name="fecha_posterior_display" 
                                    class="input-field-custom date-masked-input" 
                                    placeholder="DD/MM/AAAA" 
                                    maxlength="10" 
                                    value="{{ $tomorrowDmy }}"
                                    autocomplete="off"
                                    aria-label="Seleccionar fecha en formato DD/MM/AAAA"
                                    title="Escribe en orden DD/MM/AAAA o haz clic en el icono de calendario para abrirlo"
                                >
                                <!-- Selector nativo con calendario gráfico interactivo y restricción de fechas pasadas -->
                                <input 
                                    type="date" 
                                    id="fechaPosteriorNativePicker" 
                                    class="native-date-hidden-picker"
                                    min="{{ $todayDate }}" 
                                    value="{{ $tomorrowDate }}"
                                    tabindex="-1"
                                    aria-label="Abrir calendario emergente"
                                    title="Haz clic para abrir el calendario"
                                >
                                <!-- Botón visual para abrir el selector gráfico del calendario -->
                                <div 
                                    id="btnOpenDatePicker" 
                                    class="btn-calendar-trigger-field" 
                                    title="Abrir calendario para seleccionar fecha"
                                    aria-hidden="true"
                                >
                                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                    </svg>
                                </div>
                            </div>
                            <span class="special-date-help-text" style="font-size: 11px; color: var(--text-muted); margin-top: 4px; display: inline-flex; align-items: center; gap: 4px;">
                                <span>Formato:</span> <strong style="color: #60a5fa; letter-spacing: 0.05em;">DD/MM/AAAA</strong> <span>(día, mes y año)</span>
                            </span>
                        </div>

                        <!-- Botones rápidos de fechas comunes -->
                        <div>
                            <span class="shifts-list-label">Accesos rápidos:</span>
                            <div class="quick-date-pills">
                                <button type="button" class="quick-date-btn active" data-offset="1">Mañana</button>
                                <button type="button" class="quick-date-btn" data-offset="2">En 2 días</button>
                                <button type="button" class="quick-date-btn" data-offset="3">En 3 días</button>
                                <button type="button" class="quick-date-btn" data-offset="7">Próx. semana</button>
                            </div>
                        </div>

                        <!-- Selector de especialista para fecha futura -->
                        <div class="form-group-custom" style="margin-top: 6px;">
                            <label for="selectWorkerPosterior" class="form-label-custom">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                <span>Especialista para la Cita</span>
                            </label>
                            <select id="selectWorkerPosterior" class="input-field-custom" style="padding-left: 12px; cursor: pointer;">
                                <option value="" disabled selected>-- Selecciona un servicio primero --</option>
                                @foreach($trabajadores ?? [] as $t)
                                    @php
                                        $tServicios = $t->serviciosActivos ? $t->serviciosActivos->pluck('id')->toArray() : [];
                                    @endphp
                                    <option value="{{ $t->id }}" data-services="{{ implode(',', $tServicios) }}" style="display: none;">
                                        {{ $t->nombre_completo ?: 'Especialista #' . $t->id }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Selector de Horarios Futuros -->
                    <div class="future-slots-container">
                        <div class="future-slots-header">
                            <span class="slot-period-title">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                <span>Horarios Disponibles para la Fecha</span>
                            </span>
                            <span id="slotsLoadingSpinner" style="display: none; color: #60a5fa; font-size: 11px; font-weight: 600;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="animation: spin 1s linear infinite; display: inline-block; vertical-align: middle; margin-right: 3px;">
                                    <circle cx="12" cy="12" r="10" stroke-opacity="0.25"></circle>
                                    <path d="M12 2a10 10 0 0 1 10 10"></path>
                                </svg>
                                Verificando disponibilidad...
                            </span>
                        </div>

                        <!-- Mensaje informativo cuando ningún horario está libre -->
                        <div id="noFutureSlotsMessage" style="display: none; background: rgba(239, 68, 68, 0.08); border: 1px solid rgba(239, 68, 68, 0.25); border-radius: 8px; padding: 12px 14px; color: #fca5a5; font-size: 12px; align-items: flex-start; gap: 8px;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" style="flex-shrink: 0; margin-top: 1px;">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line>
                            </svg>
                            <div>
                                <strong style="color: #ffffff; display: block; margin-bottom: 2px;">Sin horarios disponibles</strong>
                                <span id="noFutureSlotsDetail">Todos los horarios para esta fecha están ocupados por citas confirmadas o bloqueos de agenda. Por favor elige otra fecha o especialista.</span>
                            </div>
                        </div>

                        <!-- Turno Matutino -->
                        <div id="morningSlotsWrapper">
                            <div class="shifts-list-label" style="font-size: 10px;">Turno Mañana (09:00 - 13:00)</div>
                            <div class="future-slots-grid" id="morningSlotsGrid">
                                <!-- Generado dinámicamente según disponibilidad real -->
                            </div>
                        </div>

                        <!-- Turno Vespertino -->
                        <div id="afternoonSlotsWrapper" style="margin-top: 6px;">
                            <div class="shifts-list-label" style="font-size: 10px;">Turno Tarde (13:30 - 19:30)</div>
                            <div class="future-slots-grid" id="afternoonSlotsGrid">
                                <!-- Generado dinámicamente según disponibilidad real -->
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>

    </div>

</div>

<!-- ==============================================
     MODAL DE CONFIRMACIÓN / TICKET DE TURNO EMITIDO
     ============================================== -->
<div class="ticket-modal-overlay" id="ticketModalOverlay" role="dialog" aria-modal="true" aria-labelledby="ticketTitle">
    <div class="ticket-modal-card">
        <div class="ticket-top-bar"></div>
        <div class="ticket-content">
            
            <div class="ticket-badge-success">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>

            <h3 class="ticket-title" id="ticketTitle">¡Turno Asignado con Éxito!</h3>
            <p style="font-size: 13px; color: var(--text-muted);" id="ticketSubtitle">
                El cliente ha sido registrado en la recepción.
            </p>

            <div class="ticket-folio" id="ticketFolio">TURNO-0001</div>

            <div class="ticket-details-box">
                <div class="ticket-row">
                    <span class="ticket-label">Cliente:</span>
                    <span class="ticket-val" id="ticketCliente">-</span>
                </div>
                <div class="ticket-row">
                    <span class="ticket-label">Teléfono:</span>
                    <span class="ticket-val" id="ticketTelefono">-</span>
                </div>
                <div class="ticket-row">
                    <span class="ticket-label">Modalidad:</span>
                    <span class="ticket-val" id="ticketModalidad">Atención al Momento</span>
                </div>
                <div class="ticket-row">
                    <span class="ticket-label">Especialista:</span>
                    <span class="ticket-val" id="ticketEspecialista">-</span>
                </div>
                <div class="ticket-row">
                    <span class="ticket-label">Servicio:</span>
                    <span class="ticket-val" id="ticketServicio">-</span>
                </div>
                <div class="ticket-row">
                    <span class="ticket-label">Fecha y Hora:</span>
                    <span class="ticket-val" id="ticketFechaHora" style="color: #60a5fa;">-</span>
                </div>
            </div>

            <div class="ticket-actions">
                <button type="button" class="btn-ticket-done" id="btnDoneTicket">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <span>Nuevo Registro</span>
                </button>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ===================================================
    // ESTADO REACTIVO DEL PANEL DE RECEPCIÓN
    // ===================================================
    const state = {
        clienteId: null,
        nombre: '',
        apellidos: '',
        telefono: '',
        modalidad: 'momento', // 'momento' | 'posterior'
        serviceId: null,
        serviceName: '',
        servicePrice: '0.00',
        serviceDuration: 30,
        workerId: null,
        workerName: '',
        selectedDate: '{{ $today }}',
        selectedTime: '11:00',
        notas: '',
    };

    // Cache de clientes existentes para autocompletado instantáneo
    const clientesRegistrados = @json($clientes ?? []);

    // Elementos del DOM - Búsqueda y Registro Rápido del Cliente
    const buscadorClienteInput = document.getElementById('buscadorClienteInput');
    const buscadorResultadosList = document.getElementById('buscadorResultadosList');
    const btnClearSearch = document.getElementById('btnClearSearch');
    const searchSpinner = document.getElementById('searchSpinner');

    const inputNombre = document.getElementById('cliente_nombre');
    const inputApellidos = document.getElementById('cliente_apellidos');
    const inputTelefono = document.getElementById('cliente_telefono');
    const inputNotas = document.getElementById('cliente_notas');
    const btnGuardarClienteRapido = document.getElementById('btnGuardarClienteRapido');
    const btnSaveClientText = document.getElementById('btnSaveClientText');

    const detectedBanner = document.getElementById('clientDetectedBanner');
    const clientDetectedAvatar = document.getElementById('clientDetectedAvatar');
    const clientDetectedName = document.getElementById('clientDetectedName');
    const clientDetectedPhone = document.getElementById('clientDetectedPhone');
    const clientDetectedBadgeLabel = document.getElementById('clientDetectedBadgeLabel');
    const btnDeselectClient = document.getElementById('btnDeselectClient');
    const btnQuickClearClient = document.getElementById('btnQuickClearClient');

    const modeCardMomento = document.getElementById('modeCardMomento');
    const modeCardPosterior = document.getElementById('modeCardPosterior');
    const panelAtencionMomento = document.getElementById('panelAtencionMomento');
    const panelFechaPosterior = document.getElementById('panelFechaPosterior');

    const inputFechaPosterior = document.getElementById('fechaPosteriorInput');
    const nativeDatePicker = document.getElementById('fechaPosteriorNativePicker');
    const btnOpenDatePicker = document.getElementById('btnOpenDatePicker');
    const selectWorkerPosterior = document.getElementById('selectWorkerPosterior');

    const defaultPosteriorYmd = '{{ $tomorrowDate }}';
    const defaultPosteriorDmy = '{{ $tomorrowDmy }}';

    const btnResetForm = document.getElementById('btnResetForm');
    const btnConfirmTurno = document.getElementById('btnConfirmTurno');
    const btnConfirmText = document.getElementById('btnConfirmText');

    // Elementos del Resumen
    const resumenCliente = document.getElementById('resumenCliente');
    const resumenTelefono = document.getElementById('resumenTelefono');
    const resumenModalidadTag = document.getElementById('resumenModalidadTag');
    const resumenHorario = document.getElementById('resumenHorario');
    const resumenEspecialista = document.getElementById('resumenEspecialista');
    const resumenServicio = document.getElementById('resumenServicio');
    const resumenTotal = document.getElementById('resumenTotal');

    // Elementos del Modal Ticket
    const ticketModalOverlay = document.getElementById('ticketModalOverlay');
    const ticketFolio = document.getElementById('ticketFolio');
    const ticketCliente = document.getElementById('ticketCliente');
    const ticketTelefono = document.getElementById('ticketTelefono');
    const ticketModalidad = document.getElementById('ticketModalidad');
    const ticketEspecialista = document.getElementById('ticketEspecialista');
    const ticketServicio = document.getElementById('ticketServicio');
    const ticketFechaHora = document.getElementById('ticketFechaHora');
    const btnDoneTicket = document.getElementById('btnDoneTicket');

    // ===================================================
    // 1. INICIALIZACIÓN DE VALORES POR DEFECTO
    // ===================================================
    // Al inicio SOLO deben mostrarse los servicios disponibles.
    // Los trabajadores se mantendrán ocultos hasta que se elija un servicio.
    filterWorkersByService(null);
    updateSummary();

    // Helper para escapar HTML en cadenas de texto
    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // ===================================================
    // 2. BUSCADOR DINÁMICO Y REGISTRO DE CLIENTES (Criterios 1, 2, 3, 4 y 5)
    // ===================================================

    // Función unificada para seleccionar cliente y propagar a todo el dashboard
    function selectClient(client, isNew = false, customBadge = null) {
        state.clienteId = client.id;
        state.nombre = client.name || client.nombre || '';
        state.apellidos = client.apellidos || '';
        state.telefono = (client.telefono || '').replace(/\D/g, '');

        inputNombre.value = state.nombre;
        inputApellidos.value = state.apellidos;
        inputTelefono.value = state.telefono;

        const nombreCompleto = client.nombre_completo || `${state.nombre} ${state.apellidos}`.trim();
        const iniciales = client.iniciales || ((state.nombre[0] || 'C') + (state.apellidos[0] || '')).toUpperCase();

        if (clientDetectedAvatar) clientDetectedAvatar.textContent = iniciales;
        if (clientDetectedName) clientDetectedName.textContent = nombreCompleto;
        if (clientDetectedPhone) clientDetectedPhone.textContent = `Tel: ${state.telefono}`;
        const tieneCuenta = client.tiene_cuenta || Boolean(client.email);
        if (clientDetectedBadgeLabel) {
            clientDetectedBadgeLabel.textContent = customBadge || (isNew ? 'Cliente Registrado (Sin Cuenta)' : (tieneCuenta ? 'Cliente con Cuenta' : 'Cliente Sin Cuenta'));
        }

        const badgeNoPortal = document.getElementById('clientDetectedNoPortalBadge');
        if (badgeNoPortal) {
            if (tieneCuenta && client.email) {
                badgeNoPortal.textContent = '• Cuenta de portal: ' + client.email;
                badgeNoPortal.style.color = '#60a5fa';
            } else {
                badgeNoPortal.textContent = '• Sin cuenta de usuario • Perfil guardado para agendamiento';
                badgeNoPortal.style.color = '#38bdf8';
            }
        }

        if (detectedBanner) detectedBanner.style.display = 'flex';
        if (btnSaveClientText) btnSaveClientText.textContent = 'Cliente Seleccionado ✓';

        if (buscadorResultadosList) buscadorResultadosList.style.display = 'none';
        if (buscadorClienteInput) buscadorClienteInput.value = '';
        if (btnClearSearch) btnClearSearch.style.display = 'none';

        updateSummary();

        // Feedback al usuario mediante notificación SweetAlert
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: isNew ? 'Cliente guardado sin cuenta' : 'Cliente seleccionado',
                text: `${nombreCompleto} ha quedado guardado sin cuenta de usuario y listo para continuar el agendamiento.`,
                showConfirmButton: false,
                timer: 2600,
                background: '#0c142b',
                color: '#ffffff',
                iconColor: '#34d399'
            });
        }

        // Desplazamiento suave para continuar inmediatamente con la asignación del turno (Criterio 5)
        const modeSection = document.getElementById('modeSection');
        if (modeSection) {
            modeSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    // Desvincular cliente actual
    function deselectClient() {
        state.clienteId = null;
        if (detectedBanner) detectedBanner.style.display = 'none';
        if (btnSaveClientText) btnSaveClientText.textContent = 'Guardar Cliente';
        updateSummary();
    }

    if (btnDeselectClient) {
        btnDeselectClient.addEventListener('click', deselectClient);
    }

    // Limpiar todos los campos del formulario de cliente
    if (btnQuickClearClient) {
        btnQuickClearClient.addEventListener('click', function () {
            inputNombre.value = '';
            inputApellidos.value = '';
            inputTelefono.value = '';
            if (buscadorClienteInput) buscadorClienteInput.value = '';
            if (buscadorResultadosList) buscadorResultadosList.style.display = 'none';
            if (btnClearSearch) btnClearSearch.style.display = 'none';

            state.nombre = '';
            state.apellidos = '';
            state.telefono = '';
            deselectClient();

            inputNombre.focus();
        });
    }

    // Detección de cambios manuales en el formulario simplificado
    function onClientInputsChanged() {
        state.nombre = inputNombre.value.trim();
        state.apellidos = inputApellidos.value.trim();
        state.telefono = inputTelefono.value.trim().replace(/\D/g, '');

        if (btnSaveClientText && !state.clienteId) {
            btnSaveClientText.textContent = 'Guardar Cliente';
        }

        updateSummary();
    }

    // Filtrado en tiempo real: nombres únicamente pueden incluir letras y espacios (sin números ni caracteres especiales)
    inputNombre.addEventListener('input', function () {
        const cleanVal = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]/g, '');
        if (this.value !== cleanVal) {
            this.value = cleanVal;
        }
        onClientInputsChanged();
    });

    // Filtrado en tiempo real: apellidos únicamente pueden incluir letras y espacios (sin números ni caracteres especiales)
    inputApellidos.addEventListener('input', function () {
        const cleanVal = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]/g, '');
        if (this.value !== cleanVal) {
            this.value = cleanVal;
        }
        onClientInputsChanged();
    });

    // Validación y formateo en tiempo real del número telefónico: únicamente 10 dígitos numéricos
    inputTelefono.addEventListener('input', function () {
        // Únicamente permitir dígitos numéricos y limitar estrictamente a 10 dígitos
        const cleanVal = this.value.replace(/\D/g, '').slice(0, 10);
        if (this.value !== cleanVal) {
            this.value = cleanVal;
        }

        onClientInputsChanged();

        // Si el usuario edita el teléfono de un cliente previamente vinculado, resetear clienteId
        if (state.clienteId) {
            deselectClient();
        }

        // Si tiene exactamente 10 dígitos, comprobar si ya existe en el cache local
        if (cleanVal.length === 10) {
            const matchLocal = clientesRegistrados.find(c => {
                const phone = (c.telefono || '').replace(/\D/g, '');
                return phone === cleanVal;
            });
            if (matchLocal && !state.clienteId) {
                selectClient(matchLocal, false, 'Cliente Existente Detectado');
            }
        }
    });

    // ---------------------------------------------------
    // LÓGICA DEL BUSCADOR DINÁMICO (Criterio 1)
    // ---------------------------------------------------
    let searchDebounceTimer = null;

    if (buscadorClienteInput) {
        buscadorClienteInput.addEventListener('input', function () {
            const query = this.value.trim();

            if (query.length > 0) {
                if (btnClearSearch) btnClearSearch.style.display = 'flex';
            } else {
                if (btnClearSearch) btnClearSearch.style.display = 'none';
                if (buscadorResultadosList) buscadorResultadosList.style.display = 'none';
                return;
            }

            clearTimeout(searchDebounceTimer);
            searchDebounceTimer = setTimeout(() => {
                realizarBusquedaDinamica(query);
            }, 250);
        });

        buscadorClienteInput.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                if (buscadorResultadosList) buscadorResultadosList.style.display = 'none';
            }
        });
    }

    if (btnClearSearch) {
        btnClearSearch.addEventListener('click', function () {
            buscadorClienteInput.value = '';
            btnClearSearch.style.display = 'none';
            if (buscadorResultadosList) buscadorResultadosList.style.display = 'none';
            buscadorClienteInput.focus();
        });
    }

    // Ejecuta la búsqueda dinámica combinando base local y consulta al endpoint de recepción
    async function realizarBusquedaDinamica(termino) {
        if (!termino || termino.length < 2) {
            if (buscadorResultadosList) buscadorResultadosList.style.display = 'none';
            return;
        }

        if (searchSpinner) searchSpinner.style.display = 'block';

        const cleanPhone = termino.replace(/\D/g, '');
        const lowerTerm = termino.toLowerCase();

        // 1. Filtro inmediato en el cache local
        let resultadosLocales = clientesRegistrados.filter(c => {
            const phone = (c.telefono || '').replace(/\D/g, '');
            const name = (c.name || '').toLowerCase();
            const ape = (c.apellidos || '').toLowerCase();
            return (cleanPhone && phone.includes(cleanPhone)) ||
                   name.includes(lowerTerm) ||
                   ape.includes(lowerTerm);
        });

        // 2. Consulta al servidor vía endpoint AJAX
        let resultadosRemotos = [];
        try {
            const response = await fetch(`{{ route('recepcion.clientes.buscar') }}?q=${encodeURIComponent(termino)}`, {
                headers: { 'Accept': 'application/json' }
            });
            if (response.ok) {
                const data = await response.json();
                if (data.success && Array.isArray(data.clientes)) {
                    resultadosRemotos = data.clientes;
                }
            }
        } catch (err) {
            console.warn('Error al buscar clientes en servidor:', err);
        }

        if (searchSpinner) searchSpinner.style.display = 'none';

        // 3. Fusionar y eliminar duplicados por ID
        const mapa = new Map();
        resultadosLocales.forEach(c => mapa.set(c.id, {
            id: c.id,
            name: c.name,
            apellidos: c.apellidos || '',
            nombre_completo: `${c.name} ${c.apellidos || ''}`.trim(),
            telefono: c.telefono || '',
            email: c.email || null,
            tiene_cuenta: Boolean(c.email),
            iniciales: ((c.name?.[0] || '') + (c.apellidos?.[0] || '')).toUpperCase() || 'CL'
        }));

        resultadosRemotos.forEach(c => mapa.set(c.id, c));

        const resultadosFinales = Array.from(mapa.values()).slice(0, 8);
        renderizarDropdownResultados(resultadosFinales, termino);
    }

    function renderizarDropdownResultados(lista, query) {
        if (!buscadorResultadosList) return;

        if (!lista || lista.length === 0) {
            const cleanQ = query.replace(/\D/g, '');
            buscadorResultadosList.innerHTML = `
                <div class="search-no-results">
                    <div>No se encontró ningún cliente registrado que coincida con "<strong>${escapeHtml(query)}</strong>".</div>
                    <div style="margin-top: 8px;">
                        <button type="button" id="btnCopiarAlFormulario" class="btn-client-action-link" style="margin: 0 auto; display: inline-flex;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 5v14M5 12h14"></path>
                            </svg>
                            <span>Capturar como nuevo cliente</span>
                        </button>
                    </div>
                </div>
            `;
            buscadorResultadosList.style.display = 'block';

            const btnCopiar = document.getElementById('btnCopiarAlFormulario');
            if (btnCopiar) {
                btnCopiar.addEventListener('click', function () {
                    if (cleanQ.length >= 8) {
                        inputTelefono.value = cleanQ;
                        inputNombre.value = '';
                        inputApellidos.value = '';
                        inputNombre.focus();
                    } else {
                        inputNombre.value = query;
                        inputApellidos.value = '';
                        inputTelefono.focus();
                    }
                    onClientInputsChanged();
                    buscadorResultadosList.style.display = 'none';
                });
            }
            return;
        }

        buscadorResultadosList.innerHTML = lista.map(c => `
            <div class="search-result-item" data-id="${c.id}" data-name="${escapeHtml(c.name)}" data-apellidos="${escapeHtml(c.apellidos || '')}" data-phone="${escapeHtml(c.telefono || '')}" data-email="${escapeHtml(c.email || '')}">
                <div class="search-result-avatar">${escapeHtml(c.iniciales || 'CL')}</div>
                <div class="search-result-info">
                    <div class="search-result-name">${escapeHtml(c.nombre_completo || c.name)}</div>
                    <div class="search-result-meta">
                        <span class="search-result-phone-badge">Tel: ${escapeHtml(c.telefono || 'Sin registrar')}</span>
                        <span>&bull; ${c.email ? 'Cuenta portal: ' + escapeHtml(c.email) : 'Cliente express (Sin cuenta)'}</span>
                    </div>
                </div>
                <span class="search-result-select-tag">Seleccionar</span>
            </div>
        `).join('');

        buscadorResultadosList.style.display = 'block';

        buscadorResultadosList.querySelectorAll('.search-result-item').forEach(item => {
            item.addEventListener('click', function () {
                const cliente = {
                    id: this.dataset.id,
                    name: this.dataset.name,
                    apellidos: this.dataset.apellidos,
                    nombre_completo: `${this.dataset.name} ${this.dataset.apellidos}`.trim(),
                    telefono: this.dataset.phone,
                    email: this.dataset.email || null,
                    tiene_cuenta: Boolean(this.dataset.email),
                    iniciales: ((this.dataset.name?.[0] || '') + (this.dataset.apellidos?.[0] || '')).toUpperCase()
                };
                selectClient(cliente, false);
            });
        });
    }

    // Cerrar dropdown al hacer click fuera
    document.addEventListener('click', function (e) {
        if (buscadorResultadosList && !buscadorResultadosList.contains(e.target) && e.target !== buscadorClienteInput) {
            buscadorResultadosList.style.display = 'none';
        }
    });

    // ---------------------------------------------------
    // REGISTRO RÁPIDO DE CLIENTES SIN CUENTA (Criterios 2, 3, 4 y 5)
    // ---------------------------------------------------
    if (btnGuardarClienteRapido) {
        btnGuardarClienteRapido.addEventListener('click', async function () {
            const nombre = inputNombre.value.trim();
            const apellidos = inputApellidos.value.trim();
            const cleanPhone = inputTelefono.value.trim().replace(/\D/g, '');

            const nameRegex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/;

            // Validación de Nombre
            if (!nombre || nombre.length < 2) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Nombre requerido',
                    text: 'Por favor ingresa el nombre del cliente (mínimo 2 letras).',
                    background: '#0c142b',
                    color: '#ffffff',
                    confirmButtonColor: '#0055ff'
                });
                inputNombre.focus();
                return;
            }

            if (!nameRegex.test(nombre)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Nombre inválido',
                    text: 'El nombre solo puede incluir letras y espacios, sin números ni caracteres especiales.',
                    background: '#0c142b',
                    color: '#ffffff',
                    confirmButtonColor: '#0055ff'
                });
                inputNombre.focus();
                return;
            }

            // Validación de Apellidos
            if (!apellidos || apellidos.length < 2) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Apellidos requeridos',
                    text: 'Por favor ingresa los apellidos del cliente (mínimo 2 letras).',
                    background: '#0c142b',
                    color: '#ffffff',
                    confirmButtonColor: '#0055ff'
                });
                inputApellidos.focus();
                return;
            }

            if (!nameRegex.test(apellidos)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Apellidos inválidos',
                    text: 'Los apellidos solo pueden incluir letras y espacios, sin números ni caracteres especiales.',
                    background: '#0c142b',
                    color: '#ffffff',
                    confirmButtonColor: '#0055ff'
                });
                inputApellidos.focus();
                return;
            }

            // Validación de Estructura Telefónica: exactamente 10 dígitos numéricos
            if (cleanPhone.length !== 10) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Número telefónico inválido',
                    text: 'El número telefónico debe contener exactamente 10 dígitos numéricos (Ej. 5512345678).',
                    background: '#0c142b',
                    color: '#ffffff',
                    confirmButtonColor: '#0055ff'
                });
                inputTelefono.focus();
                return;
            }

            // Estado de carga en el botón
            btnGuardarClienteRapido.disabled = true;
            btnGuardarClienteRapido.innerHTML = `
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="animation: spin 0.8s linear infinite;">
                    <circle cx="12" cy="12" r="10" stroke-opacity="0.25"></circle>
                    <path d="M12 2a10 10 0 0 1 10 10"></path>
                </svg>
                <span>Guardando...</span>
            `;

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            try {
                const response = await fetch('{{ route("recepcion.clientes.registrar-rapido") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({
                        nombre: nombre,
                        apellidos: apellidos,
                        telefono: cleanPhone,
                        _token: token
                    })
                });

                const data = await response.json();

                btnGuardarClienteRapido.disabled = false;
                btnGuardarClienteRapido.innerHTML = `
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="8.5" cy="7" r="4"></circle>
                        <line x1="20" y1="8" x2="20" y2="14"></line>
                        <line x1="23" y1="11" x2="17" y2="11"></line>
                    </svg>
                    <span id="btnSaveClientText">Guardar Cliente</span>
                `;

                if (response.status === 201 && data.success) {
                    // Cliente nuevo registrado con éxito (sin correo ni usuario)
                    // Incorporar al cache local
                    clientesRegistrados.push(data.cliente);
                    // Selección automática para continuar con el turno (Criterio 5)
                    selectClient(data.cliente, true, 'Cliente Registrado (Sin Cuenta)');
                } else if (response.status === 409 && data.duplicado) {
                    // Control de duplicidad: Ya existe este teléfono en la base de datos (Criterio 4)
                    // Se selecciona automáticamente el cliente existente (Criterio 5)
                    selectClient(data.cliente, false, 'Cliente Existente Detectado');
                    Swal.fire({
                        icon: 'info',
                        title: 'Cliente ya registrado',
                        text: data.mensaje || 'El número telefónico ya existe en el sistema. Se ha seleccionado automáticamente para continuar.',
                        background: '#0c142b',
                        color: '#ffffff',
                        confirmButtonColor: '#0055ff'
                    });
                } else {
                    // Error de validación u otro
                    Swal.fire({
                        icon: 'error',
                        title: 'No se pudo guardar el cliente',
                        text: data.mensaje || (data.errors ? Object.values(data.errors)[0]?.[0] : 'Ocurrió un error inesperado.'),
                        background: '#0c142b',
                        color: '#ffffff',
                        confirmButtonColor: '#ef4444'
                    });
                }
            } catch (err) {
                console.error('Error al registrar cliente express:', err);
                btnGuardarClienteRapido.disabled = false;
                btnGuardarClienteRapido.innerHTML = `
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="8.5" cy="7" r="4"></circle>
                        <line x1="20" y1="8" x2="20" y2="14"></line>
                        <line x1="23" y1="11" x2="17" y2="11"></line>
                    </svg>
                    <span id="btnSaveClientText">Guardar Cliente</span>
                `;

                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo conectar con el servidor para registrar al cliente.',
                    background: '#0c142b',
                    color: '#ffffff',
                    confirmButtonColor: '#ef4444'
                });
            }
        });
    }

    // ===================================================
    // 3. CONMUTADOR DE MODALIDAD (Criterio 2)
    // ===================================================
    function setModalidad(modalidad) {
        state.modalidad = modalidad;

        if (modalidad === 'momento') {
            modeCardMomento.classList.add('active');
            modeCardMomento.setAttribute('aria-checked', 'true');
            modeCardPosterior.classList.remove('active');
            modeCardPosterior.setAttribute('aria-checked', 'false');

            panelAtencionMomento.style.display = 'block';
            panelFechaPosterior.style.display = 'none';

            state.selectedDate = '{{ $today }}';
            btnConfirmText.textContent = 'Asignar Turno Inmediato';

            if (state.serviceId) {
                const visibleSelectedWorker = document.querySelector('.worker-immediate-card.selected-worker:not([style*="display: none"])');
                const firstVisibleWorker = document.querySelector('.worker-immediate-card:not([style*="display: none"])');
                const targetCard = visibleSelectedWorker || firstVisibleWorker;
                if (targetCard) {
                    selectWorkerCard(targetCard);
                } else {
                    state.workerId = null;
                    state.workerName = '';
                    state.selectedTime = '';
                }
            } else {
                state.workerId = null;
                state.workerName = '';
                state.selectedTime = '';
            }

            resumenModalidadTag.className = 'summary-tag-modalidad momento';
            resumenModalidadTag.textContent = 'Atención al momento';
        } else {
            modeCardPosterior.classList.add('active');
            modeCardPosterior.setAttribute('aria-checked', 'true');
            modeCardMomento.classList.remove('active');
            modeCardMomento.setAttribute('aria-checked', 'false');

            panelAtencionMomento.style.display = 'none';
            panelFechaPosterior.style.display = 'block';

            state.selectedDate = getPosteriorDateYmd();
            btnConfirmText.textContent = 'Confirmar Cita Programada';

            if (state.serviceId && selectWorkerPosterior) {
                const validOptions = Array.from(selectWorkerPosterior.options).filter(o => !o.disabled && o.style.display !== 'none' && o.value !== "");
                if (validOptions.length > 0) {
                    if (!validOptions.some(o => o.value === String(state.workerId))) {
                        selectWorkerPosterior.value = validOptions[0].value;
                    }
                    state.workerId = selectWorkerPosterior.value;
                    state.workerName = selectWorkerPosterior.options[selectWorkerPosterior.selectedIndex]?.text?.trim() || '';
                } else {
                    state.workerId = null;
                    state.workerName = '';
                }
            } else {
                state.workerId = null;
                state.workerName = '';
            }

            const activeSlot = document.querySelector('.time-slot-btn.active');
            if (activeSlot) {
                state.selectedTime = activeSlot.dataset.time;
            }

            resumenModalidadTag.className = 'summary-tag-modalidad posterior';
            resumenModalidadTag.textContent = 'Fecha posterior';

            fetchPosteriorAvailableSlots();
        }

        updateSummary();
    }

    modeCardMomento.addEventListener('click', () => setModalidad('momento'));
    modeCardPosterior.addEventListener('click', () => setModalidad('posterior'));

    modeCardMomento.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); setModalidad('momento'); }
    });
    modeCardPosterior.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); setModalidad('posterior'); }
    });

    // ===================================================
    // 4. SELECCIÓN DE SERVICIO Y FILTRADO DE TRABAJADORES
    // ===================================================
    function selectServiceChip(chip) {
        document.querySelectorAll('.service-chip').forEach(c => c.classList.remove('active'));
        chip.classList.add('active');

        state.serviceId = chip.dataset.serviceId;
        state.serviceName = chip.dataset.serviceName;
        state.servicePrice = chip.dataset.servicePrice;
        state.serviceDuration = chip.dataset.serviceDuration;

        // Desplegar únicamente las opciones de los trabajadores que ofrecen este servicio
        filterWorkersByService(state.serviceId);
        updateSummary();
    }

    // Filtra los trabajadores según el servicio seleccionado.
    // Si serviceId es null/vacío, oculta los trabajadores y muestra el placeholder inicial.
    function filterWorkersByService(serviceId) {
        const noServicePlaceholder = document.getElementById('noServiceSelectedPlaceholder');
        const noWorkersMsg = document.getElementById('noWorkersForServiceMessage');
        const workersGrid = document.getElementById('workersImmediateGrid');
        const workerCards = document.querySelectorAll('.worker-immediate-card');
        const selectPosterior = document.getElementById('selectWorkerPosterior');

        if (!serviceId) {
            // Estado inicial: solo servicios visibles, especialistas ocultos
            if (noServicePlaceholder) noServicePlaceholder.style.display = 'flex';
            if (noWorkersMsg) noWorkersMsg.style.display = 'none';
            if (workersGrid) workersGrid.style.display = 'none';

            workerCards.forEach(card => {
                card.style.display = 'none';
                card.classList.remove('selected-worker');
                card.querySelectorAll('.shift-pill-btn').forEach(b => b.classList.remove('active'));
            });

            if (selectPosterior) {
                Array.from(selectPosterior.options).forEach(opt => {
                    if (opt.value === "") {
                        opt.style.display = '';
                        opt.disabled = false;
                        opt.selected = true;
                    } else {
                        opt.style.display = 'none';
                        opt.disabled = true;
                    }
                });
            }

            state.workerId = null;
            state.workerName = '';
            state.selectedTime = '';
            updateSummary();
            return;
        }

        // Si se seleccionó un servicio:
        if (noServicePlaceholder) noServicePlaceholder.style.display = 'none';
        if (workersGrid) workersGrid.style.display = 'grid';

        const sIdStr = String(serviceId);
        let visibleCount = 0;
        let firstMatchingCard = null;
        let currentWorkerStillValid = false;

        // 1. Filtrar tarjetas de especialistas en "Atención al momento"
        workerCards.forEach(card => {
            const rawServices = card.dataset.services || '';
            const servicesList = rawServices.split(',').map(s => s.trim()).filter(Boolean);
            const offersService = servicesList.includes(sIdStr);

            if (offersService) {
                card.style.display = 'flex';
                visibleCount++;
                if (!firstMatchingCard) firstMatchingCard = card;
                if (String(card.dataset.workerId) === String(state.workerId)) {
                    currentWorkerStillValid = true;
                }
            } else {
                card.style.display = 'none';
                card.classList.remove('selected-worker');
                card.querySelectorAll('.shift-pill-btn').forEach(b => b.classList.remove('active'));
            }
        });

        // 2. Filtrar opciones de especialistas en "Fecha posterior"
        let firstMatchingOption = null;
        let currentOptionStillValid = false;
        if (selectPosterior) {
            Array.from(selectPosterior.options).forEach(opt => {
                if (opt.value === "") {
                    opt.style.display = 'none';
                    opt.disabled = true;
                    return;
                }
                const rawServices = opt.dataset.services || '';
                const servicesList = rawServices.split(',').map(s => s.trim()).filter(Boolean);
                const offersService = servicesList.includes(sIdStr);

                if (offersService) {
                    opt.style.display = '';
                    opt.disabled = false;
                    if (!firstMatchingOption) firstMatchingOption = opt;
                    if (String(opt.value) === String(state.workerId)) {
                        currentOptionStillValid = true;
                    }
                } else {
                    opt.style.display = 'none';
                    opt.disabled = true;
                }
            });
        }

        // 3. Resolver selección de especialista
        if (visibleCount > 0) {
            if (noWorkersMsg) noWorkersMsg.style.display = 'none';

            if (state.modalidad === 'momento') {
                if (currentWorkerStillValid) {
                    const currentCard = Array.from(workerCards).find(c => String(c.dataset.workerId) === String(state.workerId) && c.style.display !== 'none');
                    if (currentCard) {
                        selectWorkerCard(currentCard);
                    }
                } else if (firstMatchingCard) {
                    selectWorkerCard(firstMatchingCard);
                }
            } else {
                // Modalidad posterior
                if (selectPosterior) {
                    if (currentOptionStillValid) {
                        selectPosterior.value = state.workerId;
                    } else if (firstMatchingOption) {
                        selectPosterior.value = firstMatchingOption.value;
                        state.workerId = firstMatchingOption.value;
                        state.workerName = firstMatchingOption.text.trim();
                    }
                }
                fetchPosteriorAvailableSlots();
            }
        } else {
            // Ningún especialista asignado a este servicio
            if (noWorkersMsg) noWorkersMsg.style.display = 'flex';
            if (workersGrid) workersGrid.style.display = 'none';
            state.workerId = null;
            state.workerName = '';
            state.selectedTime = '';
            if (selectPosterior) {
                selectPosterior.value = "";
            }
            if (state.modalidad === 'posterior') {
                fetchPosteriorAvailableSlots();
            }
        }

        updateSummary();
    }

    document.querySelectorAll('.service-chip').forEach(chip => {
        chip.addEventListener('click', function () {
            selectServiceChip(this);
        });
        chip.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                selectServiceChip(this);
            }
        });
    });

    document.querySelectorAll('.service-filter-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.service-filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const cat = this.dataset.cat;
            document.querySelectorAll('.service-chip').forEach(chip => {
                if (cat === 'all' || chip.dataset.serviceCategory === cat) {
                    chip.style.display = 'flex';
                } else {
                    chip.style.display = 'none';
                }
            });
        });
    });

    // ===================================================
    // 5. ATENCIÓN AL MOMENTO: TURNOS Y ESPECIALISTAS (Criterio 3)
    // ===================================================
    document.querySelectorAll('.worker-immediate-card').forEach(card => {
        card.addEventListener('click', function (e) {
            if (e.target.closest('.shift-pill-btn')) return;
            selectWorkerCard(this);
        });
    });

    function selectWorkerCard(card) {
        document.querySelectorAll('.worker-immediate-card').forEach(c => c.classList.remove('selected-worker'));
        card.classList.add('selected-worker');

        state.workerId = card.dataset.workerId;
        state.workerName = card.dataset.workerName;

        const firstActiveShift = card.querySelector('.shift-pill-btn.active') || card.querySelector('.shift-pill-btn');
        if (firstActiveShift) {
            document.querySelectorAll('.shift-pill-btn').forEach(s => s.classList.remove('active'));
            firstActiveShift.classList.add('active');
            state.selectedTime = firstActiveShift.dataset.slotTime;
        } else {
            // El especialista tiene todos sus horarios ocupados por citas o bloqueos hoy
            document.querySelectorAll('.shift-pill-btn').forEach(s => s.classList.remove('active'));
            state.selectedTime = '';
        }

        updateSummary();
    }

    document.querySelectorAll('.shift-pill-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            const parentCard = this.closest('.worker-immediate-card');
            if (parentCard) {
                document.querySelectorAll('.worker-immediate-card').forEach(c => c.classList.remove('selected-worker'));
                parentCard.classList.add('selected-worker');
                state.workerId = parentCard.dataset.workerId;
                state.workerName = parentCard.dataset.workerName;
            }

            document.querySelectorAll('.shift-pill-btn').forEach(s => s.classList.remove('active'));
            this.classList.add('active');
            state.selectedTime = this.dataset.slotTime;

            updateSummary();
        });
    });

    // ===================================================
    // 6. FECHA POSTERIOR: CALENDARIO INTERACTIVO Y FORMATO DD/MM/AAAA (Criterio 4)
    // ===================================================

    // Convierte fecha YYYY-MM-DD a DD/MM/AAAA
    function formatYmdToDmy(ymd) {
        if (!ymd || typeof ymd !== 'string' || !ymd.includes('-')) return ymd || '';
        const parts = ymd.split('-');
        if (parts.length !== 3) return ymd;
        return `${parts[2]}/${parts[1]}/${parts[0]}`;
    }

    // Convierte fecha DD/MM/AAAA a YYYY-MM-DD
    function formatDmyToYmd(dmy) {
        if (!dmy || typeof dmy !== 'string' || !dmy.includes('/')) return dmy || '';
        const parts = dmy.split('/');
        if (parts.length !== 3) return dmy;
        return `${parts[2]}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}`;
    }

    // Obtiene la fecha posterior en formato YYYY-MM-DD para la cita
    function getPosteriorDateYmd() {
        if (nativeDatePicker && nativeDatePicker.value) {
            return nativeDatePicker.value;
        }
        if (inputFechaPosterior && inputFechaPosterior.value.includes('/')) {
            return formatDmyToYmd(inputFechaPosterior.value);
        }
        return defaultPosteriorYmd;
    }

    // Abre el calendario gráfico emergente del navegador
    function openFutureDatePicker() {
        if (!nativeDatePicker) return;
        try {
            if (typeof nativeDatePicker.showPicker === 'function') {
                nativeDatePicker.showPicker();
            } else {
                nativeDatePicker.focus();
                nativeDatePicker.click();
            }
        } catch (err) {
            console.warn('showPicker:', err);
            try {
                nativeDatePicker.focus();
                nativeDatePicker.click();
            } catch (e) {}
        }
    }

    window.openFutureDatePicker = openFutureDatePicker;

    // Restablece la fecha posterior al valor por defecto (mañana)
    function resetToDefaultPosteriorDate() {
        if (inputFechaPosterior) inputFechaPosterior.value = defaultPosteriorDmy;
        if (nativeDatePicker) nativeDatePicker.value = defaultPosteriorYmd;
        state.selectedDate = defaultPosteriorYmd;
        syncQuickDateButtons(defaultPosteriorYmd);
        updateSummary();
    }

    // Sincroniza la activación visual de los botones de acceso rápido
    function syncQuickDateButtons(ymd) {
        document.querySelectorAll('.quick-date-btn').forEach(b => {
            const offset = parseInt(b.dataset.offset || '1', 10);
            const d = new Date();
            d.setDate(d.getDate() + offset);
            const btnYmd = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
            b.classList.toggle('active', btnYmd === ymd);
        });
    }

    // Valida y sincroniza la fecha posterior ingresada manualmente
    function validateAndSyncPosteriorDate(input) {
        const val = input.value.trim();
        if (!val) {
            resetToDefaultPosteriorDate();
            return false;
        }

        // Si incompleto (menos de 10 caracteres DD/MM/AAAA)
        if (val.length < 10) {
            Swal.fire({
                icon: 'warning',
                title: 'Fecha incompleta',
                text: 'Por favor ingresa la fecha completa en formato DD/MM/AAAA (día, mes y año).',
                background: '#0c142b',
                color: '#ffffff',
                confirmButtonColor: '#0055ff'
            });
            resetToDefaultPosteriorDate();
            return false;
        }

        const regex = /^(\d{2})\/(\d{2})\/(\d{4})$/;
        const match = val.match(regex);
        if (!match) {
            Swal.fire({
                icon: 'warning',
                title: 'Formato incorrecto',
                text: 'El formato de fecha debe comenzar por día, mes y año: DD/MM/AAAA.',
                background: '#0c142b',
                color: '#ffffff',
                confirmButtonColor: '#0055ff'
            });
            resetToDefaultPosteriorDate();
            return false;
        }

        const day = parseInt(match[1], 10);
        const month = parseInt(match[2], 10);
        const year = parseInt(match[3], 10);

        // Validar día entre 1 y 31
        if (day < 1 || day > 31) {
            Swal.fire({
                icon: 'warning',
                title: 'Día inválido',
                text: 'El día ingresado debe estar entre 01 y 31.',
                background: '#0c142b',
                color: '#ffffff',
                confirmButtonColor: '#0055ff'
            });
            resetToDefaultPosteriorDate();
            return false;
        }

        // Validar mes entre 1 y 12
        if (month < 1 || month > 12) {
            Swal.fire({
                icon: 'warning',
                title: 'Mes inválido',
                text: 'El mes ingresado debe estar entre 01 y 12.',
                background: '#0c142b',
                color: '#ffffff',
                confirmButtonColor: '#0055ff'
            });
            resetToDefaultPosteriorDate();
            return false;
        }

        // Validar existencia real de la fecha en el calendario
        const testDate = new Date(year, month - 1, day);
        if (testDate.getFullYear() !== year || (testDate.getMonth() + 1) !== month || testDate.getDate() !== day) {
            Swal.fire({
                icon: 'warning',
                title: 'Fecha inexistente',
                text: 'La fecha ingresada no existe en el calendario. Verifica los días del mes.',
                background: '#0c142b',
                color: '#ffffff',
                confirmButtonColor: '#0055ff'
            });
            resetToDefaultPosteriorDate();
            return false;
        }

        // Validar que no sea fecha pasada
        const now = new Date();
        const todayStart = new Date(now.getFullYear(), now.getMonth(), now.getDate()).getTime();
        if (testDate.getTime() < todayStart) {
            Swal.fire({
                icon: 'warning',
                title: 'Fecha pasada no permitida',
                text: 'No es posible agendar citas en fechas anteriores al día de hoy.',
                background: '#0c142b',
                color: '#ffffff',
                confirmButtonColor: '#0055ff'
            });
            resetToDefaultPosteriorDate();
            return false;
        }

        // Sincronizar fecha válida
        const ymd = `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        if (nativeDatePicker) {
            nativeDatePicker.value = ymd;
        }
        state.selectedDate = ymd;
        syncQuickDateButtons(ymd);
        updateSummary();
        return true;
    }

    if (btnOpenDatePicker) {
        btnOpenDatePicker.addEventListener('click', openFutureDatePicker);
    }

    if (inputFechaPosterior) {
        // Formateo dinámico con máscara DD/MM/AAAA (empieza estrictamente por día, mes y año)
        inputFechaPosterior.addEventListener('input', function () {
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

            // Cuando completa los 10 caracteres DD/MM/AAAA, validar automáticamente
            if (formatted.length === 10) {
                validateAndSyncPosteriorDate(this);
            }
        });

        inputFechaPosterior.addEventListener('blur', function () {
            if (this.value.trim().length > 0) {
                validateAndSyncPosteriorDate(this);
            }
        });

        // Doble clic o clic en la zona derecha del campo abre el selector de calendario
        inputFechaPosterior.addEventListener('dblclick', openFutureDatePicker);
        inputFechaPosterior.addEventListener('click', function (e) {
            const rect = this.getBoundingClientRect();
            if (e.clientX > rect.right - 55) {
                openFutureDatePicker();
            }
        });
    }

    if (nativeDatePicker) {
        // Asegurar que el atributo min sea dinámico al día actual
        const todayIso = new Date().toISOString().split('T')[0];
        nativeDatePicker.min = todayIso;

        const handleNativeDateChange = function () {
            const ymd = this.value;
            if (!ymd) return;

            const parts = ymd.split('-');
            if (parts.length === 3) {
                const year = parseInt(parts[0], 10);
                const month = parseInt(parts[1], 10);
                const day = parseInt(parts[2], 10);

                const now = new Date();
                const todayStart = new Date(now.getFullYear(), now.getMonth(), now.getDate()).getTime();
                const testDate = new Date(year, month - 1, day);

                if (testDate.getTime() < todayStart) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Fecha pasada no permitida',
                        text: 'No es posible seleccionar fechas anteriores al día de hoy.',
                        background: '#0c142b',
                        color: '#ffffff',
                        confirmButtonColor: '#0055ff'
                    });
                    resetToDefaultPosteriorDate();
                    return;
                }

                const dmy = `${String(day).padStart(2, '0')}/${String(month).padStart(2, '0')}/${year}`;
                if (inputFechaPosterior) {
                    inputFechaPosterior.value = dmy;
                }
                state.selectedDate = ymd;
                syncQuickDateButtons(ymd);
                fetchPosteriorAvailableSlots();
                updateSummary();
            }
        };

        nativeDatePicker.addEventListener('change', handleNativeDateChange);
        nativeDatePicker.addEventListener('input', handleNativeDateChange);
    }

    document.querySelectorAll('.quick-date-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.quick-date-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const offset = parseInt(this.dataset.offset || '1', 10);
            const date = new Date();
            date.setDate(date.getDate() + offset);

            const yyyy = date.getFullYear();
            const mm = String(date.getMonth() + 1).padStart(2, '0');
            const dd = String(date.getDate()).padStart(2, '0');
            const dateStr = `${yyyy}-${mm}-${dd}`;
            const dmyStr = `${dd}/${mm}/${yyyy}`;

            inputFechaPosterior.value = dmyStr;
            if (nativeDatePicker) nativeDatePicker.value = dateStr;
            state.selectedDate = dateStr;
            fetchPosteriorAvailableSlots();
            updateSummary();
        });
    });

    if (selectWorkerPosterior) {
        selectWorkerPosterior.addEventListener('change', function () {
            state.workerId = this.value;
            state.workerName = this.options[this.selectedIndex]?.text || '';
            fetchPosteriorAvailableSlots();
            updateSummary();
        });
    }

    // ===================================================
    // CONSULTA DINÁMICA DE HORARIOS LIBRES (DESCARTANDO CITAS Y BLOQUEOS)
    // ===================================================
    let slotsAbortController = null;

    function fetchPosteriorAvailableSlots() {
        if (state.modalidad !== 'posterior') return;

        const morningGrid = document.getElementById('morningSlotsGrid');
        const afternoonGrid = document.getElementById('afternoonSlotsGrid');
        const morningWrapper = document.getElementById('morningSlotsWrapper');
        const afternoonWrapper = document.getElementById('afternoonSlotsWrapper');
        const noFutureSlotsMsg = document.getElementById('noFutureSlotsMessage');
        const noFutureSlotsDetail = document.getElementById('noFutureSlotsDetail');
        const spinner = document.getElementById('slotsLoadingSpinner');

        if (!state.serviceId || !state.workerId || !state.selectedDate) {
            if (morningGrid) morningGrid.innerHTML = '';
            if (afternoonGrid) afternoonGrid.innerHTML = '';
            if (noFutureSlotsMsg) {
                noFutureSlotsMsg.style.display = 'flex';
                if (noFutureSlotsDetail) {
                    noFutureSlotsDetail.textContent = 'Selecciona un servicio y un especialista para consultar los horarios disponibles.';
                }
            }
            if (morningWrapper) morningWrapper.style.display = 'none';
            if (afternoonWrapper) afternoonWrapper.style.display = 'none';
            state.selectedTime = '';
            updateSummary();
            return;
        }

        if (slotsAbortController) {
            slotsAbortController.abort();
        }
        slotsAbortController = new AbortController();

        if (spinner) spinner.style.display = 'inline-flex';
        if (noFutureSlotsMsg) noFutureSlotsMsg.style.display = 'none';

        const url = `{{ route('recepcion.agendamiento.disponibilidad') }}?service_id=${encodeURIComponent(state.serviceId)}&worker_id=${encodeURIComponent(state.workerId)}&date=${encodeURIComponent(state.selectedDate)}`;

        fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            signal: slotsAbortController.signal
        })
        .then(res => res.json())
        .then(data => {
            if (spinner) spinner.style.display = 'none';

            const horariosLibres = Array.isArray(data.horarios) ? data.horarios : [];
            const hayDisponibles = Boolean(data.success && horariosLibres.length > 0);

            if (!hayDisponibles) {
                if (morningGrid) morningGrid.innerHTML = '';
                if (afternoonGrid) afternoonGrid.innerHTML = '';
                if (morningWrapper) morningWrapper.style.display = 'none';
                if (afternoonWrapper) afternoonWrapper.style.display = 'none';
                if (noFutureSlotsMsg) {
                    noFutureSlotsMsg.style.display = 'flex';
                    if (noFutureSlotsDetail) {
                        noFutureSlotsDetail.textContent = data.mensaje || 'Todos los horarios para esta fecha están ocupados por citas confirmadas o bloqueos de agenda. Por favor elige otra fecha o especialista.';
                    }
                }
                state.selectedTime = '';
                updateSummary();
                return;
            }

            // Ocultar mensaje de vacío
            if (noFutureSlotsMsg) noFutureSlotsMsg.style.display = 'none';

            // Clasificar horarios en Mañana (< 13:00) y Tarde (>= 13:00)
            const morningSlots = [];
            const afternoonSlots = [];

            horariosLibres.forEach(timeStr => {
                const parts = timeStr.split(':');
                const hour = parseInt(parts[0], 10);
                if (hour < 13) {
                    morningSlots.push(timeStr);
                } else {
                    afternoonSlots.push(timeStr);
                }
            });

            // Render turno mañana
            if (morningGrid) {
                morningGrid.innerHTML = '';
                if (morningSlots.length > 0) {
                    if (morningWrapper) morningWrapper.style.display = 'block';
                    morningSlots.forEach(slot => {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'time-slot-btn';
                        btn.dataset.time = slot;
                        btn.textContent = slot;
                        btn.addEventListener('click', function () {
                            document.querySelectorAll('.time-slot-btn').forEach(b => b.classList.remove('active'));
                            this.classList.add('active');
                            state.selectedTime = this.dataset.time;
                            updateSummary();
                        });
                        morningGrid.appendChild(btn);
                    });
                } else {
                    if (morningWrapper) morningWrapper.style.display = 'none';
                }
            }

            // Render turno tarde
            if (afternoonGrid) {
                afternoonGrid.innerHTML = '';
                if (afternoonSlots.length > 0) {
                    if (afternoonWrapper) afternoonWrapper.style.display = 'block';
                    afternoonSlots.forEach(slot => {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'time-slot-btn';
                        btn.dataset.time = slot;
                        btn.textContent = slot;
                        btn.addEventListener('click', function () {
                            document.querySelectorAll('.time-slot-btn').forEach(b => b.classList.remove('active'));
                            this.classList.add('active');
                            state.selectedTime = this.dataset.time;
                            updateSummary();
                        });
                        afternoonGrid.appendChild(btn);
                    });
                } else {
                    if (afternoonWrapper) afternoonWrapper.style.display = 'none';
                }
            }

            // Mantener selección anterior o activar el primer horario libre disponible
            const allSlotButtons = document.querySelectorAll('.time-slot-btn');
            let matchingBtn = null;
            if (state.selectedTime) {
                matchingBtn = Array.from(allSlotButtons).find(b => b.dataset.time === state.selectedTime);
            }

            if (matchingBtn) {
                matchingBtn.classList.add('active');
            } else if (allSlotButtons.length > 0) {
                allSlotButtons[0].classList.add('active');
                state.selectedTime = allSlotButtons[0].dataset.time;
            } else {
                state.selectedTime = '';
            }

            updateSummary();
        })
        .catch(err => {
            if (err.name === 'AbortError') return;
            console.error('Error al consultar horarios disponibles:', err);
            if (spinner) spinner.style.display = 'none';
        });
    }

    // ===================================================
    // 7. ACTUALIZACIÓN DEL RESUMEN
    // ===================================================
    function updateSummary() {
        const nombreCompleto = `${state.nombre} ${state.apellidos}`.trim();
        resumenCliente.textContent = nombreCompleto || 'Sin cliente ingresado';
        resumenTelefono.textContent = state.telefono || '-';

        if (state.modalidad === 'momento') {
            resumenHorario.textContent = state.selectedTime ? `${state.selectedTime} hrs (Hoy)` : 'Pendiente de selección';
        } else {
            const fechaDmy = formatYmdToDmy(state.selectedDate) || state.selectedDate;
            resumenHorario.innerHTML = state.selectedTime 
                ? `${fechaDmy} <span style="color:#60a5fa;">(${state.selectedTime} hrs)</span>`
                : `${fechaDmy} <span style="color:#f87171;">(Sin turno disponible)</span>`;
        }

        resumenEspecialista.textContent = state.workerName || 'No seleccionado';
        resumenServicio.textContent = state.serviceName ? `${state.serviceName} (${state.serviceDuration} min)` : 'No seleccionado';
        resumenTotal.textContent = `$${state.servicePrice || '0.00'}`;
    }

    // ===================================================
    // 8. CRITERIO 5: LIMPIAR / REINICIAR FORMULARIO
    // ===================================================
    function resetEntireForm() {
        inputNombre.value = '';
        inputApellidos.value = '';
        inputTelefono.value = '';
        if (inputNotas) inputNotas.value = '';

        if (buscadorClienteInput) buscadorClienteInput.value = '';
        if (buscadorResultadosList) buscadorResultadosList.style.display = 'none';
        if (btnClearSearch) btnClearSearch.style.display = 'none';
        if (btnSaveClientText) btnSaveClientText.textContent = 'Guardar Cliente';

        state.clienteId = null;
        state.nombre = '';
        state.apellidos = '';
        state.telefono = '';
        state.notas = '';

        if (detectedBanner) detectedBanner.style.display = 'none';

        setModalidad('momento');

        // Deseleccionar servicios
        document.querySelectorAll('.service-chip').forEach(c => c.classList.remove('active'));
        state.serviceId = null;
        state.serviceName = '';
        state.servicePrice = '0.00';
        state.serviceDuration = 0;

        // Regresar trabajadores al estado inicial (ocultos hasta elegir servicio)
        filterWorkersByService(null);

        resetToDefaultPosteriorDate();
        state.selectedDate = '{{ $today }}';

        updateSummary();

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'info',
                title: 'Formulario de recepción reiniciado',
                showConfirmButton: false,
                timer: 2000,
                background: '#0c142b',
                color: '#ffffff'
            });
        }

        inputNombre.focus();
    }

    btnResetForm.addEventListener('click', resetEntireForm);

    // ===================================================
    // 9. CONFIRMACIÓN Y REGISTRO DE TURNO / CITA
    // ===================================================
    btnConfirmTurno.addEventListener('click', function () {
        const nameRegex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/;

        if (!state.nombre || state.nombre.trim().length < 2) {
            Swal.fire({
                icon: 'warning',
                title: 'Nombre requerido',
                text: 'Por favor ingresa el nombre del cliente (mínimo 2 caracteres).',
                background: '#0c142b',
                color: '#ffffff',
                confirmButtonColor: '#0055ff'
            });
            inputNombre.focus();
            return;
        }

        if (!nameRegex.test(state.nombre.trim())) {
            Swal.fire({
                icon: 'warning',
                title: 'Nombre inválido',
                text: 'El nombre solo puede incluir letras y espacios, sin números ni caracteres especiales.',
                background: '#0c142b',
                color: '#ffffff',
                confirmButtonColor: '#0055ff'
            });
            inputNombre.focus();
            return;
        }

        if (!state.apellidos || state.apellidos.trim().length < 2) {
            Swal.fire({
                icon: 'warning',
                title: 'Apellidos requeridos',
                text: 'Por favor ingresa los apellidos del cliente (mínimo 2 caracteres).',
                background: '#0c142b',
                color: '#ffffff',
                confirmButtonColor: '#0055ff'
            });
            inputApellidos.focus();
            return;
        }

        if (!nameRegex.test(state.apellidos.trim())) {
            Swal.fire({
                icon: 'warning',
                title: 'Apellidos inválidos',
                text: 'Los apellidos solo pueden incluir letras y espacios, sin números ni caracteres especiales.',
                background: '#0c142b',
                color: '#ffffff',
                confirmButtonColor: '#0055ff'
            });
            inputApellidos.focus();
            return;
        }

        const cleanPhone = (state.telefono || '').replace(/\D/g, '');
        if (!cleanPhone || cleanPhone.length !== 10) {
            Swal.fire({
                icon: 'warning',
                title: 'Teléfono inválido',
                text: 'Por favor ingresa un número telefónico de contacto válido de exactamente 10 dígitos numéricos.',
                background: '#0c142b',
                color: '#ffffff',
                confirmButtonColor: '#0055ff'
            });
            inputTelefono.focus();
            return;
        }

        if (!state.serviceId) {
            Swal.fire({
                icon: 'warning',
                title: 'Servicio no seleccionado',
                text: 'Por favor selecciona un servicio del catálogo.',
                background: '#0c142b',
                color: '#ffffff',
                confirmButtonColor: '#0055ff'
            });
            return;
        }

        if (!state.workerId) {
            Swal.fire({
                icon: 'warning',
                title: 'Especialista no seleccionado',
                text: 'Por favor selecciona un especialista disponible.',
                background: '#0c142b',
                color: '#ffffff',
                confirmButtonColor: '#0055ff'
            });
            return;
        }

        if (!state.selectedTime) {
            Swal.fire({
                icon: 'warning',
                title: 'Turno u horario no disponible',
                text: 'No hay un horario disponible seleccionado. Los horarios para esta fecha y especialista están ocupados por citas o bloqueos de agenda.',
                background: '#0c142b',
                color: '#ffffff',
                confirmButtonColor: '#0055ff'
            });
            return;
        }

        const payload = {
            client_id: state.clienteId || null,
            nombre: state.nombre,
            apellidos: state.apellidos,
            telefono: cleanPhone,
            modalidad: state.modalidad,
            service_id: state.serviceId,
            worker_id: state.workerId,
            date: state.selectedDate,
            time: state.selectedTime,
            notas: inputNotas ? inputNotas.value.trim() : '',
            _token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        };

        btnConfirmTurno.disabled = true;
        btnConfirmTurno.innerHTML = `
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="animation: spin 1s linear infinite;">
                <circle cx="12" cy="12" r="10" stroke-opacity="0.25"></circle>
                <path d="M12 2a10 10 0 0 1 10 10"></path>
            </svg>
            <span>Procesando Turno...</span>
        `;

        fetch('{{ route("recepcion.turnos.registrar") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': payload._token
            },
            body: JSON.stringify(payload)
        })
        .then(response => response.json().then(data => ({ status: response.status, body: data })))
        .then(({ status, body }) => {
            btnConfirmTurno.disabled = false;
            btnConfirmTurno.innerHTML = `
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span>${state.modalidad === 'momento' ? 'Asignar Turno Inmediato' : 'Confirmar Cita Programada'}</span>
            `;

            if (status === 200 && body.success) {
                ticketFolio.textContent = body.folio_turno || 'TURNO-0001';
                ticketCliente.textContent = `${state.nombre} ${state.apellidos}`;
                ticketTelefono.textContent = state.telefono;
                ticketModalidad.textContent = state.modalidad === 'momento' ? 'Atención al Momento (Turno Sala)' : 'Fecha Posterior (Cita Programada)';
                ticketEspecialista.textContent = state.workerName;
                ticketServicio.textContent = `${state.serviceName} ($${state.servicePrice})`;
                ticketFechaHora.textContent = `${state.selectedDate} a las ${state.selectedTime} hrs`;

                ticketModalOverlay.style.display = 'flex';
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'No se pudo completar el registro',
                    text: body.mensaje || body.message || 'Verifica la disponibilidad del horario o los datos ingresados.',
                    background: '#0c142b',
                    color: '#ffffff',
                    confirmButtonColor: '#ef4444'
                });
            }
        })
        .catch(err => {
            console.error('Error al registrar turno:', err);
            btnConfirmTurno.disabled = false;
            btnConfirmTurno.innerHTML = `
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span>${state.modalidad === 'momento' ? 'Asignar Turno Inmediato' : 'Confirmar Cita Programada'}</span>
            `;

            Swal.fire({
                icon: 'error',
                title: 'Error de comunicación',
                text: 'No se pudo conectar con el servidor para agendar el turno. Por favor verifica tu conexión e intenta nuevamente.',
                background: '#0c142b',
                color: '#ffffff',
                confirmButtonColor: '#ef4444'
            });
        });
    });


    btnDoneTicket.addEventListener('click', function () {
        ticketModalOverlay.style.display = 'none';
        resetEntireForm();
    });

    ticketModalOverlay.addEventListener('click', function (e) {
        if (e.target === ticketModalOverlay) {
            ticketModalOverlay.style.display = 'none';
            resetEntireForm();
        }
    });

});
</script>
@endpush
