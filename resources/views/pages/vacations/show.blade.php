@extends('master.main')
@section('content')
    @component('components.vacations.vacation-list' ,['vacations' => $vacations , 'totaldias' => $totaldias])

    @endcomponent
@endsection
