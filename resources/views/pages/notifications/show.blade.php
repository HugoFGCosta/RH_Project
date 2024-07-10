@extends('master.main')
@section('content')
    @component('components.notifications.notification-list', ['notifications' => $notifications, 'user' => $user])
    @endcomponent
@endsection
