@extends('master.main')
@section('content')
    @component('components.vacations.vacation-form-edit', ['vacations' => $vacations, 'role' => $role,'role_id_table'=> $role_id_table])
    @endcomponent
@endsection

