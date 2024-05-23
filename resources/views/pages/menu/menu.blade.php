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
    <div>
        @component('components.calendar.calendar', [
                'events' => $events,
            ])
        @endcomponent
    </div>
@endsection





