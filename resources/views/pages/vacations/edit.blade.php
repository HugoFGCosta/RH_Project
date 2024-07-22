@extends('master.main')



@section('styles')
    <link rel="stylesheet" href="{{ asset('/css/vacation-edit.css') }}">
@endsection

@section('content')
    <h1>Editar Férias</h1>
    @component('components.alerts.alerts')
    @endcomponent
    @component('components.vacations.vacation-form-edit', ['vacations' => $vacations, 'role' => $role, 'role_id_table' => $role_id_table])
    @endcomponent
@endsection
