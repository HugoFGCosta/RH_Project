@extends('master.main')

@section('styles')
    <link rel="stylesheet" href="{{ asset('/css/show-all.css') }}">
@endsection

@section('content')
    @component('components.alerts.alerts')
    @endcomponent
    @component('components.vacations.vacation-list', ['vacations' => $vacations, 'role' => $role, 'totaldias' => $totaldias])
    @endcomponent
@endsection

@section('scripts')
    <script src="{{ asset('/js/vacation-list.js') }}"></script>
@endsection
