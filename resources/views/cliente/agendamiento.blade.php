@extends('cliente.layout')

@section('title', 'Beauty & Barber Studio - Agendamiento de Cita')

@push('styles')
<style>
    /* ===================================================
       ESTILOS DEL MÓDULO DE AGENDAMIENTO (LAYOUT COMPACTO)
       =================================================== */
    .main-content {
        padding: 16px 24px 30px !important;
    }

    .page-header {
        margin-bottom: 14px;
    }

    .page-title {
        font-size: 22px;
        font-weight: 800;
        color: var(--text-white);
        letter-spacing: -0.02em;
        margin-bottom: 2px;
        line-height: 1.2;
    }

    .page-subtitle {
        font-size: 12.5px;
        color: var(--text-muted);
        line-height: 1.35;
    }

    /* Grid principal: Catálogo a la izquierda (1.35fr) y Formulario/Resumen a la derecha (1fr) */
    .booking-grid {
        display: grid;
        grid-template-columns: 1.35fr 1fr;
        gap: 20px;
        align-items: start;
    }

    /* Contenedor del catálogo con scroll interno para evitar scroll excesivo en la página */
    .catalog-section-card {
        background: var(--bg-header);
        border: 1px solid var(--border-subtle);
        border-radius: 14px;
        padding: 18px 20px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
    }

    .catalog-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 14px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--border-subtle);
    }

    .header-title-box {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .step-badge {
        width: 26px;
        height: 26px;
        border-radius: 7px;
        background: var(--barber-blue);
        color: #ffffff;
        font-size: 12px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(0, 85, 255, 0.4);
    }

    .booking-card-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-white);
        letter-spacing: -0.01em;
        line-height: 1.2;
    }

    .section-desc {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 1px;
    }

    .btn-reset-filters {
        background: transparent;
        border: 1px solid var(--border-subtle);
        color: var(--text-muted);
        font-size: 11.5px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 10px;
        border-radius: 6px;
        transition: all 0.2s ease;
        font-family: inherit;
    }

    .btn-reset-filters:hover {
        background: var(--barber-blue-pale);
        color: var(--barber-blue-light);
        border-color: var(--barber-blue-border);
    }

    /* Pestañas de categoría compactas */
    .catalog-filter-bar {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 14px;
        padding: 4px;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid var(--border-subtle);
        border-radius: 8px;
    }

    .catalog-filter-btn {
        flex: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 6px;
        border: 1px solid transparent;
        background: transparent;
        color: var(--text-muted);
        font-family: inherit;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s var(--ease-fluid);
        white-space: nowrap;
    }

    .catalog-filter-btn:hover {
        color: var(--text-white);
        background: rgba(255, 255, 255, 0.05);
    }

    .catalog-filter-btn.active {
        background: var(--barber-blue-pale);
        color: var(--text-white);
        border-color: var(--barber-blue-border);
        box-shadow: 0 2px 8px rgba(0, 85, 255, 0.2);
    }

    .catalog-badge-count {
        background: rgba(255, 255, 255, 0.08);
        color: var(--text-muted);
        font-size: 10px;
        font-weight: 700;
        padding: 1px 6px;
        border-radius: 8px;
    }

    .catalog-filter-btn.active .catalog-badge-count {
        background: var(--barber-blue);
        color: #fff;
    }

    /* Contenedor scrolleable del catálogo para no alargar la página */
    .catalog-scroll-container {
        max-height: calc(100vh - 215px);
        min-height: 440px;
        overflow-y: auto;
        padding-right: 6px;
    }

    .catalog-scroll-container::-webkit-scrollbar {
        width: 5px;
    }

    .catalog-scroll-container::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.02);
        border-radius: 4px;
    }

    .catalog-scroll-container::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.15);
        border-radius: 4px;
    }

    .catalog-scroll-container::-webkit-scrollbar-thumb:hover {
        background: var(--barber-blue);
    }

    /* Bloques de categorías: Barbería y Estética */
    .category-block {
        margin-bottom: 18px;
        transition: all 0.2s ease;
    }

    .category-block:last-child {
        margin-bottom: 0;
    }

    .category-block.is-hidden {
        display: none !important;
    }

    .category-block-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px;
        padding: 8px 12px;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid var(--border-subtle);
    }

    .category-block-header.barberia {
        border-left: 3px solid var(--barber-blue);
    }

    .category-block-header.estetica {
        border-left: 3px solid #ec4899;
    }

    .cat-header-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .cat-icon-badge {
        width: 28px;
        height: 28px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .cat-icon-badge.barberia {
        background: rgba(0, 85, 255, 0.15);
        color: #60a5fa;
        border: 1px solid rgba(0, 85, 255, 0.3);
    }

    .cat-icon-badge.estetica {
        background: rgba(236, 72, 153, 0.15);
        color: #f472b6;
        border: 1px solid rgba(236, 72, 153, 0.3);
    }

    .category-block-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--text-white);
        line-height: 1.2;
    }

    .category-block-desc {
        font-size: 11px;
        color: var(--text-muted);
        line-height: 1.25;
    }

    .cat-pill {
        font-size: 10px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 12px;
        white-space: nowrap;
    }

    .cat-pill.barberia {
        background: rgba(0, 85, 255, 0.12);
        color: #93c5fd;
        border: 1px solid rgba(0, 85, 255, 0.25);
    }

    .cat-pill.estetica {
        background: rgba(236, 72, 153, 0.12);
        color: #fbcfe8;
        border: 1px solid rgba(236, 72, 153, 0.25);
    }

    /* Grilla de tarjetas de servicio compactas (2 columnas) */
    .services-cards-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }

    /* Tarjeta individual de servicio */
    .service-card {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid var(--border-subtle);
        border-radius: 12px;
        overflow: hidden;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        transition: all 0.25s var(--ease-fluid);
        position: relative;
    }

    .service-card:hover {
        background: rgba(255, 255, 255, 0.04);
        border-color: rgba(255, 255, 255, 0.2);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
    }

    /* Estado seleccionado distintivo */
    .service-card.is-selected {
        background: rgba(0, 85, 255, 0.08);
        border-color: var(--barber-blue);
        box-shadow: 0 0 0 2px var(--barber-blue), 0 8px 22px rgba(0, 85, 255, 0.35);
        transform: translateY(-2px);
    }

    /* ===================================================
       IMÁGENES DE SERVICIO: AMPLIAS, DESTACADAS Y ESTÉTICAS
       =================================================== */
    .service-card-media {
        height: 160px; /* Altura amplia y visible */
        position: relative;
        background: #060c18;
        overflow: hidden;
    }

    .service-card-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        display: block;
        transition: transform 0.4s var(--ease-fluid);
    }

    .service-card:hover .service-card-img {
        transform: scale(1.06);
    }

    /* Gradiente inferior para integrar la foto armónicamente con el cuerpo */
    .service-card-media::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 48px;
        background: linear-gradient(to top, rgba(10, 17, 36, 0.9) 0%, transparent 100%);
        pointer-events: none;
    }

    /* Estado premium para servicios sin imagen */
    .service-card-media.empty-media {
        display: flex;
        align-items: center;
        justify-content: center;
        border-bottom: 1px solid var(--border-subtle);
    }

    .service-card-media.empty-media.barberia {
        background: radial-gradient(circle at 50% 40%, rgba(0, 85, 255, 0.2) 0%, rgba(10, 17, 36, 0.95) 75%);
    }

    .service-card-media.empty-media.estetica {
        background: radial-gradient(circle at 50% 40%, rgba(236, 72, 153, 0.2) 0%, rgba(10, 17, 36, 0.95) 75%);
    }

    .empty-media-inner {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        text-align: center;
        z-index: 1;
    }

    .empty-media-icon {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.4);
    }

    .empty-media.barberia .empty-media-icon {
        background: rgba(0, 85, 255, 0.2);
        color: #93c5fd;
        border: 1px solid rgba(0, 85, 255, 0.4);
    }

    .empty-media.estetica .empty-media-icon {
        background: rgba(236, 72, 153, 0.2);
        color: #fbcfe8;
        border: 1px solid rgba(236, 72, 153, 0.4);
    }

    .empty-media-text {
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.02em;
        color: var(--text-muted);
    }

    .service-category-tag {
        position: absolute;
        top: 8px;
        left: 8px;
        font-size: 10px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 6px;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        backdrop-filter: blur(8px);
        z-index: 2;
    }

    .service-category-tag.barberia {
        background: rgba(10, 17, 36, 0.85);
        color: #93c5fd;
        border: 1px solid rgba(0, 85, 255, 0.4);
    }

    .service-category-tag.estetica {
        background: rgba(10, 17, 36, 0.85);
        color: #fbcfe8;
        border: 1px solid rgba(236, 72, 153, 0.4);
    }

    .service-card-selected-badge {
        position: absolute;
        top: 8px;
        right: 8px;
        display: none;
        align-items: center;
        gap: 4px;
        background: var(--barber-blue);
        color: #ffffff;
        font-size: 11px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 6px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
        z-index: 3;
    }

    .service-card.is-selected .service-card-selected-badge {
        display: inline-flex;
    }

    /* Cuerpo de la tarjeta */
    .service-card-body {
        padding: 12px 14px;
        display: flex;
        flex-direction: column;
        flex: 1;
        justify-content: space-between;
    }

    .service-card-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--text-white);
        margin-bottom: 4px;
        line-height: 1.3;
    }

    .service-card-desc {
        font-size: 11.5px;
        color: var(--text-muted);
        line-height: 1.35;
        margin-bottom: 10px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .service-card-desc.empty-desc {
        font-style: italic;
        opacity: 0.6;
    }

    /* Pie de la tarjeta */
    .service-card-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 10px;
        border-top: 1px solid var(--border-subtle);
        margin-top: auto;
    }

    .service-card-metrics {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .card-price-tag {
        font-size: 15px;
        font-weight: 800;
        color: var(--barber-blue-light);
        line-height: 1.1;
    }

    .card-duration-tag {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 11px;
        color: var(--text-muted);
        font-weight: 600;
    }

    .btn-card-select {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 5px 11px;
        border-radius: 6px;
        font-size: 11.5px;
        font-weight: 700;
        border: 1px solid var(--border-subtle);
        background: rgba(255, 255, 255, 0.04);
        color: var(--text-main);
        cursor: pointer;
        transition: all 0.2s ease;
        font-family: inherit;
        pointer-events: none;
    }

    .service-card:hover .btn-card-select {
        background: var(--barber-blue-pale);
        color: var(--barber-blue-light);
        border-color: var(--barber-blue-border);
    }

    .service-card.is-selected .btn-card-select {
        background: var(--barber-blue);
        color: #fff;
        border-color: var(--barber-blue);
    }

    .empty-category-notice {
        padding: 20px;
        text-align: center;
        background: rgba(255, 255, 255, 0.015);
        border: 1px dashed var(--border-subtle);
        border-radius: 8px;
        color: var(--text-muted);
        font-size: 12px;
    }

    /* ===================================================
       PANEL DERECHO: FORMULARIO DE CITA + RESUMEN INTEGRADO
       =================================================== */
    .booking-sidebar-card {
        background: var(--bg-header);
        border: 1px solid var(--border-subtle);
        border-radius: 14px;
        padding: 18px 20px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
        position: sticky;
        top: calc(var(--header-height) + 12px);
    }

    .booking-sidebar-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--barber-blue) 0%, var(--barber-red) 100%);
    }

    .sidebar-card-header {
        margin-bottom: 14px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--border-subtle);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* Caja informativa fija del Servicio Seleccionado (NO desplegable) */
    .selected-service-display {
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.03);
        border: 1px dashed var(--border-subtle);
        padding: 10px 14px;
        margin-bottom: 12px;
        transition: all 0.25s ease;
    }

    .selected-service-display.has-service {
        background: rgba(0, 85, 255, 0.06);
        border: 1px solid var(--barber-blue-border);
        box-shadow: 0 2px 10px rgba(0, 85, 255, 0.15);
    }

    .selected-service-empty {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--text-muted);
        font-size: 12px;
        font-style: italic;
    }

    .selected-service-empty svg {
        color: var(--barber-blue-light);
        flex-shrink: 0;
    }

    .selected-service-active {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .selected-service-info {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .selected-service-badge {
        font-size: 9.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--barber-blue-light);
    }

    .selected-service-badge.estetica {
        color: #f472b6;
    }

    .selected-service-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--text-white);
        line-height: 1.2;
    }

    .selected-service-meta {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 11.5px;
    }

    .selected-service-price {
        font-weight: 800;
        color: var(--barber-blue-light);
    }

    .selected-service-sep {
        color: var(--text-muted);
    }

    .selected-service-duration {
        color: var(--text-muted);
        font-weight: 500;
    }

    .selected-service-status-tag {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: var(--barber-blue-pale);
        border: 1px solid var(--barber-blue-border);
        color: var(--barber-blue-light);
        font-size: 10.5px;
        font-weight: 700;
        padding: 3px 7px;
        border-radius: 5px;
        white-space: nowrap;
    }

    /* Ocultar selector técnico accesible */
    .visually-hidden {
        position: absolute !important;
        width: 1px !important;
        height: 1px !important;
        padding: 0 !important;
        margin: -1px !important;
        overflow: hidden !important;
        clip: rect(0, 0, 0, 0) !important;
        white-space: nowrap !important;
        border: 0 !important;
    }

    /* Grupos del formulario */
    .form-group {
        margin-bottom: 12px;
    }

    .form-label {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 600;
        color: var(--text-main);
        margin-bottom: 4px;
    }

    .form-label svg {
        width: 13px;
        height: 13px;
        color: var(--barber-blue-light);
    }

    .form-hint {
        font-size: 10.5px;
        color: var(--text-muted);
        margin-top: 2px;
        line-height: 1.2;
    }

    .form-hint.text-success {
        color: #10b981 !important;
    }

    .form-hint.text-warning {
        color: #f59e0b !important;
    }

    /* Inputs y Selects */
    .form-control {
        width: 100%;
        padding: 8px 11px;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid var(--border-subtle);
        color: var(--text-white);
        font-family: inherit;
        font-size: 12.5px;
        transition: all 0.2s var(--ease-fluid);
        box-sizing: border-box;
    }

    .form-control:focus {
        border-color: var(--barber-blue);
        background: rgba(0, 85, 255, 0.05);
        outline: none;
        box-shadow: 0 0 0 3px rgba(0, 85, 255, 0.2);
    }

    /* Estilo deshabilitado para el selector de trabajadores */
    select.form-control:disabled {
        opacity: 0.55;
        cursor: not-allowed;
        background-color: rgba(255, 255, 255, 0.015);
        border-color: rgba(255, 255, 255, 0.04);
    }

    select.form-control {
        appearance: none;
        padding-right: 32px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 10px center;
        background-size: 13px;
        cursor: pointer;
    }

    select.form-control option {
        background-color: var(--bg-header);
        color: var(--text-white);
        padding: 6px 8px;
    }

    select.form-control optgroup {
        background-color: #0c182c;
        color: #93c5fd;
        font-weight: 700;
        font-size: 10.5px;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        padding: 6px 6px;
    }

    input[type="date"].form-control {
        color-scheme: dark;
        cursor: pointer;
    }

    .form-row-two {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }

    /* Sección de Resumen dentro del sidebar */
    .summary-section-box {
        margin-top: 14px;
        padding-top: 12px;
        border-top: 1px solid var(--border-subtle);
    }

    .summary-section-title {
        font-size: 13.5px;
        font-weight: 700;
        color: var(--text-white);
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .summary-details {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 12px;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        font-size: 12px;
    }

    .summary-item-label {
        color: var(--text-muted);
        font-weight: 500;
    }

    .summary-item-val {
        color: var(--text-white);
        font-weight: 600;
        text-align: right;
        max-width: 60%;
        word-break: break-word;
    }

    .summary-item-val.empty {
        color: var(--text-muted);
        font-style: italic;
        font-weight: 400;
    }

    .metrics-highlight-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        margin-bottom: 12px;
    }

    .metric-box {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid var(--border-subtle);
        border-radius: 8px;
        padding: 7px 8px;
        text-align: center;
    }

    .metric-box-title {
        font-size: 9.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--text-muted);
        margin-bottom: 1px;
    }

    .metric-box-value {
        font-size: 16px;
        font-weight: 800;
        color: var(--text-white);
    }

    .metric-box.price .metric-box-value {
        color: var(--barber-blue-light);
    }

    .metric-box.duration .metric-box-value {
        color: var(--barber-red);
    }

    /* Botones de acción */
    .actions-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid var(--border-subtle);
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s var(--ease-fluid);
        font-family: inherit;
        border: none;
    }

    .btn-secondary {
        background: rgba(255, 255, 255, 0.05);
        color: var(--text-main);
        border: 1px solid var(--border-subtle);
    }

    .btn-secondary:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: var(--border-strong);
        color: var(--text-white);
    }

    .btn-primary {
        background: var(--barber-blue);
        color: var(--text-white);
        box-shadow: 0 4px 12px rgba(0, 85, 255, 0.3);
    }

    .btn-primary:hover {
        background: #0044cc;
        box-shadow: 0 6px 16px rgba(0, 85, 255, 0.5);
    }

    /* ===================================================
       RESPONSIVIDAD
       =================================================== */
    @media (max-width: 992px) {
        .booking-grid {
            grid-template-columns: 1fr;
        }

        .booking-sidebar-card {
            position: static;
        }

        .catalog-scroll-container {
            max-height: none;
            min-height: auto;
        }
    }

    @media (max-width: 640px) {
        .services-cards-grid {
            grid-template-columns: 1fr;
        }

        .form-row-two {
            grid-template-columns: 1fr;
        }

        .actions-bar {
            flex-direction: column-reverse;
            gap: 10px;
        }

        .btn {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
    <!-- ENCABEZADO DE LA SECCIÓN -->
    <div class="page-header">
        <h1 class="page-title">Agendamiento de Cita</h1>
        <p class="page-subtitle">Elige tu servicio directamente en el catálogo y configura los detalles de tu cita.</p>
    </div>

    <!-- CONTENEDOR DE AGENDAMIENTO -->
    <div class="booking-grid">
        
        <!-- ==============================================
             COLUMNA IZQUIERDA: CATÁLOGO DE SERVICIOS
             ============================================== -->
        <section class="catalog-section-card" id="catalogCard" aria-label="Catálogo de servicios">
            <div class="catalog-card-header">
                <div class="header-title-box">
                    <span class="step-badge" aria-hidden="true">1</span>
                    <div>
                        <h2 class="booking-card-title">Catálogo de Servicios</h2>
                        <p class="section-desc">Selecciona un servicio haciendo clic sobre su tarjeta.</p>
                    </div>
                </div>

                <button type="button" class="btn-reset-filters" id="btnResetFilters" title="Restablecer servicio y trabajador">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path>
                        <path d="M3 3v5h5"></path>
                    </svg>
                    <span>Limpiar selección</span>
                </button>
            </div>

            <!-- Pestañas de filtrado de categoría -->
            <div class="catalog-filter-bar" role="tablist" aria-label="Categorías del catálogo">
                <button type="button" class="catalog-filter-btn active" data-filter="all" role="tab" aria-selected="true">
                    <span>Todos</span>
                    <span class="catalog-badge-count">{{ isset($servicios) ? $servicios->count() : 0 }}</span>
                </button>

                <button type="button" class="catalog-filter-btn" data-filter="barberia" role="tab" aria-selected="false">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="6" cy="6" r="3"></circle>
                        <circle cx="6" cy="18" r="3"></circle>
                        <line x1="20" y1="4" x2="8.12" y2="15.88"></line>
                        <line x1="14.47" y1="14.48" x2="20" y2="20"></line>
                        <line x1="8.12" y1="8.12" x2="12" y2="12"></line>
                    </svg>
                    <span>Barbería</span>
                    <span class="catalog-badge-count">{{ isset($serviciosBarberia) ? $serviciosBarberia->count() : (isset($servicios) ? $servicios->filter(fn($s) => strtolower($s->category ?? '') === 'barberia')->count() : 0) }}</span>
                </button>

                <button type="button" class="catalog-filter-btn" data-filter="estetica" role="tab" aria-selected="false">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                    </svg>
                    <span>Estética</span>
                    <span class="catalog-badge-count">{{ isset($serviciosEstetica) ? $serviciosEstetica->count() : (isset($servicios) ? $servicios->filter(fn($s) => strtolower($s->category ?? '') === 'estetica')->count() : 0) }}</span>
                </button>
            </div>

            @php
                $listBarberia = isset($serviciosBarberia) ? $serviciosBarberia : (isset($servicios) ? $servicios->filter(fn($s) => strtolower($s->category ?? '') === 'barberia') : collect());
                $listEstetica = isset($serviciosEstetica) ? $serviciosEstetica : (isset($servicios) ? $servicios->filter(fn($s) => strtolower($s->category ?? '') === 'estetica') : collect());
            @endphp

            <!-- Contenedor con scroll interno para catálogo compacto -->
            <div class="catalog-scroll-container">

                <!-- 1.1 CATEGORÍA BARBERÍA -->
                <div class="category-block" id="categoryBlockBarberia" data-category="barberia">
                    <div class="category-block-header barberia">
                        <div class="cat-header-info">
                            <div class="cat-icon-badge barberia" aria-hidden="true">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="6" cy="6" r="3"></circle>
                                    <circle cx="6" cy="18" r="3"></circle>
                                    <line x1="20" y1="4" x2="8.12" y2="15.88"></line>
                                    <line x1="14.47" y1="14.48" x2="20" y2="20"></line>
                                    <line x1="8.12" y1="8.12" x2="12" y2="12"></line>
                                </svg>
                            </div>
                            <div>
                                <h3 class="category-block-title">Barbería</h3>
                                <p class="category-block-desc">Cortes masculinos clásicos, fades, toalla caliente y barba.</p>
                            </div>
                        </div>
                        <span class="cat-pill barberia">{{ $listBarberia->count() }} servicios</span>
                    </div>

                    @if($listBarberia->count() > 0)
                        <div class="services-cards-grid">
                            @foreach($listBarberia as $servicio)
                                <div class="service-card" 
                                     id="service-card-{{ $servicio->id }}" 
                                     data-id="{{ $servicio->id }}" 
                                     data-name="{{ $servicio->name }}" 
                                     data-category="{{ $servicio->category }}" 
                                     data-price="{{ number_format($servicio->price, 2) }}" 
                                     data-duration="{{ $servicio->duration_minutes }}"
                                     role="button"
                                     tabindex="0"
                                     aria-selected="false"
                                     aria-label="Seleccionar servicio {{ $servicio->name }}">
                                    
                                    @if(!empty($servicio->image_path))
                                        <div class="service-card-media">
                                            <img src="{{ asset($servicio->image_path) }}" alt="{{ $servicio->name }}" class="service-card-img" loading="lazy">
                                            <span class="service-category-tag barberia">Barbería</span>
                                            <div class="service-card-selected-badge" aria-hidden="true">
                                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                                <span>Seleccionado</span>
                                            </div>
                                        </div>
                                    @else
                                        <div class="service-card-media empty-media barberia">
                                            <div class="empty-media-inner">
                                                <div class="empty-media-icon">
                                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <circle cx="6" cy="6" r="3"></circle>
                                                        <circle cx="6" cy="18" r="3"></circle>
                                                        <line x1="20" y1="4" x2="8.12" y2="15.88"></line>
                                                        <line x1="14.47" y1="14.48" x2="20" y2="20"></line>
                                                        <line x1="8.12" y1="8.12" x2="12" y2="12"></line>
                                                    </svg>
                                                </div>
                                                <span class="empty-media-text">Servicio de Barbería</span>
                                            </div>
                                            <span class="service-category-tag barberia">Barbería</span>
                                            <div class="service-card-selected-badge" aria-hidden="true">
                                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                                <span>Seleccionado</span>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="service-card-body">
                                        <h4 class="service-card-title">{{ $servicio->name }}</h4>
                                        
                                        <p class="service-card-desc {{ empty($servicio->description) ? 'empty-desc' : '' }}">
                                            {{ !empty($servicio->description) ? $servicio->description : 'Sin descripción adicional para este servicio.' }}
                                        </p>

                                        <div class="service-card-footer">
                                            <div class="service-card-metrics">
                                                <div class="card-price-tag">
                                                    <span>${{ number_format($servicio->price, 2) }}</span>
                                                </div>
                                                <div class="card-duration-tag">
                                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <circle cx="12" cy="12" r="10"></circle>
                                                        <polyline points="12 6 12 12 16 14"></polyline>
                                                    </svg>
                                                    <span>{{ $servicio->duration_minutes }} min</span>
                                                </div>
                                            </div>

                                            <button type="button" class="btn-card-select" tabindex="-1">
                                                <span class="btn-card-select-text">Elegir</span>
                                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="9 18 15 12 9 6"></polyline>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-category-notice">
                            <p>No hay servicios de barbería disponibles en este momento.</p>
                        </div>
                    @endif
                </div>

                <!-- 1.2 CATEGORÍA ESTÉTICA -->
                <div class="category-block" id="categoryBlockEstetica" data-category="estetica">
                    <div class="category-block-header estetica">
                        <div class="cat-header-info">
                            <div class="cat-icon-badge estetica" aria-hidden="true">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="category-block-title">Estética</h3>
                                <p class="category-block-desc">Cortes de dama, estilizado, tintes y tratamientos capilares.</p>
                            </div>
                        </div>
                        <span class="cat-pill estetica">{{ $listEstetica->count() }} servicios</span>
                    </div>

                    @if($listEstetica->count() > 0)
                        <div class="services-cards-grid">
                            @foreach($listEstetica as $servicio)
                                <div class="service-card" 
                                     id="service-card-{{ $servicio->id }}" 
                                     data-id="{{ $servicio->id }}" 
                                     data-name="{{ $servicio->name }}" 
                                     data-category="{{ $servicio->category }}" 
                                     data-price="{{ number_format($servicio->price, 2) }}" 
                                     data-duration="{{ $servicio->duration_minutes }}"
                                     role="button"
                                     tabindex="0"
                                     aria-selected="false"
                                     aria-label="Seleccionar servicio {{ $servicio->name }}">
                                    
                                    @if(!empty($servicio->image_path))
                                        <div class="service-card-media">
                                            <img src="{{ asset($servicio->image_path) }}" alt="{{ $servicio->name }}" class="service-card-img" loading="lazy">
                                            <span class="service-category-tag estetica">Estética</span>
                                            <div class="service-card-selected-badge" aria-hidden="true">
                                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                                <span>Seleccionado</span>
                                            </div>
                                        </div>
                                    @else
                                        <div class="service-card-media empty-media estetica">
                                            <div class="empty-media-inner">
                                                <div class="empty-media-icon">
                                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                                                    </svg>
                                                </div>
                                                <span class="empty-media-text">Servicio de Estética</span>
                                            </div>
                                            <span class="service-category-tag estetica">Estética</span>
                                            <div class="service-card-selected-badge" aria-hidden="true">
                                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                                <span>Seleccionado</span>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="service-card-body">
                                        <h4 class="service-card-title">{{ $servicio->name }}</h4>
                                        
                                        <p class="service-card-desc {{ empty($servicio->description) ? 'empty-desc' : '' }}">
                                            {{ !empty($servicio->description) ? $servicio->description : 'Sin descripción adicional para este servicio.' }}
                                        </p>

                                        <div class="service-card-footer">
                                            <div class="service-card-metrics">
                                                <div class="card-price-tag">
                                                    <span>${{ number_format($servicio->price, 2) }}</span>
                                                </div>
                                                <div class="card-duration-tag">
                                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <circle cx="12" cy="12" r="10"></circle>
                                                        <polyline points="12 6 12 12 16 14"></polyline>
                                                    </svg>
                                                    <span>{{ $servicio->duration_minutes }} min</span>
                                                </div>
                                            </div>

                                            <button type="button" class="btn-card-select" tabindex="-1">
                                                <span class="btn-card-select-text">Elegir</span>
                                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="9 18 15 12 9 6"></polyline>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-category-notice">
                            <p>No hay servicios de estética disponibles en este momento.</p>
                        </div>
                    @endif
                </div>

            </div>
        </section>

        <!-- ==============================================
             COLUMNA DERECHA: CONFIGURACIÓN DE CITA Y RESUMEN
             ============================================== -->
        <aside class="booking-sidebar-card" id="appointmentCard" aria-label="Configuración y resumen de la cita">
            <div class="sidebar-card-header">
                <div class="header-title-box">
                    <span class="step-badge" aria-hidden="true">2</span>
                    <div>
                        <h2 class="booking-card-title">Datos de la Cita</h2>
                        <p class="section-desc">Detalles del servicio y selección de especialista y fecha.</p>
                    </div>
                </div>
            </div>

            <!-- Formulario interactivo -->
            <form id="appointmentForm" onsubmit="event.preventDefault();">
                
                <!-- 1. SERVICIO SELECCIONADO (FIJO - NO DESPLEGABLE) -->
                <div class="form-group">
                    <label class="form-label">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="6" cy="6" r="3"></circle>
                            <circle cx="6" cy="18" r="3"></circle>
                            <line x1="20" y1="4" x2="8.12" y2="15.88"></line>
                            <line x1="14.47" y1="14.48" x2="20" y2="20"></line>
                            <line x1="8.12" y1="8.12" x2="12" y2="12"></line>
                        </svg>
                        <span>Servicio seleccionado</span>
                    </label>

                    <!-- Caja visual del servicio seleccionado (Fijado automáticamente desde los cards) -->
                    <div class="selected-service-display" id="selectedServiceDisplay">
                        <div class="selected-service-empty" id="selectedServiceEmpty">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                            <span>Selecciona un servicio en las tarjetas del catálogo</span>
                        </div>

                        <div class="selected-service-active" id="selectedServiceActive" style="display: none;">
                            <div class="selected-service-info">
                                <span class="selected-service-badge" id="selectedServiceBadge">Barbería</span>
                                <h4 class="selected-service-title" id="selectedServiceTitle">Nombre del servicio</h4>
                                <div class="selected-service-meta">
                                    <span class="selected-service-price" id="selectedServicePrice">$0.00</span>
                                    <span class="selected-service-sep">•</span>
                                    <span class="selected-service-duration" id="selectedServiceDuration">0 min</span>
                                </div>
                            </div>
                            <span class="selected-service-status-tag" title="Servicio fijado desde las tarjetas del catálogo">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                <span>Fijado</span>
                            </span>
                        </div>
                    </div>

                    <!-- Input técnico accesible para sincronización y compatibilidad de tests -->
                    <select id="serviceSelect" class="form-control visually-hidden" name="service_id" tabindex="-1" aria-hidden="true">
                        <option value="" selected>Selecciona un servicio</option>
                        @if(isset($servicios) && $servicios->count() > 0)
                            @foreach($servicios as $servicio)
                                <option 
                                    value="{{ $servicio->id }}" 
                                    data-price="{{ number_format($servicio->price, 2) }}" 
                                    data-duration="{{ $servicio->duration_minutes }}"
                                    data-name="{{ $servicio->name }}"
                                    data-category="{{ $servicio->category }}"
                                >
                                    {{ $servicio->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <!-- 2. SELECCIÓN DE TRABAJADOR / ESPECIALISTA (DESHABILITADO HASTA ELEGIR SERVICIO) -->
                <div class="form-group">
                    <label for="workerSelect" class="form-label">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <span>Trabajador o especialista</span>
                    </label>
                    <select id="workerSelect" class="form-control" name="worker_id" disabled>
                        <option value="" selected>Selecciona un trabajador</option>
                        @if(isset($trabajadores) && $trabajadores->count() > 0)
                            @foreach($trabajadores as $trabajador)
                                <option 
                                    value="{{ $trabajador->id }}"
                                    data-name="{{ trim(($trabajador->nombre ?? '') . ' ' . ($trabajador->apellidos ?? '')) ?: ('Trabajador #' . $trabajador->id) }}"
                                >
                                    {{ trim(($trabajador->nombre ?? '') . ' ' . ($trabajador->apellidos ?? '')) ?: ('Trabajador #' . $trabajador->id) }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    <p class="form-hint" id="workerHint">Primero selecciona un servicio en el catálogo para ver los especialistas disponibles.</p>
                </div>

                <!-- 3. SELECCIÓN DE FECHA Y HORARIO -->
                <div class="form-row-two">
                    <!-- Control visual de Fecha -->
                    <div class="form-group">
                        <label for="dateSelect" class="form-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            <span>Fecha de la cita</span>
                        </label>
                        <input 
                            type="date" 
                            id="dateSelect" 
                            class="form-control" 
                            name="appointment_date"
                            min="{{ date('Y-m-d') }}"
                        >
                    </div>

                    <!-- Control visual de Horario -->
                    <div class="form-group">
                        <label for="timeSelect" class="form-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            <span>Horario de atención</span>
                        </label>
                        <select id="timeSelect" class="form-control" name="appointment_time" disabled>
                            <option value="" disabled selected>Selecciona servicio, especialista y fecha primero</option>
                        </select>
                        <p class="form-hint" id="timeHint">Selecciona un servicio, especialista y fecha para consultar horarios disponibles.</p>
                    </div>
                </div>

                <!-- RESUMEN DEL SERVICIO Y VALORES ESTIMADOS -->
                <div class="summary-section-box">
                    <h3 class="summary-section-title">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="2" y="5" width="20" height="14" rx="2"></rect>
                            <line x1="2" y1="10" x2="22" y2="10"></line>
                        </svg>
                        <span>Resumen del Servicio</span>
                    </h3>

                    <!-- Cajas de Duración y Precio -->
                    <div class="metrics-highlight-grid">
                        <div class="metric-box duration">
                            <span class="metric-box-title">Duración</span>
                            <span class="metric-box-value" id="summaryDurationVal">—</span>
                        </div>
                        <div class="metric-box price">
                            <span class="metric-box-title">Precio Total</span>
                            <span class="metric-box-value" id="summaryPriceVal">$0.00</span>
                        </div>
                    </div>

                    <!-- Listado de detalles del resumen -->
                    <div class="summary-details">
                        <div class="summary-item">
                            <span class="summary-item-label">Servicio</span>
                            <span class="summary-item-val empty" id="summaryServiceName">Selecciona un servicio</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-item-label">Categoría</span>
                            <span class="summary-item-val empty" id="summaryCategoryName">—</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-item-label">Especialista</span>
                            <span class="summary-item-val empty" id="summaryWorkerName">Selecciona un servicio primero</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-item-label">Fecha</span>
                            <span class="summary-item-val empty" id="summaryDateVal">Selecciona una fecha</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-item-label">Horario</span>
                            <span class="summary-item-val empty" id="summaryTimeVal">Selecciona un horario</span>
                        </div>
                    </div>
                </div>

                <!-- ACCIONES: REGRESAR / CONTINUAR -->
                <div class="actions-bar">
                    <a href="{{ route('cliente.dashboard') }}" class="btn btn-secondary">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                        <span>Regresar</span>
                    </a>

                    <button type="button" class="btn btn-primary" id="btnContinue">
                        <span>Continuar</span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </button>
                </div>
            </form>
        </aside>
    </div>

    <!-- ==============================================================
         MODAL DE AVISO: HORARIO YA NO DISPONIBLE (DISEÑO AZUL + ICONO BLANCO)
         ============================================================== -->
    <div id="unavailableSlotModal" style="display: none; position: fixed; inset: 0; background: rgba(5,10,21,0.8); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(6px); padding: 16px;">
        <div style="background: linear-gradient(135deg, #091a3e 0%, #0d2868 50%, #11388f 100%); border: 1px solid rgba(59, 130, 246, 0.45); border-radius: 16px; width: 100%; max-width: 440px; padding: 28px 24px; text-align: center; box-shadow: 0 25px 50px -12px rgba(0, 50, 180, 0.5); color: #ffffff;">
            <!-- Icono de alerta de color blanco -->
            <div style="width: 58px; height: 58px; border-radius: 50%; background: rgba(255, 255, 255, 0.12); border: 2px solid #ffffff; margin: 0 auto 16px; display: flex; align-items: center; justify-content: center;">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
            </div>
            <h3 style="font-size: 1.25rem; font-weight: 800; color: #ffffff; margin-bottom: 8px;">Horario No Disponible</h3>
            <p id="unavailableModalMessage" style="font-size: 0.92rem; color: rgba(255, 255, 255, 0.9); line-height: 1.5; margin-bottom: 22px;">
                El horario seleccionado ya no está disponible. Por favor selecciona otro horario para continuar con tu cita.
            </p>
            <div style="display: flex; justify-content: center;">
                <button type="button" id="btnCloseUnavailableModal" style="background: #ffffff; color: #0b2256; font-weight: 700; padding: 10px 26px; border-radius: 10px; border: none; cursor: pointer; font-size: 0.9rem; transition: all 0.2s; box-shadow: 0 4px 12px rgba(0,0,0,0.25);">
                    Seleccionar otro horario
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        function escapeHtml(text) {
            if (text === null || text === undefined) return '';
            return String(text)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        // Datos de servicios
        const servicesData = [
            @if(isset($servicios) && $servicios->count() > 0)
                @foreach($servicios as $s)
                    {
                        id: {{ $s->id }},
                        name: @json($s->name),
                        category: @json($s->category ?? ''),
                        price: @json(number_format($s->price, 2)),
                        duration: {{ $s->duration_minutes }},
                        worker_ids: @json($s->trabajadoresActivos ? $s->trabajadoresActivos->pluck('id')->values()->all() : [])
                    },
                @endforeach
            @endif
        ];

        // Datos de trabajadores
        const workersData = [
            @if(isset($trabajadores) && $trabajadores->count() > 0)
                @foreach($trabajadores as $w)
                    {
                        id: {{ $w->id }},
                        name: @json(trim(($w->nombre ?? '') . ' ' . ($w->apellidos ?? '')) ?: ('Trabajador #' . $w->id)),
                        service_ids: @json($w->serviciosActivos ? $w->serviciosActivos->pluck('id')->values()->all() : [])
                    },
                @endforeach
            @endif
        ];

        // Elementos DOM del formulario
        const serviceSelect = document.getElementById('serviceSelect');
        const workerSelect = document.getElementById('workerSelect');
        const dateSelect = document.getElementById('dateSelect');
        const timeSelect = document.getElementById('timeSelect');
        const btnResetFilters = document.getElementById('btnResetFilters');
        const workerHint = document.getElementById('workerHint');

        // Elementos del bloque fijo de servicio seleccionado
        const selectedServiceDisplay = document.getElementById('selectedServiceDisplay');
        const selectedServiceEmpty = document.getElementById('selectedServiceEmpty');
        const selectedServiceActive = document.getElementById('selectedServiceActive');
        const selectedServiceBadge = document.getElementById('selectedServiceBadge');
        const selectedServiceTitle = document.getElementById('selectedServiceTitle');
        const selectedServicePrice = document.getElementById('selectedServicePrice');
        const selectedServiceDuration = document.getElementById('selectedServiceDuration');

        // Elementos del resumen
        const summaryServiceName = document.getElementById('summaryServiceName');
        const summaryCategoryName = document.getElementById('summaryCategoryName');
        const summaryWorkerName = document.getElementById('summaryWorkerName');
        const summaryDateVal = document.getElementById('summaryDateVal');
        const summaryTimeVal = document.getElementById('summaryTimeVal');
        const summaryPriceVal = document.getElementById('summaryPriceVal');
        const summaryDurationVal = document.getElementById('summaryDurationVal');

        // Elementos del catálogo
        const serviceCards = document.querySelectorAll('.service-card');
        const catalogFilterBtns = document.querySelectorAll('.catalog-filter-btn');
        const categoryBlockBarberia = document.getElementById('categoryBlockBarberia');
        const categoryBlockEstetica = document.getElementById('categoryBlockEstetica');

        let isSyncing = false;

        // Asegurar que al inicio no haya ningún trabajador seleccionado ni asignado si no hay servicio
        workerSelect.disabled = true;
        workerSelect.value = '';
        summaryWorkerName.textContent = 'Selecciona un servicio primero';
        summaryWorkerName.classList.add('empty');

        /**
         * Filtrado de pestañas del catálogo (Todos / Barbería / Estética)
         */
        catalogFilterBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                catalogFilterBtns.forEach(b => {
                    b.classList.remove('active');
                    b.setAttribute('aria-selected', 'false');
                });
                this.classList.add('active');
                this.setAttribute('aria-selected', 'true');

                const filter = this.getAttribute('data-filter');

                if (filter === 'all') {
                    if (categoryBlockBarberia) categoryBlockBarberia.classList.remove('is-hidden');
                    if (categoryBlockEstetica) categoryBlockEstetica.classList.remove('is-hidden');
                } else if (filter === 'barberia') {
                    if (categoryBlockBarberia) categoryBlockBarberia.classList.remove('is-hidden');
                    if (categoryBlockEstetica) categoryBlockEstetica.classList.add('is-hidden');
                } else if (filter === 'estetica') {
                    if (categoryBlockBarberia) categoryBlockBarberia.classList.add('is-hidden');
                    if (categoryBlockEstetica) categoryBlockEstetica.classList.remove('is-hidden');
                }
            });
        });

        /**
         * Actualiza el estado visual de las tarjetas del catálogo
         */
        function updateSelectedCardVisual(serviceId) {
            serviceCards.forEach(card => {
                const cardId = card.getAttribute('data-id');
                const isMatch = (cardId === String(serviceId));
                card.classList.toggle('is-selected', isMatch);
                card.setAttribute('aria-selected', isMatch ? 'true' : 'false');

                const btnText = card.querySelector('.btn-card-select-text');
                if (btnText) {
                    btnText.textContent = isMatch ? 'Elegido' : 'Elegir';
                }
            });
        }

        /**
         * Selección al hacer clic en cualquier tarjeta del catálogo
         */
        serviceCards.forEach(card => {
            card.addEventListener('click', function () {
                const sId = this.getAttribute('data-id');

                if (serviceSelect.value === sId) {
                    if (!workerSelect.value) {
                        renderWorkerOptions(sId);
                    }
                    fetchAvailability();
                    return;
                }

                serviceSelect.value = sId;
                serviceSelect.dispatchEvent(new Event('change'));
            });

            card.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.click();
                }
            });
        });

        /**
         * Renderiza las opciones de trabajadores basándose en el servicio seleccionado.
         */
        function renderWorkerOptions(selectedServiceId) {
            const currentWorkerVal = workerSelect.value;
            workerSelect.innerHTML = '';

            const defaultOpt = document.createElement('option');
            defaultOpt.value = '';
            defaultOpt.textContent = 'Selecciona un trabajador';
            workerSelect.appendChild(defaultOpt);

            if (!selectedServiceId) {
                workerSelect.disabled = true;
                defaultOpt.selected = true;
                workerHint.textContent = 'Primero selecciona un servicio en el catálogo para ver los especialistas disponibles.';
                summaryWorkerName.textContent = 'Selecciona un servicio primero';
                summaryWorkerName.classList.add('empty');
                return;
            }

            const sId = parseInt(selectedServiceId, 10);
            const sObj = servicesData.find(s => s.id === sId);
            const allowedWorkerIds = (sObj && sObj.worker_ids) ? sObj.worker_ids : [];

            // Filtrar ÚNICAMENTE especialistas que realizan este servicio seleccionado
            const matchingWorkers = workersData.filter(w => allowedWorkerIds.includes(w.id));

            if (matchingWorkers.length > 0) {
                workerSelect.disabled = false;

                let foundSelected = false;
                matchingWorkers.forEach(w => {
                    const opt = document.createElement('option');
                    opt.value = w.id;
                    opt.textContent = w.name;
                    opt.setAttribute('data-name', w.name);
                    if (currentWorkerVal && parseInt(currentWorkerVal, 10) === w.id) {
                        opt.selected = true;
                        foundSelected = true;
                        summaryWorkerName.textContent = w.name;
                        summaryWorkerName.classList.remove('empty');
                    }
                    workerSelect.appendChild(opt);
                });

                if (matchingWorkers.length === 1) {
                    // Si el servicio tiene un único especialista disponible, auto-seleccionarlo de inmediato
                    const singleWorker = matchingWorkers[0];
                    workerSelect.value = singleWorker.id;
                    summaryWorkerName.textContent = singleWorker.name;
                    summaryWorkerName.classList.remove('empty');
                    workerHint.textContent = `Especialista asignado: ${singleWorker.name}`;
                } else if (foundSelected) {
                    workerHint.textContent = `Mostrando ${matchingWorkers.length} especialista(s) disponibles para este servicio.`;
                } else {
                    defaultOpt.selected = true;
                    summaryWorkerName.textContent = 'Selecciona un trabajador';
                    summaryWorkerName.classList.add('empty');
                    workerHint.textContent = `Mostrando ${matchingWorkers.length} especialistas. Por favor selecciona a tu especialista preferido.`;
                }
            } else {
                // NO hay especialistas asignados a este servicio en el sistema
                workerSelect.disabled = true;
                defaultOpt.textContent = 'Sin especialistas para este servicio';
                defaultOpt.selected = true;
                workerHint.textContent = 'Aviso: Actualmente no hay especialistas asignados a este servicio en el estudio.';
                summaryWorkerName.textContent = 'Sin especialistas disponibles';
                summaryWorkerName.classList.add('empty');
            }
        }

        /**
         * Evento al cambiar el servicio (se dispara al hacer clic en las cards)
         */
        if (serviceSelect) {
            serviceSelect.addEventListener('change', function () {
                if (isSyncing) return;
                isSyncing = true;

                const val = serviceSelect.value;
                const sObj = servicesData.find(s => s.id === parseInt(val, 10));

                if (sObj) {
                    // 1. Actualizar el bloque fijo de servicio seleccionado en el formulario
                    selectedServiceEmpty.style.display = 'none';
                    selectedServiceActive.style.display = 'flex';
                    selectedServiceDisplay.classList.add('has-service');

                    const isEstetica = (sObj.category || '').toLowerCase() === 'estetica';
                    selectedServiceBadge.textContent = isEstetica ? 'Estética' : 'Barbería';
                    selectedServiceBadge.className = 'selected-service-badge ' + (isEstetica ? 'estetica' : 'barberia');
                    selectedServiceTitle.textContent = sObj.name;
                    selectedServicePrice.textContent = `$${sObj.price}`;
                    selectedServiceDuration.textContent = `${sObj.duration} min`;

                    // 2. Actualizar el resumen
                    summaryServiceName.textContent = sObj.name;
                    summaryServiceName.classList.remove('empty');

                    summaryCategoryName.textContent = isEstetica ? 'Estética' : 'Barbería';
                    summaryCategoryName.classList.remove('empty');

                    summaryPriceVal.textContent = `$${sObj.price}`;
                    summaryDurationVal.textContent = `${sObj.duration} min`;

                    // 3. Verificar si el trabajador seleccionado previamente realiza el nuevo servicio
                    const currentWorkerVal = workerSelect.value;
                    if (currentWorkerVal) {
                        const wId = parseInt(currentWorkerVal, 10);
                        const doesWorkerPerform = sObj.worker_ids && sObj.worker_ids.includes(wId);
                        if (!doesWorkerPerform) {
                            workerSelect.value = '';
                            summaryWorkerName.textContent = 'Selecciona un trabajador';
                            summaryWorkerName.classList.add('empty');
                        }
                    } else {
                        summaryWorkerName.textContent = 'Selecciona un trabajador';
                        summaryWorkerName.classList.add('empty');
                    }

                    // 4. Habilitar y poblar especialistas para este servicio
                    renderWorkerOptions(val);

                    // 5. Marcar tarjeta activa en el catálogo
                    updateSelectedCardVisual(val);
                } else {
                    // Deselección del servicio
                    selectedServiceEmpty.style.display = 'flex';
                    selectedServiceActive.style.display = 'none';
                    selectedServiceDisplay.classList.remove('has-service');

                    summaryServiceName.textContent = 'Selecciona un servicio';
                    summaryServiceName.classList.add('empty');
                    summaryCategoryName.textContent = '—';
                    summaryCategoryName.classList.add('empty');
                    summaryPriceVal.textContent = '$0.00';
                    summaryDurationVal.textContent = '—';

                    // Bloquear trabajador y resetear a vacío
                    workerSelect.disabled = true;
                    workerSelect.value = '';
                    summaryWorkerName.textContent = 'Selecciona un servicio primero';
                    summaryWorkerName.classList.add('empty');
                    workerHint.textContent = 'Primero selecciona un servicio en el catálogo para ver los especialistas disponibles.';

                    updateSelectedCardVisual(null);
                }

                // Actualizar consulta de disponibilidad
                fetchAvailability();

                isSyncing = false;
            });
        }

        /**
         * Evento al cambiar de trabajador
         */
        if (workerSelect) {
            workerSelect.addEventListener('change', function () {
                if (isSyncing) return;
                isSyncing = true;

                const val = workerSelect.value;
                const wObj = workersData.find(w => w.id === parseInt(val, 10));

                if (wObj) {
                    summaryWorkerName.textContent = wObj.name;
                    summaryWorkerName.classList.remove('empty');
                } else {
                    summaryWorkerName.textContent = 'Selecciona un trabajador';
                    summaryWorkerName.classList.add('empty');
                    workerHint.textContent = 'Selecciona al barbero o estilista de tu preferencia.';
                }

                fetchAvailability();

                isSyncing = false;
            });
        }

        /**
         * Evento para limpiar selección
         */
        if (btnResetFilters) {
            btnResetFilters.addEventListener('click', function () {
                isSyncing = true;

                serviceSelect.value = '';
                workerSelect.value = '';
                workerSelect.disabled = true;
                if (dateSelect) dateSelect.value = '';

                selectedServiceEmpty.style.display = 'flex';
                selectedServiceActive.style.display = 'none';
                selectedServiceDisplay.classList.remove('has-service');

                updateSelectedCardVisual(null);

                summaryServiceName.textContent = 'Selecciona un servicio';
                summaryServiceName.classList.add('empty');
                summaryCategoryName.textContent = '—';
                summaryCategoryName.classList.add('empty');
                summaryWorkerName.textContent = 'Selecciona un servicio primero';
                summaryWorkerName.classList.add('empty');
                summaryDateVal.textContent = 'Selecciona una fecha';
                summaryDateVal.classList.add('empty');
                summaryTimeVal.textContent = 'Selecciona un horario';
                summaryTimeVal.classList.add('empty');
                summaryPriceVal.textContent = '$0.00';
                summaryDurationVal.textContent = '—';

                workerHint.textContent = 'Primero selecciona un servicio en el catálogo para ver los especialistas disponibles.';

                fetchAvailability();

                isSyncing = false;
            });
        }

        /**
         * Evento al cambiar la fecha
         */
        if (dateSelect) {
            dateSelect.addEventListener('change', function () {
                if (dateSelect.value) {
                    summaryDateVal.textContent = dateSelect.value;
                    summaryDateVal.classList.remove('empty');
                } else {
                    summaryDateVal.textContent = 'Selecciona una fecha';
                    summaryDateVal.classList.add('empty');
                }

                fetchAvailability();
            });
        }

        /**
         * Evento al cambiar el horario
         */
        if (timeSelect) {
            timeSelect.addEventListener('change', function () {
                if (timeSelect.value) {
                    summaryTimeVal.textContent = timeSelect.options[timeSelect.selectedIndex].text;
                    summaryTimeVal.classList.remove('empty');
                } else {
                    summaryTimeVal.textContent = 'Selecciona un horario';
                    summaryTimeVal.classList.add('empty');
                }
            });
        }

        // =========================================================
        // CONSULTA DINÁMICA DE DISPONIBILIDAD DE HORARIOS (Subtarea 3)
        // =========================================================
        const timeHint = document.getElementById('timeHint');
        const unavailableSlotModal = document.getElementById('unavailableSlotModal');
        const unavailableModalMessage = document.getElementById('unavailableModalMessage');
        const btnCloseUnavailableModal = document.getElementById('btnCloseUnavailableModal');
        const btnContinue = document.getElementById('btnContinue');

        /**
         * Consulta en el servidor los horarios disponibles según servicio, trabajador y fecha.
         */
        async function fetchAvailability() {
            if (!timeSelect) return;

            const serviceId = serviceSelect ? serviceSelect.value : '';
            const workerId = workerSelect ? workerSelect.value : '';
            const dateVal = dateSelect ? dateSelect.value : '';

            // 1. Si falta alguno de los datos obligatorios, mostrar mensaje orientativo
            if (!serviceId || !workerId || !dateVal) {
                timeSelect.disabled = true;
                const missing = [];
                if (!serviceId) missing.push('un servicio');
                if (!workerId) missing.push('un especialista');
                if (!dateVal) missing.push('una fecha');

                const pendingText = `Selecciona ${missing.join(', ')} para consultar horarios disponibles.`;
                timeSelect.innerHTML = `<option value="" disabled selected>${pendingText}</option>`;
                if (timeHint) {
                    timeHint.textContent = pendingText;
                    timeHint.className = 'form-hint';
                }
                summaryTimeVal.textContent = 'Selecciona un horario';
                summaryTimeVal.classList.add('empty');
                return;
            }

            // 2. Estado de carga mientras se consulta al backend
            timeSelect.disabled = true;
            timeSelect.innerHTML = '<option value="" disabled selected>Consultando horarios disponibles...</option>';
            if (timeHint) {
                timeHint.textContent = 'Consultando disponibilidad en tiempo real...';
                timeHint.className = 'form-hint';
            }

            try {
                const url = `{{ route('cliente.agendamiento.disponibilidad') }}?service_id=${encodeURIComponent(serviceId)}&worker_id=${encodeURIComponent(workerId)}&date=${encodeURIComponent(dateVal)}`;
                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (response.ok && data.success && Array.isArray(data.horarios) && data.horarios.length > 0) {
                    timeSelect.innerHTML = '<option value="" disabled selected>Selecciona un horario</option>';
                    data.horarios.forEach(h => {
                        const opt = document.createElement('option');
                        opt.value = h;
                        opt.textContent = `${h} hrs`;
                        timeSelect.appendChild(opt);
                    });
                    timeSelect.disabled = false;

                    if (timeHint) {
                        let hintMsg = `${data.horarios.length} horario(s) disponible(s) para el ${dateVal}. Duración estimada: ${data.duracion_minutos} min.`;
                        if (data.aviso_cierre) {
                            hintMsg += ` • ${data.aviso_cierre}`;
                        }
                        timeHint.textContent = hintMsg;
                        timeHint.className = 'form-hint text-success';
                    }
                    summaryTimeVal.textContent = 'Selecciona un horario';
                    summaryTimeVal.classList.add('empty');
                } else {
                    // Si no existen horarios disponibles, mensaje claro sin inventar horarios ficticios
                    timeSelect.disabled = true;
                    timeSelect.innerHTML = '<option value="" disabled selected>Sin horarios disponibles</option>';
                    let msg = (data && data.mensaje) ? data.mensaje : 'No hay horarios disponibles para esta fecha. Por favor selecciona otro día o especialista.';
                    if (data && data.aviso_cierre) {
                        msg += ` • ${data.aviso_cierre}`;
                    }
                    if (timeHint) {
                        timeHint.textContent = msg;
                        timeHint.className = 'form-hint text-warning';
                    }
                    summaryTimeVal.textContent = 'Sin disponibilidad';
                    summaryTimeVal.classList.add('empty');
                }
            } catch (err) {
                timeSelect.disabled = true;
                timeSelect.innerHTML = '<option value="" disabled selected>Error al consultar horarios</option>';
                if (timeHint) {
                    timeHint.textContent = 'No se pudo consultar la disponibilidad en el servidor.';
                    timeHint.className = 'form-hint text-warning';
                }
            }
        }

        // =========================================================
        // VALIDACIÓN DE HORARIO Y AVISO AL CONTINUAR (Subtarea 3)
        // =========================================================

        /**
         * Muestra la ventana modal azul con icono blanco de alerta cuando un horario deja de estar disponible.
         */
        function mostrarAvisoHorarioNoDisponible(mensaje) {
            if (unavailableModalMessage) {
                unavailableModalMessage.textContent = mensaje || 'El horario seleccionado ya no está disponible. Por favor selecciona otro horario para continuar.';
            }
            if (unavailableSlotModal) {
                unavailableSlotModal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
        }

        // Acción para cerrar el aviso azul y regresar a la selección de horarios
        if (btnCloseUnavailableModal) {
            btnCloseUnavailableModal.addEventListener('click', function () {
                if (unavailableSlotModal) {
                    unavailableSlotModal.style.display = 'none';
                    document.body.style.overflow = 'auto';
                }
                if (timeSelect) {
                    timeSelect.value = '';
                }
                summaryTimeVal.textContent = 'Selecciona un horario';
                summaryTimeVal.classList.add('empty');

                // Vuelve a consultar la disponibilidad fresca del día
                fetchAvailability();
                if (timeSelect) timeSelect.focus();
            });
        }

        // Cerrar modal al hacer clic en el fondo oscuro
        if (unavailableSlotModal) {
            unavailableSlotModal.addEventListener('click', function (e) {
                if (e.target === unavailableSlotModal && btnCloseUnavailableModal) {
                    btnCloseUnavailableModal.click();
                }
            });
        }

        // Cerrar modal con la tecla Escape
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && unavailableSlotModal && unavailableSlotModal.style.display === 'flex') {
                if (btnCloseUnavailableModal) btnCloseUnavailableModal.click();
            }
        });

        // Registro y confirmación de la cita en el servidor
        if (btnContinue) {
            btnContinue.addEventListener('click', async function () {
                const serviceId = serviceSelect ? serviceSelect.value : '';
                const workerId = workerSelect ? workerSelect.value : '';
                const dateVal = dateSelect ? dateSelect.value : '';
                const timeVal = timeSelect ? timeSelect.value : '';

                if (!serviceId) {
                    mostrarAvisoCampoFaltante('Por favor selecciona primero un servicio del catálogo.');
                    return;
                }
                if (!workerId) {
                    mostrarAvisoCampoFaltante('Por favor selecciona a un especialista.');
                    return;
                }
                if (!dateVal) {
                    mostrarAvisoCampoFaltante('Por favor selecciona la fecha de tu cita.');
                    return;
                }
                if (!timeVal) {
                    mostrarAvisoCampoFaltante('Por favor selecciona un horario de atención disponible.');
                    return;
                }

                const clienteNombre = @json(Auth::user()->name . ' ' . (Auth::user()->apellidos ?? ''));
                const serviceObj = servicesData.find(s => String(s.id) === String(serviceId));
                const workerObj = workersData.find(w => String(w.id) === String(workerId));

                // Formateo de fecha amigable en español
                let fechaFormateada = dateVal;
                try {
                    const [y, m, d] = dateVal.split('-');
                    const fechaObj = new Date(y, m - 1, d);
                    const opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                    fechaFormateada = fechaObj.toLocaleDateString('es-MX', opciones);
                    fechaFormateada = fechaFormateada.charAt(0).toUpperCase() + fechaFormateada.slice(1);
                } catch (e) {
                    fechaFormateada = dateVal;
                }

                // Modal estructurado de confirmación de la cita con SweetAlert2 o fallback seguro
                let confirmacionAceptada = false;
                if (typeof Swal !== 'undefined') {
                    const confirmResult = await Swal.fire({
                        title: '¿Deseas Confirmar tu Cita?',
                        html: `
                            <div style="font-size: 13px; color: #94a3b8; margin-bottom: 12px;">
                                Por favor revisa cuidadosamente el resumen de tu reservación:
                            </div>
                            <div style="text-align: left; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 14px; font-size: 13px; line-height: 1.5;">
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 7px 0; border-bottom: 1px solid rgba(255,255,255,0.06);">
                                    <span style="color: #94a3b8; font-weight: 500; display: inline-flex; align-items: center; gap: 7px;">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #60a5fa; flex-shrink: 0;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                        Cliente:
                                    </span>
                                    <span style="color: #ffffff; font-weight: 700; text-align: right; max-width: 65%; word-break: break-word;">${escapeHtml(clienteNombre)}</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 7px 0; border-bottom: 1px solid rgba(255,255,255,0.06);">
                                    <span style="color: #94a3b8; font-weight: 500; display: inline-flex; align-items: center; gap: 7px;">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #c084fc; flex-shrink: 0;"><circle cx="6" cy="6" r="3"></circle><circle cx="6" cy="18" r="3"></circle><line x1="20" y1="4" x2="8.12" y2="15.88"></line><line x1="14.47" y1="14.48" x2="20" y2="20"></line><line x1="8.12" y1="8.12" x2="12" y2="12"></line></svg>
                                        Servicio:
                                    </span>
                                    <span style="color: #ffffff; font-weight: 700; text-align: right; max-width: 65%;">${escapeHtml(serviceObj ? serviceObj.name : 'Servicio')}</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 7px 0; border-bottom: 1px solid rgba(255,255,255,0.06);">
                                    <span style="color: #94a3b8; font-weight: 500; display: inline-flex; align-items: center; gap: 7px;">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #38bdf8; flex-shrink: 0;"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><polyline points="17 11 19 13 23 9"></polyline></svg>
                                        Especialista:
                                    </span>
                                    <span style="color: #ffffff; font-weight: 700; text-align: right; max-width: 65%;">${escapeHtml(workerObj ? workerObj.name : 'Especialista')}</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 7px 0; border-bottom: 1px solid rgba(255,255,255,0.06);">
                                    <span style="color: #94a3b8; font-weight: 500; display: inline-flex; align-items: center; gap: 7px;">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #fbbf24; flex-shrink: 0;"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                        Fecha:
                                    </span>
                                    <span style="color: #ffffff; font-weight: 700; text-align: right;">${escapeHtml(fechaFormateada)}</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 7px 0; border-bottom: 1px solid rgba(255,255,255,0.06);">
                                    <span style="color: #94a3b8; font-weight: 500; display: inline-flex; align-items: center; gap: 7px;">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #38bdf8; flex-shrink: 0;"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                        Horario:
                                    </span>
                                    <span style="color: #38bdf8; font-weight: 700; text-align: right;">${escapeHtml(timeVal)} hrs (${serviceObj ? serviceObj.duration : 30} min)</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 9px 0 2px 0;">
                                    <span style="color: #94a3b8; font-weight: 600; font-size: 13.5px; display: inline-flex; align-items: center; gap: 7px;">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #10b981; flex-shrink: 0;"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                                        Total a pagar:
                                    </span>
                                    <span style="color: #10b981; font-weight: 800; font-size: 16px; text-align: right;">$${serviceObj ? serviceObj.price : '0.00'} MXN</span>
                                </div>
                            </div>
                        `,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Confirmar Cita',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#0055ff',
                        cancelButtonColor: '#64748b',
                        background: '#101d36',
                        color: '#ffffff',
                        reverseButtons: true,
                        focusCancel: false
                    });
                    confirmacionAceptada = !!(confirmResult && confirmResult.isConfirmed);
                } else {
                    confirmacionAceptada = window.confirm(`¿Deseas confirmar tu cita para ${serviceObj ? serviceObj.name : 'el servicio'} con ${workerObj ? workerObj.name : 'el especialista'} el día ${fechaFormateada} a las ${timeVal} hrs?`);
                }

                // Si el usuario cancela la confirmación, la cita NO se registra
                if (!confirmacionAceptada) {
                    return;
                }

                // Evitar registros duplicados bloqueando el botón mientras se procesa
                btnContinue.disabled = true;
                const originalHtml = btnContinue.innerHTML;
                btnContinue.innerHTML = `
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="spin-animation" style="animation: spin 1s linear infinite;">
                        <circle cx="12" cy="12" r="10" stroke-opacity="0.25"></circle>
                        <path d="M12 2a10 10 0 0 1 10 10"></path>
                    </svg>
                    <span>Confirmando cita...</span>
                `;

                try {
                    const response = await fetch(`{{ route('cliente.agendamiento.confirmar') }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            service_id: serviceId,
                            worker_id: workerId,
                            date: dateVal,
                            time: timeVal
                        })
                    });

                    const data = await response.json();
                    btnContinue.disabled = false;
                    btnContinue.innerHTML = originalHtml;

                    if (response.ok && data.success) {
                        // 1. Confirmar únicamente cuando el registro en BD fue exitoso con SweetAlert2
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Cita Confirmada!',
                                text: data.mensaje || 'Tu cita ha sido registrada exitosamente.',
                                background: '#101d36',
                                color: '#ffffff',
                                confirmButtonColor: '#0055ff',
                                confirmButtonText: 'Aceptar',
                                timer: 3500,
                                timerProgressBar: true
                            });
                        }

                        // 2. Limpiar selector de horario y consultar disponibilidad fresca (el turno recién ocupado ya no aparecerá)
                        if (timeSelect) {
                            timeSelect.value = '';
                        }
                        summaryTimeVal.textContent = 'Selecciona un horario';
                        summaryTimeVal.classList.add('empty');
                        fetchAvailability();
                    } else if (data.valido === false || response.status === 422) {
                        // 3. Horario ya no disponible: ventana azul con icono blanco
                        mostrarAvisoHorarioNoDisponible(data.mensaje || 'El horario seleccionado ya no está disponible. Por favor selecciona otro horario.');
                    } else {
                        // Error de validación o del servidor: no mostrar confirmación falsa
                        mostrarAvisoCampoFaltante(data.mensaje || 'No fue posible registrar la cita. Por favor intenta de nuevo.');
                    }
                } catch (err) {
                    btnContinue.disabled = false;
                    btnContinue.innerHTML = originalHtml;
                    mostrarAvisoCampoFaltante('Error de conexión al procesar la cita. Por favor intenta nuevamente.');
                }
            });
        }

        function mostrarAvisoCampoFaltante(mensaje) {
            const toast = document.createElement('div');
            toast.style.cssText = 'position: fixed; bottom: 24px; right: 24px; background: #1e293b; border: 1px solid rgba(239,68,68,0.5); color: #fca5a5; padding: 12px 18px; border-radius: 10px; font-size: 13px; font-weight: 600; box-shadow: 0 10px 25px rgba(0,0,0,0.4); z-index: 10000; display: flex; align-items: center; gap: 8px;';
            toast.innerHTML = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg><span>${mensaje}</span>`;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3200);
        }
    });
</script>
@endpush
