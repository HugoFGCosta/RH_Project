@extends('master.main')
@section('content')
    @component('components.vacations.vacation-list' ,['vacations' => $vacations])

    @component('components.vacations.vacation-list',['vacations' => $vacations])

    @endcomponent
@endsection
