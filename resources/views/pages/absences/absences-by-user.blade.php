@extends('master.main')

@section('content')

    @if (session('success'))
        <div class="alert alert-success successMessage">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger errorMessage">
            {{ session('error') }}
        </div>
    @endif

    <div class="container pt-5">

        @component('components.absences.absences-by-user', ['absences' => $absences, 'absences_states' => $absences_states, 'absences_types' => $absences_types])
        @endcomponent

    </div>

@endsection
