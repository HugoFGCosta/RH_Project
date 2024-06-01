@extends('master.main')
@section('content')
    @component('components.vacations.vacation-list' ,['vacations' => $vacations ,'role' => $role, 'totaldias' => $totaldias  ])

    @endcomponent
@endsection
