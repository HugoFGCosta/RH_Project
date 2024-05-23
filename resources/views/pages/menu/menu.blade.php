@extends('master.main')
@component('components.styles.home')
@endcomponent

@section('content')
    <div class="button-In-Out">
        @component('components.users.user-form-presence', [
                'user' => $user,
                'presence' => $presence,
            ])
        @endcomponent
    </div>
    <div class="calendar-container">
        <div id='calendar'>
            @component('components.calendar.calendar')
            @endcomponent
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/calendar.js') }}"></script>
@endpush


