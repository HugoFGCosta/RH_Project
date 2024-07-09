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

        @component('components.work-shifts.work-shift-list', ['workShifts' => $workShifts])
        @endcomponent

    </div>

@endsection

@section('scripts')
    <script src="{{ asset('js/work-shifts/work-shift-index.js') }}"></script>
@endsection
