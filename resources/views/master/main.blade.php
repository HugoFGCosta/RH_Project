@php use Illuminate\Support\Facades\Auth; @endphp

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/mainPage.css') }}" rel="stylesheet">
    <link href="{{ asset('css/daily-tasks.css') }}" rel="stylesheet">
    <link href="{{ asset('css/showform.css') }}" rel="stylesheet">
    <link href="{{ asset('css/forms.css') }}" rel="stylesheet">
    @yield('styles')

    <!-- Script de js para correr primeiro para resolver problema de expansão no recarregamento da página -->
    <script>
        (function() {
            const sidebarState = localStorage.getItem('sidebarState');
            if (sidebarState === 'collapsed') {
                document.documentElement.classList.add('sidebar-collapsed');
            }
        })();
    </script>

    <!-- Pusher Configuration -->
    <script src="https://js.pusher.com/8.2/pusher.min.js"></script>
    <script>
        window.pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
            cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
            useTLS: true
        });

        window.pusherChannel = window.pusher.subscribe('notification-channel');

        window.pusherChannel.bind('App\\Events\\NotificationEvent', function(data) {
            console.log('Received notification: ' + data.message);
            fetchNotifications();
        });

        function fetchNotifications() {
            $.ajax({
                url: '{{ route('notifications.index') }}',
                type: 'GET',
                dataType: 'json',
                success: function(notifications) {
                    renderSections(notifications);
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });
        }

        function renderSections(notifications) {
            const notificationList = $('#notification-list');
            notificationList.empty();

            notifications.forEach(notification => {
                const notificationElement = $('<div></div>').addClass('notification-item');
                if (notification.event) {
                    notificationElement.append(`<p>Evento: ${notification.event.title}</p>`);
                } else if (notification.absence) {
                    let description = 'Injustificado';
                    if (notification.absence.absence_states_id == 1) {
                        description = 'Aprovado';
                    } else if (notification.absence.absence_states_id == 2) {
                        description = 'Rejeitado';
                    } else if (notification.absence.absence_states_id == 3) {
                        description = 'Pendente';
                    }
                    notificationElement.append(`<p>Falta: ${description}</p>`);
                } else if (notification.vacation) {
                    let description = 'Pendente';
                    if (notification.vacation.vacation_approval_states_id == 1) {
                        description = 'Aprovado';
                    } else if (notification.vacation.vacation_approval_states_id == 2) {
                        description = 'Negado';
                    }
                    notificationElement.append(`<p>Férias: ${description}</p>`);
                }
                notificationList.append(notificationElement);
            });
        }

        // Initial fetch of notifications
        fetchNotifications();
    </script>
</head>

<body>
    @component('master.header')
    @endcomponent

    <main>
        <div class="main">
            <div class="topbar">
                <div class="toggle">
                    <img src="{{ asset('images/menu-arrow-open.svg') }}" alt=""
                        style="width: 70%; height: auto;" id="menu-arrow-open">
                </div>
                <div class="user">
                    @if (Auth::check())
                        @php
                            $fullName = Auth::user()->name;
                            $nameParts = explode(' ', $fullName);
                            $firstName = $nameParts[0];
                            $lastName = count($nameParts) > 1 ? end($nameParts) : '';
                            $role = Auth::user()->role->role;

                            $roleId = Auth::user()->role_id;
                            if ($roleId == 3) {
                                $role = 'Administrador';
                            } elseif ($roleId == 2) {
                                $role = 'Gestor';
                            } else {
                                $role = 'Utilizador';
                            }
                        @endphp
                        <li class="nav-item">
                            <a href="/user/show">{{ $firstName }}{{ $lastName ? ' ' . $lastName : '' }}
                                ({{ $role }})</a>
                        </li>
                    @endif
                </div>
            </div>
        </div>
        <div class="content-area hidden">
            @yield('content')
        </div>
    </main>

    @component('master.footer')
    @endcomponent

    <script src="{{ asset('js/menu.js') }}"></script>

    <!-- Icons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <!-- Scripts -->
    <script>
        var presenceStatusUrl = '{{ url('user/presence/status') }}';
    </script>

    @yield('scripts')
    @stack('scripts')
</body>

</html>
