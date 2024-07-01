@extends('master.main')

@section('styles')
    <link rel="stylesheet" href="{{ asset('/css/show-all.css') }}">
@endsection

@section('content')
    <h1>Lista de Férias</h1>
    @component('components.vacations.vacation-list', ['vacations' => $vacations, 'role' => $role, 'totaldias' => $totaldias])
    @endcomponent
@endsection

@section('scripts')
    <script src="{{ asset('/js/show-all.js') }}"></script>
@endsection
