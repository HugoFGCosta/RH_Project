<link rel="stylesheet" href="{{ asset('css/work-shifts.css') }}">

<div class="container">

    <h1 class="mt-5 addWorkShiftTitle">Editar Horário</h1>

    <form id="editForm" method="POST" action="{{ url('work-shifts/'.$workShift->id) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label class="start_hour_label" for="start_hour">Horario de Inicio:</label>
            <input
                type="time"
                id="start_hour"
                name="start_hour"
                autocomplete="start_hour"
                placeholder="Type your name"
                class="inputHour form-control @error('name') is-invalid @enderror"
                value="{{ $workShift->start_hour}}"
                required
                aria-describedby="nameHelp">
            @error('name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="form-group">
            <label class="break_start_label" for="break_start ">Hora de Inicio de Intervalo:</label>
            <input
                type="time"
                id="break_start"
                name="break_start"
                autocomplete="break_start"
                placeholder="Type your name"
                class="inputHour form-control @error('name') is-invalid @enderror"
                value="{{ $workShift->break_start}}"
                required
                aria-describedby="nameHelp">
            @error('name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="form-group">
            <label class="break_end_label" for="break_end">Hora de Fim de Intervalo:</label>
            <input
                type="time"
                id="break_end"
                name="break_end"
                autocomplete="break_end"
                placeholder="Type your name"
                class="inputHour form-control @error('name') is-invalid @enderror"
                value="{{ $workShift->break_end}}"
                required
                aria-describedby="nameHelp">
            @error('name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="form-group">
            <label class="end_hour_label" for="end_hour">Hora de Fim:</label>
            <input
                type="time"
                id="end_hour"
                name="end_hour"
                autocomplete="end_hour"
                placeholder="Type your name"
                class="inputHour form-control @error('name') is-invalid @enderror"
                value="{{ $workShift->end_hour}}"
                required
                aria-describedby="nameHelp">
            @error('name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <button type="button" id="editButton" class="btn editButton">Editar</button>

    </form>

    <div id="errorDiv" class="mensagemDiv">
        <p id="errorMessage">Mensagem</p>
    </div>

</div>

<script src="{{ asset('js/work-shifts/work-shift-edit.js') }}"></script>

