let editButton = document.getElementById('editButton');
let errorDiv = document.getElementById('errorDiv');
let errorMessage = document.getElementById('errorMessage');

editButton.addEventListener('click', function() {
    let start_hourInput = document.getElementById('start_hour');
    let end_hourInput = document.getElementById('end_hour');
    let break_startInput = document.getElementById('break_start');
    let break_endInput = document.getElementById('break_end');

    let start_hour = start_hourInput.value;
    let end_hour = end_hourInput.value;
    let break_start = break_startInput.value;
    let break_end = break_endInput.value;

    //Check if the input fields are empty
    if(start_hourInput.value == '' || end_hourInput.value == '' || break_startInput.value == '' || break_endInput.value == ''){
        errorDiv.style.visibility = 'visible';
        errorMessage.innerHTML = 'Por favor preencha todos os campos';
        return;
    }

    const startTime = new Date(`1970-01-01T${start_hour}:00Z`);
    const endTime = new Date(`1970-01-01T${end_hour}:00Z`);
    const breakTime = new Date(`1970-01-01T${break_start}:00Z`);
    const breakEndTime = new Date(`1970-01-01T${break_end}:00Z`);

    //Validates the input times
    if (startTime >= endTime) {
        errorDiv.style.visibility = 'visible';
        errorMessage.innerHTML = 'A hora de entrada tem de ser menor que a hora de saída';
        return;
    } else if (breakTime >= breakEndTime) {
        errorDiv.style.visibility = 'visible';
        errorMessage.innerHTML = 'A hora de início do intervalo tem de ser menor que a hora de fim do intervalo';
        return;
    }

    //Calculate the difference in hours
    const timeDifference = Math.abs(endTime - startTime);
    const hoursDifference = timeDifference / (1000 * 60 * 60);

    console.log(hoursDifference);
    //Check if the difference is 8 hours
    if(hoursDifference != 8){
        errorDiv.style.visibility = 'visible';
        errorMessage.innerHTML = 'O turno tem de ter 8 horas';
        return;
    }

    const breakTimeDifference = Math.abs(breakEndTime - breakTime);
    const breakHoursDifference = breakTimeDifference / (1000 * 60 * 60);
    console.log(breakHoursDifference);

    //Check if the break is 1 to 2 hours
    if(breakHoursDifference < 1 || breakHoursDifference > 2){
        errorDiv.style.visibility = 'visible';
        errorMessage.innerHTML = 'O intervalo tem de ser entre 1 e 2 horas';
        return;
    }

    const durationFirstShift = Math.abs(breakTime - startTime);
    const durationSecondShift = Math.abs(endTime - breakEndTime);

    if(durationFirstShift > 4 || durationSecondShift > 4){
        errorDiv.style.visibility = 'visible';
        errorMessage.innerHTML = 'Os turnos têm de ter no máximo 4 horas';
        return;
    }

    //Submit the form
    document.getElementById('editForm').submit();

});
