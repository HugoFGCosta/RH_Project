@extends('master.main')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-3"></div>
            <div class="col-6">
                <h1>Add User</h1>
                @component('components.users.user-form-create', [
                    'users' => $users,
                    'work_shifts' => $work_shifts,
                    'roles' => $roles,
                ])
                @endcomponent
            </div>
        </div>
    </div>
    </div>
@endsection
