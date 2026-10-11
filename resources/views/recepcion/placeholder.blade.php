@extends('recepcion.layout')

@section('title', 'Beauty & Barber Studio - ' . $title)

@push('styles')
<style>
    .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 28px;
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

    .btn-action-primary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: linear-gradient(135deg, var(--barber-blue) 0%, #1d4ed8 100%);
        color: var(--text-white);
        font-size: 14px;
        font-weight: 700;
        border-radius: 10px;
        text-decoration: none;
        transition: all 0.25s var(--ease-fluid);
        border: 1px solid rgba(0, 85, 255, 0.4);
        box-shadow: 0 4px 14px rgba(0, 85, 255, 0.25);
    }

    .btn-action-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 85, 255, 0.4);
        border-color: #60a5fa;
    }

    .placeholder-panel {
        background: var(--bg-header);
        border: 1px solid var(--border-subtle);
        border-radius: 16px;
        padding: 56px 24px;
        text-align: center;
        min-height: 380px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    }

    .placeholder-icon-wrap {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        background: var(--barber-blue-pale);
        color: var(--barber-blue-light);
        border: 1px solid var(--barber-blue-border);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
    }

    .placeholder-icon-wrap svg {
        width: 32px;
        height: 32px;
    }

    .placeholder-heading {
        font-size: 20px;
        font-weight: 700;
        color: var(--text-white);
        margin-bottom: 8px;
    }

    .placeholder-text {
        font-size: 14px;
        color: var(--text-muted);
        max-width: 520px;
        line-height: 1.6;
        margin-bottom: 24px;
    }
</style>
@endpush

@section('content')
    <div class="section-header">
        <div>
            <h1 class="section-title">{{ $title }}</h1>
            <p class="section-subtitle">{{ $description }}</p>
        </div>
        <a href="{{ route('recepcion.agendamiento') }}" class="btn-action-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="18" height="18">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Agendar nueva cita
        </a>
    </div>

    <div class="placeholder-panel">
        <div class="placeholder-icon-wrap" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
        </div>
        <h2 class="placeholder-heading">{{ $title }}</h2>
        <p class="placeholder-text">
            {{ $description }} Para programar una cita para un cliente, utiliza el módulo de agendamiento directo.
        </p>
        <a href="{{ route('recepcion.agendamiento') }}" class="btn-action-primary">
            Ir al Agendamiento de Citas
        </a>
    </div>
@endsection
