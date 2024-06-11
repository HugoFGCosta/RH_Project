@extends('master.main')

@section('content')
    <div class="container p-5">
        <h1>EDIT SPEC</h1>
        <div class="form-container">
            @component('components.user-shifts.user-form-edit-spec', [
                'user_shifts' => $user_shifts,
                'work_shifts' => $work_shifts,
            ])
            @endcomponent
        </div>
    </div>
@endsection
