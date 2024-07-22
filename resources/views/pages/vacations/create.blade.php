@extends('master.main')



@section('styles')
    <link rel="stylesheet" href="{{ asset('/css/vacation-form-create.css') }}">
@endsection

@section('content')
    @component('components.alerts.alerts')
    @endcomponent
    @component('components.vacations.vacation-form-create', ['totaldias' => $totaldias, 'role' => $role])
    @endcomponent
@endsection


