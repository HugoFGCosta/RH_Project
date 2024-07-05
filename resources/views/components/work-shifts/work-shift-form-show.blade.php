<link rel="stylesheet" href="{{ asset('css/work-shifts.css') }}">

<div class="container">

    <h1 class="mt-5 mb-3">Detalhes de Horário</h1>

    <div class="showform-form-row">
        <div class="showform-input-data">
            <label for="email">Hora de Entrada:</label>
            <p id="email">{{ $work_shift->start_hour }}</p>
            <div class="showform-underline"></div>
        </div>
    </div>
    <div class="showform-form-row">
        <div class="showform-input-data">
            <label for="email">Hora de Almoço:</label>
            <p id="email">{{ $work_shift->break_start }}</p>
            <div class="showform-underline"></div>
        </div>
    </div>
    <div class="showform-form-row">
        <div class="showform-input-data">
            <label for="email">Fim de Hora de Almoço:</label>
            <p id="email">{{ $work_shift->break_end }}</p>
            <div class="showform-underline"></div>
        </div>
    </div>
    <div class="showform-form-row">
        <div class="showform-input-data">
            <label for="email">Hora de Saída:</label>
            <p id="email">{{ $work_shift->end_hour }}</p>
            <div class="showform-underline"></div>
        </div>
    </div>

    <div class="showform-form-row">
        <a class="buttonEditData" href="{{ url('work-shifts/edit/'.$work_shift->id) }}" type="button"><button class="editButton">Editar Dados</button></a>
    </div>




</div>
