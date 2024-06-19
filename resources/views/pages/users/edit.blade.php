@extends('master.main')

@section('styles')
    <link rel="stylesheet" href="{{ asset('/css/users-edit.css') }}">
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h1>Perfil de {{ $user->name }}</h1>
                @component('components.users.user-form-edit', [
                    'user' => $user,
                    'work_shifts' => $work_shifts,
                    'roles' => $roles,
                    'user_shift' => $user_shift,
                ])
                @endcomponent
            </div>
        </div>
    </div>
@endsection
