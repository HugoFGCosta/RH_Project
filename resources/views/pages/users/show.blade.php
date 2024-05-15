@extends('master.main')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-3"></div>
            <div class="col-6">
                <h1>SHOW USER</h1>
                @component('components.users.user-form-show', ['user' => $user, 'user_shift' => $user_shift])
                @endcomponent
            </div>
        </div>
    </div>
    </div>
@endsection
