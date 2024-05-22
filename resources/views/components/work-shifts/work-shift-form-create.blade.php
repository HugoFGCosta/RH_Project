<link rel="stylesheet" href="{{ asset('css/work-shifts.css') }}">

<div class="container">

    <h1 class="mt-5 addWorkShiftTitle">Adicionar Turno</h1>

    <form id="createForm" method="POST" action="{{ url('work-shifts') }}">
        @csrf
        @method('POST')

        <div class="form-group">
            <label class="start_hour_label" for="start_hour">Hora de Entrada</label>
            <input
                type="time"
                id="start_hour"
                name="start_hour"
                class="inputHour form-control @error('start_hour') is-invalid @enderror"
                value="{{ old('start_hour') }}"
                required>
            @error('start_hour')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="form-group">
            <label class="break_start_label" for="break_start">Hora de Início do Intervalo</label>
            <input
                type="time"
                id="break_start"
                name="break_start"
                class="inputHour form-control @error('break_start') is-invalid @enderror"
                value="{{ old('break_start') }}"
                required>
            @error('break_start')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="form-group">
            <label class="break_end_label" for="break_end">Hora de Fim do Intervalo</label>
            <input
                type="time"
                id="break_end"
                name="break_end"
                class="inputHour form-control @error('break_end') is-invalid @enderror"
                value="{{ old('break_end') }}"
                required>
            @error('break_end')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="form-group">
            <label class="end_hour_label_create" for="end_hour">Hora de Saída</label>
            <input
                type="time"
                id="end_hour"
                name="end_hour"
                class="inputHour form-control @error('end_hour') is-invalid @enderror"
                value="{{ old('end_hour') }}"
                required>
            @error('end_hour')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <button type="button" id="createButton" class="btn btn-primary createButton">Submit</button>

    </form>

    <div id="errorDiv" class="mensagemDiv">
        <p id="errorMessage">Mensagem</p>
    </div>
</div>

<script src="{{ asset('js/work-shifts/work-shift-create.js') }}"></script>

