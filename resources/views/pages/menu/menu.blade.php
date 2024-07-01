@extends('master.main')
@component('components.styles.home')
@endcomponent

@section('content')
    <div class="container-menu">
        @if (session('error'))
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

                    <table>
                        <tr>
                            <td style="width: 60px">
                                <img src="{{ asset('images/notifications.svg') }}" alt="notifications">
                            </td>
                        </tr>

                        @component('components.notifications.notification-list', ['notifications' => $notifications])
                        @endcomponent



                    </table>
                </div>
            </div>
        </div>

        <script src="https://js.pusher.com/8.2/pusher.min.js"></script>
        <script>
            // Inicializa o cliente Pusher
            const pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
                cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
                useTLS: true // Deixe como true se estiver usando HTTPS
            });

            // Subscreve ao canal de notificação
            const channel = pusher.subscribe('notification-channel');

            // Manipula eventos recebidos no canal
            channel.bind('App\\Events\\NotificationEvent', function(data) {
                // Adiciona a nova notificação à lista
                const notificationList = document.getElementById('notification-list');
                const newItem = document.createElement('li');
                newItem.textContent = data.message;
                newItem.dataset.id = data.notificationId; // Inclui o ID da notificação
                notificationList.appendChild(newItem);
            });
        </script>
    @endsection
