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
                            'presence' => $presence ?? null,
                        ])
                    @endcomponent
                </div>

                <div class="calendar">
                    @component('components.calendar.calendar', [
                            'events' => $events,
                        ])
                    @endcomponent
                </div>
            </div>
            <div class="right-column">
                <div class="notifications">
                    <h2>Notificações</h2>
                </div>

                <table>
                    <tr>
                        <td style="width: 60px">
                            <img src="{{ asset('images/notifications.svg') }}" alt="notifications">
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
@endsection
