@extends('recepcion.layout')

@section('title', 'Beauty & Barber Studio - Calendario Interactivo de Citas')

@section('content')
    @include('partials.calendario-citas', [
        'apiEventosUrl' => route('recepcion.calendario.eventos'),
        'apiDetalleUrlBase' => url('/recepcion/citas'),
        'apiEstadoUrlBase' => url('/recepcion/citas'),
        'nuevaCitaUrl' => route('recepcion.agendamiento'),
        'trabajadores' => $trabajadores,
        'servicios' => $servicios,
        'rolUsuario' => 'recepcion',
    ])

    @include('partials.modal-editar-cita')
@endsection
