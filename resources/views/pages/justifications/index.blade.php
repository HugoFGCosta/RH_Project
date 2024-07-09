@extends('master.main')

@section('content')

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

    <div class="container pt-5">

        @component('components.justifications.justification-list', ['justifications' => $justifications])
        @endcomponent

    </div>

@endsection

@section('scripts')
    <script src="{{ asset('js/justification-list.js') }}"></script>
@endsection
