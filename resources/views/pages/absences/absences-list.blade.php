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
        <h1>Lista de faltas (Gestão)</h1>

        @component('components.absences.absences-list', ['absences' => $absences, 'absences_states' => $absences_states, 'absences_types' => $absences_types,'justifications'=>$justifications])
        @endcomponent

    </div>

@endsection

@section('scripts')
    <script src="{{asset('js/absences-list.js')}}"></script>
@endsection
