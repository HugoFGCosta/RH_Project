@extends('master.main')

@section('content')
    <div class="container p-5">
        <h1>Todos Users</h1>
        <div class="form-container">
            @component('components.users.user-form-show-all', ['users' => $users])
            @endcomponent
        </div>
    </div>
@endsection
