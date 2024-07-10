@extends('master.main')

@section('content')
    <h1>Lista de Faltas</h1>
    @component('components.alerts.alerts')
    @endcomponent

    <div class="container pt-5">

        @component('components.absences.absences-by-user', ['absences' => $absences, 'absences_states' => $absences_states, 'absences_types' => $absences_types])
        @endcomponent

    </div>

@endsection

@section('scripts')
    <script src="{{asset('js/absences-by-user.js')}}"></script>
@endsection
