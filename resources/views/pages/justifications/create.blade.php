@extends('master.main')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-6">
                <h1>Justificar Falta</h1>

                @component('components.alerts.alerts')
                @endcomponent

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

@section('scripts')
    <script>
        function updateFileName() {
            let input = document.getElementById('file');
            document.getElementById('file-name').innerText = input.files.length > 0 ? input.files[0].name : 'Nenhum ficheiro selecionado';
        }
    </script>
@endsection
