@extends('master.main')

@section('content')
    <div class="container p-5">
        <h1>Editar Dados</h1>
        <div class="form-container">
            @component('components.users.user-form-edit-spec', [
                'user' => $user,
                'user_shift' => $user_shift,
                'work_shifts' => $work_shifts,
                'roles' => $roles,
            ])
            @endcomponent
        </div>
    </div>
@endsection
