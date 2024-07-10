@extends('master.main')

@section('styles')
    <link rel="stylesheet" href="{{ asset('/css/attendance-record.css') }}">
@endsection

@section('content')
    <div class="container p-5">
        <h1>Registo de Assiduidade</h1>

        <div class="form-container">
            @component('components.attendance-record.attendance-record2', ['user' => $user,'presences' => $presences, 'user_shifts' => $user_shifts]);
            @endcomponent
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('/js/attendance-record.js') }}"></script>
@endsection
