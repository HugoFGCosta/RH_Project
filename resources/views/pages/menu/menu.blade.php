@extends('master.main')
@component('components.styles.home')
@endcomponent

@section('content')
    <div class="container-menu">
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        <div class="row">
            <div class="left-column">
                <div class="button-In-Out">
                    @component('components.users.user-form-presence', [
                            'user' => $user,
                            'presence' => $presence,
                        ])
                    @endcomponent
                </div>
                <div class="notifications">
                    <p>Aqui ficam as notificações.
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusantium dolor ea earum eum ex, fuga iste, labore mollitia placeat porro quaerat qui quia ratione repellendus reprehenderit sunt tempore unde.
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Amet, commodi deleniti dolorem eum expedita, fugit id laborum natus nulla, officia omnis placeat possimus quas unde velit. Cupiditate neque possimus soluta?</p>

                </div>
            </div>
            <div class="right-column">
                <div class="calendar">
                    @component('components.calendar.calendar', [
                            'events' => $events,
                        ])
                    @endcomponent
                </div>
            </div>
        </div>
    </div>
@endsection
