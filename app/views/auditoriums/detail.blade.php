@extends('layouts.default')

@section('breadcrumbs')
<li>
    {{ HTML::link('/exhibition', 'Programación') }}
</li>
<li class="active">
	Sala: {{ $auditorium->name }}
</li>
@stop


@section('content')

	<div>
		<img src="{{$auditorium->image->url('medium'); }}" alt="sala {{$auditorium->name}}">
	</div>
    <div class="dirección">
        <span class="bold"> Dirección:</span> <br> 
		{{ $auditorium->address}}
    </div>
    <span class="bold">Sede: </span> {{ (isset($resource->venue))? $resource->venue->name : 'Ninguna' }}<br>
    <span class="bold">Teléfonos: </span> {{ $auditorium->telephone }}<br>
    <span class="bold">Horario: </span> {{ $auditorium->schedule }}<br>
    <span class="bold">Costo General: </span> {{ $auditorium->general_cost }}<br>
    <span class="bold">Costo Estudiantes/Trabajadores: </span> {{ $auditorium->special_cost }}<br>

	<div class="embed-responsive embed-responsive-16by9">
		{{ $auditorium->map }}
	</div>

@stop