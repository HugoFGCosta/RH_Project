let balancePresences = document.getElementById('balancePresence');
let balanceAbsence = document.getElementById('balanceAbsence');
let balanceTotal = document.getElementById('balanceTotal');

let yearInput = document.getElementById('year');

if(balancePresences.innerHTML.includes('-')){
    balancePresences.style.color = 'red';

    let counter = 0;
    let indexToRemove = -1;

    for (let i = 0; i < balancePresences.innerHTML.length; i++) {
        if (balancePresences.innerHTML[i] === '-') {
            counter++;
        }
        if (counter === 2) {
            indexToRemove = i;
            break;
        }
    }

    if (indexToRemove !== -1) {
        balancePresences.innerHTML = balancePresences.innerHTML.slice(0, indexToRemove) + balancePresences.innerHTML.slice(indexToRemove + 1);
    }
}
else{
    balancePresences.style.color = 'green';
}

if(balanceAbsence.innerHTML.includes('-')){
    balanceAbsence.style.color = 'red';

    let counter = 0;
    let indexToRemove = -1;

    for (let i = 0; i < balanceAbsence.innerHTML.length; i++) {
        if (balanceAbsence.innerHTML[i] === '-') {
            counter++;
        }
        if (counter === 2) {
            indexToRemove = i;
            break;
        }
    }

    if (indexToRemove !== -1) {
        balanceAbsence.innerHTML = balanceAbsence.innerHTML.slice(0, indexToRemove) + balanceAbsence.innerHTML.slice(indexToRemove + 1);
    }

}
else{
    balanceAbsence.style.color = 'green';
    balanceAbsence.classList.add('greenMargin');

}

if(balanceTotal.innerHTML.includes('-')){
    balanceTotal.style.color = 'red';

    let counter = 0;
    let indexToRemove = -1;

    for (let i = 0; i < balanceTotal.innerHTML.length; i++) {
        if (balanceTotal.innerHTML[i] === '-') {
            counter++;
        }
        if (counter === 2) {
            indexToRemove = i;
            break;
        }
    }

    if (indexToRemove !== -1) {
        balanceTotal.innerHTML = balanceTotal.innerHTML.slice(0, indexToRemove) + balanceTotal.innerHTML.slice(indexToRemove + 1);
    }
}
else{
    balanceTotal.style.color = 'green';
    balanceTotal.classList.add('greenMargin');
}

if(yearInput.value === ""){
    yearInput.value = new Date().getFullYear();
}
