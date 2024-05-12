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

</head>
<body>

@component('master.header')
@endcomponent

<main>
    <div class="main">
        <div class="topbar">
            <div class="toggle">
                <ion-icon name="menu-outline"></ion-icon>
            </div>
            <div class="search">
                <label>
                    <input type="text" placeholder="Procure">
                    <ion-icon name="search-outline"></ion-icon>
                </label>
            </div>
            <div class="user">
                @if (Auth::check())
                <li class="nav-item">
                    {{ Auth::user()->name }}
                </li>
                @endif
            </div>
        </div>
    </div>
</main>

@component('master.footer')
@endcomponent

<script src="{{ asset('js/menu.js') }}"></script>

<!-- Icons -->
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

@yield('scripts')

</body>
</html>
