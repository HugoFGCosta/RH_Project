@extends('master.main')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-6">
                <h1>Registo de Usuário</h1>
                @component('components.users.user-form-create', [
                    'users' => $users,
                    'work_shifts' => $work_shifts,
                    'roles' => $roles,
                ])
                @endcomponent
            </div>
        </div>
    </div>
@endsection
