@extends('master.main')

@section('styles')
    <link rel="stylesheet" href="{{ asset('/css/attendance-record.css') }}">
@endsection

@section('content')
    <div class="container p-5">
        <div class="form-container">
            @component('components.attendance-record.attendance-record', ['user' => $user,'presences' => $presences, 'user_shifts' => $user_shifts]);
            @endcomponent
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('/js/show-all.js') }}"></script>
@endsection
