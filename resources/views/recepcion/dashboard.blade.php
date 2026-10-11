@extends('recepcion.layout')

@section('title', 'Beauty & Barber Studio - Panel de Recepción')

@push('styles')
<style>
    .welcome-banner {
        background: linear-gradient(135deg, rgba(10, 17, 36, 0.95) 0%, rgba(12, 20, 43, 0.98) 100%);
        border: 1px solid var(--border-subtle);
        border-radius: 18px;
        padding: 36px 40px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
        margin-bottom: 32px;
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
        font-size: 30px;
        font-weight: 800;
        color: var(--text-white);
        letter-spacing: -0.02em;
        margin-bottom: 10px;
        line-height: 1.2;
    }

    .welcome-subtitle {
        font-size: 15px;
        color: var(--text-muted);
        max-width: 800px;
        line-height: 1.6;
    }

</style>
@endpush

@section('content')
    <!-- SALUDO Y BIENVENIDA -->
    <section class="welcome-banner" aria-labelledby="welcomeTitle">
        <div class="welcome-banner-badge">
            Recepción &bull; Beauty & Barber Studio
        </div>
        <h1 id="welcomeTitle" class="welcome-title">
            ¡Hola, {{ $user->name }}! Te damos la bienvenida
        </h1>
        <p class="welcome-subtitle">
            Desde este panel podrás atender las solicitudes de los clientes, registrar y consultar citas programadas, consultar disponibilidad de especialistas y mantener organizada la agenda del estudio.
        </p>
    </section>
@endsection
