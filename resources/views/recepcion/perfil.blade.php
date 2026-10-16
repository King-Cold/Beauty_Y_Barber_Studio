@extends('recepcion.layout')

@section('title', 'Beauty & Barber Studio - Mi Perfil y Seguridad (Recepción)')

@push('styles')
<style>
    /* ===================================================
       ESTILOS DEL MÓDULO DE PERFIL DE RECEPCIÓN
       =================================================== */
    .profile-header-area {
        margin-bottom: 24px;
    }

    .breadcrumb-custom {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: var(--text-muted);
        margin-bottom: 8px;
    }
    .breadcrumb-custom a {
        color: var(--text-muted);
        text-decoration: none;
        transition: color 0.15s ease;
    }
    .breadcrumb-custom a:hover {
        color: var(--barber-blue-light);
    }
    .breadcrumb-custom .separator {
        color: var(--border-strong);
    }
    .breadcrumb-custom .current {
        color: var(--text-white);
        font-weight: 600;
    }

    .page-title-box {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
    }
    .page-title-main {
        font-size: 26px;
        font-weight: 800;
        color: var(--text-white);
        letter-spacing: -0.02em;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .role-badge-chip {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        background: var(--barber-blue-pale);
        color: var(--barber-blue-light);
        border: 1px solid var(--barber-blue-border);
        padding: 4px 12px;
        border-radius: 20px;
    }

    /* HERO CARD DE PERFIL */
    .recepcion-hero-card {
        background: linear-gradient(135deg, rgba(10, 17, 36, 0.95) 0%, rgba(12, 20, 43, 0.98) 100%);
        border: 1px solid var(--border-subtle);
        border-radius: 18px;
        padding: 28px 32px;
        margin-bottom: 28px;
        display: grid;
        grid-template-columns: auto 1fr auto;
        gap: 24px;
        align-items: center;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
    }
    .recepcion-hero-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, var(--barber-blue) 0%, var(--barber-red) 100%);
    }

    .hero-avatar-wrap {
        position: relative;
    }
    .hero-avatar-circle {
        width: 82px;
        height: 82px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--barber-blue) 0%, #1e40af 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        font-weight: 800;
        color: #ffffff;
        box-shadow: 0 8px 24px rgba(0, 85, 255, 0.35);
        border: 3px solid rgba(255, 255, 255, 0.12);
        letter-spacing: 0.05em;
    }
    .hero-avatar-status {
        position: absolute;
        bottom: 2px;
        right: 2px;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #10b981;
        border: 3px solid var(--bg-card);
        box-shadow: 0 0 10px rgba(16, 185, 129, 0.6);
    }

    .hero-details h2 {
        font-size: 22px;
        font-weight: 700;
        color: var(--text-white);
        margin-bottom: 4px;
    }
    .hero-details p {
        font-size: 13px;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 10px;
    }
    .hero-details p svg {
        width: 15px;
        height: 15px;
        color: var(--barber-blue-light);
    }

    .role-badge-detail {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(0, 85, 255, 0.12);
        color: #93c5fd;
        border: 1px solid rgba(0, 85, 255, 0.25);
        padding: 3px 12px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 600;
    }

    .hero-metrics-grid {
        display: flex;
        gap: 14px;
    }
    .hero-metric-box {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid var(--border-subtle);
        border-radius: 12px;
        padding: 12px 18px;
        text-align: center;
        min-width: 105px;
        transition: all 0.2s ease;
    }
    .hero-metric-box:hover {
        background: rgba(255, 255, 255, 0.06);
        border-color: var(--border-strong);
        transform: translateY(-2px);
    }
    .hero-metric-box .metric-val {
        font-size: 20px;
        font-weight: 800;
        color: var(--text-white);
        display: block;
    }
    .hero-metric-box .metric-tag {
        font-size: 11px;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 600;
        margin-top: 2px;
        display: block;
    }

    /* GRID DE FORMULARIOS */
    .profile-forms-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 28px;
    }

    .recepcion-card {
        background: var(--bg-card);
        border: 1px solid var(--border-subtle);
        border-radius: 16px;
        padding: 28px;
        display: flex;
        flex-direction: column;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        transition: border-color 0.2s ease;
    }
    .recepcion-card:hover {
        border-color: var(--border-strong);
    }

    .card-top-header {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 22px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--border-subtle);
    }

    .card-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .card-icon-blue {
        background: var(--barber-blue-pale);
        color: var(--barber-blue-light);
        border: 1px solid var(--barber-blue-border);
    }
    .card-icon-red {
        background: var(--barber-red-light);
        color: var(--barber-red);
        border: 1px solid var(--barber-red-border);
    }
    .card-icon-box svg {
        width: 22px;
        height: 22px;
    }

    .card-title-meta h3 {
        font-size: 17px;
        font-weight: 700;
        color: var(--text-white);
    }
    .card-title-meta p {
        font-size: 12.5px;
        color: var(--text-muted);
        margin-top: 2px;
    }

    /* FORM ELEMENTS */
    .form-group-custom {
        margin-bottom: 18px;
    }
    .form-group-custom label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #cbd5e1;
        margin-bottom: 6px;
    }
    .form-group-custom label .star {
        color: var(--barber-red);
        margin-left: 2px;
    }

    .input-field-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-icon-lead {
        position: absolute;
        left: 14px;
        width: 18px;
        height: 18px;
        color: var(--text-muted);
        pointer-events: none;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .input-icon-lead svg {
        width: 17px;
        height: 17px;
    }

    .form-input-recepcion {
        width: 100%;
        background: #070d1e;
        border: 1px solid var(--border-subtle);
        border-radius: 10px;
        padding: 11px 14px 11px 42px;
        font-size: 14px;
        color: var(--text-white);
        font-family: inherit;
        transition: all 0.2s ease;
    }
    .form-input-recepcion:focus {
        outline: none;
        border-color: var(--barber-blue);
        background: #09122a;
        box-shadow: 0 0 0 3px rgba(0, 85, 255, 0.2);
    }
    .form-input-recepcion::placeholder {
        color: #475569;
    }

    .btn-toggle-eye {
        position: absolute;
        right: 12px;
        background: transparent;
        border: none;
        color: var(--text-muted);
        cursor: pointer;
        padding: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color 0.15s ease;
    }
    .btn-toggle-eye:hover {
        color: var(--text-white);
    }
    .btn-toggle-eye svg {
        width: 18px;
        height: 18px;
    }

    .input-hint-text {
        font-size: 11.5px;
        color: var(--text-muted);
        margin-top: 5px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* REGLAS DINAMICAS DE CONTRASEÑA */
    .rules-checklist-box {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid var(--border-subtle);
        border-radius: 10px;
        padding: 14px;
        margin-bottom: 20px;
    }
    .checklist-title {
        font-size: 11.5px;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 8px;
    }
    .check-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        color: var(--text-muted);
        margin-bottom: 5px;
        transition: color 0.2s ease;
    }
    .check-item:last-child {
        margin-bottom: 0;
    }
    .check-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #475569;
        transition: all 0.2s ease;
    }
    .check-item.valid {
        color: #34d399;
    }
    .check-item.valid .check-dot {
        background: #10b981;
        box-shadow: 0 0 8px rgba(16, 185, 129, 0.7);
    }

    /* ALERTA DE AUTONOMÍA */
    .autonomy-alert {
        display: flex;
        gap: 12px;
        background: rgba(0, 85, 255, 0.08);
        border: 1px solid rgba(0, 85, 255, 0.25);
        border-radius: 10px;
        padding: 12px 14px;
        margin-bottom: 20px;
    }
    .autonomy-alert svg {
        width: 20px;
        height: 20px;
        color: var(--barber-blue-light);
        flex-shrink: 0;
        margin-top: 1px;
    }
    .autonomy-alert p {
        font-size: 12px;
        color: #bfdbfe;
        line-height: 1.4;
    }

    /* BOTONES */
    .btn-action-submit {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 12px 22px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        transition: all 0.2s ease;
        margin-top: auto;
        width: 100%;
        font-family: inherit;
    }
    .btn-submit-blue {
        background: linear-gradient(135deg, var(--barber-blue) 0%, #1d4ed8 100%);
        color: #ffffff;
        box-shadow: 0 4px 14px rgba(0, 85, 255, 0.35);
    }
    .btn-submit-blue:hover:not(:disabled) {
        background: linear-gradient(135deg, var(--barber-blue-light) 0%, var(--barber-blue) 100%);
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(0, 85, 255, 0.45);
    }
    .btn-submit-red {
        background: linear-gradient(135deg, var(--barber-red) 0%, #b91c1c 100%);
        color: #ffffff;
        box-shadow: 0 4px 14px rgba(239, 68, 68, 0.35);
    }
    .btn-submit-red:hover:not(:disabled) {
        background: linear-gradient(135deg, #f87171 0%, var(--barber-red) 100%);
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(239, 68, 68, 0.45);
    }
    .btn-action-submit:disabled {
        opacity: 0.65;
        cursor: not-allowed;
        transform: none !important;
    }
    .btn-spinner {
        display: none;
        width: 16px;
        height: 16px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: #ffffff;
        animation: spin 0.8s linear infinite;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* RESPONSIVE */
    @media (max-width: 1024px) {
        .profile-forms-grid {
            grid-template-columns: 1fr;
        }
        .recepcion-hero-card {
            grid-template-columns: 1fr;
            text-align: center;
            justify-items: center;
        }
        .hero-metrics-grid {
            justify-content: center;
            flex-wrap: wrap;
        }
        .hero-details p {
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
<div class="profile-header-area">
    <div class="breadcrumb-custom">
        <a href="{{ route('recepcion.dashboard') }}">Recepción</a>
        <span class="separator">/</span>
        <span class="current">Mi Perfil y Seguridad</span>
    </div>

    <div class="page-title-box">
        <h1 class="page-title-main">
            <span>Perfil de Recepción</span>
            <span class="role-badge-chip">Rol 2</span>
        </h1>
    </div>
</div>

<!-- HERO BANNER -->
<div class="recepcion-hero-card">
    <div class="hero-avatar-wrap">
        @php
            $initials = strtoupper(substr($user->name ?? 'R', 0, 1) . substr($user->apellidos ?? 'E', 0, 1));
        @endphp
        <div class="hero-avatar-circle" id="heroAvatarCircle">{{ $initials }}</div>
        <div class="hero-avatar-status" title="Cuenta activa"></div>
    </div>

    <div class="hero-details">
        <h2>
            <span id="heroDisplayName">{{ $user->name }} {{ $user->apellidos }}</span>
        </h2>
        <p>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                <polyline points="22,6 12,13 2,6"></polyline>
            </svg>
            <span id="heroDisplayEmail">{{ $user->email }}</span>
        </p>
        <div class="role-badge-detail">
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
            </svg>
            <span>Personal de Recepción & Atención al Cliente</span>
        </div>
    </div>

    <div class="hero-metrics-grid">
        <div class="hero-metric-box" title="Citas agendadas para el día de hoy">
            <span class="metric-val">{{ $citasHoy ?? 0 }}</span>
            <span class="metric-tag">Citas Hoy</span>
        </div>
        <div class="hero-metric-box" title="Citas confirmadas en el sistema">
            <span class="metric-val">{{ $citasConfirmadas ?? 0 }}</span>
            <span class="metric-tag">Confirmadas</span>
        </div>
        <div class="hero-metric-box" title="Clientes registrados">
            <span class="metric-val">{{ $totalClientes ?? 0 }}</span>
            <span class="metric-tag">Clientes</span>
        </div>
    </div>
</div>

<!-- GRID DE DOS TARJETAS -->
<div class="profile-forms-grid">

    <!-- TARJETA 1: DATOS PERSONALES -->
    <div class="recepcion-card">
        <div class="card-top-header">
            <div class="card-icon-box card-icon-blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </div>
            <div class="card-title-meta">
                <h3>Información de Contacto</h3>
                <p>Mantén al día tu identidad y datos de contacto de recepción.</p>
            </div>
        </div>

        <form id="formRecepcionDatos" method="POST" action="{{ route('recepcion.perfil.datos') }}">
            @csrf
            @method('PUT')

            <div class="form-group-custom">
                <label for="recep_name">Nombre(s) <span class="star">*</span></label>
                <div class="input-field-wrap">
                    <span class="input-icon-lead">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </span>
                    <input 
                        type="text" 
                        id="recep_name" 
                        name="name" 
                        class="form-input-recepcion" 
                        value="{{ old('name', $user->name) }}" 
                        required 
                        placeholder="Nombre de la recepcionista"
                        autocomplete="given-name"
                    >
                </div>
            </div>

            <div class="form-group-custom">
                <label for="recep_apellidos">Apellidos <span class="star">*</span></label>
                <div class="input-field-wrap">
                    <span class="input-icon-lead">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                        </svg>
                    </span>
                    <input 
                        type="text" 
                        id="recep_apellidos" 
                        name="apellidos" 
                        class="form-input-recepcion" 
                        value="{{ old('apellidos', $user->apellidos) }}" 
                        required 
                        placeholder="Apellidos de la recepcionista"
                        autocomplete="family-name"
                    >
                </div>
            </div>

            <div class="form-group-custom">
                <label for="recep_email">Correo Electrónico <span class="star">*</span></label>
                <div class="input-field-wrap">
                    <span class="input-icon-lead">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                    </span>
                    <input 
                        type="email" 
                        id="recep_email" 
                        name="email" 
                        class="form-input-recepcion" 
                        value="{{ old('email', $user->email) }}" 
                        required 
                        placeholder="correo@ejemplo.com"
                        autocomplete="email"
                    >
                </div>
                <div class="input-hint-text">Tu correo es utilizado para ingresar a la plataforma.</div>
            </div>

            <div class="form-group-custom">
                <label for="recep_telefono">Teléfono de Contacto</label>
                <div class="input-field-wrap">
                    <span class="input-icon-lead">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                        </svg>
                    </span>
                    <input 
                        type="tel" 
                        id="recep_telefono" 
                        name="telefono" 
                        class="form-input-recepcion" 
                        value="{{ old('telefono', $user->telefono) }}" 
                        placeholder="Ej. 5559876543"
                        maxlength="20"
                        autocomplete="tel"
                    >
                </div>
            </div>

            <div class="form-group-custom">
                <label for="recep_fecha_nacimiento">Fecha de Nacimiento</label>
                <div class="input-field-wrap">
                    <span class="input-icon-lead">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                    </span>
                    <input 
                        type="date" 
                        id="recep_fecha_nacimiento" 
                        name="fecha_nacimiento" 
                        class="form-input-recepcion" 
                        value="{{ old('fecha_nacimiento', $user->fecha_nacimiento ? substr($user->fecha_nacimiento, 0, 10) : '') }}"
                        min="1920-01-01"
                        max="{{ date('Y-m-d') }}"
                    >
                </div>
                <div class="input-hint-text">No se permiten fechas futuras ni el día de mañana.</div>
            </div>

            <button type="submit" class="btn-action-submit btn-submit-blue" id="btnGuardarRecepDatos">
                <span class="btn-spinner" id="spinnerRecepDatos"></span>
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                    <polyline points="7 3 7 8 15 8"></polyline>
                </svg>
                <span>Guardar Datos Personales</span>
            </button>
        </form>
    </div>

    <!-- TARJETA 2: SEGURIDAD Y CAMBIO DE CONTRASEÑA -->
    <div class="recepcion-card">
        <div class="card-top-header">
            <div class="card-icon-box card-icon-red">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
            </div>
            <div class="card-title-meta">
                <h3>Seguridad de la Cuenta</h3>
                <p>Renueva periódicamente tu contraseña para proteger tu acceso.</p>
            </div>
        </div>

        <div class="autonomy-alert">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="16" x2="12" y2="12"></line>
                <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
            <p><strong>Módulo Autónomo:</strong> Puedes actualizar tu propia contraseña de acceso en cualquier momento sin necesidad de solicitar asistencia al administrador.</p>
        </div>

        <form id="formRecepcionPassword" method="POST" action="{{ route('recepcion.perfil.password') }}">
            @csrf
            @method('PUT')

            <div class="form-group-custom">
                <label for="recep_current_password">Contraseña Actual <span class="star">*</span></label>
                <div class="input-field-wrap">
                    <span class="input-icon-lead">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                    </span>
                    <input 
                        type="password" 
                        id="recep_current_password" 
                        name="current_password" 
                        class="form-input-recepcion" 
                        required 
                        placeholder="Ingresa tu contraseña actual"
                        autocomplete="current-password"
                    >
                    <button type="button" class="btn-toggle-eye" data-target="recep_current_password" aria-label="Mostrar u ocultar contraseña">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="form-group-custom">
                <label for="recep_password">Nueva Contraseña <span class="star">*</span></label>
                <div class="input-field-wrap">
                    <span class="input-icon-lead">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 2l-2 2m-1.5 1.5L14 9l-1.5-1.5L10 10l4 4 2.5-2.5L18 13l3.5-3.5a2.12 2.12 0 0 0 0-3l-2-2a2.12 2.12 0 0 0-3 0z"></path>
                            <path d="M3 21l3.5-3.5"></path>
                            <path d="M6 18l-1.5 1.5"></path>
                            <path d="M7.5 19.5L6 21"></path>
                        </svg>
                    </span>
                    <input 
                        type="password" 
                        id="recep_password" 
                        name="password" 
                        class="form-input-recepcion" 
                        required 
                        placeholder="Mínimo 8 caracteres alfanuméricos"
                        autocomplete="new-password"
                    >
                    <button type="button" class="btn-toggle-eye" data-target="recep_password" aria-label="Mostrar u ocultar contraseña">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="form-group-custom">
                <label for="recep_password_confirmation">Confirmar Nueva Contraseña <span class="star">*</span></label>
                <div class="input-field-wrap">
                    <span class="input-icon-lead">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </span>
                    <input 
                        type="password" 
                        id="recep_password_confirmation" 
                        name="password_confirmation" 
                        class="form-input-recepcion" 
                        required 
                        placeholder="Repite la nueva contraseña"
                        autocomplete="new-password"
                    >
                    <button type="button" class="btn-toggle-eye" data-target="recep_password_confirmation" aria-label="Mostrar u ocultar contraseña">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- CHECKLIST DE REGLAS -->
            <div class="rules-checklist-box">
                <div class="checklist-title">Requisitos de Seguridad</div>
                <div class="check-item" id="ruleRecepMin">
                    <div class="check-dot"></div>
                    <span>Mínimo 8 caracteres</span>
                </div>
                <div class="check-item" id="ruleRecepAlpha">
                    <div class="check-dot"></div>
                    <span>Alfanumérica (letras y al menos un número)</span>
                </div>
                <div class="check-item" id="ruleRecepMatch">
                    <div class="check-dot"></div>
                    <span>Las contraseñas coinciden exactamente</span>
                </div>
            </div>

            <button type="submit" class="btn-action-submit btn-submit-red" id="btnGuardarRecepPass">
                <span class="btn-spinner" id="spinnerRecepPass"></span>
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                </svg>
                <span>Actualizar Contraseña</span>
            </button>
        </form>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        // --- Alternar visibilidad de contraseñas ---
        document.querySelectorAll('.btn-toggle-eye').forEach(button => {
            button.addEventListener('click', function () {
                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                if (!input) return;

                const isPassword = input.getAttribute('type') === 'password';
                input.setAttribute('type', isPassword ? 'text' : 'password');
                this.style.color = isPassword ? 'var(--barber-blue-light)' : 'var(--text-muted)';
            });
        });

        // --- Validador en tiempo real de reglas de contraseña ---
        const newPassInput = document.getElementById('recep_password');
        const confirmPassInput = document.getElementById('recep_password_confirmation');
        const ruleMin = document.getElementById('ruleRecepMin');
        const ruleAlpha = document.getElementById('ruleRecepAlpha');
        const ruleMatch = document.getElementById('ruleRecepMatch');

        function checkRecepPassRules() {
            const val = newPassInput.value || '';
            const confirmVal = confirmPassInput.value || '';

            // Mínimo 8 caracteres
            const hasMin = val.length >= 8;
            ruleMin.classList.toggle('valid', hasMin);

            // Alfanumérica
            const hasLetter = /[a-zA-Z]/.test(val);
            const hasNumber = /\d/.test(val);
            const isAlpha = hasLetter && hasNumber;
            ruleAlpha.classList.toggle('valid', isAlpha);

            // Coincidencia
            const matches = val.length > 0 && val === confirmVal;
            ruleMatch.classList.toggle('valid', matches);
        }

        if (newPassInput && confirmPassInput) {
            newPassInput.addEventListener('input', checkRecepPassRules);
            confirmPassInput.addEventListener('input', checkRecepPassRules);
        }

        // --- Validación Estricta de Fecha de Nacimiento (No mañana ni futuras) ---
        const fechaInput = document.getElementById('recep_fecha_nacimiento');
        if (fechaInput) {
            const today = new Date().toISOString().split('T')[0];
            fechaInput.setAttribute('max', today);

            fechaInput.addEventListener('change', function () {
                if (this.value && this.value > today) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Fecha inválida',
                        text: 'La fecha de nacimiento no puede ser una fecha futura ni el día de mañana.',
                        confirmButtonColor: '#0055ff'
                    });
                    this.value = '';
                }
            });
        }

        // --- Envío AJAX de Formulario de Datos Personales ---
        const formDatos = document.getElementById('formRecepcionDatos');
        const btnGuardarDatos = document.getElementById('btnGuardarRecepDatos');
        const spinnerDatos = document.getElementById('spinnerRecepDatos');

        if (formDatos) {
            formDatos.addEventListener('submit', async function (e) {
                e.preventDefault();

                if (fechaInput && fechaInput.value) {
                    const today = new Date().toISOString().split('T')[0];
                    if (fechaInput.value > today) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Fecha inválida',
                            text: 'La fecha de nacimiento no puede ser una fecha futura ni el día de mañana.',
                            confirmButtonColor: '#ef4444'
                        });
                        return;
                    }
                }

                btnGuardarDatos.disabled = true;
                if (spinnerDatos) spinnerDatos.style.display = 'inline-block';

                const formData = new FormData(formDatos);

                try {
                    const response = await fetch(formDatos.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Datos Actualizados!',
                            text: data.message || 'Tus datos de recepción se han guardado con éxito.',
                            confirmButtonColor: '#0055ff',
                            timer: 3000,
                            timerProgressBar: true
                        });

                        if (data.user) {
                            const fullName = `${data.user.name} ${data.user.apellidos}`.trim();
                            const initials = ((data.user.name ? data.user.name.substring(0, 1) : 'R') + 
                                              (data.user.apellidos ? data.user.apellidos.substring(0, 1) : 'E')).toUpperCase();

                            const heroDisplayName = document.getElementById('heroDisplayName');
                            if (heroDisplayName) heroDisplayName.textContent = fullName;

                            const heroDisplayEmail = document.getElementById('heroDisplayEmail');
                            if (heroDisplayEmail) heroDisplayEmail.textContent = data.user.email;

                            const heroAvatarCircle = document.getElementById('heroAvatarCircle');
                            if (heroAvatarCircle) heroAvatarCircle.textContent = initials;

                            // Actualizar dropdown del layout
                            const navAvatar = document.querySelector('.user-nav .user-avatar');
                            if (navAvatar) navAvatar.textContent = initials;

                            const navUserName = document.querySelector('.user-nav .user-name');
                            if (navUserName) navUserName.textContent = fullName;

                            const navUserRole = document.querySelector('.user-nav .user-role');
                            if (navUserRole) navUserRole.textContent = data.user.email;
                        }
                    } else {
                        const errorMsg = data.message || (data.errors ? Object.values(data.errors).flat().join('<br>') : 'Error al guardar los datos.');
                        Swal.fire({
                            icon: 'error',
                            title: 'Error de validación',
                            html: errorMsg,
                            confirmButtonColor: '#ef4444'
                        });
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de conexión',
                        text: 'No se pudo conectar con el servidor. Intenta nuevamente.',
                        confirmButtonColor: '#ef4444'
                    });
                } finally {
                    btnGuardarDatos.disabled = false;
                    if (spinnerDatos) spinnerDatos.style.display = 'none';
                }
            });
        }

        // --- Envío AJAX de Formulario de Contraseña ---
        const formPass = document.getElementById('formRecepcionPassword');
        const btnGuardarPass = document.getElementById('btnGuardarRecepPass');
        const spinnerPass = document.getElementById('spinnerRecepPass');

        if (formPass) {
            formPass.addEventListener('submit', async function (e) {
                e.preventDefault();

                const currentPass = document.getElementById('recep_current_password').value;
                const newPass = document.getElementById('recep_password').value;
                const confirmPass = document.getElementById('recep_password_confirmation').value;

                if (!currentPass) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Campo obligatorio',
                        text: 'Debes ingresar tu contraseña actual para confirmar el cambio.',
                        confirmButtonColor: '#0055ff'
                    });
                    return;
                }

                if (newPass.length < 8) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Contraseña insegura',
                        text: 'La nueva contraseña debe tener al menos 8 caracteres.',
                        confirmButtonColor: '#0055ff'
                    });
                    return;
                }

                if (newPass !== confirmPass) {
                    Swal.fire({
                        icon: 'error',
                        title: 'No coinciden',
                        text: 'La nueva contraseña y su confirmación no coinciden exactamente.',
                        confirmButtonColor: '#ef4444'
                    });
                    return;
                }

                btnGuardarPass.disabled = true;
                if (spinnerPass) spinnerPass.style.display = 'inline-block';

                const formData = new FormData(formPass);

                try {
                    const response = await fetch(formPass.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Contraseña Actualizada!',
                            text: data.message || 'Tu contraseña de recepción ha sido modificada con éxito.',
                            confirmButtonColor: '#0055ff'
                        });
                        formPass.reset();
                        checkRecepPassRules();
                    } else {
                        const errorMsg = data.message || (data.errors ? Object.values(data.errors).flat().join('<br>') : 'Error al actualizar la contraseña.');
                        Swal.fire({
                            icon: 'error',
                            title: 'Validación de credenciales',
                            html: errorMsg,
                            confirmButtonColor: '#ef4444'
                        });
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de servidor',
                        text: 'Ocurrió un error inesperado al procesar la actualización. Intenta de nuevo.',
                        confirmButtonColor: '#ef4444'
                    });
                } finally {
                    btnGuardarPass.disabled = false;
                    if (spinnerPass) spinnerPass.style.display = 'none';
                }
            });
        }
    });
</script>
@endpush
