<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.js"></script>
</head>

<body>

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
                        @component('components.users.user-form-presence', ['user' => $user, 'presence' => $presence])
                        @endcomponent
                    </div>

                    <div class="calendar">
                        @component('components.calendar.calendar', ['events' => $events])
                        @endcomponent
                    </div>
                </div>
                <div class="right-column">
                    <div class="notifications">
                        <h2>Notificações</h2>
                        <form action="{{ route('notifications.changeState') }}" method="POST">
                            @csrf
                            <div id="notification-list-container">
                                <!-- A lista de notificações será carregada aquiii -->
                            </div>
                            <button type="submit" class="btn btn-primary">Marcar como lido</button>
                            <button type="button" id="mark-all" class="btn btn-secondary">Marcar todos</button>
                        </form>
                    </div>
                </div>
            </div>

            <script src="https://js.pusher.com/8.2/pusher.min.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
                        cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
                        useTLS: true
                    });

                    const channel = pusher.subscribe('notification-channel');

                    channel.bind('App\\Events\\NotificationEvent', function(data) {
                        fetchNotifications();
                    });

                    function fetchNotifications() {
                        $.ajax({
                            url: '{{ route('notifications.index') }}',
                            type: 'GET',
                            dataType: 'json',
                            success: function(response) {
                                console.log('AJAX response:', response);
                                if (response && response.notifications) {
                                    const notifications = response.notifications;
                                    if (Array.isArray(notifications)) {
                                        updateNotificationList(notifications);
                                    } else {
                                        console.error('notifications não é um array:', notifications);
                                    }
                                } else {
                                    console.error('Resposta inesperada:', response);
                                }
                            },
                            error: function(xhr) {
                                console.error(xhr.responseText);
                            }
                        });
                    }

                    function updateNotificationList(notifications) {
                        const notificationListContainer = $('#notification-list-container');
                        notificationListContainer.empty();

                        let html = `
                        <div class="container mt-5">
                            <h1>Notificações de hoje</h1>
                            <table class="table-notifications">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">TIPO</th>
                                        <th scope="col">DESCRIÇÃO</th>
                                        <th scope="col">STATE</th>
                                        <th scope="col">AÇÃO</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;

                        notifications.forEach(notification => {
                            html += `
                            <tr>
                                <td>
                                    <input type="checkbox" name="notifications[${notification.id}][id]" value="${notification.id}">
                                    <input type="hidden" name="notifications[${notification.id}][events_id]" value="${notification.events_id}">
                                    <input type="hidden" name="notifications[${notification.id}][absence_id]" value="${notification.absence_id}">
                                    <input type="hidden" name="notifications[${notification.id}][vacation_id]" value="${notification.vacation_id}">
                                    <input type="hidden" name="notifications[${notification.id}][state]" value="${notification.state}">
                                </td>
                        `;

                            if (notification.events_id !== null) {
                                html += `<td>EVENTOS</td>`;
                                html += `<td>${notification.event.title}</td>`;
                            } else if (notification.absence_id !== null) {
                                html += `<td>FALTAS</td>`;
                                let description = 'Injustificado';
                                if (notification.absence.absence_states_id === 1) {
                                    description = 'Aprovado';
                                } else if (notification.absence.absence_states_id === 2) {
                                    description = 'Rejeitado';
                                } else if (notification.absence.absence_states_id === 3) {
                                    description = 'Pendente';
                                }
                                html += `<td>${description}</td>`;
                            } else if (notification.vacation_id !== null) {
                                html += `<td>FERIAS</td>`;
                                let description = 'Pendente';
                                if (notification.vacation.vacation_approval_states_id === 1) {
                                    description = 'Aprovado';
                                } else if (notification.vacation.vacation_approval_states_id === 2) {
                                    description = 'Negado';
                                }
                                html += `<td>${description}</td>`;
                            }

                            html += `
                            <td>${notification.state === 0 ? 'Nao Lido' : 'Lido'}</td>

                            <td>
                                ${notification.events_id !== null ? '1---' : ''}
                                ${notification.absence_id !== null ? '<a href="/users/' + notification.user_id + '/absences">Justificar</a>' : ''}
                                ${notification.vacation_id !== null ? '3---' : ''}
                            </td>
                            </tr>
                        `;
                        });

                        html += `
                    </tbody>
                </table>
            </div>
            `;

                        notificationListContainer.append(html);
                    }

                    fetchNotifications(); // Initial fetch

                    // Add event listener for "Marcar todos" button
                    document.getElementById('mark-all').addEventListener('click', function() {
                        document.querySelectorAll('#notification-list-container input[type="checkbox"]').forEach(
                            checkbox => {
                                checkbox.checked = true;
                            });
                    });
                });
            </script>
        </div>
    @endsection
</body>

</html>
