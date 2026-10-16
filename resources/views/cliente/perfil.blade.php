@extends('cliente.layout')

@section('title', 'Beauty & Barber Studio - Mi Perfil')

@push('styles')
<style>
    /* ===================================================
       ESTILOS DEL PERFIL DEL CLIENTE
       =================================================== */
    .profile-wrapper {
        display: flex;
        flex-direction: column;
        gap: 28px;
    }

    /* TARJETA HERO DEL CLIENTE */
    .client-hero-card {
        background: linear-gradient(135deg, rgba(10, 17, 36, 0.95) 0%, rgba(12, 20, 43, 0.98) 100%);
        border: 1px solid var(--border-subtle);
        border-radius: 18px;
        padding: 32px 36px;
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 24px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
    }

    .client-hero-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, var(--barber-blue) 0%, var(--barber-red) 100%);
    }

    .client-hero-left {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .client-avatar-big {
        width: 76px;
        height: 76px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--barber-blue), #1d4ed8);
        color: var(--text-white);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        font-weight: 800;
        box-shadow: 0 0 20px rgba(0, 85, 255, 0.4);
        border: 2px solid rgba(255, 255, 255, 0.12);
        flex-shrink: 0;
    }

    .client-hero-info {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .client-hero-name {
        font-size: 24px;
        font-weight: 800;
        color: var(--text-white);
        margin: 0;
        line-height: 1.2;
    }

    .client-badges-row {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .badge-client-role {
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

    .client-meta-text {
        font-size: 13px;
        color: var(--text-muted);
    }

    /* CONTADORES RESUMEN HERO */
    .client-hero-stats {
        display: flex;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }

    .hero-stat-box {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid var(--border-subtle);
        border-radius: 12px;
        padding: 12px 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        min-width: 100px;
    }

    .hero-stat-number {
        font-size: 22px;
        font-weight: 800;
        color: var(--text-white);
    }

    .hero-stat-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-muted);
        letter-spacing: 0.05em;
    }

    /* GRID DE CONFIGURACIÓN */
    .profile-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(360px, 1fr));
        gap: 24px;
    }

    .profile-card {
        background: var(--bg-card);
        border: 1px solid var(--border-subtle);
        border-radius: 16px;
        padding: 28px;
        display: flex;
        flex-direction: column;
        gap: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    }

    .profile-card-header {
        display: flex;
        align-items: center;
        gap: 14px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--border-subtle);
    }

    .profile-card-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .profile-card-icon.blue {
        background: var(--barber-blue-pale);
        color: var(--barber-blue-light);
        border: 1px solid var(--barber-blue-border);
    }

    .profile-card-icon.red {
        background: var(--barber-red-light);
        color: var(--barber-red);
        border: 1px solid var(--barber-red-border);
    }

    .profile-card-icon svg {
        width: 20px;
        height: 20px;
    }

    .profile-card-title {
        font-size: 17px;
        font-weight: 700;
        color: var(--text-white);
        margin-bottom: 2px;
    }

    .profile-card-subtitle {
        font-size: 12px;
        color: var(--text-muted);
    }

    /* FORMULARIOS */
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
        gap: 6px;
    }

    .form-label {
        font-size: 12.5px;
        font-weight: 600;
        color: var(--text-muted);
    }

    .form-input {
        background: #070d1e;
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        padding: 12px 16px;
        font-size: 14px;
        color: var(--text-white);
        font-family: inherit;
        transition: all 0.2s var(--ease-fluid);
        outline: none;
        width: 100%;
        box-sizing: border-box;
    }

    .form-input:focus {
        border-color: var(--barber-blue);
        box-shadow: 0 0 0 3px rgba(0, 85, 255, 0.2);
    }

    .btn-submit-premium {
        background: linear-gradient(135deg, var(--barber-blue) 0%, #1d4ed8 100%);
        color: var(--text-white);
        border: 1px solid rgba(0, 85, 255, 0.4);
        padding: 12px 24px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.25s var(--ease-fluid);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 4px 14px rgba(0, 85, 255, 0.25);
    }

    .btn-submit-premium:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 85, 255, 0.4);
        border-color: #60a5fa;
    }

    .btn-submit-premium:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    /* TARJETA PRÓXIMA CITA */
    .upcoming-appointment-box {
        background: linear-gradient(135deg, rgba(0, 85, 255, 0.08) 0%, rgba(30, 64, 175, 0.08) 100%);
        border: 1px solid rgba(0, 85, 255, 0.25);
        border-radius: 12px;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .upcoming-info h4 {
        font-size: 14px;
        font-weight: 700;
        color: var(--text-white);
        margin-bottom: 2px;
    }

    .upcoming-info p {
        font-size: 12px;
        color: var(--text-muted);
        margin: 0;
    }

    .btn-view-citas {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        border-radius: 8px;
        background: rgba(0, 85, 255, 0.15);
        color: #60a5fa;
        border: 1px solid rgba(0, 85, 255, 0.3);
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-view-citas:hover {
        background: var(--barber-blue);
        color: #ffffff;
    }
</style>
@endpush

@section('content')
<div class="profile-wrapper">

    <!-- HERO BANNER DEL CLIENTE -->
    <section class="client-hero-card" aria-labelledby="clientHeroName">
        @php
            $initials = strtoupper(substr($user->name, 0, 1) . substr($user->apellidos ?? '', 0, 1));
        @endphp
        <div class="client-hero-left">
            <div class="client-avatar-big" aria-hidden="true">
                {{ $initials ?: 'CL' }}
            </div>
            <div class="client-hero-info">
                <h1 id="clientHeroName" class="client-hero-name">{{ $user->name }} {{ $user->apellidos }}</h1>
                <div class="client-badges-row">
                    <span class="badge-client-role">Cliente VIP &bull; Beauty & Barber Studio</span>
                </div>
                <span class="client-meta-text">
                    {{ $user->email }} &bull; {{ $user->telefono ?? 'Teléfono no registrado' }}
                </span>
            </div>
        </div>

        <div class="client-hero-stats">
            <div class="hero-stat-box">
                <span class="hero-stat-number">{{ $totalCitas }}</span>
                <span class="hero-stat-label">Citas Totales</span>
            </div>
            <div class="hero-stat-box">
                <span class="hero-stat-number">{{ $citasCompletadas }}</span>
                <span class="hero-stat-label">Completadas</span>
            </div>
        </div>
    </section>

    @if($proximaCita)
    <!-- BANNER DE PRÓXIMA CITA ACTIVA -->
    <div class="upcoming-appointment-box">
        <div class="upcoming-info">
            <h4>Tienes una próxima cita programada</h4>
            <p>
                <strong>{{ $proximaCita->service->name ?? 'Servicio' }}</strong> el 
                <strong>{{ \Carbon\Carbon::parse($proximaCita->fecha)->translatedFormat('l d \d\e F') }}</strong> a las 
                <strong>{{ substr($proximaCita->hora_inicio, 0, 5) }} hrs</strong> con 
                <strong>{{ $proximaCita->trabajador->user->name ?? 'Especialista' }}</strong>.
            </p>
        </div>
        <a href="{{ route('cliente.historial') }}" class="btn-view-citas">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
            Ver en Historial
        </a>
    </div>
    @endif

    <!-- GRID DE FORMULARIOS: DATOS PERSONALES & SEGURIDAD -->
    <div class="profile-grid">

        <!-- 1. DATOS PERSONALES Y CONTACTO -->
        <div class="profile-card">
            <div class="profile-card-header">
                <div class="profile-card-icon blue">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </div>
                <div>
                    <h2 class="profile-card-title">Datos Personales</h2>
                    <span class="profile-card-subtitle">Mantén tus datos actualizados para tus citas</span>
                </div>
            </div>

            <form id="formDatosCliente" action="{{ route('cliente.perfil.datos') }}" method="POST" style="display: flex; flex-direction: column; gap: 16px;">
                @csrf
                @method('PUT')

                <div class="form-group-grid">
                    <div class="form-group">
                        <label class="form-label" for="clientInputName">Nombre(s) <span style="color: var(--barber-red);">*</span></label>
                        <input type="text" id="clientInputName" name="name" class="form-input" value="{{ old('name', $user->name) }}" placeholder="Nombre" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="clientInputApellidos">Apellidos <span style="color: var(--barber-red);">*</span></label>
                        <input type="text" id="clientInputApellidos" name="apellidos" class="form-input" value="{{ old('apellidos', $user->apellidos) }}" placeholder="Apellidos" required>
                    </div>
                </div>

                <div class="form-group-grid">
                    <div class="form-group">
                        <label class="form-label" for="clientInputEmail">Correo Electrónico <span style="color: var(--barber-red);">*</span></label>
                        <input type="email" id="clientInputEmail" name="email" class="form-input" value="{{ old('email', $user->email) }}" placeholder="correo@ejemplo.com" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="clientInputTelefono">Teléfono de Contacto (WhatsApp / Avisos)</label>
                        <input type="tel" id="clientInputTelefono" name="telefono" class="form-input" value="{{ old('telefono', $user->telefono) }}" placeholder="10 dígitos numéricos">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="clientInputFechaNac">Fecha de Nacimiento</label>
                    <input 
                        type="date" 
                        id="clientInputFechaNac" 
                        name="fecha_nacimiento" 
                        class="form-input" 
                        value="{{ old('fecha_nacimiento', $user->fecha_nacimiento) }}"
                        min="1920-01-01"
                        max="{{ date('Y-m-d') }}"
                    >
                </div>

                <div style="margin-top: 8px;">
                    <button type="submit" id="btnGuardarDatosCliente" class="btn-submit-premium">
                        <span>Guardar Datos Personales</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- 2. SEGURIDAD Y CAMBIO DE CONTRASEÑA -->
        <div class="profile-card">
            <div class="profile-card-header">
                <div class="profile-card-icon red">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="profile-card-title">Seguridad de la Cuenta</h2>
                    <span class="profile-card-subtitle">Cambio de contraseña y protección de acceso</span>
                </div>
            </div>

            <form id="formPasswordCliente" action="{{ route('cliente.perfil.password') }}" method="POST" style="display: flex; flex-direction: column; gap: 16px;">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label" for="clientCurrentPass">Contraseña Actual <span style="color: var(--barber-red);">*</span></label>
                    <input type="password" id="clientCurrentPass" name="current_password" class="form-input" placeholder="••••••••••••" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="clientNewPass">Nueva Contraseña <span style="color: var(--barber-red);">*</span></label>
                    <input type="password" id="clientNewPass" name="password" class="form-input" placeholder="Mínimo 8 caracteres (letras y números)" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="clientConfirmPass">Confirmar Nueva Contraseña <span style="color: var(--barber-red);">*</span></label>
                    <input type="password" id="clientConfirmPass" name="password_confirmation" class="form-input" placeholder="Repite la nueva contraseña" required>
                </div>

                <div style="margin-top: 8px;">
                    <button type="submit" id="btnGuardarPasswordCliente" class="btn-submit-premium">
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
    const formDatos = document.getElementById('formDatosCliente');
    const btnGuardarDatos = document.getElementById('btnGuardarDatosCliente');

    if (formDatos) {
        formDatos.addEventListener('submit', async function (e) {
            e.preventDefault();

            const name = document.getElementById('clientInputName').value.trim();
            const apellidos = document.getElementById('clientInputApellidos').value.trim();
            const email = document.getElementById('clientInputEmail').value.trim();
            const telefono = document.getElementById('clientInputTelefono').value.trim();
            const fechaNac = document.getElementById('clientInputFechaNac').value;

            if (!name || !apellidos || !email) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Campos requeridos',
                    text: 'El nombre, los apellidos y el correo electrónico son obligatorios.',
                    background: '#0a1124',
                    color: '#fff',
                    confirmButtonColor: '#0055ff'
                });
                return;
            }

            if (fechaNac) {
                const selectedDate = new Date(fechaNac + 'T00:00:00');
                const today = new Date();
                today.setHours(23, 59, 59, 999);
                if (selectedDate > today) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Fecha inválida',
                        text: 'La fecha de nacimiento no puede ser una fecha futura ni el día de mañana.',
                        background: '#0a1124',
                        color: '#fff',
                        confirmButtonColor: '#0055ff'
                    });
                    return;
                }
            }

            const originalBtnHtml = btnGuardarDatos.innerHTML;
            btnGuardarDatos.disabled = true;
            btnGuardarDatos.innerHTML = '<span>Guardando...</span>';

            try {
                const response = await fetch("{{ route('cliente.perfil.datos') }}", {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        name: name,
                        apellidos: apellidos,
                        email: email,
                        telefono: telefono,
                        fecha_nacimiento: fechaNac || null
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

                    // Actualizar nombre en el hero banner y header
                    const heroName = document.getElementById('clientHeroName');
                    if (heroName && data.user) {
                        heroName.textContent = data.user.name + ' ' + (data.user.apellidos || '');
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de validación',
                        text: data.message || 'No se pudieron actualizar los datos.',
                        background: '#0a1124',
                        color: '#fff',
                        confirmButtonColor: '#ef4444'
                    });
                }
            } catch (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'Ocurrió un fallo de conexión con el servidor.',
                    background: '#0a1124',
                    color: '#fff',
                    confirmButtonColor: '#ef4444'
                });
            } finally {
                btnGuardarDatos.disabled = false;
                btnGuardarDatos.innerHTML = originalBtnHtml;
            }
        });
    }

    // 2. Envío Asíncrono de Cambio de Contraseña
    const formPassword = document.getElementById('formPasswordCliente');
    const btnGuardarPassword = document.getElementById('btnGuardarPasswordCliente');

    if (formPassword) {
        formPassword.addEventListener('submit', async function (e) {
            e.preventDefault();

            const currentPassword = document.getElementById('clientCurrentPass').value;
            const newPassword = document.getElementById('clientNewPass').value;
            const confirmPassword = document.getElementById('clientConfirmPass').value;

            if (!currentPassword || !newPassword || !confirmPassword) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Formulario incompleto',
                    text: 'Debes completar todos los campos de contraseña.',
                    background: '#0a1124',
                    color: '#fff',
                    confirmButtonColor: '#0055ff'
                });
                return;
            }

            if (newPassword !== confirmPassword) {
                Swal.fire({
                    icon: 'error',
                    title: 'Contraseñas no coinciden',
                    text: 'La nueva contraseña y su confirmación no coinciden.',
                    background: '#0a1124',
                    color: '#fff',
                    confirmButtonColor: '#ef4444'
                });
                return;
            }

            const originalBtnHtml = btnGuardarPassword.innerHTML;
            btnGuardarPassword.disabled = true;
            btnGuardarPassword.innerHTML = '<span>Verificando...</span>';

            try {
                const response = await fetch("{{ route('cliente.perfil.password') }}", {
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
                        text: data.message || 'La contraseña actual no es válida o la nueva no cumple los requisitos.',
                        background: '#0a1124',
                        color: '#fff',
                        confirmButtonColor: '#ef4444'
                    });
                }
            } catch (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'Ocurrió un problema de comunicación con el servidor.',
                    background: '#0a1124',
                    color: '#fff',
                    confirmButtonColor: '#ef4444'
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
