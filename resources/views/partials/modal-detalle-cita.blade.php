{{-- MODAL COMPARTIDO DE DETALLE DE CITA (ADMINISTRACIÓN Y RECEPCIÓN) --}}
<div class="detalle-modal-backdrop" id="modalDetalleCitaBackdrop" style="display: none;" role="dialog" aria-modal="true" aria-labelledby="modalDetalleCitaTitulo">
    <div class="detalle-modal-card">
        {{-- Encabezado --}}
        <div class="detalle-modal-header">
            <div class="detalle-modal-header-info">
                <div class="detalle-modal-pretitle">
                    <span class="detalle-folio-tag" id="detCitaFolio">CITA-00000</span>
                    <span class="status-badge confirmada" id="detCitaStatusBadge">Confirmada</span>
                </div>
                <h3 class="detalle-modal-title" id="modalDetalleCitaTitulo">Detalle de la Cita</h3>
            </div>
            <button type="button" class="detalle-modal-close" id="btnCerrarDetalleModal" aria-label="Cerrar modal">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="20" height="20">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        {{-- Contenedor del Cuerpo con Estado de Carga --}}
        <div class="detalle-modal-body">
            <div class="detalle-modal-loader" id="detCitaLoader" style="display: none;">
                <div class="detalle-spinner"></div>
                <p>Cargando información de la cita...</p>
            </div>

            <div class="detalle-modal-content-grid" id="detCitaContentGrid">
                {{-- 1. Tarjeta: Cliente --}}
                <div class="detalle-info-card">
                    <div class="detalle-card-icon icon-purple">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <div class="detalle-card-data">
                        <span class="detalle-card-label">Cliente</span>
                        <strong class="detalle-card-val" id="detCitaClienteNombre">—</strong>
                        <div class="detalle-contact-items">
                            <span class="detalle-contact-row" id="detCitaClienteTelRow">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                </svg>
                                <span id="detCitaClienteTel">No registrado</span>
                            </span>
                            <span class="detalle-contact-row" id="detCitaClienteEmailRow">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                    <polyline points="22,6 12,13 2,6"></polyline>
                                </svg>
                                <span id="detCitaClienteEmail">Sin correo</span>
                            </span>
                        </div>
                    </div>
                </div>

                {{-- 2. Tarjeta: Especialista Asignado --}}
                <div class="detalle-info-card">
                    <div class="detalle-card-icon icon-cyan">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                            <rect x="2" y="7" width="20" height="14" rx="2"></rect>
                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                        </svg>
                    </div>
                    <div class="detalle-card-data">
                        <span class="detalle-card-label">Especialista Asignado</span>
                        <strong class="detalle-card-val" id="detCitaEspecialista">—</strong>
                        <span class="detalle-card-sub" id="detCitaEspecialistaRol">Equipo Profesional Beauty & Barber</span>
                    </div>
                </div>

                {{-- 3. Tarjeta: Servicio y Precio --}}
                <div class="detalle-info-card">
                    <div class="detalle-card-icon icon-amber">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M12 6v12M15 9.5c0-1.5-1.5-2-3-2s-3 .5-3 2 1.5 2 3 2 3 .5 3 2-1.5 2-3 2-3-.5-3-2"></path>
                        </svg>
                    </div>
                    <div class="detalle-card-data">
                        <span class="detalle-card-label">Servicio Contratado</span>
                        <strong class="detalle-card-val" id="detCitaServicio">—</strong>
                        <div class="detalle-tags-row">
                            <span class="detalle-tag" id="detCitaCategoria">Servicio</span>
                            <span class="detalle-price-tag" id="detCitaPrecio">$0.00</span>
                        </div>
                    </div>
                </div>

                {{-- 4. Tarjeta: Fecha, Horario y Duración --}}
                <div class="detalle-info-card">
                    <div class="detalle-card-icon icon-blue">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <div class="detalle-card-data">
                        <span class="detalle-card-label">Fecha y Horario</span>
                        <strong class="detalle-card-val" id="detCitaHorario">—</strong>
                        <span class="detalle-card-sub" id="detCitaFechaTexto">—</span>
                        <div class="detalle-tags-row" style="margin-top: 4px;">
                            <span class="detalle-tag" id="detCitaDuracion">30 min</span>
                        </div>
                    </div>
                </div>

                {{-- 5. Tarjeta: Check-in / Llegada del Cliente (Subtarea 5) --}}
                <div class="detalle-info-card" id="detCitaCheckInCard">
                    <div class="detalle-card-icon icon-slate" id="detCitaCheckInIcon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                    <div class="detalle-card-data">
                        <span class="detalle-card-label">Check-in de Llegada</span>
                        <strong class="detalle-card-val" id="detCitaCheckInTexto">Pendiente de llegada</strong>
                        <span class="detalle-card-sub" id="detCitaCheckInSub">El cliente aún no ha registrado arribo</span>
                    </div>
                </div>
            </div>

            {{-- Sección de Notas y Observaciones --}}
            <div class="detalle-notes-container">
                <div class="detalle-note-box">
                    <div class="detalle-note-header">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                        <span>Notas del Cliente / Agendamiento</span>
                    </div>
                    <p class="detalle-note-content" id="detCitaNotas">Sin notas adicionales.</p>
                </div>

                <div class="detalle-note-box">
                    <div class="detalle-note-header">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                        <span>Observaciones del Especialista</span>
                    </div>
                    <p class="detalle-note-content" id="detCitaObservaciones">Sin observaciones registradas.</p>
                </div>
            </div>
        </div>

        {{-- Pie del modal --}}
        <div class="detalle-modal-footer">
            <div class="detalle-modal-footer-actions">
                {{-- Indicador exclusivo para Administrador --}}
                <div id="detCitaAdminBadge" style="display: none; align-items: center; gap: 7px; font-size: 12px; color: #7dd3fc; font-weight: 600; padding: 6px 12px; background: rgba(56, 189, 248, 0.08); border: 1px solid rgba(56, 189, 248, 0.25); border-radius: 8px;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    <span>Modo Consulta • Solo lectura</span>
                </div>

                {{-- Botón Registrar Check-in (Subtarea 5) --}}
                <button type="button" class="btn-detalle-checkin" id="btnDetalleCheckInCita" style="display: none;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="15" height="15">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <span>Registrar Check-in</span>
                </button>
                {{-- Botón Iniciar Atención (Subtarea 6) --}}
                <button type="button" class="btn-detalle-iniciar" id="btnDetalleIniciarAtencion" style="display: none;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="15" height="15">
                        <polygon points="5 3 19 12 5 21 5 3"></polygon>
                    </svg>
                    <span>Iniciar Atención</span>
                </button>
                {{-- Botón Finalizar Atención (Subtarea 6) --}}
                <button type="button" class="btn-detalle-finalizar" id="btnDetalleFinalizarAtencion" style="display: none;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="15" height="15">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    <span>Finalizar Atención</span>
                </button>
                <button type="button" class="btn-detalle-cancelar" id="btnDetalleCancelarCita" style="display: none;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="15" y1="9" x2="9" y2="15"></line>
                        <line x1="9" y1="9" x2="15" y2="15"></line>
                    </svg>
                    <span>Cancelar Cita</span>
                </button>
                <button type="button" class="btn-detalle-editar" id="btnDetalleEditarCita" style="display: none;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                    <span>Editar Cita</span>
                </button>
            </div>
            <button type="button" class="btn-detalle-cerrar" id="btnCerrarDetalleModalFooter">
                Cerrar consulta
            </button>
        </div>
    </div>
