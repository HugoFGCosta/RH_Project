@extends('master.main')

@section('styles')
    <link rel="stylesheet" href="{{ asset('/css/show-all.css') }}">
@endsection

@section('content')
    <div class="container p-5">
        <div class="form-container">
            @component('components.users.user-form-show-all', ['users' => $users])
            @endcomponent
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('/js/show-all.js') }}"></script>
@endsection
