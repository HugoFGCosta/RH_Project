@extends('master.main')

@section('content')
    @component('components.vacations.vacation-form-create',['totaldias' => $totaldias,'role' => $role]);
    @endcomponent
@endsection
