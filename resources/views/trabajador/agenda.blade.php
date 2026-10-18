@extends('trabajador.layout')

@section('title', 'Beauty & Barber Studio - Citas Organizadas por Fecha')

@push('styles')
<style>
    /* ===================================================
       AGENDA Y CONSULTA DE CITAS ORGANIZADAS POR FECHA
       =================================================== */
    .agenda-wrapper {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    /* ENCABEZADO SUPERIOR */
    .agenda-header {
        background: linear-gradient(135deg, rgba(10, 17, 36, 0.95) 0%, rgba(12, 20, 43, 0.98) 100%);
        border: 1px solid var(--border-subtle);
        border-radius: 18px;
        padding: 28px 36px;
        display: flex;
        flex-direction: column;
        gap: 20px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
    }

    .agenda-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, var(--barber-blue) 0%, var(--barber-red) 100%);
    }

    .agenda-header-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
    }

    .agenda-title-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .agenda-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--barber-blue-pale);
        color: var(--barber-blue-light);
        border: 1px solid var(--barber-blue-border);
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        width: fit-content;
        margin-bottom: 4px;
    }

    .agenda-badge::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background-color: var(--barber-blue-light);
    }

    .agenda-title {
        font-size: 26px;
        font-weight: 800;
        color: var(--text-white);
        margin: 0;
    }

    .agenda-subtitle {
        font-size: 14px;
        color: var(--text-muted);
    }

    /* TIRA SEMANAL INTERACTIVA (DAY STRIP) */
    .week-strip-card {
        background: var(--bg-card);
        border: 1px solid var(--border-subtle);
        border-radius: 16px;
        padding: 20px 24px;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .week-strip-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--border-subtle);
    }

    .week-range-text {
        font-size: 15px;
        font-weight: 700;
        color: var(--text-white);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .week-nav-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-nav-week {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid var(--border-subtle);
        color: var(--text-main);
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
    }

    .btn-nav-week:hover {
        background: var(--barber-blue-pale);
        border-color: var(--barber-blue-border);
        color: var(--text-white);
    }

    .btn-nav-week.active {
        background: var(--barber-blue);
        color: var(--text-white);
        border-color: var(--barber-blue);
    }

    /* GRID DE DÍAS */
    .days-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 10px;
    }

    @media (max-width: 900px) {
        .days-grid {
            display: flex;
            overflow-x: auto;
            padding-bottom: 8px;
        }
        .day-box-item {
            min-width: 90px;
            flex-shrink: 0;
        }
    }

    .day-box-item {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid var(--border-subtle);
        border-radius: 12px;
        padding: 12px 8px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 4px;
        text-decoration: none;
        color: var(--text-main);
        transition: all 0.2s var(--ease-fluid);
        position: relative;
    }

    .day-box-item:hover {
        background: rgba(255, 255, 255, 0.05);
        border-color: var(--barber-blue-border);
        transform: translateY(-2px);
    }

    .day-box-item.selected {
        background: var(--barber-blue-pale);
        border-color: var(--barber-blue);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25);
    }

    .day-box-item.selected .day-num {
        color: var(--text-white);
    }

    .day-box-item.today {
        border-color: rgba(37, 99, 235, 0.5);
    }

    .day-name-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
    }

    .day-num {
        font-size: 20px;
        font-weight: 800;
        color: var(--text-white);
        line-height: 1.1;
    }

    .day-count-badge {
        font-size: 10px;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.05);
        color: var(--text-muted);
        margin-top: 2px;
    }

    .day-box-item.has-citas .day-count-badge {
        background: var(--barber-blue-pale);
        color: var(--barber-blue-light);
        border: 1px solid rgba(37, 99, 235, 0.3);
    }

    .today-pill-tag {
        position: absolute;
        top: -6px;
        font-size: 9px;
        font-weight: 800;
        text-transform: uppercase;
        background: var(--barber-blue);
        color: var(--text-white);
        padding: 1px 6px;
        border-radius: 10px;
        letter-spacing: 0.05em;
    }

    /* BARRA DE FILTROS Y BÚSQUEDA */
    .filter-bar-card {
        background: var(--bg-card);
        border: 1px solid var(--border-subtle);
        border-radius: 16px;
        padding: 18px 24px;
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .filter-bar-form {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
    }

    .filter-left-group {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }

    .date-input-container {
        position: relative;
        display: flex;
        align-items: center;
    }

    .date-input-box {
        background: var(--bg-canvas);
        border: 1px solid var(--border-subtle);
        color: var(--text-white);
        padding: 8px 14px 8px 36px;
        border-radius: 8px;
        font-family: inherit;
        font-size: 13px;
        font-weight: 600;
        outline: none;
        transition: border-color 0.2s ease;
    }

    .date-input-box:focus {
        border-color: var(--barber-blue);
    }

    .date-input-icon {
        position: absolute;
        left: 10px;
        width: 16px;
        height: 16px;
        color: var(--text-muted);
        pointer-events: none;
    }

    .status-select-box {
        background: var(--bg-canvas);
        border: 1px solid var(--border-subtle);
        color: var(--text-white);
        padding: 8px 14px;
        border-radius: 8px;
        font-family: inherit;
        font-size: 13px;
        font-weight: 600;
        outline: none;
        cursor: pointer;
    }

    .status-select-box:focus {
        border-color: var(--barber-blue);
    }

    .search-input-wrapper {
        position: relative;
        flex: 1;
        min-width: 260px;
        max-width: 400px;
    }

    .search-input-box {
        width: 100%;
        background: var(--bg-canvas);
        border: 1px solid var(--border-subtle);
        color: var(--text-white);
        padding: 8px 14px 8px 36px;
        border-radius: 8px;
        font-family: inherit;
        font-size: 13px;
        outline: none;
        transition: border-color 0.2s ease;
    }

    .search-input-box:focus {
        border-color: var(--barber-blue);
    }

    .search-icon {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        width: 16px;
        height: 16px;
        color: var(--text-muted);
        pointer-events: none;
    }

    .btn-filter-apply {
        background: #1e40af;
        color: #ffffff;
        border: 1px solid rgba(147, 197, 253, 0.28);
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        text-decoration: none;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.35);
    }

    .btn-filter-apply:hover {
        background: #1d4ed8;
        color: #ffffff;
        border-color: rgba(147, 197, 253, 0.45);
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
    }

    /* PÍLDORAS DE RESULTADOS GLOBALES */
    .results-pills-row {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        padding-top: 12px;
        border-top: 1px solid var(--border-subtle);
    }

    .result-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 600;
        color: var(--text-muted);
        background: rgba(255, 255, 255, 0.03);
        padding: 4px 10px;
        border-radius: 20px;
        border: 1px solid var(--border-subtle);
    }

    .result-pill strong {
        color: var(--text-white);
    }

    /* ===================================================
       SECCIONES DE CITAS AGRUPADAS POR FECHA
       =================================================== */
    .citas-grouped-container {
        display: flex;
        flex-direction: column;
        gap: 28px;
    }

    .date-group-card {
        background: var(--bg-card);
        border: 1px solid var(--border-subtle);
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    }

    /* ENCABEZADO DEL DÍA */
    .date-group-header {
        background: linear-gradient(90deg, rgba(10, 17, 36, 0.95) 0%, rgba(12, 20, 43, 0.9) 100%);
        padding: 18px 24px;
        border-bottom: 1px solid var(--border-subtle);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
    }

    .date-group-title-side {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .date-group-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: var(--barber-blue-pale);
        color: var(--barber-blue-light);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .date-group-icon svg {
        width: 18px;
        height: 18px;
    }

    .date-group-heading {
        font-size: 17px;
        font-weight: 800;
        color: var(--text-white);
        margin: 0;
    }

    .date-context-badge {
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        padding: 2px 8px;
        border-radius: 12px;
    }

    .date-context-badge.badge-hoy {
        background: var(--barber-blue);
        color: var(--text-white);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25);
    }

    .date-context-badge.badge-manana {
        background: rgba(168, 85, 247, 0.2);
        color: #a78bfa;
        border: 1px solid rgba(168, 85, 247, 0.3);
    }

    .date-context-badge.badge-pasado {
        background: rgba(255, 255, 255, 0.05);
        color: var(--text-muted);
        border: 1px solid var(--border-subtle);
    }

    .date-group-meta-side {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .date-summary-item {
        font-size: 12px;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .date-summary-item strong {
        color: var(--text-white);
    }

    /* LISTA DE CITAS DENTRO DEL DÍA */
    .date-group-citas-list {
        padding: 16px 24px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .cita-row-item {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid var(--border-subtle);
        border-radius: 14px;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
        transition: all 0.2s ease;
        position: relative;
    }

    .cita-row-item:hover {
        background: rgba(255, 255, 255, 0.04);
        border-color: var(--border-strong);
        transform: translateY(-1px);
    }

    .cita-row-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 3px;
        height: 100%;
        border-radius: 14px 0 0 14px;
        background: var(--barber-blue);
    }

    .cita-row-item.status-en_atencion::before {
        background: var(--accent-warning);
    }

    .cita-row-item.status-completada::before {
        background: var(--accent-success);
    }

    .cita-row-item.status-cancelada::before {
        background: var(--barber-red);
    }

    /* COLUMNA 1: HORARIO */
    .col-horario {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: var(--bg-canvas);
        border: 1px solid var(--border-subtle);
        border-radius: 10px;
        padding: 10px 14px;
        min-width: 120px;
        text-align: center;
        flex-shrink: 0;
    }

    .horario-rango {
        font-size: 15px;
        font-weight: 800;
        color: var(--barber-blue-light);
    }

    .horario-duracion {
        font-size: 11px;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 4px;
        margin-top: 2px;
    }

    /* COLUMNA 2: CLIENTE */
    .col-cliente {
        display: flex;
        align-items: center;
        gap: 14px;
        flex: 1.2;
        min-width: 210px;
    }

    .cliente-avatar-circle {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: linear-gradient(135deg, #1e293b, #334155);
        border: 2px solid var(--border-subtle);
        color: var(--text-white);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 800;
        flex-shrink: 0;
    }

    .cliente-info-wrapper {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .cliente-nombre-title {
        font-size: 15px;
        font-weight: 800;
        color: var(--text-white);
    }

    .cliente-contact-links {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .link-contact {
        font-size: 12px;
        color: var(--text-muted);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 4px;
        transition: color 0.2s ease;
    }

    .link-contact:hover {
        color: var(--barber-blue-light);
    }

    /* COLUMNA 3: SERVICIO */
    .col-servicio {
        display: flex;
        flex-direction: column;
        gap: 3px;
        flex: 1.2;
        min-width: 190px;
    }

    .servicio-name-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--text-white);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .servicio-price-tag {
        color: #60a5fa;
        font-weight: 800;
        font-size: 13px;
    }

    .servicio-meta-tags {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .chip-category {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        background: rgba(255, 255, 255, 0.05);
        color: var(--text-muted);
        padding: 2px 6px;
        border-radius: 4px;
    }

    .nota-preview {
        font-size: 12px;
        color: #cbd5e1;
        font-style: italic;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        max-width: 250px;
    }

    /* COLUMNA 4: ESTADO Y CONSULTAR FICHA */
    .col-estado-action {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 8px;
        min-width: 150px;
    }

    .status-badge {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        padding: 4px 10px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .status-badge::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        display: inline-block;
    }

    .status-badge.confirmada {
        background: var(--barber-blue-pale);
        color: var(--barber-blue-light);
        border: 1px solid var(--barber-blue-border);
    }
    .status-badge.confirmada::before { background: var(--barber-blue-light); }

    .status-badge.en_atencion {
        background: var(--accent-warning-pale);
        color: var(--accent-warning);
        border: 1px solid rgba(245, 158, 11, 0.3);
    }
    .status-badge.en_atencion::before { background: var(--accent-warning); }

    .status-badge.completada {
        background: var(--accent-success-pale);
        color: var(--accent-success);
        border: 1px solid rgba(16, 185, 129, 0.3);
    }
    .status-badge.completada::before { background: var(--accent-success); }

    .status-badge.cancelada {
        background: var(--barber-red-light);
        color: var(--barber-red);
        border: 1px solid var(--barber-red-border);
    }
    .status-badge.cancelada::before { background: var(--barber-red); }

    .btn-ficha-modal {
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid var(--border-subtle);
        color: var(--text-white);
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
    }

    .btn-ficha-modal:hover {
        background: var(--barber-blue-pale);
        border-color: var(--barber-blue-border);
        color: var(--barber-blue-light);
    }

    /* ESTADO VACÍO ELEGANTE */
    .empty-citas-card {
        background: var(--bg-card);
        border: 1px dashed var(--border-subtle);
        border-radius: 16px;
        padding: 48px 24px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 14px;
    }

    .empty-citas-icon {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: var(--barber-blue-pale);
        color: var(--barber-blue-light);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .empty-citas-icon svg {
        width: 30px;
        height: 30px;
    }

    .empty-citas-title {
        font-size: 18px;
        font-weight: 800;
        color: var(--text-white);
        margin: 0;
    }

    .empty-citas-text {
        font-size: 14px;
        color: var(--text-muted);
        max-width: 440px;
        line-height: 1.5;
    }

    /* MODAL DE CONSULTA DETALLADA */
    .modal-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(5, 10, 21, 0.85);
        backdrop-filter: blur(8px);
        z-index: 100;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .modal-backdrop.active {
        display: flex;
    }

    .modal-dialog {
        background: var(--bg-card);
        border: 1px solid var(--border-strong);
        border-radius: 18px;
        width: 100%;
        max-width: 640px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6);
        position: relative;
        animation: modalFadeIn 0.25s var(--ease-fluid);
    }

    @keyframes modalFadeIn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }

    .modal-header {
        padding: 22px 28px;
        border-bottom: 1px solid var(--border-subtle);
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: sticky;
        top: 0;
        background: var(--bg-card);
        z-index: 10;
    }

    .modal-title {
        font-size: 18px;
        font-weight: 800;
        color: var(--text-white);
        margin: 0;
    }

    .modal-close-btn {
        background: transparent;
        border: none;
        color: var(--text-muted);
        width: 32px;
        height: 32px;
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .modal-close-btn:hover {
        background: rgba(255, 255, 255, 0.05);
        color: var(--text-white);
    }

    .modal-body {
        padding: 22px 28px;
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .detail-section-card {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid var(--border-subtle);
        border-radius: 12px;
        padding: 16px 18px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .detail-section-title {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--barber-blue-light);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .detail-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    @media (max-width: 550px) {
        .detail-grid-2 {
            grid-template-columns: 1fr;
        }
    }

    .detail-item {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .detail-item-label {
        font-size: 11px;
        color: var(--text-muted);
        text-transform: uppercase;
        font-weight: 600;
    }

    .detail-item-value {
        font-size: 14px;
        color: var(--text-white);
        font-weight: 700;
    }

    .modal-footer {
        padding: 18px 28px;
        border-top: 1px solid var(--border-subtle);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        background: rgba(0, 0, 0, 0.15);
    }

    .status-change-group {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .status-change-select {
        background: var(--bg-canvas);
        border: 1px solid var(--border-strong);
        color: var(--text-white);
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 700;
        outline: none;
        cursor: pointer;
    }

    /* ===================================================
       APARTADO DE OBSERVACIONES DEL SERVICIO
       =================================================== */
    .badge-observacion {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 11px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 6px;
        white-space: nowrap;
    }

    .badge-observacion.has-obs {
        background: rgba(16, 185, 129, 0.15);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    .badge-observacion.pending-obs {
        background: rgba(245, 158, 11, 0.12);
        color: #f59e0b;
        border: 1px solid rgba(245, 158, 11, 0.3);
    }

    .obs-snippet-preview {
        font-size: 12px;
        color: #93c5fd;
        margin-top: 3px;
        display: flex;
        align-items: center;
        gap: 5px;
        line-height: 1.3;
    }

    .obs-snippet-preview strong {
        color: var(--barber-blue-light);
        font-weight: 800;
        font-size: 11px;
        text-transform: uppercase;
    }

    .btn-action-completar {
        background: #1e40af;
        color: #ffffff;
        border: 1px solid rgba(147, 197, 253, 0.28);
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.35);
    }

    .btn-action-completar:hover {
        background: #1d4ed8;
        color: #ffffff;
        border-color: rgba(147, 197, 253, 0.45);
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
    }

    /* TARJETA DE OBSERVACIONES EN EL MODAL */
    .obs-section-card {
        background: rgba(15, 23, 42, 0.7);
        border: 1px solid rgba(59, 130, 246, 0.25);
        border-radius: 14px;
        padding: 18px 20px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        transition: all 0.3s ease;
        position: relative;
    }

    .obs-section-card.highlight-focus {
        border-color: #3b82f6;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.3);
        background: rgba(30, 58, 138, 0.2);
    }

    .obs-header-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
    }

    .obs-status-tag {
        font-size: 11px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.05);
        color: var(--text-muted);
    }

    .obs-status-tag.registered {
        background: rgba(16, 185, 129, 0.15);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    .obs-alert-completion {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(5, 150, 105, 0.08));
        border: 1px solid rgba(16, 185, 129, 0.35);
        border-radius: 10px;
        padding: 12px 14px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        color: #d1fae5;
        font-size: 12px;
        line-height: 1.45;
        animation: fadeInAlert 0.3s ease;
    }

    .obs-alert-completion svg {
        color: #10b981;
        flex-shrink: 0;
        margin-top: 2px;
    }

    @keyframes fadeInAlert {
        from { opacity: 0; transform: translateY(-4px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .obs-quick-chips {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }

    .obs-chip-label {
        font-size: 11px;
        color: var(--text-muted);
        font-weight: 600;
        margin-right: 2px;
    }

    .obs-chip-btn {
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid var(--border-subtle);
        color: var(--text-main);
        font-size: 11px;
        font-weight: 600;
        padding: 4px 9px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .obs-chip-btn:hover {
        background: var(--barber-blue-pale);
        border-color: var(--barber-blue-border);
        color: var(--text-white);
    }

    .obs-textarea-wrapper {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .obs-textarea {
        width: 100%;
        background: var(--bg-canvas);
        border: 1px solid var(--border-subtle);
        border-radius: 10px;
        padding: 12px 14px;
        color: var(--text-white);
        font-family: inherit;
        font-size: 13px;
        line-height: 1.5;
        resize: vertical;
        min-height: 95px;
        outline: none;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .obs-textarea:focus {
        border-color: var(--barber-blue);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }

    .obs-textarea-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 11px;
        color: var(--text-muted);
    }

    .obs-saved-notice {
        color: #10b981;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .obs-actions-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap;
        padding-top: 2px;
    }

    .btn-guardar-obs {
        background: #1e40af;
        color: #ffffff;
        border: 1px solid rgba(147, 197, 253, 0.28);
        padding: 7px 16px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.35);
    }

    .btn-guardar-obs:hover {
        background: #1d4ed8;
        color: #ffffff;
        border-color: rgba(147, 197, 253, 0.45);
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
    }
</style>
@endpush

@section('content')
<div class="agenda-wrapper">

    <!-- 1. ENCABEZADO -->
    <section class="agenda-header" aria-labelledby="agendaHeaderTitle">
        <div class="agenda-header-top">
            <div class="agenda-title-group">
                <div class="agenda-badge">
                    Agenda del Trabajador &bull; Cronograma por Fecha
                </div>
                <h1 id="agendaHeaderTitle" class="agenda-title">
                    Citas Organizadas por Fecha
                </h1>
                <p class="agenda-subtitle">
                    Consulta tus citas ordenadas cronológicamente por día, con el horario detallado, cliente asignado, servicio requerido y estado actual.
                </p>
            </div>
        </div>
    </section>

    <!-- 2. TIRA SEMANAL INTERACTIVA (DAY STRIP) -->
    <section class="week-strip-card" aria-label="Navegación de días de la semana">
        <div class="week-strip-header">
            <span class="week-range-text">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                Semana del {{ $inicioSemana->locale('es')->isoFormat('D [de] MMMM') }} al {{ $inicioSemana->copy()->addDays(6)->locale('es')->isoFormat('D [de] MMMM, YYYY') }}
            </span>

            <div class="week-nav-actions">
                <a href="{{ route('trabajador.agenda', ['fecha' => $semanaAnterior, 'estado' => $filtroEstado]) }}" class="btn-nav-week" title="Semana anterior">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                    <span>Anterior</span>
                </a>

                <a href="{{ route('trabajador.agenda', ['fecha' => \Carbon\Carbon::today()->format('Y-m-d'), 'estado' => $filtroEstado]) }}" class="btn-nav-week {{ $fechaSeleccionada === \Carbon\Carbon::today()->format('Y-m-d') ? 'active' : '' }}" title="Ir a hoy">
                    <span>Hoy</span>
                </a>

                <a href="{{ route('trabajador.agenda', ['fecha' => $semanaSiguiente, 'estado' => $filtroEstado]) }}" class="btn-nav-week" title="Semana siguiente">
                    <span>Siguiente</span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>

                <a href="{{ route('trabajador.agenda', ['fecha' => 'todas', 'estado' => $filtroEstado]) }}" class="btn-nav-week {{ $fechaSeleccionada === 'todas' ? 'active' : '' }}" title="Ver todas las fechas agrupadas">
                    <span>Ver Todas Agrupadas</span>
                </a>
            </div>
        </div>

        <!-- 7 DÍAS DE LA SEMANA -->
        <div class="days-grid">
            @foreach($diasSemana as $dia)
                <a 
                    href="{{ route('trabajador.agenda', ['fecha' => $dia['fecha'], 'estado' => $filtroEstado, 'buscar' => $terminoBusqueda]) }}" 
                    class="day-box-item {{ $dia['es_seleccionado'] ? 'selected' : '' }} {{ $dia['es_hoy'] ? 'today' : '' }} {{ $dia['citas_count'] > 0 ? 'has-citas' : '' }}"
                    title="Ver citas del {{ $dia['nombre_dia'] }} {{ $dia['numero_dia'] }}"
                >
                    @if($dia['es_hoy'])
                        <span class="today-pill-tag">Hoy</span>
                    @endif

                    <span class="day-name-label">{{ $dia['nombre_dia'] }}</span>
                    <span class="day-num">{{ $dia['numero_dia'] }}</span>
                    <span class="day-count-badge">
                        {{ $dia['citas_count'] }} {{ $dia['citas_count'] == 1 ? 'cita' : 'citas' }}
                    </span>
                </a>
            @endforeach
        </div>
    </section>

    <!-- 3. BARRA DE FILTROS Y BÚSQUEDA -->
    <section class="filter-bar-card" aria-label="Filtros de consulta">
        <form action="{{ route('trabajador.agenda') }}" method="GET" class="filter-bar-form">
            <input type="hidden" name="fecha" value="{{ $fechaSeleccionada }}">

            <div class="filter-left-group">
                <!-- Selector de fecha directa -->
                <div class="date-input-container">
                    <svg class="date-input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <input 
                        type="date" 
                        name="fecha" 
                        value="{{ $fechaSeleccionada !== 'todas' ? $fechaSeleccionada : '' }}" 
                        class="date-input-box"
                        onchange="this.form.submit()"
                        title="Ir a fecha específica"
                    >
                </div>

                <!-- Selector de Estado -->
                <select name="estado" class="status-select-box" onchange="this.form.submit()" title="Filtrar por estado">
                    <option value="todas" {{ $filtroEstado === 'todas' ? 'selected' : '' }}>Todos los estados</option>
                    <option value="confirmada" {{ $filtroEstado === 'confirmada' ? 'selected' : '' }}>Confirmadas</option>
                    <option value="en_atencion" {{ $filtroEstado === 'en_atencion' ? 'selected' : '' }}>En Atención</option>
                    <option value="completada" {{ $filtroEstado === 'completada' ? 'selected' : '' }}>Completadas</option>
                    <option value="cancelada" {{ $filtroEstado === 'cancelada' ? 'selected' : '' }}>Canceladas</option>
                </select>
            </div>

            <!-- Buscador por texto -->
            <div class="filter-left-group" style="flex: 1; justify-content: flex-end;">
                <div class="search-input-wrapper">
                    <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input 
                        type="text" 
                        name="buscar" 
                        value="{{ $terminoBusqueda }}" 
                        placeholder="Buscar cliente, teléfono o servicio..." 
                        class="search-input-box"
                    >
                </div>

                <button type="submit" class="btn-filter-apply">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                    <span>Buscar</span>
                </button>
            </div>
        </form>

        <!-- Píldoras de resumen de resultados -->
        <div class="results-pills-row">
            <span class="result-pill">
                Citas encontradas: <strong>{{ $totalCitas }}</strong>
            </span>
            <span class="result-pill">
                Días con citas: <strong>{{ $citasAgrupadas->count() }}</strong>
            </span>
            <span class="result-pill">
                En Atención: <strong style="color: var(--accent-warning);">{{ $citasEnAtencion }}</strong>
            </span>
            <span class="result-pill">
                Confirmadas: <strong style="color: var(--barber-blue-light);">{{ $citasConfirmadas }}</strong>
            </span>
            <span class="result-pill">
                Completadas: <strong style="color: var(--accent-success);">{{ $citasCompletadas }}</strong>
            </span>
            <span class="result-pill">
                Monto previsto: <strong style="color: #60a5fa;">${{ number_format($ingresosEstimados, 2) }}</strong>
            </span>
        </div>
    </section>

    <!-- 4. LISTADO ORGANIZADO POR FECHA -->
    <section class="citas-grouped-container" aria-label="Citas organizadas por fecha">
        @if($citasAgrupadas->isNotEmpty())
            @foreach($citasAgrupadas as $fechaStr => $citasDelDia)
                @php
                    $fechaCarbon = \Carbon\Carbon::parse($fechaStr);
                    $totalMinutosDia = $citasDelDia->sum('duracion_minutos');
                    $totalMontoDia = $citasDelDia->sum('precio');
                @endphp

                <!-- TARJETA DEL DÍA CON CITAS AGRUPADAS -->
                <div class="date-group-card">
                    
                    <!-- ENCABEZADO DE LA FECHA -->
                    <div class="date-group-header">
                        <div class="date-group-title-side">
                            <div class="date-group-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                            </div>
                            <div>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <h2 class="date-group-heading">
                                        {{ ucfirst($fechaCarbon->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY')) }}
                                    </h2>
                                    @if($fechaCarbon->isToday())
                                        <span class="date-context-badge badge-hoy">HOY</span>
                                    @elseif($fechaCarbon->isTomorrow())
                                        <span class="date-context-badge badge-manana">MAÑANA</span>
                                    @elseif($fechaCarbon->isPast())
                                        <span class="date-context-badge badge-pasado">HISTÓRICO</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- MÉTRICAS RESUMEN DEL DÍA -->
                        <div class="date-group-meta-side">
                            <span class="date-summary-item">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                                <strong>{{ $citasDelDia->count() }}</strong> {{ $citasDelDia->count() == 1 ? 'cita' : 'citas' }}
                            </span>
                            <span class="date-summary-item">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                <strong>{{ $totalMinutosDia }}</strong> min
                            </span>
                            <span class="date-summary-item">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="1" x2="12" y2="23"></line>
                                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                </svg>
                                <strong style="color: #60a5fa;">${{ number_format($totalMontoDia, 2) }}</strong>
                            </span>
                        </div>
                    </div>

                    <!-- CITAS EN ORDEN CRONOLÓGICO PARA ESTE DÍA -->
                    <div class="date-group-citas-list">
                        @foreach($citasDelDia as $cita)
                            <div class="cita-row-item status-{{ strtolower($cita->estado) }}">
                                
                                <!-- 1. HORARIO -->
                                <div class="col-horario">
                                    <span class="horario-rango">{{ $cita->hora_inicio }} - {{ $cita->hora_fin }}</span>
                                    <span class="horario-duracion">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <polyline points="12 6 12 12 16 14"></polyline>
                                        </svg>
                                        {{ $cita->duracion_minutos }} min
                                    </span>
                                </div>

                                <!-- 2. INFORMACIÓN DEL CLIENTE -->
                                <div class="col-cliente">
                                    <div class="cliente-avatar-circle">
                                        {{ strtoupper(substr($cita->cliente->name ?? 'C', 0, 1) . substr($cita->cliente->apellidos ?? 'L', 0, 1)) }}
                                    </div>
                                    <div class="cliente-info-wrapper">
                                        <span class="cliente-nombre-title">
                                            {{ $cita->cliente->name ?? 'Cliente Asignado' }} {{ $cita->cliente->apellidos ?? '' }}
                                        </span>
                                        <div class="cliente-contact-links">
                                            <a href="tel:{{ $cita->cliente->telefono ?? '' }}" class="link-contact" title="Llamar al cliente">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                                </svg>
                                                <span>{{ $cita->cliente->telefono ?? 'Sin teléfono' }}</span>
                                            </a>
                                            <a href="mailto:{{ $cita->cliente->email ?? '' }}" class="link-contact" title="Enviar correo">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                                    <polyline points="22,6 12,13 2,6"></polyline>
                                                </svg>
                                                <span>{{ $cita->cliente->email ?? 'Sin correo' }}</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- 3. SERVICIO SOLICITADO -->
                                <div class="col-servicio">
                                    <div class="servicio-name-title">
                                        <span>{{ $cita->service->name ?? 'Servicio Profesional' }}</span>
                                        <span class="servicio-price-tag">${{ number_format($cita->precio, 2) }}</span>
                                    </div>
                                    <div class="servicio-meta-tags">
                                        <span class="chip-category">
                                            {{ ucfirst($cita->service->category ?? 'Barbería') }}
                                        </span>
                                        @if(!empty($cita->observaciones))
                                            <span class="badge-observacion has-obs" title="{{ \Illuminate\Support\Str::limit($cita->observaciones, 90) }}">
                                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                    <polyline points="14 2 14 8 20 8"></polyline>
                                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                                </svg>
                                                Obs. registradas
                                            </span>
                                        @elseif($cita->estado === 'completada')
                                            <span class="badge-observacion pending-obs" title="Servicio concluido sin observaciones registradas">
                                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <circle cx="12" cy="12" r="10"></circle>
                                                    <line x1="12" y1="8" x2="12" y2="12"></line>
                                                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                                </svg>
                                                Sin observaciones
                                            </span>
                                        @endif
                                    </div>
                                    @if(!empty($cita->observaciones))
                                        <div class="obs-snippet-preview" title="Observaciones del especialista">
                                            <strong>Obs:</strong> {{ \Illuminate\Support\Str::limit($cita->observaciones, 55) }}
                                        </div>
                                    @endif
                                    @if($cita->notas)
                                        <div class="nota-preview" title="{{ $cita->notas }}">
                                            Nota cliente: {{ $cita->notas }}
                                        </div>
                                    @endif
                                </div>

                                <!-- 4. ESTADO Y ACCIONES -->
                                <div class="col-estado-action">
                                    <span class="status-badge {{ strtolower($cita->estado) }}">
                                        {{ str_replace('_', ' ', ucfirst($cita->estado)) }}
                                    </span>

                                    <div style="display: flex; gap: 6px; align-items: center; flex-wrap: wrap; justify-content: flex-end;">
                                        @if($cita->estado === 'en_atencion')
                                            <button 
                                                type="button" 
                                                class="btn-action-completar" 
                                                onclick="abrirModalFinalizar({{ $cita->id }})"
                                                title="Terminar servicio y registrar observaciones técnicas"
                                            >
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                                <span>Finalizar Servicio</span>
                                            </button>
                                        @endif

                                        <button 
                                            type="button" 
                                            class="btn-ficha-modal" 
                                            onclick="abrirModalConsulta({{ $cita->id }})"
                                            title="Consultar detalles y observaciones de la cita"
                                        >
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <line x1="12" y1="16" x2="12" y2="12"></line>
                                                <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                            </svg>
                                            <span>{{ !empty($cita->observaciones) ? 'Ver / Observaciones' : 'Consultar Ficha' }}</span>
                                        </button>
                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>

                </div>
            @endforeach
        @else
            <!-- ESTADO VACÍO -->
            <div class="empty-citas-card">
                <div class="empty-citas-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                </div>
                <h3 class="empty-citas-title">No hay citas registradas para la fecha seleccionada</h3>
                <p class="empty-citas-text">
                    No se encontraron citas para este día o con los filtros aplicados. Puedes navegar por la tira semanal superior o seleccionar "Ver Todas Agrupadas".
                </p>
                <div style="display: flex; gap: 10px; margin-top: 6px;">
                    <a href="{{ route('trabajador.agenda', ['fecha' => 'todas']) }}" class="btn-filter-apply">
                        Ver todas mis citas agrupadas
                    </a>
                    <a href="{{ route('trabajador.agenda', ['fecha' => \Carbon\Carbon::today()->format('Y-m-d')]) }}" class="btn-nav-week">
                        Ir a Hoy
                    </a>
                </div>
            </div>
        @endif
    </section>

</div>

<!-- 5. MODAL INTERACTIVO DE CONSULTA DE CITA -->
<div class="modal-backdrop" id="modalConsultaCita" aria-hidden="true" role="dialog" aria-modal="true">
    <div class="modal-dialog">
        
        <div class="modal-header">
            <div style="display: flex; align-items: center; gap: 10px;">
                <h2 class="modal-title" id="modalCitaTitle">Ficha de Cita #--</h2>
                <span id="modalEstadoBadge" class="status-badge confirmada">Confirmada</span>
            </div>
            <button type="button" class="modal-close-btn" onclick="cerrarModalConsulta()" aria-label="Cerrar modal">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <div class="modal-body">
            
            <!-- HORARIO DE LA CITA -->
            <div class="detail-section-card">
                <span class="detail-section-title">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    Horario y Programación
                </span>
                <div class="detail-grid-2">
                    <div class="detail-item">
                        <span class="detail-item-label">Fecha Programada</span>
                        <span class="detail-item-value" id="modalFecha">--</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-item-label">Horario de Atención</span>
                        <span class="detail-item-value" id="modalHorario" style="color: var(--barber-blue-light);">--:-- - --:--</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-item-label">Duración</span>
                        <span class="detail-item-value" id="modalDuracion">-- min</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-item-label">Precio del Servicio</span>
                        <span class="detail-item-value" id="modalPrecio" style="color: #60a5fa;">$--</span>
                    </div>
                </div>
            </div>

            <!-- INFORMACIÓN DEL CLIENTE -->
            <div class="detail-section-card">
                <span class="detail-section-title">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    Información del Cliente
                </span>
                <div class="detail-grid-2">
                    <div class="detail-item">
                        <span class="detail-item-label">Nombre del Cliente</span>
                        <span class="detail-item-value" id="modalClienteNombre">--</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-item-label">Teléfono de Contacto</span>
                        <a href="#" id="modalClienteTelLink" class="detail-item-value" style="color: var(--barber-blue-light); text-decoration: none;">
                            <span id="modalClienteTel">--</span>
                        </a>
                    </div>
                    <div class="detail-item" style="grid-column: span 2;">
                        <span class="detail-item-label">Correo Electrónico</span>
                        <a href="#" id="modalClienteEmailLink" class="detail-item-value" style="color: var(--text-muted); text-decoration: none;">
                            <span id="modalClienteEmail">--</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- SERVICIO SOLICITADO -->
            <div class="detail-section-card">
                <span class="detail-section-title">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="6" cy="6" r="3"></circle>
                        <circle cx="6" cy="18" r="3"></circle>
                        <line x1="20" y1="4" x2="8.12" y2="15.88"></line>
                        <line x1="14.47" y1="14.48" x2="20" y2="20"></line>
                        <line x1="8.12" y1="8.12" x2="12" y2="12"></line>
                    </svg>
                    Servicio Solicitado
                </span>
                <div class="detail-item">
                    <span class="detail-item-label">Servicio</span>
                    <span class="detail-item-value" id="modalServicioNombre">--</span>
                </div>
                <div class="detail-item">
                    <span class="detail-item-label">Descripción</span>
                    <span class="detail-item-value" id="modalServicioDesc" style="font-weight: 500; font-size: 13px; color: var(--text-muted);">--</span>
                </div>
                <div class="detail-item">
                    <span class="detail-item-label">Notas o Requerimientos del Cliente</span>
                    <span class="detail-item-value" id="modalNotas" style="font-weight: 500; font-size: 13px; color: #cbd5e1; font-style: italic;">--</span>
                </div>
            </div>

            <!-- 4. APARTADO DE OBSERVACIONES DEL SERVICIO (FICHA TÉCNICA DEL ESPECIALISTA) -->
            <div class="detail-section-card obs-section-card" id="seccionObservaciones">
                <div class="obs-header-bar">
                    <span class="detail-section-title" style="color: var(--barber-blue-light);">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                        Observaciones del Servicio (Ficha Técnica)
                    </span>
                    <span id="obsEstadoIndicator" class="obs-status-tag">Sin registrar</span>
                </div>

                <!-- Banner dinámico al finalizar servicio -->
                <div id="obsAlertFinalizar" class="obs-alert-completion" style="display: none;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    <div>
                        <strong>¡Servicio Terminado!</strong> Registra aquí las notas técnicas del servicio: corte o estilo realizado, productos o fórmulas empleadas, sensibilidad de la piel y recomendaciones clave para su próxima visita.
                    </div>
                </div>

                <!-- Plantillas / Atajos rápidos -->
                <div class="obs-quick-chips">
                    <span class="obs-chip-label">Plantillas:</span>
                    <button type="button" class="obs-chip-btn" onclick="insertarPlantillaObs('Técnica de corte: ')">+ Técnica de corte</button>
                    <button type="button" class="obs-chip-btn" onclick="insertarPlantillaObs('Productos aplicados: ')">+ Productos</button>
                    <button type="button" class="obs-chip-btn" onclick="insertarPlantillaObs('Preferencia del cliente: ')">+ Preferencia</button>
                    <button type="button" class="obs-chip-btn" onclick="insertarPlantillaObs('Recomendación próxima cita: ')">+ Próxima Cita</button>
                </div>

                <!-- Textarea de observaciones -->
                <div class="obs-textarea-wrapper">
                    <textarea 
                        id="modalObservacionesInput" 
                        class="obs-textarea" 
                        rows="4" 
                        placeholder="Escribe las observaciones técnicas del servicio: técnica empleada, fórmulas, productos recomendados, detalles del cliente..."
                        maxlength="2500"
                        oninput="actualizarContadorObs()"
                    ></textarea>
                    <div class="obs-textarea-meta">
                        <span id="obsUltimaActualizacion" class="obs-saved-notice"></span>
                        <span id="obsCharCounter" class="obs-char-count">0 / 2500 caracteres</span>
                    </div>
                </div>

                <!-- Fila de acciones -->
                <div class="obs-actions-row">
                    <span style="font-size: 11px; color: var(--text-muted);">
                        Ficha técnica reservada para el especialista y administración.
                    </span>
                    <button type="button" class="btn-guardar-obs" id="btnGuardarObs" onclick="guardarObservacionesSolo()">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                            <polyline points="17 21 17 13 7 13 7 21"></polyline>
                            <polyline points="7 3 7 8 15 8"></polyline>
                        </svg>
                        <span id="btnGuardarObsText">Guardar Observaciones</span>
                    </button>
                </div>
            </div>

        </div>

        <div class="modal-footer">
            <div class="status-change-group">
                <label for="selectNuevoEstado" style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Estado:</label>
                <select id="selectNuevoEstado" class="status-change-select" onchange="manejarCambioEstadoModal()">
                    <option value="confirmada">Confirmada</option>
                    <option value="en_atencion">En Atención</option>
                    <option value="completada">Completada</option>
                    <option value="cancelada">Cancelada</option>
                </select>
                <button type="button" class="btn-filter-apply" id="btnActualizarEstadoModal" onclick="actualizarEstadoCitaModal()">
                    Actualizar Estado
                </button>
            </div>

            <button type="button" class="btn-nav-week" onclick="cerrarModalConsulta()">
                Cerrar
            </button>
        </div>

    </div>
</div>

@push('scripts')
<script>
    let citaActualId = null;

    /**
     * Consulta y despliega la información completa de la cita en el modal.
     */
    async function abrirModalConsulta(citaId, focusObs = false) {
        citaActualId = citaId;
        const modal = document.getElementById('modalConsultaCita');
        
        try {
            const response = await fetch(`/trabajador/citas/${citaId}`);
            if (!response.ok) throw new Error('No se pudo cargar la cita');

            const data = await response.json();
            const cita = data.cita;

            document.getElementById('modalCitaTitle').textContent = `Ficha de Cita #${cita.id}`;
            
            // Badge de estado
            const badge = document.getElementById('modalEstadoBadge');
            badge.className = `status-badge ${cita.estado.toLowerCase()}`;
            badge.textContent = cita.estado.replace('_', ' ').toUpperCase();

            // Horario
            document.getElementById('modalFecha').textContent = cita.fecha_formateada;
            document.getElementById('modalHorario').textContent = cita.rango_horario;
            document.getElementById('modalDuracion').textContent = `${cita.duracion_minutos} min`;
            document.getElementById('modalPrecio').textContent = `$${cita.precio}`;

            // Cliente
            document.getElementById('modalClienteNombre').textContent = cita.cliente.nombre_completo;
            document.getElementById('modalClienteTel').textContent = cita.cliente.telefono;
            document.getElementById('modalClienteTelLink').href = `tel:${cita.cliente.telefono}`;
            document.getElementById('modalClienteEmail').textContent = cita.cliente.email;
            document.getElementById('modalClienteEmailLink').href = `mailto:${cita.cliente.email}`;

            // Servicio
            document.getElementById('modalServicioNombre').textContent = `${cita.servicio.nombre} (${cita.servicio.categoria})`;
            document.getElementById('modalServicioDesc').textContent = cita.servicio.descripcion;
            document.getElementById('modalNotas').textContent = cita.notas;

            // Observaciones
            const obsText = cita.observaciones || '';
            const obsInput = document.getElementById('modalObservacionesInput');
            obsInput.value = obsText;
            actualizarContadorObs();

            const obsIndicator = document.getElementById('obsEstadoIndicator');
            if (obsText.trim().length > 0) {
                obsIndicator.className = 'obs-status-tag registered';
                obsIndicator.textContent = 'Registradas ✅';
                document.getElementById('obsUltimaActualizacion').textContent = 'Observaciones registradas para este servicio.';
            } else {
                obsIndicator.className = 'obs-status-tag';
                obsIndicator.textContent = 'Sin registrar';
                document.getElementById('obsUltimaActualizacion').textContent = '';
            }

            // Selector de estado
            const selectEstado = document.getElementById('selectNuevoEstado');
            if (focusObs) {
                selectEstado.value = 'completada';
            } else {
                selectEstado.value = cita.estado.toLowerCase();
            }

            // Ocultar o deshabilitar opción cancelada si la cita ya es pasada
            const optCancModal = selectEstado.querySelector('option[value="cancelada"]');
            if (optCancModal) {
                const noCancelable = Boolean(cita.es_pasada) || cita.puede_cancelar === false;
                optCancModal.disabled = noCancelable && cita.estado.toLowerCase() !== 'cancelada';
                optCancModal.hidden = noCancelable && cita.estado.toLowerCase() !== 'cancelada';
            }

            manejarCambioEstadoModal();

            // Abrir modal
            modal.classList.add('active');

            if (focusObs) {
                setTimeout(() => {
                    const seccion = document.getElementById('seccionObservaciones');
                    if (seccion) {
                        seccion.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    obsInput.focus();
                }, 200);
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error de consulta',
                text: error.message || 'No fue posible consultar la información de la cita.',
                background: '#0a1124',
                color: '#ffffff'
            });
        }
    }

    /**
     * Abre el modal directamente en modo finalización con foco en observaciones.
     */
    function abrirModalFinalizar(citaId) {
        abrirModalConsulta(citaId, true);
    }

    function cerrarModalConsulta() {
        document.getElementById('modalConsultaCita').classList.remove('active');
        citaActualId = null;
        const seccion = document.getElementById('seccionObservaciones');
        if (seccion) seccion.classList.remove('highlight-focus');
        document.getElementById('obsAlertFinalizar').style.display = 'none';
    }

    // Cerrar al pulsar fuera del cuadro de diálogo
    document.getElementById('modalConsultaCita').addEventListener('click', function (e) {
        if (e.target === this) {
            cerrarModalConsulta();
        }
    });

    /**
     * Maneja el cambio reactivo en el selector de estado del modal.
     * Si se selecciona 'completada', resalta el apartado de observaciones y despliega el banner.
     */
    function manejarCambioEstadoModal() {
        const select = document.getElementById('selectNuevoEstado');
        const alertBanner = document.getElementById('obsAlertFinalizar');
        const seccion = document.getElementById('seccionObservaciones');
        const btnActualizar = document.getElementById('btnActualizarEstadoModal');

        if (select.value === 'completada') {
            alertBanner.style.display = 'flex';
            seccion.classList.add('highlight-focus');
            btnActualizar.innerHTML = `
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span>Finalizar y Guardar</span>
            `;
            btnActualizar.style.background = 'linear-gradient(135deg, #10b981, #059669)';
        } else {
            alertBanner.style.display = 'none';
            seccion.classList.remove('highlight-focus');
            btnActualizar.innerHTML = 'Actualizar Estado';
            btnActualizar.style.background = '#1e40af';
        }
    }

    /**
     * Inserta texto de plantilla al textarea de observaciones.
     */
    function insertarPlantillaObs(plantilla) {
        const textarea = document.getElementById('modalObservacionesInput');
        const valorActual = textarea.value.trim();

        if (valorActual.length > 0) {
            textarea.value = valorActual + '\n' + plantilla;
        } else {
            textarea.value = plantilla;
        }

        actualizarContadorObs();
        textarea.focus();
    }

    /**
     * Actualiza el contador de caracteres del textarea.
     */
    function actualizarContadorObs() {
        const textarea = document.getElementById('modalObservacionesInput');
        const counter = document.getElementById('obsCharCounter');
        const len = textarea.value.length;
        counter.textContent = `${len} / 2500 caracteres`;
    }

    /**
     * Guarda exclusivamente las observaciones técnicas del servicio sin cambiar necesariamente el estado.
     */
    async function guardarObservacionesSolo() {
        if (!citaActualId) return;

        const observaciones = document.getElementById('modalObservacionesInput').value.trim();
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const btn = document.getElementById('btnGuardarObs');
        const originalHtml = btn.innerHTML;

        btn.disabled = true;
        btn.innerHTML = '<span>Guardando...</span>';

        try {
            const response = await fetch(`/trabajador/citas/${citaActualId}/observaciones`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ observaciones: observaciones })
            });

            const result = await response.json();

            if (result.success) {
                const obsIndicator = document.getElementById('obsEstadoIndicator');
                if (observaciones.length > 0) {
                    obsIndicator.className = 'obs-status-tag registered';
                    obsIndicator.textContent = 'Registradas ✅';
                } else {
                    obsIndicator.className = 'obs-status-tag';
                    obsIndicator.textContent = 'Sin registrar';
                }

                document.getElementById('obsUltimaActualizacion').textContent = 'Guardado recientemente.';

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: result.mensaje || 'Observaciones del servicio guardadas.',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true,
                    background: '#0c142b',
                    color: '#ffffff'
                });

                // Recargar tras 850ms para reflejar badges en la lista de citas
                setTimeout(() => window.location.reload(), 850);
            } else {
                throw new Error(result.error || 'Error al guardar observaciones');
            }
        } catch (err) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: err.message || 'No fue posible registrar las observaciones.',
                background: '#0a1124',
                color: '#ffffff'
            });
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    }

    /**
     * Actualiza el estado de la cita desde el modal, guardando simultáneamente las observaciones ingresadas.
     */
    async function actualizarEstadoCitaModal() {
        if (!citaActualId) return;

        const nuevoEstado = document.getElementById('selectNuevoEstado').value;
        const observaciones = document.getElementById('modalObservacionesInput').value.trim();
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        try {
            const response = await fetch(`/trabajador/citas/${citaActualId}/estado`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    estado: nuevoEstado,
                    observaciones: observaciones
                })
            });

            const result = await response.json();

            if (result.success) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: result.mensaje,
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true,
                    background: nuevoEstado === 'completada' ? '#10b981' : '#1e40af',
                    color: '#ffffff'
                });

                cerrarModalConsulta();
                setTimeout(() => window.location.reload(), 800);
            } else {
                throw new Error(result.error || 'Error al actualizar estado');
            }
        } catch (err) {
            Swal.fire({
                icon: 'error',
                title: 'No se pudo actualizar',
                text: err.message || 'Ocurrió un error al intentar cambiar el estado.',
                background: '#0a1124',
                color: '#ffffff'
            });
        }
    }
</script>
@endpush
@endsection
