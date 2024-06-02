@extends('master.main')
@section('content')
    @component('components.vacations.vacation-form-edit', ['vacations' => $vacations, 'role' => $role])
    @endcomponent
@endsection

