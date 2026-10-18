@once('modal-editar-cita')
{{-- MODAL COMPARTIDO DE EDICIÓN Y GESTIÓN DE CITAS (ADMINISTRACIÓN Y RECEPCIÓN) --}}
<div class="edit-modal-backdrop" id="modalEditarCitaBackdrop" style="display: none;" role="dialog" aria-modal="true" aria-labelledby="modalEditarCitaTitulo">
    <div class="edit-modal-card">
        {{-- Encabezado fijo --}}
        <div class="edit-modal-header">
            <div class="edit-modal-header-info">
                <div class="edit-modal-pretitle">
                    <span class="edit-folio-tag" id="editCitaFolio">CITA-00000</span>
                    <span class="status-badge" id="editCitaStatusBadge">En edición</span>
                </div>
                <h3 class="edit-modal-title" id="modalEditarCitaTitulo">Modificar Cita</h3>
            </div>
            <button type="button" class="edit-modal-close" id="btnCerrarEditarModal" aria-label="Cerrar modal">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="18" height="18">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        {{-- Formulario estructurado para scroll vertical --}}
        <form id="formEditarCita" onsubmit="guardarEdicionCita(event)">
            <input type="hidden" id="editCitaId" value="">
            <input type="hidden" id="editCitaUpdateUrl" value="">

            {{-- Cuerpo scrollable con padding y límites claros --}}
            <div class="edit-modal-body">
                <div class="edit-modal-loader" id="editCitaLoader" style="display: none;">
                    <div class="edit-spinner"></div>
                    <p>Cargando información y disponibilidad...</p>
                </div>

                {{-- Banner informativo si la cita está cancelada o vencida --}}
                <div class="edit-alert-info" id="editModalInfoBanner" style="display: none;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    <span id="editModalInfoText">Reagendando cita: asigna una nueva fecha y horario disponible.</span>
                </div>

                {{-- Banner de errores de validación del servidor --}}
                <div class="edit-alert-error" id="editModalErrorBanner" style="display: none;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <span id="editModalErrorText">Error en los datos de la cita.</span>
                </div>

                <div class="edit-modal-form-grid" id="editCitaFormGrid">
                    {{-- 1. Cliente (Informativo) --}}
                    <div class="edit-form-group full-width">
                        <label class="edit-label">Cliente</label>
                        <div class="edit-client-preview">
                            <div class="edit-client-avatar">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </div>
                            <div class="edit-client-info">
                                <strong id="editCitaClienteNombre">—</strong>
                                <span id="editCitaClienteContacto" class="edit-subtext">—</span>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Servicio --}}
                    <div class="edit-form-group">
                        <label class="edit-label" for="editSelectServicio">Servicio *</label>
                        <select id="editSelectServicio" name="service_id" class="edit-select" required onchange="onEditServicioChange()">
                            <option value="">Seleccione un servicio...</option>
                            @isset($servicios)
                                @foreach($servicios as $s)
                                    <option value="{{ $s->id }}" data-duracion="{{ $s->duration_minutes }}" data-precio="{{ $s->price }}">
                                        {{ $s->name }} ({{ $s->duration_minutes }} min - ${{ number_format($s->price, 2) }})
                                    </option>
                                @endforeach
                            @endisset
                        </select>
                    </div>

                    {{-- 3. Especialista --}}
                    <div class="edit-form-group">
                        <label class="edit-label" for="editSelectTrabajador" style="display: flex; align-items: center; gap: 6px;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14" style="color: #60a5fa;">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            <span>Trabajador o especialista *</span>
                        </label>
                        <select id="editSelectTrabajador" name="trabajador_id" class="edit-select" required>
                            <option value="">Selecciona un trabajador</option>
                            @isset($trabajadores)
                                @foreach($trabajadores as $t)
                                    @php
                                        $serviciosIds = ($t->serviciosActivos && $t->serviciosActivos->isNotEmpty())
                                            ? $t->serviciosActivos->pluck('id')->values()->all()
                                            : ($t->servicios ? $t->servicios->pluck('id')->values()->all() : []);
                                    @endphp
                                    <option value="{{ $t->id }}" data-servicios="{{ implode(',', $serviciosIds) }}">
                                        {{ $t->nombre_completo ?? ($t->user->name . ' ' . ($t->user->apellidos ?? '')) }}
                                    </option>
                                @endforeach
                            @endisset
                        </select>
                        <p class="edit-hint-text" id="editWorkerHint" style="font-size: 11px; margin-top: 4px; color: #94a3b8;"></p>
                    </div>

                    {{-- 4. Fecha --}}
                    <div class="edit-form-group">
                        <label class="edit-label" for="editInputFecha">Fecha de la Cita *</label>
                        <input type="date" id="editInputFecha" name="fecha" class="edit-input" required min="{{ date('Y-m-d') }}">
                    </div>

                    {{-- 5. Horario de Inicio --}}
                    <div class="edit-form-group">
                        <label class="edit-label" for="editInputHora">Horario de Inicio (HH:MM) *</label>
                        <input type="time" id="editInputHora" name="hora_inicio" class="edit-input" required step="300">
                        <span class="edit-hint-text" id="editDuracionCalculada">Duración estimada: 30 min</span>
                    </div>

                    {{-- 6. Notas del Cliente / Agendamiento --}}
                    <div class="edit-form-group full-width">
                        <label class="edit-label" for="editInputNotas">Notas del Cliente</label>
                        <textarea id="editInputNotas" name="notas" class="edit-textarea" rows="2" placeholder="Indicaciones o peticiones especiales..."></textarea>
                    </div>

                    {{-- 7. Observaciones del Especialista / Recepción --}}
                    <div class="edit-form-group full-width">
                        <label class="edit-label" for="editInputObservaciones">Observaciones Internas</label>
                        <textarea id="editInputObservaciones" name="observaciones" class="edit-textarea" rows="2" placeholder="Notas operativas, seguimiento o motivo de reprogramación..."></textarea>
                    </div>
                </div>
            </div>

            {{-- Pie del modal fijo en la parte inferior --}}
            <div class="edit-modal-footer">
                <button type="button" class="btn-edit-cancelar" id="btnCerrarEditarModalFooter">
                    Cancelar
                </button>
                <button type="submit" class="btn-edit-guardar" id="btnGuardarEdicionSubmit">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="16" height="16">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <span>Guardar Cambios</span>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    /* Estilos del Modal de Edición Compartido y Responsivo */
    .edit-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(3, 7, 18, 0.85);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        z-index: 2000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
        animation: editFadeIn 0.2s ease-out;
    }

    @keyframes editFadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .edit-modal-card {
        background: #0a1124;
        border: 1px solid rgba(255, 255, 255, 0.14);
        border-radius: 18px;
        width: 100%;
        max-width: 620px;
        max-height: calc(100vh - 40px);
        display: flex;
        flex-direction: column;
        box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.85), 0 0 0 1px rgba(37, 99, 235, 0.25);
        overflow: hidden;
        animation: editSlideUp 0.25s cubic-bezier(0.25, 1, 0.5, 1);
        margin: auto;
    }

    @keyframes editSlideUp {
        from { transform: translateY(16px) scale(0.98); opacity: 0; }
        to { transform: translateY(0) scale(1); opacity: 1; }
    }

    .edit-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 22px 14px 22px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        background: rgba(255, 255, 255, 0.02);
        flex-shrink: 0;
    }

    .edit-modal-header-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .edit-modal-pretitle {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .edit-folio-tag {
        font-family: monospace;
        font-size: 11.5px;
        font-weight: 700;
        color: #93c5fd;
        background: rgba(37, 99, 235, 0.15);
        border: 1px solid rgba(37, 99, 235, 0.3);
        padding: 2px 7px;
        border-radius: 5px;
    }

    .edit-modal-title {
        font-size: 18px;
        font-weight: 800;
        color: #ffffff;
        letter-spacing: -0.02em;
        margin: 0;
    }

    .edit-modal-close {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 8px;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .edit-modal-close:hover {
        background: rgba(239, 68, 68, 0.2);
        color: #f87171;
        border-color: rgba(239, 68, 68, 0.4);
    }

    #formEditarCita {
        display: flex;
        flex-direction: column;
        flex: 1;
        min-height: 0;
        overflow: hidden;
        margin: 0;
    }

    .edit-modal-body {
        padding: 16px 22px;
        overflow-y: auto;
        flex: 1;
        min-height: 0;
        position: relative;
        -webkit-overflow-scrolling: touch;
    }

    /* Scrollbar visible y fluido */
    .edit-modal-body::-webkit-scrollbar {
        width: 6px;
    }
    .edit-modal-body::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.02);
        border-radius: 4px;
    }
    .edit-modal-body::-webkit-scrollbar-thumb {
        background: rgba(37, 99, 235, 0.45);
        border-radius: 4px;
    }
    .edit-modal-body::-webkit-scrollbar-thumb:hover {
        background: #1e40af;
    }

    .edit-alert-error {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 14px;
        background: rgba(220, 38, 38, 0.12);
        border: 1px solid rgba(220, 38, 38, 0.35);
        border-radius: 8px;
        color: #fca5a5;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 14px;
    }

    .edit-alert-info {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 14px;
        background: rgba(37, 99, 235, 0.12);
        border: 1px solid rgba(37, 99, 235, 0.28);
        border-radius: 8px;
        color: #93c5fd;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 14px;
    }

    .edit-modal-form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }

    .edit-form-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .edit-form-group.full-width {
        grid-column: span 2;
    }

    .edit-label {
        font-size: 11.5px;
        font-weight: 700;
        color: #cbd5e1;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .edit-hint-text {
        font-size: 11px;
        color: #94a3b8;
        margin-top: 1px;
    }

    .edit-input,
    .edit-select,
    .edit-textarea {
        width: 100%;
        padding: 8px 12px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 8px;
        color: #ffffff;
        font-size: 13px;
        font-family: inherit;
        outline: none;
        transition: all 0.2s ease;
    }

    .edit-select option {
        background-color: #0c142b;
        color: #ffffff;
    }

    .edit-input:focus,
    .edit-select:focus,
    .edit-textarea:focus {
        border-color: #1e40af;
        background: rgba(30, 64, 175, 0.08);
        box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.2);
    }

    .edit-client-preview {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 12px;
        background: rgba(255, 255, 255, 0.03);
        border: 1px dashed rgba(255, 255, 255, 0.12);
        border-radius: 8px;
    }

    .edit-client-avatar {
        width: 32px;
        height: 32px;
        border-radius: 7px;
        background: rgba(139, 92, 246, 0.12);
        color: #a78bfa;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .edit-client-info {
        display: flex;
        flex-direction: column;
    }

    .edit-client-info strong {
        color: #ffffff;
        font-size: 13px;
    }

    .edit-subtext {
        font-size: 11.5px;
        color: #94a3b8;
    }

    .edit-modal-footer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        padding: 12px 22px;
        border-top: 1px solid rgba(255, 255, 255, 0.08);
        background: rgba(255, 255, 255, 0.02);
        flex-shrink: 0;
    }

    .btn-edit-cancelar {
        padding: 8px 16px;
        background: rgba(255, 255, 255, 0.06);
        color: #cbd5e1;
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-edit-cancelar:hover {
        background: rgba(255, 255, 255, 0.1);
        color: #ffffff;
    }

    .btn-edit-guardar {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 18px;
        background: #1e40af;
        color: #ffffff;
        border: 1px solid rgba(147, 197, 253, 0.28);
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.35);
        transition: all 0.2s ease;
    }

    .btn-edit-guardar:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
        border-color: rgba(147, 197, 253, 0.45);
        background: #1d4ed8;
        color: #ffffff;
    }

    .btn-edit-guardar:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    .edit-modal-loader {
        position: absolute;
        inset: 0;
        background: rgba(10, 17, 36, 0.85);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 10px;
        z-index: 10;
        color: #94a3b8;
        font-size: 13px;
    }

    .edit-spinner {
        width: 32px;
        height: 32px;
        border: 3px solid rgba(30, 64, 175, 0.2);
        border-top-color: #1e40af;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    @media (max-width: 640px) {
        .edit-modal-card {
            max-height: 94vh;
        }
        .edit-modal-form-grid {
            grid-template-columns: 1fr;
        }
        .edit-form-group.full-width {
            grid-column: span 1;
        }
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

        const modal = document.getElementById('modalEditarCitaBackdrop');
        const btnCerrar = document.getElementById('btnCerrarEditarModal');
        const btnCerrarFooter = document.getElementById('btnCerrarEditarModalFooter');
        const errorBanner = document.getElementById('editModalErrorBanner');
        const errorText = document.getElementById('editModalErrorText');
        const infoBanner = document.getElementById('editModalInfoBanner');
        const infoText = document.getElementById('editModalInfoText');
        const loader = document.getElementById('editCitaLoader');
        const submitBtn = document.getElementById('btnGuardarEdicionSubmit');
        const modalTitle = document.getElementById('modalEditarCitaTitulo');

        function cerrarModal() {
            if (modal) modal.style.display = 'none';
            document.body.style.overflow = '';
            if (errorBanner) errorBanner.style.display = 'none';
            if (infoBanner) infoBanner.style.display = 'none';
        }

        if (btnCerrar) btnCerrar.addEventListener('click', cerrarModal);
        if (btnCerrarFooter) btnCerrarFooter.addEventListener('click', cerrarModal);

        if (modal) {
            modal.addEventListener('click', function (e) {
                if (e.target === modal) cerrarModal();
            });
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && modal && modal.style.display !== 'none') {
                cerrarModal();
            }
        });

        // Catálogo maestro de especialistas activos y sus servicios asignados
        window.CATALOGO_TRABAJADORES = [
            @isset($trabajadores)
                @foreach($trabajadores as $t)
                    @php
                        $serviciosIds = ($t->serviciosActivos && $t->serviciosActivos->isNotEmpty())
                            ? $t->serviciosActivos->pluck('id')->values()->all()
                            : ($t->servicios ? $t->servicios->pluck('id')->values()->all() : []);
                    @endphp
                    {
                        id: {{ (int)$t->id }},
                        nombre: @json($t->nombre_completo ?: trim(($t->user->name ?? '') . ' ' . ($t->user->apellidos ?? ''))),
                        servicios: @json(array_values(array_map('intval', $serviciosIds)))
                    }@if(!$loop->last),@endif
                @endforeach
            @endisset
        ];

        // Filtrar especialistas dinámicamente según el servicio seleccionado
        // Maneja exactamente la misma lógica de visualización que en el agendamiento del cliente:
        // - Si no hay especialistas asignados: select deshabilitado, texto "Sin especialistas para este servicio" y aviso
        // - Si hay 1 solo especialista: auto-selección inmediata
        // - Si hay varios: preserva el seleccionado o pide elegir
        window.filtrarEspecialistasPorServicio = function (serviceId, preselectedWorkerId = null) {
            const selTrabajador = document.getElementById('editSelectTrabajador');
            const hintCount = document.getElementById('editWorkerHint') || document.getElementById('editWorkerCountHint');
            if (!selTrabajador) return;

            const sid = serviceId ? parseInt(serviceId, 10) : null;
            const targetWorkerVal = (preselectedWorkerId !== null && preselectedWorkerId !== undefined && preselectedWorkerId !== '')
                ? String(preselectedWorkerId)
                : String(selTrabajador.value || '');

            // Asegurar catálogo de trabajadores
            let catalogo = window.CATALOGO_TRABAJADORES;
            if (!Array.isArray(catalogo) || catalogo.length === 0) {
                catalogo = [];
                const existingOptions = selTrabajador.querySelectorAll('option[data-servicios]');
                existingOptions.forEach(opt => {
                    const raw = opt.getAttribute('data-servicios') || '';
                    catalogo.push({
                        id: parseInt(opt.value, 10),
                        nombre: opt.textContent.trim(),
                        servicios: raw.split(',').map(s => parseInt(s.trim(), 10)).filter(n => !isNaN(n))
                    });
                });
                window.CATALOGO_TRABAJADORES = catalogo;
            }

            // Limpiar opciones actuales del select
            selTrabajador.innerHTML = '';

            const defaultOpt = document.createElement('option');
            defaultOpt.value = '';
            defaultOpt.textContent = 'Selecciona un trabajador';
            selTrabajador.appendChild(defaultOpt);

            // Caso 1: No hay servicio seleccionado
            if (!sid) {
                selTrabajador.disabled = true;
                defaultOpt.selected = true;
                selTrabajador.value = '';
                if (hintCount) {
                    hintCount.textContent = 'Primero selecciona un servicio en el catálogo para ver los especialistas disponibles.';
                    hintCount.style.color = '#94a3b8';
                }
                return;
            }

            // Caso 2: Filtrar ÚNICAMENTE especialistas que realizan este servicio seleccionado
            const matchingWorkers = catalogo.filter(t => {
                return Array.isArray(t.servicios) && t.servicios.includes(sid);
            });

            if (matchingWorkers.length > 0) {
                selTrabajador.disabled = false;

                let foundSelected = false;
                matchingWorkers.forEach(w => {
                    const opt = document.createElement('option');
                    opt.value = String(w.id);
                    opt.textContent = w.nombre;
                    opt.setAttribute('data-name', w.nombre);
                    opt.setAttribute('data-servicios', Array.isArray(w.servicios) ? w.servicios.join(',') : '');

                    if (targetWorkerVal && String(w.id) === targetWorkerVal) {
                        opt.selected = true;
                        foundSelected = true;
                    }
                    selTrabajador.appendChild(opt);
                });

                if (matchingWorkers.length === 1) {
                    // Si el servicio tiene un único especialista disponible, auto-seleccionarlo de inmediato
                    const singleWorker = matchingWorkers[0];
                    selTrabajador.value = String(singleWorker.id);
                    if (hintCount) {
                        hintCount.textContent = `Especialista asignado: ${singleWorker.nombre}`;
                        hintCount.style.color = '#10b981';
                    }
                } else if (foundSelected) {
                    selTrabajador.value = targetWorkerVal;
                    if (hintCount) {
                        hintCount.textContent = `Mostrando ${matchingWorkers.length} especialista(s) disponibles para este servicio.`;
                        hintCount.style.color = '#60a5fa';
                    }
                } else {
                    defaultOpt.selected = true;
                    selTrabajador.value = '';
                    if (hintCount) {
                        hintCount.textContent = `Mostrando ${matchingWorkers.length} especialistas. Por favor selecciona al especialista preferido.`;
                        hintCount.style.color = '#cbd5e1';
                    }
                }
            } else {
                // NO hay especialistas asignados a este servicio en el sistema (IGUAL QUE EN EL CLIENTE)
                selTrabajador.disabled = true;
                defaultOpt.textContent = 'Sin especialistas para este servicio';
                defaultOpt.selected = true;
                selTrabajador.value = '';
                if (hintCount) {
                    hintCount.textContent = 'Aviso: Actualmente no hay especialistas asignados a este servicio en el estudio.';
                    hintCount.style.color = '#f59e0b';
                }
            }
        };

        // Evento disparado cuando la recepcionista cambia manualmente el servicio en el selector
        window.onEditServicioChange = function() {
            const selServicio = document.getElementById('editSelectServicio');
            const duracionHint = document.getElementById('editDuracionCalculada');
            if (selServicio && selServicio.selectedIndex >= 0) {
                const opt = selServicio.options[selServicio.selectedIndex];
                const duracion = opt ? (opt.getAttribute('data-duracion') || 30) : 30;
                if (duracionHint) {
                    duracionHint.textContent = `Duración estimada: ${duracion} min`;
                }
            }

            const sid = selServicio ? selServicio.value : '';
            // Al cambiar de servicio deliberadamente, preselectedWorkerId es null
            // para que se mantenga el especialista anterior solo si está asignado al nuevo servicio
            window.filtrarEspecialistasPorServicio(sid, null);
        };

        // Alias para compatibilidad
        window.onEditServicioOrWorkerChange = window.onEditServicioChange;

        // Función global para abrir el modal con datos
        window.abrirModalEditarCita = function (citaId, updateUrl, detalleUrl) {
            if (!modal) return;

            // Determinar URLs de fallback según el prefijo de la página actual
            const isAdmin = window.location.pathname.startsWith('/admin');
            const finalUpdateUrl = updateUrl || (isAdmin ? `/admin/citas/${citaId}` : `/recepcion/citas/${citaId}`);
            const finalDetalleUrl = detalleUrl || (isAdmin ? `/admin/citas/${citaId}/detalle` : `/recepcion/citas/${citaId}/detalle`);

            // Resetear estados
            document.getElementById('editCitaId').value = citaId;
            document.getElementById('editCitaUpdateUrl').value = finalUpdateUrl;
            document.getElementById('editCitaFolio').textContent = 'CITA-' + String(citaId).padStart(5, '0');
            if (errorBanner) errorBanner.style.display = 'none';
            if (infoBanner) infoBanner.style.display = 'none';

            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';

            if (loader) loader.style.display = 'flex';

            fetch(finalDetalleUrl, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => {
                if (!res.ok) throw new Error('Error al consultar cita: ' + res.status);
                return res.json();
            })
            .then(data => {
                if (!data.success || !data.cita) {
                    throw new Error(data.error || 'No se obtuvieron detalles de la cita.');
                }
                const c = data.cita;
                const estado = (c.estado || '').toLowerCase();
                const submitBtnSpan = submitBtn ? submitBtn.querySelector('span') : null;

                // Cita completada es inmutable en fechas/horarios
                if (estado === 'completada') {
                    cerrarModal();
                    if (window.Swal) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Cita Completada',
                            text: 'Esta cita ya fue completada con éxito. Por motivos de auditoría contable y registro del servicio prestado, su fecha y horario no pueden alterarse.',
                            confirmButtonColor: '#1e40af'
                        });
                    } else {
                        alert('Esta cita ya fue completada y no permite reprogramación.');
                    }
                    return;
                }

                // Ajustar títulos dinámicamente si es reagendamiento (cancelada o vencida)
                if (estado === 'cancelada' || estado === 'vencida') {
                    if (modalTitle) modalTitle.textContent = 'Reagendar Cita';
                    if (submitBtnSpan) submitBtnSpan.textContent = 'Reagendar Cita';
                    if (infoBanner && infoText) {
                        infoText.textContent = `La cita se encuentra ${c.estado_label || estado}. Al seleccionar un nuevo horario válido quedará confirmada nuevamente.`;
                        infoBanner.style.display = 'flex';
                    }
                } else {
                    if (modalTitle) modalTitle.textContent = 'Modificar Cita';
                    if (submitBtnSpan) submitBtnSpan.textContent = 'Guardar Cambios';
                }

                // Poblar datos del cliente
                document.getElementById('editCitaFolio').textContent = c.folio || ('CITA-' + String(c.id).padStart(5, '0'));
                const badge = document.getElementById('editCitaStatusBadge');
                if (badge) {
                    badge.className = 'status-badge ' + (c.estado_visual || estado || 'confirmada');
                    badge.textContent = c.estado_label || (estado.toUpperCase());
                }

                document.getElementById('editCitaClienteNombre').textContent = c.cliente?.nombre_completo || 'Cliente #' + c.cliente?.id;
                document.getElementById('editCitaClienteContacto').textContent = c.cliente?.telefono || c.cliente?.email || 'Sin contacto directo';

                // Poblar formulario
                const selServicio = document.getElementById('editSelectServicio');
                if (selServicio && c.servicio?.id) {
                    selServicio.value = c.servicio.id;
                    window.filtrarEspecialistasPorServicio(c.servicio.id, c.trabajador?.id);
                } else if (selServicio) {
                    selServicio.value = '';
                    window.filtrarEspecialistasPorServicio('', null);
                }

                const selTrabajador = document.getElementById('editSelectTrabajador');
                if (selTrabajador && c.trabajador?.id) {
                    if (selTrabajador.querySelector(`option[value="${c.trabajador.id}"]`)) {
                        selTrabajador.value = String(c.trabajador.id);
                    }
                }

                const inputFecha = document.getElementById('editInputFecha');
                const todayStr = new Date().toISOString().split('T')[0];
                if (inputFecha) {
                    inputFecha.min = todayStr;
                    if (c.fecha) {
                        // Si la cita anterior es del pasado (ej. cancelada o vencida),
                        // sugerir por defecto la fecha de hoy para evitar que quede en estado vencida
                        if (c.fecha < todayStr && (estado === 'cancelada' || estado === 'vencida')) {
                            inputFecha.value = todayStr;
                        } else {
                            inputFecha.value = c.fecha;
                        }
                    } else {
                        inputFecha.value = todayStr;
                    }
                }

                const inputHora = document.getElementById('editInputHora');
                if (inputHora && c.hora_inicio) {
                    inputHora.value = c.hora_inicio.substring(0, 5);
                }

                document.getElementById('editInputNotas').value = c.notas || '';
                document.getElementById('editInputObservaciones').value = c.observaciones || '';

                // Actualizar estimación de duración sin alterar la selección del especialista
                const duracionHint = document.getElementById('editDuracionCalculada');
                const duracion = c.servicio?.duracion_minutos || 30;
                if (duracionHint) {
                    duracionHint.textContent = `Duración estimada: ${duracion} min`;
                }
            })
            .catch(err => {
                console.error('Error al cargar datos en edición:', err);
                if (errorBanner) {
                    errorText.textContent = 'Error al cargar datos de la cita: ' + err.message;
                    errorBanner.style.display = 'flex';
                }
            })
            .finally(() => {
                if (loader) loader.style.display = 'none';
            });
        };

        // Guardar edición
        window.guardarEdicionCita = function (e) {
            e.preventDefault();

            const citaId = document.getElementById('editCitaId').value;
            const updateUrl = document.getElementById('editCitaUpdateUrl').value;
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            if (errorBanner) errorBanner.style.display = 'none';

            // Validar que la fecha no sea anterior a hoy
            const inputFecha = document.getElementById('editInputFecha');
            const todayStr = new Date().toISOString().split('T')[0];
            if (inputFecha && inputFecha.value && inputFecha.value < todayStr) {
                if (errorBanner && errorText) {
                    errorText.textContent = 'No puedes seleccionar una fecha anterior al día de hoy para la cita.';
                    errorBanner.style.display = 'flex';
                }
                if (window.Swal) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Fecha no válida',
                        text: 'No es posible agendar o reagendar una cita con una fecha anterior al día de hoy.',
                        confirmButtonColor: '#1e40af'
                    });
                }
                return;
            }

            // Validar que la fecha no corresponda a un día no laborable (ej. domingo)
            if (inputFecha && inputFecha.value) {
                const fParts = inputFecha.value.split('-');
                if (fParts.length === 3) {
                    const dObj = new Date(parseInt(fParts[0], 10), parseInt(fParts[1], 10) - 1, parseInt(fParts[2], 10));
                    if (dObj.getDay() === 0) {
                        const msgCerrado = 'Los domingos son días no laborables de la sucursal (Cerrado, no se labora). Por favor selecciona otra fecha.';
                        if (errorBanner && errorText) {
                            errorText.textContent = msgCerrado;
                            errorBanner.style.display = 'flex';
                        }
                        if (window.Swal) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Día no laborable',
                                text: msgCerrado,
                                confirmButtonColor: '#1e40af'
                            });
                        }
                        return;
                    }
                }
            }

            const payload = {
                service_id: document.getElementById('editSelectServicio').value,
                trabajador_id: document.getElementById('editSelectTrabajador').value,
                fecha: document.getElementById('editInputFecha').value,
                hora_inicio: document.getElementById('editInputHora').value,
                notas: document.getElementById('editInputNotas').value,
                observaciones: document.getElementById('editInputObservaciones').value,
            };

            // Validar que se haya seleccionado un servicio y un especialista asignado
            if (!payload.service_id) {
                if (errorBanner && errorText) {
                    errorText.textContent = 'Debes seleccionar un servicio para la cita.';
                    errorBanner.style.display = 'flex';
                }
                return;
            }

            if (!payload.trabajador_id) {
                const selTrab = document.getElementById('editSelectTrabajador');
                const msg = (selTrab && selTrab.disabled)
                    ? 'Aviso: Actualmente no hay especialistas asignados a este servicio en el estudio.'
                    : 'Por favor selecciona al especialista que atenderá el servicio.';

                if (errorBanner && errorText) {
                    errorText.textContent = msg;
                    errorBanner.style.display = 'flex';
                }
                if (window.Swal) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Especialista requerido',
                        text: msg,
                        confirmButtonColor: '#1e40af'
                    });
                }
                return;
            }

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `<span>Guardando...</span>`;
            }

            fetch(updateUrl, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json().then(data => ({ status: res.status, ok: res.ok, data })))
            .then(({ status, ok, data }) => {
                if (!ok || !data.success) {
                    const mensaje = data.mensaje || data.error || 'No se pudo guardar la modificación.';
                    if (errorBanner) {
                        errorText.textContent = mensaje;
                        errorBanner.style.display = 'flex';
                    }
                    if (window.Swal) {
                        Swal.fire({
                            icon: 'error',
                            title: 'No disponible',
                            text: mensaje,
                            confirmButtonColor: '#1e40af'
                        });
                    }
                    return;
                }

                // Éxito
                cerrarModal();

                if (window.Swal) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Cita Actualizada!',
                        text: data.mensaje || 'La cita fue modificada exitosamente.',
                        confirmButtonColor: '#1e40af',
                        timer: 2000
                    }).then(() => {
                        window.recargarPreservandoVista();
                    });
                } else {
                    alert(data.mensaje || 'Cita modificada exitosamente.');
                    window.recargarPreservandoVista();
                }
            })
            .catch(err => {
                console.error('Error al actualizar cita:', err);
                if (errorBanner) {
                    errorText.textContent = 'Ocurrió un error en la conexión con el servidor.';
                    errorBanner.style.display = 'flex';
                }
            })
            .finally(() => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = `
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="16" height="16">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span>Guardar Cambios</span>
                    `;
                }
            });
        };

        // Función global para Cancelación de Cita con Confirmación
        window.cancelarCitaPrompt = function (citaId, cancelUrl, folio) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const folioText = folio || ('CITA-' + String(citaId).padStart(5, '0'));
            const isAdmin = window.location.pathname.startsWith('/admin');
            const finalCancelUrl = cancelUrl || (isAdmin ? `/admin/citas/${citaId}/cancelar` : `/recepcion/citas/${citaId}/cancelar`);

            if (window.Swal) {
                Swal.fire({
                    title: `¿Cancelar cita ${folioText}?`,
                    text: 'La cita cambiará a estado Cancelada y el horario quedará inmediatamente liberado para otros clientes.',
                    icon: 'warning',
                    input: 'text',
                    inputLabel: 'Motivo de la cancelación (opcional):',
                    inputPlaceholder: 'Ej. Solicitud del cliente, emergencia, etc.',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Sí, cancelar cita',
                    cancelButtonText: 'Conservar cita',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        ejecutarCancelacion(finalCancelUrl, csrfToken, result.value);
                    }
                });
            } else {
                if (confirm(`¿Estás seguro de cancelar la cita ${folioText}?`)) {
                    const motivo = prompt('Motivo de cancelación (opcional):');
                    ejecutarCancelacion(finalCancelUrl, csrfToken, motivo);
                }
            }
        };

        function ejecutarCancelacion(cancelUrl, csrfToken, motivo) {
            fetch(cancelUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ motivo: motivo || null })
            })
            .then(res => res.json().then(data => ({ status: res.status, ok: res.ok, data })))
            .then(({ ok, data }) => {
                if (!ok || !data.success) {
                    const msg = data.mensaje || data.error || 'No se pudo cancelar la cita.';
                    if (window.Swal) {
                        Swal.fire({ icon: 'error', title: 'Error', text: msg, confirmButtonColor: '#1e40af' });
                    } else {
                        alert(msg);
                    }
                    return;
                }

                if (window.Swal) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Cita Cancelada',
                        text: data.mensaje || 'La cita fue cancelada exitosamente.',
                        confirmButtonColor: '#1e40af',
                        timer: 2000
                    }).then(() => {
                        window.recargarPreservandoVista();
                    });
                } else {
                    alert(data.mensaje || 'Cita cancelada exitosamente.');
                    window.recargarPreservandoVista();
                }
            })
            .catch(err => {
                console.error('Error al cancelar:', err);
                alert('Ocurrió un error al procesar la cancelación.');
            });
        }

        // Función global para Confirmación Directa de Cita
        window.confirmarCitaPrompt = function (citaId, confirmUrl, folio) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const folioText = folio || ('CITA-' + String(citaId).padStart(5, '0'));
            const isAdmin = window.location.pathname.startsWith('/admin');
            const finalConfirmUrl = confirmUrl || (isAdmin ? `/admin/citas/${citaId}/confirmar` : `/recepcion/citas/${citaId}/confirmar`);

            if (window.Swal) {
                Swal.fire({
                    title: `¿Confirmar cita ${folioText}?`,
                    text: 'La cita quedará confirmada formalmente en la agenda del especialista.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Sí, confirmar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        ejecutarConfirmacion(finalConfirmUrl, csrfToken);
                    }
                });
            } else {
                if (confirm(`¿Confirmar cita ${folioText}?`)) {
                    ejecutarConfirmacion(finalConfirmUrl, csrfToken);
                }
            }
        };

        function ejecutarConfirmacion(confirmUrl, csrfToken) {
            fetch(confirmUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json().then(data => ({ status: res.status, ok: res.ok, data })))
            .then(({ ok, data }) => {
                if (!ok || !data.success) {
                    const msg = data.mensaje || data.error || 'No se pudo confirmar la cita.';
                    if (window.Swal) {
                        Swal.fire({ icon: 'error', title: 'Error', text: msg, confirmButtonColor: '#1e40af' });
                    } else {
                        alert(msg);
                    }
                    return;
                }

                if (window.Swal) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Cita Confirmada!',
                        text: data.mensaje || 'La cita fue confirmada exitosamente.',
                        confirmButtonColor: '#10b981',
                        timer: 2000
                    }).then(() => {
                        window.recargarPreservandoVista();
                    });
                } else {
                    alert(data.mensaje || 'Cita confirmada exitosamente.');
                    window.recargarPreservandoVista();
                }
            })
            .catch(err => {
                console.error('Error al confirmar:', err);
                alert('Ocurrió un error al procesar la confirmación.');
            });
        }
    })();
</script>
@endonce
