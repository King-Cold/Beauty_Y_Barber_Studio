@extends('trabajador.layout')

@section('title', 'Beauty & Barber Studio - Panel Principal')

@push('styles')
<style>
    /* ===================================================
       ESTILOS DEL PANEL PRINCIPAL DEL TRABAJADOR
       =================================================== */
    .dashboard-wrapper {
        display: flex;
        flex-direction: column;
        gap: 28px;
    }

    /* BANNER DE BIENVENIDA */
    .welcome-banner {
        background: linear-gradient(135deg, rgba(10, 17, 36, 0.95) 0%, rgba(12, 20, 43, 0.98) 100%);
        border: 1px solid var(--border-subtle);
        border-radius: 18px;
        padding: 36px 40px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
    }

    .welcome-banner::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, var(--barber-blue) 0%, var(--barber-red) 100%);
    }

    .welcome-banner-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--barber-blue-pale);
        color: var(--barber-blue-light);
        border: 1px solid var(--barber-blue-border);
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        margin-bottom: 14px;
    }

    .welcome-banner-badge::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background-color: var(--barber-blue-light);
        display: inline-block;
    }

    .welcome-title {
        font-size: 32px;
        font-weight: 800;
        color: var(--text-white);
        letter-spacing: -0.02em;
        margin-bottom: 10px;
        line-height: 1.2;
    }

    .welcome-subtitle {
        font-size: 15px;
        color: var(--text-muted);
        max-width: 820px;
        line-height: 1.6;
    }

    /* GRID DE TARJETAS DE ESTADÍSTICAS RÁPIDAS */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 20px;
    }

    .stat-card {
        background: var(--bg-card);
        border: 1px solid var(--border-subtle);
        border-radius: 14px;
        padding: 24px;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        transition: transform 0.2s var(--ease-fluid), border-color 0.2s ease, box-shadow 0.2s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        border-color: var(--border-strong);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
    }

    .stat-info {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .stat-label {
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
        line-height: 1.1;
    }

    .stat-desc {
        font-size: 12px;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 4px;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .stat-icon svg {
        width: 24px;
        height: 24px;
    }

    .stat-icon.blue {
        background: var(--barber-blue-pale);
        color: var(--barber-blue-light);
        border: 1px solid rgba(37, 99, 235, 0.3);
    }

    .stat-icon.red {
        background: var(--barber-red-light);
        color: var(--barber-red);
        border: 1px solid var(--barber-red-border);
    }

    .stat-icon.green {
        background: var(--accent-success-pale);
        color: var(--accent-success);
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    .stat-icon.amber {
        background: var(--accent-warning-pale);
        color: var(--accent-warning);
        border: 1px solid rgba(245, 158, 11, 0.3);
    }

    /* SECCIÓN DE CONTENIDO A DOBLE COLUMNA */
    .dashboard-columns {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
    }

    @media (max-width: 1024px) {
        .dashboard-columns {
            grid-template-columns: 1fr;
        }
    }

    .content-panel {
        background: var(--bg-card);
        border: 1px solid var(--border-subtle);
        border-radius: 16px;
        padding: 24px 28px;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--border-subtle);
    }

    .panel-title-group {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .panel-title-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: var(--barber-blue-pale);
        color: var(--barber-blue-light);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .panel-title-icon svg {
        width: 18px;
        height: 18px;
    }

    .panel-title {
        font-size: 17px;
        font-weight: 700;
        color: var(--text-white);
        margin: 0;
    }

    .panel-action-link {
        font-size: 13px;
        font-weight: 600;
        color: var(--barber-blue-light);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 4px;
        transition: color 0.2s ease, transform 0.2s ease;
    }

    .panel-action-link:hover {
        color: #60a5fa;
        transform: translateX(2px);
    }

    /* LISTA DE CITAS DE HOY */
    .citas-list {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .cita-item-card {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid var(--border-subtle);
        border-radius: 12px;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        transition: all 0.2s ease;
    }

    .cita-item-card:hover {
        background: rgba(255, 255, 255, 0.04);
        border-color: var(--border-strong);
    }

    .cita-item-left {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .cita-time-badge {
        background: var(--bg-canvas);
        border: 1px solid var(--border-subtle);
        border-radius: 8px;
        padding: 8px 12px;
        text-align: center;
        min-width: 75px;
    }

    .cita-time-hour {
        font-size: 14px;
        font-weight: 800;
        color: var(--barber-blue-light);
        display: block;
    }

    .cita-time-duration {
        font-size: 11px;
        color: var(--text-muted);
    }

    .cita-details {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .cita-client-name {
        font-size: 15px;
        font-weight: 700;
        color: var(--text-white);
    }

    .cita-service-name {
        font-size: 13px;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .cita-service-badge {
        font-size: 11px;
        font-weight: 600;
        padding: 1px 6px;
        border-radius: 4px;
        background: rgba(255, 255, 255, 0.06);
        color: var(--text-muted);
    }

    .cita-item-right {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .status-badge {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        padding: 4px 10px;
        border-radius: 20px;
        white-space: nowrap;
    }

    .status-badge.confirmada {
        background: var(--barber-blue-pale);
        color: var(--barber-blue-light);
        border: 1px solid rgba(37, 99, 235, 0.3);
    }

    .status-badge.en-curso {
        background: var(--accent-warning-pale);
        color: var(--accent-warning);
        border: 1px solid rgba(245, 158, 11, 0.3);
    }

    .status-badge.completada {
        background: var(--accent-success-pale);
        color: var(--accent-success);
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    /* ESTADO VACÍO ELEGANTE */
    .empty-state {
        padding: 40px 20px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
    }

    .empty-state-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: var(--barber-blue-pale);
        color: var(--barber-blue-light);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 4px;
    }

    .empty-state-icon svg {
        width: 28px;
        height: 28px;
    }

    .empty-state-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-white);
    }

    .empty-state-text {
        font-size: 13px;
        color: var(--text-muted);
        max-width: 360px;
        line-height: 1.5;
    }

    /* TARJETA LATERAL DE ACCIONES RÁPIDAS */
    .quick-actions-card {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .action-btn {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid var(--border-subtle);
        color: var(--text-main);
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .action-btn:hover {
        background: rgba(255, 255, 255, 0.05);
        border-color: var(--barber-blue-border);
        transform: translateX(4px);
    }

    .action-btn-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .action-btn-icon.blue {
        background: var(--barber-blue-pale);
        color: var(--barber-blue-light);
    }

    .action-btn-icon.red {
        background: var(--barber-red-light);
        color: var(--barber-red);
    }

    .action-btn-text {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .action-btn-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--text-white);
    }

    .action-btn-desc {
        font-size: 12px;
        color: var(--text-muted);
    }

    /* HORARIO ASIGNADO HOY */
    .schedule-info-box {
        background: rgba(37, 99, 235, 0.04);
        border: 1px dashed var(--barber-blue-border);
        border-radius: 12px;
        padding: 18px;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .schedule-info-title {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--barber-blue-light);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .schedule-info-detail {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-white);
    }
</style>
@endpush

@section('content')
<div class="dashboard-wrapper">

    <!-- 1. SALUDO Y BIENVENIDA -->
    <section class="welcome-banner" aria-labelledby="welcomeTitle">
        <div class="welcome-banner-badge">
            Especialista &bull; Beauty & Barber Studio
        </div>
        <h1 id="welcomeTitle" class="welcome-title">
            ¡Hola, {{ $user->name }}! Te damos la bienvenida a tu panel
        </h1>
        <p class="welcome-subtitle">
            Aquí tienes el control de tu jornada: consulta tus citas programadas, revisa tu agenda de servicios y mantén al día la configuración de tu cuenta profesional.
        </p>
    </section>

    <!-- 2. TARJETAS DE MÉTRICAS Y RESUMEN RÁPIDO -->
    <section class="stats-grid" aria-label="Métricas del día">
        
        <!-- Citas para hoy -->
        <div class="stat-card">
            <div class="stat-info">
                <span class="stat-label">Citas para hoy</span>
                <span class="stat-value">{{ $citasHoy->count() }}</span>
                <span class="stat-desc">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    Jornada actual
                </span>
            </div>
            <div class="stat-icon blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
            </div>
        </div>

        <!-- Próxima Cita -->
        <div class="stat-card">
            <div class="stat-info">
                <span class="stat-label">Próximo servicio</span>
                <span class="stat-value" style="font-size: 22px;">
                    @if($citasHoy->isNotEmpty())
                        {{ $citasHoy->first()->hora_inicio }}
                    @else
                        --:--
                    @endif
                </span>
                <span class="stat-desc">
                    @if($citasHoy->isNotEmpty())
                        {{ $citasHoy->first()->service->name ?? 'Servicio agendado' }}
                    @else
                        Sin citas pendientes hoy
                    @endif
                </span>
            </div>
            <div class="stat-icon amber">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2v4"></path>
                    <path d="M12 18v4"></path>
                    <path d="M4.93 4.93l2.83 2.83"></path>
                    <path d="M16.24 16.24l2.83 2.83"></path>
                    <path d="M2 12h4"></path>
                    <path d="M18 12h4"></path>
                    <path d="M4.93 19.07l2.83-2.83"></path>
                    <path d="M16.24 7.76l2.83-2.83"></path>
                </svg>
            </div>
        </div>

        <!-- Estado de disponibilidad -->
        <div class="stat-card">
            <div class="stat-info">
                <span class="stat-label">Estado de Turno</span>
                <span class="stat-value" style="color: var(--accent-success); font-size: 22px;">Disponible</span>
                <span class="stat-desc">
                    <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: var(--accent-success);"></span>
                    Listo para atender clientes
                </span>
            </div>
            <div class="stat-icon green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
            </div>
        </div>

        <!-- Especialidad / Servicios -->
        <div class="stat-card">
            <div class="stat-info">
                <span class="stat-label">Especialidad</span>
                <span class="stat-value" style="font-size: 20px;">
                    {{ $trabajador ? ($trabajador->experiencia ? $trabajador->experiencia . ' años exp.' : 'Especialista') : 'Barbero / Estilista' }}
                </span>
                <span class="stat-desc">Beauty & Barber Studio</span>
            </div>
            <div class="stat-icon red">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="6" cy="6" r="3"></circle>
                    <circle cx="6" cy="18" r="3"></circle>
                    <line x1="20" y1="4" x2="8.12" y2="15.88"></line>
                    <line x1="14.47" y1="14.48" x2="20" y2="20"></line>
                    <line x1="8.12" y1="8.12" x2="12" y2="12"></line>
                </svg>
            </div>
        </div>

    </section>

    <!-- 3. DETALLE DE JORNADA Y ACCESOS RÁPIDOS -->
    <div class="dashboard-columns">
        
        <!-- Columna Izquierda: Citas programadas para hoy -->
        <div class="content-panel">
            <div class="panel-header">
                <div class="panel-title-group">
                    <div class="panel-title-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                    </div>
                    <h2 class="panel-title">Citas de hoy ({{ \Carbon\Carbon::now()->locale('es')->isoFormat('D [de] MMMM') }})</h2>
                </div>
                <a href="{{ route('trabajador.agenda') }}" class="panel-action-link">
                    <span>Ver agenda completa</span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>
            </div>

            @if($citasHoy->isNotEmpty())
                <div class="citas-list">
                    @foreach($citasHoy as $cita)
                        <div class="cita-item-card">
                            <div class="cita-item-left">
                                <div class="cita-time-badge">
                                    <span class="cita-time-hour">{{ $cita->hora_inicio }} - {{ $cita->hora_fin ?? '' }}</span>
                                    <span class="cita-time-duration">{{ $cita->duracion_minutos ?? 45 }} min</span>
                                </div>
                                <div class="cita-details">
                                    <span class="cita-client-name">
                                        {{ $cita->cliente->name ?? 'Cliente Registrado' }} {{ $cita->cliente->apellidos ?? '' }}
                                    </span>
                                    <div class="cita-service-name">
                                        <span>{{ $cita->service->name ?? 'Servicio General' }}</span>
                                        <span class="cita-service-badge">${{ number_format($cita->precio ?? $cita->service->price ?? 0, 2) }}</span>
                                        @if($cita->cliente && $cita->cliente->telefono)
                                            <a href="tel:{{ $cita->cliente->telefono }}" style="color: var(--barber-blue-light); font-size: 11px; text-decoration: none; margin-left: 6px;" title="Llamar al cliente">
                                                📞 {{ $cita->cliente->telefono }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="cita-item-right">
                                <span class="status-badge {{ strtolower($cita->estado ?? 'confirmada') }}">
                                    {{ str_replace('_', ' ', ucfirst($cita->estado ?? 'Confirmada')) }}
                                </span>
                                <a href="{{ route('trabajador.agenda', ['fecha' => $cita->fecha instanceof \Carbon\Carbon ? $cita->fecha->format('Y-m-d') : $cita->fecha]) }}" class="panel-action-link" style="font-size: 12px; margin-left: 8px;">
                                    Consultar &rarr;
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 14 14"></polyline>
                        </svg>
                    </div>
                    <h3 class="empty-state-title">No tienes citas programadas para hoy</h3>
                    <p class="empty-state-text">
                        Cualquier nueva reserva que se asigne a tu perfil aparecerá automáticamente en esta sección y en tu agenda.
                    </p>
                </div>
            @endif
        </div>

        <!-- Columna Derecha: Accesos rápidos y horario del trabajador -->
        <div class="content-panel">
            <div class="panel-header">
                <div class="panel-title-group">
                    <div class="panel-title-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                        </svg>
                    </div>
                    <h2 class="panel-title">Accesos Rápidos</h2>
                </div>
            </div>

            <div class="quick-actions-card">
                <a href="{{ route('trabajador.agenda') }}" class="action-btn">
                    <div class="action-btn-icon blue">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                            <path d="m9 16 2 2 4-4"></path>
                        </svg>
                    </div>
                    <div class="action-btn-text">
                        <span class="action-btn-title">Mi Agenda de Citas</span>
                        <span class="action-btn-desc">Ver calendario completo y citas</span>
                    </div>
                </a>

                <a href="{{ route('trabajador.configuracion') }}" class="action-btn">
                    <div class="action-btn-icon red">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="3"></circle>
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                        </svg>
                    </div>
                    <div class="action-btn-text">
                        <span class="action-btn-title">Configuración de Cuenta</span>
                        <span class="action-btn-desc">Editar perfil, datos y servicios</span>
                    </div>
                </a>
            </div>

            <!-- Horario de la sucursal -->
            <div class="schedule-info-box">
                <span class="schedule-info-title">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    Horario de Atención del Estudio
                </span>
                <span class="schedule-info-detail">Lunes a Sábado: 09:00 AM - 08:00 PM</span>
                <span style="font-size: 12px; color: var(--text-muted);">Domingo: Cerrado</span>
            </div>
        </div>

    </div>

</div>
@endsection
