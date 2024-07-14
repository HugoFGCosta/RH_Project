@extends('master.main')

@section('content')

    @component('components.alerts.alerts')
    @endcomponent

    <div class="container pt-5">
        <h1>Lista de horários</h1>
        @component('components.work-shifts.work-shift-list', ['workShifts' => $workShifts])
        @endcomponent

    </div>

@endsection

@section('scripts')
    <script src="{{ asset('js/work-shifts/work-shift-index.js') }}"></script>
@endsection
