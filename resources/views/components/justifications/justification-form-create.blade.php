<link rel="stylesheet" href="{{ asset('css/show-all.css') }}">
<link rel="stylesheet" href="{{ asset('css/justification-create.css') }}">

<div class="container">

    <section class="table__body tableSection">

        <table>
            <thead>
            <tr>
                <th> Nome </th>
                <th> Data de Falta </th>
                <th> Duração </th>
                <th> Estado </th>
            </tr>
            </thead>
            <tbody>
            @foreach($absences as $index => $absence)
                <tr>
                    <td>{{ $absence->user->name }}</td>
                    <td>{{ $absence->absence_start_date . " - " . $absence->absence_end_date }}</td>
                    <td>{{ $durations[$index] . " hora(as)" }}</td>
                    <td>{{ $states[$index]->description }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>


    </section>

    <div class="formDiv">

    <h1>Formulário de justificação de falta</h1>

        <form id="createForm" method="POST" action="{{ url('absences/justification/store') }}" enctype="multipart/form-data">
            @csrf

            <!-- Inputs ocultos para IDs das faltas selecionadas -->
            @foreach($absences as $absence)
                <input type="hidden" name="selected_absences[]" value="{{ $absence->id }}">
            @endforeach

            <div class="form-group motiveSection section">
                <label class="motive" for="motive">Motivo</label>
                <input
                    type="text"
                    id="motive"
                    name="motive"
                    class="inputHour form-control @error('motive') is-invalid @enderror"
                    value="{{ old('motive') }}"
                    required>
                @error('motive')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="form-group section">
                <label class="justification_date" for="justification_date">Data</label>
                <input
                    type="text"
                    id="justification_date"
                    name="justification_date"
                    class="inputHour form-control @error('justification_date') is-invalid @enderror"
                    value="{{ Carbon\Carbon::now()->format('Y-m-d') }}"
                    required>
                @error('justification_date')
                <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
                @enderror
            </div>
            <div class="form-group section">
                <label class="observation" for="start_hour">Observações</label>
                <input
                    type="text"
                    id="observation"
                    name="observation"
                    class="inputHour form-control @error('observation') is-invalid @enderror"
                    value="{{ old('observation') }}"
                    required>
                @error('observation')
                <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
                @enderror
            </div>

            <div class="form-group section">
                <div class="file-upload-container">
                    <input
                        type="file"
                        id="file"
                        name="file"
                        accept=".png,.jpg,.jpeg,.pdf,.docx"
                        autocomplete="file"
                        class="form-control @error('file') is-invalid @enderror"
                        value="{{ old('file') }}"
                        required
                        onchange="updateFileName()">
                    <button type="button" class="file-description-button" onclick="document.getElementById('file').click()">Escolher Ficheiro</button>
                    <span id="file-name" class="file-name">Nenhum ficheiro selecionado</span>
                </div>
                @error('file')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary createButton justificationCreateButton section">Submeter</button>

        </form>
    </div>

</div>
