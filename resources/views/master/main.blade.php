@php use Illuminate\Support\Facades\Auth; @endphp

    <!DOCTYPE html>
<html lang="{{app()->getLocale() }}">

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

    <script src="https://js.pusher.com/8.2/pusher.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>
    <script>
        const pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
            cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
            useTLS: true, // Ajuste conforme seu ambiente (true/false)
        });

        const channel = pusher.subscribe('notification-channel');
        channel.bind('App\\Events\\NotificationEvent', function(data) {
            alert('Received notification: ' + data.message);
            // Aqui você pode adicionar lógica para lidar com a notificação recebida
        });
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






    @yield('scripts')
    @stack('scripts')

</body>

</html>
