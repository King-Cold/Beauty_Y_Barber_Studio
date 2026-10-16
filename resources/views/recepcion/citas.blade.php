@extends('recepcion.layout')

@section('title', 'Beauty & Barber Studio - Gestión de Citas')

@push('styles')
<style>
    .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 24px;
    }

    .section-title {
        font-size: 26px;
        font-weight: 800;
        color: var(--text-white);
        letter-spacing: -0.02em;
        margin-bottom: 4px;
    }

    .section-subtitle {
        font-size: 14px;
        color: var(--text-muted);
    }

    .header-actions {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .btn-calendario-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 11px 18px;
        background: rgba(255, 255, 255, 0.05);
        color: var(--text-white);
        font-size: 13.5px;
        font-weight: 700;
        border-radius: 10px;
        text-decoration: none;
        transition: all 0.25s var(--ease-fluid);
        border: 1px solid var(--border-subtle);
    }

    .btn-calendario-link:hover {
        background: rgba(0, 85, 255, 0.15);
        border-color: rgba(0, 85, 255, 0.4);
        color: #60a5fa;
        transform: translateY(-2px);
    }

    .btn-agendar-cta {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 11px 22px;
        background: linear-gradient(135deg, var(--barber-blue) 0%, #1d4ed8 100%);
        color: var(--text-white);
        font-size: 13.5px;
        font-weight: 700;
        border-radius: 10px;
        text-decoration: none;
        transition: all 0.25s var(--ease-fluid);
        border: 1px solid rgba(0, 85, 255, 0.4);
        box-shadow: 0 4px 14px rgba(0, 85, 255, 0.25);
    }

    .btn-agendar-cta:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 85, 255, 0.4);
        border-color: #60a5fa;
    }

    /* BARRA DE FILTROS */
    .filtros-card {
        background: var(--bg-card);
        border: 1px solid var(--border-subtle);
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    }

    .filtros-form {
        display: grid;
        grid-template-columns: 2fr 1.2fr 1.2fr 1fr 1fr auto;
        gap: 14px;
        align-items: flex-end;
    }

    @media (max-width: 1200px) {
        .filtros-form {
            grid-template-columns: 1fr 1fr 1fr;
        }
    }

    @media (max-width: 768px) {
        .filtros-form {
            grid-template-columns: 1fr;
        }
    }

    .filtro-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .filtro-label {
        font-size: 12px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .filtro-input,
    .filtro-select {
        width: 100%;
        padding: 10px 14px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid var(--border-subtle);
        border-radius: 8px;
        color: var(--text-white);
        font-size: 13.5px;
        font-family: inherit;
        outline: none;
        transition: all 0.2s ease;
    }

    .filtro-input:focus,
    .filtro-select:focus {
        border-color: var(--barber-blue-light);
        background: rgba(0, 85, 255, 0.08);
        box-shadow: 0 0 0 3px rgba(0, 85, 255, 0.2);
    }

    .filtro-select option {
        background-color: #0c142b;
        color: #ffffff;
    }

    .filtros-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-filtrar {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 18px;
        background: var(--barber-blue);
        color: #ffffff;
        border: 1px solid rgba(0, 85, 255, 0.4);
        border-radius: 8px;
        font-size: 13.5px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-filtrar:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
    }

    .btn-limpiar {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 14px;
        background: rgba(255, 255, 255, 0.05);
        color: var(--text-muted);
        border: 1px solid var(--border-subtle);
        border-radius: 8px;
        font-size: 13.5px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-limpiar:hover {
        background: rgba(255, 255, 255, 0.1);
        color: #ffffff;
    }

    /* RESUMEN DE RESULTADOS */
    .resultados-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 14px;
        padding: 0 4px;
    }

    .resultados-count {
        font-size: 13px;
        color: var(--text-muted);
    }

    .resultados-count strong {
        color: var(--text-white);
    }

    /* TABLA DE CITAS */
    .panel-card {
        background: var(--bg-card);
        border: 1px solid var(--border-subtle);
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    }

    .citas-table-wrapper {
        overflow-x: auto;
    }

    .citas-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .citas-table th {
        padding: 12px 12px;
        font-size: 11px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1px solid var(--border-subtle);
        background: rgba(255, 255, 255, 0.02);
        white-space: nowrap;
    }

    .citas-table td {
        padding: 11px 12px;
        font-size: 13px;
        color: var(--text-main);
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        vertical-align: middle;
    }

    .citas-table tr:hover td {
        background: rgba(255, 255, 255, 0.02);
    }

    .table-folio-tag {
        font-family: monospace;
        font-size: 12px;
        font-weight: 700;
        color: #93c5fd;
        background: rgba(0, 85, 255, 0.12);
        border: 1px solid rgba(0, 85, 255, 0.25);
        padding: 3px 8px;
        border-radius: 6px;
        white-space: nowrap;
    }

    .cliente-cell {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .cliente-cell strong {
        color: #ffffff;
        font-size: 14px;
    }

    .cliente-subtext {
        font-size: 12px;
        color: var(--text-muted);
    }

    .servicio-cell {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .servicio-tag {
        display: inline-block;
        font-size: 10.5px;
        text-transform: uppercase;
        padding: 2px 6px;
        border-radius: 4px;
        background: rgba(255, 255, 255, 0.06);
        color: #94a3b8;
        width: fit-content;
    }

    .horario-cell {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .horario-cell strong {
        color: #ffffff;
        font-size: 13.5px;
    }

    .duracion-subtext {
        font-size: 11.5px;
        color: var(--text-muted);
    }

    .precio-highlight {
        color: #38bdf8;
        font-weight: 800;
        font-size: 14px;
        white-space: nowrap;
    }

    /* BADGES DE ESTADOS */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 700;
        text-transform: capitalize;
        white-space: nowrap;
    }

    .status-badge::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        display: inline-block;
    }

    .status-badge.confirmada {
        background: rgba(16, 185, 129, 0.15);
        color: #34d399;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }
    .status-badge.confirmada::before { background: #34d399; }

    .status-badge.cancelada {
        background: rgba(239, 68, 68, 0.15);
        color: #f87171;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }
    .status-badge.cancelada::before { background: #f87171; }

    .status-badge.completada {
        background: rgba(6, 182, 212, 0.15);
        color: #22d3ee;
        border: 1px solid rgba(6, 182, 212, 0.3);
    }
    .status-badge.completada::before { background: #22d3ee; }

    .status-badge.en_atencion,
    .status-badge.en_proceso {
        background: rgba(0, 85, 255, 0.15);
        color: #60a5fa;
        border: 1px solid rgba(0, 85, 255, 0.3);
    }
    .status-badge.en_atencion::before,
    .status-badge.en_proceso::before { background: #60a5fa; }

    .status-badge.vencida {
        background: rgba(245, 158, 11, 0.15);
        color: #fbbf24;
        border: 1px solid rgba(245, 158, 11, 0.3);
    }
    .status-badge.vencida::before { background: #fbbf24; }

    /* CONTENEDOR DE ACCIONES ESCORADO Y COMPACTO */
    .table-actions-group {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        justify-content: flex-end;
        white-space: nowrap;
    }

    /* BOTONES OPERATIVOS PRINCIPALES */
    .btn-iniciar-table {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 6px 11px;
        background: rgba(245, 158, 11, 0.14);
        color: #fbbf24;
        border: 1px solid rgba(245, 158, 11, 0.35);
        border-radius: 7px;
        font-size: 11.5px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    .btn-iniciar-table:hover {
        background: #d97706;
        color: #ffffff;
        border-color: #d97706;
        transform: translateY(-1px);
    }

    .btn-finalizar-table {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 6px 11px;
        background: rgba(16, 185, 129, 0.14);
        color: #34d399;
        border: 1px solid rgba(16, 185, 129, 0.35);
        border-radius: 7px;
        font-size: 11.5px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    .btn-finalizar-table:hover {
        background: #059669;
        color: #ffffff;
        border-color: #059669;
        transform: translateY(-1px);
    }

    .btn-checkin-table {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 6px 10px;
        background: rgba(16, 185, 129, 0.12);
        color: #34d399;
        border: 1px solid rgba(16, 185, 129, 0.3);
        border-radius: 7px;
        font-size: 11.5px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    .btn-checkin-table:hover {
        background: #059669;
        color: #ffffff;
        border-color: #059669;
        transform: translateY(-1px);
    }

    .btn-reagendar-table {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 6px 10px;
        background: rgba(0, 85, 255, 0.12);
        color: #60a5fa;
        border: 1px solid rgba(0, 85, 255, 0.3);
        border-radius: 7px;
        font-size: 11.5px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    .btn-reagendar-table:hover {
        background: #0055ff;
        color: #ffffff;
        border-color: #0055ff;
        transform: translateY(-1px);
    }

    /* MINI-BARRA DE HERRAMIENTAS RÁPIDAS (Detalles, Editar, Cancelar) */
    .table-tool-group {
        display: inline-flex;
        align-items: center;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 8px;
        padding: 2px;
        gap: 2px;
    }

    .btn-tool-icon {
        width: 29px;
        height: 29px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: transparent;
        border: none;
        border-radius: 6px;
        color: var(--text-muted);
        cursor: pointer;
        transition: all 0.18s ease;
    }
    .btn-tool-icon:hover {
        color: #ffffff;
        transform: scale(1.08);
    }
    .btn-tool-icon.tool-detalles:hover {
        background: rgba(0, 85, 255, 0.25);
        color: #93c5fd;
    }
    .btn-tool-icon.tool-editar:hover {
        background: rgba(56, 189, 248, 0.25);
        color: #38bdf8;
    }
    .btn-tool-icon.tool-cancelar:hover {
        background: rgba(239, 68, 68, 0.25);
        color: #fca5a5;
    }

    .badge-checkin-table {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 7px;
        background: rgba(16, 185, 129, 0.15);
        color: #34d399;
        border: 1px solid rgba(16, 185, 129, 0.3);
        border-radius: 12px;
        font-size: 10.5px;
        font-weight: 700;
        margin-top: 4px;
        white-space: nowrap;
    }
</style>
@endpush

@section('content')
    {{-- ENCABEZADO DE SECCIÓN --}}
    <div class="section-header">
        <div>
            <h1 class="section-title">Gestión de Citas</h1>
            <p class="section-subtitle">Consulta de citas registradas, filtros avanzados y acceso a información detallada.</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('recepcion.dashboard') }}" class="btn-calendario-link" style="border-color: rgba(0, 85, 255, 0.35); color: #60a5fa;" title="Ir al panel de turnos de recepción">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                </svg>
                Turnos en Recepción
            </a>
            <a href="{{ route('recepcion.calendario') }}" class="btn-calendario-link" title="Ver en vista de calendario interactivo">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                    <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                Ver Calendario
            </a>
            <a href="{{ route('recepcion.agendamiento') }}" class="btn-agendar-cta">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="18" height="18">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Agendar nueva cita
            </a>
        </div>
    </div>

    {{-- BARRA DE FILTROS DE CONSULTA (SERVIDOR) --}}
    <div class="filtros-card">
        <form action="{{ route('recepcion.citas') }}" method="GET" class="filtros-form" id="filtrosCitasForm">
            {{-- 1. Búsqueda por cliente, folio, teléfono o correo --}}
            <div class="filtro-group">
                <label class="filtro-label" for="inputBuscar">Buscar Cliente o Folio</label>
                <input 
                    type="text" 
                    id="inputBuscar" 
                    name="buscar" 
                    class="filtro-input" 
                    placeholder="Nombre, teléfono, correo o folio..." 
                    value="{{ $filtros['buscar'] ?? '' }}"
                >
            </div>

            {{-- 2. Filtro por Trabajador / Especialista --}}
            <div class="filtro-group">
                <label class="filtro-label" for="selectTrabajador">Especialista</label>
                <select id="selectTrabajador" name="trabajador_id" class="filtro-select">
                    <option value="">Todos los especialistas</option>
                    @foreach($trabajadores as $t)
                        <option value="{{ $t->id }}" {{ (isset($filtros['trabajador_id']) && (string)$filtros['trabajador_id'] === (string)$t->id) ? 'selected' : '' }}>
                            {{ $t->user->name ?? 'Especialista' }} {{ $t->user->apellidos ?? '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- 3. Filtro por Estado --}}
            <div class="filtro-group">
                <label class="filtro-label" for="selectEstado">Estado</label>
                <select id="selectEstado" name="estado" class="filtro-select">
                    <option value="todas" {{ (!isset($filtros['estado']) || $filtros['estado'] === 'todas') ? 'selected' : '' }}>Todos los estados</option>
                    <option value="confirmada" {{ (isset($filtros['estado']) && $filtros['estado'] === 'confirmada') ? 'selected' : '' }}>Confirmada</option>
                    <option value="en_proceso" {{ (isset($filtros['estado']) && in_array($filtros['estado'], ['en_proceso', 'en_atencion'])) ? 'selected' : '' }}>En proceso</option>
                    <option value="completada" {{ (isset($filtros['estado']) && $filtros['estado'] === 'completada') ? 'selected' : '' }}>Completada</option>
                    <option value="cancelada" {{ (isset($filtros['estado']) && $filtros['estado'] === 'cancelada') ? 'selected' : '' }}>Cancelada (Historial)</option>
                    <option value="vencida" {{ (isset($filtros['estado']) && $filtros['estado'] === 'vencida') ? 'selected' : '' }}>Vencida</option>
                </select>
            </div>

            {{-- 4. Fecha Desde --}}
            <div class="filtro-group">
                <label class="filtro-label" for="inputFechaDesde">Desde</label>
                <input 
                    type="date" 
                    id="inputFechaDesde" 
                    name="fecha_desde" 
                    class="filtro-input" 
                    value="{{ $filtros['fecha_desde'] ?? '' }}"
                >
            </div>

            {{-- 5. Fecha Hasta --}}
            <div class="filtro-group">
                <label class="filtro-label" for="inputFechaHasta">Hasta</label>
                <input 
                    type="date" 
                    id="inputFechaHasta" 
                    name="fecha_hasta" 
                    class="filtro-input" 
                    value="{{ $filtros['fecha_hasta'] ?? '' }}"
                >
            </div>

            {{-- 6. Botones de Acción --}}
            <div class="filtros-actions">
                <button type="submit" class="btn-filtrar" title="Aplicar filtros de búsqueda">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="16" height="16">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <span>Filtrar</span>
                </button>
                <a href="{{ route('recepcion.citas') }}" class="btn-limpiar" title="Restablecer filtros">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                        <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path>
                        <polyline points="3 3 3 8 8 8"></polyline>
                    </svg>
                    <span>Limpiar</span>
                </a>
            </div>
        </form>
    </div>

    {{-- BARRA DE RESULTADOS --}}
    <div class="resultados-bar">
        <span class="resultados-count">
            Mostrando <strong>{{ $citas->count() }}</strong> {{ $citas->count() === 1 ? 'cita encontrada' : 'citas encontradas' }} en el sistema
        </span>
        @if(!empty($filtros['buscar']) || !empty($filtros['trabajador_id']) || (!empty($filtros['estado']) && $filtros['estado'] !== 'todas') || !empty($filtros['fecha_desde']) || !empty($filtros['fecha_hasta']))
            <span style="font-size: 12px; color: #38bdf8; font-weight: 600;">
                Filtros activos en el servidor
            </span>
        @endif
    </div>

    {{-- LISTADO DE CITAS REGISTRADAS --}}
    <div class="panel-card">
        @if($citas->isEmpty())
            <div style="text-align: center; padding: 48px 24px;">
                <div style="width: 56px; height: 56px; margin: 0 auto 16px; border-radius: 12px; background: rgba(255,255,255,0.04); display: flex; align-items: center; justify-content: center; color: var(--text-muted);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="32" height="32">
                        <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                </div>
                <h3 style="font-size: 18px; font-weight: 700; color: #ffffff; margin-bottom: 8px;">
                    No se encontraron citas con los criterios seleccionados
                </h3>
                <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 24px; max-width: 480px; margin-left: auto; margin-right: auto;">
                    Prueba modificando o limpiando los filtros de búsqueda, o registra una nueva cita desde el agendamiento.
                </p>
                <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
                    <a href="{{ route('recepcion.citas') }}" class="btn-limpiar" style="color: #ffffff; background: rgba(255,255,255,0.08);">
                        Limpiar todos los filtros
                    </a>
                    <a href="{{ route('recepcion.agendamiento') }}" class="btn-agendar-cta">
                        Agendar nueva cita
                    </a>
                </div>
            </div>
        @else
            <div class="citas-table-wrapper">
                <table class="citas-table">
                    <thead>
                        <tr>
                            <th style="width: 85px;">Folio</th>
                            <th>Cliente</th>
                            <th>Servicio</th>
                            <th>Especialista</th>
                            <th style="width: 95px;">Fecha</th>
                            <th style="width: 120px;">Horario</th>
                            <th style="width: 85px;">Precio</th>
                            <th style="width: 120px;">Estado</th>
                            <th style="text-align: right; width: 185px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($citas as $cita)
                            <tr>
                                {{-- Folio --}}
                                <td>
                                    <span class="table-folio-tag">{{ $cita->folio }}</span>
                                </td>

                                {{-- Cliente --}}
                                <td>
                                    <div class="cliente-cell">
                                        <strong>
                                            {{ $cita->cliente ? trim($cita->cliente->name . ' ' . ($cita->cliente->apellidos ?? '')) : 'Cliente #' . $cita->user_id }}
                                        </strong>
                                        <span class="cliente-subtext">
                                            {{ $cita->cliente?->telefono ?? $cita->cliente?->email ?? 'Sin contacto' }}
                                        </span>
                                    </div>
                                </td>

                                {{-- Servicio --}}
                                <td>
                                    <div class="servicio-cell">
                                        <strong>{{ $cita->service->name ?? 'Servicio general' }}</strong>
                                        <span class="servicio-tag">{{ ucfirst($cita->service->category ?? 'Estudio') }}</span>
                                    </div>
                                </td>

                                {{-- Especialista --}}
                                <td>
                                    <span style="font-weight: 600; color: #f1f5f9;">
                                        {{ $cita->trabajador && $cita->trabajador->user ? trim($cita->trabajador->user->name . ' ' . ($cita->trabajador->user->apellidos ?? '')) : 'Especialista #' . $cita->trabajador_id }}
                                    </span>
                                </td>

                                {{-- Fecha --}}
                                <td>
                                    <span style="font-weight: 600;">
                                        {{ $cita->fecha ? \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') : '—' }}
                                    </span>
                                </td>

                                {{-- Horario --}}
                                <td>
                                    <div class="horario-cell">
                                        <strong>{{ substr($cita->hora_inicio, 0, 5) }} - {{ substr($cita->hora_fin, 0, 5) }}</strong>
                                        <span class="duracion-subtext">{{ $cita->duracion_minutos }} min</span>
                                    </div>
                                </td>

                                {{-- Precio --}}
                                <td>
                                    <span class="precio-highlight">{{ $cita->precio_formateado }}</span>
                                </td>

                                {{-- Estado --}}
                                <td>
                                    <div style="display: flex; flex-direction: column; align-items: flex-start; gap: 4px;">
                                        <span class="status-badge {{ $cita->estado_visual }}">
                                            {{ $cita->estado_label }}
                                        </span>
                                        @if($cita->isCheckedIn())
                                            <span class="badge-checkin-table" title="Check-in registrado el {{ $cita->check_in_at?->format('d/m/Y') }} a las {{ $cita->check_in_hora }} hrs">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="10" height="10">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                                Llegó {{ $cita->check_in_hora }}
                                            </span>
                                        @endif
                                    </div>
                                                         {{-- Acciones: Escoradas y Compactas --}}
                                <td style="text-align: right;">
                                    <div class="table-actions-group">
                                        {{-- 1. Botón Check-in de Llegada (si no ha llegado y no inicia) --}}
                                        @if($cita->puedeRegistrarCheckIn() && !$cita->puedeIniciarAtencion())
                                            <button 
                                                type="button" 
                                                class="btn-checkin-table" 
                                                onclick="registrarCheckInPrompt({{ $cita->id }}, '{{ route('recepcion.citas.checkin', $cita->id) }}', '{{ $cita->folio }}', '{{ addslashes($cita->cliente ? trim($cita->cliente->name . ' ' . ($cita->cliente->apellidos ?? '')) : 'Cliente #' . $cita->user_id) }}')"
                                                title="Registrar llegada del cliente (Check-in)"
                                            >
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="13" height="13">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                                <span>Check-in</span>
                                            </button>
                                        @endif

                                        {{-- 2. Iniciar Atención (Subtarea 6) --}}
                                        @if($cita->puedeIniciarAtencion())
                                            <button 
                                                type="button" 
                                                class="btn-iniciar-table" 
                                                onclick="iniciarAtencionPrompt({{ $cita->id }}, '{{ route('recepcion.citas.actualizar-estado', $cita->id) }}', '{{ $cita->folio }}', '{{ addslashes($cita->cliente ? trim($cita->cliente->name . ' ' . ($cita->cliente->apellidos ?? '')) : 'Cliente #' . $cita->user_id) }}')"
                                                title="Iniciar atención del servicio (Pasa a En proceso)"
                                            >
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="12" height="12">
                                                    <polygon points="5 3 19 12 5 21 5 3"></polygon>
                                                </svg>
                                                <span>Iniciar</span>
                                            </button>
                                        @endif

                                        {{-- 3. Finalizar Atención (Subtarea 6) --}}
                                        @if($cita->puedeFinalizarAtencion())
                                            <button 
                                                type="button" 
                                                class="btn-finalizar-table" 
                                                onclick="finalizarAtencionPrompt({{ $cita->id }}, '{{ route('recepcion.citas.actualizar-estado', $cita->id) }}', '{{ $cita->folio }}', '{{ addslashes($cita->cliente ? trim($cita->cliente->name . ' ' . ($cita->cliente->apellidos ?? '')) : 'Cliente #' . $cita->user_id) }}')"
                                                title="Finalizar atención del servicio (Pasa a Completada)"
                                            >
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="12" height="12">
                                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                                </svg>
                                                <span>Finalizar</span>
                                            </button>
                                        @endif

                                        {{-- 4. Reagendar si está cancelada o vencida --}}
                                        @if(in_array(strtolower($cita->estado), ['cancelada', 'vencida']))
                                            <button 
                                                type="button" 
                                                class="btn-reagendar-table" 
                                                onclick="abrirModalEditarCita({{ $cita->id }}, '{{ route('recepcion.citas.update', $cita->id) }}', '{{ route('recepcion.citas.detalle', $cita->id) }}')"
                                                title="Reagendar esta cita con nuevo horario"
                                            >
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="12" height="12">
                                                    <path d="M21 2v6h-6"></path>
                                                    <path d="M3 12a9 9 0 0 1 15-6.7L21 8"></path>
                                                    <path d="M3 22v-6h6"></path>
                                                    <path d="M21 12a9 9 0 0 1-15 6.7L3 16"></path>
                                                </svg>
                                                <span>Reagendar</span>
                                            </button>
                                        @endif

                                        {{-- 5. Mini-Toolbar Compacto de Herramientas (Detalle, Editar, Cancelar) --}}
                                        <div class="table-tool-group">
                                            {{-- Ver Detalle --}}
                                            <button 
                                                type="button" 
                                                class="btn-tool-icon tool-detalles" 
                                                onclick="abrirModalDetalleCita({{ $cita->id }}, '{{ route('recepcion.citas.detalle', $cita->id) }}')"
                                                title="Consultar detalles completos de la cita"
                                                aria-label="Ver detalles"
                                            >
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                            </button>

                                            {{-- Editar (solo si no es completada, cancelada ni vencida) --}}
                                            @if(!in_array(strtolower($cita->estado), ['completada', 'cancelada', 'vencida']))
                                                <button 
                                                    type="button" 
                                                    class="btn-tool-icon tool-editar" 
                                                    onclick="abrirModalEditarCita({{ $cita->id }}, '{{ route('recepcion.citas.update', $cita->id) }}', '{{ route('recepcion.citas.detalle', $cita->id) }}')"
                                                    title="Modificar servicio, especialista o fecha/hora"
                                                    aria-label="Editar cita"
                                                >
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13">
                                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                    </svg>
                                                </button>
                                            @endif

                                            {{-- Cancelar: disponible mientras no esté completada ni cancelada --}}
                                            @if(!in_array(strtolower($cita->estado), ['cancelada', 'completada']))
                                                <button 
                                                    type="button" 
                                                    class="btn-tool-icon tool-cancelar" 
                                                    onclick="cancelarCitaPrompt({{ $cita->id }}, '{{ route('recepcion.citas.cancelar', $cita->id) }}', '{{ $cita->folio }}')"
                                                    title="Cancelar cita y liberar horario"
                                                    aria-label="Cancelar cita"
                                                >
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13">
                                                        <circle cx="12" cy="12" r="10"></circle>
                                                        <line x1="15" y1="9" x2="9" y2="15"></line>
                                                        <line x1="9" y1="9" x2="15" y2="15"></line>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- MODAL DE CONSULTA DE DETALLE --}}
    @include('partials.modal-detalle-cita')

    {{-- MODAL DE EDICIÓN Y GESTIÓN --}}
    @include('partials.modal-editar-cita')
@endsection
