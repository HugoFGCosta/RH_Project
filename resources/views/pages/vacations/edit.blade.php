@extends('master.main')
@section('content')
    @component('components.vacations.vacation-form-edit', ['vacations' => $vacations, 'totaldias' => $totaldias])
    @endcomponent
@endsection

