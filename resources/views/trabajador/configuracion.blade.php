@extends('trabajador.layout')

@section('title', 'Beauty & Barber Studio - Mi Perfil')

@push('styles')
<style>
    /* ===================================================
       ESTILOS DE CONFIGURACIÓN DE CUENTA DEL TRABAJADOR
       =================================================== */
    .config-wrapper {
        display: flex;
        flex-direction: column;
        gap: 28px;
    }

    /* TARJETA DE PERFIL SUPERIOR */
    .profile-hero-card {
        background: linear-gradient(135deg, rgba(10, 17, 36, 0.95) 0%, rgba(12, 20, 43, 0.98) 100%);
        border: 1px solid var(--border-subtle);
        border-radius: 18px;
        padding: 32px 40px;
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 24px;
    }

    .profile-hero-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, var(--barber-blue) 0%, var(--barber-red) 100%);
    }

    .profile-hero-left {
        display: flex;
        align-items: center;
        gap: 24px;
    }

    .big-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--barber-blue), #1d4ed8);
        color: var(--text-white);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        font-weight: 800;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.35);
        border: 2px solid rgba(255, 255, 255, 0.1);
        flex-shrink: 0;
    }

    .profile-hero-info {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .profile-name {
        font-size: 24px;
        font-weight: 800;
        color: var(--text-white);
        margin: 0;
    }

    .profile-badges-row {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .role-badge {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        padding: 3px 10px;
        border-radius: 20px;
        background: var(--barber-blue-pale);
        color: var(--barber-blue-light);
        border: 1px solid var(--barber-blue-border);
    }

    .status-active-badge {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        padding: 3px 10px;
        border-radius: 20px;
        background: var(--accent-success-pale);
        color: var(--accent-success);
        border: 1px solid rgba(16, 185, 129, 0.3);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .status-active-badge::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--accent-success);
    }

    .profile-meta-text {
        font-size: 13px;
        color: var(--text-muted);
    }

    /* GRID DE SECCIONES DE CONFIGURACIÓN */
    .config-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
    }

    @media (max-width: 1024px) {
        .config-grid {
            grid-template-columns: 1fr;
        }
    }

    .config-card {
        background: var(--bg-card);
        border: 1px solid var(--border-subtle);
        border-radius: 16px;
        padding: 28px;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .config-card-header {
        display: flex;
        align-items: center;
        gap: 12px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--border-subtle);
    }

    .config-card-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .config-card-icon.blue {
        background: var(--barber-blue-pale);
        color: var(--barber-blue-light);
    }

    .config-card-icon.red {
        background: var(--barber-red-light);
        color: var(--barber-red);
    }

    .config-card-icon.amber {
        background: var(--accent-warning-pale);
        color: var(--accent-warning);
    }

    .config-card-icon svg {
        width: 20px;
        height: 20px;
    }

    .config-card-title {
        font-size: 17px;
        font-weight: 700;
        color: var(--text-white);
        margin: 0;
    }

    .config-card-subtitle {
        font-size: 13px;
        color: var(--text-muted);
    }

    /* FORMULARIOS ESTILIZADOS */
    .form-group-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    @media (max-width: 640px) {
        .form-group-grid {
            grid-template-columns: 1fr;
        }
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-label {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
    }

    .form-input {
        background: var(--bg-canvas);
        border: 1px solid var(--border-subtle);
        color: var(--text-white);
        padding: 10px 14px;
        border-radius: 8px;
        font-family: inherit;
        font-size: 14px;
        outline: none;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .form-input:focus {
        border-color: var(--barber-blue);
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2);
    }

    .form-input[readonly],
    .form-input:disabled {
        background: rgba(255, 255, 255, 0.02);
        color: #cbd5e1;
        cursor: not-allowed;
    }

    /* CHIPS DE SERVICIOS */
    .services-tag-cloud {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .service-tag {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid var(--border-subtle);
        border-radius: 8px;
        padding: 8px 14px;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-main);
    }

    .service-tag svg {
        width: 14px;
        height: 14px;
        color: var(--barber-blue-light);
    }

    /* TABLA DE HORARIO ASIGNADO */
    .schedule-table {
        width: 100%;
        border-collapse: collapse;
    }

    .schedule-table th,
    .schedule-table td {
        padding: 10px 14px;
        text-align: left;
        font-size: 13px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
    }

    .schedule-table th {
        color: var(--text-muted);
        font-weight: 700;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.05em;
    }

    .schedule-table td {
        color: var(--text-white);
    }

    .badge-open {
        color: #10b981;
        font-weight: 700;
        background: rgba(16, 185, 129, 0.1);
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 11px;
    }

    .badge-closed {
        color: #dc2626;
        font-weight: 700;
        background: rgba(239, 68, 68, 0.1);
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 11px;
    }

    /* BOTONES DE ACCIÓN */
    .btn-submit-premium {
        background: #1e40af;
        color: #ffffff;
        border: 1px solid rgba(147, 197, 253, 0.28);
        padding: 11px 22px;
        border-radius: 8px;
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.2s ease;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.35);
        width: fit-content;
    }

    .btn-submit-premium:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
        border-color: rgba(147, 197, 253, 0.45);
        background: #1d4ed8;
        color: #ffffff;
    }
