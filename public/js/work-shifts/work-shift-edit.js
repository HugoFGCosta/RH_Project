let editButton = document.getElementById('editButton');
let errorDiv = document.getElementById('errorDiv');
let errorMessage = document.getElementById('errorMessage');

editButton.addEventListener('click', function() {

    let start_hourInput = document.getElementById('start_hour_edit');
    let end_hourInput = document.getElementById('end_hour_edit');
    let break_startInput = document.getElementById('break_start_edit');
    let break_endInput = document.getElementById('break_end_edit');

    let start_hour = start_hourInput.value;
    let break_start = break_startInput.value;
    let break_end = break_endInput.value;
    let end_hour = end_hourInput.value;

    //Calcula a duração do Primeiro Turno e do Segundo Turno em minutos
    let durationFirstTurn = calculaHoraPrimeiroTurno(start_hour, break_start);
    let durationSecondTurn = calculaHoraSegundoTurno(break_end, end_hour);
    let durationBreak = calculaDuracaoIntervalo(break_start, break_end);

    console.log("Duração Primeiro Turno: " + durationFirstTurn);
    console.log("Duração Segundo Turno: " + durationSecondTurn);
    console.log("Duração Intervalo: " + durationBreak);

    //Converte tudo para horas
    let durationFirstTurnHours = durationFirstTurn / 60;
    let durationSecondTurnHours = durationSecondTurn / 60;
    let durationBreakHours = durationBreak / 60;

    console.log("Duração Primeiro Turno: " + durationFirstTurnHours);
    console.log("Duração Segundo Turno: " + durationSecondTurnHours);
    console.log("Duração Intervalo: " + durationBreakHours);

    if(durationFirstTurnHours + durationSecondTurnHours + durationBreakHours > 8){
        errorDiv.style.visibility = 'visible';
        errorMessage.innerHTML = 'O turno tem de ter no máximo 8 horas de duração';
        return;
    }

    if(durationBreakHours <1 || durationBreakHours > 2){
        errorDiv.style.visibility = 'visible';
        errorMessage.innerHTML = 'O intervalo tem de ter entre 1 e 2 horas';
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

function calculaHoraPrimeiroTurno(start_hourInput, break_startInput) {  //Calcula a duração do primeiro turno em minutos

    let duration = 0;

    if (break_startInput > start_hourInput) {

        //Se a hora de entrada for menor que a hora de saída, então o funcionário trabalhou no mesmo dia
        let start_hour = timeToMinutes(start_hourInput);
        let break_start = timeToMinutes(break_startInput);

        duration = break_start - start_hour;

    }
    else{

        //Se a hora de entrada for maior que a hora de saída, então o funcionário trabalhou até o dia seguinte
        let start_hour = timeToMinutes(start_hourInput);
        let break_start = timeToMinutes(break_startInput);

        duration = (1440 - start_hour) + break_start;

    }

    return duration;
}

function calculaHoraSegundoTurno(break_endInput, end_hourInput) {   //Calcula a duração do segundo turno em minutos

    let duration = 0;

    if (end_hourInput > break_endInput) {

        //Se a hora de entrada for menor que a hora de saída, então o funcionário trabalhou no mesmo dia
        let break_end = timeToMinutes(break_endInput);
        let end_hour = timeToMinutes(end_hourInput);

        duration = end_hour - break_end;
    }

    else{

        //Se a hora de entrada for maior que a hora de saída, então o funcionário trabalhou até o dia seguinte
        let break_end = timeToMinutes(break_endInput);
        let end_hour = timeToMinutes(end_hourInput);

        duration = (1440 - break_end) + end_hour;

    }

    return duration;
}

function calculaDuracaoIntervalo(break_startInput, break_endInput) {    //Calcula a duração do intervalo em minutos

    let duration = 0;

    if (break_endInput > break_startInput) {

        //Se a hora de entrada for menor que a hora de saída, então o funcionário trabalhou no mesmo dia
        let break_end = timeToMinutes(break_startInput);
        let end_hour = timeToMinutes(break_endInput);

        duration = end_hour - break_end;
    }

    else{
        //Se a hora de entrada for maior que a hora de saída, então o funcionário trabalhou até o dia seguinte
        let break_end = timeToMinutes(break_startInput);
        let end_hour = timeToMinutes(break_endInput);

        duration = (1440 - break_end) + end_hour;

    }

    return duration;

}
