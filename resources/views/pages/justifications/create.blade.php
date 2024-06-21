@extends('master.main')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-6">
                <h1>Justificar Falta</h1>
                @component('components.justifications.justification-form-create', [
                    'absences' => $absences,
                    'states' => $states,
                    'durations' => $durations,
                ])
                @endcomponent
            </div>
        </div>
    </div>
@endsection
