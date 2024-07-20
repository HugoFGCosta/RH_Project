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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.css" rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('/css/alerts.css') }}">

    @yield('styles')

    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>

    <!-- Pusher Configuration -->
    <script src="https://js.pusher.com/8.2/pusher.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>
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
    </script>

    <!-- Inclua o arquivo de notificações -->
    <script src="{{ asset('js/notifications.js') }}"></script>
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
                                $role = 'Funcionário';
                            }
                        @endphp

                        <li class="nav-item">
                            <a href="/menu">
                                <span id="notification-bell" style="display: none;">
                                    <ion-icon name="notifications-outline" size="large"></ion-icon>
                                </span>
                                <a href="/user/show">
                                    {{ $firstName }}{{ $lastName ? ' ' . $lastName : '' }}
                                    <span class="user-role">({{ $role }})</span>
                                </a>
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

    <!-- Ícones -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <!-- Scripts -->
    <script>
        var presenceStatusUrl = '{{ url('user/presence/status') }}';
    </script>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.js"></script>
    <!-- Scripts Notifications-->

    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script>
        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = true;

        var pusher = new Pusher('f680f8f49f5085f74a06', {
            cluster: 'ap1'
        });

        var channel = pusher.subscribe('notification-channel');

        channel.bind('notification-event', function(data) {
            if (data.event_type === 'vacation_approval') {
                var vacationId = data.vacation_id;
                var newApprovalStatus = data.approval_status;

                // Exemplo: Atualizar dinamicamente o estado de aprovação na interface
                var approvalSelect = document.getElementById('vacation_approval_states_id');
                if (approvalSelect) {
                    approvalSelect.value = newApprovalStatus;
                }

                // Exemplo: Mostrar uma mensagem de notificação na interface
                var notificationElement = document.createElement('div');
                notificationElement.classList.add('alert', 'alert-info');
                notificationElement.innerHTML = 'Aprovação de férias atualizada para ' + newApprovalStatus;
                document.body.appendChild(notificationElement);
            }
        });

        // Callback para pusher:subscription_succeeded
        channel.bind('pusher:subscription_succeeded', function() {
            console.log('Subscribed to notification-channel');
            // Aqui você pode adicionar qualquer lógica adicional necessária após a subscrição
        });
    </script>



    <script src="{{ asset('/js/alerts.js') }}"></script>



    @yield('scripts')
    @stack('scripts')
</body>

</html>
