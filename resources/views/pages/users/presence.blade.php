@extends('master.main')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-3"></div>
            <div class="col-6">
                <h1>User Presence</h1>
                @component('components.users.user-form-presence', [
                    'user' => $user,
                    'presence' => $presence,
                ])
                @endcomponent
            </div>
        </div>
    </div>
    </div>
@endsection
