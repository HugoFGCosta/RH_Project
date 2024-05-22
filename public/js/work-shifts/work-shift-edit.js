let editButton = document.getElementById('editButton');
let errorDiv = document.getElementById('errorDiv');
let errorMessage = document.getElementById('errorMessage');

editButton.addEventListener('click', function() {
    let start_hourInput = document.getElementById('start_hour_edit');
    let end_hourInput = document.getElementById('end_hour_edit');
    let break_startInput = document.getElementById('break_start_edit');
    let break_endInput = document.getElementById('break_end_edit');

    //Valida se os campos estão preenchidos
    if(start_hourInput.value == '' || end_hourInput.value == '' || break_startInput.value == '' || break_endInput.value == ''){
        errorDiv.style.visibility = 'visible';
        errorMessage.innerHTML = 'Por favor preencha todos os campos';
        return;
    }

    let start_hour = start_hourInput.value;
    let end_hour = end_hourInput.value;
    let break_start = break_startInput.value;
    let break_end = break_endInput.value;

    // Converter horários para minutos
    let start_minutes = timeToMinutes(start_hour);
    let end_minutes = timeToMinutes(end_hour);
    let break_start_minutes = timeToMinutes(break_start);
    let break_end_minutes = timeToMinutes(break_end);

    //Validates the input times
    if (start_minutes >= break_start_minutes) {
        errorDiv.style.visibility = 'visible';
        errorMessage.innerHTML = 'A hora de entrada tem de ser menor que a hora de inicio de intervalo';
        return;
    }
    else if(start_minutes >= end_minutes){
        errorDiv.style.visibility = 'visible';
        errorMessage.innerHTML = 'A hora de entrada tem de ser menor que a hora de saída';
        return;
    }
    else if(start_minutes >= break_end_minutes){
        errorDiv.style.visibility = 'visible';
        errorMessage.innerHTML = 'A hora de entrada tem de ser menor que a hora de fim de intervalo';
        return;
    }
    else if(break_start_minutes>=break_end_minutes){
        errorDiv.style.visibility = 'visible';
        errorMessage.innerHTML = 'A hora de inicio de intervalo tem de ser menor que a horea de fim de intervalo';
        return;
    }
    else if(break_end_minutes>=end_minutes){
        errorDiv.style.visibility = 'visible';
        errorMessage.innerHTML = 'A hora de fim de intervalo tem de ser menor que a hora de saída';
        return;
    }


    //Valida total de horas do turno
    let work_minutes = (end_minutes - start_minutes);
    let work_hours = Math.floor(work_minutes / 60);

    if(work_hours!=8){
        errorDiv.style.visibility = 'visible';
        errorMessage.innerHTML = 'O turno tem de ter 8 horas';
        return;
    }

    //Valida hora almoço
    let break_minutes=break_end_minutes-break_start_minutes;
    let break_hours = Math.floor(break_minutes / 60);
    console.log(break_hours);

    if(break_hours<1||break_hours>2){
        errorDiv.style.visibility = 'visible';
        errorMessage.innerHTML = 'O intervalo de almoço tem de ter no minimo 1 e no máximo 2 horas';
        return;
    }

    //Valida total de horas do turno
    let firstShiftMinutes = (break_start_minutes-start_minutes);
    let firstShiftHours = Math.floor(firstShiftMinutes / 60);

    //Valida total de horas do turno
    let secondShiftMinutes = (end_minutes - break_end_minutes);
    let secondShiftHours = Math.floor(secondShiftMinutes / 60);

    console.log(firstShiftHours);
    //Valida duração turnos
    if(firstShiftHours>4){
        errorDiv.style.visibility = 'visible';
        errorMessage.innerHTML = 'O primeiro turno tem de ter 4 horas ou menos';
        return;
    }
    if(secondShiftHours>4){
        errorDiv.style.visibility = 'visible';
        errorMessage.innerHTML = 'O segundo turno tem de ter 4 horas ou menos';
        return;
    }

    //Submit the form
    document.getElementById('editForm').submit();


});

// Função para converter horas em minutos desde meia-noite
function timeToMinutes(time) {
    let [hours, minutes] = time.split(':').map(Number);
    return hours * 60 + minutes;
}
