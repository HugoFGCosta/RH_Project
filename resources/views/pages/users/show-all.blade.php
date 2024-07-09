@extends('master.main')

@section('styles')
    <link rel="stylesheet" href="{{ asset('/css/show-all.css') }}">
@endsection

@section('content')
    <div class="container p-5">

        @if (session('success'))
            <div class="alert alert-success successMessage">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger errorMessage">
                {{ session('error') }}
            </div>
        @endif

        <div class="form-container">
            @component('components.users.user-form-show-all', ['users' => $users])
            @endcomponent
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('/js/show-all.js') }}"></script>
@endsection
