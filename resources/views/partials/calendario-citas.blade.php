{{-- COMPONENTE REUTILIZABLE: CALENDARIO INTERACTIVO DE CITAS --}}
@php
    $apiEventos = $apiEventosUrl ?? route('recepcion.calendario.eventos');
    $apiDetalleBase = $apiDetalleUrlBase ?? url('/recepcion/citas');
    $apiEstadoBase = $apiEstadoUrlBase ?? url('/recepcion/citas');
    $urlAgendar = $nuevaCitaUrl ?? ((isset($rolUsuario) && $rolUsuario === 'admin') ? null : route('recepcion.agendamiento'));
@endphp

<div class="calendario-component-container" id="calendarioInteractivoApp">
    {{-- BARRA SUPERIOR DE ENCABEZADO Y ACCIONES --}}
    <div class="cal-top-header">
        <div class="cal-title-group">
            <div class="cal-badge-pill">
                <span class="cal-status-pulse"></span>
                <span>En vivo • Base de Datos</span>
            </div>
            <h1 class="cal-main-title">Calendario Interactivo de Citas</h1>
            <p class="cal-subtitle">Consulta y gestión de citas en tiempo real por mes, semana y día con especialistas del estudio.</p>
        </div>

        <div class="cal-top-actions">
            <button type="button" class="btn-cal-refresh" id="btnCalRefresh" title="Actualizar datos del calendario">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                    <path d="M3 12a9 9 0 0 1 15-6.7L21 8"></path>
                    <path d="M21 3v5h-5"></path>
                    <path d="M21 12a9 9 0 0 1-15 6.7L3 16"></path>
                    <path d="M3 21v-5h5"></path>
                </svg>
                <span>Actualizar</span>
            </button>

            @if(!empty($urlAgendar) && (!isset($rolUsuario) || $rolUsuario !== 'admin'))
            <a href="{{ $urlAgendar }}" class="btn-cal-primary" id="btnCalAgendarCita">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="17" height="17">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>Agendar nueva cita</span>
            </a>
            @endif
        </div>
    </div>

    {{-- BARRA DE CONTROLES: NAVEGACIÓN, VISTAS Y FECHAS --}}
    <div class="cal-controls-card">
        <div class="cal-controls-row">
            {{-- Navegación temporal --}}
            <div class="cal-nav-group">
                <button type="button" class="cal-nav-btn" id="btnCalPrev" aria-label="Periodo anterior">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="18" height="18">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                </button>
                <button type="button" class="cal-today-btn" id="btnCalToday">Hoy</button>
                <button type="button" class="cal-nav-btn" id="btnCalNext" aria-label="Periodo siguiente">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="18" height="18">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </button>

                <div class="cal-datepicker-wrap">
                    <label for="calInputFechaDirecta" class="sr-only">Seleccionar fecha directa</label>
                    <input type="date" id="calInputFechaDirecta" class="cal-date-input" title="Saltar a fecha específica">
                </div>
            </div>

            {{-- Título destacado del periodo activo --}}
            <div class="cal-period-heading">
                <h2 id="calPeriodDisplayTitle" class="cal-period-title">Cargando periodo...</h2>
                <span id="calPeriodSubtext" class="cal-period-subtext">Sincronizando citas...</span>
            </div>

            {{-- Selector de tipo de vista (Mes / Semana / Día) --}}
            <div class="cal-view-selector" role="tablist" aria-label="Vistas del calendario">
                <button type="button" class="cal-view-btn active" id="btnViewMes" data-view="mes" role="tab" aria-selected="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15">
                        <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <span>Mes</span>
                </button>
                <button type="button" class="cal-view-btn" id="btnViewSemana" data-view="semana" role="tab" aria-selected="false">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15">
                        <rect x="3" y="3" width="7" height="18" rx="1"></rect>
                        <rect x="14" y="3" width="7" height="18" rx="1"></rect>
                    </svg>
                    <span>Semana</span>
                </button>
                <button type="button" class="cal-view-btn" id="btnViewDia" data-view="dia" role="tab" aria-selected="false">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    <span>Día</span>
                </button>
            </div>
        </div>

        {{-- BARRA DE FILTROS Y LEYENDA DE ESTADOS --}}
        <div class="cal-filters-row">
            <div class="cal-filter-inputs">
                {{-- Filtro por especialista --}}
                <div class="cal-select-wrap">
                    <label for="calSelectTrabajador" class="cal-filter-label">Especialista:</label>
                    <select id="calSelectTrabajador" class="cal-filter-select">
                        <option value="">Todos los Especialistas</option>
                        @isset($trabajadores)
                            @foreach($trabajadores as $t)
                                <option value="{{ $t->id }}">
                                    {{ $t->nombre_completo ?? ($t->user->name . ' ' . ($t->user->apellidos ?? '')) }}
                                </option>
                            @endforeach
                        @endisset
                    </select>
                </div>

                {{-- Filtro por estado --}}
                <div class="cal-select-wrap">
                    <label for="calSelectEstado" class="cal-filter-label">Estado:</label>
                    <select id="calSelectEstado" class="cal-filter-select">
                        <option value="todas" {{ (!request()->filled('estado') || request('estado') === 'todas') ? 'selected' : '' }}>Todos los Estados</option>
                        <option value="confirmada" {{ request('estado') === 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                        <option value="en_proceso" {{ in_array(request('estado'), ['en_proceso', 'en_atencion']) ? 'selected' : '' }}>En proceso / En atención</option>
                        <option value="completada" {{ request('estado') === 'completada' ? 'selected' : '' }}>Completada</option>
                        <option value="cancelada" {{ request('estado') === 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                        <option value="vencida" {{ request('estado') === 'vencida' ? 'selected' : '' }}>Vencida</option>
                    </select>
                </div>

                {{-- Búsqueda rápida por cliente o servicio --}}
                <div class="cal-search-wrap">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16" class="cal-search-icon">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" id="calSearchInput" class="cal-search-input" placeholder="Buscar cliente o servicio..." aria-label="Buscar cliente o servicio">
                </div>
            </div>

            {{-- Leyenda informativa de estados con contadores (Subtarea 5) --}}
            <div class="cal-status-legend" aria-label="Leyenda de estados">
                <span class="legend-chip legend-confirmada" title="Citas agendadas y activas">
                    <span class="legend-dot"></span> Confirmada (<strong id="cntConfirmada">0</strong>)
                </span>
                <span class="legend-chip legend-en_proceso" title="Citas en curso o en atención">
                    <span class="legend-dot"></span> En proceso (<strong id="cntEnProceso">0</strong>)
                </span>
                <span class="legend-chip legend-completada" title="Citas finalizadas con éxito">
                    <span class="legend-dot"></span> Completada (<strong id="cntCompletada">0</strong>)
                </span>
                <span class="legend-chip legend-cancelada" title="Citas anuladas o canceladas">
                    <span class="legend-dot"></span> Cancelada (<strong id="cntCancelada">0</strong>)
                </span>
                <span class="legend-chip legend-vencida" title="Citas no concluidas cuyo horario expiró">
                    <span class="legend-dot"></span> Vencida (<strong id="cntVencida">0</strong>)
                </span>
            </div>
        </div>
    </div>

    {{-- ÁREA DE VISTA DEL CALENDARIO (CONTENEDOR DINÁMICO) --}}
    <div class="cal-main-board" id="calMainBoard">
        {{-- Spinner de Carga --}}
        <div class="cal-loading-overlay" id="calLoadingOverlay" style="display: none;">
            <div class="cal-spinner"></div>
            <p>Consultando citas en la base de datos...</p>
        </div>

        {{-- CONTENEDOR VISTA MES --}}
        <div class="cal-view-container" id="calViewMesContainer">
            <div class="cal-month-grid-header">
                <div class="cal-day-col-title">Lunes</div>
                <div class="cal-day-col-title">Martes</div>
                <div class="cal-day-col-title">Miércoles</div>
                <div class="cal-day-col-title">Jueves</div>
                <div class="cal-day-col-title">Viernes</div>
                <div class="cal-day-col-title">Sábado</div>
                <div class="cal-day-col-title">Domingo</div>
            </div>
            <div class="cal-month-days-grid" id="calMonthDaysGrid">
                {{-- Renderizado dinámico vía JavaScript --}}
            </div>
        </div>

        {{-- CONTENEDOR VISTA SEMANA --}}
        <div class="cal-view-container" id="calViewSemanaContainer" style="display: none;">
            <div class="cal-week-board" id="calWeekBoard">
                {{-- Renderizado dinámico de 7 columnas vía JavaScript --}}
            </div>
        </div>

        {{-- CONTENEDOR VISTA DÍA (TIMELINE DETALLADO) --}}
        <div class="cal-view-container" id="calViewDiaContainer" style="display: none;">
            <div class="cal-day-timeline-card">
                <div class="cal-day-header-summary" id="calDayHeaderSummary">
                    {{-- Información del día seleccionado --}}
                </div>
                <div class="cal-day-hours-list" id="calDayHoursList">
                    {{-- Renderizado dinámico de bloques horarios vía JavaScript --}}
                </div>
            </div>
        </div>

        {{-- ESTADO VACÍO (CUANDO NO HAY CITAS EN EL PERIODO) --}}
        <div class="cal-empty-state" id="calEmptyState" style="display: none;">
            <div class="cal-empty-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="48" height="48">
                    <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
            </div>
            <h3 class="cal-empty-title">No hay citas registradas en este periodo</h3>
            <p class="cal-empty-desc" id="calEmptyDesc">No se encontraron reservaciones con los filtros y fechas seleccionadas. Puedes agendar una cita o cambiar de periodo.</p>
            <div class="cal-empty-actions">
                <a href="{{ $urlAgendar }}" class="btn-cal-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="16" height="16">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    <span>Agendar Cita en esta fecha</span>
                </a>
            </div>
        </div>
    </div>

    {{-- MODAL INTERACTIVO: DETALLE DE CITA Y GESTIÓN DE ESTADO --}}
    <div class="cal-modal-backdrop" id="calCitaModal" style="display: none;" role="dialog" aria-modal="true" aria-labelledby="modalCitaTitle">
        <div class="cal-modal-card">
            {{-- Encabezado del modal --}}
            <div class="cal-modal-header">
                <div class="cal-modal-header-info">
                    <div class="cal-modal-pretitle">
                        <span id="modalCitaFolio">CITA-00000</span>
                        <span class="cal-modal-status-badge" id="modalCitaStatusBadge">Confirmada</span>
                    </div>
                    <h3 class="cal-modal-title" id="modalCitaTitle">Detalle de la Cita</h3>
                </div>
                <button type="button" class="cal-modal-close-btn" id="btnCerrarModalCita" aria-label="Cerrar modal">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="20" height="20">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            {{-- Cuerpo del modal --}}
            <div class="cal-modal-body">
                <div class="cal-modal-grid">
                    {{-- Tarjeta 1: Fecha y Horario --}}
                    <div class="cal-info-item">
                        <div class="cal-info-icon cal-icon-blue">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </div>
                        <div class="cal-info-content">
                            <span class="cal-info-label">Fecha y Horario</span>
                            <strong class="cal-info-val" id="modalCitaHorario">10:00 - 10:45</strong>
                            <span class="cal-info-sub" id="modalCitaFechaTexto">Miércoles 14 de Octubre</span>
                            <span class="cal-info-badge" id="modalCitaDuracion">45 min</span>
                        </div>
                    </div>

                    {{-- Tarjeta 2: Cliente --}}
                    <div class="cal-info-item">
                        <div class="cal-info-icon cal-icon-purple">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>
                        <div class="cal-info-content">
                            <span class="cal-info-label">Cliente</span>
                            <strong class="cal-info-val" id="modalCitaClienteNombre">Roberto Sánchez</strong>
                            <span class="cal-info-sub" id="modalCitaClienteTel">Tel: 555-123-4567</span>
                            <span class="cal-info-sub" id="modalCitaClienteEmail">cliente@email.com</span>
                        </div>
                    </div>

                    {{-- Tarjeta 3: Especialista --}}
                    <div class="cal-info-item">
                        <div class="cal-info-icon cal-icon-cyan">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                                <rect x="2" y="7" width="20" height="14" rx="2"></rect>
                                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                            </svg>
                        </div>
                        <div class="cal-info-content">
                            <span class="cal-info-label">Especialista Asignado</span>
                            <strong class="cal-info-val" id="modalCitaEspecialista">Alejandro Torres</strong>
                            <span class="cal-info-sub">Personal Profesional del Estudio</span>
                        </div>
                    </div>

                    {{-- Tarjeta 4: Servicio y Precio --}}
                    <div class="cal-info-item">
                        <div class="cal-info-icon cal-icon-amber">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M12 6v12M15 9.5c0-1.5-1.5-2-3-2s-3 .5-3 2 1.5 2 3 2 3 .5 3 2-1.5 2-3 2-3-.5-3-2"></path>
                            </svg>
                        </div>
                        <div class="cal-info-content">
                            <span class="cal-info-label">Servicio Contratado</span>
                            <strong class="cal-info-val" id="modalCitaServicio">Corte Clásico</strong>
                            <span class="cal-info-sub" id="modalCitaCategoria">Barbería</span>
                            <span class="cal-info-price" id="modalCitaPrecio">$250.00</span>
                        </div>
                    </div>
                </div>

                {{-- Sección de Check-in de Llegada (Subtarea 5) --}}
                <div class="cal-checkin-container" id="modalCalCheckInContainer">
                    <div class="cal-checkin-status-info">
                        <div class="cal-checkin-icon-circle" id="modalCalCheckInIcon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="16" height="16">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </div>
                        <div class="cal-checkin-text-block">
                            <span class="cal-checkin-heading" id="modalCalCheckInTitulo">Check-in de Llegada</span>
                            <span class="cal-checkin-detail" id="modalCalCheckInSub">Pendiente de llegada en recepción</span>
                        </div>
                    </div>
                    @if(!isset($rolUsuario) || $rolUsuario !== 'admin')
                    <button type="button" class="btn-cal-checkin-action" id="btnCalModalCheckIn" style="display: none;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="15" height="15">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span>Registrar Check-in</span>
                    </button>
                    @endif
                </div>

                {{-- Notas y Observaciones --}}
                <div class="cal-notes-section">
                    <div class="cal-note-block">
                        <span class="cal-note-title">Notas de la Reservación:</span>
                        <p class="cal-note-text" id="modalCitaNotas">Sin notas adicionales.</p>
                    </div>

                    <div class="cal-note-block">
                        <span class="cal-note-title">Observaciones Técnicas del Servicio:</span>
                        <textarea id="modalCitaObservacionesInput" class="cal-textarea-obs" rows="3" placeholder="{{ (isset($rolUsuario) && $rolUsuario === 'admin') ? 'Sin observaciones registradas por el personal de atención.' : 'Registrar observaciones del corte, estilo o requerimientos especiales del cliente...' }}" {{ (isset($rolUsuario) && $rolUsuario === 'admin') ? 'readonly' : '' }}></textarea>
                    </div>
                </div>

                @if(isset($rolUsuario) && $rolUsuario === 'admin')
                    {{-- Bloque Informativo de Solo Lectura para el Administrador --}}
                    <div style="display: flex; align-items: center; justify-content: center; gap: 8px; padding: 12px 16px; background: rgba(56, 189, 248, 0.08); border: 1px solid rgba(56, 189, 248, 0.25); border-radius: 12px; color: #7dd3fc; font-size: 13px; font-weight: 600;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        <span>Modo Consulta • Solo visualización de datos registrados por recepción</span>
                    </div>
                @else
                    {{-- Formulario para cambiar estado (Criterio 5) --}}
                    <div class="cal-status-change-box">
                        <div class="cal-status-select-wrap">
                            <label for="modalSelectNuevoEstado" class="cal-status-label">Actualizar Estado de la Cita:</label>
                            <select id="modalSelectNuevoEstado" class="cal-select-status-action">
                                <option value="confirmada">Confirmada</option>
                                <option value="en_proceso">En proceso / En atención</option>
                                <option value="completada">Completada</option>
                                <option value="cancelada">Cancelada</option>
                            </select>
                        </div>
                        <button type="button" class="btn-cal-save-status" id="btnGuardarCambioEstado">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="16" height="16">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            <span>Guardar Cambios</span>
                        </button>
                    </div>
                @endif


            </div>
        </div>
    </div>
</div>

{{-- ESTILOS EXCLUSIVOS DEL CALENDARIO INTERACTIVO (VANILLA CSS MODERNO Y ELEGANTE) --}}
<style>
    /* ===================================================
       SISTEMA DE DISEÑO DEL CALENDARIO INTERACTIVO
       Coherente con Beauty & Barber Studio (Red, White, Blue)
       =================================================== */
    .calendario-component-container {
        display: flex;
        flex-direction: column;
        gap: 24px;
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
    }

    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        border: 0;
    }

    /* ENCABEZADO SUPERIOR */
    .cal-top-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
    }

    .cal-title-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .cal-badge-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(37, 99, 235, 0.12);
        color: #60a5fa;
        border: 1px solid rgba(37, 99, 235, 0.28);
        padding: 3px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        width: fit-content;
    }

    .cal-status-pulse {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background-color: #10b981;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
    }

    .cal-main-title {
        font-size: 26px;
        font-weight: 800;
        color: var(--text-white, #ffffff);
        letter-spacing: -0.02em;
        margin: 0;
    }

    .cal-subtitle {
        font-size: 14px;
        color: var(--text-muted, #94a3b8);
        margin: 0;
    }

    .cal-top-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .btn-cal-refresh {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        background: rgba(255, 255, 255, 0.05);
        color: var(--text-main, #f8fafc);
        border: 1px solid var(--border-subtle, rgba(255, 255, 255, 0.08));
        border-radius: 10px;
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-cal-refresh:hover {
        background: rgba(255, 255, 255, 0.09);
        border-color: rgba(255, 255, 255, 0.2);
        transform: translateY(-1px);
    }

    .btn-cal-primary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 18px;
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

    .btn-cal-primary:hover {
        background: #1d4ed8;
        color: #ffffff;
        border-color: rgba(147, 197, 253, 0.45);
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
    }

    /* BARRA DE CONTROLES PRINCIPAL */
    .cal-controls-card {
        background: var(--bg-card, #0a1124);
        border: 1px solid var(--border-subtle, rgba(255, 255, 255, 0.08));
        border-radius: 16px;
        padding: 20px 24px;
        display: flex;
        flex-direction: column;
        gap: 18px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
    }

    .cal-controls-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--border-subtle, rgba(255, 255, 255, 0.06));
    }

    /* Navegación */
    .cal-nav-group {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .cal-nav-btn {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid var(--border-subtle, rgba(255, 255, 255, 0.08));
        border-radius: 8px;
        color: var(--text-main, #f8fafc);
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .cal-nav-btn:hover {
        background: var(--barber-blue-pale, rgba(37, 99, 235, 0.12));
        border-color: rgba(37, 99, 235, 0.28);
        color: #60a5fa;
        transform: scale(1.05);
    }

    .cal-today-btn {
        padding: 0 14px;
        height: 36px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid var(--border-subtle, rgba(255, 255, 255, 0.08));
        border-radius: 8px;
        color: var(--text-main, #f8fafc);
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .cal-today-btn:hover {
        background: rgba(255, 255, 255, 0.09);
        border-color: rgba(255, 255, 255, 0.2);
    }

    .cal-date-input {
        height: 36px;
        padding: 0 10px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid var(--border-subtle, rgba(255, 255, 255, 0.08));
        border-radius: 8px;
        color: var(--text-main, #f8fafc);
        font-size: 13px;
        font-family: inherit;
        outline: none;
        cursor: pointer;
        transition: all 0.2s ease;
        color-scheme: dark;
    }

    .cal-date-input:focus {
        border-color: var(--barber-blue, #1e40af);
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.25);
    }

    /* Título del Periodo */
    .cal-period-heading {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .cal-period-title {
        font-size: 20px;
        font-weight: 800;
        color: var(--text-white, #ffffff);
        letter-spacing: -0.01em;
        margin: 0;
    }

    .cal-period-subtext {
        font-size: 12px;
        color: var(--text-muted, #94a3b8);
        font-weight: 500;
    }

    /* Selector de Vistas */
    .cal-view-selector {
        display: inline-flex;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid var(--border-subtle, rgba(255, 255, 255, 0.08));
        border-radius: 10px;
        padding: 3px;
        gap: 3px;
    }

    .cal-view-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 14px;
        background: transparent;
        border: none;
        border-radius: 7px;
        color: var(--text-muted, #94a3b8);
        font-size: 12.5px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .cal-view-btn:hover {
        color: var(--text-main, #f8fafc);
    }

    .cal-view-btn.active {
        background: #1e3a8a;
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.12);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.25);
    }

    /* Filtros y Leyenda */
    .cal-filters-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
    }

    .cal-filter-inputs {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }

    .cal-select-wrap {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .cal-filter-label {
        font-size: 12px;
        font-weight: 700;
        color: var(--text-muted, #94a3b8);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .cal-filter-select {
        height: 36px;
        padding: 0 28px 0 12px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid var(--border-subtle, rgba(255, 255, 255, 0.08));
        border-radius: 8px;
        color: var(--text-main, #f8fafc);
        font-size: 13px;
        font-family: inherit;
        outline: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .cal-filter-select:focus {
        border-color: var(--barber-blue, #1e40af);
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.25);
    }

    .cal-filter-select option {
        background: #0a1124;
        color: #f8fafc;
    }

    .cal-search-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }

    .cal-search-icon {
        position: absolute;
        left: 10px;
        color: var(--text-muted, #94a3b8);
        pointer-events: none;
    }

    .cal-search-input {
        height: 36px;
        padding: 0 12px 0 34px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid var(--border-subtle, rgba(255, 255, 255, 0.08));
        border-radius: 8px;
        color: var(--text-main, #f8fafc);
        font-size: 13px;
        font-family: inherit;
        outline: none;
        width: 220px;
        transition: all 0.2s ease;
    }

    .cal-search-input:focus {
        width: 260px;
        border-color: var(--barber-blue, #1e40af);
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.25);
    }

    /* Leyenda de Estados */
    .cal-status-legend {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }

    .legend-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 600;
        cursor: default;
        user-select: none;
    }

    .legend-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
    }

    .legend-confirmada {
        background: rgba(16, 185, 129, 0.12);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.25);
    }
    .legend-confirmada .legend-dot { background-color: #10b981; }

    .legend-en_proceso {
        background: rgba(37, 99, 235, 0.12);
        color: #60a5fa;
        border: 1px solid rgba(37, 99, 235, 0.25);
    }
    .legend-en_proceso .legend-dot { background-color: #60a5fa; }

    .legend-completada {
        background: rgba(13, 148, 136, 0.12);
        color: #2dd4bf;
        border: 1px solid rgba(13, 148, 136, 0.25);
    }
    .legend-completada .legend-dot { background-color: #2dd4bf; }

    .legend-cancelada {
        background: rgba(220, 38, 38, 0.12);
        color: #f87171;
        border: 1px solid rgba(220, 38, 38, 0.25);
    }
    .legend-cancelada .legend-dot { background-color: #f87171; }

    .legend-vencida {
        background: rgba(245, 158, 11, 0.12);
        color: #f59e0b;
        border: 1px solid rgba(245, 158, 11, 0.25);
    }
    .legend-vencida .legend-dot { background-color: #f59e0b; }

    /* TABLERO PRINCIPAL */
    .cal-main-board {
        position: relative;
        background: var(--bg-card, #0a1124);
        border: 1px solid var(--border-subtle, rgba(255, 255, 255, 0.08));
        border-radius: 16px;
        padding: 24px;
        min-height: 480px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
        overflow-x: auto;
    }

    /* SPINNER */
    .cal-loading-overlay {
        position: absolute;
        inset: 0;
        background: rgba(10, 17, 36, 0.85);
        backdrop-filter: blur(4px);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 14px;
        z-index: 20;
        border-radius: 16px;
        color: var(--text-main, #f8fafc);
        font-weight: 600;
        font-size: 14px;
    }

    .cal-spinner {
        width: 38px;
        height: 38px;
        border: 3px solid rgba(37, 99, 235, 0.2);
        border-top-color: var(--barber-blue, #1e40af);
        border-radius: 50%;
        animation: calSpin 0.7s linear infinite;
    }

    @keyframes calSpin {
        to { transform: rotate(360deg); }
    }

    /* ==========================================
       VISTA MES
       ========================================== */
    .cal-month-grid-header {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 10px;
        margin-bottom: 10px;
        text-align: center;
    }

    .cal-day-col-title {
        font-size: 12px;
        font-weight: 700;
        color: var(--text-muted, #94a3b8);
        text-transform: uppercase;
        letter-spacing: 0.08em;
        padding: 8px 0;
    }

    .cal-month-days-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 10px;
    }

    .cal-month-cell {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid var(--border-subtle, rgba(255, 255, 255, 0.06));
        border-radius: 12px;
        min-height: 118px;
        height: 126px;
        padding: 8px 9px;
        display: flex;
        flex-direction: column;
        gap: 5px;
        transition: background 0.15s ease, border-color 0.15s ease;
        position: relative;
        overflow: hidden !important; /* Nunca scrollbars nativos en las celdas del mes */
        box-sizing: border-box;
    }

    .cal-month-cell:hover {
        background: rgba(255, 255, 255, 0.05);
        border-color: rgba(255, 255, 255, 0.18);
    }

    .cal-month-cell.different-month {
        opacity: 0.3;
        background: transparent;
    }

    .cal-month-cell.is-today {
        border-color: rgba(37, 99, 235, 0.5);
        background: rgba(37, 99, 235, 0.06);
    }

    .cal-month-cell-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 2px;
    }

    .cal-day-num {
        font-size: 13px;
        font-weight: 700;
        color: var(--text-main, #f8fafc);
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
    }

    .cal-month-cell.is-today .cal-day-num {
        background: var(--barber-blue, #1e40af);
        color: #ffffff;
    }

    .cal-day-count-badge {
        font-size: 10.5px;
        font-weight: 700;
        background: rgba(255, 255, 255, 0.08);
        color: var(--text-muted, #94a3b8);
        padding: 1px 7px;
        border-radius: 10px;
    }

    .cal-cell-events-list {
        display: flex;
        flex-direction: column;
        gap: 4px;
        overflow: hidden !important; /* Cero barras de scroll internas */
        flex: 1;
        min-height: 0;
        width: 100%;
        box-sizing: border-box;
    }

    /* Event Chip en Celda (Fijo, elegante y sin jitter) */
    .cal-event-chip {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        text-align: left;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        width: 100%;
        box-sizing: border-box;
        line-height: 1.25;
        transition: background 0.15s ease, filter 0.15s ease, box-shadow 0.15s ease;
    }

    .cal-event-chip:hover {
        filter: brightness(1.25);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
    }

    .chip-time {
        font-weight: 800;
        font-size: 10.5px;
        flex-shrink: 0;
    }

    .chip-client {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        flex: 1;
        min-width: 0;
    }

    /* Clases de estado para eventos */
    .status-bg-confirmada {
        background: rgba(16, 185, 129, 0.18);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.35);
    }

    .status-bg-en_proceso {
        background: rgba(37, 99, 235, 0.18);
        color: #60a5fa;
        border: 1px solid rgba(37, 99, 235, 0.35);
    }

    .status-bg-completada {
        background: rgba(13, 148, 136, 0.18);
        color: #2dd4bf;
        border: 1px solid rgba(13, 148, 136, 0.35);
    }

    .status-bg-cancelada {
        background: rgba(220, 38, 38, 0.18);
        color: #f87171;
        border: 1px solid rgba(220, 38, 38, 0.35);
        text-decoration: line-through;
    }

    .status-bg-vencida {
        background: rgba(245, 158, 11, 0.18);
        color: #f59e0b;
        border: 1px solid rgba(245, 158, 11, 0.35);
    }

    .cal-more-events-btn {
        font-size: 10.5px;
        font-weight: 700;
        color: #93c5fd;
        background: rgba(37, 99, 235, 0.12);
        border: 1px solid rgba(37, 99, 235, 0.25);
        border-radius: 4px;
        cursor: pointer;
        text-align: center;
        padding: 2px 6px;
        margin-top: auto;
        width: 100%;
        box-sizing: border-box;
        transition: all 0.15s ease;
    }

    .cal-more-events-btn:hover {
        background: rgba(37, 99, 235, 0.25);
        color: #ffffff;
        border-color: rgba(37, 99, 235, 0.5);
    }

    /* Scrollbars oscuros y estilizados para todo el calendario */
    #calendarioInteractivoApp *::-webkit-scrollbar {
        width: 5px;
        height: 5px;
    }
    #calendarioInteractivoApp *::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.02);
        border-radius: 10px;
    }
    #calendarioInteractivoApp *::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.16);
        border-radius: 10px;
    }
    #calendarioInteractivoApp *::-webkit-scrollbar-thumb:hover {
        background: rgba(37, 99, 235, 0.5);
    }
    #calendarioInteractivoApp * {
        scrollbar-width: thin;
        scrollbar-color: rgba(255, 255, 255, 0.16) rgba(255, 255, 255, 0.02);
    }

    /* ==========================================
       VISTA SEMANA
       ========================================== */
    .cal-week-board {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 12px;
        min-width: 840px;
    }

    .cal-week-column {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid var(--border-subtle, rgba(255, 255, 255, 0.06));
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .cal-week-col-header {
        padding: 12px;
        background: rgba(255, 255, 255, 0.03);
        border-bottom: 1px solid var(--border-subtle, rgba(255, 255, 255, 0.06));
        text-align: center;
    }

    .cal-week-col-header.is-today {
        background: rgba(37, 99, 235, 0.15);
        border-bottom-color: rgba(37, 99, 235, 0.3);
    }

    .cal-week-day-name {
        font-size: 11px;
        font-weight: 700;
        color: var(--text-muted, #94a3b8);
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .cal-week-day-number {
        font-size: 18px;
        font-weight: 800;
        color: var(--text-white, #ffffff);
        margin-top: 2px;
    }

    .cal-week-col-body {
        padding: 10px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        min-height: 380px;
        overflow-y: auto;
    }

    /* Tarjeta de Cita en Vista Semana */
    .cal-week-event-card {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid var(--border-subtle, rgba(255, 255, 255, 0.08));
        border-radius: 10px;
        padding: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .cal-week-event-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        border-color: rgba(255, 255, 255, 0.2);
    }

    .cal-card-top-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 6px;
    }

    .cal-card-time {
        font-size: 11.5px;
        font-weight: 800;
        color: var(--text-white, #ffffff);
    }

    .cal-card-status-pill {
        font-size: 10px;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 12px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .cal-card-client {
        font-size: 13px;
        font-weight: 700;
        color: var(--text-main, #f8fafc);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .cal-card-service {
        font-size: 12px;
        color: var(--text-muted, #94a3b8);
    }

    .cal-card-worker {
        font-size: 11px;
        color: #60a5fa;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* ==========================================
       VISTA DÍA (TIMELINE DETALLADO)
       ========================================== */
    .cal-day-timeline-card {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .cal-day-header-summary {
        padding: 16px 20px;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid var(--border-subtle, rgba(255, 255, 255, 0.06));
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }

    .cal-day-summary-left h3 {
        font-size: 18px;
        font-weight: 800;
        color: var(--text-white, #ffffff);
        margin: 0;
    }

    .cal-day-summary-left p {
        font-size: 13px;
        color: var(--text-muted, #94a3b8);
        margin: 2px 0 0 0;
    }

    .cal-day-hours-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .cal-timeline-hour-row {
        display: grid;
        grid-template-columns: 80px 1fr;
        gap: 16px;
        align-items: flex-start;
        padding: 8px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.03);
    }

    .cal-hour-label {
        font-size: 13px;
        font-weight: 700;
        color: var(--text-muted, #94a3b8);
        padding-top: 6px;
        text-align: right;
    }

    .cal-hour-events-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .cal-day-event-card {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid var(--border-subtle, rgba(255, 255, 255, 0.08));
        border-radius: 12px;
        padding: 14px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .cal-day-event-card:hover {
        background: rgba(255, 255, 255, 0.05);
        border-color: rgba(37, 99, 235, 0.3);
        transform: translateX(4px);
    }

    .cal-day-event-left {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .cal-day-event-time-badge {
        font-size: 13.5px;
        font-weight: 800;
        color: var(--text-white, #ffffff);
        background: rgba(255, 255, 255, 0.06);
        padding: 6px 12px;
        border-radius: 8px;
        white-space: nowrap;
    }

    .cal-day-event-main-info {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .cal-day-client-name {
        font-size: 15px;
        font-weight: 800;
        color: var(--text-white, #ffffff);
    }

    .cal-day-service-name {
        font-size: 13px;
        color: var(--text-muted, #94a3b8);
    }

    .cal-day-worker-tag {
        font-size: 12px;
        color: #60a5fa;
        font-weight: 600;
    }

    .cal-day-event-right {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .cal-day-price-tag {
        font-size: 16px;
        font-weight: 800;
        color: #60a5fa;
    }

    .cal-hour-empty-slot {
        padding: 8px 12px;
        color: rgba(255, 255, 255, 0.2);
        font-size: 12.5px;
        font-style: italic;
    }

    /* ESTILOS PARA DÍAS NO LABORABLES (SUBTAREA 6) */
    .cal-month-cell.is-closed {
        background: repeating-linear-gradient(135deg, rgba(15, 23, 42, 0.75), rgba(15, 23, 42, 0.75) 12px, rgba(30, 41, 59, 0.4) 12px, rgba(30, 41, 59, 0.4) 24px) !important;
        border-color: rgba(239, 68, 68, 0.22) !important;
        cursor: not-allowed !important;
    }

    .cal-month-cell.is-closed:hover {
        background: repeating-linear-gradient(135deg, rgba(15, 23, 42, 0.85), rgba(15, 23, 42, 0.85) 12px, rgba(30, 41, 59, 0.5) 12px, rgba(30, 41, 59, 0.5) 24px) !important;
        border-color: rgba(239, 68, 68, 0.38) !important;
    }

    .cal-month-closed-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: rgba(239, 68, 68, 0.15);
        color: #fca5a5;
        border: 1px solid rgba(239, 68, 68, 0.3);
        border-radius: 6px;
        padding: 1px 6px;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.02em;
    }

    .cal-month-closed-banner {
        padding: 6px 8px;
        background: rgba(239, 68, 68, 0.08);
        border: 1px dashed rgba(239, 68, 68, 0.25);
        border-radius: 8px;
        color: #fca5a5;
        font-size: 11px;
        font-weight: 700;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        box-sizing: border-box;
        margin-top: auto;
    }

    .cal-week-column.is-closed {
        background: repeating-linear-gradient(135deg, rgba(15, 23, 42, 0.65), rgba(15, 23, 42, 0.65) 14px, rgba(30, 41, 59, 0.3) 14px, rgba(30, 41, 59, 0.3) 28px) !important;
        border-color: rgba(239, 68, 68, 0.25) !important;
        cursor: not-allowed !important;
    }

    .cal-week-col-header.is-closed {
        background: rgba(239, 68, 68, 0.08) !important;
        border-bottom-color: rgba(239, 68, 68, 0.25) !important;
    }

    .cal-week-closed-header-badge {
        font-size: 10.5px;
        font-weight: 700;
        color: #fca5a5;
        background: rgba(239, 68, 68, 0.14);
        border: 1px solid rgba(239, 68, 68, 0.28);
        border-radius: 10px;
        padding: 2px 8px;
        margin-top: 6px;
        display: inline-block;
        letter-spacing: 0.02em;
    }

    .cal-week-closed-notice {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 32px 14px;
        background: rgba(239, 68, 68, 0.05);
        border: 1px dashed rgba(239, 68, 68, 0.25);
        border-radius: 10px;
        margin-top: 10px;
        gap: 8px;
    }

    .cal-closed-lock-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: rgba(239, 68, 68, 0.15);
        color: #f87171;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .cal-closed-notice-title {
        font-size: 13.5px;
        font-weight: 800;
        color: #fca5a5;
        letter-spacing: 0.01em;
    }

    .cal-closed-notice-sub {
        font-size: 11.5px;
        color: var(--text-muted, #94a3b8);
    }

    .cal-badge-closed {
        background: rgba(239, 68, 68, 0.15) !important;
        color: #fca5a5 !important;
        border-color: rgba(239, 68, 68, 0.35) !important;
    }

    .cal-day-closed-card {
        padding: 48px 24px;
        background: rgba(239, 68, 68, 0.04);
        border: 1px dashed rgba(239, 68, 68, 0.28);
        border-radius: 14px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 12px;
    }

    .cal-day-closed-icon {
        width: 64px;
        height: 64px;
        border-radius: 18px;
        background: rgba(239, 68, 68, 0.12);
        color: #f87171;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .cal-day-closed-title {
        font-size: 20px;
        font-weight: 800;
        color: #ffffff;
        margin: 0;
    }

    .cal-day-closed-desc {
        font-size: 13.5px;
        color: #94a3b8;
        max-width: 520px;
        margin: 0;
        line-height: 1.5;
    }

    .cal-day-closed-warning {
        color: #fca5a5 !important;
    }

    .btn-cal-primary.is-disabled-closed {
        opacity: 0.45 !important;
        cursor: not-allowed !important;
        filter: grayscale(0.85) !important;
    }

    .cal-hour-empty-slot.is-outside-hours {
        color: rgba(148, 163, 184, 0.4);
        background: rgba(255, 255, 255, 0.01);
        border-radius: 6px;
    }

    /* ESTADO VACÍO */
    .cal-empty-state {
        padding: 56px 20px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 14px;
    }

    .cal-empty-icon {
        width: 72px;
        height: 72px;
        border-radius: 20px;
        background: var(--barber-blue-pale, rgba(37, 99, 235, 0.15));
        color: #60a5fa;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .cal-empty-title {
        font-size: 18px;
        font-weight: 800;
        color: var(--text-white, #ffffff);
        margin: 0;
    }

    .cal-empty-desc {
        font-size: 13.5px;
        color: var(--text-muted, #94a3b8);
        max-width: 440px;
        margin: 0;
    }

    /* ==========================================
       MODAL DE DETALLE Y GESTIÓN DE CITA
       ========================================== */
    .cal-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.75);
        backdrop-filter: blur(8px);
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .cal-modal-card {
        background: #0a1124;
        border: 1px solid var(--border-subtle, rgba(255, 255, 255, 0.1));
        border-radius: 18px;
        width: 100%;
        max-width: 640px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.6);
        display: flex;
        flex-direction: column;
        animation: calModalFadeIn 0.25s var(--ease-fluid, cubic-bezier(0.25, 1, 0.5, 1));
    }

    @keyframes calModalFadeIn {
        from { opacity: 0; transform: scale(0.96) translateY(10px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }

    .cal-modal-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border-subtle, rgba(255, 255, 255, 0.08));
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .cal-modal-pretitle {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 4px;
        font-size: 12px;
        color: var(--text-muted, #94a3b8);
        font-weight: 700;
    }

    .cal-modal-status-badge {
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .cal-modal-title {
        font-size: 20px;
        font-weight: 800;
        color: var(--text-white, #ffffff);
        margin: 0;
    }

    .cal-modal-close-btn {
        background: transparent;
        border: none;
        color: var(--text-muted, #94a3b8);
        cursor: pointer;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .cal-modal-close-btn:hover {
        background: rgba(255, 255, 255, 0.08);
        color: var(--text-white, #ffffff);
    }

    .cal-modal-body {
        padding: 24px;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .cal-modal-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    @media (max-width: 600px) {
        .cal-modal-grid {
            grid-template-columns: 1fr;
        }
    }

    .cal-info-item {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid var(--border-subtle, rgba(255, 255, 255, 0.06));
        border-radius: 12px;
        padding: 14px;
        display: flex;
        gap: 12px;
    }

    .cal-info-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .cal-icon-blue { background: rgba(37, 99, 235, 0.12); color: #60a5fa; }
    .cal-icon-purple { background: rgba(139, 92, 246, 0.12); color: #a78bfa; }
    .cal-icon-cyan { background: rgba(13, 148, 136, 0.12); color: #2dd4bf; }
    .cal-icon-amber { background: rgba(245, 158, 11, 0.12); color: #f59e0b; }

    .cal-info-content {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .cal-info-label {
        font-size: 11px;
        font-weight: 700;
        color: var(--text-muted, #94a3b8);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .cal-info-val {
        font-size: 14px;
        font-weight: 800;
        color: var(--text-white, #ffffff);
    }

    .cal-info-sub {
        font-size: 12px;
        color: var(--text-muted, #94a3b8);
    }

    .cal-info-badge {
        margin-top: 4px;
        font-size: 11px;
        font-weight: 700;
        background: rgba(255, 255, 255, 0.08);
        color: var(--text-main, #f8fafc);
        padding: 2px 6px;
        border-radius: 6px;
        width: fit-content;
    }

    .cal-info-price {
        font-size: 15px;
        font-weight: 800;
        color: #60a5fa;
        margin-top: 4px;
    }

    /* Check-in de Llegada (Subtarea 5) */
    .cal-checkin-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 14px 18px;
        background: rgba(16, 185, 129, 0.06);
        border: 1px solid rgba(16, 185, 129, 0.2);
        border-radius: 12px;
        flex-wrap: wrap;
    }

    .cal-checkin-status-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .cal-checkin-icon-circle {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(16, 185, 129, 0.15);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.3);
        flex-shrink: 0;
    }

    .cal-checkin-icon-circle.pending {
        background: rgba(148, 163, 184, 0.1);
        color: #94a3b8;
        border-color: rgba(148, 163, 184, 0.25);
    }

    .cal-checkin-text-block {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .cal-checkin-heading {
        font-size: 13.5px;
        font-weight: 700;
        color: #ffffff;
    }

    .cal-checkin-detail {
        font-size: 12px;
        color: #94a3b8;
    }

    .btn-cal-checkin-action {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        background: #1e40af;
        color: #ffffff;
        border: 1px solid rgba(147, 197, 253, 0.28);
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.35);
    }

    .btn-cal-checkin-action:hover {
        background: #1d4ed8;
        color: #ffffff;
        border-color: rgba(147, 197, 253, 0.45);
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
    }

    /* Notas */
    .cal-notes-section {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .cal-note-block {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid var(--border-subtle, rgba(255, 255, 255, 0.06));
        border-radius: 10px;
        padding: 12px 14px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .cal-note-title {
        font-size: 11.5px;
        font-weight: 700;
        color: var(--text-muted, #94a3b8);
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .cal-note-text {
        font-size: 13px;
        color: var(--text-main, #f8fafc);
        margin: 0;
        line-height: 1.5;
    }

    .cal-textarea-obs {
        width: 100%;
        box-sizing: border-box;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid var(--border-subtle, rgba(255, 255, 255, 0.1));
        border-radius: 8px;
        padding: 10px 12px;
        color: var(--text-white, #ffffff);
        font-size: 13px;
        font-family: inherit;
        resize: vertical;
        outline: none;
    }

    .cal-textarea-obs:focus {
        border-color: var(--barber-blue, #1e40af);
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.25);
    }

    /* Cuadro de Actualización de Estado (Criterio 5) */
    .cal-status-change-box {
        background: rgba(37, 99, 235, 0.06);
        border: 1px solid rgba(37, 99, 235, 0.25);
        border-radius: 12px;
        padding: 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
    }

    .cal-status-select-wrap {
        display: flex;
        flex-direction: column;
        gap: 6px;
        flex: 1;
        min-width: 200px;
    }

    .cal-status-label {
        font-size: 12px;
        font-weight: 700;
        color: var(--text-white, #ffffff);
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .cal-select-status-action {
        height: 38px;
        padding: 0 12px;
        background: #0a1124;
        border: 1px solid rgba(37, 99, 235, 0.4);
        border-radius: 8px;
        color: var(--text-white, #ffffff);
        font-size: 13.5px;
        font-weight: 600;
        outline: none;
        cursor: pointer;
    }

    .cal-select-status-action option {
        background: #0a1124;
        color: #ffffff;
    }

    .btn-cal-save-status {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 18px;
        background: #1e40af;
        color: #ffffff;
        border: 1px solid rgba(147, 197, 253, 0.28);
        border-radius: 8px;
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.35);
    }

    .btn-cal-save-status:hover {
        background: #1d4ed8;
        color: #ffffff;
        border-color: rgba(147, 197, 253, 0.45);
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
    }

    .btn-cal-save-status:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }
</style>

{{-- JAVASCRIPT ROBUSTO DEL CALENDARIO INTERACTIVO (VANILLA JS COMPLETO) --}}
<script>
(function() {
    'use strict';

    // CONFIGURACIÓN DE ENDPOINTS Y HORARIOS
    const API_EVENTOS_URL = @json($apiEventos);
    const API_DETALLE_BASE = @json($apiDetalleBase);
    const API_ESTADO_BASE = @json($apiEstadoBase);
    const HORARIOS_SUCURSAL_INICIAL = @json(\App\Models\HorarioSucursal::getHorariosMap());

    // ESTADO LOCAL DEL CALENDARIO
    const state = {
        vista: 'mes', // 'mes' | 'semana' | 'dia'
        fechaSeleccionada: new Date(),
        filtroTrabajador: '',
        filtroEstado: @json(request('estado', 'todas')),
        terminoBusqueda: '',
        citas: [],
        horariosSucursal: HORARIOS_SUCURSAL_INICIAL,
        fechasEspeciales: [],
        citaSeleccionadaId: null,
        cargando: false
    };

    // ELEMENTOS DEL DOM
    const el = {
        loadingOverlay: document.getElementById('calLoadingOverlay'),
        periodTitle: document.getElementById('calPeriodDisplayTitle'),
        periodSubtext: document.getElementById('calPeriodSubtext'),
        btnPrev: document.getElementById('btnCalPrev'),
        btnNext: document.getElementById('btnCalNext'),
        btnToday: document.getElementById('btnCalToday'),
        inputFechaDirecta: document.getElementById('calInputFechaDirecta'),
        btnRefresh: document.getElementById('btnCalRefresh'),
        btnAgendarCita: document.getElementById('btnCalAgendarCita'),
        btnViewMes: document.getElementById('btnViewMes'),
        btnViewSemana: document.getElementById('btnViewSemana'),
        btnViewDia: document.getElementById('btnViewDia'),
        selectTrabajador: document.getElementById('calSelectTrabajador'),
        selectEstado: document.getElementById('calSelectEstado'),
        searchInput: document.getElementById('calSearchInput'),
        // Contenedores de vista
        viewMesContainer: document.getElementById('calViewMesContainer'),
        viewSemanaContainer: document.getElementById('calViewSemanaContainer'),
        viewDiaContainer: document.getElementById('calViewDiaContainer'),
        monthDaysGrid: document.getElementById('calMonthDaysGrid'),
        weekBoard: document.getElementById('calWeekBoard'),
        dayHeaderSummary: document.getElementById('calDayHeaderSummary'),
        dayHoursList: document.getElementById('calDayHoursList'),
        emptyState: document.getElementById('calEmptyState'),
        emptyDesc: document.getElementById('calEmptyDesc'),
        // Contadores
        cntConfirmada: document.getElementById('cntConfirmada'),
        cntEnProceso: document.getElementById('cntEnProceso'),
        cntCompletada: document.getElementById('cntCompletada'),
        cntCancelada: document.getElementById('cntCancelada'),
        cntVencida: document.getElementById('cntVencida'),
        // Modal
        modal: document.getElementById('calCitaModal'),
        btnCerrarModal: document.getElementById('btnCerrarModalCita'),
        modalFolio: document.getElementById('modalCitaFolio'),
        modalStatusBadge: document.getElementById('modalCitaStatusBadge'),
        modalTitle: document.getElementById('modalCitaTitle'),
        modalHorario: document.getElementById('modalCitaHorario'),
        modalFechaTexto: document.getElementById('modalCitaFechaTexto'),
        modalDuracion: document.getElementById('modalCitaDuracion'),
        modalClienteNombre: document.getElementById('modalCitaClienteNombre'),
        modalClienteTel: document.getElementById('modalCitaClienteTel'),
        modalClienteEmail: document.getElementById('modalCitaClienteEmail'),
        modalEspecialista: document.getElementById('modalCitaEspecialista'),
        modalServicio: document.getElementById('modalCitaServicio'),
        modalCategoria: document.getElementById('modalCitaCategoria'),
        modalPrecio: document.getElementById('modalCitaPrecio'),
        modalNotas: document.getElementById('modalCitaNotas'),
        modalObservacionesInput: document.getElementById('modalCitaObservacionesInput'),
        modalSelectNuevoEstado: document.getElementById('modalSelectNuevoEstado'),
        btnGuardarCambioEstado: document.getElementById('btnGuardarCambioEstado'),
        // Check-in (Subtarea 5)
        modalCheckInContainer: document.getElementById('modalCalCheckInContainer'),
        modalCheckInIcon: document.getElementById('modalCalCheckInIcon'),
        modalCheckInTitulo: document.getElementById('modalCalCheckInTitulo'),
        modalCheckInSub: document.getElementById('modalCalCheckInSub'),
        btnModalCheckIn: document.getElementById('btnCalModalCheckIn')
    };

    // UTILIDADES DE FECHA
    const nombresMeses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    const nombresDiasSemana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    const nombresDiasCortos = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
    const DIAS_KEYS = ['domingo', 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];

    function horaAMinutos(hora) {
        if (!hora) return 0;
        const parts = hora.split(':').map(Number);
        return (parts[0] * 60) + (parts[1] || 0);
    }

    function formatDateISO(date) {
        const d = new Date(date);
        const y = d.getFullYear();
        const m = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${y}-${m}-${day}`;
    }

    function parseISODate(isoStr) {
        if (!isoStr) return new Date();
        const parts = isoStr.split('-');
        return new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
    }

    function isSameDay(d1, d2) {
        return d1.getFullYear() === d2.getFullYear() &&
               d1.getMonth() === d2.getMonth() &&
               d1.getDate() === d2.getDate();
    }

    /**
     * Determina si una fecha dada corresponde a un día laborable de la sucursal.
     * Reutiliza la configuración centralizada de horarios y fechas especiales.
     */
    function infoDiaLaborable(date) {
        const d = (date instanceof Date) ? date : parseISODate(date);
        const fechaStr = formatDateISO(d);
        const diaIndex = d.getDay(); // 0: domingo, 6: sabado
        const diaClave = DIAS_KEYS[diaIndex];

        // 1. Revisar si hay fecha especial cerrada todo el día
        if (state.fechasEspeciales && state.fechasEspeciales.length > 0) {
            const fe = state.fechasEspeciales.find(f => f.fecha === fechaStr);
            if (fe && fe.cerrado_todo_el_dia) {
                return {
                    laborable: false,
                    motivo: fe.motivo || 'Fecha especial',
                    mensaje: 'Cerrado, no se labora',
                    subtexto: fe.motivo ? `Fecha especial (${fe.motivo})` : 'Sucursal cerrada',
                    tipo: 'fecha_especial',
                    diaClave: diaClave,
                    apertura: null,
                    cierre: null
                };
            }
        }

        // 2. Horarios de la sucursal (fuente de verdad)
        const horarios = state.horariosSucursal || HORARIOS_SUCURSAL_INICIAL || {};
        const configDia = horarios[diaClave];

        if (!configDia || !configDia.abierto) {
            const nombreDia = configDia ? configDia.nombre : nombresDiasSemana[diaIndex];
            return {
                laborable: false,
                motivo: nombreDia,
                mensaje: 'Cerrado, no se labora',
                subtexto: 'Día no laborable',
                tipo: 'dia_no_laborable',
                diaClave: diaClave,
                apertura: null,
                cierre: null
            };
        }

        return {
            laborable: true,
            motivo: null,
            mensaje: 'Laborable',
            subtexto: `${configDia.apertura} a ${configDia.cierre}`,
            tipo: 'laborable',
            diaClave: diaClave,
            apertura: configDia.apertura || '08:00',
            cierre: configDia.cierre || '20:00'
        };
    }

    /**
     * Muestra una notificación visual informando que el día está cerrado y no se labora.
     */
    function notificarDiaNoLaborable(diaInfo) {
        const sub = (diaInfo && diaInfo.subtexto) ? ` (${diaInfo.subtexto})` : '';
        if (window.Swal) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'warning',
                title: 'Cerrado, no se labora',
                text: `La sucursal permanece cerrada en esta fecha${sub}. No hay horarios de atención disponibles para agendar.`,
                showConfirmButton: false,
                timer: 3500,
                timerProgressBar: true,
                background: '#0c142b',
                color: '#ffffff'
            });
        }
    }

    // OBTENER RANGO DE FECHAS SEGÚN LA VISTA
    function getRangoFechasActivo() {
        const curr = new Date(state.fechaSeleccionada);
        let start, end;

        if (state.vista === 'mes') {
            // Primer día del mes
            const primerDiaMes = new Date(curr.getFullYear(), curr.getMonth(), 1);
            // Ajustar al lunes previo si el 1er día no es lunes
            let dayOfWeek = primerDiaMes.getDay(); // 0 Dom, 1 Lun, etc.
            let diffLunes = dayOfWeek === 0 ? -6 : 1 - dayOfWeek;
            start = new Date(primerDiaMes);
            start.setDate(primerDiaMes.getDate() + diffLunes);

            // Último día del mes
            const ultimoDiaMes = new Date(curr.getFullYear(), curr.getMonth() + 1, 0);
            let dayOfWeekFin = ultimoDiaMes.getDay();
            let diffDom = dayOfWeekFin === 0 ? 0 : 7 - dayOfWeekFin;
            end = new Date(ultimoDiaMes);
            end.setDate(ultimoDiaMes.getDate() + diffDom);
        } else if (state.vista === 'semana') {
            // Lunes de la semana
            let dayOfWeek = curr.getDay();
            let diffLunes = dayOfWeek === 0 ? -6 : 1 - dayOfWeek;
            start = new Date(curr);
            start.setDate(curr.getDate() + diffLunes);

            end = new Date(start);
            end.setDate(start.getDate() + 6);
        } else {
            // Día
            start = new Date(curr);
            end = new Date(curr);
        }

        return {
            start: formatDateISO(start),
            end: formatDateISO(end)
        };
    }

    // CARGAR CITAS VÍA AJAX
    async function cargarCitas() {
        const rango = getRangoFechasActivo();
        state.cargando = true;
        el.loadingOverlay.style.display = 'flex';

        const params = new URLSearchParams({
            start: rango.start,
            end: rango.end
        });

        if (state.filtroTrabajador) {
            params.append('worker_id', state.filtroTrabajador);
        }
        if (state.filtroEstado && state.filtroEstado !== 'todas') {
            params.append('estado', state.filtroEstado);
        }

        try {
            const url = `${API_EVENTOS_URL}?${params.toString()}`;
            const res = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!res.ok) {
                throw new Error(`Error en servidor (${res.status})`);
            }

            const data = await res.json();
            if (data.success) {
                state.citas = data.eventos || [];
                if (data.horarios_sucursal) {
                    state.horariosSucursal = data.horarios_sucursal;
                }
                if (data.fechas_especiales) {
                    state.fechasEspeciales = data.fechas_especiales;
                }
                actualizarTituloPeriodo();
                actualizarContadoresLegend();
                renderizarVista();
            } else {
                throw new Error(data.message || 'No se pudieron consultar las citas.');
            }
        } catch (err) {
            console.error('Error al cargar citas:', err);
            if (window.Swal) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No fue posible sincronizar las citas con el servidor: ' + err.message,
                    background: '#0a1124',
                    color: '#ffffff'
                });
            }
        } finally {
            state.cargando = false;
            el.loadingOverlay.style.display = 'none';
        }
    }

    // ACTUALIZAR TÍTULO DEL PERIODO
    function actualizarTituloPeriodo() {
        const d = state.fechaSeleccionada;
        el.inputFechaDirecta.value = formatDateISO(d);

        if (state.vista === 'mes') {
            el.periodTitle.textContent = `${nombresMeses[d.getMonth()]} ${d.getFullYear()}`;
            el.periodSubtext.textContent = `Visualizando mes completo • ${state.citas.length} citas registradas`;
        } else if (state.vista === 'semana') {
            const rango = getRangoFechasActivo();
            const startD = parseISODate(rango.start);
            const endD = parseISODate(rango.end);
            el.periodTitle.textContent = `Semana del ${startD.getDate()} al ${endD.getDate()} de ${nombresMeses[endD.getMonth()]}, ${endD.getFullYear()}`;
            el.periodSubtext.textContent = `Agenda semanal • ${state.citas.length} citas registradas`;
        } else {
            const diaNombre = nombresDiasSemana[d.getDay()];
            el.periodTitle.textContent = `${diaNombre} ${d.getDate()} de ${nombresMeses[d.getMonth()]}, ${d.getFullYear()}`;
            el.periodSubtext.textContent = `Timeline detallado del día • ${state.citas.length} citas registradas`;
        }
    }

    // ACTUALIZAR CONTADORES DE ESTADO
    function actualizarContadoresLegend() {
        const counts = {
            confirmada: 0,
            en_proceso: 0,
            completada: 0,
            cancelada: 0,
            vencida: 0
        };

        state.citas.forEach(c => {
            const st = c.estado_visual || c.estado;
            if (counts[st] !== undefined) {
                counts[st]++;
            }
        });

        el.cntConfirmada.textContent = counts.confirmada;
        el.cntEnProceso.textContent = counts.en_proceso;
        el.cntCompletada.textContent = counts.completada;
        el.cntCancelada.textContent = counts.cancelada;
        el.cntVencida.textContent = counts.vencida;
    }

    // FILTRADO LOCAL (ESTADO ESTRICTO Y BÚSQUEDA TEXTUAL)
    function obtenerCitasFiltradas() {
        let list = [...state.citas];

        // 1. Filtro estricto por estado visual (evita que confirmadas incluyan vencidas)
        if (state.filtroEstado && state.filtroEstado !== 'todas') {
            const fEstado = state.filtroEstado.toLowerCase();
            list = list.filter(c => {
                const stVisual = (c.estado_visual || c.estado || '').toLowerCase();
                if (fEstado === 'en_proceso' || fEstado === 'en_atencion') {
                    return stVisual === 'en_proceso' || stVisual === 'en_atencion';
                }
                return stVisual === fEstado;
            });
        }

        // 2. Búsqueda textual
        const term = state.terminoBusqueda.trim().toLowerCase();
        if (term !== '') {
            list = list.filter(c => {
                const cliente = (c.cliente?.nombre_completo || '').toLowerCase();
                const tel = (c.cliente?.telefono || '').toLowerCase();
                const servicio = (c.servicio?.nombre || '').toLowerCase();
                const especialista = (c.trabajador?.nombre_completo || '').toLowerCase();
                const folio = (c.folio || '').toLowerCase();
                return cliente.includes(term) || tel.includes(term) || servicio.includes(term) || especialista.includes(term) || folio.includes(term);
            });
        }

        return list;
    }

    // RENDERIZAR VISTA ACTIVA
    function renderizarVista() {
        // Ocultar todos los contenedores
        el.viewMesContainer.style.display = 'none';
        el.viewSemanaContainer.style.display = 'none';
        el.viewDiaContainer.style.display = 'none';
        el.emptyState.style.display = 'none';

        const citasFiltradas = obtenerCitasFiltradas();
        const diaActivoInfo = infoDiaLaborable(state.fechaSeleccionada);

        // Bloquear o habilitar botón superior de agendar según la fecha activa en vista día
        if (el.btnAgendarCita) {
            if (state.vista === 'dia' && !diaActivoInfo.laborable) {
                el.btnAgendarCita.classList.add('is-disabled-closed');
                el.btnAgendarCita.setAttribute('aria-disabled', 'true');
                el.btnAgendarCita.title = 'No se puede agendar citas en días no laborables (Cerrado, no se labora)';
            } else {
                el.btnAgendarCita.classList.remove('is-disabled-closed');
                el.btnAgendarCita.removeAttribute('aria-disabled');
                el.btnAgendarCita.title = 'Agendar nueva cita';
            }
        }

        if (state.vista === 'mes') {
            el.viewMesContainer.style.display = 'block';
            renderizarVistaMes(citasFiltradas);
        } else if (state.vista === 'semana') {
            el.viewSemanaContainer.style.display = 'block';
            renderizarVistaSemana(citasFiltradas);
        } else {
            el.viewDiaContainer.style.display = 'block';
            renderizarVistaDia(citasFiltradas);
        }

        // Mostrar estado vacío si no hay eventos y no estamos en mes (en mes se ven las celdas)
        // CRÍTICO: Si estamos en vista día y el día no es laborable, NUNCA mostrar estado vacío ni opción de agendar
        if (citasFiltradas.length === 0 && (state.vista === 'semana' || state.vista === 'dia')) {
            if (state.vista === 'dia' && !diaActivoInfo.laborable) {
                el.emptyState.style.display = 'none';
            } else {
                el.emptyState.style.display = 'flex';
            }
        } else {
            el.emptyState.style.display = 'none';
        }
    }

    // RENDER VISTA MES
    function renderizarVistaMes(citas) {
        el.monthDaysGrid.innerHTML = '';
        const curr = state.fechaSeleccionada;
        const hoy = new Date();
        const primerDiaMes = new Date(curr.getFullYear(), curr.getMonth(), 1);
        let dayOfWeek = primerDiaMes.getDay();
        let diffLunes = dayOfWeek === 0 ? -6 : 1 - dayOfWeek;
        const start = new Date(primerDiaMes);
        start.setDate(primerDiaMes.getDate() + diffLunes);

        const totalCeldas = 35; // 5 semanas estándar o 42 si requiere
        const fechaIter = new Date(start);

        // Agrupar citas por fecha (YYYY-MM-DD)
        const citasPorFecha = {};
        citas.forEach(c => {
            if (!citasPorFecha[c.fecha]) {
                citasPorFecha[c.fecha] = [];
            }
            citasPorFecha[c.fecha].push(c);
        });

        for (let i = 0; i < totalCeldas; i++) {
            const fechaStr = formatDateISO(fechaIter);
            const esHoy = isSameDay(fechaIter, hoy);
            const esMismoMes = fechaIter.getMonth() === curr.getMonth();
            const citasDelDia = citasPorFecha[fechaStr] || [];
            const diaInfo = infoDiaLaborable(fechaIter);

            const cell = document.createElement('div');
            cell.className = `cal-month-cell ${esHoy ? 'is-today' : ''} ${!esMismoMes ? 'different-month' : ''} ${!diaInfo.laborable ? 'is-closed cal-month-cell-closed' : ''}`;
            cell.dataset.fecha = fechaStr;
            cell.dataset.laborable = diaInfo.laborable ? '1' : '0';

            // Encabezado de celda
            const header = document.createElement('div');
            header.className = 'cal-month-cell-header';

            const numSpan = document.createElement('span');
            numSpan.className = 'cal-day-num';
            numSpan.textContent = fechaIter.getDate();
            header.appendChild(numSpan);

            if (diaInfo.laborable && citasDelDia.length > 0) {
                const countBadge = document.createElement('span');
                countBadge.className = 'cal-day-count-badge';
                countBadge.textContent = `${citasDelDia.length} ${citasDelDia.length === 1 ? 'cita' : 'citas'}`;
                header.appendChild(countBadge);
            } else if (!diaInfo.laborable) {
                const closedPill = document.createElement('span');
                closedPill.className = 'cal-month-closed-pill';
                closedPill.title = 'Cerrado, no se labora';
                closedPill.innerHTML = `
                    <svg viewBox="0 0 24 24" width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                    <span>Cerrado</span>
                `;
                header.appendChild(closedPill);
            }

            cell.appendChild(header);

            // Lista de eventos o aviso de cerrado
            const eventsList = document.createElement('div');
            eventsList.className = 'cal-cell-events-list';

            if (!diaInfo.laborable) {
                const closedBanner = document.createElement('div');
                closedBanner.className = 'cal-month-closed-banner';
                closedBanner.innerHTML = `<span>Cerrado, no se labora</span>`;
                eventsList.appendChild(closedBanner);
            }

            const maxChips = diaInfo.laborable ? 2 : 1;
            citasDelDia.slice(0, maxChips).forEach(cita => {
                const chip = document.createElement('div');
                const st = cita.estado_visual || cita.estado;
                chip.className = `cal-event-chip status-bg-${st}`;
                chip.title = `${cita.hora_inicio} - ${cita.cliente.nombre_completo} (${cita.servicio.nombre})`;
                chip.dataset.id = cita.id;

                chip.innerHTML = `
                    <span class="chip-time">${cita.hora_inicio}</span>
                    <span class="chip-client">${escapeHtml(cita.cliente.nombre_completo)}</span>
                `;

                chip.addEventListener('click', (e) => {
                    e.stopPropagation();
                    abrirModalCita(cita.id);
                });

                eventsList.appendChild(chip);
            });

            if (citasDelDia.length > maxChips) {
                const moreBtn = document.createElement('button');
                moreBtn.type = 'button';
                moreBtn.className = 'cal-more-events-btn';
                moreBtn.textContent = `+${citasDelDia.length - maxChips} más...`;
                moreBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    cambiarADia(fechaStr);
                });
                eventsList.appendChild(moreBtn);
            }

            cell.appendChild(eventsList);

            // Click en la celda para saltar a vista de día solo si es laborable
            if (!diaInfo.laborable) {
                cell.addEventListener('click', (e) => {
                    if (e.target.closest('.cal-event-chip') || e.target.closest('.cal-more-events-btn')) {
                        return;
                    }
                    e.preventDefault();
                    e.stopPropagation();
                    notificarDiaNoLaborable(diaInfo);
                });
            } else {
                cell.addEventListener('click', () => {
                    cambiarADia(fechaStr);
                });
            }

            el.monthDaysGrid.appendChild(cell);
            fechaIter.setDate(fechaIter.getDate() + 1);
        }
    }

    // RENDER VISTA SEMANA
    function renderizarVistaSemana(citas) {
        el.weekBoard.innerHTML = '';
        const curr = state.fechaSeleccionada;
        const hoy = new Date();
        let dayOfWeek = curr.getDay();
        let diffLunes = dayOfWeek === 0 ? -6 : 1 - dayOfWeek;
        const start = new Date(curr);
        start.setDate(curr.getDate() + diffLunes);

        const citasPorFecha = {};
        citas.forEach(c => {
            if (!citasPorFecha[c.fecha]) {
                citasPorFecha[c.fecha] = [];
            }
            citasPorFecha[c.fecha].push(c);
        });

        const fechaIter = new Date(start);

        for (let i = 0; i < 7; i++) {
            const fechaStr = formatDateISO(fechaIter);
            const esHoy = isSameDay(fechaIter, hoy);
            const citasDelDia = citasPorFecha[fechaStr] || [];
            const diaInfo = infoDiaLaborable(fechaIter);

            const col = document.createElement('div');
            col.className = `cal-week-column ${!diaInfo.laborable ? 'is-closed cal-week-column-closed' : ''}`;
            col.dataset.fecha = fechaStr;
            col.dataset.laborable = diaInfo.laborable ? '1' : '0';

            // Cabecera columna
            const colHeader = document.createElement('div');
            colHeader.className = `cal-week-col-header ${esHoy ? 'is-today' : ''} ${!diaInfo.laborable ? 'is-closed' : ''}`;
            colHeader.innerHTML = `
                <div class="cal-week-day-name">${nombresDiasCortos[i]}</div>
                <div class="cal-week-day-number">${fechaIter.getDate()} ${nombresMeses[fechaIter.getMonth()].substring(0, 3)}</div>
                ${!diaInfo.laborable ? '<div class="cal-week-closed-header-badge">Cerrado, no se labora</div>' : ''}
            `;
            col.appendChild(colHeader);

            // Cuerpo columna
            const colBody = document.createElement('div');
            colBody.className = 'cal-week-col-body';

            if (!diaInfo.laborable) {
                const closedNotice = document.createElement('div');
                closedNotice.className = 'cal-week-closed-notice';
                closedNotice.innerHTML = `
                    <div class="cal-closed-lock-icon">
                        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                    </div>
                    <div class="cal-closed-notice-title">Cerrado, no se labora</div>
                    <div class="cal-closed-notice-sub">${diaInfo.subtexto || 'Día no laborable'}</div>
                `;
                colBody.appendChild(closedNotice);

                // Si hubieran citas históricas existentes, mostrarlas informativamente
                citasDelDia.forEach(cita => {
                    const card = document.createElement('div');
                    const st = cita.estado_visual || cita.estado;
                    card.className = 'cal-week-event-card';
                    card.dataset.id = cita.id;
                    card.innerHTML = `
                        <div class="cal-card-top-row">
                            <span class="cal-card-time">${cita.hora_inicio} - ${cita.hora_fin}</span>
                            <span class="cal-card-status-pill status-bg-${st}">${cita.estado_label}</span>
                        </div>
                        <div class="cal-card-client">${escapeHtml(cita.cliente.nombre_completo)}</div>
                        <div class="cal-card-service">${escapeHtml(cita.servicio.nombre)} • ${cita.precio_formateado}</div>
                    `;
                    card.addEventListener('click', () => abrirModalCita(cita.id));
                    colBody.appendChild(card);
                });
            } else if (citasDelDia.length === 0) {
                const emptySlot = document.createElement('div');
                emptySlot.className = 'cal-hour-empty-slot';
                emptySlot.textContent = 'Sin citas programadas';
                colBody.appendChild(emptySlot);
            } else {
                citasDelDia.forEach(cita => {
                    const card = document.createElement('div');
                    const st = cita.estado_visual || cita.estado;
                    card.className = 'cal-week-event-card';
                    card.dataset.id = cita.id;

                    card.innerHTML = `
                        <div class="cal-card-top-row">
                            <span class="cal-card-time">${cita.hora_inicio} - ${cita.hora_fin}</span>
                            <span class="cal-card-status-pill status-bg-${st}">${cita.estado_label}</span>
                        </div>
                        <div class="cal-card-client">${escapeHtml(cita.cliente.nombre_completo)}</div>
                        <div class="cal-card-service">${escapeHtml(cita.servicio.nombre)} • ${cita.precio_formateado}</div>
                        <div class="cal-card-worker">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="12" height="12">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            <span>${escapeHtml(cita.trabajador.nombre_completo)}</span>
                        </div>
                    `;

                    card.addEventListener('click', () => abrirModalCita(cita.id));
                    colBody.appendChild(card);
                });
            }

            if (!diaInfo.laborable) {
                col.addEventListener('click', (e) => {
                    if (e.target.closest('.cal-week-event-card')) {
                        return;
                    }
                    e.preventDefault();
                    e.stopPropagation();
                    notificarDiaNoLaborable(diaInfo);
                });
            }

            col.appendChild(colBody);
            el.weekBoard.appendChild(col);
            fechaIter.setDate(fechaIter.getDate() + 1);
        }
    }

    // RENDER VISTA DÍA (TIMELINE)
    function renderizarVistaDia(citas) {
        const d = state.fechaSeleccionada;
        const fechaStr = formatDateISO(d);
        const diaNombre = nombresDiasSemana[d.getDay()];
        const diaInfo = infoDiaLaborable(d);

        if (!diaInfo.laborable) {
            el.emptyState.style.display = 'none';
            el.dayHeaderSummary.innerHTML = `
                <div class="cal-day-summary-left">
                    <h3>${diaNombre} ${d.getDate()} de ${nombresMeses[d.getMonth()]}, ${d.getFullYear()}</h3>
                    <p class="cal-day-closed-warning">La sucursal permanece cerrada en esta fecha (${diaInfo.subtexto || 'Día no laborable'})</p>
                </div>
                <div class="cal-day-summary-right">
                    <span class="cal-badge-pill cal-badge-closed">
                        <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                        <span>Cerrado, no se labora</span>
                    </span>
                </div>
            `;

            el.dayHoursList.innerHTML = `
                <div class="cal-day-closed-card">
                    <div class="cal-day-closed-icon">
                        <svg viewBox="0 0 24 24" width="36" height="36" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                    </div>
                    <h4 class="cal-day-closed-title">Cerrado, no se labora</h4>
                    <p class="cal-day-closed-desc">Este día está configurado como no laborable en la sucursal. No hay horarios de atención ni selección disponible.</p>
                </div>
            `;

            if (citas.length > 0) {
                const histWrap = document.createElement('div');
                histWrap.className = 'cal-day-hist-citas';
                histWrap.innerHTML = `<h5 style="color: #94a3b8; font-size: 13px; margin: 16px 0 8px;">Citas registradas previamente en este día (${citas.length}):</h5>`;
                citas.forEach(cita => {
                    const card = document.createElement('div');
                    const st = cita.estado_visual || cita.estado;
                    card.className = 'cal-day-event-card';
                    card.dataset.id = cita.id;
                    card.innerHTML = `
                        <div class="cal-day-event-left">
                            <span class="cal-day-event-time-badge">${cita.hora_inicio} - ${cita.hora_fin}</span>
                            <div class="cal-day-event-main-info">
                                <span class="cal-day-client-name">${escapeHtml(cita.cliente.nombre_completo)}</span>
                                <span class="cal-day-service-name">${escapeHtml(cita.servicio.nombre)} • ${cita.duracion_minutos} min</span>
                                <span class="cal-day-worker-tag">Especialista: ${escapeHtml(cita.trabajador.nombre_completo)}</span>
                            </div>
                        </div>
                        <div class="cal-day-event-right">
                            <span class="cal-day-price-tag">${cita.precio_formateado}</span>
                            <span class="cal-card-status-pill status-bg-${st}">${cita.estado_label}</span>
                        </div>
                    `;
                    card.addEventListener('click', () => abrirModalCita(cita.id));
                    histWrap.appendChild(card);
                });
                el.dayHoursList.appendChild(histWrap);
            }
            return;
        }

        // Resumen superior para día laborable
        el.dayHeaderSummary.innerHTML = `
            <div class="cal-day-summary-left">
                <h3>${diaNombre} ${d.getDate()} de ${nombresMeses[d.getMonth()]}, ${d.getFullYear()}</h3>
                <p>${citas.length} ${citas.length === 1 ? 'cita programada' : 'citas programadas'} para esta fecha • Horario de atención: ${diaInfo.apertura} a ${diaInfo.cierre}</p>
            </div>
            <div class="cal-day-summary-right">
                <span class="cal-badge-pill">Fecha seleccionada</span>
            </div>
        `;

        el.dayHoursList.innerHTML = '';

        // Horas laborales desde las 08:00 hasta las 20:00
        const horas = [];
        for (let h = 8; h <= 20; h++) {
            horas.push(String(h).padStart(2, '0') + ':00');
        }

        const aperturaMin = diaInfo.apertura ? horaAMinutos(diaInfo.apertura) : 480;
        const cierreMin = diaInfo.cierre ? horaAMinutos(diaInfo.cierre) : 1200;

        horas.forEach(hora => {
            const horaPrefijo = hora.substring(0, 2);
            const horaActualMin = horaAMinutos(hora);
            // Citas que caen en esta franja horaria
            const citasDeLaHora = citas.filter(c => c.hora_inicio.startsWith(horaPrefijo));

            const row = document.createElement('div');
            row.className = 'cal-timeline-hour-row';

            const label = document.createElement('div');
            label.className = 'cal-hour-label';
            label.textContent = hora;
            row.appendChild(label);

            const eventsGroup = document.createElement('div');
            eventsGroup.className = 'cal-hour-events-group';

            if (citasDeLaHora.length === 0) {
                const emptySlot = document.createElement('div');
                if (horaActualMin < aperturaMin || horaActualMin >= cierreMin) {
                    emptySlot.className = 'cal-hour-empty-slot is-outside-hours';
                    emptySlot.textContent = `Fuera de horario de atención (Cierre: ${diaInfo.cierre})`;
                } else {
                    emptySlot.className = 'cal-hour-empty-slot';
                    emptySlot.textContent = 'Disponible para agendar';
                }
                eventsGroup.appendChild(emptySlot);
            } else {
                citasDeLaHora.forEach(cita => {
                    const card = document.createElement('div');
                    const st = cita.estado_visual || cita.estado;
                    card.className = 'cal-day-event-card';
                    card.dataset.id = cita.id;

                    card.innerHTML = `
                        <div class="cal-day-event-left">
                            <span class="cal-day-event-time-badge">${cita.hora_inicio} - ${cita.hora_fin}</span>
                            <div class="cal-day-event-main-info">
                                <span class="cal-day-client-name">${escapeHtml(cita.cliente.nombre_completo)}</span>
                                <span class="cal-day-service-name">${escapeHtml(cita.servicio.nombre)} • ${cita.duracion_minutos} min</span>
                                <span class="cal-day-worker-tag">Especialista: ${escapeHtml(cita.trabajador.nombre_completo)}</span>
                            </div>
                        </div>
                        <div class="cal-day-event-right">
                            <span class="cal-day-price-tag">${cita.precio_formateado}</span>
                            <span class="cal-card-status-pill status-bg-${st}">${cita.estado_label}</span>
                        </div>
                    `;

                    card.addEventListener('click', () => abrirModalCita(cita.id));
                    eventsGroup.appendChild(card);
                });
            }

            row.appendChild(eventsGroup);
            el.dayHoursList.appendChild(row);
        });
    }

    // CAMBIAR A VISTA DÍA
    function cambiarADia(fechaStr) {
        state.fechaSeleccionada = parseISODate(fechaStr);
        setVista('dia');
    }

    // GESTIÓN DE VISTA
    function setVista(nuevaVista) {
        state.vista = nuevaVista;

        el.btnViewMes.classList.toggle('active', nuevaVista === 'mes');
        el.btnViewSemana.classList.toggle('active', nuevaVista === 'semana');
        el.btnViewDia.classList.toggle('active', nuevaVista === 'dia');

        el.btnViewMes.setAttribute('aria-selected', nuevaVista === 'mes');
        el.btnViewSemana.setAttribute('aria-selected', nuevaVista === 'semana');
        el.btnViewDia.setAttribute('aria-selected', nuevaVista === 'dia');

        cargarCitas();
    }

    // NAVEGACIÓN TEMPORAL (ANTERIOR / SIGUIENTE / HOY)
    function navegarPeriodo(direccion) {
        const d = new Date(state.fechaSeleccionada);

        if (state.vista === 'mes') {
            d.setMonth(d.getMonth() + direccion);
        } else if (state.vista === 'semana') {
            d.setDate(d.getDate() + (direccion * 7));
        } else {
            d.setDate(d.getDate() + direccion);
        }

        state.fechaSeleccionada = d;
        cargarCitas();
    }

    // MODAL DE DETALLE DE CITA
    async function abrirModalCita(citaId) {
        state.citaSeleccionadaId = citaId;
        const citaLocal = state.citas.find(c => c.id == citaId);

        if (citaLocal) {
            poblarModal(citaLocal);
            el.modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        // Obtener detalles frescos del servidor
        try {
            const res = await fetch(`${API_DETALLE_BASE}/${citaId}/detalle`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (res.ok) {
                const data = await res.json();
                if (data.success && data.cita) {
                    poblarModal(data.cita);
                }
            }
        } catch (err) {
            console.warn('Detalle obtenido desde memoria local:', err);
        }
    }

    function poblarModal(c) {
        el.modalFolio.textContent = c.folio || `CITA-${String(c.id).padStart(5, '0')}`;
        const st = c.estado_visual || c.estado;

        el.modalStatusBadge.textContent = c.estado_label || ucfirst(st);
        el.modalStatusBadge.className = `cal-modal-status-badge status-bg-${st}`;

        el.modalTitle.textContent = `${c.servicio.nombre} • ${c.precio_formateado || '$' + c.precio}`;
        el.modalHorario.textContent = `${c.hora_inicio} - ${c.hora_fin} hrs`;
        el.modalFechaTexto.textContent = c.fecha_formateada || c.fecha;
        el.modalDuracion.textContent = `${c.duracion_minutos} min`;

        el.modalClienteNombre.textContent = c.cliente.nombre_completo;
        el.modalClienteTel.textContent = `Tel: ${c.cliente.telefono || 'No registrado'}`;
        el.modalClienteEmail.textContent = c.cliente.email || 'Sin correo registrado';

        el.modalEspecialista.textContent = c.trabajador.nombre_completo;
        el.modalServicio.textContent = c.servicio.nombre;
        el.modalCategoria.textContent = `Categoría: ${c.servicio.categoria || 'Estudio'}`;
        el.modalPrecio.textContent = c.precio_formateado || `$${c.precio}`;

        el.modalNotas.textContent = c.notas && c.notas.trim() !== '' ? c.notas : 'Sin notas registradas al agendar.';
        el.modalObservacionesInput.value = c.observaciones || '';

        // Actualizar datos de Check-in de Llegada (Subtarea 5)
        if (el.modalCheckInTitulo && el.modalCheckInSub && el.btnModalCheckIn) {
            if (c.checked_in) {
                el.modalCheckInTitulo.textContent = 'Llegada Registrada';
                el.modalCheckInTitulo.style.color = '#10b981';
                el.modalCheckInSub.textContent = `Registrado a las ${c.check_in_hora || ''} hrs (${c.check_in_formateado || ''})`;
                if (el.modalCheckInIcon) {
                    el.modalCheckInIcon.className = 'cal-checkin-icon-circle';
                }
                el.btnModalCheckIn.style.display = 'none';
            } else {
                el.modalCheckInTitulo.textContent = 'Pendiente de Llegada';
                el.modalCheckInTitulo.style.color = '#ffffff';
                el.modalCheckInSub.textContent = 'El cliente aún no ha registrado arribo en recepción.';
                if (el.modalCheckInIcon) {
                    el.modalCheckInIcon.className = 'cal-checkin-icon-circle pending';
                }

                if (c.puede_check_in) {
                    el.btnModalCheckIn.style.display = 'inline-flex';
                    const checkinUrl = `${API_ESTADO_BASE}/${c.id}/checkin`;
                    el.btnModalCheckIn.onclick = function () {
                        if (window.registrarCheckInPrompt) {
                            window.registrarCheckInPrompt(c.id, checkinUrl, c.folio, c.cliente?.nombre_completo, async function () {
                                await cargarCitas();
                                await abrirModalCita(c.id);
                            });
                        }
                    };
                } else {
                    el.btnModalCheckIn.style.display = 'none';
                }
            }
        }

        // Configurar opciones de transición según el estado actual de la cita (si el selector existe)
        if (el.modalSelectNuevoEstado && el.btnGuardarCambioEstado) {
            const estadoDb = c.estado || 'confirmada';
            const estadoVisual = c.estado_visual || estadoDb;
            el.modalSelectNuevoEstado.innerHTML = '';

            if (estadoDb === 'cancelada' || estadoDb === 'completada') {
                // Estado terminal inmutable
                const opt = document.createElement('option');
                opt.value = estadoDb;
                opt.textContent = `${estadoDb === 'completada' ? 'Completada' : 'Cancelada'} (Registro final inmutable)`;
                opt.selected = true;
                el.modalSelectNuevoEstado.appendChild(opt);
                el.modalSelectNuevoEstado.disabled = true;
                el.btnGuardarCambioEstado.disabled = true;
                el.btnGuardarCambioEstado.style.opacity = '0.5';
                el.btnGuardarCambioEstado.title = 'Este registro se encuentra en estado final y no puede modificarse.';
            } else {
                el.modalSelectNuevoEstado.disabled = false;
                el.btnGuardarCambioEstado.disabled = false;
                el.btnGuardarCambioEstado.style.opacity = '1';
                el.btnGuardarCambioEstado.title = 'Guardar cambios de estado y observaciones';

                if (estadoDb === 'confirmada') {
                    if (estadoVisual === 'vencida' || c.es_pasada) {
                        const optVencida = document.createElement('option');
                        optVencida.value = 'vencida';
                        optVencida.textContent = 'Vencida (Expirada sin atención)';
                        el.modalSelectNuevoEstado.appendChild(optVencida);
                    } else {
                        const optMantener = document.createElement('option');
                        optMantener.value = 'confirmada';
                        optMantener.textContent = 'Confirmada (Mantener)';
                        el.modalSelectNuevoEstado.appendChild(optMantener);

                        const optProc = document.createElement('option');
                        optProc.value = 'en_proceso';
                        optProc.textContent = 'Iniciar Atención (En proceso)';
                        el.modalSelectNuevoEstado.appendChild(optProc);

                        if (!c.es_pasada && c.puede_cancelar !== false) {
                            const optCanc = document.createElement('option');
                            optCanc.value = 'cancelada';
                            optCanc.textContent = 'Cancelar Cita';
                            el.modalSelectNuevoEstado.appendChild(optCanc);
                        }
                    }
                } else if (estadoDb === 'en_atencion' || estadoDb === 'en_proceso') {
                    const optMantener = document.createElement('option');
                    optMantener.value = 'en_proceso';
                    optMantener.textContent = 'En proceso / En atención (Mantener)';
                    el.modalSelectNuevoEstado.appendChild(optMantener);

                    const optComp = document.createElement('option');
                    optComp.value = 'completada';
                    optComp.textContent = 'Finalizar Atención (Completada)';
                    el.modalSelectNuevoEstado.appendChild(optComp);

                    if (!c.es_pasada && c.puede_cancelar !== false) {
                        const optCanc = document.createElement('option');
                        optCanc.value = 'cancelada';
                        optCanc.textContent = 'Cancelar / Interrumpir Servicio';
                        el.modalSelectNuevoEstado.appendChild(optCanc);
                    }
                } else if (estadoDb === 'vencida') {
                    const optVencida = document.createElement('option');
                    optVencida.value = 'vencida';
                    optVencida.textContent = 'Vencida (Mantener)';
                    el.modalSelectNuevoEstado.appendChild(optVencida);
                }
            }
        }
    }

    function cerrarModal() {
        el.modal.style.display = 'none';
        state.citaSeleccionadaId = null;
        document.body.style.overflow = '';
    }

    // ACTUALIZAR ESTADO DE CITA (CRITERIO 5)
    async function guardarCambioEstado() {
        if (!state.citaSeleccionadaId) return;

        const nuevoEstado = el.modalSelectNuevoEstado.value;
        const observaciones = el.modalObservacionesInput.value.trim();
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        el.btnGuardarCambioEstado.disabled = true;
        const textoOriginal = el.btnGuardarCambioEstado.innerHTML;
        el.btnGuardarCambioEstado.innerHTML = '<span>Guardando...</span>';

        try {
            const res = await fetch(`${API_ESTADO_BASE}/${state.citaSeleccionadaId}/estado`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    estado: nuevoEstado,
                    observaciones: observaciones
                })
            });

            const data = await res.json();

            if (res.ok && data.success) {
                if (window.Swal) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: data.mensaje || 'Estado de la cita actualizado con éxito.',
                        showConfirmButton: false,
                        timer: 2500,
                        timerProgressBar: true,
                        background: '#0c142b',
                        color: '#ffffff'
                    });
                }
                cerrarModal();
                // Actualizar calendario en tiempo real sin recargar página
                await cargarCitas();
            } else {
                throw new Error(data.error || data.message || 'No se pudo guardar el cambio de estado.');
            }
        } catch (err) {
            console.error('Error al actualizar cita:', err);
            if (window.Swal) {
                Swal.fire({
                    icon: 'error',
                    title: 'No se pudo actualizar',
                    text: err.message,
                    background: '#0a1124',
                    color: '#ffffff'
                });
            }
        } finally {
            el.btnGuardarCambioEstado.disabled = false;
            el.btnGuardarCambioEstado.innerHTML = textoOriginal;
        }
    }

    // ESCAPE HTML PARA SEGURIDAD
    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function ucfirst(str) {
        if (!str) return '';
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    // LISTENERS DE EVENTOS
    function initEventListeners() {
        // Navegación
        el.btnPrev.addEventListener('click', () => navegarPeriodo(-1));
        el.btnNext.addEventListener('click', () => navegarPeriodo(1));
        el.btnToday.addEventListener('click', () => {
            state.fechaSeleccionada = new Date();
            cargarCitas();
        });
        el.inputFechaDirecta.addEventListener('change', (e) => {
            if (e.target.value) {
                state.fechaSeleccionada = parseISODate(e.target.value);
                cargarCitas();
            }
        });
        el.btnRefresh.addEventListener('click', () => cargarCitas());

        // Botón Agendar Cita (bloquear si el día activo en vista día es no laborable)
        if (el.btnAgendarCita) {
            el.btnAgendarCita.addEventListener('click', (e) => {
                if (state.vista === 'dia') {
                    const diaInfo = infoDiaLaborable(state.fechaSeleccionada);
                    if (!diaInfo.laborable) {
                        e.preventDefault();
                        e.stopPropagation();
                        notificarDiaNoLaborable(diaInfo);
                    }
                }
            });
        }

        // Selector de vistas
        el.btnViewMes.addEventListener('click', () => setVista('mes'));
        el.btnViewSemana.addEventListener('click', () => setVista('semana'));
        el.btnViewDia.addEventListener('click', () => setVista('dia'));

        // Filtros
        el.selectTrabajador.addEventListener('change', (e) => {
            state.filtroTrabajador = e.target.value;
            cargarCitas();
        });

        el.selectEstado.addEventListener('change', (e) => {
            state.filtroEstado = e.target.value;
            cargarCitas();
        });

        // Subtarea 5: Las etiquetas de leyenda de estado se mantienen como elementos informativos y no interactivos (sin listeners de clic ni filtros)

        el.searchInput.addEventListener('input', (e) => {
            state.terminoBusqueda = e.target.value;
            renderizarVista();
        });

        // Modal
        el.btnCerrarModal.addEventListener('click', cerrarModal);
        el.modal.addEventListener('click', (e) => {
            if (e.target === el.modal) cerrarModal();
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && el.modal.style.display !== 'none') {
                cerrarModal();
            }
        });

        if (el.btnGuardarCambioEstado) {
            el.btnGuardarCambioEstado.addEventListener('click', guardarCambioEstado);
        }
    }

    // INICIALIZACIÓN
    document.addEventListener('DOMContentLoaded', () => {
        initEventListeners();
        cargarCitas();
    });
})();
</script>
