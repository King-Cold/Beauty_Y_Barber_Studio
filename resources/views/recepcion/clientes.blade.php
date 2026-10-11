@extends('recepcion.layout')

@section('title', 'Beauty & Barber Studio - Clientes')

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

    .btn-agendar-cta:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 85, 255, 0.4);
        border-color: #60a5fa;
    }

    .clients-card {
        background: var(--bg-card);
        border: 1px solid var(--border-subtle);
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    }

    .clients-table-wrapper {
        overflow-x: auto;
    }

    .clients-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .clients-table th {
        padding: 14px 16px;
        font-size: 12px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1px solid var(--border-subtle);
        background: rgba(255, 255, 255, 0.02);
    }

    .clients-table td {
        padding: 16px;
        font-size: 13.5px;
        color: var(--text-main);
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        vertical-align: middle;
    }

    .clients-table tr:hover td {
        background: rgba(255, 255, 255, 0.02);
    }

    .client-avatar {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: linear-gradient(135deg, rgba(0, 85, 255, 0.2), rgba(30, 64, 175, 0.3));
        border: 1px solid rgba(0, 85, 255, 0.3);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #93c5fd;
        font-weight: 700;
        font-size: 13px;
        margin-right: 12px;
    }

    .client-name-cell {
        display: flex;
        align-items: center;
    }

    .client-name {
        font-weight: 700;
        color: var(--text-white);
    }

    .citas-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 10px;
        border-radius: 12px;
        background: rgba(0, 85, 255, 0.12);
        color: #60a5fa;
        font-size: 12px;
        font-weight: 700;
        border: 1px solid rgba(0, 85, 255, 0.25);
    }

    .btn-agendar-row {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 8px;
        background: rgba(0, 85, 255, 0.15);
        color: #60a5fa;
        border: 1px solid rgba(0, 85, 255, 0.3);
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-agendar-row:hover {
        background: var(--barber-blue);
        color: #ffffff;
    }
</style>
@endpush

@section('content')
    <div class="section-header">
        <div>
            <h1 class="section-title">Directorio de Clientes</h1>
            <p class="section-subtitle">Consulta de clientes registrados en el estudio para agendamiento de citas.</p>
        </div>
        <a href="{{ route('recepcion.agendamiento') }}" class="btn-agendar-cta">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="18" height="18">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Agendar cita
        </a>
    </div>

    <div class="clients-card">
        @if($clientes->isEmpty())
            <div style="text-align: center; padding: 40px; color: var(--text-muted);">
                <p>No hay clientes registrados en el sistema actualmente.</p>
            </div>
        @else
            <div class="clients-table-wrapper">
                <table class="clients-table">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Correo electrónico</th>
                            <th>Teléfono</th>
                            <th>Citas registradas</th>
                            <th style="text-align: right;">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clientes as $cliente)
                            @php
                                $ini = strtoupper(substr($cliente->name, 0, 1) . substr($cliente->apellidos ?? '', 0, 1));
                            @endphp
                            <tr>
                                <td>
                                    <div class="client-name-cell">
                                        <div class="client-avatar">{{ $ini ?: 'CL' }}</div>
                                        <div>
                                            <div class="client-name">{{ $cliente->name }} {{ $cliente->apellidos }}</div>
                                            <div style="font-size: 11px; color: var(--text-muted);">ID #{{ $cliente->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $cliente->email }}</td>
                                <td>{{ $cliente->telefono ?? '—' }}</td>
                                <td>
                                    <span class="citas-badge">{{ $cliente->citas_count }} citas</span>
                                </td>
                                <td style="text-align: right;">
                                    <a href="{{ route('recepcion.agendamiento') }}?client_id={{ $cliente->id }}" class="btn-agendar-row">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                            <line x1="12" y1="5" x2="12" y2="19"></line>
                                            <line x1="5" y1="12" x2="19" y2="12"></line>
                                        </svg>
                                        Agendar cita
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