</div>

<style>
    /* Estilos del Modal de Detalle Compartido */
    .detalle-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(3, 7, 18, 0.85);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        z-index: 99999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        animation: detFadeIn 0.2s ease-out;
    }

    @keyframes detFadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .detalle-modal-card {
        background: #0a1124;
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 20px;
        width: 100%;
        max-width: 680px;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.8), 0 0 0 1px rgba(37, 99, 235, 0.15);
        overflow: hidden;
        animation: detSlideUp 0.25s var(--ease-fluid, cubic-bezier(0.25, 1, 0.5, 1));
    }

    @keyframes detSlideUp {
        from { transform: translateY(20px) scale(0.97); opacity: 0; }
        to { transform: translateY(0) scale(1); opacity: 1; }
    }

    .detalle-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 22px 26px 18px 26px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        background: rgba(255, 255, 255, 0.02);
    }

    .detalle-modal-header-info {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .detalle-modal-pretitle {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .detalle-folio-tag {
        font-family: monospace;
        font-size: 13px;
        font-weight: 700;
        color: #93c5fd;
        background: rgba(37, 99, 235, 0.18);
        border: 1px solid rgba(37, 99, 235, 0.35);
        padding: 3px 10px;
        border-radius: 6px;
        letter-spacing: 0.05em;
    }

    .detalle-modal-title {
        font-size: 20px;
        font-weight: 800;
        color: #ffffff;
        letter-spacing: -0.01em;
    }

    .detalle-modal-close {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #94a3b8;
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .detalle-modal-close:hover {
        background: rgba(239, 68, 68, 0.15);
        border-color: rgba(239, 68, 68, 0.4);
        color: #f87171;
        transform: rotate(90deg);
    }

    .detalle-modal-body {
        padding: 24px 26px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .detalle-modal-loader {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
        color: #94a3b8;
        gap: 12px;
    }

    .detalle-spinner {
        width: 36px;
        height: 36px;
        border: 3px solid rgba(37, 99, 235, 0.2);
        border-top-color: #3b82f6;
        border-radius: 50%;
        animation: detSpin 0.7s linear infinite;
    }

    @keyframes detSpin {
        to { transform: rotate(360deg); }
    }

    .detalle-modal-content-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 14px;
    }

    @media (max-width: 640px) {
        .detalle-modal-content-grid {
            grid-template-columns: 1fr;
        }
    }

    .detalle-info-card {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.07);
        border-radius: 14px;
        padding: 16px;
        display: flex;
        align-items: flex-start;
        gap: 14px;
        transition: border-color 0.2s ease;
    }

    .detalle-info-card:hover {
        border-color: rgba(255, 255, 255, 0.15);
    }

    .detalle-card-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .icon-purple  { background: rgba(139, 92, 246, 0.12); color: #a78bfa; border: 1px solid rgba(139, 92, 246, 0.28); }
    .icon-cyan    { background: rgba(13, 148, 136, 0.12); color: #2dd4bf; border: 1px solid rgba(13, 148, 136, 0.28); }
    .icon-amber   { background: rgba(245, 158, 11, 0.12); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.28); }
    .icon-blue    { background: rgba(37, 99, 235, 0.12); color: #60a5fa; border: 1px solid rgba(37, 99, 235, 0.28); }
    .icon-emerald { background: rgba(16, 185, 129, 0.12); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.28); }
    .icon-slate   { background: rgba(148, 163, 184, 0.1); color: #94a3b8; border: 1px solid rgba(148, 163, 184, 0.2); }

    .detalle-card-data {
        display: flex;
        flex-direction: column;
        flex: 1;
        min-width: 0;
    }

    .detalle-card-label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        font-weight: 700;
        color: #94a3b8;
        margin-bottom: 2px;
    }

    .detalle-card-val {
        font-size: 15px;
        font-weight: 700;
        color: #ffffff;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .detalle-card-sub {
        font-size: 12px;
        color: #94a3b8;
        margin-top: 2px;
    }

    .detalle-contact-items {
        display: flex;
        flex-direction: column;
        gap: 4px;
        margin-top: 6px;
    }

    .detalle-contact-row {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: #cbd5e1;
    }

    .detalle-contact-row svg {
        color: #94a3b8;
        flex-shrink: 0;
    }

    .detalle-tags-row {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 6px;
        flex-wrap: wrap;
    }

    .detalle-tag {
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 6px;
        background: rgba(255, 255, 255, 0.08);
        color: #cbd5e1;
        font-weight: 600;
    }

    .detalle-price-tag {
        font-size: 14px;
        font-weight: 800;
        color: #60a5fa;
    }

    .detalle-notes-container {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .detalle-note-box {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 12px;
        padding: 14px 16px;
    }

    .detalle-note-header {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        font-weight: 700;
        color: #94a3b8;
        margin-bottom: 6px;
    }

    .detalle-note-content {
        font-size: 13px;
        color: #e2e8f0;
        line-height: 1.45;
        white-space: pre-wrap;
    }

    .detalle-modal-footer {
        padding: 16px 26px;
        border-top: 1px solid rgba(255, 255, 255, 0.08);
        background: rgba(255, 255, 255, 0.015);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .detalle-modal-footer-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .btn-detalle-checkin {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(16, 185, 129, 0.12);
        border: 1px solid rgba(16, 185, 129, 0.28);
        color: #10b981;
        font-size: 13px;
        font-weight: 700;
        padding: 10px 18px;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-detalle-checkin:hover {
        background: #059669;
        border-color: #059669;
        color: #ffffff;
        transform: translateY(-1px);
    }

    .btn-detalle-iniciar {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(245, 158, 11, 0.12);
        border: 1px solid rgba(245, 158, 11, 0.28);
        color: #f59e0b;
        font-size: 13px;
        font-weight: 700;
        padding: 10px 18px;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-detalle-iniciar:hover {
        background: #d97706;
        border-color: #d97706;
        color: #ffffff;
        transform: translateY(-1px);
    }

    .btn-detalle-finalizar {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(16, 185, 129, 0.12);
        border: 1px solid rgba(16, 185, 129, 0.28);
        color: #10b981;
        font-size: 13px;
        font-weight: 700;
        padding: 10px 18px;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-detalle-finalizar:hover {
        background: #059669;
        border-color: #059669;
        color: #ffffff;
        transform: translateY(-1px);
    }

    .btn-detalle-cancelar {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(220, 38, 38, 0.12);
        border: 1px solid rgba(220, 38, 38, 0.28);
        color: #f87171;
        font-size: 13px;
        font-weight: 700;
        padding: 10px 16px;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-detalle-cancelar:hover {
        background: rgba(220, 38, 38, 0.22);
        border-color: rgba(220, 38, 38, 0.5);
        color: #ffffff;
    }

    .btn-detalle-editar {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(37, 99, 235, 0.12);
        border: 1px solid rgba(37, 99, 235, 0.28);
        color: #60a5fa;
        font-size: 13px;
        font-weight: 700;
        padding: 10px 18px;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-detalle-editar:hover {
        background: #1e40af;
        border-color: rgba(147, 197, 253, 0.45);
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
    }

    .btn-detalle-cerrar {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.12);
        color: #f1f5f9;
        font-size: 13.5px;
        font-weight: 700;
        padding: 10px 22px;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-detalle-cerrar:hover {
        background: rgba(255, 255, 255, 0.15);
        border-color: rgba(255, 255, 255, 0.25);
        color: #ffffff;
    }
</style>

<script>
    (function () {
        // Helper global para recargar la vista preservando la pestaña activa y la posición de scroll
        if (!window.recargarPreservandoVista) {
            window.recargarPreservandoVista = function() {
                try {
                    const currentUrl = new URL(window.location.href);
                    const isListado = (
                        document.getElementById('vistaListadoContainer')?.style.display !== 'none' ||
                        sessionStorage.getItem('admin_citas_tab') === 'listado' ||
                        currentUrl.searchParams.get('tab') === 'listado'
                    );

                    if (window.location.pathname.includes('/admin/citas')) {
                        if (isListado) {
                            currentUrl.searchParams.set('tab', 'listado');
                            sessionStorage.setItem('admin_citas_tab', 'listado');
                        } else {
                            currentUrl.searchParams.set('tab', 'calendario');
                            sessionStorage.setItem('admin_citas_tab', 'calendario');
                        }
                    }

                    sessionStorage.setItem('citas_scroll_y', String(window.scrollY));
                    window.location.href = currentUrl.toString();
                } catch (e) {
                    window.location.reload();
                }
            };
        }

        function obtenerModalDetalle() {
            return document.getElementById('modalDetalleCitaBackdrop');
        }

        function cerrarModal() {
            const m = obtenerModalDetalle();
            if (m) {
                m.style.display = 'none';
                document.body.style.overflow = '';
            }
        }

        // Delegación global para botones de cierre y click fuera
        document.addEventListener('click', function (e) {
            const m = obtenerModalDetalle();
            if (!m || m.style.display === 'none') return;

            if (e.target.closest('#btnCerrarDetalleModal') ||
                e.target.closest('#btnCerrarDetalleModalFooter') ||
                e.target === m) {
                cerrarModal();
            }
        });

        document.addEventListener('keydown', function (e) {
            const m = obtenerModalDetalle();
            if (e.key === 'Escape' && m && m.style.display !== 'none') {
                cerrarModal();
            }
        });

        // Función global accesible para abrir el modal con datos o llamada AJAX
        window.abrirModalDetalleCita = function (citaId, detalleUrl) {
            const modalEl = obtenerModalDetalle();
            if (!modalEl) {
                console.error('Modal detalle (#modalDetalleCitaBackdrop) no encontrado en el DOM.');
                return;
            }

            const loaderEl = document.getElementById('detCitaLoader');
            const contentGridEl = document.getElementById('detCitaContentGrid');

            // 1. Mostrar modal inmediatamente para retroalimentación visual instantánea
            modalEl.style.display = 'flex';
            document.body.style.overflow = 'hidden';

            if (loaderEl) loaderEl.style.display = 'flex';
            if (contentGridEl) contentGridEl.style.opacity = '0.3';

            // Helper defensivo para evitar excepciones si un nodo no estuviera en DOM
            const setSafeText = (id, text) => {
                const el = document.getElementById(id);
                if (el) el.textContent = text;
            };

            // 2. Resetear datos
            setSafeText('detCitaFolio', 'CITA-' + String(citaId).padStart(5, '0'));
            const badge = document.getElementById('detCitaStatusBadge');
            if (badge) {
                badge.className = 'status-badge';
                badge.textContent = 'Consultando...';
            }

            setSafeText('detCitaClienteNombre', '—');
            setSafeText('detCitaClienteTel', '—');
            setSafeText('detCitaClienteEmail', '—');
            setSafeText('detCitaEspecialista', '—');
            setSafeText('detCitaServicio', '—');
            setSafeText('detCitaCategoria', '—');
            setSafeText('detCitaPrecio', '—');
            setSafeText('detCitaHorario', '—');
            setSafeText('detCitaFechaTexto', '—');
            setSafeText('detCitaDuracion', '—');
            setSafeText('detCitaNotas', 'Sin notas adicionales.');
            setSafeText('detCitaObservaciones', 'Sin observaciones registradas.');

            // 3. Petición AJAX al backend sin mutación de estado
            const fetchUrl = detalleUrl || `/recepcion/citas/${citaId}/detalle`;

            fetch(fetchUrl, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => {
                if (!res.ok) throw new Error('Error al obtener la cita: ' + res.status);
                return res.json();
            })
            .then(data => {
                if (!data.success || !data.cita) {
                    throw new Error(data.error || 'No se recibieron datos de la cita.');
                }
                const c = data.cita;

                // Actualizar Folio y Badge de Estado
                setSafeText('detCitaFolio', c.folio || ('CITA-' + String(c.id).padStart(5, '0')));
                if (badge) {
                    badge.className = 'status-badge ' + (c.estado_visual || c.estado || 'confirmada');
                    badge.textContent = c.estado_label || (c.estado ? c.estado.toUpperCase() : 'CONFIRMADA');
                }

                // Cliente
                setSafeText('detCitaClienteNombre', c.cliente?.nombre_completo || 'Cliente #' + c.cliente?.id);
                setSafeText('detCitaClienteTel', c.cliente?.telefono || 'No registrado');
                setSafeText('detCitaClienteEmail', c.cliente?.email || 'Sin correo');

                // Especialista
                setSafeText('detCitaEspecialista', c.trabajador?.nombre_completo || 'Especialista #' + c.trabajador?.id);

                // Servicio y Precio
                setSafeText('detCitaServicio', c.servicio?.nombre || 'Servicio');
                setSafeText('detCitaCategoria', c.servicio?.categoria ? (c.servicio.categoria.charAt(0).toUpperCase() + c.servicio.categoria.slice(1)) : 'General');
                setSafeText('detCitaPrecio', c.precio_formateado || ('$' + Number(c.precio || 0).toFixed(2)));

                // Fecha y Horario
                setSafeText('detCitaHorario', (c.hora_inicio || '') + ' - ' + (c.hora_fin || ''));
                setSafeText('detCitaFechaTexto', c.fecha_formateada || c.fecha || 'Fecha programada');
                setSafeText('detCitaDuracion', (c.duracion_minutos || 30) + ' min');

                // Notas y Observaciones
                setSafeText('detCitaNotas', (c.notas && c.notas.trim()) ? c.notas : 'Sin notas adicionales.');
                setSafeText('detCitaObservaciones', (c.observaciones && c.observaciones.trim()) ? c.observaciones : 'Sin observaciones registradas.');

                // Configurar Check-in de Llegada (Subtarea 5)
                const checkInCard = document.getElementById('detCitaCheckInCard');
                const checkInIcon = document.getElementById('detCitaCheckInIcon');
                const checkInTexto = document.getElementById('detCitaCheckInTexto');
                const checkInSub = document.getElementById('detCitaCheckInSub');
                const btnCheckIn = document.getElementById('btnDetalleCheckInCita');
                const btnIniciar = document.getElementById('btnDetalleIniciarAtencion');
                const btnFinalizar = document.getElementById('btnDetalleFinalizarAtencion');
                const btnEditar = document.getElementById('btnDetalleEditarCita');
                const btnCancelar = document.getElementById('btnDetalleCancelarCita');
                const adminBadge = document.getElementById('detCitaAdminBadge');

                const isAdmin = window.location.pathname.startsWith('/admin');
                if (adminBadge) {
                    adminBadge.style.display = isAdmin ? 'inline-flex' : 'none';
                }

                if (c.checked_in) {
                    if (checkInTexto) {
                        checkInTexto.textContent = 'Llegada Registrada';
                        checkInTexto.style.color = '#10b981';
                    }
                    if (checkInSub) {
                        checkInSub.textContent = `Registrado a las ${c.check_in_hora || ''} hrs (${c.check_in_formateado || ''})`;
                    }
                    if (checkInIcon) {
                        checkInIcon.className = 'detalle-card-icon icon-emerald';
                    }
                    if (btnCheckIn) {
                        btnCheckIn.style.display = 'none';
                    }
                } else {
                    if (checkInTexto) {
                        checkInTexto.textContent = 'Pendiente de Llegada';
                        checkInTexto.style.color = '#94a3b8';
                    }
                    if (checkInSub) {
                        checkInSub.textContent = 'El cliente no ha registrado su arribo en recepción.';
                    }
                    if (checkInIcon) {
                        checkInIcon.className = 'detalle-card-icon icon-slate';
                    }

                    if (c.puede_check_in && !isAdmin) {
                        if (btnCheckIn) {
                            btnCheckIn.style.display = 'inline-flex';
                            const checkinUrl = `/recepcion/citas/${c.id}/checkin`;
                            btnCheckIn.onclick = function() {
                                if (window.registrarCheckInPrompt) {
                                    window.registrarCheckInPrompt(c.id, checkinUrl, c.folio, c.cliente?.nombre_completo, function(updatedCita) {
                                        window.abrirModalDetalleCita(c.id, fetchUrl);
                                    });
                                }
                            };
                        }
                    } else {
                        if (btnCheckIn) {
                            btnCheckIn.style.display = 'none';
                        }
                    }
                }

                // Si es Administrador: Solo permisos de visualización (no puede mutar estados, editar ni cancelar)
                if (isAdmin) {
                    if (btnCheckIn) btnCheckIn.style.display = 'none';
                    if (btnIniciar) btnIniciar.style.display = 'none';
                    if (btnFinalizar) btnFinalizar.style.display = 'none';
                    if (btnEditar) btnEditar.style.display = 'none';
                    if (btnCancelar) btnCancelar.style.display = 'none';
                    return;
                }

                // Configurar Botones de Atención (Subtarea 6)
                const estadoUrl = `/recepcion/citas/${c.id}/estado`;

                if (btnIniciar) {
                    if (c.puede_iniciar_atencion) {
                        btnIniciar.style.display = 'inline-flex';
                        btnIniciar.onclick = function() {
                            if (window.iniciarAtencionPrompt) {
                                window.iniciarAtencionPrompt(c.id, estadoUrl, c.folio, c.cliente?.nombre_completo, function(updatedCita) {
                                    window.abrirModalDetalleCita(c.id, fetchUrl);
                                });
                            }
                        };
                    } else {
                        btnIniciar.style.display = 'none';
                    }
                }

                if (btnFinalizar) {
                    if (c.puede_finalizar_atencion) {
                        btnFinalizar.style.display = 'inline-flex';
                        btnFinalizar.onclick = function() {
                            if (window.finalizarAtencionPrompt) {
                                window.finalizarAtencionPrompt(c.id, estadoUrl, c.folio, c.cliente?.nombre_completo, function(updatedCita) {
                                    window.abrirModalDetalleCita(c.id, fetchUrl);
                                });
                            }
                        };
                    } else {
                        btnFinalizar.style.display = 'none';
                    }
                }

                // Configurar botones de acción según el estado de la cita (solo para roles no admin)
                const estado = (c.estado || '').toLowerCase();

                if (btnEditar && btnCancelar) {
                    const updateUrl = isAdmin ? `/admin/citas/${c.id}` : `/recepcion/citas/${c.id}`;
                    const cancelUrl = isAdmin ? `/admin/citas/${c.id}/cancelar` : `/recepcion/citas/${c.id}/cancelar`;
                    const citaDetalleUrl = isAdmin ? `/admin/citas/${c.id}/detalle` : `/recepcion/citas/${c.id}/detalle`;
                    const editSpan = btnEditar.querySelector('span');

                    if (estado === 'completada') {
                        // Cita completada es registro contable/operativo final
                        btnEditar.style.display = 'none';
                        btnCancelar.style.display = 'none';
                    } else if (estado === 'cancelada') {
                        // En cancelada: se permite Reagendar a nuevo horario
                        btnEditar.style.display = 'inline-flex';
                        if (editSpan) editSpan.textContent = 'Reagendar Cita';
                        btnEditar.onclick = function() {
                            cerrarModal();
                            if (window.abrirModalEditarCita) {
                                window.abrirModalEditarCita(c.id, updateUrl, citaDetalleUrl);
                            }
                        };
                        btnCancelar.style.display = 'none';
                    } else if (estado === 'vencida') {
                        // En vencida: se permite Reagendar (no se permite cancelar citas vencidas/pasadas)
                        btnEditar.style.display = 'inline-flex';
                        if (editSpan) editSpan.textContent = 'Reagendar Cita';
                        btnEditar.onclick = function() {
                            cerrarModal();
                            if (window.abrirModalEditarCita) {
                                window.abrirModalEditarCita(c.id, updateUrl, citaDetalleUrl);
                            }
                        };

                        btnCancelar.style.display = 'none';
                    } else {
                        // Confirmada, En proceso / En atención
                        btnEditar.style.display = 'inline-flex';
                        if (editSpan) editSpan.textContent = 'Editar Cita';
                        btnEditar.onclick = function() {
                            cerrarModal();
                            if (window.abrirModalEditarCita) {
                                window.abrirModalEditarCita(c.id, updateUrl, citaDetalleUrl);
                            }
                        };

                        // Cancelar disponible únicamente si la cita es futura y cancelable
                        if (!c.es_pasada && c.puede_cancelar !== false) {
                            btnCancelar.style.display = 'inline-flex';
                            btnCancelar.onclick = function() {
                                if (window.cancelarCitaPrompt) {
                                    window.cancelarCitaPrompt(c.id, cancelUrl, c.folio);
                                }
                            };
                        } else {
                            btnCancelar.style.display = 'none';
                        }
                    }
                }
            })
            .catch(err => {
                console.error('Error al consultar cita:', err);
                const b = document.getElementById('detCitaStatusBadge');
                if (b) {
                    b.className = 'status-badge cancelada';
                    b.textContent = 'Error';
                }
                setSafeText('detCitaNotas', 'No se pudo cargar la información de la cita: ' + (err.message || 'Error de red o servidor.'));
            })
            .finally(() => {
                const l = document.getElementById('detCitaLoader');
                const cg = document.getElementById('detCitaContentGrid');
                if (l) l.style.display = 'none';
                if (cg) cg.style.opacity = '1';
            });
        };

        // Función Global Reutilizable para Check-in con SweetAlert2 (Subtarea 5)
        window.registrarCheckInPrompt = function (citaId, checkinUrl, folio, clienteNom, onCompleteCallback) {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const nombre = clienteNom || 'Cliente';
            const fol = folio || ('CITA-' + String(citaId).padStart(5, '0'));

            if (typeof Swal === 'undefined') {
                if (confirm(`¿Confirmar check-in de llegada para ${nombre} (${fol})?`)) {
                    ejecutarCheckInFetch();
                }
                return;
            }

            Swal.fire({
                title: '¿Registrar Llegada (Check-in)?',
                html: `Se registrará la llegada presencial de <b>${nombre}</b> para la cita <b>${fol}</b>.<br><span style="font-size: 13px; color: #94a3b8; display: inline-block; margin-top: 6px;">Quedará sellada con la marca de tiempo de recepción actual.</span>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#059669',
                cancelButtonColor: '#475569',
                confirmButtonText: 'Sí, registrar llegada',
                cancelButtonText: 'Cancelar',
                reverseButtons: true,
                background: '#0a1124',
                color: '#f8fafc'
            }).then((result) => {
                if (result.isConfirmed) {
                    ejecutarCheckInFetch();
                }
            });

            function ejecutarCheckInFetch() {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Procesando Check-in...',
                        text: 'Registrando la marca temporal de llegada...',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); },
                        background: '#0a1124',
                        color: '#f8fafc'
                    });
                }

                fetch(checkinUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(async (res) => {
                    const data = await res.json().catch(() => ({}));
                    if (!res.ok || !data.success) {
                        throw new Error(data.message || data.error || ('Error HTTP ' + res.status));
                    }
                    return data;
                })
                .then((data) => {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Llegada Registrada!',
                            html: `<b>${data.message || 'Check-in registrado con éxito.'}</b><br><span style="font-size: 13px; color: #10b981; display: inline-block; margin-top: 6px;">Hora: ${data.cita?.check_in_hora || 'Registrada'}</span>`,
                            confirmButtonColor: '#1e40af',
                            background: '#0a1124',
                            color: '#f8fafc',
                            timer: 2000,
                            timerProgressBar: true
                        }).then(() => {
                            if (typeof onCompleteCallback === 'function') {
                                onCompleteCallback(data.cita);
                            } else {
                                window.recargarPreservandoVista();
                            }
                        });
                    } else {
                        alert(data.message || 'Check-in registrado con éxito.');
                        if (typeof onCompleteCallback === 'function') {
                            onCompleteCallback(data.cita);
                        } else {
                            window.recargarPreservandoVista();
                        }
                    }
                })
                .catch((err) => {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'No se pudo realizar el Check-in',
                            text: err.message || 'Ocurrió un error inesperado al procesar la solicitud.',
                            confirmButtonColor: '#ef4444',
                            background: '#0a1124',
                            color: '#f8fafc'
                        });
                    } else {
                        alert('Error: ' + (err.message || 'No se pudo realizar el check-in.'));
                    }
                });
            }
        };

        // Función Global Reutilizable para Iniciar Atención (Subtarea 6)
        window.iniciarAtencionPrompt = function (citaId, estadoUrl, folio, clienteNom, onCompleteCallback) {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const nombre = clienteNom || 'Cliente';
            const fol = folio || ('CITA-' + String(citaId).padStart(5, '0'));

            if (typeof Swal === 'undefined') {
                if (confirm(`¿Iniciar la atención del servicio para ${nombre} (${fol})? Pasará a estado "En proceso".`)) {
                    ejecutarIniciarFetch();
                }
                return;
            }

            Swal.fire({
                title: '¿Iniciar Atención del Servicio?',
                html: `Se iniciará el servicio de <b>${nombre}</b> para la cita <b>${fol}</b>.<br><span style="font-size: 13px; color: #94a3b8; display: inline-block; margin-top: 6px;">El estado cambiará a <b>En proceso</b>. Si no contaba con check-in previo, se registrará la llegada automáticamente.</span>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#d97706',
                cancelButtonColor: '#475569',
                confirmButtonText: 'Sí, iniciar atención',
                cancelButtonText: 'Cancelar',
                reverseButtons: true,
                background: '#0a1124',
                color: '#f8fafc'
            }).then((result) => {
                if (result.isConfirmed) {
                    ejecutarIniciarFetch();
                }
            });

            function ejecutarIniciarFetch() {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Iniciando Servicio...',
                        text: 'Actualizando estado de la cita a En proceso...',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); },
                        background: '#0a1124',
                        color: '#f8fafc'
                    });
                }

                fetch(estadoUrl, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        estado: 'en_atencion'
                    })
                })
                .then(async (res) => {
                    const data = await res.json().catch(() => ({}));
                    if (!res.ok || !data.success) {
                        throw new Error(data.message || data.error || ('Error HTTP ' + res.status));
                    }
                    return data;
                })
                .then((data) => {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Atención Iniciada!',
                            html: `<b>${data.mensaje || 'La cita ahora se encuentra en proceso de atención.'}</b>`,
                            confirmButtonColor: '#1e40af',
                            background: '#0a1124',
                            color: '#f8fafc',
                            timer: 2000,
                            timerProgressBar: true
                        }).then(() => {
                            if (typeof onCompleteCallback === 'function') {
                                onCompleteCallback(data.cita);
                            } else {
                                window.recargarPreservandoVista();
                            }
                        });
                    } else {
                        alert(data.mensaje || 'Atención iniciada con éxito.');
                        if (typeof onCompleteCallback === 'function') {
                            onCompleteCallback(data.cita);
                        } else {
                            window.recargarPreservandoVista();
                        }
                    }
                })
                .catch((err) => {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'No se pudo iniciar la atención',
                            text: err.message || 'Ocurrió un error inesperado al procesar la solicitud.',
                            confirmButtonColor: '#ef4444',
                            background: '#0a1124',
                            color: '#f8fafc'
                        });
                    } else {
                        alert('Error: ' + (err.message || 'No se pudo iniciar la atención.'));
                    }
                });
            }
        };

        // Función Global Reutilizable para Finalizar Atención (Subtarea 6)
        window.finalizarAtencionPrompt = function (citaId, estadoUrl, folio, clienteNom, onCompleteCallback) {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const nombre = clienteNom || 'Cliente';
            const fol = folio || ('CITA-' + String(citaId).padStart(5, '0'));

            if (typeof Swal === 'undefined') {
                const obs = prompt(`¿Finalizar atención de la cita ${fol} para ${nombre}? Observaciones opcionales:`);
                if (obs !== null) {
                    ejecutarFinalizarFetch(obs);
                }
                return;
            }

            Swal.fire({
                title: '¿Finalizar Atención del Servicio?',
                html: `Se marcará la cita <b>${fol}</b> de <b>${nombre}</b> como <b>Completada</b>.<br><div style="text-align: left; margin-top: 14px;"><label style="font-size: 12.5px; color: #94a3b8; display: block; margin-bottom: 6px; font-weight: 600;">Observaciones técnicas del especialista (opcional):</label><textarea id="swalObsFinalizar" class="swal2-textarea" style="width: 100%; box-sizing: border-box; background: #070d1e; border: 1px solid rgba(255,255,255,0.15); color: #fff; border-radius: 8px; padding: 10px; font-size: 13px; min-height: 70px; resize: vertical;" placeholder="Ej. Corte clásico terminado con cera mate, cliente satisfecho..."></textarea></div>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#059669',
                cancelButtonColor: '#475569',
                confirmButtonText: 'Sí, finalizar servicio',
                cancelButtonText: 'Cancelar',
                reverseButtons: true,
                background: '#0a1124',
                color: '#f8fafc',
                preConfirm: () => {
                    const textarea = document.getElementById('swalObsFinalizar');
                    return textarea ? textarea.value.trim() : '';
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    ejecutarFinalizarFetch(result.value || null);
                }
            });

            function ejecutarFinalizarFetch(observaciones) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Finalizando Servicio...',
                        text: 'Completando la cita y registrando observaciones...',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); },
                        background: '#0a1124',
                        color: '#f8fafc'
                    });
                }

                fetch(estadoUrl, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        estado: 'completada',
                        observaciones: observaciones
                    })
                })
                .then(async (res) => {
                    const data = await res.json().catch(() => ({}));
                    if (!res.ok || !data.success) {
                        throw new Error(data.message || data.error || ('Error HTTP ' + res.status));
                    }
                    return data;
                })
                .then((data) => {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Servicio Completado!',
                            html: `<b>${data.mensaje || 'La cita ha sido marcada como completada con éxito.'}</b>`,
                            confirmButtonColor: '#1e40af',
                            background: '#0a1124',
                            color: '#f8fafc',
                            timer: 2000,
                            timerProgressBar: true
                        }).then(() => {
                            if (typeof onCompleteCallback === 'function') {
                                onCompleteCallback(data.cita);
                            } else {
                                window.recargarPreservandoVista();
                            }
                        });
                    } else {
                        alert(data.mensaje || 'Servicio completado con éxito.');
                        if (typeof onCompleteCallback === 'function') {
                            onCompleteCallback(data.cita);
                        } else {
                            window.recargarPreservandoVista();
                        }
                    }
                })
                .catch((err) => {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'No se pudo finalizar el servicio',
                            text: err.message || 'Ocurrió un error inesperado al procesar la solicitud.',
                            confirmButtonColor: '#ef4444',
                            background: '#0a1124',
                            color: '#f8fafc'
                        });
                    } else {
                        alert('Error: ' + (err.message || 'No se pudo finalizar el servicio.'));
                    }
                });
            }
        };
    })();
</script>
