<link rel="stylesheet" href="{{ asset('css/show-all.css') }}">

<div class="container">

<section class="table__body">

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
            <tr>
                <td>{{ $absence->user->name }}</td>
                <td>{{ $absence->absence_start_date . " - " . $absence->absence_end_date }}</td>
                <td> {{ $duration . " hora(as)" }}  </td>
                <td> {{ $state->description }} </td>
            </tr>
        </tbody>
    </table>

</section>

    <form id="createForm" method="POST" action="{{ url('absences/' . $absence->id . '/justification') }}" enctype="multipart/form-data">
        @csrf
        @method('POST')

        <div class="form-group">
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

        <div class="form-group">
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
        <div class="form-group">
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
        <div class="form-group">
            <label for="name">Ficheiro</label>
            <input
                type="file"
                id="file"
                name="file"
                accept=".png,.jpg,.jpeg,.pdf,.docx"
                autocomplete="file"
                class="form-control
                @error('file') is-invalid @enderror"
                value="{{ old('file') }}"
                required>
                @error('file')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
             </span>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary createButton">Submit</button>

    </form>

</div>
