<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Project</title>

    <link href="{{ asset('css/app.css') }}" media="all" rel="stylesheet" type="text/css">
    @yield('styles')
</head>

<body>


    @component('master.header')
    @endcomponent


    <main>
        @yield('content')
    </main>


    @component('master.footer')
    @endcomponent


    <script src="{{ asset('js/app.js') }}" type="text/javascript"></script>
    @yield('scripts')


</body>

</html>
