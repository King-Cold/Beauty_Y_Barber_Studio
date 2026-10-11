@extends('recepcion.layout')

@section('title', 'Beauty & Barber Studio - Agendamiento de Citas')

@push('styles')
<style>
    /* ===================================================
       ESTILOS DEL MÓDULO DE AGENDAMIENTO PARA RECEPCIÓN
       =================================================== */
    .main-content {
        padding: 16px 24px 30px !important;
    }

    .page-header {
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
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

    /* Grid principal: Catálogo (1.35fr) y Formulario/Resumen (1fr) */
    .booking-grid {
        display: grid;
        grid-template-columns: 1.35fr 1fr;
        gap: 20px;
        align-items: start;
    }

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

    /* Pestañas de categoría */
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

    /* Contenedor scrolleable del catálogo */
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

    .services-cards-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }

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

    .service-card.is-selected {
        background: rgba(0, 85, 255, 0.08);
        border-color: var(--barber-blue);
        box-shadow: 0 0 0 2px var(--barber-blue), 0 8px 22px rgba(0, 85, 255, 0.35);
        transform: translateY(-2px);
    }

    .service-card-media {
        height: 140px;
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
        gap: 6px;
        text-align: center;
        z-index: 1;
    }

    .empty-media-icon {
        width: 38px;
        height: 38px;
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

    .service-card-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 10px;
        border-top: 1px solid var(--border-subtle);
        margin-top: auto;
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

    /* Panel derecho */
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
        width: 14px;
        height: 14px;
        color: var(--barber-blue-light);
    }

    .form-hint {
        font-size: 10.5px;
        color: var(--text-muted);
        margin-top: 3px;
        line-height: 1.25;
    }

    .form-hint.text-success { color: #10b981 !important; }
    .form-hint.text-warning { color: #f59e0b !important; }

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

    select.form-control {
        appearance: none;
        padding-right: 32px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 10px center;
        background-size: 13px;
        cursor: pointer;
    }

    select.form-control:disabled {
        opacity: 0.55;
        cursor: not-allowed;
    }

    select.form-control option {
        background-color: var(--bg-header);
        color: var(--text-white);
        padding: 6px 8px;
    }

    .form-row-two {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }

    /* Caja informativa fija del Servicio Seleccionado */
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

    .selected-service-duration {
        color: var(--text-muted);
        font-weight: 500;
    }

    /* Resumen */
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
        border: none;
        font-family: inherit;
    }

    .btn-secondary {
        background: rgba(255, 255, 255, 0.05);
        color: var(--text-main);
        border: 1px solid var(--border-subtle);
    }

    .btn-secondary:hover {
        background: rgba(255, 255, 255, 0.1);
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

    @media (max-width: 992px) {
        .booking-grid {
            grid-template-columns: 1fr;
        }
        .booking-sidebar-card {
            position: static;
        }
        .catalog-scroll-container {
            max-height: none;
        }
    }
</style>
@endpush

@section('content')
    <!-- ENCABEZADO DE LA SECCIÓN -->
    <header class="page-header">
        <div>
            <h1 class="page-title">Agendamiento de Citas</h1>
            <p class="page-subtitle">Registra una nueva cita para un cliente seleccionando el cliente, servicio, fecha y especialista disponible.</p>
        </div>
        <a href="{{ route('recepcion.citas') }}" class="btn btn-secondary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
            <span>Ver listado de citas</span>
        </a>
    </header>

    <!-- GRID PRINCIPAL DE RESERVA -->
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
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="6" cy="6" r="3"></circle>
                        <circle cx="6" cy="18" r="3"></circle>
                        <line x1="20" y1="4" x2="8.12" y2="15.88"></line>
                        <line x1="14.47" y1="14.48" x2="20" y2="20"></line>
                        <line x1="8.12" y1="8.12" x2="12" y2="12"></line>
                    </svg>
                    <span>Barbería</span>
                    <span class="catalog-badge-count">{{ isset($serviciosBarberia) ? $serviciosBarberia->count() : 0 }}</span>
                </button>

                <button type="button" class="catalog-filter-btn" data-filter="estetica" role="tab" aria-selected="false">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                    </svg>
                    <span>Estética</span>
                    <span class="catalog-badge-count">{{ isset($serviciosEstetica) ? $serviciosEstetica->count() : 0 }}</span>
                </button>
            </div>

            @php
                $listBarberia = isset($serviciosBarberia) ? $serviciosBarberia : collect();
                $listEstetica = isset($serviciosEstetica) ? $serviciosEstetica : collect();
            @endphp

            <!-- Contenedor con scroll interno -->
            <div class="catalog-scroll-container">

                <!-- 1.1 CATEGORÍA BARBERÍA -->
                <div class="category-block" id="categoryBlockBarberia" data-category="barberia">
                    <div class="category-block-header barberia">
                        <div class="cat-header-info">
                            <div class="cat-icon-badge barberia" aria-hidden="true">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
                                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                                <span>Seleccionado</span>
                                            </div>
                                        </div>
                                    @else
                                        <div class="service-card-media empty-media barberia">
                                            <div class="empty-media-inner">
                                                <div class="empty-media-icon">
                                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
                                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
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
                                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <circle cx="12" cy="12" r="10"></circle>
                                                        <polyline points="12 6 12 12 16 14"></polyline>
                                                    </svg>
                                                    <span>{{ $servicio->duration_minutes }} min</span>
                                                </div>
                                            </div>

                                            <button type="button" class="btn-card-select" tabindex="-1">
                                                <span class="btn-card-select-text">Elegir</span>
                                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                    <polyline points="9 18 15 12 9 6"></polyline>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div style="padding: 20px; text-align: center; color: var(--text-muted); font-size: 12px;">
                            <p>No hay servicios de barbería disponibles en este momento.</p>
                        </div>
                    @endif
                </div>

                <!-- 1.2 CATEGORÍA ESTÉTICA -->
                <div class="category-block" id="categoryBlockEstetica" data-category="estetica">
                    <div class="category-block-header estetica">
                        <div class="cat-header-info">
                            <div class="cat-icon-badge estetica" aria-hidden="true">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="category-block-title">Estética</h3>
                                <p class="category-block-desc">Cortes unisex, diseño de imagen, tintes, balayage y peinados.</p>
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
                                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                                <span>Seleccionado</span>
                                            </div>
                                        </div>
                                    @else
                                        <div class="service-card-media empty-media estetica">
                                            <div class="empty-media-inner">
                                                <div class="empty-media-icon">
                                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                                                    </svg>
                                                </div>
                                                <span class="empty-media-text">Servicio de Estética</span>
                                            </div>
                                            <span class="service-category-tag estetica">Estética</span>
                                            <div class="service-card-selected-badge" aria-hidden="true">
                                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
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
                                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <circle cx="12" cy="12" r="10"></circle>
                                                        <polyline points="12 6 12 12 16 14"></polyline>
                                                    </svg>
                                                    <span>{{ $servicio->duration_minutes }} min</span>
                                                </div>
                                            </div>

                                            <button type="button" class="btn-card-select" tabindex="-1">
                                                <span class="btn-card-select-text">Elegir</span>
                                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                    <polyline points="9 18 15 12 9 6"></polyline>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div style="padding: 20px; text-align: center; color: var(--text-muted); font-size: 12px;">
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
                        <p class="section-desc">Selecciona el cliente, servicio, especialista y horario.</p>
                    </div>
                </div>
            </div>

            <!-- Formulario interactivo -->
            <form id="appointmentForm" onsubmit="event.preventDefault();">
                
                <!-- 1. SELECCIÓN DE CLIENTE (EXCLUSIVO DE RECEPCIÓN) -->
                <div class="form-group">
                    <label for="clientSelect" class="form-label">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <span>Cliente</span>
                    </label>
                    <select id="clientSelect" class="form-control" name="client_id" required>
                        <option value="" selected disabled>Selecciona un cliente</option>
                        @if(isset($clientes) && $clientes->count() > 0)
                            @foreach($clientes as $cliente)
                                <option 
                                    value="{{ $cliente->id }}"
                                    data-name="{{ $cliente->name }} {{ $cliente->apellidos ?? '' }}"
                                    data-email="{{ $cliente->email }}"
                                    data-phone="{{ $cliente->telefono ?? '' }}"
                                    {{ (request('client_id') == $cliente->id) ? 'selected' : '' }}
                                >
                                    {{ $cliente->name }} {{ $cliente->apellidos ?? '' }} ({{ $cliente->email }})
                                </option>
                            @endforeach
                        @endif
                    </select>
                    <p class="form-hint" id="clientHint">Elige el cliente para quien se registrará la reserva.</p>
                </div>

                <!-- 2. SERVICIO SELECCIONADO (FIJO - DESDE CARDS O SELECT TÉCNICO) -->
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
                            <span class="selected-service-status-tag">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                <span>Fijado</span>
                            </span>
                        </div>
                    </div>

                    <!-- Input técnico accesible para sincronización -->
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

                <!-- 3. SELECCIÓN DE TRABAJADOR / ESPECIALISTA -->
                <div class="form-group">
                    <label for="workerSelect" class="form-label">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="2" y="7" width="20" height="14" rx="2"></rect>
                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
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

                <!-- 4. SELECCIÓN DE FECHA Y HORARIO -->
                <div class="form-row-two">
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

                    <div class="form-group">
                        <label for="timeSelect" class="form-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            <span>Horario de atención</span>
                        </label>
                        <select id="timeSelect" class="form-control" name="appointment_time" disabled>
                            <option value="" disabled selected>Selecciona servicio y fecha primero</option>
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
                        <span>Resumen de la Cita</span>
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
                            <span class="summary-item-label">Cliente</span>
                            <span class="summary-item-val empty" id="summaryClientName">Selecciona un cliente</span>
                        </div>
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
                    <a href="{{ route('recepcion.citas') }}" class="btn btn-secondary">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                        <span>Regresar</span>
                    </a>

                    <button type="button" class="btn btn-primary" id="btnContinue">
                        <span>Confirmar Cita</span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </button>
                </div>
            </form>
        </aside>
    </div>

    <!-- MODAL DE AVISO: HORARIO YA NO DISPONIBLE (VENTANA AZUL CON ICONO BLANCO) -->
    <div id="unavailableSlotModal" style="display: none; position: fixed; inset: 0; background: rgba(5,10,21,0.8); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(6px); padding: 16px;">
        <div style="background: linear-gradient(135deg, #091a3e 0%, #0d2868 50%, #11388f 100%); border: 1px solid rgba(59, 130, 246, 0.45); border-radius: 16px; width: 100%; max-width: 440px; padding: 28px 24px; text-align: center; box-shadow: 0 25px 50px -12px rgba(0, 50, 180, 0.5); color: #ffffff;">
            <div style="width: 58px; height: 58px; border-radius: 50%; background: rgba(255, 255, 255, 0.12); border: 2px solid #ffffff; margin: 0 auto 16px; display: flex; align-items: center; justify-content: center;">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
            </div>
            <h3 style="font-size: 1.25rem; font-weight: 800; color: #ffffff; margin-bottom: 8px;">Horario No Disponible</h3>
            <p id="unavailableModalMessage" style="font-size: 0.92rem; color: rgba(255, 255, 255, 0.9); line-height: 1.5; margin-bottom: 22px;">
                El horario seleccionado ya no está disponible. Por favor selecciona otro horario para continuar con la cita.
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

        // Elementos DOM
        const clientSelect = document.getElementById('clientSelect');
        const serviceSelect = document.getElementById('serviceSelect');
        const workerSelect = document.getElementById('workerSelect');
        const dateSelect = document.getElementById('dateSelect');
        const timeSelect = document.getElementById('timeSelect');
        const btnResetFilters = document.getElementById('btnResetFilters');
        const workerHint = document.getElementById('workerHint');
        const timeHint = document.getElementById('timeHint');
        const btnContinue = document.getElementById('btnContinue');

        // Bloque visual de servicio seleccionado
        const selectedServiceDisplay = document.getElementById('selectedServiceDisplay');
        const selectedServiceEmpty = document.getElementById('selectedServiceEmpty');
        const selectedServiceActive = document.getElementById('selectedServiceActive');
        const selectedServiceBadge = document.getElementById('selectedServiceBadge');
        const selectedServiceTitle = document.getElementById('selectedServiceTitle');
        const selectedServicePrice = document.getElementById('selectedServicePrice');
        const selectedServiceDuration = document.getElementById('selectedServiceDuration');

        // Resumen
        const summaryClientName = document.getElementById('summaryClientName');
        const summaryServiceName = document.getElementById('summaryServiceName');
        const summaryCategoryName = document.getElementById('summaryCategoryName');
        const summaryWorkerName = document.getElementById('summaryWorkerName');
        const summaryDateVal = document.getElementById('summaryDateVal');
        const summaryTimeVal = document.getElementById('summaryTimeVal');
        const summaryPriceVal = document.getElementById('summaryPriceVal');
        const summaryDurationVal = document.getElementById('summaryDurationVal');

        // Catálogo
        const serviceCards = document.querySelectorAll('.service-card');
        const catalogFilterBtns = document.querySelectorAll('.catalog-filter-btn');
        const categoryBlockBarberia = document.getElementById('categoryBlockBarberia');
        const categoryBlockEstetica = document.getElementById('categoryBlockEstetica');

        // Modal de aviso
        const unavailableSlotModal = document.getElementById('unavailableSlotModal');
        const unavailableModalMessage = document.getElementById('unavailableModalMessage');
        const btnCloseUnavailableModal = document.getElementById('btnCloseUnavailableModal');

        // Inicialización de cliente en resumen si viene preseleccionado
        function updateClientSummary() {
            if (clientSelect && clientSelect.value) {
                const opt = clientSelect.selectedOptions[0];
                summaryClientName.textContent = opt ? (opt.getAttribute('data-name') || opt.text) : 'Cliente #' + clientSelect.value;
                summaryClientName.classList.remove('empty');
            } else {
                summaryClientName.textContent = 'Selecciona un cliente';
                summaryClientName.classList.add('empty');
            }
        }
        if (clientSelect) {
            clientSelect.addEventListener('change', updateClientSummary);
            updateClientSummary();
        }

        // Estado inicial de trabajador
        workerSelect.disabled = true;
        workerSelect.value = '';

        // Filtrado por pestañas del catálogo
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

        function renderWorkerOptions(serviceId) {
            workerSelect.innerHTML = '<option value="" selected>Selecciona un trabajador</option>';
            if (!serviceId) {
                workerSelect.disabled = true;
                if (workerHint) {
                    workerHint.textContent = 'Primero selecciona un servicio en el catálogo para ver los especialistas disponibles.';
                    workerHint.className = 'form-hint';
                }
                summaryWorkerName.textContent = 'Selecciona un servicio primero';
                summaryWorkerName.classList.add('empty');
                return;
            }

            const currentService = servicesData.find(s => String(s.id) === String(serviceId));
            const eligibleWorkers = workersData.filter(w => {
                if (!currentService || !currentService.worker_ids || currentService.worker_ids.length === 0) {
                    return true;
                }
                return currentService.worker_ids.map(String).includes(String(w.id));
            });

            if (eligibleWorkers.length > 0) {
                eligibleWorkers.forEach(w => {
                    const opt = document.createElement('option');
                    opt.value = w.id;
                    opt.textContent = w.name;
                    opt.setAttribute('data-name', w.name);
                    workerSelect.appendChild(opt);
                });
                workerSelect.disabled = false;
                if (workerHint) {
                    workerHint.textContent = `${eligibleWorkers.length} especialista(s) disponible(s) para este servicio.`;
                    workerHint.className = 'form-hint text-success';
                }
            } else {
                workerSelect.disabled = true;
                if (workerHint) {
                    workerHint.textContent = 'No hay especialistas activos asignados a este servicio en este momento.';
                    workerHint.className = 'form-hint text-warning';
                }
            }
        }

        serviceSelect.addEventListener('change', function () {
            const sId = this.value;
            const currentService = servicesData.find(s => String(s.id) === String(sId));

            if (currentService) {
                selectedServiceTitle.textContent = currentService.name;
                selectedServicePrice.textContent = `$${currentService.price}`;
                selectedServiceDuration.textContent = `${currentService.duration} min`;
                selectedServiceBadge.textContent = currentService.category ? currentService.category.toUpperCase() : 'SERVICIO';

                selectedServiceEmpty.style.display = 'none';
                selectedServiceActive.style.display = 'flex';
                selectedServiceDisplay.classList.add('has-service');

                summaryServiceName.textContent = currentService.name;
                summaryServiceName.classList.remove('empty');
                summaryCategoryName.textContent = currentService.category ? currentService.category.charAt(0).toUpperCase() + currentService.category.slice(1) : 'General';
                summaryCategoryName.classList.remove('empty');
                summaryPriceVal.textContent = `$${currentService.price}`;
                summaryDurationVal.textContent = `${currentService.duration} min`;

                renderWorkerOptions(sId);
                updateSelectedCardVisual(sId);
            } else {
                selectedServiceEmpty.style.display = 'flex';
                selectedServiceActive.style.display = 'none';
                selectedServiceDisplay.classList.remove('has-service');

                summaryServiceName.textContent = 'Selecciona un servicio';
                summaryServiceName.classList.add('empty');
                summaryCategoryName.textContent = '—';
                summaryCategoryName.classList.add('empty');
                summaryPriceVal.textContent = '$0.00';
                summaryDurationVal.textContent = '—';

                renderWorkerOptions('');
                updateSelectedCardVisual('');
            }

            if (timeSelect) {
                timeSelect.disabled = true;
                timeSelect.innerHTML = '<option value="" disabled selected>Selecciona fecha y especialista primero</option>';
            }
            summaryTimeVal.textContent = 'Selecciona un horario';
            summaryTimeVal.classList.add('empty');
            fetchAvailability();
        });

        workerSelect.addEventListener('change', function () {
            if (this.value) {
                const opt = this.selectedOptions[0];
                summaryWorkerName.textContent = opt ? (opt.getAttribute('data-name') || opt.text) : 'Especialista #' + this.value;
                summaryWorkerName.classList.remove('empty');
            } else {
                summaryWorkerName.textContent = 'Selecciona un trabajador';
                summaryWorkerName.classList.add('empty');
            }
            fetchAvailability();
        });

        dateSelect.addEventListener('change', function () {
            if (this.value) {
                const parts = this.value.split('-');
                summaryDateVal.textContent = (parts.length === 3) ? `${parts[2]}/${parts[1]}/${parts[0]}` : this.value;
                summaryDateVal.classList.remove('empty');
            } else {
                summaryDateVal.textContent = 'Selecciona una fecha';
                summaryDateVal.classList.add('empty');
            }
            fetchAvailability();
        });

        timeSelect.addEventListener('change', function () {
            if (this.value) {
                summaryTimeVal.textContent = `${this.value} hrs`;
                summaryTimeVal.classList.remove('empty');
            } else {
                summaryTimeVal.textContent = 'Selecciona un horario';
                summaryTimeVal.classList.add('empty');
            }
        });

        btnResetFilters.addEventListener('click', function () {
            serviceSelect.value = '';
            serviceSelect.dispatchEvent(new Event('change'));
            workerSelect.value = '';
            dateSelect.value = '';
            summaryDateVal.textContent = 'Selecciona una fecha';
            summaryDateVal.classList.add('empty');
            timeSelect.innerHTML = '<option value="" disabled selected>Selecciona servicio, especialista y fecha primero</option>';
            timeSelect.disabled = true;
            summaryTimeVal.textContent = 'Selecciona un horario';
            summaryTimeVal.classList.add('empty');
            if (timeHint) {
                timeHint.textContent = 'Selecciona un servicio, especialista y fecha para consultar horarios disponibles.';
                timeHint.className = 'form-hint';
            }
        });

        // Consulta de disponibilidad asíncrona en el servidor
        async function fetchAvailability() {
            const serviceId = serviceSelect ? serviceSelect.value : '';
            const workerId = workerSelect ? workerSelect.value : '';
            const dateVal = dateSelect ? dateSelect.value : '';

            if (!serviceId || !workerId || !dateVal) {
                timeSelect.disabled = true;
                timeSelect.innerHTML = '<option value="" disabled selected>Selecciona servicio, especialista y fecha primero</option>';
                if (timeHint) {
                    timeHint.textContent = 'Selecciona un servicio, especialista y fecha para consultar horarios disponibles.';
                    timeHint.className = 'form-hint';
                }
                return;
            }

            timeSelect.disabled = true;
            timeSelect.innerHTML = '<option value="" disabled selected>Consultando horarios disponibles...</option>';
            if (timeHint) {
                timeHint.textContent = 'Verificando agenda y turnos con el especialista...';
                timeHint.className = 'form-hint';
            }

            try {
                const url = `{{ route('recepcion.agendamiento.disponibilidad') }}?service_id=${encodeURIComponent(serviceId)}&worker_id=${encodeURIComponent(workerId)}&date=${encodeURIComponent(dateVal)}`;
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
                        if (data.aviso_cierre) hintMsg += ` • ${data.aviso_cierre}`;
                        timeHint.textContent = hintMsg;
                        timeHint.className = 'form-hint text-success';
                    }
                } else {
                    timeSelect.disabled = true;
                    timeSelect.innerHTML = '<option value="" disabled selected>Sin horarios disponibles</option>';
                    let msg = (data && data.mensaje) ? data.mensaje : 'No hay horarios disponibles para esta fecha. Selecciona otro día o especialista.';
                    if (data && data.aviso_cierre) msg += ` • ${data.aviso_cierre}`;
                    if (timeHint) {
                        timeHint.textContent = msg;
                        timeHint.className = 'form-hint text-warning';
                    }
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

        // Modal de aviso horario no disponible
        function mostrarAvisoHorarioNoDisponible(mensaje) {
            if (unavailableModalMessage) {
                unavailableModalMessage.textContent = mensaje || 'El horario seleccionado ya no está disponible. Por favor selecciona otro horario.';
            }
            if (unavailableSlotModal) {
                unavailableSlotModal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
        }

        if (btnCloseUnavailableModal) {
            btnCloseUnavailableModal.addEventListener('click', function () {
                if (unavailableSlotModal) {
                    unavailableSlotModal.style.display = 'none';
                    document.body.style.overflow = 'auto';
                }
                if (timeSelect) timeSelect.value = '';
                summaryTimeVal.textContent = 'Selecciona un horario';
                summaryTimeVal.classList.add('empty');
                fetchAvailability();
            });
        }

        // Envío y confirmación de la cita
        if (btnContinue) {
            btnContinue.addEventListener('click', async function () {
                const clientId = clientSelect ? clientSelect.value : '';
                const serviceId = serviceSelect ? serviceSelect.value : '';
                const workerId = workerSelect ? workerSelect.value : '';
                const dateVal = dateSelect ? dateSelect.value : '';
                const timeVal = timeSelect ? timeSelect.value : '';

                if (!clientId) {
                    mostrarAvisoCampoFaltante('Por favor selecciona un cliente registrado.');
                    if (clientSelect) clientSelect.focus();
                    return;
                }
                if (!serviceId) {
                    mostrarAvisoCampoFaltante('Por favor selecciona un servicio del catálogo.');
                    return;
                }
                if (!workerId) {
                    mostrarAvisoCampoFaltante('Por favor selecciona un especialista.');
                    if (workerSelect) workerSelect.focus();
                    return;
                }
                if (!dateVal) {
                    mostrarAvisoCampoFaltante('Por favor selecciona la fecha de la cita.');
                    if (dateSelect) dateSelect.focus();
                    return;
                }
                if (!timeVal) {
                    mostrarAvisoCampoFaltante('Por favor selecciona un horario de atención disponible.');
                    if (timeSelect) timeSelect.focus();
                    return;
                }

                btnContinue.disabled = true;
                const originalHtml = btnContinue.innerHTML;
                btnContinue.innerHTML = `
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="spin-animation" style="animation: spin 1s linear infinite;">
                        <circle cx="12" cy="12" r="10" stroke-opacity="0.25"></circle>
                        <path d="M12 2a10 10 0 0 1 10 10"></path>
                    </svg>
                    <span>Guardando cita...</span>
                `;

                try {
                    const response = await fetch(`{{ route('recepcion.agendamiento.confirmar') }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            client_id: clientId,
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
                        // Confirmación exitosa únicamente cuando el registro en BD fue exitoso
                        Swal.fire({
                            icon: 'success',
                            title: '¡Cita Registrada!',
                            text: data.mensaje || 'La cita ha sido registrada y confirmada exitosamente.',
                            background: '#0c142b',
                            color: '#ffffff',
                            confirmButtonColor: '#0055ff',
                            confirmButtonText: 'Aceptar'
                        }).then(() => {
                            // Limpiar horario y refrescar disponibilidad
                            if (timeSelect) timeSelect.value = '';
                            summaryTimeVal.textContent = 'Selecciona un horario';
                            summaryTimeVal.classList.add('empty');
                            fetchAvailability();
                        });
                    } else if (data.valido === false || response.status === 422) {
                        mostrarAvisoHorarioNoDisponible(data.mensaje || 'El horario seleccionado ya no está disponible. Por favor selecciona otro horario.');
                    } else {
                        mostrarAvisoCampoFaltante(data.mensaje || 'No fue posible registrar la cita. Por favor revisa los datos.');
                    }
                } catch (err) {
                    btnContinue.disabled = false;
                    btnContinue.innerHTML = originalHtml;
                    mostrarAvisoCampoFaltante('Error de conexión al procesar la cita. Inténtalo nuevamente.');
                }
            });
        }

        function mostrarAvisoCampoFaltante(mensaje) {
            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: mensaje,
                background: '#0c142b',
                color: '#ffffff',
                confirmButtonColor: '#0055ff'
            });
        }
    });
</script>
@endpush
