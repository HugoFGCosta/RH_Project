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

    <!-- Script para ajustar o estado da barra lateral -->
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
                url: '{{ route('notifications.unreadCount') }}',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.unread_count > 0) {
                        $('#notification-bell').show();
                    } else {
                        $('#notification-bell').hide();
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });
        }

        // Inicialmente busca notificações
        fetchNotifications();
        setInterval(fetchNotifications, 60000); // Verifica a cada minuto
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
                            <a href="/menu">
                                <span>
                                    <ion-icon name="notifications-outline" size="large"></ion-icon>
                                </span>
                                <a href="/user/show">{{ $firstName }}{{ $lastName ? ' ' . $lastName : '' }}
                                    ({{ $role }})</a>
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

    @yield('scripts')
    @stack('scripts')
</body>

</html>
