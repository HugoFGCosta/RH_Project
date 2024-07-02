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
                document.addEventListener('DOMContentLoaded', function() {
                    if (typeof window.pusher === 'undefined') {
                        // Initialize the Pusher client
                        window.pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
                            cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
                            useTLS: true
                        });
                    }

                    // Subscribe to the notification channel
                    const channel = window.pusher.subscribe('notification-channel');

                    // Handle events received on the channel
                    channel.bind('App\\Events\\NotificationEvent', function(data) {
                        // Add the new notification to the list
                        const notificationList = document.getElementById('notification-list');
                        const newItem = document.createElement('li');
                        newItem.textContent = data.message;
                        newItem.dataset.id = data.notificationId; // Include the notification ID
                        notificationList.appendChild(newItem);
                        fetchNotifications();
                    });

                    function fetchNotifications() {
                        $.ajax({
                            url: '{{ route('notifications.index') }}',
                            type: 'GET',
                            dataType: 'json',
                            success: function(notifications) {
                                updateNotificationTable(notifications);
                            },
                            error: function(xhr) {
                                console.error(xhr.responseText);
                            }
                        });
                    }

                    function updateNotificationTable(notifications) {
                        const tbody = $('#notification-table tbody');
                        tbody.empty();

                        notifications.forEach(notification => {
                            const row = $('<tr></tr>');
                            row.append(
                                `<td><input type="checkbox" name="notifications[${notification.id}][id]" value="${notification.id}"></td>`
                            );
                            if (notification.events_id) {
                                row.append('<td>EVENTOS</td>');
                                row.append(`<td>${notification.event.title}</td>`);
                            } else if (notification.absence_id) {
                                row.append('<td>FALTAS</td>');
                                let description = 'Injustificado';
                                if (notification.absence.absence_states_id == 1) {
                                    description = 'Aprovado';
                                } else if (notification.absence.absence_states_id == 2) {
                                    description = 'Rejeitado';
                                } else if (notification.absence.absence_states_id == 3) {
                                    description = 'Pendente';
                                }
                                row.append(`<td>${description}</td>`);
                            } else if (notification.vacation_id) {
                                row.append('<td>FERIAS</td>');
                                let description = 'Pendente';
                                if (notification.vacation.vacation_approval_states_id == 1) {
                                    description = 'Aprovado';
                                } else if (notification.vacation.vacation_approval_states_id == 2) {
                                    description = 'Negado';
                                }
                                row.append(`<td>${description}</td>`);
                            }
                            row.append('<td>Nao Lido</td>');
                            row.append('<td> --- </td>');
                            tbody.append(row);
                        });
                    }
                });
            </script>
@endsection