</style>
@endpush

@section('content')
<div class="config-wrapper">

    <!-- HERO DE PERFIL -->
    <section class="profile-hero-card" aria-label="Perfil del trabajador">
        <div class="profile-hero-left">
            @php
                $initials = strtoupper(substr($user->name ?? 'T', 0, 1) . substr($user->apellidos ?? 'E', 0, 1));
            @endphp
            <div class="big-avatar" aria-hidden="true">
                {{ $initials }}
            </div>
            <div class="profile-hero-info">
                <h1 class="profile-name">{{ $user->name }} {{ $user->apellidos }}</h1>
                <div class="profile-badges-row">
                    <span class="role-badge">Especialista &bull; Rol Profesional</span>
                    <span class="status-active-badge">Activo en Turno</span>
                </div>
                <span class="profile-meta-text">
                    {{ $user->email }} &bull; Miembro del equipo de Beauty & Barber Studio
                </span>
            </div>
        </div>
    </section>

    <!-- GRID DE SECCIONES DE CONFIGURACIÓN -->
    <div class="config-grid">
        
        <!-- 1. INFORMACIÓN PERSONAL Y DE CONTACTO -->
        <div class="config-card">
            <div class="config-card-header">
                <div class="config-card-icon blue">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </div>
                <div>
                    <h2 class="config-card-title">Datos Personales</h2>
                    <span class="config-card-subtitle">Información registrada en tu ficha técnica</span>
                </div>
            </div>

            <form id="formDatosPersonales" action="{{ route('trabajador.configuracion.datos') }}" method="POST" style="display: flex; flex-direction: column; gap: 16px;">
                @csrf
                @method('PUT')

                <div class="form-group-grid">
                    <div class="form-group">
                        <label class="form-label" for="inputName">Nombre(s) <span style="color: var(--barber-red);">*</span></label>
                        <input type="text" id="inputName" name="name" class="form-input" value="{{ old('name', $user->name) }}" placeholder="Nombre" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="inputApellidos">Apellidos <span style="color: var(--barber-red);">*</span></label>
                        <input type="text" id="inputApellidos" name="apellidos" class="form-input" value="{{ old('apellidos', $user->apellidos) }}" placeholder="Apellidos" required>
                    </div>
                </div>

                <div class="form-group-grid">
                    <div class="form-group">
                        <label class="form-label" for="inputEmail">Correo Electrónico (No modificable)</label>
                        <input type="email" id="inputEmail" class="form-input" value="{{ $user->email }}" readonly style="opacity: 0.7; cursor: not-allowed;">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="inputTelefono">Teléfono de Contacto</label>
                        <input type="tel" id="inputTelefono" name="telefono" class="form-input" value="{{ old('telefono', $user->telefono) }}" placeholder="Número telefónico de 10 dígitos">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="inputDireccion">Dirección de Trabajo / Residencia</label>
                    <input type="text" id="inputDireccion" name="direccion" class="form-input" value="{{ old('direccion', $trabajador->direccion ?? '') }}" placeholder="Dirección completa">
                </div>

                <div style="margin-top: 8px;">
                    <button type="submit" id="btnGuardarDatos" class="btn-submit-premium">
                        <span>Guardar Datos Personales</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- 2. SERVICIOS QUE REALIZAS / ESPECIALIDAD -->
        <div class="config-card">
            <div class="config-card-header">
                <div class="config-card-icon red">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="6" cy="6" r="3"></circle>
                        <circle cx="6" cy="18" r="3"></circle>
                        <line x1="20" y1="4" x2="8.12" y2="15.88"></line>
                        <line x1="14.47" y1="14.48" x2="20" y2="20"></line>
                        <line x1="8.12" y1="8.12" x2="12" y2="12"></line>
                    </svg>
                </div>
                <div>
                    <h2 class="config-card-title">Mis Servicios & Especialidades</h2>
                    <span class="config-card-subtitle">Servicios habilitados en tu agenda para los clientes</span>
                </div>
            </div>

            <p style="font-size: 13px; color: var(--text-muted); line-height: 1.5;">
                Estos son los servicios que los clientes pueden agendar directamente contigo desde la plataforma web.
            </p>

            <div class="services-tag-cloud">
                @forelse($serviciosAsignados as $servicio)
                    <div class="service-tag">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span>{{ $servicio->name }}</span>
                    </div>
                @empty
                    <div style="font-size: 13px; color: var(--text-muted); font-style: italic; padding: 12px 4px;">
                        No tienes servicios asignados actualmente en tu perfil. Comunícate con la administración para habilitar tus especialidades.
                    </div>
                @endforelse
            </div>

            <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid var(--border-subtle); padding: 14px 18px; border-radius: 10px; margin-top: auto;">
                <span style="font-size: 12px; font-weight: 700; color: var(--barber-blue-light); display: block; margin-bottom: 2px;">
                    ¿Requieres agregar o remover un servicio?
                </span>
                <span style="font-size: 12px; color: var(--text-muted);">
                    Comunícate con la administración para actualizar tu catálogo de especialidades asignadas.
                </span>
            </div>
        </div>

        <!-- 3. HORARIO DE TRABAJO ASIGNADO -->
        <div class="config-card">
            <div class="config-card-header">
                <div class="config-card-icon amber">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <div>
                    <h2 class="config-card-title">Horario Laboral Asignado</h2>
                    <span class="config-card-subtitle">Tu disponibilidad para la asignación de turnos y citas</span>
                </div>
            </div>

            <table class="schedule-table">
                <thead>
                    <tr>
                        <th>Día</th>
                        <th>Turno de Atención</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Lunes</td>
                        <td>09:00 AM - 07:00 PM</td>
                        <td><span class="badge-open">Laboral</span></td>
                    </tr>
                    <tr>
                        <td>Martes</td>
                        <td>09:00 AM - 07:00 PM</td>
                        <td><span class="badge-open">Laboral</span></td>
                    </tr>
                    <tr>
                        <td>Miércoles</td>
                        <td>09:00 AM - 07:00 PM</td>
                        <td><span class="badge-open">Laboral</span></td>
                    </tr>
                    <tr>
                        <td>Jueves</td>
                        <td>09:00 AM - 07:00 PM</td>
                        <td><span class="badge-open">Laboral</span></td>
                    </tr>
                    <tr>
                        <td>Viernes</td>
                        <td>09:00 AM - 08:00 PM</td>
                        <td><span class="badge-open">Laboral</span></td>
                    </tr>
                    <tr>
                        <td>Sábado</td>
                        <td>09:00 AM - 08:00 PM</td>
                        <td><span class="badge-open">Laboral</span></td>
                    </tr>
                    <tr>
                        <td>Domingo</td>
                        <td>Descanso semanal</td>
                        <td><span class="badge-closed">Descanso</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- 4. SEGURIDAD Y CONTRASEÑA -->
        <div class="config-card">
            <div class="config-card-header">
                <div class="config-card-icon blue">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="config-card-title">Seguridad de la Cuenta</h2>
                    <span class="config-card-subtitle">Cambio de contraseña y protección de acceso</span>
                </div>
            </div>

            <form id="formPassword" action="{{ route('trabajador.configuracion.password') }}" method="POST" style="display: flex; flex-direction: column; gap: 16px;">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label" for="currentPass">Contraseña Actual <span style="color: var(--barber-red);">*</span></label>
                    <input type="password" id="currentPass" name="current_password" class="form-input" placeholder="••••••••••••" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="newPass">Nueva Contraseña <span style="color: var(--barber-red);">*</span></label>
                    <input type="password" id="newPass" name="password" class="form-input" placeholder="Mínimo 8 caracteres (letras y números)" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="confirmPass">Confirmar Nueva Contraseña <span style="color: var(--barber-red);">*</span></label>
                    <input type="password" id="confirmPass" name="password_confirmation" class="form-input" placeholder="Repite la nueva contraseña" required>
                </div>

                <div style="margin-top: 8px;">
                    <button type="submit" id="btnGuardarPassword" class="btn-submit-premium">
                        <span>Actualizar Contraseña</span>
                    </button>
                </div>
            </form>
        </div>

    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    // 1. Envío Asíncrono de Datos Personales
    const formDatos = document.getElementById('formDatosPersonales');
    const btnGuardarDatos = document.getElementById('btnGuardarDatos');

    if (formDatos) {
        formDatos.addEventListener('submit', async function (e) {
            e.preventDefault();

            const name = document.getElementById('inputName').value.trim();
            const apellidos = document.getElementById('inputApellidos').value.trim();
            const telefono = document.getElementById('inputTelefono').value.trim();
            const direccion = document.getElementById('inputDireccion').value.trim();

            if (!name || !apellidos) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Campos requeridos',
                    text: 'El nombre y los apellidos son obligatorios.',
                    background: '#0a1124',
                    color: '#fff',
                    confirmButtonColor: '#1e40af'
                });
                return;
            }

            const originalBtnHtml = btnGuardarDatos.innerHTML;
            btnGuardarDatos.disabled = true;
            btnGuardarDatos.innerHTML = '<span>Guardando...</span>';

            try {
                const response = await fetch("{{ route('trabajador.configuracion.datos') }}", {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        name: name,
                        apellidos: apellidos,
                        telefono: telefono,
                        direccion: direccion
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: data.message || 'Datos actualizados con éxito',
                        showConfirmButton: false,
                        timer: 3000,
                        background: '#10b981',
                        color: '#fff'
                    });

                    // Actualizar el nombre en el encabezado del perfil
                    const heroName = document.querySelector('.profile-name');
                    if (heroName && data.user) {
                        heroName.textContent = data.user.name + ' ' + (data.user.apellidos || '');
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de validación',
                        text: data.message || 'No se pudieron guardar los datos.',
                        background: '#0a1124',
                        color: '#fff',
                        confirmButtonColor: '#dc2626'
                    });
                }
            } catch (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'Ocurrió un fallo al comunicarse con el servidor.',
                    background: '#0a1124',
                    color: '#fff',
                    confirmButtonColor: '#dc2626'
                });
            } finally {
                btnGuardarDatos.disabled = false;
                btnGuardarDatos.innerHTML = originalBtnHtml;
            }
        });
    }

    // 2. Envío Asíncrono de Cambio de Contraseña
    const formPassword = document.getElementById('formPassword');
    const btnGuardarPassword = document.getElementById('btnGuardarPassword');

    if (formPassword) {
        formPassword.addEventListener('submit', async function (e) {
            e.preventDefault();

            const currentPassword = document.getElementById('currentPass').value;
            const newPassword = document.getElementById('newPass').value;
            const confirmPassword = document.getElementById('confirmPass').value;

            if (!currentPassword || !newPassword || !confirmPassword) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Formulario incompleto',
                    text: 'Debes completar todos los campos de contraseña.',
                    background: '#0a1124',
                    color: '#fff',
                    confirmButtonColor: '#1e40af'
                });
                return;
            }

            if (newPassword !== confirmPassword) {
                Swal.fire({
                    icon: 'error',
                    title: 'Contraseñas no coinciden',
                    text: 'La confirmación de la contraseña no coincide con la nueva contraseña.',
                    background: '#0a1124',
                    color: '#fff',
                    confirmButtonColor: '#dc2626'
                });
                return;
            }

            const originalBtnHtml = btnGuardarPassword.innerHTML;
            btnGuardarPassword.disabled = true;
            btnGuardarPassword.innerHTML = '<span>Verificando...</span>';

            try {
                const response = await fetch("{{ route('trabajador.configuracion.password') }}", {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        current_password: currentPassword,
                        password: newPassword,
                        password_confirmation: confirmPassword
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: data.message || 'Contraseña actualizada con éxito',
                        showConfirmButton: false,
                        timer: 3500,
                        background: '#10b981',
                        color: '#fff'
                    });

                    formPassword.reset();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'No se pudo actualizar',
                        text: data.message || 'La contraseña actual no es correcta o la nueva clave no cumple las reglas.',
                        background: '#0a1124',
                        color: '#fff',
                        confirmButtonColor: '#dc2626'
                    });
                }
            } catch (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'Ocurrió un problema de comunicación con el servidor.',
                    background: '#0a1124',
                    color: '#fff',
                    confirmButtonColor: '#dc2626'
                });
            } finally {
                btnGuardarPassword.disabled = false;
                btnGuardarPassword.innerHTML = originalBtnHtml;
            }
        });
    }
});
</script>
@endpush
@endsection
