@extends('master.main')

@section('content')
    <div class="container">
                <h1>Perfil de {{Auth::user()->name}}</h1>
        <div class="form-container">
        @component('components.users.user-form-show', ['user' => $user, 'user_shift' => $user_shift])
        @endcomponent
        </div>
    </div>
@endsection
