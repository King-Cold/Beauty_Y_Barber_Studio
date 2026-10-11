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

    .btn-agendar-cta {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
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

    .btn-agendar-cta:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 85, 255, 0.4);
        border-color: #60a5fa;
    }

    .panel-card {
        background: var(--bg-card);
        border: 1px solid var(--border-subtle);
        border-radius: 16px;
        padding: 24px;
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
        padding: 14px 16px;
        font-size: 12px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1px solid var(--border-subtle);
        background: rgba(255, 255, 255, 0.02);
    }

    .citas-table td {
        padding: 16px;
        font-size: 13.5px;
        color: var(--text-main);
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        vertical-align: middle;
    }

    .citas-table tr:hover td {
        background: rgba(255, 255, 255, 0.02);
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
    }

    .status-badge.confirmada {
        background: rgba(16, 185, 129, 0.15);
        color: #34d399;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    .status-badge.cancelada {
        background: rgba(239, 68, 68, 0.15);
        color: #f87171;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }

    .status-badge.completada {
        background: rgba(6, 182, 212, 0.15);
        color: #22d3ee;
        border: 1px solid rgba(6, 182, 212, 0.3);
    }
</style>
@endpush

@section('content')
    <div class="section-header">
        <div>
            <h1 class="section-title">Gestión de Citas</h1>
            <p class="section-subtitle">Administración y registro de citas de clientes en Beauty & Barber Studio.</p>
        </div>
        <a href="{{ route('recepcion.agendamiento') }}" class="btn-agendar-cta">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="18" height="18">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Agendar nueva cita
        </a>
    </div>

    <div class="panel-card">
        @if($citas->isEmpty())
            <div style="text-align: center; padding: 48px 24px;">
                <p style="color: var(--text-muted); font-size: 15px; margin-bottom: 20px;">
                    No hay citas registradas en el sistema actualmente.
                </p>
                <a href="{{ route('recepcion.agendamiento') }}" class="btn-agendar-cta">
                    Agendar primera cita
                </a>
            </div>
        @else
            <div class="citas-table-wrapper">
                <table class="citas-table">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Servicio</th>
                            <th>Especialista</th>
                            <th>Fecha</th>
                            <th>Horario</th>
                            <th>Precio</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($citas as $cita)
                            @php
                                $estado = strtolower($cita->estado);
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $cita->cliente->name ?? 'Cliente #' . $cita->user_id }} {{ $cita->cliente->apellidos ?? '' }}</strong>
                                </td>
                                <td>{{ $cita->service->name ?? 'Servicio' }}</td>
                                <td>{{ $cita->trabajador->user->name ?? 'Especialista' }}</td>
                                <td>{{ $cita->fecha ? \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') : '—' }}</td>
                                <td><strong>{{ $cita->hora_inicio }} - {{ $cita->hora_fin }}</strong></td>
                                <td style="color: #38bdf8; font-weight: 700;">${{ number_format($cita->precio, 2) }}</td>
                                <td>
                                    <span class="status-badge {{ $estado }}">
                                        {{ ucfirst($cita->estado) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
